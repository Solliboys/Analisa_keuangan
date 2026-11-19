<?php
/**
 * Tab 10: Laporan Analisis Keuangan & Disbursement Debitur Perorangan
 * Sesuai Permintaan:
 * 1. Jika di Data Keuangan mencentang "Memiliki Pendapatan Lain / Usaha Sampingan", otomatis masuk kategori Punya Usaha (Wiraswasta).
 *    Jika tidak dicentang, otomatis masuk kategori Punya Gaji (Karyawan/PNS/Pensiun).
 * 2. Seluruh data (Pemasukan, Beban, Surplus, Harta, Utang, Angsuran, Rasio, Disbursement) 100% SINKRON dengan Data Keuangan (Tab 9).
 * 3. Harta Bersih TIDAK BOLEH mengambil nilai agunan/jaminan.
 * 4. Komponen Disbursement terintegrasi menjadi satu di dalam Laporan.
 */

// =============================================================================
// 1. DATA KREDIT & FASILITAS BARU YANG DIAJUKAN
// =============================================================================
$namaDebiturDisplay = !empty($kredit['nama_debitur']) ? esc($kredit['nama_debitur']) : 'Calon Debitur Perorangan';
$noAppDisplay       = !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-2024-AK8921';
$tahunPeriode       = date('Y');

$plafonBaru         = (float)($kredit['plafon_pengajuan'] ?? 50000000);
$tenorBulanBaru     = (int)($kredit['tenor_bulan'] ?? 12);
if ($tenorBulanBaru <= 0) $tenorBulanBaru = 12;
$bungaBulanPersen   = (float)($kredit['bunga_per_bulan'] ?? 1.25);
$angsuranPokokBaru  = ($plafonBaru > 0) ? round($plafonBaru / $tenorBulanBaru) : 0;
$angsuranBungaBaru  = ($plafonBaru > 0) ? round($plafonBaru * ($bungaBulanPersen / 100)) : 0;
$estimasiAngsuranBaru = $angsuranPokokBaru + $angsuranBungaBaru;

// =============================================================================
// 2. DATA SLIK OJK (Baki Debet & Angsuran Berjalan)
// =============================================================================
$totalOutstandingSlik = 0;
$totalCicilanSlik     = 0;
$rekapSlikFasilitas   = [];
$worstKolektibilitas  = 'Kol 1 (Lancar)';

if (!empty($slik) && is_array($slik)) {
    foreach ($slik as $s) {
        $out  = (float)($s['outstanding'] ?? ($s['baki_debet'] ?? 0));
        $angs = (float)($s['angsuran'] ?? ($s['angsuran_bulanan'] ?? 0));
        $kol  = trim($s['kelancaran'] ?? ($s['kolektibilitas'] ?? 'Kol 1'));
        if ($angs <= 0 && $out > 0) {
            $angs = round(($out / 36) + ($out * 0.0125));
        }
        $totalOutstandingSlik += $out;
        $totalCicilanSlik     += $angs;

        if (!empty($kol) && strpos(strtolower($kol), 'kol 1') === false && strpos(strtolower($kol), 'lancar') === false) {
            $worstKolektibilitas = $kol;
        }

        $rekapSlikFasilitas[] = [
            'bank'        => !empty($s['bank_lembaga']) ? $s['bank_lembaga'] : 'Lembaga Keuangan',
            'jenis'       => !empty($s['jenis_fasilitas']) ? $s['jenis_fasilitas'] : 'Fasilitas Kredit',
            'plafon'      => (float)($s['plafon'] ?? 0),
            'outstanding' => $out,
            'angsuran'    => $angs,
            'kol'         => $kol ?: 'Kol 1',
        ];
    }
}
if ($totalCicilanSlik <= 0) {
    $totalCicilanSlik = (float)($keuangan['angsuran_dipilih'] ?? ($keuangan['angsuran_installment'] ?? 0));
}
if ($totalOutstandingSlik <= 0 && $totalCicilanSlik > 0) {
    $totalOutstandingSlik = $totalCicilanSlik * 18;
}

// =============================================================================
// 3. DETEKSI STATUS CENTANG PENDAPATAN LAIN (TAB 9)
// =============================================================================
$adaPendapatanLainChecked = !empty($keuangan['ada_bukti_pendapatan_lain_rk']) || (!empty($keuangan['pendapatan_lain_rk']) && (float)$keuangan['pendapatan_lain_rk'] > 0);
$kategoriDebiturAwal = $adaPendapatanLainChecked ? 'usaha' : 'gaji';

// Pemasukan awal
$rawDetailPendapatan = $keuangan['detail_pendapatan_rk'] ?? '';
$listDetailPendapatan = [];
if (!empty($rawDetailPendapatan)) {
    $decP = json_decode($rawDetailPendapatan, true);
    if (is_array($decP) && count($decP) > 0) {
        $listDetailPendapatan = $decP;
    }
}
$totalPemasukanPokokAwal = 0;
if (!empty($listDetailPendapatan)) {
    foreach ($listDetailPendapatan as $p) {
        $totalPemasukanPokokAwal += (float)($p['nominal'] ?? 0);
    }
} else {
    $totalPemasukanPokokAwal = (float)($keuangan['pendapatan_rk'] ?? 0);
    $listDetailPendapatan = [['nama' => ($kategoriDebiturAwal === 'usaha' ? 'Omzet Penjualan' : 'Gaji Pokok'), 'nominal' => $totalPemasukanPokokAwal]];
}
$nomPendapatanLainAwal = $adaPendapatanLainChecked ? (float)($keuangan['pendapatan_lain_rk'] ?? 0) : 0;
$totalPemasukanAwal     = $totalPemasukanPokokAwal + $nomPendapatanLainAwal;

// Beban awal
$rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
$listDetailBeban = [];
if (!empty($rawDetailBeban)) {
    $decB = json_decode($rawDetailBeban, true);
    if (is_array($decB) && count($decB) > 0) {
        $listDetailBeban = $decB;
    }
}
$totalBebanAwal = 0;
if (!empty($listDetailBeban)) {
    foreach ($listDetailBeban as $b) {
        $totalBebanAwal += (float)($b['nominal'] ?? 0);
    }
} else {
    $totalBebanAwal = (float)($keuangan['beban_lainnya_nominal_rk'] ?? ($keuangan['biaya_hidup_nominal_rk'] ?? 0));
    $listDetailBeban = [['nama' => 'Pengeluaran Rutin & Kebutuhan Rumah Tangga', 'nominal' => $totalBebanAwal]];
}
$surplusBulananAwal = $totalPemasukanAwal - $totalBebanAwal;

// =============================================================================
// 4. DAFTAR HARTA / ASET DARI DATA KEUANGAN (TIDAK BOLEH DARI NILAI AGUNAN)
// =============================================================================
$rawDetailHarta = $keuangan['detail_harta_json'] ?? '';
$listDetailHarta = [];
if (!empty($rawDetailHarta)) {
    $decH = json_decode($rawDetailHarta, true);
    if (is_array($decH)) {
        if (isset($decH['daftar_harta']) && is_array($decH['daftar_harta'])) {
            $listDetailHarta = $decH['daftar_harta'];
        } elseif (isset($decH[0]['nama'])) {
            $listDetailHarta = $decH;
        }
    }
}
if (empty($listDetailHarta)) {
    $tabunganDb = (float)($keuangan['harta_tabungan'] ?? 0);
    $depositoDb = (float)($keuangan['harta_deposito'] ?? 0);
    $statusDepoDb = $keuangan['status_deposito'] ?? 'bebas';
    $tanahDb = (float)($keuangan['harta_tanah'] ?? 0);
    $statusTanahDb = $keuangan['status_tanah'] ?? 'bebas';
    $bangunanDb = (float)($keuangan['harta_bangunan'] ?? 0);
    $statusBangunanDb = $keuangan['status_bangunan'] ?? 'bebas';
    $kendaraanDb = (float)($keuangan['harta_kendaraan'] ?? 0);
    $statusKendaraanDb = $keuangan['status_kendaraan'] ?? 'bebas';

    $listDetailHarta = [
        ['nama' => 'Tabungan Bank', 'nominal' => $tabunganDb, 'status' => 'bebas'],
        ['nama' => 'Deposito Berjangka', 'nominal' => $depositoDb, 'status' => $statusDepoDb],
        ['nama' => 'Bangunan', 'nominal' => $bangunanDb, 'status' => $statusBangunanDb],
        ['nama' => 'Tanah', 'nominal' => $tanahDb, 'status' => $statusTanahDb],
        ['nama' => 'Kendaraan Pribadi', 'nominal' => $kendaraanDb, 'status' => $statusKendaraanDb],
    ];
}

$totalHartaBrutoAwal = 0;
$asetTerikatAwal     = 0;
foreach ($listDetailHarta as $h) {
    $nomH = (float)($h['nominal'] ?? 0);
    $totalHartaBrutoAwal += $nomH;
    if (($h['status'] ?? 'bebas') === 'terikat') {
        $asetTerikatAwal += $nomH;
    }
}
$totalHartaEligibleAwal = max(0, $totalHartaBrutoAwal - $asetTerikatAwal);

// =============================================================================
// 5. DAFTAR UTANG LUAR DARI DATA KEUANGAN & SLIK OJK
// =============================================================================
$listDetailUtang = [];
if (!empty($rawDetailHarta)) {
    $decH = json_decode($rawDetailHarta, true);
    if (is_array($decH) && isset($decH['daftar_utang']) && is_array($decH['daftar_utang'])) {
        $listDetailUtang = $decH['daftar_utang'];
    }
}
if (empty($listDetailUtang)) {
    $utangDagangDb = (float)($keuangan['neraca_utang_usaha'] ?? 0);
    $listDetailUtang = [
        ['nama' => 'Utang Dagang / Supplier', 'nominal' => $utangDagangDb, 'angsuran' => 0]
    ];
}

$subtotalUtangDinamisAwal = 0;
$subtotalAngsuranDinamisAwal = 0;
foreach ($listDetailUtang as $u) {
    $subtotalUtangDinamisAwal    += (float)($u['nominal'] ?? 0);
    $subtotalAngsuranDinamisAwal += (float)($u['angsuran'] ?? 0);
}

$totalUtangLuarAwal        = $subtotalUtangDinamisAwal + $totalOutstandingSlik;
$totalAngsuranBerjalanAwal  = $subtotalAngsuranDinamisAwal + $totalCicilanSlik;
$totalAngsuranGabunganAwal  = $totalAngsuranBerjalanAwal + $estimasiAngsuranBaru;

// =============================================================================
// 6. KEKAYAAN BERSIH & RASIO KEUANGAN
// =============================================================================
$kekayaanBersihAwal = $totalHartaEligibleAwal - $totalUtangLuarAwal;
$dscrAwal = ($totalAngsuranGabunganAwal > 0) ? round($surplusBulananAwal / $totalAngsuranGabunganAwal, 2) : 0;
$dsrAwal  = ($totalPemasukanAwal > 0) ? round(($totalAngsuranGabunganAwal / $totalPemasukanAwal) * 100, 1) : 0;
$darAwal  = ($totalHartaEligibleAwal > 0) ? round(($totalUtangLuarAwal / $totalHartaEligibleAwal) * 100, 1) : 0;
$derAwal  = ($kekayaanBersihAwal > 0) ? round($totalUtangLuarAwal / $kekayaanBersihAwal, 2) : 0;

// =============================================================================
// 7. DATA DISBURSEMENT (PENCAIRAN BERSIH)
// =============================================================================
$biayaLainList = [];
if (!empty($disbursement['rincian_biaya_lain'])) {
    $decoded = json_decode($disbursement['rincian_biaya_lain'], true);
    if (is_array($decoded)) {
        $biayaLainList = $decoded;
    }
}
$provisiPersen = (float)($disbursement['provisi_persen'] ?? 7);
$adminPersen   = (float)($disbursement['admin_persen'] ?? 1);
$nomProvisiAwal = round($plafonBaru * ($provisiPersen / 100));
$nomAdminAwal   = round($plafonBaru * ($adminPersen / 100));

$totalBiayaLainAwal = 0;
foreach ($biayaLainList as $b) {
    $totalBiayaLainAwal += (float)($b['nominal'] ?? 0);
}
$totalPotonganMukaAwal = $nomProvisiAwal + $nomAdminAwal + $totalBiayaLainAwal;
$danaBersihNettAwal    = max(0, $plafonBaru - $totalPotonganMukaAwal);
?>

<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-8">
    
    <!-- =========================================================================
         HEADER SECTION & AKSI SINKRONISASI
    ========================================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                10
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">LAPORAN ANALISIS KEUANGAN LENGKAP</h3>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Format laporan dan seluruh angka terhubung otomatis secara real-time dengan isian pada Data Keuangan (Tab 9)</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <button type="button" onclick="sinkronkanLaporanKeuangan()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-2xs cursor-pointer" title="Perbarui data langsung dari Tab 9">
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
         TOP EXECUTIVE SUMMARY: 4 KARTU METRIK UTAMA (SINKRON DATA KEUANGAN)
    ========================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Pemasukan Bulanan (Biru) -->
        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-200 flex flex-col justify-between shadow-2xs">
            <span class="text-[10px] font-extrabold text-blue-900 uppercase tracking-wider" id="label_kpi_pemasukan">
                <?= $kategoriDebiturAwal === 'usaha' ? 'TOTAL OMZET & PEMASUKAN' : 'TOTAL GAJI & PENGHASILAN' ?>
            </span>
            <span id="metric_lap_pemasukan" class="text-xl sm:text-2xl font-black text-blue-700 tracking-tight block mt-1">
                Rp <?= number_format($totalPemasukanAwal, 0, ',', '.') ?>
            </span>
            <span class="text-[11px] text-blue-800/80 font-medium mt-1 block" id="sub_kpi_pemasukan">
                <?= $kategoriDebiturAwal === 'usaha' ? 'Omzet pokok + pendapatan sampingan' : 'Gaji pokok + tunjangan + tambahan' ?>
            </span>
        </div>

        <!-- 2. Total Pengeluaran / Beban Bulanan (Merah) -->
        <div class="p-4 bg-rose-50/70 rounded-2xl border border-rose-200 flex flex-col justify-between shadow-2xs">
            <span class="text-[10px] font-extrabold text-rose-900 uppercase tracking-wider" id="label_kpi_beban">
                <?= $kategoriDebiturAwal === 'usaha' ? 'TOTAL BEBAN USAHA & RT' : 'TOTAL BIAYA HIDUP & RUTIN' ?>
            </span>
            <span id="metric_lap_beban" class="text-xl sm:text-2xl font-black text-rose-700 tracking-tight block mt-1">
                Rp <?= number_format($totalBebanAwal, 0, ',', '.') ?>
            </span>
            <span class="text-[11px] text-rose-800/80 font-medium mt-1 block">Akumulasi seluruh beban dinamis</span>
        </div>

        <!-- 3. Sisa Penghasilan / Surplus Bulanan (Hijau) -->
        <div class="p-4 bg-emerald-50/70 rounded-2xl border border-emerald-200 flex flex-col justify-between shadow-2xs">
            <span class="text-[10px] font-extrabold text-emerald-900 uppercase tracking-wider">SURPLUS BULANAN (SISA)</span>
            <span id="metric_lap_surplus" class="text-xl sm:text-2xl font-black text-emerald-700 tracking-tight block mt-1">
                Rp <?= number_format($surplusBulananAwal, 0, ',', '.') ?>
            </span>
            <span class="text-[11px] text-emerald-800/80 font-medium mt-1 block">Kapasitas bayar angsuran bulanan</span>
        </div>

        <!-- 4. Kekayaan Bersih / Harta Bersih (Ungu/Indigo) -->
        <div class="p-4 bg-indigo-50/70 rounded-2xl border border-indigo-200 flex flex-col justify-between shadow-2xs">
            <span class="text-[10px] font-extrabold text-indigo-900 uppercase tracking-wider">KEKAYAAN BERSIH (NET WORTH)</span>
            <span id="metric_lap_net_worth" class="text-xl sm:text-2xl font-black text-indigo-700 tracking-tight block mt-1">
                Rp <?= number_format($kekayaanBersihAwal, 0, ',', '.') ?>
            </span>
            <span class="text-[11px] text-indigo-800/80 font-medium mt-1 block">Harta eligible dikurangi seluruh utang</span>
        </div>
    </div>

    <!-- =========================================================================
         BAGIAN 1: LAPORAN PENDAPATAN & BIAYA BULANAN (SINKRON PENUH DARI TAB 9)
    ========================================================================== -->
    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-200/90 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                    1
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide" id="title_bagian_1">
                        <?= $kategoriDebiturAwal === 'usaha' ? 'LAPORAN PENDAPATAN & BIAYA USAHA (WIRASWASTA)' : 'LAPORAN PENGHASILAN & BIAYA HIDUP (BERPENGHASILAN TETAP)' ?>
                    </h4>
                    <p class="text-[11px] text-slate-500">Rincian seluruh arus kas masuk dan beban pengeluaran rutin dari Data Keuangan</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                Data Keuangan Valid
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kolom Pemasukan (Biru) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-900 uppercase tracking-wider flex items-center gap-1.5" id="label_col_pemasukan">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <?= $kategoriDebiturAwal === 'usaha' ? 'Rincian Penjualan / Omzet' : 'Rincian Penghasilan / Gaji' ?>
                    </span>
                    <span class="text-[11px] font-bold text-blue-700" id="lap_badge_tot_pemasukan">Rp <?= number_format($totalPemasukanAwal, 0, ',', '.') ?></span>
                </div>
                <div class="border border-blue-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-blue-50/70 border-b border-blue-100 text-blue-900 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-2.5 px-3">Komponen Pendapatan</th>
                                <th class="py-2.5 px-3 text-right">Nominal (Rp/Bulan)</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_lap_pemasukan" class="divide-y divide-slate-100">
                            <!-- Diisi dinamis oleh sinkronkanLaporanKeuangan() -->
                            <?php foreach ($listDetailPendapatan as $p): ?>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800"><?= esc($p['nama'] ?: 'Pendapatan Pokok') ?></td>
                                    <td class="py-2.5 px-3 text-right font-bold text-blue-700">Rp <?= number_format((float)($p['nominal'] ?? 0), 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($nomPendapatanLainAwal > 0): ?>
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-slate-800">Pendapatan Sampingan / Lainnya</td>
                                <td class="py-2.5 px-3 text-right font-bold text-blue-700">Rp <?= number_format($nomPendapatanLainAwal, 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-blue-50/80 font-bold border-t border-blue-200 text-blue-950">
                                <td class="py-2.5 px-3">TOTAL PEMASUKAN</td>
                                <td class="py-2.5 px-3 text-right text-blue-700 font-black text-sm" id="lap_tfoot_tot_pemasukan">Rp <?= number_format($totalPemasukanAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Kolom Pengeluaran / Beban (Merah) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-900 uppercase tracking-wider flex items-center gap-1.5" id="label_col_beban">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        <?= $kategoriDebiturAwal === 'usaha' ? 'Rincian Beban Usaha & Rumah Tangga' : 'Rincian Pengeluaran & Biaya Hidup' ?>
                    </span>
                    <span class="text-[11px] font-bold text-rose-700" id="lap_badge_tot_beban">Rp <?= number_format($totalBebanAwal, 0, ',', '.') ?></span>
                </div>
                <div class="border border-rose-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-rose-50/70 border-b border-rose-100 text-rose-900 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-2.5 px-3">Komponen Beban</th>
                                <th class="py-2.5 px-3 text-right">Nominal (Rp/Bulan)</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_lap_beban" class="divide-y divide-slate-100">
                            <!-- Diisi dinamis oleh sinkronkanLaporanKeuangan() -->
                            <?php foreach ($listDetailBeban as $b): ?>
                                <tr>
                                    <td class="py-2.5 px-3 font-semibold text-slate-800"><?= esc($b['nama'] ?: 'Beban Hidup') ?></td>
                                    <td class="py-2.5 px-3 text-right font-bold text-rose-600">Rp <?= number_format((float)($b['nominal'] ?? 0), 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-rose-50/80 font-bold border-t border-rose-200 text-rose-950">
                                <td class="py-2.5 px-3">TOTAL PENGELUARAN</td>
                                <td class="py-2.5 px-3 text-right text-rose-700 font-black text-sm" id="lap_tfoot_tot_beban">Rp <?= number_format($totalBebanAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bar Surplus / Sisa Penghasilan Bersih Bulanan -->
        <div class="p-4 bg-emerald-600 text-white rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div>
                <div class="text-[10px] font-black uppercase tracking-wider text-emerald-200">SISA PENGHASILAN BERSIH / SURPLUS BULANAN</div>
                <div class="text-xs font-semibold text-white">Total Pemasukan dikurangi Total Seluruh Beban (Kapasitas Pembayaran)</div>
            </div>
            <div class="text-right">
                <span id="lap_surplus_nominal" class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Rp <?= number_format($surplusBulananAwal, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-emerald-100 block">per bulan</span>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         BAGIAN 2: DAFTAR HARTA (ASET) - BEBAS JAMINAN vs TERIKAT JAMINAN
    ========================================================================== -->
    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-200/90 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                    2
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">DAFTAR HARTA / ASET PEMOHON</h4>
                    <p class="text-[11px] text-slate-500">Daftar harta langsung diambil dari Data Keuangan (Agunan jaminan pihak ketiga tidak dimasukkan)</p>
                </div>
            </div>
            <div class="p-2 bg-amber-50 border border-amber-200 rounded-xl text-[10px] text-amber-800 font-medium max-w-md">
                <strong>Aturan Harta Bersih:</strong> Aset yang berstatus jaminan / belum tentu kepemilikannya otomatis dikecualikan dari Harta Bersih.
            </div>
        </div>

        <div class="border border-emerald-200 rounded-2xl overflow-hidden bg-white shadow-2xs">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-emerald-50/80 border-b border-emerald-200 text-emerald-950 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Harta / Aset</th>
                        <th class="py-3 px-4 text-center">Status Kepemilikan</th>
                        <th class="py-3 px-4 text-right">Nilai Pasar / Aset (Rp)</th>
                    </tr>
                </thead>
                <tbody id="tbody_lap_harta" class="divide-y divide-slate-100">
                    <!-- Diisi dinamis oleh sinkronkanLaporanKeuangan() -->
                    <?php $noH = 1; foreach ($listDetailHarta as $h): ?>
                        <?php 
                            $isTerikat = (($h['status'] ?? 'bebas') === 'terikat');
                            $nomH = (float)($h['nominal'] ?? 0);
                        ?>
                        <tr class="<?= $isTerikat ? 'bg-amber-50/40 text-slate-500' : '' ?>">
                            <td class="py-2.5 px-4 text-center font-bold text-slate-500"><?= $noH++ ?></td>
                            <td class="py-2.5 px-4 font-semibold text-slate-800">
                                <?= esc($h['nama'] ?: 'Aset') ?>
                                <?php if ($isTerikat): ?>
                                    <span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Dijaminkan / Terikat</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-4 text-center font-bold">
                                <?php if ($isTerikat): ?>
                                    <span class="text-amber-700">Terikat Jaminan</span>
                                <?php else: ?>
                                    <span class="text-emerald-700">Bebas Jaminan (Eligible)</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-4 text-right font-bold <?= $isTerikat ? 'text-amber-800' : 'text-slate-800' ?>">
                                Rp <?= number_format($nomH, 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Rekapitulasi Posisi Harta -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-emerald-50/50 rounded-2xl border border-emerald-200 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Harta Bruto:</span>
                <span id="lap_tot_harta_bruto" class="text-base font-extrabold text-slate-800">Rp <?= number_format($totalHartaBrutoAwal, 0, ',', '.') ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">(-) Aset Terikat Jaminan:</span>
                <span id="lap_tot_aset_terikat" class="text-base font-extrabold text-amber-700">(Rp <?= number_format($asetTerikatAwal, 0, ',', '.') ?>)</span>
            </div>
            <div class="sm:text-right">
                <span class="text-[10px] font-black text-emerald-900 uppercase tracking-wider block">= TOTAL HARTA BERSIH ELIGIBLE:</span>
                <span id="lap_tot_harta_eligible" class="text-lg font-black text-emerald-700">Rp <?= number_format($totalHartaEligibleAwal, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         BAGIAN 3: DAFTAR UTANG LUAR & TAMBAHAN UTANG BARU
    ========================================================================== -->
    <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-200/90 space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                    3
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">DAFTAR UTANG LUAR &amp; TAMBAHAN UTANG BARU</h4>
                    <p class="text-[11px] text-slate-500">Kewajiban utang berjalan (Data Keuangan + SLIK OJK) dan fasilitas kredit baru yang diajukan</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                Komprehensif SLIK
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- A. Tabel Utang Luar Berjalan -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">A. Utang Luar Berjalan (Data Keuangan &amp; SLIK)</span>
                    <span id="lap_badge_tot_utang_luar" class="text-[11px] font-bold text-rose-700">Rp <?= number_format($totalUtangLuarAwal, 0, ',', '.') ?></span>
                </div>
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-rose-50/70 border-b border-rose-100 text-rose-900 font-bold uppercase text-[10px] tracking-wider">
                                <th class="py-2.5 px-3">Nama Kreditur / Fasilitas</th>
                                <th class="py-2.5 px-3 text-right">Sisa Utang (Pokok)</th>
                                <th class="py-2.5 px-3 text-right">Angsuran/Bln</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_lap_utang_luar" class="divide-y divide-slate-100">
                            <!-- Diisi dinamis oleh sinkronkanLaporanKeuangan() -->
                            <?php foreach ($listDetailUtang as $u): ?>
                                <tr>
                                    <td class="py-2 px-3 font-semibold text-slate-800"><?= esc($u['nama'] ?: 'Utang Lainnya') ?></td>
                                    <td class="py-2 px-3 text-right font-bold text-slate-700">Rp <?= number_format((float)($u['nominal'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-2 px-3 text-right font-bold text-rose-600">Rp <?= number_format((float)($u['angsuran'] ?? 0), 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!empty($rekapSlikFasilitas)): ?>
                                <?php foreach ($rekapSlikFasilitas as $s): ?>
                                    <tr class="bg-slate-50/60">
                                        <td class="py-2 px-3 font-medium text-slate-700">
                                            <?= esc($s['bank']) ?> - <?= esc($s['jenis']) ?>
                                            <span class="block text-[9px] text-slate-400 font-mono"><?= esc($s['kol']) ?></span>
                                        </td>
                                        <td class="py-2 px-3 text-right font-bold text-slate-800">Rp <?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?></td>
                                        <td class="py-2 px-3 text-right font-bold text-rose-600">Rp <?= number_format((float)($s['angsuran'] ?? 0), 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-rose-50/80 font-bold border-t border-rose-200 text-rose-950">
                                <td class="py-2.5 px-3">SUBTOTAL UTANG LUAR</td>
                                <td class="py-2.5 px-3 text-right text-rose-800 font-extrabold" id="lap_tfoot_sub_utang_luar">Rp <?= number_format($totalUtangLuarAwal, 0, ',', '.') ?></td>
                                <td class="py-2.5 px-3 text-right text-rose-700 font-black text-xs" id="lap_tfoot_sub_angs_luar">Rp <?= number_format($totalAngsuranBerjalanAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- B. Kartu Fasilitas Kredit Baru yang Diajukan -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">B. Tambahan Utang Baru (Fasilitas Diajukan)</span>
                    <span class="text-[11px] font-bold text-blue-700">Permohonan Baru</span>
                </div>
                <div class="p-5 bg-gradient-to-br from-blue-50/90 to-indigo-50/70 rounded-2xl border border-blue-200/90 space-y-4 shadow-2xs">
                    <div class="flex items-center justify-between border-b border-blue-200/60 pb-3">
                        <span class="text-xs font-bold text-slate-700">Plafon Pinjaman Baru:</span>
                        <span id="lap_baru_plafon" class="text-base font-black text-blue-700">Rp <?= number_format($plafonBaru, 0, ',', '.') ?></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 bg-white/80 rounded-xl border border-blue-100">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block mb-0.5">Jangka Waktu:</span>
                            <span id="lap_baru_tenor" class="font-extrabold text-slate-800"><?= $tenorBulanBaru ?> Bulan</span>
                        </div>
                        <div class="p-2.5 bg-white/80 rounded-xl border border-blue-100">
                            <span class="text-[10px] text-slate-400 font-bold uppercase block mb-0.5">Suku Bunga:</span>
                            <span id="lap_baru_bunga" class="font-extrabold text-slate-800"><?= $bungaBulanPersen ?>% / Bulan</span>
                        </div>
                    </div>
                    <div class="p-3 bg-blue-600 text-white rounded-xl flex items-center justify-between shadow-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block">Estimasi Angsuran Baru:</span>
                            <span class="text-[11px] text-blue-100">Pokok + Bunga per bulan</span>
                        </div>
                        <span id="lap_baru_angs_nom" class="text-lg font-black text-white">Rp <?= number_format($estimasiAngsuranBaru, 0, ',', '.') ?></span>
                    </div>
                </div>

                <!-- Total Kewajiban Angsuran Gabungan -->
                <div class="p-4 bg-slate-900 text-white rounded-2xl flex items-center justify-between shadow-xs">
                    <div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL KEWAJIBAN ANGSURAN GABUNGAN</div>
                        <div class="text-xs font-semibold text-slate-200">Angsuran Berjalan + Angsuran Baru</div>
                    </div>
                    <div class="text-right">
                        <span id="lap_tot_angs_gabungan" class="text-xl sm:text-2xl font-black text-amber-400 tracking-tight">
                            Rp <?= number_format($totalAngsuranGabunganAwal, 0, ',', '.') ?>
                        </span>
                        <span class="text-[10px] text-slate-300 block">per bulan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         BAGIAN 4: REKAPITULASI KEKAYAAN BERSIH & RASIO-RASIO KEUANGAN RELEVAN
    ========================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kalkulasi Kekayaan Bersih (Harta Bersih) -->
        <div class="p-6 bg-slate-50/70 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                        4
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">POSISI KEKAYAAN BERSIH (HARTA BERSIH)</h4>
                        <p class="text-[11px] text-slate-500">Total Harta Bersih Eligible dikurangi Total Seluruh Utang Luar</p>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center p-3 bg-white border border-slate-200 rounded-xl">
                        <span class="font-semibold text-slate-600">Total Harta Bersih Eligible (Aset Bebas):</span>
                        <span id="rekap_lap_harta_eligible" class="font-extrabold text-emerald-700 text-sm">Rp <?= number_format($totalHartaEligibleAwal, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white border border-slate-200 rounded-xl">
                        <span class="font-semibold text-slate-600">(-) Total Seluruh Utang Luar:</span>
                        <span id="rekap_lap_utang_luar" class="font-extrabold text-rose-600 text-sm">(Rp <?= number_format($totalUtangLuarAwal, 0, ',', '.') ?>)</span>
                    </div>
                </div>
            </div>

            <!-- Banner Highlight Kekayaan Bersih -->
            <div class="mt-6 p-5 bg-gradient-to-r from-indigo-700 to-blue-700 text-white rounded-2xl shadow-xs">
                <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-200">TOTAL POSISI KEKAYAAN BERSIH (NET WORTH)</div>
                <div class="mt-1 flex justify-between items-baseline">
                    <span class="text-xs text-indigo-100 font-semibold">Kekayaan Bersih:</span>
                    <span id="rekap_lap_net_worth_bold" class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Rp <?= number_format($kekayaanBersihAwal, 0, ',', '.') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Rasio-Rasio Keuangan Relevan -->
        <div class="p-6 bg-slate-50/70 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            %
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wide">RASIO-RASIO KEUANGAN RELEVAN</h4>
                            <p class="text-[11px] text-slate-500">Indikator kemampuan bayar &amp; kesehatan finansial pemohon</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Analisa Kelayakan</span>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <!-- DSCR -->
                    <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">DSCR (Coverage Ratio)</span>
                        <div class="flex items-baseline justify-between">
                            <span id="rasio_lap_dscr" class="text-lg font-black text-emerald-700"><?= $dscrAwal ?>x</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Min 1.30x</span>
                        </div>
                        <span class="text-[9px] text-slate-400 block">Sisa / Total Angsuran</span>
                    </div>

                    <!-- DSR / DBR -->
                    <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">DSR / DBR (Debt Service)</span>
                        <div class="flex items-baseline justify-between">
                            <span id="rasio_lap_dsr" class="text-lg font-black text-blue-700"><?= $dsrAwal ?>%</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Maks 35-40%</span>
                        </div>
                        <span class="text-[9px] text-slate-400 block">Angsuran / Pemasukan</span>
                    </div>

                    <!-- DAR -->
                    <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">DAR (Debt to Asset)</span>
                        <div class="flex items-baseline justify-between">
                            <span id="rasio_lap_dar" class="text-lg font-black text-slate-800"><?= $darAwal ?>%</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Maks 50%</span>
                        </div>
                        <span class="text-[9px] text-slate-400 block">Total Utang / Harta</span>
                    </div>

                    <!-- DER -->
                    <div class="p-3 bg-white border border-slate-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">DER (Debt to Equity)</span>
                        <div class="flex items-baseline justify-between">
                            <span id="rasio_lap_der" class="text-lg font-black text-slate-800"><?= $derAwal ?>x</span>
                            <span class="text-[10px] text-slate-500 font-semibold">Wajar</span>
                        </div>
                        <span class="text-[9px] text-slate-400 block">Utang / Kekayaan Bersih</span>
                    </div>
                </div>
            </div>

            <div id="boxStatusKelayakanRasio" class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800 font-medium">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="labelStatusKelayakanRasio">Rasio keuangan debitur dalam batas wajar dan memiliki kapasitas pembayaran yang memadai.</span>
            </div>
        </div>
    </div>

</div>

<!-- Script Library html2pdf.js untuk cetak & unduh PDF instan -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<!-- =============================================================================
     MODAL PREVIEW & CETAK LEMBAR A4 RESMI LAPORAN PERORANGAN
============================================================================== -->
<div id="modalPreviewLaporanKeuangan" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="bg-slate-100 rounded-3xl shadow-2xl border border-slate-300 max-w-4xl w-full max-h-[96vh] flex flex-col overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Toolbar Header -->
        <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between gap-4 shrink-0 shadow-2xs no-print">
            <div class="flex items-center gap-3">
                <button type="button" onclick="tutupModalPreviewLaporan()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl transition border border-slate-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </button>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-xs font-bold text-slate-800" id="doc_preview_filename">Laporan_Keuangan_Perorangan.pdf</span>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <span class="text-xs text-slate-400 font-mono hidden sm:inline">A4 Portrait (210 × 297mm)</span>
                
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

        <!-- Sheet Kertas A4 White Document Preview -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex justify-center bg-slate-200/70" id="printSheetContainer">
            <div id="printSheetLaporan" class="bg-white w-full max-w-[760px] p-8 sm:p-12 shadow-xl border border-slate-200/90 text-slate-800 space-y-6 text-xs rounded-sm">
                
                <!-- Header Dokumen Cetak -->
                <div class="flex items-center justify-between text-xs text-slate-500 font-semibold border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-1.5">
                        <span class="tracking-wider uppercase font-bold text-blue-900 text-[11px]" id="doc_header_entity">
                            ANALISA KEUANGAN DEBITUR PERORANGAN (<?= $kategoriDebiturAwal === 'usaha' ? 'WIRASWASTA' : 'BERPENGHASILAN TETAP' ?>)
                        </span>
                    </div>
                    <span class="font-mono text-slate-500 text-[11px]">NO APLIKASI: <?= $noAppDisplay ?></span>
                </div>

                <!-- Judul Laporan -->
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight" id="doc_title_category">
                        <?= $kategoriDebiturAwal === 'usaha' ? 'LAPORAN KEUANGAN USAHA (WIRASWASTA)' : 'LAPORAN PENGHASILAN & BIAYA HIDUP' ?>
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Nama Debitur: <strong><?= $namaDebiturDisplay ?></strong> &bull; Periode Evaluasi: <?= $tahunPeriode ?></p>
                </div>

                <!-- 1. DATA PENDAPATAN & BIAYA -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between bg-blue-50/80 px-3 py-1.5 text-blue-950 font-bold text-xs uppercase border border-blue-200">
                        <span id="doc_title_sec1"><?= $kategoriDebiturAwal === 'usaha' ? '1. LAPORAN PENDAPATAN & BIAYA USAHA' : '1. LAPORAN PENGHASILAN & BIAYA HIDUP' ?></span>
                        <span class="text-[10px] text-blue-800 font-semibold">(IDR / Bulan)</span>
                    </div>
                    <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700" id="doc_label_pemasukan">
                                    <?= $kategoriDebiturAwal === 'usaha' ? 'Total Penjualan / Omzet (Pokok + Sampingan)' : 'Total Penghasilan / Gaji (Pokok + Tambahan)' ?>
                                </td>
                                <td class="py-1.5 px-3 text-right font-bold text-blue-700" id="doc_pemasukan">Rp <?= number_format($totalPemasukanAwal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700" id="doc_label_beban">
                                    <?= $kategoriDebiturAwal === 'usaha' ? '(-) Total Biaya Operasional & Rumah Tangga' : '(-) Total Pengeluaran & Biaya Hidup' ?>
                                </td>
                                <td class="py-1.5 px-3 text-right font-bold text-rose-600" id="doc_beban">(Rp <?= number_format($totalBebanAwal, 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="bg-emerald-50 font-bold text-emerald-950">
                                <td class="py-2 px-3 uppercase">= Sisa Penghasilan Bersih / Surplus Bulanan</td>
                                <td class="py-2 px-3 text-right font-black text-emerald-800 text-sm" id="doc_surplus">Rp <?= number_format($surplusBulananAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. DATA HARTA / ASET ELIGIBLE -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between bg-emerald-50/80 px-3 py-1.5 text-emerald-950 font-bold text-xs uppercase border border-emerald-200">
                        <span>2. DAFTAR HARTA / ASET PEMOHON (BEBAS JAMINAN)</span>
                        <span class="text-[10px] text-emerald-800 font-semibold">Bebas Jaminan</span>
                    </div>
                    <table class="w-full text-left text-xs border border-emerald-100 border-collapse">
                        <thead>
                            <tr class="bg-emerald-100/40 text-[10px] uppercase font-bold text-slate-600 border-b border-emerald-100">
                                <th class="py-1.5 px-3 font-bold">Aset / Harta</th>
                                <th class="py-1.5 px-3 text-center font-bold">Status</th>
                                <th class="py-1.5 px-3 text-right font-bold">Nilai (IDR)</th>
                            </tr>
                        </thead>
                        <tbody id="doc_tbody_harta" class="divide-y divide-slate-100">
                            <!-- Diisi dinamis -->
                        </tbody>
                        <tfoot>
                            <tr class="bg-emerald-50 font-bold text-emerald-950 border-t border-emerald-200">
                                <td class="py-1.5 px-3" colspan="2">TOTAL HARTA BERSIH ELIGIBLE</td>
                                <td class="py-1.5 px-3 text-right text-emerald-800 font-black" id="doc_tot_harta_eligible">Rp <?= number_format($totalHartaEligibleAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 3. DATA UTANG LUAR & TAMBAHAN UTANG BARU -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between bg-rose-50/80 px-3 py-1.5 text-rose-950 font-bold text-xs uppercase border border-rose-200">
                        <span>3. UTANG LUAR &amp; FASILITAS KREDIT BARU</span>
                        <span class="text-[10px] text-rose-800 font-semibold">Kewajiban Riil</span>
                    </div>
                    <table class="w-full text-left text-xs border border-rose-100 border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700">Total Utang Luar Berjalan (Data Keuangan + SLIK)</td>
                                <td class="py-1.5 px-3 text-right font-bold text-rose-600" id="doc_utang_luar">Rp <?= number_format($totalUtangLuarAwal, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700">Total Angsuran Utang Berjalan per Bulan</td>
                                <td class="py-1.5 px-3 text-right font-bold text-amber-700" id="doc_angs_berjalan">Rp <?= number_format($totalAngsuranBerjalanAwal, 0, ',', '.') ?>/bln</td>
                            </tr>
                            <tr class="bg-blue-50/50">
                                <td class="py-1.5 px-3 font-semibold text-blue-900">Tambahan Plafon Kredit Baru yang Diajukan</td>
                                <td class="py-1.5 px-3 text-right font-bold text-blue-800" id="doc_plafon_baru">Rp <?= number_format($plafonBaru, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="bg-blue-50/50">
                                <td class="py-1.5 px-3 font-semibold text-blue-900">Estimasi Angsuran Fasilitas Kredit Baru per Bulan</td>
                                <td class="py-1.5 px-3 text-right font-bold text-blue-800" id="doc_angs_baru">Rp <?= number_format($estimasiAngsuranBaru, 0, ',', '.') ?>/bln</td>
                            </tr>
                            <tr class="bg-slate-900 text-white font-bold">
                                <td class="py-2 px-3 uppercase text-slate-200">TOTAL KEWAJIBAN ANGSURAN GABUNGAN / BULAN</td>
                                <td class="py-2 px-3 text-right text-amber-400 font-black text-sm" id="doc_tot_angs_gabungan">Rp <?= number_format($totalAngsuranGabunganAwal, 0, ',', '.') ?>/bln</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 4. KEKAYAAN BERSIH & RASIO KEUANGAN -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between bg-indigo-50/80 px-3 py-1.5 text-indigo-950 font-bold text-xs uppercase border border-indigo-200">
                        <span>4. REKAPITULASI KEKAYAAN BERSIH &amp; RASIO KEUANGAN</span>
                        <span class="text-[10px] text-indigo-800 font-semibold">Hasil Analisa</span>
                    </div>
                    <table class="w-full text-left text-xs border border-indigo-100 border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-indigo-100/50 font-bold">
                                <td class="py-2 px-3 text-indigo-950 font-black">KEKAYAAN BERSIH (HARTA BERSIH - UTANG LUAR)</td>
                                <td class="py-2 px-3 text-right text-indigo-900 font-black text-sm" id="doc_kekayaan_bersih">Rp <?= number_format($kekayaanBersihAwal, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="text-[11px] bg-slate-50">
                                <td class="py-1.5 px-3 text-slate-700">
                                    DSCR: <strong class="text-emerald-700" id="doc_dscr"><?= $dscrAwal ?>x</strong> (Standar >= 1.30x) &bull; 
                                    DSR: <strong class="text-blue-700" id="doc_dsr"><?= $dsrAwal ?>%</strong> (Maks 40%)
                                </td>
                                <td class="py-1.5 px-3 text-right text-slate-700">
                                    DAR: <strong class="text-slate-800" id="doc_dar"><?= $darAwal ?>%</strong> &bull; 
                                    DER: <strong class="text-slate-800" id="doc_der"><?= $derAwal ?>x</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 5. REKAPITULASI PENCAIRAN BERSIH (DISBURSEMENT) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between bg-blue-50/80 px-3 py-1.5 text-blue-950 font-bold text-xs uppercase border border-blue-200">
                        <span>5. SIMULASI PENCAIRAN BERSIH (DISBURSEMENT)</span>
                        <span class="text-[10px] text-blue-800 font-semibold">Akad Kredit</span>
                    </div>
                    <table class="w-full text-left text-xs border border-blue-100 border-collapse">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700">Plafon Pinjaman Pokok</td>
                                <td class="py-1.5 px-3 text-right font-bold text-slate-800" id="doc_disb_plafon">Rp <?= number_format($plafonBaru, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 font-semibold text-slate-700">(-) Total Biaya Potongan Muka (Provisi, Admin &amp; Lainnya)</td>
                                <td class="py-1.5 px-3 text-right font-bold text-rose-600" id="doc_disb_potongan">- Rp <?= number_format($totalPotonganMukaAwal, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="bg-blue-600 text-white font-bold">
                                <td class="py-2 px-3 uppercase text-blue-100">ESTIMASI DANA BERSIH YANG DITERIMA DEBITUR (NETT)</td>
                                <td class="py-2 px-3 text-right text-white font-black text-sm sm:text-base" id="doc_disb_nett">Rp <?= number_format($danaBersihNettAwal, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Tanda Tangan Cetak -->
                <div class="pt-6 border-t border-slate-200 grid grid-cols-2 text-center text-xs text-slate-600">
                    <div class="space-y-12">
                        <span>Disusun oleh (Account Officer):</span>
                        <span class="font-bold text-slate-800 block underline">( ............................................ )</span>
                    </div>
                    <div class="space-y-12">
                        <span>Disetujui oleh (Komite Kredit):</span>
                        <span class="font-bold text-slate-800 block underline">( ............................................ )</span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
/* CSS Print Native Browser */
@media print {
    body * {
        visibility: hidden !important;
    }
    #modalPreviewLaporanKeuangan,
    #modalPreviewLaporanKeuangan #printSheetLaporan,
    #modalPreviewLaporanKeuangan #printSheetLaporan * {
        visibility: visible !important;
    }
    #modalPreviewLaporanKeuangan {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100vw !important;
        height: auto !important;
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
        z-index: 99999 !important;
        overflow: visible !important;
    }
    #modalPreviewLaporanKeuangan .no-print,
    #modalPreviewLaporanKeuangan > div > div:first-child {
        display: none !important;
    }
    #modalPreviewLaporanKeuangan > div {
        max-width: 100% !important;
        width: 100% !important;
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #modalPreviewLaporanKeuangan #printSheetLaporan {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 10mm 15mm !important;
        box-shadow: none !important;
        border: none !important;
    }
}
</style>

<!-- =============================================================================
     LOGIKA JAVASCRIPT: SWITCH KATEGORI & SINKRONISASI 100% DENGAN TAB 9
============================================================================== -->
<script>
let activeKategoriDebitur = '<?= $kategoriDebiturAwal ?>';

// Format Angka ke Rupiah String
function fNomLap(num) {
    const val = Math.round(Number(num) || 0);
    return 'Rp ' + val.toLocaleString('id-ID');
}

// Parse Rupiah String ke Numeric
function pNomLap(val) {
    if (val === null || val === undefined) return 0;
    if (typeof val === 'number') return isNaN(val) ? 0 : val;
    const clean = String(val).replace(/[^0-9]/g, '');
    return clean ? parseInt(clean, 10) : 0;
}

// Ganti Kategori Debitur (Punya Usaha vs Punya Gaji)
function gantiKategoriDebitur(kategori) {
    activeKategoriDebitur = kategori;
    const btnUsaha = document.getElementById('btnKategoriUsaha');
    const btnGaji = document.getElementById('btnKategoriGaji');
    const badge = document.getElementById('badgeKategoriAktif');

    const labelKpiPemasukan = document.getElementById('label_kpi_pemasukan');
    const subKpiPemasukan = document.getElementById('sub_kpi_pemasukan');
    const labelKpiBeban = document.getElementById('label_kpi_beban');
    const titleSec1 = document.getElementById('title_bagian_1');
    const labelColPemasukan = document.getElementById('label_col_pemasukan');
    const labelColBeban = document.getElementById('label_col_beban');

    const docHeaderEntity = document.getElementById('doc_header_entity');
    const docTitleCategory = document.getElementById('doc_title_category');
    const docTitleSec1 = document.getElementById('doc_title_sec1');
    const docLabelPemasukan = document.getElementById('doc_label_pemasukan');
    const docLabelBeban = document.getElementById('doc_label_beban');
    const docFilename = document.getElementById('doc_preview_filename');

    if (kategori === 'usaha') {
        if (badge) {
            badge.innerText = '💼 Format Usaha (Wiraswasta)';
            badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-300';
        }

        if (labelKpiPemasukan) labelKpiPemasukan.innerText = 'TOTAL OMZET & PEMASUKAN';
        if (subKpiPemasukan) subKpiPemasukan.innerText = 'Omzet pokok + pendapatan sampingan';
        if (labelKpiBeban) labelKpiBeban.innerText = 'TOTAL BEBAN USAHA & RT';
        if (titleSec1) titleSec1.innerText = 'LAPORAN PENDAPATAN & BIAYA USAHA (WIRASWASTA)';
        if (labelColPemasukan) labelColPemasukan.innerHTML = `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Rincian Penjualan / Omzet`;
        if (labelColBeban) labelColBeban.innerHTML = `<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg> Rincian Beban Usaha & Rumah Tangga`;

        if (docHeaderEntity) docHeaderEntity.innerText = 'ANALISA KEUANGAN DEBITUR PERORANGAN (WIRASWASTA)';
        if (docTitleCategory) docTitleCategory.innerText = 'LAPORAN KEUANGAN USAHA (WIRASWASTA)';
        if (docTitleSec1) docTitleSec1.innerText = '1. LAPORAN PENDAPATAN & BIAYA USAHA';
        if (docLabelPemasukan) docLabelPemasukan.innerText = 'Total Penjualan / Omzet (Pokok + Sampingan)';
        if (docLabelBeban) docLabelBeban.innerText = '(-) Total Biaya Operasional & Rumah Tangga';
        if (docFilename) docFilename.innerText = 'Laporan_Keuangan_Usaha.pdf';
    } else {
        if (badge) {
            badge.innerText = '👔 Format Gaji (Karyawan/PNS)';
            badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300';
        }

        if (labelKpiPemasukan) labelKpiPemasukan.innerText = 'TOTAL GAJI & PENGHASILAN';
        if (subKpiPemasukan) subKpiPemasukan.innerText = 'Gaji pokok + tunjangan + tambahan';
        if (labelKpiBeban) labelKpiBeban.innerText = 'TOTAL BIAYA HIDUP & RUTIN';
        if (titleSec1) titleSec1.innerText = 'LAPORAN PENGHASILAN & BIAYA HIDUP (BERPENGHASILAN TETAP)';
        if (labelColPemasukan) labelColPemasukan.innerHTML = `<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Rincian Penghasilan / Gaji`;
        if (labelColBeban) labelColBeban.innerHTML = `<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg> Rincian Pengeluaran & Biaya Hidup`;

        if (docHeaderEntity) docHeaderEntity.innerText = 'ANALISA KEUANGAN DEBITUR PERORANGAN (BERPENGHASILAN TETAP)';
        if (docTitleCategory) docTitleCategory.innerText = 'LAPORAN PENGHASILAN & BIAYA HIDUP';
        if (docTitleSec1) docTitleSec1.innerText = '1. LAPORAN PENGHASILAN & BIAYA HIDUP';
        if (docLabelPemasukan) docLabelPemasukan.innerText = 'Total Penghasilan / Gaji (Pokok + Tambahan)';
        if (docLabelBeban) docLabelBeban.innerText = '(-) Total Pengeluaran & Biaya Hidup';
        if (docFilename) docFilename.innerText = 'Laporan_Keuangan_Gaji.pdf';
    }
}
window.gantiKategoriDebitur = gantiKategoriDebitur;

// Sinkronisasi Seluruh Data dari Tab 9 ke Tab Laporan Secara Real-Time
function sinkronkanLaporanKeuangan() {
    // 0. Auto-deteksi kategori sesuai checkbox 'ada_bukti_pendapatan_lain_rk' di Tab 9
    const isCheckedAdaLain = Boolean(document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked);
    const targetKategori = isCheckedAdaLain ? 'usaha' : 'gaji';
    if (activeKategoriDebitur !== targetKategori) {
        gantiKategoriDebitur(targetKategori);
    }

    // 1. Ambil Pemasukan dari Tab 9
    let totPemasukanPokok = 0;
    let listPendapatan = [];
    if (Array.isArray(window.daftarPendapatan) && window.daftarPendapatan.length > 0) {
        listPendapatan = window.daftarPendapatan;
        listPendapatan.forEach(item => {
            totPemasukanPokok += pNomLap(item.nominal);
        });
    } else {
        totPemasukanPokok = pNomLap(document.getElementById('pendapatan_rk')?.value) || 0;
        listPendapatan = [{ nama: (activeKategoriDebitur === 'usaha' ? 'Omzet Penjualan' : 'Gaji Pokok'), nominal: totPemasukanPokok }];
    }

    const nomLain = isCheckedAdaLain ? (pNomLap(document.getElementById('pendapatan_lain_rk')?.value) || 0) : 0;
    const totalPemasukan = totPemasukanPokok + nomLain;

    // 2. Ambil Pengeluaran / Beban dari Tab 9
    let listBeban = [];
    let totBeban = 0;
    if (Array.isArray(window.daftarBebanLain) && window.daftarBebanLain.length > 0) {
        listBeban = window.daftarBebanLain;
        listBeban.forEach(item => {
            totBeban += pNomLap(item.nominal);
        });
    } else {
        totBeban = pNomLap(document.getElementById('beban_lainnya_nominal_rk')?.value) || 0;
        listBeban = [{ nama: 'Pengeluaran Rutin & Kebutuhan Rumah Tangga', nominal: totBeban }];
    }

    // 3. Sisa Penghasilan (Surplus Bulanan)
    const surplus = totalPemasukan - totBeban;

    // 4. Ambil Daftar Harta (Aset) dari Tab 9 (TIDAK BOLEH DARI NILAI AGUNAN)
    let listHarta = [];
    if (Array.isArray(window.daftarHarta) && window.daftarHarta.length > 0) {
        listHarta = window.daftarHarta;
    } else {
        listHarta = <?= json_encode($listDetailHarta) ?>;
    }

    let totHartaBruto = 0;
    let asetTerikat = 0;
    listHarta.forEach(item => {
        const nom = pNomLap(item.nominal);
        totHartaBruto += nom;
        if (item.status === 'terikat') {
            asetTerikat += nom;
        }
    });
    const totHartaEligible = Math.max(0, totHartaBruto - asetTerikat);

    // 5. Ambil Daftar Utang Luar dari Tab 9 + SLIK OJK
    let listUtang = [];
    if (Array.isArray(window.daftarUtang) && window.daftarUtang.length > 0) {
        listUtang = window.daftarUtang;
    } else {
        listUtang = <?= json_encode($listDetailUtang) ?>;
    }

    let subUtangDinamis = 0;
    let subAngsDinamis = 0;
    listUtang.forEach(item => {
        subUtangDinamis += pNomLap(item.nominal);
        subAngsDinamis += pNomLap(item.angsuran);
    });

    const outSlik = window.OUTSTANDING_SLIK_DEFAULT || <?= $totalOutstandingSlik ?>;
    const angsSlik = window.ANGSURAN_SLIK_DEFAULT || <?= $totalCicilanSlik ?>;

    const totUtangLuar = subUtangDinamis + outSlik;
    const totAngsBerjalan = subAngsDinamis + angsSlik;

    // 6. Fasilitas Kredit Baru yang Diajukan
    const plafonBaru = pNomLap(document.getElementById('plafon_pengajuan')?.value) || <?= $plafonBaru ?>;
    const tenorBaru = parseInt(document.getElementById('tenor_bulan')?.value, 10) || <?= $tenorBulanBaru ?>;
    const bungaBaru = parseFloat(document.getElementById('bunga_per_bulan')?.value) || <?= $bungaBulanPersen ?>;
    const angsPokokBaru = (plafonBaru > 0 && tenorBaru > 0) ? Math.round(plafonBaru / tenorBaru) : 0;
    const angsBungaBaru = (plafonBaru > 0) ? Math.round(plafonBaru * (bungaBaru / 100)) : 0;
    const estimasiAngsBaru = angsPokokBaru + angsBungaBaru;
    const totAngsGabungan = totAngsBerjalan + estimasiAngsBaru;

    // 7. Kekayaan Bersih (Harta Bersih)
    const kekayaanBersih = totHartaEligible - totUtangLuar;

    // 8. Rasio-Rasio Keuangan
    const dscr = (totAngsGabungan > 0) ? (surplus / totAngsGabungan).toFixed(2) : '0.00';
    const dsr  = (totalPemasukan > 0) ? ((totAngsGabungan / totalPemasukan) * 100).toFixed(1) : '0.0';
    const dar  = (totHartaEligible > 0) ? ((totUtangLuar / totHartaEligible) * 100).toFixed(1) : '0.0';
    const der  = (kekayaanBersih > 0) ? (totUtangLuar / kekayaanBersih).toFixed(2) : '0.00';

    // 9. Update UI Metrics Tab Laporan (100% Cocok Dengan Tab 9)
    if (document.getElementById('metric_lap_pemasukan')) document.getElementById('metric_lap_pemasukan').innerText = fNomLap(totalPemasukan);
    if (document.getElementById('metric_lap_beban')) document.getElementById('metric_lap_beban').innerText = fNomLap(totBeban);
    if (document.getElementById('metric_lap_surplus')) document.getElementById('metric_lap_surplus').innerText = fNomLap(surplus);
    if (document.getElementById('metric_lap_net_worth')) document.getElementById('metric_lap_net_worth').innerText = fNomLap(kekayaanBersih);

    if (document.getElementById('lap_badge_tot_pemasukan')) document.getElementById('lap_badge_tot_pemasukan').innerText = fNomLap(totalPemasukan);
    if (document.getElementById('lap_tfoot_tot_pemasukan')) document.getElementById('lap_tfoot_tot_pemasukan').innerText = fNomLap(totalPemasukan);
    if (document.getElementById('lap_badge_tot_beban')) document.getElementById('lap_badge_tot_beban').innerText = fNomLap(totBeban);
    if (document.getElementById('lap_tfoot_tot_beban')) document.getElementById('lap_tfoot_tot_beban').innerText = fNomLap(totBeban);
    if (document.getElementById('lap_surplus_nominal')) document.getElementById('lap_surplus_nominal').innerText = fNomLap(surplus);

    // Update Tabel Pemasukan Tab Laporan
    const tbodyPemasukan = document.getElementById('tbody_lap_pemasukan');
    if (tbodyPemasukan) {
        let htmP = '';
        listPendapatan.forEach(item => {
            const nom = pNomLap(item.nominal);
            if (item.nama || nom > 0) {
                htmP += `
                    <tr>
                        <td class="py-2.5 px-3 font-semibold text-slate-800">${escapeHtmlLap(item.nama || 'Pendapatan')}</td>
                        <td class="py-2.5 px-3 text-right font-bold text-blue-700">${fNomLap(nom)}</td>
                    </tr>
                `;
            }
        });
        if (nomLain > 0) {
            htmP += `
                <tr>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">Pendapatan Sampingan / Lainnya</td>
                    <td class="py-2.5 px-3 text-right font-bold text-blue-700">${fNomLap(nomLain)}</td>
                </tr>
            `;
        }
        if (!htmP) {
            htmP = `<tr><td class="py-2.5 px-3 text-slate-400 italic" colspan="2">Belum ada data pemasukan</td></tr>`;
        }
        tbodyPemasukan.innerHTML = htmP;
    }

    // Update Tabel Beban Tab Laporan
    const tbodyBeban = document.getElementById('tbody_lap_beban');
    if (tbodyBeban) {
        let htmB = '';
        listBeban.forEach(item => {
            const nom = pNomLap(item.nominal);
            if (item.nama || nom > 0) {
                htmB += `
                    <tr>
                        <td class="py-2.5 px-3 font-semibold text-slate-800">${escapeHtmlLap(item.nama || 'Beban')}</td>
                        <td class="py-2.5 px-3 text-right font-bold text-rose-600">${fNomLap(nom)}</td>
                    </tr>
                `;
            }
        });
        if (!htmB) {
            htmB = `<tr><td class="py-2.5 px-3 text-slate-400 italic" colspan="2">Belum ada rincian beban</td></tr>`;
        }
        tbodyBeban.innerHTML = htmB;
    }

    // Update Tabel Harta Tab Laporan
    const tbodyHarta = document.getElementById('tbody_lap_harta');
    if (tbodyHarta) {
        let htmH = '';
        let noH = 1;
        listHarta.forEach(item => {
            const nom = pNomLap(item.nominal);
            const isTerikat = (item.status === 'terikat');
            htmH += `
                <tr class="${isTerikat ? 'bg-amber-50/40 text-slate-500' : ''}">
                    <td class="py-2.5 px-4 text-center font-bold text-slate-500">${noH++}</td>
                    <td class="py-2.5 px-4 font-semibold text-slate-800">
                        ${escapeHtmlLap(item.nama || 'Aset')}
                        ${isTerikat ? '<span class="ml-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Dijaminkan / Terikat</span>' : ''}
                    </td>
                    <td class="py-2.5 px-4 text-center font-bold">
                        ${isTerikat ? '<span class="text-amber-700">Terikat Jaminan</span>' : '<span class="text-emerald-700">Bebas Jaminan (Eligible)</span>'}
                    </td>
                    <td class="py-2.5 px-4 text-right font-bold ${isTerikat ? 'text-amber-800' : 'text-slate-800'}">
                        ${fNomLap(nom)}
                    </td>
                </tr>
            `;
        });
        tbodyHarta.innerHTML = htmH;
    }

    if (document.getElementById('lap_tot_harta_bruto')) document.getElementById('lap_tot_harta_bruto').innerText = fNomLap(totHartaBruto);
    if (document.getElementById('lap_tot_aset_terikat')) document.getElementById('lap_tot_aset_terikat').innerText = `(${fNomLap(asetTerikat)})`;
    if (document.getElementById('lap_tot_harta_eligible')) document.getElementById('lap_tot_harta_eligible').innerText = fNomLap(totHartaEligible);

    // Update Tabel Utang Luar Tab Laporan
    const tbodyUtang = document.getElementById('tbody_lap_utang_luar');
    if (tbodyUtang) {
        let htmU = '';
        listUtang.forEach(item => {
            const nom = pNomLap(item.nominal);
            const angs = pNomLap(item.angsuran);
            if (item.nama || nom > 0 || angs > 0) {
                htmU += `
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">${escapeHtmlLap(item.nama || 'Utang Lainnya')}</td>
                        <td class="py-2 px-3 text-right font-bold text-slate-700">${fNomLap(nom)}</td>
                        <td class="py-2 px-3 text-right font-bold text-rose-600">${fNomLap(angs)}</td>
                    </tr>
                `;
            }
        });

        const rekapSlik = <?= json_encode($rekapSlikFasilitas) ?>;
        if (Array.isArray(rekapSlik)) {
            rekapSlik.forEach(s => {
                htmU += `
                    <tr class="bg-slate-50/60">
                        <td class="py-2 px-3 font-medium text-slate-700">
                            ${escapeHtmlLap(s.bank || 'Lembaga')} - ${escapeHtmlLap(s.jenis || 'Kredit')}
                            <span class="block text-[9px] text-slate-400 font-mono">${escapeHtmlLap(s.kol || 'Kol 1')}</span>
                        </td>
                        <td class="py-2 px-3 text-right font-bold text-slate-800">${fNomLap(s.outstanding || 0)}</td>
                        <td class="py-2 px-3 text-right font-bold text-rose-600">${fNomLap(s.angsuran || 0)}</td>
                    </tr>
                `;
            });
        }
        tbodyUtang.innerHTML = htmU;
    }

    if (document.getElementById('lap_badge_tot_utang_luar')) document.getElementById('lap_badge_tot_utang_luar').innerText = fNomLap(totUtangLuar);
    if (document.getElementById('lap_tfoot_sub_utang_luar')) document.getElementById('lap_tfoot_sub_utang_luar').innerText = fNomLap(totUtangLuar);
    if (document.getElementById('lap_tfoot_sub_angs_luar')) document.getElementById('lap_tfoot_sub_angs_luar').innerText = fNomLap(totAngsBerjalan);

    // Update Fasilitas Baru
    if (document.getElementById('lap_baru_plafon')) document.getElementById('lap_baru_plafon').innerText = fNomLap(plafonBaru);
    if (document.getElementById('lap_baru_tenor')) document.getElementById('lap_baru_tenor').innerText = `${tenorBaru} Bulan`;
    if (document.getElementById('lap_baru_bunga')) document.getElementById('lap_baru_bunga').innerText = `${bungaBaru}% / Bulan`;
    if (document.getElementById('lap_baru_angs_nom')) document.getElementById('lap_baru_angs_nom').innerText = fNomLap(estimasiAngsBaru);
    if (document.getElementById('lap_tot_angs_gabungan')) document.getElementById('lap_tot_angs_gabungan').innerText = fNomLap(totAngsGabungan);

    // Update Rekapitulasi Kekayaan Bersih & Rasio
    if (document.getElementById('rekap_lap_harta_eligible')) document.getElementById('rekap_lap_harta_eligible').innerText = fNomLap(totHartaEligible);
    if (document.getElementById('rekap_lap_utang_luar')) document.getElementById('rekap_lap_utang_luar').innerText = `(${fNomLap(totUtangLuar)})`;
    if (document.getElementById('rekap_lap_net_worth_bold')) document.getElementById('rekap_lap_net_worth_bold').innerText = fNomLap(kekayaanBersih);

    if (document.getElementById('rasio_lap_dscr')) {
        const elDscr = document.getElementById('rasio_lap_dscr');
        elDscr.innerText = `${dscr}x`;
        elDscr.className = (parseFloat(dscr) >= 1.30) ? 'text-lg font-black text-emerald-700' : 'text-lg font-black text-rose-600';
    }
    if (document.getElementById('rasio_lap_dsr')) document.getElementById('rasio_lap_dsr').innerText = `${dsr}%`;
    if (document.getElementById('rasio_lap_dar')) document.getElementById('rasio_lap_dar').innerText = `${dar}%`;
    if (document.getElementById('rasio_lap_der')) document.getElementById('rasio_lap_der').innerText = `${der}x`;

    const labelStatus = document.getElementById('labelStatusKelayakanRasio');
    const boxStatus = document.getElementById('boxStatusKelayakanRasio');
    if (labelStatus && boxStatus) {
        if (parseFloat(dscr) >= 1.30 && parseFloat(dsr) <= 40) {
            boxStatus.className = 'mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs text-emerald-800 font-medium';
            labelStatus.innerText = 'Rasio keuangan debitur sangat sehat (DSCR >= 1.30x) dan memiliki kapasitas pembayaran yang memadai.';
        } else if (parseFloat(dscr) >= 1.0) {
            boxStatus.className = 'mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2 text-xs text-amber-800 font-medium';
            labelStatus.innerText = 'Perhatian: Kapasitas angsuran debitur ketat (DSCR mendekati 1.00x). Pertimbangkan penyesuaian plafon atau tenor.';
        } else {
            boxStatus.className = 'mt-4 p-3 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-2 text-xs text-rose-800 font-medium';
            labelStatus.innerText = 'Peringatan Risiko: Total angsuran melebihi kapasitas surplus bulanan (DSCR < 1.00x).';
        }
    }

    // 10. Update Print Sheet Modal Preview (Sama Persis)
    if (document.getElementById('doc_pemasukan')) document.getElementById('doc_pemasukan').innerText = fNomLap(totalPemasukan);
    if (document.getElementById('doc_beban')) document.getElementById('doc_beban').innerText = `(${fNomLap(totBeban)})`;
    if (document.getElementById('doc_surplus')) document.getElementById('doc_surplus').innerText = fNomLap(surplus);

    const docTbodyH = document.getElementById('doc_tbody_harta');
    if (docTbodyH) {
        let docHtmH = '';
        listHarta.forEach(item => {
            const nom = pNomLap(item.nominal);
            const isTerikat = (item.status === 'terikat');
            docHtmH += `
                <tr class="${isTerikat ? 'bg-amber-50/50 text-slate-400' : ''}">
                    <td class="py-1.5 px-3 font-semibold text-slate-800">
                        ${escapeHtmlLap(item.nama || 'Aset')}
                        ${isTerikat ? '<span class="text-[9px] text-amber-700 ml-1">(Terikat)</span>' : ''}
                    </td>
                    <td class="py-1.5 px-3 text-center font-bold ${isTerikat ? 'text-amber-700' : 'text-emerald-700'}">
                        ${isTerikat ? 'Terikat' : 'Bebas'}
                    </td>
                    <td class="py-1.5 px-3 text-right font-medium text-slate-800">${fNomLap(nom)}</td>
                </tr>
            `;
        });
        docTbodyH.innerHTML = docHtmH;
    }
    if (document.getElementById('doc_tot_harta_eligible')) document.getElementById('doc_tot_harta_eligible').innerText = fNomLap(totHartaEligible);

    if (document.getElementById('doc_utang_luar')) document.getElementById('doc_utang_luar').innerText = fNomLap(totUtangLuar);
    if (document.getElementById('doc_angs_berjalan')) document.getElementById('doc_angs_berjalan').innerText = `${fNomLap(totAngsBerjalan)}/bln`;
    if (document.getElementById('doc_plafon_baru')) document.getElementById('doc_plafon_baru').innerText = fNomLap(plafonBaru);
    if (document.getElementById('doc_angs_baru')) document.getElementById('doc_angs_baru').innerText = `${fNomLap(estimasiAngsBaru)}/bln`;
    if (document.getElementById('doc_tot_angs_gabungan')) document.getElementById('doc_tot_angs_gabungan').innerText = `${fNomLap(totAngsGabungan)}/bln`;

    if (document.getElementById('doc_kekayaan_bersih')) document.getElementById('doc_kekayaan_bersih').innerText = fNomLap(kekayaanBersih);
    if (document.getElementById('doc_dscr')) document.getElementById('doc_dscr').innerText = `${dscr}x`;
    if (document.getElementById('doc_dsr')) document.getElementById('doc_dsr').innerText = `${dsr}%`;
    if (document.getElementById('doc_dar')) document.getElementById('doc_dar').innerText = `${dar}%`;
    if (document.getElementById('doc_der')) document.getElementById('doc_der').innerText = `${der}x`;

    // 11. Jalankan Perhitungan Disbursement Terintegrasi
    if (typeof hitungDisbursement === 'function') {
        hitungDisbursement();
    }
}
window.sinkronkanLaporanKeuangan = sinkronkanLaporanKeuangan;

// Helper Escape HTML
function escapeHtmlLap(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// Buka Modal Preview Lembar A4
function bukaModalPreviewLaporan() {
    sinkronkanLaporanKeuangan();
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}
window.bukaModalPreviewLaporan = bukaModalPreviewLaporan;

// Tutup Modal Preview
function tutupModalPreviewLaporan() {
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}
window.tutupModalPreviewLaporan = tutupModalPreviewLaporan;

// Cetak Lembar A4
function cetakDokumenA4() {
    window.print();
}
window.cetakDokumenA4 = cetakDokumenA4;

// Unduh File PDF Langsung ke Komputer
function unduhDokumenPdfLangsung() {
    const sheet = document.getElementById('printSheetLaporan');
    if (!sheet) return;

    const btn = document.getElementById('btnUnduhPdfModal');
    const label = document.getElementById('labelUnduhModal');
    const oldText = label ? label.innerText : 'Unduh PDF';

    if (label) label.innerText = 'Membuat PDF...';
    if (btn) btn.disabled = true;

    const pdfName = (activeKategoriDebitur === 'usaha') ? 'Laporan_Keuangan_Usaha.pdf' : 'Laporan_Keuangan_Gaji.pdf';

    if (typeof html2pdf !== 'undefined') {
        const opt = {
            margin:       [8, 10, 8, 10],
            filename:     pdfName,
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
window.unduhDokumenPdfLangsung = unduhDokumenPdfLangsung;

// Hook sinkronisasi awal saat dokumen siap
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const isChecked = Boolean(document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked);
        gantiKategoriDebitur(isChecked ? 'usaha' : 'gaji');
        sinkronkanLaporanKeuangan();
    }, 350);
});
</script>
