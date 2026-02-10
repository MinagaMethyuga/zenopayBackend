<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserChallenge;
use Illuminate\Support\Collection;

class AdaptiveChallengeService
{
    private const TIER_1_XP_MAX = 200;
    private const TIER_2_XP_MAX = 600;
    private const RECOMMENDED_LIMIT = 8;

    /**
     * Get recommended challenges for the user based on tier and completion state.
     *
     * @return Collection<int, Challenge>
     */
    public function getRecommendedChallenges(User $user): Collection
    {
        $metrics = $this->computeUserMetrics($user);
        $tier = $this->determineTier($metrics);
        $excludeChallengeIds = $this->getCompletedChallengeIds($user);

        return $this->selectChallengesForTier($tier, $excludeChallengeIds);
    }

    /**
     * Get recommended challenges with tier and top_category metadata for the recommended endpoint.
     *
     * @return array{tier: int, top_category: string, recommended: Collection<int, Challenge>}
     */
    public function getRecommendedWithMetadata(User $user): array
    {
        $metrics = $this->computeUserMetrics($user);
        $tier = $this->determineTier($metrics);
        $topCategory = $this->computeUserTopCategory($user);
        $excludeChallengeIds = $this->getCompletedChallengeIds($user);

        $recommended = $this->selectChallengesForTierAndCategory(
            $tier,
            $excludeChallengeIds,
            $topCategory
        );

        return [
            'tier' => $tier,
            'top_category' => $topCategory,
            'recommended' => $recommended,
        ];
    }

    /**
     * Compute the user's highest-spending category from expenses in the last 14 days.
     * Groups by category, sums amounts for type=expense only.
     */
    public function computeUserTopCategory(User $user): string
    {
        $top = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->where('occurred_at', '>=', now()->subDays(14))
            ->selectRaw('category, SUM(ABS(amount)) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->first();

        return $top ? (string) $top->category : '';
    }

    /**
     * Compute user metrics for the last 7 days (tx) and 14 days (challenge rate).
     */
    public function computeUserMetrics(User $user): array
    {
        $weeklyTxCount = Transaction::where('user_id', $user->id)
            ->where('occurred_at', '>=', now()->subDays(7))
            ->count();

        $acceptedIn14Days = UserChallenge::where('user_id', $user->id)
            ->where('accepted_at', '>=', now()->subDays(14))
            ->count();

        $completedIn14Days = UserChallenge::where('user_id', $user->id)
            ->where('accepted_at', '>=', now()->subDays(14))
            ->where('status', 'completed')
            ->count();

        $challengeCompletionRate = $acceptedIn14Days > 0
            ? $completedIn14Days / $acceptedIn14Days
            : 0.0;

        return [
            'weekly_tx_count' => $weeklyTxCount,
            'challenge_completion_rate' => round($challengeCompletionRate, 4),
        ];
    }

    /**
     * Determine user tier: 1 (starter), 2 (regular), 3 (advanced).
     */
    public function determineTier(array $metrics): int
    {
        $tx = $metrics['weekly_tx_count'];
        $rate = $metrics['challenge_completion_rate'];

        if ($tx > 15 && $rate > 0.7) {
            return 3;
        }
        if ($tx < 5 || $rate < 0.3) {
            return 1;
        }
        return 2;
    }

    /**
     * Challenge IDs the user has already completed (any time).
     *
     * @return array<int>
     */
    private function getCompletedChallengeIds(User $user): array
    {
        return UserChallenge::where('user_id', $user->id)
            ->where('status', 'completed')
            ->pluck('challenge_id')
            ->all();
    }

    /**
     * Select up to RECOMMENDED_LIMIT challenges preferring tier-matching xp_reward range.
     * Tier 1: 0–200, Tier 2: 201–600, Tier 3: 601+
     *
     * @param  array<int>  $excludeChallengeIds
     * @return Collection<int, Challenge>
     */
    private function selectChallengesForTier(int $tier, array $excludeChallengeIds): Collection
    {
        $baseQuery = Challenge::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });

        if (! empty($excludeChallengeIds)) {
            $baseQuery->whereNotIn('id', $excludeChallengeIds);
        }

        [$minXp, $maxXp] = $this->getTierXpRange($tier);

        $tierMatches = (clone $baseQuery)
            ->whereBetween('xp_reward', [$minXp, $maxXp])
            ->orderBy('xp_reward')
            ->limit(self::RECOMMENDED_LIMIT)
            ->get();

        if ($tierMatches->count() >= self::RECOMMENDED_LIMIT) {
            return $tierMatches;
        }

        $tierIds = $tierMatches->pluck('id')->all();
        $remaining = (clone $baseQuery)
            ->whereNotIn('id', $tierIds)
            ->orderBy('xp_reward')
            ->limit(self::RECOMMENDED_LIMIT - $tierMatches->count())
            ->get();

        return $tierMatches->merge($remaining)->take(self::RECOMMENDED_LIMIT);
    }

    /**
     * Select up to RECOMMENDED_LIMIT challenges: prefer tier xp range, then category match or keyword in name/description.
     *
     * @param  array<int>  $excludeChallengeIds
     * @return Collection<int, Challenge>
     */
    private function selectChallengesForTierAndCategory(int $tier, array $excludeChallengeIds, string $topCategory): Collection
    {
        $baseQuery = Challenge::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });

        if (! empty($excludeChallengeIds)) {
            $baseQuery->whereNotIn('id', $excludeChallengeIds);
        }

        [$minXp, $maxXp] = $this->getTierXpRange($tier);

        $candidates = (clone $baseQuery)
            ->orderBy('xp_reward')
            ->limit(100)
            ->get();

        $categoryMatches = $candidates->filter(fn (Challenge $c) => $this->challengeMatchesCategory($c, $topCategory));
        $keywordMatches = $topCategory !== ''
            ? $candidates->filter(fn (Challenge $c) => $this->challengeMatchesKeyword($c, $topCategory))
            : collect();

        $ordered = $candidates->sortByDesc(function (Challenge $c) use ($categoryMatches, $keywordMatches, $minXp, $maxXp) {
            $tierMatch = $c->xp_reward >= $minXp && $c->xp_reward <= $maxXp ? 4 : 0;
            $categoryMatch = $categoryMatches->contains('id', $c->id) ? 2 : 0;
            $keywordMatch = $keywordMatches->contains('id', $c->id) ? 1 : 0;
            return $tierMatch + $categoryMatch + $keywordMatch;
        });

        return $ordered->take(self::RECOMMENDED_LIMIT)->values();
    }

    private function challengeMatchesCategory(Challenge $challenge, string $topCategory): bool
    {
        if ($topCategory === '') {
            return false;
        }
        return strcasecmp((string) $challenge->category, $topCategory) === 0;
    }

    private function challengeMatchesKeyword(Challenge $challenge, string $keyword): bool
    {
        if ($keyword === '') {
            return false;
        }
        $name = (string) $challenge->name;
        $description = (string) $challenge->description;
        return stripos($name, $keyword) !== false || stripos($description, $keyword) !== false;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function getTierXpRange(int $tier): array
    {
        return match ($tier) {
            1 => [0, self::TIER_1_XP_MAX],
            3 => [self::TIER_2_XP_MAX + 1, 999999],
            default => [self::TIER_1_XP_MAX + 1, self::TIER_2_XP_MAX],
        };
    }
}
