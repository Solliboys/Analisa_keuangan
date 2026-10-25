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
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col pb-24">

    <!-- Sticky Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('pengajuan') ?>" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition" title="Kembali ke Daftar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base font-bold text-slate-900 tracking-tight">Credit Application & Memo</span>
                            <span id="badgeNomorAplikasi" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <?= esc($nomor_aplikasi) ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-500">Evaluasi Pembiayaan & Analisa Risiko Terpadu</span>
                    </div>
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
                    <button type="button" onclick="simpanForm(false)" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Sub-Navbar: 13 Workflow Steps -->
    <div class="bg-white border-b border-slate-200 overflow-x-auto shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center gap-2 whitespace-nowrap" id="tabNavContainer">
            <button type="button" onclick="goToTab(1)" id="step-btn-1" class="step-tab active px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">1</span>
                Aplikasi & Plafon
            </button>
            <button type="button" onclick="goToTab(2)" id="step-btn-2" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">2</span>
                Data Debitur
            </button>
            <button type="button" onclick="goToTab(3)" id="step-btn-3" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">3</span>
                Profil Usaha
            </button>
            <button type="button" onclick="goToTab(4)" id="step-btn-4" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">4</span>
                Agunan & Jaminan
            </button>
            <button type="button" onclick="goToTab(5)" id="step-btn-5" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">5</span>
                Dokumen Berkas
            </button>
            <button type="button" onclick="goToTab(6)" id="step-btn-6" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">6</span>
                SLIK OJK
            </button>
            <button type="button" onclick="goToTab(7)" id="step-btn-7" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">7</span>
                Web & Legal Check
            </button>
            <button type="button" onclick="goToTab(8)" id="step-btn-8" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">8</span>
                Analisa & AI
            </button>
            <button type="button" onclick="goToTab(9)" id="step-btn-9" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">9</span>
                Rekening Koran
            </button>
            <button type="button" onclick="goToTab(10)" id="step-btn-10" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">10</span>
                Laba Rugi
            </button>
            <button type="button" onclick="goToTab(11)" id="step-btn-11" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">11</span>
                Repayment & DSC
            </button>
            <button type="button" onclick="goToTab(12)" id="step-btn-12" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">12</span>
                Pencairan
            </button>
            <button type="button" onclick="goToTab(13)" id="step-btn-13" class="step-tab px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition flex items-center gap-1.5">
                <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-[10px] font-bold">13</span>
                Komite Approval
            </button>
        </div>
    </div>

    <!-- Main Wizard Form -->
    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1">
        
        <form id="camForm" onsubmit="event.preventDefault(); simpanForm(false);">
            <!-- Hidden Identification Fields -->
            <input type="hidden" name="kredit_id" id="kredit_id" value="<?= esc($kredit_id ?? '') ?>">
            <input type="hidden" name="nomor_aplikasi" id="nomor_aplikasi" value="<?= esc($nomor_aplikasi) ?>">

            <!-- ==========================================
                 TAB 1: APLIKASI & PLAFON PINJAMAN
            =========================================== -->
            <div id="tab-1" class="tab-panel active bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">1. Data Permohonan Kredit & Plafon</h2>
                    <p class="text-sm text-slate-500">Tentukan skema fasilitas, plafon pinjaman, tenor, suku bunga, dan kalkulasi angsuran.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Aplikasi</label>
                        <select name="tipe_aplikasi" class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $ta = $kredit['tipe_aplikasi'] ?? 'Fasilitas Baru'; ?>
                            <option value="Fasilitas Baru" <?= $ta === 'Fasilitas Baru' ? 'selected' : '' ?>>Fasilitas Baru</option>
                            <option value="Perpanjangan" <?= $ta === 'Perpanjangan' ? 'selected' : '' ?>>Perpanjangan</option>
                            <option value="Top Up" <?= $ta === 'Top Up' ? 'selected' : '' ?>>Top Up</option>
                            <option value="Restrukturisasi" <?= $ta === 'Restrukturisasi' ? 'selected' : '' ?>>Restrukturisasi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Debitur</label>
                        <select name="jenis_debitur" class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $jd = $kredit['jenis_debitur'] ?? 'Individu'; ?>
                            <option value="Individu" <?= $jd === 'Individu' ? 'selected' : '' ?>>Individu / Perorangan</option>
                            <option value="Badan Usaha (PT)" <?= $jd === 'Badan Usaha (PT)' ? 'selected' : '' ?>>Badan Usaha (PT)</option>
                            <option value="Badan Usaha (CV)" <?= $jd === 'Badan Usaha (CV)' ? 'selected' : '' ?>>Badan Usaha (CV)</option>
                            <option value="Koperasi / Yayasan" <?= $jd === 'Koperasi / Yayasan' ? 'selected' : '' ?>>Koperasi / Yayasan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tujuan Pembiayaan</label>
                        <select name="tujuan_pembiayaan" class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $tp = $kredit['tujuan_pembiayaan'] ?? 'Modal Kerja'; ?>
                            <option value="Modal Kerja" <?= $tp === 'Modal Kerja' ? 'selected' : '' ?>>Modal Kerja</option>
                            <option value="Investasi" <?= $tp === 'Investasi' ? 'selected' : '' ?>>Investasi</option>
                            <option value="Konsumtif / Multiguna" <?= $tp === 'Konsumtif / Multiguna' ? 'selected' : '' ?>>Konsumtif / Multiguna</option>
                            <option value="Take Over" <?= $tp === 'Take Over' ? 'selected' : '' ?>>Take Over</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Plafon Diajukan (Rp)</label>
                        <input type="text" name="plafon_pengajuan" id="plafon_pengajuan" 
                               value="<?= number_format((float)($kredit['plafon_pengajuan'] ?? 500000000), 0, ',', '.') ?>" 
                               class="w-full text-sm font-semibold text-slate-900 border border-slate-300 rounded-lg p-2.5 bg-indigo-50/30 focus:ring-2 focus:ring-indigo-500 rupiah-input"
                               oninput="formatRupiah(this); hitungKalkulasiAngsuran(); hitungCoverageAgunan(); hitungDisbursement();">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tenor (Bulan)</label>
                        <input type="number" name="tenor_bulan" id="tenor_bulan" 
                               value="<?= esc($kredit['tenor_bulan'] ?? 12) ?>" min="1" max="120"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"
                               oninput="hitungKalkulasiAngsuran();">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Jaminan</label>
                        <select name="jenis_jaminan" class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $jj = $kredit['jenis_jaminan'] ?? 'On Hand'; ?>
                            <option value="On Hand" <?= $jj === 'On Hand' ? 'selected' : '' ?>>On Hand (SHM / SHGB / BPKB)</option>
                            <option value="Take Over" <?= $jj === 'Take Over' ? 'selected' : '' ?>>Take Over Bank Lain</option>
                            <option value="Pari Passu" <?= $jj === 'Pari Passu' ? 'selected' : '' ?>>Pari Passu</option>
                            <option value="Tanpa Jaminan" <?= $jj === 'Tanpa Jaminan' ? 'selected' : '' ?>>Tanpa Jaminan (Clean)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Skema Pembayaran</label>
                        <select name="skema_pembayaran" id="skema_pembayaran" 
                                class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500"
                                onchange="hitungKalkulasiAngsuran();">
                            <?php $sp = $kredit['skema_pembayaran'] ?? 'Balloon Payment'; ?>
                            <option value="Balloon Payment" <?= $sp === 'Balloon Payment' ? 'selected' : '' ?>>Balloon Payment (Bunga Tiap Bulan, Pokok di Akhir)</option>
                            <option value="Installment" <?= $sp === 'Installment' ? 'selected' : '' ?>>Installment (Pokok + Bunga Tiap Bulan)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bunga per Bulan (%)</label>
                        <input type="number" step="0.01" name="bunga_per_bulan" id="bunga_per_bulan" 
                               value="<?= esc($kredit['bunga_per_bulan'] ?? 3) ?>" 
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"
                               oninput="syncBunga('bulan'); hitungKalkulasiAngsuran();">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bunga per Tahun (Eff/p.a %)</label>
                        <input type="number" step="0.01" name="bunga_per_tahun" id="bunga_per_tahun" 
                               value="<?= esc($kredit['bunga_per_tahun'] ?? 36) ?>" 
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"
                               oninput="syncBunga('tahun'); hitungKalkulasiAngsuran();">
                    </div>
                </div>

                <!-- Live Estimasi Box -->
                <div class="mt-8 p-5 bg-gradient-to-r from-indigo-50 via-slate-50 to-emerald-50 rounded-2xl border border-indigo-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Kalkulasi Simulasi Angsuran</span>
                        <div class="text-sm text-slate-600 mt-0.5">Dihitung otomatis berdasarkan plafon, tenor, dan suku bunga.</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-6">
                        <div class="text-right">
                            <div class="text-xs text-slate-500">Estimasi Balloon (Bunga/bln)</div>
                            <div id="boxAngsuranBalloon" class="text-lg font-extrabold text-indigo-700">Rp 0</div>
                        </div>
                        <div class="h-8 w-px bg-slate-300 hidden sm:block"></div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500">Estimasi Installment (Pokok+Bunga)</div>
                            <div id="boxAngsuranInstallment" class="text-lg font-extrabold text-emerald-700">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 2: DATA DEBITUR
            =========================================== -->
            <div id="tab-2" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">2. Informasi Pribadi Calon Debitur</h2>
                    <p class="text-sm text-slate-500">Data identitas nasabah pemohon sesuai KTP dan verifikasi kependudukan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap (Sesuai KTP)</label>
                        <input type="text" name="nama_debitur" id="nama_debitur" value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                               placeholder="Nama lengkap tanpa gelar singkatan"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tempat, Tanggal Lahir</label>
                        <input type="text" name="tempat_tanggal_lahir" value="<?= esc($kredit['tempat_tanggal_lahir'] ?? '') ?>" 
                               placeholder="Contoh: Surabaya, 12 Mei 1985"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat KTP Lengkap</label>
                        <textarea name="alamat_ktp" rows="2" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kab, Provinsi"
                                  class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"><?= esc($kredit['alamat_ktp'] ?? '') ?></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Domisili Sekarang</label>
                        <textarea name="alamat_domisili" rows="2" placeholder="Kosongkan jika sama dengan alamat KTP"
                                  class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"><?= esc($kredit['alamat_domisili'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kepemilikan Rumah Domisili</label>
                        <select name="kepemilikan_domisili" class="w-full text-sm border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $kd = $kredit['kepemilikan_domisili'] ?? 'Milik Sendiri'; ?>
                            <option value="Milik Sendiri" <?= $kd === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                            <option value="Milik Keluarga / Orang Tua" <?= $kd === 'Milik Keluarga / Orang Tua' ? 'selected' : '' ?>>Milik Keluarga / Orang Tua</option>
                            <option value="Sewa / Kontrak" <?= $kd === 'Sewa / Kontrak' ? 'selected' : '' ?>>Sewa / Kontrak</option>
                            <option value="Rumah Dinas" <?= $kd === 'Rumah Dinas' ? 'selected' : '' ?>>Rumah Dinas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor HP / WhatsApp Aktif</label>
                        <input type="text" name="nomor_hp" value="<?= esc($kredit['nomor_hp'] ?? '') ?>" placeholder="081234567xxx"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Emergency / Kontak Darurat</label>
                        <input type="text" name="nomor_emergency" value="<?= esc($kredit['nomor_emergency'] ?? '') ?>" placeholder="Nomor kerabat yang dapat dihubungi"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NPWP Pribadi</label>
                        <input type="text" name="npwp_pribadi" value="<?= esc($kredit['npwp_pribadi'] ?? '') ?>" placeholder="16 digit NPWP"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Gadis Ibu Kandung</label>
                        <input type="text" name="nama_ibu_kandung" value="<?= esc($kredit['nama_ibu_kandung'] ?? '') ?>" placeholder="Untuk verifikasi data perbankan"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 3: PROFIL USAHA
            =========================================== -->
            <div id="tab-3" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">3. Profil Kegiatan Usaha & Legalitas</h2>
                    <p class="text-sm text-slate-500">Profil operasional unit usaha penghasil arus kas pembayaran pinjaman.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Usaha / Merk Dagang</label>
                        <input type="text" name="nama_usaha" value="<?= esc($kredit['nama_usaha'] ?? '') ?>" placeholder="Contoh: Toko Berkah / PT Mandiri Sejahtera"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jabatan Pemohon di Usaha</label>
                        <input type="text" name="jabatan" value="<?= esc($kredit['jabatan'] ?? '') ?>" placeholder="Pemilik / Direktur / General Manager"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lama Usaha Berjalan</label>
                        <input type="text" name="lama_usaha" value="<?= esc($kredit['lama_usaha'] ?? '') ?>" placeholder="Contoh: 5 Tahun"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Lokasi Tempat Usaha</label>
                        <textarea name="alamat_usaha" rows="2" placeholder="Alamat fisik workshop/toko/pabrik yang beroperasi"
                                  class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500"><?= esc($kredit['alamat_usaha'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NPWP Usaha / Badan</label>
                        <input type="text" name="npwp_usaha" value="<?= esc($kredit['npwp_usaha'] ?? '') ?>" placeholder="Nomor Pokok Wajib Pajak Usaha"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Induk Berusaha (NIB) / Izin</label>
                        <input type="text" name="nomor_induk_berusaha_nib" value="<?= esc($kredit['nomor_induk_berusaha_nib'] ?? '') ?>" placeholder="NIB OSS RBA"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sumber Utama Pembayaran (Repayment)</label>
                        <input type="text" name="sumber_pembayaran" value="<?= esc($kredit['sumber_pembayaran'] ?? '') ?>" placeholder="Hasil penjualan harian / invoice piutang"
                               class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Dokumen Legalitas Khusus Badan Hukum -->
                    <div class="md:col-span-3 pt-4 border-t border-slate-100">
                        <div class="text-xs font-bold text-indigo-700 uppercase tracking-wider mb-4">Legalitas Akta & Kemenkumham (Untuk PT / CV)</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Akta Pendirian</label>
                                <input type="text" name="akta_pendirian" value="<?= esc($kredit['akta_pendirian'] ?? '') ?>" placeholder="No. Akta Notaris Pendirian"
                                       class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">SK Pengesahan Kemenkumham Pendirian</label>
                                <input type="text" name="sk_pendirian" value="<?= esc($kredit['sk_pendirian'] ?? '') ?>" placeholder="No. AHU-xxxx.AH.01.01"
                                       class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Nomor Akta Perubahan Terakhir</label>
                                <input type="text" name="akta_perubahan" value="<?= esc($kredit['akta_perubahan'] ?? '') ?>" placeholder="No. Akta Perubahan Pengurus/Modal"
                                       class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">SK Penerimaan Kemenkumham Perubahan</label>
                                <input type="text" name="sk_perubahan" value="<?= esc($kredit['sk_perubahan'] ?? '') ?>" placeholder="No. AHU Perubahan Terakhir"
                                       class="w-full text-sm border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 4: AGUNAN & JAMINAN
            =========================================== -->
            <div id="tab-4" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">4. Penilaian Agunan & Agunan Coverage</h2>
                        <p class="text-sm text-slate-500">Detail aset jaminan, nilai pasar (MV), likuidasi (LV), dan rasio penutupan terhadap plafon.</p>
                    </div>
                    <button type="button" onclick="tambahBarisAgunan()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Tambah Agunan
                    </button>
                </div>

                <div class="space-y-6" id="containerAgunan">
                    <?php if (empty($agunan)): ?>
                        <!-- Default 1 Baris Agunan -->
                        <div class="agunan-item p-5 bg-slate-50 rounded-2xl border border-slate-200 relative">
                            <button type="button" onclick="hapusBarisAgunan(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Objek Jaminan</label>
                                    <input type="text" name="agunan[0][alamat_agunan]" placeholder="Alamat lokasi aset jaminan" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Dokumen Agunan</label>
                                    <select name="agunan[0][jenis_agunan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                        <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                        <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                        <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                        <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                        <option value="Bilyet Deposito">Bilyet Deposito</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Sertifikat / BPKB</label>
                                    <input type="text" name="agunan[0][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Tanah (m²)</label>
                                    <input type="number" name="agunan[0][luas_tanah_m2]" placeholder="LT m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Bangunan (m²)</label>
                                    <input type="number" name="agunan[0][luas_bangunan_m2]" placeholder="LB m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status IMB / PBG</label>
                                    <select name="agunan[0][status_imb]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Proses Pengurusan">Proses Pengurusan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pemilik Agunan</label>
                                    <input type="text" name="agunan[0][pemilik_agunan]" placeholder="Sesuai nama di sertifikat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hubungan Kepemilikan</label>
                                    <select name="agunan[0][hubungan_kepemilikan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                        <option value="Milik Sendiri">Milik Sendiri</option>
                                        <option value="Suami / Istri">Suami / Istri</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Pihak Ketiga">Pihak Ketiga</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lama Kepemilikan</label>
                                    <input type="text" name="agunan[0][lama_kepemilikan]" placeholder="Contoh: 8 Tahun" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1">Nilai Pasar / MV (Rp)</label>
                                    <input type="text" name="agunan[0][nilai_pasar_mv]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-bold text-indigo-700 rupiah-input agunan-mv" oninput="formatRupiah(this); hitungCoverageAgunan();">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 mb-1">Likuidasi (% / Nilai LV)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" step="1" name="agunan[0][persen_likuidasi]" value="60" class="w-16 text-xs border border-slate-300 rounded-lg p-2.5 bg-white agunan-lv-pct" oninput="hitungCoverageAgunan();">
                                        <span class="text-xs text-slate-500 font-semibold">%</span>
                                        <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($agunan as $idx => $row): ?>
                            <div class="agunan-item p-5 bg-slate-50 rounded-2xl border border-slate-200 relative">
                                <button type="button" onclick="hapusBarisAgunan(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Objek Jaminan</label>
                                        <input type="text" name="agunan[<?= $idx ?>][alamat_agunan]" value="<?= esc($row['alamat_agunan'] ?? '') ?>" placeholder="Alamat lokasi aset jaminan" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Dokumen Agunan</label>
                                        <select name="agunan[<?= $idx ?>][jenis_agunan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                            <?php $ja = $row['jenis_agunan'] ?? 'SHM'; ?>
                                            <option value="SHM" <?= $ja === 'SHM' ? 'selected' : '' ?>>SHM (Sertifikat Hak Milik)</option>
                                            <option value="SHGB" <?= $ja === 'SHGB' ? 'selected' : '' ?>>SHGB (Hak Guna Bangunan)</option>
                                            <option value="SHM Sarusun" <?= $ja === 'SHM Sarusun' ? 'selected' : '' ?>>SHM Sarusun (Apartemen)</option>
                                            <option value="BPKB Mobil" <?= $ja === 'BPKB Mobil' ? 'selected' : '' ?>>BPKB Kendaraan Roda 4</option>
                                            <option value="Bilyet Deposito" <?= $ja === 'Bilyet Deposito' ? 'selected' : '' ?>>Bilyet Deposito</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Sertifikat / BPKB</label>
                                        <input type="text" name="agunan[<?= $idx ?>][nomor_sertifikat]" value="<?= esc($row['nomor_sertifikat'] ?? '') ?>" placeholder="No. Sertifikat / BPKB" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Tanah (m²)</label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_tanah_m2]" value="<?= esc($row['luas_tanah_m2'] ?? '') ?>" placeholder="LT m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Bangunan (m²)</label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_bangunan_m2]" value="<?= esc($row['luas_bangunan_m2'] ?? '') ?>" placeholder="LB m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status IMB / PBG</label>
                                        <select name="agunan[<?= $idx ?>][status_imb]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                            <?php $si = $row['status_imb'] ?? 'Ada'; ?>
                                            <option value="Ada" <?= $si === 'Ada' ? 'selected' : '' ?>>Ada</option>
                                            <option value="Tidak Ada" <?= $si === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                                            <option value="Proses Pengurusan" <?= $si === 'Proses Pengurusan' ? 'selected' : '' ?>>Proses Pengurusan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pemilik Agunan</label>
                                        <input type="text" name="agunan[<?= $idx ?>][pemilik_agunan]" value="<?= esc($row['pemilik_agunan'] ?? '') ?>" placeholder="Sesuai nama di sertifikat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hubungan Kepemilikan</label>
                                        <select name="agunan[<?= $idx ?>][hubungan_kepemilikan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                            <?php $hk = $row['hubungan_kepemilikan'] ?? 'Milik Sendiri'; ?>
                                            <option value="Milik Sendiri" <?= $hk === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                            <option value="Suami / Istri" <?= $hk === 'Suami / Istri' ? 'selected' : '' ?>>Suami / Istri</option>
                                            <option value="Orang Tua" <?= $hk === 'Orang Tua' ? 'selected' : '' ?>>Orang Tua</option>
                                            <option value="Pihak Ketiga" <?= $hk === 'Pihak Ketiga' ? 'selected' : '' ?>>Pihak Ketiga</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Lama Kepemilikan</label>
                                        <input type="text" name="agunan[<?= $idx ?>][lama_kepemilikan]" value="<?= esc($row['lama_kepemilikan'] ?? '') ?>" placeholder="Contoh: 8 Tahun" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800 mb-1">Nilai Pasar / MV (Rp)</label>
                                        <input type="text" name="agunan[<?= $idx ?>][nilai_pasar_mv]" 
                                               value="<?= number_format((float)($row['nilai_pasar_mv'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-bold text-indigo-700 rupiah-input agunan-mv" 
                                               oninput="formatRupiah(this); hitungCoverageAgunan();">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800 mb-1">Likuidasi (% / Nilai LV)</label>
                                        <div class="flex items-center gap-2">
                                            <input type="number" step="1" name="agunan[<?= $idx ?>][persen_likuidasi]" 
                                                   value="<?= esc($row['persen_likuidasi'] ?? 60) ?>" 
                                                   class="w-16 text-xs border border-slate-300 rounded-lg p-2.5 bg-white agunan-lv-pct" 
                                                   oninput="hitungCoverageAgunan();">
                                            <span class="text-xs text-slate-500 font-semibold">%</span>
                                            <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">
                                                Rp <?= number_format((float)($row['nilai_likuidasi_lv'] ?? 0), 0, ',', '.') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Aggregated Coverage Box -->
                <div class="mt-8 p-5 bg-slate-900 text-white rounded-2xl flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                    <div>
                        <div class="text-xs font-bold text-indigo-300 uppercase tracking-wider">Total Evaluasi Nilai Agunan</div>
                        <div class="text-xs text-slate-400 mt-0.5">Penetapan rasio penutupan jaminan terhadap total plafon pinjaman.</div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-right">
                        <div>
                            <div class="text-[11px] text-slate-400">Total Nilai Pasar (MV)</div>
                            <div id="totalAgunanMV" class="text-sm font-bold text-white">Rp 0</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Total Likuidasi (LV)</div>
                            <div id="totalAgunanLV" class="text-sm font-bold text-emerald-400">Rp 0</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Coverage MV</div>
                            <div id="coverageMV" class="text-base font-extrabold text-indigo-400">0%</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Coverage LV</div>
                            <div id="coverageLV" class="text-base font-extrabold text-emerald-400">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 5: UPLOAD DOKUMEN PERSYARATAN
            =========================================== -->
            <div id="tab-5" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">5. Dokumen Persyaratan & Berkas Legalitas</h2>
                    <p class="text-sm text-slate-500">Unggah file dokumen fisik pendukung pengajuan analisa (PDF, JPG, PNG).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Dropzone Box -->
                    <div class="p-6 border-2 border-dashed border-indigo-200 rounded-2xl bg-indigo-50/20 text-center flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pilih Dokumen untuk Diunggah</h4>
                        <p class="text-xs text-slate-500 mt-1 mb-4">Format disarankan: PDF, JPG, PNG maks 10MB</p>
                        
                        <div class="w-full mb-3 text-left">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Dokumen</label>
                            <select id="kategoriUpload" class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white">
                                <option value="KTP & NPWP">KTP & NPWP Debitur / Pasangan</option>
                                <option value="Sertifikat Agunan">Sertifikat Agunan / PBB / IMB</option>
                                <option value="Rekening Koran">Rekening Koran 3-6 Bulan Terakhir</option>
                                <option value="Legalitas Usaha">NIB / SIUP / TDP / Akta Perusahaan</option>
                                <option value="Laporan Keuangan">Laporan Keuangan / Pembukuan Usaha</option>
                                <option value="Foto Usaha & Jaminan">Foto Lokasi Usaha & Agunan</option>
                                <option value="Lainnya">Dokumen Pendukung Lainnya</option>
                            </select>
                        </div>

                        <input type="file" id="fileUploadInput" class="hidden" onchange="prosesUploadDokumen(this)">
                        <button type="button" onclick="document.getElementById('fileUploadInput').click()" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                            Pilih File dari Komputer
                        </button>
                    </div>

                    <!-- List Uploaded Docs -->
                    <div class="md:col-span-2">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Daftar Dokumen yang Telah Diunggah</h4>
                        <div id="listDokumenTerunggah" class="space-y-3">
                            <?php if (empty($dokumen)): ?>
                                <div class="text-center py-10 border border-slate-200 rounded-2xl bg-slate-50 text-slate-400 text-xs">
                                    Belum ada berkas yang diunggah untuk pengajuan ini.
                                </div>
                            <?php else: ?>
                                <?php foreach ($dokumen as $doc): ?>
                                    <div class="p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between text-xs hover:border-indigo-300 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg font-bold">PDF</div>
                                            <div>
                                                <div class="font-semibold text-slate-800"><?= esc($doc['nama_file_asli']) ?></div>
                                                <div class="text-[11px] text-slate-400"><?= esc($doc['kategori_dokumen']) ?> &bull; <?= number_format($doc['ukuran_file_bytes'] / 1024, 1) ?> KB</div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url($doc['path_file']) ?>" target="_blank" class="px-2.5 py-1 text-indigo-600 hover:text-indigo-800 font-semibold border border-indigo-200 rounded-md">
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
            <div id="tab-6" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">6. Riwayat Fasilitas Kredit (SLIK OJK)</h2>
                        <p class="text-sm text-slate-500">Pencatatan fasilitas pinjaman aktif di bank atau lembaga keuangan lain.</p>
                    </div>
                    <button type="button" onclick="tambahBarisSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Tambah Baris SLIK
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" id="tabelSlik">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="p-3">Nama Debitur / Pemilik Fasilitas</th>
                                <th class="p-3">Bank / Lembaga</th>
                                <th class="p-3">Plafon (Rp)</th>
                                <th class="p-3">Baki Debet / Outstanding (Rp)</th>
                                <th class="p-3">Kualitas / Kol</th>
                                <th class="p-3">Keterangan / Tujuan</th>
                                <th class="p-3 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="bodySlik">
                            <?php if (empty($slik)): ?>
                                <tr>
                                    <td class="p-2"><input type="text" name="slik[0][nama_debitur]" placeholder="Debitur / Pasangan" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                    <td class="p-2"><input type="text" name="slik[0][bank_lembaga]" placeholder="Nama Bank / Leasing" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                    <td class="p-2"><input type="text" name="slik[0][plafon]" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input" oninput="formatRupiah(this)"></td>
                                    <td class="p-2"><input type="text" name="slik[0][outstanding]" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input slik-outstanding" oninput="formatRupiah(this); hitungTotalSlik();"></td>
                                    <td class="p-2">
                                        <select name="slik[0][kelancaran]" class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white">
                                            <option value="Lancar">Kol 1 (Lancar)</option>
                                            <option value="DPK">Kol 2 (DPK / Dalam Perhatian Khusus)</option>
                                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                                            <option value="Macet">Kol 5 (Macet)</option>
                                        </select>
                                    </td>
                                    <td class="p-2"><input type="text" name="slik[0][keterangan]" placeholder="Kredit Modal Kerja / KPR / dll" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                    <td class="p-2 text-center"><button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slik as $idx => $s): ?>
                                    <tr>
                                        <td class="p-2"><input type="text" name="slik[<?= $idx ?>][nama_debitur]" value="<?= esc($s['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                        <td class="p-2"><input type="text" name="slik[<?= $idx ?>][bank_lembaga]" value="<?= esc($s['bank_lembaga'] ?? '') ?>" placeholder="Nama Bank / Leasing" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                        <td class="p-2"><input type="text" name="slik[<?= $idx ?>][plafon]" value="<?= number_format((float)($s['plafon'] ?? 0), 0, ',', '.') ?>" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input" oninput="formatRupiah(this)"></td>
                                        <td class="p-2"><input type="text" name="slik[<?= $idx ?>][outstanding]" value="<?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?>" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input slik-outstanding" oninput="formatRupiah(this); hitungTotalSlik();"></td>
                                        <td class="p-2">
                                            <select name="slik[<?= $idx ?>][kelancaran]" class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white">
                                                <?php $kl = $s['kelancaran'] ?? 'Lancar'; ?>
                                                <option value="Lancar" <?= $kl === 'Lancar' ? 'selected' : '' ?>>Kol 1 (Lancar)</option>
                                                <option value="DPK" <?= $kl === 'DPK' ? 'selected' : '' ?>>Kol 2 (DPK / Dalam Perhatian Khusus)</option>
                                                <option value="Kurang Lancar" <?= $kl === 'Kurang Lancar' ? 'selected' : '' ?>>Kol 3 (Kurang Lancar)</option>
                                                <option value="Diragukan" <?= $kl === 'Diragukan' ? 'selected' : '' ?>>Kol 4 (Diragukan)</option>
                                                <option value="Macet" <?= $kl === 'Macet' ? 'selected' : '' ?>>Kol 5 (Macet)</option>
                                            </select>
                                        </td>
                                        <td class="p-2"><input type="text" name="slik[<?= $idx ?>][keterangan]" value="<?= esc($s['keterangan'] ?? '') ?>" placeholder="Kredit Modal Kerja / KPR / dll" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                                        <td class="p-2 text-center"><button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="p-3 text-right text-slate-700">Total Baki Debet Terhutang:</td>
                                <td class="p-3 text-indigo-700 font-extrabold" id="totalOutstandingSlik">Rp 0</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ringkasan & Catatan Historis SLIK OJK</label>
                    <textarea name="ringkasan_slik" rows="3" placeholder="Tuliskan kesimpulan profil karakter kredit nasabah, riwayat tunggakan (jika ada), serta rencana take over / pelunasan..."
                              class="w-full text-xs border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500"><?= esc($kredit['ringkasan_slik'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- ==========================================
                 TAB 7: WEB & LEGAL CHECK
            =========================================== -->
            <div id="tab-7" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">7. Verifikasi Latar Belakang & Legal Check</h2>
                    <p class="text-sm text-slate-500">Pemeriksaan profil reputasi debitur pada kanal publik, peradilan, dan daftar hitam.</p>
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
            <div id="tab-8" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">8. Analisa Bisnis & Rekomendasi AI Engine</h2>
                    <p class="text-sm text-slate-500">Evaluasi menyeluruh 5C dan modul kecerdasan buatan untuk rekomendasi keputusan.</p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Analisa Kualitas & Marketability Jaminan</label>
                        <textarea name="analisa_jaminan" rows="3" placeholder="Lokasi strategis, akses jalan, kondisi fisik bangunan, dan kemudahan penjualan kembali..."
                                  class="w-full text-xs border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500"><?= esc($analisa['analisa_jaminan'] ?? '') ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Business Review & Prospek Pasar Usaha</label>
                        <textarea name="business_review" rows="3" placeholder="Model bisnis, segmen pelanggan, daya saing kompetitor, stabilitas pasokan bahan baku..."
                                  class="w-full text-xs border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500"><?= esc($analisa['business_review'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Kunjungan / On The Spot (OTS)</label>
                            <textarea name="business_catatan" rows="3" placeholder="Hasil observasi fisik di lokasi usaha nasabah..."
                                      class="w-full text-xs border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500"><?= esc($analisa['business_catatan'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rincian Kebutuhan Modal Kerja</label>
                            <textarea name="detail_kebutuhan" rows="3" placeholder="Alokasi dana pinjaman yang diajukan..."
                                      class="w-full text-xs border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500"><?= esc($analisa['detail_kebutuhan'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- AI Scoring & Recommendation Card -->
                    <div class="p-6 bg-gradient-to-br from-indigo-900 to-slate-900 text-white rounded-2xl border border-indigo-800 shadow-md">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-indigo-700/50 pb-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-500/20 border border-indigo-400 flex items-center justify-center text-indigo-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white tracking-wide">AI Credit Underwriting Assistant</h4>
                                    <p class="text-xs text-indigo-200">Model penilaian berbasis profil 5C (Character, Capacity, Capital, Collateral, Condition).</p>
                                </div>
                            </div>
                            <button type="button" onclick="jalankanAnalisaAI()" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-400 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Analisis Rekomendasi AI
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[11px] font-semibold text-indigo-300 uppercase tracking-wider mb-2">Skor Kelayakan AI (0 - 100)</label>
                                <input type="number" name="skor_kelayakan_ai" id="skor_kelayakan_ai" 
                                       value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>" min="0" max="100"
                                       class="w-full text-base font-bold text-center bg-white/10 border border-indigo-400/30 rounded-xl p-2.5 text-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-indigo-300 uppercase tracking-wider mb-2">Rekomendasi Keputusan AI</label>
                                <select name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" class="w-full text-xs font-bold bg-slate-800 border border-indigo-400/30 rounded-xl p-2.5 text-white">
                                    <?php $rk = $analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh'; ?>
                                    <option value="Disetujui Penuh" <?= $rk === 'Disetujui Penuh' ? 'selected' : '' ?>>Disetujui Penuh</option>
                                    <option value="Disetujui dengan Penurunan Plafon" <?= $rk === 'Disetujui dengan Penurunan Plafon' ? 'selected' : '' ?>>Disetujui dengan Penurunan Plafon</option>
                                    <option value="Pertimbangan Khusus" <?= $rk === 'Pertimbangan Khusus' ? 'selected' : '' ?>>Pertimbangan Khusus / Tambah Jaminan</option>
                                    <option value="Tolak" <?= $rk === 'Tolak' ? 'selected' : '' ?>>Tolak / Berisiko Tinggi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-indigo-300 uppercase tracking-wider mb-2">Rekomendasi Plafon AI (Rp)</label>
                                <input type="text" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" 
                                       value="<?= number_format((float)($analisa['rekomendasi_plafon_ai'] ?? 500000000), 0, ',', '.') ?>" 
                                       class="w-full text-xs font-bold bg-white/10 border border-indigo-400/30 rounded-xl p-2.5 text-white rupiah-input" oninput="formatRupiah(this)">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-semibold text-indigo-300 uppercase tracking-wider mb-2">Analisis Risiko & Mitigasi AI</label>
                                <textarea name="analisis_risiko_ai" id="analisis_risiko_ai" rows="3" class="w-full text-xs bg-slate-800 border border-indigo-400/30 rounded-xl p-3 text-indigo-100"><?= esc($analisa['analisis_risiko_ai'] ?? 'Profil usaha menunjukkan perputaran kas positif dengan rasio DSC di atas batas aman. Agunan memiliki nilai pasar dan legalitas kuat yang mampu memitigasi risiko gagal bayar.') ?></textarea>
                            </div>
                        </div>

                        <!-- Banner Laporan PDF Siap Dicetak -->
                        <div id="aiPdfBanner" class="mt-5 p-4 bg-emerald-500/15 border border-emerald-400/30 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm">
                                    &check;
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-emerald-300" id="aiSumberLabel">Model Evaluasi Gemini AI Aktif</div>
                                    <div class="text-[11px] text-slate-300">Hasil analisa komprehensif dapat langsung dicetak sebagai Laporan Memo Kredit resmi (PDF).</div>
                                </div>
                            </div>
                            <button type="button" onclick="bukaLaporanPDF()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm whitespace-nowrap">
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
            <div id="tab-9" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">9. Rekapitulasi Mutasi Rekening Koran</h2>
                        <p class="text-sm text-slate-500">Catat perputaran arus kas masuk (kredit) dan keluar (debit) untuk analisa omzet riil.</p>
                    </div>
                    <button type="button" onclick="tambahBarisRekening()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Tambah Rekening Bank
                    </button>
                </div>

                <div class="space-y-4" id="containerRekening">
                    <?php if (empty($rekening)): ?>
                        <!-- Default 1 Baris Rekening -->
                        <div class="rekening-item p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                            <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bank</label>
                                    <input type="text" name="rekening[0][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Mutasi</label>
                                    <input type="text" name="rekening[0][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Rekening</label>
                                    <input type="text" name="rekening[0][nomor_rekening]" placeholder="Nomor rekening bank" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Atas Nama Rekening</label>
                                    <input type="text" name="rekening[0][atas_nama]" placeholder="Nama pemilik rekening" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Kredit / Masuk (Rp)</label>
                                    <input type="text" name="rekening[0][total_mutasi_kredit]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-indigo-700 rupiah-input rek-kredit" oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Debit / Keluar (Rp)</label>
                                    <input type="text" name="rekening[0][total_mutasi_debit]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-slate-700 rupiah-input rek-debit" oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Bulan Mutasi</label>
                                    <input type="number" name="rekening[0][jumlah_bulan]" value="6" min="1" max="24" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white rek-bulan" oninput="hitungRataRataRekening();">
                                </div>
                                <div class="flex items-center gap-4 pt-4">
                                    <div>
                                        <div class="text-[10px] text-slate-500 font-semibold uppercase">Rata-rata Kredit/Bulan:</div>
                                        <div class="text-xs font-bold text-emerald-700 rek-avg-kredit">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($rekening as $idx => $r): ?>
                            <div class="rekening-item p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                                <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bank</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nama_bank]" value="<?= esc($r['nama_bank'] ?? '') ?>" placeholder="Contoh: BCA / Mandiri / BRI" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Mutasi</label>
                                        <input type="text" name="rekening[<?= $idx ?>][periode]" value="<?= esc($r['periode'] ?? '') ?>" placeholder="Contoh: Jan 2026 - Jun 2026" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nomor_rekening]" value="<?= esc($r['nomor_rekening'] ?? '') ?>" placeholder="Nomor rekening bank" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Atas Nama Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][atas_nama]" value="<?= esc($r['atas_nama'] ?? '') ?>" placeholder="Nama pemilik rekening" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Kredit / Masuk (Rp)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_kredit]" 
                                               value="<?= number_format((float)($r['total_mutasi_kredit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-indigo-700 rupiah-input rek-kredit" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Debit / Keluar (Rp)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_debit]" 
                                               value="<?= number_format((float)($r['total_mutasi_debit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-slate-700 rupiah-input rek-debit" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Bulan Mutasi</label>
                                        <input type="number" name="rekening[<?= $idx ?>][jumlah_bulan]" 
                                               value="<?= esc($r['jumlah_bulan'] ?? 6) ?>" min="1" max="24" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white rek-bulan" 
                                               oninput="hitungRataRataRekening();">
                                    </div>
                                    <div class="flex items-center gap-4 pt-4">
                                        <div>
                                            <div class="text-[10px] text-slate-500 font-semibold uppercase">Rata-rata Kredit/Bulan:</div>
                                            <div class="text-xs font-bold text-emerald-700 rek-avg-kredit">
                                                Rp <?= number_format((float)($r['rata_rata_kredit'] ?? 0), 0, ',', '.') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Rekap Mutasi Seluruh Bank -->
                <div class="mt-8 p-5 bg-indigo-50 border border-indigo-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <div class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Total Omzet Arus Kas Mutasi Kredit</div>
                        <div class="text-xs text-indigo-700">Dapat disalin langsung ke input Omzet Rekening Koran di Tab Laba Rugi.</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-[11px] text-slate-500">Rata-rata Gabungan / Bulan</div>
                            <div id="totalAvgKreditGabungan" class="text-base font-extrabold text-indigo-700">Rp 0</div>
                        </div>
                        <button type="button" onclick="salinOmzetKeLabaRugi()" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                            Gunakan di Laba Rugi &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 10: ANALISA LABA RUGI
            =========================================== -->
            <div id="tab-10" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">10. Analisa Laba Rugi Komparatif (Rekening Koran vs Wawancara)</h2>
                    <p class="text-sm text-slate-500">Dual track verifikasi profitabilitas debitur berdasarkan bukti perbankan dan hasil wawancara.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Stream 1: Berdasarkan Rekening Koran -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                                <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">Jalur 1: Berdasarkan Rekening Koran</h3>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_rk" id="pendapatan_rk" 
                                           value="<?= number_format((float)($keuangan['pendapatan_rk'] ?? 150000000), 0, ',', '.') ?>" 
                                           class="w-full text-xs font-bold text-indigo-700 border border-slate-300 rounded-lg p-2.5 bg-white rupiah-input"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_rk" id="hpp_persen_rk" 
                                               value="<?= esc($keuangan['hpp_persen_rk'] ?? 60) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal HPP</label>
                                        <div id="nomHppRK" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>

                                <div class="p-3 bg-white border border-slate-200 rounded-xl flex justify-between items-center text-xs">
                                    <span class="font-semibold text-slate-700">Laba Kotor (Gross Profit):</span>
                                    <span id="grossRK" class="font-bold text-slate-900">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Biaya Hidup</label>
                                        <div id="nomBiayaHidupRK" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Beban Lain</label>
                                        <div id="nomBebanLainRK" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-indigo-600 text-white rounded-xl flex items-center justify-between">
                            <span class="text-xs font-semibold">Laba Bersih Operasional (RK):</span>
                            <span id="nettRK" class="text-base font-extrabold">Rp 0</span>
                        </div>
                    </div>

                    <!-- Stream 2: Berdasarkan Hasil Wawancara -->
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                                <h3 class="font-bold text-sm text-slate-900 uppercase tracking-wider">Jalur 2: Berdasarkan Hasil Wawancara</h3>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_iv" id="pendapatan_iv" 
                                           value="<?= number_format((float)($keuangan['pendapatan_iv'] ?? 160000000), 0, ',', '.') ?>" 
                                           class="w-full text-xs font-bold text-emerald-700 border border-slate-300 rounded-lg p-2.5 bg-white rupiah-input"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_iv" id="hpp_persen_iv" 
                                               value="<?= esc($keuangan['hpp_persen_iv'] ?? 60) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal HPP</label>
                                        <div id="nomHppIV" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>

                                <div class="p-3 bg-white border border-slate-200 rounded-xl flex justify-between items-center text-xs">
                                    <span class="font-semibold text-slate-700">Laba Kotor (Gross Profit):</span>
                                    <span id="grossIV" class="font-bold text-slate-900">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_iv" id="biaya_hidup_persen_iv" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Biaya Hidup</label>
                                        <div id="nomBiayaHidupIV" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_iv" id="beban_lainnya_persen_iv" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Beban Lain</label>
                                        <div id="nomBebanLainIV" class="text-xs font-bold text-slate-700 pt-2.5">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-emerald-600 text-white rounded-xl flex items-center justify-between">
                            <span class="text-xs font-semibold">Laba Bersih Operasional (Wawancara):</span>
                            <span id="nettIV" class="text-base font-extrabold">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 11: REPAYMENT CAPACITY & DSC
            =========================================== -->
            <div id="tab-11" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">11. Repayment Capacity & Debt Service Coverage (DSC)</h2>
                    <p class="text-sm text-slate-500">Pengujian kemampuan bayar debitur terhadap angsuran pinjaman (Standar aman DSC &ge; 1.30x).</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Angsuran Balloon Payment</label>
                        <input type="text" name="angsuran_balloon" id="angsuran_balloon" 
                               value="<?= number_format((float)($keuangan['angsuran_balloon'] ?? 0), 0, ',', '.') ?>" 
                               class="w-full text-base font-extrabold text-indigo-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
                        <div class="text-[11px] text-slate-400 mt-1">Pembayaran bunga bulanan</div>
                    </div>

                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Angsuran Installment</label>
                        <input type="text" name="angsuran_installment" id="angsuran_installment" 
                               value="<?= number_format((float)($keuangan['angsuran_installment'] ?? 0), 0, ',', '.') ?>" 
                               class="w-full text-base font-extrabold text-emerald-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
                        <div class="text-[11px] text-slate-400 mt-1">Pokok + Bunga bulanan</div>
                    </div>

                    <div class="p-5 bg-indigo-50 rounded-2xl border border-indigo-200">
                        <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-1">Angsuran yang Dipilih Sebagai Acuan</label>
                        <input type="text" name="angsuran_dipilih" id="angsuran_dipilih" 
                               value="<?= number_format((float)($keuangan['angsuran_dipilih'] ?? 0), 0, ',', '.') ?>" 
                               class="w-full text-base font-extrabold text-indigo-900 bg-white border border-indigo-300 rounded-lg p-2 rupiah-input"
                               oninput="formatRupiah(this); hitungRasioDSC(); hitungDisbursement();">
                        <div class="text-[11px] text-indigo-600 mt-1">Digunakan untuk menghitung rasio DSC dan Hold Angsuran</div>
                    </div>
                </div>

                <!-- Hasil Perhitungan Rasio DSC -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-xs">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rasio DSC (Basis Rekening Koran)</div>
                        <div class="flex items-baseline gap-3 mt-2">
                            <span id="labelRasioDSCRK" class="text-4xl font-extrabold text-slate-900">0.00x</span>
                            <span id="badgeStatusDSCRK" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-2">Rumus: Laba Bersih Rekening Koran / Angsuran Acuan</div>
                    </div>

                    <div class="p-6 rounded-2xl border border-slate-200 bg-white shadow-xs">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rasio DSC (Basis Wawancara)</div>
                        <div class="flex items-baseline gap-3 mt-2">
                            <span id="labelRasioDSCIV" class="text-4xl font-extrabold text-slate-900">0.00x</span>
                            <span id="badgeStatusDSCIV" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-2">Rumus: Laba Bersih Wawancara / Angsuran Acuan</div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 12: SIMULASI PENCAIRAN (DISBURSEMENT)
            =========================================== -->
            <div id="tab-12" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h2 class="text-xl font-bold text-slate-900">12. Simulasi Biaya & Pencairan Bersih (Disbursement)</h2>
                    <p class="text-sm text-slate-500">Rincian seluruh komponen biaya potong muka dan dana riil yang diterima debitur saat akad.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left: Input Biaya -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Provisi (%)</label>
                                <input type="number" step="0.1" name="provisi_persen" id="provisi_persen" 
                                       value="<?= esc($disbursement['provisi_persen'] ?? 7) ?>" 
                                       class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white" oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Provisi</label>
                                <div id="nomProvisi" class="text-xs font-bold text-slate-800 pt-2.5">Rp 0</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Agent Fee (%)</label>
                                <input type="number" step="0.1" name="agent_persen" id="agent_persen" 
                                       value="<?= esc($disbursement['agent_persen'] ?? 1) ?>" 
                                       class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white" oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Agent Fee</label>
                                <div id="nomAgent" class="text-xs font-bold text-slate-800 pt-2.5">Rp 0</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Admin Fee (%)</label>
                                <input type="number" step="0.1" name="admin_persen" id="admin_persen" 
                                       value="<?= esc($disbursement['admin_persen'] ?? 1) ?>" 
                                       class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white" oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Admin Fee</label>
                                <div id="nomAdmin" class="text-xs font-bold text-slate-800 pt-2.5">Rp 0</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Hold Angsuran (Bulan)</label>
                                <input type="number" name="hold_angsuran_bulan" id="hold_angsuran_bulan" 
                                       value="<?= esc($disbursement['hold_angsuran_bulan'] ?? 4) ?>" min="0" max="24"
                                       class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white" oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Hold Angsuran</label>
                                <div id="nomHoldAngsuran" class="text-xs font-bold text-slate-800 pt-2.5">Rp 0</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Premi Asuransi Jiwa (Rp)</label>
                            <input type="text" name="asuransi_jiwa" id="asuransi_jiwa" 
                                   value="<?= number_format((float)($disbursement['asuransi_jiwa'] ?? 0), 0, ',', '.') ?>" 
                                   class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white rupiah-input" oninput="formatRupiah(this); hitungDisbursement();">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Premi Asuransi Kebakaran / Agunan (Rp)</label>
                            <input type="text" name="asuransi_kebakaran" id="asuransi_kebakaran" 
                                   value="<?= number_format((float)($disbursement['asuransi_kebakaran'] ?? 0), 0, ',', '.') ?>" 
                                   class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white rupiah-input" oninput="formatRupiah(this); hitungDisbursement();">
                        </div>
                    </div>

                    <!-- Right: Summary Box -->
                    <div class="p-6 bg-slate-900 text-white rounded-2xl flex flex-col justify-between shadow-sm">
                        <div>
                            <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Struktur Rekapitulasi Pencairan</span>
                            <div class="mt-6 space-y-3 text-xs border-b border-slate-800 pb-4">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Plafon Pinjaman Pokok:</span>
                                    <span id="disbPlafon" class="font-bold text-white">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Total Biaya & Potongan Muka:</span>
                                    <span id="disbTotalPotongan" class="font-bold text-rose-400">- Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-5 bg-indigo-950/60 border border-indigo-500/40 rounded-xl">
                            <span class="text-xs font-semibold text-indigo-300 uppercase tracking-wider">Estimasi Dana Bersih yang Dicairkan (Nett)</span>
                            <div id="disbDanaBersih" class="text-2xl sm:text-3xl font-extrabold text-emerald-400 mt-2">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 13: KEPUTUSAN KOMITE KREDIT
            =========================================== -->
            <div id="tab-13" class="tab-panel bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
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

                <!-- Status Final Pengajuan -->
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Status Pengajuan Akhir Dokumen CAM</label>
                        <p class="text-xs text-slate-500">Pilih status untuk menentukan alur proses selanjutnya di sistem.</p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select name="status_pengajuan" id="status_pengajuan" class="w-full text-sm font-bold border border-slate-300 rounded-xl p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
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

    </main>

    <!-- Bottom Sticky Navigation Controls -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 py-3 z-20 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <button type="button" onclick="tabNav(-1)" id="btnPrev" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5">
                &larr; Sebelumnya
            </button>
            <div class="flex items-center gap-2">
                <span id="tabStepLabel" class="text-xs font-semibold text-slate-500 hidden sm:inline">Langkah 1 dari 13</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="simpanForm(false)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
                    Simpan Draft
                </button>
                <button type="button" onclick="tabNav(1)" id="btnNext" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
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
        const totalTabs = 13;

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            hitungKalkulasiAngsuran();
            hitungCoverageAgunan();
            hitungTotalSlik();
            hitungRataRataRekening();
            hitungLabaRugi();
            hitungDisbursement();
        });

        // Tab Navigation
        function goToTab(tabIndex) {
            if (tabIndex < 1 || tabIndex > totalTabs) return;
            currentTab = tabIndex;

            // Sembunyikan semua tab panel
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            // Tampilkan tab terpilih
            const target = document.getElementById('tab-' + tabIndex);
            if (target) target.classList.add('active');

            // Update status button nav
            document.querySelectorAll('.step-tab').forEach((btn, idx) => {
                const num = idx + 1;
                btn.classList.remove('active');
                if (num === tabIndex) {
                    btn.classList.add('active');
                } else if (num < tabIndex) {
                    btn.classList.add('completed');
                }
            });

            // Update Label & Button Prev/Next
            document.getElementById('tabStepLabel').innerText = `Langkah ${tabIndex} dari ${totalTabs}`;
            document.getElementById('btnPrev').style.visibility = tabIndex === 1 ? 'hidden' : 'visible';
            document.getElementById('btnNext').innerHTML = tabIndex === totalTabs ? 'Simpan & Selesai &check;' : 'Selanjutnya &rarr;';

            // Scroll ke atas
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
            const html = `
                <div class="agunan-item p-5 bg-slate-50 rounded-2xl border border-slate-200 relative">
                    <button type="button" onclick="hapusBarisAgunan(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Objek Jaminan</label>
                            <input type="text" name="agunan[${count}][alamat_agunan]" placeholder="Alamat lokasi aset jaminan" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Dokumen Agunan</label>
                            <select name="agunan[${count}][jenis_agunan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                <option value="Bilyet Deposito">Bilyet Deposito</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Sertifikat / BPKB</label>
                            <input type="text" name="agunan[${count}][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Tanah (m²)</label>
                            <input type="number" name="agunan[${count}][luas_tanah_m2]" placeholder="LT m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Luas Bangunan (m²)</label>
                            <input type="number" name="agunan[${count}][luas_bangunan_m2]" placeholder="LB m2" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status IMB / PBG</label>
                            <select name="agunan[${count}][status_imb]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                <option value="Ada">Ada</option>
                                <option value="Tidak Ada">Tidak Ada</option>
                                <option value="Proses Pengurusan">Proses Pengurusan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pemilik Agunan</label>
                            <input type="text" name="agunan[${count}][pemilik_agunan]" placeholder="Sesuai nama di sertifikat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Hubungan Kepemilikan</label>
                            <select name="agunan[${count}][hubungan_kepemilikan]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                <option value="Milik Sendiri">Milik Sendiri</option>
                                <option value="Suami / Istri">Suami / Istri</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Pihak Ketiga">Pihak Ketiga</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lama Kepemilikan</label>
                            <input type="text" name="agunan[${count}][lama_kepemilikan]" placeholder="Contoh: 8 Tahun" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Nilai Pasar / MV (Rp)</label>
                            <input type="text" name="agunan[${count}][nilai_pasar_mv]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-bold text-indigo-700 rupiah-input agunan-mv" oninput="formatRupiah(this); hitungCoverageAgunan();">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Likuidasi (% / Nilai LV)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" step="1" name="agunan[${count}][persen_likuidasi]" value="60" class="w-16 text-xs border border-slate-300 rounded-lg p-2.5 bg-white agunan-lv-pct" oninput="hitungCoverageAgunan();">
                                <span class="text-xs text-slate-500 font-semibold">%</span>
                                <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function hapusBarisAgunan(btn) {
            btn.closest('.agunan-item').remove();
            hitungCoverageAgunan();
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
                    <td class="p-2"><input type="text" name="slik[${count}][nama_debitur]" placeholder="Debitur / Pasangan" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                    <td class="p-2"><input type="text" name="slik[${count}][bank_lembaga]" placeholder="Nama Bank / Leasing" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                    <td class="p-2"><input type="text" name="slik[${count}][plafon]" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input" oninput="formatRupiah(this)"></td>
                    <td class="p-2"><input type="text" name="slik[${count}][outstanding]" placeholder="0" class="w-full text-xs border border-slate-300 rounded p-1.5 rupiah-input slik-outstanding" oninput="formatRupiah(this); hitungTotalSlik();"></td>
                    <td class="p-2">
                        <select name="slik[${count}][kelancaran]" class="w-full text-xs border border-slate-300 rounded p-1.5 bg-white">
                            <option value="Lancar">Kol 1 (Lancar)</option>
                            <option value="DPK">Kol 2 (DPK / Dalam Perhatian Khusus)</option>
                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                            <option value="Macet">Kol 5 (Macet)</option>
                        </select>
                    </td>
                    <td class="p-2"><input type="text" name="slik[${count}][keterangan]" placeholder="Kredit Modal Kerja / KPR / dll" class="w-full text-xs border border-slate-300 rounded p-1.5"></td>
                    <td class="p-2 text-center"><button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button></td>
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
                <div class="rekening-item p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                    <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bank</label>
                            <input type="text" name="rekening[${count}][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Periode Mutasi</label>
                            <input type="text" name="rekening[${count}][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Rekening</label>
                            <input type="text" name="rekening[${count}][nomor_rekening]" placeholder="Nomor rekening bank" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Atas Nama Rekening</label>
                            <input type="text" name="rekening[${count}][atas_nama]" placeholder="Nama pemilik rekening" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Kredit / Masuk (Rp)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_kredit]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-indigo-700 rupiah-input rek-kredit" oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Total Mutasi Debit / Keluar (Rp)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_debit]" placeholder="0" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold text-slate-700 rupiah-input rek-debit" oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Bulan Mutasi</label>
                            <input type="number" name="rekening[${count}][jumlah_bulan]" value="6" min="1" max="24" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white rek-bulan" oninput="hitungRataRataRekening();">
                        </div>
                        <div class="flex items-center gap-4 pt-4">
                            <div>
                                <div class="text-[10px] text-slate-500 font-semibold uppercase">Rata-rata Kredit/Bulan:</div>
                                <div class="text-xs font-bold text-emerald-700 rek-avg-kredit">Rp 0</div>
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
                    // Tambahkan ke list dokumen
                    const list = document.getElementById('listDokumenTerunggah');
                    const itemHtml = `
                        <div class="p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between text-xs hover:border-indigo-300 transition">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg font-bold">DOC</div>
                                <div>
                                    <div class="font-semibold text-slate-800">${data.nama_file}</div>
                                    <div class="text-[11px] text-slate-400">Baru saja diunggah</div>
                                </div>
                            </div>
                            <a href="${data.path}" target="_blank" class="px-2.5 py-1 text-indigo-600 hover:text-indigo-800 font-semibold border border-indigo-200 rounded-md">
                                Lihat File
                            </a>
                        </div>
                    `;
                    list.insertAdjacentHTML('afterbegin', itemHtml);
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
                            window.location.href = '<?= base_url('pengajuan') ?>';
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
