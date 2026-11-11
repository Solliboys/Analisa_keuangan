<?php
    // Persiapan data rincian pendapatan pokok dinamis (Gaji pokok, bonus, lembur, dll)
    $rawDetailPendapatan = $keuangan['detail_pendapatan_rk'] ?? '';
    $listDetailPendapatan = [];
    if (!empty($rawDetailPendapatan)) {
        $decP = json_decode($rawDetailPendapatan, true);
        if (is_array($decP) && count($decP) > 0) {
            $listDetailPendapatan = $decP;
        }
    }
    if (empty($listDetailPendapatan)) {
        $nomPendapatanLama = (float)($keuangan['pendapatan_rk'] ?? 0);
        if ($nomPendapatanLama > 0) {
            $listDetailPendapatan = [['nama' => 'Gaji Pokok', 'nominal' => $nomPendapatanLama]];
        } else {
            $listDetailPendapatan = [['nama' => 'Gaji Pokok', 'nominal' => 0]];
        }
    }

    // Persiapan data biaya hidup pokok
    $nomBiayaHidup = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);
    if ($nomBiayaHidup <= 0 && !empty($keuangan['pendapatan_rk'])) {
        $nomBiayaHidup = ((float)$keuangan['pendapatan_rk']) * (((float)($keuangan['biaya_hidup_persen_rk'] ?? 10)) / 100);
    }
    if ($nomBiayaHidup <= 0) {
        $nomBiayaHidup = 4500000;
    }

    // Persiapan data rincian beban lainnya dinamis (asuransi, biaya tak terduga, dll)
    $rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
    $listDetailBeban = [];
    if (!empty($rawDetailBeban)) {
        $decBeban = json_decode($rawDetailBeban, true);
        if (is_array($decBeban) && count($decBeban) > 0) {
            $listDetailBeban = $decBeban;
        }
    }
    if (empty($listDetailBeban)) {
        $nomBebanLama = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
        if ($nomBebanLama > 0) {
            $listDetailBeban = [['nama' => 'Beban Lain-Lain', 'nominal' => $nomBebanLama]];
        } else {
            // Sedia 1 baris kosong siap diisi sesuai arahan
            $listDetailBeban = [['nama' => '', 'nominal' => 0]];
        }
    }

    // Persiapan data pendapatan lain-lain (khusus perorangan: usaha sampingan, dll)
    $adaPendapatanLain = !empty($keuangan['ada_bukti_pendapatan_lain_rk']) || (!empty($keuangan['pendapatan_lain_rk']) && (float)$keuangan['pendapatan_lain_rk'] > 0);
    $nominalPendapatanLain = (float)($keuangan['pendapatan_lain_rk'] ?? 0);
    $ketPendapatanLain = $keuangan['keterangan_pendapatan_lain_rk'] ?? '';
    $buktiRaw = $keuangan['bukti_pendapatan_lain_rk'] ?? '';
    $listBuktiLain = [];
    if (!empty($buktiRaw)) {
        $decoded = json_decode($buktiRaw, true);
        if (is_array($decoded)) {
            $listBuktiLain = $decoded;
        } else {
            $listBuktiLain = [['name' => basename($buktiRaw), 'path' => $buktiRaw]];
        }
    }
?>
<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                9
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA LABA RUGI & PENDAPATAN (DEBITUR PERORANGAN / KARYAWAN)</h3>
                <p class="text-[11px] text-slate-500">Pencatatan rincian pendapatan pokok, pendapatan sampingan, dan pengeluaran kebutuhan hidup pemohon</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Perorangan / Karyawan
        </span>
    </div>

    <!-- Layout 2 Kolom: Kiri Penghasilan & Pendapatan Lain-lain, Kanan Pengeluaran & Beban Bebas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        
        <!-- ============================================================
             KOLOM KIRI: PENGHASILAN (PENDAPATAN POKOK DINAMIS + PENDAPATAN LAIN-LAIN)
             ============================================================ -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div class="space-y-5">
                
                <!-- 1. PENDAPATAN POKOK / UTAMA (DINAMIS: BISA DITAMBAH SENDIRI) -->
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-blue-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pendapatan Pokok / Utama (Dinamis)</h3>
                                <span class="text-[11px] text-slate-400 font-medium">Gaji pokok, tunjangan, bonus, lembur, dll</span>
                            </div>
                        </div>
                        <button type="button" onclick="tambahPendapatan();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Baris</span>
                        </button>
                    </div>

                    <!-- Container List Baris Pendapatan Dinamis -->
                    <div id="containerPendapatanDinamis" class="space-y-2 mb-3">
                        <!-- Diisi oleh renderListPendapatan() -->
                    </div>

                    <!-- Bar Total Pendapatan Pokok -->
                    <div class="p-3 bg-white border border-blue-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pendapatan Pokok:</span>
                        <span id="nomTotalPendapatanDinamis" class="text-sm font-black text-blue-700">Rp 0</span>
                    </div>

                    <!-- Hidden inputs untuk database -->
                    <input type="hidden" name="pendapatan_rk" id="pendapatan_rk" value="<?= esc($keuangan['pendapatan_rk'] ?? 0) ?>">
                    <input type="hidden" name="detail_pendapatan_rk" id="detail_pendapatan_rk" value="<?= esc(!empty($listDetailPendapatan) ? json_encode($listDetailPendapatan) : '[]') ?>">
                    <input type="hidden" name="hpp_persen_rk" id="hpp_persen_rk" value="0">
                    <span id="nomHppRK" class="hidden">Rp 0</span>
                    <span id="grossRK" class="hidden">Rp 0</span>
                </div>

                <!-- 2. PENDAPATAN LAIN-LAIN (KHUSUS KARYAWAN: MISAL USAHA SAMPINGAN) -->
                <div class="pt-4 border-t border-blue-200/70">
                    <div class="flex items-center justify-between pb-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-600 text-white flex items-center justify-center font-extrabold text-[11px]">
                                +
                            </span>
                            <div>
                                <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pendapatan Lain-Lain (Usaha Sampingan, dll)</h4>
                                <span class="text-[10px] text-slate-400 font-medium">Khusus perorangan yang punya usaha sampingan / sewa aset / pasangan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Box Centang Toggle Pendapatan Lain-Lain -->
                    <div class="p-3.5 rounded-xl border transition mb-3.5 <?= $adaPendapatanLain ? 'bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-200' : 'bg-white border-slate-200 hover:border-slate-300' ?>" id="boxTogglePendapatanLain">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="ada_bukti_pendapatan_lain_rk" 
                                   id="ada_bukti_pendapatan_lain_rk" 
                                   value="1" 
                                   <?= $adaPendapatanLain ? 'checked' : '' ?>
                                   onchange="togglePendapatanLain(this.checked)"
                                   class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mt-0.5 cursor-pointer">
                            <div class="flex-1">
                                <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <span>Apakah memiliki pendapatan lain-lain?</span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[9px] font-extrabold rounded uppercase">Centang jika Ada</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Misalnya: Karyawan yang juga memiliki usaha sampingan (toko kelontong, laundry, rental kos), atau penghasilan pasangan.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Input Nominal & Dokumen Bukti (Tampil jika Dicentang) -->
                    <div id="sectionPendapatanLain" class="space-y-3 <?= $adaPendapatanLain ? '' : 'hidden' ?>">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                Nominal Pendapatan Lain-Lain / Sampingan (Rp/Bulan)
                            </label>
                            <input type="text" 
                                   name="pendapatan_lain_rk" 
                                   id="pendapatan_lain_rk" 
                                   value="<?= $nominalPendapatanLain > 0 ? number_format($nominalPendapatanLain, 0, ',', '.') : '0' ?>" 
                                   class="w-full text-xs font-bold text-emerald-700 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition shadow-2xs rupiah-input h-[38px]"
                                   placeholder="Rp 0"
                                   oninput="formatRupiah(this); hitungLabaRugi();">
                        </div>

                        <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">

                        <!-- Upload Bukti Pendapatan Lain-Lain (Opsional) -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[10px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Bukti Pendapatan Sampingan</span>
                                </label>
                                <span class="text-[9px] font-semibold text-slate-400">Opsional</span>
                            </div>

                            <input type="hidden" name="bukti_pendapatan_lain_rk" id="bukti_pendapatan_lain_rk" value="<?= esc(!empty($listBuktiLain) ? json_encode($listBuktiLain) : '') ?>">
                            <input type="file" id="file_bukti_pendapatan_lain" name="file_bukti_pendapatan_lain[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="handlePilihBuktiLain(this)">

                            <div onclick="document.getElementById('file_bukti_pendapatan_lain').click()" 
                                 class="border border-dashed border-emerald-300 hover:border-emerald-500 bg-white hover:bg-emerald-50/40 rounded-xl p-2.5 text-center cursor-pointer transition flex items-center justify-center gap-2 shadow-2xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-xs font-bold text-emerald-800">Unggah Bukti (Buku tabungan usaha / slip tambahan)</span>
                            </div>

                            <div id="containerListBuktiLain" class="space-y-1.5 mt-2">
                                <?php if (!empty($listBuktiLain)): ?>
                                    <?php foreach ($listBuktiLain as $idx => $item): ?>
                                        <?php 
                                            $fName = $item['name'] ?? basename($item['path'] ?? 'Bukti');
                                            $fPath = $item['path'] ?? '';
                                        ?>
                                        <div class="p-2 bg-white border border-slate-200 rounded-xl flex items-center justify-between text-xs" id="buktiRow_<?= $idx ?>">
                                            <span class="truncate font-semibold text-slate-700"><?= esc($fName) ?></span>
                                            <button type="button" onclick="hapusBuktiPendapatanLain(<?= $idx ?>)" class="text-rose-500 hover:text-rose-700 text-xs font-bold ml-2">Hapus</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pendapatan Sampingan -->
                    <div class="mt-3 p-3 bg-emerald-50/80 border border-emerald-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-emerald-900 uppercase tracking-wider text-[11px]">Tambahan Pendapatan Lain:</span>
                        <span id="labelTotalPendapatanLain" class="text-sm font-black text-emerald-700">Rp 0</span>
                    </div>
                </div>

            </div>

            <!-- Banner Total Penghasilan Bersih -->
            <div class="mt-6 p-4 bg-gradient-to-r from-blue-700 to-indigo-700 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Total Penghasilan Pemohon</div>
                    <div class="text-xs font-extrabold text-white">Pendapatan Pokok + Pendapatan Lain-Lain</div>
                </div>
                <div class="text-right">
                    <span id="nomTotalPenghasilanPR" class="text-xl sm:text-2xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- ============================================================
             KOLOM KANAN: PENGELUARAN & BIAYA/BEBAN BEBAS
             ============================================================ -->
        <div class="p-6 bg-slate-50/70 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div class="space-y-5">
                
                <!-- Header Card Pengeluaran -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pengeluaran &amp; Biaya Hidup</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Kebutuhan rumah tangga dan biaya/beban bebas lainnya</span>
                        </div>
                    </div>
                </div>

                <!-- 1. Pengeluaran Kebutuhan Pokok Bulanan -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pengeluaran Kebutuhan Pokok / Rumah Tangga (Rp/Bulan)
                    </label>
                    <p class="text-[10px] text-slate-400 mb-1.5">Kebutuhan makan, belanja bulanan, listrik, air, pulsa &amp; transportasi</p>
                    <input type="text" 
                           name="biaya_hidup_nominal_rk" 
                           id="biaya_hidup_nominal_rk" 
                           value="<?= number_format($nomBiayaHidup, 0, ',', '.') ?>" 
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition shadow-2xs rupiah-input h-[38px]"
                           placeholder="Rp 0"
                           oninput="formatRupiah(this); hitungLabaRugi();">
                    <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>">
                    <span id="nomBiayaHidupRK" class="hidden">Rp 0</span>
                </div>

                <!-- 2. Biaya/Beban Bebas Lainnya (Dinamis: Bisa ditambah sendiri) -->
                <div class="pt-3 border-t border-slate-200/80">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Biaya &amp; Beban Lainnya (Bebas)</label>
                            <p class="text-[10px] text-slate-400">Tambahkan baris bebas (contoh: Premi Asuransi, Biaya Sekolah Anak, Biaya Tak Terduga)</p>
                        </div>
                        <button type="button" onclick="tambahBebanLain();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-300 rounded-lg shadow-2xs transition">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Beban</span>
                        </button>
                    </div>

                    <!-- Container List Baris Beban Dinamis -->
                    <div id="containerBebanLain" class="space-y-2 mb-2.5">
                        <!-- Diisi oleh renderListBebanLain() -->
                    </div>

                    <!-- Bar Total Beban Lainnya -->
                    <div class="p-2.5 bg-white border border-slate-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Total Beban Lain:</span>
                        <span id="nomBebanLainRK" class="font-bold text-rose-700 text-xs sm:text-sm">Rp 0</span>
                    </div>

                    <!-- Hidden fields untuk database -->
                    <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
                    <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
                    <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>">
                </div>

            </div>

            <!-- Banner Total Pengeluaran Rutin Bulanan -->
            <div class="mt-6 p-4 bg-rose-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-rose-200">Total Pengeluaran Bulanan</div>
                    <div class="text-xs font-extrabold text-white">Kebutuhan Pokok + Biaya Beban Lainnya</div>
                </div>
                <div class="text-right">
                    <span id="nomTotalPengeluaranPR" class="text-xl sm:text-2xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================
         RINGKASAN TOTAL LABA BERSIH / SURPLUS (OTOMATIS)
         ============================================================ -->
    <div class="mt-6 p-4 sm:p-5 bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-white uppercase tracking-wider">SISA SURPLUS BULANAN (KAPASITAS KEMAMPUAN ANGSURAN)</h4>
                <p class="text-[11px] text-blue-200 mt-0.5">Total Penghasilan dikurangi Total Pengeluaran bulanan (dihitung langsung otomatis tanpa rumus rumit)</p>
            </div>
        </div>
        <div class="text-right flex items-center gap-3">
            <span class="text-xs text-blue-200 font-medium hidden sm:inline">Surplus Bersih:</span>
            <span id="nomSurplusPR" class="text-xl sm:text-2xl font-black text-emerald-400 tracking-tight whitespace-nowrap block">Rp 0</span>
            <span id="nettRK" class="hidden">Rp 0</span>
            <span id="totalCashflowBersih" class="hidden">Rp 0</span>
        </div>
    </div>
</div>
