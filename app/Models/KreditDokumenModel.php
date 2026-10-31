<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditDokumenModel extends Model
{
    protected $table            = 'kredit_dokumen';
    protected $primaryKey       = 'dokumen_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'kategori_dokumen',
        'nama_file_asli',
        'path_file',
        'ukuran_file_bytes',
        'tipe_file',
        'catatan_dokumen',
    ];

    protected $useTimestamps = false;
}
