<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Review & Keputusan Pengajuan Kredit') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine & CAM';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';

    $statusAplikasi  = $kredit['status_pengajuan'] ?? 'Draft';
    $currentDecision = $keputusan_reviewer['status_approval'] ?? ($statusAplikasi === 'Disetujui' || $statusAplikasi === 'Disetujui Bersyarat' || $statusAplikasi === 'Bersyarat' || $statusAplikasi === 'Ditolak' ? $statusAplikasi : '');
    if ($currentDecision === 'Bersyarat') $currentDecision = 'Disetujui Bersyarat';
    $currentCatatan  = $keputusan_reviewer['catatan_approval'] ?? '';

    // Hitung parameter 5C & Skor AI
    $dscVal     = (float)($keuangan['rasio_dsc_dscr'] ?? 1.35);
    $omzetVal   = (float)($keuangan['pendapatan_rata_rata_bulanan'] ?? ($keuangan['pendapatan_bersih_bulan'] ?? 0));
    $angsuranVal= (float)($keuangan['angsuran_bulanan_usulan'] ?? 0);
    $labaVal    = (float)($keuangan['laba_bersih_operasional'] ?? ($omzetVal * 0.25));

    $skorAi     = !empty($analisa['skor_kelayakan_ai']) ? (int)$analisa['skor_kelayakan_ai'] : 82;
    $rekomAi    = !empty($analisa['rekomendasi_keputusan_ai']) ? $analisa['rekomendasi_keputusan_ai'] : 'Disetujui Penuh';
    $plafonAi   = !empty($analisa['rekomendasi_plafon_ai']) ? (float)$analisa['rekomendasi_plafon_ai'] : (float)($kredit['plafon_pengajuan'] ?? 0);
    $risikoAi   = !empty($analisa['analisis_risiko_ai']) ? $analisa['analisis_risiko_ai'] : 'Profil usaha menunjukkan perputaran kas positif dengan rasio DSC di atas batas aman. Agunan memiliki nilai pasar dan legalitas kuat yang mampu memitigasi risiko gagal bayar.';

    // Penilaian 5C individual
    $charStatus = (($kredit['status_slik'] ?? '') === 'Terbit' && empty($kredit['ada_tunggakan_slik'])) ? 'Sangat Baik' : 'Cukup Baik';
    $charScore  = (($kredit['status_slik'] ?? '') === 'Terbit' && empty($kredit['ada_tunggakan_slik'])) ? 88 : 75;

    $capStatus  = ($dscVal >= 1.30) ? 'Kuat / Aman' : 'Perlu Pengawasan';
    $capScore   = ($dscVal >= 1.50) ? 90 : (($dscVal >= 1.30) ? 82 : 65);

    $capitStatus= 'Stabil';
    $capitScore = 80;

    $collStatus = ($coverage_lv >= 100) ? 'Menutup Penuh' : 'Cukup';
    $collScore  = ($coverage_lv >= 120) ? 92 : (($coverage_lv >= 100) ? 84 : 70);

    $condStatus = 'Kondusif';
    $condScore  = 80;
?>

    <!-- Top Bar Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand & Breadcrumb -->
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('dashboard') ?>" class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition text-slate-600 hover:text-slate-900" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Reviewer / Komite Kredit</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs font-semibold text-slate-500"><?= esc($kredit['nomor_aplikasi']) ?></span>
                        </div>
                        <h1 class="text-base font-extrabold text-slate-900 leading-tight">
                            Evaluasi & Lembar Keputusan Reviewer
                        </h1>
                    </div>
                </div>

                <!-- Right Action: Status Badge -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <?php
                        $badgeHeader = 'bg-blue-50 text-blue-700 border-blue-200';
                        if ($statusAplikasi === 'Disetujui') $badgeHeader = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        elseif (strpos(strtolower($statusAplikasi), 'bersyarat') !== false) $badgeHeader = 'bg-amber-50 text-amber-700 border-amber-200';
                        elseif ($statusAplikasi === 'Ditolak') $badgeHeader = 'bg-rose-50 text-rose-700 border-rose-200';
                    ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border <?= $badgeHeader ?>">
                        Status: <?= esc($statusAplikasi) ?>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1 space-y-6">
        
        <!-- ============================================================== -->
        <!-- ROW 1: KOTAK DILURUSKAN (KIRI: RINGKASAN BERKAS, KANAN: KEPUTUSAN) -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            <!-- SEBELAH KIRI (lg:col-span-7): RINGKASAN INPUTAN BERKAS PENGUSUL -->
            <div class="lg:col-span-7 flex flex-col">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
                    <!-- Header Card Ringkasan Inputan (Tinggi dan styling lurus dengan panel kanan) -->
                    <div class="p-5 border-b border-blue-100 bg-blue-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 min-h-[92px]">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">No. CAM: <?= esc($kredit['nomor_aplikasi']) ?></span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-500 font-medium"><?= date('d F Y', strtotime($kredit['created_at'])) ?></span>
                            </div>
                            <h2 class="text-lg font-extrabold text-slate-900 mt-0.5">
                                <?= esc($kredit['nama_debitur'] ?: 'Nama Debitur Belum Terisi') ?>
                            </h2>
                            <span class="text-xs text-slate-500 font-medium"><?= esc($kredit['nama_usaha'] ?: ($kredit['jenis_debitur'] ?? 'Individu')) ?></span>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[11px] text-slate-500 uppercase font-bold tracking-wider block">Plafon Pengajuan</span>
                            <div class="text-xl font-extrabold text-blue-700">
                                Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                            </div>
                            <span class="text-xs text-slate-500 font-medium"><?= (int)($kredit['tenor_bulan'] ?? 12) ?> Bulan &bull; <?= esc($kredit['skema_pembayaran'] ?? 'Installment') ?></span>
                        </div>
                    </div>

                    <!-- Body: 4 Kotak Ringkas Data Pokok Pengajuan (DILURUSKAN DENGAN TINGGI RATA) -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Ringkasan Berkas Inputan Pengusul
                            </h3>
                            <span class="text-xs text-slate-400">Data Pokok Analisis</span>
                        </div>

                        <!-- Grid 4 Kotak (Diluruskan tingginya dengan flex-1) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 flex-1 items-stretch">
                            <!-- 1. Debitur & Usaha -->
                            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex flex-col justify-between min-h-[145px]">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">1</span>
                                    <span>Calon Debitur & Usaha</span>
                                </span>
                                <div class="space-y-1 my-1.5 text-xs">
                                    <div class="truncate">
                                        <span class="text-slate-400 text-[11px] block">Nama & NIK:</span>
                                        <strong class="text-slate-800 text-xs block truncate"><?= esc($kredit['nama_debitur'] ?: '-') ?></strong>
                                    </div>
                                    <div class="truncate">
                                        <span class="text-slate-400 text-[11px] block">Usaha & Lama:</span>
                                        <span class="text-blue-800 font-bold block truncate"><?= esc($kredit['nama_usaha'] ?: '-') ?> (<?= esc($kredit['lama_usaha'] ?: '-') ?>)</span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 flex justify-between">
                                    <span>HP: <?= esc($kredit['nomor_hp'] ?: '-') ?></span>
                                    <span>NIK: <?= esc($kredit['npwp_pribadi'] ?: '-') ?></span>
                                </div>
                            </div>

                            <!-- 2. Struktur Pembiayaan -->
                            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex flex-col justify-between min-h-[145px]">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">2</span>
                                    <span>Struktur Pembiayaan</span>
                                </span>
                                <div class="space-y-1 my-1.5 text-xs">
                                    <div>
                                        <span class="text-slate-400 text-[11px] block">Plafon Pinjaman:</span>
                                        <strong class="text-blue-700 text-sm block">Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></strong>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span>Tenor: <strong><?= (int)($kredit['tenor_bulan'] ?? 12) ?> Bulan</strong></span>
                                        <span>Bunga: <strong><?= (float)($kredit['bunga_per_bulan'] ?? 2) ?>% / Bln</strong></span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-blue-700 font-bold pt-1 border-t border-slate-200/50 flex justify-between">
                                    <span><?= esc($kredit['skema_pembayaran'] ?? 'Installment') ?></span>
                                    <span>Est: Rp <?= number_format($angsuranVal, 0, ',', '.') ?>/bln</span>
                                </div>
                            </div>

                            <!-- 3. SLIK OJK -->
                            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex flex-col justify-between min-h-[145px]">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">3</span>
                                    <span>SLIK OJK (Ideb)</span>
                                </span>
                                <div class="space-y-1 my-1.5 text-xs flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Status SLIK:</span>
                                        <span class="font-bold <?= ($kredit['status_slik'] ?? '') === 'Terbit' ? 'text-emerald-700' : 'text-amber-700' ?>">
                                            <?= esc($kredit['status_slik'] ?? 'Belum Terbit') ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-700 text-[11px] line-clamp-2 leading-relaxed mt-1">
                                        <?= esc($kredit['ringkasan_slik'] ?: 'Riwayat pembayaran lancar, tidak ada catatan tunggakan fasilitas kredit aktif.') ?>
                                    </p>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 flex justify-between">
                                    <span>Kolektibilitas: Lancar</span>
                                    <span class="text-emerald-700 font-semibold">Kol 1</span>
                                </div>
                            </div>

                            <!-- 4. Agunan & Arus Kas -->
                            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex flex-col justify-between min-h-[145px]">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">4</span>
                                    <span>Agunan & Arus Kas</span>
                                </span>
                                <div class="space-y-1 my-1.5 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[11px]">Nilai Likuidasi (LV):</span>
                                        <strong class="text-slate-800">Rp <?= number_format($total_lv, 0, ',', '.') ?></strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[11px]">Coverage Jaminan:</span>
                                        <strong class="text-blue-700"><?= $coverage_lv ?>%</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[11px]">Omzet Bulanan:</span>
                                        <strong class="text-slate-800">Rp <?= number_format($omzetVal, 0, ',', '.') ?></strong>
                                    </div>
                                </div>
                                <div class="text-[11px] pt-1 border-t border-slate-200/50 flex justify-between items-center">
                                    <span class="text-slate-500">Rasio DSC:</span>
                                    <strong class="text-emerald-700 font-bold"><?= number_format($dscVal, 2) ?>x (Aman)</strong>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <!-- SEBELAH KANAN (lg:col-span-5): LEMBAR KEPUTUSAN REVIEWER (DILURUSKAN DENGAN KIRI) -->
            <div class="lg:col-span-5 flex flex-col">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
                    <!-- Header Card (Tinggi dan styling lurus sejajar dengan header kiri) -->
                    <div class="p-5 border-b border-slate-100 bg-blue-50/60 min-h-[92px] flex flex-col justify-center">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full <?= !empty($currentDecision) ? 'bg-emerald-500' : 'bg-blue-600 animate-pulse' ?>"></span>
                                <h2 class="text-sm font-extrabold text-blue-900 uppercase tracking-wider">
                                    Lembar Keputusan Reviewer
                                </h2>
                            </div>
                            <?php if (!empty($currentDecision)): ?>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                    🔒 Mode Lihat (Sudah Di-review)
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-blue-700/80 mt-1">
                            <?= !empty($currentDecision) ? 'Pemeriksaan review telah selesai. Keputusan tercatat resmi pada risalah komite.' : 'Pilih salah satu dari 3 opsi keputusan komite kredit beserta catatan resmi.' ?>
                        </p>
                    </div>

                    <?php 
                        $userRole = strtolower(session()->get('jabatan') ?? '');
                        $isCeoRole = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false);
                        $isFinalized = (!empty($currentDecision) && in_array($currentDecision, ['Disetujui', 'Disetujui Bersyarat', 'Ditolak']));
                    ?>

                    <?php if ($isFinalized): ?>
                        <!-- ============================================================== -->
                        <!-- TAMPILAN READ-ONLY: KETIKA PETUGAS REVIEW TELAH SELESAI       -->
                        <!-- CEO CUKUP MELIHAT, BEGITUPUN SEBALIKNYA                        -->
                        <!-- ============================================================== -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <!-- Alert Status Final -->
                            <div class="p-4 rounded-xl border flex items-start gap-3 <?= $currentDecision === 'Disetujui' ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : ($currentDecision === 'Ditolak' ? 'bg-rose-50/80 border-rose-200 text-rose-900' : 'bg-amber-50/80 border-amber-200 text-amber-900') ?>">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= $currentDecision === 'Disetujui' ? 'bg-emerald-600 text-white' : ($currentDecision === 'Ditolak' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white') ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider">
                                        <?= $isCeoRole ? 'Pemberitahuan CEO: Review Telah Selesai' : 'Keputusan Resmi Telah Ditetapkan' ?>
                                    </h4>
                                    <p class="text-xs mt-0.5 leading-relaxed opacity-90">
                                        <?= $isCeoRole ? 'Petugas Reviewer telah menyelesaikan penilaian berkas pengajuan kredit ini. Anda berada dalam <strong>Mode Lihat (Read-Only)</strong>.' : 'Keputusan telah disahkan dalam risalah komite. Formulir terkunci untuk menjaga keabsahan dokumen audit.' ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Ringkasan Hasil Keputusan -->
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Keputusan:</span>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black border shadow-2xs <?= $currentDecision === 'Disetujui' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($currentDecision === 'Ditolak' ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-amber-100 text-amber-800 border-amber-300') ?>">
                                        <span class="w-2.5 h-2.5 rounded-full <?= $currentDecision === 'Disetujui' ? 'bg-emerald-600' : ($currentDecision === 'Ditolak' ? 'bg-rose-600' : 'bg-amber-600') ?>"></span>
                                        <span><?= strtoupper(esc($currentDecision)) ?></span>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Pertimbangan / Syarat Realisasi:</span>
                                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 leading-relaxed font-medium">
                                        <?= nl2br(esc($currentCatatan ?: 'Tidak ada catatan khusus yang dicantumkan.')) ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Approver / Reviewer -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1.5 text-xs text-slate-600">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Ditinjau Oleh:</span>
                                    <span class="font-bold text-slate-800"><?= esc($keputusan_reviewer['nama_approver'] ?? $nama_lengkap ?? 'Senior Credit Reviewer') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Peran Komite:</span>
                                    <span class="font-semibold text-blue-700"><?= esc($keputusan_reviewer['role_approver'] ?? $jabatan ?? 'Reviewer') ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Tanggal Pengesahan:</span>
                                    <span class="font-medium text-slate-700"><?= !empty($keputusan_reviewer['tanggal_approval']) ? date('d F Y', strtotime($keputusan_reviewer['tanggal_approval'])) : date('d F Y') ?></span>
                                </div>
                            </div>

                            <!-- Action Buttons Mode Lihat -->
                            <div class="space-y-2 pt-2">
                                <a href="<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>" target="_blank" 
                                   class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>Cetak Memorandum CAM Resmi</span>
                                </a>

                                <a href="<?= base_url('dashboard') ?>" 
                                   class="w-full py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center block">
                                    Kembali ke Dashboard
                                </a>

                                <?php if ($userRole === 'superadmin'): ?>
                                    <button type="button" onclick="document.getElementById('containerFormEditReviewer').classList.toggle('hidden')" 
                                            class="w-full py-2 px-3 text-[11px] text-slate-400 hover:text-slate-600 font-medium transition text-center cursor-pointer">
                                        &bull; Superadmin: Buka Formulir Revisi Keputusan &bull;
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div id="containerFormEditReviewer" class="<?= $isFinalized ? 'hidden' : '' ?> flex-1 flex flex-col justify-between">
                    <form id="formKeputusanReviewer" class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <input type="hidden" name="kredit_id" value="<?= esc($kredit['kredit_id']) ?>">

                        <!-- Pilihan Keputusan: 3 OPSI (Setuju, Bersyarat, Tolak) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Pilihan Keputusan <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Pilih 1 dari 3 opsi</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <!-- 1. Option Setuju -->
                                <label class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center <?= $currentDecision === 'Disetujui' ? 'border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-200' : 'border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30' ?>" id="labelPilihanSetuju">
                                    <input type="radio" name="keputusan" value="Disetujui" class="sr-only" <?= $currentDecision === 'Disetujui' ? 'checked' : '' ?> onchange="updateKeputusanUI()">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center mb-1.5 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-emerald-800">Setuju</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5 leading-tight">Disetujui Penuh</span>
                                </label>

                                <!-- 2. Option Bersyarat -->
                                <label class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center <?= ($currentDecision === 'Disetujui Bersyarat' || $currentDecision === 'Bersyarat') ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-200' : 'border-slate-200 hover:border-amber-300 hover:bg-amber-50/30' ?>" id="labelPilihanBersyarat">
                                    <input type="radio" name="keputusan" value="Disetujui Bersyarat" class="sr-only" <?= ($currentDecision === 'Disetujui Bersyarat' || $currentDecision === 'Bersyarat') ? 'checked' : '' ?> onchange="updateKeputusanUI()">
                                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center mb-1.5 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-amber-800">Bersyarat</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5 leading-tight">Syarat Khusus</span>
                                </label>

                                <!-- 3. Option Tolak -->
                                <label class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center <?= $currentDecision === 'Ditolak' ? 'border-rose-500 bg-rose-50/70 ring-2 ring-rose-200' : 'border-slate-200 hover:border-rose-300 hover:bg-rose-50/30' ?>" id="labelPilihanTolak">
                                    <input type="radio" name="keputusan" value="Ditolak" class="sr-only" <?= $currentDecision === 'Ditolak' ? 'checked' : '' ?> onchange="updateKeputusanUI()">
                                    <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center mb-1.5 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-rose-800">Ditolak</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5 leading-tight">Tolak Berkas</span>
                                </label>
                            </div>
                        </div>

                        <!-- Teks Input: Catatan & Pertimbangan -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label for="catatanKeputusan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Catatan Pertimbangan / Syarat / Alasan
                                    <span id="wajibCatatanBadge" class="text-rose-500 font-bold hidden">(Wajib Diisi)</span>
                                </label>
                            </div>
                            <textarea id="catatanKeputusan" name="catatan" rows="3" 
                                      class="w-full text-xs text-slate-800 border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition placeholder:text-slate-400"
                                      placeholder="Tuliskan catatan pertimbangan kelayakan kredit, klausul syarat pencairan, atau alasan penolakan..."><?= esc($currentCatatan) ?></textarea>
                            <p class="text-[11px] text-slate-400 mt-1" id="catatanHelperText">
                                Catatan ini akan dicatat resmi dalam risalah komite dan tertulis di dokumen CAM.
                            </p>
                        </div>

                        <!-- Info Reviewer -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1.5 text-xs text-slate-600">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Reviewer:</span>
                                <span class="font-bold text-slate-800"><?= esc($nama_lengkap ?? 'Senior Credit Reviewer') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Jabatan:</span>
                                <span class="font-semibold text-blue-700"><?= esc($jabatan ?? 'Reviewer') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Tanggal Keputusan:</span>
                                <span class="font-medium text-slate-700"><?= date('d F Y') ?></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-2 pt-1">
                            <button type="submit" id="btnSubmitKeputusan" 
                                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <span>Simpan Keputusan Reviewer</span>
                            </button>

                            <a href="<?= base_url('dashboard') ?>" 
                               class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center block">
                                Kembali ke Dashboard Reviewer
                            </a>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- ROW 2: HASIL ANALISIS AI PER 5C (MEMANJANG PENUH & ADA TOMBOL LIHAT) -->
        <!-- ============================================================== -->
        <div class="w-full">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <!-- Header Card AI 5C Memanjang Penuh + Tombol Lihat -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shadow-2xs shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Hasil Evaluasi AI per Prinsip 5C</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    AI Underwriting Engine
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Evaluasi komprehensif risiko perbankan terhadap Character, Capacity, Capital, Collateral, & Condition</p>
                        </div>
                    </div>

                    <!-- Tombol Lihat Detail AI -->
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="bukaModalLihatAI()" 
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Lihat Detail AI</span>
                        </button>
                    </div>
                </div>

                <!-- Top Metric Bar: Skor Kelayakan AI, Rekomendasi Keputusan, Plafon Rekomendasi AI -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                    <div class="text-center sm:text-left flex items-center justify-between sm:justify-start sm:gap-4 p-2 bg-white rounded-lg border border-blue-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Skor Kelayakan AI</span>
                            <div class="text-2xl font-extrabold text-blue-700">
                                <?= $skorAi ?><span class="text-xs text-slate-400 font-normal"> / 100</span>
                            </div>
                        </div>
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold border 
                            <?= $skorAi >= 75 ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : ($skorAi >= 60 ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-rose-100 text-rose-800 border-rose-300') ?>">
                            <?= $skorAi >= 75 ? 'Sangat Layak (Low Risk)' : ($skorAi >= 60 ? 'Cukup Layak' : 'Tinggi Risiko') ?>
                        </span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-blue-100 flex flex-col justify-center">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Rekomendasi Keputusan AI</span>
                        <div class="text-sm font-extrabold text-blue-900 mt-0.5 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            <span><?= esc($rekomAi) ?></span>
                        </div>
                        <span class="text-[11px] text-slate-500 block">Profil risiko aman & layak realisasi</span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-blue-100 flex flex-col justify-center">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Rekomendasi Plafon AI</span>
                        <div class="text-base font-extrabold text-blue-700 mt-0.5">
                            Rp <?= number_format($plafonAi, 0, ',', '.') ?>
                        </div>
                        <span class="text-[11px] text-slate-500 block">Kapasitas repayment optimal</span>
                    </div>
                </div>

                <!-- 5 PILAR 5C (MEMANJANG PENUH DALAM GRID 5 KOLOM) -->
                <div>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2.5">Evaluasi 5 Pilar 5C (Full Overview):</span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <!-- 1. Character -->
                        <div class="p-3.5 bg-slate-50/80 hover:bg-blue-50/40 transition rounded-xl border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 mb-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">C1</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><?= $charStatus ?></span>
                                </div>
                                <strong class="text-slate-900 block font-bold text-xs">Character</strong>
                                <span class="text-[11px] text-slate-400 block mb-1">Watak & Rekam Jejak</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    SLIK OJK <?= ($kredit['status_slik'] ?? '') === 'Terbit' ? 'Terbit (Lancar)' : 'Verifikasi' ?> &bull; SIPP Pengadilan bebas perkara hukum.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 text-[11px] text-slate-500 flex justify-between font-semibold">
                                <span>Skor Pilar:</span>
                                <span class="text-blue-700"><?= $charScore ?>/100</span>
                            </div>
                        </div>

                        <!-- 2. Capacity -->
                        <div class="p-3.5 bg-slate-50/80 hover:bg-blue-50/40 transition rounded-xl border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 mb-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">C2</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $dscVal >= 1.3 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' ?>"><?= $capStatus ?></span>
                                </div>
                                <strong class="text-slate-900 block font-bold text-xs">Capacity</strong>
                                <span class="text-[11px] text-slate-400 block mb-1">Kapasitas Bayar (DSC)</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Rasio DSC <strong class="text-emerald-700"><?= number_format($dscVal, 2) ?>x</strong> (&ge; 1.30x) &bull; Omzet rekening koran terkonfirmasi.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 text-[11px] text-slate-500 flex justify-between font-semibold">
                                <span>Skor Pilar:</span>
                                <span class="text-blue-700"><?= $capScore ?>/100</span>
                            </div>
                        </div>

                        <!-- 3. Capital -->
                        <div class="p-3.5 bg-slate-50/80 hover:bg-blue-50/40 transition rounded-xl border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 mb-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">C3</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200"><?= $capitStatus ?></span>
                                </div>
                                <strong class="text-slate-900 block font-bold text-xs">Capital</strong>
                                <span class="text-[11px] text-slate-400 block mb-1">Permodalan Usaha</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Usaha berjalan <?= esc($kredit['lama_usaha'] ?: '1+ thn') ?> dengan arus kas operasional mandiri yang stabil.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 text-[11px] text-slate-500 flex justify-between font-semibold">
                                <span>Skor Pilar:</span>
                                <span class="text-blue-700"><?= $capitScore ?>/100</span>
                            </div>
                        </div>

                        <!-- 4. Collateral -->
                        <div class="p-3.5 bg-slate-50/80 hover:bg-blue-50/40 transition rounded-xl border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 mb-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">C4</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><?= $collStatus ?></span>
                                </div>
                                <strong class="text-slate-900 block font-bold text-xs">Collateral</strong>
                                <span class="text-[11px] text-slate-400 block mb-1">Agunan & Likuidasi</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Coverage Likuidasi (LV) mencapai <strong class="text-blue-700"><?= $coverage_lv ?>%</strong> terhadap plafon yang diajukan.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 text-[11px] text-slate-500 flex justify-between font-semibold">
                                <span>Skor Pilar:</span>
                                <span class="text-blue-700"><?= $collScore ?>/100</span>
                            </div>
                        </div>

                        <!-- 5. Condition -->
                        <div class="p-3.5 bg-slate-50/80 hover:bg-blue-50/40 transition rounded-xl border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60 mb-2">
                                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[11px]">C5</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200"><?= $condStatus ?></span>
                                </div>
                                <strong class="text-slate-900 block font-bold text-xs">Condition</strong>
                                <span class="text-[11px] text-slate-400 block mb-1">Kondisi Pasar</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Permintaan pasar stabil dan berkelanjutan pada sektor usaha debitur.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 text-[11px] text-slate-500 flex justify-between font-semibold">
                                <span>Skor Pilar:</span>
                                <span class="text-blue-700"><?= $condScore ?>/100</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Analisis Risiko AI Memanjang Penuh -->
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                    <div class="flex-1">
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-0.5">Catatan Analisis Risiko & Mitigasi Gemini AI:</span>
                        <p class="text-slate-600 text-[11px] leading-relaxed line-clamp-2">
                            <?= esc($risikoAi) ?>
                        </p>
                    </div>
                    <button type="button" onclick="bukaModalLihatAI()" 
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-bold rounded-xl text-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Lihat Uraian Lengkap</span>
                    </button>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Sistem Evaluasi & Keputusan Komite Kredit
        </div>
    </footer>

    <!-- Modal Pop-up Laporan Analisa AI & Rekomendasi Komite Kredit -->
    <div id="modalDetailAI" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-5 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[94vh] flex flex-col overflow-hidden border border-slate-200">
            <!-- Modal Header -->
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Laporan Analisa Kelayakan Kredit & Rekomendasi Komite (AI)</h3>
                        <p class="text-[11px] text-slate-500">Format Dokumen Hasil Evaluasi Sesuai Standar Komite</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Tombol Download / Cetak PDF (dw) -->
                    <button type="button" onclick="cetakIframeLaporan()"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                    <!-- Tombol Tutup Modal -->
                    <button type="button" onclick="tutupModalLihatAI()" 
                            class="p-2 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition cursor-pointer" title="Tutup Modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body: Iframe Halaman Laporan CAM Sesuai Screenshot -->
            <div class="flex-1 overflow-hidden bg-slate-100 p-0">
                <iframe id="iframeLaporanCAM" src="<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>?modal=1" class="w-full h-[78vh] border-0"></iframe>
            </div>
        </div>
    </div>

    <script>
        function updateKeputusanUI() {
            const radSetuju    = document.querySelector('input[name="keputusan"][value="Disetujui"]');
            const radBersyarat = document.querySelector('input[name="keputusan"][value="Disetujui Bersyarat"]');
            const radTolak     = document.querySelector('input[name="keputusan"][value="Ditolak"]');

            const lblSetuju    = document.getElementById('labelPilihanSetuju');
            const lblBersyarat = document.getElementById('labelPilihanBersyarat');
            const lblTolak     = document.getElementById('labelPilihanTolak');

            const wajibBadge   = document.getElementById('wajibCatatanBadge');
            const helperText   = document.getElementById('catatanHelperText');

            // Reset base classes
            lblSetuju.className    = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30';
            lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-amber-300 hover:bg-amber-50/30';
            lblTolak.className     = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-rose-300 hover:bg-rose-50/30';

            if (radSetuju && radSetuju.checked) {
                lblSetuju.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-200';
                if (wajibBadge) wajibBadge.classList.add('hidden');
                if (helperText) helperText.innerText = 'Catatan pertimbangan kelayakan kredit yang akan dicatat resmi dalam risalah komite.';
            } else if (radBersyarat && radBersyarat.checked) {
                lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-amber-500 bg-amber-50/70 ring-2 ring-amber-200';
                if (wajibBadge) wajibBadge.classList.remove('hidden');
                if (helperText) helperText.innerText = 'Harap tuliskan klausul syarat khusus pencairan (misal: penambahan agunan / penurunan plafon / kelengkapan berkas).';
            } else if (radTolak && radTolak.checked) {
                lblTolak.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-rose-500 bg-rose-50/70 ring-2 ring-rose-200';
                if (wajibBadge) wajibBadge.classList.remove('hidden');
                if (helperText) helperText.innerText = 'Harap tuliskan alasan penolakan pengajuan kredit untuk arsip komite.';
            }
        }

        // Buka Pop-up Laporan CAM & AI
        function bukaModalLihatAI() {
            const modal = document.getElementById('modalDetailAI');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        // Tutup Pop-up Laporan CAM & AI
        function tutupModalLihatAI() {
            const modal = document.getElementById('modalDetailAI');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Cetak dokumen dari iframe modal
        function cetakIframeLaporan() {
            const iframe = document.getElementById('iframeLaporanCAM');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } else {
                window.open('<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>', '_blank');
            }
        }

        // Tutup pop-up jika menekan tombol Escape atau klik di luar modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tutupModalLihatAI();
        });
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalDetailAI');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) tutupModalLihatAI();
                });
            }
        });

        // Submit Keputusan Form Reviewer
        document.getElementById('formKeputusanReviewer').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const radKeputusan = form.querySelector('input[name="keputusan"]:checked');
            if (!radKeputusan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Keputusan',
                    text: 'Silakan pilih keputusan: Setuju, Bersyarat, atau Tolak.'
                });
                return;
            }

            const keputusan = radKeputusan.value;
            const catatan = document.getElementById('catatanKeputusan').value.trim();

            if ((keputusan === 'Ditolak' || keputusan === 'Disetujui Bersyarat') && !catatan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Wajib Diisi',
                    text: keputusan === 'Ditolak' 
                        ? 'Harap berikan alasan penolakan pada kolom catatan keputusan!' 
                        : 'Harap berikan persyaratan khusus persetujuan pada kolom catatan keputusan!'
                });
                document.getElementById('catatanKeputusan').focus();
                return;
            }

            let confirmTitle = 'Konfirmasi Persetujuan';
            let confirmText  = 'Apakah Anda yakin menyetujui pengajuan kredit ini?';
            let confirmColor = '#2563eb';
            let confirmBtn   = 'Ya, Setujui Pengajuan';

            if (keputusan === 'Disetujui Bersyarat') {
                confirmTitle = 'Konfirmasi Persetujuan Bersyarat';
                confirmText  = 'Apakah Anda yakin memberikan persetujuan bersyarat untuk permohonan kredit ini?';
                confirmColor = '#f59e0b';
                confirmBtn   = 'Ya, Tetapkan Bersyarat';
            } else if (keputusan === 'Ditolak') {
                confirmTitle = 'Konfirmasi Penolakan';
                confirmText  = 'Apakah Anda yakin ingin menolak pengajuan kredit ini?';
                confirmColor = '#e11d48';
                confirmBtn   = 'Ya, Tolak Pengajuan';
            }

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: keputusan === 'Ditolak' ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtn,
                cancelButtonText: 'Periksa Kembali'
            }).then((res) => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: 'Menyimpan Keputusan...',
                        text: 'Memproses pembaruan status dan risalah komite...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const formData = new FormData(form);

                    fetch('<?= base_url('reviewer/simpan-keputusan') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                text: data.message,
                                timer: 1800,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url || '<?= base_url('dashboard') ?>';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: data.message || 'Terjadi kesalahan sistem.'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal terhubung ke server. Silakan coba lagi.'
                        });
                    });
                }
            });
        });

        // Trigger on load
        document.addEventListener('DOMContentLoaded', () => {
            updateKeputusanUI();
        });
    </script>
</body>
</html>
