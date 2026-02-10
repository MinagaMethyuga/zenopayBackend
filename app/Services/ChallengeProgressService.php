<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserChallenge;
use App\Models\UserBadge;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChallengeProgressService
{
    public static function handleNewTransaction(Transaction $tx): void
    {
        $txType = strtolower((string) $tx->type);
        $txCategory = trim((string) $tx->category);
        $txAmount = (float) $tx->amount;

        Log::channel('single')->info('ChallengeProgressService: new transaction', [
            'transaction_id' => $tx->id,
            'user_id' => $tx->user_id,
            'type' => $tx->type,
            'category' => $tx->category,
            'amount' => $tx->amount,
        ]);

        $active = UserChallenge::with('challenge')
            ->where('user_id', $tx->user_id)
            ->where('status', 'active')
            ->get();

        Log::channel('single')->info('ChallengeProgressService: active user challenges', [
            'user_id' => $tx->user_id,
            'count' => $active->count(),
            'challenge_ids' => $active->pluck('challenge_id')->toArray(),
        ]);

        foreach ($active as $uc) {
            $c = $uc->challenge;
            if (!$c) continue;

            $winConditions = $c->win_conditions;
            if (empty($winConditions) || !is_array($winConditions)) {
                continue;
            }

            if (!self::transactionMatchesWinCondition($txType, $txCategory, $txAmount, $winConditions)) {
                continue;
            }

            $amountToAdd = 0.0;
            if (!empty($winConditions['sum_amount'])) {
                $amountToAdd = $txAmount;
            }

            if ($amountToAdd <= 0) {
                continue;
            }

            Log::channel('single')->info('ChallengeProgressService: challenge matched, applying progress', [
                'challenge_id' => $c->id,
                'challenge_name' => $c->name,
                'user_challenge_id' => $uc->id,
                'amount_to_add' => $amountToAdd,
                'current_progress' => (float) $uc->progress,
            ]);

            self::applyProgress($uc, $amountToAdd);
        }
    }

    /**
     * Check if transaction matches win_conditions: transaction_type, transaction_category, min_amount.
     */
    private static function transactionMatchesWinCondition(string $txType, string $txCategory, float $txAmount, array $winConditions): bool
    {
        $requiredType = isset($winConditions['transaction_type']) ? strtolower(trim((string) $winConditions['transaction_type'])) : null;
        if ($requiredType !== null && $requiredType !== '') {
            if ($txType !== $requiredType) {
                return false;
            }
        }

        $requiredCategory = isset($winConditions['transaction_category']) ? trim((string) $winConditions['transaction_category']) : null;
        if ($requiredCategory !== null && $requiredCategory !== '') {
            if (strcasecmp($txCategory, $requiredCategory) !== 0) {
                return false;
            }
        }

        $minAmount = isset($winConditions['min_amount']) ? (float) $winConditions['min_amount'] : 0;
        if ($minAmount > 0 && $txAmount < $minAmount) {
            return false;
        }

        return true;
    }

    private static function parseTargetValue($val): float
    {
        if ($val === null) return 0.0;

        $s = str_replace([',', ' '], '', (string) $val);
        if (preg_match('/(\d+(\.\d+)?)/', $s, $m)) {
            return (float) $m[1];
        }
        return 0.0;
    }

    private static function applyProgress(UserChallenge $uc, float $amount): void
    {
        $ucId = (int) $uc->id;

        DB::transaction(function () use ($ucId, $amount) {
            $locked = UserChallenge::query()
                ->with('challenge')
                ->whereKey($ucId)
                ->lockForUpdate()
                ->first();

            if (!$locked || !$locked->challenge) {
                return;
            }

            if ($locked->status === 'completed') {
                return; // already completed; never award XP twice
            }

            $c = $locked->challenge;
            $target = self::parseTargetValue($c->target_value ?? null);
            if ($target <= 0) {
                return;
            }

            $locked->progress = (float) $locked->progress + $amount;

            $justCompleted = false;
            if ($locked->progress >= $target) {
                $locked->progress = $target;
                $locked->status = 'completed';
                $locked->completed_at = now();
                $justCompleted = true;
            }

            $locked->save();

            if (!$justCompleted) {
                return;
            }

            // Award XP for completion exactly once (user_challenges row is locked).
            $xpReward = (int) ($c->xp_reward ?? 0);
            if ($xpReward > 0) {
                $user = User::query()->whereKey($locked->user_id)->first();
                if ($user) {
                    app(XpService::class)->addXp($user, $xpReward);
                }
            }

            if ((bool) $c->unlock_badge) {
                UserBadge::updateOrCreate(
                    ['user_id' => $locked->user_id, 'challenge_id' => $c->id],
                    ['badge_image' => $c->badge_image, 'unlocked_at' => now()]
                );
            }

            Log::channel('single')->info('ChallengeProgressService: challenge completed', [
                'user_challenge_id' => $locked->id,
                'challenge_id' => $c->id,
                'xp_awarded' => $xpReward,
                'badge_unlocked' => (bool) $c->unlock_badge,
            ]);
        });
    }
}
