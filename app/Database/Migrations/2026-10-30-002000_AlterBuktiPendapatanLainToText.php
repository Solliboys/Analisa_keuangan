<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterBuktiPendapatanLainToText extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $this->forge->modifyColumn('kredit_keuangan_dsc', [
                'bukti_pendapatan_lain_rk' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $this->forge->modifyColumn('kredit_keuangan_dsc', [
                'bukti_pendapatan_lain_rk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
            ]);
        }
    }
}
