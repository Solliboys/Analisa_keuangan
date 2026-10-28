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
                                <div class="md:col-span-2">
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Alamat Lengkap Objek Jaminan
                                    </label>
                                    <input type="text" name="agunan[0][alamat_agunan]" placeholder="Alamat lokasi aset jaminan"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Jenis Dokumen Agunan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Jenis Dokumen Agunan
                                    </label>
                                    <select name="agunan[0][jenis_agunan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                        <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                        <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                        <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                        <option value="Bilyet Deposito">Bilyet Deposito</option>
                                    </select>
                                </div>

                                <!-- Nomor Sertifikat / BPKB -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                        Nomor Sertifikat / BPKB
                                    </label>
                                    <input type="text" name="agunan[0][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Nama Pemilik Agunan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Nama Pemilik Agunan
                                    </label>
                                    <input type="text" name="agunan[0][pemilik_agunan]" placeholder="Sesuai nama di sertifikat"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Hubungan Kepemilikan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Hubungan Kepemilikan
                                    </label>
                                    <select name="agunan[0][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="Milik Sendiri">Milik Sendiri</option>
                                        <option value="Suami / Istri">Suami / Istri</option>
                                        <option value="Orang Tua">Orang Tua</option>
                                        <option value="Pihak Ketiga">Pihak Ketiga</option>
                                    </select>
                                </div>

                                <!-- Status IMB / PBG -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Status IMB / PBG
                                    </label>
                                    <select name="agunan[0][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Proses Pengurusan">Proses Pengurusan</option>
                                    </select>
                                </div>

                                <!-- Lama Kepemilikan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Lama Kepemilikan
                                    </label>
                                    <input type="text" name="agunan[0][lama_kepemilikan]" placeholder="Contoh: 8 Tahun"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Luas Tanah (m²) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                        Luas Tanah (m²)
                                    </label>
                                    <input type="number" name="agunan[0][luas_tanah_m2]" placeholder="Contoh: 120"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Luas Bangunan (m²) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Luas Bangunan (m²)
                                    </label>
                                    <input type="number" name="agunan[0][luas_bangunan_m2]" placeholder="Contoh: 85"
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Nilai Pasar / MV (Rp) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Nilai Pasar / MV (Rp)
                                    </label>
                                    <input type="text" name="agunan[0][nilai_pasar_mv]" placeholder="0"
                                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv"
                                           oninput="formatRupiah(this); hitungCoverageAgunan();">
                                </div>

                                <!-- Likuidasi (% & Nominal Nilai LV) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                        Likuidasi (% / Nilai LV)
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative w-16 shrink-0">
                                            <input type="number" step="1" name="agunan[0][persen_likuidasi]" value="60"
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
                            <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                                <!-- Item Top Bar -->
                                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        </span>
                                        <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num"><?= $idx + 1 ?></span></span>
                                    </div>
                                    <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                                    <div class="md:col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Alamat Lengkap Objek Jaminan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][alamat_agunan]" value="<?= esc($row['alamat_agunan'] ?? '') ?>" placeholder="Alamat lokasi aset jaminan"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Jenis Dokumen Agunan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Jenis Dokumen Agunan
                                        </label>
                                        <select name="agunan[<?= $idx ?>][jenis_agunan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                            <?php $ja = $row['jenis_agunan'] ?? 'SHM'; ?>
                                            <option value="SHM" <?= $ja === 'SHM' ? 'selected' : '' ?>>SHM (Sertifikat Hak Milik)</option>
                                            <option value="SHGB" <?= $ja === 'SHGB' ? 'selected' : '' ?>>SHGB (Hak Guna Bangunan)</option>
                                            <option value="SHM Sarusun" <?= $ja === 'SHM Sarusun' ? 'selected' : '' ?>>SHM Sarusun (Apartemen)</option>
                                            <option value="BPKB Mobil" <?= $ja === 'BPKB Mobil' ? 'selected' : '' ?>>BPKB Kendaraan Roda 4</option>
                                            <option value="Bilyet Deposito" <?= $ja === 'Bilyet Deposito' ? 'selected' : '' ?>>Bilyet Deposito</option>
                                        </select>
                                    </div>

                                    <!-- Nomor Sertifikat / BPKB -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                            Nomor Sertifikat / BPKB
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][nomor_sertifikat]" value="<?= esc($row['nomor_sertifikat'] ?? '') ?>" placeholder="No. Sertifikat / BPKB"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Nama Pemilik Agunan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            Nama Pemilik Agunan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][pemilik_agunan]" value="<?= esc($row['pemilik_agunan'] ?? '') ?>" placeholder="Sesuai nama di sertifikat"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Hubungan Kepemilikan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Hubungan Kepemilikan
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
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Status IMB / PBG
                                        </label>
                                        <select name="agunan[<?= $idx ?>][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                            <?php $si = $row['status_imb'] ?? 'Ada'; ?>
                                            <option value="Ada" <?= $si === 'Ada' ? 'selected' : '' ?>>Ada</option>
                                            <option value="Tidak Ada" <?= $si === 'Tidak Ada' ? 'selected' : '' ?>>Tidak Ada</option>
                                            <option value="Proses Pengurusan" <?= $si === 'Proses Pengurusan' ? 'selected' : '' ?>>Proses Pengurusan</option>
                                        </select>
                                    </div>

                                    <!-- Lama Kepemilikan -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Lama Kepemilikan
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][lama_kepemilikan]" value="<?= esc($row['lama_kepemilikan'] ?? '') ?>" placeholder="Contoh: 8 Tahun"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Luas Tanah (m²) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                            Luas Tanah (m²)
                                        </label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_tanah_m2]" value="<?= esc($row['luas_tanah_m2'] ?? '') ?>" placeholder="Contoh: 120"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Luas Bangunan (m²) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                            Luas Bangunan (m²)
                                        </label>
                                        <input type="number" name="agunan[<?= $idx ?>][luas_bangunan_m2]" value="<?= esc($row['luas_bangunan_m2'] ?? '') ?>" placeholder="Contoh: 85"
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                    </div>

                                    <!-- Nilai Pasar / MV (Rp) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Nilai Pasar / MV (Rp)
                                        </label>
                                        <input type="text" name="agunan[<?= $idx ?>][nilai_pasar_mv]" 
                                               value="<?= number_format((float)($row['nilai_pasar_mv'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv" 
                                               oninput="formatRupiah(this); hitungCoverageAgunan();">
                                    </div>

                                    <!-- Likuidasi (% & Nominal Nilai LV) -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                            Likuidasi (% / Nilai LV)
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <div class="relative w-16 shrink-0">
                                                <input type="number" step="1" name="agunan[<?= $idx ?>][persen_likuidasi]" 
                                                       value="<?= esc($row['persen_likuidasi'] ?? 60) ?>" 
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
