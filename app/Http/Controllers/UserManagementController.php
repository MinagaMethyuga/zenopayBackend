<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('profile')
            ->withCount(['userChallenges as completed_challenges_count' => function ($q) {
                $q->where('status', 'completed');
            }]);

        // Search: name, email, or ID
        if ($search = $request->filled('search') ? trim($request->search) : null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        // Status filter: all | active | low_activity | inactive
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter !== 'all') {
            $now = Carbon::now();
            $sevenDaysAgo = $now->copy()->subDays(7);
            $thirtyDaysAgo = $now->copy()->subDays(30);
            $sevenDaysAgoEnd = $sevenDaysAgo->copy()->subDay()->toDateString();

            if ($statusFilter === 'active') {
                $query->where('users.is_active', true)
                    ->whereHas('profile', function ($q) use ($sevenDaysAgo) {
                        $q->where('last_login_date', '>=', $sevenDaysAgo->toDateString());
                    });
            } elseif ($statusFilter === 'low_activity') {
                $query->where('users.is_active', true)
                    ->whereHas('profile', function ($q) use ($thirtyDaysAgo, $sevenDaysAgoEnd) {
                        $q->whereBetween('last_login_date', [$thirtyDaysAgo->toDateString(), $sevenDaysAgoEnd]);
                    });
            } elseif ($statusFilter === 'inactive') {
                $query->where(function ($q) use ($thirtyDaysAgo) {
                    $q->where('is_active', false)
                        ->orWhereDoesntHave('profile')
                        ->orWhereHas('profile', function ($q2) use ($thirtyDaysAgo) {
                            $q2->whereNull('last_login_date')
                                ->orWhere('last_login_date', '<', $thirtyDaysAgo->toDateString());
                        });
                });
            }
        }

        $query->orderByDesc('users.created_at');
        $users = $query->paginate(15)->withQueryString();

        // Resolve display status per user (for view)
        $users->getCollection()->transform(function ($user) {
            $user->display_status = $this->displayStatus($user);
            return $user;
        });

        $totalChallenges = Challenge::count();

        return view('UserManagement', [
            'users' => $users,
            'search' => $request->get('search'),
            'statusFilter' => $statusFilter,
            'totalChallenges' => $totalChallenges,
        ]);
    }

    private function displayStatus(User $user): string
    {
        if (! $user->is_active) {
            return 'inactive';
        }
        $profile = $user->profile;
        $lastLogin = $profile?->last_login_date;
        if (! $lastLogin) {
            return 'inactive';
        }
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(7);
        $thirtyDaysAgo = $now->copy()->subDays(30);
        if ($lastLogin->gte($sevenDaysAgo)) {
            return 'active';
        }
        if ($lastLogin->gte($thirtyDaysAgo)) {
            return 'low_activity';
        }
        return 'inactive';
    }
}
