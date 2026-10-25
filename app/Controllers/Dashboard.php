<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $kreditModel = new KreditModel();
        $userModel   = new UserModel();
        $db          = \Config\Database::connect();

        $userId  = session()->get('user_id');
        $jabatan = session()->get('jabatan');
        $role    = strtolower($jabatan ?? '');

        if ($role === 'superadmin') {
            // Data Komprehensif Superadmin: Seluruh Pengajuan, User Management, AI Setting
            $totalPengajuan = $kreditModel->countAllResults();
            $semuaKredit    = $kreditModel->orderBy('created_at', 'DESC')->findAll(10);
            
            $totalPlafon = 0;
            $pendingKomite = 0;
            $approvedKomite = 0;
            $rejectedKomite = 0;
            
            $allData = $kreditModel->findAll();
            foreach ($allData as $k) {
                $totalPlafon += (float)($k['plafon_pengajuan'] ?? 0);
                $st = strtolower($k['status_pengajuan'] ?? '');
                if ($st === 'diajukan' || $st === 'draft') $pendingKomite++;
                elseif ($st === 'disetujui') $approvedKomite++;
                elseif ($st === 'ditolak') $rejectedKomite++;
            }

            $daftarUsers = $userModel->orderBy('created_at', 'DESC')->findAll();
            $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
                'nama_aplikasi' => 'OTW Credit Engine & CAM',
                'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
                'logo_aplikasi' => '',
                'api_key'       => getenv('GEMINI_API_KEY') ?: '',
                'model_ai'      => 'gemini-3.8-flash',
                'api_endpoint'  => 'https://generativelanguage.googleapis.com/v1beta/models',
                'prompt_sistem' => 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).'
            ];
            $pengaturanAi = $db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray() ?? [
                'prompt_character'  => 'Analisis karakter debitur berdasarkan riwayat kredit dan integritas.',
                'prompt_capacity'   => 'Analisis kapasitas debitur berdasarkan perputaran rekening koran dan omset.',
                'prompt_capital'    => 'Analisis permodalan debitur berdasarkan rasio keuangan dan aset.',
                'prompt_collateral' => 'Analisis jaminan debitur berdasarkan nilai pasar, nilai likuidasi dan LTV.',
                'prompt_condition'  => 'Analisis prospek ekonomi debitur dan sektor usahanya.',
                'prompt_final'      => 'Berikan ringkasan rekomendasi keputusan akhir kredit beserta skor 1-100.'
            ];

            $usersPending = 0;
            foreach ($daftarUsers as $u) {
                if ($u['status'] == 0) $usersPending++;
            }

            $data = [
                'title'                 => 'Executive Command Dashboard | Superadmin',
                'nama_lengkap'          => session()->get('nama_lengkap'),
                'jabatan'               => $jabatan,
                'total_pengajuan'       => $totalPengajuan,
                'total_plafon'          => $totalPlafon,
                'total_pending'         => $pendingKomite,
                'total_approved'        => $approvedKomite,
                'total_rejected'        => $rejectedKomite,
                'total_users'           => count($daftarUsers),
                'users_pending'         => $usersPending,
                'antrean_pengajuan'     => $semuaKredit,
                'daftar_users'          => $daftarUsers,
                'pengaturan_sistem'     => $pengaturanSistem,
                'pengaturan_ai'         => $pengaturanAi
            ];

            return view('dashboard/superadmin', $data);

        } elseif ($role === 'pengusul') {
            // Data Spesifik Pengusul: Hanya aplikasi milik akun login ini
            $kreditSaya = $kreditModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
            
            $totalPengajuanSaya = count($kreditSaya);
            $totalPlafonSaya    = 0;
            $draftSaya          = 0;
            $diajukanSaya       = 0;
            $disetujuiSaya      = 0;

            foreach ($kreditSaya as $k) {
                $totalPlafonSaya += (float)($k['plafon_pengajuan'] ?? 0);
                $st = strtolower($k['status_pengajuan'] ?? '');
                if ($st === 'draft') $draftSaya++;
                elseif ($st === 'diajukan') $diajukanSaya++;
                elseif ($st === 'disetujui') $disetujuiSaya++;
            }

            $data = [
                'title'               => 'Workspace Account Officer | Pengusul Kredit',
                'nama_lengkap'        => session()->get('nama_lengkap'),
                'jabatan'             => $jabatan,
                'total_pengajuan'     => $totalPengajuanSaya,
                'total_plafon'        => $totalPlafonSaya,
                'total_draft'         => $draftSaya,
                'total_diajukan'      => $diajukanSaya,
                'total_disetujui'     => $disetujuiSaya,
                'daftar_pengajuan'    => array_slice($kreditSaya, 0, 8)
            ];

            return view('dashboard/pengusul', $data);

        } else {
            session()->destroy();
            session()->setFlashdata('error', 'Sesi tidak valid.');
            return redirect()->to('/auth');
        }
    }

    public function setujuiUser($id)
    {
        if (!session()->get('logged_in') || strtolower(session()->get('jabatan') ?? '') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $userModel->update($id, ['status' => 1]);
            return redirect()->to('/dashboard')->with('success', 'Akun Pengusul "' . esc($user['nama_lengkap']) . '" (@' . esc($user['username']) . ') BERHASIL DISETUJUI dan sekarang dapat login ke sistem.');
        }

        return redirect()->to('/dashboard')->with('error', 'User tidak ditemukan.');
    }

    public function tolakUser($id)
    {
        if (!session()->get('logged_in') || strtolower(session()->get('jabatan') ?? '') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $userModel->update($id, ['status' => 2]); // Status 2: Dinonaktifkan / Ditolak
            return redirect()->to('/dashboard')->with('success', 'Akun Pengusul "' . esc($user['nama_lengkap']) . '" telah DINONAKTIFKAN.');
        }

        return redirect()->to('/dashboard')->with('error', 'User tidak ditemukan.');
    }

    public function updatePengaturan()
    {
        if (!session()->get('logged_in') || strtolower(session()->get('jabatan') ?? '') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();

        $namaAplikasi = trim($this->request->getPost('nama_aplikasi') ?? 'OTW Credit Engine & CAM');
        $namaLembaga  = trim($this->request->getPost('nama_lembaga') ?? 'PT BPR / Lembaga Pembiayaan');
        $apiKey       = trim($this->request->getPost('api_key') ?? '');
        $modelAi      = trim($this->request->getPost('model_ai') ?? 'gemini-3.8-flash');
        $apiEndpoint  = trim($this->request->getPost('api_endpoint') ?? 'https://generativelanguage.googleapis.com/v1beta/models');

        $promptSistem     = trim($this->request->getPost('prompt_sistem') ?? 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).');
        $promptCharacter  = trim($this->request->getPost('prompt_character') ?? 'Analisis karakter debitur berdasarkan riwayat kredit dan integritas.');
        $promptCapacity   = trim($this->request->getPost('prompt_capacity') ?? 'Analisis kapasitas debitur berdasarkan perputaran rekening koran dan omset.');
        $promptCapital    = trim($this->request->getPost('prompt_capital') ?? 'Analisis permodalan debitur berdasarkan rasio keuangan dan aset.');
        $promptCollateral = trim($this->request->getPost('prompt_collateral') ?? 'Analisis jaminan debitur berdasarkan nilai pasar, nilai likuidasi dan LTV.');
        $promptCondition  = trim($this->request->getPost('prompt_condition') ?? 'Analisis prospek ekonomi debitur dan sektor usahanya.');
        $promptFinal      = trim($this->request->getPost('prompt_final') ?? 'Berikan ringkasan rekomendasi keputusan akhir kredit beserta skor 1-100.');

        // Handle upload logo
        $logoFile = $this->request->getFile('logo_file');
        $logoPath = null;
        if ($logoFile && $logoFile->isValid()) {
            $uploadFolder = FCPATH . 'uploads/logo/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }
            $logoName = 'logo_' . time() . '.' . $logoFile->getExtension();
            $logoFile->move($uploadFolder, $logoName);
            $logoPath = 'uploads/logo/' . $logoName;
        }

        $updateData = [
            'nama_aplikasi' => $namaAplikasi,
            'nama_lembaga'  => $namaLembaga,
            'api_key'       => $apiKey,
            'model_ai'      => $modelAi,
            'api_endpoint'  => $apiEndpoint,
            'prompt_sistem' => $promptSistem,
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        if ($logoPath) {
            $updateData['logo_aplikasi'] = $logoPath;
        }

        // Simpan ke pengaturan_sistem
        $db->table('pengaturan_sistem')->where('id', 1)->update($updateData);

        // Sinkronisasi ke pengaturan_ai untuk GeminiAiService
        $db->table('pengaturan_ai')->where('id', 1)->update([
            'api_key'           => $apiKey,
            'model_ai'          => $modelAi,
            'prompt_character'  => $promptCharacter,
            'prompt_capacity'   => $promptCapacity,
            'prompt_capital'    => $promptCapital,
            'prompt_collateral' => $promptCollateral,
            'prompt_condition'  => $promptCondition,
            'prompt_final'      => $promptFinal,
            'diubah_oleh'       => session()->get('nama_lengkap'),
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/dashboard')->with('success', 'Pengaturan sistem, identitas lembaga, logo, kredensial API, dan SELURUH PROMPT AI berhasil diperbarui!');
    }
}
