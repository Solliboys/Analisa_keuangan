<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pengaturan Sistem & Lembaga | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
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
        'active_menu'  => 'pengaturan',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Full Width Layout md:pl-64) -->
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
                
                <!-- CARD 1: IDENTITAS SISTEM & LEMBAGA -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                                🏢
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Identitas Aplikasi &amp; Lembaga Perbankan / BPR
                                </h2>
                                <p class="text-[11px] text-slate-400">Nama brand sistem, slogan navbar, dan instansi resmi</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200">Identitas Utama</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Nama Sistem / Aplikasi -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Nama Sistem / Aplikasi</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_aplikasi" 
                                   id="inputNamaAplikasi"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Kredit') ?>" 
                                   class="w-full text-xs font-bold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Tampil pada logo navbar atas dan header cetak.</span>
                        </div>

                        <!-- Slogan / Keterangan Aplikasi Navbar -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Slogan / Keterangan Navbar</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="slogan_aplikasi" 
                                   id="inputSloganAplikasi"
                                   value="<?= esc($pengaturan_sistem['slogan_aplikasi'] ?? 'Sistem Analisa Kredit') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Teks kecil di bawah nama aplikasi pada sidebar/navbar.</span>
                        </div>

                        <!-- Nama Lembaga / Koperasi / BPR -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Nama Lembaga / Koperasi / BPR</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nama_lembaga" 
                                   id="inputNamaLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['nama_lembaga'] ?? 'PT BPR / Lembaga Pembiayaan') ?>" 
                                   class="w-full text-xs font-bold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Nama instansi pada kop surat memorandum CAM.</span>
                        </div>

                        <!-- Logo Lembaga & Navbar -->
                        <div class="md:col-span-3 space-y-2 pt-4 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Logo Aplikasi &amp; Lembaga (Upload Gambar Baru)
                            </label>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-5 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center gap-3 shrink-0">
                                    <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                                        <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" alt="Logo Saat Ini" class="w-16 h-16 object-contain rounded-xl border border-slate-200 p-1.5 bg-white shadow-2xs">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-xl border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-[10px] shrink-0 bg-white shadow-2xs">
                                            <svg class="w-6 h-6 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            <span class="font-bold">Icon Default</span>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Logo Aktif (Navbar &amp; CAM)</span>
                                        <span class="text-[11px] text-slate-400 block">Otomatis tampil di pojok kiri atas navbar dan kop surat.</span>
                                        <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                                            <label class="inline-flex items-center gap-1.5 mt-1.5 cursor-pointer text-[11px] text-rose-600 font-semibold hover:text-rose-800">
                                                <input type="checkbox" name="hapus_logo" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                                <span>Hapus Logo (Kembalikan ke Icon Bawaan)</span>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex-1 border-t sm:border-t-0 sm:border-l border-slate-200 sm:pl-5 pt-3 sm:pt-0">
                                    <input type="file" 
                                           name="logo_file" 
                                           accept="image/png, image/jpeg, image/svg+xml"
                                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl p-1 bg-white">
                                    <span class="text-[11px] text-slate-400 mt-1 block">Format didukung: PNG, JPG, atau SVG (Maksimal 2MB, rasio persegi/transparan disarankan). <b>Otomatis dikompresi & dioptimasi sistem</b>.</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- CARD 2: INFORMASI KONTAK RESMI LEMBAGA (KOP SURAT CAM) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                📞
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Informasi Kontak &amp; Alamat Kantor Resmi
                                </h2>
                                <p class="text-[11px] text-slate-400">Dicantumkan pada kop surat memorandum CAM dan footer dokumen</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">Kop Surat CAM</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Alamat Lembaga -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Alamat Kantor Pusat / Lembaga</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="alamat_lembaga" 
                                   id="inputAlamatLembaga"
                                   oninput="updatePreviewKop()"
                                   value="<?= esc($pengaturan_sistem['alamat_lembaga'] ?? 'BPR Financial Center, Lantai 8, Jakarta Selatan') ?>" 
                                   class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                   placeholder="Contoh: BPR Financial Center, Lantai 8, Jakarta Selatan" 
                                   required />
                            <span class="text-[11px] text-slate-400 block">Alamat lengkap instansi pada kop surat memorandum analisis kredit CAM.</span>
                        </div>

                        <!-- Email Resmi -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Email Resmi Lembaga</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       name="email_lembaga" 
                                       id="inputEmailLembaga"
                                       oninput="updatePreviewKop()"
                                       value="<?= esc($pengaturan_sistem['email_lembaga'] ?? 'info@otwkredit.id') ?>" 
                                       class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                       placeholder="info@otwkredit.id" 
                                       required />
                            </div>
                            <span class="text-[11px] text-slate-400 block">Email instansi untuk korespondensi resmi.</span>
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1">
                                <span>Nomor Telepon / Call Center</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="telepon_lembaga" 
                                       id="inputTeleponLembaga"
                                       oninput="updatePreviewKop()"
                                       value="<?= esc($pengaturan_sistem['telepon_lembaga'] ?? '(021) 5088-2900') ?>" 
                                       class="w-full text-xs font-semibold border border-slate-200 rounded-xl p-3 focus:border-blue-500 focus:outline-none transition shadow-2xs text-slate-900 bg-white" 
                                       placeholder="(021) 5088-2900" 
                                       required />
                            </div>
                            <span class="text-[11px] text-slate-400 block">Nomor telepon kantor operasional perbankan/koperasi.</span>
                        </div>

                    </div>

                    <!-- PREVIEW MINI KOP SURAT MEMORANDUM CAM -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2.5">
                            Pratinjau Kop Surat Resmi Dokumen CAM:
                        </span>
                        <div class="p-4 sm:p-5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <div class="font-extrabold text-sm sm:text-base text-slate-900 tracking-wide uppercase" id="previewNamaLembaga">
                                <?= esc($pengaturan_sistem['nama_lembaga'] ?? 'PT BPR / Lembaga Pembiayaan') ?>
                            </div>
                            <div class="text-xs text-slate-600 mt-1" id="previewAlamatLembaga">
                                <?= esc($pengaturan_sistem['alamat_lembaga'] ?? 'BPR Financial Center, Lantai 8, Jakarta Selatan') ?>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                Telp: <span id="previewTeleponLembaga" class="font-medium text-slate-600"><?= esc($pengaturan_sistem['telepon_lembaga'] ?? '(021) 5088-2900') ?></span> • 
                                Email: <span id="previewEmailLembaga" class="font-medium text-slate-600"><?= esc($pengaturan_sistem['email_lembaga'] ?? 'info@otwkredit.id') ?></span>
                            </div>
                            <div class="border-b-2 border-slate-800 mt-3 mb-1"></div>
                            <div class="border-b border-slate-400"></div>
                        </div>
                    </div>

                </div>

                <!-- CARD 3: WORKFLOW & OTORITAS INPUT SLIK OJK (PETUGAS SLIK) -->
                <?php 
                    $isPetugasSlikAktif = (int)($pengaturan_sistem['petugas_slik_aktif'] ?? (($pengaturan_sistem['ijin_pengusul_isi_slik'] ?? 1) == 0 ? 1 : 0));
                ?>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-2xs w-full">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                🛡️
                            </div>
                            <div>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Alur Kerja &amp; Fitur Petugas SLIK OJK
                                </h2>
                                <p class="text-[11px] text-slate-400">Pengaturan pemisahan tugas pengusul kredit vs petugas verifikator SLIK iDeb resmi</p>
                            </div>
                        </div>
                        <span id="badgeStatusSlikSetting" class="text-[11px] font-bold px-2.5 py-1 rounded-md border <?= $isPetugasSlikAktif === 1 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-600 bg-slate-100 border-slate-200' ?>">
                            <?= $isPetugasSlikAktif === 1 ? '🟢 Petugas SLIK Aktif' : '⚪ Petugas SLIK Nonaktif' ?>
                        </span>
                    </div>

                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="max-w-2xl space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-extrabold text-slate-900 tracking-tight">Pemisahan Peran Petugas SLIK Terpisah</span>
                                <span id="labelModeSlik" class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $isPetugasSlikAktif === 1 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-700' ?>">
                                    <?= $isPetugasSlikAktif === 1 ? 'Mode Standar (SOP Ketat)' : 'Mode Fleksibel (Single AO)' ?>
                                </span>
                            </div>
                            <p id="deskripsiSlikSetting" class="text-xs text-slate-600 leading-relaxed">
                                <?= $isPetugasSlikAktif === 1 
                                    ? '<b>Petugas SLIK Khusus Diaktifkan:</b> Pengusul (AO) <b>tidak bisa</b> langsung menginput atau mengunggah berkas hasil SLIK di formulir pengajuan (Tab 6 terkunci). Hasil verifikasi fasilitas kredit wajib diinput oleh Petugas SLIK terpisah. Pengusul tetap dapat melanjutkan pengisian tahap formulir berikutnya.' 
                                    : '<b>Petugas SLIK Nonaktif / Bebas:</b> Pengusul (AO) <b>bisa langsung</b> menginput hasil kolektibilitas, ringkasan, serta mengunggah PDF SLIK sendiri di Tab 6 formulir pengajuan kredit.' 
                                ?>
                            </p>
                            <div class="flex items-center gap-2 pt-1 text-[11px] text-slate-400">
                                <span class="w-1.5 h-1.5 rounded-full <?= $isPetugasSlikAktif === 1 ? 'bg-emerald-500' : 'bg-amber-500' ?>"></span>
                                <span id="hintSlikSetting"><?= $isPetugasSlikAktif === 1 ? 'Pengusul dibatasi hanya dapat membuat permohonan TXT dan membaca hasil.' : 'Pengusul memiliki wewenang penuh mengisi formulir SLIK.' ?></span>
                            </div>
                        </div>

                        <!-- Toggle Switch Button -->
                        <div class="shrink-0 flex flex-col items-end gap-2">
                            <input type="hidden" name="petugas_slik_aktif" id="inputPetugasSlikAktif" value="<?= $isPetugasSlikAktif ?>">
                            <button type="button" 
                                    id="btnTogglePetugasSlik" 
                                    onclick="toggleSettingPetugasSlik()"
                                    class="relative inline-flex h-8 w-16 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none <?= $isPetugasSlikAktif === 1 ? 'bg-blue-600' : 'bg-slate-300' ?>" 
                                    role="switch" 
                                    aria-checked="<?= $isPetugasSlikAktif === 1 ? 'true' : 'false' ?>">
                                <span class="sr-only">Aktifkan Petugas SLIK</span>
                                <span id="toggleKnobPetugasSlik" 
                                      class="pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out <?= $isPetugasSlikAktif === 1 ? 'translate-x-8' : 'translate-x-0' ?>">
                                </span>
                            </button>
                            <span id="toggleTextPetugasSlik" class="text-[11px] font-bold text-slate-600">
                                <?= $isPetugasSlikAktif === 1 ? 'Status: AKTIF' : 'Status: NONAKTIF' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- BOTTOM ACTION BAR -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs w-full">
                    <button type="button" 
                            onclick="resetFormDefault()" 
                            class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition cursor-pointer shadow-2xs">
                        Kembalikan Kontak &amp; Identitas ke Default
                    </button>
                    
                    <button type="submit" 
                            id="btnSimpanPengaturan" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold inline-flex items-center gap-2 transition-colors shadow-xs shadow-blue-500/20 cursor-pointer">
                        <span>💾</span>
                        <span>Simpan Pengaturan Lembaga &amp; Kontak</span>
                    </button>
                </div>

            </form>

        </main>
    </div>

    <!-- Script Interaktif -->
    <script>
        function updatePreviewKop() {
            const nama = document.getElementById('inputNamaLembaga').value || 'PT BPR / Lembaga Pembiayaan';
            const alamat = document.getElementById('inputAlamatLembaga').value || 'BPR Financial Center, Lantai 8, Jakarta Selatan';
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
                text: 'Kembalikan data nama aplikasi, nama lembaga, dan informasi kontak ke format standar bawaan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    document.getElementById('inputNamaAplikasi').value = 'OTW Kredit';
                    document.getElementById('inputSloganAplikasi').value = 'Sistem Analisa Kredit';
                    document.getElementById('inputNamaLembaga').value = 'PT BPR / Lembaga Pembiayaan';
                    document.getElementById('inputAlamatLembaga').value = 'BPR Financial Center, Lantai 8, Jakarta Selatan';
                    document.getElementById('inputEmailLembaga').value = 'info@otwkredit.id';
                    document.getElementById('inputTeleponLembaga').value = '(021) 5088-2900';
                    updatePreviewKop();
                    Swal.fire({
                        icon: 'success',
                        title: 'Formulir Direset',
                        text: 'Silakan klik Simpan Pengaturan untuk menerapkan perubahan ke database.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        function toggleSettingPetugasSlik() {
            const input = document.getElementById('inputPetugasSlikAktif');
            const btn = document.getElementById('btnTogglePetugasSlik');
            const knob = document.getElementById('toggleKnobPetugasSlik');
            const badge = document.getElementById('badgeStatusSlikSetting');
            const text = document.getElementById('toggleTextPetugasSlik');
            const labelMode = document.getElementById('labelModeSlik');
            const desc = document.getElementById('deskripsiSlikSetting');
            const hint = document.getElementById('hintSlikSetting');

            const current = parseInt(input.value) || 0;
            const next = current === 1 ? 0 : 1;
            input.value = next;

            if (next === 1) {
                // Aktif
                btn.className = 'relative inline-flex h-8 w-16 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-blue-600';
                btn.setAttribute('aria-checked', 'true');
                knob.className = 'pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-8';
                badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-md border text-emerald-700 bg-emerald-50 border-emerald-200';
                badge.innerText = '🟢 Petugas SLIK Aktif';
                text.innerText = 'Status: AKTIF';
                labelMode.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700';
                labelMode.innerText = 'Mode Standar (SOP Ketat)';
                desc.innerHTML = '<b>Petugas SLIK Khusus Diaktifkan:</b> Pengusul (AO) <b>tidak bisa</b> langsung menginput atau mengunggah berkas hasil SLIK di formulir pengajuan (Tab 6 terkunci). Hasil verifikasi fasilitas kredit wajib diinput oleh Petugas SLIK terpisah. Pengusul tetap dapat melanjutkan pengisian tahap formulir berikutnya.';
                hint.innerText = 'Pengusul dibatasi hanya dapat membuat permohonan TXT dan membaca hasil.';
            } else {
                // Nonaktif
                btn.className = 'relative inline-flex h-8 w-16 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none bg-slate-300';
                btn.setAttribute('aria-checked', 'false');
                knob.className = 'pointer-events-none inline-block h-7 w-7 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-0';
                badge.className = 'text-[11px] font-bold px-2.5 py-1 rounded-md border text-slate-600 bg-slate-100 border-slate-200';
                badge.innerText = '⚪ Petugas SLIK Nonaktif';
                text.innerText = 'Status: NONAKTIF';
                labelMode.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700';
                labelMode.innerText = 'Mode Fleksibel (Single AO)';
                desc.innerHTML = '<b>Petugas SLIK Nonaktif / Bebas:</b> Pengusul (AO) <b>bisa langsung</b> menginput hasil kolektibilitas, ringkasan, serta mengunggah PDF SLIK sendiri di Tab 6 formulir pengajuan kredit.';
                hint.innerText = 'Pengusul memiliki wewenang penuh mengisi formulir SLIK.';
            }
        }
    </script>
</body>
</html>
