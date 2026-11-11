<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlikUploadColumnsToKredit extends Migration
{
    public function up()
    {
        $fields = [
            'daftar_permohonan_slik' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'metode_slik' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'mandiri',
                'null'       => true,
            ],
            'berkas_slik_pdf' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'berkas_slik_txt' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'kesimpulan_kolektibilitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'slik_permohonan_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'slik_permohonan_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'slik_permohonan_hubungan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'slik_ktp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'slik_surat_kuasa_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ];

        foreach ($fields as $col => $def) {
            if (!$this->db->fieldExists($col, 'kredit')) {
                $this->forge->addColumn('kredit', [$col => $def]);
            }
        }
    }

    public function down()
    {
        $cols = [
            'daftar_permohonan_slik',
            'metode_slik',
            'berkas_slik_pdf',
            'berkas_slik_txt',
            'kesimpulan_kolektibilitas',
            'slik_permohonan_nama',
            'slik_permohonan_nik',
            'slik_permohonan_hubungan',
            'slik_ktp_file',
            'slik_surat_kuasa_file',
        ];

        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'kredit')) {
                $this->forge->dropColumn('kredit', $col);
            }
        }
    }
}

