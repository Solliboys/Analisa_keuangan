<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterSlikKelancaranVarchar extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('slik', [
            'kelancaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Lancar',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Tetap VARCHAR atau rollback bila diperlukan
    }
}
