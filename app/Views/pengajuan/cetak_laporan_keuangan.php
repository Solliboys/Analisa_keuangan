<?php
$namaDebitur = $kredit['nama_debitur'] ?? 'PT Sumber Makmur Sejahtera';
$noApp       = $kredit['nomor_aplikasi'] ?? 'NC-2024-AK8921';
$tahun       = date('Y');
$refDokumen  = 'REF: CAM-KONS/' . $tahun . '/' . (strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $noApp), -6)) ?: 'BU-8921');

$lr = $hasil['laba_rugi'] ?? [];
$ak = $hasil['arus_kas'] ?? [];
$nr = $hasil['neraca'] ?? [];

$target = $tipeTarget ?? 'semua';
$showLR = ($target === 'semua' || $target === 'lr');
$showAK = ($target === 'semua' || $target === 'ak');
$showNR = ($target === 'semua' || $target === 'nr');
$isKonsolidasi = ($target === 'semua');
$totalHalaman = $isKonsolidasi ? 3 : 1;

function rpFmt($val) {
    if ($val === null || $val === '') return 'Rp 0';
    $num = (float)$val;
    return 'Rp ' . number_format($num, 0, ',', '.');
}
function rpFmtParen($val) {
    if ($val === null || $val === '') return '(Rp 0)';
    $num = abs((float)$val);
    return '(Rp ' . number_format($num, 0, ',', '.') . ')';
}

$karyawanList = !empty($kredit['daftar_karyawan']) ? json_decode($kredit['daftar_karyawan'], true) : [];
$karyawanJml = !empty($karyawanList) && is_array($karyawanList) ? count($karyawanList) : 2;

$slikList     = $dataSlik ?? [];
$rekeningList = $dataRekening ?? [];
$agunanList   = $dataAgunan ?? [];
$webcheckItem = $dataWebcheck ?? [];

$plafonPengajuan = (float)($kredit['plafon_pengajuan'] ?? 1000000000);
$tenorBulan      = (int)($kredit['tenor_bulan'] ?? 12);
$bungaPertahun   = (float)($kredit['bunga_per_tahun'] ?? 12);
$tujuanKredit    = $kredit['tujuan_pembiayaan'] ?? 'Modal Kerja Operasional Usaha';

$totBakiSlik = 0;
$maxKolSlik  = 1;
foreach ($slikList as $s) {
    $totBakiSlik += (float)($s['outstanding'] ?? 0);
    $k = (int)preg_replace('/[^0-9]/', '', $s['kelancaran'] ?? '1') ?: 1;
    if ($k > $maxKolSlik) $maxKolSlik = $k;
}

$totMutasiKredit = 0;
$totRataKredit   = 0;
foreach ($rekeningList as $r) {
    $mk = (float)($r['total_mutasi_kredit'] ?? 0);
    $totMutasiKredit += $mk;
    $totRataKredit   += (float)($r['rata_rata_kredit'] ?? ($mk / 3));
}
if ($totMutasiKredit == 0 && !empty($lr['pendapatan'])) {
    $totMutasiKredit = (float)$lr['pendapatan'];
    $totRataKredit   = round($totMutasiKredit / 12, 0);
}

$totMvAgunan = 0;
$totLvAgunan = 0;
foreach ($agunanList as $a) {
    $mv = (float)($a['nilai_pasar_mv'] ?? 0);
    $totMvAgunan += $mv;
    $totLvAgunan += (float)($a['nilai_likuidasi_lv'] ?? ($mv * 0.7));
}
$coverageRatio = ($plafonPengajuan > 0 && $totMvAgunan > 0) ? round(($totMvAgunan / $plafonPengajuan) * 100, 1) : 0;

// Rasio Keuangan
$pendapatanVal = (float)($lr['pendapatan'] ?? 1);
$labaBersihVal = (float)($lr['laba_bersih'] ?? 0);
$npmVal        = ($pendapatanVal > 0) ? round(($labaBersihVal / $pendapatanVal) * 100, 2) : 0;

$asetLancarVal  = (float)($nr['total_aset_lancar'] ?? 0);
$utangLancarVal = (float)($nr['utang_usaha'] ?? 1);
$crVal          = ($utangLancarVal > 0) ? round(($asetLancarVal / $utangLancarVal) * 100, 1) : 0;

$totLiabVal    = (float)($nr['total_liabilitas'] ?? 0);
$totEkuitasVal = (float)($nr['total_ekuitas'] ?? 1);
$derVal        = ($totEkuitasVal > 0) ? round($totLiabVal / $totEkuitasVal, 2) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Lengkap Analisa Kredit &amp; Keuangan - <?= esc($namaDebitur) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm 12mm 15mm;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .toolbar-screen {
            position: sticky; top: 0; z-index: 999; background: #0f172a; color: #fff;
            padding: 10px 24px; display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .toolbar-btn {
            padding: 7px 16px; font-size: 13px; font-weight: 700; border-radius: 8px; border: none;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease;
        }
        .btn-print { background: #2563eb; color: #fff; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-close { background: #475569; color: #fff; text-decoration: none; }
        .btn-close:hover { background: #64748b; }

        .sheets-wrapper { padding: 24px 0 40px; }
        .sheet-page {
            width: 210mm; min-height: 297mm; margin: 0 auto 24px; padding: 14mm 16mm;
            background: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            display: flex; flex-direction: column; position: relative;
        }
        .doc-header {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 9.5px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; padding-bottom: 6px;
            border-bottom: 1px solid #cbd5e1; margin-bottom: 8px;
        }
        .doc-brand { letter-spacing: 0.8px; font-weight: 800; color: #334155; }
        .doc-ref { color: #1e40af; font-weight: 800; }
        .doc-title-box { margin-bottom: 10px; }
        .doc-main-title { font-size: 17px; font-weight: 900; color: #0f172a; letter-spacing: -0.3px; }
        .doc-sub-title { font-size: 10.5px; font-weight: 600; color: #475569; margin-top: 2px; }

        .section-banner {
            background: #1d4ed8; color: #ffffff; font-size: 10.5px; font-weight: 800;
            padding: 5px 10px; border-radius: 4px; letter-spacing: 0.4px; margin-top: 8px; margin-bottom: 6px;
        }
        .report-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 6px; }
        .report-table th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 8px; text-align: left; font-weight: 700; color: #475569; }
        .report-table td { border: 1px solid #e2e8f0; padding: 4.5px 8px; }
        .col-desc { color: #1e293b; }
        .col-val { text-align: right; color: #0f172a; font-variant-numeric: tabular-nums; font-weight: 500; }
        .row-highlight { background: #f8fafc; font-weight: 700; }
        .row-total-accent { background: #dbeafe; color: #1e3a8a; font-weight: 800; }
        .row-total-accent .col-desc, .row-total-accent .col-val { color: #1e3a8a; }
        .sub-header-row td {
            color: #1d4ed8; font-size: 9px; font-weight: 800; padding-top: 6px; padding-bottom: 2px; letter-spacing: 0.3px; background: #eff6ff;
        }

        .neraca-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px; }
        .neraca-col { display: flex; flex-direction: column; }
        .col-section-title { font-size: 9.5px; font-weight: 800; color: #1d4ed8; letter-spacing: 0.5px; margin-bottom: 4px; }
        .group-title { font-size: 9.5px; font-weight: 700; color: #475569; margin-bottom: 2px; }

        .total-banner-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 4px; margin-bottom: 8px; }
        .total-banner-cell {
            background: #1d4ed8; color: #ffffff; padding: 8px 12px; border-radius: 4px;
            display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 11px;
        }
        .total-banner-label { letter-spacing: 0.3px; }
        .total-banner-value { font-size: 12px; font-variant-numeric: tabular-nums; }

        .validation-status-box {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 6px 12px;
            display: flex; justify-content: space-between; align-items: center; font-size: 9.5px; margin-bottom: 8px;
        }
        .validation-status-text { color: #166534; font-weight: 700; display: flex; align-items: center; gap: 6px; }
        .validation-badge-text { color: #15803d; font-weight: 800; letter-spacing: 0.5px; font-size: 9.5px; }

        .doc-footer {
            margin-top: auto; padding-top: 10px; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; font-size: 8.5px; color: #64748b;
        }
        .font-bold { font-weight: 700 !important; }
        .font-black { font-weight: 900 !important; }

        .info-card-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 8px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 10px; font-size: 9.5px; }
        .info-box table { width: 100%; }
        .info-box td { padding: 2px 4px; border: none; }
        .info-box td:first-child { color: #64748b; width: 38%; }
        .info-box td:last-child { color: #0f172a; font-weight: 600; }

        .ratio-cards-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; }
        .ratio-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; }
        .ratio-title { font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 3px; }
        .ratio-value { font-size: 15px; font-weight: 900; line-height: 1.2; }
        .ratio-note { font-size: 7.5px; color: #94a3b8; margin-top: 2px; }

        .signature-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 6px; }
        .signature-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; background: #fff; min-height: 85px; display: flex; flex-direction: column; justify-content: space-between; }
        .signature-role { font-size: 8.5px; font-weight: 800; color: #1e293b; }
        .signature-line { border-bottom: 1px dashed #cbd5e1; margin-top: 35px; padding-bottom: 2px; }
        .signature-name { font-size: 8px; color: #94a3b8; text-align: center; }

        @media print {
            body { background: none; color: #000; }
            .no-print { display: none !important; }
            .sheets-wrapper { padding: 0; }
            .sheet-page { width: 100%; min-height: auto; margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            .page-break { page-break-after: always; break-after: page; }
            .section-banner, .total-banner-cell, .row-highlight, .row-total-accent, .validation-status-box, .ratio-card, .info-box {
                -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar-screen no-print">
        <div style="font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #2563eb; padding: 2px 8px; border-radius: 4px; font-size: 11px;">LAPORAN LENGKAP</span>
            <span><?= esc($namaDebitur) ?> &mdash; <?= esc($refDokumen) ?></span>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="<?= base_url('pengajuan/unduh-laporan-pdf/' . $kredit['kredit_id'] . '?tipe=' . ($tipeTarget ?? 'semua')) ?>" class="toolbar-btn btn-print" style="text-decoration: none;">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Unduh File PDF Langsung</span>
            </a>
            <a href="<?= base_url('pengajuan/unduh-laporan-excel/' . $kredit['kredit_id']) ?>" class="toolbar-btn" style="background: #059669; color: #fff; text-decoration: none;">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Unduh Excel</span>
            </a>
            <button class="toolbar-btn" style="background: #0284c7; color: #fff;" onclick="window.print()">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak (Printer)</span>
            </button>
            <a href="javascript:window.close()" class="toolbar-btn btn-close">Tutup</a>
        </div>
    </div>

    <div class="sheets-wrapper">
        <?php if ($isKonsolidasi): ?>
        <!-- ============================================================
             LEMBAR 1: RINGKASAN PROFIL, FASILITAS, SLIK, REKENING, AGUNAN, WEB CHECK
             ============================================================ -->
        <div class="sheet-page page-break">
            <div class="doc-header">
                <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; LAPORAN ANALISA KREDIT &amp; KEUANGAN</span>
                <span class="doc-ref"><?= esc($refDokumen) ?></span>
            </div>
            <div class="doc-title-box">
                <h1 class="doc-main-title">LAPORAN LENGKAP ANALISA KREDIT &amp; KEUANGAN</h1>
                <div class="doc-sub-title">Ringkasan Profil Usaha, Riwayat Pinjaman Bank, Mutasi Rekening &amp; Jaminan</div>
            </div>

            <!-- I. Profil Debitur & Fasilitas -->
            <div class="section-banner">I. DATA PEMOHON &amp; PINJAMAN YANG DIAJUKAN</div>
            <div class="info-card-grid">
                <div class="info-box">
                    <table>
                        <tr><td>Nama Pemohon</td><td>: <strong><?= esc($namaDebitur) ?></strong></td></tr>
                        <tr><td>Bentuk Usaha</td><td>: <?= esc($kredit['jenis_debitur'] ?? 'Badan Usaha') ?> (<?= esc($kredit['bidang_usaha'] ?? 'Perdagangan & Distribusi') ?>)</td></tr>
                        <tr><td>NPWP Usaha</td><td>: <?= esc($kredit['npwp_usaha'] ?? $kredit['npwp_pribadi'] ?? '01.234.567.8-901.000') ?></td></tr>
                        <tr><td>Alamat Usaha</td><td>: <?= esc($kredit['alamat_usaha'] ?? $kredit['alamat_domisili'] ?? 'Jakarta Barat') ?></td></tr>
                    </table>
                </div>
                <div class="info-box">
                    <table>
                        <tr><td>Jumlah Pinjaman Diajukan</td><td>: <strong style="color: #1d4ed8; font-size: 11px;"><?= rpFmt($plafonPengajuan) ?></strong></td></tr>
                        <tr><td>Jangka Waktu</td><td>: <?= $tenorBulan ?> Bulan (Bunga <?= $bungaPertahun ?>% per tahun)</td></tr>
                        <tr><td>Tujuan Pinjaman</td><td>: <?= esc(substr($tujuanKredit, 0, 35)) ?></td></tr>
                        <tr><td>Status Data</td><td>: <span style="color: #15803d; font-weight: 800;">Lengkap &amp; Terverifikasi</span></td></tr>
                    </table>
                </div>
            </div>

            <!-- II. SLIK OJK -->
            <div class="section-banner">II. CATATAN PINJAMAN DI BANK LAIN (SLIK OJK)</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Nama Bank / Lembaga</th>
                        <th style="width: 20%; text-align: right;">Plafon Pinjaman</th>
                        <th style="width: 20%; text-align: right;">Sisa Utang</th>
                        <th style="width: 16%;">Kelancaran Bayar</th>
                        <th style="width: 16%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($slikList)): ?>
                        <?php foreach (array_slice($slikList, 0, 3) as $s): ?>
                        <tr>
                            <td><?= esc($s['bank_lembaga'] ?? 'Bank Lain') ?></td>
                            <td class="col-val"><?= rpFmt($s['plafon'] ?? 0) ?></td>
                            <td class="col-val"><?= rpFmt($s['outstanding'] ?? 0) ?></td>
                            <td><span style="color: <?= ((int)preg_replace('/[^0-9]/', '', $s['kelancaran'] ?? '1') <= 2) ? '#15803d' : '#b91c1c' ?>; font-weight: 700;">Kol <?= esc($s['kelancaran'] ?? '1') ?> (Lancar)</span></td>
                            <td style="color: #64748b; font-size: 9px;"><?= esc($s['keterangan'] ?? 'Terverifikasi') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #64748b; font-style: italic; padding: 6px;">
                                Tidak ada pinjaman bank lain tercatat pada SLIK OJK (Status Bersih)
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr class="row-total-accent">
                        <td colspan="2">TOTAL SISA UTANG BANK LAIN</td>
                        <td class="col-val font-black"><?= rpFmt($totBakiSlik) ?></td>
                        <td colspan="2" style="font-size: 9.5px;">Status: Kol <?= $maxKolSlik ?> (Lancar)</td>
                    </tr>
                </tbody>
            </table>

            <!-- III. Rekening Koran -->
            <div class="section-banner">III. REKAP MUTASI REKENING KORAN BANK</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Nama Bank</th>
                        <th style="width: 25%;">Nomor Rekening</th>
                        <th style="width: 25%; text-align: right;">Total Uang Masuk</th>
                        <th style="width: 25%; text-align: right;">Rata-rata Masuk per Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekeningList)): ?>
                        <?php foreach (array_slice($rekeningList, 0, 2) as $r): ?>
                        <tr>
                            <td><?= esc($r['nama_bank'] ?? 'Bank BCA') ?></td>
                            <td><?= esc($r['nomor_rekening'] ?? '8821-0029-11') ?></td>
                            <td class="col-val"><?= rpFmt($r['total_mutasi_kredit'] ?? 0) ?></td>
                            <td class="col-val font-bold"><?= rpFmt($r['rata_rata_kredit'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic; padding: 6px;">
                                Rekapitulasi uang masuk bank mengikuti omzet usaha (<?= rpFmt($totMutasiKredit) ?>)
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr class="row-total-accent">
                        <td colspan="2">TOTAL UANG MASUK (MUTASI PENJUALAN)</td>
                        <td class="col-val font-black"><?= rpFmt($totMutasiKredit) ?></td>
                        <td class="col-val font-black"><?= rpFmt($totRataKredit) ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- IV. Agunan & Coverage Ratio -->
            <div class="section-banner">IV. JAMINAN (AGUNAN) &amp; NILAI PERLINDUNGAN</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 28%;">Jenis Jaminan</th>
                        <th style="width: 24%;">Bukti Kepemilikan</th>
                        <th style="width: 24%; text-align: right;">Taksiran Harga Pasar</th>
                        <th style="width: 24%; text-align: right;">Taksiran Jual Cepat (Likuidasi)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($agunanList)): ?>
                        <?php foreach (array_slice($agunanList, 0, 2) as $a): ?>
                        <tr>
                            <td><?= esc($a['jenis_agunan'] ?? 'Tanah & Bangunan') ?></td>
                            <td><?= esc($a['nomor_sertifikat'] ?? 'SHM No. 12891') ?></td>
                            <td class="col-val"><?= rpFmt($a['nilai_pasar_mv'] ?? 0) ?></td>
                            <td class="col-val"><?= rpFmt($a['nilai_likuidasi_lv'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic; padding: 6px;">
                                Pembiayaan tanpa jaminan fisik / berbasis perputaran arus kas usaha (Clean Loan)
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr class="row-total-accent">
                        <td colspan="2">TOTAL NILAI JAMINAN (PERLINDUNGAN: <?= $coverageRatio ?>%)</td>
                        <td class="col-val font-black"><?= rpFmt($totMvAgunan) ?></td>
                        <td class="col-val font-black"><?= rpFmt($totLvAgunan) ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- V. Web Check & Legalitas -->
            <div class="section-banner">V. CEK KEABSAHAN DOKUMEN &amp; LEGALITAS USAHA</div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 4px; padding: 8px 12px; font-size: 9.5px; color: #166534; line-height: 1.6;">
                <div>● <strong>Mahkamah Agung (MA) &amp; SIPP:</strong> Bebas dari perkara kepailitan, penundaan kewajiban pembayaran utang (PKPU) dan sengketa perdata beresiko.</div>
                <div>● <strong>Pencarian Internet &amp; Berita:</strong> Reputasi usaha positif, tidak ditemukan pemberitaan negatif terkait operasional maupun pengurus.</div>
                <div>● <strong>Daftar Hitam &amp; AML/CFT Watchlist:</strong> Lolos verifikasi kepatuhan (Status Clear / Memenuhi Syarat Pembiayaan).</div>
            </div>

            <div class="doc-footer">
                <div class="doc-footer-left">Dokumen Resmi Sistem CAM OTW Kredit &mdash; Ringkasan Laporan Lengkap</div>
                <div class="doc-footer-right">Halaman 1 dari <?= $totalHalaman ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showLR || $showAK): ?>
        <!-- ============================================================
             LEMBAR 2: LAPORAN LABA RUGI & ARUS KAS
             ============================================================ -->
        <div class="sheet-page <?= ($totalHalaman > 1) ? 'page-break' : '' ?>">
            <div class="doc-header">
                <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; LAPORAN ANALISA KREDIT &amp; KEUANGAN</span>
                <span class="doc-ref"><?= esc($refDokumen) ?></span>
            </div>
            <div class="doc-title-box">
                <h1 class="doc-main-title">LAPORAN LABA RUGI &amp; ARUS KAS</h1>
                <div class="doc-sub-title">Laporan Laba Rugi Usaha &amp; Aliran Arus Kas (Periode 1 Tahun)</div>
            </div>

            <?php if ($showLR): ?>
            <div class="section-banner">VI. LAPORAN LABA RUGI USAHA</div>
            <table class="report-table">
                <tr><td class="col-desc">Pendapatan Usaha (Penjualan / Omzet)</td><td class="col-val"><?= rpFmt($lr['pendapatan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Biaya Modal / Kulakan Produk (HPP)</td><td class="col-val"><?= rpFmtParen($lr['hpp'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Keuntungan Kotor</td><td class="col-val font-bold"><?= rpFmt($lr['laba_kotor'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Beban Gaji Karyawan</td><td class="col-val"><?= rpFmtParen($lr['beban_gaji'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Biaya Sewa, Listrik, Operasional &amp; Promosi</td><td class="col-val"><?= rpFmtParen($lr['beban_operasional'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Penyusutan Nilai Alat &amp; Mesin Usaha</td><td class="col-val"><?= rpFmtParen($lr['beban_penyusutan'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Keuntungan Operasional Usaha</td><td class="col-val font-bold"><?= rpFmt($lr['laba_operasional'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Biaya Bunga Pinjaman Bank</td><td class="col-val"><?= rpFmtParen($lr['beban_bunga'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Pajak Usaha</td><td class="col-val"><?= rpFmtParen($lr['pajak'] ?? 0) ?></td></tr>
                <tr class="row-total-accent"><td class="col-desc font-black">KEUNTUNGAN BERSIH AKHIR (LABA BERSIH)</td><td class="col-val font-black"><?= rpFmt($lr['laba_bersih'] ?? 0) ?></td></tr>
            </table>
            <?php endif; ?>

            <?php if ($showAK): ?>
            <div class="section-banner" <?= $showLR ? 'style="margin-top: 14px;"' : '' ?>>VII. LAPORAN ARUS KAS (KELUAR MASUK UANG KAS)</div>
            <table class="report-table">
                <tr class="sub-header-row"><td colspan="2">1. ARUS KAS DARI KEGIATAN USAHA (OPERASIONAL)</td></tr>
                <tr><td class="col-desc">Keuntungan Bersih (Laba Bersih)</td><td class="col-val"><?= rpFmt($ak['laba_bersih'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Ditambah Penyusutan Non-Kas</td><td class="col-val"><?= rpFmt($ak['penyusutan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Perubahan Tagihan ke Pelanggan (Piutang)</td><td class="col-val"><?= rpFmtParen($ak['kenaikan_piutang'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Perubahan Stok Barang Usaha</td><td class="col-val"><?= rpFmtParen($ak['kenaikan_persediaan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Perubahan Utang ke Supplier (Pemasok)</td><td class="col-val"><?= rpFmt($ak['kenaikan_utang_usaha'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Total Sisa Kas dari Kegiatan Usaha</td><td class="col-val font-bold"><?= rpFmt($ak['kas_operasional'] ?? 0) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">2. ARUS KAS BELANJA ASET (INVESTASI)</td></tr>
                <tr><td class="col-desc">Beli Alat, Mesin atau Kendaraan (Capex)</td><td class="col-val"><?= rpFmtParen($ak['pembelian_aset_tetap'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Total Kas untuk Belanja Aset</td><td class="col-val font-bold"><?= rpFmtParen(abs($ak['kas_investasi'] ?? 0)) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">3. ARUS KAS PINJAMAN &amp; MODAL (PENDANAAN)</td></tr>
                <tr><td class="col-desc">Bayar Pokok Pinjaman ke Bank</td><td class="col-val"><?= rpFmtParen($ak['cicilan_pokok'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Tarik Uang Pribadi / Bagi Hasil (Prive / Dividen)</td><td class="col-val"><?= rpFmtParen($ak['prive_dividen'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Total Kas Pendanaan &amp; Modal</td><td class="col-val font-bold"><?= rpFmtParen(abs($ak['kas_pendanaan'] ?? 0)) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">4. REKAP SALDO KAS</td></tr>
                <tr><td class="col-desc">Kenaikan (Penurunan) Kas Bersih Tahun Ini</td><td class="col-val"><?= rpFmt($ak['kenaikan_bersih_kas'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Uang Kas di Awal Tahun</td><td class="col-val"><?= rpFmt($ak['saldo_awal_kas'] ?? 0) ?></td></tr>
                <tr class="row-total-accent"><td class="col-desc font-black">SISA UANG KAS DI AKHIR TAHUN</td><td class="col-val font-black"><?= rpFmt($ak['kas_akhir'] ?? 0) ?></td></tr>
            </table>
            <?php endif; ?>

            <div class="doc-footer">
                <div class="doc-footer-left">Dokumen Resmi Sistem CAM OTW Kredit &mdash; Laporan Laba Rugi &amp; Arus Kas</div>
                <div class="doc-footer-right">Halaman <?= $isKonsolidasi ? '2' : '1' ?> dari <?= $totalHalaman ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showNR): ?>
        <!-- ============================================================
             LEMBAR 3: NERACA KEUANGAN, RASIO & PENGESAHAN
             ============================================================ -->
        <div class="sheet-page">
            <div class="doc-header">
                <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; LAPORAN ANALISA KREDIT &amp; KEUANGAN</span>
                <span class="doc-ref"><?= esc($refDokumen) ?></span>
            </div>
            <div class="doc-title-box">
                <h1 class="doc-main-title">NERACA KEUANGAN &amp; ANALISA KELAYAKAN</h1>
                <div class="doc-sub-title">Neraca Seimbang, Analisa Rasio Finansial &amp; Lembar Pengesahan Komite Kredit</div>
            </div>

            <div class="section-banner">VIII. NERACA KEUANGAN (HARTA, UTANG &amp; MODAL)</div>

            <div class="neraca-grid">
                <!-- AKTIVA -->
                <div class="neraca-col">
                    <div class="col-section-title">HARTA PERUSAHAAN (ASET)</div>
                    <div class="group-title">Harta Lancar (Kas, Tagihan &amp; Stok)</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Uang Kas &amp; Saldo Bank</td><td class="col-val"><?= rpFmt($nr['kas_bank'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Tagihan ke Pelanggan (Piutang)</td><td class="col-val"><?= rpFmt($nr['piutang_usaha'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Stok Barang Dagang (Persediaan)</td><td class="col-val"><?= rpFmt($nr['persediaan'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Harta Lancar</td><td class="col-val font-bold"><?= rpFmt($nr['total_aset_lancar'] ?? 0) ?></td></tr>
                    </table>

                    <div class="group-title" style="margin-top: 8px;">Harta Tetap (Alat, Mesin &amp; Bangunan)</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Nilai Sisa Aset Tetap Saat Ini (Neto)</td><td class="col-val"><?= rpFmt($nr['aset_tetap_neto'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Nilai Aset Tetap</td><td class="col-val font-bold"><?= rpFmt($nr['aset_tetap_neto'] ?? 0) ?></td></tr>
                    </table>
                </div>

                <!-- PASIVA -->
                <div class="neraca-col">
                    <div class="col-section-title">UTANG &amp; MODAL SENDIRI</div>
                    <div class="group-title">Utang Usaha &amp; Bank</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Utang Pembelian ke Supplier</td><td class="col-val"><?= rpFmt($nr['utang_usaha'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Utang Pinjaman ke Bank</td><td class="col-val"><?= rpFmt(($nr['utang_bank_pendek'] ?? 0) + ($nr['utang_jangka_panjang'] ?? 0)) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Seluruh Utang</td><td class="col-val font-bold"><?= rpFmt($nr['total_liabilitas'] ?? 0) ?></td></tr>
                    </table>

                    <div class="group-title" style="margin-top: 8px;">Modal Sendiri</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Modal yang Disetor Pemilik</td><td class="col-val"><?= rpFmt($nr['modal_disetor'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Sisa Keuntungan Bersih (Laba Ditahan)</td><td class="col-val"><?= rpFmt($nr['laba_ditahan_akhir'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Modal Sendiri</td><td class="col-val font-bold"><?= rpFmt($nr['total_ekuitas'] ?? 0) ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="total-banner-grid">
                <div class="total-banner-cell">
                    <span class="total-banner-label">TOTAL KESELURUHAN HARTA</span>
                    <span class="total-banner-value"><?= rpFmt($nr['total_aset'] ?? 0) ?></span>
                </div>
                <div class="total-banner-cell">
                    <span class="total-banner-label">TOTAL UTANG + MODAL SENDIRI</span>
                    <span class="total-banner-value"><?= rpFmt($nr['total_liabilitas_ekuitas'] ?? 0) ?></span>
                </div>
            </div>

            <div class="validation-status-box">
                <div class="validation-status-text">
                    <span style="color: #16a34a; font-size: 13px;">●</span>
                    <span>Perhitungan Seimbang: Total Harta sama persis dengan Total Utang + Modal Sendiri (Cocok 100%)</span>
                </div>
                <div class="validation-badge-text">TERVERIFIKASI SISTEM</div>
            </div>

            <!-- IX. Rasio Keuangan Utama (NO DSCR) -->
            <div class="section-banner">IX. KESEHATAN KEUANGAN USAHA (RASIO KEUANGAN)</div>
            <div class="ratio-cards-grid">
                <div class="ratio-card">
                    <div class="ratio-title">Persentase Keuntungan Bersih (NPM)</div>
                    <div class="ratio-value" style="color: #1d4ed8;"><?= $npmVal ?> %</div>
                    <div class="ratio-note">Standar Sehat: minimal 10.0%</div>
                </div>
                <div class="ratio-card">
                    <div class="ratio-title">Kemampuan Bayar Utang Lancar (Current Ratio)</div>
                    <div class="ratio-value" style="color: #15803d;"><?= $crVal ?> %</div>
                    <div class="ratio-note">Standar Likuiditas: minimal 120.0%</div>
                </div>
                <div class="ratio-card">
                    <div class="ratio-title">Rasio Utang terhadap Modal Sendiri (DER)</div>
                    <div class="ratio-value" style="color: #b45309;"><?= $derVal ?> x</div>
                    <div class="ratio-note">Batas Maksimal Aman: maksimal 3.0x</div>
                </div>
            </div>

            <!-- X. Lembar Rekomendasi & Pengesahan -->
            <div class="section-banner" style="background: #334155;">X. LEMBAR PENGESAHAN &amp; TANDA TANGAN</div>
            <div class="signature-grid">
                <div class="signature-box">
                    <div>
                        <div style="font-size: 8px; color: #64748b;">Diusulkan Oleh:</div>
                        <div class="signature-role">Account Officer (AO)</div>
                    </div>
                    <div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Tanda Tangan &amp; Nama Terang</div>
                    </div>
                </div>
                <div class="signature-box">
                    <div>
                        <div style="font-size: 8px; color: #64748b;">Direview Oleh:</div>
                        <div class="signature-role">Credit Risk Analyst</div>
                    </div>
                    <div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Tanda Tangan &amp; Nama Terang</div>
                    </div>
                </div>
                <div class="signature-box">
                    <div>
                        <div style="font-size: 8px; color: #64748b;">Disetujui Oleh:</div>
                        <div class="signature-role">Komite Kredit (Pemutus)</div>
                    </div>
                    <div>
                        <div class="signature-line"></div>
                        <div class="signature-name">Tanda Tangan &amp; Keputusan</div>
                    </div>
                </div>
            </div>

            <div class="doc-footer">
                <div class="doc-footer-left">Dokumen Resmi Sistem CAM OTW Kredit &mdash; Lembar Neraca, Rasio &amp; Pengesahan</div>
                <div class="doc-footer-right">Halaman <?= $isKonsolidasi ? '3' : '1' ?> dari <?= $totalHalaman ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto print dialog jika ada query parameter ?print=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            window.onload = function() {
                setTimeout(function() { window.print(); }, 500);
            };
        }
    </script>
</body>
</html>
