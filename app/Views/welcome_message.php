<?php
    $rawNamaWeb  = !empty($pengaturan_sistem['nama_aplikasi']) ? trim($pengaturan_sistem['nama_aplikasi']) : '';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? trim($pengaturan_sistem['nama_lembaga']) : 'Lembaga Pembiayaan';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : '';
    $emailWeb    = !empty($pengaturan_sistem['email_lembaga']) ? trim($pengaturan_sistem['email_lembaga']) : 'info@otwapproved.id';
    $teleponWeb  = !empty($pengaturan_sistem['telepon_lembaga']) ? trim($pengaturan_sistem['telepon_lembaga']) : '(021) 5088-2900';
    $alamatWeb   = !empty($pengaturan_sistem['alamat_lembaga']) ? trim($pengaturan_sistem['alamat_lembaga']) : 'Jakarta Selatan, Indonesia';

    // Gunakan branding resmi OTW Approved jika pengaturan masih berisi nama lama (Credit Engine / CAM)
    $namaWebBersih = $rawNamaWeb;
    if (empty($namaWebBersih) || preg_match('/(credit\s*engine|cam|bpr)/i', $namaWebBersih)) {
        $namaWebBersih = 'OTW Approved';
    }

    $namaLembagaBersih = trim(preg_replace('/\b(BPR|CAM)\b/i', '', $namaLembaga));
    $namaLembagaBersih = trim(preg_replace('/\bPT\.?\s*\/\s*/i', '', $namaLembagaBersih));
    $namaLembagaBersih = trim(preg_replace('/\s+/', ' ', $namaLembagaBersih), " \t\n\r\0\x0B/");
    if (empty($namaLembagaBersih)) {
        $namaLembagaBersih = 'Lembaga Pembiayaan';
    }

    $alamatBersih = trim(preg_replace('/\b(BPR|CAM)\b/i', '', $alamatWeb));
    $alamatBersih = trim(preg_replace('/\s+/', ' ', $alamatBersih));
    if (empty($alamatBersih)) {
        $alamatBersih = 'Jakarta Selatan, Indonesia';
    }

    $isLoggedIn = (bool) session()->get('logged_in');
    $portalUrl  = $isLoggedIn ? base_url('dashboard') : base_url('auth');
    $portalCta  = $isLoggedIn ? 'Buka Dashboard' : 'Masuk Portal';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($namaWebBersih) ?> — Platform Analisa &amp; Persetujuan Pembiayaan</title>
    <meta name="description" content="<?= esc($namaWebBersih) ?> adalah platform analisa kelayakan pembiayaan, verifikasi SLIK, pembacaan mutasi rekening koran, dan persetujuan komite secara terpadu.">
    <link rel="shortcut icon" type="image/png" href="<?= !empty($logoWeb) ? base_url($logoWeb) : base_url('favicon.ico') ?>">

    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .tabular-nums {
            font-variant-numeric: tabular-nums;
        }
        .bg-grid-subtle {
            background-image:
                linear-gradient(to right, rgba(226, 232, 240, 0.55) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(226, 232, 240, 0.55) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: #e2e8f0;
            height: 6px;
            border-radius: 999px;
            outline: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #1d4ed8;
            border: 3px solid #ffffff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.25);
            cursor: pointer;
        }
        input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #1d4ed8;
            border: 3px solid #ffffff;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.25);
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased selection:bg-blue-100 selection:text-blue-900 overflow-x-hidden">

    <!-- =======================================================
         NAVIGATION BAR (Clean Corporate Fintech)
    ======================================================== -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <!-- Brand -->
            <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 shrink-0 group">
                <?php if (!empty($logoWeb)): ?>
                    <img src="<?= base_url($logoWeb) ?>" alt="<?= esc($namaWebBersih) ?>" class="h-8 w-auto object-contain">
                <?php else: ?>
                    <div class="w-8 h-8 rounded-lg bg-slate-950 text-white flex items-center justify-center font-extrabold text-xs tracking-tight">
                        OA
                    </div>
                <?php endif; ?>
                <div class="leading-tight">
                    <span class="text-sm sm:text-base font-extrabold text-slate-950 tracking-tight block"><?= esc($namaWebBersih) ?></span>
                    <span class="text-[10px] font-medium text-slate-500 hidden sm:block">Platform Analisa Pembiayaan</span>
                </div>
            </a>

            <!-- Center Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-600">
                <a href="#alur-kerja" class="hover:text-slate-950 transition">Alur Kerja</a>
                <a href="#kapabilitas" class="hover:text-slate-950 transition">Kapabilitas</a>
                <a href="#simulasi" class="hover:text-slate-950 transition">Simulasi Kredit</a>
            </nav>

            <!-- Right CTA & Mobile Menu Toggle -->
            <div class="flex items-center gap-2.5">
                <a href="<?= $portalUrl ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold transition shadow-2xs">
                    <span><?= $portalCta ?></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <button type="button" onclick="toggleMobileNav()" class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 transition cursor-pointer" aria-label="Buka Menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Nav -->
        <div id="mobileNavMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
            <a href="#alur-kerja" onclick="toggleMobileNav(false)" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">Alur Kerja</a>
            <a href="#kapabilitas" onclick="toggleMobileNav(false)" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">Kapabilitas</a>
            <a href="#simulasi" onclick="toggleMobileNav(false)" class="block px-3 py-2 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50">Simulasi Kredit</a>
        </div>
    </header>

    <!-- =======================================================
         SECTION 1: EDITORIAL ASYMMETRIC HERO
    ======================================================== -->
    <section class="relative border-b border-slate-200/80 bg-white overflow-hidden">
        <div class="absolute inset-0 bg-grid-subtle opacity-45 pointer-events-none"></div>
        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 sm:pt-16 sm:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Left Copy (7 cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.14em] text-blue-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>
                        <span>Sistem Analisa &amp; Persetujuan Kredit</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-950 tracking-[-0.03em] leading-[1.08]">
                        Keputusan kredit lebih cepat, terukur, dan berbasis data.
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                        <?= esc($namaWebBersih) ?> menyatukan pengajuan debitur, verifikasi SLIK, pembacaan mutasi rekening koran otomatis, hingga persetujuan komite dalam satu alur kerja yang transparan bagi <?= esc($namaLembagaBersih) ?>.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="#simulasi" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition shadow-xs">
                            <span>Coba Simulasi Kredit</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        </a>
                        <a href="#alur-kerja" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs sm:text-sm font-bold transition">
                            <span>Pelajari Alur Kerja</span>
                        </a>
                    </div>

                    <!-- Trust Strip -->
                    <div class="pt-6 border-t border-slate-200/90 grid grid-cols-3 gap-4 max-w-lg">
                        <div>
                            <div class="text-lg sm:text-xl font-extrabold text-slate-950 tabular-nums">4 Peran</div>
                            <div class="text-[11px] text-slate-500 font-medium mt-0.5">Pengusul hingga Komite</div>
                        </div>
                        <div class="border-l border-slate-200 pl-4">
                            <div class="text-lg sm:text-xl font-extrabold text-slate-950 tabular-nums">5C &amp; DSCR</div>
                            <div class="text-[11px] text-slate-500 font-medium mt-0.5">Skoring &amp; rasio otomatis</div>
                        </div>
                        <div class="border-l border-slate-200 pl-4">
                            <div class="text-lg sm:text-xl font-extrabold text-slate-950 tabular-nums">A4 PDF</div>
                            <div class="text-[11px] text-slate-500 font-medium mt-0.5">Laporan siap komite</div>
                        </div>
                    </div>
                </div>

                <!-- Right Authentic Product Preview Card (5 cols) -->
                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-2 sm:p-3 shadow-sm">
                        <div class="rounded-xl bg-white border border-slate-200/90 p-5 sm:p-6 space-y-5">
                            <!-- Card Top -->
                            <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100">
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Evaluasi Kredit</div>
                                    <div class="text-sm font-extrabold text-slate-950 mt-0.5 truncate">UD Sumber Rejeki</div>
                                    <div class="text-[11px] text-slate-500">Modal Kerja &bull; 24 Bulan (Anuitas)</div>
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Layak
                                </span>
                            </div>

                            <!-- Key Financial Figures -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Plafon Diajukan</div>
                                    <div class="text-sm font-extrabold text-slate-950 tabular-nums mt-1">Rp 250.000.000</div>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Angsuran / Bulan</div>
                                    <div class="text-sm font-extrabold text-blue-700 tabular-nums mt-1">Rp 11.768.368</div>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Rasio DSCR</div>
                                    <div class="text-sm font-extrabold text-emerald-700 tabular-nums mt-1">2,97x <span class="text-[10px] font-semibold text-slate-400">(Aman)</span></div>
                                </div>
                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Coverage Agunan</div>
                                    <div class="text-sm font-extrabold text-slate-950 tabular-nums mt-1">182,0% <span class="text-[10px] font-semibold text-slate-400">LV</span></div>
                                </div>
                            </div>

                            <!-- 5C Compact Score Breakdown -->
                            <div class="space-y-2.5 pt-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-700 uppercase tracking-wider">Skor Kelayakan 5C</span>
                                    <span class="font-extrabold text-slate-950 tabular-nums">85,6 / 100</span>
                                </div>
                                <div class="space-y-2 text-[11px]">
                                    <div class="flex items-center gap-3">
                                        <span class="w-20 text-slate-500 font-medium">Character</span>
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-slate-900 rounded-full" style="width: 86%"></div></div>
                                        <span class="w-7 text-right font-bold text-slate-700 tabular-nums">86</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-20 text-slate-500 font-medium">Capacity</span>
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-blue-700 rounded-full" style="width: 83%"></div></div>
                                        <span class="w-7 text-right font-bold text-slate-700 tabular-nums">83</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="w-20 text-slate-500 font-medium">Collateral</span>
                                        <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-600 rounded-full" style="width: 95%"></div></div>
                                        <span class="w-7 text-right font-bold text-slate-700 tabular-nums">95</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Footer -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                                <span>SLIK Kol 1 (Lancar)</span>
                                <span class="font-semibold text-slate-700">Mutasi Bank Terverifikasi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =======================================================
         SECTION 2: ALUR KERJA 4 PERAN
    ======================================================== -->
    <section id="alur-kerja" class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-700 mb-2">Alur Kerja Terstruktur</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-[-0.025em]">
                    Dari pengajuan awal hingga keputusan komite dalam satu alur.
                </h2>
                <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                    Setiap peran memiliki ruang kerja dan tanggung jawab yang jelas sehingga proses pemeriksaan tidak tumpang tindih.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 border border-slate-200 rounded-2xl bg-white divide-y sm:divide-y-0 sm:divide-x divide-slate-200 overflow-hidden">
                <div class="p-6 space-y-3">
                    <span class="text-xs font-extrabold text-blue-700 tabular-nums">01</span>
                    <h3 class="text-sm font-extrabold text-slate-950">Pengusul (AO)</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Input profil debitur perorangan maupun badan usaha, unggah dokumen agunan, dan impor mutasi rekening koran otomatis.
                    </p>
                </div>
                <div class="p-6 space-y-3">
                    <span class="text-xs font-extrabold text-blue-700 tabular-nums">02</span>
                    <h3 class="text-sm font-extrabold text-slate-950">Verifikator SLIK</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pencocokan KTP &amp; NIK debitur, ekspor format permohonan, unggah hasil iDeb, serta validasi baki debet berjalan.
                    </p>
                </div>
                <div class="p-6 space-y-3">
                    <span class="text-xs font-extrabold text-blue-700 tabular-nums">03</span>
                    <h3 class="text-sm font-extrabold text-slate-950">Reviewer Kredit</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tinjauan menyeluruh atas laporan keuangan, rasio DSCR/DSR, coverage agunan, web check, dan rekomendasi skor 5C.
                    </p>
                </div>
                <div class="p-6 space-y-3">
                    <span class="text-xs font-extrabold text-blue-700 tabular-nums">04</span>
                    <h3 class="text-sm font-extrabold text-slate-950">Komite &amp; CEO</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Penetapan keputusan resmi (Setuju, Bersyarat, Revisi, atau Tolak) lengkap dengan catatan risalah dan jejak audit.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- =======================================================
         SECTION 3: KAPABILITAS UTAMA (Editorial Bento Grid)
    ======================================================== -->
    <section id="kapabilitas" class="py-16 sm:py-24 bg-white border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-700 mb-2">Kapabilitas Inti</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-[-0.025em]">
                    Dirancang khusus untuk ketelitian analisa pembiayaan.
                </h2>
            </div>

            <!-- Row 1: Asymmetric 7 / 5 Bento -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-5">
                <div class="lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 flex flex-col justify-between gap-6">
                    <div class="space-y-2">
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">Pembacaan Mutasi Rekening Koran Otomatis</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-xl">
                            Unggah e-Statement PDF atau foto mutasi bank. Sistem mengekstrak nama bank, periode, saldo awal, total mutasi kredit dan debit, lalu menyajikannya dalam tabel siap verifikasi beserta pratinjau dokumen asli.
                        </p>
                    </div>
                    <!-- Mini Clean Table Preview -->
                    <div class="rounded-xl border border-slate-200 overflow-x-auto bg-slate-50/50">
                        <table class="w-full text-left text-[11px] border-collapse min-w-[440px]">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider bg-slate-50">
                                    <th class="py-2.5 px-3">Bank</th>
                                    <th class="py-2.5 px-3">Periode</th>
                                    <th class="py-2.5 px-3 text-right">Mutasi Kredit</th>
                                    <th class="py-2.5 px-3 text-right">Rata² / Bln</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/70 bg-white tabular-nums">
                                <tr>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">BCA</td>
                                    <td class="py-2.5 px-3 text-slate-600">Mei – Okt 2026</td>
                                    <td class="py-2.5 px-3 text-right font-semibold text-slate-800">Rp 720.000.000</td>
                                    <td class="py-2.5 px-3 text-right font-extrabold text-blue-700">Rp 120.000.000</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">Mandiri</td>
                                    <td class="py-2.5 px-3 text-slate-600">Jan 2026</td>
                                    <td class="py-2.5 px-3 text-right font-semibold text-slate-800">Rp 62.000.000</td>
                                    <td class="py-2.5 px-3 text-right font-extrabold text-blue-700">Rp 62.000.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="lg:col-span-5 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8 flex flex-col justify-between gap-6">
                    <div class="space-y-2">
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">Laporan Keuangan &amp; Cetak A4</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Perhitungan Laba Rugi, HPP, arus kas bersih, DSCR, DSR, hingga jadwal disbursement tersinkronisasi otomatis dan siap diunduh dalam format PDF A4 untuk rapat komite.
                        </p>
                    </div>
                    <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-2.5 text-xs tabular-nums">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Pendapatan Bulanan</span>
                            <span class="font-bold text-slate-900">Rp 120.000.000</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">HPP &amp; Beban Operasional</span>
                            <span class="font-semibold text-rose-600">- Rp 85.000.000</span>
                        </div>
                        <div class="pt-2.5 border-t border-slate-100 flex justify-between items-center">
                            <span class="font-bold text-slate-900">Surplus / Net Cashflow</span>
                            <span class="font-extrabold text-emerald-700">Rp 35.000.000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: 3 Clean Hairline Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-2.5">
                    <div class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-950">Verifikasi SLIK &amp; Web Check</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pencatatan fasilitas pinjaman berjalan, pengecekan kolektibilitas, serta penelusuran rekam jejak debitur secara terpusat.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-2.5">
                    <div class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-950">Evaluasi Agunan &amp; LTV</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Perhitungan nilai pasar (MV), nilai likuidasi (LV), serta rasio coverage agunan untuk memastikan mitigasi risiko yang sehat.
                    </p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 space-y-2.5">
                    <div class="w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 text-slate-800 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-950">Audit Trail &amp; Kontrol Akses</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Setiap perubahan data, pengajuan, dan keputusan komite tercatat otomatis dalam log aktivitas sistem yang mudah diaudit.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- =======================================================
         SECTION 4: SIMULASI KREDIT & RASIO LTV INTERAKTIF
    ======================================================== -->
    <section id="simulasi" class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-10">
                <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-700 mb-2">Simulasi Interaktif</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-[-0.025em]">
                    Hitung estimasi angsuran &amp; rasio LTV secara instan.
                </h2>
                <p class="text-sm text-slate-600 mt-2">
                    Sesuaikan plafon, nilai agunan, tenor, dan metode bunga untuk melihat gambaran pembiayaan sebelum masuk ke portal.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <!-- Controls Panel (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-6">
                    <!-- Plafon -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <label for="simPlafon" class="font-bold text-slate-700">Plafon Pinjaman</label>
                            <span id="lblSimPlafon" class="font-extrabold text-slate-950 tabular-nums">Rp 250.000.000</span>
                        </div>
                        <input type="range" id="simPlafon" min="10000000" max="2000000000" step="10000000" value="250000000" class="w-full" oninput="hitungSimulasiLanding()">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium tabular-nums">
                            <span>Rp 10 Juta</span>
                            <span>Rp 2 Miliar</span>
                        </div>
                    </div>

                    <!-- Nilai Agunan -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <label for="simAgunan" class="font-bold text-slate-700">Nilai Pasar Agunan (MV)</label>
                            <span id="lblSimAgunan" class="font-extrabold text-slate-950 tabular-nums">Rp 500.000.000</span>
                        </div>
                        <input type="range" id="simAgunan" min="20000000" max="4000000000" step="10000000" value="500000000" class="w-full" oninput="hitungSimulasiLanding()">
                        <div class="flex justify-between text-[10px] text-slate-400 font-medium tabular-nums">
                            <span>Rp 20 Juta</span>
                            <span>Rp 4 Miliar</span>
                        </div>
                    </div>

                    <!-- Grid: Tenor, Bunga, Metode -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label for="simTenor" class="block text-xs font-bold text-slate-700 mb-1.5">Tenor (Bulan)</label>
                            <select id="simTenor" onchange="hitungSimulasiLanding()" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-800 focus:border-blue-600 focus:outline-none cursor-pointer">
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan</option>
                                <option value="18">18 Bulan</option>
                                <option value="24" selected>24 Bulan</option>
                                <option value="36">36 Bulan</option>
                                <option value="48">48 Bulan</option>
                                <option value="60">60 Bulan</option>
                            </select>
                        </div>
                        <div>
                            <label for="simBunga" class="block text-xs font-bold text-slate-700 mb-1.5">Bunga / Tahun (%)</label>
                            <input type="number" id="simBunga" value="12" min="1" max="36" step="0.5" oninput="hitungSimulasiLanding()" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-800 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label for="simMetode" class="block text-xs font-bold text-slate-700 mb-1.5">Metode Bunga</label>
                            <select id="simMetode" onchange="hitungSimulasiLanding()" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs font-bold text-slate-800 focus:border-blue-600 focus:outline-none cursor-pointer">
                                <option value="ANUITAS" selected>Anuitas</option>
                                <option value="FLAT">Flat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Output Executive Card (5 cols) -->
                <div class="lg:col-span-5 bg-slate-950 text-white rounded-2xl p-6 sm:p-8 flex flex-col justify-between gap-6">
                    <div class="space-y-6">
                        <div>
                            <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Estimasi Angsuran per Bulan</div>
                            <div id="outSimAngsuran" class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight tabular-nums mt-1.5">
                                Rp 11.768.368
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-slate-800 text-xs tabular-nums">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Rasio LTV (Plafon / Agunan)</span>
                                <span class="flex items-center gap-2">
                                    <strong id="outSimLtv" class="text-white font-bold">50,0%</strong>
                                    <span id="outSimLtvBadge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aman</span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Coverage Agunan (MV)</span>
                                <strong id="outSimCov" class="text-white font-bold">200,0%</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Estimasi Total Pengembalian</span>
                                <strong id="outSimTotal" class="text-slate-200 font-semibold">Rp 282.440.832</strong>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 space-y-3">
                        <a href="<?= $portalUrl ?>" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs sm:text-sm font-bold transition">
                            <span>Ajukan &amp; Analisa di Portal</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <p class="text-[10px] text-slate-500 text-center">
                            Hasil simulasi bersifat estimasi indikatif dan dapat disesuaikan pada tahap analisa komite.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =======================================================
         FOOTER (Minimalist Corporate)
    ======================================================== -->
    <footer class="bg-white py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-slate-200/80">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2.5">
                        <?php if (!empty($logoWeb)): ?>
                            <img src="<?= base_url($logoWeb) ?>" alt="<?= esc($namaWebBersih) ?>" class="h-7 w-auto object-contain">
                        <?php else: ?>
                            <div class="w-7 h-7 rounded-md bg-slate-950 text-white flex items-center justify-center font-extrabold text-[11px]">OA</div>
                        <?php endif; ?>
                        <span class="text-sm font-extrabold text-slate-950 tracking-tight"><?= esc($namaWebBersih) ?></span>
                    </div>
                    <p class="text-xs text-slate-500 max-w-md">
                        <?= esc($namaLembagaBersih) ?> &bull; <?= esc($alamatBersih) ?>
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-600 font-medium">
                    <span><?= esc($emailWeb) ?></span>
                    <span class="text-slate-300 hidden sm:inline">&bull;</span>
                    <span><?= esc($teleponWeb) ?></span>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                <span>&copy; <?= date('Y') ?> <?= esc($namaWebBersih) ?>. Seluruh hak cipta dilindungi.</span>
                <a href="<?= $portalUrl ?>" class="font-semibold text-slate-600 hover:text-blue-700 transition">Portal Internal &rarr;</a>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileNav(forceState) {
            const menu = document.getElementById('mobileNavMenu');
            if (!menu) return;
            if (typeof forceState === 'boolean') {
                menu.classList.toggle('hidden', !forceState);
            } else {
                menu.classList.toggle('hidden');
            }
        }

        function formatIdr(num) {
            return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
        }

        function hitungSimulasiLanding() {
            const plafon = parseFloat(document.getElementById('simPlafon')?.value || 250000000);
            const agunan = parseFloat(document.getElementById('simAgunan')?.value || 500000000);
            const tenor  = Math.max(1, parseInt(document.getElementById('simTenor')?.value || 24, 10));
            const bungaThn = Math.max(0, parseFloat(document.getElementById('simBunga')?.value || 12));
            const metode = (document.getElementById('simMetode')?.value || 'ANUITAS').toUpperCase();

            document.getElementById('lblSimPlafon').innerText = formatIdr(plafon);
            document.getElementById('lblSimAgunan').innerText = formatIdr(agunan);

            const r = (bungaThn / 100) / 12;
            let angsuran = 0;
            if (metode === 'ANUITAS' && r > 0) {
                angsuran = Math.round(plafon * (r / (1 - Math.pow(1 + r, -tenor))));
            } else {
                angsuran = Math.round((plafon / tenor) + (plafon * r));
            }
            const totalBayar = angsuran * tenor;
            const ltv = agunan > 0 ? (plafon / agunan) * 100 : 0;
            const cov = plafon > 0 ? (agunan / plafon) * 100 : 0;

            document.getElementById('outSimAngsuran').innerText = formatIdr(angsuran);
            document.getElementById('outSimTotal').innerText = formatIdr(totalBayar);
            document.getElementById('outSimLtv').innerText = ltv.toFixed(1).replace('.', ',') + '%';
            document.getElementById('outSimCov').innerText = cov.toFixed(1).replace('.', ',') + '%';

            const badge = document.getElementById('outSimLtvBadge');
            if (badge) {
                if (ltv <= 70) {
                    badge.innerText = 'Aman';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                } else if (ltv <= 85) {
                    badge.innerText = 'Moderat';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30';
                } else {
                    badge.innerText = 'Tinggi';
                    badge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', hitungSimulasiLanding);
    </script>
</body>
</html>
