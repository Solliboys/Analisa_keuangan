<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHubunganKontakDaruratToKredit extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('hubungan_kontak_darurat', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'hubungan_kontak_darurat' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'nama_kontak_darurat'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('hubungan_kontak_darurat', 'kredit')) {
            $this->forge->dropColumn('kredit', 'hubungan_kontak_darurat');
        }
    }
}
