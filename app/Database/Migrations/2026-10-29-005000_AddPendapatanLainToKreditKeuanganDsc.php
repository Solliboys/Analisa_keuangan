<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPendapatanLainToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        $fields = [];
        if (!$this->db->fieldExists('pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $fields['pendapatan_lain_rk'] = [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0.00,
                'after'      => 'pendapatan_rk',
            ];
        }

        if (!$this->db->fieldExists('ada_bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $fields['ada_bukti_pendapatan_lain_rk'] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'pendapatan_lain_rk',
            ];
        }

        if (!$this->db->fieldExists('bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $fields['bukti_pendapatan_lain_rk'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'ada_bukti_pendapatan_lain_rk',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('kredit_keuangan_dsc', $fields);
        }
    }

    public function down()
    {
        $drops = [];
        if ($this->db->fieldExists('bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $drops[] = 'bukti_pendapatan_lain_rk';
        }
        if ($this->db->fieldExists('ada_bukti_pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $drops[] = 'ada_bukti_pendapatan_lain_rk';
        }
        if ($this->db->fieldExists('pendapatan_lain_rk', 'kredit_keuangan_dsc')) {
            $drops[] = 'pendapatan_lain_rk';
        }

        if (!empty($drops)) {
            $this->forge->dropColumn('kredit_keuangan_dsc', $drops);
        }
    }
}
