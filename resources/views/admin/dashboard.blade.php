<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Admin BPSDM Kalender</title>
    @vite(['resources/css/app.css'])
    <style>
        /* ── FEATURE CARDS ───────────────────────────────── */
        .feat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transform: translateY(20px);
            transition: box-shadow 250ms, border-color 250ms, transform 250ms;
            cursor: default;
        }
        .feat-card.available { cursor: pointer; }
        .feat-card.available:hover {
            box-shadow: 0 12px 32px rgba(15,23,42,.14), 0 4px 10px rgba(15,23,42,.08);
            border-color: #0369a1;
            transform: translateY(-3px) !important;
        }
        .feat-card.coming:hover {
            box-shadow: 0 4px 16px rgba(15,23,42,.07);
            border-color: #cbd5e1;
            transform: translateY(-1px) !important;
        }
        .feat-card-header {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
        }
        .feat-card-icon { opacity: .2; }
        .feat-card-body { padding: 16px; flex: 1; }
        .feat-card-title {
            font-size: 13px; font-weight: 700; color: #0f172a;
            margin-bottom: 4px; line-height: 1.3;
        }
        .feat-card-desc { font-size: 11px; color: #64748b; line-height: 1.5; }
        .feat-card-footer {
            padding: 10px 16px 14px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .badge-avail {
            height: 20px; padding: 0 8px; border-radius: 100px;
            font-size: 10px; font-weight: 700; letter-spacing: .3px;
            background: #dcfce7; color: #166534;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .badge-soon {
            height: 20px; padding: 0 8px; border-radius: 100px;
            font-size: 10px; font-weight: 600; letter-spacing: .3px;
            background: #f1f5f9; color: #64748b;
            display: inline-flex; align-items: center;
        }
        .feat-card-cta {
            height: 28px; padding: 0 12px; border-radius: 7px;
            font-size: 11px; font-weight: 600; font-family: var(--font-sans);
            background: #0369a1; color: #fff; border: none;
            display: flex; align-items: center; gap: 5px;
            text-decoration: none; cursor: pointer; transition: background 150ms;
        }
        .feat-card-cta:hover { background: #0284c7; }

        /* BIG CARDS */
        .big-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            cursor: pointer;
            transition: box-shadow 250ms, border-color 250ms, transform 250ms;
            opacity: 0;
            transform: translateY(20px);
        }
        .big-card:hover {
            box-shadow: 0 16px 40px rgba(15,23,42,.15), 0 4px 12px rgba(15,23,42,.08);
            border-color: #0369a1;
            transform: translateY(-4px) !important;
        }
        .big-card-header {
            height: 120px;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .big-card-icon { opacity: .18; }
        .big-card-body { padding: 20px 20px 14px; }
        .big-card-title { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 5px; }
        .big-card-desc  { font-size: 13px; color: #64748b; line-height: 1.5; }
        .big-card-footer {
            padding: 12px 20px 18px;
            display: flex; align-items: center; justify-content: space-between;
        }

        /* FLOW STEPS */
        .flow-step {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; background: #fff;
            border: 1px solid #e2e8f0; border-radius: 9px;
            opacity: 0; transform: translateX(-16px);
            transition: background 150ms, border-color 150ms;
        }
        .flow-step:hover { background: #f8fafc; border-color: #0369a1; }
        .flow-step-num {
            width: 28px; height: 28px; border-radius: 7px;
            background: #0369a1; color: #fff;
            font-family: var(--font-mono); font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .flow-arrow {
            text-align: center; color: #94a3b8; font-size: 16px;
            padding: 2px 0; opacity: 0;
        }

        /* ARCH CARD */
        .arch-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            padding: 16px 20px; display: flex; flex-direction: column; gap: 8px;
            opacity: 0; transform: translateY(16px);
        }
        .arch-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 4px; }
        .arch-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 10px; background: #f8fafc; border-radius: 7px;
            font-size: 12px; color: #334155;
        }
        .arch-item svg { flex-shrink: 0; color: #0369a1; }

        /* USER INFO */
        .user-info-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            border-radius: 100px; padding: 4px 12px 4px 6px;
            font-size: 12px; color: rgba(255,255,255,.85);
        }
        .user-info-dot {
            width: 6px; height: 6px; border-radius: 50%; background: #22c55e; flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .big-grid { grid-template-columns: 1fr !important; }
            .feat-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .flow-grid { grid-template-columns: 1fr !important; }
            .arch-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 480px) {
            .feat-grid { grid-template-columns: 1fr !important; }
            .arch-grid { grid-template-columns: 1fr !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body class="font-sans bg-slate-50 text-slate-950 min-h-screen text-base leading-[1.6]">

@include('admin.partials.navbar')

<!-- HERO -->
<section class="hero-pattern bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 px-6 pt-10 pb-12 relative overflow-hidden max-[768px]:px-5 max-[768px]:pt-7 max-[768px]:pb-9 max-[640px]:px-4" id="hero">
    <div class="max-w-[1200px] mx-auto relative">

        <!-- Welcome -->
        <div id="heroBadge" class="hero-badge inline-flex items-center gap-1.5 bg-white/[0.12] border border-white/20 rounded-full px-3 py-1 text-[12px] font-medium text-white/90 mb-4">
            <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
            Panel Admin Aktif — Sistem Kalender Pelatihan
        </div>

        <div class="flex items-start justify-between gap-6 flex-wrap mb-8 max-[768px]:flex-col max-[768px]:gap-4" id="heroTop">
            <div>
                <h1 class="text-[clamp(22px,3.5vw,34px)] font-bold text-white leading-tight mb-2">
                    Selamat datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-[14px] text-white/70 max-w-[520px] mb-4 leading-relaxed">
                    Kelola seluruh program pelatihan ASN dari satu panel terpusat.
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="user-info-pill">
                        <span class="user-info-dot"></span>
                        Admin
                    </span>
                    <span class="user-info-pill">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        @if(Auth::user()->organizational_unit_id)
                            Unit #{{ Auth::user()->organizational_unit_id }}
                        @else
                            Unit belum diatur
                        @endif
                    </span>
                    <span class="user-info-pill">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Tahun 2026
                    </span>
                </div>
            </div>
            <div class="text-right max-[768px]:text-left">
                <div class="font-mono text-[13px] text-white/50 mb-1">Tanggal Hari Ini</div>
                <div class="font-mono text-[22px] font-semibold text-white" id="heroDate"></div>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex gap-5 flex-wrap" id="statsRow">
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[110px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[640px]:text-[18px]" id="statTotal">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Total Pelatihan</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[110px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[640px]:text-[18px]" id="statOpen">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Dibuka</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[110px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[640px]:text-[18px]" id="statBatches">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Angkatan</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[110px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[640px]:text-[18px]" id="statConflicts">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Konflik</div>
            </div>
            <div class="stat-card bg-white/10 border border-white/[0.15] rounded-xl px-5 py-3.5 min-w-[110px] max-[640px]:px-3 max-[640px]:py-2.5 max-[640px]:min-w-[80px]">
                <div class="font-mono text-2xl font-semibold text-white leading-none max-[640px]:text-[18px]" id="statSeats">0</div>
                <div class="text-xs text-white/[0.65] mt-1">Kuota Tersedia</div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN -->
<main class="max-w-[1200px] mx-auto px-6 pt-8 pb-16 max-[640px]:px-4 max-[640px]:pt-5">

    <!-- ── AVAILABLE FEATURES ───────────────────────── -->
    <div class="flex items-baseline gap-2.5 mb-5">
        <h2 class="text-lg font-bold text-slate-900">Fitur Tersedia</h2>
        <span class="font-mono text-[13px] text-slate-500">2 modul aktif</span>
    </div>

    <div class="grid grid-cols-2 gap-5 mb-10 big-grid max-[640px]:grid-cols-1">

        <!-- Katalog -->
        <a href="/admin/catalog" class="big-card no-underline" id="bigCatalog">
            <div class="big-card-header" style="background:linear-gradient(135deg,#0F172A 0%,#1e3a5f 100%)">
                <div class="big-card-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-green-500/20 border border-green-400/30 rounded-full px-2.5 py-1 text-[11px] font-semibold text-green-300">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>Tersedia
                </div>
            </div>
            <div class="big-card-body">
                <div class="big-card-title">Katalog Pelatihan</div>
                <div class="big-card-desc">Lihat, cari, dan kelola seluruh daftar program pelatihan ASN 2026. Tambah pelatihan via upload Excel atau input manual.</div>
            </div>
            <div class="big-card-footer">
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 h-[22px] px-2 bg-sky-50 text-sky-700 border border-sky-200 rounded-md text-[10px] font-semibold">{{ $stats['total'] }} Program</span>
                    <span class="inline-flex items-center gap-1 h-[22px] px-2 bg-green-50 text-green-700 border border-green-200 rounded-md text-[10px] font-semibold">Upload Excel</span>
                </div>
                <span class="inline-flex items-center gap-1.5 h-[34px] px-4 bg-sky-700 text-white rounded-[9px] text-[12px] font-bold">
                    Buka
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            </div>
        </a>

        <!-- Kalender -->
        <a href="/admin/calendar" class="big-card no-underline" id="bigCalendar">
            <div class="big-card-header" style="background:linear-gradient(135deg,#064E3B 0%,#065F46 100%)">
                <div class="big-card-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-green-500/20 border border-green-400/30 rounded-full px-2.5 py-1 text-[11px] font-semibold text-green-300">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>Tersedia
                </div>
            </div>
            <div class="big-card-body">
                <div class="big-card-title">Kalender Jadwal</div>
                <div class="big-card-desc">Visualisasi kalender bulanan seluruh pelatihan. Filter per kategori, lihat konflik jadwal, dan pantau kepadatan program pelatihan.</div>
            </div>
            <div class="big-card-footer">
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 h-[22px] px-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[10px] font-semibold">Kalender Bulanan</span>
                    <span class="inline-flex items-center gap-1 h-[22px] px-2 bg-sky-50 text-sky-700 border border-sky-200 rounded-md text-[10px] font-semibold">Filter Kategori</span>
                </div>
                <span class="inline-flex items-center gap-1.5 h-[34px] px-4 bg-emerald-700 text-white rounded-[9px] text-[12px] font-bold">
                    Buka
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                </span>
            </div>
        </a>
    </div>

    <!-- ── SYSTEM FEATURES ───────────────────────────── -->
    <div class="flex items-baseline gap-2.5 mb-5">
        <h2 class="text-lg font-bold text-slate-900">Fitur Sistem</h2>
        <span class="font-mono text-[13px] text-slate-500">{{ auth()->user()->isSuperAdmin() ? 7 : 6 }} aktif · 1 segera</span>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-10 feat-grid max-[900px]:grid-cols-3 max-[640px]:grid-cols-2 max-[400px]:grid-cols-1">

        <!-- Template -->
        <a href="{{ route('admin.templates.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#1e3a5f 0%,#0369a1 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Template Pelatihan</div>
                <div class="feat-card-desc">Kelola template dan phase pelatihan per unit.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- Batch -->
        <a href="{{ route('admin.batches.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#3B0764 0%,#6D28D9 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <rect x="2" y="3" width="6" height="6" rx="1"/><rect x="9" y="3" width="6" height="6" rx="1"/><rect x="16" y="3" width="6" height="6" rx="1"/>
                        <rect x="2" y="10" width="6" height="6" rx="1"/><rect x="9" y="10" width="6" height="6" rx="1"/><rect x="16" y="10" width="6" height="6" rx="1"/>
                        <rect x="2" y="17" width="6" height="6" rx="1"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Batch Training</div>
                <div class="feat-card-desc">Buat dan kelola angkatan pelatihan berdasarkan template.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- Generate Schedule — link ke batches karena generate dari sana -->
        <a href="{{ route('admin.batches.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#7C2D12 0%,#C2410C 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Generate Jadwal</div>
                <div class="feat-card-desc">Buat jadwal otomatis dari template dengan skip hari libur.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- WI Assignment -->
        <a href="{{ route('admin.widyaiswaras.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#0C4A6E 0%,#0369A1 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Assign Widyaiswara</div>
                <div class="feat-card-desc">Tugaskan WI ke setiap aktivitas jadwal tanpa konflik.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- Holidays -->
        <a href="{{ route('admin.holidays.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#065F46 0%,#059669 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M18.66 5.34l-1.41 1.41"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Kelola Hari Libur</div>
                <div class="feat-card-desc">Atur hari libur nasional dan custom untuk generator jadwal.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- PDF Export — belum implementasi -->
        <div class="feat-card coming">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#831843 0%,#BE185D 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Export PDF</div>
                <div class="feat-card-desc">Unduh jadwal batch sebagai dokumen PDF profesional.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-soon">Segera</span>
                <span class="text-[10px] text-slate-400 font-mono">export_pdf</span>
            </div>
        </div>

        <!-- Conflict Monitor -->
        <a href="{{ route('admin.conflicts.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#713F12 0%,#D97706 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Monitor Konflik</div>
                <div class="feat-card-desc">Deteksi konflik seminar dan jadwal WI lintas unit.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>

        <!-- User Management -->
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.users.index') }}" class="feat-card available no-underline">
            <div class="feat-card-header" style="background:linear-gradient(135deg,#1e293b 0%,#334155 100%)">
                <div class="feat-card-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="absolute top-2 right-2 h-[18px] px-2 bg-yellow-500/30 border border-yellow-400/40 rounded-full text-[9px] font-bold text-yellow-200">super_admin</span>
            </div>
            <div class="feat-card-body">
                <div class="feat-card-title">Manajemen Pengguna</div>
                <div class="feat-card-desc">Kelola akun admin per unit organisasi. Khusus super admin.</div>
            </div>
            <div class="feat-card-footer">
                <span class="badge-avail"><span class="w-1.5 h-1.5 bg-green-600 rounded-full"></span>Aktif</span>
                <span class="feat-card-cta">Buka →</span>
            </div>
        </a>
        @endif

    </div>

    <!-- ── ALUR KERJA SISTEM ──────────────────────────── -->
    <div class="grid grid-cols-[1fr_360px] gap-8 mb-10 max-[900px]:grid-cols-1 flow-grid">

        <!-- Flow Steps -->
        <div>
            <div class="flex items-baseline gap-2.5 mb-5">
                <h2 class="text-lg font-bold text-slate-900">Alur Kerja Sistem</h2>
                <span class="font-mono text-[13px] text-slate-500">6 tahap</span>
            </div>
            <div class="flex flex-col gap-1.5" id="flowSteps">
                <a href="{{ route('admin.templates.import') }}" class="flow-step no-underline">
                    <div class="flow-step-num">01</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Upload Template CSV/Excel</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Upload file CSV/Excel berisi nama aktivitas, durasi, aturan phase. Phase otomatis terbuat.</div>
                    </div>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="ml-auto shrink-0 text-sky-600"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div class="flow-step-num">02</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Buat Batch Training</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Pilih template, isi tahun, angkatan, start date, dan jumlah peserta.</div>
                    </div>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div class="flow-step-num">03</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Generate Jadwal Otomatis</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Sistem hitung tanggal per phase, skip hari libur, terapkan aturan hari kerja.</div>
                    </div>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div class="flow-step-num">04</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Assign Widyaiswara</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Pilih WI untuk setiap aktivitas. Sistem validasi konflik jadwal otomatis.</div>
                    </div>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div class="flow-step-num">05</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Conflict Checking</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Deteksi konflik seminar antar unit dan jadwal WI yang tumpang tindih.</div>
                    </div>
                </div>
                <div class="flow-arrow">↓</div>
                <div class="flow-step">
                    <div class="flow-step-num">06</div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">Export PDF</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Unduh jadwal resmi batch sebagai dokumen PDF dengan format standar.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Architecture -->
        <div>
            <div class="flex items-baseline gap-2.5 mb-5">
                <h2 class="text-lg font-bold text-slate-900">Arsitektur Sistem</h2>
            </div>
            <div class="flex flex-col gap-3" id="archCards">

                <div class="arch-card">
                    <div class="arch-title">Authentication</div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Laravel Session Authentication
                    </div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        CSRF Protection + Secure Cookies
                    </div>
                </div>

                <div class="arch-card">
                    <div class="arch-title">Authorization</div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Spatie Laravel Permission (planned)
                    </div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Roles: super_admin · admin
                    </div>
                </div>

                <div class="arch-card">
                    <div class="arch-title">Ownership</div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        organizational_unit_id per admin
                    </div>
                    <div class="arch-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83"/></svg>
                        Unit: Tekpim · Fungsional · Sekretariat · Penkom
                    </div>
                </div>

                <div class="arch-card">
                    <div class="arch-title">Conflict Engine</div>
                    <div class="arch-item" style="background:#FEF9C3; color:#713F12;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Seminar Conflict — Global (all units)
                    </div>
                    <div class="arch-item" style="background:#FEF9C3; color:#713F12;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        WI Conflict — Global (all units)
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>

<script type="module">
import { animate, stagger, createTimeline } from 'https://esm.sh/animejs@4';

// ── DATE ─────────────────────────────────────────────────
const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const now    = new Date();
document.getElementById('heroDate').textContent =
    `${DAYS[now.getDay()]}, ${now.getDate()} ${MONTHS[now.getMonth()]} ${now.getFullYear()}`;

// ── STATS COUNTER ─────────────────────────────────────────
const counter = (el, to) => {
    const obj = { val: 0 };
    animate(obj, { val: to, duration: 1200, ease: 'outExpo',
        onUpdate: () => { el.textContent = Math.round(obj.val); }
    });
};
counter(document.getElementById('statTotal'),     {{ $stats['total'] }});
counter(document.getElementById('statOpen'),      {{ $stats['opened'] }});
counter(document.getElementById('statBatches'),   {{ $stats['batches'] }});
counter(document.getElementById('statConflicts'), {{ $stats['conflicts'] }});
counter(document.getElementById('statSeats'),     {{ $stats['seats'] }});

// ── HERO ENTRANCE ─────────────────────────────────────────
const tl = createTimeline({ defaults: { ease: 'outExpo' } });
tl.add('#heroBadge',   { opacity:[0,1], translateY:[-10,0], duration:500 }, 100)
  .add('#heroTop',     { opacity:[0,1], translateY:[-16,0], duration:600 }, 180)
  .add('.stat-card',   { opacity:[0,1], translateY:[20,0], duration:450, delay:stagger(80) }, 380);

// ── BIG CARDS ─────────────────────────────────────────────
animate(['#bigCatalog','#bigCalendar'], {
    opacity:    [0,1],
    translateY: [24,0],
    duration:   500,
    ease:       'outExpo',
    delay:      stagger(100, { start: 600 })
});

// ── FEATURE CARDS ─────────────────────────────────────────
animate('.feat-card', {
    opacity:    [0,1],
    translateY: [20,0],
    scale:      [.97,1],
    duration:   420,
    ease:       'outExpo',
    delay:      stagger(55, { start: 700 })
});

// ── FLOW STEPS ────────────────────────────────────────────
animate('.flow-step', {
    opacity:    [0,1],
    translateX: [-20,0],
    duration:   380,
    ease:       'outExpo',
    delay:      stagger(80, { start: 900 })
});
animate('.flow-arrow', {
    opacity:    [0,1],
    duration:   200,
    delay:      stagger(80, { start: 950 })
});

// ── ARCH CARDS ────────────────────────────────────────────
animate('.arch-card', {
    opacity:    [0,1],
    translateY: [16,0],
    duration:   380,
    ease:       'outExpo',
    delay:      stagger(90, { start: 1000 })
});

// ── NAVBAR SHADOW ON SCROLL ────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('navbar').style.boxShadow =
        window.scrollY > 10 ? '0 2px 20px rgba(15,23,42,.10)' : 'none';
}, { passive: true });

// ── CARD HOVER BOUNCE ─────────────────────────────────────
document.querySelectorAll('.big-card, .feat-card.coming').forEach(card => {
    card.addEventListener('mouseenter', () => {
        animate(card, { scale: [1, 1.01], duration: 180, ease: 'outBack' });
    });
    card.addEventListener('mouseleave', () => {
        animate(card, { scale: [1.01, 1], duration: 180, ease: 'outBack' });
    });
});
</script>
</body>
</html>
