<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Zenopay - Challenge Management</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;700;900&family=Spline+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#25f478",
                        "background-light": "#f5f8f7",
                        "background-dark": "#0D0F10",
                        "card-dark": "#121417",
                        "surface-dark": "#121417"
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "1.5rem",
                        "xl": "2rem",
                        "2xl": "3rem",
                        "full": "9999px"
                    },
                    boxShadow: {
                        "neon": "0 0 10px rgba(37, 244, 120, 0.3), 0 0 20px rgba(37, 244, 120, 0.1)"
                    }
                },
            },
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0D0F10; }
        ::-webkit-scrollbar-thumb { background: #224932; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #25f478; }
        .glass-panel {
            background: rgba(18, 20, 23, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white font-display overflow-hidden">
<div class="relative flex h-screen w-full flex-row overflow-hidden">
    <x-dashboardNavBar/>
    <main class="flex-1 flex flex-col h-full relative overflow-hidden bg-background-dark">
        <header class="flex items-center justify-between px-8 py-5 border-b border-[#224932]/50 bg-background-dark/80 backdrop-blur-md sticky top-0 z-20">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-[#90cba8]">Dashboard</span>
                    <span class="material-symbols-outlined text-[12px] text-[#90cba8]">chevron_right</span>
                    <span class="text-white font-medium">Challenges</span>
                </div>
                <h2 class="text-white text-2xl font-bold tracking-tight">Challenge Management</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex relative">
                    <input id="searchInput" class="h-11 bg-[#121417] text-white pl-10 pr-4 rounded-full border border-[#224932]/30 focus:border-primary/50 focus:ring-2 focus:ring-primary/20 placeholder-[#5c856e] text-sm w-64 outline-none" placeholder="Search challenges..." type="text"/>
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#5c856e] text-[20px]">search</span>
                </div>
                <button class="size-11 flex items-center justify-center rounded-full bg-[#121417] border border-[#224932] text-white hover:bg-[#1a1d21] hover:border-primary/50 transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-3 size-2 bg-red-500 rounded-full"></span>
                </button>
                <button onclick="openCreatePanel()" class="md:hidden size-11 flex items-center justify-center rounded-full bg-primary text-background-dark font-bold shadow-neon">
                    <span class="material-symbols-outlined">add</span>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-8 pb-32">
            <div class="flex flex-col gap-8 max-w-[1400px] mx-auto">
                <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div onclick="openCreatePanel()" class="bg-gradient-to-br from-primary/10 to-[#121417] border border-primary/30 rounded-xl p-6 flex flex-col justify-center items-start gap-3 hover:border-primary/60 transition-all cursor-pointer group h-full min-h-[160px]">
                        <div class="size-12 rounded-full bg-primary flex items-center justify-center text-background-dark shadow-neon group-hover:scale-110 transition-transform duration-300">
                            <span class="material-symbols-outlined">add</span>
                        </div>
                        <div>
                            <h3 class="text-white text-lg font-bold">Create Challenge</h3>
                            <p class="text-[#90cba8] text-sm">Design a new financial quest</p>
                        </div>
                    </div>

                    <div class="bg-[#121417] border border-[#224932] rounded-xl p-6 flex flex-col justify-between h-full min-h-[160px]">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-white/5 rounded-lg text-primary">
                                <span class="material-symbols-outlined">bolt</span>
                            </div>
                            <span class="bg-[#224932]/30 px-2 py-1 rounded-md text-[#25f478] text-xs font-bold border border-[#25f478]/20">+2% this week</span>
                        </div>
                        <div>
                            <p class="text-[#90cba8] text-sm font-medium mb-1">Active Challenges</p>
                            <p class="text-white text-3xl font-bold tracking-tight">{{ $stats['active_count'] }}</p>
                        </div>
                    </div>

                    <div class="bg-[#121417] border border-[#224932] rounded-xl p-6 flex flex-col justify-between h-full min-h-[160px]">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-white/5 rounded-lg text-primary">
                                <span class="material-symbols-outlined">stars</span>
                            </div>
                            <span class="bg-[#224932]/30 px-2 py-1 rounded-md text-[#25f478] text-xs font-bold border border-[#25f478]/20">+15% vs last mo</span>
                        </div>
                        <div>
                            <p class="text-[#90cba8] text-sm font-medium mb-1">Total XP Distributed</p>
                            <p class="text-white text-3xl font-bold tracking-tight">{{ number_format($stats['total_xp'] / 1000, 0) }}k</p>
                        </div>
                    </div>

                    <div class="bg-[#121417] border border-[#224932] rounded-xl p-6 flex flex-col justify-between h-full min-h-[160px]">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-white/5 rounded-lg text-orange-400">
                                <span class="material-symbols-outlined">pending_actions</span>
                            </div>
                            <span class="bg-[#224932]/30 px-2 py-1 rounded-md text-orange-400 text-xs font-bold border border-orange-400/20">Needs Attention</span>
                        </div>
                        <div>
                            <p class="text-[#90cba8] text-sm font-medium mb-1">Pending Review</p>
                            <p class="text-white text-3xl font-bold tracking-tight">{{ $stats['pending_review'] }}</p>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-[#121417] p-3 rounded-2xl border border-[#224932]">
                    <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto no-scrollbar">
                        <button onclick="filterChallenges('all')" class="filter-btn px-5 py-2.5 rounded-full bg-[#1A1D21] text-white text-sm font-bold whitespace-nowrap border border-primary/40 shadow-[0_0_10px_rgba(37,244,120,0.1)]" data-filter="all">All</button>
                        <button onclick="filterChallenges('active')" class="filter-btn px-5 py-2.5 rounded-full hover:bg-[#1A1D21] text-[#90cba8] text-sm font-medium whitespace-nowrap transition-colors border border-transparent hover:border-[#224932]" data-filter="active">Active</button>
                        <button onclick="filterChallenges('inactive')" class="filter-btn px-5 py-2.5 rounded-full hover:bg-[#1A1D21] text-[#90cba8] text-sm font-medium whitespace-nowrap transition-colors border border-transparent hover:border-[#224932]" data-filter="inactive">Inactive</button>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button onclick="sortChallenges('xp_reward')" class="flex items-center gap-2 px-4 py-2 bg-[#0D0F10] rounded-full border border-[#224932] hover:border-primary/30 transition-colors">
                            <span class="material-symbols-outlined text-[#90cba8] text-[20px]">sort</span>
                            <span class="text-white text-sm font-medium">Sort by XP</span>
                        </button>
                    </div>
                </div>

                <div id="challengesGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($challenges as $challenge)
                        <div class="challenge-card bg-[#121417] rounded-2xl p-5 border border-[#224932] hover:border-primary/50 transition-all group relative overflow-hidden {{ !$challenge->is_active ? 'opacity-75 hover:opacity-100' : '' }}"
                             data-status="{{ $challenge->is_active ? 'active' : 'inactive' }}"
                             data-frequency="{{ $challenge->frequency }}"
                             data-challenge-id="{{ $challenge->id }}">
                            @if(!$challenge->is_active)
                                <div class="absolute top-0 right-0 w-32 h-32 bg-slate-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                            @else
                                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                            @endif

                            <div class="flex justify-between items-start mb-4 relative z-10">
                                <div class="flex gap-3">
                                    <div class="size-12 rounded-xl bg-[#0D0F10] border border-[#224932] flex items-center justify-center text-2xl">
                                        {{ $challenge->icon ?? '🎯' }}
                                    </div>
                                    <div>
                                        <h4 class="text-white font-bold text-lg leading-tight group-hover:text-primary transition-colors">{{ $challenge->name }}</h4>
                                        <div class="flex gap-2 mt-1">
                                            <span class="text-xs font-semibold text-{{ $challenge->difficulty_color }}-400 bg-{{ $challenge->difficulty_color }}-400/10 px-2 py-0.5 rounded-md inline-block border border-{{ $challenge->difficulty_color }}-400/20">{{ $challenge->difficulty }}</span>
                                            <span class="text-xs font-semibold text-{{ $challenge->frequency_color }}-400 bg-{{ $challenge->frequency_color }}-400/10 px-2 py-0.5 rounded-md inline-flex items-center gap-1 border border-{{ $challenge->frequency_color }}-400/20">
                                                <span class="material-symbols-outlined text-[14px]">{{ $challenge->frequency_icon }}</span>
                                                {{ $challenge->frequency }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-bold uppercase text-[#90cba8] tracking-wider mb-1">Reward</span>
                                    <div class="flex items-center gap-1 {{ $challenge->is_active ? 'text-primary' : 'text-white/50' }} font-black text-xl">
                                        <span class="material-symbols-outlined text-sm pt-0.5">bolt</span>{{ $challenge->xp_reward }}
                                    </div>
                                </div>
                            </div>

                            <p class="text-[#90cba8] text-sm mb-4 line-clamp-2">{{ $challenge->description }}</p>

                            <div class="flex items-center gap-2 mb-5">
                                @if($challenge->target_value)
                                    <span class="text-xs text-white/60 bg-white/5 border border-white/5 px-2 py-1 rounded">{{ $challenge->target_value }}</span>
                                @endif
                                @if($challenge->duration)
                                    <span class="text-xs text-white/60 bg-white/5 border border-white/5 px-2 py-1 rounded">{{ $challenge->duration }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-[#224932]/50">
                                <div class="flex items-center gap-2">
                                    @if($challenge->is_active)
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                        </span>
                                        <span class="text-sm text-white font-medium">Active</span>
                                    @else
                                        <span class="h-3 w-3 rounded-full bg-slate-500"></span>
                                        <span class="text-sm text-slate-400 font-medium">Inactive</span>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="editChallenge({{ $challenge->id }})" class="p-2 hover:bg-white/10 rounded-full text-white transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button
                                        onclick="event.stopPropagation(); toggleChallengeStatus({{ $challenge->id }})"
                                        class="p-2 rounded-full transition-colors text-white {{ $challenge->is_active ? 'hover:bg-red-500/20 hover:text-red-400' : 'hover:bg-emerald-500/20 hover:text-emerald-400' }}"
                                        title="{{ $challenge->is_active ? 'Disable' : 'Enable' }}"
                                    >
                                        <span class="material-symbols-outlined text-[20px]">
                                            {{ $challenge->is_active ? 'power_settings_new' : 'play_arrow' }}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="h-24"></div>
        </div>

        <!-- Create/Edit Panel -->
        <x-ChallenegeEditCreate/>
    </main>
</div>

<script src="{{asset('../JS/Challenges.js')}}"></script>
</body>
</html>
