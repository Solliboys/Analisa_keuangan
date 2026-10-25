<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Daftar Simulasi Analisa Kredit | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; 
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'simulasi',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Budi Santoso',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Senior Credit Analyst'
    ]) ?>

    <!-- MAIN WRAPPER (Offset by sidebar width md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen">

        <!-- Mobile Header Bar -->
        <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="font-bold text-slate-900 text-sm">OTW <span class="text-blue-600">Kredit</span></span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                    Logout
                </a>
            </div>
        </header>

        <!-- Main Content Body -->
        <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto">
            
            <!-- Page Header: Title & Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight">
                        Daftar Simulasi Analisa Kredit
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Riwayat dan draf kalkulasi simulasi kredit nasabah.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Export Button -->
                    <button type="button" onclick="handleExportData()" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export</span>
                    </button>

                    <!-- + Tambah Simulasi Button -->
                    <button type="button" onclick="openTambahSimulasiModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>+ Tambah Simulasi</span>
                    </button>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 mb-6 shadow-2xs">
                <div class="flex flex-col md:flex-row items-center gap-3">
                    
                    <!-- Search Input Field -->
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="searchInput" 
                               onkeyup="filterTable()" 
                               placeholder="Cari nama debitur, ID simulasi, atau NIK..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-transparent rounded-xl border border-transparent focus:border-blue-500 focus:bg-slate-50/50 outline-none text-slate-800 placeholder-slate-400 transition" />
                    </div>

                    <!-- Filter Tenor Dropdown -->
                    <div class="relative w-full md:w-auto shrink-0">
                        <select id="tenorFilter" onchange="filterTable()" class="w-full md:w-44 px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Semua Tenor</option>
                            <option value="12">12 Bulan</option>
                            <option value="24">24 Bulan</option>
                            <option value="36">36 Bulan</option>
                            <option value="48">48 Bulan</option>
                            <option value="60">60 Bulan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Filter Rentang Plafon Dropdown -->
                    <div class="relative w-full md:w-auto shrink-0">
                        <select id="plafonFilter" onchange="filterTable()" class="w-full md:w-44 px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Rentang Plafon</option>
                            <option value="under_100">< Rp 100 Juta</option>
                            <option value="100_300">Rp 100 - 300 Juta</option>
                            <option value="300_500">Rp 300 - 500 Juta</option>
                            <option value="above_500">> Rp 500 Juta</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="simulasiTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-white text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th scope="col" class="py-4 px-6 font-semibold">TANGGAL SIMULASI</th>
                                <th scope="col" class="py-4 px-6 font-semibold">NAMA NASABAH / CALON DEBITUR</th>
                                <th scope="col" class="py-4 px-6 font-semibold">PLAFON PINJAMAN</th>
                                <th scope="col" class="py-4 px-6 font-semibold">TENOR</th>
                                <th scope="col" class="py-4 px-6 font-semibold text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="tableBody">
                            
                            <!-- Row 1: Hendrawan Pratama -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="12" data-plafon="200000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    24 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    Hendrawan Pratama
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 200.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    12 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('Hendrawan Pratama', 'Rp 200.000.000', '12 Bulan', '24 Okt 2024', 'Perorangan', '82 / 100 (Layak)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('Hendrawan Pratama')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2: PT Maju Bersama -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="36" data-plafon="750000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    24 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    PT Maju Bersama
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 750.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    36 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('PT Maju Bersama', 'Rp 750.000.000', '36 Bulan', '24 Okt 2024', 'Badan Usaha (PT)', '88 / 100 (Sangat Layak)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('PT Maju Bersama')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3: CV Berkah Mandiri -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="24" data-plafon="350000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    23 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    CV Berkah Mandiri
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 350.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    24 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('CV Berkah Mandiri', 'Rp 350.000.000', '24 Bulan', '23 Okt 2024', 'Badan Usaha (CV)', '76 / 100 (Layak Bersyarat)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('CV Berkah Mandiri')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 4: Dewi Sartika -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="12" data-plafon="120000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    23 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    Dewi Sartika
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 120.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    12 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('Dewi Sartika', 'Rp 120.000.000', '12 Bulan', '23 Okt 2024', 'Perorangan', '85 / 100 (Layak)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('Dewi Sartika')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 5: Budi Prasetyo -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="36" data-plafon="500000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    22 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    Budi Prasetyo
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 500.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    36 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('Budi Prasetyo', 'Rp 500.000.000', '36 Bulan', '22 Okt 2024', 'Perorangan', '79 / 100 (Layak)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('Budi Prasetyo')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 6: Siti Aminah -->
                            <tr class="hover:bg-slate-50/70 transition-colors data-row" data-tenor="24" data-plafon="50000000">
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    22 Okt 2024
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">
                                    Siti Aminah
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">
                                    Rp 50.000.000
                                </td>
                                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">
                                    24 Bulan
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="viewDetail('Siti Aminah', 'Rp 50.000.000', '24 Bulan', '22 Okt 2024', 'Perorangan', '91 / 100 (Sangat Layak)')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                                            Detail
                                        </button>
                                        <button type="button" onclick="analisaUlang('Siti Aminah')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                                            Analisa Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Table Footer & Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-t border-slate-100 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800" id="showingCount">1–6</span> dari <span class="font-bold text-slate-800">48</span> simulasi kredit
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" class="px-3 py-1.5 text-slate-400 font-medium cursor-not-allowed rounded-lg hover:bg-slate-50">
                            Sebelumnya
                        </button>
                        <button type="button" class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold flex items-center justify-center shadow-xs shadow-blue-500/20">
                            1
                        </button>
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium flex items-center justify-center transition">
                            2
                        </button>
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium flex items-center justify-center transition">
                            3
                        </button>
                        <span class="px-1 text-slate-400">...</span>
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium flex items-center justify-center transition">
                            8
                        </button>
                        <button type="button" class="px-3 py-1.5 text-slate-700 font-semibold hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL: Tambah Simulasi Kredit -->
    <div id="tambahSimulasiModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Tambah Simulasi Baru</h3>
                </div>
                <button onclick="closeTambahSimulasiModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form onsubmit="submitSimulasiBaru(event)" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Nasabah / Badan Usaha</label>
                    <input type="text" id="newNama" required placeholder="Contoh: Hendrawan Pratama" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Plafon Pinjaman (Rp)</label>
                        <input type="number" id="newPlafon" required placeholder="200000000" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tenor (Bulan)</label>
                        <select id="newTenor" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            <option value="12">12 Bulan</option>
                            <option value="24">24 Bulan</option>
                            <option value="36">36 Bulan</option>
                            <option value="48">48 Bulan</option>
                            <option value="60">60 Bulan</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Usaha / Segmentasi</label>
                    <select id="newKategori" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        <option value="Perorangan">Perorangan / Mikro</option>
                        <option value="Badan Usaha (CV)">Badan Usaha (CV / Retail)</option>
                        <option value="Badan Usaha (PT)">Badan Usaha (PT / Komersial)</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeTambahSimulasiModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition">
                        Simpan Simulasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Detail Simulasi -->
    <div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Detail Draf Simulasi</h3>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-4 space-y-3 text-xs" id="detailContent">
                <!-- Dynamically populated via JS -->
            </div>
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <a id="btnReview5C" href="<?= base_url('superadmin/review') ?>" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition">
                    Buka AI 5C Review &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Scripts -->
    <script>
        function filterTable() {
            const searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
            const tenorVal = document.getElementById('tenorFilter').value;
            const plafonVal = document.getElementById('plafonFilter').value;
            const rows = document.querySelectorAll('#tableBody tr.data-row');
            
            let visibleCount = 0;

            rows.forEach(row => {
                const nama = row.querySelector('.nama-nasabah')?.innerText.toLowerCase() || '';
                const tenor = row.getAttribute('data-tenor');
                const plafon = parseInt(row.getAttribute('data-plafon'), 10);

                let matchSearch = (nama.indexOf(searchVal) !== -1);
                let matchTenor = !tenorVal || (tenor === tenorVal);
                let matchPlafon = true;

                if (plafonVal === 'under_100') {
                    matchPlafon = (plafon < 100000000);
                } else if (plafonVal === '100_300') {
                    matchPlafon = (plafon >= 100000000 && plafon <= 300000000);
                } else if (plafonVal === '300_500') {
                    matchPlafon = (plafon > 300000000 && plafon <= 500000000);
                } else if (plafonVal === 'above_500') {
                    matchPlafon = (plafon > 500000000);
                }

                if (matchSearch && matchTenor && matchPlafon) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('showingCount').innerText = visibleCount > 0 ? `1–${visibleCount}` : '0';
        }

        function handleExportData() {
            alert('Menyiapkan file export data simulasi (Excel/PDF)... File siap diunduh.');
        }

        function openTambahSimulasiModal() {
            document.getElementById('tambahSimulasiModal').classList.remove('hidden');
        }

        function closeTambahSimulasiModal() {
            document.getElementById('tambahSimulasiModal').classList.add('hidden');
        }

        function submitSimulasiBaru(e) {
            e.preventDefault();
            const nama = document.getElementById('newNama').value;
            const plafon = document.getElementById('newPlafon').value;
            const tenor = document.getElementById('newTenor').value;
            const kategori = document.getElementById('newKategori').value;

            const formattedPlafon = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(plafon);

            const tableBody = document.getElementById('tableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-slate-50/70 transition-colors data-row bg-blue-50/30';
            newRow.setAttribute('data-tenor', tenor);
            newRow.setAttribute('data-plafon', plafon);
            newRow.innerHTML = `
                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">Hari Ini</td>
                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap nama-nasabah">${nama}</td>
                <td class="py-4 px-6 font-bold text-slate-900 whitespace-nowrap">${formattedPlafon}</td>
                <td class="py-4 px-6 text-slate-600 whitespace-nowrap">${tenor} Bulan</td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-2">
                        <button type="button" onclick="viewDetail('${nama}', '${formattedPlafon}', '${tenor} Bulan', 'Hari Ini', '${kategori}', 'Dalam Evaluasi')" class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50/70 hover:bg-blue-100/70 border border-blue-100 rounded-lg transition-colors">
                            Detail
                        </button>
                        <button type="button" onclick="analisaUlang('${nama}')" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                            Analisa Ulang
                        </button>
                    </div>
                </td>
            `;

            tableBody.insertBefore(newRow, tableBody.firstChild);
            closeTambahSimulasiModal();
            filterTable();

            // feedback toast
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 z-50 bg-emerald-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 text-xs font-medium';
            toast.innerHTML = `<span>Simulasi untuk <strong>${nama}</strong> berhasil ditambahkan!</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        function viewDetail(nama, plafon, tenor, tgl, kategori, skor) {
            const container = document.getElementById('detailContent');
            container.innerHTML = `
                <div class="p-3 bg-slate-50 rounded-xl space-y-2">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Nama Debitur:</span>
                        <span class="font-bold text-slate-900">${nama}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Plafon Pinjaman:</span>
                        <span class="font-bold text-blue-600">${plafon}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Tenor:</span>
                        <span class="font-semibold text-slate-800">${tenor}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Tanggal Simulasi:</span>
                        <span class="text-slate-800">${tgl}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Segmentasi Usaha:</span>
                        <span class="text-slate-800">${kategori}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Skor Kelayakan AI:</span>
                        <span class="font-bold text-emerald-600">${skor}</span>
                    </div>
                </div>
            `;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function analisaUlang(nama) {
            window.location.href = '<?= base_url('superadmin/review') ?>?nasabah=' + encodeURIComponent(nama);
        }
    </script>
</body>
</html>
