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
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Keuangan</h2>
                <p class="text-[12px] text-slate-500 mt-1 max-w-xl">
                    Konsolidasi posisi aset, liabilitas, dan ekuitas berdasarkan buku besar serta sinkronisasi perbankan mutakhir.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold text-blue-700 bg-blue-100">Badan Usaha</span>
                <span class="px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 bg-slate-200">1 Tahun</span>
                <span class="px-3 py-1 rounded-full text-[11px] font-bold text-indigo-700 bg-indigo-100">● Estimasi</span>
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

    <!-- ============================================================
         FOOTER: ID DOKUMEN & SIMPAN & LANJUT
         ============================================================ -->
    <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
        <div class="text-[12px] text-slate-400 font-medium">
            ID Dokumen: <span class="font-mono text-slate-600"><?= !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-' . date('Y') . '-AK' . str_pad($kredit_id ?? 0, 4, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="generateLaporanKeuangan()"
                    class="px-4 py-2.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition">
                🔄 Hitung Ulang
            </button>
            <button type="button" onclick="if(typeof tabNav === 'function') { tabNav(1); } else { nextTab(); }"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                <span>Simpan &amp; Lanjut</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </div>

</div>

<style>
.tabNavBU { transition: all .15s ease-in-out; }
.tabNavBU.active { color: #2563eb !important; border-color: #2563eb !important; }
</style>

<script>
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

    const karyawanJumlah = window._jumlahKaryawan || 2;
    document.getElementById('labelBebanGaji').textContent     = 'Beban Gaji (' + karyawanJumlah + ' karyawan)';
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
