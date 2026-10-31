<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Profil Pengguna') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $namaWeb = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine & CAM';
    $logoWeb = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';
?>
    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="<?= base_url('dashboard') ?>" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition" title="Kembali ke Dashboard">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">Pengaturan Profil Akun</span>
                        <span class="text-xs block text-slate-500 font-medium"><?= esc($namaWeb) ?> &bull; Kelola informasi pribadi akun Anda</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?= base_url('dashboard') ?>" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        Dashboard
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="px-3.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">

        <!-- Flash Alert -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold text-sm">&times;</button>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Left Card: User Identity & Avatar -->
            <div class="md:col-span-1 bg-white rounded-3xl border border-slate-200/90 p-6 shadow-xs flex flex-col items-center text-center">
                <?php
                    $fotoUrl = !empty($user['foto']) ? base_url($user['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap'] ?? $nama_lengkap ?? 'AO') . '&background=2563eb&color=fff&bold=true';
                ?>
                <div class="relative mb-4 group cursor-pointer" onclick="document.getElementById('inputFotoProfil').click()" title="Klik untuk ganti foto">
                    <img id="previewAvatarImg" 
                         src="<?= $fotoUrl ?>" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['nama_lengkap'] ?? $nama_lengkap ?? 'AO') ?>&background=2563eb&color=fff&bold=true';" 
                         alt="Foto Profil" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md ring-2 ring-blue-500/30">
                    <div class="absolute inset-0 rounded-full bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white" title="Aktif"></span>
                </div>

                <h2 class="text-base font-extrabold text-slate-900 leading-tight"><?= esc($user['nama_lengkap'] ?? $nama_lengkap) ?></h2>
                <p class="text-xs text-slate-400 mt-0.5">@<?= esc($user['username'] ?? 'user') ?></p>
                
                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-3 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    <?= esc($user['jabatan'] ?? $jabatan ?? 'Petugas Pengusul') ?>
                </span>

                <div class="w-full border-t border-slate-100 mt-6 pt-5 text-left text-xs space-y-3">
                    <?php if (!empty($user['email'])): ?>
                        <div>
                            <span class="text-slate-400 font-medium block">Alamat Email</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block truncate"><?= esc($user['email']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($user['nomor_hp'])): ?>
                        <div>
                            <span class="text-slate-400 font-medium block">Nomor HP / WhatsApp</span>
                            <span class="font-semibold text-slate-700 mt-0.5 block"><?= esc($user['nomor_hp']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-slate-400 font-medium block">Status Akun</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1.5 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Terverifikasi & Aktif
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Terdaftar Sejak</span>
                        <span class="font-semibold text-slate-700 mt-0.5 block">
                            <?= !empty($user['created_at']) ? date('d M Y, H:i', strtotime($user['created_at'])) : date('d M Y') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Card: Form Edit Profil -->
            <div class="md:col-span-2 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <div class="pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Perbarui Informasi Akun</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Ubah nama lengkap, username, email, nomor HP, foto profil, atau kata sandi Anda.</p>
                </div>

                <form action="<?= base_url('profil/update') ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <?= csrf_field() ?>

                    <!-- Upload Foto Profil -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Unggah Foto Profil Baru
                        </label>
                        <div class="p-3 bg-slate-50/70 rounded-2xl border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <input type="file" name="foto_file" id="inputFotoProfil" accept="image/png, image/jpeg, image/jpg, image/webp"
                                   onchange="previewFoto(this)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer transition">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Format: JPG, PNG, atau WEBP (Maksimal 2MB). <b>Otomatis dikompresi & dioptimasi sistem</b> agar ringan.</span>
                    </div>

                    <!-- Nama Lengkap & Username -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nama Lengkap
                            </label>
                            <input type="text" name="nama_lengkap" value="<?= esc($user['nama_lengkap'] ?? $nama_lengkap) ?>" required
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Username
                            </label>
                            <input type="text" name="username" value="<?= esc($user['username'] ?? '') ?>" required
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Email & Nomor HP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Alamat Email
                            </label>
                            <input type="email" name="email" value="<?= esc($user['email'] ?? '') ?>" placeholder="contoh: nama@email.com"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nomor HP / WhatsApp
                            </label>
                            <input type="tel" name="nomor_hp" value="<?= esc($user['nomor_hp'] ?? '') ?>" placeholder="contoh: 081234567890"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>

                    <!-- Jabatan (Read-Only) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Jabatan / Role Akses
                        </label>
                        <input type="text" value="<?= esc($user['jabatan'] ?? $jabatan ?? 'Petugas Pengusul') ?>" disabled
                               class="w-full text-xs font-semibold text-slate-500 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-100 cursor-not-allowed">
                        <span class="text-[11px] text-slate-400 mt-1 block">Role ditetapkan dan diverifikasi oleh Super Administrator.</span>
                    </div>

                    <!-- Divider Password -->
                    <div class="pt-3 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Ganti Kata Sandi (Opsional)</h4>
                        <p class="text-[11px] text-slate-400 mb-4">Kosongkan jika Anda tidak ingin mengubah kata sandi.</p>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                        <a href="<?= base_url('dashboard') ?>" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs hover:shadow transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function previewFoto(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('previewAvatarImg');
                        if (preview) {
                            preview.src = e.target.result;
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?>. Keamanan data pengguna terenkripsi.
        </div>
    </footer>

</body>
</html>
