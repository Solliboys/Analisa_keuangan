<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIjinSlikPengusulToPengaturanSistem extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('ijin_pengusul_isi_slik', 'pengaturan_sistem')) {
            $this->forge->addColumn('pengaturan_sistem', [
                'ijin_pengusul_isi_slik' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1, // 1 = Aktif (Pengusul bisa mengisi SLIK), 0 = Nonaktif (Khusus Petugas SLIK)
                    'after'      => 'prompt_sistem'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('ijin_pengusul_isi_slik', 'pengaturan_sistem')) {
            $this->forge->dropColumn('pengaturan_sistem', 'ijin_pengusul_isi_slik');
        }
    }
}
