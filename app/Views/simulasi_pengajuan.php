<?php
    $logoWeb = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : 'img/logo-oap.png';
    $namaWeb = 'OTW Approved';
    $isLoggedIn = (bool) session()->get('logged_in');
    $portalUrl  = $isLoggedIn ? base_url('dashboard') : base_url('auth');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
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
        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; }
            .sim-tab-panel { display: none !important; }
            #sim-tab-5 { display: block !important; border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col pb-24 overflow-x-hidden">

    <!-- Sticky Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 no-print">
        <div class="w-full px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                <!-- Hamburger Mobile Stepper -->
                <button type="button" onclick="toggleSimDrawer()" class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition cursor-pointer shrink-0" aria-label="Buka Menu Tahapan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <a href="<?= base_url('/') ?>" class="flex items-center gap-2 shrink-0">
                    <img src="<?= base_url($logoWeb) ?>" alt="<?= esc($namaWeb) ?>" class="h-8 w-8 rounded-lg object-contain">
                    <div class="hidden sm:block leading-tight">
                        <span class="text-sm font-extrabold text-slate-950 block tracking-tight"><?= esc($namaWeb) ?></span>
                        <span class="text-[10px] font-semibold text-slate-500 block">Simulasi Pengajuan Kredit</span>
                    </div>
                </a>
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Mode Uji Coba — Data tidak disimpan ke database
                </span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="isiContohSimulasi()" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition cursor-pointer">
                    Isi Contoh
                </button>
                <a href="<?= $portalUrl ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold transition">
                    <span>Masuk Portal</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Info Strip -->
    <div class="md:hidden bg-amber-50 border-b border-amber-200 px-3 py-2 text-[11px] font-semibold text-amber-800 text-center no-print">
        Mode Uji Coba (Sandbox) — Data simulasi ini tidak disimpan ke database.
    </div>

    <!-- Backdrop Mobile Drawer -->
    <div id="simDrawerBackdrop" onclick="toggleSimDrawer(false)" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 hidden lg:hidden no-print"></div>

    <!-- Main Layout -->
    <main class="w-full max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-7 flex-1 min-w-0">
        <div class="flex flex-col lg:flex-row items-start gap-6 lg:gap-8 min-w-0">

            <!-- SIDEBAR STEPPER KIRI (1 - 5) -->
            <aside id="simDrawer" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full transition-transform duration-300 ease-in-out bg-white border-r border-slate-200 p-5 shadow-2xl flex flex-col lg:static lg:sticky lg:top-20 lg:z-10 lg:translate-x-0 lg:w-72 lg:max-w-none lg:shrink-0 lg:rounded-2xl lg:border lg:shadow-2xs no-print">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <span class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Tahap <span id="simStepNum">1</span> / 5</span>
                    <button type="button" onclick="toggleSimDrawer(false)" class="lg:hidden p-1 rounded-lg text-slate-400 hover:bg-slate-100 cursor-pointer" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="h-1.5 w-full bg-slate-100 rounded-full mb-4 overflow-hidden">
                    <div id="simProgressBar" class="h-full bg-blue-700 rounded-full transition-all duration-300" style="width: 20%;"></div>
                </div>

                <?php
                $simSteps = [
                    1 => ['title' => '1. Aplikasi Kredit',    'sub' => 'Plafon, tenor & bunga'],
                    2 => ['title' => '2. Profil & Usaha',     'sub' => 'Data debitur & usaha'],
                    3 => ['title' => '3. Agunan & LTV',       'sub' => 'Nilai pasar & likuidasi'],
                    4 => ['title' => '4. Mutasi & Keuangan',  'sub' => 'Omzet, HPP & beban'],
                    5 => ['title' => '5. Hasil & Laporan',    'sub' => '5C, DSCR & pencairan'],
                ];
                ?>
                <div class="space-y-1.5 flex-1">
                    <?php foreach ($simSteps as $n => $st): ?>
                        <button type="button" onclick="goSimTab(<?= $n ?>)" id="sim-step-btn-<?= $n ?>"
                                class="w-full text-left p-2.5 rounded-xl border transition flex items-center gap-3 cursor-pointer <?= $n === 1 ? 'bg-blue-50 border-blue-200' : 'border-transparent hover:bg-slate-50' ?>">
                            <span id="sim-step-circle-<?= $n ?>" class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= $n === 1 ? 'bg-blue-700 text-white' : 'bg-white border border-slate-300 text-slate-500' ?>">
                                <?= $n ?>
                            </span>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-800 truncate"><?= $st['title'] ?></div>
                                <div class="text-[11px] text-slate-500 truncate"><?= $st['sub'] ?></div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100 space-y-2">
                    <button type="button" onclick="resetSimulasi()" class="w-full py-2 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-600 transition cursor-pointer">
                        Reset Semua Isian
                    </button>
                    <a href="<?= base_url('/') ?>" class="block text-center text-[11px] font-semibold text-slate-400 hover:text-slate-700 transition">
                        &larr; Kembali ke Beranda
                    </a>
                </div>
            </aside>

            <!-- CONTENT PANEL KANAN -->
            <div class="flex-1 w-full min-w-0 space-y-6">

                <!-- =================================================
                     TAB 1: APLIKASI KREDIT
                ================================================== -->
                <div id="sim-tab-1" class="sim-tab-panel active bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div>
                            <h1 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">1. Parameter Aplikasi Kredit</h1>
                            <p class="text-xs text-slate-500 mt-0.5">Tentukan struktur pembiayaan untuk menghitung estimasi angsuran bulanan secara langsung.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">Sandbox</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Pemohon</label>
                            <select id="s_tipe" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="Perorangan" selected>Perorangan / Wiraswasta</option>
                                <option value="Badan Usaha">Badan Usaha (PT / CV / UD)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Debitur / Pemohon</label>
                            <input type="text" id="s_nama" value="Budi Santoso" oninput="hitungSemuaSimulasi()" placeholder="Nama lengkap pemohon" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Plafon Pengajuan (Rp)</label>
                            <input type="text" id="s_plafon" value="250.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-extrabold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jangka Waktu / Tenor (Bulan)</label>
                            <input type="number" id="s_tenor" value="24" min="1" max="120" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Suku Bunga per Tahun (%)</label>
                            <input type="number" id="s_bunga" value="12" min="0" max="48" step="0.25" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Metode Perhitungan Bunga</label>
                            <select id="s_metode" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="ANUITAS" selected>Anuitas</option>
                                <option value="FLAT">Flat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Skema Pembayaran</label>
                            <select id="s_skema" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="Installment" selected>Installment (Pokok + Bunga)</option>
                                <option value="Balloon Payment">Balloon Payment (Bunga Saja)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tujuan Penggunaan Dana</label>
                            <select id="s_tujuan" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="Modal Kerja" selected>Modal Kerja</option>
                                <option value="Investasi">Investasi Usaha</option>
                                <option value="Konsumtif">Konsumtif / Multiguna</option>
                            </select>
                        </div>
                    </div>

                    <!-- Live Output Bar Tab 1 -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200">
                            <div class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Estimasi Installment / Bulan</div>
                            <div id="out_t1_inst" class="text-xl font-extrabold text-blue-900 tabular-nums mt-1">Rp 0</div>
                            <div class="text-[11px] text-blue-600 mt-0.5">Pokok + bunga bulanan sesuai metode dipilih</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Estimasi Balloon Payment / Bulan</div>
                            <div id="out_t1_balloon" class="text-xl font-extrabold text-slate-900 tabular-nums mt-1">Rp 0</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Pembayaran bunga bulanan (pokok di akhir tenor)</div>
                        </div>
                    </div>
                </div>

                <!-- =================================================
                     TAB 2: PROFIL & USAHA
                ================================================== -->
                <div id="sim-tab-2" class="sim-tab-panel bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                    <div class="pb-4 border-b border-slate-100">
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">2. Profil Debitur &amp; Usaha</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Informasi latar belakang usaha atau pekerjaan untuk penilaian karakter &amp; kondisi usaha.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Usaha / Perusahaan</label>
                            <input type="text" id="s_usaha" value="UD Sumber Rejeki" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Bidang Usaha / Sektor</label>
                            <input type="text" id="s_bidang" value="Perdagangan Bahan Bangunan" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Lama Usaha / Bekerja (Tahun)</label>
                            <input type="number" id="s_lama" value="7" min="0" max="60" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kolektibilitas SLIK (Simulasi)</label>
                            <select id="s_kol" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="1" selected>Kol 1 — Lancar</option>
                                <option value="2">Kol 2 — Dalam Perhatian Khusus (DPK)</option>
                                <option value="3">Kol 3 — Kurang Lancar</option>
                                <option value="5">Kol 5 — Macet</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Domisili / Kota Usaha</label>
                            <input type="text" id="s_kota" value="Surabaya, Jawa Timur" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:border-blue-600 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- =================================================
                     TAB 3: AGUNAN & LTV
                ================================================== -->
                <div id="sim-tab-3" class="sim-tab-panel bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                    <div class="pb-4 border-b border-slate-100">
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">3. Penilaian Agunan &amp; Coverage LTV</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Hitung nilai likuidasi (LV), rasio LTV, dan persentase penutupan jaminan terhadap plafon.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Agunan</label>
                            <select id="s_agunan_jenis" onchange="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-white focus:border-blue-600 focus:outline-none">
                                <option value="SHM (Sertifikat Hak Milik)" selected>SHM (Sertifikat Hak Milik)</option>
                                <option value="SHGB">SHGB</option>
                                <option value="BPKB Mobil">BPKB Mobil</option>
                                <option value="Bilyet Deposito">Bilyet Deposito</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nilai Pasar / MV (Rp)</label>
                            <input type="text" id="s_mv" value="650.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-extrabold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Persentase Likuidasi (%)</label>
                            <input type="number" id="s_lv_pct" value="70" min="10" max="100" oninput="hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 tabular-nums">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-[11px] font-bold text-slate-500 uppercase">Nilai Likuidasi (LV)</div>
                            <div id="out_t3_lv" class="text-lg font-extrabold text-slate-950 mt-1">Rp 0</div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-[11px] font-bold text-slate-500 uppercase">Rasio LTV (Plafon / MV)</div>
                            <div id="out_t3_ltv" class="text-lg font-extrabold text-blue-700 mt-1">0%</div>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200">
                            <div class="text-[11px] font-bold text-emerald-700 uppercase">Coverage LV</div>
                            <div id="out_t3_cov" class="text-lg font-extrabold text-emerald-800 mt-1">0%</div>
                        </div>
                    </div>
                </div>

                <!-- =================================================
                     TAB 4: MUTASI REKENING & DATA KEUANGAN
                ================================================== -->
                <div id="sim-tab-4" class="sim-tab-panel bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                    <div class="pb-4 border-b border-slate-100">
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">4. Mutasi Rekening &amp; Arus Kas Bulanan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Masukkan estimasi omzet bulanan, HPP (kulakan), biaya operasional, dan cicilan berjalan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pendapatan / Omzet Bulanan (Rp)</label>
                            <input type="text" id="s_omzet" value="120.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-extrabold text-blue-800 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">HPP / Modal Kulakan per Bulan (Rp)</label>
                            <input type="text" id="s_hpp" value="65.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Beban Operasional &amp; Hidup (Rp)</label>
                            <input type="text" id="s_beban" value="20.000.000" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Cicilan Pinjaman Lain / SLIK per Bulan (Rp)</label>
                            <input type="text" id="s_cicilan_lain" value="0" oninput="fmtRpInput(this); hitungSemuaSimulasi()" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs font-bold text-slate-900 tabular-nums focus:border-blue-600 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 tabular-nums">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-[11px] font-bold text-slate-500 uppercase">Laba Kotor (Omzet − HPP)</div>
                            <div id="out_t4_gross" class="text-lg font-extrabold text-slate-900 mt-1">Rp 0</div>
                        </div>
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200">
                            <div class="text-[11px] font-bold text-blue-700 uppercase">Surplus / Net Cashflow Bulanan</div>
                            <div id="out_t4_net" class="text-lg font-extrabold text-blue-900 mt-1">Rp 0</div>
                        </div>
                    </div>
                </div>

                <!-- =================================================
                     TAB 5: HASIL ANALISA, SKOR 5C & DISBURSEMENT
                ================================================== -->
                <div id="sim-tab-5" class="sim-tab-panel bg-white rounded-2xl border border-slate-200 p-5 sm:p-7 space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">5. Ringkasan Hasil Analisa &amp; Pencairan</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Estimasi kelayakan kredit berdasarkan data simulasi yang Anda masukkan.</p>
                        </div>
                        <div class="flex items-center gap-2 no-print">
                            <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer">
                                Cetak / Simpan PDF
                            </button>
                            <a href="<?= $portalUrl ?>" class="px-4 py-2 rounded-lg bg-slate-950 hover:bg-slate-800 text-white text-xs font-bold transition">
                                Ajukan Resmi di Portal &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Top Executive Summary -->
                    <div class="rounded-2xl bg-slate-950 text-white p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rekomendasi Indikatif</div>
                            <div id="out_t5_keputusan" class="text-xl sm:text-2xl font-extrabold text-emerald-400">Layak Disetujui</div>
                            <div class="text-xs text-slate-400">
                                <span id="out_t5_identitas">Budi Santoso — UD Sumber Rejeki</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 tabular-nums">
                            <div>
                                <div class="text-[10px] font-semibold uppercase text-slate-400">Skor 5C</div>
                                <div id="out_t5_skor" class="text-2xl font-extrabold text-white">85,6</div>
                            </div>
                            <div class="border-l border-slate-800 pl-6">
                                <div class="text-[10px] font-semibold uppercase text-slate-400">DSCR</div>
                                <div id="out_t5_dscr" class="text-2xl font-extrabold text-emerald-400">2,97x</div>
                            </div>
                            <div class="border-l border-slate-800 pl-6">
                                <div class="text-[10px] font-semibold uppercase text-slate-400">DSR</div>
                                <div id="out_t5_dsr" class="text-2xl font-extrabold text-white">9,8%</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2 Cols: 5C Breakdown + Disbursement -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- 5C Breakdown -->
                        <div class="rounded-xl border border-slate-200 p-5 space-y-3">
                            <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Rincian Skor 5C</div>
                            <div class="space-y-2.5 text-xs tabular-nums" id="out_t5_5c_bars">
                                <!-- Diisi JS -->
                            </div>
                        </div>

                        <!-- Estimasi Disbursement -->
                        <div class="rounded-xl border border-slate-200 p-5 space-y-3 text-xs tabular-nums">
                            <div class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Simulasi Pencairan (Disbursement)</div>
                            <div class="space-y-2 pt-1">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Plafon Disetujui</span>
                                    <strong id="out_t5_plafon" class="text-slate-900">Rp 0</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Biaya Provisi (1%)</span>
                                    <span id="out_t5_provisi" class="text-rose-600 font-semibold">- Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Biaya Administrasi (0,5%)</span>
                                    <span id="out_t5_admin" class="text-rose-600 font-semibold">- Rp 0</span>
                                </div>
                                <div class="pt-2.5 border-t border-slate-200 flex justify-between items-center text-sm">
                                    <span class="font-extrabold text-slate-900">Estimasi Dana Bersih Cair</span>
                                    <strong id="out_t5_cair" class="text-emerald-700 font-extrabold">Rp 0</strong>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex justify-between text-[11px] text-slate-500">
                                <span>Angsuran Kredit Baru / Bln</span>
                                <strong id="out_t5_angs" class="text-blue-700">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Bottom Nav Controls -->
                <div class="flex items-center justify-between gap-3 pt-2 no-print">
                    <button type="button" id="btnSimPrev" onclick="navSimTab(-1)" class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 transition cursor-pointer disabled:opacity-40">
                        &larr; Sebelumnya
                    </button>
                    <button type="button" id="btnSimNext" onclick="navSimTab(1)" class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold transition cursor-pointer">
                        Selanjutnya &rarr;
                    </button>
                </div>

            </div>
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
                const circle = document.getElementById('sim-step-circle-' + i);
                if (panel) panel.classList.toggle('active', i === n);
                if (btn) {
                    btn.className = 'w-full text-left p-2.5 rounded-xl border transition flex items-center gap-3 cursor-pointer ' +
                        (i === n ? 'bg-blue-50 border-blue-200' : 'border-transparent hover:bg-slate-50');
                }
                if (circle) {
                    circle.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 ' +
                        (i === n ? 'bg-blue-700 text-white' : (i < n ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-300 text-slate-500'));
                    circle.innerHTML = i < n ? '✓' : i;
                }
            }

            document.getElementById('simStepNum').innerText = n;
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
            return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
        }

        function fmtRpInput(el) {
            const raw = pNum(el.value);
            el.value = raw > 0 ? raw.toLocaleString('id-ID') : '0';
        }

        function hitungSemuaSimulasi() {
            const plafon = pNum(document.getElementById('s_plafon')?.value);
            const tenor  = Math.max(1, parseInt(document.getElementById('s_tenor')?.value || 12, 10));
            const bungaThn = Math.max(0, parseFloat(document.getElementById('s_bunga')?.value || 0));
            const metode = (document.getElementById('s_metode')?.value || 'ANUITAS').toUpperCase();
            const skema  = document.getElementById('s_skema')?.value || 'Installment';

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
            const mv = pNum(document.getElementById('s_mv')?.value);
            const lvPct = Math.min(100, Math.max(0, parseFloat(document.getElementById('s_lv_pct')?.value || 70)));
            const lv = Math.round(mv * (lvPct / 100));
            const ltv = mv > 0 ? (plafon / mv) * 100 : 0;
            const covLv = plafon > 0 ? (lv / plafon) * 100 : 0;

            document.getElementById('out_t3_lv').innerText = fmtRp(lv);
            document.getElementById('out_t3_ltv').innerText = ltv.toFixed(1).replace('.', ',') + '%';
            document.getElementById('out_t3_cov').innerText = covLv.toFixed(1).replace('.', ',') + '%';

            // Keuangan
            const omzet = pNum(document.getElementById('s_omzet')?.value);
            const hpp   = pNum(document.getElementById('s_hpp')?.value);
            const beban = pNum(document.getElementById('s_beban')?.value);
            const cicilanLain = pNum(document.getElementById('s_cicilan_lain')?.value);
            const gross = omzet - hpp;
            const netCf = gross - beban;

            document.getElementById('out_t4_gross').innerText = fmtRp(gross);
            document.getElementById('out_t4_net').innerText = fmtRp(netCf);

            // Rasio & 5C
            const totAngs = angsDipilih + cicilanLain;
            const dscr = totAngs > 0 ? (netCf / totAngs) : 0;
            const dsr  = omzet > 0 ? (totAngs / omzet) * 100 : 0;

            const kol = parseInt(document.getElementById('s_kol')?.value || 1, 10);
            const lama = parseInt(document.getElementById('s_lama')?.value || 0, 10);

            const sChar = kol === 1 ? 88 : (kol === 2 ? 70 : (kol === 3 ? 52 : 30));
            const sCap  = dscr >= 2.0 ? 90 : (dscr >= 1.3 ? 80 : (dscr >= 1.0 ? 65 : 42));
            const sCpt  = netCf > 0 ? (omzet > 0 && (netCf / omzet) >= 0.2 ? 84 : 74) : 45;
            const sColl = covLv >= 120 ? 94 : (covLv >= 100 ? 84 : (covLv >= 75 ? 70 : 50));
            const sCond = lama >= 5 ? 85 : (lama >= 2 ? 76 : 65);

            const skorTotal = (sChar * 0.20) + (sCap * 0.30) + (sCpt * 0.15) + (sColl * 0.25) + (sCond * 0.10);

            const nama = document.getElementById('s_nama')?.value || 'Calon Debitur';
            const usaha = document.getElementById('s_usaha')?.value || 'Usaha Simulasi';
            document.getElementById('out_t5_identitas').innerText = `${nama} — ${usaha}`;
            document.getElementById('out_t5_skor').innerText = skorTotal.toFixed(1).replace('.', ',');
            document.getElementById('out_t5_dscr').innerText = dscr.toFixed(2).replace('.', ',') + 'x';
            document.getElementById('out_t5_dsr').innerText = dsr.toFixed(1).replace('.', ',') + '%';

            const kepEl = document.getElementById('out_t5_keputusan');
            if (kol >= 3 || dscr < 1.0 || skorTotal < 60) {
                kepEl.innerText = 'Risiko Tinggi / Tidak Direkomendasikan';
                kepEl.className = 'text-xl sm:text-2xl font-extrabold text-rose-400';
            } else if (skorTotal >= 78 && dscr >= 1.3 && covLv >= 100) {
                kepEl.innerText = 'Layak Disetujui';
                kepEl.className = 'text-xl sm:text-2xl font-extrabold text-emerald-400';
            } else {
                kepEl.innerText = 'Layak Bersyarat / Perlu Tinjauan';
                kepEl.className = 'text-xl sm:text-2xl font-extrabold text-amber-400';
            }

            const bars = [
                { label: 'Character (20%)', val: sChar, color: 'bg-slate-900' },
                { label: 'Capacity (30%)',  val: sCap,  color: 'bg-blue-700' },
                { label: 'Capital (15%)',   val: sCpt,  color: 'bg-slate-700' },
                { label: 'Collateral (25%)',val: sColl, color: 'bg-emerald-600' },
                { label: 'Condition (10%)', val: sCond, color: 'bg-slate-500' },
            ];
            document.getElementById('out_t5_5c_bars').innerHTML = bars.map(b => `
                <div class="flex items-center gap-3">
                    <span class="w-28 text-slate-600 font-medium">${b.label}</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full ${b.color} rounded-full" style="width:${b.val}%"></div>
                    </div>
                    <span class="w-8 text-right font-bold text-slate-900">${b.val}</span>
                </div>
            `).join('');

            // Disbursement
            const provisi = Math.round(plafon * 0.01);
            const admin   = Math.round(plafon * 0.005);
            const cair    = Math.max(0, plafon - provisi - admin);

            document.getElementById('out_t5_plafon').innerText = fmtRp(plafon);
            document.getElementById('out_t5_provisi').innerText = '- ' + fmtRp(provisi);
            document.getElementById('out_t5_admin').innerText = '- ' + fmtRp(admin);
            document.getElementById('out_t5_cair').innerText = fmtRp(cair);
            document.getElementById('out_t5_angs').innerText = fmtRp(angsDipilih);
        }

        function isiContohSimulasi() {
            document.getElementById('s_tipe').value = 'Perorangan';
            document.getElementById('s_nama').value = 'Budi Santoso';
            document.getElementById('s_plafon').value = '250.000.000';
            document.getElementById('s_tenor').value = '24';
            document.getElementById('s_bunga').value = '12';
            document.getElementById('s_metode').value = 'ANUITAS';
            document.getElementById('s_skema').value = 'Installment';
            document.getElementById('s_usaha').value = 'UD Sumber Rejeki';
            document.getElementById('s_bidang').value = 'Perdagangan Bahan Bangunan';
            document.getElementById('s_lama').value = '7';
            document.getElementById('s_kol').value = '1';
            document.getElementById('s_kota').value = 'Surabaya, Jawa Timur';
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
            document.getElementById('s_usaha').value = '';
            document.getElementById('s_bidang').value = '';
            document.getElementById('s_mv').value = '0';
            document.getElementById('s_omzet').value = '0';
            document.getElementById('s_hpp').value = '0';
            document.getElementById('s_beban').value = '0';
            document.getElementById('s_cicilan_lain').value = '0';
            hitungSemuaSimulasi();
            goSimTab(1);
        }

        document.addEventListener('DOMContentLoaded', () => {
            hitungSemuaSimulasi();
        });
    </script>
</body>
</html>
