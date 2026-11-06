<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Setting Referensi | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <!-- SweetAlert2 CDN -->
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; 
        }
        /* Custom scrollbar for table container */
        .table-scroll::-webkit-scrollbar {
            height: 6px;
        }
        .table-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'referensi',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full screen layout with sidebar offset md:pl-64) -->
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
                    <?php 
                        $cfg = $pengaturan_sistem ?? [];
                        if (!empty($cfg['logo_aplikasi'])): 
                    ?>
                        <img src="<?= base_url($cfg['logo_aplikasi']) ?>" class="w-6 h-6 object-contain rounded" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-sm"><?= esc($cfg['nama_aplikasi'] ?? 'OTW Kredit') ?></span>
                </div>
            </div>
            <a href="<?= base_url('profil') ?>" class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                <?= strtoupper(substr(session()->get('nama_lengkap') ?? 'SA', 0, 2)) ?>
            </a>
        </header>

        <!-- Top Header Navigation (Desktop) -->
        <div class="hidden md:flex bg-white border-b border-slate-200 px-8 py-3.5 items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('superadmin/dashboard') ?>" class="text-xs font-semibold text-slate-400 hover:text-blue-600 transition">
                    Dashboard
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Setting Referensi</span>
            </div>

            <div class="flex items-center gap-4">
                <!-- Tahun Pill -->
                <div class="px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs font-semibold text-slate-700 flex items-center gap-1.5 shadow-2xs">
                    <span>Tahun <?= date('Y') ?></span>
                </div>

                <!-- Fullscreen Button -->
                <button type="button" onclick="toggleFullscreen()" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="Toggle Layar Penuh">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>

                <!-- Profile Action -->
                <a href="<?= base_url('profil') ?>" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition" title="Profil Pengguna">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full max-w-[1600px] mx-auto">

            <!-- Flash Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div id="flashSuccessAlert" class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashSuccessAlert').remove()" class="text-emerald-500 hover:text-emerald-800 font-bold text-sm cursor-pointer">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div id="flashErrorAlert" class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-2xs w-full">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                    <button onclick="document.getElementById('flashErrorAlert').remove()" class="text-rose-500 hover:text-rose-800 font-bold text-sm cursor-pointer">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Header Title Pill -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
                    <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <span class="text-sm font-bold text-indigo-950 tracking-tight">Setting Referensi Sistem & Formulir</span>
                </div>

                <!-- Info Guide Badges -->
                <div class="flex flex-wrap items-center gap-2.5 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <strong>Fleksibel:</strong> Tambah, Edit & Hapus Bebas
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 font-semibold shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <strong>Sistem Terkunci:</strong> Bawaan Inti (Tidak Boleh Dihapus)
                    </span>
                </div>
            </div>

            <!-- Filter & Action Row -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                <!-- Filter Jenis Referensi Dropdown Terkelompok -->
                <div class="w-full sm:w-96">
                    <label for="filterJenisReferensi" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Filter Jenis Referensi
                    </label>
                    <div class="relative">
                        <select id="filterJenisReferensi" onchange="handleFilterChange(this.value)" 
                                class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs appearance-none cursor-pointer">
                            <option value="">Semua Jenis (Tampilkan Semua Data)</option>
                            
                            <!-- Kelompok 1: Referensi Formulir Dinamis -->
                            <optgroup label="📂 REFERENSI FORMULIR DINAMIS (Bisa Tambah / Edit / Hapus)">
                                <?php foreach ($daftar_jenis as $j): ?>
                                    <?php 
                                        $cat = $map_metadata[$j]['kategori'] ?? 'fleksibel';
                                        $lbl = $map_metadata[$j]['label'] ?? $j;
                                        if ($cat === 'fleksibel'):
                                    ?>
                                        <option value="<?= esc($j) ?>" <?= ($selected_jenis === $j) ? 'selected' : '' ?>>
                                            <?= esc($lbl) ?> (<?= esc($j) ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>

                            <!-- Kelompok 2: Referensi Inti Sistem -->
                            <optgroup label="🔒 REFERENSI INTI SISTEM (Terkunci - Hanya Edit)">
                                <?php foreach ($daftar_jenis as $j): ?>
                                    <?php 
                                        $cat = $map_metadata[$j]['kategori'] ?? 'fleksibel';
                                        $lbl = $map_metadata[$j]['label'] ?? $j;
                                        if ($cat === 'sistem'):
                                    ?>
                                        <option value="<?= esc($j) ?>" <?= ($selected_jenis === $j) ? 'selected' : '' ?>>
                                            <?= esc($lbl) ?> (<?= esc($j) ?>)
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- + Tambah Referensi Button -->
                <div>
                    <button type="button" onclick="bukaModalTambah()" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-xs shadow-emerald-600/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Referensi</span>
                    </button>
                </div>
            </div>

            <!-- Table Card Container -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- Table Controls (Show entries & Search Cari Data) -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <!-- Show entries selector -->
                    <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto">
                        <span>Show</span>
                        <select id="entriesPerPage" onchange="ubahEntriesPerPage(this.value)" 
                                class="text-xs font-semibold border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span>entries</span>
                    </div>

                    <!-- Cari Data Input -->
                    <div class="w-full sm:w-72">
                        <div class="relative">
                            <input type="text" id="cariDataInput" oninput="handleSearchTable(this.value)" 
                                   placeholder="Cari Data (nama, jenis, tipe...)" 
                                   class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl pl-3.5 pr-8 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:outline-none transition shadow-2xs">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto table-scroll">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-4 text-center w-12">NO</th>
                                <th class="py-3.5 px-4 w-60">JENIS REFERENSI</th>
                                <th class="py-3.5 px-4">KETERANGAN REFERENSI</th>
                                <th class="py-3.5 px-4">TIPE REFERENSI</th>
                                <th class="py-3.5 px-4">COLOR REFERENSI</th>
                                <th class="py-3.5 px-4 text-center w-36">SIFAT / AKSES</th>
                                <th class="py-3.5 px-4 text-center w-28">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="referensiTableBody" class="divide-y divide-slate-100 text-slate-700">
                            <?php if (empty($daftar_referensi)): ?>
                                <tr id="noDataRow">
                                    <td colspan="7" class="py-10 text-center text-slate-400 italic">
                                        Belum ada data referensi yang tersimpan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                    $no = 1; 
                                    foreach ($daftar_referensi as $ref): 
                                        $jenisKey = $ref['jenis_referensi'];
                                        $meta = $map_metadata[$jenisKey] ?? [
                                            'label'     => $jenisKey,
                                            'kategori'  => ((int)($ref['is_locked'] ?? 0) === 1 ? 'sistem' : 'fleksibel'),
                                            'deskripsi' => ''
                                        ];
                                        $isLocked = (int)($ref['is_locked'] ?? 0);
                                ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors ref-row" 
                                        data-id="<?= esc($ref['id']) ?>"
                                        data-jenis="<?= esc($ref['jenis_referensi']) ?>"
                                        data-keterangan="<?= esc($ref['keterangan_referensi']) ?>"
                                        data-tipe="<?= esc($ref['tipe_referensi'] ?? '') ?>"
                                        data-color="<?= esc($ref['color_referensi'] ?? 'primary') ?>"
                                        data-is-locked="<?= $isLocked ?>"
                                        data-label="<?= esc($meta['label']) ?>">
                                        
                                        <!-- NO -->
                                        <td class="py-3.5 px-4 text-center font-medium text-slate-600 row-no">
                                            <?= $no++ ?>
                                        </td>

                                        <!-- JENIS REFERENSI -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-800 text-xs">
                                                <?= esc($meta['label']) ?>
                                            </div>
                                            <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                                <?= esc($ref['jenis_referensi']) ?>
                                            </div>
                                        </td>

                                        <!-- KETERANGAN REFERENSI -->
                                        <td class="py-3.5 px-4 font-semibold text-slate-900">
                                            <?= esc($ref['keterangan_referensi']) ?>
                                        </td>

                                        <!-- TIPE REFERENSI -->
                                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                            <?= esc($ref['tipe_referensi'] ?: '-') ?>
                                        </td>

                                        <!-- COLOR REFERENSI -->
                                        <td class="py-3.5 px-4 font-medium">
                                            <?php 
                                                $clr = strtolower($ref['color_referensi'] ?? 'primary');
                                                $badgeStyle = 'text-slate-600 bg-slate-100';
                                                if ($clr === 'primary')   $badgeStyle = 'text-blue-700 bg-blue-50 border border-blue-200';
                                                elseif ($clr === 'info')      $badgeStyle = 'text-sky-700 bg-sky-50 border border-sky-200';
                                                elseif ($clr === 'success')   $badgeStyle = 'text-emerald-700 bg-emerald-50 border border-emerald-200';
                                                elseif ($clr === 'warning')   $badgeStyle = 'text-amber-700 bg-amber-50 border border-amber-200';
                                                elseif ($clr === 'danger')    $badgeStyle = 'text-rose-700 bg-rose-50 border border-rose-200';
                                                elseif ($clr === 'secondary') $badgeStyle = 'text-slate-700 bg-slate-100 border border-slate-200';
                                            ?>
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold <?= $badgeStyle ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= ($clr === 'primary') ? 'bg-blue-600' : (($clr === 'info') ? 'bg-sky-500' : (($clr === 'success') ? 'bg-emerald-500' : (($clr === 'warning') ? 'bg-amber-500' : (($clr === 'danger') ? 'bg-rose-500' : 'bg-slate-400')))) ?>"></span>
                                                <?= esc($ref['color_referensi'] ?: 'primary') ?>
                                            </span>
                                        </td>

                                        <!-- SIFAT / AKSES -->
                                        <td class="py-3.5 px-4 text-center">
                                            <?php if ($isLocked === 1): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="Data Inti Sistem: Tidak dapat dihapus karena dibutuhkan oleh alur logika aplikasi">
                                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    Sistem (Terkunci)
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200" title="Referensi Dinamis: Bebas ditambah, diedit, dan dihapus">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Fleksibel
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- AKSI (Edit Amber & Hapus Rose atau Locked) -->
                                        <td class="py-3.5 px-4 text-center">
                                            <div class="inline-flex items-center gap-1.5 justify-center">
                                                <button type="button" onclick="bukaModalEdit(<?= esc($ref['id']) ?>)" 
                                                        class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white rounded text-[11px] font-semibold transition cursor-pointer shadow-2xs" 
                                                        title="Edit Data">
                                                    Edit
                                                </button>
                                                
                                                <?php if ($isLocked === 1): ?>
                                                    <button type="button" disabled
                                                            class="px-2 py-1 bg-slate-100 text-slate-400 rounded text-[11px] font-semibold cursor-not-allowed border border-slate-200 inline-flex items-center gap-1 opacity-70" 
                                                            title="Data referensi inti sistem ini tidak boleh dihapus agar alur aplikasi tidak error.">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                        Kunci
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" onclick="konfirmasiHapus(<?= esc($ref['id']) ?>, '<?= esc(addslashes($ref['keterangan_referensi'])) ?>')" 
                                                            class="px-2.5 py-1 bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white rounded text-[11px] font-semibold transition cursor-pointer shadow-2xs" 
                                                            title="Hapus Data">
                                                        Hapus
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

                <!-- Table Footer & Pagination -->
                <div class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                    <div id="paginationInfo">
                        Menampilkan 1 sampai <span id="infoShowingCount">10</span> dari <span id="infoTotalCount"><?= count($daftar_referensi) ?></span> entri
                    </div>
                    <div class="inline-flex items-center gap-1" id="paginationControls">
                        <!-- Controls generated via JS -->
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL FORM TAMBAH / EDIT REFERENSI -->
    <!-- ============================================================== -->
    <div id="modalSettingReferensi" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 sm:p-7 shadow-2xl relative animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header: Title & Close 'X' -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h3 id="modalTitle" class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Tambah Setting Referensi
                    </h3>
                    <p id="modalSubtitle" class="text-xs text-slate-500 mt-0.5">Kelola opsi formulir atau parameter sistem</p>
                </div>
                <button type="button" onclick="tutupModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Notice Alert if editing locked system data -->
            <div id="modalLockedNotice" class="hidden mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span><strong>Perhatian:</strong> Data ini adalah referensi sistem inti. Anda hanya disarankan mengubah label keterangan atau warna tampilan.</span>
            </div>

            <!-- Modal Form (Grid 2 Kolom) -->
            <form id="formReferensi" method="post" action="<?= base_url('superadmin/referensi/simpan') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="editReferensiId" name="id" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 mb-6">
                    
                    <!-- Kolom Kiri: 1. Jenis Referensi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Jenis Referensi <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="modalJenisSelect" name="jenis_referensi" required 
                                    onchange="handleSelectJenisModal(this.value)"
                                    class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih Jenis Referensi</option>
                                
                                <optgroup label="📂 Opsi Formulir Dinamis">
                                    <?php foreach ($daftar_jenis as $j): ?>
                                        <?php 
                                            $cat = $map_metadata[$j]['kategori'] ?? 'fleksibel';
                                            $lbl = $map_metadata[$j]['label'] ?? $j;
                                            if ($cat === 'fleksibel'):
                                        ?>
                                            <option value="<?= esc($j) ?>"><?= esc($lbl) ?> (<?= esc($j) ?>)</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>

                                <optgroup label="🔒 Parameter Sistem Inti">
                                    <?php foreach ($daftar_jenis as $j): ?>
                                        <?php 
                                            $cat = $map_metadata[$j]['kategori'] ?? 'fleksibel';
                                            $lbl = $map_metadata[$j]['label'] ?? $j;
                                            if ($cat === 'sistem'):
                                        ?>
                                            <option value="<?= esc($j) ?>"><?= esc($lbl) ?> (<?= esc($j) ?>)</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>

                                <option value="__NEW__" class="text-blue-600 font-bold">+ Input Jenis Baru...</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        <!-- Input Teks Tambahan jika memilih + Input Jenis Baru -->
                        <div id="wrapCustomJenis" class="hidden mt-2">
                            <input type="text" id="customJenisInput" placeholder="Ketik nama jenis referensi baru (misal: kre_sektor)..." 
                                   class="w-full text-xs font-semibold text-slate-800 border border-blue-300 rounded-xl px-3.5 py-2 bg-blue-50/30 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                        </div>
                    </div>

                    <!-- Kolom Kanan: 1. Keterangan Referensi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Keterangan Referensi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="modalKeteranganInput" name="keterangan_referensi" required 
                               placeholder="Contoh: PT, Surat Kuasa, Lancar..." 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Kolom Kiri: 2. Tipe Referensi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Tipe Referensi (Kode Teknis)
                        </label>
                        <input type="text" id="modalTipeInput" name="tipe_referensi" 
                               placeholder="Contoh: pt, movable_asset, 1..." 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs font-mono">
                    </div>

                    <!-- Kolom Kanan: 2. Color Referensi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Color Referensi (Badge Warna)
                        </label>
                        <input type="text" id="modalColorInput" name="color_referensi" list="colorSuggestions" 
                               placeholder="Pilih atau ketik warna badge..." 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        <datalist id="colorSuggestions">
                            <option value="primary">primary (Biru)</option>
                            <option value="info">info (Biru Muda / Sky)</option>
                            <option value="success">success (Hijau)</option>
                            <option value="warning">warning (Kuning / Oranye)</option>
                            <option value="danger">danger (Merah / Rose)</option>
                            <option value="secondary">secondary (Abu-abu)</option>
                        </datalist>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                    <button type="button" onclick="tutupModal()" 
                            class="px-5 py-2 bg-slate-500 hover:bg-slate-600 active:bg-slate-700 text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer">
                        Tutup
                    </button>
                    <button type="submit" id="btnSubmitModal" 
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-bold transition shadow-xs shadow-emerald-600/20 cursor-pointer">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Hidden Form for Delete (POST Method with CSRF) -->
    <form id="formHapusReferensi" method="post" action="" class="hidden">
        <?= csrf_field() ?>
    </form>

    <!-- JavaScript Application Logic -->
    <script>
        // State management
        let currentPage = 1;
        let perPage = 10;
        let currentFilterJenis = '<?= esc($selected_jenis) ?>';
        let currentSearchQuery = '';

        // Inisialisasi saat DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            terapkanFilterDanPaginasi();
        });

        // Buka Modal Tambah
        function bukaModalTambah() {
            document.getElementById('modalTitle').innerText = 'Tambah Setting Referensi';
            document.getElementById('modalSubtitle').innerText = 'Tambahkan opsi formulir atau referensi sistem baru';
            document.getElementById('modalLockedNotice').classList.add('hidden');
            document.getElementById('formReferensi').action = '<?= base_url('superadmin/referensi/simpan') ?>';
            document.getElementById('editReferensiId').value = '';
            
            // Set default select sesuai filter yang aktif jika ada
            const selectEl = document.getElementById('modalJenisSelect');
            selectEl.disabled = false;
            if (currentFilterJenis && currentFilterJenis !== 'Semua Jenis') {
                selectEl.value = currentFilterJenis;
            } else {
                selectEl.selectedIndex = 0;
            }

            document.getElementById('modalKeteranganInput').value = '';
            document.getElementById('modalTipeInput').value = '';
            document.getElementById('modalColorInput').value = '';
            document.getElementById('wrapCustomJenis').classList.add('hidden');
            document.getElementById('customJenisInput').value = '';

            document.getElementById('modalSettingReferensi').classList.remove('hidden');
        }

        // Buka Modal Edit
        function bukaModalEdit(id) {
            const row = document.querySelector(`.ref-row[data-id="${id}"]`);
            if (!row) return;

            const jenis = row.getAttribute('data-jenis');
            const ket = row.getAttribute('data-keterangan');
            const tipe = row.getAttribute('data-tipe');
            const color = row.getAttribute('data-color');
            const isLocked = parseInt(row.getAttribute('data-is-locked') || '0', 10);
            const labelText = row.getAttribute('data-label') || jenis;

            document.getElementById('modalTitle').innerText = 'Edit Setting Referensi';
            document.getElementById('modalSubtitle').innerText = `Mengubah data referensi untuk "${labelText}"`;
            document.getElementById('formReferensi').action = '<?= base_url('superadmin/referensi/update') ?>/' + id;
            document.getElementById('editReferensiId').value = id;

            if (isLocked === 1) {
                document.getElementById('modalLockedNotice').classList.remove('hidden');
            } else {
                document.getElementById('modalLockedNotice').classList.add('hidden');
            }

            const selectEl = document.getElementById('modalJenisSelect');
            selectEl.disabled = false;
            let found = false;
            for (let i = 0; i < selectEl.options.length; i++) {
                if (selectEl.options[i].value === jenis) {
                    selectEl.selectedIndex = i;
                    found = true;
                    break;
                }
            }

            if (!found) {
                const opt = new Option(`${jenis} (${jenis})`, jenis, true, true);
                selectEl.insertBefore(opt, selectEl.options[selectEl.options.length - 1]);
                selectEl.value = jenis;
            }

            document.getElementById('modalKeteranganInput').value = ket;
            document.getElementById('modalTipeInput').value = tipe;
            document.getElementById('modalColorInput').value = color;
            document.getElementById('wrapCustomJenis').classList.add('hidden');

            document.getElementById('modalSettingReferensi').classList.remove('hidden');
        }

        // Tutup Modal
        function tutupModal() {
            document.getElementById('modalSettingReferensi').classList.add('hidden');
        }

        // Handle Select Jenis Modal (Input Jenis Baru)
        function handleSelectJenisModal(val) {
            const wrap = document.getElementById('wrapCustomJenis');
            const input = document.getElementById('customJenisInput');
            if (val === '__NEW__') {
                wrap.classList.remove('hidden');
                input.focus();
            } else {
                wrap.classList.add('hidden');
            }
        }

        // Form Submit Interceptor jika user memilih jenis baru
        document.getElementById('formReferensi').addEventListener('submit', function(e) {
            const selectEl = document.getElementById('modalJenisSelect');
            if (selectEl.value === '__NEW__') {
                const customVal = document.getElementById('customJenisInput').value.trim();
                if (!customVal) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan ketik nama jenis referensi baru.',
                        confirmButtonColor: '#059669'
                    });
                    return;
                }
                const opt = new Option(customVal, customVal, true, true);
                selectEl.add(opt);
                selectEl.value = customVal;
            }
        });

        // Konfirmasi Hapus Data dengan SweetAlert2
        function konfirmasiHapus(id, keterangan) {
            Swal.fire({
                title: 'Hapus Referensi?',
                text: `Apakah Anda yakin ingin menghapus referensi "${keterangan}"? Data yang telah dihapus tidak dapat dipulihkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Data!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formHapusReferensi');
                    form.action = '<?= base_url('superadmin/referensi/hapus') ?>/' + id;
                    form.submit();
                }
            });
        }

        // Handle Filter Jenis Dropdown
        function handleFilterChange(val) {
            currentFilterJenis = val;
            currentPage = 1;
            terapkanFilterDanPaginasi();
        }

        // Handle Search Bar Input
        function handleSearchTable(val) {
            currentSearchQuery = val.toLowerCase().trim();
            currentPage = 1;
            terapkanFilterDanPaginasi();
        }

        // Ubah entries per page
        function ubahEntriesPerPage(val) {
            perPage = parseInt(val, 10);
            currentPage = 1;
            terapkanFilterDanPaginasi();
        }

        // Fungsi Filter Client-side + Paginasi
        function terapkanFilterDanPaginasi() {
            const rows = Array.from(document.querySelectorAll('.ref-row'));
            if (rows.length === 0) return;

            // 1. Filter rows
            const filteredRows = rows.filter(row => {
                const jenis = row.getAttribute('data-jenis');
                const keterangan = (row.getAttribute('data-keterangan') || '').toLowerCase();
                const tipe = (row.getAttribute('data-tipe') || '').toLowerCase();
                const color = (row.getAttribute('data-color') || '').toLowerCase();
                const label = (row.getAttribute('data-label') || '').toLowerCase();

                // Cek filter dropdown jenis
                const matchJenis = (!currentFilterJenis || currentFilterJenis === 'Semua Jenis' || currentFilterJenis === '') ? true : (jenis === currentFilterJenis);

                // Cek teks pencarian
                const matchSearch = (!currentSearchQuery) ? true : (
                    jenis.toLowerCase().includes(currentSearchQuery) ||
                    label.includes(currentSearchQuery) ||
                    keterangan.includes(currentSearchQuery) ||
                    tipe.includes(currentSearchQuery) ||
                    color.includes(currentSearchQuery)
                );

                return matchJenis && matchSearch;
            });

            // 2. Hide all rows first
            rows.forEach(r => r.style.display = 'none');

            // 3. Paginasi slicing
            const total = filteredRows.length;
            const totalPages = Math.ceil(total / perPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * perPage;
            const endIndex = Math.min(startIndex + perPage, total);

            for (let i = startIndex; i < endIndex; i++) {
                filteredRows[i].style.display = '';
                const noEl = filteredRows[i].querySelector('.row-no');
                if (noEl) noEl.innerText = (i + 1);
            }

            // Update Info Footer
            const infoShowing = document.getElementById('infoShowingCount');
            const infoTotal = document.getElementById('infoTotalCount');
            if (infoShowing) infoShowing.innerText = total === 0 ? 0 : `${startIndex + 1} - ${endIndex}`;
            if (infoTotal) infoTotal.innerText = total;

            // Update Tombol Navigasi Paginasi
            renderPaginationControls(totalPages);
        }

        // Render Pagination Buttons
        function renderPaginationControls(totalPages) {
            const container = document.getElementById('paginationControls');
            if (!container) return;
            container.innerHTML = '';

            if (totalPages <= 1) return;

            // Tombol Prev
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.innerText = 'Sebelumnya';
            prevBtn.disabled = (currentPage === 1);
            prevBtn.className = `px-2.5 py-1 text-xs rounded-md border border-slate-200 font-semibold transition ${currentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700 cursor-pointer'}`;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    terapkanFilterDanPaginasi();
                }
            };
            container.appendChild(prevBtn);

            // Tombol Nomor Halaman
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.innerText = i;
                    pageBtn.className = `w-7 h-7 text-xs rounded-md font-bold transition ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 cursor-pointer'}`;
                    pageBtn.onclick = () => {
                        currentPage = i;
                        terapkanFilterDanPaginasi();
                    };
                    container.appendChild(pageBtn);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    const dots = document.createElement('span');
                    dots.className = 'px-1 text-slate-400';
                    dots.innerText = '...';
                    container.appendChild(dots);
                }
            }

            // Tombol Next
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.innerText = 'Selanjutnya';
            nextBtn.disabled = (currentPage === totalPages);
            nextBtn.className = `px-2.5 py-1 text-xs rounded-md border border-slate-200 font-semibold transition ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-white hover:bg-slate-50 text-slate-700 cursor-pointer'}`;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    terapkanFilterDanPaginasi();
                }
            };
            container.appendChild(nextBtn);
        }

        // Toggle Fullscreen
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                }
            }
        }
    </script>
</body>
</html>
