<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditAnalisaModel extends Model
{
    protected $table            = 'kredit_analisa';
    protected $primaryKey       = 'analisa_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'analisa_jaminan',
        'business_review',
        'business_catatan',
        'detail_kebutuhan',
        'skor_kelayakan_ai',
        'rekomendasi_keputusan_ai',
        'rekomendasi_plafon_ai',
        'analisis_risiko_ai',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
