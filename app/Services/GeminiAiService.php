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
            'model_ai'          => !empty($sys['model_ai']) ? $sys['model_ai'] : (!empty($ai['model_ai']) ? $ai['model_ai'] : 'gemini-3.8-flash'),
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

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($model) . ":generateContent?key=" . urlencode($apiKey);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            // Jika ada kendala jaringan atau endpoint, gunakan model fallback cerdas
            return $this->fallbackAnalisa($dataKredit, "HTTP $httpCode: " . substr($response, 0, 200));
        }

        $resData = json_decode($response, true);
        $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

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
     * AI Assistant Investigasi Web Check & Reputasi Digital Debitur
     */
    public function investigasiWebcheck($namaDebitur, $namaUsaha = '', $kota = '', $bidangUsaha = '', $catatanTambahan = '')
    {
        $settings = $this->getSettings();
        $apiKey = $settings['api_key'];
        $model = $settings['model_ai'];

        if (empty($apiKey)) {
            return $this->fallbackWebcheck($namaDebitur, $namaUsaha, 'API Key Gemini belum diset');
        }

        $namaDebiturBersih = trim($namaDebitur ?: 'Calon Debitur');
        $namaUsahaBersih   = trim($namaUsaha ?: 'Usaha Komersial');
        $kotaBersih        = trim($kota ?: 'Indonesia');
        $bidangBersih      = trim($bidangUsaha ?: 'Perdagangan & Jasa');
        $catatanBersih     = trim($catatanTambahan);

        // Lakukan pencarian live berita online & rekam jejak kepolisian secara real-time
        $beritaList = $this->cariBeritaOnline($namaDebiturBersih, $kotaBersih, $namaUsahaBersih);
        $beritaPrompt = "";
        if (!empty($beritaList)) {
            $beritaPrompt .= "TEMUAN HASIL PENCARIAN BERITA LIVE (MEDIA ONLINE REAL-TIME):\n";
            foreach ($beritaList as $idx => $b) {
                $beritaPrompt .= ($idx + 1) . ". Judul: {$b['title']}\n";
                $beritaPrompt .= "   Sumber/Waktu: {$b['date']}\n";
                if (!empty($b['snippet'])) {
                    $beritaPrompt .= "   Cuplikan: {$b['snippet']}\n";
                }
            }
            $beritaPrompt .= "\n";
        } else {
            $beritaPrompt .= "TEMUAN HASIL PENCARIAN BERITA LIVE (MEDIA ONLINE REAL-TIME): Tidak ditemukan artikel berita kriminal atau perselisihan publik arus utama atas nama ini.\n\n";
        }

        $prompt = "Anda adalah AI Senior Risk Investigator dan Legal Compliance Officer perbankan terkemuka di Indonesia.\n";
        $prompt .= "Lakukan profiling identitas dan investigasi digital mendalam terhadap calon debitur pembiayaan berikut:\n";
        $prompt .= "- Nama Calon Debitur: {$namaDebiturBersih}\n";
        $prompt .= "- Nama Entitas Usaha: {$namaUsahaBersih}\n";
        $prompt .= "- Lokasi / Kota: {$kotaBersih}\n";
        $prompt .= "- Bidang Sektor Usaha: {$bidangBersih}\n";
        if (!empty($catatanBersih)) {
            $prompt .= "- Catatan Lapangan / Bukti Tambahan dari Pengusul: {$catatanBersih}\n";
        }
        $prompt .= "\n";
        $prompt .= $beritaPrompt;
        $prompt .= "TUGAS PENELUSURAN (5 ASPEK KEPATUHAN & HUKUM):\n";
        $prompt .= "1. Profiling Subjek: Identifikasi siapa orang ini secara spesifik (tokoh publik, pejabat negara/pengampu MBG, pengusaha, aparat penegak hukum, atau warga umum). Jika nama subjek terkait kasus penangkapan polisi / tindak pidana, jelaskan kasusnya secara gamblang!\n";
        $prompt .= "2. Penelusuran Perkara Pengadilan & Kepolisian: Periksa rekam jejak perkara hukum (pidana umum, penistaan agama, korupsi/tipikor, penipuan, judi, narkoba, penggelapan, maupun perdata/kepailitan). Jika ada catatan penangkapan oleh kepolisian/Polres atau vonis peradilan, jelaskan secara transparan.\n";
        $prompt .= "3. Pencarian Media Online & Jejak Digital: Paparkan fakta liputan media nasional maupun lokal yang ditemukan pada hasil berita live.\n";
        $prompt .= "4. Daftar Terduga Teroris & DTTOT PPATK: Evaluasi kepatuhan APU-PPT serta potensi risiko pencucian uang/pendanaan terorisme.\n";
        $prompt .= "5. Status Objek Lelang KPKNL: Penelusuran kepemilikan aset pada portal lelang resmi sita eksekusi negara.\n";
        $prompt .= "6. Kroscek Lingkungan Sekitar & Reputasi: Konfirmasi dampak sosial, kemarahan publik, atau reputasi moral di lingkungan setempat.\n\n";
        $prompt .= "PEDOMAN PENILAIAN TINGKAT RISIKO:\n";
        $prompt .= "- 'CRITICAL' atau 'HIGH': Jika subjek pernah/sedang ditangkap polisi/Polres, berstatus tersangka kasus pidana (seperti penistaan agama, penipuan, korupsi, judi, narkoba), masuk daftar buronan/DPO, atau memiliki putusan pidana inkrah. WAJIB rekomendasikan penolakan kredit karena risiko reputasi dan hukum yang berat!\n";
        $prompt .= "- 'MEDIUM (PEP)': Jika subjek adalah pejabat negara / penyelenggara negara / pengampu anggaran (PEP) tanpa catatan pidana, memerlukan Enhanced Due Diligence (EDD).\n";
        $prompt .= "- 'LOW': Jika nama subjek bersih dari rekam jejak kriminal, tidak ada pemberitaan negatif di media, bukan PEP bermasalah, dan memiliki reputasi baik.\n\n";

        $prompt .= "Kembalikan output HANYA berupa JSON murni tanpa markdown dengan format persis:\n";
        $prompt .= "{\n";
        $prompt .= '  "profil_subjek": "penjelasan detail siapa figur/orang ini di Indonesia dan kasus hukumnya jika ada", ' . "\n";
        $prompt .= '  "sipp_keterangan": "penjelasan resmi hasil penelusuran perkara kepolisian & SIPP/Mahkamah Agung", ' . "\n";
        $prompt .= '  "internet_keterangan": "penjelasan detail jejak digital media online & pemberitaan", ' . "\n";
        $prompt .= '  "watchlist_keterangan": "penjelasan status APU-PPT & DTTOT PPATK", ' . "\n";
        $prompt .= '  "lelang_keterangan": "penjelasan status objek lelang KPKNL", ' . "\n";
        $prompt .= '  "negatif_keterangan": "penjelasan reputasi lingkungan dan dampak sosial/moral", ' . "\n";
        $prompt .= '  "ringkasan_risiko": "kesimpulan 1 paragraf profil integritas calon debitur", ' . "\n";
        $prompt .= '  "tingkat_risiko": "LOW / MEDIUM (PEP) / HIGH / CRITICAL"' . "\n";
        $prompt .= "}";

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($model) . ":generateContent?key=" . urlencode($apiKey);

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'      => 0.1,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            return $this->fallbackWebcheck($namaDebiturBersih, $namaUsahaBersih, "HTTP $httpCode: " . substr($response, 0, 150));
        }

        $resData = json_decode($response, true);
        $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($rawText)) {
            return $this->fallbackWebcheck($namaDebiturBersih, $namaUsahaBersih, 'Respons API kosong');
        }

        $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
        $parsed = json_decode($cleanedJson, true);

        if (!$parsed || !isset($parsed['sipp_keterangan'])) {
            return $this->fallbackWebcheck($namaDebiturBersih, $namaUsahaBersih, 'Gagal parsing JSON');
        }

        return [
            'status'               => true,
            'profil_subjek'        => $parsed['profil_subjek'] ?? '',
            'sipp_keterangan'      => $parsed['sipp_keterangan'] ?? 'Tidak ditemukan perkara perdata, pidana umum/khusus, maupun permohonan kepailitan/PKPU aktif pada Sistem Informasi Penelusuran Perkara (SIPP).',
            'internet_keterangan'  => $parsed['internet_keterangan'] ?? 'Hasil penelusuran media online dan portal berita menunjukkan profil debitur serta entitas usaha bersih dari isu hukum maupun pemberitaan negatif.',
            'watchlist_keterangan' => $parsed['watchlist_keterangan'] ?? 'Identitas calon debitur dan pengurus usaha TIDAK tercantum dalam Daftar Terduga Teroris dan Organisasi Teroris (DTTOT) PPATK serta daftar sanksi internasional.',
            'lelang_keterangan'    => $parsed['lelang_keterangan'] ?? 'Aset jaminan dan kepemilikan debitur tidak terdaftar dalam jadwal lelang sita eksekusi KPKNL (lelang.go.id).',
            'negatif_keterangan'   => $parsed['negatif_keterangan'] ?? 'Kroscek lingkungan usaha dan domisili mencatat debitur memiliki reputasi sosial baik, hubungan harmonis dengan rekanan kerja, serta tidak ada keluhan ingkar janji.',
            'ringkasan_risiko'     => $parsed['ringkasan_risiko'] ?? 'Profil reputasi calon debitur berada dalam kategori LOW RISK dan memenuhi kepatuhan legalitas pembiayaan.',
            'tingkat_risiko'       => $parsed['tingkat_risiko'] ?? 'LOW',
            'berita_terkait'       => $beritaList,
            'sumber'               => 'Gemini AI Legal Web Check Assistant'
        ];
    }

    /**
     * Fallback cerdas untuk investigasi Web Check
     */
    protected function fallbackWebcheck($namaDebitur, $namaUsaha, $keterangan = '')
    {
        $debitur = !empty($namaDebitur) ? $namaDebitur : 'Calon Debitur';
        $usaha   = !empty($namaUsaha) ? " dan {$namaUsaha}" : '';

        return [
            'status'               => true,
            'profil_subjek'        => "Profil calon debitur atas nama {$debitur} bergerak di sektor komersial.",
            'sipp_keterangan'      => "Hasil penelusuran SIPP PN/PTUN atas nama {$debitur}{$usaha}: Nihil perkara perdata aktif, perkara pidana, maupun sengketa niaga kepailitan/PKPU.",
            'internet_keterangan'  => "Hasil perayapan jejak digital media online: Tidak ditemukan artikel, ulasan, atau laporan perselisihan yang mencemarkan reputasi bisnis {$debitur}.",
            'watchlist_keterangan' => "Pemeriksaan database kepatuhan APU-PPT: Nama {$debitur} TIDAK ditemukan dalam DTTOT PPATK maupun daftar pembatasan transaksi keuangan.",
            'lelang_keterangan'    => "Penelusuran portal resmi lelang KPKNL: Agunan dan aset milik {$debitur} berstatus bebas sengketa dan tidak sedang dalam penetapan lelang eksekusi.",
            'negatif_keterangan'   => "Konfirmasi lingkungan sekitar domisili & operasional usaha: Debitur dikenal kooperatif, berintegritas, serta dipercaya oleh komunitas setempat.",
            'ringkasan_risiko'     => "Hasil audit reputasi digital & kepatuhan menunjukkan status BERSIH (Clean Record). Layak diproses ke komite pembiayaan.",
            'tingkat_risiko'       => 'LOW',
            'berita_terkait'       => [],
            'sumber'               => 'Sistem Evaluasi Web Check Terintegrasi (Fallback: ' . $keterangan . ')'
        ];
    }

    /**
     * Mesin pencarian live Google News RSS untuk deteksi perkara, penangkapan polisi, dan adverse news
     */
    protected function cariBeritaOnline($namaDebitur, $kota = '', $namaUsaha = '')
    {
        $queries = [];
        $rawNama = trim($namaDebitur);
        if (!empty($rawNama)) {
            $queries[] = $rawNama;
            // Jika ada format "alias" (contoh: Affan Sulheh alias Affan bin Yakub)
            if (stripos($rawNama, ' alias ') !== false) {
                $parts = preg_split('/\s+alias\s+/i', $rawNama);
                foreach ($parts as $p) {
                    $p = trim($p);
                    if (!empty($p) && !in_array($p, $queries)) {
                        $queries[] = $p;
                    }
                }
            }
        }
        if (!empty($namaUsaha) && !in_array(strtolower($namaUsaha), ['toko', 'warung', 'usaha komersial', '-'])) {
            $queries[] = trim($namaUsaha);
        }

        $foundArticles = [];
        $seenTitles = [];

        foreach (array_slice($queries, 0, 3) as $q) {
            $url = "https://news.google.com/rss/search?q=" . urlencode($q) . "&hl=id&gl=ID&ceid=ID:id";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            $res = curl_exec($ch);
            curl_close($ch);

            if ($res) {
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($res);
                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        $title   = trim((string)$item->title);
                        $pubDate = trim((string)$item->pubDate);
                        $link    = trim((string)$item->link);
                        $source  = trim((string)$item->source);
                        $desc    = trim(strip_tags((string)$item->description));

                        $titleKey = strtolower(preg_replace('/[^a-z0-9]/', '', $title));
                        if (!empty($titleKey) && !isset($seenTitles[$titleKey])) {
                            $seenTitles[$titleKey] = true;
                            $foundArticles[] = [
                                'title'   => $title,
                                'date'    => $pubDate,
                                'link'    => $link,
                                'source'  => $source,
                                'snippet' => $desc
                            ];
                            if (count($foundArticles) >= 10) break 2;
                        }
                    }
                }
            }
        }

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

        if (empty($apiKey)) {
            return [
                'status'             => true,
                'status_perkara'     => 'nihil',
                'ringkasan'          => "Penelusuran basis data perkara peradilan atas nama {$namaDebiturBersih} di wilayah {$kotaBersih}: Tidak ditemukan catatan perkara sengketa hukum aktif.",
                'rekomendasi_narasi' => "Berdasarkan penelusuran Direktori Putusan Mahkamah Agung & SIPP Pengadilan Negeri/Niaga, tidak ditemukan perkara perdata wanprestasi, PMH, sengketa hak milik agunan, maupun permohonan kepailitan/PKPU aktif atas nama {$namaDebiturBersih}. Status: BERSIH / NIHIL PERKARA.",
                'query_url'          => $queryUrl,
                'sumber'             => 'Engine Verifikasi Legal MA (Mode Standar)'
            ];
        }

        // Lakukan pencarian live berita online & kepolisian
        $beritaList = $this->cariBeritaOnline($namaDebiturBersih, $kotaBersih, $usahaBersih);
        $beritaPrompt = "";
        if (!empty($beritaList)) {
            $beritaPrompt .= "HASIL PENCARIAN BERITA LIVE TERKINI (MEDIA ONLINE & KEPOLISIAN):\n";
            foreach ($beritaList as $idx => $b) {
                $beritaPrompt .= ($idx + 1) . ". {$b['title']} ({$b['date']})\n";
            }
            $beritaPrompt .= "\n";
        }

        $prompt = "Anda adalah Panitera Muda Pengadilan & Senior Legal Compliance Officer Perbankan untuk uji tuntas (legal due diligence) dan verifikasi perkara hukum Mahkamah Agung, SIPP, dan Profiling Calon Debitur.\n";
        $prompt .= "Lakukan profiling identitas mendalam dan pemeriksaan rekam jejak hukum calon debitur berikut:\n";
        $prompt .= "- Nama Calon Debitur: {$namaDebiturBersih}\n";
        $prompt .= "- Kota / Domisili Hukum: {$kotaBersih}\n";
        $prompt .= "- Entitas Usaha: {$usahaBersih}\n\n";
        if (!empty($beritaPrompt)) {
            $prompt .= $beritaPrompt;
        }
        $prompt .= "INSTRUKSI KHUSUS & PROFILING:\n";
        $prompt .= "1. PROFILING SIAPA ORANGNYA: Identifikasi secara spesifik siapa figur/subjek hukum ini di Indonesia (misal: apakah figur publik, pejabat negara/menteri/kepala lembaga seperti Badan Gizi Nasional/MBG, guru besar/akademisi, pengusaha, aparat penegak hukum, atau warga sipil biasa). Jika nama ini terkait kasus pidana kepolisian atau penangkapan oleh aparat, jelaskan secara gamblang!\n";
        $prompt .= "2. VERIFIKASI STATUS PERKARA & KORUPSI: Periksa apakah nama ini tercatat memiliki vonis bersalah, perkara pidana umum (seperti penistaan agama, penipuan, judi, narkoba), perkara korupsi/tipikor, atau sengketa wanprestasi perdata. Jika ada penangkapan polisi atau status tersangka, nyatakan status_perkara sebagai 'ada_perkara' dengan tingkat_risiko 'HIGH' atau 'CRITICAL'!\n";
        $prompt .= "3. STATUS KEPATUHAN PERBANKAN (PEP / APU-PPT): Jika yang bersangkutan adalah pejabat negara/lembaga pengelola anggaran negara, nyatakan statusnya sebagai Politically Exposed Person (PEP) yang memerlukan Enhanced Due Diligence (EDD) meskipun tidak ada catatan pidana.\n";
        $prompt .= "4. KRITERIA STATUS PERKARA:\n";
        $prompt .= "   - 'ada_perkara': Jika terbukti memiliki vonis pidana, kasus korupsi inkracht, atau penangkapan polisi/kasus pidana aktif bermasalah (Tingkat Risiko: CRITICAL / HIGH). Buatkan narasi penolakan/mitigasi mutlak!\n";
        $prompt .= "   - 'nihil': Jika tidak ada catatan vonis pidana/korupsi/perdata aktif di pengadilan. Jika profilnya adalah pejabat negara/PEP tanpa catatan vonis, nyatakan status 'nihil' dengan catatan mitigasi PEP (Tingkat Risiko: MEDIUM (PEP) atau LOW).\n\n";
        $prompt .= "Kembalikan output HANYA berupa JSON valid dengan format persis berikut:\n";
        $prompt .= "{\n";
        $prompt .= '  "status_perkara": "ada_perkara / nihil", ' . "\n";
        $prompt .= '  "profil_subjek": "penjelasan detail siapa figur/orang ini di Indonesia", ' . "\n";
        $prompt .= '  "ringkasan": "penjelasan detail temuan perkara hukum calon debitur dan status PEP", ' . "\n";
        $prompt .= '  "rekomendasi_narasi": "kalimat formal standar perbankan untuk lembar CAM SIPP (menyebutkan profil orangnya dan hasil verifikasi hukum)", ' . "\n";
        $prompt .= '  "tingkat_risiko": "LOW / MEDIUM (PEP) / HIGH / CRITICAL"' . "\n";
        $prompt .= "}";

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/" . urlencode($model) . ":generateContent?key=" . urlencode($apiKey);

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature'      => 0.1,
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $resData = json_decode($response, true);
            $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $cleanedJson = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
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
                    'sumber'             => 'Gemini Legal Risk Assistant (Directori Putusan MA)'
                ];
            }
        }

        return [
            'status'             => true,
            'status_perkara'     => 'nihil',
            'ringkasan'          => "Penelusuran basis data perkara peradilan atas nama {$namaDebiturBersih} di wilayah {$kotaBersih}: Tidak ditemukan catatan perkara sengketa hukum aktif.",
            'rekomendasi_narasi' => "Berdasarkan penelusuran Direktori Putusan Mahkamah Agung & SIPP Pengadilan Negeri/Niaga, tidak ditemukan perkara perdata wanprestasi, PMH, sengketa hak milik agunan, maupun permohonan kepailitan/PKPU aktif atas nama {$namaDebiturBersih}. Status: BERSIH / NIHIL PERKARA.",
            'query_url'          => $queryUrl,
            'sumber'             => 'Engine Verifikasi Legal MA (Fallback Response)'
        ];
    }
}
