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

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        CE
                    </div>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">Credit Engine & CAM</span>
                        <span class="text-xs block text-slate-500 font-medium">Sistem Analisa & Komite Pembiayaan</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-slate-900"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700">
                            <?= esc($jabatan) ?>
                        </span>
                    </div>
                    <a href="<?= base_url('dashboard') ?>" class="px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                        Dashboard
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="px-3 py-1.5 text-sm font-medium text-rose-600 hover:text-rose-700 border border-rose-200 bg-rose-50/50 rounded-lg hover:bg-rose-100 transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
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

        <!-- Action Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Pengajuan Kredit (CAM)</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data pembiayaan, evaluasi profil risiko, dan cetak credit memo.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?= base_url('pengajuan/baru') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Input Pengajuan Baru
                </a>
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
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Aplikasi</div>
                <div class="text-2xl font-bold text-slate-900 mt-1"><?= number_format($totalPengajuan) ?></div>
                <div class="text-xs text-slate-400 mt-1">Berkas terdaftar di sistem</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Plafon Diajukan</div>
                <div class="text-2xl font-bold text-indigo-600 mt-1">Rp <?= number_format($totalPlafon, 0, ',', '.') ?></div>
                <div class="text-xs text-slate-400 mt-1">Akumulasi seluruh aplikasi</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Disetujui Komite</div>
                <div class="text-2xl font-bold text-emerald-700 mt-1"><?= number_format($approvedCount) ?></div>
                <div class="text-xs text-slate-400 mt-1">Siap proses akad & pencairan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Draft / Dalam Analisa</div>
                <div class="text-2xl font-bold text-amber-700 mt-1"><?= number_format($draftCount) ?></div>
                <div class="text-xs text-slate-400 mt-1">Menunggu finalisasi data</div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nomor CAM, debitur, usaha..." 
                           class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-800"><?= count($daftar_kredit) ?></span> pengajuan
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600" id="tabelPengajuan">
                    <thead class="bg-slate-50 text-xs font-semibold text-slate-700 uppercase tracking-wider border-b border-slate-200">
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
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Input Pengajuan Baru" di atas untuk memulai analisa.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_kredit as $row): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-xs font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-xs text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? '-')) ?></div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-indigo-700 whitespace-nowrap">
                                        Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-800"><?= esc($row['skema_pembayaran'] ?? 'Balloon') ?></div>
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $badgeClass ?>">
                                            <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>" 
                                               class="p-1.5 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Buka / Edit Analisa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank"
                                               class="p-1.5 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Cetak CAM PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            <a href="<?= base_url('pengajuan/hapus/' . $row['kredit_id']) ?>" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengajuan <?= esc($row['nomor_aplikasi']) ?>?')"
                                               class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </a>
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
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> OTW Credit Engine & CAM. Keamanan data terenkripsi & tersimpan permanen di database.
        </div>
    </footer>

    <script>
        // Simple client-side search
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabelPengajuan tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
