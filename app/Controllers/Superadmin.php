<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\UserModel;
use App\Models\LogAktivitasModel;

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
}
