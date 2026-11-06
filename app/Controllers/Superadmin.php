<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\UserModel;
use App\Models\LogAktivitasModel;
use App\Models\SettingReferensiModel;

class Superadmin extends BaseController
{
    /**
     * Helper untuk memverifikasi hak akses Superadmin
     */
    protected function checkSuperadmin()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        if ($jabatan !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses dibatasi hanya untuk Super Administrator');
        }

        return null;
    }

    /**
     * Helper untuk memverifikasi hak akses Superadmin atau CEO/Direksi (misal audit log)
     */
    protected function checkSuperadminOrCeo()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        if ($jabatan !== 'superadmin' && $jabatan !== 'ceo' && strpos($jabatan, 'direk') === false && strpos($jabatan, 'ceo') === false) {
            return redirect()->to('/dashboard')->with('error', 'Akses dibatasi hanya untuk Super Administrator & CEO/Direksi');
        }

        return null;
    }

    /**
     * Halaman index: arahkan ke Dashboard
     */
    public function index()
    {
        return redirect()->to('/dashboard');
    }

    /**
     * Menampilkan view superadmin/simulasi.php dengan data berkas kredit dari database
     */
    public function simulasi()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $kreditModel  = new \App\Models\KreditModel();
        $daftarKredit = $kreditModel->orderBy('created_at', 'DESC')->findAll();

        $db = \Config\Database::connect();
        $analisaModel = new \App\Models\KreditAnalisaModel();
        $komiteModel  = new \App\Models\KomiteModel();
        $agunanModel  = new \App\Models\AgunanModel();
        
        $semuaAnalisa = $analisaModel->findAll();
        $mapAnalisa = [];
        foreach ($semuaAnalisa as $a) {
            $mapAnalisa[$a['kredit_id']] = $a;
        }

        $semuaKomite = $komiteModel->orderBy('komite_id', 'DESC')->findAll();
        $mapKomite = [];
        foreach ($semuaKomite as $km) {
            if (!isset($mapKomite[$km['kredit_id']]) && !empty($km['catatan_approval'])) {
                $mapKomite[$km['kredit_id']] = $km;
            }
        }
        foreach ($semuaKomite as $km) {
            if (!isset($mapKomite[$km['kredit_id']])) {
                $mapKomite[$km['kredit_id']] = $km;
            }
        }

        $semuaAgunan = $agunanModel->findAll();
        $mapAgunan = [];
        foreach ($semuaAgunan as $ag) {
            if (!isset($mapAgunan[$ag['kredit_id']])) {
                $mapAgunan[$ag['kredit_id']] = [];
            }
            $mapAgunan[$ag['kredit_id']][] = $ag;
        }

        foreach ($daftarKredit as &$k) {
            $km = $mapKomite[$k['kredit_id']] ?? null;
            $an = $mapAnalisa[$k['kredit_id']] ?? null;
            $agList = $mapAgunan[$k['kredit_id']] ?? [];

            $k['catatan_reviewer'] = !empty($km['catatan_approval']) ? $km['catatan_approval'] : '';
            $k['status_komite']    = !empty($km['status_approval']) ? $km['status_approval'] : ($k['status_pengajuan'] ?? 'Draft');
            $k['skor_ai']          = !empty($an['skor_kelayakan_ai']) ? (int)$an['skor_kelayakan_ai'] : 82;
            $k['rekomendasi_ai']   = !empty($an['rekomendasi_keputusan_ai']) ? $an['rekomendasi_keputusan_ai'] : 'Disetujui Penuh';
            $k['analisis_risiko']  = !empty($an['analisis_risiko_ai']) ? $an['analisis_risiko_ai'] : '';
            
            $totalLv = 0;
            $totalMv = 0;
            foreach ($agList as $ag) {
                $totalLv += (float)($ag['nilai_likuidasi'] ?? 0);
                $totalMv += (float)($ag['nilai_pasar'] ?? 0);
            }
            $k['total_nilai_likuidasi'] = $totalLv;
            $k['total_nilai_pasar']     = $totalMv;
            $plafon = (float)($k['plafon_pengajuan'] ?? 0);
            $k['coverage_lv'] = $plafon > 0 ? round(($totalLv / $plafon) * 100) : 0;
        }
        unset($k);

        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Semua Pengajuan Kredit | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'OTW Kredit'),
            'nama_lengkap'      => session()->get('nama_lengkap') ?? 'Super Administrator',
            'jabatan'           => session()->get('jabatan') ?? 'Superadmin',
            'active_menu'       => 'simulasi',
            'daftar_kredit'     => $daftarKredit,
            'pengaturan_sistem' => $pengaturanSistem,
        ];

        return view('superadmin/simulasi', $data);
    }

    /**
     * Menghapus data simulasi / berkas kredit (Asinkronus & Sinkronus)
     */
    public function hapusSimulasi($id = null)
    {
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($redirect = $this->checkSuperadmin()) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak atau sesi telah habis.']);
            }
            return $redirect;
        }

        $kreditModel = new \App\Models\KreditModel();
        $kredit = $kreditModel->find($id);

        if (!$kredit) {
            $msg = 'Berkas simulasi/pengajuan tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/simulasi')->with('error', $msg);
        }

        $nomorAplikasi = $kredit['nomor_aplikasi'] ?? ('ID #' . $id);
        $kreditModel->delete($id);

        // Catat Audit Trail Log
        LogAktivitasModel::catat(
            'Menghapus berkas pengajuan kredit ' . $nomorAplikasi,
            'KREDIT',
            'WARNING',
            [
                'kredit_id'       => $id,
                'nomor_aplikasi'  => $nomorAplikasi,
                'nama_debitur'    => $kredit['nama_debitur'] ?? '-',
                'plafon_pengajuan'=> $kredit['plafon_pengajuan'] ?? 0
            ]
        );

        $successMsg = 'Berkas simulasi kredit ' . esc($nomorAplikasi) . ' berhasil dihapus.';

        if ($isAjax) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg
            ]);
        }

        return redirect()->to('/superadmin/simulasi')->with('success', $successMsg);
    }

    /**
     * Menampilkan view superadmin/ai_5c.php
     */
    public function ai_5c()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        $pengaturanAi = $db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Konfigurasi Parameter Analisis 5C | OTW Kredit',
            'nama_lengkap'      => session()->get('nama_lengkap') ?? 'Super Administrator',
            'jabatan'           => session()->get('jabatan') ?? 'Superadmin',
            'active_menu'       => 'ai_5c',
            'pengaturan_sistem' => $pengaturanSistem,
            'pengaturan_ai'     => $pengaturanAi,
        ];

        return view('superadmin/ai_5c', $data);
    }

    /**
     * Simpan konfigurasi API, Model AI, Link Gateway, dan Prompt secara asinkron
     */
    public function simpanPengaturanAi()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $modelAi     = trim($this->request->getPost('model_ai') ?: 'gemini-2.5-flash');
        $apiEndpoint = trim($this->request->getPost('api_endpoint') ?: 'https://generativelanguage.googleapis.com/v1beta/models');
        $apiKey      = trim($this->request->getPost('api_key') ?? '');
        $pillar      = trim($this->request->getPost('pillar') ?? '');
        $prompt      = trim($this->request->getPost('prompt') ?? '');

        $db = \Config\Database::connect();

        $updateSistem = [
            'model_ai'     => $modelAi,
            'api_endpoint' => $apiEndpoint,
        ];
        if (!empty($apiKey)) {
            $updateSistem['api_key'] = $apiKey;
        }
        $db->table('pengaturan_sistem')->where('id', 1)->update($updateSistem);

        $updateAi = [
            'model_ai' => $modelAi,
        ];
        if (!empty($apiKey)) {
            $updateAi['api_key'] = $apiKey;
        }

        if (!empty($pillar) && !empty($prompt)) {
            if (strlen(trim($prompt)) < 15) {
                $msg = 'Prompt analisis pilar 5C tidak boleh terlalu pendek (minimal 15 karakter).';
                if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                    return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
                }
                return redirect()->to('/superadmin/ai_5c')->with('error', $msg);
            }
            $promptCol = 'prompt_' . strtolower($pillar);
            if (in_array($promptCol, ['prompt_character', 'prompt_capacity', 'prompt_capital', 'prompt_collateral', 'prompt_condition', 'prompt_final'])) {
                $updateAi[$promptCol] = trim($prompt);
            }
        }
        $db->table('pengaturan_ai')->where('id', 1)->update($updateAi);

        // Catat Audit Trail Log
        LogAktivitasModel::catat(
            'Memperbarui konfigurasi AI Engine (' . $modelAi . ')',
            'AI_ENGINE',
            'SUCCESS',
            [
                'model_ai'     => $modelAi,
                'api_endpoint' => $apiEndpoint,
                'pillar'       => $pillar ?: 'Global Directives'
            ]
        );

        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Konfigurasi Model AI (' . esc($modelAi) . ') dan Link Gateway berhasil disimpan.',
                'model'   => $modelAi,
                'gateway' => $apiEndpoint
            ]);
        }

        return redirect()->to('/superadmin/ai_5c')->with('success', 'Konfigurasi model AI & gateway berhasil diperbarui');
    }

    /**
     * Alias route review dari simulasi menuju ke modul AI 5C
     */
    public function review()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        return redirect()->to('/superadmin/ai_5c');
    }

    /**
     * Menampilkan view superadmin/manajemen_user.php
     */
    public function manajemen_user()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $userModel   = new UserModel();
        $daftarUsers = $userModel->orderBy('id', 'DESC')->findAll();

        $currentUserId = (int)(session()->get('user_id') ?? 0);

        // Prioritaskan Super Administrator di paling atas (No. 1)
        usort($daftarUsers, function ($a, $b) use ($currentUserId) {
            $isSuperA = (strtolower($a['jabatan'] ?? '') === 'superadmin' || strtolower($a['username'] ?? '') === 'superadmin' || (int)$a['id'] === $currentUserId);
            $isSuperB = (strtolower($b['jabatan'] ?? '') === 'superadmin' || strtolower($b['username'] ?? '') === 'superadmin' || (int)$b['id'] === $currentUserId);

            if ($isSuperA && !$isSuperB) return -1;
            if (!$isSuperA && $isSuperB) return 1;
            return (int)$b['id'] <=> (int)$a['id'];
        });

        $totalUsers    = count($daftarUsers);
        $totalAktif    = 0;
        $totalNonaktif = 0;

        foreach ($daftarUsers as $u) {
            if ((int)($u['status'] ?? 0) === 1) {
                $totalAktif++;
            } else {
                $totalNonaktif++;
            }
        }

        $data = [
            'title'          => 'Manajemen Pengguna & Akses | OTW Kredit',
            'nama_lengkap'   => session()->get('nama_lengkap') ?? 'Super Administrator',
            'jabatan'        => session()->get('jabatan') ?? 'Superadmin',
            'daftar_users'   => $daftarUsers,
            'total_users'    => $totalUsers,
            'total_aktif'    => $totalAktif,
            'total_nonaktif' => $totalNonaktif,
            'active_menu'    => 'users',
        ];

        return view('superadmin/manajemen_user', $data);
    }

    /**
     * Helper untuk menghitung total, aktif, dan nonaktif user
     */
    private function getUserCounts()
    {
        $userModel = new UserModel();
        $all = $userModel->findAll();
        $total = count($all);
        $aktif = 0;
        $nonaktif = 0;
        foreach ($all as $u) {
            if ((int)($u['status'] ?? 0) === 1) {
                $aktif++;
            } else {
                $nonaktif++;
            }
        }
        return ['total' => $total, 'aktif' => $aktif, 'nonaktif' => $nonaktif];
    }

    /**
     * Menyimpan Pengguna Baru yang disesuaikan dengan Profil Akun (Asinkronus & Sinkronus)
     */
    public function simpanUser()
    {
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($redirect = $this->checkSuperadmin()) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak atau sesi habis.']);
            }
            return $redirect;
        }

        $userModel = new UserModel();

        $namaLengkap = trim($this->request->getPost('nama_lengkap') ?? '');
        $username    = trim($this->request->getPost('username') ?? '');
        $email       = trim($this->request->getPost('email') ?? '');
        $nomorHp     = trim($this->request->getPost('nomor_hp') ?? '');
        $jabatan     = trim($this->request->getPost('jabatan') ?? 'Pengusul');
        $password    = $this->request->getPost('password');
        $status      = (int)($this->request->getPost('status') ?? 1);

        // Validasi field wajib
        if (empty($namaLengkap) || empty($username) || empty($password)) {
            $msg = 'Nama lengkap, username, dan kata sandi wajib diisi.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        // Validasi password minimal 8 karakter & berkarakter
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $msg = 'Password wajib minimal 8 karakter dan berkarakter (kombinasi huruf besar A-Z, kecil a-z, angka 0-9, dan simbol khusus).';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        // Cek duplikasi username
        $existing = $userModel->where('username', $username)->first();
        if ($existing) {
            $msg = 'Username @' . esc($username) . ' sudah digunakan. Silakan gunakan username lain.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        // Handle upload foto profil jika diunggah
        $fotoPath = '';
        $fotoFile = $this->request->getFile('foto_file');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $uploadFolder = FCPATH . 'uploads/profil/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }
            $newName = $fotoFile->getRandomName();
            $fotoFile->move($uploadFolder, $newName);
            $fullFotoPath = $uploadFolder . $newName;

            // Kompresi otomatis foto profil pengguna (maks 500x500 px, kualitas 80%)
            \App\Libraries\ImageCompressor::compress($fullFotoPath, 500, 500, 80);

            $fotoPath = 'uploads/profil/' . $newName;
        }

        $dataInsert = [
            'nama_lengkap'   => $namaLengkap,
            'username'       => $username,
            'email'          => $email,
            'nomor_hp'       => $nomorHp,
            'jabatan'        => $jabatan,
            'foto'           => $fotoPath,
            'password'       => password_hash($password, PASSWORD_DEFAULT),
            'status'         => ($status === 1) ? 1 : 0,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        $insertId = $userModel->insert($dataInsert);

        // Catat Audit Trail Log
        LogAktivitasModel::catat(
            'Menambahkan pengguna baru: ' . $namaLengkap . ' (@' . $username . ')',
            'USER_MANAGEMENT',
            'SUCCESS',
            [
                'new_user_id' => $insertId,
                'username'    => $username,
                'jabatan'     => $jabatan,
                'status'      => $status === 1 ? 'Aktif' : 'Non-aktif'
            ]
        );

        $successMsg = 'Pengguna baru "' . esc($namaLengkap) . '" (@' . esc($username) . ') berhasil ditambahkan dengan status ' . ($status === 1 ? 'Aktif' : 'Tidak Aktif') . '.';

        if ($isAjax) {
            $avatarUrl = !empty($fotoPath) 
                ? base_url($fotoPath) 
                : 'https://ui-avatars.com/api/?name=' . urlencode($namaLengkap) . '&background=2563eb&color=fff&bold=true';

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg,
                'user'    => [
                    'id'           => $insertId,
                    'nama_lengkap' => $namaLengkap,
                    'username'     => $username,
                    'email'        => $email,
                    'nomor_hp'     => $nomorHp,
                    'jabatan'      => $jabatan,
                    'foto'         => $fotoPath,
                    'avatar_url'   => $avatarUrl,
                    'status'       => $status,
                    'created_at'   => date('d M Y')
                ],
                'counts'  => $this->getUserCounts()
            ]);
        }

        return redirect()->to('/superadmin/manajemen_user')->with('success', $successMsg);
    }

    /**
     * Mengubah status aktif / tidak aktif pengguna (Asinkronus & Sinkronus)
     */
    public function toggleStatusUser($id)
    {
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($redirect = $this->checkSuperadmin()) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
            }
            return $redirect;
        }

        $currentUserId = session()->get('user_id');
        if ((int)$id === (int)$currentUserId) {
            $msg = 'Anda tidak dapat mengubah status akun Anda sendiri.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            $msg = 'Pengguna tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        $newStatus = ((int)($user['status'] ?? 0) === 1) ? 0 : 1;
        $userModel->update($id, [
            'status'     => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $statusText = ($newStatus === 1) ? 'diaktifkan' : 'dinonaktifkan';
        $successMsg = 'Akun pengguna "' . esc($user['nama_lengkap']) . '" telah ' . $statusText . '.';

        if ($isAjax) {
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => $successMsg,
                'new_status' => $newStatus,
                'counts'     => $this->getUserCounts()
            ]);
        }

        return redirect()->to('/superadmin/manajemen_user')->with('success', $successMsg);
    }

    /**
     * Menghapus pengguna dari sistem (Asinkronus & Sinkronus)
     */
    public function hapusUser($id)
    {
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($redirect = $this->checkSuperadmin()) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
            }
            return $redirect;
        }

        $currentUserId = session()->get('user_id');
        if ((int)$id === (int)$currentUserId) {
            $msg = 'Anda tidak dapat menghapus akun Anda sendiri.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            $msg = 'Pengguna tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to('/superadmin/manajemen_user')->with('error', $msg);
        }

        $userModel->delete($id);

        // Catat Audit Trail Log
        LogAktivitasModel::catat(
            'Menghapus akun pengguna: ' . ($user['nama_lengkap'] ?? '-') . ' (@' . ($user['username'] ?? '-') . ')',
            'USER_MANAGEMENT',
            'WARNING',
            [
                'deleted_user_id' => $id,
                'username'        => $user['username'] ?? '',
                'jabatan'         => $user['jabatan'] ?? ''
            ]
        );

        $successMsg = 'Pengguna "' . esc($user['nama_lengkap']) . '" (@' . esc($user['username']) . ') berhasil dihapus dari sistem.';

        if ($isAjax) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg,
                'counts'  => $this->getUserCounts()
            ]);
        }

        return redirect()->to('/superadmin/manajemen_user')->with('success', $successMsg);
    }

    /**
     * Menampilkan halaman Pengaturan Sistem & Konfigurasi Lembaga
     */
    public function pengaturan()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        $pengaturanAi = $db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Pengaturan Sistem & Konfigurasi Lembaga | OTW Kredit',
            'nama_lengkap'      => session()->get('nama_lengkap') ?? 'Super Administrator',
            'jabatan'           => session()->get('jabatan') ?? 'Superadmin',
            'active_menu'       => 'pengaturan',
            'pengaturan_sistem' => $pengaturanSistem,
            'pengaturan_ai'     => $pengaturanAi,
        ];

        return view('superadmin/pengaturan', $data);
    }

    /**
     * Toggle status fitur Petugas SLIK (Aktif / Nonaktif) via AJAX oleh Superadmin
     */
    public function togglePetugasSlik()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses khusus Superadmin.']);
        }

        $db = \Config\Database::connect();
        $pengaturan = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        
        $currentAktif = (int)($pengaturan['petugas_slik_aktif'] ?? (($pengaturan['ijin_pengusul_isi_slik'] ?? 1) == 0 ? 1 : 0));
        $newAktif = ($currentAktif === 1) ? 0 : 1;
        $ijinPengusul = ($newAktif === 1) ? 0 : 1;

        $db->table('pengaturan_sistem')->where('id', 1)->update([
            'petugas_slik_aktif'     => $newAktif,
            'ijin_pengusul_isi_slik' => $ijinPengusul,
            'updated_at'             => date('Y-m-d H:i:s')
        ]);

        $statusText = ($newAktif === 1) ? 'DIAKTIFKAN (Mode Standar / SOP Ketat)' : 'DINONAKTIFKAN (Mode Fleksibel / Single AO)';

        // Catat Log Aktivitas
        LogAktivitasModel::catat(
            'Mengubah alur verifikasi SLIK OJK menjadi: ' . $statusText,
            'PENGATURAN',
            'SUCCESS',
            [
                'petugas_slik_aktif'     => $newAktif,
                'ijin_pengusul_isi_slik' => $ijinPengusul
            ]
        );

        return $this->response->setJSON([
            'status'             => true,
            'is_active'          => $newAktif,
            'petugas_slik_aktif' => $newAktif,
            'ijin_pengusul_isi_slik' => $ijinPengusul,
            'message'            => 'Fitur Petugas SLIK berhasil ' . $statusText
        ]);
    }

    /**
     * Toggle status fitur individual (Feature Flag) via AJAX oleh Superadmin
     */
    public function toggleFeatureFlag()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Akses khusus Superadmin.']);
        }

        $feature = trim($this->request->getPost('feature') ?? '');
        $value   = $this->request->getPost('value');

        $allowedFeatures = [
            'fitur_ai_aktif',
            'fitur_badan_usaha_aktif',
            'mode_approval_komite',
            'fitur_disbursement_aktif',
            'mode_strict_dokumen'
        ];

        if (!in_array($feature, $allowedFeatures)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'message' => 'Fitur tidak dikenali.']);
        }

        $db = \Config\Database::connect();
        $pengaturan = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        if ($feature === 'mode_approval_komite') {
            $newVal = in_array($value, ['cepat', 'bertingkat']) ? $value : (($pengaturan['mode_approval_komite'] ?? 'cepat') === 'cepat' ? 'bertingkat' : 'cepat');
            $statusText = ($newVal === 'bertingkat') ? 'Mode Bertingkat (Hierarki Reviewer lalu CEO)' : 'Mode Cepat (Siapa cepat Reviewer/CEO)';
        } else {
            if ($value !== null && $value !== '') {
                $newVal = (int)$value === 1 ? 1 : 0;
            } else {
                $currentVal = (int)($pengaturan[$feature] ?? 1);
                $newVal = ($currentVal === 1) ? 0 : 1;
            }
            $statusText = ($newVal === 1) ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
        }

        $db->table('pengaturan_sistem')->where('id', 1)->update([
            $feature     => $newVal,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Catat Log Aktivitas
        LogAktivitasModel::catat(
            "Mengubah switch fitur [{$feature}] menjadi: {$statusText}",
            'PENGATURAN',
            'SUCCESS',
            [
                'feature'   => $feature,
                'new_value' => $newVal
            ]
        );

        return $this->response->setJSON([
            'status'     => true,
            'feature'    => $feature,
            'new_value'  => $newVal,
            'status_text'=> $statusText,
            'message'    => "Fitur {$feature} berhasil {$statusText}."
        ]);
    }

    /**
     * Backup database lengkap (struktur + data) ke file .sql, lalu diunduh langsung oleh Superadmin.
     * Salinan juga disimpan di writable/backups/ agar ada arsip di server.
     */
    public function backupDatabase()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        @set_time_limit(0);
        $db     = \Config\Database::connect();
        $tables = $db->listTables();

        $sql  = "-- OTW Credit Engine - Backup Database\n";
        $sql .= "-- Dibuat: " . date('Y-m-d H:i:s') . " oleh " . (session()->get('nama_lengkap') ?? '-') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\nSET NAMES utf8mb4;\n\n";

        foreach ($tables as $table) {
            $create = $db->query('SHOW CREATE TABLE `' . $table . '`')->getRowArray();
            $createSql = $create['Create Table'] ?? array_values($create)[1] ?? '';
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n{$createSql};\n\n";

            $rows = $db->table($table)->get()->getResultArray();
            foreach (array_chunk($rows, 200) as $chunk) {
                $cols = '`' . implode('`,`', array_keys($chunk[0])) . '`';
                $vals = [];
                foreach ($chunk as $row) {
                    $vals[] = '(' . implode(',', array_map(
                        static fn($v) => $v === null ? 'NULL' : $db->escape((string) $v),
                        array_values($row)
                    )) . ')';
                }
                $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES\n" . implode(",\n", $vals) . ";\n";
            }
            $sql .= "\n";
        }
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'backup_otw_' . date('Ymd_His') . '.sql';
        $dir      = WRITEPATH . 'backups/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir . $filename, $sql);

        LogAktivitasModel::catat(
            'Mengunduh backup database: ' . $filename,
            'PENGATURAN',
            'SUCCESS',
            ['file' => $filename, 'jumlah_tabel' => count($tables)]
        );

        return $this->response->download($dir . $filename, null)->setFileName($filename);
    }

    /**
     * Backup folder uploads (dokumen, bukti, foto) dalam satu file ZIP untuk disalin ke flashdisk.
     */
    public function backupBerkas()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        if (!class_exists('ZipArchive')) {
            return redirect()->to('/superadmin/pengaturan')->with('error', 'Ekstensi PHP ZipArchive belum aktif di server.');
        }

        @set_time_limit(0);
        $src = FCPATH . 'uploads';
        if (!is_dir($src)) {
            return redirect()->to('/superadmin/pengaturan')->with('error', 'Folder uploads belum ada / belum ada berkas.');
        }

        $dir = WRITEPATH . 'backups/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = 'backup_berkas_' . date('Ymd_His') . '.zip';
        $zipPath  = $dir . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->to('/superadmin/pengaturan')->with('error', 'Gagal membuat file ZIP backup.');
        }

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile()) {
                $zip->addFile($file->getPathname(), 'uploads/' . substr($file->getPathname(), strlen($src) + 1));
            }
        }
        $zip->close();

        LogAktivitasModel::catat('Mengunduh backup berkas upload: ' . $filename, 'PENGATURAN', 'SUCCESS', ['file' => $filename]);

        return $this->response->download($zipPath, null)->setFileName($filename);
    }

    /**
     * Menampilkan tabel aktivitas log sistem (Hanya Bisa Download dan Lihat Detail)
     */
    public function log()
    {
        if ($redirect = $this->checkSuperadminOrCeo()) {
            return $redirect;
        }

        $logModel = new LogAktivitasModel();

        $search       = trim($this->request->getGet('q') ?? '');
        $filterModul  = trim($this->request->getGet('modul') ?? '');
        $filterStatus = trim($this->request->getGet('status') ?? '');

        $builder = $logModel->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('aktivitas', $search)
                ->orLike('nama_user', $search)
                ->orLike('ip_address', $search)
                ->orLike('detail', $search)
            ->groupEnd();
        }

        if (!empty($filterModul)) {
            $builder->where('modul', $filterModul);
        }

        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }

        $logs = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        // Data statistik audit
        $totalLogs   = $logModel->countAllResults(false);
        $todayLogs   = $logModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(false);
        $warningLogs = $logModel->whereIn('status', ['WARNING', 'DANGER', 'ERROR'])->countAllResults(false);
        $successLogs = $logModel->where('status', 'SUCCESS')->countAllResults(false);

        // Ambil list unik modul untuk filter dropdown
        $modulList = $logModel->select('modul')->distinct()->findAll();
        $daftarModul = array_filter(array_column($modulList, 'modul'));

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Log Aktivitas & Audit Trail Sistem | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'OTW Kredit'),
            'nama_lengkap'      => session()->get('nama_lengkap') ?? 'Super Administrator',
            'jabatan'           => session()->get('jabatan') ?? 'Superadmin',
            'active_menu'       => 'log',
            'pengaturan_sistem' => $pengaturanSistem,
            'logs'              => $logs,
            'total_logs'        => $totalLogs,
            'today_logs'        => $todayLogs,
            'warning_logs'      => $warningLogs,
            'success_logs'      => $successLogs,
            'daftar_modul'      => $daftarModul,
            'filter_q'          => $search,
            'filter_modul'      => $filterModul,
            'filter_status'     => $filterStatus,
        ];

        return view('superadmin/log', $data);
    }

    /**
     * Download/Ekspor tabel aktivitas log ke format CSV yang kompatibel dengan Excel (UTF-8 BOM)
     */
    public function downloadLog()
    {
        if ($redirect = $this->checkSuperadminOrCeo()) {
            return $redirect;
        }

        $logModel = new LogAktivitasModel();

        $search       = trim($this->request->getGet('q') ?? '');
        $filterModul  = trim($this->request->getGet('modul') ?? '');
        $filterStatus = trim($this->request->getGet('status') ?? '');

        $builder = $logModel->builder();

        if (!empty($search)) {
            $builder->groupStart()
                ->like('aktivitas', $search)
                ->orLike('nama_user', $search)
                ->orLike('ip_address', $search)
                ->orLike('detail', $search)
            ->groupEnd();
        }

        if (!empty($filterModul)) {
            $builder->where('modul', $filterModul);
        }

        if (!empty($filterStatus)) {
            $builder->where('status', $filterStatus);
        }

        $logs = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        $filename = 'Log_Aktivitas_Sistem_' . date('Ymd_His') . '.csv';

        // Set response headers untuk direct download CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM agar aksen dan karakter terbuka rapi di Excel
        fputs($output, "\xEF\xBB\xBF");

        // Header kolom CSV
        fputcsv($output, [
            'No',
            'ID Log',
            'Waktu Kejadian (WIB)',
            'Nama Pengguna',
            'Hak Akses / Peran',
            'Modul Sistem',
            'Aktivitas / Kejadian',
            'Status',
            'Alamat IP',
            'User Agent / Perangkat',
            'Detail Payload JSON'
        ], ';');

        $no = 1;
        foreach ($logs as $row) {
            fputcsv($output, [
                $no++,
                '#' . $row['id'],
                $row['created_at'],
                $row['nama_user'],
                $row['role'],
                $row['modul'],
                $row['aktivitas'],
                $row['status'],
                $row['ip_address'],
                $row['user_agent'],
                $row['detail']
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Mengambil detail satu record log untuk tampilan modal Lihat Detail (Asinkronus / AJAX)
     */
    public function detailLog($id)
    {
        if ($redirect = $this->checkSuperadminOrCeo()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $logModel = new LogAktivitasModel();
        $log = $logModel->find($id);

        if (!$log) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data log tidak ditemukan.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $log
        ]);
    }

    /**
     * Halaman Setting Referensi Sistem
     */
    public function referensi()
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $redirect;
        }

        $referensiModel = new SettingReferensiModel();
        
        // Ambil filter jenis dari query param jika ada
        $filterJenis = trim($this->request->getGet('jenis_referensi') ?? '');
        
        $builder = $referensiModel->orderBy('jenis_referensi', 'ASC')
                                   ->orderBy('urutan', 'ASC')
                                   ->orderBy('id', 'ASC');

        if (!empty($filterJenis) && $filterJenis !== 'Semua Jenis') {
            $builder->where('jenis_referensi', $filterJenis);
        }

        $daftarReferensi = $builder->findAll();
        
        // Ambil seluruh distinct jenis referensi untuk filter dan dropdown modal
        $rawJenis = $referensiModel->getDistinctJenis();
        $daftarJenis = array_map(function($item) {
            return $item['jenis_referensi'];
        }, $rawJenis);

        // Metadata nama lengkap, kategori hak akses, dan deskripsi sesuai struktur form input saat ini
        $mapJenisMetadata = [
            // Kategori 1: Formulir Dinamis (Bebas Tambah / Edit / Hapus)
            'kre_jaminan' => [
                'label'     => 'Jenis Jaminan / Agunan Kredit',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan jenis agunan pada Tab 1 & Tab 4 (SHM, SHGB, BPKB Mobil, BPKB Motor, Deposito, dll.)',
            ],
            'kre_tujuan' => [
                'label'     => 'Tujuan Pembiayaan Kredit',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan tujuan penggunaan kredit pada Tab 1 (Modal Kerja, Investasi, Konsumsi/Multiguna, Take Over)',
            ],
            'kre_jns' => [
                'label'     => 'Tipe Aplikasi Kredit',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan permohonan pada Tab 1 (Fasilitas Baru, Perpanjangan, Top Up, Restrukturisasi)',
            ],
            'kre_skema' => [
                'label'     => 'Skema Pembayaran / Angsuran',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan skema angsuran pada Tab 1 (Installment, Balloon Payment, Grace Period, Step Up)',
            ],
            'bu_bentuk' => [
                'label'     => 'Bentuk Legalitas Badan Usaha',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan legalitas perusahaan pada Tab 2 Badan Usaha (PT, CV, Koperasi, Yayasan, Firma, dll.)',
            ],
            'dom_status' => [
                'label'     => 'Status Kepemilikan Tempat Usaha / Domisili',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan status kepemilikan kantor / tempat tinggal pada Tab 2 (Milik Sendiri, Sewa, HGB, dll.)',
            ],
            'disb_biaya' => [
                'label'     => 'Komponen Biaya Pencairan (Disbursement)',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Rincian komponen potongan biaya pencairan pada Tab 11 (Provisi, Admin, Agent Fee, Asuransi, dll.)',
            ],
            'slik_keterkaitan' => [
                'label'     => 'Keterkaitan Subjek Permohonan SLIK',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan relasi subjek pada Tab 6 SLIK (Pemohon, Pasangan, Penjamin, Pengurus, Pemegang Saham, dll.)',
            ],
            'kre_lampberkas' => [
                'label'     => 'Kategori Dokumen Persyaratan',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Kategori upload dokumen pada Tab 5 Berkas Persyaratan (Identitas, Agunan, Rekening Koran, Legalitas, dll.)',
            ],
            'kre_referral' => [
                'label'     => 'Sumber Referral / Saluran Pemasaran',
                'kategori'  => 'fleksibel',
                'deskripsi' => 'Pilihan asal perolehan nasabah pada formulir (Direct, Broker, Agen Properti, Karyawan, dll.)',
            ],

            // Kategori 2: Data Inti Sistem & Regulasi (Terkunci - Hanya Edit Keterangan / Warna, Tidak Boleh Dihapus)
            'jns_entitas' => [
                'label'     => 'Jenis Entitas Debitur [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Entitas dasar aplikasi: Perorangan (I) & Badan Usaha (B). Terkunci untuk menjaga logika alur.',
            ],
            'kre_sts' => [
                'label'     => 'Status Alur Pengajuan Kredit [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Status siklus hidup kredit (Draft, Diajukan, Disetujui, Ditolak). Terkunci sistem.',
            ],
            'kre_kepkomite' => [
                'label'     => 'Keputusan Komite & Reviewer [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Hasil sidang komite (Disetujui, Disetujui Bersyarat, Ditolak, Revisi ke AO, Pending). Terkunci sistem.',
            ],
            'slik_hsl' => [
                'label'     => 'Hasil Kolektibilitas SLIK OJK [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Standar regulasi OJK Kolektibilitas 1 s/d 5 (Lancar s/d Macet). Terkunci sistem.',
            ],
            'target_kategori' => [
                'label'     => 'Kategori Prinsip Analisa 5C [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Pilar analisa 5C: Character, Capacity, Capital, Collateral, Condition. Terkunci sistem.',
            ],
            'target_kode' => [
                'label'     => 'Parameter Batasan Rasio (DSC / LTV) [Sistem]',
                'kategori'  => 'sistem',
                'deskripsi' => 'Batas ambang parameter rasio kelayakan keuangan (DSC min, LTV max, Tenor max). Terkunci sistem.',
            ],
        ];

        // Pastikan seluruh jenis referensi standar selalu terdaftar
        foreach (array_keys($mapJenisMetadata) as $sj) {
            if (!in_array($sj, $daftarJenis)) {
                $daftarJenis[] = $sj;
            }
        }
        sort($daftarJenis);

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        return view('superadmin/referensi', [
            'title'             => 'Setting Referensi | OTW Kredit',
            'active_menu'       => 'referensi',
            'daftar_referensi'  => $daftarReferensi,
            'daftar_jenis'      => $daftarJenis,
            'map_metadata'      => $mapJenisMetadata,
            'selected_jenis'    => $filterJenis,
            'pengaturan_sistem' => $pengaturanSistem,
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan')
        ]);
    }

    /**
     * Simpan Tambah Referensi Baru
     */
    public function simpanReferensi()
    {
        if ($redirect = $this->checkSuperadmin()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah habis.']);
            }
            return $redirect;
        }

        $jenisReferensi       = trim($this->request->getPost('jenis_referensi') ?? '');
        $keteranganReferensi  = trim($this->request->getPost('keterangan_referensi') ?? '');
        $tipeReferensi        = trim($this->request->getPost('tipe_referensi') ?? '');
        $colorReferensi       = trim($this->request->getPost('color_referensi') ?? 'primary');

        if (empty($jenisReferensi) || empty($keteranganReferensi)) {
            $msg = 'Jenis Referensi dan Keterangan Referensi wajib diisi.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $referensiModel = new SettingReferensiModel();

        // Hitung urutan terakhir untuk jenis ini
        $last = $referensiModel->where('jenis_referensi', $jenisReferensi)
                               ->orderBy('urutan', 'DESC')
                               ->first();
        $nextUrutan = ($last && isset($last['urutan'])) ? ((int)$last['urutan'] + 1) : 1;

        $insertData = [
            'jenis_referensi'      => $jenisReferensi,
            'keterangan_referensi' => $keteranganReferensi,
            'tipe_referensi'       => $tipeReferensi,
            'color_referensi'      => !empty($colorReferensi) ? $colorReferensi : 'primary',
            'urutan'               => $nextUrutan,
            'is_active'            => 1
        ];

        $insertedId = $referensiModel->insert($insertData);

        // Catat Audit Trail
        LogAktivitasModel::catat(
            'Menambahkan referensi baru: ' . $jenisReferensi . ' - ' . $keteranganReferensi,
            'SISTEM',
            'INFO',
            [
                'id'                   => $insertedId,
                'jenis_referensi'      => $jenisReferensi,
                'keterangan_referensi' => $keteranganReferensi,
                'tipe_referensi'       => $tipeReferensi,
                'color_referensi'      => $colorReferensi
            ]
        );

        $successMsg = 'Referensi berhasil ditambahkan.';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg,
                'data'    => array_merge(['id' => $insertedId], $insertData)
            ]);
        }

        return redirect()->to(base_url('superadmin/referensi'))->with('success', $successMsg);
    }

    /**
     * Update Data Referensi
     */
    public function updateReferensi($id)
    {
        if ($redirect = $this->checkSuperadmin()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah habis.']);
            }
            return $redirect;
        }

        $referensiModel = new SettingReferensiModel();
        $ref = $referensiModel->find($id);

        if (!$ref) {
            $msg = 'Data referensi tidak ditemukan.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to(base_url('superadmin/referensi'))->with('error', $msg);
        }

        $jenisReferensi       = trim($this->request->getPost('jenis_referensi') ?? '');
        $keteranganReferensi  = trim($this->request->getPost('keterangan_referensi') ?? '');
        $tipeReferensi        = trim($this->request->getPost('tipe_referensi') ?? '');
        $colorReferensi       = trim($this->request->getPost('color_referensi') ?? 'primary');

        if (empty($jenisReferensi) || empty($keteranganReferensi)) {
            $msg = 'Jenis Referensi dan Keterangan Referensi wajib diisi.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->back()->withInput()->with('error', $msg);
        }

        $updateData = [
            'jenis_referensi'      => $jenisReferensi,
            'keterangan_referensi' => $keteranganReferensi,
            'tipe_referensi'       => $tipeReferensi,
            'color_referensi'      => !empty($colorReferensi) ? $colorReferensi : 'primary',
        ];

        $referensiModel->update($id, $updateData);

        // Catat Audit Trail
        LogAktivitasModel::catat(
            'Mengubah referensi ID #' . $id . ': ' . $jenisReferensi . ' - ' . $keteranganReferensi,
            'SISTEM',
            'INFO',
            [
                'id'       => $id,
                'sebelum'  => $ref,
                'sesudah'  => $updateData
            ]
        );

        $successMsg = 'Referensi berhasil diperbarui.';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg
            ]);
        }

        return redirect()->to(base_url('superadmin/referensi'))->with('success', $successMsg);
    }

    /**
     * Hapus Referensi
     */
    public function hapusReferensi($id)
    {
        if ($redirect = $this->checkSuperadmin()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah habis.']);
            }
            return $redirect;
        }

        $referensiModel = new SettingReferensiModel();
        $ref = $referensiModel->find($id);

        if (!$ref) {
            $msg = 'Data referensi tidak ditemukan.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to(base_url('superadmin/referensi'))->with('error', $msg);
        }

        // Cek apakah data bawaan sistem yang terkunci
        if (!empty($ref['is_locked']) && (int)$ref['is_locked'] === 1) {
            $msg = 'Data referensi ini adalah bawaan sistem inti dan tidak boleh dihapus agar alur aplikasi tidak terganggu.';
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->to(base_url('superadmin/referensi'))->with('error', $msg);
        }

        $referensiModel->delete($id);

        // Catat Audit Trail
        LogAktivitasModel::catat(
            'Menghapus referensi: ' . ($ref['jenis_referensi'] ?? '') . ' - ' . ($ref['keterangan_referensi'] ?? ''),
            'SISTEM',
            'WARNING',
            $ref
        );

        $successMsg = 'Referensi berhasil dihapus.';
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg
            ]);
        }

        return redirect()->to(base_url('superadmin/referensi'))->with('success', $successMsg);
    }

    /**
     * Ambil detail data referensi dalam format JSON untuk modal Edit
     */
    public function getReferensiDetail($id)
    {
        if ($redirect = $this->checkSuperadmin()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $referensiModel = new SettingReferensiModel();
        $ref = $referensiModel->find($id);

        if (!$ref) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $ref
        ]);
    }
}
