<?php
// Pre-load laporan yang sudah ada
$lk  = $laporan_keuangan ?? [];
$lkJ = !empty($lk['json_laporan']) ? json_decode($lk['json_laporan'], true) : null;
$sudahGenerate = !empty($lk['updated_at']) && !empty($lkJ);
$statusNeraca  = $lkJ['validasi']['status_neraca'] ?? 'Seimbang';
$isSeimbang    = $sudahGenerate && ($statusNeraca === 'Seimbang');
?>
<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">

    <!-- ============================================================
         HEADER LAPORAN KEUANGAN
         ============================================================ -->
    <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 border-b border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Keuangan</h2>
                <p class="text-[11px] text-slate-500 mt-1 max-w-lg">
                    Konsolidasi posisi aset, liabilitas, dan ekuitas berdasarkan buku besar serta sinkronisasi perbankan mutakhir.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200">Badan Usaha</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200">1 Tahun</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200">✦ Estimasi</span>
            </div>
        </div>
    </div>

    <!-- ============================================================
         VALIDASI BANNER (muncul jika neraca tidak seimbang)
         ============================================================ -->
    <?php if ($sudahGenerate && !$isSeimbang): ?>
    <div class="mx-6 sm:mx-8 mt-4 p-4 bg-rose-50 border border-rose-300 rounded-2xl flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <div class="flex-1">
            <div class="text-sm font-extrabold text-rose-800">⚠️ Neraca Tidak Seimbang</div>
            <div class="text-xs text-rose-600 mt-1">
                Selisih: <strong>Rp <?= number_format(abs((float)($lkJ['validasi']['selisih_neraca'] ?? 0)), 0, ',', '.') ?></strong>
                — Periksa kembali data input di Tahap 9, lalu klik "Generate Ulang".
            </div>
        </div>
        <button type="button" onclick="switchTabLaporan('laporan')"
                class="px-3 py-1.5 text-[11px] font-bold text-rose-700 bg-white border border-rose-200 rounded-lg hover:bg-rose-50 transition whitespace-nowrap">
            Periksa Data
        </button>
    </div>
    <?php endif; ?>

    <!-- ============================================================
         STATUS GENERATE (jika belum ada data)
         ============================================================ -->
    <?php if (!$sudahGenerate): ?>
    <div class="mx-6 sm:mx-8 mt-6 p-6 bg-slate-50/80 rounded-2xl border border-dashed border-slate-300 text-center">
        <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-500 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h4 class="text-sm font-extrabold text-slate-700 mb-1">Laporan Belum Dibuat</h4>
        <p class="text-xs text-slate-500 mb-4 max-w-xs mx-auto">Lengkapi data di Tahap 9 → buka section "Data Laporan Keuangan" → klik Generate.</p>
        <button type="button" onclick="switchTabLaporan('laporan')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            Ke Tahap 9 — Isi Data
        </button>
    </div>
    <?php endif; ?>

    <!-- Panel Laporan (tampil jika sudah generate) -->
    <div id="panelHasilLaporanBU" class="<?= $sudahGenerate ? '' : 'hidden' ?>">
        <!-- ============================================================
             TAB NAVIGASI
             ============================================================ -->
        <div class="px-6 sm:px-8 pt-5">
            <div class="flex border-b border-slate-200 gap-0">
                <button type="button" onclick="switchTabLaporan('lr')" id="navBU_lr"
                        class="tabNavBU pb-3 px-4 text-xs font-bold border-b-2 transition text-blue-600 border-blue-600">Laba Rugi</button>
                <button type="button" onclick="switchTabLaporan('ak')" id="navBU_ak"
                        class="tabNavBU pb-3 px-4 text-xs font-bold border-b-2 transition text-slate-400 border-transparent hover:text-slate-600">Arus Kas</button>
                <button type="button" onclick="switchTabLaporan('nr')" id="navBU_nr"
                        class="tabNavBU pb-3 px-4 text-xs font-bold border-b-2 transition text-slate-400 border-transparent hover:text-slate-600">Neraca</button>
            </div>
        </div>

        <!-- ============================================================
             TAB 1: LABA RUGI
             ============================================================ -->
        <div id="panelBU_lr" class="tabPanelBU px-6 sm:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">LABA RUGI (INCOME STATEMENT)</div>
                </div>
                <div class="text-xs text-slate-400 font-semibold" id="periodeLR">Periode <?= date('Y') ?></div>
            </div>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">Pendapatan Usaha</td>
                        <td class="py-3 text-right font-semibold text-slate-800" id="bu_lr_pendapatan">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">HPP</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_hpp">—</td>
                    </tr>
                    <tr class="bg-blue-50/60">
                        <td class="py-3 pl-3 font-extrabold text-blue-900 rounded-l">Laba Kotor</td>
                        <td class="py-3 pr-3 text-right font-extrabold text-blue-700 rounded-r" id="bu_lr_laba_kotor">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium" id="labelBebanGaji">Beban Gaji</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_beban_gaji">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">Sewa, Utilitas &amp; Pemasaran</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_beban_op">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">Penyusutan</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_penyusutan">—</td>
                    </tr>
                    <tr class="bg-indigo-50/60">
                        <td class="py-3 pl-3 font-extrabold text-indigo-900 rounded-l">Laba Operasional</td>
                        <td class="py-3 pr-3 text-right font-extrabold text-indigo-700 rounded-r" id="bu_lr_laba_op">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">Beban Bunga</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_beban_bunga">—</td>
                    </tr>
                    <tr>
                        <td class="py-3 text-slate-600 font-medium">Pajak</td>
                        <td class="py-3 text-right font-semibold text-slate-600" id="bu_lr_pajak">—</td>
                    </tr>
                    <tr class="bg-blue-600 text-white">
                        <td class="py-4 pl-3 font-extrabold text-base rounded-l">LABA BERSIH</td>
                        <td class="py-4 pr-3 text-right font-black text-base rounded-r" id="bu_lr_laba_bersih">—</td>
                    </tr>
                </tbody>
            </table>

            <!-- Validasi & Rasio -->
            <div class="mt-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div id="badgeNeracaLR" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold">
                    <!-- diisi JS -->
                </div>
                <div class="flex-1 text-xs text-slate-500" id="infoRasioLR">
                    <!-- diisi JS: Margin Laba Bersih -->
                </div>
                <div class="text-xs font-bold text-slate-600" id="statusLR">Status: —</div>
            </div>
        </div>

        <!-- ============================================================
             TAB 2: ARUS KAS
             ============================================================ -->
        <div id="panelBU_ak" class="tabPanelBU hidden px-6 sm:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">ARUS KAS (CASH FLOW STATEMENT)</div>
                <div class="text-xs text-slate-400 font-semibold">Periode <?= date('Y') ?></div>
            </div>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <!-- Aktivitas Operasi -->
                    <tr class="bg-blue-50/50">
                        <td colspan="2" class="py-2.5 pl-2 font-extrabold text-blue-800 text-xs uppercase tracking-wider">Aktivitas Operasi</td>
                    </tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Laba Bersih</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_ak_laba_bersih">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Penyusutan</td><td class="py-2.5 text-right font-semibold text-emerald-600" id="bu_ak_penyusutan">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Kenaikan Piutang <span id="tagPiutang" class="ml-1 text-[10px] text-amber-600 font-bold"></span></td><td class="py-2.5 text-right font-semibold" id="bu_ak_kenaikan_piutang">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Kenaikan Persediaan <span id="tagPersediaan" class="ml-1 text-[10px] text-amber-600 font-bold"></span></td><td class="py-2.5 text-right font-semibold" id="bu_ak_kenaikan_persediaan">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Kenaikan Utang Usaha <span id="tagUtang" class="ml-1 text-[10px] text-amber-600 font-bold"></span></td><td class="py-2.5 text-right font-semibold" id="bu_ak_kenaikan_utang">—</td></tr>
                    <tr class="bg-blue-100/60">
                        <td class="py-3 pl-3 font-extrabold text-blue-900 rounded-l">Kas Operasi</td>
                        <td class="py-3 pr-1 text-right font-extrabold text-blue-700 rounded-r" id="bu_ak_kas_operasi">—</td>
                    </tr>

                    <!-- Aktivitas Investasi -->
                    <tr class="bg-indigo-50/50">
                        <td colspan="2" class="py-2.5 pl-2 font-extrabold text-indigo-800 text-xs uppercase tracking-wider">Aktivitas Investasi</td>
                    </tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Pembelian Aset Tetap</td><td class="py-2.5 text-right font-semibold text-rose-600" id="bu_ak_beli_aset">—</td></tr>
                    <tr class="bg-indigo-100/60">
                        <td class="py-3 pl-3 font-extrabold text-indigo-900 rounded-l">Kas Bersih Investasi</td>
                        <td class="py-3 pr-1 text-right font-extrabold text-indigo-700 rounded-r" id="bu_ak_kas_investasi">—</td>
                    </tr>

                    <!-- Aktivitas Pendanaan -->
                    <tr class="bg-violet-50/50">
                        <td colspan="2" class="py-2.5 pl-2 font-extrabold text-violet-800 text-xs uppercase tracking-wider">Aktivitas Pendanaan</td>
                    </tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Cicilan Pokok Utang Bank</td><td class="py-2.5 text-right font-semibold text-rose-600" id="bu_ak_cicilan">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Prive/Dividen</td><td class="py-2.5 text-right font-semibold text-rose-600" id="bu_ak_prive">—</td></tr>
                    <tr class="bg-violet-100/60">
                        <td class="py-3 pl-3 font-extrabold text-violet-900 rounded-l">Kas Pendanaan</td>
                        <td class="py-3 pr-1 text-right font-extrabold text-violet-700 rounded-r" id="bu_ak_kas_pendanaan">—</td>
                    </tr>

                    <!-- Rekap -->
                    <tr class="border-t-2 border-slate-200">
                        <td class="py-3 pl-2 font-bold text-slate-700">Kenaikan Kas Bersih</td>
                        <td class="py-3 text-right font-bold text-slate-700" id="bu_ak_kenaikan_bersih">—</td>
                    </tr>
                    <tr>
                        <td class="py-2.5 pl-2 text-slate-500 font-medium">Saldo Kas Awal Periode</td>
                        <td class="py-2.5 text-right font-semibold text-slate-600" id="bu_ak_saldo_awal">—</td>
                    </tr>
                    <tr class="bg-slate-800 text-white">
                        <td class="py-4 pl-3 font-extrabold text-base rounded-l">SALDO KAS AKHIR PERIODE</td>
                        <td class="py-4 pr-3 text-right font-black text-base rounded-r" id="bu_ak_kas_akhir">—</td>
                    </tr>
                </tbody>
            </table>

            <!-- Sinkronisasi info -->
            <div class="mt-4 p-3 rounded-xl border text-xs flex items-start gap-2" id="infoSinkronAK">
                <!-- diisi JS -->
            </div>
        </div>

        <!-- ============================================================
             TAB 3: NERACA
             ============================================================ -->
        <div id="panelBU_nr" class="tabPanelBU hidden px-6 sm:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">NERACA (BALANCE SHEET)</div>
                <div class="text-xs text-slate-400 font-semibold" id="periodeNR">Des <?= date('Y') ?></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- KOLOM KIRI: ASET -->
                <div>
                    <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-3 text-center">ASET</div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr><td class="py-2.5 text-slate-600 font-medium">Kas &amp; Bank</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_kas">—</td></tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Piutang Usaha <span id="tagNrPiutang" class="ml-1 text-[10px] text-amber-500"></span></td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_piutang">—</td></tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Persediaan <span id="tagNrPersediaan" class="ml-1 text-[10px] text-amber-500"></span></td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_persediaan">—</td></tr>
                            <tr class="bg-blue-50/60">
                                <td class="py-3 pl-2 font-extrabold text-blue-800 rounded-l">Total Aset Lancar</td>
                                <td class="py-3 pr-1 text-right font-extrabold text-blue-700 rounded-r" id="bu_nr_total_aset_lancar">—</td>
                            </tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Aset Tetap Neto</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_aset_tetap_neto">—</td></tr>
                            <tr class="bg-slate-800 text-white">
                                <td class="py-4 pl-3 font-extrabold text-base rounded-l">TOTAL ASET</td>
                                <td class="py-4 pr-3 text-right font-black text-base rounded-r" id="bu_nr_total_aset">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- KOLOM KANAN: LIABILITAS & EKUITAS -->
                <div>
                    <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-3 text-center">LIABILITAS &amp; EKUITAS</div>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr><td class="py-2.5 text-slate-600 font-medium">Utang Usaha <span id="tagNrUtang" class="ml-1 text-[10px] text-amber-500"></span></td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_utang_usaha">—</td></tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Utang Bank</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_utang_bank">—</td></tr>
                            <tr class="bg-rose-50/70">
                                <td class="py-3 pl-2 font-extrabold text-rose-800 rounded-l">Total Liabilitas</td>
                                <td class="py-3 pr-1 text-right font-extrabold text-rose-700 rounded-r" id="bu_nr_total_liab">—</td>
                            </tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Modal Disetor</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_modal">—</td></tr>
                            <tr><td class="py-2.5 text-slate-600 font-medium">Laba Ditahan</td><td class="py-2.5 text-right font-semibold text-slate-700" id="bu_nr_laba_ditahan">—</td></tr>
                            <tr class="bg-emerald-50/70">
                                <td class="py-3 pl-2 font-extrabold text-emerald-800 rounded-l">Total Ekuitas</td>
                                <td class="py-3 pr-1 text-right font-extrabold text-emerald-700 rounded-r" id="bu_nr_total_ekuitas">—</td>
                            </tr>
                            <tr class="bg-slate-800 text-white">
                                <td class="py-4 pl-3 font-extrabold text-base rounded-l">TOTAL LIABILITAS</td>
                                <td class="py-4 pr-3 text-right font-black text-base rounded-r" id="bu_nr_total_liab_ekuitas">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Validasi Neraca -->
            <div class="mt-5 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div id="badgeNeracaNR" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold">
                    <!-- diisi JS -->
                </div>
                <div class="flex-1 text-xs text-slate-500" id="infoRasioNR">
                    <!-- diisi JS: rasio ringkas -->
                </div>
                <div id="statusNR" class="text-xs font-bold text-slate-600">Status: —</div>
            </div>

            <!-- Ekspor -->
            <div class="mt-4 flex gap-2 justify-end">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    📄 Cetak PDF
                </button>
                <button type="button" onclick="exportExcelLaporan()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    📊 Export Excel
                </button>
            </div>
        </div>

    </div><!-- /panelHasilLaporanBU -->

    <!-- Footer -->
    <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
        <div class="text-[11px] text-slate-400 font-medium">
            ID Dokumen: <span id="idDokumenLaporan"><?= !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-' . date('Y') . '-' . str_pad($kredit_id ?? 0, 4, '0', STR_PAD_LEFT) ?></span>
        </div>
        <button type="button" id="btnGenerateUlang" onclick="generateLaporanKeuangan()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-xs">
            <svg id="ikonGenUlangDefault" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <svg id="ikonGenUlangSpinner" class="hidden w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span id="textGenUlang"><?= $sudahGenerate ? '🔄 Generate Ulang' : '⚡ Generate Laporan' ?></span>
        </button>
    </div>

</div>

<style>
.tabNavBU { transition: color .15s, border-color .15s; }
.tabNavBU.active { color: #2563eb !important; border-color: #2563eb !important; }
</style>

<script>
// ============================================================
// HELPER FORMAT
// ============================================================
function rpFmt(val) {
    const n = parseFloat(val);
    if (isNaN(n)) return '—';
    if (n === 0) return 'Rp 0';
    const abs = Math.abs(n);
    const str = 'Rp ' + abs.toLocaleString('id-ID', {minimumFractionDigits: 0});
    return n < 0 ? '(' + str + ')' : str;
}

function rpFmtColor(val, negMeansDeduction) {
    // negMeansDeduction: true = negatif → ditampilkan () merah
    const n = parseFloat(val);
    if (isNaN(n)) return '<span class="text-slate-400">—</span>';
    const display = rpFmt(val);
    if (n < 0) {
        return '<span class="text-rose-600">' + display + '</span>';
    } else if (n > 0 && negMeansDeduction) {
        return '<span class="text-slate-700">' + display + '</span>';
    }
    return '<span class="text-emerald-600">' + display + '</span>';
}

// ============================================================
// SWITCH TAB
// ============================================================
function switchTabLaporan(tab) {
    // Sembunyikan semua panel
    document.querySelectorAll('.tabPanelBU').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tabNavBU').forEach(b => b.classList.remove('active'));

    const panelMap = { lr: 'panelBU_lr', ak: 'panelBU_ak', nr: 'panelBU_nr' };
    const navMap   = { lr: 'navBU_lr',   ak: 'navBU_ak',   nr: 'navBU_nr' };

    if (panelMap[tab]) {
        document.getElementById(panelMap[tab])?.classList.remove('hidden');
        document.getElementById(navMap[tab])?.classList.add('active');
    }
}

// ============================================================
// ISI DATA LAPORAN KE DOM
// ============================================================
function isiDataLaporanBU(data) {
    if (!data) return;
    const lr = data.laba_rugi  || {};
    const ak = data.arus_kas   || {};
    const nr = data.neraca     || {};

    // --- LABA RUGI ---
    document.getElementById('bu_lr_pendapatan').textContent   = rpFmt(lr.pendapatan);
    document.getElementById('bu_lr_hpp').textContent          = lr.hpp > 0 ? '(' + rpFmt(lr.hpp) + ')' : '—';
    document.getElementById('bu_lr_laba_kotor').textContent   = rpFmt(lr.laba_kotor);
    // Label gaji — tampilkan jumlah karyawan jika ada
    const karyawanJumlah = window._jumlahKaryawan || 0;
    if (karyawanJumlah > 0) {
        document.getElementById('labelBebanGaji').textContent = 'Beban Gaji (' + karyawanJumlah + ' karyawan)';
    }
    document.getElementById('bu_lr_beban_gaji').textContent   = lr.beban_gaji > 0 ? '(' + rpFmt(lr.beban_gaji) + ')' : 'Rp 0';
    document.getElementById('bu_lr_beban_op').textContent     = lr.beban_operasional > 0 ? '(' + rpFmt(lr.beban_operasional) + ')' : 'Rp 0';
    document.getElementById('bu_lr_penyusutan').textContent   = lr.beban_penyusutan > 0 ? '(' + rpFmt(lr.beban_penyusutan) + ')' : 'Rp 0';
    document.getElementById('bu_lr_laba_op').textContent      = rpFmt(lr.laba_operasional);
    document.getElementById('bu_lr_beban_bunga').textContent  = lr.beban_bunga > 0 ? '(' + rpFmt(lr.beban_bunga) + ')' : 'Rp 0';
    document.getElementById('bu_lr_pajak').textContent        = lr.pajak > 0 ? '(' + rpFmt(lr.pajak) + ')' : 'Rp 0';
    document.getElementById('bu_lr_laba_bersih').textContent  = rpFmt(lr.laba_bersih);

    // Badge neraca + rasio di tab Laba Rugi
    const marginPct = nr.margin_laba_bersih !== undefined ? nr.margin_laba_bersih : (lr.pendapatan > 0 ? ((lr.laba_bersih / lr.pendapatan) * 100).toFixed(2) : 0);
    const neracaOk  = (data.validasi?.status_neraca === 'Seimbang');
    document.getElementById('badgeNeracaLR').className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold '
        + (neracaOk ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200');
    document.getElementById('badgeNeracaLR').innerHTML = (neracaOk
        ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Neraca Seimbang'
        : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.962-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>Neraca Tidak Seimbang');
    document.getElementById('infoRasioLR').textContent = 'Margin Laba Bersih: ' + parseFloat(marginPct).toFixed(2) + '% (Di atas ambang batas risiko 12%)';
    document.getElementById('statusLR').textContent   = 'Status: ' + (neracaOk ? 'Terverifikasi' : 'Perlu Periksa');

    // --- ARUS KAS ---
    document.getElementById('bu_ak_laba_bersih').textContent        = rpFmt(ak.laba_bersih);
    document.getElementById('bu_ak_penyusutan').textContent          = rpFmt(ak.penyusutan);
    // Kenaikan piutang — negatif artinya pengurangan kas
    const kp = parseFloat(ak.kenaikan_piutang || 0);
    document.getElementById('bu_ak_kenaikan_piutang').innerHTML = kp > 0
        ? '<span class="text-rose-600">(' + rpFmt(kp) + ')</span>'
        : '<span class="text-emerald-600">' + rpFmt(Math.abs(kp)) + '</span>';
    const ks = parseFloat(ak.kenaikan_persediaan || 0);
    document.getElementById('bu_ak_kenaikan_persediaan').innerHTML = ks > 0
        ? '<span class="text-rose-600">(' + rpFmt(ks) + ')</span>'
        : '<span class="text-emerald-600">' + rpFmt(Math.abs(ks)) + '</span>';
    const ku = parseFloat(ak.kenaikan_utang_usaha || 0);
    document.getElementById('bu_ak_kenaikan_utang').innerHTML = ku >= 0
        ? '<span class="text-emerald-600">' + rpFmt(ku) + '</span>'
        : '<span class="text-rose-600">(' + rpFmt(Math.abs(ku)) + ')</span>';
    document.getElementById('bu_ak_kas_operasi').textContent        = rpFmt(ak.kas_operasional);
    document.getElementById('bu_ak_beli_aset').textContent          = ak.pembelian_aset_tetap > 0 ? '(' + rpFmt(ak.pembelian_aset_tetap) + ')' : 'Rp 0';
    document.getElementById('bu_ak_kas_investasi').textContent      = rpFmt(ak.kas_investasi);
    document.getElementById('bu_ak_cicilan').textContent            = ak.cicilan_pokok > 0 ? '(' + rpFmt(ak.cicilan_pokok) + ')' : 'Rp 0';
    document.getElementById('bu_ak_prive').textContent              = ak.prive_dividen > 0 ? '(' + rpFmt(ak.prive_dividen) + ')' : 'Rp 0';
    document.getElementById('bu_ak_kas_pendanaan').textContent      = rpFmt(ak.kas_pendanaan);
    document.getElementById('bu_ak_kenaikan_bersih').textContent    = rpFmt(ak.kenaikan_bersih_kas);
    document.getElementById('bu_ak_saldo_awal').textContent         = rpFmt(ak.saldo_awal_kas);
    document.getElementById('bu_ak_kas_akhir').textContent          = rpFmt(ak.kas_akhir);

    // Tag estimasi arus kas
    if (data.neraca) {
        document.getElementById('tagPiutang').textContent    = data.neraca.piutang_sumber === 'estimasi' ? '(Estimasi DSO)' : '';
        document.getElementById('tagPersediaan').textContent = data.neraca.persediaan_sumber === 'estimasi' ? '(Estimasi DIO)' : '';
        document.getElementById('tagUtang').textContent      = data.neraca.utang_usaha_sumber === 'estimasi' ? '(Estimasi DPO)' : '';
    }

    // Info sinkronisasi kas
    const kasAkAK = parseFloat(ak.kas_akhir || 0);
    const kasNR   = parseFloat(nr.kas_bank || 0);
    const selKas  = Math.abs(kasAkAK - kasNR);
    const infoEl  = document.getElementById('infoSinkronAK');
    if (infoEl) {
        infoEl.className = 'mt-4 p-3 rounded-xl border text-xs flex items-start gap-2 '
            + (selKas < 1 ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700');
        infoEl.innerHTML = 'Kas Akhir Arus Kas (' + rpFmt(kasAkAK) + ') sinkron '
            + (selKas < 1 ? '100%' : 'selisih Rp ' + selKas.toLocaleString('id-ID'))
            + ' dengan Kas &amp; Bank Neraca.&nbsp;&nbsp;'
            + '<strong>Status: ' + (selKas < 1 ? 'Sinkron 100%' : 'Perlu Periksa') + '</strong>';
    }

    // --- NERACA ---
    if (nr && Object.keys(nr).length > 0) {
        document.getElementById('bu_nr_kas').textContent              = rpFmt(nr.kas_bank);
        document.getElementById('bu_nr_piutang').textContent          = rpFmt(nr.piutang_usaha);
        document.getElementById('bu_nr_persediaan').textContent       = rpFmt(nr.persediaan);
        document.getElementById('bu_nr_total_aset_lancar').textContent= rpFmt(nr.total_aset_lancar);
        document.getElementById('bu_nr_aset_tetap_neto').textContent  = rpFmt(nr.aset_tetap_neto);
        document.getElementById('bu_nr_total_aset').textContent       = rpFmt(nr.total_aset);
        document.getElementById('bu_nr_utang_usaha').textContent      = rpFmt(nr.utang_usaha);
        document.getElementById('bu_nr_utang_bank').textContent       = rpFmt((parseFloat(nr.utang_bank_pendek||0) + parseFloat(nr.utang_jangka_panjang||0)));
        document.getElementById('bu_nr_total_liab').textContent       = rpFmt(nr.total_liabilitas);
        document.getElementById('bu_nr_modal').textContent            = rpFmt(nr.modal_disetor);
        document.getElementById('bu_nr_laba_ditahan').textContent     = rpFmt(nr.laba_ditahan_akhir);
        document.getElementById('bu_nr_total_ekuitas').textContent    = rpFmt(nr.total_ekuitas);
        document.getElementById('bu_nr_total_liab_ekuitas').textContent = rpFmt(nr.total_liabilitas_ekuitas);

        // Tag estimasi neraca
        document.getElementById('tagNrPiutang').textContent    = nr.piutang_sumber === 'estimasi' ? '(Estimasi)' : '';
        document.getElementById('tagNrPersediaan').textContent = nr.persediaan_sumber === 'estimasi' ? '(Estimasi)' : '';
        document.getElementById('tagNrUtang').textContent      = nr.utang_usaha_sumber === 'estimasi' ? '(Estimasi)' : '';

        // Badge validasi neraca
        const neracaOk2 = (nr.status_neraca === 'Seimbang');
        document.getElementById('badgeNeracaNR').className = 'inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold '
            + (neracaOk2 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200');
        const selisih = parseFloat(nr.selisih_neraca || 0);
        document.getElementById('badgeNeracaNR').innerHTML = neracaOk2
            ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Neraca Seimbang — Selisih matematis: Rp 0 (Validasi mesin risiko lulus)'
            : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856"/></svg>Neraca Tidak Seimbang — Selisih: Rp ' + Math.abs(selisih).toLocaleString('id-ID');

        // Rasio ringkas
        const margin = nr.margin_laba_bersih !== undefined ? nr.margin_laba_bersih : 0;
        const cr     = nr.current_ratio     !== undefined ? nr.current_ratio : '—';
        const de     = nr.debt_to_equity    !== undefined ? nr.debt_to_equity : '—';
        document.getElementById('infoRasioNR').innerHTML = 'Margin Laba Bersih: <strong>' + parseFloat(margin).toFixed(2) + '%</strong>'
            + ' &nbsp;|&nbsp; Current Ratio: <strong>' + (cr !== null ? cr : '—') + 'x</strong>'
            + ' &nbsp;|&nbsp; DER: <strong>' + (de !== null ? de : '—') + 'x</strong>';
        document.getElementById('statusNR').textContent = 'Status: ' + (selKas < 1 && neracaOk2 ? 'Sinkron 100%' : 'Perlu Periksa');
    }
}

// ============================================================
// GENERATE LAPORAN (AJAX)
// ============================================================
function generateLaporanKeuangan() {
    const kreditId = document.getElementById('kredit_id')?.value
        || document.querySelector('[name="kredit_id"]')?.value;
    if (!kreditId) {
        Swal.fire({ icon: 'warning', title: 'Simpan Dulu', text: 'Simpan form pengajuan terlebih dahulu sebelum generate laporan.' });
        return;
    }

    // Ambil semua input neraca dari Tab 9
    function getV(id) {
        const el = document.getElementById(id);
        if (!el) return '';
        return el.value.replace(/\./g, '').replace(',', '.');
    }

    const payload = new FormData();
    payload.append('kredit_id', kreditId);
    // Aset
    payload.append('saldo_awal_kas',          getV('lkn_saldo_awal_kas'));
    payload.append('piutang_usaha',            getV('lkn_piutang_usaha'));
    payload.append('persediaan',               getV('lkn_persediaan'));
    payload.append('aset_lancar_lain',         getV('lkn_aset_lancar_lain'));
    payload.append('saldo_awal_piutang',       getV('lkn_saldo_awal_piutang'));
    payload.append('saldo_awal_persediaan',    getV('lkn_saldo_awal_persediaan'));
    // Aset Tetap
    payload.append('harga_perolehan_aset_tetap', getV('lkn_harga_perolehan'));
    payload.append('akumulasi_penyusutan',     getV('lkn_akum_penyusutan'));
    payload.append('umur_ekonomis_tahun',      document.getElementById('lkn_umur_ekonomis')?.value || '5');
    payload.append('pembelian_aset_tetap',     getV('lkn_beli_aset'));
    payload.append('beban_penyusutan',         getV('lkn_beban_penyusutan'));
    payload.append('saldo_awal_aset_tetap',    getV('lkn_saldo_awal_aset'));
    // Liabilitas
    payload.append('utang_usaha',              getV('lkn_utang_usaha'));
    payload.append('utang_bank_pendek',        getV('lkn_utang_bank_pendek'));
    payload.append('utang_jangka_panjang',     getV('lkn_utang_jk_panjang'));
    payload.append('cicilan_pokok',            getV('lkn_cicilan_pokok'));
    payload.append('saldo_awal_utang_usaha',   getV('lkn_saldo_awal_utang'));
    payload.append('saldo_awal_utang_bank',    getV('lkn_saldo_awal_utang_bank'));
    // Ekuitas
    payload.append('modal_disetor',            getV('lkn_modal_disetor'));
    payload.append('laba_ditahan_awal',        getV('lkn_laba_ditahan_awal'));
    payload.append('prive_dividen',            getV('lkn_prive_dividen'));
    payload.append('beban_bunga',              getV('lkn_beban_bunga'));
    const pajakVal = getV('lkn_pajak');
    if (pajakVal) payload.append('pajak', pajakVal);
    // Asumsi
    payload.append('asumsi_dso', document.getElementById('lkn_asumsi_dso')?.value || '30');
    payload.append('asumsi_dio', document.getElementById('lkn_asumsi_dio')?.value || '45');
    payload.append('asumsi_dpo', document.getElementById('lkn_asumsi_dpo')?.value || '30');

    // CSRF
    const csrf = document.querySelector('[name="csrf_token"]');
    if (csrf) payload.append(csrf.name, csrf.value);

    // Loading state
    ['btnGenerateLaporanBU', 'btnGenerateUlang'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = true;
    });
    ['ikonGenDefaultBU','ikonGenUlangDefault'].forEach(id => document.getElementById(id)?.classList.add('hidden'));
    ['ikonGenSpinnerBU','ikonGenUlangSpinner'].forEach(id => document.getElementById(id)?.classList.remove('hidden'));
    ['textGenBU','textGenUlang'].forEach(id => { const el = document.getElementById(id); if(el) el.textContent = 'Memproses...'; });

    fetch(baseUrl + 'pengajuan/generate-laporan', { method: 'POST', body: payload })
        .then(r => r.json())
        .then(res => {
            if (!res.status) {
                Swal.fire({ icon: 'error', title: 'Gagal Generate', text: res.message || 'Terjadi kesalahan.' });
                return;
            }

            // Tampilkan panel
            document.getElementById('panelHasilLaporanBU')?.classList.remove('hidden');

            // Hitung jumlah karyawan dari data
            try {
                const karyawanArr = JSON.parse(document.querySelector('[name="daftar_karyawan"]')?.value || '[]');
                window._jumlahKaryawan = Array.isArray(karyawanArr) ? karyawanArr.length : 0;
            } catch(e) { window._jumlahKaryawan = 0; }

            // Isi data ke DOM
            isiDataLaporanBU(res.data);

            // Update stepper
            const neracaOk = res.data?.validasi?.is_seimbang;
            if (typeof tabValidationStatus !== 'undefined') {
                tabValidationStatus['laporan'] = !!neracaOk;
                if (typeof updateStepperStatus === 'function') updateStepperStatus();
                if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
            }

            if (res.warning) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', html: res.warning, confirmButtonText: 'Periksa Data' });
            } else {
                Swal.fire({ icon: 'success', title: 'Laporan Berhasil Dibuat!', text: res.message, timer: 2200, showConfirmButton: false });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Koneksi Error', text: 'Gagal terhubung ke server.' }))
        .finally(() => {
            ['btnGenerateLaporanBU', 'btnGenerateUlang'].forEach(id => { const el = document.getElementById(id); if(el) el.disabled = false; });
            ['ikonGenDefaultBU','ikonGenUlangDefault'].forEach(id => document.getElementById(id)?.classList.remove('hidden'));
            ['ikonGenSpinnerBU','ikonGenUlangSpinner'].forEach(id => document.getElementById(id)?.classList.add('hidden'));
            document.getElementById('textGenBU') && (document.getElementById('textGenBU').textContent = '⚡ Generate Laporan Keuangan');
            document.getElementById('textGenUlang') && (document.getElementById('textGenUlang').textContent = '🔄 Generate Ulang');
        });
}

function exportExcelLaporan() {
    Swal.fire({ icon: 'info', title: 'Export Excel', text: 'Fitur export Excel akan tersedia setelah integrasi dengan library PhpSpreadsheet.', timer: 2500, showConfirmButton: false });
}

// ============================================================
// AUTO LOAD DATA JIKA SUDAH ADA
// ============================================================
<?php if ($sudahGenerate && $lkJ): ?>
(function() {
    const data = <?= json_encode($lkJ) ?>;
    try {
        const karyawan = <?= !empty($kredit['daftar_karyawan']) ? json_encode(json_decode($kredit['daftar_karyawan'], true)) : '[]' ?>;
        window._jumlahKaryawan = Array.isArray(karyawan) ? karyawan.length : 0;
    } catch(e) { window._jumlahKaryawan = 0; }
    isiDataLaporanBU(data);
    if (typeof tabValidationStatus !== 'undefined' && <?= $isSeimbang ? 'true' : 'false' ?>) {
        tabValidationStatus['laporan'] = true;
        if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
    }
    // Set active tab
    switchTabLaporan('lr');
})();
<?php else: ?>
// Tab default saat belum ada data
switchTabLaporan('lr');
<?php endif; ?>
</script>
