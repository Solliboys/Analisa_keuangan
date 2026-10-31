<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditUsahaModel extends Model
{
    protected $table            = 'kredit_usaha';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'is_utama',
        'nama_usaha',
        'bidang_usaha',
        'jabatan',
        'lama_usaha',
        'sumber_pembayaran',
        'alamat_usaha',
        'npwp_usaha',
        'nomor_induk_berusaha_nib',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
