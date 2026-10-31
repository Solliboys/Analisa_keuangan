<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTempatLahirTanggalLahirToKredit extends Migration
{
    public function up()
    {
        $fields = [
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'tempat_tanggal_lahir',
            ],
            'tanggal_lahir' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'tempat_lahir',
            ],
        ];

        // Check if columns already exist before adding
        if (!$this->db->fieldExists('tempat_lahir', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'tempat_lahir' => $fields['tempat_lahir'],
            ]);
        }

        if (!$this->db->fieldExists('tanggal_lahir', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'tanggal_lahir' => $fields['tanggal_lahir'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('tanggal_lahir', 'kredit')) {
            $this->forge->dropColumn('kredit', 'tanggal_lahir');
        }
        if ($this->db->fieldExists('tempat_lahir', 'kredit')) {
            $this->forge->dropColumn('kredit', 'tempat_lahir');
        }
    }
}
