<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        $existing = $this->db->getFieldNames('kredit_keuangan_dsc');
        $fields   = [];

        $allDefinitions = [
            // Beban Tambahan
            'beban_depresiasi'              => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'beban_bunga_bank'              => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'beban_pajak'                   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'laba_bersih_setelah_pajak'     => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],

            // Neraca Akhir
            'neraca_kas'                    => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_piutang_usaha'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_hari_piutang_dso'       => ['type' => 'INT', 'null' => true, 'default' => 30],
            'neraca_persediaan'             => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_hari_stok_dio'          => ['type' => 'INT', 'null' => true, 'default' => 45],
            'neraca_aset_lancar'            => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_aset_tetap_bruto'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_akumulasi_penyusutan'   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_aset_tetap_netto'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_total_aset'             => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_utang_usaha'            => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_hari_utang_dpo'         => ['type' => 'INT', 'null' => true, 'default' => 30],
            'neraca_utang_bank_lancar'      => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_beban_akrual'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_total_liabilitas_lancar' => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_utang_bank_panjang'     => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_total_liabilitas'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_modal_disetor'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_laba_ditahan'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'neraca_total_ekuitas'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],

            // Neraca Awal
            'neraca_awal_kas'               => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_piutang'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_persediaan'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_aset_tetap'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_akum_penyusutan'   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_utang_usaha'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_utang_bank'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_modal_disetor'     => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'neraca_awal_laba_ditahan'      => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // Input Neraca & Parameter Badan Usaha
            'input_piutang_akhir'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'input_persediaan_akhir'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'input_utang_usaha_akhir'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'input_capex'                   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_capex_umur_ekonomis'     => ['type' => 'INT', 'null' => true, 'default' => 5],
            'input_pinjaman_baru'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_pembayaran_pokok'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_beban_bunga'             => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_beban_pajak'             => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'input_setoran_modal'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_prive_dividen'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // Input Perorangan
            'input_kas_tabungan_awal'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_aset_emas'               => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_aset_kendaraan'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_aset_properti'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_total_utang'             => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'input_pengeluaran_hidup'       => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // Arus Kas
            'cf_operasional'                => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'cf_investasi'                  => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'cf_pendanaan'                  => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'cf_kenaikan_bersih'            => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'cf_kas_akhir'                  => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // Rasio Keuangan
            'rasio_current_ratio'           => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'rasio_der'                     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'rasio_gpm'                     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'rasio_npm'                     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],
            'rasio_roa'                     => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true],

            // Status Laporan
            'is_laporan_generated'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'laporan_json_data'             => ['type' => 'LONGTEXT', 'null' => true],
            'laporan_generated_at'          => ['type' => 'DATETIME', 'null' => true],
        ];

        foreach ($allDefinitions as $colName => $def) {
            if (!in_array($colName, $existing)) {
                $fields[$colName] = $def;
            }
        }

        if (!empty($fields)) {
            $this->forge->addColumn('kredit_keuangan_dsc', $fields);
        }
    }

    public function down()
    {
        // No-op or drop added columns if needed
    }
}

