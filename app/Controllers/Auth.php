<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika ada flash message (misal baru daftar atau ada error), tetap tampilkan halaman login
        if (session()->getFlashdata('success') || session()->getFlashdata('error')) {
            return view('auth/login');
        }

        // Redirect jika sudah login
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process()
    {
        $userModel = new UserModel();
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Cek password (plain atau hash)
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                if ($user['status'] == 1) {
                    $sessionData = [
                        'user_id'      => $user['id'],
                        'username'     => $user['username'],
                        'nama_lengkap' => $user['nama_lengkap'],
                        'email'        => $user['email'] ?? '',
                        'nomor_hp'     => $user['nomor_hp'] ?? '',
                        'foto'         => $user['foto'] ?? '',
                        'jabatan'      => $user['jabatan'],
                        'logged_in'    => true,
                    ];
                    session()->set($sessionData);
                    
                    // Update terakhir login
                    $userModel->update($user['id'], [
                        'terakhir_login' => date('Y-m-d H:i:s')
                    ]);

                    return redirect()->to('/dashboard');
                } else {
                    // Status 0: Tidak Aktif / Dinonaktifkan
                    session()->remove(['user_id', 'username', 'nama_lengkap', 'jabatan', 'logged_in']);
                    session()->setFlashdata('error', '⛔ Akun Anda (@' . esc($user['username']) . ') berstatus TIDAK AKTIF atau dinonaktifkan oleh administrator.');
                    return redirect()->to('/auth');
                }
            } else {
                session()->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->to('/auth');
            }
        } else {
            session()->setFlashdata('error', 'Username "' . esc($username) . '" tidak terdaftar di sistem.');
            return redirect()->to('/auth');
        }
    }

    public function register()
    {
        // Jika membuka halaman registrasi, bersihkan sesi login sebelumnya agar murni sebagai pendaftar baru
        session()->remove(['user_id', 'username', 'nama_lengkap', 'jabatan', 'logged_in']);

        return view('auth/register');
    }

    public function registerProcess()
    {
        // Pastikan tidak ada sesi login yang aktif
        session()->remove(['user_id', 'username', 'nama_lengkap', 'jabatan', 'logged_in']);

        $userModel = new UserModel();

        $username    = trim($this->request->getPost('username') ?? '');
        $password    = $this->request->getPost('password');
        $konfirmasi  = $this->request->getPost('konfirmasi_password');
        $namaLengkap = trim($this->request->getPost('nama_lengkap') ?? '');
        $email       = trim($this->request->getPost('email') ?? '');
        $nomorHp     = trim($this->request->getPost('nomor_hp') ?? '');

        if (empty($username) || empty($password) || empty($namaLengkap)) {
            session()->setFlashdata('error', 'Mohon lengkapi seluruh kolom wajib.');
            return redirect()->back()->withInput();
        }

        if (strlen($password) < 6) {
            session()->setFlashdata('error', 'Password minimal 6 karakter.');
            return redirect()->back()->withInput();
        }

        if ($password !== $konfirmasi) {
            session()->setFlashdata('error', 'Konfirmasi password tidak cocok.');
            return redirect()->back()->withInput();
        }

        // Cek username unik
        $existing = $userModel->where('username', $username)->first();
        if ($existing) {
            session()->setFlashdata('error', 'Username "' . esc($username) . '" sudah digunakan. Silakan gunakan username lain.');
            return redirect()->back()->withInput();
        }

        // Simpan pendaftaran dengan status = 0 (Menunggu Approval)
        $userModel->insert([
            'username'     => $username,
            'password'     => password_hash($password, PASSWORD_DEFAULT),
            'nama_lengkap' => $namaLengkap,
            'email'        => $email,
            'nomor_hp'     => $nomorHp,
            'jabatan'      => 'Pengusul',
            'status'       => 0 // 0: Menunggu persetujuan Superadmin
        ]);

        // Pastikan sesi login tetap bersih
        session()->remove(['user_id', 'username', 'nama_lengkap', 'jabatan', 'logged_in']);

        session()->setFlashdata('success', 'Pendaftaran akun Pengusul atas nama "' . esc($namaLengkap) . '" (@' . esc($username) . ') BERHASIL! Status akun Anda adalah MENUNGGU PERSETUJUAN (PENDING). Harap menunggu Super Administrator menyetujui akun Anda sebelum login.');
        return redirect()->to('/auth');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
