<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Pragma: no-cache");
header("Expires: 0");

$namaDebitur = $kredit['nama_debitur'] ?? 'PT Sumber Makmur Sejahtera';
$noApp       = $kredit['nomor_aplikasi'] ?? 'NC-2024-AK8921';
$tahun       = date('Y');

$lr = $hasil['laba_rugi'] ?? [];
$ak = $hasil['arus_kas'] ?? [];
$nr = $hasil['neraca'] ?? [];

function v($val) {
    if ($val === null || $val === '') return 'Rp 0';
    return 'Rp ' . number_format((float)$val, 0, ',', '.');
}
function vParen($val) {
    if ($val === null || $val === '') return '(Rp 0)';
    return '(Rp ' . number_format(abs((float)$val), 0, ',', '.') . ')';
}

echo "\xEF\xBB\xBF"; // UTF-8 BOM
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; font-family: Calibri, Arial, sans-serif; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 11pt; }
        .title-row { font-size: 14pt; font-weight: bold; background-color: #1e3a8a; color: #ffffff; text-align: left; }
        .sub-title { font-size: 10pt; color: #475569; }
        .header-section { background-color: #dbeafe; font-weight: bold; color: #1e40af; }
        .highlight-row { background-color: #eff6ff; font-weight: bold; color: #1d4ed8; }
        .total-row { background-color: #0f172a; color: #ffffff; font-weight: bold; }
        .num-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr><td colspan="4" class="title-row">LAPORAN KEUANGAN ESTIMASI (BADAN USAHA)</td></tr>
        <tr><td colspan="4" class="sub-title">Perusahaan: <strong><?= esc($namaDebitur) ?></strong> | No. Aplikasi: <?= esc($noApp) ?> | Periode: <?= esc($tahun) ?></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
    </table>

    <!-- I. LABA RUGI -->
    <table>
        <tr class="header-section"><td colspan="2">I. LAPORAN LABA RUGI (INCOME STATEMENT)</td></tr>
        <tr><td>Pendapatan Usaha</td><td class="num-right"><?= v($lr['pendapatan'] ?? 0) ?></td></tr>
        <tr><td>HPP</td><td class="num-right"><?= vParen($lr['hpp'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Laba Kotor</td><td class="num-right"><?= v($lr['laba_kotor'] ?? 0) ?></td></tr>
        <tr><td>Beban Gaji (Karyawan)</td><td class="num-right"><?= vParen($lr['beban_gaji'] ?? 0) ?></td></tr>
        <tr><td>Sewa, Utilitas &amp; Pemasaran</td><td class="num-right"><?= vParen($lr['beban_operasional'] ?? 0) ?></td></tr>
        <tr><td>Penyusutan</td><td class="num-right"><?= vParen($lr['beban_penyusutan'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Laba Operasional</td><td class="num-right"><?= v($lr['laba_operasional'] ?? 0) ?></td></tr>
        <tr><td>Beban Bunga</td><td class="num-right"><?= vParen($lr['beban_bunga'] ?? 0) ?></td></tr>
        <tr><td>Pajak</td><td class="num-right"><?= vParen($lr['pajak'] ?? 0) ?></td></tr>
        <tr class="total-row"><td>LABA BERSIH</td><td class="num-right"><?= v($lr['laba_bersih'] ?? 0) ?></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
    </table>

    <!-- II. ARUS KAS -->
    <table>
        <tr class="header-section"><td colspan="2">II. LAPORAN ARUS KAS (CASH FLOW STATEMENT)</td></tr>
        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Operasi</td></tr>
        <tr><td>Laba Bersih</td><td class="num-right"><?= v($ak['laba_bersih'] ?? 0) ?></td></tr>
        <tr><td>Penyusutan</td><td class="num-right"><?= v($ak['penyusutan'] ?? 0) ?></td></tr>
        <tr><td>Kenaikan Piutang</td><td class="num-right"><?= vParen($ak['kenaikan_piutang'] ?? 0) ?></td></tr>
        <tr><td>Kenaikan Persediaan</td><td class="num-right"><?= vParen($ak['kenaikan_persediaan'] ?? 0) ?></td></tr>
        <tr><td>Kenaikan Utang Usaha</td><td class="num-right"><?= v($ak['kenaikan_utang_usaha'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Operasi</td><td class="num-right"><?= v($ak['kas_operasional'] ?? 0) ?></td></tr>

        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Investasi</td></tr>
        <tr><td>Pembelian Aset Tetap</td><td class="num-right"><?= vParen($ak['pembelian_aset_tetap'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Bersih Investasi</td><td class="num-right"><?= vParen(abs($ak['kas_investasi'] ?? 0)) ?></td></tr>

        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Pendanaan</td></tr>
        <tr><td>Cicilan Pokok Utang Bank</td><td class="num-right"><?= vParen($ak['cicilan_pokok'] ?? 0) ?></td></tr>
        <tr><td>Prive/Dividen</td><td class="num-right"><?= vParen($ak['prive_dividen'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Pendanaan</td><td class="num-right"><?= vParen(abs($ak['kas_pendanaan'] ?? 0)) ?></td></tr>

        <tr class="highlight-row"><td>Kenaikan Kas Bersih</td><td class="num-right"><?= v($ak['kenaikan_bersih_kas'] ?? 0) ?></td></tr>
        <tr><td>Saldo Kas Awal Periode</td><td class="num-right"><?= v($ak['saldo_awal_kas'] ?? 0) ?></td></tr>
        <tr class="total-row"><td>SALDO KAS AKHIR PERIODE</td><td class="num-right"><?= v($ak['kas_akhir'] ?? 0) ?></td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
    </table>

    <!-- III. NERACA -->
    <table>
        <tr class="header-section"><td colspan="2">ASET</td><td colspan="2">LIABILITAS &amp; EKUITAS</td></tr>
        <tr>
            <td>Kas &amp; Bank</td><td class="num-right"><?= v($nr['kas_bank'] ?? 0) ?></td>
            <td>Utang Usaha</td><td class="num-right"><?= v($nr['utang_usaha'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>Piutang Usaha</td><td class="num-right"><?= v($nr['piutang_usaha'] ?? 0) ?></td>
            <td>Utang Bank</td><td class="num-right"><?= v(($nr['utang_bank_pendek'] ?? 0) + ($nr['utang_jangka_panjang'] ?? 0)) ?></td>
        </tr>
        <tr>
            <td>Persediaan</td><td class="num-right"><?= v($nr['persediaan'] ?? 0) ?></td>
            <td class="highlight-row">Total Liabilitas</td><td class="num-right highlight-row"><?= v($nr['total_liabilitas'] ?? 0) ?></td>
        </tr>
        <tr class="highlight-row">
            <td>Total Aset Lancar</td><td class="num-right"><?= v($nr['total_aset_lancar'] ?? 0) ?></td>
            <td>Modal Disetor</td><td class="num-right"><?= v($nr['modal_disetor'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>Aset Tetap Neto</td><td class="num-right"><?= v($nr['aset_tetap_neto'] ?? 0) ?></td>
            <td>Laba Ditahan</td><td class="num-right"><?= v($nr['laba_ditahan_akhir'] ?? 0) ?></td>
        </tr>
        <tr>
            <td class="total-row">TOTAL ASET</td><td class="num-right total-row"><?= v($nr['total_aset'] ?? 0) ?></td>
            <td class="highlight-row">Total Ekuitas</td><td class="num-right highlight-row"><?= v($nr['total_ekuitas'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td><td>&nbsp;</td>
            <td class="total-row">TOTAL LIABILITAS &amp; EKUITAS</td><td class="num-right total-row"><?= v($nr['total_liabilitas_ekuitas'] ?? 0) ?></td>
        </tr>
    </table>
</body>
</html>
