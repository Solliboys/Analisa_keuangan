<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Verifikasi Fasilitas Kredit SLIK OJK') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

<?php
    if (!isset($pengaturan_sistem)) {
        $pengaturan_sistem = \Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
    }
    $namaWeb     = !empty($pengaturan_sistem['nama_aplikasi']) ? $pengaturan_sistem['nama_aplikasi'] : 'OTW Credit Engine & CAM';
    $namaLembaga = !empty($pengaturan_sistem['nama_lembaga']) ? $pengaturan_sistem['nama_lembaga'] : 'Portal Account Officer';
    $logoWeb     = !empty($pengaturan_sistem['logo_aplikasi']) ? $pengaturan_sistem['logo_aplikasi'] : '';

    $words = preg_split("/[\s,_-]+/", trim($namaWeb));
    $initials = '';
    foreach ($words as $w) {
        if (!empty($w)) $initials .= mb_substr($w, 0, 1);
        if (mb_strlen($initials) >= 2) break;
    }
    if (empty($initials)) $initials = 'OT';
    $initials = strtoupper($initials);

    // Ambil data debitur dari kredit
    $nikVal = !empty($kredit['npwp_pribadi']) ? $kredit['npwp_pribadi'] : (!empty($kredit['nik']) ? $kredit['nik'] : ($no_id_val ?? '-'));
    $namaDebiturVal = !empty($kredit['nama_debitur']) ? $kredit['nama_debitur'] : 'Debitur';
    $tglLahirVal = !empty($kredit['tanggal_lahir']) ? date('d M Y', strtotime($kredit['tanggal_lahir'])) : (!empty($kredit['tempat_tanggal_lahir']) ? $kredit['tempat_tanggal_lahir'] : '-');
    $tempatLahirVal = !empty($kredit['tempat_lahir']) ? $kredit['tempat_lahir'] : '';
    $ibuKandungVal = !empty($kredit['nama_ibu_kandung']) ? $kredit['nama_ibu_kandung'] : (!empty($kredit['nama_kontak_darurat']) ? $kredit['nama_kontak_darurat'] : '-');
    $alamatKtpVal = !empty($kredit['alamat_ktp']) ? $kredit['alamat_ktp'] : (!empty($kredit['alamat_domisili']) ? $kredit['alamat_domisili'] : '-');
    $noHpVal = !empty($kredit['nomor_hp']) ? $kredit['nomor_hp'] : '-';
    $berkasPdfVal = $kredit['berkas_slik_pdf'] ?? '';
    $berkasTxtVal = $kredit['berkas_slik_txt'] ?? '';
?>

    <!-- Topbar Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <div class="flex items-center gap-4">
                    <a href="<?= base_url('dashboard') ?>" 
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:text-blue-700 hover:bg-blue-50 hover:border-blue-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Dashboard SLIK</span>
                    </a>

                    <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>

                    <div class="hidden md:flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aplikasi:</span>
                        <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                            <?= esc($kredit['nomor_aplikasi'] ?? '-') ?>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 truncate max-w-xs">
                            <?= esc($namaDebiturVal) ?>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="text-[10px] block font-bold text-slate-400 uppercase tracking-wider">Petugas Verifikator</span>
                        <span class="text-xs font-bold text-slate-800"><?= esc($nama_lengkap ?? 'Petugas SLIK') ?></span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                        <?= esc(mb_substr($nama_lengkap ?? 'SL', 0, 2)) ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-1 max-w-7xl mx-auto">

        <!-- DEBITUR IDENTITY & SLIK DISPATCH CARD -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-7 mb-6 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-xl shrink-0 shadow-md">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                <?= esc($namaDebiturVal) ?>
                            </h1>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <?= esc($kredit['nomor_aplikasi']) ?>
                            </span>
                            <?php if (($kredit['status_slik'] ?? '') === 'Terbit'): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-300 ring-1 ring-emerald-200/50 flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    SLIK Telah Terbit
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300 ring-1 ring-amber-200/50 flex items-center gap-1.5 shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Verifikasi SLIK
                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span>Jenis Usaha: <strong class="text-slate-700"><?= esc($kredit['nama_usaha'] ?: ($kredit['bidang_usaha'] ?: '-')) ?></strong></span>
                            <span>&bull;</span>
                            <span>Plafon Pengajuan: <strong class="text-blue-700">Rp <?= number_format((float)($kredit['plafon_pengajuan'] ?? 0), 0, ',', '.') ?></strong></span>
                            <span>&bull;</span>
                            <span>Tanggal Masuk: <?= date('d M Y', strtotime($kredit['created_at'])) ?></span>
                        </p>
                    </div>
                </div>

                <!-- Action Button: Export TXT Permohonan IDEB OJK & Lihat KTP -->
                <div class="flex flex-wrap items-center gap-2.5 self-start lg:self-auto">
                    <button type="button" onclick="exportTxtPermohonanSlik()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer"
                            title="Unduh berkas TXT baku IDEB OJK untuk diunggah langsung ke portal SLIK OJK">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Export TXT IDEB OJK</span>
                    </button>

                    <button type="button" onclick="bukaModalKtpDebitur()" 
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:scale-98 text-slate-700 rounded-xl text-xs font-bold transition border border-slate-200 cursor-pointer"
                            title="Buka / Periksa Foto Berkas KTP Debitur Asli">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                        <span>Periksa KTP</span>
                    </button>
                </div>
            </div>

            <!-- Parameter Kunci Identitas Pemohon SLIK (Grid 4 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                <!-- NIK KTP -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">NIK KTP (16 Digit)</span>
                        <span class="text-sm font-extrabold text-slate-900 tracking-wide block mt-0.5" id="valNikKtp"><?= esc($nikVal) ?></span>
                    </div>
                    <button type="button" onclick="salinTeks('<?= esc($nikVal) ?>', 'NIK KTP')" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition" title="Salin NIK">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <!-- Tempat & Tanggal Lahir -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tempat & Tanggal Lahir</span>
                    <span class="text-xs font-extrabold text-slate-900 block mt-0.5">
                        <?= esc($tempatLahirVal ? $tempatLahirVal . ', ' : '') ?><?= esc($tglLahirVal) ?>
                    </span>
                </div>

                <!-- Nama Ibu Kandung -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Ibu Kandung / Pasangan</span>
                        <span class="text-xs font-extrabold text-slate-900 block mt-0.5"><?= esc($ibuKandungVal) ?></span>
                    </div>
                    <button type="button" onclick="salinTeks('<?= esc($ibuKandungVal) ?>', 'Nama Ibu Kandung')" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition" title="Salin Nama Ibu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>

                <!-- No Reff Permohonan OJK -->
                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/80 flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">No. Reff IDEB OJK</span>
                        <span class="text-xs font-mono font-bold text-blue-900 truncate block mt-0.5 max-w-[190px]" title="<?= esc($no_reff_slik ?? '') ?>">
                            <?= esc($no_reff_slik ?? '-') ?>
                        </span>
                    </div>
                    <button type="button" onclick="salinTeks('<?= esc($no_reff_slik ?? '') ?>', 'No Reff SLIK')" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-white rounded-lg transition" title="Salin No Reff">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Banner Usulan Pengusul (Jika Pengusul telah menginput draf data) -->
        <?php if (!empty($usulan_pengusul)): ?>
            <div class="bg-gradient-to-r from-amber-500/10 via-amber-50 to-orange-50 border border-amber-200 rounded-3xl p-5 sm:p-6 mb-6 shadow-xs">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-amber-900 uppercase tracking-wide">
                                    Ditemukan Usulan Data SLIK dari Pengusul
                                </h3>
                                <span class="px-2 py-0.5 bg-amber-200 text-amber-800 text-[11px] font-bold rounded-full">
                                    <?= count($usulan_pengusul) ?> Fasilitas Terdaftar
                                </span>
                            </div>
                            <p class="text-xs text-amber-800 mt-1 max-w-2xl leading-relaxed">
                                Pengusul telah mengisi draf fasilitas kredit nasabah ini. Anda dapat menyetujui seluruh usulan tersebut secara otomatis untuk disahkan ke tabel resmi atau melakukan penyesuaian di tabel formulir di bawah.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" onclick="setujuiUsulanPengusul(<?= (int)$kredit_id ?>)" 
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Setujui Data Pengusul</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- FORMULIR VERIFIKASI RESMI PETUGAS SLIK -->
        <form id="formVerifikasiSlik" onsubmit="simpanFormSlik(event)" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="kredit_id" value="<?= esc($kredit_id) ?>">

            <!-- KARTU 1: TABEL FASILITAS PINJAMAN AKTIF DI BANK LAIN -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-6">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            1
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DAFTAR FASILITAS KREDIT (SLIK OJK / IDEB)</h2>
                            <p class="text-[11px] text-slate-500">Pencatatan rincian seluruh fasilitas pinjaman debitur/pasangan di bank atau lembaga keuangan lain</p>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBarisSlik()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition border border-blue-200/60 shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Fasilitas</span>
                    </button>
                </div>

                <!-- Tabel Riwayat Fasilitas Kredit -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                    <table class="w-full text-left border-collapse" id="tabelSlik">
                        <thead class="bg-slate-50/80 text-slate-700 font-bold text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-3 min-w-[170px]">Nama Debitur / Pemilik</th>
                                <th class="py-3.5 px-3 min-w-[160px]">Bank / Lembaga</th>
                                <th class="py-3.5 px-3 min-w-[140px]">Plafon (Rp)</th>
                                <th class="py-3.5 px-3 min-w-[150px]">Baki Debet / Outstanding</th>
                                <th class="py-3.5 px-3 min-w-[170px]">Kualitas / Kol</th>
                                <th class="py-3.5 px-3 min-w-[170px]">Keterangan / Tujuan</th>
                                <th class="py-3.5 px-2 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs" id="bodySlik">
                            <?php if (empty($slik)): ?>
                                <tr>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][nama_debitur]" value="<?= esc($namaDebiturVal) ?>" placeholder="Debitur / Pasangan" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][plafon]" placeholder="0" 
                                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-plafon" 
                                               oninput="formatRupiah(this); hitungMetrikSlik();">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][outstanding]" placeholder="0" 
                                               class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                               oninput="formatRupiah(this); hitungMetrikSlik();">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <select name="slik[0][kelancaran]" onchange="hitungMetrikSlik()"
                                                class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer slik-kol">
                                            <option value="Lancar">Kol 1 (Lancar)</option>
                                            <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                                            <option value="Macet">Kol 5 (Macet)</option>
                                        </select>
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </td>
                                    <td class="p-2.5 bg-white text-center">
                                        <button type="button" onclick="hapusBarisTabel(this); hitungMetrikSlik();" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slik as $idx => $s): ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][nama_debitur]" value="<?= esc($s['nama_debitur'] ?? $namaDebiturVal) ?>" placeholder="Debitur / Pasangan" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][bank_lembaga]" value="<?= esc($s['bank_lembaga'] ?? '') ?>" placeholder="Nama Bank / Leasing" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][plafon]" value="<?= number_format((float)($s['plafon'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-plafon" 
                                                   oninput="formatRupiah(this); hitungMetrikSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][outstanding]" value="<?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                                   oninput="formatRupiah(this); hitungMetrikSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <select name="slik[<?= $idx ?>][kelancaran]" onchange="hitungMetrikSlik()"
                                                    class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer slik-kol">
                                                <?php $kl = $s['kelancaran'] ?? 'Lancar'; ?>
                                                <option value="Lancar" <?= $kl === 'Lancar' ? 'selected' : '' ?>>Kol 1 (Lancar)</option>
                                                <option value="DPK" <?= $kl === 'DPK' ? 'selected' : '' ?>>Kol 2 (DPK / Perhatian Khusus)</option>
                                                <option value="Kurang Lancar" <?= $kl === 'Kurang Lancar' ? 'selected' : '' ?>>Kol 3 (Kurang Lancar)</option>
                                                <option value="Diragukan" <?= $kl === 'Diragukan' ? 'selected' : '' ?>>Kol 4 (Diragukan)</option>
                                                <option value="Macet" <?= $kl === 'Macet' ? 'selected' : '' ?>>Kol 5 (Macet)</option>
                                            </select>
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][keterangan]" value="<?= esc($s['keterangan'] ?? '') ?>" placeholder="Modal Kerja / KPR / dll" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white text-center">
                                            <button type="button" onclick="hapusBarisTabel(this); hitungMetrikSlik();" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 4 KARTU REKAPITULASI LIVE HASIL SLIK -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
                    <!-- Total Baki Debet -->
                    <div class="p-4 bg-blue-50/80 border border-blue-200/90 rounded-2xl flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">Total Baki Debet (Outstanding)</span>
                        <div class="text-lg sm:text-xl font-black text-blue-700 mt-1" id="totalOutstandingSlik">Rp 0</div>
                        <span class="text-[10px] text-blue-600 mt-1">Kewajiban pokok terutang</span>
                    </div>

                    <!-- Total Plafon -->
                    <div class="p-4 bg-indigo-50/80 border border-indigo-200/90 rounded-2xl flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-indigo-800 uppercase tracking-wider block">Total Plafon Pinjaman</span>
                        <div class="text-lg sm:text-xl font-black text-indigo-700 mt-1" id="totalPlafonSlik">Rp 0</div>
                        <span class="text-[10px] text-indigo-600 mt-1">Limit fasilitas di bank lain</span>
                    </div>

                    <!-- Kolektibilitas Terburuk -->
                    <div class="p-4 bg-slate-50 border border-slate-200/90 rounded-2xl flex flex-col justify-between" id="boxKolTerburuk">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Kualitas / Kol Terburuk</span>
                        <div class="mt-1" id="badgeKolTerburuk">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Kol 1 (Lancar)</span>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1">Status histori kelancaran</span>
                    </div>

                    <!-- Jumlah Fasilitas -->
                    <div class="p-4 bg-slate-50 border border-slate-200/90 rounded-2xl flex flex-col justify-between">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Jumlah Fasilitas</span>
                        <div class="text-lg sm:text-xl font-black text-slate-800 mt-1" id="jumlahFasilitasSlik">0 Fasilitas</div>
                        <span class="text-[10px] text-slate-400 mt-1">Lembaga keuangan aktif</span>
                    </div>
                </div>

            </div>

            <!-- KARTU 2: UPLOAD BERKAS BUKTI HASIL SLIK (PDF & TXT DARI OJK) -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-6">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            2
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">UNGGAH BERKAS BUKTI RESMI SLIK OJK</h2>
                            <p class="text-[11px] text-slate-500">Lampirkan file PDF laporan iDeb resmi dan berkas data TXT dari portal SLIK OJK</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 1. Upload Berkas PDF Laporan iDeb -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                    Laporan Resmi iDeb SLIK (PDF)
                                </label>
                                <?php if (!empty($berkasPdfVal)): ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Sudah Ada File ✓
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-slate-500 mb-3">Unggah dokumen hasil penarikan iDeb format PDF untuk arsip audit perbankan.</p>

                            <!-- Input File PDF -->
                            <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-5 text-center bg-white cursor-pointer transition"
                                 onclick="document.getElementById('file_pdf').click()">
                                <input type="file" name="file_pdf" id="file_pdf" accept=".pdf" class="hidden" onchange="previewUploadName(this, 'labelPdfName')">
                                <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 block" id="labelPdfName">
                                    <?= !empty($berkasPdfVal) ? basename($berkasPdfVal) : 'Klik untuk pilih file PDF iDeb' ?>
                                </span>
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Format: .PDF (Maks 15 MB)</span>
                            </div>
                        </div>

                        <?php if (!empty($berkasPdfVal)): ?>
                            <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                                <span class="text-slate-500 truncate max-w-[200px]" title="<?= esc($berkasPdfVal) ?>">
                                    <?= esc(basename($berkasPdfVal)) ?>
                                </span>
                                <a href="<?= base_url($berkasPdfVal) ?>" target="_blank" class="px-3 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-blue-700 font-bold transition flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Buka PDF ↗</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Upload Berkas TXT Data SLIK -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    File Mentah Data SLIK (TXT)
                                </label>
                                <?php if (!empty($berkasTxtVal)): ?>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Sudah Ada File ✓
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-slate-500 mb-3">File teks data rincian fasilitas yang diunduh dari OJK (opsional pendukung).</p>

                            <!-- Input File TXT -->
                            <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-5 text-center bg-white cursor-pointer transition"
                                 onclick="document.getElementById('file_txt').click()">
                                <input type="file" name="file_txt" id="file_txt" accept=".txt" class="hidden" onchange="previewUploadName(this, 'labelTxtName')">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-slate-700 block" id="labelTxtName">
                                    <?= !empty($berkasTxtVal) ? basename($berkasTxtVal) : 'Klik untuk pilih file TXT data iDeb' ?>
                                </span>
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Format: .TXT</span>
                            </div>
                        </div>

                        <?php if (!empty($berkasTxtVal)): ?>
                            <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-xs">
                                <span class="text-slate-500 truncate max-w-[200px]" title="<?= esc($berkasTxtVal) ?>">
                                    <?= esc(basename($berkasTxtVal)) ?>
                                </span>
                                <a href="<?= base_url($berkasTxtVal) ?>" target="_blank" download class="px-3 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-blue-700 font-bold transition flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh TXT</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KARTU 3: KESIMPULAN VERIFIKASI & PENERBITAN RESMI -->
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-6">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            3
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">OPINI & KESIMPULAN VERIFIKASI SLIK</h2>
                            <p class="text-[11px] text-slate-500">Evaluasi kualitatif karakter kreditur, histori kelancaran angsuran, serta status penerbitan</p>
                        </div>
                    </div>
                </div>

                <!-- Textarea Ringkasan & Catatan Historis SLIK OJK -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ringkasan & Catatan Historis SLIK OJK
                    </label>
                    <textarea name="ringkasan_slik" rows="3" 
                              placeholder="Tuliskan kesimpulan profil karakter kredit nasabah, riwayat tunggakan (jika ada), rekomendasi pelunasan / take over, dan kelayakan fasilitas..."
                              class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs resize-y"><?= esc($kredit['ringkasan_slik'] ?? '') ?></textarea>
                </div>

                <!-- Status Penerbitan Hasil SLIK -->
                <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Status Hasil Pemeriksaan SLIK:</span>
                        <span class="text-[11px] text-slate-400">Pilih "Terbitkan Resmi" agar status pengajuan di AO dan Komite berubah menjadi terverifikasi SLIK.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/80 cursor-pointer shadow-2xs hover:bg-emerald-100/60 transition">
                            <input type="radio" name="status_slik" value="Terbit" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" <?= ($kredit['status_slik'] ?? 'Terbit') === 'Terbit' ? 'checked' : '' ?>>
                            <span class="text-xs font-bold text-emerald-900">Terbitkan Resmi (Terbit)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer shadow-2xs hover:bg-slate-100 transition">
                            <input type="radio" name="status_slik" value="Belum Terbit" class="w-4 h-4 text-slate-600 focus:ring-slate-500" <?= ($kredit['status_slik'] ?? '') === 'Belum Terbit' ? 'checked' : '' ?>>
                            <span class="text-xs font-bold text-slate-700">Simpan Draf (Belum Terbit)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ACTION BAR BAWAH -->
            <div class="flex items-center justify-between gap-4 py-4">
                <a href="<?= base_url('dashboard') ?>" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                    Kembali ke Dashboard
                </a>

                <div class="flex items-center gap-3">
                    <button type="submit" id="btnSubmitSlik"
                            class="inline-flex items-center gap-2 px-7 py-3 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-xl text-xs font-extrabold transition shadow-md cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan & Terbitkan Hasil SLIK</span>
                    </button>
                </div>
            </div>

        </form>

    </main>

    <!-- MODAL PREVIEW BERKAS KTP DEBITUR -->
    <div id="modalKtpDebitur" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl border border-slate-200 max-w-2xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        🪪
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Berkas KTP Debitur Asli</h3>
                        <p class="text-[11px] text-slate-500">Diverifikasi untuk pencocokan NIK & Identitas di SLIK OJK</p>
                    </div>
                </div>
                <button type="button" onclick="tutupModalKtpDebitur()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="py-4 flex-1 overflow-y-auto text-center">
                <?php if (!empty($ktp_dokumen) && !empty($ktp_dokumen['file_path'])): ?>
                    <?php 
                        $ext = strtolower(pathinfo($ktp_dokumen['file_path'], PATHINFO_EXTENSION));
                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                    ?>
                    <?php if ($isImg): ?>
                        <img src="<?= base_url($ktp_dokumen['file_path']) ?>" 
                             alt="KTP Debitur" 
                             class="max-h-[60vh] mx-auto rounded-2xl border border-slate-200 shadow-sm object-contain">
                    <?php else: ?>
                        <iframe src="<?= base_url($ktp_dokumen['file_path']) ?>" class="w-full h-[60vh] rounded-2xl border border-slate-200"></iframe>
                    <?php endif; ?>
                    <div class="mt-3 text-xs font-semibold text-slate-600">
                        <?= esc($ktp_dokumen['nama_dokumen'] ?? 'Berkas KTP') ?>
                    </div>
                <?php else: ?>
                    <div class="py-12 px-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/60">
                        <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 text-slate-300 flex items-center justify-center mx-auto mb-2 text-2xl">
                            🪪
                        </div>
                        <h4 class="text-xs font-bold text-slate-700">Berkas KTP Belum Diunggah</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Pengusul belum mengunggah file KTP pada Langkah 5 (Berkas Dokumen).</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                <span class="text-slate-400">NIK: <strong><?= esc($nikVal) ?></strong></span>
                <button type="button" onclick="tutupModalKtpDebitur()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 rounded-xl transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-slate-200 py-4 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-400">
            &copy; <?= date('Y') ?> <?= esc($namaWeb) ?> &bull; Sistem Verifikasi SLIK OJK Terintegrasi
        </div>
    </footer>

    <script>
        let indexSlik = <?= max(1, count($slik ?? [])) ?>;

        function formatRupiah(el) {
            let val = el.value.replace(/[^0-9]/g, '');
            if (val === '') {
                el.value = '';
                return;
            }
            el.value = new Intl.NumberFormat('id-ID').format(val);
        }

        function cleanNumber(val) {
            if (!val) return 0;
            let cleaned = val.toString().replace(/[^0-9]/g, '');
            return parseFloat(cleaned) || 0;
        }

        function hitungMetrikSlik() {
            let totalOutstanding = 0;
            let totalPlafon = 0;
            let jumlahFasilitas = 0;
            let maxKol = 1;
            const priorityMap = { 'Lancar': 1, 'DPK': 2, 'Kurang Lancar': 3, 'Diragukan': 4, 'Macet': 5 };
            const labelMap = { 
                1: { label: 'Kol 1 (Lancar)', badgeClass: 'bg-emerald-100 text-emerald-800' },
                2: { label: 'Kol 2 (DPK / Perhatian Khusus)', badgeClass: 'bg-amber-100 text-amber-900 font-bold' },
                3: { label: 'Kol 3 (Kurang Lancar)', badgeClass: 'bg-rose-100 text-rose-800 font-bold' },
                4: { label: 'Kol 4 (Diragukan)', badgeClass: 'bg-rose-100 text-rose-800 font-bold' },
                5: { label: 'Kol 5 (Macet)', badgeClass: 'bg-rose-600 text-white font-extrabold' }
            };

            const outstandingInputs = document.querySelectorAll('.slik-outstanding');
            const plafonInputs = document.querySelectorAll('.slik-plafon');
            const kolSelects = document.querySelectorAll('.slik-kol');

            outstandingInputs.forEach(input => {
                const val = cleanNumber(input.value);
                totalOutstanding += val;
                if (val > 0) jumlahFasilitas++;
            });

            plafonInputs.forEach(input => {
                totalPlafon += cleanNumber(input.value);
            });

            kolSelects.forEach(sel => {
                const p = priorityMap[sel.value] || 1;
                if (p > maxKol) maxKol = p;
            });

            // Update UI Total Outstanding
            const totalOutEl = document.getElementById('totalOutstandingSlik');
            if (totalOutEl) totalOutEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalOutstanding);

            // Update UI Total Plafon
            const totalPlEl = document.getElementById('totalPlafonSlik');
            if (totalPlEl) totalPlEl.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalPlafon);

            // Update UI Jumlah Fasilitas
            const totalFasEl = document.getElementById('jumlahFasilitasSlik');
            if (totalFasEl) totalFasEl.innerText = Math.max(outstandingInputs.length, jumlahFasilitas) + ' Fasilitas';

            // Update UI Kol Terburuk
            const badgeKolEl = document.getElementById('badgeKolTerburuk');
            if (badgeKolEl) {
                const info = labelMap[maxKol] || labelMap[1];
                badgeKolEl.innerHTML = `<span class="px-2.5 py-1 rounded-full text-xs font-bold ${info.badgeClass}">${info.label}</span>`;
            }
        }

        function tambahBarisSlik() {
            const tbody = document.getElementById('bodySlik');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][nama_debitur]" value="<?= esc($namaDebiturVal) ?>" placeholder="Debitur / Pasangan" 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][plafon]" placeholder="0" 
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-plafon" 
                           oninput="formatRupiah(this); hitungMetrikSlik();">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][outstanding]" placeholder="0" 
                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                           oninput="formatRupiah(this); hitungMetrikSlik();">
                </td>
                <td class="p-2.5 bg-white">
                    <select name="slik[${indexSlik}][kelancaran]" onchange="hitungMetrikSlik()"
                            class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer slik-kol">
                        <option value="Lancar">Kol 1 (Lancar)</option>
                        <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                        <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                        <option value="Diragukan">Kol 4 (Diragukan)</option>
                        <option value="Macet">Kol 5 (Macet)</option>
                    </select>
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                </td>
                <td class="p-2.5 bg-white text-center">
                    <button type="button" onclick="hapusBarisTabel(this); hitungMetrikSlik();" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            indexSlik++;
            hitungMetrikSlik();
        }

        function hapusBarisTabel(btn) {
            const tr = btn.closest('tr');
            const tbody = tr.parentNode;
            if (tbody.querySelectorAll('tr').length <= 1) {
                tr.querySelectorAll('input').forEach(inp => inp.value = '');
                tr.querySelector('select').value = 'Lancar';
            } else {
                tr.remove();
            }
            hitungMetrikSlik();
        }

        function previewUploadName(input, labelId) {
            const label = document.getElementById(labelId);
            if (input.files && input.files[0] && label) {
                label.innerText = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
            }
        }

        function salinTeks(teks, label) {
            if (!teks || teks === '-') {
                Swal.fire({ icon: 'warning', title: 'Data Kosong', text: `${label} belum terisi pada data nasabah ini.`, timer: 1500, showConfirmButton: false });
                return;
            }
            navigator.clipboard.writeText(teks).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: `${label} berhasil disalin: ${teks}`,
                    timer: 1200,
                    showConfirmButton: false
                });
            });
        }

        // Export File TXT Permohonan OJK
        function exportTxtPermohonanSlik() {
            const lineReff = "<?= esc($no_reff_slik ?? '') ?>";
            const filename = `Permohonan_SLIK_<?= esc($kredit['nomor_aplikasi'] ?? 'CAM') ?>.txt`;
            const content = lineReff + "\r\n";

            const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(() => {
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 200);

            Swal.fire({
                icon: 'success',
                title: 'Berkas TXT Berhasil Diunduh!',
                text: `File ${filename} siap diunggah ke portal resmi SLIK OJK.`,
                confirmButtonColor: '#2563eb'
            });
        }

        // Modal KTP
        function bukaModalKtpDebitur() {
            document.getElementById('modalKtpDebitur')?.classList.remove('hidden');
        }
        function tutupModalKtpDebitur() {
            document.getElementById('modalKtpDebitur')?.classList.add('hidden');
        }

        // Jalankan hitung total saat halaman siap
        document.addEventListener('DOMContentLoaded', () => {
            hitungMetrikSlik();
        });

        // Setujui Usulan Pengusul
        function setujuiUsulanPengusul(kreditId) {
            Swal.fire({
                title: 'Setujui Data Pengusul?',
                text: 'Seluruh riwayat fasilitas yang diisi oleh pengusul akan disahkan sebagai data resmi verifikasi SLIK OJK.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Setujui Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses Persetujuan...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('<?= base_url('slik/setujui-pengusul') ?>/' + kreditId, {
                        method: 'POST',
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
                            Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                    });
                }
            });
        }

        // Simpan Form SLIK
        function simpanFormSlik(e) {
            e.preventDefault();
            const form = document.getElementById('formVerifikasiSlik');
            const formData = new FormData(form);
            const btn = document.getElementById('btnSubmitSlik');

            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyimpan & Menerbitkan...</span>
            `;

            fetch('<?= base_url('slik/simpan') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan & Terbitkan Hasil SLIK</span>
                `;

                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Diterbitkan!',
                        text: data.message,
                        timer: 1600,
                        showConfirmButton: false
                    }).then(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '<?= base_url('dashboard') ?>';
                        }
                    });
                } else {
                    Swal.fire('Gagal Menyimpan', data.message || 'Periksa kembali data Anda.', 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan & Terbitkan Hasil SLIK</span>
                `;
                Swal.fire('Error', 'Gagal memproses permohonan ke server.', 'error');
            });
        }
    </script>
</body>
</html>
