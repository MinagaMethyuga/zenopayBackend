<aside class="hidden lg:flex w-[280px] flex-col border-r border-[#224932]/50 bg-background-dark h-full shrink-0">
    <div class="flex h-full flex-col justify-between p-6">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-1 px-2">
                <div class="flex items-center gap-2 mb-2">
                    <div class="size-8 rounded-full bg-primary flex items-center justify-center text-background-dark font-bold text-xl">Z</div>
                    <h1 class="text-white text-xl font-bold tracking-tight">Zenopay<span class="text-primary">Admin</span></h1>
                </div>
                <p class="text-[#90cba8] text-xs font-normal tracking-wide uppercase px-1">Gamified Finance Sri Lanka</p>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="flex items-center gap-4 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('Dashboard') ? 'bg-[#121417] border border-[#224932] text-white shadow-[inset_4px_0_0_0_#25f478]' : 'text-white/70 hover:text-white hover:bg-white/5' }}" href="{{ route('Dashboard') }}">
                    <span class="material-symbols-outlined text-[24px] {{ request()->routeIs('Dashboard') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors">dashboard</span>
                    <span class="text-sm font-medium">{{ request()->routeIs('Dashboard') ? 'font-bold' : '' }}Dashboard</span>
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('UserManagement') ? 'bg-[#121417] border border-[#224932] text-white shadow-[inset_4px_0_0_0_#25f478]' : 'text-white/70 hover:text-white hover:bg-white/5' }}" href="{{ route('UserManagement') }}">
                    <span class="material-symbols-outlined text-[24px] {{ request()->routeIs('UserManagement') ? 'text-primary' : 'group-hover:text-primary' }} transition-colors">group</span>
                    <span class="text-sm {{ request()->routeIs('UserManagement') ? 'font-bold' : 'font-medium' }}">Users</span>
                </a>
                <a class="flex items-center gap-4 px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs('challenges.*') ? 'bg-[#121417] border border-[#224932] text-white shadow-[inset_4px_0_0_0_#25f478]' : 'text-white/70 hover:text-white hover:bg-white/5' }}" href="{{ route('challenges.index') }}">
                    <span class="material-symbols-outlined text-[24px] {{ request()->routeIs('challenges.*') ? 'text-primary fill-1' : 'group-hover:text-primary' }} transition-colors">emoji_events</span>
                    <span class="text-sm {{ request()->routeIs('challenges.*') ? 'font-bold' : 'font-medium' }}">Challenges</span>
                </a>
                <a class="flex items-center gap-4 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors group" href="#">
                    <span class="material-symbols-outlined text-[24px] group-hover:text-primary transition-colors">account_balance_wallet</span>
                    <span class="text-sm font-medium">Transactions</span>
                </a>
                <a class="flex items-center gap-4 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors group" href="#">
                    <span class="material-symbols-outlined text-[24px] group-hover:text-primary transition-colors">settings</span>
                    <span class="text-sm font-medium">Settings</span>
                </a>
            </nav>
        </div>
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-[#121417] to-transparent border border-[#224932]/50">
            <div class="size-10 rounded-full bg-center bg-cover border-2 border-primary" data-alt="Admin user avatar" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuB8chnG-wlU6jWw2g6aV8UaU2yX0o68urYzDLlgLfRqI2eTU_4N3Nl68JbG96XCAopXECdL0SdiEww6w9qGSLZbJ4gj1tkg4TANTJC-f0ryZWX6j_MEENm35Z5WWfEDUx1SNaSjIVMfBSncPRcJPQFimj7_57zduFUa1KoSVmINlKG85266URGqKl9fLXrgSlDJGWgFVsFcE2ku2mIB0ywS8CR2bt56bkeB68jgUwNykcys6QVWRSuPYAgUGKOa6NPKMvIYOmUxMJw')"></div>
            <div class="flex flex-col">
                <p class="text-white text-sm font-bold">Admin User</p>
                <p class="text-[#90cba8] text-xs">Super Admin</p>
            </div>
        </div>
    </div>
</aside>
