<?php

namespace App\Controllers;

use App\Models\KreditModel;
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

        // Superadmin, CEO, Direktur, Reviewer, dan Petugas SLIK melihat seluruh pengajuan
        $isGlobal = ($jabatan === 'superadmin' || 
                     strpos($jabatan, 'ceo') !== false || 
                     strpos($jabatan, 'direk') !== false || 
                     strpos($jabatan, 'review') !== false || 
                     strpos($jabatan, 'slik') !== false);

        if ($isGlobal) {
            $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
        } else {
            $daftarKredit = $this->kreditModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
        }

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

        $isGlobal = ($jabatan === 'superadmin' || 
                     strpos($jabatan, 'ceo') !== false || 
                     strpos($jabatan, 'direk') !== false || 
                     strpos($jabatan, 'review') !== false || 
                     strpos($jabatan, 'slik') !== false);

        if ($isGlobal) {
            $daftarKredit = $this->kreditModel->orderBy('created_at', 'DESC')->findAll();
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

        $input = $this->request->getPost();
        $userId = session()->get('user_id');
        $kreditId = !empty($input['kredit_id']) ? $input['kredit_id'] : null;

        $dataKredit = [
            'user_id'                 => $userId,
            'nomor_aplikasi'          => $input['nomor_aplikasi'] ?? ('CAM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))),
            'tipe_aplikasi'           => $input['tipe_aplikasi'] ?? 'Fasilitas Baru',
            'jenis_debitur'           => $input['jenis_debitur'] ?? 'Individu',
            'nama_debitur'            => $input['nama_debitur'] ?? '',
            'plafon_pengajuan'        => $this->cleanNumber($input['plafon_pengajuan'] ?? 0),
            'tenor_bulan'             => (int)($input['tenor_bulan'] ?? 12),
            'jenis_jaminan'           => $input['jenis_jaminan'] ?? 'On Hand',
            'tujuan_pembiayaan'       => $input['tujuan_pembiayaan'] ?? 'Modal Kerja',
            'skema_pembayaran'        => $input['skema_pembayaran'] ?? 'Balloon Payment',
            'bunga_per_bulan'         => (float)($input['bunga_per_bulan'] ?? 3),
            'bunga_per_tahun'         => (float)($input['bunga_per_tahun'] ?? 36),
            'alamat_ktp'              => $input['alamat_ktp'] ?? '',
            'alamat_domisili'         => $input['alamat_domisili'] ?? '',
            'kepemilikan_domisili'    => $input['kepemilikan_domisili'] ?? 'Milik Sendiri',
            'nomor_hp'                => $input['nomor_hp'] ?? '',
            'nomor_emergency'         => $input['nomor_emergency'] ?? '',
            'npwp_pribadi'            => $input['npwp_pribadi'] ?? '',
            'nama_ibu_kandung'        => $input['nama_ibu_kandung'] ?? '',
            'tempat_tanggal_lahir'    => $input['tempat_tanggal_lahir'] ?? '',
            'akta_pendirian'          => $input['akta_pendirian'] ?? '',
            'akta_perubahan'          => $input['akta_perubahan'] ?? '',
            'sk_pendirian'            => $input['sk_pendirian'] ?? '',
            'sk_perubahan'            => $input['sk_perubahan'] ?? '',
            'nama_usaha'              => $input['nama_usaha'] ?? '',
            'jabatan'                 => $input['jabatan'] ?? '',
            'lama_usaha'              => $input['lama_usaha'] ?? '',
            'alamat_usaha'            => $input['alamat_usaha'] ?? '',
            'npwp_usaha'              => $input['npwp_usaha'] ?? '',
            'nomor_induk_berusaha_nib'=> $input['nomor_induk_berusaha_nib'] ?? '',
            'sumber_pembayaran'       => $input['sumber_pembayaran'] ?? '',
            'ringkasan_slik'          => $input['ringkasan_slik'] ?? '',
            'status_pengajuan'        => $input['status_pengajuan'] ?? 'Draft'
        ];

        if ($kreditId) {
            $this->kreditModel->update($kreditId, $dataKredit);
        } else {
            $kreditId = $this->kreditModel->insert($dataKredit);
        }

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data induk kredit']);
        }

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

        // 8. Simpan Approval Komite (Tab 13)
        $this->simpanKomite($kreditId, $input);

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Data berhasil disimpan',
            'kredit_id' => $kreditId
        ]);
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
            'path'       => base_url('uploads/dokumen/' . $newName)
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
