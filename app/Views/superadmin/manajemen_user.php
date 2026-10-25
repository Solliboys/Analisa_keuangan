<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manajemen Pengguna & Akses | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
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
        'active_menu'  => 'users',
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
                <span class="font-bold text-slate-900 text-sm">OTW <span class="text-blue-600">Kredit</span></span>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                Logout
            </a>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight">
                        Manajemen Pengguna &amp; Akses
                    </h1>
                    <p class="text-slate-500 text-sm mt-1 max-w-2xl leading-relaxed">
                        Kelola akun personel sistem, verifikasi persetujuan analis kredit, dan pantau status keaktifan pengguna.
                    </p>
                </div>
                <div>
                    <!-- + Tambah Pengguna Baru Button -->
                    <button type="button" onclick="openTambahUserModal()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>+ Tambah Pengguna Baru</span>
                    </button>
                </div>
            </div>

            <!-- Top Metric Summary Cards (Grid 2 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                
                <!-- Card 1: TOTAL PENGGUNA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                TOTAL PENGGUNA
                            </span>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold text-slate-900" id="totalUserCount">32</span>
                                <span class="text-xs text-slate-400 font-medium">Personel</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: PENGGUNA AKTIF -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                PENGGUNA AKTIF
                            </span>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold text-slate-900" id="activeUserCount">28</span>
                                <span class="text-xs text-slate-400 font-medium">Online / Bertugas</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 mb-6 shadow-2xs">
                <div class="flex flex-col md:flex-row items-center gap-3">
                    
                    <!-- Search Field -->
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="searchUserInput" 
                               onkeyup="filterUsers()" 
                               placeholder="Cari nama pengguna, NIK, atau email dinas..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-transparent rounded-xl border border-transparent focus:border-blue-500 focus:bg-slate-50/50 outline-none text-slate-800 placeholder-slate-400 transition" />
                    </div>

                    <!-- Dropdown Filter Status -->
                    <div class="relative w-full md:w-auto shrink-0 flex items-center gap-2">
                        <div class="relative w-full md:w-48">
                            <select id="statusFilter" onchange="filterUsers()" class="w-full px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                                <option value="">Semua Status Akun</option>
                                <option value="aktif">Aktif</option>
                                <option value="pending">Pending Verif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Reset / Refresh Button -->
                        <button type="button" onclick="refreshUserFilter()" class="p-2 border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 rounded-xl transition" title="Refresh">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Table Card: Manajemen Pengguna -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="userTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-white text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th scope="col" class="py-4 px-6 font-semibold">NAMA LENGKAP / PENGGUNA</th>
                                <th scope="col" class="py-4 px-6 font-semibold">TANGGAL DAFTAR</th>
                                <th scope="col" class="py-4 px-6 font-semibold">NOMOR HP</th>
                                <th scope="col" class="py-4 px-6 font-semibold">STATUS AKUN</th>
                                <th scope="col" class="py-4 px-6 font-semibold">TIPE USER</th>
                                <th scope="col" class="py-4 px-6 font-semibold text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="userTableBody">
                            
                            <!-- Row 1: Budi Santoso (Anda) -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" data-status="aktif">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 user-name">Budi Santoso</span>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Anda</span>
                                    </div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">budi.santoso@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    14 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0812-8899-1021
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                        Admin
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" onclick="viewUserDetail('Budi Santoso', 'budi.santoso@otwkredit.id', '0812-8899-1021', 'Senior Credit Analyst / Superadmin', '14 Jan 2024', 'Aktif', 'Admin')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2: Rina Marlina, S.E., CRM -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" data-status="aktif">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 user-name">Rina Marlina, S.E., CRM</div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">rina.marlina@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    12 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0813-7721-9943
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Anggota
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" onclick="viewUserDetail('Rina Marlina, S.E., CRM', 'rina.marlina@otwkredit.id', '0813-7721-9943', 'Credit Risk Manager', '12 Jan 2024', 'Aktif', 'Anggota')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3: Hendra Gunawan, S.H. -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" data-status="aktif">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 user-name">Hendra Gunawan, S.H.</div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">hendra.gunawan@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    10 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0821-3344-5566
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Anggota
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" onclick="viewUserDetail('Hendra Gunawan, S.H.', 'hendra.gunawan@otwkredit.id', '0821-3344-5566', 'Legal Officer / Agunan', '10 Jan 2024', 'Aktif', 'Anggota')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 4: Siti Nurhaliza (Pending Verif) -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" id="row-siti" data-status="pending">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 user-name">Siti Nurhaliza</div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">siti.nurhaliza@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    08 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0857-1122-3344
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap" id="status-siti">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending Verif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Anggota
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2" id="action-siti">
                                        <!-- Tombol Setuju -->
                                        <button type="button" onclick="approveUser('Siti Nurhaliza')" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg inline-flex items-center gap-1 transition-colors">
                                            <span>✓</span>
                                            <span>Setuju</span>
                                        </button>
                                        <!-- Tombol Detail -->
                                        <button type="button" onclick="viewUserDetail('Siti Nurhaliza', 'siti.nurhaliza@otwkredit.id', '0857-1122-3344', 'Junior Credit Analyst', '08 Jan 2024', 'Pending Verif', 'Anggota')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 5: David Wijaya -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" data-status="aktif">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 user-name">David Wijaya</div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">david.wijaya@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    05 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0819-5566-7788
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Anggota
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" onclick="viewUserDetail('David Wijaya', 'david.wijaya@otwkredit.id', '0819-5566-7788', 'Account Officer / Pengusul', '05 Jan 2024', 'Aktif', 'Anggota')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 6: Amanda Putri (Nonaktif) -->
                            <tr class="hover:bg-slate-50/70 transition-colors user-row" data-status="nonaktif">
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 user-name">Amanda Putri</div>
                                    <div class="text-xs text-slate-400 user-email mt-0.5">amanda.putri@otwkredit.id</div>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                    02 Jan 2024
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                    0878-9900-1122
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Nonaktif
                                    </span>
                                </td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Anggota
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right whitespace-nowrap">
                                    <button type="button" onclick="viewUserDetail('Amanda Putri', 'amanda.putri@otwkredit.id', '0878-9900-1122', 'Staff Administrasi Kredit', '02 Jan 2024', 'Nonaktif', 'Anggota')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Table Footer & Pagination -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-t border-slate-100 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800" id="userShowingCount">1 - 6</span> dari <span class="font-bold text-slate-800">32</span> pengguna terdaftar
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
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-600 hover:bg-slate-100 font-medium flex items-center justify-center transition">
                            4
                        </button>
                        <button type="button" class="px-3 py-1.5 text-slate-700 font-semibold hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL: Tambah Pengguna Baru -->
    <div id="tambahUserModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Tambah Pengguna Baru</h3>
                </div>
                <button onclick="closeTambahUserModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <form onsubmit="submitUserBaru(event)" class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap &amp; Gelar</label>
                    <input type="text" id="newUserName" required placeholder="Contoh: Rina Marlina, S.E., CRM" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email Kedinasan</label>
                        <input type="email" id="newUserEmail" required placeholder="nama@otwkredit.id" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" id="newUserPhone" required placeholder="0812-xxxx-xxxx" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Peran / Jabatan Akses</label>
                        <select id="newUserRole" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            <option value="Senior Credit Analyst">Senior Credit Analyst</option>
                            <option value="Junior Credit Analyst">Junior Credit Analyst</option>
                            <option value="Account Officer / Pengusul">Account Officer / Pengusul Kredit</option>
                            <option value="Legal Officer / Agunan">Legal Officer / Penilai Agunan</option>
                            <option value="Komite Pemutus">Komite Pemutus Kredit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Tipe Pengguna</label>
                        <select id="newUserType" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none font-semibold text-slate-800">
                            <option value="Anggota">Anggota</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeTambahUserModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Detail Pengguna -->
    <div id="userDetailModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Profil Pengguna</h3>
                <button onclick="closeUserDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-4 space-y-3 text-xs" id="userDetailContent">
                <!-- Dynamically populated via JS -->
            </div>
            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" onclick="closeUserDetailModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Scripts -->
    <script>
        function filterUsers() {
            const searchVal = document.getElementById('searchUserInput').value.toLowerCase().trim();
            const statusVal = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#userTableBody tr.user-row');

            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.querySelector('.user-name')?.innerText.toLowerCase() || '';
                const email = row.querySelector('.user-email')?.innerText.toLowerCase() || '';
                const status = row.getAttribute('data-status');

                let matchSearch = (name.indexOf(searchVal) !== -1 || email.indexOf(searchVal) !== -1);
                let matchStatus = !statusVal || (status === statusVal);

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('userShowingCount').innerText = visibleCount > 0 ? `1 - ${visibleCount}` : '0';
        }

        function refreshUserFilter() {
            document.getElementById('searchUserInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterUsers();
            showToast('Filter direset kembali ke default.');
        }

        function openTambahUserModal() {
            document.getElementById('tambahUserModal').classList.remove('hidden');
        }

        function closeTambahUserModal() {
            document.getElementById('tambahUserModal').classList.add('hidden');
        }

        function submitUserBaru(e) {
            e.preventDefault();
            const name = document.getElementById('newUserName').value;
            const email = document.getElementById('newUserEmail').value;
            const phone = document.getElementById('newUserPhone').value;
            const role = document.getElementById('newUserRole').value;
            const type = document.getElementById('newUserType').value;

            const typeBadge = type === 'Admin' 
                ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200"><svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg> Admin</span>`
                : `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> Anggota</span>`;

            const tableBody = document.getElementById('userTableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-slate-50/70 transition-colors user-row bg-blue-50/20';
            newRow.setAttribute('data-status', 'aktif');
            newRow.innerHTML = `
                <td class="py-4 px-6 whitespace-nowrap">
                    <div class="font-bold text-slate-900 user-name">${name}</div>
                    <div class="text-xs text-slate-400 user-email mt-0.5">${email}</div>
                </td>
                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">Hari Ini</td>
                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">${phone}</td>
                <td class="py-4 px-6 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                </td>
                <td class="py-4 px-6 whitespace-nowrap">
                    ${typeBadge}
                </td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                    <button type="button" onclick="viewUserDetail('${name}', '${email}', '${phone}', '${role}', 'Hari Ini', 'Aktif', '${type}')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Detail</span>
                    </button>
                </td>
            `;

            tableBody.insertBefore(newRow, tableBody.firstChild);
            closeTambahUserModal();
            filterUsers();
            showToast(`Pengguna ${name} (${type}) berhasil ditambahkan!`, 'success');
        }

        function viewUserDetail(name, email, phone, role, date, status, type = 'Anggota') {
            const container = document.getElementById('userDetailContent');
            const statusClass = status === 'Aktif' ? 'text-emerald-600' : (status === 'Pending Verif' ? 'text-amber-600' : 'text-rose-600');
            const typeBadge = type === 'Admin' ? 'bg-blue-50 text-blue-700 border-blue-200 font-bold' : 'bg-slate-100 text-slate-700 border-slate-200 font-medium';
            container.innerHTML = `
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-2.5">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Nama Lengkap:</span>
                        <span class="font-bold text-slate-900">${name}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Tipe Pengguna:</span>
                        <span class="px-2 py-0.5 rounded border text-xs ${typeBadge}">${type}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Email Kedinasan:</span>
                        <span class="font-medium text-slate-800">${email}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Nomor Telepon:</span>
                        <span class="font-mono text-slate-800">${phone}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Hak Akses / Peran:</span>
                        <span class="font-semibold text-blue-600">${role}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Terdaftar Sejak:</span>
                        <span class="text-slate-800">${date}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Status Keaktifan:</span>
                        <span class="font-bold ${statusClass}">● ${status}</span>
                    </div>
                </div>
            `;
            document.getElementById('userDetailModal').classList.remove('hidden');
        }
            document.getElementById('userDetailModal').classList.remove('hidden');
        }

        function closeUserDetailModal() {
            document.getElementById('userDetailModal').classList.add('hidden');
        }

        function approveUser(name) {
            const row = document.getElementById('row-siti');
            if (row) {
                row.setAttribute('data-status', 'aktif');
                document.getElementById('status-siti').innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                `;
                document.getElementById('action-siti').innerHTML = `
                    <button type="button" onclick="viewUserDetail('Siti Nurhaliza', 'siti.nurhaliza@otwkredit.id', '0857-1122-3344', 'Junior Credit Analyst', '08 Jan 2024', 'Aktif')" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Detail</span>
                    </button>
                `;
                showToast(`Akun ${name} telah berhasil diverifikasi dan disetujui!`, 'success');
            }
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 text-xs font-medium text-white ${type === 'success' ? 'bg-emerald-600' : 'bg-slate-900'} animate-bounce`;
            toast.innerHTML = `
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }
    </script>
</body>
</html>
