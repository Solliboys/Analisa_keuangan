<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\KomiteModel;

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

        $agunan   = $agunanModel->where('kredit_id', $kreditId)->findAll();
        $slik     = $slikModel->where('kredit_id', $kreditId)->findAll();
        $rekening = $rekeningModel->where('kredit_id', $kreditId)->findAll();
        $keuangan = $keuanganModel->where('kredit_id', $kreditId)->first() ?? [];
        $webcheck = $webcheckModel->where('kredit_id', $kreditId)->first() ?? [];
        $analisa  = $analisaModel->where('kredit_id', $kreditId)->first() ?? [];
        $dokumen  = $dokumenModel->where('kredit_id', $kreditId)->findAll();

        $komiteList = $this->komiteModel->where('kredit_id', $kreditId)->findAll();
        $keputusanReviewer = $this->komiteModel->where('kredit_id', $kreditId)
                                               ->where('role_approver', 'Reviewer / Analis Senior')
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
            'komite'             => $komiteList,
            'keputusan_reviewer' => $keputusanReviewer,
            'total_mv'           => $totalMV,
            'total_lv'           => $totalLV,
            'coverage_mv'        => $coverageMV,
            'coverage_lv'        => $coverageLV,
            'pengaturan_sistem'  => $pengaturanSistem
        ];

        return view('reviewer/form_review', $data);
    }

    /**
     * Simpan Keputusan Form Review oleh Reviewer (Setuju / Tolak)
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
        $keputusan = $this->request->getPost('keputusan'); // 'Disetujui' atau 'Ditolak'
        $catatan   = trim($this->request->getPost('catatan') ?? '');

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID Pengajuan tidak valid.']);
        }

        $allowedKeputusan = ['Disetujui', 'Bersyarat', 'Disetujui Bersyarat', 'Ditolak'];
        if (!in_array($keputusan, $allowedKeputusan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Silakan pilih keputusan: Setuju, Bersyarat, atau Tolak.']);
        }

        if ($keputusan === 'Ditolak' && empty($catatan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Alasan penolakan wajib diisi pada catatan keputusan!']);
        }

        if (($keputusan === 'Bersyarat' || $keputusan === 'Disetujui Bersyarat') && empty($catatan)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Persyaratan khusus persetujuan wajib diisi pada catatan keputusan!']);
        }

        if (empty($catatan)) {
            $catatan = ($keputusan === 'Disetujui') 
                ? 'Pengajuan dinilai layak setelah dilakukan review mendalam terhadap prinsip 5C, kapasitas repayment (DSC), rekam jejak fasilitas kredit SLIK OJK, serta kelayakan nilai jaminan.'
                : 'Pengajuan disetujui dengan pemenuhan syarat khusus sebelum realisasi.';
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
        }

        // Normalisasi nama status
        $statusSave = ($keputusan === 'Bersyarat' || $keputusan === 'Disetujui Bersyarat') ? 'Disetujui Bersyarat' : $keputusan;

        // 1. Update status_pengajuan di tabel kredit
        $this->kreditModel->update($kreditId, [
            'status_pengajuan' => $statusSave,
            'updated_at'       => date('Y-m-d H:i:s')
        ]);

        // 2. Simpan / perbarui ke tabel komite
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
            'status_approval'  => $statusSave,
            'tanggal_approval' => date('Y-m-d'),
            'catatan_approval' => $catatan,
        ];

        if ($komiteExist) {
            $this->komiteModel->update($komiteExist['komite_id'], $dataKomite);
        } else {
            $this->komiteModel->insert($dataKomite);
        }

        return $this->response->setJSON([
            'status'       => true,
            'message'      => 'Keputusan Reviewer berhasil disimpan: Pengajuan telah di-' . strtoupper($keputusan) . '.',
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
        if ($res = $this->cekAuthReviewer()) return $res;

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan.']);
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
            foreach ($agunanList as $ag) {
                $mv = (float)($ag['nilai_pasar_mv'] ?? 0);
                $lv = (float)($ag['nilai_likuidasi_lv'] ?? ($mv * 0.6));
                $totalMV += $mv;
                $totalLV += $lv;
            }

            $keu = $keuanganModel->where('kredit_id', $kreditId)->first() ?? [];
            $omzet = (float)($keu['pendapatan_rata_rata_bulanan'] ?? ($keu['pendapatan_bersih_bulan'] ?? 0));
            $angsuran = (float)($keu['angsuran_bulanan_usulan'] ?? 0);
            $labaBersih = (float)($keu['laba_bersih_operasional'] ?? ($omzet * 0.2));
            $dsc = $angsuran > 0 ? round($labaBersih / $angsuran, 2) : 1.35;

            $plafon = (float)($kredit['plafon_pengajuan'] ?? 0);
            $covMV = $plafon > 0 ? round(($totalMV / $plafon) * 100, 1) : 0;
            $covLV = $plafon > 0 ? round(($totalLV / $plafon) * 100, 1) : 0;

            $service = new \App\Services\GeminiAiService();
            $aiResult = $service->analisaKredit([
                'nama_debitur'     => $kredit['nama_debitur'] ?? '',
                'nama_usaha'       => $kredit['nama_usaha'] ?? '',
                'lama_usaha'       => $kredit['lama_usaha'] ?? '',
                'plafon_pengajuan' => $plafon,
                'tenor_bulan'      => (int)($kredit['tenor_bulan'] ?? 12),
                'skema_pembayaran' => $kredit['skema_pembayaran'] ?? 'Installment',
                'bunga_per_bulan'  => (float)($kredit['bunga_per_bulan'] ?? 2),
                'total_mv'         => $totalMV,
                'total_lv'         => $totalLV,
                'coverage_mv'      => $covMV,
                'coverage_lv'      => $covLV,
                'omzet'            => $omzet,
                'laba_bersih'      => $labaBersih,
                'angsuran'         => $angsuran,
                'rasio_dsc'        => $dsc,
                'ringkasan_slik'   => $kredit['ringkasan_slik'] ?? ''
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
}
