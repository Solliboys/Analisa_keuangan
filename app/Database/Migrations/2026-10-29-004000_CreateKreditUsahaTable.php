<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKreditUsahaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kredit_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'is_utama' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'default'        => 0,
            ],
            'nama_usaha' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'bidang_usaha' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'jabatan' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => true,
            ],
            'lama_usaha' => [
                'type'           => 'VARCHAR',
                'constraint'     => 100,
                'null'           => true,
            ],
            'sumber_pembayaran' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'alamat_usaha' => [
                'type'           => 'TEXT',
                'null'           => true,
            ],
            'npwp_usaha' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'nomor_induk_berusaha_nib' => [
                'type'           => 'VARCHAR',
                'constraint'     => 100,
                'null'           => true,
            ],
            'created_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_at' => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kredit_id');
        $this->forge->createTable('kredit_usaha', true);
    }

    public function down()
    {
        $this->forge->dropTable('kredit_usaha', true);
    }
}
