<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $jabatan = session()->get('jabatan');
        $role = strtolower($jabatan ?? '');
        
        $data = [
            'title'        => 'Dashboard | OTW Kredit',
            'nama_lengkap' => session()->get('nama_lengkap'),
            'jabatan'      => $jabatan
        ];

        // Render view sesuai role / jabatan
        switch ($role) {
            case 'superadmin':
                return view('dashboard/superadmin', $data);
            case 'pengusul':
                return view('dashboard/pengusul', $data);
            default:
                // Sesi tidak valid (misal sisa dari role lama), paksa logout
                session()->destroy();
                session()->setFlashdata('error', 'Sesi atau Role tidak valid. Silakan login ulang.');
                return redirect()->to('/auth');
        }
    }
}
