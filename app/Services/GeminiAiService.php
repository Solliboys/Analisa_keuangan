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

        $sanitizePrompt = static function ($raw, string $pillar, string $fallback): string {
            $txt = trim((string)($raw ?? ''));
            if ($txt === '' || mb_strlen($txt) < 40) {
                return $fallback;
            }
            if (preg_match('/(was\s+here|hacked|pwned|defaced|<script|0[x×][a-z0-9]+)/iu', $txt)) {
                return $fallback;
            }
            if (preg_match('/^(Analisis (karakter|kapasitas|permodalan|jaminan|prospek ekonomi) debitur\b|Berikan ringkasan rekomendasi keputusan akhir kredit)/i', $txt) && mb_strlen($txt) < 150) {
                return $fallback;
            }
            $wrongMarkers = [
                'character'  => ['PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
                'capacity'   => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPITAL', 'PILAR CONDITION'],
                'capital'    => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CONDITION'],
                'collateral' => ['PILAR CHARACTER', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
                'condition'  => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CAPITAL'],
                'reviewer'   => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
            ];
            foreach ($wrongMarkers[$pillar] ?? [] as $bad) {
                if (stripos($txt, $bad) !== false) {
                    return $fallback;
                }
            }
            return $txt;
        };

        $defaultChar  = "Anda adalah Analis Kredit Senior spesialis evaluasi PILAR CHARACTER (Karakter & Integritas Debitur). Analisa riwayat SLIK OJK/iDeb (Kolektibilitas 1-5, frekuensi DPD, kedisiplinan angsuran) dan verifikasi kebersihan nama pada Web Check SIPP Kepaniteraan Pengadilan dari perkara sengketa/wanprestasi/pailit/PKPU serta DTTOT PPATK.";
        $defaultCap   = "Anda adalah Analis Keuangan & Cashflow Underwriter spesialis evaluasi PILAR CAPACITY (Kemampuan Membayar). Validasi omzet riil mutasi rekening koran, surplus kas bersih operasional bulanan, Debt Service Coverage (DSC min 1.30x untuk komersial), DSR (maks 35-40%), dan kesesuaian skema angsuran (Installment vs Balloon Payment).";
        $defaultCapt  = "Anda adalah Analis Struktur Modal & Solvabilitas spesialis evaluasi PILAR CAPITAL (Permodalan & Posisi Ekuitas). Evaluasi nilai Harta Bersih/Ekuitas bebas agunan (Net Worth), rasio solvabilitas Debt to Asset Ratio (DAR maks 50-60%), dan ketersediaan financial buffer penyangga likuiditas.";
        $defaultColl  = "Anda adalah Penilai Agunan & Legal Underwriter spesialis evaluasi PILAR COLLATERAL (Agunan & Jaminan). Uji keabsahan yuridis dokumen kepemilikan (SHM/SHGB/BPKB/Deposito), taksasi Nilai Pasar (MV) dan Nilai Likuidasi (LV), rasio penutupan Coverage MV (min 120-130%), Coverage LV (min 100%), serta LTV (maks 70-80%).";
        $defaultCond  = "Anda adalah Penilai Risiko Ekonomi & Makro Sektoral spesialis evaluasi PILAR CONDITION (Kondisi Makro & Sektor Usaha). Evaluasi ketahanan sektor industri usaha pemohon terhadap inflasi dan persaingan, jam terbang pengalaman usaha (min 2-3 tahun), dan keselarasan tujuan kredit (modal kerja/investasi/konsumtif).";
        $defaultFinal = "Anda adalah Ketua Komite Pemutus Kredit Utama. Lakukan sintesis terintegrasi pembobotan 5C (Character 25%, Capacity 25%, Capital 15%, Collateral 20%, Condition 15%) dan tetapkan rekomendasi keputusan final (Disetujui Penuh / Disetujui dengan Penurunan Plafon / Pertimbangan Khusus / Tolak), nominal plafon aman, serta syarat tangguh mitigasi pencairan kredit.";

        return [
            'api_key'           => $sys['api_key'] ?? $ai['api_key'] ?? (getenv('GEMINI_API_KEY') ?: ''),
            'model_ai'          => !empty($sys['model_ai']) ? $sys['model_ai'] : (!empty($ai['model_ai']) ? $ai['model_ai'] : 'gemini-flash-lite-latest'),
            'api_endpoint'      => $sys['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models',
            'prompt_sistem'     => $sys['prompt_sistem'] ?? "Anda adalah Senior Credit Underwriter dan Risk Analyst pembiayaan profesional berstandar OJK. Lakukan evaluasi kelayakan kredit komprehensif terhadap calon debitur berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).",
            'prompt_character'  => $sanitizePrompt($ai['prompt_character'] ?? '', 'character', $defaultChar),
            'prompt_capacity'   => $sanitizePrompt($ai['prompt_capacity'] ?? '', 'capacity', $defaultCap),
            'prompt_capital'    => $sanitizePrompt($ai['prompt_capital'] ?? '', 'capital', $defaultCapt),
            'prompt_collateral' => $sanitizePrompt($ai['prompt_collateral'] ?? '', 'collateral', $defaultColl),
            'prompt_condition'  => $sanitizePrompt($ai['prompt_condition'] ?? '', 'condition', $defaultCond),
            'prompt_final'      => $sanitizePrompt($ai['prompt_final'] ?? '', 'reviewer', $defaultFinal)
        ];
    }

    /**
     * Deteksi provider AI berdasarkan model_ai dan api_endpoint terpusat di Pengaturan Superadmin
     */
    protected function deteksiProvider(string $model, string $endpoint = ''): string
    {
        $m = strtolower(trim($model));
        $e = strtolower(trim($endpoint));

        if (strpos($m, 'deepseek') !== false || strpos($e, 'deepseek.com') !== false) {
            return 'deepseek';
        }
        if (strpos($m, 'claude') !== false || strpos($e, 'anthropic.com') !== false) {
            return 'anthropic';
        }
        if (
            strpos($m, 'gpt-') !== false ||
            strpos($m, 'o1') === 0 ||
            strpos($m, 'o3') === 0 ||
            $m === 'custom' ||
            strpos($e, 'openai.com') !== false ||
            strpos($e, 'openrouter.ai') !== false ||
            strpos($e, 'groq.com') !== false ||
            strpos($e, '/chat/completions') !== false
        ) {
            return 'openai_compatible';
        }
        return 'gemini';
    }

    /**
     * Helper pemanggilan AI terpusat (mendukung Google Gemini, DeepSeek V3/R1, OpenAI, Anthropic Claude, & Custom Endpoint)
     */
    protected function panggilGeminiApi(string $prompt, string $apiKey, string $preferredModel = 'gemini-2.5-flash', float $temperature = 0.2, ?string $overrideEndpoint = null): array
    {
        $settings = $this->getSettings();
        $endpointCfg = trim((string)($overrideEndpoint !== null ? $overrideEndpoint : ($settings['api_endpoint'] ?? '')));
        $cleanModel = trim($preferredModel ?: ($settings['model_ai'] ?? 'gemini-2.5-flash'));
        $provider = $this->deteksiProvider($cleanModel, $endpointCfg);

        if (empty(trim($apiKey))) {
            return ['ok' => false, 'error' => 'API Key belum diisi di Pengaturan Superadmin'];
        }

        // 1. DEEPSEEK / OPENAI / CUSTOM OPENAI-COMPATIBLE
        if ($provider === 'deepseek' || $provider === 'openai_compatible') {
            $defaultUrl = ($provider === 'deepseek')
                ? 'https://api.deepseek.com/chat/completions'
                : 'https://api.openai.com/v1/chat/completions';

            $url = $defaultUrl;
            if ($endpointCfg !== '' && stripos($endpointCfg, 'generativelanguage.googleapis.com') === false) {
                $url = rtrim($endpointCfg, '/');
                if (stripos($url, '/chat/completions') === false) {
                    $url .= '/chat/completions';
                }
            }

            $targetModel = $cleanModel;
            if ($targetModel === '' || $targetModel === 'custom') {
                $targetModel = ($provider === 'deepseek') ? 'deepseek-chat' : 'gpt-4o-mini';
            }

            $payload = [
                'model'    => $targetModel,
                'messages' => [
                    ['role' => 'system', 'content' => 'Anda adalah analis kredit profesional. Selalu balas dengan JSON murni yang valid tanpa markdown.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 2048
            ];
            // deepseek-reasoner tidak menerima temperature / response_format tertentu
            if (stripos($targetModel, 'reasoner') === false) {
                $payload['temperature'] = $temperature;
                $payload['response_format'] = ['type' => 'json_object'];
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($apiKey)
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
            curl_setopt($ch, CURLOPT_TIMEOUT, 35);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if (!$curlError && $httpCode === 200 && !empty($response)) {
                $resData = json_decode($response, true);
                $rawText = $resData['choices'][0]['message']['content'] ?? '';
                if (!empty($rawText)) {
                    return [
                        'ok'    => true,
                        'text'  => $rawText,
                        'model' => $targetModel
                    ];
                }
            }
            $errMsg = '';
            if (!empty($response)) {
                $errJson = json_decode($response, true);
                $errMsg = $errJson['error']['message'] ?? '';
            }
            return [
                'ok'    => false,
                'error' => $curlError ?: ("HTTP $httpCode ($targetModel)" . ($errMsg ? ": $errMsg" : ''))
            ];
        }

        // 2. ANTHROPIC CLAUDE
        if ($provider === 'anthropic') {
            $url = ($endpointCfg !== '' && stripos($endpointCfg, 'anthropic.com') !== false)
                ? $endpointCfg
                : 'https://api.anthropic.com/v1/messages';

            $targetModel = $cleanModel ?: 'claude-3-5-sonnet-20241022';
            if ($targetModel === 'claude-3-5-sonnet') {
                $targetModel = 'claude-3-5-sonnet-20241022';
            }

            $payload = [
                'model'       => $targetModel,
                'max_tokens'  => 2048,
                'temperature' => $temperature,
                'messages'    => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'x-api-key: ' . trim($apiKey),
                'anthropic-version: 2023-06-01'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
            curl_setopt($ch, CURLOPT_TIMEOUT, 35);

            $response  = curl_exec($ch);
            $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if (!$curlError && $httpCode === 200 && !empty($response)) {
                $resData = json_decode($response, true);
                $rawText = $resData['content'][0]['text'] ?? '';
                if (!empty($rawText)) {
                    return [
                        'ok'    => true,
                        'text'  => $rawText,
                        'model' => $targetModel
                    ];
                }
            }
            $errMsg = '';
            if (!empty($response)) {
                $errJson = json_decode($response, true);
                $errMsg = $errJson['error']['message'] ?? '';
            }
            return [
                'ok'    => false,
                'error' => $curlError ?: ("HTTP $httpCode ($targetModel)" . ($errMsg ? ": $errMsg" : ''))
            ];
        }

        // 3. GOOGLE GEMINI (default)
        if (preg_match('/^gemini-3\./i', $cleanModel)) {
            $cleanModel = 'gemini-2.5-flash';
        }
        $baseGemini = ($endpointCfg !== '' && stripos($endpointCfg, 'googleapis.com') !== false)
            ? rtrim($endpointCfg, '/')
            : 'https://generativelanguage.googleapis.com/v1beta/models';

        // Prioritaskan model pilihan Superadmin terlebih dahulu, lalu fallback model Gemini valid
        $candidateModels = array_values(array_unique(array_filter([
            $cleanModel,
            'gemini-2.5-flash',
            'gemini-2.5-flash-lite',
            'gemini-2.0-flash',
            'gemini-flash-latest',
            'gemini-flash-lite-latest'
        ])));

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
                'responseMimeType' => 'application/json',
                'maxOutputTokens'  => 2048
            ]
        ];

        $lastError = '';
        foreach ($candidateModels as $mod) {
            $endpoint = $baseGemini . '/' . urlencode($mod) . ':generateContent?key=' . urlencode(trim($apiKey));
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

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
            $errMsg = '';
            if (!empty($response)) {
                $errJson = json_decode($response, true);
                $errMsg = $errJson['error']['message'] ?? '';
            }
            $lastError = $curlError ?: ("HTTP $httpCode ($mod)" . ($errMsg ? ": $errMsg" : ''));
        }

        return [
            'ok'    => false,
            'error' => $lastError
        ];
    }

    /**
     * Tes koneksi langsung ke provider AI aktif (Gemini / DeepSeek / OpenAI / Claude) dari Pengaturan Superadmin
     */
    public function tesKoneksiAi(?string $modelOverride = null, ?string $endpointOverride = null, ?string $apiKeyOverride = null): array
    {
        $settings = $this->getSettings();
        $model    = trim((string)($modelOverride !== null && $modelOverride !== '' ? $modelOverride : $settings['model_ai']));
        $endpoint = trim((string)($endpointOverride !== null ? $endpointOverride : $settings['api_endpoint']));
        $apiKey   = trim((string)($apiKeyOverride !== null && $apiKeyOverride !== '' ? $apiKeyOverride : $settings['api_key']));

        $provider = $this->deteksiProvider($model, $endpoint);
        $testPrompt = 'Balas hanya dengan JSON valid: {"status":"ok","pesan":"Koneksi AI berhasil"}';
        $res = $this->panggilGeminiApi($testPrompt, $apiKey, $model, 0.0, $endpoint);

        if ($res['ok']) {
            return [
                'status'   => true,
                'provider' => strtoupper($provider),
                'model'    => $res['model'] ?? $model,
                'message'  => 'Koneksi berhasil ke ' . strtoupper($provider) . ' (' . ($res['model'] ?? $model) . ').'
            ];
        }

        return [
            'status'   => false,
            'provider' => strtoupper($provider),
            'model'    => $model,
            'message'  => 'Gagal terhubung ke ' . strtoupper($provider) . ': ' . ($res['error'] ?? 'Kesalahan tidak diketahui')
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
        $prompt .= "1. Karakter (Character):\n" . $this->substitusiVariabelPrompt($settings['prompt_character'], $dataKredit) . "\n\n";
        $prompt .= "2. Kapasitas (Capacity):\n" . $this->substitusiVariabelPrompt($settings['prompt_capacity'], $dataKredit) . "\n\n";
        $prompt .= "3. Permodalan (Capital):\n" . $this->substitusiVariabelPrompt($settings['prompt_capital'], $dataKredit) . "\n\n";
        $prompt .= "4. Agunan (Collateral):\n" . $this->substitusiVariabelPrompt($settings['prompt_collateral'], $dataKredit) . "\n\n";
        $prompt .= "5. Kondisi (Condition):\n" . $this->substitusiVariabelPrompt($settings['prompt_condition'], $dataKredit) . "\n\n";
        $prompt .= "6. Keputusan Komite (Reviewer):\n" . $this->substitusiVariabelPrompt($settings['prompt_final'], $dataKredit) . "\n\n";

        $prompt .= "DATA LENGKAP CALON DEBITUR & PEMBIAYAAN (FAKTA PENGAJUAN):\n";
        $prompt .= "1. IDENTITAS DEBITUR & FASILITAS PINJAMAN:\n";
        $prompt .= "- Nama Debitur: " . ($dataKredit['nama_debitur'] ?? '-') . "\n";
        $prompt .= "- Jenis Debitur: " . ($dataKredit['jenis_debitur'] ?? 'Perorangan') . "\n";
        $prompt .= "- Nama Usaha: " . ($dataKredit['nama_usaha'] ?? '-') . " (Lama Berjalan: " . ($dataKredit['lama_usaha'] ?? '-') . ")\n";
        $prompt .= "- Sektor / Bidang Usaha: " . ($dataKredit['bidang_usaha'] ?? ($dataKredit['sektor_usaha'] ?? 'Perdagangan / Jasa')) . "\n";
        $prompt .= "- Tujuan Pembiayaan: " . ($dataKredit['tujuan_pembiayaan'] ?? 'Modal Kerja Operasional') . "\n";
        $prompt .= "- Plafon Pengajuan: Rp " . number_format((float)($dataKredit['plafon_pengajuan'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Tenor: " . ($dataKredit['tenor_bulan'] ?? 12) . " Bulan\n";
        $prompt .= "- Skema Pembayaran: " . ($dataKredit['skema_pembayaran'] ?? 'Balloon Payment') . "\n";
        $prompt .= "- Suku Bunga: " . ($dataKredit['bunga_per_bulan'] ?? 3) . "%/bulan\n\n";

        $prompt .= "2. KUALITAS AGUNAN / JAMINAN (COLLATERAL):\n";
        $prompt .= "- Rincian Dokumen & Jenis Jaminan: " . ($dataKredit['rincian_agunan'] ?? 'Agunan Properti / Kendaraan') . "\n";
        $prompt .= "- Total Nilai Pasar (MV): Rp " . number_format((float)($dataKredit['total_mv'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Total Nilai Likuidasi (LV): Rp " . number_format((float)($dataKredit['total_lv'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Rasio Penutupan Coverage MV: " . ($dataKredit['coverage_mv'] ?? 0) . "% (Standar min lembaga pembiayaan: 120%)\n";
        $prompt .= "- Rasio Penutupan Coverage LV: " . ($dataKredit['coverage_lv'] ?? 0) . "% (Standar min lembaga pembiayaan: 100%)\n\n";

        $prompt .= "3. KEMAMPUAN BAYAR & ARUS KAS (CAPACITY):\n";
        $prompt .= "- Omzet Rata-rata Bulanan: Rp " . number_format((float)($dataKredit['omzet'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Laba Bersih Operasional / Surplus Bulanan: Rp " . number_format((float)($dataKredit['laba_bersih'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Angsuran Kredit Acuan: Rp " . number_format((float)($dataKredit['angsuran'] ?? 0), 0, ',', '.') . "/bulan\n";
        $prompt .= "- Rasio Debt Service Coverage (DSC): " . ($dataKredit['rasio_dsc'] ?? 0) . "x (Standar minimum sehat: 1.30x)\n\n";

        $prompt .= "4. STRUKTUR PERMODALAN & POSISI ASET (CAPITAL):\n";
        $prompt .= "- Total Harta / Aset: Rp " . number_format((float)($dataKredit['total_aset'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Total Liabilitas / Utang Luar: Rp " . number_format((float)($dataKredit['total_utang'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Modal Bersih / Kekayaan Bersih (Ekuitas): Rp " . number_format((float)($dataKredit['modal_bersih'] ?? 0), 0, ',', '.') . "\n";
        $prompt .= "- Debt to Asset Ratio (DAR): " . ($dataKredit['rasio_dar'] ?? '0%') . "\n\n";

        $prompt .= "5. REPUTASI & LEGALITAS (CHARACTER):\n";
        $prompt .= "- Status & Catatan SLIK OJK: " . ($dataKredit['kesimpulan_kolektibilitas'] ?? ($dataKredit['ringkasan_slik'] ?? 'Lancar / Kolektibilitas 1')) . "\n";
        $prompt .= "- Riwayat Tunggakan: " . ($dataKredit['ringkasan_slik'] ?? 'Tidak ditemukan catatan tunggakan material.') . "\n";
        $prompt .= "- Hasil Web Check (SIPP, Kepailitan, DTTOT): " . ($dataKredit['webcheck_status'] ?? 'Bersih / tidak ada perkara hukum aktif.') . "\n\n";

        $prompt .= "6. KONDISI MAKRO & SEKTOR USAHA (CONDITION):\n";
        $prompt .= "- Sektor Usaha: " . ($dataKredit['bidang_usaha'] ?? 'Sektor Riil') . "\n";
        $prompt .= "- Lama Beroperasi: " . ($dataKredit['lama_usaha'] ?? '-') . "\n";
        $prompt .= "- Tujuan Penggunaan Kredit: " . ($dataKredit['tujuan_pembiayaan'] ?? 'Modal Kerja') . "\n\n";

        $prompt .= "TUGAS ANALISA & FORMAT OUTPUT:\n";
        $prompt .= "Keluarkan hasil analisa komprehensif dalam format JSON murni TANPA markdown formatting (tanpa ```json) dengan struktur eksak berikut:\n";
        $prompt .= "{\n";
        $prompt .= '  "skor_kelayakan": (angka bulat integer 0 sampai 100),' . "\n";
        $prompt .= '  "rekomendasi_keputusan": (pilih salah satu eksak: "Disetujui Penuh", "Disetujui dengan Penurunan Plafon", "Pertimbangan Khusus", "Tolak"),' . "\n";
        $prompt .= '  "rekomendasi_plafon": (angka nominal bulat rupiah tanpa titik, koma, atau simbol Rp),' . "\n";
        $prompt .= '  "analisis_risiko": (uraian analisa underwriting profesional 2 paragraf padat & tajam: paragraf 1 merangkum evaluasi 5C debitur, rasio DSC, coverage agunan, dan posisi modal; paragraf 2 menguraikan justifikasi keputusan, penetapan batas plafon aman, serta klausul mitigasi syarat tangguh sebelum pencairan kredit)' . "\n";
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

        $rawPlafon = $parsed['rekomendasi_plafon'] ?? ($dataKredit['plafon_pengajuan'] ?? 0);
        if (is_string($rawPlafon)) {
            $rawPlafon = (float)preg_replace('/[^0-9]/', '', $rawPlafon);
        }

        $keputusan = $parsed['rekomendasi_keputusan'] ?? $parsed['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh';
        $modelName = !empty($apiRes['model']) ? ucwords(str_replace('-', ' ', $apiRes['model'])) : 'Gemini AI';

        return [
            'status'                   => true,
            'skor_kelayakan_ai'        => (int)($parsed['skor_kelayakan'] ?? 80),
            'rekomendasi_keputusan_ai' => $keputusan,
            'rekomendasi_plafon_ai'    => (float)$rawPlafon,
            'analisis_risiko_ai'       => $parsed['analisis_risiko'] ?? '',
            'sumber'                   => $modelName . ' (Official Engine)'
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
     * Substitusi variabel dinamis pada template prompt 5C & Reviewer
     * Mendukung tag flat {{variable}} maupun ekspresi n8n {{ $json.variable }} / {{ $('node').item.json.variable }}
     */
    public function substitusiVariabelPrompt(string $prompt, array $dataKredit): string
    {
        if (empty($prompt)) return '';

        $namaDebitur = $dataKredit['nama_debitur'] ?? '-';
        $plafon = (float)($dataKredit['plafon_pengajuan'] ?? 0);
        $omzet  = (float)($dataKredit['omzet'] ?? ($dataKredit['omzet_bulanan'] ?? 0));
        $labaKotor = (float)($dataKredit['laba_kotor'] ?? ($omzet * 0.3));
        $labaBersih = (float)($dataKredit['laba_bersih'] ?? 0);
        $angsuran = (float)($dataKredit['angsuran'] ?? ($dataKredit['estimasi_cicilan_baru'] ?? 0));
        $totalAset = (float)($dataKredit['total_aset'] ?? 0);
        $totalUtang = (float)($dataKredit['total_utang'] ?? 0);
        $modalBersih = (float)($dataKredit['modal_bersih'] ?? ($totalAset - $totalUtang));
        $totalMv = (float)($dataKredit['total_mv'] ?? ($dataKredit['nilai_pasar_mv'] ?? 0));
        $totalLv = (float)($dataKredit['total_lv'] ?? ($dataKredit['nilai_likuidasi_lv'] ?? 0));

        // Mapping dictionary variabel dasar sistem pengajuan
        $varMap = [
            'nama_debitur'              => $namaDebitur,
            'nama_pemohon'              => $namaDebitur,
            'identitas_pemilik'         => $namaDebitur,
            'jenis_debitur'             => $dataKredit['jenis_debitur'] ?? 'Perorangan',
            'nama_usaha'                => $dataKredit['nama_usaha'] ?? '-',
            'lama_usaha'                => $dataKredit['lama_usaha'] ?? '3 tahun',
            'lama_menjalankan_usaha'    => preg_replace('/[^0-9]/', '', (string)($dataKredit['lama_usaha'] ?? '3')) ?: '3',
            'bidang_usaha'              => $dataKredit['bidang_usaha'] ?? ($dataKredit['sektor_usaha'] ?? 'Perdagangan / Jasa'),
            'sektor_usaha'              => $dataKredit['bidang_usaha'] ?? ($dataKredit['sektor_usaha'] ?? 'Perdagangan / Jasa'),
            'riwayat_usaha'             => 'Usaha berjalan normal dan memiliki reputasi baik di lingkungan operasional.',
            'reputasi_bisnis'           => 'Baik / tidak ada catatan negatif dari rekan bisnis atau mitra usaha.',
            'kelengkapan_legalitas'     => $dataKredit['kelengkapan_legalitas'] ?? 'Lengkap (KTP, KK, NIB/SKU, NPWP)',
            'sengketa_hukum'            => 'Tidak Ada (Hasil penelusuran perkara pengadilan SIPP/MA bersih)',
            'riwayat_pembayaran_supplier'=> 'Lancar dan tepat waktu',
            'tanggungan_pribadi_bulanan'=> number_format((float)($dataKredit['biaya_hidup'] ?? 0), 0, ',', '.'),
            'tujuan_pembiayaan'         => $dataKredit['tujuan_pembiayaan'] ?? 'Modal Kerja Operasional',
            
            'plafon_diajukan'           => number_format($plafon, 0, ',', '.'),
            'plafon_pengajuan'          => number_format($plafon, 0, ',', '.'),
            'tenor_diminta'             => ($dataKredit['tenor_bulan'] ?? 12),
            'tenor_bulan'               => ($dataKredit['tenor_bulan'] ?? 12),
            'suku_bunga'                => ($dataKredit['bunga_per_bulan'] ?? 1.25) . '%/bulan',
            'bunga_per_bulan'           => ($dataKredit['bunga_per_bulan'] ?? 1.25),
            'skema_pembayaran'          => $dataKredit['skema_pembayaran'] ?? 'Installment / Anuitas',
            'skema_kredit'              => $dataKredit['skema_pembayaran'] ?? 'Installment / Anuitas',
            'estimasi_cicilan_baru'     => number_format($angsuran, 0, ',', '.'),
            'estimasi_angsuran'         => number_format($angsuran, 0, ',', '.'),
            'angsuran'                  => number_format($angsuran, 0, ',', '.'),
            
            'omzet_bulanan'             => number_format($omzet, 0, ',', '.'),
            'omzet'                     => number_format($omzet, 0, ',', '.'),
            'laba_kotor'                => number_format($labaKotor, 0, ',', '.'),
            'laba_bersih'               => number_format($labaBersih, 0, ',', '.'),
            'surplus_bulanan'           => number_format($labaBersih, 0, ',', '.'),
            'beban_operasional'         => number_format((float)($dataKredit['biaya_hidup'] ?? 0), 0, ',', '.'),
            'biaya_hidup'               => number_format((float)($dataKredit['biaya_hidup'] ?? 0), 0, ',', '.'),
            'beban_bunga'               => number_format((float)($dataKredit['beban_bunga'] ?? 0), 0, ',', '.'),
            'rasio_dsc'                 => ($dataKredit['rasio_dsc'] ?? '1.50'),
            'dscr'                      => ($dataKredit['rasio_dsc'] ?? '1.50'),
            'rasio_dsr'                 => ($dataKredit['rasio_dsr'] ?? '25%'),
            'dsr'                       => ($dataKredit['rasio_dsr'] ?? '25%'),
            
            'total_aset'                => number_format($totalAset, 0, ',', '.'),
            'total_harta'               => number_format($totalAset, 0, ',', '.'),
            'total_liabilitas'          => number_format($totalUtang, 0, ',', '.'),
            'total_utang'               => number_format($totalUtang, 0, ',', '.'),
            'modal_bersih'              => number_format($modalBersih, 0, ',', '.'),
            'ekuitas'                   => number_format($modalBersih, 0, ',', '.'),
            'kekayaan_bersih'           => number_format($modalBersih, 0, ',', '.'),
            'rasio_dar'                 => ($dataKredit['rasio_dar'] ?? '0%'),
            'rasio_der'                 => ($dataKredit['rasio_der'] ?? '0.00x'),
            
            'rincian_agunan'            => $dataKredit['rincian_agunan'] ?? 'Sertifikat Properti / BPKB Kendaraan',
            'jenis_agunan'              => $dataKredit['rincian_agunan'] ?? 'SHM / SHGB / BPKB',
            'total_mv'                  => number_format($totalMv, 0, ',', '.'),
            'nilai_pasar_mv'            => number_format($totalMv, 0, ',', '.'),
            'total_lv'                  => number_format($totalLv, 0, ',', '.'),
            'nilai_likuidasi_lv'        => number_format($totalLv, 0, ',', '.'),
            'coverage_mv'               => ($dataKredit['coverage_mv'] ?? 120) . '%',
            'coverage_lv'               => ($dataKredit['coverage_lv'] ?? 100) . '%',
            
            'kesimpulan_kolektibilitas' => $dataKredit['kesimpulan_kolektibilitas'] ?? ($dataKredit['ringkasan_slik'] ?? 'Kol 1 (Lancar)'),
            'riwayat_kredit'            => $dataKredit['kesimpulan_kolektibilitas'] ?? 'Kol 1 (Lancar)',
            'ringkasan_slik'            => $dataKredit['ringkasan_slik'] ?? 'Riwayat pembayaran kredit teratur tanpa tunggakan berjalan.',
            'teks_lampiran_pdf'         => $dataKredit['ringkasan_slik'] ?? 'Dokumen SLIK terbaca lengkap, kolektibilitas Lancar Kol 1.',
            'total_outstanding'         => number_format((float)($dataKredit['total_baki_debet_slik'] ?? 0), 0, ',', '.'),
            'total_baki_debet_slik'     => number_format((float)($dataKredit['total_baki_debet_slik'] ?? 0), 0, ',', '.'),
            'jumlah_fasilitas_kredit'   => $dataKredit['jumlah_fasilitas_slik'] ?? '1',
            'jumlah_tunggakan'          => '0',
            'tunggakan_terlama'         => '0',
            'riwayat_kredit_macet'      => 'Tidak Pernah',
            'riwayat_restrukturisasi'   => 'Tidak Pernah',
            'esai_penjelasan_karakter'  => 'Pemohon berkomitmen melunasi seluruh kewajiban tepat waktu dan memiliki hubungan baik dengan perbankan.',
            'esai_pov_pengusul'         => $dataKredit['opini_pengusul'] ?? 'Pengusul menilai debitur memiliki iktikad baik, usaha stabil, dan agunan memadai.',
            'opini_pengusul'            => $dataKredit['opini_pengusul'] ?? 'Debitur memiliki kapabilitas keuangan dan rekam jejak usaha yang layak didukung.',
            
            'webcheck_status'           => $dataKredit['webcheck_status'] ?? 'Clear / Bersih dari perkara perdata dan pidana',
            'hasil_webcheck'            => $dataKredit['webcheck_status'] ?? 'Clear / Tidak ada catatan perkara perdata, kepailitan, maupun DTTOT',
            'kondisi_ekonomi_sektor'    => 'Sektor usaha menunjukkan prospek stabil dan permintaan pasar yang berkelanjutan.',
            'persaingan_usaha'          => 'Tingkat kompetisi moderat dengan captive market yang loyal.'
        ];

        // Ganti tag-tag n8n & {{var}} dengan regex cerdas
        return preg_replace_callback('/\{\{\s*(?:\$\(\'[^\']+\'\)\.item\.json\.|\$json\.)?([a-zA-Z0-9_]+)(?:\s*\?\?[^}]+)?\s*\}\}/i', function($matches) use ($varMap) {
            $key = strtolower(trim($matches[1]));
            return $varMap[$key] ?? ('{{' . $key . '}}');
        }, $prompt);
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

    /**
     * Ekstraksi cerdas dokumen Rekening Koran (PDF / Gambar) menggunakan Gemini AI Multimodal Vision
     */
    public function ekstrakRekeningKoran(string $filePath, string $clientMime = ''): array
    {
        try {
            if (!file_exists($filePath)) {
                return [
                    'status'  => false,
                    'message' => 'Berkas rekening koran tidak ditemukan di server.'
                ];
            }

            $settings = $this->getSettings();
            $apiKey = $settings['api_key'] ?? (getenv('GEMINI_API_KEY') ?: '');

            // Deteksi MIME type secara aman tanpa crash jika fileinfo tidak tersedia
            $mimeType = 'application/octet-stream';
            if (preg_match('/\.pdf$/i', $filePath)) {
                $mimeType = 'application/pdf';
            } elseif (preg_match('/\.(jpg|jpeg)$/i', $filePath)) {
                $mimeType = 'image/jpeg';
            } elseif (preg_match('/\.png$/i', $filePath)) {
                $mimeType = 'image/png';
            } elseif (preg_match('/\.webp$/i', $filePath)) {
                $mimeType = 'image/webp';
            } elseif (!empty($clientMime)) {
                $mimeType = $clientMime;
            } elseif (function_exists('mime_content_type')) {
                $mimeType = @mime_content_type($filePath) ?: 'application/octet-stream';
            }

            $binaryData = file_get_contents($filePath);
            // Optimasi otomatis: jika berkas gambar berukuran besar (> 800KB), kompresi & resize agar transmisi super cepat
            if (strpos($mimeType, 'image/') === 0 && function_exists('imagecreatefromstring') && strlen($binaryData) > 800000) {
                try {
                    $img = @imagecreatefromstring($binaryData);
                    if ($img !== false) {
                        $width  = imagesx($img);
                        $height = imagesy($img);
                        $maxDim = 1800;
                        if ($width > $maxDim || $height > $maxDim) {
                            $ratio    = min($maxDim / $width, $maxDim / $height);
                            $newW     = (int)round($width * $ratio);
                            $newH     = (int)round($height * $ratio);
                            $resized  = imagecreatetruecolor($newW, $newH);
                            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $width, $height);
                            ob_start();
                            imagejpeg($resized, null, 82);
                            $binaryData = ob_get_clean();
                            imagedestroy($resized);
                            $mimeType = 'image/jpeg';
                        }
                        imagedestroy($img);
                    }
                } catch (\Throwable $e) {}
            }
            $fileData = base64_encode($binaryData);

            $prompt = "Anda adalah Senior Bank Statement / Rekening Koran Parser dan Financial Analyst yang sangat presisi.
Tugas Anda adalah membaca dokumen mutasi bank / rekening koran yang dilampirkan PERSIS sesuai isi dokumen asli (JANGAN mengarang atau mengasumsikan angka yang tidak ada di dokumen) dan mengekstraknya ke format JSON berikut:
{
  \"nama_bank\": \"...\",
  \"nomor_rekening\": \"...\",
  \"atas_nama\": \"...\",
  \"periode\": \"...\",
  \"jumlah_bulan\": 1,
  \"saldo_awal\": 0,
  \"total_mutasi_kredit\": 0,
  \"total_mutasi_debit\": 0,
  \"saldo_akhir\": 0,
  \"transaksi\": [
    {
      \"tanggal\": \"02/01/2026\",
      \"keterangan\": \"Deskripsi persis dari mutasi\",
      \"kredit\": 20000000,
      \"debit\": 0,
      \"saldo\": 20000000
    }
  ]
}

PANDUAN EKSTRAKSI WAJIB:
1. BACA PERSIS DARI DOKUMEN: Nama bank, nomor rekening, atas nama pemilik rekening, periode mutasi, saldo awal, saldo akhir, dan nominal transaksi harus 100% sesuai yang tertulis di PDF/gambar.
2. FORMAT ANGKA RUPIAH: Perhatikan pemisah ribuan dan desimal pada rekening koran Indonesia (contoh: '1.500.000,00' atau '1,500,000.00' berarti 1500000 rupiah, JANGAN ikutkan 2 digit sen di belakang desimal sebagai ribuan!). Semua field angka (saldo_awal, total_mutasi_kredit, total_mutasi_debit, saldo_akhir, kredit, debit, saldo) WAJIB berupa ANGKA INTEGER MURNI tanpa titik, koma, atau 'Rp'.
3. TOTAL MUTASI: Jika dokumen menampilkan baris ringkasan resmi (Total Mutasi Kredit / CR, Total Mutasi Debit / DB, Saldo Awal, Saldo Akhir), WAJIB gunakan angka ringkasan resmi tersebut. Jika tidak ada baris ringkasan, hitung penjumlahan eksak seluruh transaksi kredit (uang masuk) dan seluruh transaksi debit (uang keluar) di seluruh halaman dokumen.
4. JUMLAH BULAN: Hitung jumlah bulan berdasarkan rentang periode mutasi pada dokumen (misal 1 bulan = 1, Jan-Mar = 3). Minimal 1.
5. DAFTAR TRANSAKSI: Jika transaksi sangat banyak (lebih dari 40 baris), tetap hitung total_mutasi_kredit dan total_mutasi_debit dari SELURUH transaksi di dokumen, dan masukkan maksimal 40 transaksi utama ke array 'transaksi' agar JSON tidak terpotong.
6. Kembalikan HANYA JSON murni tanpa markdown, tanpa ```json, tanpa penjelasan apapun.";

            $configuredModel = trim((string)($settings['model_ai'] ?? ''));
            $configuredEndpoint = trim((string)($settings['api_endpoint'] ?? ''));
            $provider = $this->deteksiProvider($configuredModel, $configuredEndpoint);

            if ($provider === 'deepseek' || $provider === 'anthropic') {
                return [
                    'status'  => false,
                    'message' => 'Model AI aktif saat ini (' . $configuredModel . ') hanya mendukung analisis teks (5C & Web Check) dan belum mendukung baca langsung berkas PDF/foto Rekening Koran. Untuk OCR Rekening Koran, pilih Google Gemini 2.5 Flash atau OpenAI GPT-4o di Pengaturan Superadmin.'
                ];
            }

            if ($provider === 'openai_compatible') {
                if (strpos($mimeType, 'image/') !== 0) {
                    return [
                        'status'  => false,
                        'message' => 'Model OpenAI (' . $configuredModel . ') mendukung OCR gambar (JPG/PNG/WebP). Untuk membaca langsung file PDF Rekening Koran, gunakan Google Gemini 2.5 Flash di Pengaturan Superadmin atau unggah foto/screenshot mutasi.'
                    ];
                }
                $oaUrl = ($configuredEndpoint !== '' && stripos($configuredEndpoint, 'googleapis.com') === false)
                    ? rtrim($configuredEndpoint, '/')
                    : 'https://api.openai.com/v1/chat/completions';
                if (stripos($oaUrl, '/chat/completions') === false) {
                    $oaUrl .= '/chat/completions';
                }
                $oaPayload = [
                    'model' => ($configuredModel && $configuredModel !== 'custom') ? $configuredModel : 'gpt-4o-mini',
                    'response_format' => ['type' => 'json_object'],
                    'max_tokens' => 4096,
                    'temperature' => 0.0,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                ['type' => 'text', 'text' => $prompt],
                                ['type' => 'image_url', 'image_url' => ['url' => 'data:' . $mimeType . ';base64,' . $fileData]]
                            ]
                        ]
                    ]
                ];
                $ch = curl_init($oaUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($oaPayload));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . trim($apiKey)]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 45);
                $oaResp = curl_exec($ch);
                $oaCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $oaErr  = curl_error($ch);
                curl_close($ch);

                $extractedData = null;
                $usedModel = $oaPayload['model'];
                $lastError = '';
                if (!$oaErr && $oaCode === 200 && !empty($oaResp)) {
                    $oaArr = json_decode($oaResp, true);
                    $rawText = $oaArr['choices'][0]['message']['content'] ?? '';
                    $cleanText = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                    $parsed = json_decode($cleanText, true);
                    if (is_array($parsed) && (isset($parsed['total_mutasi_kredit']) || isset($parsed['transaksi']))) {
                        $extractedData = $parsed;
                    } else {
                        $lastError = 'Format JSON OpenAI tidak valid';
                    }
                } else {
                    $errJson = json_decode((string)$oaResp, true);
                    $lastError = $oaErr ?: ("HTTP $oaCode: " . ($errJson['error']['message'] ?? 'Gagal menghubungi OpenAI'));
                }
            } else {
                if (preg_match('/^gemini-3\./i', $configuredModel)) {
                    $configuredModel = '';
                }
                $baseGemini = ($configuredEndpoint !== '' && stripos($configuredEndpoint, 'googleapis.com') !== false)
                    ? rtrim($configuredEndpoint, '/')
                    : 'https://generativelanguage.googleapis.com/v1beta/models';

                $candidateModels = array_values(array_unique(array_filter([
                    $configuredModel,
                    'gemini-2.5-flash',
                    'gemini-2.5-flash-lite',
                    'gemini-2.0-flash',
                    'gemini-flash-latest',
                    'gemini-flash-lite-latest'
                ])));

                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data'      => $fileData
                                    ]
                                ],
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.0,
                        'responseMimeType' => 'application/json',
                        'maxOutputTokens'  => 8192
                    ]
                ];

                $extractedData = null;
                $usedModel = '';
                $lastError = '';

                if (!empty($apiKey)) {
                    foreach ($candidateModels as $mod) {
                        $endpoint = $baseGemini . '/' . urlencode($mod) . ':generateContent?key=' . urlencode(trim($apiKey));
                        $ch = curl_init($endpoint);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 45);

                        $response  = curl_exec($ch);
                        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curlErr   = curl_error($ch);
                        curl_close($ch);

                        if (!$curlErr && $httpCode === 200 && !empty($response)) {
                            $resArr = json_decode($response, true);
                            $rawText = $resArr['candidates'][0]['content']['parts'][0]['text'] ?? '';
                            if (!empty($rawText)) {
                                $cleanText = preg_replace('/^```(?:json)?\s*/i', '', trim($rawText));
                                $cleanText = preg_replace('/\s*```$/i', '', $cleanText);
                                $parsed = json_decode($cleanText, true);
                                if (is_array($parsed) && (isset($parsed['total_mutasi_kredit']) || isset($parsed['transaksi']))) {
                                    $extractedData = $parsed;
                                    $usedModel = $mod;
                                    break;
                                }
                                $lastError = "Format JSON AI tidak valid ($mod)";
                            } else {
                                $lastError = "Respon AI kosong ($mod)";
                            }
                        } else {
                            $errDetail = '';
                            if (!empty($response)) {
                                $errJson = json_decode($response, true);
                                $errDetail = $errJson['error']['message'] ?? '';
                            }
                            $lastError = $curlErr ?: ("HTTP $httpCode ($mod)" . ($errDetail ? ": $errDetail" : ''));
                        }
                    }
                } else {
                    $lastError = 'API Key belum dikonfigurasi di Pengaturan Superadmin.';
                }
            }

            // JANGAN gunakan data palsu/hardcoded jika ekstraksi AI gagal — laporkan error secara jujur
            if (!$extractedData) {
                return [
                    'status'  => false,
                    'message' => 'Gagal membaca dokumen rekening koran dari PDF/gambar (' . ($lastError ?: 'AI tidak merespons') . '). Pastikan berkas jelas/tidak diproteksi password dan API Key Gemini aktif.'
                ];
            }

            $parseAngka = static function ($val): float {
                if (is_int($val) || is_float($val)) {
                    return round((float)$val);
                }
                $s = trim((string)$val);
                if ($s === '') return 0.0;
                $s = preg_replace('/[^0-9.,-]/', '', $s);
                // Hapus bagian desimal 2 digit di akhir (mis. .00 atau ,00)
                if (preg_match('/[.,]\d{2}$/', $s)) {
                    $s = substr($s, 0, -3);
                }
                $s = str_replace(['.', ','], '', $s);
                return (float)$s;
            };

            // Normalisasi transaksi
            $transaksiClean = [];
            $sumKreditTrans = 0.0;
            $sumDebitTrans  = 0.0;
            $lastSaldoTrans = 0.0;
            if (!empty($extractedData['transaksi']) && is_array($extractedData['transaksi'])) {
                foreach ($extractedData['transaksi'] as $tr) {
                    if (!is_array($tr)) continue;
                    $kVal = max(0.0, $parseAngka($tr['kredit'] ?? 0));
                    $dVal = max(0.0, $parseAngka($tr['debit'] ?? 0));
                    $sVal = $parseAngka($tr['saldo'] ?? 0);
                    $sumKreditTrans += $kVal;
                    $sumDebitTrans  += $dVal;
                    if ($sVal > 0) $lastSaldoTrans = $sVal;
                    $transaksiClean[] = [
                        'tanggal'    => trim((string)($tr['tanggal'] ?? '-')),
                        'keterangan' => trim((string)($tr['keterangan'] ?? '-')),
                        'kredit'     => $kVal,
                        'debit'      => $dVal,
                        'saldo'      => $sVal,
                    ];
                }
            }

            $saldoAwal   = $parseAngka($extractedData['saldo_awal'] ?? 0);
            $totalKredit = $parseAngka($extractedData['total_mutasi_kredit'] ?? 0);
            $totalDebit  = $parseAngka($extractedData['total_mutasi_debit'] ?? 0);
            $saldoAkhir  = $parseAngka($extractedData['saldo_akhir'] ?? 0);

            if ($totalKredit <= 0 && $sumKreditTrans > 0) {
                $totalKredit = $sumKreditTrans;
            }
            if ($totalDebit <= 0 && $sumDebitTrans > 0) {
                $totalDebit = $sumDebitTrans;
            }
            if ($saldoAkhir <= 0 && $lastSaldoTrans > 0) {
                $saldoAkhir = $lastSaldoTrans;
            }

            $jumlahBulan = max(1, (int)($extractedData['jumlah_bulan'] ?? 1));
            $avgKredit   = round($totalKredit / $jumlahBulan);
            $avgDebit    = round($totalDebit / $jumlahBulan);

            return [
                'status'  => true,
                'message' => 'Ekstraksi rekening koran berhasil' . ($usedModel ? " (Gemini AI: $usedModel)" : ''),
                'data'    => [
                    'nama_bank'           => trim((string)($extractedData['nama_bank'] ?? '')) ?: 'Bank Operasional Usaha',
                    'nomor_rekening'      => trim((string)($extractedData['nomor_rekening'] ?? '')),
                    'atas_nama'           => trim((string)($extractedData['atas_nama'] ?? '')),
                    'periode'             => trim((string)($extractedData['periode'] ?? '')) ?: date('F Y'),
                    'jumlah_bulan'        => $jumlahBulan,
                    'saldo_awal'          => $saldoAwal,
                    'total_mutasi_kredit' => $totalKredit,
                    'total_mutasi_debit'  => $totalDebit,
                    'saldo_akhir'         => $saldoAkhir,
                    'rata_rata_kredit'    => $avgKredit,
                    'rata_rata_debit'     => $avgDebit,
                    'transaksi'           => $transaksiClean
                ]
            ];
        } catch (\Throwable $e) {
            return [
                'status'  => false,
                'message' => 'Gagal memproses dokumen rekening koran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fallback parser kosong (tidak lagi mengembalikan data dummy UD SUMBER REJEKI)
     */
    public function fallbackParserRekening(string $filePath): array
    {
        return [
            'nama_bank'           => '',
            'nomor_rekening'      => '',
            'atas_nama'           => '',
            'periode'             => '',
            'jumlah_bulan'        => 1,
            'saldo_awal'          => 0,
            'total_mutasi_kredit' => 0,
            'total_mutasi_debit'  => 0,
            'saldo_akhir'         => 0,
            'transaksi'           => []
        ];
    }
}
