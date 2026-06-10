<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Pelatihan — BPSDM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen text-base leading-[1.6]">

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-white/[0.92] backdrop-blur-md border-b border-slate-200 px-6 h-16 flex items-center gap-5 max-[640px]:px-4 max-[640px]:gap-3 max-[480px]:px-3" id="navbar">
    <a href="/" class="flex items-center gap-2.5 no-underline shrink-0">
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

    <nav class="flex items-center gap-1" aria-label="Menu utama">
        <a href="/catalog" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="max-[480px]:hidden">Katalog</span>
        </a>
        <a href="/calendar" class="nav-link active" aria-current="page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span class="max-[480px]:hidden">Kalender</span>
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

<!-- LAYOUT -->
<div class="flex h-[calc(100vh-64px)] overflow-hidden">
    <!-- SIDEBAR -->
    <aside class="w-[300px] shrink-0 bg-white border-r border-slate-200 flex flex-col overflow-hidden max-[768px]:hidden" id="sidebar">
        <div class="px-5 pt-5 pb-4 border-b border-slate-200 shrink-0">
            <div class="text-[13px] font-semibold text-slate-500 uppercase tracking-[0.6px] mb-3" id="sidebarTitle">Pelatihan</div>
            <div class="flex bg-slate-50 rounded-lg p-[3px] gap-0.5" id="viewToggle">
                <button class="vt-btn flex-1 h-[30px] border-none rounded-md text-xs font-medium font-sans cursor-pointer transition-all duration-[180ms] text-slate-500 bg-transparent [&.active]:bg-white [&.active]:text-slate-900 [&.active]:shadow-[0_1px_4px_rgba(15,23,42,.1)] active" data-mode="all">Semua</button>
                <button class="vt-btn flex-1 h-[30px] border-none rounded-md text-xs font-medium font-sans cursor-pointer transition-all duration-[180ms] text-slate-500 bg-transparent [&.active]:bg-white [&.active]:text-slate-900 [&.active]:shadow-[0_1px_4px_rgba(15,23,42,.1)]" data-mode="detail" id="detailModeBtn">Detail</button>
            </div>
        </div>
        <div class="thin-scroll flex-1 overflow-y-auto p-4" id="sidebarBody"></div>
    </aside>

    <!-- CALENDAR -->
    <div class="cal-main flex-1 flex flex-col overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-200 shrink-0 bg-white max-[768px]:px-4 max-[768px]:py-2.5 max-[768px]:gap-2 max-[480px]:px-3 max-[480px]:py-2 max-[480px]:gap-1.5">
            <div class="flex gap-1">
                <button class="w-[34px] h-[34px] border-[1.5px] border-slate-200 rounded-lg bg-white flex items-center justify-center cursor-pointer text-slate-700 transition-all duration-150 hover:border-sky-700 hover:text-sky-700 hover:bg-sky-100 max-[480px]:w-[30px] max-[480px]:h-[30px]" id="prevBtn" aria-label="Bulan sebelumnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="w-[34px] h-[34px] border-[1.5px] border-slate-200 rounded-lg bg-white flex items-center justify-center cursor-pointer text-slate-700 transition-all duration-150 hover:border-sky-700 hover:text-sky-700 hover:bg-sky-100 max-[480px]:w-[30px] max-[480px]:h-[30px]" id="nextBtn" aria-label="Bulan berikutnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
            <div class="font-mono text-lg font-semibold text-slate-900 min-w-[200px] max-[768px]:text-[15px] max-[768px]:min-w-0 max-[480px]:text-[13px]" id="monthLabel"></div>
            <button class="h-[34px] px-3.5 border-[1.5px] border-slate-200 rounded-lg bg-white text-[13px] font-medium font-sans text-slate-700 cursor-pointer transition-all duration-150 hover:border-sky-700 hover:text-sky-700 hover:bg-sky-100 max-[480px]:hidden" id="todayBtn">Hari ini</button>
            <div class="ml-auto flex bg-slate-50 rounded-lg p-[3px] gap-0.5 max-[768px]:hidden">
                <button class="h-7 px-3 border-none rounded-md text-xs font-medium font-sans cursor-pointer transition-all duration-[180ms] bg-white text-slate-900 shadow-[0_1px_4px_rgba(15,23,42,.1)]" data-view="month">Bulan</button>
            </div>
        </div>

        <div class="flex-1 overflow-hidden relative">
            <div class="h-full flex flex-col" id="calGrid">
                <div class="grid grid-cols-7 bg-white border-b border-slate-200 shrink-0" id="daysHeader"></div>
                <div class="flex-1 flex flex-col overflow-hidden" id="calWeeks"></div>
            </div>
        </div>
    </div>
</div>

<!-- TOOLTIP -->
<div class="tooltip" id="tooltip">
    <div class="tooltip-title" id="ttTitle"></div>
    <div class="tooltip-row" id="ttDate"></div>
    <div class="tooltip-row" id="ttLoc"></div>
</div>

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

// ── DATA (shared with catalog) ────────────────────────────────────────
const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAYS_ID   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

const CATEGORY_COLORS = {
    kepemimpinan: { bg: '#1e3a5f', text: '#93C5FD', event: '#1D4ED8', eventText: '#EFF6FF' },
    teknis:       { bg: '#064E3B', text: '#6EE7B7', event: '#059669', eventText: '#ECFDF5' },
    fungsional:   { bg: '#3B0764', text: '#C4B5FD', event: '#7C3AED', eventText: '#F5F3FF' },
    prajabatan:   { bg: '#7C2D12', text: '#FED7AA', event: '#EA580C', eventText: '#FFF7ED' },
    manajemen:    { bg: '#0C4A6E', text: '#BAE6FD', event: '#0369A1', eventText: '#F0F9FF' },
};

const trainings = {!! json_encode($trainings) !!};

// ── STATE ─────────────────────────────────────────────────────────────
const params     = new URLSearchParams(location.search);
const detailId   = params.get('id') ? parseInt(params.get('id')) : null;
let   mode       = detailId ? 'detail' : 'all';           // 'all' | 'detail'
let   visibleIds = new Set(detailId ? [detailId] : trainings.map(t => t.id));

const today = new Date();
let   curYear  = today.getFullYear();
let   curMonth = today.getMonth();

// Navigate to first training month if in detail mode
if (detailId) {
    const t = trainings.find(x => x.id === detailId);
    if (t) {
        const d = new Date(t.start);
        curYear  = d.getFullYear();
        curMonth = d.getMonth();
    }
}

// ── SIDEBAR ───────────────────────────────────────────────────────────
function buildSidebar() {
    const body  = document.getElementById('sidebarBody');
    const title = document.getElementById('sidebarTitle');
    const dBtn  = document.getElementById('detailModeBtn');

    if (mode === 'detail' && detailId) {
        const t   = trainings.find(x => x.id === detailId);
        const c   = CATEGORY_COLORS[t.category];
        const pct = t.seats.filled / t.seats.total;
        const avail = t.seats.total - t.seats.filled;
        const seatsColor = pct >= .9 ? '#EF4444' : pct >= .6 ? '#F59E0B' : '#22C55E';

        title.textContent = 'Detail Pelatihan';
        dBtn.disabled     = false;

        body.innerHTML = `
        <div class="detail-card">
            <div class="detail-header" style="background:linear-gradient(135deg,${c.bg} 0%,${c.event} 100%)">
                <div class="detail-header-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span class="detail-cat-badge" style="background:rgba(0,0,0,.45);color:${c.text}">${t.category}</span>
            </div>
            <div class="detail-body">
                <div class="detail-name">${t.title}</div>
                <div class="detail-meta">
                    <div class="detail-meta-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>${formatDateRange(t.start, t.end)}</span>
                    </div>
                    <div class="detail-meta-row">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>${t.location}</span>
                    </div>
                </div>
                <div class="detail-seats">
                    <div class="seats-label">
                        <span>Kuota terisi</span>
                        <span>${t.seats.filled}/${t.seats.total} (${avail} sisa)</span>
                    </div>
                    <div class="seats-bar-wrap">
                        <div class="seats-bar" id="detailSeatsBar" style="width:0%;background:${seatsColor}" data-target="${Math.round(pct*100)}"></div>
                    </div>
                </div>
            </div>
        </div>
        <a href="/catalog" style="display:flex;align-items:center;gap:6px;font-size:12px;color:#0369a1;text-decoration:none;padding:4px 2px;margin-top:4px;cursor:pointer;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Katalog
        </a>`;

        setTimeout(() => {
            const bar = document.getElementById('detailSeatsBar');
            if (bar) animate(bar, { width: bar.dataset.target + '%', duration: 900, ease: 'outExpo', delay: 300 });
        }, 100);

    } else {
        title.textContent = 'Semua Pelatihan';

        body.innerHTML = `<div class="training-list" id="trainingList">${
            trainings.map(t => {
                const c     = CATEGORY_COLORS[t.category];
                const isVis = visibleIds.has(t.id);
                return `<div class="tl-item${isVis ? ' active' : ''}" data-id="${t.id}" role="button" tabindex="0" aria-pressed="${isVis}">
                    <div class="tl-dot" style="background:${c.event}"></div>
                    <div class="tl-info">
                        <div class="tl-name">${t.title}</div>
                        <div class="tl-date">${shortDateRange(t.start, t.end)}</div>
                    </div>
                    <div class="tl-check">
                        ${isVis ? `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>` : ''}
                    </div>
                </div>`;
            }).join('')
        }</div>`;

        document.getElementById('trainingList').addEventListener('click', e => {
            const item = e.target.closest('.tl-item');
            if (!item) return;
            const id = parseInt(item.dataset.id);
            if (visibleIds.has(id)) { visibleIds.delete(id); }
            else                    { visibleIds.add(id);    }
            buildSidebar();
            renderCalendar();
            animate(item, { scale: [.97, 1], duration: 180, ease: 'outBack' });
        });
    }
}

// ── DATE HELPERS ──────────────────────────────────────────────────────
function formatDateRange(start, end) {
    const s = new Date(start), e = new Date(end);
    const sm = MONTHS_ID[s.getMonth()], em = MONTHS_ID[e.getMonth()];
    if (sm === em) return `${s.getDate()} – ${e.getDate()} ${sm} ${s.getFullYear()}`;
    return `${s.getDate()} ${sm} – ${e.getDate()} ${em} ${e.getFullYear()}`;
}

function shortDateRange(start, end) {
    const s = new Date(start), e = new Date(end);
    return `${s.getDate()} ${MONTHS_ID[s.getMonth()].slice(0,3)} – ${e.getDate()} ${MONTHS_ID[e.getMonth()].slice(0,3)} ${e.getFullYear()}`;
}

// ── CALENDAR RENDER ───────────────────────────────────────────────────
function renderCalendar(animDir = 0) {
    document.getElementById('monthLabel').textContent =
        `${MONTHS_ID[curMonth]} ${curYear}`;

    // Build days header
    const header = document.getElementById('daysHeader');
    header.innerHTML = DAYS_ID.map((d,i) =>
        `<div class="cal-day-label${i===0||i===6?' weekend':''}">${d}</div>`
    ).join('');

    // First day of month
    const firstDay  = new Date(curYear, curMonth, 1);
    const startDow  = firstDay.getDay(); // 0=Sun
    const daysInMon = new Date(curYear, curMonth + 1, 0).getDate();

    // Build 6-week grid (42 cells)
    const cells = [];
    // Days from prev month
    const prevDays = new Date(curYear, curMonth, 0).getDate();
    for (let i = startDow - 1; i >= 0; i--) {
        cells.push({ day: prevDays - i, month: curMonth - 1, year: curYear, other: true });
    }
    // Current month
    for (let d = 1; d <= daysInMon; d++) {
        cells.push({ day: d, month: curMonth, year: curYear, other: false });
    }
    // Next month fill
    while (cells.length % 7 !== 0) {
        cells.push({ day: cells.length - daysInMon - startDow + 1, month: curMonth + 1, year: curYear, other: true });
    }

    // Map trainings to date ranges for quick lookup
    const eventsOnDay = {};  // key = "YYYY-MM-DD" → [{training, position}]

    // For each visible training, compute which cells it spans
    const visibleTrainings = trainings.filter(t => visibleIds.has(t.id));

    // Assign row positions to avoid overlap
    const positions = {};
    visibleTrainings.forEach((t, ti) => { positions[t.id] = ti % 3; }); // max 3 rows

    visibleTrainings.forEach(t => {
        const s   = new Date(t.start + 'T00:00:00');
        const e   = new Date(t.end   + 'T00:00:00');
        const pos = positions[t.id];
        let cur   = new Date(s);
        while (cur <= e) {
            const key = dateKey(cur);
            if (!eventsOnDay[key]) eventsOnDay[key] = [];
            const isStart  = cur.getTime() === s.getTime();
            const isEnd    = cur.getTime() === e.getTime();
            const isSingle = isStart && isEnd;
            const isWkStart = cur.getDay() === 0 || isStart;
            const isWkEnd   = cur.getDay() === 6 || isEnd;
            eventsOnDay[key].push({ t, pos,
                segment: isSingle ? 'single' : isWkStart ? 'start' : isWkEnd ? 'end' : 'mid',
                showLabel: isStart || cur.getDay() === 0
            });
            cur = new Date(cur.getTime() + 86400000);
        }
    });

    // Render weeks
    const weeksEl = document.getElementById('calWeeks');
    const weeks = [];
    for (let w = 0; w < cells.length / 7; w++) {
        weeks.push(cells.slice(w * 7, w * 7 + 7));
    }

    weeksEl.innerHTML = weeks.map(week => `
    <div class="cal-week">
        ${week.map(cell => {
            const d     = new Date(cell.year, cell.month, cell.day);
            const key   = dateKey(d);
            const isToday = cell.day === today.getDate() && cell.month === today.getMonth() && cell.year === today.getFullYear() && !cell.other;
            const isWknd  = d.getDay() === 0 || d.getDay() === 6;
            const evs     = eventsOnDay[key] || [];

            // Sort by position
            const slots = Array(4).fill(null);
            evs.forEach(ev => { if (ev.pos <= 3) slots[ev.pos] = ev; });

            const evHtml = slots.map((ev, slotIdx) => {
                if (!ev) {
                    return slotIdx < 3 ? `<div style="height:18px;flex-shrink:0"></div>` : '';
                }
                const c    = CATEGORY_COLORS[ev.t.category];
                const dim  = mode === 'detail' ? '' : '';
                const lbl  = ev.showLabel ? ev.t.title : '';
                return `<div class="event-bar ${ev.segment}${dim?(' dim'):''}"
                    style="background:${c.event};color:${c.eventText}"
                    data-id="${ev.t.id}"
                    title="${ev.t.title}">${lbl}</div>`;
            }).join('');

            const extraCount = evs.filter(ev => ev.pos >= 3).length;

            return `<div class="cal-cell${cell.other?' other-month':''}${isToday?' today':''}${isWknd?' weekend':''}" data-date="${key}">
                <div class="cell-num">${cell.day}</div>
                <div class="cell-events">
                    ${evHtml}
                    ${extraCount > 0 ? `<div class="more-events">+${extraCount} lagi</div>` : ''}
                </div>
            </div>`;
        }).join('')}
    </div>`).join('');

    // Animate calendar entrance
    if (animDir !== 0) {
        animate('.cal-week', {
            opacity:    [0, 1],
            translateX: [animDir * 30, 0],
            duration:   320,
            ease:       'outExpo',
            delay:      stagger(30)
        });
    } else {
        animate('.cal-week', {
            opacity:    [0, 1],
            translateY: [10, 0],
            duration:   350,
            ease:       'outExpo',
            delay:      stagger(30)
        });
    }

    // Event bar tooltips
    const tooltip = document.getElementById('tooltip');
    weeksEl.querySelectorAll('.event-bar').forEach(bar => {
        bar.addEventListener('mouseenter', e => {
            const t = trainings.find(x => x.id === parseInt(bar.dataset.id));
            if (!t) return;
            document.getElementById('ttTitle').textContent = t.title;
            document.getElementById('ttDate').innerHTML  = `<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> ${shortDateRange(t.start, t.end)}`;
            document.getElementById('ttLoc').innerHTML   = `<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> ${t.location}`;
            tooltip.style.opacity = '1';
            posTooltip(e);
        });
        bar.addEventListener('mousemove', posTooltip);
        bar.addEventListener('mouseleave', () => { tooltip.style.opacity = '0'; });
    });
}

function posTooltip(e) {
    const tt = document.getElementById('tooltip');
    let x = e.clientX + 14, y = e.clientY - 10;
    if (x + 240 > window.innerWidth) x = e.clientX - 240;
    if (y + 100 > window.innerHeight) y = e.clientY - 100;
    tt.style.left = x + 'px'; tt.style.top = y + 'px';
}

function dateKey(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

// ── VIEW TOGGLE ───────────────────────────────────────────────────────
document.getElementById('viewToggle').addEventListener('click', e => {
    const btn = e.target.closest('.vt-btn');
    if (!btn || btn.disabled) return;
    document.querySelectorAll('.vt-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    mode = btn.dataset.mode;
    if (mode === 'detail' && detailId) {
        visibleIds = new Set([detailId]);
    } else {
        mode = 'all';
        visibleIds = new Set(trainings.map(t => t.id));
    }
    buildSidebar();
    renderCalendar();
    animate(btn, { scale: [.94, 1], duration: 180, ease: 'outBack' });
});

// ── NAV ───────────────────────────────────────────────────────────────
document.getElementById('prevBtn').addEventListener('click', () => {
    curMonth--;
    if (curMonth < 0) { curMonth = 11; curYear--; }
    renderCalendar(-1);
    animate('#prevBtn', { scale: [.88, 1], duration: 200, ease: 'outBack' });
});

document.getElementById('nextBtn').addEventListener('click', () => {
    curMonth++;
    if (curMonth > 11) { curMonth = 0; curYear++; }
    renderCalendar(1);
    animate('#nextBtn', { scale: [.88, 1], duration: 200, ease: 'outBack' });
});

document.getElementById('todayBtn').addEventListener('click', () => {
    curYear  = today.getFullYear();
    curMonth = today.getMonth();
    renderCalendar(0);
    animate('#todayBtn', { scale: [.94, 1], duration: 180, ease: 'outBack' });
});

// ── INIT ──────────────────────────────────────────────────────────────
if (detailId) {
    document.querySelectorAll('.vt-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.mode === 'detail');
    });
}

buildSidebar();
renderCalendar(0);

// Navbar shadow on scroll
document.querySelector('.cal-main').addEventListener('scroll', () => {
    document.getElementById('navbar').style.boxShadow =
        document.querySelector('.cal-main').scrollTop > 10 ? '0 2px 20px rgba(15,23,42,.10)' : 'none';
}, { passive: true });
</script>
</body>
</html>
