<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTW Kredit — Platform Analisis Kredit &amp; Operasional BPR Terintegrasi</title>
    <meta name="description" content="Platform sistem otomasi analisis kredit modern untuk Bank Perekonomian Rakyat (BPR). Cepat, aman, transparan, dan sesuai standar regulasi OJK.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    
    <!-- EXTERNAL STYLESHEET -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>

    <!-- NAVBAR -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="brand">
                    <div class="brand-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <span class="brand-text">OTW<span>Kredit</span></span>
                </a>

                <ul class="nav-menu" id="navMenu">
                    <li><a href="#fitur" class="nav-link">Fitur</a></li>
                    <li><a href="#manfaat" class="nav-link">Manfaat</a></li>
                    <li><a href="#alur-kerja" class="nav-link">Alur Kerja</a></li>
                    <li><a href="#testimoni" class="nav-link">Testimoni</a></li>
                    <li><a href="#kontak" class="nav-link">Kontak</a></li>
                </ul>

                <div class="nav-actions">
                    <a href="<?= base_url('login') ?>" class="nav-login">Masuk</a>
                    <a href="#demoModal" onclick="openModal(event)" class="btn btn-primary btn-sm">Hubungi Kami</a>
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
                <!-- Left Column -->
                <div class="hero-content">
                    <div>
                        <span class="pill-badge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                            </svg>
                            Platform Analisis Kredit &amp; Operasional BPR
                        </span>
                    </div>

                    <h1 class="hero-title">
                        Kelola Kredit BPR Lebih Cepat, Aman, dan Transparan
                    </h1>

                    <p class="hero-desc">
                        Tingkatkan efisiensi kerja operasional BPR Anda mulai dari pengajuan pinjaman hingga verifikasi data nasabah secara otomatis dan transparan dalam satu platform modern terintegrasi.
                    </p>

                    <div class="hero-cta">
                        <a href="#demoModal" onclick="openModal(event)" class="btn btn-primary">
                            Mulai Sekarang
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" x2="19" y1="12" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        <a href="#demoModal" onclick="openModal(event)" class="btn btn-outline">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                            Lihat Demo
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">BPR Terdaftar</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">50.000+</span>
                            <span class="stat-label">Total Kredit Diproses</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">99.9%</span>
                            <span class="stat-label">Uptime &amp; Keamanan</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Dashboard Preview Card) -->
                <div class="hero-visual">
                    <div class="dashboard-card">
                        <!-- Top Header -->
                        <div class="dash-header">
                            <div class="dash-title-wrap">
                                <span class="dash-title">Ringkasan Portofolio BPR</span>
                                <span class="dash-sub">Pembaruan otomatis real-time</span>
                            </div>
                            <span class="dash-badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                    <polyline points="17 6 23 6 23 12"/>
                                </svg>
                                +12.4% MoM
                            </span>
                        </div>

                        <!-- 3 Metric Boxes -->
                        <div class="dash-metrics-row">
                            <div class="dash-metric-box">
                                <div class="metric-box-label">Total Plafon</div>
                                <div class="metric-box-val">Rp 48.5 M</div>
                                <div class="metric-box-sub">▲ +8.2% bulan ini</div>
                            </div>
                            <div class="dash-metric-box">
                                <div class="metric-box-label">NPL Ratio</div>
                                <div class="metric-box-val">1.85%</div>
                                <div class="metric-box-sub">Sehat &lt; 5% OJK</div>
                            </div>
                            <div class="dash-metric-box">
                                <div class="metric-box-label">Nasabah Aktif</div>
                                <div class="metric-box-val">1,420</div>
                                <div class="metric-box-sub primary">Debitur BPR</div>
                            </div>
                        </div>

                        <!-- Mini SVG Chart -->
                        <div class="dash-chart-container">
                            <div class="chart-header">
                                <span>Tren Penyaluran Kredit (6 Bulan Terakhir)</span>
                                <span style="color: var(--primary); font-weight:700;">Target 96%</span>
                            </div>
                            <svg class="chart-svg" viewBox="0 0 380 90" fill="none">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#2563EB" stop-opacity="0.25"/>
                                        <stop offset="100%" stop-color="#2563EB" stop-opacity="0.0"/>
                                    </linearGradient>
                                </defs>
                                <line x1="0" y1="75" x2="380" y2="75" stroke="#E2E8F0" stroke-dasharray="3 3"/>
                                <line x1="0" y1="45" x2="380" y2="45" stroke="#E2E8F0" stroke-dasharray="3 3"/>
                                <line x1="0" y1="15" x2="380" y2="15" stroke="#E2E8F0" stroke-dasharray="3 3"/>
                                <path d="M 10,70 Q 70,60 130,48 T 250,30 T 370,12 L 370,85 L 10,85 Z" fill="url(#chartGradient)"/>
                                <path d="M 10,70 Q 70,60 130,48 T 250,30 T 370,12" stroke="#2563EB" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                <circle cx="370" cy="12" r="4.5" fill="#2563EB" stroke="#FFFFFF" stroke-width="2"/>
                            </svg>
                        </div>

                        <!-- Live Credit List -->
                        <div class="dash-table-wrap">
                            <div class="table-title">Pengajuan Kredit Terbaru</div>
                            <div class="credit-item">
                                <div class="credit-info">
                                    <span class="credit-name">PT Cahaya Surya Sejahtera</span>
                                    <span class="credit-plafon">Kredit Modal Kerja • Rp 450.000.000</span>
                                </div>
                                <span class="status-badge success">
                                    ● Disetujui
                                </span>
                            </div>
                            <div class="credit-item">
                                <div class="credit-info">
                                    <span class="credit-name">CV Berkah Jaya Sentosa</span>
                                    <span class="credit-plafon">Kredit Investasi • Rp 120.000.000</span>
                                </div>
                                <span class="status-badge warning">
                                    ● Verifikasi SLIK
                                </span>
                            </div>
                        </div>

                        <!-- Security indicator -->
                        <div class="dash-footer-info">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            </svg>
                            Enkripsi 256-bit Bank Grade • Kepatuhan Regulasi OJK
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CLIENT LOGOS / TRUSTED SECTION -->
    <section class="clients-section">
        <div class="container">
            <div class="clients-title">Dipercaya oleh berbagai lembaga keuangan di seluruh Indonesia</div>
            <div class="clients-grid">
                <div class="client-logo-item">
                    <span class="client-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 2 2 7h20L12 2z"/></svg>
                    </span>
                    BPR Surya Sejahtera
                </div>
                <div class="client-logo-item">
                    <span class="client-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    BPR Berkah Artha Mandiri
                </div>
                <div class="client-logo-item">
                    <span class="client-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    BPR Mitra Bersama
                </div>
                <div class="client-logo-item">
                    <span class="client-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    </span>
                    BPR Digital Dana
                </div>
                <div class="client-logo-item">
                    <span class="client-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </span>
                    BPR Buana Kencana
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features-section" id="fitur">
        <div class="container">
            <div class="section-header">
                <span class="pill-badge center">Fitur Unggulan</span>
                <h2 class="section-title">Semua yang Anda butuhkan untuk mengelola kredit</h2>
                <p class="section-desc">
                    Fitur komprehensif yang dirancang khusus untuk memenuhi standar regulasi dan alur operasional perbankan BPR modern di era digital.
                </p>
            </div>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" x2="8" y1="13" y2="13"/>
                            <line x1="16" x2="8" y1="17" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Manajemen Pengajuan Kredit</h3>
                    <p class="feature-card-desc">
                        Kelola seluruh pengajuan kredit dalam satu alur terpusat, mulai dari intake berkas, validasi agunan, hingga persetujuan komite kredit.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" x2="16.65" y1="21" y2="16.65"/>
                            <path d="m9 11 2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Koneksi Akses SLIK OJK</h3>
                    <p class="feature-card-desc">
                        Pemeriksaan riwayat debitur terintegrasi iDeb SLIK OJK cepat, otomatis mengevaluasi kolektibilitas dan beban angsuran debitur secara akurat.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <line x1="3" x2="21" y1="9" y2="9"/>
                            <line x1="9" x2="9" y1="21" y2="9"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Analisis Keuangan Terintegrasi</h3>
                    <p class="feature-card-desc">
                        Perhitungan rasio finansial otomatis (DSCR, DTI, Cash Flow, LTV) memudahkan analisis kelayakan pembiayaan dengan rekomendasi analitik objektif.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Dashboard Kinerja Real-Time</h3>
                    <p class="feature-card-desc">
                        Pantau penyaluran kredit, tren NPL, performa account officer (AO), hingga portofolio jatuh tempo secara instan lewat visualisasi interaktif.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Kontrol Akses Cerdas &amp; Role</h3>
                    <p class="feature-card-desc">
                        Hierarki otorisasi berjenjang untuk Admin, Account Officer, Komite Kredit, hingga Direksi sesuai SOP kepatuhan perbankan dan four-eyes principle.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card">
                    <div class="feature-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" x2="22" y1="12" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <h3 class="feature-card-title">Standar OJK &amp; Keamanan</h3>
                    <p class="feature-card-desc">
                        Sistem dirancang sesuai ketentuan POJK, dilengkapi enkripsi data end-to-end serta audit log aktivitas pengguna yang tidak dapat dimanipulasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR KERJA SECTION -->
    <section class="workflow-section" id="alur-kerja">
        <div class="container">
            <div class="section-header">
                <span class="pill-badge center">Alur Kerja</span>
                <h2 class="section-title">Alur kerja yang sederhana</h2>
                <p class="section-desc">
                    Empat langkah mudah mentransformasi proses kredit konvensional menjadi serba terotomasi, cepat, dan presisi.
                </p>
            </div>

            <div class="workflow-grid">
                <!-- Step 1 -->
                <div class="workflow-card">
                    <div class="step-circle">1</div>
                    <h3 class="workflow-title">Pengajuan Kredit</h3>
                    <p class="workflow-desc">
                        Input data calon nasabah secara ringkas dan unggah berkas legalitas serta dokumen agunan secara paperless.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="workflow-card">
                    <div class="step-circle">2</div>
                    <h3 class="workflow-title">Verifikasi &amp; SLIK</h3>
                    <p class="workflow-desc">
                        Otomatisasi pencarian data riwayat debitur, analisa baki debet, serta identifikasi risiko kredit di SLIK iDeb OJK.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="workflow-card">
                    <div class="step-circle">3</div>
                    <h3 class="workflow-title">Keputusan Kredit</h3>
                    <p class="workflow-desc">
                        Rekomendasi sistem berbasis analitik rasio finansial membantu komite kredit mengambil keputusan dengan objektif.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="workflow-card">
                    <div class="step-circle">4</div>
                    <h3 class="workflow-title">Pencairan &amp; Monitoring</h3>
                    <p class="workflow-desc">
                        Penerbitan dokumen akad, pencairan fasilitas, dan monitoring portofolio cicilan berkala secara otomatis.
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
                    <span class="dark-badge">Dampak Transformasi Digital</span>
                    <h2 class="dark-title">Dirancang untuk melipatgandakan produktivitas kredit BPR</h2>
                    <p class="dark-desc">
                        Tinggalkan proses manual yang lambat dan rentan risiko human error, beralihlah ke ekosistem operasional kredit modern yang efisien dan siap diaudit.
                    </p>
                </div>

                <div class="dark-stats-grid">
                    <div class="dark-stat-col">
                        <div class="dark-stat-number">40%</div>
                        <div class="dark-stat-label">Lebih Cepat</div>
                        <div class="dark-stat-desc">
                            Waktu pemrosesan kredit dari pengajuan awal hingga pencairan dana terpangkas signifikan.
                        </div>
                    </div>

                    <div class="dark-stat-col">
                        <div class="dark-stat-number">0 Kertas</div>
                        <div class="dark-stat-label">Transformasi Paperless</div>
                        <div class="dark-stat-desc">
                            Penyimpanan arsip digital aman, pencarian dokumen debitur hitungan detik tanpa tumpukan map fisik.
                        </div>
                    </div>

                    <div class="dark-stat-col">
                        <div class="dark-stat-number">100%</div>
                        <div class="dark-stat-label">Sesuai Regulasi</div>
                        <div class="dark-stat-desc">
                            Penerapan prinsip kehati-hatian (prudential banking) sesuai ketentuan dan format pelaporan OJK.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section class="testimonials-section" id="testimoni">
        <div class="container">
            <div class="section-header">
                <span class="pill-badge center">Testimoni</span>
                <h2 class="section-title">Dipercaya oleh tim BPR</h2>
                <p class="section-desc">
                    Pengalaman langsung para pimpinan dan praktisi BPR yang telah memodernisasi operasional kredit mereka.
                </p>
            </div>

            <div class="testimonials-grid" id="testimonialsGrid">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div>
                        <div class="stars-row">★★★★★</div>
                        <p class="testimonial-text">
                            "Proses analisa kredit menjadi jauh lebih terstruktur. Pengecekan data dan perhitungan rasio yang dulunya manual kini selesai dalam hitungan menit tanpa khawatir salah hitung."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">HW</div>
                        <div class="author-info">
                            <span class="author-name">Hendra Wijaya</span>
                            <span class="author-role">Direktur Operasional, BPR Sejahtera</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div>
                        <div class="stars-row">★★★★★</div>
                        <p class="testimonial-text">
                            "Efisiensi tim AO kami meningkat drastis. Komite kredit dapat memeriksa berkas dan memberikan persetujuan kapan saja secara transparan, akurat, dan akuntabel."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">MD</div>
                        <div class="author-info">
                            <span class="author-name">Meilani Dewi</span>
                            <span class="author-role">Credit Operations Officer, BPR Artha Kencana</span>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div>
                        <div class="stars-row">★★★★★</div>
                        <p class="testimonial-text">
                            "Sangat membantu dalam menjaga kualitas portofolio dan pemantauan NPL. Sistem audit trail dan kepatuhan regulasi OJK-nya sangat solid dan memuaskan."
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">BT</div>
                        <div class="author-info">
                            <span class="author-name">Bambang Trihatmojo</span>
                            <span class="author-role">Komisaris Utama, BPR Mitra Dana</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM REVIEW / ULASAN -->
            <div class="review-form-wrapper" id="formReviewSection">
                <div class="review-success-alert" id="reviewSuccessAlert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <span>Terima kasih! Ulasan Anda berhasil dikirim dan ditambahkan ke daftar testimoni di atas.</span>
                </div>

                <div class="review-form-header">
                    <div class="review-form-title">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <line x1="9" x2="15" y1="10" y2="10"/>
                            <line x1="12" x2="12" y1="7" y2="13"/>
                        </svg>
                        Tulis Ulasan &amp; Pengalaman Anda
                    </div>

                    <div class="star-rating-select">
                        <span class="star-label">Rating:</span>
                        <input type="hidden" id="selectedRating" value="5">
                        <div class="stars-btn-group" id="starBtnGroup">
                            <button type="button" class="star-btn active" data-val="1" onclick="setRating(1)" title="1 Bintang">★</button>
                            <button type="button" class="star-btn active" data-val="2" onclick="setRating(2)" title="2 Bintang">★</button>
                            <button type="button" class="star-btn active" data-val="3" onclick="setRating(3)" title="3 Bintang">★</button>
                            <button type="button" class="star-btn active" data-val="4" onclick="setRating(4)" title="4 Bintang">★</button>
                            <button type="button" class="star-btn active" data-val="5" onclick="setRating(5)" title="5 Bintang">★</button>
                        </div>
                    </div>
                </div>

                <form id="reviewForm" onsubmit="handleReviewSubmit(event)">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="reviewerName">Nama Lengkap</label>
                        <input type="text" id="reviewerName" class="form-input" placeholder="Contoh: Budi Pratama" required>
                    </div>

                    <div class="form-group" style="margin-top: 16px; margin-bottom: 0;">
                        <label class="form-label" for="reviewerComment">Ulasan / Komentar</label>
                        <textarea id="reviewerComment" class="review-textarea" placeholder="Tuliskan ulasan atau pengalaman Anda di sini..." required></textarea>
                    </div>

                    <div class="review-form-footer">
                        <div class="review-notice">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" x2="12" y1="8" y2="12"/>
                                <line x1="12" x2="12.01" y1="16" y2="16"/>
                            </svg>
                            Ulasan Anda akan langsung ditampilkan secara transparan di sistem.
                        </div>
                        <button type="submit" class="btn btn-primary" id="btnSubmitReview">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" x2="11" y1="2" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Kirim Ulasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- CALL TO ACTION SECTION -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <span class="pill-badge center">Mulai Transformasi Digital</span>
                <h2 class="cta-title">Siap meningkatkan efisiensi proses kredit BPR Anda?</h2>
                <p class="cta-desc">
                    Bergabunglah dengan puluhan BPR inovatif lainnya dan rasakan kemudahan pengelolaan kredit yang lebih cepat, aman, dan taat regulasi mulai hari ini.
                </p>

                <div class="cta-buttons">
                    <a href="#demoModal" onclick="openModal(event)" class="btn btn-primary">
                        Mulai Sekarang
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" x2="19" y1="12" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                    <a href="#demoModal" onclick="openModal(event)" class="btn btn-outline">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                        Jadwalkan Demo
                    </a>
                </div>

                <div class="cta-benefits">
                    <div class="benefit-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Registrasi Cepat
                    </div>
                    <div class="benefit-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Dukungan Onboarding Penuh
                    </div>
                    <div class="benefit-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Sesuai Regulasi OJK
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer" id="kontak">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1 -->
                <div class="footer-brand-col">
                    <a href="/" class="footer-logo">
                        <div class="brand-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                <line x1="2" x2="22" y1="10" y2="10"/>
                            </svg>
                        </div>
                        OTW<span>Kredit</span>
                    </a>
                    <p class="footer-desc">
                        Platform sistem otomasi dan analisis kredit modern terintegrasi untuk Bank Perekonomian Rakyat (BPR). Mempercepat proses persetujuan, meminimalisir NPL, dan memastikan kepatuhan regulasi perbankan.
                    </p>
                </div>

                <!-- Col 2 -->
                <div>
                    <h4 class="footer-col-title">Produk</h4>
                    <ul class="footer-links">
                        <li><a href="#fitur" class="footer-link">Sistem Analisis Kredit</a></li>
                        <li><a href="#fitur" class="footer-link">Otomasi SLIK iDeb</a></li>
                        <li><a href="#fitur" class="footer-link">Dashboard Portofolio</a></li>
                        <li><a href="#fitur" class="footer-link">Scoring Kelayakan Debitur</a></li>
                        <li><a href="#fitur" class="footer-link">Manajemen Dokumen Legal</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <h4 class="footer-col-title">Perusahaan</h4>
                    <ul class="footer-links">
                        <li><a href="#manfaat" class="footer-link">Tentang Kami</a></li>
                        <li><a href="#testimoni" class="footer-link">Keamanan Data &amp; Privasi</a></li>
                        <li><a href="#alur-kerja" class="footer-link">Standar Regulasi OJK</a></li>
                        <li><a href="#kontak" class="footer-link">Bantuan &amp; Panduan</a></li>
                        <li><a href="#kontak" class="footer-link">Hubungi Kami</a></li>
                        <li><a href="<?= base_url('login') ?>" class="footer-link">Login</a></li>
                    </ul>
                </div>

                <!-- Col 4 -->
                <div>
                    <h4 class="footer-col-title">Kontak</h4>
                    <ul class="footer-contact-list">
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>BPR Financial Center, Lantai 8, Jakarta Selatan</span>
                        </li>
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="16" x="2" y="4" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <span>info@otwkredit.id</span>
                        </li>
                        <li class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <span>(021) 5088-2900</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> OTW Kredit. Hak Cipta Dilindungi Undang-Undang.
                </div>
                <div class="footer-legal-links">
                    <a href="#kebijakan">Kebijakan Privasi</a>
                    <a href="#syarat">Syarat &amp; Ketentuan</a>
                    <a href="#keamanan">Keamanan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE DEMO MODAL -->
    <div class="modal-overlay" id="demoModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h3 class="modal-title">Jadwalkan Demo OTW Kredit</h3>
            <p class="modal-desc">
                Isi data BPR Anda dan tim kami akan segera menghubungi untuk memberikan akses demo platform interaktif.
            </p>
            <form onsubmit="handleDemoSubmit(event)">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-input" placeholder="Contoh: Hendra Wijaya" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp / Telepon</label>
                    <input type="tel" class="form-input" placeholder="Contoh: 08123456789" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Kerja</label>
                    <input type="email" class="form-input" placeholder="Contoh: hendra@bprsurya.co.id" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 12px;">
                    Kirim Permintaan Demo
                </button>
            </form>
        </div>
    </div>

    <!-- EXTERNAL JAVASCRIPT -->
    <script src="<?= base_url('js/main.js') ?>"></script>
</body>
</html>
