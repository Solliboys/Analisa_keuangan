<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingReferensiTable extends Migration
{
    public function up()
    {
        // 1. Buat Tabel setting_referensi
        $this->db->query("CREATE TABLE IF NOT EXISTS `setting_referensi` (
            `id` int NOT NULL AUTO_INCREMENT,
            `jenis_referensi` varchar(50) NOT NULL,
            `keterangan_referensi` varchar(255) NOT NULL,
            `tipe_referensi` varchar(100) DEFAULT NULL,
            `color_referensi` varchar(50) DEFAULT 'primary',
            `urutan` int DEFAULT '0',
            `is_active` tinyint(1) DEFAULT '1',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_jenis_referensi` (`jenis_referensi`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Data Seed Awal Sesuai Gambar & Kebutuhan Analisa Sistem
        $initialData = [
            // jns_entitas (Sesuai Gambar)
            [
                'jenis_referensi'      => 'jns_entitas',
                'keterangan_referensi' => 'Badan Usaha',
                'tipe_referensi'       => 'B',
                'color_referensi'      => 'info',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'jns_entitas',
                'keterangan_referensi' => 'Perorangan',
                'tipe_referensi'       => 'I',
                'color_referensi'      => 'primary',
                'urutan'               => 2,
            ],

            // kre_jaminan (Sesuai Gambar)
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'BPKB Kendaraan Roda 2',
                'tipe_referensi'       => 'movable_asset',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'BPKB Kendaraan Roda 4',
                'tipe_referensi'       => 'movable_asset',
                'color_referensi'      => 'primary',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'Deposito Berjangka',
                'tipe_referensi'       => 'cash_collateral',
                'color_referensi'      => 'warning',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'SHGB (Hak Guna Bangunan)',
                'tipe_referensi'       => 'fixed_asset',
                'color_referensi'      => 'info',
                'urutan'               => 4,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'SHM (Sertifikat Hak Milik)',
                'tipe_referensi'       => 'fixed_asset',
                'color_referensi'      => 'success',
                'urutan'               => 5,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'SK Pegawai / Payroll',
                'tipe_referensi'       => 'cashflow',
                'color_referensi'      => 'secondary',
                'urutan'               => 6,
            ],
            [
                'jenis_referensi'      => 'kre_jaminan',
                'keterangan_referensi' => 'Tanpa Agunan',
                'tipe_referensi'       => 'unsecured',
                'color_referensi'      => 'danger',
                'urutan'               => 7,
            ],

            // kre_jns (Jenis Fasilitas Kredit)
            [
                'jenis_referensi'      => 'kre_jns',
                'keterangan_referensi' => 'Fasilitas Baru',
                'tipe_referensi'       => 'new_facility',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_jns',
                'keterangan_referensi' => 'Perpanjangan',
                'tipe_referensi'       => 'renewal',
                'color_referensi'      => 'info',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_jns',
                'keterangan_referensi' => 'Top Up',
                'tipe_referensi'       => 'topup',
                'color_referensi'      => 'success',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_jns',
                'keterangan_referensi' => 'Restrukturisasi',
                'tipe_referensi'       => 'restructure',
                'color_referensi'      => 'warning',
                'urutan'               => 4,
            ],

            // kre_kepkomite (Keputusan Komite Kredit)
            [
                'jenis_referensi'      => 'kre_kepkomite',
                'keterangan_referensi' => 'Disetujui',
                'tipe_referensi'       => 'approved',
                'color_referensi'      => 'success',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_kepkomite',
                'keterangan_referensi' => 'Disetujui Bersyarat',
                'tipe_referensi'       => 'conditional',
                'color_referensi'      => 'info',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_kepkomite',
                'keterangan_referensi' => 'Ditolak',
                'tipe_referensi'       => 'rejected',
                'color_referensi'      => 'danger',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_kepkomite',
                'keterangan_referensi' => 'Pending / Tinjau Ulang',
                'tipe_referensi'       => 'pending',
                'color_referensi'      => 'warning',
                'urutan'               => 4,
            ],

            // kre_lampberkas (Kategori Lampiran Berkas)
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'KTP & NPWP (Pemohon / Pasangan)',
                'tipe_referensi'       => 'identity',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Sertifikat Agunan / PBB / IMB',
                'tipe_referensi'       => 'collateral',
                'color_referensi'      => 'success',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Rekening Koran 3-6 Bulan',
                'tipe_referensi'       => 'financial',
                'color_referensi'      => 'info',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Legalitas Usaha (NIB / SIUP)',
                'tipe_referensi'       => 'legal',
                'color_referensi'      => 'secondary',
                'urutan'               => 4,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Laporan Keuangan & Pembukuan',
                'tipe_referensi'       => 'financial',
                'color_referensi'      => 'warning',
                'urutan'               => 5,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Foto Usaha & Lokasi Agunan',
                'tipe_referensi'       => 'site_visit',
                'color_referensi'      => 'primary',
                'urutan'               => 6,
            ],
            [
                'jenis_referensi'      => 'kre_lampberkas',
                'keterangan_referensi' => 'Dokumen Pendukung Lainnya',
                'tipe_referensi'       => 'supporting',
                'color_referensi'      => 'secondary',
                'urutan'               => 7,
            ],

            // kre_referral (Sumber Referral)
            [
                'jenis_referensi'      => 'kre_referral',
                'keterangan_referensi' => 'Direct / Kantor Cabang',
                'tipe_referensi'       => 'direct',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_referral',
                'keterangan_referensi' => 'Broker Kredit',
                'tipe_referensi'       => 'agent',
                'color_referensi'      => 'info',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_referral',
                'keterangan_referensi' => 'Agen Properti / Showroom',
                'tipe_referensi'       => 'partner',
                'color_referensi'      => 'success',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_referral',
                'keterangan_referensi' => 'Karyawan Internal',
                'tipe_referensi'       => 'employee',
                'color_referensi'      => 'warning',
                'urutan'               => 4,
            ],

            // kre_sts (Status Pengajuan Kredit)
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Draft',
                'tipe_referensi'       => 'draft',
                'color_referensi'      => 'secondary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Menunggu Verifikasi SLIK',
                'tipe_referensi'       => 'wait_slik',
                'color_referensi'      => 'warning',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Verifikasi SLIK Disetujui',
                'tipe_referensi'       => 'slik_approved',
                'color_referensi'      => 'info',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Dalam Review Komite',
                'tipe_referensi'       => 'in_review',
                'color_referensi'      => 'primary',
                'urutan'               => 4,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Disetujui',
                'tipe_referensi'       => 'approved',
                'color_referensi'      => 'success',
                'urutan'               => 5,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Ditolak',
                'tipe_referensi'       => 'rejected',
                'color_referensi'      => 'danger',
                'urutan'               => 6,
            ],
            [
                'jenis_referensi'      => 'kre_sts',
                'keterangan_referensi' => 'Selesai Dicairkan',
                'tipe_referensi'       => 'disbursed',
                'color_referensi'      => 'success',
                'urutan'               => 7,
            ],

            // kre_tujuan (Tujuan Pembiayaan)
            [
                'jenis_referensi'      => 'kre_tujuan',
                'keterangan_referensi' => 'Modal Kerja',
                'tipe_referensi'       => 'working_capital',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'kre_tujuan',
                'keterangan_referensi' => 'Investasi',
                'tipe_referensi'       => 'investment',
                'color_referensi'      => 'success',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'kre_tujuan',
                'keterangan_referensi' => 'Multiguna',
                'tipe_referensi'       => 'multipurpose',
                'color_referensi'      => 'info',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'kre_tujuan',
                'keterangan_referensi' => 'Take Over',
                'tipe_referensi'       => 'takeover',
                'color_referensi'      => 'warning',
                'urutan'               => 4,
            ],

            // slik_hsl (Hasil SLIK OJK)
            [
                'jenis_referensi'      => 'slik_hsl',
                'keterangan_referensi' => 'Lancar (Kolektibilitas 1)',
                'tipe_referensi'       => 'kol_1',
                'color_referensi'      => 'success',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'slik_hsl',
                'keterangan_referensi' => 'Dalam Perhatian Khusus (Kolektibilitas 2)',
                'tipe_referensi'       => 'kol_2',
                'color_referensi'      => 'info',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'slik_hsl',
                'keterangan_referensi' => 'Kurang Lancar (Kolektibilitas 3)',
                'tipe_referensi'       => 'kol_3',
                'color_referensi'      => 'warning',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'slik_hsl',
                'keterangan_referensi' => 'Diragukan (Kolektibilitas 4)',
                'tipe_referensi'       => 'kol_4',
                'color_referensi'      => 'danger',
                'urutan'               => 4,
            ],
            [
                'jenis_referensi'      => 'slik_hsl',
                'keterangan_referensi' => 'Macet (Kolektibilitas 5)',
                'tipe_referensi'       => 'kol_5',
                'color_referensi'      => 'danger',
                'urutan'               => 5,
            ],

            // slik_keterkaitan (Keterkaitan SLIK)
            [
                'jenis_referensi'      => 'slik_keterkaitan',
                'keterangan_referensi' => 'Tidak Terkait',
                'tipe_referensi'       => 'not_related',
                'color_referensi'      => 'success',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'slik_keterkaitan',
                'keterangan_referensi' => 'Terkait Bank / BPR',
                'tipe_referensi'       => 'related_bank',
                'color_referensi'      => 'warning',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'slik_keterkaitan',
                'keterangan_referensi' => 'Pengurus / Pemegang Saham',
                'tipe_referensi'       => 'management',
                'color_referensi'      => 'danger',
                'urutan'               => 3,
            ],

            // target_kategori (Kategori Parameter Analisa 5C)
            [
                'jenis_referensi'      => 'target_kategori',
                'keterangan_referensi' => 'Capacity (Kemampuan Membayar & Arus Kas)',
                'tipe_referensi'       => '5c_capacity',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'target_kategori',
                'keterangan_referensi' => 'Character (Integritas & Riwayat Kredit)',
                'tipe_referensi'       => '5c_character',
                'color_referensi'      => 'info',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'target_kategori',
                'keterangan_referensi' => 'Capital (Kekuatan Permodalan Debitur)',
                'tipe_referensi'       => '5c_capital',
                'color_referensi'      => 'success',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'target_kategori',
                'keterangan_referensi' => 'Collateral (Kualitas & Nilai Likuidasi Agunan)',
                'tipe_referensi'       => '5c_collateral',
                'color_referensi'      => 'warning',
                'urutan'               => 4,
            ],
            [
                'jenis_referensi'      => 'target_kategori',
                'keterangan_referensi' => 'Condition of Economy (Prospek Usaha & Pasar)',
                'tipe_referensi'       => '5c_condition',
                'color_referensi'      => 'secondary',
                'urutan'               => 5,
            ],

            // target_kode (Target Kode Rasio & Batasan Analisa)
            [
                'jenis_referensi'      => 'target_kode',
                'keterangan_referensi' => 'DSC_MIN_RATIO (Batas Minimal Rasio DSC 1.30x)',
                'tipe_referensi'       => 'ratio_threshold',
                'color_referensi'      => 'primary',
                'urutan'               => 1,
            ],
            [
                'jenis_referensi'      => 'target_kode',
                'keterangan_referensi' => 'LTV_MAX_LIMIT (Maksimal Loan to Value 70%)',
                'tipe_referensi'       => 'collateral_threshold',
                'color_referensi'      => 'warning',
                'urutan'               => 2,
            ],
            [
                'jenis_referensi'      => 'target_kode',
                'keterangan_referensi' => 'HPP_BENCHMARK (Standar HPP Usaha 60%)',
                'tipe_referensi'       => 'benchmark',
                'color_referensi'      => 'info',
                'urutan'               => 3,
            ],
            [
                'jenis_referensi'      => 'target_kode',
                'keterangan_referensi' => 'MAX_TENOR_MONTHS (Batas Maksimal Tenor 60 Bulan)',
                'tipe_referensi'       => 'policy_limit',
                'color_referensi'      => 'danger',
                'urutan'               => 4,
            ],
        ];

        $builder = $this->db->table('setting_referensi');
        foreach ($initialData as $row) {
            $builder->insert($row);
        }
    }

    public function down()
    {
        $this->forge->dropTable('setting_referensi', true);
    }
}
