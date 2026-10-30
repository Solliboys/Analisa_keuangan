<?php

namespace App\Controllers;

use App\Models\KreditModel;
use App\Models\SlikModel;

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
            'pengaturan_sistem' => $pengaturanSistem
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
                $kelancaran  = $row['kelancaran'] ?? 'Lancar';

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

        // Update data kredit induk
        $this->kreditModel->update($kreditId, [
            'ringkasan_slik'   => $ringkasanSlik,
            'status_slik'      => $statusSlik,
            'hasil_slik'       => $hasilSlik,
            'slik_verified_at' => date('Y-m-d H:i:s'),
            'slik_verified_by' => session()->get('nama_lengkap')
        ]);

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

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data fasilitas SLIK dari pengusul berhasil DISETUJUI dan disahkan ke tabel resmi!'
        ]);
    }

    /**
     * Toggle Akses Input SLIK oleh Pengusul (Aktif / Nonaktif)
     */
    public function toggleAksesPengusul()
    {
        if ($redirect = $this->cekAuthSlik()) return $redirect;

        $pengaturan = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray();
        $currentVal = (int)($pengaturan['ijin_pengusul_isi_slik'] ?? 1);
        $newVal     = $currentVal === 1 ? 0 : 1;

        $this->db->table('pengaturan_sistem')->where('id', 1)->update([
            'ijin_pengusul_isi_slik' => $newVal,
            'updated_at'             => date('Y-m-d H:i:s')
        ]);

        $statusText = $newVal === 1 ? 'DIAKTIFKAN (Pengusul dapat mengisi draf SLIK)' : 'DINONAKTIFKAN (Khusus Petugas SLIK)';

        return $this->response->setJSON([
            'status'    => true,
            'is_active' => $newVal === 1,
            'message'   => 'Akses pengisian SLIK oleh Pengusul berhasil ' . $statusText
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
