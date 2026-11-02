<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterStatusPengajuanAndApprovalVarchar extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('kredit', [
            'status_pengajuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Draft',
                'null'       => false,
            ],
        ]);

        $this->forge->modifyColumn('komite', [
            'status_approval' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pending',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // No down needed
    }
}
