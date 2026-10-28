<div id="tab-2" class="tab-panel space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                    
                    <!-- Section Header B -->
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                                2
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">INFORMASI PRIBADI CALON DEBITUR</h3>
                                <p class="text-[11px] text-slate-500">Data identitas nasabah pemohon sesuai KTP dan verifikasi kependudukan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                            Wajib Diisi
                        </span>
                    </div>

                    <?php
$valTempatLahir = $kredit['tempat_lahir'] ?? '';
$valTanggalLahir = $kredit['tanggal_lahir'] ?? '';
if (empty($valTempatLahir) && empty($valTanggalLahir) && !empty($kredit['tempat_tanggal_lahir'])) {
    $ttlParts = explode(',', $kredit['tempat_tanggal_lahir'], 2);
    $valTempatLahir = trim($ttlParts[0] ?? '');
    if (!empty($ttlParts[1])) {
        $parsedTime = strtotime(trim($ttlParts[1]));
        if ($parsedTime !== false) {
            $valTanggalLahir = date('Y-m-d', $parsedTime);
        }
    }
}
?>
                    <!-- Grid Form Fields 2 Kolom Simetris -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Lengkap Sesuai KTP -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Nama Lengkap (Sesuai KTP) <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="nama_debitur" id="nama_debitur_tab2" 
                                   value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                                   placeholder="Nama lengkap tanpa gelar singkatan"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                                   oninput="document.getElementById('nama_debitur') ? document.getElementById('nama_debitur').value = this.value : null;">
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Tempat Lahir
                            </label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" 
                                   value="<?= esc($valTempatLahir) ?>" 
                                   placeholder="Contoh: Surabaya"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tanggal Lahir
                            </label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" 
                                   value="<?= esc($valTanggalLahir) ?>" 
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Alamat KTP Lengkap -->
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Alamat KTP Lengkap <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <textarea name="alamat_ktp" rows="2" 
                                      placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kab, Provinsi"
                                      class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_ktp'] ?? '') ?></textarea>
                        </div>

                        <!-- Alamat Domisili Sekarang -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                Alamat Domisili Sekarang
                            </label>
                            <textarea name="alamat_domisili" rows="2" 
                                      placeholder="Kosongkan jika sama dengan alamat KTP"
                                      class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_domisili'] ?? '') ?></textarea>
                        </div>

                        <!-- Kepemilikan Rumah Domisili -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Kepemilikan Rumah Domisili
                            </label>
                            <select name="kepemilikan_domisili" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                <?php $kd = $kredit['kepemilikan_domisili'] ?? 'Milik Sendiri'; ?>
                                <option value="Milik Sendiri" <?= $kd === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                <option value="Milik Keluarga / Orang Tua" <?= $kd === 'Milik Keluarga / Orang Tua' ? 'selected' : '' ?>>Milik Keluarga / Orang Tua</option>
                                <option value="Sewa / Kontrak" <?= $kd === 'Sewa / Kontrak' ? 'selected' : '' ?>>Sewa / Kontrak</option>
                                <option value="Rumah Dinas" <?= $kd === 'Rumah Dinas' ? 'selected' : '' ?>>Rumah Dinas</option>
                            </select>
                        </div>

                        <!-- Nomor HP / WhatsApp Aktif -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Nomor HP / WhatsApp Aktif <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="nomor_hp" id="nomor_hp" 
                                   value="<?= esc($kredit['nomor_hp'] ?? '') ?>" 
                                   placeholder="081234567xxx (Hanya angka)"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nomor Emergency / Kontak Darurat -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Nomor Emergency / Kontak Darurat
                            </label>
                            <input type="text" name="nomor_emergency" id="nomor_emergency" 
                                   value="<?= esc($kredit['nomor_emergency'] ?? '') ?>" 
                                   placeholder="Nomor kerabat (Hanya angka)"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- NPWP Pribadi -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                NIK KTP / NPWP Debitur (16 Digit Angka) <span class="text-rose-500 font-extrabold">*</span>
                            </label>
                            <input type="text" name="npwp_pribadi" id="npwp_pribadi" 
                                   value="<?= esc($kredit['npwp_pribadi'] ?? '') ?>" 
                                   placeholder="Contoh: 3578012345670001 (16 digit angka)"
                                   maxlength="16"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nama Kontak Darurat -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Nama Kontak Darurat
                            </label>
                            <input type="text" name="nama_kontak_darurat" id="nama_kontak_darurat" 
                                   value="<?= esc($kredit['nama_kontak_darurat'] ?? ($kredit['nama_ibu_kandung'] ?? '')) ?>" 
                                   placeholder="Nama lengkap kerabat / kontak darurat"
                                   class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>
                    </div>
                </div>
            </div>
