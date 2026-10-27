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

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine & CAM';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';

    // Inisial dinamis bila logo gambar belum diunggah
    $words = preg_split("/[\s,_-]+/", trim($namaWeb));
    $initials = '';
    foreach ($words as $w) {
        if (!empty($w)) $initials .= mb_substr($w, 0, 1);
        if (mb_strlen($initials) >= 2) break;
    }
    if (empty($initials)) $initials = 'OT';
    $initials = strtoupper($initials);
?>
    <!-- Top Navigation -->
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
                            <?= esc(!empty($jabatan) ? $jabatan : 'Petugas Pengusul') ?>
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
                                <p class="text-[10px] text-slate-400"><?= esc(!empty($jabatan) ? $jabatan : 'Petugas Pengusul') ?></p>
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

    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1">
        
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
                    Buat Pengajuan & CAM Baru
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

        <!-- Table Card: Daftar Berkas Pengajuan Terakhir (Full Width) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nomor CAM, debitur, usaha..." 
                           class="w-full pl-9 pr-3 py-2 text-xs font-medium border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-slate-800"><?= count($daftar_pengajuan) ?></span> pengajuan
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="text-xs font-bold text-blue-600 hover:text-blue-800 hidden sm:inline">
                        Lihat Semua &rarr;
                    </a>
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
                        <?php if (empty($daftar_pengajuan)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-600">Anda belum memiliki berkas pengajuan kredit.</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Buat Pengajuan & CAM Baru" di atas untuk memulai analisa.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_pengajuan as $row): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-xs font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-xs text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? 'Individu')) ?></div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-blue-700 whitespace-nowrap">
                                        Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-800"><?= esc($row['skema_pembayaran'] ?? 'Balloon Payment') ?></div>
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

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?>. Hak Akses Account Officer (Pengusul).
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

    <script>
        // Fitur pencarian interaktif tabel pengajuan
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tabelPengajuan tbody tr');
                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
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

        // Modal Profil Petugas Pengusul (opsional)
        function bukaModalProfil() {
            const m = document.getElementById('modalProfil');
            if (m) m.classList.remove('hidden');
        }
        function tutupModalProfil() {
            const m = document.getElementById('modalProfil');
            if (m) m.classList.add('hidden');
        }
    </script>
</body>
</html>
