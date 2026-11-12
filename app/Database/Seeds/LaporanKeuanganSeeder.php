<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Services\LaporanKeuanganService;
use App\Models\KreditModel;
use App\Models\KreditKeuanganDscModel;
use App\Models\KreditLaporanKeuanganModel;

class LaporanKeuanganSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $service = new LaporanKeuanganService();

        // Cari user pengusul atau superadmin untuk user_id
        $user = $db->table('users')->where('jabatan', 'Pengusul')->orWhere('jabatan', 'Superadmin')->get()->getRowArray();
        $userId = $user['id'] ?? 1;

        // ========================================================
        // 1. SEED BADAN USAHA DENGAN 2 KARYAWAN
        // ========================================================
        $nomorAppBU = 'APP-BU-TEST-001';
        $existingBU = $db->table('kredit')->where('nomor_aplikasi', $nomorAppBU)->get()->getRowArray();

        $daftarKaryawanBU = json_encode([
            ['nama' => 'Budi Santoso', 'jabatan' => 'Manajer Operasional', 'gaji' => 48000000],
            ['nama' => 'Siti Nurhaliza', 'jabatan' => 'Staff Keuangan', 'gaji' => 48000000],
        ]);

        $dataKreditBU = [
            'user_id'           => $userId,
            'nomor_aplikasi'    => $nomorAppBU,
            'tipe_aplikasi'     => 'Fasilitas Baru',
            'jenis_debitur'     => 'Badan Usaha',
            'nama_debitur'      => 'PT Sumber Makmur Sejahtera',
            'plafon_pengajuan'  => 1000000000,
            'tenor_bulan'       => 12,
            'jenis_jaminan'     => 'Aset Tetap / Mesin',
            'tujuan_pembiayaan' => 'Modal Kerja & Investasi',
            'skema_pembayaran'  => 'Installment',
            'nama_usaha'        => 'PT Sumber Makmur Sejahtera',
            'daftar_karyawan'   => $daftarKaryawanBU,
            'status_pengajuan'  => 'Draft',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        if ($existingBU) {
            $db->table('kredit')->where('kredit_id', $existingBU['kredit_id'])->update($dataKreditBU);
            $kreditIdBU = $existingBU['kredit_id'];
        } else {
            $db->table('kredit')->insert($dataKreditBU);
            $kreditIdBU = $db->insertID();
        }

        // Data keuangan DSC untuk Badan Usaha
        // Sesuai requirement:
        // Pendapatan 1.200jt, HPP 700jt, Gaji 96jt, Sewa/Utilitas 104jt, Penyusutan 30jt, Bunga 20jt, Pajak 25jt
        // Kas awal 150jt, Piutang 80jt -> 120jt, Persediaan 100jt -> 130jt, Utang usaha 60jt -> 90jt
        // Aset tetap awal 300jt, Beli aset (CAPEX) 50jt, Utang bank awal 200jt -> cicilan 40jt (akhir 160jt)
        // Modal 250jt, Laba ditahan awal 120jt, Prive 75jt
        $dataKeuanganBU = [
            'kredit_id'                   => $kreditIdBU,
            'pendapatan_rk'               => 1200000000,
            'hpp_nominal_rk'              => 700000000,
            'hpp_persen_rk'               => round((700 / 1200) * 100, 2),
            'laba_kotor_rk'               => 500000000,
            'biaya_hidup_nominal_rk'      => 104000000, // Sewa/Utilitas/Pemasaran
            'beban_depresiasi'            => 30000000,
            'input_beban_bunga'           => 20000000,
            'input_beban_pajak'           => 25000000,
            // Aset lancar
            'neraca_awal_kas'             => 150000000,
            'neraca_awal_piutang'         => 80000000,
            'input_piutang_akhir'         => 120000000,
            'neraca_awal_persediaan'      => 100000000,
            'input_persediaan_akhir'      => 130000000,
            'neraca_awal_utang_usaha'     => 60000000,
            'input_utang_usaha_akhir'     => 90000000,
            // Aset tetap
            'neraca_awal_aset_tetap'      => 300000000,
            'input_capex'                 => 50000000,
            'neraca_aset_tetap_bruto'     => 350000000, // 300jt + 50jt
            'neraca_akumulasi_penyusutan' => 30000000,
            'input_capex_umur_ekonomis'   => 5,
            // Liabilitas & Ekuitas
            'neraca_awal_utang_bank'      => 200000000,
            'input_pembayaran_pokok'      => 40000000,
            'neraca_utang_bank_panjang'   => 160000000,
            'neraca_modal_disetor'        => 250000000,
            'neraca_awal_modal_disetor'   => 250000000,
            'neraca_awal_laba_ditahan'    => 120000000,
            'input_prive_dividen'         => 75000000,
            'created_at'                  => date('Y-m-d H:i:s'),
            'updated_at'                  => date('Y-m-d H:i:s'),
        ];

        $existingKeuanganBU = $db->table('kredit_keuangan_dsc')->where('kredit_id', $kreditIdBU)->get()->getRowArray();
        if ($existingKeuanganBU) {
            $db->table('kredit_keuangan_dsc')->where('keuangan_id', $existingKeuanganBU['keuangan_id'])->update($dataKeuanganBU);
        } else {
            $db->table('kredit_keuangan_dsc')->insert($dataKeuanganBU);
        }

        // Jalankan kalkulasi service dan simpan laporan
        $mergedBU = array_merge($dataKeuanganBU, [
            'daftar_karyawan' => $daftarKaryawanBU,
            'pendapatan'      => 1200000000,
            'hpp'             => 700000000,
        ]);
        $hasilBU = $service->hitungLaporan($mergedBU, 'badan_usaha');

        $laporanBU = [
            'kredit_id'                  => $kreditIdBU,
            'tipe_pemohon'               => 'badan_usaha',
            'asumsi_dso'                 => $hasilBU['asumsi']['dso'],
            'asumsi_dio'                 => $hasilBU['asumsi']['dio'],
            'asumsi_dpo'                 => $hasilBU['asumsi']['dpo'],
            'saldo_awal_piutang'         => $hasilBU['asumsi']['saldo_awal_piutang'],
            'saldo_awal_persediaan'      => $hasilBU['asumsi']['saldo_awal_persediaan'],
            'saldo_awal_utang_usaha'     => $hasilBU['asumsi']['saldo_awal_utang_usaha'],
            'saldo_awal_aset_tetap'      => $hasilBU['asumsi']['saldo_awal_aset_tetap'],
            'saldo_awal_kas'             => $hasilBU['asumsi']['saldo_awal_kas'],
            'kas_bank_neraca'            => $hasilBU['neraca']['kas_bank'],
            'piutang_usaha_neraca'       => $hasilBU['neraca']['piutang_usaha'],
            'persediaan_neraca'          => $hasilBU['neraca']['persediaan'],
            'aset_lancar_lain_neraca'    => $hasilBU['neraca']['aset_lancar_lain'],
            'harga_perolehan_aset_tetap' => $hasilBU['neraca']['harga_perolehan_aset_tetap'],
            'akumulasi_penyusutan'       => $hasilBU['neraca']['akumulasi_penyusutan'],
            'umur_ekonomis_tahun'        => $hasilBU['neraca']['umur_ekonomis_tahun'],
            'utang_usaha'                => $hasilBU['neraca']['utang_usaha'],
            'utang_bank_pendek'          => $hasilBU['neraca']['utang_bank_pendek'],
            'utang_jangka_panjang'       => $hasilBU['neraca']['utang_jangka_panjang'],
            'modal_disetor'              => $hasilBU['neraca']['modal_disetor'],
            'laba_ditahan_awal'          => $hasilBU['neraca']['laba_ditahan_awal'],
            'laba_ditahan_akhir'         => $hasilBU['neraca']['laba_ditahan_akhir'],
            'prive_dividen'              => $hasilBU['neraca']['prive_dividen'],
            'pendapatan'                 => $hasilBU['laba_rugi']['pendapatan'],
            'hpp'                        => $hasilBU['laba_rugi']['hpp'],
            'laba_kotor'                 => $hasilBU['laba_rugi']['laba_kotor'],
            'beban_gaji'                 => $hasilBU['laba_rugi']['beban_gaji'],
            'beban_operasional'          => $hasilBU['laba_rugi']['beban_operasional'],
            'beban_penyusutan'           => $hasilBU['laba_rugi']['beban_penyusutan'],
            'laba_operasional'           => $hasilBU['laba_rugi']['laba_operasional'],
            'pendapatan_lain'            => 0,
            'beban_bunga'                => $hasilBU['laba_rugi']['beban_bunga'],
            'laba_sebelum_pajak'         => $hasilBU['laba_rugi']['laba_sebelum_pajak'],
            'pajak'                      => $hasilBU['laba_rugi']['pajak'],
            'laba_bersih'                => $hasilBU['laba_rugi']['laba_bersih'],
            'kas_operasional'            => $hasilBU['arus_kas']['kas_operasional'],
            'kas_investasi'              => $hasilBU['arus_kas']['kas_investasi'],
            'kas_pendanaan'              => $hasilBU['arus_kas']['kas_pendanaan'],
            'kenaikan_bersih_kas'        => $hasilBU['arus_kas']['kenaikan_bersih_kas'],
            'kas_akhir'                  => $hasilBU['arus_kas']['kas_akhir'],
            'total_aset'                 => $hasilBU['neraca']['total_aset'],
            'total_liabilitas'           => $hasilBU['neraca']['total_liabilitas'],
            'total_ekuitas'              => $hasilBU['neraca']['total_ekuitas'],
            'status_neraca'              => $hasilBU['validasi']['status_neraca'],
            'selisih_neraca'             => $hasilBU['validasi']['selisih_neraca'],
            'status_kas'                 => $hasilBU['validasi']['status_kas'],
            'selisih_kas'                => $hasilBU['validasi']['selisih_kas'],
            'json_laporan'               => json_encode($hasilBU),
            'generated_at'               => date('Y-m-d H:i:s'),
            'generated_by'               => $userId,
            'created_at'                 => date('Y-m-d H:i:s'),
            'updated_at'                 => date('Y-m-d H:i:s'),
        ];

        $existingLaporanBU = $db->table('kredit_laporan_keuangan')->where('kredit_id', $kreditIdBU)->get()->getRowArray();
        if ($existingLaporanBU) {
            $db->table('kredit_laporan_keuangan')->where('id', $existingLaporanBU['id'])->update($laporanBU);
        } else {
            $db->table('kredit_laporan_keuangan')->insert($laporanBU);
        }

        // ========================================================
        // 2. SEED PERORANGAN / KARYAWAN
        // ========================================================
        // Penghasilan 8,0jt, lain-lain 1,0jt, pengeluaran 4,5jt, cicilan 2,0jt, surplus 2,5jt
        $nomorAppPR = 'APP-PR-TEST-002';
        $existingPR = $db->table('kredit')->where('nomor_aplikasi', $nomorAppPR)->get()->getRowArray();

        $dataKreditPR = [
            'user_id'           => $userId,
            'nomor_aplikasi'    => $nomorAppPR,
            'tipe_aplikasi'     => 'Fasilitas Baru',
            'jenis_debitur'     => 'Perorangan',
            'nama_debitur'      => 'Ahmad Wahyudi (Karyawan)',
            'plafon_pengajuan'  => 50000000,
            'tenor_bulan'       => 12,
            'jenis_jaminan'     => 'BPKB Kendaraan',
            'tujuan_pembiayaan' => 'Konsumtif / Renovasi',
            'skema_pembayaran'  => 'Installment',
            'status_pengajuan'  => 'Draft',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        if ($existingPR) {
            $db->table('kredit')->where('kredit_id', $existingPR['kredit_id'])->update($dataKreditPR);
            $kreditIdPR = $existingPR['kredit_id'];
        } else {
            $db->table('kredit')->insert($dataKreditPR);
            $kreditIdPR = $db->insertID();
        }

        $dataKeuanganPR = [
            'kredit_id'               => $kreditIdPR,
            'pendapatan_iv'           => 8000000,
            'pendapatan_lain_rk'      => 1000000,
            'biaya_hidup_nominal_iv'  => 4500000,
            'angsuran_installment'    => 2000000,
            'input_pengeluaran_hidup' => 4500000,
            'input_pembayaran_pokok'  => 2000000,
            'neraca_awal_kas'         => 5000000,
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ];

        $existingKeuanganPR = $db->table('kredit_keuangan_dsc')->where('kredit_id', $kreditIdPR)->get()->getRowArray();
        if ($existingKeuanganPR) {
            $db->table('kredit_keuangan_dsc')->where('keuangan_id', $existingKeuanganPR['keuangan_id'])->update($dataKeuanganPR);
        } else {
            $db->table('kredit_keuangan_dsc')->insert($dataKeuanganPR);
        }

        $mergedPR = [
            'penghasilan_utama'        => 8000000,
            'pendapatan_lain'          => 1000000,
            'pengeluaran_rumah_tangga' => 4500000,
            'cicilan_berjalan'         => 2000000,
            'saldo_awal_kas'           => 5000000,
        ];
        $hasilPR = $service->hitungLaporan($mergedPR, 'karyawan');

        $laporanPR = [
            'kredit_id'           => $kreditIdPR,
            'tipe_pemohon'        => 'karyawan',
            'pendapatan'          => $hasilPR['laba_rugi']['pendapatan'],
            'pendapatan_lain'     => $hasilPR['laba_rugi']['pendapatan_lain'],
            'beban_operasional'   => $hasilPR['laba_rugi']['beban_operasional'],
            'beban_bunga'         => $hasilPR['laba_rugi']['beban_bunga'],
            'laba_bersih'         => $hasilPR['laba_rugi']['laba_bersih'],
            'kas_operasional'     => $hasilPR['arus_kas']['kas_operasional'],
            'kas_pendanaan'       => $hasilPR['arus_kas']['kas_pendanaan'],
            'kenaikan_bersih_kas' => $hasilPR['arus_kas']['kenaikan_bersih_kas'],
            'kas_akhir'           => $hasilPR['arus_kas']['kas_akhir'],
            'saldo_awal_kas'      => $hasilPR['arus_kas']['saldo_awal_kas'],
            'status_neraca'       => 'Tidak Berlaku (Karyawan)',
            'selisih_neraca'      => 0,
            'status_kas'          => 'Cocok',
            'selisih_kas'         => 0,
            'json_laporan'        => json_encode($hasilPR),
            'generated_at'        => date('Y-m-d H:i:s'),
            'generated_by'        => $userId,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];

        $existingLaporanPR = $db->table('kredit_laporan_keuangan')->where('kredit_id', $kreditIdPR)->get()->getRowArray();
        if ($existingLaporanPR) {
            $db->table('kredit_laporan_keuangan')->where('id', $existingLaporanPR['id'])->update($laporanPR);
        } else {
            $db->table('kredit_laporan_keuangan')->insert($laporanPR);
        }
    }
}
