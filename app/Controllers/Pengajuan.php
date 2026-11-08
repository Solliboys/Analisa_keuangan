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
use App\Models\LogAktivitasModel;
use App\Models\SettingReferensiModel;
use App\Services\GeminiAiService;
use App\Models\KreditLaporanKeuanganModel;
use App\Services\LaporanKeuanganService;

class Pengajuan extends BaseController
{
    protected $db;
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
    protected $referensiModel;

    public function __construct()
    {
        $this->db                = \Config\Database::connect();
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
        $this->referensiModel     = new SettingReferensiModel();
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

        $kreditIds = !empty($daftarKredit) ? array_column($daftarKredit, 'kredit_id') : [];
        $mapAnalisa = [];
        $mapKomite  = [];

        if (!empty($kreditIds)) {
            $semuaAnalisa = $this->analisaModel->whereIn('kredit_id', $kreditIds)->findAll();
            foreach ($semuaAnalisa as $a) {
                $mapAnalisa[$a['kredit_id']] = $a;
            }

            $semuaKomite = $this->komiteModel->whereIn('kredit_id', $kreditIds)->orderBy('komite_id', 'DESC')->findAll();
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
            'referensi'         => $this->referensiModel->where('is_active', 1)->orderBy('urutan', 'ASC')->findAll(),
            'pengaturan_sistem' => $pengaturanSistem,
            'laporan_keuangan'  => [],
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

        $jenisDebitur = $this->request->getGet('tipe') ?? 'perorangan';
        $viewFolder = (stripos($jenisDebitur, 'badan') !== false) ? 'badan_usaha' : 'perorangan';

        $data = [
            'title'             => 'Form Pengajuan & Analisa Kredit (' . (($viewFolder === 'badan_usaha') ? 'Badan Usaha' : 'Perorangan') . ')',
            'view_folder'       => $viewFolder,
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'kredit_id'         => null,
            'nomor_aplikasi'    => $nomorAplikasi,
            'kredit'            => [],
            'agunan'            => [],
            'daftar_usaha'      => [],
            'daftar_karyawan'   => [],
            'slik'              => [],
            'webcheck'          => [],
            'analisa'           => [],
            'rekening'          => [],
            'keuangan'          => [],
            'disbursement'      => [],
            'dokumen'           => [],
            'komite'            => [],
            'referensi'         => $this->referensiModel->where('is_active', 1)->orderBy('urutan', 'ASC')->findAll(),
            'pengaturan_sistem' => $pengaturanSistem,
            'laporan_keuangan'  => [],
        ];

        return view('pengajuan/' . $viewFolder . '/form', $data);
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

        $rawJd = $kredit['jenis_debitur'] ?? 'Perorangan';
        $viewFolder = (stripos($rawJd, 'badan') !== false || stripos($rawJd, 'pt') !== false || stripos($rawJd, 'cv') !== false) ? 'badan_usaha' : 'perorangan';

        $keuangan = $this->keuanganModel->where('kredit_id', $id)->first() ?? [];
        $laporanKeuangan = (new KreditLaporanKeuanganModel())->where('kredit_id', $id)->first() ?? [];

        // Jika belum ada laporan tersimpan tetapi sudah ada data keuangan di tahap 9, hitung otomatis
        if (empty($laporanKeuangan['json_laporan']) && !empty($keuangan)) {
            $service = new LaporanKeuanganService();
            $calcInput = array_merge($keuangan, ['daftar_karyawan' => $kredit['daftar_karyawan'] ?? '[]']);
            $hasil = $service->hitungLaporan($calcInput, $viewFolder);
            $laporanKeuangan = [
                'kredit_id'    => $id,
                'tipe_pemohon' => $viewFolder,
                'json_laporan' => json_encode($hasil),
                'generated_at' => date('Y-m-d H:i:s'),
                'status_neraca'=> $hasil['validasi']['status_neraca'] ?? 'Seimbang',
            ];
        }

        $data = [
            'title'             => 'Edit Analisa Kredit - ' . $kredit['nomor_aplikasi'] . ' (' . (($viewFolder === 'badan_usaha') ? 'Badan Usaha' : 'Perorangan') . ')',
            'view_folder'       => $viewFolder,
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'kredit_id'         => $id,
            'nomor_aplikasi'    => $kredit['nomor_aplikasi'],
            'kredit'            => $kredit,
            'agunan'            => $this->agunanModel->where('kredit_id', $id)->findAll(),
            'daftar_usaha'      => $this->usahaModel->where('kredit_id', $id)->orderBy('id', 'ASC')->findAll(),
            'daftar_karyawan'   => !empty($kredit['daftar_karyawan']) ? json_decode($kredit['daftar_karyawan'], true) : [],
            'slik'              => $this->slikModel->where('kredit_id', $id)->findAll(),
            'webcheck'          => $this->webcheckModel->where('kredit_id', $id)->first() ?? [],
            'analisa'           => $this->analisaModel->where('kredit_id', $id)->first() ?? [],
            'rekening'          => $this->rekeningModel->where('kredit_id', $id)->findAll(),
            'keuangan'          => $keuangan,
            'disbursement'      => $this->disbursementModel->where('kredit_id', $id)->first() ?? [],
            'dokumen'           => $this->dokumenModel->where('kredit_id', $id)->findAll(),
            'komite'            => $this->komiteModel->where('kredit_id', $id)->findAll(),
            'referensi'         => $this->referensiModel->where('is_active', 1)->orderBy('urutan', 'ASC')->findAll(),
            'pengaturan_sistem' => $pengaturanSistem,
            'laporan_keuangan'  => $laporanKeuangan,
        ];

        return view('pengajuan/' . $viewFolder . '/form', $data);
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
                if (!$isReviewer && !in_array(strtolower($reqStatus), ['draft', 'diajukan', 'revisi'])) {
                    $reqStatus = 'Draft';
                }
                $statusPengajuan = (strtolower($reqStatus) === 'revisi') ? 'Revisi' : $reqStatus;
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

        // Olah daftar karyawan untuk Badan Usaha jika dikirim
        $jsonKaryawan = null;
        if (!empty($input['karyawan']) && is_array($input['karyawan'])) {
            $jsonKaryawan = json_encode(array_values($input['karyawan']));
        }

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
            'daftar_karyawan'         => $jsonKaryawan ?? ($input['daftar_karyawan'] ?? null),
            'sumber_pembayaran'       => $valSumberBayar,
            'ringkasan_slik'          => $input['ringkasan_slik'] ?? '',
            'status_pengajuan'        => $statusPengajuan
        ];

        // Jika terdapat input karyawan (Badan Usaha), update data penanggung jawab dari karyawan pertama
        if (!empty($input['karyawan']) && is_array($input['karyawan'])) {
            $firstKaryawan = $input['karyawan'][0] ?? [];
            if (!empty($firstKaryawan['nama'])) {
                $dataKredit['nama_debitur'] = $firstKaryawan['nama'];
            }
            if (!empty($firstKaryawan['nik'])) {
                $dataKredit['npwp_pribadi'] = $firstKaryawan['nik'];
            }
            if (!empty($firstKaryawan['no_hp'])) {
                $dataKredit['nomor_hp'] = $firstKaryawan['no_hp'];
            }
            if (!empty($firstKaryawan['tempat_lahir'])) {
                $dataKredit['tempat_lahir'] = $firstKaryawan['tempat_lahir'];
            }
            if (!empty($firstKaryawan['tanggal_lahir'])) {
                $dataKredit['tanggal_lahir'] = $firstKaryawan['tanggal_lahir'];
            }
            if (!empty($firstKaryawan['alamat'])) {
                $dataKredit['alamat_ktp'] = $firstKaryawan['alamat'];
            }
            if (!empty($firstKaryawan['jabatan'])) {
                $dataKredit['jabatan'] = $firstKaryawan['jabatan'];
            }
        }

        if (!empty($input['status_slik'])) {
            $dataKredit['status_slik'] = $input['status_slik'];
            if ($input['status_slik'] === 'Menunggu Pengecekan' && empty($dataKredit['hasil_slik'])) {
                $dataKredit['hasil_slik'] = 'Menunggu Pengecekan Petugas SLIK';
            }
        }
        if (!empty($input['hasil_slik'])) {
            $dataKredit['hasil_slik'] = $input['hasil_slik'];
        }
        if (isset($input['berkas_slik_pdf'])) {
            $dataKredit['berkas_slik_pdf'] = $input['berkas_slik_pdf'];
        }
        if (isset($input['berkas_slik_txt'])) {
            $dataKredit['berkas_slik_txt'] = $input['berkas_slik_txt'];
        }
        if (!empty($input['slik_permohonan_nik'])) {
            $dataKredit['slik_permohonan_nik'] = $input['slik_permohonan_nik'];
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

        // Catat ke log aktivitas sistem
        try {
            $nomorApp = $nomorAplikasi ?? ('ID #' . $kreditId);
            $namaDeb  = $input['nama_debitur'] ?? '-';
            if ($statusPengajuan === 'Diajukan') {
                LogAktivitasModel::catat(
                    'Mengajukan berkas pengajuan kredit ' . $nomorApp . ' (' . $namaDeb . ') ke Komite Kredit',
                    'PENGAJUAN',
                    'SUCCESS',
                    [
                        'kredit_id'       => $kreditId,
                        'nomor_aplikasi'  => $nomorApp,
                        'nama_debitur'    => $namaDeb,
                        'status'          => 'Diajukan',
                        'plafon'          => $input['plafon_pengajuan'] ?? 0
                    ]
                );
            } else {
                LogAktivitasModel::catat(
                    'Menyimpan draf / pembaruan berkas pengajuan kredit ' . $nomorApp . ' (' . $namaDeb . ')',
                    'PENGAJUAN',
                    'SUCCESS',
                    [
                        'kredit_id'       => $kreditId,
                        'nomor_aplikasi'  => $nomorApp,
                        'nama_debitur'    => $namaDeb,
                        'status'          => $statusPengajuan,
                        'plafon'          => $input['plafon_pengajuan'] ?? 0
                    ]
                );
            }
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'    => true,
            'message'   => ($statusPengajuan === 'Diajukan') ? 'Pengajuan berhasil diajukan ke Reviewer / Komite Kredit!' : 'Data berhasil disimpan',
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
        // Biaya hidup nominal langsung & persentase
        $bhNomRK = isset($input['biaya_hidup_nominal_rk']) ? $this->cleanNumber($input['biaya_hidup_nominal_rk']) : 0;
        if ($bhNomRK == 0 && !empty($input['biaya_hidup_persen_rk']) && $pRK > 0) {
            $bhPctRK = (float)$input['biaya_hidup_persen_rk'];
            $bhNomRK = $pRK * ($bhPctRK / 100);
        } else {
            $bhPctRK = $pRK > 0 ? round(($bhNomRK / $pRK) * 100, 2) : (float)($input['biaya_hidup_persen_rk'] ?? 0);
        }

        // Beban lain nominal langsung & rincian beban lainnya
        $blNomRK = isset($input['beban_lainnya_nominal_rk']) ? $this->cleanNumber($input['beban_lainnya_nominal_rk']) : 0;
        if ($blNomRK == 0 && !empty($input['beban_lainnya_persen_rk']) && $pRK > 0) {
            $blPctRK = (float)$input['beban_lainnya_persen_rk'];
            $blNomRK = $pRK * ($blPctRK / 100);
        } else {
            $blPctRK = $pRK > 0 ? round(($blNomRK / $pRK) * 100, 2) : (float)($input['beban_lainnya_persen_rk'] ?? 0);
        }
        $detailBebanRK = !empty($input['detail_beban_lainnya_rk']) ? $input['detail_beban_lainnya_rk'] : null;
        if ($detailBebanRK === '[]') $detailBebanRK = null;

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

        // Tambahan Pendapatan Lain-lain
        $adaPendapatanLain = !empty($input['ada_bukti_pendapatan_lain_rk']) ? 1 : 0;
        $pLainRK           = $adaPendapatanLain ? $this->cleanNumber($input['pendapatan_lain_rk'] ?? 0) : 0;
        $ketLainRK         = $adaPendapatanLain ? trim($input['keterangan_pendapatan_lain_rk'] ?? '') : null;
        
        // Cek file upload bukti pendapatan lain (JSON atau single path)
        $pathBukti = !empty($input['bukti_pendapatan_lain_rk']) ? $input['bukti_pendapatan_lain_rk'] : null;
        if ($pathBukti === '[]') $pathBukti = null;

        $totalNettRK = $nettRK + $pLainRK;
        $angsuran = $this->cleanNumber($input['angsuran_dipilih'] ?? 0);
        $dscRK = $angsuran > 0 ? round($totalNettRK / $angsuran, 2) : 0;
        $dscIV = $angsuran > 0 ? round($nettIV / $angsuran, 2) : 0;

        $statusRK = $dscRK >= 1.3 ? 'Aman' : ($dscRK >= 1.0 ? 'Hati-hati' : 'Berisiko');
        $statusIV = $dscIV >= 1.3 ? 'Aman' : ($dscIV >= 1.0 ? 'Hati-hati' : 'Berisiko');

        $dataKeu = [
            'kredit_id'                     => $kreditId,
            'pendapatan_rk'                 => $pRK,
            'pendapatan_lain_rk'            => $pLainRK,
            'keterangan_pendapatan_lain_rk' => $ketLainRK,
            'ada_bukti_pendapatan_lain_rk'  => $adaPendapatanLain,
            'bukti_pendapatan_lain_rk'      => $pathBukti,
            'hpp_persen_rk'                 => $hppPctRK,
            'hpp_nominal_rk'                => $hppNomRK,
            'laba_kotor_rk'                 => $grossRK,
            'biaya_hidup_persen_rk'         => $bhPctRK,
            'biaya_hidup_nominal_rk'        => $bhNomRK,
            'beban_lainnya_persen_rk'       => $blPctRK,
            'beban_lainnya_nominal_rk'      => $blNomRK,
            'detail_beban_lainnya_rk'       => $detailBebanRK,
            'laba_bersih_rk'                => $nettRK,
            'pendapatan_iv'                 => $pIV,
            'hpp_persen_iv'                 => $hppPctIV,
            'hpp_nominal_iv'                => $hppNomIV,
            'laba_kotor_iv'                 => $grossIV,
            'biaya_hidup_persen_iv'         => $bhPctIV,
            'biaya_hidup_nominal_iv'        => $bhNomIV,
            'beban_lainnya_persen_iv'       => $blPctIV,
            'beban_lainnya_nominal_iv'      => $blNomIV,
            'laba_bersih_iv'                => $nettIV,
            'angsuran_balloon'              => $this->cleanNumber($input['angsuran_balloon'] ?? 0),
            'angsuran_installment'          => $this->cleanNumber($input['angsuran_installment'] ?? 0),
            'angsuran_dipilih'              => $angsuran,
            'rasio_dsc_rk'                  => $dscRK,
            'rasio_dsc_iv'                  => $dscIV,
            'status_dsc_rk'                 => $statusRK,
            'status_dsc_iv'                 => $statusIV
        ];

        $cek = $this->keuanganModel->where('kredit_id', $kreditId)->first();
        if ($cek) {
            $this->keuanganModel->update($cek['keuangan_id'], $dataKeu);
        } else {
            $this->keuanganModel->insert($dataKeu);
        }
    }

    public function uploadBuktiPendapatanLain()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $folder = FCPATH . 'uploads/keuangan/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $uploaded = [];

        // Cek jika dikirim multiple files via files[]
        $filesObj = $this->request->getFiles();
        if (isset($filesObj['files']) && is_array($filesObj['files'])) {
            foreach ($filesObj['files'] as $f) {
                if ($f && $f->isValid() && !$f->hasMoved()) {
                    $newName = $f->getRandomName();
                    $f->move($folder, $newName);
                    $uploaded[] = [
                        'nama_file' => $f->getClientName(),
                        'path_file' => 'uploads/keuangan/' . $newName
                    ];
                }
            }
        } elseif ($f = $this->request->getFile('file')) {
            if ($f && $f->isValid() && !$f->hasMoved()) {
                $newName = $f->getRandomName();
                $f->move($folder, $newName);
                $uploaded[] = [
                    'nama_file' => $f->getClientName(),
                    'path_file' => 'uploads/keuangan/' . $newName
                ];
            }
        }

        if (empty($uploaded)) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid atau tidak ditemukan']);
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => count($uploaded) . ' berkas berhasil diunggah',
            'files'     => $uploaded,
            'path_file' => $uploaded[0]['path_file'],
            'nama_file' => $uploaded[0]['nama_file']
        ]);
    }

    private function simpanDisbursement($kreditId, $input)
    {
        $plafon     = $this->cleanNumber($input['plafon_pengajuan'] ?? 0);
        $provPct    = (float)($input['provisi_persen'] ?? 7);
        $provNom    = $plafon * ($provPct / 100);
        $adminPct   = (float)($input['admin_persen'] ?? 1);
        $adminNom   = $plafon * ($adminPct / 100);

        // Rincian Biaya / Potongan Lainnya (Dinamis: Keterangan & Nominal)
        $biayaLainList = [];
        $totalBiayaLain = 0;
        if (!empty($input['biaya_lain']) && is_array($input['biaya_lain'])) {
            foreach ($input['biaya_lain'] as $item) {
                $ket = trim($item['keterangan'] ?? '');
                $nom = $this->cleanNumber($item['nominal'] ?? 0);
                if ($ket !== '' || $nom > 0) {
                    $biayaLainList[] = [
                        'keterangan' => $ket ?: 'Biaya Tambahan',
                        'nominal'    => $nom
                    ];
                    $totalBiayaLain += $nom;
                }
            }
        }

        $totalPot   = $provNom + $adminNom + $totalBiayaLain;
        $netCair    = max(0, $plafon - $totalPot);

        $dataDisb = [
            'kredit_id'             => $kreditId,
            'provisi_persen'        => $provPct,
            'provisi_nominal'       => $provNom,
            'admin_persen'          => $adminPct,
            'admin_nominal'         => $adminNom,
            'agent_persen'          => 0,
            'agent_nominal'         => 0,
            'hold_angsuran_bulan'   => 0,
            'hold_angsuran_nominal' => 0,
            'asuransi_jiwa'         => 0,
            'asuransi_kebakaran'    => 0,
            'rincian_biaya_lain'    => !empty($biayaLainList) ? json_encode($biayaLainList) : null,
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

        $catatan = trim($this->request->getPost('catatan') ?? '');
        $docId = $this->dokumenModel->insert([
            'kredit_id'        => $kreditId,
            'kategori_dokumen' => $kategori,
            'nama_file_asli'   => $file->getClientName(),
            'path_file'        => 'uploads/dokumen/' . $newName,
            'ukuran_file_bytes'=> file_exists($destPath) ? filesize($destPath) : $file->getSize(),
            'tipe_file'        => $file->getClientMimeType(),
            'catatan_dokumen'  => $catatan ?: $kategori
        ]);

        return $this->response->setJSON([
            'status'     => true,
            'dokumen_id' => $docId,
            'nama_file'  => $file->getClientName(),
            'kategori'   => $kategori,
            'catatan'    => $catatan ?: $kategori,
            'ukuran_kb'  => number_format($file->getSize() / 1024, 1),
            'path'       => base_url('uploads/dokumen/' . $newName)
        ]);
    }

    public function hapusDokumen()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $docId = (int)($this->request->getPost('dokumen_id') ?? $this->request->getGet('dokumen_id') ?? 0);
        $doc = $this->dokumenModel->find($docId);
        if (!$doc) {
            return $this->response->setJSON(['status' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        // Hapus file fisik jika ada
        if (!empty($doc['path_file'])) {
            $filePath = FCPATH . $doc['path_file'];
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
        }

        $this->dokumenModel->delete($docId);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Dokumen berhasil dihapus'
        ]);
    }

    public function updateKategoriDokumen()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $docId    = (int)$this->request->getPost('dokumen_id');
        $kategori = trim($this->request->getPost('kategori') ?? '');
        $catatan  = $this->request->getPost('catatan');

        $doc = $this->dokumenModel->find($docId);
        if (!$doc) {
            return $this->response->setJSON(['status' => false, 'message' => 'Dokumen tidak ditemukan']);
        }

        $updateData = [];
        if (!empty($kategori)) {
            $updateData['kategori_dokumen'] = $kategori;
        }
        if ($catatan !== null) {
            $updateData['catatan_dokumen'] = trim($catatan);
        }

        if (!empty($updateData)) {
            $this->dokumenModel->update($docId, $updateData);
        }

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Keterangan / kategori dokumen berhasil diperbarui',
            'kategori' => $kategori ?: ($doc['kategori_dokumen'] ?? ''),
            'catatan'  => $catatan !== null ? trim($catatan) : ($doc['catatan_dokumen'] ?? '')
        ]);
    }

    public function hapus($id = null)
    {
        $isAjax = $this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        if ($redirect = $this->cekAuth()) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi login telah habis.']);
            }
            return $redirect;
        }

        $kredit = $this->kreditModel->find($id);
        if (!$kredit) {
            $msg = 'Data berkas pengajuan kredit tidak ditemukan.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $userId   = (int)session()->get('user_id');
        $userRole = strtolower(trim(session()->get('jabatan') ?? ''));
        $isExecutiveOrAdmin = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false || $userRole === 'superadmin' || $userRole === 'admin');
        $isPengusul = ($userRole === 'pengusul' || strpos($userRole, 'pengusul') !== false || strpos($userRole, 'ao') !== false || strpos($userRole, 'officer') !== false);

        // Pengusul dapat menghapus berkas pengajuan miliknya
        $isOwner = (!empty($kredit['user_id']) && (int)$kredit['user_id'] === $userId) || empty($kredit['user_id']);

        if (!$isExecutiveOrAdmin && !($isPengusul && $isOwner)) {
            $msg = 'Akses ditolak: Anda tidak memiliki izin untuk menghapus berkas pengajuan ini.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        $nomorAplikasi = $kredit['nomor_aplikasi'] ?? ('ID #' . $id);
        $this->kreditModel->delete($id);

        try {
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
        } catch (\Throwable $e) {
            // Abaikan jika log aktivitas gagal
        }

        if ($isAjax) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Berkas pengajuan ' . esc($nomorAplikasi) . ' berhasil dihapus.'
            ]);
        }

        return redirect()->back()->with('success', 'Berkas pengajuan ' . esc($nomorAplikasi) . ' berhasil dihapus.');
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
            'referensi'         => $this->referensiModel->where('is_active', 1)->orderBy('urutan', 'ASC')->findAll(),
            'pengaturan_sistem' => $pengaturanSistem,
            'laporan_keuangan'  => [],
        ];

        return view('pengajuan/cetak', $data);
    }

    public function aiWebCheck()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $namaDebitur     = $this->request->getPost('nama_debitur') ?? '';
        $namaUsaha       = $this->request->getPost('nama_usaha') ?? '';
        $kota            = $this->request->getPost('kota') ?? '';
        $bidangUsaha     = $this->request->getPost('bidang_usaha') ?? '';
        $catatanTambahan = $this->request->getPost('catatan_tambahan') ?? '';
        $jenisForm       = $this->request->getPost('jenis_form') ?? 'perorangan';
        $daftarPengurus  = $this->request->getPost('daftar_pengurus') ?? '';

        // Tambahan parameter 4 Variabel Inti Pembeda Homonim (Nama & Kota Sama)
        $metaIdentitas = [
            'bentuk_usaha'   => $this->request->getPost('bentuk_usaha') ?? '',
            'tanggal_lahir'  => $this->request->getPost('tanggal_lahir') ?? '',
            'umur_debitur'   => $this->request->getPost('umur_debitur') ?? '',
            'jabatan'        => $this->request->getPost('jabatan') ?? '',
            'alamat_lengkap' => $this->request->getPost('alamat_lengkap') ?? '',
        ];

        $service = new GeminiAiService();
        $hasil = $service->investigasiWebcheck(
            $namaDebitur,
            $namaUsaha,
            $kota,
            $bidangUsaha,
            $catatanTambahan,
            $jenisForm,
            $daftarPengurus,
            $metaIdentitas
        );

        return $this->response->setJSON($hasil);
    }

    public function uploadBuktiWebcheck()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $field = trim($this->request->getPost('field') ?? '');
        $file  = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File bukti tidak valid atau tidak terbaca.']);
        }

        $folder = FCPATH . 'uploads/webcheck/';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $newName = $file->getRandomName();
        $file->move($folder, $newName);

        $pathRelatif = 'uploads/webcheck/' . $newName;

        return $this->response->setJSON([
            'status'    => true,
            'field'     => $field,
            'nama_file' => $file->getClientName(),
            'path'      => $pathRelatif,
            'url'       => base_url($pathRelatif)
        ]);
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
        $bhNom  = isset($input['biaya_hidup_nominal_rk']) ? $this->cleanNumber($input['biaya_hidup_nominal_rk']) : ($omzet * ((float)($input['biaya_hidup_persen_rk'] ?? 10) / 100));
        $blNom  = isset($input['beban_lainnya_nominal_rk']) ? $this->cleanNumber($input['beban_lainnya_nominal_rk']) : ($omzet * ((float)($input['beban_lainnya_persen_rk'] ?? 10) / 100));
        $labaBersih = $omzet - ($omzet * $hppPct/100) - $bhNom - $blNom;
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

    public function uploadDokumenSlik()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $folder = FCPATH . 'uploads/slik';
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $uploaded = [];
        $filesObj = $this->request->getFiles();

        foreach (['file_identitas' => 'identitas', 'file_permohonan' => 'permohonan'] as $key => $type) {
            if (isset($filesObj[$key]) && $filesObj[$key]->isValid() && !$filesObj[$key]->hasMoved()) {
                $f = $filesObj[$key];
                $newName = $f->getRandomName();
                $targetFile = $folder . DIRECTORY_SEPARATOR . $newName;
                $f->move($folder, $newName);

                $mime = @mime_content_type($targetFile);
                if ($mime && str_starts_with($mime, 'image/')) {
                    $this->kompresGambarServer($targetFile, 1600, 75);
                }

                $uploaded[$type] = [
                    'name' => $f->getClientName(),
                    'path' => 'uploads/slik/' . $newName,
                    'url'  => base_url('uploads/slik/' . $newName),
                    'size' => number_format(filesize($targetFile) / 1024, 1) . ' KB'
                ];
            }
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Berkas berhasil diunggah',
            'files' => $uploaded
        ]);
    }

    private function kompresGambarServer($filePath, $maxWidth = 1600, $quality = 75)
    {
        if (!extension_loaded('gd') || !file_exists($filePath)) return;
        $info = @getimagesize($filePath);
        if (!$info) return;

        $mime = $info['mime'];
        $src = null;
        switch ($mime) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($filePath);
                break;
            case 'image/webp':
                $src = @imagecreatefromwebp($filePath);
                break;
        }
        if (!$src) return;

        $w = imagesx($src);
        $h = imagesy($src);
        if ($w > $maxWidth || $h > $maxWidth) {
            if ($w > $h) {
                $newH = (int)round(($h * $maxWidth) / $w);
                $newW = $maxWidth;
            } else {
                $newW = (int)round(($w * $maxWidth) / $h);
                $newH = $maxWidth;
            }
            $dst = imagecreatetruecolor($newW, $newH);
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($src);
            $src = $dst;
        }

        if ($mime === 'image/png') {
            imagepng($src, $filePath, 6);
        } elseif ($mime === 'image/webp') {
            imagewebp($src, $filePath, $quality);
        } else {
            imagejpeg($src, $filePath, $quality);
        }
        imagedestroy($src);
    }

    public function uploadPdfSlik()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $file = $this->request->getFile('file_pdf');
        $kreditId = $this->request->getPost('kredit_id');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File PDF tidak valid atau gagal diunggah']);
        }

        $newName = $file->getRandomName();
        $targetDir = FCPATH . 'uploads/slik';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $file->move($targetDir, $newName);
        $filePath = 'uploads/slik/' . $newName;

        if ($kreditId) {
            $this->kreditModel->update($kreditId, [
                'berkas_slik_pdf' => $filePath
            ]);
        }

        return $this->response->setJSON([
            'status'   => true,
            'message'  => 'Berkas PDF SLIK iDeb berhasil diunggah',
            'filepath' => $filePath,
            'url'      => base_url($filePath),
            'filename' => $file->getClientName()
        ]);
    }

    public function uploadHasilSlik()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $kreditId   = $this->request->getPost('kredit_id');
        $activeIdx  = $this->request->getPost('active_index');
        $hasilSlik  = $this->request->getPost('hasil_slik') ?? 'Lancar';
        $statusSlik = 'Telah Terbit';

        $filePdf = $this->request->getFile('file_pdf');
        $fileTxt = $this->request->getFile('file_txt');

        $updateData = [
            'status_slik'      => $statusSlik,
            'hasil_slik'       => $hasilSlik,
            'slik_verified_at' => date('Y-m-d H:i:s'),
            'slik_verified_by' => session()->get('nama_lengkap') ?: 'Staff Pengusul Kredit'
        ];

        $targetDir = FCPATH . 'uploads/slik';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $pdfPath = null;
        if ($filePdf && $filePdf->isValid()) {
            $newNamePdf = $filePdf->getRandomName();
            $filePdf->move($targetDir, $newNamePdf);
            $pdfPath = 'uploads/slik/' . $newNamePdf;
            $updateData['berkas_slik_pdf'] = $pdfPath;
        }

        $txtPath = null;
        if ($fileTxt && $fileTxt->isValid()) {
            $newNameTxt = $fileTxt->getRandomName();
            $fileTxt->move($targetDir, $newNameTxt);
            $txtPath = 'uploads/slik/' . $newNameTxt;
            $updateData['berkas_slik_txt'] = $txtPath;
        }

        // Sinkronisasi daftar_permohonan_slik JSON
        $daftarSlikPost = $this->request->getPost('daftar_permohonan_slik');
        $daftarSlikArray = [];
        if (!empty($daftarSlikPost)) {
            $decoded = json_decode($daftarSlikPost, true);
            if (is_array($decoded)) {
                $daftarSlikArray = $decoded;
            }
        } elseif ($kreditId) {
            $existingKredit = $this->kreditModel->find($kreditId);
            if (!empty($existingKredit['daftar_permohonan_slik'])) {
                $decoded = json_decode($existingKredit['daftar_permohonan_slik'], true);
                if (is_array($decoded)) {
                    $daftarSlikArray = $decoded;
                }
            }
        }

        if (!empty($daftarSlikArray)) {
            $matched = false;
            if ($activeIdx !== null && $activeIdx !== '') {
                foreach ($daftarSlikArray as &$item) {
                    if (isset($item['index']) && (int)$item['index'] === (int)$activeIdx) {
                        $item['status'] = $statusSlik;
                        $item['hasil']  = $hasilSlik;
                        if ($pdfPath) $item['berkas_pdf'] = $pdfPath;
                        if ($txtPath) $item['berkas_txt'] = $txtPath;
                        $matched = true;
                    }
                }
                unset($item);
            }
            if (!$matched && isset($daftarSlikArray[0])) {
                $daftarSlikArray[0]['status'] = $statusSlik;
                $daftarSlikArray[0]['hasil']  = $hasilSlik;
                if ($pdfPath) $daftarSlikArray[0]['berkas_pdf'] = $pdfPath;
                if ($txtPath) $daftarSlikArray[0]['berkas_txt'] = $txtPath;
            }
            $updateData['daftar_permohonan_slik'] = json_encode($daftarSlikArray);
        }

        if ($kreditId) {
            $this->kreditModel->update($kreditId, $updateData);
        }

        // Otomatis ekstrak data fasilitas dari berkas PDF yang baru diunggah
        $extractedFacilities = [];
        if ($pdfPath && file_exists(FCPATH . $pdfPath)) {
            $namaDeb = !empty($existingKredit['nama_debitur']) ? $existingKredit['nama_debitur'] : '';
            $extractedFacilities = $this->parseSlikFacilitiesFromPdf(FCPATH . $pdfPath, $namaDeb);
            if (!empty($extractedFacilities) && $kreditId) {
                $this->slikModel->where('kredit_id', $kreditId)->delete();
                foreach ($extractedFacilities as $fac) {
                    $this->slikModel->insert([
                        'kredit_id'       => $kreditId,
                        'nama_debitur'    => $fac['nama_debitur'],
                        'plafon'          => (float)$fac['plafon'],
                        'outstanding'     => (float)$fac['outstanding'],
                        'kelancaran'      => $fac['kelancaran'],
                        'bank_lembaga'    => $fac['bank_lembaga'],
                        'keterangan'      => $fac['keterangan'] ?? 'Modal Kerja',
                        'is_verified'     => 1,
                        'diinput_oleh'    => session()->get('nama_lengkap') ?: 'Sistem SLIK'
                    ]);
                }
            }
        }

        return $this->response->setJSON([
            'status'     => true,
            'message'    => 'Hasil SLIK berhasil disimpan dan diterbitkan!',
            'hasil_slik' => $hasilSlik,
            'status_slik'=> $statusSlik,
            'pdf_path'   => $pdfPath,
            'pdf_url'    => $pdfPath ? base_url($pdfPath) : null,
            'txt_path'   => $txtPath,
            'facilities' => $extractedFacilities,
            'daftar_permohonan_slik' => $updateData['daftar_permohonan_slik'] ?? null
        ]);
    }

    /**
     * Meneruskan permohonan pengecekan SLIK debitur ke antrean kerja Petugas Verifikator SLIK OJK
     */
    public function kirimKePetugasSlik()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $kreditId = $this->request->getPost('kredit_id');
        if (!$kreditId) {
            return $this->response->setStatusCode(400)->setJSON(['status' => false, 'message' => 'Kredit ID tidak valid']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan']);
        }

        // Ambil daftar permohonan slik yang dikirim jika ada
        $daftarPermohonanPost = $this->request->getPost('daftar_permohonan_slik');
        $daftarPermohonan = [];
        if (!empty($daftarPermohonanPost)) {
            $decoded = json_decode($daftarPermohonanPost, true);
            if (is_array($decoded)) {
                $daftarPermohonan = $decoded;
            }
        } elseif (!empty($kredit['daftar_permohonan_slik'])) {
            $decoded = json_decode($kredit['daftar_permohonan_slik'], true);
            if (is_array($decoded)) {
                $daftarPermohonan = $decoded;
            }
        }

        // Update status di setiap permohonan menjadi 'Menunggu Pengecekan'
        if (!empty($daftarPermohonan)) {
            foreach ($daftarPermohonan as &$row) {
                if (($row['status'] ?? '') !== 'Telah Terbit' && ($row['status'] ?? '') !== 'Terbit') {
                    $row['status'] = 'Menunggu Pengecekan';
                    $row['hasil']  = 'Menunggu';
                }
            }
        }

        $updateData = [
            'status_slik' => 'Menunggu Pengecekan',
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        if (!empty($daftarPermohonan)) {
            $updateData['daftar_permohonan_slik'] = json_encode($daftarPermohonan);
        }

        $this->kreditModel->update($kreditId, $updateData);

        // Catat Audit Trail
        LogAktivitasModel::catat(
            'Mengirimkan permohonan verifikasi SLIK debitur (' . ($kredit['nama_debitur'] ?? '-') . ') ke Petugas Verifikator SLIK',
            'SLIK',
            'SUCCESS',
            [
                'kredit_id'       => $kreditId,
                'nomor_aplikasi'  => $kredit['nomor_aplikasi'] ?? '',
                'nama_debitur'    => $kredit['nama_debitur'] ?? '',
                'pengusul'        => session()->get('nama_lengkap')
            ]
        );

        return $this->response->setJSON([
            'status'                 => true,
            'status_slik'            => 'Menunggu Pengecekan',
            'daftar_permohonan_slik' => $updateData['daftar_permohonan_slik'] ?? null,
            'message'                => 'Permohonan SLIK berhasil dikirimkan ke Petugas Verifikator SLIK OJK!'
        ]);
    }

    public function ambilRingkasanSlik()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized']);
        }

        $kreditId   = $this->request->getPost('kredit_id');
        $activeIdx  = $this->request->getPost('active_index');
        $pdfPathReq = $this->request->getPost('pdf_path');

        $kredit = $kreditId ? $this->kreditModel->find($kreditId) : null;
        $namaDebitur = $kredit['nama_debitur'] ?? '';

        // Cari berkas PDF dari parameter, daftar permohonan JSON, atau kolom berkas_slik_pdf
        $pdfPath = $pdfPathReq;
        if (empty($pdfPath) && !empty($kredit['daftar_permohonan_slik'])) {
            $daftarSlik = json_decode($kredit['daftar_permohonan_slik'], true);
            if (is_array($daftarSlik)) {
                if ($activeIdx !== null && isset($daftarSlik[$activeIdx]['berkas_pdf'])) {
                    $pdfPath = $daftarSlik[$activeIdx]['berkas_pdf'];
                    if (!empty($daftarSlik[$activeIdx]['nama'])) {
                        $namaDebitur = $daftarSlik[$activeIdx]['nama'];
                    }
                } else {
                    foreach ($daftarSlik as $row) {
                        if (!empty($row['berkas_pdf'])) {
                            $pdfPath = $row['berkas_pdf'];
                            if (!empty($row['nama'])) $namaDebitur = $row['nama'];
                            break;
                        }
                    }
                }
            }
        }
        if (empty($pdfPath) && !empty($kredit['berkas_slik_pdf'])) {
            $pdfPath = $kredit['berkas_slik_pdf'];
        }

        $facilities = [];
        if (!empty($pdfPath) && file_exists(FCPATH . $pdfPath)) {
            $parsed = $this->parseSlikFacilitiesFromPdf(FCPATH . $pdfPath, $namaDebitur);
            if (!empty($parsed)) {
                $facilities = $parsed;
                if ($kreditId) {
                    $this->slikModel->where('kredit_id', $kreditId)->delete();
                    foreach ($facilities as $fac) {
                        $this->slikModel->insert([
                            'kredit_id'       => $kreditId,
                            'nama_debitur'    => $fac['nama_debitur'],
                            'plafon'          => (float)$fac['plafon'],
                            'outstanding'     => (float)$fac['outstanding'],
                            'kelancaran'      => $fac['kelancaran'],
                            'bank_lembaga'    => $fac['bank_lembaga'],
                            'keterangan'      => $fac['keterangan'] ?? 'Modal Kerja',
                            'is_verified'     => 1,
                            'diinput_oleh'    => session()->get('nama_lengkap') ?: 'Sistem SLIK'
                        ]);
                    }
                }
            }
        }

        // Fallback jika sudah ada data fasilitas di tabel slik
        if (empty($facilities) && $kreditId) {
            $existing = $this->slikModel->where('kredit_id', $kreditId)->findAll();
            if (!empty($existing)) {
                $facilities = $existing;
            }
        }

        if (empty($facilities)) {
            return $this->response->setJSON([
                'status'     => false,
                'message'    => 'Berkas hasil SLIK belum diunggah atau tidak ditemukan data fasilitas kredit aktif. Silakan klik tombol Proses untuk mengunggah berkas PDF Hasil SLIK.',
                'facilities' => []
            ]);
        }

        return $this->response->setJSON([
            'status'     => true,
            'message'    => 'Data ringkasan SLIK berhasil dimuat dari berkas hasil SLIK.',
            'facilities' => $facilities,
            'pdf_path'   => $pdfPath
        ]);
    }

    private function ascii85_decode($str)
    {
        $str = preg_replace('/\s+/', '', $str);
        if (str_starts_with($str, '<~')) $str = substr($str, 2);
        if (str_ends_with($str, '~>')) $str = substr($str, 0, -2);
        
        $out = '';
        $len = strlen($str);
        $tuple = 0;
        $count = 0;
        
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];
            if ($c === 'z' && $count === 0) {
                $out .= "\0\0\0\0";
                continue;
            }
            $val = ord($c) - 33;
            if ($val < 0 || $val > 84) continue;
            $tuple = $tuple * 85 + $val;
            $count++;
            if ($count === 5) {
                $out .= chr(($tuple >> 24) & 0xFF) . chr(($tuple >> 16) & 0xFF) . chr(($tuple >> 8) & 0xFF) . chr($tuple & 0xFF);
                $tuple = 0;
                $count = 0;
            }
        }
        if ($count > 0) {
            $padding = 5 - $count;
            for ($i = 0; $i < $padding; $i++) {
                $tuple = $tuple * 85 + 84;
            }
            for ($i = 0; $i < $count - 1; $i++) {
                $out .= chr(($tuple >> (24 - $i * 8)) & 0xFF);
            }
        }
        return $out;
    }

    private function extractPdfText($filePath)
    {
        if (!file_exists($filePath)) return '';
        $raw = file_get_contents($filePath);
        $text = '';
        
        $offset = 0;
        while (($sPos = strpos($raw, 'stream', $offset)) !== false) {
            $dictStart = strrpos(substr($raw, 0, $sPos), '<<');
            $dict = '';
            if ($dictStart !== false) {
                $dict = substr($raw, $dictStart, $sPos - $dictStart);
            }
            
            $nlPos = $sPos + 6;
            while ($nlPos < strlen($raw) && ($raw[$nlPos] === "\r" || $raw[$nlPos] === "\n")) {
                $nlPos++;
            }
            $ePos = strpos($raw, 'endstream', $nlPos);
            if ($ePos === false) break;
            
            $streamData = substr($raw, $nlPos, $ePos - $nlPos);
            $offset = $ePos + 9;
            
            $decoded = $streamData;
            if (stripos($dict, 'ASCII85Decode') !== false) {
                $decoded = $this->ascii85_decode($decoded);
            }
            if (stripos($dict, 'FlateDecode') !== false) {
                $uncompressed = @gzuncompress($decoded);
                if (!$uncompressed) $uncompressed = @gzinflate($decoded);
                if (!$uncompressed) $uncompressed = @zlib_decode($decoded);
                if ($uncompressed) $decoded = $uncompressed;
            }
            
            $text .= ' ' . $decoded;
        }
        return $text;
    }

    private function parseSlikFacilitiesFromPdf($filePath, $defaultDebitur = '')
    {
        $rawText = $this->extractPdfText($filePath);
        if (empty($rawText)) return [];
        
        preg_match_all('/\((.*?)\)Tj|\[(.*?)\]TJ|<([0-9a-fA-F]+)>T[jJ]/', $rawText, $matches, PREG_SET_ORDER);
        
        $tokens = [];
        foreach ($matches as $m) {
            if (!empty($m[1])) {
                $tokens[] = stripcslashes($m[1]);
            } elseif (!empty($m[2])) {
                preg_match_all('/<([0-9a-fA-F]+)>|\((.*?)\)/', $m[2], $innerMatches, PREG_SET_ORDER);
                $part = '';
                foreach ($innerMatches as $im) {
                    if (!empty($im[1])) {
                        $part .= hex2bin($im[1]);
                    } elseif (isset($im[2])) {
                        $part .= stripcslashes($im[2]);
                    }
                }
                if ($part !== '') $tokens[] = $part;
            } elseif (!empty($m[3])) {
                $tokens[] = hex2bin($m[3]);
            }
        }
        
        $namaDebitur = $defaultDebitur;
        foreach ($tokens as $idx => $t) {
            if (stripos($t, 'UD SUMBER') !== false || stripos($t, 'SUMBER REJEKI') !== false) {
                $namaDebitur = trim($t);
            }
        }
        
        $facilities = [];
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $t = trim($tokens[$i]);
            if (preg_match('/^([1-9][0-9]?)$/', $t, $numMatch)) {
                $rowNum = (int)$numMatch[1];
                $rowTokens = [];
                for ($j = $i + 1; $j < $count; $j++) {
                    $nextT = trim($tokens[$j]);
                    if (preg_match('/^' . ($rowNum + 1) . '$/', $nextT)) {
                        break;
                    }
                    if ($nextT === 'Ringkasan Analisa SLIK' || $nextT === 'Hal. 1') {
                        break;
                    }
                    $rowTokens[] = $nextT;
                }
                
                $fullRowStr = implode('§', $rowTokens);
                $fullRowStr = str_replace(['\(', '\)'], ['(', ')'], $fullRowStr);
                $fullRowStr = preg_replace('/Rp\s*([0-9.,]+)§([0-9.,]+)/i', 'Rp $1$2', $fullRowStr);
                $fullRowStr = preg_replace('/Rp§\s*([0-9.,]+)/i', 'Rp $1', $fullRowStr);
                $fullRowStr = preg_replace('/([0-9]{2}-[0-9]{2}-[0-9])§([0-9]{3})/', '$1$2', $fullRowStr);
                $fullRowStr = preg_replace('/Konsum§si/i', 'Konsumsi', $fullRowStr);
                $fullRowStr = preg_replace('/Modal§Kerja/i', 'Modal Kerja', $fullRowStr);
                $fullRowStr = preg_replace('/PT Bank§/i', 'PT Bank ', $fullRowStr);
                $fullRowStr = preg_replace('/Modal Kerja§Lainnya§\(Dengan§Perjanjian\)/i', 'Modal Kerja Lainnya (Dengan Perjanjian)', $fullRowStr);
                $fullRowStr = preg_replace('/Kartu Kredit\/§Kartu§Pembiayaan§Syariah/i', 'Kartu Kredit/Kartu Pembiayaan Syariah', $fullRowStr);
                
                $reTokens = explode('§', $fullRowStr);
                
                $bank = '';
                $jenisPenggunaan = 'Modal Kerja';
                $jenisKredit = 'Kredit Usaha';
                $plafon = 0;
                $outstanding = 0;
                $sukuBunga = '-';
                $tglAkad = '-';
                $jatuhTempo = '-';
                $jangkaWaktu = '-';
                $tunggakan = '0';
                $kelancaran = 'Lancar';
                $kondisi = 'Fasilitas Aktif';
                
                $amounts = [];
                foreach ($reTokens as $k => $item) {
                    if (preg_match('/Rp\s*([0-9.,]+)/i', $item, $amtMatch)) {
                        $val = (float)str_replace(['.', ','], '', $amtMatch[1]);
                        $amounts[] = $val;
                    }
                }
                if (isset($amounts[0])) $plafon = $amounts[0];
                if (isset($amounts[1])) $outstanding = $amounts[1];
                
                $dates = [];
                foreach ($reTokens as $k => $item) {
                    if (preg_match('/[0-9]{2}-[0-9]{2}-[0-9]{4}/', $item, $dMatch)) {
                        $dates[] = $dMatch[0];
                    }
                }
                if (isset($dates[0])) $tglAkad = $dates[0];
                if (isset($dates[1])) $jatuhTempo = $dates[1];
                
                foreach ($reTokens as $k => $item) {
                    if (preg_match('/[0-9.,]+%/', $item, $bMatch)) {
                        $sukuBunga = $bMatch[0];
                        break;
                    }
                }
                
                foreach ($reTokens as $k => $item) {
                    if (preg_match('/[0-9]+\s*bln/i', $item, $jwMatch)) {
                        $jangkaWaktu = $jwMatch[0];
                        break;
                    }
                }
                
                $bankParts = [];
                $foundBank = false;
                foreach ($reTokens as $k => $item) {
                    if (stripos($item, 'Bank') !== false || stripos($item, 'BPR') !== false || $foundBank) {
                        if (stripos($item, 'Modal') !== false || stripos($item, 'Konsum') !== false || stripos($item, 'Rp') !== false) {
                            break;
                        }
                        $bankParts[] = $item;
                        $foundBank = true;
                    }
                }
                if (!empty($bankParts)) {
                    $bank = implode(' ', $bankParts);
                }
                
                foreach ($reTokens as $k => $item) {
                    if (stripos($item, 'Modal Kerja') !== false && !stripos($item, 'Lainnya')) {
                        $jenisPenggunaan = 'Modal Kerja';
                    } elseif (stripos($item, 'Konsum') !== false) {
                        $jenisPenggunaan = 'Konsumsi';
                    } elseif (stripos($item, 'Investasi') !== false) {
                        $jenisPenggunaan = 'Investasi';
                    }
                }
                
                $kreditParts = [];
                foreach ($reTokens as $k => $item) {
                    if (stripos($item, 'Kartu Kredit') !== false || stripos($item, 'Pembiayaan') !== false || stripos($item, 'Lainnya') !== false) {
                        $kreditParts[] = $item;
                    }
                }
                if (!empty($kreditParts)) {
                    $jenisKredit = implode(' ', $kreditParts);
                } elseif ($jenisPenggunaan === 'Modal Kerja') {
                    $jenisKredit = 'Modal Kerja Lainnya (Dengan Perjanjian)';
                }
                
                foreach ($reTokens as $k => $item) {
                    if (in_array(strtolower($item), ['lancar', 'dpk', 'kurang lancar', 'diragukan', 'macet'])) {
                        $kelancaran = ucfirst(strtolower($item));
                        break;
                    }
                }
                
                $facilities[] = [
                    'nama_debitur'    => $namaDebitur ?: ($defaultDebitur ?: 'Debitur'),
                    'bank_lembaga'    => $bank ?: 'Bank Mitra',
                    'jenis_penggunaan'=> $jenisPenggunaan,
                    'jenis_kredit'    => $jenisKredit,
                    'keterangan'      => $jenisPenggunaan,
                    'jenis_fasilitas' => $jenisKredit,
                    'plafon'          => $plafon,
                    'outstanding'     => $outstanding,
                    'suku_bunga'      => $sukuBunga,
                    'tgl_akad'        => $tglAkad,
                    'jatuh_tempo'     => $jatuhTempo,
                    'jangka_waktu'    => $jangkaWaktu,
                    'tunggakan'       => $tunggakan,
                    'kelancaran'      => $kelancaran,
                    'kondisi'         => $kondisi
                ];
                
                $i = $j - 1;
            }
        }
        return $facilities;
    }

    private function cleanNumber($val)
    {
        if (is_numeric($val)) return (float)$val;
        return (float)preg_replace('/[^0-9]/', '', (string)$val);
    }

    // ==========================================
    // TAHAP 10: GENERATE LAPORAN KEUANGAN ESTIMASI
    // ==========================================
    public function generateLaporan()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir, silakan login ulang.']);
        }

        $input    = $this->request->getPost();
        $kreditId = (int)($input['kredit_id'] ?? 0);
        if ($kreditId <= 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID pengajuan tidak valid.']);
        }

        // Ambil data kredit untuk menentukan tipe pemohon
        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan tidak ditemukan.']);
        }

        $rawJd       = $kredit['jenis_debitur'] ?? 'Perorangan';
        $tipePemohon = (stripos($rawJd, 'badan') !== false || stripos($rawJd, 'pt') !== false || stripos($rawJd, 'cv') !== false)
            ? 'badan_usaha' : 'karyawan';

        // Ambil data laba rugi dari kredit_keuangan_dsc sebagai base
        $keuangan = $this->keuanganModel->where('kredit_id', $kreditId)->first() ?? [];

        // Gabungkan data DB dengan input POST (input POST override DB)
        $mergedInput = array_merge($keuangan, $input);
        $mergedInput['daftar_karyawan'] = $kredit['daftar_karyawan'] ?? '[]';

        // Hitung laporan menggunakan service
        $service = new LaporanKeuanganService();
        $hasil   = $service->hitungLaporan($mergedInput, $tipePemohon);

        // Validasi keseimbangan neraca (badan usaha)
        $validasi = $hasil['validasi'];
        $warningMsg = null;
        if ($tipePemohon === 'badan_usaha' && !$validasi['is_seimbang']) {
            $selisih = abs($validasi['selisih_neraca']);
            $warningMsg = 'Neraca tidak seimbang — Selisih: Rp ' . number_format($selisih, 0, ',', '.') . '. Periksa kembali data input di Tahap 9.';
        }

        // Simpan ke tabel kredit_laporan_keuangan (upsert by kredit_id)
        $laporanModel  = new KreditLaporanKeuanganModel();
        $existingLaporan = $laporanModel->where('kredit_id', $kreditId)->first();

        $labaRugi  = $hasil['laba_rugi'];
        $arusKas   = $hasil['arus_kas'];
        $neraca    = $hasil['neraca'] ?? [];
        $asumsi    = $hasil['asumsi'];

        $dataLaporan = [
            'kredit_id'                 => $kreditId,
            'tipe_pemohon'              => $tipePemohon,
            // Asumsi & Saldo Awal
            'asumsi_dso'                => $asumsi['dso'] ?? 30,
            'asumsi_dio'                => $asumsi['dio'] ?? 45,
            'asumsi_dpo'                => $asumsi['dpo'] ?? 30,
            'saldo_awal_piutang'        => $asumsi['saldo_awal_piutang'] ?? 0,
            'saldo_awal_persediaan'     => $asumsi['saldo_awal_persediaan'] ?? 0,
            'saldo_awal_utang_usaha'    => $asumsi['saldo_awal_utang_usaha'] ?? 0,
            'saldo_awal_aset_tetap'     => $asumsi['saldo_awal_aset_tetap'] ?? 0,
            'saldo_awal_kas'            => $asumsi['saldo_awal_kas'] ?? 0,
            // Input Neraca
            'kas_bank_neraca'           => $neraca['kas_bank'] ?? ($arusKas['kas_akhir'] ?? 0),
            'piutang_usaha_neraca'      => $neraca['piutang_usaha'] ?? 0,
            'persediaan_neraca'         => $neraca['persediaan'] ?? 0,
            'aset_lancar_lain_neraca'   => $neraca['aset_lancar_lain'] ?? 0,
            'harga_perolehan_aset_tetap'=> $neraca['harga_perolehan_aset_tetap'] ?? 0,
            'akumulasi_penyusutan'      => $neraca['akumulasi_penyusutan'] ?? 0,
            'umur_ekonomis_tahun'       => $neraca['umur_ekonomis_tahun'] ?? 5,
            'utang_usaha'               => $neraca['utang_usaha'] ?? 0,
            'utang_bank_pendek'         => $neraca['utang_bank_pendek'] ?? 0,
            'utang_jangka_panjang'      => $neraca['utang_jangka_panjang'] ?? 0,
            'modal_disetor'             => $neraca['modal_disetor'] ?? 0,
            'laba_ditahan_awal'         => $neraca['laba_ditahan_awal'] ?? 0,
            'laba_ditahan_akhir'        => $neraca['laba_ditahan_akhir'] ?? 0,
            'prive_dividen'             => $neraca['prive_dividen'] ?? (float)($input['prive_dividen'] ?? $input['input_prive_dividen'] ?? 0),
            // Laba Rugi Summary
            'pendapatan'                => $labaRugi['pendapatan'],
            'hpp'                       => $labaRugi['hpp'],
            'laba_kotor'                => $labaRugi['laba_kotor'],
            'beban_gaji'                => $labaRugi['beban_gaji'],
            'beban_operasional'         => $labaRugi['beban_operasional'],
            'beban_sewa'                => $labaRugi['beban_sewa'] ?? 0,
            'beban_lainnya'             => $labaRugi['beban_lainnya'],
            'beban_penyusutan'          => $labaRugi['beban_penyusutan'] ?? 0,
            'laba_operasional'          => $labaRugi['laba_operasional'],
            'pendapatan_lain'           => $labaRugi['pendapatan_lain'],
            'beban_bunga'               => $labaRugi['beban_bunga'],
            'laba_sebelum_pajak'        => $labaRugi['laba_sebelum_pajak'],
            'pajak'                     => $labaRugi['pajak'],
            'laba_bersih'               => $labaRugi['laba_bersih'],
            // Arus Kas
            'kas_operasional'           => $arusKas['kas_operasional'],
            'kas_investasi'             => $arusKas['kas_investasi'],
            'kas_pendanaan'             => $arusKas['kas_pendanaan'],
            'kenaikan_bersih_kas'       => $arusKas['kenaikan_bersih_kas'],
            'kas_akhir'                 => $arusKas['kas_akhir'],
            // Neraca Ringkas
            'total_aset'                => $neraca['total_aset'] ?? 0,
            'total_liabilitas'          => $neraca['total_liabilitas'] ?? 0,
            'total_ekuitas'             => $neraca['total_ekuitas'] ?? 0,
            // Validasi
            'status_neraca'             => $validasi['status_neraca'],
            'selisih_neraca'            => $validasi['selisih_neraca'],
            'status_kas'                => $validasi['status_kas'],
            'selisih_kas'               => $validasi['selisih_kas'],
            // Simpan JSON lengkap
            'json_laporan'              => json_encode($hasil),
            'generated_at'              => date('Y-m-d H:i:s'),
            'generated_by'              => session()->get('user_id'),
        ];

        try {
            if ($existingLaporan) {
                $laporanModel->update($existingLaporan['id'], $dataLaporan);
            } else {
                $laporanModel->insert($dataLaporan);
            }

            // Juga update status ke tabel kredit_keuangan_dsc jika ada
            if ($keuangan && !empty($keuangan['keuangan_id'])) {
                $this->keuanganModel->update($keuangan['keuangan_id'], [
                    'is_laporan_generated' => 1,
                    'laporan_generated_at' => date('Y-m-d H:i:s'),
                    'laporan_json_data'    => json_encode($hasil),
                    'neraca_total_aset'    => $neraca['total_aset'] ?? 0,
                    'cf_kas_akhir'         => $arusKas['kas_akhir'] ?? 0,
                ]);
            }
        } catch (\Throwable $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()]);
        }

        // Catat ke log aktivitas
        try {
            LogAktivitasModel::catat(
                'Generate Laporan Keuangan Estimasi untuk pengajuan #' . $kredit['nomor_aplikasi'] . ' (' . ($kredit['nama_debitur'] ?? '-') . ')',
                'LAPORAN',
                'SUCCESS',
                [
                    'kredit_id'      => $kreditId,
                    'tipe_pemohon'   => $tipePemohon,
                    'laba_bersih'    => $labaRugi['laba_bersih'],
                    'kas_akhir'      => $arusKas['kas_akhir'],
                    'status_neraca'  => $validasi['status_neraca'],
                ]
            );
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'   => true,
            'warning'  => $warningMsg,
            'message'  => $warningMsg
                ? ('Laporan berhasil dibuat. ⚠️ ' . $warningMsg)
                : 'Laporan Keuangan Estimasi berhasil dibuat.',
            'data'     => $hasil,
        ]);
    }
}
