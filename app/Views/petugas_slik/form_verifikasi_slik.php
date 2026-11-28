<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Verifikasi Fasilitas Kredit SLIK OJK') ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <style>
        html { color-scheme: light !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        input, textarea, select {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        input::placeholder, textarea::placeholder {
            color: #94a3b8 !important;
        }
    </style>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
    <link rel="stylesheet" href="<?= base_url('css/otw-portal.css?v=' . (@filemtime(FCPATH . 'css/otw-portal.css') ?: 1)) ?>">
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Approved';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : 'img/logo-oap.png?v=2';

    // Ambil data debitur dari kredit
    $nikVal = !empty($kredit['npwp_pribadi']) ? $kredit['npwp_pribadi'] : (!empty($kredit['nik']) ? $kredit['nik'] : ($no_id_val ?? '-'));
    $namaDebiturVal = !empty($kredit['nama_debitur']) ? $kredit['nama_debitur'] : 'Debitur';
    $tglLahirVal = !empty($kredit['tanggal_lahir']) ? date('d M Y', strtotime($kredit['tanggal_lahir'])) : (!empty($kredit['tempat_tanggal_lahir']) ? $kredit['tempat_tanggal_lahir'] : '-');
    $tempatLahirVal = !empty($kredit['tempat_lahir']) ? $kredit['tempat_lahir'] : '';
    $ibuKandungVal = !empty($kredit['nama_ibu_kandung']) ? $kredit['nama_ibu_kandung'] : (!empty($kredit['nama_kontak_darurat']) ? $kredit['nama_kontak_darurat'] : '-');
    $berkasPdfVal = $kredit['berkas_slik_pdf'] ?? '';
    $berkasTxtVal = $kredit['berkas_slik_txt'] ?? '';

    // Daftar Permohonan Subjek SLIK (Badan Usaha, Direktur, Pasangan, Penjamin, dll.)
    $daftarPermohonanSlik = [];
    if (!empty($kredit['daftar_permohonan_slik'])) {
        $decodedPermohonan = json_decode($kredit['daftar_permohonan_slik'], true);
        if (is_array($decodedPermohonan) && !empty($decodedPermohonan)) {
            $daftarPermohonanSlik = $decodedPermohonan;
        }
    }
    // Inisialisasi default hanya jika field belum pernah ada di database (null atau string kosong)
    // Jika nilainya adalah '[]' (array kosong hasil penghapusan subjek), JANGAN auto-generate kembali!
    if (empty($daftarPermohonanSlik) && (!isset($kredit['daftar_permohonan_slik']) || $kredit['daftar_permohonan_slik'] === null || $kredit['daftar_permohonan_slik'] === '')) {
        $isBadanUsaha = (isset($kredit['jenis_debitur']) && (stripos($kredit['jenis_debitur'], 'badan') !== false || stripos($kredit['jenis_debitur'], 'pt') !== false || stripos($kredit['jenis_debitur'], 'cv') !== false));
        $daftarPermohonanSlik[] = [
            'index'        => 0,
            'nama'         => $namaDebiturVal,
            'no_id'        => $nikVal,
            'keterkaitan'  => $isBadanUsaha ? 'Badan Usaha' : 'Pemohon',
            'no_reff'      => $no_reff_slik ?? '-',
            'tanggal'      => !empty($kredit['created_at']) ? date('d M Y', strtotime($kredit['created_at'])) : date('d M Y'),
            'status'       => ($kredit['status_slik'] ?? '') === 'Terbit' ? 'Telah Terbit' : 'Menunggu Pengecekan',
            'hasil'        => $kredit['hasil_slik'] ?? 'Menunggu',
            'tempat_lahir' => $tempatLahirVal,
            'tgl_lahir'    => $tglLahirVal,
            'ibu_kandung'  => $ibuKandungVal,
            'pengusul'     => $kredit['slik_verified_by'] ?? 'Staff Pengusul Kredit',
            'file_identitas' => !empty($ktp_dokumen) ? [
                'name' => $ktp_dokumen['nama_dokumen'] ?? 'KTP Debitur',
                'url'  => base_url($ktp_dokumen['file_path'])
            ] : null,
            'file_permohonan' => null
        ];
    }

    // Persiapkan slikDokumenUploaded untuk dokumen permohonan SLIK
    $daftarPermohonanSlikDocs = [];
    foreach ($daftarPermohonanSlik as $rowSlik) {
        $rIdx = $rowSlik['index'] ?? 0;
        $daftarPermohonanSlikDocs[$rIdx] = [
            'nama' => $rowSlik['nama'] ?? '',
            'identitas' => $rowSlik['file_identitas'] ?? null,
            'permohonan' => $rowSlik['file_permohonan'] ?? null
        ];
    }
    if (isset($daftarPermohonanSlikDocs[0]) && empty($daftarPermohonanSlikDocs[0]['identitas']) && !empty($ktp_dokumen) && !empty($ktp_dokumen['file_path'])) {
        $daftarPermohonanSlikDocs[0]['identitas'] = [
            'name' => $ktp_dokumen['nama_dokumen'] ?? 'KTP Debitur',
            'url'  => base_url($ktp_dokumen['file_path']),
            'size' => ''
        ];
    }

    // Kalkulasi ringkasan awal jika ada fasilitas
    $totalPlafonSlik = 0;
    $totalBakiDebetSlik = 0;
    $totalAngsuranSlik = 0;
    $kolTerburuk = 'Kol 1 (Lancar)';
    $countBankUmum = 0;
    $countBankRakyat = 0;
    $countLembagaPembiayaan = 0;
    $countLainnya = 0;
    $kolPriority = ['macet' => 5, 'diragukan' => 4, 'kurang lancar' => 3, 'dpk' => 2, 'lancar' => 1];
    $maxKolVal = 0;

    if (!empty($slik) && is_array($slik)) {
        foreach ($slik as &$sItem) {
            $pl = (float)($sItem['plafon'] ?? 0);
            $bd = (float)($sItem['outstanding'] ?? 0);
            $pAcuan = $pl > 0 ? $pl : $bd;

            $totalPlafonSlik += $pl;
            $totalBakiDebetSlik += $bd;

            $angs = (float)($sItem['angsuran'] ?? 0);
            if ($angs <= 0 && $pAcuan > 0) {
                $jwBln = 12;
                if (preg_match('/([0-9]+)/', (string)($sItem['jangka_waktu'] ?? ''), $jwm)) {
                    $jwBln = (int)$jwm[1];
                }
                if ($jwBln <= 0) $jwBln = 12;

                $bPct = 10;
                if (preg_match('/([0-9]+([.,][0-9]+)?)/', (string)($sItem['suku_bunga'] ?? ''), $bm)) {
                    $bPct = (float)str_replace(',', '.', $bm[1]);
                }
                if ($bPct <= 0) $bPct = 10;

                $angs = round(($pAcuan + ($pAcuan * ($bPct / 100) * ($jwBln / 12))) / $jwBln);
                $sItem['angsuran'] = $angs;
            }
            $totalAngsuranSlik += $angs;

            $bank = strtolower($sItem['bank_lembaga'] ?? '');
            $jenisKredit = strtolower($sItem['jenis_kredit'] ?? ($sItem['jenis_fasilitas'] ?? ''));
            if (strpos($bank, 'bpr') !== false || strpos($bank, 'bprs') !== false) {
                $countBankRakyat++;
            } elseif (
                strpos($bank, 'finance') !== false || strpos($bank, 'leasing') !== false || 
                strpos($bank, 'adira') !== false || strpos($bank, 'fif') !== false || 
                strpos($bank, 'oto') !== false || strpos($bank, 'baf') !== false || 
                strpos($bank, 'wom') !== false || strpos($bank, 'kreditplus') !== false || 
                strpos($bank, 'multifinance') !== false || strpos($bank, 'pembiayaan') !== false || 
                strpos($jenisKredit, 'pembiayaan') !== false || strpos($bank, 'indomobil') !== false
            ) {
                $countLembagaPembiayaan++;
            } elseif (
                strpos($bank, 'bank') !== false || strpos($bank, 'mandiri') !== false || 
                strpos($bank, 'bca') !== false || strpos($bank, 'bri') !== false || 
                strpos($bank, 'bni') !== false || strpos($bank, 'cimb') !== false || 
                strpos($bank, 'danamon') !== false || strpos($bank, 'permata') !== false || 
                strpos($bank, 'btn') !== false || strpos($bank, 'panin') !== false || 
                strpos($bank, 'mega') !== false || strpos($bank, 'ocbc') !== false || 
                strpos($bank, 'maybank') !== false || strpos($bank, 'uob') !== false || 
                strpos($bank, 'dbs') !== false || strpos($bank, 'bsi') !== false
            ) {
                $countBankUmum++;
            } else {
                $countLainnya++;
            }

            $k = strtolower(trim($sItem['kelancaran'] ?? 'lancar'));
            $p = $kolPriority[$k] ?? 1;
            if ($p > $maxKolVal) {
                $maxKolVal = $p;
                $kolTerburuk = $sItem['kelancaran'] ?? 'Kol 1 (Lancar)';
            }
        }
        unset($sItem);
    }
?>

    <!-- Topbar Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('dashboard') ?>" 
                       class="inline-flex items-center gap-2 px-3.5 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition shadow-xs" title="Kembali ke Dashboard">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali</span>
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="text-[10px] block font-bold text-slate-400 uppercase tracking-wider">Petugas Verifikator</span>
                        <span class="text-xs font-bold text-slate-800"><?= esc($nama_lengkap ?? 'Petugas SLIK') ?></span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                        <?= esc(mb_substr($nama_lengkap ?? 'SL', 0, 2)) ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1 max-w-7xl mx-auto">

        <!-- Hidden input pendukung sinkronisasi JS -->
        <input type="hidden" name="kredit_id" id="kredit_id" value="<?= esc($kredit_id) ?>">
        <input type="hidden" name="daftar_permohonan_slik" id="hidden_daftar_permohonan_slik" value="<?= esc(!empty($kredit['daftar_permohonan_slik']) ? $kredit['daftar_permohonan_slik'] : json_encode($daftarPermohonanSlik)) ?>">
        <input type="hidden" name="status_slik" id="hidden_status_slik" value="<?= esc($kredit['status_slik'] ?? 'Belum Terbit') ?>">
        <input type="hidden" name="hasil_slik" id="hidden_hasil_slik" value="<?= esc($kredit['hasil_slik'] ?? 'Menunggu Pengecekan') ?>">
        <input type="hidden" name="ringkasan_slik" id="ringkasan_slik" value="<?= esc($kredit['ringkasan_slik'] ?? '') ?>">
        <input type="hidden" name="berkas_slik_pdf" id="hidden_berkas_slik_pdf" value="<?= esc($berkasPdfVal) ?>">
        <input type="hidden" name="berkas_slik_txt" id="hidden_berkas_slik_txt" value="<?= esc($berkasTxtVal) ?>">

        <!-- DEBITUR IDENTITY & SLIK DISPATCH CARD -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 mb-6 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0 shadow-md">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                <?= esc($namaDebiturVal) ?>
                            </h1>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <?= esc($kredit['nomor_aplikasi']) ?>
                            </span>
                            <?php if (($kredit['status_slik'] ?? '') === 'Terbit' || ($kredit['status_slik'] ?? '') === 'Telah Terbit'): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-300 ring-1 ring-emerald-200/50 flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    SLIK Telah Terbit
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300 ring-1 ring-amber-200/50 flex items-center gap-1.5 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Verifikasi SLIK
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span>Nama / Bidang Usaha: <strong class="text-slate-700"><?= esc(($kredit['nama_usaha'] ?? '') ?: (($kredit['bidang_usaha'] ?? '') ?: '-')) ?><?= (!empty($kredit['nama_usaha']) && !empty($kredit['bidang_usaha'])) ? ' (' . esc($kredit['bidang_usaha']) . ')' : '' ?></strong></span>
                            <span>&bull;</span>
                            <span>Plafon Pengajuan: <strong class="text-blue-700">Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></strong></span>
                            <span>&bull;</span>
                            <span>Tanggal Masuk: <?= date('d M Y', strtotime($kredit['created_at'])) ?></span>
                        </p>
                    </div>
                </div>

                <!-- Action Button: Export TXT Permohonan IDEB OJK -->
                <div class="flex flex-wrap items-center gap-2.5 self-start lg:self-auto">
                    <button type="button" onclick="exportTxtPermohonanSlik()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer"
                            title="Unduh berkas TXT baku IDEB OJK untuk diunggah langsung ke portal SLIK OJK">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Export TXT IDEB OJK</span>
                    </button>
                </div>
            </div>

            <!-- Tabel Permohonan Subjek SLIK / IDEB OJK (Bentuk Tabel Sesuai Pengajuan SLIK) -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wide uppercase flex items-center gap-2">
                            <span>Daftar Subjek Permohonan SLIK / iDeb OJK</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                <?= count($daftarPermohonanSlik) ?> Subjek
                            </span>
                        </h3>
                        <p class="text-[11px] text-slate-500">Pilih subjek untuk diekspor ke berkas TXT atau proses verifikasi hasil SLIK di bawah ini.</p>
                    </div>
                </div>

                <div class="w-full overflow-x-auto border border-slate-200 rounded-2xl bg-white shadow-2xs">
                    <table class="w-full text-left border-collapse text-[11px] table-auto" id="tabelSlikIdeb">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-500 tracking-wider uppercase border-b border-slate-200 bg-slate-50">
                                <th class="py-3 px-2 text-center whitespace-nowrap w-8">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" id="checkAllSlik" onchange="toggleSelectAllSlik(this)" class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer" checked title="Pilih Semua">
                                    </div>
                                </th>
                                <th class="py-3 px-2.5 whitespace-nowrap">STATUS</th>
                                <th class="py-3 px-2.5 whitespace-nowrap">NO REFF SLIK</th>
                                <th class="py-3 px-2.5 whitespace-nowrap">TANGGAL</th>
                                <th class="py-3 px-2.5 whitespace-nowrap">NO ID</th>
                                <th class="py-3 px-2.5 whitespace-nowrap">NAMA</th>
                                <th class="py-3 px-2.5 whitespace-nowrap text-center">HASIL</th>
                                <th class="py-3 px-2.5 whitespace-nowrap text-center">RINGKASAN</th>
                                <th class="py-3 px-2.5 whitespace-nowrap text-center">HASIL PDF</th>
                                <th class="py-3 px-2.5 whitespace-nowrap text-center">DOKUMEN</th>
                                <th class="py-3 px-2.5 whitespace-nowrap text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[11px]" id="bodySlikIdeb">
                            <?php if (empty($daftarPermohonanSlik)): ?>
                                <tr id="emptySubjekSlikRow">
                                    <td colspan="11" class="py-12 text-center text-xs text-slate-400 italic bg-white">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span class="font-bold text-slate-600">Tidak ada subjek permohonan SLIK aktif</span>
                                            <span class="text-[11px] text-slate-400">Seluruh subjek permohonan SLIK untuk pengajuan ini telah dihapus.</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                            <?php foreach ($daftarPermohonanSlik as $idx => $row): 
                                $rNama = $row['nama'] ?? $namaDebiturVal;
                                $rNoId = $row['no_id'] ?? $nikVal;
                                $rKeterkaitan = $row['keterkaitan'] ?? 'Pemohon';
                                $rNoReff = $row['no_reff'] ?? ($no_reff_slik ?? '-');
                                $rTgl = $row['tanggal'] ?? (!empty($kredit['created_at']) ? date('d M Y', strtotime($kredit['created_at'])) : '-');
                                $rStatus = $row['status'] ?? (($kredit['status_slik'] ?? '') === 'Terbit' ? 'Telah Terbit' : 'Menunggu Pengecekan');
                                $rHasil = $row['hasil'] ?? ($kredit['hasil_slik'] ?? 'Menunggu');

                                // Deteksi file identitas (KTP/NPWP)
                                $fileIdUrl = '';
                                $fileIdName = '';
                                if (!empty($row['file_identitas'])) {
                                    if (is_array($row['file_identitas'])) {
                                        $fileIdPath = $row['file_identitas']['path'] ?? '';
                                        $fileIdUrl = !empty($fileIdPath) ? base_url($fileIdPath) : ($row['file_identitas']['url'] ?? '');
                                        $fileIdName = $row['file_identitas']['name'] ?? 'Berkas Identitas';
                                    } else {
                                        $fileIdUrl = base_url($row['file_identitas']);
                                        $fileIdName = basename($row['file_identitas']);
                                    }
                                } elseif ($idx === 0 && !empty($ktp_dokumen) && !empty($ktp_dokumen['file_path'])) {
                                    $fileIdUrl = base_url($ktp_dokumen['file_path']);
                                    $fileIdName = $ktp_dokumen['nama_dokumen'] ?? 'Berkas KTP';
                                } elseif ($idx === 0 && !empty($kredit['slik_ktp_file'])) {
                                    $fileIdUrl = base_url($kredit['slik_ktp_file']);
                                    $fileIdName = basename($kredit['slik_ktp_file']);
                                }

                                // Deteksi file permohonan
                                $filePermUrl = '';
                                $filePermName = '';
                                if (!empty($row['file_permohonan'])) {
                                    if (is_array($row['file_permohonan'])) {
                                        $filePermPath = $row['file_permohonan']['path'] ?? '';
                                        $filePermUrl = !empty($filePermPath) ? base_url($filePermPath) : ($row['file_permohonan']['url'] ?? '');
                                        $filePermName = $row['file_permohonan']['name'] ?? 'Berkas Permohonan';
                                    } else {
                                        $filePermUrl = base_url($row['file_permohonan']);
                                        $filePermName = basename($row['file_permohonan']);
                                    }
                                } elseif ($idx === 0 && !empty($kredit['slik_surat_kuasa_file'])) {
                                    $filePermUrl = base_url($kredit['slik_surat_kuasa_file']);
                                    $filePermName = basename($kredit['slik_surat_kuasa_file']);
                                }
                                
                                $isTerbit = (strtolower($rStatus) === 'telah terbit' || strtolower($rStatus) === 'terbit');
                                $colorHasil = 'border-slate-300 text-slate-500 bg-slate-50/50';
                                if (strtolower($rHasil) === 'lancar') {
                                    $colorHasil = 'border-[#00a859] text-[#00a859] bg-emerald-50/30';
                                } elseif (in_array(strtolower($rHasil), ['dpk', 'kurang lancar', 'diragukan', 'macet'])) {
                                    $colorHasil = 'border-amber-500 text-amber-600 bg-amber-50/30';
                                }
                            ?>
                            <tr class="hover:bg-slate-50/80 transition slik-row-item" data-index="<?= $idx ?>" id="slik-row-<?= $idx ?>">
                                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="slik-select-item slik-subjek-item w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer" data-index="<?= $idx ?>" value="<?= esc($rNoReff) ?>" checked>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2.5 whitespace-nowrap" id="status-badge-cell-<?= $idx ?>">
                                    <?php if ($isTerbit): ?>
                                        <span class="inline-block bg-[#00a859] text-white font-bold text-[10px] px-2.5 py-0.5 rounded shadow-2xs">
                                            Telah Terbit
                                        </span>
                                    <?php elseif (stripos(strtolower($rStatus), 'menunggu') !== false): ?>
                                        <span class="inline-block bg-blue-600 text-white font-bold text-[10px] px-2.5 py-0.5 rounded shadow-2xs">
                                            <?= esc($rStatus) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-block bg-[#f59e0b] text-white font-bold text-[10px] px-2.5 py-0.5 rounded shadow-2xs">
                                            <?= esc($rStatus) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-2.5 text-[11px] font-semibold text-slate-700 max-w-[140px] truncate" id="cell-reff-<?= $idx ?>" title="<?= esc($rNoReff) ?>">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-blue-900 text-[11px] truncate"><?= esc($rNoReff) ?></span>
                                        <button type="button" onclick="salinTeks('<?= esc($rNoReff) ?>', 'No. Reff SLIK')" class="p-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition cursor-pointer" title="Salin No Reff">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2.5 whitespace-nowrap text-[11px] text-slate-600" id="cell-tgl-<?= $idx ?>">
                                    <?= esc($rTgl) ?>
                                </td>
                                <td class="py-2.5 px-2.5 whitespace-nowrap text-[11px] font-semibold text-slate-800" id="cell-noid-<?= $idx ?>">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-slate-900 text-[11px]"><?= esc($rNoId) ?></span>
                                        <button type="button" onclick="salinTeks('<?= esc($rNoId) ?>', 'No. ID')" class="p-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition cursor-pointer" title="Salin No ID">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2.5 whitespace-nowrap text-[11px] font-bold text-slate-900" id="cell-nama-<?= $idx ?>">
                                    <div class="flex items-center gap-1.5">
                                        <span><?= esc($rNama) ?></span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase"><?= esc($rKeterkaitan) ?></span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2.5 whitespace-nowrap text-center" id="hasil-badge-cell-<?= $idx ?>">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full border <?= $colorHasil ?> text-[10px] font-bold">
                                        <?= esc($rHasil) ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                    <button type="button" onclick="bukaModalRingkasanSlik(<?= $idx ?>)" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat</span>
                                    </button>
                                </td>
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                    <button type="button" onclick="bukaModalPdfSlik(<?= $idx ?>)" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat</span>
                                    </button>
                                </td>
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-center">
                                        <?php if (!empty($fileIdUrl)): ?>
                                            <button type="button" onclick="bukaPratinjauDokumenSlik('<?= esc($fileIdUrl) ?>', '<?= esc($fileIdName) ?>', 'Dokumen Identitas (KTP/NPWP)')" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-md text-[10px] font-bold transition cursor-pointer shadow-2xs" 
                                                    title="Lihat Identitas: <?= esc($fileIdName) ?>">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                                <span>Identitas</span>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (!empty($filePermUrl)): ?>
                                            <button type="button" onclick="bukaPratinjauDokumenSlik('<?= esc($filePermUrl) ?>', '<?= esc($filePermName) ?>', 'Dokumen Permohonan SLIK')" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md text-[10px] font-bold transition cursor-pointer shadow-2xs" 
                                                    title="Lihat Permohonan: <?= esc($filePermName) ?>">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>Permohonan</span>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (empty($fileIdUrl) && empty($filePermUrl)): ?>
                                            <span class="text-slate-400 text-[10px] italic">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-center">
                                        <?php if (!$isTerbit): ?>
                                            <button type="button" onclick="bukaModalUploadHasilSlik(<?= $idx ?>)" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-md text-[10px] font-bold shadow-xs transition cursor-pointer"
                                                    title="Proses / Upload Hasil SLIK">
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                                <span>Proses</span>
                                            </button>
                                        <?php endif; ?>
                                        <!-- Tombol Edit SLIK (Icon Pensil) -->
                                        <button type="button" onclick="editHasilSlik(<?= $idx ?>)" 
                                                class="inline-flex items-center justify-center w-7 h-7 text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-300 rounded-lg shadow-2xs transition cursor-pointer" 
                                                title="Edit Hasil SLIK (Ubah Hasil & Upload Ulang)">
                                            <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>
                                        <!-- Tombol Hapus SLIK (Icon Tempat Sampah) -->
                                        <button type="button" onclick="hapusHasilSlik(<?= $idx ?>)" 
                                                class="inline-flex items-center justify-center w-7 h-7 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-lg shadow-2xs transition cursor-pointer" 
                                                title="Hapus Data SLIK / Reset Status">
                                            <svg class="w-3.5 h-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- =======================================================
         MODAL 1: RINGKASAN HASIL SLIK (Fasilitas & Metrik Ekstraksi)
    ======================================================== -->
    <div id="modalRingkasanSlik" onclick="if(event.target === this) tutupModalRingkasanSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
            <!-- Header: Judul & Tombol Export PDF / Export Excel + Close -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Ringkasan Hasil SLIK</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="ringkasanModalSubJudul">Laporan riwayat dan evaluasi fasilitas pinjaman perbankan</p>
                    <div id="loadingRingkasanSlik" class="hidden items-center gap-1.5 mt-1.5 text-xs font-semibold text-blue-600 animate-pulse">
                        <svg class="w-3.5 h-3.5 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memuat ringkasan...</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="exportPdfRingkasanSlik()" class="px-3.5 py-1.5 bg-white border border-[#06b6d4] text-[#0891b2] hover:bg-cyan-50 rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                        <span>Export PDF</span>
                    </button>
                    <button type="button" onclick="exportExcelRingkasanSlik()" class="px-3.5 py-1.5 bg-[#00a859] hover:bg-[#00914c] text-white rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                        <span>Export Excel</span>
                    </button>
                    <button type="button" onclick="tutupModalRingkasanSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 ml-1 transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Body Scrollable -->
            <div class="py-5 overflow-y-auto space-y-4 flex-1">
                <!-- Banner Identitas Subjek Aktif -->
                <div class="p-3.5 bg-indigo-50/70 border border-indigo-200/70 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                            IDEB
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span id="ringkasanDebiturNama"><?= esc($namaDebiturVal ?: 'Debitur') ?></span>
                                <span id="ringkasanDebiturKeterkaitan" class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase">PEMOHON</span>
                            </div>
                            <div class="text-[11px] text-slate-600 mt-0.5">
                                <span>No. ID: <strong id="ringkasanDebiturNoId" class="font-mono"><?= esc($nikVal ?: '-') ?></strong></span>
                                <span class="mx-1.5 text-slate-300">|</span>
                                <span>No. Reff: <span id="ringkasanDebiturNoReff" class="font-mono"><?= esc($no_reff_slik ?? '-') ?></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="ringkasanDebiturHasilBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            <?= esc($kredit['hasil_slik'] ?? 'Lancar') ?>
                        </span>
                        <button type="button" onclick="tutupModalRingkasanSlik(); bukaModalPdfSlik(currentActiveSlikIndex);" class="px-3 py-1 bg-white border border-[#4834d4] text-[#4834d4] hover:bg-indigo-50 rounded-lg text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Lihat PDF SLIK</span>
                        </button>
                    </div>
                </div>
                
                <!-- Baris 1: 3 Kartu Metrik Utama -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Plafond -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Total Plafond</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricPlafon">
                            Rp <?= number_format($totalPlafonSlik, 0, ',', '.') ?>
                        </div>
                    </div>

                    <!-- Total Baki Debet -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Total Baki Debet</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBakiDebet">
                            Rp <?= number_format($totalBakiDebetSlik, 0, ',', '.') ?>
                        </div>
                    </div>

                    <!-- Kolektibilitas Terburuk -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Kolektibilitas Terburuk</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricKol">
                            <?= esc($kolTerburuk) ?>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5" id="ringkasanMetricKolSub">Berdasarkan data fasilitas</div>
                    </div>
                </div>

                <!-- Baris 2: 4 Kartu Breakdown Bank -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Bank Umum -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Bank Umum</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBankUmum">
                            <?= (int)$countBankUmum ?>
                        </div>
                    </div>

                    <!-- BPR / BPRS (Bank Perekonomian Rakyat) -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">BPR / BPRS</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBpr" data-legacy-id="ringkasanMetricBankRakyat">
                            <?= (int)$countBankRakyat ?>
                        </div>
                    </div>

                    <!-- Lembaga Pembiayaan -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Lembaga Pembiayaan</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricLembaga">
                            <?= (int)$countLembagaPembiayaan ?>
                        </div>
                    </div>

                    <!-- Lainnya -->
                    <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                        <div class="text-[11px] text-slate-500 font-medium">Lainnya</div>
                        <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricLainnya">
                            <?= (int)$countLainnya ?>
                        </div>
                    </div>
                </div>

                <!-- Header Tabel Rincian Fasilitas -->
                <div class="flex items-center justify-between pt-2">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Rincian Fasilitas Pinjaman / Kredit</h4>
                </div>

                <!-- Tabel Rincian Fasilitas Kredit -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs mt-1">
                    <table class="w-full text-left border-collapse text-xs" id="tabelRincianFasilitasSlik">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                <th class="py-2.5 px-3 border-r border-slate-200 text-center w-10">NO</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">NAMA</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[110px]">BANK</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[130px]">JENIS PENGGUNAAN</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">JENIS KREDIT</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">PLAFOND</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">BAKI DEBET</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[90px]">SUKU BUNGA</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">TGL AKAD</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">JATUH TEMPO</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">JANGKA WAKTU</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px] bg-rose-50/70 text-rose-800">ANGSURAN / BLN</th>
                                <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">TUNGGAKAN</th>
                                <th class="py-2.5 px-3 min-w-[110px] text-center">KOLEKTIBILITAS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="bodyRincianFasilitasSlik">
                            <?php if (empty($slik)): ?>
                                <tr id="emptyFasilitasRow">
                                    <td colspan="14" class="py-8 text-center text-xs text-slate-400 italic bg-white">
                                        Tidak ada catatan fasilitas pinjaman aktif.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slik as $i => $row): ?>
                                    <tr class="hover:bg-slate-50/80 transition" id="row-fasilitas-<?= $i ?>">
                                        <td class="py-2 px-3 border-r border-slate-100 text-center font-bold"><?= $i + 1 ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100 font-bold"><?= esc(!empty($row['nama_debitur']) ? $row['nama_debitur'] : ($namaDebiturVal ?? '-')) ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['bank_lembaga']) ? $row['bank_lembaga'] : '-') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['keterangan']) ? $row['keterangan'] : (!empty($row['jenis_fasilitas']) ? $row['jenis_fasilitas'] : 'Modal Kerja')) ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['jenis_fasilitas']) ? $row['jenis_fasilitas'] : (!empty($row['keterangan']) ? $row['keterangan'] : 'Kredit Usaha')) ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100 font-semibold">Rp <?= number_format((float)($row['plafon'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100 font-bold text-blue-700">Rp <?= number_format((float)($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['suku_bunga'] ?? '-') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['tgl_akad'] ?? '-') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['jatuh_tempo'] ?? '-') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['jangka_waktu'] ?? '-') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100 font-bold text-rose-700 bg-rose-50/40">Rp <?= number_format((float)($row['angsuran'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['tunggakan'] ?? '0') ?></td>
                                        <td class="py-2 px-3 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= strtolower($row['kelancaran'] ?? '') === 'lancar' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300' ?>">
                                                <?= esc($row['kelancaran'] ?? 'Lancar') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Container hidden input sync untuk fasilitas SLIK agar otomatis tersimpan saat submit form -->
                <div id="hiddenFasilitasSlikContainer"></div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModalRingkasanSlik()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- =======================================================
         MODAL 2: UPLOAD HASIL SLIK (In-Table Processing)
    ======================================================== -->
    <div id="modalUploadHasilSlik" onclick="if(event.target === this) tutupModalUploadHasilSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200">
            <!-- Header: Judul & Close -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Upload Hasil SLIK</h3>
                <button type="button" onclick="tutupModalUploadHasilSlik()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body Form -->
            <div id="formUploadHasilSlik" class="py-4 space-y-4">
                <!-- Hasil SLIK Dropdown -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hasil SLIK</label>
                    <select id="modalHasilSlikSelect" name="hasil_slik" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                        <option value="Lancar">Lancar</option>
                        <option value="DPK">DPK</option>
                        <option value="Kurang Lancar">Kurang Lancar</option>
                        <option value="Diragukan">Diragukan</option>
                        <option value="Macet">Macet</option>
                    </select>
                </div>

                <!-- Grid 2 Upload Box: Upload Hasil TXT & Upload Hasil PDF -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <!-- Box 1: Upload Hasil TXT -->
                    <div class="min-w-0 overflow-hidden">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil TXT</label>
                        <div id="dropzoneTxt" onclick="document.getElementById('inputTxtFile').click()" 
                             class="border border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group w-full max-w-full overflow-hidden">
                            <input type="file" id="inputTxtFile" accept=".txt" class="hidden" onchange="handleFileSelected(this, 'txt')">
                            <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p id="labelFileNameTxt" class="text-xs text-slate-700 font-semibold group-hover:text-blue-600 transition max-w-[190px] sm:max-w-[220px] mx-auto px-2 truncate block overflow-hidden">Drag and drop a file here or click</p>
                            <span class="text-[10px] text-slate-400 mt-1 block truncate max-w-full" id="labelFileSubTxt">Format .TXT</span>
                        </div>
                    </div>

                    <!-- Box 2: Upload Hasil PDF -->
                    <div class="min-w-0 overflow-hidden">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil PDF</label>
                        <div id="dropzonePdf" onclick="document.getElementById('inputPdfFile').click()" 
                             class="border border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group w-full max-w-full overflow-hidden">
                            <input type="file" id="inputPdfFile" accept=".pdf" class="hidden" onchange="handleFileSelected(this, 'pdf')">
                            <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p id="labelFileNamePdf" class="text-xs text-slate-700 font-semibold group-hover:text-blue-600 transition max-w-[190px] sm:max-w-[220px] mx-auto px-2 truncate block overflow-hidden">Drag and drop a file here or click</p>
                            <span class="text-[10px] text-slate-400 mt-1 block truncate max-w-full" id="labelFileSubPdf">Format .PDF</span>
                        </div>
                    </div>
                </div>

                <!-- Footer: Batal & Simpan -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="tutupModalUploadHasilSlik()" class="px-5 py-2 bg-[#64748b] hover:bg-[#475569] text-white text-xs font-bold rounded-lg transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" onclick="submitFormUploadHasilSlik(event)" id="btnSimpanUploadSlik" class="px-5 py-2 bg-[#00a859] hover:bg-[#00914c] text-white text-xs font-bold rounded-lg transition shadow-xs cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =======================================================
         MODAL 3: HASIL SLIK OJK (Menampilkan Hasil Upload PDF)
    ======================================================== -->
    <div id="modalPdfSlik" onclick="if(event.target === this) tutupModalPdfSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
            <!-- Header: Hasil SLIK OJK & Close X -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-sm sm:text-base font-bold text-slate-900">Hasil SLIK OJK</h3>
                <button type="button" onclick="tutupModalPdfSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Body: Menampilkan Berkas PDF yang Diupload pada Proses -->
            <div class="py-4 flex-1 overflow-y-auto" id="modalPdfSlikContainer">
                <div id="pdfViewerWrapper" class="hidden">
                    <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl mb-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                            <span class="text-xs font-bold text-slate-800" id="pdfFileNameLabel">berkas_ideb_slik.pdf</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a id="btnBukaPdfTab" href="#" target="_blank" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-bold transition">
                                Buka di Tab Baru ↗
                            </a>
                            <button type="button" onclick="tutupModalPdfSlik(); bukaModalUploadHasilSlik(currentActiveSlikIndex);" class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                Unggah Ulang
                            </button>
                        </div>
                    </div>
                    <iframe id="iframePdfSlik" src="" class="w-full h-[65vh] border border-slate-200 rounded-xl shadow-inner"></iframe>
                </div>

                <!-- Tampilan bila belum ada PDF yang diupload -->
                <div id="pdfEmptyWrapper" class="text-center py-16 px-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800">Berkas Hasil SLIK (iDeb PDF) Belum Diunggah</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Silakan klik tombol <strong>Proses</strong> pada tabel permohonan untuk mengunggah berkas hasil penarikan SLIK (PDF & TXT) debitur.</p>
                    <div class="mt-5">
                        <button type="button" onclick="tutupModalPdfSlik(); bukaModalUploadHasilSlik(currentActiveSlikIndex);" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Buka Form Upload Hasil SLIK</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModalPdfSlik()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- =======================================================
         MODAL 4: LIHAT DOKUMEN PENGAJUAN SLIK (Identitas & Permohonan)
    ======================================================== -->
    <div id="modalLihatDokumenSlik" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Dokumen Pengajuan SLIK</h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="modalLihatDokumenSub">Berkas identitas dan permohonan</p>
                </div>
                <button type="button" onclick="tutupModalLihatDokumenSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-3.5 py-4" id="modalLihatDokumenBody">
                <!-- Dokumen Identitas Card -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-xs">
                            ID
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-800">Dokumen Identitas (KTP/NPWP)</div>
                            <div class="text-[11px] text-slate-500 truncate" id="lihatDocNamaIdentitas">-</div>
                        </div>
                    </div>
                    <div id="lihatDocBtnIdentitas" class="shrink-0">
                        <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                    </div>
                </div>

                <!-- Dokumen Permohonan Card -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-xs">
                            REQ
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-800">Dokumen Permohonan SLIK</div>
                            <div class="text-[11px] text-slate-500 truncate" id="lihatDocNamaPermohonan">-</div>
                        </div>
                    </div>
                    <div id="lihatDocBtnPermohonan" class="shrink-0">
                        <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" onclick="tutupModalLihatDokumenSlik()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- =======================================================
         MODAL 5: PREVIEW BERKAS DOKUMEN SLIK (Image / PDF / TXT)
    ======================================================== -->
    <div id="modalPreviewDocSlik" onclick="if(event.target === this) tutupPreviewDocSlik()" class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-4xl w-full p-4 sm:p-5 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <div class="min-w-0 pr-2">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate" id="previewDocTitle">Preview Dokumen</h3>
                    <p class="text-xs text-slate-500 truncate" id="previewDocSub">Berkas pengajuan</p>
                </div>
                <div class="flex items-center gap-2">
                    <a id="previewDocDownloadBtn" href="#" target="_blank" class="hidden px-2.5 py-1 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-semibold transition items-center gap-1">
                        Buka di Tab Baru ↗
                    </a>
                    <button type="button" onclick="tutupPreviewDocSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto py-3 flex items-center justify-center min-h-[350px] bg-slate-50/70 rounded-xl mt-3" id="previewDocContainer">
                <!-- Konten Pratinjau Dokumen -->
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 shrink-0 mt-3">
                <button type="button" onclick="tutupPreviewDocSlik()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Sistem Verifikasi SLIK OJK Terintegrasi
        </div>
    </footer>

    <script>
        window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';
        window.KREDIT_ID_VAL = '<?= esc($kredit_id) ?>';
        window.NAMA_DEBITUR_VAL = '<?= esc($namaDebiturVal) ?>';
        window.NO_ID_VAL = '<?= esc($nikVal) ?>';
        window.BERKAS_SLIK_PDF_VAL = '<?= esc($berkasPdfVal) ?>';
        window.BERKAS_SLIK_TXT_VAL = '<?= esc($berkasTxtVal) ?>';
        window.CAN_EDIT_SLIK = true;
        window.PETUGAS_SLIK_AKTIF = 1;
        window.USER_ROLE = 'petugas_slik';
        window.daftarPermohonanSlikArray = <?= json_encode($daftarPermohonanSlik) ?>;
        window.slikFasilitasArray = <?= json_encode(!empty($slik) ? $slik : []) ?>;
        window.slikDokumenUploaded = <?= json_encode(!empty($daftarPermohonanSlikDocs) ? (object)$daftarPermohonanSlikDocs : new stdClass()) ?>;

        function bukaPratinjauDokumenSlik(url, name, tipe) {
            if (!url) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Kosong',
                        text: 'Berkas belum diunggah untuk subjek ini.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    alert('Berkas belum diunggah.');
                }
                return;
            }
            const previewModal = document.getElementById('modalPreviewDocSlik');
            const previewTitle = document.getElementById('previewDocTitle');
            const previewSub = document.getElementById('previewDocSub');
            const previewDownloadBtn = document.getElementById('previewDocDownloadBtn');
            const container = document.getElementById('previewDocContainer');
            if (!previewModal || !container) return;

            if (previewTitle) previewTitle.textContent = tipe || 'Pratinjau Dokumen';
            if (previewSub) previewSub.textContent = name || '';

            const cleanBase = (window.BASE_URL || '').replace(/\/+$/, '');
            const fullUrl = url.startsWith('http') ? url : (cleanBase + '/' + url.replace(/^\/+/, ''));

            if (previewDownloadBtn) {
                previewDownloadBtn.href = fullUrl;
                previewDownloadBtn.classList.remove('hidden');
                previewDownloadBtn.classList.add('inline-flex');
            }

            const ext = fullUrl.split('.').pop().toLowerCase().split('?')[0];

            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
                container.innerHTML = `<div class="p-2 flex items-center justify-center w-full"><img src="${fullUrl}" alt="${name || 'Dokumen'}" class="max-h-[70vh] max-w-full object-contain rounded-lg shadow-sm" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'text-center p-6 text-slate-500 text-xs\\'><p class=\\'font-semibold mb-1 text-slate-700\\'>Gagal memuat gambar</p><p class=\\'text-slate-400 mb-3\\'>Berkas tidak dapat ditampilkan langsung atau format tidak terbaca.</p><a href=\\'${fullUrl}\\' target=\\'_blank\\' class=\\'px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 inline-block\\'>Buka di Tab Baru ↗</a></div>';"></div>`;
            } else {
                container.innerHTML = `<iframe src="${fullUrl}" class="w-full h-[70vh] border border-slate-200 rounded-lg shadow-inner"></iframe>`;
            }

            previewModal.classList.remove('hidden');
            previewModal.style.display = 'flex';
        }
        window.bukaPratinjauDokumenSlik = bukaPratinjauDokumenSlik;

        function tutupPreviewDocSlik() {
            const modal = document.getElementById('modalPreviewDocSlik');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                const container = document.getElementById('previewDocContainer');
                if (container) container.innerHTML = '';
            }
        }
        window.tutupPreviewDocSlik = tutupPreviewDocSlik;

        function salinTeks(teks, label) {
            if (!teks || teks === '-') {
                Swal.fire({ icon: 'warning', title: 'Data Kosong', text: `${label} belum terisi pada data nasabah ini.`, timer: 1500, showConfirmButton: false });
                return;
            }
            navigator.clipboard.writeText(teks).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: `${label} berhasil disalin: ${teks}`,
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        }
        window.salinTeks = salinTeks;

        function escapeHtmlText(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
        window.escapeHtmlText = escapeHtmlText;

        // FUNGSI EDIT SLIK (ICON PENSIL)
        function editHasilSlik(idx) {
            if (typeof bukaModalUploadHasilSlik === 'function') {
                bukaModalUploadHasilSlik(idx);
            }
        }
        window.editHasilSlik = editHasilSlik;

        // FUNGSI HAPUS SLIK (ICON TEMPAT SAMPAH)
        function hapusHasilSlik(idx) {
            let rowItem = null;
            if (window.daftarPermohonanSlikArray && window.daftarPermohonanSlikArray[idx]) {
                rowItem = window.daftarPermohonanSlikArray[idx];
            }
            const namaSubjek = rowItem ? (rowItem.nama || 'Subjek') : 'data ini';

            const runDelete = function() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Menghapus Data SLIK...',
                        text: 'Mereset status dan membersihkan fasilitas SLIK',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                }

                const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '');
                const baseUrl = (window.BASE_URL || '').replace(/\/+$/, '');
                const fd = new FormData();
                fd.append('kredit_id', kid);
                fd.append('subjek_index', idx);

                const csrf = document.querySelector('input[name="csrf_token"]') || document.querySelector('input[name="csrf_test_name"]');
                if (csrf) fd.append(csrf.name, csrf.value);

                fetch(baseUrl + '/slik/hapus/' + kid, {
                    method: 'POST',
                    body: fd
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dihapus',
                                text: data.message || 'Data SLIK berhasil dihapus.',
                                timer: 1400,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            alert(data.message || 'Data SLIK berhasil dihapus.');
                            window.location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal menghapus data SLIK.'
                            });
                        } else {
                            alert('Gagal: ' + (data.message || 'Gagal menghapus data SLIK.'));
                        }
                    }
                })
                .catch(err => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menghubungi server: ' + err.message
                        });
                    } else {
                        alert('Kesalahan sistem: ' + err.message);
                    }
                });
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus Subjek Permohonan SLIK?',
                    html: `Apakah Anda yakin ingin menghapus subjek permohonan SLIK <strong>${escapeHtmlText(namaSubjek)}</strong>?<br><span class="text-xs text-rose-600 font-semibold block mt-1.5">Subjek ini akan dihapus permanen dari daftar permohonan SLIK beserta seluruh fasilitas kredit terkaitnya.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus Subjek',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        runDelete();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menghapus subjek permohonan SLIK untuk ' + namaSubjek + '? Subjek akan dihapus permanen.')) {
                    runDelete();
                }
            }
        }
        window.hapusHasilSlik = hapusHasilSlik;

        // Tutup modal jika tombol Escape ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (typeof tutupModalRingkasanSlik === 'function') tutupModalRingkasanSlik();
                if (typeof tutupModalUploadHasilSlik === 'function') tutupModalUploadHasilSlik();
                if (typeof tutupModalPdfSlik === 'function') tutupModalPdfSlik();
                if (typeof tutupModalLihatDokumenSlik === 'function') tutupModalLihatDokumenSlik();
                if (typeof tutupPreviewDocSlik === 'function') tutupPreviewDocSlik();
            }
        });
    </script>
    <script src="<?= base_url('js/pengajuan-slik.js?v=' . (@filemtime(FCPATH . 'js/pengajuan-slik.js') ?: 1)) ?>"></script>
</body>
</html>
