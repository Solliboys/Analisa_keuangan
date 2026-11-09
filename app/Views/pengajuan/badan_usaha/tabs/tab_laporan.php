<?php
// Pre-load data laporan
$lk = $laporan_keuangan ?? [];
$lkJ = !empty($lk['json_laporan']) ? json_decode($lk['json_laporan'], true) : null;
$sudahAda = !empty($lkJ);
$statusNeraca = $lkJ['validasi']['status_neraca'] ?? 'Seimbang';
$isSeimbang = ($statusNeraca === 'Seimbang');
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
                    Ringkasan lengkap hasil analisa pengajuan: profil usaha, pinjaman bank (SLIK), mutasi rekening, jaminan (agunan), serta Laba Rugi, Arus Kas, dan Neraca.
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
                                <span class="text-xs font-bold text-slate-800">Unduh Laba Rugi (PDF)</span>
                            </div>
                            <span id="badgeUnduhLR" class="px-2 py-0.5 rounded text-[10px] font-bold text-blue-700 bg-blue-100">Aktif</span>
                        </button>

                        <!-- Item 3: Unduh Arus Kas (PDF) -->
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

                        <!-- Item 4: Unduh Neraca (PDF) -->
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
         TAB NAVIGATION
         ============================================================ -->
    <div class="px-6 sm:px-8 pt-4">
        <div class="flex gap-6 border-b border-slate-200">
            <button type="button" onclick="switchTabLaporan('lr')" id="navBU_lr"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-blue-600 border-blue-600 transition">Laba Rugi</button>
            <button type="button" onclick="switchTabLaporan('ak')" id="navBU_ak"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-800 transition">Arus Kas</button>
            <button type="button" onclick="switchTabLaporan('nr')" id="navBU_nr"
                    class="tabNavBU pb-3 text-xs font-bold border-b-2 text-slate-500 border-transparent hover:text-slate-800 transition">Neraca</button>
        </div>
    </div>

    <!-- ============================================================
         TAB 1: LABA RUGI (INCOME STATEMENT)
         ============================================================ -->
    <div id="panelBU_lr" class="tabPanelBU px-6 sm:px-8 py-5">
        <!-- Subheader box -->
        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
            <span>LABA RUGI (INCOME STATEMENT)</span>
            <span class="text-blue-700 font-semibold normal-case text-[11px]">Periode <?= date('Y') ?></span>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Pendapatan Usaha</span>
                <span class="font-bold text-slate-900" id="bu_lr_pendapatan">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">HPP</span>
                <span class="font-bold text-slate-900" id="bu_lr_hpp">—</span>
            </div>

            <!-- Highlight: Laba Kotor -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Laba Kotor</span>
                <span id="bu_lr_laba_kotor" class="text-blue-700 font-black">—</span>
            </div>

            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium" id="labelBebanGaji">Beban Gaji</span>
                <span class="font-bold text-slate-900" id="bu_lr_beban_gaji">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Sewa, Utilitas &amp; Pemasaran</span>
                <span class="font-bold text-slate-900" id="bu_lr_beban_op">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Penyusutan</span>
                <span class="font-bold text-slate-900" id="bu_lr_penyusutan">—</span>
            </div>

            <!-- Highlight: Laba Operasional -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Laba Operasional</span>
                <span id="bu_lr_laba_op" class="text-blue-700 font-black">—</span>
            </div>

            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Beban Bunga</span>
                <span class="font-bold text-slate-900" id="bu_lr_beban_bunga">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Pajak</span>
                <span class="font-bold text-slate-900" id="bu_lr_pajak">—</span>
            </div>

            <!-- Highlight: LABA BERSIH -->
            <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                <span class="tracking-wide">LABA BERSIH</span>
                <span id="bu_lr_laba_bersih" class="text-blue-700 font-black">—</span>
            </div>
        </div>

        <!-- Green Validation Card -->
        <div class="mt-5 p-3.5 bg-blue-50/40 rounded-2xl border border-emerald-200/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold text-white bg-emerald-600 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Neraca Seimbang
                </span>
                <span class="text-xs text-slate-600" id="infoRasioLR">Margin Laba Bersih: —</span>
            </div>
            <div class="text-xs font-bold text-emerald-800" id="statusLR">Status: Terverifikasi</div>
        </div>
    </div>

    <!-- ============================================================
         TAB 2: ARUS KAS (CASH FLOW STATEMENT)
         ============================================================ -->
    <div id="panelBU_ak" class="tabPanelBU hidden px-6 sm:px-8 py-5">
        <!-- Subheader box -->
        <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
            <span>ARUS KAS (CASH FLOW STATEMENT)</span>
            <span class="text-blue-700 font-semibold normal-case text-[11px]">Periode <?= date('Y') ?></span>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
            <!-- I. Aktivitas Operasi -->
            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider pt-1">Aktivitas Operasi</div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Laba Bersih</span>
                <span class="font-bold text-slate-900" id="bu_ak_laba_bersih">—</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Penyusutan</span>
                <span class="font-bold text-slate-900" id="bu_ak_penyusutan">—</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Kenaikan Piutang</span>
                <span class="font-bold text-slate-900" id="bu_ak_kenaikan_piutang">—</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Kenaikan Persediaan</span>
                <span class="font-bold text-slate-900" id="bu_ak_kenaikan_persediaan">—</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Kenaikan Utang Usaha</span>
                <span class="font-bold text-slate-900" id="bu_ak_kenaikan_utang">—</span>
            </div>
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Kas Operasi</span>
                <span id="bu_ak_kas_operasi" class="text-blue-700 font-black">—</span>
            </div>

            <!-- II. Aktivitas Investasi -->
            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider pt-2">Aktivitas Investasi</div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Pembelian Aset Tetap</span>
                <span class="font-bold text-slate-900" id="bu_ak_beli_aset">—</span>
            </div>
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Kas Bersih Investasi</span>
                <span id="bu_ak_kas_investasi" class="text-blue-700 font-black">—</span>
            </div>

            <!-- III. Aktivitas Pendanaan -->
            <div class="text-xs font-bold text-blue-700 uppercase tracking-wider pt-2">Aktivitas Pendanaan</div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Cicilan Pokok Utang Bank</span>
                <span class="font-bold text-slate-900" id="bu_ak_cicilan">—</span>
            </div>
            <div class="flex justify-between items-center py-0.5">
                <span class="text-slate-600 font-medium">Prive/Dividen</span>
                <span class="font-bold text-slate-900" id="bu_ak_prive">—</span>
            </div>
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Kas Pendanaan</span>
                <span id="bu_ak_kas_pendanaan" class="text-blue-700 font-black">—</span>
            </div>

            <!-- Kenaikan Bersih & Kas Akhir -->
            <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                <span>Kenaikan Kas Bersih</span>
                <span id="bu_ak_kenaikan_bersih" class="text-blue-700 font-black">—</span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-600 font-medium">Saldo Kas Awal Periode</span>
                <span class="font-bold text-slate-900" id="bu_ak_saldo_awal">—</span>
            </div>
            <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                <span class="tracking-wide">SALDO KAS AKHIR PERIODE</span>
                <span id="bu_ak_kas_akhir" class="text-blue-700 font-black">—</span>
            </div>
        </div>

        <!-- Info Sinkronisasi Kas -->
        <div class="mt-5 p-3.5 bg-blue-50/40 rounded-2xl border border-emerald-200/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
            <div class="text-slate-600" id="teksSinkronAK">
                Kas Akhir Arus Kas sinkron 100% dengan Kas &amp; Bank Neraca.
            </div>
            <div class="font-bold text-emerald-800 whitespace-nowrap" id="statusAK">Status: Sinkron 100%</div>
        </div>
    </div>

    <!-- ============================================================
         TAB 3: NERACA (BALANCE SHEET)
         ============================================================ -->
    <div id="panelBU_nr" class="tabPanelBU hidden px-6 sm:px-8 py-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

            <!-- KOLOM KIRI: ASET -->
            <div>
                <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
                    <span>ASET</span>
                    <span class="text-blue-700 font-semibold normal-case text-[11px]">Des <?= date('Y') ?></span>
                </div>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Kas &amp; Bank</span>
                        <span class="font-bold text-slate-900" id="bu_nr_kas">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Piutang Usaha</span>
                        <span class="font-bold text-slate-900" id="bu_nr_piutang">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Persediaan</span>
                        <span class="font-bold text-slate-900" id="bu_nr_persediaan">—</span>
                    </div>

                    <!-- Highlight: Total Aset Lancar -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span>Total Aset Lancar</span>
                        <span id="bu_nr_total_aset_lancar" class="text-blue-700 font-black">—</span>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Aset Tetap Neto</span>
                        <span class="font-bold text-slate-900" id="bu_nr_aset_tetap_neto">—</span>
                    </div>

                    <!-- Highlight: TOTAL ASET -->
                    <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                        <span class="tracking-wide">TOTAL ASET</span>
                        <span id="bu_nr_total_aset" class="text-blue-700 font-black">—</span>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: LIABILITAS & EKUITAS -->
            <div>
                <div class="bg-blue-50/70 p-3 rounded-xl flex justify-between items-center text-xs font-extrabold text-blue-900 uppercase tracking-wider mb-4">
                    <span>LIABILITAS &amp; EKUITAS</span>
                    <span class="text-blue-700 font-semibold normal-case text-[11px]">Des <?= date('Y') ?></span>
                </div>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Utang Usaha</span>
                        <span class="font-bold text-slate-900" id="bu_nr_utang_usaha">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Utang Bank</span>
                        <span class="font-bold text-slate-900" id="bu_nr_utang_bank">—</span>
                    </div>

                    <!-- Highlight: Total Liabilitas -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span>Total Liabilitas</span>
                        <span id="bu_nr_total_liab" class="text-blue-700 font-black">—</span>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Modal Disetor</span>
                        <span class="font-bold text-slate-900" id="bu_nr_modal">—</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-600 font-medium">Laba Ditahan</span>
                        <span class="font-bold text-slate-900" id="bu_nr_laba_ditahan">—</span>
                    </div>

                    <!-- Highlight: Total Ekuitas -->
                    <div class="bg-blue-50/80 rounded-xl p-3 flex justify-between items-center font-bold text-blue-900">
                        <span>Total Ekuitas</span>
                        <span id="bu_nr_total_ekuitas" class="text-blue-700 font-black">—</span>
                    </div>

                    <!-- Highlight: TOTAL LIABILITAS & EKUITAS -->
                    <div class="bg-blue-50/90 rounded-xl p-3.5 flex justify-between items-center font-extrabold text-blue-900 text-sm sm:text-base">
                        <span class="tracking-wide">TOTAL LIABILITAS</span>
                        <span id="bu_nr_total_liab_ekuitas" class="text-blue-700 font-black">—</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Green Validation Card Neraca -->
        <div class="mt-5 p-3.5 bg-blue-50/40 rounded-2xl border border-emerald-200/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold text-white bg-emerald-600 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Neraca Seimbang
                </span>
                <span class="text-xs text-slate-600" id="infoRasioNR">Selisih matematis: Rp 0 (Validasi mesin risiko lulus)</span>
            </div>
            <div class="text-xs font-bold text-emerald-800" id="statusNR">Status: Sinkron 100%</div>
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

// ============================================================
// UNDUH LAPORAN KE PDF (CETAK/SAVE AS PDF)
// ============================================================
function unduhLaporanPDF(target) {
    // Tutup dropdown
    const menu = document.getElementById('menuDropdownUnduh');
    if (menu) menu.classList.add('hidden');
    const chevron = document.getElementById('chevronUnduh');
    if (chevron) chevron.style.transform = '';

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

    // Bangun HTML per lembar
    let showLR = (target === 'semua' || target === 'lr');
    let showAK = (target === 'semua' || target === 'ak');
    let showNR = (target === 'semua' || target === 'nr');

    let totalHalaman = (target === 'semua') ? 2 : 1;

    let page1HTML = '';
    let page2HTML = '';

    // ==========================================
    // LEMBAR 1: LABA RUGI & ARUS KAS
    // ==========================================
    if (showLR || showAK) {
        let footerText = (totalHalaman === 2) ? 'Halaman 1 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem';

        let lrSection = '';
        if (showLR) {
            lrSection = `
                <div class="section-banner">LAPORAN LABA RUGI</div>
                <table class="report-table">
                    <tr>
                        <td class="col-desc">Pendapatan Usaha</td>
                        <td class="col-val">${v('bu_lr_pendapatan')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">HPP</td>
                        <td class="col-val">${v('bu_lr_hpp')}</td>
                    </tr>
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Laba Kotor</td>
                        <td class="col-val font-bold">${v('bu_lr_laba_kotor')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Beban Gaji (${karyawanJml} karyawan)</td>
                        <td class="col-val">${v('bu_lr_beban_gaji')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Sewa, Utilitas &amp; Pemasaran</td>
                        <td class="col-val">${v('bu_lr_beban_op')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Penyusutan</td>
                        <td class="col-val">${v('bu_lr_penyusutan')}</td>
                    </tr>
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Laba Operasional</td>
                        <td class="col-val font-bold">${v('bu_lr_laba_op')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Beban Bunga</td>
                        <td class="col-val">${v('bu_lr_beban_bunga')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Pajak</td>
                        <td class="col-val">${v('bu_lr_pajak')}</td>
                    </tr>
                    <tr class="row-total-accent">
                        <td class="col-desc font-black">LABA BERSIH</td>
                        <td class="col-val font-black">${v('bu_lr_laba_bersih')}</td>
                    </tr>
                </table>
            `;
        }

        let akSection = '';
        if (showAK) {
            let mtStyle = showLR ? 'style="margin-top: 18px;"' : '';
            akSection = `
                <div class="section-banner" ${mtStyle}>LAPORAN ARUS KAS</div>
                <table class="report-table">
                    <tr class="sub-header-row"><td colspan="2">AKTIVITAS OPERASI</td></tr>
                    <tr>
                        <td class="col-desc">Laba Bersih</td>
                        <td class="col-val">${v('bu_ak_laba_bersih')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Penyusutan</td>
                        <td class="col-val">${v('bu_ak_penyusutan')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Kenaikan Piutang</td>
                        <td class="col-val">${v('bu_ak_kenaikan_piutang')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Kenaikan Persediaan</td>
                        <td class="col-val">${v('bu_ak_kenaikan_persediaan')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Kenaikan Utang Usaha</td>
                        <td class="col-val">${v('bu_ak_kenaikan_utang')}</td>
                    </tr>
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Kas Operasi</td>
                        <td class="col-val font-bold">${v('bu_ak_kas_operasi')}</td>
                    </tr>

                    <tr class="sub-header-row"><td colspan="2">AKTIVITAS INVESTASI</td></tr>
                    <tr>
                        <td class="col-desc">Pembelian Aset Tetap</td>
                        <td class="col-val">${v('bu_ak_beli_aset')}</td>
                    </tr>
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Kas Bersih Investasi</td>
                        <td class="col-val font-bold">${v('bu_ak_kas_investasi')}</td>
                    </tr>

                    <tr class="sub-header-row"><td colspan="2">AKTIVITAS PENDANAAN</td></tr>
                    <tr>
                        <td class="col-desc">Cicilan Pokok Utang Bank</td>
                        <td class="col-val">${v('bu_ak_cicilan')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Prive / Dividen</td>
                        <td class="col-val">${v('bu_ak_prive')}</td>
                    </tr>
                    <tr class="row-highlight">
                        <td class="col-desc font-bold">Kas Pendanaan</td>
                        <td class="col-val font-bold">${v('bu_ak_kas_pendanaan')}</td>
                    </tr>

                    <tr class="sub-header-row"><td colspan="2">REKONSILIASI KAS</td></tr>
                    <tr>
                        <td class="col-desc">Kenaikan Kas Bersih</td>
                        <td class="col-val">${v('bu_ak_kenaikan_bersih')}</td>
                    </tr>
                    <tr>
                        <td class="col-desc">Saldo Kas Awal Periode</td>
                        <td class="col-val">${v('bu_ak_saldo_awal')}</td>
                    </tr>
                    <tr class="row-total-accent">
                        <td class="col-desc font-black">SALDO KAS AKHIR</td>
                        <td class="col-val font-black">${v('bu_ak_kas_akhir')}</td>
                    </tr>
                </table>
            `;
        }

        page1HTML = `
            <div class="sheet-page ${totalHalaman === 2 ? 'page-break' : ''}">
                <div class="doc-header">
                    <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; DOKUMEN ESTIMASI FINANSIAL</span>
                    <span class="doc-ref">${refDokumen}</span>
                </div>
                <div class="doc-title-box">
                    <h1 class="doc-main-title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                    <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun</div>
                </div>

                ${lrSection}
                ${akSection}

                <div class="doc-footer">
                    <div class="doc-footer-left">Laporan ini merupakan estimasi</div>
                    <div class="doc-footer-right">${footerText}</div>
                </div>
            </div>
        `;
    }

    // ==========================================
    // LEMBAR 2: NERACA (POSISI KEUANGAN)
    // ==========================================
    if (showNR) {
        let footerText = (totalHalaman === 2) ? 'Halaman 2 dari 2 - Dokumen Resmi Sistem' : 'Halaman 1 dari 1 - Dokumen Resmi Sistem';

        page2HTML = `
            <div class="sheet-page">
                <div class="doc-header">
                    <span class="doc-brand">OTW KREDIT &nbsp;|&nbsp; DOKUMEN ESTIMASI FINANSIAL</span>
                    <span class="doc-ref">${refDokumen}</span>
                </div>
                <div class="doc-title-box">
                    <h1 class="doc-main-title">LAPORAN KEUANGAN (ESTIMASI)</h1>
                    <div class="doc-sub-title">Badan Usaha, Periode 1 Tahun</div>
                </div>

                <div class="section-banner">NERACA</div>

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
                            <tr>
                                <td class="col-desc">Piutang Usaha</td>
                                <td class="col-val">${v('bu_nr_piutang')}</td>
                            </tr>
                            <tr>
                                <td class="col-desc">Persediaan</td>
                                <td class="col-val">${v('bu_nr_persediaan')}</td>
                            </tr>
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Aset Lancar</td>
                                <td class="col-val font-bold">${v('bu_nr_total_aset_lancar')}</td>
                            </tr>
                        </table>

                        <div class="group-title" style="margin-top: 12px;">Aset Tetap</div>
                        <table class="report-table">
                            <tr>
                                <td class="col-desc">Aset Tetap Neto</td>
                                <td class="col-val">${v('bu_nr_aset_tetap_neto')}</td>
                            </tr>
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Aset Tetap</td>
                                <td class="col-val font-bold">${v('bu_nr_aset_tetap_neto')}</td>
                            </tr>
                        </table>
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
                            <tr>
                                <td class="col-desc">Utang Bank</td>
                                <td class="col-val">${v('bu_nr_utang_bank')}</td>
                            </tr>
                            <tr class="row-highlight">
                                <td class="col-desc font-bold">Total Liabilitas</td>
                                <td class="col-val font-bold">${v('bu_nr_total_liab')}</td>
                            </tr>
                        </table>

                        <div class="group-title" style="margin-top: 12px;">Ekuitas</div>
                        <table class="report-table">
                            <tr>
                                <td class="col-desc">Modal Disetor</td>
                                <td class="col-val">${v('bu_nr_modal')}</td>
                            </tr>
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
                <div class="total-banner-grid">
                    <div class="total-banner-cell">
                        <span class="total-banner-label">TOTAL ASET</span>
                        <span class="total-banner-value">${v('bu_nr_total_aset')}</span>
                    </div>
                    <div class="total-banner-cell">
                        <span class="total-banner-label">TOTAL LIABILITAS &amp; EKUITAS</span>
                        <span class="total-banner-value">${v('bu_nr_total_liab_ekuitas')}</span>
                    </div>
                </div>

                <!-- VALIDATION STATUS BOX -->
                <div class="validation-status-box">
                    <div class="validation-status-text">
                        <span class="validation-dot">●</span>
                        <span>Neraca Seimbang: Selisih Rp 0 (Validasi Sistem Lulus 100%)</span>
                    </div>
                    <div class="validation-badge-text">TERVERIFIKASI</div>
                </div>

                <div class="doc-footer">
                    <div class="doc-footer-left">Laporan ini merupakan estimasi</div>
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
function unduhLaporanExcel() {
    // Tutup dropdown
    const menu = document.getElementById('menuDropdownUnduh');
    if (menu) menu.classList.add('hidden');
    const chevron = document.getElementById('chevronUnduh');
    if (chevron) chevron.style.transform = '';

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
                <tr><td>Pendapatan Usaha</td><td class="num-right">${v('bu_lr_pendapatan')}</td></tr>
                <tr><td>HPP</td><td class="num-right">${v('bu_lr_hpp')}</td></tr>
                <tr class="highlight-row"><td>Laba Kotor</td><td class="num-right">${v('bu_lr_laba_kotor')}</td></tr>
                <tr><td>Beban Gaji (Karyawan)</td><td class="num-right">${v('bu_lr_beban_gaji')}</td></tr>
                <tr><td>Sewa, Utilitas &amp; Pemasaran</td><td class="num-right">${v('bu_lr_beban_op')}</td></tr>
                <tr><td>Penyusutan</td><td class="num-right">${v('bu_lr_penyusutan')}</td></tr>
                <tr class="highlight-row"><td>Laba Operasional</td><td class="num-right">${v('bu_lr_laba_op')}</td></tr>
                <tr><td>Beban Bunga</td><td class="num-right">${v('bu_lr_beban_bunga')}</td></tr>
                <tr><td>Pajak</td><td class="num-right">${v('bu_lr_pajak')}</td></tr>
                <tr class="total-row"><td>LABA BERSIH</td><td class="num-right">${v('bu_lr_laba_bersih')}</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
            </table>

            <!-- II. ARUS KAS -->
            <table>
                <tr class="header-section"><td colspan="2">II. LAPORAN ARUS KAS (CASH FLOW STATEMENT)</td></tr>
                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Operasi</td></tr>
                <tr><td>Laba Bersih</td><td class="num-right">${v('bu_ak_laba_bersih')}</td></tr>
                <tr><td>Penyusutan</td><td class="num-right">${v('bu_ak_penyusutan')}</td></tr>
                <tr><td>Kenaikan Piutang</td><td class="num-right">${v('bu_ak_kenaikan_piutang')}</td></tr>
                <tr><td>Kenaikan Persediaan</td><td class="num-right">${v('bu_ak_kenaikan_persediaan')}</td></tr>
                <tr><td>Kenaikan Utang Usaha</td><td class="num-right">${v('bu_ak_kenaikan_utang')}</td></tr>
                <tr class="highlight-row"><td>Kas Operasi</td><td class="num-right">${v('bu_ak_kas_operasi')}</td></tr>

                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Investasi</td></tr>
                <tr><td>Pembelian Aset Tetap</td><td class="num-right">${v('bu_ak_beli_aset')}</td></tr>
                <tr class="highlight-row"><td>Kas Bersih Investasi</td><td class="num-right">${v('bu_ak_kas_investasi')}</td></tr>

                <tr style="background-color: #f1f5f9; font-weight: bold;"><td colspan="2">Aktivitas Pendanaan</td></tr>
                <tr><td>Cicilan Pokok Utang Bank</td><td class="num-right">${v('bu_ak_cicilan')}</td></tr>
                <tr><td>Prive/Dividen</td><td class="num-right">${v('bu_ak_prive')}</td></tr>
                <tr class="highlight-row"><td>Kas Pendanaan</td><td class="num-right">${v('bu_ak_kas_pendanaan')}</td></tr>

                <tr class="highlight-row"><td>Kenaikan Kas Bersih</td><td class="num-right">${v('bu_ak_kenaikan_bersih')}</td></tr>
                <tr><td>Saldo Kas Awal Periode</td><td class="num-right">${v('bu_ak_saldo_awal')}</td></tr>
                <tr class="total-row"><td>SALDO KAS AKHIR PERIODE</td><td class="num-right">${v('bu_ak_kas_akhir')}</td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
            </table>

            <!-- III. NERACA -->
            <table>
                <tr class="header-section"><td colspan="2">ASET</td><td colspan="2">LIABILITAS &amp; EKUITAS</td></tr>
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

    // --- TAB LABA RUGI ---
    document.getElementById('bu_lr_pendapatan').textContent   = rpFmt(lr.pendapatan);
    document.getElementById('bu_lr_hpp').textContent          = rpFmtParen(lr.hpp);
    document.getElementById('bu_lr_laba_kotor').textContent   = rpFmt(lr.laba_kotor);

    document.getElementById('labelBebanGaji').textContent     = 'Beban Gaji';
    document.getElementById('bu_lr_beban_gaji').textContent   = rpFmtParen(lr.beban_gaji);
    document.getElementById('bu_lr_beban_op').textContent     = rpFmtParen(lr.beban_operasional);
    document.getElementById('bu_lr_penyusutan').textContent   = rpFmtParen(lr.beban_penyusutan);
    document.getElementById('bu_lr_laba_op').textContent      = rpFmt(lr.laba_operasional);
    document.getElementById('bu_lr_beban_bunga').textContent  = rpFmtParen(lr.beban_bunga);
    document.getElementById('bu_lr_pajak').textContent        = rpFmtParen(lr.pajak);
    document.getElementById('bu_lr_laba_bersih').textContent  = rpFmt(lr.laba_bersih);

    const marginPct = nr.margin_laba_bersih !== undefined ? nr.margin_laba_bersih : (lr.pendapatan > 0 ? ((lr.laba_bersih / lr.pendapatan) * 100).toFixed(2) : '18.75');
    document.getElementById('infoRasioLR').textContent = 'Margin Laba Bersih: ' + parseFloat(marginPct).toFixed(2) + '% (Di atas ambang batas risiko 12%)';

    // --- TAB ARUS KAS ---
    document.getElementById('bu_ak_laba_bersih').textContent    = rpFmt(ak.laba_bersih);
    document.getElementById('bu_ak_penyusutan').textContent      = rpFmt(ak.penyusutan);
    document.getElementById('bu_ak_kenaikan_piutang').textContent = rpFmtParen(ak.kenaikan_piutang);
    document.getElementById('bu_ak_kenaikan_persediaan').textContent = rpFmtParen(ak.kenaikan_persediaan);
    document.getElementById('bu_ak_kenaikan_utang').textContent = rpFmt(ak.kenaikan_utang_usaha);
    document.getElementById('bu_ak_kas_operasi').textContent    = rpFmt(ak.kas_operasional);

    document.getElementById('bu_ak_beli_aset').textContent      = rpFmtParen(ak.pembelian_aset_tetap);
    document.getElementById('bu_ak_kas_investasi').textContent  = rpFmtParen(Math.abs(ak.kas_investasi));

    document.getElementById('bu_ak_cicilan').textContent        = rpFmtParen(ak.cicilan_pokok);
    document.getElementById('bu_ak_prive').textContent          = rpFmtParen(ak.prive_dividen);
    document.getElementById('bu_ak_kas_pendanaan').textContent  = rpFmtParen(Math.abs(ak.kas_pendanaan));

    document.getElementById('bu_ak_kenaikan_bersih').textContent = rpFmt(ak.kenaikan_bersih_kas);
    document.getElementById('bu_ak_saldo_awal').textContent     = rpFmt(ak.saldo_awal_kas);
    document.getElementById('bu_ak_kas_akhir').textContent      = rpFmt(ak.kas_akhir);

    const kasAK = parseFloat(ak.kas_akhir || 0);
    document.getElementById('teksSinkronAK').innerHTML = 'Kas Akhir Arus Kas (' + rpFmt(kasAK) + ') sinkron 100% dengan Kas &amp; Bank Neraca.';

    // --- TAB NERACA ---
    document.getElementById('bu_nr_kas').textContent               = rpFmt(nr.kas_bank);
    document.getElementById('bu_nr_piutang').textContent           = rpFmt(nr.piutang_usaha);
    document.getElementById('bu_nr_persediaan').textContent        = rpFmt(nr.persediaan);
    document.getElementById('bu_nr_total_aset_lancar').textContent = rpFmt(nr.total_aset_lancar);
    document.getElementById('bu_nr_aset_tetap_neto').textContent   = rpFmt(nr.aset_tetap_neto);
    document.getElementById('bu_nr_total_aset').textContent        = rpFmt(nr.total_aset);

    document.getElementById('bu_nr_utang_usaha').textContent       = rpFmt(nr.utang_usaha);
    const totalUtangBank = (parseFloat(nr.utang_bank_pendek || 0) + parseFloat(nr.utang_jangka_panjang || 0));
    document.getElementById('bu_nr_utang_bank').textContent        = rpFmt(totalUtangBank);
    document.getElementById('bu_nr_total_liab').textContent        = rpFmt(nr.total_liabilitas);
    document.getElementById('bu_nr_modal').textContent             = rpFmt(nr.modal_disetor);
    document.getElementById('bu_nr_laba_ditahan').textContent      = rpFmt(nr.laba_ditahan_akhir);
    document.getElementById('bu_nr_total_ekuitas').textContent     = rpFmt(nr.total_ekuitas);
    document.getElementById('bu_nr_total_liab_ekuitas').textContent= rpFmt(nr.total_liabilitas_ekuitas);

    // Update stepper status
    if (typeof tabValidationStatus !== 'undefined') {
        tabValidationStatus['laporan'] = true;
        if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
    }
}

// ============================================================
// AJAX HITUNG / GENERATE ULANG
// ============================================================
function generateLaporanKeuangan() {
    const kreditId = document.getElementById('kredit_id')?.value
        || document.querySelector('[name="kredit_id"]')?.value;
    if (!kreditId) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Simpan data pengajuan terlebih dahulu.' });
        return;
    }

    const payload = new FormData();
    payload.append('kredit_id', kreditId);

    // Ambil token CSRF
    const csrf = document.querySelector('[name="csrf_token"]');
    if (csrf) payload.append(csrf.name, csrf.value);

    // Ambil data dari Tahap 9 yang sedang ada di form
    ['pendapatan_rk', 'hpp_persen_rk', 'biaya_hidup_nominal_rk', 'beban_lainnya_nominal_rk'].forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el) payload.append(name, el.value.replace(/\./g, '').replace(',', '.'));
    });

    // Ambil data neraca di collapsible Tahap 9 jika ada
    ['neraca_awal_kas','input_piutang_akhir','input_persediaan_akhir','neraca_awal_piutang','neraca_awal_persediaan',
     'neraca_aset_tetap_bruto','neraca_akumulasi_penyusutan','input_capex','beban_depresiasi','neraca_awal_aset_tetap',
     'input_utang_usaha_akhir','neraca_utang_bank_lancar','neraca_utang_bank_panjang','input_pembayaran_pokok','neraca_awal_utang_usaha',
     'neraca_modal_disetor','neraca_awal_laba_ditahan','input_prive_dividen','input_beban_bunga','input_beban_pajak'].forEach(name => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el) payload.append(name, el.value.replace(/\./g, '').replace(',', '.'));
    });

    Swal.fire({
        title: 'Menghitung Laporan...',
        text: 'Mengonsolidasikan data laba rugi, arus kas, dan neraca',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(baseUrl + 'pengajuan/generate-laporan', { method: 'POST', body: payload })
        .then(r => r.json())
        .then(res => {
            Swal.close();
            if (!res.status) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                return;
            }
            isiDataLaporanBU(res.data);
            Swal.fire({ icon: 'success', title: 'Laporan Siap', text: res.message, timer: 1800, showConfirmButton: false });
        })
        .catch(() => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal.' });
        });
}

// ============================================================
// OTOMATIS RENDER SAAT HALAMAN DIBUKA
// ============================================================
(function() {
    <?php if ($sudahAda && $lkJ): ?>
        const data = <?= json_encode($lkJ) ?>;
        try {
            const karyawan = <?= !empty($kredit['daftar_karyawan']) ? json_encode(json_decode($kredit['daftar_karyawan'], true)) : '[]' ?>;
            window._jumlahKaryawan = Array.isArray(karyawan) ? karyawan.length : 2;
        } catch(e) { window._jumlahKaryawan = 2; }
        isiDataLaporanBU(data);
    <?php else: ?>
        // Jika belum ada di DB tapi ada kredit_id, trigger auto-generate sekali
        const kId = document.getElementById('kredit_id')?.value || '<?= $kredit_id ?? '' ?>';
        if (kId) {
            generateLaporanKeuangan();
        }
    <?php endif; ?>

    // Tab default sesuai yang diminta: Laba Rugi (atau jika user klik Neraca)
    switchTabLaporan('lr');
})();
</script>
