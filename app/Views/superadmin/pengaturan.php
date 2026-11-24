<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pengaturan Sistem & Lembaga | OTW Kredit') ?></title>
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
    </style>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
    <link rel="stylesheet" href="<?= base_url('css/otw-portal.css?v=' . (@filemtime(FCPATH . 'css/otw-portal.css') ?: 1)) ?>">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'pengaturan',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full Width Layout md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen w-full">

        <!-- Mobile Header Bar Responsif -->
        <header class="md:hidden bg-white border-b border-slate-200 px-3.5 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between gap-4 sm:gap-6 sticky top-0 z-20 w-full min-w-0">
            <div class="flex items-center gap-2.5 min-w-0 flex-1 mr-2 sm:mr-3">
                <button type="button" onclick="toggleSidebar()" class="p-1.5 sm:p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 shrink-0 cursor-pointer" aria-label="Toggle Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2 min-w-0 flex-1 pr-1.5 sm:pr-2">
                    <?php 
                        $cfgHeader = !empty($pengaturan_sistem) ? $pengaturan_sistem : (\Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? []);
                        $appHeaderLogo = !empty($cfgHeader['logo_aplikasi']) ? $cfgHeader['logo_aplikasi'] : '';
                        $appHeaderName = !empty($cfgHeader['nama_aplikasi']) ? $cfgHeader['nama_aplikasi'] : 'OTW Kredit';
                    ?>
                    <?php if (!empty($appHeaderLogo)): ?>
                        <img src="<?= base_url($appHeaderLogo) ?>" class="w-6 h-6 object-contain rounded shrink-0" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-xs sm:text-sm truncate whitespace-nowrap min-w-0 block"><?= esc($appHeaderName) ?></span>
                </div>
            </div>
            <div class="flex items-center shrink-0">
                <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200 hover:bg-rose-100 transition shrink-0 whitespace-nowrap">
                    Logout
                </a>
            </div>
        </header>

        <!-- Desktop Topbar Full Width -->
        <div class="bg-white border-b border-slate-200/80 px-6 lg:px-8 py-3.5 hidden md:flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Super Administrator</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Pengaturan Lembaga</span>
            </div>
        </div>

        <!-- Main Content Full Width -->
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

            <!-- FORM PENGATURAN IDENTITAS & KONTAK -->
            <form id="formPengaturan" action="<?= base_url('dashboard/update-pengaturan') ?>" method="POST" enctype="multipart/form-data" class="space-y-6 w-full">
                
                <!-- CARD 1: IDENTITAS APLIKASI & LEMBAGA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/80 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Identitas Aplikasi &amp; Lembaga</h2>
                                <p class="text-[11px] text-slate-400">Nama sistem, slogan navbar, dan identitas instansi</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200 shrink-0">Identitas Utama</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Nama Sistem / Aplikasi -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Aplikasi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_aplikasi" 
                                   id="inputNamaAplikasi"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Kredit') ?>" 
                                   class="w-full text-xs font-bold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Tampil pada logo navbar dan cetak dokumen.</span>
                        </div>

                        <!-- Slogan Aplikasi Navbar -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Slogan Navbar <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="slogan_aplikasi" 
                                   id="inputSloganAplikasi"
                                   value="<?= esc($pengaturan_sistem['slogan_aplikasi'] ?? 'Sistem Analisa Kredit') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Sub-teks di bawah nama aplikasi.</span>
                        </div>

                        <!-- Nama Lembaga -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Lembaga <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_lembaga" 
                                   id="inputNamaLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['nama_lembaga'] ?? 'PT Lembaga Pembiayaan') ?>" 
                                   class="w-full text-xs font-bold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Nama resmi pada kop surat dokumen.</span>
                        </div>

                        <!-- Logo Lembaga -->
                        <div class="md:col-span-3 space-y-2 pt-3 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Logo Aplikasi &amp; Kop Dokumen
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center gap-3 shrink-0">
                                    <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                                        <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" alt="Logo Saat Ini" class="w-14 h-14 object-contain rounded-xl border border-slate-200 p-1 bg-white shadow-2xs">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-[10px] shrink-0 bg-white shadow-2xs">
                                            <svg class="w-5 h-5 text-blue-500 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span class="font-bold">Default</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Logo Aktif</span>
                                        <span class="text-[11px] text-slate-400 block">Tampil di navbar dan kop surat.</span>
                                        <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                                            <label class="inline-flex items-center gap-1.5 mt-1 cursor-pointer text-[11px] text-rose-600 font-semibold hover:text-rose-800">
                                                <input type="checkbox" name="hapus_logo" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                                <span>Hapus Logo (Gunakan Ikon Bawaan)</span>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 sm:pl-4 pt-3 sm:pt-0">
                                    <input type="file" 
                                           name="logo_file" 
                                           accept="image/png, image/jpeg, image/svg+xml"
                                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl p-1 bg-white">
                                    <span class="text-[11px] text-slate-400 mt-1 block">Format: PNG, JPG, atau SVG (Maks. 2MB). Disarankan rasio kotak / transparan.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: KONTAK & ALAMAT LEMBAGA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Kontak &amp; Alamat Lembaga</h2>
                                <p class="text-[11px] text-slate-400">Dicantumkan pada kop surat dan dokumen resmi</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">Kop Surat</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Alamat Lembaga -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Alamat Kantor Pusat <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="alamat_lembaga" 
                                   id="inputAlamatLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['alamat_lembaga'] ?? 'Financial Center, Lantai 8, Jakarta Selatan') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   placeholder="Contoh: Financial Center, Lantai 8, Jakarta Selatan" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Alamat lengkap kantor untuk kop surat dokumen kredit.</span>
                        </div>

                        <!-- Email Resmi -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Email Resmi <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email_lembaga" 
                                   id="inputEmailLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['email_lembaga'] ?? 'info@otwkredit.id') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   placeholder="info@otwkredit.id" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Email instansi untuk korespondensi resmi.</span>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nomor Telepon <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="telepon_lembaga" 
                                   id="inputTeleponLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['telepon_lembaga'] ?? '(021) 5088-2900') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-2.5 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   placeholder="(021) 5088-2900" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Nomor telepon kantor operasional.</span>
                        </div>
                    </div>

                    <!-- PREVIEW KOP SURAT -->
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                            Pratinjau Kop Surat Resmi:
                        </span>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="font-extrabold text-sm sm:text-base text-slate-900 tracking-wide uppercase" id="previewNamaLembaga">
                                <?= esc($pengaturan_sistem['nama_lembaga'] ?? 'PT Lembaga Pembiayaan') ?>
                            </div>
                            <div class="text-xs text-slate-600 mt-0.5" id="previewAlamatLembaga">
                                <?= esc($pengaturan_sistem['alamat_lembaga'] ?? 'Financial Center, Lantai 8, Jakarta Selatan') ?>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Telp: <span id="previewTeleponLembaga" class="font-medium text-slate-600"><?= esc($pengaturan_sistem['telepon_lembaga'] ?? '(021) 5088-2900') ?></span> • 
                                Email: <span id="previewEmailLembaga" class="font-medium text-slate-600"><?= esc($pengaturan_sistem['email_lembaga'] ?? 'info@otwkredit.id') ?></span>
                            </div>
                            <div class="border-b-2 border-slate-800 mt-2.5 mb-0.5"></div>
                            <div class="border-b border-slate-400"></div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: ALUR VERIFIKASI SLIK OJK -->
                <?php 
                    $isPetugasSlikAktif = (int)($pengaturan_sistem['petugas_slik_aktif'] ?? (($pengaturan_sistem['ijin_pengusul_isi_slik'] ?? 1) == 0 ? 1 : 0));
                ?>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200/80 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Alur Verifikasi SLIK OJK</h2>
                                <p class="text-[11px] text-slate-400">Pemisahan tugas verifikator SLIK</p>
                            </div>
                        </div>
                        <span id="badgeStatusSlikSetting" class="text-[11px] font-bold px-2.5 py-1 rounded-md border <?= $isPetugasSlikAktif === 1 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-600 bg-slate-100 border-slate-200' ?>">
                            <?= $isPetugasSlikAktif === 1 ? '🟢 Petugas SLIK Aktif' : '⚪ Petugas SLIK Nonaktif' ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-4 p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80">
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-900 block">Pemisahan Petugas SLIK</span>
                            <p id="deskripsiSlikSetting" class="text-[11px] text-slate-500 mt-0.5">
                                <?= $isPetugasSlikAktif === 1 
                                    ? 'Hasil SLIK diinput khusus oleh Petugas SLIK.' 
                                    : 'Pengusul dapat langsung menginput hasil SLIK.' 
                                ?>
                            </p>
                        </div>

                        <!-- Toggle Switch Button -->
                        <div class="shrink-0 flex flex-col items-end gap-1">
                            <input type="hidden" name="petugas_slik_aktif" id="inputPetugasSlikAktif" value="<?= $isPetugasSlikAktif ?>">
                            <button type="button" 
                                    id="btnTogglePetugasSlik" 
                                    onclick="toggleSettingPetugasSlik()"
                                    class="relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none <?= $isPetugasSlikAktif === 1 ? 'bg-blue-600' : 'bg-slate-300' ?>" 
                                    role="switch" 
                                    aria-checked="<?= $isPetugasSlikAktif === 1 ? 'true' : 'false' ?>">
                                <span class="sr-only">Aktifkan Petugas SLIK</span>
                                <span id="toggleKnobPetugasSlik" 
                                      class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out <?= $isPetugasSlikAktif === 1 ? 'translate-x-7' : 'translate-x-0' ?>">
                                </span>
                            </button>
                            <span id="toggleTextPetugasSlik" class="text-[10px] font-bold text-slate-600">
                                <?= $isPetugasSlikAktif === 1 ? 'AKTIF' : 'NONAKTIF' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: FITUR & MODUL SISTEM -->
                <?php
                    $ffList = [
                        ['key' => 'fitur_ai_aktif', 'icon' => '🤖', 'judul' => 'AI 5C & Cek Rekam Jejak', 'desc' => 'Analisis risiko otomatis.', 'def' => 1],
                        ['key' => 'fitur_badan_usaha_aktif', 'icon' => '🏢', 'judul' => 'Pembiayaan Badan Usaha', 'desc' => 'Pengajuan PT dan CV.', 'def' => 1],
                        ['key' => 'fitur_disbursement_aktif', 'icon' => '💰', 'judul' => 'Pencairan & Angsuran', 'desc' => 'Simulasi biaya pencairan.', 'def' => 1],
                        ['key' => 'mode_strict_dokumen', 'icon' => '📁', 'judul' => 'Dokumen Wajib', 'desc' => 'Wajib unggah berkas.', 'def' => 0],
                    ];
                    $modeKomite = $pengaturan_sistem['mode_approval_komite'] ?? 'cepat';
                ?>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-700 border border-violet-200/80 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Fitur &amp; Modul Sistem</h2>
                            <p class="text-[11px] text-slate-400">Aktifkan atau nonaktifkan fitur sistem</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <?php foreach ($ffList as $ff): $on = (int)($pengaturan_sistem[$ff['key']] ?? $ff['def']) === 1; ?>
                            <div class="flex items-center justify-between gap-4 p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80">
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-900"><?= $ff['icon'] ?> <?= esc($ff['judul']) ?></div>
                                    <p class="text-[11px] text-slate-500 mt-0.5"><?= esc($ff['desc']) ?></p>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-1">
                                    <button type="button" data-feature="<?= $ff['key'] ?>" data-on="<?= $on ? 1 : 0 ?>" onclick="toggleFitur(this)"
                                            class="ff-switch relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 <?= $on ? 'bg-blue-600' : 'bg-slate-300' ?>" role="switch" aria-checked="<?= $on ? 'true' : 'false' ?>">
                                        <span class="ff-knob pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition duration-200 <?= $on ? 'translate-x-7' : 'translate-x-0' ?>"></span>
                                    </button>
                                    <span class="ff-text text-[10px] font-bold text-slate-600"><?= $on ? 'AKTIF' : 'NONAKTIF' ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Mode Persetujuan Komite -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-900">⚖️ Persetujuan Komite</div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Alur persetujuan pengajuan kredit.</p>
                            </div>
                            <select id="selectModeKomite" onchange="setModeKomite(this.value)" class="shrink-0 px-3 py-1.5 text-xs font-bold text-slate-900 bg-white border border-slate-200 rounded-xl cursor-pointer">
                                <option value="cepat" <?= $modeKomite === 'cepat' ? 'selected' : '' ?>>Cepat (Siapa Cepat)</option>
                                <option value="bertingkat" <?= $modeKomite === 'bertingkat' ? 'selected' : '' ?>>Bertingkat (Reviewer → CEO)</option>
                            </select>
                        </div>
                    </div>

                    <p class="mt-3 text-[11px] text-slate-500 bg-slate-50 border border-slate-200/80 rounded-xl px-3 py-2">
                        ℹ️ Pengaturan tersimpan otomatis saat saklar diubah.
                    </p>
                </div>

                <!-- CARD 5: BACKUP & ARSIP DATA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Backup &amp; Arsip Data</h2>
                            <p class="text-[11px] text-slate-400">Unduh salinan cadangan data sistem secara berkala</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-2">
                            <div class="text-xs font-bold text-slate-900">🗄️ Backup Database (.sql)</div>
                            <p class="text-[11px] text-slate-500">Seluruh data pengajuan, pengguna, pengaturan, dan log.</p>
                            <a href="<?= base_url('superadmin/backup-database') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                                <span>⬇️</span> Unduh Database
                            </a>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-2">
                            <div class="text-xs font-bold text-slate-900">📦 Backup Berkas Upload (.zip)</div>
                            <p class="text-[11px] text-slate-500">Semua dokumen, berkas persyaratan, dan foto pengajuan.</p>
                            <a href="<?= base_url('superadmin/backup-berkas') ?>" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                                <span>⬇️</span> Unduh Berkas
                            </a>
                        </div>
                    </div>
                </div>

                <!-- CARD 6: KONFIGURASI MODEL AI -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-2xs w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200/80 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Konfigurasi Model AI</h2>
                                <p class="text-[11px] text-slate-400">Pilih engine model AI dan kredensial akses analisis kredit</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="tesKoneksiAiSekarang()" id="btnTesKoneksiAi" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>Tes Koneksi API</span>
                            </button>
                            <span id="badgeStatusGateway" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Terpusat
                            </span>
                        </div>
                    </div>

                    <?php
                        $currentModel = $pengaturan_sistem['model_ai'] ?? 'gemini-2.5-flash';
                        $currentEndpoint = $pengaturan_sistem['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models';
                        $currentKey = $pengaturan_sistem['api_key'] ?? '';
                    ?>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Col 1: Model AI -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Model AI <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Engine</span>
                            </div>
                            <div class="relative">
                                <select name="model_ai" id="selectModelAi" onchange="sesuaikanEndpointModelAi(this.value)" class="w-full px-3 py-2.5 text-xs font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition appearance-none pr-8 cursor-pointer shadow-2xs">
                                    <option value="gemini-2.5-flash" <?= $currentModel === 'gemini-2.5-flash' ? 'selected' : '' ?>>Google Gemini 2.5 Flash (Rekomendasi)</option>
                                    <option value="gemini-2.5-flash-lite" <?= $currentModel === 'gemini-2.5-flash-lite' ? 'selected' : '' ?>>Google Gemini 2.5 Flash Lite</option>
                                    <option value="gemini-2.0-flash" <?= $currentModel === 'gemini-2.0-flash' ? 'selected' : '' ?>>Google Gemini 2.0 Flash</option>
                                    <option value="deepseek-chat" <?= $currentModel === 'deepseek-chat' ? 'selected' : '' ?>>DeepSeek Chat (V3)</option>
                                    <option value="deepseek-reasoner" <?= $currentModel === 'deepseek-reasoner' ? 'selected' : '' ?>>DeepSeek Reasoner (R1)</option>
                                    <option value="gpt-4o" <?= $currentModel === 'gpt-4o' ? 'selected' : '' ?>>OpenAI GPT-4o</option>
                                    <option value="gpt-4o-mini" <?= $currentModel === 'gpt-4o-mini' ? 'selected' : '' ?>>OpenAI GPT-4o Mini</option>
                                    <option value="claude-3-5-sonnet" <?= $currentModel === 'claude-3-5-sonnet' ? 'selected' : '' ?>>Anthropic Claude 3.5 Sonnet</option>
                                    <option value="custom" <?= !in_array($currentModel, ['gemini-2.5-flash','gemini-2.5-flash-lite','gemini-2.0-flash','deepseek-chat','deepseek-reasoner','gpt-4o','gpt-4o-mini','claude-3-5-sonnet']) ? 'selected' : '' ?>>Model Kustom / Self-Hosted</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400">Model utama untuk analisis 5C otomatis.</p>
                        </div>

                        <!-- Col 2: Endpoint Gateway -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Endpoint Gateway
                                </label>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Opsional</span>
                            </div>
                            <div class="relative">
                                <input type="url" 
                                       name="api_endpoint"
                                       id="inputApiEndpoint" 
                                       value="<?= esc($currentEndpoint) ?>" 
                                       placeholder="Biarkan kosong untuk default..." 
                                       class="w-full px-3 py-2.5 text-xs font-mono text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                            </div>
                            <p class="text-[11px] text-slate-400">Gunakan jika memakai server proxy / relay API.</p>
                        </div>

                        <!-- Col 3: API Key -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    API Key / Token
                                </label>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Tersimpan</span>
                            </div>
                            <div class="relative">
                                <input type="password" 
                                       name="api_key"
                                       id="inputApiKey" 
                                       value="<?= esc($currentKey) ?>" 
                                       placeholder="Masukkan API Key..." 
                                       class="w-full pl-3 pr-10 py-2.5 text-xs font-mono text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                                <button type="button" 
                                        onclick="toggleApiKeyVisibility()" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer" 
                                        title="Tampilkan / Sembunyikan Key">
                                    <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-[11px] text-slate-400">Kunci otentikasi API yang terenkripsi.</p>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM ACTION BAR -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs w-full">
                    <button type="button" 
                            onclick="resetFormDefault()" 
                            class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer shadow-2xs">
                        Kembalikan Default
                    </button>
                    
                    <button type="submit" 
                            id="btnSimpanPengaturan" 
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20 cursor-pointer">
                        <span>💾</span>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>

            </form>

        </main>
    </div>

    <!-- Script Interaktif -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updatePreviewKop();
        });

        function sesuaikanEndpointModelAi(model) {
            const epInput = document.getElementById('inputApiEndpoint');
            if (!epInput) return;
            const m = String(model || '').toLowerCase();
            if (m.startsWith('deepseek')) {
                epInput.value = 'https://api.deepseek.com/chat/completions';
            } else if (m.startsWith('gemini')) {
                epInput.value = 'https://generativelanguage.googleapis.com/v1beta/models';
            } else if (m.startsWith('gpt-')) {
                epInput.value = 'https://api.openai.com/v1/chat/completions';
            } else if (m.startsWith('claude')) {
                epInput.value = 'https://api.anthropic.com/v1/messages';
            }
        }

        async function tesKoneksiAiSekarang() {
            const btn = document.getElementById('btnTesKoneksiAi');
            const model = document.getElementById('selectModelAi')?.value || '';
            const endpoint = document.getElementById('inputApiEndpoint')?.value || '';
            const apiKey = document.getElementById('inputApiKey')?.value || '';

            if (!apiKey.trim()) {
                Swal.fire({ icon: 'warning', title: 'API Key Kosong', text: 'Masukkan API Key terlebih dahulu sebelum mengetes koneksi.' });
                return;
            }

            if (btn) btn.disabled = true;
            Swal.fire({
                title: 'Mengetes Koneksi API...',
                text: 'Menghubungi ' + model + ' menggunakan konfigurasi di atas...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const fd = new FormData();
                fd.append('model_ai', model);
                fd.append('api_endpoint', endpoint);
                fd.append('api_key', apiKey);
                const r = await fetch('<?= base_url('superadmin/tes-koneksi-ai') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                const res = await r.json();
                Swal.fire({
                    icon: res.status ? 'success' : 'error',
                    title: res.status ? 'Koneksi API Berhasil' : 'Koneksi API Gagal',
                    text: res.message || 'Tidak ada respons.',
                    confirmButtonColor: '#1D4ED8'
                });
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Gagal Menguji Koneksi', text: 'Terjadi kesalahan jaringan ke server.' });
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        function updatePreviewKop() {
            const nama = document.getElementById('inputNamaLembaga').value || 'PT Lembaga Pembiayaan';
            const alamat = document.getElementById('inputAlamatLembaga').value || 'Financial Center, Lantai 8, Jakarta Selatan';
            const telp = document.getElementById('inputTeleponLembaga').value || '(021) 5088-2900';
            const email = document.getElementById('inputEmailLembaga').value || 'info@otwkredit.id';

            document.getElementById('previewNamaLembaga').innerText = nama;
            document.getElementById('previewAlamatLembaga').innerText = alamat;
            document.getElementById('previewTeleponLembaga').innerText = telp;
            document.getElementById('previewEmailLembaga').innerText = email;
        }

        function resetFormDefault() {
            Swal.fire({
                title: 'Reset Identitas & Kontak?',
                text: 'Kembalikan data nama aplikasi, lembaga, dan kontak ke format bawaan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    if (document.getElementById('inputNamaAplikasi')) document.getElementById('inputNamaAplikasi').value = 'OTW Kredit';
                    if (document.getElementById('inputSloganAplikasi')) document.getElementById('inputSloganAplikasi').value = 'Sistem Analisa Kredit';
                    if (document.getElementById('inputNamaLembaga')) document.getElementById('inputNamaLembaga').value = 'PT Lembaga Pembiayaan';
                    if (document.getElementById('inputAlamatLembaga')) document.getElementById('inputAlamatLembaga').value = 'Financial Center, Lantai 8, Jakarta Selatan';
                    if (document.getElementById('inputEmailLembaga')) document.getElementById('inputEmailLembaga').value = 'info@otwkredit.id';
                    if (document.getElementById('inputTeleponLembaga')) document.getElementById('inputTeleponLembaga').value = '(021) 5088-2900';
                    updatePreviewKop();
                    Swal.fire({
                        icon: 'success',
                        title: 'Formulir Direset',
                        text: 'Silakan klik Simpan Pengaturan untuk menerapkan perubahan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function updateUiPetugasSlik(isAktif) {
            const btn = document.getElementById('btnTogglePetugasSlik');
            const knob = document.getElementById('toggleKnobPetugasSlik');
            const badge = document.getElementById('badgeStatusSlikSetting');
            const text = document.getElementById('toggleTextPetugasSlik');
            const desc = document.getElementById('deskripsiSlikSetting');
            const input = document.getElementById('inputPetugasSlikAktif');

            if (input) input.value = isAktif ? '1' : '0';

            if (isAktif) {
                if (btn) {
                    btn.className = 'relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-blue-600';
                    btn.setAttribute('aria-checked', 'true');
                }
                if (knob) {
                    knob.className = 'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-7';
                }
                if (badge) {
                    badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-md border text-emerald-700 bg-emerald-50 border-emerald-200';
                    badge.innerText = '🟢 Petugas SLIK Aktif';
                }
                if (text) text.innerText = 'AKTIF';
                if (desc) {
                    desc.innerText = 'Hasil SLIK diinput khusus oleh Petugas SLIK.';
                }
            } else {
                if (btn) {
                    btn.className = 'relative inline-flex h-7 w-14 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-slate-300';
                    btn.setAttribute('aria-checked', 'false');
                }
                if (knob) {
                    knob.className = 'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-0';
                }
                if (badge) {
                    badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-md border text-slate-600 bg-slate-100 border-slate-200';
                    badge.innerText = '⚪ Petugas SLIK Nonaktif';
                }
                if (text) text.innerText = 'NONAKTIF';
                if (desc) {
                    desc.innerText = 'Pengusul dapat langsung menginput hasil SLIK.';
                }
            }
        }

        function toggleSettingPetugasSlik() {
            const input = document.getElementById('inputPetugasSlikAktif');
            const current = parseInt(input ? input.value : 0) || 0;
            const next = current === 1 ? 0 : 1;

            // Update UI langsung untuk respon cepat
            updateUiPetugasSlik(next === 1);

            // Simpan status langsung via AJAX ke server
            fetch('<?= base_url('superadmin/toggle-petugas-slik') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    const isAktif = (parseInt(data.petugas_slik_aktif) === 1);
                    updateUiPetugasSlik(isAktif);
                    Swal.fire({
                        icon: isAktif ? 'success' : 'info',
                        title: isAktif ? 'Petugas SLIK Diaktifkan' : 'Petugas SLIK Dinonaktifkan',
                        text: data.message || 'Status alur verifikasi SLIK OJK berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Rollback ke state semula jika gagal
                    updateUiPetugasSlik(current === 1);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengubah Pengaturan',
                        text: data.message || 'Terjadi kesalahan sistem.'
                    });
                }
            })
            .catch(err => {
                console.error(err);
                // Rollback ke state semula jika koneksi terganggu
                updateUiPetugasSlik(current === 1);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat menghubungi server.'
                });
            });
        }

        function kirimToggleFitur(feature, value, onOk, onFail) {
            const body = new URLSearchParams({ feature: feature, value: value });
            fetch('<?= base_url('superadmin/toggle-fitur') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            })
            .then(r => r.json())
            .then(d => {
                if (d.status) {
                    onOk(d);
                    Swal.fire({ icon: 'success', title: 'Tersimpan', text: d.message, timer: 1600, showConfirmButton: false });
                } else {
                    onFail();
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan.' });
                }
            })
            .catch(() => {
                onFail();
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Tidak dapat menghubungi server.' });
            });
        }

        function renderSwitch(btn, on) {
            btn.dataset.on = on ? 1 : 0;
            btn.setAttribute('aria-checked', on ? 'true' : 'false');
            btn.classList.toggle('bg-blue-600', on);
            btn.classList.toggle('bg-slate-300', !on);
            const knob = btn.querySelector('.ff-knob');
            knob.classList.toggle('translate-x-7', on);
            knob.classList.toggle('translate-x-0', !on);
            const txt = btn.parentElement.querySelector('.ff-text');
            if (txt) txt.innerText = on ? 'AKTIF' : 'NONAKTIF';
        }

        function toggleFitur(btn) {
            const cur = parseInt(btn.dataset.on) === 1;
            renderSwitch(btn, !cur);
            kirimToggleFitur(btn.dataset.feature, cur ? 0 : 1, () => {}, () => renderSwitch(btn, cur));
        }

        function setModeKomite(val) {
            kirimToggleFitur('mode_approval_komite', val, () => {}, () => { window.location.reload(); });
        }

        function toggleApiKeyVisibility() {
            const input = document.getElementById('inputApiKey');
            const eyeIcon = document.getElementById('eyeIcon');
            if (!input || !eyeIcon) return;
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>
</body>
</html>
