<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHartaDanRasioToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        $existing = $this->db->getFieldNames('kredit_keuangan_dsc');
        $fields   = [];

        $allDefinitions = [
            // Komponen Aset Usaha (Perorangan)
            'harta_kas_usaha'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'harta_piutang_usaha'      => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'harta_stok_usaha'         => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'harta_alat_usaha'         => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // Komponen Aset Pribadi & Deposito (Perorangan)
            'harta_tabungan'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'harta_deposito'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'status_deposito'          => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => true, 'default' => 'bebas'],
            'harta_tanah'              => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'status_tanah'             => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => true, 'default' => 'bebas'],
            'harta_bangunan'           => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'status_bangunan'          => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => true, 'default' => 'bebas'],
            'harta_kendaraan'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'status_kendaraan'         => ['type' => 'VARCHAR', 'constraint' => '30', 'null' => true, 'default' => 'bebas'],

            // Akumulasi Neraca & Kekayaan Bersih
            'total_harta_bruto'        => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'aset_terikat_jaminan'     => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'total_harta_eligible'     => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'total_utang_riil'         => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'total_angsuran_bulanan'   => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],
            'kekayaan_bersih'          => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true, 'default' => 0],

            // 5 Rasio Keuangan
            'rasio_dsr'                => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'default' => 0],
            'rasio_dar'                => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'default' => 0],
            'rasio_ltv'                => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'default' => 0],
            'rasio_liquidity_buffer'   => ['type' => 'DECIMAL', 'constraint' => '8,2', 'null' => true, 'default' => 0],

            // Detail JSON lengkap
            'detail_harta_json'        => ['type' => 'LONGTEXT', 'null' => true],
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
        $cols = [
            'harta_kas_usaha', 'harta_piutang_usaha', 'harta_stok_usaha', 'harta_alat_usaha',
            'harta_tabungan', 'harta_deposito', 'status_deposito',
            'harta_tanah', 'status_tanah', 'harta_bangunan', 'status_bangunan',
            'harta_kendaraan', 'status_kendaraan',
            'total_harta_bruto', 'aset_terikat_jaminan', 'total_harta_eligible',
            'total_utang_riil', 'total_angsuran_bulanan', 'kekayaan_bersih',
            'rasio_dsr', 'rasio_dar', 'rasio_ltv', 'rasio_liquidity_buffer',
            'detail_harta_json',
        ];
        foreach ($cols as $col) {
            try {
                $this->forge->dropColumn('kredit_keuangan_dsc', $col);
            } catch (\Throwable $e) {}
        }
    }
}

