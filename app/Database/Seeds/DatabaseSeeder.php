<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed akun default pengguna (superadmin, reviewer, ceo, slik, pengusul)
        $this->call(UserSeeder::class);

        // 2. Seed pengaturan awal sistem dan AI
        $this->call(PengaturanSeeder::class);
    }
}
