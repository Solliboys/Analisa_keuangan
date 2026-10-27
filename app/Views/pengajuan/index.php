<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> | OTW Credit Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                            <th class="py-3.5 px-4">Status</th>
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
                                <tr class="hover:bg-slate-50 transition" data-tanggal="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
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
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <?php
                                            $st = strtolower($row['status_pengajuan'] ?? 'draft');
                                            $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if ($st === 'disetujui') $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            elseif ($st === 'ditolak') $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                            elseif ($st === 'diajukan') $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $badgeClass ?>">
                                            <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>" 
                                               class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-lg transition" title="Buka / Edit Analisa">
                                                Edit Form
                                            </a>
                                            <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank"
                                               class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold rounded-lg transition" title="Cetak CAM PDF">
                                                PDF
                                            </a>
                                            <?php if ($canDelete): ?>
                                            <a href="<?= base_url('pengajuan/hapus/' . $row['kredit_id']) ?>" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan <?= esc($row['nomor_aplikasi']) ?>?')"
                                               class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </a>
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
