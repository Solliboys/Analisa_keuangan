<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamaKontakDaruratToKredit extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('nama_kontak_darurat', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'nama_kontak_darurat' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'nomor_emergency'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nama_kontak_darurat', 'kredit')) {
            $this->forge->dropColumn('kredit', 'nama_kontak_darurat');
        }
    }
}
