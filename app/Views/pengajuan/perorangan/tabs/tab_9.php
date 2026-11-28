<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                9
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA DATA KEUANGAN &amp; PENGHASILAN (PERORANGAN / WIRASWASTA)</h3>
                <p class="text-[11px] text-slate-500">Perhitungan data keuangan operasional dari mutasi rekening, penghasilan bulanan, pengeluaran rutin, dan belanja aset</p>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs">
                Perorangan / Karyawan / Wiraswasta
            </span>
        </div>
    </div>

    <?php
        // Cek akumulasi mutasi rekening koran untuk auto-sync jika ada
        $totRekKreditPR = 0;
        $totRekDebitPR  = 0;
        if (!empty($rekening) && is_array($rekening)) {
            foreach ($rekening as $rk) {
                $m = max(1, (int)($rk['jumlah_bulan'] ?? 6));
                $k = (float)($rk['total_mutasi_kredit'] ?? 0);
                $d = (float)($rk['total_mutasi_debit'] ?? 0);
                $totRekKreditPR += round($k / $m);
                $totRekDebitPR  += round($d / $m);
            }
        }
        $autoOmzetRekPR = ($totRekKreditPR > 0) ? $totRekKreditPR : 0;

        // Nominal awal murni dari database (default 0 / kosongan jika belum diisi)
        $nomPendapatanPR = (float)($keuangan['pendapatan_rk'] ?? 0);
        if ($nomPendapatanPR <= 0 && $autoOmzetRekPR > 0) {
            $nomPendapatanPR = $autoOmzetRekPR;
        }

        $persenHppPR = (float)($keuangan['hpp_persen_rk'] ?? 0);
        $nomHppAwalPR = (float)($keuangan['hpp_nominal_rk'] ?? 0);

        // 1. Data Rincian Pendapatan Dinamis (Default Kosongan)
        $rawDetailPendapatanPR = $keuangan['detail_pendapatan_rk'] ?? '';
        $listDetailPendapatanPR = [];
        if (!empty($rawDetailPendapatanPR) && $rawDetailPendapatanPR !== '[]') {
            $decP = json_decode($rawDetailPendapatanPR, true);
            if (is_array($decP) && count($decP) > 0) {
                $listDetailPendapatanPR = $decP;
            }
        }
        if (empty($listDetailPendapatanPR) && $nomPendapatanPR > 0) {
            $listDetailPendapatanPR = [
                ['nama' => '', 'nominal' => $nomPendapatanPR]
            ];
        }

        // 2. Data Rincian HPP & Belanja Aset Dinamis (Default Kosongan)
        $rawDetailHppPR = $keuangan['detail_hpp_rk'] ?? '';
        $listDetailHppPR = [];
        if (!empty($rawDetailHppPR) && $rawDetailHppPR !== '[]') {
            $decHpp = json_decode($rawDetailHppPR, true);
            if (is_array($decHpp) && count($decHpp) > 0) {
                $listDetailHppPR = $decHpp;
            }
        }
        if (empty($listDetailHppPR) && $nomHppAwalPR > 0) {
            $listDetailHppPR = [
                ['nama' => '', 'kategori' => 'hpp', 'nominal' => $nomHppAwalPR]
            ];
        }

        // 3. Data Rincian Pengeluaran Dinamis (Default Kosongan)
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
                    ['nama' => 'Beban Kebutuhan Hidup', 'nominal' => $nomBebanOperasional],
                    ['nama' => 'Pengeluaran Rutin Lainnya', 'nominal' => $nomBebanLama]
                ];
            } elseif ($nomBebanOperasional > 0) {
                $listDetailBeban = [
                    ['nama' => 'Beban Kebutuhan Hidup', 'nominal' => $nomBebanOperasional]
                ];
            } elseif ($nomBebanLama > 0) {
                $listDetailBeban = [
                    ['nama' => 'Pengeluaran Rutin', 'nominal' => $nomBebanLama]
                ];
            }
        }

        // Komponen SLIK & Fasilitas Kredit untuk Kompatibilitas Laporan (Rumus Resmi Komite Kredit)
        $totalOutstandingSlik = 0;
        $totalCicilanSlik = 0;
        $listSlikAutoUtangPR = [];
        if (!empty($slik) && is_array($slik)) {
            foreach ($slik as $s) {
                $out = (float)($s['outstanding'] ?? ($s['baki_debet'] ?? 0));
                $pl  = (float)($s['plafon'] ?? 0);
                $pAcuan = $pl > 0 ? $pl : $out;
                $angs = (float)($s['angsuran'] ?? ($s['angsuran_bulanan'] ?? 0));

                $jwBln = 12;
                if (preg_match('/([0-9]+)/', (string)($s['jangka_waktu'] ?? ''), $jwm)) {
                    $jwBln = (int)$jwm[1];
                }
                if ($jwBln <= 0) $jwBln = 12;

                $bPct = 10;
                if (preg_match('/([0-9]+([.,][0-9]+)?)/', (string)($s['suku_bunga'] ?? ''), $bm)) {
                    $bPct = (float)str_replace(',', '.', $bm[1]);
                }
                if ($bPct <= 0) $bPct = 10;

                // Rumus resmi komite kredit: angsuran_per_bulan = (plafon + (plafon * (bunga_persen / 100) * (tenor_bulan / 12))) / tenor_bulan
                if ($pAcuan > 0) {
                    $angs = (int)round(($pAcuan + ($pAcuan * ($bPct / 100) * ($jwBln / 12))) / $jwBln);
                }

                $totalOutstandingSlik += $out;
                $totalCicilanSlik += $angs;

                if ($pAcuan > 0 || $out > 0) {
                    $listSlikAutoUtangPR[] = [
                        'nama'            => !empty($s['bank_lembaga']) ? "{$s['bank_lembaga']} ({$s['jenis_fasilitas']})" : 'Pinjaman Bank',
                        'plafon'          => $pAcuan,
                        'baki_debet'      => $out,
                        'bunga_persen'    => $bPct,
                        'tenor_tahun'     => round($jwBln / 12, 1),
                        'tenor_bulan'     => $jwBln,
                        'cicilan_bulanan' => $angs,
                        'is_slik'         => 1
                    ];
                }
            }
        }
        // Jika tidak ada fasilitas SLIK berjalan, kewajiban SLIK = 0 (jangan tarik angsuran kredit baru)

        $plafonBaru = (float)($kredit['plafon_pengajuan'] ?? 50000000);
        $tenorBulanBaru = (int)($kredit['tenor_bulan'] ?? 12);
        if ($tenorBulanBaru <= 0) $tenorBulanBaru = 12;
        $bungaTahunPersenPR = (float)($kredit['bunga_per_tahun'] ?? 0);
        $bungaBulanPersen = ($bungaTahunPersenPR > 0) ? ($bungaTahunPersenPR / 12) : (float)($kredit['bunga_per_bulan'] ?? 1.25);
        $metodeBungaPR = strtoupper($kredit['perhitungan_bunga'] ?? 'ANUITAS');
        $skemaBayarPR = strtolower(($kredit['skema_pembayaran'] ?? '') . ' ' . ($kredit['skema_kredit'] ?? ''));
        $rateBlnPR = $bungaBulanPersen / 100;
        if (strpos($skemaBayarPR, 'balloon') !== false) {
            $estimasiAngsuranBaru = ($plafonBaru > 0) ? round($plafonBaru * $rateBlnPR) : 0;
        } elseif ($metodeBungaPR === 'ANUITAS' && $plafonBaru > 0 && $tenorBulanBaru > 0) {
            $estimasiAngsuranBaru = ($rateBlnPR > 0)
                ? round($plafonBaru * ($rateBlnPR / (1 - pow(1 + $rateBlnPR, -$tenorBulanBaru))))
                : round($plafonBaru / $tenorBulanBaru);
        } else {
            $angsuranPokokBaru = ($plafonBaru > 0) ? ($plafonBaru / $tenorBulanBaru) : 0;
            $angsuranBungaBaru = ($plafonBaru > 0) ? ($plafonBaru * $rateBlnPR) : 0;
            $estimasiAngsuranBaru = round($angsuranPokokBaru + $angsuranBungaBaru);
        }

        // Data Harta & Utang untuk fallback kompatibilitas laporan
        $rawDetailHarta = $keuangan['detail_harta_json'] ?? '';
        $listDetailHarta = [];
        $listDetailUtang = [];
        if (!empty($rawDetailHarta)) {
            $decH = json_decode($rawDetailHarta, true);
            if (is_array($decH)) {
                if (isset($decH['daftar_harta']) && is_array($decH['daftar_harta'])) {
                    $listDetailHarta = $decH['daftar_harta'];
                } elseif (isset($decH[0]['nama'])) {
                    $listDetailHarta = $decH;
                }
                if (isset($decH['daftar_utang']) && is_array($decH['daftar_utang'])) {
                    $listDetailUtang = $decH['daftar_utang'];
                }
            }
        }
        if (empty($listDetailUtang) && !empty($listSlikAutoUtangPR)) {
            $listDetailUtang = $listSlikAutoUtangPR;
        }

        // Auto-sync HPP dari Mutasi Debit Rekening Koran bila belum ada item HPP yang terisi (> 0)
        $totalBebanAwalPR = 0;
        foreach ($listDetailBeban as $b) {
            $totalBebanAwalPR += (float)($b['nominal'] ?? 0);
        }
        $totalCicilanUtangAwalPR = 0;
        foreach ($listDetailUtang as $u) {
            $totalCicilanUtangAwalPR += (float)($u['cicilan_bulanan'] ?? ($u['angsuran'] ?? 0));
        }
        $hasHppItemPositifPR = false;
        foreach ($listDetailHppPR as $itH) {
            if (strtolower((string)($itH['kategori'] ?? 'hpp')) !== 'aset' && (float)($itH['nominal'] ?? 0) > 0) {
                $hasHppItemPositifPR = true;
                break;
            }
        }
        if (!$hasHppItemPositifPR && $nomHppAwalPR <= 0 && $totRekDebitPR > 0) {
            $autoHppRekPR = max(0, $totRekDebitPR - $totalBebanAwalPR - $totalCicilanUtangAwalPR);
            if ($autoHppRekPR > 0) {
                $nomHppAwalPR = $autoHppRekPR;
                $persenHppPR = ($nomPendapatanPR > 0) ? round(($nomHppAwalPR / $nomPendapatanPR) * 100, 1) : 0;
                array_unshift($listDetailHppPR, [
                    'nama'       => 'Estimasi HPP (Mutasi Debit Rekening Koran)',
                    'kategori'   => 'hpp',
                    'nominal'    => $nomHppAwalPR,
                    '_syncedRek' => true,
                ]);
            }
        }

        // Prioritas Aset: Ekstrak aset dari Kotak 3 ($listDetailHppPR kategori 'aset')
        $listAsetKotak3PR = [];
        $piutangAsetPR = 0;
        if (!empty($listDetailHppPR)) {
            foreach ($listDetailHppPR as $it) {
                if (($it['kategori'] ?? '') === 'aset') {
                    $nmAset = strtolower($it['nama'] ?? '');
                    $nomAset = (float)($it['nominal'] ?? 0);
                    $listAsetKotak3PR[] = [
                        'nama'    => $it['nama'] ?? 'Aset / Inventaris',
                        'nominal' => $nomAset,
                        'status'  => 'bebas'
                    ];
                    if (strpos($nmAset, 'piutang') !== false || strpos($nmAset, 'tagihan') !== false || strpos($nmAset, 'invoice') !== false) {
                        $piutangAsetPR += $nomAset;
                    }
                }
            }
        }
        if (!empty($listAsetKotak3PR)) {
            $listDetailHarta = $listAsetKotak3PR;
        } else {
            $listDetailHarta = array_values(array_filter($listDetailHarta, function($item) {
                return (float)($item['nominal'] ?? 0) > 0;
            }));
            foreach ($listDetailHarta as $it) {
                $nmAset = strtolower($it['nama'] ?? '');
                if (strpos($nmAset, 'piutang') !== false || strpos($nmAset, 'tagihan') !== false || strpos($nmAset, 'invoice') !== false) {
                    $piutangAsetPR += (float)($it['nominal'] ?? 0);
                }
            }
        }
        if ($piutangAsetPR <= 0 && !empty($keuangan['neraca_piutang_usaha'])) {
            $piutangAsetPR = (float)$keuangan['neraca_piutang_usaha'];
        }
    ?>

    <!-- Layout 3 Kotak Data Keuangan Perorangan (1: Pendapatan [Biru], 2: Pengeluaran [Merah], 3: Aset [Hijau]) -->
    <div class="space-y-6">
        
        <!-- ======================================================== -->
        <!-- KOTAK 1: PENDAPATAN / PENGHASILAN BULANAN (AKSEN BIRU)   -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-blue-50/25 rounded-2xl border border-blue-200/90 shadow-2xs hover:border-blue-300 transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-blue-100 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        1
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>PENDAPATAN / PENGHASILAN BULANAN</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-full">Pemasukan</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian sumber pendapatan operasional atau penghasilan yang dinamis</span>
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
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Total Pendapatan / Penghasilan Bulanan:</span>
                <span id="nomTotalPendapatanDinamis" class="text-sm font-black text-blue-700">Rp <?= number_format($nomPendapatanPR, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KOTAK 2: PENGELUARAN (MERAH)                             -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-rose-50/20 rounded-2xl border border-rose-200/90 shadow-2xs hover:border-rose-300 transition">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-rose-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        2
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>PENGELUARAN</span>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-rose-100 text-rose-700 rounded-full">Arus Keluar</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian pengeluaran kebutuhan hidup, operasional rutin, dan modal bahan/kulakan (HPP) yang dinamis</span>
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <!-- Sub-Bagian 2A: Pengeluaran (Beban Kebutuhan Hidup & Operasional) - DI ATAS -->
                <div class="p-4 bg-white/80 rounded-xl border border-rose-100 shadow-2xs">
                    <div class="flex items-center justify-between mb-2.5">
                        <div>
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                A. Pengeluaran (Beban Kebutuhan Hidup &amp; Operasional)
                            </h4>
                            <p class="text-[10px] text-slate-400">Kebutuhan rumah tangga, operasional rutin, transportasi, listrik/air, logistik, dll</p>
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
                            <p class="text-[10px] text-slate-400">Biaya pokok barang dagang, pembelian stok bahan, atau modal kulakan</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" id="btnSinkronDebitHpp" onclick="sinkronHppDariMutasiDebit();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg shadow-2xs transition cursor-pointer <?= $totRekDebitPR > 0 ? '' : 'hidden' ?>" title="Isi otomatis HPP dari rata-rata mutasi debit Rekening Koran dikurangi beban operasional &amp; cicilan">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Sinkron Mutasi Debit</span>
                            </button>
                            <button type="button" onclick="tambahHppOnly('', 0);" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg shadow-2xs transition cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah HPP</span>
                            </button>
                        </div>
                    </div>

                    <!-- Banner Peringatan / Bantu Sinkron Mutasi Debit ke HPP -->
                    <div id="alertSinkronDebitHpp" class="hidden mb-2.5 p-3 bg-amber-50/90 border border-amber-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div class="flex items-start gap-2 min-w-0">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <span id="textAlertSinkronDebitHpp" class="text-[11px] text-amber-900 font-medium leading-relaxed"></span>
                        </div>
                        <button type="button" onclick="sinkronHppDariMutasiDebit();" class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-[11px] font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-lg shadow-2xs transition cursor-pointer shrink-0">
                            <span>Terapkan ke HPP</span>
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
                            <span id="nomTotalHppDinamis" class="font-bold text-rose-700 text-xs sm:text-sm">Rp <?= number_format($nomHppAwalPR, 0, ',', '.') ?></span>
                            <span id="badgePersenHpp" class="text-[11px] text-slate-500 font-medium">(<?= esc($persenHppPR) ?>% dari Omzet)</span>
                        </div>
                    </div>

                    <!-- Laba Kotor Usaha (Gross Profit): Tepat di bawah Subtotal HPP -->
                    <div class="p-3 bg-blue-50/70 border border-blue-200/80 rounded-xl flex justify-between items-center text-xs shadow-2xs mt-2.5">
                        <div>
                            <span class="font-bold text-blue-900 text-[11px] uppercase tracking-wider block">Laba Kotor Usaha (Gross Profit):</span>
                            <span class="text-[10px] text-blue-600 font-medium">* Total Pendapatan / Penghasilan dikurangi Subtotal HPP</span>
                        </div>
                        <span id="grossRK" class="text-sm font-bold text-blue-700">Rp 0</span>
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
                            <p class="text-[10px] text-slate-400">Cicilan pinjaman berjalan di bank/lembaga lain: [Plafon + (Plafon &times; Bunga &times; Tenor)] &divide; (Tenor &times; 12 Bulan)</p>
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
                        <span class="font-bold text-slate-700 text-[11px] uppercase tracking-wider block">Total Seluruh Pengeluaran:</span>
                        <span class="text-[10px] text-slate-400 font-medium">* Akumulasi Subtotal Beban Pengeluaran + Subtotal HPP + Subtotal Cicilan Bulanan Hutang</span>
                    </div>
                    <span id="nomTotalPengeluaranBU" class="text-sm font-bold text-rose-700">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- KOTAK 3: BELANJA / PEMBELIAN ASET (AKSEN HIJAU)          -->
        <!-- ======================================================== -->
        <div class="p-5 sm:p-6 bg-emerald-50/20 rounded-2xl border border-emerald-200/90 shadow-2xs hover:border-emerald-300 transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3.5 mb-4 border-b border-emerald-100 gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                        3
                    </div>
                    <div>
                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>ASET</span>
                        </h3>
                        <span class="text-[11px] text-slate-400 font-medium">Rincian pembelian aset usaha, kendaraan, inventaris kantor/rumah, atau barang modal</span>
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
                <span id="nomTotalAsetDinamis" class="text-sm font-bold text-emerald-700">Rp 0</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- BANNER TOTAL: SISA PENGHASILAN BERSIH (SURPLUS / CASHFLOW)-->
        <!-- ======================================================== -->
        <div class="p-4 sm:p-5 bg-blue-700 bg-gradient-to-r from-blue-700 to-indigo-700 text-white rounded-2xl flex items-center justify-between gap-4 shadow-sm" style="background-color: #1d4ed8 !important; background-image: linear-gradient(135deg, #1d4ed8, #2563eb) !important; color: #ffffff !important;">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-100 truncate">Sisa Penghasilan Bersih (Surplus / Net Cashflow)</div>
                    <div class="text-xs sm:text-sm font-bold text-white truncate">Pendapatan dikurangi HPP, Beban &amp; Cicilan Utang Berjalan</div>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span id="nettRK" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block" style="color: #ffffff !important;">Rp 0</span>
            </div>
        </div>

        <!-- Hidden inputs untuk database dan kompatibilitas sistem -->
        <input type="hidden" name="pendapatan_rk" id="pendapatan_rk" value="<?= esc($nomPendapatanPR) ?>">
        <input type="hidden" name="detail_pendapatan_rk" id="detail_pendapatan_rk" value="<?= esc(!empty($listDetailPendapatanPR) ? json_encode($listDetailPendapatanPR) : '[]') ?>">
        <input type="hidden" name="hpp_persen_rk" id="hpp_persen_rk" value="<?= esc($persenHppPR) ?>">
        <input type="hidden" name="hpp_nominal_rk" id="nomHppRKInput" value="<?= esc($nomHppAwalPR) ?>">
        <input type="hidden" name="detail_hpp_rk" id="detail_hpp_rk" value="<?= esc(!empty($listDetailHppPR) ? json_encode($listDetailHppPR) : '[]') ?>">
        <input type="hidden" name="total_aset_hpp_rk" id="total_aset_hpp_rk" value="0">
        <input type="hidden" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" value="<?= esc($nomBebanOperasional) ?>">
        <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 0) ?>">
        <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
        <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
        <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 0) ?>">
        <input type="hidden" name="detail_harta_json" id="detail_harta_json" value="<?= esc(!empty($rawDetailHarta) ? $rawDetailHarta : json_encode(['daftar_harta' => $listDetailHarta, 'daftar_utang' => $listDetailUtang])) ?>">
        <input type="hidden" name="detail_utang_json" id="detail_utang_json" value="<?= esc(!empty($listDetailUtang) ? json_encode($listDetailUtang) : '[]') ?>">
        <input type="hidden" name="dsc_rasio_rk" id="dsc_rasio_rk" value="0">
        <input type="hidden" name="rasio_dsc_dscr" id="rasio_dsc_dscr" value="0">
        <input type="hidden" name="piutang_aset" id="piutang_aset" value="<?= esc($piutangAsetPR) ?>">
        <input type="hidden" name="neraca_piutang_usaha" id="neraca_piutang_usaha" value="<?= esc($piutangAsetPR) ?>">
        <input type="hidden" name="total_baki_debet_slik" id="total_baki_debet_slik" value="<?= esc($totalOutstandingSlik) ?>">
        <input type="hidden" name="cicilan_slik_per_bulan" id="cicilan_slik_per_bulan" value="<?= esc($totalCicilanSlik) ?>">

        <span id="nomHppRK" class="hidden">Rp 0</span>
        <span id="nomBiayaHidupRK" class="hidden">Rp 0</span>
        <input type="hidden" name="ada_bukti_pendapatan_lain_rk" id="ada_bukti_pendapatan_lain_rk" value="0">
        <input type="hidden" name="pendapatan_lain_rk" id="pendapatan_lain_rk" value="0">
        <input type="hidden" name="bukti_pendapatan_lain_rk" id="bukti_pendapatan_lain_rk" value="">
        <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">
        <span id="labelTotalPendapatanLain" class="hidden">Rp 0</span>
        <span id="totalCashflowBersih" class="hidden">Rp 0</span>
        <span id="nomTotalPengeluaranPR" class="hidden">Rp 0</span>
        <span id="nomTotalPenghasilanPR" class="hidden">Rp 0</span>
        <span id="nomSurplusPR" class="hidden">Rp 0</span>
    </div>

    <script>
    window.TOTAL_BAKI_DEBET_SLIK = <?= (float)$totalOutstandingSlik ?>;
    window.OUTSTANDING_SLIK_DEFAULT = <?= (float)$totalOutstandingSlik ?>;
    window.CICILAN_SLIK_PER_BULAN = <?= (float)$totalCicilanSlik ?>;
    window.ANGSURAN_SLIK_DEFAULT = <?= (float)$totalCicilanSlik ?>;
    window.PIUTANG_ASET = <?= (float)$piutangAsetPR ?>;
    window.ESTIMASI_ANGSURAN_BARU = <?= (float)$estimasiAngsuranBaru ?>;
    window.TOTAL_AVG_KREDIT_REK = <?= (float)$totRekKreditPR ?>;
    window.TOTAL_AVG_DEBIT_REK = <?= (float)$totRekDebitPR ?>;
    window.daftarHpp = <?= !empty($listDetailHppPR) ? json_encode($listDetailHppPR) : '[]' ?>;
    window.daftarHarta = <?= !empty($listDetailHarta) ? json_encode($listDetailHarta) : '[]' ?>;
    window.daftarUtang = <?= !empty($listDetailUtang) ? json_encode($listDetailUtang) : '[]' ?>;
    if (typeof initDaftarUtang === 'function') {
        initDaftarUtang();
    }
    </script>
</div>
