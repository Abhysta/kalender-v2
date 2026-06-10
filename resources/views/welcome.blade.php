<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BPSDM Kalender — Sistem Pelatihan ASN</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 flex items-stretch min-h-screen max-[900px]:flex-col">

<!-- LEFT — PUBLIC ACCESS -->
<div id="leftPanel" class="anim-fade-up pattern-bg flex-1 bg-gradient-to-br from-slate-900 via-[#1e3a5f] to-sky-700 flex flex-col justify-center px-16 py-[60px] relative overflow-hidden max-[900px]:px-8 max-[900px]:py-9 max-[900px]:justify-start max-[600px]:px-5 max-[600px]:py-7 max-[400px]:px-4 max-[400px]:py-6">
    <div class="absolute bottom-[-40px] right-[-40px] w-[280px] h-[280px] border-2 border-white/[0.08] rounded-[32px] opacity-40 rotate-[15deg] max-[900px]:hidden"></div>
    <div class="absolute top-[-60px] right-[80px] w-[160px] h-[160px] border-2 border-white/[0.06] rounded-[20px] opacity-30 rotate-[-10deg] max-[900px]:hidden"></div>

    <div class="relative max-w-[440px] max-[900px]:max-w-none">
        <div class="flex items-center gap-3 mb-12 max-[900px]:mb-7 max-[600px]:mb-5">
            <div class="w-11 h-11 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center max-[600px]:w-[38px] max-[600px]:h-[38px]">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="text-white">
                <div class="font-mono text-base font-semibold leading-none max-[400px]:text-[14px]">BPSDM Kalender</div>
                <div class="text-xs opacity-65 mt-0.5">Sistem Pelatihan ASN</div>
            </div>
        </div>

        <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-white leading-tight mb-3.5 max-[900px]:text-[clamp(22px,5vw,32px)] max-[600px]:text-2xl">
            Jadwal Pelatihan<br>ASN 2026
        </h1>
        <p class="text-[15px] text-white/70 leading-[1.7] mb-10 max-w-[380px] max-[900px]:max-w-none max-[900px]:text-sm max-[900px]:mb-6 max-[600px]:text-[13px] max-[600px]:leading-relaxed max-[600px]:mb-5">
            Temukan dan pantau seluruh program pelatihan aparatur sipil negara. Akses katalog dan kalender tanpa perlu login.
        </p>

        <div class="bg-white/10 border border-white/[0.18] rounded-2xl p-6 backdrop-blur-sm max-[900px]:p-5 max-[600px]:p-4 max-[600px]:rounded-xl">
            <div class="text-[13px] font-semibold text-white/60 uppercase tracking-wider mb-2">Akses Publik</div>
            <div class="text-sm text-white/85 mb-5 leading-relaxed max-[600px]:text-[13px] max-[600px]:mb-3.5">
                Lihat katalog pelatihan dan kalender jadwal tanpa perlu membuat akun.
            </div>
            <div class="flex gap-2.5 flex-wrap items-center max-[600px]:gap-2">
                <a href="/catalog" class="inline-flex items-center gap-2 h-11 px-[22px] bg-white text-slate-900 rounded-[10px] text-sm font-bold no-underline border-none cursor-pointer shadow-[0_4px_16px_rgba(0,0,0,.2)] transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_8px_24px_rgba(0,0,0,.28)] max-[600px]:text-[13px] max-[600px]:px-[18px]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Lihat Katalog
                </a>
                <a href="/calendar" class="inline-flex items-center gap-1.5 h-11 px-4 bg-white/[0.08] border border-white/[0.18] text-white/85 rounded-[10px] text-[13px] font-medium no-underline cursor-pointer transition-all duration-180 hover:bg-white/15 hover:text-white">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Lihat Kalender
                </a>
            </div>
        </div>
    </div>
</div>

<!-- RIGHT — ADMIN LOGIN -->
<div id="rightPanel" class="anim-fade-right w-[440px] shrink-0 flex items-center justify-center px-12 py-10 bg-white border-l border-slate-200 max-[900px]:w-full max-[900px]:px-8 max-[900px]:py-9 max-[900px]:border-l-0 max-[900px]:border-t max-[600px]:px-5 max-[600px]:py-7 max-[400px]:px-4 max-[400px]:py-6">
    <div class="w-full max-w-[340px] max-[900px]:max-w-[480px] max-[900px]:mx-auto">
        <div class="mb-8 max-[600px]:mb-6">
            <div class="inline-flex items-center gap-1.5 bg-sky-100 text-sky-700 rounded-full px-3 py-1 text-xs font-semibold mb-3.5">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Portal Admin
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-1.5 max-[600px]:text-xl">Masuk sebagai Admin</h2>
            <p class="text-sm text-slate-500">Kelola data pelatihan dan jadwal kalender.</p>
        </div>

        @if ($errors->any())
        <div class="flex items-start gap-2.5 bg-red-50 border border-red-200 rounded-lg px-3.5 py-3 mb-5 text-[13px] text-red-800">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shrink-0 mt-px">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="/login" id="loginForm">
            @csrf

            <div class="mb-[18px]">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5" for="email">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full h-11 bg-slate-50 border-[1.5px] {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200' }} rounded-lg px-3 text-base font-sans text-slate-900 outline-none transition-all duration-200 focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)] focus:bg-white"
                    value="{{ old('email') }}"
                    placeholder="admin@bpsdm.go.id"
                    autocomplete="email"
                    required>
            </div>

            <div class="mb-[18px]">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5" for="password">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="w-full h-11 bg-slate-50 border-[1.5px] {{ $errors->has('email') ? 'border-red-500' : 'border-slate-200' }} rounded-lg px-3 pr-11 text-base font-sans text-slate-900 outline-none transition-all duration-200 focus:border-sky-700 focus:shadow-[0_0_0_3px_rgba(3,105,161,.12)] focus:bg-white"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required>
                    <button type="button" id="togglePwd" aria-label="Tampilkan password"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 bg-transparent border-none p-0 flex items-center cursor-pointer transition-colors hover:text-slate-700">
                        <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mb-[22px]">
                <label class="flex items-center gap-1.5 text-[13px] text-slate-500 cursor-pointer">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} class="accent-sky-700 w-3.5 h-3.5">
                    Ingat saya
                </label>
            </div>

            <button type="submit" id="loginBtn"
                class="w-full h-11 bg-sky-700 text-white border-none rounded-[10px] text-sm font-bold font-sans cursor-pointer transition-all flex items-center justify-center gap-2 hover:bg-sky-600 hover:shadow-[0_4px_16px_rgba(3,105,161,.3)] active:scale-[.98]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                Masuk
            </button>
        </form>

        <div class="flex items-center gap-3 my-6 text-xs text-slate-500 before:content-[''] before:flex-1 before:h-px before:bg-slate-200 after:content-[''] after:flex-1 after:h-px after:bg-slate-200 max-[600px]:my-[18px]">
            kredensial demo
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-3 text-xs text-slate-500 leading-relaxed">
            <strong class="text-slate-700">Email:</strong> admin@bpsdm.go.id<br>
            <strong class="text-slate-700">Password:</strong> admin123
        </div>
    </div>
</div>

<script>
const pwdInput   = document.getElementById('password');
const toggleBtn  = document.getElementById('togglePwd');
const eyeIcon    = document.getElementById('eyeIcon');
const eyeOffPath = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
const eyeOnPath  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;

toggleBtn.addEventListener('click', () => {
    const show = pwdInput.type === 'password';
    pwdInput.type     = show ? 'text' : 'password';
    eyeIcon.innerHTML = show ? eyeOffPath : eyeOnPath;
});

document.getElementById('loginForm').addEventListener('submit', () => {
    const btn = document.getElementById('loginBtn');
    btn.style.opacity       = '.7';
    btn.style.pointerEvents = 'none';
    btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="anim-spin"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Memproses...`;
});
</script>
</body>
</html>
