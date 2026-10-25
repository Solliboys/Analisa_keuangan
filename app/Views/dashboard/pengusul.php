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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        AO
                    </div>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">Portal Account Officer (Pengusul)</span>
                        <span class="text-xs block text-slate-500 font-medium">Input Permohonan & Analisa Kelayakan Calon Debitur</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-slate-900"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            Petugas Pengusul
                        </span>
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Semua Berkas
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="px-3.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        
        <!-- Welcome Hero Banner -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-blue-100 uppercase tracking-wider">
                    Ruang Kerja Pengusul Kredit
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold mt-3 tracking-tight">Selamat Datang, <?= esc($nama_lengkap) ?>!</h1>
                <p class="text-sm text-blue-100 mt-1 max-w-xl">
                    Kelola dan pantau permohonan pembiayaan nasabah Anda. Pastikan berkas 5C lengkap sebelum diajukan ke sidang Komite Kredit.
                </p>
            </div>
            <div>
                <a href="<?= base_url('pengajuan/baru') ?>" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white text-blue-800 hover:bg-blue-50 font-extrabold text-sm rounded-2xl shadow-md hover:shadow-lg transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    + Buat Pengajuan & CAM Baru
                </a>
            </div>
        </div>

        <!-- Personal Performance Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Aplikasi Saya</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($total_pengajuan) ?></div>
                <div class="text-xs text-slate-400 mt-1">Total berkas nasabah yang Anda input</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Plafon Diusulkan</span>
                <div class="text-2xl font-extrabold text-blue-700 mt-1">Rp <?= number_format($total_plafon, 0, ',', '.') ?></div>
                <div class="text-xs text-slate-400 mt-1">Akumulasi nilai plafon permohonan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Draft / Belum Final</span>
                <div class="text-2xl font-extrabold text-amber-600 mt-1"><?= number_format($total_draft) ?></div>
                <div class="text-xs text-slate-400 mt-1">Perlu dilengkapi sebelum diajukan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui Komite</span>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= number_format($total_disetujui) ?></div>
                <div class="text-xs text-slate-400 mt-1">Siap akad & pencairan kredit</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Left: Table Berkas Pengajuan Saya -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Berkas Pengajuan Terakhir Saya</h2>
                        <p class="text-xs text-slate-500">Pantau proses analisa dan keputusan komite secara real-time</p>
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-4">No. Aplikasi</th>
                                <th class="py-3 px-4">Debitur & Usaha</th>
                                <th class="py-3 px-4">Plafon</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($daftar_pengajuan)): ?>
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-slate-400">
                                        Anda belum memiliki berkas pengajuan. Klik <strong>"+ Buat Pengajuan & CAM Baru"</strong> untuk memulai.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftar_pengajuan as $row): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                                            <?= esc($row['nomor_aplikasi']) ?>
                                            <span class="block text-[11px] font-normal text-slate-400"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-800"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                            <div class="text-[11px] text-slate-500"><?= esc($row['nama_usaha'] ?: '-') ?></div>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-blue-700 whitespace-nowrap">
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
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border <?= $badge ?>">
                                                <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>" class="px-2.5 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold rounded-lg transition" title="Lanjutkan Form">
                                                    Edit Form
                                                </a>
                                                <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank" class="px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold rounded-lg transition" title="Cetak Memo PDF">
                                                    PDF
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

            <!-- Right: Guideline & Checklist AO -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Checklist Validasi 5C AO
                    </h3>
                    
                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">1. Character (SLIK OJK)</strong>
                            Pastikan tidak ada fasilitas kredit menunggak (Kol 2 - 5) dan seluruh baki debet tercatat jujur.
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">2. Capacity (Rasio DSC &ge; 1.30x)</strong>
                            Laba bersih riil mutasi rekening koran harus mencukupi angsuran bulanan.
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">3. Collateral (Coverage MV &ge; 100%)</strong>
                            Sertifikat SHM/HGB wajib clean & clear atas nama debitur/keluarga inti.
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <strong class="text-slate-900 block mb-0.5">4. Condition & Capital</strong>
                            Usaha minimal berjalan 2 tahun dengan izin usaha resmi (NIB).
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="<?= base_url('pengajuan/baru') ?>" class="w-full block py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-center font-bold text-xs rounded-xl transition shadow-xs">
                        Mulai Input Permohonan Baru
                    </a>
                </div>
            </div>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> Credit Engine & CAM. Hak Akses Account Officer (Pengusul).
        </div>
    </footer>
</body>
</html>
