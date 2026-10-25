<?php

namespace App\Models;

use CodeIgniter\Model;

class KomiteModel extends Model
{
    protected $table            = 'komite';
    protected $primaryKey       = 'komite_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kredit_id',
        'user_id',
        'role_approver',
        'nama_approver',
        'status_approval',
        'tanggal_approval',
        'catatan_approval',
        'file_dokumen_cam_pdf',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
