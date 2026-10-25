<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditRekeningModel extends Model
{
    protected $table            = 'kredit_rekening';
    protected $primaryKey       = 'rekening_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'nama_bank',
        'periode',
        'nomor_rekening',
        'atas_nama',
        'total_mutasi_kredit',
        'total_mutasi_debit',
        'jumlah_bulan',
        'rata_rata_kredit',
        'rata_rata_debit',
    ];

    protected $useTimestamps = false;
}
