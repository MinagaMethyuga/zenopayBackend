<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $today = $this->todayAppDateString();

        UserProfile::query()
            ->where('user_id', $user->getKey())
            ->update(['last_login_date' => $today]);
    }

    /**
     * Call when a new transaction is created.
     *
     * - If user already has a streak updated today (last_activity_date = today): do NOT increment.
     * - If last streak update was yesterday: increment current_streak by 1 and update best_streak if needed.
     * - If last streak update was older than yesterday or null: reset current_streak to 1.
     * - Always set last_activity_date to the transaction's activity day (in app timezone).
     */
    public function registerTransaction(User $user, \DateTimeInterface $occurredAt): void
    {
        $tz = config('app.timezone', 'UTC');
        $activityDay = Carbon::parse($occurredAt)->timezone($tz)->toDateString();

        DB::transaction(function () use ($user, $activityDay, $tz) {
            /** @var UserProfile|null $profile */
            $profile = UserProfile::query()
                ->where('user_id', $user->getKey())
                ->lockForUpdate()
                ->first();

            if (! $profile) {
                return;
            }

            $lastActivityDay = $profile->last_activity_date
                ? Carbon::parse($profile->last_activity_date)->timezone($tz)->toDateString()
                : null;

            $today = $this->todayAppDateString();

            // Already counted this calendar day for streak – do not increment.
            if ($lastActivityDay === $activityDay) {
                return;
            }

            if ($lastActivityDay === null) {
                // First ever streak day or gap: reset to 1.
                $profile->current_streak = 1;
            } else {
                $diffInDays = (int) Carbon::parse($lastActivityDay)->timezone($tz)->diffInDays(
                    Carbon::parse($activityDay)->timezone($tz),
                    false
                );

                if ($diffInDays === 1) {
                    // Consecutive day – increment.
                    $profile->current_streak = (int) $profile->current_streak + 1;
                } else {
                    // Gap or same day (already handled): reset to 1.
                    $profile->current_streak = 1;
                }
            }

            if ((int) $profile->current_streak > (int) $profile->best_streak) {
                $profile->best_streak = (int) $profile->current_streak;
            }

            $profile->last_activity_date = $activityDay;
            $profile->save();

            Log::info('Streak updated after transaction', [
                'user_id' => $user->getKey(),
                'last_streak_date' => $lastActivityDay,
                'current_streak' => (int) $profile->current_streak,
                'best_streak' => (int) $profile->best_streak,
            ]);
        });
    }

    private function todayAppDateString(): string
    {
        return now()->timezone(config('app.timezone', 'UTC'))->toDateString();
    }
}
