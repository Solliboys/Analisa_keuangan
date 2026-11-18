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
    <!-- Section Header (Perorangan) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                11
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">OPINI PENGUSUL (DEBITUR PERORANGAN)</h3>
                <p class="text-[11px] text-slate-500">Evaluasi kualitatif dan pertimbangan pengusul terkait karakter debitur perorangan, kelayakan usaha/profesi, kondisi agunan, dan kapasitas pengembalian kredit</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Opini Pengusul • Perorangan
        </span>
    </div>

    <div class="space-y-6">
        <!-- 1. Analisa Agunan Perorangan -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Analisa Kualitas & Marketability Jaminan (Agunan Pribadi / Keluarga)
                </label>
                <div class="relative inline-block text-left dropdown-narasi-container">
                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_jaminan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                        <span>ℹ️ Bantuan Narasi</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="menu_narasi_jaminan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Agunan Pribadi:</div>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'positif'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Strategis / Aman)
                        </button>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'netral'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Cukup Terawat)
                        </button>
                        <button type="button" onclick="terapkanNarasi('analisa_jaminan', 'analisa_jaminan', 'negatif'); toggleDropdownNarasi('menu_narasi_jaminan')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Akses Sempit / Berisiko)
                        </button>
                    </div>
                </div>
            </div>
            <textarea id="analisa_jaminan" name="analisa_jaminan" rows="3" placeholder="Lokasi strategis agunan pribadi, kemudahan akses jalan, kondisi fisik bangunan/tanah, status legalitas SHM/SHGB atas nama debitur/pasangan, dan estimasi waktu likuidasi..."
                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisa_jaminan'] ?? '') ?></textarea>
        </div>

        <!-- 2. Business Review / Karakter & Penghasilan Perorangan -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Business Review & Analisa Sumber Penghasilan / Karakter Pemohon
                </label>
                <div class="relative inline-block text-left dropdown-narasi-container">
                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_review')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                        <span>ℹ️ Bantuan Narasi</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="menu_narasi_review" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Karakter & Usaha:</div>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'positif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Karakter Baik & Stabil)
                        </button>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'netral'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Penghasilan Cukup)
                        </button>
                        <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'negatif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Omset Fluktuatif)
                        </button>
                    </div>
                </div>
            </div>
            <textarea id="business_review" name="business_review" rows="3" placeholder="Model usaha atau profesi pemohon, reputasi karakter nasabah di lingkungan, stabilitas omset/penghasilan bulanan, dan kontinuitas arus kas keluarga debitur..."
                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_review'] ?? '') ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 3. Catatan OTS Domisili & Usaha Perorangan -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Kunjungan Domisili & Usaha / Kerja (OTS)
                    </label>
                    <div class="relative inline-block text-left dropdown-narasi-container">
                        <button type="button" onclick="toggleDropdownNarasi('menu_narasi_ots')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                            <span>ℹ️ Bantuan Narasi</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="menu_narasi_ots" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                            <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi OTS Perorangan:</div>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'positif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Kondisi Nyata Baik)
                            </button>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'netral'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Berjalan Normal)
                            </button>
                            <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'negatif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Tempat Sepi / Kurang Aktif)
                            </button>
                        </div>
                    </div>
                </div>
                <textarea id="business_catatan" name="business_catatan" rows="3" placeholder="Hasil observasi fisik dan wawancara On The Spot (OTS) di rumah tinggal/domisili pemohon serta tempat usaha atau kantor bekerja..."
                          class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_catatan'] ?? '') ?></textarea>

                <!-- Area Bukti Upload Foto / Dokumen Kunjungan OTS -->
                <div class="mt-3.5 pt-3.5 border-t border-slate-200/80">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Bukti Foto / Dokumen OTS
                            </label>
                        </div>
                        <input type="file" id="inputUploadOts" class="hidden" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple onchange="uploadBuktiOts(this)">
                        <button type="button" id="btnUploadOts" onclick="document.getElementById('inputUploadOts').click()" 
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Unggah Bukti</span>
                        </button>
                    </div>

                    <!-- Dropzone Ringkas -->
                    <div onclick="document.getElementById('inputUploadOts').click()" 
                         class="border border-dashed border-blue-200 hover:border-blue-400 bg-blue-50/20 hover:bg-blue-50/50 rounded-xl p-3 text-center cursor-pointer transition group">
                        <p class="text-[11px] font-semibold text-slate-600 group-hover:text-blue-700 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
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
                                <div id="otsDocRow_<?= $doc['dokumen_id'] ?>" class="ots-doc-item flex items-center justify-between p-2.5 bg-white border border-slate-200 rounded-xl hover:border-blue-300 transition shadow-2xs text-xs">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0">
                                            <?php if ($isImg): ?>
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
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
                                        <button type="button" onclick="previewDokumenModal('<?= base_url($doc['path_file']) ?>', '<?= esc(addslashes($namaFileDisplay)) ?>', 'Bukti Kunjungan OTS')" class="inline-flex items-center gap-1 px-2.5 py-1 text-blue-600 hover:text-blue-800 font-bold text-[11px] bg-blue-50/60 hover:bg-blue-100 border border-blue-200 rounded-lg transition cursor-pointer">
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

            <!-- 4. Rincian Kebutuhan Kredit Perorangan -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Rincian Kebutuhan Modal Kerja / Pembiayaan
                    </label>
                    <div class="relative inline-block text-left dropdown-narasi-container">
                        <button type="button" onclick="toggleDropdownNarasi('menu_narasi_kebutuhan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                            <span>ℹ️ Bantuan Narasi</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="menu_narasi_kebutuhan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                            <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Kebutuhan:</div>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'positif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Belanja Stok Dagang)
                            </button>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'netral'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Perputaran Kas Usaha)
                            </button>
                            <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'negatif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Rincian Biaya Kurang Jelas)
                            </button>
                        </div>
                    </div>
                </div>
                <textarea id="detail_kebutuhan" name="detail_kebutuhan" rows="3" placeholder="Alokasi rencana penggunaan dana kredit: tambahan modal usaha perorangan, belanja stok barang, atau perputaran kas pemohon..."
                          class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['detail_kebutuhan'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Hidden fields untuk menjaga konsistensi data scoring & rekomendasi -->
        <input type="hidden" name="skor_kelayakan_ai" id="skor_kelayakan_ai" value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>">
        <input type="hidden" name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" value="<?= esc($analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh') ?>">
        <input type="hidden" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" value="<?= esc($analisa['rekomendasi_plafon_ai'] ?? 500000000) ?>">
        <input type="hidden" name="analisis_risiko_ai" id="analisis_risiko_ai" value="<?= esc($analisa['analisis_risiko_ai'] ?? '') ?>">
    </div>
</div>

