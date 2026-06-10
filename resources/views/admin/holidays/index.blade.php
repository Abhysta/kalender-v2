<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hari Libur — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
    <style>
        .side-card { opacity: 0; transform: translateY(16px); }
        #importDropzone { transition: border-color 150ms, background 150ms; }
        #importDropzone.drag-over { border-color: #0284c7; background: #f0f9ff; }

        /* Conflict modal */
        #conflictOverlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(15,23,42,.55); backdrop-filter: blur(3px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
        }
        #conflictOverlay.open { pointer-events: auto; }
        #conflictModal {
            background: #fff; border-radius: 16px; width: min(480px, calc(100vw - 32px));
            box-shadow: 0 20px 60px rgba(15,23,42,.25);
            opacity: 0; transform: scale(.92) translateY(12px);
            overflow: hidden;
        }
        .conflict-badge {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px; letter-spacing: .03em;
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen">
@include('admin.partials.navbar')

<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-9 pb-10">
    <div class="max-w-[1200px] mx-auto">
        <div class="inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/85 mb-3">
            <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>manage_holidays
        </div>
        <h1 class="text-[clamp(20px,3vw,30px)] font-bold text-white mb-2">Hari Libur</h1>
        <p class="text-[13px] text-white/70 mb-5">Tanggal libur nasional dan cuti bersama. Digunakan engine jadwal untuk hitung hari kerja.</p>
        <div class="flex gap-4 flex-wrap">
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $holidays->flatten()->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Total Libur</div>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-xl px-4 py-3">
                <div class="font-mono text-xl font-semibold text-white">{{ $holidays->count() }}</div>
                <div class="text-xs text-white/60 mt-0.5">Tahun</div>
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
    @if(session('info'))
    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-amber-800">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        {{ session('info') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-[1fr_360px] gap-7 max-[900px]:grid-cols-1">
        <!-- LIST BY YEAR -->
        <div>
            @if($holidays->isEmpty())
            <div class="text-center py-14 text-slate-400 bg-white border border-slate-200 rounded-xl">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-2 opacity-40">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <p class="text-sm">Belum ada hari libur.</p>
            </div>
            @else
            <div class="flex flex-col gap-5">
                @foreach($holidays as $year => $yearHolidays)
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="font-mono font-bold text-slate-900 text-base">{{ $year }}</span>
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span class="text-[12px] text-slate-400">{{ $yearHolidays->count() }} hari</span>
                    </div>
                    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-slate-100">
                                @foreach($yearHolidays as $holiday)
                                <tr class="hover:bg-slate-50 transition-colors" id="hRow{{ $holiday->id }}">
                                    <td class="px-4 py-2.5">
                                        <span class="font-mono text-[12px] text-slate-700">{{ $holiday->date->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="text-[13px] text-slate-900">{{ $holiday->name }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if($holiday->is_national)
                                        <span class="inline-flex items-center h-4 px-1.5 bg-red-100 text-red-700 rounded text-[9px] font-bold">NASIONAL</span>
                                        @else
                                        <span class="inline-flex items-center h-4 px-1.5 bg-orange-100 text-orange-700 rounded text-[9px] font-bold">CUTI</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex gap-1.5 justify-end">
                                            <button onclick="toggleEditHoliday({{ $holiday->id }})"
                                                class="h-6 px-2 bg-slate-50 border border-slate-200 rounded text-[10px] text-slate-500 font-semibold cursor-pointer hover:border-sky-300 hover:text-sky-600 transition-colors">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.holidays.destroy', $holiday) }}" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="h-6 px-2 bg-red-50 border border-red-200 rounded text-[10px] text-red-600 font-semibold cursor-pointer hover:bg-red-100 transition-colors">✕</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Inline edit row -->
                                <tr id="editHRow{{ $holiday->id }}" class="hidden bg-sky-50/60">
                                    <td colspan="4" class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.holidays.update', $holiday) }}" class="flex items-end gap-3 flex-wrap">
                                            @csrf @method('PUT')
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Tanggal</label>
                                                <input type="date" name="date" value="{{ $holiday->date->format('Y-m-d') }}" required
                                                    class="h-7 bg-white border border-slate-200 rounded-lg px-2.5 text-[11px] font-mono text-slate-900 outline-none focus:border-sky-700">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-semibold text-slate-500 mb-1">Nama</label>
                                                <input type="text" name="name" value="{{ $holiday->name }}" required
                                                    class="h-7 bg-white border border-slate-200 rounded-lg px-2.5 text-[11px] text-slate-900 outline-none focus:border-sky-700 min-w-[160px]">
                                            </div>
                                            <div class="flex items-center gap-1.5 pb-0.5">
                                                <input type="checkbox" name="is_national" value="1" {{ $holiday->is_national ? 'checked' : '' }} class="accent-sky-700">
                                                <span class="text-[11px] text-slate-600">Nasional</span>
                                            </div>
                                            <button type="submit" class="h-7 px-3 bg-sky-700 text-white rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-sky-600">Simpan</button>
                                            <button type="button" onclick="toggleEditHoliday({{ $holiday->id }})"
                                                class="h-7 px-2.5 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-semibold border-none cursor-pointer hover:bg-slate-200">Batal</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- SIDE PANEL: ADD + IMPORT + BULK -->
        <div class="flex flex-col gap-5">
            <!-- ADD SINGLE -->
            <div class="side-card">
                <h2 class="text-base font-bold text-slate-900 mb-3">Tambah Hari Libur</h2>
                <form method="POST" action="{{ route('admin.holidays.store') }}" class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Tanggal *</label>
                        <input type="date" name="date" value="{{ old('date') }}" required
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Hari Kemerdekaan RI" required
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_national" value="1" id="isNational" checked class="accent-sky-700">
                        <label for="isNational" class="text-[13px] text-slate-700 cursor-pointer">Libur Nasional</label>
                    </div>
                    <button type="submit" class="h-9 w-full bg-sky-700 text-white rounded-lg text-[13px] font-bold border-none cursor-pointer hover:bg-sky-600">
                        Tambah
                    </button>
                </form>
            </div>

            <!-- IMPORT CSV/EXCEL -->
            <div class="side-card">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-slate-900">Import CSV / Excel</h2>
                    <a href="{{ route('admin.holidays.download-csv') }}"
                       class="inline-flex items-center gap-1 h-6 px-2.5 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-semibold no-underline hover:bg-slate-200 transition-colors">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Unduh Template
                    </a>
                </div>
                <form method="POST" action="{{ route('admin.holidays.import') }}" enctype="multipart/form-data"
                      class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4" id="importForm">
                    @csrf
                    <div id="importDropzone"
                         class="border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center gap-2 cursor-pointer"
                         onclick="document.getElementById('importFile').click()">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/>
                            <polyline points="9 15 12 12 15 15"/>
                        </svg>
                        <p class="text-[12px] text-slate-500 text-center">
                            Klik atau drag file CSV / Excel<br>
                            <span class="text-[10px] text-slate-400">Kolom: date, name, is_national</span>
                        </p>
                        <span id="importFileName" class="text-[11px] font-semibold text-sky-700 hidden"></span>
                    </div>
                    <input type="file" id="importFile" name="file" accept=".csv,.txt,.xlsx,.xls" class="hidden">
                    @error('file')
                    <p class="text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                    <button type="submit" id="importBtn"
                            class="h-9 w-full bg-emerald-700 text-white rounded-lg text-[13px] font-bold border-none cursor-pointer hover:bg-emerald-600 transition-colors flex items-center justify-center gap-2">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Import File
                    </button>
                </form>
                <p class="text-[10px] text-slate-400 mt-2 px-1">Duplikat tanggal akan dilewati (tidak ditimpa).</p>
            </div>

            <!-- BULK JSON -->
            <div class="side-card">
                <h2 class="text-base font-bold text-slate-900 mb-3">Import JSON</h2>
                <form method="POST" action="{{ route('admin.holidays.bulk') }}" class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                    @csrf
                    <p class="text-[12px] text-slate-500">Format: array of <code class="font-mono bg-slate-100 px-1 rounded text-[10px]">{date, name, is_national}</code></p>
                    <textarea name="holidays_json" rows="5" placeholder='[
  {"date":"2026-01-01","name":"Tahun Baru","is_national":true},
  {"date":"2026-08-17","name":"HUT RI","is_national":true}
]' class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 py-2.5 text-[11px] font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white resize-none"></textarea>
                    <button type="submit" class="h-9 w-full bg-slate-700 text-white rounded-lg text-[13px] font-bold border-none cursor-pointer hover:bg-slate-600">
                        Import JSON
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

@if(session('conflict_alert'))
@php $ca = session('conflict_alert'); $rv = $ca['revert']; @endphp
<!-- CONFLICT ALERT MODAL -->
<div id="conflictOverlay" role="dialog" aria-modal="true" aria-labelledby="conflictTitle">
    <div id="conflictModal">
        <!-- Header stripe -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center gap-3">
            <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <div id="conflictTitle" class="text-white font-bold text-[15px] leading-tight">Konflik Jadwal Terdeteksi</div>
                <div class="text-white/80 text-[11px] mt-0.5">Perubahan hari libur mempengaruhi jadwal aktif</div>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-5">
            <div class="flex items-center gap-2 mb-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span class="text-[12px] text-slate-600">
                    @if($ca['type'] === 'added') Ditambahkan:
                    @elseif($ca['type'] === 'updated') Diperbarui:
                    @else Dihapus:
                    @endif
                    <strong class="text-slate-800">{{ $ca['holiday_name'] }}</strong>
                    <span class="font-mono text-slate-500 ml-1">{{ \Carbon\Carbon::parse($ca['holiday_date'])->format('d M Y') }}</span>
                </span>
            </div>

            <p class="text-[13px] text-slate-700 mb-4">Kalkulasi ulang jadwal selesai. Berikut konflik yang ditemukan:</p>

            <div class="flex flex-wrap gap-2 mb-5">
                @if($ca['seminar_count'] > 0)
                <span class="conflict-badge bg-red-100 text-red-700">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $ca['seminar_count'] }} Konflik Seminar
                </span>
                @endif
                @if($ca['wi_count'] > 0)
                <span class="conflict-badge bg-orange-100 text-orange-700">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $ca['wi_count'] }} Konflik WI
                </span>
                @endif
                @if($ca['locked_count'] > 0)
                <span class="conflict-badge bg-slate-100 text-slate-600">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    {{ $ca['locked_count'] }} Jadwal Terkunci
                </span>
                @endif
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 mb-5 text-[12px] text-amber-800 leading-relaxed">
                <strong>Jadwal sudah diperbarui.</strong> Pilih <em>Lanjutkan</em> untuk menerima perubahan ini (batch terdampak akan ditandai), atau <em>Batalkan</em> untuk mengembalikan hari libur ke kondisi sebelumnya.
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <!-- Continue: just dismiss the modal -->
                <button id="conflictContinueBtn" type="button"
                    class="flex-1 h-10 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[13px] font-bold border-none cursor-pointer transition-colors">
                    Lanjutkan
                </button>
                <!-- Revert: form POST -->
                <form method="POST" action="{{ route('admin.holidays.revert-conflict') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="action" value="{{ $rv['action'] }}">
                    @if($rv['action'] === 'delete')
                        <input type="hidden" name="holiday_id" value="{{ $rv['holiday_id'] }}">
                    @elseif($rv['action'] === 'restore')
                        <input type="hidden" name="holiday_id"     value="{{ $rv['holiday_id'] }}">
                        <input type="hidden" name="old_date"       value="{{ $rv['old_date'] }}">
                        <input type="hidden" name="old_name"       value="{{ $rv['old_name'] }}">
                        <input type="hidden" name="old_is_national" value="{{ $rv['old_is_national'] }}">
                    @elseif($rv['action'] === 'recreate')
                        <input type="hidden" name="date"       value="{{ $rv['date'] }}">
                        <input type="hidden" name="name"       value="{{ $rv['name'] }}">
                        <input type="hidden" name="is_national" value="{{ $rv['is_national'] }}">
                        <input type="hidden" name="unit_id"    value="{{ $rv['unit_id'] }}">
                    @endif
                    <button type="submit"
                        class="w-full h-10 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-[13px] font-semibold border border-slate-200 cursor-pointer transition-colors">
                        Batalkan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

animate('.side-card', {
    opacity: [0, 1],
    translateY: [16, 0],
    duration: 400,
    ease: 'outExpo',
    delay: stagger(80, { start: 80 }),
});

// File input interactions
const fileInput  = document.getElementById('importFile');
const dropzone   = document.getElementById('importDropzone');
const fileLabel  = document.getElementById('importFileName');

if (fileInput) {
    fileInput.addEventListener('change', () => {
        const f = fileInput.files[0];
        if (f) {
            fileLabel.textContent = f.name;
            fileLabel.classList.remove('hidden');
            animate(fileLabel, { opacity: [0, 1], translateY: [-4, 0], duration: 250 });
        }
    });

    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
        const f = e.dataTransfer.files[0];
        if (f) {
            const dt = new DataTransfer();
            dt.items.add(f);
            fileInput.files = dt.files;
            fileLabel.textContent = f.name;
            fileLabel.classList.remove('hidden');
        }
    });
}

// Conflict modal
const overlay = document.getElementById('conflictOverlay');
const modal   = document.getElementById('conflictModal');
const continueBtn = document.getElementById('conflictContinueBtn');

function openConflictModal() {
    overlay.classList.add('open');
    const tl = createTimeline();
    tl.add(overlay, { opacity: [0, 1], duration: 220, ease: 'outQuad' })
      .add(modal,   { opacity: [0, 1], scale: [.92, 1], translateY: [12, 0], duration: 380, ease: 'outExpo' }, '-=120');
}

function closeConflictModal(onDone) {
    const tl = createTimeline();
    tl.add(modal,   { opacity: [1, 0], scale: [1, .94], translateY: [0, 8], duration: 220, ease: 'inQuad' })
      .add(overlay, { opacity: [1, 0], duration: 160, ease: 'inQuad' }, '-=80')
      .then(() => {
          overlay.classList.remove('open');
          if (onDone) onDone();
      });
}

if (overlay) {
    // auto-open on page load
    setTimeout(openConflictModal, 320);

    // "Lanjutkan" closes the modal
    if (continueBtn) {
        continueBtn.addEventListener('click', () => closeConflictModal());
    }

    // close on overlay click (outside modal)
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeConflictModal();
    });
}
</script>
<script>
function toggleEditHoliday(id) {
    const row = document.getElementById('editHRow' + id);
    if (row) row.classList.toggle('hidden');
}
</script>
</body>
</html>
