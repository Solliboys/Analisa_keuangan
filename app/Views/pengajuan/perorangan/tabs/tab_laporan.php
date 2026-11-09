<?php
$lk  = $laporan_keuangan ?? [];
$lkJ = !empty($lk['json_laporan']) ? json_decode($lk['json_laporan'], true) : null;
$sudahGenerate = !empty($lk['updated_at']) && !empty($lkJ);
?>
<div id="tab-laporan" class="tab-panel bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">

    <!-- HEADER -->
    <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-4 border-b border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Keuangan</h2>
                <p class="text-[11px] text-slate-500 mt-1 max-w-lg">
                    Ringkasan penghasilan, pengeluaran, dan arus kas estimasi berdasarkan data laba rugi perorangan.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200">Perorangan</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200">Bulanan</span>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200">✦ Estimasi</span>
            </div>
        </div>
    </div>

    <!-- Input tambahan (Perorangan) -->
    <div class="px-6 sm:px-8 mt-5">
        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/90">
            <div class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider mb-3">Parameter Tambahan</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Kas Awal (Rp)</label>
                    <input type="text" id="lkp_saldo_awal_kas" name="lkp_saldo_awal_kas"
                           value="<?= !empty($lk['saldo_awal_kas']) ? number_format((float)$lk['saldo_awal_kas'], 0, ',', '.') : '' ?>"
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                           placeholder="Rp 0" oninput="formatRupiah(this)">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Prive / Penarikan (Rp)</label>
                    <input type="text" id="lkp_prive_dividen" name="lkp_prive_dividen"
                           value="<?= !empty($lkJ['arus_kas']['prive_dividen']) ? number_format((float)$lkJ['arus_kas']['prive_dividen'], 0, ',', '.') : '' ?>"
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                           placeholder="Rp 0" oninput="formatRupiah(this)">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Bunga (Rp)</label>
                    <input type="text" id="lkp_beban_bunga" name="lkp_beban_bunga"
                           value="<?= !empty($lkJ['laba_rugi']['beban_bunga']) ? number_format((float)$lkJ['laba_rugi']['beban_bunga'], 0, ',', '.') : '' ?>"
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                           placeholder="Rp 0" oninput="formatRupiah(this)">
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Generate -->
    <div class="px-6 sm:px-8 mt-4 mb-2">
        <button type="button" id="btnGenerateLaporanPR" onclick="generateLaporanKeuanganPR()"
                class="inline-flex items-center gap-2.5 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-xs transition">
            <svg id="ikonPrDefault" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <svg id="ikonPrSpinner" class="hidden w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span id="textGenPR"><?= $sudahGenerate ? '🔄 Generate Ulang' : '⚡ Generate Laporan' ?></span>
        </button>
    </div>

    <!-- Panel Laporan -->
    <div id="panelHasilLaporanPR" class="<?= $sudahGenerate ? '' : 'hidden' ?>">

        <!-- Tab Nav -->
        <div class="px-6 sm:px-8 pt-3">
            <div class="flex border-b border-slate-200 gap-0">
                <button type="button" onclick="switchTabLaporan('lr')" id="navPR_lr"
                        class="tabNavPR pb-3 px-4 text-xs font-bold border-b-2 transition text-blue-600 border-blue-600">Laba Rugi</button>
                <button type="button" onclick="switchTabLaporan('ak')" id="navPR_ak"
                        class="tabNavPR pb-3 px-4 text-xs font-bold border-b-2 transition text-slate-400 border-transparent hover:text-slate-600">Arus Kas</button>
            </div>
        </div>

        <!-- TAB LABA RUGI -->
        <div id="panelPR_lr" class="tabPanelPR px-6 sm:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">LABA RUGI (INCOME STATEMENT)</div>
                <div class="text-xs text-slate-400 font-semibold">Periode <?= date('Y') ?></div>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="py-3 text-slate-600 font-medium">Penghasilan Utama</td><td class="py-3 text-right font-semibold text-slate-800" id="pr_lr_pendapatan">—</td></tr>
                    <tr><td class="py-3 text-slate-600 font-medium">Pendapatan Lain-Lain</td><td class="py-3 text-right font-semibold text-emerald-600" id="pr_lr_pend_lain">—</td></tr>
                    <tr class="bg-blue-50/60"><td class="py-3 pl-3 font-extrabold text-blue-900 rounded-l">Total Penghasilan</td><td class="py-3 pr-3 text-right font-extrabold text-blue-700 rounded-r" id="pr_lr_total">—</td></tr>
                    <tr><td class="py-3 text-slate-600 font-medium">Pengeluaran Rumah Tangga</td><td class="py-3 text-right font-semibold text-slate-600" id="pr_lr_beban_op">—</td></tr>
                    <tr><td class="py-3 text-slate-600 font-medium">Beban Bunga</td><td class="py-3 text-right font-semibold text-slate-600" id="pr_lr_beban_bunga">—</td></tr>
                    <tr><td class="py-3 text-slate-600 font-medium">Pajak (Estimasi)</td><td class="py-3 text-right font-semibold text-slate-600" id="pr_lr_pajak">—</td></tr>
                    <tr class="bg-blue-600 text-white"><td class="py-4 pl-3 font-extrabold text-base rounded-l">SURPLUS / LABA BERSIH</td><td class="py-4 pr-3 text-right font-black text-base rounded-r" id="pr_lr_laba_bersih">—</td></tr>
                </tbody>
            </table>
        </div>

        <!-- TAB ARUS KAS -->
        <div id="panelPR_ak" class="tabPanelPR hidden px-6 sm:px-8 py-6">
            <div class="flex items-center justify-between mb-5">
                <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">ARUS KAS (CASH FLOW STATEMENT)</div>
                <div class="text-xs text-slate-400 font-semibold">Periode <?= date('Y') ?></div>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr class="bg-blue-50/50"><td colspan="2" class="py-2.5 pl-2 font-extrabold text-blue-800 text-xs uppercase tracking-wider">Arus Masuk</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Penghasilan Utama</td><td class="py-2.5 text-right font-semibold text-emerald-600" id="pr_ak_pendapatan">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Pendapatan Lain-Lain</td><td class="py-2.5 text-right font-semibold text-emerald-600" id="pr_ak_pend_lain">—</td></tr>
                    <tr class="bg-blue-50/50"><td colspan="2" class="py-2.5 pl-2 font-extrabold text-blue-800 text-xs uppercase tracking-wider">Arus Keluar</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Pengeluaran Rumah Tangga</td><td class="py-2.5 text-right font-semibold text-rose-600" id="pr_ak_beban_op">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Beban Bunga</td><td class="py-2.5 text-right font-semibold text-rose-600" id="pr_ak_bunga">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Pajak</td><td class="py-2.5 text-right font-semibold text-rose-600" id="pr_ak_pajak">—</td></tr>
                    <tr><td class="py-2.5 pl-4 text-slate-600 font-medium">Prive / Penarikan</td><td class="py-2.5 text-right font-semibold text-rose-600" id="pr_ak_prive">—</td></tr>
                    <tr class="border-t-2 border-slate-200"><td class="py-3 pl-2 font-bold text-slate-700">Kenaikan Kas Bersih</td><td class="py-3 text-right font-bold text-slate-700" id="pr_ak_kenaikan">—</td></tr>
                    <tr><td class="py-2.5 pl-2 text-slate-500 font-medium">Saldo Kas Awal</td><td class="py-2.5 text-right font-semibold" id="pr_ak_saldo_awal">—</td></tr>
                    <tr class="bg-slate-800 text-white"><td class="py-4 pl-3 font-extrabold text-base rounded-l">SALDO KAS AKHIR</td><td class="py-4 pr-3 text-right font-black text-base rounded-r" id="pr_ak_kas_akhir">—</td></tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Footer -->
    <div class="px-6 sm:px-8 py-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-2">
        <div class="text-[11px] text-slate-400 font-medium">
            ID Dokumen: <?= !empty($kredit['nomor_aplikasi']) ? esc($kredit['nomor_aplikasi']) : 'NC-' . date('Y') . '-' . str_pad($kredit_id ?? 0, 4, '0', STR_PAD_LEFT) ?>
        </div>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition shadow-2xs">
            📄 Cetak
        </button>
    </div>

</div>

<style>
.tabNavPR { transition: color .15s, border-color .15s; }
.tabNavPR.active { color: #2563eb !important; border-color: #2563eb !important; }
</style>

<script>
const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
function switchTabLaporan(tab) {
    document.querySelectorAll('.tabPanelPR').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tabNavPR').forEach(b => b.classList.remove('active'));
    const pm = { lr: 'panelPR_lr', ak: 'panelPR_ak' };
    const nm = { lr: 'navPR_lr',   ak: 'navPR_ak' };
    if (pm[tab]) { document.getElementById(pm[tab])?.classList.remove('hidden'); document.getElementById(nm[tab])?.classList.add('active'); }
}

function rpFmt(val) {
    const n = parseFloat(val);
    if (isNaN(n)) return '—';
    const abs = Math.abs(n);
    const s = 'Rp ' + abs.toLocaleString('id-ID', {minimumFractionDigits: 0});
    return n < 0 ? '(' + s + ')' : s;
}

function isiDataLaporanPR(data) {
    const lr = data.laba_rugi || {};
    const ak = data.arus_kas  || {};
    // Total penghasilan = pendapatan + pendapatan_lain
    const total = parseFloat(lr.pendapatan||0) + parseFloat(lr.pendapatan_lain||0);

    document.getElementById('pr_lr_pendapatan').textContent  = rpFmt(lr.pendapatan);
    document.getElementById('pr_lr_pend_lain').textContent   = rpFmt(lr.pendapatan_lain);
    document.getElementById('pr_lr_total').textContent       = rpFmt(total);
    document.getElementById('pr_lr_beban_op').textContent    = lr.beban_operasional > 0 ? '(' + rpFmt(lr.beban_operasional) + ')' : 'Rp 0';
    document.getElementById('pr_lr_beban_bunga').textContent = lr.beban_bunga > 0 ? '(' + rpFmt(lr.beban_bunga) + ')' : 'Rp 0';
    document.getElementById('pr_lr_pajak').textContent       = lr.pajak > 0 ? '(' + rpFmt(lr.pajak) + ')' : 'Rp 0';
    document.getElementById('pr_lr_laba_bersih').textContent = rpFmt(lr.laba_bersih);

    document.getElementById('pr_ak_pendapatan').textContent  = rpFmt(lr.pendapatan);
    document.getElementById('pr_ak_pend_lain').textContent   = rpFmt(lr.pendapatan_lain);
    document.getElementById('pr_ak_beban_op').textContent    = lr.beban_operasional > 0 ? '(' + rpFmt(lr.beban_operasional) + ')' : 'Rp 0';
    document.getElementById('pr_ak_bunga').textContent       = lr.beban_bunga > 0 ? '(' + rpFmt(lr.beban_bunga) + ')' : 'Rp 0';
    document.getElementById('pr_ak_pajak').textContent       = lr.pajak > 0 ? '(' + rpFmt(lr.pajak) + ')' : 'Rp 0';
    document.getElementById('pr_ak_prive').textContent       = ak.prive_dividen > 0 ? '(' + rpFmt(ak.prive_dividen) + ')' : 'Rp 0';
    document.getElementById('pr_ak_kenaikan').textContent    = rpFmt(ak.kenaikan_bersih_kas);
    document.getElementById('pr_ak_saldo_awal').textContent  = rpFmt(ak.saldo_awal_kas);
    document.getElementById('pr_ak_kas_akhir').textContent   = rpFmt(ak.kas_akhir);
}

function generateLaporanKeuanganPR() {
    const kreditId = document.getElementById('kredit_id')?.value || document.querySelector('[name="kredit_id"]')?.value;
    if (!kreditId) { Swal.fire({ icon: 'warning', title: 'Simpan Dulu', text: 'Simpan form terlebih dahulu.' }); return; }

    function getV(id) { const el = document.getElementById(id); return el ? el.value.replace(/\./g, '').replace(',', '.') : ''; }

    const payload = new FormData();
    payload.append('kredit_id', kreditId);
    payload.append('saldo_awal_kas', getV('lkp_saldo_awal_kas'));
    payload.append('prive_dividen',  getV('lkp_prive_dividen'));
    payload.append('beban_bunga',    getV('lkp_beban_bunga'));
    const csrf = document.querySelector('[name="csrf_token"]');
    if (csrf) payload.append(csrf.name, csrf.value);

    document.getElementById('btnGenerateLaporanPR').disabled = true;
    document.getElementById('ikonPrDefault').classList.add('hidden');
    document.getElementById('ikonPrSpinner').classList.remove('hidden');
    document.getElementById('textGenPR').textContent = 'Memproses...';

    fetch(baseUrl + 'pengajuan/generate-laporan', { method: 'POST', body: payload })
        .then(r => r.json())
        .then(res => {
            if (!res.status) { Swal.fire({ icon: 'error', title: 'Gagal', text: res.message }); return; }
            document.getElementById('panelHasilLaporanPR')?.classList.remove('hidden');
            isiDataLaporanPR(res.data);
            if (typeof tabValidationStatus !== 'undefined') {
                tabValidationStatus['laporan'] = true;
                if (typeof updateStepperStatus === 'function') updateStepperStatus();
                if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
            }
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 2000, showConfirmButton: false });
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Koneksi gagal.' }))
        .finally(() => {
            document.getElementById('btnGenerateLaporanPR').disabled = false;
            document.getElementById('ikonPrDefault').classList.remove('hidden');
            document.getElementById('ikonPrSpinner').classList.add('hidden');
            document.getElementById('textGenPR').textContent = '🔄 Generate Ulang';
        });
}

<?php if ($sudahGenerate && $lkJ): ?>
(function() {
    isiDataLaporanPR(<?= json_encode($lkJ) ?>);
    if (typeof tabValidationStatus !== 'undefined') {
        tabValidationStatus['laporan'] = true;
        if (typeof updateStepperVisuals === 'function') updateStepperVisuals();
    }
    switchTabLaporan('lr');
})();
<?php else: ?>
switchTabLaporan('lr');
<?php endif; ?>
</script>
