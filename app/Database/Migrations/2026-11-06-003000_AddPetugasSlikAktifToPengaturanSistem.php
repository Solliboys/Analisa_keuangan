<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPetugasSlikAktifToPengaturanSistem extends Migration
{
    public function up()
    {
        $fields = [
            'petugas_slik_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => true,
                'after'      => 'ijin_pengusul_isi_slik'
            ],
        ];

        // Cek apakah kolom sudah ada
        if (!$this->db->fieldExists('petugas_slik_aktif', 'pengaturan_sistem')) {
            $this->forge->addColumn('pengaturan_sistem', $fields);
        }

        // Sinkronisasi data awal: jika ijin_pengusul_isi_slik == 0, maka petugas_slik_aktif = 1, jika 1 maka 0
        $this->db->query("UPDATE pengaturan_sistem SET petugas_slik_aktif = IF(ijin_pengusul_isi_slik = 0, 1, 0)");
    }

    public function down()
    {
        if ($this->db->fieldExists('petugas_slik_aktif', 'pengaturan_sistem')) {
            $this->forge->dropColumn('pengaturan_sistem', 'petugas_slik_aktif');
        }
    }
}

