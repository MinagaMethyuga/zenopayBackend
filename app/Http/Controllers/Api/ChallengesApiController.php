<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\UserChallenge;
use App\Services\AdaptiveChallengeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChallengesApiController extends Controller
{
    public function __construct(
        private AdaptiveChallengeService $adaptiveChallengeService
    ) {}

    /**
     * Get all challenges (catalog)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Challenge::query();

        // Filter by status
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            if ($filter === 'active') {
                $query->where('is_active', true);
            } elseif ($filter === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by frequency
        if ($request->has('frequency')) {
            $query->where('frequency', $request->get('frequency'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->get('difficulty'));
        }

        // Only currently available by date window
        $query->where(function ($q) {
            $q->whereNull('starts_at')
                ->orWhere('starts_at', '<=', now());
        })->where(function ($q) {
            $q->whereNull('ends_at')
                ->orWhere('ends_at', '>=', now());
        });

        $challenges = $query->orderBy('created_at', 'desc')->get();

        return response()->json(
            $challenges->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'name' => $challenge->name,
                    'description' => $challenge->description,
                    'difficulty' => $challenge->difficulty,
                    'category' => $challenge->category,
                    'frequency' => $challenge->frequency,
                    'xp_reward' => $challenge->xp_reward,

                    // ✅ include both (Flutter needs both)
                    'unlock_badge' => (bool) $challenge->unlock_badge,
                    'badge_image_url' => $challenge->badge_image_url,

                    'icon' => $challenge->icon ?? '🎯',
                    'target_type' => $challenge->target_type,
                    'target_value' => $challenge->target_value,
                    'duration' => $challenge->duration,
                    'type' => $challenge->type,
                    'is_active' => (bool) $challenge->is_active,
                    'win_conditions' => $challenge->win_conditions,
                    'starts_at' => $challenge->starts_at?->toIso8601String(),
                    'ends_at' => $challenge->ends_at?->toIso8601String(),
                    'created_at' => $challenge->created_at?->toIso8601String(),
                    'updated_at' => $challenge->updated_at?->toIso8601String(),
                ];
            })
        );
    }

    /**
     * Get a single challenge by ID
     */
    public function show(int $id): JsonResponse
    {
        $challenge = Challenge::find($id);

        if (!$challenge) {
            return response()->json(['error' => 'Challenge not found'], 404);
        }

        return response()->json([
            'id' => $challenge->id,
            'name' => $challenge->name,
            'description' => $challenge->description,
            'difficulty' => $challenge->difficulty,
            'category' => $challenge->category,
            'frequency' => $challenge->frequency,
            'xp_reward' => $challenge->xp_reward,

            'unlock_badge' => (bool) $challenge->unlock_badge,
            'badge_image_url' => $challenge->badge_image_url,

            'icon' => $challenge->icon ?? '🎯',
            'target_type' => $challenge->target_type,
            'target_value' => $challenge->target_value,
            'duration' => $challenge->duration,
            'type' => $challenge->type,
            'is_active' => (bool) $challenge->is_active,
            'win_conditions' => $challenge->win_conditions,
            'starts_at' => $challenge->starts_at?->toIso8601String(),
            'ends_at' => $challenge->ends_at?->toIso8601String(),
            'created_at' => $challenge->created_at?->toIso8601String(),
            'updated_at' => $challenge->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Get daily challenges
     */
    public function daily(): JsonResponse
    {
        $challenges = Challenge::where('is_active', true)
            ->where('frequency', 'Daily')
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderBy('xp_reward', 'desc')
            ->limit(5)
            ->get();

        return response()->json(
            $challenges->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'name' => $challenge->name,
                    'description' => $challenge->description,
                    'difficulty' => $challenge->difficulty,
                    'category' => $challenge->category,
                    'frequency' => $challenge->frequency,
                    'xp_reward' => $challenge->xp_reward,

                    'unlock_badge' => (bool) $challenge->unlock_badge,
                    'badge_image_url' => $challenge->badge_image_url,

                    'icon' => $challenge->icon ?? '🎯',
                    'target_type' => $challenge->target_type,
                    'target_value' => $challenge->target_value,
                    'duration' => $challenge->duration,
                    'type' => $challenge->type,
                    'is_active' => (bool) $challenge->is_active,
                    'win_conditions' => $challenge->win_conditions,
                    'starts_at' => $challenge->starts_at?->toIso8601String(),
                    'ends_at' => $challenge->ends_at?->toIso8601String(),
                    'created_at' => $challenge->created_at?->toIso8601String(),
                    'updated_at' => $challenge->updated_at?->toIso8601String(),
                ];
            })
        );
    }

    /**
     * Stats
     */
    public function stats(): JsonResponse
    {
        $activeCount = Challenge::where('is_active', true)->count();
        $totalXP = Challenge::where('is_active', true)->sum('xp_reward');
        $challengesByFrequency = Challenge::where('is_active', true)
            ->selectRaw('frequency, count(*) as count')
            ->groupBy('frequency')
            ->get();

        return response()->json([
            'active_count' => $activeCount,
            'total_xp' => $totalXP,
            'by_frequency' => $challengesByFrequency,
            'total_challenges' => Challenge::count(),
        ]);
    }

    /**
     * Accept a challenge (creates user_challenges row)
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $challenge = Challenge::find($id);

        if (!$challenge) {
            return response()->json(['error' => 'Challenge not found'], 404);
        }

        if (!$challenge->is_active) {
            return response()->json(['error' => 'Challenge is not active'], 400);
        }

        $uc = UserChallenge::firstOrCreate(
            ['user_id' => $user->id, 'challenge_id' => $challenge->id],
            ['status' => 'active', 'progress' => 0, 'accepted_at' => now()]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Challenge accepted',
            'user_challenge' => [
                'id' => $uc->id,
                'status' => $uc->status,
                'progress' => (float) $uc->progress,
                'accepted_at' => optional($uc->accepted_at)->toIso8601String(),
            ],
            'challenge' => [
                'id' => $challenge->id,
                'name' => $challenge->name,
                'xp_reward' => $challenge->xp_reward,
                'unlock_badge' => (bool) $challenge->unlock_badge,
                'badge_image_url' => $challenge->badge_image_url,
                'target_type' => $challenge->target_type,
                'target_value' => $challenge->target_value,
                'category' => $challenge->category,
            ]
        ]);
    }

    /**
     * GET /api/challenges/recommended
     * Returns up to 8 challenges personalized by tier and top spending category, with metadata.
     */
    public function recommended(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $result = $this->adaptiveChallengeService->getRecommendedWithMetadata($user);

        return response()->json([
            'tier' => $result['tier'],
            'top_category' => $result['top_category'],
            'recommended' => $result['recommended']->map(fn ($challenge) => $this->mapChallengeToApi($challenge)),
        ]);
    }

    /**
     * GET /api/challenges/for-you
     * Returns accepted + available challenges for the authenticated user in one payload.
     * Progress is read from user_challenges.progress (source of truth, updated by ChallengeProgressService).
     */
    public function forYou(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Single query: all user_challenges for this user with challenge eager loaded
        $userChallenges = UserChallenge::with('challenge')
            ->where('user_id', $user->id)
            ->get();

        $acceptedChallengeIds = $userChallenges->pluck('challenge_id')->filter()->unique()->values()->all();

        // Available = active challenges (within date window) that user has NOT accepted
        $availableQuery = Challenge::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });

        if (!empty($acceptedChallengeIds)) {
            $availableQuery->whereNotIn('id', $acceptedChallengeIds);
        }

        $availableChallenges = $availableQuery->orderBy('created_at', 'desc')->get();

        $accepted = $userChallenges
            ->filter(fn ($uc) => $uc->challenge !== null)
            ->map(fn ($uc) => $this->mapToForYouAcceptedItem($uc))
            ->values()
            ->all();

        $available = $availableChallenges
            ->map(fn ($c) => $this->mapToForYouAvailableItem($c))
            ->values()
            ->all();

        return response()->json([
            'accepted' => $accepted,
            'available' => $available,
        ]);
    }

    /**
     * Map challenge + user_challenge to the "accepted" item shape.
     * Progress is from user_challenges.progress (no recomputation).
     */
    private function mapToForYouAcceptedItem(UserChallenge $uc): array
    {
        $c = $uc->challenge;
        $progress = (float) $uc->progress;
        $target = $this->parseTargetValue($c->target_value ?? null);
        if ($uc->status === 'completed' && $target > 0 && $progress < $target) {
            $progress = $target;
        }

        return [
            'id' => $c->id,
            'title' => $c->name,
            'description' => (string) $c->description,
            'type' => (string) ($c->type ?? 'regular'),
            'target' => $target > 0 ? $target : null,
            'reward_points' => (int) $c->xp_reward,
            'icon' => (string) ($c->icon ?? '🎯'),
            'color' => (string) ($c->difficulty_color ?? 'gray'),
            'frequency' => $this->normalizeFrequencyForApi($c->frequency),
            'status' => $uc->status === 'completed' ? 'completed' : 'accepted',
            'progress' => $progress,
            'accepted_at' => $uc->accepted_at?->toIso8601String() ?? null,
            'completed_at' => $uc->completed_at?->toIso8601String(),
        ];
    }

    /**
     * Map challenge to the "available" item shape (no user_challenge).
     */
    private function mapToForYouAvailableItem(Challenge $c): array
    {
        $target = $this->parseTargetValue($c->target_value ?? null);
        return [
            'id' => $c->id,
            'title' => $c->name,
            'description' => (string) $c->description,
            'type' => (string) ($c->type ?? 'regular'),
            'target' => $target > 0 ? $target : null,
            'reward_points' => (int) $c->xp_reward,
            'icon' => (string) ($c->icon ?? '🎯'),
            'color' => (string) ($c->difficulty_color ?? 'gray'),
            'frequency' => $this->normalizeFrequencyForApi($c->frequency),
        ];
    }

    private function parseTargetValue(?string $val): float
    {
        if ($val === null || $val === '') {
            return 0.0;
        }
        $s = str_replace([',', ' '], '', (string) $val);
        return preg_match('/(\d+(\.\d+)?)/', $s, $m) ? (float) $m[1] : 0.0;
    }

    private function normalizeFrequencyForApi(?string $frequency): string
    {
        if ($frequency === null || $frequency === '') {
            return 'weekly';
        }
        return match (strtolower(str_replace('-', '', $frequency))) {
            'daily' => 'daily',
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            'quarterly' => 'quarterly',
            'yearly' => 'yearly',
            'onetime' => 'once',
            default => strtolower($frequency),
        };
    }

    private function mapChallengeToApi(Challenge $challenge): array
    {
        return [
            'id' => $challenge->id,
            'name' => $challenge->name,
            'description' => $challenge->description,
            'difficulty' => $challenge->difficulty,
            'category' => $challenge->category,
            'frequency' => $challenge->frequency,
            'xp_reward' => $challenge->xp_reward,
            'unlock_badge' => (bool) $challenge->unlock_badge,
            'badge_image_url' => $challenge->badge_image_url,
            'icon' => $challenge->icon ?? '🎯',
            'target_type' => $challenge->target_type,
            'target_value' => $challenge->target_value,
            'duration' => $challenge->duration,
            'type' => $challenge->type,
            'is_active' => (bool) $challenge->is_active,
            'win_conditions' => $challenge->win_conditions,
            'starts_at' => $challenge->starts_at?->toIso8601String(),
            'ends_at' => $challenge->ends_at?->toIso8601String(),
            'created_at' => $challenge->created_at?->toIso8601String(),
            'updated_at' => $challenge->updated_at?->toIso8601String(),
        ];
    }
}
