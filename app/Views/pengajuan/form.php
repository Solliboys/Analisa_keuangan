<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | OTW Credit Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?= base_url('css/pengajuan-form.css?v=' . time()) ?>">
    <style>
        /* Posisi tanda panah dropdown (chevron) agar rapi & tidak terlalu kepinggir */
        select:not([multiple]):not([size]) {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 1.25rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.15rem 1.15rem !important;
            padding-right: 3rem !important;
            cursor: pointer;
        }
        select:not([multiple]):not([size]):disabled {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            cursor: not-allowed;
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
                </div>

                <div class="flex items-center gap-3">
                    <span id="saveStatusIndicator" class="text-xs text-slate-500 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span> Siap disimpan
                    </span>
                    <?php if (!empty($kredit_id)): ?>
                        <a href="<?= base_url('pengajuan/cetak/' . $kredit_id) ?>" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition flex items-center gap-1">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak CAM
                        </a>
                    <?php endif; ?>
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
                <?php
                $sidebarSteps = [
                    1  => ['title' => '1. Aplikasi',       'sub' => 'Parameter dasar'],
                    2  => ['title' => '2. Info Pribadi',   'sub' => 'Data demografi'],
                    3  => ['title' => '3. Info Usaha',     'sub' => 'Legalitas usaha'],
                    4  => ['title' => '4. Jaminan',        'sub' => 'Agunan kredit'],
                    5  => ['title' => '5. Dokumen',        'sub' => 'Unggah berkas'],
                    6  => ['title' => '6. SLIK / CLIK',    'sub' => 'Cek riwayat kredit'],
                    7  => ['title' => '7. Web Check',      'sub' => 'Verifikasi online'],
                    8  => ['title' => '8. Rekening Koran', 'sub' => 'Analisis mutasi'],
                    9  => ['title' => '9. Laba Rugi',      'sub' => 'Proyeksi keuangan'],
                    10 => ['title' => '10. Opini Pengusul','sub' => 'Catatan kualitatif'],
                    11 => ['title' => '11. Disbursement',  'sub' => 'Jadwal pencairan'],
                    12 => ['title' => '12. Repayment',     'sub' => 'Kemampuan angsur'],
                ];
                ?>
                <!-- Vertical Stepper List (12 Tahap Pengusul) -->
                <div class="flex-1 overflow-y-auto scroll-smooth space-y-1 relative pr-1" id="verticalStepperList">
                    <?php foreach ($sidebarSteps as $stepNum => $step): ?>
                        <?php 
                            $isActive = ($stepNum === 1);
                            $btnClass = $isActive 
                                ? 'bg-blue-50/80 border-blue-200/80 shadow-2xs' 
                                : 'hover:bg-slate-50 border-transparent';
                            $circleClass = $isActive 
                                ? 'bg-blue-600 text-white shadow-xs' 
                                : 'bg-white border border-slate-300 text-slate-500';
                            $titleClass = $isActive ? 'text-blue-900' : 'text-slate-700';
                            $subClass = $isActive ? 'text-blue-600 font-semibold' : 'text-slate-400';
                            $subText = $isActive ? 'Sedang dikerjakan' : $step['sub'];
                        ?>
                        <button type="button" onclick="goToTab(<?= $stepNum ?>)" id="step-item-<?= $stepNum ?>" class="w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 border <?= $btnClass ?>">
                            <span id="step-circle-<?= $stepNum ?>" class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 <?= $circleClass ?>">
                                <?= $stepNum ?>
                            </span>
                            <div class="min-w-0">
                                <div id="step-title-<?= $stepNum ?>" class="text-xs font-bold truncate <?= $titleClass ?>">
                                    <?= esc($step['title']) ?>
                                </div>
                                <div id="step-sub-<?= $stepNum ?>" class="text-[11px] truncate <?= $subClass ?>">
                                    <?= esc($subText) ?>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; ?>
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
                    <input type="hidden" name="status_slik" id="inputStatusSlik" value="<?= esc($kredit['status_slik'] ?? 'Belum Terbit') ?>">

                    <!-- ==========================================
                         TAB 1: APLIKASI & PLAFON PINJAMAN
                    =========================================== -->
                    <?= $this->include('pengajuan/tabs/tab_1') ?>

                    <?= $this->include('pengajuan/tabs/tab_2') ?>

                    <?= $this->include('pengajuan/tabs/tab_3') ?>

                    <?= $this->include('pengajuan/tabs/tab_4') ?>

                    <?= $this->include('pengajuan/tabs/tab_5') ?>

                    <?= $this->include('pengajuan/tabs/tab_6') ?>

                    <?= $this->include('pengajuan/tabs/tab_7') ?>

                    <?= $this->include('pengajuan/tabs/tab_8') ?>

                    <?= $this->include('pengajuan/tabs/tab_9') ?>

                    <?= $this->include('pengajuan/tabs/tab_10') ?>

                    <?= $this->include('pengajuan/tabs/tab_11') ?>

                    <?= $this->include('pengajuan/tabs/tab_12') ?>

                    <?= $this->include('pengajuan/tabs/tab_13') ?>

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
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
    </script>
    <script src="<?= base_url('js/pengajuan-form.js?v=' . time()) ?>"></script>

    <!-- Modal Interaktif: Penelusuran Perkara Mahkamah Agung RI -->
    <div id="modalCekPerkaraMa" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full overflow-hidden flex flex-col my-auto animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 border border-white/10 shrink-0 text-lg">
                        ⚖️
                    </div>
                    <div>
                        <h3 class="text-sm font-bold tracking-tight text-white flex items-center gap-2">
                            <span>Penelusuran Perkara Mahkamah Agung RI</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/30 text-blue-200 border border-blue-400/30">Direktori Putusan MA</span>
                        </h3>
                        <p class="text-[11px] text-slate-300">Pemeriksaan perkara perdata, wanprestasi perbankan, dan kepailitan/PKPU</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalCekMa()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer font-bold">
                    ✕
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Info UU PDP Badge -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-2.5">
                    <span class="text-base leading-none">ℹ️</span>
                    <div class="leading-relaxed">
                        <strong class="font-bold">Ketentuan Publikasi Peradilan (UU PDP):</strong>
                        Dokumen putusan Mahkamah Agung mempublikasikan <span class="font-semibold underline">Nama Lengkap & Domisili Hukum</span>, namun <span class="font-semibold">NIK disamarkan</span>. Pencarian menggunakan kombinasi Nama Lengkap Debitur dan Kota.
                    </div>
                </div>

                <!-- Parameter Pencarian -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Calon Debitur</label>
                        <input type="text" id="modal_ma_nama" class="w-full text-xs font-semibold text-slate-800 border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Kota / Domisili Pengadilan</label>
                        <input type="text" id="modal_ma_kota" placeholder="Contoh: Jakarta / Surabaya" class="w-full text-xs font-semibold text-slate-800 border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Entitas Usaha (Opsional)</label>
                        <input type="text" id="modal_ma_usaha" placeholder="Nama PT / CV / Toko" class="w-full text-xs font-semibold text-slate-800 border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">NIK KTP (Identitas Unik)</label>
                        <input type="text" id="modal_ma_nik" readonly class="w-full text-xs font-mono font-semibold text-slate-600 border border-slate-200 rounded-lg p-2.5 bg-slate-100">
                    </div>
                </div>

                <!-- Tombol Aksi Penelusuran -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button" id="btnJalankanScanMa" onclick="jalankanScanPerkaraMa()" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>⚡ Scan Otomatis (AI Legal Assistant)</span>
                    </button>
                    <button type="button" onclick="bukaPortalResmiMa()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span>🌐 Buka Portal Resmi MA</span>
                    </button>
                </div>

                <!-- Area Hasil Scan -->
                <div id="modal_ma_area_hasil" class="hidden space-y-3 pt-2 border-t border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Hasil Verifikasi Peradilan:</span>
                        <span id="modal_ma_status_badge" class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            🟢 NIHIL PERKARA / BERSIH
                        </span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Rekomendasi Narasi (Siap Diterapkan ke Form CAM SIPP):</label>
                        <textarea id="modal_ma_hasil_narasi" rows="3" class="w-full text-xs font-medium text-slate-800 border border-slate-300 rounded-xl p-3 bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 leading-relaxed"></textarea>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" onclick="tutupModalCekMa()" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition cursor-pointer">
                    Tutup
                </button>
                <button type="button" id="btnTerapkanMa" onclick="terapkanHasilMaKeForm()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <span>✅ Terapkan Hasil ke Kolom SIPP</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
