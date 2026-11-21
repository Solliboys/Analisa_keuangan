<?php
/**
 * @var string $title
 * @var string $nama_lengkap
 * @var string $jabatan
 * @var int $total_pengajuan
 * @var float $total_plafon
 * @var int $total_draft
 * @var int $total_diajukan
 * @var int $total_disetujui
 * @var array $daftar_pengajuan
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Workspace Account Officer') ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; -webkit-font-smoothing: antialiased; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $rawNamaWeb  = !empty($pengaturan_sistem['nama_aplikasi']) ? trim($pengaturan_sistem['nama_aplikasi']) : '';
    $namaWeb     = (empty($rawNamaWeb) || preg_match('/(credit\s*engine|cam|bpr)/i', $rawNamaWeb)) ? 'OTW Approved' : $rawNamaWeb;
    $rawLembaga  = !empty($pengaturan_sistem['nama_lembaga']) ? trim($pengaturan_sistem['nama_lembaga']) : 'Lembaga Pembiayaan';
    $namaLembaga = trim(preg_replace('/\b(BPR|CAM)\b/i', '', $rawLembaga));
    $namaLembaga = trim(preg_replace('/\bPT\.?\s*\/\s*/i', '', $namaLembaga));
    $namaLembaga = trim(preg_replace('/\s+/', ' ', $namaLembaga), " \t\n\r\0\x0B/");
    if (empty($namaLembaga)) {
        $namaLembaga = 'Lembaga Pembiayaan';
    }
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? trim($pengaturan_sistem['logo_aplikasi']) : 'img/logo-oap.png?v=2';
?>
    <!-- Top Navigation -->
    <header class="bg-white border-t-2 border-t-blue-700 border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-15 sm:h-16 items-center gap-3">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-2.5 min-w-0">
                    <img src="<?= base_url($logoWeb) ?>"
                         alt="<?= esc($namaWeb) ?>"
                         class="w-8 h-8 object-contain shrink-0">
                    <div class="min-w-0 leading-tight">
                        <span class="text-sm sm:text-[15px] font-bold text-slate-950 tracking-tight block truncate"><?= esc($namaWeb) ?></span>
                        <span class="text-[11px] block text-slate-500 font-medium truncate"><?= esc($namaLembaga) ?></span>
                    </div>
                </a>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-semibold text-slate-900 leading-tight"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-1.5 py-0.5 mt-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">
                            <?= esc(!empty($jabatan) ? $jabatan : 'Pengusul') ?>
                        </span>
                    </div>

                    <!-- Profile Avatar & Dropdown -->
                    <div class="relative" id="userProfileDropdown">
                        <?php
                            $fotoProfil = session()->get('foto');
                            if (empty($fotoProfil) && session()->get('user_id')) {
                                $uRow = \Config\Database::connect()->table('users')->select('foto')->where('id', session()->get('user_id'))->get()->getRowArray();
                                $fotoProfil = $uRow['foto'] ?? '';
                            }
                            $avatarSrc = !empty($fotoProfil) ? base_url($fotoProfil) : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap ?? 'AO') . '&background=1d4ed8&color=fff&bold=true';
                        ?>
                        <button type="button" onclick="toggleProfileDropdown(event)" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-blue-600/30 focus:ring-2 focus:ring-blue-700 transition cursor-pointer">
                            <img src="<?= $avatarSrc ?>"
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap ?? 'AO') ?>&background=1d4ed8&color=fff&bold=true';"
                                 alt="Foto Profil"
                                 class="w-9 h-9 rounded-full object-cover border border-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu (Edit Profil & Logout) -->
                        <div id="dropdownMenuProfil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                            <div class="px-3.5 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-semibold text-slate-900 truncate"><?= esc($nama_lengkap) ?></p>
                                <p class="text-[11px] text-slate-500"><?= esc(!empty($jabatan) ? $jabatan : 'Pengusul') ?></p>
                            </div>

                            <a href="<?= base_url('profil') ?>" class="w-full text-left px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Profil
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <a href="<?= base_url('auth/logout') ?>" class="w-full text-left px-3.5 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex-1 min-w-0 space-y-5">

        <!-- Notifikasi Flash Message -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg flex items-center gap-2.5 text-xs font-medium">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg flex items-center gap-2.5 text-xs font-medium">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <!-- Welcome Hero Banner (Biru Elegan seperti Desain Awal) -->
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-7 text-white shadow-lg shadow-blue-500/10 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-5">
            <!-- Ambient Decorative Elements -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
            <div class="pointer-events-none absolute -left-10 -top-10 w-48 h-48 bg-sky-400/15 rounded-full blur-xl"></div>

            <div class="relative z-10 min-w-0">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 text-blue-100 uppercase tracking-wider backdrop-blur-xs border border-white/10 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Ruang Kerja Pengusul</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Daftar Pengajuan Kredit
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 mt-1 max-w-2xl leading-relaxed">
                    Kelola permohonan pembiayaan debitur dan lengkapi analisa 5C sebelum diteruskan ke komite.
                </p>
            </div>

            <div class="relative z-10 shrink-0">
                <button type="button" onclick="bukaModalPilihDebitur()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-blue-700 hover:bg-blue-50 hover:text-blue-800 font-bold text-xs sm:text-sm rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-[1.02] cursor-pointer">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Pengajuan</span>
                </button>
            </div>
        </div>

        <!-- 4-Column Stat Cards (Wow & Clean) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 tabular-nums">
            <!-- Card 1: Aplikasi Saya -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aplikasi Saya</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2"><?= number_format($total_pengajuan) ?></div>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span>Total berkas diinput</span>
                </div>
            </div>

            <!-- Card 2: Total Plafon Diusulkan -->
            <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/40 rounded-2xl p-5 border border-blue-200/90 shadow-sm shadow-blue-500/5 hover:shadow-md hover:border-blue-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Total Plafon Diusulkan</span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-xl sm:text-2xl font-extrabold text-blue-700 mt-2 truncate">Rp <?= number_format($total_plafon, 0, ',', '.') ?></div>
                <div class="text-xs text-blue-600/80 font-medium mt-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span>Akumulasi nilai permohonan</span>
                </div>
            </div>

            <!-- Card 3: Draft / Belum Final -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Draft / Belum Final</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2"><?= number_format($total_draft) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60 inline-flex items-center gap-1">Perlu dilengkapi</span>
                </div>
            </div>

            <!-- Card 4: Disetujui Komite -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Disetujui Komite</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-2"><?= number_format($total_disetujui) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 inline-flex items-center gap-1">Siap akad &amp; pencairan</span>
                </div>
            </div>
        </div>

        <!-- Table Card: Daftar Berkas Pengajuan Terakhir (Full Width) -->
        <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full lg:w-auto">
                    <!-- Search Box -->
                    <div class="relative w-full sm:w-72">
                        <input type="text" id="searchInput" placeholder="Cari nomor aplikasi, debitur, usaha..."
                               class="w-full h-9 pl-9 pr-3 text-xs font-medium text-slate-900 placeholder-slate-400 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700/10 focus:border-blue-700 transition">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>

                    <!-- Dropdown Filter Tipe Debitur -->
                    <div class="relative w-full sm:w-48 shrink-0">
                        <select id="filterTipeDebitur" onchange="filterTabelPengajuan()"
                                class="w-full h-9 appearance-none pl-3 pr-8 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700/10 focus:border-blue-700 transition cursor-pointer">
                            <option value="semua">Semua Tipe Debitur</option>
                            <option value="perorangan">Perorangan</option>
                            <option value="badan_usaha">Badan Usaha</option>
                        </select>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 justify-between sm:justify-end">
                    <div class="text-xs text-slate-500">
                        Menampilkan <span id="countDisplay" class="font-semibold text-slate-900 tabular-nums"><?= count($daftar_pengajuan) ?></span> pengajuan
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-800 hidden sm:inline">
                        Lihat Semua &rarr;
                    </a>
                </div>
            </div>

            <?php
                // Helper badge status
                $getStatusInfo = function($row) {
                    $rawStatus = trim($row['status_pengajuan'] ?? '');
                    if (empty($rawStatus) && !empty($row['status_komite'])) {
                        $rawStatus = trim($row['status_komite']);
                    }
                    if (empty($rawStatus)) {
                        $rawStatus = 'Draft';
                    }
                    $st = strtolower($rawStatus);

                    if (strpos($st, 'bersyarat') !== false) {
                        return ['badge' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Bersyarat'];
                    } elseif ($st === 'disetujui' || strpos($st, 'setuju') !== false) {
                        return ['badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-600', 'label' => 'Disetujui'];
                    } elseif ($st === 'ditolak' || strpos($st, 'tolak') !== false) {
                        return ['badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-600', 'label' => 'Ditolak'];
                    } elseif ($st === 'revisi' || strpos($st, 'revisi') !== false) {
                        return ['badge' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-600', 'label' => 'Perlu Revisi'];
                    } elseif ($st === 'diajukan') {
                        return ['badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-700', 'label' => 'Diajukan'];
                    }
                    return ['badge' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'Draft'];
                };
            ?>

            <!-- Desktop Table View (>= 768px) -->
            <div class="hidden md:block w-full overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[820px] text-left text-xs text-slate-600 tabular-nums" id="tabelPengajuan">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Aplikasi</th>
                            <th class="py-3 px-4">Debitur &amp; Usaha</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4 text-right">Plafon</th>
                            <th class="py-3 px-4">Skema &amp; Tenor</th>
                            <th class="py-3 px-4">Jaminan</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        <?php if (empty($daftar_pengajuan)): ?>
                            <tr id="emptyTableRow">
                                <td colspan="9" class="text-center py-12 text-slate-400">
                                    <p class="font-semibold text-slate-700 text-xs">Belum ada berkas pengajuan kredit.</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Klik tombol "Tambah Pengajuan" di atas untuk mulai menginput berkas.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_pengajuan as $row): ?>
                                <?php
                                    $jdRaw = $row['jenis_debitur'] ?? '';
                                    $isBadan = (!empty($jdRaw) && (stripos($jdRaw, 'badan') !== false || stripos($jdRaw, 'pt') !== false || stripos($jdRaw, 'cv') !== false));
                                    $tipeKey = $isBadan ? 'badan_usaha' : 'perorangan';
                                    $stInfo = $getStatusInfo($row);
                                ?>
                                <tr class="hover:bg-blue-50/30 transition-colors" id="row-kredit-<?= $row['kredit_id'] ?>" data-jenis-debitur="<?= $tipeKey ?>">
                                    <td class="py-3 px-4 font-semibold text-slate-950 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-[11px] font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-[11px] text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? 'Individu')) ?></div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-700 font-medium">
                                        <?= $isBadan ? 'Badan Usaha' : 'Perorangan' ?>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold text-blue-700 whitespace-nowrap">
                                        Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800"><?= esc($row['skema_pembayaran'] ?? 'Balloon Payment') ?></div>
                                        <div class="text-[11px] text-slate-500"><?= (int)($row['tenor_bulan'] ?? 12) ?> bln &middot; <?= (float)($row['bunga_per_bulan'] ?? 0) ?>%/bln</div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap text-slate-600">
                                        <?= esc($row['jenis_jaminan'] ?? 'On Hand') ?>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold border <?= $stInfo['badge'] ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $stInfo['dot'] ?>"></span>
                                            <span><?= esc($stInfo['label']) ?></span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    onclick="bukaModalHasilReview(<?= $row['kredit_id'] ?>)"
                                                    class="px-2.5 py-1 bg-white text-slate-700 hover:bg-slate-50 hover:text-blue-700 hover:border-blue-300 border border-slate-200 text-[11px] font-semibold rounded-md transition cursor-pointer"
                                                    title="Lihat Keputusan & Catatan Reviewer">
                                                Keputusan
                                            </button>
                                            <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>"
                                               class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200/80 text-[11px] font-semibold rounded-md transition"
                                               title="Buka / Edit Analisa">
                                                Edit
                                            </a>
                                            <button type="button"
                                                    onclick="hapusPengajuan(<?= $row['kredit_id'] ?>, '<?= esc($row['nomor_aplikasi']) ?>')"
                                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer"
                                                    title="Hapus Pengajuan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (< 768px) -->
            <div class="block md:hidden divide-y divide-slate-100" id="mobileCardsContainer">
                <?php if (empty($daftar_pengajuan)): ?>
                    <div class="p-6 text-center text-slate-400" id="mobileEmptyState">
                        <p class="font-semibold text-slate-700 text-xs">Belum ada berkas pengajuan kredit.</p>
                        <p class="text-[11px] text-slate-400 mt-1">Klik tombol "Tambah Pengajuan" di atas untuk mulai menginput berkas.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($daftar_pengajuan as $row): ?>
                        <?php
                            $jdRaw = $row['jenis_debitur'] ?? '';
                            $isBadan = (!empty($jdRaw) && (stripos($jdRaw, 'badan') !== false || stripos($jdRaw, 'pt') !== false || stripos($jdRaw, 'cv') !== false));
                            $tipeKey = $isBadan ? 'badan_usaha' : 'perorangan';
                            $stInfo = $getStatusInfo($row);
                        ?>
                        <div class="p-4 space-y-3 hover:bg-blue-50/20 transition-colors" id="card-kredit-<?= $row['kredit_id'] ?>" data-jenis-debitur="<?= $tipeKey ?>">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="text-xs font-bold text-slate-900"><?= esc($row['nomor_aplikasi']) ?></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?> &middot; <?= date('d M Y', strtotime($row['created_at'])) ?></div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold border <?= $stInfo['badge'] ?> shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $stInfo['dot'] ?>"></span>
                                    <span><?= esc($stInfo['label']) ?></span>
                                </span>
                            </div>

                            <div class="bg-slate-50/80 rounded-lg p-2.5 space-y-1.5 text-xs border border-slate-100">
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-400 text-[11px]">Debitur</span>
                                    <span class="font-semibold text-slate-800 text-right"><?= esc($row['nama_debitur'] ?: '-') ?></span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-400 text-[11px]">Usaha / Tipe</span>
                                    <span class="text-slate-600 text-right"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? 'Individu')) ?> &middot; <span class="font-medium text-slate-700"><?= $isBadan ? 'Badan Usaha' : 'Perorangan' ?></span></span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2 pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-500 font-medium text-[11px]">Plafon</span>
                                    <span class="font-bold text-blue-700">Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-400 text-[11px]">Tenor &amp; Skema</span>
                                    <span class="text-slate-700 text-right"><?= (int)($row['tenor_bulan'] ?? 12) ?> bln &middot; <?= esc($row['skema_pembayaran'] ?? 'Balloon Payment') ?></span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-400 text-[11px]">Jaminan</span>
                                    <span class="text-slate-600"><?= esc($row['jenis_jaminan'] ?? 'On Hand') ?></span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button"
                                        onclick="bukaModalHasilReview(<?= $row['kredit_id'] ?>)"
                                        class="px-2.5 py-1.5 bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 text-xs font-semibold rounded-md transition shadow-xs cursor-pointer">
                                    Keputusan
                                </button>
                                <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>"
                                   class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-semibold rounded-md transition">
                                    Edit
                                </a>
                                <button type="button"
                                        onclick="hapusPengajuan(<?= $row['kredit_id'] ?>, '<?= esc($row['nomor_aplikasi']) ?>')"
                                        class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition cursor-pointer"
                                        title="Hapus Pengajuan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Bar Paginasi Responsif -->
            <div id="paginationBar" class="px-4 sm:px-5 py-3 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div class="tabular-nums text-center sm:text-left">
                    Menampilkan <span id="pageInfoStart" class="font-bold text-slate-900">0</span> &minus; <span id="pageInfoEnd" class="font-bold text-slate-900">0</span> dari <span id="pageInfoTotal" class="font-bold text-slate-900"><?= count($daftar_pengajuan) ?></span> berkas
                </div>
                <div class="flex items-center gap-1.5" id="paginationButtons">
                    <!-- Tombol paginasi akan dirender oleh JavaScript -->
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-3.5 mt-auto">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400">
            <span>&copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &middot; <?= esc($namaLembaga) ?></span>
            <span>Portal Pengusul Kredit</span>
        </div>
    </footer>

    <!-- Modal Detail Profil Petugas Pengusul -->
    <div id="modalProfil" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-md w-full p-6 shadow-xl relative animate-in fade-in duration-200">
            <button type="button" onclick="tutupModalProfil()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Profile Header -->
            <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-500/20 shrink-0">
                    <?= strtoupper(substr($nama_lengkap ?? 'AO', 0, 2)) ?>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 leading-tight"><?= esc($nama_lengkap) ?></h3>
                    <p class="text-xs text-slate-400 mt-0.5">@<?= esc(session()->get('username') ?? 'pengusul') ?></p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 mt-1.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        Petugas Pengusul (AO)
                    </span>
                </div>
            </div>

            <!-- Profile Details List -->
            <div class="space-y-3 py-5 text-xs">
                <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                    <span class="text-slate-500 font-medium">Jabatan / Role</span>
                    <span class="text-slate-800 font-bold"><?= esc($jabatan ?? 'Pengusul Kredit') ?></span>
                </div>
                <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                    <span class="text-slate-500 font-medium">Status Akun</span>
                    <span class="inline-flex items-center gap-1 text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif & Terverifikasi
                    </span>
                </div>
                <div class="flex justify-between items-center p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                    <span class="text-slate-500 font-medium">Lingkup Akses</span>
                    <span class="text-slate-800 font-bold">Input & Analisa Pengajuan (12 Tahap)</span>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" onclick="tutupModalProfil()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Pop-up Laporan Analisa AI & Rekomendasi Komite Kredit (Sesuai Screenshot) -->
    <div id="modalHasilAI" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-5 transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[94vh] flex flex-col overflow-hidden border border-slate-200">
            <!-- Modal Header -->
            <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900" id="aiModalJudul">Laporan Analisis Kredit AI</h3>
                        <p class="text-[11px] text-slate-500">Evaluasi Risiko 5C & Rekomendasi Komite</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Tombol Download / Cetak PDF -->
                    <button type="button" onclick="cetakIframeLaporanAI()"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                    <!-- Tombol Tutup Modal -->
                    <button type="button" onclick="tutupModalHasilAI()" 
                            class="p-2 rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition cursor-pointer" title="Tutup Modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body: Iframe Halaman Laporan Analisis Kredit -->
            <div class="flex-1 overflow-hidden bg-slate-100 p-0">
                <iframe id="iframeLaporanCAM" src="" class="w-full h-[78vh] border-0"></iframe>
            </div>
        </div>
    </div>

    <!-- Modal Hasil Review & Keputusan Reviewer / Komite -->
    <div id="modalHasilReview" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in duration-200">
            <button type="button" onclick="tutupModalHasilReview()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Header Modal -->
            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 leading-tight">Hasil Review & Keputusan Reviewer</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Lembar Keputusan dan Notulensi Komite Kredit Terhadap Pengajuan</p>
                </div>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="overflow-y-auto flex-1 py-4 space-y-4 text-xs pr-1">
                <!-- Info Aplikasi -->
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aplikasi & Debitur</span>
                        <div class="font-bold text-slate-800 text-sm" id="revModalNoAplikasiNama">-</div>
                        <div class="text-slate-500 text-[11px]" id="revModalUsaha">-</div>
                    </div>
                    <div class="sm:text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Plafon Pinjaman</span>
                        <div class="font-extrabold text-blue-700 text-sm" id="revModalPlafon">Rp 0</div>
                    </div>
                </div>

                <!-- Keputusan Utama Reviewer Banner -->
                <div class="p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5" id="revModalStatusBanner">
                    <span class="text-[10px] font-bold uppercase tracking-wider opacity-80">Keputusan Resmi Reviewer / Komite:</span>
                    <div class="text-xl font-black flex items-center gap-2" id="revModalStatusText">
                        <span id="revModalStatusBadge">-</span>
                    </div>
                    <div class="text-xs opacity-90" id="revModalStatusSub">-</div>
                </div>

                <!-- Reviewer & Tanggal Info -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Reviewer / Approver</span>
                        <div class="font-bold text-slate-800 mt-0.5" id="revModalApprover">-</div>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Tanggal Keputusan</span>
                        <div class="font-bold text-slate-800 mt-0.5" id="revModalTanggal">-</div>
                    </div>
                </div>

                <!-- Catatan & Notulensi Reviewer -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                            Catatan Pertimbangan & Notulensi Reviewer
                        </span>
                        <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-100">Evaluasi Komite</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-700 text-xs leading-relaxed italic whitespace-pre-line min-h-[64px]" id="revModalCatatan">
                        -
                    </div>
                </div>

                <!-- Petunjuk Tindak Lanjut Pengusul -->
                <div class="p-3.5 rounded-2xl border text-xs" id="revModalPanduanBox">
                    <span class="font-bold text-slate-800 block mb-0.5" id="revModalPanduanTitle">Instruksi Bagi Petugas Pengusul:</span>
                    <p class="text-slate-600 text-[11px]" id="revModalPanduanDesc">-</p>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end">
                <button type="button" onclick="tutupModalHasilReview()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Data pengajuan yang dikirim dari controller untuk modal cepat
        const pengajuanDataStore = <?= json_encode($daftar_pengajuan ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        // Modal Hasil AI (Menampilkan Dokumen Laporan Analisis Kredit)
        function bukaModalHasilAI(kreditId) {
            const item = (pengajuanDataStore || []).find(x => x.kredit_id == kreditId);
            const judul = item ? `Laporan Analisis Kredit AI - ${item.nomor_aplikasi || ''}` : 'Laporan Analisis Kredit AI';
            const judulElem = document.getElementById('aiModalJudul');
            if (judulElem) judulElem.innerText = judul;

            const iframe = document.getElementById('iframeLaporanCAM');
            if (iframe) {
                iframe.src = '<?= base_url('pengajuan/cetak/') ?>/' + kreditId + '?modal=1';
            }
            const modal = document.getElementById('modalHasilAI');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function tutupModalHasilAI() {
            const modal = document.getElementById('modalHasilAI');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            const iframe = document.getElementById('iframeLaporanCAM');
            if (iframe) {
                iframe.src = '';
            }
        }

        // Cetak dokumen dari iframe modal
        function cetakIframeLaporanAI() {
            const iframe = document.getElementById('iframeLaporanCAM');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        // Modal Hasil Review
        function bukaModalHasilReview(kreditId) {
            const item = (pengajuanDataStore || []).find(x => x.kredit_id == kreditId);
            if (!item) return;

            document.getElementById('revModalNoAplikasiNama').innerText = (item.nomor_aplikasi || '-') + ' \u2022 ' + (item.nama_debitur || '-');
            document.getElementById('revModalUsaha').innerText = item.nama_usaha || item.jenis_debitur || 'Individu';
            document.getElementById('revModalPlafon').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(item.plafon_pengajuan || 0));

            const rawStatus = (item.status_komite || item.status_pengajuan || 'Draft').trim();
            const st = rawStatus.toLowerCase();

            const banner = document.getElementById('revModalStatusBanner');
            const badge = document.getElementById('revModalStatusBadge');
            const sub = document.getElementById('revModalStatusSub');
            const panduanBox = document.getElementById('revModalPanduanBox');
            const panduanTitle = document.getElementById('revModalPanduanTitle');
            const panduanDesc = document.getElementById('revModalPanduanDesc');

            if (st.includes('bersyarat')) {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-amber-50 text-amber-900 border-amber-300 ring-1 ring-amber-200/50';
                badge.innerText = 'DISETUJUI BERSYARAT';
                sub.innerText = 'Pengajuan disetujui dengan ketentuan / syarat tertentu dari Reviewer';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-amber-50/60 border-amber-200 text-amber-900';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Periksa syarat pada catatan reviewer di atas. Hubungi debitur untuk melengkapi dokumen/kondisi yang dipersyaratkan sebelum proses akad.';
            } else if (st.includes('setuju')) {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-emerald-50 text-emerald-900 border-emerald-300 ring-1 ring-emerald-200/50';
                badge.innerText = 'DISETUJUI PENUH';
                sub.innerText = 'Pengajuan telah disetujui resmi oleh Reviewer / Komite Kredit';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-emerald-50/60 border-emerald-200 text-emerald-900';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Pengajuan telah lolos evaluasi. Anda dapat mencetak lembar Analisis Kredit PDF resmi dan mempersiapkan proses akad serta pencairan kredit.';
            } else if (st.includes('tolak')) {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-rose-50 text-rose-900 border-rose-300 ring-1 ring-rose-200/50';
                badge.innerText = 'DITOLAK';
                sub.innerText = 'Pengajuan tidak disetujui oleh Reviewer / Komite Kredit';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-rose-50/60 border-rose-200 text-rose-900';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Pengajuan ditolak berdasarkan mitigasi risiko di atas. Sampaikan penjelasan resmi kepada nasabah secara profesional.';
            } else if (st.includes('revisi')) {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-purple-50 text-purple-900 border-purple-300 ring-1 ring-purple-200/50';
                badge.innerText = 'PERLU REVISI (DIKEMBALIKAN KE AO)';
                sub.innerText = 'Berkas dikembalikan oleh Reviewer untuk perbaikan data / dokumen pendukung';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-purple-50/60 border-purple-200 text-purple-900';
                panduanTitle.innerText = 'Instruksi Tindak Lanjut AO:';
                panduanDesc.innerText = 'Periksa catatan perbaikan Reviewer di atas. Buka tombol "Edit Form" untuk merevisi isian atau dokumen yang diminta, kemudian ajukan ulang pada Tab 12.';
            } else if (st === 'diajukan') {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-blue-50 text-blue-900 border-blue-200';
                badge.innerText = 'SEDANG DIAJUKAN';
                sub.innerText = 'Berkas berada dalam antrean peninjauan oleh Tim Reviewer & Komite';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-blue-50/60 border-blue-200 text-blue-900';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Menunggu sidang atau evaluasi oleh Reviewer. Pastikan berkas pendukung sudah lengkap agar tidak ada kendala.';
            } else {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-slate-50 text-slate-800 border-slate-200';
                badge.innerText = 'STATUS DRAFT';
                sub.innerText = 'Berkas pengajuan masih berstatus draft (belum diajukan ke Reviewer)';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-slate-50 border-slate-200 text-slate-700';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Selesaikan pengisian 12 tahap formulir analisa kredit agar berkas dapat diajukan ke Reviewer & Komite Kredit.';
            }

            document.getElementById('revModalApprover').innerText = item.nama_reviewer || 'Reviewer / Komite Kredit';
            document.getElementById('revModalTanggal').innerText = item.tanggal_review ? item.tanggal_review : '-';

            const catatanElem = document.getElementById('revModalCatatan');
            if (item.catatan_reviewer && item.catatan_reviewer.trim() !== '') {
                catatanElem.innerText = '"' + item.catatan_reviewer.trim() + '"';
                catatanElem.classList.remove('text-slate-400');
                catatanElem.classList.add('text-slate-700');
            } else {
                catatanElem.innerText = 'Belum ada catatan evaluasi dari Reviewer. Keputusan resmi akan dicatat saat sidang komite berlangsung.';
                catatanElem.classList.remove('text-slate-700');
                catatanElem.classList.add('text-slate-400');
            }

            document.getElementById('modalHasilReview').classList.remove('hidden');
        }

        function tutupModalHasilReview() {
            document.getElementById('modalHasilReview').classList.add('hidden');
        }

        // Paginasi & Pencarian Responsif Tabel & Kartu Mobile
        let currentPage = 1;
        const pageSize = 5;

        function filterTabelPengajuan() {
            currentPage = 1;
            renderPagination();
        }

        function goToPage(page) {
            currentPage = page;
            renderPagination();
        }

        function renderPagination() {
            const filterSelect = document.getElementById('filterTipeDebitur');
            const selectedTipe = filterSelect ? filterSelect.value : 'semua';
            const searchInput = document.getElementById('searchInput');
            const term = (searchInput?.value || '').toLowerCase().trim();

            const rows = Array.from(document.querySelectorAll('#tabelPengajuan tbody tr[id^="row-kredit-"]'));
            const cards = Array.from(document.querySelectorAll('#mobileCardsContainer div[id^="card-kredit-"]'));

            const matchedRows = [];
            const matchedCards = [];

            // Filter Desktop Rows
            rows.forEach(row => {
                const rowTipe = row.getAttribute('data-jenis-debitur') || '';
                const rowText = row.innerText.toLowerCase();
                const matchTipe = (selectedTipe === 'semua' || rowTipe === selectedTipe);
                const matchSearch = (!term || rowText.includes(term));

                if (matchTipe && matchSearch) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter Mobile Cards
            cards.forEach(card => {
                const cardTipe = card.getAttribute('data-jenis-debitur') || '';
                const cardText = card.innerText.toLowerCase();
                const matchTipe = (selectedTipe === 'semua' || cardTipe === selectedTipe);
                const matchSearch = (!term || cardText.includes(term));

                if (matchTipe && matchSearch) {
                    matchedCards.push(card);
                } else {
                    card.style.display = 'none';
                }
            });

            const totalMatches = matchedRows.length;
            const totalPages = Math.max(1, Math.ceil(totalMatches / pageSize));

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;

            // Render slice baris desktop
            matchedRows.forEach((row, idx) => {
                row.style.display = (idx >= startIndex && idx < endIndex) ? '' : 'none';
            });

            // Render slice kartu mobile
            matchedCards.forEach((card, idx) => {
                card.style.display = (idx >= startIndex && idx < endIndex) ? '' : 'none';
            });

            // Update badge dan info jumlah
            const countDisplay = document.getElementById('countDisplay');
            if (countDisplay) countDisplay.innerText = totalMatches;

            const pageInfoStart = document.getElementById('pageInfoStart');
            const pageInfoEnd = document.getElementById('pageInfoEnd');
            const pageInfoTotal = document.getElementById('pageInfoTotal');

            if (pageInfoStart && pageInfoEnd && pageInfoTotal) {
                if (totalMatches === 0) {
                    pageInfoStart.innerText = '0';
                    pageInfoEnd.innerText = '0';
                } else {
                    pageInfoStart.innerText = (startIndex + 1);
                    pageInfoEnd.innerText = Math.min(endIndex, totalMatches);
                }
                pageInfoTotal.innerText = totalMatches;
            }

            // Render Tombol Paginasi
            const paginationButtons = document.getElementById('paginationButtons');
            if (!paginationButtons) return;

            if (totalMatches <= pageSize) {
                if (totalMatches === 0) {
                    paginationButtons.innerHTML = '<span class="text-slate-400 text-xs italic">Tidak ada berkas yang cocok</span>';
                } else {
                    paginationButtons.innerHTML = `
                        <button type="button" disabled class="px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-200 text-slate-300 bg-white cursor-not-allowed">
                            Sebelumnya
                        </button>
                        <button type="button" class="w-7 h-7 text-xs font-bold rounded-md bg-blue-600 text-white shadow-xs">
                            1
                        </button>
                        <button type="button" disabled class="px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-200 text-slate-300 bg-white cursor-not-allowed">
                            Selanjutnya
                        </button>
                    `;
                }
                return;
            }

            let btnHtml = '';

            // Tombol Prev
            const prevDisabled = (currentPage === 1);
            btnHtml += `
                <button type="button" onclick="goToPage(${currentPage - 1})" ${prevDisabled ? 'disabled' : ''}
                        class="px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-200 ${prevDisabled ? 'text-slate-300 bg-white cursor-not-allowed' : 'text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-700 hover:border-blue-300 cursor-pointer shadow-xs transition'}">
                    Sebelumnya
                </button>
            `;

            // Tombol Angka Halaman
            for (let p = 1; p <= totalPages; p++) {
                if (totalPages > 7) {
                    if (p !== 1 && p !== totalPages && Math.abs(p - currentPage) > 1) {
                        if (p === 2 && currentPage > 3) {
                            btnHtml += `<span class="px-1 text-slate-400">...</span>`;
                        } else if (p === totalPages - 1 && currentPage < totalPages - 2) {
                            btnHtml += `<span class="px-1 text-slate-400">...</span>`;
                        }
                        continue;
                    }
                }

                if (p === currentPage) {
                    btnHtml += `
                        <button type="button" class="w-7 h-7 text-xs font-bold rounded-md bg-blue-600 text-white shadow-xs flex items-center justify-center">
                            ${p}
                        </button>
                    `;
                } else {
                    btnHtml += `
                        <button type="button" onclick="goToPage(${p})"
                                class="w-7 h-7 text-xs font-semibold rounded-md border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-700 hover:border-blue-300 transition flex items-center justify-center cursor-pointer shadow-xs">
                            ${p}
                        </button>
                    `;
                }
            }

            // Tombol Next
            const nextDisabled = (currentPage === totalPages);
            btnHtml += `
                <button type="button" onclick="goToPage(${currentPage + 1})" ${nextDisabled ? 'disabled' : ''}
                        class="px-2.5 py-1 text-xs font-semibold rounded-md border border-slate-200 ${nextDisabled ? 'text-slate-300 bg-white cursor-not-allowed' : 'text-slate-600 bg-white hover:bg-slate-50 hover:text-blue-700 hover:border-blue-300 cursor-pointer shadow-xs transition'}">
                    Selanjutnya
                </button>
            `;

            paginationButtons.innerHTML = btnHtml;
        }

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', filterTabelPengajuan);
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderPagination();
        });

        // Dropdown Profil
        function toggleProfileDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('dropdownMenuProfil');
            if (menu) menu.classList.toggle('hidden');
        }

        // Tutup dropdown & modal saat klik di luar
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userProfileDropdown');
            const menu = document.getElementById('dropdownMenuProfil');
            if (dropdown && !dropdown.contains(e.target) && menu) {
                menu.classList.add('hidden');
            }

            const mAi = document.getElementById('modalHasilAI');
            if (mAi && e.target === mAi) {
                tutupModalHasilAI();
            }
            const mRev = document.getElementById('modalHasilReview');
            if (mRev && e.target === mRev) {
                tutupModalHasilReview();
            }
        });

        // Modal Profil Petugas Pengusul (opsional)
        function bukaModalProfil() {
            const m = document.getElementById('modalProfil');
            if (m) m.classList.remove('hidden');
        }
        function tutupModalProfil() {
            const m = document.getElementById('modalProfil');
            if (m) m.classList.add('hidden');
        }

        function hapusPengajuan(id, nomor) {
            Swal.fire({
                title: 'Hapus Berkas Pengajuan?',
                html: `Apakah Anda yakin ingin menghapus berkas nomor <b>${nomor}</b>?<br><small class="text-slate-400">Data yang dihapus tidak dapat dipulihkan kembali.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const tr = document.getElementById(`row-kredit-${id}`);
                    const card = document.getElementById(`card-kredit-${id}`);
                    if (tr) tr.style.opacity = '0.5';
                    if (card) card.style.opacity = '0.5';

                    fetch(`<?= base_url('pengajuan/hapus') ?>/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (tr) tr.remove();
                            if (card) card.remove();
                            renderPagination();
                            Swal.fire({
                                title: 'Berhasil Dihapus',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            if (tr) tr.style.opacity = '1';
                            if (card) card.style.opacity = '1';
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    })
                    .catch(err => {
                        if (tr) tr.style.opacity = '1';
                        if (card) card.style.opacity = '1';
                        Swal.fire('Error!', 'Koneksi terputus atau terjadi kesalahan.', 'error');
                    });
                }
            });
        }
    </script>
    <!-- Modal Pilihan Jenis Debitur (Perorangan vs Badan Usaha) -->
    <div id="modalPilihDebitur" class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-lg max-w-lg w-full shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-950">Pilih Kategori Pengajuan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih formulir sesuai jenis entitas calon debitur.</p>
                </div>
                <button type="button" onclick="tutupModalPilihDebitur()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-100 transition cursor-pointer" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Pilihan 1: Perorangan -->
                <a href="<?= base_url('pengajuan/baru?tipe=perorangan') ?>" class="group p-4 rounded-lg border border-slate-200 hover:border-blue-700 hover:bg-blue-50/40 transition flex flex-col justify-between text-left">
                    <div>
                        <div class="w-9 h-9 rounded-md bg-blue-50 border border-blue-200/80 text-blue-700 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-950 group-hover:text-blue-700 mb-1">Perorangan</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Individu, karyawan, atau usaha mikro.</p>
                    </div>
                    <span class="mt-4 inline-flex items-center text-xs font-semibold text-blue-700">
                        Buka Form &rarr;
                    </span>
                </a>

                <!-- Pilihan 2: Badan Usaha -->
                <a href="<?= base_url('pengajuan/baru?tipe=badan_usaha') ?>" class="group p-4 rounded-lg border border-slate-200 hover:border-blue-700 hover:bg-blue-50/40 transition flex flex-col justify-between text-left">
                    <div>
                        <div class="w-9 h-9 rounded-md bg-blue-50 border border-blue-200/80 text-blue-700 flex items-center justify-center mb-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-950 group-hover:text-blue-700 mb-1">Badan Usaha</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">PT, CV, Koperasi, atau Yayasan.</p>
                    </div>
                    <span class="mt-4 inline-flex items-center text-xs font-semibold text-blue-700">
                        Buka Form &rarr;
                    </span>
                </a>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="button" onclick="tutupModalPilihDebitur()" class="px-3.5 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-100 text-xs font-medium text-slate-600 transition cursor-pointer">Batal</button>
            </div>
        </div>
    </div>

    <script>
        function bukaModalPilihDebitur() {
            document.getElementById('modalPilihDebitur').classList.remove('hidden');
        }
        function tutupModalPilihDebitur() {
            document.getElementById('modalPilihDebitur').classList.add('hidden');
        }
    </script>
</body>
</html>
