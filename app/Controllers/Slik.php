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

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        if ($jabatan !== 'superadmin' && strpos($jabatan, 'slik') === false) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses khusus Petugas Verifikasi SLIK.']);
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

        // Mulai Transaksi Database untuk menjamin integritas data
        $this->db->transBegin();

        try {
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
                    $pAcuan      = $plafon > 0 ? $plafon : $outstanding;
                    $kelancaran  = trim($row['kelancaran'] ?? 'Lancar');
                    $bungaStr    = trim($row['suku_bunga'] ?? '10%');
                    $jwStr       = trim($row['jangka_waktu'] ?? '36 bln');
                    $angsuran    = $this->cleanNumber($row['angsuran'] ?? 0);

                    // Kalkulasi otomatis angsuran jika belum terisi
                    if ($angsuran <= 0 && $pAcuan > 0) {
                        $angsuran = $this->hitungAngsuranPinjamanResmi($pAcuan, $bungaStr, $jwStr);
                    }

                    $totalPlafonSlik += $plafon;
                    $totalBakiDebet  += $outstanding;
                    $jumlahFasilitas++;

                    $kolLower = strtolower($kelancaran);
                    if ($kolLower !== 'lancar' && !empty($kolLower)) {
                        $adaTunggakan = true;
                    }

                    $dataSlikInsert = [
                        'kredit_id'       => $kreditId,
                        'nama_debitur'    => $nama ?: ($kredit['nama_debitur'] ?: 'Debitur'),
                        'bank_lembaga'    => $bank,
                        'jenis_fasilitas' => $row['jenis_fasilitas'] ?? ($row['keterangan'] ?? 'Kredit Usaha'),
                        'keterangan'      => $row['keterangan'] ?? ($row['jenis_fasilitas'] ?? 'Modal Kerja'),
                        'plafon'          => $plafon,
                        'outstanding'     => $outstanding,
                        'angsuran'        => $angsuran,
                        'kelancaran'      => $kelancaran,
                        'is_verified'     => 1, // Resmi disetujui / diverifikasi oleh petugas SLIK
                        'diinput_oleh'    => 'petugas_slik'
                    ];

                    if ($this->db->fieldExists('suku_bunga', 'slik')) $dataSlikInsert['suku_bunga'] = $bungaStr;
                    if ($this->db->fieldExists('jangka_waktu', 'slik')) $dataSlikInsert['jangka_waktu'] = $jwStr;
                    if ($this->db->fieldExists('tgl_akad', 'slik')) $dataSlikInsert['tgl_akad'] = $row['tgl_akad'] ?? '-';
                    if ($this->db->fieldExists('jatuh_tempo', 'slik')) $dataSlikInsert['jatuh_tempo'] = $row['jatuh_tempo'] ?? '-';
                    if ($this->db->fieldExists('tunggakan', 'slik')) $dataSlikInsert['tunggakan'] = $row['tunggakan'] ?? '0';

                    $this->slikModel->insert($dataSlikInsert);
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

            // Handle Berkas Upload Hasil SLIK (PDF & TXT) dengan Validasi Ketat
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
                $extPdf = strtolower($filePdf->getClientExtension() ?: $filePdf->guessExtension() ?: '');
                $mimePdf = $filePdf->getMimeType();
                if ($extPdf !== 'pdf' || (strpos((string)$mimePdf, 'pdf') === false && strpos((string)$mimePdf, 'octet-stream') === false) || $filePdf->getSize() > 20 * 1024 * 1024) {
                    $this->db->transRollback();
                    return $this->response->setJSON(['status' => false, 'message' => 'Berkas hasil SLIK harus berupa dokumen PDF resmi maksimal 20 MB.']);
                }
                $newNamePdf = $filePdf->getRandomName();
                $filePdf->move($targetDir, $newNamePdf);
                $updateKredit['berkas_slik_pdf'] = 'uploads/slik/' . $newNamePdf;
            }

            $fileTxt = $this->request->getFile('file_txt');
            if ($fileTxt && $fileTxt->isValid() && !$fileTxt->hasMoved()) {
                $extTxt = strtolower($fileTxt->getClientExtension() ?: $fileTxt->guessExtension() ?: '');
                if ($extTxt !== 'txt' || $fileTxt->getSize() > 10 * 1024 * 1024) {
                    $this->db->transRollback();
                    return $this->response->setJSON(['status' => false, 'message' => 'Berkas teks SLIK harus berupa file TXT maksimal 10 MB.']);
                }
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

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan transaksi verifikasi SLIK.']);
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Slik::simpan error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }

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

    /**
     * Hapus / Reset Hasil Verifikasi SLIK
     */
    public function hapusSlik($kreditId = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON(['status' => false, 'message' => 'Sesi berakhir, silakan login ulang']);
        }

        $jabatan = strtolower(session()->get('jabatan') ?? '');
        if ($jabatan !== 'superadmin' && strpos($jabatan, 'slik') === false) {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses khusus Petugas Verifikasi SLIK.']);
        }

        $kreditId = (int)$kreditId;
        if ($kreditId <= 0) {
            $kreditId = (int)$this->request->getPost('kredit_id');
        }

        if ($kreditId <= 0) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID Pengajuan tidak valid']);
        }

        $kredit = $this->kreditModel->find($kreditId);
        if (!$kredit) {
            return $this->response->setJSON(['status' => false, 'message' => 'Pengajuan kredit tidak ditemukan']);
        }

        $idx = $this->request->getPost('subjek_index');

        $this->db->transBegin();
        try {
            $daftarPermohonan = json_decode($kredit['daftar_permohonan_slik'] ?? '[]', true);
            if (!is_array($daftarPermohonan)) {
                $daftarPermohonan = [];
            }

            $namaSubjekDihapus = '';

            if ($idx !== null && $idx !== '' && isset($daftarPermohonan[(int)$idx])) {
                $subjekDihapus = $daftarPermohonan[(int)$idx];
                $namaSubjekDihapus = $subjekDihapus['nama'] ?? '';

                // Hapus berkas fisik khusus subjek ini jika ada
                if (!empty($subjekDihapus['file_pdf']) && file_exists(FCPATH . $subjekDihapus['file_pdf'])) {
                    @unlink(FCPATH . $subjekDihapus['file_pdf']);
                }
                if (!empty($subjekDihapus['file_txt']) && file_exists(FCPATH . $subjekDihapus['file_txt'])) {
                    @unlink(FCPATH . $subjekDihapus['file_txt']);
                }

                // Hapus subjek ini dari daftar permohonan SLIK
                array_splice($daftarPermohonan, (int)$idx, 1);

                // Re-index array
                foreach ($daftarPermohonan as $newI => &$item) {
                    $item['index'] = $newI;
                }
                unset($item);

                // Hapus fasilitas yang terhubung dengan nama subjek ini
                if (!empty($namaSubjekDihapus)) {
                    $this->slikModel->where('kredit_id', $kreditId)
                                   ->where('nama_debitur', $namaSubjekDihapus)
                                   ->delete();
                }
            } else {
                // Hapus seluruh fasilitas dan kosongkan seluruh subjek SLIK
                $this->slikModel->where('kredit_id', $kreditId)->delete();
                $daftarPermohonan = [];

                if (!empty($kredit['berkas_slik_pdf']) && file_exists(FCPATH . $kredit['berkas_slik_pdf'])) {
                    @unlink(FCPATH . $kredit['berkas_slik_pdf']);
                }
                if (!empty($kredit['berkas_slik_txt']) && file_exists(FCPATH . $kredit['berkas_slik_txt'])) {
                    @unlink(FCPATH . $kredit['berkas_slik_txt']);
                }
            }

            // Update data ke tabel kredit
            $updateData = [
                'daftar_permohonan_slik' => json_encode(array_values($daftarPermohonan)),
            ];

            if (empty($daftarPermohonan)) {
                $updateData['status_slik']      = 'Belum Terbit';
                $updateData['hasil_slik']       = 'Menunggu Pengecekan';
                $updateData['ringkasan_slik']   = '';
                $updateData['berkas_slik_pdf']  = null;
                $updateData['berkas_slik_txt']  = null;
                $updateData['slik_verified_at'] = null;
                $updateData['slik_verified_by'] = null;
                $this->slikModel->where('kredit_id', $kreditId)->delete();
            } else {
                $semuaTerbit = true;
                $adaTunggakan = false;
                foreach ($daftarPermohonan as $s) {
                    $st = strtolower($s['status'] ?? '');
                    if ($st !== 'telah terbit' && $st !== 'terbit') {
                        $semuaTerbit = false;
                    }
                    if (in_array(strtolower($s['hasil'] ?? ''), ['dpk', 'kurang lancar', 'diragukan', 'macet'])) {
                        $adaTunggakan = true;
                    }
                }
                if ($semuaTerbit) {
                    $updateData['status_slik'] = 'Terbit';
                    $updateData['hasil_slik']  = $adaTunggakan ? 'Kolektibilitas 2-5 (Ada Tunggakan)' : 'Kol 1 (Lancar / Clear)';
                } else {
                    $updateData['status_slik'] = 'Belum Terbit';
                    $updateData['hasil_slik']  = 'Menunggu Pengecekan';
                }
            }

            $this->kreditModel->update($kreditId, $updateData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus subjek permohonan SLIK.']);
            }

            $this->db->transCommit();

            try {
                LogAktivitasModel::catat(
                    'Petugas SLIK menghapus subjek permohonan SLIK (' . ($namaSubjekDihapus ?: 'Semua Subjek') . ') berkas ' . ($kredit['nomor_aplikasi'] ?? $kreditId),
                    'SLIK',
                    'WARNING',
                    ['kredit_id' => $kreditId, 'subjek' => $namaSubjekDihapus]
                );
            } catch (\Throwable $e) {}

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Subjek permohonan SLIK ' . ($namaSubjekDihapus ? "($namaSubjekDihapus) " : '') . 'berhasil dihapus dari daftar!'
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setJSON(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function hitungAngsuranPinjamanResmi($plafon, $bungaStr = '10%', $jwStr = '12 bln')
    {
        $plafon = (float)$plafon;
        if ($plafon <= 0) return 0;

        $bungaPct = 10;
        if (preg_match('/([0-9]+([.,][0-9]+)?)/', (string)$bungaStr, $bm)) {
            $bungaPct = (float)str_replace(',', '.', $bm[1]);
        }
        if ($bungaPct <= 0) $bungaPct = 10;

        $tenorBulan = 12;
        if (preg_match('/([0-9]+)/', (string)$jwStr, $jwm)) {
            $tenorBulan = (int)$jwm[1];
        }
        if ($tenorBulan <= 0) $tenorBulan = 12;

        $tenorTahun = $tenorBulan / 12;
        $totalBungaPct = ($bungaPct / 100) * $tenorTahun;
        $totalPengembalian = $plafon * (1 + $totalBungaPct);
        $angsuranBulanan = (int)round($totalPengembalian / $tenorBulan);

        return $angsuranBulanan;
    }

    private function cleanNumber($val)
    {
        if ($val === null || $val === '') return 0.0;
        if (is_int($val) || is_float($val)) return (float)$val;

        $s = trim((string)$val);
        if ($s === '' || $s === '-') return 0.0;

        $isNeg = strpos($s, '-') === 0;
        $s = preg_replace('/[^0-9.,-]/', '', $s);

        if (preg_match('/,\d{1,2}$/', $s)) {
            $parts = explode(',', $s);
            $s = $parts[0];
        }

        if (preg_match('/^-?\d+\.\d{1,2}$/', $s)) {
            return (float)$s;
        }

        $clean = preg_replace('/[^0-9]/', '', $s);
        $num = $clean !== '' ? (float)$clean : 0.0;
        return $isNeg ? -$num : $num;
    }
}
