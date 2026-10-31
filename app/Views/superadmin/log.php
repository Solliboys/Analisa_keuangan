<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Log Aktivitas Sistem | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- SIDEBAR NAVIGASI DINAMIS -->
    <?= $this->include('navbar') ?>

    <!-- MAIN CONTENT CONTAINER -->
    <main class="ml-0 md:ml-64 min-h-screen flex flex-col transition-all duration-300">
        
        <!-- TOPBAR DESKTOP DENGAN BREADCRUMB -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Hamburger Mobile Toggle -->
                <button type="button" onclick="toggleSidebar()" class="md:hidden p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Breadcrumb Pill -->
                <div class="flex items-center gap-2 text-xs">
                    <span class="font-bold text-slate-400 tracking-wider uppercase text-[10px] hidden sm:inline"><?= esc(strtoupper($jabatan ?? 'Super Administrator')) ?></span>
                    <span class="text-slate-300 hidden sm:inline">/</span>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-700 font-bold text-[11px]">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Log Aktivitas Sistem</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Audit Engine Online
                </span>
            </div>
        </header>

        <!-- CONTENT AREA (FULL WIDTH) -->
        <div class="p-4 sm:p-6 lg:p-8 flex-1 space-y-6 w-full">

            <!-- HEADER SECTION -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shadow-md shadow-blue-500/20">
                            📜
                        </span>
                        Log Aktivitas &amp; Audit Trail Sistem
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pencatatan riwayat operasi, inferensi AI, perubahan parameter, dan keamanan sistem. Hak akses bersifat <b>Audit-Only</b> (Download &amp; Lihat).
                    </p>
                </div>

                <!-- TOMBOL DOWNLOAD LOG (CSV / EXCEL) -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="<?= base_url('superadmin/log/download') . (!empty($_SERVER['QUERY_STRING']) ? '?' . esc($_SERVER['QUERY_STRING']) : '') ?>" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm hover:shadow transition transform active:scale-95 cursor-pointer"
                       title="Download berkas CSV / Excel dari riwayat aktivitas">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Download Log (CSV / Excel)</span>
                    </a>
                </div>
            </div>

            <!-- KPI STATISTIK AUDIT TRAIL -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                <!-- Card 1: Total Log -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Aktivitas</span>
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">📋</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                        <?= number_format($total_logs ?? count($logs ?? []), 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 mt-1 block">Seluruh riwayat audit</span>
                </div>

                <!-- Card 2: Aktivitas Hari Ini -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Hari Ini</span>
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">📅</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-2">
                        <?= number_format($today_logs ?? 0, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 mt-1 block">Aktivitas <?= date('d M Y') ?></span>
                </div>

                <!-- Card 3: Status Sukses -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Operasi Berhasil</span>
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold">✅</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600 mt-2">
                        <?= number_format($success_logs ?? 0, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 mt-1 block">Integritas 100% normal</span>
                </div>

                <!-- Card 4: Notifikasi / Peringatan -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Peringatan / Alert</span>
                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">⚠️</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-2">
                        <?= number_format($warning_logs ?? 0, 0, ',', '.') ?>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 mt-1 block">Perhatian &amp; Security Warning</span>
                </div>
            </div>

            <!-- FILTER & SEARCH BAR -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-2xs">
                <form method="GET" action="<?= base_url('superadmin/log') ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-2 relative">
                        <input type="text" 
                               name="q" 
                               value="<?= esc($filter_q ?? '') ?>" 
                               placeholder="Cari aktivitas, nama pengguna, alamat IP..." 
                               class="w-full text-xs font-semibold pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <!-- Filter Modul -->
                    <div>
                        <select name="modul" onchange="this.form.submit()" class="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Semua Modul Sistem</option>
                            <?php foreach ($daftar_modul as $m): ?>
                                <option value="<?= esc($m) ?>" <?= (($filter_modul ?? '') === $m) ? 'selected' : '' ?>>
                                    Modul: <?= esc($m) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Status & Action Buttons -->
                    <div class="flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()" class="flex-1 text-xs font-semibold px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="SUCCESS" <?= (($filter_status ?? '') === 'SUCCESS') ? 'selected' : '' ?>>SUCCESS</option>
                            <option value="INFO" <?= (($filter_status ?? '') === 'INFO') ? 'selected' : '' ?>>INFO</option>
                            <option value="WARNING" <?= (($filter_status ?? '') === 'WARNING') ? 'selected' : '' ?>>WARNING</option>
                            <option value="DANGER" <?= (($filter_status ?? '') === 'DANGER') ? 'selected' : '' ?>>DANGER</option>
                        </select>

                        <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition cursor-pointer shadow-2xs">
                            Cari
                        </button>

                        <?php if (!empty($filter_q) || !empty($filter_modul) || !empty($filter_status)): ?>
                            <a href="<?= base_url('superadmin/log') ?>" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition cursor-pointer" title="Reset Filter">
                                ✕
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- TABEL AKTIVITAS LOG SISTEM -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                            Daftar Catatan Audit Trail (<?= count($logs ?? []) ?> Catatan Ditampilkan)
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tabel bersifat permanen &amp; hanya baca untuk keamanan audit perbankan</p>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                        Aksi: Download &amp; Lihat
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                                <th class="py-3.5 px-4">Waktu (WIB)</th>
                                <th class="py-3.5 px-4">Pengguna</th>
                                <th class="py-3.5 px-4">Modul</th>
                                <th class="py-3.5 px-4">Aktivitas Sistem</th>
                                <th class="py-3.5 px-4">Alamat IP</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                            📄
                                        </div>
                                        <p class="font-bold text-slate-600 text-sm">Belum Ada Data Log Aktivitas</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Tidak ditemukan log aktivitas yang sesuai dengan kriteria filter.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <?php
                                        // Modul Badge Color
                                        $modulUpper = strtoupper($log['modul'] ?? 'SISTEM');
                                        $modulBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                                        if (str_contains($modulUpper, 'AI')) {
                                            $modulBadge = 'bg-purple-50 text-purple-700 border-purple-200';
                                        } elseif (str_contains($modulUpper, 'AUTH')) {
                                            $modulBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } elseif (str_contains($modulUpper, 'KREDIT')) {
                                            $modulBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        } elseif (str_contains($modulUpper, 'PENGATURAN')) {
                                            $modulBadge = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                        } elseif (str_contains($modulUpper, 'GATEWAY')) {
                                            $modulBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                                        }

                                        // Status Badge Color
                                        $stUpper = strtoupper($log['status'] ?? 'SUCCESS');
                                        $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        if ($stUpper === 'WARNING') {
                                            $statusBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                                        } elseif ($stUpper === 'DANGER' || $stUpper === 'ERROR') {
                                            $statusBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                                        } elseif ($stUpper === 'INFO') {
                                            $statusBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                                        }
                                    ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        
                                        <!-- Waktu -->
                                        <td class="py-3.5 px-4 font-mono-code text-[11px] text-slate-600 whitespace-nowrap">
                                            <span class="font-bold text-slate-800 block">
                                                <?= date('d M Y', strtotime($log['created_at'])) ?>
                                            </span>
                                            <span class="text-slate-400">
                                                <?= date('H:i:s', strtotime($log['created_at'])) ?> WIB
                                            </span>
                                        </td>

                                        <!-- Pengguna -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                                                    <?= strtoupper(substr($log['nama_user'] ?? 'U', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="font-bold text-slate-800 block">
                                                        <?= esc($log['nama_user'] ?? 'System') ?>
                                                    </span>
                                                    <span class="text-[10px] text-slate-400">
                                                        <?= esc($log['role'] ?? 'Superadmin') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Modul -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border <?= $modulBadge ?>">
                                                <?= esc($log['modul']) ?>
                                            </span>
                                        </td>

                                        <!-- Aktivitas -->
                                        <td class="py-3.5 px-4 min-w-[200px]">
                                            <span class="font-semibold text-slate-800 block leading-tight">
                                                <?= esc($log['aktivitas']) ?>
                                            </span>
                                        </td>

                                        <!-- IP Address -->
                                        <td class="py-3.5 px-4 font-mono-code text-[11px] text-slate-500 whitespace-nowrap">
                                            <?= esc($log['ip_address'] ?? '127.0.0.1') ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border <?= $statusBadge ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= ($stUpper === 'SUCCESS' ? 'bg-emerald-500' : ($stUpper === 'WARNING' ? 'bg-amber-500' : ($stUpper === 'INFO' ? 'bg-blue-500' : 'bg-rose-500'))) ?>"></span>
                                                <?= esc($log['status']) ?>
                                            </span>
                                        </td>

                                        <!-- AKSI (HANYA LIHAT DETAIL) -->
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <button type="button" 
                                                    onclick="bukaModalDetailLog(<?= htmlspecialchars(json_encode($log), ENT_QUOTES, 'UTF-8') ?>)" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition border border-blue-200 cursor-pointer shadow-2xs"
                                                    title="Lihat detail audit trail dan payload log">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Lihat</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Informasi Audit -->
                <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Audit Log Immutable &bull; Tidak dapat dimanipulasi atau dihapus secara manual.</span>
                    </div>
                    <div class="font-mono-code text-[11px] text-slate-400">
                        Waktu Server: <?= date('Y-m-d H:i:s') ?> WIB
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL DETAIL AUDIT LOG (LIHAT DATA) -->
    <!-- MODAL DETAIL AUDIT LOG (TABEL RIWAYAT AKTIVITAS & PARAMETER) -->
    <div id="modalDetailLog" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl max-w-3xl w-full border border-slate-200 shadow-2xl overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200 my-6">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/90">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        📊
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 leading-tight">
                            Tabel Riwayat &amp; Rincian Aktivitas
                        </h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span id="detailLogId" class="text-[11px] font-mono-code text-slate-400">ID Log: #0</span>
                            <span class="text-slate-300">&bull;</span>
                            <span id="detailLogStatusBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span id="detailLogStatusText">SUCCESS</span>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="tutupModalDetailLog()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-200/60 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 sm:p-6 space-y-5 text-xs max-h-[75vh] overflow-y-auto">
                
                <!-- Ringkasan Metadata Kejadian -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 block font-medium text-[11px]">Waktu Kejadian (WIB)</span>
                        <span id="detailLogWaktu" class="font-bold text-slate-800 font-mono-code mt-0.5 block">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium text-[11px]">Pengguna &amp; Peran</span>
                        <span id="detailLogUser" class="font-bold text-slate-800 mt-0.5 block truncate">-</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-medium text-[11px]">Modul &amp; IP Klien</span>
                        <span id="detailLogModulIp" class="font-bold text-slate-800 mt-0.5 block font-mono-code">-</span>
                    </div>
                    <div class="sm:col-span-3 pt-2 border-t border-slate-200/60">
                        <span class="text-slate-400 block font-medium text-[11px]">Aktivitas Utama Sistem</span>
                        <span id="detailLogAktivitas" class="font-bold text-slate-900 text-sm mt-0.5 block leading-snug">-</span>
                    </div>
                </div>

                <!-- TABEL RINCIAN PARAMETER & PERUBAHAN AKTIVITAS -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Tabel Rincian Parameter Aktivitas
                            </h4>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Format tabel audit terstruktur</span>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-3.5 w-12 text-center">No</th>
                                    <th class="py-2.5 px-4 w-1/3">Parameter / Bagian</th>
                                    <th class="py-2.5 px-4">Nilai / Rincian Riwayat</th>
                                </tr>
                            </thead>
                            <tbody id="detailLogTableBody" class="divide-y divide-slate-100">
                                <!-- Baris tabel di-generate via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABEL RIWAYAT AKTIVITAS LAIN PENGGUNA TERKAIT -->
                <div id="sectionRiwayatTerkait" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Riwayat Aktivitas Terkait Lainnya
                            </h4>
                        </div>
                        <span id="labelRiwayatUser" class="text-[11px] text-slate-500 font-medium">
                            Histori log akun ini
                        </span>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs bg-white">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100/80 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2 px-3 text-center w-10">#</th>
                                    <th class="py-2 px-3">Waktu</th>
                                    <th class="py-2 px-3">Modul</th>
                                    <th class="py-2 px-3">Aktivitas</th>
                                    <th class="py-2 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableRiwayatTerkaitBody" class="divide-y divide-slate-100 text-[11px]">
                                <!-- Riwayat aktivitas lain user ini di-render JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Informasi Browser / User Agent -->
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 flex items-start gap-2.5">
                    <span class="text-base leading-none">💻</span>
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Perangkat / Browser User Agent</span>
                        <span id="detailLogUserAgent" class="text-[11px] text-slate-600 font-mono-code block break-all mt-0.5 select-all">-</span>
                    </div>
                </div>

                <!-- Accordion / Toggle Raw JSON (Opsional jika dibutuhkan teknisi) -->
                <div class="pt-1">
                    <button type="button" 
                            onclick="toggleRawJson()" 
                            class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <span>Tampilkan Format Raw JSON (Opsional Teknis)</span>
                    </button>
                    
                    <div id="rawJsonContainer" class="hidden mt-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] text-slate-400">Salin payload mentah:</span>
                            <button type="button" onclick="copyDetailJson()" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold flex items-center gap-1 cursor-pointer">
                                <span>Salin JSON</span>
                            </button>
                        </div>
                        <pre id="detailLogPayload" class="p-3 bg-slate-900 text-slate-100 rounded-xl font-mono-code text-[11px] overflow-x-auto max-h-40 leading-relaxed select-all">{}</pre>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Audit Trail Resmi Sistem
                </span>
                <button type="button" onclick="tutupModalDetailLog()" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Simpan referensi seluruh logs dari server untuk pencarian riwayat terkait
        const allSystemLogs = <?= json_encode($logs ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        function formatParameterName(key) {
            return key
                .replace(/_/g, ' ')
                .replace(/\b\w/g, char => char.toUpperCase());
        }

        function renderParameterValue(value) {
            if (value === null || value === undefined) {
                return '<span class="text-slate-400 italic">-</span>';
            }
            if (typeof value === 'boolean') {
                return value 
                    ? '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Ya / True</span>'
                    : '<span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Tidak / False</span>';
            }
            if (Array.isArray(value)) {
                if (value.length === 0) return '<span class="text-slate-400 italic">Kosong</span>';
                return `<div class="flex flex-wrap gap-1.5">` + 
                    value.map(item => `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80">${escapeHtml(String(item))}</span>`).join('') + 
                    `</div>`;
            }
            if (typeof value === 'object') {
                return `<div class="p-2 bg-slate-50 rounded-lg border border-slate-200 font-mono-code text-[11px]">${escapeHtml(JSON.stringify(value, null, 2))}</div>`;
            }
            return `<span class="font-semibold text-slate-800">${escapeHtml(String(value))}</span>`;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function bukaModalDetailLog(data) {
            if (!data) return;

            document.getElementById('detailLogId').textContent = 'ID Log: #' + (data.id || '-');
            document.getElementById('detailLogWaktu').textContent = (data.created_at || '-') + ' WIB';
            document.getElementById('detailLogUser').textContent = (data.nama_user || 'System') + ' (' + (data.role || 'Superadmin') + ')';
            document.getElementById('detailLogModulIp').textContent = (data.modul || 'SISTEM') + ' &bull; ' + (data.ip_address || '127.0.0.1');
            document.getElementById('detailLogAktivitas').textContent = data.aktivitas || '-';
            document.getElementById('detailLogUserAgent').textContent = data.user_agent || '-';

            // Status Badge
            const statusText = data.status || 'SUCCESS';
            const badgeEl = document.getElementById('detailLogStatusBadge');
            const textEl = document.getElementById('detailLogStatusText');
            textEl.textContent = statusText;

            if (statusText === 'WARNING') {
                badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
            } else if (statusText === 'DANGER' || statusText === 'ERROR') {
                badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200';
            } else if (statusText === 'INFO') {
                badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200';
            } else {
                badgeEl.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
            }

            // Render TABEL PARAMETER / DATA DETAIL
            const tableBody = document.getElementById('detailLogTableBody');
            tableBody.innerHTML = '';

            let rawJsonText = data.detail || '{}';
            let parsedObj = null;
            try {
                parsedObj = JSON.parse(data.detail);
            } catch (e) {
                parsedObj = null;
            }

            if (parsedObj && typeof parsedObj === 'object' && !Array.isArray(parsedObj) && Object.keys(parsedObj).length > 0) {
                let no = 1;
                for (const [key, val] of Object.entries(parsedObj)) {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-slate-50/70 transition-colors';
                    row.innerHTML = `
                        <td class="py-2.5 px-3.5 text-center font-mono-code text-slate-400 text-[11px] font-semibold">${no++}</td>
                        <td class="py-2.5 px-4 font-bold text-slate-700 text-xs">${formatParameterName(key)}</td>
                        <td class="py-2.5 px-4 text-xs">${renderParameterValue(val)}</td>
                    `;
                    tableBody.appendChild(row);
                }
            } else if (data.detail && data.detail.trim() !== '') {
                tableBody.innerHTML = `
                    <tr class="hover:bg-slate-50/70">
                        <td class="py-2.5 px-3.5 text-center font-mono-code text-slate-400 text-[11px]">1</td>
                        <td class="py-2.5 px-4 font-bold text-slate-700 text-xs">Catatan / Detail</td>
                        <td class="py-2.5 px-4 text-xs font-semibold text-slate-800">${escapeHtml(data.detail)}</td>
                    </tr>
                `;
            } else {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="py-4 text-center text-slate-400 italic text-[11px]">
                            Tidak ada parameter data tambahan yang dicatat untuk aktivitas ini.
                        </td>
                    </tr>
                `;
            }

            // Raw JSON Container update
            try {
                document.getElementById('detailLogPayload').textContent = JSON.stringify(parsedObj || data.detail || {}, null, 2);
            } catch(e) {
                document.getElementById('detailLogPayload').textContent = data.detail || '{}';
            }
            document.getElementById('rawJsonContainer').classList.add('hidden');

            // Render TABEL RIWAYAT AKTIVITAS TERKAIT USER INI
            const riwayatBody = document.getElementById('tableRiwayatTerkaitBody');
            riwayatBody.innerHTML = '';
            document.getElementById('labelRiwayatUser').textContent = 'Histori aktivitas ' + (data.nama_user || 'Pengguna');

            const relatedLogs = allSystemLogs.filter(l => l.id != data.id && (l.user_id == data.user_id || l.nama_user == data.nama_user)).slice(0, 5);

            if (relatedLogs.length > 0) {
                document.getElementById('sectionRiwayatTerkait').classList.remove('hidden');
                relatedLogs.forEach((rl, idx) => {
                    const rRow = document.createElement('tr');
                    rRow.className = 'hover:bg-slate-50 transition-colors';
                    const stColor = rl.status === 'WARNING' ? 'text-amber-600 bg-amber-50' : (rl.status === 'DANGER' ? 'text-rose-600 bg-rose-50' : 'text-emerald-600 bg-emerald-50');
                    rRow.innerHTML = `
                        <td class="py-2 px-3 text-center text-slate-400 font-mono-code text-[10px]">${idx + 1}</td>
                        <td class="py-2 px-3 font-mono-code text-slate-600 text-[10px] whitespace-nowrap">${escapeHtml(rl.created_at)}</td>
                        <td class="py-2 px-3 font-bold text-slate-700 text-[10px] whitespace-nowrap">${escapeHtml(rl.modul)}</td>
                        <td class="py-2 px-3 font-semibold text-slate-800 text-[11px] truncate max-w-[200px]" title="${escapeHtml(rl.aktivitas)}">${escapeHtml(rl.aktivitas)}</td>
                        <td class="py-2 px-3 text-center whitespace-nowrap">
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold ${stColor}">${escapeHtml(rl.status)}</span>
                        </td>
                    `;
                    riwayatBody.appendChild(rRow);
                });
            } else {
                riwayatBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="py-3 text-center text-slate-400 italic text-[11px]">
                            Belum ada riwayat aktivitas lain yang tercatat untuk pengguna ini pada sesi ini.
                        </td>
                    </tr>
                `;
            }

            const modal = document.getElementById('modalDetailLog');
            modal.classList.remove('hidden');
        }

        function tutupModalDetailLog() {
            const modal = document.getElementById('modalDetailLog');
            modal.classList.add('hidden');
        }

        function toggleRawJson() {
            const container = document.getElementById('rawJsonContainer');
            container.classList.toggle('hidden');
        }

        function copyDetailJson() {
            const text = document.getElementById('detailLogPayload').textContent;
            navigator.clipboard.writeText(text).then(() => {
                alert('Payload JSON berhasil disalin ke clipboard!');
            }).catch(() => {
                alert('Gagal menyalin. Silakan pilih teks secara manual.');
            });
        }

        // Tutup modal jika klik di luar box
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('modalDetailLog');
            if (e.target === modal) {
                tutupModalDetailLog();
            }
        });
    </script>
</body>
</html>
