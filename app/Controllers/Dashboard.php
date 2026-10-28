<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\UserModel;
use App\Models\LogAktivitasModel;

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

        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => '',
            'api_key'       => getenv('GEMINI_API_KEY') ?: '',
            'model_ai'      => 'gemini-3.8-flash',
            'api_endpoint'  => 'https://generativelanguage.googleapis.com/v1beta/models',
            'prompt_sistem' => 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).'
        ];

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
                if ($st === 'diajukan' || $st === 'draft') {
                    $pendingKomite++;
                } elseif (strpos($st, 'setuju') !== false) {
                    $approvedKomite++;
                } elseif (strpos($st, 'tolak') !== false) {
                    $rejectedKomite++;
                }
            }

            $daftarUsers = $userModel->orderBy('created_at', 'DESC')->findAll();
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

            $recentLogs = [];
            try {
                $recentLogs = $db->table('log_aktivitas')->orderBy('created_at', 'DESC')->limit(5)->get()->getResultArray() ?? [];
            } catch (\Throwable $e) {
                $recentLogs = [];
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
                'recent_logs'           => $recentLogs,
                'pengaturan_sistem'     => $pengaturanSistem,
                'pengaturan_ai'         => $pengaturanAi
            ];

            return view('superadmin/dashboar_admin', $data);

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
                'title'               => 'Workspace Account Officer | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
                'nama_lengkap'        => session()->get('nama_lengkap'),
                'jabatan'             => $jabatan,
                'total_pengajuan'     => $totalPengajuanSaya,
                'total_plafon'        => $totalPlafonSaya,
                'total_draft'         => $draftSaya,
                'total_diajukan'      => $diajukanSaya,
                'total_disetujui'     => $disetujuiSaya,
                'daftar_pengajuan'    => array_slice($kreditSaya, 0, 8),
                'pengaturan_sistem'   => $pengaturanSistem
            ];

            return view('pengusul/dashboard_pengusul', $data);

        } elseif ($role === 'petugas_slik' || $role === 'petugas slik' || $role === 'slik') {
            // Data untuk Petugas SLIK
            $semuaKredit = $kreditModel->orderBy('created_at', 'DESC')->findAll();
            $slikModel   = new \App\Models\SlikModel();
            
            $totalPengajuan = count($semuaKredit);
            $totalPlafon    = 0;
            $totalDraft     = 0;
            $totalDiajukan  = 0;
            $totalDisetujui = 0;
            $totalSlikChecked = 0;

            foreach ($semuaKredit as &$k) {
                $totalPlafon += (float)($k['plafon_pengajuan'] ?? 0);
                $st = strtolower($k['status_pengajuan'] ?? '');
                if ($st === 'draft') $totalDraft++;
                elseif ($st === 'diajukan') $totalDiajukan++;
                elseif ($st === 'disetujui') $totalDisetujui++;

                // Periksa data fasilitas SLIK
                $slikRows = $slikModel->where('kredit_id', $k['kredit_id'])->findAll();
                $totalBakiDebet = 0;
                $adaTunggakan = false;
                foreach ($slikRows as $sr) {
                    $totalBakiDebet += (float)($sr['outstanding'] ?? 0);
                    $kol = strtolower($sr['kelancaran'] ?? '');
                    if ($kol !== 'lancar' && !empty($kol)) {
                        $adaTunggakan = true;
                    }
                }
                $k['total_slik_records']    = count($slikRows);
                $k['total_baki_debet_slik'] = $totalBakiDebet;
                $k['ada_tunggakan_slik']    = $adaTunggakan;
                
                // Status Terbit SLIK (Belum Terbit / Terbit) & Hasil SLIK
                if (count($slikRows) > 0 || !empty($k['ringkasan_slik'])) {
                    $totalSlikChecked++;
                    $k['status_slik'] = 'Terbit';
                    if ($adaTunggakan) {
                        $k['hasil_slik'] = 'Kolektibilitas 2-5 (Ada Tunggakan)';
                    } else {
                        $k['hasil_slik'] = 'Kol 1 (Lancar / Bersih)';
                    }
                } else {
                    $k['status_slik'] = 'Belum Terbit';
                    $k['hasil_slik']  = 'Menunggu Pengecekan';
                }
            }
            unset($k);

            $data = [
                'title'               => 'Workspace Petugas SLIK | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
                'nama_lengkap'        => session()->get('nama_lengkap'),
                'jabatan'             => $jabatan,
                'total_pengajuan'     => $totalPengajuan,
                'total_plafon'        => $totalPlafon,
                'total_draft'         => $totalDraft,
                'total_diajukan'      => $totalDiajukan,
                'total_disetujui'     => $totalDisetujui,
                'total_slik_checked'  => $totalSlikChecked,
                'daftar_pengajuan'    => array_slice($semuaKredit, 0, 15),
                'pengaturan_sistem'   => $pengaturanSistem
            ];

            return view('petugas_slik/dashboard_slik', $data);

        } elseif ($role === 'reviewer' || strpos($role, 'review') !== false) {
            // Data untuk Reviewer / Komite Kredit
            $semuaKredit  = $kreditModel->orderBy('created_at', 'DESC')->findAll();
            $slikModel    = new \App\Models\SlikModel();
            $analisaModel = new \App\Models\KreditAnalisaModel();
            $komiteModel  = new \App\Models\KomiteModel();
            
            $semuaAnalisa = $analisaModel->findAll();
            $mapAnalisa   = [];
            foreach ($semuaAnalisa as $a) {
                $mapAnalisa[$a['kredit_id']] = $a;
            }

            // Ambil keputusan & catatan reviewer dari tabel komite
            $semuaKomite  = $komiteModel->orderBy('komite_id', 'DESC')->findAll();
            $mapKomite    = [];
            foreach ($semuaKomite as $km) {
                if (!isset($mapKomite[$km['kredit_id']]) && !empty($km['catatan_approval'])) {
                    $mapKomite[$km['kredit_id']] = $km;
                }
            }
            // Fallback record komite tanpa filter catatan jika ada
            foreach ($semuaKomite as $km) {
                if (!isset($mapKomite[$km['kredit_id']])) {
                    $mapKomite[$km['kredit_id']] = $km;
                }
            }
            
            $totalPengajuan = count($semuaKredit);
            $totalPlafon    = 0;
            $totalDraft     = 0;
            $totalDiajukan  = 0;
            $totalDisetujui = 0;
            $totalDitolak   = 0;

            foreach ($semuaKredit as &$k) {
                $totalPlafon += (float)($k['plafon_pengajuan'] ?? 0);
                
                // Ambil catatan komite / reviewer
                $km = $mapKomite[$k['kredit_id']] ?? null;
                $k['catatan_reviewer'] = !empty($km['catatan_approval']) ? $km['catatan_approval'] : '';
                $k['status_komite']    = !empty($km['status_approval']) ? $km['status_approval'] : '';

                // Sinkronkan status jika kosong di kredit tapi ada di komite
                if (empty($k['status_pengajuan']) && !empty($k['status_komite'])) {
                    $k['status_pengajuan'] = $k['status_komite'];
                }

                $st = strtolower($k['status_pengajuan'] ?? '');
                if ($st === 'draft') $totalDraft++;
                elseif ($st === 'diajukan') $totalDiajukan++;
                elseif (strpos($st, 'setuju') !== false || strpos($st, 'bersyarat') !== false) $totalDisetujui++;
                elseif (strpos($st, 'tolak') !== false) $totalDitolak++;

                // Hubungkan data analisa AI jika ada
                $k['analisa'] = $mapAnalisa[$k['kredit_id']] ?? null;

                // Periksa data fasilitas SLIK
                $slikRows = $slikModel->where('kredit_id', $k['kredit_id'])->findAll();
                $totalBakiDebet = 0;
                $adaTunggakan = false;
                foreach ($slikRows as $sr) {
                    $totalBakiDebet += (float)($sr['outstanding'] ?? 0);
                    $kol = strtolower($sr['kelancaran'] ?? '');
                    if ($kol !== 'lancar' && !empty($kol)) {
                        $adaTunggakan = true;
                    }
                }
                $k['total_slik_records']    = count($slikRows);
                $k['total_baki_debet_slik'] = $totalBakiDebet;
                $k['ada_tunggakan_slik']    = $adaTunggakan;
                
                // Status Terbit SLIK
                if (count($slikRows) > 0 || !empty($k['ringkasan_slik']) || ($k['status_slik'] ?? '') === 'Terbit') {
                    $k['status_slik'] = 'Terbit';
                    $k['hasil_slik']  = $adaTunggakan ? 'Kolektibilitas 2-5' : 'Kol 1 (Lancar)';
                } else {
                    $k['status_slik'] = 'Belum Terbit';
                    $k['hasil_slik']  = 'Menunggu Pengecekan';
                }
            }
            unset($k);

            $data = [
                'title'               => 'Workspace Reviewer & Komite Kredit | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
                'nama_lengkap'        => session()->get('nama_lengkap'),
                'jabatan'             => $jabatan,
                'total_pengajuan'     => $totalPengajuan,
                'total_plafon'        => $totalPlafon,
                'total_menunggu'      => $totalDiajukan,
                'total_disetujui'     => $totalDisetujui,
                'total_ditolak'       => $totalDitolak,
                'total_draft'         => $totalDraft,
                'daftar_pengajuan'    => $semuaKredit,
                'pengaturan_sistem'   => $pengaturanSistem
            ];

            return view('reviewer/dashboard_review', $data);

        } elseif ($role === 'ceo' || $role === 'direktur' || $role === 'direksi' || strpos($role, 'ceo') !== false || strpos($role, 'direk') !== false) {
            // Data untuk CEO / Direktur / Direksi Eksekutif
            $semuaKredit  = $kreditModel->orderBy('created_at', 'DESC')->findAll();
            $slikModel    = new \App\Models\SlikModel();
            $analisaModel = new \App\Models\KreditAnalisaModel();
            $komiteModel  = new \App\Models\KomiteModel();
            
            $semuaAnalisa = $analisaModel->findAll();
            $mapAnalisa   = [];
            foreach ($semuaAnalisa as $a) {
                $mapAnalisa[$a['kredit_id']] = $a;
            }

            // Ambil keputusan & catatan reviewer dari tabel komite
            $semuaKomite  = $komiteModel->orderBy('komite_id', 'DESC')->findAll();
            $mapKomite    = [];
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
            
            $totalPengajuan = count($semuaKredit);
            $totalPlafon    = 0;
            $totalDraft     = 0;
            $totalDiajukan  = 0;
            $totalDisetujui = 0;
            $totalDitolak   = 0;

            foreach ($semuaKredit as &$k) {
                $totalPlafon += (float)($k['plafon_pengajuan'] ?? 0);
                
                $km = $mapKomite[$k['kredit_id']] ?? null;
                $k['catatan_reviewer'] = !empty($km['catatan_approval']) ? $km['catatan_approval'] : '';
                $k['status_komite']    = !empty($km['status_approval']) ? $km['status_approval'] : '';

                if (empty($k['status_pengajuan']) && !empty($k['status_komite'])) {
                    $k['status_pengajuan'] = $k['status_komite'];
                }

                $st = strtolower($k['status_pengajuan'] ?? '');
                if ($st === 'draft') $totalDraft++;
                elseif ($st === 'diajukan') $totalDiajukan++;
                elseif (strpos($st, 'setuju') !== false || strpos($st, 'bersyarat') !== false) $totalDisetujui++;
                elseif (strpos($st, 'tolak') !== false) $totalDitolak++;

                $k['analisa'] = $mapAnalisa[$k['kredit_id']] ?? null;

                $slikRows = $slikModel->where('kredit_id', $k['kredit_id'])->findAll();
                $totalBakiDebet = 0;
                $adaTunggakan = false;
                foreach ($slikRows as $sr) {
                    $totalBakiDebet += (float)($sr['outstanding'] ?? 0);
                    $kol = strtolower($sr['kelancaran'] ?? '');
                    if ($kol !== 'lancar' && !empty($kol)) {
                        $adaTunggakan = true;
                    }
                }
                $k['total_slik_records']    = count($slikRows);
                $k['total_baki_debet_slik'] = $totalBakiDebet;
                $k['ada_tunggakan_slik']    = $adaTunggakan;
                
                if (count($slikRows) > 0 || !empty($k['ringkasan_slik']) || ($k['status_slik'] ?? '') === 'Terbit') {
                    $k['status_slik'] = 'Terbit';
                    $k['hasil_slik']  = $adaTunggakan ? 'Kolektibilitas 2-5' : 'Kol 1 (Lancar)';
                } else {
                    $k['status_slik'] = 'Belum Terbit';
                    $k['hasil_slik']  = 'Menunggu Pengecekan';
                }
            }
            unset($k);

            // Ambil riwayat log aktivitas terbaru
            $recentLogs = [];
            try {
                $recentLogs = $db->table('log_aktivitas')->orderBy('created_at', 'DESC')->limit(15)->get()->getResultArray() ?? [];
            } catch (\Throwable $e) {
                $recentLogs = [];
            }

            $data = [
                'title'               => 'Executive Command Dashboard | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
                'nama_lengkap'        => session()->get('nama_lengkap'),
                'jabatan'             => $jabatan,
                'total_pengajuan'     => $totalPengajuan,
                'total_plafon'        => $totalPlafon,
                'total_menunggu'      => $totalDiajukan,
                'total_disetujui'     => $totalDisetujui,
                'total_ditolak'       => $totalDitolak,
                'total_draft'         => $totalDraft,
                'daftar_pengajuan'    => $semuaKredit,
                'recent_logs'         => $recentLogs,
                'pengaturan_sistem'   => $pengaturanSistem
            ];

            return view('ceo/dashboard_ceo', $data);

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

        $namaAplikasi   = trim($this->request->getPost('nama_aplikasi') ?? 'OTW Credit Engine & CAM');
        $sloganAplikasi = trim($this->request->getPost('slogan_aplikasi') ?? 'Sistem Analisa Kredit');
        $namaLembaga    = trim($this->request->getPost('nama_lembaga') ?? 'PT BPR / Lembaga Pembiayaan');
        $alamatLembaga  = trim($this->request->getPost('alamat_lembaga') ?? 'BPR Financial Center, Lantai 8, Jakarta Selatan');
        $emailLembaga   = trim($this->request->getPost('email_lembaga') ?? 'info@otwkredit.id');
        $teleponLembaga = trim($this->request->getPost('telepon_lembaga') ?? '(021) 5088-2900');

        // Handle upload logo
        $logoFile = $this->request->getFile('logo_file');
        $logoPath = null;
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $uploadFolder = FCPATH . 'uploads/logo/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }
            $logoName = 'logo_' . time() . '.' . $logoFile->getExtension();
            $logoFile->move($uploadFolder, $logoName);
            $fullLogoPath = $uploadFolder . $logoName;

            // Kompresi otomatis gambar logo (maks 600x600 px, kualitas 80%)
            \App\Libraries\ImageCompressor::compress($fullLogoPath, 600, 600, 80);

            $logoPath = 'uploads/logo/' . $logoName;
        }

        $updateData = [
            'nama_aplikasi'   => $namaAplikasi,
            'slogan_aplikasi' => $sloganAplikasi,
            'nama_lembaga'    => $namaLembaga,
            'alamat_lembaga'  => $alamatLembaga,
            'email_lembaga'   => $emailLembaga,
            'telepon_lembaga' => $teleponLembaga,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        if ($logoPath) {
            $updateData['logo_aplikasi'] = $logoPath;
        } elseif ($this->request->getPost('hapus_logo') === '1') {
            $updateData['logo_aplikasi'] = '';
        }

        // Sinkronisasi field AI hanya jika dikirim dari form (opsional)
        $aiUpdate = [];
        if ($this->request->getPost('api_key') !== null) {
            $apiKey = trim($this->request->getPost('api_key'));
            if (!empty($apiKey)) {
                $updateData['api_key'] = $apiKey;
                $aiUpdate['api_key']   = $apiKey;
            }
        }
        if ($this->request->getPost('model_ai') !== null) {
            $modelAi = trim($this->request->getPost('model_ai'));
            $updateData['model_ai'] = $modelAi;
            $aiUpdate['model_ai']   = $modelAi;
        }
        if ($this->request->getPost('api_endpoint') !== null) {
            $updateData['api_endpoint'] = trim($this->request->getPost('api_endpoint'));
        }
        if ($this->request->getPost('prompt_sistem') !== null) {
            $updateData['prompt_sistem'] = trim($this->request->getPost('prompt_sistem'));
        }

        $promptKeys = ['prompt_character', 'prompt_capacity', 'prompt_capital', 'prompt_collateral', 'prompt_condition', 'prompt_final'];
        foreach ($promptKeys as $pk) {
            if ($this->request->getPost($pk) !== null) {
                $aiUpdate[$pk] = trim($this->request->getPost($pk));
            }
        }

        // Simpan ke pengaturan_sistem
        $db->table('pengaturan_sistem')->where('id', 1)->update($updateData);

        // Catat Audit Trail Log
        LogAktivitasModel::catat(
            'Memperbarui identitas sistem & lembaga (' . $namaAplikasi . ')',
            'PENGATURAN',
            'SUCCESS',
            [
                'nama_aplikasi'   => $namaAplikasi,
                'slogan_aplikasi' => $sloganAplikasi,
                'nama_lembaga'    => $namaLembaga,
                'telepon_lembaga' => $teleponLembaga,
                'logo_diunggah'   => !empty($logoPath)
            ]
        );

        // Simpan ke pengaturan_ai jika ada input AI yang dikirim
        if (!empty($aiUpdate)) {
            $aiUpdate['diubah_oleh'] = session()->get('nama_lengkap');
            $aiUpdate['updated_at']  = date('Y-m-d H:i:s');
            $db->table('pengaturan_ai')->where('id', 1)->update($aiUpdate);
        }

        $successMsg = 'Pengaturan sistem, identitas lembaga, kontak resmi, dan logo instansi berhasil diperbarui!';

        if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $successMsg
            ]);
        }

        // Redirect ke referrer jika dari superadmin/pengaturan
        $redirectUrl = str_contains($this->request->getServer('HTTP_REFERER') ?? '', 'superadmin/pengaturan') 
            ? '/superadmin/pengaturan' 
            : '/dashboard';

        return redirect()->to($redirectUrl)->with('success', $successMsg);
    }

    public function profil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));
        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => ''
        ];

        $data = [
            'title'             => 'Profil Saya | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
            'user'              => $user,
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('profil', $data);
    }

    public function updateProfil()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        $userId = session()->get('user_id');
        $userModel = new UserModel();

        $namaLengkap = trim($this->request->getPost('nama_lengkap') ?? '');
        $username    = trim($this->request->getPost('username') ?? '');
        $email       = trim($this->request->getPost('email') ?? '');
        $nomorHp     = trim($this->request->getPost('nomor_hp') ?? '');
        $password    = $this->request->getPost('password');

        $updateData = [
            'nama_lengkap' => $namaLengkap,
            'username'     => $username,
            'email'        => $email,
            'nomor_hp'     => $nomorHp,
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Handle upload foto profil
        $fotoFile = $this->request->getFile('foto_file');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $uploadFolder = FCPATH . 'uploads/profil/';
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777, true);
            }
            $fotoName = 'profil_' . $userId . '_' . time() . '.' . $fotoFile->getExtension();
            $fotoFile->move($uploadFolder, $fotoName);
            $fullFotoPath = $uploadFolder . $fotoName;

            // Kompresi otomatis foto profil (maks 500x500 px, kualitas 80%)
            \App\Libraries\ImageCompressor::compress($fullFotoPath, 500, 500, 80);

            $updateData['foto'] = 'uploads/profil/' . $fotoName;
            session()->set('foto', 'uploads/profil/' . $fotoName);
        }

        $userModel->update($userId, $updateData);

        session()->set([
            'nama_lengkap' => $namaLengkap,
            'username'     => $username,
            'email'        => $email,
            'nomor_hp'     => $nomorHp,
        ]);

        return redirect()->to('/profil')->with('success', 'Profil dan foto akun Anda berhasil diperbarui!');
    }

    public function laporanAudit()
    {
        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan'
        ];

        return view('audit/laporan_qa_qc', [
            'title'             => 'Laporan Resmi Audit QA & QC - OTW Credit Engine & CAM',
            'pengaturan_sistem' => $pengaturanSistem,
            'tanggal_audit'     => date('d F Y')
        ]);
    }
}

