<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | OTW Credit Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $namaWeb       = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine & CAM';
    $namaLembaga   = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb       = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';
    $isPengusul    = strtolower(trim($jabatan ?? '')) === 'pengusul';
    $roleBadgeText = $isPengusul ? 'Petugas Pengusul' : esc($jabatan);

    // Hak akses tombol hapus: Hanya untuk CEO / Direktur / Superadmin, Reviewer TIDAK ADA
    $userRole = strtolower(trim($jabatan ?? session()->get('jabatan') ?? ''));
    $canDelete = (strpos($userRole, 'ceo') !== false || strpos($userRole, 'direk') !== false || $userRole === 'superadmin' || $userRole === 'admin');

    // Inisial dinamis
    $words = preg_split("/[\s,_-]+/", trim($namaWeb));
    $initials = '';
    foreach ($words as $w) {
        if (!empty($w)) $initials .= mb_substr($w, 0, 1);
        if (mb_strlen($initials) >= 2) break;
    }
    if (empty($initials)) $initials = 'OT';
    $initials = strtoupper($initials);
?>
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoWeb)): ?>
                        <img src="<?= base_url($logoWeb) ?>" 
                             alt="<?= esc($namaWeb) ?>" 
                             class="w-10 h-10 object-contain rounded-xl border border-slate-200 bg-white p-1 shadow-xs shrink-0">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm shrink-0 tracking-wider">
                            <?= esc($initials) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight"><?= esc($namaWeb) ?></span>
                        <span class="text-xs block text-slate-500 font-medium"><?= esc($namaLembaga) ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-900 leading-tight"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <?= $roleBadgeText ?>
                        </span>
                    </div>
                    <a href="<?= base_url('dashboard') ?>" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Dashboard
                    </a>

                    <!-- Profile Avatar & Dropdown -->
                    <div class="relative" id="userProfileDropdown">
                        <?php
                            $fotoProfil = session()->get('foto');
                            if (empty($fotoProfil) && session()->get('user_id')) {
                                $uRow = \Config\Database::connect()->table('users')->select('foto')->where('id', session()->get('user_id'))->get()->getRowArray();
                                $fotoProfil = $uRow['foto'] ?? '';
                            }
                            $avatarSrc = !empty($fotoProfil) ? base_url($fotoProfil) : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap ?? 'AO') . '&background=2563eb&color=fff&bold=true';
                        ?>
                        <button type="button" onclick="toggleProfileDropdown(event)" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-blue-400 focus:ring-2 focus:ring-blue-500 transition cursor-pointer">
                            <img src="<?= $avatarSrc ?>" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap ?? 'AO') ?>&background=2563eb&color=fff&bold=true';" 
                                 alt="Foto Profil" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu (Edit Profil & Logout) -->
                        <div id="dropdownMenuProfil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 animate-in fade-in duration-150">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-bold text-slate-800 truncate"><?= esc($nama_lengkap) ?></p>
                                <p class="text-[10px] text-slate-400"><?= $roleBadgeText ?></p>
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

    <!-- Main Content -->
    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 py-6">
        
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                <span><?= session()->getFlashdata('success') ?></span>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between">
                <span><?= session()->getFlashdata('error') ?></span>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Tombol Kembali di Sebelah Kiri Atas Judul -->
        <div class="mb-4">
            <a href="<?= base_url('dashboard') ?>" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 hover:text-slate-900 font-bold text-xs rounded-xl shadow-xs transition" title="Kembali ke Dashboard">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Pengajuan Kredit (CAM)</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data pembiayaan, evaluasi profil risiko, dan cetak credit memo.</p>
            </div>
            <!-- Tombol Download & Dropdown Filter Rentang Waktu -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Tombol Download CSV/Excel -->
                <a href="<?= base_url('pengajuan/download') ?>" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-emerald-700 font-bold text-xs rounded-xl shadow-xs transition" title="Download Ekspor Data Pengajuan (CSV/Excel)">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download</span>
                </a>

                <!-- Dropdown Filter Rentang Waktu -->
                <div class="relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <select id="filterRentangWaktu" onchange="filterTabelPengajuan()" 
                            class="w-full pl-10 pr-9 py-2.5 text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl appearance-none shadow-xs focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition outline-none cursor-pointer">
                        <option value="semua">Semua Rentang Waktu</option>
                        <option value="hari_ini">Hari Ini</option>
                        <option value="7_hari">7 Hari Terakhir</option>
                        <option value="30_hari">30 Hari Terakhir</option>
                        <option value="bulan_ini">Bulan Ini</option>
                        <option value="bulan_lalu">Bulan Lalu</option>
                        <option value="tahun_ini">Tahun Ini</option>
                        <option value="custom">Kustom Rentang Tanggal...</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Input Kustom Rentang Tanggal -->
                <div id="customDateRangeBox" class="hidden flex items-center gap-1.5 bg-white border border-slate-300 rounded-xl px-2.5 py-1.5 shadow-xs">
                    <input type="date" id="tglMulai" onchange="filterTabelPengajuan()" class="text-xs font-semibold text-slate-700 outline-none border-none bg-transparent cursor-pointer" title="Tanggal Mulai">
                    <span class="text-xs text-slate-400 font-bold">&ndash;</span>
                    <input type="date" id="tglSelesai" onchange="filterTabelPengajuan()" class="text-xs font-semibold text-slate-700 outline-none border-none bg-transparent cursor-pointer" title="Tanggal Selesai">
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <?php
            $totalPengajuan = count($daftar_kredit);
            $totalPlafon = 0;
            $approvedCount = 0;
            $draftCount = 0;
            foreach ($daftar_kredit as $k) {
                $totalPlafon += (float)($k['plafon_pengajuan'] ?? 0);
                if (strtolower($k['status_pengajuan'] ?? '') === 'disetujui') $approvedCount++;
                if (strtolower($k['status_pengajuan'] ?? '') === 'draft') $draftCount++;
            }
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Aplikasi</div>
                <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($totalPengajuan) ?></div>
                <div class="text-xs text-slate-400 mt-1">Berkas terdaftar di sistem</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Plafon Diajukan</div>
                <div class="text-2xl font-extrabold text-blue-700 mt-1">Rp <?= number_format($totalPlafon, 0, ',', '.') ?></div>
                <div class="text-xs text-slate-400 mt-1">Akumulasi seluruh aplikasi</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui Komite</div>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= number_format($approvedCount) ?></div>
                <div class="text-xs text-slate-400 mt-1">Siap proses akad & pencairan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Draft / Dalam Analisa</div>
                <div class="text-2xl font-extrabold text-amber-700 mt-1"><?= number_format($draftCount) ?></div>
                <div class="text-xs text-slate-400 mt-1">Menunggu finalisasi data</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nomor CAM, debitur, usaha..." 
                           class="w-full pl-9 pr-3 py-2 text-xs font-medium border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span id="countDisplay" class="font-bold text-slate-800"><?= count($daftar_kredit) ?></span> pengajuan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600" id="tabelPengajuan">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">No. Aplikasi</th>
                            <th class="py-3.5 px-4">Nama Debitur & Usaha</th>
                            <th class="py-3.5 px-4">Plafon Pinjaman</th>
                            <th class="py-3.5 px-4">Skema & Tenor</th>
                            <th class="py-3.5 px-4">Jaminan</th>
                            <th class="py-3.5 px-4 text-center">Status Pengajuan</th>
                            <th class="py-3.5 px-4">Tanggal Buat</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-normal">
                        <?php if (empty($daftar_kredit)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-600">Belum ada pengajuan kredit yang diinput.</p>
                                    <p class="text-xs text-slate-400 mt-1">Belum ada data pengajuan kredit yang tercatat di sistem.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_kredit as $row): ?>
                                <tr class="hover:bg-slate-50 transition" id="row-kredit-<?= $row['kredit_id'] ?>" data-tanggal="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-xs font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-xs text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? '-')) ?></div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-blue-700 whitespace-nowrap">
                                        Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800"><?= esc($row['skema_pembayaran'] ?? 'Balloon') ?></div>
                                        <div class="text-xs text-slate-500"><?= (int)($row['tenor_bulan'] ?? 12) ?> Bulan (<?= (float)($row['bunga_per_bulan'] ?? 0) ?>%/bln)</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                            <?= esc($row['jenis_jaminan'] ?? 'On Hand') ?>
                                        </span>
                                    </td>
                                    <!-- Status Pengajuan -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php
                                            $rawStatus = trim($row['status_pengajuan'] ?? '');
                                            if (empty($rawStatus) && !empty($row['status_komite'])) {
                                                $rawStatus = trim($row['status_komite']);
                                            }
                                            if (empty($rawStatus)) {
                                                $rawStatus = 'Draft';
                                            }
                                            $st = strtolower($rawStatus);

                                            if (strpos($st, 'bersyarat') !== false) {
                                                $badgeClass = 'bg-amber-50 text-amber-800 border-amber-300 ring-1 ring-amber-200/50';
                                                $dotClass   = 'bg-amber-500';
                                                $statusLabel= 'Bersyarat';
                                            } elseif ($st === 'disetujui' || strpos($st, 'setuju') !== false) {
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-1 ring-emerald-200/50';
                                                $dotClass   = 'bg-emerald-500';
                                                $statusLabel= 'Disetujui';
                                            } elseif ($st === 'ditolak' || strpos($st, 'tolak') !== false) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-200/50';
                                                $dotClass   = 'bg-rose-500';
                                                $statusLabel= 'Ditolak';
                                            } elseif ($st === 'diajukan') {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $dotClass   = 'bg-blue-500';
                                                $statusLabel= 'Diajukan';
                                            } else {
                                                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                                $dotClass   = 'bg-slate-400';
                                                $statusLabel= 'Draft';
                                            }
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border <?= $badgeClass ?> shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $dotClass ?>"></span>
                                            <span><?= esc($statusLabel) ?></span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <!-- Aksi (Hasil Review, Edit Form, Hapus khusus Admin/CEO) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Tombol Hasil Review / Keputusan Reviewer -->
                                            <button type="button" 
                                                    onclick="bukaModalHasilReview(<?= $row['kredit_id'] ?>)" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 hover:bg-purple-100 hover:text-purple-800 border border-purple-200/70 text-xs font-bold rounded-lg transition shadow-2xs cursor-pointer" 
                                                    title="Lihat Keputusan & Catatan Reviewer">
                                                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Hasil Review</span>
                                            </button>

                                            <!-- Tombol Edit Form -->
                                            <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>" 
                                               class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-lg transition border border-blue-200/50" 
                                               title="Buka / Edit Analisa">
                                                Edit Form
                                            </a>

                                            <?php 
                                                $roleUser = strtolower(session()->get('jabatan') ?? '');
                                                $currentUserId = (int)session()->get('user_id');
                                                $isExecutiveOrAdmin = ($roleUser === 'superadmin' || strpos($roleUser, 'ceo') !== false || strpos($roleUser, 'direk') !== false);
                                                $isPengusulOwner = (strpos($roleUser, 'pengusul') !== false || strpos($roleUser, 'ao') !== false) && (!empty($row['user_id']) ? (int)$row['user_id'] === $currentUserId : true);
                                                $canDeleteRow = $isExecutiveOrAdmin || $isPengusulOwner;
                                            ?>
                                            <?php if ($canDeleteRow): ?>
                                            <button type="button" 
                                                    onclick="hapusPengajuan(<?= $row['kredit_id'] ?>, '<?= esc($row['nomor_aplikasi']) ?>')"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" 
                                                    title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?>. Keamanan data terenkripsi & tersimpan permanen di database.
        </div>
    </footer>

    <script>
        // Logika Filter Terpadu: Rentang Waktu & Pencarian Kata Kunci
        function filterTabelPengajuan() {
            const rentang = document.getElementById('filterRentangWaktu')?.value || 'semua';
            const customBox = document.getElementById('customDateRangeBox');
            
            if (rentang === 'custom') {
                customBox?.classList.remove('hidden');
            } else {
                customBox?.classList.add('hidden');
            }

            const now = new Date();
            const padZero = (n) => String(n).padStart(2, '0');
            const todayStr = `${now.getFullYear()}-${padZero(now.getMonth() + 1)}-${padZero(now.getDate())}`;

            let startDate = null;
            let endDate = null;

            if (rentang === 'hari_ini') {
                startDate = todayStr;
                endDate = todayStr;
            } else if (rentang === '7_hari') {
                const d = new Date();
                d.setDate(d.getDate() - 7);
                startDate = `${d.getFullYear()}-${padZero(d.getMonth() + 1)}-${padZero(d.getDate())}`;
                endDate = todayStr;
            } else if (rentang === '30_hari') {
                const d = new Date();
                d.setDate(d.getDate() - 30);
                startDate = `${d.getFullYear()}-${padZero(d.getMonth() + 1)}-${padZero(d.getDate())}`;
                endDate = todayStr;
            } else if (rentang === 'bulan_ini') {
                const y = now.getFullYear();
                const m = padZero(now.getMonth() + 1);
                startDate = `${y}-${m}-01`;
                const lastDay = new Date(y, now.getMonth() + 1, 0).getDate();
                endDate = `${y}-${m}-${padZero(lastDay)}`;
            } else if (rentang === 'bulan_lalu') {
                const prev = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                const y = prev.getFullYear();
                const m = padZero(prev.getMonth() + 1);
                startDate = `${y}-${m}-01`;
                const lastDay = new Date(y, prev.getMonth() + 1, 0).getDate();
                endDate = `${y}-${m}-${padZero(lastDay)}`;
            } else if (rentang === 'tahun_ini') {
                const y = now.getFullYear();
                startDate = `${y}-01-01`;
                endDate = `${y}-12-31`;
            } else if (rentang === 'custom') {
                const tglM = document.getElementById('tglMulai')?.value;
                const tglS = document.getElementById('tglSelesai')?.value;
                if (tglM) startDate = tglM;
                if (tglS) endDate = tglS;
            }

            const searchInput = document.getElementById('searchInput');
            const term = (searchInput?.value || '').toLowerCase().trim();
            const rows = document.querySelectorAll('#tabelPengajuan tbody tr[data-tanggal]');
            let visibleCount = 0;

            rows.forEach(row => {
                const rowDate = row.getAttribute('data-tanggal');
                const rowText = row.innerText.toLowerCase();

                let matchDate = true;
                if (startDate && rowDate < startDate) matchDate = false;
                if (endDate && rowDate > endDate) matchDate = false;

                let matchSearch = true;
                if (term && !rowText.includes(term)) matchSearch = false;

                if (matchDate && matchSearch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const countDisplay = document.getElementById('countDisplay');
            if (countDisplay) {
                countDisplay.innerText = visibleCount;
            }
        }

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', filterTabelPengajuan);
        }
    </script>

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
                        <h3 class="text-sm font-extrabold text-slate-900" id="aiModalJudul">Laporan Analisa Kelayakan Kredit & Rekomendasi Komite (AI)</h3>
                        <p class="text-[11px] text-slate-500">Format Dokumen Hasil Evaluasi Sesuai Standar Komite</p>
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

            <!-- Modal Body: Iframe Halaman Laporan CAM Sesuai Screenshot -->
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
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                <a href="#" id="revModalBtnPdf" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak CAM PDF</span>
                </a>
                <button type="button" onclick="tutupModalHasilReview()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Data pengajuan yang dikirim dari controller untuk modal cepat
        const pengajuanDataStore = <?= json_encode($daftar_kredit ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        // Modal Hasil AI (Menampilkan Dokumen Laporan CAM Sesuai Screenshot)
        function bukaModalHasilAI(kreditId) {
            const item = (pengajuanDataStore || []).find(x => x.kredit_id == kreditId);
            const judul = item ? `Laporan Analisa Kelayakan Kredit & Rekomendasi Komite (AI) - ${item.nomor_aplikasi || ''}` : 'Laporan Analisa Kelayakan Kredit & Rekomendasi Komite (AI)';
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
            document.getElementById('revModalBtnPdf').href = '<?= base_url('pengajuan/cetak/') ?>/' + item.kredit_id;

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
                panduanDesc.innerText = 'Pengajuan telah lolos evaluasi. Anda dapat mencetak lembar CAM PDF resmi dan mempersiapkan proses akad serta pencairan kredit.';
            } else if (st.includes('tolak')) {
                banner.className = 'p-4 rounded-2xl border text-center flex flex-col items-center justify-center space-y-1.5 bg-rose-50 text-rose-900 border-rose-300 ring-1 ring-rose-200/50';
                badge.innerText = 'DITOLAK';
                sub.innerText = 'Pengajuan tidak disetujui oleh Reviewer / Komite Kredit';
                panduanBox.className = 'p-3.5 rounded-2xl border text-xs bg-rose-50/60 border-rose-200 text-rose-900';
                panduanTitle.innerText = 'Tindak Lanjut Pengusul:';
                panduanDesc.innerText = 'Pengajuan ditolak berdasarkan mitigasi risiko di atas. Sampaikan penjelasan resmi kepada nasabah secara profesional.';
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
                    if (tr) tr.style.opacity = '0.5';

                    fetch(`<?= base_url('pengajuan/hapus') ?>/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (tr) {
                                tr.style.transition = 'all 0.3s ease';
                                tr.style.opacity = '0';
                                tr.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    tr.remove();
                                    const tbody = document.querySelector('#tabelPengajuan tbody') || document.querySelector('table tbody');
                                    if (tbody && tbody.querySelectorAll('tr[id^="row-kredit-"]').length === 0) {
                                        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-12 text-slate-400"><p class="font-medium text-slate-600">Belum ada pengajuan kredit yang diinput.</p></td></tr>`;
                                    }
                                }, 300);
                            }
                            Swal.fire({
                                title: 'Berhasil Dihapus',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            if (tr) tr.style.opacity = '1';
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    })
                    .catch(err => {
                        if (tr) tr.style.opacity = '1';
                        Swal.fire('Error!', 'Koneksi terputus atau terjadi kesalahan.', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>
