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
            [
                'username'     => 'slik',
                'password'     => password_hash('slik123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Verifikasi SLIK',
                'jabatan'      => 'Petugas SLIK',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'reviewer',
                'password'     => password_hash('reviewer123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Senior Credit Reviewer',
                'jabatan'      => 'Reviewer',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'ceo',
                'password'     => password_hash('ceo123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Direktur Utama / CEO',
                'jabatan'      => 'CEO',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $row) {
            $existing = $this->db->table('users')->where('username', $row['username'])->get()->getRowArray();
            if ($existing) {
                $this->db->table('users')->where('username', $row['username'])->update($row);
            } else {
                $this->db->table('users')->insert($row);
            }
        }
    }
}
