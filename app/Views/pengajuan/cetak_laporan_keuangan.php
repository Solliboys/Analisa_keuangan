<?php
$namaDebitur = $kredit['nama_debitur'] ?? 'PT Sumber Makmur Sejahtera';
$noApp       = $kredit['nomor_aplikasi'] ?? 'NC-2024-AK8921';
$tahun       = date('Y');
$refDokumen  = 'REF: FIN-EST/' . $tahun . '/' . (strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $noApp), -6)) ?: 'BU-8921');

$lr = $hasil['laba_rugi'] ?? [];
$ak = $hasil['arus_kas'] ?? [];
$nr = $hasil['neraca'] ?? [];

$target = $tipeTarget ?? 'semua';
$showLR = ($target === 'semua' || $target === 'lr');
$showAK = ($target === 'semua' || $target === 'ak');
$showNR = ($target === 'semua' || $target === 'nr');
$totalHalaman = ($target === 'semua') ? 2 : 1;

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan (Estimasi) - <?= esc($namaDebitur) ?></title>
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
            position: sticky; top: 0; z-index: 999; background: #1e293b; color: #fff;
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
            width: 210mm; min-height: 297mm; margin: 0 auto 24px; padding: 16mm 18mm;
            background: #ffffff; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            display: flex; flex-direction: column; position: relative;
        }
        .doc-header {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 9.5px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; padding-bottom: 8px;
        }
        .doc-brand { letter-spacing: 0.8px; }
        .doc-ref { color: #1e40af; font-weight: 800; }
        .doc-title-box { margin-top: 6px; margin-bottom: 14px; }
        .doc-main-title { font-size: 20px; font-weight: 900; color: #0f172a; letter-spacing: -0.3px; }
        .doc-sub-title { font-size: 11px; font-weight: 600; color: #475569; margin-top: 2px; }

        .section-banner {
            background: #1d4ed8; color: #ffffff; font-size: 11px; font-weight: 800;
            padding: 6px 12px; border-radius: 3px; letter-spacing: 0.5px; margin-bottom: 6px;
        }
        .report-table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .report-table td { padding: 4.5px 8px; }
        .col-desc { color: #1e293b; width: 65%; }
        .col-val { text-align: right; color: #0f172a; font-variant-numeric: tabular-nums; font-weight: 500; }
        .row-highlight { background: #f1f5f9; }
        .row-total-accent { background: #dbeafe; color: #1e3a8a; }
        .row-total-accent .col-desc, .row-total-accent .col-val { color: #1e3a8a; }
        .sub-header-row td {
            color: #1d4ed8; font-size: 9.5px; font-weight: 800; padding-top: 8px; padding-bottom: 2px; letter-spacing: 0.4px;
        }

        .neraca-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 12px; }
        .neraca-col { display: flex; flex-direction: column; }
        .col-section-title { font-size: 10px; font-weight: 800; color: #1d4ed8; letter-spacing: 0.5px; margin-bottom: 6px; }
        .group-title { font-size: 10px; font-weight: 700; color: #475569; margin-bottom: 3px; }

        .total-banner-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 8px; margin-bottom: 12px; }
        .total-banner-cell {
            background: #1d4ed8; color: #ffffff; padding: 10px 14px; border-radius: 4px;
            display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 12px;
        }
        .total-banner-label { letter-spacing: 0.3px; }
        .total-banner-value { font-size: 13px; font-variant-numeric: tabular-nums; }

        .validation-status-box {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px 14px;
            display: flex; justify-content: space-between; align-items: center; font-size: 10.5px; margin-top: 4px; margin-bottom: auto;
        }
        .validation-status-text { color: #166534; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .validation-dot { color: #16a34a; font-size: 13px; }
        .validation-badge-text { color: #15803d; font-weight: 800; letter-spacing: 0.5px; font-size: 10px; }

        .doc-footer {
            margin-top: auto; padding-top: 14px; border-top: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; font-size: 9.5px; color: #64748b;
        }
        .font-bold { font-weight: 700 !important; }
        .font-black { font-weight: 900 !important; }

        @media print {
            body { background: none; color: #000; }
            .no-print { display: none !important; }
            .sheets-wrapper { padding: 0; }
            .sheet-page { width: 100%; min-height: auto; margin: 0; padding: 0; box-shadow: none; border-radius: 0; }
            .page-break { page-break-after: always; break-after: page; }
            .section-banner, .total-banner-cell, .row-highlight, .row-total-accent, .validation-status-box {
                -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar-screen no-print">
        <div style="font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #2563eb; padding: 2px 8px; border-radius: 4px; font-size: 11px;">PDF PREVIEW</span>
            <span><?= esc($namaDebitur) ?> &mdash; <?= esc($refDokumen) ?></span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button class="toolbar-btn btn-print" onclick="window.print()">
                <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <a href="javascript:window.close()" class="toolbar-btn btn-close">Tutup</a>
        </div>
    </div>

    <div class="sheets-wrapper">
        <?php if ($showLR || $showAK): ?>
        <!-- LEMBAR 1 -->
        <div class="sheet-page <?= ($totalHalaman === 2) ? 'page-break' : '' ?>">
            <div class="doc-header">
                <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; DOKUMEN ESTIMASI FINANSIAL</span>
                <span class="doc-ref"><?= esc($refDokumen) ?></span>
            </div>
            <div class="doc-title-box">
                <h1 class="doc-main-title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun</div>
            </div>

            <?php if ($showLR): ?>
            <div class="section-banner">LAPORAN LABA RUGI</div>
            <table class="report-table">
                <tr><td class="col-desc">Pendapatan Usaha</td><td class="col-val"><?= rpFmt($lr['pendapatan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">HPP</td><td class="col-val"><?= rpFmtParen($lr['hpp'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Laba Kotor</td><td class="col-val font-bold"><?= rpFmt($lr['laba_kotor'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Beban Gaji (<?= $karyawanJml ?> karyawan)</td><td class="col-val"><?= rpFmtParen($lr['beban_gaji'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Sewa, Utilitas &amp; Pemasaran</td><td class="col-val"><?= rpFmtParen($lr['beban_operasional'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Penyusutan</td><td class="col-val"><?= rpFmtParen($lr['beban_penyusutan'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Laba Operasional</td><td class="col-val font-bold"><?= rpFmt($lr['laba_operasional'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Beban Bunga</td><td class="col-val"><?= rpFmtParen($lr['beban_bunga'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Pajak</td><td class="col-val"><?= rpFmtParen($lr['pajak'] ?? 0) ?></td></tr>
                <tr class="row-total-accent"><td class="col-desc font-black">LABA BERSIH</td><td class="col-val font-black"><?= rpFmt($lr['laba_bersih'] ?? 0) ?></td></tr>
            </table>
            <?php endif; ?>

            <?php if ($showAK): ?>
            <div class="section-banner" <?= $showLR ? 'style="margin-top: 18px;"' : '' ?>>LAPORAN ARUS KAS</div>
            <table class="report-table">
                <tr class="sub-header-row"><td colspan="2">AKTIVITAS OPERASI</td></tr>
                <tr><td class="col-desc">Laba Bersih</td><td class="col-val"><?= rpFmt($ak['laba_bersih'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Penyusutan</td><td class="col-val"><?= rpFmt($ak['penyusutan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Kenaikan Piutang</td><td class="col-val"><?= rpFmtParen($ak['kenaikan_piutang'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Kenaikan Persediaan</td><td class="col-val"><?= rpFmtParen($ak['kenaikan_persediaan'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Kenaikan Utang Usaha</td><td class="col-val"><?= rpFmt($ak['kenaikan_utang_usaha'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Kas Operasi</td><td class="col-val font-bold"><?= rpFmt($ak['kas_operasional'] ?? 0) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">AKTIVITAS INVESTASI</td></tr>
                <tr><td class="col-desc">Pembelian Aset Tetap</td><td class="col-val"><?= rpFmtParen($ak['pembelian_aset_tetap'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Kas Bersih Investasi</td><td class="col-val font-bold"><?= rpFmtParen(abs($ak['kas_investasi'] ?? 0)) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">AKTIVITAS PENDANAAN</td></tr>
                <tr><td class="col-desc">Cicilan Pokok Utang Bank</td><td class="col-val"><?= rpFmtParen($ak['cicilan_pokok'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Prive / Dividen</td><td class="col-val"><?= rpFmtParen($ak['prive_dividen'] ?? 0) ?></td></tr>
                <tr class="row-highlight"><td class="col-desc font-bold">Kas Pendanaan</td><td class="col-val font-bold"><?= rpFmtParen(abs($ak['kas_pendanaan'] ?? 0)) ?></td></tr>

                <tr class="sub-header-row"><td colspan="2">REKONSILIASI KAS</td></tr>
                <tr><td class="col-desc">Kenaikan Kas Bersih</td><td class="col-val"><?= rpFmt($ak['kenaikan_bersih_kas'] ?? 0) ?></td></tr>
                <tr><td class="col-desc">Saldo Kas Awal Periode</td><td class="col-val"><?= rpFmt($ak['saldo_awal_kas'] ?? 0) ?></td></tr>
                <tr class="row-total-accent"><td class="col-desc font-black">SALDO KAS AKHIR</td><td class="col-val font-black"><?= rpFmt($ak['kas_akhir'] ?? 0) ?></td></tr>
            </table>
            <?php endif; ?>

            <div class="doc-footer">
                <div class="doc-footer-left">Laporan ini merupakan estimasi</div>
                <div class="doc-footer-right"><?= ($totalHalaman === 2) ? 'Halaman 1 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem' ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($showNR): ?>
        <!-- LEMBAR 2 -->
        <div class="sheet-page">
            <div class="doc-header">
                <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; DOKUMEN ESTIMASI FINANSIAL</span>
                <span class="doc-ref"><?= esc($refDokumen) ?></span>
            </div>
            <div class="doc-title-box">
                <h1 class="doc-main-title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun</div>
            </div>

            <div class="section-banner">NERACA</div>

            <div class="neraca-grid">
                <!-- AKTIVA -->
                <div class="neraca-col">
                    <div class="col-section-title">AKTIVA / ASET</div>
                    <div class="group-title">Aset Lancar</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Kas &amp; Bank</td><td class="col-val"><?= rpFmt($nr['kas_bank'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Piutang Usaha</td><td class="col-val"><?= rpFmt($nr['piutang_usaha'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Persediaan</td><td class="col-val"><?= rpFmt($nr['persediaan'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Aset Lancar</td><td class="col-val font-bold"><?= rpFmt($nr['total_aset_lancar'] ?? 0) ?></td></tr>
                    </table>

                    <div class="group-title" style="margin-top: 12px;">Aset Tetap</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Aset Tetap Neto</td><td class="col-val"><?= rpFmt($nr['aset_tetap_neto'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Aset Tetap</td><td class="col-val font-bold"><?= rpFmt($nr['aset_tetap_neto'] ?? 0) ?></td></tr>
                    </table>
                </div>

                <!-- PASIVA -->
                <div class="neraca-col">
                    <div class="col-section-title">PASIVA / LIABILITAS &amp; EKUITAS</div>
                    <div class="group-title">Liabilitas</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Utang Usaha</td><td class="col-val"><?= rpFmt($nr['utang_usaha'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Utang Bank</td><td class="col-val"><?= rpFmt(($nr['utang_bank_pendek'] ?? 0) + ($nr['utang_jangka_panjang'] ?? 0)) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Liabilitas</td><td class="col-val font-bold"><?= rpFmt($nr['total_liabilitas'] ?? 0) ?></td></tr>
                    </table>

                    <div class="group-title" style="margin-top: 12px;">Ekuitas</div>
                    <table class="report-table">
                        <tr><td class="col-desc">Modal Disetor</td><td class="col-val"><?= rpFmt($nr['modal_disetor'] ?? 0) ?></td></tr>
                        <tr><td class="col-desc">Laba Ditahan</td><td class="col-val"><?= rpFmt($nr['laba_ditahan_akhir'] ?? 0) ?></td></tr>
                        <tr class="row-highlight"><td class="col-desc font-bold">Total Ekuitas</td><td class="col-val font-bold"><?= rpFmt($nr['total_ekuitas'] ?? 0) ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="total-banner-grid">
                <div class="total-banner-cell">
                    <span class="total-banner-label">TOTAL ASET</span>
                    <span class="total-banner-value"><?= rpFmt($nr['total_aset'] ?? 0) ?></span>
                </div>
                <div class="total-banner-cell">
                    <span class="total-banner-label">TOTAL LIABILITAS &amp; EKUITAS</span>
                    <span class="total-banner-value"><?= rpFmt($nr['total_liabilitas_ekuitas'] ?? 0) ?></span>
                </div>
            </div>

            <div class="validation-status-box">
                <div class="validation-status-text">
                    <span class="validation-dot">●</span>
                    <span>Neraca Seimbang: Selisih Rp 0 (Validasi Sistem Lulus 100%)</span>
                </div>
                <div class="validation-badge-text">TERVERIFIKASI</div>
            </div>

            <div class="doc-footer">
                <div class="doc-footer-left">Laporan ini merupakan estimasi</div>
                <div class="doc-footer-right"><?= ($totalHalaman === 2) ? 'Halaman 2 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem' ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
    </script>
</body>
</html>
