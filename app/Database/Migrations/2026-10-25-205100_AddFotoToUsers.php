<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'nama_lengkap'
            ]
        ];
        if (!$this->db->fieldExists('foto', 'users')) {
            $this->forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('foto', 'users')) {
            $this->forge->dropColumn('users', 'foto');
        }
    }
}
