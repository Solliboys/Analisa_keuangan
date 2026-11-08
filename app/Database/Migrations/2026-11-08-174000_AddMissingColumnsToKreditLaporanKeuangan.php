<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToKreditLaporanKeuangan extends Migration
{
    public function up()
    {
        // Tambah kolom yang dibutuhkan controller (json_laporan, generated_at, generated_by)
        // dan kolom input neraca yang belum ada
        $fields = [];

        // Cek dan tambah kolom baru
        $existingColumns = $this->db->getFieldNames('kredit_laporan_keuangan');

        if (!in_array('json_laporan', $existingColumns)) {
            $fields['json_laporan'] = ['type' => 'LONGTEXT', 'null' => true, 'after' => 'raw_data_json'];
        }
        if (!in_array('generated_at', $existingColumns)) {
            $fields['generated_at'] = ['type' => 'DATETIME', 'null' => true, 'after' => 'json_laporan'];
        }
        if (!in_array('generated_by', $existingColumns)) {
            $fields['generated_by'] = ['type' => 'INT', 'constraint' => 11, 'null' => true, 'after' => 'generated_at'];
        }
        // Kolom input neraca yang belum ada
        if (!in_array('kas_bank_neraca', $existingColumns)) {
            $fields['kas_bank_neraca'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0, 'after' => 'tipe_pemohon'];
        }
        if (!in_array('piutang_usaha_neraca', $existingColumns)) {
            $fields['piutang_usaha_neraca'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0, 'after' => 'kas_bank_neraca'];
        }
        if (!in_array('persediaan_neraca', $existingColumns)) {
            $fields['persediaan_neraca'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0, 'after' => 'piutang_usaha_neraca'];
        }
        if (!in_array('aset_lancar_lain_neraca', $existingColumns)) {
            $fields['aset_lancar_lain_neraca'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0, 'after' => 'persediaan_neraca'];
        }
        if (!in_array('utang_bank_pendek', $existingColumns)) {
            $fields['utang_bank_pendek'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0, 'after' => 'utang_usaha'];
        }
        if (!in_array('utang_jangka_panjang_neraca', $existingColumns)) {
            $fields['utang_jangka_panjang_neraca'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        // Kolom arus kas tambahan
        if (!in_array('kenaikan_piutang', $existingColumns)) {
            $fields['kenaikan_piutang'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('kenaikan_persediaan', $existingColumns)) {
            $fields['kenaikan_persediaan'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('kenaikan_utang_usaha', $existingColumns)) {
            $fields['kenaikan_utang_usaha'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('penyusutan_tahunan_ak', $existingColumns)) {
            $fields['penyusutan_tahunan_ak'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('pembelian_aset_tetap', $existingColumns)) {
            $fields['pembelian_aset_tetap'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('cicilan_pokok', $existingColumns)) {
            $fields['cicilan_pokok'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('tambahan_modal', $existingColumns)) {
            $fields['tambahan_modal'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('pinjaman_baru', $existingColumns)) {
            $fields['pinjaman_baru'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        // Laba Rugi: beban sewa, utilitas, pemasaran, penyusutan per baris
        if (!in_array('beban_sewa', $existingColumns)) {
            $fields['beban_sewa'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        if (!in_array('beban_penyusutan', $existingColumns)) {
            $fields['beban_penyusutan'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }
        // Neraca ekuitas: laba ditahan akhir
        if (!in_array('laba_ditahan_akhir', $existingColumns)) {
            $fields['laba_ditahan_akhir'] = ['type' => 'DECIMAL', 'constraint' => '20,2', 'default' => 0];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('kredit_laporan_keuangan', $fields);
        }
    }

    public function down()
    {
        $cols = [
            'json_laporan', 'generated_at', 'generated_by',
            'kas_bank_neraca', 'piutang_usaha_neraca', 'persediaan_neraca', 'aset_lancar_lain_neraca',
            'utang_bank_pendek', 'utang_jangka_panjang_neraca',
            'kenaikan_piutang', 'kenaikan_persediaan', 'kenaikan_utang_usaha',
            'penyusutan_tahunan_ak', 'pembelian_aset_tetap', 'cicilan_pokok',
            'tambahan_modal', 'pinjaman_baru',
            'beban_sewa', 'beban_penyusutan', 'laba_ditahan_akhir',
        ];
        foreach ($cols as $col) {
            try {
                $this->forge->dropColumn('kredit_laporan_keuangan', $col);
            } catch (\Throwable $e) {}
        }
    }
}

