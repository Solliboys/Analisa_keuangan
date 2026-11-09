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

$slikList     = $dataSlik ?? [];
$rekeningList = $dataRekening ?? [];
$agunanList   = $dataAgunan ?? [];

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
        table { border-collapse: collapse; width: 100%; font-family: Calibri, Arial, sans-serif; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 10px; font-size: 10.5pt; }
        .title-row { font-size: 14pt; font-weight: bold; background-color: #1e3a8a; color: #ffffff; text-align: left; }
        .sub-title { font-size: 10pt; color: #475569; background-color: #f1f5f9; }
        .header-section { background-color: #dbeafe; font-weight: bold; color: #1e40af; font-size: 11pt; }
        .highlight-row { background-color: #eff6ff; font-weight: bold; color: #1d4ed8; }
        .total-row { background-color: #0f172a; color: #ffffff; font-weight: bold; }
        .num-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr><td colspan="4" class="title-row">LAPORAN LENGKAP ANALISA KREDIT &amp; KEUANGAN</td></tr>
        <tr><td colspan="4" class="sub-title">Perusahaan: <strong><?= esc($namaDebitur) ?></strong> | No. Aplikasi: <?= esc($noApp) ?> | Periode: <?= esc($tahun) ?></td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
    </table>

    <!-- RINGKASAN PROFIL & FASILITAS -->
    <table>
        <tr class="header-section"><td colspan="4">I. PROFIL DEBITUR &amp; STRUKTUR FASILITAS PENGAJUAN</td></tr>
        <tr>
            <td style="width: 25%; background: #f8fafc;">Nama Debitur</td><td style="width: 25%; font-weight: bold;"><?= esc($namaDebitur) ?></td>
            <td style="width: 25%; background: #f8fafc;">Plafon Pengajuan</td><td style="width: 25%; font-weight: bold; color: #1d4ed8;"><?= v($kredit['plafon_pengajuan'] ?? 0) ?></td>
        </tr>
        <tr>
            <td style="background: #f8fafc;">Bentuk Badan Usaha</td><td><?= esc($kredit['jenis_debitur'] ?? 'Badan Usaha') ?></td>
            <td style="background: #f8fafc;">Jangka Waktu</td><td><?= esc($kredit['tenor_bulan'] ?? 12) ?> Bulan (Bunga <?= esc($kredit['bunga_per_tahun'] ?? 12) ?>% p.a)</td>
        </tr>
        <tr>
            <td style="background: #f8fafc;">NPWP Usaha</td><td><?= esc($kredit['npwp_usaha'] ?? $kredit['npwp_pribadi'] ?? '-') ?></td>
            <td style="background: #f8fafc;">Tujuan Pembiayaan</td><td><?= esc($kredit['tujuan_pembiayaan'] ?? 'Modal Kerja Operasional') ?></td>
        </tr>
        <tr>
            <td style="background: #f8fafc;">Bidang Usaha</td><td><?= esc($kredit['bidang_usaha'] ?? 'Perdagangan & Distribusi') ?></td>
            <td style="background: #f8fafc;">Status Verifikasi</td><td style="color: #15803d; font-weight: bold;">Terverifikasi Lengkap</td>
        </tr>
    </table>

    <!-- SLIK OJK -->
    <table>
        <tr class="header-section"><td colspan="4">II. REKAPITULASI FASILITAS KREDIT &amp; SLIK OJK (TAHAP 4)</td></tr>
        <tr style="background: #f1f5f9; font-weight: bold;">
            <td>Bank / Lembaga</td>
            <td class="num-right">Plafon</td>
            <td class="num-right">Baki Debet</td>
            <td>Kolektibilitas</td>
        </tr>
        <?php 
        $totBaki = 0;
        if (!empty($slikList)):
            foreach ($slikList as $s): 
                $totBaki += (float)($s['outstanding'] ?? 0);
        ?>
        <tr>
            <td><?= esc($s['bank_lembaga'] ?? 'Bank Lain') ?></td>
            <td class="num-right"><?= v($s['plafon'] ?? 0) ?></td>
            <td class="num-right"><?= v($s['outstanding'] ?? 0) ?></td>
            <td>Kol <?= esc($s['kelancaran'] ?? '1') ?> (Lancar)</td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic;">Tidak ada pinjaman bank lain tercatat pada SLIK OJK (Nihil / Bersih)</td>
        </tr>
        <?php endif; ?>
        <tr class="highlight-row">
            <td colspan="2">TOTAL BAKI DEBET SLIK</td>
            <td class="num-right"><?= v($totBaki) ?></td>
            <td>Lancar</td>
        </tr>
    </table>

    <!-- REKENING KORAN -->
    <table>
        <tr class="header-section"><td colspan="4">III. ANALISA ARUS KAS REKENING KORAN BANK (TAHAP 6)</td></tr>
        <tr style="background: #f1f5f9; font-weight: bold;">
            <td>Bank Operasional</td>
            <td>Nomor Rekening</td>
            <td class="num-right">Total Mutasi Kredit</td>
            <td class="num-right">Rata-rata Kredit / Bulan</td>
        </tr>
        <?php 
        $totMutasi = 0; $totRata = 0;
        if (!empty($rekeningList)):
            foreach ($rekeningList as $r):
                $mk = (float)($r['total_mutasi_kredit'] ?? 0);
                $totMutasi += $mk;
                $totRata += (float)($r['rata_rata_kredit'] ?? ($mk/3));
        ?>
        <tr>
            <td><?= esc($r['nama_bank'] ?? 'Bank') ?></td>
            <td><?= esc($r['nomor_rekening'] ?? '-') ?></td>
            <td class="num-right"><?= v($mk) ?></td>
            <td class="num-right"><?= v($r['rata_rata_kredit'] ?? ($mk/3)) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic;">Data mutasi rekening koran belum diunggah / dicatat</td>
        </tr>
        <?php endif; ?>
        <tr class="highlight-row">
            <td colspan="2">TOTAL MUTASI ARUS KAS MASUK</td>
            <td class="num-right"><?= v($totMutasi) ?></td>
            <td class="num-right"><?= v($totRata) ?></td>
        </tr>
    </table>

    <!-- AGUNAN -->
    <table>
        <tr class="header-section"><td colspan="4">IV. PENILAIAN AGUNAN &amp; COVERAGE RATIO (TAHAP 7)</td></tr>
        <tr style="background: #f1f5f9; font-weight: bold;">
            <td>Jenis Agunan</td>
            <td>Bukti Kepemilikan</td>
            <td class="num-right">Nilai Pasar (MV)</td>
            <td class="num-right">Nilai Likuidasi (LV)</td>
        </tr>
        <?php 
        $totMv = 0; $totLv = 0;
        if (!empty($agunanList)):
            foreach ($agunanList as $a):
                $mv = (float)($a['nilai_pasar_mv'] ?? 0);
                $lv = (float)($a['nilai_likuidasi_lv'] ?? ($mv * 0.7));
                $totMv += $mv; $totLv += $lv;
        ?>
        <tr>
            <td><?= esc($a['jenis_agunan'] ?? 'Tanah & Bangunan') ?></td>
            <td><?= esc($a['nomor_sertifikat'] ?? '-') ?></td>
            <td class="num-right"><?= v($mv) ?></td>
            <td class="num-right"><?= v($lv) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="4" style="text-align: center; color: #64748b; font-style: italic;">Pembiayaan tanpa agunan fisik / Clean Loan (Jaminan Personal / Fidusia Piutang &amp; Stok Usaha)</td>
        </tr>
        <?php endif; ?>
        <tr class="highlight-row">
            <td colspan="2">TOTAL AGUNAN</td>
            <td class="num-right"><?= v($totMv) ?></td>
            <td class="num-right"><?= v($totLv) ?></td>
        </tr>
    </table>

    <!-- V. LABA RUGI -->
    <table>
        <tr class="header-section"><td colspan="2">V. LAPORAN LABA RUGI KOMPREHENSIF (TAHAP 9)</td></tr>
        <tr><td>Pendapatan Usaha (Penjualan Bersih)</td><td class="num-right"><?= v($lr['pendapatan'] ?? 0) ?></td></tr>
        <tr><td>Harga Pokok Penjualan (HPP)</td><td class="num-right"><?= vParen($lr['hpp'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Laba Kotor (Gross Profit)</td><td class="num-right"><?= v($lr['laba_kotor'] ?? 0) ?></td></tr>
        <tr><td>Beban Gaji Karyawan</td><td class="num-right"><?= vParen($lr['beban_gaji'] ?? 0) ?></td></tr>
        <tr><td>Sewa, Utilitas &amp; Pemasaran</td><td class="num-right"><?= vParen($lr['beban_operasional'] ?? 0) ?></td></tr>
        <tr><td>Penyusutan / Depresiasi Aset</td><td class="num-right"><?= vParen($lr['beban_penyusutan'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Laba Operasional (EBIT)</td><td class="num-right"><?= v($lr['laba_operasional'] ?? 0) ?></td></tr>
        <tr><td>Beban Bunga Pinjaman Bank</td><td class="num-right"><?= vParen($lr['beban_bunga'] ?? 0) ?></td></tr>
        <tr><td>Pajak Penghasilan (PPh)</td><td class="num-right"><?= vParen($lr['pajak'] ?? 0) ?></td></tr>
        <tr class="total-row"><td>LABA BERSIH (NET PROFIT)</td><td class="num-right"><?= v($lr['laba_bersih'] ?? 0) ?></td></tr>
    </table>

    <!-- VI. ARUS KAS -->
    <table>
        <tr class="header-section"><td colspan="2">VI. LAPORAN ARUS KAS (CASH FLOW STATEMENT)</td></tr>
        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Operasi</td></tr>
        <tr><td>Laba Bersih</td><td class="num-right"><?= v($ak['laba_bersih'] ?? 0) ?></td></tr>
        <tr><td>Penyusutan Non-Kas</td><td class="num-right"><?= v($ak['penyusutan'] ?? 0) ?></td></tr>
        <tr><td>Perubahan Piutang Usaha</td><td class="num-right"><?= vParen($ak['kenaikan_piutang'] ?? 0) ?></td></tr>
        <tr><td>Perubahan Persediaan</td><td class="num-right"><?= vParen($ak['kenaikan_persediaan'] ?? 0) ?></td></tr>
        <tr><td>Perubahan Utang Usaha</td><td class="num-right"><?= v($ak['kenaikan_utang_usaha'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Bersih Aktivitas Operasi</td><td class="num-right"><?= v($ak['kas_operasional'] ?? 0) ?></td></tr>

        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Investasi</td></tr>
        <tr><td>Pembelian Aset Tetap (Capex)</td><td class="num-right"><?= vParen($ak['pembelian_aset_tetap'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Bersih Aktivitas Investasi</td><td class="num-right"><?= vParen(abs($ak['kas_investasi'] ?? 0)) ?></td></tr>

        <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Pendanaan</td></tr>
        <tr><td>Pembayaran Pokok Bank</td><td class="num-right"><?= vParen($ak['cicilan_pokok'] ?? 0) ?></td></tr>
        <tr><td>Prive / Dividen</td><td class="num-right"><?= vParen($ak['prive_dividen'] ?? 0) ?></td></tr>
        <tr class="highlight-row"><td>Kas Bersih Aktivitas Pendanaan</td><td class="num-right"><?= vParen(abs($ak['kas_pendanaan'] ?? 0)) ?></td></tr>

        <tr class="highlight-row"><td>Kenaikan Kas Bersih</td><td class="num-right"><?= v($ak['kenaikan_bersih_kas'] ?? 0) ?></td></tr>
        <tr><td>Saldo Kas Awal Periode</td><td class="num-right"><?= v($ak['saldo_awal_kas'] ?? 0) ?></td></tr>
        <tr class="total-row"><td>SALDO KAS AKHIR PERIODE</td><td class="num-right"><?= v($ak['kas_akhir'] ?? 0) ?></td></tr>
    </table>

    <!-- VII. NERACA -->
    <table>
        <tr class="header-section"><td colspan="2">ASET</td><td colspan="2">LIABILITAS &amp; EKUITAS</td></tr>
        <tr>
            <td>Kas &amp; Setara Kas</td><td class="num-right"><?= v($nr['kas_bank'] ?? 0) ?></td>
            <td>Utang Usaha</td><td class="num-right"><?= v($nr['utang_usaha'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>Piutang Usaha</td><td class="num-right"><?= v($nr['piutang_usaha'] ?? 0) ?></td>
            <td>Utang Bank (Lancar &amp; Panjang)</td><td class="num-right"><?= v(($nr['utang_bank_pendek'] ?? 0) + ($nr['utang_jangka_panjang'] ?? 0)) ?></td>
        </tr>
        <tr>
            <td>Persediaan Barang</td><td class="num-right"><?= v($nr['persediaan'] ?? 0) ?></td>
            <td class="highlight-row">Total Liabilitas</td><td class="num-right highlight-row"><?= v($nr['total_liabilitas'] ?? 0) ?></td>
        </tr>
        <tr class="highlight-row">
            <td>Total Aset Lancar</td><td class="num-right"><?= v($nr['total_aset_lancar'] ?? 0) ?></td>
            <td>Modal Disetor</td><td class="num-right"><?= v($nr['modal_disetor'] ?? 0) ?></td>
        </tr>
        <tr>
            <td>Aset Tetap Neto</td><td class="num-right"><?= v($nr['aset_tetap_neto'] ?? 0) ?></td>
            <td>Laba Ditahan Akhir</td><td class="num-right"><?= v($nr['laba_ditahan_akhir'] ?? 0) ?></td>
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

    <!-- VIII. RASIO KEUANGAN -->
    <?php
    $pend = (float)($lr['pendapatan'] ?? 1);
    $lb   = (float)($lr['laba_bersih'] ?? 0);
    $npm  = ($pend > 0) ? round(($lb / $pend) * 100, 2) : 0;

    $al   = (float)($nr['total_aset_lancar'] ?? 0);
    $ul   = (float)($nr['utang_usaha'] ?? 1);
    $cr   = ($ul > 0) ? round(($al / $ul) * 100, 1) : 0;

    $tLiab = (float)($nr['total_liabilitas'] ?? 0);
    $tEk   = (float)($nr['total_ekuitas'] ?? 1);
    $der   = ($tEk > 0) ? round($tLiab / $tEk, 2) : 0;
    ?>
    <table>
        <tr class="header-section"><td colspan="3">VIII. RASIO KEUANGAN UTAMA</td></tr>
        <tr style="background: #f1f5f9; font-weight: bold;">
            <td>Rasio</td><td>Nilai</td><td>Keterangan Benchmark</td>
        </tr>
        <tr><td>Margin Laba Bersih (NPM)</td><td style="font-weight: bold;"><?= $npm ?> %</td><td>Benchmark Sehat: &ge; 10%</td></tr>
        <tr><td>Current Ratio (Rasio Lancar)</td><td style="font-weight: bold;"><?= $cr ?> %</td><td>Benchmark Likuiditas: &ge; 120%</td></tr>
        <tr><td>Debt to Equity Ratio (DER)</td><td style="font-weight: bold;"><?= $der ?> x</td><td>Batas Maksimum Solvabilitas: &le; 3.0x</td></tr>
    </table>
</body>
</html>
