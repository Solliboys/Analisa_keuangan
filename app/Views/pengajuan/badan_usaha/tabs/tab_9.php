<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                8
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA DATA KEUANGAN & BEBAN USAHA (BADAN USAHA / PERUSAHAAN)</h3>
                <p class="text-[11px] text-slate-500">Perhitungan data keuangan operasional dari mutasi rekening koran perusahaan, beban usaha operasional, dan belanja aset</p>
            </div>
        </div>
    </div>

    <?php
        // Cek akumulasi mutasi rekening koran untuk auto-sync jika ada
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

        // Nominal awal murni dari database (default 0 / kosongan jika belum diisi)
        $nomPendapatanBU = (float)($keuangan['pendapatan_rk'] ?? 0);
        if ($nomPendapatanBU <= 0 && $autoOmzetRekBU > 0) {
            $nomPendapatanBU = $autoOmzetRekBU;
        }

        $persenHppBU = (float)($keuangan['hpp_persen_rk'] ?? 0);
        $nomHppAwalBU = (float)($keuangan['hpp_nominal_rk'] ?? 0);

        // 1. Data Rincian Pendapatan Dinamis (Default Kosongan)
        $rawDetailPendapatanBU = $keuangan['detail_pendapatan_rk'] ?? '';
        $listDetailPendapatanBU = [];
        if (!empty($rawDetailPendapatanBU) && $rawDetailPendapatanBU !== '[]') {
            $decP = json_decode($rawDetailPendapatanBU, true);
            if (is_array($decP) && count($decP) > 0) {
                $listDetailPendapatanBU = $decP;
            }
        }
        if (empty($listDetailPendapatanBU) && $nomPendapatanBU > 0) {
            $listDetailPendapatanBU = [
                ['nama' => '', 'nominal' => $nomPendapatanBU]
            ];
        }

        // 2. Data Rincian HPP & Belanja Aset Dinamis (Default Kosongan)
        $rawDetailHppBU = $keuangan['detail_hpp_rk'] ?? '';
        $listDetailHppBU = [];
        if (!empty($rawDetailHppBU) && $rawDetailHppBU !== '[]') {
            $decHpp = json_decode($rawDetailHppBU, true);
            if (is_array($decHpp) && count($decHpp) > 0) {
                $listDetailHppBU = $decHpp;
            }
        }
        if (empty($listDetailHppBU) && $nomHppAwalBU > 0) {
            $listDetailHppBU = [
                ['nama' => '', 'kategori' => 'hpp', 'nominal' => $nomHppAwalBU]
            ];
        }

        // 3. Data Rincian Pengeluaran Biasa Dinamis (Default Kosongan)
        $nomBebanOperasional = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);
        $rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
        $listDetailBeban = [];
        if (!empty($rawDetailBeban) && $rawDetailBeban !== '[]') {
            $decBeban = json_decode($rawDetailBeban, true);
            if (is_array($decBeban) && count($decBeban) > 0) {
                $listDetailBeban = $decBeban;
            }
        }
        if (empty($listDetailBeban)) {
            $nomBebanLama = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
            if ($nomBebanOperasional > 0 && $nomBebanLama > 0) {
                $listDetailBeban = [
                    ['nama' => 'Beban Operasional Pokok', 'nominal' => $nomBebanOperasional],
                    ['nama' => 'Beban Usaha Lainnya', 'nominal' => $nomBebanLama]
                ];
            } elseif ($nomBebanOperasional > 0) {
                $listDetailBeban = [
                    ['nama' => 'Beban Operasional Pokok', 'nominal' => $nomBebanOperasional]
                ];
            } elseif ($nomBebanLama > 0) {
                $listDetailBeban = [
                    ['nama' => 'Beban Usaha & Operasional', 'nominal' => $nomBebanLama]
                ];
            }
        }
    ?>

    <!-- Layout 3 Kotak Data Keuangan Badan Usaha (1: Pendapatan [Biru], 2: Pengeluaran [Merah], 3: Aset [Hijau]) -->
    <div class="space-y-6">
        
        <!-- ======================================================== -->
        <!-- KOTAK 1: PENDAPATAN / OMSET USAHA (AKSEN BIRU DIKIT)     -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-blue-50/25 rounded-2xl border border-blue-200/90 shadow-2xs hover:border-blue-300 transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-blue-100 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        1
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>PENDAPATAN / OMSET USAHA BULANAN</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-full">Pemasukan</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian sumber pendapatan operasional perusahaan yang dinamis</span>
                    </div>
                </div>
                <button type="button" onclick="tambahPendapatan('', 0);" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-xl shadow-2xs transition cursor-pointer self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Pendapatan</span>
                </button>
            </div>

            <!-- Container List Rincian Pendapatan Dinamis -->
            <div id="containerPendapatanDinamis" class="space-y-2 mb-3">
                <!-- Diisi oleh renderListPendapatan() -->
            </div>

            <!-- Bar Ringkasan Total Pendapatan Bulanan -->
            <div class="p-3 bg-white border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pendapatan / Omset Bulanan:</span>
                <span id="nomTotalPendapatanDinamis" class="text-sm font-black text-blue-700">Rp <?= number_format($nomPendapatanBU, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KOTAK 2: PENGELUARAN USAHA (MERAH)                      -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-rose-50/20 rounded-2xl border border-rose-200/90 shadow-2xs hover:border-rose-300 transition">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-rose-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        2
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>PENGELUARAN USAHA</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-rose-100 text-rose-700 rounded-full">Pengeluaran</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian pengeluaran operasional rutin dan modal bahan/kulakan (HPP) yang dinamis</span>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <!-- Sub-Bagian 2A: Pengeluaran (Beban Operasional & Administrasi) - DI ATAS -->
                <div class="p-4 bg-white/80 rounded-xl border border-rose-100 shadow-2xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                A. Pengeluaran (Beban Operasional &amp; Administrasi)
                            </h4>
                            <p class="text-[10px] text-slate-400">Gaji karyawan tetap, sewa kantor/gudang, listrik & utilitas, logistik, operasional rutin, dll</p>
                        </div>
                        <button type="button" onclick="tambahBebanLain('', 0);" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg shadow-2xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Pengeluaran</span>
                        </button>
                    </div>

                    <!-- Container List Beban Dinamis -->
                    <div id="containerBebanLain" class="space-y-2 mb-2.5">
                        <!-- Diisi oleh renderListBebanLain() -->
                    </div>

                    <!-- Subtotal Pengeluaran -->
                    <div class="p-2.5 bg-rose-50/60 border border-rose-200/70 rounded-xl flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Subtotal Pengeluaran:</span>
                        <span id="nomBebanLainRK" class="font-bold text-rose-700 text-xs sm:text-sm">Rp 0</span>
                    </div>
                </div>

                <!-- Sub-Bagian 2B: Pengeluaran HPP (Bahan Baku / Modal Kulakan) - DI BAWAH -->
                <div class="p-4 bg-white/80 rounded-xl border border-rose-100 shadow-2xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                B. Pengeluaran HPP (Bahan Baku / Modal Kulakan)
                            </h4>
                            <p class="text-[10px] text-slate-400">Biaya pokok barang dagang, pembelian stok bahan, atau modal produksi</p>
                        </div>
                        <button type="button" onclick="tambahHppOnly('', 0);" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg shadow-2xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah HPP</span>
                        </button>
                    </div>

                    <!-- Container List HPP Dinamis -->
                    <div id="containerHppOnlyDinamis" class="space-y-2 mb-2.5">
                        <!-- Diisi oleh renderListHpp() -->
                    </div>

                    <!-- Subtotal HPP -->
                    <div class="p-2.5 bg-rose-50/60 border border-rose-200/70 rounded-xl flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Subtotal HPP (Bahan/Kulakan):</span>
                        <div class="flex items-center gap-1.5">
                            <span id="nomTotalHppDinamis" class="font-bold text-rose-700 text-xs sm:text-sm">Rp <?= number_format($nomHppAwalBU, 0, ',', '.') ?></span>
                            <span id="badgePersenHpp" class="text-[11px] text-slate-500 font-medium">(<?= esc($persenHppBU) ?>% dari Omzet)</span>
                        </div>
                    </div>

                    <!-- Laba Kotor Usaha (Gross Profit): Tepat di bawah Subtotal HPP -->
                    <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl flex justify-between items-center text-xs shadow-2xs mt-2.5">
                        <div>
                            <span class="font-bold text-blue-900 text-[11px] uppercase tracking-wider block">Laba Kotor Usaha (Gross Profit):</span>
                            <span class="text-[10px] text-blue-600 font-medium">* Total Pendapatan / Omset dikurangi Subtotal HPP</span>
                        </div>
                        <span id="grossRK" class="text-sm sm:text-base font-black text-blue-700">Rp 0</span>
                    </div>
                </div>

                <!-- Sub-Bagian 2C: Pengeluaran Cicilan Bulanan (Daftar Hutang / Pinjaman Berjalan) -->
                <div class="p-4 bg-white/80 rounded-xl border border-rose-100 shadow-2xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                C. Pengeluaran Cicilan Bulanan (Daftar Hutang / Pinjaman Berjalan)
                            </h4>
                            <p class="text-[10px] text-slate-400">Cicilan pinjaman berjalan perusahaan/badan usaha di bank/lembaga lain: [Plafon + (Plafon &times; Bunga &times; Tenor)] &divide; (Tenor &times; 12 Bulan)</p>
                        </div>
                        <button type="button" onclick="tambahUtang('', 0, 12, 1);" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg shadow-2xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Tambah Hutang</span>
                        </button>
                    </div>

                    <!-- Container List Utang Dinamis -->
                    <div id="containerUtangDinamis" class="space-y-2 mb-2.5">
                        <!-- Diisi oleh renderListUtang() -->
                    </div>

                    <!-- Subtotal Cicilan Bulanan Hutang -->
                    <div class="p-2.5 bg-rose-50/60 border border-rose-200/70 rounded-xl flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Subtotal Cicilan Bulanan Hutang:</span>
                        <span id="nomTotalCicilanUtangDinamis" class="font-bold text-rose-700 text-xs sm:text-sm">Rp 0</span>
                    </div>
                </div>

                <!-- Ringkasan Terpisah: Total Seluruh Pengeluaran -->
                <div class="p-3.5 bg-white border border-rose-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                    <div>
                        <span class="font-bold text-rose-900 text-xs uppercase tracking-wider block">Total Seluruh Pengeluaran:</span>
                        <span class="text-[10px] text-slate-400 font-medium">* Akumulasi Subtotal Beban Pengeluaran + Subtotal HPP + Subtotal Cicilan Bulanan Hutang</span>
                    </div>
                    <span id="nomTotalPengeluaranBU" class="text-sm sm:text-base font-black text-rose-700">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KOTAK 3: BELANJA / PEMBELIAN ASET USAHA (AKSEN HIJAU)    -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-emerald-50/20 rounded-2xl border border-emerald-200/90 shadow-2xs hover:border-emerald-300 transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-emerald-100 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        3
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>BELANJA / PEMBELIAN ASET USAHA</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Investasi / Capex</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian pembelian aset tetap, mesin produksi, kendaraan, atau peralatan kantor</span>
                    </div>
                </div>
                <button type="button" onclick="tambahAsetOnly('', 0);" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-300 rounded-xl shadow-2xs transition cursor-pointer self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Aset</span>
                </button>
            </div>

            <!-- Container List Aset Dinamis -->
            <div id="containerAsetOnlyDinamis" class="space-y-2 mb-3">
                <!-- Diisi oleh renderListHpp() -->
            </div>

            <!-- Bar Ringkasan Total Aset Kotak 3 -->
            <div class="p-3 bg-white border border-emerald-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                <div>
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pembelian Aset:</span>
                    <p class="text-[10px] text-slate-400">* Pembelian aset dicatat sebagai belanja modal (Capex) dan tidak memotong Laba Kotor</p>
                </div>
                <span id="nomTotalAsetDinamis" class="text-sm font-black text-emerald-700">Rp 0</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- BANNER TOTAL: LABA OPERASIONAL USAHA (EBITDA)            -->
        <!-- ======================================================== -->
        <div class="p-4 sm:p-5 bg-blue-700 bg-gradient-to-r from-blue-700 to-indigo-700 text-white rounded-2xl flex items-center justify-between gap-4 shadow-sm" style="background-color: #1d4ed8 !important; background-image: linear-gradient(135deg, #1d4ed8, #2563eb) !important; color: #ffffff !important;">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-100 truncate">Laba Operasional Usaha (EBITDA)</div>
                    <div class="text-xs sm:text-sm font-extrabold text-white truncate">Pendapatan dikurangi HPP, Beban &amp; Cicilan Utang Berjalan</div> (feat: overhaul Rekening Koran direct upload without popup, add Daftar Hutang and live DSCR, update Tab 1 & Tab 6 metadata, and optimize Reviewer & CEO workflow)
                </div>
            </div>
            <div class="text-right shrink-0">
                <span id="nettRK" class="text-xl sm:text-2xl font-black text-white tracking-tight whitespace-nowrap block" style="color: #ffffff !important;">Rp 0</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- BANNER METRIK UTAMA: RASIO DSCR (DEBT SERVICE COVERAGE)  -->
        <!-- ======================================================== -->
        <div id="metric_lap_dscr_tab9" class="p-4 sm:p-5 bg-slate-900 text-white rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-md border border-slate-800">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 border border-blue-400/30">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-300">Kapasitas Bayar: Rasio DSCR</span>
                        <span id="badgeStatusDSCRK" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Sangat Aman (≥ 1.30x)
                        </span>
                    </div>
                    <div class="text-xs text-slate-300 mt-1">
                        Surplus Bersih (<span id="dscrNumeratorLabel" class="font-semibold text-white">Rp 0</span>) &divide; Angsuran Baru yang Diajukan (<span id="dscrDenominatorLabel" class="font-semibold text-white">Rp 0</span>)
                    </div>
                </div>
            </div>
            <div class="text-left sm:text-right shrink-0">
                <span id="labelRasioDSCRK" class="text-2xl sm:text-3xl font-black text-emerald-400 tracking-tight whitespace-nowrap block">0.00x</span>
                <span class="text-[10px] text-slate-400 block">* Standar perbankan &amp; lembaga pembiayaan: DSCR ≥ 1.30x</span>
            </div>
        </div>

        <!-- Hidden inputs untuk database dan kompatibilitas sistem -->
        <input type="hidden" name="pendapatan_rk" id="pendapatan_rk" value="<?= esc($nomPendapatanBU) ?>">
        <input type="hidden" name="detail_pendapatan_rk" id="detail_pendapatan_rk" value="<?= esc(!empty($listDetailPendapatanBU) ? json_encode($listDetailPendapatanBU) : '[]') ?>">
        <input type="hidden" name="hpp_persen_rk" id="hpp_persen_rk" value="<?= esc($persenHppBU) ?>">
        <input type="hidden" name="hpp_nominal_rk" id="nomHppRKInput" value="<?= esc($nomHppAwalBU) ?>">
        <input type="hidden" name="detail_hpp_rk" id="detail_hpp_rk" value="<?= esc(!empty($listDetailHppBU) ? json_encode($listDetailHppBU) : '[]') ?>">
        <input type="hidden" name="total_aset_hpp_rk" id="total_aset_hpp_rk" value="0">
        <input type="hidden" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" value="<?= esc($nomBebanOperasional) ?>">
        <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 0) ?>">
        <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
        <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
        <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 0) ?>">
        <input type="hidden" name="detail_utang_json" id="detail_utang_json" value="<?= esc(!empty($listDetailUtang) ? json_encode($listDetailUtang) : '[]') ?>">
        <input type="hidden" name="dsc_rasio_rk" id="dsc_rasio_rk" value="0">
        <input type="hidden" name="rasio_dsc_dscr" id="rasio_dsc_dscr" value="0">

        <span id="nomHppRK" class="hidden">Rp 0</span>
        <span id="nomBiayaHidupRK" class="hidden">Rp 0</span>
        <input type="hidden" name="ada_bukti_pendapatan_lain_rk" id="ada_bukti_pendapatan_lain_rk" value="0">
        <input type="hidden" name="pendapatan_lain_rk" id="pendapatan_lain_rk" value="0">
        <input type="hidden" name="bukti_pendapatan_lain_rk" id="bukti_pendapatan_lain_rk" value="">
        <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">
        <span id="labelTotalPendapatanLain" class="hidden">Rp 0</span>
        <span id="totalCashflowBersih" class="hidden">Rp 0</span>
    </div>

    <script>
    window.daftarHpp = <?= !empty($listDetailHppBU) ? json_encode($listDetailHppBU) : '[]' ?>;
    window.daftarUtang = <?= !empty($listDetailUtang) ? json_encode($listDetailUtang) : '[]' ?>;
    if (typeof initDaftarUtang === 'function') {
        initDaftarUtang();
    }
    </script>
</div>
