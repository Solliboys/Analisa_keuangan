<?php
// Ambil data konfigurasi sistem dari database jika belum diset
if (!isset($pengaturan_sistem)) {
    try {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    } catch (\Throwable $e) {
        $pengaturan_sistem = [];
    }
}

$brandName    = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Kredit';
$brandSlogan  = !empty($pengaturan_sistem['slogan_aplikasi']) ? $pengaturan_sistem['slogan_aplikasi'] : 'Sistem Analisa Kredit AI & CAM';
$brandLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Lembaga Pembiayaan';
$brandLogo    = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';
$brandEmail   = !empty($pengaturan_sistem['email_lembaga']) ? $pengaturan_sistem['email_lembaga'] : 'info@otwkredit.id';
$brandTelepon = !empty($pengaturan_sistem['telepon_lembaga']) ? $pengaturan_sistem['telepon_lembaga'] : '(021) 5088-2900';

// Salam waktu dinamis
$jam = (int) date('H');
if ($jam >= 4 && $jam < 11) {
    $salam = 'Pagi';
    $salamIcon = '🌅';
} elseif ($jam >= 11 && $jam < 15) {
    $salam = 'Siang';
    $salamIcon = '☀️';
} elseif ($jam >= 15 && $jam < 18) {
    $salam = 'Sore';
    $salamIcon = '🌇';
} else {
    $salam = 'Malam';
    $salamIcon = '🌙';
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($brandName) ?> — Masuk ke Sistem</title>
    <meta name="description" content="Masuk ke portal <?= esc($brandName) ?> - <?= esc($brandSlogan) ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= !empty($brandLogo) ? base_url($brandLogo) : base_url('favicon.ico') ?>">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Ambient Wave Animations */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(1deg); }
        }
        @keyframes pulseSoft {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.05); }
        }
        .anim-float {
            animation: floatSlow 12s ease-in-out infinite;
        }
        .anim-pulse {
            animation: pulseSoft 8s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4 sm:p-6 md:p-10 relative overflow-x-hidden selection:bg-blue-500 selection:text-white">

    <!-- Ambient Gradient Background Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Lingkaran Cahaya Lembut Atas -->
        <div class="anim-pulse absolute -top-40 -left-40 w-96 h-96 bg-blue-300/30 rounded-full blur-3xl"></div>
        <!-- Lingkaran Cahaya Kanan Bawah -->
        <div class="anim-pulse absolute -bottom-40 -right-40 w-[30rem] h-[30rem] bg-indigo-300/30 rounded-full blur-3xl" style="animation-delay: -3s;"></div>
        <!-- Garis Pola Geometris Halus -->
        <svg class="anim-float absolute inset-0 w-full h-full opacity-20" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#94a3b8" stroke-width="0.8" stroke-dasharray="2 4" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid-pattern)" />
        </svg>
    </div>

    <!-- MAIN CONTAINER CARD -->
    <div class="relative z-10 w-full max-w-4xl bg-white rounded-3xl shadow-2xl shadow-slate-200/70 border border-slate-200/80 overflow-hidden flex flex-col md:flex-row transition-all duration-300">
        
        <!-- KOLOM KIRI: Visual Branding & Value Proposition (Desktop Only - Tema Biru) -->
        <div class="hidden md:flex md:w-5/12 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 lg:p-10 text-white flex-col justify-between relative overflow-hidden shadow-inner">
            <!-- Ambient Glow Lembut Dalam Panel Kiri -->
            <div class="absolute -top-16 -left-16 w-56 h-56 bg-white/15 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-56 h-56 bg-cyan-300/25 rounded-full blur-2xl pointer-events-none"></div>
            
            <!-- Bagian Atas Panel Kiri -->
            <div class="relative z-10">
                <div class="mb-4">
                    <?php if (!empty($brandLogo)) : ?>
                        <div class="p-2.5 bg-white rounded-2xl border border-white/30 inline-block mb-3 shadow-lg shadow-blue-900/20">
                            <img src="<?= base_url($brandLogo) ?>" alt="Logo" class="h-10 w-auto object-contain">
                        </div>
                    <?php else : ?>
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-white mb-3 shadow-lg shadow-blue-900/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                    <?php endif; ?>

                    <h2 class="text-2xl font-extrabold text-white tracking-tight leading-snug drop-shadow-xs">
                        <?= esc($brandName) ?>
                    </h2>
                    <p class="text-blue-100 text-xs font-medium mt-1 leading-relaxed">
                        <?= esc($brandSlogan) ?>
                    </p>
                </div>
            </div>

            <!-- Bagian Tengah: Fitur Unggulan Berwarna Kaca Bersih (3 Kelebihan Sistem) -->
            <div class="relative z-10 space-y-3 my-6">
                <!-- Kelebihan 1: AI Scoring 5C -->
                <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">AI Credit Scoring 5C</h4>
                        <p class="text-[11px] text-blue-100 leading-relaxed">Evaluasi presisi Character, Capacity, Capital, Collateral & Condition otomatis.</p>
                    </div>
                </div>

                <!-- Kelebihan 2: Verifikasi SLIK OJK -->
                <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Verifikasi SLIK Terstandar</h4>
                        <p class="text-[11px] text-blue-100 leading-relaxed">Penyelarasan scoring kolektibilitas OJK dengan modul rekomendasi CAM.</p>
                    </div>
                </div>

                <!-- Kelebihan 3: Rekomendasi CAM & Keputusan Cepat -->
                <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-white/20 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Rekomendasi CAM & Plafon</h4>
                        <p class="text-[11px] text-blue-100 leading-relaxed">Perhitungan limit kredit, rasio angsuran, dan mitigasi risiko instan.</p>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah Panel Kiri -->
            <div class="relative z-10 pt-4 border-t border-white/20 text-[11px] text-blue-100 font-medium">
                <span class="truncate block"><?= esc($brandLembaga) ?></span>
            </div>
        </div>

        <!-- KOLOM KANAN: Formulir Login -->
        <div class="w-full md:w-7/12 p-6 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-between bg-white">
            
            <div>
                <!-- Bar Navigasi Atas Form -->
                <div class="flex items-center justify-between mb-6">
                    <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-50 hover:bg-blue-50 hover:text-blue-600 border border-slate-200/80 transition-all duration-200 group">
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>

                    <!-- Badge Status Lembaga -->
                    <span class="hidden sm:inline-flex text-[11px] font-medium text-slate-400 truncate max-w-[170px]" title="<?= esc($brandLembaga) ?>">
                        <?= esc($brandLembaga) ?>
                    </span>
                </div>

                <!-- Header Brand (Khusus Tampilan Mobile & Tablet) -->
                <div class="md:hidden flex items-center gap-3 mb-6 pb-5 border-b border-slate-100">
                    <?php if (!empty($brandLogo)) : ?>
                        <div class="p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                            <img src="<?= base_url($brandLogo) ?>" alt="Logo" class="h-9 w-auto object-contain">
                        </div>
                    <?php else : ?>
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-800 leading-tight"><?= esc($brandName) ?></h2>
                        <p class="text-[11px] text-slate-500 font-medium"><?= esc($brandSlogan) ?></p>
                    </div>
                </div>

                <!-- Judul Form & Ucapan Waktu Dinamis -->
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Masuk ke Akun</span>
                        <span class="text-xl"><?= $salamIcon ?></span>
                    </h1>
                    <p class="text-slate-500 text-sm mt-1 font-normal">
                        Selamat <?= $salam ?>. Silakan login untuk mengakses modul dan data Anda.
                    </p>
                </div>

                <!-- Notifikasi Flashdata Sukses -->
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3 shadow-xs">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-emerald-900 mb-0.5">Berhasil</p>
                            <p class="text-xs text-emerald-700 leading-relaxed"><?= session()->getFlashdata('success'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Notifikasi Flashdata Error -->
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-xs">
                        <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-rose-900 mb-0.5">Gagal Masuk</p>
                            <p class="text-xs text-rose-700 leading-relaxed"><?= session()->getFlashdata('error'); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Login -->
                <form id="loginForm" action="<?= base_url('auth/process') ?>" method="post" class="space-y-4">
                    <?= csrf_field() ?>
                    
                    <!-- Input Username -->
                    <div>
                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Username / ID Pegawai
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-3 pl-11 pr-4 text-sm text-slate-800 placeholder-slate-400 font-medium focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all duration-200" 
                                placeholder="Masukkan username Anda" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                Kata Sandi
                            </label>
                            <button 
                                type="button" 
                                onclick="handleLupaSandi()" 
                                class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors focus:outline-none"
                            >
                                Lupa sandi?
                            </button>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/60 py-3 pl-11 pr-12 text-sm text-slate-800 placeholder-slate-400 font-medium focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all duration-200" 
                                placeholder="••••••••" 
                                required
                            >
                            <!-- Tombol Toggle Show/Hide Password -->
                            <button 
                                type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none transition-colors"
                                title="Lihat/Sembunyikan Kata Sandi"
                                aria-label="Toggle password visibility"
                            >
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox Ingat Sesi -->
                    <div class="flex items-center pt-1">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer transition"
                        >
                        <label for="remember-me" class="ml-2.5 block text-xs sm:text-sm font-medium text-slate-600 cursor-pointer select-none">
                            Ingat sesi saya di perangkat ini
                        </label>
                    </div>

                    <!-- Tombol Masuk ke Sistem -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            id="btnLogin"
                            class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 active:scale-[0.99] text-white text-sm font-bold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-blue-600/25 hover:shadow-xl hover:shadow-blue-600/35 cursor-pointer"
                        >
                            <span id="btnText">Masuk ke Sistem</span>
                            <svg id="btnArrow" class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                            <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- KARTU AKUN PENGUJIAN (QUICK LOGIN 1-KLIK) -->
                <div class="mt-6 pt-5 border-t border-slate-200/80">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Pilih Akun Demo (Klik untuk Isi)</span>
                        <span class="text-[10px] bg-blue-50 text-blue-700 border border-blue-200 font-bold px-2 py-0.5 rounded-full">Dev Mode</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="quickFill('superadmin', 'admin123')" class="p-2 text-left bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl transition text-xs group cursor-pointer">
                            <div class="font-bold text-slate-800 group-hover:text-blue-700">👑 Superadmin</div>
                            <div class="text-[10px] text-slate-400 font-mono">admin123</div>
                        </button>
                        <button type="button" onclick="quickFill('pengusul', 'pengusul123')" class="p-2 text-left bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl transition text-xs group cursor-pointer">
                            <div class="font-bold text-slate-800 group-hover:text-blue-700">📝 AO Pengusul</div>
                            <div class="text-[10px] text-slate-400 font-mono">pengusul123</div>
                        </button>
                        <button type="button" onclick="quickFill('slik', 'slik123')" class="p-2 text-left bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl transition text-xs group cursor-pointer">
                            <div class="font-bold text-slate-800 group-hover:text-blue-700">🔍 Petugas SLIK</div>
                            <div class="text-[10px] text-slate-400 font-mono">slik123</div>
                        </button>
                        <button type="button" onclick="quickFill('reviewer', 'reviewer123')" class="p-2 text-left bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl transition text-xs group cursor-pointer">
                            <div class="font-bold text-slate-800 group-hover:text-blue-700">⚖️ Reviewer</div>
                            <div class="text-[10px] text-slate-400 font-mono">reviewer123</div>
                        </button>
                        <button type="button" onclick="quickFill('ceo', 'ceo123')" class="p-2 text-left bg-slate-50 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 rounded-xl transition text-xs group cursor-pointer">
                            <div class="font-bold text-slate-800 group-hover:text-blue-700">👔 Direktur CEO</div>
                            <div class="text-[10px] text-slate-400 font-mono">ceo123</div>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Footer Hak Cipta (Mengalir Normal Tanpa Bertumpuk) -->
            <div class="mt-8 pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-400 font-medium">
                    &copy; <?= date('Y') ?> <?= esc($brandLembaga ?: $brandName) ?>. Seluruh hak cipta dilindungi.
                </p>
            </div>

        </div>
    </div>

    <!-- SCRIPT CLIENT -->
    <script>
        // Fungsi Quick Fill Akun Demo
        function quickFill(user, pass) {
            const u = document.getElementById('username');
            const p = document.getElementById('password');
            if (u && p) {
                u.value = user;
                p.value = pass;
                u.classList.add('border-blue-500', 'bg-blue-50/50');
                p.classList.add('border-blue-500', 'bg-blue-50/50');
                setTimeout(() => {
                    u.classList.remove('border-blue-500', 'bg-blue-50/50');
                    p.classList.remove('border-blue-500', 'bg-blue-50/50');
                }, 800);
            }
        }
        // Toggle Show / Hide Password
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // Popup Informasi Lupa Kata Sandi
        function handleLupaSandi() {
            Swal.fire({
                icon: 'info',
                title: 'Bantuan Akses & Sandi',
                html: `
                    <div class="text-left text-xs sm:text-sm text-slate-600 space-y-3">
                        <p class="leading-relaxed">Demi alasan keamanan dan audit internal perbankan, permohonan reset kata sandi dikelola langsung oleh Administrator Sistem.</p>
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs space-y-1.5 font-medium text-slate-700">
                            <div><span class="text-slate-400">Instansi:</span> <?= esc($brandLembaga) ?></div>
                            <div><span class="text-slate-400">Email IT:</span> <a href="mailto:<?= esc($brandEmail) ?>" class="text-blue-600 hover:underline"><?= esc($brandEmail) ?></a></div>
                            <div><span class="text-slate-400">Telepon / Ext:</span> <?= esc($brandTelepon) ?></div>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-normal">Silakan hubungi IT Helpdesk dengan menyertakan Username atau Nomor Induk Pegawai Anda.</p>
                    </div>
                `,
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#2563EB',
                buttonsStyling: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 text-xs font-bold'
                }
            });
        }

        // Form Submit Loading Feedback
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnLogin');
            const text = document.getElementById('btnText');
            const arrow = document.getElementById('btnArrow');
            const spinner = document.getElementById('btnSpinner');

            // Gunakan pointer-events none agar tidak klik ganda tanpa membatalkan submit browser
            btn.style.pointerEvents = 'none';
            btn.classList.add('opacity-80');
            text.textContent = 'Memverifikasi...';
            if (arrow) arrow.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
        });
    </script>
</body>
</html>
