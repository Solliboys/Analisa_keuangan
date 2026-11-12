<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSaldoAwalToKreditRekening extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('saldo_awal', 'kredit_rekening')) {
            $this->forge->addColumn('kredit_rekening', [
                'saldo_awal' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '20,2',
                    'default'    => 0.00,
                    'after'      => 'atas_nama'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('saldo_awal', 'kredit_rekening')) {
            $this->forge->dropColumn('kredit_rekening', 'saldo_awal');
        }
    }
}

