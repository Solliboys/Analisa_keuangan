<?php
if (!function_exists('getAgunanFieldConfig')) {
    function getAgunanFieldConfig($jenis) {
        if ($jenis === 'BPKB Mobil') {
            return [
                'is_kendaraan'   => true,
                'is_deposito'    => false,
                'is_sarusun'     => false,
                'hide_imb'       => true,
                'hide_tanah'     => true,
                'hide_bangunan'  => true,
                'lbl_alamat'     => 'Merk / Tipe Kendaraan & Plat Nomor',
                'ph_alamat'      => 'Contoh: Toyota Avanza 1.5 Veloz AT 2022 (Plat B 1234 CD)',
                'lbl_nomor'      => 'Nomor BPKB / No. Rangka Mesin',
                'ph_nomor'       => 'No. BPKB & No. Rangka Mesin',
                'lbl_pemilik'    => 'Nama Pemilik di BPKB / STNK',
                'ph_pemilik'     => 'Sesuai nama tercatat di BPKB/STNK',
                'lbl_lama'       => 'Tahun Pembuatan Kendaraan',
                'ph_lama'        => 'Contoh: Th. 2022 / 3 Tahun',
                'lbl_mv'         => 'Nilai Pasar Kendaraan / MV (Rp)',
                'default_lv_pct' => 50,
            ];
        } elseif ($jenis === 'Bilyet Deposito') {
            return [
                'is_kendaraan'   => false,
                'is_deposito'    => true,
                'is_sarusun'     => false,
                'hide_imb'       => true,
                'hide_tanah'     => true,
                'hide_bangunan'  => true,
                'lbl_alamat'     => 'Bank Penerbit & Kantor Cabang',
                'ph_alamat'      => 'Contoh: Bank BPR SA / Bank Mandiri KC Bandung',
                'lbl_nomor'      => 'Nomor Bilyet Deposito & No. Rekening',
                'ph_nomor'       => 'No. Seri Bilyet Deposito & No. Rekening',
                'lbl_pemilik'    => 'Nama Pemilik Rekening Bilyet',
                'ph_pemilik'     => 'Sesuai nama di Bilyet Deposito / KTP',
                'lbl_lama'       => 'Tenor & Tanggal Jatuh Tempo',
                'ph_lama'        => 'Contoh: 12 Bulan / Jatuh Tempo 15 Nov 2027',
                'lbl_mv'         => 'Nominal Pokok Deposito (Rp)',
                'default_lv_pct' => 100,
            ];
        } elseif ($jenis === 'SHM Sarusun') {
            return [
                'is_kendaraan'   => false,
                'is_deposito'    => false,
                'is_sarusun'     => true,
                'hide_imb'       => false,
                'hide_tanah'     => true,
                'hide_bangunan'  => false,
                'lbl_imb'        => 'Status Sertifikat Laik Fungsi (SLF)',
                'lbl_bangunan'   => 'Luas Unit Sarusun (m²)',
                'ph_bangunan'    => 'Contoh: 45',
                'lbl_alamat'     => 'Nama Apartemen, Tower, Unit & Alamat',
                'ph_alamat'      => 'Nama Apartemen, Tower, No. Lantai/Unit, Alamat Lengkap',
                'lbl_nomor'      => 'Nomor SHM Sarusun',
                'ph_nomor'       => 'No. Sertifikat Hak Milik Sarusun',
                'lbl_pemilik'    => 'Nama Pemilik di Sertifikat',
                'ph_pemilik'     => 'Sesuai nama di sertifikat',
                'lbl_lama'       => 'Lama Kepemilikan Unit',
                'ph_lama'        => 'Contoh: 5 Tahun',
                'lbl_mv'         => 'Nilai Pasar / MV (Rp)',
                'default_lv_pct' => 60,
            ];
        } else {
            // SHM / SHGB / Default Properti
            return [
                'is_kendaraan'   => false,
                'is_deposito'    => false,
                'is_sarusun'     => false,
                'hide_imb'       => false,
                'hide_tanah'     => false,
                'hide_bangunan'  => false,
                'lbl_imb'        => 'Status IMB / PBG',
                'lbl_tanah'      => 'Luas Tanah (m²)',
                'ph_tanah'       => 'Contoh: 120',
                'lbl_bangunan'   => 'Luas Bangunan (m²)',
                'ph_bangunan'    => 'Contoh: 85',
                'lbl_alamat'     => 'Alamat Lengkap Objek Jaminan',
                'ph_alamat'      => 'Alamat lokasi aset jaminan',
                'lbl_nomor'      => 'Nomor Sertifikat / BPKB',
                'ph_nomor'       => 'No. Sertifikat / BPKB',
                'lbl_pemilik'    => 'Nama Pemilik Agunan',
                'ph_pemilik'     => 'Sesuai nama di sertifikat',
                'lbl_lama'       => 'Lama Kepemilikan',
                'ph_lama'        => 'Contoh: 8 Tahun',
                'lbl_mv'         => 'Nilai Pasar / MV (Rp)',
                'default_lv_pct' => 60,
            ];
        }
    }
}
?>
<div id="tab-4" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                4
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">PENILAIAN AGUNAN & AGUNAN COVERAGE</h3>
                <p class="text-[11px] text-slate-500">Detail aset jaminan, nilai pasar (MV), likuidasi (LV), dan rasio penutupan terhadap plafon</p>
            </div>
        </div>
        <button type="button" onclick="tambahBarisAgunan()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition border border-blue-200/60 shadow-2xs">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Agunan
        </button>
    </div>

    <div class="space-y-5" id="containerAgunan">
        <?php if (empty($agunan)): ?>
            <?php 
                $ja = 'SHM';
                $cfg = getAgunanFieldConfig($ja);
            ?>
            <!-- Default 1 Baris Agunan -->
            <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                <!-- Item Top Bar -->
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </span>
                        <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num">1</span></span>
                    </div>
                    <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                    <div class="md:col-span-2 wrap-alamat-agunan">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_alamat']) ?></span>
                        </label>
                        <input type="text" name="agunan[0][alamat_agunan]" placeholder="<?= esc($cfg['ph_alamat']) ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Jenis Dokumen Agunan -->
                    <div class="wrap-jenis-agunan">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="lbl-text">Jenis Dokumen Agunan</span>
                        </label>
                        <select name="agunan[0][jenis_agunan]" onchange="sesuaikanFormAgunan(this.closest('.agunan-item'))" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                            <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                            <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                            <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                            <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                            <option value="Bilyet Deposito">Bilyet Deposito</option>
                        </select>
                    </div>

                    <!-- Nomor Sertifikat / BPKB -->
                    <div class="wrap-nomor-sertifikat">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_nomor']) ?></span>
                        </label>
                        <input type="text" name="agunan[0][nomor_sertifikat]" placeholder="<?= esc($cfg['ph_nomor']) ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Nama Pemilik Agunan -->
                    <div class="wrap-pemilik-agunan">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_pemilik']) ?></span>
                        </label>
                        <input type="text" name="agunan[0][pemilik_agunan]" placeholder="<?= esc($cfg['ph_pemilik']) ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Hubungan Kepemilikan -->
                    <div class="wrap-hubungan-kepemilikan">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="lbl-text">Hubungan Kepemilikan</span>
                        </label>
                        <select name="agunan[0][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                            <option value="Milik Sendiri">Milik Sendiri</option>
                            <option value="Suami / Istri">Suami / Istri</option>
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Pihak Ketiga">Pihak Ketiga</option>
                        </select>
                    </div>

                    <!-- Status IMB / PBG -->
                    <div class="wrap-status-imb <?= $cfg['hide_imb'] ? 'hidden' : '' ?>">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_imb'] ?? 'Status IMB / PBG') ?></span>
                        </label>
                        <select name="agunan[0][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                            <option value="Ada">Ada</option>
                            <option value="Tidak Ada">Tidak Ada</option>
                            <option value="Proses Pengurusan">Proses Pengurusan</option>
                        </select>
                    </div>

                    <!-- Lama Kepemilikan -->
                    <div class="wrap-lama-kepemilikan">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_lama']) ?></span>
                        </label>
                        <input type="text" name="agunan[0][lama_kepemilikan]" placeholder="<?= esc($cfg['ph_lama']) ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Luas Tanah (m²) -->
                    <div class="wrap-luas-tanah <?= $cfg['hide_tanah'] ? 'hidden' : '' ?>">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_tanah'] ?? 'Luas Tanah (m²)') ?></span>
                        </label>
                        <input type="number" name="agunan[0][luas_tanah_m2]" placeholder="<?= esc($cfg['ph_tanah'] ?? 'Contoh: 120') ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Luas Bangunan (m²) -->
                    <div class="wrap-luas-bangunan <?= $cfg['hide_bangunan'] ? 'hidden' : '' ?>">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_bangunan'] ?? 'Luas Bangunan (m²)') ?></span>
                        </label>
                        <input type="number" name="agunan[0][luas_bangunan_m2]" placeholder="<?= esc($cfg['ph_bangunan'] ?? 'Contoh: 85') ?>"
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </div>

                    <!-- Nilai Pasar / MV (Rp) -->
                    <div class="wrap-nilai-mv">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="lbl-text"><?= esc($cfg['lbl_mv']) ?></span>
                        </label>
                        <input type="text" name="agunan[0][nilai_pasar_mv]" placeholder="0"
                               class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv"
                               oninput="formatRupiah(this); hitungCoverageAgunan();">
                    </div>

                    <!-- Likuidasi (% & Nominal Nilai LV) -->
                    <div class="wrap-likuidasi">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            <span class="lbl-text">Likuidasi (% / Nilai LV)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative w-16 shrink-0">
                                <input type="number" step="1" name="agunan[0][persen_likuidasi]" value="<?= esc($cfg['default_lv_pct']) ?>"
                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct"
                                       oninput="hitungCoverageAgunan();">
                            </div>
                            <span class="text-xs text-slate-400 font-bold">%</span>
                            <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($agunan as $idx => $row): ?>
                <?php 
                    $ja = $row['jenis_agunan'] ?? 'SHM';
                    $cfg = getAgunanFieldConfig($ja);
                    $itemNum = $idx + 1;
                ?>
                <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                    <!-- Item Top Bar -->
                    <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num"><?= $itemNum ?></span></span>
                        </div>
                        <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                        <div class="md:col-span-2 wrap-alamat-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_alamat']) ?></span>
                            </label>
                            <input type="text" name="agunan[<?= $idx ?>][alamat_agunan]" value="<?= esc($row['alamat_agunan'] ?? '') ?>" placeholder="<?= esc($cfg['ph_alamat']) ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Jenis Dokumen Agunan -->
                        <div class="wrap-jenis-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="lbl-text">Jenis Dokumen Agunan</span>
                            </label>
                            <select name="agunan[<?= $idx ?>][jenis_agunan]" onchange="sesuaikanFormAgunan(this.closest('.agunan-item'))" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="SHM" <?= $ja === 'SHM' ? 'selected' : '' ?>>SHM (Sertifikat Hak Milik)</option>
                                <option value="SHGB" <?= $ja === 'SHGB' ? 'selected' : '' ?>>SHGB (Hak Guna Bangunan)</option>
                                <option value="SHM Sarusun" <?= $ja === 'SHM Sarusun' ? 'selected' : '' ?>>SHM Sarusun (Apartemen)</option>
                                <option value="BPKB Mobil" <?= $ja === 'BPKB Mobil' ? 'selected' : '' ?>>BPKB Kendaraan Roda 4</option>
                                <option value="Bilyet Deposito" <?= $ja === 'Bilyet Deposito' ? 'selected' : '' ?>>Bilyet Deposito</option>
                            </select>
                        </div>

                        <!-- Nomor Sertifikat / BPKB -->
                        <div class="wrap-nomor-sertifikat">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_nomor']) ?></span>
                            </label>
                            <input type="text" name="agunan[<?= $idx ?>][nomor_sertifikat]" value="<?= esc($row['nomor_sertifikat'] ?? '') ?>" placeholder="<?= esc($cfg['ph_nomor']) ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nama Pemilik Agunan -->
                        <div class="wrap-pemilik-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_pemilik']) ?></span>
                            </label>
                            <input type="text" name="agunan[<?= $idx ?>][pemilik_agunan]" value="<?= esc($row['pemilik_agunan'] ?? '') ?>" placeholder="<?= esc($cfg['ph_pemilik']) ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Hubungan Kepemilikan -->
                        <div class="wrap-hubungan-kepemilikan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="lbl-text">Hubungan Kepemilikan</span>
                            </label>
                            <select name="agunan[<?= $idx ?>][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <?php $hk = $row['hubungan_kepemilikan'] ?? 'Milik Sendiri'; ?>
                                <option value="Milik Sendiri" <?= $hk === 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                <option value="Suami / Istri" <?= $hk === 'Suami / Istri' ? 'selected' : '' ?>>Suami / Istri</option>
                                <option value="Orang Tua" <?= $hk === 'Orang Tua' ? 'selected' : '' ?>>Orang Tua</option>
                                <option value="Pihak Ketiga" <?= $hk === 'Pihak Ketiga' ? 'selected' : '' ?>>Pihak Ketiga</option>
                            </select>
                        </div>

                        <!-- Status IMB / PBG -->
                        <div class="wrap-status-imb <?= $cfg['hide_imb'] ? 'hidden' : '' ?>">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_imb'] ?? 'Status IMB / PBG') ?></span>
                            </label>
                            <select name="agunan[<?= $idx ?>][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <?php $si = $row['status_imb'] ?? 'Ada'; ?>
                                <option value="Ada" <?= $si === 'Ada' ? 'selected' : '' ?>>Ada</option>
                                <option value="Tidak Ada" <?= $si === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                                <option value="Proses Pengurusan" <?= $si === 'Proses Pengurusan' ? 'selected' : '' ?>>Proses Pengurusan</option>
                            </select>
                        </div>

                        <!-- Lama Kepemilikan -->
                        <div class="wrap-lama-kepemilikan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_lama']) ?></span>
                            </label>
                            <input type="text" name="agunan[<?= $idx ?>][lama_kepemilikan]" value="<?= esc($row['lama_kepemilikan'] ?? '') ?>" placeholder="<?= esc($cfg['ph_lama']) ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Tanah (m²) -->
                        <div class="wrap-luas-tanah <?= $cfg['hide_tanah'] ? 'hidden' : '' ?>">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_tanah'] ?? 'Luas Tanah (m²)') ?></span>
                            </label>
                            <input type="number" name="agunan[<?= $idx ?>][luas_tanah_m2]" value="<?= esc($row['luas_tanah_m2'] ?? '') ?>" placeholder="<?= esc($cfg['ph_tanah'] ?? 'Contoh: 120') ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Bangunan (m²) -->
                        <div class="wrap-luas-bangunan <?= $cfg['hide_bangunan'] ? 'hidden' : '' ?>">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_bangunan'] ?? 'Luas Bangunan (m²)') ?></span>
                            </label>
                            <input type="number" name="agunan[<?= $idx ?>][luas_bangunan_m2]" value="<?= esc($row['luas_bangunan_m2'] ?? '') ?>" placeholder="<?= esc($cfg['ph_bangunan'] ?? 'Contoh: 85') ?>"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nilai Pasar / MV (Rp) -->
                        <div class="wrap-nilai-mv">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text"><?= esc($cfg['lbl_mv']) ?></span>
                            </label>
                            <input type="text" name="agunan[<?= $idx ?>][nilai_pasar_mv]" 
                                   value="<?= number_format((float)($row['nilai_pasar_mv'] ?? 0), 0, ',', '.') ?>" 
                                   placeholder="0" class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv" 
                                   oninput="formatRupiah(this); hitungCoverageAgunan();">
                        </div>

                        <!-- Likuidasi (% & Nominal Nilai LV) -->
                        <div class="wrap-likuidasi">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span class="lbl-text">Likuidasi (% / Nilai LV)</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="relative w-16 shrink-0">
                                    <input type="number" step="1" name="agunan[<?= $idx ?>][persen_likuidasi]" 
                                           value="<?= esc($row['persen_likuidasi'] ?? $cfg['default_lv_pct']) ?>" 
                                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct" 
                                           oninput="hitungCoverageAgunan();">
                                </div>
                                <span class="text-xs text-slate-400 font-bold">%</span>
                                <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                    <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">
                                        Rp <?= number_format((float)($row['nilai_likuidasi_lv'] ?? 0), 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Ringkasan Evaluasi Agunan -->
    <div class="mt-6 p-4 sm:p-5 bg-blue-50/60 border border-blue-200/60 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Evaluasi Agunan</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-left sm:text-right w-full md:w-auto">
            <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                <div class="text-[11px] text-slate-500 font-medium">Nilai Pasar (MV)</div>
                <div id="totalAgunanMV" class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">Rp 0</div>
            </div>
            <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                <div class="text-[11px] text-slate-500 font-medium">Likuidasi (LV)</div>
                <div id="totalAgunanLV" class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">Rp 0</div>
            </div>
            <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                <div class="text-[11px] text-slate-500 font-medium">Coverage MV</div>
                <div id="coverageMV" class="text-xs sm:text-sm font-extrabold text-blue-600 mt-0.5">0.0%</div>
            </div>
            <div class="bg-white rounded-xl px-4 py-2.5 border border-blue-100 shadow-2xs">
                <div class="text-[11px] text-slate-500 font-medium">Coverage LV</div>
                <div id="coverageLV" class="text-xs sm:text-sm font-extrabold text-blue-700 mt-0.5">0.0%</div>
            </div>
        </div>
    </div>
</div>

<script>
function sesuaikanFormAgunan(item) {
    if (!item) return;
    const jenisSel = item.querySelector('select[name$="[jenis_agunan]"]');
    if (!jenisSel) return;
    const val = jenisSel.value;

    const wrapAlamat = item.querySelector('.wrap-alamat-agunan');
    const wrapNomor = item.querySelector('.wrap-nomor-sertifikat');
    const wrapPemilik = item.querySelector('.wrap-pemilik-agunan');
    const wrapImb = item.querySelector('.wrap-status-imb');
    const wrapLama = item.querySelector('.wrap-lama-kepemilikan');
    const wrapTanah = item.querySelector('.wrap-luas-tanah');
    const wrapBangunan = item.querySelector('.wrap-luas-bangunan');
    const wrapMv = item.querySelector('.wrap-nilai-mv');
    const inputLvPct = item.querySelector('.agunan-lv-pct');

    const setField = (wrap, labelText, placeholderText, isHidden) => {
        if (!wrap) return;
        if (isHidden) {
            wrap.classList.add('hidden');
        } else {
            wrap.classList.remove('hidden');
        }
        const lbl = wrap.querySelector('.lbl-text');
        if (lbl && labelText) lbl.innerText = labelText;
        const inp = wrap.querySelector('input, textarea');
        if (inp && placeholderText !== undefined && placeholderText !== '') inp.placeholder = placeholderText;
    };

    if (val === 'BPKB Mobil' || val.indexOf('BPKB') !== -1 || val.indexOf('Kendaraan') !== -1) {
        // HIDE Luas Tanah, Luas Bangunan, Status IMB
        setField(wrapImb, '', '', true);
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, '', '', true);

        // Ubah label & placeholder untuk BPKB Kendaraan
        setField(wrapAlamat, 'Merk / Tipe Kendaraan & Plat Nomor', 'Contoh: Toyota Avanza 1.5 Veloz AT 2022 (Plat B 1234 CD)', false);
        setField(wrapNomor, 'Nomor BPKB / No. Rangka Mesin', 'No. BPKB & No. Rangka Mesin', false);
        setField(wrapPemilik, 'Nama Pemilik di BPKB / STNK', 'Sesuai nama tercatat di BPKB/STNK', false);
        setField(wrapLama, 'Tahun Pembuatan Kendaraan', 'Contoh: Th. 2022 / 3 Tahun', false);
        setField(wrapMv, 'Nilai Pasar Kendaraan / MV (Rp)', '0', false);

        if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '100' || !inputLvPct.value)) {
            inputLvPct.value = '50';
            if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
        }
    } else if (val === 'Bilyet Deposito' || val.indexOf('Deposito') !== -1) {
        // HIDE Luas Tanah, Luas Bangunan, Status IMB
        setField(wrapImb, '', '', true);
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, '', '', true);

        // Ubah label & placeholder untuk Deposito
        setField(wrapAlamat, 'Bank Penerbit & Kantor Cabang', 'Contoh: Bank BPR SA / Bank Mandiri KC Bandung', false);
        setField(wrapNomor, 'Nomor Bilyet Deposito & No. Rekening', 'No. Seri Bilyet Deposito & No. Rekening', false);
        setField(wrapPemilik, 'Nama Pemilik Rekening Bilyet', 'Sesuai nama di Bilyet Deposito / KTP', false);
        setField(wrapLama, 'Tenor & Tanggal Jatuh Tempo', 'Contoh: 12 Bulan / Jatuh Tempo 15 Nov 2027', false);
        setField(wrapMv, 'Nominal Pokok Deposito (Rp)', '0', false);

        if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '50' || !inputLvPct.value)) {
            inputLvPct.value = '100';
            if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
        }
    } else if (val === 'SHM Sarusun' || val.indexOf('Sarusun') !== -1 || val.indexOf('Apartemen') !== -1) {
        // HIDE Luas Tanah, TAMPILKAN Luas Bangunan & IMB/SLF
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, 'Luas Unit Sarusun (m²)', 'Contoh: 45', false);
        setField(wrapImb, 'Status Sertifikat Laik Fungsi (SLF)', '', false);

        setField(wrapAlamat, 'Nama Apartemen, Tower, Unit & Alamat', 'Nama Apartemen, Tower, No. Lantai/Unit, Alamat Lengkap', false);
        setField(wrapNomor, 'Nomor SHM Sarusun', 'No. Sertifikat Hak Milik Sarusun', false);
        setField(wrapPemilik, 'Nama Pemilik di Sertifikat', 'Sesuai nama di sertifikat', false);
        setField(wrapLama, 'Lama Kepemilikan Unit', 'Contoh: 5 Tahun', false);
        setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
    } else {
        // SHM / SHGB / Default Properti
        setField(wrapTanah, 'Luas Tanah (m²)', 'Contoh: 120', false);
        setField(wrapBangunan, 'Luas Bangunan (m²)', 'Contoh: 85', false);
        setField(wrapImb, 'Status IMB / PBG', '', false);

        setField(wrapAlamat, 'Alamat Lengkap Objek Jaminan', 'Alamat lokasi aset jaminan', false);
        setField(wrapNomor, 'Nomor Sertifikat (SHM / SHGB)', 'No. Sertifikat / BPKB', false);
        setField(wrapPemilik, 'Nama Pemilik Agunan', 'Sesuai nama di sertifikat', false);
        setField(wrapLama, 'Lama Kepemilikan', 'Contoh: 8 Tahun', false);
        setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
    }
}
window.sesuaikanFormAgunan = sesuaikanFormAgunan;

function inisialisasiSemuaAgunan() {
    document.querySelectorAll('.agunan-item').forEach(function(item) {
        sesuaikanFormAgunan(item);
    });
    document.querySelectorAll('select[name$="[jenis_agunan]"]').forEach(function(sel) {
        sel.onchange = function() {
            sesuaikanFormAgunan(this.closest('.agunan-item'));
        };
    });
}

document.addEventListener('DOMContentLoaded', inisialisasiSemuaAgunan);
setTimeout(inisialisasiSemuaAgunan, 100);
setTimeout(inisialisasiSemuaAgunan, 500);

// Hook ke tambahBarisAgunan jika dipanggil
if (typeof window.tambahBarisAgunan === 'function') {
    var _oldTambah = window.tambahBarisAgunan;
    window.tambahBarisAgunan = function() {
        _oldTambah();
        setTimeout(inisialisasiSemuaAgunan, 50);
    };
}
</script>