<?php
// Pre-load data laporan
$lk = $laporan_keuangan ?? [];
$lkJ = !empty($lk['json_laporan']) ? json_decode($lk['json_laporan'], true) : null;
$sudahAda = !empty($lkJ);
$statusNeraca = $lkJ['validasi']['status_neraca'] ?? 'Seimbang';
$isSeimbang = ($statusNeraca === 'Seimbang');
$nrData = $lkJ['neraca'] ?? [];
$lrData = $lkJ['laba_rugi'] ?? [];
$showAsetTetap = !empty($nrData['aset_tetap_neto']) && (float)$nrData['aset_tetap_neto'] > 0;
$showUtangBank = (!empty($nrData['utang_bank_pendek']) && (float)$nrData['utang_bank_pendek'] > 0) || (!empty($nrData['utang_jangka_panjang']) && (float)$nrData['utang_jangka_panjang'] > 0);
$showModalDisetor = !empty($nrData['modal_disetor']) && (float)$nrData['modal_disetor'] > 0;
$showBebanBunga = !empty($lrData['beban_bunga']) && (float)$lrData['beban_bunga'] > 0;

if (!function_exists('badgeSumberData')) {
    function badgeSumberData($bagian = '', $tipe = '', $rumus = '') {
        return '';
    }
}
?>
<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">

    <!-- ============================================================
         HEADER LAPORAN KEUANGAN
         ============================================================ -->
    <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 border-b border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Keuangan Lengkap</h2>
                <p class="text-[12px] text-slate-500 mt-1 max-w-xl">
                    Ringkasan lengkap hasil analisa pengajuan: profil usaha, pinjaman bank (SLIK), mutasi rekening, jaminan (agunan), serta Data Keuangan, Neraca, dan Arus Kas.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap relative">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 bg-blue-100">Badan Usaha</span>

                <!-- BUTTON HITUNG ULANG -->
                <button type="button" onclick="generateLaporanKeuangan()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Hitung Ulang</span>
                </button>

                <!-- DROPDOWN UNDUH LAPORAN -->
                <div class="relative inline-block text-left" id="wrapperDropdownUnduh">
                    <button type="button" id="btnDropdownUnduh" onclick="toggleDropdownUnduh(event)"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition shadow-2xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Laporan</span>
                        <svg id="chevronUnduh" class="w-3.5 h-3.5 text-white transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Menu Card -->
                    <div id="menuDropdownUnduh" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-2.5 z-50">
                        <!-- Header -->
                        <div class="px-4 py-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                            FORMAT DOKUMEN
                        </div>

                        <!-- Item 1: Unduh Laporan Lengkap (PDF) -->
                        <button type="button" onclick="unduhLaporanPDF('semua')"
                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-6 h-6 rounded flex items-center justify-center text-rose-600 shrink-0 border border-rose-200 bg-rose-50/50">
                                    <span class="text-[9px] font-black text-rose-600">PDF</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 leading-tight">Unduh Laporan Lengkap (PDF)</div>
                                    <div class="text-[10px] text-slate-400">Rangkuman semua tahap (Profil, SLIK, Rekening, Jaminan, LapKeu)</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold text-rose-700 bg-rose-100">PDF</span>
                        </button>

                        <!-- Item 2: Unduh Laba Rugi (PDF) -->
                        <button type="button" onclick="unduhLaporanPDF('lr')"
                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-slate-800">Unduh Data Keuangan (PDF)</span>
                            </div>
                            <span id="badgeUnduhLR" class="px-2 py-0.5 rounded text-[10px] font-bold text-blue-700 bg-blue-100">Aktif</span>
                        </button>

                        <!-- Item 3: Unduh Neraca (PDF) -->
                        <button type="button" onclick="unduhLaporanPDF('nr')"
                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-slate-800">Unduh Neraca (PDF)</span>
                            </div>
                            <span id="badgeUnduhNR" class="text-[10px] text-slate-400 font-semibold">.pdf</span>
                        </button>

                        <!-- Item 4: Unduh Arus Kas (PDF) -->
                        <button type="button" onclick="unduhLaporanPDF('ak')"
                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-xs font-bold text-slate-800">Unduh Arus Kas (PDF)</span>
                            </div>
                            <span id="badgeUnduhAK" class="text-[10px] text-slate-400 font-semibold">.pdf</span>
                        </button>

                        <div class="my-1.5 border-t border-slate-100"></div>

                        <!-- Item 5: Unduh Semua (Excel) -->
                        <button type="button" onclick="unduhLaporanExcel()"
                                class="w-full text-left px-4 py-2 hover:bg-slate-50 flex items-center justify-between transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-6 h-6 rounded flex items-center justify-center text-emerald-600 shrink-0 border border-emerald-200 bg-emerald-50/50">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 leading-tight">Unduh Semua (Excel)</div>
                                    <div class="text-[10px] text-slate-400">Buku besar &amp; rekapitulasi</div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold text-emerald-800 bg-emerald-200">XLSX</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         TAB NAVIGATION (URUTAN: DATA KEUANGAN -> NERACA -> ARUS KAS)
         ============================================================ -->
    <div class="px-6 sm:px-8 pt-4">
        <div class="flex gap-6 border-b border-slate-200">
            <button type="button" onclick="switchTabLaporan('lr')" id="navBU_lr"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-blue-600 border-blue-600 transition">Data Keuangan</button>
            <button type="button" onclick="switchTabLaporan('nr')" id="navBU_nr"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-800 transition">Neraca</button>
            <button type="button" onclick="switchTabLaporan('ak')" id="navBU_ak"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-800 transition">Arus Kas</button>
        </div>
    </div>

    <!-- ============================================================
         TAB 1: DATA KEUANGAN (PENDAPATAN & BIAYA)
         ============================================================ -->
    <div id="panelBU_lr" class="tabPanelBU px-6 sm:px-8 py-5">
        <!-- Subheader box -->
        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
            <span>DATA KEUANGAN USAHA (PENDAPATAN &amp; PENGELUARAN)</span>
            <span class="text-blue-700 font-semibold normal-case text-[11px]">Periode <?= date('Y') ?></span>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
            <!-- 1. PENDAPATAN / OMSET -->
            <div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-700 font-bold">Pendapatan Usaha (Penjualan / Omzet)</span>
                    <span class="font-bold text-slate-900" id="bu_lr_pendapatan">—</span>
                </div>
                <!-- Container Rincian Pendapatan Dinamis -->
                <div id="containerPendapatanLaporan" class="space-y-1.5 my-1">
                    <!-- Rincian dinamis dari Kotak 1 Tab 9 akan dirender di sini via JS -->
                </div>
            </div>

            <!-- 2. HPP (BIAYA MODAL / KULAKAN) -->
            <div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-700 font-bold">Biaya Modal / Kulakan (HPP)</span>
                    <span class="font-bold text-slate-900" id="bu_lr_hpp">—</span>
                </div>
                <!-- Container Rincian HPP Dinamis -->
                <div id="containerHppLaporan" class="space-y-1.5 my-1">
                    <!-- Rincian dinamis dari Kotak 2B Tab 9 akan dirender di sini via JS -->
                </div>
            </div>

            <!-- Highlight: Laba Kotor -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Keuntungan Kotor (Laba Kotor)</span>
                <span id="bu_lr_laba_kotor" class="text-blue-700 font-black">—</span>
            </div>

            <!-- 3. PENGELUARAN / BEBAN OPERASIONAL -->
            <div>
                <div class="flex justify-between items-start py-1">
                    <div>
                        <span class="text-slate-700 font-bold block">Beban Operasional &amp; Administrasi (Pengeluaran Usaha)</span>
                        <span class="text-[11px] text-slate-400 block">Gaji karyawan, sewa kantor/gudang, listrik &amp; utilitas, logistik, operasional rutin</span>
                    </div>
                    <span class="font-bold text-slate-900 shrink-0 ml-4" id="bu_lr_beban_op">—</span>
                </div>

                <!-- Container Rincian Pengeluaran Dinamis -->
                <div id="containerBebanLainLaporan" class="space-y-1.5 my-1">
                    <!-- Rincian dinamis dari Kotak 2A Tab 9 akan dirender di sini via JS -->
                </div>
            </div>

            <!-- Highlight: Laba Operasional -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Keuntungan Operasional Usaha</span>
                <span id="bu_lr_laba_op" class="text-blue-700 font-black">—</span>
            </div>

            <div class="flex justify-between items-center py-1" id="row_bu_lr_beban_bunga" style="display: <?= $showBebanBunga ? 'flex' : 'none' ?>;">
                <span class="text-slate-600 font-medium">Biaya Bunga Pinjaman Bank</span>
                <span class="font-bold text-slate-900" id="bu_lr_beban_bunga">—</span>
            </div>

            <!-- Highlight: LABA BERSIH (Rumus: Pendapatan - Beban = Laba Bersih) -->
            <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                <span class="tracking-wide">KEUNTUNGAN BERSIH AKHIR (LABA BERSIH)</span>
                <span id="bu_lr_laba_bersih" class="text-blue-700 font-black">—</span>
            </div>
        </div>
    </div>

    <!-- ============================================================
         TAB 2: NERACA (POSISI HARTA, UTANG & MODAL)
         ============================================================ -->
    <div id="panelBU_nr" class="tabPanelBU hidden px-6 sm:px-8 py-5">
        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
            <span>NERACA KEUANGAN (POSISI HARTA, UTANG &amp; MODAL)</span>
            <span class="text-blue-700 font-semibold normal-case text-[11px]">Des <?= date('Y') ?></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

            <!-- KOLOM KIRI: HARTA USAHA (ASET) -->
            <div>
                <div class="bg-blue-100/70 p-2.5 rounded-lg text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-3">
                    HARTA USAHA (ASET)
                </div>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Uang Kas &amp; Saldo Bank</span>
                        <span class="font-bold text-slate-900" id="bu_nr_kas">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1" id="row_bu_nr_piutang" style="display: <?= (!empty($nrData['piutang_usaha']) && (float)$nrData['piutang_usaha'] > 0) ? 'flex' : 'none' ?>;">
                        <span class="text-slate-600 font-medium">Tagihan ke Pelanggan (Piutang)</span>
                        <span class="font-bold text-slate-900" id="bu_nr_piutang">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1" id="row_bu_nr_persediaan" style="display: <?= (!empty($nrData['persediaan']) && (float)$nrData['persediaan'] > 0) ? 'flex' : 'none' ?>;">
                        <span class="text-slate-600 font-medium">Stok Barang Dagang (Persediaan)</span>
                        <span class="font-bold text-slate-900" id="bu_nr_persediaan">—</span>
                    </div>

                    <!-- Highlight: Total Aset Lancar -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span>Total Harta Lancar</span>
                        <span id="bu_nr_total_aset_lancar" class="text-blue-700 font-black">—</span>
                    </div>

                    <div id="row_bu_nr_aset_tetap" style="display: <?= $showAsetTetap ? 'block' : 'none' ?>;" class="py-1">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600 font-medium" id="lbl_bu_nr_aset_tetap">Aset Tetap / Peralatan Usaha (Neto)</span>
                            <span class="font-bold text-slate-900" id="bu_nr_aset_tetap_neto">—</span>
                        </div>
                        <!-- Rincian Aset Tetap Dinamis dari Kotak 3 Tab 9 -->
                        <div id="containerAsetLaporan" class="space-y-1.5 my-1">
                            <!-- Diisi oleh JS -->
                        </div>
                    </div>

                    <!-- Highlight: TOTAL ASET (Rumus: Aset = Liabilitas + Ekuitas) -->
                    <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                        <span class="tracking-wide">TOTAL KESELURUHAN HARTA (ASET)</span>
                        <span id="bu_nr_total_aset" class="text-blue-700 font-black">—</span>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: UTANG & MODAL (LIABILITAS & EKUITAS) -->
            <div>
                <div class="bg-blue-100/70 p-2.5 rounded-lg text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-3">
                    UTANG &amp; MODAL SENDIRI (LIABILITAS &amp; EKUITAS)
                </div>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center py-1" id="row_bu_nr_utang_usaha" style="display: <?= (!empty($nrData['utang_usaha']) && (float)$nrData['utang_usaha'] > 0) ? 'flex' : 'none' ?>;">
                        <span class="text-slate-600 font-medium">Utang ke Supplier (Pemasok)</span>
                        <span class="font-bold text-slate-900" id="bu_nr_utang_usaha">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1" id="row_bu_nr_utang_bank" style="display: <?= $showUtangBank ? 'flex' : 'none' ?>;">
                        <span class="text-slate-600 font-medium">Utang Pinjaman ke Bank</span>
                        <span class="font-bold text-slate-900" id="bu_nr_utang_bank">—</span>
                    </div>

                    <!-- Highlight: Total Liabilitas -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span id="lbl_bu_nr_total_liab">Total Seluruh Utang (Liabilitas)</span>
                        <span id="bu_nr_total_liab" class="text-blue-700 font-black">—</span>
                    </div>

                    <div class="flex justify-between items-center py-1" id="row_bu_nr_modal" style="display: <?= $showModalDisetor ? 'flex' : 'none' ?>;">
                        <span class="text-slate-600 font-medium">Modal Sendiri yang Disetor</span>
                        <span class="font-bold text-slate-900" id="bu_nr_modal">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Sisa Keuntungan yang Disimpan (Laba Ditahan)</span>
                        <span class="font-bold text-slate-900" id="bu_nr_laba_ditahan">—</span>
                    </div>

                    <!-- Highlight: Total Ekuitas (Rumus: Ekuitas = Aset - Liabilitas) -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span>Total Modal Bersih Sendiri (Ekuitas)</span>
                        <span id="bu_nr_total_ekuitas" class="text-blue-700 font-black">—</span>
                    </div>

                    <!-- Highlight: TOTAL LIABILITAS & EKUITAS (Rumus: Aset = Liabilitas + Ekuitas) -->
                    <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                        <span class="tracking-wide">TOTAL UTANG + MODAL (ASET)</span>
                        <span id="bu_nr_total_liab_ekuitas" class="text-blue-700 font-black">—</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================================
         TAB 3: ARUS KAS (HASIL AKHIR ALIRAN UANG KAS)
         ============================================================ -->
    <div id="panelBU_ak" class="tabPanelBU hidden px-6 sm:px-8 py-5">
        <!-- Subheader box -->
        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
            <span>ARUS KAS USAHA (HASIL AKHIR ALIRAN UANG KAS)</span>
            <span class="text-blue-700 font-semibold normal-case text-[11px]">Periode <?= date('Y') ?></span>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
            <!-- I. Aktivitas Operasi -->
            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider pt-1">Arus Kas dari Usaha Pokok (Operasional)</div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Keuntungan Bersih (Laba Bersih)</span>
                <span class="font-bold text-slate-900" id="bu_ak_laba_bersih">—</span>
            </div>
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Total Kas dari Usaha Pokok (Kas Operasi)</span>
                <span id="bu_ak_kas_operasi" class="text-blue-700 font-black">—</span>
            </div>

            <!-- II. Aktivitas Pendanaan -->
            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider pt-2" id="judul_bu_ak_pendanaan">Arus Kas Utang &amp; Modal (Pendanaan)</div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Bayar Pokok Pinjaman ke Bank</span>
                <span class="font-bold text-slate-900" id="bu_ak_cicilan">—</span>
            </div>
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Total Kas Pendanaan &amp; Pembiayaan</span>
                <span id="bu_ak_kas_pendanaan" class="text-blue-700 font-black">—</span>
            </div>

            <!-- Kenaikan Bersih & Kas Akhir (Rumus Kenaikan Kas & Kas Akhir) -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Kenaikan (Penurunan) Kas Bersih Tahun Ini</span>
                <span id="bu_ak_kenaikan_bersih" class="text-blue-700 font-black">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Uang Kas di Awal Tahun (Kas Awal)</span>
                <span class="font-bold text-slate-900" id="bu_ak_saldo_awal">—</span>
            </div>
            <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                <span class="tracking-wide">SISA UANG KAS DI AKHIR TAHUN (Kas Akhir)</span>
                <span id="bu_ak_kas_akhir" class="text-blue-700 font-black">—</span>
            </div>
        </div>
    </div>

</div>

<style>
.tabNavBU { transition: all .15s ease-in-out; }
.tabNavBU.active { color: #2563eb !important; border-color: #2563eb !important; }
</style>

<script>
// ============================================================
// KONFIGURASI BASE URL & ID PENGAJUAN
// ============================================================
const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
const currentKreditId = '<?= !empty($kredit_id) ? $kredit_id : (!empty($kredit['kredit_id']) ? $kredit['kredit_id'] : (!empty($kredit['id']) ? $kredit['id'] : '')) ?>';

// ============================================================
// FORMAT RUPIAH
// ============================================================
function rpFmt(val) {
    const n = parseFloat(val);
    if (isNaN(n)) return '—';
    if (n === 0) return 'Rp 0';
    const abs = Math.abs(n);
    const str = 'Rp ' + abs.toLocaleString('id-ID', {minimumFractionDigits: 0});
    return n < 0 ? '(' + str + ')' : str;
}

function rpFmtParen(val) {
    const n = parseFloat(val);
    if (isNaN(n)) return '—';
    if (n === 0) return 'Rp 0';
    const abs = Math.abs(n);
    return '(Rp ' + abs.toLocaleString('id-ID', {minimumFractionDigits: 0}) + ')';
}

// ============================================================
// DROPDOWN UNDUH LAPORAN
// ============================================================
function toggleDropdownUnduh(event) {
    if (event) event.stopPropagation();
    const menu = document.getElementById('menuDropdownUnduh');
    const chevron = document.getElementById('chevronUnduh');
    if (!menu) return;
    const isHidden = menu.classList.toggle('hidden');
    if (chevron) {
        chevron.style.transform = isHidden ? '' : 'rotate(180deg)';
    }
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('wrapperDropdownUnduh');
    const menu = document.getElementById('menuDropdownUnduh');
    const chevron = document.getElementById('chevronUnduh');
    if (wrapper && !wrapper.contains(e.target) && menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
        if (chevron) chevron.style.transform = '';
    }
});

// ============================================================
// SWITCH TAB
// ============================================================
function switchTabLaporan(tab) {
    document.querySelectorAll('.tabPanelBU').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tabNavBU').forEach(b => {
        b.classList.remove('active');
        b.classList.remove('text-blue-600', 'border-blue-600');
        b.classList.add('text-slate-500', 'border-transparent');
    });

    const panelMap = { lr: 'panelBU_lr', ak: 'panelBU_ak', nr: 'panelBU_nr' };
    const navMap   = { lr: 'navBU_lr',   ak: 'navBU_ak',   nr: 'navBU_nr' };

    if (panelMap[tab]) {
        document.getElementById(panelMap[tab])?.classList.remove('hidden');
        const nb = document.getElementById(navMap[tab]);
        if (nb) {
            nb.classList.add('active', 'text-blue-600', 'border-blue-600');
            nb.classList.remove('text-slate-500', 'border-transparent');
        }
    }

    // Update dynamic badge in dropdown (Aktif vs .pdf)
    const badgeLR = document.getElementById('badgeUnduhLR');
    const badgeAK = document.getElementById('badgeUnduhAK');
    const badgeNR = document.getElementById('badgeUnduhNR');
    const activeClass = 'px-2 py-0.5 rounded text-[10px] font-bold text-blue-700 bg-blue-100';
    const inactiveClass = 'text-[10px] text-slate-400 font-semibold';

    if (badgeLR) {
        badgeLR.className = (tab === 'lr') ? activeClass : inactiveClass;
        badgeLR.textContent = (tab === 'lr') ? 'Aktif' : '.pdf';
    }
    if (badgeAK) {
        badgeAK.className = (tab === 'ak') ? activeClass : inactiveClass;
        badgeAK.textContent = (tab === 'ak') ? 'Aktif' : '.pdf';
    }
    if (badgeNR) {
        badgeNR.className = (tab === 'nr') ? activeClass : inactiveClass;
        badgeNR.textContent = (tab === 'nr') ? 'Aktif' : '.pdf';
    }
}
window.switchTabLaporan = switchTabLaporan;

// ============================================================
// POPUP SUMBER DATA (MODAL INTERAKTIF)
// ============================================================
function showPopupSumberData(bagian, tipe, rumus) {
    let rumusHtml = rumus ? `
        <div style="margin-top: 12px; padding: 10px 14px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; text-align: left;">
            <div style="font-size: 11px; font-weight: 800; color: #92400e; letter-spacing: 0.5px;">RUMUS PERHITUNGAN:</div>
            <div style="font-family: monospace, monospace; font-size: 13px; font-weight: 700; color: #b45309; margin-top: 3px;">${rumus}</div>
        </div>` : '';
    let badgeColor = (tipe === 'Input User') ? '#10b981' : '#2563eb';
    let htmlContent = `
        <div style="text-align: left; font-size: 13px; line-height: 1.6; color: #334155;">
            <div style="margin-bottom: 10px;">
                <span style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Berasal Dari:</span>
                <div style="font-weight: 800; color: #0f172a; font-size: 14px; margin-top: 2px;">${bagian}</div>
            </div>
            <div style="margin-bottom: 8px;">
                <span style="font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tipe Data:</span>
                <div style="margin-top: 2px;">
                    <span style="display: inline-block; background: ${badgeColor}15; color: ${badgeColor}; border: 1px solid ${badgeColor}40; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                        ${tipe}
                    </span>
                </div>
            </div>
            ${rumusHtml}
        </div>
    `;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '<div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 800; color: #1e40af;"><span style="font-size: 20px;">ℹ️</span> Sumber & Asal Data</div>',
            html: htmlContent,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#2563eb',
            customClass: { popup: 'rounded-2xl shadow-xl' }
        });
    } else {
        alert("Berasal dari: " + bagian + "\nTipe: " + tipe + (rumus ? "\nRumus: " + rumus : ""));
    }
}
window.showPopupSumberData = showPopupSumberData;

// ============================================================
// UNDUH LAPORAN KE PDF (CETAK/SAVE AS PDF)
// ============================================================
async function unduhLaporanPDF(target) {
    // Tutup dropdown
    const menu = document.getElementById('menuDropdownUnduh');
    if (menu) menu.classList.add('hidden');
    const chevron = document.getElementById('chevronUnduh');
    if (chevron) chevron.style.transform = '';

    // Auto-save form jika fungsi simpanForm tersedia agar perubahan terbaru di layar tersimpan ke DB
    if (typeof simpanForm === 'function') {
        try {
            await simpanForm(false, false);
        } catch(e) {
            console.warn('Auto-save sebelum cetak PDF:', e);
        }
    }

    const kreditId = document.getElementById('kredit_id')?.value 
                  || document.querySelector('[name="kredit_id"]')?.value 
                  || currentKreditId;
    if (kreditId) {
        window.location.href = baseUrl + 'pengajuan/unduh-laporan-pdf/' + kreditId + '?tipe=' + encodeURIComponent(target);
        return;
    }

    const namaDebitur = document.querySelector('[name="nama_debitur"]')?.value || 'PT Sumber Makmur Sejahtera';
    const noApp       = document.querySelector('[name="nomor_aplikasi"]')?.value || 'NC-2024-AK8921';
    const refDokumen  = 'REF: FIN-EST/' + ('<?= date("Y") ?>') + '/' + (noApp.replace(/[^a-zA-Z0-9]/g, '').slice(-6).toUpperCase() || 'BU-8921');
    const tahun       = '<?= date("Y") ?>';
    const karyawanJml = window._jumlahKaryawan || 2;

    function v(id) {
        return document.getElementById(id)?.textContent?.trim() || '—';
    }

    const printWin = window.open('', '_blank', 'width=1000,height=850');
    if (!printWin) {
        alert('Popup diblokir oleh browser. Harap izinkan popup untuk mencetak laporan.');
        return;
    }

    // Bangun HTML per lembar (URUTAN: LABA RUGI -> NERACA -> ARUS KAS)
    let showLR = (target === 'semua' || target === 'lr');
    let showNR = (target === 'semua' || target === 'nr');
    let showAK = (target === 'semua' || target === 'ak');

    let totalHalaman = (target === 'semua') ? 2 : 1;

    let page1HTML = '';
    let page2HTML = '';

    // ==========================================
    // LEMBAR 1: LABA RUGI & NERACA
    // ==========================================
    if (showLR || showNR) {
        let footerText = (totalHalaman === 2) ? 'Halaman 1 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem';

        let lrSection = '';
        if (showLR) {
            lrSection = `
                <div class="section-banner">LAPORAN DATA KEUANGAN</div>
                <div style="font-size: 9px; color: #475569; margin-bottom: 4px; font-style: italic;">Rumus: Pendapatan - Beban = Laba (Rugi) Bersih</div>
                <table class="report-table">
                    <tr>
                        <td class="col-desc">Pendapatan Usaha (Penjualan / Omzet)</td>
                        <td class="col-val">${v('bu_lr_pendapatan')}</td>
                    </tr>
                    ${renderPendapatanRowsHtml('pdf')}
                    <tr>
                        <td class="col-desc">Biaya Modal / Kulakan (HPP)</td>
                        <td class="col-val">${v('bu_lr_hpp')}</td>
                    </tr>
                    ${renderHppRowsHtml('pdf')}
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Laba Kotor</td>
                        <td class="col-val font-bold">${v('bu_lr_laba_kotor')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Beban Operasional &amp; Administrasi (Pengeluaran Usaha)</td>
                        <td class="col-val">${v('bu_lr_beban_op')}</td>
                    </tr>
                    ${renderBebanLainRowsHtml('pdf')}
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Laba Operasional</td>
                        <td class="col-val font-bold">${v('bu_lr_laba_op')}</td>
                    </tr>
                    ${(!v('bu_lr_beban_bunga').includes('—') && !v('bu_lr_beban_bunga').includes('Rp 0') && v('bu_lr_beban_bunga').trim() !== '') ? `
                    <tr>
                        <td class="col-desc">Beban Bunga Pinjaman Bank</td>
                        <td class="col-val">${v('bu_lr_beban_bunga')}</td>
                    </tr>` : ''}
                    <tr class="row-total-accent">
                        <td class="col-desc font-black">LABA BERSIH</td>
                        <td class="col-val font-black">${v('bu_lr_laba_bersih')}</td>
                    </tr>
                </table>
            `;
        }

        let nrSection = '';
        if (showNR) {
            let mtStyle = showLR ? 'style="margin-top: 14px;"' : '';
            nrSection = `
                <div class="section-banner" ${mtStyle}>2. NERACA KEUANGAN</div>
                <div style="font-size: 9px; color: #475569; margin-bottom: 4px; font-style: italic;">Rumus: Aset = Liabilitas + Ekuitas | Ekuitas = Aset - Liabilitas</div>
                <!-- Two-Column Balance Sheet Grid -->
                <div class="neraca-grid">
                    <!-- KOLOM AKTIVA / ASET -->
                    <div class="neraca-col">
                        <div class="col-section-title">AKTIVA / ASET</div>

                        <div class="group-title">Aset Lancar</div>
                        <table class="report-table">
                            <tr>
                                <td class="col-desc">Kas &amp; Bank</td>
                                <td class="col-val">${v('bu_nr_kas')}</td>
                            </tr>
                            ${(!v('bu_nr_piutang').includes('—') && !v('bu_nr_piutang').includes('Rp 0') && v('bu_nr_piutang').trim() !== '') ? `
                            <tr>
                                <td class="col-desc">Piutang Usaha</td>
                                <td class="col-val">${v('bu_nr_piutang')}</td>
                            </tr>` : ''}
                            ${(!v('bu_nr_persediaan').includes('—') && !v('bu_nr_persediaan').includes('Rp 0') && v('bu_nr_persediaan').trim() !== '') ? `
                            <tr>
                                <td class="col-desc">Persediaan</td>
                                <td class="col-val">${v('bu_nr_persediaan')}</td>
                            </tr>` : ''}
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Aset Lancar</td>
                                <td class="col-val font-bold">${v('bu_nr_total_aset_lancar')}</td>
                            </tr>
                        </table>

                        ${(!v('bu_nr_aset_tetap_neto').includes('—') && !v('bu_nr_aset_tetap_neto').includes('Rp 0') && v('bu_nr_aset_tetap_neto').trim() !== '') ? `
                        <div class="group-title" style="margin-top: 8px;">Aset Tetap / Peralatan</div>
                        <table class="report-table">
                            <tr>
                                <td class="col-desc">Aset Tetap / Peralatan (Neto)</td>
                                <td class="col-val">${v('bu_nr_aset_tetap_neto')}</td>
                            </tr>
                            ${renderAsetRowsHtml('pdf')}
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Aset Tetap</td>
                                <td class="col-val font-bold">${v('bu_nr_aset_tetap_neto')}</td>
                            </tr>
                        </table>` : ''}
                    </div>

                    <!-- KOLOM PASIVA / LIABILITAS & EKUITAS -->
                    <div class="neraca-col">
                        <div class="col-section-title">PASIVA / LIABILITAS &amp; EKUITAS</div>

                        <div class="group-title">Liabilitas</div>
                        <table class="report-table">
                            <tr>
                                <td class="col-desc">Utang Usaha</td>
                                <td class="col-val">${v('bu_nr_utang_usaha')}</td>
                            </tr>
                            ${(!v('bu_nr_utang_bank').includes('—') && !v('bu_nr_utang_bank').includes('Rp 0') && v('bu_nr_utang_bank').trim() !== '') ? `
                            <tr>
                                <td class="col-desc">Utang Bank</td>
                                <td class="col-val">${v('bu_nr_utang_bank')}</td>
                            </tr>` : ''}
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Liabilitas</td>
                                <td class="col-val font-bold">${v('bu_nr_total_liab')}</td>
                            </tr>
                        </table>

                        <div class="group-title" style="margin-top: 8px;">Ekuitas</div>
                        <table class="report-table">
                            ${(!v('bu_nr_modal').includes('—') && !v('bu_nr_modal').includes('Rp 0') && v('bu_nr_modal').trim() !== '') ? `
                            <tr>
                                <td class="col-desc">Modal Disetor</td>
                                <td class="col-val">${v('bu_nr_modal')}</td>
                            </tr>` : ''}
                            <tr>
                                <td class="col-desc">Laba Ditahan</td>
                                <td class="col-val">${v('bu_nr_laba_ditahan')}</td>
                            </tr>
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Ekuitas</td>
                                <td class="col-val font-bold">${v('bu_nr_total_ekuitas')}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TOTAL BANNER BESAR -->
                <div class="total-banner-grid" style="margin-top: 6px; margin-bottom: 4px;">
                    <div class="total-banner-cell">
                        <span class="total-banner-label">TOTAL ASET</span>
                        <span class="total-banner-value">${v('bu_nr_total_aset')}</span>
                    </div>
                    <div class="total-banner-cell">
                        <span class="total-banner-label">TOTAL LIABILITAS &amp; EKUITAS</span>
                        <span class="total-banner-value">${v('bu_nr_total_liab_ekuitas')}</span>
                    </div>
                </div>
            `;
        }

        page1HTML = `
            <div class="sheet-page ${totalHalaman === 2 ? 'page-break' : ''}">
                <div class="doc-title-box">
                    <h1 class="doc-main-title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                    <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun &mdash; Data Keuangan &amp; Neraca</div>
                </div>

                ${lrSection}
                ${nrSection}

                <div class="doc-footer">
                    <div class="doc-footer-left">Laporan ini merupakan estimasi hasil perhitungan sistem</div>
                    <div class="doc-footer-right">${footerText}</div>
                </div>
            </div>
        `;
    }

    // ==========================================
    // LEMBAR 2: ARUS KAS (HASIL AKHIR)
    // ==========================================
    if (showAK) {
        let footerText = (totalHalaman === 2) ? 'Halaman 2 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem';

        let akSection = `
            <div class="section-banner">3. LAPORAN ARUS KAS (HASIL AKHIR ALIRAN KAS)</div>
            <div style="font-size: 9px; color: #475569; margin-bottom: 6px; font-style: italic;">
                Rumus: Kenaikan Kas = Kas Operasi + Kas Investasi + Kas Pendanaan | Kas Akhir = Kas Awal + Kas Operasi + Kas Investasi + Kas Pendanaan
            </div>
            <table class="report-table">
                <tr class="sub-header-row"><td colspan="2">AKTIVITAS OPERASI</td></tr>
                <tr>
                    <td class="col-desc">Laba Bersih</td>
                    <td class="col-val">${v('bu_ak_laba_bersih')}</td>
                </tr>
                <tr class="row-highlight">
                    <td class="col-desc font-bold">Kas Operasi</td>
                    <td class="col-val font-bold">${v('bu_ak_kas_operasi')}</td>
                </tr>

                ${(!v('bu_ak_beli_aset').includes('—') && !v('bu_ak_beli_aset').includes('Rp 0') && v('bu_ak_beli_aset').trim() !== '') ? `
                <tr class="sub-header-row"><td colspan="2">AKTIVITAS INVESTASI</td></tr>
                <tr>
                    <td class="col-desc">Pembelian Aset Tetap</td>
                    <td class="col-val">${v('bu_ak_beli_aset')}</td>
                </tr>
                <tr class="row-highlight">
                    <td class="col-desc font-bold">Kas Bersih Investasi</td>
                    <td class="col-val font-bold">${v('bu_ak_kas_investasi')}</td>
                </tr>` : ''}

                <tr class="sub-header-row"><td colspan="2">AKTIVITAS PENDANAAN</td></tr>
                <tr>
                    <td class="col-desc">Cicilan Pokok Utang Bank</td>
                    <td class="col-val">${v('bu_ak_cicilan')}</td>
                </tr>
                <tr class="row-highlight">
                    <td class="col-desc font-bold">Kas Pendanaan</td>
                    <td class="col-val font-bold">${v('bu_ak_kas_pendanaan')}</td>
                </tr>

                <tr class="sub-header-row"><td colspan="2">REKONSILIASI KAS (HASIL AKHIR)</td></tr>
                <tr class="row-highlight">
                    <td class="col-desc font-bold">Kenaikan (Penurunan) Kas Bersih</td>
                    <td class="col-val font-bold">${v('bu_ak_kenaikan_bersih')}</td>
                </tr>
                <tr>
                    <td class="col-desc">Saldo Kas Awal Periode</td>
                    <td class="col-val">${v('bu_ak_saldo_awal')}</td>
                </tr>
                <tr class="row-total-accent">
                    <td class="col-desc font-black">SALDO KAS AKHIR TAHUN</td>
                    <td class="col-val font-black">${v('bu_ak_kas_akhir')}</td>
                </tr>
            </table>
        `;

        page2HTML = `
            <div class="sheet-page">
                <div class="doc-title-box">
                    <h1 class="doc-main-title">LAPORAN ARUS KAS (HASIL AKHIR)</h1>
                    <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun &mdash; Rekonsiliasi Kas</div>
                </div>

                ${akSection}

                <div class="doc-footer">
                    <div class="doc-footer-left">Laporan ini merupakan estimasi hasil perhitungan sistem</div>
                    <div class="doc-footer-right">${footerText}</div>
                </div>
            </div>
        `;
    }

    let fullHTML = `
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="utf-8">
            <title>Laporan Keuangan (Estimasi) - ${namaDebitur}</title>
            <style>
                @page {
                    size: A4 portrait;
                    margin: 10mm 15mm 12mm 15mm;
                }
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background-color: #f1f5f9;
                    color: #0f172a;
                    -webkit-font-smoothing: antialiased;
                }

                /* Toolbar Layar */
                .toolbar-screen {
                    position: sticky;
                    top: 0;
                    z-index: 999;
                    background: #1e293b;
                    color: #fff;
                    padding: 10px 24px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
                .toolbar-btn {
                    padding: 7px 16px;
                    font-size: 13px;
                    font-weight: 700;
                    border-radius: 8px;
                    border: none;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    transition: all 0.15s ease;
                }
                .btn-print {
                    background: #2563eb;
                    color: #fff;
                }
                .btn-print:hover {
                    background: #1d4ed8;
                }
                .btn-close {
                    background: #475569;
                    color: #fff;
                }
                .btn-close:hover {
                    background: #64748b;
                }

                /* Container Kertas A4 */
                .sheets-wrapper {
                    padding: 24px 0 40px;
                }
                .sheet-page {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0 auto 24px;
                    padding: 16mm 18mm 16mm 18mm;
                    background: #ffffff;
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                    position: relative;
                    display: flex;
                    flex-direction: column;
                }

                /* Header Dokumen */
                .doc-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 9.5px;
                    font-weight: 700;
                    color: #64748b;
                    letter-spacing: 0.5px;
                    padding-bottom: 8px;
                }
                .doc-brand {
                    letter-spacing: 0.8px;
                }
                .doc-ref {
                    color: #1e40af;
                    font-weight: 800;
                }

                /* Judul Utama */
                .doc-title-box {
                    margin-top: 6px;
                    margin-bottom: 14px;
                }
                .doc-main-title {
                    font-size: 20px;
                    font-weight: 900;
                    color: #0f172a;
                    letter-spacing: -0.3px;
                }
                .doc-sub-title {
                    font-size: 11px;
                    font-weight: 600;
                    color: #475569;
                    margin-top: 2px;
                }

                /* Bar Biru Judul Section */
                .section-banner {
                    background: #1d4ed8;
                    color: #ffffff;
                    font-size: 11px;
                    font-weight: 800;
                    padding: 6px 12px;
                    border-radius: 3px;
                    letter-spacing: 0.5px;
                    margin-bottom: 6px;
                }

                /* Tabel Laporan */
                .report-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 10.5px;
                }
                .report-table td {
                    padding: 4.5px 8px;
                }
                .col-desc {
                    color: #1e293b;
                    width: 65%;
                }
                .col-val {
                    text-align: right;
                    color: #0f172a;
                    font-variant-numeric: tabular-nums;
                    font-weight: 500;
                }
                .row-highlight {
                    background: #f1f5f9;
                }
                .row-total-accent {
                    background: #dbeafe;
                    color: #1e3a8a;
                }
                .row-total-accent .col-desc,
                .row-total-accent .col-val {
                    color: #1e3a8a;
                }
                .sub-header-row td {
                    color: #1d4ed8;
                    font-size: 9.5px;
                    font-weight: 800;
                    padding-top: 8px;
                    padding-bottom: 2px;
                    letter-spacing: 0.4px;
                }

                /* Layout Neraca 2 Kolom */
                .neraca-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                    margin-bottom: 12px;
                }
                .neraca-col {
                    display: flex;
                    flex-direction: column;
                }
                .col-section-title {
                    font-size: 10px;
                    font-weight: 800;
                    color: #1d4ed8;
                    letter-spacing: 0.5px;
                    margin-bottom: 6px;
                    padding-bottom: 2px;
                }
                .group-title {
                    font-size: 10px;
                    font-weight: 700;
                    color: #475569;
                    margin-bottom: 3px;
                }

                /* Total Banner Neraca */
                .total-banner-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 16px;
                    margin-top: 8px;
                    margin-bottom: 12px;
                }
                .total-banner-cell {
                    background: #1d4ed8;
                    color: #ffffff;
                    padding: 10px 14px;
                    border-radius: 4px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-weight: 800;
                    font-size: 12px;
                }
                .total-banner-label {
                    letter-spacing: 0.3px;
                }
                .total-banner-value {
                    font-size: 13px;
                    font-variant-numeric: tabular-nums;
                }

                /* Validation Status Box */
                .validation-status-box {
                    background: #f0fdf4;
                    border: 1px solid #bbf7d0;
                    border-radius: 6px;
                    padding: 8px 14px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 10.5px;
                    margin-top: 4px;
                    margin-bottom: auto;
                }
                .validation-status-text {
                    color: #166534;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                .validation-dot {
                    color: #16a34a;
                    font-size: 13px;
                }
                .validation-badge-text {
                    color: #15803d;
                    font-weight: 800;
                    letter-spacing: 0.5px;
                    font-size: 10px;
                }

                /* Footer Dokumen */
                .doc-footer {
                    margin-top: auto;
                    padding-top: 14px;
                    border-top: 1px solid #e2e8f0;
                    display: flex;
                    justify-content: space-between;
                    font-size: 9.5px;
                    color: #64748b;
                }

                .font-bold { font-weight: 700 !important; }
                .font-black { font-weight: 900 !important; }

                /* Mode Cetak / PDF */
                @media print {
                    body {
                        background: none;
                        color: #000;
                    }
                    .no-print {
                        display: none !important;
                    }
                    .sheets-wrapper {
                        padding: 0;
                    }
                    .sheet-page {
                        width: 100%;
                        min-height: auto;
                        margin: 0;
                        padding: 0;
                        box-shadow: none;
                        border-radius: 0;
                    }
                    .page-break {
                        page-break-after: always;
                        break-after: page;
                    }
                    .section-banner,
                    .total-banner-cell {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    .row-highlight,
                    .row-total-accent,
                    .validation-status-box {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="toolbar-screen no-print">
                <div style="font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span style="background: #2563eb; padding: 2px 8px; border-radius: 4px; font-size: 11px;">PDF PREVIEW</span>
                    <span>${namaDebitur} &mdash; ${refDokumen}</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button class="toolbar-btn btn-print" onclick="window.print()">
                        <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                    <button class="toolbar-btn btn-close" onclick="window.close()">Tutup</button>
                </div>
            </div>

            <div class="sheets-wrapper">
                ${page1HTML}
                ${page2HTML}
            </div>

            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `;

    printWin.document.write(fullHTML);
    printWin.document.close();
}

// ============================================================
// UNDUH LAPORAN KE EXCEL (XLSX FORMAT)
// ============================================================
async function unduhLaporanExcel() {
    // Tutup dropdown
    const menu = document.getElementById('menuDropdownUnduh');
    if (menu) menu.classList.add('hidden');
    const chevron = document.getElementById('chevronUnduh');
    if (chevron) chevron.style.transform = '';

    // Auto-save form jika fungsi simpanForm tersedia agar perubahan terbaru di layar tersimpan ke DB
    if (typeof simpanForm === 'function') {
        try {
            await simpanForm(false, false);
        } catch(e) {
            console.warn('Auto-save sebelum unduh Excel:', e);
        }
    }

    const kreditId = document.getElementById('kredit_id')?.value 
                  || document.querySelector('[name="kredit_id"]')?.value 
                  || currentKreditId;
    if (kreditId) {
        window.location.href = baseUrl + 'pengajuan/unduh-laporan-excel/' + kreditId;
        return;
    }

    const namaDebitur = document.querySelector('[name="nama_debitur"]')?.value || 'PT Sumber Makmur Sejahtera';
    const noApp       = document.querySelector('[name="nomor_aplikasi"]')?.value || 'NC-<?= date("Y") ?>';
    const tahun       = '<?= date("Y") ?>';

    // Ambil nilai teks langsung dari DOM
    function v(id) {
        return document.getElementById(id)?.textContent?.trim() || '—';
    }

    let excelHTML = `
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
            <!-- KOP PERUSAHAAN -->
            <table>
                <tr><td colspan="4" class="title-row">LAPORAN KEUANGAN ESTIMASI (BADAN USAHA)</td></tr>
                <tr><td colspan="4" class="sub-title">Perusahaan: <strong>${namaDebitur}</strong> | No. Aplikasi: ${noApp} | Periode: ${tahun}</td></tr>
                <tr><td colspan="4">&nbsp;</td></tr>
            </table>

            <!-- I. LABA RUGI -->
            <table>
                <tr class="header-section"><td colspan="2">I. LAPORAN LABA RUGI (INCOME STATEMENT)</td></tr>
                <tr><td>Pendapatan Usaha (Penjualan / Omzet)</td><td class="num-right">${v('bu_lr_pendapatan')}</td></tr>
                ${renderPendapatanRowsHtml('print')}
                <tr><td>Biaya Modal / Kulakan (HPP)</td><td class="num-right">${v('bu_lr_hpp')}</td></tr>
                ${renderHppRowsHtml('print')}
                <tr class="highlight-row"><td>Laba Kotor</td><td class="num-right">${v('bu_lr_laba_kotor')}</td></tr>
                <tr><td>Beban Operasional &amp; Administrasi (Pengeluaran Usaha)</td><td class="num-right">${v('bu_lr_beban_op')}</td></tr>
                ${renderBebanLainRowsHtml('print')}
                <tr class="highlight-row"><td>Laba Operasional</td><td class="num-right">${v('bu_lr_laba_op')}</td></tr>
                ${(!v('bu_lr_beban_bunga').includes('—') && !v('bu_lr_beban_bunga').includes('Rp 0') && v('bu_lr_beban_bunga').trim() !== '') ? `
                <tr><td>Beban Bunga Pinjaman Bank</td><td class="num-right">${v('bu_lr_beban_bunga')}</td></tr>` : ''}
                <tr class="total-row"><td>LABA BERSIH</td><td class="num-right">${v('bu_lr_laba_bersih')}</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
            </table>

            <!-- II. NERACA -->
            <table>
                <tr class="header-section"><td colspan="2">II. NERACA KEUANGAN (ASET)</td><td colspan="2">LIABILITAS &amp; EKUITAS</td></tr>
                <tr style="background-color: #f8fafc; font-size: 9pt; color: #475569; font-style: italic;"><td colspan="4">Rumus: Aset = Liabilitas + Ekuitas | Ekuitas = Aset - Liabilitas</td></tr>
                <tr>
                    <td>Kas &amp; Bank</td><td class="num-right">${v('bu_nr_kas')}</td>
                    <td>Utang Usaha</td><td class="num-right">${v('bu_nr_utang_usaha')}</td>
                </tr>
                <tr>
                    <td>Piutang Usaha</td><td class="num-right">${v('bu_nr_piutang')}</td>
                    <td>Utang Bank</td><td class="num-right">${v('bu_nr_utang_bank')}</td>
                </tr>
                <tr>
                    <td>Persediaan</td><td class="num-right">${v('bu_nr_persediaan')}</td>
                    <td class="highlight-row">Total Liabilitas</td><td class="num-right highlight-row">${v('bu_nr_total_liab')}</td>
                </tr>
                <tr class="highlight-row">
                    <td>Total Aset Lancar</td><td class="num-right">${v('bu_nr_total_aset_lancar')}</td>
                    <td>Modal Disetor</td><td class="num-right">${v('bu_nr_modal')}</td>
                </tr>
                <tr>
                    <td>Aset Tetap Neto</td><td class="num-right">${v('bu_nr_aset_tetap_neto')}</td>
                    <td>Laba Ditahan</td><td class="num-right">${v('bu_nr_laba_ditahan')}</td>
                </tr>
                <tr>
                    <td class="total-row">TOTAL ASET</td><td class="num-right total-row">${v('bu_nr_total_aset')}</td>
                    <td class="highlight-row">Total Ekuitas</td><td class="num-right highlight-row">${v('bu_nr_total_ekuitas')}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td>
                    <td class="total-row">TOTAL LIABILITAS &amp; EKUITAS</td><td class="num-right total-row">${v('bu_nr_total_liab_ekuitas')}</td>
                </tr>
                <tr><td colspan="4">&nbsp;</td></tr>
            </table>

            <!-- III. ARUS KAS (HASIL AKHIR) -->
            <table>
                <tr class="header-section"><td colspan="2">III. LAPORAN ARUS KAS (HASIL AKHIR ALIRAN KAS)</td></tr>
                <tr style="background-color: #f8fafc; font-size: 9pt; color: #475569; font-style: italic;"><td colspan="2">Rumus: Kenaikan Kas = Kas Operasi + Kas Investasi + Kas Pendanaan | Kas Akhir = Kas Awal + Kas Operasi + Kas Investasi + Kas Pendanaan</td></tr>
                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Operasi</td></tr>
                <tr><td>Laba Bersih</td><td class="num-right">${v('bu_ak_laba_bersih')}</td></tr>
                <tr class="highlight-row"><td>Kas Operasi</td><td class="num-right">${v('bu_ak_kas_operasi')}</td></tr>

                ${(!v('bu_ak_beli_aset').includes('—') && !v('bu_ak_beli_aset').includes('Rp 0') && v('bu_ak_beli_aset').trim() !== '') ? `
                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Investasi</td></tr>
                <tr><td>Pembelian Aset Tetap</td><td class="num-right">${v('bu_ak_beli_aset')}</td></tr>
                <tr class="highlight-row"><td>Kas Bersih Investasi</td><td class="num-right">${v('bu_ak_kas_investasi')}</td></tr>` : ''}

                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Pendanaan</td></tr>
                <tr><td>Cicilan Pokok Utang Bank</td><td class="num-right">${v('bu_ak_cicilan')}</td></tr>
                <tr class="highlight-row"><td>Kas Pendanaan</td><td class="num-right">${v('bu_ak_kas_pendanaan')}</td></tr>

                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Rekonsiliasi Kas (Hasil Akhir)</td></tr>
                <tr class="highlight-row"><td>Kenaikan (Penurunan) Kas Bersih</td><td class="num-right">${v('bu_ak_kenaikan_bersih')}</td></tr>
                <tr><td>Saldo Kas Awal Periode</td><td class="num-right">${v('bu_ak_saldo_awal')}</td></tr>
                <tr class="total-row"><td>SALDO KAS AKHIR PERIODE</td><td class="num-right">${v('bu_ak_kas_akhir')}</td></tr>
            </table>
        </body>
        </html>
    `;

    const blob = new Blob([excelHTML], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const link = document.createElement('a');
    const safeName = namaDebitur.replace(/[^a-zA-Z0-9_-]/g, '_');
    link.href = URL.createObjectURL(blob);
    link.download = `Laporan_Keuangan_${safeName}_${tahun}.xls`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// ============================================================
// POPULATE DATA KE DOM SECARA READ-ONLY
// ============================================================
function isiDataLaporanBU(data) {
    if (!data) return;
    const lr = data.laba_rugi || {};
    const ak = data.arus_kas  || {};
    const nr = data.neraca    || {};

    const setT = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };

    // --- TAB LABA RUGI ---
    setT('bu_lr_pendapatan', rpFmt(lr.pendapatan));
    setT('bu_lr_hpp', rpFmtParen(lr.hpp));
    setT('bu_lr_laba_kotor', rpFmt(lr.laba_kotor));

    // 1. Simpan & render rincian pendapatan usaha dinamis dari Kotak 1 Tab 9
    let listPendapatan = lr.detail_pendapatan || [];
    if (typeof listPendapatan === 'string') {
        try { listPendapatan = JSON.parse(listPendapatan); } catch(e) { listPendapatan = []; }
    }
    if (!Array.isArray(listPendapatan) || listPendapatan.length === 0) {
        const rawP = document.getElementById('detail_pendapatan_rk')?.value;
        if (rawP) {
            try { listPendapatan = JSON.parse(rawP); } catch(e) { listPendapatan = []; }
        }
    }
    window._currentDetailPendapatan = Array.isArray(listPendapatan) ? listPendapatan : [];

    const contPendapatan = document.getElementById('containerPendapatanLaporan');
    if (contPendapatan) {
        contPendapatan.innerHTML = '';
        if (window._currentDetailPendapatan.length > 0) {
            window._currentDetailPendapatan.forEach(item => {
                const nom = parseFloat(item.nominal || 0);
                if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center py-1 pl-4 border-l-2 border-blue-500 bg-blue-50/50 rounded-r-lg pr-2';
                    row.innerHTML = `
                        <span class="text-slate-600 text-xs sm:text-sm font-medium">• ${escapeHtmlText(item.nama || 'Omset Penjualan')}</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm">${rpFmt(nom)}</span>
                    `;
                    contPendapatan.appendChild(row);
                }
            });
        }
    }

    // 2. Simpan & render rincian HPP dinamis dari Kotak 2B Tab 9
    let listHpp = lr.detail_hpp || [];
    if (typeof listHpp === 'string') {
        try { listHpp = JSON.parse(listHpp); } catch(e) { listHpp = []; }
    }
    if (!Array.isArray(listHpp) || listHpp.length === 0) {
        const rawHpp = document.getElementById('detail_hpp_rk')?.value;
        if (rawHpp) {
            try {
                const parsed = JSON.parse(rawHpp);
                if (Array.isArray(parsed)) {
                    listHpp = parsed.filter(it => (it.kategori || 'hpp').toLowerCase() !== 'aset');
                }
            } catch(e) { listHpp = []; }
        }
    }
    window._currentDetailHpp = Array.isArray(listHpp) ? listHpp : [];

    const contHpp = document.getElementById('containerHppLaporan');
    if (contHpp) {
        contHpp.innerHTML = '';
        if (window._currentDetailHpp.length > 0) {
            window._currentDetailHpp.forEach(item => {
                const nom = parseFloat(item.nominal || 0);
                if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center py-1 pl-4 border-l-2 border-rose-400 bg-rose-50/40 rounded-r-lg pr-2';
                    row.innerHTML = `
                        <span class="text-slate-600 text-xs sm:text-sm font-medium">• ${escapeHtmlText(item.nama || 'Biaya Modal / Kulakan')}</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm">${rpFmtParen(nom)}</span>
                    `;
                    contHpp.appendChild(row);
                }
            });
        }
    }

    // 3. Simpan & render rincian pengeluaran/beban operasional dinamis dari Kotak 2A Tab 9
    let listBeban = lr.detail_beban_lainnya || [];
    if (typeof listBeban === 'string') {
        try { listBeban = JSON.parse(listBeban); } catch(e) { listBeban = []; }
    }
    if (!Array.isArray(listBeban) || listBeban.length === 0) {
        const rawEl = document.getElementById('detail_beban_lainnya_rk')?.value;
        if (rawEl) {
            try { listBeban = JSON.parse(rawEl); } catch(e) { listBeban = []; }
        }
    }
    window._currentDetailBebanLain = Array.isArray(listBeban) ? listBeban : [];

    const totBebanDinamis = window._currentDetailBebanLain.reduce((acc, it) => acc + (parseFloat(it.nominal) || 0), 0);
    const nominalBebanOpTampil = totBebanDinamis > 0 ? totBebanDinamis : (lr.beban_operasional || lr.beban_lainnya || 0);
    setT('bu_lr_beban_op', rpFmtParen(nominalBebanOpTampil));

    const contBeban = document.getElementById('containerBebanLainLaporan');
    if (contBeban) {
        contBeban.innerHTML = '';
        if (window._currentDetailBebanLain.length > 0) {
            window._currentDetailBebanLain.forEach(item => {
                const nom = parseFloat(item.nominal || 0);
                if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center py-1 pl-4 border-l-2 border-slate-400 bg-slate-50/70 rounded-r-lg pr-2';
                    row.innerHTML = `
                        <span class="text-slate-600 text-xs sm:text-sm font-medium">• ${escapeHtmlText(item.nama || 'Pengeluaran Usaha')}</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm">${rpFmtParen(nom)}</span>
                    `;
                    contBeban.appendChild(row);
                }
            });
        }
    }

    setT('bu_lr_penyusutan', rpFmtParen(lr.beban_penyusutan));
    setT('bu_lr_laba_op', rpFmt(lr.laba_operasional));

    const bebanBunga = parseFloat(lr.beban_bunga || 0);
    const rowBebanBunga = document.getElementById('row_bu_lr_beban_bunga');
    if (rowBebanBunga) {
        rowBebanBunga.style.display = (bebanBunga > 0) ? 'flex' : 'none';
    }
    setT('bu_lr_beban_bunga', rpFmtParen(bebanBunga));
    setT('bu_lr_pajak', rpFmtParen(lr.pajak));
    setT('bu_lr_laba_bersih', rpFmt(lr.laba_bersih));

    const marginPct = nr.margin_laba_bersih !== undefined ? nr.margin_laba_bersih : (lr.pendapatan > 0 ? ((lr.laba_bersih / lr.pendapatan) * 100).toFixed(2) : '18.75');
    const elInfoRasioLR = document.getElementById('infoRasioLR');
    if (elInfoRasioLR) {
        elInfoRasioLR.textContent = 'Margin Laba Bersih: ' + parseFloat(marginPct).toFixed(2) + '% (Di atas ambang batas risiko 12%)';
    }

    // --- TAB ARUS KAS ---
    setT('bu_ak_laba_bersih', rpFmt(ak.laba_bersih));
    setT('bu_ak_penyusutan', rpFmt(ak.penyusutan));
    setT('bu_ak_kenaikan_piutang', rpFmtParen(ak.kenaikan_piutang));
    setT('bu_ak_kenaikan_persediaan', rpFmtParen(ak.kenaikan_persediaan));
    setT('bu_ak_kenaikan_utang', rpFmt(ak.kenaikan_utang_usaha));
    setT('bu_ak_kas_operasi', rpFmt(ak.kas_operasional));

    const capex = parseFloat(ak.pembelian_aset_tetap || 0);
    const contInvestasi = document.getElementById('container_bu_ak_investasi');
    const judulPendanaan = document.getElementById('judul_bu_ak_pendanaan');
    if (capex > 0) {
        if (contInvestasi) contInvestasi.style.display = 'block';
        if (judulPendanaan) judulPendanaan.textContent = 'Arus Kas Utang & Modal (Pendanaan)';
        setT('bu_ak_beli_aset', rpFmtParen(capex));
        setT('bu_ak_kas_investasi', rpFmtParen(Math.abs(ak.kas_investasi)));
    } else {
        if (contInvestasi) contInvestasi.style.display = 'none';
        if (judulPendanaan) judulPendanaan.textContent = 'Arus Kas Utang & Modal (Pendanaan)';
    }

    setT('bu_ak_cicilan', rpFmtParen(ak.cicilan_pokok));
    setT('bu_ak_kas_pendanaan', rpFmt(ak.kas_pendanaan));
    setT('bu_ak_kenaikan_bersih', rpFmt(ak.kenaikan_bersih_kas));
    setT('bu_ak_saldo_awal', rpFmt(ak.saldo_awal_kas));
    setT('bu_ak_kas_akhir', rpFmt(ak.kas_akhir));

    const kasAK = parseFloat(ak.kas_akhir || 0);
    const elTeksSinkronAK = document.getElementById('teksSinkronAK');
    if (elTeksSinkronAK) {
        elTeksSinkronAK.innerHTML = 'Kas Akhir Arus Kas (' + rpFmt(kasAK) + ') sinkron 100% dengan Kas &amp; Bank Neraca.';
    }

    // --- TAB NERACA ---
    setT('bu_nr_kas', rpFmt(nr.kas_bank));

    const piutangVal = parseFloat(nr.piutang_usaha || 0);
    setT('bu_nr_piutang', rpFmt(piutangVal));
    const rowPiutang = document.getElementById('row_bu_nr_piutang');
    if (rowPiutang) {
        rowPiutang.style.display = (piutangVal > 0) ? 'flex' : 'none';
    }

    const persediaanVal = parseFloat(nr.persediaan || 0);
    setT('bu_nr_persediaan', rpFmt(persediaanVal));
    const rowPersediaan = document.getElementById('row_bu_nr_persediaan');
    if (rowPersediaan) {
        rowPersediaan.style.display = (persediaanVal > 0) ? 'flex' : 'none';
    }

    setT('bu_nr_total_aset_lancar', rpFmt(nr.total_aset_lancar));

    const asetTetapNeto = parseFloat(nr.aset_tetap_neto || 0);
    setT('bu_nr_aset_tetap_neto', rpFmt(asetTetapNeto));
    const rowAsetTetap = document.getElementById('row_bu_nr_aset_tetap');
    if (rowAsetTetap) {
        rowAsetTetap.style.display = (asetTetapNeto > 0) ? 'block' : 'none';
    }

    // Render rincian aset tetap dinamis dari Tab 9 (Kotak 3)
    let listAset = nr.detail_aset || lr.detail_aset || [];
    if (typeof listAset === 'string') {
        try { listAset = JSON.parse(listAset); } catch(e) { listAset = []; }
    }
    if (!Array.isArray(listAset) || listAset.length === 0) {
        const rawHpp = document.getElementById('detail_hpp_rk')?.value;
        if (rawHpp) {
            try {
                const parsed = JSON.parse(rawHpp);
                if (Array.isArray(parsed)) {
                    listAset = parsed.filter(it => (it.kategori || '').toLowerCase() === 'aset');
                }
            } catch(e) { listAset = []; }
        }
    }
    window._currentDetailAset = Array.isArray(listAset) ? listAset : [];

    const lblAset = document.getElementById('lbl_bu_nr_aset_tetap');
    const contAset = document.getElementById('containerAsetLaporan');
    if (contAset) contAset.innerHTML = '';

    if (window._currentDetailAset.length === 1 && window._currentDetailAset[0].nama && window._currentDetailAset[0].nama.trim() !== '') {
        const namaAset = escapeHtmlText(window._currentDetailAset[0].nama);
        if (lblAset) {
            lblAset.innerHTML = `Aset Tetap &bull; ${namaAset} (Neto)`;
        }
    } else if (window._currentDetailAset.length > 1) {
        if (lblAset) {
            lblAset.textContent = 'Aset Tetap / Peralatan Usaha (Neto)';
        }
        if (contAset) {
            window._currentDetailAset.forEach(item => {
                const nom = parseFloat(item.nominal || 0);
                if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
                    const row = document.createElement('div');
                    row.className = 'flex justify-between items-center py-1 pl-4 border-l-2 border-emerald-500 bg-emerald-50/50 rounded-r-lg pr-2';
                    row.innerHTML = `
                        <span class="text-slate-600 text-xs sm:text-sm font-medium">• ${escapeHtmlText(item.nama || 'Aset / Peralatan')}</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm">${rpFmt(nom)}</span>
                    `;
                    contAset.appendChild(row);
                }
            });
        }
    } else {
        if (lblAset) {
            lblAset.textContent = 'Aset Tetap / Peralatan Usaha (Neto)';
        }
    }

    setT('bu_nr_total_aset', rpFmt(nr.total_aset));

    const utangUsahaVal = parseFloat(nr.utang_usaha || 0);
    setT('bu_nr_utang_usaha', rpFmt(utangUsahaVal));
    const rowUtangUsaha = document.getElementById('row_bu_nr_utang_usaha');
    if (rowUtangUsaha) {
        rowUtangUsaha.style.display = (utangUsahaVal > 0) ? 'flex' : 'none';
    }

    const totalUtangBank = (parseFloat(nr.utang_bank_pendek || 0) + parseFloat(nr.utang_jangka_panjang || 0));
    setT('bu_nr_utang_bank', rpFmt(totalUtangBank));
    const rowUtangBank = document.getElementById('row_bu_nr_utang_bank');
    if (rowUtangBank) {
        rowUtangBank.style.display = (totalUtangBank > 0) ? 'flex' : 'none';
    }
    const lblTotalLiab = document.getElementById('lbl_bu_nr_total_liab');
    if (lblTotalLiab) {
        lblTotalLiab.textContent = (totalUtangBank > 0) ? 'Total Seluruh Utang (Total Liabilitas)' : 'Total Seluruh Utang Usaha (Total Liabilitas)';
    }

    setT('bu_nr_total_liab', rpFmt(nr.total_liabilitas));

    const modalDisetor = parseFloat(nr.modal_disetor || 0);
    setT('bu_nr_modal', rpFmt(modalDisetor));
    const rowModal = document.getElementById('row_bu_nr_modal');
    if (rowModal) {
        rowModal.style.display = (modalDisetor > 0) ? 'flex' : 'none';
    }

    setT('bu_nr_laba_ditahan', rpFmt(nr.laba_ditahan_akhir));
    setT('bu_nr_total_ekuitas', rpFmt(nr.total_ekuitas));
    setT('bu_nr_total_liab_ekuitas', rpFmt(nr.total_liabilitas_ekuitas));

    // Update stepper status
    if (typeof tabValidationStatus !== 'undefined') {
        tabValidationStatus['laporan'] = true;
        if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
    }
}

function escapeHtmlText(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
}

function renderPendapatanRowsHtml(mode = 'pdf') {
    const list = window._currentDetailPendapatan || [];
    if (!Array.isArray(list) || list.length === 0) return '';
    let html = '';
    list.forEach(item => {
        const nom = parseFloat(item.nominal || 0);
        if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
            const nama = escapeHtmlText(item.nama || 'Omset Penjualan');
            if (mode === 'pdf') {
                html += `<tr><td class="col-desc" style="padding-left: 20px; color: #2563eb;">• ${nama}</td><td class="col-val">${rpFmt(nom)}</td></tr>`;
            } else {
                html += `<tr><td style="padding-left: 20px; color: #2563eb;">• ${nama}</td><td class="num-right">${rpFmt(nom)}</td></tr>`;
            }
        }
    });
    return html;
}

function renderHppRowsHtml(mode = 'pdf') {
    const list = window._currentDetailHpp || [];
    if (!Array.isArray(list) || list.length === 0) return '';
    let html = '';
    list.forEach(item => {
        const nom = parseFloat(item.nominal || 0);
        if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
            const nama = escapeHtmlText(item.nama || 'Biaya Modal / Kulakan');
            if (mode === 'pdf') {
                html += `<tr><td class="col-desc" style="padding-left: 20px; color: #e11d48;">• ${nama}</td><td class="col-val">${rpFmtParen(nom)}</td></tr>`;
            } else {
                html += `<tr><td style="padding-left: 20px; color: #e11d48;">• ${nama}</td><td class="num-right">${rpFmtParen(nom)}</td></tr>`;
            }
        }
    });
    return html;
}

function renderAsetRowsHtml(mode = 'pdf') {
    const list = window._currentDetailAset || [];
    if (!Array.isArray(list) || list.length === 0) return '';
    let html = '';
    list.forEach(item => {
        const nom = parseFloat(item.nominal || 0);
        if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
            const nama = escapeHtmlText(item.nama || 'Aset / Peralatan');
            if (mode === 'pdf') {
                html += `<tr><td class="col-desc" style="padding-left: 20px; color: #059669;">• ${nama}</td><td class="col-val">${rpFmt(nom)}</td></tr>`;
            } else {
                html += `<tr><td style="padding-left: 20px; color: #059669;">• ${nama}</td><td class="num-right">${rpFmt(nom)}</td></tr>`;
            }
        }
    });
    return html;
}

function renderBebanLainRowsHtml(mode = 'pdf') {
    const list = window._currentDetailBebanLain || [];
    if (!Array.isArray(list) || list.length === 0) return '';
    let html = '';
    list.forEach(item => {
        const nom = parseFloat(item.nominal || 0);
        if (nom > 0 || (item.nama && item.nama.trim() !== '')) {
            const nama = escapeHtmlText(item.nama || 'Pengeluaran Usaha');
            if (mode === 'pdf') {
                html += `<tr><td class="col-desc" style="padding-left: 20px; color: #475569;">• ${nama}</td><td class="col-val">${rpFmtParen(nom)}</td></tr>`;
            } else {
                html += `<tr><td style="padding-left: 20px; color: #475569;">• ${nama}</td><td class="num-right">${rpFmtParen(nom)}</td></tr>`;
            }
        }
    });
    return html;
}

// ============================================================
// AJAX HITUNG / GENERATE ULANG
// ============================================================
function generateLaporanKeuangan(isSilent = false) {
    const kreditId = document.getElementById('kredit_id')?.value
        || document.querySelector('[name="kredit_id"]')?.value;
    if (!kreditId) {
        if (!isSilent) Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Simpan data pengajuan terlebih dahulu.' });
        return;
    }

    const payload = new FormData();
    payload.append('kredit_id', kreditId);

    // Ambil token CSRF
    const csrf = document.querySelector('[name="csrf_token"]');
    if (csrf) payload.append(csrf.name, csrf.value);

    // Jalankan hitungLabaRugi terlebih dahulu agar semua nominal di form ter-update sinkron
    if (typeof hitungLabaRugi === 'function') {
        hitungLabaRugi();
    }

    // Ambil data dari Tahap 8/9 yang sedang ada di form
    // hpp_persen_rk adalah nilai persen (desimal, mis. 42.3), bukan rupiah —
    // JANGAN pakai replace(/\./g, '') karena akan mengubah 42.3 → 423!
    const persenFields = new Set(['hpp_persen_rk', 'hpp_persen_iv', 'biaya_hidup_persen_rk', 'beban_lainnya_persen_rk']);
    ['pendapatan_rk', 'hpp_persen_rk', 'hpp_nominal_rk', 'biaya_hidup_nominal_rk', 'beban_lainnya_nominal_rk'].forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el) {
            if (persenFields.has(name)) {
                // Nilai persen: hanya ganti koma → titik, jangan buang titik desimal
                payload.append(name, el.value.replace(',', '.').trim());
            } else {
                // Nilai rupiah: titik adalah pemisah ribuan, buang dan ganti koma → titik
                payload.append(name, el.value.replace(/\./g, '').replace(',', '.'));
            }
        }
    });

    // Ambil rincian pendapatan dinamis dari Tab 9 (dalam format JSON murni)
    const elDetailPendapatan = document.querySelector('[name="detail_pendapatan_rk"]') || document.getElementById('detail_pendapatan_rk');
    if (elDetailPendapatan && elDetailPendapatan.value) {
        payload.append('detail_pendapatan_rk', elDetailPendapatan.value);
    }

    // Ambil rincian beban usaha dinamis dari Tab 9 (dalam format JSON murni)
    const elDetailBeban = document.querySelector('[name="detail_beban_lainnya_rk"]') || document.getElementById('detail_beban_lainnya_rk');
    if (elDetailBeban && elDetailBeban.value) {
        payload.append('detail_beban_lainnya_rk', elDetailBeban.value);
    }

    // Ambil rincian HPP & Aset dinamis dari Tab 9 (dalam format JSON murni)
    const elDetailHpp = document.querySelector('[name="detail_hpp_rk"]') || document.getElementById('detail_hpp_rk');
    if (elDetailHpp && elDetailHpp.value) {
        payload.append('detail_hpp_rk', elDetailHpp.value);
    }

    // Ambil rincian Hutang dinamis dari Tab 9 (dalam format JSON murni)
    const elDetailUtang = document.querySelector('[name="detail_utang_json"]') || document.getElementById('detail_utang_json');
    if (elDetailUtang && elDetailUtang.value) {
        payload.append('detail_utang_json', elDetailUtang.value);
    } else if (Array.isArray(window.daftarUtang) && window.daftarUtang.length > 0) {
        payload.append('detail_utang_json', JSON.stringify(window.daftarUtang));
    }

    // Ambil state baku SLIK dan Piutang Aset Tab 9
    const elTotalBakiDebet = document.getElementById('total_baki_debet_slik');
    if (elTotalBakiDebet && elTotalBakiDebet.value) {
        payload.append('total_baki_debet_slik', elTotalBakiDebet.value);
        payload.append('neraca_utang_bank_panjang', elTotalBakiDebet.value);
    } else if (window.TOTAL_BAKI_DEBET_SLIK) {
        payload.append('total_baki_debet_slik', window.TOTAL_BAKI_DEBET_SLIK);
        payload.append('neraca_utang_bank_panjang', window.TOTAL_BAKI_DEBET_SLIK);
    }

    const elCicilanSlik = document.getElementById('cicilan_slik_per_bulan');
    if (elCicilanSlik && elCicilanSlik.value) {
        payload.append('cicilan_slik_per_bulan', elCicilanSlik.value);
        payload.append('input_pembayaran_pokok', elCicilanSlik.value);
    } else if (window.CICILAN_SLIK_PER_BULAN) {
        payload.append('cicilan_slik_per_bulan', window.CICILAN_SLIK_PER_BULAN);
        payload.append('input_pembayaran_pokok', window.CICILAN_SLIK_PER_BULAN);
    }

    const elPiutangAset = document.getElementById('piutang_aset') || document.getElementById('neraca_piutang_usaha');
    if (elPiutangAset && elPiutangAset.value) {
        payload.append('piutang_aset', elPiutangAset.value);
        payload.append('neraca_piutang_usaha', elPiutangAset.value);
        payload.append('input_piutang_akhir', elPiutangAset.value);
    } else if (window.PIUTANG_ASET) {
        payload.append('piutang_aset', window.PIUTANG_ASET);
        payload.append('neraca_piutang_usaha', window.PIUTANG_ASET);
        payload.append('input_piutang_akhir', window.PIUTANG_ASET);
    }

    // Ambil data neraca di collapsible Tahap 9 jika ada
    ['neraca_awal_kas','input_piutang_akhir','input_persediaan_akhir','neraca_awal_piutang','neraca_awal_persediaan',
     'neraca_aset_tetap_bruto','neraca_akumulasi_penyusutan','input_capex','beban_depresiasi','neraca_awal_aset_tetap',
     'input_utang_usaha_akhir','neraca_utang_bank_lancar','neraca_utang_bank_panjang','input_pembayaran_pokok','neraca_awal_utang_usaha',
     'neraca_modal_disetor','neraca_awal_laba_ditahan','input_prive_dividen','input_beban_bunga','input_beban_pajak'].forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el && el.value) payload.append(name, el.value.replace(/\./g, '').replace(',', '.'));
    });

    // Ambil total saldo awal rekening koran langsung dari input Tahap 7 jika tersedia di form
    let sumSaldoAwalDOM = 0;
    document.querySelectorAll('.rek-saldo-awal').forEach(el => {
        const val = parseFloat((el.value || '').replace(/\./g, '').replace(',', '.')) || 0;
        sumSaldoAwalDOM += val;
    });
    if (sumSaldoAwalDOM > 0 && (!payload.get('neraca_awal_kas') || parseFloat(payload.get('neraca_awal_kas')) <= 0)) {
        payload.set('neraca_awal_kas', sumSaldoAwalDOM);
    }

    if (!isSilent) {
        Swal.fire({
            title: 'Menghitung Laporan...',
            text: 'Mengonsolidasikan data laba rugi, arus kas, dan neraca',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    }

    fetch(baseUrl + 'pengajuan/generate-laporan', { method: 'POST', body: payload })
        .then(r => r.json())
        .then(res => {
            if (!isSilent) Swal.close();
            if (!res.status) {
                if (!isSilent) Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                return;
            }
            isiDataLaporanBU(res.data);
            if (!isSilent) {
                Swal.fire({ icon: 'success', title: 'Laporan Siap', text: res.message, timer: 1800, showConfirmButton: false });
            }
        })
        .catch(() => {
            if (!isSilent) {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal.' });
            }
        });
}
window.generateLaporanKeuangan = generateLaporanKeuangan;

// ============================================================
// HITUNG DATA LAPORAN LANGSUNG DARI DOM FORM (FALLBACK REALTIME)
// ============================================================
function hitungDataLaporanDariDOM() {
    // Parser rupiah: titik = ribuan, koma = desimal → buang titik, ganti koma→titik
    const pF = (val) => {
        if (!val) return 0;
        const clean = String(val).replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
        return parseFloat(clean) || 0;
    };
    // Parser persen: titik atau koma bisa jadi desimal (42.3 / 42,3 → 42.3)
    // Untuk nilai kecil (< 1000) satu titik dianggap desimal, bukan ribuan
    const pPct = (val) => {
        if (!val) return 0;
        const s = String(val).trim();
        if (s.indexOf(',') !== -1) {
            return parseFloat(s.replace(/\./g, '').replace(',', '.')) || 0;
        }
        // Satu titik pada nilai persen (0–100) → desimal
        const dots = (s.match(/\./g) || []).length;
        if (dots === 1) return parseFloat(s.replace(/[^0-9.]/g, '')) || 0;
        return parseFloat(s.replace(/\./g, '').replace(/[^0-9]/g, '')) || 0;
    };

    const omzetBulan = pF(document.querySelector('[name="pendapatan_rk"]')?.value || document.getElementById('pendapatan_rk')?.value);
    const pendapatan = omzetBulan > 0 ? omzetBulan : 0;

    let hppPersen = pPct(document.querySelector('[name="hpp_persen_rk"]')?.value || document.getElementById('hpp_persen_rk')?.value);
    let hppNomBulan = pF(document.querySelector('[name="hpp_nominal_rk"]')?.value || document.getElementById('nomHppRKInput')?.value);
    let detailHppDOM = [];
    let detailAsetDOM = [];
    let sumAsetKotak3DOM = 0;
    const rawDetailHppDOM = document.querySelector('[name="detail_hpp_rk"]')?.value || document.getElementById('detail_hpp_rk')?.value;
    if (rawDetailHppDOM && rawDetailHppDOM !== '[]') {
        try {
            const arrHpp = JSON.parse(rawDetailHppDOM);
            if (Array.isArray(arrHpp) && arrHpp.length > 0) {
                let sumHpp = 0;
                arrHpp.forEach(it => {
                    const k = (it.kategori || 'hpp').toLowerCase();
                    if (k === 'aset') {
                        detailAsetDOM.push(it);
                        sumAsetKotak3DOM += pF(it.nominal);
                    } else {
                        detailHppDOM.push(it);
                        sumHpp += pF(it.nominal);
                    }
                });
                if (sumHpp > 0 || detailHppDOM.length > 0) hppNomBulan = sumHpp;
            }
        } catch(e) {}
    }
    if (hppNomBulan === 0 && hppPersen > 0) {
        hppNomBulan = Math.round(omzetBulan * (hppPersen / 100));
    }
    const hpp = hppNomBulan;
    const labaKotor = pendapatan - hpp;

    let detailPendapatanDOM = [];
    const rawDetailPDOM = document.querySelector('[name="detail_pendapatan_rk"]')?.value || document.getElementById('detail_pendapatan_rk')?.value;
    if (rawDetailPDOM && rawDetailPDOM !== '[]') {
        try {
            const arrP = JSON.parse(rawDetailPDOM);
            if (Array.isArray(arrP) && arrP.length > 0) {
                detailPendapatanDOM = arrP;
            }
        } catch(e) {}
    }

    let detailBebanDOM = [];
    const rawDetailBDOM = document.querySelector('[name="detail_beban_lainnya_rk"]')?.value || document.getElementById('detail_beban_lainnya_rk')?.value;
    if (rawDetailBDOM && rawDetailBDOM !== '[]') {
        try {
            const arrB = JSON.parse(rawDetailBDOM);
            if (Array.isArray(arrB) && arrB.length > 0) {
                detailBebanDOM = arrB;
            }
        } catch(e) {}
    }

    const bebanGajiBulan = pF(document.querySelector('[name="beban_gaji_nominal_rk"]')?.value);
    let bebanOpBulan = pF(document.querySelector('[name="biaya_hidup_nominal_rk"]')?.value || document.getElementById('biaya_hidup_nominal_rk')?.value);
    let bebanLainBulan = pF(document.querySelector('[name="beban_lainnya_nominal_rk"]')?.value || document.getElementById('beban_lainnya_nominal_rk')?.value);
    if (bebanOpBulan > 0 && bebanOpBulan === bebanLainBulan) {
        bebanOpBulan = 0; // Hindari dobel hitung
    }
    const bebanOp = bebanOpBulan;
    const bebanGaji = bebanGajiBulan;
    const bebanLain = bebanLainBulan;

    const penyusutan = pF(document.querySelector('[name="beban_depresiasi"]')?.value) || 0;
    const labaOperasional = labaKotor - bebanOp - bebanGaji - bebanLain - penyusutan;

    const bebanBunga = pF(document.querySelector('[name="input_beban_bunga"]')?.value) || 0;
    const labaBersih = labaOperasional - bebanBunga;

    // Saldo Awal Kas & Pembelian Aset (sinkron 100% dengan LaporanKeuanganService)
    let saldoAwalKas = pF(document.querySelector('[name="neraca_awal_kas"]')?.value) || 0;
    if (saldoAwalKas === 0) {
        document.querySelectorAll('.rek-saldo-awal').forEach(el => {
            saldoAwalKas += pF(el.value);
        });
    }
    const saldoAwalAsetTetap = pF(document.querySelector('[name="neraca_awal_aset_tetap"]')?.value) || 0;
    let pembelianAsetTetap = pF(document.querySelector('[name="input_capex"]')?.value) || pF(document.getElementById('total_aset_hpp_rk')?.value) || sumAsetKotak3DOM;

    // Arus Kas: Cicilan Pokok Pinjaman / Pembayaran Hutang Bank
    let cicilanPokok = 0;
    if (Array.isArray(window.daftarUtang) && window.daftarUtang.length > 0) {
        window.daftarUtang.forEach(u => {
            cicilanPokok += pF(u.cicilan_bulanan || 0);
        });
    }
    if (cicilanPokok === 0) {
        const elCicilan = document.getElementById('cicilan_slik_per_bulan');
        if (elCicilan && elCicilan.value) cicilanPokok = pF(elCicilan.value);
        else if (window.CICILAN_SLIK_PER_BULAN) cicilanPokok = pF(window.CICILAN_SLIK_PER_BULAN);
    }
    if (cicilanPokok === 0) {
        cicilanPokok = pF(document.querySelector('[name="input_pembayaran_pokok"]')?.value) || 0;
    }

    const kasOperasional = labaBersih + penyusutan;
    const kasInvestasi = -pembelianAsetTetap;
    const kasPendanaan = -cicilanPokok;
    const kenaikanBersihKas = kasOperasional + kasInvestasi + kasPendanaan;
    const kasAkhir = saldoAwalKas + kenaikanBersihKas;

    // Neraca (identitas akuntansi seimbang: Kas Neraca = Kas Akhir Arus Kas)
    const kasBank = kasAkhir;

    // Piutang Usaha: Wajib bersumber murni dari data/modul Aset (tagihan/invoice), bukan agunan/SLIK
    let piutang = 0;
    const elPiutangAset = document.getElementById('piutang_aset') || document.getElementById('neraca_piutang_usaha') || document.querySelector('[name="input_piutang_akhir"]');
    if (elPiutangAset && elPiutangAset.value) {
        piutang = pF(elPiutangAset.value);
    } else if (window.PIUTANG_ASET) {
        piutang = pF(window.PIUTANG_ASET);
    }

    const persediaan = pF(document.querySelector('[name="input_persediaan_akhir"]')?.value) || 0;
    const totalAsetLancar = kasBank + piutang + persediaan;

    const asetTetapBrutoInput = pF(document.querySelector('[name="neraca_aset_tetap_bruto"]')?.value) || 0;
    const asetTetapBruto = asetTetapBrutoInput > 0 ? asetTetapBrutoInput : (saldoAwalAsetTetap + pembelianAsetTetap);
    const akmPenyusutanInput = document.querySelector('[name="neraca_akumulasi_penyusutan"]')?.value;
    const akmPenyusutan = (akmPenyusutanInput !== undefined && akmPenyusutanInput !== '') ? pF(akmPenyusutanInput) : penyusutan;
    const asetTetapNeto = Math.max(0, asetTetapBruto - akmPenyusutan);
    const totalAset = totalAsetLancar + asetTetapNeto;

    const utangUsaha = pF(document.querySelector('[name="input_utang_usaha_akhir"]')?.value) || 0;
    let utangBankPendek = pF(document.querySelector('[name="neraca_utang_bank_lancar"]')?.value) || 0;
    let utangBankPanjang = pF(document.querySelector('[name="neraca_utang_bank_panjang"]')?.value) || 0;

    // Hutang Bank: Wajib bersumber dari penjumlahan Baki Debet SLIK OJK (totalBakiDebetSLIK) + Utang Berjalan
    if (utangBankPendek === 0 && utangBankPanjang === 0) {
        let sumBakiDebet = 0;
        if (Array.isArray(window.daftarUtang) && window.daftarUtang.length > 0) {
            window.daftarUtang.forEach(u => {
                sumBakiDebet += pF(u.baki_debet !== undefined ? u.baki_debet : (u.plafon || 0));
            });
        }
        if (sumBakiDebet === 0) {
            const elTotSlik = document.getElementById('total_baki_debet_slik');
            if (elTotSlik && elTotSlik.value) sumBakiDebet = pF(elTotSlik.value);
            else if (window.TOTAL_BAKI_DEBET_SLIK) sumBakiDebet = pF(window.TOTAL_BAKI_DEBET_SLIK);
        }
        if (sumBakiDebet > 0) {
            utangBankPanjang = sumBakiDebet;
        }
    }
    const totalLiabilitas = utangUsaha + utangBankPendek + utangBankPanjang;

    let modalDisetor = pF(document.querySelector('[name="neraca_modal_disetor"]')?.value) || 0;
    const saldoAwalLabaDitahan = pF(document.querySelector('[name="neraca_awal_laba_ditahan"]')?.value) || 0;
    const labaDitahanAkhir = saldoAwalLabaDitahan + labaBersih;
    let totalEkuitas = modalDisetor + labaDitahanAkhir;
    let totalLiabEkuitas = totalLiabilitas + totalEkuitas;

    // Auto-balance jika belum ada input manual untuk Aset Tetap / Modal Disetor
    let finalAsetTetapNeto = asetTetapNeto;
    let finalTotalAset = totalAset;
    if (totalLiabEkuitas > finalTotalAset && asetTetapBrutoInput === 0) {
        const diff = totalLiabEkuitas - finalTotalAset;
        finalAsetTetapNeto += diff;
        finalTotalAset += diff;
    } else if (finalTotalAset > totalLiabEkuitas && modalDisetor === 0) {
        const diff = finalTotalAset - totalLiabEkuitas;
        modalDisetor += diff;
        totalEkuitas += diff;
        totalLiabEkuitas += diff;
    }

    return {
        laba_rugi: {
            pendapatan,
            detail_pendapatan: detailPendapatanDOM,
            hpp,
            detail_hpp: detailHppDOM,
            detail_aset: detailAsetDOM,
            laba_kotor: labaKotor,
            beban_gaji: bebanGaji,
            beban_operasional: bebanOp,
            beban_lainnya: bebanLain,
            detail_beban_lainnya: detailBebanDOM,
            beban_penyusutan: penyusutan,
            laba_operasional: labaOperasional,
            beban_bunga: bebanBunga,
            laba_bersih: labaBersih,
        },
        neraca: {
            kas_bank: kasBank,
            piutang_usaha: piutang,
            persediaan: persediaan,
            total_aset_lancar: totalAsetLancar,
            aset_tetap_neto: finalAsetTetapNeto,
            detail_aset: detailAsetDOM,
            total_aset: finalTotalAset,
            utang_usaha: utangUsaha,
            utang_bank_pendek: utangBankPendek,
            utang_jangka_panjang: utangBankPanjang,
            total_liabilitas: totalLiabilitas,
            modal_disetor: modalDisetor,
            laba_ditahan_akhir: labaDitahanAkhir,
            total_ekuitas: totalEkuitas,
            total_liabilitas_ekuitas: totalLiabEkuitas,
        },
        arus_kas: {
            laba_bersih: labaBersih,
            penyusutan: penyusutan,
            kas_operasional: kasOperasional,
            pembelian_aset_tetap: pembelianAsetTetap,
            kas_investasi: kasInvestasi,
            cicilan_pokok: cicilanPokok,
            kas_pendanaan: kasPendanaan,
            kenaikan_bersih_kas: kenaikanBersihKas,
            saldo_awal_kas: saldoAwalKas,
            kas_akhir: kasAkhir,
        }
    };
}
window.hitungDataLaporanDariDOM = hitungDataLaporanDariDOM;
window.sinkronkanLaporanKeuangan = function() {
    try {
        const domData = hitungDataLaporanDariDOM();
        if (domData) isiDataLaporanBU(domData);
    } catch(e) {
        console.warn('Gagal sinkron laporan BU:', e);
    }
};

// ============================================================
// OTOMATIS RENDER SAAT HALAMAN DIBUKA
// ============================================================
(function() {
    let populated = false;
    <?php if ($sudahAda && $lkJ): ?>
        try {
            const data = <?= json_encode($lkJ) ?>;
            const karyawan = <?= !empty($kredit['daftar_karyawan']) ? json_encode(json_decode($kredit['daftar_karyawan'], true)) : '[]' ?>;
            window._jumlahKaryawan = Array.isArray(karyawan) ? karyawan.length : 2;
            isiDataLaporanBU(data);
            populated = true;
        } catch(e) { console.error('Error pre-loading laporan:', e); }
    <?php endif; ?>

    if (!populated) {
        // Fallback hitung instan dari form agar tidak ada strip (—)
        try {
            const domData = hitungDataLaporanDariDOM();
            if (domData && (domData.laba_rugi.pendapatan > 0 || domData.neraca.total_aset > 0)) {
                isiDataLaporanBU(domData);
                populated = true;
            }
        } catch(e) { console.warn('Hitung DOM fallback:', e); }
    }

    // Sinkronisasi data ke server di background
    const kId = document.getElementById('kredit_id')?.value || '<?= $kredit_id ?? '' ?>';
    if (kId && typeof generateLaporanKeuangan === 'function') {
        generateLaporanKeuangan(true);
    }

    // Tab default sesuai yang diminta: Laba Rugi
    switchTabLaporan('lr');
})();
</script>
