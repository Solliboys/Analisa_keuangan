<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusSlikToKreditAndSlik extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('status_slik', 'kredit')) {
            $this->forge->addColumn('kredit', [
                'status_slik' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'Belum Terbit', // Belum Terbit, Terbit
                    'after'      => 'ringkasan_slik'
                ],
                'hasil_slik' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'status_slik'
                ],
                'slik_verified_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'hasil_slik'
                ],
                'slik_verified_by' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'slik_verified_at'
                ],
            ]);
        }

        if (!$this->db->fieldExists('is_verified', 'slik')) {
            $this->forge->addColumn('slik', [
                'is_verified' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0, // 0: Usulan Pengusul, 1: Disetujui Petugas SLIK
                    'after'      => 'keterangan'
                ],
                'diinput_oleh' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'pengusul',
                    'after'      => 'is_verified'
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('status_slik', 'kredit')) {
            $this->forge->dropColumn('kredit', ['status_slik', 'hasil_slik', 'slik_verified_at', 'slik_verified_by']);
        }
        if ($this->db->fieldExists('is_verified', 'slik')) {
            $this->forge->dropColumn('slik', ['is_verified', 'diinput_oleh']);
        }
    }
}
