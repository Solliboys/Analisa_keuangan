<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\KreditUsahaModel;
use App\Models\AgunanModel;
use App\Models\KreditDokumenModel;
use App\Models\SlikModel;
use App\Models\KreditWebcheckModel;
use App\Models\KreditAnalisaModel;
use App\Models\KreditRekeningModel;
use App\Models\KreditKeuanganDscModel;
use App\Models\KreditDisbursementModel;
use App\Models\KomiteModel;
use App\Services\GeminiAiService;

class Pengajuan extends BaseController
{
    protected $kreditModel;
    protected $usahaModel;
    protected $agunanModel;
    protected $dokumenModel;
    protected $slikModel;
    protected $webcheckModel;
    protected $analisaModel;
    protected $rekeningModel;
    protected $keuanganModel;
    protected $disbursementModel;
    protected $komiteModel;

    public function __construct()
    {
        $this->kreditModel       = new KreditModel();
        $this->usahaModel        = new KreditUsahaModel();
        $this->agunanModel       = new AgunanModel();
        $this->dokumenModel      = new KreditDokumenModel();
        $this->slikModel         = new SlikModel();
        $this->webcheckModel     = new KreditWebcheckModel();
        $this->analisaModel      = new KreditAnalisaModel();
        $this->rekeningModel     = new KreditRekeningModel();
        $this->keuanganModel     = new KreditKeuanganDscModel();
        $this->disbursementModel = new KreditDisbursementModel();
        $this->komiteModel       = new KomiteModel();
    }

    private function cekAuth()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $userId  = session()->get('user_id');
        $jabatan = strtolower(session()->get('jabatan') ?? '');

        // Query daftar pengajuan berdasarkan peran
        if ($jabatan === 'superadmin') {
            $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
        } elseif (strpos($jabatan, 'review') !== false || strpos($jabatan, 'ceo') !== false || strpos($jabatan, 'direk') !== false) {
            // Reviewer & CEO: Draft JANGAN dimasukkan, hanya yang sudah Diajukan / Disetujui / Ditolak
            $daftarKredit = $this->kreditModel->where('status_pengajuan !=', 'Draft')
                                              ->where('status_pengajuan !=', 'draft')
                                              ->orderBy('created_at', 'DESC')->findAll();
        } elseif (strpos($jabatan, 'slik') !== false) {
            $pengaturan = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
            $ijinSlik = (int)($pengaturan['ijin_pengusul_isi_slik'] ?? 1);
            if ($ijinSlik === 0) {
                // Jika fitur upload sendiri ditutup: masuk ketika pencet tombol setuju di slik (status_slik = Menunggu Pengecekan / Terbit) atau status diajukan
                $daftarKredit = $this->kreditModel->groupStart()
                                                  ->whereIn('status_slik', ['Menunggu Pengecekan', 'Terbit'])
                                                  ->orWhere('status_pengajuan !=', 'Draft')
                                                  ->groupEnd()
                                                  ->orderBy('created_at', 'DESC')->findAll();
            } else {
                $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
            }
        } else {
            // Pengusul hanya melihat pengajuan miliknya
            $daftarKredit = $this->kreditModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
        }

        $semuaAnalisa = $this->analisaModel->findAll();
        $mapAnalisa   = [];
        foreach ($semuaAnalisa as $a) {
            $mapAnalisa[$a['kredit_id']] = $a;
        }

        $semuaKomite = $this->komiteModel->orderBy('komite_id', 'DESC')->findAll();
        $mapKomite   = [];
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

        foreach ($daftarKredit as &$k) {
            $km = $mapKomite[$k['kredit_id']] ?? null;
            $k['catatan_reviewer'] = !empty($km['catatan_approval']) ? $km['catatan_approval'] : '';
            $k['status_komite']    = !empty($km['status_approval']) ? $km['status_approval'] : '';
            $k['nama_reviewer']    = !empty($km['nama_approver']) ? $km['nama_approver'] : '';
            $k['tanggal_review']   = !empty($km['tanggal_approval']) ? $km['tanggal_approval'] : (!empty($km['created_at']) ? date('Y-m-d', strtotime($km['created_at'])) : '');

            $hasOfficialReview = !empty($k['status_komite']) && (!empty($k['nama_reviewer']) || !empty($k['catatan_reviewer']));
            if ($hasOfficialReview && (empty($k['status_pengajuan']) || in_array(strtolower($k['status_pengajuan']), ['draft', 'diajukan']))) {
                $k['status_pengajuan'] = $k['status_komite'];
            }

            $an = $mapAnalisa[$k['kredit_id']] ?? null;
            $k['skor_ai']              = !empty($an['skor_kelayakan_ai']) ? (int)$an['skor_kelayakan_ai'] : null;
            $k['rekomendasi_ai']       = !empty($an['rekomendasi_keputusan_ai']) ? $an['rekomendasi_keputusan_ai'] : '';
            $k['rekomendasi_plafon_ai'] = !empty($an['rekomendasi_plafon_ai']) ? (float)$an['rekomendasi_plafon_ai'] : null;
            $k['analisis_risiko_ai']   = !empty($an['analisis_risiko_ai']) ? $an['analisis_risiko_ai'] : '';
        }
        unset($k);

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => ''
        ];

        $data = [
            'title'             => 'Daftar Pengajuan Kredit | ' . ($pengaturanSistem['nama_aplikasi'] ?? 'Credit Engine'),
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'daftar_kredit'     => $daftarKredit,
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('pengajuan/index', $data);
    }

    /**
     * Download Ekspor Data Pengajuan Kredit ke CSV/Excel
     */
    public function download()
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $userId  = session()->get('user_id');
        $jabatan = strtolower(session()->get('jabatan') ?? '');

        if ($jabatan === 'superadmin') {
            $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
        } elseif (strpos($jabatan, 'review') !== false || strpos($jabatan, 'ceo') !== false || strpos($jabatan, 'direk') !== false) {
            $daftarKredit = $this->kreditModel->where('status_pengajuan !=', 'Draft')
                                              ->where('status_pengajuan !=', 'draft')
                                              ->orderBy('created_at', 'DESC')->findAll();
        } elseif (strpos($jabatan, 'slik') !== false) {
            $pengaturan = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
            $ijinSlik = (int)($pengaturan['ijin_pengusul_isi_slik'] ?? 1);
            if ($ijinSlik === 0) {
                $daftarKredit = $this->kreditModel->groupStart()
                                                  ->whereIn('status_slik', ['Menunggu Pengecekan', 'Terbit'])
                                                  ->orWhere('status_pengajuan !=', 'Draft')
                                                  ->groupEnd()
                                                  ->orderBy('created_at', 'DESC')->findAll();
            } else {
                $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
            }
        } else {
            $daftarKredit = $this->kreditModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
        }

        $filename = 'Daftar_Pengajuan_Kredit_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'No',
            'No. Aplikasi',
            'Tipe Aplikasi',
            'Nama Debitur',
            'Nama Usaha',
            'Jenis Debitur',
            'Plafon Pinjaman (Rp)',
            'Tenor (Bulan)',
            'Skema Pembayaran',
            'Bunga per Bulan (%)',
            'Jenis Jaminan',
            'Status Pengajuan',
            'Tanggal Buat'
        ], ';');

        $no = 1;
        foreach ($daftarKredit as $row) {
            fputcsv($output, [
                $no++,
                $row['nomor_aplikasi'] ?? '-',
                $row['tipe_aplikasi'] ?? 'Fasilitas Baru',
                $row['nama_debitur'] ?? '-',
                $row['nama_usaha'] ?? '-',
                $row['jenis_debitur'] ?? 'Individu',
                (float)($row['plafon_pengajuan'] ?? 0),
                (int)($row['tenor_bulan'] ?? 12),
                $row['skema_pembayaran'] ?? 'Installment',
                (float)($row['bunga_per_bulan'] ?? 0),
                $row['jenis_jaminan'] ?? 'On Hand',
                $row['status_pengajuan'] ?? 'Draft',
                $row['created_at'] ?? '-'
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function baru()
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $nomorAplikasi = 'CAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Form Pengajuan & Analisa Kredit',
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'kredit_id'         => null,
            'nomor_aplikasi'    => $nomorAplikasi,
            'kredit'            => [],
            'agunan'            => [],
            'daftar_usaha'      => [],
            'slik'              => [],
            'webcheck'          => [],
            'analisa'           => [],
            'rekening'          => [],
            'keuangan'          => [],
            'disbursement'      => [],
            'dokumen'           => [],
            'komite'            => [],
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('pengajuan/form', $data);
    }

    public function edit($id = null)
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $kredit = $this->kreditModel->find($id);
        if (!$kredit) {
            return redirect()->to('/pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $userId  = session()->get('user_id');
        $jabatan = strtolower(session()->get('jabatan') ?? '');
        $isGlobal = ($jabatan === 'superadmin' || 
                     strpos($jabatan, 'ceo') !== false || 
                     strpos($jabatan, 'direk') !== false || 
                     strpos($jabatan, 'review') !== false || 
                     strpos($jabatan, 'slik') !== false);

        // Pengusul hanya dapat mengakses pengajuan miliknya sendiri
        if (!$isGlobal && $kredit['user_id'] != $userId) {
            return redirect()->to('/pengajuan')->with('error', 'Akses ditolak: Anda hanya dapat mengakses dan mengedit pengajuan yang Anda buat sendiri.');
        }

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Edit Analisa Kredit - ' . $kredit['nomor_aplikasi'],
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'kredit_id'         => $id,
            'nomor_aplikasi'    => $kredit['nomor_aplikasi'],
            'kredit'            => $kredit,
            'agunan'            => $this->agunanModel->where('kredit_id', $id)->findAll(),
            'daftar_usaha'      => $this->usahaModel->where('kredit_id', $id)->orderBy('id', 'ASC')->findAll(),
            'slik'              => $this->slikModel->where('kredit_id', $id)->findAll(),
            'webcheck'          => $this->webcheckModel->where('kredit_id', $id)->first() ?? [],
            'analisa'           => $this->analisaModel->where('kredit_id', $id)->first() ?? [],
            'rekening'          => $this->rekeningModel->where('kredit_id', $id)->findAll(),
            'keuangan'          => $this->keuanganModel->where('kredit_id', $id)->first() ?? [],
            'disbursement'      => $this->disbursementModel->where('kredit_id', $id)->first() ?? [],
            'dokumen'           => $this->dokumenModel->where('kredit_id', $id)->findAll(),
            'komite'            => $this->komiteModel->where('kredit_id', $id)->findAll(),
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('pengajuan/form', $data);
    }

    public function simpan()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir, silakan login ulang']);
        }

        $input    = $this->request->getPost();
        $userId   = session()->get('user_id');
        $jabatan  = strtolower(session()->get('jabatan') ?? '');
        $isReviewer = ($jabatan === 'superadmin' || strpos($jabatan, 'review') !== false || strpos($jabatan, 'direk') !== false || strpos($jabatan, 'ceo') !== false);
        $kreditId = !empty($input['kredit_id']) ? $input['kredit_id'] : null;

        // Tentukan status pengajuan yang sah
        $statusPengajuan = 'Draft';
        if ($kreditId) {
            $existing = $this->kreditModel->find($kreditId);
            if (!$existing) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan tidak ditemukan']);
            }
            if (!$isReviewer && $existing['user_id'] != $userId) {
                return $this->response->setJSON(['status' => false, 'message' => 'Akses ditolak: Anda hanya dapat memperbarui pengajuan milik Anda sendiri']);
            }
            $userIdPembuat = $existing['user_id'];

            // Jika sebelumnya sudah ada keputusan reviewer, pertahankan status reviewer
            if (!$isReviewer && in_array(strtolower($existing['status_pengajuan']), ['disetujui', 'disetujui bersyarat', 'ditolak'])) {
                $statusPengajuan = $existing['status_pengajuan'];
            } else {
                $reqStatus = $input['status_pengajuan'] ?? $existing['status_pengajuan'] ?? 'Draft';
                if (!$isReviewer && !in_array(strtolower($reqStatus), ['draft', 'diajukan'])) {
                    $reqStatus = 'Draft';
                }
                $statusPengajuan = $reqStatus;
            }
        } else {
            $userIdPembuat = $userId;
            $reqStatus = $input['status_pengajuan'] ?? 'Draft';
            if (!$isReviewer && !in_array(strtolower($reqStatus), ['draft', 'diajukan'])) {
                $reqStatus = 'Draft';
            }
            $statusPengajuan = $reqStatus;
        }

        // Ambil data usaha pertama jika dikirim sebagai array dinamis usaha
        $firstUsaha = (!empty($input['usaha']) && is_array($input['usaha'])) ? ($input['usaha'][0] ?? []) : [];
        $valNamaUsaha = !empty($firstUsaha['nama_usaha']) ? $firstUsaha['nama_usaha'] : ($input['nama_usaha'] ?? '');
        $valBidangUsaha = !empty($firstUsaha['bidang_usaha']) ? $firstUsaha['bidang_usaha'] : ($input['bidang_usaha'] ?? ($input['jenis_usaha'] ?? ''));
        $valJabatan = !empty($firstUsaha['jabatan']) ? $firstUsaha['jabatan'] : ($input['jabatan'] ?? '');
        $valLamaUsaha = !empty($firstUsaha['lama_usaha']) ? $firstUsaha['lama_usaha'] : ($input['lama_usaha'] ?? '');
        $valAlamatUsaha = !empty($firstUsaha['alamat_usaha']) ? $firstUsaha['alamat_usaha'] : ($input['alamat_usaha'] ?? '');
        $valNpwpUsaha = !empty($firstUsaha['npwp_usaha']) ? $firstUsaha['npwp_usaha'] : ($input['npwp_usaha'] ?? '');
        $valNib = !empty($firstUsaha['nomor_induk_berusaha_nib']) ? $firstUsaha['nomor_induk_berusaha_nib'] : ($input['nomor_induk_berusaha_nib'] ?? '');
        $valSumberBayar = !empty($firstUsaha['sumber_pembayaran']) ? $firstUsaha['sumber_pembayaran'] : ($input['sumber_pembayaran'] ?? '');

        $dataKredit = [
            'user_id'                 => $userIdPembuat,
            'nomor_aplikasi'          => $input['nomor_aplikasi'] ?? ('CAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'tipe_aplikasi'           => $input['tipe_aplikasi'] ?? 'Fasilitas Baru',
            'jenis_debitur'           => $input['jenis_debitur'] ?? 'Individu',
            'nama_debitur'            => $input['nama_debitur'] ?? '',
            'plafon_pengajuan'        => $this->cleanNumber($input['plafon_pengajuan'] ?? 0),
            'tenor_bulan'             => (int)($input['tenor_bulan'] ?? 12),
            'jenis_jaminan'           => $input['jenis_jaminan'] ?? 'On Hand',
            'tujuan_pembiayaan'       => $input['tujuan_pembiayaan'] ?? 'Modal Kerja',
            'skema_pembayaran'        => $input['skema_pembayaran'] ?? 'Balloon Payment',
            'perhitungan_bunga'       => $input['perhitungan_bunga'] ?? 'ANUITAS',
            'bunga_per_bulan'         => (float)($input['bunga_per_bulan'] ?? 3),
            'bunga_per_tahun'         => (float)($input['bunga_per_tahun'] ?? 36),
            'alamat_ktp'              => $input['alamat_ktp'] ?? '',
            'alamat_domisili'         => $input['alamat_domisili'] ?? '',
            'kepemilikan_domisili'    => $input['kepemilikan_domisili'] ?? 'Milik Sendiri',
            'jenis_usaha'             => $valBidangUsaha,
            'bidang_usaha'            => $valBidangUsaha,
            'nomor_hp'                => $input['nomor_hp'] ?? '',
            'nomor_emergency'         => $input['nomor_emergency'] ?? '',
            'nama_kontak_darurat'     => $input['nama_kontak_darurat'] ?? ($input['nama_ibu_kandung'] ?? ''),
            'npwp_pribadi'            => $input['npwp_pribadi'] ?? '',
            'nama_ibu_kandung'        => $input['nama_kontak_darurat'] ?? ($input['nama_ibu_kandung'] ?? ''),
            'tempat_tanggal_lahir'    => (!empty($input['tempat_lahir']) && !empty($input['tanggal_lahir']))
                                            ? (trim($input['tempat_lahir']) . ', ' . trim($input['tanggal_lahir']))
                                            : (!empty($input['tempat_lahir']) ? trim($input['tempat_lahir']) : ($input['tempat_tanggal_lahir'] ?? '')),
            'tempat_lahir'            => $input['tempat_lahir'] ?? '',
            'tanggal_lahir'           => !empty($input['tanggal_lahir']) ? $input['tanggal_lahir'] : null,
            'akta_pendirian'          => $input['akta_pendirian'] ?? '',
            'akta_perubahan'          => $input['akta_perubahan'] ?? '',
            'sk_pendirian'            => $input['sk_pendirian'] ?? '',
            'sk_perubahan'            => $input['sk_perubahan'] ?? '',
            'nama_usaha'              => $valNamaUsaha,
            'jabatan'                 => $valJabatan,
            'lama_usaha'              => $valLamaUsaha,
            'alamat_usaha'            => $valAlamatUsaha,
            'npwp_usaha'              => $valNpwpUsaha,
            'nomor_induk_berusaha_nib'=> $valNib,
            'sumber_pembayaran'       => $valSumberBayar,
            'ringkasan_slik'          => $input['ringkasan_slik'] ?? '',
            'status_pengajuan'        => $statusPengajuan
        ];

        if (!empty($input['status_slik'])) {
            $dataKredit['status_slik'] = $input['status_slik'];
            if ($input['status_slik'] === 'Menunggu Pengecekan' && empty($dataKredit['hasil_slik'])) {
                $dataKredit['hasil_slik'] = 'Menunggu Pengecekan Petugas SLIK';
            }
        }

        if ($kreditId) {
            $this->kreditModel->update($kreditId, $dataKredit);
        } else {
            $kreditId = $this->kreditModel->insert($dataKredit);
        }

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data induk kredit']);
        }

        // 0. Simpan Daftar Usaha (Multi Usaha Dinamis)
        $this->simpanUsaha($kreditId, $input);

        // 1. Simpan Agunan
        $this->simpanAgunan($kreditId, $input);

        // 2. Simpan SLIK
        $this->simpanSlik($kreditId, $input);

        // 3. Simpan Web Check
        $this->simpanWebcheck($kreditId, $input);

        // 4. Simpan Analisa (Tab 8)
        $this->simpanAnalisa($kreditId, $input);

        // 5. Simpan Rekening Koran (Tab 9)
        $this->simpanRekening($kreditId, $input);

        // 6. Simpan Laba Rugi & Repayment DSC (Tab 10 & 11)
        $this->simpanKeuangan($kreditId, $input);

        // 7. Simpan Disbursement (Tab 12)
        $this->simpanDisbursement($kreditId, $input);

        // 8. Simpan Approval Komite (HANYA boleh disimpan oleh Reviewer / Komite / Superadmin, BUKAN oleh pengusul)
        if ($isReviewer) {
            $this->simpanKomite($kreditId, $input);
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Data berhasil disimpan',
            'kredit_id' => $kreditId
        ]);
    }

    private function simpanUsaha($kreditId, $input)
    {
        $this->usahaModel->where('kredit_id', $kreditId)->delete();

        $daftar = $input['usaha'] ?? [];
        if (!empty($daftar) && is_array($daftar)) {
            $isFirst = true;
            foreach ($daftar as $row) {
                if (empty($row['nama_usaha']) && empty($row['bidang_usaha']) && empty($row['alamat_usaha'])) {
                    continue;
                }
                $this->usahaModel->insert([
                    'kredit_id'                => $kreditId,
                    'is_utama'                 => $isFirst ? 1 : 0,
                    'nama_usaha'               => $row['nama_usaha'] ?? '',
                    'bidang_usaha'             => $row['bidang_usaha'] ?? '',
                    'jabatan'                  => $row['jabatan'] ?? '',
                    'lama_usaha'               => $row['lama_usaha'] ?? '',
                    'sumber_pembayaran'        => $row['sumber_pembayaran'] ?? '',
                    'alamat_usaha'             => $row['alamat_usaha'] ?? '',
                    'npwp_usaha'               => $row['npwp_usaha'] ?? '',
                    'nomor_induk_berusaha_nib' => $row['nomor_induk_berusaha_nib'] ?? '',
                ]);
                $isFirst = false;
            }
        } elseif (!empty($input['nama_usaha']) || !empty($input['bidang_usaha'])) {
            $this->usahaModel->insert([
                'kredit_id'                => $kreditId,
                'is_utama'                 => 1,
                'nama_usaha'               => $input['nama_usaha'] ?? '',
                'bidang_usaha'             => $input['bidang_usaha'] ?? ($input['jenis_usaha'] ?? ''),
                'jabatan'                  => $input['jabatan'] ?? '',
                'lama_usaha'               => $input['lama_usaha'] ?? '',
                'sumber_pembayaran'        => $input['sumber_pembayaran'] ?? '',
                'alamat_usaha'             => $input['alamat_usaha'] ?? '',
                'npwp_usaha'               => $input['npwp_usaha'] ?? '',
                'nomor_induk_berusaha_nib' => $input['nomor_induk_berusaha_nib'] ?? '',
            ]);
        }
    }

    private function simpanAgunan($kreditId, $input)
    {
        $this->agunanModel->where('kredit_id', $kreditId)->delete();
        if (isset($input['agunan']) && is_array($input['agunan'])) {
            foreach ($input['agunan'] as $row) {
                if (empty($row['nomor_sertifikat']) && empty($row['alamat_agunan'])) continue;
                $mv = $this->cleanNumber($row['nilai_pasar_mv'] ?? 0);
                $lvPct = (float)($row['persen_likuidasi'] ?? 60);
                $lv = $mv * ($lvPct / 100);
                $plafon = $this->cleanNumber($input['plafon_pengajuan'] ?? 0);

                $this->agunanModel->insert([
                    'kredit_id'           => $kreditId,
                    'alamat_agunan'       => $row['alamat_agunan'] ?? '',
                    'jenis_agunan'        => $row['jenis_agunan'] ?? 'SHM',
                    'nomor_sertifikat'    => $row['nomor_sertifikat'] ?? '',
                    'luas_tanah_m2'       => (int)($row['luas_tanah_m2'] ?? 0),
                    'luas_bangunan_m2'    => (int)($row['luas_bangunan_m2'] ?? 0),
                    'status_imb'          => $row['status_imb'] ?? 'Ada',
                    'pemilik_agunan'      => $row['pemilik_agunan'] ?? '',
                    'hubungan_kepemilikan'=> $row['hubungan_kepemilikan'] ?? 'Milik Sendiri',
                    'lama_kepemilikan'    => $row['lama_kepemilikan'] ?? '',
                    'nilai_pasar_mv'      => $mv,
                    'nilai_njop'          => $this->cleanNumber($row['nilai_njop'] ?? 0),
                    'persen_likuidasi'    => $lvPct,
                    'nilai_likuidasi_lv'  => $lv,
                    'coverage_mv'         => $plafon > 0 ? round($mv / $plafon, 2) : 0,
                    'coverage_lv'         => $plafon > 0 ? round($lv / $plafon, 2) : 0,
                ]);
            }
        }
    }

    private function simpanSlik($kreditId, $input)
    {
        $db = \Config\Database::connect();
        $pengaturan = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        $ijinPengusul = (int)($pengaturan['ijin_pengusul_isi_slik'] ?? 1);

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        $isPetugasSlik = ($jabatan === 'superadmin' || strpos($jabatan, 'slik') !== false);

        // Jika bukan Petugas SLIK dan izin pengisian dinonaktifkan, jangan ubah data SLIK yang sudah ada
        if (!$isPetugasSlik && $ijinPengusul === 0) {
            return;
        }

        // Jika bukan Petugas SLIK dan data resmi SLIK sudah diverifikasi (is_verified = 1), jangan ditimpa oleh pengusul
        $dataResmiAda = $this->slikModel->where('kredit_id', $kreditId)->where('is_verified', 1)->countAllResults();
        if (!$isPetugasSlik && $dataResmiAda > 0) {
            return;
        }

        // Hapus data SLIK lama
        if (!$isPetugasSlik) {
            // Pengusul hanya memperbarui data draf miliknya (is_verified = 0)
            $this->slikModel->where('kredit_id', $kreditId)->where('is_verified', 0)->delete();
        } else {
            $this->slikModel->where('kredit_id', $kreditId)->delete();
        }

        if (isset($input['slik']) && is_array($input['slik'])) {
            foreach ($input['slik'] as $row) {
                if (empty($row['bank_lembaga']) && empty($row['nama_debitur'])) continue;
                $this->slikModel->insert([
                    'kredit_id'    => $kreditId,
                    'nama_debitur' => $row['nama_debitur'] ?? '',
                    'plafon'       => $this->cleanNumber($row['plafon'] ?? 0),
                    'outstanding'  => $this->cleanNumber($row['outstanding'] ?? 0),
                    'kelancaran'   => $row['kelancaran'] ?? 'Lancar',
                    'bank_lembaga' => $row['bank_lembaga'] ?? '',
                    'keterangan'   => $row['keterangan'] ?? '',
                    'is_verified'  => $isPetugasSlik ? 1 : 0,
                    'diinput_oleh' => $isPetugasSlik ? 'petugas_slik' : 'pengusul'
                ]);
            }
        }
    }

    private function simpanWebcheck($kreditId, $input)
    {
        $dataWeb = [
            'kredit_id'            => $kreditId,
            'sipp_keterangan'      => $input['sipp_keterangan'] ?? '',
            'internet_keterangan'  => $input['internet_keterangan'] ?? '',
            'watchlist_keterangan' => $input['watchlist_keterangan'] ?? '',
            'lelang_keterangan'    => $input['lelang_keterangan'] ?? '',
            'negatif_keterangan'   => $input['negatif_keterangan'] ?? ''
        ];

        if (!empty($input['sipp_file'])) $dataWeb['sipp_file'] = $input['sipp_file'];
        if (!empty($input['internet_file'])) $dataWeb['internet_file'] = $input['internet_file'];
        if (!empty($input['watchlist_file'])) $dataWeb['watchlist_file'] = $input['watchlist_file'];
        if (!empty($input['lelang_file'])) $dataWeb['lelang_file'] = $input['lelang_file'];
        if (!empty($input['negatif_file'])) $dataWeb['negatif_file'] = $input['negatif_file'];

        $cek = $this->webcheckModel->where('kredit_id', $kreditId)->first();
        if ($cek) {
            $this->webcheckModel->update($cek['webcheck_id'], $dataWeb);
        } else {
            $this->webcheckModel->insert($dataWeb);
        }
    }

    private function simpanAnalisa($kreditId, $input)
    {
        $dataAnalisa = [
            'kredit_id'                => $kreditId,
            'analisa_jaminan'          => $input['analisa_jaminan'] ?? '',
            'business_review'          => $input['business_review'] ?? '',
            'business_catatan'         => $input['business_catatan'] ?? '',
            'detail_kebutuhan'         => $input['detail_kebutuhan'] ?? '',
            'skor_kelayakan_ai'        => !empty($input['skor_kelayakan_ai']) ? (int)$input['skor_kelayakan_ai'] : null,
            'rekomendasi_keputusan_ai' => $input['rekomendasi_keputusan_ai'] ?? null,
            'rekomendasi_plafon_ai'    => !empty($input['rekomendasi_plafon_ai']) ? $this->cleanNumber($input['rekomendasi_plafon_ai']) : null,
            'analisis_risiko_ai'       => $input['analisis_risiko_ai'] ?? null
        ];

        $cek = $this->analisaModel->where('kredit_id', $kreditId)->first();
        if ($cek) {
            $this->analisaModel->update($cek['analisa_id'], $dataAnalisa);
        } else {
            $this->analisaModel->insert($dataAnalisa);
        }
    }

    private function simpanRekening($kreditId, $input)
    {
        $this->rekeningModel->where('kredit_id', $kreditId)->delete();
        if (isset($input['rekening']) && is_array($input['rekening'])) {
            foreach ($input['rekening'] as $row) {
                if (empty($row['nama_bank'])) continue;
                $credit = $this->cleanNumber($row['total_mutasi_kredit'] ?? 0);
                $debit  = $this->cleanNumber($row['total_mutasi_debit'] ?? 0);
                $months = max(1, (int)($row['jumlah_bulan'] ?? 6));

                $this->rekeningModel->insert([
                    'kredit_id'           => $kreditId,
                    'nama_bank'           => $row['nama_bank'] ?? '',
                    'periode'             => $row['periode'] ?? '',
                    'nomor_rekening'      => $row['nomor_rekening'] ?? '',
                    'atas_nama'           => $row['atas_nama'] ?? '',
                    'total_mutasi_kredit' => $credit,
                    'total_mutasi_debit'  => $debit,
                    'jumlah_bulan'        => $months,
                    'rata_rata_kredit'    => round($credit / $months, 2),
                    'rata_rata_debit'     => round($debit / $months, 2)
                ]);
            }
        }
    }

    private function simpanKeuangan($kreditId, $input)
    {
        $pRK   = $this->cleanNumber($input['pendapatan_rk'] ?? 0);
        $hppPctRK = (float)($input['hpp_persen_rk'] ?? 60);
        $hppNomRK = $pRK * ($hppPctRK / 100);
        $grossRK  = $pRK - $hppNomRK;
        $bhPctRK  = (float)($input['biaya_hidup_persen_rk'] ?? 10);
        $bhNomRK  = $pRK * ($bhPctRK / 100);
        $blPctRK  = (float)($input['beban_lainnya_persen_rk'] ?? 10);
        $blNomRK  = $pRK * ($blPctRK / 100);
        $nettRK   = $grossRK - $bhNomRK - $blNomRK;

        $pIV   = $this->cleanNumber($input['pendapatan_iv'] ?? 0);
        $hppPctIV = (float)($input['hpp_persen_iv'] ?? 60);
        $hppNomIV = $pIV * ($hppPctIV / 100);
        $grossIV  = $pIV - $hppNomIV;
        $bhPctIV  = (float)($input['biaya_hidup_persen_iv'] ?? 10);
        $bhNomIV  = $pIV * ($bhPctIV / 100);
        $blPctIV  = (float)($input['beban_lainnya_persen_iv'] ?? 10);
        $blNomIV  = $pIV * ($blPctIV / 100);
        $nettIV   = $grossIV - $bhNomIV - $blNomIV;

        $angsuran = $this->cleanNumber($input['angsuran_dipilih'] ?? 0);
        $dscRK = $angsuran > 0 ? round($nettRK / $angsuran, 2) : 0;
        $dscIV = $angsuran > 0 ? round($nettIV / $angsuran, 2) : 0;

        $statusRK = $dscRK >= 1.3 ? 'Aman' : ($dscRK >= 1.0 ? 'Hati-hati' : 'Berisiko');
        $statusIV = $dscIV >= 1.3 ? 'Aman' : ($dscIV >= 1.0 ? 'Hati-hati' : 'Berisiko');

        $dataKeu = [
            'kredit_id'                 => $kreditId,
            'pendapatan_rk'             => $pRK,
            'hpp_persen_rk'             => $hppPctRK,
            'hpp_nominal_rk'            => $hppNomRK,
            'laba_kotor_rk'             => $grossRK,
            'biaya_hidup_persen_rk'     => $bhPctRK,
            'biaya_hidup_nominal_rk'     => $bhNomRK,
            'beban_lainnya_persen_rk'   => $blPctRK,
            'beban_lainnya_nominal_rk'  => $blNomRK,
            'laba_bersih_rk'            => $nettRK,
            'pendapatan_iv'             => $pIV,
            'hpp_persen_iv'             => $hppPctIV,
            'hpp_nominal_iv'            => $hppNomIV,
            'laba_kotor_iv'             => $grossIV,
            'biaya_hidup_persen_iv'     => $bhPctIV,
            'biaya_hidup_nominal_iv'    => $bhNomIV,
            'beban_lainnya_persen_iv'   => $blPctIV,
            'beban_lainnya_nominal_iv'  => $blNomIV,
            'laba_bersih_iv'            => $nettIV,
            'angsuran_balloon'          => $this->cleanNumber($input['angsuran_balloon'] ?? 0),
            'angsuran_installment'      => $this->cleanNumber($input['angsuran_installment'] ?? 0),
            'angsuran_dipilih'          => $angsuran,
            'rasio_dsc_rk'              => $dscRK,
            'rasio_dsc_iv'              => $dscIV,
            'status_dsc_rk'             => $statusRK,
            'status_dsc_iv'             => $statusIV
        ];

        $cek = $this->keuanganModel->where('kredit_id', $kreditId)->first();
        if ($cek) {
            $this->keuanganModel->update($cek['keuangan_id'], $dataKeu);
        } else {
            $this->keuanganModel->insert($dataKeu);
        }
    }

    private function simpanDisbursement($kreditId, $input)
    {
        $plafon     = $this->cleanNumber($input['plafon_pengajuan'] ?? 0);
        $provPct    = (float)($input['provisi_persen'] ?? 7);
        $provNom    = $plafon * ($provPct / 100);
        $agentPct   = (float)($input['agent_persen'] ?? 1);
        $agentNom   = $plafon * ($agentPct / 100);
        $adminPct   = (float)($input['admin_persen'] ?? 1);
        $adminNom   = $plafon * ($adminPct / 100);
        $holdBulan  = (int)($input['hold_angsuran_bulan'] ?? 4);
        $angsuran   = $this->cleanNumber($input['angsuran_dipilih'] ?? 0);
        $holdNom    = $angsuran * $holdBulan;
        $asJiwa     = $this->cleanNumber($input['asuransi_jiwa'] ?? 0);
        $asKeb      = $this->cleanNumber($input['asuransi_kebakaran'] ?? 0);
        $totalPot   = $provNom + $agentNom + $adminNom + $holdNom + $asJiwa + $asKeb;
        $netCair    = $plafon - $totalPot;

        $dataDisb = [
            'kredit_id'             => $kreditId,
            'provisi_persen'        => $provPct,
            'provisi_nominal'       => $provNom,
            'agent_persen'          => $agentPct,
            'agent_nominal'         => $agentNom,
            'admin_persen'          => $adminPct,
            'admin_nominal'         => $adminNom,
            'hold_angsuran_bulan'   => $holdBulan,
            'hold_angsuran_nominal' => $holdNom,
            'asuransi_jiwa'         => $asJiwa,
            'asuransi_kebakaran'    => $asKeb,
            'total_potongan'        => $totalPot,
            'dana_bersih_cair'      => $netCair
        ];

        $cek = $this->disbursementModel->where('kredit_id', $kreditId)->first();
        if ($cek) {
            $this->disbursementModel->update($cek['disbursement_id'], $dataDisb);
        } else {
            $this->disbursementModel->insert($dataDisb);
        }
    }

    private function simpanKomite($kreditId, $input)
    {
        $this->komiteModel->where('kredit_id', $kreditId)->delete();
        if (isset($input['komite']) && is_array($input['komite'])) {
            foreach ($input['komite'] as $row) {
                if (empty($row['role_approver'])) continue;
                $this->komiteModel->insert([
                    'kredit_id'        => $kreditId,
                    'user_id'          => session()->get('user_id'),
                    'role_approver'    => $row['role_approver'] ?? 'Komite 1',
                    'nama_approver'    => $row['nama_approver'] ?? '',
                    'status_approval'  => $row['status_approval'] ?? 'Pending',
                    'tanggal_approval' => !empty($row['tanggal_approval']) ? $row['tanggal_approval'] : date('Y-m-d'),
                    'catatan_approval' => $row['catatan_approval'] ?? ''
                ]);
            }
        }
    }

    public function uploadDokumen()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $kreditId = $this->request->getPost('kredit_id');
        $kategori = $this->request->getPost('kategori') ?? 'lainnya';
        $file     = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid']);
        }

        $folder = FCPATH . 'uploads/dokumen/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($folder, $newName);
        $destPath = $folder . $newName;

        // Server-side image optimization / compression jika ekstensi gambar
        $ext = strtolower(pathinfo($newName, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && function_exists('imagecreatefromstring')) {
            try {
                $imageInfo = @getimagesize($destPath);
                if ($imageInfo) {
                    $src = @imagecreatefromstring(file_get_contents($destPath));
                    if ($src) {
                        $width  = imagesx($src);
                        $height = imagesy($src);
                        $maxDim = 1600;
                        if ($width > $maxDim || $height > $maxDim) {
                            $ratio = min($maxDim / $width, $maxDim / $height);
                            $newW  = (int)round($width * $ratio);
                            $newH  = (int)round($height * $ratio);
                            $dst   = imagecreatetruecolor($newW, $newH);
                            if ($ext === 'png') {
                                imagealphablending($dst, false);
                                imagesavealpha($dst, true);
                            }
                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
                            if ($ext === 'png') {
                                imagepng($dst, $destPath, 7);
                            } else {
                                imagejpeg($dst, $destPath, 75);
                            }
                            imagedestroy($dst);
                        } else {
                            if ($ext === 'jpg' || $ext === 'jpeg') {
                                imagejpeg($src, $destPath, 75);
                            }
                        }
                        imagedestroy($src);
                    }
                }
            } catch (\Throwable $e) {
                // Biarkan file asli jika terjadi kendala pada GD
            }
        }

        $docId = $this->dokumenModel->insert([
            'kredit_id'        => $kreditId,
            'kategori_dokumen' => $kategori,
            'nama_file_asli'   => $file->getClientName(),
            'path_file'        => 'uploads/dokumen/' . $newName,
            'ukuran_file_bytes'=> file_exists($destPath) ? filesize($destPath) : $file->getSize(),
            'tipe_file'        => $file->getClientMimeType()
        ]);

        return $this->response->setJSON([
            'status'     => true,
            'dokumen_id' => $docId,
            'nama_file'  => $file->getClientName(),
            'kategori'   => $kategori,
            'ukuran_kb'  => number_format($file->getSize() / 1024, 1),
            'path'       => base_url('uploads/dokumen/' . $newName)
        ]);
    }

    public function updateKategoriDokumen()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $docId    = (int)$this->request->getPost('dokumen_id');
        $kategori = trim($this->request->getPost('kategori') ?? '');
        if (empty($kategori)) {
            $kategori = 'KTP & NPWP';
        }

        $doc = $this->dokumenModel->find($docId);
        if (!$doc) {
            return $this->response->setJSON(['status' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        $this->dokumenModel->update($docId, [
            'kategori_dokumen' => $kategori
        ]);

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Kategori berhasil diperbarui',
            'kategori' => $kategori
        ]);
    }

    public function hapus($id = null)
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $userRole = strtolower(trim(session()->get('jabatan') ?? ''));
        $canDelete = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false || $userRole === 'superadmin' || $userRole === 'admin');

        if (!$canDelete) {
            return redirect()->to('/pengajuan')->with('error', 'Akses ditolak: Hanya CEO / Direksi yang berhak menghapus data pengajuan kredit.');
        }

        $this->kreditModel->delete($id);
        return redirect()->to('/pengajuan')->with('success', 'Data pengajuan berhasil dihapus.');
    }

    public function cetak($id = null)
    {
        if ($redirect = $this->cekAuth()) return $redirect;

        $kredit = $this->kreditModel->find($id);
        if (!$kredit) {
            return redirect()->to('/pengajuan')->with('error', 'Data tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [
            'nama_aplikasi' => 'OTW Credit Engine & CAM',
            'nama_lembaga'  => 'PT BPR / Lembaga Pembiayaan',
            'logo_aplikasi' => ''
        ];

        $data = [
            'title'             => 'Credit Application & Memo (CAM) - ' . $kredit['nomor_aplikasi'],
            'kredit'            => $kredit,
            'agunan'            => $this->agunanModel->where('kredit_id', $id)->findAll(),
            'slik'              => $this->slikModel->where('kredit_id', $id)->findAll(),
            'webcheck'          => $this->webcheckModel->where('kredit_id', $id)->first() ?? [],
            'analisa'           => $this->analisaModel->where('kredit_id', $id)->first() ?? [],
            'rekening'          => $this->rekeningModel->where('kredit_id', $id)->findAll(),
            'keuangan'          => $this->keuanganModel->where('kredit_id', $id)->first() ?? [],
            'disbursement'      => $this->disbursementModel->where('kredit_id', $id)->first() ?? [],
            'komite'            => $this->komiteModel->where('kredit_id', $id)->findAll(),
            'pengaturan_sistem' => $pengaturanSistem
        ];

        return view('pengajuan/cetak', $data);
    }

    public function aiWebCheck()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $namaDebitur = $this->request->getPost('nama_debitur') ?? '';
        $namaUsaha   = $this->request->getPost('nama_usaha') ?? '';
        $kota        = $this->request->getPost('kota') ?? '';
        $bidangUsaha = $this->request->getPost('bidang_usaha') ?? '';

        $service = new GeminiAiService();
        $hasil = $service->investigasiWebcheck($namaDebitur, $namaUsaha, $kota, $bidangUsaha);

        return $this->response->setJSON($hasil);
    }

    public function cekPerkaraMa()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $namaDebitur = trim($this->request->getPost('nama_debitur') ?? '');
        $kota        = trim($this->request->getPost('kota') ?? '');
        $namaUsaha   = trim($this->request->getPost('nama_usaha') ?? '');

        if (empty($namaDebitur)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Nama debitur wajib diisi untuk melakukan penelusuran perkara.'
            ]);
        }

        $service = new GeminiAiService();
        $hasil = $service->cekPerkaraPengadilan($namaDebitur, $kota, $namaUsaha);

        return $this->response->setJSON($hasil);
    }

    public function analisaAi()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getPost();
        
        $totalMV = 0;
        $totalLV = 0;
        if (isset($input['agunan']) && is_array($input['agunan'])) {
            foreach ($input['agunan'] as $row) {
                $mv = $this->cleanNumber($row['nilai_pasar_mv'] ?? 0);
                $lvPct = (float)($row['persen_likuidasi'] ?? 60);
                $totalMV += $mv;
                $totalLV += $mv * ($lvPct / 100);
            }
        }

        $plafon = $this->cleanNumber($input['plafon_pengajuan'] ?? 0);
        $covMV = $plafon > 0 ? round(($totalMV / $plafon) * 100, 1) : 0;
        $covLV = $plafon > 0 ? round(($totalLV / $plafon) * 100, 1) : 0;

        $omzet = $this->cleanNumber($input['pendapatan_rk'] ?? ($input['pendapatan_iv'] ?? 0));
        $angsuran = $this->cleanNumber($input['angsuran_dipilih'] ?? 0);
        $hppPct = (float)($input['hpp_persen_rk'] ?? 60);
        $bhPct  = (float)($input['biaya_hidup_persen_rk'] ?? 10);
        $blPct  = (float)($input['beban_lainnya_persen_rk'] ?? 10);
        $labaBersih = $omzet - ($omzet * $hppPct/100) - ($omzet * $bhPct/100) - ($omzet * $blPct/100);
        $dsc = $angsuran > 0 ? round($labaBersih / $angsuran, 2) : 0;

        $service = new GeminiAiService();
        $hasil = $service->analisaKredit([
            'nama_debitur'     => $input['nama_debitur'] ?? '',
            'nama_usaha'       => $input['nama_usaha'] ?? '',
            'lama_usaha'       => $input['lama_usaha'] ?? '',
            'plafon_pengajuan' => $plafon,
            'tenor_bulan'      => (int)($input['tenor_bulan'] ?? 12),
            'skema_pembayaran' => $input['skema_pembayaran'] ?? 'Balloon Payment',
            'bunga_per_bulan'  => (float)($input['bunga_per_bulan'] ?? 3),
            'total_mv'         => $totalMV,
            'total_lv'         => $totalLV,
            'coverage_mv'      => $covMV,
            'coverage_lv'      => $covLV,
            'omzet'            => $omzet,
            'laba_bersih'      => $labaBersih,
            'angsuran'         => $angsuran,
            'rasio_dsc'        => $dsc,
            'ringkasan_slik'   => $input['ringkasan_slik'] ?? ''
        ]);

        return $this->response->setJSON($hasil);
    }

    private function cleanNumber($val)
    {
        if (is_numeric($val)) return (float)$val;
        return (float)preg_replace('/[^0-9]/', '', (string)$val);
    }
}
