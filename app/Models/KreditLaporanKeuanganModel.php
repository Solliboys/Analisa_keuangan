<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditLaporanKeuanganModel extends Model
{
    protected $table            = 'kredit_laporan_keuangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'tipe_pemohon',
        // Aset Lancar — input + kalkulasi
        'kas_bank',
        'kas_bank_neraca',
        'piutang_usaha',
        'piutang_usaha_neraca',
        'persediaan',
        'persediaan_neraca',
        'aset_lancar_lain',
        'aset_lancar_lain_neraca',
        'total_aset_lancar',
        // Aset Tetap
        'harga_perolehan_aset_tetap',
        'akumulasi_penyusutan',
        'umur_ekonomis_tahun',
        'penyusutan_tahunan',
        'aset_tetap_neto',
        'total_aset',
        // Liabilitas
        'utang_usaha',
        'utang_usaha_neraca',
        'utang_bank_pendek',
        'utang_jangka_panjang',
        'utang_jangka_panjang_neraca',
        'total_liabilitas',
        // Ekuitas
        'modal_disetor',
        'laba_ditahan_awal',
        'laba_ditahan_akhir',
        'prive_dividen',
        'laba_bersih_periode',
        'total_ekuitas',
        'total_liabilitas_ekuitas',
        // Saldo Awal
        'saldo_awal_piutang',
        'saldo_awal_persediaan',
        'saldo_awal_utang_usaha',
        'saldo_awal_aset_tetap',
        'saldo_awal_kas',
        // Asumsi
        'asumsi_dso',
        'asumsi_dio',
        'asumsi_dpo',
        // Laba Rugi
        'pendapatan',
        'hpp',
        'laba_kotor',
        'beban_gaji',
        'beban_sewa',
        'beban_operasional',
        'beban_lainnya',
        'beban_penyusutan',
        'laba_operasional',
        'pendapatan_lain',
        'beban_bunga',
        'laba_sebelum_pajak',
        'pajak',
        'laba_bersih',
        // Arus Kas — tambahan
        'kenaikan_piutang',
        'kenaikan_persediaan',
        'kenaikan_utang_usaha',
        'penyusutan_tahunan_ak',
        'pembelian_aset_tetap',
        'cicilan_pokok',
        'tambahan_modal',
        'pinjaman_baru',
        'kas_operasional',
        'kas_investasi',
        'kas_pendanaan',
        'kenaikan_bersih_kas',
        'kas_akhir',
        // Status Validasi
        'status_neraca',
        'selisih_neraca',
        'status_kas',
        'selisih_kas',
        // JSON & metadata
        'raw_data_json',
        'json_laporan',
        'generated_at',
        'generated_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
