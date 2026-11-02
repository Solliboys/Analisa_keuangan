<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisUsahaAndModifyJenisDebitur extends Migration
{
    public function up()
    {
        // 1. Modify jenis_debitur to VARCHAR(50) so it supports Perorangan, PT, CV, etc.
        $this->forge->modifyColumn('kredit', [
            'jenis_debitur' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Individu',
                'null'       => false,
            ],
        ]);

        // 2. Add jenis_usaha column if not exists
        if (!$this->db->fieldExists('jenis_usaha', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'jenis_usaha' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => true,
                    'after'      => 'kepemilikan_domisili',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('jenis_usaha', 'kredit')) {
            $this->forge->dropColumn('kredit', 'jenis_usaha');
        }
    }
}
