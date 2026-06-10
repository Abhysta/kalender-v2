<nav class="sticky top-0 z-50 bg-white/[0.92] backdrop-blur-md border-b border-slate-200 px-6 h-16 flex items-center gap-4 max-[640px]:px-4 max-[640px]:gap-2" id="topnav">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 no-underline shrink-0">
        <div class="w-9 h-9 bg-sky-700 rounded-lg flex items-center justify-center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div class="max-[480px]:hidden">
            <div class="font-mono text-[15px] font-semibold text-slate-900 tracking-[-0.3px]">BPSDM Kalender</div>
            <div class="text-[11px] text-slate-500">Sistem Pelatihan ASN</div>
        </div>
    </a>

    @php
        $role = auth()->user()->getRoleNames()->first();
        $roleLabel = match($role) {
            'super_admin'  => 'Super Admin',
            'unit_manager' => 'Unit Manager',
            default        => 'Admin',
        };
        $roleClass = match($role) {
            'super_admin'  => 'bg-amber-100 text-amber-800 border-amber-200',
            'unit_manager' => 'bg-purple-100 text-purple-800 border-purple-200',
            default        => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    @endphp
    <span class="inline-flex items-center gap-1.5 {{ $roleClass }} border rounded-full px-2.5 py-[3px] text-[11px] font-bold shrink-0 max-[480px]:hidden">
        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        {{ $roleLabel }}
    </span>

    <nav class="flex items-center gap-0.5 flex-wrap" aria-label="Menu admin">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="max-[640px]:hidden">Dashboard</span>
        </a>
        <a href="{{ route('admin.templates.index') }}" class="nav-link {{ request()->routeIs('admin.templates*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span class="max-[768px]:hidden">Template</span>
        </a>
        <a href="{{ route('admin.batches.index') }}" class="nav-link {{ request()->routeIs('admin.batches*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="6" height="6" rx="1"/><rect x="9" y="3" width="6" height="6" rx="1"/><rect x="16" y="3" width="6" height="6" rx="1"/><rect x="2" y="10" width="6" height="6" rx="1"/><rect x="9" y="10" width="6" height="6" rx="1"/></svg>
            <span class="max-[768px]:hidden">Batch</span>
        </a>
        <a href="{{ route('admin.widyaiswaras.index') }}" class="nav-link {{ request()->routeIs('admin.widyaiswaras*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <span class="max-[900px]:hidden">Widyaiswara</span>
        </a>
        <a href="{{ route('admin.holidays.index') }}" class="nav-link {{ request()->routeIs('admin.holidays*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span class="max-[900px]:hidden">Hari Libur</span>
        </a>
        <a href="{{ route('admin.conflicts.index') }}" class="nav-link {{ request()->routeIs('admin.conflicts*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            <span class="max-[900px]:hidden">Konflik</span>
        </a>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span class="max-[900px]:hidden">Pengguna</span>
        </a>
        @endif
    </nav>

    <div class="flex items-center gap-2 ml-auto shrink-0">
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 h-[32px] px-3 bg-red-50 text-red-800 border border-red-200 rounded-lg text-[12px] font-semibold font-sans cursor-pointer transition-all duration-150 hover:bg-red-100">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span class="max-[640px]:hidden">Logout</span>
            </button>
        </form>
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-700 to-cyan-500 flex items-center justify-center text-white text-[12px] font-semibold border-2 border-slate-200 hover:border-sky-700 transition-all cursor-default"
             title="{{ Auth::user()->name }}">
            {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
        </div>
    </div>
</nav>
