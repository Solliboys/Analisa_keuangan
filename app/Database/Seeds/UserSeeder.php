<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'     => 'pengusul',
                'password'     => password_hash('pengusul123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Staff Pengusul Kredit',
                'jabatan'      => 'Pengusul',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'superadmin',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Super Administrator',
                'jabatan'      => 'Superadmin',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
