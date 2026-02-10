<!DOCTYPE html>
<html class="dark" lang="en"><head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>User Management - Zenopay</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#30e87a",
                        "background-light": "#f6f8f7",
                        "background-dark": "#0D0E12",
                        "surface-dark": "#16171C",
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "1rem", "lg": "1.25rem", "xl": "1.5rem", "full": "9999px"},
                    boxShadow: {
                        'glow-primary': '0 0 15px 0 rgba(48, 232, 122, 0.5)',
                        'glow-primary-hover': '0 0 25px 3px rgba(48, 232, 122, 0.6)',
                    }
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24
        }
    </style>
</head>
<body class="font-display bg-background-dark text-gray-200 flex">
<x-dashboardNavBar/>
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
    <div class="flex h-full grow flex-col">
        <div class="flex flex-1 justify-center p-4 sm:p-6 lg:p-8">
            <div class="flex w-full max-w-7xl flex-col gap-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-4xl font-bold tracking-tight text-white">User Management</h1>
                    <button type="button" class="flex h-12 min-w-[84px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-full bg-primary px-6 text-base font-bold text-background-dark shadow-glow-primary transition-shadow hover:shadow-glow-primary-hover">
                        <span class="material-symbols-outlined">add</span>
                        <span>Add User</span>
                    </button>
                </div>
                <form method="GET" action="{{ route('UserManagement') }}" class="flex flex-col gap-4 md:flex-row md:items-center">
                    <input type="hidden" name="status" value="{{ $statusFilter ?? 'all' }}" id="form-status"/>
                    <div class="flex-grow">
                        <label class="flex h-12 w-full flex-col">
                            <div class="group flex h-full w-full flex-1 items-stretch rounded-full bg-surface-dark ring-1 ring-primary/50 transition-all focus-within:ring-2 focus-within:ring-primary focus-within:shadow-glow-primary">
                                <div class="flex items-center justify-center pl-4 text-primary">
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                                <input type="search" name="search" value="{{ old('search', $search ?? '') }}" class="form-input h-full min-w-0 flex-1 resize-none overflow-hidden border-none bg-transparent px-3 text-base font-normal leading-normal text-white placeholder:text-gray-400 focus:outline-none focus:ring-0" placeholder="Search by name, email, or ID..."/>
                            </div>
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('UserManagement', ['search' => $search, 'status' => 'all']) }}" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-5 {{ ($statusFilter ?? 'all') === 'all' ? 'bg-primary text-background-dark' : 'bg-surface-dark text-gray-400 hover:bg-white/5' }}">
                            <p class="text-sm font-bold">All</p>
                        </a>
                        <a href="{{ route('UserManagement', ['search' => $search, 'status' => 'active']) }}" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-5 {{ ($statusFilter ?? '') === 'active' ? 'bg-sky-500/20 text-sky-400 ring-1 ring-sky-400' : 'bg-surface-dark text-sky-400 ring-1 ring-sky-400/50 hover:bg-sky-400/10' }}">
                            <p class="text-sm font-medium">Active</p>
                        </a>
                        <a href="{{ route('UserManagement', ['search' => $search, 'status' => 'low_activity']) }}" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-5 {{ ($statusFilter ?? '') === 'low_activity' ? 'bg-yellow-500/20 text-yellow-400 ring-1 ring-yellow-400' : 'bg-surface-dark text-yellow-400 ring-1 ring-yellow-400/50 hover:bg-yellow-400/10' }}">
                            <p class="text-sm font-medium">Low Activity</p>
                        </a>
                        <a href="{{ route('UserManagement', ['search' => $search, 'status' => 'inactive']) }}" class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full px-5 {{ ($statusFilter ?? '') === 'inactive' ? 'bg-pink-500/20 text-pink-400 ring-1 ring-pink-400' : 'bg-surface-dark text-pink-400 ring-1 ring-pink-400/50 hover:bg-pink-400/10' }}">
                            <p class="text-sm font-medium">Inactive</p>
                        </a>
                    </div>
                </form>
                <div class="w-full overflow-x-auto">
                    <div class="overflow-hidden rounded-xl border border-gray-800 bg-surface-dark">
                        <table class="min-w-full table-auto">
                            <thead class="bg-black/20">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Challenges Completed</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">XP / Level</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Registration Date</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                            @forelse($users as $user)
                                @php
                                    $completed = $user->completed_challenges_count ?? 0;
                                    $total = $totalChallenges ?? 1;
                                    $pct = $total > 0 ? min(100, round(($completed / $total) * 100)) : 0;
                                    $status = $user->display_status ?? 'inactive';
                                    $statusLabel = match($status) {
                                        'active' => 'Active',
                                        'low_activity' => 'Low activity',
                                        default => 'Inactive',
                                    };
                                    $statusClass = match($status) {
                                        'active' => 'bg-sky-500/10 text-sky-400',
                                        'low_activity' => 'bg-yellow-500/10 text-yellow-400',
                                        default => 'bg-pink-500/10 text-pink-400',
                                    };
                                    $statusDot = match($status) {
                                        'active' => 'bg-sky-500',
                                        'low_activity' => 'bg-yellow-500',
                                        default => 'bg-pink-500',
                                    };
                                    $initials = strtoupper(mb_substr($user->name ?? 'U', 0, 1) . mb_substr(preg_replace('/\s+/', '', $user->name ?? '') ?: 'U', 1, 1) ?: '');
                                    if ($initials === '') { $initials = 'U'; }
                                @endphp
                                <tr class="transition-colors hover:bg-black/10">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm">{{ $initials }}</div>
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2 rounded-full {{ $statusClass }} px-3 py-1 text-xs font-medium">
                                            <span class="h-2 w-2 rounded-full {{ $statusDot }}"></span>
                                            {{ $statusLabel }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 overflow-hidden rounded-full bg-gray-700"><div class="h-1.5 rounded-full bg-primary" style="width: {{ $pct }}%;"></div></div>
                                            <p class="text-sm font-medium text-white">{{ $completed }} / {{ $total }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-medium text-white">{{ $user->profile?->xp ?? 0 }} XP / Lv {{ $user->profile?->level ?? 1 }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $user->created_at?->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" class="text-gray-400 hover:text-white"><span class="material-symbols-outlined">more_vert</span></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">No users found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($users) && $users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</body></html>
