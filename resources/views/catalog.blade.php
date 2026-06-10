<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Pelatihan — BPSDM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Fira+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .chip { display:inline-flex; align-items:center; gap:5px; height:32px; padding:0 14px; border-radius:100px; font-size:12px; font-weight:500; font-family:inherit; border:1.5px solid #e2e8f0; background:#fff; color:#64748b; cursor:pointer; transition:all 150ms; white-space:nowrap; }
        .chip:hover { border-color:#94a3b8; color:#334155; }
        .chip.active { background:#0369a1; border-color:#0369a1; color:#fff; }
        .chip-count { font-family:'Fira Code',monospace; font-size:10px; opacity:.75; background:none; padding:0; border-radius:0; }
        .chip:not(.active) .chip-count { background:none; color:inherit; }
        .no-scrollbar::-webkit-scrollbar { display:none; }
        .no-scrollbar { -ms-overflow-style:none; scrollbar-width:none; }
        .select-custom { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748B' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen text-base leading-[1.6]">

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-white/[0.92] backdrop-blur-md border-b border-slate-200 px-6 h-16 flex items-center gap-6 max-[640px]:px-4 max-[640px]:gap-3 max-[400px]:px-3" id="navbar">
    <a href="#" class="flex items-center gap-2.5 no-underline shrink-0">
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
            <div class="text-[11px] text-slate-500 font-normal max-[640px]:hidden">Sistem Pelatihan ASN</div>
        </div>
    </a>

    <div class="flex-1 max-w-[400px] relative max-[900px]:max-w-60 max-[640px]:hidden">
        <svg class="absolute left-[11px] top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" id="searchInput" placeholder="Cari pelatihan..." autocomplete="off"
            class="w-full h-[38px] bg-slate-50 border-[1.5px] border-slate-200 rounded-lg pl-[38px] pr-3 text-sm font-sans text-slate-950 outline-none transition-[border-color,box-shadow] duration-200 focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)]">
    </div>

    <nav class="flex items-center gap-1" aria-label="Menu utama">
        <a href="/catalog" class="nav-link active" aria-current="page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="max-[400px]:hidden">Katalog</span>
        </a>
        <a href="/calendar" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span class="max-[400px]:hidden">Kalender</span>
        </a>
    </nav>

    <a href="/" class="ml-auto inline-flex items-center gap-1.5 h-[34px] px-3.5 bg-sky-700 text-white rounded-lg text-[12px] font-semibold no-underline shrink-0 transition-all duration-150 hover:bg-sky-600 hover:shadow-[0_4px_12px_rgba(3,105,161,.3)]">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <span class="max-[480px]:hidden">Masuk Admin</span>
    </a>
</nav>

<!-- HERO -->
<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-10 pb-12 relative overflow-hidden max-[768px]:px-5 max-[768px]:pt-7 max-[768px]:pb-9 max-[640px]:px-4 max-[640px]:pt-6 max-[640px]:pb-[30px]" id="hero">
    <div class="max-w-[1200px] mx-auto relative">
        <div class="hero-badge inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] font-medium text-white/90 mb-4">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
            Pendaftaran Pelatihan 2026 Dibuka
        </div>
        <h1 class="text-[clamp(24px,4vw,36px)] font-bold text-white leading-tight mb-2.5 max-[640px]:text-[22px]">Katalog Pelatihan ASN</h1>
        <p class="text-[15px] text-white/[0.72] max-w-[520px] mb-8 max-[640px]:text-[13px] max-[640px]:mb-5">Temukan program pelatihan yang sesuai dengan kebutuhan pengembangan kompetensi Anda sebagai aparatur sipil negara.</p>
        <div class="flex gap-6 flex-wrap max-[640px]:gap-2.5" id="statsRow">
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[120px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[768px]:text-xl max-[640px]:text-[18px]" id="statTotal">0</div>
                <div class="text-xs text-white/[0.65] mt-1 max-[640px]:text-[11px]">Total Pelatihan</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[120px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[768px]:text-xl max-[640px]:text-[18px]" id="statOpen">0</div>
                <div class="text-xs text-white/[0.65] mt-1 max-[640px]:text-[11px]">Dibuka</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[120px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[768px]:text-xl max-[640px]:text-[18px]" id="statBatch">0</div>
                <div class="text-xs text-white/[0.65] mt-1 max-[640px]:text-[11px]">Angkatan</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[120px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[768px]:text-xl max-[640px]:text-[18px]" id="statSeats">0</div>
                <div class="text-xs text-white/[0.65] mt-1 max-[640px]:text-[11px]">Kuota Tersedia</div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN -->
<main class="max-w-[1200px] mx-auto px-6 pt-8 pb-16 max-[640px]:px-4 max-[640px]:pt-5 max-[640px]:pb-12">
    <!-- FILTER BAR -->
    <div class="flex items-center gap-3 mb-6 flex-wrap no-scrollbar max-[640px]:gap-2 max-[640px]:flex-nowrap max-[640px]:overflow-x-auto max-[640px]:pb-1">
        <span class="text-[13px] font-medium text-slate-500 shrink-0">Filter:</span>
        <div class="chips no-scrollbar flex gap-2 flex-wrap max-[640px]:flex-nowrap max-[640px]:overflow-x-auto" id="chips">
            <button class="chip active" data-filter="all">Semua <span class="chip-count">{{ count($trainings) }}</span></button>
            @foreach($units as $unit)
            <button class="chip" data-filter="unit:{{ $unit->id }}">{{ $unit->name }} <span class="chip-count">{{ $trainings->where('unitId', $unit->id)->count() }}</span></button>
            @endforeach
        </div>
        <div class="ml-auto flex items-center gap-2 shrink-0">
            <select id="sortSelect" aria-label="Urutkan"
                class="select-custom h-[34px] pl-3 pr-8 border-[1.5px] border-slate-200 rounded-lg text-[13px] text-slate-700 bg-white cursor-pointer outline-none focus:border-sky-700 max-[640px]:hidden">
                <option value="terbaru">Terbaru</option>
                <option value="nama">Nama A-Z</option>
                <option value="kuota">Kuota Tersedia</option>
            </select>
        </div>
    </div>

    <!-- SECTION HEAD -->
    <div class="flex items-baseline gap-2.5 mb-5">
        <h2 class="text-lg font-bold text-slate-900">Semua Pelatihan</h2>
        <span class="font-mono text-[13px] text-slate-500" id="sectionCount">12 program</span>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-[repeat(auto-fill,minmax(300px,1fr))] gap-5 max-[768px]:grid-cols-2 max-[640px]:grid-cols-1" id="cardGrid"></div>
</main>

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

// ── DATA (real DB) ────────────────────────────────────────────────────
const trainings = {!! json_encode($trainings) !!};

const CATEGORY_ICONS = {
    kepemimpinan: `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
    teknis:       `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>`,
    fungsional:   `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>`,
    prajabatan:   `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>`,
    manajemen:    `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`,
};

// ── STATE ─────────────────────────────────────────────────────────────
let activeFilter = 'all';
let searchQuery  = '';
let sortBy       = 'terbaru';

// ── RENDER CARD ───────────────────────────────────────────────────────
function seatsColor(pct) {
    if (pct >= .9) return '#EF4444';
    if (pct >= .6) return '#F59E0B';
    return '#22C55E';
}

function renderCard(t) {
    const pct   = t.seats.filled / t.seats.total;
    const avail = t.seats.total - t.seats.filled;
    const color = seatsColor(pct);

    const statusMap = {
        open:     { dot: '#22C55E', label: 'Dibuka' },
        full:     { dot: '#EF4444', label: 'Penuh' },
        waitlist: { dot: '#F59E0B', label: 'Antrian' }
    };
    const st = statusMap[t.status];

    const detailBtn = `<a href="/calendar?id=${t.id}" class="btn-daftar available"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>Detail</a>`;
    const btnMap = { open: detailBtn, full: detailBtn, waitlist: detailBtn };

    return `
    <article class="card theme-${t.category}" data-category="${t.category}" data-id="${t.id}">
        <div class="card-header">
            <div class="card-bg">
                <div class="card-bg-icon">${CATEGORY_ICONS[t.category] || CATEGORY_ICONS.teknis}</div>
            </div>
            <span class="card-category-badge">${t.category}</span>
            <span class="card-status">
                <span class="status-dot" style="background:${st.dot}"></span>
                ${st.label}
            </span>
        </div>
        <div class="card-body">
            <h3 class="card-title">${t.title}</h3>
            <div class="card-meta">
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    ${t.date}
                </div>
                <div class="meta-item">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                    ${t.location}
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="seats-info">
                <div class="seats-bar-wrap">
                    <div class="seats-bar" style="width:0%;background:${color}" data-target="${Math.round(pct*100)}"></div>
                </div>
                <span class="seats-text">${avail} sisa</span>
            </div>
            ${btnMap[t.status]}
        </div>
    </article>`;
}

// ── FILTER + SEARCH ───────────────────────────────────────────────────
function filtered() {
    let list = trainings.filter(t => {
        const matchCat    = activeFilter === 'all' ||
                            (activeFilter.startsWith('unit:') && t.unitId === parseInt(activeFilter.slice(5)));
        const matchSearch = t.title.toLowerCase().includes(searchQuery.toLowerCase());
        return matchCat && matchSearch;
    });
    if (sortBy === 'nama')  list.sort((a,b) => a.title.localeCompare(b.title));
    if (sortBy === 'kuota') list.sort((a,b) => (b.seats.total - b.seats.filled) - (a.seats.total - a.seats.filled));
    return list;
}

// ── MOUNT GRID ────────────────────────────────────────────────────────
function mountGrid(doAnimate = true) {
    const grid  = document.getElementById('cardGrid');
    const list  = filtered();
    const count = document.getElementById('sectionCount');
    count.textContent = `${list.length} program`;

    if (list.length === 0) {
        grid.innerHTML = `<div class="empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <h3>Tidak ada pelatihan ditemukan</h3>
            <p>Coba kata kunci atau filter lain.</p>
        </div>`;
        return;
    }

    grid.innerHTML = list.map(renderCard).join('');

    // Seats bar fill animation
    setTimeout(() => {
        grid.querySelectorAll('.seats-bar').forEach(bar => {
            animate(bar, { width: bar.dataset.target + '%', duration: 800, ease: 'outExpo', delay: 200 });
        });
    }, 100);

    if (!doAnimate) return;

    animate('.card', {
        opacity:    [0, 1],
        translateY: [24, 0],
        scale:      [.97, 1],
        duration:   480,
        ease:       'outExpo',
        delay:      stagger(60, { start: 80 })
    });
}

// ── STATS COUNTER ─────────────────────────────────────────────────────
function animateStats() {
    const openCount  = trainings.filter(t => t.status === 'open').length;
    const totalSeats = trainings.reduce((s, t) => s + (t.seats.total - t.seats.filled), 0);

    const counter = (el, to) => {
        if (!el) return;
        const obj = { val: 0 };
        animate(obj, {
            val: to, duration: 1200, ease: 'outExpo',
            onUpdate: () => { el.textContent = Math.round(obj.val); }
        });
    };

    counter(document.getElementById('statTotal'), trainings.length);
    counter(document.getElementById('statOpen'),  openCount);
    counter(document.getElementById('statBatch'), trainings.length);
    counter(document.getElementById('statSeats'), totalSeats);
}

// ── CHIP COUNTS (dynamic) ──────────────────────────────────────────────
function updateChipCounts() {
    document.querySelectorAll('.chip').forEach(chip => {
        const f     = chip.dataset.filter;
        const count = f === 'all' ? trainings.length
                    : trainings.filter(t => t.unitId === parseInt(f.slice(5))).length;
        const el = chip.querySelector('.chip-count');
        if (el) el.textContent = count;
    });
}

// ── CHIP FILTER ───────────────────────────────────────────────────────
document.getElementById('chips').addEventListener('click', e => {
    const chip = e.target.closest('.chip');
    if (!chip) return;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    activeFilter = chip.dataset.filter;
    animate(chip, { scale: [.95, 1], duration: 200, ease: 'outBack' });
    mountGrid();
});

// ── SEARCH ────────────────────────────────────────────────────────────
let searchTimer;
document.getElementById('searchInput').addEventListener('input', e => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => { searchQuery = e.target.value; mountGrid(); }, 280);
});

// ── SORT ──────────────────────────────────────────────────────────────
document.getElementById('sortSelect').addEventListener('change', e => {
    sortBy = e.target.value; mountGrid();
});

// ── LINK HOVER FEEDBACK ───────────────────────────────────────────────
document.getElementById('cardGrid').addEventListener('mousedown', e => {
    const btn = e.target.closest('.btn-daftar');
    if (!btn) return;
    animate(btn, { scale: [1, .94, 1], duration: 200, ease: 'outBack' });
});

// ── NAVBAR SHADOW ON SCROLL ───────────────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('navbar').style.boxShadow =
        window.scrollY > 10 ? '0 2px 20px rgba(15,23,42,.10)' : 'none';
}, { passive: true });

// ── HERO ENTRANCE TIMELINE ────────────────────────────────────────────
const tl = createTimeline({ defaults: { ease: 'outExpo' } });
tl.add('#hero .hero-badge',  { opacity: [0,1], translateY: [-10,0], duration: 500 }, 100)
  .add('#hero h1',           { opacity: [0,1], translateY: [-16,0], duration: 600 }, 180)
  .add('#hero p',            { opacity: [0,1], translateY: [-10,0], duration: 500 }, 280)
  .add('#hero .stat-card',   { opacity: [0,1], translateY: [20,0],  duration: 450, delay: stagger(80) }, 380);

// ── INIT ──────────────────────────────────────────────────────────────
updateChipCounts();
mountGrid(true);
animateStats();
</script>
</body>
</html>
