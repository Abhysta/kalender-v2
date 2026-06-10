<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Template — Admin BPSDM</title>
    @vite(['resources/css/app.css'])
    <style>
        .drop-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 36px 24px;
            text-align: center;
            cursor: pointer;
            transition: border-color 200ms, background 200ms;
            background: #f8fafc;
        }
        .drop-zone:hover, .drop-zone.drag-over {
            border-color: #0369a1;
            background: #f0f9ff;
        }
        .drop-zone input[type=file] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
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
        <h1 class="text-[clamp(18px,3vw,26px)] font-bold text-white mb-1">Import Template via CSV/Excel</h1>
        <p class="text-[13px] text-white/70">Upload file CSV atau Excel berisi daftar phase pelatihan. Satu file = satu template baru.</p>
    </div>
</section>

<main class="max-w-[860px] mx-auto px-6 pt-7 pb-16 max-[640px]:px-4">
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-[13px] text-red-800">
        @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
    </div>
    @endif

    <div class="grid grid-cols-[1fr_300px] gap-7 max-[800px]:grid-cols-1">

        <!-- FORM -->
        <form method="POST" action="{{ route('admin.templates.import.store') }}" enctype="multipart/form-data" class="flex flex-col gap-5">
            @csrf

            <!-- Metadata -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                <h2 class="text-[13px] font-bold text-slate-800">Informasi Template</h2>

                <div class="grid grid-cols-2 gap-4 max-[480px]:grid-cols-1">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Kode Template *</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="PKA-2026" required
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm font-mono text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Nama Template *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Pelatihan Kepemimpinan Administrator" required
                            class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Unit Organisasi</label>
                    @if(Auth::user()->hasRole('super_admin'))
                    <select name="organizational_unit_id"
                        class="w-full h-9 bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white select-custom">
                        <option value="">— Semua Unit —</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" name="organizational_unit_id" value="{{ Auth::user()->organizational_unit_id }}">
                    <div class="w-full h-9 bg-slate-100 border-[1.5px] border-slate-200 rounded-lg px-3 text-sm text-slate-500 flex items-center cursor-not-allowed">
                        {{ Auth::user()->organizationalUnit?->name ?? '—' }}
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Deskripsi singkat template…"
                        class="w-full bg-slate-50 border-[1.5px] border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-900 outline-none focus:border-sky-700 focus:bg-white resize-none">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- File Upload -->
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-[13px] font-bold text-slate-800">File Phase</h2>
                    <a href="{{ route('admin.templates.download-csv') }}"
                       class="inline-flex items-center gap-1.5 h-7 px-3 bg-green-50 text-green-700 border border-green-200 rounded-lg text-[11px] font-semibold no-underline hover:bg-green-100 transition-colors">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Unduh Template CSV
                    </a>
                </div>

                <div class="drop-zone relative" id="dropZone">
                    <input type="file" name="file" id="fileInput" accept=".csv,.txt,.xlsx,.xls" required>
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-2 text-slate-400">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p class="text-[13px] text-slate-600 font-semibold mb-1">Drag & drop file di sini</p>
                    <p class="text-[11px] text-slate-400">atau klik untuk pilih file</p>
                    <p class="text-[11px] text-slate-400 mt-1">Mendukung: .csv, .xlsx, .xls (max 2MB)</p>
                    <div id="fileName" class="mt-2 text-[12px] text-sky-700 font-semibold hidden"></div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-[12px] text-amber-800">
                    <div class="font-semibold mb-1">Catatan untuk .xlsx:</div>
                    <div>Dukungan Excel native memerlukan: <code class="font-mono bg-amber-100 px-1 rounded">composer require phpoffice/phpspreadsheet</code></div>
                    <div class="mt-0.5">Tanpa package tersebut, gunakan format <strong>.csv</strong> (Export dari Excel via File → Save As → CSV).</div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.templates.index') }}"
                   class="h-10 px-5 flex items-center bg-slate-100 text-slate-700 rounded-lg text-sm font-semibold no-underline hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 h-10 px-6 bg-sky-700 text-white rounded-lg text-sm font-bold border-none cursor-pointer hover:bg-sky-600 transition-colors">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Import Template
                </button>
            </div>
        </form>

        <!-- GUIDE -->
        <div class="flex flex-col gap-4">
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <h3 class="text-[13px] font-bold text-slate-800 mb-3">Format Kolom CSV</h3>
                <div class="flex flex-col gap-1.5 text-[11px]">
                    @php
                    $cols = [
                        ['sequence','Urutan phase (angka)','required','#dcfce7','#166534'],
                        ['name','Nama aktivitas','required','#dcfce7','#166534'],
                        ['duration','Durasi (angka)','required','#dcfce7','#166534'],
                        ['duration_unit','day / hour','optional','#f1f5f9','#475569'],
                        ['day_type','working_day / calendar_day','optional','#f1f5f9','#475569'],
                        ['work_days','5 / 6 / 7 — hari kerja per minggu (default 5)','optional','#fef3c7','#92400e'],
                        ['offset_days','0 = berurutan, -1 = H-1','optional','#f1f5f9','#475569'],
                        ['activity_type','seminar/classroom/ojt dll','optional','#f1f5f9','#475569'],
                        ['conflict_group','seminar (global conflict)','optional','#f1f5f9','#475569'],
                        ['color','HEX (#0369A1)','optional','#f1f5f9','#475569'],
                        ['is_alert','1 / 0','optional','#f1f5f9','#475569'],
                        ['can_manual_edit','1 / 0','optional','#f1f5f9','#475569'],
                    ];
                    @endphp
                    @foreach($cols as [$col, $desc, $req, $bg, $fg])
                    <div class="flex items-start gap-2">
                        <span class="font-mono text-[10px] font-bold shrink-0 h-5 px-1.5 rounded flex items-center" style="background:{{ $bg }};color:{{ $fg }}">{{ $col }}</span>
                        <span class="text-slate-500 leading-5">{{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-slate-900 rounded-xl p-4">
                <div class="text-[10px] font-bold text-slate-400 mb-2 font-mono">CONTOH CSV</div>
                <pre class="text-[10px] text-green-400 font-mono leading-relaxed overflow-x-auto">sequence,name,duration,duration_unit,day_type,work_days,offset_days,...
1,CekIn1,1,day,working_day,5,-1
2,On-Campus1,3,day,working_day,5,0
3,Klasikal1,5,day,working_day,6,0
4,Seminar,1,day,working_day,5,0
5,OJT1,10,day,working_day,6,0</pre>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <h3 class="text-[12px] font-bold text-slate-800 mb-2">Alur Import</h3>
                <div class="flex flex-col gap-1.5">
                    @foreach([
                        ['1','Unduh template CSV di atas'],
                        ['2','Buka di Excel / Google Sheets'],
                        ['3','Isi data phase pelatihan'],
                        ['4','Simpan sebagai .csv'],
                        ['5','Upload di form ini'],
                        ['6','Phase otomatis terbuat'],
                    ] as [$num, $step])
                    <div class="flex items-center gap-2 text-[11px] text-slate-600">
                        <span class="w-5 h-5 bg-sky-700 text-white rounded-md font-bold font-mono text-[10px] flex items-center justify-center shrink-0">{{ $num }}</span>
                        {{ $step }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const input = document.getElementById('fileInput');
const zone  = document.getElementById('dropZone');
const label = document.getElementById('fileName');

input.addEventListener('change', () => {
    if (input.files[0]) {
        label.textContent = '✓ ' + input.files[0].name;
        label.classList.remove('hidden');
    }
});
zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    if (e.dataTransfer.files[0]) {
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        input.files = dt.files;
        label.textContent = '✓ ' + e.dataTransfer.files[0].name;
        label.classList.remove('hidden');
    }
});
</script>
</body>
</html>
