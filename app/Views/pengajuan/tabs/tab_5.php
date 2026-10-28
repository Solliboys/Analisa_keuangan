<div id="tab-5" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            5
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DOKUMEN PERSYARATAN & BERKAS LEGALITAS</h3>
                            <p class="text-[11px] text-slate-500">Unggah berkas dokumen fisik pendukung pengajuan analisa kredit pemohon</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                        Format: PDF, JPG, PNG (Maks 10MB)
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Panel Kiri: Form Unggah Dokumen -->
                    <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 pb-3.5 mb-4 border-b border-slate-200/60">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-700 tracking-wide">Unggah Berkas Baru</span>
                            </div>

                            <!-- Pilih Kategori Dokumen -->
                            <div class="mb-4">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    Kategori Dokumen
                                </label>
                                <select id="kategoriUpload" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                    <option value="KTP & NPWP">KTP & NPWP Debitur / Pasangan</option>
                                    <option value="Sertifikat Agunan">Sertifikat Agunan / PBB / IMB</option>
                                    <option value="Rekening Koran">Rekening Koran 3-6 Bulan Terakhir</option>
                                    <option value="Legalitas Usaha">Legalitas Usaha (NIB / SIUP / TDP)</option>
                                    <option value="Laporan Keuangan">Laporan Keuangan & Pembukuan</option>
                                    <option value="Foto Usaha & Agunan">Foto Lokasi Usaha & Agunan</option>
                                    <option value="Dokumen Pendukung">Dokumen Pendukung Lainnya</option>
                                </select>
                            </div>

                            <!-- Area Dropzone / Picker Ringkas -->
                            <div onclick="document.getElementById('fileUploadInput').click()" 
                                 class="border border-dashed border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50/30 rounded-xl p-5 text-center cursor-pointer transition group">
                                <input type="file" id="fileUploadInput" class="hidden" onchange="prosesUploadDokumen(this)">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2.5 group-hover:scale-105 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <div class="text-xs font-bold text-slate-700">Pilih Berkas dari Komputer</div>
                                <div class="text-[11px] text-slate-400 mt-1">PDF, JPG, atau PNG (Maks. 10MB)</div>
                            </div>
                        </div>

                        <button type="button" onclick="document.getElementById('fileUploadInput').click()" 
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Pilih & Unggah File
                        </button>
                    </div>

                    <!-- Panel Kanan: Daftar Dokumen Terunggah -->
                    <div class="lg:col-span-2 bg-slate-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col">
                        <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </span>
                                <span class="text-xs font-bold text-slate-700 tracking-wide">Daftar Dokumen yang Telah Diunggah</span>
                            </div>
                            <span id="docCountBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">
                                <?= count($dokumen ?? []) ?> Dokumen
                            </span>
                        </div>

                        <div id="listDokumenTerunggah" class="space-y-2.5 flex-1 flex flex-col justify-center">
                            <?php if (empty($dokumen)): ?>
                                <div id="emptyDokumenState" class="py-12 px-4 border border-slate-200/80 rounded-xl bg-white text-center">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada berkas yang diunggah</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih kategori dan berkas di sebelah kiri untuk mengunggah dokumen pendukung.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($dokumen as $doc): ?>
                                    <?php
                                        $ext = strtoupper(pathinfo($doc['nama_file_asli'] ?? '', PATHINFO_EXTENSION) ?: 'DOC');
                                        $rawKat = trim($doc['kategori_dokumen'] ?? '');
                                        if (empty($rawKat) || $rawKat === 'lainnya') {
                                            $rawKat = 'KTP & NPWP';
                                        }
                                        $ukuranKb = number_format(($doc['ukuran_file_bytes'] ?? 0) / 1024, 1);
                                    ?>
                                    <div class="p-3.5 bg-white border border-slate-200/90 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs hover:border-blue-300 hover:shadow-xs transition" id="docRow_<?= $doc['dokumen_id'] ?>">
                                        <div class="flex items-start sm:items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-xs shrink-0 border border-blue-100">
                                                <?= $ext ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm leading-snug"><?= esc($doc['nama_file_asli']) ?></div>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                                        <span class="text-indigo-400 font-semibold">Kategori:</span>
                                                        <select onchange="ubahKategoriDokumen(<?= $doc['dokumen_id'] ?>, this.value)" class="bg-transparent font-extrabold text-indigo-800 focus:outline-none cursor-pointer">
                                                            <option value="KTP & NPWP" <?= strpos($rawKat, 'KTP') !== false ? 'selected' : '' ?>>KTP & NPWP</option>
                                                            <option value="Sertifikat Agunan" <?= strpos($rawKat, 'Agunan') !== false || strpos($rawKat, 'Sertifikat') !== false ? 'selected' : '' ?>>Sertifikat Agunan</option>
                                                            <option value="Rekening Koran" <?= strpos($rawKat, 'Rekening') !== false ? 'selected' : '' ?>>Rekening Koran</option>
                                                            <option value="Legalitas Usaha" <?= strpos($rawKat, 'Legalitas') !== false ? 'selected' : '' ?>>Legalitas Usaha</option>
                                                            <option value="Laporan Keuangan" <?= strpos($rawKat, 'Keuangan') !== false || strpos($rawKat, 'Neraca') !== false ? 'selected' : '' ?>>Laporan Keuangan</option>
                                                            <option value="Foto Usaha & Agunan" <?= strpos($rawKat, 'Foto') !== false ? 'selected' : '' ?>>Foto Usaha & Agunan</option>
                                                            <option value="Dokumen Pendukung" <?= strpos($rawKat, 'Pendukung') !== false || strpos($rawKat, 'Lainnya') !== false ? 'selected' : '' ?>>Dokumen Pendukung</option>
                                                        </select>
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 font-medium">
                                                        <?= $ukuranKb ?> KB
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <a href="<?= base_url($doc['path_file']) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-200 rounded-xl hover:bg-blue-50 transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span>Lihat File</span>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
