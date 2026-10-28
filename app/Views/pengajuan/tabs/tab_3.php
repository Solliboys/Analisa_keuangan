<?php
$rawJd = $kredit['jenis_debitur'] ?? 'Individu';
$isPt = (stripos($rawJd, 'PT') !== false);
$isCv = (stripos($rawJd, 'CV') !== false);
$isBadanUsaha = ($isPt || $isCv || stripos($rawJd, 'Badan Usaha') !== false);

// Siapkan data daftar usaha: jika kosong, ambil dari data $kredit
$listUsaha = !empty($daftar_usaha) ? $daftar_usaha : [];
if (empty($listUsaha)) {
    $listUsaha[] = [
        'is_utama'                 => 1,
        'nama_usaha'               => $kredit['nama_usaha'] ?? '',
        'bidang_usaha'             => $kredit['bidang_usaha'] ?? ($kredit['jenis_usaha'] ?? ''),
        'jabatan'                  => $kredit['jabatan'] ?? '',
        'lama_usaha'               => $kredit['lama_usaha'] ?? '',
        'sumber_pembayaran'        => $kredit['sumber_pembayaran'] ?? '',
        'alamat_usaha'             => $kredit['alamat_usaha'] ?? '',
        'npwp_usaha'               => $kredit['npwp_usaha'] ?? '',
        'nomor_induk_berusaha_nib' => $kredit['nomor_induk_berusaha_nib'] ?? '',
    ];
}
?>
<div id="tab-3" class="tab-panel space-y-6">
    <!-- Header Tab 3 & Tombol Tambah Usaha -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    3
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">PROFIL KEGIATAN USAHA & OPERASIONAL</h3>
                    <p class="text-[11px] text-slate-500">Profil operasional unit usaha penghasil arus kas pembayaran pinjaman (bisa lebih dari 1 usaha)</p>
                </div>
            </div>
            
            <button type="button" onclick="tambahBarisUsaha()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs hover:shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                <span id="btn_text_tambah_usaha"><?= $isBadanUsaha ? 'Tambah Unit Usaha / Perusahaan' : 'Tambah Usaha / Pekerjaan Lain' ?></span>
            </button>
        </div>

        <!-- Container Daftar Usaha Dinamis -->
        <div id="containerUsaha" class="space-y-6 mt-6">
            <?php foreach ($listUsaha as $idx => $u): ?>
                <?php $isFirst = ($idx === 0); ?>
                <div class="card-usaha-item bg-slate-50/50 hover:bg-slate-50/80 border border-slate-200 rounded-2xl p-5 sm:p-6 transition relative" data-index="<?= $idx ?>">
                    <!-- Baris Header Card Usaha -->
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-100 text-blue-700 font-bold text-xs">
                                <span class="usaha-num"><?= $idx + 1 ?></span>
                            </span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wide uppercase">
                                <span class="usaha-prefix-title"><?= $isBadanUsaha ? 'Unit Usaha / Perusahaan #' : 'Usaha / Sumber Penghasilan #' ?></span><span class="usaha-num-title"><?= $idx + 1 ?></span>
                                <span class="text-[11px] font-semibold text-blue-600 ml-1.5 usaha-status-badge">(<?= $isFirst ? 'Utama' : 'Tambahan' ?>)</span>
                            </h4>
                        </div>

                        <?php if ($isFirst): ?>
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-white border border-slate-200">
                                Wajib Diisi
                            </span>
                        <?php else: ?>
                            <button type="button" onclick="hapusBarisUsaha(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-200/70">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Usaha
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Grid Form 8 Field Sesuai Spesifikasi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- 1. Nama Usaha / Nama Perusahaan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="lbl-nama-usaha"><?= $isBadanUsaha ? 'Nama Perusahaan' : 'Nama Usaha atau Nama Perusahaan / Tempat Bekerja' ?></span>
                                <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][nama_usaha]" 
                                   value="<?= esc($u['nama_usaha'] ?? '') ?>" 
                                   placeholder="<?= $isBadanUsaha ? 'Contoh: PT Mandiri Sejahtera' : 'Nama tempat bekerja / Toko Berkah / Usaha Mandiri' ?>"
                                   class="inp-nama-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- 2. Bidang Usaha -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span>Bidang Usaha</span>
                                <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][bidang_usaha]" 
                                   value="<?= esc($u['bidang_usaha'] ?? '') ?>" 
                                   placeholder="Contoh: Perdagangan / Jasa / F&B / Industri / Karyawan"
                                   class="inp-bidang-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- 3. Jabatan / Status -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="lbl-jabatan"><?= $isBadanUsaha ? 'Jabatan Pemohon di Usaha' : 'Jabatan / Status (Pemilik, Karyawan, dll.)' ?></span>
                                <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][jabatan]" 
                                   value="<?= esc($u['jabatan'] ?? '') ?>" 
                                   placeholder="<?= $isBadanUsaha ? 'Direktur / Komisaris / General Manager' : 'Pemilik / Karyawan / Pengelola' ?>"
                                   class="inp-jabatan w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- 4. Lama Usaha / Lama Bekerja -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-lama-usaha"><?= $isBadanUsaha ? 'Lama Usaha Berjalan' : 'Lama Usaha / Lama Bekerja' ?></span>
                                <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][lama_usaha]" 
                                   value="<?= esc($u['lama_usaha'] ?? '') ?>" 
                                   placeholder="Contoh: 5 Tahun"
                                   class="inp-lama-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- 5. Alamat Usaha / Tempat Bekerja -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="lbl-alamat-usaha"><?= $isBadanUsaha ? 'Alamat Lokasi Tempat Usaha / Kantor Operasional' : 'Alamat Usaha / Tempat Bekerja' ?></span>
                                <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <textarea name="usaha[<?= $idx ?>][alamat_usaha]" rows="2" 
                                      placeholder="<?= $isBadanUsaha ? 'Alamat kantor / pabrik / workshop operasional perusahaan' : 'Alamat tempat usaha atau alamat kantor / tempat bekerja' ?>"
                                      class="inp-alamat-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($u['alamat_usaha'] ?? '') ?></textarea>
                        </div>

                        <!-- 7. NPWP Usaha -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="lbl-npwp-usaha"><?= $isBadanUsaha ? 'NPWP Usaha / Badan' : 'NPWP Usaha' ?></span>
                                <span class="text-slate-400 text-[10px] font-normal lowercase">(opsional)</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][npwp_usaha]" 
                                   value="<?= esc($u['npwp_usaha'] ?? '') ?>" 
                                   placeholder="Nomor Pokok Wajib Pajak Usaha (jika ada)"
                                   class="inp-npwp-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- 8. NIB / Izin Usaha -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <span class="lbl-nib"><?= $isBadanUsaha ? 'Nomor Induk Berusaha (NIB) / Izin' : 'NIB / Izin Usaha' ?></span>
                                <span class="text-slate-400 text-[10px] font-normal lowercase">(opsional)</span>
                            </label>
                            <input type="text" name="usaha[<?= $idx ?>][nomor_induk_berusaha_nib]" 
                                   value="<?= esc($u['nomor_induk_berusaha_nib'] ?? '') ?>" 
                                   placeholder="NIB OSS RBA / SKU / Izin Usaha"
                                   class="inp-nib w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


</div>

<!-- Template Card Usaha Baru (Hidden) -->
<template id="templateBarisUsaha">
    <div class="card-usaha-item bg-slate-50/50 hover:bg-slate-50/80 border border-slate-200 rounded-2xl p-5 sm:p-6 transition relative" data-index="__INDEX__">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200/80">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-100 text-blue-700 font-bold text-xs">
                    <span class="usaha-num">__NUM__</span>
                </span>
                <h4 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wide uppercase">
                    <span class="usaha-prefix-title">Usaha / Sumber Penghasilan #</span><span class="usaha-num-title">__NUM__</span>
                    <span class="text-[11px] font-semibold text-blue-600 ml-1.5 usaha-status-badge">(Tambahan)</span>
                </h4>
            </div>

            <button type="button" onclick="hapusBarisUsaha(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-200/70">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus Usaha
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Nama Usaha / Nama Perusahaan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="lbl-nama-usaha">Nama Usaha atau Nama Perusahaan / Tempat Bekerja</span>
                    <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="usaha[__INDEX__][nama_usaha]" 
                       placeholder="Nama tempat bekerja / Toko Berkah / Usaha Mandiri"
                       class="inp-nama-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 2. Bidang Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>Bidang Usaha</span>
                    <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="usaha[__INDEX__][bidang_usaha]" 
                       placeholder="Contoh: Perdagangan / Jasa / F&B / Industri / Karyawan"
                       class="inp-bidang-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 3. Jabatan / Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="lbl-jabatan">Jabatan / Status (Pemilik, Karyawan, dll.)</span>
                    <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="usaha[__INDEX__][jabatan]" 
                       placeholder="Pemilik / Karyawan / Pengelola"
                       class="inp-jabatan w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 4. Lama Usaha / Lama Bekerja -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="lbl-lama-usaha">Lama Usaha / Lama Bekerja</span>
                    <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="usaha[__INDEX__][lama_usaha]" 
                       placeholder="Contoh: 5 Tahun"
                       class="inp-lama-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 5. Alamat Usaha / Tempat Bekerja -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="lbl-alamat-usaha">Alamat Usaha / Tempat Bekerja</span>
                    <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <textarea name="usaha[__INDEX__][alamat_usaha]" rows="2" 
                          placeholder="Alamat tempat usaha atau alamat kantor / tempat bekerja"
                          class="inp-alamat-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"></textarea>
            </div>

            <!-- 7. NPWP Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="lbl-npwp-usaha">NPWP Usaha</span>
                    <span class="text-slate-400 text-[10px] font-normal lowercase">(opsional)</span>
                </label>
                <input type="text" name="usaha[__INDEX__][npwp_usaha]" 
                       placeholder="Nomor Pokok Wajib Pajak Usaha (jika ada)"
                       class="inp-npwp-usaha w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 8. NIB / Izin Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="lbl-nib">NIB / Izin Usaha</span>
                    <span class="text-slate-400 text-[10px] font-normal lowercase">(opsional)</span>
                </label>
                <input type="text" name="usaha[__INDEX__][nomor_induk_berusaha_nib]" 
                       placeholder="NIB OSS RBA / SKU / Izin Usaha"
                       class="inp-nib w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>
        </div>
    </div>
</template>