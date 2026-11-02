<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingReferensiModel extends Model
{
    protected $table            = 'setting_referensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis_referensi',
        'keterangan_referensi',
        'tipe_referensi',
        'color_referensi',
        'urutan',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'jenis_referensi'      => 'required|min_length[2]|max_length[50]',
        'keterangan_referensi' => 'required|min_length[1]|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil daftar unik jenis referensi yang ada
     */
    public function getDistinctJenis()
    {
        return $this->select('jenis_referensi')
                    ->distinct()
                    ->orderBy('jenis_referensi', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil referensi berdasarkan jenis
     */
    public function getByJenis($jenis, $onlyActive = true)
    {
        $builder = $this->where('jenis_referensi', $jenis);
        if ($onlyActive) {
            $builder->where('is_active', 1);
        }
        return $builder->orderBy('urutan', 'ASC')
                       ->orderBy('id', 'ASC')
                       ->findAll();
    }
}
