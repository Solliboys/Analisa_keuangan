<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBidangUsahaToKredit extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('bidang_usaha', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'bidang_usaha' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'jenis_usaha',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('bidang_usaha', 'kredit')) {
            $this->forge->dropColumn('kredit', 'bidang_usaha');
        }
    }
}
