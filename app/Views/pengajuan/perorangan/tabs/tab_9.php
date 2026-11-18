<?php
    // =========================================================================
    // 1. DATA PENDAPATAN POKOK / UTAMA (Gaji pokok, tunjangan, dll)
    // =========================================================================
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

    // =========================================================================
    // 2. DATA RINCIAN BEBAN (Kebutuhan hidup, operasional, dll)
    // =========================================================================
    $rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
    $listDetailBeban = [];
    if (!empty($rawDetailBeban)) {
        $decBeban = json_decode($rawDetailBeban, true);
        if (is_array($decBeban) && count($decBeban) > 0) {
            $listDetailBeban = $decBeban;
        }
    }
    if (empty($listDetailBeban)) {
        $nomBebanLama = (float)($keuangan['beban_lainnya_nominal_rk'] ?? ($keuangan['biaya_hidup_nominal_rk'] ?? 0));
        if ($nomBebanLama > 0) {
            $listDetailBeban = [['nama' => 'Pengeluaran Rutin & Kebutuhan Rumah Tangga', 'nominal' => $nomBebanLama]];
        } else {
            $listDetailBeban = [['nama' => '', 'nominal' => 0]];
        }
    }

    // =========================================================================
    // 3. PENDAPATAN LAIN-LAIN / SAMPINGAN (Usaha sampingan / sewa / pasangan)
    // =========================================================================
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

    // =========================================================================
    // 4. DATA SLIK OJK (Baki Debet, Tenor, Suku Bunga & Angsuran)
    // =========================================================================
    $totalOutstandingSlik = 0;
    $totalCicilanSlik = 0;
    $rekapSlikFasilitas = [];
    $worstKolektibilitas = 'Kol 1 (Lancar)';

    if (!empty($slik) && is_array($slik)) {
        foreach ($slik as $s) {
            $out = (float)($s['outstanding'] ?? ($s['baki_debet'] ?? 0));
            $angs = (float)($s['angsuran'] ?? ($s['angsuran_bulanan'] ?? 0));
            $plafon = (float)($s['plafon'] ?? 0);
            $kol = $s['kelancaran'] ?? ($s['kolektibilitas'] ?? 'Kol 1');

            if ($angs <= 0 && $out > 0) {
                $angs = round(($out / 36) + ($out * 0.0125));
            }

            $totalOutstandingSlik += $out;
            $totalCicilanSlik += $angs;

            if (!empty($kol) && strpos(strtolower($kol), 'kol 1') === false && strpos(strtolower($kol), 'lancar') === false) {
                $worstKolektibilitas = $kol;
            }

            $rekapSlikFasilitas[] = [
                'bank'        => !empty($s['bank_lembaga']) ? $s['bank_lembaga'] : 'Lembaga Keuangan',
                'jenis'       => !empty($s['jenis_fasilitas']) ? $s['jenis_fasilitas'] : 'Kredit/Pembiayaan',
                'plafon'      => $plafon,
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

    // =========================================================================
    // 5. ESTIMASI ANGSURAN FASILITAS KREDIT BARU YANG DIAJUKAN
    // =========================================================================
    $plafonBaru = (float)($kredit['plafon_pengajuan'] ?? 50000000);
    $tenorBulanBaru = (int)($kredit['tenor_bulan'] ?? 12);
    if ($tenorBulanBaru <= 0) $tenorBulanBaru = 12;
    $bungaBulanPersen = (float)($kredit['bunga_per_bulan'] ?? 1.25);
    $angsuranPokokBaru = ($plafonBaru > 0) ? round($plafonBaru / $tenorBulanBaru) : 0;
    $angsuranBungaBaru = ($plafonBaru > 0) ? round($plafonBaru * ($bungaBulanPersen / 100)) : 0;
    $estimasiAngsuranBaru = $angsuranPokokBaru + $angsuranBungaBaru;
    $totalAngsuranGabungan = $totalCicilanSlik + $estimasiAngsuranBaru;

    // =========================================================================
    // 6. INITIALISASI DAFTAR HARTA / ASET
    // =========================================================================
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
        $tabunganDb = (float)($keuangan['harta_tabungan'] ?? 25000000);
        $depositoDb = (float)($keuangan['harta_deposito'] ?? 50000000);
        $statusDepoDb = $keuangan['status_deposito'] ?? 'bebas';
        $tanahDb = (float)($keuangan['harta_tanah'] ?? 250000000);
        $statusTanahDb = $keuangan['status_tanah'] ?? 'bebas';
        $bangunanDb = (float)($keuangan['harta_bangunan'] ?? 180000000);
        $statusBangunanDb = $keuangan['status_bangunan'] ?? 'bebas';
        $kendaraanDb = (float)($keuangan['harta_kendaraan'] ?? 65000000);
        $statusKendaraanDb = $keuangan['status_kendaraan'] ?? 'bebas';

        $listDetailHarta = [
            ['nama' => 'Tabungan Bank', 'nominal' => $tabunganDb, 'status' => 'bebas'],
            ['nama' => 'Deposito Berjangka', 'nominal' => $depositoDb, 'status' => $statusDepoDb],
            ['nama' => 'Bangunan (Rumah Tinggal)', 'nominal' => $bangunanDb, 'status' => $statusBangunanDb],
            ['nama' => 'Tanah', 'nominal' => $tanahDb, 'status' => $statusTanahDb],
            ['nama' => 'Kendaraan Pribadi', 'nominal' => $kendaraanDb, 'status' => $statusKendaraanDb],
        ];
        if (!empty($keuangan['harta_kas_usaha']) && (float)$keuangan['harta_kas_usaha'] > 0) {
            $listDetailHarta[] = ['nama' => 'Kas Usaha / Kas Tunai', 'nominal' => (float)$keuangan['harta_kas_usaha'], 'status' => 'bebas'];
        }
    }

    // =========================================================================
    // 7. INITIALISASI DAFTAR UTANG DAN KEWAJIBAN
    // =========================================================================
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
?>

<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-8">
    
    <!-- ============================================================
         HEADER TAB: DATA KEUANGAN
         ============================================================ -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                9
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-800 tracking-wider uppercase">DATA KEUANGAN</h3>
                <p class="text-[11px] text-slate-500">Pemasukan, pengeluaran bulanan, inventarisasi harta dan kewajiban utang, perhitungan angsuran SLIK OJK, dan rasio keuangan (DSCR)</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs">
                Perorangan / Karyawan / Wiraswasta
            </span>
        </div>
    </div>

    <!-- ============================================================
         BAGIAN 1: PEMASUKAN (BIRU) & PENGELUARAN/BEBAN (MERAH)
         ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        
        <!-- ============================================================
             KOLOM KIRI: PEMASUKAN / PENDAPATAN BULANAN (WARNA BIRU)
             ============================================================ -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/90 flex flex-col justify-between shadow-2xs hover:border-blue-400 transition">
            <div class="space-y-5">
                
                <!-- Header Kolom Pemasukan (Biru) -->
                <div class="flex items-center justify-between pb-3 border-b border-blue-200/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-blue-950 uppercase tracking-wider">Pemasukan / Pendapatan Bulanan</h3>
                            <span class="text-[11px] text-blue-700/70 font-medium">Gaji pokok, tunjangan, insentif, omzet, dan pendapatan rutin</span>
                        </div>
                    </div>
                    <button type="button" onclick="tambahPendapatan();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <!-- Container List Baris Pendapatan -->
                <div id="containerPendapatanDinamis" class="space-y-2 mb-3">
                    <!-- Diisi otomatis oleh renderListPendapatan() -->
                </div>

                <!-- Bar Total Pendapatan Pokok -->
                <div class="p-3 bg-white border border-blue-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pendapatan Pokok:</span>
                    <span id="nomTotalPendapatanDinamis" class="text-sm font-black text-blue-700">Rp 0</span>
                </div>

                <!-- Hidden inputs untuk database compatibility -->
                <input type="hidden" name="pendapatan_rk" id="pendapatan_rk" value="<?= esc($keuangan['pendapatan_rk'] ?? 0) ?>">
                <input type="hidden" name="detail_pendapatan_rk" id="detail_pendapatan_rk" value="<?= esc(!empty($listDetailPendapatan) ? json_encode($listDetailPendapatan) : '[]') ?>">
                <input type="hidden" name="hpp_persen_rk" id="hpp_persen_rk" value="0">
                <input type="hidden" name="hpp_nominal_rk" id="hpp_nominal_rk" value="0">

                <!-- Pendapatan Sampingan / Lain-Lain -->
                <div class="pt-4 border-t border-blue-200/70 space-y-3">
                    <div class="p-3.5 rounded-xl border transition <?= $adaPendapatanLain ? 'bg-blue-100/60 border-blue-300 ring-1 ring-blue-200' : 'bg-white border-slate-200 hover:border-slate-300' ?>" id="boxTogglePendapatanLain">
                        <label class="flex items-start gap-3 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="ada_bukti_pendapatan_lain_rk" 
                                   id="ada_bukti_pendapatan_lain_rk" 
                                   value="1" 
                                   <?= $adaPendapatanLain ? 'checked' : '' ?>
                                   onchange="togglePendapatanLain(this.checked)"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 cursor-pointer">
                            <div class="flex-1">
                                <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <span>Memiliki Pendapatan Lain-Lain / Usaha Sampingan?</span>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[9px] font-extrabold rounded uppercase">Centang jika Ada</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                    Contoh: Usaha sampingan, sewa kos/kontrakan, royalti, atau penghasilan pasangan suami/istri.
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- Input Nominal Pendapatan Lain (Muncul jika dicentang) -->
                    <div id="sectionPendapatanLain" class="space-y-3 <?= $adaPendapatanLain ? '' : 'hidden' ?>">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nominal Pendapatan Sampingan / Lain-Lain (Rp/Bulan)
                            </label>
                            <input type="text" 
                                   name="pendapatan_lain_rk" 
                                   id="pendapatan_lain_rk" 
                                   value="<?= $nominalPendapatanLain > 0 ? number_format($nominalPendapatanLain, 0, ',', '.') : '0' ?>" 
                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                                   placeholder="Rp 0"
                                   oninput="formatRupiah(this); hitungSemuaDataKeuangan();">
                        </div>

                        <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">

                        <!-- Upload Bukti -->
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
                                 class="border border-dashed border-blue-300 hover:border-blue-500 bg-white hover:bg-blue-50/40 rounded-xl p-2.5 text-center cursor-pointer transition flex items-center justify-center gap-2 shadow-2xs">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-xs font-bold text-blue-800">Unggah Bukti (Buku tabungan / mutasi / slip)</span>
                            </div>

                            <div id="containerListBuktiLain" class="space-y-1.5 mt-2">
                                <?php if (!empty($listBuktiLain)): ?>
                                    <?php foreach ($listBuktiLain as $idx => $item): ?>
                                        <?php 
                                            $fName = $item['name'] ?? basename($item['path'] ?? 'Bukti');
                                        ?>
                                        <div class="p-2 bg-white border border-slate-200 rounded-xl flex items-center justify-between text-xs" id="buktiRow_<?= $idx ?>">
                                            <span class="truncate font-semibold text-slate-700"><?= esc($fName) ?></span>
                                            <button type="button" onclick="hapusBuktiPendapatanLain(<?= $idx ?>)" class="text-rose-500 hover:text-rose-700 text-xs font-bold ml-2">Hapus</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bar Total Pendapatan Sampingan -->
                        <div class="p-3 bg-blue-100/60 border border-blue-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                            <span class="font-bold text-blue-900 uppercase tracking-wider text-[11px]">Tambahan Pendapatan Sampingan:</span>
                            <span id="labelTotalPendapatanLain" class="text-sm font-black text-blue-700">Rp 0</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Banner Total Pemasukan Bulanan (Biru) -->
            <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-blue-200">TOTAL PEMASUKAN BULANAN (BIRU)</div>
                    <div class="text-xs font-semibold text-white">Akumulasi Pendapatan Pokok + Sampingan</div>
                </div>
                <div class="text-right">
                    <span id="nomTotalPenghasilanPR" class="text-xl sm:text-2xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- ============================================================
             KOLOM KANAN: PENGELUARAN & BEBAN BULANAN (WARNA MERAH)
             ============================================================ -->
        <div class="p-6 bg-rose-50/40 rounded-2xl border border-rose-200/90 flex flex-col justify-between shadow-2xs hover:border-rose-400 transition">
            <div class="space-y-5">
                
                <!-- Header Kolom Pengeluaran (Merah) -->
                <div class="flex items-center justify-between pb-3 border-b border-rose-200/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-rose-950 uppercase tracking-wider">Pengeluaran &amp; Biaya/Beban Hidup</h3>
                            <span class="text-[11px] text-rose-700/70 font-medium">Kebutuhan rumah tangga, operasional, sekolah, asuransi, dll</span>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBebanLain();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-white bg-rose-600 hover:bg-rose-700 border border-rose-600 rounded-lg shadow-2xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Beban</span>
                    </button>
                </div>

                <!-- Container List Baris Beban -->
                <div>
                    <div id="containerBebanLain" class="space-y-2 mb-3">
                        <!-- Diisi otomatis oleh renderListBebanLain() -->
                    </div>

                    <!-- Bar Total Beban Lainnya -->
                    <div class="p-3 bg-white border border-rose-200 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Total Seluruh Beban:</span>
                        <span id="nomBebanLainRK" class="font-bold text-rose-700 text-sm">Rp 0</span>
                    </div>

                    <!-- Hidden inputs untuk sinkronisasi database & form submission -->
                    <input type="hidden" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" value="0">
                    <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="0">
                    <span id="nomBiayaHidupRK" class="hidden">Rp 0</span>
                    <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
                    <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
                    <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>">
                </div>

                <div class="p-3 bg-rose-100/50 rounded-xl border border-rose-200 text-[11px] text-rose-800 leading-relaxed">
                    <span class="font-bold">Tips Pengisian Beban:</span> Rinci pengeluaran rutin pemohon secara bebas dengan menekan tombol <strong>Tambah Beban</strong> (cth: Belanja dapur, utilitas listrik/air, biaya pendidikan, transportasi, premi asuransi, dll).
                </div>

            </div>

            <!-- Banner Total Pengeluaran Bulanan (Merah) -->
            <div class="mt-6 p-4 bg-rose-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-200">TOTAL PENGELUARAN BULANAN (MERAH)</div>
                    <div class="text-xs font-semibold text-white">Akumulasi Seluruh Beban &amp; Biaya Hidup</div>
                </div>
                <div class="text-right">
                    <span id="nomTotalPengeluaranPR" class="text-xl sm:text-2xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================================
         RINGKASAN TOTAL LABA / SISA SURPLUS BULANAN (WARNA BIRU)
         Sisa Penghasilan Bersih = Total Pemasukan - Total Beban
         ============================================================ -->
    <div class="p-5 bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">SISA PENGHASILAN / LABA SURPLUS BULANAN (WARNA BIRU)</h4>
                <p class="text-[11px] text-blue-100 mt-0.5">Total Pemasukan (Biru) dikurangi Total Beban (Merah) &bull; Dasar kapasitas kemampuan angsuran</p>
            </div>
        </div>
        <div class="text-right flex items-center gap-3">
            <span class="text-xs text-blue-200 font-medium hidden sm:inline">Surplus Bersih:</span>
            <span id="nomSurplusPR" class="text-xl sm:text-2xl font-black text-emerald-300 tracking-tight whitespace-nowrap block">Rp 0</span>
            <span id="nettRK" class="hidden">Rp 0</span>
            <span id="grossRK" class="hidden">Rp 0</span>
            <span id="totalCashflowBersih" class="hidden">Rp 0</span>
        </div>
    </div>

    <!-- ============================================================
         BAGIAN 2: HARTA, UTANG DAN KEWAJIBAN
         ASET & UTANG BISA DITAMBAH BEBAS (MODAL / KEKAYAAN BERSIH DIHAPUS)
         ============================================================ -->
    <div class="pt-6 border-t border-slate-200 space-y-6">
        
        <!-- Section Header: HARTA, UTANG DAN KEWAJIBAN -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-800 tracking-wider uppercase">HARTA, UTANG DAN KEWAJIBAN</h3>
                    <p class="text-[11px] text-slate-500">Rincian seluruh harta/aset yang dimiliki serta akumulasi seluruh kewajiban utang &amp; angsuran bulanan pemohon</p>
                </div>
            </div>
        </div>

        <!-- 2 Kolom: Kiri Harta/Aset (Hijau), Kanan Utang & Kewajiban (Merah) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            <!-- ============================================================
                 KOLOM KIRI: DAFTAR HARTA / ASET PEMOHON (WARNA HIJAU)
                 BISA DITAMBAH BARIS BEBAS DENGAN TOMBOL "+ TAMBAH HARTA"
                 ============================================================ -->
            <div class="p-6 bg-emerald-50/30 rounded-2xl border border-emerald-300 shadow-2xs space-y-5 hover:border-emerald-500 transition">
                <div class="flex items-center justify-between pb-3 border-b border-emerald-200">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">A</div>
                        <span class="font-extrabold text-xs text-emerald-950 uppercase tracking-wider">HARTA / ASET PEMOHON (WARNA HIJAU)</span>
                    </div>
                    <button type="button" onclick="tambahBarisHarta();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-600 rounded-lg shadow-2xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Harta</span>
                    </button>
                </div>

                <p class="text-[11px] text-emerald-800 leading-relaxed">
                    Tambahkan seluruh aset yang dimiliki (tabungan, deposito, tanah, rumah tinggal, kendaraan, emas, dll). Aset yang dipilih <strong>Sedang Dijaminkan</strong> otomatis dikecualikan dari Harta Bersih Eligible.
                </p>

                <!-- Container Baris Harta Dinamis -->
                <div id="containerDaftarHarta" class="space-y-2.5">
                    <!-- Diisi otomatis oleh renderListHarta() -->
                </div>

                <!-- Hidden inputs untuk database persistence & sinkronisasi -->
                <input type="hidden" name="detail_harta_json" id="detail_harta_json" value="<?= esc(!empty($rawDetailHarta) ? $rawDetailHarta : json_encode(['daftar_harta' => $listDetailHarta, 'daftar_utang' => $listDetailUtang])) ?>">
                <input type="hidden" name="keu_harta_kas_usaha" id="keu_harta_kas_usaha" value="<?= esc($keuangan['harta_kas_usaha'] ?? 0) ?>">
                <input type="hidden" name="keu_harta_tabungan" id="keu_harta_tabungan" value="<?= esc($keuangan['harta_tabungan'] ?? 0) ?>">
                <input type="hidden" name="keu_harta_deposito" id="keu_harta_deposito" value="<?= esc($keuangan['harta_deposito'] ?? 0) ?>">
                <input type="hidden" name="keu_status_deposito" id="keu_status_deposito" value="<?= esc($keuangan['status_deposito'] ?? 'bebas') ?>">
                <input type="hidden" name="keu_harta_tanah" id="keu_harta_tanah" value="<?= esc($keuangan['harta_tanah'] ?? 0) ?>">
                <input type="hidden" name="keu_status_tanah" id="keu_status_tanah" value="<?= esc($keuangan['status_tanah'] ?? 'bebas') ?>">
                <input type="hidden" name="keu_harta_bangunan" id="keu_harta_bangunan" value="<?= esc($keuangan['harta_bangunan'] ?? 0) ?>">
                <input type="hidden" name="keu_status_bangunan" id="keu_status_bangunan" value="<?= esc($keuangan['status_bangunan'] ?? 'bebas') ?>">
                <input type="hidden" name="keu_harta_kendaraan" id="keu_harta_kendaraan" value="<?= esc($keuangan['harta_kendaraan'] ?? 0) ?>">
                <input type="hidden" name="keu_status_kendaraan" id="keu_status_kendaraan" value="<?= esc($keuangan['status_kendaraan'] ?? 'bebas') ?>">
                <input type="hidden" name="keu_harta_piutang_usaha" id="keu_harta_piutang_usaha" value="<?= esc($keuangan['harta_piutang_usaha'] ?? 0) ?>">
                <input type="hidden" name="keu_harta_stok_usaha" id="keu_harta_stok_usaha" value="<?= esc($keuangan['harta_stok_usaha'] ?? 0) ?>">
                <input type="hidden" name="keu_harta_alat_usaha" id="keu_harta_alat_usaha" value="<?= esc($keuangan['harta_alat_usaha'] ?? 0) ?>">

                <!-- Subtotal Harta & Pengurangan Jaminan (Warna Hijau) -->
                <div class="space-y-2 pt-3 border-t border-emerald-200 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Total Nilai Harta Bruto:</span>
                        <span class="font-bold text-slate-800" id="keu_total_harta_bruto">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-rose-600 font-semibold">
                        <span>(-) Aset Berstatus Sedang Dijaminkan:</span>
                        <span id="keu_label_aset_terikat">(Rp 0)</span>
                    </div>
                    
                    <div class="p-3.5 bg-emerald-600 text-white rounded-xl flex justify-between items-center shadow-xs">
                        <div>
                            <span class="font-black uppercase text-[10px] text-emerald-200 block">TOTAL HARTA BERSIH ELIGIBLE (HIJAU)</span>
                            <span class="text-[10px] text-emerald-100">Hanya harta bebas jaminan</span>
                        </div>
                        <span class="text-base font-black text-white" id="keu_total_harta_eligible">Rp 0</span>
                    </div>
                </div>

            </div>

            <!-- ============================================================
                 KOLOM KANAN: UTANG DAN KEWAJIBAN PEMOHON (WARNA MERAH)
                 BISA DITAMBAH BARIS BEBAS DENGAN TOMBOL "+ TAMBAH UTANG"
                 LENGKAP DENGAN KOMPONEN ANGSURAN BERJALAN DARI SLIK OJK
                 ============================================================ -->
            <div class="p-6 bg-rose-50/30 rounded-2xl border border-rose-300 shadow-2xs space-y-5 hover:border-rose-500 transition">
                <div class="flex items-center justify-between pb-3 border-b border-rose-200">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-[10px]">U</div>
                        <span class="font-extrabold text-xs text-rose-950 uppercase tracking-wider">UTANG DAN KEWAJIBAN (WARNA MERAH)</span>
                    </div>
                    <button type="button" onclick="tambahBarisUtang();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-white bg-rose-600 hover:bg-rose-700 border border-rose-600 rounded-lg shadow-2xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Utang</span>
                    </button>
                </div>

                <p class="text-[11px] text-rose-800 leading-relaxed">
                    Tambahkan seluruh kewajiban utang berjalan (utang dagang, koperasi, pinjaman keluarga/kerabat, pinjaman lainnya). Utang Bank &amp; Lembaga Keuangan diakumulasikan otomatis dari data SLIK OJK di bawah.
                </p>

                <!-- Container Baris Utang Dinamis -->
                <div id="containerDaftarUtang" class="space-y-2.5">
                    <!-- Diisi otomatis oleh renderListUtang() -->
                </div>

                <!-- Hidden inputs untuk sinkronisasi database -->
                <input type="hidden" name="keu_neraca_utang_dagang" id="keu_neraca_utang_dagang" value="<?= esc($keuangan['neraca_utang_usaha'] ?? 0) ?>">
                <input type="hidden" name="keu_neraca_utang_bank" id="keu_neraca_utang_bank" value="<?= esc($totalOutstandingSlik) ?>">

                <!-- ============================================================
                     KOMPONEN ANGSURAN BULANAN DARI SLIK OJK
                     ============================================================ -->
                <div class="p-3.5 bg-amber-50/90 rounded-xl border border-amber-300 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[11px] font-extrabold text-amber-950 uppercase tracking-wider">Kewajiban Utang &amp; Angsuran SLIK OJK</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                            <?= esc($worstKolektibilitas) ?>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1 text-xs">
                        <div class="bg-white p-2 rounded-lg border border-amber-200">
                            <span class="text-[10px] text-slate-500 block">Total Baki Debet SLIK:</span>
                            <span class="font-bold text-rose-700 text-xs sm:text-sm" id="slik_baki_debet_val">
                                Rp <?= number_format($totalOutstandingSlik, 0, ',', '.') ?>
                            </span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-amber-200">
                            <span class="text-[10px] text-slate-500 block">Angsuran SLIK / Bulan:</span>
                            <span class="font-bold text-amber-800 text-xs sm:text-sm" id="keu_angs_slik_berjalan">
                                Rp <?= number_format($totalCicilanSlik, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($rekapSlikFasilitas)): ?>
                        <div class="pt-2 border-t border-amber-200/80 space-y-1">
                            <?php foreach ($rekapSlikFasilitas as $f): ?>
                                <div class="flex items-center justify-between text-[10px] text-amber-900">
                                    <span class="truncate max-w-[200px]">&bull; <?= esc($f['bank']) ?> (<?= esc($f['jenis']) ?>)</span>
                                    <span class="font-bold">Rp <?= number_format($f['angsuran'], 0, ',', '.') ?>/bln</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ringkasan Total Utang & Total Beban Angsuran Bulanan (Warna Merah) -->
                <div class="space-y-2 pt-3 border-t border-rose-200 text-xs">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Total Utang Dinamis:</span>
                        <span class="font-bold text-slate-800" id="subtotal_utang_dinamis">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>(+) Baki Debet Fasilitas SLIK OJK:</span>
                        <span class="font-bold text-slate-800">Rp <?= number_format($totalOutstandingSlik, 0, ',', '.') ?></span>
                    </div>
                    
                    <div class="p-3.5 bg-rose-600 text-white rounded-xl flex justify-between items-center shadow-xs">
                        <div>
                            <span class="font-black uppercase text-[10px] text-rose-200 block">TOTAL KEWAJIBAN UTANG RIIL</span>
                            <span class="text-[10px] text-rose-100" id="label_tot_angs_kewajiban">Total Angsuran Berjalan: Rp 0/bln</span>
                        </div>
                        <span class="text-base font-black text-white" id="keu_total_utang">Rp 0</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- ============================================================
         BAGIAN 3: ANALISIS RASIO KEUANGAN & KELAYAKAN (DSCR, DSR, DAR, DER)
         MEMBANDINGKAN SISA PENGHASILAN DENGAN TOTAL ANGSURAN BULANAN (LAMA + BARU)
         ============================================================ -->
    <div class="pt-6 border-t border-slate-200 space-y-5">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-black text-slate-900 tracking-wider uppercase">PERHITUNGAN RASIO-RASIO KEUANGAN</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200">Evaluasi Kredit</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Membandingkan kapasitas sisa penghasilan dengan beban angsuran bulanan berjalan (SLIK) dan kredit baru yang diajukan</p>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                Plafon Pengajuan: <strong>Rp <?= number_format($plafonBaru, 0, ',', '.') ?></strong> (Tenor: <?= $tenorBulanBaru ?> Bln)
            </span>
        </div>

        <!-- Bar Komparasi Beban Angsuran Bulanan Gabungan -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">1. Angsuran Berjalan (Lama)</span>
                <span class="text-sm font-black text-amber-800" id="rasio_angs_slik">Rp <?= number_format($totalCicilanSlik, 0, ',', '.') ?> / bln</span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-500 uppercase block">2. Estimasi Angsuran Baru</span>
                <span class="text-sm font-black text-blue-700" id="rasio_angs_baru">Rp <?= number_format($estimasiAngsuranBaru, 0, ',', '.') ?> / bln</span>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-rose-700 uppercase block">3. Total Angsuran Gabungan</span>
                <span class="text-base font-black text-rose-700" id="rasio_angs_gabungan">Rp <?= number_format($totalAngsuranGabungan, 0, ',', '.') ?> / bln</span>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-emerald-700 uppercase block">4. Sisa Penghasilan (Surplus)</span>
                <span class="text-base font-black text-emerald-700" id="rasio_surplus_ref">Rp 0 / bln</span>
            </div>
        </div>

        <!-- 5 Kartu Rasio Keuangan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            
            <!-- 1. DSCR (Debt Service Coverage Ratio) -->
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5 hover:border-blue-400 transition">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">1. DSCR</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800" id="badge_dscr">LAYAK</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-1 block" id="val_dscr">0.00x</span>
                    <span class="text-[10px] text-slate-400 block">Sisa Surplus ÷ Angsuran Total</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-500 leading-tight">
                    Standar aman: &ge; 1.25x
                </div>
            </div>

            <!-- 2. DSR / DBR (Debt Service Ratio) -->
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5 hover:border-blue-400 transition">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">2. DSR / DBR</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800" id="badge_dsr">IDEAL</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-1 block" id="val_dsr">0.0%</span>
                    <span class="text-[10px] text-slate-400 block">Angsuran Gabungan ÷ Pemasukan</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-500 leading-tight">
                    Maksimal aman: &le; 40%
                </div>
            </div>

            <!-- 3. DAR (Debt to Asset Ratio) -->
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5 hover:border-blue-400 transition">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">3. DAR</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800" id="badge_dar">SEHAT</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-1 block" id="val_dar">0.0%</span>
                    <span class="text-[10px] text-slate-400 block">Total Utang ÷ Harta Eligible</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-500 leading-tight">
                    Maksimal: &le; 50%
                </div>
            </div>

            <!-- 4. DER (Debt to Equity Ratio) -->
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5 hover:border-blue-400 transition">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">4. DER</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800" id="badge_der">KUAT</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-1 block" id="val_der">0.00x</span>
                    <span class="text-[10px] text-slate-400 block">Total Utang ÷ Kekayaan Bersih</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-500 leading-tight">
                    Standar aman: &le; 1.50x
                </div>
            </div>

            <!-- 5. Liquidity Buffer -->
            <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs flex flex-col justify-between space-y-2.5 hover:border-blue-400 transition">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black text-blue-900 uppercase tracking-wider">5. DANA DARURAT</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800" id="badge_buffer">AMAN</span>
                    </div>
                    <span class="text-2xl font-black text-slate-900 mt-1 block" id="val_buffer">0.0 bln</span>
                    <span class="text-[10px] text-slate-400 block">Kas Likuid ÷ Total Beban Bulanan</span>
                </div>
                <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-500 leading-tight">
                    Ketahanan kas: &ge; 3.0 Bulan
                </div>
            </div>

        </div>

    </div>

    <!-- Tombol Aksi Bawah & Navigasi -->
    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <button type="button" onclick="goToTab(8)" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
            &larr; Kembali ke Langkah 8 (Mutasi Rekening)
        </button>
        <button type="button" onclick="handleStepperClick('laporan')" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
            <span>Buka Langkah 10 (Laporan Keuangan)</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
    </div>

    <!-- ============================================================
         SCRIPT DATA KEUANGAN, HARTA, UTANG, SLIK & RASIO
         ============================================================ -->
    <script>
    window.ANGSURAN_SLIK_DEFAULT = <?= (float)$totalCicilanSlik ?>;
    window.OUTSTANDING_SLIK_DEFAULT = <?= (float)$totalOutstandingSlik ?>;
    window.ESTIMASI_ANGSURAN_BARU = <?= (float)$estimasiAngsuranBaru ?>;

    // Data List Pemasukan, Beban, Harta & Utang
    window.daftarPendapatan = <?= json_encode($listDetailPendapatan) ?>;
    window.daftarBebanLain = <?= json_encode($listDetailBeban) ?>;
    window.daftarHarta = <?= json_encode($listDetailHarta) ?>;
    window.daftarUtang = <?= json_encode($listDetailUtang) ?>;
    if (typeof daftarPendapatan !== 'undefined') daftarPendapatan = window.daftarPendapatan;
    if (typeof daftarBebanLain !== 'undefined') daftarBebanLain = window.daftarBebanLain;

    function pNom(val) {
        if (!val) return 0;
        const clean = String(val).replace(/[^0-9]/g, '');
        return clean ? parseFloat(clean) : 0;
    }

    function fNom(num) {
        return 'Rp ' + Math.round(num || 0).toLocaleString('id-ID');
    }

    // --- MANAJEMEN BARIS HARTA ---
    function renderListHarta() {
        const container = document.getElementById('containerDaftarHarta');
        if (!container) return;

        if (!Array.isArray(window.daftarHarta) || window.daftarHarta.length === 0) {
            window.daftarHarta = [{ nama: '', nominal: 0, status: 'bebas' }];
        }

        let html = '';
        window.daftarHarta.forEach((item, index) => {
            const safeNama = (item.nama || '').replace(/"/g, '&quot;');
            const nomVal = parseInt(item.nominal, 10) || 0;
            const nomStr = nomVal > 0 ? nomVal.toLocaleString('id-ID') : '';
            const status = item.status || 'bebas';

            html += `
                <div class="p-3 bg-white border border-emerald-200 rounded-xl space-y-2 shadow-2xs hover:border-emerald-400 transition">
                    <div class="flex items-center gap-2">
                        <input type="text" value="${safeNama}" placeholder="Nama Harta (cth: Tabungan Bank, Deposito, Rumah, dll)" 
                               class="flex-1 text-xs font-bold text-slate-800 border-0 bg-transparent px-1 py-1 focus:ring-0 focus:outline-none placeholder:text-slate-400"
                               oninput="updateItemHarta(${index}, 'nama', this.value)">
                        <select class="text-[10px] font-bold border border-slate-200 rounded-lg px-2 py-1 bg-slate-50 text-slate-700"
                                onchange="updateItemHarta(${index}, 'status', this.value)">
                            <option value="bebas" ${status === 'bebas' ? 'selected' : ''}>Bebas (Tidak Dijaminkan)</option>
                            <option value="terikat" ${status === 'terikat' ? 'selected' : ''}>Sedang Dijaminkan</option>
                        </select>
                        <button type="button" onclick="hapusBarisHarta(${index})" title="Hapus harta ini" 
                                class="w-7 h-7 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 flex items-center justify-center border border-slate-200 transition shrink-0 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100">
                        <span class="text-[10px] text-slate-400 font-semibold uppercase">Nilai Harta (Pasar):</span>
                        <div class="relative flex items-center w-40">
                            <span class="absolute left-2.5 text-[10px] font-bold text-slate-400 select-none">Rp</span>
                            <input type="text" value="${nomStr}" placeholder="0" 
                                   class="w-full text-xs font-bold text-slate-800 text-right border border-slate-200 rounded-lg pl-7 pr-2.5 py-1.5 bg-slate-50/70 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-100 transition rupiah-input"
                                   oninput="formatRupiah(this); updateItemHarta(${index}, 'nominal', this.value)">
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        hitungSemuaDataKeuangan();
    }
    window.renderListHarta = renderListHarta;

    function tambahBarisHarta() {
        if (!Array.isArray(window.daftarHarta)) window.daftarHarta = [];
        window.daftarHarta.push({ nama: '', nominal: 0, status: 'bebas' });
        renderListHarta();
    }
    window.tambahBarisHarta = tambahBarisHarta;

    function hapusBarisHarta(index) {
        if (index >= 0 && index < window.daftarHarta.length) {
            window.daftarHarta.splice(index, 1);
            if (window.daftarHarta.length === 0) {
                window.daftarHarta.push({ nama: '', nominal: 0, status: 'bebas' });
            }
            renderListHarta();
        }
    }
    window.hapusBarisHarta = hapusBarisHarta;

    function updateItemHarta(index, field, val) {
        if (!window.daftarHarta[index]) return;
        if (field === 'nominal') {
            window.daftarHarta[index].nominal = pNom(val);
        } else {
            window.daftarHarta[index][field] = val;
        }
        hitungSemuaDataKeuangan();
    }
    window.updateItemHarta = updateItemHarta;

    // --- MANAJEMEN BARIS UTANG ---
    function renderListUtang() {
        const container = document.getElementById('containerDaftarUtang');
        if (!container) return;

        if (!Array.isArray(window.daftarUtang) || window.daftarUtang.length === 0) {
            window.daftarUtang = [{ nama: '', nominal: 0, angsuran: 0 }];
        }

        let html = '';
        window.daftarUtang.forEach((item, index) => {
            const safeNama = (item.nama || '').replace(/"/g, '&quot;');
            const nomVal = parseInt(item.nominal, 10) || 0;
            const nomStr = nomVal > 0 ? nomVal.toLocaleString('id-ID') : '';
            const angsVal = parseInt(item.angsuran, 10) || 0;
            const angsStr = angsVal > 0 ? angsVal.toLocaleString('id-ID') : '';

            html += `
                <div class="p-3 bg-white border border-rose-200 rounded-xl space-y-2 shadow-2xs hover:border-rose-400 transition">
                    <div class="flex items-center gap-2">
                        <input type="text" value="${safeNama}" placeholder="Nama Utang (cth: Utang Dagang, Koperasi, Pinjaman Lain)" 
                               class="flex-1 text-xs font-bold text-slate-800 border-0 bg-transparent px-1 py-1 focus:ring-0 focus:outline-none placeholder:text-slate-400"
                               oninput="updateItemUtang(${index}, 'nama', this.value)">
                        <button type="button" onclick="hapusBarisUtang(${index})" title="Hapus utang ini" 
                                class="w-7 h-7 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 flex items-center justify-center border border-slate-200 transition shrink-0 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                        <div>
                            <span class="text-[9px] text-slate-400 font-semibold uppercase block mb-0.5">Sisa Utang (Pokok):</span>
                            <div class="relative flex items-center">
                                <span class="absolute left-2 text-[10px] font-bold text-slate-400 select-none">Rp</span>
                                <input type="text" value="${nomStr}" placeholder="0" 
                                       class="w-full text-xs font-bold text-slate-800 text-right border border-slate-200 rounded-lg pl-6 pr-2 py-1 bg-slate-50/70 focus:bg-white focus:border-rose-500 focus:ring-1 focus:ring-rose-100 transition rupiah-input"
                                       oninput="formatRupiah(this); updateItemUtang(${index}, 'nominal', this.value)">
                            </div>
                        </div>
                        <div>
                            <span class="text-[9px] text-slate-400 font-semibold uppercase block mb-0.5">Angsuran / Bulan:</span>
                            <div class="relative flex items-center">
                                <span class="absolute left-2 text-[10px] font-bold text-slate-400 select-none">Rp</span>
                                <input type="text" value="${angsStr}" placeholder="0" 
                                       class="w-full text-xs font-bold text-slate-800 text-right border border-slate-200 rounded-lg pl-6 pr-2 py-1 bg-slate-50/70 focus:bg-white focus:border-rose-500 focus:ring-1 focus:ring-rose-100 transition rupiah-input"
                                       oninput="formatRupiah(this); updateItemUtang(${index}, 'angsuran', this.value)">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        hitungSemuaDataKeuangan();
    }
    window.renderListUtang = renderListUtang;

    function tambahBarisUtang() {
        if (!Array.isArray(window.daftarUtang)) window.daftarUtang = [];
        window.daftarUtang.push({ nama: '', nominal: 0, angsuran: 0 });
        renderListUtang();
    }
    window.tambahBarisUtang = tambahBarisUtang;

    function hapusBarisUtang(index) {
        if (index >= 0 && index < window.daftarUtang.length) {
            window.daftarUtang.splice(index, 1);
            if (window.daftarUtang.length === 0) {
                window.daftarUtang.push({ nama: '', nominal: 0, angsuran: 0 });
            }
            renderListUtang();
        }
    }
    window.hapusBarisUtang = hapusBarisUtang;

    function updateItemUtang(index, field, val) {
        if (!window.daftarUtang[index]) return;
        if (field === 'nominal' || field === 'angsuran') {
            window.daftarUtang[index][field] = pNom(val);
        } else {
            window.daftarUtang[index][field] = val;
        }
        hitungSemuaDataKeuangan();
    }
    window.updateItemUtang = updateItemUtang;

    // --- KALKULASI UTAMA DATA KEUANGAN, HARTA, UTANG & RASIO ---
    function hitungSemuaDataKeuangan() {
        // 1. Pemasukan Bulanan
        let totalPemasukanPokok = 0;
        if (Array.isArray(window.daftarPendapatan) && window.daftarPendapatan.length > 0) {
            window.daftarPendapatan.forEach(item => {
                totalPemasukanPokok += pNom(item.nominal);
            });
        } else {
            totalPemasukanPokok = pNom(document.getElementById('pendapatan_rk')?.value);
        }

        const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
        const pLain = isAdaLain ? pNom(document.getElementById('pendapatan_lain_rk')?.value) : 0;
        const totalPemasukan = totalPemasukanPokok + pLain;

        // 2. Pengeluaran / Beban Bulanan
        let totalBeban = 0;
        if (Array.isArray(window.daftarBebanLain) && window.daftarBebanLain.length > 0) {
            window.daftarBebanLain.forEach(item => {
                totalBeban += pNom(item.nominal);
            });
        } else {
            totalBeban = pNom(document.getElementById('beban_lainnya_nominal_rk')?.value);
        }

        // 3. Sisa Penghasilan / Surplus Bulanan
        const surplus = totalPemasukan - totalBeban;

        // Sinkronisasi hidden input form untuk database
        const inPRK = document.getElementById('pendapatan_rk');
        if (inPRK) inPRK.value = totalPemasukanPokok;
        const inDetailP = document.getElementById('detail_pendapatan_rk');
        if (inDetailP && window.daftarPendapatan) inDetailP.value = JSON.stringify(window.daftarPendapatan);

        const inBRK = document.getElementById('beban_lainnya_nominal_rk');
        if (inBRK) inBRK.value = totalBeban;
        const inDetailB = document.getElementById('detail_beban_lainnya_rk');
        if (inDetailB && window.daftarBebanLain) inDetailB.value = JSON.stringify(window.daftarBebanLain);

        // Update UI Pemasukan, Beban, dan Surplus
        if (document.getElementById('nomTotalPendapatanDinamis')) document.getElementById('nomTotalPendapatanDinamis').innerText = fNom(totalPemasukanPokok);
        if (document.getElementById('labelTotalPendapatanLain')) document.getElementById('labelTotalPendapatanLain').innerText = fNom(pLain);
        if (document.getElementById('nomTotalPenghasilanPR')) document.getElementById('nomTotalPenghasilanPR').innerText = fNom(totalPemasukan);
        if (document.getElementById('nomBebanLainRK')) document.getElementById('nomBebanLainRK').innerText = fNom(totalBeban);
        if (document.getElementById('nomTotalPengeluaranPR')) document.getElementById('nomTotalPengeluaranPR').innerText = fNom(totalBeban);
        if (document.getElementById('nomSurplusPR')) document.getElementById('nomSurplusPR').innerText = fNom(surplus);
        if (document.getElementById('rasio_surplus_ref')) document.getElementById('rasio_surplus_ref').innerText = fNom(surplus);

        // 4. Hitung Harta (Aset) dari Baris Dinamis
        let totalHartaBruto = 0;
        let asetTerikat = 0;
        let totalKasLikuid = 0;

        if (Array.isArray(window.daftarHarta)) {
            window.daftarHarta.forEach(item => {
                const nom = pNom(item.nominal);
                totalHartaBruto += nom;
                if (item.status === 'terikat') {
                    asetTerikat += nom;
                }
                const nLower = (item.nama || '').toLowerCase();
                if ((nLower.includes('kas') || nLower.includes('tabungan') || nLower.includes('deposito')) && item.status !== 'terikat') {
                    totalKasLikuid += nom;
                }
            });
        }
        const totalHartaEligible = Math.max(0, totalHartaBruto - asetTerikat);

        if (document.getElementById('keu_total_harta_bruto')) document.getElementById('keu_total_harta_bruto').innerText = fNom(totalHartaBruto);
        if (document.getElementById('keu_label_aset_terikat')) document.getElementById('keu_label_aset_terikat').innerText = `(${fNom(asetTerikat)})`;
        if (document.getElementById('keu_total_harta_eligible')) document.getElementById('keu_total_harta_eligible').innerText = fNom(totalHartaEligible);

        // 5. Hitung Utang & Kewajiban dari Baris Dinamis + SLIK OJK
        let subtotalUtangDinamis = 0;
        let subtotalAngsuranDinamis = 0;
        if (Array.isArray(window.daftarUtang)) {
            window.daftarUtang.forEach(item => {
                subtotalUtangDinamis += pNom(item.nominal);
                subtotalAngsuranDinamis += pNom(item.angsuran);
            });
        }

        const outSlik = window.OUTSTANDING_SLIK_DEFAULT || 0;
        const angsSlik = window.ANGSURAN_SLIK_DEFAULT || 0;
        const totalUtangRiil = subtotalUtangDinamis + outSlik;
        const totalAngsuranBerjalan = subtotalAngsuranDinamis + angsSlik;

        if (document.getElementById('subtotal_utang_dinamis')) document.getElementById('subtotal_utang_dinamis').innerText = fNom(subtotalUtangDinamis);
        if (document.getElementById('keu_total_utang')) document.getElementById('keu_total_utang').innerText = fNom(totalUtangRiil);
        if (document.getElementById('label_tot_angs_kewajiban')) document.getElementById('label_tot_angs_kewajiban').innerText = `Total Angsuran Berjalan: ${fNom(totalAngsuranBerjalan)}/bln`;

        // 6. Angsuran Kredit Baru & Gabungan
        const angsBaru = window.ESTIMASI_ANGSURAN_BARU || 0;
        const totAngsuranGabungan = totalAngsuranBerjalan + angsBaru;

        if (document.getElementById('keu_angs_slik_berjalan')) document.getElementById('keu_angs_slik_berjalan').innerText = fNom(angsSlik);
        if (document.getElementById('rasio_angs_slik')) document.getElementById('rasio_angs_slik').innerText = `${fNom(totalAngsuranBerjalan)} / bln`;
        if (document.getElementById('rasio_angs_baru')) document.getElementById('rasio_angs_baru').innerText = `${fNom(angsBaru)} / bln`;
        if (document.getElementById('rasio_angs_gabungan')) document.getElementById('rasio_angs_gabungan').innerText = `${fNom(totAngsuranGabungan)} / bln`;

        // 7. Simpan state JSON ke hidden input
        const hiddenDetailHarta = document.getElementById('detail_harta_json');
        if (hiddenDetailHarta) {
            hiddenDetailHarta.value = JSON.stringify({
                daftar_harta: window.daftarHarta,
                daftar_utang: window.daftarUtang,
                total_harta_bruto: totalHartaBruto,
                total_harta_eligible: totalHartaEligible,
                total_utang: totalUtangRiil,
                total_angsuran_berjalan: totalAngsuranBerjalan
            });
        }

        // 8. Hitung Rasio Keuangan
        // a. DSCR = Sisa Penghasilan ÷ Total Angsuran Gabungan
        let dscr = 0;
        if (totAngsuranGabungan > 0) {
            dscr = Math.round((surplus / totAngsuranGabungan) * 100) / 100;
        } else if (surplus > 0) {
            dscr = 99.9;
        }
        const valDscrEl = document.getElementById('val_dscr');
        const badgeDscrEl = document.getElementById('badge_dscr');
        if (valDscrEl) valDscrEl.innerText = `${dscr.toFixed(2)}x`;
        if (badgeDscrEl) {
            if (dscr >= 1.5) {
                badgeDscrEl.innerText = 'SANGAT LAYAK';
                badgeDscrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800';
            } else if (dscr >= 1.25) {
                badgeDscrEl.innerText = 'LAYAK';
                badgeDscrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-blue-100 text-blue-800';
            } else if (dscr >= 1.0) {
                badgeDscrEl.innerText = 'MODERAT';
                badgeDscrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800';
            } else {
                badgeDscrEl.innerText = 'BERISIKO / DEFISIT';
                badgeDscrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800';
            }
        }

        // b. DSR = Total Angsuran Gabungan ÷ Total Pemasukan * 100%
        let dsr = 0;
        if (totalPemasukan > 0) {
            dsr = Math.round((totAngsuranGabungan / totalPemasukan) * 1000) / 10;
        }
        const valDsrEl = document.getElementById('val_dsr');
        const badgeDsrEl = document.getElementById('badge_dsr');
        if (valDsrEl) valDsrEl.innerText = `${dsr.toFixed(1)}%`;
        if (badgeDsrEl) {
            if (dsr <= 40) {
                badgeDsrEl.innerText = 'IDEAL';
                badgeDsrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800';
            } else if (dsr <= 60) {
                badgeDsrEl.innerText = 'MODERAT';
                badgeDsrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800';
            } else {
                badgeDsrEl.innerText = 'KETAT';
                badgeDsrEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800';
            }
        }

        // c. DAR = Total Utang ÷ Total Harta Eligible * 100%
        let dar = 0;
        if (totalHartaEligible > 0) {
            dar = Math.round((totalUtangRiil / totalHartaEligible) * 1000) / 10;
        }
        const valDarEl = document.getElementById('val_dar');
        const badgeDarEl = document.getElementById('badge_dar');
        if (valDarEl) valDarEl.innerText = `${dar.toFixed(1)}%`;
        if (badgeDarEl) {
            if (dar <= 50) {
                badgeDarEl.innerText = 'SEHAT';
                badgeDarEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800';
            } else if (dar <= 70) {
                badgeDarEl.innerText = 'WASPADA';
                badgeDarEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800';
            } else {
                badgeDarEl.innerText = 'TINGGI';
                badgeDarEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800';
            }
        }

        // d. DER = Total Utang ÷ (Total Harta Eligible - Total Utang)
        const kekayaanBersih = Math.max(0, totalHartaEligible - totalUtangRiil);
        let der = 0;
        if (kekayaanBersih > 0) {
            der = Math.round((totalUtangRiil / kekayaanBersih) * 100) / 100;
        }
        const valDerEl = document.getElementById('val_der');
        const badgeDerEl = document.getElementById('badge_der');
        if (valDerEl) valDerEl.innerText = `${der.toFixed(2)}x`;
        if (badgeDerEl) {
            if (der <= 1.5) {
                badgeDerEl.innerText = 'KUAT';
                badgeDerEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800';
            } else if (der <= 2.5) {
                badgeDerEl.innerText = 'MODERAT';
                badgeDerEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800';
            } else {
                badgeDerEl.innerText = 'TINGGI';
                badgeDerEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800';
            }
        }

        // e. Liquidity Buffer = Kas Likuid ÷ Total Beban Komitmen
        const totalBebanKomitmen = totalBeban + totAngsuranGabungan;
        let buffer = 0;
        if (totalBebanKomitmen > 0) {
            buffer = Math.round((totalKasLikuid / totalBebanKomitmen) * 10) / 10;
        }
        const valBufferEl = document.getElementById('val_buffer');
        const badgeBufferEl = document.getElementById('badge_buffer');
        if (valBufferEl) valBufferEl.innerText = `${buffer.toFixed(1)} bln`;
        if (badgeBufferEl) {
            if (buffer >= 3.0) {
                badgeBufferEl.innerText = 'AMAN';
                badgeBufferEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800';
            } else if (buffer >= 1.5) {
                badgeBufferEl.innerText = 'CUKUP';
                badgeBufferEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-amber-100 text-amber-800';
            } else {
                badgeBufferEl.innerText = 'TIPIS';
                badgeBufferEl.className = 'px-2 py-0.5 rounded text-[9px] font-extrabold bg-rose-100 text-rose-800';
            }
        }

        // Auto-switch kategori laporan berdasarkan checkbox pendapatan lain
        if (typeof gantiKategoriDebitur === 'function') {
            gantiKategoriDebitur(isAdaLain ? 'usaha' : 'gaji');
        }

        // Sinkronkan ke Tab 10 (Laporan) seketika
        if (typeof sinkronkanLaporanKeuangan === 'function') {
            sinkronkanLaporanKeuangan();
        }
    }
    window.hitungSemuaDataKeuangan = hitungSemuaDataKeuangan;

    // Render baris awal saat halaman siap
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            if (typeof renderListHarta === 'function') renderListHarta();
            if (typeof renderListUtang === 'function') renderListUtang();
            if (typeof hitungSemuaDataKeuangan === 'function') hitungSemuaDataKeuangan();
        }, 300);
    });
    </script>

</div>
