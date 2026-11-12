<?php

namespace App\Models;

use CodeIgniter\Model;

class SlikModel extends Model
{
    protected $table            = 'slik';
    protected $primaryKey       = 'slik_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'nama_debitur',
        'plafon',
        'outstanding',
        'angsuran',
        'kelancaran',
        'tindak_lanjut',
        'bank_lembaga',
        'jenis_fasilitas',
        'keterangan',
        'is_verified',
        'diinput_oleh',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
