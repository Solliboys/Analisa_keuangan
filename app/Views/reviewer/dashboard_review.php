<?php
/**
 * @var string $title
 * @var string $nama_lengkap
 * @var string $jabatan
 * @var int $total_pengajuan
 * @var float $total_plafon
 * @var int $total_menunggu
 * @var int $total_disetujui
 * @var int $total_ditolak
 * @var int $total_draft
 * @var array $daftar_pengajuan
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Workspace Reviewer & Komite Kredit') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <?php if (!empty($logoWeb)): ?>
                        <img src="<?= base_url($logoWeb) ?>" 
                             alt="<?= esc($namaWeb) ?>" 
                             class="w-10 h-10 object-contain rounded-xl border border-slate-200 bg-white p-1 shadow-xs shrink-0">
                    <?php elseif (file_exists(FCPATH . 'logo_otwe_besar.png')): ?>
                        <img src="<?= base_url('logo_otwe_besar.png') ?>" 
                             alt="<?= esc($namaWeb) ?>" 
                             class="h-10 w-auto max-w-[120px] object-contain rounded-xl border border-slate-200 bg-white p-1 shadow-xs shrink-0">
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
                        <span class="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <?= esc(!empty($jabatan) ? $jabatan : 'Reviewer Kredit') ?>
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
                            $avatarSrc = !empty($fotoProfil) ? base_url($fotoProfil) : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap ?? 'Reviewer') . '&background=4f46e5&color=fff&bold=true';
                        ?>
                        <button type="button" onclick="toggleProfileDropdown(event)" class="flex items-center gap-1.5 p-0.5 rounded-full hover:ring-2 hover:ring-indigo-400 focus:ring-2 focus:ring-indigo-500 transition cursor-pointer">
                            <img src="<?= $avatarSrc ?>" 
                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap ?? 'Reviewer') ?>&background=4f46e5&color=fff&bold=true';" 
                                 alt="Foto Profil" 
                                 class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400 hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="dropdownMenuProfil" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 animate-in fade-in duration-150">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="text-xs font-bold text-slate-800 truncate"><?= esc($nama_lengkap) ?></p>
                                <p class="text-[10px] text-slate-400"><?= esc(!empty($jabatan) ? $jabatan : 'Reviewer Kredit') ?></p>
                            </div>
                            
                            <a href="<?= base_url('profil') ?>" class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition flex items-center gap-2.5">
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
        <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-sm mb-8">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-blue-100 uppercase tracking-wider">
                Ruang Kerja <?= esc(!empty($jabatan) ? $jabatan : 'Reviewer Kredit') ?>
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold mt-3 tracking-tight">Selamat Datang, <?= esc($nama_lengkap) ?>!</h1>
            <p class="text-sm text-blue-100 mt-1 max-w-3xl leading-relaxed">
                Tinjau berkas analisa kredit (CAM), evaluasi kepatuhan 5C serta riwayat SLIK OJK, dan berikan keputusan mutlak persetujuan (Approve) atau penolakan (Reject) permohonan kredit.
            </p>
        </div>

        <!-- Metric Performance Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Berkas Masuk</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($total_pengajuan ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Total berkas nasabah terdaftar</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Menunggu Review</span>
                <div class="text-2xl font-extrabold text-blue-700 mt-1"><?= number_format($total_menunggu ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Status Diajukan (Belum diputuskan)</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Pengajuan Disetujui</span>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= number_format($total_disetujui ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Disetujui oleh Reviewer / Komite</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Pengajuan Ditolak</span>
                <div class="text-2xl font-extrabold text-rose-700 mt-1"><?= number_format($total_ditolak ?? 0) ?></div>
                <div class="text-xs text-slate-400 mt-1">Tidak memenuhi syarat risiko kredit</div>
            </div>
        </div>

        <!-- Table Card: Daftar Berkas Pengajuan untuk Reviewer (Full Width) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" id="searchInput" placeholder="Cari nomor CAM, debitur, usaha..." 
                           class="w-full pl-9 pr-3 py-2 text-xs font-medium border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-xs text-slate-500 font-medium">
                        Menampilkan <span class="font-bold text-slate-800"><?= count($daftar_pengajuan ?? []) ?></span> pengajuan
                    </div>
                    <a href="<?= base_url('pengajuan') ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hidden sm:inline">
                        Lihat Semua &rarr;
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600" id="tabelPengajuan">
                    <thead class="bg-slate-50 text-xs font-bold text-slate-700 uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">ID Pengajuan</th>
                            <th class="py-3.5 px-4">Nama Debitur & Usaha</th>
                            <th class="py-3.5 px-4">Plafon & Tenor</th>
                            <th class="py-3.5 px-4 text-center">Status SLIK</th>
                            <th class="py-3.5 px-4 text-center">Status Pengajuan</th>
                            <th class="py-3.5 px-4">Catatan Keputusan</th>
                            <th class="py-3.5 px-4">Tanggal Permohonan</th>
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
                                    <p class="font-medium text-slate-600">Belum ada berkas pengajuan kredit yang masuk.</p>
                                    <p class="text-xs text-slate-400 mt-1">Daftar berkas nasabah yang diinput staf pengusul akan otomatis muncul di sini untuk ditinjau oleh Reviewer.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($daftar_pengajuan as $row): ?>
                                <tr class="hover:bg-slate-50 transition" id="row-kredit-<?= $row['kredit_id'] ?>">
                                    <!-- 1. ID Pengajuan -->
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-xs font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                    </td>

                                    <!-- 2. Nama Debitur & Usaha -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-xs text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? 'Individu')) ?></div>
                                    </td>

                                    <!-- 3. Plafon & Tenor -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-blue-700">Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></div>
                                        <div class="text-xs text-slate-400"><?= (int)($row['tenor_bulan'] ?? 12) ?> Bulan &bull; <?= esc($row['skema_pembayaran'] ?? 'Installment') ?></div>
                                    </td>

                                    <!-- 4. Status SLIK OJK -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <?php
                                            $slikStatus = $row['status_slik'] ?? 'Belum Terbit';
                                            if ($slikStatus === 'Terbit') {
                                                if (!empty($row['ada_tunggakan_slik'])) {
                                                    $slikClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                    $slikText  = 'Kol 2-5 (Tunggakan)';
                                                } else {
                                                    $slikClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                    $slikText  = 'Kol 1 (Lancar)';
                                                }
                                            } else {
                                                $slikClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                                $slikText  = 'Belum Terbit';
                                            }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border <?= $slikClass ?>">
                                            <?= esc($slikText) ?>
                                        </span>
                                    </td>

                                    <!-- 5. Status Pengajuan -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap" id="status-badge-container-<?= $row['kredit_id'] ?>">
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
                                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-300';
                                                $dotClass   = 'bg-emerald-500';
                                                $statusLabel= 'Disetujui';
                                            } elseif ($st === 'ditolak' || strpos($st, 'tolak') !== false) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-300';
                                                $dotClass   = 'bg-rose-500';
                                                $statusLabel= 'Ditolak';
                                            } elseif ($st === 'diajukan') {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $dotClass   = 'bg-blue-500';
                                                $statusLabel= 'Diajukan';
                                            } else {
                                                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                                $dotClass   = 'bg-slate-400';
                                                $statusLabel= !empty($rawStatus) ? $rawStatus : 'Draft';
                                            }
                                        ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?= $badgeClass ?> shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $dotClass ?>"></span>
                                            <span><?= esc($statusLabel) ?></span>
                                        </span>
                                    </td>

                                    <!-- 6. Catatan Keputusan Reviewer / Komite -->
                                    <td class="py-3.5 px-4 min-w-[200px] max-w-[320px]">
                                        <?php 
                                            $catatanReviewer = trim($row['catatan_reviewer'] ?? '');
                                        ?>
                                        <?php if (!empty($catatanReviewer)): ?>
                                            <div class="text-xs text-slate-700 leading-relaxed font-medium bg-slate-50/80 p-2 rounded-xl border border-slate-200/80" title="<?= esc($catatanReviewer) ?>">
                                                <div class="line-clamp-2 hover:line-clamp-none transition-all cursor-default">
                                                    <?= esc($catatanReviewer) ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 italic font-normal flex items-center gap-1">
                                                <span class="text-slate-300">&mdash;</span>
                                                <span>Belum ada catatan</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- 7. Tanggal Permohonan -->
                                    <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </td>

                                    <!-- 7. Aksi Reviewer (Edit, PDF) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Edit (Buka Form Review & Keputusan) -->
                                            <a href="<?= base_url('reviewer/review/' . $row['kredit_id']) ?>" 
                                               class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-lg transition shadow-xs flex items-center gap-1.5" title="Buka Form Review & Keputusan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                <span>Edit</span>
                                            </a>

                                            <!-- Cetak CAM PDF -->
                                            <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank"
                                               class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 text-xs font-bold rounded-lg transition" title="Cetak Laporan CAM PDF">
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



    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Portal Komite & Reviewer Kredit Terintegrasi
        </div>
    </footer>

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

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userProfileDropdown');
            const menu = document.getElementById('dropdownMenuProfil');
            if (dropdown && !dropdown.contains(e.target) && menu) {
                menu.classList.add('hidden');
            }
        });

        // Setujui Pengajuan Kredit
        function bukaModalSetujui(kreditId, noAplikasi, namaDebitur) {
            Swal.fire({
                title: 'Setujui Pengajuan Kredit?',
                html: `
                    <div class="text-left text-xs text-slate-600 mb-3">
                        <div class="font-bold text-slate-800 text-sm mb-1">${noAplikasi} - ${namaDebitur}</div>
                        <p class="mb-2">Anda akan memberikan persetujuan resmi (Approve) untuk permohonan kredit ini.</p>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Catatan Persetujuan / Rekomendasi Reviewer:</label>
                        <textarea id="catatanApproval" rows="3" class="w-full text-xs border border-slate-300 rounded-xl p-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none" placeholder="Tuliskan catatan kelayakan atau rekomendasi komite..."></textarea>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui Sekarang',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return document.getElementById('catatanApproval').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Persetujuan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const formData = new FormData();
                    formData.append('kredit_id', kreditId);
                    formData.append('catatan', result.value);

                    fetch('<?= base_url('reviewer/setujui') ?>/' + kreditId, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disetujui!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    });
                }
            });
        }

        // Tolak Pengajuan Kredit
        function bukaModalTolak(kreditId, noAplikasi, namaDebitur) {
            Swal.fire({
                title: 'Tolak Pengajuan Kredit?',
                html: `
                    <div class="text-left text-xs text-slate-600 mb-3">
                        <div class="font-bold text-slate-800 text-sm mb-1">${noAplikasi} - ${namaDebitur}</div>
                        <p class="mb-2 text-rose-600 font-semibold">Pengajuan ini akan ditolak (Reject) dan tidak dapat dicairkan.</p>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Alasan Penolakan (Wajib Diisi):</label>
                        <textarea id="alasanPenolakan" rows="3" class="w-full text-xs border border-rose-300 rounded-xl p-2.5 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 outline-none" placeholder="Tuliskan alasan penolakan kredit (misal: DSC tidak mencukupi, riwayat macet SLIK, dll)..."></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tolak Pengajuan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const alasan = document.getElementById('alasanPenolakan').value.trim();
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }
                    return alasan;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Penolakan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const formData = new FormData();
                    formData.append('kredit_id', kreditId);
                    formData.append('alasan', result.value);

                    fetch('<?= base_url('reviewer/tolak') ?>/' + kreditId, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pengajuan Ditolak',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan sistem.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    });
                }
            });
        }

        // Review Evaluasi AI 5C Gemini
        function bukaModalReviewAI(kreditId, noAplikasi, namaDebitur) {
            Swal.fire({
                title: 'Memuat Analisa AI...',
                text: 'Mengambil data evaluasi risiko 5C Gemini AI...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= base_url('reviewer/detail-ai') ?>/' + kreditId, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.status) {
                    Swal.fire('Gagal', data.message || 'Gagal memuat analisa AI.', 'error');
                    return;
                }

                const a = data.analisa || {};
                const skor = parseInt(a.skor_kelayakan_ai || 0);
                const rekomendasi = a.rekomendasi_keputusan_ai || 'Perlu Evaluasi Lanjutan';
                const rekomendasiPlafon = a.rekomendasi_plafon_ai ? 'Rp ' + new Intl.NumberFormat('id-ID').format(a.rekomendasi_plafon_ai) : '-';
                const analisisRisiko = a.analisis_risiko_ai || 'Analisis risiko normal.';

                let badgeSkorClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                if (skor < 60) badgeSkorClass = 'bg-rose-100 text-rose-800 border-rose-300';
                else if (skor < 75) badgeSkorClass = 'bg-amber-100 text-amber-800 border-amber-300';

                Swal.fire({
                    title: `<div class="flex items-center justify-center gap-2 text-indigo-700 text-base font-extrabold uppercase">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Review Analisa AI (Gemini 5C)
                            </div>`,
                    html: `
                        <div class="text-left space-y-4 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="font-bold text-slate-800 text-sm">${data.nomor_aplikasi} - ${data.nama_debitur}</div>
                                <div class="text-slate-500 mt-0.5">Plafon Pengajuan: <strong class="text-indigo-700">Rp ${new Intl.NumberFormat('id-ID').format(data.plafon || 0)}</strong></div>
                            </div>

                            <!-- Skor & Rekomendasi Grid -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3.5 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-100 text-center">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Skor Kelayakan AI</span>
                                    <div class="text-3xl font-extrabold text-indigo-700 mt-1">${skor}<span class="text-xs text-slate-400">/100</span></div>
                                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${badgeSkorClass}">
                                        ${skor >= 75 ? 'Sangat Layak' : (skor >= 60 ? 'Cukup Layak' : 'Berisiko')}
                                    </span>
                                </div>
                                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-center flex flex-col justify-center">
                                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Rekomendasi Plafon AI</span>
                                    <div class="text-base font-extrabold text-emerald-700 mt-1">${rekomendasiPlafon}</div>
                                    <div class="text-[11px] font-semibold text-slate-600 mt-0.5">${rekomendasi}</div>
                                </div>
                            </div>

                            <!-- Evaluasi Risiko 5C -->
                            <div>
                                <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block mb-1">Analisis Risiko & Catatan 5C AI:</span>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 max-h-48 overflow-y-auto leading-relaxed text-slate-700 whitespace-pre-line text-[11px]">
                                    ${analisisRisiko}
                                </div>
                            </div>
                        </div>
                    `,
                    width: '600px',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Buka Form Review & Keputusan',
                    cancelButtonText: 'Tutup'
                }).then((res) => {
                    if (res.isConfirmed) {
                        window.location.href = '<?= base_url('reviewer/review') ?>/' + kreditId;
                    }
                });
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal memproses permintaan ke server.', 'error');
            });
        }
    </script>
</body>
</html>
