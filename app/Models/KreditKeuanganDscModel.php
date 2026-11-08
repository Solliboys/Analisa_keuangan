<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditKeuanganDscModel extends Model
{
    protected $table            = 'kredit_keuangan_dsc';
    protected $primaryKey       = 'keuangan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        // === LABA RUGI REKENING KORAN (Badan Usaha) ===
        'pendapatan_rk',
        'pendapatan_lain_rk',
        'keterangan_pendapatan_lain_rk',
        'ada_bukti_pendapatan_lain_rk',
        'bukti_pendapatan_lain_rk',
        'hpp_persen_rk',
        'hpp_nominal_rk',
        'laba_kotor_rk',
        'biaya_hidup_persen_rk',
        'biaya_hidup_nominal_rk',
        'beban_lainnya_persen_rk',
        'beban_lainnya_nominal_rk',
        'detail_beban_lainnya_rk',
        'laba_bersih_rk',
        // === LABA RUGI ITEM VERIFIKASI (Perorangan) ===
        'pendapatan_iv',
        'hpp_persen_iv',
        'hpp_nominal_iv',
        'laba_kotor_iv',
        'biaya_hidup_persen_iv',
        'biaya_hidup_nominal_iv',
        'beban_lainnya_persen_iv',
        'beban_lainnya_nominal_iv',
        'laba_bersih_iv',
        // === BEBAN TAMBAHAN ===
        'beban_depresiasi',
        'beban_bunga_bank',
        'beban_pajak',
        'laba_bersih_setelah_pajak',
        // === ANGSURAN & DSC ===
        'angsuran_balloon',
        'angsuran_installment',
        'angsuran_dipilih',
        'rasio_dsc_rk',
        'rasio_dsc_iv',
        'status_dsc_rk',
        'status_dsc_iv',
        // === NERACA — SALDO AKHIR PERIODE ===
        'neraca_kas',
        'neraca_piutang_usaha',
        'neraca_hari_piutang_dso',
        'neraca_persediaan',
        'neraca_hari_stok_dio',
        'neraca_aset_lancar',
        'neraca_aset_tetap_bruto',
        'neraca_akumulasi_penyusutan',
        'neraca_aset_tetap_netto',
        'neraca_total_aset',
        'neraca_utang_usaha',
        'neraca_hari_utang_dpo',
        'neraca_utang_bank_lancar',
        'neraca_beban_akrual',
        'neraca_total_liabilitas_lancar',
        'neraca_utang_bank_panjang',
        'neraca_total_liabilitas',
        'neraca_modal_disetor',
        'neraca_laba_ditahan',
        'neraca_total_ekuitas',
        // === NERACA — SALDO AWAL PERIODE ===
        'neraca_awal_kas',
        'neraca_awal_piutang',
        'neraca_awal_persediaan',
        'neraca_awal_aset_tetap',
        'neraca_awal_akum_penyusutan',
        'neraca_awal_utang_usaha',
        'neraca_awal_utang_bank',
        'neraca_awal_modal_disetor',
        'neraca_awal_laba_ditahan',
        // === INPUT NERACA (Form Tab 9 — Badan Usaha) ===
        'input_piutang_akhir',
        'input_persediaan_akhir',
        'input_utang_usaha_akhir',
        'input_capex',
        'input_capex_umur_ekonomis',
        'input_pinjaman_baru',
        'input_pembayaran_pokok',
        'input_beban_bunga',
        'input_beban_pajak',
        'input_setoran_modal',
        'input_prive_dividen',
        // Input Perorangan
        'input_kas_tabungan_awal',
        'input_aset_emas',
        'input_aset_kendaraan',
        'input_aset_properti',
        'input_total_utang',
        'input_pengeluaran_hidup',
        // === ARUS KAS ===
        'cf_operasional',
        'cf_investasi',
        'cf_pendanaan',
        'cf_kenaikan_bersih',
        'cf_kas_akhir',
        // === RASIO ===
        'rasio_current_ratio',
        'rasio_der',
        'rasio_gpm',
        'rasio_npm',
        'rasio_roa',
        // === STATUS LAPORAN ===
        'is_laporan_generated',
        'laporan_json_data',
        'laporan_generated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
