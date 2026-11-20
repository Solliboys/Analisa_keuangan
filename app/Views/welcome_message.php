<?php
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit AI';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Lembaga Pembiayaan';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';
    $sloganWeb   = !empty($pengaturan_sistem['slogan_aplikasi']) ? $pengaturan_sistem['slogan_aplikasi'] : 'Autonomous Credit Underwriting AI';
    $emailWeb    = !empty($pengaturan_sistem['email_lembaga']) ? $pengaturan_sistem['email_lembaga'] : 'info@otwkredit.id';
    $teleponWeb  = !empty($pengaturan_sistem['telepon_lembaga']) ? $pengaturan_sistem['telepon_lembaga'] : '(021) 5088-2900';
    $alamatWeb   = !empty($pengaturan_sistem['alamat_lembaga']) ? $pengaturan_sistem['alamat_lembaga'] : 'Financial Center, Jakarta Selatan';
    $modelAi     = !empty($pengaturan_ai['model_ai']) ? $pengaturan_ai['model_ai'] : 'Gemini AI';

    $statKreditCount = !empty($total_kredit) && $total_kredit > 0 ? number_format($total_kredit, 0, ',', '.') . '+' : '61.000+';
    $statPlafonTotal = !empty($total_plafon) && $total_plafon > 0 ? 'Rp ' . number_format($total_plafon / 1000000000, 1, ',', '.') . ' Miliar' : 'Rp 48.5 Miliar';
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="web_standard" name="shell-type"/>
    <title><?= esc($namaWeb) ?> - Autonomous Underwriting AI &amp; Evaluasi 5C</title>
    <meta name="description" content="Platform Credit Engine dan Analisis Kelayakan Kredit modern berbasis AI 5C terintegrasi SLIK OJK untuk Lembaga Keuangan dan Pembiayaan.">

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link rel="shortcut icon" type="image/png" href="<?= !empty($logoWeb) ? base_url($logoWeb) : base_url('favicon.ico') ?>">

    <style>
        @layer base {
          html, body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fafcff;
            color: #0f172a;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
          }
        }
        ::-webkit-scrollbar { display: none; }

        @keyframes floatSlow {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          50% {
            transform: translateY(-8px) rotate(0.4deg);
          }
        }

        @keyframes floatMedium {
          0%, 100% {
            transform: translateY(0px);
          }
          50% {
            transform: translateY(-11px);
          }
        }

        @keyframes floatSlight {
          0%, 100% {
            transform: translateY(0px);
          }
          50% {
            transform: translateY(-6px);
          }
        }

        @keyframes pulseGlow {
          0%, 100% {
            opacity: 0.6;
            transform: scale(1);
          }
          50% {
            opacity: 0.85;
            transform: scale(1.04);
          }
        }

        .animate-float-slow {
          animation: floatSlow 6s ease-in-out infinite;
        }
        .animate-float-medium {
          animation: floatMedium 5s ease-in-out infinite 0.7s;
        }
        .animate-float-slight {
          animation: floatSlight 7s ease-in-out infinite 1.4s;
        }
        .animate-pulse-glow {
          animation: pulseGlow 8s ease-in-out infinite alternate;
        }

        .glass-card {
          background: rgba(255, 255, 255, 0.88);
          backdrop-filter: blur(16px);
          -webkit-backdrop-filter: blur(16px);
          border: 1px solid rgba(226, 232, 240, 0.85);
          box-shadow: 0 10px 30px -5px rgba(27, 43, 85, 0.07), 0 4px 12px -2px rgba(0, 0, 0, 0.03);
        }

        .glass-card:hover {
          box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.14), 0 8px 16px -4px rgba(0, 0, 0, 0.04);
          border-color: rgba(147, 197, 253, 0.8);
        }
    </style>
    
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <script>
        if (typeof tailwind === 'undefined') {
            document.write('<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"><\/script>');
        }
    </script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
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
                },
                slate: {
                  50: '#f8fafc',
                  100: '#f1f5f9',
                  200: '#e2e8f0',
                  300: '#cbd5e1',
                  400: '#94a3b8',
                  500: '#64748b',
                  600: '#475569',
                  700: '#334155',
                  800: '#1e293b',
                  900: '#0f172a',
                }
              },
              borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
              },
              boxShadow: {
                'subtle': '0 2px 10px rgba(0, 0, 0, 0.03), 0 10px 30px rgba(0, 0, 0, 0.04)',
                'card': '0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
                'float': '0 20px 40px -15px rgba(37, 99, 235, 0.12)',
                'blue-glow': '0 10px 25px -5px rgba(37, 99, 235, 0.25)',
              }
            }
          }
        };
    </script>
</head>
<body class="bg-[#fafcff] text-slate-900 antialiased overflow-x-hidden selection:bg-brand-500/10 selection:text-brand-600">

<!-- Ambient Light Mesh Background with Exact Color Distribution -->
<div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    <!-- Soft top center illumination -->
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[1200px] h-[550px] bg-gradient-to-b from-blue-100/50 via-sky-50/30 to-transparent rounded-[100%] blur-3xl animate-pulse-glow"></div>
    <!-- Bottom-left intense cyan/vibrant blue atmospheric glow -->
    <div class="absolute top-[480px] -left-36 w-[580px] h-[580px] bg-gradient-to-tr from-cyan-400/35 via-blue-500/25 to-transparent rounded-full blur-[130px]"></div>
    <!-- Bottom-right soft purple / violet atmospheric glow -->
    <div class="absolute top-[520px] -right-36 w-[600px] h-[600px] bg-gradient-to-tl from-indigo-400/25 via-purple-300/25 to-transparent rounded-full blur-[140px]"></div>
    <!-- Mid-canvas ambient fill -->
    <div class="absolute top-[1600px] -left-48 w-[500px] h-[500px] bg-brand-50/60 rounded-full blur-[140px]"></div>
</div>

<!-- STICKY APPTICS-STYLE HEADER -->
<header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 transition-all">
    <div class="max-w-7xl mx-auto h-20 px-6 sm:px-8 flex items-center justify-between">
        <!-- Logo -->
        <a class="flex items-center gap-3 group" href="<?= base_url() ?>">
            <?php if (!empty($logoWeb)): ?>
                <img src="<?= base_url($logoWeb) ?>" alt="Logo" class="w-10 h-10 object-contain rounded-2xl shadow-md group-hover:scale-105 transition-transform">
            <?php else: ?>
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-[22px]">auto_awesome</span>
                </div>
            <?php endif; ?>
            <div class="flex flex-col">
                <span class="text-[17px] font-extrabold tracking-tight text-slate-900 leading-tight"><?= esc($namaWeb) ?><span class="text-brand-600">.</span></span>
                <span class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">AI 5C Credit Engine</span>
            </div>
        </a>
        <!-- Clean Center Navigation -->
        <nav class="hidden md:flex items-center gap-1 bg-slate-100/80 p-1.5 rounded-full border border-slate-200/70 shadow-inner">
            <a class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 rounded-full hover:bg-white transition-all" href="#fitur">Fitur</a>
            <a class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 rounded-full hover:bg-white transition-all" href="#alur-kerja">Alur Kerja</a>
            <a class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 rounded-full hover:bg-white transition-all" href="#simulasi">Kalkulator</a>
            <a class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 rounded-full hover:bg-white transition-all" href="#komparasi">Keunggulan</a>
            <a class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-900 rounded-full hover:bg-white transition-all" href="#faq">FAQ</a>
        </nav>
        <!-- Right Header Actions -->
        <div class="flex items-center gap-3">
            <a class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-slate-900 text-white hover:bg-brand-600 font-semibold text-sm transition-all duration-200 shadow-sm hover:shadow-blue-glow" href="<?= base_url('auth') ?>">
                <span>Masuk Portal</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</header>

<main class="relative z-10 pt-28 sm:pt-32">
    <!-- HERO SECTION WITH INSPIRATION GRAPHICS & FLOATING METRIC CARDS -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 pt-6 pb-20 text-center flex flex-col items-center relative">
        <!-- Top Pill Tag -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/90 backdrop-blur border border-blue-200/80 shadow-sm hover:border-brand-300 transition-colors mb-6 cursor-default">
            <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Engine AI 5C • Autonomous Underwriting</span>
            <span class="text-slate-300 font-light">|</span>
            <span class="text-xs font-semibold text-brand-600"><?= esc($modelAi) ?> Online</span>
        </div>
        <!-- Main Headline -->
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 max-w-4xl leading-[1.12] mb-6">
            Ubah Analisis Kredit Manual Menjadi <span class="bg-gradient-to-r from-brand-600 via-sky-500 to-indigo-600 bg-clip-text text-transparent">Presisi &amp; Otomatis</span>
        </h1>
        <!-- Subheadline -->
        <p class="text-base sm:text-xl text-slate-500 max-w-2xl leading-relaxed mb-8">
            Otomasi penyusunan <span class="text-slate-800 font-semibold">Laporan Analisis Kredit</span> dan scoring 5C dalam hitungan detik. Terintegrasi SLIK OJK real-time untuk Institusi &amp; Lembaga Pembiayaan.
        </p>
        <!-- CTA Buttons -->
        <div class="relative z-20 flex flex-col sm:flex-row items-center gap-4 mb-8">
            <a class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-brand-600 hover:bg-brand-700 text-white font-bold text-base transition-all duration-200 shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 flex items-center justify-center gap-2 group" href="<?= base_url('auth') ?>">
                <span>Uji Coba Sandbox Sekarang</span>
                <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">bolt</span>
            </a>
            <a class="w-full sm:w-auto px-7 py-3.5 rounded-full bg-white text-slate-700 hover:text-brand-600 border border-slate-200/90 hover:border-slate-300 font-semibold text-base transition-all shadow-sm flex items-center justify-center gap-2" href="#alur-kerja">
                <span class="material-symbols-outlined text-[20px] text-slate-400">play_circle</span>
                <span>Lihat Cara Kerja</span>
            </a>
        </div>

        <!-- HERO VISUAL COMPOSITION: CURVED CONNECTING SVG LINES & FLOATING CARDS -->
        <div class="relative w-full max-w-5xl mt-2 min-h-[580px] flex items-center justify-center">
            <!-- SVG CURVED CONNECTING LINES -->
            <svg class="absolute inset-0 w-full h-full pointer-events-none hidden md:block z-10" fill="none" viewbox="0 0 1024 600" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <lineargradient gradientunits="userSpaceOnUse" id="curveGradLeftTop" x1="512" x2="160" y1="20" y2="70">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.5"></stop>
                        <stop offset="100%" stop-color="#93c5fd" stop-opacity="0.15"></stop>
                    </lineargradient>
                    <lineargradient gradientunits="userSpaceOnUse" id="curveGradLeftBottom" x1="512" x2="200" y1="20" y2="340">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.6"></stop>
                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="0.2"></stop>
                    </lineargradient>
                    <lineargradient gradientunits="userSpaceOnUse" id="curveGradRightTop" x1="512" x2="860" y1="20" y2="75">
                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.6"></stop>
                        <stop offset="100%" stop-color="#c084fc" stop-opacity="0.15"></stop>
                    </lineargradient>
                    <lineargradient gradientunits="userSpaceOnUse" id="curveGradRightBottom" x1="512" x2="820" y1="20" y2="340">
                        <stop offset="0%" stop-color="#6366f1" stop-opacity="0.5"></stop>
                        <stop offset="100%" stop-color="#818cf8" stop-opacity="0.2"></stop>
                    </lineargradient>
                </defs>
                <!-- Path to Top Left -->
                <path d="M 512 25 C 440 25, 280 40, 190 75" stroke="url(#curveGradLeftTop)" stroke-dasharray="3 3" stroke-width="1.8"></path>
                <!-- Path to Bottom Left -->
                <path d="M 512 25 C 420 50, 260 160, 215 310" stroke="url(#curveGradLeftBottom)" stroke-linecap="round" stroke-width="2"></path>
                <!-- Path to Top Right -->
                <path d="M 512 25 C 590 25, 740 40, 830 75" stroke="url(#curveGradRightTop)" stroke-dasharray="3 3" stroke-width="1.8"></path>
                <!-- Path to Bottom Right -->
                <path d="M 512 25 C 600 50, 760 160, 805 310" stroke="url(#curveGradRightBottom)" stroke-linecap="round" stroke-width="2"></path>
                <!-- Anchor glowing dots -->
                <circle cx="190" cy="75" fill="#3b82f6" r="3.5"></circle>
                <circle cx="215" cy="310" fill="#38bdf8" r="3.5"></circle>
                <circle cx="830" cy="75" fill="#a855f7" r="3.5"></circle>
                <circle cx="805" cy="310" fill="#6366f1" r="3.5"></circle>
            </svg>

            <!-- AMBIENT BACKDROPS -->
            <div class="absolute -left-12 bottom-4 w-72 h-72 bg-gradient-to-tr from-cyan-400/40 via-sky-400/30 to-transparent rounded-full blur-3xl -z-10 pointer-events-none"></div>
            <div class="absolute -right-12 bottom-4 w-72 h-72 bg-gradient-to-tl from-purple-400/35 via-indigo-400/25 to-transparent rounded-full blur-3xl -z-10 pointer-events-none"></div>
            <div class="absolute inset-x-20 top-1/3 bottom-0 bg-blue-100/20 rounded-[2.5rem] blur-2xl -z-20 pointer-events-none"></div>

            <!-- FLOATING CARD 1: TOP LEFT -->
            <div class="absolute -top-4 left-0 md:left-2 z-30 animate-float-slow hidden sm:flex">
                <div class="glass-card rounded-2xl px-4 py-3 flex items-center gap-3.5 shadow-lg border border-slate-200/90 transition-all hover:scale-105">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-400 via-rose-500 to-indigo-600 flex items-center justify-center text-white shadow-sm shrink-0">
                        <span class="material-symbols-outlined text-[20px]">hub</span>
                    </div>
                    <div class="text-left">
                        <div class="text-[11px] font-semibold text-slate-500 leading-tight">Total Fasilitas Teranalisis</div>
                        <div class="text-base font-extrabold text-slate-900 tracking-tight"><?= esc($statKreditCount) ?> <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full">100% SLIK</span></div>
                    </div>
                </div>
            </div>

            <!-- FLOATING CARD 2: TOP RIGHT -->
            <div class="absolute -top-4 right-0 md:right-2 z-30 animate-float-medium hidden sm:flex">
                <div class="glass-card rounded-2xl px-4 py-3 flex items-center gap-3.5 shadow-lg border border-slate-200/90 transition-all hover:scale-105">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center text-white shadow-sm shrink-0">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-extrabold text-slate-900">Komite Approval</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-[11px] text-slate-500">Auto-Pass Level 1</div>
                        <div class="flex items-center gap-1 mt-1">
                            <span class="h-1.5 w-8 bg-brand-500 rounded-full"></span>
                            <span class="h-1.5 w-4 bg-indigo-300 rounded-full"></span>
                            <span class="text-[9px] font-bold text-slate-400">4-Eyes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FLOATING CARD 3: BOTTOM LEFT -->
            <div class="absolute bottom-6 left-0 md:-left-4 z-30 animate-float-medium hidden md:block">
                <div class="glass-card rounded-2xl p-4 w-60 text-left transition-all hover:scale-105">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-800">Plafon Disbursed</span>
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Bulanan</span>
                    </div>
                    <!-- Mini Bar Chart -->
                    <div class="h-20 flex items-end justify-between gap-2 px-1">
                        <div class="w-4 bg-purple-200 rounded-t-md h-[45%] transition-all hover:bg-brand-500"></div>
                        <div class="w-4 bg-purple-300 rounded-t-md h-[70%] transition-all hover:bg-brand-500"></div>
                        <div class="w-4 bg-purple-200 rounded-t-md h-[55%] transition-all hover:bg-brand-500"></div>
                        <div class="w-4 bg-gradient-to-t from-brand-600 to-blue-400 rounded-t-md h-[90%] shadow-sm transition-all"></div>
                        <div class="w-4 bg-purple-200 rounded-t-md h-[60%] transition-all hover:bg-brand-500"></div>
                        <div class="w-4 bg-purple-300 rounded-t-md h-[80%] transition-all hover:bg-brand-500"></div>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px] font-bold">
                        <span class="text-slate-400">Pertumbuhan</span>
                        <span class="text-emerald-600 flex items-center gap-0.5"><span class="material-symbols-outlined text-[13px]">arrow_upward</span> +32.4%</span>
                    </div>
                </div>
            </div>

            <!-- FLOATING CARD 4: BOTTOM RIGHT -->
            <div class="absolute bottom-6 right-0 md:-right-4 z-30 animate-float-slow hidden md:block">
                <div class="glass-card rounded-2xl p-4 w-60 text-left relative transition-all hover:scale-105">
                    <!-- Mini Daily Badge floating on top of this card -->
                    <div class="absolute -top-4 -right-2 bg-white border border-slate-200/80 rounded-xl px-2.5 py-1.5 shadow-md flex items-center gap-2">
                        <div class="text-[10px] text-slate-400 font-bold uppercase">Akurasi AI</div>
                        <div class="text-xs font-black text-brand-600">99.4%</div>
                    </div>
                    <div class="text-xs font-bold text-slate-800 mb-2">Statistik Risiko Portofolio</div>
                    <div class="flex items-center gap-3">
                        <!-- SVG Donut Chart -->
                        <div class="relative w-14 h-14 shrink-0">
                            <svg class="w-full h-full transform -rotate-90" viewbox="0 0 36 36">
                                <circle cx="18" cy="18" fill="none" r="14" stroke="#e2e8f0" stroke-width="4"></circle>
                                <circle cx="18" cy="18" fill="none" r="14" stroke="#2563eb" stroke-dasharray="88 100" stroke-dashoffset="0" stroke-width="4"></circle>
                                <circle cx="18" cy="18" fill="none" r="14" stroke="#c084fc" stroke-dasharray="12 100" stroke-dashoffset="-88" stroke-width="4"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[10px] font-extrabold text-slate-800">Kol 1</span>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
                                <span class="w-2 h-2 rounded-full bg-brand-600"></span> 94% Lancar
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span> NPL &lt; 0.8%
                            </div>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center gap-1">
                        <span class="h-1.5 w-12 bg-emerald-500 rounded-full"></span>
                        <span class="h-1.5 w-6 bg-brand-400 rounded-full"></span>
                        <span class="h-1.5 w-3 bg-purple-300 rounded-full"></span>
                    </div>
                </div>
            </div>

            <!-- MAIN CENTER CARD: DATA ANALYTICS & SCORING TREND -->
            <div class="w-full max-w-2xl bg-white/95 backdrop-blur-xl rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-2xl relative z-20 text-left">
                <!-- Header of Mockup Card -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <div class="text-[11px] font-bold text-brand-600 uppercase tracking-wider">AI Credit Engine Dashboard</div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">Data Analytics &amp; Scoring 5C Trend</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Scoring
                        </span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                            PT Surya Sentosa Jaya
                        </span>
                    </div>
                </div>
                <!-- Multi-line Smooth Wave Chart SVG with Tooltip -->
                <div class="relative mt-5 pt-2">
                    <!-- Axis Labels -->
                    <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[10px] font-semibold text-slate-400 pr-2">
                        <span>100</span>
                        <span>75</span>
                        <span>50</span>
                        <span>25</span>
                        <span>0</span>
                    </div>
                    <!-- Wave Lines Area -->
                    <div class="ml-7 relative h-44 sm:h-52">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                            <div class="border-b border-slate-100 w-full"></div>
                        </div>
                        <svg class="w-full h-full overflow-visible" preserveaspectratio="none" viewbox="0 0 500 200">
                            <defs>
                                <lineargradient id="areaGradientPrimary" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"></stop>
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"></stop>
                                </lineargradient>
                                <lineargradient id="waveStroke1" x1="0" x2="1" y1="0" y2="0">
                                    <stop offset="0%" stop-color="#38bdf8"></stop>
                                    <stop offset="50%" stop-color="#2563eb"></stop>
                                    <stop offset="100%" stop-color="#6366f1"></stop>
                                </lineargradient>
                            </defs>
                            <!-- Filled wave area -->
                            <path d="M 0 170 C 40 160, 70 140, 110 130 C 150 120, 180 80, 220 90 C 260 100, 280 40, 320 35 C 360 30, 400 90, 440 70 C 470 55, 490 50, 500 45 L 500 200 L 0 200 Z" fill="url(#areaGradientPrimary)"></path>
                            <!-- Wave Line 1 (Main Upper Line) -->
                            <path d="M 0 170 C 40 160, 70 140, 110 130 C 150 120, 180 80, 220 90 C 260 100, 280 40, 320 35 C 360 30, 400 90, 440 70 C 470 55, 490 50, 500 45" fill="none" stroke="url(#waveStroke1)" stroke-linecap="round" stroke-width="3.5"></path>
                            <!-- Wave Line 2 (Cyan Secondary Lower Line) -->
                            <path d="M 0 185 C 50 180, 90 165, 130 155 C 170 145, 200 130, 240 140 C 280 150, 310 115, 350 110 C 390 105, 420 145, 460 125 C 480 115, 495 105, 500 100" fill="none" stroke="#38bdf8" stroke-linecap="round" stroke-opacity="0.8" stroke-width="2.5"></path>
                            <!-- Tooltip Marker Dot on Peak (Position 320, 35) -->
                            <circle cx="320" cy="35" fill="#ffffff" r="5.5" stroke="#2563eb" stroke-width="3"></circle>
                        </svg>
                        <!-- Floating Tooltip Pill -->
                        <div class="absolute left-[60%] sm:left-[62%] top-1 -translate-x-1/2 -translate-y-full mb-1">
                            <div class="bg-gradient-to-r from-brand-600 to-indigo-600 text-white px-3 py-1.5 rounded-xl shadow-lg flex items-center gap-1.5 text-xs font-bold">
                                <span>Score: 94.8</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="w-2.5 h-2.5 bg-indigo-600 transform rotate-45 mx-auto -mt-1 rounded-xs"></div>
                        </div>
                    </div>
                </div>
                <!-- Quick Micro Stats Grid inside card -->
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                    <div class="bg-slate-50 p-2.5 rounded-xl">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Plafon Usulan</span>
                        <span class="text-sm font-extrabold text-slate-900">Rp 350 Juta</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">SLIK OJK</span>
                        <span class="text-sm font-extrabold text-emerald-600">Kol 1 (Lancar)</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Rasio DSCR</span>
                        <span class="text-sm font-extrabold text-brand-600">1.62x Sehat</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Status Laporan</span>
                        <span class="text-sm font-extrabold text-indigo-600">Ready PDF</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Micro Metrics Strip -->
        <div class="w-full max-w-4xl mt-12 pt-6 border-t border-slate-200/70 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
            <div>
                <div class="text-xs text-slate-400 font-semibold uppercase">Waktu Analisis</div>
                <div class="text-lg font-extrabold text-slate-900 mt-0.5">&lt; 10 Detik</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold uppercase">DSCR Otomatis</div>
                <div class="text-lg font-extrabold text-brand-600 mt-0.5">1.62x Sehat</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold uppercase">Rasio LTV Agunan</div>
                <div class="text-lg font-extrabold text-slate-900 mt-0.5">145% Tercover</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-semibold uppercase">Kepatuhan</div>
                <div class="text-lg font-extrabold text-emerald-600 mt-0.5">Standar OJK</div>
            </div>
        </div>
    </section>

    <!-- SECTION: BENTO GRID OF CORE FEATURES -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 py-24 relative" id="fitur">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider mb-4">
                <span>Fitur Unggulan</span>
            </div>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Solusi Lengkap Evaluasi 5C &amp; <br class="hidden sm:block"/>Otomasi Analisis Kredit Institusional
            </h2>
            <p class="text-slate-500 text-base sm:text-lg mt-4">
                Platform terpadu untuk percepatan proses kredit tanpa mengorbankan ketelitian manajemen risiko pembiayaan.
            </p>
        </div>
        <!-- Bento Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: SLIK OJK Intelligence -->
            <div class="bg-white/80 backdrop-blur border border-slate-200/90 rounded-3xl p-8 flex flex-col justify-between shadow-card hover:shadow-xl hover:border-brand-300 transition-all">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Modul 01</div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">Verifikasi SLIK OJK (iDeb)</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Pemeriksaan riwayat debitur otomatis, penguraian baki debet antar bank, serta deteksi keterlambatan Kol 1–5 secara akurat.
                    </p>
                </div>
                <!-- Clean Preview Mockup -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700">Status Kolektibilitas</span>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-bold">Kol 1 (Lancar)</span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs py-1 border-b border-slate-200">
                            <span class="text-slate-500">Bank Mandiri (KPR)</span>
                            <span class="font-semibold text-slate-800">Rp 120.000.000</span>
                        </div>
                        <div class="flex items-center justify-between text-xs py-1">
                            <span class="text-slate-500">BCA (Modal Kerja)</span>
                            <span class="font-semibold text-slate-800">Rp 75.000.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Financial Ratio Engine -->
            <div class="bg-white/80 backdrop-blur border border-slate-200/90 rounded-3xl p-8 flex flex-col justify-between shadow-card hover:shadow-xl hover:border-brand-300 transition-all">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Modul 02</div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">Kalkulator Rasio &amp; DSCR</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Kalkulasi instan kemampuan angsuran, debt service coverage ratio (DSCR), loan-to-value (LTV), dan simulasi bunga flat/efektif.
                    </p>
                </div>
                <!-- Clean Ratio Preview -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col gap-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-medium">DSCR Target Benchmark</span>
                        <span class="font-extrabold text-brand-600">1.62x (Aman &gt; 1.3x)</span>
                    </div>
                    <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                        <div class="bg-brand-500 h-full rounded-full w-[82%]"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1 text-center">
                        <div class="bg-white p-2 rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">LTV Ratio</span>
                            <span class="text-sm font-extrabold text-slate-800">145%</span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold">Net Cashflow</span>
                            <span class="text-sm font-extrabold text-emerald-600">Surplus</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: 5C Scoring Radar & Document Export -->
            <div class="bg-white/80 backdrop-blur border border-slate-200/90 rounded-3xl p-8 flex flex-col justify-between shadow-card hover:shadow-xl hover:border-brand-300 transition-all">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-brand-600 mb-2">Modul 03</div>
                    <h3 class="text-xl font-extrabold text-slate-900 mb-2">Dokumen Analisis Kredit 1-Click PDF</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Satu klik untuk mengonversi seluruh parameter analisis menjadi berkas memorandum evaluasi kredit resmi siap cetak dan tandatangan komite.
                    </p>
                </div>
                <!-- Document Badge Mockup -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">picture_as_pdf</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Laporan_Analisis_Kredit_2026.pdf</span>
                            <span class="text-[11px] text-slate-400">12 Halaman Standar Kepatuhan</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-brand-50 text-brand-600 text-xs font-bold">Siap Cetak</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: INTERACTIVE CALCULATOR -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 py-20 bg-slate-50/70 rounded-3xl border border-slate-200/80 my-10" id="simulasi">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
                <span class="material-symbols-outlined text-[16px] text-brand-600">tune</span>
                <span>Simulasi Interaktif</span>
            </div>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                Kalkulator Sensitivitas Plafon &amp; Kelayakan
            </h2>
            <p class="text-slate-500 text-base sm:text-lg mt-3">
                Geser nilai pengajuan untuk melihat simulasi rekomendasi komite kredit dan perkiraan angsuran secara real-time.
            </p>
        </div>
        <!-- Calculator Box -->
        <div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sm:p-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left Column: Inputs -->
                <div class="lg:col-span-6 space-y-6">
                    <!-- Plafon Range -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-bold text-slate-800" for="plafon-slider">Plafon Pinjaman</label>
                            <span class="text-lg font-black text-brand-600" id="plafon-display">Rp 250.000.000</span>
                        </div>
                        <input class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-brand-600" id="plafon-slider" max="1000000000" min="50000000" step="25000000" type="range" value="250000000"/>
                        <div class="flex justify-between text-[11px] text-slate-400 font-semibold mt-1">
                            <span>Rp 50 Jt</span>
                            <span>Rp 500 Jt</span>
                            <span>Rp 1 Miliar</span>
                        </div>
                    </div>
                    <!-- Tenor Selection Buttons -->
                    <div>
                        <label class="text-sm font-bold text-slate-800 block mb-2">Jangka Waktu (Tenor)</label>
                        <div class="grid grid-cols-4 gap-2">
                            <button class="tenor-btn py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all" data-tenor="12" type="button">12 Bln</button>
                            <button class="tenor-btn py-2.5 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-sm transition-all" data-tenor="24" type="button">24 Bln</button>
                            <button class="tenor-btn py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all" data-tenor="36" type="button">36 Bln</button>
                            <button class="tenor-btn py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all" data-tenor="48" type="button">48 Bln</button>
                        </div>
                    </div>
                    <!-- Nilai Agunan Range -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-bold text-slate-800" for="agunan-slider">Nilai Likuidasi Agunan</label>
                            <span class="text-lg font-black text-slate-900" id="agunan-display">Rp 375.000.000</span>
                        </div>
                        <input class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-brand-600" id="agunan-slider" max="1500000000" min="100000000" step="25000000" type="range" value="375000000"/>
                        <div class="flex justify-between text-[11px] text-slate-400 font-semibold mt-1">
                            <span>Rp 100 Jt</span>
                            <span>Rp 750 Jt</span>
                            <span>Rp 1.5 Miliar</span>
                        </div>
                    </div>
                </div>
                <!-- Right Column: Projection Card -->
                <div class="lg:col-span-6 bg-slate-50/90 rounded-2xl border border-slate-200/90 p-6 flex flex-col justify-between space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Proyeksi Real-time</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" id="sim-status-chip">● DISARANKAN APPROVAL</span>
                    </div>
                    <!-- Metric Highlights -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase block">Est. Angsuran / Bln</span>
                            <span class="text-lg font-black text-slate-900 mt-1 block" id="angsuran-display">Rp 16.666.667</span>
                            <span class="text-[11px] text-slate-500">Bunga 2.5% Flat/bln</span>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
                            <span class="text-xs font-semibold text-slate-400 uppercase block">Rasio Coverage Agunan</span>
                            <span class="text-lg font-black text-brand-600 mt-1 block" id="ltv-display">150.0%</span>
                            <span class="text-[11px] text-brand-600 font-semibold" id="ltv-subtext">Kategori Sangat Aman</span>
                        </div>
                    </div>
                    <!-- Summary text box -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200/80">
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-brand-600 text-[20px] shrink-0 mt-0.5">insights</span>
                            <p class="text-xs text-slate-600 leading-relaxed" id="sim-summary">
                                Kapasitas agunan memadai dengan coverage ratio LTV 150.0%. Struktur angsuran berada dalam ambang batas toleransi arus kas debitur. Disarankan untuk masuk ke tahapan komite kredit.
                            </p>
                        </div>
                    </div>
                    <!-- CTA link -->
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs text-slate-500">Ingin verifikasi SLIK OJK komprehensif?</span>
                        <a class="px-4 py-2 rounded-full bg-brand-600 text-white font-bold text-xs hover:bg-brand-700 transition-colors shadow-sm" href="<?= base_url('auth') ?>">
                            Buka Portal →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: 4-STEP PROCESS -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 py-24" id="alur-kerja">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider mb-4">
                <span>Alur Kerja Praktis</span>
            </div>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                4 Langkah Evaluasi Kredit Mandiri
            </h2>
            <p class="text-slate-500 text-base sm:text-lg mt-3">
                Proses otomatis tanpa kerumitan spreadsheet dari pengumpulan data hingga persetujuan final.
            </p>
        </div>
        <!-- Step Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Step 01 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-card hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-extrabold text-lg text-slate-900 mb-6">
                        01
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Input Berkas &amp; Agunan</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Unggah identitas calon debitur, legalitas usaha, dan bukti agunan secara digital tanpa tumpukan kertas.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-semibold text-brand-600">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Digital Ingestion</span>
                </div>
            </div>
            <!-- Step 02 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-card hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-extrabold text-lg text-slate-900 mb-6">
                        02
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Sinkronisasi SLIK OJK</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Mesin menarik data riwayat fasilitas aktif, tunggakan kredit, dan histori kolektibilitas secara langsung.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-semibold text-brand-600">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>iDeb Real-time Query</span>
                </div>
            </div>
            <!-- Step 03 -->
            <div class="bg-gradient-to-b from-brand-50/50 to-white border-2 border-brand-500/80 rounded-3xl p-6 shadow-card hover:shadow-blue-glow transition-all flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -top-6 -right-6 w-20 h-20 bg-brand-200/40 rounded-full blur-xl"></div>
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-extrabold text-lg shadow-md shadow-brand-500/30 mb-6">
                        03
                    </div>
                    <div class="text-[10px] font-extrabold text-brand-600 uppercase tracking-wider mb-1">AI 5C Core Engine</div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Inferensi &amp; Scoring 5C</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Model AI memvalidasi Character, Capacity, Capital, Collateral, &amp; Condition serta menghasilkan skor kelayakan.
                    </p>
                </div>
                <div class="pt-4 border-t border-brand-100 flex items-center gap-2 text-xs font-bold text-brand-700">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    <span>Kalkulasi Otomatis 5C</span>
                </div>
            </div>
            <!-- Step 04 -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-card hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-extrabold text-lg text-slate-900 mb-6">
                        04
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Penerbitan Laporan Kredit</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-6">
                        Komite kredit menerima lembar laporan evaluasi terstandarisasi siap tanda tangan dan persetujuan formal.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-semibold text-brand-600">
                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                    <span>Ekspor Laporan PDF</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: COMPARISON BENCHMARK -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 py-20" id="komparasi">
        <div class="bg-gradient-to-b from-slate-50 to-white border border-slate-200/90 rounded-3xl p-8 sm:p-12 shadow-card">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left Copy -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-100/70 text-brand-700 text-xs font-bold uppercase tracking-wider">
                        <span>Keunggulan Kompetitif</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Mengapa Lembaga Pembiayaan Beralih ke <br/><span class="text-brand-600"><?= esc($namaWeb) ?></span>
                    </h2>
                    <p class="text-slate-500 text-sm sm:text-base leading-relaxed">
                        Tinggalkan proses analisis manual berhari-hari yang rentan salah rumus. Dapatkan efisiensi 95% dengan audit trail kepatuhan lengkap.
                    </p>
                    <div class="pt-4">
                        <a class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-950 text-white font-bold text-sm hover:bg-brand-600 transition-colors shadow-sm" href="<?= base_url('auth') ?>">
                            <span>Daftar Akses Institusi</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
                <!-- Right Table Comparison -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
                    <div class="grid grid-cols-12 bg-slate-50/80 p-4 border-b border-slate-200 font-bold text-xs text-slate-600">
                        <div class="col-span-6 uppercase">Parameter Evaluasi</div>
                        <div class="col-span-3 text-center text-slate-400 uppercase">Manual</div>
                        <div class="col-span-3 text-center text-brand-600 uppercase font-extrabold">OTW Engine</div>
                    </div>
                    <div class="divide-y divide-slate-100 text-sm">
                        <!-- Row 1 -->
                        <div class="grid grid-cols-12 p-4 items-center">
                            <div class="col-span-6 font-semibold text-slate-800">Durasi Analisis Kredit</div>
                            <div class="col-span-3 text-center text-xs text-slate-400">3 - 5 Hari</div>
                            <div class="col-span-3 text-center font-extrabold text-brand-600 text-xs bg-brand-50/80 py-1 rounded-full">&lt; 10 Menit</div>
                        </div>
                        <!-- Row 2 -->
                        <div class="grid grid-cols-12 p-4 items-center">
                            <div class="col-span-6 font-semibold text-slate-800">Scoring 5C Otomatis</div>
                            <div class="col-span-3 flex justify-center text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </div>
                            <div class="col-span-3 flex justify-center text-brand-600">
                                <div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                </div>
                            </div>
                        </div>
                        <!-- Row 3 -->
                        <div class="grid grid-cols-12 p-4 items-center">
                            <div class="col-span-6 font-semibold text-slate-800">Integrasi SLIK OJK Real-time</div>
                            <div class="col-span-3 flex justify-center text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </div>
                            <div class="col-span-3 flex justify-center text-brand-600">
                                <div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                </div>
                            </div>
                        </div>
                        <!-- Row 4 -->
                        <div class="grid grid-cols-12 p-4 items-center">
                            <div class="col-span-6 font-semibold text-slate-800">Penerbitan Laporan PDF 1-Klik</div>
                            <div class="col-span-3 flex justify-center text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </div>
                            <div class="col-span-3 flex justify-center text-brand-600">
                                <div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                </div>
                            </div>
                        </div>
                        <!-- Row 5 -->
                        <div class="grid grid-cols-12 p-4 items-center">
                            <div class="col-span-6 font-semibold text-slate-800">Four-Eyes Approval Hierarchy</div>
                            <div class="col-span-3 flex justify-center text-slate-400 text-xs">Manual Email</div>
                            <div class="col-span-3 flex justify-center text-brand-600">
                                <div class="w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: CURATED QUESTIONS (FAQ) -->
    <section class="max-w-4xl mx-auto px-6 sm:px-8 py-20" id="faq">
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold uppercase tracking-wider mb-3">
                <span>FAQ</span>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
        </div>
        <div class="space-y-3">
            <details class="group bg-white rounded-2xl border border-slate-200/90 p-5 [&amp;_summary::-webkit-details-marker]:none">
                <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-800 text-base">
                    <span>Bagaimana integrasi SLIK OJK bekerja di <?= esc($namaWeb) ?>?</span>
                    <span class="material-symbols-outlined text-slate-400 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-sm text-slate-500 leading-relaxed mt-3 pt-3 border-t border-slate-100">
                    Engine membaca file iDeb SLIK OJK yang diunggah dan mengekstrak kolektibilitas fasilitas aktif, baki debet, serta histori tunggakan dalam hitungan detik tanpa input manual.
                </p>
            </details>
            <details class="group bg-white rounded-2xl border border-slate-200/90 p-5 [&amp;_summary::-webkit-details-marker]:none">
                <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-800 text-base">
                    <span>Apakah format dokumen laporan analisis kredit sesuai dengan standar audit regulator?</span>
                    <span class="material-symbols-outlined text-slate-400 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-sm text-slate-500 leading-relaxed mt-3 pt-3 border-t border-slate-100">
                    Ya, format laporan evaluasi dan memorandum kredit disusun sesuai standar kehati-hatian finansial, mencakup analisis 5C, mitigasi risiko, kalkulasi DSCR, LTV, dan kolom persetujuan komite berjenjang (four-eyes principle).
                </p>
            </details>
            <details class="group bg-white rounded-2xl border border-slate-200/90 p-5 [&amp;_summary::-webkit-details-marker]:none">
                <summary class="flex items-center justify-between cursor-pointer font-bold text-slate-800 text-base">
                    <span>Apakah data calon debitur dijamin kerahasiaannya?</span>
                    <span class="material-symbols-outlined text-slate-400 group-open:rotate-180 transition-transform">expand_more</span>
                </summary>
                <p class="text-sm text-slate-500 leading-relaxed mt-3 pt-3 border-t border-slate-100">
                    Seluruh data dienkripsi dengan standar perbankan AES-256 dan TLS 1.3. <?= esc($namaWeb) ?> beroperasi dengan kontrol kepatuhan standar ISO 27001 dan regulasi perlindungan data pribadi.
                </p>
            </details>
        </div>
    </section>

    <!-- SECTION: HIGH-CONVERSION CTA BANNER -->
    <section class="max-w-7xl mx-auto px-6 sm:px-8 py-16">
        <div class="relative bg-gradient-to-tr from-brand-700 via-brand-600 to-indigo-600 rounded-3xl p-10 sm:p-16 text-center text-white overflow-hidden shadow-float">
            <!-- Subtle Glow Layer -->
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-white/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-bold uppercase tracking-wider text-brand-100 border border-white/20">
                    <span class="material-symbols-outlined text-[16px]">bolt</span>
                    <span>Mulai Transformasi Kredit Hari Ini</span>
                </div>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                    Tingkatkan Kecepatan Underwriting Kredit Institusi Anda Sekarang
                </h2>
                <p class="text-brand-100 text-base sm:text-lg max-w-xl mx-auto leading-relaxed">
                    Bergabung bersama jaringan lembaga pembiayaan dan institusi finansial yang telah memodernisasi evaluasi 5C dan penerbitan laporan analisis mereka.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-slate-950 hover:bg-slate-100 font-extrabold text-base transition-all shadow-md hover:scale-105" href="<?= base_url('auth') ?>">
                        Buka Portal Sistem
                    </a>
                    <a class="w-full sm:w-auto px-7 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-base transition-colors border border-white/20" href="mailto:<?= esc($emailWeb) ?>">
                        Jadwalkan Demo Institusi
                    </a>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 pt-4 text-xs font-semibold text-brand-100/90">
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">verified_user</span> OJK Compliant Standard</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">lock</span> Bank-Grade AES-256</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">speed</span> 95% Waktu Lebih Hemat</span>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- APPTICS MINIMAL CLEAN FOOTER -->
<footer class="relative z-10 bg-slate-50 border-t border-slate-200/80 mt-12">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
            <!-- Brand Info -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoWeb)): ?>
                        <img src="<?= base_url($logoWeb) ?>" alt="Logo" class="w-9 h-9 object-contain rounded-xl">
                    <?php else: ?>
                        <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-[20px]">auto_awesome</span>
                        </div>
                    <?php endif; ?>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight"><?= esc($namaWeb) ?><span class="text-brand-600">.</span></span>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed max-w-sm">
                    Platform AI 5C Credit Engine &amp; Autonomous Underwriting untuk <?= esc($namaLembaga) ?> dan Lembaga Pembiayaan di seluruh Indonesia.
                </p>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 pt-2">
                    <span class="material-symbols-outlined text-[18px] text-brand-600">verified</span>
                    <span>Standar Kepatuhan OJK &amp; Bersertifikasi ISO 27001</span>
                </div>
            </div>
            <!-- Links Modul -->
            <div class="lg:col-span-2 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Modul Engine</h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a class="hover:text-brand-600 transition-colors" href="#fitur">SLIK OJK Intelligence</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#fitur">Scoring 5C Matrix</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#fitur">Kalkulator DSCR &amp; LTV</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#fitur">Ekspor Laporan PDF</a></li>
                </ul>
            </div>
            <!-- Links Navigasi -->
            <div class="lg:col-span-2 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Navigasi</h4>
                <ul class="space-y-2 text-sm text-slate-500">
                    <li><a class="hover:text-brand-600 transition-colors" href="#fitur">Fitur AI</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#alur-kerja">Alur Analisis</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#simulasi">Sandbox Kalkulasi</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#komparasi">Perbandingan</a></li>
                    <li><a class="hover:text-brand-600 transition-colors" href="#faq">FAQ</a></li>
                </ul>
            </div>
            <!-- Kantor Operasional -->
            <div class="lg:col-span-3 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Kantor Operasional</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    <?= esc($alamatWeb) ?>
                </p>
                <div class="text-xs text-slate-600 font-semibold pt-1">
                    <?= esc($emailWeb) ?>
                </div>
            </div>
        </div>
        <!-- Footer Bottom -->
        <div class="mt-12 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div>© <?= date('Y') ?> <?= esc($namaWeb) ?>. Hak Cipta Dilindungi.</div>
            <div class="flex items-center gap-6">
                <a class="hover:text-slate-700 transition-colors" href="#">Syarat &amp; Ketentuan</a>
                <a class="hover:text-slate-700 transition-colors" href="#">Kebijakan Privasi</a>
                <a class="hover:text-slate-700 transition-colors" href="#">Keamanan Sistem</a>
            </div>
        </div>
    </div>
</footer>

<!-- CLIENT-SIDE SIMULATOR SCRIPT -->
<script>
    (function initCreditSimulator() {
      const plafonSlider = document.getElementById('plafon-slider');
      const agunanSlider = document.getElementById('agunan-slider');
      const plafonDisplay = document.getElementById('plafon-display');
      const agunanDisplay = document.getElementById('agunan-display');
      const angsuranDisplay = document.getElementById('angsuran-display');
      const ltvDisplay = document.getElementById('ltv-display');
      const ltvSubtext = document.getElementById('ltv-subtext');
      const simStatusChip = document.getElementById('sim-status-chip');
      const simSummary = document.getElementById('sim-summary');
      const tenorButtons = document.querySelectorAll('.tenor-btn');

      let currentTenor = 24;

      function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
      }

      function calculateMetrics() {
        const plafon = parseFloat(plafonSlider.value);
        const agunan = parseFloat(agunanSlider.value);
        const tenor = currentTenor;
        const monthlyRate = 0.025; // 2.5% per month

        // Format Displays
        plafonDisplay.textContent = formatRupiah(plafon);
        agunanDisplay.textContent = formatRupiah(agunan);

        // Angsuran: Pokok bulanan + Bunga flat
        const pokokBulanan = plafon / tenor;
        const bungaBulanan = plafon * monthlyRate;
        const totalAngsuran = Math.round(pokokBulanan + bungaBulanan);
        angsuranDisplay.textContent = formatRupiah(totalAngsuran);

        // LTV: (Agunan / Plafon) * 100
        const ltvPercent = (agunan / plafon) * 100;
        ltvDisplay.textContent = ltvPercent.toFixed(1) + '%';

        let simulatedScore = 80;
        if (ltvPercent >= 150) simulatedScore += 10;
        else if (ltvPercent >= 120) simulatedScore += 5;
        else if (ltvPercent < 100) simulatedScore -= 15;

        if (tenor <= 24) simulatedScore += 3;
        else if (tenor > 36) simulatedScore -= 4;

        if (simulatedScore >= 85) {
          simStatusChip.textContent = '● DISARANKAN APPROVAL';
          simStatusChip.className = 'px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs';
          ltvSubtext.textContent = 'Kategori Sangat Aman';
          ltvSubtext.className = 'text-[11px] text-emerald-600 font-semibold';
          simSummary.textContent = 'Kapasitas agunan memadai dengan coverage ratio LTV ' + ltvPercent.toFixed(1) + '%. Struktur angsuran berada dalam ambang batas toleransi arus kas debitur. Disarankan untuk masuk ke tahapan komite kredit.';
        } else if (simulatedScore >= 70) {
          simStatusChip.textContent = '● BUTUH VERIFIKASI TAMBAHAN';
          simStatusChip.className = 'px-3 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-xs';
          ltvSubtext.textContent = 'Kategori Standar';
          ltvSubtext.className = 'text-[11px] text-amber-700 font-semibold';
          simSummary.textContent = 'Rasio pembiayaan memenuhi ambang batas dasar dengan LTV ' + ltvPercent.toFixed(1) + '%. Disarankan pengecekan mutasi rekening 6 bulan terakhir serta konfirmasi legalitas sertifikat agunan.';
        } else {
          simStatusChip.textContent = '● PERTIMBANGKAN ULANG';
          simStatusChip.className = 'px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold text-xs';
          ltvSubtext.textContent = 'Coverage Agunan Minim';
          ltvSubtext.className = 'text-[11px] text-red-600 font-semibold';
          simSummary.textContent = 'Plafon pinjaman melebihi ambang batas toleransi nilai likuidasi agunan (LTV ' + ltvPercent.toFixed(1) + '%). Diperlukan agunan tambahan atau pengurangan plafon pengajuan.';
        }
      }

      if (plafonSlider && agunanSlider) {
        plafonSlider.addEventListener('input', calculateMetrics);
        agunanSlider.addEventListener('input', calculateMetrics);
      }

      tenorButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          tenorButtons.forEach(b => {
            b.className = 'tenor-btn py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 transition-all';
          });
          this.className = 'tenor-btn py-2.5 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-sm transition-all';
          currentTenor = parseInt(this.getAttribute('data-tenor'), 10);
          calculateMetrics();
        });
      });

      calculateMetrics();
    })();
</script>
</body>
</html>
