<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersJabatanVarchar extends Migration
{
    public function up()
    {
        // Ubah kolom jabatan dari ENUM menjadi VARCHAR(50) agar mendukung role fleksibel seperti Petugas SLIK
        $this->db->query("ALTER TABLE users MODIFY COLUMN jabatan VARCHAR(50) NOT NULL DEFAULT 'Pengusul'");
        
        // Update user slik yang sebelumnya kosong karena limitasi ENUM
        $this->db->table('users')->where('username', 'slik')->update([
            'jabatan' => 'Petugas SLIK',
            'status'  => 1
        ]);
    }

    public function down()
    {
    }
}
