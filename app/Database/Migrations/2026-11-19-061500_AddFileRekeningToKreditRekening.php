<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFileRekeningToKreditRekening extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('file_rekening', 'kredit_rekening')) {
            $this->forge->addColumn('kredit_rekening', [
                'file_rekening' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'rata_rata_debit',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('file_rekening', 'kredit_rekening')) {
            $this->forge->dropColumn('kredit_rekening', 'file_rekening');
        }
    }
}
