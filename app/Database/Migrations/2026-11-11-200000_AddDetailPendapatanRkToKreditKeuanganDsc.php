<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailPendapatanRkToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('detail_pendapatan_rk', 'kredit_keuangan_dsc')) {
            $this->forge->addColumn('kredit_keuangan_dsc', [
                'detail_pendapatan_rk' => [
                    'type'  => 'TEXT',
                    'null'  => true,
                    'after' => 'pendapatan_rk',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('detail_pendapatan_rk', 'kredit_keuangan_dsc')) {
            $this->forge->dropColumn('kredit_keuangan_dsc', 'detail_pendapatan_rk');
        }
    }
}

