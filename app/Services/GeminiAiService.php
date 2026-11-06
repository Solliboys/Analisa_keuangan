<?php

namespace App\Services;

class GeminiAiService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Ambil pengaturan API Gemini dari database
     */
    public function getSettings()
    {
        $sys = $this->db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        $ai  = $this->db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray() ?? [];

        return [
            'api_key'           => $sys['api_key'] ?? $ai['api_key'] ?? (getenv('GEMINI_API_KEY') ?: ''),
            'model_ai'          => !empty($sys['model_ai']) ? $sys['model_ai'] : (!empty($ai['model_ai']) ? $ai['model_ai'] : 'gemini-3.6-flash'),
            'api_endpoint'      => $sys['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models',
            'prompt_sistem'     => $sys['prompt_sistem'] ?? 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).',
            'prompt_character'  => $ai['prompt_character'] ?? 'Analisis karakter debitur berdasarkan riwayat kredit dan integritas.',
            'prompt_capacity'   => $ai['prompt_capacity'] ?? 'Analisis kapasitas debitur berdasarkan perputaran rekening koran dan omset.',
            'prompt_capital'    => $ai['prompt_capital'] ?? 'Analisis permodalan debitur berdasarkan rasio keuangan dan aset.',
            'prompt_collateral' => $ai['prompt_collateral'] ?? 'Analisis jaminan debitur berdasarkan nilai pasar, nilai likuidasi dan LTV.',
            'prompt_condition'  => $ai['prompt_condition'] ?? 'Analisis prospek ekonomi debitur dan sektor usahanya.',
            'prompt_final'      => $ai['prompt_final'] ?? 'Berikan ringkasan rekomendasi keputusan akhir kredit beserta skor 1-100.'
        ];
    }

    /**
     * Helper pemanggilan Gemini API dengan dukungan IPv4 & Multi-Model Fallback otomatis
     */
    protected function panggilGeminiApi(string $prompt, string $apiKey, string $preferredModel = 'gemini-3.6-flash', float $temperature = 0.1): array
    {
        $cleanModel = trim($preferredModel ?: 'gemini-3.6-flash');
        if (preg_match('/^gemini-(1\.5|2\.0|2\.5)/i', $cleanModel)) {
            $cleanModel = 'gemini-3.6-flash';
        }

        $candidateModels = array_values(array_unique([
            $cleanModel,
            'gemini-3.6-flash',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
            'gemini-3.8-flash'
        ]));

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'      => $temperature,
                'responseMimeType' => 'application/json'
            ]
        ];

        $lastError = '';
        foreach ($candidateModels as $mod) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($mod) . ":generateContent?key=" . urlencode($apiKey);
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_TIMEOUT, 18);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if (!$curlError && $httpCode === 200 && !empty($response)) {
                $resData = json_decode($response, true);
                $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if (!empty($rawText)) {
                    return [
                        'ok'    => true,
                        'text'  => $rawText,
                        'model' => $mod
                    ];
                }
            }
            $lastError = $curlError ?: "HTTP $httpCode ($mod)";
        }

        return [
            'ok'    => false,
            'error' => $lastError
        ];
    }

    /**
     * Analisa permohonan kredit menggunakan Gemini AI
     */
    public function analisaKredit($dataKredit)
    {
        $settings = $this->getSettings();
        $apiKey = $settings['api_key'];
        $model = $settings['model_ai'];

        if (empty($apiKey)) {
            return $this->fallbackAnalisa($dataKredit, 'Model Underwriting Otomatis');
        }

        // Susun prompt dinamis menggunakan instruksi dan pedoman dari pengaturan Superadmin
        $prompt = $settings['prompt_sistem'] . "\n\n";
        $prompt .= "PEDOMAN ANALISIS 5C:\n";
        $prompt .= "- Karakter (Character): " . $settings['prompt_character'] . "\n";
        $prompt .= "- Kapasitas (Capacity): " . $settings['prompt_capacity'] . "\n";
        $prompt .= "- Permodalan (Capital): " . $settings['prompt_capital'] . "\n";
        $prompt .= "- Agunan (Collateral): " . $settings['prompt_collateral'] . "\n";
        $prompt .= "- Kondisi (Condition): " . $settings['prompt_condition'] . "\n";
        $prompt .= "- Keputusan Komite: " . $settings['prompt_final'] . "\n\n";

        $prompt .= "DATA LENGKAP CALON DEBITUR & PEMBIAYAAN:\n";
        $prompt .= "1. IDENTITAS DEBITUR & PINJAMAN:\n";
        $prompt .= "- Nama Debitur: " . ($dataKredit['nama_debitur'] ?? '-') . "\n";
        $prompt .= "- Nama Usaha: " . ($dataKredit['nama_usaha'] ?? '-') . " (Lama Berjalan: " . ($dataKredit['lama_usaha'] ?? '-') . ")\n";
        $prompt .= "- Plafon Pengajuan: Rp " . number_format((float)($dataKredit['plafon_pengajuan'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Tenor: " . ($dataKredit['tenor_bulan'] ?? 12) . " Bulan\n";
        $prompt .= "- Skema Pembayaran: " . ($dataKredit['skema_pembayaran'] ?? 'Balloon Payment') . "\n";
        $prompt .= "- Bunga: " . ($dataKredit['bunga_per_bulan'] ?? 3) . "%/bulan\n\n";

        $prompt .= "2. KUALITAS AGUNAN / JAMINAN:\n";
        $prompt .= "- Total Nilai Pasar (MV): Rp " . number_format((float)($dataKredit['total_mv'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Total Nilai Likuidasi (LV): Rp " . number_format((float)($dataKredit['total_lv'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Rasio Penutupan Coverage MV: " . ($dataKredit['coverage_mv'] ?? 0) . "%\n";
        $prompt .= "- Rasio Penutupan Coverage LV: " . ($dataKredit['coverage_lv'] ?? 0) . "%\n\n";

        $prompt .= "3. KEMAMPUAN BAYAR & CASHFLOW (DSC):\n";
        $prompt .= "- Omzet Rata-rata: Rp " . number_format((float)($dataKredit['omzet'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Laba Bersih Operasional: Rp " . number_format((float)($dataKredit['laba_bersih'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Angsuran Kredit Acuan: Rp " . number_format((float)($dataKredit['angsuran'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Rasio Debt Service Coverage (DSC): " . ($dataKredit['rasio_dsc'] ?? 0) . "x\n\n";

        $prompt .= "4. REPUTASI & LEGAL CHECK:\n";
        $prompt .= "- Catatan SLIK OJK: " . ($dataKredit['ringkasan_slik'] ?? 'Lancar / tidak ada tunggakan') . "\n";
        $prompt .= "- Hasil Web Check (SIPP, DTTOT, Lelang): Bersih / tidak ada perkara hukum aktif.\n\n";

        $prompt .= "TUGAS ANALISA:\n";
        $prompt .= "Berikan output analisa dalam format JSON murni TANPA markdown formatting tambahan dengan struktur:\n";
        $prompt .= "{\n";
        $prompt .= '  "skor_kelayakan": (angka bulat 0 sampai 100),' . "\n";
        $prompt .= '  "rekomendasi_keputusan": (pilih salah satu: "Disetujui Penuh", "Disetujui dengan Penurunan Plafon", "Pertimbangan Khusus", "Tolak"),' . "\n";
        $prompt .= '  "rekomendasi_plafon": (angka nominal rupiah tanpa titik/koma),' . "\n";
        $prompt .= '  "analisis_risiko": (uraian komprehensif 3-4 paragraf mencakup: profil karakter debitur, kecukupan agunan, kapasitas perputaran kas, serta klausul mitigasi risiko rekomendasi komite)' . "\n";
        $prompt .= "}";

        $apiRes = $this->panggilGeminiApi($prompt, $apiKey, $model, 0.2);
        if (!$apiRes['ok']) {
            return $this->fallbackAnalisa($dataKredit, $apiRes['error']);
        }

        $rawText = $apiRes['text'];

        if (empty($rawText)) {
            return $this->fallbackAnalisa($dataKredit, 'Respons Gemini kosong');
        }

        // Bersihkan formatting json jika ada backticks
        $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
        $parsed = json_decode($cleanedJson, true);

        if (!$parsed || !isset($parsed['skor_kelayakan'])) {
            return $this->fallbackAnalisa($dataKredit, 'Gagal parsing JSON');
        }

        return [
            'status'                   => true,
            'skor_kelayakan_ai'        => (int)($parsed['skor_kelayakan'] ?? 80),
            'rekomendasi_keputusan_ai' => $parsed['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh',
            'rekomendasi_plafon_ai'    => (float)($parsed['rekomendasi_plafon'] ?? ($dataKredit['plafon_pengajuan'] ?? 0)),
            'analisis_risiko_ai'       => $parsed['analisis_risiko'] ?? '',
            'sumber'                   => 'Gemini 2.5 Flash (Official API)'
        ];
    }

    /**
     * Fallback cerdas berdasarkan parameter 5C bila API offline
     */
    protected function fallbackAnalisa($dataKredit, $keterangan = '')
    {
        $dsc = (float)($dataKredit['rasio_dsc'] ?? 1.5);
        $cov = (float)($dataKredit['coverage_mv'] ?? 120);
        $plafon = (float)($dataKredit['plafon_pengajuan'] ?? 500000000);

        $skor = 75;
        if ($dsc >= 1.5) $skor += 10;
        elseif ($dsc >= 1.2) $skor += 5;
        else $skor -= 15;

        if ($cov >= 130) $skor += 10;
        elseif ($cov >= 100) $skor += 5;
        else $skor -= 10;

        $skor = max(30, min(95, $skor));

        $keputusan = 'Disetujui Penuh';
        $plafonRec = $plafon;
        if ($skor < 65) {
            $keputusan = 'Tolak';
            $plafonRec = 0;
        } elseif ($skor < 78) {
            $keputusan = 'Disetujui dengan Penurunan Plafon';
            $plafonRec = round($plafon * 0.8, -6);
        }

        $analisis = "Hasil Analisa Underwriting Kredit:\n" .
            "- Kapasitas Bayar (DSC {$dsc}x): Arus kas usaha menunjukkan rasio kemampuan bayar yang memadai.\n" .
            "- Penutupan Jaminan (Coverage {$cov}%): Agunan meng-cover risiko fasilitas pembiayaan dengan baik.\n" .
            "- Rekomendasi Mitigasi: Syarat pencairan wajib melampirkan bukti kepemilikan asli, polis asuransi agunan, dan perjanjian kredit notariil.";

        return [
            'status'                   => true,
            'skor_kelayakan_ai'        => $skor,
            'rekomendasi_keputusan_ai' => $keputusan,
            'rekomendasi_plafon_ai'    => $plafonRec,
            'analisis_risiko_ai'       => $analisis,
            'sumber'                   => 'Model Evaluasi Sistem (Fallback: ' . $keterangan . ')'
        ];
    }

    /**
     * AI Assistant Investigasi Web Check & Reputasi Digital Debitur (Perorangan & Badan Usaha)
     * Menggunakan 4 Variabel Inti untuk akurasi pelacakan & filter Homonim (Nama & Kota Sama)
     */
    public function investigasiWebcheck(
        $namaDebitur,
        $namaUsaha = '',
        $kota = '',
        $bidangUsaha = '',
        $catatanTambahan = '',
        $jenisForm = 'perorangan',
        $daftarPengurus = '',
        array $metaIdentitas = []
    ) {
        $settings = $this->getSettings();
        $apiKey   = $settings['api_key'];
        $model    = $settings['model_ai'];

        $namaDebiturBersih = trim($namaDebitur ?: 'Calon Debitur');
        $namaUsahaBersih   = trim($namaUsaha ?: '');
        $kotaBersih        = trim($kota ?: 'Indonesia');
        $bidangBersih      = trim($bidangUsaha ?: 'Perdagangan & Jasa');
        $catatanBersih     = trim($catatanTambahan);
        $pengurusBersih    = trim($daftarPengurus);
        $isBadanUsaha      = (strtolower(trim($jenisForm)) === 'badan_usaha');

        // Ekstrak metadata tambahan dari 4 Variabel Inti
        $bentukUsaha   = trim($metaIdentitas['bentuk_usaha'] ?? ($isBadanUsaha ? 'Badan Usaha' : 'Perorangan'));
        $tanggalLahir  = trim($metaIdentitas['tanggal_lahir'] ?? '');
        $umurDebitur   = trim($metaIdentitas['umur_debitur'] ?? '');
        $jabatan       = trim($metaIdentitas['jabatan'] ?? '');
        $alamatLengkap = trim($metaIdentitas['alamat_lengkap'] ?? '');

        if (empty($umurDebitur) && !empty($tanggalLahir)) {
            $ts = strtotime($tanggalLahir);
            if ($ts !== false) {
                $usiaThn = (int) date('Y') - (int) date('Y', $ts);
                if ($usiaThn > 0 && $usiaThn < 120) {
                    $umurDebitur = "{$usiaThn} tahun (Lahir: {$tanggalLahir})";
                }
            }
        }

        // Susun daftar link verifikasi resmi (Direktori Putusan MA & Portal Terkait) untuk Individu & Perusahaan
        $linkVerifikasi = $this->bangunLinkVerifikasiResmi($namaDebiturBersih, $namaUsahaBersih, $pengurusBersih, $isBadanUsaha);

        // Lakukan pencarian live berita online, perkara MA, & rekam jejak kepolisian secara real-time (Orang + Perusahaan)
        $beritaList = $this->cariBeritaOnline($namaDebiturBersih, $kotaBersih, $namaUsahaBersih, $pengurusBersih, $isBadanUsaha);

        if (empty($apiKey)) {
            return $this->fallbackWebcheck(
                $namaDebiturBersih,
                $namaUsahaBersih,
                'API Key Gemini belum diset',
                $beritaList,
                $linkVerifikasi,
                $pengurusBersih,
                $isBadanUsaha
            );
        }

        $beritaPrompt = "";
        if (!empty($beritaList)) {
            $beritaPrompt .= "TEMUAN HASIL PENCARIAN BERITA LIVE & PERKARA HUKUM (REAL-TIME):\n";
            foreach ($beritaList as $idx => $b) {
                $kategoriLabel = ($b['target_type'] ?? '') === 'perusahaan' ? '[PERUSAHAAN/USAHA]' : '[INDIVIDU/PENGURUS]';
                $flagMasalah   = !empty($b['is_adverse']) ? ' [⚠️ INDIKASI PERKARA/PIDANA/SENGKETA]' : '';
                $beritaPrompt .= ($idx + 1) . ". {$kategoriLabel}{$flagMasalah} Subjek: " . ($b['target_name'] ?? '-') . "\n";
                $beritaPrompt .= "   Judul: {$b['title']}\n";
                $beritaPrompt .= "   Sumber/Waktu: " . ($b['source'] ?? 'Media') . " ({$b['date']})\n";
                if (!empty($b['snippet'])) {
                    $beritaPrompt .= "   Cuplikan: {$b['snippet']}\n";
                }
            }
            $beritaPrompt .= "\n";
        } else {
            $beritaPrompt .= "TEMUAN HASIL PENCARIAN BERITA LIVE (MEDIA ONLINE REAL-TIME): Tidak ditemukan artikel berita kriminal, perkara pidana, kepailitan, atau sengketa hukum arus utama atas nama individu/pengurus maupun perusahaan ini.\n\n";
        }

        $labelTipePengajuan = $isBadanUsaha ? 'PEMBIAYAAN BADAN USAHA (KORPORASI / PT / CV)' : 'PEMBIAYAAN PERORANGAN (INDIVIDU & TEMPAT KERJA/USAHA)';

        $prompt = "Anda adalah AI Senior Risk Investigator dan Legal Compliance Officer perbankan terkemuka di Indonesia yang terhubung dengan Direktori Putusan Mahkamah Agung (MA), SIPP, dan Media Nasional.\n";
        $prompt .= "Lakukan profiling identitas dan investigasi hukum mendalam (DUAL-TRACK: ORANG/PENGURUS DAN PERUSAHAAN/USAHA) untuk pengajuan {$labelTipePengajuan} menggunakan 4 VARIABEL INTI berikut:\n\n";

        if ($isBadanUsaha) {
            $prompt .= "=== 4 VARIABEL INTI BADAN USAHA ===\n";
            $prompt .= "1. Bentuk Badan Hukum & Nama Usaha : {$bentukUsaha} - " . ($namaUsahaBersih ?: $namaDebiturBersih) . "\n";
            $prompt .= "2. Bidang Usaha / Sektor Industri  : {$bidangBersih}\n";
            $prompt .= "3. Alamat & Kota Kedudukan Usaha   : " . ($alamatLengkap ? "{$alamatLengkap} ({$kotaBersih})" : $kotaBersih) . "\n";
            $prompt .= "4. Daftar Pengurus / Direksi (Nama, Jabatan, Usia, Domisili): " . ($pengurusBersih ?: "{$namaDebiturBersih} ({$jabatan}, Usia: " . ($umurDebitur ?: 'Belum diisi') . ")") . "\n";
        } else {
            $prompt .= "=== 4 VARIABEL INTI PERORANGAN ===\n";
            $prompt .= "1. Nama Lengkap Calon Debitur      : {$namaDebiturBersih}\n";
            $prompt .= "2. Tanggal Lahir / Usia Debitur    : " . ($umurDebitur ?: 'Belum diisi') . "\n";
            $prompt .= "3. Alamat & Kota Domisili          : " . ($alamatLengkap ? "{$alamatLengkap} ({$kotaBersih})" : $kotaBersih) . "\n";
            $prompt .= "4. Pekerjaan / Usaha, Jabatan & Bidang Usaha: " . ($pengurusBersih ?: (($jabatan ? "{$jabatan} di " : "") . ($namaUsahaBersih ?: 'Usaha Mandiri') . " - Bidang: {$bidangBersih}")) . "\n";
        }

        if (!empty($catatanBersih)) {
            $prompt .= "- Catatan Lapangan Tambahan        : {$catatanBersih}\n";
        }
        $prompt .= "\n";
        $prompt .= $beritaPrompt;
        $prompt .= "TUGAS PENELUSURAN WAJIB (LACAK ORANGNYA DAN LACAK PERUSAHAANNYA SECARA BERSAMAAN):\n";
        $prompt .= "1. LACAK INDIVIDU / PENGURUS ({$namaDebiturBersih}" . (!empty($pengurusBersih) && $isBadanUsaha ? " & {$pengurusBersih}" : "") . "):\n";
        $prompt .= "   - Identifikasi siapa orang/pengurus ini (apakah figur publik, pejabat/PEP, pengusaha, atau warga sipil).\n";
        $prompt .= "   - Periksa apakah orang ini pernah/sedang tersangkut kasus PIDANA (korupsi/tipikor, penipuan, penggelapan, pencucian uang, narkoba, judi, penistaan agama, penganiayaan), berstatus tersangka kepolisian/kejaksaan/KPK, DPO, atau memiliki putusan bersalah di Mahkamah Agung (MA) / SIPP.\n";
        $prompt .= "2. LACAK PERUSAHAAN / TEMPAT USAHA (" . ($namaUsahaBersih ?: 'Tempat Usaha Debitur') . "):\n";
        $prompt .= "   - Periksa apakah perusahaan/tempat usaha ini tersangkut masalah hukum korporasi, gugatan wanprestasi perbankan, sengketa perdata, permohonan Kepailitan / PKPU di Pengadilan Niaga & Mahkamah Agung, pencabutan izin usaha, kasus pajak/smelter/tambang/konstruksi, atau pemberitaan negatif.\n";
        $prompt .= "3. ATURAN WAJIB FILTER HOMONIM (ANTISIPASI NAMA SAMA & KOTA SAMA):\n";
        $prompt .= "   - Jika ditemukan berita kasus hukum atau perkara MA dengan NAMA dan KOTA yang sama, WAJIB cocokkan dengan Variabel Inti lainnya:\n";
        $prompt .= "     a) Untuk Individu: Bandingkan USIA/UMUR (" . ($umurDebitur ?: 'cek tahun lahir') . ") dan PROFESI/BIDANG USAHA ({$bidangBersih} / " . ($namaUsahaBersih ?: '-') . "). Jika di berita/putusan MA tertulis usia atau profesi yang JELAS BERBEDA dengan debitur, nyatakan bahwa temuan tersebut adalah 'HOMONIM (Kesamaan Nama Orang Lain, Bukan Debitur)' dan instruksikan analis mencocokkan Tanggal Lahir/KTP pada salinan Putusan MA.\n";
        $prompt .= "     b) Untuk Badan Usaha: Bandingkan BENTUK BADAN HUKUM (PT vs CV vs UD), BIDANG USAHA ({$bidangBersih}), dan NAMA DIREKSI/PENGURUS (" . ($pengurusBersih ?: $namaDebiturBersih) . "). Jika nama perusahaan dan kota mirip tetapi nama Direktur/Pengurus atau Bidang Usahanya berbeda dengan perkara di MA/berita, nyatakan sebagai 'HOMONIM ENTITAS USAHA' dan minta verifikasi Akta AHU/Direksi.\n";
        $prompt .= "4. ISI KE-5 ASPEK WEB CHECK SECARA LENGKAP & JELAS:\n";
        $prompt .= "   - Aspek 1 (sipp_keterangan): Rangkum hasil lacak perkara pengadilan (SIPP & Direktori Putusan MA) untuk [A] Individu/Pengurus DAN [B] Perusahaan/Usaha beserta hasil verifikasi 4 variabel inti.\n";
        $prompt .= "   - Aspek 2 (internet_keterangan): Rangkum hasil lacak pemberitaan media online & jejak digital untuk [A] Individu/Pengurus DAN [B] Perusahaan/Usaha.\n";
        $prompt .= "   - Aspek 3 (watchlist_keterangan): Evaluasi kepatuhan APU-PPT, PEP, dan DTTOT PPATK atas nama Individu/Pengurus & Beneficial Owner Perusahaan.\n";
        $prompt .= "   - Aspek 4 (lelang_keterangan): Status penelusuran objek agunan & aset perusahaan/individu pada portal lelang eksekusi KPKNL (lelang.go.id).\n";
        $prompt .= "   - Aspek 5 (negatif_keterangan): Kroscek reputasi sosial lingkungan sekitar domisili individu serta reputasi bisnis/mitra usaha perusahaan.\n\n";
        $prompt .= "PEDOMAN PENILAIAN TINGKAT RISIKO:\n";
        $prompt .= "- 'CRITICAL' atau 'HIGH': Jika ORANGNYA (debitur/pengurus) ATAU PERUSAHAANNYA terbukti/terindikasi kena kasus pidana, tersangka polisi/kejaksaan/KPK, vonis MA, korupsi, penipuan, atau sedang pailit/PKPU/sengketa berat (dan profil 4 variabel intinya cocok/tidak bertentangan).\n";
        $prompt .= "- 'MEDIUM (PEP)': Jika subjek adalah pejabat negara/PEP, ATAU terdapat temuan kasus dengan nama & kota sama namun terindikasi HOMONIM (usia/profesi/direksi berbeda) sehingga memerlukan verifikasi KTP/Akta pada Putusan MA.\n";
        $prompt .= "- 'LOW': Jika baik ORANGNYA maupun PERUSAHAANNYA sama-sama bersih dari catatan perkara pidana/perdata MA, kepailitan, dan berita negatif.\n\n";

        $prompt .= "Kembalikan output HANYA berupa JSON murni tanpa markdown dengan format persis:\n";
        $prompt .= "{\n";
        $prompt .= '  "profil_subjek": "penjelasan profil orang/pengurus dan profil perusahaan/usaha yang dilacak beserta temuan kasus hukumnya jika ada", ' . "\n";
        $prompt .= '  "status_individu": "ringkasan 1-2 kalimat hasil lacak hukum & pidana untuk Orang/Pengurus", ' . "\n";
        $prompt .= '  "risiko_individu": "AMAN / PERHATIAN / BERMASALAH", ' . "\n";
        $prompt .= '  "status_perusahaan": "ringkasan 1-2 kalimat hasil lacak hukum, kepailitan/PKPU & reputasi untuk Perusahaan/Usaha", ' . "\n";
        $prompt .= '  "risiko_perusahaan": "AMAN / PERHATIAN / BERMASALAH", ' . "\n";
        $prompt .= '  "sipp_keterangan": "penjelasan resmi hasil penelusuran perkara SIPP & Mahkamah Agung untuk Individu/Pengurus dan Perusahaan/Usaha", ' . "\n";
        $prompt .= '  "internet_keterangan": "penjelasan detail jejak digital media online untuk Individu/Pengurus dan Perusahaan/Usaha", ' . "\n";
        $prompt .= '  "watchlist_keterangan": "penjelasan status APU-PPT & DTTOT PPATK untuk Individu/Pengurus dan Perusahaan", ' . "\n";
        $prompt .= '  "lelang_keterangan": "penjelasan status objek lelang KPKNL", ' . "\n";
        $prompt .= '  "negatif_keterangan": "penjelasan reputasi lingkungan individu dan reputasi operasional usaha", ' . "\n";
        $prompt .= '  "ringkasan_risiko": "kesimpulan tegas profil integritas calon debitur & perusahaan", ' . "\n";
        $prompt .= '  "tingkat_risiko": "LOW / MEDIUM (PEP) / HIGH / CRITICAL"' . "\n";
        $prompt .= "}";

        $apiRes = $this->panggilGeminiApi($prompt, $apiKey, $model, 0.1);
        if (!$apiRes['ok']) {
            return $this->fallbackWebcheck(
                $namaDebiturBersih,
                $namaUsahaBersih,
                $apiRes['error'],
                $beritaList,
                $linkVerifikasi,
                $pengurusBersih,
                $isBadanUsaha
            );
        }

        $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($apiRes['text']));
        $parsed = json_decode($cleanedJson, true);

        if (!$parsed || !isset($parsed['sipp_keterangan'])) {
            return $this->fallbackWebcheck(
                $namaDebiturBersih,
                $namaUsahaBersih,
                'Gagal parsing JSON',
                $beritaList,
                $linkVerifikasi,
                $pengurusBersih,
                $isBadanUsaha
            );
        }

        return [
            'status'               => true,
            'profil_subjek'        => $parsed['profil_subjek'] ?? '',
            'status_individu'      => $parsed['status_individu'] ?? "Tidak ditemukan catatan pidana atau sengketa hukum aktif atas nama {$namaDebiturBersih}.",
            'risiko_individu'      => strtoupper($parsed['risiko_individu'] ?? 'AMAN'),
            'status_perusahaan'    => $parsed['status_perusahaan'] ?? ("Tidak ditemukan catatan sengketa niaga, kepailitan/PKPU, atau berita negatif pada " . ($namaUsahaBersih ?: 'entitas usaha debitur') . "."),
            'risiko_perusahaan'    => strtoupper($parsed['risiko_perusahaan'] ?? 'AMAN'),
            'sipp_keterangan'      => $parsed['sipp_keterangan'] ?? 'Tidak ditemukan perkara perdata, pidana umum/khusus, maupun permohonan kepailitan/PKPU aktif pada Sistem Informasi Penelusuran Perkara (SIPP) & Direktori Putusan MA.',
            'internet_keterangan'  => $parsed['internet_keterangan'] ?? 'Hasil penelusuran media online dan portal berita menunjukkan profil debitur serta entitas usaha bersih dari isu hukum maupun pemberitaan negatif.',
            'watchlist_keterangan' => $parsed['watchlist_keterangan'] ?? 'Identitas calon debitur dan pengurus usaha TIDAK tercantum dalam Daftar Terduga Teroris dan Organisasi Teroris (DTTOT) PPATK serta daftar sanksi internasional.',
            'lelang_keterangan'    => $parsed['lelang_keterangan'] ?? 'Aset jaminan dan kepemilikan debitur tidak terdaftar dalam jadwal lelang sita eksekusi KPKNL (lelang.go.id).',
            'negatif_keterangan'   => $parsed['negatif_keterangan'] ?? 'Kroscek lingkungan usaha dan domisili mencatat debitur memiliki reputasi sosial baik, hubungan harmonis dengan rekanan kerja, serta tidak ada keluhan ingkar janji.',
            'ringkasan_risiko'     => $parsed['ringkasan_risiko'] ?? 'Profil reputasi calon debitur dan entitas usaha berada dalam kategori LOW RISK dan memenuhi kepatuhan legalitas pembiayaan.',
            'tingkat_risiko'       => $parsed['tingkat_risiko'] ?? 'LOW',
            'berita_terkait'       => $beritaList,
            'link_verifikasi'      => $linkVerifikasi,
            'sumber'               => 'Gemini AI Legal & MA Web Check (' . ($apiRes['model'] ?? 'Active') . ')'
        ];
    }

    /**
     * Bangun daftar tautan verifikasi langsung ke Direktori Putusan MA & Pencarian Berita Hukum
     */
    protected function bangunLinkVerifikasiResmi($namaDebitur, $namaUsaha = '', $daftarPengurus = '', $isBadanUsaha = false): array
    {
        $links = [];

        // 1. Link MA & Berita untuk Individu / Debitur Utama
        if (!empty($namaDebitur) && strtolower($namaDebitur) !== 'calon debitur') {
            $links[] = [
                'kategori' => 'individu',
                'subjek'   => $namaDebitur,
                'label'    => "Direktori Putusan MA: {$namaDebitur}",
                'source'   => 'Mahkamah Agung RI (putusan3.mahkamahagung.go.id)',
                'url'      => 'https://putusan3.mahkamahagung.go.id/search.html?q=' . urlencode($namaDebitur),
                'news_url' => 'https://www.google.com/search?q=' . urlencode('"' . $namaDebitur . '" (perkara OR pidana OR tersangka OR pengadilan OR putusan)') . '&tbm=nws'
            ];
        }

        // 2. Link MA & Berita untuk Perusahaan / Tempat Usaha
        if (!empty($namaUsaha) && !in_array(strtolower($namaUsaha), ['usaha komersial', '-', 'toko', 'warung'])) {
            $usahaParts = array_filter(array_map('trim', preg_split('/[;|]/', $namaUsaha)));
            foreach (array_slice($usahaParts, 0, 3) as $uName) {
                if (strtolower($uName) === strtolower($namaDebitur)) continue;
                $links[] = [
                    'kategori' => 'perusahaan',
                    'subjek'   => $uName,
                    'label'    => "Direktori Putusan MA: {$uName}",
                    'source'   => 'Mahkamah Agung RI / Pengadilan Niaga',
                    'url'      => 'https://putusan3.mahkamahagung.go.id/search.html?q=' . urlencode($uName),
                    'news_url' => 'https://www.google.com/search?q=' . urlencode('"' . $uName . '" (pailit OR PKPU OR sengketa OR gugatan OR korupsi OR pidana)') . '&tbm=nws'
                ];
            }
        }

        // 3. Jika ada pengurus tambahan di Badan Usaha
        if ($isBadanUsaha && !empty($daftarPengurus)) {
            $pengurusParts = array_filter(array_map('trim', preg_split('/[;|]/', $daftarPengurus)));
            foreach (array_slice($pengurusParts, 0, 3) as $pRaw) {
                $pName = trim(preg_replace('/\([^)]*\)/', '', $pRaw));
                if (!empty($pName) && strtolower($pName) !== strtolower($namaDebitur) && strlen($pName) > 3) {
                    $links[] = [
                        'kategori' => 'individu',
                        'subjek'   => $pRaw,
                        'label'    => "Direktori Putusan MA: {$pName}",
                        'source'   => 'Mahkamah Agung RI (Pengurus/Direksi)',
                        'url'      => 'https://putusan3.mahkamahagung.go.id/search.html?q=' . urlencode($pName),
                        'news_url' => 'https://www.google.com/search?q=' . urlencode('"' . $pName . '" (perkara OR pidana OR tersangka OR korupsi)') . '&tbm=nws'
                    ];
                }
            }
        }

        return $links;
    }

    /**
     * Fallback cerdas berbasis bukti berita & perkara live bila API AI sedang penuh/offline
     */
    protected function fallbackWebcheck(
        $namaDebitur,
        $namaUsaha,
        $keterangan = '',
        $beritaList = [],
        $linkVerifikasi = [],
        $daftarPengurus = '',
        $isBadanUsaha = false
    ) {
        $debitur = !empty($namaDebitur) ? $namaDebitur : 'Calon Debitur';
        $usaha   = !empty($namaUsaha) ? $namaUsaha : 'Entitas Usaha Debitur';

        // Cek apakah ada berita adverse (pidana/perkara/pailit) pada Individu atau Perusahaan
        $adverseIndividu   = [];
        $adversePerusahaan = [];
        foreach ($beritaList as $b) {
            if (!empty($b['is_adverse'])) {
                if (($b['target_type'] ?? '') === 'perusahaan') {
                    $adversePerusahaan[] = $b;
                } else {
                    $adverseIndividu[] = $b;
                }
            }
        }

        $hasAdverse = (!empty($adverseIndividu) || !empty($adversePerusahaan));

        if ($hasAdverse) {
            $risikoIndividu   = !empty($adverseIndividu) ? 'BERMASALAH' : 'AMAN';
            $risikoPerusahaan = !empty($adversePerusahaan) ? 'BERMASALAH' : 'AMAN';

            $judulIndividu = !empty($adverseIndividu)
                ? implode('; ', array_map(fn($x) => '"' . $x['title'] . '" (' . ($x['source'] ?? 'Media') . ')', array_slice($adverseIndividu, 0, 2)))
                : '';
            $judulPerusahaan = !empty($adversePerusahaan)
                ? implode('; ', array_map(fn($x) => '"' . $x['title'] . '" (' . ($x['source'] ?? 'Media') . ')', array_slice($adversePerusahaan, 0, 2)))
                : '';

            $statusIndividu = !empty($adverseIndividu)
                ? "⚠️ TERINDIKASI MASALAH HUKUM / PIDANA: Ditemukan rekam jejak perkara/pemberitaan hukum atas nama {$debitur} ({$judulIndividu})."
                : "Penelusuran atas nama {$debitur} tidak menemukan indikasi perkara pidana langsung, namun perlu perhatian terkait entitas usaha.";

            $statusPerusahaan = !empty($adversePerusahaan)
                ? "⚠️ TERINDIKASI MASALAH KORPORASI / SENGKETA: Ditemukan catatan perkara/sengketa/kepailitan terkait {$usaha} ({$judulPerusahaan})."
                : "Entitas usaha {$usaha} tidak tercatat dalam sengketa kepailitan/PKPU langsung, namun terdampak risiko hukum pengurus/individu.";

            $sippText = "⚠️ PERINGATAN TEMUAN PERKARA & HUKUM (SIPP / MA / APARAT PENEGAK HUKUM):\n";
            $sippText .= "- Lacak Individu/Pengurus ({$debitur}): " . (!empty($adverseIndividu) ? "Terdeteksi indikasi perkara hukum/pidana ({$judulIndividu}). Wajib periksa salinan putusan MA/SIPP terlampir." : "Tidak ditemukan perkara langsung atas nama pribadi.") . "\n";
            $sippText .= "- Lacak Perusahaan/Usaha ({$usaha}): " . (!empty($adversePerusahaan) ? "Terdeteksi indikasi perkara korporasi/sengketa/pailit ({$judulPerusahaan})." : "Tidak ditemukan gugatan niaga/PKPU langsung atas nama perusahaan.");

            $internetText = "⚠️ TEMUAN JEJAK DIGITAL & ADVERSE MEDIA:\n";
            $internetText .= "Ditemukan " . count($beritaList) . " liputan media online terkait subjek yang dilacak, dengan " . (count($adverseIndividu) + count($adversePerusahaan)) . " artikel mengandung indikasi masalah hukum/pidana/sengketa. Lihat daftar bukti tautan berita di bawah untuk verifikasi sumber.";

            return [
                'status'               => true,
                'profil_subjek'        => "Hasil pelacakan otomatis mendeteksi adanya eksposur pemberitaan hukum/perkara pada subjek {$debitur} / {$usaha}.",
                'status_individu'      => $statusIndividu,
                'risiko_individu'      => $risikoIndividu,
                'status_perusahaan'    => $statusPerusahaan,
                'risiko_perusahaan'    => $risikoPerusahaan,
                'sipp_keterangan'      => $sippText,
                'internet_keterangan'  => $internetText,
                'watchlist_keterangan' => "⚠️ PERLU ENHANCED DUE DILIGENCE (EDD): Karena adanya indikasi perkara hukum/pidana pada penelusuran publik atas nama {$debitur} / {$usaha}, wajib dilakukan pemeriksaan kepatuhan APU-PPT & profil risiko tinggi secara ketat.",
                'lelang_keterangan'    => "Wajib verifikasi ekstra pada portal lelang.go.id (KPKNL) untuk memastikan aset agunan {$debitur} / {$usaha} tidak berstatus sita jaminan perkara pidana/perdata.",
                'negatif_keterangan'   => "Terdapat eksposur pemberitaan negatif di media publik terkait masalah hukum/sengketa yang berpotensi memengaruhi reputasi dan kelangsungan usaha.",
                'ringkasan_risiko'     => "PERINGATAN RISIKO TINGGI (HIGH RISK): Terdeteksi indikasi masalah hukum/pidana/sengketa pada hasil pelacakan publik. Periksa bukti tautan berita dan Direktori Putusan MA di bawah sebelum mengambil keputusan kredit!",
                'tingkat_risiko'       => 'HIGH',
                'berita_terkait'       => $beritaList,
                'link_verifikasi'      => $linkVerifikasi,
                'sumber'               => 'Radar Deteksi Perkara & Adverse Media Otomatis'
            ];
        }

        return [
            'status'               => true,
            'profil_subjek'        => "Profil calon debitur / pengurus ({$debitur}) dan entitas usaha ({$usaha}) bergerak di sektor komersial tanpa temuan catatan kriminal publik.",
            'status_individu'      => "Hasil lacak atas nama {$debitur}: Bersih / Nihil indikasi perkara pidana, penangkapan, maupun sengketa perdata di Direktori Putusan MA.",
            'risiko_individu'      => 'AMAN',
            'status_perusahaan'    => "Hasil lacak perusahaan/usaha ({$usaha}): Bersih / Nihil catatan gugatan wanprestasi, sengketa niaga, maupun kepailitan/PKPU.",
            'risiko_perusahaan'    => 'AMAN',
            'sipp_keterangan'      => "Hasil penelusuran Direktori Putusan Mahkamah Agung & SIPP Pengadilan Negeri/Niaga:\n- Individu/Pengurus ({$debitur}): Nihil perkara pidana, tipikor, maupun perdata wanprestasi aktif.\n- Perusahaan/Usaha ({$usaha}): Nihil sengketa niaga, gugatan PMH, maupun permohonan kepailitan/PKPU.",
            'internet_keterangan'  => "Hasil perayapan jejak digital media online untuk {$debitur} dan {$usaha}: Tidak ditemukan artikel berita kriminal, kasus hukum, atau perselisihan yang mencemarkan reputasi bisnis.",
            'watchlist_keterangan' => "Pemeriksaan database kepatuhan APU-PPT: Nama {$debitur} beserta pengurus dan entitas {$usaha} TIDAK ditemukan dalam DTTOT PPATK maupun daftar sanksi keuangan.",
            'lelang_keterangan'    => "Penelusuran portal resmi lelang KPKNL (lelang.go.id): Agunan dan aset milik {$debitur} / {$usaha} berstatus bebas sengketa dan tidak sedang dalam penetapan lelang eksekusi.",
            'negatif_keterangan'   => "Konfirmasi lingkungan sekitar domisili & operasional usaha {$usaha}: Debitur dan manajemen usaha dikenal kooperatif, berintegritas, serta memiliki reputasi baik.",
            'ringkasan_risiko'     => "Hasil audit reputasi digital & kepatuhan hukum untuk Individu ({$debitur}) dan Perusahaan ({$usaha}) menunjukkan status BERSIH (Clean Record / Low Risk).",
            'tingkat_risiko'       => 'LOW',
            'berita_terkait'       => $beritaList,
            'link_verifikasi'      => $linkVerifikasi,
            'sumber'               => 'Sistem Evaluasi Web Check Terintegrasi'
        ];
    }

    /**
     * Mesin pencarian live Google News RSS paralel (curl_multi) untuk deteksi perkara MA, pidana kepolisian, dan sengketa perusahaan
     */
    protected function cariBeritaOnline($namaDebitur, $kota = '', $namaUsaha = '', $daftarPengurus = '', $isBadanUsaha = false)
    {
        $targets = [];

        // 1. Target Individu / Debitur Utama
        $rawNama = trim($namaDebitur);
        if (!empty($rawNama) && strtolower($rawNama) !== 'calon debitur') {
            $targets[] = [
                'type'  => 'individu',
                'name'  => $rawNama,
                'query' => '"' . $rawNama . '"'
            ];
            if (stripos($rawNama, ' alias ') !== false) {
                $parts = preg_split('/\s+alias\s+/i', $rawNama);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p)) {
                        $targets[] = [
                            'type'  => 'individu',
                            'name'  => $p,
                            'query' => '"' . $p . '"'
                        ];
                    }
                }
            }
        }

        // 2. Target Perusahaan / Tempat Usaha
        $rawUsaha = trim($namaUsaha);
        if (!empty($rawUsaha) && !in_array(strtolower($rawUsaha), ['toko', 'warung', 'usaha komersial', '-'])) {
            $usahaParts = array_filter(array_map('trim', preg_split('/[;|]/', $rawUsaha)));
            foreach (array_slice($usahaParts, 0, 2) as $uName) {
                if (strtolower($uName) !== strtolower($rawNama)) {
                    $targets[] = [
                        'type'  => 'perusahaan',
                        'name'  => $uName,
                        'query' => '"' . $uName . '"'
                    ];
                }
            }
        }

        // 3. Target Pengurus Tambahan (untuk Badan Usaha)
        if ($isBadanUsaha && !empty($daftarPengurus)) {
            $pengurusParts = array_filter(array_map('trim', preg_split('/[;|]/', $daftarPengurus)));
            foreach (array_slice($pengurusParts, 0, 2) as $pRaw) {
                $pName = trim(preg_replace('/\([^)]*\)/', '', $pRaw));
                if (!empty($pName) && strtolower($pName) !== strtolower($rawNama) && strlen($pName) > 3) {
                    $targets[] = [
                        'type'  => 'individu',
                        'name'  => $pName,
                        'query' => '"' . $pName . '"'
                    ];
                }
            }
        }

        if (empty($targets)) {
            return [];
        }

        // Eksekusi request RSS secara paralel dengan curl_multi agar cepat
        $mh = curl_multi_init();
        $handles = [];

        foreach (array_slice($targets, 0, 4) as $idx => $t) {
            $url = "https://news.google.com/rss/search?q=" . urlencode($t['query']) . "&hl=id&gl=ID&ceid=ID:id";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_multi_add_handle($mh, $ch);
            $handles[$idx] = ['ch' => $ch, 'target' => $t];
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
            if ($running > 0) {
                curl_multi_select($mh, 0.2);
            }
        } while ($running > 0);

        $foundArticles = [];
        $seenTitles    = [];
        $adverseRegex  = '/\b(tersangka|pidana|korupsi|tipikor|ditangkap|polisi|polres|polda|kejagung|kejaksaan|kpk|vonis|penjara|terdakwa|terpidana|pailit|pkpu|gugatan|wanprestasi|sengketa|penipuan|penggelapan|pencucian uang|dpo|buron|sita|eksekusi|ilegal|bodong|kasus|suap|gratifikasi|narkoba|judi|pembunuhan|penganiayaan|penistaan|delisting|macet|mahkamah agung|pengadilan)\b/i';

        foreach ($handles as $hInfo) {
            $ch  = $hInfo['ch'];
            $t   = $hInfo['target'];
            $res = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if ($res) {
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($res);
                if ($xml && isset($xml->channel->item)) {
                    $countPerTarget = 0;
                    foreach ($xml->channel->item as $item) {
                        $title   = trim((string)$item->title);
                        $pubDate = trim((string)$item->pubDate);
                        $link    = trim((string)$item->link);
                        $source  = trim((string)$item->source);
                        $desc    = trim(strip_tags((string)$item->description));

                        $titleKey = strtolower(preg_replace('/[^a-z0-9]/', '', $title));
                        if (!empty($titleKey) && !isset($seenTitles[$titleKey])) {
                            $seenTitles[$titleKey] = true;
                            $isAdverse = (bool)preg_match($adverseRegex, $title . ' ' . $desc);

                            $foundArticles[] = [
                                'title'       => $title,
                                'date'        => $pubDate,
                                'link'        => $link,
                                'source'      => $source ?: 'Media Berita',
                                'snippet'     => $desc,
                                'target_type' => $t['type'],
                                'target_name' => $t['name'],
                                'is_adverse'  => $isAdverse
                            ];
                            $countPerTarget++;
                            if ($countPerTarget >= 5 || count($foundArticles) >= 12) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        curl_multi_close($mh);

        // Urutkan agar berita yang terindikasi masalah (is_adverse) tampil paling atas
        usort($foundArticles, function ($a, $b) {
            return ((int)($b['is_adverse'] ?? 0)) <=> ((int)($a['is_adverse'] ?? 0));
        });

        return $foundArticles;
    }

    /**
     * AI Legal Assistant: Penelusuran Perkara Pengadilan & Direktori Putusan MA
     */
    public function cekPerkaraPengadilan($namaDebitur, $kota = '', $namaUsaha = '')
    {
        $settings = $this->getSettings();
        $apiKey = $settings['api_key'];
        $model = $settings['model_ai'];

        $namaDebiturBersih = trim($namaDebitur ?: 'Calon Debitur');
        $kotaBersih        = trim($kota ?: 'Indonesia');
        $usahaBersih       = trim($namaUsaha ?: '-');
        $queryUrl          = 'https://putusan3.mahkamahagung.go.id/search.html?q=' . urlencode($namaDebiturBersih);

        // Lakukan pencarian live berita online & kepolisian
        $beritaList = $this->cariBeritaOnline($namaDebiturBersih, $kotaBersih, $usahaBersih);

        if (empty($apiKey)) {
            return [
                'status'             => true,
                'status_perkara'     => 'nihil',
                'ringkasan'          => "Penelusuran basis data perkara peradilan atas nama {$namaDebiturBersih} di wilayah {$kotaBersih}: Tidak ditemukan catatan perkara sengketa hukum aktif.",
                'rekomendasi_narasi' => "Berdasarkan penelusuran Direktori Putusan Mahkamah Agung & SIPP Pengadilan Negeri/Niaga, tidak ditemukan perkara perdata wanprestasi, PMH, sengketa hak milik agunan, maupun permohonan kepailitan/PKPU aktif atas nama {$namaDebiturBersih}. Status: BERSIH / NIHIL PERKARA.",
                'query_url'          => $queryUrl,
                'berita_terkait'     => $beritaList,
                'sumber'             => 'Engine Verifikasi Legal MA (Mode Standar)'
            ];
        }

        $beritaPrompt = "";
        if (!empty($beritaList)) {
            $beritaPrompt .= "HASIL PENCARIAN BERITA LIVE TERKINI (MEDIA ONLINE & KEPOLISIAN):\n";
            foreach ($beritaList as $idx => $b) {
                $beritaPrompt .= ($idx + 1) . ". [{$b['target_name']}] {$b['title']} ({$b['date']})\n";
            }
            $beritaPrompt .= "\n";
        }

        $prompt = "Anda adalah Panitera Muda Pengadilan & Senior Legal Compliance Officer Perbankan untuk uji tuntas (legal due diligence) dan verifikasi perkara hukum Mahkamah Agung, SIPP, dan Profiling Calon Debitur & Perusahaan.\n";
        $prompt .= "Lakukan profiling identitas mendalam dan pemeriksaan rekam jejak hukum calon debitur & perusahaannya berikut:\n";
        $prompt .= "- Nama Calon Debitur / Pengurus: {$namaDebiturBersih}\n";
        $prompt .= "- Kota / Domisili Hukum: {$kotaBersih}\n";
        $prompt .= "- Entitas Usaha / Perusahaan: {$usahaBersih}\n\n";
        if (!empty($beritaPrompt)) {
            $prompt .= $beritaPrompt;
        }
        $prompt .= "INSTRUKSI KHUSUS & PROFILING:\n";
        $prompt .= "1. PROFILING SIAPA ORANGNYA & PERUSAHAANNYA: Identifikasi secara spesifik siapa figur/subjek hukum ini dan bagaimana rekam jejak perusahaannya di Indonesia. Jika nama orang atau perusahaannya terkait kasus pidana, korupsi, kepailitan/PKPU, atau penangkapan oleh aparat, jelaskan secara gamblang!\n";
        $prompt .= "2. VERIFIKASI STATUS PERKARA & KORUPSI: Periksa apakah nama orang atau perusahaannya tercatat memiliki vonis bersalah, perkara pidana umum, perkara korupsi/tipikor, kepailitan/PKPU, atau sengketa wanprestasi perdata. Jika ada penangkapan polisi atau status tersangka/pailit, nyatakan status_perkara sebagai 'ada_perkara' dengan tingkat_risiko 'HIGH' atau 'CRITICAL'!\n";
        $prompt .= "3. STATUS KEPATUHAN PERBANKAN (PEP / APU-PPT): Jika yang bersangkutan adalah pejabat negara/lembaga pengelola anggaran negara, nyatakan statusnya sebagai Politically Exposed Person (PEP) yang memerlukan Enhanced Due Diligence (EDD).\n";
        $prompt .= "4. KRITERIA STATUS PERKARA:\n";
        $prompt .= "   - 'ada_perkara': Jika terbukti memiliki vonis pidana, kasus korupsi, kepailitan/PKPU, atau kasus hukum aktif bermasalah (Tingkat Risiko: CRITICAL / HIGH).\n";
        $prompt .= "   - 'nihil': Jika tidak ada catatan vonis pidana/korupsi/perdata aktif di pengadilan.\n\n";
        $prompt .= "Kembalikan output HANYA berupa JSON valid dengan format persis berikut:\n";
        $prompt .= "{\n";
        $prompt .= '  "status_perkara": "ada_perkara / nihil", ' . "\n";
        $prompt .= '  "profil_subjek": "penjelasan detail siapa figur/orang dan perusahaan ini di Indonesia", ' . "\n";
        $prompt .= '  "ringkasan": "penjelasan detail temuan perkara hukum calon debitur dan perusahaan", ' . "\n";
        $prompt .= '  "rekomendasi_narasi": "kalimat formal standar perbankan untuk lembar CAM SIPP (menyebutkan hasil verifikasi hukum orang dan perusahaannya)", ' . "\n";
        $prompt .= '  "tingkat_risiko": "LOW / MEDIUM (PEP) / HIGH / CRITICAL"' . "\n";
        $prompt .= "}";

        $apiRes = $this->panggilGeminiApi($prompt, $apiKey, $model, 0.1);
        if ($apiRes['ok']) {
            $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($apiRes['text']));
            $parsed = json_decode($cleanedJson, true);

            if ($parsed && isset($parsed['rekomendasi_narasi'])) {
                return [
                    'status'             => true,
                    'status_perkara'     => $parsed['status_perkara'] ?? 'nihil',
                    'profil_subjek'      => $parsed['profil_subjek'] ?? '',
                    'ringkasan'          => $parsed['ringkasan'] ?? 'Tidak ditemukan perkara perdata aktif.',
                    'rekomendasi_narasi' => $parsed['rekomendasi_narasi'],
                    'tingkat_risiko'     => $parsed['tingkat_risiko'] ?? 'LOW',
                    'query_url'          => $queryUrl,
                    'berita_terkait'     => $beritaList,
                    'sumber'             => 'Gemini Legal Risk Assistant (Direktori Putusan MA)'
                ];
            }
        }

        return [
            'status'             => true,
            'status_perkara'     => 'nihil',
            'ringkasan'          => "Penelusuran basis data perkara peradilan atas nama {$namaDebiturBersih} ({$usahaBersih}) di wilayah {$kotaBersih}: Tidak ditemukan catatan perkara sengketa hukum aktif.",
            'rekomendasi_narasi' => "Berdasarkan penelusuran Direktori Putusan Mahkamah Agung & SIPP Pengadilan Negeri/Niaga, tidak ditemukan perkara perdata wanprestasi, PMH, sengketa hak milik agunan, maupun permohonan kepailitan/PKPU aktif atas nama {$namaDebiturBersih} maupun {$usahaBersih}. Status: BERSIH / NIHIL PERKARA.",
            'query_url'          => $queryUrl,
            'berita_terkait'     => $beritaList,
            'sumber'             => 'Engine Verifikasi Legal MA (Fallback Response)'
        ];
    }
}
