<?php
/**
 * @var string $title
 * @var string $nama_lengkap
 * @var string $jabatan
 * @var int $total_pengajuan
 * @var float $total_plafon
 * @var int $total_pending
 * @var int $total_approved
 * @var int $total_rejected
 * @var int $total_users
 * @var int $users_pending
 * @var array $antrean_pengajuan
 * @var array $daftar_users
 * @var array $recent_logs
 * @var array $pengaturan_sistem
 * @var array $pengaturan_ai
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Executive Command Dashboard') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Include Sidebar Navbar dari app/Views/navbar.php -->
    <?= $this->include('navbar', [
        'active_menu'  => 'dashboard',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full Width dengan offset sidebar md:pl-64) -->
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
                    <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                        <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" class="w-6 h-6 object-contain rounded" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-sm"><?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Kredit') ?></span>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                Logout
            </a>
        </header>

        <!-- Desktop Topbar Breadcrumb (Full Width) -->
        <div class="bg-white border-b border-slate-200/80 px-6 lg:px-8 py-3.5 hidden md:flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Super Administrator</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Executive Dashboard</span>
            </div>
            <div class="flex items-center gap-2.5 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Engine Online
                </span>
                <span class="text-slate-400 font-mono-code text-[11px]"><?= date('d M Y') ?></span>
            </div>
        </div>

        <!-- MAIN CONTENT (RINGKAS & FULL WIDTH) -->
        <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1 space-y-6">
            
            <!-- Flash Alert -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <?= session()->getFlashdata('success') ?>
                    </span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
                </div>
            <?php endif; ?>

            <!-- EXECUTIVE BANNER (WARNA BIRU ELEGAN) -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-2xl p-5 sm:p-6 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider border border-white/25">
                            Pusat Komando Eksekutif
                        </span>
                        <span class="text-blue-200 text-xs">&bull;</span>
                        <span class="text-xs text-blue-100 font-medium"><?= esc($pengaturan_sistem['nama_lembaga'] ?? 'Lembaga Pembiayaan') ?></span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-extrabold tracking-tight text-white">
                        Selamat Datang, <?= esc($nama_lengkap ?? 'Super Administrator') ?>
                    </h1>
                    <p class="text-xs text-blue-100/90">
                        Pantau ringkasan portofolio pembiayaan, keputusan sidang komite, dan audit sistem secara real-time.
                    </p>
                </div>

                <?php if (($users_pending ?? 0) > 0): ?>
                    <div class="shrink-0">
                        <a href="<?= base_url('superadmin/manajemen_user') ?>" class="inline-flex items-center gap-2 px-3.5 py-2 bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs rounded-xl shadow-sm transition">
                            <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                            <span>Persetujuan Pengusul Baru (<?= $users_pending ?>) &rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 5 KPI STATISTIK UTAMA (PORTOFOLIO & SISTEM) -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
                
                <!-- 1. Total Portofolio -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pengajuan</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($total_pengajuan ?? 0) ?></div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Seluruh berkas kredit</span>
                </div>

                <!-- 2. Total Plafon -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Total Plafon</span>
                    <div class="text-xl sm:text-2xl font-extrabold text-blue-600 mt-1 truncate" title="Rp <?= number_format($total_plafon ?? 0, 0, ',', '.') ?>">
                        Rp <?= number_format($total_plafon ?? 0, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Akumulasi pengajuan</span>
                </div>

                <!-- 3. Menunggu Komite -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Menunggu persetujuan</span>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1"><?= number_format($total_pending ?? 0) ?></div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Draft / Sedang Diajukan</span>
                </div>

                <!-- 4. Disetujui Komite -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs">
                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Disetujui Komite</span>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-1"><?= number_format($total_approved ?? 0) ?></div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Lolos persetujuan komite</span>
                </div>

                <!-- 5. Pengguna Sistem -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs col-span-2 lg:col-span-1">
                    <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block">Pengguna Sistem</span>
                    <div class="text-2xl font-extrabold text-indigo-700 mt-1"><?= number_format($total_users ?? 0) ?></div>
                    <span class="text-[11px] text-slate-500 mt-0.5 block"><?= (int)($users_pending ?? 0) ?> menunggu verifikasi</span>
                </div>
            </div>

            <!-- DAFTAR TABEL DASHBOARD PER BARIS (FULL WIDTH) -->
            <div class="space-y-6">

                <!-- KOLOM KIRI: PENGAJUAN KREDIT TERBARU -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                📑
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Pengajuan Kredit Terbaru</h2>
                                <p class="text-[11px] text-slate-400">Antrean permohonan kredit terkini dari seluruh pengusul</p>
                            </div>
                        </div>
                        <a href="<?= base_url('superadmin/simulasi') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">No. Aplikasi</th>
                                    <th class="py-3 px-4">Debitur</th>
                                    <th class="py-3 px-4">Plafon</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($antrean_pengajuan)): ?>
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic text-[11px]">
                                            Belum ada pengajuan kredit terdaftar.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach (array_slice($antrean_pengajuan, 0, 5) as $row): ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap font-mono-code text-[11px]">
                                                <?= esc($row['nomor_aplikasi']) ?>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <div class="font-bold text-slate-800"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                                <div class="text-[10px] text-slate-400"><?= esc($row['nama_usaha'] ?: 'Individu') ?></div>
                                            </td>
                                            <td class="py-3 px-4 font-bold text-blue-600 whitespace-nowrap">
                                                Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <?php
                                                    $st = strtolower($row['status_pengajuan'] ?? 'draft');
                                                    $badge = 'bg-slate-100 text-slate-700 border-slate-200';
                                                    if ($st === 'disetujui') $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                    elseif ($st === 'diajukan') $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                                                    elseif ($st === 'ditolak') $badge = 'bg-rose-50 text-rose-700 border-rose-200';
                                                ?>
                                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $badge ?>">
                                                    <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                                <a href="<?= base_url('superadmin/simulasi') ?>" class="px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- KOLOM KANAN: AKTIVITAS SISTEM TERKINI (LOG AUDIT) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                                📜
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Log Aktivitas Sistem Terkini</h2>
                                <p class="text-[11px] text-slate-400">Riwayat audit kejadian dan operasi engine terbaru</p>
                            </div>
                        </div>
                        <a href="<?= base_url('superadmin/log') ?>" class="text-xs font-bold text-purple-600 hover:text-purple-800 hover:underline">
                            Buka Log &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-4">Waktu</th>
                                    <th class="py-3 px-4">Pengguna</th>
                                    <th class="py-3 px-4">Modul</th>
                                    <th class="py-3 px-4">Aktivitas</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (empty($recent_logs)): ?>
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 italic text-[11px]">
                                            Belum ada log aktivitas tercatat.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_logs as $lg): ?>
                                        <?php
                                            $stUpper = strtoupper($lg['status'] ?? 'SUCCESS');
                                            $stClass = 'bg-emerald-50 text-emerald-700';
                                            if ($stUpper === 'WARNING') $stClass = 'bg-amber-50 text-amber-700';
                                            elseif ($stUpper === 'DANGER' || $stUpper === 'ERROR') $stClass = 'bg-rose-50 text-rose-700';
                                            elseif ($stUpper === 'INFO') $stClass = 'bg-blue-50 text-blue-700';
                                        ?>
                                        <tr class="hover:bg-slate-50/70 transition-colors">
                                            <td class="py-3 px-4 font-mono-code text-[10px] text-slate-500 whitespace-nowrap">
                                                <?= date('H:i:s', strtotime($lg['created_at'])) ?>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap font-bold text-slate-800 text-[11px]">
                                                <?= esc($lg['nama_user'] ?? 'System') ?>
                                            </td>
                                            <td class="py-3 px-4 whitespace-nowrap">
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">
                                                    <?= esc($lg['modul'] ?? 'SISTEM') ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-[11px] text-slate-700" title="<?= esc($lg['aktivitas']) ?>">
                                                <?= esc($lg['aktivitas']) ?>
                                            </td>
                                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                                <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold <?= $stClass ?>">
                                                    <?= esc($lg['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>

    </div>

</body>
</html>
