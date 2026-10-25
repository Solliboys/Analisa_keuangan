<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditWebcheckModel extends Model
{
    protected $table            = 'kredit_webcheck';
    protected $primaryKey       = 'webcheck_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'sipp_keterangan',
        'sipp_file',
        'internet_keterangan',
        'internet_file',
        'watchlist_keterangan',
        'watchlist_file',
        'lelang_keterangan',
        'lelang_file',
        'negatif_keterangan',
        'negatif_file',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
