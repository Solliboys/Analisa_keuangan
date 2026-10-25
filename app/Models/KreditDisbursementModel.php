<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditDisbursementModel extends Model
{
    protected $table            = 'kredit_disbursement';
    protected $primaryKey       = 'disbursement_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'provisi_persen',
        'provisi_nominal',
        'agent_persen',
        'agent_nominal',
        'admin_persen',
        'admin_nominal',
        'hold_angsuran_bulan',
        'hold_angsuran_nominal',
        'asuransi_jiwa',
        'asuransi_kebakaran',
        'total_potongan',
        'dana_bersih_cair',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
