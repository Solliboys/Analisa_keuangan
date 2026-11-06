<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRincianBiayaLainToKreditDisbursement extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('rincian_biaya_lain', 'kredit_disbursement')) {
            $this->forge->addColumn('kredit_disbursement', [
                'rincian_biaya_lain' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                    'after'      => 'asuransi_kebakaran'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('rincian_biaya_lain', 'kredit_disbursement')) {
            $this->forge->dropColumn('kredit_disbursement', 'rincian_biaya_lain');
        }
    }
}

