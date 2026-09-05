<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $template->name }} — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
    <style>
        .phase-row { transition: background 150ms; }
        .phase-row:hover { background: #f8fafc; }
        .color-dot { width:12px; height:12px; border-radius:3px; flex-shrink:0; }
        .tag { height:20px; padding:0 7px; border-radius:100px; font-size:10px; font-weight:600; display:inline-flex; align-items:center; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-1.5 text-[12px] text-white/60 hover:text-white/90 no-underline mb-4">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            Template
        </a>
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-[clamp(18px,3vw,28px)] font-bold text-white mb-1">{{ $template->name }}</h1>
                <div class="flex gap-2 items-center">
                    <span class="font-mono text-[12px] text-white/60 bg-white/10 px-2 py-0.5 rounded">{{ $template->code }}</span>
                    @if($template->organizationalUnit)
                    <span class="text-[12px] text-white/60">{{ $template->organizationalUnit->name }}</span>
                    @endif
                    <span class="tag {{ $template->is_active ? 'bg-green-500/20 text-green-300' : 'bg-slate-500/20 text-slate-300' }}">
                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-4 mt-5 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $template->phases->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Phase</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $template->batches->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Batch Dibuat</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $template->phases->sum('duration') }}</div>
                <div class="text-xs text-white/60 mt-0.5">Total Hari</div>
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

    <!-- PHASES LIST -->
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-slate-900">Phase Pelatihan</h2>
        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('importPhaseForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 h-8 px-3 bg-green-600 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-green-500 transition-colors">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Import CSV/Excel
            </button>
            <button onclick="document.getElementById('addPhaseForm').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 h-8 px-3 bg-sky-700 text-white rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-sky-600 transition-colors">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Phase
            </button>
        </div>
    </div>

    <!-- IMPORT PHASE FORM -->
    <div id="importPhaseForm" class="hidden mb-5">
        <form method="POST" action="{{ route('admin.templates.phases.import', $template) }}" enctype="multipart/form-data"
              class="bg-white border border-green-200 rounded-xl p-5 flex flex-col gap-4">
            @csrf
            <div class="flex items-center justify-between">
                <h3 class="text-[13px] font-bold text-slate-800">Import Phase dari CSV/Excel</h3>
                <a href="{{ route('admin.templates.download-csv') }}"
                   class="inline-flex items-center gap-1.5 h-7 px-3 bg-green-50 text-green-700 border border-green-200 rounded-lg text-[11px] font-semibold no-underline hover:bg-green-100 transition-colors">
                    Unduh Contoh CSV
                </a>
            </div>
            <p class="text-[11px] text-slate-500 -mt-2">Phase dari file akan ditambahkan ke template ini (tidak membuat template baru). Kolom wajib: sequence, name, duration.</p>
            <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required
                class="w-full text-[13px] text-slate-700 file:mr-3 file:h-8 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-[12px] file:font-semibold file:cursor-pointer hover:file:bg-slate-200">
            <div class="flex gap-2 justify-end pt-1 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('importPhaseForm').classList.add('hidden')"
                    class="h-8 px-4 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                <button type="submit" class="h-8 px-4 bg-green-600 text-white rounded-lg text-[12px] font-bold border-none cursor-pointer hover:bg-green-500">Upload & Import</button>
            </div>
        </form>
    </div>

    <!-- ADD PHASE FORM -->
    <div id="addPhaseForm" class="hidden mb-5">
        <form method="POST" action="{{ route('admin.templates.phases.store', $template) }}"
              class="bg-white border border-sky-200 rounded-xl p-5 flex flex-col gap-4">
            @csrf
            <h3 class="text-[13px] font-bold text-slate-800">Phase Baru</h3>
            <div class="grid grid-cols-3 gap-3 max-[640px]:grid-cols-2 max-[400px]:grid-cols-1">
                <div class="col-span-2 max-[640px]:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Phase *</label>
                    <input type="text" name="name" placeholder="CekIn1" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Urutan *</label>
                    <input type="number" name="sequence" value="0" min="0" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Durasi *</label>
                    <input type="number" name="duration" value="1" min="1" required
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Satuan</label>
                    <select name="duration_unit" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="day">Hari</option>
                        <option value="hour">Jam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Offset Hari</label>
                    <input type="number" name="offset_days" value="0"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white"
                        title="0=berurutan, -1=H-1 dari start batch">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Hari</label>
                    <select name="day_type" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="working_day">Hari Kerja</option>
                        <option value="calendar_day">Hari Kalender</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Hari Kerja / Minggu</label>
                    <select name="work_days" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="5">5 hari (Sen–Jum)</option>
                        <option value="6">6 hari (Sen–Sab)</option>
                        <option value="7">7 hari (termasuk Minggu)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Aktivitas</label>
                    <input type="text" name="activity_type" placeholder="seminar/classroom"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Conflict Group</label>
                    <select name="conflict_group" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="">Tidak ada</option>
                        <option value="seminar">Seminar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Warna</label>
                    <input type="color" name="color" value="#0369A1"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-1.5 cursor-pointer">
                </div>
                <div class="flex items-end gap-4 pb-1 flex-wrap">
                    <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer">
                        <input type="checkbox" name="is_alert" value="1" class="accent-sky-700">
                        Alert
                    </label>
                    <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer">
                        <input type="checkbox" name="can_manual_edit" value="1" class="accent-sky-700">
                        Edit Manual
                    </label>
                </div>
            </div>
            <div class="flex gap-2 justify-end pt-1 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addPhaseForm').classList.add('hidden')"
                    class="h-8 px-4 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                <button type="submit" class="h-8 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-bold border-none cursor-pointer hover:bg-sky-600">Simpan Phase</button>
            </div>
        </form>
    </div>

    <!-- PHASES TABLE -->
    @if($template->phases->isEmpty())
    <div class="bg-white border border-slate-200 rounded-xl p-10 text-center text-slate-400">
        <p class="text-sm">Belum ada phase. Klik "Tambah Phase" untuk mulai.</p>
    </div>
    @else
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                <tr>
                    <th class="text-left px-4 py-3 w-8">#</th>
                    <th class="text-left px-4 py-3">Nama Phase</th>
                    <th class="text-center px-3 py-3">Durasi</th>
                    <th class="text-center px-3 py-3 max-[640px]:hidden">Jenis Hari</th>
                    <th class="text-left px-3 py-3 max-[768px]:hidden">Aktivitas</th>
                    <th class="text-center px-3 py-3 max-[768px]:hidden">Flag</th>
                    <th class="text-center px-3 py-3 w-8">Warna</th>
                    <th class="px-3 py-3 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($template->phases as $phase)
                <tr class="phase-row">
                    <td class="px-4 py-3 font-mono text-[12px] text-slate-400">{{ str_pad($phase->sequence, 2, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-3">
                        <span class="font-semibold text-slate-900 text-[13px]">{{ $phase->name }}</span>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <span class="font-mono text-[12px] text-slate-700">{{ $phase->duration }} {{ $phase->duration_unit === 'day' ? 'hr' : 'jam' }}</span>
                        @if($phase->offset_days != 0)
                        <span class="block text-[10px] text-amber-600 font-mono">offset {{ $phase->offset_days }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center max-[640px]:hidden">
                        @if($phase->day_type === 'working_day')
                        <span class="tag bg-sky-100 text-sky-700">Kerja</span>
                        @else
                        <span class="tag bg-violet-100 text-violet-700">Kalender</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 max-[768px]:hidden">
                        @if($phase->activity_type)
                        <span class="tag bg-slate-100 text-slate-600">{{ $phase->activity_type }}</span>
                        @endif
                        @if($phase->conflict_group)
                        <span class="tag bg-amber-100 text-amber-700 ml-1">{{ $phase->conflict_group }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center max-[768px]:hidden">
                        @if($phase->is_alert)<span class="tag bg-red-100 text-red-600">Alert</span>@endif
                        @if($phase->can_manual_edit)<span class="tag bg-green-100 text-green-700 ml-1">Edit</span>@endif
                        @if(($phase->work_days ?? 5) > 5)<span class="tag bg-amber-100 text-amber-700 ml-1">{{ $phase->work_days }}hr</span>@endif
                    </td>
                    <td class="px-3 py-3 text-center">
                        <div class="color-dot mx-auto" style="background:{{ $phase->color }}"></div>
                    </td>
                    <td class="px-3 py-3 text-right">
                        <div class="inline-flex items-center gap-1.5">
                            <button type="button"
                                onclick="document.getElementById('editPhaseRow{{ $phase->id }}').classList.toggle('hidden')"
                                class="w-7 h-7 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200 transition-all cursor-pointer flex items-center justify-center">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('admin.templates.phases.destroy', [$template, $phase]) }}" onsubmit="return confirm('Hapus phase ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-7 h-7 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition-all cursor-pointer flex items-center justify-center">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr id="editPhaseRow{{ $phase->id }}" class="hidden bg-sky-50/40">
                    <td colspan="8" class="px-4 py-4">
                        <form method="POST" action="{{ route('admin.templates.phases.update', [$template, $phase]) }}"
                              class="bg-white border border-sky-200 rounded-xl p-5 flex flex-col gap-4">
                            @csrf
                            @method('PUT')
                            <h3 class="text-[13px] font-bold text-slate-800">Edit Phase</h3>
                            <div class="grid grid-cols-3 gap-3 max-[640px]:grid-cols-2 max-[400px]:grid-cols-1">
                                <div class="col-span-2 max-[640px]:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Phase *</label>
                                    <input type="text" name="name" value="{{ $phase->name }}" required
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Urutan *</label>
                                    <input type="number" name="sequence" value="{{ $phase->sequence }}" min="0" required
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Durasi *</label>
                                    <input type="number" name="duration" value="{{ $phase->duration }}" min="1" required
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Satuan</label>
                                    <select name="duration_unit" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                                        <option value="day" {{ $phase->duration_unit === 'day' ? 'selected' : '' }}>Hari</option>
                                        <option value="hour" {{ $phase->duration_unit === 'hour' ? 'selected' : '' }}>Jam</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Offset Hari</label>
                                    <input type="number" name="offset_days" value="{{ $phase->offset_days }}"
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white"
                                        title="0=berurutan, -1=H-1 dari start batch">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Hari</label>
                                    <select name="day_type" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                                        <option value="working_day" {{ $phase->day_type === 'working_day' ? 'selected' : '' }}>Hari Kerja</option>
                                        <option value="calendar_day" {{ $phase->day_type === 'calendar_day' ? 'selected' : '' }}>Hari Kalender</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Hari Kerja / Minggu</label>
                                    <select name="work_days" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                                        <option value="5" {{ (int)$phase->work_days === 5 ? 'selected' : '' }}>5 hari (Sen–Jum)</option>
                                        <option value="6" {{ (int)$phase->work_days === 6 ? 'selected' : '' }}>6 hari (Sen–Sab)</option>
                                        <option value="7" {{ (int)$phase->work_days === 7 ? 'selected' : '' }}>7 hari (termasuk Minggu)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis Aktivitas</label>
                                    <input type="text" name="activity_type" value="{{ $phase->activity_type }}" placeholder="seminar/classroom"
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Conflict Group</label>
                                    <select name="conflict_group" class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-2.5 text-[13px] text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                                        <option value="" {{ ! $phase->conflict_group ? 'selected' : '' }}>Tidak ada</option>
                                        <option value="seminar" {{ $phase->conflict_group === 'seminar' ? 'selected' : '' }}>Seminar</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Warna</label>
                                    <input type="color" name="color" value="{{ $phase->color }}"
                                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-1.5 cursor-pointer">
                                </div>
                                <div class="flex items-end gap-4 pb-1 flex-wrap">
                                    <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer">
                                        <input type="checkbox" name="is_alert" value="1" {{ $phase->is_alert ? 'checked' : '' }} class="accent-sky-700">
                                        Alert
                                    </label>
                                    <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer">
                                        <input type="checkbox" name="can_manual_edit" value="1" {{ $phase->can_manual_edit ? 'checked' : '' }} class="accent-sky-700">
                                        Edit Manual
                                    </label>
                                </div>
                            </div>
                            <div class="flex gap-2 justify-end pt-1 border-t border-slate-100">
                                <button type="button"
                                    onclick="document.getElementById('editPhaseRow{{ $phase->id }}').classList.add('hidden')"
                                    class="h-8 px-4 bg-slate-100 text-slate-600 rounded-lg text-[12px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                                <button type="submit" class="h-8 px-4 bg-sky-700 text-white rounded-lg text-[12px] font-bold border-none cursor-pointer hover:bg-sky-600">Simpan Perubahan</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- BATCHES SECTION -->
    @if($template->batches->isNotEmpty())
    <div class="mt-8">
        <h2 class="text-base font-bold text-slate-900 mb-4">Batch Menggunakan Template Ini</h2>
        <div class="grid grid-cols-3 gap-3 max-[768px]:grid-cols-2 max-[480px]:grid-cols-1">
            @foreach($template->batches as $batch)
            <a href="{{ route('admin.batches.show', $batch) }}"
               class="bg-white border border-slate-200 rounded-xl p-4 no-underline hover:border-sky-300 hover:shadow-md transition-all group">
                <div class="font-semibold text-slate-900 text-[13px] group-hover:text-sky-700">{{ $batch->name }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Angkatan {{ $batch->batch_number }} • {{ $batch->year }}</div>
                <span class="inline-flex items-center gap-1 mt-2 h-5 px-2 rounded-full text-[10px] font-semibold
                    {{ $batch->status === 'generated' ? 'bg-green-100 text-green-700' : ($batch->status === 'finished' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">
                    {{ ucfirst($batch->status) }}
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</main>
</body>
</html>
