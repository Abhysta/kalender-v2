<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Pelatihan — Admin BPSDM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;500;600;700&family=Fira+Code:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root {
            --bg:#F8FAFC; --surface:#FFFFFF; --primary:#0F172A; --secondary:#334155;
            --cta:#0369A1; --cta-light:#E0F2FE; --text:#020617; --muted:#64748B;
            --border:#E2E8F0; --sidebar:280px;
        }
        body { font-family:'Fira Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; font-size:16px; line-height:1.6; }
        .navbar { position:sticky; top:0; z-index:50; background:rgba(255,255,255,.92); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border-bottom:1px solid var(--border); padding:0 24px; height:64px; display:flex; align-items:center; gap:20px; }
        .navbar-brand { display:flex; align-items:center; gap:10px; text-decoration:none; flex-shrink:0; }
        .brand-icon { width:36px; height:36px; background:var(--cta); border-radius:8px; display:flex; align-items:center; justify-content:center; }
        .brand-name { font-family:'Fira Code',monospace; font-size:15px; font-weight:600; color:var(--primary); letter-spacing:-.3px; }
        .brand-sub { font-size:11px; color:var(--muted); font-weight:400; }
        .admin-badge { display:inline-flex; align-items:center; gap:4px; height:20px; padding:0 8px; border-radius:100px; font-size:10px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; background:#FEF3C7; color:#92400E; border:1px solid #FDE68A; flex-shrink:0; }
        .navbar-nav { display:flex; align-items:center; gap:4px; }
        .nav-link { display:flex; align-items:center; gap:7px; height:36px; padding:0 14px; border-radius:8px; font-size:14px; font-weight:500; color:var(--secondary); text-decoration:none; transition:background 150ms,color 150ms; white-space:nowrap; }
        .nav-link:hover { background:var(--bg); color:var(--primary); }
        .nav-link.active { background:var(--cta-light); color:var(--cta); font-weight:600; }
        .navbar-actions { display:flex; align-items:center; gap:8px; margin-left:auto; }
        .logout-btn { display:flex; align-items:center; gap:6px; height:36px; padding:0 14px; border-radius:8px; font-size:13px; font-weight:500; color:#DC2626; background:transparent; border:none; cursor:pointer; font-family:'Fira Sans',sans-serif; transition:background 150ms; white-space:nowrap; }
        .logout-btn:hover { background:#FEF2F2; }
        .avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#0369A1 0%,#22D3EE 100%); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; cursor:pointer; border:2px solid var(--border); transition:border-color 150ms,box-shadow 150ms; flex-shrink:0; }
        .avatar:hover { border-color:var(--cta); box-shadow:0 0 0 3px #E0F2FE; }
        .layout { display:flex; height:calc(100vh - 64px); overflow:hidden; }
        .sidebar { width:var(--sidebar); flex-shrink:0; background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; overflow:hidden; }
        .sidebar-header { padding:16px 16px 12px; border-bottom:1px solid var(--border); flex-shrink:0; }
        .sidebar-title { font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.6px; margin-bottom:10px; }
        .view-toggle { display:flex; background:var(--bg); border-radius:8px; padding:3px; gap:2px; }
        .vt-btn { flex:1; height:28px; border:none; border-radius:6px; font-size:11px; font-weight:500; font-family:'Fira Sans',sans-serif; cursor:pointer; transition:all 180ms; color:var(--muted); background:transparent; }
        .vt-btn.active { background:var(--surface); color:var(--primary); box-shadow:0 1px 4px rgba(15,23,42,.1); }
        .sidebar-filter { padding:8px 14px; border-bottom:1px solid var(--border); flex-shrink:0; }
        .sidebar-filter select { width:100%; height:30px; background:var(--bg); border:1.5px solid var(--border); border-radius:6px; font-size:11px; font-family:'Fira Sans',sans-serif; color:var(--secondary); padding:0 8px; outline:none; cursor:pointer; }
        .sidebar-body { flex:1; overflow-y:auto; padding:12px; }
        .sidebar-body::-webkit-scrollbar { width:4px; }
        .sidebar-body::-webkit-scrollbar-track { background:transparent; }
        .sidebar-body::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }
        .training-list { display:flex; flex-direction:column; gap:6px; }
        .tl-item { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px; cursor:pointer; transition:background 150ms; border:1.5px solid transparent; }
        .tl-item:hover { background:var(--bg); }
        .tl-item.active { background:var(--cta-light); border-color:#BAE6FD; }
        .tl-dot { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
        .tl-info { flex:1; min-width:0; }
        .tl-name { font-size:11px; font-weight:600; color:var(--primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .tl-date { font-size:10px; color:var(--muted); }
        .tl-check { width:16px; height:16px; border-radius:4px; border:1.5px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .tl-item.active .tl-check { background:var(--cta); border-color:var(--cta); }
        .empty-sidebar { text-align:center; padding:28px 14px; color:var(--muted); font-size:12px; }
        .cal-main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .cal-toolbar { display:flex; align-items:center; gap:8px; padding:12px 20px; border-bottom:1px solid var(--border); flex-shrink:0; background:var(--surface); flex-wrap:wrap; }
        .cal-month-label { font-family:'Fira Code',monospace; font-size:17px; font-weight:600; color:var(--primary); min-width:180px; }
        .cal-nav { display:flex; gap:4px; }
        .cal-nav-btn { width:34px; height:34px; border:1.5px solid var(--border); border-radius:8px; background:var(--surface); display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--secondary); transition:all 150ms; }
        .cal-nav-btn:hover { border-color:var(--cta); color:var(--cta); background:var(--cta-light); }
        .cal-today-btn { height:34px; padding:0 14px; border:1.5px solid var(--border); border-radius:8px; background:var(--surface); font-size:13px; font-weight:500; font-family:'Fira Sans',sans-serif; color:var(--secondary); cursor:pointer; transition:all 150ms; }
        .cal-today-btn:hover { border-color:var(--cta); color:var(--cta); background:var(--cta-light); }
        .cal-toolbar-right { margin-left:auto; display:flex; align-items:center; gap:8px; }
        .unit-filter { height:32px; padding:0 10px; border:1.5px solid var(--border); border-radius:8px; background:var(--surface); font-size:12px; font-family:'Fira Sans',sans-serif; color:var(--secondary); cursor:pointer; outline:none; transition:border-color 150ms; }
        .unit-filter:hover { border-color:#94a3b8; }
        .conflict-badge { display:inline-flex; align-items:center; gap:5px; height:30px; padding:0 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; border:none; font-family:'Fira Sans',sans-serif; transition:all 150ms; text-decoration:none; }
        .conflict-badge.has-conflict { background:#FEF2F2; color:#DC2626; border:1.5px solid #FECACA; }
        .conflict-badge.no-conflict { background:#F0FDF4; color:#16A34A; border:1.5px solid #BBF7D0; }
        .cal-wrap { flex:1; overflow:hidden; position:relative; }
        .cal-grid { height:100%; display:flex; flex-direction:column; }
        .cal-days-header { display:grid; grid-template-columns:repeat(7,1fr); background:var(--surface); border-bottom:1px solid var(--border); flex-shrink:0; }
        .cal-day-label { padding:8px 0; text-align:center; font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; }
        .cal-day-label.weekend { color:#94A3B8; }
        .cal-weeks { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .cal-week { flex:1; display:grid; grid-template-columns:repeat(7,1fr); border-bottom:1px solid var(--border); min-height:0; }
        .cal-week:last-child { border-bottom:none; }
        .cal-cell { border-right:1px solid var(--border); padding:5px 5px 3px; display:flex; flex-direction:column; min-height:0; overflow:hidden; cursor:default; transition:background 100ms; position:relative; }
        .cal-cell:last-child { border-right:none; }
        .cal-cell.other-month .cell-num { opacity:.3; }
        .cal-cell.today { background:#EFF6FF; }
        .cal-cell.today .cell-num { background:var(--cta); color:#fff; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; }
        .cal-cell.weekend { background:#FAFAFA; }
        .cal-cell.holiday { background:#FFF7ED; }
        .cal-cell.holiday .cell-num { color:#C2410C; font-weight:700; }
        .holiday-dot { width:5px; height:5px; border-radius:50%; background:#EF4444; flex-shrink:0; margin-left:3px; margin-top:1px; }
        .cell-num { font-family:'Fira Code',monospace; font-size:11px; font-weight:500; color:var(--secondary); flex-shrink:0; display:flex; align-items:center; justify-content:flex-start; margin-bottom:2px; min-height:22px; }
        .cell-events { flex:1; display:flex; flex-direction:column; gap:2px; overflow:hidden; min-height:0; }
        .event-bar { height:16px; border-radius:3px; display:flex; align-items:center; font-size:9px; font-weight:600; padding:0 4px; cursor:pointer; transition:filter 150ms,opacity 150ms; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
        .event-bar:hover { filter:brightness(1.12); }
        .event-bar.start { border-radius:3px 0 0 3px; padding-left:5px; }
        .event-bar.mid { border-radius:0; }
        .event-bar.end { border-radius:0 3px 3px 0; }
        .event-bar.single { border-radius:3px; }
        .event-bar.conflict-alert { opacity:.55; background-image:repeating-linear-gradient(135deg,rgba(0,0,0,.12) 0,rgba(0,0,0,.12) 2px,transparent 2px,transparent 6px) !important; outline:1.5px solid rgba(245,158,11,.7); outline-offset:-1px; }
        .more-events { font-size:9px; color:var(--muted); font-weight:500; padding:0 3px; cursor:pointer; }
        .more-events:hover { color:var(--cta); }
        .tooltip { position:fixed; z-index:100; background:var(--primary); color:#fff; border-radius:10px; padding:10px 14px; font-size:11px; line-height:1.5; pointer-events:none; box-shadow:0 8px 24px rgba(15,23,42,.25); max-width:240px; opacity:0; transition:opacity 150ms ease; }
        .tooltip-title { font-weight:700; margin-bottom:5px; font-size:12px; }
        .tooltip-row { display:flex; align-items:flex-start; gap:6px; opacity:.8; margin-bottom:3px; }
        .wi-chips { display:flex; flex-wrap:wrap; gap:3px; margin-top:5px; }
        .wi-chip { display:inline-flex; align-items:center; height:16px; padding:0 6px; border-radius:4px; background:rgba(255,255,255,.15); font-size:9px; font-weight:600; }
        @media (max-width:900px) { .logout-btn span { display:none; } .logout-btn { padding:0 10px; } }
        @media (max-width:768px) {
            .sidebar { display:none; }
            .navbar { padding:0 16px; gap:12px; }
            .brand-sub { display:none; }
            .nav-link { padding:0 10px; font-size:13px; gap:5px; }
            .cal-toolbar { padding:8px 14px; }
            .cal-month-label { font-size:14px; min-width:0; }
            .cal-week { min-height:80px; }
            .cal-cell { padding:3px 2px 2px; }
            .cell-num { font-size:10px; width:18px; height:18px; }
            .event-bar { font-size:0 !important; height:12px; }
            .more-events { font-size:8px; }
            .cal-day-label { font-size:10px; padding:5px 0; }
        }
        @media (max-width:480px) {
            .navbar { padding:0 12px; }
            .brand-name { font-size:13px; }
            .nav-link span.link-text { display:none; }
            .admin-badge { display:none; }
            .cal-toolbar { padding:6px 10px; gap:5px; }
            .cal-month-label { font-size:12px; }
            .cal-today-btn { display:none; }
            .cal-nav-btn { width:28px; height:28px; }
            .cal-week { min-height:60px; }
            .cell-num { font-size:9px; width:16px; height:16px; }
            .event-bar { height:10px; border-radius:2px; }
        }
        @media (prefers-reduced-motion:reduce) { *, *::before, *::after { animation-duration:.01ms !important; transition-duration:.01ms !important; } }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <a href="{{ route('admin.catalog') }}" class="navbar-brand">
        <div class="brand-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>
        <div>
            <div class="brand-name">BPSDM Kalender</div>
            <div class="brand-sub">Sistem Pelatihan ASN</div>
        </div>
    </a>

    <span class="admin-badge">
        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Admin
    </span>

    <nav class="navbar-nav">
        <a href="{{ route('admin.catalog') }}" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span class="link-text">Katalog</span>
        </a>
        <a href="{{ route('admin.calendar') }}" class="nav-link active" aria-current="page">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span class="link-text">Kalender</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="link-text">Admin</span>
        </a>
    </nav>

    <div class="navbar-actions">
        <form action="/logout" method="POST" style="display:contents">
            @csrf
            <button type="submit" class="logout-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </form>
        <div class="avatar" title="{{ Auth::user()->name ?? 'Admin' }}">
            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
        </div>
    </div>
</nav>

<!-- LAYOUT -->
<div class="layout">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">Angkatan Pelatihan</div>
            <div class="view-toggle" id="viewToggle">
                <button class="vt-btn active" data-mode="all">Semua</button>
                <button class="vt-btn" data-mode="unit">Per Unit</button>
            </div>
        </div>
        @if($units->count())
        <div class="sidebar-filter" id="unitFilterWrap" style="display:none">
            <select id="unitFilterSelect">
                <option value="">— Semua Unit —</option>
                @foreach($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="sidebar-body" id="sidebarBody"></div>
    </aside>

    <!-- CALENDAR -->
    <div class="cal-main">
        <div class="cal-toolbar">
            <div class="cal-nav">
                <button class="cal-nav-btn" id="prevBtn" aria-label="Bulan sebelumnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button class="cal-nav-btn" id="nextBtn" aria-label="Bulan berikutnya">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
            <div class="cal-month-label" id="monthLabel"></div>
            <button class="cal-today-btn" id="todayBtn">Hari ini</button>
            <div class="cal-toolbar-right">
                @if($units->count())
                <select class="unit-filter" id="calUnitFilter" aria-label="Filter unit">
                    <option value="">Semua Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
                @endif
                <a href="{{ route('admin.conflicts.index') }}" class="conflict-badge no-conflict" id="conflictBadge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <span id="conflictText">Tidak ada konflik</span>
                </a>
            </div>
        </div>

        <div class="cal-wrap">
            <div class="cal-grid" id="calGrid">
                <div class="cal-days-header" id="daysHeader"></div>
                <div class="cal-weeks" id="calWeeks"></div>
            </div>
        </div>
    </div>
</div>

<!-- TOOLTIP -->
<div class="tooltip" id="tooltip">
    <div class="tooltip-title" id="ttTitle"></div>
    <div class="tooltip-row" id="ttBatch"></div>
    <div class="tooltip-row" id="ttDate"></div>
    <div class="tooltip-row" id="ttUnit"></div>
    <div class="wi-chips" id="ttWis"></div>
</div>

<script type="module">
import { animate, stagger } from 'https://esm.sh/animejs@4';

const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAYS_ID   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

const events    = @json($events);
const batchList = @json($batchList);
const holidays  = @json($holidays); // { 'YYYY-MM-DD': 0, ... } keyed set for O(1) lookup
const conflictBatchIds = new Set(batchList.filter(b => b.conflictAlert).map(b => b.id));

const params     = new URLSearchParams(location.search);
const focusBatch = params.get('batch') ? parseInt(params.get('batch')) : null;

let visibleBatchIds = new Set(focusBatch ? [focusBatch] : batchList.map(b => b.id));
let unitFilter = '';

const today   = new Date();
let curYear   = today.getFullYear();
let curMonth  = today.getMonth();

if (focusBatch) {
    const b = batchList.find(x => x.id === focusBatch);
    if (b && b.startDate) {
        const d = new Date(b.startDate + 'T00:00:00');
        curYear  = d.getFullYear();
        curMonth = d.getMonth();
    }
}

function textOnColor(hex) {
    if (!hex || hex.length < 4) return '#fff';
    const h = hex.replace('#','');
    const n = parseInt(h.length === 3 ? h.split('').map(c=>c+c).join('') : h, 16);
    const r = (n>>16)&255, g = (n>>8)&255, b = n&255;
    return (0.299*r+0.587*g+0.114*b)/255 > 0.55 ? '#0F172A' : '#FFFFFF';
}

function buildSidebar(doAnimate=false) {
    const body = document.getElementById('sidebarBody');
    let list   = batchList;
    if (unitFilter) list = list.filter(b => String(b.unitId) === unitFilter);

    if (!list.length) {
        body.innerHTML = `<div class="empty-sidebar">Tidak ada angkatan${unitFilter?' untuk unit ini':''}.</div>`;
        return;
    }

    body.innerHTML = `<div class="training-list" id="trainingList">${
        list.map(b => {
            const isVis      = visibleBatchIds.has(b.id);
            const evs        = events.filter(e => e.batchId === b.id);
            const color      = (evs[0] && evs[0].color) || '#0369A1';
            const ds         = b.startDate ? shortDate(b.startDate) : (b.status==='draft'?'Draft':'—');
            const hasConflict = conflictBatchIds.has(b.id);
            return `<div class="tl-item${isVis?' active':''}" data-id="${b.id}" role="button" tabindex="0">
                <div class="tl-dot" style="background:${color}${hasConflict?';box-shadow:0 0 0 2px #F59E0B':''}"></div>
                <div class="tl-info">
                    <div class="tl-name">${b.name}${hasConflict?'<span style="display:inline-block;width:6px;height:6px;background:#F59E0B;border-radius:50%;margin-left:5px;vertical-align:middle"></span>':''}</div>
                    <div class="tl-date">${ds}</div>
                </div>
                <div class="tl-check">
                    ${isVis?`<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`:''}
                </div>
            </div>`;
        }).join('')
    }</div>`;

    if (doAnimate) {
        animate('.tl-item', { opacity:[0,1], translateX:[-10,0], duration:240, ease:'outExpo', delay:stagger(28) });
    }

    function toggleItem(item) {
        const id = parseInt(item.dataset.id);
        if (visibleBatchIds.has(id)) visibleBatchIds.delete(id);
        else                         visibleBatchIds.add(id);
        buildSidebar();
        renderCalendar();
        animate(item, { scale:[.96,1], duration:180, ease:'outBack' });
    }

    document.getElementById('trainingList').addEventListener('click', e => {
        const item = e.target.closest('.tl-item'); if (!item) return;
        toggleItem(item);
    });
    document.getElementById('trainingList').addEventListener('keydown', e => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const item = e.target.closest('.tl-item'); if (!item) return;
        e.preventDefault();
        toggleItem(item);
    });
}

function shortDate(ds) {
    if (!ds) return '—';
    const d = new Date(ds + 'T00:00:00');
    return `${d.getDate()} ${MONTHS_ID[d.getMonth()].slice(0,3)} ${d.getFullYear()}`;
}

document.getElementById('viewToggle').addEventListener('click', e => {
    const btn = e.target.closest('.vt-btn'); if (!btn) return;
    document.querySelectorAll('.vt-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    const uw = document.getElementById('unitFilterWrap');
    if (uw) uw.style.display = btn.dataset.mode === 'unit' ? '' : 'none';
    animate(btn, { scale:[.94,1], duration:180, ease:'outBack' });
});

const unitSel = document.getElementById('unitFilterSelect');
if (unitSel) unitSel.addEventListener('change', () => {
    unitFilter = unitSel.value;
    visibleBatchIds = new Set(
        unitFilter
            ? batchList.filter(b=>String(b.unitId)===unitFilter).map(b=>b.id)
            : batchList.map(b=>b.id)
    );
    buildSidebar(); renderCalendar();
});

const calUnitFilter = document.getElementById('calUnitFilter');
if (calUnitFilter) calUnitFilter.addEventListener('change', () => {
    const val = calUnitFilter.value;
    visibleBatchIds = new Set(val ? batchList.filter(b=>String(b.unitId)===val).map(b=>b.id) : batchList.map(b=>b.id));
    buildSidebar(); renderCalendar();
});

function renderCalendar(animDir=0) {
    document.getElementById('monthLabel').textContent = `${MONTHS_ID[curMonth]} ${curYear}`;
    document.getElementById('daysHeader').innerHTML = DAYS_ID.map((d,i) =>
        `<div class="cal-day-label${i===0||i===6?' weekend':''}">${d}</div>`
    ).join('');

    const firstDay  = new Date(curYear, curMonth, 1);
    const startDow  = firstDay.getDay();
    const daysInMon = new Date(curYear, curMonth+1, 0).getDate();
    const cells     = [];
    const prevDays  = new Date(curYear, curMonth, 0).getDate();

    for (let i = startDow-1; i >= 0; i--) cells.push({ day:prevDays-i, month:curMonth-1, year:curYear, other:true });
    for (let d = 1; d <= daysInMon; d++)   cells.push({ day:d, month:curMonth, year:curYear, other:false });
    while (cells.length % 7 !== 0) cells.push({ day:cells.length-daysInMon-startDow+1, month:curMonth+1, year:curYear, other:true });

    const eventsOnDay = {};
    const visEvs = events.filter(ev => visibleBatchIds.has(ev.batchId));

    // Assign slot per batch
    const batchSlot = {};
    let slot = 0;
    visEvs.forEach(ev => { if (!(ev.batchId in batchSlot)) batchSlot[ev.batchId] = slot++ % 4; });

    let hasAlert = false;
    visEvs.forEach(ev => {
        if (!ev.start || !ev.end) return;
        if (ev.isAlert || conflictBatchIds.has(ev.batchId)) hasAlert = true;
        const s       = new Date(ev.start+'T00:00:00');
        const e       = new Date(ev.end+'T00:00:00');
        const pos     = batchSlot[ev.batchId] ?? 0;
        const wkDays  = ev.workDays || 5;
        let cur       = new Date(s);
        while (cur <= e) {
            const dow = cur.getDay(); // 0=Sun, 6=Sat
            const skipSun     = dow === 0 && wkDays < 7;
            const skipSat     = dow === 6 && wkDays < 6;
            const skipHoliday = Object.prototype.hasOwnProperty.call(holidays, dateKey(cur));
            if (!skipSun && !skipSat && !skipHoliday) {
                const key = dateKey(cur);
                if (!eventsOnDay[key]) eventsOnDay[key] = [];
                const isStart   = cur.getTime() === s.getTime();
                const isEnd     = cur.getTime() === e.getTime();
                const isSingle  = isStart && isEnd;
                const isWkStart = dow === 0 || isStart;
                const isWkEnd   = dow === 6 || isEnd;
                eventsOnDay[key].push({
                    ev, pos,
                    segment: isSingle ? 'single' : isWkStart ? 'start' : isWkEnd ? 'end' : 'mid',
                    showLabel: isStart || dow === 0
                });
            }
            cur = new Date(cur.getTime() + 86400000);
        }
    });

    const badge = document.getElementById('conflictBadge');
    if (hasAlert) {
        badge.className = 'conflict-badge has-conflict';
        document.getElementById('conflictText').textContent = 'Ada peringatan!';
    } else {
        badge.className = 'conflict-badge no-conflict';
        document.getElementById('conflictText').textContent = 'Tidak ada konflik';
    }

    const weeksEl = document.getElementById('calWeeks');
    const weeks   = [];
    for (let w = 0; w < cells.length/7; w++) weeks.push(cells.slice(w*7, w*7+7));

    weeksEl.innerHTML = weeks.map(week => `<div class="cal-week">${
        week.map(cell => {
            const d       = new Date(cell.year, cell.month, cell.day);
            const key     = dateKey(d);
            const isToday   = !cell.other && cell.day===today.getDate() && cell.month===today.getMonth() && cell.year===today.getFullYear();
            const isWknd    = d.getDay()===0 || d.getDay()===6;
            const isHoliday = !cell.other && Object.prototype.hasOwnProperty.call(holidays, key);
            const evs     = eventsOnDay[key] || [];
            const slots   = Array(4).fill(null);
            evs.forEach(ev => { if (ev.pos <= 3) slots[ev.pos] = ev; });

            const evHtml = slots.map((evItem, si) => {
                if (!evItem) return si < 3 ? `<div style="height:16px;flex-shrink:0"></div>` : '';
                const color      = evItem.ev.color || '#0369A1';
                const txtCol     = textOnColor(color);
                const lbl        = evItem.showLabel ? evItem.ev.name : '';
                const isConflict = conflictBatchIds.has(evItem.ev.batchId);
                return `<div class="event-bar ${evItem.segment}${isConflict?' conflict-alert':''}"
                    style="background:${color};color:${txtCol}"
                    data-id="${evItem.ev.id}">${lbl}</div>`;
            }).join('');

            const extra       = evs.filter(ev => ev.pos > 3).length;
            const holidayHtml = isHoliday ? `<div class="holiday-dot" title="Hari Libur"></div>` : '';

            return `<div class="cal-cell${cell.other?' other-month':''}${isToday?' today':''}${isWknd?' weekend':''}${isHoliday?' holiday':''}">
                <div class="cell-num">${cell.day}${holidayHtml}</div>
                <div class="cell-events">
                    ${evHtml}
                    ${extra > 0 ? `<div class="more-events">+${extra}</div>` : ''}
                </div>
            </div>`;
        }).join('')
    }</div>`).join('');

    if (animDir !== 0) {
        animate('.cal-week', { opacity:[0,1], translateX:[animDir*24,0], duration:290, ease:'outExpo', delay:stagger(22) });
    } else {
        animate('.cal-week', { opacity:[0,1], translateY:[8,0], duration:310, ease:'outExpo', delay:stagger(22) });
    }

    // Tooltip
    const tooltip = document.getElementById('tooltip');
    weeksEl.querySelectorAll('.event-bar').forEach(bar => {
        bar.addEventListener('mouseenter', e => {
            const ev = events.find(x => x.id === parseInt(bar.dataset.id));
            if (!ev) return;
            document.getElementById('ttTitle').textContent = ev.name;
            document.getElementById('ttBatch').innerHTML =
                `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> ${ev.batchName}`;
            document.getElementById('ttDate').innerHTML =
                `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> ${shortDate(ev.start)} – ${shortDate(ev.end)}`;
            const unitEl = document.getElementById('ttUnit');
            if (ev.unit) { unitEl.innerHTML = `<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg> ${ev.unit}`; unitEl.style.display=''; }
            else          unitEl.style.display = 'none';
            const wisEl = document.getElementById('ttWis');
            if (ev.wis && ev.wis.length) {
                wisEl.innerHTML = ev.wis.map(n=>`<span class="wi-chip">${n}</span>`).join('');
                wisEl.style.display = '';
            } else wisEl.style.display = 'none';
            tooltip.style.opacity = '1';
            posTooltip(e);
        });
        bar.addEventListener('mousemove', posTooltip);
        bar.addEventListener('mouseleave', () => { tooltip.style.opacity = '0'; });
        bar.addEventListener('click', () => {
            const ev = events.find(x => x.id === parseInt(bar.dataset.id));
            if (ev) window.location.href = `/admin/batches/${ev.batchId}`;
        });
    });
}

function posTooltip(e) {
    const tt = document.getElementById('tooltip');
    let x = e.clientX+14, y = e.clientY-10;
    if (x+260 > window.innerWidth)  x = e.clientX-260;
    if (y+130 > window.innerHeight) y = e.clientY-130;
    tt.style.left = x+'px'; tt.style.top = y+'px';
}

function dateKey(d) {
    return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
}

document.getElementById('prevBtn').addEventListener('click', () => {
    curMonth--; if (curMonth<0) { curMonth=11; curYear--; }
    renderCalendar(-1);
    animate('#prevBtn', { scale:[.86,1], duration:180, ease:'outBack' });
});
document.getElementById('nextBtn').addEventListener('click', () => {
    curMonth++; if (curMonth>11) { curMonth=0; curYear++; }
    renderCalendar(1);
    animate('#nextBtn', { scale:[.86,1], duration:180, ease:'outBack' });
});
document.getElementById('todayBtn').addEventListener('click', () => {
    curYear=today.getFullYear(); curMonth=today.getMonth();
    renderCalendar(0);
    animate('#todayBtn', { scale:[.94,1], duration:180, ease:'outBack' });
});

buildSidebar(true);
renderCalendar(0);
</script>
</body>
</html>
