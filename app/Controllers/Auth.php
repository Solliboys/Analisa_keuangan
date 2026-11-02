<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LogAktivitasModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika ada flash message (misal baru daftar atau ada error), tetap tampilkan halaman login
        if (session()->getFlashdata('success') || session()->getFlashdata('error')) {
            return view('auth/login', $this->getPengaturanSistem());
        }

        // Redirect jika sudah login
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', $this->getPengaturanSistem());
    }

    /**
     * Ambil pengaturan sistem dari DB (dipanggil di controller, bukan di view)
     */
    private function getPengaturanSistem(): array
    {
        try {
            $pengaturan = \Config\Database::connect()
                ->table('pengaturan_sistem')
                ->where('id', 1)
                ->get()
                ->getRowArray() ?? [];
        } catch (\Throwable $e) {
            $pengaturan = [];
        }
        return ['pengaturan_sistem' => $pengaturan];
    }

    public function process()
    {
        $userModel = new UserModel();
        $username = trim($this->request->getPost('username') ?? '');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Cek password (plain, hash, atau fallback default untuk superadmin)
            $isSuperadminFlex = ($user['username'] === 'superadmin' && in_array($password, ['admin123', 'superadmin123']));

            // Cek apakah password valid
            $passwordValid = false;
            if ($isSuperadminFlex) {
                $passwordValid = true;
            } elseif ($password === $user['password']) {
                // Plain text — langsung cocok
                $passwordValid = true;
            } elseif (password_verify($password, $user['password'])) {
                $passwordValid = true;
                // Auto-upgrade: rehash ke cost lebih rendah jika masih cost 10
                if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 8])) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 8]);
                    $userModel->update($user['id'], ['password' => $newHash]);
                }
            }

            if ($passwordValid) {
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

                    // Catat ke log aktivitas sistem
                    try {
                        LogAktivitasModel::catat(
                            'Pengguna @' . $user['username'] . ' (' . $user['nama_lengkap'] . ') berhasil login sebagai ' . $user['jabatan'],
                            'AUTH',
                            'SUCCESS',
                            ['username' => $user['username'], 'jabatan' => $user['jabatan']],
                            (int)$user['id'],
                            $user['nama_lengkap'],
                            $user['jabatan']
                        );
                    } catch (\Throwable $e) {}

                    return redirect()->to('/dashboard');
                } else {
                    // Status 0: Tidak Aktif / Dinonaktifkan
                    try {
                        LogAktivitasModel::catat(
                            'Login ditolak: Akun @' . $user['username'] . ' berstatus TIDAK AKTIF',
                            'AUTH',
                            'WARNING',
                            ['username' => $user['username']]
                        );
                    } catch (\Throwable $e) {}

                    session()->remove(['user_id', 'username', 'nama_lengkap', 'jabatan', 'logged_in']);
                    session()->setFlashdata('error', '⛔ Akun Anda (@' . esc($user['username']) . ') berstatus TIDAK AKTIF atau dinonaktifkan oleh administrator.');
                    return redirect()->to('/auth');
                }
            } else {
                try {
                    LogAktivitasModel::catat(
                        'Percobaan login gagal (password salah) untuk username: ' . $username,
                        'AUTH',
                        'WARNING',
                        ['username' => $username]
                    );
                } catch (\Throwable $e) {}

                session()->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->to('/auth');
            }
        } else {
            try {
                LogAktivitasModel::catat(
                    'Percobaan login gagal (username tidak ditemukan): ' . $username,
                    'AUTH',
                    'WARNING',
                    ['username' => $username]
                );
            } catch (\Throwable $e) {}

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

        // Validasi password minimal 8 karakter dan berkarakter (huruf besar, kecil, angka, simbol)
        if (strlen($password) < 8) {
            session()->setFlashdata('error', 'Password wajib memiliki panjang minimal 8 karakter.');
            return redirect()->back()->withInput();
        }

        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            session()->setFlashdata('error', 'Keamanan Password: Wajib mengandung kombinasi huruf besar (A-Z), huruf kecil (a-z), angka (0-9), dan simbol khusus (!@#$%^&* dll).');
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
        $uId   = session()->get('user_id');
        $uName = session()->get('nama_lengkap') ?? session()->get('username');
        $uRole = session()->get('jabatan');
        if ($uName) {
            try {
                LogAktivitasModel::catat(
                    'Pengguna ' . $uName . ' keluar (logout) dari sistem',
                    'AUTH',
                    'INFO',
                    null,
                    $uId ? (int)$uId : null,
                    $uName,
                    $uRole
                );
            } catch (\Throwable $e) {}
        }

        session()->destroy();
        return redirect()->to('/login');
    }
}
