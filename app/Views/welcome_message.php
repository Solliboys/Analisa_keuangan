<?php
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit AI';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Lembaga Pembiayaan';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';
    $sloganWeb   = !empty($pengaturan_sistem['slogan_aplikasi']) ? $pengaturan_sistem['slogan_aplikasi'] : 'AI-Powered Credit Engine';
    $emailWeb    = !empty($pengaturan_sistem['email_lembaga']) ? $pengaturan_sistem['email_lembaga'] : 'info@otwkredit.id';
    $teleponWeb  = !empty($pengaturan_sistem['telepon_lembaga']) ? $pengaturan_sistem['telepon_lembaga'] : '(021) 5088-2900';
    $alamatWeb   = !empty($pengaturan_sistem['alamat_lembaga']) ? $pengaturan_sistem['alamat_lembaga'] : 'Financial Center, Jakarta Selatan';
    $modelAi     = !empty($pengaturan_ai['model_ai']) ? $pengaturan_ai['model_ai'] : 'Gemini AI';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($namaWeb) ?> — <?= esc($sloganWeb) ?></title>
    <meta name="description" content="Platform Credit Engine dan Analisis CAM modern berbasis AI 5C terintegrasi SLIK OJK.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="<?= !empty($logoWeb) ? base_url($logoWeb) : '/favicon.ico' ?>">
    
    <!-- EXTERNAL STYLESHEET -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <!-- STYLES KHUSUS SHOWCASE AI -->
    <style>
        .ai-sparkle-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #EFF6FF 0%, #EEF2FF 100%);
            color: #4F46E5;
            border: 1px solid #C7D2FE;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .ai-card-glow {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            box-shadow: 0 15px 35px -10px rgba(37, 99, 235, 0.12);
            position: relative;
            overflow: hidden;
        }
        .ai-header-accent {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: #ffffff;
            padding: 14px 18px;
            border-bottom: 1px solid #334155;
        }
        .ai-score-ring {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 100%);
            border-bottom: 1px solid #E2E8F0;
        }
        .ai-score-big {
            font-size: 2rem;
            font-weight: 800;
            color: #4F46E5;
            line-height: 1;
        }
        .ai-score-meta {
            font-size: 0.72rem;
            color: #64748B;
            font-weight: 600;
            margin-top: 3px;
        }
        .ai-pill-green {
            background: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .ai-5c-mini-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 6px;
            padding: 14px 18px;
            border-bottom: 1px solid #F1F5F9;
        }
        .ai-5c-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 8px 4px;
            text-align: center;
        }
        .ai-5c-title {
            font-size: 0.62rem;
            color: #64748B;
            font-weight: 700;
            text-transform: uppercase;
        }
        .ai-5c-val {
            font-size: 0.9rem;
            font-weight: 800;
            color: #1E293B;
            margin-top: 2px;
        }
        .ai-5c-sub {
            font-size: 0.6rem;
            color: #059669;
            font-weight: 700;
        }
        .ai-mitigasi-box {
            padding: 12px 18px;
            background: #FAFAFA;
            font-size: 0.75rem;
            color: #475569;
            line-height: 1.45;
            border-bottom: 1px solid #F1F5F9;
        }
        .ai-bottom-bar {
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.72rem;
            color: #64748B;
            background: #FFFFFF;
        }
        @media (max-width: 640px) {
            .ai-5c-mini-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* LAYAR PENUH (FULL WIDTH) */
        .container {
            max-width: 95% !important;
            width: 95% !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* SUSUNAN 4 KARTU FITUR DALAM 1 BARIS SEJAJAR */
        .features-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
        }

        .hero-grid {
            grid-template-columns: 1.1fr 0.9fr !important;
            gap: 48px !important;
        }

        .section-header {
            max-width: 850px !important;
        }

        @media (max-width: 1024px) {
            .container {
                max-width: 96% !important;
                width: 96% !important;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .hero-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 640px) {
            .features-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="brand">
                    <?php if (!empty($logoWeb)): ?>
                        <img src="<?= base_url($logoWeb) ?>" alt="Logo" style="height: 34px; width: auto; max-width: 140px; object-fit: contain; border-radius: 6px;">
                    <?php else: ?>
                        <div class="brand-icon" style="background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <span class="brand-text"><?= esc($namaWeb) ?></span>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="#fitur" class="nav-link">Fitur AI</a></li>
                    <li><a href="#alur-kerja" class="nav-link">Alur Kerja</a></li>
                    <li><a href="#manfaat" class="nav-link">Keunggulan</a></li>
                    <li><a href="#kontak" class="nav-link">Kontak</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="/login" class="btn btn-primary btn-sm">Masuk Portal &rarr;</a>
                </div>

                <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="12" y2="12"/>
                        <line x1="4" x2="20" y1="6" y2="6"/>
                        <line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <!-- Left Column (Ringkas & Padat) -->
                <div class="hero-content">
                    <div>
                        <span class="ai-sparkle-badge">
                            ✨ AI 5C Credit Engine &bull; <?= esc($modelAi) ?>
                        </span>
                    </div>

                    <h1 class="hero-title">
                        Evaluasi Kelayakan Kredit Cepat &amp; Presisi dengan AI 5C
                    </h1>

                    <p class="hero-desc">
                        Otomasi penyusunan <strong>Credit Assessment Memorandum (CAM)</strong> berbasis kecerdasan buatan. Evaluasi 5C (*Character, Capacity, Capital, Collateral, Condition*), integrasi SLIK OJK, dan rekomendasi komite dalam hitungan detik.
                    </p>

                    <div class="hero-cta">
                        <a href="/login" class="btn btn-primary">
                            <span>Buka Portal Sistem</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" x2="19" y1="12" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        <a href="#fitur" class="btn btn-outline">
                            <span>Pelajari Fitur AI &darr;</span>
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">95%</span>
                            <span class="stat-label">Lebih Cepat</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5C</span>
                            <span class="stat-label">Pilar Terotomasi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Standar OJK</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column (LIVE AI EVALUATION SHOWCASE CARD) -->
                <div class="hero-visual">
                    <div class="ai-card-glow">
                        <!-- Top Header -->
                        <div class="ai-header-accent">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                                    <strong style="font-size: 0.8rem; letter-spacing: 0.05em; text-transform: uppercase;">Inferensi AI 5C</strong>
                                </div>
                                <span style="font-size: 0.68rem; color: #94A3B8; font-family: 'JetBrains Mono', monospace;">CAM-2026-LIVE</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #CBD5E1; margin-top: 3px;">
                                Debitur: <strong>PT Cahaya Surya Sejahtera</strong> (Plafon Rp 250 Jt)
                            </div>
                        </div>

                        <!-- Big AI Score Header -->
                        <div class="ai-score-ring">
                            <div>
                                <div class="ai-score-big">88 <span style="font-size: 1rem; color: #64748B; font-weight: 600;">/ 100</span></div>
                                <div class="ai-score-meta">Skor Kelayakan AI: <strong style="color: #4F46E5;">Sangat Layak</strong></div>
                            </div>
                            <div style="text-align: right;">
                                <span class="ai-pill-green">● Disetujui Penuh</span>
                                <div style="font-size: 0.68rem; color: #64748B; margin-top: 4px;">Tenor 24 Bln &bull; 2.5%/bln</div>
                            </div>
                        </div>

                        <!-- 5C Evaluation Parameter Grid -->
                        <div class="ai-5c-mini-grid">
                            <div class="ai-5c-box">
                                <div class="ai-5c-title">Character</div>
                                <div class="ai-5c-val">88</div>
                                <div class="ai-5c-sub">Kol 1 Lancar</div>
                            </div>
                            <div class="ai-5c-box">
                                <div class="ai-5c-title">Capacity</div>
                                <div class="ai-5c-val">82</div>
                                <div class="ai-5c-sub">DSCR 1.45x</div>
                            </div>
                            <div class="ai-5c-box">
                                <div class="ai-5c-title">Capital</div>
                                <div class="ai-5c-val">80</div>
                                <div class="ai-5c-sub">Kas Sehat</div>
                            </div>
                            <div class="ai-5c-box">
                                <div class="ai-5c-title">Collateral</div>
                                <div class="ai-5c-val">92</div>
                                <div class="ai-5c-sub">LTV 135%</div>
                            </div>
                            <div class="ai-5c-box">
                                <div class="ai-5c-title">Condition</div>
                                <div class="ai-5c-val">80</div>
                                <div class="ai-5c-sub">Prospektif</div>
                            </div>
                        </div>

                        <!-- AI Reasoning & Mitigasi Box -->
                        <div class="ai-mitigasi-box">
                            <div style="font-weight: 700; color: #1E293B; margin-bottom: 2px;">
                                🧠 Rekomendasi &amp; Mitigasi AI:
                            </div>
                            <p style="margin: 0; font-size: 0.72rem;">
                                Arus kas surplus (DSCR 1.45x) dan nilai likuidasi agunan menutup 135% plafon pinjaman. Risiko gagal bayar rendah.
                            </p>
                        </div>

                        <!-- Footer Info -->
                        <div class="ai-bottom-bar">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                                </svg>
                                <span>SLIK Terverifikasi &bull; Dokumen CAM Ready</span>
                            </div>
                            <span style="font-weight: 700; color: #059669;">Siap Cetak PDF</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION (FOKUS AI & EFISIEN) -->
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="section-header">
                <span class="ai-sparkle-badge center">Teknologi Inti</span>
                <h2 class="section-title">Otomasi Analisis Kredit 5C Bertenaga AI</h2>
                <p class="section-desc">
                    Keputusan berbasis data analitik yang presisi, objektif, dan siap diaudit regulator.
                </p>
            </div>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon-box" style="background: #EEF2FF; color: #4F46E5;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Evaluasi 5C Berbasis AI</h3>
                    <p class="feature-card-desc">
                        Memindai Character, Capacity, Capital, Collateral, dan Condition untuk menghasilkan skor kelayakan objektif 0–100.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <line x1="3" x2="21" y1="9" y2="9"/>
                            <line x1="9" x2="9" y1="21" y2="9"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Kalkulasi Rasio Keuangan</h3>
                    <p class="feature-card-desc">
                        Hitung rasio DSCR, LTV, net cashflow, dan simulasi angsuran secara real-time tanpa rumus manual.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" x2="16.65" y1="21" y2="16.65"/>
                            <path d="m9 11 2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Verifikasi SLIK OJK (iDeb)</h3>
                    <p class="feature-card-desc">
                        Deteksi status kolektibilitas (Kol 1 - 5), riwayat fasilitas kredit aktif, dan catatan tunggakan nasabah.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" x2="8" y1="13" y2="13"/>
                            <line x1="16" x2="8" y1="17" y2="17"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Penerbitan Dokumen CAM PDF</h3>
                    <p class="feature-card-desc">
                        Otomasi penyusunan Credit Assessment Memorandum (CAM) berstandar perbankan siap cetak resmi dalam 1 klik.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR KERJA SECTION (4 LANGKAH RINGKAS) -->
    <section class="workflow-section" id="alur-kerja">
        <div class="container">
            <div class="section-header">
                <span class="pill-badge center">Alur Kerja</span>
                <h2 class="section-title">4 Langkah Alur Analisis Cerdas</h2>
                <p class="section-desc">
                    Proses terstruktur dari pengajuan awal hingga keputusan komite kredit.
                </p>
            </div>

            <div class="workflow-grid">
                <div class="workflow-card">
                    <div class="step-circle">1</div>
                    <h3 class="workflow-title">Input Berkas &amp; Agunan</h3>
                    <p class="workflow-desc">
                        Input data calon nasabah dan unggah dokumen agunan secara paperless.
                    </p>
                </div>

                <div class="workflow-card">
                    <div class="step-circle">2</div>
                    <h3 class="workflow-title">Verifikasi SLIK OJK</h3>
                    <p class="workflow-desc">
                        Pemeriksaan riwayat iDeb dan kolektibilitas fasilitas aktif.
                    </p>
                </div>

                <div class="workflow-card">
                    <div class="step-circle" style="background: #4F46E5; color: white;">✨ 3</div>
                    <h3 class="workflow-title">Inferensi AI 5C</h3>
                    <p class="workflow-desc">
                        Model AI menghitung skor kelayakan (0-100) dan mitigasi risiko.
                    </p>
                </div>

                <div class="workflow-card">
                    <div class="step-circle">4</div>
                    <h3 class="workflow-title">Sidang Komite &amp; CAM</h3>
                    <p class="workflow-desc">
                        Reviewer menentukan keputusan dan menerbitkan berkas resmi CAM PDF.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- DARK STATS HIGHLIGHT BANNER -->
    <section class="dark-banner-section" id="manfaat">
        <div class="container">
            <div class="dark-banner-card">
                <div class="dark-banner-header">
                    <span class="dark-badge">Dampak Efisiensi</span>
                    <h2 class="dark-title">Kecepatan &amp; Akurasi Analisis Kredit</h2>
                    <p class="dark-desc">
                        Hilangkan potensi kesalahan hitung rasio dan percepat waktu analisis pembiayaan secara signifikan.
                    </p>
                </div>

                <div class="dark-stats-grid">
                    <div class="dark-stat-col">
                        <div class="dark-stat-number">&lt; 10 Menit</div>
                        <div class="dark-stat-label">Analisis &amp; Dokumen CAM</div>
                        <div class="dark-stat-desc">
                            Pembuatan draft Memorandum Analisis Kredit selesai dalam hitungan menit.
                        </div>
                    </div>

                    <div class="dark-stat-col">
                        <div class="dark-stat-number">5 Pilar</div>
                        <div class="dark-stat-label">Evaluasi 5C Objektif</div>
                        <div class="dark-stat-desc">
                            Character, Capacity, Capital, Collateral, Condition teranalisis simultan.
                        </div>
                    </div>

                    <div class="dark-stat-col">
                        <div class="dark-stat-number">100%</div>
                        <div class="dark-stat-label">Four-Eyes Principle</div>
                        <div class="dark-stat-desc">
                            Hierarki otorisasi berjenjang sesuai SOP kepatuhan perbankan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION SECTION -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <span class="ai-sparkle-badge center">Mulai Transformasi</span>
                <h2 class="cta-title">Siap Mempercepat Analisis Kelayakan Kredit Anda?</h2>
                <p class="cta-desc">
                    Terapkan kecerdasan buatan 5C dan otomasi CAM hari ini. Percepat proses persetujuan dan minimalkan risiko NPL.
                </p>

                <div class="cta-buttons">
                    <a href="<?= base_url('login') ?>" class="btn btn-primary">
                        <span>Masuk ke Sistem</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" x2="19" y1="12" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER (DISESUAIKAN DENGAN PENGATURAN & BISA DIUBAH) -->
    <footer class="footer" id="kontak">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Identitas Lembaga & Logo -->
                <div class="footer-brand-col">
                    <a href="/" class="footer-logo">
                        <?php if (!empty($logoWeb)): ?>
                            <img src="<?= base_url($logoWeb) ?>" alt="Logo" style="height: 30px; width: auto; max-width: 140px; object-fit: contain; border-radius: 6px;">
                        <?php else: ?>
                            <div class="brand-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <span><?= esc($namaWeb) ?></span>
                    </a>
                    <p class="footer-desc">
                        <strong><?= esc($namaLembaga) ?></strong><br>
                        <?= esc($sloganWeb) ?>
                    </p>
                </div>

                <!-- Col 2: Modul Utama -->
                <div>
                    <h4 class="footer-col-title">Modul Engine</h4>
                    <ul class="footer-links">
                        <li><a href="#fitur" class="footer-link">Inferensi AI 5C</a></li>
                        <li><a href="#fitur" class="footer-link">Verifikasi SLIK OJK</a></li>
                        <li><a href="#fitur" class="footer-link">Kalkulasi DSCR &amp; LTV</a></li>
                        <li><a href="#fitur" class="footer-link">Cetak Dokumen CAM PDF</a></li>
                    </ul>
                </div>

                <!-- Col 3: Navigasi Platform -->
                <div>
                    <h4 class="footer-col-title">Navigasi Platform</h4>
                    <ul class="footer-links">
                        <li><a href="#fitur" class="footer-link">Fitur AI 5C</a></li>
                        <li><a href="#alur-kerja" class="footer-link">Alur Kerja Analisis</a></li>
                        <li><a href="#manfaat" class="footer-link">Keunggulan Engine</a></li>
                        <li><a href="#kontak" class="footer-link">Informasi Lembaga</a></li>
                    </ul>
                </div>

                <!-- Col 4: KONTAK RESMI (DISINKRON DENGAN PENGATURAN) -->
                <div>
                    <h4 class="footer-col-title">Kontak Resmi</h4>
                    <ul class="footer-contact-list">
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span><?= esc($alamatWeb) ?></span>
                        </li>
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <span><?= esc($emailWeb) ?></span>
                        </li>
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <span><?= esc($teleponWeb) ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; <?= esc($namaLembaga) ?>. Seluruh hak cipta dilindungi.
                </div>
                <div class="footer-legal-links">
                    <a href="#kebijakan">Kebijakan Privasi</a>
                    <a href="#syarat">Syarat &amp; Ketentuan</a>
                    <a href="#keamanan">Keamanan Data</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- EXTERNAL JAVASCRIPT -->
    <script src="<?= base_url('js/main.js') ?>"></script>
</body>
</html>
