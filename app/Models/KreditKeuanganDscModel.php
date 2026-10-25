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
        'pendapatan_rk',
        'hpp_persen_rk',
        'hpp_nominal_rk',
        'laba_kotor_rk',
        'biaya_hidup_persen_rk',
        'biaya_hidup_nominal_rk',
        'beban_lainnya_persen_rk',
        'beban_lainnya_nominal_rk',
        'laba_bersih_rk',
        'pendapatan_iv',
        'hpp_persen_iv',
        'hpp_nominal_iv',
        'laba_kotor_iv',
        'biaya_hidup_persen_iv',
        'biaya_hidup_nominal_iv',
        'beban_lainnya_persen_iv',
        'beban_lainnya_nominal_iv',
        'laba_bersih_iv',
        'angsuran_balloon',
        'angsuran_installment',
        'angsuran_dipilih',
        'rasio_dsc_rk',
        'rasio_dsc_iv',
        'status_dsc_rk',
        'status_dsc_iv',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
