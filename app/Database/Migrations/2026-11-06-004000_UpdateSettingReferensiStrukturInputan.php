<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSettingReferensiStrukturInputan extends Migration
{
    public function up()
    {
        // 1. Tambah kolom is_locked jika belum ada
        if (!$this->db->fieldExists('is_locked', 'setting_referensi')) {
            $this->forge->addColumn('setting_referensi', [
                'is_locked' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'is_active'
                ]
            ]);
        }

        // 2. Bersihkan baris aneh (seperti "kartu keluarga" di kre_jaminan)
        $this->db->query("DELETE FROM setting_referensi WHERE jenis_referensi = 'kre_jaminan' AND keterangan_referensi LIKE '%kartu keluarga%'");

        // 3. Update status is_locked untuk kategori sistem bawaan (tidak boleh dihapus)
        $systemLocked = ['jns_entitas', 'kre_sts', 'slik_hsl', 'target_kategori', 'target_kode', 'kre_kepkomite'];
        $this->db->table('setting_referensi')
            ->whereIn('jenis_referensi', $systemLocked)
            ->update(['is_locked' => 1]);

        // Kategori dinamis (bebas tambah/edit/hapus)
        $flexible = ['kre_jaminan', 'kre_jns', 'kre_tujuan', 'kre_lampberkas', 'kre_referral', 'slik_keterkaitan'];
        $this->db->table('setting_referensi')
            ->whereIn('jenis_referensi', $flexible)
            ->update(['is_locked' => 0]);

        // 4. Update data slik_keterkaitan agar persis dengan struktur formulir Tab 6 saat ini
        $this->db->query("DELETE FROM setting_referensi WHERE jenis_referensi = 'slik_keterkaitan'");
        $slikKeterkaitan = [
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Pemohon', 'tipe_referensi' => 'pemohon', 'color_referensi' => 'primary', 'urutan' => 1, 'is_locked' => 0],
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Pasangan', 'tipe_referensi' => 'pasangan', 'color_referensi' => 'info', 'urutan' => 2, 'is_locked' => 0],
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Penjamin', 'tipe_referensi' => 'penjamin', 'color_referensi' => 'warning', 'urutan' => 3, 'is_locked' => 0],
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Pengurus / Direksi', 'tipe_referensi' => 'pengurus', 'color_referensi' => 'primary', 'urutan' => 4, 'is_locked' => 0],
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Pemegang Saham', 'tipe_referensi' => 'pemegang_saham', 'color_referensi' => 'secondary', 'urutan' => 5, 'is_locked' => 0],
            ['jenis_referensi' => 'slik_keterkaitan', 'keterangan_referensi' => 'Pemilik Jaminan', 'tipe_referensi' => 'pemilik_agunan', 'color_referensi' => 'success', 'urutan' => 6, 'is_locked' => 0],
        ];
        $this->db->table('setting_referensi')->insertBatch($slikKeterkaitan);

        // 5. Tambah referensi skema pembayaran (kre_skema) jika belum ada
        $cekSkema = $this->db->table('setting_referensi')->where('jenis_referensi', 'kre_skema')->countAllResults();
        if ($cekSkema === 0) {
            $dataSkema = [
                ['jenis_referensi' => 'kre_skema', 'keterangan_referensi' => 'Installment (Pokok + Bunga)', 'tipe_referensi' => 'installment', 'color_referensi' => 'primary', 'urutan' => 1, 'is_locked' => 0],
                ['jenis_referensi' => 'kre_skema', 'keterangan_referensi' => 'Balloon Payment (Bunga Bulanan, Pokok Akhir)', 'tipe_referensi' => 'balloon', 'color_referensi' => 'warning', 'urutan' => 2, 'is_locked' => 0],
                ['jenis_referensi' => 'kre_skema', 'keterangan_referensi' => 'Grace Period (Tenggang Pokok)', 'tipe_referensi' => 'grace_period', 'color_referensi' => 'info', 'urutan' => 3, 'is_locked' => 0],
                ['jenis_referensi' => 'kre_skema', 'keterangan_referensi' => 'Step Up', 'tipe_referensi' => 'step_up', 'color_referensi' => 'secondary', 'urutan' => 4, 'is_locked' => 0],
            ];
            $this->db->table('setting_referensi')->insertBatch($dataSkema);
        }

        // 6. Tambah referensi Bentuk Badan Usaha (bu_bentuk) jika belum ada
        $cekBu = $this->db->table('setting_referensi')->where('jenis_referensi', 'bu_bentuk')->countAllResults();
        if ($cekBu === 0) {
            $dataBu = [
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'PT (Perseroan Terbatas)', 'tipe_referensi' => 'pt', 'color_referensi' => 'primary', 'urutan' => 1, 'is_locked' => 0],
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'CV (Persekutuan Komanditer)', 'tipe_referensi' => 'cv', 'color_referensi' => 'info', 'urutan' => 2, 'is_locked' => 0],
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'Koperasi', 'tipe_referensi' => 'koperasi', 'color_referensi' => 'success', 'urutan' => 3, 'is_locked' => 0],
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'Yayasan', 'tipe_referensi' => 'yayasan', 'color_referensi' => 'secondary', 'urutan' => 4, 'is_locked' => 0],
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'Firma', 'tipe_referensi' => 'firma', 'color_referensi' => 'warning', 'urutan' => 5, 'is_locked' => 0],
                ['jenis_referensi' => 'bu_bentuk', 'keterangan_referensi' => 'Badan Usaha Lainnya', 'tipe_referensi' => 'lainnya', 'color_referensi' => 'secondary', 'urutan' => 6, 'is_locked' => 0],
            ];
            $this->db->table('setting_referensi')->insertBatch($dataBu);
        }

        // 7. Tambah referensi Status Kepemilikan Tempat Usaha / Domisili (dom_status) jika belum ada
        $cekDom = $this->db->table('setting_referensi')->where('jenis_referensi', 'dom_status')->countAllResults();
        if ($cekDom === 0) {
            $dataDom = [
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Milik Sendiri', 'tipe_referensi' => 'milik_sendiri', 'color_referensi' => 'success', 'urutan' => 1, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Sewa / Kontrak', 'tipe_referensi' => 'sewa_kontrak', 'color_referensi' => 'warning', 'urutan' => 2, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Milik Orang Tua / Keluarga', 'tipe_referensi' => 'milik_keluarga', 'color_referensi' => 'info', 'urutan' => 3, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Milik Grup Perusahaan', 'tipe_referensi' => 'milik_grup', 'color_referensi' => 'primary', 'urutan' => 4, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Hak Guna Bangunan (HGB)', 'tipe_referensi' => 'hgb', 'color_referensi' => 'secondary', 'urutan' => 5, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Rumah Dinas', 'tipe_referensi' => 'dinas', 'color_referensi' => 'info', 'urutan' => 6, 'is_locked' => 0],
                ['jenis_referensi' => 'dom_status', 'keterangan_referensi' => 'Lainnya', 'tipe_referensi' => 'lainnya', 'color_referensi' => 'secondary', 'urutan' => 7, 'is_locked' => 0],
            ];
            $this->db->table('setting_referensi')->insertBatch($dataDom);
        }

        // 8. Tambah referensi Komponen Biaya Pencairan (disb_biaya) jika belum ada
        $cekDisb = $this->db->table('setting_referensi')->where('jenis_referensi', 'disb_biaya')->countAllResults();
        if ($cekDisb === 0) {
            $dataDisb = [
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Provisi', 'tipe_referensi' => 'provisi', 'color_referensi' => 'primary', 'urutan' => 1, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Admin Fee', 'tipe_referensi' => 'admin_fee', 'color_referensi' => 'info', 'urutan' => 2, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Agent Fee', 'tipe_referensi' => 'agent_fee', 'color_referensi' => 'secondary', 'urutan' => 3, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Hold Angsuran', 'tipe_referensi' => 'hold_angsuran', 'color_referensi' => 'warning', 'urutan' => 4, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Premi Asuransi Jiwa', 'tipe_referensi' => 'asuransi_jiwa', 'color_referensi' => 'danger', 'urutan' => 5, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Premi Asuransi Kebakaran / Agunan', 'tipe_referensi' => 'asuransi_kebakaran', 'color_referensi' => 'danger', 'urutan' => 6, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Biaya Notaris & APHT', 'tipe_referensi' => 'notaris', 'color_referensi' => 'primary', 'urutan' => 7, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Biaya Appraisal', 'tipe_referensi' => 'appraisal', 'color_referensi' => 'info', 'urutan' => 8, 'is_locked' => 0],
                ['jenis_referensi' => 'disb_biaya', 'keterangan_referensi' => 'Biaya Materai & Administrasi', 'tipe_referensi' => 'materai', 'color_referensi' => 'secondary', 'urutan' => 9, 'is_locked' => 0],
            ];
            $this->db->table('setting_referensi')->insertBatch($dataDisb);
        }

        // 9. Pastikan kre_kepkomite memiliki Revisi ke AO
        $cekRevisi = $this->db->table('setting_referensi')->where('jenis_referensi', 'kre_kepkomite')->like('keterangan_referensi', 'Revisi')->countAllResults();
        if ($cekRevisi === 0) {
            $this->db->table('setting_referensi')->insert([
                'jenis_referensi'      => 'kre_kepkomite',
                'keterangan_referensi' => 'Revisi ke AO',
                'tipe_referensi'       => 'revision',
                'color_referensi'      => 'warning',
                'urutan'               => 5,
                'is_locked'            => 1
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('is_locked', 'setting_referensi')) {
            $this->forge->dropColumn('setting_referensi', 'is_locked');
        }
    }
}

