<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterJenisJaminanVarchar extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('kredit', [
            'jenis_jaminan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
                'default'    => 'SHM',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('kredit', [
            'jenis_jaminan' => [
                'type'       => "ENUM('On Hand','Take Over')",
                'null'       => false,
                'default'    => 'On Hand',
            ],
        ]);
    }
}
