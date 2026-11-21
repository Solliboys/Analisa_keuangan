<?php
    $logoWeb    = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : 'img/logo-oap.png';
    $namaWeb    = 'OTW Approved';
    $isLoggedIn = (bool) session()->get('logged_in');
    $portalUrl  = $isLoggedIn ? base_url('dashboard') : base_url('auth');

    $simSteps = [
        1 => ['num' => '01', 'title' => 'Aplikasi Kredit',   'sub' => 'Plafon, tenor & bunga'],
        2 => ['num' => '02', 'title' => 'Profil & Usaha',    'sub' => 'Debitur, bidang & SLIK'],
        3 => ['num' => '03', 'title' => 'Agunan & LTV',      'sub' => 'Nilai pasar & likuidasi'],
        4 => ['num' => '04', 'title' => 'Arus Kas Bulanan',  'sub' => 'Omzet, HPP & beban'],
        5 => ['num' => '05', 'title' => 'Memo Analisa',      'sub' => 'Skor 5C, DSCR & cair'],
    ];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Pengajuan Kredit — <?= esc($namaWeb) ?></title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url($logoWeb) ?>">
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
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
        .fin-input {
            width: 100%;
            height: 38px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 500;
            color: #0f172a;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }
        .fin-input:hover { border-color: #94a3b8; }
        .fin-input:focus {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
        }
        .fin-input-prefix { padding-left: 38px; }
        .fin-input-suffix { padding-right: 46px; }
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
            main { max-width: 100% !important; padding: 0 !important; }
            .sim-tab-panel { display: none !important; }
            #sim-tab-5 { display: block !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col overflow-x-hidden">

    <!-- Top Utility Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 no-print">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleSimDrawer()" class="lg:hidden inline-flex items-center justify-center w-8 h-8 rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 cursor-pointer shrink-0" aria-label="Buka Navigasi Tahapan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 shrink-0">
                    <img src="<?= base_url($logoWeb) ?>" alt="<?= esc($namaWeb) ?>" class="h-7 w-7 object-contain">
                    <span class="text-sm font-bold text-slate-950 tracking-tight"><?= esc($namaWeb) ?></span>
                </a>
                <span class="hidden sm:inline text-slate-300">/</span>
                <span class="hidden sm:inline text-xs font-medium text-slate-600 truncate">Simulasi Pengajuan Kredit</span>
                <span class="hidden md:inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-slate-200 bg-slate-50 text-[11px] font-medium text-slate-600">
                    Sandbox &middot; Data tidak disimpan
                </span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="isiContohSimulasi()" class="px-2.5 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-medium transition cursor-pointer">
                    Isi Contoh
                </button>
                <button type="button" onclick="resetSimulasi()" class="hidden sm:inline-flex px-2.5 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-medium transition cursor-pointer">
                    Reset
                </button>
                <a href="<?= $portalUrl ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition">
                    <span>Masuk Portal</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Compact Live Strip -->
    <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-2 no-print">
        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1.5">
            <span>Sandbox &middot; Tidak disimpan</span>
            <span class="font-semibold text-slate-800 tabular-nums">Tahap <span id="simStepNumMobile">1</span>/5</span>
        </div>
        <div class="grid grid-cols-3 divide-x divide-slate-200 border border-slate-200 rounded-md bg-slate-50/70 text-center tabular-nums">
            <div class="py-1.5 px-1">
                <div class="text-[9px] font-medium text-slate-500 uppercase">Angsuran/bln</div>
                <div id="mob_angs" class="text-[11px] font-bold text-slate-950 truncate">Rp 0</div>
            </div>
            <div class="py-1.5 px-1">
                <div class="text-[9px] font-medium text-slate-500 uppercase">Skor 5C</div>
                <div id="mob_skor" class="text-[11px] font-bold text-blue-700">85,6</div>
            </div>
            <div class="py-1.5 px-1">
                <div class="text-[9px] font-medium text-slate-500 uppercase">DSCR</div>
                <div id="mob_dscr" class="text-[11px] font-bold text-slate-950">2,97x</div>
            </div>
        </div>
    </div>

    <!-- Backdrop Mobile Drawer -->
    <div id="simDrawerBackdrop" onclick="toggleSimDrawer(false)" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden no-print"></div>

    <!-- Main 3-Column Workbench -->
    <main class="w-full max-w-[1360px] mx-auto px-4 sm:px-6 py-6 flex-1 min-w-0">
        <div class="flex flex-col lg:flex-row items-start gap-6 min-w-0">

            <!-- LEFT RAIL: STEP NAVIGATION -->
            <aside id="simDrawer" class="fixed inset-y-0 left-0 z-50 w-64 max-w-[82vw] -translate-x-full transition-transform duration-200 ease-out bg-white border-r border-slate-200 p-4 flex flex-col lg:static lg:sticky lg:top-20 lg:z-10 lg:translate-x-0 lg:w-56 lg:max-w-none lg:shrink-0 lg:bg-transparent lg:border-r-0 lg:p-0 no-print">
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-200 lg:border-none lg:pb-1">
                    <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Alur Simulasi</span>
                    <button type="button" onclick="toggleSimDrawer(false)" class="lg:hidden p-1 rounded text-slate-400 hover:text-slate-700 cursor-pointer" aria-label="Tutup">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <nav class="space-y-0.5 flex-1">
                    <?php foreach ($simSteps as $n => $st): ?>
                        <button type="button" onclick="goSimTab(<?= $n ?>)" id="sim-step-btn-<?= $n ?>"
                                class="w-full text-left px-3 py-2.5 rounded-md border-l-2 transition flex items-start gap-2.5 cursor-pointer <?= $n === 1 ? 'bg-white border-blue-700 text-slate-950 shadow-[0_1px_2px_rgba(15,23,42,0.04)]' : 'border-transparent text-slate-600 hover:bg-white/70 hover:text-slate-900' ?>">
                            <span id="sim-step-num-<?= $n ?>" class="text-[11px] font-semibold tabular-nums mt-0.5 <?= $n === 1 ? 'text-blue-700' : 'text-slate-400' ?>">
                                <?= $st['num'] ?>
                            </span>
                            <div class="min-w-0">
                                <div class="text-[13px] font-semibold leading-snug truncate"><?= $st['title'] ?></div>
                                <div class="text-[11px] text-slate-500 truncate"><?= $st['sub'] ?></div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </nav>

                <div class="pt-4 mt-4 border-t border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-[11px] text-slate-500">
                        <span>Progres</span>
                        <span class="font-semibold text-slate-800 tabular-nums"><span id="simStepNum">1</span> / 5</span>
                    </div>
                    <div class="h-1 w-full bg-slate-200 rounded-full overflow-hidden">
                        <div id="simProgressBar" class="h-full bg-blue-700 transition-all duration-200" style="width: 20%;"></div>
                    </div>
                    <div class="pt-2 flex items-center justify-between">
                        <a href="<?= base_url('/') ?>" class="text-[11px] font-medium text-slate-500 hover:text-slate-900 transition">
                            &larr; Beranda
                        </a>
                        <button type="button" onclick="resetSimulasi()" class="sm:hidden text-[11px] font-medium text-slate-500 hover:text-slate-900 cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>
            </aside>

            <!-- CENTER WORKSPACE: FORM SHEET -->
            <section class="flex-1 w-full min-w-0 space-y-4">

                <!-- TAB 1: APLIKASI KREDIT -->
                <div id="sim-tab-1" class="sim-tab-panel active bg-white border border-slate-200 rounded-lg">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <h1 class="text-base font-bold text-slate-950 tracking-tight">Parameter Aplikasi Kredit</h1>
                            <p class="text-xs text-slate-500 mt-0.5">Struktur plafon, tenor, suku bunga, dan skema pembayaran.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 tabular-nums shrink-0">01 / 05</span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="s_tipe" class="block text-xs font-medium text-slate-700 mb-1.5">Tipe Pemohon</label>
                                <select id="s_tipe" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Perorangan" selected>Perorangan / Wiraswasta</option>
                                    <option value="Badan Usaha">Badan Usaha (PT / CV / UD)</option>
                                </select>
                            </div>
                            <div>
                                <label for="s_nama" class="block text-xs font-medium text-slate-700 mb-1.5">Nama Debitur / Pemohon</label>
                                <input type="text" id="s_nama" value="Budi Santoso" oninput="hitungSemuaSimulasi()" placeholder="Nama lengkap pemohon" class="fin-input">
                            </div>
                            <div>
                                <label for="s_plafon" class="block text-xs font-medium text-slate-700 mb-1.5">Plafon Pengajuan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_plafon" value="250.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                            <div>
                                <label for="s_tenor" class="block text-xs font-medium text-slate-700 mb-1.5">Jangka Waktu (Tenor)</label>
                                <div class="relative">
                                    <input type="number" id="s_tenor" value="24" min="1" max="120" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix font-semibold tabular-nums">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 select-none">bln</span>
                                </div>
                            </div>
                            <div>
                                <label for="s_bunga" class="block text-xs font-medium text-slate-700 mb-1.5">Suku Bunga per Tahun</label>
                                <div class="relative">
                                    <input type="number" id="s_bunga" value="12" min="0" max="48" step="0.25" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix font-semibold tabular-nums">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 select-none">% p.a</span>
                                </div>
                            </div>
                            <div>
                                <label for="s_metode" class="block text-xs font-medium text-slate-700 mb-1.5">Metode Perhitungan Bunga</label>
                                <select id="s_metode" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="ANUITAS" selected>Anuitas</option>
                                    <option value="FLAT">Flat</option>
                                </select>
                            </div>
                            <div>
                                <label for="s_skema" class="block text-xs font-medium text-slate-700 mb-1.5">Skema Pembayaran</label>
                                <select id="s_skema" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Installment" selected>Installment (Pokok + Bunga)</option>
                                    <option value="Balloon Payment">Balloon Payment (Bunga Saja)</option>
                                </select>
                            </div>
                            <div>
                                <label for="s_tujuan" class="block text-xs font-medium text-slate-700 mb-1.5">Tujuan Penggunaan Dana</label>
                                <select id="s_tujuan" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="Modal Kerja" selected>Modal Kerja</option>
                                    <option value="Investasi">Investasi Usaha</option>
                                    <option value="Konsumtif">Konsumtif / Multiguna</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hairline Calculation Strip -->
                        <div class="border border-slate-200 rounded-md divide-y sm:divide-y-0 sm:divide-x divide-slate-200 grid grid-cols-1 sm:grid-cols-2 bg-slate-50/60 tabular-nums">
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Installment (Pokok + Bunga / bln)</div>
                                <div id="out_t1_inst" class="text-lg font-bold text-slate-950 mt-1">Rp 0</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Balloon Payment (Bunga / bln)</div>
                                <div id="out_t1_balloon" class="text-lg font-bold text-slate-950 mt-1">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: PROFIL & USAHA -->
                <div id="sim-tab-2" class="sim-tab-panel bg-white border border-slate-200 rounded-lg">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-950 tracking-tight">Profil Debitur &amp; Usaha</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Informasi usaha dan riwayat kredit untuk pembobotan Character &amp; Condition.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 tabular-nums shrink-0">02 / 05</span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="s_usaha" class="block text-xs font-medium text-slate-700 mb-1.5">Nama Usaha / Perusahaan</label>
                                <input type="text" id="s_usaha" value="UD Sumber Rejeki" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>
                            <div>
                                <label for="s_bidang" class="block text-xs font-medium text-slate-700 mb-1.5">Bidang Usaha / Sektor</label>
                                <input type="text" id="s_bidang" value="Perdagangan Bahan Bangunan" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>
                            <div>
                                <label for="s_lama" class="block text-xs font-medium text-slate-700 mb-1.5">Lama Usaha / Bekerja</label>
                                <div class="relative">
                                    <input type="number" id="s_lama" value="7" min="0" max="60" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix font-semibold tabular-nums">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 select-none">thn</span>
                                </div>
                            </div>
                            <div>
                                <label for="s_kol" class="block text-xs font-medium text-slate-700 mb-1.5">Kolektibilitas SLIK (Simulasi)</label>
                                <select id="s_kol" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="1" selected>Kol 1 — Lancar</option>
                                    <option value="2">Kol 2 — Dalam Perhatian Khusus (DPK)</option>
                                    <option value="3">Kol 3 — Kurang Lancar</option>
                                    <option value="5">Kol 5 — Macet</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="s_kota" class="block text-xs font-medium text-slate-700 mb-1.5">Domisili / Kota Usaha</label>
                                <input type="text" id="s_kota" value="Surabaya, Jawa Timur" oninput="hitungSemuaSimulasi()" class="fin-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: AGUNAN & LTV -->
                <div id="sim-tab-3" class="sim-tab-panel bg-white border border-slate-200 rounded-lg">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-950 tracking-tight">Penilaian Agunan &amp; LTV</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Estimasi nilai likuidasi (LV), rasio LTV, dan kecukupan jaminan terhadap plafon.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 tabular-nums shrink-0">03 / 05</span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="s_agunan_jenis" class="block text-xs font-medium text-slate-700 mb-1.5">Jenis Agunan</label>
                                <select id="s_agunan_jenis" onchange="hitungSemuaSimulasi()" class="fin-input">
                                    <option value="SHM (Sertifikat Hak Milik)" selected>SHM (Sertifikat Hak Milik)</option>
                                    <option value="SHGB">SHGB</option>
                                    <option value="BPKB Mobil">BPKB Mobil</option>
                                    <option value="Bilyet Deposito">Bilyet Deposito</option>
                                </select>
                            </div>
                            <div>
                                <label for="s_mv" class="block text-xs font-medium text-slate-700 mb-1.5">Nilai Pasar (Market Value)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_mv" value="650.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                            <div>
                                <label for="s_lv_pct" class="block text-xs font-medium text-slate-700 mb-1.5">Persentase Likuidasi</label>
                                <div class="relative">
                                    <input type="number" id="s_lv_pct" value="70" min="10" max="100" oninput="hitungSemuaSimulasi()" class="fin-input fin-input-suffix font-semibold tabular-nums">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 select-none">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="border border-slate-200 rounded-md divide-y sm:divide-y-0 sm:divide-x divide-slate-200 grid grid-cols-1 sm:grid-cols-3 bg-slate-50/60 tabular-nums">
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Nilai Likuidasi (LV)</div>
                                <div id="out_t3_lv" class="text-base font-bold text-slate-950 mt-1">Rp 0</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">LTV (Plafon / MV)</div>
                                <div id="out_t3_ltv" class="text-base font-bold text-slate-950 mt-1">0%</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Coverage LV</div>
                                <div id="out_t3_cov" class="text-base font-bold text-blue-700 mt-1">0%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: ARUS KAS BULANAN -->
                <div id="sim-tab-4" class="sim-tab-panel bg-white border border-slate-200 rounded-lg">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-950 tracking-tight">Mutasi Rekening &amp; Arus Kas Bulanan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Rekap pendapatan, HPP (kulakan), biaya operasional, dan kewajiban cicilan berjalan.</p>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-400 tabular-nums shrink-0">04 / 05</span>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="s_omzet" class="block text-xs font-medium text-slate-700 mb-1.5">Omzet / Pendapatan Bulanan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_omzet" value="120.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                            <div>
                                <label for="s_hpp" class="block text-xs font-medium text-slate-700 mb-1.5">HPP / Modal Kulakan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_hpp" value="65.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                            <div>
                                <label for="s_beban" class="block text-xs font-medium text-slate-700 mb-1.5">Beban Operasional &amp; Biaya Hidup</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_beban" value="20.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                            <div>
                                <label for="s_cicilan_lain" class="block text-xs font-medium text-slate-700 mb-1.5">Angsuran Eksisting / SLIK per Bulan</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400 select-none">Rp</span>
                                    <input type="text" id="s_cicilan_lain" value="0" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="fin-input fin-input-prefix font-semibold tabular-nums">
                                </div>
                            </div>
                        </div>

                        <div class="border border-slate-200 rounded-md divide-y sm:divide-y-0 sm:divide-x divide-slate-200 grid grid-cols-1 sm:grid-cols-2 bg-slate-50/60 tabular-nums">
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Laba Kotor (Omzet &minus; HPP)</div>
                                <div id="out_t4_gross" class="text-base font-bold text-slate-950 mt-1">Rp 0</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Net Cashflow / Surplus Bulanan</div>
                                <div id="out_t4_net" class="text-base font-bold text-blue-700 mt-1">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: MEMO ANALISA KREDIT -->
                <div id="sim-tab-5" class="sim-tab-panel bg-white border border-slate-200 rounded-lg">
                    <div class="px-5 sm:px-6 py-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-slate-950 tracking-tight">Memo Analisa Kredit (Simulasi)</h2>
                                <span id="out_t5_badge" class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Layak Disetujui</span>
                            </div>
                            <p id="out_t5_identitas" class="text-xs text-slate-500 mt-0.5">Budi Santoso — UD Sumber Rejeki</p>
                        </div>
                        <div class="flex items-center gap-2 no-print">
                            <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium text-slate-700 transition cursor-pointer">
                                Cetak PDF
                            </button>
                            <a href="<?= $portalUrl ?>" class="px-3 py-1.5 rounded-md bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold transition">
                                Ajukan di Portal
                            </a>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">
                        <!-- Key Metrics Ledger -->
                        <div class="border border-slate-200 rounded-md divide-y sm:divide-y-0 sm:divide-x divide-slate-200 grid grid-cols-2 sm:grid-cols-4 tabular-nums">
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Skor 5C</div>
                                <div id="out_t5_skor" class="text-xl font-bold text-slate-950 mt-1">85,6</div>
                                <div class="text-[11px] text-slate-400">dari 100</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">DSCR</div>
                                <div id="out_t5_dscr" class="text-xl font-bold text-slate-950 mt-1">2,97x</div>
                                <div class="text-[11px] text-slate-400">Min. 1,30x</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">DSR</div>
                                <div id="out_t5_dsr" class="text-xl font-bold text-slate-950 mt-1">9,8%</div>
                                <div class="text-[11px] text-slate-400">Rasio angsuran</div>
                            </div>
                            <div class="p-4">
                                <div class="text-[11px] font-medium text-slate-500">Coverage LV</div>
                                <div id="out_t5_cov" class="text-xl font-bold text-slate-950 mt-1">182,0%</div>
                                <div class="text-[11px] text-slate-400">Min. 100%</div>
                            </div>
                        </div>

                        <!-- 5C Scoring Table -->
                        <div>
                            <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wider mb-2.5">Pembobotan Skor 5C</h3>
                            <div class="border border-slate-200 rounded-md overflow-x-auto">
                                <table class="w-full text-xs text-left tabular-nums">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] text-slate-500 uppercase">
                                        <tr>
                                            <th class="px-4 py-2.5 font-semibold">Komponen</th>
                                            <th class="px-4 py-2.5 font-semibold text-right">Bobot</th>
                                            <th class="px-4 py-2.5 font-semibold text-right">Skor</th>
                                            <th class="px-4 py-2.5 font-semibold w-40 hidden sm:table-cell">Indikator</th>
                                            <th class="px-4 py-2.5 font-semibold text-right">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="out_t5_5c_rows" class="divide-y divide-slate-200 text-slate-700">
                                        <!-- Diisi JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Disbursement & Facility Terms -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 tabular-nums">
                            <div class="border border-slate-200 rounded-md p-4 space-y-2.5 text-xs">
                                <h3 class="text-[11px] font-semibold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">Ringkasan Fasilitas</h3>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tujuan &amp; Skema</span>
                                    <span id="out_t5_skema" class="font-medium text-slate-900">Modal Kerja &middot; Installment</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Tenor &amp; Bunga</span>
                                    <span id="out_t5_term" class="font-medium text-slate-900">24 bln &middot; 12% ANUITAS</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Agunan</span>
                                    <span id="out_t5_agunan" class="font-medium text-slate-900">SHM</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-slate-100">
                                    <span class="font-medium text-slate-700">Angsuran Kredit Baru / Bln</span>
                                    <strong id="out_t5_angs" class="text-blue-700 font-bold">Rp 0</strong>
                                </div>
                            </div>

                            <div class="border border-slate-200 rounded-md p-4 space-y-2.5 text-xs">
                                <h3 class="text-[11px] font-semibold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">Estimasi Pencairan (Disbursement)</h3>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Plafon Disetujui</span>
                                    <span id="out_t5_plafon" class="font-medium text-slate-900">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Biaya Provisi (1%)</span>
                                    <span id="out_t5_provisi" class="text-slate-600">- Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Biaya Administrasi (0,5%)</span>
                                    <span id="out_t5_admin" class="text-slate-600">- Rp 0</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-slate-200 text-[13px]">
                                    <span class="font-semibold text-slate-900">Estimasi Dana Cair Bersih</span>
                                    <strong id="out_t5_cair" class="text-slate-950 font-bold">Rp 0</strong>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Catatan: Hasil di atas bersifat indikatif dari simulasi client-side tanpa penyimpanan database. Keputusan akhir tetap mengacu pada verifikasi SLIK OJK, analisa rekening koran, dan persetujuan komite kredit di portal resmi.
                        </p>
                    </div>
                </div>

                <!-- Step Footer Navigation -->
                <div class="flex items-center justify-between gap-3 pt-1 no-print">
                    <button type="button" id="btnSimPrev" onclick="navSimTab(-1)" class="px-3.5 py-2 rounded-md border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium text-slate-700 transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                        &larr; Sebelumnya
                    </button>
                    <button type="button" id="btnSimNext" onclick="navSimTab(1)" class="px-4 py-2 rounded-md bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold transition cursor-pointer">
                        Selanjutnya &rarr;
                    </button>
                </div>

            </section>

            <!-- RIGHT RAIL: LIVE UNDERWRITING PREVIEW (Desktop Sticky) -->
            <aside class="w-full lg:w-72 xl:w-80 lg:shrink-0 lg:sticky lg:top-20 no-print">
                <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-200">
                    <div class="px-4 py-3 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Ringkasan Langsung</span>
                        <span id="rail_status" class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Layak</span>
                    </div>

                    <div class="p-4 space-y-3 tabular-nums">
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-slate-500">Angsuran / bln</span>
                            <span id="rail_angs" class="text-base font-bold text-slate-950">Rp 0</span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-slate-500">Plafon</span>
                            <span id="rail_plafon" class="text-xs font-semibold text-slate-800">Rp 0</span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-xs text-slate-500">Net Cashflow / bln</span>
                            <span id="rail_net" class="text-xs font-semibold text-slate-800">Rp 0</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 divide-x divide-slate-200 text-center tabular-nums">
                        <div class="py-3 px-2">
                            <div class="text-[10px] font-medium text-slate-500 uppercase">Skor 5C</div>
                            <div id="rail_skor" class="text-sm font-bold text-slate-950 mt-0.5">85,6</div>
                        </div>
                        <div class="py-3 px-2">
                            <div class="text-[10px] font-medium text-slate-500 uppercase">DSCR</div>
                            <div id="rail_dscr" class="text-sm font-bold text-slate-950 mt-0.5">2,97x</div>
                        </div>
                        <div class="py-3 px-2">
                            <div class="text-[10px] font-medium text-slate-500 uppercase">Cov. LV</div>
                            <div id="rail_cov" class="text-sm font-bold text-slate-950 mt-0.5">182%</div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-50/70 rounded-b-lg flex items-center justify-between gap-2">
                        <div class="text-[11px] text-slate-500 tabular-nums">
                            Dana cair <strong id="rail_cair" class="text-slate-900">Rp 0</strong>
                        </div>
                        <button type="button" onclick="goSimTab(5)" class="text-[11px] font-semibold text-blue-700 hover:text-blue-800 cursor-pointer shrink-0">
                            Lihat Memo &rarr;
                        </button>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <script>
        let currentSimTab = 1;
        const totalSimTabs = 5;

        function toggleSimDrawer(forceOpen) {
            const drawer = document.getElementById('simDrawer');
            const backdrop = document.getElementById('simDrawerBackdrop');
            if (!drawer) return;
            const isClosed = drawer.classList.contains('-translate-x-full');
            const open = typeof forceOpen === 'boolean' ? forceOpen : isClosed;
            drawer.classList.toggle('-translate-x-full', !open);
            if (backdrop) backdrop.classList.toggle('hidden', !open);
        }

        function goSimTab(n) {
            if (n < 1 || n > totalSimTabs) return;
            currentSimTab = n;
            if (window.innerWidth < 1024) toggleSimDrawer(false);

            for (let i = 1; i <= totalSimTabs; i++) {
                const panel = document.getElementById('sim-tab-' + i);
                const btn = document.getElementById('sim-step-btn-' + i);
                const num = document.getElementById('sim-step-num-' + i);
                if (panel) panel.classList.toggle('active', i === n);
                if (btn) {
                    btn.className = 'w-full text-left px-3 py-2.5 rounded-md border-l-2 transition flex items-start gap-2.5 cursor-pointer ' +
                        (i === n
                            ? 'bg-white border-blue-700 text-slate-950 shadow-[0_1px_2px_rgba(15,23,42,0.04)]'
                            : 'border-transparent text-slate-600 hover:bg-white/70 hover:text-slate-900');
                }
                if (num) {
                    num.className = 'text-[11px] font-semibold tabular-nums mt-0.5 ' +
                        (i === n ? 'text-blue-700' : (i < n ? 'text-emerald-600' : 'text-slate-400'));
                }
            }

            const stepEl = document.getElementById('simStepNum');
            const stepMob = document.getElementById('simStepNumMobile');
            if (stepEl) stepEl.innerText = n;
            if (stepMob) stepMob.innerText = n;

            document.getElementById('simProgressBar').style.width = ((n / totalSimTabs) * 100) + '%';
            document.getElementById('btnSimPrev').disabled = (n === 1);
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

            document.getElementById('out_t1_inst').innerText = fmtRp(installment);
            document.getElementById('out_t1_balloon').innerText = fmtRp(balloon);

            // Agunan
            const mv    = pNum(document.getElementById('s_mv')?.value);
            const lvPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('s_lv_pct')?.value || 70)));
            const lv    = Math.round(mv * (lvPct / 100));
            const ltv   = mv > 0 ? (plafon / mv) * 100 : 0;
            const covLv = plafon > 0 ? (lv / plafon) * 100 : 0;

            document.getElementById('out_t3_lv').innerText = fmtRp(lv);
            document.getElementById('out_t3_ltv').innerText = ltv.toFixed(1).replace('.', ',') + '%';
            document.getElementById('out_t3_cov').innerText = covLv.toFixed(1).replace('.', ',') + '%';

            // Keuangan
            const omzet       = pNum(document.getElementById('s_omzet')?.value);
            const hpp         = pNum(document.getElementById('s_hpp')?.value);
            const beban       = pNum(document.getElementById('s_beban')?.value);
            const cicilanLain = pNum(document.getElementById('s_cicilan_lain')?.value);
            const gross       = omzet - hpp;
            const netCf       = gross - beban;

            document.getElementById('out_t4_gross').innerText = fmtRp(gross);
            document.getElementById('out_t4_net').innerText = fmtRp(netCf);

            // Rasio & 5C
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
            document.getElementById('out_t5_identitas').innerText = `${nama} — ${usaha}`;
            document.getElementById('out_t5_skor').innerText = skorTotal.toFixed(1).replace('.', ',');
            document.getElementById('out_t5_dscr').innerText = dscr.toFixed(2).replace('.', ',') + 'x';
            document.getElementById('out_t5_dsr').innerText  = dsr.toFixed(1).replace('.', ',') + '%';
            document.getElementById('out_t5_cov').innerText  = covLv.toFixed(1).replace('.', ',') + '%';
            document.getElementById('out_t5_skema').innerText  = `${tujuan} · ${skema}`;
            document.getElementById('out_t5_term').innerText   = `${tenor} bln · ${bungaThn}% ${metode}`;
            document.getElementById('out_t5_agunan').innerText = agunan;

            let badgeText = 'Layak Bersyarat';
            let shortText = 'Bersyarat';
            let badgeCls  = 'bg-amber-50 text-amber-800 border-amber-200';
            if (kol >= 3 || dscr < 1.0 || skorTotal < 60) {
                badgeText = 'Risiko Tinggi';
                shortText = 'Risiko Tinggi';
                badgeCls  = 'bg-rose-50 text-rose-700 border-rose-200';
            } else if (skorTotal >= 78 && dscr >= 1.3 && covLv >= 100) {
                badgeText = 'Layak Disetujui';
                shortText = 'Layak';
                badgeCls  = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            }

            const t5Badge = document.getElementById('out_t5_badge');
            t5Badge.innerText = badgeText;
            t5Badge.className = 'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border ' + badgeCls;

            const railStatus = document.getElementById('rail_status');
            railStatus.innerText = shortText;
            railStatus.className = 'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border ' + badgeCls;

            const rows = [
                { label: 'Character',  bobot: 20, val: sChar },
                { label: 'Capacity',   bobot: 30, val: sCap  },
                { label: 'Capital',    bobot: 15, val: sCpt  },
                { label: 'Collateral', bobot: 25, val: sColl },
                { label: 'Condition',  bobot: 10, val: sCond },
            ];
            document.getElementById('out_t5_5c_rows').innerHTML = rows.map(row => {
                const kontribusi = (row.val * row.bobot / 100).toFixed(2).replace('.', ',');
                return `
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-slate-900">${row.label}</td>
                        <td class="px-4 py-2.5 text-right text-slate-500">${row.bobot}%</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900">${row.val}</td>
                        <td class="px-4 py-2.5 hidden sm:table-cell">
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-700 rounded-full" style="width:${row.val}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold text-slate-900">${kontribusi}</td>
                    </tr>
                `;
            }).join('') + `
                <tr class="bg-slate-50 font-bold text-slate-950">
                    <td class="px-4 py-2.5">Total Skor Kelayakan</td>
                    <td class="px-4 py-2.5 text-right">100%</td>
                    <td class="px-4 py-2.5 text-right">—</td>
                    <td class="px-4 py-2.5 hidden sm:table-cell"></td>
                    <td class="px-4 py-2.5 text-right">${skorTotal.toFixed(2).replace('.', ',')}</td>
                </tr>
            `;

            // Disbursement
            const provisi = Math.round(plafon * 0.01);
            const admin   = Math.round(plafon * 0.005);
            const cair    = Math.max(0, plafon - provisi - admin);

            document.getElementById('out_t5_plafon').innerText  = fmtRp(plafon);
            document.getElementById('out_t5_provisi').innerText = '- ' + fmtRp(provisi);
            document.getElementById('out_t5_admin').innerText   = '- ' + fmtRp(admin);
            document.getElementById('out_t5_cair').innerText    = fmtRp(cair);
            document.getElementById('out_t5_angs').innerText    = fmtRp(angsDipilih);

            // Right Sticky Rail & Mobile Strip
            document.getElementById('rail_angs').innerText   = fmtRp(angsDipilih);
            document.getElementById('rail_plafon').innerText = fmtRp(plafon);
            document.getElementById('rail_net').innerText    = fmtRp(netCf);
            document.getElementById('rail_skor').innerText   = skorTotal.toFixed(1).replace('.', ',');
            document.getElementById('rail_dscr').innerText   = dscr.toFixed(2).replace('.', ',') + 'x';
            document.getElementById('rail_cov').innerText    = Math.round(covLv) + '%';
            document.getElementById('rail_cair').innerText   = fmtRp(cair);

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
