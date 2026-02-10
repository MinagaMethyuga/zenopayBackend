<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StreakService
{
    /**
     * Mark that the user has successfully logged in today.
     *
     * This alone does NOT increment the streak. The streak only increments
     * once the user also creates at least one transaction for this same day.
     */
    public function registerLogin(User $user): void
    {
        $today = now()->toDateString();

        UserProfile::query()
            ->where('user_id', $user->getKey())
            ->update(['last_login_date' => $today]);
    }

    /**
     * Call when a new transaction is created.
     *
     * Rule:
     * - A day only counts as a "streak day" if the user:
     *   1) logged in that calendar day, AND
     *   2) created at least one transaction on that same calendar day.
     *
     * We increment the streak at most once per calendar day.
     */
    public function registerTransaction(User $user, \DateTimeInterface $occurredAt): void
    {
        $activityDay = Carbon::instance($occurredAt)->toDateString();

        DB::transaction(function () use ($user, $activityDay) {
            /** @var UserProfile|null $profile */
            $profile = UserProfile::query()
                ->where('user_id', $user->getKey())
                ->lockForUpdate()
                ->first();

            if (!$profile) {
                return;
            }

            // Require that the user has logged in on this same calendar day.
            $lastLoginDay = $profile->last_login_date
                ? $profile->last_login_date->toDateString()
                : null;

            if ($lastLoginDay !== $activityDay) {
                // User did not log in on this day, so we do NOT count it for streak.
                return;
            }

            $lastActivityDay = $profile->last_activity_date
                ? $profile->last_activity_date->toDateString()
                : null;

            // Already counted this calendar day for streak – nothing to do.
            if ($lastActivityDay === $activityDay) {
                return;
            }

            // First ever streak day.
            if ($lastActivityDay === null) {
                $profile->current_streak = 1;
            } else {
                $diffInDays = Carbon::parse($lastActivityDay)->diffInDays($activityDay);

                if ($diffInDays === 1) {
                    // Consecutive day – continue the streak.
                    $profile->current_streak = (int) $profile->current_streak + 1;
                } elseif ($diffInDays > 1) {
                    // Break in the streak – start over from 1.
                    $profile->current_streak = 1;
                } else {
                    // Activity day is before last_activity_date; ignore.
                    return;
                }
            }

            if ((int) $profile->current_streak > (int) $profile->best_streak) {
                $profile->best_streak = (int) $profile->current_streak;
            }

            $profile->last_activity_date = $activityDay;
            $profile->save();
        });
    }
}

