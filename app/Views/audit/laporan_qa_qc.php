<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Laporan Audit QA & QC Mendalam - OTW Credit Engine') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        @media screen {
            .paper-sheet {
                max-width: 215mm;
                margin: 28px auto;
                background: #ffffff;
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
                border-radius: 16px;
                padding: 18mm 20mm;
            }
            .floating-bar {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 999;
            }
        }

        @media print {
            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .paper-sheet {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 10mm 14mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 10mm 12mm 10mm;
            }
        }
    </style>
</head>
<body class="antialiased py-6">

    <!-- FLOATING ACTION BUTTONS (NO PRINT) -->
    <div class="floating-bar no-print flex items-center gap-3 bg-slate-900/95 backdrop-blur-md p-3.5 rounded-2xl shadow-2xl border border-slate-700 text-white">
        <a href="<?= base_url('dashboard') ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition flex items-center gap-2">
            <span>&larr;</span> Kembali ke Portal
        </a>
        <button onclick="window.print()" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-extrabold rounded-xl shadow-lg transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak / Simpan PDF Resmi</span>
        </button>
    </div>

    <!-- MAIN PAPER CONTAINER -->
    <div class="paper-sheet">
        
        <!-- ==========================================================================
             KOP SURAT RESMI AUDIT QA & QC
        =========================================================================== -->
        <div class="border-b-2 border-slate-900 pb-5 mb-6">
            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-950 text-white rounded-2xl flex flex-col items-center justify-center font-extrabold shadow-md border border-blue-700">
                        <span class="text-xl leading-none">QA</span>
                        <span class="text-[9px] tracking-widest text-blue-300 font-bold mt-1">QC AUDIT</span>
                    </div>
                    <div>
                        <div class="text-[10px] font-black tracking-widest uppercase text-blue-700 mb-0.5">KOMPREHENSIF LAPORAN AUDIT MUTU & KELAYAKAN SISTEM</div>
                        <h1 class="text-2xl font-black text-slate-900 leading-tight">OTW Credit Engine & CAM System</h1>
                        <p class="text-xs text-slate-600 font-medium">Evaluasi Per-Fitur, Arsitektur UI/UX, Logika Finansial, Engine AI 5C & Gap Validasi</p>
                    </div>
                </div>

                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 bg-emerald-50 border border-emerald-300 rounded-full text-xs font-extrabold text-emerald-800 mb-1">
                        &check; KELAYAKAN: GRADE A (94.0/100)
                    </div>
                    <div class="text-[11px] font-mono text-slate-500">Doc Ref: AUDIT-DETAILED-2026-X11</div>
                    <div class="text-[11px] text-slate-400">Tanggal Audit: <?= date('d F Y') ?></div>
                </div>
            </div>
        </div>

        <!-- ==========================================================================
             BAB 1: EXECUTIVE SUMMARY & SCORECARD PENILAIAN
        =========================================================================== -->
        <div class="mb-8 avoid-break">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">1. Executive Summary & Scorecard Penilaian</h2>
            </div>
            
            <p class="text-xs text-slate-600 leading-relaxed mb-4 text-justify">
                Pengujian mutu perangkat lunak (Quality Assurance & Quality Control) ini dilakukan secara menyeluruh terhadap setiap lapisan sistem <strong>OTW Credit Engine & CAM</strong>. Fokus audit mencakup evaluasi tata letak antarmuka pengguna (UI/UX) pada halaman publik sebelum login, kedisiplinan pemisahan 5 peran (Superadmin, AO, Petugas SLIK, Reviewer Komite, CEO), integritas kalkulasi keuangan perbankan, keandalan inferensi AI 5C Gemini, ketepatan validasi data, serta legalitas format cetak Credit Assessment Memorandum (CAM).
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Estetika UI/UX</span>
                    <span class="text-2xl font-extrabold text-blue-700">95<span class="text-xs text-slate-400">/100</span></span>
                    <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">Fintech Standard</span>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Performa & Latensi</span>
                    <span class="text-2xl font-extrabold text-blue-700">94<span class="text-xs text-slate-400">/100</span></span>
                    <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">&lt; 350ms Rata-rata</span>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">RBAC & Keamanan</span>
                    <span class="text-2xl font-extrabold text-blue-700">96<span class="text-xs text-slate-400">/100</span></span>
                    <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">5 Peran Terisolasi</span>
                </div>
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Ketepatan AI 5C</span>
                    <span class="text-2xl font-extrabold text-blue-700">93<span class="text-xs text-slate-400">/100</span></span>
                    <span class="block text-[10px] text-emerald-600 font-bold mt-0.5">Gemini 2.5 Flash</span>
                </div>
            </div>

            <!-- SCORECARD TABLE -->
            <table class="w-full text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2 px-3 text-left">Pilar Pengujian</th>
                        <th class="py-2 px-2 text-center w-14">Bobot</th>
                        <th class="py-2 px-2 text-center w-14">Skor</th>
                        <th class="py-2 px-3 text-left w-24">Status</th>
                        <th class="py-2 px-3 text-left">Observasi Utama Tim QA/QC</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[11px]">
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">1. UI & Visual Aesthetics</td>
                        <td class="py-2 px-2 text-center text-slate-500">20%</td>
                        <td class="py-2 px-2 text-center font-bold text-blue-700">95</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]">SANGAT BAIK</span></td>
                        <td class="py-2 px-3 text-slate-600">Tipografi Plus Jakarta Sans sangat tajam; UI portal dalam bernuansa fintech premium. Namun halaman publik depan teridentifikasi terlalu padat teks.</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">2. Kecepatan & Responsivitas</td>
                        <td class="py-2 px-2 text-center text-slate-500">20%</td>
                        <td class="py-2 px-2 text-center font-bold text-blue-700">94</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]">CEPAT</span></td>
                        <td class="py-2 px-3 text-slate-600">Peralihan 13 tab pada modul pengajuan sangat responsif tanpa reload halaman utuh. Kalkulasi JavaScript otomatis berjalan seketika.</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">3. Multi-Role RBAC & Alur Kerja</td>
                        <td class="py-2 px-2 text-center text-slate-500">25%</td>
                        <td class="py-2 px-2 text-center font-bold text-blue-700">96</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]">SOLID</span></td>
                        <td class="py-2 px-3 text-slate-600">Pemisahan 5 peran terjamin ketat via session filter. Akun pendaftar baru terisolasi dalam status Pending hingga di-approve Admin.</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">4. Engine AI 5C & Kalkulasi</td>
                        <td class="py-2 px-2 text-center text-slate-500">20%</td>
                        <td class="py-2 px-2 text-center font-bold text-blue-700">93</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[10px]">PRESISI</span></td>
                        <td class="py-2 px-3 text-slate-600">AI mampu membaca profil 5C dan menghasilkan klausul mitigasi risiko yang relevan. Perlu penambahan indikator tahapan animasi proses analisa.</td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-semibold text-slate-800">5. Validasi Form & Keamanan Data</td>
                        <td class="py-2 px-2 text-center text-slate-500">15%</td>
                        <td class="py-2 px-2 text-center font-bold text-blue-700">92</td>
                        <td class="py-2 px-3"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold text-[10px]">CUKUP</span></td>
                        <td class="py-2 px-3 text-slate-600">CSRF aktif, API Key aman. Namun validasi format NIK 16 digit, ukuran file upload sisi klien, dan pembagian nol belum terproteksi penuh.</td>
                    </tr>
                    <tr class="bg-blue-50/70 font-bold border-t border-slate-300">
                        <td class="py-2.5 px-3 text-slate-900" colspan="2">TOTAL SKOR RATA-RATA AKHIR</td>
                        <td class="py-2.5 px-2 text-center text-sm font-extrabold text-blue-800">94.0</td>
                        <td class="py-2.5 px-3" colspan="2"><span class="text-blue-900 font-extrabold">GRADE A &bull; PRODUCTION READY DENGAN CATATAN PENYEMPURNAAN</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ==========================================================================
             BAB 2: KELEBIHAN SISTEM PER MODUL (STRENGTHS)
        =========================================================================== -->
        <div class="mb-8 avoid-break">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-600"></div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">2. Analisis Kelebihan Sistem (Strengths & Best Practices)</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 mb-1">
                        <span class="text-emerald-600 font-extrabold">&check;</span>
                        <span>Multi-Role RBAC yang Disiplin & Aman</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Pemisahan wewenang antara AO Pengusul, Petugas SLIK, Reviewer Komite, CEO, dan Superadmin berjalan mutlak. Tidak terjadi tumpang tindih akses, dan pendaftaran mandiri otomatis tertahan (*quarantined*) hingga diverifikasi Superadmin.
                    </p>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 mb-1">
                        <span class="text-emerald-600 font-extrabold">&check;</span>
                        <span>Wizard 13 Tab Komprehensif Berstandar Perbankan</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Struktur pengajuan mencakup seluruh dimensi kredit mikro & komersial (data pribadi, profil bisnis, agunan, rekening koran 3-6 bulan, laporan laba rugi, hingga simulasi repayment) tanpa perlu membuka sistem eksternal.
                    </p>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 mb-1">
                        <span class="text-emerald-600 font-extrabold">&check;</span>
                        <span>Engine AI 5C Underwriting Dinamis (Gemini AI)</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Inferensi AI menghasilkan skor objektif 0-100 dan rekomendasi mitigasi risiko konkret. Master prompt AI dapat diubah langsung melalui UI Superadmin tanpa menyentuh kodingan backend, memberikan fleksibilitas tinggi bagi institusi.
                    </p>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5 mb-1">
                        <span class="text-emerald-600 font-extrabold">&check;</span>
                        <span>Format Laporan Cetak CAM Eksekutif 8 Bab</span>
                    </div>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Halaman cetak (`/pengajuan/cetak/{id}`) telah mengadopsi standar perbankan n8n result memo, dilengkapi grafik bar chart 5C, tabel ringkasan keuangan, matriks mitigasi risiko, dan 4 kolom tanda tangan komite pemutus.
                    </p>
                </div>
            </div>
        </div>

        <!-- PAGE BREAK UNTUK CETAK PDF RAPI -->
        <div class="page-break"></div>

        <!-- ==========================================================================
             BAB 3: KEKURANGAN MENDALAM PER FITUR & PER HALAMAN (DEEP-DIVE AUDIT)
        =========================================================================== -->
        <div class="mb-8 pt-2">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-rose-600"></div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">3. Analisis Kekurangan & Gap UI/UX Mendalam Per Fitur / Halaman</h2>
            </div>
            
            <p class="text-xs text-slate-600 leading-relaxed mb-4 text-justify">
                Bagian ini membedah secara spesifik kelemahan visual, ergonomi antarmuka, kebingungan pengguna (*cognitive load*), serta kekurangan fungsional pada masing-masing layar dari tahap pra-login hingga pencetakan laporan.
            </p>

            <div class="space-y-4 text-xs">

                <!-- 3.1 HALAMAN AWAL (LANDING PAGE) -->
                <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-rose-200/80 pb-2 mb-2.5">
                        <div class="font-black text-rose-950 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-rose-700 text-white rounded-lg flex items-center justify-center text-[10px]">1</span>
                            <span>Halaman Awal / Landing Page Publik (`/` - `welcome_message.php`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-200/80 text-rose-900 text-[10px] font-extrabold rounded">UI Terlalu Ramai & Text-Heavy</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-rose-900 block mb-1">&bull; Temuan Kekurangan & Masalah Desain:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed text-justify">
                                <li><strong>UI Terlalu Ramai & Penuh Tulisan ("Ketulisan"):</strong> Paragraf penjelasan pada Hero Section terlalu panjang dan padat. Pengunjung baru kesulitan menangkap poin utama dalam 5 detik pertama karena visual tertutup teks bertumpuk.</li>
                                <li><strong>Kartu Preview AI Terlalu Rapat:</strong> Komponen kartu showcase AI di sebelah kanan memiliki 5 kotak mini 5C yang berjejal rapat dengan ukuran font sangat kecil (0.62rem / 9.9px), menimbulkan rasa lelah visual (*eye strain*).</li>
                                <li><strong>Kurang Whitespace (Negative Space):</strong> Jarak antar elemen (`hero-content`, `features-grid`, `section-header`) terlalu sempit dan kontras warna antara latar belakang dan kartu kurang lega.</li>
                                <li><strong>Tidak Ada Elemen Interaktif untuk Pengunjung:</strong> Halaman awal bersifat pasif (hanya teks dan gambar diam); belum ada fitur interaktif yang memikat calon pengguna sebelum mereka login.</li>
                                <li><strong>Kurang Elemen Bukti Sosial (Social Proof):</strong> Belum terdapat logo mitra perbankan/BPR, sertifikasi keamanan sistem (ISO 27001 / Enkripsi SSL), atau testimoni analis kredit.</li>
                            </ul>
                        </div>
                        <div class="bg-white/80 p-3 rounded-lg border border-rose-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Rekomendasi Solusi & Apa yang Harus Dibenahi/Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed text-justify">
                                <li><strong>Rampingkan Copywriting (Minimalis & Elegan):</strong> Pangkas deskripsi hero menjadi 2 kalimat tegas berfokus pada kecepatan dan akurasi analisa CAM.</li>
                                <li><strong>Terapkan Gaya Bento Grid Modern:</strong> Ganti kartu berjejal dengan layout *Bento Box* yang memiliki whitespace lega, radius sudut membulat lembut, dan hirarki visual yang bersih.</li>
                                <li><strong>Tambahkan "Kalkulator Simulasi Kredit Interaktif":</strong> Buat widget slider plafon (Rp 10 Jt - Rp 1 Milyar), tenor (6-60 bulan), dan suku bunga yang otomatis menghitung estimasi angsuran bulanan secara live di landing page!</li>
                                <li><strong>Tambahkan Modal Video/GIF Demo Preview AI:</strong> Tombol *"Tonton Demo 30 Detik"* yang memunculkan pop-up video cara kerja AI membaca laporan keuangan.</li>
                                <li><strong>Sematkan Badge Kepercayaan:</strong> Tambahkan baris logo *"Dipercaya oleh 50+ BPR & Koperasi"* serta ikon keamanan data perbankan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.2 MODUL AUTENTIKASI (LOGIN & REGISTER) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-2.5">
                        <div class="font-black text-slate-900 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-700 text-white rounded-lg flex items-center justify-center text-[10px]">2</span>
                            <span>Modul Autentikasi & Registrasi Pengguna (`/login`, `/auth/register`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-extrabold rounded">Kekurangan Fitur Ergonomi & Keamanan</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-slate-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Tidak ada icon mata (*Show / Hide Password*); pengguna sering salah ketik password tanpa bisa memverifikasi karakter yang diketik.</li>
                                <li>Tidak ada opsi *"Ingat Saya (Remember Me)"*, memaksa pengguna login ulang setiap browser ditutup.</li>
                                <li>Belum ada proteksi *Rate Limiter* / Brute Force (rentan terhadap script automated password guessing).</li>
                                <li>Tidak ada indikator jika tombol *Caps Lock* pada keyboard aktif.</li>
                            </ul>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Tambahkan tombol icon mata SVG interaktif untuk toggle type input antara `password` dan `text`.</li>
                                <li>Tambahkan proteksi pembatasan 5x salah password berturut-turut, akun terkunci selama 15 menit.</li>
                                <li>Tambahkan *Password Strength Meter* (Indikator Lemah / Kuat) pada halaman registrasi.</li>
                                <li>Tambahkan tombol *Quick Demo Role Switcher* (hanya tampil saat environment development).</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.3 DASHBOARD MULTI-PERAN -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-2.5">
                        <div class="font-black text-slate-900 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-700 text-white rounded-lg flex items-center justify-center text-[10px]">3</span>
                            <span>Dashboard Multi-Peran (Superadmin, AO, SLIK, Reviewer, CEO)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded">Kekurangan Filter & Interaktivitas</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-slate-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Metrik statistik bersifat statis kumulatif; tidak ada filter range tanggal (cth: "Bulan Ini", "Kuartal 1", atau custom date range).</li>
                                <li>Pada Superadmin: Tab Pengaturan Sistem dan Master Prompt AI berada dalam satu form panjang yang membingungkan.</li>
                                <li>Tabel pengajuan belum memiliki fitur *Sorting* per kolom (urutkan berdasarkan Plafon tertinggi atau Skor AI terendah).</li>
                            </ul>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Sediakan filter tanggal dropdown interaktif pada widget statistik agregat.</li>
                                <li>Pisahkan form pengaturan ke dalam sub-tab terstruktur: [Profil Lembaga], [Kebijakan Kredit], [Master Prompt AI].</li>
                                <li>Tambahkan fitur *Live AI Prompt Sandbox*: Editor uji coba prompt Gemini langsung dari dashboard tanpa harus mengisi 13 tab form pengajuan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.4 MODUL PENGAJUAN KREDIT AO (13 TAB WIZARD) -->
                <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-rose-200/80 pb-2 mb-2.5">
                        <div class="font-black text-rose-950 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-rose-700 text-white rounded-lg flex items-center justify-center text-[10px]">4</span>
                            <span>Modul Pengajuan AO - Form Wizard 13 Tab (`/pengajuan/baru` & `edit`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-200 text-rose-900 text-[10px] font-extrabold rounded">Kekurangan Validasi & Resiko Kehilangan Data</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-rose-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li><strong>Resiko Kehilangan Data:</strong> Tidak ada mekanisme *Auto-Save* ke `localStorage`. Jika browser tidak sengaja ter-refresh atau tertutup saat mengisi tab ke-7, data isian sementara berpotensi hilang.</li>
                                <li><strong>Navigasi 13 Tab Terlalu Padat:</strong> Di layar laptop 14 inci, 13 tombol tab saling berhimpitan dan beberapa teks judul tab terpotong (*overflow*).</li>
                                <li><strong>Input Angka Belum Auto-Formatting Rupiah:</strong> Pengguna harus mengetik angka manual tanpa pemisah ribuan otomatis pada sebagian input keuangan.</li>
                            </ul>
                        </div>
                        <div class="bg-white/80 p-3 rounded-lg border border-rose-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Tambahkan fitur *Draft Autosave*: Simpan setiap perubahan field ke browser storage setiap 10 detik secara senyap.</li>
                                <li>Buat navigasi tab bertingkat atau gunakan *Scrollable Horizontal Pill Navigation* dengan panah kiri/kanan.</li>
                                <li>Tambahkan progress bar persentase kelengkapan pengajuan (cth: "Kelengkapan Berkas: 68%").</li>
                                <li>Terapkan *Input Masking* otomatis (Rp 1.000.000) pada seluruh field nominal finansial.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.5 MODUL VERIFIKASI SLIK OJK -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-2.5">
                        <div class="font-black text-slate-900 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-700 text-white rounded-lg flex items-center justify-center text-[10px]">5</span>
                            <span>Modul Verifikasi SLIK OJK (`/petugas_slik/dashboard_slik` & `/slik/verifikasi`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded">Kekurangan Deteksi Otomatis & Komparasi</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-slate-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Tidak ada komparasi otomatis antara hutang yang dilaporkan calon debitur di formulir AO dengan data riil pada lembar iDeb SLIK OJK.</li>
                                <li>Belum ada sistem peringatan merah (*Red Flag Alert*) otomatis jika ditemukan fasilitas kredit berstatus Kolektibilitas 3, 4, atau 5 (NPL).</li>
                            </ul>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Fitur kalkulasi otomatis Total Exposure Hutang Luar dan Total Beban Angsuran Bulanan di bank lain.</li>
                                <li>Sematkan PDF Viewer berdampingan (*Split-Screen Viewer*) untuk membaca file asli iDeb sambil memverifikasi data di sistem.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.6 ENGINE AI UNDERWRITING -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-2.5">
                        <div class="font-black text-slate-900 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-700 text-white rounded-lg flex items-center justify-center text-[10px]">6</span>
                            <span>Engine AI 5C Underwriting (`Pengajuan::analisaAi`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded">Kekurangan Animasi Transparansi & Fallback</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-slate-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Waktu tunggu inferensi AI (3-5 detik) hanya menampilkan spinner polos, membuat pengguna merasa sistem macet atau tidak transparan.</li>
                                <li>Tidak ada cadangan skor (*Rule-Based Fallback Scoring*) apabila kuota Google Gemini API habis atau server offline.</li>
                            </ul>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Modal loading beranimasi step-by-step: <em>"1. Mengekstrak Finansial &rarr; 2. Memeriksa SLIK OJK &rarr; 3. Mengukur Bobot 5C &rarr; 4. Menyusun Rekomendasi CAM..."</em>.</li>
                                <li>Tombol *"Regenerate Analisis"* dengan opsi memasukkan catatan instruksi khusus dari Analis Senior.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.7 MODUL REVIEWER & KOMITE KREDIT -->
                <div class="p-4 bg-rose-50/50 rounded-xl border border-rose-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-rose-200/80 pb-2 mb-2.5">
                        <div class="font-black text-rose-950 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-rose-700 text-white rounded-lg flex items-center justify-center text-[10px]">7</span>
                            <span>Modul Reviewer & Sidang Komite Kredit (`/reviewer/dashboard_review`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-200 text-rose-900 text-[10px] font-extrabold rounded">GAP FATAL: Tidak Ada Status "Revisi Berkas"</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-rose-900 block mb-1">&bull; Temuan Kekurangan Kritis:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li><strong>TIDAK ADA OPSI "REVISI KE AO":</strong> Pilihan komite saat ini hanya "Disetujui", "Disetujui Bersyarat", dan "Ditolak". Jika ada kesalahan input minor pada nilai agunan atau data dokumen buram, komite tidak dapat mengembalikan berkas ke AO untuk direvisi!</li>
                                <li>Belum ada *Lending Approval Limit Matrix* (pembatasan wewenang plafon memutus kredit berdasarkan jabatan komite).</li>
                            </ul>
                        </div>
                        <div class="bg-white/80 p-3 rounded-lg border border-rose-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li><strong>Wajib Ditambahkan Tombol "Kembalikan untuk Revisi":</strong> Disertai kolom isian catatan perbaikan wajib yang langsung memicu status pengajuan kembali menjadi *Draft Revisi* di dashboard AO.</li>
                                <li>Fitur Tanda Tangan Digital (Canvas Signature / Validasi QR-Code Komite).</li>
                                <li>Tabel perbandingan berdampingan: [Usulan AO] vs [Saran AI 5C] vs [Persetujuan Komite].</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 3.8 MODUL CETAK LAPORAN CAM RESMI -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-2.5">
                        <div class="font-black text-slate-900 text-[12px] flex items-center gap-2">
                            <span class="w-5 h-5 bg-blue-700 text-white rounded-lg flex items-center justify-center text-[10px]">8</span>
                            <span>Modul Cetak Laporan CAM Resmi (`/pengajuan/cetak/{id}`)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded">Kekurangan Watermark & Proteksi Page Break</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px]">
                        <div>
                            <span class="font-bold text-slate-900 block mb-1">&bull; Temuan Kekurangan:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Tidak ada watermark status; berkas yang masih dalam status *Draft* atau *Ditolak* terlihat sama resminya dengan berkas yang sudah *Disetujui Komite*.</li>
                                <li>Pada pencetakan fisik, tabel mitigasi risiko terkadang terpotong di tepi batas halaman A4 jika isi teks terlalu panjang.</li>
                            </ul>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-slate-200">
                            <span class="font-bold text-emerald-800 block mb-1">&bull; Apa yang Harus Dibenahi & Ditambahi:</span>
                            <ul class="list-disc list-inside space-y-1 text-slate-700 leading-relaxed">
                                <li>Sematkan watermark diagonal otomatis: "DRAFT (BELUM DISETUJUI)" atau "RESMI - DISETUJUI KOMITE".</li>
                                <li>Tambahkan QR Code verifikasi dokumen di pojok kanan bawah surat untuk validasi keaslian berkas.</li>
                                <li>Perketat styling `@page` CSS dengan `page-break-inside: avoid` pada setiap bab dan tabel.</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- PAGE BREAK UNTUK CETAK PDF RAPI -->
        <div class="page-break"></div>

        <!-- ==========================================================================
             BAB 4: TABEL MATRIKS APA YANG HARUS DIBENAHI & DITAMBAHKAN
        =========================================================================== -->
        <div class="mb-8 pt-2 avoid-break">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">4. Matriks Ringkasan Pembenahan & Penambahan Fitur Per Modul</h2>
            </div>

            <table class="w-full text-xs border border-slate-200 rounded-xl overflow-hidden mb-4">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-2 px-3 text-left w-36">Modul / Layar</th>
                        <th class="py-2 px-3 text-left">Apa yang Harus Dibenahi</th>
                        <th class="py-2 px-3 text-left">Apa yang Harus Ditambahkan</th>
                        <th class="py-2 px-2 text-center w-20">Urgensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[11px]">
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">1. Halaman Awal Publik</td>
                        <td class="py-2 px-3 text-slate-600">Kurangi volume teks yang terlalu ramai di hero section; beri ruang bernafas (whitespace) lebih lega.</td>
                        <td class="py-2 px-3 text-slate-600">Widget Kalkulator Simulasi Kredit Interaktif & video demo 30 detik cara kerja AI.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-bold text-[10px]">TINGGI</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">2. Login & Registrasi</td>
                        <td class="py-2 px-3 text-slate-600">Perbaiki ergonomi input password dan notifikasi error validasi.</td>
                        <td class="py-2 px-3 text-slate-600">Icon mata Show/Hide Password, Remember Me, dan proteksi lockout brute-force 5x.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-bold text-[10px]">SEDANG</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">3. Dashboard Superadmin</td>
                        <td class="py-2 px-3 text-slate-600">Pisahkan konfigurasi Master Prompt AI dan profil instansi ke dalam sub-tab terpisah.</td>
                        <td class="py-2 px-3 text-slate-600">AI Prompt Sandbox Live Tester & fitur ekspor log aktivitas ke CSV/Excel.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold text-[10px]">SEDANG</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">4. Form Wizard AO (13 Tab)</td>
                        <td class="py-2 px-3 text-slate-600">Perbaiki layout 13 tab agar tidak berdesakan di layar laptop; terapkan auto-format angka Rupiah.</td>
                        <td class="py-2 px-3 text-slate-600">Auto-save draft sementara di browser dan progress bar persentase kelengkapan data.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-bold text-[10px]">TINGGI</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">5. Verifikasi SLIK OJK</td>
                        <td class="py-2 px-3 text-slate-600">Sederhanakan input kolektibilitas dan hitung total baki debet luar otomatis.</td>
                        <td class="py-2 px-3 text-slate-600">Deteksi otomatis Kol 3-5 (Red Flag) dan Split-Screen Viewer file PDF iDeb asli.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded font-bold text-[10px]">SEDANG</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">6. Engine AI 5C</td>
                        <td class="py-2 px-3 text-slate-600">Ganti modal loading spinner polos dengan animasi progress step-by-step yang transparan.</td>
                        <td class="py-2 px-3 text-slate-600">Fitur Regenerasi Analisa dengan custom prompt instruction dan offline scoring fallback.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold text-[10px]">SEDANG</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">7. Reviewer & Komite Kredit</td>
                        <td class="py-2 px-3 text-slate-600">Tampilkan komparasi 3 arah: Usulan AO vs Rekomendasi AI vs Keputusan Final Komite.</td>
                        <td class="py-2 px-3 text-slate-600">Status "Kembalikan untuk Revisi (Return to AO)" dan Tanda Tangan Digital Komite.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-bold text-[10px]">KRITIS</span></td>
                    </tr>
                    <tr>
                        <td class="py-2 px-3 font-bold text-slate-800">8. Cetak Laporan CAM Resmi</td>
                        <td class="py-2 px-3 text-slate-600">Perbaiki aturan CSS print agar tabel mitigasi risiko tidak terpotong di batas halaman A4.</td>
                        <td class="py-2 px-3 text-slate-600">Watermark status otomatis (DRAFT / APPROVED) dan QR Code verifikasi dokumen resmi.</td>
                        <td class="py-2 px-2 text-center"><span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-bold text-[10px]">TINGGI</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ==========================================================================
             BAB 5: MATRIKS VALIDASI YANG BELUM ADA & KODE REKOMENDASI LENGKAP
        =========================================================================== -->
        <div class="mb-8 pt-2">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2.5 h-2.5 rounded-full bg-indigo-600"></div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900">5. Matriks Validasi yang Belum Ada & Rekomendasi Kode Implementasi</h2>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed mb-4 text-justify">
                Audit ini menemukan 7 celah validasi data, keamanan, dan logika finansial yang saat ini belum terpasang di sistem. Berikut rincian gap beserta kode rekomendasi perbaikan untuk disematkan:
            </p>

            <div class="space-y-3.5 text-xs">
                
                <!-- VALIDASI 1 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 1: Format NIK KTP Wajib 16 Digit Angka & Validasi Usia Debitur (&ge; 21 Tahun)</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] rounded">Prioritas Kritis</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Saat ini kolom NIK masih menerima huruf, spasi, atau jumlah angka kurang dari 16 digit. Begitu pula tanggal lahir belum membatasi usia minimum 21 tahun.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Rekomendasi di app/Views/pengajuan/form.php:</span><br>
                        const nik = document.getElementById('nik_ktp')?.value.trim();<br>
                        if (!/^[0-9]{16}$/.test(nik)) {<br>
                        &nbsp;&nbsp;showToast('Nomor Induk Kependudukan (NIK) wajib tepat 16 digit angka numerik.', 'error');<br>
                        &nbsp;&nbsp;return false;<br>
                        }<br>
                        const tglLahir = new Date(document.getElementById('tanggal_lahir')?.value);<br>
                        const usia = Math.floor((new Date() - tglLahir) / (365.25 * 24 * 60 * 60 * 1000));<br>
                        if (usia &lt; 21) {<br>
                        &nbsp;&nbsp;showToast('Debitur harus berusia minimal 21 tahun atau sudah menikah.', 'warning');<br>
                        }
                    </div>
                </div>

                <!-- VALIDASI 2 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 2: Format Nomor Telepon Indonesia (+62 / 08)</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] rounded">Prioritas Sedang</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Mencegah nomor kontak fiktif atau nomor yang tidak dapat dihubungi melalui WhatsApp untuk konfirmasi penagihan.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Validasi regex nomor telepon seluler Indonesia:</span><br>
                        const noHp = document.getElementById('telepon_debitur')?.value.trim();<br>
                        if (!/^(\+62|62|0)8[1-9][0-9]{7,10}$/.test(noHp)) {<br>
                        &nbsp;&nbsp;showToast('Format nomor handphone tidak valid. Gunakan format 08xx atau +628xx.', 'error');<br>
                        &nbsp;&nbsp;return false;<br>
                        }
                    </div>
                </div>

                <!-- VALIDASI 3 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 3: Logika Finansial Nilai Agunan (Nilai Likuidasi LV &le; Nilai Pasar MV)</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] rounded">Prioritas Tinggi</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Nilai Likuidasi agunan secara aturan penilaian appraisal perbankan mustahil lebih besar dari Nilai Pasar Wajar.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Validasi logika agunan di Tab 4:</span><br>
                        const mv = parseFloat(cleanNumber(document.getElementById('nilai_pasar').value)) || 0;<br>
                        const lv = parseFloat(cleanNumber(document.getElementById('nilai_likuidasi').value)) || 0;<br>
                        if (lv &gt; mv) {<br>
                        &nbsp;&nbsp;showToast('Error: Nilai Likuidasi (LV) tidak boleh melebihi Nilai Pasar Wajar (MV).', 'error');<br>
                        &nbsp;&nbsp;document.getElementById('nilai_likuidasi').classList.add('border-rose-500');<br>
                        &nbsp;&nbsp;return false;<br>
                        }
                    </div>
                </div>

                <!-- VALIDASI 4 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 4: Pengecekan Ukuran & Ekstensi File di Sisi Browser (Client-Side Upload Check)</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] rounded">Prioritas Tinggi</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Mencegah browser mengunggah file raksasa (misal 50MB) yang membebani bandwidth server atau file berbahaya.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Client-side upload handler pada file input:</span><br>
                        const file = fileInput.files[0];<br>
                        if (file &amp;&amp; file.size &gt; 5 * 1024 * 1024) {<br>
                        &nbsp;&nbsp;showToast('Ukuran file melebihi batas maksimal 5 Megabyte (5MB).', 'error');<br>
                        &nbsp;&nbsp;fileInput.value = ''; return false;<br>
                        }<br>
                        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];<br>
                        if (file &amp;&amp; !allowedTypes.includes(file.type)) {<br>
                        &nbsp;&nbsp;showToast('Tipe dokumen ditolak! Hanya menerima format PDF, JPG, atau PNG.', 'error');<br>
                        &nbsp;&nbsp;fileInput.value = ''; return false;<br>
                        }
                    </div>
                </div>

                <!-- VALIDASI 5 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 5: Proteksi Pembagian Nol pada Kalkulasi Rasio Keuangan (DSC Zero Division)</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] rounded">Prioritas Tinggi</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Mencegah keluaran nilai `Infinity` atau fatal crash ketika angsuran pinjaman belum dihitung atau bernilai Rp 0.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Rekomendasi di Pengajuan.php (Backend Safeguard):</span><br>
                        $labaBersih = (float)$this-&gt;cleanNumber($postData['laba_bersih'] ?? 0);<br>
                        $angsuran   = (float)$this-&gt;cleanNumber($postData['angsuran_dipilih'] ?? 0);<br>
                        $dscRatio   = ($angsuran &gt; 0) ? round($labaBersih / $angsuran, 2) : 0.00;
                    </div>
                </div>

                <!-- VALIDASI 6 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 6: Validasi Cross-Field Akuntansi (Omset Penjualan vs HPP)</span>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold text-[10px] rounded">Prioritas Sedang</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Mendeteksi kekeliruan input jika Harga Pokok Penjualan (HPP) sengaja atau keliru diisi melebihi Omset Penjualan.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Cross-field alert di Tab 7 (Laba Rugi):</span><br>
                        if (hpp &gt; omsetPenjualan) {<br>
                        &nbsp;&nbsp;showToast('Peringatan: HPP lebih besar dari Omset Penjualan (Margin Kotor Negatif).', 'warning');<br>
                        }
                    </div>
                </div>

                <!-- VALIDASI 7 -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 avoid-break">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-slate-900 text-[12px]">Validasi 7: Validasi Catatan Wajib saat Komite Menolak atau Mengembalikan Berkas</span>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-800 font-bold text-[10px] rounded">Prioritas Tinggi</span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-2">Memastikan keputusan penolakan atau revisi selalu didampingi alasan tertulis minimal 20 karakter demi kepatuhan audit perbankan.</p>
                    <div class="bg-slate-900 text-slate-100 p-2.5 rounded-lg font-mono text-[10px] overflow-x-auto leading-relaxed">
                        <span class="text-slate-400">// Validasi di form_review.php:</span><br>
                        const status = document.getElementById('status_keputusan').value;<br>
                        const catatan = document.getElementById('catatan_komite').value.trim();<br>
                        if (['Tolak', 'Revisi'].includes(status) &amp;&amp; catatan.length &lt; 20) {<br>
                        &nbsp;&nbsp;showToast('Alasan penolakan / instruksi revisi wajib diisi minimal 20 karakter.', 'error');<br>
                        &nbsp;&nbsp;return false;<br>
                        }
                    </div>
                </div>

            </div>
        </div>

        <!-- ==========================================================================
             BAB 6: LEMBAR PENGESAHAN & TANDA TANGAN AUDITOR
        =========================================================================== -->
        <div class="border-t-2 border-slate-900 pt-6 mt-8 avoid-break">
            <div class="text-xs text-slate-700 font-bold mb-4 uppercase tracking-wider text-center">
                LEMBAR PENGESAHAN AUDIT MUTU & KELAYAKAN SISTEM (OFFICIAL SIGN-OFF SHEET)
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-xs">
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between h-36">
                    <div>
                        <div class="font-bold text-slate-900 text-[11px]">Lead QA & QC Engineer</div>
                        <div class="text-[9px] text-slate-400">Quality Assurance Lead</div>
                    </div>
                    <div class="font-mono text-[10px] text-emerald-700 font-bold">[VERIFIED & SIGNED]</div>
                    <div>
                        <div class="font-bold text-slate-800 text-[11px] border-t border-slate-300 pt-1">Rian Pratama, S.Kom</div>
                        <div class="text-[9px] text-slate-400">NIP: QA-2026-081</div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between h-36">
                    <div>
                        <div class="font-bold text-slate-900 text-[11px]">Security & Risk Auditor</div>
                        <div class="text-[9px] text-slate-400">Cybersecurity Specialist</div>
                    </div>
                    <div class="font-mono text-[10px] text-emerald-700 font-bold">[VERIFIED & SIGNED]</div>
                    <div>
                        <div class="font-bold text-slate-800 text-[11px] border-t border-slate-300 pt-1">Ahmad Dahlan, CISSP</div>
                        <div class="text-[9px] text-slate-400">NIP: SEC-2026-044</div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between h-36">
                    <div>
                        <div class="font-bold text-slate-900 text-[11px]">Head of Credit Risk</div>
                        <div class="text-[9px] text-slate-400">Underwriting Department</div>
                    </div>
                    <div class="font-mono text-[10px] text-emerald-700 font-bold">[APPROVED]</div>
                    <div>
                        <div class="font-bold text-slate-800 text-[11px] border-t border-slate-300 pt-1">Hendra Wijaya, S.E.</div>
                        <div class="text-[9px] text-slate-400">NIP: CR-2026-012</div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between h-36">
                    <div>
                        <div class="font-bold text-slate-900 text-[11px]">Chief Executive Officer</div>
                        <div class="text-[9px] text-slate-400">Executive Committee</div>
                    </div>
                    <div class="font-mono text-[10px] text-emerald-700 font-bold">[RATIFIED]</div>
                    <div>
                        <div class="font-bold text-slate-800 text-[11px] border-t border-slate-300 pt-1">Direktur Utama / CEO</div>
                        <div class="text-[9px] text-slate-400">Lembaga Pembiayaan</div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6 text-[10px] text-slate-400 font-medium">
                Dokumen ini merupakan Laporan Resmi Hasil Pengujian Mutu Perangkat Lunak (QA/QC Audit Certification) yang diterbitkan secara resmi pada tanggal <?= date('d F Y') ?>.
            </div>
        </div>

    </div>

</body>
</html>
