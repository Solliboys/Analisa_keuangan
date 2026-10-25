<?php

namespace App\Models;

use CodeIgniter\Model;

class AgunanModel extends Model
{
    protected $table            = 'agunan';
    protected $primaryKey       = 'agunan_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'alamat_agunan',
        'jenis_agunan',
        'nomor_sertifikat',
        'luas_tanah_m2',
        'luas_bangunan_m2',
        'status_imb',
        'pemilik_agunan',
        'hubungan_kepemilikan',
        'lama_kepemilikan',
        'nilai_pasar_mv',
        'nilai_njop',
        'persen_likuidasi',
        'nilai_likuidasi_lv',
        'coverage_mv',
        'coverage_lv',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
