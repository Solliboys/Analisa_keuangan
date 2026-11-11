<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Services\LaporanKeuanganService;

final class LaporanKeuanganServiceTest extends CIUnitTestCase
{
    private LaporanKeuanganService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LaporanKeuanganService();
    }

    /**
     * Test 1: Kasus Seimbang — Sesuai spesifikasi Seed Badan Usaha (dalam juta Rp)
     * Pendapatan: 1.200.000.000, HPP: 700.000.000, Gaji (2 karyawan): 96.000.000,
     * Sewa/Utilitas/Pemasaran: 104.000.000, Penyusutan: 30.000.000, Bunga: 20.000.000, Pajak: 25.000.000
     * Kas awal: 150.000.000; Piutang: 80jt -> 120jt; Persediaan: 100jt -> 130jt;
     * Utang usaha: 60jt -> 90jt; Aset tetap awal: 300jt; Utang bank: 200jt -> 160jt;
     * Modal: 250jt; Laba ditahan awal: 120jt; Beli aset (CAPEX): 50jt; Cicilan pokok: 40jt; Prive: 75jt
     * Hasil: Laba bersih 225jt, Kas operasi 215jt, Kas akhir 200jt, Aset lancar 450jt,
     * Aset tetap neto 320jt, Total aset 770jt = Liabilitas 250jt + Ekuitas 520jt
     */
    public function testKasusSeimbangBadanUsaha(): void
    {
        $input = [
            'pendapatan'                 => 1200000000,
            'hpp'                        => 700000000,
            'daftar_karyawan'            => [
                ['nama' => 'Staff 1', 'gaji' => 48000000],
                ['nama' => 'Staff 2', 'gaji' => 48000000],
            ],
            'beban_operasional'          => 104000000,
            'beban_depresiasi'           => 30000000,
            'input_beban_bunga'          => 20000000,
            'input_beban_pajak'          => 25000000,
            // Aset Lancar
            'neraca_awal_kas'            => 150000000,
            'neraca_awal_piutang'        => 80000000,
            'input_piutang_akhir'        => 120000000,
            'neraca_awal_persediaan'     => 100000000,
            'input_persediaan_akhir'     => 130000000,
            'neraca_awal_utang_usaha'    => 60000000,
            'input_utang_usaha_akhir'    => 90000000,
            // Aset Tetap
            'neraca_awal_aset_tetap'     => 300000000,
            'input_capex'                => 50000000,
            'neraca_akumulasi_penyusutan'=> 30000000,
            // Liabilitas & Ekuitas
            'neraca_awal_utang_bank'     => 200000000,
            'input_pembayaran_pokok'     => 40000000,
            'neraca_modal_disetor'       => 250000000,
            'neraca_awal_laba_ditahan'   => 120000000,
            'input_prive_dividen'        => 75000000,
        ];

        $hasil = $this->service->hitungLaporan($input, 'badan_usaha');

        // Laba Rugi
        $this->assertEquals(500000000, $hasil['laba_rugi']['laba_kotor']);
        $this->assertEquals(96000000, $hasil['laba_rugi']['beban_gaji']);
        $this->assertEquals(270000000, $hasil['laba_rugi']['laba_operasional']);
        $this->assertEquals(225000000, $hasil['laba_rugi']['laba_bersih']);

        // Arus Kas
        $this->assertEquals(215000000, $hasil['arus_kas']['kas_operasional']);
        $this->assertEquals(-50000000, $hasil['arus_kas']['kas_investasi']);
        $this->assertEquals(-115000000, $hasil['arus_kas']['kas_pendanaan']);
        $this->assertEquals(200000000, $hasil['arus_kas']['kas_akhir']);

        // Neraca
        $this->assertNotNull($hasil['neraca']);
        $this->assertEquals(200000000, $hasil['neraca']['kas_bank']);
        $this->assertEquals(450000000, $hasil['neraca']['total_aset_lancar']);
        $this->assertEquals(320000000, $hasil['neraca']['aset_tetap_neto']);
        $this->assertEquals(770000000, $hasil['neraca']['total_aset']);
        $this->assertEquals(250000000, $hasil['neraca']['total_liabilitas']);
        $this->assertEquals(520000000, $hasil['neraca']['total_ekuitas']);
        $this->assertEquals(770000000, $hasil['neraca']['total_liabilitas_ekuitas']);

        // Validasi
        $this->assertEquals('Seimbang', $hasil['validasi']['status_neraca']);
        $this->assertEquals('Cocok', $hasil['validasi']['status_kas']);
        $this->assertTrue($hasil['validasi']['is_seimbang']);
    }

    /**
     * Test 2: Kasus Selisih (Unbalanced Balance Sheet Terdeteksi)
     */
    public function testKasusSelisihBadanUsaha(): void
    {
        $input = [
            'pendapatan'                 => 100000000,
            'hpp'                        => 60000000,
            'beban_operasional'          => 20000000,
            'harga_perolehan_aset_tetap' => 100000000,
            'akumulasi_penyusutan'       => 0,
            'modal_disetor'              => 20000000,
            'saldo_awal_laba_ditahan'    => 0,
            'utang_usaha'                => 10000000,
            'saldo_awal_kas'             => 5000000,
        ];

        $hasil = $this->service->hitungLaporan($input, 'badan_usaha');

        $this->assertEquals('Tidak Seimbang', $hasil['validasi']['status_neraca']);
        $this->assertNotEquals(0, $hasil['validasi']['selisih_neraca']);
        $this->assertFalse($hasil['validasi']['is_seimbang']);
    }

    /**
     * Test 3: Kasus Piutang Kosong Menggunakan Asumsi DSO
     */
    public function testEstimasiDsoKetikaPiutangKosong(): void
    {
        $input = [
            'pendapatan'                 => 365000000, // Rp 365jt agar perhitungan DSO 30 hari = Rp 30jt tepat
            'hpp'                        => 150000000,
            'asumsi_dso'                 => 30,
            // input_piutang_akhir sengaja dikosongkan
            'modal_disetor'              => 50000000,
            'saldo_awal_kas'             => 10000000,
        ];

        $hasil = $this->service->hitungLaporan($input, 'badan_usaha');

        // Piutang harus dihitung = (30 / 365) * 365.000.000 = 30.000.000
        $this->assertEquals(30000000, $hasil['neraca']['piutang_usaha']);
        $this->assertEquals('estimasi', $hasil['neraca']['piutang_sumber']);
    }

    /**
     * Test 4: Perorangan Tanpa Neraca
     * Sesuai spesifikasi Seed:
     * Penghasilan utama 8,0jt, lain-lain 1,0jt, pengeluaran rumah tangga 4,5jt, cicilan 2,0jt, surplus 2,5jt
     */
    public function testPeroranganTanpaNeraca(): void
    {
        $input = [
            'penghasilan_utama'          => 8000000,
            'pendapatan_lain'            => 1000000,
            'pengeluaran_rumah_tangga'   => 4500000,
            'cicilan_berjalan'           => 2000000,
            'saldo_awal_kas'             => 5000000,
        ];

        $hasil = $this->service->hitungLaporan($input, 'karyawan');

        // Laba Rugi Perorangan
        $this->assertEquals(8000000, $hasil['laba_rugi']['pendapatan']);
        $this->assertEquals(1000000, $hasil['laba_rugi']['pendapatan_lain']);
        $this->assertEquals(9000000, $hasil['laba_rugi']['total_penghasilan']);
        $this->assertEquals(2500000, $hasil['laba_rugi']['laba_bersih']); // Surplus = 9jt - 4.5jt - 2jt = 2.5jt

        // Arus Kas
        $this->assertEquals(2500000, $hasil['arus_kas']['kas_operasional']);
        $this->assertEquals(2500000, $hasil['arus_kas']['kenaikan_bersih_kas']);
        $this->assertEquals(7500000, $hasil['arus_kas']['kas_akhir']); // 5jt + 2.5jt = 7.5jt

        // Tanpa Neraca
        $this->assertNull($hasil['neraca']);
        $this->assertEquals('Tidak Berlaku (Karyawan)', $hasil['validasi']['status_neraca']);
        $this->assertTrue($hasil['validasi']['is_seimbang']);
    }

    /**
     * Test 5: Perorangan / Karyawan dengan Pendapatan Dinamis (Gaji + Bonus + Lembur) & Beban Bebas
     * Contoh: Gaji Pokok 10jt, Bonus 2jt, Pendapatan Lain (Usaha Sampingan) 3jt -> Total Penghasilan 15jt
     * Biaya Hidup 6jt, Beban Bebas (Asuransi) 1jt, Cicilan 3jt -> Total Pengeluaran + Cicilan 10jt
     * Surplus = 15jt - 6jt - 1jt - 3jt = 5jt
     */
    public function testPeroranganPendapatanDinamisDanBebanBebas(): void
    {
        $input = [
            'detail_pendapatan_rk'       => json_encode([
                ['nama' => 'Gaji Pokok', 'nominal' => 10000000],
                ['nama' => 'Bonus',      'nominal' => 2000000],
            ]),
            'pendapatan_lain_rk'         => 3000000,
            'biaya_hidup_nominal_rk'     => 6000000,
            'detail_beban_lainnya_rk'    => json_encode([
                ['nama' => 'Premi Asuransi', 'nominal' => 1000000],
            ]),
            'cicilan_berjalan'           => 3000000,
            'saldo_awal_kas'             => 10000000,
        ];

        $hasil = $this->service->hitungLaporan($input, 'karyawan');

        // Total Penghasilan Pokok dari rincian = 12.000.000
        $this->assertEquals(12000000, $hasil['laba_rugi']['pendapatan']);
        // Pendapatan Lain-lain (Usaha Sampingan) = 3.000.000
        $this->assertEquals(3000000, $hasil['laba_rugi']['pendapatan_lain']);
        // Total Penghasilan = 15.000.000
        $this->assertEquals(15000000, $hasil['laba_rugi']['total_penghasilan']);
        // Beban Pokok = 6.000.000, Beban Lainnya = 1.000.000, Total Pengeluaran = 7.000.000
        $this->assertEquals(6000000, $hasil['laba_rugi']['beban_operasional']);
        $this->assertEquals(1000000, $hasil['laba_rugi']['beban_lainnya']);
        $this->assertEquals(7000000, $hasil['laba_rugi']['total_pengeluaran']);
        // Surplus = 15jt - 7jt - 3jt (cicilan) = 5.000.000
        $this->assertEquals(5000000, $hasil['laba_rugi']['laba_bersih']);

        // Arus Kas Akhir = Saldo Awal (10jt) + Surplus (5jt) = 15.000.000
        $this->assertEquals(15000000, $hasil['arus_kas']['kas_akhir']);
    }
}
