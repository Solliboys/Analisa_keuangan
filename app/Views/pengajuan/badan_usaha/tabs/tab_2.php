<?php
$listKaryawan = !empty($daftar_karyawan) && is_array($daftar_karyawan) ? $daftar_karyawan : [];
if (empty($listKaryawan)) {
    // Ambil data dari debitur yang sudah ada sebagai penanggung jawab utama
    $listKaryawan[] = [
        'nama'          => $kredit['nama_debitur'] ?? '',
        'jabatan'       => $kredit['jabatan'] ?? 'Direktur Utama',
        'nik'           => $kredit['npwp_pribadi'] ?? '',
        'no_hp'         => $kredit['nomor_hp'] ?? '',
        'tempat_lahir'  => $kredit['tempat_lahir'] ?? '',
        'tanggal_lahir' => $kredit['tanggal_lahir'] ?? '',
        'alamat'        => $kredit['alamat_ktp'] ?? '',
        'keterangan'    => 'Penanggung Jawab Utama',
    ];
}

$rawJd = $kredit['jenis_debitur'] ?? 'Badan Usaha (PT)';
$valNamaUsaha = $kredit['nama_usaha'] ?? ($kredit['nama_debitur'] ?? '');
?>

<div id="tab-2" class="tab-panel space-y-7">
    
    <!-- ========================================== -->
    <!-- BAGIAN 1 (PALING ATAS): INFORMASI USAHA    -->
    <!-- ========================================== -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        <!-- Section Header 2 -->
        <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    2
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">LEGALITAS & PROFIL PERUSAHAAN (INFO USAHA)</h3>
                    <p class="text-[11px] text-slate-500">Profil badan usaha, perizinan berusaha, akta pendirian, dan operasional bisnis utama</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                Wajib Diisi
            </span>
        </div>

        <!-- Grid Field Info Usaha -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- 1. Nama Perusahaan / Badan Usaha -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Nama Badan Usaha / Perusahaan (PT / CV / Koperasi / dll) <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="nama_usaha" id="nama_usaha_bu" 
                       value="<?= esc($valNamaUsaha) ?>" 
                       placeholder="Contoh: PT Sumber Pangan Makmur Abadi"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                       oninput="syncNamaUsahaKeDebitur(this.value)">
                <!-- Hidden input untuk kompatibilitas data kredit -->
                <input type="hidden" name="nama_debitur" id="nama_debitur_hidden" value="<?= esc($kredit['nama_debitur'] ?? $valNamaUsaha) ?>">
            </div>

            <!-- 2. Bentuk Badan Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Bentuk Badan Usaha <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <select name="jenis_debitur" id="jenis_debitur_bu" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                    <option value="Badan Usaha (PT)" <?= stripos($rawJd, 'PT') !== false ? 'selected' : '' ?>>PT (Perseroan Terbatas)</option>
                    <option value="Badan Usaha (CV)" <?= stripos($rawJd, 'CV') !== false ? 'selected' : '' ?>>CV (Persekutuan Komanditer)</option>
                    <option value="Badan Usaha (Koperasi)" <?= stripos($rawJd, 'Koperasi') !== false ? 'selected' : '' ?>>Koperasi</option>
                    <option value="Badan Usaha (Yayasan)" <?= stripos($rawJd, 'Yayasan') !== false ? 'selected' : '' ?>>Yayasan</option>
                    <option value="Badan Usaha (Firma)" <?= stripos($rawJd, 'Firma') !== false ? 'selected' : '' ?>>Firma</option>
                    <option value="Badan Usaha Lainnya" <?= (!stripos($rawJd, 'PT') && !stripos($rawJd, 'CV') && !stripos($rawJd, 'Koperasi') && !stripos($rawJd, 'Yayasan') && !stripos($rawJd, 'Firma')) ? 'selected' : '' ?>>Badan Usaha Lainnya</option>
                </select>
            </div>

            <!-- 3. Bidang Usaha / Sektor Industri -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Bidang Usaha / Sektor Industri <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="bidang_usaha" id="bidang_usaha_bu" 
                       value="<?= esc($kredit['bidang_usaha'] ?? ($kredit['jenis_usaha'] ?? '')) ?>" 
                       placeholder="Contoh: Perdagangan Hasil Bumi / Konstruksi / Manufaktur"
                       class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                <input type="hidden" name="jenis_usaha" id="jenis_usaha_bu" value="<?= esc($kredit['bidang_usaha'] ?? ($kredit['jenis_usaha'] ?? '')) ?>">
            </div>

            <!-- 4. Lama Berdiri / Operasional -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lama Berdiri / Masa Operasional (Tahun) <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="lama_usaha" id="lama_usaha_bu" 
                       value="<?= esc($kredit['lama_usaha'] ?? '') ?>" 
                       placeholder="Contoh: 7 Tahun"
                       class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 5. Status Kepemilikan Tempat Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Status Kepemilikan Tempat Usaha / Kantor
                </label>
                <?php $kep = $kredit['kepemilikan_domisili'] ?? 'Milik Sendiri'; ?>
                <select name="kepemilikan_domisili" id="kepemilikan_domisili_bu" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                    <option value="Milik Sendiri" <?= $kep === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                    <option value="Sewa / Kontrak" <?= $kep === 'Sewa / Kontrak' || $kep === 'Sewa' ? 'selected' : '' ?>>Sewa / Kontrak</option>
                    <option value="Milik Grup Perusahaan" <?= $kep === 'Milik Grup Perusahaan' ? 'selected' : '' ?>>Milik Grup Perusahaan</option>
                    <option value="Hak Guna Bangunan (HGB)" <?= $kep === 'Hak Guna Bangunan (HGB)' ? 'selected' : '' ?>>Hak Guna Bangunan (HGB)</option>
                    <option value="Lainnya" <?= $kep === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                </select>
            </div>

            <!-- 6. Alamat Lengkap Kantor / Domisili Usaha -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Alamat Lengkap Kantor / Pabrik / Tempat Usaha <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <textarea name="alamat_usaha" id="alamat_usaha_bu" rows="2" 
                          placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"
                          class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_usaha'] ?? ($kredit['alamat_domisili'] ?? '')) ?></textarea>
            </div>

            <!-- 7. Nomor Induk Berusaha (NIB) -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Nomor Induk Berusaha (NIB)
                </label>
                <input type="text" name="nomor_induk_berusaha_nib" id="nib_bu" 
                       value="<?= esc($kredit['nomor_induk_berusaha_nib'] ?? '') ?>" 
                       placeholder="13 Digit Nomor Induk Berusaha OSS"
                       class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 8. NPWP Badan Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    NPWP Badan Usaha / Perusahaan
                </label>
                <input type="text" name="npwp_usaha" id="npwp_usaha_bu" 
                       value="<?= esc($kredit['npwp_usaha'] ?? '') ?>" 
                       placeholder="15 atau 16 digit NPWP Perusahaan"
                       class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- 9. Telepon Kantor / Kontak Resmi -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    Nomor Telepon Kantor / Kontak Resmi Usaha
                </label>
                <input type="text" name="nomor_emergency" id="nomor_emergency_bu" 
                       value="<?= esc($kredit['nomor_emergency'] ?? '') ?>" 
                       placeholder="Contoh: (021) 555-1234 / 081234567890"
                       class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>
        </div>
    </div>


    <!-- ======================================================== -->
    <!-- BAGIAN 2 (BAWAHNYA): DAFTAR PENGURUS & KARYAWAN PERUSAHAAN-->
    <!-- ======================================================== -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        
        <!-- Header Section Daftar Karyawan + Tombol Tambah -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    👥
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DAFTAR PENGURUS & KARYAWAN PERUSAHAAN</h3>
                    <p class="text-[11px] text-slate-500">Susunan penanggung jawab, direktur, komisaris, atau karyawan utama yang mewakili badan usaha</p>
                </div>
            </div>

            <!-- Tombol Tambah Karyawan -->
            <button type="button" onclick="tambahBarisKaryawan()" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs hover:shadow-sm cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Pengurus / Karyawan</span>
            </button>
        </div>

        <!-- Container Daftar Karyawan Dinamis -->
        <div id="containerKaryawan" class="space-y-6">
            <?php foreach ($listKaryawan as $idx => $k): ?>
                <?php $isFirst = ($idx === 0); ?>
                <div class="card-karyawan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200 rounded-2xl p-5 sm:p-6 transition relative" data-index="<?= $idx ?>">
                    
                    <!-- Top Bar Card Karyawan -->
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-xs">
                                <span class="karyawan-num"><?= $idx + 1 ?></span>
                            </span>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wide uppercase">
                                Pengurus / Karyawan #<span class="karyawan-num-title"><?= $idx + 1 ?></span>
                                <span class="text-[11px] font-semibold text-indigo-600 ml-1.5 karyawan-badge">
                                    <?= $isFirst ? '(Penanggung Jawab Utama / Direktur)' : '(Personil Tambahan)' ?>
                                </span>
                            </h4>
                        </div>

                        <?php if ($isFirst): ?>
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-white border border-slate-200">
                                Wajib Diisi
                            </span>
                        <?php else: ?>
                            <button type="button" onclick="hapusBarisKaryawan(this)" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-200/70 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Hapus Karyawan
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Grid Form Field Karyawan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- 1. Nama Lengkap Sesuai KTP -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Nama Lengkap Sesuai KTP <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="karyawan[<?= $idx ?>][nama]" 
                                   value="<?= esc($k['nama'] ?? '') ?>" 
                                   placeholder="Nama lengkap tanpa singkatan gelar"
                                   class="inp-karyawan-nama w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"
                                   <?= $isFirst ? 'oninput="syncDirekturUtama(this.value)"' : '' ?>>
                        </div>

                        <!-- 2. Jabatan di Perusahaan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Jabatan di Perusahaan <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="karyawan[<?= $idx ?>][jabatan]" 
                                   value="<?= esc($k['jabatan'] ?? ($isFirst ? 'Direktur Utama' : '')) ?>" 
                                   placeholder="Contoh: Direktur Utama / Komisaris / Manajer Keuangan / Staff"
                                   class="inp-karyawan-jabatan w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
                        </div>

                        <!-- 3. NIK KTP (16 Digit) -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                NIK KTP (16 Digit Angka) <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="karyawan[<?= $idx ?>][nik]" 
                                   value="<?= esc($k['nik'] ?? '') ?>" 
                                   placeholder="Contoh: 3578012345670001" maxlength="16"
                                   class="inp-karyawan-nik w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"
                                   <?= $isFirst ? 'id="npwp_pribadi"' : '' ?>>
                        </div>

                        <!-- 4. Nomor HP / WhatsApp Aktif -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                Nomor HP / WhatsApp Aktif <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="karyawan[<?= $idx ?>][no_hp]" 
                                   value="<?= esc($k['no_hp'] ?? '') ?>" 
                                   placeholder="Contoh: 081234567890"
                                   class="inp-karyawan-hp w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"
                                   <?= $isFirst ? 'id="nomor_hp"' : '' ?>>
                        </div>

                        <!-- 5. Tempat Lahir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                Tempat Lahir
                            </label>
                            <input type="text" name="karyawan[<?= $idx ?>][tempat_lahir]" 
                                   value="<?= esc($k['tempat_lahir'] ?? '') ?>" 
                                   placeholder="Kota / Kabupaten Tempat Lahir"
                                   class="inp-karyawan-tempat-lahir w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"
                                   <?= $isFirst ? 'id="tempat_lahir"' : '' ?>>
                        </div>

                        <!-- 6. Tanggal Lahir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tanggal Lahir
                            </label>
                            <input type="date" name="karyawan[<?= $idx ?>][tanggal_lahir]" 
                                   value="<?= esc($k['tanggal_lahir'] ?? '') ?>" 
                                   class="inp-karyawan-tanggal-lahir w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"
                                   <?= $isFirst ? 'id="tanggal_lahir"' : '' ?>>
                        </div>

                        <!-- 7. Alamat Domisili / KTP Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Alamat KTP / Domisili Lengkap
                            </label>
                            <textarea name="karyawan[<?= $idx ?>][alamat]" rows="2" 
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"
                                      class="inp-karyawan-alamat w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"><?= esc($k['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Template HTML untuk Baris Karyawan Baru -->
<template id="templateKaryawan">
    <div class="card-karyawan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200 rounded-2xl p-5 sm:p-6 transition relative" data-index="__INDEX__">
        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200/80">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 font-bold text-xs">
                    <span class="karyawan-num">__NUM__</span>
                </span>
                <h4 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wide uppercase">
                    Pengurus / Karyawan #<span class="karyawan-num-title">__NUM__</span>
                    <span class="text-[11px] font-semibold text-indigo-600 ml-1.5 karyawan-badge">(Personil Tambahan)</span>
                </h4>
            </div>

            <button type="button" onclick="hapusBarisKaryawan(this)" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-200/70 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus Karyawan
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Nama Lengkap Sesuai KTP <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="karyawan[__INDEX__][nama]" 
                       placeholder="Nama lengkap tanpa singkatan gelar"
                       class="inp-karyawan-nama w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Jabatan di Perusahaan <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="karyawan[__INDEX__][jabatan]" 
                       placeholder="Contoh: Direktur Operasional / Komisaris / Manajer Keuangan / Staff"
                       class="inp-karyawan-jabatan w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    NIK KTP (16 Digit Angka) <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="karyawan[__INDEX__][nik]" 
                       placeholder="Contoh: 3578012345670002" maxlength="16"
                       class="inp-karyawan-nik w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Nomor HP / WhatsApp Aktif <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="karyawan[__INDEX__][no_hp]" 
                       placeholder="Contoh: 081234567891"
                       class="inp-karyawan-hp w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    Tempat Lahir
                </label>
                <input type="text" name="karyawan[__INDEX__][tempat_lahir]" 
                       placeholder="Kota / Kabupaten Tempat Lahir"
                       class="inp-karyawan-tempat-lahir w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Tanggal Lahir
                </label>
                <input type="date" name="karyawan[__INDEX__][tanggal_lahir]" 
                       class="inp-karyawan-tanggal-lahir w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs">
            </div>

            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Alamat KTP / Domisili Lengkap
                </label>
                <textarea name="karyawan[__INDEX__][alamat]" rows="2" 
                          placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"
                          class="inp-karyawan-alamat w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
    function syncNamaUsahaKeDebitur(val) {
        const debHid = document.getElementById('nama_debitur_hidden');
        if (debHid) debHid.value = val;
        const mainDeb = document.getElementById('nama_debitur');
        if (mainDeb) mainDeb.value = val;
    }

    function syncDirekturUtama(val) {
        const debHid = document.getElementById('nama_debitur_hidden');
        if (debHid && !document.getElementById('nama_usaha_bu')?.value) {
            debHid.value = val;
        }
    }

    function tambahBarisKaryawan() {
        const container = document.getElementById('containerKaryawan');
        const template = document.getElementById('templateKaryawan');
        if (!container || !template) return;

        const currentItems = container.querySelectorAll('.card-karyawan-item');
        const nextIndex = currentItems.length;
        const nextNum = nextIndex + 1;

        let html = template.innerHTML
            .replace(/__INDEX__/g, nextIndex)
            .replace(/__NUM__/g, nextNum);

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
        const newCard = tempDiv.firstChild;

        container.appendChild(newCard);

        // Berikan fokus pada nama karyawan yang baru ditambahkan
        const inputNama = newCard.querySelector('.inp-karyawan-nama');
        if (inputNama) inputNama.focus();
    }

    function hapusBarisKaryawan(btn) {
        const card = btn.closest('.card-karyawan-item');
        if (!card) return;

        const container = document.getElementById('containerKaryawan');
        const currentItems = container.querySelectorAll('.card-karyawan-item');
        if (currentItems.length <= 1) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Dapat Dihapus',
                    text: 'Minimal harus ada 1 penanggung jawab / pengurus perusahaan!',
                    confirmButtonColor: '#4f46e5'
                });
            } else {
                alert('Minimal harus ada 1 penanggung jawab / pengurus perusahaan!');
            }
            return;
        }

        card.remove();
        updateKaryawanIndices();
    }

    function updateKaryawanIndices() {
        const container = document.getElementById('containerKaryawan');
        if (!container) return;

        const items = container.querySelectorAll('.card-karyawan-item');
        items.forEach((item, index) => {
            const num = index + 1;
            item.setAttribute('data-index', index);

            const numSpan = item.querySelector('.karyawan-num');
            if (numSpan) numSpan.innerText = num;

            const numTitleSpan = item.querySelector('.karyawan-num-title');
            if (numTitleSpan) numTitleSpan.innerText = num;

            // Update name attributes
            item.querySelectorAll('input, select, textarea').forEach(el => {
                const name = el.getAttribute('name');
                if (name && name.startsWith('karyawan[')) {
                    el.setAttribute('name', name.replace(/karyawan\[\d+\]/, `karyawan[${index}]`));
                }
            });
        });
    }
</script>
