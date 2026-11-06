<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFeatureFlagsToPengaturanSistem extends Migration
{
    public function up()
    {
        $fields = [
            'fitur_ai_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'petugas_slik_aktif',
                'comment'    => '1=Fitur AI 5C & Web Check Aktif, 0=Nonaktif (Manual Only)'
            ],
            'fitur_badan_usaha_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'fitur_ai_aktif',
                'comment'    => '1=Kredit Badan Usaha (PT/CV) Aktif, 0=Perorangan Only'
            ],
            'mode_approval_komite' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'cepat',
                'after'      => 'fitur_badan_usaha_aktif',
                'comment'    => 'cepat = Siapa cepat (Reviewer / CEO), bertingkat = Hierarki Reviewer lalu CEO'
            ],
            'fitur_disbursement_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'mode_approval_komite',
                'comment'    => '1=Tab Disbursement & Repayment Aktif, 0=Sembunyikan'
            ],
            'mode_strict_dokumen' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'fitur_disbursement_aktif',
                'comment'    => '1=Wajib upload KTP & berkas sebelum submit, 0=Fleksibel'
            ],
        ];

        $this->forge->addColumn('pengaturan_sistem', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan_sistem', [
            'fitur_ai_aktif',
            'fitur_badan_usaha_aktif',
            'mode_approval_komite',
            'fitur_disbursement_aktif',
            'mode_strict_dokumen'
        ]);
    }
}

