<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Batch Training — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-violet-400 rounded-full"></span>manage_batches
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Batch Training</h1>
        <p class="text-[13px] text-white/70 mb-6">Angkatan pelatihan berdasarkan template. Generate jadwal otomatis setelah membuat batch.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $batches->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Total Batch</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $batches->where('status','generated')->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Terjadwal</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $batches->where('status','draft')->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Draft</div>
            </div>
        </div>
    </div>
</section>

<main class="max-w-[1200px] mx-auto px-6 pt-7 pb-16 max-[640px]:px-4">
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-green-800">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-slate-900">Semua Batch</h2>
        <a href="{{ route('admin.batches.create') }}"
           class="inline-flex items-center gap-1.5 h-9 px-4 bg-sky-700 text-white rounded-[9px] text-[13px] font-semibold no-underline hover:bg-sky-600 transition-colors">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Batch Baru
        </a>
    </div>

    @if($batches->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 opacity-40">
            <rect x="2" y="3" width="6" height="6" rx="1"/><rect x="9" y="3" width="6" height="6" rx="1"/><rect x="16" y="3" width="6" height="6" rx="1"/>
        </svg>
        <p class="text-sm">Belum ada batch. <a href="{{ route('admin.batches.create') }}" class="text-sky-700 font-semibold">Buat batch pertama</a>.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-3">
        @foreach($batches as $batch)
        <div class="bg-white border border-slate-200 rounded-xl p-4 flex items-center gap-4 hover:shadow-sm hover:border-slate-300 transition-all max-[640px]:flex-wrap">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <span class="font-semibold text-slate-900">{{ $batch->name }}</span>
                    <span class="font-mono text-[11px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">Angkatan {{ $batch->batch_number }} / {{ $batch->year }}</span>
                    <span class="inline-flex items-center gap-1 h-5 px-2 rounded-full text-[10px] font-semibold
                        {{ $batch->status === 'generated' ? 'bg-green-100 text-green-700' : ($batch->status === 'finished' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">
                        {{ ['draft'=>'Draft','generated'=>'Terjadwal','finished'=>'Selesai'][$batch->status] ?? $batch->status }}
                    </span>
                </div>
                <div class="flex gap-3 text-[12px] text-slate-500 flex-wrap">
                    <span>{{ $batch->template->name }}</span>
                    @if($batch->organizationalUnit)<span>• {{ $batch->organizationalUnit->name }}</span>@endif
                    <span>• Mulai: {{ $batch->start_date->format('d M Y') }}</span>
                    @if($batch->end_date)<span>• Selesai: {{ $batch->end_date->format('d M Y') }}</span>@endif
                    <span>• {{ $batch->schedules_count }} jadwal</span>
                </div>
            </div>
            <div class="flex gap-2 shrink-0">
                @if($batch->status === 'draft')
                <form method="POST" action="{{ route('admin.batches.generate', $batch) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 h-8 px-3 bg-amber-500 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-amber-400 transition-colors">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Generate
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.batches.show', $batch) }}"
                   class="inline-flex items-center gap-1 h-8 px-3 bg-sky-50 text-sky-700 border border-sky-200 rounded-lg text-[12px] font-semibold no-underline hover:bg-sky-100 transition-colors">
                    Detail →
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</main>
</body>
</html>
