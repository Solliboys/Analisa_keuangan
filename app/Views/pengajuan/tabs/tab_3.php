<div id="tab-3" class="tab-panel space-y-6">
    <!-- Card Utama: Profil Kegiatan Usaha -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        
        <!-- Section Header C -->
        <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                    3
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">PROFIL KEGIATAN USAHA & OPERASIONAL</h3>
                    <p class="text-[11px] text-slate-500">Profil operasional unit usaha penghasil arus kas pembayaran pinjaman</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                Wajib Diisi
            </span>
        </div>

        <!-- Grid Form 2 Kolom Seimbang -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Nama Usaha / Merk Dagang -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Nama Usaha / Merk Dagang
                </label>
                <input type="text" name="nama_usaha" 
                       value="<?= esc($kredit['nama_usaha'] ?? '') ?>" 
                       placeholder="Contoh: Toko Berkah / PT Mandiri Sejahtera"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Jabatan Pemohon di Usaha -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Jabatan Pemohon di Usaha
                </label>
                <input type="text" name="jabatan" 
                       value="<?= esc($kredit['jabatan'] ?? '') ?>" 
                       placeholder="Pemilik / Direktur / General Manager"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Lama Usaha Berjalan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Lama Usaha Berjalan
                </label>
                <input type="text" name="lama_usaha" 
                       value="<?= esc($kredit['lama_usaha'] ?? '') ?>" 
                       placeholder="Contoh: 5 Tahun"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Sumber Utama Pembayaran (Repayment) -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Sumber Utama Pembayaran (Repayment)
                </label>
                <input type="text" name="sumber_pembayaran" 
                       value="<?= esc($kredit['sumber_pembayaran'] ?? '') ?>" 
                       placeholder="Hasil penjualan harian / invoice piutang"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Alamat Lokasi Tempat Usaha -->
            <div class="md:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Alamat Lokasi Tempat Usaha
                </label>
                <textarea name="alamat_usaha" rows="2" 
                          placeholder="Alamat fisik workshop/toko/pabrik yang beroperasi"
                          class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"><?= esc($kredit['alamat_usaha'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Card Sub: Legalitas Akta & Kemenkumham (Khusus PT / CV) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h4 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">LEGALITAS AKTA & KEMENKUMHAM (UNTUK PT / CV)</h4>
            </div>
            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold text-slate-400 bg-slate-50 border border-slate-200">
                Opsional
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- NPWP Usaha / Badan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    NPWP Usaha / Badan
                </label>
                <input type="text" name="npwp_usaha" 
                       value="<?= esc($kredit['npwp_usaha'] ?? '') ?>" 
                       placeholder="Nomor Pokok Wajib Pajak Usaha"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Nomor Induk Berusaha (NIB) / Izin -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Nomor Induk Berusaha (NIB) / Izin
                </label>
                <input type="text" name="nomor_induk_berusaha_nib" 
                       value="<?= esc($kredit['nomor_induk_berusaha_nib'] ?? '') ?>" 
                       placeholder="NIB OSS RBA"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Nomor Akta Pendirian -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Nomor Akta Pendirian
                </label>
                <input type="text" name="akta_pendirian" 
                       value="<?= esc($kredit['akta_pendirian'] ?? '') ?>" 
                       placeholder="No. Akta Notaris Pendirian"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- SK Pengesahan Kemenkumham Pendirian -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    SK Pengesahan Kemenkumham Pendirian
                </label>
                <input type="text" name="sk_pendirian" 
                       value="<?= esc($kredit['sk_pendirian'] ?? '') ?>" 
                       placeholder="No. AHU-xxxx.AH.01.01"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Nomor Akta Perubahan Terakhir -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Nomor Akta Perubahan Terakhir
                </label>
                <input type="text" name="akta_perubahan" 
                       value="<?= esc($kredit['akta_perubahan'] ?? '') ?>" 
                       placeholder="No. Akta Perubahan Pengurus/Modal"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- SK Penerimaan Kemenkumham Perubahan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    SK Penerimaan Kemenkumham Perubahan
                </label>
                <input type="text" name="sk_perubahan" 
                       value="<?= esc($kredit['sk_perubahan'] ?? '') ?>" 
                       placeholder="No. AHU Perubahan Terakhir"
                       class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
            </div>
        </div>
    </div>
</div>