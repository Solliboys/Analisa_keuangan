<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Kredit – <?= esc($kredit['nomor_aplikasi'] ?? '-') ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .print-page {
                box-shadow: none !important;
                margin: 0 !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
            }
            .surat-body-wrap {
                padding: 32px 48px !important;
            }
            .surat-kop-wrap {
                padding: 20px 48px 16px 48px !important;
            }
            .surat-ttd-row {
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
            }
        }
        @media (max-width: 640px) {
            .surat-page-card {
                margin: 4px auto !important;
                border-radius: 12px !important;
                box-shadow: 0 1px 4px rgba(0,0,0,0.08) !important;
            }
            .surat-kop-wrap {
                padding: 16px 14px 12px 14px !important;
            }
            .surat-kop-inner {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
            }
            .surat-kop-kontak {
                text-align: left !important;
                font-size: 8.5pt !important;
                border-top: 1px dashed #cbd5e1;
                padding-top: 8px;
                width: 100%;
            }
            .surat-body-wrap {
                padding: 18px 14px !important;
                font-size: 10.5pt !important;
            }
            .surat-box-content {
                padding: 12px 10px !important;
            }
            .surat-col-label {
                width: 120px !important;
                font-size: 9.5pt !important;
            }
            .surat-col-val {
                font-size: 9.5pt !important;
            }
            .surat-ttd-row {
                flex-direction: column !important;
                align-items: center !important;
                gap: 28px !important;
            }
            .surat-ttd-col {
                min-width: 100% !important;
                width: 100% !important;
            }
            .surat-footer-wrap {
                padding: 10px 14px !important;
                flex-direction: column !important;
                gap: 6px !important;
                text-align: center !important;
            }
            .surat-footer-wrap > div {
                text-align: center !important;
            }
        }
        @page { size: A4; margin: 0; }
    </style>
</head>
<body class="bg-slate-200 min-h-screen">

<?php
    $namaLembaga    = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'PT Lembaga Pembiayaan';
    $namaAplikasi   = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine';
    $logoAplikasi   = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : 'img/logo-oap.png?v=2';
    $alamatLembaga  = !empty($pengaturan_sistem['alamat_lembaga']) ? $pengaturan_sistem['alamat_lembaga'] : (!empty($pengaturan_sistem['alamat']) ? $pengaturan_sistem['alamat'] : 'Financial Center, Lantai 8, Jakarta Selatan');
    $teleponLembaga = !empty($pengaturan_sistem['telepon_lembaga']) ? $pengaturan_sistem['telepon_lembaga'] : (!empty($pengaturan_sistem['telepon']) ? $pengaturan_sistem['telepon'] : '(021) 5088-2900');
    $emailLembaga   = !empty($pengaturan_sistem['email_lembaga']) ? $pengaturan_sistem['email_lembaga'] : (!empty($pengaturan_sistem['email']) ? $pengaturan_sistem['email'] : 'info@otwkredit.id');

    $nomorAplikasi  = $kredit['nomor_aplikasi'] ?? '-';
    $namaDebitur    = strtoupper($kredit['nama_debitur'] ?? '-');
    $jenisKredit    = $kredit['jenis_kredit'] ?? ($kredit['jenis_fasilitas'] ?? 'Kredit Modal Kerja');
    $plafonPengajuan = (float)($kredit['plafon_pengajuan'] ?? 0);
    $tenorBulan     = (int)($kredit['tenor_bulan'] ?? 12);
    $bungaPerTahun  = (float)($kredit['bunga_per_tahun'] ?? 0);
    $bungaPerBulan  = (float)($kredit['bunga_per_bulan'] ?? ($bungaPerTahun / 12));
    $tujuanKredit   = $kredit['tujuan_pembiayaan'] ?? 'Modal Kerja Operasional';
    $noKtp          = $kredit['nik_ktp'] ?? ($kredit['no_ktp'] ?? '-');
    $alamatDebitur  = $kredit['alamat'] ?? '-';
    $pekerjaanDebitur = $kredit['pekerjaan'] ?? ($kredit['jenis_pekerjaan'] ?? '-');
    $skemaPembayaran = $kredit['skema_pembayaran'] ?? 'Angsuran Tetap Bulanan (Anuitas)';

    // Keputusan CEO & Data Direktur Utama secara Dinamis
    $keputusanCeoStatus  = $keputusan_ceo['status_approval'] ?? '-';
    $catatanCeo          = $keputusan_ceo['catatan_approval'] ?? '-';

    // Prioritas Nama Direktur Utama secara Dinamis:
    // 1. Akun CEO terkait ($ceo_user['nama_lengkap']) agar dinamis saat profil diperbarui
    // 2. Pengaturan Sistem ($pengaturan_sistem['nama_direktur'])
    // 3. Nama Approver pada catatan komite jika bukan label jabatan generik
    // 4. Query langsung akun CEO aktif di database users
    $namaDirektur = '';
    if (!empty($ceo_user['nama_lengkap']) && !preg_match('/^(direktur\s*utama|direktur|ceo|pimpinan)(\s*\/.*)?$/i', trim($ceo_user['nama_lengkap']))) {
        $namaDirektur = trim($ceo_user['nama_lengkap']);
    } elseif (!empty($pengaturan_sistem['nama_direktur']) && !preg_match('/^(direktur\s*utama|direktur|ceo|pimpinan)(\s*\/.*)?$/i', trim($pengaturan_sistem['nama_direktur']))) {
        $namaDirektur = trim($pengaturan_sistem['nama_direktur']);
    } elseif (!empty($keputusan_ceo['nama_approver']) && !preg_match('/^(direktur\s*utama|direktur|ceo|pimpinan)(\s*\/.*)?$/i', trim($keputusan_ceo['nama_approver']))) {
        $namaDirektur = trim($keputusan_ceo['nama_approver']);
    } else {
        $dbTmp = \Config\Database::connect();
        $uCeo = $dbTmp->table('users')
                      ->groupStart()
                          ->where('jabatan', 'CEO')
                          ->orLike('jabatan', 'CEO')
                          ->orLike('jabatan', 'Direktur')
                      ->groupEnd()
                      ->where('status', 1)
                      ->get()->getRowArray();
        $namaDirektur = (!empty($uCeo['nama_lengkap']) && !preg_match('/^(direktur\s*utama|direktur|ceo|pimpinan)(\s*\/.*)?$/i', trim($uCeo['nama_lengkap'])))
                        ? trim($uCeo['nama_lengkap'])
                        : 'Dr. H. Hendra Wijaya, S.E., M.M.';
    }

    $jabatanDirektur = !empty($pengaturan_sistem['jabatan_direktur']) 
                       ? $pengaturan_sistem['jabatan_direktur'] 
                       : (!empty($ceo_user['jabatan']) ? $ceo_user['jabatan'] : 'Direktur Utama / CEO');
    if (strcasecmp($jabatanDirektur, 'CEO') === 0) {
        $jabatanDirektur = 'Direktur Utama / CEO';
    }

    // Pastikan namaApproverCeo selalu terdefinisi sesuai namaDirektur
    $namaApproverCeo = $namaDirektur;

    // Pengaturan Tanda Tangan Digital CEO
    $isTtdAktif = !empty($ceo_user['is_ttd_aktif']) && !empty($ceo_user['ttd_digital']) && file_exists(FCPATH . $ceo_user['ttd_digital']);
    $ttdDigitalUrl = $isTtdAktif ? base_url($ceo_user['ttd_digital']) : null;
    $tanggalKeputusan    = !empty($keputusan_ceo['tanggal_approval'])
                            ? date('d', strtotime($keputusan_ceo['tanggal_approval'])) . ' ' .
                              ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n', strtotime($keputusan_ceo['tanggal_approval']))] . ' ' .
                              date('Y', strtotime($keputusan_ceo['tanggal_approval']))
                            : date('d') . ' ' . ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n')] . ' ' . date('Y');

    // Jenis surat berdasarkan keputusan
    $isDisetujui = in_array($keputusanCeoStatus, ['Disetujui', 'Disetujui Bersyarat', 'Bersyarat']);
    $isDitolak   = $keputusanCeoStatus === 'Ditolak';
    $isRevisi    = in_array($keputusanCeoStatus, ['Revisi', 'Kembalikan untuk Revisi']);

    $judulSurat = 'SURAT KEPUTUSAN KREDIT';
    if ($isDisetujui) $judulSurat = 'SURAT PERSETUJUAN KREDIT';
    if ($isDitolak)   $judulSurat = 'SURAT PENOLAKAN KREDIT';
    if ($isRevisi)    $judulSurat = 'SURAT PENGEMBALIAN BERKAS KREDIT';

    // Nomor surat
    $bulanRomawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    $nomorSurat  = '001/SK/' . ($isDitolak ? 'TLK' : ($isRevisi ? 'REV' : 'PK')) . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
    if (!empty($kredit['nomor_aplikasi'])) {
        // Ambil bagian angka dari nomor aplikasi untuk nomor surat
        $nomorUrut = preg_replace('/\D/', '', $kredit['nomor_aplikasi']);
        $nomorUrut = str_pad(substr($nomorUrut, -3), 3, '0', STR_PAD_LEFT);
        $nomorSurat = $nomorUrut . '/SK/' . ($isDitolak ? 'TLK' : ($isRevisi ? 'REV' : 'PK')) . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
    }

    // Hitung angsuran estimasi
    $angsuranVal = (float)($keuangan['angsuran_dipilih'] ?? ($keuangan['angsuran_installment'] ?? 0));
    if ($angsuranVal <= 0 && $plafonPengajuan > 0) {
        $tnrTmp = max(1, $tenorBulan);
        $rTmp   = $bungaPerBulan / 100;
        $mtdTmp = strtoupper($kredit['perhitungan_bunga'] ?? 'ANUITAS');
        if ($mtdTmp === 'ANUITAS' && $rTmp > 0) {
            $angsuranVal = round($plafonPengajuan * ($rTmp / (1 - pow(1 + $rTmp, -$tnrTmp))));
        } else {
            $angsuranVal = round(($plafonPengajuan / $tnrTmp) + ($plafonPengajuan * $rTmp));
        }
    }

    // Agunan
    $agunanList = $agunan ?? [];
?>

<!-- Toolbar No-Print -->
<div class="no-print sticky top-0 z-50 bg-slate-800 border-b border-slate-700 shadow-lg">
    <div class="max-w-5xl mx-auto px-3 sm:px-4 py-2.5 sm:py-3 flex flex-wrap items-center justify-between gap-2.5">
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <a href="javascript:history.back()" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 rounded-lg transition shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
            <div class="min-w-0">
                <div class="text-xs font-bold text-white truncate"><?= $judulSurat ?></div>
                <div class="text-[10px] sm:text-[11px] text-slate-400 truncate"><?= esc($nomorAplikasi) ?> • <?= esc($namaDebitur) ?></div>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <?php if ($isDisetujui): ?>
            <span class="px-2 sm:px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-500 text-white">
                ✓ <?= esc($keputusanCeoStatus) ?>
            </span>
            <?php elseif ($isDitolak): ?>
            <span class="px-2 sm:px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-bold bg-rose-500 text-white">
                ✕ Ditolak
            </span>
            <?php else: ?>
            <span class="px-2 sm:px-2.5 py-1 rounded-full text-[10px] sm:text-[11px] font-bold bg-amber-500 text-white">
                ⟳ Revisi
            </span>
            <?php endif; ?>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-1.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span class="hidden sm:inline">Cetak / Unduh PDF</span>
                <span class="sm:hidden">Cetak</span>
            </button>
        </div>
    </div>
</div>

<!-- Halaman Surat (A4 / Mobile Responsif) -->
<div class="w-full max-w-[794px] mx-auto my-2 sm:my-8 px-1 sm:px-0 no-print:shadow-2xl print-page">
<div class="bg-white min-h-[1123px] flex flex-col surat-page-card" style="font-family: 'Times New Roman', Times, serif; font-size: 12pt;">

    <!-- HEADER KOP SURAT -->
    <div class="surat-kop-wrap" style="border-bottom: 3px solid #1e3a8a; padding: 20px 48px 16px 48px;">
        <div class="surat-kop-inner" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <!-- Logo & Nama Lembaga -->
            <div style="display: flex; align-items: center; gap: 14px;">
                <img src="<?= base_url(esc($logoAplikasi)) ?>" alt="Logo" style="height: 55px; width: auto; max-width: 90px; object-fit: contain;" onerror="this.style.display='none'">
                <div>
                    <div style="font-size: 17pt; font-weight: 900; color: #1e3a8a; line-height: 1.15; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <?= esc($namaLembaga) ?>
                    </div>
                    <div style="font-size: 9pt; color: #475569; margin-top: 2px; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <?= esc($namaAplikasi) ?> — Sistem Analisa Kredit Digital
                    </div>
                </div>
            </div>
            <!-- Kontak -->
            <div class="surat-kop-kontak" style="text-align: right; font-size: 9pt; color: #475569; line-height: 1.6;">
                <div><?= esc($alamatLembaga) ?></div>
                <div>Telp. <?= esc($teleponLembaga) ?></div>
                <div>Email: <?= esc($emailLembaga) ?></div>
            </div>
        </div>
    </div>

    <!-- BODY SURAT -->
    <div class="surat-body-wrap" style="padding: 32px 48px; flex: 1;">

        <!-- Judul Surat -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-weight: 900; font-size: 14pt; text-decoration: underline; letter-spacing: 1px; font-family: 'Plus Jakarta Sans', sans-serif;">
                <?= $judulSurat ?>
            </div>
            <?php if ($isDisetujui && $keputusanCeoStatus === 'Disetujui Bersyarat'): ?>
            <div style="font-size: 10pt; color: #b45309; margin-top: 4px; font-weight: 600;">(Dengan Syarat dan Ketentuan Tambahan)</div>
            <?php endif; ?>
        </div>

        <!-- Nomor & Perihal -->
        <table style="margin-bottom: 20px; font-size: 11pt; width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 80px; vertical-align: top;">No.</td>
                <td style="width: 12px; vertical-align: top;">:</td>
                <td style="font-weight: 700;"><?= esc($nomorSurat) ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Perihal</td>
                <td style="vertical-align: top;">:</td>
                <td><?= esc($judulSurat) ?> – <?= esc($jenisKredit) ?></td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Tanggal</td>
                <td style="vertical-align: top;">:</td>
                <td><?= esc($tanggalKeputusan) ?></td>
            </tr>
        </table>

        <!-- Pembuka -->
        <p style="margin-bottom: 16px; text-align: justify; line-height: 1.8;">
            Yang bertanda tangan di bawah ini, atas nama Pimpinan / Direksi
            <strong><?= esc($namaLembaga) ?></strong>, dengan ini menyatakan bahwa Komite Kredit
            telah mengadakan rapat dan melakukan evaluasi kelayakan kredit secara independen berdasarkan
            metode analisa 5C (<em>Character, Capacity, Capital, Collateral, Condition</em>) dan
            <em>Credit Scoring</em> terhadap permohonan kredit sebagai berikut:
        </p>

        <!-- Data Debitur -->
        <div class="surat-box-content" style="border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; background: #f8fafc;">
            <div style="font-weight: 800; font-size: 11pt; margin-bottom: 10px; color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">
                DATA PEMOHON KREDIT
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11pt;">
                <tr>
                    <td class="surat-col-label" style="width: 180px; padding: 3px 0; vertical-align: top;">Nama Pemohon / Debitur</td>
                    <td style="width: 12px; padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="font-weight: 700; padding: 3px 0;"><strong><?= esc($namaDebitur) ?></strong></td>
                </tr>
                <?php if (!empty($noKtp) && $noKtp !== '-'): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">NIK / No. KTP</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($noKtp) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($alamatDebitur) && $alamatDebitur !== '-'): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Alamat</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($alamatDebitur) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pekerjaanDebitur) && $pekerjaanDebitur !== '-'): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Pekerjaan / Usaha</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($pekerjaanDebitur) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Nomor Aplikasi</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0; font-weight: 600;"><?= esc($nomorAplikasi) ?></td>
                </tr>
            </table>
        </div>

        <!-- Fasilitas Kredit -->
        <div class="surat-box-content" style="border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; background: #f8fafc;">
            <div style="font-weight: 800; font-size: 11pt; margin-bottom: 10px; color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">
                FASILITAS KREDIT <?= $isDisetujui ? 'YANG DISETUJUI' : 'YANG DIMOHON' ?>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11pt;">
                <tr>
                    <td class="surat-col-label" style="width: 180px; padding: 3px 0; vertical-align: top;">Jenis Kredit</td>
                    <td style="width: 12px; padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($jenisKredit) ?></td>
                </tr>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Tujuan Penggunaan</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($tujuanKredit) ?></td>
                </tr>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Plafon Kredit</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0; font-weight: 700;">Rp <?= number_format($plafonPengajuan, 0, ',', '.') ?></td>
                </tr>
                <?php if ($tenorBulan > 0): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Jangka Waktu</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= $tenorBulan ?> Bulan</td>
                </tr>
                <?php endif; ?>
                <?php if ($bungaPerTahun > 0): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Suku Bunga</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= number_format($bungaPerTahun, 2) ?>% per tahun (<?= number_format($bungaPerBulan, 2) ?>% per bulan, Efektif)</td>
                </tr>
                <?php endif; ?>
                <?php if ($angsuranVal > 0): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Estimasi Angsuran</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;">Rp <?= number_format($angsuranVal, 0, ',', '.') ?> / bulan</td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Skema Pembayaran</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($skemaPembayaran) ?></td>
                </tr>
            </table>
        </div>

        <!-- Agunan -->
        <?php if (!empty($agunanList)): ?>
        <div class="surat-box-content" style="border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; background: #f8fafc;">
            <div style="font-weight: 800; font-size: 11pt; margin-bottom: 10px; color: #1e3a8a; border-bottom: 1px solid #e2e8f0; padding-bottom: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">
                JAMINAN / AGUNAN
            </div>
            <?php foreach ($agunanList as $i => $ag): ?>
            <?php
                $mvAg = (float)($ag['nilai_pasar_mv'] ?? 0);
                $lvAg = (float)($ag['nilai_likuidasi_lv'] ?? ($mvAg * 0.6));
            ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 11pt; margin-bottom: <?= (count($agunanList) > 1 && $i < count($agunanList)-1) ? '10px' : '0' ?>;">
                <?php if (count($agunanList) > 1): ?>
                <tr><td colspan="3" style="font-weight: 700; padding-top: 4px; color: #334155;"><?= ($i+1) ?>. <?= esc($ag['jenis_agunan'] ?? 'Agunan') ?></td></tr>
                <?php endif; ?>
                <?php if (!empty($ag['keterangan_agunan']) || !empty($ag['jenis_agunan'])): ?>
                <tr>
                    <td class="surat-col-label" style="width: 180px; padding: 2px 0 2px 12px; vertical-align: top;">Jenis Agunan</td>
                    <td style="width: 12px; padding: 2px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 2px 0;"><?= esc($ag['jenis_agunan'] ?? '-') ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($ag['nomor_sertifikat']) || !empty($ag['no_bpkb'])): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 2px 0 2px 12px; vertical-align: top;">No. Sertifikat / BPKB</td>
                    <td style="padding: 2px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 2px 0;"><?= esc($ag['nomor_sertifikat'] ?? ($ag['no_bpkb'] ?? '-')) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($mvAg > 0): ?>
                <tr>
                    <td class="surat-col-label" style="padding: 2px 0 2px 12px; vertical-align: top;">Nilai Pasar (MV)</td>
                    <td style="padding: 2px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 2px 0;">Rp <?= number_format($mvAg, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="surat-col-label" style="padding: 2px 0 2px 12px; vertical-align: top;">Nilai Likuidasi (LV)</td>
                    <td style="padding: 2px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 2px 0;">Rp <?= number_format($lvAg, 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
            </table>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- KEPUTUSAN KOMITE -->
        <div class="surat-box-content" style="border: 2px solid <?= $isDisetujui ? '#059669' : ($isDitolak ? '#dc2626' : '#d97706') ?>; border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; background: <?= $isDisetujui ? '#f0fdf4' : ($isDitolak ? '#fff1f2' : '#fffbeb') ?>;">
            <div style="font-weight: 800; font-size: 11pt; margin-bottom: 10px; color: <?= $isDisetujui ? '#065f46' : ($isDitolak ? '#991b1b' : '#92400e') ?>; border-bottom: 1px solid <?= $isDisetujui ? '#a7f3d0' : ($isDitolak ? '#fecaca' : '#fde68a') ?>; padding-bottom: 6px; font-family: 'Plus Jakarta Sans', sans-serif;">
                KEPUTUSAN KOMITE KREDIT
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 11pt;">
                <tr>
                    <td class="surat-col-label" style="width: 180px; padding: 3px 0; vertical-align: top;">Status Keputusan</td>
                    <td style="width: 12px; padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0; font-weight: 900; font-size: 12pt; color: <?= $isDisetujui ? '#065f46' : ($isDitolak ? '#991b1b' : '#92400e') ?>;">
                        <?= strtoupper(esc($keputusanCeoStatus)) ?>
                    </td>
                </tr>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Tanggal Keputusan</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0;"><?= esc($tanggalKeputusan) ?></td>
                </tr>
                <tr>
                    <td class="surat-col-label" style="padding: 3px 0; vertical-align: top;">Pejabat Pemutus</td>
                    <td style="padding: 3px 0; vertical-align: top;">:</td>
                    <td class="surat-col-val" style="padding: 3px 0; font-weight: 600;"><?= esc($namaDirektur) ?></td>
                </tr>
            </table>
            <?php if (!empty($catatanCeo) && $catatanCeo !== '-'): ?>
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid <?= $isDisetujui ? '#a7f3d0' : ($isDitolak ? '#fecaca' : '#fde68a') ?>;">
                <div style="font-weight: 700; margin-bottom: 4px;">Pertimbangan / Catatan Keputusan:</div>
                <div style="text-align: justify; line-height: 1.7; color: #374151; font-style: italic;">
                    "<?= nl2br(esc($catatanCeo)) ?>"
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Isi / Klausula Surat -->
        <p style="margin-bottom: 12px; text-align: justify; line-height: 1.8;">
            <?php if ($isDisetujui): ?>
            Berdasarkan hasil evaluasi dan rapat Komite Kredit, permohonan kredit tersebut di atas
            <strong>DISETUJUI</strong> dengan ketentuan sebagai berikut:
            <?php elseif ($isDitolak): ?>
            Berdasarkan hasil evaluasi dan rapat Komite Kredit, dengan mempertimbangkan seluruh aspek kelayakan
            kredit, permohonan kredit tersebut di atas <strong>TIDAK DAPAT DISETUJUI / DITOLAK</strong>
            dengan pertimbangan sebagaimana tercantum di atas.
            <?php else: ?>
            Berdasarkan hasil evaluasi Komite Kredit, permohonan kredit tersebut di atas perlu dilakukan
            <strong>PERBAIKAN / REVISI</strong> berkas dan data terlebih dahulu sebelum dapat diproses lebih lanjut.
            <?php endif; ?>
        </p>

        <?php if ($isDisetujui): ?>
        <ol style="margin: 0 0 16px 20px; padding: 0; line-height: 1.8; font-size: 11pt;">
            <li style="margin-bottom: 6px;">Realisasi/pencairan kredit dapat dilakukan setelah seluruh persyaratan administrasi dan legalitas agunan dipenuhi dan ditandatangani.</li>
            <li style="margin-bottom: 6px;">Pengikatan agunan dilakukan secara notariil dengan Hak Tanggungan / Fidusia sebelum pencairan kredit dilaksanakan.</li>
            <li style="margin-bottom: 6px;">Debitur wajib membuka rekening tabungan di <?= esc($namaLembaga) ?> dan menyetujui pendebetan otomatis (auto-debet) angsuran setiap bulannya.</li>
            <li style="margin-bottom: 6px;">Agunan wajib diasuransikan dengan klausul <em>Banker's Clause</em> atas nama <?= esc($namaLembaga) ?>.</li>
            <li style="margin-bottom: 6px;">Keputusan persetujuan ini berlaku selama <strong>30 (tiga puluh) hari kalender</strong> sejak tanggal surat ini. Apabila dalam jangka waktu tersebut belum direalisasikan, keputusan ini batal demi hukum.</li>
            <?php if ($keputusanCeoStatus === 'Disetujui Bersyarat'): ?>
            <li style="margin-bottom: 6px; font-weight: 600; color: #b45309;">Persetujuan ini bersifat bersyarat. Debitur wajib memenuhi seluruh persyaratan tambahan yang tercantum dalam Catatan Keputusan sebelum pencairan dilakukan.</li>
            <?php endif; ?>
        </ol>
        <?php elseif ($isDitolak): ?>
        <p style="margin-bottom: 16px; text-align: justify; line-height: 1.8;">
            Demikian keputusan ini dibuat berdasarkan prinsip kehati-hatian dalam pemberian kredit.
            Pemohon dapat mengajukan kembali permohonan kredit apabila kondisi dan persyaratan telah
            memenuhi ketentuan yang berlaku.
        </p>
        <?php else: ?>
        <p style="margin-bottom: 16px; text-align: justify; line-height: 1.8;">
            Pemohon dimohon untuk melengkapi/memperbaiki berkas dan data sesuai catatan di atas, kemudian
            mengajukan kembali kepada petugas Account Officer (AO) untuk dilakukan proses analisa ulang.
        </p>
        <?php endif; ?>

        <p style="margin-bottom: 32px; text-align: justify; line-height: 1.8;">
            Demikian surat keputusan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
        </p>

        <!-- Tanda Tangan (Responsif HP & Desktop) -->
        <div class="surat-ttd-row" style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 24px;">
            <!-- Kiri: Penerima (Debitur) -->
            <div class="surat-ttd-col" style="text-align: center; min-width: 180px;">
                <div style="margin-bottom: 4px;">Pemohon,</div>
                <div style="height: 70px;"></div>
                <div style="border-top: 1px solid #374151; padding-top: 4px; font-weight: 700;">
                    <?= esc($namaDebitur) ?>
                </div>
                <div style="font-size: 10pt; color: #6b7280;">Debitur / Pemohon Kredit</div>
            </div>
            <!-- Kanan: Pimpinan/CEO -->
            <div class="surat-ttd-col" style="text-align: center; min-width: 200px;">
                <div style="margin-bottom: 4px;"><?= esc($namaLembaga) ?>,</div>
                <div style="font-size: 9.5pt; color: #64748b; margin-bottom: 2px;">
                    <?= esc($tanggalKeputusan) ?>
                </div>
                <?php if ($isTtdAktif && $ttdDigitalUrl): ?>
                    <div style="height: 70px; display: flex; align-items: center; justify-content: center; margin: 2px 0;">
                        <img src="<?= $ttdDigitalUrl ?>" alt="Tanda Tangan Digital CEO" style="max-height: 65px; max-width: 170px; object-fit: contain;">
                    </div>
                <?php else: ?>
                    <div style="height: 70px;"></div>
                <?php endif; ?>
                <div style="border-top: 1px solid #374151; padding-top: 4px; font-weight: 700; text-decoration: underline;">
                    <?= esc($namaDirektur) ?>
                </div>
                <div style="font-size: 10pt; color: #6b7280;"><?= esc($jabatanDirektur) ?></div>
            </div>
        </div>

    </div><!-- /body -->

    <!-- FOOTER SURAT -->
    <div class="surat-footer-wrap" style="border-top: 2px solid #1e3a8a; padding: 10px 48px; background: #f1f5f9; margin-top: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 8.5pt; color: #64748b;" class="flex-col sm:flex-row gap-2">
            <div>
                <strong><?= esc($namaLembaga) ?></strong> — <?= esc($namaAplikasi) ?><br>
                Dokumen ini diterbitkan secara digital oleh Sistem Komite Kredit.
            </div>
            <div class="text-center sm:text-right">
                No. Ref: <?= esc($nomorAplikasi) ?><br>
                Dicetak: <?= date('d/m/Y H:i') ?> WIB
            </div>
        </div>
    </div>

</div><!-- /bg-white -->
</div><!-- /print-page -->

</body>
</html>
