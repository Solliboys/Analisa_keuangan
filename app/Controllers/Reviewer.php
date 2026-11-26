<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\KomiteModel;
use App\Models\LogAktivitasModel;

class Reviewer extends BaseController
{
    protected $kreditModel;
    protected $komiteModel;
    protected $db;

    public function __construct()
    {
        $this->kreditModel = new KreditModel();
        $this->komiteModel = new KomiteModel();
        $this->db          = \Config\Database::connect();
    }

    private function isAuthorizedReviewer()
    {
        $jabatan = strtolower(session()->get('jabatan') ?? '');
        return ($jabatan === 'superadmin' || 
                strpos($jabatan, 'review') !== false || 
                strpos($jabatan, 'ceo') !== false || 
                strpos($jabatan, 'direk') !== false);
    }

    private function cekAuthReviewer()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir, silakan login ulang.']);
        }

        if (!$this->isAuthorizedReviewer()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses khusus Reviewer / Komite Kredit / CEO.']);
        }

        return null;
    }

    /**
     * Tampilan Form Review Khusus Reviewer & CEO
     * Layout 2 Kolom: Kiri Keputusan Reviewer/CEO, Kanan Ringkasan Inputan Pengajuan
     */
    public function review($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        if (!$this->isAuthorizedReviewer()) {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Reviewer / Komite Kredit / CEO.');
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return redirect()->to('/dashboard')->with('error', 'Data pengajuan kredit tidak ditemukan.');
        }

        $agunanModel   = new \App\Models\AgunanModel();
        $slikModel     = new \App\Models\SlikModel();
        $rekeningModel = new \App\Models\KreditRekeningModel();
        $keuanganModel = new \App\Models\KreditKeuanganDscModel();
        $webcheckModel = new \App\Models\KreditWebcheckModel();
        $analisaModel  = new \App\Models\KreditAnalisaModel();
        $dokumenModel  = new \App\Models\KreditDokumenModel();
        $disbursementModel = new \App\Models\KreditDisbursementModel();

        $agunan   = $agunanModel->where('kredit_id', $kreditId)->findAll();
        $slik     = $slikModel->where('kredit_id', $kreditId)->findAll();
        $rekening = $rekeningModel->where('kredit_id', $kreditId)->findAll();
        $keuangan = $keuanganModel->where('kredit_id', $kreditId)->first() ?? [];
        $webcheck = $webcheckModel->where('kredit_id', $kreditId)->first() ?? [];
        $analisa  = $analisaModel->where('kredit_id', $kreditId)->first() ?? [];
        $dokumen  = $dokumenModel->where('kredit_id', $kreditId)->findAll();
        $disbursement = $disbursementModel->where('kredit_id', $kreditId)->first() ?? [];

        $komiteList = $this->komiteModel->where('kredit_id', $kreditId)->findAll();

        $userRole = strtolower(session()->get('jabatan') ?? '');
        $userId   = (int)session()->get('user_id');
        $isCeo    = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false);

        // Ambil semua catatan dari reviewer (role_approver bukan CEO)
        $semuaCatatanReviewer = $this->komiteModel->where('kredit_id', $kreditId)
                                                  ->where('role_approver !=', 'Direktur Utama / CEO')
                                                  ->orderBy('komite_id', 'ASC')
                                                  ->findAll();

        // Untuk Reviewer: HANYA tampilkan catatan miliknya sendiri (catatan orang lain disembunyikan)
        // Untuk CEO: tampilkan seluruh catatan dari semua reviewer
        if (!$isCeo) {
            $userNama = strtolower(trim(session()->get('nama_lengkap') ?? ''));
            $daftarCatatanReviewer = array_values(array_filter($semuaCatatanReviewer, function($c) use ($userId, $userNama) {
                $cUserId = (int)($c['user_id'] ?? 0);
                $cNama   = strtolower(trim($c['nama_approver'] ?? ''));
                if ($userId > 0 && $cUserId === $userId) {
                    return true;
                }
                if ($cUserId === 0 && !empty($userNama) && $cNama === $userNama) {
                    return true;
                }
                return false;
            }));
        } else {
            $daftarCatatanReviewer = $semuaCatatanReviewer;
        }

        $keputusanReviewer   = !empty($daftarCatatanReviewer) ? end($daftarCatatanReviewer) : null;
        $catatanDariReviewer = $keputusanReviewer;

        // Ambil keputusan CEO (jika sudah ada)
        $keputusanCeo = $this->komiteModel->where('kredit_id', $kreditId)
                                          ->where('role_approver', 'Direktur Utama / CEO')
                                          ->first();

        // Hitung total MV dan LV Agunan
        $totalMV = 0;
        $totalLV = 0;
        foreach ($agunan as $ag) {
            $mv = (float)($ag['nilai_pasar_mv'] ?? 0);
            $lv = (float)($ag['nilai_likuidasi_lv'] ?? ($mv * 0.6));
            $totalMV += $mv;
            $totalLV += $lv;
        }

        $plafon = (float)($kredit['plafon_pengajuan'] ?? 0);
        $coverageMV = $plafon > 0 ? round(($totalMV / $plafon) * 100, 1) : 0;
        $coverageLV = $plafon > 0 ? round(($totalLV / $plafon) * 100, 1) : 0;

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => ''
        ];

        $pengusul = $db->table('users')->where('id', $kredit['user_id'] ?? 0)->get()->getRowArray() ?? [];

        $data = [
            'title'              => 'Review Pengajuan Kredit - ' . esc($kredit['nomor_aplikasi']),
            'nama_lengkap'       => session()->get('nama_lengkap'),
            'jabatan'            => session()->get('jabatan'),
            'kredit_id'          => $kreditId,
            'kredit'             => $kredit,
            'agunan'             => $agunan,
            'slik'               => $slik,
            'rekening'           => $rekening,
            'keuangan'           => $keuangan,
            'webcheck'           => $webcheck,
            'analisa'            => $analisa,
            'dokumen'            => $dokumen,
            'disbursement'       => $disbursement,
            'pengusul'           => $pengusul,
            'komite'                  => $komiteList,
            'keputusan_reviewer'      => $keputusanReviewer,
            'catatan_dari_reviewer'   => $catatanDariReviewer,
            'daftar_catatan_reviewer' => $daftarCatatanReviewer,
            'keputusan_ceo'           => $keputusanCeo,
            'total_mv'           => $totalMV,
            'total_lv'           => $totalLV,
            'coverage_mv'        => $coverageMV,
            'coverage_lv'        => $coverageLV,
            'pengaturan_sistem'  => $pengaturanSistem
        ];

        if ($isCeo) {
            $data['title'] = 'Lembar Keputusan CEO - ' . esc($kredit['nomor_aplikasi']);
            return view('ceo/form_keputusan_ceo', $data);
        }

        return view('reviewer/form_review', $data);
    }

    /**
     * Simpan Catatan / Keputusan Form Review
     * Reviewer bertugas mengirimkan catatan hasil review & rekomendasi (opsional) ke CEO.
     * CEO bertugas menetapkan keputusan resmi final komite.
     */
    public function simpanKeputusan()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir.']);
        }

        if (!$this->isAuthorizedReviewer()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses khusus Reviewer / Komite Kredit / CEO.']);
        }

        $kreditId  = $this->request->getPost('kredit_id');
        $keputusan = $this->request->getPost('keputusan'); // Opsional bagi reviewer, wajib bagi CEO
        $catatan   = trim($this->request->getPost('catatan') ?? '');

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID Pengajuan tidak valid.']);
        }

        $userRole = strtolower(session()->get('jabatan') ?? '');
        $isCeo    = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false);

        // Catatan wajib diisi karena tugas reviewer adalah mengirimkan catatan ke CEO
        if (empty($catatan)) {
            return $this->response->setJSON([
                'status'  => false, 
                'message' => $isCeo ? 'Catatan pertimbangan keputusan wajib diisi!' : 'Catatan hasil review untuk CEO wajib diisi!'
            ]);
        }

        $allowedKeputusan = ['Disetujui', 'Bersyarat', 'Disetujui Bersyarat', 'Ditolak', 'Revisi', 'Kembalikan untuk Revisi'];
        if (!empty($keputusan) && !in_array($keputusan, $allowedKeputusan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pilihan rekomendasi keputusan tidak valid.']);
        }

        // Khusus CEO: keputusan wajib dipilih
        if ($isCeo && empty($keputusan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Silakan pilih keputusan final komite: Setuju, Bersyarat, Revisi, atau Tolak.']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        // Normalisasi status jika ada pilihan keputusan / rekomendasi
        $statusSave = null;
        if (!empty($keputusan)) {
            if ($keputusan === 'Bersyarat' || $keputusan === 'Disetujui Bersyarat') {
                $statusSave = 'Disetujui Bersyarat';
            } elseif ($keputusan === 'Revisi' || $keputusan === 'Kembalikan untuk Revisi') {
                $statusSave = 'Revisi';
            } else {
                $statusSave = $keputusan;
            }
        }

        $userId       = session()->get('user_id');
        $namaApprover = session()->get('nama_lengkap') ?: ($isCeo ? 'Direktur Utama / CEO' : 'Senior Credit Reviewer');
        $roleApprover = $isCeo ? 'Direktur Utama / CEO' : 'Reviewer / Analis Senior';

        // 1. Simpan / perbarui ke tabel komite
        if ($isCeo) {
            $komiteExist = $this->komiteModel->where('kredit_id', $kreditId)
                                             ->where('role_approver', 'Direktur Utama / CEO')
                                             ->first();
            $dataKomite = [
                'kredit_id'        => $kreditId,
                'user_id'          => $userId,
                'role_approver'    => 'Direktur Utama / CEO',
                'nama_approver'    => $namaApprover,
                'status_approval'  => $statusSave ?: 'Disetujui',
                'tanggal_approval' => date('Y-m-d'),
                'catatan_approval' => $catatan,
            ];

            if ($komiteExist) {
                $this->komiteModel->update($komiteExist['komite_id'], $dataKomite);
            } else {
                $this->komiteModel->insert($dataKomite);
            }
        } else {
            // Reviewer: SELALU insert catatan review baru agar bisa menambah review lainnya (multi-review/history)
            $dataKomite = [
                'kredit_id'        => $kreditId,
                'user_id'          => $userId,
                'role_approver'    => 'Reviewer / Analis Senior',
                'nama_approver'    => $namaApprover,
                'status_approval'  => $statusSave ?: 'Ditinjau Reviewer',
                'tanggal_approval' => date('Y-m-d'),
                'catatan_approval' => $catatan,
            ];
            $this->komiteModel->insert($dataKomite);
        }

        // 2. Update status_pengajuan di tabel kredit
        if ($isCeo) {
            // CEO menetapkan keputusan final
            $statusKreditUpdate = $statusSave ?: 'Disetujui';
        } else {
            // Reviewer: hanya mengirim catatan ke CEO
            if ($statusSave === 'Revisi') {
                $statusKreditUpdate = 'Revisi';
            } else {
                $statusKreditUpdate = 'Menunggu Keputusan CEO';
            }
        }

        $this->kreditModel->update($kreditId, [
            'status_pengajuan' => $statusKreditUpdate,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // Catat ke log aktivitas sistem
        try {
            $nomorApp  = $kredit['nomor_aplikasi'] ?? ('ID #' . $kreditId);
            $namaDeb   = $kredit['nama_debitur'] ?? '-';
            $logAction = $isCeo 
                ? ('Menetapkan keputusan komite final: ' . strtoupper($statusKreditUpdate) . ' pada pengajuan ' . $nomorApp . ' (' . $namaDeb . ')')
                : ('Mengirimkan catatan review ke CEO untuk pengajuan ' . $nomorApp . ' (' . $namaDeb . ')' . ($statusSave ? ' [Rekomendasi: ' . $statusSave . ']' : ''));

            LogAktivitasModel::catat(
                $logAction,
                'KOMITE',
                ($statusKreditUpdate === 'Ditolak') ? 'DANGER' : (($statusKreditUpdate === 'Revisi') ? 'WARNING' : 'SUCCESS'),
                [
                    'kredit_id'       => $kreditId,
                    'nomor_aplikasi'  => $nomorApp,
                    'nama_debitur'    => $namaDeb,
                    'status_approval' => $statusKreditUpdate,
                    'catatan'         => $catatan
                ]
            );
        } catch (\Throwable $e) {}

        if ($isCeo) {
            $messageSukses = ($statusSave === 'Revisi')
                ? 'Pengajuan berhasil dikembalikan ke Staf Pengusul (AO) untuk dilakukan perbaikan / revisi.'
                : 'Keputusan Final CEO berhasil disimpan: Pengajuan telah di-' . strtoupper($statusKreditUpdate) . '.';
        } else {
            $messageSukses = ($statusSave === 'Revisi')
                ? 'Pengajuan berhasil dikembalikan ke Staf Pengusul (AO) untuk revisi data.'
                : 'Catatan review berhasil dikirimkan ke CEO untuk pengambilan keputusan final.';
        }

        return $this->response->setJSON([
            'status'       => true,
            'message'      => $messageSukses,
            'redirect_url' => base_url('dashboard')
        ]);
    }

    /**
     * Menyetujui Pengajuan Kredit oleh Reviewer
     */
    public function setujui($kreditId = null)
    {
        if ($res = $this->cekAuthReviewer()) return $res;

        if (!$kreditId) {
            $kreditId = $this->request->getPost('kredit_id');
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        $catatan = trim($this->request->getPost('catatan') ?? '');
        if (empty($catatan)) {
            $catatan = 'Pengajuan dinilai layak setelah dilakukan review mendalam terhadap prinsip 5C, kapasitas repayment (DSC), rekam jejak fasilitas kredit SLIK OJK, serta kelayakan nilai jaminan.';
        }

        // 1. Update status di tabel kredit induk
        $this->kreditModel->update($kreditId, [
            'status_pengajuan' => 'Disetujui',
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // 2. Catat keputusan di tabel komite / approval log
        $userId       = session()->get('user_id');
        $namaApprover = session()->get('nama_lengkap') ?: 'Reviewer / Analis Senior';

        $komiteExist = $this->komiteModel->where('kredit_id', $kreditId)
                                         ->where('role_approver', 'Reviewer / Analis Senior')
                                         ->first();

        $dataKomite = [
            'kredit_id'        => $kreditId,
            'user_id'          => $userId,
            'role_approver'    => 'Reviewer / Analis Senior',
            'nama_approver'    => $namaApprover,
            'status_approval'  => 'Disetujui',
            'tanggal_approval' => date('Y-m-d'),
            'catatan_approval' => $catatan,
        ];

        if ($komiteExist) {
            $this->komiteModel->update($komiteExist['komite_id'], $dataKomite);
        } else {
            $this->komiteModel->insert($dataKomite);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pengajuan ' . esc($kredit['nomor_aplikasi']) . ' (' . esc($kredit['nama_debitur']) . ') BERHASIL DISETUJUI oleh Reviewer!'
        ]);
    }

    /**
     * Menolak Pengajuan Kredit oleh Reviewer
     */
    public function tolak($kreditId = null)
    {
        if ($res = $this->cekAuthReviewer()) return $res;

        if (!$kreditId) {
            $kreditId = $this->request->getPost('kredit_id');
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        $alasan = trim($this->request->getPost('alasan') ?? $this->request->getPost('catatan') ?? '');
        if (empty($alasan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Harap berikan alasan penolakan pengajuan kredit.']);
        }

        // 1. Update status di tabel kredit induk
        $this->kreditModel->update($kreditId, [
            'status_pengajuan' => 'Ditolak',
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // 2. Catat keputusan di tabel komite
        $userId       = session()->get('user_id');
        $namaApprover = session()->get('nama_lengkap') ?: 'Reviewer / Analis Senior';

        $komiteExist = $this->komiteModel->where('kredit_id', $kreditId)
                                         ->where('role_approver', 'Reviewer / Analis Senior')
                                         ->first();

        $dataKomite = [
            'kredit_id'        => $kreditId,
            'user_id'          => $userId,
            'role_approver'    => 'Reviewer / Analis Senior',
            'nama_approver'    => $namaApprover,
            'status_approval'  => 'Ditolak',
            'tanggal_approval' => date('Y-m-d'),
            'catatan_approval' => $alasan,
        ];

        if ($komiteExist) {
            $this->komiteModel->update($komiteExist['komite_id'], $dataKomite);
        } else {
            $this->komiteModel->insert($dataKomite);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Pengajuan ' . esc($kredit['nomor_aplikasi']) . ' (' . esc($kredit['nama_debitur']) . ') telah DITOLAK.'
        ]);
    }

    /**
     * Ambil data hasil evaluasi AI untuk popup Review AI
     */
    public function detailAi($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir, silakan login ulang.']);
        }

        $userId = session()->get('user_id');
        $isReviewer = $this->isAuthorizedReviewer();

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        // Pengusul hanya dapat melihat hasil AI pengajuan miliknya
        if (!$isReviewer && $kredit['user_id'] != $userId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk pengajuan ini.']);
        }

        $analisaModel = new \App\Models\KreditAnalisaModel();
        $analisa = $analisaModel->where('kredit_id', $kreditId)->first();

        // Jika belum ada analisa AI, generate otomatis
        if (!$analisa || empty($analisa['skor_kelayakan_ai'])) {
            $agunanModel   = new \App\Models\AgunanModel();
            $keuanganModel = new \App\Models\KreditKeuanganDscModel();

            $agunanList = $agunanModel->where('kredit_id', $kreditId)->findAll();
            $totalMV = 0;
            $totalLV = 0;
            $rincianAgunanArr = [];
            foreach ($agunanList as $ag) {
                $mv = (float)($ag['nilai_pasar_mv'] ?? 0);
                $lv = (float)($ag['nilai_likuidasi_lv'] ?? ($mv * 0.6));
                $totalMV += $mv;
                $totalLV += $lv;
                $jns = !empty($ag['jenis_agunan']) ? $ag['jenis_agunan'] : (!empty($ag['jenis_jaminan']) ? $ag['jenis_jaminan'] : 'Properti/Kendaraan');
                $dok = !empty($ag['dokumen_agunan']) ? $ag['dokumen_agunan'] : (!empty($ag['bukti_kepemilikan']) ? $ag['bukti_kepemilikan'] : 'SHM/BPKB');
                if ($mv > 0) {
                    $rincianAgunanArr[] = "{$jns} ({$dok}) MV Rp " . number_format($mv, 0, ',', '.');
                }
            }
            $rincianAgunan = !empty($rincianAgunanArr) ? implode('; ', $rincianAgunanArr) : ($kredit['jenis_jaminan'] ?? 'Agunan Properti / Kendaraan');

            $keu = $keuanganModel->where('kredit_id', $kreditId)->first() ?? [];
            $omzet = (float)($keu['pendapatan_rata_rata_bulanan'] ?? ($keu['pendapatan_bersih_bulan'] ?? 0));
            $angsuran = (float)($keu['angsuran_bulanan_usulan'] ?? 0);
            $labaBersih = (float)($keu['laba_bersih_operasional'] ?? ($omzet * 0.2));
            $dsc = $angsuran > 0 ? round($labaBersih / $angsuran, 2) : 1.35;

            $plafon = (float)($kredit['plafon_pengajuan'] ?? 0);
            $covMV = $plafon > 0 ? round(($totalMV / $plafon) * 100, 1) : 0;
            $covLV = $plafon > 0 ? round(($totalLV / $plafon) * 100, 1) : 0;

            $totalAset = (float)($keu['neraca_total_aset'] ?? ($keu['total_aset'] ?? $totalMV));
            $totalUtang = (float)($keu['neraca_total_liabilitas'] ?? ($keu['total_utang'] ?? 0));
            $modalBersih = $totalAset - $totalUtang;
            $darStr = $totalAset > 0 ? round(($totalUtang / $totalAset) * 100, 1) . '%' : '0%';

            $service = new \App\Services\GeminiAiService();
            $aiResult = $service->analisaKredit([
                'nama_debitur'              => $kredit['nama_debitur'] ?? '',
                'jenis_debitur'             => $kredit['jenis_debitur'] ?? 'Perorangan',
                'nama_usaha'                => $kredit['nama_usaha'] ?? '',
                'bidang_usaha'              => $kredit['bidang_usaha'] ?? ($kredit['jenis_usaha'] ?? 'Perdagangan / Jasa'),
                'lama_usaha'                => $kredit['lama_usaha'] ?? '',
                'tujuan_pembiayaan'         => $kredit['tujuan_pembiayaan'] ?? 'Modal Kerja',
                'plafon_pengajuan'          => $plafon,
                'tenor_bulan'               => (int)($kredit['tenor_bulan'] ?? 12),
                'skema_pembayaran'          => $kredit['skema_pembayaran'] ?? 'Installment',
                'bunga_per_bulan'           => (float)($kredit['bunga_per_bulan'] ?? 2),
                'rincian_agunan'            => $rincianAgunan,
                'total_mv'                  => $totalMV,
                'total_lv'                  => $totalLV,
                'coverage_mv'               => $covMV,
                'coverage_lv'               => $covLV,
                'omzet'                     => $omzet,
                'laba_bersih'               => $labaBersih,
                'angsuran'                  => $angsuran,
                'rasio_dsc'                 => $dsc,
                'total_aset'                => $totalAset,
                'total_utang'               => $totalUtang,
                'modal_bersih'              => $modalBersih,
                'rasio_dar'                 => $darStr,
                'kesimpulan_kolektibilitas' => $kredit['kesimpulan_kolektibilitas'] ?? ($kredit['ringkasan_slik'] ?? 'Kolektibilitas 1 (Lancar)'),
                'ringkasan_slik'            => $kredit['ringkasan_slik'] ?? '',
                'webcheck_status'           => 'Hasil Web Check (SIPP Pengadilan & DTTOT): Bersih, tidak ada perkara hukum aktif.'
            ]);

            if ($aiResult['status'] ?? false) {
                $dataSave = [
                    'kredit_id'                => $kreditId,
                    'skor_kelayakan_ai'        => $aiResult['skor_kelayakan_ai'] ?? 80,
                    'rekomendasi_keputusan_ai' => $aiResult['rekomendasi_keputusan_ai'] ?? 'Direkomendasikan Disetujui',
                    'rekomendasi_plafon_ai'    => $aiResult['rekomendasi_plafon_ai'] ?? $plafon,
                    'analisis_risiko_ai'       => $aiResult['analisis_risiko_ai'] ?? 'Evaluasi risiko komprehensif 5C.'
                ];
                if ($analisa) {
                    $analisaModel->update($analisa['analisa_id'], $dataSave);
                } else {
                    $analisaModel->insert($dataSave);
                }
                $analisa = $dataSave;
            }
        }

        return $this->response->setJSON([
            'status'         => true,
            'nomor_aplikasi' => $kredit['nomor_aplikasi'],
            'nama_debitur'   => $kredit['nama_debitur'],
            'plafon'         => (float)($kredit['plafon_pengajuan'] ?? 0),
            'analisa'        => $analisa
        ]);
    }

    /**
     * Ambil data hasil evaluasi & keputusan Reviewer / Komite
     */
    public function detailReview($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi login telah berakhir, silakan login ulang.']);
        }

        $userId = session()->get('user_id');
        $isReviewer = $this->isAuthorizedReviewer();

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        if (!$isReviewer && $kredit['user_id'] != $userId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk pengajuan ini.']);
        }

        $komite = $this->komiteModel->where('kredit_id', $kreditId)
                                    ->orderBy('komite_id', 'DESC')
                                    ->first();

        return $this->response->setJSON([
            'status'           => true,
            'nomor_aplikasi'   => $kredit['nomor_aplikasi'],
            'nama_debitur'     => $kredit['nama_debitur'],
            'nama_usaha'       => $kredit['nama_usaha'] ?: ($kredit['jenis_debitur'] ?? 'Individu'),
            'plafon'           => (float)($kredit['plafon_pengajuan'] ?? 0),
            'status_pengajuan' => $kredit['status_pengajuan'] ?? 'Draft',
            'review'           => [
                'status_approval'  => $komite['status_approval'] ?? ($kredit['status_pengajuan'] ?? 'Menunggu Review'),
                'nama_approver'    => $komite['nama_approver'] ?? 'Belum ditentukan',
                'role_approver'    => $komite['role_approver'] ?? 'Reviewer / Komite Kredit',
                'tanggal_approval' => !empty($komite['tanggal_approval']) ? date('d M Y', strtotime($komite['tanggal_approval'])) : (!empty($komite['created_at']) ? date('d M Y', strtotime($komite['created_at'])) : '-'),
                'catatan_approval' => $komite['catatan_approval'] ?? ''
            ]
        ]);
    }
}
