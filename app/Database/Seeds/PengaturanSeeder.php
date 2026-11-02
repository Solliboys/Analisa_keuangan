<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        // 1. Inisialisasi pengaturan_sistem
        $existingSistem = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray();
        $sistemData = [
            'id'                    => 1,
            'nama_aplikasi'         => 'OTW Credit Engine & CAM',
            'slogan_aplikasi'       => 'Sistem Analisa Kredit AI & CAM Terintegrasi',
            'nama_lembaga'          => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi'         => '',
            'api_key'               => '',
            'model_ai'              => 'gemini-3.8-flash',
            'api_endpoint'          => 'https://generativelanguage.googleapis.com/v1beta/models',
            'prompt_sistem'         => 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).',
            'ijin_pengusul_isi_slik'=> 1,
            'alamat_lembaga'        => 'BPR Financial Center, Lantai 8, Jakarta Selatan',
            'email_lembaga'         => 'info@otwkredit.id',
            'telepon_lembaga'       => '(021) 5088-2900',
        ];

        if ($existingSistem) {
            // Update hanya jika kolom kosong
            $update = [];
            foreach ($sistemData as $k => $v) {
                if (empty($existingSistem[$k]) && !empty($v)) {
                    $update[$k] = $v;
                }
            }
            if (!empty($update)) {
                $this->db->table('pengaturan_sistem')->where('id', 1)->update($update);
            }
        } else {
            $this->db->table('pengaturan_sistem')->insert($sistemData);
        }

        // 2. Inisialisasi pengaturan_ai
        $existingAi = $this->db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray();
        $aiData = [
            'id'                => 1,
            'api_key'           => '',
            'model_ai'          => 'gemini-2.5-flash',
            'prompt_character'  => 'Analisis karakter debitur berdasarkan riwayat kredit dan integritas.',
            'prompt_capacity'   => 'Analisis kapasitas debitur berdasarkan perputaran rekening koran dan omset.',
            'prompt_capital'    => 'Analisis permodalan debitur berdasarkan rasio keuangan dan aset.',
            'prompt_collateral' => 'Analisis jaminan debitur berdasarkan nilai pasar, nilai likuidasi dan LTV.',
            'prompt_condition'  => 'Analisis prospek ekonomi debitur dan sektor usahanya.',
            'prompt_final'      => 'Berikan ringkasan rekomendasi keputusan akhir kredit beserta skor 1-100.',
            'diubah_oleh'       => null,
        ];

        if (!$existingAi) {
            $this->db->table('pengaturan_ai')->insert($aiData);
        }
    }
}
