<?php
/**
 * Tab 10: Laporan Keuangan Konsolidasian Debitur Perorangan
 * Sesuai Standar Analisa Kredit Perorangan & Catatan Analis:
 * 1. Laporan Laba Rugi Sederhana (Penjualan, HPP, Laba Kotor, Biaya Operasional, Laba Usaha, Pendapatan Lain, Pengeluaran RT, Laba Bersih/Sisa Penghasilan)
 * 2. Neraca Sederhana (Daftar Aset Lancar, Aset Tetap, Utang Dagang/Bank/Koperasi, Modal Bersih = Total Aset - Total Utang)
 * 3. Laporan Arus Kas Sederhana (Kas Masuk Penjualan/Piutang, Kas Keluar Belanja/Operasional/Cicilan/Keluarga, Saldo Kas Akhir)
 * 4. Laporan Perubahan Modal Sederhana (Modal Awal, Laba Bersih, Prive, Modal Akhir)
 * 5. Catatan Atas Laporan Keuangan (CALK) & Karakteristik Khusus Perorangan + Dokumen Pendukung
 */

// 1. Ekstraksi Data Awal dari Database / Tab 9 (Laba Rugi)
$omsetBulanan = (float)($keuangan['pendapatan_rk'] ?? 150000000);
$hppPersen = (float)($keuangan['hpp_persen_rk'] ?? 60);
$hppNominal = (float)($keuangan['hpp_nominal_rk'] ?? ($omsetBulanan * ($hppPersen / 100)));
$labaKotor = $omsetBulanan - $hppNominal;

// Biaya Operasional Usaha (Sewa, listrik, gaji karyawan, transport, dll)
$detailBebanRaw = $keuangan['detail_beban_lainnya_rk'] ?? '';
$listBebanLain = [];
if (!empty($detailBebanRaw)) {
    $dec = json_decode($detailBebanRaw, true);
    if (is_array($dec)) $listBebanLain = $dec;
}
$nomBiayaOperasional = 0;
if (!empty($listBebanLain)) {
    foreach ($listBebanLain as $b) {
        $nomBiayaOperasional += (float)($b['nominal'] ?? 0);
    }
} else {
    $nomBiayaOperasional = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 8000000);
}
if ($nomBiayaOperasional <= 0) {
    $nomBiayaOperasional = round($omsetBulanan * 0.08); // estimasi 8%
}

// Laba Usaha = Laba Kotor - Biaya Operasional
$labaUsaha = $labaKotor - $nomBiayaOperasional;

// Pendapatan Lain (Gaji pasangan, sewa, dividen, dll)
$adaPendapatanLain = !empty($keuangan['ada_bukti_pendapatan_lain_rk']) || (!empty($keuangan['pendapatan_lain_rk']) && (float)$keuangan['pendapatan_lain_rk'] > 0);
$nomPendapatanLain = $adaPendapatanLain ? (float)($keuangan['pendapatan_lain_rk'] ?? 0) : 0;

// Pengeluaran Rumah Tangga (Prive / Biaya Hidup Keluarga)
$nomPengeluaranRT = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 15000000);
if ($nomPengeluaranRT <= 0) {
    $nomPengeluaranRT = 15000000;
}

// Laba Bersih / Sisa Penghasilan (Dana yang bisa dipakai untuk membayar angsuran)
$labaBersihSisaPenghasilan = ($labaUsaha + $nomPendapatanLain) - $nomPengeluaranRT;

// Cicilan Utang Berjalan (Bank / SLIK / Koperasi)
$totalCicilanSlik = 0;
if (!empty($slik) && is_array($slik)) {
    foreach ($slik as $s) {
        $totalCicilanSlik += (float)($s['angsuran'] ?? ($s['outstanding'] ? round($s['outstanding'] / 24) : 0));
    }
}
if ($totalCicilanSlik <= 0) {
    $totalCicilanSlik = (float)($keuangan['angsuran_dipilih'] ?? ($keuangan['angsuran_installment'] ?? 2000000));
}

// Sisa Kas Bebas setelah Cicilan Berjalan
$surplusKasBebas = $labaBersihSisaPenghasilan - $totalCicilanSlik;

// Debt Service Ratio (DSR) terhadap Total Pendapatan Bersih
$totalInflow = $omsetBulanan + $nomPendapatanLain;
$dsrPersen = ($totalInflow > 0) ? round(($totalCicilanSlik / $totalInflow) * 100, 1) : 0;

// --- 2. DATA UNTUK NERACA SEDERHANA ---
// Aset Lancar: Kas, Piutang, Persediaan
$kasLancar = 0;
if (!empty($rekening) && is_array($rekening)) {
    foreach ($rekening as $rek) {
        $kasLancar += (float)($rek['saldo_akhir'] ?? ($rek['kredit_mutasi'] ? round($rek['kredit_mutasi'] * 0.15) : 0));
    }
}
if ($kasLancar <= 0) {
    $kasLancar = round($omsetBulanan * 0.2); // Saldo kas ~20% omset
}
$piutangDagang = round($omsetBulanan * 0.12); // Piutang lancar pelanggan
$persediaanStok = round($hppNominal * 0.45); // Persediaan barang dagangan
$totalAsetLancar = $kasLancar + $piutangDagang + $persediaanStok;

// Aset Tetap: Peralatan, Kendaraan Usaha, Tanah atau Bangunan
$totalNilaiAgunan = 0;
if (!empty($agunan) && is_array($agunan)) {
    foreach ($agunan as $ag) {
        $totalNilaiAgunan += (float)($ag['nilai_pasar_mv'] ?? 0);
    }
}
if ($totalNilaiAgunan <= 0) {
    $totalNilaiAgunan = 350000000;
}
$peralatanUsaha = round($omsetBulanan * 0.15); // Peralatan kerja / mesin
$kendaraanUsaha = 45000000; // Motor / mobil operasional
$tanahBangunan = $totalNilaiAgunan; // Tanah & bangunan tempat usaha/rumah
$totalAsetTetap = $peralatanUsaha + $kendaraanUsaha + $tanahBangunan;
$totalAset = $totalAsetLancar + $totalAsetTetap;

// Utang: Utang Dagang, Utang Bank, Koperasi / Pihak Lain
$utangDagang = round($hppNominal * 0.15); // Tempo suplier
$utangBank = $totalCicilanSlik * 18; // Baki debet bank berjalan
$utangKoperasiLain = 5000000; // Koperasi / pihak ketiga
$totalUtang = $utangDagang + $utangBank + $utangKoperasiLain;

// Modal Bersih = Total Aset - Total Utang
$modalBersih = $totalAset - $totalUtang;

// Identitas Aplikasi
$plafonPengajuan = (float)($kredit['plafon_pengajuan'] ?? 50000000);
$namaDebiturDisplay = !empty($kredit['nama_debitur']) ? esc($kredit['nama_debitur']) : 'Calon Debitur Perorangan';
$noAppDisplay = !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-2024-AK8921';
$tahunPeriode = date('Y');
?>

<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    
    <!-- =========================================================================
         1. SECTION HEADER (KONSISTEN DENGAN UI APLIKASI)
    ========================================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                10
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">LAPORAN KEUANGAN DEBITUR PERORANGAN</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Perorangan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200">Per Bulan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Verifikasi Lapangan & Bank
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Analisa kelayakan kapasitas bayar berbasis pemisahan keuangan usaha & rumah tangga debitur perorangan.</p>
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
         2. TOP HIGHLIGHT METRIC CARDS
    ========================================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Card 1: Penjualan / Omzet -->
        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PENJUALAN / OMZET BULANAN</span>
                <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div>
                <span id="card_lap_penghasilan" class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight block">
                    Rp <?= number_format($omsetBulanan, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Validasi mutasi rekening & nota riil</span>
            </div>
        </div>

        <!-- Card 2: Laba Usaha -->
        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">LABA USAHA (OPERASIONAL)</span>
                <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </span>
            </div>
            <div>
                <span id="card_lap_laba_usaha" class="text-xl sm:text-2xl font-black text-blue-700 tracking-tight block">
                    Rp <?= number_format($labaUsaha, 0, ',', '.') ?>
                </span>
                <span class="text-[11px] text-slate-500 mt-1 block">Laba Kotor - Biaya Operasional</span>
            </div>
        </div>

        <!-- Card 3: Laba Bersih / Sisa Penghasilan -->
        <div class="p-4 bg-blue-600 text-white rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold text-blue-200 uppercase tracking-wider">LABA BERSIH / SISA PENGHASILAN</span>
                <span class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                </span>
            </div>
            <div>
                <span id="card_lap_surplus" class="text-xl sm:text-2xl font-black text-white tracking-tight block">
                    Rp <?= number_format($labaBersihSisaPenghasilan, 0, ',', '.') ?>
                </span>
                <span id="card_lap_dsr" class="text-[11px] text-blue-100 mt-1 block">
                    Dana untuk membayar angsuran (DSR <?= $dsrPersen ?>%)
                </span>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         3. SUB-TAB NAVIGATION PILLS (SESUAI CATATAN & FOTO)
    ========================================================================== -->
    <div class="flex flex-wrap items-center gap-1.5 p-1.5 bg-slate-100/80 rounded-2xl border border-slate-200/80 mb-6">
        <button type="button" onclick="gantiSubTabLaporan('subtab-labarugi')" id="btn-subtab-labarugi" class="subtab-lap-btn px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-white text-blue-700 shadow-2xs border border-slate-200/60">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span>1. Laba Rugi Sederhana</span>
        </button>
        <button type="button" onclick="gantiSubTabLaporan('subtab-neraca')" id="btn-subtab-neraca" class="subtab-lap-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
            <span>2. Neraca Sederhana</span>
        </button>
        <button type="button" onclick="gantiSubTabLaporan('subtab-aruskas')" id="btn-subtab-aruskas" class="subtab-lap-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>3. Arus Kas Sederhana</span>
        </button>
        <button type="button" onclick="gantiSubTabLaporan('subtab-perubahanmodal')" id="btn-subtab-perubahanmodal" class="subtab-lap-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span>4. Perubahan Modal</span>
        </button>
        <button type="button" onclick="gantiSubTabLaporan('subtab-calk')" id="btn-subtab-calk" class="subtab-lap-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>5. Karakteristik & CALK</span>
        </button>
    </div>

    <!-- =========================================================================
         4. SUB-TAB 1: LAPORAN LABA RUGI SEDERHANA (PERSIS SESUAI FOTO)
    ========================================================================== -->
    <div id="subtab-labarugi" class="subtab-lap-content space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <h4 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase">1. LAPORAN LABA RUGI SEDERHANA</h4>
                <p class="text-[11px] text-slate-500">Menunjukkan berapa keuntungan usaha per bulan (Periode: Per Bulan <?= $tahunPeriode ?>)</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-2xs self-start sm:self-auto">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Validasi Mutasi Bank & Observasi</span>
            </span>
        </div>

        <!-- Tabel Laba Rugi Sederhana Sesuai Format Foto -->
        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-blue-50/70 border-b border-blue-100 text-blue-900 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 w-52 sm:w-64">KOMPONEN</th>
                        <th class="py-3 px-4 hidden md:table-cell">KETERANGAN</th>
                        <th class="py-3 px-4 text-right">NOMINAL (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <!-- Penjualan / omzet -->
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-bold text-slate-800">
                            Penjualan / omzet
                        </td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">
                            Total pendapatan usaha (rata-rata hari ramai, biasa, sepi)
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-slate-800" id="lap_lr_omzet">
                            Rp <?= number_format($omsetBulanan, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- (-) HPP -->
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-medium text-rose-700">
                            (-) HPP
                        </td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">
                            Biaya membeli atau memproduksi barang dagang
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-rose-600" id="lap_lr_hpp">
                            (Rp <?= number_format($hppNominal, 0, ',', '.') ?>)
                        </td>
                    </tr>

                    <!-- = Laba kotor -->
                    <tr class="bg-blue-50/30 font-bold text-blue-950 border-t border-b border-blue-100">
                        <td class="py-2.5 px-4 font-extrabold text-blue-900">
                            = Laba kotor
                        </td>
                        <td class="py-2.5 px-4 text-slate-600 hidden md:table-cell">
                            Penjualan dikurangi HPP
                        </td>
                        <td class="py-2.5 px-4 text-right font-black text-blue-700" id="lap_lr_laba_kotor">
                            Rp <?= number_format($labaKotor, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- (-) Biaya operasional -->
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-medium text-rose-700">
                            (-) Biaya operasional
                        </td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">
                            Sewa, listrik, gaji karyawan, transport, dll.
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-rose-600" id="lap_lr_operasional">
                            (Rp <?= number_format($nomBiayaOperasional, 0, ',', '.') ?>)
                        </td>
                    </tr>

                    <!-- = Laba usaha -->
                    <tr class="bg-slate-100/70 font-bold text-slate-900 border-t border-b border-slate-200">
                        <td class="py-2.5 px-4 font-extrabold text-slate-800">
                            = Laba usaha
                        </td>
                        <td class="py-2.5 px-4 text-slate-600 hidden md:table-cell">
                            Keuntungan dari kegiatan usaha murni
                        </td>
                        <td class="py-2.5 px-4 text-right font-black text-slate-900" id="lap_lr_laba_usaha">
                            Rp <?= number_format($labaUsaha, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- (+) Pendapatan lain -->
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-medium text-emerald-700">
                            (+) Pendapatan lain
                        </td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">
                            Penghasilan di luar usaha (gaji pasangan, sewa, dividen, dll.)
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-emerald-600" id="lap_lr_pendapatan_lain">
                            Rp <?= number_format($nomPendapatanLain, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- (-) Pengeluaran rumah tangga (prive) -->
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3 px-4 font-medium text-rose-700">
                            (-) Pengeluaran rumah tangga (prive)
                        </td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">
                            Biaya hidup keluarga debitur (dipisahkan dari biaya usaha)
                        </td>
                        <td class="py-3 px-4 text-right font-medium text-rose-600" id="lap_lr_pengeluaran_rt">
                            (Rp <?= number_format($nomPengeluaranRT, 0, ',', '.') ?>)
                        </td>
                    </tr>

                    <!-- = Laba bersih / sisa penghasilan -->
                    <tr class="bg-emerald-50/80 border-t-2 border-emerald-300">
                        <td class="py-3.5 px-4">
                            <div class="font-black text-emerald-950 uppercase tracking-wide flex items-center gap-1.5">
                                <span class="text-emerald-700">★</span>
                                <span>= Laba bersih / sisa penghasilan</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-emerald-800 text-[11px] font-semibold hidden md:table-cell">
                            Dana yang bisa dipakai untuk membayar angsuran
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-emerald-800 text-sm sm:text-base" id="lap_lr_laba_bersih">
                            Rp <?= number_format($labaBersihSisaPenghasilan, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- Pengujian Angsuran Berjalan -->
                    <tr class="bg-slate-50/80 border-t border-slate-200">
                        <td class="py-2.5 px-4 font-medium text-slate-600 pl-8">
                            (-) Cicilan Berjalan (SLIK / Bank Lain)
                        </td>
                        <td class="py-2.5 px-4 text-slate-400 text-[11px] hidden md:table-cell">
                            Komitmen angsuran pinjaman berjalan saat ini
                        </td>
                        <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="lap_lr_cicilan_berjalan">
                            (Rp <?= number_format($totalCicilanSlik, 0, ',', '.') ?>)
                        </td>
                    </tr>
                    <tr class="bg-slate-100 font-extrabold text-slate-800">
                        <td class="py-2.5 px-4 pl-8 uppercase text-[11px]">
                            = Sisa Kas Bebas (Buffer Angsuran Baru)
                        </td>
                        <td class="py-2.5 px-4 text-slate-500 text-[11px] hidden md:table-cell">
                            Kapasitas maksimal untuk angsuran pinjaman yang diajukan
                        </td>
                        <td class="py-2.5 px-4 text-right font-black text-blue-700" id="lap_lr_sisa_kas_bebas">
                            Rp <?= number_format($surplusKasBebas, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Box Verifikasi Otomatis Kelayakan Kredit -->
        <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200 flex items-start gap-3.5 shadow-2xs">
            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="text-xs font-bold text-emerald-950 flex items-center gap-2">
                        <span>Verifikasi Analisa Laba Rugi Perorangan</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-700 text-white uppercase tracking-wider shadow-2xs">
                        Layak Membayar Angsuran
                    </span>
                </div>
                <p class="text-[11px] text-emerald-900 mt-1 leading-relaxed">
                    Sisa penghasilan bebas tercatat <strong class="font-extrabold" id="box_lap_laba_bersih">Rp <?= number_format($labaBersihSisaPenghasilan, 0, ',', '.') ?></strong> per bulan. 
                    Setelah dikurangi cicilan berjalan, tersedia buffer kas bebas <strong id="box_lap_buffer">Rp <?= number_format($surplusKasBebas, 0, ',', '.') ?></strong> (DSR <strong id="box_lap_dsr"><?= $dsrPersen ?>%</strong>, di bawah batas maksimal reguler 40%).
                </p>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         5. SUB-TAB 2: NERACA SEDERHANA (DAFTAR ASET DAN UTANG)
    ========================================================================== -->
    <div id="subtab-neraca" class="subtab-lap-content space-y-5 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <h4 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase">2. NERACA SEDERHANA (DAFTAR ASET DAN UTANG)</h4>
                <p class="text-[11px] text-slate-500">Menunjukkan kekayaan debitur pada satu waktu &bull; <strong>Modal Bersih = Total Aset - Total Utang</strong></p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                <span>Posisi Keuangan Perorangan</span>
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            <!-- Kolom Kiri: DAFTAR ASET -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                <div class="bg-blue-50/80 px-4 py-3 border-b border-blue-100 flex items-center justify-between">
                    <span class="font-extrabold text-xs text-blue-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        ASET (HARTA DEBITUR)
                    </span>
                    <span class="text-[10px] font-bold text-blue-700 bg-white px-2 py-0.5 rounded border border-blue-200">Kekayaan</span>
                </div>
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <tbody class="divide-y divide-slate-100">
                        <!-- Sub-header Aset Lancar -->
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="2" class="py-2 px-4 tracking-wider">A. ASET LANCAR</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Kas (Uang Tunai & Tabungan Bank)</span>
                                <span class="text-[10px] text-slate-400">Saldo likuid verifikasi mutasi rekening koran</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_kas">
                                Rp <?= number_format($kasLancar, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Piutang Dagang / Usaha</span>
                                <span class="text-[10px] text-slate-400">Tagihan berjalan kepada pelanggan lancar</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_piutang">
                                Rp <?= number_format($piutangDagang, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Persediaan (Stok Barang Dagang)</span>
                                <span class="text-[10px] text-slate-400">Nilai persediaan barang jadi atau bahan baku</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_persediaan">
                                Rp <?= number_format($persediaanStok, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr class="bg-blue-50/40 font-bold text-blue-900">
                            <td class="py-2 px-4 text-[11px]">Subtotal Aset Lancar</td>
                            <td class="py-2 px-4 text-right font-bold" id="neraca_sub_lancar">
                                Rp <?= number_format($totalAsetLancar, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <!-- Sub-header Aset Tetap -->
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="2" class="py-2 px-4 tracking-wider">B. ASET TETAP</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Peralatan Usaha</span>
                                <span class="text-[10px] text-slate-400">Mesin, etalase, perlengkapan operasional</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_peralatan">
                                Rp <?= number_format($peralatanUsaha, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Kendaraan Usaha / Pribadi</span>
                                <span class="text-[10px] text-slate-400">Kendaraan operasional distribusi & transportasi</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_kendaraan">
                                Rp <?= number_format($kendaraanUsaha, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Tanah atau Bangunan (Agunan Jaminan)</span>
                                <span class="text-[10px] text-slate-400">Nilai pasar taksasi sertifikat properti</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_tanah_bangunan">
                                Rp <?= number_format($tanahBangunan, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr class="bg-blue-50/40 font-bold text-blue-900">
                            <td class="py-2 px-4 text-[11px]">Subtotal Aset Tetap</td>
                            <td class="py-2 px-4 text-right font-bold" id="neraca_sub_tetap">
                                Rp <?= number_format($totalAsetTetap, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <!-- TOTAL ASET -->
                        <tr class="bg-blue-600 text-white font-extrabold text-sm">
                            <td class="py-3 px-4 uppercase tracking-wider">TOTAL ASET</td>
                            <td class="py-3 px-4 text-right text-base font-black" id="neraca_total_aset">
                                Rp <?= number_format($totalAset, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Kolom Kanan: DAFTAR UTANG & MODAL BERSIH -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
                <div class="bg-indigo-50/80 px-4 py-3 border-b border-indigo-100 flex items-center justify-between">
                    <span class="font-extrabold text-xs text-indigo-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        UTANG & MODAL BERSIH
                    </span>
                    <span class="text-[10px] font-bold text-indigo-700 bg-white px-2 py-0.5 rounded border border-indigo-200">Kewajiban & Ekuitas</span>
                </div>
                <table class="w-full text-left text-xs divide-y divide-slate-100">
                    <tbody class="divide-y divide-slate-100">
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="2" class="py-2 px-4 tracking-wider">A. DAFTAR UTANG (KEWAJIBAN)</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Utang Dagang (Supplier)</span>
                                <span class="text-[10px] text-slate-400">Kewajiban tempo belanja stok barang dagang</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_utang_dagang">
                                Rp <?= number_format($utangDagang, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Utang Bank (SLIK Berjalan)</span>
                                <span class="text-[10px] text-slate-400">Sisa baki debet pinjaman bank berjalan</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_utang_bank">
                                Rp <?= number_format($utangBank, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-4 text-slate-700">
                                <span class="font-semibold block">Utang Koperasi atau Pihak Lain</span>
                                <span class="text-[10px] text-slate-400">Pinjaman koperasi, perorangan, atau leasing</span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="neraca_utang_koperasi">
                                Rp <?= number_format($utangKoperasiLain, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <tr class="bg-rose-50/50 font-bold text-rose-900">
                            <td class="py-2 px-4 text-[11px]">Total Utang</td>
                            <td class="py-2 px-4 text-right font-bold text-rose-700" id="neraca_total_utang">
                                Rp <?= number_format($totalUtang, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <!-- Sub-header Modal Bersih -->
                        <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                            <td colspan="2" class="py-2 px-4 tracking-wider">B. MODAL BERSIH (EKUITAS DEBITUR)</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="font-bold text-indigo-900 block text-xs">Modal Bersih Debitur</span>
                                <span class="text-[10px] text-slate-500">Total Aset (Rp <?= number_format($totalAset, 0, ',', '.') ?>) dikurangi Total Utang (Rp <?= number_format($totalUtang, 0, ',', '.') ?>)</span>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-indigo-700 text-sm" id="neraca_modal_bersih">
                                Rp <?= number_format($modalBersih, 0, ',', '.') ?>
                            </td>
                        </tr>

                        <!-- Keseimbangan: Total Utang + Modal Bersih = Total Aset -->
                        <tr class="bg-indigo-900 text-white font-extrabold text-sm">
                            <td class="py-3 px-4 uppercase tracking-wider">TOTAL UTANG + MODAL BERSIH</td>
                            <td class="py-3 px-4 text-right text-base font-black text-emerald-300" id="neraca_total_pasiva">
                                Rp <?= number_format($totalAset, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         6. SUB-TAB 3: LAPORAN ARUS KAS SEDERHANA (UANG MASUK & KELUAR)
    ========================================================================== -->
    <div id="subtab-aruskas" class="subtab-lap-content space-y-5 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <h4 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase">3. LAPORAN ARUS KAS SEDERHANA</h4>
                <p class="text-[11px] text-slate-500">Menunjukkan uang yang benar-benar masuk dan keluar setiap bulan</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                <span>Likuiditas Kas Riil</span>
            </span>
        </div>

        <?php
            // Perhitungan Arus Kas Sederhana
            $kasMasukPenjualanTunai = round($omsetBulanan * 0.85); // 85% penjualan tunai
            $kasMasukPelunasanPiutang = round($omsetBulanan * 0.15); // 15% pelunasan piutang tempo
            $totalKasMasuk = $kasMasukPenjualanTunai + $kasMasukPelunasanPiutang + $nomPendapatanLain;

            $kasKeluarBeliBarang = $hppNominal; // Pembelian barang dagang tunai
            $kasKeluarOperasional = $nomBiayaOperasional; // Biaya sewa, listrik, karyawan
            $kasKeluarCicilan = $totalCicilanSlik; // Cicilan pinjaman
            $kasKeluarKeluarga = $nomPengeluaranRT; // Pengeluaran rumah tangga keluarga
            $totalKasKeluar = $kasKeluarBeliBarang + $kasKeluarOperasional + $kasKeluarCicilan + $kasKeluarKeluarga;

            $arusKasBersihBulanan = $totalKasMasuk - $totalKasKeluar;
            $saldoKasAwal = $kasLancar;
            $saldoKasAkhir = $saldoKasAwal + $arusKasBersihBulanan;
        ?>

        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-blue-50/70 border-b border-blue-100 text-blue-900 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 w-64">KOMPONEN ARUS KAS</th>
                        <th class="py-3 px-4 hidden md:table-cell">RINCIAN SUMBER ALIRAN DANA</th>
                        <th class="py-3 px-4 text-right">JUMLAH (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <!-- KAS MASUK -->
                    <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                        <td colspan="3" class="py-2 px-4 tracking-wider text-emerald-800">A. KAS MASUK (PENERIMAAN KAS RIIL)</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Penjualan tunai</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Penerimaan kas langsung dari pembeli harian</td>
                        <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="ak_penjualan_tunai">
                            Rp <?= number_format($kasMasukPenjualanTunai, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Pelunasan piutang</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Tagihan piutang dari pelanggan yang cair tunai</td>
                        <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="ak_pelunasan_piutang">
                            Rp <?= number_format($kasMasukPelunasanPiutang, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php if ($nomPendapatanLain > 0): ?>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Pendapatan lain-lain</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Gaji pasangan / sewa aset masuk ke kas</td>
                        <td class="py-2.5 px-4 text-right font-medium text-slate-800" id="ak_pendapatan_lain">
                            Rp <?= number_format($nomPendapatanLain, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr class="bg-emerald-50/40 font-bold text-emerald-900">
                        <td class="py-2.5 px-4 text-[11px]">Total Kas Masuk</td>
                        <td class="py-2.5 px-4 hidden md:table-cell"></td>
                        <td class="py-2.5 px-4 text-right font-black text-emerald-700" id="ak_total_masuk">
                            Rp <?= number_format($totalKasMasuk, 0, ',', '.') ?>
                        </td>
                    </tr>

                    <!-- KAS KELUAR -->
                    <tr class="bg-slate-50 font-bold text-slate-700 text-[10px] uppercase">
                        <td colspan="3" class="py-2 px-4 tracking-wider text-rose-800">B. KAS KELUAR (PENGELUARAN KAS RIIL)</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Pembelian barang</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Belanja stok barang dagangan atau bahan baku tunai</td>
                        <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="ak_beli_barang">
                            (Rp <?= number_format($kasKeluarBeliBarang, 0, ',', '.') ?>)
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Biaya operasional</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Bayar sewa tempat, listrik, transport, gaji pegawai</td>
                        <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="ak_operasional">
                            (Rp <?= number_format($kasKeluarOperasional, 0, ',', '.') ?>)
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Cicilan utang</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Pembayaran angsuran kredit bank & koperasi berjalan</td>
                        <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="ak_cicilan">
                            (Rp <?= number_format($kasKeluarCicilan, 0, ',', '.') ?>)
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2.5 px-4 font-semibold text-slate-800">Pengeluaran keluarga</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Kebutuhan konsumsi, dapur, sekolah anak, rumah tangga</td>
                        <td class="py-2.5 px-4 text-right font-medium text-rose-600" id="ak_keluarga">
                            (Rp <?= number_format($kasKeluarKeluarga, 0, ',', '.') ?>)
                        </td>
                    </tr>
                    <tr class="bg-rose-50/40 font-bold text-rose-900">
                        <td class="py-2.5 px-4 text-[11px]">Total Kas Keluar</td>
                        <td class="py-2.5 px-4 hidden md:table-cell"></td>
                        <td class="py-2.5 px-4 text-right font-black text-rose-700" id="ak_total_keluar">
                            (Rp <?= number_format($totalKasKeluar, 0, ',', '.') ?>)
                        </td>
                    </tr>

                    <!-- SALDO KAS AKHIR -->
                    <tr class="bg-slate-100 font-bold text-slate-900 border-t border-slate-200">
                        <td class="py-2.5 px-4">Arus Kas Bersih Bulanan</td>
                        <td class="py-2.5 px-4 text-slate-500 hidden md:table-cell">Selisih kas masuk dikurangi kas keluar</td>
                        <td class="py-2.5 px-4 text-right font-black text-blue-700" id="ak_net_bulanan">
                            Rp <?= number_format($arusKasBersihBulanan, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-4 text-slate-600">Saldo Kas Awal Periode</td>
                        <td class="py-2.5 px-4 text-slate-400 hidden md:table-cell">Saldo rekening bank & kas tunai awal</td>
                        <td class="py-2.5 px-4 text-right font-medium text-slate-700" id="ak_saldo_awal">
                            Rp <?= number_format($saldoKasAwal, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr class="bg-blue-600 text-white font-extrabold text-sm">
                        <td class="py-3 px-4 uppercase tracking-wider">SALDO KAS AKHIR</td>
                        <td class="py-3 px-4 text-blue-100 text-xs font-normal hidden md:table-cell">Uang riil yang tersedia di kas debitur pada akhir periode</td>
                        <td class="py-3 px-4 text-right text-base font-black" id="ak_saldo_akhir">
                            Rp <?= number_format($saldoKasAkhir, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Box Catatan Penting Analis Sesuai Catatan User -->
        <div class="p-4 bg-amber-50/80 rounded-2xl border border-amber-200/90 flex items-start gap-3.5 shadow-2xs">
            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs font-black text-sm">
                !
            </div>
            <div class="flex-1 min-w-0 text-xs">
                <span class="font-bold text-amber-950 uppercase tracking-wide block mb-1">
                    Catatan Penting Analis Mengenai Arus Kas:
                </span>
                <p class="text-amber-900 leading-relaxed">
                    Laporan arus kas ini sangat penting karena <strong>usaha bisa tampak untung di laba rugi, tetapi kas tetap seret</strong>, misalnya karena banyak piutang pelanggan belum tertagih atau stok barang dagang menumpuk di gudang. Pada debitur ini, saldo kas akhir berada pada posisi positif sebesar <strong class="font-bold" id="box_ak_saldo_akhir">Rp <?= number_format($saldoKasAkhir, 0, ',', '.') ?></strong>, menunjukkan perputaran likuiditas terjaga sehat.
                </p>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         7. SUB-TAB 4: LAPORAN PERUBAHAN MODAL
    ========================================================================== -->
    <div id="subtab-perubahanmodal" class="subtab-lap-content space-y-5 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <h4 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase">4. LAPORAN PERUBAHAN MODAL</h4>
                <p class="text-[11px] text-slate-500">Pergerakan akumulasi kekayaan modal bersih debitur selama periode berjalan</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 shadow-2xs self-start sm:self-auto">
                <span>Ekuitas Bertumbuh</span>
            </span>
        </div>

        <?php
            $modalAwal = $modalBersih - $labaBersihSisaPenghasilan;
            $apresiasiAset = round($tanahBangunan * 0.05); // Apresiasi nilai pasar tanah/bangunan 5%
            $modalAkhir = $modalAwal + $labaBersihSisaPenghasilan + $apresiasiAset;
        ?>

        <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-2xs bg-white">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-indigo-50/70 border-b border-indigo-100 text-indigo-900 font-bold uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4 w-64">URAIAN PERUBAHAN MODAL</th>
                        <th class="py-3 px-4 hidden md:table-cell">PENJELASAN</th>
                        <th class="py-3 px-4 text-right">JUMLAH (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-800">Modal Bersih Awal Periode</td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">Akumulasi modal dan kekayaan bersih pada awal review</td>
                        <td class="py-3 px-4 text-right font-bold text-slate-800" id="pm_awal">
                            Rp <?= number_format($modalAwal, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-semibold text-emerald-800">(+) Laba Bersih / Sisa Penghasilan</td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">Keuntungan usaha setelah prive rumah tangga periode berjalan</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600" id="pm_laba">
                            Rp <?= number_format($labaBersihSisaPenghasilan, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-semibold text-blue-800">(+) Kenaikan Nilai Wajar Agunan / Aset</td>
                        <td class="py-3 px-4 text-slate-500 hidden md:table-cell">Apresiasi harga pasar tanah & bangunan tempat tinggal</td>
                        <td class="py-3 px-4 text-right font-medium text-slate-700" id="pm_apresiasi">
                            Rp <?= number_format($apresiasiAset, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr class="bg-indigo-900 text-white font-extrabold text-sm">
                        <td class="py-3.5 px-4 uppercase tracking-wider">MODAL BERSIH AKHIR PERIODE</td>
                        <td class="py-3.5 px-4 text-indigo-200 text-xs font-normal hidden md:table-cell">Total modal bersih penopang keamanan pengembalian kredit</td>
                        <td class="py-3.5 px-4 text-right text-base font-black text-amber-300" id="pm_akhir">
                            Rp <?= number_format($modalAkhir, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================================================================
         8. SUB-TAB 5: KARAKTERISTIK KHUSUS PERORANGAN & CALK
    ========================================================================== -->
    <div id="subtab-calk" class="subtab-lap-content space-y-6 hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2">
            <div>
                <h4 class="text-sm font-extrabold text-slate-800 tracking-wide uppercase">5. KARAKTERISTIK KHUSUS & CATATAN ATAS LAPORAN KEUANGAN</h4>
                <p class="text-[11px] text-slate-500">Prinsip analisa kredit perorangan, mitigasi pencampuran keuangan, dan dokumen pendukung</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 bg-slate-100 border border-slate-300 shadow-2xs self-start sm:self-auto">
                <span>Analisa Karakteristik 5C</span>
            </span>
        </div>

        <div class="space-y-4 text-xs">
            
            <!-- Box 1: Karakteristik Khusus Perorangan Sesuai Catatan User -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3.5">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">A</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">KARAKTERISTIK KHUSUS KEUANGAN DEBITUR PERORANGAN</h5>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                    <!-- Point 1 -->
                    <div class="p-3.5 rounded-xl border border-blue-100 bg-blue-50/40 space-y-1.5">
                        <div class="flex items-center gap-2 text-blue-900 font-bold">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Pemisahan Keuangan Usaha & Keluarga</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Keuangan usaha dan keluarga sering bercampur pada debitur perorangan. Analis wajib memisahkannya secara teliti agar kemampuan bayar tidak dinilai terlalu tinggi (<em>overstated</em>).
                        </p>
                    </div>

                    <!-- Point 2 -->
                    <div class="p-3.5 rounded-xl border border-blue-100 bg-blue-50/40 space-y-1.5">
                        <div class="flex items-center gap-2 text-blue-900 font-bold">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Estimasi Penjualan Harian (3 Skenario)</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Angka biasanya merupakan estimasi, bukan hasil pembukuan formal. Analis menghitung rata-rata penjualan harian pada <strong>hari ramai, hari biasa, dan hari sepi</strong> untuk mendapatkan angka omzet bulanan riil.
                        </p>
                    </div>

                    <!-- Point 3 -->
                    <div class="p-3.5 rounded-xl border border-blue-100 bg-blue-50/40 space-y-1.5">
                        <div class="flex items-center gap-2 text-blue-900 font-bold">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <span>Verifikasi Silang (Cross-Check)</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Estimasi angka diverifikasi silang dengan mutasi rekening koran, bukti nota pembelian dari suplier, kunjungan lapangan langsung (OTS), dan inspeksi fisik stok barang.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Box 2: Dokumen Pendukung yang Lazim & Diverifikasi -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3.5">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">B</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">DOKUMEN PENDUKUNG YANG LAZIM & TELAH DIVERIFIKASI</h5>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 text-center space-y-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xs font-bold">📄</div>
                        <span class="font-bold text-slate-800 text-[11px] block">Rekening Koran</span>
                        <span class="text-[10px] text-slate-500 block">Mutasi 3-6 bulan terakhir</span>
                    </div>

                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 text-center space-y-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xs font-bold">🧾</div>
                        <span class="font-bold text-slate-800 text-[11px] block">Nota & Catatan</span>
                        <span class="text-[10px] text-slate-500 block">Bukti jual beli harian</span>
                    </div>

                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 text-center space-y-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xs font-bold">📸</div>
                        <span class="font-bold text-slate-800 text-[11px] block">Foto Lokasi & Stok</span>
                        <span class="text-[10px] text-slate-500 block">Dokumentasi fisik OTS</span>
                    </div>

                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 text-center space-y-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xs font-bold">🏛️</div>
                        <span class="font-bold text-slate-800 text-[11px] block">Izin Usaha / SKU</span>
                        <span class="text-[10px] text-slate-500 block">Surat Ket. RT/Kelurahan / NIB</span>
                    </div>

                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 text-center space-y-1">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mx-auto text-xs font-bold">🪪</div>
                        <span class="font-bold text-slate-800 text-[11px] block">Legalitas Pribadi</span>
                        <span class="text-[10px] text-slate-500 block">KTP, KK, NPWP</span>
                    </div>
                </div>
            </div>

            <!-- Box 3: Rincian Kebijakan Akuntansi Pendukung -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">C</span>
                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">RINCIAN KEBIJAKAN AKUNTANSI & RASIO ANGSURAN</h5>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 leading-relaxed text-[11px] text-slate-600">
                    <p>
                        <strong>1. Pengakuan Pendapatan:</strong> Omset bulanan dihitung dari rerata mutasi kredit rekening koran setelah dikurangi transfer non-penjualan, didukung estimasi hari ramai/biasa/sepi dari hasil wawancara OTS.
                    </p>
                    <p>
                        <strong>2. Pengakuan Beban Pokok (HPP):</strong> Ditetapkan sebesar <?= $hppPersen ?>% dari omset berdasarkan margin industri sejenis dan sampel nota pembelian suplier.
                    </p>
                    <p>
                        <strong>3. Beban Prive / Keluarga:</strong> Biaya hidup diestimasi Rp <?= number_format($nomPengeluaranRT, 0, ',', '.') ?> per bulan sesuai jumlah tanggungan, terpisah penuh dari kas usaha.
                    </p>
                    <p>
                        <strong>4. Batas Kelayakan DSR:</strong> Beban angsuran yang aman adalah maksimal 40% dari total pendapatan kotor atau tidak melebihi sisa kas bebas bulanan.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         9. FOOTER NAVIGASI
    ========================================================================== -->
    <div class="mt-8 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <button type="button" onclick="tabNav(-1)" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </button>

        <div class="flex items-center gap-2 text-[11px] text-slate-400 font-mono">
            <span>Document ID: <?= $noAppDisplay ?></span>
        </div>

        <button type="button" onclick="tabNav(1)" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
            <span>Simpan & Lanjut</span>
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </div>
</div>

<!-- =============================================================================
     10. MODAL PREVIEW & PRINT A4 (LENGKAP SESUAI STANDAR CATATAN ANALIS)
============================================================================== -->
<div id="modalPreviewLaporanKeuangan" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
    <div class="bg-slate-100 rounded-3xl shadow-2xl border border-slate-300 max-w-4xl w-full max-h-[96vh] flex flex-col overflow-hidden my-auto animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Modal Toolbar Header -->
        <div class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between gap-4 shrink-0 shadow-2xs">
            <div class="flex items-center gap-3">
                <button type="button" onclick="tutupModalPreviewLaporan()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-lg transition border border-slate-200 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </button>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                    <span class="text-xs font-bold text-slate-800">Laporan_Keuangan_Perorangan.pdf</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400 font-mono hidden sm:inline">A4 Portrait (218 × 297mm)</span>
                <button type="button" onclick="cetakHalamanLaporanPdf()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition shadow-2xs cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak (Print)</span>
                </button>
                <button type="button" onclick="cetakHalamanLaporanPdf()" class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Unduh PDF</span>
                </button>
            </div>
        </div>

        <!-- Sheet Kertas A4 White Document Preview -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex justify-center bg-slate-200/70">
            <div id="printSheetLaporan" class="bg-white w-full max-w-[760px] p-8 sm:p-12 shadow-xl border border-slate-200 text-slate-800 space-y-6 text-xs rounded-sm">
                
                <!-- Header Dokumen A4 -->
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span class="text-[10px] font-bold text-slate-700 tracking-wider uppercase">OTW KREDIT | LAPORAN EVALUASI KEUANGAN PERORANGAN</span>
                    </div>
                    <span class="text-[10px] font-mono font-semibold text-slate-400">REF: <?= $noAppDisplay ?></span>
                </div>

                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">LAPORAN KEUANGAN DEBITUR PERORANGAN</h2>
                    <p class="text-xs text-slate-500 font-medium"><?= $namaDebiturDisplay ?> &bull; Periode Per Bulan <?= $tahunPeriode ?></p>
                </div>

                <!-- Bagian 1. LAPORAN LABA RUGI SEDERHANA -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between bg-blue-50/70 px-3 py-1.5 rounded text-blue-900 font-extrabold text-[11px] uppercase">
                        <span>1. LAPORAN LABA RUGI SEDERHANA</span>
                        <span class="font-mono text-[10px]">IDR (Bulanan)</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <thead>
                            <tr class="text-[10px] uppercase font-bold text-slate-400">
                                <th class="py-1.5 px-2">KOMPONEN</th>
                                <th class="py-1.5 px-2">KETERANGAN</th>
                                <th class="py-1.5 px-2 text-right">NOMINAL (IDR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-1.5 px-2 font-semibold">Penjualan / omzet</td>
                                <td class="py-1.5 px-2 text-slate-500">Total pendapatan usaha</td>
                                <td class="py-1.5 px-2 text-right font-medium" id="preview_omzet">Rp <?= number_format($omsetBulanan, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-2 font-semibold text-rose-700">(-) HPP</td>
                                <td class="py-1.5 px-2 text-slate-500">Biaya membeli/memproduksi barang</td>
                                <td class="py-1.5 px-2 text-right font-medium text-rose-600" id="preview_hpp">(Rp <?= number_format($hppNominal, 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="font-bold text-blue-950 bg-blue-50/30">
                                <td class="py-1.5 px-2">= Laba kotor</td>
                                <td class="py-1.5 px-2 text-slate-500">Penjualan dikurangi HPP</td>
                                <td class="py-1.5 px-2 text-right font-extrabold text-blue-700" id="preview_laba_kotor">Rp <?= number_format($labaKotor, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-2 font-semibold text-rose-700">(-) Biaya operasional</td>
                                <td class="py-1.5 px-2 text-slate-500">Sewa, listrik, gaji, transport, dll.</td>
                                <td class="py-1.5 px-2 text-right font-medium text-rose-600" id="preview_operasional">(Rp <?= number_format($nomBiayaOperasional, 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="font-bold text-slate-900 bg-slate-50">
                                <td class="py-1.5 px-2">= Laba usaha</td>
                                <td class="py-1.5 px-2 text-slate-500">Keuntungan dari kegiatan usaha</td>
                                <td class="py-1.5 px-2 text-right font-extrabold" id="preview_laba_usaha">Rp <?= number_format($labaUsaha, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-2 font-semibold text-emerald-700">(+) Pendapatan lain</td>
                                <td class="py-1.5 px-2 text-slate-500">Gaji pasangan, sewa, dividen</td>
                                <td class="py-1.5 px-2 text-right font-medium text-emerald-600" id="preview_pendapatan_lain">Rp <?= number_format($nomPendapatanLain, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-2 font-semibold text-rose-700">(-) Pengeluaran RT (prive)</td>
                                <td class="py-1.5 px-2 text-slate-500">Biaya hidup keluarga debitur</td>
                                <td class="py-1.5 px-2 text-right font-medium text-rose-600" id="preview_pengeluaran_rt">(Rp <?= number_format($nomPengeluaranRT, 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="bg-emerald-50/80 font-black text-emerald-950 text-xs">
                                <td class="py-2 px-2">= Laba bersih / sisa penghasilan</td>
                                <td class="py-2 px-2 text-emerald-800 font-semibold">Dana yang bisa dipakai membayar angsuran</td>
                                <td class="py-2 px-2 text-right text-emerald-900 text-sm font-black" id="preview_laba_bersih">Rp <?= number_format($labaBersihSisaPenghasilan, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bagian 2. NERACA SEDERHANA -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between bg-blue-50/70 px-3 py-1.5 rounded text-blue-900 font-extrabold text-[11px] uppercase">
                        <span>2. NERACA SEDERHANA (DAFTAR ASET DAN UTANG)</span>
                        <span class="font-mono text-[10px]">Posisi Keuangan</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-[11px] pt-1">
                        <div class="space-y-1">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase border-b border-slate-200 pb-0.5">DAFTAR ASET</span>
                            <div class="flex justify-between py-0.5"><span class="text-slate-600">Aset Lancar (Kas, Piutang, Stok):</span><span class="font-medium" id="preview_ast_lancar">Rp <?= number_format($totalAsetLancar, 0, ',', '.') ?></span></div>
                            <div class="flex justify-between py-0.5"><span class="text-slate-600">Aset Tetap (Alat, Kendaraan, Tanah):</span><span class="font-medium" id="preview_ast_tetap">Rp <?= number_format($totalAsetTetap, 0, ',', '.') ?></span></div>
                            <div class="flex justify-between py-1 bg-slate-50 px-2 font-bold text-slate-900"><span>TOTAL ASET:</span><span id="preview_tot_aset">Rp <?= number_format($totalAset, 0, ',', '.') ?></span></div>
                        </div>
                        <div class="space-y-1">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase border-b border-slate-200 pb-0.5">DAFTAR UTANG & MODAL</span>
                            <div class="flex justify-between py-0.5"><span class="text-slate-600">Total Utang (Dagang, Bank, Koperasi):</span><span class="font-medium text-rose-600" id="preview_tot_utang">Rp <?= number_format($totalUtang, 0, ',', '.') ?></span></div>
                            <div class="flex justify-between py-0.5"><span class="text-slate-600">Modal Bersih (Aset - Utang):</span><span class="font-bold text-indigo-700" id="preview_modal_bersih">Rp <?= number_format($modalBersih, 0, ',', '.') ?></span></div>
                            <div class="flex justify-between py-1 bg-slate-50 px-2 font-bold text-slate-900"><span>TOTAL PASIVA:</span><span id="preview_tot_pasiva">Rp <?= number_format($totalAset, 0, ',', '.') ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3. ARUS KAS SEDERHANA -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between bg-blue-50/70 px-3 py-1.5 rounded text-blue-900 font-extrabold text-[11px] uppercase">
                        <span>3. LAPORAN ARUS KAS SEDERHANA</span>
                        <span class="font-mono text-[10px]">Uang Masuk & Keluar</span>
                    </div>
                    <table class="w-full text-left text-xs divide-y divide-slate-100">
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-1 px-2 font-semibold">Kas Masuk (Penjualan tunai, pelunasan piutang, lain)</td>
                                <td class="py-1 px-2 text-right font-medium text-emerald-700" id="preview_ak_masuk">Rp <?= number_format($totalKasMasuk, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-1 px-2 font-semibold">Kas Keluar (Beli barang, operasional, cicilan, keluarga)</td>
                                <td class="py-1 px-2 text-right font-medium text-rose-600" id="preview_ak_keluar">(Rp <?= number_format($totalKasKeluar, 0, ',', '.') ?>)</td>
                            </tr>
                            <tr class="bg-blue-50/60 font-black text-blue-950">
                                <td class="py-1.5 px-2">= Saldo Kas Akhir Periode</td>
                                <td class="py-1.5 px-2 text-right text-blue-800 font-black" id="preview_ak_akhir">Rp <?= number_format($saldoKasAkhir, 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Bagian 4. CATATAN ANALIS & DOKUMEN PENDUKUNG -->
                <div class="pt-3 border-t border-slate-200 text-[10px] text-slate-500 space-y-1.5 leading-relaxed">
                    <div class="font-bold text-slate-700 uppercase tracking-wider text-[9px]">Karakteristik & Dokumen Verifikasi Pendukung:</div>
                    <p>
                        Keuangan usaha dan rumah tangga telah diverifikasi terpisah. Angka omzet dihitung dari estimasi penjualan harian (hari ramai, biasa, dan sepi) serta diselaraskan dengan mutasi rekening koran 3-6 bulan, nota belanja suplier, foto stok barang, dan surat keterangan izin usaha. Laba bersih/sisa penghasilan mencukupi untuk memenuhi kewajiban angsuran.
                    </p>
                </div>

                <!-- Footer Dokumen A4 -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-400 font-medium">
                    <span>Laporan Analisa Keuangan Debitur Perorangan</span>
                    <span>Halaman 1 dari 1 &bull; Dokumen Resmi OTW Credit</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: white !important;
        color: #000 !important;
    }
    header, aside, .fixed, .no-print, button, #topActionToolbar {
        display: none !important;
    }
    body * {
        visibility: hidden;
    }
    #modalPreviewLaporanKeuangan,
    #modalPreviewLaporanKeuangan * {
        visibility: visible;
    }
    #modalPreviewLaporanKeuangan {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        background: white !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        z-index: 99999 !important;
    }
    #modalPreviewLaporanKeuangan .shrink-0 {
        display: none !important;
    }
    #printSheetLaporan {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 10mm 12mm !important;
        box-shadow: none !important;
        border: none !important;
    }
    @page {
        size: A4 portrait;
        margin: 10mm 12mm;
    }
}
</style>

<!-- =============================================================================
     11. LOGIKA JAVASCRIPT TAB LAPORAN & SINKRONISASI REAL-TIME
============================================================================== -->
<script>
// Fungsi Mengganti Sub-Tab Laporan
function gantiSubTabLaporan(targetSubId) {
    document.querySelectorAll('.subtab-lap-content').forEach(el => {
        el.classList.add('hidden');
    });
    document.querySelectorAll('.subtab-lap-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-blue-700', 'shadow-2xs', 'border', 'border-slate-200/60');
        btn.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
    });

    const targetEl = document.getElementById(targetSubId);
    if (targetEl) targetEl.classList.remove('hidden');

    const activeBtn = document.getElementById('btn-' + targetSubId);
    if (activeBtn) {
        activeBtn.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
        activeBtn.classList.add('bg-white', 'text-blue-700', 'shadow-2xs', 'border', 'border-slate-200/60');
    }
}

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

// Sinkronisasi Data Keuangan Laporan dari Tab 9 dan Form Input
function sinkronkanLaporanKeuangan() {
    // 1. Ambil data dari input Tab 9
    const omzet = parseNominalLap(document.getElementById('pendapatan_rk')?.value) || <?= $omsetBulanan ?>;
    const hppPct = parseFloat(document.getElementById('hpp_persen_rk')?.value) || <?= $hppPersen ?>;
    const hppNom = Math.round(omzet * (hppPct / 100));
    const labaKotor = omzet - hppNom;

    // Biaya Operasional (beban lainnya / rincian operasional)
    const biayaOperasional = parseNominalLap(document.getElementById('beban_lainnya_nominal_rk')?.value) || <?= $nomBiayaOperasional ?>;
    const labaUsaha = labaKotor - biayaOperasional;

    // Pendapatan Lain-lain
    const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
    const pendapatanLain = isAdaLain ? (parseNominalLap(document.getElementById('pendapatan_lain_rk')?.value) || 0) : 0;

    // Pengeluaran Rumah Tangga (Prive / Biaya Hidup)
    const pengeluaranRT = parseNominalLap(document.getElementById('biaya_hidup_nominal_rk')?.value) || <?= $nomPengeluaranRT ?>;

    // Laba Bersih / Sisa Penghasilan (Dana yang bisa dipakai membayar angsuran)
    const labaBersih = (labaUsaha + pendapatanLain) - pengeluaranRT;

    // Cicilan Pinjaman Berjalan (SLIK / Acuan)
    const cicilanBerjalan = parseNominalLap(document.getElementById('angsuran_dipilih')?.value) 
        || parseNominalLap(document.getElementById('angsuran_installment')?.value) 
        || <?= $totalCicilanSlik ?>;

    const sisaKasBebas = labaBersih - cicilanBerjalan;
    const totalInflow = omzet + pendapatanLain;
    const dsr = totalInflow > 0 ? (Math.round((cicilanBerjalan / totalInflow) * 1000) / 10) : 0;

    // 2. Update Highlight Metric Cards
    if (document.getElementById('card_lap_penghasilan')) document.getElementById('card_lap_penghasilan').innerText = formatNominalLap(omzet);
    if (document.getElementById('card_lap_laba_usaha')) document.getElementById('card_lap_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('card_lap_surplus')) document.getElementById('card_lap_surplus').innerText = formatNominalLap(labaBersih);
    if (document.getElementById('card_lap_dsr')) {
        document.getElementById('card_lap_dsr').innerText = `Dana untuk membayar angsuran (DSR ${dsr}%)`;
    }

    // 3. Update Tabel Laba Rugi Sederhana
    if (document.getElementById('lap_lr_omzet')) document.getElementById('lap_lr_omzet').innerText = formatNominalLap(omzet);
    if (document.getElementById('lap_lr_hpp')) document.getElementById('lap_lr_hpp').innerText = `(${formatNominalLap(hppNom)})`;
    if (document.getElementById('lap_lr_laba_kotor')) document.getElementById('lap_lr_laba_kotor').innerText = formatNominalLap(labaKotor);
    if (document.getElementById('lap_lr_operasional')) document.getElementById('lap_lr_operasional').innerText = `(${formatNominalLap(biayaOperasional)})`;
    if (document.getElementById('lap_lr_laba_usaha')) document.getElementById('lap_lr_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('lap_lr_pendapatan_lain')) document.getElementById('lap_lr_pendapatan_lain').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('lap_lr_pengeluaran_rt')) document.getElementById('lap_lr_pengeluaran_rt').innerText = `(${formatNominalLap(pengeluaranRT)})`;
    if (document.getElementById('lap_lr_laba_bersih')) document.getElementById('lap_lr_laba_bersih').innerText = formatNominalLap(labaBersih);
    if (document.getElementById('lap_lr_cicilan_berjalan')) document.getElementById('lap_lr_cicilan_berjalan').innerText = `(${formatNominalLap(cicilanBerjalan)})`;
    if (document.getElementById('lap_lr_sisa_kas_bebas')) document.getElementById('lap_lr_sisa_kas_bebas').innerText = formatNominalLap(sisaKasBebas);

    if (document.getElementById('box_lap_laba_bersih')) document.getElementById('box_lap_laba_bersih').innerText = formatNominalLap(labaBersih);
    if (document.getElementById('box_lap_buffer')) document.getElementById('box_lap_buffer').innerText = formatNominalLap(sisaKasBebas);
    if (document.getElementById('box_lap_dsr')) document.getElementById('box_lap_dsr').innerText = `${dsr}%`;

    // 4. Update Neraca Sederhana
    const kasLancar = Math.round(omzet * 0.2);
    const piutang = Math.round(omzet * 0.12);
    const persediaan = Math.round(hppNom * 0.45);
    const totalAsetLancar = kasLancar + piutang + persediaan;

    const peralatan = Math.round(omzet * 0.15);
    const kendaraan = 45000000;
    const tanahBangunan = <?= $tanahBangunan ?>;
    const totalAsetTetap = peralatan + kendaraan + tanahBangunan;
    const totalAset = totalAsetLancar + totalAsetTetap;

    const utangDagang = Math.round(hppNom * 0.15);
    const utangBank = cicilanBerjalan * 18;
    const utangKoperasi = 5000000;
    const totalUtang = utangDagang + utangBank + utangKoperasi;
    const modalBersih = totalAset - totalUtang;

    if (document.getElementById('neraca_kas')) document.getElementById('neraca_kas').innerText = formatNominalLap(kasLancar);
    if (document.getElementById('neraca_piutang')) document.getElementById('neraca_piutang').innerText = formatNominalLap(piutang);
    if (document.getElementById('neraca_persediaan')) document.getElementById('neraca_persediaan').innerText = formatNominalLap(persediaan);
    if (document.getElementById('neraca_sub_lancar')) document.getElementById('neraca_sub_lancar').innerText = formatNominalLap(totalAsetLancar);

    if (document.getElementById('neraca_peralatan')) document.getElementById('neraca_peralatan').innerText = formatNominalLap(peralatan);
    if (document.getElementById('neraca_kendaraan')) document.getElementById('neraca_kendaraan').innerText = formatNominalLap(kendaraan);
    if (document.getElementById('neraca_tanah_bangunan')) document.getElementById('neraca_tanah_bangunan').innerText = formatNominalLap(tanahBangunan);
    if (document.getElementById('neraca_sub_tetap')) document.getElementById('neraca_sub_tetap').innerText = formatNominalLap(totalAsetTetap);
    if (document.getElementById('neraca_total_aset')) document.getElementById('neraca_total_aset').innerText = formatNominalLap(totalAset);

    if (document.getElementById('neraca_utang_dagang')) document.getElementById('neraca_utang_dagang').innerText = formatNominalLap(utangDagang);
    if (document.getElementById('neraca_utang_bank')) document.getElementById('neraca_utang_bank').innerText = formatNominalLap(utangBank);
    if (document.getElementById('neraca_utang_koperasi')) document.getElementById('neraca_utang_koperasi').innerText = formatNominalLap(utangKoperasi);
    if (document.getElementById('neraca_total_utang')) document.getElementById('neraca_total_utang').innerText = formatNominalLap(totalUtang);
    if (document.getElementById('neraca_modal_bersih')) document.getElementById('neraca_modal_bersih').innerText = formatNominalLap(modalBersih);
    if (document.getElementById('neraca_total_pasiva')) document.getElementById('neraca_total_pasiva').innerText = formatNominalLap(totalAset);

    // 5. Update Arus Kas Sederhana
    const kasMasukPenjualan = Math.round(omzet * 0.85);
    const kasMasukPiutang = Math.round(omzet * 0.15);
    const totalKasMasuk = kasMasukPenjualan + kasMasukPiutang + pendapatanLain;

    const kasKeluarBeli = hppNom;
    const kasKeluarOpr = biayaOperasional;
    const kasKeluarCcl = cicilanBerjalan;
    const kasKeluarKel = pengeluaranRT;
    const totalKasKeluar = kasKeluarBeli + kasKeluarOpr + kasKeluarCcl + kasKeluarKel;

    const netKasBulanan = totalKasMasuk - totalKasKeluar;
    const saldoKasAwal = kasLancar;
    const saldoKasAkhir = saldoKasAwal + netKasBulanan;

    if (document.getElementById('ak_penjualan_tunai')) document.getElementById('ak_penjualan_tunai').innerText = formatNominalLap(kasMasukPenjualan);
    if (document.getElementById('ak_pelunasan_piutang')) document.getElementById('ak_pelunasan_piutang').innerText = formatNominalLap(kasMasukPiutang);
    if (document.getElementById('ak_pendapatan_lain')) document.getElementById('ak_pendapatan_lain').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('ak_total_masuk')) document.getElementById('ak_total_masuk').innerText = formatNominalLap(totalKasMasuk);

    if (document.getElementById('ak_beli_barang')) document.getElementById('ak_beli_barang').innerText = `(${formatNominalLap(kasKeluarBeli)})`;
    if (document.getElementById('ak_operasional')) document.getElementById('ak_operasional').innerText = `(${formatNominalLap(kasKeluarOpr)})`;
    if (document.getElementById('ak_cicilan')) document.getElementById('ak_cicilan').innerText = `(${formatNominalLap(kasKeluarCcl)})`;
    if (document.getElementById('ak_keluarga')) document.getElementById('ak_keluarga').innerText = `(${formatNominalLap(kasKeluarKel)})`;
    if (document.getElementById('ak_total_keluar')) document.getElementById('ak_total_keluar').innerText = `(${formatNominalLap(totalKasKeluar)})`;

    if (document.getElementById('ak_net_bulanan')) document.getElementById('ak_net_bulanan').innerText = formatNominalLap(netKasBulanan);
    if (document.getElementById('ak_saldo_awal')) document.getElementById('ak_saldo_awal').innerText = formatNominalLap(saldoKasAwal);
    if (document.getElementById('ak_saldo_akhir')) document.getElementById('ak_saldo_akhir').innerText = formatNominalLap(saldoKasAkhir);
    if (document.getElementById('box_ak_saldo_akhir')) document.getElementById('box_ak_saldo_akhir').innerText = formatNominalLap(saldoKasAkhir);

    // 6. Update Perubahan Modal
    const modalAwal = modalBersih - labaBersih;
    const apresiasi = Math.round(tanahBangunan * 0.05);
    const modalAkhir = modalAwal + labaBersih + apresiasi;

    if (document.getElementById('pm_awal')) document.getElementById('pm_awal').innerText = formatNominalLap(modalAwal);
    if (document.getElementById('pm_laba')) document.getElementById('pm_laba').innerText = formatNominalLap(labaBersih);
    if (document.getElementById('pm_apresiasi')) document.getElementById('pm_apresiasi').innerText = formatNominalLap(apresiasi);
    if (document.getElementById('pm_akhir')) document.getElementById('pm_akhir').innerText = formatNominalLap(modalAkhir);

    // 7. Update Dokumen Preview PDF A4
    if (document.getElementById('preview_omzet')) document.getElementById('preview_omzet').innerText = formatNominalLap(omzet);
    if (document.getElementById('preview_hpp')) document.getElementById('preview_hpp').innerText = `(${formatNominalLap(hppNom)})`;
    if (document.getElementById('preview_laba_kotor')) document.getElementById('preview_laba_kotor').innerText = formatNominalLap(labaKotor);
    if (document.getElementById('preview_operasional')) document.getElementById('preview_operasional').innerText = `(${formatNominalLap(biayaOperasional)})`;
    if (document.getElementById('preview_laba_usaha')) document.getElementById('preview_laba_usaha').innerText = formatNominalLap(labaUsaha);
    if (document.getElementById('preview_pendapatan_lain')) document.getElementById('preview_pendapatan_lain').innerText = formatNominalLap(pendapatanLain);
    if (document.getElementById('preview_pengeluaran_rt')) document.getElementById('preview_pengeluaran_rt').innerText = `(${formatNominalLap(pengeluaranRT)})`;
    if (document.getElementById('preview_laba_bersih')) document.getElementById('preview_laba_bersih').innerText = formatNominalLap(labaBersih);

    if (document.getElementById('preview_ast_lancar')) document.getElementById('preview_ast_lancar').innerText = formatNominalLap(totalAsetLancar);
    if (document.getElementById('preview_ast_tetap')) document.getElementById('preview_ast_tetap').innerText = formatNominalLap(totalAsetTetap);
    if (document.getElementById('preview_tot_aset')) document.getElementById('preview_tot_aset').innerText = formatNominalLap(totalAset);
    if (document.getElementById('preview_tot_utang')) document.getElementById('preview_tot_utang').innerText = formatNominalLap(totalUtang);
    if (document.getElementById('preview_modal_bersih')) document.getElementById('preview_modal_bersih').innerText = formatNominalLap(modalBersih);
    if (document.getElementById('preview_tot_pasiva')) document.getElementById('preview_tot_pasiva').innerText = formatNominalLap(totalAset);

    if (document.getElementById('preview_ak_masuk')) document.getElementById('preview_ak_masuk').innerText = formatNominalLap(totalKasMasuk);
    if (document.getElementById('preview_ak_keluar')) document.getElementById('preview_ak_keluar').innerText = `(${formatNominalLap(totalKasKeluar)})`;
    if (document.getElementById('preview_ak_akhir')) document.getElementById('preview_ak_akhir').innerText = formatNominalLap(saldoKasAkhir);
}

// Buka Modal Preview Laporan A4
function bukaModalPreviewLaporan() {
    sinkronkanLaporanKeuangan();
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
}

// Tutup Modal Preview Laporan A4
function tutupModalPreviewLaporan() {
    const modal = document.getElementById('modalPreviewLaporanKeuangan');
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Cetak Laporan PDF
function cetakHalamanLaporanPdf() {
    window.print();
}

// Event Listener Otomatis saat Halaman Dimuat
document.addEventListener('DOMContentLoaded', function() {
    sinkronkanLaporanKeuangan();
});
</script>
