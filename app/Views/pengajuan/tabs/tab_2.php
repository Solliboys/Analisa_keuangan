<div id="tab-2" class="tab-panel space-y-6">
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        
        <!-- Section Header B -->
        <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    2
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">INFORMASI PRIBADI & BADAN HUKUM PEMOHON</h3>
                    <p class="text-[11px] text-slate-500">Data identitas nasabah pemohon sesuai KTP / legalitas badan usaha</p>
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

        // Deteksi jenis debitur
        $rawJd = $kredit['jenis_debitur'] ?? 'Individu';
        $isPt = (stripos($rawJd, 'PT') !== false);
        $isCv = (stripos($rawJd, 'CV') !== false);
        $isBadanUsaha = ($isPt || $isCv || stripos($rawJd, 'Badan Usaha') !== false);
        $tipeAwal = 'Perorangan';
        if ($isPt) {
            $tipeAwal = 'PT';
        } elseif ($isCv) {
            $tipeAwal = 'CV';
        }
        ?>

        <!-- Grid Form Fields 2 Kolom Simetris -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Jenis Debitur (Dropdown disesuaikan persis seperti Gambar 2) -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Jenis Debitur
                </label>
                <select id="jenis_debitur_tab2" onchange="ubahJenisDebitur(this.value)" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    <option value="Individu" <?= (!$isBadanUsaha) ? 'selected' : '' ?>>Individu / Perorangan</option>
                    <option value="Badan Usaha (PT)" <?= ($isBadanUsaha && $isPt) ? 'selected' : '' ?>>Badan Usaha (PT)</option>
                    <option value="Badan Usaha (CV)" <?= ($isBadanUsaha && $isCv) ? 'selected' : '' ?>>Badan Usaha (CV)</option>
                </select>
            </div>

            <!-- Nama Lengkap Sesuai KTP / Nama Perusahaan -->
            <div>
                <label id="label_nama_debitur_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Nama Perusahaan (' . $tipeAwal . ')' : 'Nama Lengkap (Sesuai KTP)' ?></span> <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="nama_debitur" id="nama_debitur_tab2" 
                       value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                       placeholder="<?= $isBadanUsaha ? 'Contoh: ' . $tipeAwal . ' Maju Sejahtera Bersama' : 'Nama lengkap tanpa gelar singkatan' ?>"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                       oninput="document.getElementById('nama_debitur') ? document.getElementById('nama_debitur').value = this.value : null;">
            </div>

            <!-- Tempat Lahir / Pendirian -->
            <div>
                <label id="label_tempat_lahir_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Kota Pendirian / Kedudukan' : 'Tempat Lahir' ?></span>
                </label>
                <input type="text" name="tempat_lahir" id="tempat_lahir" 
                       value="<?= esc($valTempatLahir) ?>" 
                       placeholder="Contoh: Surabaya"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Tanggal Lahir / Tanggal Akta Pendirian -->
            <div>
                <label id="label_tanggal_lahir_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Tanggal Pendirian (Akta / SK)' : 'Tanggal Lahir' ?></span>
                </label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" 
                       value="<?= esc($valTanggalLahir) ?>" 
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Alamat KTP Lengkap / Alamat Legal Akta -->
            <div class="md:col-span-2">
                <label id="label_alamat_ktp_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Alamat Legal Sesuai Akta / Kedudukan Perusahaan' : 'Alamat KTP Lengkap' ?></span> <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <textarea name="alamat_ktp" rows="2" 
                          placeholder="<?= $isBadanUsaha ? 'Alamat domisili hukum kantor pusat / cabang' : 'Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kab, Provinsi' ?>"
                          class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_ktp'] ?? '') ?></textarea>
            </div>

            <!-- Alamat Domisili Sekarang / Kantor Operasional -->
            <div>
                <label id="label_alamat_domisili_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span><?= $isBadanUsaha ? 'Alamat Kantor / Operasional Usaha' : 'Alamat Domisili Sekarang' ?></span>
                </label>
                <textarea name="alamat_domisili" rows="2" 
                          placeholder="Kosongkan jika sama dengan alamat di atas"
                          class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_domisili'] ?? '') ?></textarea>
            </div>

            <!-- Kepemilikan Rumah Domisili (Hanya muncul jika Perorangan) -->
            <div id="wrapper_kepemilikan_rumah" class="<?= $isBadanUsaha ? 'hidden' : '' ?>">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Kepemilikan Rumah Domisili
                </label>
                <select name="kepemilikan_domisili" id="kepemilikan_domisili" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    <?php $kd = $kredit['kepemilikan_domisili'] ?? 'Milik Sendiri'; ?>
                    <option value="Milik Sendiri" <?= $kd === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                    <option value="Milik Keluarga / Orang Tua" <?= $kd === 'Milik Keluarga / Orang Tua' ? 'selected' : '' ?>>Milik Keluarga / Orang Tua</option>
                    <option value="Sewa / Kontrak" <?= $kd === 'Sewa / Kontrak' ? 'selected' : '' ?>>Sewa / Kontrak</option>
                    <option value="Rumah Dinas" <?= $kd === 'Rumah Dinas' ? 'selected' : '' ?>>Rumah Dinas</option>
                </select>
            </div>

            <!-- Jenis Usaha (Muncul untuk PT / CV menggantikan Kepemilikan Rumah) -->
            <div id="wrapper_jenis_usaha" class="<?= $isBadanUsaha ? '' : 'hidden' ?>">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Jenis Usaha / Sektor Usaha <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="jenis_usaha" id="jenis_usaha" 
                       value="<?= esc($kredit['jenis_usaha'] ?? '') ?>" 
                       placeholder="Contoh: Perdagangan Umum / Kontraktor / Jasa / Manufaktur"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Nomor HP / Telepon PIC -->
            <div>
                <label id="label_nomor_hp_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Nomor Telepon Kantor / HP PIC' : 'Nomor HP / WhatsApp Aktif' ?></span> <span class="text-rose-500 font-extrabold">*</span>
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
                    Nomor Kontak Darurat / PIC 2
                </label>
                <input type="text" name="nomor_emergency" id="nomor_emergency" 
                       value="<?= esc($kredit['nomor_emergency'] ?? '') ?>" 
                       placeholder="Nomor kontak darurat (Hanya angka)"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- NPWP Pribadi / NPWP Perusahaan -->
            <div>
                <label id="label_npwp_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span><?= $isBadanUsaha ? 'NPWP Perusahaan / NIK Direktur (16 Digit)' : 'NIK KTP / NPWP Debitur (16 Digit Angka)' ?></span> <span class="text-rose-500 font-extrabold">*</span>
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
                <label id="label_kontak_darurat_tab2" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span><?= $isBadanUsaha ? 'Nama PIC / Kontak Darurat Perusahaan' : 'Nama Kontak Darurat' ?></span>
                </label>
                <input type="text" name="nama_kontak_darurat" id="nama_kontak_darurat" 
                       value="<?= esc($kredit['nama_kontak_darurat'] ?? ($kredit['nama_ibu_kandung'] ?? '')) ?>" 
                       placeholder="<?= $isBadanUsaha ? 'Nama Direktur / Finance / PIC' : 'Nama lengkap kerabat / kontak darurat' ?>"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>
        </div>
    </div>
</div>

<script>
function ubahJenisDebitur(val) {
    const isBadan = (val.includes('PT') || val.includes('CV'));
    const isPt = val.includes('PT');
    const tipe = isPt ? 'PT' : (val.includes('CV') ? 'CV' : 'Perorangan');
    
    // Sync dropdown di Tab 1
    const jdTab1 = document.getElementById('jenis_debitur');
    if (jdTab1 && jdTab1.value !== val) {
        jdTab1.value = val;
    }
    // Sync dropdown di Tab 2
    const jdTab2 = document.getElementById('jenis_debitur_tab2');
    if (jdTab2 && jdTab2.value !== val) {
        jdTab2.value = val;
    }

    // Toggle field Kepemilikan Rumah vs Jenis Usaha
    const wrapRumah = document.getElementById('wrapper_kepemilikan_rumah');
    const wrapUsaha = document.getElementById('wrapper_jenis_usaha');
    if (isBadan) {
        if (wrapRumah) wrapRumah.classList.add('hidden');
        if (wrapUsaha) wrapUsaha.classList.remove('hidden');
    } else {
        if (wrapRumah) wrapRumah.classList.remove('hidden');
        if (wrapUsaha) wrapUsaha.classList.add('hidden');
    }

    // Update label & placeholder adaptif
    const labelNama = document.querySelector('#label_nama_debitur_tab2 span');
    const inputNama = document.getElementById('nama_debitur_tab2');
    if (labelNama && inputNama) {
        if (isBadan) {
            labelNama.innerText = `Nama Perusahaan (${tipe})`;
            inputNama.placeholder = `Contoh: ${tipe} Maju Sejahtera Bersama`;
        } else {
            labelNama.innerText = 'Nama Lengkap (Sesuai KTP)';
            inputNama.placeholder = 'Nama lengkap tanpa gelar singkatan';
        }
    }

    const labelTempat = document.querySelector('#label_tempat_lahir_tab2 span');
    if (labelTempat) labelTempat.innerText = isBadan ? 'Kota Pendirian / Kedudukan' : 'Tempat Lahir';

    const labelTgl = document.querySelector('#label_tanggal_lahir_tab2 span');
    if (labelTgl) labelTgl.innerText = isBadan ? 'Tanggal Pendirian (Akta / SK)' : 'Tanggal Lahir';

    const labelKtp = document.querySelector('#label_alamat_ktp_tab2 span');
    if (labelKtp) labelKtp.innerText = isBadan ? 'Alamat Legal Sesuai Akta / Kedudukan Perusahaan' : 'Alamat KTP Lengkap';

    const labelDom = document.querySelector('#label_alamat_domisili_tab2 span');
    if (labelDom) labelDom.innerText = isBadan ? 'Alamat Kantor / Operasional Usaha' : 'Alamat Domisili Sekarang';

    const labelNpwp = document.querySelector('#label_npwp_tab2 span');
    if (labelNpwp) labelNpwp.innerText = isBadan ? 'NPWP Perusahaan / NIK Direktur (16 Digit)' : 'NIK KTP / NPWP Debitur (16 Digit Angka)';

    const labelKontak = document.querySelector('#label_kontak_darurat_tab2 span');
    if (labelKontak) labelKontak.innerText = isBadan ? 'Nama PIC / Kontak Darurat Perusahaan' : 'Nama Kontak Darurat';
}

function pilihTipeDebitur(tipe) {
    if (tipe === 'PT') ubahJenisDebitur('Badan Usaha (PT)');
    else if (tipe === 'CV') ubahJenisDebitur('Badan Usaha (CV)');
    else ubahJenisDebitur('Individu');
}
</script>