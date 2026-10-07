<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Redirect jika sudah login
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process()
    {
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // Cek password (Bisa menggunakan password_verify jika di hash)
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                if ($user['status'] == 1) {
                    // Set Session
                    $sessionData = [
                        'user_id'      => $user['id'],
                        'username'     => $user['username'],
                        'nama_lengkap' => $user['nama_lengkap'],
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
                    session()->setFlashdata('error', 'Akun Anda sedang dinonaktifkan.');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error', 'Username tidak terdaftar di sistem.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}
