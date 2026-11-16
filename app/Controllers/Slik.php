<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\SlikModel;
use App\Models\LogAktivitasModel;

class Slik extends BaseController
{
    protected $kreditModel;
    protected $slikModel;
    protected $db;

    public function __construct()
    {
        $this->kreditModel = new KreditModel();
        $this->slikModel   = new SlikModel();
        $this->db          = \Config\Database::connect();
    }

    private function cekAuthSlik()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth')->with('error', 'Silakan login terlebih dahulu');
        }

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        if ($jabatan !== 'superadmin' && strpos($jabatan, 'slik') === false) {
            return redirect()->to('/dashboard')->with('error', 'Akses khusus Petugas Verifikasi SLIK.');
        }

        return null;
    }

    /**
     * Tampilan Formulir Khusus Verifikasi SLIK
     */
    public function verifikasi($kreditId = null)
    {
        if ($redirect = $this->cekAuthSlik()) return $redirect;

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return redirect()->to('/dashboard')->with('error', 'Data pengajuan kredit tidak ditemukan.');
        }

        // Ambil data SLIK yang sudah ada
        $allSlik = $this->slikModel->where('kredit_id', $kreditId)->findAll();
        
        // Cek data dari usulan pengusul (is_verified = 0) vs resmi (is_verified = 1)
        $usulanPengusul = [];
        $slikResmi      = [];

        foreach ($allSlik as $s) {
            if (isset($s['is_verified']) && (int)$s['is_verified'] === 1) {
                $slikResmi[] = $s;
            } else {
                $usulanPengusul[] = $s;
            }
        }

        // Jika belum ada data resmi tapi ada usulan, atau sebaliknya
        $daftarSlik = !empty($slikResmi) ? $slikResmi : $usulanPengusul;

        // Ambil data berkas dokumen nasabah dari Tab 5 (terutama KTP)
        $dokumenModel = new \App\Models\KreditDokumenModel();
        $daftarDokumen = $dokumenModel->where('kredit_id', $kreditId)->findAll();
        $ktpDokumen = null;
        foreach ($daftarDokumen as $doc) {
            $kat = strtolower($doc['kategori_dokumen'] ?? '');
            $nm  = strtolower($doc['nama_dokumen'] ?? '');
            if (strpos($kat, 'ktp') !== false || strpos($nm, 'ktp') !== false) {
                $ktpDokumen = $doc;
                break;
            }
        }

        // Format No Reff IDEB OJK Baku
        $tglTimestamp = !empty($kredit['created_at']) ? strtotime($kredit['created_at']) : time();
        $entitas = (isset($kredit['jenis_debitur']) && (stripos($kredit['jenis_debitur'], 'badan') !== false || stripos($kredit['jenis_debitur'], 'usaha') !== false || stripos($kredit['jenis_debitur'], 'pt') !== false || stripos($kredit['jenis_debitur'], 'cv') !== false)) ? 'B' : 'I';
        $noIdVal = !empty($kredit['slik_permohonan_nik']) ? $kredit['slik_permohonan_nik'] : (!empty($kredit['npwp_pribadi']) ? $kredit['npwp_pribadi'] : (!empty($kredit['nik']) ? $kredit['nik'] : ''));
        $noReffSlik = $kreditId . '/BPR-SA/IDEB/' . date('Y', $tglTimestamp) . '|01|' . $entitas . '|' . ($noIdVal ?: '-');

        // Ambil pengaturan sistem
        $pengaturanSistem = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];

        $data = [
            'title'             => 'Verifikasi SLIK OJK - ' . $kredit['nomor_aplikasi'],
            'nama_lengkap'      => session()->get('nama_lengkap'),
            'jabatan'           => session()->get('jabatan'),
            'kredit'            => $kredit,
            'kredit_id'         => $kreditId,
            'nomor_aplikasi'    => $kredit['nomor_aplikasi'],
            'slik'              => $daftarSlik,
            'usulan_pengusul'   => $usulanPengusul,
            'has_usulan'        => (!empty($usulanPengusul) && empty($slikResmi)),
            'pengaturan_sistem' => $pengaturanSistem,
            'daftar_dokumen'    => $daftarDokumen,
            'ktp_dokumen'       => $ktpDokumen,
            'no_reff_slik'      => $noReffSlik,
            'no_id_val'         => $noIdVal
        ];

        return view('petugas_slik/form_verifikasi_slik', $data);
    }

    /**
     * Simpan & Terbitkan Hasil Verifikasi SLIK
     */
    public function simpan()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir, silakan login ulang']);
        }

        $input    = $this->request->getPost();
        $kreditId = $input['kredit_id'] ?? null;

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID Pengajuan tidak valid']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pengajuan kredit tidak ditemukan']);
        }

        // 1. Simpan Data Fasilitas SLIK
        // Bersihkan data lama untuk kredit ini
        $this->slikModel->where('kredit_id', $kreditId)->delete();

        $slikInputs = $input['slik'] ?? [];
        $totalBakiDebet = 0;
        $totalPlafonSlik = 0;
        $adaTunggakan = false;
        $jumlahFasilitas = 0;

        if (is_array($slikInputs) && !empty($slikInputs)) {
            foreach ($slikInputs as $row) {
                $bank = trim($row['bank_lembaga'] ?? '');
                $nama = trim($row['nama_debitur'] ?? '');
                
                // Lewati jika nama dan bank kosong
                if (empty($bank) && empty($nama)) {
                    continue;
                }

                $plafon      = $this->cleanNumber($row['plafon'] ?? 0);
                $outstanding = $this->cleanNumber($row['outstanding'] ?? 0);
                $kelancaran  = trim($row['kelancaran'] ?? 'Lancar');

                $totalPlafonSlik += $plafon;
                $totalBakiDebet += $outstanding;
                $jumlahFasilitas++;

                $kolLower = strtolower($kelancaran);
                if ($kolLower !== 'lancar' && !empty($kolLower)) {
                    $adaTunggakan = true;
                }

                $this->slikModel->insert([
                    'kredit_id'    => $kreditId,
                    'nama_debitur' => $nama ?: ($kredit['nama_debitur'] ?: 'Debitur'),
                    'bank_lembaga' => $bank,
                    'plafon'       => $plafon,
                    'outstanding'  => $outstanding,
                    'kelancaran'   => $kelancaran,
                    'keterangan'   => $row['keterangan'] ?? '',
                    'is_verified'  => 1, // Resmi disetujui / diverifikasi oleh petugas SLIK
                    'diinput_oleh' => 'petugas_slik'
                ]);
            }
        }

        // Tentukan Hasil SLIK
        $ringkasanSlik = trim($input['ringkasan_slik'] ?? '');
        $statusSlikInput = $input['status_slik'] ?? 'Terbit';

        if ($statusSlikInput === 'Terbit') {
            $statusSlik = 'Terbit';
            if ($adaTunggakan) {
                $hasilSlik = 'Kolektibilitas 2-5 (Ada Tunggakan)';
            } else {
                $hasilSlik = 'Kol 1 (Lancar / Clear)';
            }
        } else {
            $statusSlik = 'Belum Terbit';
            $hasilSlik  = 'Menunggu Pengecekan';
        }

        // Handle Berkas Upload Hasil SLIK (PDF & TXT)
        $updateKredit = [
            'ringkasan_slik'   => $ringkasanSlik,
            'status_slik'      => $statusSlik,
            'hasil_slik'       => $hasilSlik,
            'slik_verified_at' => date('Y-m-d H:i:s'),
            'slik_verified_by' => session()->get('nama_lengkap')
        ];

        $targetDir = FCPATH . 'uploads/slik';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filePdf = $this->request->getFile('file_pdf');
        if ($filePdf && $filePdf->isValid() && !$filePdf->hasMoved()) {
            $newNamePdf = $filePdf->getRandomName();
            $filePdf->move($targetDir, $newNamePdf);
            $updateKredit['berkas_slik_pdf'] = 'uploads/slik/' . $newNamePdf;
        }

        $fileTxt = $this->request->getFile('file_txt');
        if ($fileTxt && $fileTxt->isValid() && !$fileTxt->hasMoved()) {
            $newNameTxt = $fileTxt->getRandomName();
            $fileTxt->move($targetDir, $newNameTxt);
            $updateKredit['berkas_slik_txt'] = 'uploads/slik/' . $newNameTxt;
        }

        // Update data kredit induk
        try {
            $tableCols = $this->kreditModel->db->getFieldNames('kredit');
            if (is_array($tableCols) && !empty($tableCols)) {
                $updateKredit = array_intersect_key($updateKredit, array_flip($tableCols));
            }
        } catch (\Throwable $e) {}
        $this->kreditModel->update($kreditId, $updateKredit);

        // Catat ke log aktivitas sistem
        try {
            $nomorApp = $kredit['nomor_aplikasi'] ?? ('ID #' . $kreditId);
            $namaDeb  = $kredit['nama_debitur'] ?? '-';
            LogAktivitasModel::catat(
                'Petugas SLIK memverifikasi & menerbitkan hasil SLIK OJK berkas ' . $nomorApp . ' (' . $namaDeb . ' - ' . $hasilSlik . ')',
                'SLIK',
                ($adaTunggakan ? 'WARNING' : 'SUCCESS'),
                [
                    'kredit_id'        => $kreditId,
                    'nomor_aplikasi'   => $nomorApp,
                    'hasil_slik'       => $hasilSlik,
                    'jumlah_fasilitas' => $jumlahFasilitas,
                    'total_baki_debet' => $totalBakiDebet
                ]
            );
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data Verifikasi SLIK OJK berhasil disimpan dan diterbitkan!',
            'redirect'=> base_url('dashboard')
        ]);
    }

    /**
     * Setujui seluruh usulan data SLIK dari pengusul
     */
    public function setujuiPengusul($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pengajuan tidak ditemukan']);
        }

        // Update seluruh baris slik milik kredit ini menjadi is_verified = 1
        $this->slikModel->where('kredit_id', $kreditId)->set([
            'is_verified'  => 1,
            'diinput_oleh' => 'petugas_slik'
        ])->update();

        // Hitung kolektibilitas
        $rows = $this->slikModel->where('kredit_id', $kreditId)->findAll();
        $adaTunggakan = false;
        foreach ($rows as $r) {
            $kol = strtolower($r['kelancaran'] ?? '');
            if ($kol !== 'lancar' && !empty($kol)) {
                $adaTunggakan = true;
            }
        }

        $hasilSlik = $adaTunggakan ? 'Kolektibilitas 2-5 (Ada Tunggakan)' : 'Kol 1 (Lancar / Clear)';

        $this->kreditModel->update($kreditId, [
            'status_slik'      => 'Terbit',
            'hasil_slik'       => $hasilSlik,
            'slik_verified_at' => date('Y-m-d H:i:s'),
            'slik_verified_by' => session()->get('nama_lengkap')
        ]);

        // Catat ke log aktivitas sistem
        try {
            $nomorApp = $kredit['nomor_aplikasi'] ?? ('ID #' . $kreditId);
            $namaDeb  = $kredit['nama_debitur'] ?? '-';
            LogAktivitasModel::catat(
                'Petugas SLIK menyetujui seluruh usulan data SLIK pengusul pada berkas ' . $nomorApp . ' (' . $namaDeb . ' - ' . $hasilSlik . ')',
                'SLIK',
                'SUCCESS',
                ['kredit_id' => $kreditId, 'hasil_slik' => $hasilSlik]
            );
        } catch (\Throwable $e) {}

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data fasilitas SLIK dari pengusul berhasil DISETUJUI dan disahkan ke tabel resmi!'
        ]);
    }

    /**
     * Toggle Akses Input SLIK oleh Pengusul / Petugas SLIK (Aktif / Nonaktif)
     */
    public function toggleAksesPengusul()
    {
        if ($redirect = $this->cekAuthSlik()) return $redirect;

        $pengaturan = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        $currentAktif = (int)($pengaturan['petugas_slik_aktif'] ?? (($pengaturan['ijin_pengusul_isi_slik'] ?? 1) == 0 ? 1 : 0));
        $newAktif     = ($currentAktif === 1) ? 0 : 1;
        $ijinPengusul = ($newAktif === 1) ? 0 : 1;

        $this->db->table('pengaturan_sistem')->where('id', 1)->update([
            'petugas_slik_aktif'     => $newAktif,
            'ijin_pengusul_isi_slik' => $ijinPengusul,
            'updated_at'             => date('Y-m-d H:i:s')
        ]);

        $statusText = ($newAktif === 1) ? 'DIAKTIFKAN (Mode Standar / SOP Ketat)' : 'DINONAKTIFKAN (Mode Fleksibel / Single AO)';

        return $this->response->setJSON([
            'status'             => true,
            'is_active'          => ($newAktif === 1),
            'petugas_slik_aktif' => $newAktif,
            'ijin_pengusul_isi_slik' => $ijinPengusul,
            'message'            => 'Status Petugas SLIK berhasil ' . $statusText
        ]);
    }

    /**
     * Persetujuan / Permintaan verifikasi SLIK dari Pengusul
     */
    public function setujuiPengajuanSlik($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir, silakan login ulang']);
        }

        if (!$kreditId) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID Pengajuan tidak valid']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data pengajuan kredit tidak ditemukan']);
        }

        $this->kreditModel->update($kreditId, [
            'status_slik' => 'Menunggu Pengecekan',
            'hasil_slik'  => 'Menunggu Pengecekan Petugas SLIK'
        ]);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Permohonan pengecekan SLIK berhasil diajukan ke meja kerja Petugas SLIK!'
        ]);
    }

    private function cleanNumber($val)
    {
        if (is_numeric($val)) return (float)$val;
        $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
        return (float)$cleaned;
    }
}
