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

    // Persiapan data rincian beban dinamis (kebutuhan hidup, operasional, asuransi, dll)
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
            $listDetailBeban = [['nama' => 'Pengeluaran Rutin', 'nominal' => $nomBebanLama]];
        } else {
            // Sedia 1 baris kosong siap diisi langsung
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

    // Persiapan Nilai Aset Awal (Ambil dari database jika ada, atau rekomendasi awal)
    $hasDbHarta = isset($keuangan['harta_kas_usaha']) || isset($keuangan['harta_tabungan']);
    $omsetBulananRef = (float)($keuangan['pendapatan_rk'] ?? 150000000);
    $hppNominalRef   = (float)($keuangan['hpp_nominal_rk'] ?? ($omsetBulananRef * 0.6));
    
    $initKasUsaha       = $hasDbHarta ? (float)($keuangan['harta_kas_usaha'] ?? 0) : round($omsetBulananRef * 0.15);
    $initPiutangUsaha   = $hasDbHarta ? (float)($keuangan['harta_piutang_usaha'] ?? 0) : round($omsetBulananRef * 0.10);
    $initPersediaanStok = $hasDbHarta ? (float)($keuangan['harta_stok_usaha'] ?? 0) : round($hppNominalRef * 0.35);
    $initPeralatanUsaha = $hasDbHarta ? (float)($keuangan['harta_alat_usaha'] ?? 0) : round($omsetBulananRef * 0.12);
    $initSubtotalAsetUsaha = $initKasUsaha + $initPiutangUsaha + $initPersediaanStok + $initPeralatanUsaha;

    $initTabunganPribadi = $hasDbHarta ? (float)($keuangan['harta_tabungan'] ?? 0) : round($omsetBulananRef * 0.10);
    if (!$hasDbHarta && $initTabunganPribadi < 15000000) $initTabunganPribadi = 15000000;
    $initDepositoPribadi  = $hasDbHarta ? (float)($keuangan['harta_deposito'] ?? 0) : 50000000;
    $initStatusDeposito   = $keuangan['status_deposito'] ?? 'bebas';

    $initTanahPribadi     = $hasDbHarta ? (float)($keuangan['harta_tanah'] ?? 0) : 250000000;
    $initStatusTanah      = $keuangan['status_tanah'] ?? 'bebas';

    $initBangunanPribadi  = $hasDbHarta ? (float)($keuangan['harta_bangunan'] ?? 0) : 180000000;
    $initStatusBangunan   = $keuangan['status_bangunan'] ?? 'bebas';

    $initKendaraanPribadi = $hasDbHarta ? (float)($keuangan['harta_kendaraan'] ?? 0) : 65000000;
    $initStatusKendaraan  = $keuangan['status_kendaraan'] ?? 'bebas';

    if (!$hasDbHarta && !empty($agunan) && is_array($agunan)) {
        $totAg = 0;
        foreach ($agunan as $ag) { 
            $totAg += (float)($ag['nilai_pasar_mv'] ?? 0); 
        }
        if ($totAg > 0) {
            $initTanahPribadi = round($totAg * 0.6);
            $initBangunanPribadi = round($totAg * 0.4);
        }
    }

    $initSubtotalAsetPribadiBruto = $initTabunganPribadi + $initDepositoPribadi + $initTanahPribadi + $initBangunanPribadi + $initKendaraanPribadi;
    $initAsetTerikat = 0;
    if ($initStatusDeposito === 'terikat')  $initAsetTerikat += $initDepositoPribadi;
    if ($initStatusTanah !== 'bebas')       $initAsetTerikat += $initTanahPribadi;
    if ($initStatusBangunan !== 'bebas')    $initAsetTerikat += $initBangunanPribadi;
    if ($initStatusKendaraan !== 'bebas')   $initAsetTerikat += $initKendaraanPribadi;

    $initTotalHartaBruto    = $initSubtotalAsetUsaha + $initSubtotalAsetPribadiBruto;
    $initTotalHartaEligible = max(0, $initTotalHartaBruto - $initAsetTerikat);
?>
<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                9
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DATA KEUANGAN</h3>
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
                    <div class="p-3 bg-white border border-blue-200 rounded-xl flex justify-between items-center text-xs shadow-2xs mb-3">
                        <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pendapatan Pokok:</span>
                        <span id="nomTotalPendapatanDinamis" class="text-sm font-black text-blue-700">Rp 0</span>
                    </div>

                    <!-- Input HPP / Biaya Pokok (Opsional untuk Usaha / Dagang) -->
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <div class="col-span-1">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP (%)</label>
                            <input type="number" step="0.1" name="hpp_persen_rk" id="hpp_persen_rk" 
                                   value="<?= esc($keuangan['hpp_persen_rk'] ?? 0) ?>" 
                                   placeholder="0"
                                   class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                   oninput="hitungLabaRugi();">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP / Biaya Pokok</label>
                            <input type="text" name="hpp_nominal_rk" id="nomHppRKInput" 
                                   value="<?= number_format((float)($keuangan['hpp_nominal_rk'] ?? 0), 0, ',', '.') ?>" 
                                   placeholder="Rp 0"
                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                                   oninput="formatRupiah(this); syncHppNominalKePersen();">
                            <span id="nomHppRK" class="hidden">Rp 0</span>
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs mb-3">
                        <span class="font-bold text-blue-900">Laba Kotor (Setelah HPP):</span>
                        <span id="grossRK" class="text-sm font-black text-blue-700">Rp 0</span>
                    </div>

                    <!-- Hidden inputs untuk database -->
                    <input type="hidden" name="pendapatan_rk" id="pendapatan_rk" value="<?= esc($keuangan['pendapatan_rk'] ?? 0) ?>">
                    <input type="hidden" name="detail_pendapatan_rk" id="detail_pendapatan_rk" value="<?= esc(!empty($listDetailPendapatan) ? json_encode($listDetailPendapatan) : '[]') ?>">
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
             KOLOM KANAN: PENGELUARAN & BIAYA/BEBAN (DINAMIS LANGSUNG TAMBAH BEBAN)
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
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pengeluaran &amp; Biaya Hidup (Dinamis)</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Kebutuhan rumah tangga, operasional hidup, dan beban lainnya</span>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBebanLain();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-300 rounded-lg shadow-2xs transition">
                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Beban</span>
                    </button>
                </div>

                <!-- Container List Baris Beban Dinamis Langsung -->
                <div>
                    <!-- Container List Baris Beban Dinamis -->
                    <div id="containerBebanLain" class="space-y-2 mb-3">
                        <!-- Diisi oleh renderListBebanLain() -->
                    </div>

                    <!-- Bar Total Beban Lainnya -->
                    <div class="p-3 bg-white border border-rose-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Total Biaya &amp; Beban:</span>
                        <span id="nomBebanLainRK" class="font-bold text-rose-700 text-sm">Rp 0</span>
                    </div>

                    <!-- Hidden fields untuk kompatibilitas database & form submission -->
                    <input type="hidden" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" value="0">
                    <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="0">
                    <span id="nomBiayaHidupRK" class="hidden">Rp 0</span>
                    <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
                    <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
                    <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>">
                </div>

            </div>

            <!-- Banner Total Pengeluaran Rutin Bulanan -->
            <div class="mt-6 p-4 bg-rose-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-rose-200">Total Pengeluaran Bulanan</div>
                    <div class="text-xs font-extrabold text-white">Akumulasi Seluruh Biaya &amp; Beban</div>
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

    <!-- ============================================================
         INPUTAN DATA HARTA & ASET (USAHA & PRIBADI)
         Dapat diisi di Data Keuangan, otomatis mengalir ke Laporan Keuangan (Tab 10)
         ============================================================ -->
    <div class="mt-8 pt-6 border-t border-slate-200">
        <!-- Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-800 tracking-wider uppercase">INPUTAN DATA HARTA &amp; ASET PEMOHON</h3>
                    <p class="text-[11px] text-slate-500">Rincian daftar harta usaha &amp; aset pribadi. Nilai ini otomatis tersinkron dan membentuk Neraca Kekayaan Bersih di Laporan Keuangan (Langkah 10)</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 shadow-2xs self-start sm:self-auto">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Otomatis Masuk ke Laporan</span>
            </span>
        </div>

        <!-- 2 Kolom: Kiri Aset Usaha, Kanan Aset Pribadi -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            
            <!-- KOLOM KIRI: ASET USAHA -->
            <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-200/80 shadow-2xs space-y-4 flex flex-col justify-between hover:border-blue-300 transition">
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-blue-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-[10px]">U</div>
                            <span class="font-bold text-xs text-blue-900 uppercase tracking-wider">1. Aset Usaha (Wiraswasta / Pemilik Usaha)</span>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400">Kosongkan jika bukan wiraswasta</span>
                    </div>

                    <!-- Kas Usaha -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Kas Usaha (Rp)</label>
                        <input type="text" id="keu_harta_kas_usaha" name="keu_harta_kas_usaha" value="<?= number_format($initKasUsaha, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <span class="text-[10px] text-slate-400">Uang tunai operasional kasir &amp; saldo rekening usaha</span>
                    </div>

                    <!-- Piutang Usaha -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Piutang Usaha (Rp)</label>
                        <input type="text" id="keu_harta_piutang_usaha" name="keu_harta_piutang_usaha" value="<?= number_format($initPiutangUsaha, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <span class="text-[10px] text-slate-400">Tagihan berjalan ke pelanggan / mitra yang belum dibayar</span>
                    </div>

                    <!-- Persediaan / Stok Barang -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Persediaan / Stok Barang (Rp)</label>
                        <input type="text" id="keu_harta_stok_usaha" name="keu_harta_stok_usaha" value="<?= number_format($initPersediaanStok, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <span class="text-[10px] text-slate-400">Nilai persediaan barang dagangan / stok bahan baku siap jual</span>
                    </div>

                    <!-- Peralatan Usaha -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Peralatan &amp; Mesin Usaha (Rp)</label>
                        <input type="text" id="keu_harta_alat_usaha" name="keu_harta_alat_usaha" value="<?= number_format($initPeralatanUsaha, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <span class="text-[10px] text-slate-400">Etalase, mesin produksi, rak toko, dan perkakas operasional</span>
                    </div>
                </div>

                <!-- Bar Subtotal Aset Usaha -->
                <div class="mt-4 p-3 bg-blue-100/70 border border-blue-200 rounded-xl flex justify-between items-center text-xs">
                    <span class="text-[11px] font-bold text-blue-900 uppercase tracking-wide">Subtotal Aset Usaha:</span>
                    <span class="text-sm font-black text-blue-700" id="keu_subtotal_aset_usaha">Rp <?= number_format($initSubtotalAsetUsaha, 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- KOLOM KANAN: ASET PRIBADI & DEPOSITO -->
            <div class="p-5 bg-indigo-50/40 rounded-2xl border border-indigo-200/80 shadow-2xs space-y-4 flex flex-col justify-between hover:border-indigo-300 transition">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between pb-2 border-b border-indigo-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px]">P</div>
                            <span class="font-bold text-xs text-indigo-900 uppercase tracking-wider">2. Aset Pribadi &amp; Deposito</span>
                        </div>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">Filter Jaminan Aktif</span>
                    </div>

                    <!-- Tabungan Bebas -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tabungan Pribadi Bebas (Rp)</label>
                        <input type="text" id="keu_harta_tabungan" name="keu_harta_tabungan" value="<?= number_format($initTabunganPribadi, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <span class="text-[10px] text-slate-400">Saldo rekening tabungan pribadi yang likuid</span>
                    </div>

                    <!-- Deposito Berjangka -->
                    <div class="p-3 bg-white rounded-xl border border-indigo-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-indigo-950 uppercase tracking-wider">Deposito Berjangka (Rp)</label>
                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">Komponen Deposito</span>
                        </div>
                        <input type="text" id="keu_harta_deposito" name="keu_harta_deposito" value="<?= number_format($initDepositoPribadi, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                        <div class="flex items-center justify-between text-[11px] pt-1">
                            <span class="text-slate-500">Status Deposito:</span>
                            <select id="keu_status_deposito" name="keu_status_deposito" onchange="sinkronDataKeuanganKeLaporan();" class="text-xs font-bold border border-slate-200 rounded-lg px-2 py-1 bg-slate-50 text-slate-700 focus:border-indigo-500">
                                <option value="bebas" <?= $initStatusDeposito === 'bebas' ? 'selected' : '' ?>>Bebas (Tidak Dijaminkan)</option>
                                <option value="terikat" <?= $initStatusDeposito === 'terikat' ? 'selected' : '' ?>>Sedang Dijaminkan (Dikecualikan)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tanah -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Tanah (Rp)</label>
                            <select id="keu_status_tanah" name="keu_status_tanah" onchange="sinkronDataKeuanganKeLaporan();" class="text-[10px] font-bold border border-slate-200 rounded-md px-1.5 py-0.5 bg-slate-50 text-slate-700">
                                <option value="bebas" <?= $initStatusTanah === 'bebas' ? 'selected' : '' ?>>Milik Sendiri (Bebas)</option>
                                <option value="terikat" <?= $initStatusTanah === 'terikat' ? 'selected' : '' ?>>Sedang Dijaminkan</option>
                                <option value="belum_pasti" <?= $initStatusTanah === 'belum_pasti' ? 'selected' : '' ?>>Belum Pasti (Girik/Sengketa)</option>
                            </select>
                        </div>
                        <input type="text" id="keu_harta_tanah" name="keu_harta_tanah" value="<?= number_format($initTanahPribadi, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                    </div>

                    <!-- Bangunan / Rumah -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Bangunan / Rumah (Rp)</label>
                            <select id="keu_status_bangunan" name="keu_status_bangunan" onchange="sinkronDataKeuanganKeLaporan();" class="text-[10px] font-bold border border-slate-200 rounded-md px-1.5 py-0.5 bg-slate-50 text-slate-700">
                                <option value="bebas" <?= $initStatusBangunan === 'bebas' ? 'selected' : '' ?>>Milik Sendiri (Bebas)</option>
                                <option value="terikat" <?= $initStatusBangunan === 'terikat' ? 'selected' : '' ?>>Sedang Dijaminkan</option>
                                <option value="belum_pasti" <?= $initStatusBangunan === 'belum_pasti' ? 'selected' : '' ?>>Belum Pasti Kepemilikannya</option>
                            </select>
                        </div>
                        <input type="text" id="keu_harta_bangunan" name="keu_harta_bangunan" value="<?= number_format($initBangunanPribadi, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                    </div>

                    <!-- Kendaraan -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Kendaraan (Mobil/Motor) (Rp)</label>
                            <select id="keu_status_kendaraan" name="keu_status_kendaraan" onchange="sinkronDataKeuanganKeLaporan();" class="text-[10px] font-bold border border-slate-200 rounded-md px-1.5 py-0.5 bg-slate-50 text-slate-700">
                                <option value="bebas" <?= $initStatusKendaraan === 'bebas' ? 'selected' : '' ?>>Lunas (Bebas)</option>
                                <option value="terikat" <?= $initStatusKendaraan === 'terikat' ? 'selected' : '' ?>>Sedang Dikredit / Dijaminkan</option>
                            </select>
                        </div>
                        <input type="text" id="keu_harta_kendaraan" name="keu_harta_kendaraan" value="<?= number_format($initKendaraanPribadi, 0, ',', '.') ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this); sinkronDataKeuanganKeLaporan();">
                    </div>
                </div>

                <!-- Subtotal Pribadi Bar -->
                <div class="space-y-1.5 pt-2 border-t border-indigo-100 text-[11px]">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Subtotal Aset Pribadi Bruto:</span>
                        <span class="font-bold text-slate-800" id="keu_subtotal_aset_pribadi">Rp <?= number_format($initSubtotalAsetPribadiBruto, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between items-center text-rose-600 font-semibold">
                        <span>(-) Aset Dijaminkan / Belum Pasti:</span>
                        <span id="keu_label_aset_terikat">(Rp 0)</span>
                    </div>
                    <div class="p-2.5 bg-indigo-100/70 border border-indigo-200 rounded-xl flex justify-between items-center">
                        <span class="font-bold text-indigo-950 uppercase text-[10px]">Aset Pribadi Bebas (Eligible):</span>
                        <span class="text-xs font-black text-indigo-800" id="keu_subtotal_pribadi_eligible">Rp <?= number_format($initSubtotalAsetPribadiBruto, 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Banner Ringkasan Total Harta & Tombol Aksi -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <div class="p-4 bg-slate-100 border border-slate-200 rounded-2xl flex justify-between items-center shadow-2xs">
                <div>
                    <span class="font-bold text-[10px] text-slate-500 uppercase tracking-wide block">TOTAL HARTA BRUTO</span>
                    <span class="text-xs text-slate-400">Seluruh aset yang diinput</span>
                </div>
                <span class="text-base font-black text-slate-800" id="keu_total_harta_bruto">Rp <?= number_format($initTotalHartaBruto, 0, ',', '.') ?></span>
            </div>

            <div class="p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl flex justify-between items-center shadow-xs">
                <div>
                    <span class="font-extrabold text-[11px] text-blue-200 uppercase tracking-wide block">TOTAL HARTA ELIGIBLE</span>
                    <span class="text-[11px] text-blue-100">Bebas jaminan &bull; masuk ke Laporan</span>
                </div>
                <span class="text-lg font-black text-white" id="keu_total_harta_eligible">Rp <?= number_format($initTotalHartaBruto, 0, ',', '.') ?></span>
            </div>

            <button type="button" onclick="handleStepperClick('laporan')" class="p-4 bg-white hover:bg-blue-50/60 border border-blue-200 rounded-2xl flex items-center justify-between text-left transition shadow-2xs group cursor-pointer">
                <div>
                    <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-wider block">LANGKAH SELANJUTNYA</span>
                    <span class="text-xs font-bold text-slate-800 group-hover:text-blue-700 transition">Buka Tab 10 (Laporan Keuangan) &rarr;</span>
                </div>
                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition">
                    10
                </div>
            </button>
        </div>
    </div>

    <!-- Script Sinkronisasi Data Keuangan (Tab 9) ke Laporan Keuangan (Tab 10) -->
    <script>
    function sinkronDataKeuanganKeLaporan() {
        function pNom(val) {
            if (!val) return 0;
            const clean = String(val).replace(/[^0-9]/g, '');
            return clean ? parseFloat(clean) : 0;
        }
        function fNom(num) {
            return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
        }

        const kasU = pNom(document.getElementById('keu_harta_kas_usaha')?.value);
        const piutangU = pNom(document.getElementById('keu_harta_piutang_usaha')?.value);
        const stokU = pNom(document.getElementById('keu_harta_stok_usaha')?.value);
        const alatU = pNom(document.getElementById('keu_harta_alat_usaha')?.value);
        const subAstU = kasU + piutangU + stokU + alatU;

        const tabP = pNom(document.getElementById('keu_harta_tabungan')?.value);
        const depoP = pNom(document.getElementById('keu_harta_deposito')?.value);
        const statusDepo = document.getElementById('keu_status_deposito')?.value || 'bebas';

        const tanahP = pNom(document.getElementById('keu_harta_tanah')?.value);
        const statusTanah = document.getElementById('keu_status_tanah')?.value || 'bebas';

        const bgnP = pNom(document.getElementById('keu_harta_bangunan')?.value);
        const statusBgn = document.getElementById('keu_status_bangunan')?.value || 'bebas';

        const kdrP = pNom(document.getElementById('keu_harta_kendaraan')?.value);
        const statusKdr = document.getElementById('keu_status_kendaraan')?.value || 'bebas';

        const subAstP_bruto = tabP + depoP + tanahP + bgnP + kdrP;
        const totHarta_bruto = subAstU + subAstP_bruto;

        // Hitung pengurang aset yang dijaminkan / belum pasti
        let asetTerikat = 0;
        if (statusDepo === 'terikat') asetTerikat += depoP;
        if (statusTanah !== 'bebas')  asetTerikat += tanahP;
        if (statusBgn !== 'bebas')    asetTerikat += bgnP;
        if (statusKdr !== 'bebas')    asetTerikat += kdrP;

        const subAstP_eligible = subAstP_bruto - asetTerikat;
        const totHarta_eligible = subAstU + subAstP_eligible;

        // Update UI Ringkasan Tab 9
        if (document.getElementById('keu_subtotal_aset_usaha')) document.getElementById('keu_subtotal_aset_usaha').innerText = fNom(subAstU);
        if (document.getElementById('keu_subtotal_aset_pribadi')) document.getElementById('keu_subtotal_aset_pribadi').innerText = fNom(subAstP_bruto);
        if (document.getElementById('keu_label_aset_terikat')) document.getElementById('keu_label_aset_terikat').innerText = `(${fNom(asetTerikat)})`;
        if (document.getElementById('keu_subtotal_pribadi_eligible')) document.getElementById('keu_subtotal_pribadi_eligible').innerText = fNom(subAstP_eligible);
        if (document.getElementById('keu_total_harta_bruto')) document.getElementById('keu_total_harta_bruto').innerText = fNom(totHarta_bruto);
        if (document.getElementById('keu_total_harta_eligible')) document.getElementById('keu_total_harta_eligible').innerText = fNom(totHarta_eligible);

        // Sinkronisasi Langsung ke Elemen Tab 10 (Format Usaha)
        const mapUsaha = [
            ['input_harta_kas_usaha', kasU],
            ['input_harta_piutang_usaha', piutangU],
            ['input_harta_stok_usaha', stokU],
            ['input_harta_alat_usaha', alatU],
            ['input_harta_tabungan', tabP],
            ['input_harta_deposito', depoP],
            ['input_harta_tanah', tanahP],
            ['input_harta_bangunan', bgnP],
            ['input_harta_kendaraan', kdrP],
        ];
        mapUsaha.forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el && el !== document.activeElement) {
                el.value = Math.round(val || 0).toLocaleString('id-ID');
            }
        });
        if (document.getElementById('status_deposito')) document.getElementById('status_deposito').value = statusDepo;
        if (document.getElementById('status_tanah')) document.getElementById('status_tanah').value = statusTanah;
        if (document.getElementById('status_bangunan')) document.getElementById('status_bangunan').value = statusBgn;
        if (document.getElementById('status_kendaraan')) document.getElementById('status_kendaraan').value = statusKdr;

        // Sinkronisasi Langsung ke Elemen Tab 10 (Format Gaji)
        const mapGaji = [
            ['input_harta_gaji_tabungan', tabP],
            ['input_harta_gaji_deposito', depoP],
            ['input_harta_gaji_tanah', tanahP],
            ['input_harta_gaji_bangunan', bgnP],
            ['input_harta_gaji_kendaraan', kdrP],
        ];
        mapGaji.forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el && el !== document.activeElement) {
                el.value = Math.round(val || 0).toLocaleString('id-ID');
            }
        });
        if (document.getElementById('status_gaji_deposito')) document.getElementById('status_gaji_deposito').value = statusDepo;
        if (document.getElementById('status_gaji_tanah')) document.getElementById('status_gaji_tanah').value = statusTanah;
        if (document.getElementById('status_gaji_bangunan')) document.getElementById('status_gaji_bangunan').value = statusBgn;
        if (document.getElementById('status_gaji_kendaraan')) document.getElementById('status_gaji_kendaraan').value = statusKdr;

        // Picu perhitungan ulang di Tab 10
        if (typeof sinkronHartaKelaporan === 'function') {
            sinkronHartaKelaporan();
        }
        if (typeof sinkronHartaGajiKelaporan === 'function') {
            sinkronHartaGajiKelaporan();
        }
    }
    window.sinkronDataKeuanganKeLaporan = sinkronDataKeuanganKeLaporan;

    // Jalankan sinkronisasi awal saat dokumen siap
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (typeof sinkronDataKeuanganKeLaporan === 'function') {
                sinkronDataKeuanganKeLaporan();
            }
        }, 300);
    });
    </script>
</div>
