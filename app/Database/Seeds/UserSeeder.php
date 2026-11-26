<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1 Superadmin
            [
                'username'     => 'superadmin',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Super Administrator Sistem',
                'jabatan'      => 'Superadmin',
                'email'        => 'admin@otwkredit.id',
                'nomor_hp'     => '081234567890',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // 1 CEO
            [
                'username'     => 'ceo',
                'password'     => password_hash('ceo123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Dr. H. Hendra Wijaya, S.E., M.M.',
                'jabatan'      => 'CEO',
                'email'        => 'ceo@otwkredit.id',
                'nomor_hp'     => '081122334455',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // 1 Petugas SLIK
            [
                'username'     => 'slik',
                'password'     => password_hash('slik123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Ahmad Fauzi, S.Kom.',
                'jabatan'      => 'Petugas SLIK',
                'email'        => 'slik@otwkredit.id',
                'nomor_hp'     => '081399887766',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // 4 Pengusul
            [
                'username'     => 'pengusul',
                'password'     => password_hash('pengusul123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Budi Pratama, S.E.',
                'jabatan'      => 'Pengusul',
                'email'        => 'budi.pratama@otwkredit.id',
                'nomor_hp'     => '081211112221',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'pengusul2',
                'password'     => password_hash('pengusul123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Siti Rahmawati, S.E.',
                'jabatan'      => 'Pengusul',
                'email'        => 'siti.rahmawati@otwkredit.id',
                'nomor_hp'     => '081211112222',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'pengusul3',
                'password'     => password_hash('pengusul123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Dedi Kurniawan, S.E.',
                'jabatan'      => 'Pengusul',
                'email'        => 'dedi.kurniawan@otwkredit.id',
                'nomor_hp'     => '081211112223',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'pengusul4',
                'password'     => password_hash('pengusul123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Maya Anggraini, S.M.',
                'jabatan'      => 'Pengusul',
                'email'        => 'maya.anggraini@otwkredit.id',
                'nomor_hp'     => '081211112224',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // 4 Reviewer
            [
                'username'     => 'reviewer',
                'password'     => password_hash('reviewer123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Rian Hidayat, S.E., CRA',
                'jabatan'      => 'Reviewer',
                'email'        => 'rian.hidayat@otwkredit.id',
                'nomor_hp'     => '081322223331',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'reviewer2',
                'password'     => password_hash('reviewer123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Ratna Kusuma Dewi, S.E., CRP',
                'jabatan'      => 'Reviewer',
                'email'        => 'ratna.kusuma@otwkredit.id',
                'nomor_hp'     => '081322223332',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'reviewer3',
                'password'     => password_hash('reviewer123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Agus Setiawan, M.M., CRA',
                'jabatan'      => 'Reviewer',
                'email'        => 'agus.setiawan@otwkredit.id',
                'nomor_hp'     => '081322223333',
                'status'       => 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'reviewer4',
                'password'     => password_hash('reviewer123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Dewi Lestari, S.E., Ak.',
                'jabatan'      => 'Reviewer',
                'email'        => 'dewi.lestari@otwkredit.id',
                'nomor_hp'     => '081322223334',
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
