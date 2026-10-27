<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Manajemen Pengguna & Akses | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full screen layout with sidebar offset md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen w-full">

        <!-- Mobile Header Bar -->
        <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <?php 
                        $cfg = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
                        if (!empty($cfg['logo_aplikasi'])): 
                    ?>
                        <img src="<?= base_url($cfg['logo_aplikasi']) ?>" class="w-6 h-6 object-contain rounded" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-sm"><?= esc($cfg['nama_aplikasi'] ?? 'OTW Kredit') ?></span>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                Logout
            </a>
        </header>

        <!-- Desktop Topbar Full Width -->
        <div class="bg-white border-b border-slate-200/80 px-6 lg:px-8 py-3.5 hidden md:flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Super Administrator</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Manajemen Pengguna &amp; Akses</span>
            </div>
        </div>

        <!-- Main Content Full Width -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">
            
            <!-- Persistent Flash Message Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div id="flashSuccessAlert" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashSuccessAlert').remove()" class="text-emerald-500 hover:text-emerald-800 font-bold text-sm">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div id="flashErrorAlert" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashErrorAlert').remove()" class="text-rose-500 hover:text-rose-800 font-bold text-sm">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 w-full">
                <div>
                    <h1 class="text-2xl sm:text-[28px] font-bold text-slate-900 tracking-tight">
                        Manajemen Pengguna &amp; Akses
                    </h1>
                    <p class="text-slate-500 text-sm mt-1 max-w-3xl leading-relaxed">
                        Kelola data profil pengguna, tambahkan akun personel baru, atur status keaktifan akun secara asinkronus, dan kontrol hak akses sistem.
                    </p>
                </div>
                <div>
                    <!-- + Tambah Pengguna Baru Button -->
                    <button type="button" onclick="openTambahUserModal()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>Tambah Pengguna Baru</span>
                    </button>
                </div>
            </div>

            <!-- Top Metric Summary Cards (Full Width 3 Grid) -->
            <?php
                $userList = $daftar_users ?? [];
                $cntTotal = count($userList);
                $cntAktif = 0;
                $cntNonaktif = 0;
                foreach ($userList as $u) {
                    if ((int)($u['status'] ?? 0) === 1) {
                        $cntAktif++;
                    } else {
                        $cntNonaktif++;
                    }
                }
            ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6 w-full">
                
                <!-- Card 1: TOTAL PENGGUNA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                TOTAL PENGGUNA
                            </span>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold text-slate-900" id="totalUserCount"><?= $cntTotal ?></span>
                                <span class="text-xs text-slate-400 font-medium">Akun Terdaftar</span>
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
                                <span class="text-3xl font-extrabold text-emerald-600" id="activeUserCount"><?= $cntAktif ?></span>
                                <span class="text-xs text-slate-400 font-medium">Dapat Login</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: PENGGUNA TIDAK AKTIF -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                PENGGUNA TIDAK AKTIF
                            </span>
                            <div class="mt-2 flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold text-rose-500" id="inactiveUserCount"><?= $cntNonaktif ?></span>
                                <span class="text-xs text-slate-400 font-medium">Dinonaktifkan</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filter & Search Toolbar (Full Width) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 mb-6 shadow-2xs w-full">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                    
                    <!-- Search Input Field -->
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="searchUserInput" 
                               onkeyup="filterUsers()" 
                               placeholder="Cari nama pengguna, username, email, atau nomor HP..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-transparent rounded-xl border border-transparent focus:border-blue-500 focus:bg-slate-50/50 outline-none text-slate-800 placeholder-slate-400 transition" />
                    </div>

                    <!-- Dropdown Filter Status (Hanya Aktif & Tidak Aktif) -->
                    <div class="relative w-full md:w-auto shrink-0 flex items-center gap-2">
                        <div class="relative w-full md:w-48">
                            <select id="statusFilter" onchange="filterUsers()" class="w-full px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                                <option value="">Semua Status Akun</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Tidak Aktif</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Reset / Refresh Button -->
                        <button type="button" onclick="refreshUserFilter()" class="p-2 border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 rounded-xl transition cursor-pointer" title="Refresh">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Table Card: Manajemen Pengguna Full Width -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden w-full">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse" id="userTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-white text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th scope="col" class="py-4 px-4 font-semibold text-center w-14">NO</th>
                                <th scope="col" class="py-4 px-6 font-semibold">NAMA LENGKAP &amp; USERNAME</th>
                                <th scope="col" class="py-4 px-6 font-semibold">TANGGAL DAFTAR</th>
                                <th scope="col" class="py-4 px-6 font-semibold">NOMOR HP / WA</th>
                                <th scope="col" class="py-4 px-6 font-semibold">STATUS AKUN</th>
                                <th scope="col" class="py-4 px-6 font-semibold">JABATAN / HAK AKSES</th>
                                <th scope="col" class="py-4 px-6 font-semibold text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="userTableBody">
                            
                            <?php if (empty($userList)): ?>
                                <tr id="emptyRow">
                                    <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                        Belum ada data pengguna yang terdaftar.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                    $currentSessionUserId = (int)(session()->get('user_id') ?? 0);
                                    $noUrut = 1;
                                    foreach ($userList as $u): 
                                        $userId   = (int)$u['id'];
                                        $isSelf   = ($currentSessionUserId === $userId);
                                        $isAktif  = ((int)($u['status'] ?? 0) === 1);
                                        $statusSlug = $isAktif ? 'aktif' : 'nonaktif';
                                        
                                        // Foto profil avatar URL
                                        $avatarUrl = !empty($u['foto']) 
                                            ? base_url($u['foto']) 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($u['nama_lengkap'] ?? 'User') . '&background=2563eb&color=fff&bold=true';
                                ?>
                                <tr id="user-row-<?= $userId ?>" class="hover:bg-slate-50/70 transition-colors user-row <?= $isSelf ? 'bg-blue-50/20' : '' ?>" data-status="<?= $statusSlug ?>">
                                    <!-- No Urut -->
                                    <td class="col-no py-4 px-4 text-center font-bold text-xs text-slate-500 whitespace-nowrap">
                                        <?= $noUrut++ ?>
                                    </td>

                                    <!-- Nama Lengkap & Username -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= $avatarUrl ?>" 
                                                 alt="Avatar" 
                                                 class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0" 
                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['nama_lengkap'] ?? 'U') ?>&background=2563eb&color=fff&bold=true';">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-900 user-name"><?= esc($u['nama_lengkap']) ?></span>
                                                    <?php if ($isSelf): ?>
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">Anda</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-xs text-slate-400 user-email mt-0.5">
                                                    @<?= esc($u['username']) ?> <?= !empty($u['email']) ? '• ' . esc($u['email']) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Tanggal Daftar -->
                                    <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                                        <?= !empty($u['created_at']) ? date('d M Y', strtotime($u['created_at'])) : '-' ?>
                                    </td>

                                    <!-- Nomor HP -->
                                    <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                                        <?= !empty($u['nomor_hp']) ? esc($u['nomor_hp']) : '-' ?>
                                    </td>

                                    <!-- Status Akun (Hanya Aktif dan Tidak Aktif) -->
                                    <td class="py-4 px-6 whitespace-nowrap" id="status-cell-<?= $userId ?>">
                                        <?php if ($isAktif): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Tidak Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Jabatan / Hak Akses -->
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <?php
                                            $jab = strtolower($u['jabatan'] ?? '');
                                            if (strpos($jab, 'superadmin') !== false) {
                                                $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                                            } elseif (strpos($jab, 'ceo') !== false || strpos($jab, 'direk') !== false) {
                                                $badgeClass = 'bg-amber-50 text-amber-800 border-amber-300 font-extrabold';
                                            } elseif (strpos($jab, 'slik') !== false) {
                                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                            } elseif (strpos($jab, 'review') !== false) {
                                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                            } else {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                            }
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border <?= $badgeClass ?>">
                                            <?= esc($u['jabatan'] ?? 'Pengusul') ?>
                                        </span>
                                    </td>

                                    <!-- AKSI: Detail, Toggle Status Asinkron, Hapus Asinkron -->
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            
                                            <!-- Tombol Detail -->
                                            <button type="button" 
                                                    onclick="viewUserDetail(
                                                        '<?= esc(addslashes($u['nama_lengkap'])) ?>', 
                                                        '<?= esc(addslashes($u['username'])) ?>', 
                                                        '<?= esc(addslashes($u['email'] ?? '-')) ?>', 
                                                        '<?= esc(addslashes($u['nomor_hp'] ?? '-')) ?>', 
                                                        '<?= esc(addslashes($u['jabatan'] ?? '-')) ?>', 
                                                        '<?= !empty($u['created_at']) ? date('d M Y, H:i', strtotime($u['created_at'])) : '-' ?>', 
                                                        '<?= $isAktif ? 'Aktif' : 'Tidak Aktif' ?>',
                                                        '<?= $avatarUrl ?>'
                                                    )" 
                                                    class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs cursor-pointer" 
                                                    title="Lihat Profil Pengguna">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>

                                            <?php if (!$isSelf): ?>
                                                <!-- Tombol Ubah Status (Aktifkan / Nonaktifkan) Asinkronus -->
                                                <button type="button" 
                                                        id="toggle-btn-<?= $userId ?>"
                                                        onclick="konfirmasiToggleStatus(<?= $userId ?>, '<?= esc(addslashes($u['nama_lengkap'])) ?>', '<?= $isAktif ? 'Nonaktifkan' : 'Aktifkan' ?>')"
                                                        class="px-2.5 py-1.5 text-xs font-semibold <?= $isAktif ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200' ?> border rounded-lg inline-flex items-center gap-1 transition-colors cursor-pointer"
                                                        title="<?= $isAktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' ?>">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $isAktif ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' ?>"/></svg>
                                                    <span><?= $isAktif ? 'Nonaktifkan' : 'Aktifkan' ?></span>
                                                </button>

                                                <!-- Tombol Hapus Pengguna Asinkronus -->
                                                <button type="button" 
                                                        onclick="konfirmasiHapus(<?= $userId ?>, '<?= esc(addslashes($u['nama_lengkap'])) ?>', '<?= esc(addslashes($u['username'])) ?>')"
                                                        class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 rounded-lg inline-flex items-center gap-1 transition-colors shadow-2xs cursor-pointer" 
                                                        title="Hapus Pengguna">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-[11px] text-slate-400 font-medium italic">Akun Anda</span>
                                            <?php endif; ?>

                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

                <!-- Footer Summary Info -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2 w-full">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800" id="userShowingCount"><?= $cntTotal ?></span> pengguna terdaftar
                    </div>
                    <div class="text-slate-400">
                        Status akun: <span class="text-emerald-600 font-semibold" id="countLabelAktif"><?= $cntAktif ?> Aktif</span> &bull; <span class="text-rose-500 font-semibold" id="countLabelNonaktif"><?= $cntNonaktif ?> Tidak Aktif</span>
                    </div>
                </div>

            </div>

        </main>

        <!-- Footer Full Width -->
        <footer class="bg-white border-t border-slate-200 py-4 mt-auto w-full">
            <div class="w-full px-6 lg:px-8 text-center text-xs text-slate-500">
                &copy; <?= date('Y') ?> OTW Kredit. Hak Akses Super Administrator & Manajemen User.
            </div>
        </footer>

    </div>

    <!-- MODAL: Tambah Pengguna Baru Asinkronus -->
    <div id="tambahUserModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden overflow-y-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-xl w-full p-6 sm:p-8 animate-in fade-in duration-200 my-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg tracking-tight">+ Tambah Pengguna Baru</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Isi data akun pengguna baru sesuai form profil sistem (Tersimpan Asinkronus).</p>
                </div>
                <button type="button" onclick="closeTambahUserModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="formTambahUser" action="<?= base_url('superadmin/tambah-user') ?>" method="POST" enctype="multipart/form-data" onsubmit="handleTambahUserAsync(event)" class="space-y-4 mt-5">
                <?= csrf_field() ?>

                <!-- Foto Profil (Unggah & Preview) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Foto Profil (Opsional)
                    </label>
                    <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-200">
                        <img id="avatarPreviewNew" 
                             src="https://ui-avatars.com/api/?name=User+Baru&background=2563eb&color=fff&bold=true" 
                             alt="Preview Avatar" 
                             class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200 shrink-0">
                        <div class="flex-1">
                            <input type="file" 
                                   name="foto_file" 
                                   id="inputFotoUserBaru" 
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   onchange="previewFotoUser(this)" 
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer transition">
                            <span class="text-[10px] text-slate-400 mt-1 block">JPG, PNG, atau WEBP. Maks 2MB (<b>Otomatis dikompresi</b> agar ringan).</span>
                        </div>
                    </div>
                </div>

                <!-- Nama Lengkap & Username -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nama Lengkap &amp; Gelar <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               id="newUserName" 
                               required 
                               placeholder="Contoh: Rina Marlina, S.E." 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username Login <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="username" 
                               id="newUserUsername" 
                               required 
                               placeholder="contoh: rina_marlina" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none" />
                    </div>
                </div>

                <!-- Email & Nomor WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Alamat Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="newUserEmail" 
                               placeholder="nama@email.com" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor HP / WhatsApp
                        </label>
                        <input type="tel" 
                               name="nomor_hp" 
                               id="newUserPhone" 
                               placeholder="081234567890" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none" />
                    </div>
                </div>

                <!-- Jabatan & Status Akun -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Jabatan / Hak Akses <span class="text-rose-500">*</span>
                        </label>
                        <select name="jabatan" id="newUserRole" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none cursor-pointer">
                            <option value="Pengusul">Pengusul (Staff Pengusul Kredit / AO)</option>
                            <option value="Superadmin">Superadmin (Super Administrator)</option>
                            <option value="Petugas SLIK">Petugas SLIK (Verifikasi SLIK OJK)</option>
                            <option value="Reviewer">Reviewer (Reviewer &amp; Komite Kredit)</option>
                            <option value="CEO">CEO (Chief Executive Officer / Direksi)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Akun Awal <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="newUserStatus" class="w-full text-xs font-bold border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none cursor-pointer">
                            <option value="1" selected class="text-emerald-700">Aktif (Dapat langsung login)</option>
                            <option value="0" class="text-rose-600">Tidak Aktif (Dinonaktifkan)</option>
                        </select>
                    </div>
                </div>

                <!-- Kata Sandi Akun -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi (Password) <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="newUserPassword" 
                           required 
                           minlength="6" 
                           placeholder="Minimal 6 karakter..." 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs outline-none" />
                    <span class="text-[11px] text-slate-400 mt-1 block">Password akan di-hash secara aman menggunakan algoritma bcrypt.</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 mt-6">
                    <button type="button" onclick="closeTambahUserModal()" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitUser" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Pengguna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Detail Profil Pengguna -->
    <div id="userDetailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Detail Profil Pengguna</h3>
                <button onclick="closeUserDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="mt-4 flex flex-col items-center text-center">
                <img id="modalAvatar" 
                     src="" 
                     alt="Avatar" 
                     class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md ring-2 ring-blue-500/20 mb-3">
                <h4 class="text-base font-bold text-slate-900" id="modalName">Nama Lengkap</h4>
                <p class="text-xs text-slate-400" id="modalUsername">@username</p>
                <div class="mt-2" id="modalBadge"></div>
            </div>

            <div class="mt-5 space-y-2.5 text-xs bg-slate-50/70 p-4 rounded-2xl border border-slate-100" id="userDetailContent">
                <div class="flex justify-between py-1 border-b border-slate-200/60">
                    <span class="text-slate-500">Alamat Email:</span>
                    <span class="font-semibold text-slate-800" id="modalEmail">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-200/60">
                    <span class="text-slate-500">Nomor WhatsApp:</span>
                    <span class="font-mono font-semibold text-slate-800" id="modalPhone">-</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-200/60">
                    <span class="text-slate-500">Terdaftar Sejak:</span>
                    <span class="font-medium text-slate-800" id="modalDate">-</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Status Keaktifan:</span>
                    <span class="font-bold" id="modalStatus">Aktif</span>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" onclick="closeUserDetailModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Scripts (Asynchronous & UI Handlers) -->
    <script>
        // Update Counter Box
        function updateCounters(counts) {
            if (!counts) return;
            const totalEl = document.getElementById('totalUserCount');
            const activeEl = document.getElementById('activeUserCount');
            const inactiveEl = document.getElementById('inactiveUserCount');
            const labelAktif = document.getElementById('countLabelAktif');
            const labelNonaktif = document.getElementById('countLabelNonaktif');

            if (totalEl) totalEl.innerText = counts.total;
            if (activeEl) activeEl.innerText = counts.aktif;
            if (inactiveEl) inactiveEl.innerText = counts.nonaktif;
            if (labelAktif) labelAktif.innerText = counts.aktif + ' Aktif';
            if (labelNonaktif) labelNonaktif.innerText = counts.nonaktif + ' Tidak Aktif';
        }

        // Re-Index Nomor Urut di Tabel
        function reindexRows() {
            const rows = document.querySelectorAll('#userTableBody tr.user-row');
            let num = 1;
            rows.forEach(r => {
                const noCell = r.querySelector('.col-no');
                if (noCell) {
                    noCell.innerText = num++;
                }
            });
            const countEl = document.getElementById('userShowingCount');
            if (countEl) countEl.innerText = rows.length;
        }

        // Filter & Search Pengguna
        function filterUsers() {
            const searchVal = document.getElementById('searchUserInput').value.toLowerCase().trim();
            const statusVal = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('#userTableBody tr.user-row');

            let visibleCount = 0;

            rows.forEach(row => {
                const textContent = row.innerText.toLowerCase();
                const status = row.getAttribute('data-status');

                let matchSearch = !searchVal || textContent.includes(searchVal);
                let matchStatus = !statusVal || (status === statusVal);

                if (matchSearch && matchStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('userShowingCount').innerText = visibleCount;
        }

        function refreshUserFilter() {
            document.getElementById('searchUserInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterUsers();
        }

        function openTambahUserModal() {
            document.getElementById('tambahUserModal').classList.remove('hidden');
        }

        function closeTambahUserModal() {
            document.getElementById('tambahUserModal').classList.add('hidden');
        }

        function previewFotoUser(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreviewNew');
                    if (preview) {
                        preview.src = e.target.result;
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // TAMBAH USER ASINKRONUS (FETCH API)
        async function handleTambahUserAsync(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = document.getElementById('btnSubmitUser');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const res = await response.json();

                if (res.status === 'success') {
                    closeTambahUserModal();
                    form.reset();
                    document.getElementById('avatarPreviewNew').src = 'https://ui-avatars.com/api/?name=User+Baru&background=2563eb&color=fff&bold=true';

                    // Update summary counts
                    if (res.counts) {
                        updateCounters(res.counts);
                    }

                    // Tambahkan baris baru ke tabel (tepat di bawah Super Administrator jika Super Admin ada di baris 1)
                    addNewRowToTable(res.user);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Ditambahkan!',
                        text: res.message,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'OK, Selesai',
                        customClass: {
                            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-xs shadow-md'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: res.message || 'Periksa kembali data yang dimasukkan.',
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Tutup',
                        customClass: {
                            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-xs shadow-md'
                        }
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                    confirmButtonColor: '#e11d48',
                    confirmButtonText: 'Tutup'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        // Helper Inject Baris Baru ke Tabel
        function addNewRowToTable(user) {
            const tableBody = document.getElementById('userTableBody');
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) {
                emptyRow.remove();
            }

            const isAktif = (parseInt(user.status) === 1);
            const statusSlug = isAktif ? 'aktif' : 'nonaktif';
            
            let badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
            const jab = (user.jabatan || '').toLowerCase();
            if (jab.includes('superadmin')) {
                badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
            } else if (jab.includes('slik')) {
                badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
            } else if (jab.includes('review')) {
                badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
            }

            const safeName = user.nama_lengkap.replace(/'/g, "\\'");
            const safeUsername = user.username.replace(/'/g, "\\'");
            const safeEmail = (user.email || '-').replace(/'/g, "\\'");
            const safePhone = (user.nomor_hp || '-').replace(/'/g, "\\'");
            const safeRole = (user.jabatan || '-').replace(/'/g, "\\'");
            const statusText = isAktif ? 'Aktif' : 'Tidak Aktif';
            const safeAvatar = user.avatar_url;

            const tr = document.createElement('tr');
            tr.id = 'user-row-' + user.id;
            tr.className = 'hover:bg-slate-50/70 transition-all duration-300 user-row bg-emerald-50/20';
            tr.setAttribute('data-status', statusSlug);

            tr.innerHTML = `
                <td class="col-no py-4 px-4 text-center font-bold text-xs text-slate-500 whitespace-nowrap">
                    -
                </td>
                <td class="py-4 px-6 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <img src="${safeAvatar}" 
                             alt="Avatar" 
                             class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0" 
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(user.nama_lengkap)}&background=2563eb&color=fff&bold=true';">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 user-name">${user.nama_lengkap}</span>
                            </div>
                            <div class="text-xs text-slate-400 user-email mt-0.5">
                                @${user.username} ${user.email ? '• ' + user.email : ''}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 text-slate-500 text-xs font-medium whitespace-nowrap">
                    ${user.created_at}
                </td>
                <td class="py-4 px-6 text-slate-600 text-xs font-mono whitespace-nowrap">
                    ${user.nomor_hp || '-'}
                </td>
                <td class="py-4 px-6 whitespace-nowrap" id="status-cell-${user.id}">
                    ${isAktif ? `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    ` : `
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Tidak Aktif
                        </span>
                    `}
                </td>
                <td class="py-4 px-6 whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border ${badgeClass}">
                        ${user.jabatan}
                    </span>
                </td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-2">
                        <button type="button" 
                                onclick="viewUserDetail('${safeName}', '${safeUsername}', '${safeEmail}', '${safePhone}', '${safeRole}', '${user.created_at}', '${statusText}', '${safeAvatar}')" 
                                class="px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg inline-flex items-center gap-1.5 transition-colors shadow-2xs cursor-pointer" 
                                title="Lihat Profil Pengguna">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Detail</span>
                        </button>
                        <button type="button" 
                                id="toggle-btn-${user.id}"
                                onclick="konfirmasiToggleStatus(${user.id}, '${safeName}', '${isAktif ? 'Nonaktifkan' : 'Aktifkan'}')"
                                class="px-2.5 py-1.5 text-xs font-semibold ${isAktif ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200'} border rounded-lg inline-flex items-center gap-1 transition-colors cursor-pointer"
                                title="${isAktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun'}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${isAktif ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}"/></svg>
                            <span>${isAktif ? 'Nonaktifkan' : 'Aktifkan'}</span>
                        </button>
                        <button type="button" 
                                onclick="konfirmasiHapus(${user.id}, '${safeName}', '${safeUsername}')"
                                class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:text-white hover:bg-rose-600 bg-rose-50 border border-rose-200 rounded-lg inline-flex items-center gap-1 transition-colors shadow-2xs cursor-pointer" 
                                title="Hapus Pengguna">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </td>
            `;

            // Super Administrator selalu baris 1 (posisi ke-0), sisipkan pengguna baru tepat di bawahnya
            const firstRow = tableBody.querySelector('tr.user-row');
            if (firstRow && firstRow.nextSibling) {
                tableBody.insertBefore(tr, firstRow.nextSibling);
            } else if (firstRow) {
                tableBody.appendChild(tr);
            } else {
                tableBody.appendChild(tr);
            }

            reindexRows();
            filterUsers();
        }

        // HAPUS USER ASINKRONUS (FETCH API)
        function konfirmasiHapus(id, namaUser, username) {
            Swal.fire({
                title: 'Hapus Pengguna?',
                html: `Apakah Anda yakin ingin menghapus akun <b>${namaUser}</b> (@${username})?<br><span class="text-xs text-rose-500 font-medium mt-1 inline-block">⚠️ Tindakan ini akan menghapus data pengguna secara langsung dari database.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-xs'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    try {
                        const response = await fetch('<?= base_url('superadmin/hapus-user') ?>/' + id, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            const row = document.getElementById('user-row-' + id);
                            if (row) {
                                row.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                                setTimeout(() => {
                                    row.remove();
                                    reindexRows();
                                    filterUsers();
                                }, 300);
                            }

                            if (res.counts) {
                                updateCounters(res.counts);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dihapus!',
                                text: res.message,
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-xs shadow-md'
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus',
                                text: res.message || 'Tidak dapat menghapus pengguna.',
                                confirmButtonColor: '#e11d48',
                                confirmButtonText: 'Tutup'
                            });
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghubungkan ke server.',
                            confirmButtonColor: '#e11d48',
                            confirmButtonText: 'Tutup'
                        });
                    }
                }
            });
        }

        // TOGGLE STATUS USER ASINKRONUS (FETCH API)
        function konfirmasiToggleStatus(id, namaUser, aksi) {
            const isNonaktifkan = aksi.toLowerCase().includes('nonaktif');
            Swal.fire({
                title: `${aksi} Akun?`,
                html: `Apakah Anda yakin ingin <b>${aksi.toLowerCase()}</b> akun pengguna <b>${namaUser}</b>?`,
                icon: isNonaktifkan ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonColor: isNonaktifkan ? '#d97706' : '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: `Ya, ${aksi}!`,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-xs shadow-md',
                    cancelButton: 'rounded-xl px-5 py-2.5 font-semibold text-xs'
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    try {
                        const response = await fetch('<?= base_url('superadmin/toggle-status-user') ?>/' + id, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const res = await response.json();

                        if (res.status === 'success') {
                            const row = document.getElementById('user-row-' + id);
                            if (row) {
                                const statusCell = document.getElementById('status-cell-' + id);
                                const toggleBtn = document.getElementById('toggle-btn-' + id);

                                if (res.new_status === 1) {
                                    row.setAttribute('data-status', 'aktif');
                                    if (statusCell) {
                                        statusCell.innerHTML = `
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        `;
                                    }
                                    if (toggleBtn) {
                                        toggleBtn.className = "px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg inline-flex items-center gap-1 transition-colors cursor-pointer";
                                        toggleBtn.title = "Nonaktifkan Akun";
                                        toggleBtn.setAttribute('onclick', `konfirmasiToggleStatus(${id}, '${namaUser.replace(/'/g, "\\'")}', 'Nonaktifkan')`);
                                        toggleBtn.innerHTML = `
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            <span>Nonaktifkan</span>
                                        `;
                                    }
                                } else {
                                    row.setAttribute('data-status', 'nonaktif');
                                    if (statusCell) {
                                        statusCell.innerHTML = `
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Tidak Aktif
                                            </span>
                                        `;
                                    }
                                    if (toggleBtn) {
                                        toggleBtn.className = "px-2.5 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg inline-flex items-center gap-1 transition-colors cursor-pointer";
                                        toggleBtn.title = "Aktifkan Akun";
                                        toggleBtn.setAttribute('onclick', `konfirmasiToggleStatus(${id}, '${namaUser.replace(/'/g, "\\'")}', 'Aktifkan')`);
                                        toggleBtn.innerHTML = `
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Aktifkan</span>
                                        `;
                                    }
                                }
                            }

                            if (res.counts) {
                                updateCounters(res.counts);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Status Diperbarui',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100'
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengubah Status',
                                text: res.message,
                                confirmButtonColor: '#e11d48'
                            });
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menghubungkan ke server.',
                            confirmButtonColor: '#e11d48'
                        });
                    }
                }
            });
        }

        // Tampilkan Modal Detail User
        function viewUserDetail(name, username, email, phone, role, date, status, avatarUrl) {
            document.getElementById('modalAvatar').src = avatarUrl;
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalUsername').innerText = '@' + username;
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalPhone').innerText = phone;
            document.getElementById('modalDate').innerText = date;

            const modalBadge = document.getElementById('modalBadge');
            modalBadge.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">${role}</span>`;

            const statusEl = document.getElementById('modalStatus');
            if (status === 'Aktif') {
                statusEl.className = 'font-bold text-emerald-600';
                statusEl.innerHTML = '● Aktif (Dapat Login)';
            } else {
                statusEl.className = 'font-bold text-rose-600';
                statusEl.innerHTML = '● Tidak Aktif (Dinonaktifkan)';
            }

            document.getElementById('userDetailModal').classList.remove('hidden');
        }

        function closeUserDetailModal() {
            document.getElementById('userDetailModal').classList.add('hidden');
        }
    </script>
</body>
</html>
