<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | OTW Credit Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .step-tab.active {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: 600;
        }
        .step-tab.completed {
            border-color: #6366f1;
            color: #4338ca;
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        /* Custom sleek scrollbar for verticalStepperList */
        #verticalStepperList {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        #verticalStepperList::-webkit-scrollbar {
            width: 4px;
        }
        #verticalStepperList::-webkit-scrollbar-track {
            background: transparent;
        }
        #verticalStepperList::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
        #verticalStepperList::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col pb-24">

    <!-- Sticky Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-700 bg-slate-100 hover:bg-blue-50 border border-slate-200 rounded-xl transition" title="Kembali ke Dashboard">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali</span>
                    </a>
                    <span id="badgeNomorAplikasi" class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <?= esc($nomor_aplikasi) ?>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span id="saveStatusIndicator" class="text-xs text-slate-500 flex items-center gap-1.5 hidden sm:flex">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span> Siap disimpan
                    </span>
                    <?php if (!empty($kredit_id)): ?>
                        <a href="<?= base_url('pengajuan/cetak/' . $kredit_id) ?>" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition flex items-center gap-1">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak CAM
                        </a>
                    <?php endif; ?>
                    <button type="button" onclick="simpanForm(false)" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Layout: 2 Columns (Sidebar Stepper 1-13 + Form Content) -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1">
        <div class="flex flex-col lg:flex-row items-start gap-8">
            
            <!-- SIDEBAR STEPPER KIRI (1 - 13) -->
            <aside class="w-full lg:w-80 shrink-0 sticky top-20 bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs max-h-[calc(100vh-140px)] flex flex-col">
                <!-- Stepper Progress Header -->
                <div class="flex items-center justify-between pb-3 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        Tahap <span id="sidebarTahapNum">1</span> dari 12
                    </span>
                    <span id="sidebarTahapBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                        1/12
                    </span>
                </div>

                <!-- Horizontal Progress Bar -->
                <div class="h-1.5 w-full bg-slate-100 rounded-full mb-4 overflow-hidden shrink-0">
                    <div id="progressBarFill" class="h-full bg-blue-600 rounded-full transition-all duration-300" style="width: 8.3%;"></div>
                </div>

                <!-- Vertical Stepper List (12 Tahap Pengusul + 13 Dinonaktifkan) -->
                <div class="flex-1 overflow-y-auto scroll-smooth space-y-1 relative pr-1" id="verticalStepperList">
                    <!-- Step 1 -->
                    <button type="button" onclick="goToTab(1)" id="step-item-1" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 bg-blue-50/80 border border-blue-200/80">
                        <span id="step-circle-1" class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-xs shrink-0">1</span>
                        <div class="min-w-0">
                            <div id="step-title-1" class="text-xs font-bold text-blue-900 truncate">1. Aplikasi</div>
                            <div id="step-sub-1" class="text-[11px] text-blue-600 font-semibold truncate">Sedang dikerjakan</div>
                        </div>
                    </button>

                    <!-- Step 2 -->
                    <button type="button" onclick="goToTab(2)" id="step-item-2" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-2" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                        <div class="min-w-0">
                            <div id="step-title-2" class="text-xs font-bold text-slate-700 truncate">2. Info Pribadi</div>
                            <div id="step-sub-2" class="text-[11px] text-slate-400 truncate">Data demografi</div>
                        </div>
                    </button>

                    <!-- Step 3 -->
                    <button type="button" onclick="goToTab(3)" id="step-item-3" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-3" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                        <div class="min-w-0">
                            <div id="step-title-3" class="text-xs font-bold text-slate-700 truncate">3. Info Usaha</div>
                            <div id="step-sub-3" class="text-[11px] text-slate-400 truncate">Legalitas usaha</div>
                        </div>
                    </button>

                    <!-- Step 4 -->
                    <button type="button" onclick="goToTab(4)" id="step-item-4" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-4" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                        <div class="min-w-0">
                            <div id="step-title-4" class="text-xs font-bold text-slate-700 truncate">4. Jaminan</div>
                            <div id="step-sub-4" class="text-[11px] text-slate-400 truncate">Agunan kredit</div>
                        </div>
                    </button>

                    <!-- Step 5 -->
                    <button type="button" onclick="goToTab(5)" id="step-item-5" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-5" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">5</span>
                        <div class="min-w-0">
                            <div id="step-title-5" class="text-xs font-bold text-slate-700 truncate">5. Dokumen</div>
                            <div id="step-sub-5" class="text-[11px] text-slate-400 truncate">Unggah berkas</div>
                        </div>
                    </button>

                    <!-- Step 6 -->
                    <button type="button" onclick="goToTab(6)" id="step-item-6" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-6" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">6</span>
                        <div class="min-w-0">
                            <div id="step-title-6" class="text-xs font-bold text-slate-700 truncate">6. SLIK / CLIK</div>
                            <div id="step-sub-6" class="text-[11px] text-slate-400 truncate">Cek riwayat kredit</div>
                        </div>
                    </button>

                    <!-- Step 7 -->
                    <button type="button" onclick="goToTab(7)" id="step-item-7" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-7" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">7</span>
                        <div class="min-w-0">
                            <div id="step-title-7" class="text-xs font-bold text-slate-700 truncate">7. Web Check</div>
                            <div id="step-sub-7" class="text-[11px] text-slate-400 truncate">Verifikasi online</div>
                        </div>
                    </button>

                    <!-- Step 8 -->
                    <button type="button" onclick="goToTab(8)" id="step-item-8" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-8" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">8</span>
                        <div class="min-w-0">
                            <div id="step-title-8" class="text-xs font-bold text-slate-700 truncate">8. Analisa Kredit</div>
                            <div id="step-sub-8" class="text-[11px] text-slate-400 truncate">Scoring kelayakan</div>
                        </div>
                    </button>

                    <!-- Step 9 -->
                    <button type="button" onclick="goToTab(9)" id="step-item-9" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-9" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">9</span>
                        <div class="min-w-0">
                            <div id="step-title-9" class="text-xs font-bold text-slate-700 truncate">9. Rekening Koran</div>
                            <div id="step-sub-9" class="text-[11px] text-slate-400 truncate">Analisis mutasi</div>
                        </div>
                    </button>

                    <!-- Step 10 -->
                    <button type="button" onclick="goToTab(10)" id="step-item-10" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-10" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">10</span>
                        <div class="min-w-0">
                            <div id="step-title-10" class="text-xs font-bold text-slate-700 truncate">10. Laba Rugi</div>
                            <div id="step-sub-10" class="text-[11px] text-slate-400 truncate">Proyeksi keuangan</div>
                        </div>
                    </button>

                    <!-- Step 11 -->
                    <button type="button" onclick="goToTab(11)" id="step-item-11" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-11" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">11</span>
                        <div class="min-w-0">
                            <div id="step-title-11" class="text-xs font-bold text-slate-700 truncate">11. Repayment</div>
                            <div id="step-sub-11" class="text-[11px] text-slate-400 truncate">Kemampuan angsur</div>
                        </div>
                    </button>

                    <!-- Step 12 -->
                    <button type="button" onclick="goToTab(12)" id="step-item-12" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent">
                        <span id="step-circle-12" class="w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0">12</span>
                        <div class="min-w-0">
                            <div id="step-title-12" class="text-xs font-bold text-slate-700 truncate">12. Disbursement</div>
                            <div id="step-sub-12" class="text-[11px] text-slate-400 truncate">Jadwal pencairan</div>
                        </div>
                    </button>
                </div>
            </aside>

            <!-- KONTEN FORMULIR KANAN -->
            <div class="flex-1 w-full min-w-0">
                
                <!-- Hidden step titles for JS compatibility -->
                <div class="hidden">
                    <span id="stepMainTitle"></span>
                    <span id="stepSubTitle"></span>
                </div>

                <form id="camForm" onsubmit="event.preventDefault(); simpanForm(false);">
                    <!-- Hidden Identification Fields -->
                    <input type="hidden" name="kredit_id" id="kredit_id" value="<?= esc($kredit_id ?? '') ?>">
                    <input type="hidden" name="nomor_aplikasi" id="nomor_aplikasi" value="<?= esc($nomor_aplikasi) ?>">

                    <!-- ==========================================
                         TAB 1: APLIKASI & PLAFON PINJAMAN
                    =========================================== -->
                    <div id="tab-1" class="tab-panel active space-y-6">
                        
                        <!-- Card: Data Aplikasi & Pemohon -->
                        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DATA APLIKASI & PEMOHON</h3>
                                        <p class="text-[11px] text-slate-500">Konfigurasi parameter dasar plafon dan skema kredit debitur</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                                    Wajib Diisi
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Tipe Aplikasi -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Tipe Aplikasi
                                    </label>
                                    <select name="tipe_aplikasi" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $ta = $kredit['tipe_aplikasi'] ?? 'Fasilitas Baru'; ?>
                                        <option value="Fasilitas Baru" <?= $ta === 'Fasilitas Baru' ? 'selected' : '' ?>>Fasilitas Baru</option>
                                        <option value="Perpanjangan" <?= $ta === 'Perpanjangan' ? 'selected' : '' ?>>Perpanjangan</option>
                                        <option value="Top Up" <?= $ta === 'Top Up' ? 'selected' : '' ?>>Top Up</option>
                                        <option value="Restrukturisasi" <?= $ta === 'Restrukturisasi' ? 'selected' : '' ?>>Restrukturisasi</option>
                                    </select>
                                </div>

                                <!-- Jenis Debitur -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Jenis Debitur
                                    </label>
                                    <select name="jenis_debitur" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $jd = $kredit['jenis_debitur'] ?? 'Individu'; ?>
                                        <option value="Individu" <?= $jd === 'Individu' ? 'selected' : '' ?>>Individu / Perorangan</option>
                                        <option value="Badan Usaha (PT)" <?= $jd === 'Badan Usaha (PT)' ? 'selected' : '' ?>>Badan Usaha (PT)</option>
                                        <option value="Badan Usaha (CV)" <?= $jd === 'Badan Usaha (CV)' ? 'selected' : '' ?>>Badan Usaha (CV)</option>
                                        <option value="Koperasi / Yayasan" <?= $jd === 'Koperasi / Yayasan' ? 'selected' : '' ?>>Koperasi / Yayasan</option>
                                    </select>
                                </div>

                                <!-- Nama Debitur -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                        Nama Debitur
                                    </label>
                                    <input type="text" name="nama_debitur" id="nama_debitur" 
                                           value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                                           placeholder="Nama lengkap sesuai KTP"
                                           class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Plafond Pengajuan (Rp) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Plafond Pengajuan (Rp)
                                    </label>
                                    <input type="text" name="plafon_pengajuan" id="plafon_pengajuan" 
                                           value="<?= number_format((float)($kredit['plafon_pengajuan'] ?? 200000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-slate-900 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs text-right sm:text-left rupiah-input"
                                           oninput="formatRupiah(this); hitungKalkulasiAngsuran(); hitungCoverageAgunan(); hitungDisbursement();">
                                </div>

                                <!-- Tenor (Bulan) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Tenor (Bulan)
                                    </label>
                                    <select name="tenor_bulan" id="tenor_bulan" onchange="hitungKalkulasiAngsuran();" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $tb = (int)($kredit['tenor_bulan'] ?? 12); ?>
                                        <option value="6" <?= $tb === 6 ? 'selected' : '' ?>>6 Bulan</option>
                                        <option value="12" <?= $tb === 12 ? 'selected' : '' ?>>12 Bulan</option>
                                        <option value="18" <?= $tb === 18 ? 'selected' : '' ?>>18 Bulan</option>
                                        <option value="24" <?= $tb === 24 ? 'selected' : '' ?>>24 Bulan</option>
                                        <option value="36" <?= $tb === 36 ? 'selected' : '' ?>>36 Bulan</option>
                                        <option value="48" <?= $tb === 48 ? 'selected' : '' ?>>48 Bulan</option>
                                        <option value="60" <?= $tb === 60 ? 'selected' : '' ?>>60 Bulan</option>
                                    </select>
                                </div>

                                <!-- Jaminan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Jaminan
                                    </label>
                                    <select name="jenis_jaminan" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $jj = $kredit['jenis_jaminan'] ?? 'On Hand'; ?>
                                        <option value="On Hand" <?= $jj === 'On Hand' ? 'selected' : '' ?>>On Hand (SHM / SHGB / BPKB)</option>
                                        <option value="Take Over" <?= $jj === 'Take Over' ? 'selected' : '' ?>>Take Over Bank Lain</option>
                                        <option value="Pari Passu" <?= $jj === 'Pari Passu' ? 'selected' : '' ?>>Pari Passu</option>
                                        <option value="Tanpa Jaminan" <?= $jj === 'Tanpa Jaminan' ? 'selected' : '' ?>>Tanpa Jaminan</option>
                                    </select>
                                </div>

                                <!-- Tujuan Pembiayaan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Tujuan Pembiayaan
                                    </label>
                                    <select name="tujuan_pembiayaan" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $tp = $kredit['tujuan_pembiayaan'] ?? 'Multiguna'; ?>
                                        <option value="Multiguna" <?= $tp === 'Multiguna' ? 'selected' : '' ?>>Multiguna</option>
                                        <option value="Modal Kerja" <?= $tp === 'Modal Kerja' ? 'selected' : '' ?>>Modal Kerja</option>
                                        <option value="Investasi" <?= $tp === 'Investasi' ? 'selected' : '' ?>>Investasi</option>
                                        <option value="Take Over" <?= $tp === 'Take Over' ? 'selected' : '' ?>>Take Over</option>
                                    </select>
                                </div>

                                <!-- Skema Pembayaran -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Skema Pembayaran
                                    </label>
                                    <select name="skema_pembayaran" id="skema_pembayaran" onchange="hitungKalkulasiAngsuran();" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $sp = $kredit['skema_pembayaran'] ?? 'Balloon Payment'; ?>
                                        <option value="Balloon Payment" <?= $sp === 'Balloon Payment' ? 'selected' : '' ?>>Balloon Payment</option>
                                        <option value="Installment" <?= $sp === 'Installment' ? 'selected' : '' ?>>Installment</option>
                                    </select>
                                </div>

                                <!-- Bunga per Bulan (%) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <span class="text-blue-600 font-bold">%</span>
                                        Bunga per Bulan (%)
                                    </label>
                                    <input type="number" step="0.01" name="bunga_per_bulan" id="bunga_per_bulan" 
                                           value="<?= esc($kredit['bunga_per_bulan'] ?? 3) ?>" 
                                           class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                                           oninput="syncBunga('bulan'); hitungKalkulasiAngsuran();">
                                </div>

                                <!-- Bunga per Tahun (%) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Bunga per Tahun (%)
                                    </label>
                                    <input type="number" step="0.01" name="bunga_per_tahun" id="bunga_per_tahun" 
                                           value="<?= esc($kredit['bunga_per_tahun'] ?? 36.0) ?>" 
                                           class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                                           oninput="syncBunga('tahun'); hitungKalkulasiAngsuran();">
                                </div>
                            </div>
                        </div>

                        <!-- Live Estimasi Box -->
                        <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-xs">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                <!-- Kiri: Judul & Penjelasan -->
                                <div>
                                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                        Kalkulasi Simulasi Angsuran
                                    </span>
                                    <div class="text-xs text-slate-500 mt-1">Dihitung otomatis berdasarkan plafon, tenor, dan suku bunga.</div>
                                </div>

                                <!-- Kanan: Nilai Estimasi Sejajar Bersih -->
                                <div class="flex items-center gap-6 sm:gap-8 self-end lg:self-center shrink-0">
                                    <div class="text-right">
                                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Estimasi Balloon (Bunga/bln)</div>
                                        <div id="boxAngsuranBalloon" class="text-xl sm:text-2xl font-extrabold text-blue-600 mt-0.5 whitespace-nowrap">Rp 0</div>
                                    </div>
                                    <div class="h-10 w-px bg-slate-200"></div>
                                    <div class="text-right">
                                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Estimasi Installment (Pokok+Bunga)</div>
                                        <div id="boxAngsuranInstallment" class="text-xl sm:text-2xl font-extrabold text-emerald-500 mt-0.5 whitespace-nowrap">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


            <!-- ==========================================
                 TAB 2: DATA DEBITUR
            =========================================== -->
            <div id="tab-2" class="tab-panel space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                    
                    <!-- Section Header B -->
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                                2
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">INFORMASI PRIBADI CALON DEBITUR</h3>
                                <p class="text-[11px] text-slate-500">Data identitas nasabah pemohon sesuai KTP dan verifikasi kependudukan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                            Wajib Diisi
                        </span>
                    </div>

                    <!-- Grid Form Fields 2 Kolom Simetris -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Lengkap Sesuai KTP -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Nama Lengkap (Sesuai KTP)
                            </label>
                            <input type="text" name="nama_debitur" id="nama_debitur_tab2" 
                                   value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                                   placeholder="Nama lengkap tanpa gelar singkatan"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                                   oninput="document.getElementById('nama_debitur') ? document.getElementById('nama_debitur').value = this.value : null;">
                        </div>

                        <!-- Tempat, Tanggal Lahir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tempat, Tanggal Lahir
                            </label>
                            <input type="text" name="tempat_tanggal_lahir" 
                                   value="<?= esc($kredit['tempat_tanggal_lahir'] ?? '') ?>" 
                                   placeholder="Contoh: Surabaya, 12 Mei 1985"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Alamat KTP Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat KTP Lengkap
                            </label>
                            <textarea name="alamat_ktp" rows="2" 
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kab, Provinsi"
                                      class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_ktp'] ?? '') ?></textarea>
                        </div>

                        <!-- Alamat Domisili Sekarang -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Alamat Domisili Sekarang
                            </label>
                            <textarea name="alamat_domisili" rows="2" 
                                      placeholder="Kosongkan jika sama dengan alamat KTP"
                                      class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_domisili'] ?? '') ?></textarea>
                        </div>

                        <!-- Kepemilikan Rumah Domisili -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Kepemilikan Rumah Domisili
                            </label>
                            <select name="kepemilikan_domisili" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                <?php $kd = $kredit['kepemilikan_domisili'] ?? 'Milik Sendiri'; ?>
                                <option value="Milik Sendiri" <?= $kd === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                <option value="Milik Keluarga / Orang Tua" <?= $kd === 'Milik Keluarga / Orang Tua' ? 'selected' : '' ?>>Milik Keluarga / Orang Tua</option>
                                <option value="Sewa / Kontrak" <?= $kd === 'Sewa / Kontrak' ? 'selected' : '' ?>>Sewa / Kontrak</option>
                                <option value="Rumah Dinas" <?= $kd === 'Rumah Dinas' ? 'selected' : '' ?>>Rumah Dinas</option>
                            </select>
                        </div>

                        <!-- Nomor HP / WhatsApp Aktif -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Nomor HP / WhatsApp Aktif
                            </label>
                            <input type="text" name="nomor_hp" 
                                   value="<?= esc($kredit['nomor_hp'] ?? '') ?>" 
                                   placeholder="081234567xxx"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nomor Emergency / Kontak Darurat -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Nomor Emergency / Kontak Darurat
                            </label>
                            <input type="text" name="nomor_emergency" 
                                   value="<?= esc($kredit['nomor_emergency'] ?? '') ?>" 
                                   placeholder="Nomor kerabat yang dapat dihubungi"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- NPWP Pribadi -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                NPWP Pribadi
                            </label>
                            <input type="text" name="npwp_pribadi" 
                                   value="<?= esc($kredit['npwp_pribadi'] ?? '') ?>" 
                                   placeholder="16 digit NPWP"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nama Gadis Ibu Kandung -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Nama Gadis Ibu Kandung
                            </label>
                            <input type="text" name="nama_ibu_kandung" 
                                   value="<?= esc($kredit['nama_ibu_kandung'] ?? '') ?>" 
                                   placeholder="Untuk verifikasi data perbankan"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 3: PROFIL USAHA
            =========================================== -->
            <div id="tab-3" class="tab-panel space-y-6">
                <!-- Card Utama: Profil Kegiatan Usaha -->
                <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                    
                    <!-- Section Header C -->
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                                3
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">PROFIL KEGIATAN USAHA & OPERASIONAL</h3>
                                <p class="text-[11px] text-slate-500">Profil operasional unit usaha penghasil arus kas pembayaran pinjaman</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                            Wajib Diisi
                        </span>
                    </div>

                    <!-- Grid Form 2 Kolom Seimbang -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Usaha / Merk Dagang -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Nama Usaha / Merk Dagang
                            </label>
                            <input type="text" name="nama_usaha" 
                                   value="<?= esc($kredit['nama_usaha'] ?? '') ?>" 
                                   placeholder="Contoh: Toko Berkah / PT Mandiri Sejahtera"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Jabatan Pemohon di Usaha -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Jabatan Pemohon di Usaha
                            </label>
                            <input type="text" name="jabatan" 
                                   value="<?= esc($kredit['jabatan'] ?? '') ?>" 
                                   placeholder="Pemilik / Direktur / General Manager"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Lama Usaha Berjalan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Lama Usaha Berjalan
                            </label>
                            <input type="text" name="lama_usaha" 
                                   value="<?= esc($kredit['lama_usaha'] ?? '') ?>" 
                                   placeholder="Contoh: 5 Tahun"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Sumber Utama Pembayaran (Repayment) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Sumber Utama Pembayaran (Repayment)
                            </label>
                            <input type="text" name="sumber_pembayaran" 
                                   value="<?= esc($kredit['sumber_pembayaran'] ?? '') ?>" 
                                   placeholder="Hasil penjualan harian / invoice piutang"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Alamat Lokasi Tempat Usaha -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat Lokasi Tempat Usaha
                            </label>
                            <textarea name="alamat_usaha" rows="2" 
                                      placeholder="Alamat fisik workshop/toko/pabrik yang beroperasi"
                                      class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_usaha'] ?? '') ?></textarea>
                        </div>

                        <!-- NPWP Usaha / Badan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                NPWP Usaha / Badan
                            </label>
                            <input type="text" name="npwp_usaha" 
                                   value="<?= esc($kredit['npwp_usaha'] ?? '') ?>" 
                                   placeholder="Nomor Pokok Wajib Pajak Usaha"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nomor Induk Berusaha (NIB) / Izin -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Nomor Induk Berusaha (NIB) / Izin
                            </label>
                            <input type="text" name="nomor_induk_berusaha_nib" 
                                   value="<?= esc($kredit['nomor_induk_berusaha_nib'] ?? '') ?>" 
                                   placeholder="NIB OSS RBA"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>
                </div>

                <!-- Card Sub: Legalitas Akta & Kemenkumham (Khusus PT / CV) -->
                <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">LEGALITAS AKTA & KEMENKUMHAM (UNTUK PT / CV)</h4>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold text-slate-400 bg-slate-50 border border-slate-200">
                            Opsional
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nomor Akta Pendirian -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Nomor Akta Pendirian
                            </label>
                            <input type="text" name="akta_pendirian" 
                                   value="<?= esc($kredit['akta_pendirian'] ?? '') ?>" 
                                   placeholder="No. Akta Notaris Pendirian"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- SK Pengesahan Kemenkumham Pendirian -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                SK Pengesahan Kemenkumham Pendirian
                            </label>
                            <input type="text" name="sk_pendirian" 
                                   value="<?= esc($kredit['sk_pendirian'] ?? '') ?>" 
                                   placeholder="No. AHU-xxxx.AH.01.01"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nomor Akta Perubahan Terakhir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Nomor Akta Perubahan Terakhir
                            </label>
                            <input type="text" name="akta_perubahan" 
                                   value="<?= esc($kredit['akta_perubahan'] ?? '') ?>" 
                                   placeholder="No. Akta Perubahan Pengurus/Modal"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- SK Penerimaan Kemenkumham Perubahan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                SK Penerimaan Kemenkumham Perubahan
                            </label>
                            <input type="text" name="sk_perubahan" 
                                   value="<?= esc($kredit['sk_perubahan'] ?? '') ?>" 
                                   placeholder="No. AHU Perubahan Terakhir"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 4: AGUNAN & JAMINAN
            =========================================== -->
            <div id="tab-4" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            4
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">PENILAIAN AGUNAN & AGUNAN COVERAGE</h3>
                            <p class="text-[11px] text-slate-500">Detail aset jaminan, nilai pasar (MV), likuidasi (LV), dan rasio penutupan terhadap plafon</p>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBarisAgunan()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition border border-blue-200/60 shadow-2xs">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Agunan
                    </button>
                </div>

                <div class="space-y-5" id="containerAgunan">
                    <?php if (empty($agunan)): ?>
                        <!-- Default 1 Baris Agunan -->
                        <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                            <!-- Item Top Bar -->
                            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </span>
                                    <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num">1</span></span>
                                </div>
                                <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Alamat Lengkap Objek Jaminan
                                    </label>
                                    <input type="text" name="agunan[0][alamat_agunan]" placeholder="Alamat lokasi aset jaminan"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Jenis Dokumen Agunan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Jenis Dokumen Agunan
                                    </label>
                                    <select name="agunan[0][jenis_agunan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                        <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                        <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                        <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                        <option value="Bilyet Deposito">Bilyet Deposito</option>
                                    </select>
                                </div>

                                <!-- Nomor Sertifikat / BPKB -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                        Nomor Sertifikat / BPKB
                                    </label>
                                    <input type="text" name="agunan[0][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Nama Pemilik Agunan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Nama Pemilik Agunan
                                    </label>
                                    <input type="text" name="agunan[0][pemilik_agunan]" placeholder="Sesuai nama di sertifikat"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Hubungan Kepemilikan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Hubungan Kepemilikan
                                    </label>
                                    <select name="agunan[0][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="Milik Sendiri">Milik Sendiri</option>
                                        <option value="Suami / Istri">Suami / Istri</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Pihak Ketiga">Pihak Ketiga</option>
                                    </select>
                                </div>

                                <!-- Status IMB / PBG -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Status IMB / PBG
                                    </label>
                                    <select name="agunan[0][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Proses Pengurusan">Proses Pengurusan</option>
                                    </select>
                                </div>

                                <!-- Lama Kepemilikan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Lama Kepemilikan
                                    </label>
                                    <input type="text" name="agunan[0][lama_kepemilikan]" placeholder="Contoh: 8 Tahun"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Luas Tanah (m²) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                        Luas Tanah (m²)
                                    </label>
                                    <input type="number" name="agunan[0][luas_tanah_m2]" placeholder="Contoh: 120"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Luas Bangunan (m²) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Luas Bangunan (m²)
                                    </label>
                                    <input type="number" name="agunan[0][luas_bangunan_m2]" placeholder="Contoh: 85"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Nilai Pasar / MV (Rp) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Nilai Pasar / MV (Rp)
                                    </label>
                                    <input type="text" name="agunan[0][nilai_pasar_mv]" placeholder="0"
                                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv"
                                           oninput="formatRupiah(this); hitungCoverageAgunan();">
                                </div>

                                <!-- Likuidasi (% & Nominal Nilai LV) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Likuidasi (% / Nilai LV)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative w-16 shrink-0">
                                            <input type="number" step="1" name="agunan[0][persen_likuidasi]" value="60"
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct"
                                                   oninput="hitungCoverageAgunan();">
                                        </div>
                                        <span class="text-xs text-slate-400 font-bold">%</span>
                                        <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                            <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($agunan as $idx => $row): ?>
                            <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                                <!-- Item Top Bar -->
                                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </span>
                                        <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num"><?= $idx + 1 ?></span></span>
                                    </div>
                                    <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Alamat Lengkap Objek Jaminan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][alamat_agunan]" value="<?= esc($row['alamat_agunan'] ?? '') ?>" placeholder="Alamat lokasi aset jaminan"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Jenis Dokumen Agunan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Jenis Dokumen Agunan
                                        </label>
                                        <select name="agunan[<?= $idx ?>][jenis_agunan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                            <?php $ja = $row['jenis_agunan'] ?? 'SHM'; ?>
                                            <option value="SHM" <?= $ja === 'SHM' ? 'selected' : '' ?>>SHM (Sertifikat Hak Milik)</option>
                                            <option value="SHGB" <?= $ja === 'SHGB' ? 'selected' : '' ?>>SHGB (Hak Guna Bangunan)</option>
                                            <option value="SHM Sarusun" <?= $ja === 'SHM Sarusun' ? 'selected' : '' ?>>SHM Sarusun (Apartemen)</option>
                                            <option value="BPKB Mobil" <?= $ja === 'BPKB Mobil' ? 'selected' : '' ?>>BPKB Kendaraan Roda 4</option>
                                            <option value="Bilyet Deposito" <?= $ja === 'Bilyet Deposito' ? 'selected' : '' ?>>Bilyet Deposito</option>
                                        </select>
                                    </div>

                                    <!-- Nomor Sertifikat / BPKB -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                            Nomor Sertifikat / BPKB
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][nomor_sertifikat]" value="<?= esc($row['nomor_sertifikat'] ?? '') ?>" placeholder="No. Sertifikat / BPKB"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Nama Pemilik Agunan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Nama Pemilik Agunan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][pemilik_agunan]" value="<?= esc($row['pemilik_agunan'] ?? '') ?>" placeholder="Sesuai nama di sertifikat"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Hubungan Kepemilikan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Hubungan Kepemilikan
                                    </label>
                                        <select name="agunan[<?= $idx ?>][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                            <?php $hk = $row['hubungan_kepemilikan'] ?? 'Milik Sendiri'; ?>
                                            <option value="Milik Sendiri" <?= $hk === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                            <option value="Suami / Istri" <?= $hk === 'Suami / Istri' ? 'selected' : '' ?>>Suami / Istri</option>
                                            <option value="Orang Tua" <?= $hk === 'Orang Tua' ? 'selected' : '' ?>>Orang Tua</option>
                                            <option value="Pihak Ketiga" <?= $hk === 'Pihak Ketiga' ? 'selected' : '' ?>>Pihak Ketiga</option>
                                        </select>
                                    </div>

                                    <!-- Status IMB / PBG -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Status IMB / PBG
                                        </label>
                                        <select name="agunan[<?= $idx ?>][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                            <?php $si = $row['status_imb'] ?? 'Ada'; ?>
                                            <option value="Ada" <?= $si === 'Ada' ? 'selected' : '' ?>>Ada</option>
                                            <option value="Tidak Ada" <?= $si === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                                            <option value="Proses Pengurusan" <?= $si === 'Proses Pengurusan' ? 'selected' : '' ?>>Proses Pengurusan</option>
                                        </select>
                                    </div>

                                    <!-- Lama Kepemilikan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Lama Kepemilikan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][lama_kepemilikan]" value="<?= esc($row['lama_kepemilikan'] ?? '') ?>" placeholder="Contoh: 8 Tahun"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Luas Tanah (m²) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            Luas Tanah (m²)
                                        </label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_tanah_m2]" value="<?= esc($row['luas_tanah_m2'] ?? '') ?>" placeholder="Contoh: 120"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Luas Bangunan (m²) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            Luas Bangunan (m²)
                                        </label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_bangunan_m2]" value="<?= esc($row['luas_bangunan_m2'] ?? '') ?>" placeholder="Contoh: 85"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Nilai Pasar / MV (Rp) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Nilai Pasar / MV (Rp)
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][nilai_pasar_mv]" 
                                               value="<?= number_format((float)($row['nilai_pasar_mv'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv" 
                                               oninput="formatRupiah(this); hitungCoverageAgunan();">
                                    </div>

                                    <!-- Likuidasi (% & Nominal Nilai LV) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                            Likuidasi (% / Nilai LV)
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <div class="relative w-16 shrink-0">
                                                <input type="number" step="1" name="agunan[<?= $idx ?>][persen_likuidasi]" 
                                                       value="<?= esc($row['persen_likuidasi'] ?? 60) ?>" 
                                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct" 
                                                       oninput="hitungCoverageAgunan();">
                                            </div>
                                            <span class="text-xs text-slate-400 font-bold">%</span>
                                            <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                                <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">
                                                    Rp <?= number_format((float)($row['nilai_likuidasi_lv'] ?? 0), 0, ',', '.') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Ringkasan Evaluasi Agunan -->
                <div class="mt-6 p-4 sm:p-5 bg-blue-50/60 border border-blue-200/60 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Evaluasi Agunan</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-left sm:text-right w-full md:w-auto">
                        <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Nilai Pasar (MV)</div>
                            <div id="totalAgunanMV" class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">Rp 0</div>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Likuidasi (LV)</div>
                            <div id="totalAgunanLV" class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">Rp 0</div>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Coverage MV</div>
                            <div id="coverageMV" class="text-xs sm:text-sm font-extrabold text-blue-600 mt-0.5">0.0%</div>
                        </div>
                        <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Coverage LV</div>
                            <div id="coverageLV" class="text-xs sm:text-sm font-extrabold text-blue-700 mt-0.5">0.0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 5: UPLOAD DOKUMEN PERSYARATAN
            =========================================== -->
            <div id="tab-5" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            5
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DOKUMEN PERSYARATAN & BERKAS LEGALITAS</h3>
                            <p class="text-[11px] text-slate-500">Unggah berkas dokumen fisik pendukung pengajuan analisa kredit pemohon</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                        Format: PDF, JPG, PNG (Maks 10MB)
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Panel Kiri: Form Unggah Dokumen -->
                    <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 pb-3.5 mb-4 border-b border-slate-200/60">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-700 tracking-wide">Unggah Berkas Baru</span>
                            </div>

                            <!-- Pilih Kategori Dokumen -->
                            <div class="mb-4">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    Kategori Dokumen
                                </label>
                                <select id="kategoriUpload" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                    <option value="KTP & NPWP">KTP & NPWP Debitur / Pasangan</option>
                                    <option value="Sertifikat Agunan">Sertifikat Agunan / PBB / IMB</option>
                                    <option value="Rekening Koran">Rekening Koran 3-6 Bulan Terakhir</option>
                                    <option value="Legalitas Usaha">NIB / SIUP / TDP / Akta Perusahaan</option>
                                    <option value="Laporan Keuangan">Laporan Keuangan / Pembukuan Usaha</option>
                                    <option value="Foto Usaha & Jaminan">Foto Lokasi Usaha & Agunan</option>
                                    <option value="Lainnya">Dokumen Pendukung Lainnya</option>
                                </select>
                            </div>

                            <!-- Area Dropzone / Picker Ringkas -->
                            <div onclick="document.getElementById('fileUploadInput').click()" 
                                 class="border border-dashed border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50/30 rounded-xl p-5 text-center cursor-pointer transition group">
                                <input type="file" id="fileUploadInput" class="hidden" onchange="prosesUploadDokumen(this)">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2.5 group-hover:scale-105 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <div class="text-xs font-bold text-slate-700">Pilih Berkas dari Komputer</div>
                                <div class="text-[11px] text-slate-400 mt-1">PDF, JPG, atau PNG (Maks. 10MB)</div>
                            </div>
                        </div>

                        <button type="button" onclick="document.getElementById('fileUploadInput').click()" 
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Pilih & Unggah File
                        </button>
                    </div>

                    <!-- Panel Kanan: Daftar Dokumen Terunggah -->
                    <div class="lg:col-span-2 bg-slate-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col">
                        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-700 tracking-wide">Daftar Dokumen yang Telah Diunggah</span>
                            </div>
                            <span id="docCountBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                <?= count($dokumen ?? []) ?> Dokumen
                            </span>
                        </div>

                        <div id="listDokumenTerunggah" class="space-y-2.5 flex-1 flex flex-col justify-center">
                            <?php if (empty($dokumen)): ?>
                                <div id="emptyDokumenState" class="py-12 px-4 border border-slate-200/80 rounded-xl bg-white text-center">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada berkas yang diunggah</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih kategori dan berkas di sebelah kiri untuk mengunggah dokumen pendukung.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($dokumen as $doc): ?>
                                    <div class="p-3.5 bg-white border border-slate-200/90 rounded-xl flex items-center justify-between text-xs hover:border-blue-300 transition shadow-2xs">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-100">
                                                <?= strtoupper(pathinfo($doc['nama_file_asli'] ?? '', PATHINFO_EXTENSION) ?: 'DOC') ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800"><?= esc($doc['nama_file_asli']) ?></div>
                                                <div class="text-[11px] text-slate-400"><?= esc($doc['kategori_dokumen']) ?> &bull; <?= number_format($doc['ukuran_file_bytes'] / 1024, 1) ?> KB</div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url($doc['path_file']) ?>" target="_blank" class="px-3 py-1.5 text-blue-600 hover:text-blue-800 font-semibold text-xs border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                                            Lihat File
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 6: FASILITAS SLIK OJK
            =========================================== -->
            <?php
                $ijinSlikPengusul = (int)($pengaturan_sistem['ijin_pengusul_isi_slik'] ?? 1);
                $currentUserRole = strtolower(session()->get('jabatan') ?? '');
                $isPetugasSlikRole = ($currentUserRole === 'superadmin' || strpos($currentUserRole, 'slik') !== false);
                $bisaIsiSlik = ($isPetugasSlikRole || $ijinSlikPengusul === 1);
            ?>
            <div id="tab-6" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            6
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">RIWAYAT FASILITAS KREDIT (SLIK OJK)</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan fasilitas pinjaman aktif di perbankan atau lembaga keuangan lain</p>
                        </div>
                    </div>
                    <?php if ($bisaIsiSlik): ?>
                        <button type="button" onclick="tambahBarisSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition border border-blue-200/60 shadow-2xs">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Fasilitas
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (!$isPetugasSlikRole && $ijinSlikPengusul === 1): ?>
                    <!-- Alert Fleksibel: Pengusul Diizinkan Mengisi Draf -->
                    <div class="mb-6 p-4 rounded-2xl bg-blue-50/80 border border-blue-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Pengisian Data SLIK Fleksibel (Opsional bagi Pengusul)</h4>
                            <p class="text-xs text-blue-800 mt-0.5 leading-relaxed">
                                Anda dapat mencantumkan fasilitas pinjaman aktif nasabah yang Anda ketahui. Data ini akan tercatat sebagai <strong>usulan draf</strong> kepada <strong>Petugas SLIK</strong> untuk ditinjau dan disahkan melalui tombol persetujuan di workspace SLIK. Anda juga dapat melewati langkah ini bila pengecekan diserahkan penuh kepada Petugas SLIK.
                            </p>
                        </div>
                    </div>
                <?php elseif (!$isPetugasSlikRole && $ijinSlikPengusul === 0): ?>
                    <!-- Alert Pengisian SLIK Dinonaktifkan bagi Pengusul -->
                    <div class="p-6 sm:p-7 rounded-2xl bg-amber-50 border border-amber-200 mb-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h4 class="text-sm font-extrabold text-amber-900 uppercase tracking-wide">Pengisian SLIK Dikelola Oleh Petugas SLIK</h4>
                        <p class="text-xs text-amber-800 mt-1.5 max-w-lg mx-auto leading-relaxed">
                            Akses pengisian riwayat kredit nasabah saat ini dikhususkan bagi <strong>Petugas Verifikasi SLIK OJK</strong>. Anda dapat langsung melanjutkan ke tahap pengisian berikutnya.
                        </p>
                        <div class="mt-4 flex items-center justify-center gap-3">
                            <button type="button" onclick="goToTab(7)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                <span>Lanjut ke Langkah 7 (Legal & Web Check)</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                    <table class="w-full text-left border-collapse" id="tabelSlik">
                        <thead class="bg-slate-50/80 text-slate-700 font-bold text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-3 min-w-[170px]">Nama Debitur / Pemilik</th>
                                <th class="py-3.5 px-3 min-w-[150px]">Bank / Lembaga</th>
                                <th class="py-3.5 px-3 min-w-[130px]">Plafon (Rp)</th>
                                <th class="py-3.5 px-3 min-w-[140px]">Baki Debet / Outstanding</th>
                                <th class="py-3.5 px-3 min-w-[160px]">Kualitas / Kol</th>
                                <th class="py-3.5 px-3 min-w-[170px]">Keterangan / Tujuan</th>
                                <th class="py-3.5 px-2 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs" id="bodySlik">
                            <?php if (empty($slik)): ?>
                                <?php if (!$bisaIsiSlik): ?>
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                            Belum ada catatan fasilitas kredit yang diterbitkan oleh Petugas SLIK.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][nama_debitur]" placeholder="Debitur / Pasangan" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][plafon]" placeholder="0" 
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                                                   oninput="formatRupiah(this)">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][outstanding]" placeholder="0" 
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                                   oninput="formatRupiah(this); hitungTotalSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <select name="slik[0][kelancaran]" 
                                                    class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                                <option value="Lancar">Kol 1 (Lancar)</option>
                                                <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                                                <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                                                <option value="Diragukan">Kol 4 (Diragukan)</option>
                                                <option value="Macet">Kol 5 (Macet)</option>
                                            </select>
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white text-center">
                                            <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100" title="Hapus Baris">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php foreach ($slik as $idx => $s): ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][nama_debitur]" value="<?= esc($s['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][bank_lembaga]" value="<?= esc($s['bank_lembaga'] ?? '') ?>" placeholder="Nama Bank / Leasing" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][plafon]" value="<?= number_format((float)($s['plafon'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>" 
                                                   oninput="formatRupiah(this)">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][outstanding]" value="<?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>" 
                                                   oninput="formatRupiah(this); hitungTotalSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <?php if (!$bisaIsiSlik): ?>
                                                <input type="hidden" name="slik[<?= $idx ?>][kelancaran]" value="<?= esc($s['kelancaran'] ?? 'Lancar') ?>">
                                                <input type="text" readonly value="<?= esc($s['kelancaran'] ?? 'Lancar') ?>" 
                                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-100 cursor-not-allowed shadow-2xs">
                                            <?php else: ?>
                                                <select name="slik[<?= $idx ?>][kelancaran]" 
                                                        class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                                    <?php $kl = $s['kelancaran'] ?? 'Lancar'; ?>
                                                    <option value="Lancar" <?= $kl === 'Lancar' ? 'selected' : '' ?>>Kol 1 (Lancar)</option>
                                                    <option value="DPK" <?= $kl === 'DPK' ? 'selected' : '' ?>>Kol 2 (DPK / Perhatian Khusus)</option>
                                                    <option value="Kurang Lancar" <?= $kl === 'Kurang Lancar' ? 'selected' : '' ?>>Kol 3 (Kurang Lancar)</option>
                                                    <option value="Diragukan" <?= $kl === 'Diragukan' ? 'selected' : '' ?>>Kol 4 (Diragukan)</option>
                                                    <option value="Macet" <?= $kl === 'Macet' ? 'selected' : '' ?>>Kol 5 (Macet)</option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][keterangan]" value="<?= esc($s['keterangan'] ?? '') ?>" placeholder="Modal Kerja / KPR / dll" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white text-center">
                                            <?php if ($bisaIsiSlik): ?>
                                                <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            <?php else: ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-400 text-xs">
                                                    🔒
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Total Baki Debet Summary Box -->
                <div class="mt-4 p-4 bg-blue-50/60 border border-blue-200/60 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Baki Debet Terhutang (SLIK)</span>
                    </div>
                    <div class="text-sm sm:text-base font-extrabold text-blue-700" id="totalOutstandingSlik">
                        Rp 0
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ringkasan & Catatan Historis SLIK OJK
                    </label>
                    <textarea name="ringkasan_slik" rows="3" 
                              <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                              placeholder="Tuliskan kesimpulan profil karakter kredit nasabah, riwayat tunggakan (jika ada), serta rencana take over / pelunasan..."
                              class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs resize-y <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>"><?= esc($kredit['ringkasan_slik'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- ==========================================
                 TAB 7: WEB & LEGAL CHECK
            =========================================== -->
            <div id="tab-7" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            7
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">VERIFIKASI LATAR BELAKANG & LEGAL CHECK</h3>
                            <p class="text-[11px] text-slate-500">Pemeriksaan profil reputasi debitur pada kanal publik, peradilan, dan daftar hitam</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                        Pemeriksaan Legalitas
                    </span>
                </div>

                <div class="space-y-4">
                    <!-- SIPP -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">1. Penelusuran Perkara Pengadilan (SIPP)</span>
                            <span class="text-[11px] text-slate-500">PN / PA / PTUN / Niaga</span>
                        </div>
                        <input type="text" name="sipp_keterangan" value="<?= esc($webcheck['sipp_keterangan'] ?? 'Tidak ditemukan perkara perdata, pidana, atau kepailitan aktif atas nama calon debitur.') ?>" 
                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                    </div>

                    <!-- Internet / Media -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">2. Pencarian Media Online & Jejak Digital</span>
                            <span class="text-[11px] text-slate-500">Google / Berita Online</span>
                        </div>
                        <input type="text" name="internet_keterangan" value="<?= esc($webcheck['internet_keterangan'] ?? 'Hasil penelusuran nama debitur dan usaha bersih dari pemberitaan negatif.') ?>" 
                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                    </div>

                    <!-- DTTOT / Watchlist -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">3. Daftar Terduga Teroris & DTTOT PPATK</span>
                            <span class="text-[11px] text-slate-500">Kepatuhan APU-PPT</span>
                        </div>
                        <input type="text" name="watchlist_keterangan" value="<?= esc($webcheck['watchlist_keterangan'] ?? 'Nama debitur TIDAK tercantum dalam daftar DTTOT / Proliferasi Senjata PPATK.') ?>" 
                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                    </div>

                    <!-- Lelang KPKNL -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">4. Status Objek Lelang KPKNL</span>
                            <span class="text-[11px] text-slate-500">lelang.go.id</span>
                        </div>
                        <input type="text" name="lelang_keterangan" value="<?= esc($webcheck['lelang_keterangan'] ?? 'Objek agunan tidak sedang dalam proses eksekusi atau pengumuman lelang KPKNL.') ?>" 
                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                    </div>

                    <!-- Lingkungan & Karakter -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-800">5. Kroscek Lingkungan Sekitar & Reputasi</span>
                            <span class="text-[11px] text-slate-500">Lingkungan Domisili & Usaha</span>
                        </div>
                        <input type="text" name="negatif_keterangan" value="<?= esc($webcheck['negatif_keterangan'] ?? 'Informasi dari lingkungan sekitar menunjukkan reputasi yang baik dan tidak ada konflik.') ?>" 
                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 8: ANALISA USAHA & REKOMENDASI AI
            =========================================== -->
            <div id="tab-8" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            8
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA BISNIS & REKOMENDASI KREDIT</h3>
                            <p class="text-[11px] text-slate-500">Evaluasi kualitatif aspek 5C dan rekomendasi keputusan kredit serta limit plafon</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs">
                        Aspek 5C & Scoring
                    </span>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Analisa Kualitas & Marketability Jaminan</label>
                        <textarea name="analisa_jaminan" rows="3" placeholder="Lokasi strategis, akses jalan, kondisi fisik bangunan, dan kemudahan penjualan kembali..."
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisa_jaminan'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Business Review & Prospek Pasar Usaha</label>
                        <textarea name="business_review" rows="3" placeholder="Model bisnis, segmen pelanggan, daya saing kompetitor, stabilitas pasokan bahan baku..."
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_review'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Kunjungan / On The Spot (OTS)</label>
                            <textarea name="business_catatan" rows="3" placeholder="Hasil observasi fisik di lokasi usaha nasabah..."
                                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_catatan'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rincian Kebutuhan Modal Kerja</label>
                            <textarea name="detail_kebutuhan" rows="3" placeholder="Alokasi dana pinjaman yang diajukan..."
                                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['detail_kebutuhan'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Rekomendasi Keputusan & Plafon Kredit -->
                    <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/50 border border-blue-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-blue-100/90 pb-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 tracking-tight">Rekomendasi Keputusan Kredit</h4>
                                    <p class="text-xs text-slate-500">Hasil penilaian scoring kelayakan 5C dan rekomendasi limit pembiayaan.</p>
                                </div>
                            </div>
                            <button type="button" onclick="jalankanAnalisaAI()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-2 cursor-pointer whitespace-nowrap self-start sm:self-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Analisis Rekomendasi
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Skor Kelayakan (0 - 100)</label>
                                <input type="number" name="skor_kelayakan_ai" id="skor_kelayakan_ai" 
                                       value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>" min="0" max="100"
                                       class="w-full text-lg font-extrabold text-center text-blue-700 bg-blue-50/50 border border-blue-200 rounded-xl py-2 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Rekomendasi Keputusan</label>
                                <?php $rk = $analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh'; ?>
                                <select name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" class="w-full text-xs font-bold text-slate-800 bg-blue-50/50 border border-blue-200 rounded-xl py-2.5 px-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                    <option value="Disetujui Penuh" <?= $rk === 'Disetujui Penuh' ? 'selected' : '' ?>>Disetujui Penuh</option>
                                    <option value="Disetujui dengan Penurunan Plafon" <?= $rk === 'Disetujui dengan Penurunan Plafon' ? 'selected' : '' ?>>Disetujui dengan Penurunan Plafon</option>
                                    <option value="Pertimbangan Khusus" <?= $rk === 'Pertimbangan Khusus' ? 'selected' : '' ?>>Pertimbangan Khusus / Tambah Jaminan</option>
                                    <option value="Tolak" <?= $rk === 'Tolak' ? 'selected' : '' ?>>Tolak / Berisiko Tinggi</option>
                                </select>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Rekomendasi Plafon (Rp)</label>
                                <input type="text" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" 
                                       value="<?= number_format((float)($analisa['rekomendasi_plafon_ai'] ?? 500000000), 0, ',', '.') ?>" 
                                       class="w-full text-sm font-bold text-slate-900 bg-blue-50/50 border border-blue-200 rounded-xl py-2 px-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" oninput="formatRupiah(this)">
                            </div>
                            <div class="md:col-span-3 bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Analisis Risiko & Mitigasi</label>
                                <textarea name="analisis_risiko_ai" id="analisis_risiko_ai" rows="3" class="w-full text-xs font-medium text-slate-800 bg-blue-50/30 border border-blue-200 rounded-xl p-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisis_risiko_ai'] ?? 'Profil usaha menunjukkan perputaran kas positif dengan rasio DSC di atas batas aman. Agunan memiliki nilai pasar dan legalitas kuat yang mampu memitigasi risiko gagal bayar.') ?></textarea>
                            </div>
                        </div>

                        <!-- Banner Laporan PDF Siap Dicetak -->
                        <div id="aiPdfBanner" class="mt-5 p-4 bg-white border border-blue-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-sm shrink-0">
                                    &check;
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800" id="aiSumberLabel">Model Evaluasi Kredit Aktif</div>
                                    <div class="text-[11px] text-slate-500">Hasil analisa komprehensif dapat langsung dicetak sebagai Laporan Memo Kredit resmi (PDF).</div>
                                </div>
                            </div>
                            <button type="button" onclick="bukaLaporanPDF()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Cetak Laporan CAM (PDF) &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 9: REKENING KORAN BANK
            =========================================== -->
            <div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            9
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REKAPITULASI MUTASI REKENING KORAN</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan arus kas masuk (kredit) dan keluar (debit) untuk validasi omzet usaha riil</p>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBarisRekening()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Rekening Bank
                    </button>
                </div>

                <div class="space-y-4" id="containerRekening">
                    <?php if (empty($rekening)): ?>
                        <!-- Default 1 Baris Rekening -->
                        <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                                </div>
                                <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>
                            <!-- Baris 1: Identitas Rekening -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                                    <input type="text" name="rekening[0][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                                    <input type="text" name="rekening[0][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                                    <input type="text" name="rekening[0][nomor_rekening]" placeholder="Nomor rekening bank" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                                    <input type="text" name="rekening[0][atas_nama]" placeholder="Nama pemilik rekening" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                            </div>
                            <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                                    <input type="text" name="rekening[0][total_mutasi_kredit]" placeholder="0" 
                                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                           oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                                    <input type="text" name="rekening[0][total_mutasi_debit]" placeholder="0" 
                                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                           oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                                    <input type="number" name="rekening[0][jumlah_bulan]" value="6" min="1" max="24" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                           oninput="hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                                    <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                        Rp 0
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($rekening as $idx => $r): ?>
                            <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                                    </div>
                                    <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                                <!-- Baris 1: Identitas Rekening -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nama_bank]" value="<?= esc($r['nama_bank'] ?? '') ?>" placeholder="Contoh: BCA / Mandiri / BRI" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                                        <input type="text" name="rekening[<?= $idx ?>][periode]" value="<?= esc($r['periode'] ?? '') ?>" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nomor_rekening]" value="<?= esc($r['nomor_rekening'] ?? '') ?>" placeholder="Nomor rekening bank" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][atas_nama]" value="<?= esc($r['atas_nama'] ?? '') ?>" placeholder="Nama pemilik rekening" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                </div>
                                <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_kredit]" 
                                               value="<?= number_format((float)($r['total_mutasi_kredit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_debit]" 
                                               value="<?= number_format((float)($r['total_mutasi_debit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                                        <input type="number" name="rekening[<?= $idx ?>][jumlah_bulan]" 
                                               value="<?= esc($r['jumlah_bulan'] ?? 6) ?>" min="1" max="24" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                               oninput="hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                                        <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                            Rp <?= number_format((float)($r['rata_rata_kredit'] ?? 0), 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Rekap Mutasi Seluruh Bank -->
                <div class="mt-6 p-5 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/60 border border-blue-200/90 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">Total Omzet Arus Kas Mutasi Kredit</div>
                            <div class="text-xs text-slate-500">Akumulasi rata-rata arus kas masuk per bulan dari seluruh rekening bank terdaftar.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-[11px] text-slate-400 font-semibold uppercase">Rata-rata Gabungan / Bulan</div>
                            <div id="totalAvgKreditGabungan" class="text-lg font-extrabold text-blue-700">Rp 0</div>
                        </div>
                        <button type="button" onclick="salinOmzetKeLabaRugi()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            Gunakan di Laba Rugi &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 10: ANALISA LABA RUGI
            =========================================== -->
            <div id="tab-10" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            10
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA LABA RUGI KOMPARATIF</h3>
                            <p class="text-[11px] text-slate-500">Verifikasi dual track profitabilitas debitur berdasarkan bukti rekening koran bank vs hasil wawancara</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                        Dual Track Verification
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Stream 1: Berdasarkan Rekening Koran -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Jalur Rekening Koran</h3>
                                        <span class="text-[11px] text-slate-400 font-medium">Validasi bukti mutasi bank</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-100/70 border border-blue-200">
                                    Data Riil Bank
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_rk" id="pendapatan_rk" 
                                           value="<?= number_format((float)($keuangan['pendapatan_rk'] ?? 150000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_rk" id="hpp_persen_rk" 
                                               value="<?= esc($keuangan['hpp_persen_rk'] ?? 60) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomHppRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                                    <span class="font-bold text-blue-900">Laba Kotor (Gross Profit):</span>
                                    <span id="grossRK" class="text-sm font-black text-blue-700">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Biaya Hidup</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBiayaHidupRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Beban Lain</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBebanLainRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Laba Bersih Operasional</div>
                                    <div class="text-xs font-extrabold text-white truncate">Rekening Koran</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span id="nettRK" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stream 2: Berdasarkan Hasil Wawancara (Warna Biru Senada) -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                        2
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Jalur Hasil Wawancara</h3>
                                        <span class="text-[11px] text-slate-400 font-medium">Klaim usaha lapangan / OTS</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-100/70 border border-blue-200">
                                    Data Wawancara
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_iv" id="pendapatan_iv" 
                                           value="<?= number_format((float)($keuangan['pendapatan_iv'] ?? 160000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_iv" id="hpp_persen_iv" 
                                               value="<?= esc($keuangan['hpp_persen_iv'] ?? 60) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomHppIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                                    <span class="font-bold text-blue-900">Laba Kotor (Gross Profit):</span>
                                    <span id="grossIV" class="text-sm font-black text-blue-700">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_iv" id="biaya_hidup_persen_iv" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Biaya Hidup</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBiayaHidupIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_iv" id="beban_lainnya_persen_iv" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Beban Lain</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBebanLainIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Laba Bersih Operasional</div>
                                    <div class="text-xs font-extrabold text-white truncate">Hasil Wawancara</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span id="nettIV" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 11: REPAYMENT CAPACITY & DSC
            =========================================== -->
            <div id="tab-11" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            11
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REPAYMENT CAPACITY & DEBT SERVICE COVERAGE (DSC)</h3>
                            <p class="text-[11px] text-slate-500">Pengujian kemampuan bayar debitur terhadap angsuran pinjaman (Standar aman DSC &ge; 1.30x)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                        Standar DSC &ge; 1.30x
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <!-- Angsuran Balloon Payment -->
                    <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Angsuran Balloon Payment</label>
                            <input type="text" name="angsuran_balloon" id="angsuran_balloon" 
                                   value="<?= number_format((float)($keuangan['angsuran_balloon'] ?? 0), 0, ',', '.') ?>" 
                                   class="w-full text-lg font-black text-blue-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
                        </div>
                        <div class="text-[11px] text-slate-400 font-medium mt-2 pt-2 border-t border-blue-100/60">Pembayaran bunga bulanan</div>
                    </div>

                    <!-- Angsuran Installment -->
                    <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Angsuran Installment</label>
                            <input type="text" name="angsuran_installment" id="angsuran_installment" 
                                   value="<?= number_format((float)($keuangan['angsuran_installment'] ?? 0), 0, ',', '.') ?>" 
                                   class="w-full text-lg font-black text-blue-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
                        </div>
                        <div class="text-[11px] text-slate-400 font-medium mt-2 pt-2 border-t border-blue-100/60">Pokok + Bunga bulanan</div>
                    </div>

                    <!-- Angsuran yang Dipilih Sebagai Acuan -->
                    <div class="p-5 bg-blue-600 text-white rounded-2xl shadow-xs flex flex-col justify-between">
                        <div>
                            <label class="block text-[11px] font-bold text-blue-100 uppercase tracking-wider mb-2">Angsuran yang Dipilih (Acuan)</label>
                            <input type="text" name="angsuran_dipilih" id="angsuran_dipilih" 
                                   value="<?= number_format((float)($keuangan['angsuran_dipilih'] ?? 0), 0, ',', '.') ?>" 
                                   class="w-full text-base font-black text-blue-900 bg-white border border-white/40 rounded-xl px-3.5 py-2 rupiah-input focus:ring-2 focus:ring-blue-300 transition shadow-2xs h-[42px]"
                                   oninput="formatRupiah(this); hitungRasioDSC(); hitungDisbursement();">
                        </div>
                        <div class="text-[11px] text-blue-100 font-medium mt-2 pt-2 border-t border-blue-500/50">Acuan rasio DSC & Hold Angsuran</div>
                    </div>
                </div>

                <!-- Hasil Perhitungan Rasio DSC -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl border border-blue-200/80 bg-white shadow-2xs hover:border-blue-300 transition">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rasio DSC (Basis Rekening Koran)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Data Riil Bank</span>
                        </div>
                        <div class="flex items-baseline gap-3 my-2">
                            <span id="labelRasioDSCRK" class="text-4xl font-black text-slate-900 tracking-tight">0.00x</span>
                            <span id="badgeStatusDSCRK" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100 font-medium">Rumus: Laba Bersih Rekening Koran / Angsuran Acuan</div>
                    </div>

                    <div class="p-6 rounded-2xl border border-blue-200/80 bg-white shadow-2xs hover:border-blue-300 transition">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rasio DSC (Basis Wawancara)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Data Wawancara</span>
                        </div>
                        <div class="flex items-baseline gap-3 my-2">
                            <span id="labelRasioDSCIV" class="text-4xl font-black text-slate-900 tracking-tight">0.00x</span>
                            <span id="badgeStatusDSCIV" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100 font-medium">Rumus: Laba Bersih Wawancara / Angsuran Acuan</div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 12: SIMULASI PENCAIRAN (DISBURSEMENT)
            =========================================== -->
            <div id="tab-12" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            12
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">SIMULASI BIAYA & PENCAIRAN BERSIH (DISBURSEMENT)</h3>
                            <p class="text-[11px] text-slate-500">Rincian seluruh komponen biaya potong muka dan estimasi dana riil yang diterima debitur saat akad</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                        Simulasi Potongan Muka
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left: Input Biaya -->
                    <div class="space-y-4">
                        <!-- Provisi -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Provisi (%)</label>
                                <input type="number" step="0.1" name="provisi_persen" id="provisi_persen" 
                                       value="<?= esc($disbursement['provisi_persen'] ?? 7) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Provisi</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomProvisi" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Fee -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Agent Fee (%)</label>
                                <input type="number" step="0.1" name="agent_persen" id="agent_persen" 
                                       value="<?= esc($disbursement['agent_persen'] ?? 1) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Agent Fee</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomAgent" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Fee -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Admin Fee (%)</label>
                                <input type="number" step="0.1" name="admin_persen" id="admin_persen" 
                                       value="<?= esc($disbursement['admin_persen'] ?? 1) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Admin Fee</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomAdmin" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hold Angsuran -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Hold (Bulan)</label>
                                <input type="number" name="hold_angsuran_bulan" id="hold_angsuran_bulan" 
                                       value="<?= esc($disbursement['hold_angsuran_bulan'] ?? 4) ?>" min="0" max="24"
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Hold Angsuran</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomHoldAngsuran" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Asuransi Jiwa & Kebakaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Premi Asuransi Jiwa (Rp)</label>
                                <input type="text" name="asuransi_jiwa" id="asuransi_jiwa" 
                                       value="<?= number_format((float)($disbursement['asuransi_jiwa'] ?? 0), 0, ',', '.') ?>" 
                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]" 
                                       oninput="formatRupiah(this); hitungDisbursement();">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Premi Asuransi Kebakaran / Agunan (Rp)</label>
                                <input type="text" name="asuransi_kebakaran" id="asuransi_kebakaran" 
                                       value="<?= number_format((float)($disbursement['asuransi_kebakaran'] ?? 0), 0, ',', '.') ?>" 
                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]" 
                                       oninput="formatRupiah(this); hitungDisbursement();">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary Box (Clean Corporate Blue) -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
                        <div>
                            <div class="flex items-center gap-2.5 pb-3 mb-5 border-b border-blue-100">
                                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Struktur Rekapitulasi Pencairan</h3>
                                    <span class="text-[11px] text-slate-400 font-medium">Kalkulasi dana riil siap cair</span>
                                </div>
                            </div>

                            <div class="space-y-3 p-4 bg-white rounded-xl border border-blue-100 shadow-2xs text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-slate-600">Plafon Pinjaman Pokok:</span>
                                    <span id="disbPlafon" class="font-bold text-slate-800 text-sm">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                                    <span class="font-semibold text-slate-600">Total Biaya & Potongan Muka:</span>
                                    <span id="disbTotalPotongan" class="font-bold text-rose-600 text-sm">- Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Dana Bersih -->
                        <div class="mt-6 p-5 bg-blue-600 text-white rounded-2xl shadow-xs">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Estimasi Dana Bersih yang Dicairkan (Nett)</div>
                                    <div class="text-[11px] text-blue-100 font-medium">Dana riil masuk ke rekening debitur</div>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-blue-500/50 flex justify-between items-baseline">
                                <span class="text-xs text-blue-100 font-semibold">Total Bersih:</span>
                                <span id="disbDanaBersih" class="text-2xl sm:text-3xl font-black text-white tracking-tight whitespace-nowrap">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Finalisasi Pengajuan Pengusul -->
                <div class="mt-8 p-6 bg-slate-50/80 border border-slate-200/90 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Status Dokumen & Pengajuan</label>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-md">Tahap Akhir Pengusul</span>
                        </div>
                        <p class="text-xs text-slate-500">Pilih status pengajuan untuk disimpan sebagai draf atau diajukan langsung ke tahap sidang komite pembiayaan.</p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select name="status_pengajuan" id="status_pengajuan" class="w-full text-xs font-bold border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                            <?php $stp = $kredit['status_pengajuan'] ?? 'Draft'; ?>
                            <option value="Draft" <?= $stp === 'Draft' ? 'selected' : '' ?>>Draft (Belum Selesai)</option>
                            <option value="Diajukan" <?= $stp === 'Diajukan' ? 'selected' : '' ?>>Diajukan ke Komite</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 13: KEPUTUSAN KOMITE KREDIT (DINONAKTIFKAN SEMENTARA)
            =========================================== -->
            <div id="tab-13" class="tab-panel hidden" style="display: none !important;">
                <!-- Disabled Alert Banner -->
                <div class="p-4 mb-6 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wide">Tahap 13 Dinonaktifkan Sementara</h4>
                        <p class="text-xs text-amber-700">Proses pengusul kredit cukup diselesaikan sampai tahap 12 (Disbursement). Tahap 13 (Approval Komite) dinonaktifkan sementara dan tidak dapat diakses pengusul.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">13. Persetujuan & Lembar Opini Komite Pembiayaan</h2>
                        <p class="text-sm text-slate-500">Pencatatan persetujuan berjenjang komite kredit, rekomendasi limit, dan syarat pencairan.</p>
                    </div>
                    <button type="button" onclick="tambahBarisKomite()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Tambah Approver Komite
                    </button>
                </div>

                <div class="space-y-4 mb-8" id="containerKomite">
                    <?php if (empty($komite)): ?>
                        <!-- Default 2 Approvers -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                            <button type="button" onclick="hapusBarisTabel(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                                    <select name="komite[0][role_approver]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                        <option value="Reviewer / Analis Senior">Reviewer / Analis Senior</option>
                                        <option value="Komite 1">Komite 1 (Branch Manager)</option>
                                        <option value="Komite 2">Komite 2 (Head of Credit)</option>
                                        <option value="Direksi">Direksi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                                    <input type="text" name="komite[0][nama_approver]" placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                                    <select name="komite[0][status_approval]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold">
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Disetujui Bersyarat">Disetujui Bersyarat</option>
                                        <option value="Pending">Pending / Tinjau Kembali</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                                    <input type="date" name="komite[0][tanggal_approval]" value="<?= date('Y-m-d') ?>" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                                    <textarea name="komite[0][catatan_approval]" rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"></textarea>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($komite as $idx => $k): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                                <button type="button" onclick="hapusBarisTabel(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                                        <select name="komite[<?= $idx ?>][role_approver]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                            <?php $ra = $k['role_approver'] ?? 'Komite 1'; ?>
                                            <option value="Reviewer / Analis Senior" <?= $ra === 'Reviewer / Analis Senior' ? 'selected' : '' ?>>Reviewer / Analis Senior</option>
                                            <option value="Komite 1" <?= $ra === 'Komite 1' ? 'selected' : '' ?>>Komite 1 (Branch Manager)</option>
                                            <option value="Komite 2" <?= $ra === 'Komite 2' ? 'selected' : '' ?>>Komite 2 (Head of Credit)</option>
                                            <option value="Direksi" <?= $ra === 'Direksi' ? 'selected' : '' ?>>Direksi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                                        <input type="text" name="komite[<?= $idx ?>][nama_approver]" value="<?= esc($k['nama_approver'] ?? '') ?>" placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                                        <select name="komite[<?= $idx ?>][status_approval]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold">
                                            <?php $sa = $k['status_approval'] ?? 'Pending'; ?>
                                            <option value="Disetujui" <?= $sa === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                            <option value="Disetujui Bersyarat" <?= $sa === 'Disetujui Bersyarat' ? 'selected' : '' ?>>Disetujui Bersyarat</option>
                                            <option value="Pending" <?= $sa === 'Pending' ? 'selected' : '' ?>>Pending / Tinjau Kembali</option>
                                            <option value="Ditolak" <?= $sa === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                                        <input type="date" name="komite[<?= $idx ?>][tanggal_approval]" value="<?= esc($k['tanggal_approval'] ?? date('Y-m-d')) ?>" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                                        <textarea name="komite[<?= $idx ?>][catatan_approval]" rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"><?= esc($k['catatan_approval'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Status Final Pengajuan (Khusus Komite - Disabled di Tab 13) -->
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Status Pengajuan Akhir Dokumen CAM</label>
                        <p class="text-xs text-slate-500">Status final komite pembiayaan (dinonaktifkan sementara di tahap pengusul).</p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select id="status_pengajuan_komite_disabled" disabled class="w-full text-xs font-bold border border-slate-200 rounded-xl p-2.5 bg-slate-100 cursor-not-allowed">
                            <?php $stp = $kredit['status_pengajuan'] ?? 'Draft'; ?>
                            <option value="Draft" <?= $stp === 'Draft' ? 'selected' : '' ?>>Draft (Belum Selesai)</option>
                            <option value="Diajukan" <?= $stp === 'Diajukan' ? 'selected' : '' ?>>Diajukan ke Komite</option>
                            <option value="Disetujui" <?= $stp === 'Disetujui' ? 'selected' : '' ?>>Disetujui (Approved)</option>
                            <option value="Ditolak" <?= $stp === 'Ditolak' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                        </select>
                    </div>
                </div>
            </div>

                </form>
            </div>
        </div>
    </main>

    <!-- Bottom Sticky Navigation Controls -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 py-3 z-20 shadow-md">
        <div class="w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <button type="button" onclick="tabNav(-1)" id="btnPrev" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5">
                &larr; Sebelumnya
            </button>
            <div class="flex items-center gap-2">
                <span id="tabStepLabel" class="text-xs font-semibold text-slate-500 hidden sm:inline">Langkah 1 dari 12</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="simpanForm(false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
                    Simpan Draft
                </button>
                <button type="button" onclick="tabNav(1)" id="btnNext" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                    Selanjutnya &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toastMessage" class="fixed top-20 right-6 z-50 transform translate-y-[-100px] opacity-0 transition-all duration-300 pointer-events-none">
        <div class="p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2" id="toastBox">
            <span id="toastText">Pesan</span>
        </div>
    </div>

    <!-- Script Application Logic -->
    <script>
        let currentTab = 1;
        const totalTabs = 12; // Proses pengusul dibatasi sampai tahap 12 (Tahap 13 didisabel sementara)

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            hitungKalkulasiAngsuran();
            hitungCoverageAgunan();
            hitungTotalSlik();
            hitungRataRataRekening();
            hitungLabaRugi();
            hitungDisbursement();

            // Buka tab spesifik jika ada query parameter misal ?tab=6 untuk Petugas SLIK
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = parseInt(urlParams.get('tab'));
            if (initialTab && initialTab >= 1 && initialTab <= totalTabs) {
                goToTab(initialTab);
            }
        });

        const stepsMetadata = [
            { title: '1. Aplikasi', sub: 'Sedang dikerjakan', defaultSub: 'Parameter dasar', mainTitle: 'Langkah 1: Aplikasi Pengajuan', desc: 'Konfigurasi parameter dasar plafon dan skema kredit debitur.' },
            { title: '2. Info Pribadi', sub: 'Sedang dikerjakan', defaultSub: 'Data demografi', mainTitle: 'Langkah 2: Data Informasi Pribadi', desc: 'Data identitas nasabah pemohon sesuai KTP dan verifikasi kependudukan.' },
            { title: '3. Info Usaha', sub: 'Sedang dikerjakan', defaultSub: 'Legalitas usaha', mainTitle: 'Langkah 3: Profil & Legalitas Usaha', desc: 'Informasi operasional, pengalaman, legalitas izin dan tempat usaha.' },
            { title: '4. Jaminan', sub: 'Sedang dikerjakan', defaultSub: 'Agunan kredit', mainTitle: 'Langkah 4: Agunan & Jaminan Kredit', desc: 'Data sertifikat tanah, BPKB kendaraan, dan taksasi nilai agunan.' },
            { title: '5. Dokumen', sub: 'Sedang dikerjakan', defaultSub: 'Unggah berkas', mainTitle: 'Langkah 5: Unggah Dokumen Berkas', desc: 'Unggah berkas pendukung KTP, KK, NPWP, NIB, dan agunan.' },
            { title: '6. SLIK / CLIK', sub: 'Sedang dikerjakan', defaultSub: 'Cek riwayat kredit', mainTitle: 'Langkah 6: Cek SLIK OJK & Riwayat', desc: 'Riwayat kolektibilitas kredit pada bank atau lembaga keuangan lain.' },
            { title: '7. Web Check', sub: 'Sedang dikerjakan', defaultSub: 'Verifikasi online', mainTitle: 'Langkah 7: Verifikasi Web & Legal Check', desc: 'Pengecekan profiling web, kepatuhan hukum, dan reputasi usaha.' },
            { title: '8. Analisa Kredit', sub: 'Sedang dikerjakan', defaultSub: 'Scoring kelayakan', mainTitle: 'Langkah 8: Analisa Kelayakan 5C & AI', desc: 'Evaluasi prinsip 5C dan rekomendasi skor risiko cerdas dari AI.' },
            { title: '9. Rekening Koran', sub: 'Sedang dikerjakan', defaultSub: 'Analisis mutasi', mainTitle: 'Langkah 9: Analisis Mutasi Rekening', desc: 'Pencatatan saldo rata-rata dan total mutasi kredit debet rekening koran.' },
            { title: '10. Laba Rugi', sub: 'Sedang dikerjakan', defaultSub: 'Proyeksi keuangan', mainTitle: 'Langkah 10: Analisis Laba Rugi Usaha', desc: 'Perhitungan omset pendapatan, HPP pengeluaran, dan laba bersih riil.' },
            { title: '11. Repayment', sub: 'Sedang dikerjakan', defaultSub: 'Kemampuan angsur', mainTitle: 'Langkah 11: Kemampuan Angsur & DSC', desc: 'Perhitungan rasio Debt Service Coverage dan batas aman angsuran bulanan.' },
            { title: '12. Disbursement', sub: 'Sedang dikerjakan', defaultSub: 'Jadwal pencairan', mainTitle: 'Langkah 12: Jadwal Pencairan (Tahap Akhir Pengusul)', desc: 'Rincian simulasi potongan biaya muka, estimasi pencairan bersih, dan status pengajuan.' }
        ];

        // Tab Navigation
        function goToTab(tabIndex) {
            if (tabIndex < 1 || tabIndex > totalTabs) return;
            currentTab = tabIndex;

            // Sembunyikan semua tab panel
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            // Tampilkan tab terpilih
            const target = document.getElementById('tab-' + tabIndex);
            if (target) target.classList.add('active');

            // Update Header Judul & Deskripsi
            const meta = stepsMetadata[tabIndex - 1];
            if (meta) {
                const titleEl = document.getElementById('stepMainTitle');
                const subEl = document.getElementById('stepSubTitle');
                if (titleEl) titleEl.innerText = meta.mainTitle;
                if (subEl) subEl.innerText = meta.desc;
            }

            // Update Sidebar Stepper Progress (1 - 12)
            const tah = document.getElementById('sidebarTahapNum');
            const bad = document.getElementById('sidebarTahapBadge');
            const prog = document.getElementById('progressBarFill');
            if (tah) tah.innerText = tabIndex;
            if (bad) bad.innerText = tabIndex + '/' + totalTabs;
            if (prog) prog.style.width = ((tabIndex / totalTabs) * 100).toFixed(1) + '%';

            // Update Vertical Stepper List (1-12)
            for (let i = 1; i <= totalTabs; i++) {
                const item = document.getElementById('step-item-' + i);
                const circle = document.getElementById('step-circle-' + i);
                const title = document.getElementById('step-title-' + i);
                const sub = document.getElementById('step-sub-' + i);
                const stepMeta = stepsMetadata[i - 1];

                if (!item || !circle || !title || !sub) continue;

                if (i === tabIndex) {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 bg-blue-50/80 border border-blue-200/80 shadow-2xs';
                    circle.className = 'w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-xs shrink-0';
                    circle.innerHTML = i;
                    title.className = 'text-xs font-bold text-blue-900 truncate';
                    sub.className = 'text-[11px] text-blue-600 font-semibold truncate';
                    sub.innerText = 'Sedang dikerjakan';
                } else if (i < tabIndex) {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent';
                    circle.className = 'w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0 border border-blue-200';
                    circle.innerHTML = '&check;';
                    title.className = 'text-xs font-bold text-slate-800 truncate';
                    sub.className = 'text-[11px] text-emerald-600 font-medium truncate';
                    sub.innerText = 'Selesai';
                } else {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent';
                    circle.className = 'w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0';
                    circle.innerHTML = i;
                    title.className = 'text-xs font-bold text-slate-700 truncate';
                    sub.className = 'text-[11px] text-slate-400 truncate';
                    sub.innerText = stepMeta.defaultSub;
                }
            }

            // Update Label & Button Prev/Next
            const lbl = document.getElementById('tabStepLabel');
            if (lbl) lbl.innerText = `Langkah ${tabIndex} dari ${totalTabs}`;
            const prev = document.getElementById('btnPrev');
            if (prev) prev.style.visibility = tabIndex === 1 ? 'hidden' : 'visible';
            const nxt = document.getElementById('btnNext');
            if (nxt) {
                if (tabIndex === totalTabs) {
                    nxt.className = 'px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5';
                    nxt.innerHTML = 'Simpan & Selesai Pengajuan &check;';
                } else {
                    nxt.className = 'px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5';
                    nxt.innerHTML = 'Selanjutnya &rarr;';
                }
            }

            // Animasi scroll stepper sidebar agar tidak terpotong dan responsif terhadap pergantian tahap
            scrollStepperList(tabIndex);

            // Scroll ke atas halaman form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Fungsi animasi scroll otomatis stepper sidebar
        function scrollStepperList(tabIndex) {
            const list = document.getElementById('verticalStepperList');
            if (!list) return;

            if (tabIndex === 1) {
                // Saat kembali ke langkah 1, scroll ke posisi paling atas
                list.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            const activeItem = document.getElementById('step-item-' + tabIndex);
            const item12 = document.getElementById('step-item-12');

            if (tabIndex === 2 && item12) {
                // Ketika pencet langkah 2, geser ke bawah secara halus agar langkah 12 muncul dan langkah 1 bergeser ke atas
                const item12Bottom = item12.offsetTop + item12.offsetHeight;
                const targetScroll = Math.max(0, item12Bottom - list.clientHeight + 8);
                list.scrollTo({ top: targetScroll, behavior: 'smooth' });
            } else if (activeItem) {
                // Jaga agar langkah aktif lainnya selalu berada di area pandang
                const itemTop = activeItem.offsetTop;
                const itemBottom = itemTop + activeItem.offsetHeight;
                const viewTop = list.scrollTop;
                const viewBottom = viewTop + list.clientHeight;

                if (itemTop < viewTop) {
                    list.scrollTo({ top: itemTop - 8, behavior: 'smooth' });
                } else if (itemBottom > viewBottom) {
                    list.scrollTo({ top: itemBottom - list.clientHeight + 8, behavior: 'smooth' });
                }
            }
        }

        function tabNav(delta) {
            const next = currentTab + delta;
            if (next > totalTabs) {
                simpanForm(true);
            } else if (next >= 1) {
                // Auto simpan progress saat berganti langkah
                simpanForm(false, false);
                goToTab(next);
            }
        }

        // Sinkronisasi suku bunga bulanan vs tahunan
        function syncBunga(origin) {
            const bungaBulanInput = document.getElementById('bunga_per_bulan');
            const bungaTahunInput = document.getElementById('bunga_per_tahun');
            if (origin === 'bulan') {
                const b = parseFloat(bungaBulanInput.value) || 0;
                bungaTahunInput.value = (b * 12).toFixed(2);
            } else {
                const t = parseFloat(bungaTahunInput.value) || 0;
                bungaBulanInput.value = (t / 12).toFixed(2);
            }
        }

        // Kalkulasi Angsuran Pinjaman (Balloon & Installment)
        function hitungKalkulasiAngsuran() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            const tenor = Math.max(1, parseInt(document.getElementById('tenor_bulan')?.value || 12));
            const bungaBulanPct = parseFloat(document.getElementById('bunga_per_bulan')?.value || 0) / 100;
            const bungaTahunPct = parseFloat(document.getElementById('bunga_per_tahun')?.value || 0) / 100;

            // 1. Balloon Payment: bunga per bulan
            const angsuranBalloon = Math.round(plafon * bungaBulanPct);
            // 2. Installment Flat: pokok/bulan + bunga/bulan
            const pokokBulan = plafon / tenor;
            const bungaInstallmentBulan = (plafon * bungaTahunPct) / 12;
            const angsuranInstallment = Math.round(pokokBulan + bungaInstallmentBulan);

            document.getElementById('boxAngsuranBalloon').innerText = formatRupiahString(angsuranBalloon);
            document.getElementById('boxAngsuranInstallment').innerText = formatRupiahString(angsuranInstallment);
            
            const fieldBalloon = document.getElementById('angsuran_balloon');
            if (fieldBalloon) fieldBalloon.value = formatRupiahString(angsuranBalloon);
            const fieldInst = document.getElementById('angsuran_installment');
            if (fieldInst) fieldInst.value = formatRupiahString(angsuranInstallment);

            // Default angsuran dipilih jika masih kosong
            const fieldPilih = document.getElementById('angsuran_dipilih');
            if (fieldPilih && (!fieldPilih.value || fieldPilih.value === '0')) {
                const skema = document.getElementById('skema_pembayaran')?.value || 'Balloon Payment';
                fieldPilih.value = formatRupiahString(skema === 'Balloon Payment' ? angsuranBalloon : angsuranInstallment);
            }

            hitungRasioDSC();
        }

        // Kalkulasi Agunan & Coverage Ratio
        function hitungCoverageAgunan() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            let totalMV = 0;
            let totalLV = 0;

            const items = document.querySelectorAll('.agunan-item');
            items.forEach(item => {
                const mvInput = item.querySelector('.agunan-mv');
                const lvPctInput = item.querySelector('.agunan-lv-pct');
                const lvLabel = item.querySelector('.agunan-lv-nominal');

                const mv = parseNominal(mvInput?.value || 0);
                const lvPct = parseFloat(lvPctInput?.value || 60);
                const lv = Math.round(mv * (lvPct / 100));

                if (lvLabel) lvLabel.innerText = formatRupiahString(lv);
                totalMV += mv;
                totalLV += lv;
            });

            document.getElementById('totalAgunanMV').innerText = formatRupiahString(totalMV);
            document.getElementById('totalAgunanLV').innerText = formatRupiahString(totalLV);

            const covMV = plafon > 0 ? ((totalMV / plafon) * 100).toFixed(1) : 0;
            const covLV = plafon > 0 ? ((totalLV / plafon) * 100).toFixed(1) : 0;

            document.getElementById('coverageMV').innerText = covMV + '%';
            document.getElementById('coverageLV').innerText = covLV + '%';
        }

        // Tambah & Hapus Baris Agunan
        function tambahBarisAgunan() {
            const container = document.getElementById('containerAgunan');
            const count = container.querySelectorAll('.agunan-item').length;
            const itemNum = count + 1;
            const html = `
                <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                    <!-- Item Top Bar -->
                    <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num">${itemNum}</span></span>
                        </div>
                        <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat Lengkap Objek Jaminan
                            </label>
                            <input type="text" name="agunan[${count}][alamat_agunan]" placeholder="Alamat lokasi aset jaminan"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Jenis Dokumen Agunan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Jenis Dokumen Agunan
                            </label>
                            <select name="agunan[${count}][jenis_agunan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                <option value="Bilyet Deposito">Bilyet Deposito</option>
                            </select>
                        </div>

                        <!-- Nomor Sertifikat / BPKB -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                Nomor Sertifikat / BPKB
                            </label>
                            <input type="text" name="agunan[${count}][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nama Pemilik Agunan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Nama Pemilik Agunan
                            </label>
                            <input type="text" name="agunan[${count}][pemilik_agunan]" placeholder="Sesuai nama di sertifikat"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Hubungan Kepemilikan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Hubungan Kepemilikan
                            </label>
                            <select name="agunan[${count}][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="Milik Sendiri">Milik Sendiri</option>
                                <option value="Suami / Istri">Suami / Istri</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Pihak Ketiga">Pihak Ketiga</option>
                            </select>
                        </div>

                        <!-- Status IMB / PBG -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Status IMB / PBG
                            </label>
                            <select name="agunan[${count}][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="Ada">Ada</option>
                                <option value="Tidak Ada">Tidak Ada</option>
                                <option value="Proses Pengurusan">Proses Pengurusan</option>
                            </select>
                        </div>

                        <!-- Lama Kepemilikan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Lama Kepemilikan
                            </label>
                            <input type="text" name="agunan[${count}][lama_kepemilikan]" placeholder="Contoh: 8 Tahun"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Tanah (m²) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                Luas Tanah (m²)
                            </label>
                            <input type="number" name="agunan[${count}][luas_tanah_m2]" placeholder="Contoh: 120"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Bangunan (m²) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Luas Bangunan (m²)
                            </label>
                            <input type="number" name="agunan[${count}][luas_bangunan_m2]" placeholder="Contoh: 85"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nilai Pasar / MV (Rp) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Nilai Pasar / MV (Rp)
                            </label>
                            <input type="text" name="agunan[${count}][nilai_pasar_mv]" placeholder="0"
                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv"
                                   oninput="formatRupiah(this); hitungCoverageAgunan();">
                        </div>

                        <!-- Likuidasi (% & Nominal Nilai LV) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                Likuidasi (% / Nilai LV)
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="relative w-16 shrink-0">
                                    <input type="number" step="1" name="agunan[${count}][persen_likuidasi]" value="60"
                                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct"
                                           oninput="hitungCoverageAgunan();">
                                </div>
                                <span class="text-xs text-slate-400 font-bold">%</span>
                                <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                    <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            hitungCoverageAgunan();
        }

        function hapusBarisAgunan(btn) {
            const container = document.getElementById('containerAgunan');
            if (container.querySelectorAll('.agunan-item').length > 1) {
                btn.closest('.agunan-item').remove();
                // Update numbering
                container.querySelectorAll('.agunan-item').forEach((el, index) => {
                    const numSpan = el.querySelector('.agunan-num');
                    if (numSpan) numSpan.textContent = index + 1;
                });
                hitungCoverageAgunan();
            } else {
                alert('Minimal harus ada 1 aset agunan');
            }
        }

        // Kalkulasi SLIK Total Outstanding
        function hitungTotalSlik() {
            let total = 0;
            document.querySelectorAll('.slik-outstanding').forEach(inp => {
                total += parseNominal(inp.value || 0);
            });
            document.getElementById('totalOutstandingSlik').innerText = formatRupiahString(total);
        }

        function tambahBarisSlik() {
            const tbody = document.getElementById('bodySlik');
            const count = tbody.querySelectorAll('tr').length;
            const html = `
                <tr>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][nama_debitur]" placeholder="Debitur / Pasangan" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][plafon]" placeholder="0" 
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                               oninput="formatRupiah(this)">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][outstanding]" placeholder="0" 
                               class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                               oninput="formatRupiah(this); hitungTotalSlik();">
                    </td>
                    <td class="p-2.5 bg-white">
                        <select name="slik[${count}][kelancaran]" 
                                class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                            <option value="Lancar">Kol 1 (Lancar)</option>
                            <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                            <option value="Macet">Kol 5 (Macet)</option>
                        </select>
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white text-center">
                        <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100" title="Hapus Baris">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', html);
        }

        // Kalkulasi Rekening Koran
        function hitungRataRataRekening() {
            let totalAvgKredit = 0;
            const items = document.querySelectorAll('.rekening-item');
            items.forEach(item => {
                const kInput = item.querySelector('.rek-kredit');
                const bInput = item.querySelector('.rek-bulan');
                const labelAvg = item.querySelector('.rek-avg-kredit');

                const totalKredit = parseNominal(kInput?.value || 0);
                const bulan = Math.max(1, parseInt(bInput?.value || 6));
                const avgKredit = Math.round(totalKredit / bulan);

                if (labelAvg) labelAvg.innerText = formatRupiahString(avgKredit);
                totalAvgKredit += avgKredit;
            });

            document.getElementById('totalAvgKreditGabungan').innerText = formatRupiahString(totalAvgKredit);
        }

        function salinOmzetKeLabaRugi() {
            let totalAvgKredit = 0;
            document.querySelectorAll('.rekening-item').forEach(item => {
                const kInput = item.querySelector('.rek-kredit');
                const bInput = item.querySelector('.rek-bulan');
                const totalKredit = parseNominal(kInput?.value || 0);
                const bulan = Math.max(1, parseInt(bInput?.value || 6));
                totalAvgKredit += Math.round(totalKredit / bulan);
            });

            if (totalAvgKredit > 0) {
                document.getElementById('pendapatan_rk').value = formatRupiahString(totalAvgKredit);
                hitungLabaRugi();
                showToast('Omzet rata-rata rekening koran berhasil disalin ke Tab Laba Rugi', 'success');
                goToTab(10);
            } else {
                showToast('Isi mutasi kredit rekening koran terlebih dahulu', 'error');
            }
        }

        function tambahBarisRekening() {
            const container = document.getElementById('containerRekening');
            const count = container.querySelectorAll('.rekening-item').length;
            const html = `
                <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                        </div>
                        <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>
                    <!-- Baris 1: Identitas Rekening -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                            <input type="text" name="rekening[${count}][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                            <input type="text" name="rekening[${count}][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                            <input type="text" name="rekening[${count}][nomor_rekening]" placeholder="Nomor rekening bank" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                            <input type="text" name="rekening[${count}][atas_nama]" placeholder="Nama pemilik rekening" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                    </div>
                    <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_kredit]" placeholder="0" 
                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                   oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_debit]" placeholder="0" 
                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                   oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                            <input type="number" name="rekening[${count}][jumlah_bulan]" value="6" min="1" max="24" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                   oninput="hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                            <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                Rp 0
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        // Kalkulasi Laba Rugi (RK & Wawancara)
        function hitungLabaRugi() {
            // Track 1: RK
            const pRK = parseNominal(document.getElementById('pendapatan_rk')?.value || 0);
            const hppPctRK = parseFloat(document.getElementById('hpp_persen_rk')?.value || 60);
            const nomHppRK = Math.round(pRK * (hppPctRK / 100));
            const grossRK = pRK - nomHppRK;

            const bhPctRK = parseFloat(document.getElementById('biaya_hidup_persen_rk')?.value || 10);
            const nomBhRK = Math.round(pRK * (bhPctRK / 100));

            const blPctRK = parseFloat(document.getElementById('beban_lainnya_persen_rk')?.value || 10);
            const nomBlRK = Math.round(pRK * (blPctRK / 100));

            const nettRK = grossRK - nomBhRK - nomBlRK;

            document.getElementById('nomHppRK').innerText = formatRupiahString(nomHppRK);
            document.getElementById('grossRK').innerText = formatRupiahString(grossRK);
            document.getElementById('nomBiayaHidupRK').innerText = formatRupiahString(nomBhRK);
            document.getElementById('nomBebanLainRK').innerText = formatRupiahString(nomBlRK);
            document.getElementById('nettRK').innerText = formatRupiahString(nettRK);

            // Track 2: IV
            const pIV = parseNominal(document.getElementById('pendapatan_iv')?.value || 0);
            const hppPctIV = parseFloat(document.getElementById('hpp_persen_iv')?.value || 60);
            const nomHppIV = Math.round(pIV * (hppPctIV / 100));
            const grossIV = pIV - nomHppIV;

            const bhPctIV = parseFloat(document.getElementById('biaya_hidup_persen_iv')?.value || 10);
            const nomBhIV = Math.round(pIV * (bhPctIV / 100));

            const blPctIV = parseFloat(document.getElementById('beban_lainnya_persen_iv')?.value || 10);
            const nomBlIV = Math.round(pIV * (blPctIV / 100));

            const nettIV = grossIV - nomBhIV - nomBlIV;

            document.getElementById('nomHppIV').innerText = formatRupiahString(nomHppIV);
            document.getElementById('grossIV').innerText = formatRupiahString(grossIV);
            document.getElementById('nomBiayaHidupIV').innerText = formatRupiahString(nomBhIV);
            document.getElementById('nomBebanLainIV').innerText = formatRupiahString(nomBlIV);
            document.getElementById('nettIV').innerText = formatRupiahString(nettIV);

            hitungRasioDSC();
        }

        // Kalkulasi Rasio DSC
        function hitungRasioDSC() {
            const angsuran = parseNominal(document.getElementById('angsuran_dipilih')?.value || 0);

            // Nett RK
            const pRK = parseNominal(document.getElementById('pendapatan_rk')?.value || 0);
            const hppPctRK = parseFloat(document.getElementById('hpp_persen_rk')?.value || 60);
            const bhPctRK = parseFloat(document.getElementById('biaya_hidup_persen_rk')?.value || 10);
            const blPctRK = parseFloat(document.getElementById('beban_lainnya_persen_rk')?.value || 10);
            const nettRK = pRK - (pRK * (hppPctRK/100)) - (pRK * (bhPctRK/100)) - (pRK * (blPctRK/100));

            // Nett IV
            const pIV = parseNominal(document.getElementById('pendapatan_iv')?.value || 0);
            const hppPctIV = parseFloat(document.getElementById('hpp_persen_iv')?.value || 60);
            const bhPctIV = parseFloat(document.getElementById('biaya_hidup_persen_iv')?.value || 10);
            const blPctIV = parseFloat(document.getElementById('beban_lainnya_persen_iv')?.value || 10);
            const nettIV = pIV - (pIV * (hppPctIV/100)) - (pIV * (bhPctIV/100)) - (pIV * (blPctIV/100));

            const dscRK = angsuran > 0 ? (nettRK / angsuran).toFixed(2) : 0;
            const dscIV = angsuran > 0 ? (nettIV / angsuran).toFixed(2) : 0;

            document.getElementById('labelRasioDSCRK').innerText = dscRK + 'x';
            document.getElementById('labelRasioDSCIV').innerText = dscIV + 'x';

            updateDSCBadge('badgeStatusDSCRK', dscRK);
            updateDSCBadge('badgeStatusDSCIV', dscIV);
        }

        function updateDSCBadge(elementId, ratio) {
            const badge = document.getElementById(elementId);
            if (!badge) return;
            const val = parseFloat(ratio);
            if (val >= 1.3) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                badge.innerText = 'Sangat Aman (\u2265 1.30x)';
            } else if (val >= 1.0) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                badge.innerText = 'Cukup / Waspada';
            } else {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
                badge.innerText = 'Defisit / Berisiko (< 1.0x)';
            }
        }

        // Kalkulasi Disbursement (Pencairan Bersih)
        function hitungDisbursement() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            const provPct = parseFloat(document.getElementById('provisi_persen')?.value || 7);
            const nomProv = Math.round(plafon * (provPct / 100));

            const agentPct = parseFloat(document.getElementById('agent_persen')?.value || 1);
            const nomAgent = Math.round(plafon * (agentPct / 100));

            const adminPct = parseFloat(document.getElementById('admin_persen')?.value || 1);
            const nomAdmin = Math.round(plafon * (adminPct / 100));

            const holdBulan = parseInt(document.getElementById('hold_angsuran_bulan')?.value || 4);
            const angsuran = parseNominal(document.getElementById('angsuran_dipilih')?.value || 0);
            const nomHold = Math.round(angsuran * holdBulan);

            const asJiwa = parseNominal(document.getElementById('asuransi_jiwa')?.value || 0);
            const asKeb  = parseNominal(document.getElementById('asuransi_kebakaran')?.value || 0);

            const totalPot = nomProv + nomAgent + nomAdmin + nomHold + asJiwa + asKeb;
            const danaBersih = Math.max(0, plafon - totalPot);

            document.getElementById('nomProvisi').innerText = formatRupiahString(nomProv);
            document.getElementById('nomAgent').innerText = formatRupiahString(nomAgent);
            document.getElementById('nomAdmin').innerText = formatRupiahString(nomAdmin);
            document.getElementById('nomHoldAngsuran').innerText = formatRupiahString(nomHold);

            document.getElementById('disbPlafon').innerText = formatRupiahString(plafon);
            document.getElementById('disbTotalPotongan').innerText = '- ' + formatRupiahString(totalPot);
            document.getElementById('disbDanaBersih').innerText = formatRupiahString(danaBersih);
        }

        // Tambah Baris Komite
        function tambahBarisKomite() {
            const container = document.getElementById('containerKomite');
            const count = container.children.length;
            const html = `
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                    <button type="button" onclick="hapusBarisTabel(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                            <select name="komite[${count}][role_approver]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                <option value="Reviewer / Analis Senior">Reviewer / Analis Senior</option>
                                <option value="Komite 1">Komite 1 (Branch Manager)</option>
                                <option value="Komite 2">Komite 2 (Head of Credit)</option>
                                <option value="Direksi">Direksi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                            <input type="text" name="komite[${count}][nama_approver]" placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                            <select name="komite[${count}][status_approval]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold">
                                <option value="Disetujui">Disetujui</option>
                                <option value="Disetujui Bersyarat">Disetujui Bersyarat</option>
                                <option value="Pending">Pending / Tinjau Kembali</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                            <input type="date" name="komite[${count}][tanggal_approval]" value="<?= date('Y-m-d') ?>" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                            <textarea name="komite[${count}][catatan_approval]" rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function hapusBarisTabel(btn) {
            btn.closest('tr, .p-4')?.remove();
        }

        // Upload Dokumen AJAX
        async function prosesUploadDokumen(input) {
            const file = input.files[0];
            if (!file) return;

            const kreditId = document.getElementById('kredit_id').value;
            if (!kreditId) {
                // Simpan data draft dulu sebelum upload agar punya ID
                await simpanForm(false, false);
            }

            const activeKreditId = document.getElementById('kredit_id').value;
            if (!activeKreditId) {
                showToast('Harap simpan data form terlebih dahulu sebelum mengunggah dokumen.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('kredit_id', activeKreditId);
            formData.append('kategori', document.getElementById('kategoriUpload').value);

            showToast('Mengunggah dokumen...', 'info');

            try {
                const res = await fetch('<?= base_url('pengajuan/upload-dokumen') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    showToast('Dokumen berhasil diunggah', 'success');
                    // Hapus empty state jika ada
                    const emptyState = document.getElementById('emptyDokumenState');
                    if (emptyState) emptyState.remove();

                    // Tambahkan ke list dokumen
                    const list = document.getElementById('listDokumenTerunggah');
                    const ext = (data.nama_file.split('.').pop() || 'DOC').toUpperCase();
                    const itemHtml = `
                        <div class="p-3.5 bg-white border border-slate-200/90 rounded-xl flex items-center justify-between text-xs hover:border-blue-300 transition shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0 border border-blue-100">
                                    ${ext}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">${data.nama_file}</div>
                                    <div class="text-[11px] text-slate-400">Baru saja diunggah</div>
                                </div>
                            </div>
                            <a href="${data.path}" target="_blank" class="px-3 py-1.5 text-blue-600 hover:text-blue-800 font-semibold text-xs border border-blue-200 rounded-lg hover:bg-blue-50 transition">
                                Lihat File
                            </a>
                        </div>
                    `;
                    list.insertAdjacentHTML('afterbegin', itemHtml);

                    // Update badge counter
                    const badge = document.getElementById('docCountBadge');
                    if (badge) {
                        const total = list.querySelectorAll('a[href]').length;
                        badge.innerText = `${total} Dokumen`;
                    }
                    input.value = '';
                } else {
                    showToast(data.message || 'Gagal mengunggah file', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan koneksi saat upload', 'error');
            }
        }

        // Simpan Form Data via AJAX
        async function simpanForm(isComplete = false, showNotification = true) {
            const form = document.getElementById('camForm');
            const formData = new FormData(form);

            // Update save indicator
            const ind = document.getElementById('saveStatusIndicator');
            if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Menyimpan data...';

            try {
                const res = await fetch('<?= base_url('pengajuan/simpan') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    if (data.kredit_id) {
                        document.getElementById('kredit_id').value = data.kredit_id;
                    }
                    if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tersimpan di database';

                    if (showNotification) {
                        showToast(data.message || 'Data berhasil disimpan', 'success');
                    }

                    if (isComplete) {
                        setTimeout(() => {
                            window.location.href = '<?= base_url('dashboard') ?>';
                        }, 1000);
                    }
                } else {
                    if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span> Gagal menyimpan';
                    if (showNotification) showToast(data.message || 'Gagal menyimpan data', 'error');
                }
            } catch (err) {
                if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span> Error koneksi';
                if (showNotification) showToast('Gagal terhubung ke server database', 'error');
            }
        }

        // Trigger AI Assistant (Gemini AI API)
        async function jalankanAnalisaAI() {
            showToast('Menghubungkan ke Gemini AI untuk evaluasi risiko...', 'info');

            const form = document.getElementById('camForm');
            const formData = new FormData(form);

            try {
                const res = await fetch('<?= base_url('pengajuan/analisa-ai') ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    if (data.skor_kelayakan_ai) {
                        document.getElementById('skor_kelayakan_ai').value = data.skor_kelayakan_ai;
                    }
                    if (data.rekomendasi_keputusan_ai) {
                        document.getElementById('rekomendasi_keputusan_ai').value = data.rekomendasi_keputusan_ai;
                    }
                    if (data.rekomendasi_plafon_ai) {
                        document.getElementById('rekomendasi_plafon_ai').value = formatRupiahString(data.rekomendasi_plafon_ai);
                    }
                    if (data.analisis_risiko_ai) {
                        document.getElementById('analisis_risiko_ai').value = data.analisis_risiko_ai;
                    }

                    const banner = document.getElementById('aiPdfBanner');
                    if (banner) banner.classList.remove('hidden');

                    const sumber = document.getElementById('aiSumberLabel');
                    if (sumber && data.sumber) {
                        sumber.innerText = data.sumber + ' - Skor ' + data.skor_kelayakan_ai + '/100';
                    }

                    showToast('Analisa AI berhasil! Rekomendasi siap dicetak.', 'success');

                    // Simpan otomatis ke database
                    simpanForm(false, false);
                } else {
                    showToast(data.message || 'Gagal menjalankan evaluasi AI', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan saat memproses evaluasi AI', 'error');
            }
        }

        // Buka Laporan CAM PDF resmi
        function bukaLaporanPDF() {
            const kreditId = document.getElementById('kredit_id').value;
            if (!kreditId) {
                // Simpan dulu lalu buka
                simpanForm(false, false).then(() => {
                    const newId = document.getElementById('kredit_id').value;
                    if (newId) {
                        window.open('<?= base_url('pengajuan/cetak/') ?>' + newId + '?print=1', '_blank');
                    } else {
                        showToast('Harap simpan data form terlebih dahulu', 'error');
                    }
                });
            } else {
                window.open('<?= base_url('pengajuan/cetak/') ?>' + kreditId + '?print=1', '_blank');
            }
        }

        // Helper Formatters
        function formatRupiah(input) {
            let val = input.value.replace(/[^0-9]/g, '');
            input.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
        }

        function formatRupiahString(num) {
            return 'Rp ' + (parseInt(num, 10) || 0).toLocaleString('id-ID');
        }

        function parseNominal(val) {
            if (typeof val === 'number') return val;
            return parseInt(String(val).replace(/[^0-9]/g, ''), 10) || 0;
        }

        function showToast(text, type = 'info') {
            const toast = document.getElementById('toastMessage');
            const box = document.getElementById('toastBox');
            const msg = document.getElementById('toastText');

            msg.innerText = text;
            if (type === 'success') {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-emerald-600 text-white border-emerald-700';
            } else if (type === 'error') {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-rose-600 text-white border-rose-700';
            } else {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-slate-900 text-white border-slate-800';
            }

            toast.classList.remove('translate-y-[-100px]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-[-100px]', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }
    </script>
</body>
</html>
