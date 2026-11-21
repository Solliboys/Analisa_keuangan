<?php
// Data pengaturan_sistem diambil dari controller Auth::getPengaturanSistem()
if (!isset($pengaturan_sistem)) {
    $pengaturan_sistem = [];
}

$rawNamaWeb   = !empty($pengaturan_sistem['nama_aplikasi']) ? trim($pengaturan_sistem['nama_aplikasi']) : '';
$brandName    = (empty($rawNamaWeb) || preg_match('/(credit\s*engine|cam|bpr)/i', $rawNamaWeb)) ? 'OTW Approved' : $rawNamaWeb;
$brandSlogan  = !empty($pengaturan_sistem['slogan_aplikasi']) ? trim($pengaturan_sistem['slogan_aplikasi']) : 'Platform Analisa & Persetujuan Pembiayaan';
$rawLembaga   = !empty($pengaturan_sistem['nama_lembaga']) ? trim($pengaturan_sistem['nama_lembaga']) : 'Lembaga Pembiayaan';
$brandLembaga = trim(preg_replace('/\b(BPR|CAM)\b/i', '', $rawLembaga));
$brandLembaga = trim(preg_replace('/\bPT\.?\s*\/\s*/i', '', $brandLembaga));
$brandLembaga = trim(preg_replace('/\s+/', ' ', $brandLembaga), " \t\n\r\0\x0B/");
if (empty($brandLembaga)) {
    $brandLembaga = 'Lembaga Pembiayaan';
}

$brandLogo    = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : 'img/logo-oap.png';
$brandEmail   = !empty($pengaturan_sistem['email_lembaga']) ? trim($pengaturan_sistem['email_lembaga']) : 'info@otwapproved.id';
$brandTelepon = !empty($pengaturan_sistem['telepon_lembaga']) ? trim($pengaturan_sistem['telepon_lembaga']) : '(021) 5088-2900';

// Salam waktu dinamis (tanpa emoji)
$jam = (int) date('H');
if ($jam >= 4 && $jam < 11) {
    $salam = 'pagi';
} elseif ($jam >= 11 && $jam < 15) {
    $salam = 'siang';
} elseif ($jam >= 15 && $jam < 18) {
    $salam = 'sore';
} else {
    $salam = 'malam';
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — <?= esc($brandName) ?></title>
    <meta name="description" content="Masuk ke portal <?= esc($brandName) ?> — <?= esc($brandSlogan) ?>">

    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= !empty($brandLogo) ? base_url($brandLogo) : base_url('favicon.ico') ?>">
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        .bg-grid-subtle {
            background-image:
                linear-gradient(to right, rgba(226, 232, 240, 0.6) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.6) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .auth-input {
            width: 100%;
            height: 40px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        .auth-input::placeholder { color: #94a3b8; font-weight: 400; }
        .auth-input:hover { border-color: #94a3b8; }
        .auth-input:focus {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-white text-slate-900 selection:bg-blue-100 selection:text-blue-900">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- LEFT COLUMN: LOGIN FORM -->
        <div class="lg:col-span-7 flex flex-col justify-between px-5 sm:px-10 py-6 sm:py-8 bg-white">
            <!-- Top Bar -->
            <div class="flex items-center justify-between gap-4 max-w-xl w-full mx-auto">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 shrink-0">
                    <img src="<?= base_url($brandLogo) ?>" alt="<?= esc($brandName) ?>" class="h-7 w-7 object-contain">
                    <span class="text-sm font-bold text-slate-950 tracking-tight"><?= esc($brandName) ?></span>
                </a>
                <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-950 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            <!-- Centered Form Container -->
            <div class="w-full max-w-[380px] mx-auto my-10">
                <div class="mb-6">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-950 tracking-[-0.02em]">
                        Masuk ke Portal
                    </h1>
                    <p class="text-xs sm:text-[13px] text-slate-500 mt-1 leading-relaxed">
                        Selamat <?= $salam ?>. Masukkan kredensial akun Anda untuk mengakses sistem analisa pembiayaan.
                    </p>
                </div>

                <!-- Flashdata Sukses -->
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="mb-4 px-3.5 py-2.5 bg-emerald-50 border border-emerald-200 rounded-md text-xs text-emerald-800 leading-relaxed">
                        <strong class="font-semibold text-emerald-900">Berhasil:</strong>
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <!-- Flashdata Error -->
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="mb-4 px-3.5 py-2.5 bg-rose-50 border border-rose-200 rounded-md text-xs text-rose-800 leading-relaxed">
                        <strong class="font-semibold text-rose-900">Gagal masuk:</strong>
                        <?= session()->getFlashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="loginForm" action="<?= base_url('auth/process') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label for="username" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Username / ID Pegawai
                        </label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="auth-input"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-medium text-slate-700">
                                Kata Sandi
                            </label>
                            <button
                                type="button"
                                onclick="handleLupaSandi()"
                                class="text-xs font-medium text-blue-700 hover:text-blue-800 hover:underline cursor-pointer"
                            >
                                Lupa sandi?
                            </button>
                        </div>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="auth-input pr-10"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer"
                                title="Lihat/Sembunyikan Kata Sandi"
                                aria-label="Toggle password visibility"
                            >
                                <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center pt-0.5">
                        <input
                            id="remember-me"
                            name="remember-me"
                            type="checkbox"
                            class="h-3.5 w-3.5 rounded border-slate-300 text-blue-700 focus:ring-blue-700 cursor-pointer"
                        >
                        <label for="remember-me" class="ml-2 block text-xs text-slate-600 cursor-pointer select-none">
                            Ingat sesi di perangkat ini
                        </label>
                    </div>

                    <div class="pt-1">
                        <button
                            type="submit"
                            id="btnLogin"
                            class="w-full h-10 inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white text-xs sm:text-[13px] font-semibold rounded-md transition cursor-pointer"
                        >
                            <span id="btnText">Masuk ke Portal</span>
                            <svg id="btnArrow" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <svg id="btnSpinner" class="hidden animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Akun Pengujian (Quick Fill) -->
                <div class="mt-7 pt-5 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Akun Uji Coba</span>
                        <span class="text-[10px] font-medium text-slate-400">Klik untuk isi otomatis</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                        <button type="button" onclick="quickFill('superadmin', 'admin123')" class="px-2.5 py-2 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md transition cursor-pointer">
                            <div class="text-[11px] font-semibold text-slate-800">Superadmin</div>
                            <div class="text-[10px] text-slate-400 font-mono">superadmin</div>
                        </button>
                        <button type="button" onclick="quickFill('pengusul', 'pengusul123')" class="px-2.5 py-2 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md transition cursor-pointer">
                            <div class="text-[11px] font-semibold text-slate-800">Pengusul</div>
                            <div class="text-[10px] text-slate-400 font-mono">pengusul</div>
                        </button>
                        <button type="button" onclick="quickFill('slik', 'slik123')" class="px-2.5 py-2 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md transition cursor-pointer">
                            <div class="text-[11px] font-semibold text-slate-800">Petugas SLIK</div>
                            <div class="text-[10px] text-slate-400 font-mono">slik</div>
                        </button>
                        <button type="button" onclick="quickFill('reviewer', 'reviewer123')" class="px-2.5 py-2 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md transition cursor-pointer">
                            <div class="text-[11px] font-semibold text-slate-800">Reviewer</div>
                            <div class="text-[10px] text-slate-400 font-mono">reviewer</div>
                        </button>
                        <button type="button" onclick="quickFill('ceo', 'ceo123')" class="px-2.5 py-2 text-left bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md transition cursor-pointer">
                            <div class="text-[11px] font-semibold text-slate-800">Direktur</div>
                            <div class="text-[10px] text-slate-400 font-mono">ceo</div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="max-w-xl w-full mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400 pt-4 border-t border-slate-100">
                <span>&copy; <?= date('Y') ?> <?= esc($brandName) ?> &middot; <?= esc($brandLembaga) ?></span>
                <a href="<?= base_url('simulasi') ?>" class="text-slate-500 hover:text-blue-700 font-medium transition">Coba Simulasi Publik &rarr;</a>
            </div>
        </div>

        <!-- RIGHT COLUMN: ARCHITECTURAL BRAND RAIL (Desktop Only) -->
        <aside class="hidden lg:flex lg:col-span-5 bg-slate-50 border-l border-slate-200 relative overflow-hidden flex-col justify-between p-10 xl:p-12">
            <div class="absolute inset-0 bg-grid-subtle opacity-60 pointer-events-none"></div>

            <div class="relative z-10 flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Portal Internal</span>
                <span class="text-[11px] font-medium text-slate-400"><?= esc($brandLembaga) ?></span>
            </div>

            <div class="relative z-10 space-y-6 my-auto py-8">
                <div class="w-11 h-11 rounded-lg bg-white border border-slate-200 p-2 flex items-center justify-center">
                    <img src="<?= base_url($brandLogo) ?>" alt="<?= esc($brandName) ?>" class="w-full h-full object-contain">
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-slate-950 tracking-[-0.02em] leading-snug">
                        Satu alur kerja terpadu untuk persetujuan pembiayaan.
                    </h2>
                    <p class="text-[13px] text-slate-600 leading-relaxed">
                        <?= esc($brandName) ?> menyatukan pengajuan debitur, pengecekan SLIK, pembacaan rekening koran, dan keputusan komite dalam satu jejak audit yang transparan.
                    </p>
                </div>

                <!-- Minimalist Workflow Ledger -->
                <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-200 text-xs">
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-[11px] font-semibold text-slate-400 tabular-nums">01</span>
                            <span class="font-semibold text-slate-900">Pengusul</span>
                        </div>
                        <span class="text-[11px] text-slate-500 truncate">Input berkas, agunan &amp; mutasi</span>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-[11px] font-semibold text-slate-400 tabular-nums">02</span>
                            <span class="font-semibold text-slate-900">Petugas SLIK</span>
                        </div>
                        <span class="text-[11px] text-slate-500 truncate">Verifikasi kolektibilitas &amp; KTP</span>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-[11px] font-semibold text-slate-400 tabular-nums">03</span>
                            <span class="font-semibold text-slate-900">Reviewer</span>
                        </div>
                        <span class="text-[11px] text-slate-500 truncate">Analisa 5C, DSCR &amp; mitigasi</span>
                    </div>
                    <div class="px-4 py-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-[11px] font-semibold text-blue-700 tabular-nums">04</span>
                            <span class="font-semibold text-slate-900">Direktur / Komite</span>
                        </div>
                        <span class="text-[11px] text-slate-500 truncate">Keputusan final &amp; cetak A4</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 flex items-center justify-between text-[11px] text-slate-500 pt-4 border-t border-slate-200/80">
                <span><?= esc($brandEmail) ?></span>
                <span><?= esc($brandTelepon) ?></span>
            </div>
        </aside>

    </div>

    <script>
        function quickFill(user, pass) {
            const u = document.getElementById('username');
            const p = document.getElementById('password');
            if (u && p) {
                u.value = user;
                p.value = pass;
                u.focus();
            }
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>`;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`;
            }
        }

        function handleLupaSandi() {
            Swal.fire({
                title: 'Bantuan Akses & Sandi',
                html: `
                    <div class="text-left text-xs text-slate-600 space-y-3">
                        <p class="leading-relaxed">Untuk menjaga keamanan dan audit internal, reset kata sandi dikelola langsung oleh Administrator Sistem.</p>
                        <div class="bg-slate-50 p-3 rounded-md border border-slate-200 text-xs space-y-1 text-slate-700">
                            <div><span class="text-slate-400">Instansi:</span> <?= esc($brandLembaga) ?></div>
                            <div><span class="text-slate-400">Email IT:</span> <a href="mailto:<?= esc($brandEmail) ?>" class="text-blue-700 hover:underline"><?= esc($brandEmail) ?></a></div>
                            <div><span class="text-slate-400">Telepon:</span> <?= esc($brandTelepon) ?></div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#1D4ED8',
                customClass: {
                    popup: 'rounded-lg',
                    confirmButton: 'rounded-md px-5 py-2 text-xs font-semibold'
                }
            });
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('btnLogin');
            const text = document.getElementById('btnText');
            const arrow = document.getElementById('btnArrow');
            const spinner = document.getElementById('btnSpinner');

            btn.style.pointerEvents = 'none';
            btn.classList.add('opacity-80');
            text.textContent = 'Memverifikasi...';
            if (arrow) arrow.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
        });
    </script>
</body>
</html>
