<?php

namespace App\Controllers;

use App\Models\LogAktivitasModel;

class LogAktivitas extends BaseController
{
    /**
     * Menampilkan Halaman Log Aktivitas Sistem dengan Tampilan Layout seperti Pengajuan
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $logModel = new LogAktivitasModel();
        $db       = \Config\Database::connect();

        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => ''
        ];

        // Ambil seluruh log aktivitas terbaru
        $daftarLog = $logModel->orderBy('created_at', 'DESC')->findAll(500);

        // Hitung metrik ringkasan
        $todayStr = date('Y-m-d');
        $totalLogs   = count($daftarLog);
        $todayLogs   = 0;
        $successLogs = 0;
        $warningLogs = 0;

        foreach ($daftarLog as $lg) {
            $tgl = substr($lg['created_at'] ?? '', 0, 10);
            if ($tgl === $todayStr) {
                $todayLogs++;
            }

            $st = strtoupper($lg['status'] ?? 'SUCCESS');
            if ($st === 'SUCCESS' || $st === 'INFO') {
                $successLogs++;
            } else {
                $warningLogs++;
            }
        }

        $data = [
            'title'             => 'Log Aktivitas Sistem & Audit Trail',
            'nama_lengkap'      => session()->get('nama_lengkap') ?? 'Pengguna',
            'jabatan'           => session()->get('jabatan') ?? 'Staff',
            'daftar_log'        => $daftarLog,
            'total_logs'        => $totalLogs,
            'today_logs'        => $todayLogs,
            'success_logs'      => $successLogs,
            'warning_logs'      => $warningLogs,
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('log/index', $data);
    }

    /**
     * Download Ekspor Seluruh Riwayat Log Aktivitas ke CSV/Excel
     */
    public function download()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $logModel = new LogAktivitasModel();
        $logs = $logModel->orderBy('created_at', 'DESC')->findAll();

        $filename = 'Log_Aktivitas_Sistem_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'No',
            'ID Log',
            'Waktu Kejadian (WIB)',
            'Nama Pengguna',
            'Hak Akses / Jabatan',
            'Modul Sistem',
            'Deskripsi Aktivitas',
            'Status',
            'Alamat IP',
            'Perangkat / User Agent',
            'Detail JSON Payload'
        ], ';');

        $no = 1;
        foreach ($logs as $row) {
            fputcsv($output, [
                $no++,
                '#' . $row['id'],
                $row['created_at'] ?? '-',
                $row['nama_user'] ?? 'System',
                $row['role'] ?? '-',
                $row['modul'] ?? 'SISTEM',
                $row['aktivitas'] ?? '-',
                $row['status'] ?? 'SUCCESS',
                $row['ip_address'] ?? '-',
                $row['user_agent'] ?? '-',
                $row['detail'] ?? ''
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Ambil data detail satu record log via AJAX
     */
    public function detail($id = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir.']);
        }

        $logModel = new LogAktivitasModel();
        $log = $logModel->find($id);

        if (!$log) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data log tidak ditemukan.']);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $log
        ]);
    }
}
