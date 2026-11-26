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
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 4px 6px; vertical-align: top; }
        th { background-color: #f8fafc; font-weight: 700; text-transform: uppercase; font-size: 9.5px; color: #334155; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Approved';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : 'img/logo-oap.png?v=2';

    $statusAplikasi  = $kredit['status_pengajuan'] ?? 'Draft';
    $userRole        = strtolower(session()->get('jabatan') ?? '');
    $isCeoRole       = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false);

    $isFinalized = in_array($statusAplikasi, ['Disetujui', 'Disetujui Bersyarat', 'Bersyarat', 'Ditolak', 'Revisi']) 
                   || !empty($keputusan_ceo);

    $currentDecision = !empty($keputusan_ceo['status_approval']) ? $keputusan_ceo['status_approval'] : (!empty($keputusan_reviewer['status_approval']) ? $keputusan_reviewer['status_approval'] : '');
    $currentCatatan  = !empty($keputusan_ceo['catatan_approval']) ? $keputusan_ceo['catatan_approval'] : ($keputusan_reviewer['catatan_approval'] ?? '');
    if ($currentDecision === 'Bersyarat') $currentDecision = 'Disetujui Bersyarat';
?>

    <!-- Top Bar Navigation -->
    <header class="bg-white border-t-2 border-t-blue-700 border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 sm:h-16 items-center gap-3">
                <!-- Tombol Kembali Saja Sesuai Permintaan User -->
                <a href="<?= base_url('dashboard') ?>" class="inline-flex items-center gap-2 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition shadow-2xs" title="Kembali ke Dashboard">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </a>

                <!-- Right Actions: Cetak, Unduh PDF & Status Badge -->
                <div class="flex items-center gap-2 shrink-0">
                    <a href="<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span class="hidden sm:inline">Lihat Laporan 10 Bab</span>
                        <span class="sm:hidden">Lihat</span>
                    </a>

                    <a href="<?= base_url('pengajuan/cetak/' . $kredit['kredit_id']) ?>?print=1" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#0284c7] hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition shadow-2xs" title="Unduh atau Simpan sebagai PDF">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh PDF (10 Bab)</span>
                    </a>

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
    <main class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex-1 min-w-0 space-y-6">

        <!-- ============================================================== -->
        <!-- PANEL KEPUTUSAN / CATATAN REVIEWER UNTUK CEO                  -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header Panel -->
            <div class="p-4 sm:p-5 border-b border-blue-100 bg-blue-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full <?= $isFinalized ? 'bg-emerald-500' : 'bg-blue-600 animate-pulse' ?>"></span>
                    <div>
                        <h2 class="text-sm sm:text-base font-extrabold text-blue-950 uppercase tracking-wider">
                            Lembar Catatan Reviewer & Rekomendasi ke CEO
                        </h2>
                        <p class="text-xs text-blue-800/80 mt-0.5">
                            <?= $isFinalized ? 'Pemeriksaan review telah selesai. Keputusan tercatat resmi pada risalah komite.' : 'Silakan pelajari laporan analisa kelayakan 10 bab di bawah ini secara lengkap, lalu kirimkan catatan hasil review ke CEO.' ?>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('bodyPanelReviewer').classList.toggle('hidden')"
                            class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 transition shadow-2xs">
                        Tutup / Buka Form Catatan
                    </button>
                </div>
            </div>

            <!-- Body Panel Reviewer -->
            <div id="bodyPanelReviewer" class="p-4 sm:p-6 space-y-5">
                <?php 
                    $daftarCatatan = $daftar_catatan_reviewer ?? [];
                ?>

                <?php if ($isFinalized): ?>
                    <!-- TAMPILAN READ-ONLY: KEPUTUSAN TELAH FINAL -->
                    <div class="p-4 rounded-xl border bg-slate-50 border-slate-200 text-slate-800 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Keputusan Resmi Final Komite:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-900 border border-blue-200">
                                <?= strtoupper(esc($currentDecision ?: $statusAplikasi)) ?>
                            </span>
                        </div>
                        <?php if (!empty($currentCatatan)): ?>
                            <div class="p-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-700 leading-relaxed">
                                <strong>Catatan Final:</strong> <?= nl2br(esc($currentCatatan)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- RIWAYAT CATATAN TELAAH DARI SELURUH TIM REVIEWER UNTUK CEO -->
                <?php if (!empty($daftarCatatan)): ?>
                    <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/60 space-y-3">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-indigo-950 uppercase tracking-wider">
                                        Catatan Telaah Tim Reviewer untuk CEO (<?= count($daftarCatatan) ?> Catatan)
                                    </h4>
                                    <p class="text-[11px] text-indigo-700/80">
                                        Kumpulan catatan dan rekomendasi dari seluruh reviewer yang telah diteruskan ke meja kerja CEO
                                    </p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                Diskusi Tim Reviewer Aktif
                            </span>
                        </div>

                        <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                            <?php foreach ($daftarCatatan as $idx => $rNote): ?>
                                <?php 
                                    $noteText = trim($rNote['catatan_approval'] ?? '');
                                    if (empty($noteText)) continue;
                                    $revName  = $rNote['nama_approver'] ?? 'Senior Credit Reviewer';
                                    $revDate  = !empty($rNote['tanggal_approval']) ? date('d F Y', strtotime($rNote['tanggal_approval'])) : (!empty($rNote['created_at']) ? date('d F Y, H:i', strtotime($rNote['created_at'])) . ' WIB' : '');
                                    $revRek   = $rNote['status_approval'] ?? '';
                                ?>
                                <div class="p-3.5 bg-white rounded-xl border border-indigo-200/80 shadow-2xs space-y-2">
                                    <div class="flex items-center justify-between text-[11px] pb-1.5 border-b border-slate-100 flex-wrap gap-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center">
                                                <?= strtoupper(substr($revName, 0, 1)) ?>
                                            </span>
                                            <strong class="text-slate-800"><?= esc($revName) ?></strong>
                                            <?php if (!empty($revRek) && !in_array($revRek, ['Ditinjau Reviewer', 'Pending', 'Menunggu Review'])): ?>
                                                <?php
                                                    $rekBadgeClass = 'bg-slate-100 text-slate-700 border-slate-300';
                                                    if ($revRek === 'Disetujui') $rekBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                                    elseif ($revRek === 'Disetujui Bersyarat' || $revRek === 'Bersyarat') $rekBadgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
                                                    elseif ($revRek === 'Revisi') $rekBadgeClass = 'bg-purple-100 text-purple-800 border-purple-300';
                                                    elseif ($revRek === 'Ditolak') $rekBadgeClass = 'bg-rose-100 text-rose-800 border-rose-300';
                                                ?>
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold border <?= $rekBadgeClass ?>">
                                                    Rekomendasi: <?= esc($revRek) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($revDate)): ?>
                                            <span class="text-slate-400 text-[10px]"><?= $revDate ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs text-slate-700 leading-relaxed font-normal">
                                        <?= nl2br(esc($noteText)) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$isFinalized): ?>
                    <!-- FORM INPUT CATATAN BARU REVIEWER UNTUK CEO -->
                    <form id="formKeputusanReviewer" action="<?= base_url('reviewer/simpan-keputusan') ?>" method="POST" class="space-y-4 pt-1">
                        <input type="hidden" name="kredit_id" value="<?= esc($kredit['kredit_id']) ?>">

                        <div class="flex items-center gap-2 pb-1 border-b border-slate-200">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">
                                <?= !empty($daftarCatatan) ? 'Tambah Catatan / Masukan Tambahan ke CEO' : 'Kirim Catatan Reviewer Pertama ke CEO' ?>
                            </h4>
                        </div>

                        <!-- Pilihan Rekomendasi (Opsional untuk Reviewer) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Rekomendasi Keputusan untuk CEO (Opsional):
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <label id="labelPilihanSetuju" class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30">
                                    <input type="radio" name="keputusan" value="Disetujui" class="sr-only" onchange="updateKeputusanUI()">
                                    <span class="font-bold text-xs text-slate-800">Setujui</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">Rekomendasi Setuju</span>
                                </label>
                                <label id="labelPilihanBersyarat" class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-amber-300 hover:bg-amber-50/30">
                                    <input type="radio" name="keputusan" value="Disetujui Bersyarat" class="sr-only" onchange="updateKeputusanUI()">
                                    <span class="font-bold text-xs text-slate-800">Bersyarat</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">Syarat Tertentu</span>
                                </label>
                                <label id="labelPilihanRevisi" class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-purple-300 hover:bg-purple-50/30">
                                    <input type="radio" name="keputusan" value="Revisi" class="sr-only" onchange="updateKeputusanUI()">
                                    <span class="font-bold text-xs text-slate-800">Revisi ke AO</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">Perbaikan Data</span>
                                </label>
                                <label id="labelPilihanTolak" class="cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-rose-300 hover:bg-rose-50/30">
                                    <input type="radio" name="keputusan" value="Ditolak" class="sr-only" onchange="updateKeputusanUI()">
                                    <span class="font-bold text-xs text-slate-800">Tolak</span>
                                    <span class="text-[10px] text-slate-400 mt-0.5">Risiko Tinggi</span>
                                </label>
                            </div>
                        </div>

                        <!-- Textarea Catatan -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label for="catatanKeputusan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Catatan / Masukan Telaah untuk CEO <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Wajib diisi</span>
                            </div>
                            <textarea id="catatanKeputusan" name="catatan" rows="4" 
                                      class="w-full bg-white text-xs text-slate-800 border border-slate-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-600 transition placeholder:text-slate-400"
                                      placeholder="Tuliskan catatan hasil telaah berkas, pertimbangan mitigasi risiko 5C, kapasitas repayment (DSC), atau catatan tambahan untuk CEO..."></textarea>
                            <p class="text-[11px] text-slate-400 mt-1" id="catatanHelperText">
                                Catatan ini akan dicatat atas nama Anda dan langsung diteruskan ke meja kerja CEO sebagai dasar pertimbangan komite.
                            </p>
                        </div>

                        <!-- Footer Form: Info Petugas & Tombol Kirim -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-slate-200">
                            <div class="text-xs text-slate-500">
                                Reviewer: <strong><?= esc($nama_lengkap ?? 'Senior Credit Reviewer') ?></strong> &bull; <?= date('d F Y') ?>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="submit" id="btnSubmitKeputusan" 
                                        class="w-full sm:w-auto py-2.5 px-6 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-sm hover:shadow flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    <span><?= !empty($daftarCatatan) ? 'Kirim Catatan Tambahan ke CEO' : 'Kirim Catatan ke CEO' ?></span>
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- DOKUMEN LAPORAN ANALISA KELAYAKAN KREDIT LENGKAP 10 BAB        -->
        <!-- ============================================================== -->
        <div class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-1 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 uppercase tracking-wider">
                        Laporan Analisa Kelayakan Kredit dan Rekomendasi Komite Kredit (10 Bab Lengkap)
                    </h3>
                </div>
                <div class="text-[11px] text-slate-500 font-medium">
                    Sesuai Format Resmi Draft Review Independen Berbasis Metode 5C dan Credit Scoring
                </div>
            </div>

            <!-- RENDER 10 BAB LENGKAP -->
            <?php include APPPATH . 'Views/reviewer/komponen_laporan_10_bab.php'; ?>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Sistem Evaluasi & Keputusan Komite Kredit
        </div>
    </footer>

    <script>
        const IS_CEO_USER = false;

        function updateKeputusanUI() {
            const radSetuju    = document.querySelector('input[name="keputusan"][value="Disetujui"]');
            const radBersyarat = document.querySelector('input[name="keputusan"][value="Disetujui Bersyarat"]');
            const radRevisi    = document.querySelector('input[name="keputusan"][value="Revisi"]');
            const radTolak     = document.querySelector('input[name="keputusan"][value="Ditolak"]');

            const lblSetuju    = document.getElementById('labelPilihanSetuju');
            const lblBersyarat = document.getElementById('labelPilihanBersyarat');
            const lblRevisi    = document.getElementById('labelPilihanRevisi');
            const lblTolak     = document.getElementById('labelPilihanTolak');

            const helperText   = document.getElementById('catatanHelperText');

            if (lblSetuju) lblSetuju.className    = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30';
            if (lblBersyarat) lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-amber-300 hover:bg-amber-50/30';
            if (lblRevisi) lblRevisi.className    = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-purple-300 hover:bg-purple-50/30';
            if (lblTolak) lblTolak.className     = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-slate-200 hover:border-rose-300 hover:bg-rose-50/30';

            if (radSetuju && radSetuju.checked) {
                if (lblSetuju) lblSetuju.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-emerald-500 bg-emerald-50/70 ring-2 ring-emerald-200';
                if (helperText) helperText.innerText = 'Rekomendasi persetujuan penuh untuk dipertimbangkan oleh CEO.';
            } else if (radBersyarat && radBersyarat.checked) {
                if (lblBersyarat) lblBersyarat.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-amber-500 bg-amber-50/70 ring-2 ring-amber-200';
                if (helperText) helperText.innerText = 'Rekomendasi persetujuan dengan syarat khusus untuk bahan telaah CEO.';
            } else if (radRevisi && radRevisi.checked) {
                if (lblRevisi) lblRevisi.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-purple-500 bg-purple-50/70 ring-2 ring-purple-200';
                if (helperText) helperText.innerText = 'Tuliskan instruksi perbaikan data/dokumen yang wajib direvisi oleh Staf Pengusul (AO).';
            } else if (radTolak && radTolak.checked) {
                if (lblTolak) lblTolak.className = 'cursor-pointer relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition select-none text-center border-rose-500 bg-rose-50/70 ring-2 ring-rose-200';
                if (helperText) helperText.innerText = 'Rekomendasi penolakan pengajuan kredit beserta argumen mitigasi risiko.';
            } else {
                if (helperText) helperText.innerText = 'Catatan ini akan langsung diterima oleh CEO sebagai dasar pertimbangan pengambilan keputusan.';
            }
        }

        // Uncheck radio button jika diklik ulang
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

        // Submit Form Reviewer
        const formRev = document.getElementById('formKeputusanReviewer');
        if (formRev) {
            formRev.addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const radKeputusan = form.querySelector('input[name="keputusan"]:checked');
                const catatan = document.getElementById('catatanKeputusan').value.trim();

                if (!catatan) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Catatan Wajib Diisi',
                        text: 'Harap tuliskan catatan review hasil telaah berkas untuk CEO!'
                    });
                    document.getElementById('catatanKeputusan').focus();
                    return;
                }

                const keputusan = radKeputusan ? radKeputusan.value : '';

                let confirmTitle = 'Kirim Catatan ke CEO?';
                let confirmText  = 'Catatan review ini akan dikirimkan kepada CEO untuk dasar pertimbangan keputusan kredit.';
                let confirmColor = '#2563eb';
                let confirmBtn   = 'Ya, Kirim ke CEO';

                if (keputusan) {
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
                            title: 'Mengirim Catatan...',
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
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateKeputusanUI();
        });
    </script>
</body>
</html>
