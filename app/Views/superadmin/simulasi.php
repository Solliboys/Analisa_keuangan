<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Semua Pengajuan Kredit | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; 
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'simulasi',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full Width md:pl-64) -->
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
            <div class="flex items-center gap-2">
                <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                    Logout
                </a>
            </div>
        </header>

        <!-- Desktop Topbar Full Width -->
        <div class="bg-white border-b border-slate-200/80 px-6 lg:px-8 py-3.5 hidden md:flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Super Administrator</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Semua Pengajuan</span>
            </div>
        </div>

        <!-- Main Content Body Full Width -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">

            <!-- Persistent Flash Message Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div id="flashSuccessAlert" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashSuccessAlert').remove()" class="text-emerald-500 hover:text-emerald-800 font-bold text-sm">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div id="flashErrorAlert" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashErrorAlert').remove()" class="text-rose-500 hover:text-rose-800 font-bold text-sm">&times;</button>
                </div>
            <?php endif; ?>
            
            <!-- Page Header: Title & Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight">
                        Semua Pengajuan Kredit
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">
                        Daftar seluruh berkas permohonan pembiayaan, skema tenor, jaminan, dan status analisis CAM.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Export Button -->
                    <button type="button" onclick="handleExportData()" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 rounded-xl border border-slate-200 text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export Data</span>
                    </button>

                    <!-- + Buat Pengajuan Baru -->
                    <a href="<?= base_url('pengajuan/baru') ?>" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Pengajuan Baru</span>
                    </a>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 mb-6 shadow-2xs">
                <div class="flex flex-col md:flex-row items-center gap-3">
                    
                    <!-- Search Input Field -->
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               id="searchInput" 
                               onkeyup="filterTable()" 
                               placeholder="Cari nomor aplikasi, nama debitur, atau nama usaha..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-transparent rounded-xl border border-transparent focus:border-blue-500 focus:bg-slate-50/50 outline-none text-slate-800 placeholder-slate-400 transition" />
                    </div>

                    <!-- Filter Tenor Dropdown -->
                    <div class="relative w-full md:w-44 shrink-0">
                        <select id="tenorFilter" onchange="filterTable()" class="w-full px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Semua Tenor</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                            <option value="24">24 Bulan</option>
                            <option value="36">36 Bulan</option>
                            <option value="48">48 Bulan</option>
                            <option value="60">60 Bulan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                    <!-- Filter Status Pengajuan Dropdown -->
                    <div class="relative w-full md:w-48 shrink-0">
                        <select id="statusFilter" onchange="filterTable()" class="w-full px-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl appearance-none pr-9 focus:border-blue-500 focus:outline-none transition cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="disetujui bersyarat">Disetujui Bersyarat</option>
                            <option value="diajukan">Diajukan</option>
                            <option value="draft">Draft</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Table Card (Struktur Gambar 1 & Aksi Detail + Hapus) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="simulasiTable">
                        <thead>
                            <tr class="border-b border-slate-100 bg-white text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">NO. APLIKASI</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">NAMA DEBITUR &amp; USAHA</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">PLAFON PINJAMAN</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">SKEMA &amp; TENOR</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">JAMINAN</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">STATUS</th>
                                <th scope="col" class="py-4 px-4 font-semibold whitespace-nowrap">TANGGAL BUAT</th>
                                <th scope="col" class="py-4 px-4 font-semibold text-center whitespace-nowrap">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm" id="tableBody">
                            
                            <?php if (empty($daftar_kredit)): ?>
                                <tr id="emptyRow">
                                    <td colspan="8" class="text-center py-12 text-slate-400">
                                        <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <p class="font-medium text-slate-600">Belum ada berkas pengajuan kredit.</p>
                                        <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Pengajuan Baru" untuk membuat berkas pengajuan kredit.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($daftar_kredit as $row): ?>
                                    <tr id="row-kredit-<?= $row['kredit_id'] ?>" 
                                        class="hover:bg-slate-50/70 transition-colors data-row" 
                                        data-id="<?= $row['kredit_id'] ?>"
                                        data-nomor="<?= strtolower(esc($row['nomor_aplikasi'])) ?>"
                                        data-nama="<?= strtolower(esc($row['nama_debitur'] ?: '')) ?>"
                                        data-usaha="<?= strtolower(esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? ''))) ?>"
                                        data-tenor="<?= (int)($row['tenor_bulan'] ?? 12) ?>"
                                        data-plafon="<?= (float)($row['plafon_pengajuan'] ?? 0) ?>"
                                        data-status="<?= strtolower($row['status_pengajuan'] ?? 'draft') ?>">
                                        
                                        <!-- 1. NO. APLIKASI -->
                                        <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                            <?= esc($row['nomor_aplikasi']) ?>
                                            <span class="block text-xs font-normal text-slate-400"><?= esc($row['tipe_aplikasi'] ?? 'Fasilitas Baru') ?></span>
                                        </td>

                                        <!-- 2. NAMA DEBITUR & USAHA -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                            <div class="text-xs text-slate-500"><?= esc($row['nama_usaha'] ?: ($row['jenis_debitur'] ?? 'Individu')) ?></div>
                                        </td>

                                        <!-- 3. PLAFON PINJAMAN (Warna Biru Tebal) -->
                                        <td class="py-3.5 px-4 font-bold text-blue-600 whitespace-nowrap">
                                            Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                        </td>

                                        <!-- 4. SKEMA & TENOR -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-semibold text-slate-800"><?= esc($row['skema_pembayaran'] ?? 'Balloon Payment') ?></div>
                                            <div class="text-xs text-slate-500"><?= (int)($row['tenor_bulan'] ?? 12) ?> Bulan (<?= (float)($row['bunga_per_bulan'] ?? 0) ?>%/bln)</div>
                                        </td>

                                        <!-- 5. JAMINAN (Badge On Hand / Jenis Jaminan) -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                                <?= esc($row['jenis_jaminan'] ?? 'On Hand') ?>
                                            </span>
                                        </td>

                                        <!-- 6. STATUS -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <?php
                                                $st = strtolower($row['status_pengajuan'] ?? 'draft');
                                                $badgeClass = 'bg-slate-100 text-slate-700 border-slate-200';
                                                if ($st === 'disetujui') {
                                                    $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                } elseif ($st === 'disetujui bersyarat' || str_contains($st, 'bersyarat')) {
                                                    $badgeClass = 'bg-blue-50/70 text-slate-700 border-blue-200';
                                                } elseif ($st === 'ditolak') {
                                                    $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                } elseif ($st === 'diajukan') {
                                                    $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                }
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $badgeClass ?>">
                                                <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                            </span>
                                        </td>

                                        <!-- 7. TANGGAL BUAT -->
                                        <td class="py-3.5 px-4 text-xs text-slate-500 whitespace-nowrap">
                                            <?= !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '-' ?>
                                        </td>

                                        <!-- 8. AKSI (Hanya Detail dan Hapus) -->
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" 
                                                        onclick="viewDetailKredit(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>)" 
                                                        class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors cursor-pointer">
                                                    Detail
                                                </button>
                                                <button type="button" 
                                                        onclick="hapusSimulasi(<?= (int)$row['kredit_id'] ?>, '<?= esc($row['nomor_aplikasi']) ?>')" 
                                                        class="px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors cursor-pointer">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>

                <!-- Table Footer & Counter -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border-t border-slate-100 text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800" id="showingCount"><?= count($daftar_kredit ?? []) ?></span> berkas pengajuan kredit
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Sinkron real-time dengan basis data CAM
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL: Detail Pengajuan Kredit (Format Form Reviewer) -->
    <div id="detailModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-5xl w-full my-auto overflow-hidden animate-in fade-in duration-200 max-h-[92vh] flex flex-col">
            
            <!-- Modal Header (Aksen Form Reviewer) -->
            <div class="p-5 border-b border-blue-100 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md font-extrabold text-lg shrink-0">
                        ⚖️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-extrabold text-blue-700 uppercase tracking-wider">Lembar Evaluasi Reviewer</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-[11px] text-slate-500 font-semibold" id="detailNomorAplikasi">CAM-XXXXXXXX-XXXX</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-[11px] text-slate-400" id="detailTanggalBuat">-</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900" id="detailNamaDebiturHeader">
                            Nama Debitur
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Plafon Pengajuan</span>
                        <div class="text-base font-extrabold text-blue-700" id="detailPlafonHeader">
                            Rp 0
                        </div>
                    </div>
                    <span id="detailBadgeStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-blue-50 text-blue-700 border-blue-200">
                        Draft
                    </span>
                    <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer ml-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body (Scrollable 2 Kolom) -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-6 text-slate-800" id="detailReviewerBody">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                    
                    <!-- SEBELAH KIRI (lg:col-span-7): RINGKASAN BERKAS INPUTAN PENGUSUL -->
                    <div class="lg:col-span-7 flex flex-col space-y-4">
                        <div class="flex items-center justify-between pb-1 border-b border-slate-100">
                            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Ringkasan Berkas Inputan Pengusul
                            </h3>
                            <span class="text-[11px] text-slate-400">Data Pokok Analisis</span>
                        </div>

                        <!-- 4 Kotak Data Pokok -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            
                            <!-- 1. Calon Debitur & Usaha -->
                            <div class="p-3.5 bg-slate-50/90 rounded-xl border border-slate-200 flex flex-col justify-between">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">1</span>
                                    <span>Calon Debitur & Usaha</span>
                                </span>
                                <div class="space-y-1.5 my-2 text-xs">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold block">Nama & Jenis:</span>
                                        <strong class="text-slate-800 text-xs block" id="modalDebiturNama">-</strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold block">Usaha & Sektor:</span>
                                        <span class="text-blue-800 font-bold block" id="modalDebiturUsaha">-</span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1.5 border-t border-slate-200/60 flex justify-between">
                                    <span>HP: <strong class="text-slate-700" id="modalDebiturHp">-</strong></span>
                                    <span>Lama: <strong class="text-slate-700" id="modalDebiturLama">-</strong></span>
                                </div>
                            </div>

                            <!-- 2. Struktur Pembiayaan -->
                            <div class="p-3.5 bg-slate-50/90 rounded-xl border border-slate-200 flex flex-col justify-between">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">2</span>
                                    <span>Struktur Pembiayaan</span>
                                </span>
                                <div class="space-y-1.5 my-2 text-xs">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold block">Plafon Pinjaman:</span>
                                        <strong class="text-blue-700 text-sm block font-extrabold" id="modalPlafonPinjaman">Rp 0</strong>
                                    </div>
                                    <div class="flex justify-between text-xs">
                                        <span>Tenor: <strong id="modalTenorBulan">12 Bulan</strong></span>
                                        <span>Bunga: <strong id="modalSukuBunga">2.5% / bln</strong></span>
                                    </div>
                                </div>
                                <div class="text-[11px] text-blue-700 font-semibold pt-1.5 border-t border-slate-200/60 flex justify-between">
                                    <span id="modalSkemaPembayaran">Installment</span>
                                    <span id="modalEstAngsuran">Est: -</span>
                                </div>
                            </div>

                            <!-- 3. SLIK OJK (Ideb) -->
                            <div class="p-3.5 bg-slate-50/90 rounded-xl border border-slate-200 flex flex-col justify-between">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">3</span>
                                    <span>SLIK OJK (Ideb)</span>
                                </span>
                                <div class="space-y-1.5 my-2 text-xs flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold">Status SLIK:</span>
                                        <span class="font-bold text-xs" id="modalStatusSlik">Terbit</span>
                                    </div>
                                    <p class="text-slate-600 text-[11px] line-clamp-2 leading-relaxed" id="modalRingkasanSlik">
                                        Riwayat fasilitas pembiayaan debitur lancar tanpa catatan NPL.
                                    </p>
                                </div>
                                <div class="text-[11px] text-slate-500 pt-1.5 border-t border-slate-200/60 flex justify-between">
                                    <span>Kolektibilitas:</span>
                                    <span class="text-emerald-700 font-bold" id="modalKolSlik">Kol 1 (Lancar)</span>
                                </div>
                            </div>

                            <!-- 4. Agunan & Jaminan -->
                            <div class="p-3.5 bg-slate-50/90 rounded-xl border border-slate-200 flex flex-col justify-between">
                                <span class="font-bold text-slate-900 block text-xs border-b border-slate-200/80 pb-1 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-extrabold shrink-0">4</span>
                                    <span>Agunan & Jaminan</span>
                                </span>
                                <div class="space-y-1.5 my-2 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold">Nilai Likuidasi (LV):</span>
                                        <strong class="text-slate-800" id="modalNilaiLv">Rp 0</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold">Coverage LTV:</span>
                                        <strong class="text-blue-700 font-bold" id="modalCoverageLv">0%</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 text-[10px] uppercase font-semibold">Jenis Jaminan:</span>
                                        <span class="text-slate-700 font-medium" id="modalJenisJaminan">On Hand</span>
                                    </div>
                                </div>
                                <div class="text-[11px] pt-1.5 border-t border-slate-200/60 flex justify-between items-center">
                                    <span class="text-slate-400">Nilai Pasar (MV):</span>
                                    <strong class="text-slate-700" id="modalNilaiMv">Rp 0</strong>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SEBELAH KANAN (lg:col-span-5): LEMBAR KEPUTUSAN REVIEWER & AI 5C -->
                    <div class="lg:col-span-5 flex flex-col space-y-4">
                        
                        <!-- Card Keputusan Reviewer / Komite -->
                        <div class="bg-slate-50/90 rounded-2xl border border-slate-200 p-4 flex flex-col space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                                    <h3 class="text-xs font-extrabold text-blue-900 uppercase tracking-wider">
                                        Keputusan Reviewer / Komite
                                    </h3>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Sidang Komite</span>
                            </div>

                            <!-- Opsi Status Visual -->
                            <div class="grid grid-cols-3 gap-2 text-center" id="modalOpsiKeputusan">
                                <div class="p-2 rounded-xl border border-slate-200 bg-white" id="badgeKepSetuju">
                                    <div class="text-[11px] font-bold text-slate-700">Setuju</div>
                                    <div class="text-[9px] text-slate-400">Penuh</div>
                                </div>
                                <div class="p-2 rounded-xl border border-slate-200 bg-white" id="badgeKepBersyarat">
                                    <div class="text-[11px] font-bold text-slate-700">Bersyarat</div>
                                    <div class="text-[9px] text-slate-400">Ketentuan</div>
                                </div>
                                <div class="p-2 rounded-xl border border-slate-200 bg-white" id="badgeKepTolak">
                                    <div class="text-[11px] font-bold text-slate-700">Ditolak</div>
                                    <div class="text-[9px] text-slate-400">Risiko</div>
                                </div>
                            </div>

                            <!-- Catatan Reviewer -->
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Catatan & Notulensi Komite:</span>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 text-xs text-slate-700 min-h-[64px] italic" id="modalCatatanReviewer">
                                    Belum ada catatan review komite kredit.
                                </div>
                            </div>
                        </div>

                        <!-- Card Inferensi AI 5C Gemini -->
                        <div class="bg-gradient-to-br from-indigo-50/80 via-white to-blue-50/80 rounded-2xl border border-indigo-100 p-4 space-y-2.5 shadow-2xs">
                            <div class="flex items-center justify-between pb-2 border-b border-indigo-100">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm">✨</span>
                                    <h3 class="text-xs font-extrabold text-indigo-900 uppercase tracking-wider">
                                        Analisis 5C & Skor AI
                                    </h3>
                                </div>
                                <div class="flex items-center gap-1 bg-indigo-600 text-white px-2 py-0.5 rounded-lg text-xs font-extrabold" id="modalSkorAiBadge">
                                    <span>Skor</span>
                                    <span id="modalSkorAiVal">82</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500">Rekomendasi AI:</span>
                                <span class="font-extrabold text-indigo-700" id="modalRekomendasiAi">Disetujui Penuh</span>
                            </div>

                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Analisis Risiko & Mitigasi:</span>
                                <p class="text-slate-600 text-[11px] leading-relaxed line-clamp-3 bg-white/80 p-2.5 rounded-xl border border-indigo-100/60" id="modalAnalisisRisikoAi">
                                    Profil arus kas positif dengan nilai jaminan mencukupi. Kapasitas pembayaran debitur dinilai aman untuk plafon yang diajukan.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/80 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <a id="btnCetakPdf" href="#" target="_blank" class="w-full sm:w-auto px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs inline-flex items-center justify-center gap-2 border border-slate-200 transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak CAM PDF</span>
                </a>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" onclick="closeDetailModal()" class="flex-1 sm:flex-none px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs border border-slate-200 transition cursor-pointer">
                        Tutup
                    </button>
                    <a id="btnBukaReview" href="#" class="flex-1 sm:flex-none px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition inline-flex items-center justify-center gap-1.5 shadow-sm">
                        <span>Buka Form Reviewer</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Client-side Interactive Scripts -->
    <script>
        function filterTable() {
            const searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
            const tenorVal = document.getElementById('tenorFilter').value;
            const statusVal = document.getElementById('statusFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr.data-row');
            
            let visibleCount = 0;

            rows.forEach(row => {
                const nomor = row.getAttribute('data-nomor') || '';
                const nama = row.getAttribute('data-nama') || '';
                const usaha = row.getAttribute('data-usaha') || '';
                const tenor = row.getAttribute('data-tenor');
                const status = row.getAttribute('data-status') || '';

                let matchSearch = (!searchVal) || (nomor.includes(searchVal) || nama.includes(searchVal) || usaha.includes(searchVal));
                let matchTenor = (!tenorVal) || (tenor === tenorVal);
                let matchStatus = (!statusVal) || (status.includes(statusVal));

                if (matchSearch && matchTenor && matchStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('showingCount').innerText = visibleCount;
        }

        function updateRowCount() {
            const visibleRows = document.querySelectorAll('#tableBody tr.data-row:not([style*="display: none"])');
            document.getElementById('showingCount').innerText = visibleRows.length;
            
            const totalRows = document.querySelectorAll('#tableBody tr.data-row');
            if (totalRows.length === 0) {
                document.getElementById('tableBody').innerHTML = `
                    <tr id="emptyRow">
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <div class="w-12 h-12 mx-auto mb-3 text-slate-300">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="font-medium text-slate-600">Semua berkas simulasi kredit telah dihapus.</p>
                        </td>
                    </tr>
                `;
            }
        }

        function viewDetailKredit(data) {
            const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val || 0);

            // Header Data
            document.getElementById('detailNomorAplikasi').innerText = data.nomor_aplikasi || 'CAM-000000';
            document.getElementById('detailTanggalBuat').innerText = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
            document.getElementById('detailNamaDebiturHeader').innerText = (data.nama_debitur || '-') + ' (' + (data.nama_usaha || data.jenis_debitur || 'Individu') + ')';
            document.getElementById('detailPlafonHeader').innerText = formatRupiah(data.plafon_pengajuan);

            // Badge Status Header
            const statusBadge = document.getElementById('detailBadgeStatus');
            const statusTxt = data.status_komite || data.status_pengajuan || 'Draft';
            statusBadge.innerText = statusTxt;
            if (statusTxt.toLowerCase().includes('setuju') && !statusTxt.toLowerCase().includes('bersyarat')) {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-emerald-50 text-emerald-700 border-emerald-200';
            } else if (statusTxt.toLowerCase().includes('bersyarat')) {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-amber-50 text-amber-700 border-amber-200';
            } else if (statusTxt.toLowerCase().includes('tolak')) {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-rose-50 text-rose-700 border-rose-200';
            } else {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-blue-50 text-blue-700 border-blue-200';
            }

            // Kotak 1: Calon Debitur & Usaha
            document.getElementById('modalDebiturNama').innerText = (data.nama_debitur || '-') + (data.npwp_pribadi ? ' • NIK: ' + data.npwp_pribadi : '');
            document.getElementById('modalDebiturUsaha').innerText = (data.nama_usaha || '-') + (data.sektor_usaha ? ' • ' + data.sektor_usaha : '');
            document.getElementById('modalDebiturHp').innerText = data.nomor_hp || '-';
            document.getElementById('modalDebiturLama').innerText = data.lama_usaha ? data.lama_usaha : '-';

            // Kotak 2: Struktur Pembiayaan
            document.getElementById('modalPlafonPinjaman').innerText = formatRupiah(data.plafon_pengajuan);
            const tenor = parseInt(data.tenor_bulan) || 12;
            const bungaBln = parseFloat(data.bunga_per_bulan) || 0;
            document.getElementById('modalTenorBulan').innerText = tenor + ' Bulan';
            document.getElementById('modalSukuBunga').innerText = (bungaBln > 0 ? bungaBln + '% / bln' : (data.bunga_per_tahun || 0) + '% / thn');
            document.getElementById('modalSkemaPembayaran').innerText = data.skema_pembayaran || 'Installment';
            
            // Hitung estimasi angsuran
            const pokok = parseFloat(data.plafon_pengajuan) || 0;
            if (pokok > 0 && tenor > 0) {
                const estAngs = (pokok / tenor) + (pokok * (bungaBln / 100));
                document.getElementById('modalEstAngsuran').innerText = 'Est: ' + formatRupiah(estAngs) + '/bln';
            } else {
                document.getElementById('modalEstAngsuran').innerText = 'Est: -';
            }

            // Kotak 3: SLIK OJK
            const statusSlik = data.status_slik || 'Terbit';
            const slikElem = document.getElementById('modalStatusSlik');
            slikElem.innerText = statusSlik;
            slikElem.className = 'font-bold text-xs ' + (statusSlik === 'Terbit' ? 'text-emerald-700' : 'text-amber-700');
            document.getElementById('modalRingkasanSlik').innerText = data.ringkasan_slik || 'Riwayat fasilitas pembiayaan debitur lancar tanpa catatan NPL aktif.';
            const isKolLancar = !data.ada_tunggakan_slik || data.ada_tunggakan_slik == 0;
            const kolElem = document.getElementById('modalKolSlik');
            kolElem.innerText = isKolLancar ? 'Kol 1 (Lancar)' : 'Kol 2+ (Perhatian)';
            kolElem.className = isKolLancar ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold';

            // Kotak 4: Agunan & Jaminan
            document.getElementById('modalNilaiLv').innerText = formatRupiah(data.total_nilai_likuidasi || 0);
            document.getElementById('modalCoverageLv').innerText = (data.coverage_lv || 0) + '%';
            document.getElementById('modalJenisJaminan').innerText = data.jenis_jaminan || 'Sertifikat / BPKB';
            document.getElementById('modalNilaiMv').innerText = formatRupiah(data.total_nilai_pasar || 0);

            // Opsi Keputusan Reviewer / Komite Highlight
            const bSetuju = document.getElementById('badgeKepSetuju');
            const bBersyarat = document.getElementById('badgeKepBersyarat');
            const bTolak = document.getElementById('badgeKepTolak');

            // Reset classes
            bSetuju.className = 'p-2 rounded-xl border border-slate-200 bg-white transition';
            bBersyarat.className = 'p-2 rounded-xl border border-slate-200 bg-white transition';
            bTolak.className = 'p-2 rounded-xl border border-slate-200 bg-white transition';

            if (statusTxt.toLowerCase().includes('bersyarat')) {
                bBersyarat.className = 'p-2 rounded-xl border-2 border-amber-500 bg-amber-50 text-amber-900 shadow-xs font-bold';
            } else if (statusTxt.toLowerCase().includes('setuju')) {
                bSetuju.className = 'p-2 rounded-xl border-2 border-emerald-500 bg-emerald-50 text-emerald-900 shadow-xs font-bold';
            } else if (statusTxt.toLowerCase().includes('tolak')) {
                bTolak.className = 'p-2 rounded-xl border-2 border-rose-500 bg-rose-50 text-rose-900 shadow-xs font-bold';
            }

            // Catatan Reviewer
            const catatanElem = document.getElementById('modalCatatanReviewer');
            if (data.catatan_reviewer && data.catatan_reviewer.trim() !== '') {
                catatanElem.innerText = '"' + data.catatan_reviewer + '"';
                catatanElem.classList.remove('text-slate-400');
                catatanElem.classList.add('text-slate-800');
            } else {
                catatanElem.innerText = 'Belum ada catatan review atau notulensi komite.';
                catatanElem.classList.remove('text-slate-800');
                catatanElem.classList.add('text-slate-400');
            }

            // Card AI 5C
            const skor = data.skor_ai || 82;
            document.getElementById('modalSkorAiVal').innerText = skor;
            document.getElementById('modalRekomendasiAi').innerText = data.rekomendasi_ai || (skor >= 75 ? 'Disetujui Penuh' : 'Perlu Mitigasi Ketat');
            document.getElementById('modalAnalisisRisikoAi').innerText = data.analisis_risiko || 'Profil arus kas positif dengan nilai jaminan mencukupi. Kapasitas pembayaran debitur dinilai aman untuk plafon yang diajukan.';

            // Link Actions
            document.getElementById('btnCetakPdf').href = '<?= base_url('pengajuan/cetak') ?>/' + data.kredit_id;
            document.getElementById('btnBukaReview').href = '<?= base_url('reviewer/review') ?>/' + data.kredit_id;

            // Show Modal
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function hapusSimulasi(id, nomor) {
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
                    fetch(`<?= base_url('superadmin/hapus-simulasi') ?>/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const tr = document.getElementById(`row-kredit-${id}`);
                            if (tr) {
                                tr.style.transition = 'all 0.3s ease';
                                tr.style.opacity = '0';
                                tr.style.transform = 'scale(0.95)';
                                setTimeout(() => {
                                    tr.remove();
                                    updateRowCount();
                                }, 300);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Dihapus',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error!', 'Gagal menghubungi server atau sesi telah berakhir.', 'error');
                    });
                }
            });
        }

        function handleExportData() {
            Swal.fire({
                icon: 'info',
                title: 'Export Data CAM',
                text: 'Mempersiapkan data rekapitulasi pengajuan kredit...',
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>
</body>
</html>
