<!DOCTYPE html>
<html class="dark" lang="en"><head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Zenopay Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Spline+Sans:wght@300;400;500;600;700&amp;family=Noto+Sans:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#30e87a",
                        "background-light": "#f6f8f7",
                        "background-dark": "#0D0F10", // Deep black requested
                        "card-dark": "#121417", // Slightly lighter for cards
                        "border-dark": "#27272a", // Zinc 800 for subtle borders
                        "hover-dark": "#1c1f26",
                    },
                    fontFamily: {
                        "display": ["Spline Sans", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "1rem",
                        "lg": "1.5rem",
                        "xl": "2rem",
                        "full": "9999px"
                    },
                    boxShadow: {
                        'neon': '0 0 10px rgba(48, 232, 122, 0.3)',
                        'neon-hover': '0 0 20px rgba(48, 232, 122, 0.5)',
                        'card-glow': '0 0 20px rgba(0,0,0,0.4)',
                    }
                },
            },
        }
    </script>
    <style>::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0D0F10;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #30e87a;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-white antialiased overflow-hidden">
<div class="flex h-screen w-full overflow-hidden">
    <x-dashboardNavBar/>
    <main class="flex-1 flex flex-col h-full relative overflow-y-auto bg-[#0D0F10]">
        <header class="sticky top-0 z-10 bg-[#0D0F10]/80 backdrop-blur-md border-b border-border-dark px-6 py-4 md:px-10">
            <div class="max-w-7xl mx-auto w-full flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-col">
                    <h2 class="text-white text-3xl font-bold tracking-tight">Dashboard Overview</h2>
                    <p class="text-gray-400 text-sm mt-1">Welcome back, Admin. Here's what's happening today.</p>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="relative w-full md:w-80 group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-500 group-focus-within:text-primary transition-colors">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input class="block w-full p-3 pl-10 text-sm text-white bg-[#121417] border border-border-dark rounded-full focus:ring-1 focus:ring-primary focus:border-primary placeholder-gray-600 transition-all" placeholder="Search users, challenges, data..." type="text"/>
                    </div>
                    <button class="relative p-3 rounded-full bg-[#121417] border border-border-dark text-gray-400 hover:text-white hover:border-primary/50 transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-3 flex h-2 w-2">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
</span>
                    </button>
                </div>
            </div>
        </header>
        <div class="flex-1 px-6 py-8 md:px-10 max-w-7xl mx-auto w-full flex flex-col gap-8">
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-primary">
                                <span class="material-symbols-outlined text-[20px]">group</span>
                            </div>
                            <span class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">+5%</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Total Users</p>
                            <h3 class="text-white text-2xl font-bold mt-1">12,450</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-blue-400">
                                <span class="material-symbols-outlined text-[20px]">trending_up</span>
                            </div>
                            <span class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">+12%</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Active (Week)</p>
                            <h3 class="text-white text-2xl font-bold mt-1">8,200</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 rounded-full blur-2xl group-hover:bg-purple-500/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-purple-400">
                                <span class="material-symbols-outlined text-[20px]">flag</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Challenges</p>
                            <h3 class="text-white text-2xl font-bold mt-1">45</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl group-hover:bg-orange-500/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-orange-400">
                                <span class="material-symbols-outlined text-[20px] fill-current">check_circle</span>
                            </div>
                            <span class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">+8%</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Completed</p>
                            <h3 class="text-white text-2xl font-bold mt-1">128K</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-500/5 rounded-full blur-2xl group-hover:bg-yellow-500/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-yellow-400">
                                <span class="material-symbols-outlined text-[20px]">bolt</span>
                            </div>
                            <span class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">+2%</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Streak Rate</p>
                            <h3 class="text-white text-2xl font-bold mt-1">78%</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-card-dark rounded-xl p-5 border border-border-dark hover:border-primary/50 transition-colors group relative overflow-hidden shadow-card-glow">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-pink-500/5 rounded-full blur-2xl group-hover:bg-pink-500/10 transition-all"></div>
                    <div class="flex flex-col gap-3 relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="p-2 bg-[#1c1f26] rounded-lg text-pink-400">
                                <span class="material-symbols-outlined text-[20px]">hotel_class</span>
                            </div>
                            <span class="text-primary text-xs font-bold bg-primary/10 px-2 py-1 rounded-full">+15%</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">System XP</p>
                            <h3 class="text-white text-2xl font-bold mt-1">4.5M</h3>
                        </div>
                    </div>
                </div>
            </section>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 flex-1 min-h-[400px]">
                <section class="lg:col-span-2 bg-card-dark rounded-xl border border-border-dark p-6 flex flex-col shadow-card-glow">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-white text-lg font-bold">Weekly Active Users</h3>
                            <p class="text-gray-400 text-sm">User engagement over the last 7 days</p>
                        </div>
                        <div class="flex gap-2">
                            <button class="px-3 py-1 rounded-full bg-[#1c1f26] text-xs text-white font-medium hover:bg-primary hover:text-black transition-colors border border-border-dark">Week</button>
                            <button class="px-3 py-1 rounded-full bg-transparent text-xs text-gray-500 font-medium hover:text-white transition-colors">Month</button>
                        </div>
                    </div>
                    <div class="relative flex-1 w-full min-h-[250px]">
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 800 300">
                            <defs>
                                <linearGradient id="gradient" x1="0%" x2="0%" y1="0%" y2="100%">
                                    <stop offset="0%" style="stop-color:#30e87a;stop-opacity:0.2"></stop>
                                    <stop offset="100%" style="stop-color:#30e87a;stop-opacity:0"></stop>
                                </linearGradient>
                            </defs>
                            <line stroke="#27272a" stroke-width="1" x1="0" x2="800" y1="250" y2="250"></line>
                            <line stroke="#27272a" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="190" y2="190"></line>
                            <line stroke="#27272a" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="130" y2="130"></line>
                            <line stroke="#27272a" stroke-dasharray="4" stroke-width="1" x1="0" x2="800" y1="70" y2="70"></line>
                            <path d="M0,250
                                         C100,220 150,230 200,180
                                         C250,130 300,160 400,120
                                         C500,80 550,100 650,50
                                         C700,20 750,40 800,30
                                         V250 Z" fill="url(#gradient)"></path>
                            <path d="M0,250
                                         C100,220 150,230 200,180
                                         C250,130 300,160 400,120
                                         C500,80 550,100 650,50
                                         C700,20 750,40 800,30" fill="none" stroke="#30e87a" stroke-linecap="round" stroke-width="3"></path>
                            <circle cx="200" cy="180" fill="#121417" r="4" stroke="#30e87a" stroke-width="2"></circle>
                            <circle cx="400" cy="120" fill="#121417" r="4" stroke="#30e87a" stroke-width="2"></circle>
                            <circle cx="650" cy="50" fill="#121417" r="4" stroke="#30e87a" stroke-width="2"></circle>
                            <circle cx="800" cy="30" fill="#30e87a" r="6"></circle>
                        </svg>
                    </div>
                    <div class="flex justify-between px-2 mt-4 text-xs text-gray-600 font-medium">
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                        <span>Sun</span>
                    </div>
                </section>
                <section class="lg:col-span-1 bg-card-dark rounded-xl border border-border-dark p-6 flex flex-col shadow-card-glow">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-white text-lg font-bold">Recent Activity</h3>
                        <a class="text-primary text-sm hover:underline" href="#">View All</a>
                    </div>
                    <div class="flex flex-col gap-4 overflow-y-auto max-h-[300px] pr-2 custom-scrollbar">
                        <div class="flex gap-3 items-start p-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer">
                            <div class="rounded-full bg-blue-500/10 p-2 text-blue-400 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">school</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300"><span class="font-bold text-white">Kasun P.</span> completed "Savings 101"</p>
                                <p class="text-xs text-gray-600 mt-1">2 mins ago</p>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start p-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer">
                            <div class="rounded-full bg-primary/10 p-2 text-primary shrink-0">
                                <span class="material-symbols-outlined text-[18px]">military_tech</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300"><span class="font-bold text-white">Amara D.</span> earned "Budget Master"</p>
                                <p class="text-xs text-gray-600 mt-1">15 mins ago</p>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start p-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer">
                            <div class="rounded-full bg-orange-500/10 p-2 text-orange-400 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">payments</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300"><span class="font-bold text-white">New User</span> subscribed to Premium</p>
                                <p class="text-xs text-gray-600 mt-1">1 hour ago</p>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start p-3 rounded-lg hover:bg-white/5 transition-colors cursor-pointer">
                            <div class="rounded-full bg-purple-500/10 p-2 text-purple-400 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">emoji_events</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-300"><span class="font-bold text-white">Class 5B</span> won the weekly challenge</p>
                                <p class="text-xs text-gray-600 mt-1">3 hours ago</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-r from-card-dark to-[#0D0F10] border border-border-dark rounded-xl p-6 relative overflow-hidden shadow-card-glow">
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="bg-[#1c1f26] p-3 rounded-full text-white border border-border-dark">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm font-medium">Total Rewards Distributed</p>
                            <h4 class="text-white text-2xl font-bold">LKR 450,200</h4>
                        </div>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none">
                        <span class="material-symbols-outlined text-[120px] text-primary">savings</span>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-card-dark to-[#0D0F10] border border-border-dark rounded-xl p-6 relative overflow-hidden shadow-card-glow">
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="bg-[#1c1f26] p-3 rounded-full text-white border border-border-dark">
                            <span class="material-symbols-outlined">verified_user</span>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm font-medium">KYC Verification Pending</p>
                            <h4 class="text-white text-2xl font-bold">24 Users</h4>
                        </div>
                        <button class="ml-auto bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-full text-sm font-medium transition-colors border border-primary/20">Review</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body></html>
