<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                8
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA LABA RUGI & BEBAN USAHA (BADAN USAHA / PERUSAHAAN)</h3>
                <p class="text-[11px] text-slate-500">Perhitungan laba rugi operasional dari mutasi rekening koran perusahaan, beban usaha operasional, dan pendapatan non-operasional</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Verifikasi Rekening Koran & Cashflow Badan Usaha
        </span>
    </div>

    <!-- Layout Laba Rugi Operasional Perusahaan (Badan Usaha fokus ke Operasional, tanpa Pendapatan Sampingan) -->
    <div class="space-y-6">
        <!-- Card Laba Rugi Operasional Perusahaan -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div>
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Laba Rugi Operasional Perusahaan</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Validasi mutasi rekening koran giro / operasional PT / CV</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-100/70 border border-blue-200">
                        Rekening Koran Perusahaan
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Usaha Bulanan (Rp)</label>
                        <input type="text" name="pendapatan_rk" id="pendapatan_rk" 
                               value="<?= number_format((float)($keuangan['pendapatan_rk'] ?? 250000000), 0, ',', '.') ?>" 
                               class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                               oninput="formatRupiah(this); hitungLabaRugi();">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP / COGS (%)</label>
                            <input type="number" step="0.1" name="hpp_persen_rk" id="hpp_persen_rk" 
                                   value="<?= esc($keuangan['hpp_persen_rk'] ?? 60) ?>" 
                                   class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                   oninput="hitungLabaRugi();">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP / Biaya Pokok Produksi</label>
                            <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                <span id="nomHppRK" class="font-bold text-slate-800">Rp 0</span>
                            </div>
                        </div>
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

    <!-- =====================================================
         SECTION DATA LAPORAN KEUANGAN (Collapsible)
         Input neraca & arus kas untuk Generate Laporan Tahap 10
         Semua nama field sinkron dengan kolom kredit_keuangan_dsc
         ===================================================== -->
    <?php
        // $keuangan sudah ter-inject oleh controller (data dari kredit_keuangan_dsc)
        $k9 = $keuangan ?? [];
    ?>
    <div class="mt-6" id="sectionDataLaporan">
        <!-- Toggle Header -->
        <button type="button" onclick="toggleSectionLaporan()"
                class="w-full flex items-center justify-between p-4 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-2xl transition shadow-2xs group">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="text-left">
                    <div class="text-xs font-extrabold text-indigo-900 uppercase tracking-wider">Data Laporan Keuangan (Neraca & Arus Kas)</div>
                    <div class="text-[11px] text-indigo-600">Isi data ini untuk Generate Laporan di Tahap 10 → klik untuk buka / tutup</div>
                </div>
            </div>
            <svg id="ikonToggleLaporan" class="w-5 h-5 text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <!-- Panel Collapsible -->
        <div id="panelDataLaporan" class="hidden mt-4 space-y-5">

            <!-- === A. ASET LANCAR === -->
            <div class="p-5 bg-blue-50/60 rounded-2xl border border-blue-200/80">
                <h5 class="text-[11px] font-extrabold text-blue-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">A</span>
                    Aset Lancar
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Kas Awal Periode (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Saldo kas & bank pada awal periode. Kas akhir dihitung otomatis dari Arus Kas.">ⓘ</span>
                        </label>
                        <input type="text" name="neraca_awal_kas" id="lkn_saldo_awal_kas"
                               value="<?= !empty($k9['neraca_awal_kas']) ? number_format((float)$k9['neraca_awal_kas'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Piutang Usaha Akhir (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Saldo piutang usaha akhir periode. Kosong = estimasi DSO × Pendapatan / 365">ⓘ</span>
                        </label>
                        <input type="text" name="input_piutang_akhir" id="lkn_piutang_usaha"
                               value="<?= !empty($k9['input_piutang_akhir']) ? number_format((float)$k9['input_piutang_akhir'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = estimasi DSO" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Persediaan Akhir (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Saldo persediaan akhir periode. Kosong = estimasi DIO × HPP / 365">ⓘ</span>
                        </label>
                        <input type="text" name="input_persediaan_akhir" id="lkn_persediaan"
                               value="<?= !empty($k9['input_persediaan_akhir']) ? number_format((float)$k9['input_persediaan_akhir'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = estimasi DIO" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Awal Piutang (Rp)</label>
                        <input type="text" name="neraca_awal_piutang" id="lkn_saldo_awal_piutang"
                               value="<?= !empty($k9['neraca_awal_piutang']) ? number_format((float)$k9['neraca_awal_piutang'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Awal Persediaan (Rp)</label>
                        <input type="text" name="neraca_awal_persediaan" id="lkn_saldo_awal_persediaan"
                               value="<?= !empty($k9['neraca_awal_persediaan']) ? number_format((float)$k9['neraca_awal_persediaan'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">DSO / Hari Piutang (hari)</label>
                        <input type="number" name="neraca_hari_piutang_dso" id="lkn_asumsi_dso"
                               value="<?= !empty($k9['neraca_hari_piutang_dso']) ? (int)$k9['neraca_hari_piutang_dso'] : 30 ?>"
                               min="1" max="365"
                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                    </div>
                </div>
            </div>

            <!-- === B. ASET TETAP === -->
            <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/90">
                <h5 class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-slate-600 text-white flex items-center justify-center text-[10px] font-black">B</span>
                    Aset Tetap
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Aset Tetap Bruto Akhir (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Total harga perolehan aset tetap akhir periode = Saldo Awal + CAPEX tahun ini">ⓘ</span>
                        </label>
                        <input type="text" name="neraca_aset_tetap_bruto" id="lkn_harga_perolehan"
                               value="<?= !empty($k9['neraca_aset_tetap_bruto']) ? number_format((float)$k9['neraca_aset_tetap_bruto'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Akumulasi Penyusutan (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Kosong = penyusutan tahun ini saja (Harga / Umur Ekonomis)">ⓘ</span>
                        </label>
                        <input type="text" name="neraca_akumulasi_penyusutan" id="lkn_akum_penyusutan"
                               value="<?= !empty($k9['neraca_akumulasi_penyusutan']) ? number_format((float)$k9['neraca_akumulasi_penyusutan'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = auto garis lurus" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Umur Ekonomis (Tahun)</label>
                        <input type="number" name="input_capex_umur_ekonomis" id="lkn_umur_ekonomis"
                               value="<?= !empty($k9['input_capex_umur_ekonomis']) ? (int)$k9['input_capex_umur_ekonomis'] : 5 ?>"
                               min="1" max="50"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">CAPEX — Pembelian Aset Tahun Ini (Rp)</label>
                        <input type="text" name="input_capex" id="lkn_beli_aset"
                               value="<?= !empty($k9['input_capex']) ? number_format((float)$k9['input_capex'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Beban Penyusutan (Rp/Tahun)
                            <span class="ml-1 text-blue-500 cursor-help" title="Masukkan dari laporan pajak/akuntansi. Kosong = Aset Bruto / Umur Ekonomis">ⓘ</span>
                        </label>
                        <input type="text" name="beban_depresiasi" id="lkn_beban_penyusutan"
                               value="<?= !empty($k9['beban_depresiasi']) ? number_format((float)$k9['beban_depresiasi'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = Aset / Umur" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Awal Aset Tetap Bruto (Rp)</label>
                        <input type="text" name="neraca_awal_aset_tetap" id="lkn_saldo_awal_aset"
                               value="<?= !empty($k9['neraca_awal_aset_tetap']) ? number_format((float)$k9['neraca_awal_aset_tetap'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                </div>
            </div>

            <!-- === C. LIABILITAS === -->
            <div class="p-5 bg-rose-50/50 rounded-2xl border border-rose-200/80">
                <h5 class="text-[11px] font-extrabold text-rose-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-rose-600 text-white flex items-center justify-center text-[10px] font-black">C</span>
                    Liabilitas (Kewajiban)
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Utang Usaha Akhir (Rp)
                            <span class="ml-1 text-blue-500 cursor-help" title="Kosong = estimasi DPO × HPP / 365">ⓘ</span>
                        </label>
                        <input type="text" name="input_utang_usaha_akhir" id="lkn_utang_usaha"
                               value="<?= !empty($k9['input_utang_usaha_akhir']) ? number_format((float)$k9['input_utang_usaha_akhir'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = estimasi DPO" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Utang Bank Jangka Pendek (Rp)</label>
                        <input type="text" name="neraca_utang_bank_lancar" id="lkn_utang_bank_pendek"
                               value="<?= !empty($k9['neraca_utang_bank_lancar']) ? number_format((float)$k9['neraca_utang_bank_lancar'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Utang Bank Jangka Panjang (Rp)</label>
                        <input type="text" name="neraca_utang_bank_panjang" id="lkn_utang_jk_panjang"
                               value="<?= !empty($k9['neraca_utang_bank_panjang']) ? number_format((float)$k9['neraca_utang_bank_panjang'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pembayaran Pokok Utang Bank (Rp/Tahun)</label>
                        <input type="text" name="input_pembayaran_pokok" id="lkn_cicilan_pokok"
                               value="<?= !empty($k9['input_pembayaran_pokok']) ? number_format((float)$k9['input_pembayaran_pokok'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Awal Utang Usaha (Rp)</label>
                        <input type="text" name="neraca_awal_utang_usaha" id="lkn_saldo_awal_utang"
                               value="<?= !empty($k9['neraca_awal_utang_usaha']) ? number_format((float)$k9['neraca_awal_utang_usaha'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Saldo Awal Utang Bank (Rp)</label>
                        <input type="text" name="neraca_awal_utang_bank" id="lkn_saldo_awal_utang_bank"
                               value="<?= !empty($k9['neraca_awal_utang_bank']) ? number_format((float)$k9['neraca_awal_utang_bank'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">DPO / Hari Utang (hari)</label>
                        <input type="number" name="neraca_hari_utang_dpo" id="lkn_asumsi_dpo"
                               value="<?= !empty($k9['neraca_hari_utang_dpo']) ? (int)$k9['neraca_hari_utang_dpo'] : 30 ?>"
                               min="1" max="365"
                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                    </div>
                </div>
            </div>

            <!-- === D. EKUITAS & BEBAN KHUSUS === -->
            <div class="p-5 bg-emerald-50/50 rounded-2xl border border-emerald-200/80">
                <h5 class="text-[11px] font-extrabold text-emerald-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black">D</span>
                    Ekuitas (Modal) &amp; Beban Khusus
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Modal Disetor (Rp)</label>
                        <input type="text" name="neraca_modal_disetor" id="lkn_modal_disetor"
                               value="<?= !empty($k9['neraca_modal_disetor']) ? number_format((float)$k9['neraca_modal_disetor'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Laba Ditahan Awal (Rp)</label>
                        <input type="text" name="neraca_awal_laba_ditahan" id="lkn_laba_ditahan_awal"
                               value="<?= !empty($k9['neraca_awal_laba_ditahan']) ? number_format((float)$k9['neraca_awal_laba_ditahan'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Prive / Dividen (Rp/Tahun)</label>
                        <input type="text" name="input_prive_dividen" id="lkn_prive_dividen"
                               value="<?= !empty($k9['input_prive_dividen']) ? number_format((float)$k9['input_prive_dividen'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Beban Bunga (Rp/Tahun)
                            <span class="ml-1 text-blue-500 cursor-help" title="Beban bunga pinjaman bank, masuk ke Laba Rugi dan mengurangi laba sebelum pajak">ⓘ</span>
                        </label>
                        <input type="text" name="input_beban_bunga" id="lkn_beban_bunga"
                               value="<?= !empty($k9['input_beban_bunga']) ? number_format((float)$k9['input_beban_bunga'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Pajak (Rp/Tahun)
                            <span class="ml-1 text-blue-500 cursor-help" title="Beban pajak aktual. Kosong = estimasi 5% dari laba sebelum pajak">ⓘ</span>
                        </label>
                        <input type="text" name="input_beban_pajak" id="lkn_pajak"
                               value="<?= !empty($k9['input_beban_pajak']) ? number_format((float)$k9['input_beban_pajak'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Kosong = auto 5%" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pinjaman Baru Diterima (Rp/Tahun)</label>
                        <input type="text" name="input_pinjaman_baru" id="lkn_pinjaman_baru"
                               value="<?= !empty($k9['input_pinjaman_baru']) ? number_format((float)$k9['input_pinjaman_baru'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Setoran Modal Baru (Rp/Tahun)</label>
                        <input type="text" name="input_setoran_modal" id="lkn_setoran_modal"
                               value="<?= !empty($k9['input_setoran_modal']) ? number_format((float)$k9['input_setoran_modal'], 0, ',', '.') : '' ?>"
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0" oninput="formatRupiah(this)">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">DIO / Hari Persediaan (hari)</label>
                        <input type="number" name="neraca_hari_stok_dio" id="lkn_asumsi_dio"
                               value="<?= !empty($k9['neraca_hari_stok_dio']) ? (int)$k9['neraca_hari_stok_dio'] : 45 ?>"
                               min="1" max="365"
                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-3 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                    </div>
                </div>
            </div>

            <!-- === TOMBOL GENERATE === -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-sm">
                <div class="text-white">
                    <div class="text-sm font-extrabold">Generate Laporan Keuangan Estimasi</div>
                    <div class="text-[11px] text-indigo-200 mt-0.5">Simpan form terlebih dahulu (Simpan &amp; Lanjut), lalu klik Generate</div>
                </div>
                <button type="button" id="btnGenerateLaporanBU" onclick="generateLaporanKeuangan()"
                        class="inline-flex items-center gap-2.5 px-6 py-3 bg-white hover:bg-indigo-50 text-indigo-700 text-sm font-extrabold rounded-xl shadow-xs transition focus:ring-2 focus:ring-white/40 whitespace-nowrap">
                    <svg id="ikonGenDefaultBU" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <svg id="ikonGenSpinnerBU" class="hidden w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span id="textGenBU">⚡ Generate Laporan Keuangan</span>
                </button>
            </div>

        </div><!-- /panelDataLaporan -->
    </div><!-- /sectionDataLaporan -->

    <script>
    function toggleSectionLaporan() {
        const panel = document.getElementById('panelDataLaporan');
        const ikon  = document.getElementById('ikonToggleLaporan');
        const hidden = panel.classList.toggle('hidden');
        ikon.style.transform = hidden ? '' : 'rotate(180deg)';
    }
    </script>
</div>

