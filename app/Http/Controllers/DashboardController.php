<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserChallenge;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);

        // Stats from tables
        $totalUsers = User::count();
        $totalChallenges = Challenge::count();
        $completedChallenges = UserChallenge::where('status', 'completed')->count();
        $totalXp = UserProfile::sum('xp');

        // Active this week: users with last_login_date in last 7 days
        $activeThisWeek = UserProfile::where('last_login_date', '>=', $weekAgo->toDateString())->count();
        $previousWeekActive = UserProfile::whereBetween('last_login_date', [
            $now->copy()->subDays(14)->toDateString(),
            $weekAgo->copy()->subDay()->toDateString(),
        ])->count();
        $activeWeekChange = $previousWeekActive > 0
            ? round((($activeThisWeek - $previousWeekActive) / $previousWeekActive) * 100)
            : 0;

        // Streak rate: % of users with profile who have current_streak > 0
        $usersWithProfile = UserProfile::count();
        $usersWithStreak = UserProfile::where('current_streak', '>', 0)->count();
        $streakRate = $usersWithProfile > 0 ? round(($usersWithStreak / $usersWithProfile) * 100) : 0;

        // Weekly active users per day (last 7 days) for chart
        $weeklyActiveByDay = UserProfile::query()
            ->where('last_login_date', '>=', $weekAgo->toDateString())
            ->select(DB::raw('last_login_date as date'), DB::raw('count(*) as count'))
            ->groupBy('last_login_date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->toDateString();
            $chartLabels[] = $now->copy()->subDays($i)->format('D');
            $chartValues[] = $weeklyActiveByDay[$day] ?? 0;
        }

        // Recent activity: completed challenges, badges, new users (last 30 days)
        $recentCompletions = UserChallenge::with(['user', 'challenge'])
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        $recentBadges = UserBadge::with(['user', 'challenge'])
            ->whereNotNull('unlocked_at')
            ->orderByDesc('unlocked_at')
            ->limit(5)
            ->get();

        $recentUsers = User::orderByDesc('created_at')->limit(5)->get();

        $activities = collect();
        foreach ($recentCompletions as $uc) {
            $activities->push([
                'type' => 'challenge_completed',
                'at' => $uc->completed_at,
                'user_name' => $uc->user->name ?? 'User',
                'title' => $uc->challenge->name ?? 'Challenge',
                'icon' => 'school',
                'icon_color' => 'blue',
            ]);
        }
        foreach ($recentBadges as $ub) {
            $user = $ub->user ?? null;
            $challenge = $ub->challenge ?? null;
            $activities->push([
                'type' => 'badge_earned',
                'at' => $ub->unlocked_at,
                'user_name' => $user ? $user->name : 'User',
                'title' => $challenge ? "earned \"{$challenge->name}\"" : 'earned a badge',
                'icon' => 'military_tech',
                'icon_color' => 'primary',
            ]);
        }
        foreach ($recentUsers as $u) {
            $activities->push([
                'type' => 'new_user',
                'at' => $u->created_at,
                'user_name' => $u->name,
                'title' => 'joined Zenopay',
                'icon' => 'person_add',
                'icon_color' => 'orange',
            ]);
        }
        $activities = $activities->sortByDesc(fn ($a) => $a['at']->getTimestamp())->take(8)->values();

        // Total rewards: we don't have a rewards table; use sum of positive transaction amounts as proxy or 0
        $totalRewardsAmount = Transaction::where('type', 'income')->sum('amount');

        // KYC pending: no KYC table; show 0 (placeholder for future)
        $kycPending = 0;

        // Simple growth placeholder for Total Users (compare to last month)
        $lastMonthUsers = User::where('created_at', '<', $now->copy()->subMonth())->count();
        $userGrowth = $totalUsers > 0 && $lastMonthUsers > 0
            ? round((($totalUsers - $lastMonthUsers) / $lastMonthUsers) * 100)
            : 0;

        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'activeThisWeek' => $activeThisWeek,
            'activeWeekChange' => $activeWeekChange,
            'totalChallenges' => $totalChallenges,
            'completedChallenges' => $completedChallenges,
            'streakRate' => $streakRate,
            'totalXp' => $totalXp,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'chartMax' => max(1, ...$chartValues),
            'activities' => $activities,
            'totalRewardsAmount' => $totalRewardsAmount,
            'kycPending' => $kycPending,
            'userGrowth' => $userGrowth,
        ]);
    }
}
