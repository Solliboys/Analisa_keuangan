<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                        <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" alt="Logo" class="w-10 h-10 object-contain rounded-xl border border-slate-200">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            SA
                        </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight"><?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Credit Engine & CAM') ?></span>
                        <span class="text-xs block text-slate-500 font-medium"><?= esc($pengaturan_sistem['nama_lembaga'] ?? 'Executive Management & Komite') ?></span>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="text-right hidden sm:block mr-2">
                        <div class="text-sm font-semibold text-slate-900"><?= esc($nama_lengkap) ?></div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                            Super Administrator / Komite
                        </span>
                    </div>
                    <a href="#sectionUsers" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition hidden md:inline-flex items-center gap-1">
                        <span>👥</span> Approval User
                    </a>
                    <a href="#sectionSettings" class="px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition inline-flex items-center gap-1">
                        <span>⚙️</span> Pengaturan & Prompt AI
                    </a>
                    <a href="<?= base_url('pengajuan') ?>" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition hidden sm:inline-flex">
                        Semua Pengajuan
                    </a>
                    <a href="<?= base_url('auth/logout') ?>" class="px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        
        <!-- Flash Alert -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
                <span>&check; <?= session()->getFlashdata('success') ?></span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between">
                <span>&times; <?= session()->getFlashdata('error') ?></span>
                <button onclick="this.parentElement.remove()" class="text-rose-600 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Executive Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/30 text-indigo-200 uppercase tracking-wider">
                    Pusat Komando & Keputusan Komite
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold mt-3 tracking-tight">Selamat Datang, <?= esc($nama_lengkap) ?></h1>
                <p class="text-sm text-slate-300 mt-1 max-w-xl">
                    Periksa usulan kredit dari seluruh Account Officer, ambil keputusan sidang komite, setujui akun pendaftaran pengusul baru, serta atur sistem lembaga.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="#sectionApprovalUsers" class="inline-flex items-center gap-2 px-4 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition">
                    <?php if ($users_pending > 0): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse"></span>
                    <?php endif; ?>
                    Persetujuan Pengusul (<?= $users_pending ?>)
                </a>
                <a href="#sectionSettings" class="inline-flex items-center gap-2 px-4 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition border border-white/20">
                    Pengaturan Sistem & Logo
                </a>
            </div>
        </div>

        <!-- Portfolio Statistics Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Portofolio</span>
                <div class="text-2xl font-extrabold text-slate-900 mt-1"><?= number_format($total_pengajuan) ?></div>
                <div class="text-xs text-slate-400 mt-1">Seluruh berkas terdaftar</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Total Plafon Portofolio</span>
                <div class="text-xl font-extrabold text-indigo-700 mt-1">Rp <?= number_format($total_plafon, 0, ',', '.') ?></div>
                <div class="text-xs text-slate-400 mt-1">Akumulasi pengajuan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Sidang Komite</span>
                <div class="text-2xl font-extrabold text-amber-600 mt-1"><?= number_format($total_pending) ?></div>
                <div class="text-xs text-slate-400 mt-1">Status Draft / Diajukan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Disetujui Komite</span>
                <div class="text-2xl font-extrabold text-emerald-700 mt-1"><?= number_format($total_approved) ?></div>
                <div class="text-xs text-slate-400 mt-1">Lolos persetujuan</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">Pengguna Sistem</span>
                <div class="text-2xl font-extrabold text-purple-700 mt-1"><?= number_format($total_users) ?></div>
                <div class="text-xs text-slate-400 mt-1"><?= $users_pending ?> menunggu approval</div>
            </div>
        </div>

        <!-- ========================================================
             FITUR 1: PERSETUJUAN PENDAFTARAN PENGUSUL BARU
        ========================================================= -->
        <div id="sectionUsers" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                        &check;
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Persetujuan Registrasi Petugas Pengusul (Account Officer)</h2>
                        <p class="text-xs text-slate-500">Verifikasi calon pengusul yang mendaftar melalui form login sebelum diberikan akses ke sistem</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $users_pending > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' ?>">
                    <?= $users_pending ?> Pendaftar Menunggu Persetujuan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Kontak (Email / No. HP)</th>
                            <th class="py-3 px-4">Role Jabatan</th>
                            <th class="py-3 px-4">Tanggal Daftar</th>
                            <th class="py-3 px-4">Status Akun</th>
                            <th class="py-3 px-4 text-center">Tindakan Superadmin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($daftar_users as $u): ?>
                            <tr class="hover:bg-slate-50 transition <?= $u['status'] == 0 ? 'bg-amber-50/30' : '' ?>">
                                <td class="py-3 px-4 font-bold text-slate-900">
                                    <?= esc($u['nama_lengkap']) ?>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-700">
                                    @<?= esc($u['username']) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-slate-800"><?= esc($u['email'] ?: '-') ?></div>
                                    <div class="text-[11px] text-slate-500"><?= esc($u['nomor_hp'] ?: '-') ?></div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        <?= esc($u['jabatan']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                    <?= date('d M Y H:i', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <?php if ($u['status'] == 1): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php elseif ($u['status'] == 0): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300 animate-pulse">
                                            Menunggu Approval
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <?php if (strtolower($u['jabatan']) !== 'superadmin'): ?>
                                        <div class="inline-flex items-center gap-1.5">
                                            <?php if ($u['status'] != 1): ?>
                                                <a href="<?= base_url('dashboard/setujui-user/' . $u['id']) ?>" 
                                                   onclick="return confirm('Setujui akun <?= esc($u['nama_lengkap']) ?> sebagai Pengusul?')"
                                                   class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition text-xs shadow-xs">
                                                    &check; Setujui (Aktifkan)
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($u['status'] == 1): ?>
                                                <a href="<?= base_url('dashboard/tolak-user/' . $u['id']) ?>" 
                                                   onclick="return confirm('Nonaktifkan akun <?= esc($u['nama_lengkap']) ?>?')"
                                                   class="px-2.5 py-1 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-600 font-bold rounded-lg transition text-xs">
                                                    Nonaktifkan
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-[11px] text-slate-400 italic">Akun Utama</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section: Antrean Sidang Komite Kredit -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Antrean Sidang Komite & Review Pengajuan</h2>
                    <p class="text-xs text-slate-500">Evaluasi berkas permohonan dari seluruh pengusul dan tetapkan keputusan komite</p>
                </div>
                <a href="<?= base_url('pengajuan') ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Buka Database Pengajuan &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider font-bold border-b border-slate-200">
                        <tr>
                            <th class="py-3.5 px-4">No. Aplikasi</th>
                            <th class="py-3.5 px-4">Calon Debitur & Usaha</th>
                            <th class="py-3.5 px-4">Plafon Diminta</th>
                            <th class="py-3.5 px-4">Skema & Tenor</th>
                            <th class="py-3.5 px-4">Status Pengajuan</th>
                            <th class="py-3.5 px-4 text-center">Aksi Komite</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($antrean_pengajuan)): ?>
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    Belum ada pengajuan kredit yang masuk dalam antrean.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($antrean_pengajuan as $row): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="py-3.5 px-4 font-bold text-slate-900 whitespace-nowrap">
                                        <?= esc($row['nomor_aplikasi']) ?>
                                        <span class="block text-[11px] font-normal text-slate-400"><?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800"><?= esc($row['nama_debitur'] ?: '-') ?></div>
                                        <div class="text-[11px] text-slate-500"><?= esc($row['nama_usaha'] ?: '-') ?></div>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-indigo-700 whitespace-nowrap">
                                        Rp <?= number_format((float)($row['plafon_pengajuan'] ?? 0), 0, ',', '.') ?>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div><?= esc($row['skema_pembayaran']) ?></div>
                                        <div class="text-[11px] text-slate-400"><?= (int)$row['tenor_bulan'] ?> Bulan</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <?php
                                            $st = strtolower($row['status_pengajuan'] ?? 'draft');
                                            $badge = 'bg-slate-100 text-slate-700 border-slate-200';
                                            if ($st === 'disetujui') $badge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            elseif ($st === 'diajukan') $badge = 'bg-blue-50 text-blue-700 border-blue-200';
                                            elseif ($st === 'ditolak') $badge = 'bg-rose-50 text-rose-700 border-rose-200';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $badge ?>">
                                            <?= esc($row['status_pengajuan'] ?? 'Draft') ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="<?= base_url('pengajuan/edit/' . $row['kredit_id']) ?>" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition text-xs shadow-xs" title="Buka Sidang Komite">
                                                Review & Putuskan
                                            </a>
                                            <a href="<?= base_url('pengajuan/cetak/' . $row['kredit_id']) ?>" target="_blank" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-xs" title="Lihat Laporan Memo CAM (PDF)">
                                                Cetak PDF
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

        <!-- ========================================================
             FITUR 2: PENGATURAN SISTEM, NAMA, LOGO, DAN API
        ========================================================= -->
        <div id="sectionSettings" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm mb-8">
            <div class="flex items-center justify-between border-b pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-lg">
                        ⚙️
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Pengaturan Sistem & Konfigurasi Lembaga</h2>
                        <p class="text-xs text-slate-500">Ubah nama aplikasi, nama lembaga perbankan/koperasi, logo, dan kredensial API Gemini</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Engine Online
                </span>
            </div>

            <form action="<?= base_url('dashboard/update-pengaturan') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Sistem / Aplikasi</label>
                        <input type="text" name="nama_aplikasi" value="<?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Credit Engine & CAM') ?>" 
                               class="w-full text-xs font-bold border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[11px] text-slate-400 mt-1 block">Ditampilkan pada navbar dan dokumen laporan.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lembaga / Koperasi / BPR</label>
                        <input type="text" name="nama_lembaga" value="<?= esc($pengaturan_sistem['nama_lembaga'] ?? 'PT BPR / Lembaga Pembiayaan') ?>" 
                               class="w-full text-xs font-bold border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500">
                        <span class="text-[11px] text-slate-400 mt-1 block">Nama instansi pada kop surat memorandum CAM.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Logo Lembaga (Upload Gambar Baru)</label>
                        <div class="flex items-center gap-4">
                            <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                                <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" alt="Logo Saat Ini" class="w-12 h-12 object-contain rounded-xl border border-slate-200 p-1 bg-white">
                            <?php endif; ?>
                            <input type="file" name="logo_file" accept="image/png, image/jpeg, image/svg+xml"
                                   class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 block">Format: PNG, JPG, atau SVG (Maksimal 2MB).</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Model Gemini AI</label>
                        <select name="model_ai" class="w-full text-xs font-semibold border border-slate-300 rounded-xl p-3 bg-white focus:ring-2 focus:ring-indigo-500">
                            <?php $m = $pengaturan_sistem['model_ai'] ?? 'gemini-3.8-flash'; ?>
                            <option value="gemini-3.8-flash" <?= $m === 'gemini-3.8-flash' ? 'selected' : '' ?>>gemini-3.8-flash (Rekomendasi Cepat & Akurat)</option>
                            <option value="gemini-2.5-flash" <?= $m === 'gemini-2.5-flash' ? 'selected' : '' ?>>gemini-2.5-flash</option>
                            <option value="gemini-2.5-pro" <?= $m === 'gemini-2.5-pro' ? 'selected' : '' ?>>gemini-2.5-pro (Analisis Risiko Mendalam)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Google Gemini API Key</label>
                        <input type="text" name="api_key" value="<?= esc($pengaturan_sistem['api_key'] ?? '') ?>" 
                               class="w-full text-xs font-mono border border-slate-300 rounded-xl p-3 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                               placeholder="Masukkan Google Gemini API Key...">
                        <span class="text-[11px] text-slate-400 mt-1 block">Kunci otentikasi Google Generative Language API.</span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Master Prompt / System Instruction (Peran & Pedoman Underwriting AI)
                        </label>
                        <textarea name="prompt_sistem" rows="3" 
                                  class="w-full text-xs font-mono border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 leading-relaxed"><?= esc($pengaturan_sistem['prompt_sistem'] ?? 'Anda adalah Senior Credit Underwriter dan Risk Analyst perbankan profesional. Lakukan evaluasi komprehensif terhadap calon debitur pembiayaan berdasarkan prinsip 5C (Character, Capacity, Capital, Collateral, Condition).') ?></textarea>
                        <span class="text-[11px] text-slate-400 mt-1 block">Instruksi utama yang mendikte peran, ketajaman mitigasi risiko, dan standar objektivitas model AI Gemini.</span>
                    </div>
                </div>

                <!-- SUB-PENGATURAN PROMPT 5C -->
                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-sm">🎯</span>
                        <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Konfigurasi Pedoman Analisis 5C (Sub-Prompts)</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>1. Prompt Karakter (Character)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">SLIK & Legalitas</span>
                            </label>
                            <textarea name="prompt_character" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_character'] ?? 'Analisis karakter debitur berdasarkan riwayat kredit, SLIK OJK, dan integritas.') ?></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>2. Prompt Kapasitas (Capacity)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">Cashflow & DSC</span>
                            </label>
                            <textarea name="prompt_capacity" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_capacity'] ?? 'Analisis kapasitas debitur berdasarkan perputaran rekening koran, omset usaha, dan rasio DSC.') ?></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>3. Prompt Permodalan (Capital)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">Struktur Modal</span>
                            </label>
                            <textarea name="prompt_capital" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_capital'] ?? 'Analisis permodalan debitur berdasarkan rasio keuangan, modal kerja, dan struktur ekuitas.') ?></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>4. Prompt Agunan (Collateral)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">Market Value & LTV</span>
                            </label>
                            <textarea name="prompt_collateral" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_collateral'] ?? 'Analisis jaminan debitur berdasarkan nilai pasar, nilai likuidasi, rasio penutupan (coverage), dan kemudahan likuidasi.') ?></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>5. Prompt Kondisi (Condition)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">Makro & Sektor</span>
                            </label>
                            <textarea name="prompt_condition" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_condition'] ?? 'Analisis prospek ekonomi, tren industri, regulasi, dan risiko persaingan sektor usaha debitur.') ?></textarea>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                            <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>6. Prompt Keputusan Final (Recommendation)</span>
                                <span class="text-[10px] text-indigo-600 font-semibold">Putusan Komite</span>
                            </label>
                            <textarea name="prompt_final" rows="2" 
                                      class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-indigo-500 font-medium"><?= esc($pengaturan_ai['prompt_final'] ?? 'Berikan ringkasan rekomendasi keputusan akhir komite kredit, penyesuaian plafon realistis, serta klausul mitigasi risiko.') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                        <span>💾</span>
                        <span>Simpan Seluruh Pengaturan & Prompt AI</span>
                    </button>
                </div>

            </form>
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500">
            &copy; <?= date('Y') ?> <?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'Credit Engine & CAM') ?>. Hak Akses Super Administrator & Komite Kredit.
        </div>
    </footer>
</body>
</html>
