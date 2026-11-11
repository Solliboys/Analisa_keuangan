<?php

namespace App\Services;

/**
 * LaporanKeuanganService — Kalkulasi Estimasi Laporan Keuangan
 *
 * Mendukung dua tipe pemohon:
 *   'badan_usaha'            → Laba Rugi + Arus Kas + Neraca
 *   'karyawan' / 'perorangan'→ Laba Rugi sederhana (Penghasilan - Pengeluaran - Cicilan = Surplus) + Arus Kas
 */
class LaporanKeuanganService
{
    /**
     * Hitung Estimasi Laporan Keuangan
     *
     * @param array  $input        Data input (dari form + DB)
     * @param string $tipePemohon  'badan_usaha' | 'karyawan' | 'perorangan'
     * @return array
     */
    public function hitungLaporan(array $input, string $tipePemohon = 'badan_usaha'): array
    {
        $tipe = strtolower(trim($tipePemohon));
        $isBadanUsaha = ($tipe === 'badan_usaha' || stripos($tipe, 'badan') !== false || stripos($tipe, 'pt') !== false || stripos($tipe, 'cv') !== false);
        $normTipe = $isBadanUsaha ? 'badan_usaha' : 'karyawan';

        if (!$isBadanUsaha) {
            return $this->hitungLaporanPerorangan($input);
        }

        return $this->hitungLaporanBadanUsaha($input);
    }

    /**
     * Hitung Laporan untuk Pemohon Badan Usaha
     */
    private function hitungLaporanBadanUsaha(array $input): array
    {
        // 1. LABA RUGI
        $pendapatan = $this->cleanNumber($input['pendapatan'] ?? $input['pendapatan_rk'] ?? 0);

        // HPP
        $hppPct = (float)($input['hpp_persen'] ?? $input['hpp_persen_rk'] ?? 60);
        $hppNom = isset($input['hpp']) && $input['hpp'] !== ''
            ? $this->cleanNumber($input['hpp'])
            : $this->cleanNumber($input['hpp_nominal_rk'] ?? 0);
        if ($hppNom == 0 && $pendapatan > 0 && $hppPct > 0) {
            $hppNom = round($pendapatan * ($hppPct / 100), 2);
        }
        $labaKotor = max(0, $pendapatan - $hppNom);

        // Beban Gaji Karyawan
        $bebanGaji = $this->cleanNumber($input['beban_gaji'] ?? 0);
        if ($bebanGaji == 0 && !empty($input['daftar_karyawan'])) {
            $karyawanList = is_array($input['daftar_karyawan'])
                ? $input['daftar_karyawan']
                : json_decode($input['daftar_karyawan'], true);
            if (is_array($karyawanList)) {
                foreach ($karyawanList as $k) {
                    $bebanGaji += $this->cleanNumber($k['gaji'] ?? $k['nominal_gaji'] ?? 0);
                }
            }
        }

        // Sewa, utilitas, pemasaran (beban operasional pokok)
        $bebanSewa = $this->cleanNumber($input['beban_sewa'] ?? 0);
        $bebanOperasional = $this->cleanNumber($input['beban_operasional'] ?? $input['biaya_hidup_nominal_rk'] ?? 0);
        if ($bebanSewa > 0 && $bebanOperasional == 0) {
            $bebanOperasional = $bebanSewa;
        }

        $bebanLainnya = $this->cleanNumber($input['beban_lainnya'] ?? $input['beban_lainnya_nominal_rk'] ?? 0);

        // Penyusutan Aset Tetap
        $hargaPerolehanAset = $this->cleanNumber($input['neraca_aset_tetap_bruto'] ?? $input['harga_perolehan_aset_tetap'] ?? $input['harga_perolehan'] ?? 0);
        $umurEkonomis       = max(1, (int)($input['input_capex_umur_ekonomis'] ?? $input['umur_ekonomis_tahun'] ?? 5));
        
        $penyusutanInput = isset($input['beban_depresiasi']) && $input['beban_depresiasi'] !== ''
            ? $this->cleanNumber($input['beban_depresiasi'])
            : (isset($input['beban_penyusutan']) && $input['beban_penyusutan'] !== ''
                ? $this->cleanNumber($input['beban_penyusutan'])
                : (isset($input['penyusutan']) && $input['penyusutan'] !== '' ? $this->cleanNumber($input['penyusutan']) : null));

        $penyusutanTahunan = ($penyusutanInput !== null)
            ? $penyusutanInput
            : ($hargaPerolehanAset > 0 ? round($hargaPerolehanAset / $umurEkonomis, 2) : 0);

        $totalBebanOp = $bebanGaji + $bebanOperasional + $bebanLainnya + $penyusutanTahunan;
        $labaOperasional = $labaKotor - $totalBebanOp;

        // Badan Usaha: TIDAK ADA pendapatan lain-lain
        $pendapatanLain = 0;

        $bebanBunga = $this->cleanNumber($input['input_beban_bunga'] ?? $input['beban_bunga'] ?? 0);
        $labaSebelumPjk = $labaOperasional + $pendapatanLain - $bebanBunga;

        // Pajak
        if (isset($input['input_beban_pajak']) && $input['input_beban_pajak'] !== '') {
            $pajakNom = $this->cleanNumber($input['input_beban_pajak']);
        } elseif (isset($input['pajak']) && $input['pajak'] !== '') {
            $pajakNom = $this->cleanNumber($input['pajak']);
        } else {
            $pajakNom = round(max(0, $labaSebelumPjk * 0.05), 2);
        }

        $labaBersih = $labaSebelumPjk - $pajakNom;

        $labaRugi = [
            'pendapatan'         => $pendapatan,
            'hpp'                => $hppNom,
            'hpp_persen'         => $hppPct,
            'laba_kotor'         => $labaKotor,
            'beban_gaji'         => $bebanGaji,
            'beban_sewa'         => $bebanSewa,
            'beban_operasional'  => $bebanOperasional,
            'beban_lainnya'      => $bebanLainnya,
            'beban_penyusutan'   => $penyusutanTahunan,
            'total_beban_op'     => $totalBebanOp,
            'laba_operasional'   => $labaOperasional,
            'pendapatan_lain'    => 0,
            'beban_bunga'        => $bebanBunga,
            'laba_sebelum_pajak' => $labaSebelumPjk,
            'pajak'              => $pajakNom,
            'laba_bersih'        => $labaBersih,
        ];

        // 2. ASUMSI & SALDO AWAL
        $dso = max(1, (int)($input['neraca_hari_piutang_dso'] ?? $input['asumsi_dso'] ?? 30));
        $dio = max(1, (int)($input['neraca_hari_stok_dio'] ?? $input['asumsi_dio'] ?? 45));
        $dpo = max(1, (int)($input['neraca_hari_utang_dpo'] ?? $input['asumsi_dpo'] ?? 30));

        $saldoAwalKas        = $this->cleanNumber($input['neraca_awal_kas'] ?? $input['saldo_awal_kas'] ?? 0);
        $saldoAwalPiutang    = $this->cleanNumber($input['neraca_awal_piutang'] ?? $input['saldo_awal_piutang'] ?? 0);
        $saldoAwalPersediaan = $this->cleanNumber($input['neraca_awal_persediaan'] ?? $input['saldo_awal_persediaan'] ?? 0);
        $saldoAwalUtangUsaha = $this->cleanNumber($input['neraca_awal_utang_usaha'] ?? $input['saldo_awal_utang_usaha'] ?? 0);
        $saldoAwalAsetTetap  = $this->cleanNumber($input['neraca_awal_aset_tetap'] ?? $input['saldo_awal_aset_tetap'] ?? 0);
        $saldoAwalUtangBank  = $this->cleanNumber($input['neraca_awal_utang_bank'] ?? $input['saldo_awal_utang_bank'] ?? 0);
        $saldoAwalModal      = $this->cleanNumber($input['neraca_awal_modal_disetor'] ?? $input['saldo_awal_modal'] ?? ($input['modal_disetor'] ?? 0));
        $saldoAwalLabaDitahan= $this->cleanNumber($input['neraca_awal_laba_ditahan'] ?? $input['laba_ditahan_awal'] ?? 0);

        // Piutang Akhir
        $piutangInput = isset($input['input_piutang_akhir']) && $input['input_piutang_akhir'] !== ''
            ? $input['input_piutang_akhir']
            : (isset($input['piutang_usaha']) && $input['piutang_usaha'] !== '' ? $input['piutang_usaha'] : null);
        $piutangAkhir = ($piutangInput !== null)
            ? $this->cleanNumber($piutangInput)
            : round(($dso / 365) * $pendapatan, 2);
        $piutangSumber = ($piutangInput !== null) ? 'input' : 'estimasi';

        // Persediaan Akhir
        $persediaanInput = isset($input['input_persediaan_akhir']) && $input['input_persediaan_akhir'] !== ''
            ? $input['input_persediaan_akhir']
            : (isset($input['persediaan']) && $input['persediaan'] !== '' ? $input['persediaan'] : null);
        $persediaanAkhir = ($persediaanInput !== null)
            ? $this->cleanNumber($persediaanInput)
            : round(($dio / 365) * $hppNom, 2);
        $persediaanSumber = ($persediaanInput !== null) ? 'input' : 'estimasi';

        // Utang Usaha Akhir
        $utangUsahaInput = isset($input['input_utang_usaha_akhir']) && $input['input_utang_usaha_akhir'] !== ''
            ? $input['input_utang_usaha_akhir']
            : (isset($input['utang_usaha']) && $input['utang_usaha'] !== '' ? $input['utang_usaha'] : null);
        $utangUsahaAkhir = ($utangUsahaInput !== null)
            ? $this->cleanNumber($utangUsahaInput)
            : round(($dpo / 365) * $hppNom, 2);
        $utangUsahaSumber = ($utangUsahaInput !== null) ? 'input' : 'estimasi';

        // Pembelian Aset Tetap (CAPEX)
        $pembelianAsetTetap = $this->cleanNumber($input['input_capex'] ?? $input['pembelian_aset_tetap'] ?? 0);

        // Total Aset Tetap Bruto Akhir
        if ($hargaPerolehanAset > 0) {
            $hargaPerolehanAkhir = $hargaPerolehanAset;
        } else {
            $hargaPerolehanAkhir = $saldoAwalAsetTetap + $pembelianAsetTetap;
        }

        // Akumulasi Penyusutan
        $akumulasiPenyusutan = isset($input['neraca_akumulasi_penyusutan']) && $input['neraca_akumulasi_penyusutan'] !== ''
            ? $this->cleanNumber($input['neraca_akumulasi_penyusutan'])
            : (isset($input['akumulasi_penyusutan']) && $input['akumulasi_penyusutan'] !== ''
                ? $this->cleanNumber($input['akumulasi_penyusutan'])
                : $penyusutanTahunan);

        $asetTetapNeto = max(0, $hargaPerolehanAkhir - $akumulasiPenyusutan);

        // 3. ARUS KAS (METODE TIDAK LANGSUNG)
        $kenaikanPiutang    = $piutangAkhir - $saldoAwalPiutang;
        $kenaikanPersediaan = $persediaanAkhir - $saldoAwalPersediaan;
        $kenaikanUtangUsaha = $utangUsahaAkhir - $saldoAwalUtangUsaha;

        $kasOperasi = $labaBersih + $penyusutanTahunan
                      - $kenaikanPiutang
                      - $kenaikanPersediaan
                      + $kenaikanUtangUsaha;

        $kasInvestasi = -$pembelianAsetTetap;

        $modalDisetor  = $this->cleanNumber($input['neraca_modal_disetor'] ?? $input['modal_disetor'] ?? 0);
        $saldoAwalModal = isset($input['neraca_awal_modal_disetor']) || isset($input['saldo_awal_modal'])
            ? $this->cleanNumber($input['neraca_awal_modal_disetor'] ?? $input['saldo_awal_modal'])
            : $modalDisetor;
        $tambahanModal = isset($input['input_setoran_modal']) && $input['input_setoran_modal'] !== ''
            ? $this->cleanNumber($input['input_setoran_modal'])
            : max(0, $modalDisetor - $saldoAwalModal);

        $pinjamanBaru = $this->cleanNumber($input['input_pinjaman_baru'] ?? 0);
        $cicilanPokok = $this->cleanNumber($input['input_pembayaran_pokok'] ?? $input['cicilan_pokok'] ?? 0);
        $priveDividen = $this->cleanNumber($input['input_prive_dividen'] ?? $input['prive_dividen'] ?? 0);

        $kasPendanaan = $tambahanModal + $pinjamanBaru - $cicilanPokok - $priveDividen;

        $kenaikanBersihKas = $kasOperasi + $kasInvestasi + $kasPendanaan;
        $kasAkhirArusKas   = $saldoAwalKas + $kenaikanBersihKas;

        $arusKas = [
            'laba_bersih'          => $labaBersih,
            'penyusutan'           => $penyusutanTahunan,
            'kenaikan_piutang'     => $kenaikanPiutang,
            'kenaikan_persediaan'  => $kenaikanPersediaan,
            'kenaikan_utang_usaha' => $kenaikanUtangUsaha,
            'kas_operasional'      => $kasOperasi,
            'pembelian_aset_tetap' => $pembelianAsetTetap,
            'kas_investasi'        => $kasInvestasi,
            'tambahan_modal'       => $tambahanModal,
            'pinjaman_bank_baru'   => $pinjamanBaru,
            'cicilan_pokok'        => $cicilanPokok,
            'prive_dividen'        => $priveDividen,
            'kas_pendanaan'        => $kasPendanaan,
            'saldo_awal_kas'       => $saldoAwalKas,
            'kenaikan_bersih_kas'  => $kenaikanBersihKas,
            'kas_akhir'            => $kasAkhirArusKas,
        ];

        // 4. NERACA
        // KAS DI NERACA = kas akhir arus kas
        $kasBankNeraca   = $kasAkhirArusKas;
        $asetLancarLain  = $this->cleanNumber($input['aset_lancar_lain'] ?? 0);
        $totalAsetLancar = $kasBankNeraca + $piutangAkhir + $persediaanAkhir + $asetLancarLain;
        $totalAset       = $totalAsetLancar + $asetTetapNeto;

        // Utang Bank Akhir
        $utangBankLancar = $this->cleanNumber($input['neraca_utang_bank_lancar'] ?? $input['utang_bank_pendek'] ?? 0);
        $utangBankPjg    = $this->cleanNumber($input['neraca_utang_bank_panjang'] ?? $input['utang_jangka_panjang'] ?? 0);
        $totalUtangBank  = $utangBankLancar + $utangBankPjg;

        // Jika utang bank kosong tapi ada saldo awal dan pinjaman/cicilan
        if ($totalUtangBank == 0 && ($saldoAwalUtangBank > 0 || $pinjamanBaru > 0 || $cicilanPokok > 0)) {
            $totalUtangBank = max(0, $saldoAwalUtangBank + $pinjamanBaru - $cicilanPokok);
            $utangBankPjg   = $totalUtangBank;
        }

        $totalLiabilitas = $utangUsahaAkhir + $totalUtangBank;

        // Ekuitas: modal disetor + laba ditahan akhir (laba ditahan awal + laba bersih - prive)
        $labaDitahanAkhir = $saldoAwalLabaDitahan + $labaBersih - $priveDividen;
        $totalEkuitas     = $modalDisetor + $labaDitahanAkhir;
        $totalLiabEkuitas = $totalLiabilitas + $totalEkuitas;

        // Validasi
        $selisihNeraca = round($totalAset - $totalLiabEkuitas, 2);
        $statusNeraca  = (abs($selisihNeraca) < 1.0) ? 'Seimbang' : 'Tidak Seimbang';
        $selisihKas    = round($kasBankNeraca - $kasAkhirArusKas, 2);
        $statusKas     = (abs($selisihKas) < 1.0) ? 'Cocok' : 'Selisih';

        // Rasio ringkas (HANYA margin laba bersih, current ratio, debt to equity. TIDAK ADA DSCR)
        $marginLabaBersih = $pendapatan > 0 ? round(($labaBersih / $pendapatan) * 100, 2) : 0;
        $currentRatio     = $totalLiabilitas > 0 ? round($totalAsetLancar / $totalLiabilitas, 2) : null;
        $debtToEquity     = $totalEkuitas > 0 ? round($totalLiabilitas / $totalEkuitas, 2) : null;

        $neraca = [
            'kas_bank'                   => $kasBankNeraca,
            'piutang_usaha'              => $piutangAkhir,
            'piutang_sumber'             => $piutangSumber,
            'persediaan'                 => $persediaanAkhir,
            'persediaan_sumber'          => $persediaanSumber,
            'aset_lancar_lain'           => $asetLancarLain,
            'total_aset_lancar'          => $totalAsetLancar,
            'harga_perolehan_aset_tetap' => $hargaPerolehanAkhir,
            'akumulasi_penyusutan'       => $akumulasiPenyusutan,
            'umur_ekonomis_tahun'        => $umurEkonomis,
            'penyusutan_tahunan'         => $penyusutanTahunan,
            'aset_tetap_neto'            => $asetTetapNeto,
            'total_aset'                 => $totalAset,
            'utang_usaha'                => $utangUsahaAkhir,
            'utang_usaha_sumber'         => $utangUsahaSumber,
            'utang_bank_pendek'          => $utangBankLancar,
            'utang_jangka_panjang'       => $utangBankPjg,
            'total_liabilitas'           => $totalLiabilitas,
            'modal_disetor'              => $modalDisetor,
            'laba_ditahan_awal'          => $saldoAwalLabaDitahan,
            'laba_bersih_periode'        => $labaBersih,
            'prive_dividen'              => $priveDividen,
            'laba_ditahan_akhir'         => $labaDitahanAkhir,
            'total_ekuitas'              => $totalEkuitas,
            'total_liabilitas_ekuitas'   => $totalLiabEkuitas,
            'status_neraca'              => $statusNeraca,
            'selisih_neraca'             => $selisihNeraca,
            'status_kas'                 => $statusKas,
            'selisih_kas'                => $selisihKas,
            'margin_laba_bersih'         => $marginLabaBersih,
            'current_ratio'              => $currentRatio,
            'debt_to_equity'             => $debtToEquity,
        ];

        return [
            'tipe_pemohon' => 'badan_usaha',
            'laba_rugi'    => $labaRugi,
            'arus_kas'     => $arusKas,
            'neraca'       => $neraca,
            'asumsi'       => [
                'dso'                    => $dso,
                'dio'                    => $dio,
                'dpo'                    => $dpo,
                'saldo_awal_piutang'     => $saldoAwalPiutang,
                'saldo_awal_persediaan'  => $saldoAwalPersediaan,
                'saldo_awal_utang_usaha' => $saldoAwalUtangUsaha,
                'saldo_awal_aset_tetap'  => $saldoAwalAsetTetap,
                'saldo_awal_kas'         => $saldoAwalKas,
            ],
            'validasi' => [
                'status_neraca'  => $statusNeraca,
                'selisih_neraca' => $selisihNeraca,
                'status_kas'     => $statusKas,
                'selisih_kas'    => $selisihKas,
                'is_seimbang'    => (abs($selisihNeraca) < 1.0 && abs($selisihKas) < 1.0),
            ],
        ];
    }

    /**
     * Hitung Laporan untuk Pemohon Perorangan / Karyawan
     * Laba Rugi: Total Penghasilan - Pengeluaran - Cicilan = Surplus
     * Arus Kas: Penghasilan Masuk, Pengeluaran Keluar, Arus Kas Bersih
     * Tanpa Neraca
     */
    private function hitungLaporanPerorangan(array $input): array
    {
        // Rincian pendapatan dinamis (Gaji pokok, bonus, lembur, dll)
        $detailPendapatan = [];
        $totalPendapatanDinamis = 0;
        if (!empty($input['detail_pendapatan_rk'])) {
            $rawDetailP = is_array($input['detail_pendapatan_rk'])
                ? $input['detail_pendapatan_rk']
                : json_decode($input['detail_pendapatan_rk'], true);
            if (is_array($rawDetailP)) {
                $detailPendapatan = $rawDetailP;
                foreach ($rawDetailP as $dp) {
                    $totalPendapatanDinamis += $this->cleanNumber($dp['nominal'] ?? 0);
                }
            }
        }

        // Penghasilan utama (gaji pokok, tunjangan, bonus)
        if ($totalPendapatanDinamis > 0) {
            $penghasilanUtama = $totalPendapatanDinamis;
        } else {
            $penghasilanUtama = $this->cleanNumber($input['penghasilan_utama'] ?? $input['pendapatan'] ?? $input['pendapatan_rk'] ?? $input['pendapatan_iv'] ?? 0);
        }

        // Pendapatan lain-lain (hanya dipakai di tipe perorangan/karyawan, misal usaha sampingan)
        $pendapatanLain   = $this->cleanNumber($input['pendapatan_lain'] ?? $input['pendapatan_lain_rk'] ?? 0);
        $totalPenghasilan = $penghasilanUtama + $pendapatanLain;

        // Pengeluaran kebutuhan hidup / rumah tangga
        $pengeluaranHidup = $this->cleanNumber($input['input_pengeluaran_hidup'] ?? $input['pengeluaran_rumah_tangga'] ?? $input['beban_operasional'] ?? $input['biaya_hidup_nominal_rk'] ?? $input['biaya_hidup_nominal_iv'] ?? 0);

        // Beban lainnya bebas (rincian dinamis: asuransi, biaya tak terduga, dll)
        $bebanLainnya = $this->cleanNumber($input['beban_lainnya'] ?? $input['beban_lainnya_nominal_rk'] ?? 0);
        $detailBeban = [];
        if (!empty($input['detail_beban_lainnya_rk'])) {
            $rawDetailB = is_array($input['detail_beban_lainnya_rk'])
                ? $input['detail_beban_lainnya_rk']
                : json_decode($input['detail_beban_lainnya_rk'], true);
            if (is_array($rawDetailB)) {
                $detailBeban = $rawDetailB;
                if ($bebanLainnya == 0) {
                    foreach ($rawDetailB as $db) {
                        $bebanLainnya += $this->cleanNumber($db['nominal'] ?? 0);
                    }
                }
            }
        }

        // Cicilan berjalan / pinjaman
        $cicilanBerjalan  = $this->cleanNumber($input['cicilan_berjalan'] ?? $input['cicilan_pokok'] ?? $input['beban_bunga'] ?? 0);

        // Pajak jika ada
        $pajakNom = isset($input['pajak']) && $input['pajak'] !== '' ? $this->cleanNumber($input['pajak']) : 0;

        // Total pengeluaran rutin
        $totalPengeluaran = $pengeluaranHidup + $bebanLainnya;

        // Surplus / Laba Bersih
        $surplus = $totalPenghasilan - $totalPengeluaran - $cicilanBerjalan - $pajakNom;

        $labaRugi = [
            'pendapatan'         => $penghasilanUtama,
            'pendapatan_lain'    => $pendapatanLain,
            'total_penghasilan'  => $totalPenghasilan,
            'detail_pendapatan'  => $detailPendapatan,
            'hpp'                => 0,
            'laba_kotor'         => $totalPenghasilan,
            'beban_operasional'  => $pengeluaranHidup,
            'beban_gaji'         => 0,
            'beban_lainnya'      => $bebanLainnya,
            'detail_beban'       => $detailBeban,
            'total_pengeluaran'  => $totalPengeluaran,
            'beban_penyusutan'   => 0,
            'beban_bunga'        => $cicilanBerjalan,
            'laba_operasional'   => $totalPenghasilan - $totalPengeluaran,
            'laba_sebelum_pajak' => $totalPenghasilan - $totalPengeluaran - $cicilanBerjalan,
            'pajak'              => $pajakNom,
            'laba_bersih'        => $surplus,
        ];

        // ARUS KAS
        $saldoAwalKas = $this->cleanNumber($input['saldo_awal_kas'] ?? $input['input_kas_tabungan_awal'] ?? 0);
        $prive        = $this->cleanNumber($input['prive_dividen'] ?? $input['input_prive_dividen'] ?? 0);

        $arusMasuk  = $totalPenghasilan;
        $arusKeluar = $totalPengeluaran + $cicilanBerjalan + $pajakNom + $prive;
        $kasBersih  = $arusMasuk - $arusKeluar;
        $kasAkhir   = $saldoAwalKas + $kasBersih;

        $arusKas = [
            'laba_bersih'          => $surplus,
            'penyusutan'           => 0,
            'kenaikan_piutang'     => 0,
            'kenaikan_persediaan'  => 0,
            'kenaikan_utang_usaha' => 0,
            'kas_operasional'      => $surplus,
            'pembelian_aset_tetap' => 0,
            'kas_investasi'        => 0,
            'tambahan_modal'       => 0,
            'pinjaman_bank_baru'   => 0,
            'cicilan_pokok'        => $cicilanBerjalan,
            'prive_dividen'        => $prive,
            'kas_pendanaan'        => -$prive,
            'saldo_awal_kas'       => $saldoAwalKas,
            'kenaikan_bersih_kas'  => $kasBersih,
            'kas_akhir'            => $kasAkhir,
        ];

        return [
            'tipe_pemohon' => 'karyawan',
            'laba_rugi'    => $labaRugi,
            'arus_kas'     => $arusKas,
            'neraca'       => null,
            'asumsi'       => [],
            'validasi'     => [
                'status_neraca'  => 'Tidak Berlaku (Karyawan)',
                'selisih_neraca' => 0,
                'status_kas'     => 'Cocok',
                'selisih_kas'    => 0,
                'is_seimbang'    => true,
            ],
        ];
    }

    /**
     * Helper pembersih format angka Rupiah / float string
     */
    public function cleanNumber($val): float
    {
        if ($val === null || $val === '') return 0.0;
        if (is_numeric($val)) return (float)$val;
        $str = preg_replace('/[^\d\.,\-]/', '', (string)$val);
        if (strpos($str, ',') !== false && strpos($str, '.') !== false) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (strpos($str, '.') !== false) {
            $parts = explode('.', $str);
            if (count($parts) > 2) {
                $str = str_replace('.', '', $str);
            }
        } elseif (strpos($str, ',') !== false) {
            $str = str_replace(',', '.', $str);
        }
        return (float)$str;
    }
}
