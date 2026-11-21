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
 * @var int $total_slik_checked
 * @var int $total_slik_lancar
 * @var int $total_slik_tunggakan
 * @var int $total_menunggu_slik
 * @var array $daftar_pengajuan
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Workspace Petugas SLIK') ?></title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
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
    $namaWeb     = !empty($rawNamaWeb) ? $rawNamaWeb : 'OTW Approved';
    $rawLembaga  = !empty($pengaturan_sistem['nama_lembaga']) ? trim($pengaturan_sistem['nama_lembaga']) : '';
    $namaLembaga = !empty($rawLembaga) ? $rawLembaga : 'Lembaga Pembiayaan';
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

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-900 leading-tight"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <?= esc(!empty($jabatan) ? $jabatan : 'Petugas SLIK') ?>
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
                            $avatarSrc = !empty($fotoProfil) ? base_url($fotoProfil) : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap ?? 'SLIK') . '&background=2563eb&color=fff&bold=true';
                        ?>
                        <button type="button" onclick="toggleProfileDropdown(event)" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-blue-400 focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                            <img src="<?= $avatarSrc ?>" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap ?? 'SLIK') ?>&background=2563eb&color=fff&bold=true';" 
                                 alt="Foto Profil" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenuProfil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 animate-in fade-in duration-150">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-bold text-slate-800 truncate"><?= esc($nama_lengkap) ?></p>
                                <p class="text-[10px] text-slate-400"><?= esc(!empty($jabatan) ? $jabatan : 'Petugas SLIK') ?></p>
                            </div>
                            
                            <a href="<?= base_url('profil') ?>" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Edit Profil
                            </a>
                            
                            <div class="border-t border-slate-100 my-1"></div>

                            <a href="<?= base_url('auth/logout') ?>" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 flex-1 min-w-0 space-y-5">
        
        <!-- Welcome Hero Banner -->
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-7 text-white shadow-lg shadow-blue-500/10 relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-5" style="background-color: #1d4ed8; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #4338ca 100%); color: #ffffff;">
            <!-- Ambient Decorative Elements -->
            <div class="pointer-events-none absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
            <div class="pointer-events-none absolute -left-10 -top-10 w-48 h-48 bg-sky-400/15 rounded-full blur-xl"></div>

            <div class="relative z-10 min-w-0">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-white/15 text-blue-100 uppercase tracking-wider backdrop-blur-xs border border-white/10 mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Ruang Kerja <?= esc(!empty($jabatan) ? $jabatan : 'Petugas SLIK') ?></span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Verifikasi SLIK OJK / CLIK
                </h1>
                <p class="text-xs sm:text-sm text-blue-100/90 mt-1 max-w-2xl leading-relaxed">
                    Periksa riwayat fasilitas pembiayaan nasabah dan terbitkan status kolektibilitas untuk mendukung evaluasi 5C.
                </p>
            </div>
        </div>

        <!-- 5-Column Metric Cards (Selaras dengan Reviewer) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5 tabular-nums">
            <!-- Card 1: Total Berkas Masuk -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Berkas Masuk</span>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2"><?= number_format($total_pengajuan ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    <span>Total nasabah terdaftar</span>
                </div>
            </div>

            <!-- Card 2: Menunggu Cek SLIK -->
            <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/40 rounded-2xl p-5 border border-blue-200/90 shadow-sm shadow-blue-500/5 hover:shadow-md hover:border-blue-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span>Menunggu Cek SLIK</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-blue-700 mt-2"><?= number_format($total_menunggu_slik ?? max(0, ($total_pengajuan ?? 0) - ($total_slik_checked ?? 0))) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/60 inline-flex items-center gap-1">Perlu verifikasi iDeb</span>
                </div>
            </div>

            <!-- Card 3: SLIK Terbit -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>SLIK Terbit</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-2"><?= number_format($total_slik_checked ?? 0) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 inline-flex items-center gap-1">Selesai diverifikasi</span>
                </div>
            </div>

            <!-- Card 4: Kol 1 (Lancar) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Kol 1 (Lancar)</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-2"><?= number_format($total_slik_lancar ?? 0) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 inline-flex items-center gap-1">Bebas tunggakan</span>
                </div>
            </div>

            <!-- Card 5: Ada Tunggakan (Kol 2-5) -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition-all duration-200 relative overflow-hidden group">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Ada Tunggakan</span>
                    </span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-rose-700 mt-2"><?= number_format($total_slik_tunggakan ?? 0) ?></div>
                <div class="mt-1">
                    <span class="text-xs text-rose-700 font-semibold bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200/60 inline-flex items-center gap-1">Kol 2 &minus; 5 bermasalah</span>
                </div>
            </div>
        </div>

        <!-- Table Card: Daftar Berkas Pengajuan untuk Verifikasi SLIK (Full Width) -->
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
                    <div class="relative w-full sm:w-44 shrink-0">
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

                    <!-- Dropdown Filter Status SLIK -->
                    <div class="relative w-full sm:w-48 shrink-0">
                        <select id="filterStatusSlik" onchange="filterTabelPengajuan()"
                                class="w-full h-9 appearance-none pl-3 pr-8 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700/10 focus:border-blue-700 transition cursor-pointer">
                            <option value="semua">Semua Status SLIK</option>
                            <option value="lancar">Kol 1 (Lancar)</option>
                            <option value="tunggakan">Kol 2-5 (Tunggakan)</option>
                            <option value="belum_terbit">Belum Terbit / Menunggu</option>
                        </select>
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 justify-between sm:justify-end">
                    <div class="text-xs text-slate-500">
                        Total <span id="countDisplay" class="font-semibold text-slate-900 tabular-nums"><?= count($daftar_pengajuan ?? []) ?></span> pengajuan
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="text-xs font-semibold text-blue-700 hover:text-blue-800 hidden sm:inline">
                        Lihat Semua &rarr;
                    </a>
                </div>
            </div>

            <?php
                $getStatusInfo = function($row) {
                    $rawStatus = trim($row['status_pengajuan'] ?? '');
                    if (empty($rawStatus)) {
                        $rawStatus = 'Draft';
                    }
                    $st = strtolower($rawStatus);

                    if (strpos($st, 'bersyarat') !== false) {
                        return ['key' => 'bersyarat', 'badge' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500', 'label' => 'Bersyarat'];
                    } elseif ($st === 'disetujui' || strpos($st, 'setuju') !== false) {
                        return ['key' => 'disetujui', 'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-600', 'label' => 'Disetujui'];
                    } elseif ($st === 'ditolak' || strpos($st, 'tolak') !== false) {
                        return ['key' => 'ditolak', 'badge' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-600', 'label' => 'Ditolak'];
                    } elseif ($st === 'revisi' || strpos($st, 'revisi') !== false) {
                        return ['key' => 'revisi', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200', 'dot' => 'bg-purple-600', 'label' => 'Perlu Revisi'];
                    } elseif ($st === 'diajukan') {
                        return ['key' => 'diajukan', 'badge' => 'bg-blue-50 text-blue-700 border-blue-200', 'dot' => 'bg-blue-700', 'label' => 'Diajukan'];
                    }
                    return ['key' => 'draft', 'badge' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'Draft'];
                };

                $getSlikInfo = function($row) {
                    $slikStatus = $row['status_slik'] ?? 'Belum Terbit';
                    if ($slikStatus === 'Terbit') {
                        if (!empty($row['ada_tunggakan_slik'])) {
                            return ['key' => 'tunggakan', 'class' => 'bg-rose-50 text-rose-700 border-rose-200', 'text' => 'Kol 2-5 (Tunggakan)'];
                        } else {
                            return ['key' => 'lancar', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'text' => 'Kol 1 (Lancar)'];
                        }
                    } elseif ($slikStatus === 'Menunggu Pengecekan') {
                        return ['key' => 'belum_terbit', 'class' => 'bg-blue-50 text-blue-700 border-blue-200', 'text' => 'Menunggu Cek'];
                    }
                    return ['key' => 'belum_terbit', 'class' => 'bg-amber-50 text-amber-700 border-amber-200', 'text' => 'Belum Terbit'];
                };
            ?>

            <!-- Desktop Table View (>= 768px) -->
            <div class="hidden md:block w-full overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[860px] text-left text-xs text-slate-600 tabular-nums" id="tabelPengajuan">
                    <thead class="bg-slate-50 text-[11px] font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">No. Aplikasi</th>
                            <th class="py-3 px-4">Nama Debitur &amp; Usaha</th>
                            <th class="py-3 px-4">Tipe</th>
                            <th class="py-3 px-4 text-right">Plafon &amp; Tenor</th>
                            <th class="py-3 px-4 text-center">Status SLIK</th>
                            <th class="py-3 px-4 text-center">Status Pengajuan</th>
                            <th class="py-3 px-4">Hasil &amp; Fasilitas SLIK</th>
                            <th class="py-3 px-4">Tanggal Permohonan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200/80">
                        <?php if (empty($daftar_pengajuan)): ?>
                            <tr id="emptyTableRow">
                                <td colspan="9" class="text-center py-12 text-slate-400">
                                    <p class="font-semibold text-slate-700 text-xs">Belum ada berkas pengajuan kredit yang masuk.</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Daftar berkas nasabah yang diinput staf pengusul akan otomatis muncul di sini untuk verifikasi riwayat SLIK OJK.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_pengajuan as $row): ?>
                                <?php
                                    $jdRaw = $row['jenis_debitur'] ?? '';
                                    $isBadan = (!empty($jdRaw) && (stripos($jdRaw, 'badan') !== false || stripos($jdRaw, 'pt') !== false || stripos($jdRaw, 'cv') !== false));
                                    $tipeKey = $isBadan ? 'badan_usaha' : 'perorangan';
                                    $stInfo = $getStatusInfo($row);
                                    $slInfo = $getSlikInfo($row);
                                    $isTerbit = (($row['status_slik'] ?? '') === 'Terbit');
                                ?>
                                <tr class="hover:bg-blue-50/30 transition-colors" id="row-kredit-<?= $row['kredit_id'] ?>" data-jenis-debitur="<?= $tipeKey ?>" data-status-slik="<?= $slInfo['key'] ?>">
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
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <div class="font-semibold text-blue-700">Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></div>
                                        <div class="text-[11px] text-slate-500"><?= (int)($row['tenor_bulan'] ?? 12) ?> bln &middot; <?= esc($row['skema_pembayaran'] ?? 'Installment') ?></div>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border <?= $slInfo['class'] ?>">
                                            <?= esc($slInfo['text']) ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold border <?= $stInfo['badge'] ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $stInfo['dot'] ?>"></span>
                                            <span><?= esc($stInfo['label']) ?></span>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 min-w-[190px]">
                                        <?php if ($isTerbit): ?>
                                            <div>
                                                <?php if (!empty($row['ada_tunggakan_slik'])): ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Kol 2 &minus; 5 (Tunggakan)
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Kol 1 (Lancar / Clear)
                                                    </span>
                                                <?php endif; ?>
                                                <div class="text-[11px] text-slate-500 mt-0.5">
                                                    Baki Debet: <span class="font-semibold text-slate-700">Rp <?= number_format((float)($row['total_baki_debet_slik'] ?? 0), 0, ',', '.') ?></span>
                                                    <span class="text-slate-400">(<?= (int)($row['total_slik_records'] ?? 0) ?> Fasilitas)</span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-[11px] text-slate-400 italic font-normal flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                                <span>Menunggu Cek SLIK</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 text-[11px] text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="<?= base_url('slik/verifikasi/' . $row['kredit_id']) ?>"
                                               class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-[11px] font-semibold rounded-md transition shadow-xs flex items-center gap-1"
                                               title="Buka Form Verifikasi SLIK OJK">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                <span>Verifikasi SLIK</span>
                                            </a>
                                            <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank"
                                               class="p-1 text-slate-400 hover:text-blue-700 hover:bg-blue-50 rounded-md transition" 
                                               title="Lihat / Cetak Ringkasan PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
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
                        <p class="font-semibold text-slate-700 text-xs">Belum ada berkas pengajuan kredit yang masuk.</p>
                        <p class="text-[11px] text-slate-400 mt-1">Daftar berkas nasabah yang diinput staf pengusul akan otomatis muncul di sini.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($daftar_pengajuan as $row): ?>
                        <?php
                            $jdRaw = $row['jenis_debitur'] ?? '';
                            $isBadan = (!empty($jdRaw) && (stripos($jdRaw, 'badan') !== false || stripos($jdRaw, 'pt') !== false || stripos($jdRaw, 'cv') !== false));
                            $tipeKey = $isBadan ? 'badan_usaha' : 'perorangan';
                            $stInfo = $getStatusInfo($row);
                            $slInfo = $getSlikInfo($row);
                            $isTerbit = (($row['status_slik'] ?? '') === 'Terbit');
                        ?>
                        <div class="p-4 space-y-3 hover:bg-blue-50/20 transition-colors" id="card-kredit-<?= $row['kredit_id'] ?>" data-jenis-debitur="<?= $tipeKey ?>" data-status-slik="<?= $slInfo['key'] ?>">
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
                                    <span class="text-slate-700 text-right"><?= (int)($row['tenor_bulan'] ?? 12) ?> bln &middot; <?= esc($row['skema_pembayaran'] ?? 'Installment') ?></span>
                                </div>
                                <div class="flex justify-between items-baseline gap-2">
                                    <span class="text-slate-400 text-[11px]">Status SLIK</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border <?= $slInfo['class'] ?>">
                                        <?= esc($slInfo['text']) ?>
                                    </span>
                                </div>
                                <?php if ($isTerbit): ?>
                                    <div class="pt-1 border-t border-slate-200/60 flex justify-between items-baseline gap-2">
                                        <span class="text-slate-400 text-[11px]">Total Baki Debet</span>
                                        <span class="text-slate-700 font-semibold text-right">Rp <?= number_format((float)($row['total_baki_debet_slik'] ?? 0), 0, ',', '.') ?> (<?= (int)($row['total_slik_records'] ?? 0) ?> Fasilitas)</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1">
                                <a href="<?= base_url('slik/verifikasi/' . $row['kredit_id']) ?>"
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold rounded-md transition shadow-xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>Verifikasi SLIK</span>
                                </a>
                                <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank"
                                   class="px-2.5 py-1.5 text-slate-600 hover:text-blue-700 hover:bg-slate-100 border border-slate-200 rounded-md transition text-xs font-semibold flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    <span>Cetak</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Bar Paginasi Responsif -->
            <div id="paginationBar" class="px-4 sm:px-5 py-3 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div class="tabular-nums text-center sm:text-left">
                    Menampilkan <span id="pageInfoStart" class="font-bold text-slate-900">0</span> &minus; <span id="pageInfoEnd" class="font-bold text-slate-900">0</span> dari <span id="pageInfoTotal" class="font-bold text-slate-900"><?= count($daftar_pengajuan ?? []) ?></span> berkas
                </div>
                <div class="flex items-center gap-1.5" id="paginationButtons">
                    <!-- Tombol paginasi akan dirender oleh JavaScript -->
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Hak Akses <?= esc(!empty($jabatan) ? $jabatan : 'Petugas SLIK') ?>
        </div>
    </footer>

    <script>
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
            const filterDebitur = document.getElementById('filterTipeDebitur');
            const selectedTipe = filterDebitur ? filterDebitur.value : 'semua';

            const filterSlikElem = document.getElementById('filterStatusSlik');
            const selectedSlik = filterSlikElem ? filterSlikElem.value : 'semua';

            const searchInput = document.getElementById('searchInput');
            const term = (searchInput?.value || '').toLowerCase().trim();

            const rows = Array.from(document.querySelectorAll('#tabelPengajuan tbody tr[id^="row-kredit-"]'));
            const cards = Array.from(document.querySelectorAll('#mobileCardsContainer div[id^="card-kredit-"]'));

            const matchedRows = [];
            const matchedCards = [];

            // Filter Desktop Rows
            rows.forEach(row => {
                const rowTipe = row.getAttribute('data-jenis-debitur') || '';
                const rowSlik = row.getAttribute('data-status-slik') || '';
                const rowText = row.innerText.toLowerCase();

                const matchTipe = (selectedTipe === 'semua' || rowTipe === selectedTipe);
                const matchSlik = (selectedSlik === 'semua' || rowSlik === selectedSlik);
                const matchSearch = (!term || rowText.includes(term));

                if (matchTipe && matchSlik && matchSearch) {
                    matchedRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });

            // Filter Mobile Cards
            cards.forEach(card => {
                const cardTipe = card.getAttribute('data-jenis-debitur') || '';
                const cardSlik = card.getAttribute('data-status-slik') || '';
                const cardText = card.innerText.toLowerCase();

                const matchTipe = (selectedTipe === 'semua' || cardTipe === selectedTipe);
                const matchSlik = (selectedSlik === 'semua' || cardSlik === selectedSlik);
                const matchSearch = (!term || cardText.includes(term));

                if (matchTipe && matchSlik && matchSearch) {
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

            // Update info jumlah
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

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userProfileDropdown');
            const menu = document.getElementById('dropdownMenuProfil');
            if (dropdown && !dropdown.contains(e.target) && menu) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
