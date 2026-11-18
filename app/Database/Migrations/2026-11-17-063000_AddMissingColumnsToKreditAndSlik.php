<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToKreditAndSlik extends Migration
{
    public function up()
    {
        // 1. Tambah kolom yang hilang di tabel kredit
        $fieldsKredit = [
            'daftar_permohonan_slik' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'after'      => 'ringkasan_slik',
            ],
            'metode_slik' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'manual',
                'after'      => 'daftar_permohonan_slik',
            ],
            'berkas_slik_pdf' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'metode_slik',
            ],
            'berkas_slik_txt' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'berkas_slik_pdf',
            ],
            'kesimpulan_kolektibilitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'berkas_slik_txt',
            ],
            'slik_permohonan_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'kesimpulan_kolektibilitas',
            ],
            'slik_permohonan_nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'slik_permohonan_nama',
            ],
            'slik_permohonan_hubungan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'slik_permohonan_nik',
            ],
            'slik_ktp_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'slik_permohonan_hubungan',
            ],
            'slik_surat_kuasa_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'slik_ktp_file',
            ],
        ];

        // Cek satu per satu agar tidak duplikat jika sudah ada
        foreach ($fieldsKredit as $col => $def) {
            if (!$this->db->fieldExists($col, 'kredit')) {
                $this->forge->addColumn('kredit', [$col => $def]);
            }
        }

        // 2. Tambah kolom di kredit_laporan_keuangan
        if (!$this->db->fieldExists('utang_usaha_neraca', 'kredit_laporan_keuangan')) {
            $this->forge->addColumn('kredit_laporan_keuangan', [
                'utang_usaha_neraca' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'null'       => true,
                    'default'    => 0.00,
                    'after'      => 'utang_usaha',
                ],
            ]);
        }

        // 3. Tambah kolom di tabel slik
        $fieldsSlik = [
            'angsuran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'default'    => 0.00,
                'after'      => 'outstanding',
            ],
            'tindak_lanjut' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'kelancaran',
            ],
            'jenis_fasilitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'bank_lembaga',
            ],
        ];

        foreach ($fieldsSlik as $col => $def) {
            if (!$this->db->fieldExists($col, 'slik')) {
                $this->forge->addColumn('slik', [$col => $def]);
            }
        }
    }

    public function down()
    {
        $colsKredit = [
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
        foreach ($colsKredit as $col) {
            if ($this->db->fieldExists($col, 'kredit')) {
                $this->forge->dropColumn('kredit', $col);
            }
        }

        if ($this->db->fieldExists('utang_usaha_neraca', 'kredit_laporan_keuangan')) {
            $this->forge->dropColumn('kredit_laporan_keuangan', 'utang_usaha_neraca');
        }

        $colsSlik = ['angsuran', 'tindak_lanjut', 'jenis_fasilitas'];
        foreach ($colsSlik as $col) {
            if ($this->db->fieldExists($col, 'slik')) {
                $this->forge->dropColumn('slik', $col);
            }
        }
    }
}
