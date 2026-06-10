<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monitor Konflik — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-red-400 rounded-full animate-pulse"></span>monitor_conflicts
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Monitor Konflik</h1>
        <p class="text-[13px] text-white/70 mb-5">Deteksi tumpang-tindih jadwal secara real-time. Seminar konflik global, WI konflik per individu.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3
                @if(count($seminarConflicts) > 0) ring-2 ring-red-400/40 @endif">
                <div class="font-mono text-xl font-semibold {{ count($seminarConflicts) > 0 ? 'text-red-300' : 'text-white' }}">
                    {{ count($seminarConflicts) }}
                </div>
                <div class="text-xs text-white/60 mt-0.5">Konflik Seminar</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3
                @if(count($wiConflicts) > 0) ring-2 ring-orange-400/40 @endif">
                <div class="font-mono text-xl font-semibold {{ count($wiConflicts) > 0 ? 'text-orange-300' : 'text-white' }}">
                    {{ count($wiConflicts) }}
                </div>
                <div class="text-xs text-white/60 mt-0.5">Konflik WI</div>
            </div>
        </div>
    </div>
</section>

<main class="max-w-[1200px] mx-auto px-6 pt-7 pb-16 max-[640px]:px-4">

    @if(count($seminarConflicts) === 0 && count($wiConflicts) === 0)
    <div class="text-center py-20">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-green-600">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900 mb-1">Tidak ada konflik</h2>
        <p class="text-sm text-slate-500">Semua jadwal berjalan tanpa tumpang-tindih.</p>
    </div>
    @else

    <!-- SEMINAR CONFLICTS -->
    @if(count($seminarConflicts) > 0)
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
            <h2 class="text-base font-bold text-slate-900">Konflik Seminar ({{ count($seminarConflicts) }})</h2>
        </div>
        <p class="text-[12px] text-slate-500 mb-4">Seminar dari batch berbeda yang overlap tanggal — konflik berlaku global lintas unit.</p>
        <div class="flex flex-col gap-3">
            @foreach($seminarConflicts as $conflict)
            @php $a = $conflict['a']; $b = $conflict['b']; @endphp
            <div class="bg-white border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-red-600">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="text-[12px] font-semibold text-red-800 mb-2">Konflik Seminar (conflict_group=seminar)</div>
                        <div class="grid grid-cols-2 gap-3 max-[640px]:grid-cols-1">
                            @foreach([$a, $b] as $s)
                            <div class="bg-red-50 border border-red-100 rounded-lg px-3 py-2.5">
                                <div class="font-semibold text-[12px] text-slate-900">{{ $s->name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $s->batch->name ?? '—' }}</div>
                                <div class="font-mono text-[11px] text-red-600 mt-1">
                                    {{ $s->start_date->format('d M') }} — {{ $s->end_date->format('d M Y') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- WI CONFLICTS -->
    @if(count($wiConflicts) > 0)
    <div>
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
            <h2 class="text-base font-bold text-slate-900">Konflik Widyaiswara ({{ count($wiConflicts) }})</h2>
        </div>
        <p class="text-[12px] text-slate-500 mb-4">WI yang di-assign ke dua jadwal berbeda dengan tanggal overlap.</p>
        <div class="flex flex-col gap-3">
            @foreach($wiConflicts as $conflict)
            @php $a = $conflict['a']; $b = $conflict['b']; @endphp
            <div class="bg-white border border-orange-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-orange-600">
                                <circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="font-semibold text-[13px] text-orange-800">{{ $a->widyaiswara->name }}</span>
                            <span class="font-mono text-[11px] text-slate-400">{{ $a->widyaiswara->nip }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 max-[640px]:grid-cols-1">
                            @foreach([$a, $b] as $assign)
                            <div class="bg-orange-50 border border-orange-100 rounded-lg px-3 py-2.5">
                                <div class="font-semibold text-[12px] text-slate-900">{{ $assign->schedule->name ?? '—' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $assign->schedule->batch->name ?? '—' }}</div>
                                <div class="font-mono text-[11px] text-orange-600 mt-1">
                                    {{ \Carbon\Carbon::parse($assign->start_date)->format('d M') }}
                                    — {{ \Carbon\Carbon::parse($assign->end_date)->format('d M Y') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</main>
</body>
</html>
