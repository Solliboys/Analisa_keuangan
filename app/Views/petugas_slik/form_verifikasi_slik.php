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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

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
?>

    <!-- Topbar Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <div class="flex items-center gap-4">
                    <a href="<?= base_url('dashboard') ?>" 
                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:text-blue-700 hover:bg-blue-50 hover:border-blue-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Dashboard</span>
                    </a>

                    <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>

                    <div class="hidden md:flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aplikasi:</span>
                        <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                            <?= esc($kredit['nomor_aplikasi'] ?? '-') ?>
                        </span>
                        <span class="text-xs font-semibold text-slate-700 truncate max-w-xs">
                            <?= esc($kredit['nama_debitur'] ?? 'Debitur') ?>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <span class="text-[11px] block font-bold text-slate-400 uppercase">Petugas Verifikator</span>
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

        <!-- Debitur Quick Info Card -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 mb-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-extrabold text-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                            <?= esc($kredit['nama_debitur'] ?: 'Nama Debitur Belum Diisi') ?>
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            <?= esc($kredit['nomor_aplikasi']) ?>
                        </span>
                        <?php if (($kredit['status_slik'] ?? '') === 'Terbit'): ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                SLIK Terbit
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Belum Terbit
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Jenis Usaha: <strong class="text-slate-700"><?= esc($kredit['nama_usaha'] ?: '-') ?></strong> &bull; 
                        Plafon Dimohon: <strong class="text-blue-700">Rp <?= number_format((float)($kredit['plafon_dimohon'] ?? 0), 0, ',', '.') ?></strong> &bull; 
                        Tanggal Pengajuan: <?= date('d M Y', strtotime($kredit['created_at'])) ?>
                    </p>
                </div>
            </div>

            <!-- Petugas SLIK quick badge -->
            <div class="flex items-center gap-2 bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-200 self-start md:self-auto">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-700">Mode Verifikasi SLIK OJK Resmi</span>
            </div>
        </div>

        <!-- Banner Usulan Pengusul (Jika Pengusul telah menginput data) -->
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

        <!-- FORMULIR SLIK PERSIS DENGAN GAMBAR USER -->
        <form id="formVerifikasiSlik" onsubmit="simpanFormSlik(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="kredit_id" value="<?= esc($kredit_id) ?>">

            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-6">
                
                <!-- Section Header (Langkah 6 Slik) -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            6
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">RIWAYAT FASILITAS KREDIT (SLIK OJK)</h2>
                            <p class="text-[11px] text-slate-500">Pencatatan fasilitas pinjaman aktif di perbankan atau lembaga keuangan lain</p>
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
                                <th class="py-3.5 px-3 min-w-[150px]">Bank / Lembaga</th>
                                <th class="py-3.5 px-3 min-w-[130px]">Plafon (Rp)</th>
                                <th class="py-3.5 px-3 min-w-[140px]">Baki Debet / Outstanding</th>
                                <th class="py-3.5 px-3 min-w-[160px]">Kualitas / Kol</th>
                                <th class="py-3.5 px-3 min-w-[170px]">Keterangan / Tujuan</th>
                                <th class="py-3.5 px-2 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs" id="bodySlik">
                            <?php if (empty($slik)): ?>
                                <tr>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][nama_debitur]" value="<?= esc($kredit['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][plafon]" placeholder="0" 
                                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                                               oninput="formatRupiah(this)">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <input type="text" name="slik[0][outstanding]" placeholder="0" 
                                               class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                               oninput="formatRupiah(this); hitungTotalSlik();">
                                    </td>
                                    <td class="p-2.5 bg-white">
                                        <select name="slik[0][kelancaran]" 
                                                class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
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
                                        <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($slik as $idx => $s): ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][nama_debitur]" value="<?= esc($s['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][bank_lembaga]" value="<?= esc($s['bank_lembaga'] ?? '') ?>" placeholder="Nama Bank / Leasing" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][plafon]" value="<?= number_format((float)($s['plafon'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                                                   oninput="formatRupiah(this)">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][outstanding]" value="<?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                                   oninput="formatRupiah(this); hitungTotalSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <select name="slik[<?= $idx ?>][kelancaran]" 
                                                    class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
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
                                            <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
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

                <!-- Total Baki Debet Summary Box (Persis Gambar User) -->
                <div class="mt-4 p-4 bg-blue-50/60 border border-blue-200/60 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">TOTAL BAKI DEBET TERHUTANG (SLIK)</span>
                    </div>
                    <div class="text-base sm:text-lg font-extrabold text-blue-700" id="totalOutstandingSlik">
                        Rp 0
                    </div>
                </div>

                <!-- Ringkasan & Catatan Historis SLIK OJK (Persis Gambar User) -->
                <div class="mt-6">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        RINGKASAN & CATATAN HISTORIS SLIK OJK
                    </label>
                    <textarea name="ringkasan_slik" rows="3" 
                              placeholder="Tuliskan kesimpulan profil karakter kredit nasabah, riwayat tunggakan (jika ada), serta rencana take over / pelunasan..."
                              class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs resize-y"><?= esc($kredit['ringkasan_slik'] ?? '') ?></textarea>
                </div>

                <!-- Status Penerbitan Hasil SLIK -->
                <div class="mt-6 pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-slate-700 block">Status Hasil Pemeriksaan SLIK:</span>
                        <span class="text-[11px] text-slate-400">Pilih status apakah data fasilitas ini sudah resmi terverifikasi dan diterbitkan.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-emerald-200 bg-emerald-50/60 cursor-pointer">
                            <input type="radio" name="status_slik" value="Terbit" class="text-emerald-600 focus:ring-emerald-500" <?= ($kredit['status_slik'] ?? 'Terbit') === 'Terbit' ? 'checked' : '' ?>>
                            <span class="text-xs font-bold text-emerald-800">Terbitkan Resmi (Terbit)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                            <input type="radio" name="status_slik" value="Belum Terbit" class="text-slate-600 focus:ring-slate-500" <?= ($kredit['status_slik'] ?? '') === 'Belum Terbit' ? 'checked' : '' ?>>
                            <span class="text-xs font-bold text-slate-700">Simpan Draf (Belum Terbit)</span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Action Bar Bawah -->
            <div class="flex items-center justify-between gap-4 py-4">
                <a href="<?= base_url('dashboard') ?>" 
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                    Batal
                </a>

                <div class="flex items-center gap-3">
                    <button type="submit" id="btnSubmitSlik"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-98 text-white rounded-xl text-xs font-extrabold transition shadow-md cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan & Terbitkan Hasil SLIK</span>
                    </button>
                </div>
            </div>

        </form>

    </main>

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

        function hitungTotalSlik() {
            let total = 0;
            const inputs = document.querySelectorAll('.slik-outstanding');
            inputs.forEach(input => {
                total += cleanNumber(input.value);
            });
            const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            const totalEl = document.getElementById('totalOutstandingSlik');
            if (totalEl) {
                totalEl.innerText = formatted;
            }
        }

        function tambahBarisSlik() {
            const tbody = document.getElementById('bodySlik');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][nama_debitur]" value="<?= esc($kredit['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][plafon]" placeholder="0" 
                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                           oninput="formatRupiah(this)">
                </td>
                <td class="p-2.5 bg-white">
                    <input type="text" name="slik[${indexSlik}][outstanding]" placeholder="0" 
                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                           oninput="formatRupiah(this); hitungTotalSlik();">
                </td>
                <td class="p-2.5 bg-white">
                    <select name="slik[${indexSlik}][kelancaran]" 
                            class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
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
                    <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            indexSlik++;
        }

        function hapusBarisTabel(btn) {
            const tr = btn.closest('tr');
            const tbody = tr.parentNode;
            if (tbody.querySelectorAll('tr').length <= 1) {
                // Jangan hapus baris terakhir, kosongkan saja inputnya
                tr.querySelectorAll('input').forEach(inp => inp.value = '');
                tr.querySelector('select').value = 'Lancar';
            } else {
                tr.remove();
            }
        }

        // Jalankan hitung total saat halaman siap
        document.addEventListener('DOMContentLoaded', () => {
            hitungTotalSlik();
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
                <span>Menyimpan Data...</span>
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
                        title: 'Berhasil!',
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
