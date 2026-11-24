<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlamatEmailTeleponToPengaturanSistem extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('slogan_aplikasi', 'pengaturan_sistem')) {
            $fields['slogan_aplikasi'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'Sistem Analisa Kredit',
            ];
        }

        if (!$this->db->fieldExists('alamat_lembaga', 'pengaturan_sistem')) {
            $fields['alamat_lembaga'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => 'Financial Center, Lantai 8, Jakarta Selatan',
            ];
        }

        if (!$this->db->fieldExists('email_lembaga', 'pengaturan_sistem')) {
            $fields['email_lembaga'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => 'info@otwkredit.id',
            ];
        }

        if (!$this->db->fieldExists('telepon_lembaga', 'pengaturan_sistem')) {
            $fields['telepon_lembaga'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => '(021) 5088-2900',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('pengaturan_sistem', $fields);
        }
    }

    public function down()
    {
        $cols = ['slogan_aplikasi', 'alamat_lembaga', 'email_lembaga', 'telepon_lembaga'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'pengaturan_sistem')) {
                $this->forge->dropColumn('pengaturan_sistem', $col);
            }
        }
    }
}
