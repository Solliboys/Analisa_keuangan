<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganPendapatanLainToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('keterangan_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $this->forge->addColumn('kredit_keuangan_dsc', [
                'keterangan_pendapatan_lain_rk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'pendapatan_lain_rk',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('keterangan_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $this->forge->dropColumn('kredit_keuangan_dsc', 'keterangan_pendapatan_lain_rk');
        }
    }
}
