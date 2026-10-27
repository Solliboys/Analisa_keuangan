<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Log Aktivitas Sistem') ?> | <?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Credit Engine') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
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
    $jabatanUser   = session()->get('jabatan') ?? ($jabatan ?? 'User');
    $roleBadgeText = esc($jabatanUser);

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
    <!-- Top Navigation Bar (Sama seperti Tampilan Pengajuan) -->
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
                        <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
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
                            $avatarSrc = !empty($fotoProfil) ? base_url($fotoProfil) : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap ?? 'User') . '&background=7c3aed&color=fff&bold=true';
                        ?>
                        <button type="button" onclick="toggleProfileDropdown(event)" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-purple-400 focus:ring-2 focus:ring-purple-500 transition cursor-pointer">
                            <img src="<?= $avatarSrc ?>" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap ?? 'User') ?>&background=7c3aed&color=fff&bold=true';" 
                                 alt="Foto Profil" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenuProfil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 animate-in fade-in duration-150">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-bold text-slate-800 truncate"><?= esc($nama_lengkap) ?></p>
                                <p class="text-[10px] text-slate-400"><?= $roleBadgeText ?></p>
                            </div>
                            
                            <a href="<?= base_url('profil') ?>" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-purple-50 hover:text-purple-700 transition flex items-center gap-2.5">
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
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg shadow-2xs border border-purple-200">
                        📜
                    </span>
                    <span>Log Aktivitas Sistem & Audit Trail</span>
                </h1>
                <p class="text-sm text-slate-500 mt-1">Pemantauan riwayat jejak audit operasional, analisis risiko, dan aktivitas sistem secara real-time.</p>
            </div>

            <!-- Tombol Download & Dropdown Filter Rentang Waktu -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Tombol Download CSV/Excel -->
                <a href="<?= base_url('log/download') ?>" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-purple-700 font-bold text-xs rounded-xl shadow-xs transition" title="Download Ekspor Data Log (CSV/Excel)">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download</span>
                </a>

                <!-- Dropdown Filter Rentang Waktu (Persis seperti yang di pengajuan) -->
                <div class="relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-purple-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <select id="filterRentangWaktu" onchange="filterTabelLog()" 
                            class="w-full pl-10 pr-9 py-2.5 text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl appearance-none shadow-xs focus:ring-2 focus:ring-purple-100 focus:border-purple-500 transition outline-none cursor-pointer">
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
                    <input type="date" id="tglMulai" onchange="filterTabelLog()" class="text-xs font-semibold text-slate-700 outline-none border-none bg-transparent cursor-pointer" title="Tanggal Mulai">
                    <span class="text-xs text-slate-400 font-bold">&ndash;</span>
                    <input type="date" id="tglSelesai" onchange="filterTabelLog()" class="text-xs font-semibold text-slate-700 outline-none border-none bg-transparent cursor-pointer" title="Tanggal Selesai">
                </div>
            </div>
        </div>

        <!-- Summary Stats Cards (Format Mirip Pengajuan) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Log Tercatat</div>
                <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($total_logs ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Seluruh audit kejadian di sistem</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-purple-600 uppercase tracking-wider">Aktivitas Hari Ini</div>
                <div class="text-2xl font-extrabold text-purple-700 mt-1"><?= number_format($today_logs ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Kejadian audit pada hari ini</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Status Berhasil</div>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= number_format($success_logs ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Operasi sukses (SUCCESS &bull; INFO)</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">Peringatan & Isu</div>
                <div class="text-2xl font-extrabold text-amber-700 mt-1"><?= number_format($warning_logs ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Status WARNING / DANGER / ERROR</div>
            </div>
        </div>

        <!-- Table Card: Riwayat Log Aktivitas Komprehensif -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nama pengguna, modul, aktivitas, IP..." 
                           class="w-full pl-9 pr-3 py-2 text-xs font-medium border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-100 focus:border-purple-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span id="countDisplay" class="font-bold text-slate-800"><?= count($daftar_log ?? []) ?></span> aktivitas
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600" id="tabelLog">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">No</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Waktu Kejadian</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Pengguna &amp; IP</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">Modul</th>
                            <th class="py-3.5 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Status</th>
                            <th class="py-3.5 px-4 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-normal text-xs">
                        <?php if (empty($daftar_log)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-600">Belum ada data log aktivitas yang tercatat.</p>
                                    <p class="text-xs text-slate-400 mt-1">Setiap operasi pengguna dan sistem akan secara otomatis direkam di sini.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                                $no = 1;
                                foreach ($daftar_log as $row): 
                                    $stUpper = strtoupper($row['status'] ?? 'SUCCESS');
                                    $stBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                    if ($stUpper === 'WARNING') $stBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                                    elseif ($stUpper === 'DANGER' || $stUpper === 'ERROR') $stBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                                    elseif ($stUpper === 'INFO') $stBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                            ?>
                                <tr class="hover:bg-slate-50 transition" data-tanggal="<?= date('Y-m-d', strtotime($row['created_at'])) ?>">
                                    <!-- No -->
                                    <td class="py-3 px-4 text-center text-slate-400 font-medium">
                                        <?= $no++ ?>
                                    </td>

                                    <!-- Waktu -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-mono-code font-bold text-slate-800 text-[11px]">
                                            <?= date('d M Y', strtotime($row['created_at'])) ?>
                                        </div>
                                        <div class="font-mono-code text-[10px] text-slate-400">
                                            <?= date('H:i:s', strtotime($row['created_at'])) ?> WIB
                                        </div>
                                    </td>

                                    <!-- Pengguna & IP -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-900 text-xs"><?= esc($row['nama_user'] ?? 'System') ?></div>
                                        <div class="text-[10px] text-slate-400 font-mono-code flex items-center gap-1.5 mt-0.5">
                                            <span><?= esc($row['role'] ?? '-') ?></span>
                                            <?php if (!empty($row['ip_address'])): ?>
                                                <span class="text-slate-300">&bull;</span>
                                                <span><?= esc($row['ip_address']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <!-- Modul -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            <?= esc($row['modul'] ?? 'SISTEM') ?>
                                        </span>
                                    </td>

                                    <!-- Aktivitas -->
                                    <td class="py-3 px-4 text-slate-700 max-w-[360px]" title="<?= esc($row['aktivitas']) ?>">
                                        <div class="font-medium text-xs leading-relaxed">
                                            <?= esc($row['aktivitas']) ?>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $stBadge ?>">
                                            <?= esc($stUpper) ?>
                                        </span>
                                    </td>

                                    <!-- Aksi: Detail -->
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <button type="button" 
                                                onclick="bukaDetailLog(<?= (int)$row['id'] ?>)" 
                                                class="px-2.5 py-1 bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 text-xs font-bold rounded-lg transition" title="Lihat Detail Payload">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer (Sama dengan Tampilan Pengajuan) -->
    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?>. Audit log terenkripsi & tersimpan permanen di database.
        </div>
    </footer>

    <script>
        // Filter Terpadu: Rentang Waktu & Pencarian Kata Kunci
        function filterTabelLog() {
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
            const rows = document.querySelectorAll('#tabelLog tbody tr[data-tanggal]');
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
            searchInput.addEventListener('keyup', filterTabelLog);
        }

        // Buka Detail Modal Log
        function bukaDetailLog(id) {
            Swal.fire({
                title: 'Memuat Detail Log...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= base_url('log/detail') ?>/' + id, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.status || !res.data) {
                    Swal.fire('Gagal', res.message || 'Detail log tidak ditemukan.', 'error');
                    return;
                }

                const d = res.data;
                let payloadHtml = '<span class="text-slate-400 italic font-mono text-[11px]">Tidak ada payload tambahan.</span>';
                if (d.detail) {
                    try {
                        const parsed = JSON.parse(d.detail);
                        payloadHtml = `<pre class="bg-slate-900 text-slate-100 p-3 rounded-xl font-mono-code text-[11px] overflow-x-auto max-h-56">${JSON.stringify(parsed, null, 2)}</pre>`;
                    } catch (e) {
                        payloadHtml = `<div class="bg-slate-50 p-3 rounded-xl border border-slate-200 font-mono-code text-[11px] text-slate-700 whitespace-pre-wrap">${d.detail}</div>`;
                    }
                }

                Swal.fire({
                    title: `<div class="text-base font-bold text-slate-900 flex items-center justify-center gap-2">
                                <span>📜 Detail Audit Log #${d.id}</span>
                            </div>`,
                    html: `
                        <div class="text-left space-y-3 text-xs">
                            <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div><span class="text-slate-400 font-medium block text-[10px]">WAKTU:</span><strong class="text-slate-800">${d.created_at}</strong></div>
                                <div><span class="text-slate-400 font-medium block text-[10px]">PENGGUNA:</span><strong class="text-slate-800">${d.nama_user || 'System'}</strong> (${d.role || '-'})</div>
                                <div><span class="text-slate-400 font-medium block text-[10px]">MODUL:</span><strong class="text-purple-700">${d.modul || '-'}</strong></div>
                                <div><span class="text-slate-400 font-medium block text-[10px]">STATUS:</span><strong class="text-slate-800">${d.status || '-'}</strong></div>
                                <div><span class="text-slate-400 font-medium block text-[10px]">IP ADDRESS:</span><span class="font-mono text-slate-700">${d.ip_address || '-'}</span></div>
                                <div><span class="text-slate-400 font-medium block text-[10px]">PERANGKAT:</span><span class="text-slate-600 truncate block" title="${d.user_agent || '-'}">${d.user_agent ? d.user_agent.substring(0, 30) + '...' : '-'}</span></div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Aktivitas:</span>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-800 font-semibold leading-relaxed">
                                    ${d.aktivitas}
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Payload / Detail JSON:</span>
                                ${payloadHtml}
                            </div>
                        </div>
                    `,
                    width: '620px',
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#7c3aed'
                });
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memuat detail log dari server.', 'error');
            });
        }

        // Dropdown Profil
        function toggleProfileDropdown(e) {
            e.stopPropagation();
            const menu = document.getElementById('dropdownMenuProfil');
            if (menu) menu.classList.toggle('hidden');
        }

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
