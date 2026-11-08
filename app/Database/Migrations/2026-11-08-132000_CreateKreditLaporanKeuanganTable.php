<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKreditLaporanKeuanganTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kredit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
            ],
            'tipe_pemohon' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'badan_usaha', // 'badan_usaha' atau 'karyawan'
            ],
            // Aset Lancar
            'kas_bank' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'piutang_usaha' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'persediaan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'aset_lancar_lain' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'total_aset_lancar' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Aset Tetap
            'harga_perolehan_aset_tetap' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'akumulasi_penyusutan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'umur_ekonomis_tahun' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 5,
            ],
            'penyusutan_tahunan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'aset_tetap_neto' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'total_aset' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Liabilitas
            'utang_usaha' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'utang_bank_pendek' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'utang_jangka_panjang' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'total_liabilitas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Ekuitas
            'modal_disetor' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_ditahan_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'prive_dividen' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_bersih_periode' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'total_ekuitas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'total_liabilitas_ekuitas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Saldo Awal Periode
            'saldo_awal_piutang' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'saldo_awal_persediaan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'saldo_awal_utang_usaha' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'saldo_awal_aset_tetap' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'saldo_awal_kas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Asumsi
            'asumsi_dso' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 30,
            ],
            'asumsi_dio' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 45,
            ],
            'asumsi_dpo' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 30,
            ],
            // Ringkasan Laba Rugi
            'pendapatan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'hpp' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_kotor' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'beban_operasional' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'beban_gaji' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'beban_lainnya' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_operasional' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'pendapatan_lain' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'beban_bunga' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_sebelum_pajak' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'pajak' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'laba_bersih' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Arus Kas (Metode Tidak Langsung)
            'kas_operasional' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'kas_investasi' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'kas_pendanaan' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'kenaikan_bersih_kas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'kas_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            // Status Validasi
            'status_neraca' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'Seimbang',
            ],
            'selisih_neraca' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'status_kas' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'Cocok',
            ],
            'selisih_kas' => [
                'type'       => 'DECIMAL',
                'constraint' => '20,2',
                'default'    => 0,
            ],
            'raw_data_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kredit_id');
        $this->forge->createTable('kredit_laporan_keuangan', true);
    }

    public function down()
    {
        $this->forge->dropTable('kredit_laporan_keuangan', true);
    }
}

