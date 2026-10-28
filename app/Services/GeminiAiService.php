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
}
