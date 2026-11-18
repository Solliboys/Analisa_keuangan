<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                8
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA DATA KEUANGAN & BEBAN USAHA (BADAN USAHA / PERUSAHAAN)</h3>
                <p class="text-[11px] text-slate-500">Perhitungan data keuangan operasional dari mutasi rekening koran perusahaan, beban usaha operasional, dan pendapatan non-operasional</p>
            </div>
        </div>
    </div>

    <!-- Layout Data Keuangan Operasional Perusahaan (Badan Usaha fokus ke Operasional, tanpa Pendapatan Sampingan) -->
    <div class="space-y-6">
        <!-- Card Data Keuangan Operasional Perusahaan -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div>
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Data Keuangan Operasional Perusahaan</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Validasi mutasi rekening koran giro / operasional PT / CV</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php
                        // Cek akumulasi mutasi rekening koran untuk auto-sync omzet dan HPP awal
                        $totRekKreditBU = 0;
                        $totRekDebitBU  = 0;
                        if (!empty($rekening) && is_array($rekening)) {
                            foreach ($rekening as $rk) {
                                $m = max(1, (int)($rk['jumlah_bulan'] ?? 6));
                                $k = (float)($rk['total_mutasi_kredit'] ?? 0);
                                $d = (float)($rk['total_mutasi_debit'] ?? 0);
                                $totRekKreditBU += round($k / $m);
                                $totRekDebitBU  += round($d / $m);
                            }
                        }
                        $autoOmzetRekBU = ($totRekKreditBU > 0) ? $totRekKreditBU : 0;

                        // Estimasi beban operasional awal untuk memisahkan HPP murni dari mutasi debit
                        $bebanOpAwalBU   = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 25000000);
                        $bebanLainAwalBU = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
                        $totalOpAwalBU   = ($bebanOpAwalBU > 0 ? $bebanOpAwalBU : 25000000) + $bebanLainAwalBU;

                        // HPP Murni = Mutasi Debit - Beban Operasional agar tidak double-counting (maksimal 78% agar tidak rugi)
                        $debitMurniBU = max(0, $totRekDebitBU - $totalOpAwalBU);
                        $autoHppRekBU = ($totRekKreditBU > 0 && $debitMurniBU > 0) 
                            ? round(($debitMurniBU / $totRekKreditBU) * 100, 1) 
                            : 78;
                        if ($autoHppRekBU > 82 || $autoHppRekBU <= 0) {
                            $autoHppRekBU = 78;
                        }

                        $nomPendapatanBU = (float)($keuangan['pendapatan_rk'] ?? 0);
                        if ($nomPendapatanBU <= 0 && $autoOmzetRekBU > 0) {
                            $nomPendapatanBU = $autoOmzetRekBU;
                        }
                        if ($nomPendapatanBU <= 0) {
                            $nomPendapatanBU = 250000000;
                        }

                        $persenHppBU = (float)($keuangan['hpp_persen_rk'] ?? 0);
                        if (($persenHppBU <= 0 || $persenHppBU == 60 || $persenHppBU >= 90) && $autoHppRekBU > 0) {
                            $persenHppBU = $autoHppRekBU;
                        }
                        if ($persenHppBU <= 0) {
                            $persenHppBU = 78;
                        }

                        $nomHppAwalBU = (float)($keuangan['hpp_nominal_rk'] ?? 0);
                        if (($nomHppAwalBU <= 0 || abs($nomHppAwalBU - round($nomPendapatanBU * ($persenHppBU / 100))) > 5000000) && $nomPendapatanBU > 0 && $persenHppBU > 0) {
                            $nomHppAwalBU = round($nomPendapatanBU * ($persenHppBU / 100));
                        }

                        $rawDetailHppBU = $keuangan['detail_hpp_rk'] ?? '';
                        $listDetailHppBU = [];
                        if (!empty($rawDetailHppBU)) {
                            $decHpp = json_decode($rawDetailHppBU, true);
                            if (is_array($decHpp) && count($decHpp) > 0) {
                                $listDetailHppBU = $decHpp;
                            }
                        }
                        if (empty($listDetailHppBU)) {
                            if ($nomHppAwalBU > 0) {
                                $listDetailHppBU = [
                                    ['nama' => 'Biaya Modal / Kulakan Barang Dagang', 'kategori' => 'hpp', 'nominal' => $nomHppAwalBU]
                                ];
                            } else {
                                $listDetailHppBU = [
                                    ['nama' => 'Biaya Modal / Kulakan Barang Dagang', 'kategori' => 'hpp', 'nominal' => 0]
                                ];
                            }
                        }
                    ?>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Usaha Bulanan (Rp)</label>
                        <input type="text" name="pendapatan_rk" id="pendapatan_rk" 
                               value="<?= number_format($nomPendapatanBU, 0, ',', '.') ?>" 
                               class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                               oninput="formatRupiah(this); hitungLabaRugi();"
                               onblur="if(this.value){ formatRupiah(this); hitungLabaRugi(); }">
                    </div>

                    <!-- Pengeluaran HPP & Belanja Aset (Dinamis: Bisa Ditambah & Dihapus) -->
                    <div class="pt-2 border-t border-blue-100/80">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Pengeluaran HPP &amp; Belanja Aset</label>
                                <p class="text-[10px] text-slate-400">Rincian pengeluaran kulakan/bahan baku dan pembelian aset usaha yang dinamis</p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="tambahHpp('Biaya Bahan / Kulakan', 'hpp');" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>Biaya HPP</span>
                                </button>
                                <button type="button" onclick="tambahHpp('Pembelian Aset / Peralatan', 'aset');" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50/80 hover:bg-emerald-100 border border-emerald-300 rounded-lg shadow-2xs transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>Tambah Aset</span>
                                </button>
                            </div>
                        </div>

                        <!-- Container List Rincian HPP & Aset Dinamis -->
                        <div id="containerHppDinamis" class="space-y-2 mb-2.5">
                            <!-- Diisi oleh renderListHpp() -->
                        </div>

                        <!-- Bar Ringkasan Total HPP & Aset -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs mb-2 shadow-2xs">
                            <div class="flex justify-between items-center px-1">
                                <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Total HPP (Bahan/Kulakan):</span>
                                <span id="nomTotalHppDinamis" class="font-bold text-blue-700 text-xs sm:text-sm">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center px-1 border-t sm:border-t-0 sm:border-l border-slate-200 sm:pl-3 pt-1 sm:pt-0">
                                <span class="font-bold text-emerald-800 text-[11px] uppercase tracking-wider">Total Pembelian Aset:</span>
                                <span id="nomTotalAsetDinamis" class="font-bold text-emerald-700 text-xs sm:text-sm">Rp 0</span>
                            </div>
                        </div>

                        <!-- Hidden fields untuk sinkronisasi database & sistem lama -->
                        <input type="hidden" name="hpp_persen_rk" id="hpp_persen_rk" value="<?= esc($persenHppBU) ?>">
                        <input type="hidden" name="hpp_nominal_rk" id="nomHppRKInput" value="<?= esc($nomHppAwalBU) ?>">
                        <input type="hidden" name="detail_hpp_rk" id="detail_hpp_rk" value="<?= esc(!empty($listDetailHppBU) ? json_encode($listDetailHppBU) : '[]') ?>">
                        <input type="hidden" name="total_aset_hpp_rk" id="total_aset_hpp_rk" value="0">
                        <span id="nomHppRK" class="hidden">Rp 0</span>
                    </div>

                    <div class="p-3.5 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                        <span class="font-bold text-blue-900">Laba Kotor Usaha (Gross Profit):</span>
                        <span id="grossRK" class="text-sm font-black text-blue-700">Rp 0</span>
                    </div>

                    <!-- Beban Operasional Pokok Usaha (Gaji Karyawan, Sewa Kantor/Gudang, Utilitas Pabrik/Kantor) -->
                    <?php
                        $nomBebanOperasional = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);
                        if ($nomBebanOperasional <= 0 && !empty($keuangan['pendapatan_rk'])) {
                            $nomBebanOperasional = ((float)$keuangan['pendapatan_rk']) * (((float)($keuangan['biaya_hidup_persen_rk'] ?? 10)) / 100);
                        }
                        if ($nomBebanOperasional <= 0) {
                            $nomBebanOperasional = 25000000;
                        }

                        $rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
                        $listDetailBeban = [];
                        if (!empty($rawDetailBeban)) {
                            $decBeban = json_decode($rawDetailBeban, true);
                            if (is_array($decBeban)) {
                                $listDetailBeban = $decBeban;
                            }
                        }
                        if (empty($listDetailBeban)) {
                            $nomBebanLama = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
                            if ($nomBebanLama > 0) {
                                $listDetailBeban = [['nama' => 'Beban Operasional Lainnya', 'nominal' => $nomBebanLama]];
                            }
                        }
                    ?>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Operasional Pokok Usaha (Rp/Bulan)</label>
                        <p class="text-[10px] text-slate-400 mb-1.5">Gaji karyawan tetap, sewa kantor/gudang usaha, listrik, air & utilitas operasional</p>
                        <input type="text" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" 
                               value="<?= number_format($nomBebanOperasional, 0, ',', '.') ?>" 
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0"
                               oninput="formatRupiah(this); hitungLabaRugi();">
                        <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>">
                    </div>

                    <!-- Beban Usaha & Administrasi Umum Lainnya -->
                    <div class="pt-2 border-t border-blue-100/80">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Beban Usaha & Administrasi Umum Lainnya</label>
                                <p class="text-[10px] text-slate-400">Rincian beban (misal: Biaya Logistik/Kirim, Perawatan Mesin/Aset, Legalitas/Pajak, dll)</p>
                            </div>
                            <button type="button" onclick="tambahBebanLain();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Beban Usaha</span>
                            </button>
                        </div>

                        <!-- Container List Rincian Beban Dinamis -->
                        <div id="containerBebanLain" class="space-y-2 mb-2.5">
                            <!-- Diisi oleh renderListBebanLain() -->
                        </div>

                        <!-- Bar Total Nominal Beban Lain -->
                        <div class="p-2.5 bg-white border border-slate-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                            <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Total Beban Usaha Lain:</span>
                            <span id="nomBebanLainRK" class="font-bold text-blue-700 text-xs sm:text-sm">Rp 0</span>
                        </div>

                        <!-- Hidden fields untuk database -->
                        <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
                        <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
                        <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Laba Operasional Usaha (EBITDA)</div>
                        <div class="text-xs font-extrabold text-white truncate">Rekening Koran Perusahaan</div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span id="nettRK" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>
            <!-- Hidden input compatibilitas (Badan Usaha tidak memiliki pendapatan sampingan) -->
            <input type="hidden" name="ada_bukti_pendapatan_lain_rk" id="ada_bukti_pendapatan_lain_rk" value="0">
            <input type="hidden" name="pendapatan_lain_rk" id="pendapatan_lain_rk" value="0">
            <input type="hidden" name="bukti_pendapatan_lain_rk" id="bukti_pendapatan_lain_rk" value="">
            <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">
            <span id="labelTotalPendapatanLain" class="hidden">Rp 0</span>
            <span id="totalCashflowBersih" class="hidden">Rp 0</span>
        </div>
    </div>
