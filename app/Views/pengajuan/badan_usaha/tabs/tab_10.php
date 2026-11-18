<?php
$otsDocs = [];
if (!empty($dokumen) && is_array($dokumen)) {
    foreach ($dokumen as $d) {
        $kat = strtolower($d['kategori_dokumen'] ?? '');
        $cat = strtolower($d['catatan_dokumen'] ?? '');
        if (strpos($kat, 'ots') !== false || strpos($cat, 'ots') !== false || strpos($kat, 'kunjungan') !== false) {
            $otsDocs[] = $d;
        }
    }
}
?>
<div id="tab-10" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header (Badan Usaha) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                10
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">OPINI PENGUSUL (BADAN USAHA / PERUSAHAAN)</h3>
                <p class="text-[11px] text-slate-500">Evaluasi komprehensif pengusul atas kapasitas yuridis & manajerial, kelayakan model bisnis PT/CV, kinerja operasional komersial, agunan perusahaan, dan mitigasi risiko fasilitas kredit</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 shadow-2xs self-start sm:self-auto">
            Opini Pengusul • Badan Usaha
        </span>
    </div>

    <div class="space-y-6">
        <!-- 1. Analisa Agunan Badan Usaha -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Analisa Kualitas, Legalitas & Marketability Agunan Perusahaan
                </label>
                <div class="relative inline-block text-left dropdown-narasi-container">
                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_jaminan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition shadow-2xs cursor-pointer">
                        <span>ℹ️ Bantuan Narasi</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="menu_narasi_jaminan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Agunan Perusahaan:</div>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'positif'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Komersial Prima / Legal Clear)
                        </button>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'netral'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Standar Industri / Perlu Rekomendasi)
                        </button>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'negatif'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Risiko Zonasi / Likuiditas Terbatas)
                        </button>
                    </div>
                </div>
            </div>
            <textarea id="analisa_jaminan" name="analisa_jaminan" rows="3" placeholder="Kesesuaian kepemilikan agunan atas nama PT/CV atau organ pengurus, kesesuaian izin peruntukan tata ruang kawasan komersial/industri, kondisi fisik aset, dan tingkat likuiditas lelang pasar..."
                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisa_jaminan'] ?? '') ?></textarea>
        </div>

        <!-- 2. Business Review / Manajemen & Pasar Perusahaan -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Business Review, Struktur Manajemen & Prospek Pasar PT / CV
                </label>
                <div class="relative inline-block text-left dropdown-narasi-container">
                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_review')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition shadow-2xs cursor-pointer">
                        <span>ℹ️ Bantuan Narasi</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="menu_narasi_review" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Bisnis Korporasi:</div>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'positif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Kinerja B2B Solid & Pasar Bertumbuh)
                        </button>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'netral'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Kompetisi Ketat / Margin Standar)
                        </button>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'negatif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Ketergantungan Pembeli Tunggal)
                        </button>
                    </div>
                </div>
            </div>
            <textarea id="business_review" name="business_review" rows="3" placeholder="Model operasional bisnis B2B/B2C, reputasi dewan direksi/komisaris, diversifikasi portofolio klien korporat, rantai pasok supplier, daya saing di industri, dan kontinuitas PO/kontrak kerja..."
                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_review'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 3. Catatan OTS Kantor & Fasilitas Perusahaan -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Kunjungan Kantor, Gudang / Pabrik (OTS Perusahaan)
                    </label>
                    <div class="relative inline-block text-left dropdown-narasi-container">
                        <button type="button" onclick="toggleDropdownNarasi('menu_narasi_ots')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition shadow-2xs cursor-pointer">
                            <span>ℹ️ Bantuan Narasi</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="menu_narasi_ots" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                            <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi OTS Perusahaan:</div>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'positif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Pabrik/Gudang Aktif & Tertata)
                            </button>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'netral'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Operasional Wajar / Kapasitas Standar)
                            </button>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'negatif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Utilisasi Mesin Rendah / Stok Tertahan)
                            </button>
                        </div>
                    </div>
                </div>
                <textarea id="business_catatan" name="business_catatan" rows="3" placeholder="Hasil observasi fisik dan survei On The Spot (OTS) di kantor pusat, gudang inventaris, atau workshop pabrik: dinamika kerja karyawan, kesiapan mesin produksi, dan keteraturan arsip administrasi..."
                          class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_catatan'] ?? '') ?></textarea>

                <!-- Area Bukti Upload Foto / Dokumen Kunjungan OTS -->
                <div class="mt-3.5 pt-3.5 border-t border-slate-200/80">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Bukti Foto / Dokumen OTS
                            </label>
                        </div>
                        <input type="file" id="inputUploadOts" class="hidden" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple onchange="uploadBuktiOts(this)">
                        <button type="button" id="btnUploadOts" onclick="document.getElementById('inputUploadOts').click()" 
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition shadow-2xs cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Unggah Bukti</span>
                        </button>
                    </div>

                    <!-- Dropzone Ringkas -->
                    <div onclick="document.getElementById('inputUploadOts').click()" 
                         class="border border-dashed border-indigo-200 hover:border-indigo-400 bg-indigo-50/20 hover:bg-indigo-50/50 rounded-xl p-3 text-center cursor-pointer transition group">
                        <p class="text-[11px] font-semibold text-slate-600 group-hover:text-indigo-700 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <span>Klik untuk pilih foto / dokumen kunjungan OTS</span>
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Mendukung format JPG, PNG, WEBP, atau PDF (Maks. 10MB per file)</p>
                    </div>

                    <!-- List Bukti OTS yang sudah diupload -->
                    <div id="listBuktiOts" class="space-y-2 mt-2.5">
                        <div id="emptyBuktiOts" class="text-center py-3 px-2 border border-slate-100 rounded-xl bg-slate-50/50 <?= !empty($otsDocs) ? 'hidden' : '' ?>">
                            <p class="text-[11px] text-slate-400">Belum ada foto atau dokumen bukti kunjungan OTS yang diunggah.</p>
                        </div>
                        <?php if (!empty($otsDocs)): ?>
                            <?php foreach ($otsDocs as $doc): ?>
                                <?php
                                    $ext = strtoupper(pathinfo($doc['nama_file_asli'] ?? '', PATHINFO_EXTENSION) ?: 'FILE');
                                    $isImg = in_array($ext, ['JPG', 'JPEG', 'PNG', 'WEBP']);
                                    $ukuranKb = number_format(($doc['ukuran_file_bytes'] ?? 0) / 1024, 1);
                                    $namaFileDisplay = $doc['nama_file_asli'] ?? 'Bukti OTS';
                                ?>
                                <div id="otsDocRow_<?= $doc['dokumen_id'] ?>" class="ots-doc-item flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-xl hover:border-indigo-300 transition shadow-2xs text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                            <?php if ($isImg): ?>
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <?php else: ?>
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <?php endif; ?>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-800 text-[11px] truncate max-w-[170px] sm:max-w-xs" title="<?= esc($namaFileDisplay) ?>"><?= esc($namaFileDisplay) ?></p>
                                            <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200/60">Terunggah &bull; <?= $ukuranKb ?> KB</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" onclick="hapusBuktiOts(<?= $doc['dokumen_id'] ?>)" class="inline-flex items-center justify-center p-1.5 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition cursor-pointer" title="Hapus Bukti OTS">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        <button type="button" onclick="previewDokumenModal('<?= base_url($doc['path_file']) ?>', '<?= esc(addslashes($namaFileDisplay)) ?>', 'Bukti Kunjungan OTS')" class="inline-flex items-center gap-1 px-2.5 py-1 text-indigo-600 hover:text-indigo-800 font-bold text-[11px] bg-indigo-50/60 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <span>Lihat</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 4. Rincian Kebutuhan Modal Kerja Perusahaan -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Rincian Kebutuhan Modal Kerja & Rekayasa Cashflow Proyek
                    </label>
                    <div class="relative inline-block text-left dropdown-narasi-container">
                        <button type="button" onclick="toggleDropdownNarasi('menu_narasi_kebutuhan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold rounded-lg border border-indigo-200 transition shadow-2xs cursor-pointer">
                            <span>ℹ️ Bantuan Narasi</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="menu_narasi_kebutuhan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                            <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Kebutuhan Korporasi:</div>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'positif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Pengadaan Bahan Baku Skala Besar)
                            </button>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'netral'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Perkuatan Modal Kerja Piutang Usaha)
                            </button>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'negatif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Cash Gap Tinggi / Siklus Piutang Lambat)
                            </button>
                        </div>
                    </div>
                </div>
                <textarea id="detail_kebutuhan" name="detail_kebutuhan" rows="3" placeholder="Rencana alokasi pembiayaan: pembelian bahan baku massal untuk efisiensi harga distributor, pemenuhan PO/kontrak proyek, penutupan cash gap termin piutang (AR 30-60 hari)..."
                          class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['detail_kebutuhan'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Hidden fields untuk menjaga konsistensi data scoring & rekomendasi -->
        <input type="hidden" name="skor_kelayakan_ai" id="skor_kelayakan_ai" value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>">
        <input type="hidden" name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" value="<?= esc($analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh') ?>">
        <input type="hidden" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" value="<?= esc($analisa['rekomendasi_plafon_ai'] ?? 500000000) ?>">
        <input type="hidden" name="analisis_risiko_ai" id="analisis_risiko_ai" value="<?= esc($analisa['analisis_risiko_ai'] ?? '') ?>">
    </div>
</div>

