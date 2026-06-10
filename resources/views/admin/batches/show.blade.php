<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $batch->name }} — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
    <style>
        .schedule-row { transition: background 150ms; }
        .schedule-row:hover { background: #f8fafc; }
        .schedule-row.is-alert { border-left: 3px solid #F59E0B; }
        .wi-chip { display:inline-flex; align-items:center; gap:4px; height:20px; padding:0 7px; background:#e0f2fe; color:#0369a1; border-radius:100px; font-size:10px; font-weight:600; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center gap-1.5 text-[12px] text-white/60 hover:text-white/90 no-underline mb-4">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            Batch
        </a>
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-[clamp(18px,3vw,28px)] font-bold text-white mb-1">{{ $batch->name }}</h1>
                <div class="flex gap-2 items-center flex-wrap">
                    <span class="font-mono text-[12px] text-white/60">Angkatan {{ $batch->batch_number }} / {{ $batch->year }}</span>
                    @if($batch->organizationalUnit)<span class="text-[12px] text-white/60">• {{ $batch->organizationalUnit->name }}</span>@endif
                    <span class="inline-flex items-center gap-1 h-5 px-2.5 rounded-full text-[10px] font-semibold
                        {{ $batch->status === 'generated' ? 'bg-green-500/20 text-green-300' : ($batch->status === 'finished' ? 'bg-slate-500/20 text-slate-300' : 'bg-amber-500/20 text-amber-300') }}">
                        {{ ['draft'=>'Draft','generated'=>'Terjadwal','finished'=>'Selesai'][$batch->status] ?? $batch->status }}
                    </span>
                </div>
                <p class="text-[12px] text-white/60 mt-1.5">Template: {{ $batch->template->name }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if($batch->status === 'draft')
                <form method="POST" action="{{ route('admin.batches.generate', $batch) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-amber-500 text-white rounded-lg text-[13px] font-semibold border-none cursor-pointer hover:bg-amber-400">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Generate Jadwal
                    </button>
                </form>
                @elseif($batch->status === 'generated')
                <form method="POST" action="{{ route('admin.batches.generate', $batch) }}" onsubmit="return confirm('Re-generate akan menghapus jadwal yang belum di-lock. Lanjutkan?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-white/10 border border-white/20 text-white rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-white/20">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Re-generate
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}" onsubmit="return confirm('Hapus batch ini beserta semua jadwalnya?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-red-500/20 border border-red-400/30 text-red-300 rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-red-500/30">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        <div class="flex gap-4 mt-5 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $batch->schedules->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Jadwal</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $batch->participant_count }}</div>
                <div class="text-xs text-white/60 mt-0.5">Peserta</div>
            </div>
            @if($batch->start_date)
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-sm font-semibold text-white">{{ $batch->start_date->format('d M Y') }}</div>
                <div class="text-xs text-white/60 mt-0.5">Mulai</div>
            </div>
            @endif
            @if($batch->end_date)
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-sm font-semibold text-white">{{ $batch->end_date->format('d M Y') }}</div>
                <div class="text-xs text-white/60 mt-0.5">Selesai</div>
            </div>
            @endif
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
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">{{ $errors->first() }}</div>
    @endif

    @if($batch->has_conflict_alert)
    <div id="conflictAlertBanner" class="flex items-start gap-3 bg-amber-50 border border-amber-300 rounded-xl px-5 py-4 mb-5">
        <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <div class="font-bold text-amber-800 text-[13px] mb-1">Konflik Jadwal Terdeteksi</div>
            <p class="text-[12px] text-amber-700 leading-relaxed">
                Batch ini memiliki konflik jadwal yang belum diselesaikan.
                Tinjau jadwal di bawah, lakukan penyesuaian manual hingga konflik teratasi, lalu klik Tandai Selesai.
            </p>
        </div>
        <form method="POST" action="{{ route('admin.batches.dismiss-conflict', $batch) }}" class="flex-shrink-0">
            @csrf
            <button type="submit"
                class="h-7 px-3 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-[11px] font-semibold border border-amber-300 cursor-pointer transition-colors whitespace-nowrap">
                Tandai Selesai
            </button>
        </form>
    </div>
    @endif

    @if($batch->schedules->isEmpty())
    <div class="text-center py-16 text-slate-400">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 opacity-40">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
        <p class="text-sm mb-3">Jadwal belum di-generate.</p>
        @if($batch->status === 'draft')
        <form method="POST" action="{{ route('admin.batches.generate', $batch) }}" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-5 bg-amber-500 text-white rounded-lg text-[13px] font-semibold border-none cursor-pointer hover:bg-amber-400">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Generate Sekarang
            </button>
        </form>
        @endif
    </div>
    @else
    <!-- LOAD WI OPTIONS FOR ASSIGN -->
    @php $allWi = \App\Models\Widyaiswara::where('is_active', true)->orderBy('name')->get(); @endphp

    <h2 class="text-base font-bold text-slate-900 mb-4">Jadwal Pelatihan</h2>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3 w-6">#</th>
                    <th class="text-left px-4 py-3" style="width:4px"><div class="w-1"></div></th>
                    <th class="text-left px-4 py-3">Aktivitas</th>
                    <th class="text-left px-4 py-3 max-[768px]:hidden">Tanggal</th>
                    <th class="text-center px-4 py-3 max-[640px]:hidden">Durasi</th>
                    <th class="text-left px-4 py-3">Widyaiswara</th>
                    <th class="px-4 py-3 max-[768px]:hidden"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($batch->schedules as $schedule)
                <tr class="schedule-row {{ $schedule->is_alert ? 'is-alert' : '' }}">
                    <td class="px-4 py-3 font-mono text-[11px] text-slate-400">{{ str_pad($schedule->sequence, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="py-3 pl-2">
                        <div class="w-2.5 h-2.5 rounded-full" style="background:{{ $schedule->color }}"></div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-[13px] text-slate-900">{{ $schedule->name }}</span>
                            @if($schedule->is_alert)
                            <span class="inline-flex items-center gap-1 h-4 px-1.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold">ALERT</span>
                            @endif
                            @if($schedule->is_manual)
                            <span class="inline-flex items-center gap-1 h-4 px-1.5 bg-violet-100 text-violet-700 rounded text-[9px] font-bold">MANUAL</span>
                            @endif
                            @if($schedule->conflict_group)
                            <span class="inline-flex items-center gap-1 h-4 px-1.5 bg-slate-100 text-slate-500 rounded text-[9px] font-mono">{{ $schedule->conflict_group }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 max-[768px]:hidden">
                        <div class="text-[12px] text-slate-700 font-mono">
                            {{ $schedule->start_date->format('d M') }}
                            @if($schedule->start_date->ne($schedule->end_date))
                            — {{ $schedule->end_date->format('d M Y') }}
                            @else
                            {{ $schedule->start_date->format('Y') }}
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center max-[640px]:hidden">
                        <span class="font-mono text-[12px] text-slate-600">{{ $schedule->start_date->diffInDays($schedule->end_date) + 1 }} hr</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($schedule->wiAssignments->isNotEmpty())
                        <div class="flex flex-wrap gap-1">
                            @foreach($schedule->wiAssignments as $assign)
                            <span class="wi-chip">
                                {{ $assign->widyaiswara->name }}
                                <form method="POST" action="{{ route('admin.wi-assignments.destroy', $assign) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bg-transparent border-none cursor-pointer p-0 text-sky-400 hover:text-red-500 leading-none">✕</button>
                                </form>
                            </span>
                            @endforeach
                        </div>
                        @endif
                        @if($allWi->isNotEmpty())
                        <details class="mt-1">
                            <summary class="text-[11px] text-sky-600 cursor-pointer list-none hover:text-sky-700">+ Assign WI</summary>
                            <form method="POST" action="{{ route('admin.schedules.assign-wi', $schedule) }}" class="mt-1.5 flex gap-1.5 items-center flex-wrap">
                                @csrf
                                <select name="widyaiswara_id" class="h-7 bg-slate-50 border border-slate-200 rounded-lg px-2 text-[11px] text-slate-900 outline-none focus:border-sky-700 select-custom min-w-[140px]">
                                    @foreach($allWi as $wi)
                                    <option value="{{ $wi->id }}">{{ $wi->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="h-7 px-3 bg-sky-700 text-white rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-sky-600">Assign</button>
                            </form>
                        </details>
                        @endif
                    </td>
                    <td class="px-4 py-3 max-[768px]:hidden">
                        @if(! $schedule->is_locked)
                        <button onclick="toggleEditRow({{ $schedule->id }})"
                            class="inline-flex items-center h-6 px-2.5 bg-slate-50 border border-slate-200 rounded text-[10px] text-slate-500 font-semibold cursor-pointer hover:border-sky-300 hover:text-sky-600 transition-colors">
                            Edit
                        </button>
                        @endif
                    </td>
                </tr>
                <!-- Inline edit row -->
                @if(! $schedule->is_locked)
                <tr id="editRow{{ $schedule->id }}" class="hidden bg-sky-50/60">
                    <td colspan="7" class="px-6 py-3">
                        <form method="POST" action="{{ route('admin.batches.schedule.update', $batch) }}" class="flex items-end gap-3 flex-wrap">
                            @csrf
                            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" value="{{ $schedule->start_date->format('Y-m-d') }}"
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" value="{{ $schedule->end_date->format('Y-m-d') }}"
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] font-mono text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Catatan</label>
                                <input type="text" name="notes" value="{{ $schedule->notes }}" placeholder="Opsional"
                                    class="h-8 bg-white border border-slate-200 rounded-lg px-2.5 text-[12px] text-slate-900 outline-none focus:border-sky-700">
                            </div>
                            <button type="submit" class="h-8 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                            <button type="button" onclick="toggleEditRow({{ $schedule->id }})"
                                class="h-8 px-3 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                        </form>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</main>

<script>
function toggleEditRow(id) {
    const row = document.getElementById('editRow' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
</body>
</html>
