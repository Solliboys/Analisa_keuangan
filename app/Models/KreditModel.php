<?php

namespace App\Models;

use CodeIgniter\Model;

class KreditModel extends Model
{
    protected $table            = 'kredit';
    protected $primaryKey       = 'kredit_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nomor_aplikasi',
        'tipe_aplikasi',
        'jenis_debitur',
        'nama_debitur',
        'plafon_pengajuan',
        'tenor_bulan',
        'jenis_jaminan',
        'tujuan_pembiayaan',
        'skema_pembayaran',
        'perhitungan_bunga',
        'bunga_per_bulan',
        'bunga_per_tahun',
        'alamat_ktp',
        'alamat_domisili',
        'kepemilikan_domisili',
        'jenis_usaha',
        'bidang_usaha',
        'nomor_hp',
        'nomor_emergency',
        'nama_kontak_darurat',
        'npwp_pribadi',
        'nama_ibu_kandung',
        'tempat_tanggal_lahir',
        'tempat_lahir',
        'tanggal_lahir',
        'akta_pendirian',
        'akta_perubahan',
        'sk_pendirian',
        'sk_perubahan',
        'nama_usaha',
        'jabatan',
        'lama_usaha',
        'alamat_usaha',
        'npwp_usaha',
        'nomor_induk_berusaha_nib',
        'daftar_karyawan',
        'sumber_pembayaran',
        'ringkasan_slik',
        'daftar_permohonan_slik',
        'metode_slik',
        'berkas_slik_pdf',
        'berkas_slik_txt',
        'kesimpulan_kolektibilitas',
        'slik_permohonan_nama',
        'slik_permohonan_nik',
        'slik_permohonan_hubungan',
        'slik_ktp_file',
        'slik_surat_kuasa_file',
        'status_slik',
        'hasil_slik',
        'slik_verified_at',
        'slik_verified_by',
        'status_pengajuan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
