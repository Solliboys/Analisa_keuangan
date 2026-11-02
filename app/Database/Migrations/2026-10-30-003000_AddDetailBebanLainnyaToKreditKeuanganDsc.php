<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailBebanLainnyaToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('detail_beban_lainnya_rk', 'kredit_keuangan_dsc')) {
            $this->forge->addColumn('kredit_keuangan_dsc', [
                'detail_beban_lainnya_rk' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'beban_lainnya_nominal_rk',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('detail_beban_lainnya_rk', 'kredit_keuangan_dsc')) {
            $this->forge->dropColumn('kredit_keuangan_dsc', 'detail_beban_lainnya_rk');
        }
    }
}
