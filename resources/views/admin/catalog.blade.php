<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — Katalog Pelatihan — BPSDM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Fira+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .card { opacity:0; transform:translateY(20px); background:#fff; border-radius:14px; border:1.5px solid #e2e8f0; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow 220ms, transform 220ms; cursor:pointer; }
        .card:hover { box-shadow:0 8px 28px rgba(15,23,42,.1); transform:translateY(-2px) !important; }
        .card-header { height:96px; position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center; }
        .card-bg { position:absolute; inset:0; }
        .card-bg-icon { position:absolute; right:-10px; bottom:-10px; opacity:.15; }
        .card-category-badge { position:absolute; top:10px; left:10px; height:22px; padding:0 9px; border-radius:100px; font-size:10px; font-weight:600; letter-spacing:.4px; text-transform:uppercase; display:flex; align-items:center; gap:5px; background:rgba(0,0,0,.35); color:#fff; }
        .card-status { position:absolute; top:10px; right:10px; height:22px; padding:0 9px; border-radius:100px; font-size:10px; font-weight:600; display:flex; align-items:center; gap:5px; background:rgba(0,0,0,.35); color:#fff; }
        .status-dot { width:6px; height:6px; border-radius:50%; }
        .card-body { padding:14px 16px 10px; flex:1; }
        .card-title { font-size:14px; font-weight:700; color:#0f172a; line-height:1.4; margin-bottom:8px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .card-meta { display:flex; flex-direction:column; gap:4px; }
        .meta-item { display:flex; align-items:center; gap:5px; font-size:11px; color:#64748b; }
        .card-footer { padding:10px 16px 14px; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:8px; margin-top:auto; }
        .btn-detail { display:inline-flex; align-items:center; gap:5px; height:30px; padding:0 12px; border-radius:8px; font-size:11px; font-weight:600; text-decoration:none; transition:all 150ms; cursor:pointer; }
        .btn-detail.available { background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; }
        .btn-detail.available:hover { background:#bae6fd; }
        .btn-manage { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
        .btn-manage:hover { background:#f1f5f9; }
        .chip { display:inline-flex; align-items:center; gap:5px; height:32px; padding:0 14px; border-radius:100px; font-size:12px; font-weight:500; font-family:inherit; border:1.5px solid #e2e8f0; background:#fff; color:#64748b; cursor:pointer; transition:all 150ms; white-space:nowrap; }
        .chip:hover { border-color:#94a3b8; color:#334155; }
        .chip.active { background:#0369a1; border-color:#0369a1; color:#fff; }
        .chip-count { font-family:'Fira Code',monospace; font-size:10px; opacity:.75; }
        .nav-link { display:flex; align-items:center; gap:6px; height:36px; padding:0 12px; border-radius:8px; font-size:13px; font-weight:500; color:#475569; text-decoration:none; transition:all 150ms; }
        .nav-link:hover { background:#f1f5f9; color:#0f172a; }
        .nav-link.active { background:#e0f2fe; color:#0369a1; font-weight:600; }
        .empty { text-align:center; padding:64px 24px; color:#94a3b8; grid-column:1/-1; }
        .empty h3 { font-size:15px; font-weight:600; color:#334155; margin:12px 0 6px; }
        .empty p { font-size:13px; }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .select-custom { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748B' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen text-base leading-[1.6]">

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-white/[0.92] backdrop-blur-md border-b border-slate-200 px-6 h-16 flex items-center gap-5 max-[640px]:px-4 max-[640px]:gap-3" id="navbar">
    <a href="{{ route('admin.catalog') }}" class="flex items-center gap-2.5 no-underline shrink-0">
        <div class="w-9 h-9 bg-sky-700 rounded-lg flex items-center justify-center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="font-mono text-[15px] font-semibold text-slate-900 tracking-[-0.3px]">BPSDM Kalender</div>
            <div class="text-[11px] text-slate-500 max-[640px]:hidden">Sistem Pelatihan ASN</div>
        </div>
    </a>

    <span class="inline-flex items-center gap-1.5 bg-amber-100 text-amber-800 border border-amber-200 rounded-full px-2.5 py-[3px] text-[11px] font-bold tracking-[0.3px] shrink-0">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Admin
    </span>

    <nav class="flex items-center gap-1">
        <a href="{{ route('admin.catalog') }}" class="nav-link active" aria-current="page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="max-[400px]:hidden">Katalog</span>
        </a>
        <a href="{{ route('admin.calendar') }}" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span class="max-[400px]:hidden">Kalender</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="nav-link max-[480px]:hidden">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Admin</span>
        </a>
    </nav>

    <div class="flex items-center gap-2 ml-auto">
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit" class="inline-flex items-center gap-1.5 h-[34px] px-3.5 bg-red-50 text-red-800 border border-red-200 rounded-lg text-[13px] font-semibold cursor-pointer hover:bg-red-100">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span class="max-[400px]:hidden">Logout</span>
            </button>
        </form>
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-700 to-cyan-500 flex items-center justify-center text-white text-[13px] font-semibold cursor-pointer border-2 border-slate-200 hover:border-sky-700 hover:shadow-[0_0_0_3px_#E0F2FE]" aria-label="Profil admin">
            {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-pattern px-6 pt-10 pb-12 relative overflow-hidden max-[768px]:px-5 max-[768px]:pt-7 max-[768px]:pb-9" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0284c7 100%)" id="hero">
    <div class="max-w-[1200px] mx-auto">
        <div class="hero-badge inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] text-white/90 mb-4">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
            Mode Admin — Kelola Angkatan Pelatihan
        </div>
        <h1 class="text-[clamp(22px,4vw,34px)] font-bold text-white leading-tight mb-2">Katalog Angkatan Pelatihan ASN</h1>
        <p class="text-[14px] text-white/[0.72] max-w-[500px] mb-7">Daftar semua angkatan pelatihan yang terdaftar. Klik Kalender untuk melihat jadwal aktivitas.</p>
        <div class="flex gap-5 flex-wrap" id="statsRow">
            @php
                $totalBatches     = $batches->count();
                $generatedBatches = $batches->whereIn('status', ['generated','active'])->count();
                $draftBatches     = $batches->where('status', 'draft')->count();
            @endphp
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3">
                <div class="font-mono text-2xl font-semibold text-white leading-none">{{ $totalBatches }}</div>
                <div class="text-xs text-white/[0.65] mt-1">Total Angkatan</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3">
                <div class="font-mono text-2xl font-semibold text-white leading-none">{{ $generatedBatches }}</div>
                <div class="text-xs text-white/[0.65] mt-1">Terjadwal</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3">
                <div class="font-mono text-2xl font-semibold text-white leading-none">{{ $draftBatches }}</div>
                <div class="text-xs text-white/[0.65] mt-1">Draft</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3">
                <div class="font-mono text-2xl font-semibold text-white leading-none">{{ $units->count() }}</div>
                <div class="text-xs text-white/[0.65] mt-1">Unit Org</div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN -->
<main class="max-w-[1200px] mx-auto px-6 pt-8 pb-16 max-[640px]:px-4 max-[640px]:pt-5">

    <!-- FILTER BAR -->
    <div class="flex items-center gap-3 mb-6 flex-wrap no-scrollbar max-[640px]:gap-2 max-[640px]:flex-nowrap max-[640px]:overflow-x-auto max-[640px]:pb-1">
        <span class="text-[13px] font-medium text-slate-500 shrink-0">Filter:</span>
        <div class="chips no-scrollbar flex gap-2 flex-wrap max-[640px]:flex-nowrap max-[640px]:overflow-x-auto" id="chips">
            <button class="chip active" data-filter="all">Semua <span class="chip-count">{{ $totalBatches }}</span></button>
            <button class="chip" data-filter="status:generated">Terjadwal <span class="chip-count">{{ $generatedBatches }}</span></button>
            <button class="chip" data-filter="status:draft">Draft <span class="chip-count">{{ $draftBatches }}</span></button>
            @foreach($units as $unit)
            <button class="chip" data-filter="unit:{{ $unit->id }}">{{ $unit->name }} <span class="chip-count">{{ $batches->where('organizational_unit_id', $unit->id)->count() }}</span></button>
            @endforeach
        </div>
        <div class="ml-auto flex items-center gap-2 shrink-0">
            <select id="sortSelect" aria-label="Urutkan"
                class="select-custom h-[34px] pl-3 pr-8 border-[1.5px] border-slate-200 rounded-lg text-[13px] text-slate-700 bg-white cursor-pointer outline-none focus:border-sky-700 max-[640px]:hidden">
                <option value="terbaru">Terbaru</option>
                <option value="nama">Nama A-Z</option>
                <option value="tahun">Tahun</option>
            </select>
            <a href="{{ route('admin.batches.index') }}" class="inline-flex items-center gap-[7px] h-9 px-4 bg-sky-700 text-white border-none rounded-lg text-[13px] font-semibold no-underline hover:bg-sky-600 hover:shadow-[0_4px_12px_rgba(3,105,161,.3)]">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span class="max-[400px]:hidden">Kelola Angkatan</span>
            </a>
        </div>
    </div>

    <!-- SEARCH -->
    <div class="relative mb-5 max-w-sm">
        <svg class="absolute left-[11px] top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="searchInput" placeholder="Cari angkatan atau template…" autocomplete="off"
            class="w-full h-[38px] bg-white border-[1.5px] border-slate-200 rounded-lg pl-[38px] pr-3 text-sm text-slate-900 outline-none focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)]">
    </div>

    <!-- SECTION HEAD -->
    <div class="flex items-baseline gap-2.5 mb-5">
        <h2 class="text-lg font-bold text-slate-900">Semua Angkatan</h2>
        <span class="font-mono text-[13px] text-slate-500" id="sectionCount">{{ $totalBatches }} angkatan</span>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-[repeat(auto-fill,minmax(300px,1fr))] gap-5 max-[768px]:grid-cols-2 max-[640px]:grid-cols-1" id="cardGrid"></div>
</main>

@php
$batchesJson = $batches->map(function($b) {
    $statusMap = [
        'draft'     => ['dot' => '#94a3b8', 'label' => 'Draft'],
        'generated' => ['dot' => '#22c55e', 'label' => 'Terjadwal'],
        'active'    => ['dot' => '#0369a1', 'label' => 'Aktif'],
        'completed' => ['dot' => '#64748b', 'label' => 'Selesai'],
    ];
    $st = $statusMap[$b->status] ?? ['dot' => '#94a3b8', 'label' => $b->status];
    return [
        'id'             => $b->id,
        'name'           => $b->name,
        'templateName'   => $b->template?->name ?? '—',
        'unit'           => $b->organizationalUnit?->name ?? '',
        'unitId'         => $b->organizational_unit_id,
        'status'         => $b->status,
        'statusDot'      => $st['dot'],
        'statusLabel'    => $st['label'],
        'year'           => $b->year,
        'batchNumber'    => $b->batch_number,
        'participants'   => $b->participant_count ?? 0,
        'schedulesCount' => $b->schedules_count ?? 0,
        'startDate'      => $b->start_date?->format('d M Y') ?? '—',
        'endDate'        => $b->end_date?->format('d M Y') ?? '—',
    ];
});
@endphp

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

const batches = @json($batchesJson);

let activeFilter = 'all', searchQuery = '', sortBy = 'terbaru';

const COLORS = ['#1D4ED8','#059669','#7C3AED','#EA580C','#0369A1','#D97706','#DC2626','#0891B2','#65A30D','#9333EA'];

function renderCard(b) {
    const color = COLORS[b.id % COLORS.length];
    const initials = b.name.split(' ').slice(0,2).map(w=>w[0]).join('').toUpperCase().slice(0,2);
    const hasDates = b.startDate !== '—';
    const icon = `<div style="width:62px;height:62px;border-radius:50%;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff;font-family:'Fira Code',monospace;letter-spacing:-1px;">${initials}</div>`;

    return `<article class="card" data-id="${b.id}" data-status="${b.status}" data-unit="${b.unitId || ''}" data-name="${b.name.toLowerCase()} ${b.templateName.toLowerCase()}">
        <div class="card-header" style="background:linear-gradient(135deg,${color}ee 0%,${color}99 100%)">
            <div class="card-bg"><div class="card-bg-icon"><svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2" opacity=".18"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div></div>
            ${icon}
            <span class="card-category-badge">${b.unit || 'Umum'}</span>
            <span class="card-status"><span class="status-dot" style="background:${b.statusDot}"></span>${b.statusLabel}</span>
        </div>
        <div class="card-body">
            <h3 class="card-title">${b.name}</h3>
            <div class="card-meta">
                <div class="meta-item"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>${b.templateName}</div>
                ${hasDates
                    ? `<div class="meta-item"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>${b.startDate} – ${b.endDate}</div>`
                    : `<div class="meta-item" style="color:#f59e0b;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Jadwal belum di-generate</div>`
                }
                <div class="meta-item"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>${b.participants} peserta · ${b.schedulesCount} aktivitas</div>
            </div>
        </div>
        <div class="card-footer">
            <span class="font-mono text-[11px] text-slate-400">Angkatan ${b.batchNumber} · ${b.year}</span>
            <div class="flex gap-1.5">
                <a href="/admin/batches/${b.id}" class="btn-detail btn-manage">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Kelola
                </a>
                <a href="/admin/calendar?batch=${b.id}" class="btn-detail available">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Kalender
                </a>
            </div>
        </div>
    </article>`;
}

function filtered() {
    let list = [...batches];
    if (activeFilter !== 'all') {
        if (activeFilter.startsWith('status:')) {
            const st = activeFilter.slice(7);
            list = list.filter(b => st === 'generated' ? ['generated','active'].includes(b.status) : b.status === st);
        } else if (activeFilter.startsWith('unit:')) {
            const uid = parseInt(activeFilter.slice(5));
            list = list.filter(b => b.unitId === uid);
        }
    }
    if (searchQuery) {
        const q = searchQuery.toLowerCase();
        list = list.filter(b => (b.name + ' ' + b.templateName).toLowerCase().includes(q));
    }
    if (sortBy === 'nama')  list.sort((a,b) => a.name.localeCompare(b.name));
    if (sortBy === 'tahun') list.sort((a,b) => (b.year - a.year) || (a.batchNumber - b.batchNumber));
    return list;
}

function mountGrid(doAnimate=true) {
    const grid = document.getElementById('cardGrid');
    const list = filtered();
    document.getElementById('sectionCount').textContent = `${list.length} angkatan`;
    if (!list.length) {
        grid.innerHTML = `<div class="empty"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg><h3>Tidak ada angkatan ditemukan</h3><p>Coba filter atau kata kunci lain.</p></div>`;
        return;
    }
    grid.innerHTML = list.map(renderCard).join('');
    if (doAnimate) animate('.card', { opacity:[0,1], translateY:[20,0], scale:[.97,1], duration:420, ease:'outExpo', delay:stagger(55,{start:60}) });
}

// Hero entrance
const tl = createTimeline({ defaults:{ease:'outExpo'} });
tl.add('#hero .hero-badge', { opacity:[0,1], translateY:[-8,0], duration:480 }, 80)
  .add('#hero h1',          { opacity:[0,1], translateY:[-12,0], duration:560 }, 150)
  .add('#hero p',           { opacity:[0,1], translateY:[-8,0],  duration:460 }, 240)
  .add('#hero .stat-card',  { opacity:[0,1], translateY:[16,0],  duration:400, delay:stagger(70) }, 330);

document.getElementById('chips').addEventListener('click', e => {
    const chip = e.target.closest('.chip'); if(!chip) return;
    document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));
    chip.classList.add('active');
    activeFilter = chip.dataset.filter;
    animate(chip, { scale:[.94,1], duration:180, ease:'outBack' });
    mountGrid();
});

let st;
document.getElementById('searchInput').addEventListener('input', e => {
    clearTimeout(st); st = setTimeout(() => { searchQuery = e.target.value; mountGrid(); }, 260);
});

document.getElementById('sortSelect').addEventListener('change', e => { sortBy = e.target.value; mountGrid(); });

window.addEventListener('scroll', () => {
    document.getElementById('navbar').style.boxShadow = window.scrollY>10 ? '0 2px 20px rgba(15,23,42,.10)' : 'none';
}, { passive:true });

mountGrid(true);
</script>
</body>
</html>
