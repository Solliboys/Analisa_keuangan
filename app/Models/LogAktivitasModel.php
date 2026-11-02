<?php

namespace App\Models;

use CodeIgniter\Model;

class LogAktivitasModel extends Model
{
    protected $table            = 'log_aktivitas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nama_user',
        'role',
        'aktivitas',
        'modul',
        'status',
        'ip_address',
        'user_agent',
        'detail',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Helper statis untuk mencatat aktivitas log ke sistem
     */
    public static function catat(string $aktivitas, string $modul = 'SISTEM', string $status = 'SUCCESS', $detail = null, ?int $userId = null, ?string $namaUser = null, ?string $role = null): bool
    {
        try {
            $session = session();
            $request = service('request');

            $uId   = $userId ?? $session->get('user_id');
            $uName = $namaUser ?? $session->get('nama_lengkap') ?? $session->get('username') ?? 'System/Guest';
            $uRole = $role ?? $session->get('jabatan') ?? 'System';

            $ipAddress = $request ? $request->getIPAddress() : '127.0.0.1';
            $userAgent = $request && $request->getUserAgent() ? (string)$request->getUserAgent() : 'CLI/Agent';

            $detailStr = null;
            if (is_array($detail) || is_object($detail)) {
                $detailStr = json_encode($detail, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            } elseif (is_string($detail)) {
                $detailStr = $detail;
            }

            $model = new self();
            return (bool)$model->insert([
                'user_id'    => $uId,
                'nama_user'  => $uName,
                'role'       => $uRole,
                'aktivitas'  => $aktivitas,
                'modul'      => strtoupper($modul),
                'status'     => strtoupper($status),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'detail'     => $detailStr,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat log aktivitas: ' . $e->getMessage());
            return false;
        }
    }
}
