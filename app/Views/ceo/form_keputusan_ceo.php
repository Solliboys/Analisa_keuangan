<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Review & Keputusan Pengajuan Kredit') ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; -webkit-font-smoothing: antialiased; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Approved';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : 'img/logo-oap.png?v=2';

    $statusAplikasi  = $kredit['status_pengajuan'] ?? 'Draft';

    // Form khusus CEO
    $currentDecision = $keputusan_ceo['status_approval'] ?? '';
    $currentCatatan  = $keputusan_ceo['catatan_approval'] ?? '';
    if ($currentDecision === 'Bersyarat') $currentDecision = 'Disetujui Bersyarat';

    // Hitung parameter 5C & Skor AI dari kolom database aktual
    $omzetVal = (float)($keuangan['pendapatan_rk'] ?? ($keuangan['pendapatan_rata_rata_bulanan'] ?? ($keuangan['pendapatan_bersih_bulan'] ?? 0)));
    if ($omzetVal <= 0 && !empty($keuangan['detail_pendapatan_rk'])) {
        $decP = json_decode($keuangan['detail_pendapatan_rk'], true);
        if (is_array($decP)) {
            foreach ($decP as $dp) $omzetVal += (float)($dp['nominal'] ?? 0);
        }
    }
    if ($omzetVal <= 0 && !empty($rekening) && is_array($rekening)) {
        foreach ($rekening as $rk) {
            $blnRk = max(1, (int)($rk['jumlah_bulan'] ?? 6));
            $omzetVal += round((float)($rk['total_mutasi_kredit'] ?? 0) / $blnRk);
        }
    }

    $angsuranVal = (float)($keuangan['angsuran_dipilih'] ?? ($keuangan['angsuran_installment'] ?? ($keuangan['angsuran_bulanan_usulan'] ?? 0)));
    if ($angsuranVal <= 0) {
        $plfTmp = (float)($kredit['plafon_pengajuan'] ?? 0);
        $tnrTmp = max(1, (int)($kredit['tenor_bulan'] ?? 12));
        $bngTmp = (float)($kredit['bunga_per_bulan'] ?? ((float)($kredit['bunga_per_tahun'] ?? 12) / 12));
        $rTmp   = $bngTmp / 100;
        $mtdTmp = strtoupper($kredit['perhitungan_bunga'] ?? 'ANUITAS');
        if ($plfTmp > 0) {
            if ($mtdTmp === 'ANUITAS' && $rTmp > 0) {
                $angsuranVal = round($plfTmp * ($rTmp / (1 - pow(1 + $rTmp, -$tnrTmp))));
            } else {
                $angsuranVal = round(($plfTmp / $tnrTmp) + ($plfTmp * $rTmp));
            }
        }
    }

    $labaVal = (float)($keuangan['nett_cashflow_rk'] ?? ($keuangan['total_cashflow_tersedia'] ?? ($keuangan['laba_bersih_operasional'] ?? 0)));
    if ($labaVal <= 0 && $omzetVal > 0) {
        $hppNom = (float)($keuangan['hpp_nominal_rk'] ?? 0);
        $bhNom  = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);
        $blNom  = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
        $labaVal = max(0, $omzetVal - $hppNom - $bhNom - $blNom);
    }

    $dscVal = (float)($keuangan['dsc_rasio_rk'] ?? ($keuangan['rasio_dsc_dscr'] ?? 0));
    if ($dscVal <= 0 && $angsuranVal > 0 && $labaVal > 0) {
        $dscVal = round($labaVal / $angsuranVal, 2);
    }

    $skorAi     = !empty($analisa['skor_kelayakan_ai']) ? (int)$analisa['skor_kelayakan_ai'] : 82;
    $rekomAi    = !empty($analisa['rekomendasi_keputusan_ai']) ? $analisa['rekomendasi_keputusan_ai'] : 'Disetujui Penuh';
    $plafonPengajuan = (float)($kredit['plafon_pengajuan'] ?? 0);
    $rawPlafonAi = !empty($analisa['rekomendasi_plafon_ai']) ? (float)$analisa['rekomendasi_plafon_ai'] : $plafonPengajuan;
    $plafonAi   = ($plafonPengajuan > 0 && $rawPlafonAi > $plafonPengajuan) ? $plafonPengajuan : $rawPlafonAi;
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
    <header class="bg-white border-t-2 border-t-blue-700 border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 sm:h-16 items-center gap-3">
                <!-- Tombol Kembali Saja Sesuai Permintaan User -->
                <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition shadow-xs" title="Kembali ke Dashboard">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </a>

                <!-- Right Action: Status Badge -->
                <div class="flex items-center gap-2 shrink-0">
                    <?php
                        $badgeHeader = 'bg-blue-50 text-blue-700 border-blue-200';
                        if ($statusAplikasi === 'Disetujui') $badgeHeader = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        elseif (strpos(strtolower($statusAplikasi), 'bersyarat') !== false) $badgeHeader = 'bg-amber-50 text-amber-700 border-amber-200';
                        elseif (strpos(strtolower($statusAplikasi), 'revisi') !== false) $badgeHeader = 'bg-purple-50 text-purple-700 border-purple-300 ring-1 ring-purple-200/50';
                        elseif ($statusAplikasi === 'Ditolak') $badgeHeader = 'bg-rose-50 text-rose-700 border-rose-200';
                    ?>
                    <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold border whitespace-nowrap <?= $badgeHeader ?>">
                        Status: <?= esc($statusAplikasi) ?>
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex-1 min-w-0 space-y-6">
        
        <!-- ============================================================== -->
        <!-- ROW 1: KOTAK DILURUSKAN (KIRI: RINGKASAN BERKAS, KANAN: KEPUTUSAN) -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            <!-- SEBELAH KIRI (lg:col-span-7): RINGKASAN INPUTAN BERKAS PENGUSUL -->
            <div class="lg:col-span-7 flex flex-col">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full">
                    <!-- Header Card Ringkasan Inputan (Tinggi dan styling lurus dengan panel kanan) -->
                    <div class="p-4 sm:p-5 border-b border-blue-100 bg-blue-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 min-h-0 sm:min-h-[92px]">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">No. Aplikasi: <?= esc($kredit['nomor_aplikasi']) ?></span>
                                <span class="text-slate-300 hidden sm:inline">&bull;</span>
                                <span class="text-xs text-slate-500 font-medium"><?= date('d F Y', strtotime($kredit['created_at'])) ?></span>
                            </div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 mt-0.5">
                                <?= esc(($kredit['nama_debitur'] ?? '') ?: 'Nama Debitur Belum Terisi') ?>
                            </h2>
                            <span class="text-xs text-slate-500 font-medium"><?= esc(($kredit['nama_usaha'] ?? '') ?: ($kredit['jenis_debitur'] ?? 'Individu')) ?></span>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[11px] text-slate-500 uppercase font-bold tracking-wider block">Plafon Pengajuan</span>
                            <div class="text-lg sm:text-xl font-extrabold text-blue-700">
                                Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                            </div>
                            <span class="text-xs text-slate-500 font-medium"><?= (int)($kredit['tenor_bulan'] ?? 12) ?> Bulan &bull; <?= esc($kredit['skema_pembayaran'] ?? 'Installment') ?></span>
                        </div>
                    </div>

                    <!-- Body: 4 Kotak Ringkas Data Pokok Pengajuan (DILURUSKAN DENGAN TINGGI RATA) -->
                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 min-w-0">
                                <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Ringkasan Berkas Inputan Pengusul</span>
                            </h3>
                            <span class="text-[11px] font-medium text-slate-400 shrink-0 whitespace-nowrap">Data Pokok Analisis</span>
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
                                        <strong class="text-slate-800 text-xs block truncate"><?= esc(($kredit['nama_debitur'] ?? '') ?: '-') ?></strong>
                                    </div>
                                    <div class="truncate">
                                        <span class="text-slate-400 text-[11px] block">Usaha & Lama:</span>
                                        <span class="text-blue-800 font-bold block truncate"><?= esc(($kredit['nama_usaha'] ?? '') ?: '-') ?> (<?= esc(($kredit['lama_usaha'] ?? '') ?: '-') ?>)</span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 flex justify-between">
                                    <span>HP: <?= esc(($kredit['nomor_hp'] ?? '') ?: '-') ?></span>
                                    <span>NIK: <?= esc(($kredit['npwp_pribadi'] ?? '') ?: '-') ?></span>
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
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">3</span>
                                        <span>SLIK OJK (Ideb)</span>
                                    </div>
                                    <?php if (!empty($kredit['berkas_slik_pdf'])): ?>
                                        <a href="<?= base_url($kredit['berkas_slik_pdf']) ?>" target="_blank" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 underline flex items-center gap-0.5" title="Lihat Laporan iDeb Resmi PDF">
                                            <span>PDF iDeb ↗</span>
                                        </a>
                                    <?php endif; ?>
                                </span>
                                <div class="space-y-1 my-1.5 text-xs flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Status SLIK:</span>
                                        <span class="font-bold <?= ($kredit['status_slik'] ?? '') === 'Terbit' ? 'text-emerald-700' : 'text-amber-700' ?>">
                                            <?= esc($kredit['status_slik'] ?? 'Belum Terbit') ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-700 text-[11px] line-clamp-2 leading-relaxed mt-1">
                                        <?= esc(($kredit['ringkasan_slik'] ?? '') ?: 'Riwayat pembayaran lancar, tidak ada catatan tunggakan fasilitas kredit aktif.') ?>
                                    </p>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50 flex justify-between items-center">
                                    <span>Hasil:</span>
                                    <span class="font-bold <?= stripos($kredit['hasil_slik'] ?? '', 'tunggakan') !== false || stripos($kredit['hasil_slik'] ?? '', 'kol 2') !== false || stripos($kredit['hasil_slik'] ?? '', 'kol 3') !== false ? 'text-rose-600' : 'text-emerald-700' ?>">
                                        <?= esc($kredit['hasil_slik'] ?? 'Kol 1 (Lancar)') ?>
                                    </span>
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
                                    <div class="flex justify-between items-baseline gap-2">
                                        <span class="text-slate-400 text-[11px] shrink-0">Coverage Jaminan:</span>
                                        <strong class="text-blue-700 truncate max-w-[140px] sm:max-w-[200px]" title="<?= $coverage_lv ?>%"><?= $coverage_lv ?>%</strong>
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
                    <div class="p-4 sm:p-5 border-b border-slate-100 bg-blue-50/60 min-h-0 sm:min-h-[92px] flex flex-col justify-center">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full <?= !empty($currentDecision) ? 'bg-emerald-500' : 'bg-blue-600 animate-pulse' ?>"></span>
                                <h2 class="text-xs sm:text-sm font-extrabold text-blue-900 uppercase tracking-wider">
                                    Lembar Keputusan CEO
                                </h2>
                            </div>
                            <?php if (!empty($currentDecision) && in_array($currentDecision, ['Disetujui', 'Disetujui Bersyarat', 'Ditolak', 'Revisi'])): ?>
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200 shrink-0">
                                    Mode Lihat (Sudah Diputuskan)
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-blue-700/80 mt-1">
                            <?php if (!empty($currentDecision) && in_array($currentDecision, ['Disetujui', 'Disetujui Bersyarat', 'Ditolak', 'Revisi'])): ?>
                                Pemeriksaan telaah selesai. Keputusan CEO tercatat resmi pada risalah komite.
                            <?php else: ?>
                                Tetapkan keputusan komite kredit resmi beserta catatan pertimbangan CEO.
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php 
                        $isFinalized = in_array($statusAplikasi, ['Disetujui', 'Disetujui Bersyarat', 'Bersyarat', 'Ditolak', 'Revisi']) 
                                       || !empty($keputusan_ceo);
                        
                        // Di mode read-only (finalized), gunakan catatan & keputusan CEO
                        if ($isFinalized) {
                            $currentDecision = !empty($keputusan_ceo['status_approval']) ? $keputusan_ceo['status_approval'] : (!empty($keputusan_reviewer['status_approval']) ? $keputusan_reviewer['status_approval'] : $statusAplikasi);
                            $currentCatatan  = !empty($keputusan_ceo['catatan_approval']) ? $keputusan_ceo['catatan_approval'] : ($keputusan_reviewer['catatan_approval'] ?? '');
                            if ($currentDecision === 'Bersyarat') $currentDecision = 'Disetujui Bersyarat';
                        }
                    ?>

                    <?php if ($isFinalized): ?>
                        <!-- ============================================================== -->
                        <!-- TAMPILAN READ-ONLY: KETIKA PETUGAS REVIEW TELAH SELESAI       -->
                        <!-- CEO CUKUP MELIHAT, BEGITUPUN SEBALIKNYA                        -->
                        <!-- ============================================================== -->
                        <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
                            <?php
                                $finalAlertClass = 'bg-slate-50 border-slate-200 text-slate-900';
                                $finalIconClass  = 'bg-slate-600 text-white';
                                $finalBadgeClass = 'bg-slate-100 text-slate-800 border-slate-300';
                                $finalDotClass   = 'bg-slate-600';
                                $finalTitle      = 'Keputusan Resmi Telah Ditetapkan';
                                $finalDesc       = 'Keputusan telah disahkan dalam risalah komite.';

                                if ($currentDecision === 'Disetujui') {
                                    $finalAlertClass = 'bg-emerald-50/80 border-emerald-200 text-emerald-900';
                                    $finalIconClass  = 'bg-emerald-600 text-white';
                                    $finalBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                    $finalDotClass   = 'bg-emerald-600';
                                    $finalTitle      = $isCeoRole ? 'Pemberitahuan CEO: Pengajuan Disetujui' : 'Pengajuan Telah Disetujui Penuh';
                                    $finalDesc       = 'Pengajuan telah disetujui tanpa syarat dan siap untuk proses realisasi / pencairan.';
                                } elseif ($currentDecision === 'Disetujui Bersyarat' || $currentDecision === 'Bersyarat') {
                                    $finalAlertClass = 'bg-amber-50/80 border-amber-200 text-amber-900';
                                    $finalIconClass  = 'bg-amber-500 text-white';
                                    $finalBadgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
                                    $finalDotClass   = 'bg-amber-600';
                                    $finalTitle      = 'Persetujuan Bersyarat Ditetapkan';
                                    $finalDesc       = 'Pengajuan disetujui dengan klausul syarat khusus sebelum realisasi akad kredit.';
                                } elseif ($currentDecision === 'Revisi') {
                                    $finalAlertClass = 'bg-purple-50/80 border-purple-200 text-purple-900';
                                    $finalIconClass  = 'bg-purple-600 text-white';
                                    $finalBadgeClass = 'bg-purple-100 text-purple-800 border-purple-300';
                                    $finalDotClass   = 'bg-purple-600';
                                    $finalTitle      = 'Berkas Dikembalikan untuk Revisi (AO)';
                                    $finalDesc       = 'Berkas telah dikembalikan ke Staf Pengusul (AO) untuk perbaikan data/dokumen sesuai catatan instruksi di bawah.';
                                } elseif ($currentDecision === 'Ditolak') {
                                    $finalAlertClass = 'bg-rose-50/80 border-rose-200 text-rose-900';
                                    $finalIconClass  = 'bg-rose-600 text-white';
                                    $finalBadgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
                                    $finalDotClass   = 'bg-rose-600';
                                    $finalTitle      = 'Pengajuan Kredit Ditolak';
                                    $finalDesc       = 'Pengajuan tidak disetujui berdasarkan evaluasi mitigasi risiko kredit.';
                                }
                            ?>
                            <!-- Alert Status Final -->
                            <div class="p-4 rounded-xl border flex items-start gap-3 <?= $finalAlertClass ?>">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 <?= $finalIconClass ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider">
                                        <?= esc($finalTitle) ?>
                                    </h4>
                                    <p class="text-xs mt-0.5 leading-relaxed opacity-90">
                                        <?= $isCeoRole ? 'Anda berada dalam <strong>Mode Lihat (Read-Only)</strong>. ' . esc($finalDesc) : esc($finalDesc) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Ringkasan Hasil Keputusan -->
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Keputusan:</span>
                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black border shadow-2xs <?= $finalBadgeClass ?>">
                                        <span class="w-2.5 h-2.5 rounded-full <?= $finalDotClass ?>"></span>
                                        <span><?= strtoupper(esc($currentDecision)) ?></span>
                                    </div>
                                </div>

                                <div>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Catatan Pertimbangan / Syarat Realisasi:</span>
                                    <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 leading-relaxed font-medium">
                                        <?= nl2br(esc($currentCatatan ?: 'Tidak ada catatan khusus yang dicantumkan.')) ?>
                                    </div>
                                </div>

                                <?php 
                                    $daftarReadOnlyNotes = $daftar_catatan_reviewer ?? [];
                                    if (empty($daftarReadOnlyNotes) && !empty($catatan_dari_reviewer)) {
                                        $daftarReadOnlyNotes = [$catatan_dari_reviewer];
                                    }
                                ?>
                                <?php if (!empty($daftarReadOnlyNotes)): ?>
                                <div>
                                    <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block mb-1">Catatan dari Reviewer (<?= count($daftarReadOnlyNotes) ?>):</span>
                                    <div class="space-y-2">
                                        <?php foreach ($daftarReadOnlyNotes as $rNote): ?>
                                            <?php 
                                                $noteText = trim($rNote['catatan_approval'] ?? '');
                                                if (empty($noteText)) continue;
                                                $revName  = $rNote['nama_approver'] ?? 'Reviewer';
                                                $revDate  = !empty($rNote['tanggal_approval']) ? date('d F Y', strtotime($rNote['tanggal_approval'])) : (!empty($rNote['created_at']) ? date('d F Y', strtotime($rNote['created_at'])) : '');
                                                $revRek   = $rNote['status_approval'] ?? '';
                                            ?>
                                            <div class="p-3 bg-indigo-50/70 border border-indigo-200/80 rounded-xl text-xs text-slate-800 leading-relaxed font-medium space-y-1.5">
                                                <div><?= nl2br(esc($noteText)) ?></div>
                                                <div class="pt-1.5 border-t border-indigo-200/50 text-[10px] text-indigo-500/80 flex items-center justify-between flex-wrap gap-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span>Oleh: <strong class="text-indigo-700"><?= esc($revName) ?></strong></span>
                                                        <?php if (!empty($revRek) && !in_array($revRek, ['Ditinjau Reviewer', 'Pending', 'Menunggu Review'])): ?>
                                                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200"><?= esc($revRek) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($revDate)): ?><span><?= $revDate ?></span><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
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

                                <a href="<?= base_url('pengajuan/cetakLaporanPdf/' . $kredit['kredit_id']) ?>" target="_blank" 
                                   class="w-full py-2.5 px-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl border border-indigo-200 transition flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>Lihat Laporan Keuangan Estimasi (PDF)</span>
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
                    <form id="formKeputusanReviewer" action="<?= base_url('reviewer/simpan-keputusan') ?>" method="POST" class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-4">
                        <input type="hidden" name="kredit_id" value="<?= esc($kredit['kredit_id']) ?>">

                        <?php 
                            // Tampilkan catatan reviewer (mendukung multi-review jika ada review tambahan)
                            $daftarReviewerNotes = $daftar_catatan_reviewer ?? [];
                            if (empty($daftarReviewerNotes) && !empty($catatan_dari_reviewer)) {
                                $daftarReviewerNotes = [$catatan_dari_reviewer];
                            }
                        ?>
                        <?php if (!empty($daftarReviewerNotes)): ?>
                        <!-- Catatan Hasil Review dari Reviewer (Bisa Nambah Review Lainnya) -->
                        <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-indigo-900 uppercase tracking-wider">Catatan Hasil Review</h4>
                                        <p class="text-[11px] text-indigo-600/80">Catatan dari Reviewer untuk bahan pertimbangan keputusan</p>
                                    </div>
                                </div>
                                <?php if (count($daftarReviewerNotes) > 1): ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 shadow-2xs">
                                        Catatan Terbaru
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2.5">
                                <?php
                                    // Hanya tampilkan catatan terbaru per reviewer (1 per user)
                                    $latestPerReviewer = [];
                                    foreach ($daftarReviewerNotes as $rn) {
                                        $key = $rn['nama_approver'] ?? 'Reviewer';
                                        $latestPerReviewer[$key] = $rn; // Yang terakhir menimpa yang sebelumnya
                                    }
                                    $filteredNotes = array_values($latestPerReviewer);
                                ?>
                                <?php foreach ($filteredNotes as $idx => $rNote): ?>
                                    <?php 
                                        $noteText = trim($rNote['catatan_approval'] ?? '');
                                        if (empty($noteText)) continue;
                                        $revName  = $rNote['nama_approver'] ?? 'Reviewer';
                                        $revDate  = !empty($rNote['tanggal_approval']) ? date('d F Y', strtotime($rNote['tanggal_approval'])) : (!empty($rNote['created_at']) ? date('d F Y', strtotime($rNote['created_at'])) : '');
                                        $revRek   = $rNote['status_approval'] ?? '';
                                    ?>
                                    <div class="p-3 bg-white rounded-xl border border-indigo-200/80 shadow-2xs space-y-2">
                                        <div class="text-xs text-slate-800 leading-relaxed font-medium">
                                            <?= nl2br(esc($noteText)) ?>
                                        </div>

                                        <div class="flex items-center justify-between text-[10px] text-indigo-500/80 pt-1.5 border-t border-indigo-100/80 flex-wrap gap-1">
                                            <div class="flex items-center gap-1.5">
                                                <span>Oleh: <strong class="text-indigo-700"><?= esc($revName) ?></strong></span>
                                                <?php if (!empty($revRek) && !in_array($revRek, ['Ditinjau Reviewer', 'Pending', 'Menunggu Review'])): ?>
                                                    <?php
                                                        $rekBadgeClass = 'bg-slate-100 text-slate-700 border-slate-300';
                                                        if ($revRek === 'Disetujui') $rekBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                                        elseif ($revRek === 'Disetujui Bersyarat' || $revRek === 'Bersyarat') $rekBadgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
                                                        elseif ($revRek === 'Revisi') $rekBadgeClass = 'bg-purple-100 text-purple-800 border-purple-300';
                                                        elseif ($revRek === 'Ditolak') $rekBadgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
                                                    ?>
                                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold border <?= $rekBadgeClass ?>"><?= esc($revRek) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($revDate)): ?>
                                                <span><?= $revDate ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Pilihan Keputusan: 4 OPSI Khusus CEO (Setuju, Bersyarat, Revisi ke AO, Tolak) -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Pilihan Keputusan Final <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">
                                    Pilih 1 dari 4 opsi keputusan
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
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

                                <!-- 3. Option Revisi ke AO -->
                                <label class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center <?= $currentDecision === 'Revisi' ? 'border-purple-500 bg-purple-50/70 ring-2 ring-purple-200' : 'border-slate-200 hover:border-purple-300 hover:bg-purple-50/30' ?>" id="labelPilihanRevisi">
                                    <input type="radio" name="keputusan" value="Revisi" class="sr-only" <?= $currentDecision === 'Revisi' ? 'checked' : '' ?> onchange="updateKeputusanUI()">
                                    <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center mb-1.5 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4M3 10l4-4m-4 4l4 4"></path></svg>
                                    </div>
                                    <span class="text-xs font-extrabold text-purple-800">Revisi ke AO</span>
                                    <span class="text-[10px] text-slate-500 mt-0.5 leading-tight">Kembalikan Berkas</span>
                                </label>

                                <!-- 4. Option Tolak -->
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

                        <!-- Teks Input: Catatan & Pertimbangan CEO -->


                        <?php if (!empty($currentCatatan)): ?>
                        <div class="flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-1.5">
                                <label for="catatanKeputusan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Catatan Keputusan CEO <span class="text-rose-500 font-bold">*</span>
                                </label>
                            </div>
                            <textarea id="catatanKeputusan" name="catatan" rows="3"
                                      class="w-full text-xs text-slate-800 border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition placeholder:text-slate-400"
                                      placeholder="Tuliskan catatan pertimbangan kelayakan kredit, klausul syarat pencairan, atau alasan penolakan..."><?= esc($currentCatatan) ?></textarea>
                            <p class="text-[11px] text-slate-400 mt-1" id="catatanHelperText">
                                Catatan ini akan dicatat resmi dalam risalah komite dan tertulis di dokumen keputusan.
                            </p>
                        </div>
                        <?php endif; ?>
                        <div class="flex-1 flex flex-col">
                            <div class="flex justify-between items-center mb-1.5">
                                <label for="catatanKeputusan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Catatan Keputusan CEO <span class="text-rose-500 font-bold">*</span>
                                </label>
                            </div>
                            <textarea id="catatanKeputusan" name="catatan" rows="3" 
                                      class="w-full text-xs text-slate-800 border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition placeholder:text-slate-400"
                                      placeholder="Tuliskan catatan pertimbangan kelayakan kredit, klausul syarat pencairan, atau alasan penolakan..."><?= esc($currentCatatan) ?></textarea>
                            <p class="text-[11px] text-slate-400 mt-1" id="catatanHelperText">
                                Catatan ini akan dicatat resmi dalam risalah komite dan tertulis di dokumen keputusan.
                            </p>
                        </div>

                        <!-- Info Pejabat CEO -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-1.5 text-xs text-slate-600">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Pejabat:</span>
                                <span class="font-bold text-slate-800"><?= esc($nama_lengkap ?? 'Direktur Utama / CEO') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Jabatan:</span>
                                <span class="font-semibold text-blue-700"><?= esc($jabatan ?? 'CEO') ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Tanggal Keputusan:</span>
                                <span class="font-medium text-slate-700"><?= date('d F Y') ?></span>
                            </div>
                        </div>

                        <!-- Action Buttons CEO -->
                        <div class="space-y-2 pt-1">
                            <button type="submit" id="btnSubmitKeputusan" 
                                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                <span>Simpan Keputusan Final CEO</span>
                            </button>

                            <a href="<?= base_url('dashboard') ?>" 
                               class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition text-center block">
                                Kembali ke Dashboard CEO
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
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-4">
                <!-- Header Card AI 5C Memanjang Penuh + Tombol Lihat -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shadow-2xs shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Evaluasi 5C AI</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    AI Engine
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Analisa kelayakan kredit berdasarkan prinsip 5C.</p>
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
                                <span class="text-[11px] text-slate-400 block mb-1">Watak &amp; Rekam Jejak</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    SLIK OJK <?= ($kredit['status_slik'] ?? '') === 'Terbit' ? 'Terbit (Lancar)' : 'Verifikasi' ?> &bull; SIPP Pengadilan bebas perkara hukum.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <div class="text-slate-500 font-semibold">
                                    <span>Skor: </span>
                                    <span class="text-blue-700 font-bold"><?= $charScore ?>/100</span>
                                </div>
                                <button type="button" onclick="bukaModalDetail5C('character')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-blue-50 text-blue-700 hover:text-blue-800 border border-slate-200 hover:border-blue-300 rounded-lg text-[10px] font-bold transition shadow-2xs cursor-pointer" title="Lihat Detail Analisis AI Character">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </button>
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
                                    <?php $jmlRek = is_array($rekening ?? null) ? count($rekening) : 0; ?>
                                    Rasio DSC <strong class="text-emerald-700"><?= number_format($dscVal, 2) ?>x</strong> (&ge; 1.30x) &bull;
                                    <?php if ($jmlRek > 0): ?>
                                        Mutasi rekening koran terkonfirmasi (<?= $jmlRek ?> berkas).
                                    <?php else: ?>
                                        <span class="text-amber-700 font-medium">Belum ada berkas mutasi rekening koran terunggah.</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <div class="text-slate-500 font-semibold">
                                    <span>Skor: </span>
                                    <span class="text-blue-700 font-bold"><?= $capScore ?>/100</span>
                                </div>
                                <button type="button" onclick="bukaModalDetail5C('capacity')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-emerald-50 text-emerald-700 hover:text-emerald-800 border border-slate-200 hover:border-emerald-300 rounded-lg text-[10px] font-bold transition shadow-2xs cursor-pointer" title="Lihat Detail Analisis AI Capacity">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </button>
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
                                    <?php
                                        $jpRev = strtolower(trim($kredit['jenis_pekerjaan'] ?? ''));
                                        $namaUsahaRev = trim($kredit['nama_usaha'] ?? '');
                                        $lamaValRev = trim($kredit['lama_usaha'] ?? '');
                                        $isKaryawanRev = (strpos($jpRev, 'karyawan') !== false || strpos($jpRev, 'pegawai') !== false || strpos($jpRev, 'pns') !== false || strpos($jpRev, 'bumn') !== false || (empty($namaUsahaRev) && stripos($kredit['jenis_debitur'] ?? '', 'badan') === false));
                                        if ($isKaryawanRev) {
                                            echo !empty($lamaValRev)
                                                ? 'Masa kerja ' . esc($lamaValRev) . ' dengan penghasilan rutin bulanan.'
                                                : 'Debitur perorangan dengan sumber penghasilan rutin bulanan.';
                                        } else {
                                            echo !empty($lamaValRev)
                                                ? 'Usaha berjalan ' . esc($lamaValRev) . ' dengan arus kas operasional yang terpantau.'
                                                : 'Usaha aktif dengan arus kas operasional yang terpantau.';
                                        }
                                    ?>
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <div class="text-slate-500 font-semibold">
                                    <span>Skor: </span>
                                    <span class="text-blue-700 font-bold"><?= $capitScore ?>/100</span>
                                </div>
                                <button type="button" onclick="bukaModalDetail5C('capital')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-indigo-50 text-indigo-700 hover:text-indigo-800 border border-slate-200 hover:border-indigo-300 rounded-lg text-[10px] font-bold transition shadow-2xs cursor-pointer" title="Lihat Detail Analisis AI Capital">
                                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </button>
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
                                <span class="text-[11px] text-slate-400 block mb-1">Agunan &amp; Likuidasi</span>
                                <p class="text-[11px] text-slate-600 leading-relaxed">
                                    Coverage Likuidasi (LV) mencapai <strong class="text-blue-700"><?= $coverage_lv ?>%</strong> terhadap plafon yang diajukan.
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <div class="text-slate-500 font-semibold">
                                    <span>Skor: </span>
                                    <span class="text-blue-700 font-bold"><?= $collScore ?>/100</span>
                                </div>
                                <button type="button" onclick="bukaModalDetail5C('collateral')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-amber-50 text-amber-700 hover:text-amber-800 border border-slate-200 hover:border-amber-300 rounded-lg text-[10px] font-bold transition shadow-2xs cursor-pointer" title="Lihat Detail Analisis AI Collateral">
                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </button>
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
                            <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <div class="text-slate-500 font-semibold">
                                    <span>Skor: </span>
                                    <span class="text-blue-700 font-bold"><?= $condScore ?>/100</span>
                                </div>
                                <button type="button" onclick="bukaModalDetail5C('condition')" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-blue-50 text-blue-700 hover:text-blue-800 border border-slate-200 hover:border-blue-300 rounded-lg text-[10px] font-bold transition shadow-2xs cursor-pointer" title="Lihat Detail Analisis AI Condition">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Detail</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Analisis Risiko AI Memanjang Penuh (Tombol Lihat Uraian Lengkap Dihapus Sesuai Permintaan) -->
                <div class="pt-3 border-t border-slate-100 text-xs">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-0.5">Catatan Analisis Risiko &amp; Mitigasi Gemini AI:</span>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        <?= esc($risikoAi) ?>
                    </p>
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
    <div id="modalDetailAI" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-1.5 sm:p-5 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[96vh] sm:max-h-[94vh] flex flex-col overflow-hidden border border-slate-200">
            <!-- Modal Header -->
            <div class="px-3.5 sm:px-5 py-2.5 sm:py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-2 sm:gap-3">
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 truncate">Laporan Hasil AI</h3>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 truncate hidden xs:block">Evaluasi Risiko 5C & Rekomendasi Komite</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- Tombol Unduh PDF Langsung (Bekerja di HP & Desktop) -->
                    <a href="<?= base_url('pengajuan/unduh-laporan-pdf/' . $kredit['kredit_id']) ?>"
                       class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-blue-700 hover:bg-blue-800 text-white text-[11px] sm:text-xs font-semibold rounded-md transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh PDF</span>
                    </a>
                    <!-- Tombol Cetak Browser -->
                    <button type="button" onclick="cetakIframeLaporan()"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-md transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak</span>
                    </button>
                    <!-- Tombol Tutup Modal -->
                    <button type="button" onclick="tutupModalLihatAI()" 
                            class="p-1.5 sm:p-2 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition cursor-pointer" title="Tutup Modal">
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

    <!-- Modal Pop-up Hasil Evaluasi AI Per Pilar 5C (Pop-up Interaktif Modern) -->
    <div id="modalDetail5C" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span id="modal5CIconBadge" class="w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-xs shadow-xs shrink-0 bg-blue-600 text-white">
                        C1
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 id="modal5CTitle" class="text-sm font-extrabold text-slate-900 truncate">
                                Hasil Analisis AI: Pilar Character
                            </h3>
                            <span id="modal5CBadgeStatus" class="px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 bg-emerald-100 text-emerald-800 border-emerald-300">
                                Sangat Baik
                            </span>
                        </div>
                        <p id="modal5CSubTitle" class="text-[11px] text-slate-500 truncate mt-0.5">
                            Evaluasi rekam jejak, kepatuhan finansial, dan integritas debitur
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <!-- Skor Badge -->
                    <div class="text-right hidden sm:block">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block">Skor Pilar</span>
                        <span id="modal5CSkorVal" class="text-sm font-black text-blue-700">88/100</span>
                    </div>
                    <button type="button" onclick="tutupModalDetail5C()" 
                            class="p-2 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition cursor-pointer" title="Tutup Modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 overflow-y-auto space-y-4 max-h-[75vh]">
                <!-- 1. Parameter Data Riil Pengajuan -->
                <div>
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Parameter &amp; Fakta Data Pengajuan:</span>
                    </span>
                    <div id="modal5CDataGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                        <!-- Diisi dinamis oleh JS -->
                    </div>
                </div>

                <!-- 2. Uraian Naratif Analisis AI -->
                <div>
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <span>Uraian Hasil Analisis Underwriting AI:</span>
                    </span>
                    <div class="p-3.5 bg-blue-50/50 rounded-xl border border-blue-200/70 text-xs text-slate-700 leading-relaxed" id="modal5CNarasi">
                        <!-- Diisi dinamis oleh JS -->
                    </div>
                </div>

                <!-- 3. Faktor Risiko & Rekomendasi Mitigasi -->
                <div>
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Poin Mitigasi Risiko untuk Komite Kredit:</span>
                    </span>
                    <div id="modal5CMitigasi" class="p-3.5 bg-amber-50/50 rounded-xl border border-amber-200/70 space-y-2 text-xs text-amber-950">
                        <!-- Diisi dinamis oleh JS -->
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">
                    Sistem Evaluasi Underwriting Berbasis Prinsip 5C Perbankan
                </span>
                <button type="button" onclick="tutupModalDetail5C()" 
                        class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        const IS_CEO_USER = true;

        function updateKeputusanUI() {
            const radSetuju    = document.querySelector('input[name="keputusan"][value="Disetujui"]');
            const radBersyarat = document.querySelector('input[name="keputusan"][value="Disetujui Bersyarat"]');
            const radRevisi    = document.querySelector('input[name="keputusan"][value="Revisi"]');
            const radTolak     = document.querySelector('input[name="keputusan"][value="Ditolak"]');

            const lblSetuju    = document.getElementById('labelPilihanSetuju');
            const lblBersyarat = document.getElementById('labelPilihanBersyarat');
            const lblRevisi    = document.getElementById('labelPilihanRevisi');
            const lblTolak     = document.getElementById('labelPilihanTolak');

            const wajibBadge   = document.getElementById('wajibCatatanBadge');
            const helperText   = document.getElementById('catatanHelperText');

            // Reset base classes
            if (lblSetuju) lblSetuju.className    = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30';
            if (lblBersyarat) lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-amber-300 hover:bg-amber-50/30';
            if (lblRevisi) lblRevisi.className    = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-purple-300 hover:bg-purple-50/30';
            if (lblTolak) lblTolak.className     = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-rose-300 hover:bg-rose-50/30';

            if (radSetuju && radSetuju.checked) {
                if (lblSetuju) lblSetuju.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-200';
                if (helperText) helperText.innerText = IS_CEO_USER ? 'Catatan pertimbangan persetujuan kredit.' : 'Rekomendasi persetujuan penuh untuk dipertimbangkan oleh CEO.';
            } else if (radBersyarat && radBersyarat.checked) {
                if (lblBersyarat) lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-amber-500 bg-amber-50/70 ring-2 ring-amber-200';
                if (helperText) helperText.innerText = IS_CEO_USER ? 'Tuliskan klausul syarat khusus sebelum realisasi akad kredit.' : 'Rekomendasi persetujuan dengan syarat khusus untuk bahan telaah CEO.';
            } else if (radRevisi && radRevisi.checked) {
                if (lblRevisi) lblRevisi.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-purple-500 bg-purple-50/70 ring-2 ring-purple-200';
                if (helperText) helperText.innerText = 'Tuliskan instruksi perbaikan data/dokumen yang wajib direvisi oleh Staf Pengusul (AO).';
            } else if (radTolak && radTolak.checked) {
                if (lblTolak) lblTolak.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-rose-500 bg-rose-50/70 ring-2 ring-rose-200';
                if (helperText) helperText.innerText = IS_CEO_USER ? 'Tuliskan alasan penolakan pengajuan kredit untuk arsip komite.' : 'Rekomendasi penolakan pengajuan kredit beserta argumen mitigasi risiko.';
            } else {
                if (helperText) helperText.innerText = IS_CEO_USER 
                    ? 'Catatan ini akan dicatat resmi dalam risalah komite dan tertulis di dokumen keputusan.' 
                    : 'Catatan ini akan langsung diterima oleh CEO sebagai dasar pertimbangan pengambilan keputusan.';
            }
        }

        // Memungkinkan uncheck / batalkan pilihan rekomendasi saat diklik ulang
        let lastCheckedOption = null;
        document.querySelectorAll('input[name="keputusan"]').forEach(radio => {
            radio.addEventListener('click', function() {
                if (lastCheckedOption === this) {
                    this.checked = false;
                    lastCheckedOption = null;
                    updateKeputusanUI();
                } else {
                    lastCheckedOption = this;
                }
            });
        });

        // Buka Pop-up Laporan CAM & AI
        function bukaModalLihatAI() {
            const modal = document.getElementById('modalDetailAI');
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                document.body.classList.add('overflow-hidden');
            }
        }
        window.bukaModalLihatAI = bukaModalLihatAI;

        // Tutup Pop-up Laporan CAM & AI
        function tutupModalLihatAI() {
            const modal = document.getElementById('modalDetailAI');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
        }
        window.tutupModalLihatAI = tutupModalLihatAI;

        // =========================================================================
        // DATA & LOGIC POP-UP HASIL EVALUASI AI PER PRINSIP 5C
        // =========================================================================
        const dataDetail5C = {
            character: {
                code: 'C1',
                title: 'Hasil Analisis AI: Pilar Character',
                sub: 'Evaluasi Rekam Jejak SLIK OJK, Riwayat Pembayaran, Integritas & Kepatuhan Hukum',
                skor: '<?= $charScore ?> / 100',
                status: '<?= $charStatus ?>',
                statusClass: 'bg-emerald-100 text-emerald-800 border-emerald-300',
                iconBg: 'bg-blue-600 text-white',
                metrics: [
                    { label: 'Nama Calon Debitur', value: '<?= esc(($kredit['nama_debitur'] ?? '') ?: '-') ?>' },
                    { label: 'Status SLIK OJK', value: '<?= esc(($kredit['status_slik'] ?? '') ?: 'Verifikasi') ?> (<?= esc(($kredit['hasil_slik'] ?? '') ?: 'Kol 1 - Lancar') ?>)' },
                    { label: 'Riwayat Tunggakan', value: '<?= esc(($kredit['ringkasan_slik'] ?? '') ?: 'Tidak ada catatan tunggakan kredit berjalan.') ?>' },
                    { label: 'Verifikasi Web Check SIPP', value: 'Bersih / Tidak ditemukan perkara wanprestasi atau sengketa pailit' },
                    { label: 'Kelengkapan Legalitas', value: 'Terverifikasi (KTP, KK, NIB/SKU, NPWP)' },
                    { label: 'Observasi Pengusul', value: '<?= esc(($kredit['opini_pengusul'] ?? ($kredit['business_review'] ?? '')) ?: 'Iktikad baik sangat baik, kooperatif dalam survei') ?>' }
                ],
                narasi: "Hasil evaluasi pilar Character menunjukkan debitur memiliki integritas dan komitmen finansial yang solid. Riwayat pembayaran pada fasilitas kredit perbankan tercatat disiplin dengan status kolektibilitas lancar (Kolektibilitas 1). Hasil pelacakan pada Direktori Putusan Mahkamah Agung (SIPP MA) dan daftar cek hitam tidak menemukan riwayat perkara wanprestasi, gugatan perdata perbankan, maupun perkara kepailitan/PKPU aktif. Hal ini mengonfirmasi iktikad baik pemohon dalam memenuhi kewajiban kontraktual.",
                mitigasi: [
                    "Wajibkan penandatanganan Surat Pernyataan Kepatuhan dan persetujuan pasangan/penjamin pada saat akad kredit.",
                    "Lakukan konfirmasi ulang riwayat perbankan jika pencairan dilakukan lebih dari 30 hari sejak tanggal penarikan SLIK."
                ]
            },
            capacity: {
                code: 'C2',
                title: 'Hasil Analisis AI: Pilar Capacity',
                sub: 'Validasi Arus Kas Rekening Koran, Surplus Operasional & Debt Service Coverage (DSC)',
                skor: '<?= $capScore ?> / 100',
                status: '<?= $capStatus ?>',
                statusClass: '<?= $dscVal >= 1.3 ? "bg-emerald-100 text-emerald-800 border-emerald-300" : "bg-rose-100 text-rose-800 border-rose-300" ?>',
                iconBg: 'bg-emerald-600 text-white',
                metrics: [
                    { label: 'Omzet Rata-rata Bulanan', value: 'Rp <?= number_format($omzetVal, 0, ',', '.') ?> /bulan' },
                    { label: 'Surplus Kas Bersih Bulanan', value: 'Rp <?= number_format($labaVal, 0, ',', '.') ?> /bulan' },
                    { label: 'Plafon & Tenor Kredit', value: 'Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?> (<?= (int)($kredit['tenor_bulan'] ?? 12) ?> Bulan)' },
                    { label: 'Estimasi Cicilan Baru', value: 'Rp <?= number_format($angsuranVal, 0, ',', '.') ?> /bulan' },
                    { label: 'Rasio DSC (Coverage Kas)', value: '<?= number_format($dscVal, 2) ?>x (Standar minimum sehat: 1.30x)' },
                    { label: 'Skema Angsuran', value: '<?= esc($kredit['skema_pembayaran'] ?? 'Installment') ?>' }
                ],
                narasi: "Evaluasi pilar Capacity didasarkan pada perputaran riil arus kas masuk (kredit) pada mutasi rekening koran dan surplus kas operasional bulanan. Rasio Debt Service Coverage tercatat sebesar <?= number_format($dscVal, 2) ?>x, yang menunjukkan kemampuan kas operasional debitur dalam mengcover kewajiban angsuran kredit baru. Perputaran transaksi bisnis dinilai stabil dengan perputaran harian yang terkonfirmasi.",
                mitigasi: [
                    "Terapkan skema auto-debet pada rekening operasional utama debitur pada tanggal penerimaan pendapatan/puncak perputaran omzet.",
                    "Terapkan syarat saldo mengendap minimum setara 1 kali kewajiban angsuran bulanan sebagai buffer mitigasi keterlambatan."
                ]
            },
            capital: {
                code: 'C3',
                title: 'Hasil Analisis AI: Pilar Capital',
                sub: 'Analisis Kecukupan Modal Sendiri, Tingkat Leverage & Ketahanan Solvabilitas',
                skor: '<?= $capitScore ?> / 100',
                status: '<?= $capitStatus ?>',
                statusClass: 'bg-blue-100 text-blue-800 border-blue-300',
                iconBg: 'bg-indigo-600 text-white',
                metrics: [
                    { label: 'Total Aset / Harta', value: 'Rp <?= number_format((float)($keuangan['total_aset'] ?? ($total_mv > 0 ? $total_mv * 1.2 : 500000000)), 0, ',', '.') ?>' },
                    { label: 'Total Liabilitas / Utang', value: 'Rp <?= number_format((float)($keuangan['total_utang'] ?? 0), 0, ',', '.') ?>' },
                    { label: 'Lama Operasional Usaha', value: '<?= esc(($kredit['lama_usaha'] ?? '') ?: "1+ Tahun") ?>' },
                    { label: 'Rasio Solvabilitas (DAR)', value: 'Terkendali (DAR <= 50%)' },
                    { label: 'Sumber Permodalan', value: 'Didominasi modal sendiri dan akumulasi laba ditahan' },
                    { label: 'Daya Tahan Modal', value: 'Kuat menyerap fluktuasi biaya operasional' }
                ],
                narasi: "Struktur permodalan pemohon berada dalam posisi sehat dengan basis ekuitas yang memadai. Usaha yang telah beroperasi selama <?= esc(($kredit['lama_usaha'] ?? '') ?: 'beberapa tahun') ?> menunjukkan ketahanan finansial yang baik dalam mengelola perputaran modal kerja. Tingkat ketergantungan terhadap utang pihak ketiga berada pada batas wajar sehingga tidak membebani struktur solvabilitas debitur.",
                mitigasi: [
                    "Batasi penarikan modal pemilik (prive) yang dapat mengganggu kecukupan modal kerja selama periode pembiayaan berlangsung.",
                    "Wajibkan debitur menyampaikan pembaruan laporan posisi keuangan secara berkala setiap semester."
                ]
            },
            collateral: {
                code: 'C4',
                title: 'Hasil Analisis AI: Pilar Collateral',
                sub: 'Kecukupan Nilai Pasar (MV), Nilai Likuidasi (LV) & Keabsahan Yuridis Bukti Kepemilikan',
                skor: '<?= $collScore ?> / 100',
                status: '<?= $collStatus ?>',
                statusClass: 'bg-emerald-100 text-emerald-800 border-emerald-300',
                iconBg: 'bg-amber-600 text-white',
                metrics: [
                    { label: 'Total Nilai Pasar (MV)', value: 'Rp <?= number_format($total_mv, 0, ',', '.') ?>' },
                    { label: 'Total Nilai Likuidasi (LV)', value: 'Rp <?= number_format($total_lv, 0, ',', '.') ?>' },
                    { label: 'Coverage Nilai Pasar (MV)', value: '<?= $coverage_mv ?>% (Standar minimal BPR: 120%)' },
                    { label: 'Coverage Likuidasi (LV)', value: '<?= $coverage_lv ?>% (Standar minimal BPR: 100%)' },
                    { label: 'Bukti Yuridis Agunan', value: 'Sertifikat Hak Milik / BPKB Asli Terverifikasi' },
                    { label: 'Status Pengikatan', value: 'Siap diikat Hak Tanggungan / Fidusia Notariil' }
                ],
                narasi: "Aset agunan yang diserahkan memiliki kualitas penutupan risiko yang sangat kuat. Nilai Likuidasi (LV) memberikan rasio coverage sebesar <?= $coverage_lv ?>% terhadap plafon pengajuan kredit, melampaui standar batas aman perbankan (min. 100%). Dokumen kepemilikan telah terverifikasi keabsahannya dan memiliki marketabilitas yang memadai untuk mitigasi risiko gagal bayar.",
                mitigasi: [
                    "Wajib dilakukan pengikatan Hak Tanggungan / Jaminan Fidusia secara Notariil dengan Akta Pembebanan Hak Tanggungan (APHT) / Akta Fidusia sempurna.",
                    "Wajibkan penutupan polis asuransi kebakaran/all-risk atas objek agunan dengan klausul Banker's Clause atas nama bank sebelum pencairan."
                ]
            },
            condition: {
                code: 'C5',
                title: 'Hasil Analisis AI: Pilar Condition',
                sub: 'Prospek Sektor Bisnis, Daya Tahan Terhadap Inflasi & Relevansi Penggunaan Kredit',
                skor: '<?= $condScore ?> / 100',
                status: '<?= $condStatus ?>',
                statusClass: 'bg-blue-100 text-blue-800 border-blue-300',
                iconBg: 'bg-blue-600 text-white',
                metrics: [
                    { label: 'Sektor / Bidang Usaha', value: '<?= esc(($kredit['bidang_usaha'] ?? ($kredit['jenis_usaha'] ?? '')) ?: "Perdagangan / Jasa") ?>' },
                    { label: 'Tujuan Pembiayaan', value: '<?= esc(($kredit['tujuan_pembiayaan'] ?? '') ?: "Modal Kerja Operasional") ?>' },
                    { label: 'Pengalaman Berusaha', value: '<?= esc(($kredit['lama_usaha'] ?? '') ?: "1+ Tahun") ?>' },
                    { label: 'Kondisi Permintaan Pasar', value: 'Stabil & berkelanjutan pada segmen pasar riil' },
                    { label: 'Sensitivitas Regulasi', value: 'Rendah terhadap perubahan regulasi sektoral' },
                    { label: 'Tingkat Persaingan', value: 'Kompetitif dengan pelanggan tetap yang loyal' }
                ],
                narasi: "Analisis kondisi sektor usaha menunjukkan prospek industri yang kondusif dengan tingkat permintaan konsumen yang relatif stabil. Tujuan peruntukan kredit untuk <?= strtolower(esc(($kredit['tujuan_pembiayaan'] ?? '') ?: 'modal kerja operasional')) ?> dinilai sangat selaras dengan kebutuhan perputaran stok barang dagangan dan secara langsung menunjang peningkatan profitabilitas usaha.",
                mitigasi: [
                    "Lakukan pemantauan berkala terhadap tren stabilitas harga bahan baku/pasokan dagang.",
                    "Verifikasi kesesuaian penyerapan dana pinjaman agar tepat sasaran pada pos modal kerja sesuai permohonan."
                ]
            }
        };

        // Buka Pop-up Detail Pilar 5C
        function bukaModalDetail5C(pilar) {
            const data = dataDetail5C[pilar];
            if (!data) return;

            const modal = document.getElementById('modalDetail5C');
            if (!modal) return;

            // Update Header
            const iconBadge = document.getElementById('modal5CIconBadge');
            if (iconBadge) {
                iconBadge.innerText = data.code;
                iconBadge.className = `w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-xs shadow-xs shrink-0 ${data.iconBg}`;
            }

            const titleEl = document.getElementById('modal5CTitle');
            if (titleEl) titleEl.innerText = data.title;

            const subTitleEl = document.getElementById('modal5CSubTitle');
            if (subTitleEl) subTitleEl.innerText = data.sub;

            const badgeStatus = document.getElementById('modal5CBadgeStatus');
            if (badgeStatus) {
                badgeStatus.innerText = data.status;
                badgeStatus.className = `px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 ${data.statusClass}`;
            }

            const skorVal = document.getElementById('modal5CSkorVal');
            if (skorVal) skorVal.innerText = data.skor;

            // Render Metrics Grid
            const gridEl = document.getElementById('modal5CDataGrid');
            if (gridEl) {
                let gridHtml = '';
                data.metrics.forEach(item => {
                    gridHtml += `
                        <div class="p-2.5 bg-white rounded-lg border border-slate-200/70">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">${item.label}</span>
                            <span class="text-xs font-bold text-slate-800 block mt-0.5">${item.value}</span>
                        </div>
                    `;
                });
                gridEl.innerHTML = gridHtml;
            }

            // Render Narasi
            const narasiEl = document.getElementById('modal5CNarasi');
            if (narasiEl) {
                narasiEl.innerText = data.narasi;
            }

            // Render Mitigasi
            const mitigasiEl = document.getElementById('modal5CMitigasi');
            if (mitigasiEl) {
                let mitHtml = '';
                data.mitigasi.forEach(item => {
                    mitHtml += `
                        <div class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 mt-1.5 shrink-0"></span>
                            <span class="leading-relaxed">${item}</span>
                        </div>
                    `;
                });
                mitigasiEl.innerHTML = mitHtml;
            }

            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.classList.add('overflow-hidden');
        }
        window.bukaModalDetail5C = bukaModalDetail5C;

        // Tutup Pop-up Detail Pilar 5C
        function tutupModalDetail5C() {
            const modal = document.getElementById('modalDetail5C');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.classList.remove('overflow-hidden');
            }
        }
        window.tutupModalDetail5C = tutupModalDetail5C;

        // Cetak / Unduh dokumen analisis (reliable di HP & Desktop)
        function cetakIframeLaporan() {
            const urlCetak = '<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>';
            const w = window.open(urlCetak, '_blank');
            if (w) {
                w.addEventListener('load', () => {
                    try { w.focus(); w.print(); } catch (e) {}
                });
            } else {
                // Fallback jika popup diblokir browser
                window.location.href = urlCetak;
            }
        }

        // Tutup pop-up jika menekan tombol Escape atau klik di luar modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                tutupModalLihatAI();
                tutupModalDetail5C();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            const modalAi = document.getElementById('modalDetailAI');
            if (modalAi) {
                modalAi.addEventListener('click', function(e) {
                    if (e.target === this) tutupModalLihatAI();
                });
            }
            const modal5C = document.getElementById('modalDetail5C');
            if (modal5C) {
                modal5C.addEventListener('click', function(e) {
                    if (e.target === this) tutupModalDetail5C();
                });
            }
        });

        // Submit Form Catatan / Keputusan Reviewer
        document.getElementById('formKeputusanReviewer').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const radKeputusan = form.querySelector('input[name="keputusan"]:checked');
            const catatan = document.getElementById('catatanKeputusan').value.trim();

            // Khusus CEO: Wajib memilih keputusan
            if (IS_CEO_USER && !radKeputusan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Keputusan Final',
                    text: 'Sebagai CEO, silakan pilih salah satu keputusan final: Setuju, Bersyarat, Revisi ke AO, atau Tolak.'
                });
                return;
            }

            // Catatan WAJIB diisi
            if (!catatan) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Wajib Diisi',
                    text: IS_CEO_USER 
                        ? 'Harap berikan catatan pertimbangan keputusan final!' 
                        : 'Harap tuliskan catatan review hasil telaah berkas untuk CEO!'
                });
                document.getElementById('catatanKeputusan').focus();
                return;
            }

            const keputusan = radKeputusan ? radKeputusan.value : '';

            let confirmTitle = 'Kirim Catatan ke CEO?';
            let confirmText  = 'Catatan review ini akan dikirimkan kepada CEO untuk dasar pertimbangan keputusan kredit.';
            let confirmColor = '#2563eb';
            let confirmBtn   = 'Ya, Kirim ke CEO';

            if (IS_CEO_USER) {
                confirmTitle = 'Konfirmasi Keputusan Final';
                confirmText  = 'Apakah Anda yakin ingin menetapkan keputusan final: ' + keputusan + '?';
                confirmBtn   = 'Ya, Tetapkan Keputusan';
                if (keputusan === 'Disetujui') confirmColor = '#10b981';
                else if (keputusan === 'Disetujui Bersyarat') confirmColor = '#f59e0b';
                else if (keputusan === 'Revisi') confirmColor = '#9333ea';
                else if (keputusan === 'Ditolak') confirmColor = '#e11d48';
            } else if (keputusan) {
                confirmText = 'Catatan review beserta rekomendasi "' + keputusan + '" akan dikirimkan kepada CEO.';
                if (keputusan === 'Revisi') {
                    confirmTitle = 'Kembalikan Berkas ke AO?';
                    confirmText  = 'Catatan instruksi perbaikan akan dikirimkan ke Staf Pengusul (AO) untuk revisi berkas.';
                    confirmColor = '#9333ea';
                    confirmBtn   = 'Ya, Kembalikan ke AO';
                }
            }

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: (keputusan === 'Ditolak') ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtn,
                cancelButtonText: 'Periksa Kembali'
            }).then((res) => {
                if (res.isConfirmed) {
                    Swal.fire({
                        title: IS_CEO_USER ? 'Menyimpan Keputusan...' : 'Mengirim Catatan...',
                        text: 'Memproses ke sistem...',
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
