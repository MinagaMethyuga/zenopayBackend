<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class XpService
{
    public const LEVEL_BEGINNER = 'Beginner';
    public const LEVEL_INTERMEDIATE = 'Intermediate';
    public const LEVEL_PRO = 'Pro';

    public const XP_PER_NEW_TRANSACTION = 5;

    /** Level stored in user_profiles: 1=Beginner, 2=Intermediate, 3=Pro */
    private const LEVEL_INT_BEGINNER = 1;
    private const LEVEL_INT_INTERMEDIATE = 2;
    private const LEVEL_INT_PRO = 3;

    /**
     * Add XP and upgrade level if thresholds are crossed (user_profiles).
     * Level can only ever increase (never downgrade).
     */
    public function addXp(User $user, int $xpToAdd): User
    {
        if ($xpToAdd < 0) {
            throw new InvalidArgumentException('XP to add must be >= 0.');
        }

        if ($xpToAdd === 0) {
            return $user->loadMissing('profile');
        }

        return DB::transaction(function () use ($user, $xpToAdd) {
            $profile = UserProfile::query()
                ->where('user_id', $user->getKey())
                ->lockForUpdate()
                ->first();

            if (!$profile) {
                UserProfile::create([
                    'user_id' => $user->getKey(),
                    'xp' => 0,
                    'level' => self::LEVEL_INT_BEGINNER,
                ]);
                $profile = UserProfile::query()
                    ->where('user_id', $user->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();
            }

            $newXp = (int) $profile->xp + $xpToAdd;
            $profile->xp = $newXp;

            $calculatedLevelInt = $this->levelIntegerForXp($newXp);
            if ($calculatedLevelInt > (int) $profile->level) {
                $profile->level = $calculatedLevelInt;
            }

            $profile->save();

            return $user->refresh()->load('profile');
        });
    }

    /**
     * Rule: every created income/expense transaction gives exactly 5 XP.
     */
    public function awardForNewTransaction(User $user): User
    {
        return $this->addXp($user, self::XP_PER_NEW_TRANSACTION);
    }

    /** Level name from XP (for API). */
    public function levelForXp(int $xp): string
    {
        if ($xp >= 20_000) {
            return self::LEVEL_PRO;
        }

        if ($xp >= 10_000) {
            return self::LEVEL_INTERMEDIATE;
        }

        return self::LEVEL_BEGINNER;
    }

    /** Level as integer for user_profiles.level (1/2/3). */
    private function levelIntegerForXp(int $xp): int
    {
        if ($xp >= 20_000) {
            return self::LEVEL_INT_PRO;
        }

        if ($xp >= 10_000) {
            return self::LEVEL_INT_INTERMEDIATE;
        }

        return self::LEVEL_INT_BEGINNER;
    }

    /**
     * XP needed to reach the next level threshold.
     * Pro has no next level (returns 0).
     */
    public function xpToNextLevel(int $xp): int
    {
        if ($xp >= 20_000) {
            return 0;
        }

        if ($xp >= 10_000) {
            return 20_000 - $xp;
        }

        return 10_000 - $xp;
    }

    /** Resolve level name from profile level integer (for API). */
    public function levelNameFromInt(?int $levelInt): string
    {
        return match ($levelInt) {
            self::LEVEL_INT_BEGINNER => self::LEVEL_BEGINNER,
            self::LEVEL_INT_INTERMEDIATE => self::LEVEL_INTERMEDIATE,
            self::LEVEL_INT_PRO => self::LEVEL_PRO,
            default => self::LEVEL_BEGINNER,
        };
    }

}
