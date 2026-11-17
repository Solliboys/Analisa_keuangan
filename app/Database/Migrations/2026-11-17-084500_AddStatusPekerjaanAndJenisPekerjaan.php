<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusPekerjaanAndJenisPekerjaan extends Migration
{
    public function up()
    {
        // 1. Tambah kolom pada tabel kredit_usaha jika belum ada
        if ($this->db->tableExists('kredit_usaha')) {
            $fieldsUsaha = [];
            if (!$this->db->fieldExists('jenis_pekerjaan', 'kredit_usaha')) {
                $fieldsUsaha['jenis_pekerjaan'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'nama_usaha',
                ];
            }
            if (!$this->db->fieldExists('status_pekerjaan', 'kredit_usaha')) {
                $fieldsUsaha['status_pekerjaan'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'jenis_pekerjaan',
                ];
            }
            if (!empty($fieldsUsaha)) {
                $this->forge->addColumn('kredit_usaha', $fieldsUsaha);
            }
        }

        // 2. Tambah kolom pada tabel kredit jika belum ada
        if ($this->db->tableExists('kredit')) {
            $fieldsKredit = [];
            if (!$this->db->fieldExists('status_pekerjaan', 'kredit')) {
                $fieldsKredit['status_pekerjaan'] = [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'jabatan',
                ];
            }
            if (!empty($fieldsKredit)) {
                $this->forge->addColumn('kredit', $fieldsKredit);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('kredit_usaha')) {
            if ($this->db->fieldExists('jenis_pekerjaan', 'kredit_usaha')) {
                $this->forge->dropColumn('kredit_usaha', 'jenis_pekerjaan');
            }
            if ($this->db->fieldExists('status_pekerjaan', 'kredit_usaha')) {
                $this->forge->dropColumn('kredit_usaha', 'status_pekerjaan');
            }
        }

        if ($this->db->tableExists('kredit')) {
            if ($this->db->fieldExists('status_pekerjaan', 'kredit')) {
                $this->forge->dropColumn('kredit', 'status_pekerjaan');
            }
        }
    }
}
