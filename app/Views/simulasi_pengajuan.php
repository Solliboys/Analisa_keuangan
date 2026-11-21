<?php
    $logoWeb    = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : 'img/logo-oap.png?v=2';
    $namaWeb    = 'OTW Approved';
    $isLoggedIn = (bool) session()->get('logged_in');
    $portalUrl  = $isLoggedIn ? base_url('dashboard') : base_url('auth');

    $simSteps = [
        1 => ['num' => '01', 'title' => 'Aplikasi Kredit',   'sub' => 'Plafon & tenor',     'icon' => 'payments'],
        2 => ['num' => '02', 'title' => 'Profil Usaha',      'sub' => 'Debitur & SLIK',     'icon' => 'storefront'],
        3 => ['num' => '03', 'title' => 'Agunan & LTV',      'sub' => 'Jaminan & likuidasi','icon' => 'home_work'],
        4 => ['num' => '04', 'title' => 'Arus Kas',          'sub' => 'Omzet & beban',      'icon' => 'account_balance_wallet'],
        5 => ['num' => '05', 'title' => 'Memo Analisa',      'sub' => 'Skor 5C & hasil',    'icon' => 'verified'],
    ];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Pengajuan Kredit — <?= esc($namaWeb) ?></title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url($logoWeb) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        .sim-tab-panel { display: none; }
        .sim-tab-panel.active { display: block; }
        
        /* Custom Modern Inputs */
        .fin-input {
            width: 100%;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            transition: all 150ms ease;
        }
        .fin-input:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }
        .fin-input:focus {
            outline: none;
            background-color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }
        .fin-input-prefix { padding-left: 42px; }
        .fin-input-suffix { padding-right: 48px; }

        /* Hide scrollbars for smooth pill scrolling */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
            main { max-width: 100% !important; padding: 0 !important; }
            .sim-tab-panel { display: none !important; }
            #sim-tab-5 { display: block !important; border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col overflow-x-hidden selection:bg-blue-500/10 selection:text-blue-600">

    <!-- Ambient Top Glow -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[1100px] h-[450px] bg-gradient-to-b from-blue-100/60 via-sky-50/30 to-transparent rounded-[100%] blur-3xl opacity-70"></div>
        <div class="absolute top-96 -left-32 w-80 h-80 bg-blue-100/30 rounded-full blur-[100px]"></div>
        <div class="absolute top-[600px] -right-32 w-80 h-80 bg-indigo-100/30 rounded-full blur-[100px]"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            
            <!-- Left Branding & Breadcrumb -->
            <div class="flex items-center gap-3 min-w-0">
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 group shrink-0">
                    <img src="<?= base_url($logoWeb) ?>" alt="<?= esc($namaWeb) ?>" class="h-8 w-8 object-contain transition-transform group-hover:scale-105">
                    <div class="flex flex-col">
                        <span class="text-sm font-extrabold text-slate-900 tracking-tight leading-tight"><?= esc($namaWeb) ?></span>
                        <span class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider hidden sm:inline">Simulasi Pengajuan</span>
                    </div>
                </a>
                <span class="text-slate-300 hidden sm:inline">/</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200/70 text-[11px] font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Sandbox Interaktif</span>
                </span>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden sm:inline">Beranda</span>
                </a>
                <button type="button" onclick="isiContohSimulasi()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-blue-200 bg-white hover:bg-blue-50 text-slate-700 hover:text-blue-700 text-xs font-semibold transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">magic_button</span>
                    <span class="hidden sm:inline">Isi Contoh</span>
                </button>
                <button type="button" onclick="resetSimulasi()" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-600 text-xs font-semibold transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Reset</span>
                </button>
                <a href="<?= $portalUrl ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm shadow-blue-500/25 hover:shadow-blue-500/40 transition">
                    <span>Masuk Portal</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Live Strip (Sticky Under Header) -->
    <div class="lg:hidden bg-white/95 backdrop-blur border-b border-slate-200/80 px-4 py-2.5 no-print sticky top-16 z-30 shadow-xs">
        <div class="flex items-center justify-between text-[11px] font-medium text-slate-500 mb-2">
            <span class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-blue-600">tune</span>
                <span>Ringkasan Langsung</span>
            </span>
            <span class="font-bold text-slate-900 tabular-nums">Langkah <span id="simStepNumMobile">1</span> dari 5</span>
        </div>
        <div class="grid grid-cols-3 divide-x divide-slate-200/80 border border-slate-200/80 rounded-xl bg-slate-50/80 text-center tabular-nums">
            <div class="py-2 px-1">
                <div class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Angsuran / bln</div>
                <div id="mob_angs" class="text-xs font-extrabold text-slate-950 truncate mt-0.5">Rp 0</div>
            </div>
            <div class="py-2 px-1">
                <div class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">Skor 5C</div>
                <div id="mob_skor" class="text-xs font-extrabold text-blue-700 mt-0.5">85,6</div>
            </div>
            <div class="py-2 px-1">
                <div class="text-[9px] font-semibold text-slate-500 uppercase tracking-wider">DSCR</div>
                <div id="mob_dscr" class="text-xs font-extrabold text-emerald-700 mt-0.5">2,97x</div>
            </div>
        </div>
    </div>

    <!-- MAIN WORKBENCH -->
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-1 min-w-0 relative z-10">
        
        <!-- TOP HORIZONTAL STEPPER -->
        <div class="mb-6 no-print">
            <!-- Progress Sub-bar -->
            <div class="flex items-center justify-between text-xs text-slate-500 mb-2.5 px-1">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900">Alur Simulasi Kredit</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-600">Langkah <span id="simStepNum" class="font-bold text-blue-600">1</span> dari 5</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="isiContohSimulasi()" class="sm:hidden text-xs font-semibold text-blue-600 hover:underline">Isi Contoh</button>
                    <button type="button" onclick="resetSimulasi()" class="sm:hidden text-xs font-semibold text-slate-500 hover:underline">Reset</button>
                </div>
            </div>

            <!-- Horizontal Progress Track -->
            <div class="h-1.5 w-full bg-slate-200/80 rounded-full overflow-hidden mb-3">
                <div id="simProgressBar" class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 transition-all duration-300 rounded-full" style="width: 20%;"></div>
            </div>

            <!-- Stepper Navigation Pills -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <?php foreach ($simSteps as $n => $st): ?>
                    <button type="button" onclick="goSimTab(<?= $n ?>)" id="sim-step-btn-<?= $n ?>"
                            class="group shrink-0 inline-flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer <?= $n === 1 ? 'bg-blue-600 border-blue-600 text-white shadow-sm shadow-blue-500/25' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50' ?>">
                        <span id="sim-step-num-<?= $n ?>" class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold tabular-nums <?= $n === 1 ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200' ?>">
                            <?= $st['num'] ?>
                        </span>
                        <div class="text-left">
                            <div class="text-xs font-bold leading-tight"><?= $st['title'] ?></div>
                            <div class="text-[10px] opacity-80 leading-tight hidden sm:block"><?= $st['sub'] ?></div>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 2-COLUMN WORKBENCH: FORM ON LEFT + LIVE SUMMARY ON RIGHT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: FORM WORKSPACE (Cols 1-8) -->
            <div class="lg:col-span-8 space-y-5">

                <!-- STEP 1: APLIKASI KREDIT -->
                <div id="sim-tab-1" class="sim-tab-panel active bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    <!-- Card Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-white to-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">payments</span>
                            </div>
                            <div>
                                <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Parameter Aplikasi Kredit</h1>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan nilai plafon pinjaman, jangka waktu (tenor), suku bunga, dan metode angsuran.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 tabular-nums">01 / 05</span>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Tipe Pemohon -->
                            <div>
                                <label for="s_tipe" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Pemohon</label>
                                <select id="s_tipe" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Perorangan" selected>Perorangan / Wiraswasta</option>
                                    <option value="Badan Usaha">Badan Usaha (PT / CV / UD)</option>
                                </select>
                            </div>

                            <!-- Nama Debitur -->
                            <div>
                                <label for="s_nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Debitur / Pemohon</label>
                                <input type="text" id="s_nama" value="Budi Santoso" oninput="hitungSemuaSimulasi()" placeholder="Nama lengkap debitur" class="fin-input">
                            </div>

                            <!-- Plafon Pengajuan -->
                            <div class="sm:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <label for="s_plafon" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Plafon Pengajuan</label>
                                    <span class="text-[11px] font-semibold text-slate-500">Pilihan cepat:</span>
                                </div>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_plafon" value="250.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix text-base font-extrabold text-blue-600 tabular-nums">
                                </div>
                                <!-- Plafon Quick Chips -->
                                <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                                    <button type="button" onclick="setPlafonPreset(100000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-[11px] font-semibold text-slate-600 transition cursor-pointer">100 Jt</button>
                                    <button type="button" onclick="setPlafonPreset(250000000)" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-[11px] font-semibold transition cursor-pointer border border-blue-200/60">250 Jt</button>
                                    <button type="button" onclick="setPlafonPreset(500000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-[11px] font-semibold text-slate-600 transition cursor-pointer">500 Jt</button>
                                    <button type="button" onclick="setPlafonPreset(1000000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-[11px] font-semibold text-slate-600 transition cursor-pointer">1 Miliar</button>
                                    <button type="button" onclick="setPlafonPreset(2500000000)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-[11px] font-semibold text-slate-600 transition cursor-pointer">2,5 Miliar</button>
                                </div>
                            </div>

                            <!-- Tenor -->
                            <div>
                                <label for="s_tenor" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jangka Waktu (Tenor)</label>
                                <div class="relative">
                                    <input type="number" id="s_tenor" value="24" min="1" max="120" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix tabular-nums">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Bulan</span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-2">
                                    <button type="button" onclick="setTenorPreset(12)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-600 transition cursor-pointer">12 bln</button>
                                    <button type="button" onclick="setTenorPreset(24)" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-semibold transition cursor-pointer border border-blue-200/60">24 bln</button>
                                    <button type="button" onclick="setTenorPreset(36)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-600 transition cursor-pointer">36 bln</button>
                                    <button type="button" onclick="setTenorPreset(60)" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-600 transition cursor-pointer">60 bln</button>
                                </div>
                            </div>

                            <!-- Suku Bunga -->
                            <div>
                                <label for="s_bunga" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Suku Bunga per Tahun</label>
                                <div class="relative">
                                    <input type="number" id="s_bunga" value="12" min="0" max="48" step="0.25" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix tabular-nums">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">% p.a</span>
                                </div>
                            </div>

                            <!-- Metode Bunga -->
                            <div>
                                <label for="s_metode" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Metode Perhitungan Bunga</label>
                                <select id="s_metode" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="ANUITAS" selected>Anuitas (Bunga Efektif Menurun)</option>
                                    <option value="FLAT">Flat (Bunga Tetap)</option>
                                </select>
                            </div>

                            <!-- Skema Pembayaran -->
                            <div>
                                <label for="s_skema" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Skema Pembayaran</label>
                                <select id="s_skema" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Installment" selected>Installment (Pokok + Bunga Tiap Bulan)</option>
                                    <option value="Balloon Payment">Balloon Payment (Bunga Saja Tiap Bulan)</option>
                                </select>
                            </div>

                            <!-- Tujuan Penggunaan -->
                            <div class="sm:col-span-2">
                                <label for="s_tujuan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tujuan Penggunaan Dana</label>
                                <select id="s_tujuan" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Modal Kerja" selected>Modal Kerja Usaha (Stok, Operasional, Piutang)</option>
                                    <option value="Investasi">Investasi Usaha (Mesin, Renovasi, Armada)</option>
                                    <option value="Konsumtif">Konsumtif / Multiguna</option>
                                </select>
                            </div>
                        </div>

                        <!-- Perbandingan Skema Output Strip -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-4 rounded-xl border border-blue-200/80 bg-blue-50/50 relative overflow-hidden">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-blue-900">Installment (Pokok + Bunga)</span>
                                    <span class="text-[10px] font-bold text-blue-700 bg-blue-100/80 px-2 py-0.5 rounded-full">Standar</span>
                                </div>
                                <div id="out_t1_inst" class="text-xl font-extrabold text-blue-700 tabular-nums">Rp 0</div>
                                <div class="text-[11px] text-slate-500 mt-1">Angsuran tetap pokok dan bunga bulanan.</div>
                            </div>

                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 relative overflow-hidden">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-slate-800">Balloon Payment (Bunga Saja)</span>
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-200/80 px-2 py-0.5 rounded-full">Khusus</span>
                                </div>
                                <div id="out_t1_balloon" class="text-xl font-extrabold text-slate-900 tabular-nums">Rp 0</div>
                                <div class="text-[11px] text-slate-500 mt-1">Hanya bayar bunga, pokok dilunasi di akhir tenor.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: PROFIL & USAHA -->
                <div id="sim-tab-2" class="sim-tab-panel bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-white to-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">storefront</span>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Profil Debitur &amp; Usaha</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Parameter profil usaha untuk pembobotan aspek Character &amp; Condition.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100 tabular-nums">02 / 05</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="s_usaha" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Usaha / Perusahaan</label>
                                <input type="text" id="s_usaha" value="UD Sumber Rejeki" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>

                            <div>
                                <label for="s_bidang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bidang Usaha / Sektor</label>
                                <input type="text" id="s_bidang" value="Perdagangan Bahan Bangunan" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>

                            <div>
                                <label for="s_lama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lama Usaha Berjalan</label>
                                <div class="relative">
                                    <input type="number" id="s_lama" value="7" min="0" max="60" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix tabular-nums">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Tahun</span>
                                </div>
                            </div>

                            <div>
                                <label for="s_kol" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kolektibilitas SLIK (Simulasi)</label>
                                <select id="s_kol" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="1" selected>Kol 1 — Lancar (Tanpa Tunggakan)</option>
                                    <option value="2">Kol 2 — DPK (Tunggakan 1-90 Hari)</option>
                                    <option value="3">Kol 3 — Kurang Lancar (91-120 Hari)</option>
                                    <option value="5">Kol 5 — Macet (&gt; 180 Hari)</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="s_kota" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Domisili / Wilayah Operasional</label>
                                <input type="text" id="s_kota" value="Surabaya, Jawa Timur" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: AGUNAN & LTV -->
                <div id="sim-tab-3" class="sim-tab-panel bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-white to-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">home_work</span>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Penilaian Agunan &amp; LTV</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Estimasi nilai likuidasi, rasio Loan-to-Value (LTV), dan rasio coverage jaminan.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 tabular-nums">03 / 05</span>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="s_agunan_jenis" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Agunan</label>
                                <select id="s_agunan_jenis" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="SHM (Sertifikat Hak Milik)" selected>SHM (Hak Milik)</option>
                                    <option value="SHGB">SHGB (Guna Bangunan)</option>
                                    <option value="BPKB Mobil">BPKB Mobil / Kendaraan</option>
                                    <option value="Bilyet Deposito">Bilyet Deposito</option>
                                </select>
                            </div>

                            <div>
                                <label for="s_mv" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nilai Pasar (Market Value)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_mv" value="650.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix tabular-nums">
                                </div>
                            </div>

                            <div>
                                <label for="s_lv_pct" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Persentase Likuidasi</label>
                                <div class="relative">
                                    <input type="number" id="s_lv_pct" value="70" min="10" max="100" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix tabular-nums">
                                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agunan Metrics Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nilai Likuidasi (LV)</div>
                                <div id="out_t3_lv" class="text-lg font-extrabold text-slate-900 mt-1 tabular-nums">Rp 0</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Nilai taksasi aman jaminan.</div>
                            </div>

                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Rasio LTV (Plafon/MV)</div>
                                <div id="out_t3_ltv" class="text-lg font-extrabold text-slate-900 mt-1 tabular-nums">0%</div>
                                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Maks. 70-80% dari pasar.</div>
                            </div>

                            <div class="p-4 rounded-xl border border-blue-200/80 bg-blue-50/50">
                                <div class="text-[11px] font-bold text-blue-900 uppercase tracking-wider">Coverage Likuidasi</div>
                                <div id="out_t3_cov" class="text-lg font-extrabold text-blue-700 mt-1 tabular-nums">0%</div>
                                <div class="text-[11px] text-blue-600 font-semibold mt-0.5">Min. 100% plafon tercakup.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: ARUS KAS BULANAN -->
                <div id="sim-tab-4" class="sim-tab-panel bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-white to-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                            </div>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Arus Kas Bulanan &amp; Kemampuan Bayar</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Analisis pendapatan, modal kulakan, biaya hidup, dan kewajiban angsuran berjalan.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100 tabular-nums">04 / 05</span>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="s_omzet" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Omzet / Pendapatan Bulanan</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_omzet" value="120.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix tabular-nums">
                                </div>
                            </div>

                            <div>
                                <label for="s_hpp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">HPP / Biaya Kulakan</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_hpp" value="65.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix tabular-nums">
                                </div>
                            </div>

                            <div>
                                <label for="s_beban" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Beban Operasional &amp; Biaya Hidup</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_beban" value="20.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix tabular-nums">
                                </div>
                            </div>

                            <div>
                                <label for="s_cicilan_lain" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cicilan Berjalan Lainnya (SLIK)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_cicilan_lain" value="0" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix tabular-nums">
                                </div>
                            </div>
                        </div>

                        <!-- Cashflow Strip -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Laba Kotor (Gross Profit)</div>
                                <div id="out_t4_gross" class="text-lg font-extrabold text-slate-900 mt-1 tabular-nums">Rp 0</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Omzet dikurangi modal pokok kulakan.</div>
                            </div>

                            <div class="p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/50">
                                <div class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider">Net Cashflow (Surplus Bersih)</div>
                                <div id="out_t4_net" class="text-lg font-extrabold text-emerald-700 mt-1 tabular-nums">Rp 0</div>
                                <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Sisa kas riil untuk membayar angsuran.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: MEMO ANALISA KREDIT (HASIL LENGKAP) -->
                <div id="sim-tab-5" class="sim-tab-panel bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    <!-- Header with Actions -->
                    <div class="px-6 py-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-white via-blue-50/30 to-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm shadow-blue-500/25">
                                <span class="material-symbols-outlined text-[22px]">verified</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Memo Analisa Kredit (Simulasi)</h2>
                                    <span id="out_t5_badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Layak Disetujui</span>
                                </div>
                                <p id="out_t5_identitas" class="text-xs text-slate-500 mt-0.5">Budi Santoso — UD Sumber Rejeki</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 no-print">
                            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">print</span>
                                <span>Cetak PDF</span>
                            </button>
                            <a href="<?= $portalUrl ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm shadow-blue-500/25 transition">
                                <span>Ajukan di Portal</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Key Metrics Ledger (4 Cards) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 tabular-nums">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Skor 5C</div>
                                <div id="out_t5_skor" class="text-2xl font-extrabold text-blue-700 mt-1">85,6</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">dari skala 100</div>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">DSCR</div>
                                <div id="out_t5_dscr" class="text-2xl font-extrabold text-emerald-700 mt-1">2,97x</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Standar min. 1,30x</div>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">DSR</div>
                                <div id="out_t5_dsr" class="text-2xl font-extrabold text-slate-900 mt-1">9,8%</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Rasio beban omzet</div>
                            </div>
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Coverage LV</div>
                                <div id="out_t5_cov" class="text-2xl font-extrabold text-slate-900 mt-1">182,0%</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Standar min. 100%</div>
                            </div>
                        </div>

                        <!-- 5C Scoring Table -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pembobotan Analisis Prinsip 5C</h3>
                                <span class="text-xs text-slate-500">Bobot Total 100%</span>
                            </div>
                            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs">
                                <table class="w-full text-xs text-left tabular-nums">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                        <tr>
                                            <th class="px-4 py-3">Pilar 5C</th>
                                            <th class="px-4 py-3 text-right">Bobot</th>
                                            <th class="px-4 py-3 text-right">Skor Riil</th>
                                            <th class="px-4 py-3 w-44 hidden sm:table-cell">Distribusi</th>
                                            <th class="px-4 py-3 text-right">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="out_t5_5c_rows" class="divide-y divide-slate-100 text-slate-700 bg-white">
                                        <!-- Injected by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Facility Terms & Disbursement Slip -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 tabular-nums">
                            <!-- Ringkasan Fasilitas -->
                            <div class="border border-slate-200 rounded-xl p-5 space-y-3 bg-slate-50/50">
                                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200/80">
                                    <span class="material-symbols-outlined text-[18px] text-blue-600">tune</span>
                                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ringkasan Struktur Fasilitas</h3>
                                </div>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Tujuan &amp; Skema</span>
                                        <span id="out_t5_skema" class="font-bold text-slate-900">Modal Kerja &middot; Installment</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Tenor &amp; Bunga</span>
                                        <span id="out_t5_term" class="font-bold text-slate-900">24 bln &middot; 12% ANUITAS</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Jaminan Utama</span>
                                        <span id="out_t5_agunan" class="font-bold text-slate-900">SHM</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2">
                                        <span class="font-bold text-slate-700">Angsuran Kredit / Bln</span>
                                        <strong id="out_t5_angs" class="text-blue-700 font-extrabold text-sm">Rp 0</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimasi Pencairan -->
                            <div class="border border-slate-200 rounded-xl p-5 space-y-3 bg-slate-50/50">
                                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-200/80">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-600">receipt_long</span>
                                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Estimasi Pencairan Bersih</h3>
                                </div>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Plafon Disetujui</span>
                                        <span id="out_t5_plafon" class="font-bold text-slate-900">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Biaya Provisi (1,0%)</span>
                                        <span id="out_t5_provisi" class="text-rose-600 font-medium">- Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-500">Biaya Administrasi (0,5%)</span>
                                        <span id="out_t5_admin" class="text-rose-600 font-medium">- Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 text-sm">
                                        <span class="font-extrabold text-slate-900">Dana Diterima Bersih</span>
                                        <strong id="out_t5_cair" class="text-emerald-700 font-extrabold text-base">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Disclaimer Box -->
                        <div class="p-3.5 rounded-xl bg-slate-100/70 border border-slate-200/60 text-[11px] text-slate-500 leading-relaxed flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 shrink-0 mt-0.5">info</span>
                            <span>Hasil perhitungan di atas adalah simulasi indikatif client-side tanpa penyimpanan database. Persetujuan akhir bergantung pada verifikasi dokumen fisik, BI Checking / SLIK OJK, survey lokasi, dan rapat komite kredit.</span>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM WORKBENCH STEP BUTTONS -->
                <div class="flex items-center justify-between gap-3 pt-2 no-print">
                    <button type="button" id="btnSimPrev" onclick="navSimTab(-1)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined text-[16px]">west</span>
                        <span>Sebelumnya</span>
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="goSimTab(5)" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 hover:border-blue-300 bg-white hover:bg-blue-50 text-blue-700 text-xs font-bold transition cursor-pointer">
                            <span>Lihat Hasil Analisa</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                        <button type="button" id="btnSimNext" onclick="navSimTab(1)" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm shadow-blue-500/25 transition cursor-pointer">
                            <span>Langkah Berikutnya</span>
                            <span class="material-symbols-outlined text-[16px]">east</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: LIVE DECISION ENGINE RAIL (Cols 9-12, Sticky Desktop) -->
            <div class="lg:col-span-4 lg:sticky lg:top-20 space-y-4 no-print">
                <div class="bg-white border border-slate-200/90 rounded-2xl shadow-sm overflow-hidden">
                    
                    <!-- Rail Header -->
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50/80 to-white">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Keputusan Langsung</span>
                        </div>
                        <span id="rail_status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Layak
                        </span>
                    </div>

                    <!-- Hero Metric: Angsuran -->
                    <div class="p-5 border-b border-slate-100 bg-gradient-to-b from-blue-50/30 to-transparent">
                        <div class="text-xs font-semibold text-slate-500">Estimasi Angsuran Bulanan</div>
                        <div id="rail_angs" class="text-2xl font-extrabold text-blue-600 tracking-tight mt-1 tabular-nums">Rp 0</div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100">
                            <span>Plafon Pokok</span>
                            <span id="rail_plafon" class="font-bold text-slate-800 tabular-nums">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                            <span>Net Cashflow / bln</span>
                            <span id="rail_net" class="font-bold text-slate-800 tabular-nums">Rp 0</span>
                        </div>
                    </div>

                    <!-- 3-KPI Bar -->
                    <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100 text-center tabular-nums">
                        <div class="p-3.5">
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Skor 5C</div>
                            <div id="rail_skor" class="text-base font-extrabold text-blue-700 mt-0.5">85,6</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">dari 100</div>
                        </div>
                        <div class="p-3.5">
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">DSCR</div>
                            <div id="rail_dscr" class="text-base font-extrabold text-emerald-700 mt-0.5">2,97x</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">Min 1,3x</div>
                        </div>
                        <div class="p-3.5">
                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Cov. LV</div>
                            <div id="rail_cov" class="text-base font-extrabold text-slate-900 mt-0.5">182%</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">Min 100%</div>
                        </div>
                    </div>

                    <!-- Bottom Summary & Jump to Memo -->
                    <div class="p-4 bg-slate-50/70 space-y-3">
                        <div class="flex items-center justify-between text-xs tabular-nums">
                            <span class="text-slate-500 font-medium">Dana Cair Bersih:</span>
                            <strong id="rail_cair" class="text-slate-950 font-bold text-sm">Rp 0</strong>
                        </div>
                        <button type="button" onclick="goSimTab(5)" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-blue-600 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            <span>Buka Memo Lengkap</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>

                </div>

                <!-- Quick Help / Trust Box -->
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-start gap-3 text-xs text-slate-600">
                    <span class="material-symbols-outlined text-[20px] text-blue-600 shrink-0 mt-0.5">verified_user</span>
                    <p class="leading-relaxed text-[11px]">
                        Semua perhitungan menggunakan formula standar perbankan nasional untuk mitigasi risiko kredit yang akurat dan terpercaya.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="mt-auto py-6 border-t border-slate-200/80 bg-white text-center text-xs text-slate-500 no-print">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &middot; Sandbox Simulasi Kredit Publik
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="<?= base_url('/') ?>" class="hover:text-slate-700 transition">Beranda</a>
                <span>&bull;</span>
                <a href="<?= $portalUrl ?>" class="hover:text-blue-600 font-semibold transition">Masuk Portal</a>
            </div>
        </div>
    </footer>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let currentSimTab = 1;
        const totalSimTabs = 5;

        function goSimTab(n) {
            if (n < 1 || n > totalSimTabs) return;
            currentSimTab = n;

            for (let i = 1; i <= totalSimTabs; i++) {
                const panel = document.getElementById('sim-tab-' + i);
                const btn   = document.getElementById('sim-step-btn-' + i);
                const num   = document.getElementById('sim-step-num-' + i);

                if (panel) panel.classList.toggle('active', i === n);

                if (btn) {
                    if (i === n) {
                        btn.className = 'group shrink-0 inline-flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-blue-600 border-blue-600 text-white shadow-sm shadow-blue-500/25';
                    } else if (i < n) {
                        btn.className = 'group shrink-0 inline-flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-emerald-50/70 border-emerald-200 text-emerald-800 hover:bg-emerald-100/70';
                    } else {
                        btn.className = 'group shrink-0 inline-flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50';
                    }
                }

                if (num) {
                    if (i === n) {
                        num.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold tabular-nums bg-white/20 text-white';
                    } else if (i < n) {
                        num.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold tabular-nums bg-emerald-200/80 text-emerald-800';
                    } else {
                        num.className = 'w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold tabular-nums bg-slate-100 text-slate-600 group-hover:bg-slate-200';
                    }
                }
            }

            const stepEl  = document.getElementById('simStepNum');
            const stepMob = document.getElementById('simStepNumMobile');
            if (stepEl)  stepEl.innerText  = n;
            if (stepMob) stepMob.innerText = n;

            const progressBar = document.getElementById('simProgressBar');
            if (progressBar) {
                progressBar.style.width = ((n / totalSimTabs) * 100) + '%';
            }

            const btnPrev = document.getElementById('btnSimPrev');
            if (btnPrev) {
                btnPrev.disabled = (n === 1);
            }

            const btnNext = document.getElementById('btnSimNext');
            if (btnNext) {
                btnNext.style.display = (n === totalSimTabs) ? 'none' : 'inline-flex';
            }

            hitungSemuaSimulasi();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function navSimTab(delta) {
            goSimTab(currentSimTab + delta);
        }

        function pNum(val) {
            if (typeof val === 'number') return val;
            return parseFloat(String(val || '0').replace(/[^0-9]/g, '')) || 0;
        }

        function fmtRp(num) {
            const sign = num < 0 ? '-' : '';
            return sign + 'Rp ' + Math.round(Math.abs(num || 0)).toLocaleString('id-ID');
        }

        function fmtRpInput(el) {
            const raw = pNum(el.value);
            el.value = raw > 0 ? raw.toLocaleString('id-ID') : '0';
        }

        function setPlafonPreset(val) {
            const el = document.getElementById('s_plafon');
            if (el) {
                el.value = (val).toLocaleString('id-ID');
                hitungSemuaSimulasi();
            }
        }

        function setTenorPreset(val) {
            const el = document.getElementById('s_tenor');
            if (el) {
                el.value = val;
                hitungSemuaSimulasi();
            }
        }

        function hitungSemuaSimulasi() {
            const plafon   = pNum(document.getElementById('s_plafon')?.value);
            const tenor    = Math.max(1, parseInt(document.getElementById('s_tenor')?.value || 12, 10));
            const bungaThn = Math.max(0, parseFloat(document.getElementById('s_bunga')?.value || 0));
            const metode   = (document.getElementById('s_metode')?.value || 'ANUITAS').toUpperCase();
            const skema    = document.getElementById('s_skema')?.value || 'Installment';
            const tujuan   = document.getElementById('s_tujuan')?.value || 'Modal Kerja';
            const agunan   = document.getElementById('s_agunan_jenis')?.value || 'SHM';

            const r = (bungaThn / 100) / 12;
            const balloon = Math.round(plafon * r);
            let installment = 0;
            if (metode === 'ANUITAS' && r > 0) {
                installment = Math.round(plafon * (r / (1 - Math.pow(1 + r, -tenor))));
            } else {
                installment = Math.round((plafon / tenor) + (plafon * r));
            }
            const angsDipilih = skema === 'Balloon Payment' ? balloon : installment;

            const elInst = document.getElementById('out_t1_inst');
            const elBall = document.getElementById('out_t1_balloon');
            if (elInst) elInst.innerText = fmtRp(installment);
            if (elBall) elBall.innerText = fmtRp(balloon);

            // Agunan & LTV
            const mv    = pNum(document.getElementById('s_mv')?.value);
            const lvPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('s_lv_pct')?.value || 70)));
            const lv    = Math.round(mv * (lvPct / 100));
            const ltv   = mv > 0 ? (plafon / mv) * 100 : 0;
            const covLv = plafon > 0 ? (lv / plafon) * 100 : 0;

            const elLv  = document.getElementById('out_t3_lv');
            const elLtv = document.getElementById('out_t3_ltv');
            const elCov = document.getElementById('out_t3_cov');
            if (elLv)  elLv.innerText  = fmtRp(lv);
            if (elLtv) elLtv.innerText = ltv.toFixed(1).replace('.', ',') + '%';
            if (elCov) elCov.innerText = covLv.toFixed(1).replace('.', ',') + '%';

            // Arus Kas Bulanan
            const omzet       = pNum(document.getElementById('s_omzet')?.value);
            const hpp         = pNum(document.getElementById('s_hpp')?.value);
            const beban       = pNum(document.getElementById('s_beban')?.value);
            const cicilanLain = pNum(document.getElementById('s_cicilan_lain')?.value);
            const gross       = omzet - hpp;
            const netCf       = gross - beban;

            const elGross = document.getElementById('out_t4_gross');
            const elNet   = document.getElementById('out_t4_net');
            if (elGross) elGross.innerText = fmtRp(gross);
            if (elNet)   elNet.innerText   = fmtRp(netCf);

            // Rasio Finansial & Scoring 5C
            const totAngs = angsDipilih + cicilanLain;
            const dscr = totAngs > 0 ? (netCf / totAngs) : 0;
            const dsr  = omzet > 0 ? (totAngs / omzet) * 100 : 0;

            const kol  = parseInt(document.getElementById('s_kol')?.value || 1, 10);
            const lama = parseInt(document.getElementById('s_lama')?.value || 0, 10);

            const sChar = kol === 1 ? 88 : (kol === 2 ? 70 : (kol === 3 ? 52 : 30));
            const sCap  = dscr >= 2.0 ? 90 : (dscr >= 1.3 ? 80 : (dscr >= 1.0 ? 65 : 42));
            const sCpt  = netCf > 0 ? (omzet > 0 && (netCf / omzet) >= 0.2 ? 84 : 74) : 45;
            const sColl = covLv >= 120 ? 94 : (covLv >= 100 ? 84 : (covLv >= 75 ? 70 : 50));
            const sCond = lama >= 5 ? 85 : (lama >= 2 ? 76 : 65);

            const skorTotal = (sChar * 0.20) + (sCap * 0.30) + (sCpt * 0.15) + (sColl * 0.25) + (sCond * 0.10);

            const nama  = document.getElementById('s_nama')?.value || 'Calon Debitur';
            const usaha = document.getElementById('s_usaha')?.value || 'Usaha Simulasi';
            const elId  = document.getElementById('out_t5_identitas');
            if (elId) elId.innerText = `${nama} — ${usaha}`;

            const elT5Skor = document.getElementById('out_t5_skor');
            const elT5Dscr = document.getElementById('out_t5_dscr');
            const elT5Dsr  = document.getElementById('out_t5_dsr');
            const elT5Cov  = document.getElementById('out_t5_cov');
            if (elT5Skor) elT5Skor.innerText = skorTotal.toFixed(1).replace('.', ',');
            if (elT5Dscr) elT5Dscr.innerText = dscr.toFixed(2).replace('.', ',') + 'x';
            if (elT5Dsr)  elT5Dsr.innerText  = dsr.toFixed(1).replace('.', ',') + '%';
            if (elT5Cov)  elT5Cov.innerText  = covLv.toFixed(1).replace('.', ',') + '%';

            const elT5Skema  = document.getElementById('out_t5_skema');
            const elT5Term   = document.getElementById('out_t5_term');
            const elT5Agunan = document.getElementById('out_t5_agunan');
            if (elT5Skema)  elT5Skema.innerText  = `${tujuan} · ${skema}`;
            if (elT5Term)   elT5Term.innerText   = `${tenor} bln · ${bungaThn}% ${metode}`;
            if (elT5Agunan) elT5Agunan.innerText = agunan;

            // Status Decision & Badges
            let badgeText = 'Layak Bersyarat';
            let shortText = 'Bersyarat';
            let badgeCls  = 'bg-amber-50 text-amber-800 border-amber-200';

            if (kol >= 3 || dscr < 1.0 || skorTotal < 60) {
                badgeText = 'Risiko Tinggi';
                shortText = 'Risiko Tinggi';
                badgeCls  = 'bg-rose-50 text-rose-700 border-rose-200';
            } else if (skorTotal >= 78 && dscr >= 1.3 && covLv >= 100) {
                badgeText = 'Layak Disetujui';
                shortText = 'Layak Disetujui';
                badgeCls  = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            }

            const t5Badge = document.getElementById('out_t5_badge');
            if (t5Badge) {
                t5Badge.innerText = badgeText;
                t5Badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border ' + badgeCls;
            }

            const railStatus = document.getElementById('rail_status');
            if (railStatus) {
                railStatus.innerText = shortText;
                railStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border ' + badgeCls;
            }

            // 5C Breakdown Rows
            const rows = [
                { label: 'Character (SLIK & Integritas)', bobot: 20, val: sChar, color: 'bg-blue-600' },
                { label: 'Capacity (Kemampuan Arus Kas)',  bobot: 30, val: sCap,  color: 'bg-emerald-600' },
                { label: 'Capital (Permodalan Usaha)',    bobot: 15, val: sCpt,  color: 'bg-indigo-600' },
                { label: 'Collateral (Kecukupan Agunan)', bobot: 25, val: sColl, color: 'bg-cyan-600' },
                { label: 'Condition (Stabilitas Usaha)',  bobot: 10, val: sCond, color: 'bg-amber-600' },
            ];

            const elRows = document.getElementById('out_t5_5c_rows');
            if (elRows) {
                elRows.innerHTML = rows.map(row => {
                    const kontribusi = (row.val * row.bobot / 100).toFixed(2).replace('.', ',');
                    return `
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-semibold text-slate-900">${row.label}</td>
                            <td class="px-4 py-3 text-right text-slate-500 font-medium">${row.bobot}%</td>
                            <td class="px-4 py-3 text-right font-extrabold text-slate-900">${row.val}</td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full ${row.color} rounded-full" style="width:${row.val}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-blue-700">${kontribusi}</td>
                        </tr>
                    `;
                }).join('') + `
                    <tr class="bg-slate-50/90 font-extrabold text-slate-950 border-t border-slate-200">
                        <td class="px-4 py-3">Total Skor Kelayakan</td>
                        <td class="px-4 py-3 text-right">100%</td>
                        <td class="px-4 py-3 text-right">—</td>
                        <td class="px-4 py-3 hidden sm:table-cell"></td>
                        <td class="px-4 py-3 text-right text-blue-700 text-sm">${skorTotal.toFixed(2).replace('.', ',')}</td>
                    </tr>
                `;
            }

            // Disbursement Calculations
            const provisi = Math.round(plafon * 0.01);
            const admin   = Math.round(plafon * 0.005);
            const cair    = Math.max(0, plafon - provisi - admin);

            const elT5Plafon  = document.getElementById('out_t5_plafon');
            const elT5Provisi = document.getElementById('out_t5_provisi');
            const elT5Admin   = document.getElementById('out_t5_admin');
            const elT5Cair    = document.getElementById('out_t5_cair');
            const elT5Angs    = document.getElementById('out_t5_angs');

            if (elT5Plafon)  elT5Plafon.innerText  = fmtRp(plafon);
            if (elT5Provisi) elT5Provisi.innerText = '- ' + fmtRp(provisi);
            if (elT5Admin)   elT5Admin.innerText   = '- ' + fmtRp(admin);
            if (elT5Cair)    elT5Cair.innerText    = fmtRp(cair);
            if (elT5Angs)    elT5Angs.innerText    = fmtRp(angsDipilih);

            // Right Sticky Rail Updates
            const elRailAngs   = document.getElementById('rail_angs');
            const elRailPlafon = document.getElementById('rail_plafon');
            const elRailNet    = document.getElementById('rail_net');
            const elRailSkor   = document.getElementById('rail_skor');
            const elRailDscr   = document.getElementById('rail_dscr');
            const elRailCov    = document.getElementById('rail_cov');
            const elRailCair   = document.getElementById('rail_cair');

            if (elRailAngs)   elRailAngs.innerText   = fmtRp(angsDipilih);
            if (elRailPlafon) elRailPlafon.innerText = fmtRp(plafon);
            if (elRailNet)    elRailNet.innerText    = fmtRp(netCf);
            if (elRailSkor)   elRailSkor.innerText   = skorTotal.toFixed(1).replace('.', ',');
            if (elRailDscr)   elRailDscr.innerText   = dscr.toFixed(2).replace('.', ',') + 'x';
            if (elRailCov)    elRailCov.innerText    = Math.round(covLv) + '%';
            if (elRailCair)   elRailCair.innerText   = fmtRp(cair);

            // Mobile Compact Strip Updates
            const mobAngs = document.getElementById('mob_angs');
            const mobSkor = document.getElementById('mob_skor');
            const mobDscr = document.getElementById('mob_dscr');
            if (mobAngs) mobAngs.innerText = fmtRp(angsDipilih);
            if (mobSkor) mobSkor.innerText = skorTotal.toFixed(1).replace('.', ',');
            if (mobDscr) mobDscr.innerText = dscr.toFixed(2).replace('.', ',') + 'x';
        }

        function isiContohSimulasi() {
            document.getElementById('s_tipe').value = 'Perorangan';
            document.getElementById('s_nama').value = 'Budi Santoso';
            document.getElementById('s_plafon').value = '250.000.000';
            document.getElementById('s_tenor').value = '24';
            document.getElementById('s_bunga').value = '12';
            document.getElementById('s_metode').value = 'ANUITAS';
            document.getElementById('s_skema').value = 'Installment';
            document.getElementById('s_tujuan').value = 'Modal Kerja';
            document.getElementById('s_usaha').value = 'UD Sumber Rejeki';
            document.getElementById('s_bidang').value = 'Perdagangan Bahan Bangunan';
            document.getElementById('s_lama').value = '7';
            document.getElementById('s_kol').value = '1';
            document.getElementById('s_kota').value = 'Surabaya, Jawa Timur';
            document.getElementById('s_agunan_jenis').value = 'SHM (Sertifikat Hak Milik)';
            document.getElementById('s_mv').value = '650.000.000';
            document.getElementById('s_lv_pct').value = '70';
            document.getElementById('s_omzet').value = '120.000.000';
            document.getElementById('s_hpp').value = '65.000.000';
            document.getElementById('s_beban').value = '20.000.000';
            document.getElementById('s_cicilan_lain').value = '0';
            hitungSemuaSimulasi();
        }

        function resetSimulasi() {
            document.getElementById('s_nama').value = '';
            document.getElementById('s_plafon').value = '0';
            document.getElementById('s_tenor').value = '12';
            document.getElementById('s_bunga').value = '12';
            document.getElementById('s_usaha').value = '';
            document.getElementById('s_bidang').value = '';
            document.getElementById('s_lama').value = '0';
            document.getElementById('s_kol').value = '1';
            document.getElementById('s_kota').value = '';
            document.getElementById('s_mv').value = '0';
            document.getElementById('s_lv_pct').value = '70';
            document.getElementById('s_omzet').value = '0';
            document.getElementById('s_hpp').value = '0';
            document.getElementById('s_beban').value = '0';
            document.getElementById('s_cicilan_lain').value = '0';
            hitungSemuaSimulasi();
            goSimTab(1);
        }

        document.addEventListener('DOMContentLoaded', () => {
            goSimTab(1);
        });
    </script>
</body>
</html>