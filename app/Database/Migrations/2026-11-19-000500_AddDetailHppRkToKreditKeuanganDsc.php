<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailHppRkToKreditKeuanganDsc extends Migration
{
    public function up()
    {
        $existing = $this->db->getFieldNames('kredit_keuangan_dsc');
        $fields = [];

        if (!in_array('detail_hpp_rk', $existing)) {
            $fields['detail_hpp_rk'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'hpp_nominal_rk'
            ];
        }

        if (!in_array('total_aset_hpp_rk', $existing)) {
            $fields['total_aset_hpp_rk'] = [
                'type' => 'DECIMAL',
                'constraint' => '20,2',
                'null' => true,
                'default' => 0,
                'after' => 'detail_hpp_rk'
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('kredit_keuangan_dsc', $fields);
        }
    }

    public function down()
    {
        $existing = $this->db->getFieldNames('kredit_keuangan_dsc');
        $fields = [];

        if (in_array('detail_hpp_rk', $existing)) {
            $fields[] = 'detail_hpp_rk';
        }
        if (in_array('total_aset_hpp_rk', $existing)) {
            $fields[] = 'total_aset_hpp_rk';
        }

        if (!empty($fields)) {
            $this->forge->dropColumn('kredit_keuangan_dsc', $fields);
        }
    }
}
