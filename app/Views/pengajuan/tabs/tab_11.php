<?php
$biayaLainList = [];
if (!empty($disbursement['rincian_biaya_lain'])) {
    $decoded = json_decode($disbursement['rincian_biaya_lain'], true);
    if (is_array($decoded)) {
        $biayaLainList = $decoded;
    }
}
?>
<div id="tab-11" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                <?= (!empty($view_folder) && $view_folder === 'badan_usaha') ? '11' : '12' ?>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">SIMULASI BIAYA & PENCAIRAN BERSIH (DISBURSEMENT)</h3>
                <p class="text-[11px] text-slate-500">Rincian seluruh komponen biaya potong muka dan estimasi dana riil yang diterima debitur saat akad</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Simulasi Potongan Muka
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Left: Input Biaya -->
        <div class="space-y-4">
            <!-- Provisi -->
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Provisi (%)</label>
                    <input type="number" step="0.1" name="provisi_persen" id="provisi_persen" 
                           value="<?= esc($disbursement['provisi_persen'] ?? 7) ?>" 
                           class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                           oninput="hitungDisbursement();">
                </div>
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Provisi</label>
                    <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                        <span id="nomProvisi" class="font-bold text-slate-800">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Admin Fee -->
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-1">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Admin Fee (%)</label>
                    <input type="number" step="0.1" name="admin_persen" id="admin_persen" 
                           value="<?= esc($disbursement['admin_persen'] ?? 1) ?>" 
                           class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                           oninput="hitungDisbursement();">
                </div>
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Admin Fee</label>
                    <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                        <span id="nomAdmin" class="font-bold text-slate-800">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic Biaya / Potongan Lainnya (Keterangan & Nominal) -->
            <div class="pt-3 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Potongan & Biaya Lainnya</label>
                        <p class="text-[11px] text-slate-400">Komponen biaya potongan muka tambahan</p>
                    </div>
                    <button type="button" onclick="tambahBarisBiayaLain()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Biaya</span>
                    </button>
                </div>

                <!-- Container Baris Biaya Lain -->
                <div id="containerBiayaLain" class="space-y-2.5">
                    <?php if (empty($biayaLainList)): ?>
                        <div id="emptyBiayaLainMsg" class="p-5 text-center border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-xs italic bg-slate-50/40">
                            Belum ada biaya potongan tambahan. Klik tombol <strong>+ Tambah Biaya</strong> di atas untuk menambahkan.
                        </div>
                    <?php else: ?>
                        <?php foreach ($biayaLainList as $idx => $biaya): ?>
                            <div class="baris-biaya-lain flex items-center gap-2.5 p-2.5 bg-slate-50/70 border border-slate-200 rounded-xl transition hover:border-slate-300">
                                <div class="flex-1">
                                    <input type="text" name="biaya_lain[<?= $idx ?>][keterangan]" value="<?= esc($biaya['keterangan'] ?? '') ?>" placeholder="Keterangan (misal: Biaya Notaris, Asuransi, dll)" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-lg px-3 py-2 bg-white focus:border-blue-500 focus:outline-none h-[38px]">
                                </div>
                                <div class="w-40 sm:w-48">
                                    <input type="text" name="biaya_lain[<?= $idx ?>][nominal]" value="<?= number_format((float)($biaya['nominal'] ?? 0), 0, ',', '.') ?>" placeholder="Rp 0" class="biaya-lain-nominal w-full text-xs font-bold text-slate-800 text-right border border-slate-200 rounded-lg px-3 py-2 bg-white focus:border-blue-500 focus:outline-none rupiah-input h-[38px]" oninput="formatRupiah(this); hitungDisbursement();">
                                </div>
                                <button type="button" onclick="hapusBarisBiayaLain(this)" class="w-9 h-[38px] flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 transition cursor-pointer shrink-0" title="Hapus Biaya">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: Summary Box (Clean Corporate Blue) -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
            <div>
                <div class="flex items-center gap-2.5 pb-3 mb-5 border-b border-blue-100">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Struktur Rekapitulasi Pencairan</h3>
                        <span class="text-[11px] text-slate-400 font-medium">Kalkulasi dana riil siap cair</span>
                    </div>
                </div>

                <div class="space-y-3 p-4 bg-white rounded-xl border border-blue-100 shadow-2xs text-xs">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-slate-600">Plafon Pinjaman Pokok:</span>
                        <span id="disbPlafon" class="font-bold text-slate-800 text-sm">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                        <span class="font-semibold text-slate-600">Total Biaya & Potongan Muka:</span>
                        <span id="disbTotalPotongan" class="font-bold text-rose-600 text-sm">- Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Highlight Dana Bersih -->
            <div class="mt-6 p-5 bg-blue-600 text-white rounded-2xl shadow-xs">
                <div class="flex items-center gap-2.5 mb-2">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Estimasi Dana Bersih yang Dicairkan (Nett)</div>
                        <div class="text-[11px] text-blue-100 font-medium">Dana riil masuk ke rekening debitur</div>
                    </div>
                </div>
                <div class="pt-3 border-t border-blue-500/50 flex justify-between items-baseline">
                    <span class="text-xs text-blue-100 font-semibold">Total Bersih:</span>
                    <span id="disbDanaBersih" class="text-2xl sm:text-3xl font-black text-white tracking-tight whitespace-nowrap">Rp 0</span>
                </div>
            </div>
        </div>
    </div>
</div>
