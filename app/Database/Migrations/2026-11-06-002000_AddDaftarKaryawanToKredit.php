<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDaftarKaryawanToKredit extends Migration
{
    public function up()
    {
        $fields = [
            'daftar_karyawan' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'after'      => 'nomor_induk_berusaha_nib',
            ],
        ];
        $this->forge->addColumn('kredit', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('kredit', 'daftar_karyawan');
    }
}

