<div id="tab-5" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                5
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DOKUMEN PERSYARATAN & BERKAS LEGALITAS</h3>
                <p class="text-[11px] text-slate-500">Unggah berkas KTP, NPWP, legalitas usaha, rekening koran, agunan, dan laporan keuangan</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/60">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            Format: PDF, JPG, PNG (Maks 10MB)
        </span>
    </div>

    <!-- Layout 2 Kolom: Kiri Area Upload, Kanan Daftar Dokumen Terunggah -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Panel Kiri: Form Upload Berkas Baru -->
        <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-5 flex flex-col">
            <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-200/60">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </span>
                <span class="text-xs font-bold text-slate-700 tracking-wide">Unggah Berkas Baru</span>
            </div>

            <!-- Pilih Kategori Dokumen -->
            <div class="space-y-3.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Kategori Dokumen
                    </label>
                    <select id="kategoriUpload" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                        <option value="KTP & NPWP">KTP & NPWP (Pemohon / Pasangan)</option>
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
                     class="border border-dashed border-blue-200 hover:border-blue-400 bg-white hover:bg-blue-50/30 rounded-xl p-6 text-center cursor-pointer transition group">
                    <input type="file" id="fileUploadInput" class="hidden" onchange="uploadBerkasDokumenTab5(this)">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-2.5 group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <div class="text-xs font-bold text-slate-700">Pilih Berkas dari Komputer</div>
                    <div class="text-[11px] text-slate-400 mt-1">PDF, JPG, atau PNG (Maks. 10MB)</div>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('fileUploadInput').click()" 
                    class="mt-4 w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
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

            <div id="listDokumenTerunggah" class="space-y-2.5 max-h-[480px] overflow-y-auto pr-1.5 scroll-smooth">
                <?php if (empty($dokumen)): ?>
                    <div id="emptyDokumenState" class="py-10 px-4 border border-slate-200/80 rounded-xl bg-white text-center">
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
                            $catatan = trim($doc['catatan_dokumen'] ?? '');
                            $ukuranKb = number_format(($doc['ukuran_file_bytes'] ?? 0) / 1024, 1);
                        ?>
                        <div class="p-3 bg-white border border-slate-200/90 rounded-2xl flex flex-col gap-2.5 text-xs hover:border-blue-300 hover:shadow-xs transition" id="docRow_<?= $doc['dokumen_id'] ?>">
                            <!-- Baris Atas: Info File & Tombol Aksi (Hapus di sebelah kiri Lihat File) -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-[11px] shrink-0 border border-blue-100 mt-0.5">
                                        <?= $ext ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug truncate" title="<?= esc($doc['nama_file_asli']) ?>">
                                            <?= esc($doc['nama_file_asli']) ?>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                            <input type="hidden" id="kategoriDokumen_<?= $doc['dokumen_id'] ?>" value="<?= esc($rawKat) ?>">
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                <?= $ukuranKb ?> KB
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aksi: Hapus (Tempat Sampah di sebelah kiri) & Lihat File -->
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <!-- Tombol Hapus (Tempat Sampah di sebelah kiri Lihat File) -->
                                    <button type="button" onclick="hapusDokumen(<?= $doc['dokumen_id'] ?>)" class="inline-flex items-center justify-center p-1.5 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition shadow-2xs cursor-pointer" title="Hapus Berkas Dokumen">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>

                                    <!-- Tombol Lihat File -->
                                    <a href="<?= base_url($doc['path_file']) ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-[11px] border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Lihat File</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Text Area Kecil untuk Keterangan Dokumen -->
                            <div class="pt-1.5 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Keterangan Dokumen
                                    </label>
                                    <span id="saveStatusCatatan_<?= $doc['dokumen_id'] ?>" class="text-[9px] text-emerald-600 font-semibold hidden">Tersimpan ✓</span>
                                </div>
                                <textarea rows="1" 
                                          placeholder="Tulis keterangan dokumen (misal: KTP Istri, KTP Pribadi, Sertifikat Rumah No. 123, dll)..." 
                                          oninput="simpanCatatanDokumen(<?= $doc['dokumen_id'] ?>, this.value)"
                                          class="w-full text-[11px] font-normal text-slate-700 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 rounded-lg py-1.5 px-2.5 transition resize-y min-h-[30px] max-h-[80px] placeholder:text-[10px] placeholder:text-slate-400 leading-normal"><?= esc($catatan) ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi Hapus Dokumen via AJAX (Tombol Tempat Sampah)
async function hapusDokumen(dokumenId) {
    if (!dokumenId) return;
    if (!confirm('Apakah Anda yakin ingin menghapus berkas dokumen ini? File yang dihapus tidak dapat dipulihkan.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('dokumen_id', dokumenId);

        const res = await fetch('<?= base_url('pengajuan/hapus-dokumen') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status) {
            if (typeof showToast === 'function') {
                showToast(data.message || 'Dokumen berhasil dihapus', 'success');
            } else {
                alert(data.message || 'Dokumen berhasil dihapus');
            }
            const row = document.getElementById('docRow_' + dokumenId);
            if (row) {
                row.remove();
                const list = document.getElementById('listDokumenTerunggah');
                const remaining = list ? list.querySelectorAll('[id^="docRow_"]').length : 0;
                const badge = document.getElementById('docCountBadge');
                if (badge) badge.innerText = `${remaining} Dokumen`;

                if (remaining === 0 && list) {
                    list.innerHTML = `
                        <div id="emptyDokumenState" class="py-12 px-4 border border-slate-200/80 rounded-xl bg-white text-center">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">Belum ada berkas yang diunggah</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih kategori dan berkas di sebelah kiri untuk mengunggah dokumen pendukung.</p>
                        </div>
                    `;
                }
            }
        } else {
            alert(data.message || 'Gagal menghapus dokumen');
        }
    } catch (e) {
        alert('Terjadi kesalahan saat menghapus dokumen');
    }
}
window.hapusDokumen = hapusDokumen;

// Fungsi Simpan Keterangan Textarea Dokumen dengan Debounce Otomatis
let debounceTimerCatatan = {};
function simpanCatatanDokumen(docId, catatanText) {
    if (!docId) return;
    const statusSpan = document.getElementById('saveStatusCatatan_' + docId);
    if (debounceTimerCatatan[docId]) {
        clearTimeout(debounceTimerCatatan[docId]);
    }
    debounceTimerCatatan[docId] = setTimeout(async () => {
        try {
            const formData = new FormData();
            formData.append('dokumen_id', docId);
            formData.append('catatan', catatanText);

            const res = await fetch('<?= base_url('pengajuan/update-kategori-dokumen') ?>', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.status && statusSpan) {
                statusSpan.classList.remove('hidden');
                setTimeout(() => statusSpan.classList.add('hidden'), 2000);
            }
        } catch (e) {
            console.error('Gagal menyimpan keterangan dokumen', e);
        }
    }, 400);
}
window.simpanCatatanDokumen = simpanCatatanDokumen;

// Handler Upload Berkas Tab 5 (Langsung render tombol hapus di sebelah kiri Lihat File & Textarea Keterangan)
async function uploadBerkasDokumenTab5(input) {
    const file = input.files[0];
    if (!file) return;

    if (file.type === 'application/pdf' && file.size > 5 * 1024 * 1024) {
        alert('Ukuran file PDF melebihi 5MB. Silakan kompres terlebih dahulu.');
        input.value = '';
        return;
    }

    const kreditIdInput = document.getElementById('kredit_id');
    let activeKreditId = kreditIdInput ? kreditIdInput.value : '';
    if (!activeKreditId && typeof simpanForm === 'function') {
        await simpanForm(false, false);
        activeKreditId = kreditIdInput ? kreditIdInput.value : '';
    }

    if (!activeKreditId) {
        alert('Harap simpan data form terlebih dahulu sebelum mengunggah dokumen.');
        return;
    }

    let finalFile = file;
    if (typeof compressImageIfNeeded === 'function') {
        try {
            finalFile = await compressImageIfNeeded(file);
        } catch (e) {
            finalFile = file;
        }
    }

    if (typeof showToast === 'function') {
        showToast('Mengunggah dokumen...', 'info');
    }

    const kategoriPilihan = document.getElementById('kategoriUpload')?.value || 'KTP & NPWP';
    const formData = new FormData();
    formData.append('file', finalFile);
    formData.append('kredit_id', activeKreditId);
    formData.append('kategori', kategoriPilihan);
    formData.append('catatan', '');

    try {
        const res = await fetch('<?= base_url('pengajuan/upload-dokumen') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status) {
            if (typeof showToast === 'function') {
                showToast('Dokumen berhasil diunggah', 'success');
            }

            const emptyState = document.getElementById('emptyDokumenState');
            if (emptyState) emptyState.remove();

            const list = document.getElementById('listDokumenTerunggah');
            const ext = (data.nama_file.split('.').pop() || 'DOC').toUpperCase();
            const kategoriLabel = data.kategori || kategoriPilihan;
            const ukuranText = data.ukuran_kb ? `${data.ukuran_kb} KB` : `${(file.size / 1024).toFixed(1)} KB`;

            const itemHtml = `
                <div class="p-3 bg-white border border-slate-200/90 rounded-2xl flex flex-col gap-2.5 text-xs hover:border-blue-300 hover:shadow-xs transition" id="docRow_${data.dokumen_id}">
                    <!-- Baris Atas: Info File & Tombol Aksi (Hapus di sebelah kiri Lihat File) -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-[11px] shrink-0 border border-blue-100 mt-0.5">
                                ${ext}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug truncate" title="${data.nama_file}">
                                    ${data.nama_file}
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                    <input type="hidden" id="kategoriDokumen_${data.dokumen_id}" value="${kategoriLabel}">
                                    <span class="text-[10px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                        Baru saja diunggah &bull; ${ukuranText}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Aksi: Hapus (Tempat Sampah di sebelah kiri) & Lihat File -->
                        <div class="flex items-center gap-1.5 shrink-0">
                            <!-- Tombol Hapus (Tempat Sampah di sebelah kiri Lihat File) -->
                            <button type="button" onclick="hapusDokumen(${data.dokumen_id})" class="inline-flex items-center justify-center p-1.5 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition shadow-2xs cursor-pointer" title="Hapus Berkas Dokumen">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <!-- Tombol Lihat File -->
                            <a href="${data.path}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-[11px] border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-2xs">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Lihat File</span>
                            </a>
                        </div>
                    </div>

                    <!-- Text Area Kecil untuk Keterangan Dokumen -->
                    <div class="pt-1.5 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Keterangan Dokumen
                            </label>
                            <span id="saveStatusCatatan_${data.dokumen_id}" class="text-[9px] text-emerald-600 font-semibold hidden">Tersimpan ✓</span>
                        </div>
                        <textarea rows="1" 
                                  placeholder="Tulis keterangan dokumen (misal: KTP Istri, KTP Pribadi, Sertifikat Rumah No. 123, dll)..." 
                                  oninput="simpanCatatanDokumen(${data.dokumen_id}, this.value)"
                                  class="w-full text-[11px] font-normal text-slate-700 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg py-1.5 px-2.5 transition resize-y min-h-[30px] max-h-[80px] placeholder:text-[10px] placeholder:text-slate-400 leading-normal"></textarea>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('afterbegin', itemHtml);

            const badge = document.getElementById('docCountBadge');
            if (badge) {
                const total = list.querySelectorAll('[id^="docRow_"]').length;
                badge.innerText = `${total} Dokumen`;
            }
            input.value = '';
        } else {
            alert(data.message || 'Gagal mengunggah file');
        }
    } catch (err) {
        alert('Terjadi kesalahan saat upload dokumen');
    }
}
</script>
