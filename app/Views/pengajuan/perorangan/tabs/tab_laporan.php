<?php
/**
 * Tab 10: Laporan Keuangan Debitur Perorangan
 * Sesuai Standar Analisa Perorangan:
 * 1. Tipe Usaha:
 *    - Laba Rugi Sederhana (Penjualan Hari Ramai, Biasa, Sepi, HPP, Laba Kotor, Biaya Operasional, Laba Usaha, Pendapatan Lain, Prive RT, Sisa Penghasilan).
 *    - Daftar Harta & Utang (Aset Usaha, Aset Pribadi, Utang Dagang/Bank/Koperasi/Pinjol, Kekayaan Bersih).
 * 2. Tipe Berpenghasilan Tetap (Karyawan, PNS, Pensiunan):
 *    - Laporan Penghasilan & Pengeluaran (Gaji Pokok, Tunjangan, Tambahan, Pasangan, Biaya Hidup, Pendidikan, Lain-lain, Cicilan, Sisa Penghasilan - TANPA omzet/HPP/operasional).
 *    - Daftar Harta & Utang (Tabungan, Tanah, Bangunan, Kendaraan, Utang Bank/Koperasi/Pinjol - TANPA aset usaha).
 */

// 1. Deteksi Otomatis Kategori Debitur (Trigger Utama: Centang HPP di Tab 9)
// Jika HPP TIDAK aktif (default 0%), maka otomatis masuk ke Laporan Gaji (Karyawan/PNS/Pensiun).
// Jika HPP aktif (karena dicentang di Tab 9), maka masuk ke Laporan Usaha (Wiraswasta/Pedagang).
$nomHppLama = (float)($keuangan['hpp_nominal_rk'] ?? 0);
$pctHppLama = (float)($keuangan['hpp_persen_rk'] ?? 0);
$adaHppAktif = ($nomHppLama > 0 || $pctHppLama > 0);
$isKaryawanPns = !$adaHppAktif;

// 2. Data Keuangan Punya Usaha (dari Tab 9 & Database)
$omsetBulanan = (float)($keuangan['pendapatan_rk'] ?? 0);
$hppPersen = $adaHppAktif ? $pctHppLama : 0;
$hppNominal = $adaHppAktif ? ($nomHppLama > 0 ? $nomHppLama : ($omsetBulanan * ($hppPersen / 100))) : 0;
$labaKotor = max(0, $omsetBulanan - $hppNominal);

// Estimasi Skenario Hari Ramai, Biasa, Sepi (Omzet = Ramai + Biasa + Sepi)
$omzetRamai = round($omsetBulanan * 0.45); // 45% omzet dari hari ramai
$omzetBiasa = round($omsetBulanan * 0.40); // 40% omzet dari hari biasa
$omzetSepi  = $omsetBulanan - $omzetRamai - $omzetBiasa; // 15% omzet dari hari sepi

// Biaya Operasional Usaha
$nomBiayaOperasional = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
$labaUsaha = max(0, $labaKotor - $nomBiayaOperasional);

// Pendapatan Lain (Luar usaha)
$isAdaBuktiLain = !empty($keuangan['ada_bukti_pendapatan_lain_rk']) || (!empty($keuangan['pendapatan_lain_rk']) && (float)$keuangan['pendapatan_lain_rk'] > 0);
$nomPendapatanLain = $isAdaBuktiLain ? (float)($keuangan['pendapatan_lain_rk'] ?? 0) : 0;

// Prive / Pengeluaran Rumah Tangga (Biaya Hidup Keluarga)
$nomPengeluaranRT = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);

// Sisa Penghasilan Usaha = (Laba Usaha + Pendapatan Lain) - Prive RT
$labaBersihSisaPenghasilanUsaha = ($labaUsaha + $nomPendapatanLain) - $nomPengeluaranRT;

// Cicilan Utang Berjalan
$totalCicilanSlik = 0;
if (!empty($slik) && is_array($slik)) {
    foreach ($slik as $s) {
        $totalCicilanSlik += (float)($s['angsuran'] ?? ($s['outstanding'] ? round($s['outstanding'] / 24) : 0));
    }
}
if ($totalCicilanSlik <= 0) {
    $totalCicilanSlik = (float)($keuangan['angsuran_dipilih'] ?? ($keuangan['angsuran_installment'] ?? 2000000));
}
$sisaKasBebasUsaha = $labaBersihSisaPenghasilanUsaha - $totalCicilanSlik;
$dsrUsaha = ($omsetBulanan > 0) ? round(($totalCicilanSlik / ($omsetBulanan + $nomPendapatanLain)) * 100, 1) : 0;

// Harta & Utang Tipe Usaha
$kasUsaha = round($omsetBulanan * 0.15);
$piutangUsaha = round($omsetBulanan * 0.10);
$persediaanStok = round($hppNominal * 0.35);
$peralatanUsaha = round($omsetBulanan * 0.12);
$totalAsetUsaha = $kasUsaha + $piutangUsaha + $persediaanStok + $peralatanUsaha;

// Hitung Agunan Riil dari Tab 4 (Tanah, Bangunan, Kendaraan, Deposito)
$tanahPribadi = 0;
$bangunanPribadi = 0;
$kendaraanPribadi = 0;
$depositoAgunan = 0;

if (!empty($agunan) && is_array($agunan)) {
    foreach ($agunan as $ag) {
        $jenisAg = strtolower($ag['jenis_agunan'] ?? '');
        $mv = (float)($ag['nilai_pasar_mv'] ?? 0);
        if ($mv <= 0) continue;

        if (strpos($jenisAg, 'deposito') !== false) {
            $depositoAgunan += $mv;
        } elseif (strpos($jenisAg, 'mobil') !== false || strpos($jenisAg, 'motor') !== false || strpos($jenisAg, 'kendaraan') !== false || strpos($jenisAg, 'bpkb') !== false) {
            $kendaraanPribadi += $mv;
        } else {
            // Properti: SHM / SHGB / Sarusun / Tanah / Bangunan
            $lt = (float)($ag['luas_tanah_m2'] ?? 0);
            $lb = (float)($ag['luas_bangunan_m2'] ?? 0);
            if ($lb > 0 && $lt > 0) {
                $tanahPribadi += round($mv * 0.6);
                $bangunanPribadi += round($mv * 0.4);
            } elseif ($lb > 0) {
                $bangunanPribadi += $mv;
            } else {
                $tanahPribadi += $mv;
            }
        }
    }
}

// Hitung Tabungan / Deposito Bank dari Tab 8 (Rekening Koran) + Deposito Tab 4
$tabunganBank = 0;
if (!empty($rekening) && is_array($rekening)) {
    foreach ($rekening as $rek) {
        $sAwal = (float)($rek['saldo_awal'] ?? 0);
        $mutK = (float)($rek['total_mutasi_kredit'] ?? 0);
        $mutD = (float)($rek['total_mutasi_debit'] ?? 0);
        $sAkhir = $sAwal + $mutK - $mutD;
        if ($sAkhir > 0) {
            $tabunganBank += $sAkhir;
        } elseif ($sAwal > 0) {
            $tabunganBank += $sAwal;
        } elseif (!empty($rek['rata_rata_kredit']) && (float)$rek['rata_rata_kredit'] > 0) {
            $tabunganBank += round((float)$rek['rata_rata_kredit'] * 0.5);
        }
    }
}
$tabunganPribadi = $tabunganBank + $depositoAgunan;
$totalAsetPribadi = $tabunganPribadi + $tanahPribadi + $bangunanPribadi + $kendaraanPribadi;
$totalHartaUsaha = $totalAsetUsaha + $totalAsetPribadi;

// Hitung Utang Riil dari Tab 6 (SLIK)
$utangBank = 0;
$utangKoperasi = 0;
$utangPinjol = 0;

if (!empty($slik) && is_array($slik)) {
    foreach ($slik as $s) {
        $out = (float)($s['outstanding'] ?? 0);
        if ($out <= 0 && !empty($s['angsuran']) && (float)$s['angsuran'] > 0) {
            $out = round((float)$s['angsuran'] * 18);
        }
        if ($out <= 0) continue;

        $lembaga = strtolower(($s['bank_lembaga'] ?? '') . ' ' . ($s['jenis_fasilitas'] ?? '') . ' ' . ($s['keterangan'] ?? ''));
        if (strpos($lembaga, 'koperasi') !== false || strpos($lembaga, 'ksp') !== false) {
            $utangKoperasi += $out;
        } elseif (strpos($lembaga, 'pinjol') !== false || strpos($lembaga, 'fintech') !== false || strpos($lembaga, 'paylater') !== false || strpos($lembaga, 'p2p') !== false) {
            $utangPinjol += $out;
        } else {
            $utangBank += $out;
        }
    }
}

// Fallback jika ada cicilan berjalan tapi belum ada detail outstanding di SLIK
if ($utangBank <= 0 && $totalCicilanSlik > 0) {
    $utangBank = $totalCicilanSlik * 18;
}

$utangDagang = round($hppNominal * 0.15);
$totalUtangUsaha = $utangDagang + $utangBank + $utangKoperasi + $utangPinjol;
$kekayaanBersihUsaha = $totalHartaUsaha - $totalUtangUsaha;

// 3. Data Keuangan Punya Gaji (Karyawan, PNS, Pensiunan) - Diambil Langsung Dinamis dari Tab 9
$rawDetailPendapatan = $keuangan['detail_pendapatan_rk'] ?? '';
$listDetailPendapatan = [];
if (!empty($rawDetailPendapatan)) {
    $decP = json_decode($rawDetailPendapatan, true);
    if (is_array($decP) && count($decP) > 0) {
        $listDetailPendapatan = $decP;
    }
}

$gajiPokok = 0;
$namaGajiPokok = 'Gaji Pokok / Penghasilan Utama';

if (!empty($listDetailPendapatan)) {
    $gajiPokok = (float)($listDetailPendapatan[0]['nominal'] ?? 0);
    if (!empty($listDetailPendapatan[0]['nama'])) {
        $namaGajiPokok = $listDetailPendapatan[0]['nama'];
    }
} else {
    $gajiPokok = (float)($keuangan['pendapatan_rk'] ?? 0);
}

$penghasilanLain = $nomPendapatanLain;
$totalPenghasilanGaji = $gajiPokok + $penghasilanLain;

$biayaHidupGaji = $nomPengeluaranRT;
$biayaPendidikanGaji = 0;
$biayaLainGaji = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);

$rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
$listDetailBeban = [];
if (!empty($rawDetailBeban)) {
    $decBeban = json_decode($rawDetailBeban, true);
    if (is_array($decBeban)) {
        foreach ($decBeban as $bItem) {
            $bNom = (float)($bItem['nominal'] ?? 0);
            if ($bNom > 0) {
                $listDetailBeban[] = [
                    'nama' => !empty($bItem['nama']) ? $bItem['nama'] : 'Biaya & Beban Lainnya',
                    'nominal' => $bNom,
                ];
            }
        }
    }
}
if (empty($listDetailBeban) && $biayaLainGaji > 0) {
    $listDetailBeban[] = [
        'nama' => 'Biaya & Beban Bebas Lainnya',
        'nominal' => $biayaLainGaji,
    ];
}

$cicilanBerjalanGaji = $totalCicilanSlik;
$totalPengeluaranGaji = $biayaHidupGaji + $biayaPendidikanGaji + $biayaLainGaji + $cicilanBerjalanGaji;

$sisaPenghasilanGaji = $totalPenghasilanGaji - $totalPengeluaranGaji;
$dsrGaji = ($totalPenghasilanGaji > 0) ? round(($cicilanBerjalanGaji / $totalPenghasilanGaji) * 100, 1) : 0;

// Harta & Utang Tipe Gaji (TANPA ASET USAHA - MURNI DINAMIS)
$tabunganGaji = $tabunganPribadi;
$tanahGaji = $tanahPribadi;
$bangunanGaji = $bangunanPribadi;
$kendaraanGaji = $kendaraanPribadi;
$totalHartaGaji = $tabunganGaji + $tanahGaji + $bangunanGaji + $kendaraanGaji;

$utangBankGaji = $utangBank;
$utangKoperasiGaji = $utangKoperasi;
$utangPinjolGaji = $utangPinjol;
$totalUtangGaji = $utangBankGaji + $utangKoperasiGaji + $utangPinjolGaji;
$kekayaanBersihGaji = $totalHartaGaji - $totalUtangGaji;

// Info Header
$namaDebiturDisplay = !empty($kredit['nama_debitur']) ? esc($kredit['nama_debitur']) : 'Calon Debitur Perorangan';
$noAppDisplay = !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-2024-AK8921';
$tahunPeriode = date('Y');
?>

<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    
    <!-- =========================================================================
         1. SECTION HEADER & TOMBOL AKSI
    ========================================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                10
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">LAPORAN KEUANGAN PERORANGAN</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Perorangan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Per Bulan</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="sinkronkanLaporanKeuangan()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-2xs cursor-pointer" title="Perbarui angka dari Tab 9">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Sinkron Data</span>
            </button>
            <button type="button" onclick="bukaModalPreviewLaporan()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Unduh / Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- =========================================================================
         2. TIPE SUMBER PENGHASILAN DEBITUR PERORANGAN (DISESUAIKAN OTOMATIS)
    ========================================================================== -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50/90 via-indigo-50/70 to-slate-50 border border-blue-200/80 mb-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] font-extrabold text-blue-900 uppercase tracking-wider block">TIPE SUMBER PENGHASILAN DEBITUR PERORANGAN:</span>
                <p id="descTipeDebitur" class="text-[11px] text-slate-600">
                    <?= $isKaryawanPns ? 'Struktur laporan disesuaikan khusus untuk debitur <strong>berpenghasilan tetap (Karyawan/PNS/Pensiun)</strong>.' : 'Struktur laporan disesuaikan khusus untuk debitur dengan <strong>kegiatan usaha (Wiraswasta/Toko)</strong>.' ?>
                </p>
            </div>

            <!-- Tipe Debitur Aktif (Eksklusif: hanya muncul yang sesuai) -->
            <div class="inline-flex p-1 bg-white border border-slate-200 rounded-xl shadow-2xs shrink-0" role="group">
                <div id="btnKategoriUsaha" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-blue-600 text-white shadow-xs <?= $isKaryawanPns ? 'hidden' : '' ?>">
                    <span>💼 Punya Usaha (Wiraswasta)</span>
                </div>
                <div id="btnKategoriGaji" class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-blue-600 text-white shadow-xs <?= !$isKaryawanPns ? 'hidden' : '' ?>">
                    <span>👔 Punya Gaji (Karyawan/PNS/Pensiun)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         BAGIAN A: LAPORAN KEUANGAN PERORANGAN YANG PUNYA USAHA
    ========================================================================== -->
    <div id="sectionPeroranganUsaha" class="<?= $isKaryawanPns ? 'hidden' : '' ?> space-y-6">
        
        <!-- Highlight Metrics Tipe Usaha -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL PENJUALAN / OMZET</span>
                <span id="metric_usaha_omzet" class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block mt-1">
                    Rp <?= number_format($omsetBulanan, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Gabungan hari ramai, biasa, dan sepi</span>
            </div>

            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">LABA USAHA (OPERASIONAL)</span>
                <span id="metric_usaha_laba_usaha" class="text-xl sm:text-2xl font-black text-blue-700 tracking-tight block mt-1">
                    Rp <?= number_format($labaUsaha, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Laba kotor dikurangi biaya operasional</span>
            </div>

            <div class="p-4 bg-blue-600 text-white rounded-2xl flex flex-col justify-between shadow-xs">
                <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">SISA PENGHASILAN (BERSIH)</span>
                <span id="metric_usaha_sisa" class="text-xl sm:text-2xl font-black text-white tracking-tight block mt-1">
                    Rp <?= number_format($labaBersihSisaPenghasilanUsaha, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-blue-100 mt-1 block">Dana yang bisa dipakai untuk membayar angsuran</span>
            </div>
        </div>

        <!-- 1. LABA RUGI SEDERHANA PER BULAN (TIPE USAHA) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wide">1. LABA RUGI SEDERHANA PER BULAN</h4>
                    <p class="text-[11px] text-slate-500">Penjualan dipecah per hari ramai, biasa, dan sepi &bull; Periode Bulanan <?= $tahunPeriode ?></p>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    Format Usaha Perorangan
                </span>
            </div>

            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-blue-50/70 border-b border-blue-100 text-blue-900 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-4 w-60">KOMPONEN</th>
                            <th class="py-3 px-4 hidden md:table-cell">KETERANGAN & RINCIAN HITUNGAN</th>
                            <th class="py-3 px-4 text-right">NOMINAL (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <!-- Penjualan dipecah 3 skenario -->
                        <tr class="bg-slate-50/50">
                            <td class="py-2.5 px-4 font-bold text-slate-800">Penjualan / Omzet:</td>
                            <td class="py-2.5 px-4 text-slate-500 text-[11px] hidden md:table-cell">Rincian estimasi hari ramai, biasa, dan sepi:</td>
                            <td class="py-2.5 px-4 text-right font-bold text-slate-800"></td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 pl-8 text-slate-700">&bull; Penjualan Hari Ramai</td>
                            <td class="py-2 px-4 text-slate-500 hidden md:table-cell">Weekend / awal bulan / tanggal gajian</td>
                            <td class="py-2 px-4 text-right font-medium text-slate-800" id="row_usaha_ramai">Rp <?= number_format($omzetRamai, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 pl-8 text-slate-700">&bull; Penjualan Hari Biasa</td>
                            <td class="py-2 px-4 text-slate-500 hidden md:table-cell">Hari kerja reguler (Senin - Kamis)</td>
                            <td class="py-2 px-4 text-right font-medium text-slate-800" id="row_usaha_biasa">Rp <?= number_format($omzetBiasa, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 pl-8 text-slate-700">&bull; Penjualan Hari Sepi</td>
                            <td class="py-2 px-4 text-slate-500 hidden md:table-cell">Hari sepi / pasca liburan</td>
                            <td class="py-2 px-4 text-right font-medium text-slate-800" id="row_usaha_sepi">Rp <?= number_format($omzetSepi, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="bg-blue-50/40 font-bold text-blue-900">
                            <td class="py-2.5 px-4">Total Penjualan / Omzet</td>
                            <td class="py-2.5 px-4 text-slate-600 text-[11px] hidden md:table-cell">Total pendapatan usaha per bulan</td>
                            <td class="py-2.5 px-4 text-right font-black text-blue-700" id="row_usaha_tot_omzet">Rp <?= number_format($omsetBulanan, 0, ',', '.') ?></td>
                        </tr>

                        <!-- HPP -->
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-rose-700">(-) HPP</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Biaya membeli atau memproduksi barang dagang (<?= $hppPersen ?>%)</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_usaha_hpp">(Rp <?= number_format($hppNominal, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- Laba Kotor -->
                        <tr class="bg-blue-50/30 font-bold text-blue-950">
                            <td class="py-2.5 px-4 font-extrabold text-blue-900">= Laba kotor</td>
                            <td class="py-2.5 px-4 text-slate-600 hidden md:table-cell">Penjualan dikurangi HPP</td>
                            <td class="py-2.5 px-4 text-right font-black text-blue-700" id="row_usaha_laba_kotor">Rp <?= number_format($labaKotor, 0, ',', '.') ?></td>
                        </tr>

                        <!-- Biaya Operasional -->
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-rose-700">(-) Biaya operasional</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Sewa tempat, listrik, gaji karyawan, transport, retribusi</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_usaha_operasional">(Rp <?= number_format($nomBiayaOperasional, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- Laba Usaha -->
                        <tr class="bg-slate-100/70 font-bold text-slate-900">
                            <td class="py-2.5 px-4 font-extrabold text-slate-800">= Laba usaha</td>
                            <td class="py-2.5 px-4 text-slate-600 hidden md:table-cell">Keuntungan murni dari kegiatan usaha pokok</td>
                            <td class="py-2.5 px-4 text-right font-black text-slate-900" id="row_usaha_laba_usaha">Rp <?= number_format($labaUsaha, 0, ',', '.') ?></td>
                        </tr>

                        <!-- Pendapatan Lain -->
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-emerald-700">(+) Pendapatan lain</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Penghasilan di luar usaha (gaji pasangan, sewa, dividen)</td>
                            <td class="py-2.5 px-4 text-right font-medium text-emerald-600" id="row_usaha_pendapatan_lain">Rp <?= number_format($nomPendapatanLain, 0, ',', '.') ?></td>
                        </tr>

                        <!-- Prive RT -->
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-rose-700">(-) Prive (pengeluaran rumah tangga)</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Biaya hidup keluarga debitur (dipisahkan penuh dari kas usaha)</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_usaha_prive">(Rp <?= number_format($nomPengeluaranRT, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- Sisa Penghasilan -->
                        <tr class="bg-emerald-50/80 border-t-2 border-emerald-300">
                            <td class="py-3 px-4 font-black text-emerald-950 uppercase">
                                = Sisa penghasilan
                            </td>
                            <td class="py-3 px-4 text-emerald-800 font-semibold text-[11px] hidden md:table-cell">
                                Dana yang bisa dipakai untuk membayar angsuran
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-800 text-sm sm:text-base" id="row_usaha_sisa_penghasilan">
                                Rp <?= number_format($labaBersihSisaPenghasilanUsaha, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <!-- Pengujian Cicilan Berjalan -->
                        <tr class="bg-slate-50/70 border-t border-slate-200 text-slate-600">
                            <td class="py-2 px-4 pl-8">(-) Cicilan utang berjalan (Bank/SLIK)</td>
                            <td class="py-2 px-4 text-slate-400 text-[11px] hidden md:table-cell">Komitmen angsuran berjalan saat ini</td>
                            <td class="py-2 px-4 text-right font-medium text-rose-600" id="row_usaha_cicilan">(Rp <?= number_format($totalCicilanSlik, 0, ',', '.') ?>)</td>
                        </tr>
                        <tr class="bg-slate-100 font-extrabold text-slate-800">
                            <td class="py-2.5 px-4 pl-8 uppercase text-[11px]">= Sisa Kas Bebas (Buffer Angsuran Baru)</td>
                            <td class="py-2.5 px-4 text-slate-500 text-[11px] hidden md:table-cell">Kapasitas aman untuk angsuran pinjaman baru</td>
                            <td class="py-2.5 px-4 text-right font-black text-blue-700" id="row_usaha_buffer">Rp <?= number_format($sisaKasBebasUsaha, 0, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. DAFTAR HARTA DAN UTANG (TIPE USAHA) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wide">2. DAFTAR HARTA DAN UTANG (USAHA & PRIBADI)</h4>
                    <p class="text-[11px] text-slate-500">Aset Usaha + Aset Pribadi dikurangi Total Utang = <strong>Kekayaan Bersih</strong></p>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Neraca Perorangan Usaha
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <!-- Sisi Kiri: DAFTAR HARTA (ASET USAHA + ASET PRIBADI) -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                    <div class="bg-blue-50/80 px-4 py-3 border-b border-blue-100 flex items-center justify-between">
                        <span class="font-extrabold text-xs text-blue-900 uppercase tracking-wider">A. DAFTAR HARTA (ASET)</span>
                        <span class="text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">Harta</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <tbody class="divide-y divide-slate-100">
                            <!-- Aset Usaha -->
                            <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                                <td colspan="2" class="py-2 px-4 tracking-wider">1. ASET USAHA</td>
                            </tr>
                            <tr><td class="py-2 px-4">&bull; Kas usaha</td><td class="py-2 px-4 text-right font-medium" id="ast_usaha_kas">Rp <?= number_format($kasUsaha, 0, ',', '.') ?></td></tr>
                            <tr><td class="py-2 px-4">&bull; Piutang usaha</td><td class="py-2 px-4 text-right font-medium" id="ast_usaha_piutang">Rp <?= number_format($piutangUsaha, 0, ',', '.') ?></td></tr>
                            <tr><td class="py-2 px-4">&bull; Persediaan (stok barang)</td><td class="py-2 px-4 text-right font-medium" id="ast_usaha_stok">Rp <?= number_format($persediaanStok, 0, ',', '.') ?></td></tr>
                            <tr><td class="py-2 px-4">&bull; Peralatan usaha</td><td class="py-2 px-4 text-right font-medium" id="ast_usaha_alat">Rp <?= number_format($peralatanUsaha, 0, ',', '.') ?></td></tr>
                            <tr class="bg-blue-50/40 font-bold text-blue-900">
                                <td class="py-2 px-4">Subtotal Aset Usaha</td>
                                <td class="py-2 px-4 text-right font-bold" id="ast_usaha_sub">Rp <?= number_format($totalAsetUsaha, 0, ',', '.') ?></td>
                            </tr>

                            <!-- Aset Pribadi -->
                            <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                                <td colspan="2" class="py-2 px-4 tracking-wider">2. ASET PRIBADI</td>
                            </tr>
                            <tr id="row_ast_pribadi_tabungan" class="<?= $tabunganPribadi > 0 ? '' : 'hidden' ?>"><td class="py-2 px-4">&bull; Tabungan pribadi</td><td class="py-2 px-4 text-right font-medium" id="ast_pribadi_tabungan">Rp <?= number_format($tabunganPribadi, 0, ',', '.') ?></td></tr>
                            <tr id="row_ast_pribadi_tanah" class="<?= $tanahPribadi > 0 ? '' : 'hidden' ?>"><td class="py-2 px-4">&bull; Tanah</td><td class="py-2 px-4 text-right font-medium" id="ast_pribadi_tanah">Rp <?= number_format($tanahPribadi, 0, ',', '.') ?></td></tr>
                            <tr id="row_ast_pribadi_bangunan" class="<?= $bangunanPribadi > 0 ? '' : 'hidden' ?>"><td class="py-2 px-4">&bull; Bangunan</td><td class="py-2 px-4 text-right font-medium" id="ast_pribadi_bangunan">Rp <?= number_format($bangunanPribadi, 0, ',', '.') ?></td></tr>
                            <tr id="row_ast_pribadi_kendaraan" class="<?= $kendaraanPribadi > 0 ? '' : 'hidden' ?>"><td class="py-2 px-4">&bull; Kendaraan</td><td class="py-2 px-4 text-right font-medium" id="ast_pribadi_kendaraan">Rp <?= number_format($kendaraanPribadi, 0, ',', '.') ?></td></tr>
                            <tr class="bg-blue-50/40 font-bold text-blue-900">
                                <td class="py-2 px-4">Subtotal Aset Pribadi</td>
                                <td class="py-2 px-4 text-right font-bold" id="ast_pribadi_sub">Rp <?= number_format($totalAsetPribadi, 0, ',', '.') ?></td>
                            </tr>

                            <!-- Total Harta -->
                            <tr class="bg-blue-600 text-white font-extrabold text-sm">
                                <td class="py-3 px-4 uppercase">TOTAL HARTA (ASET)</td>
                                <td class="py-3 px-4 text-right text-base font-black" id="ast_total_harta_usaha">Rp <?= number_format($totalHartaUsaha, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sisi Kanan: DAFTAR UTANG & KEKAYAAN BERSIH -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                    <div class="bg-indigo-50/80 px-4 py-3 border-b border-indigo-100 flex items-center justify-between">
                        <span class="font-extrabold text-xs text-indigo-900 uppercase tracking-wider">B. DAFTAR UTANG & KEKAYAAN BERSIH</span>
                        <span class="text-[10px] font-bold text-indigo-700 bg-white px-2 py-0.5 rounded border border-indigo-200">Kewajiban & Ekuitas</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                                <td colspan="2" class="py-2 px-4 tracking-wider">1. DAFTAR UTANG</td>
                            </tr>
                            <tr id="row_utang_dagang" class="<?= $utangDagang > 0 ? '' : 'hidden' ?>"><td class="py-2.5 px-4">&bull; Utang dagang (supplier)</td><td class="py-2.5 px-4 text-right font-medium" id="utang_dagang">Rp <?= number_format($utangDagang, 0, ',', '.') ?></td></tr>
                            <tr id="row_utang_bank" class="<?= $utangBank > 0 ? '' : 'hidden' ?>"><td class="py-2.5 px-4">&bull; Utang bank (SLIK berjalan)</td><td class="py-2.5 px-4 text-right font-medium" id="utang_bank">Rp <?= number_format($utangBank, 0, ',', '.') ?></td></tr>
                            <tr id="row_utang_koperasi" class="<?= $utangKoperasi > 0 ? '' : 'hidden' ?>"><td class="py-2.5 px-4">&bull; Utang koperasi</td><td class="py-2.5 px-4 text-right font-medium" id="utang_koperasi">Rp <?= number_format($utangKoperasi, 0, ',', '.') ?></td></tr>
                            <tr id="row_utang_pinjol" class="<?= $utangPinjol > 0 ? '' : 'hidden' ?>"><td class="py-2.5 px-4">&bull; Pinjaman online (Pinjol/P2P)</td><td class="py-2.5 px-4 text-right font-medium" id="utang_pinjol">Rp <?= number_format($utangPinjol, 0, ',', '.') ?></td></tr>
                            <tr class="bg-rose-50/50 font-bold text-rose-900">
                                <td class="py-2 px-4">Total Utang</td>
                                <td class="py-2 px-4 text-right font-bold text-rose-700" id="utang_total_usaha">Rp <?= number_format($totalUtangUsaha, 0, ',', '.') ?></td>
                            </tr>

                            <!-- Hasil Kekayaan Bersih -->
                            <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                                <td colspan="2" class="py-2 px-4 tracking-wider">2. HASIL KEKAYAAN BERSIH</td>
                            </tr>
                            <tr class="bg-indigo-50/50">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-indigo-900 block text-xs uppercase">Kekayaan Bersih</span>
                                    <span class="text-[10px] text-slate-500">Total Harta dikurangi Total Utang</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-indigo-700 text-sm sm:text-base" id="kekayaan_bersih_usaha">
                                    Rp <?= number_format($kekayaanBersihUsaha, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <tr class="bg-indigo-900 text-white font-extrabold text-sm">
                                <td class="py-3 px-4 uppercase">TOTAL UTANG + KEKAYAAN BERSIH</td>
                                <td class="py-3 px-4 text-right text-base font-black text-emerald-300" id="pasiva_total_usaha">Rp <?= number_format($totalHartaUsaha, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- =========================================================================
         BAGIAN B: LAPORAN KEUANGAN PERORANGAN YANG PUNYA GAJI (KARYAWAN/PNS/PENSIUN)
         *TIDAK ADA OMZET, HPP, ATAU BIAYA OPERASIONAL USAHA!*
    ========================================================================== -->
    <div id="sectionPeroranganGaji" class="<?= !$isKaryawanPns ? 'hidden' : '' ?> space-y-6">
        
        <!-- Highlight Metrics Tipe Gaji -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL PENGHASILAN BULANAN</span>
                <span id="metric_gaji_total" class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block mt-1">
                    Rp <?= number_format($totalPenghasilanGaji, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Gaji pokok, tunjangan, dan tambahan</span>
            </div>

            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TOTAL PENGELUARAN BULANAN</span>
                <span id="metric_gaji_pengeluaran" class="text-xl sm:text-2xl font-black text-rose-600 tracking-tight block mt-1">
                    Rp <?= number_format($totalPengeluaranGaji, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Biaya hidup, pendidikan, dan cicilan</span>
            </div>

            <div class="p-4 bg-blue-600 text-white rounded-2xl flex flex-col justify-between shadow-xs">
                <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">SISA PENGHASILAN BEBAS</span>
                <span id="metric_gaji_sisa" class="text-xl sm:text-2xl font-black text-white tracking-tight block mt-1">
                    Rp <?= number_format($sisaPenghasilanGaji, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-blue-100 mt-1 block">Kapasitas bayar angsuran kredit baru</span>
            </div>
        </div>

        <!-- 1. LAPORAN PENGHASILAN DAN PENGELUARAN PER BULAN (TIPE GAJI) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wide">1. LAPORAN PENGHASILAN DAN PENGELUARAN PER BULAN</h4>
                    <p class="text-[11px] text-slate-500">Karyawan / PNS / Pensiunan (Tanpa Omzet, HPP, atau Biaya Operasional) &bull; Periode <?= $tahunPeriode ?></p>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Format Penghasilan Gaji
                </span>
            </div>

            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-blue-50/70 border-b border-blue-100 text-blue-900 font-bold uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-4 w-60">KOMPONEN KEUANGAN</th>
                            <th class="py-3 px-4 hidden md:table-cell">KETERANGAN & BUKTI VERIFIKASI</th>
                            <th class="py-3 px-4 text-right">JUMLAH (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <!-- PENGHASILAN -->
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="3" class="py-2 px-4 tracking-wider text-blue-900">A. PENGHASILAN BULANAN</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 font-semibold text-slate-800" id="label_gaji_pokok"><?= esc($namaGajiPokok) ?></td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Sesuai slip gaji resmi &amp; verifikasi rekening payroll</td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="row_gaji_pokok">Rp <?= number_format($gajiPokok, 0, ',', '.') ?></td>
                        </tr>
                        <!-- Baris Pendapatan Lain-Lain (Dinamis: Muncul Hanya Jika Ada) -->
                        <tr id="container_row_gaji_lain" class="<?= $penghasilanLain > 0 ? '' : 'hidden' ?>">
                            <td class="py-2.5 px-4 font-semibold text-slate-800" id="label_gaji_lain">Pendapatan Lain-Lain</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell" id="desc_gaji_lain"><?= !empty($keuangan['keterangan_pendapatan_lain_rk']) ? esc($keuangan['keterangan_pendapatan_lain_rk']) : 'Usaha sampingan, sewa aset, atau penghasilan pasangan' ?></td>
                            <td class="py-2.5 px-4 text-right font-medium text-emerald-700" id="row_gaji_lain">Rp <?= number_format($penghasilanLain, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="bg-blue-50/40 font-bold text-blue-900">
                            <td class="py-2.5 px-4">Total Penghasilan</td>
                            <td class="py-2.5 px-4 text-slate-600 text-[11px] hidden md:table-cell">Total inflow penghasilan keluarga per bulan</td>
                            <td class="py-2.5 px-4 text-right font-black text-blue-700" id="row_gaji_tot_inflow">Rp <?= number_format($totalPenghasilanGaji, 0, ',', '.') ?></td>
                        </tr>

                        <!-- PENGELUARAN -->
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="3" class="py-2 px-4 tracking-wider text-rose-800">B. PENGELUARAN BULANAN</td>
                        </tr>
                        <!-- 1. Pengeluaran Kebutuhan Pokok (Dinamis: Muncul jika > 0) -->
                        <tr id="container_row_gaji_hidup" class="<?= $biayaHidupGaji > 0 ? '' : 'hidden' ?>">
                            <td class="py-2.5 px-4 font-semibold text-rose-700">Pengeluaran Kebutuhan Pokok</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Belanja dapur, konsumsi keluarga, listrik, air &amp; kebutuhan rumah tangga harian</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_gaji_b_hidup">(Rp <?= number_format($biayaHidupGaji, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- 2. Biaya & Beban Bebas Lainnya (Dinamis per rincian yang diinput) -->
                        <tr id="container_row_gaji_beban_default" class="<?= (empty($listDetailBeban) && $biayaLainGaji > 0) ? '' : 'hidden' ?>">
                            <td class="py-2.5 px-4 font-semibold text-rose-700" id="label_gaji_b_lain">Biaya &amp; Beban Bebas Lainnya</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Asuransi, biaya pendidikan/sekolah, transport, dan biaya tak terduga (beban dinamis)</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_gaji_b_lain">(Rp <?= number_format($biayaLainGaji, 0, ',', '.') ?>)</td>
                        </tr>
                        <?php if (!empty($listDetailBeban)): ?>
                            <?php foreach ($listDetailBeban as $bItem): ?>
                                <tr class="row_gaji_beban_item">
                                    <td class="py-2.5 px-4 font-semibold text-rose-700"><?= esc($bItem['nama']) ?></td>
                                    <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Biaya / beban bebas tambahan</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600">(Rp <?= number_format($bItem['nominal'], 0, ',', '.') ?>)</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- Anchor marker untuk live sync JS rows beban lain -->
                        <tr id="anchor_gaji_beban" class="hidden"></tr>

                        <!-- 3. Cicilan Pinjaman Berjalan (SLIK) (Dinamis: Muncul jika ada cicilan) -->
                        <tr id="container_row_gaji_cicilan" class="<?= $cicilanBerjalanGaji > 0 ? '' : 'hidden' ?>">
                            <td class="py-2.5 px-4 font-semibold text-rose-700">Cicilan Pinjaman Berjalan</td>
                            <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">KPR, kredit kendaraan, atau pinjaman bank saat ini (SLIK / komitmen)</td>
                            <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="row_gaji_b_cicilan">(Rp <?= number_format($cicilanBerjalanGaji, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- Baris Keterangan Jika Seluruh Pengeluaran Belum Diisi / Rp 0 -->
                        <tr id="row_gaji_pengeluaran_kosong" class="<?= ($biayaHidupGaji <= 0 && empty($listDetailBeban) && $cicilanBerjalanGaji <= 0) ? '' : 'hidden' ?>">
                            <td colspan="3" class="py-2.5 px-4 text-center text-slate-400 italic text-[11px]">Belum ada data pengeluaran bulanan / belum diisi di Tab 9</td>
                        </tr>
                        <tr class="bg-rose-50/40 font-bold text-rose-900">
                            <td class="py-2.5 px-4">Total Pengeluaran</td>
                            <td class="py-2.5 px-4 text-slate-600 text-[11px] hidden md:table-cell">Total beban komitmen hidup dan angsuran</td>
                            <td class="py-2.5 px-4 text-right font-black text-rose-700" id="row_gaji_tot_outflow">(Rp <?= number_format($totalPengeluaranGaji, 0, ',', '.') ?>)</td>
                        </tr>

                        <!-- SISA PENGHASILAN -->
                        <tr class="bg-emerald-50/80 border-t-2 border-emerald-300">
                            <td class="py-3 px-4 font-black text-emerald-950 uppercase">
                                = Sisa Penghasilan
                            </td>
                            <td class="py-3 px-4 text-emerald-800 font-semibold text-[11px] hidden md:table-cell">
                                Dana kapasitas bersih untuk membayar angsuran pembiayaan baru
                            </td>
                            <td class="py-3 px-4 text-right font-black text-emerald-800 text-sm sm:text-base" id="row_gaji_sisa_penghasilan">
                                Rp <?= number_format($sisaPenghasilanGaji, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. DAFTAR HARTA DAN UTANG (TIPE GAJI - TANPA ASET USAHA!) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between pb-1">
                <div>
                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-800 uppercase tracking-wide">2. DAFTAR HARTA DAN UTANG (PRIBADI)</h4>
                    <p class="text-[11px] text-slate-500">Tanpa Aset Usaha &bull; Tabungan, Tanah, Bangunan, Kendaraan dikurangi Utang = <strong>Kekayaan Bersih</strong></p>
                </div>
                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Neraca Pribadi Karyawan/PNS
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <!-- Sisi Kiri: HARTA PRIBADI (TANPA ASET USAHA) -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                    <div class="bg-blue-50/80 px-4 py-3 border-b border-blue-100 flex items-center justify-between">
                        <span class="font-extrabold text-xs text-blue-900 uppercase tracking-wider">A. DAFTAR HARTA PRIBADI</span>
                        <span class="text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">Harta</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <tbody class="divide-y divide-slate-100">
                            <!-- 1. Tabungan / Deposito Bank -->
                            <tr id="row_harta_gaji_tabungan" class="<?= $tabunganGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Tabungan / Deposito Bank</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="harta_gaji_tabungan">Rp <?= number_format($tabunganGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- 2. Tanah -->
                            <tr id="row_harta_gaji_tanah" class="<?= $tanahGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Tanah</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="harta_gaji_tanah">Rp <?= number_format($tanahGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- 3. Bangunan (Rumah Tinggal) -->
                            <tr id="row_harta_gaji_bangunan" class="<?= $bangunanGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Bangunan (Rumah Tinggal)</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="harta_gaji_bangunan">Rp <?= number_format($bangunanGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- 4. Kendaraan (Mobil / Motor) -->
                            <tr id="row_harta_gaji_kendaraan" class="<?= $kendaraanGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Kendaraan (Mobil / Motor)</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="harta_gaji_kendaraan">Rp <?= number_format($kendaraanGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- Baris Keterangan Jika Belum Ada Data Harta -->
                            <tr id="row_harta_gaji_kosong" class="<?= $totalHartaGaji <= 0 ? '' : 'hidden' ?>">
                                <td colspan="2" class="py-3 px-4 text-center text-slate-400 italic text-[11px]">Belum ada data agunan / tabungan yang dicatat di Tab 4 &amp; Tab 8</td>
                            </tr>
                            <tr class="bg-blue-600 text-white font-extrabold text-sm">
                                <td class="py-3 px-4 uppercase">TOTAL HARTA</td>
                                <td class="py-3 px-4 text-right text-base font-black" id="harta_gaji_total">Rp <?= number_format($totalHartaGaji, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sisi Kanan: UTANG & KEKAYAAN BERSIH -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                    <div class="bg-indigo-50/80 px-4 py-3 border-b border-indigo-100 flex items-center justify-between">
                        <span class="font-extrabold text-xs text-indigo-900 uppercase tracking-wider">B. DAFTAR UTANG & KEKAYAAN BERSIH</span>
                        <span class="text-[10px] font-bold text-indigo-700 bg-white px-2 py-0.5 rounded border border-indigo-200">Kewajiban & Ekuitas</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <tbody class="divide-y divide-slate-100">
                            <!-- 1. Utang Bank -->
                            <tr id="row_utang_gaji_bank" class="<?= $utangBankGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Utang Bank (KPR, KTA, SLIK)</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="utang_gaji_bank">Rp <?= number_format($utangBankGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- 2. Utang Koperasi -->
                            <tr id="row_utang_gaji_koperasi" class="<?= $utangKoperasiGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Utang Koperasi</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="utang_gaji_koperasi">Rp <?= number_format($utangKoperasiGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- 3. Pinjaman Online -->
                            <tr id="row_utang_gaji_pinjol" class="<?= $utangPinjolGaji > 0 ? '' : 'hidden' ?>">
                                <td class="py-2.5 px-4 font-semibold text-slate-800">&bull; Pinjaman Online (Pinjol / Paylater)</td>
                                <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="utang_gaji_pinjol">Rp <?= number_format($utangPinjolGaji, 0, ',', '.') ?></td>
                            </tr>
                            <!-- Baris Keterangan Jika Tidak Ada Utang -->
                            <tr id="row_utang_gaji_kosong" class="<?= $totalUtangGaji <= 0 ? '' : 'hidden' ?>">
                                <td colspan="2" class="py-3 px-4 text-center text-slate-400 italic text-[11px]">Tidak ada catatan pinjaman / utang berjalan di SLIK</td>
                            </tr>
                            <tr class="bg-rose-50/50 font-bold text-rose-900">
                                <td class="py-2 px-4">Total Utang</td>
                                <td class="py-2 px-4 text-right font-bold text-rose-700" id="utang_gaji_total">Rp <?= number_format($totalUtangGaji, 0, ',', '.') ?></td>
                            </tr>

                            <!-- Hasil Kekayaan Bersih -->
                            <tr class="bg-indigo-50/50">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-indigo-900 block text-xs uppercase">Kekayaan Bersih</span>
                                    <span class="text-[10px] text-slate-500">Total Harta dikurangi Total Utang</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-indigo-700 text-sm sm:text-base" id="kekayaan_bersih_gaji">
                                    Rp <?= number_format($kekayaanBersihGaji, 0, ',', '.') ?>
                                </td>
                            </tr>
                            <tr class="bg-indigo-900 text-white font-extrabold text-sm">
                                <td class="py-3 px-4 uppercase">TOTAL UTANG + KEKAYAAN BERSIH</td>
                                <td class="py-3 px-4 text-right text-base font-black text-emerald-300" id="pasiva_total_gaji">Rp <?= number_format($totalHartaGaji, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- =========================================================================
         FOOTER NAVIGASI FORM
    ========================================================================== -->
    <div class="mt-8 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <button type="button" onclick="tabNav(-1)" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </button>

        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
            <span>Document ID: <?= $noAppDisplay ?></span>
        </div>
    </div>
</div>

<!-- Script Library html2pdf.js untuk cetak & unduh PDF instan -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- =============================================================================
     MODAL PREVIEW & CETAK LEMBAR A4 RESMI (PERSIS CONTOH DESAIN)
============================================================================== -->
<div id="modalPreviewLaporanKeuangan" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="bg-slate-100 rounded-3xl shadow-2xl border border-slate-300 max-w-4xl w-full max-h-[96vh] flex flex-col overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Toolbar Header (Persis Seperti di Foto Contoh) -->
        <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between gap-4 shrink-0 shadow-2xs no-print">
            <div class="flex items-center gap-3">
                <button type="button" onclick="tutupModalPreviewLaporan()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl transition border border-slate-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </button>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-xs font-bold text-slate-800">Estimasi_Keuangan_Perorangan.pdf</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <span class="text-xs text-slate-400 font-mono hidden sm:inline">A4 Portrait (218 × 297mm)</span>
                
                <!-- Tombol Cetak (Print) -->
                <button type="button" id="btnCetakModal" onclick="cetakDokumenA4()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-300 transition shadow-2xs cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak (Print)</span>
                </button>

                <!-- Tombol Unduh PDF -->
                <button type="button" id="btnUnduhPdfModal" onclick="unduhDokumenPdfLangsung()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span id="labelUnduhModal">Unduh PDF</span>
                </button>
            </div>
        </div>

        <!-- Sheet Kertas A4 White Document Preview (Persis Format & Warna Foto) -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex justify-center bg-slate-200/70" id="printSheetContainer">
            <div id="printSheetLaporan" class="bg-white w-full max-w-[760px] p-8 sm:p-14 shadow-xl border border-slate-200/90 text-slate-800 space-y-7 text-xs rounded-sm">
                
                <!-- Header Dokumen -->
                <div class="flex items-center justify-between text-xs text-slate-500 font-semibold border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="tracking-wider uppercase font-bold text-slate-700 text-[11px]">OTW KREDIT | DOKUMEN ESTIMASI FINANSIAL</span>
                    </div>
                    <span class="font-mono text-slate-500 text-[11px]">REF: <?= $noAppDisplay ?></span>
                </div>

                <!-- Judul Laporan -->
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight" id="doc_preview_title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                    <p class="text-xs text-slate-500 mt-1" id="doc_preview_subtitle"><?= esc($kredit['nama_debitur'] ?? 'Perorangan') ?>, Periode Per Bulan <?= $tahunPeriode ?></p>
                </div>

                <!-- ==============================================================
                     TAMPILAN DOKUMEN 1: PERORANGAN PUNYA GAJI (PERSIS FOTO CONTOH)
                     ============================================================== -->
                <div id="doc_block_gaji" class="space-y-7 <?= !$isKaryawanPns ? 'hidden' : '' ?>">
                    <!-- 1. LAPORAN LABA RUGI / PENGHASILAN -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>1. LAPORAN LABA RUGI</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Bulanan)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal" id="p_label_g_pokok"><?= esc($namaGajiPokok) ?></td>
                                    <td class="py-2.5 px-4 text-right font-medium" id="p_g_pokok">Rp <?= number_format($gajiPokok, 0, ',', '.') ?></td>
                                </tr>
                                <tr id="p_row_g_lain" class="<?= $penghasilanLain > 0 ? '' : 'hidden' ?>">
                                    <td class="py-2.5 px-4 font-normal">Pendapatan Lain-lain</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-emerald-700" id="p_g_tambahan">Rp <?= number_format($penghasilanLain, 0, ',', '.') ?></td>
                                </tr>
                                <tr class="font-bold text-slate-900 bg-slate-50/50">
                                    <td class="py-2.5 px-4">Total Penghasilan</td>
                                    <td class="py-2.5 px-4 text-right font-bold" id="p_g_total_in">Rp <?= number_format($totalPenghasilanGaji, 0, ',', '.') ?></td>
                                </tr>
                                <tr id="p_row_g_hidup" class="<?= ($biayaHidupGaji + $biayaLainGaji) > 0 ? '' : 'hidden' ?>">
                                    <td class="py-2.5 px-4 font-normal">Pengeluaran Rumah Tangga</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_g_hidup">(Rp <?= number_format($biayaHidupGaji + $biayaLainGaji, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr id="p_row_g_cicilan" class="<?= $cicilanBerjalanGaji > 0 ? '' : 'hidden' ?>">
                                    <td class="py-2.5 px-4 font-normal">Cicilan Berjalan</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_g_cicilan">(Rp <?= number_format($cicilanBerjalanGaji, 0, ',', '.') ?>)</td>
                                </tr>
                                <!-- Baris Surplus (Persis Kotak Biru di Gambar) -->
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Surplus</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_g_surplus">Rp <?= number_format($sisaPenghasilanGaji, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. LAPORAN ARUS KAS -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>2. LAPORAN ARUS KAS</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Likuiditas)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Penghasilan Masuk</td>
                                    <td class="py-2.5 px-4 text-right font-medium" id="p_g_kas_masuk">Rp <?= number_format($totalPenghasilanGaji, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Pengeluaran Keluar</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_g_kas_keluar">(Rp <?= number_format($totalPengeluaranGaji, 0, ',', '.') ?>)</td>
                                </tr>
                                <!-- Baris Arus Kas Bersih (Persis Kotak Biru di Gambar) -->
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Arus Kas Bersih</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_g_kas_bersih">Rp <?= number_format($sisaPenghasilanGaji, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 3. DAFTAR HARTA & UTANG -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>3. DAFTAR HARTA DAN UTANG</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Neraca)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Total Harta Pribadi (Tabungan, Tanah, Bangunan, Kendaraan)</td>
                                    <td class="py-2.5 px-4 text-right font-medium" id="p_g_tot_harta">Rp <?= number_format($totalHartaGaji, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(-) Total Utang (Bank, Koperasi, Pinjol)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_g_tot_utang">(Rp <?= number_format($totalUtangGaji, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Kekayaan Bersih</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_g_net_worth">Rp <?= number_format($kekayaanBersihGaji, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ==============================================================
                     TAMPILAN DOKUMEN 2: PERORANGAN PUNYA USAHA (WIRASWASTA)
                     ============================================================== -->
                <div id="doc_block_usaha" class="space-y-7 <?= $isKaryawanPns ? 'hidden' : '' ?>">
                    <!-- 1. LAPORAN LABA RUGI SEDERHANA -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>1. LAPORAN LABA RUGI SEDERHANA (USAHA)</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Bulanan)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">
                                        <span class="font-semibold text-slate-800 block">Penjualan / Omzet (Ramai, Biasa, Sepi)</span>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-bold text-slate-900" id="p_u_omzet">Rp <?= number_format($omsetBulanan, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(-) HPP (Harga Pokok Penjualan)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_u_hpp">(Rp <?= number_format($hppNominal, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="font-bold text-slate-900 bg-slate-50/50">
                                    <td class="py-2.5 px-4">= Laba Kotor</td>
                                    <td class="py-2.5 px-4 text-right font-bold" id="p_u_laba_kotor">Rp <?= number_format($labaKotor, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(-) Biaya Operasional (Sewa, Listrik, Transport, dll)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_u_operasional">(Rp <?= number_format($nomBiayaOperasional, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="font-bold text-slate-900 bg-slate-50/50">
                                    <td class="py-2.5 px-4">= Laba Usaha</td>
                                    <td class="py-2.5 px-4 text-right font-bold" id="p_u_laba_usaha">Rp <?= number_format($labaUsaha, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(+) Pendapatan Lain-lain</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-emerald-600" id="p_u_pendapatan_lain">Rp <?= number_format($nomPendapatanLain, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(-) Prive (Pengeluaran Rumah Tangga)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_u_prive">(Rp <?= number_format($nomPengeluaranRT, 0, ',', '.') ?>)</td>
                                </tr>
                                <!-- Baris Surplus (Persis Kotak Biru di Gambar) -->
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Surplus / Sisa Penghasilan</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_u_surplus">Rp <?= number_format($labaBersihSisaPenghasilanUsaha, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. LAPORAN ARUS KAS -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>2. LAPORAN ARUS KAS</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Likuiditas)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Kas Masuk (Penjualan + Pendapatan Lain)</td>
                                    <td class="py-2.5 px-4 text-right font-medium" id="p_u_kas_masuk">Rp <?= number_format($omsetBulanan + $nomPendapatanLain, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Kas Keluar (HPP + Operasional + Prive)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_u_kas_keluar">(Rp <?= number_format($hppNominal + $nomBiayaOperasional + $nomPengeluaranRT, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Arus Kas Bersih (Saldo Akhir)</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_u_kas_bersih">Rp <?= number_format($labaBersihSisaPenghasilanUsaha, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 3. DAFTAR HARTA DAN UTANG -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between bg-blue-50/70 px-4 py-2 rounded-t-lg text-blue-900 font-bold text-xs uppercase tracking-wide border-t border-x border-blue-100">
                            <span>3. DAFTAR HARTA DAN UTANG</span>
                            <span class="text-[11px] text-blue-800 font-semibold lowercase tracking-normal">IDR (Neraca)</span>
                        </div>
                        <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                            <thead>
                                <tr class="bg-blue-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-blue-100">
                                    <th class="py-2.5 px-4 font-bold">KOMPONEN</th>
                                    <th class="py-2.5 px-4 text-right font-bold">NOMINAL (IDR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">Total Aset Usaha & Pribadi</td>
                                    <td class="py-2.5 px-4 text-right font-medium" id="p_u_tot_harta">Rp <?= number_format($totalHartaUsaha, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 font-normal">(-) Total Utang (Dagang, Bank, Koperasi, Pinjol)</td>
                                    <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="p_u_tot_utang">(Rp <?= number_format($totalUtangUsaha, 0, ',', '.') ?>)</td>
                                </tr>
                                <tr class="bg-blue-100/80">
                                    <td class="py-3 px-4 font-bold text-blue-800 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-700 inline-block"></span>
                                        <span>Kekayaan Bersih</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-black text-blue-800 text-sm" id="p_u_net_worth">Rp <?= number_format($kekayaanBersihUsaha, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Dokumen -->
                <div class="pt-8 border-t border-slate-200 flex items-center justify-between text-xs text-slate-400 font-medium">
                    <span>Laporan ini merupakan estimasi</span>
                    <span>Halaman 1 dari 1 &bull; Dokumen Resmi Sistem</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm 10mm 12mm;
    }
    html, body {
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        min-height: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    /* Sembunyikan semua konten app di luar modal preview */
    body * {
        visibility: hidden !important;
    }
    #modalPreviewLaporanKeuangan,
    #modalPreviewLaporanKeuangan #printSheetLaporan,
    #modalPreviewLaporanKeuangan #printSheetLaporan * {
        visibility: visible !important;
    }
    #modalPreviewLaporanKeuangan {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        overflow: visible !important;
        z-index: 999999 !important;
    }
    #modalPreviewLaporanKeuangan .no-print,
    #modalPreviewLaporanKeuangan > div > div:first-child {
        display: none !important;
    }
    #modalPreviewLaporanKeuangan > div {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #modalPreviewLaporanKeuangan #printSheetLaporan {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        background: #ffffff !important;
    }
}
</style>

<!-- =============================================================================
     LOGIKA JAVASCRIPT: SWITCH KATEGORI & SINKRONISASI
============================================================================== -->
<script>
let activeKategoriDebitur = '<?= $isKaryawanPns ? "gaji" : "usaha" ?>';

// Ganti Kategori Debitur (Punya Usaha vs Punya Gaji)
function gantiKategoriDebitur(kategori, syncCheckbox = true) {
    activeKategoriDebitur = kategori;
    const secUsaha = document.getElementById('sectionPeroranganUsaha');
    const secGaji = document.getElementById('sectionPeroranganGaji');
    const btnUsaha = document.getElementById('btnKategoriUsaha');
    const btnGaji = document.getElementById('btnKategoriGaji');

    const docUsaha = document.getElementById('doc_block_usaha');
    const docGaji = document.getElementById('doc_block_gaji');
    const sub = document.getElementById('doc_preview_subtitle');

    if (kategori === 'usaha') {
        if (secUsaha) secUsaha.classList.remove('hidden');
        if (secGaji) secGaji.classList.add('hidden');
        if (btnUsaha) {
            btnUsaha.classList.remove('hidden');
            btnUsaha.className = 'px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-blue-600 text-white shadow-xs';
        }
        if (btnGaji) {
            btnGaji.classList.add('hidden');
        }
        const descEl = document.getElementById('descTipeDebitur');
        if (descEl) {
            descEl.innerHTML = 'Struktur laporan disesuaikan khusus untuk debitur dengan <strong>kegiatan usaha (Wiraswasta/Toko)</strong>.';
        }
        if (docUsaha) docUsaha.classList.remove('hidden');
        if (docGaji) docGaji.classList.add('hidden');
        if (sub) sub.innerText = 'Perorangan (Wiraswasta), Periode Per Bulan <?= $tahunPeriode ?>';

        // Sinkronisasi balik ke checkbox Tab 9
        if (syncCheckbox) {
            const checkHpp = document.getElementById('checkAdaHppPerorangan');
            if (checkHpp && !checkHpp.checked) {
                checkHpp.checked = true;
                if (typeof window.toggleHppPerorangan === 'function') {
                    window.toggleHppPerorangan(true, false);
                }
            }
        }
    } else {
        if (secUsaha) secUsaha.classList.add('hidden');
        if (secGaji) secGaji.classList.remove('hidden');
        if (btnGaji) {
            btnGaji.classList.remove('hidden');
            btnGaji.className = 'px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 bg-blue-600 text-white shadow-xs';
        }
        if (btnUsaha) {
            btnUsaha.classList.add('hidden');
        }
        const descEl = document.getElementById('descTipeDebitur');
        if (descEl) {
            descEl.innerHTML = 'Struktur laporan disesuaikan khusus untuk debitur <strong>berpenghasilan tetap (Karyawan/PNS/Pensiun)</strong>.';
        }
        if (docUsaha) docUsaha.classList.add('hidden');
        if (docGaji) docGaji.classList.remove('hidden');
        if (sub) sub.innerText = 'Perorangan (Penghasilan Tetap), Periode Per Bulan <?= $tahunPeriode ?>';

        // Sinkronisasi balik ke checkbox Tab 9
        if (syncCheckbox) {
            const checkHpp = document.getElementById('checkAdaHppPerorangan');
            if (checkHpp && checkHpp.checked) {
                checkHpp.checked = false;
                if (typeof window.toggleHppPerorangan === 'function') {
                    window.toggleHppPerorangan(false, false);
                }
            }
        }
    }

    sinkronkanLaporanKeuangan();
}
window.gantiKategoriDebitur = gantiKategoriDebitur;

// Format Angka ke Rupiah String
function formatNominalLap(num) {
    return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Parse Rupiah String ke Numeric Float
function parseNominalLap(str) {
    if (!str) return 0;
    if (typeof str === 'number') return str;
    const clean = str.toString().replace(/[^0-9]/g, '');
    return clean ? parseFloat(clean) : 0;
}

// Sinkronisasi Live Data
function sinkronkanLaporanKeuangan() {
    // 1. Data Usaha
    const omzet = parseNominalLap(document.getElementById('pendapatan_rk')?.value) || <?= $omsetBulanan ?>;
    const hppPct = parseFloat(document.getElementById('hpp_persen_rk')?.value) || <?= $hppPersen ?>;
    const hppNom = Math.round(omzet * (hppPct / 100));
    const labaKotor = omzet - hppNom;

    const omzetRamai = Math.round(omzet * 0.45);
    const omzetBiasa = Math.round(omzet * 0.40);
    const omzetSepi  = omzet - omzetRamai - omzetBiasa;

    const biayaOperasional = parseNominalLap(document.getElementById('beban_lainnya_nominal_rk')?.value) || <?= $nomBiayaOperasional ?>;
    const labaUsaha = labaKotor - biayaOperasional;

    const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
    const pendapatanLain = isAdaLain ? (parseNominalLap(document.getElementById('pendapatan_lain_rk')?.value) || 0) : 0;
    const elBiayaHidup = document.getElementById('biaya_hidup_nominal_rk');
    const pengeluaranRT = elBiayaHidup ? parseNominalLap(elBiayaHidup.value) : <?= (float)$nomPengeluaranRT ?>;

    const sisaUsaha = (labaUsaha + pendapatanLain) - pengeluaranRT;
    const cicilanAcuan = parseNominalLap(document.getElementById('angsuran_dipilih')?.value) 
        || parseNominalLap(document.getElementById('angsuran_installment')?.value) 
        || <?= $totalCicilanSlik ?>;
    const bufferUsaha = sisaUsaha - cicilanAcuan;

    // Update Tampilan Usaha
    if (document.getElementById('metric_usaha_omzet')) document.getElementById('metric_usaha_omzet').innerText = formatNominalLap(omzet);
    if (document.getElementById('metric_usaha_laba_usaha')) document.getElementById('metric_usaha_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('metric_usaha_sisa')) document.getElementById('metric_usaha_sisa').innerText = formatNominalLap(sisaUsaha);

    if (document.getElementById('row_usaha_ramai')) document.getElementById('row_usaha_ramai').innerText = formatNominalLap(omzetRamai);
    if (document.getElementById('row_usaha_biasa')) document.getElementById('row_usaha_biasa').innerText = formatNominalLap(omzetBiasa);
    if (document.getElementById('row_usaha_sepi')) document.getElementById('row_usaha_sepi').innerText = formatNominalLap(omzetSepi);
    if (document.getElementById('row_usaha_tot_omzet')) document.getElementById('row_usaha_tot_omzet').innerText = formatNominalLap(omzet);

    if (document.getElementById('row_usaha_hpp')) document.getElementById('row_usaha_hpp').innerText = `(${formatNominalLap(hppNom)})`;
    if (document.getElementById('row_usaha_laba_kotor')) document.getElementById('row_usaha_laba_kotor').innerText = formatNominalLap(labaKotor);
    if (document.getElementById('row_usaha_operasional')) document.getElementById('row_usaha_operasional').innerText = `(${formatNominalLap(biayaOperasional)})`;
    if (document.getElementById('row_usaha_laba_usaha')) document.getElementById('row_usaha_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('row_usaha_pendapatan_lain')) document.getElementById('row_usaha_pendapatan_lain').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('row_usaha_prive')) document.getElementById('row_usaha_prive').innerText = `(${formatNominalLap(pengeluaranRT)})`;
    if (document.getElementById('row_usaha_sisa_penghasilan')) document.getElementById('row_usaha_sisa_penghasilan').innerText = formatNominalLap(sisaUsaha);
    if (document.getElementById('row_usaha_cicilan')) document.getElementById('row_usaha_cicilan').innerText = `(${formatNominalLap(cicilanAcuan)})`;
    if (document.getElementById('row_usaha_buffer')) document.getElementById('row_usaha_buffer').innerText = formatNominalLap(bufferUsaha);

    // Harta & Utang Usaha
    const kasU = Math.round(omzet * 0.15);
    const piutangU = Math.round(omzet * 0.10);
    const stokU = Math.round(hppNom * 0.35);
    const alatU = Math.round(omzet * 0.12);
    const subAstU = kasU + piutangU + stokU + alatU;

    const tabP = <?= (float)$tabunganPribadi ?>;
    const tanahP = <?= (float)$tanahPribadi ?>;
    const bgnP = <?= (float)$bangunanPribadi ?>;
    const kdrP = <?= (float)$kendaraanPribadi ?>;
    const subAstP = tabP + tanahP + bgnP + kdrP;
    const totHartaU = subAstU + subAstP;

    const utgDagang = Math.round(hppNom * 0.15);
    const utgBank = (<?= (float)$utangBank ?> > 0) ? <?= (float)$utangBank ?> : (cicilanAcuan > 0 ? cicilanAcuan * 18 : 0);
    const utgKoperasi = <?= (float)$utangKoperasi ?>;
    const utgPinjol = <?= (float)$utangPinjol ?>;
    const totUtangU = utgDagang + utgBank + utgKoperasi + utgPinjol;
    const netWorthU = totHartaU - totUtangU;

    if (document.getElementById('ast_usaha_kas')) document.getElementById('ast_usaha_kas').innerText = formatNominalLap(kasU);
    if (document.getElementById('ast_usaha_piutang')) document.getElementById('ast_usaha_piutang').innerText = formatNominalLap(piutangU);
    if (document.getElementById('ast_usaha_stok')) document.getElementById('ast_usaha_stok').innerText = formatNominalLap(stokU);
    if (document.getElementById('ast_usaha_alat')) document.getElementById('ast_usaha_alat').innerText = formatNominalLap(alatU);
    if (document.getElementById('ast_usaha_sub')) document.getElementById('ast_usaha_sub').innerText = formatNominalLap(subAstU);

    if (document.getElementById('row_ast_pribadi_tabungan')) {
        if (tabP > 0) document.getElementById('row_ast_pribadi_tabungan').classList.remove('hidden');
        else document.getElementById('row_ast_pribadi_tabungan').classList.add('hidden');
    }
    if (document.getElementById('row_ast_pribadi_tanah')) {
        if (tanahP > 0) document.getElementById('row_ast_pribadi_tanah').classList.remove('hidden');
        else document.getElementById('row_ast_pribadi_tanah').classList.add('hidden');
    }
    if (document.getElementById('row_ast_pribadi_bangunan')) {
        if (bgnP > 0) document.getElementById('row_ast_pribadi_bangunan').classList.remove('hidden');
        else document.getElementById('row_ast_pribadi_bangunan').classList.add('hidden');
    }
    if (document.getElementById('row_ast_pribadi_kendaraan')) {
        if (kdrP > 0) document.getElementById('row_ast_pribadi_kendaraan').classList.remove('hidden');
        else document.getElementById('row_ast_pribadi_kendaraan').classList.add('hidden');
    }

    if (document.getElementById('ast_pribadi_tabungan')) document.getElementById('ast_pribadi_tabungan').innerText = formatNominalLap(tabP);
    if (document.getElementById('ast_pribadi_tanah')) document.getElementById('ast_pribadi_tanah').innerText = formatNominalLap(tanahP);
    if (document.getElementById('ast_pribadi_bangunan')) document.getElementById('ast_pribadi_bangunan').innerText = formatNominalLap(bgnP);
    if (document.getElementById('ast_pribadi_kendaraan')) document.getElementById('ast_pribadi_kendaraan').innerText = formatNominalLap(kdrP);
    if (document.getElementById('ast_pribadi_sub')) document.getElementById('ast_pribadi_sub').innerText = formatNominalLap(subAstP);
    if (document.getElementById('ast_total_harta_usaha')) document.getElementById('ast_total_harta_usaha').innerText = formatNominalLap(totHartaU);

    if (document.getElementById('row_utang_dagang')) {
        if (utgDagang > 0) document.getElementById('row_utang_dagang').classList.remove('hidden');
        else document.getElementById('row_utang_dagang').classList.add('hidden');
    }
    if (document.getElementById('row_utang_bank')) {
        if (utgBank > 0) document.getElementById('row_utang_bank').classList.remove('hidden');
        else document.getElementById('row_utang_bank').classList.add('hidden');
    }
    if (document.getElementById('row_utang_koperasi')) {
        if (utgKoperasi > 0) document.getElementById('row_utang_koperasi').classList.remove('hidden');
        else document.getElementById('row_utang_koperasi').classList.add('hidden');
    }
    if (document.getElementById('row_utang_pinjol')) {
        if (utgPinjol > 0) document.getElementById('row_utang_pinjol').classList.remove('hidden');
        else document.getElementById('row_utang_pinjol').classList.add('hidden');
    }

    if (document.getElementById('utang_dagang')) document.getElementById('utang_dagang').innerText = formatNominalLap(utgDagang);
    if (document.getElementById('utang_bank')) document.getElementById('utang_bank').innerText = formatNominalLap(utgBank);
    if (document.getElementById('utang_koperasi')) document.getElementById('utang_koperasi').innerText = formatNominalLap(utgKoperasi);
    if (document.getElementById('utang_pinjol')) document.getElementById('utang_pinjol').innerText = formatNominalLap(utgPinjol);
    if (document.getElementById('utang_total_usaha')) document.getElementById('utang_total_usaha').innerText = formatNominalLap(totUtangU);
    if (document.getElementById('kekayaan_bersih_usaha')) document.getElementById('kekayaan_bersih_usaha').innerText = formatNominalLap(netWorthU);
    if (document.getElementById('pasiva_total_usaha')) document.getElementById('pasiva_total_usaha').innerText = formatNominalLap(totHartaU);

    // 2. Data Gaji (Dinamis sinkron dari Tab 9)
    let liveGajiPokok = 0;
    let liveNamaGaji = 'Gaji Pokok / Penghasilan Utama';

    if (Array.isArray(window.daftarPendapatan) && window.daftarPendapatan.length > 0) {
        liveGajiPokok = parseNominalLap(window.daftarPendapatan[0]?.nominal || 0);
        if (window.daftarPendapatan[0]?.nama) {
            liveNamaGaji = window.daftarPendapatan[0].nama;
        }
    } else {
        liveGajiPokok = parseNominalLap(document.getElementById('pendapatan_rk')?.value) || <?= $gajiPokok ?>;
    }

    const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
    const pendapatanLain = isAdaLain ? (parseNominalLap(document.getElementById('pendapatan_lain_rk')?.value) || 0) : 0;
    const ketLain = document.getElementById('keterangan_pendapatan_lain_rk')?.value?.trim();

    const totGajiIn = liveGajiPokok + pendapatanLain;

    // --- PENGELUARAN GAJI DINAMIS (TERGANTUNG TAB 9) ---
    const bHidupG = pengeluaranRT;

    // 1. Pengeluaran Kebutuhan Pokok
    const rowHidup = document.getElementById('container_row_gaji_hidup');
    if (bHidupG > 0) {
        if (rowHidup) rowHidup.classList.remove('hidden');
        if (document.getElementById('row_gaji_b_hidup')) {
            document.getElementById('row_gaji_b_hidup').innerText = `(${formatNominalLap(bHidupG)})`;
        }
    } else {
        if (rowHidup) rowHidup.classList.add('hidden');
        if (document.getElementById('row_gaji_b_hidup')) {
            document.getElementById('row_gaji_b_hidup').innerText = '(Rp 0)';
        }
    }

    // 2. Biaya & Beban Bebas Lainnya
    let liveBebanList = [];
    if (Array.isArray(window.daftarBebanLain) && window.daftarBebanLain.length > 0) {
        liveBebanList = window.daftarBebanLain;
    } else {
        const rawDetailBeban = document.getElementById('detail_beban_lainnya_rk')?.value;
        if (rawDetailBeban) {
            try {
                const parsedBeban = JSON.parse(rawDetailBeban);
                if (Array.isArray(parsedBeban)) {
                    liveBebanList = parsedBeban;
                }
            } catch(e) {}
        }
    }

    const activeBebanList = liveBebanList.filter(item => parseNominalLap(item?.nominal || 0) > 0);
    let totBebanLain = 0;
    activeBebanList.forEach(item => {
        totBebanLain += parseNominalLap(item.nominal || 0);
    });

    // Bersihkan baris dinamis lama
    document.querySelectorAll('.row_gaji_beban_item').forEach(el => el.remove());

    const defaultBebanRow = document.getElementById('container_row_gaji_beban_default');
    const anchorBeban = document.getElementById('anchor_gaji_beban');

    if (activeBebanList.length > 0) {
        if (defaultBebanRow) defaultBebanRow.classList.add('hidden');
        if (anchorBeban && anchorBeban.parentNode) {
            activeBebanList.forEach(item => {
                const nama = item.nama && item.nama.trim() !== '' ? item.nama.trim() : 'Biaya & Beban Lainnya';
                const nom = parseNominalLap(item.nominal || 0);
                const tr = document.createElement('tr');
                tr.className = 'row_gaji_beban_item';
                tr.innerHTML = `
                    <td class="py-2.5 px-4 font-semibold text-rose-700">${nama.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</td>
                    <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Biaya / beban bebas tambahan</td>
                    <td class="py-2.5 px-4 text-right font-medium text-rose-600">(${formatNominalLap(nom)})</td>
                `;
                anchorBeban.parentNode.insertBefore(tr, anchorBeban);
            });
        }
    } else {
        const nomBebanDirect = parseNominalLap(document.getElementById('beban_lainnya_nominal_rk')?.value) || 0;
        if (nomBebanDirect > 0) {
            totBebanLain = nomBebanDirect;
            if (defaultBebanRow) {
                defaultBebanRow.classList.remove('hidden');
                if (document.getElementById('row_gaji_b_lain')) {
                    document.getElementById('row_gaji_b_lain').innerText = `(${formatNominalLap(nomBebanDirect)})`;
                }
            }
        } else {
            if (defaultBebanRow) defaultBebanRow.classList.add('hidden');
        }
    }

    // 3. Cicilan Pinjaman Berjalan
    const rowCicilan = document.getElementById('container_row_gaji_cicilan');
    if (cicilanAcuan > 0) {
        if (rowCicilan) rowCicilan.classList.remove('hidden');
        if (document.getElementById('row_gaji_b_cicilan')) {
            document.getElementById('row_gaji_b_cicilan').innerText = `(${formatNominalLap(cicilanAcuan)})`;
        }
    } else {
        if (rowCicilan) rowCicilan.classList.add('hidden');
        if (document.getElementById('row_gaji_b_cicilan')) {
            document.getElementById('row_gaji_b_cicilan').innerText = '(Rp 0)';
        }
    }

    // 4. Baris Keterangan Jika Semua Pengeluaran 0
    const rowKosong = document.getElementById('row_gaji_pengeluaran_kosong');
    if (bHidupG <= 0 && totBebanLain <= 0 && cicilanAcuan <= 0) {
        if (rowKosong) rowKosong.classList.remove('hidden');
    } else {
        if (rowKosong) rowKosong.classList.add('hidden');
    }

    const totGajiOut = bHidupG + totBebanLain + cicilanAcuan;
    const sisaGaji = totGajiIn - totGajiOut;

    if (document.getElementById('metric_gaji_total')) document.getElementById('metric_gaji_total').innerText = formatNominalLap(totGajiIn);
    if (document.getElementById('metric_gaji_pengeluaran')) document.getElementById('metric_gaji_pengeluaran').innerText = formatNominalLap(totGajiOut);
    if (document.getElementById('metric_gaji_sisa')) document.getElementById('metric_gaji_sisa').innerText = formatNominalLap(sisaGaji);

    if (document.getElementById('row_gaji_tot_outflow')) document.getElementById('row_gaji_tot_outflow').innerText = `(${formatNominalLap(totGajiOut)})`;
    if (document.getElementById('row_gaji_sisa_penghasilan')) document.getElementById('row_gaji_sisa_penghasilan').innerText = formatNominalLap(sisaGaji);

    // Harta & Utang Gaji (Dinamis Sesuai Data Riil Tab 4, 6, 8)
    const tabG = <?= (float)$tabunganGaji ?>;
    const tanahG = <?= (float)$tanahGaji ?>;
    const bgnG = <?= (float)$bangunanGaji ?>;
    const kdrG = <?= (float)$kendaraanGaji ?>;
    const totHartaG = tabG + tanahG + bgnG + kdrG;

    const utgBankG = (<?= (float)$utangBankGaji ?> > 0) ? <?= (float)$utangBankGaji ?> : (cicilanAcuan > 0 ? cicilanAcuan * 18 : 0);
    const utgKopG = <?= (float)$utangKoperasiGaji ?>;
    const utgPinjolG = <?= (float)$utangPinjolGaji ?>;
    const totUtangG = utgBankG + utgKopG + utgPinjolG;
    const netWorthG = totHartaG - totUtangG;

    // Toggle Tampilan Harta Gaji
    const rTabG = document.getElementById('row_harta_gaji_tabungan');
    const rTanahG = document.getElementById('row_harta_gaji_tanah');
    const rBgnG = document.getElementById('row_harta_gaji_bangunan');
    const rKdrG = document.getElementById('row_harta_gaji_kendaraan');
    const rHartaKosong = document.getElementById('row_harta_gaji_kosong');

    if (rTabG) { if (tabG > 0) rTabG.classList.remove('hidden'); else rTabG.classList.add('hidden'); }
    if (rTanahG) { if (tanahG > 0) rTanahG.classList.remove('hidden'); else rTanahG.classList.add('hidden'); }
    if (rBgnG) { if (bgnG > 0) rBgnG.classList.remove('hidden'); else rBgnG.classList.add('hidden'); }
    if (rKdrG) { if (kdrG > 0) rKdrG.classList.remove('hidden'); else rKdrG.classList.add('hidden'); }
    if (rHartaKosong) { if (totHartaG <= 0) rHartaKosong.classList.remove('hidden'); else rHartaKosong.classList.add('hidden'); }

    if (document.getElementById('harta_gaji_tabungan')) document.getElementById('harta_gaji_tabungan').innerText = formatNominalLap(tabG);
    if (document.getElementById('harta_gaji_tanah')) document.getElementById('harta_gaji_tanah').innerText = formatNominalLap(tanahG);
    if (document.getElementById('harta_gaji_bangunan')) document.getElementById('harta_gaji_bangunan').innerText = formatNominalLap(bgnG);
    if (document.getElementById('harta_gaji_kendaraan')) document.getElementById('harta_gaji_kendaraan').innerText = formatNominalLap(kdrG);
    if (document.getElementById('harta_gaji_total')) document.getElementById('harta_gaji_total').innerText = formatNominalLap(totHartaG);

    // Toggle Tampilan Utang Gaji
    const rUtgBank = document.getElementById('row_utang_gaji_bank');
    const rUtgKop = document.getElementById('row_utang_gaji_koperasi');
    const rUtgPinjol = document.getElementById('row_utang_gaji_pinjol');
    const rUtgKosong = document.getElementById('row_utang_gaji_kosong');

    if (rUtgBank) { if (utgBankG > 0) rUtgBank.classList.remove('hidden'); else rUtgBank.classList.add('hidden'); }
    if (rUtgKop) { if (utgKopG > 0) rUtgKop.classList.remove('hidden'); else rUtgKop.classList.add('hidden'); }
    if (rUtgPinjol) { if (utgPinjolG > 0) rUtgPinjol.classList.remove('hidden'); else rUtgPinjol.classList.add('hidden'); }
    if (rUtgKosong) { if (totUtangG <= 0) rUtgKosong.classList.remove('hidden'); else rUtgKosong.classList.add('hidden'); }

    if (document.getElementById('utang_gaji_bank')) document.getElementById('utang_gaji_bank').innerText = formatNominalLap(utgBankG);
    if (document.getElementById('utang_gaji_koperasi')) document.getElementById('utang_gaji_koperasi').innerText = formatNominalLap(utgKopG);
    if (document.getElementById('utang_gaji_pinjol')) document.getElementById('utang_gaji_pinjol').innerText = formatNominalLap(utgPinjolG);
    if (document.getElementById('utang_gaji_total')) document.getElementById('utang_gaji_total').innerText = formatNominalLap(totUtangG);
    if (document.getElementById('kekayaan_bersih_gaji')) document.getElementById('kekayaan_bersih_gaji').innerText = formatNominalLap(netWorthG);
    if (document.getElementById('pasiva_total_gaji')) document.getElementById('pasiva_total_gaji').innerText = formatNominalLap(totHartaG);

    // Update Elemen di Modal Preview Dokumen
    // Preview Gaji
    if (document.getElementById('p_g_pokok')) document.getElementById('p_g_pokok').innerText = formatNominalLap(liveGajiPokok);
    if (document.getElementById('p_g_tambahan')) document.getElementById('p_g_tambahan').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('p_g_total_in')) document.getElementById('p_g_total_in').innerText = formatNominalLap(totGajiIn);

    const pRowHidup = document.getElementById('p_row_g_hidup');
    const totPengeluaranHidup = bHidupG + totBebanLain;
    if (totPengeluaranHidup > 0) {
        if (pRowHidup) pRowHidup.classList.remove('hidden');
        if (document.getElementById('p_g_hidup')) document.getElementById('p_g_hidup').innerText = `(${formatNominalLap(totPengeluaranHidup)})`;
    } else {
        if (pRowHidup) pRowHidup.classList.add('hidden');
    }

    const pRowCicilan = document.getElementById('p_row_g_cicilan');
    if (cicilanAcuan > 0) {
        if (pRowCicilan) pRowCicilan.classList.remove('hidden');
        if (document.getElementById('p_g_cicilan')) document.getElementById('p_g_cicilan').innerText = `(${formatNominalLap(cicilanAcuan)})`;
    } else {
        if (pRowCicilan) pRowCicilan.classList.add('hidden');
    }

    if (document.getElementById('p_g_surplus')) document.getElementById('p_g_surplus').innerText = formatNominalLap(sisaGaji);
    if (document.getElementById('p_g_kas_masuk')) document.getElementById('p_g_kas_masuk').innerText = formatNominalLap(totGajiIn);
    if (document.getElementById('p_g_kas_keluar')) document.getElementById('p_g_kas_keluar').innerText = `(${formatNominalLap(totGajiOut)})`;
    if (document.getElementById('p_g_kas_bersih')) document.getElementById('p_g_kas_bersih').innerText = formatNominalLap(sisaGaji);
    if (document.getElementById('p_g_tot_harta')) document.getElementById('p_g_tot_harta').innerText = formatNominalLap(totHartaG);
    if (document.getElementById('p_g_tot_utang')) document.getElementById('p_g_tot_utang').innerText = `(${formatNominalLap(totUtangG)})`;
    if (document.getElementById('p_g_net_worth')) document.getElementById('p_g_net_worth').innerText = formatNominalLap(netWorthG);

    // Preview Usaha
    if (document.getElementById('p_u_omzet')) document.getElementById('p_u_omzet').innerText = formatNominalLap(omzet);
    if (document.getElementById('p_u_hpp')) document.getElementById('p_u_hpp').innerText = `(${formatNominalLap(hppNom)})`;
    if (document.getElementById('p_u_laba_kotor')) document.getElementById('p_u_laba_kotor').innerText = formatNominalLap(labaKotor);
    if (document.getElementById('p_u_operasional')) document.getElementById('p_u_operasional').innerText = `(${formatNominalLap(biayaOperasional)})`;
    if (document.getElementById('p_u_laba_usaha')) document.getElementById('p_u_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('p_u_pendapatan_lain')) document.getElementById('p_u_pendapatan_lain').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('p_u_prive')) document.getElementById('p_u_prive').innerText = `(${formatNominalLap(pengeluaranRT)})`;
    if (document.getElementById('p_u_surplus')) document.getElementById('p_u_surplus').innerText = formatNominalLap(sisaUsaha);
    if (document.getElementById('p_u_kas_masuk')) document.getElementById('p_u_kas_masuk').innerText = formatNominalLap(omzet + pendapatanLain);
    if (document.getElementById('p_u_kas_keluar')) document.getElementById('p_u_kas_keluar').innerText = `(${formatNominalLap(hppNom + biayaOperasional + pengeluaranRT)})`;
    if (document.getElementById('p_u_kas_bersih')) document.getElementById('p_u_kas_bersih').innerText = formatNominalLap(sisaUsaha);
    if (document.getElementById('p_u_tot_harta')) document.getElementById('p_u_tot_harta').innerText = formatNominalLap(totHartaU);
    if (document.getElementById('p_u_tot_utang')) document.getElementById('p_u_tot_utang').innerText = `(${formatNominalLap(totUtangU)})`;
    if (document.getElementById('p_u_net_worth')) document.getElementById('p_u_net_worth').innerText = formatNominalLap(netWorthU);
}
window.sinkronkanLaporanKeuangan = sinkronkanLaporanKeuangan;

// Buka Modal Preview Lembar A4
function bukaModalPreviewLaporan() {
    sinkronkanLaporanKeuangan();
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

// Tutup Modal Preview
function tutupModalPreviewLaporan() {
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Cetak Lembar A4 (Print Dialog Native Browser Tanpa Hambatan)
function cetakDokumenA4() {
    window.print();
}

// Unduh File PDF Langsung ke Komputer
function unduhDokumenPdfLangsung() {
    const sheet = document.getElementById('printSheetLaporan');
    if (!sheet) return;

    const btn = document.getElementById('btnUnduhPdfModal');
    const label = document.getElementById('labelUnduhModal');
    const oldText = label ? label.innerText : 'Unduh PDF';

    if (label) label.innerText = 'Membuat PDF...';
    if (btn) btn.disabled = true;

    if (typeof html2pdf !== 'undefined') {
        const opt = {
            margin:       [8, 10, 8, 10],
            filename:     'Estimasi_Keuangan_Perorangan.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                allowTaint: true,
                logging: false,
                scrollX: 0,
                scrollY: 0
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(sheet).save().then(function() {
            if (label) label.innerText = oldText;
            if (btn) btn.disabled = false;
        }).catch(function(err) {
            console.warn('html2pdf dialihkan ke print dialog:', err);
            if (label) label.innerText = oldText;
            if (btn) btn.disabled = false;
            window.print();
        });
    } else {
        if (label) label.innerText = oldText;
        if (btn) btn.disabled = false;
        window.print();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkHpp = document.getElementById('checkAdaHppPerorangan');
    const kid = document.getElementById('kredit_id')?.value || 'draft';
    const savedHpp = sessionStorage.getItem('hpp_perorangan_' + kid);
    let isUsaha = false;
    if (checkHpp) {
        isUsaha = checkHpp.checked;
    } else if (savedHpp !== null) {
        isUsaha = (savedHpp === '1');
    } else {
        isUsaha = (activeKategoriDebitur === 'usaha');
    }
    gantiKategoriDebitur(isUsaha ? 'usaha' : 'gaji', false);
    sinkronkanLaporanKeuangan();
});
</script>
