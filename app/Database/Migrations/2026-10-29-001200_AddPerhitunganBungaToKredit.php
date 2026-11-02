<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerhitunganBungaToKredit extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('perhitungan_bunga', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'perhitungan_bunga' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'ANUITAS',
                    'after'      => 'skema_pembayaran'
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('perhitungan_bunga', 'kredit')) {
            $this->forge->dropColumn('kredit', 'perhitungan_bunga');
        }
    }
}
