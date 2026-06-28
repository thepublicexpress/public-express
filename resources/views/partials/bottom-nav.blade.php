<nav class="fixed bottom-0 left-0 right-0 z-50 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden">
    <div class="mx-auto flex max-w-7xl items-center justify-around px-2 py-2 text-[11px] font-semibold text-slate-600">
        <a href="/" class="flex flex-col items-center gap-1 {{ request()->is('/') ? 'text-brand' : '' }}">
            <i class="fas fa-home"></i>
            <span>होम</span>
        </a>
        <a href="/leaderboard" class="flex flex-col items-center gap-1 {{ request()->is('leaderboard') ? 'text-brand' : '' }}">
            <i class="fas fa-trophy"></i>
            <span>लीडर</span>
        </a>
        <a href="/report-problem" class="flex flex-col items-center gap-1 {{ request()->is('report-problem') ? 'text-brand' : '' }}">
            <i class="fas fa-exclamation-circle"></i>
            <span>रिपोर्ट</span>
        </a>
        <a href="/reporter/login" class="flex flex-col items-center gap-1">
            <i class="fas fa-pen-nib"></i>
            <span>रिपोर्टर</span>
        </a>
    </div>
</nav>
