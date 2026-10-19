<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $calendar->name }} — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <a href="{{ route('admin.annual-calendars.index') }}" class="inline-flex items-center gap-1.5 text-[12px] text-white/60 hover:text-white/90 no-underline mb-4">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            Kalender Tahunan
        </a>
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-[clamp(18px,3vw,28px)] font-bold text-white mb-1">{{ $calendar->name }}</h1>
                <div class="flex gap-2 items-center flex-wrap">
                    <span class="font-mono text-[12px] text-white/60">{{ $calendar->year }}</span>
                    @if($calendar->organizationalUnit)<span class="text-[12px] text-white/60">• {{ $calendar->organizationalUnit->name }}</span>@endif
                    @if($calendar->archived_at)
                    <span class="inline-flex items-center gap-1 h-5 px-2.5 rounded-full text-[10px] font-semibold bg-slate-500/20 text-slate-300">Diarsipkan</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if($calendar->archived_at)
                <form method="POST" action="{{ route('admin.annual-calendars.unarchive', $calendar) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-white/10 border border-white/20 text-white rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-white/20">Aktifkan</button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.annual-calendars.archive', $calendar) }}" onsubmit="return confirmModal(event, 'Arsipkan kalender ini? Batch/Sequence yang sudah terhubung tidak akan terpengaruh.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-white/10 border border-white/20 text-white rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-white/20">Arsipkan</button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.annual-calendars.destroy', $calendar) }}" onsubmit="return confirmModal(event, 'Hapus kalender ini? Batch/Sequence yang terhubung TIDAK akan terhapus, hanya kehilangan rujukan rencana.', true)">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 bg-red-500/20 border border-red-400/30 text-red-300 rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-red-500/30">Hapus</button>
                </form>
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
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">{{ $errors->first() }}</div>
    @endif

    <h2 class="text-base font-bold text-slate-900 mb-4">Daftar Kegiatan/Pelatihan</h2>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden mb-6">
        @if($calendar->activities->isEmpty())
        <div class="text-center py-10 text-slate-400 text-sm">Belum ada kegiatan. Tambahkan di bawah — tanggal boleh dikosongkan jika belum ditentukan.</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3">Kegiatan</th>
                    <th class="text-left px-4 py-3">Tanggal Rencana</th>
                    <th class="text-left px-4 py-3">Dipakai di Sequence</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($calendar->activities as $activity)
                <tr>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-[13px] text-slate-900">{{ $activity->name }}</div>
                        @if($activity->template)<div class="text-[11px] text-slate-500">{{ $activity->template->name }}</div>@endif
                    </td>
                    <td class="px-4 py-3 font-mono text-[12px] text-slate-700">
                        @if($activity->planned_start_date)
                            {{ $activity->planned_start_date->format('d M Y') }}
                            @if($activity->planned_end_date && $activity->planned_end_date->ne($activity->planned_start_date)) — {{ $activity->planned_end_date->format('d M Y') }} @endif
                        @else
                            <span class="text-slate-400 italic">belum ditentukan</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($activity->trainingBatches->isEmpty())
                        <span class="text-[11px] text-slate-400">—</span>
                        @else
                        <div class="flex flex-col gap-1">
                        @foreach($activity->trainingBatches as $tb)
                            <a href="{{ route('admin.batches.show', $tb) }}" class="text-[11px] text-sky-700 no-underline hover:underline">
                                {{ $tb->name }}
                                @if($tb->hasDateOverride())<span class="text-amber-600">(override)</span>@endif
                            </a>
                        @endforeach
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.annual-calendars.activities.destroy', [$calendar, $activity]) }}" onsubmit="return confirmModal(event, 'Hapus kegiatan ini? Sequence yang terhubung tidak akan terhapus.', true)" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[11px] text-red-500 hover:text-red-700 bg-transparent border-none cursor-pointer">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <h3 class="text-sm font-bold text-slate-900 mb-3">Tambah Kegiatan</h3>
    <form method="POST" action="{{ route('admin.annual-calendars.activities.store', $calendar) }}" class="bg-white border border-slate-200 rounded-xl p-5 grid grid-cols-2 gap-4 max-[640px]:grid-cols-1">
        @csrf
        <div class="col-span-2 max-[640px]:col-span-1">
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama Kegiatan *</label>
            <input type="text" name="name" placeholder="Pelatihan A" required
                class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Template (opsional)</label>
            <select name="training_template_id" class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                <option value="">— Tidak ada —</option>
                @foreach($templates as $template)
                <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </select>
        </div>
        <div></div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tanggal Mulai Rencana</label>
            <input type="date" name="planned_start_date" class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
            <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika belum ditentukan.</p>
        </div>
        <div>
            <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tanggal Selesai Rencana</label>
            <input type="date" name="planned_end_date" class="w-full h-10 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
        </div>
        <div class="col-span-2 flex justify-end max-[640px]:col-span-1">
            <button type="submit" class="h-10 px-5 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600">Tambah Kegiatan</button>
        </div>
    </form>
</main>
</body>
</html>
