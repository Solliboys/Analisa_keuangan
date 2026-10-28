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

        // Deteksi jenis debitur
        $rawJd = $kredit['jenis_debitur'] ?? 'Individu';
        $isBadanUsaha = (stripos($rawJd, 'PT') !== false || stripos($rawJd, 'CV') !== false || stripos($rawJd, 'Badan Usaha') !== false);
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

            <!-- Bidang Usaha (Muncul untuk PT / CV menggantikan Kepemilikan Rumah) -->
            <div id="wrapper_bidang_usaha" class="<?= $isBadanUsaha ? '' : 'hidden' ?>">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Bidang Usaha <span class="text-rose-500 font-extrabold">*</span>
                </label>
                <input type="text" name="jenis_usaha" id="jenis_usaha" 
                       value="<?= esc($kredit['jenis_usaha'] ?? '') ?>" 
                       placeholder="Contoh: Perdagangan Umum / Kontraktor / Jasa / Manufaktur"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
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

            <!-- NIK KTP / NPWP Debitur -->
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

<script>
function tambahBarisUsaha() {
    const container = document.getElementById('containerUsaha');
    const tmpl = document.getElementById('templateBarisUsaha');
    if (!container || !tmpl) return;

    const items = container.querySelectorAll('.card-usaha-item');
    const nextIdx = items.length;
    const nextNum = nextIdx + 1;

    let html = tmpl.innerHTML
        .replace(/__INDEX__/g, nextIdx)
        .replace(/__NUM__/g, nextNum);

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html.trim();
    const newCard = tempDiv.firstElementChild;
    container.appendChild(newCard);

    // Update label sesuai jenis debitur yang sedang aktif
    const jdVal = document.getElementById('jenis_debitur')?.value || 'Individu';
    const isBadan = (jdVal.includes('PT') || jdVal.includes('CV') || jdVal.includes('Badan Usaha'));
    updateCardUsahaLabels(newCard, isBadan, nextNum);

    // Smooth focus / scroll ke card baru
    newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    const firstInput = newCard.querySelector('input');
    if (firstInput) firstInput.focus();
}

function hapusBarisUsaha(btn) {
    const card = btn.closest('.card-usaha-item');
    if (!card) return;

    const container = document.getElementById('containerUsaha');
    card.remove();

    if (!container) return;
    const items = container.querySelectorAll('.card-usaha-item');
    const jdVal = document.getElementById('jenis_debitur')?.value || 'Individu';
    const isBadan = (jdVal.includes('PT') || jdVal.includes('CV') || jdVal.includes('Badan Usaha'));

    items.forEach((item, idx) => {
        const num = idx + 1;
        item.setAttribute('data-index', idx);

        const numEl = item.querySelector('.usaha-num');
        if (numEl) numEl.innerText = num;
        const numTitle = item.querySelector('.usaha-num-title');
        if (numTitle) numTitle.innerText = num;

        // Update semua nama input
        const inputs = item.querySelectorAll('input, textarea, select');
        inputs.forEach(inp => {
            if (inp.name) {
                inp.name = inp.name.replace(/usaha\[\d+\]/, `usaha[${idx}]`);
            }
        });

        updateCardUsahaLabels(item, isBadan, num);
    });
}

function updateCardUsahaLabels(card, isBadan, num) {
    const prefixTitle = card.querySelector('.usaha-prefix-title');
    if (prefixTitle) {
        prefixTitle.innerText = isBadan ? 'Unit Usaha / Perusahaan #' : 'Usaha / Sumber Penghasilan #';
    }

    const lblNamaUsaha = card.querySelector('.lbl-nama-usaha');
    const inpNamaUsaha = card.querySelector('.inp-nama-usaha');
    if (lblNamaUsaha && inpNamaUsaha) {
        if (isBadan) {
            lblNamaUsaha.innerText = 'Nama Perusahaan';
            inpNamaUsaha.placeholder = 'Contoh: PT Mandiri Sejahtera';
        } else {
            lblNamaUsaha.innerText = 'Nama Usaha atau Nama Perusahaan / Tempat Bekerja';
            inpNamaUsaha.placeholder = 'Nama tempat bekerja / Toko Berkah / Usaha Mandiri';
        }
    }

    const lblJabatan = card.querySelector('.lbl-jabatan');
    const inpJabatan = card.querySelector('.inp-jabatan');
    if (lblJabatan && inpJabatan) {
        if (isBadan) {
            lblJabatan.innerText = 'Jabatan Pemohon di Usaha';
            inpJabatan.placeholder = 'Direktur / Komisaris / General Manager';
        } else {
            lblJabatan.innerText = 'Jabatan / Status (Pemilik, Karyawan, dll.)';
            inpJabatan.placeholder = 'Pemilik / Karyawan / Pengelola';
        }
    }

    const lblLamaUsaha = card.querySelector('.lbl-lama-usaha');
    if (lblLamaUsaha) {
        lblLamaUsaha.innerText = isBadan ? 'Lama Usaha Berjalan' : 'Lama Usaha / Lama Bekerja';
    }

    const lblSumberBayar = card.querySelector('.lbl-sumber-bayar');
    const inpSumberBayar = card.querySelector('.inp-sumber-bayar');
    if (lblSumberBayar && inpSumberBayar) {
        if (isBadan) {
            lblSumberBayar.innerText = 'Sumber Utama Pembayaran (Repayment)';
            inpSumberBayar.placeholder = 'Hasil penjualan / invoice piutang / termin proyek';
        } else {
            lblSumberBayar.innerText = 'Sumber Utama Pembayaran (gaji atau hasil usaha)';
            inpSumberBayar.placeholder = 'Gaji bulanan / hasil usaha dagang / laba operasional';
        }
    }

    const lblAlamatUsaha = card.querySelector('.lbl-alamat-usaha');
    const inpAlamatUsaha = card.querySelector('.inp-alamat-usaha');
    if (lblAlamatUsaha && inpAlamatUsaha) {
        if (isBadan) {
            lblAlamatUsaha.innerText = 'Alamat Lokasi Tempat Usaha / Kantor Operasional';
            inpAlamatUsaha.placeholder = 'Alamat kantor / pabrik / workshop operasional perusahaan';
        } else {
            lblAlamatUsaha.innerText = 'Alamat Usaha / Tempat Bekerja';
            inpAlamatUsaha.placeholder = 'Alamat tempat usaha atau alamat kantor / tempat bekerja';
        }
    }

    const lblNpwp = card.querySelector('.lbl-npwp-usaha');
    if (lblNpwp) {
        lblNpwp.innerText = isBadan ? 'NPWP Usaha / Badan' : 'NPWP Usaha';
    }

    const lblNib = card.querySelector('.lbl-nib');
    if (lblNib) {
        lblNib.innerText = isBadan ? 'Nomor Induk Berusaha (NIB) / Izin' : 'NIB / Izin Usaha';
    }
}

function updateSemuaCardUsaha(isBadan) {
    const btnText = document.getElementById('btn_text_tambah_usaha');
    if (btnText) {
        btnText.innerText = isBadan ? 'Tambah Unit Usaha / Perusahaan' : 'Tambah Usaha / Pekerjaan Lain';
    }

    const container = document.getElementById('containerUsaha');
    if (!container) return;
    const items = container.querySelectorAll('.card-usaha-item');
    items.forEach((item, idx) => {
        updateCardUsahaLabels(item, isBadan, idx + 1);
    });
}

function ubahJenisDebitur(val) {
    if (!val) {
        val = document.getElementById('jenis_debitur')?.value || 'Individu';
    }
    const isBadan = (val.includes('PT') || val.includes('CV') || val.includes('Badan Usaha'));

    // 1. Toggle field Kepemilikan Rumah vs Bidang Usaha di Tab 2
    const wrapRumah = document.getElementById('wrapper_kepemilikan_rumah');
    const wrapUsaha = document.getElementById('wrapper_bidang_usaha');
    if (wrapRumah) {
        if (isBadan) wrapRumah.classList.add('hidden');
        else wrapRumah.classList.remove('hidden');
    }
    if (wrapUsaha) {
        if (isBadan) wrapUsaha.classList.remove('hidden');
        else wrapUsaha.classList.add('hidden');
    }

    // 2. Update semua kartu usaha di Tab 3
    updateSemuaCardUsaha(isBadan);
}

// Inisialisasi event listener dan status awal
document.addEventListener('DOMContentLoaded', () => {
    const jdVal = document.getElementById('jenis_debitur')?.value;
    if (jdVal) {
        ubahJenisDebitur(jdVal);
    }

    const ju = document.getElementById('jenis_usaha');
    const bu1 = document.querySelector('input[name="usaha[0][bidang_usaha]"]');
    if (ju && bu1) {
        ju.addEventListener('input', () => { bu1.value = ju.value; });
        bu1.addEventListener('input', () => { ju.value = bu1.value; });
    }
});
</script>