<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKreditTables extends Migration
{
    public function up()
    {
        // 1. Tabel kredit (Induk)
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit` (
            `kredit_id` int NOT NULL AUTO_INCREMENT,
            `user_id` int NOT NULL COMMENT 'ID Pengguna pembuat pengajuan',
            `nomor_aplikasi` varchar(50) NOT NULL COMMENT 'Contoh: CAM-2026-0001',
            `tipe_aplikasi` enum('Fasilitas Baru','Fasilitas Exist') NOT NULL DEFAULT 'Fasilitas Baru',
            `jenis_debitur` enum('Individu','Badan Usaha') NOT NULL DEFAULT 'Individu',
            `nama_debitur` varchar(150) NOT NULL,
            `plafon_pengajuan` decimal(15,2) NOT NULL DEFAULT '0.00',
            `tenor_bulan` int NOT NULL DEFAULT '12',
            `jenis_jaminan` enum('On Hand','Take Over') NOT NULL DEFAULT 'On Hand',
            `tujuan_pembiayaan` enum('Multiguna','Modal Kerja') NOT NULL DEFAULT 'Modal Kerja',
            `skema_pembayaran` enum('Balloon Payment','Installment') NOT NULL DEFAULT 'Balloon Payment',
            `bunga_per_bulan` decimal(5,2) NOT NULL DEFAULT '3.00',
            `bunga_per_tahun` decimal(5,2) NOT NULL DEFAULT '36.00',
            `alamat_ktp` text NOT NULL,
            `alamat_domisili` text,
            `kepemilikan_domisili` varchar(50) DEFAULT NULL,
            `nomor_hp` varchar(30) NOT NULL,
            `nomor_emergency` varchar(30) DEFAULT NULL,
            `npwp_pribadi` varchar(30) DEFAULT NULL,
            `nama_ibu_kandung` varchar(150) DEFAULT NULL,
            `tempat_tanggal_lahir` varchar(150) DEFAULT NULL,
            `akta_pendirian` varchar(100) DEFAULT NULL,
            `akta_perubahan` varchar(100) DEFAULT NULL,
            `sk_pendirian` varchar(100) DEFAULT NULL,
            `sk_perubahan` varchar(100) DEFAULT NULL,
            `nama_usaha` varchar(150) DEFAULT NULL,
            `jabatan` varchar(100) DEFAULT NULL,
            `lama_usaha` varchar(50) DEFAULT NULL,
            `alamat_usaha` text,
            `npwp_usaha` varchar(30) DEFAULT NULL,
            `nomor_induk_berusaha_nib` varchar(50) DEFAULT NULL,
            `sumber_pembayaran` varchar(100) DEFAULT NULL,
            `ringkasan_slik` text,
            `status_slik` varchar(30) DEFAULT 'Belum Terbit',
            `hasil_slik` varchar(100) DEFAULT NULL,
            `slik_verified_at` datetime DEFAULT NULL,
            `slik_verified_by` varchar(100) DEFAULT NULL,
            `status_pengajuan` varchar(50) NOT NULL DEFAULT 'Draft',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`kredit_id`),
            UNIQUE KEY `nomor_aplikasi` (`nomor_aplikasi`),
            KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Tabel agunan
        $this->db->query("CREATE TABLE IF NOT EXISTS `agunan` (
            `agunan_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `alamat_agunan` text,
            `jenis_agunan` varchar(50) NOT NULL COMMENT 'SHM, SHGB, BPKB, Deposito, Lainnya',
            `nomor_sertifikat` varchar(100) NOT NULL,
            `luas_tanah_m2` int DEFAULT '0',
            `luas_bangunan_m2` int DEFAULT '0',
            `status_imb` enum('Ada','Tidak Ada') NOT NULL DEFAULT 'Ada',
            `pemilik_agunan` varchar(150) NOT NULL,
            `hubungan_kepemilikan` varchar(50) DEFAULT NULL,
            `lama_kepemilikan` varchar(50) DEFAULT NULL,
            `nilai_pasar_mv` decimal(20,2) NOT NULL DEFAULT '0.00',
            `nilai_njop` decimal(20,2) NOT NULL DEFAULT '0.00',
            `persen_likuidasi` decimal(5,2) NOT NULL DEFAULT '60.00',
            `nilai_likuidasi_lv` decimal(20,2) NOT NULL DEFAULT '0.00',
            `coverage_mv` decimal(8,2) DEFAULT '0.00',
            `coverage_lv` decimal(8,2) DEFAULT '0.00',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`agunan_id`),
            KEY `kredit_id` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 3. Tabel slik
        $this->db->query("CREATE TABLE IF NOT EXISTS `slik` (
            `slik_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `nama_debitur` varchar(150) NOT NULL,
            `plafon` decimal(15,2) NOT NULL DEFAULT '0.00',
            `outstanding` decimal(15,2) NOT NULL DEFAULT '0.00',
            `kelancaran` enum('Lancar','DPD 1-30','DPD 60-90','DPD 90-180','DPD 180+','Macet','Hapus Buku') NOT NULL DEFAULT 'Lancar',
            `bank_lembaga` varchar(150) NOT NULL,
            `keterangan` varchar(255) DEFAULT NULL,
            `is_verified` tinyint(1) DEFAULT '0',
            `diinput_oleh` varchar(50) DEFAULT 'pengusul',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`slik_id`),
            KEY `kredit_id` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 4. Tabel komite
        $this->db->query("CREATE TABLE IF NOT EXISTS `komite` (
            `komite_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `user_id` int DEFAULT NULL,
            `role_approver` varchar(50) NOT NULL,
            `nama_approver` varchar(150) DEFAULT NULL,
            `status_approval` varchar(50) NOT NULL DEFAULT 'Pending',
            `tanggal_approval` date DEFAULT NULL,
            `catatan_approval` text,
            `file_dokumen_cam_pdf` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`komite_id`),
            KEY `kredit_id` (`kredit_id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 5. Tabel kredit_analisa
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_analisa` (
            `analisa_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `analisa_jaminan` text,
            `business_review` text,
            `business_catatan` text,
            `detail_kebutuhan` text,
            `skor_kelayakan_ai` int DEFAULT NULL,
            `rekomendasi_keputusan_ai` varchar(50) DEFAULT NULL,
            `rekomendasi_plafon_ai` decimal(20,2) DEFAULT NULL,
            `analisis_risiko_ai` longtext,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`analisa_id`),
            UNIQUE KEY `uk_analisa_kredit` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 6. Tabel kredit_disbursement
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_disbursement` (
            `disbursement_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `provisi_persen` decimal(5,2) DEFAULT '7.00',
            `provisi_nominal` decimal(20,2) DEFAULT '0.00',
            `agent_persen` decimal(5,2) DEFAULT '1.00',
            `agent_nominal` decimal(20,2) DEFAULT '0.00',
            `admin_persen` decimal(5,2) DEFAULT '1.00',
            `admin_nominal` decimal(20,2) DEFAULT '0.00',
            `hold_angsuran_bulan` int DEFAULT '4',
            `hold_angsuran_nominal` decimal(20,2) DEFAULT '0.00',
            `asuransi_jiwa` decimal(20,2) DEFAULT '0.00',
            `asuransi_kebakaran` decimal(20,2) DEFAULT '0.00',
            `total_potongan` decimal(20,2) DEFAULT '0.00',
            `dana_bersih_cair` decimal(20,2) DEFAULT '0.00',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`disbursement_id`),
            UNIQUE KEY `uk_disbursement_kredit` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 7. Tabel kredit_dokumen
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_dokumen` (
            `dokumen_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `kategori_dokumen` varchar(100) NOT NULL DEFAULT 'KTP & NPWP',
            `nama_file_asli` varchar(255) NOT NULL,
            `path_file` varchar(255) NOT NULL,
            `ukuran_file_bytes` int DEFAULT '0',
            `tipe_file` varchar(100) DEFAULT NULL,
            `catatan_dokumen` text,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`dokumen_id`),
            KEY `kredit_id` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 8. Tabel kredit_keuangan_dsc
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_keuangan_dsc` (
            `keuangan_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `pendapatan_rk` decimal(20,2) DEFAULT '0.00',
            `hpp_persen_rk` decimal(5,2) DEFAULT '60.00',
            `hpp_nominal_rk` decimal(20,2) DEFAULT '0.00',
            `laba_kotor_rk` decimal(20,2) DEFAULT '0.00',
            `biaya_hidup_persen_rk` decimal(5,2) DEFAULT '10.00',
            `biaya_hidup_nominal_rk` decimal(20,2) DEFAULT '0.00',
            `beban_lainnya_persen_rk` decimal(5,2) DEFAULT '10.00',
            `beban_lainnya_nominal_rk` decimal(20,2) DEFAULT '0.00',
            `laba_bersih_rk` decimal(20,2) DEFAULT '0.00',
            `pendapatan_iv` decimal(20,2) DEFAULT '0.00',
            `hpp_persen_iv` decimal(5,2) DEFAULT '60.00',
            `hpp_nominal_iv` decimal(20,2) DEFAULT '0.00',
            `laba_kotor_iv` decimal(20,2) DEFAULT '0.00',
            `biaya_hidup_persen_iv` decimal(5,2) DEFAULT '10.00',
            `biaya_hidup_nominal_iv` decimal(20,2) DEFAULT '0.00',
            `beban_lainnya_persen_iv` decimal(5,2) DEFAULT '10.00',
            `beban_lainnya_nominal_iv` decimal(20,2) DEFAULT '0.00',
            `laba_bersih_iv` decimal(20,2) DEFAULT '0.00',
            `angsuran_balloon` decimal(20,2) DEFAULT '0.00',
            `angsuran_installment` decimal(20,2) DEFAULT '0.00',
            `angsuran_dipilih` decimal(20,2) DEFAULT '0.00',
            `rasio_dsc_rk` decimal(6,2) DEFAULT '0.00',
            `rasio_dsc_iv` decimal(6,2) DEFAULT '0.00',
            `status_dsc_rk` varchar(50) DEFAULT 'Aman',
            `status_dsc_iv` varchar(50) DEFAULT 'Aman',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`keuangan_id`),
            UNIQUE KEY `uk_keuangan_kredit` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 9. Tabel kredit_rekening
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_rekening` (
            `rekening_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `nama_bank` varchar(100) NOT NULL,
            `periode` varchar(100) DEFAULT NULL,
            `nomor_rekening` varchar(100) DEFAULT NULL,
            `atas_nama` varchar(150) DEFAULT NULL,
            `total_mutasi_kredit` decimal(20,2) DEFAULT '0.00',
            `total_mutasi_debit` decimal(20,2) DEFAULT '0.00',
            `jumlah_bulan` int DEFAULT '6',
            `rata_rata_kredit` decimal(20,2) DEFAULT '0.00',
            `rata_rata_debit` decimal(20,2) DEFAULT '0.00',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`rekening_id`),
            KEY `kredit_id` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 10. Tabel kredit_webcheck
        $this->db->query("CREATE TABLE IF NOT EXISTS `kredit_webcheck` (
            `webcheck_id` int NOT NULL AUTO_INCREMENT,
            `kredit_id` int NOT NULL,
            `sipp_keterangan` varchar(255) DEFAULT NULL,
            `sipp_file` varchar(255) DEFAULT NULL,
            `internet_keterangan` varchar(255) DEFAULT NULL,
            `internet_file` varchar(255) DEFAULT NULL,
            `watchlist_keterangan` varchar(255) DEFAULT NULL,
            `watchlist_file` varchar(255) DEFAULT NULL,
            `lelang_keterangan` varchar(255) DEFAULT NULL,
            `lelang_file` varchar(255) DEFAULT NULL,
            `negatif_keterangan` varchar(255) DEFAULT NULL,
            `negatif_file` varchar(255) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`webcheck_id`),
            UNIQUE KEY `uk_webcheck_kredit` (`kredit_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function down()
    {
        $tables = [
            'kredit_webcheck', 'kredit_rekening', 'kredit_keuangan_dsc',
            'kredit_dokumen', 'kredit_disbursement', 'kredit_analisa',
            'komite', 'slik', 'agunan', 'kredit'
        ];
        foreach ($tables as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
