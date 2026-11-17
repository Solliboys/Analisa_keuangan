<div id="tab-8" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                <?= (!empty($view_folder) && $view_folder === 'badan_usaha') ? '7' : '8' ?>
            </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REKAPITULASI MUTASI REKENING KORAN</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan arus kas masuk (kredit) dan keluar (debit) untuk validasi omzet usaha riil</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="bukaModalUploadOcrRekening()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-indigo-600 via-blue-600 to-indigo-700 hover:from-indigo-700 hover:to-blue-800 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
                            <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                            Upload Rekening & OCR AI
                        </button>
                        <button type="button" onclick="tambahBarisRekening()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Rekening Bank
                        </button>
                    </div>
                </div>

                <!-- Banner Fitur Ekstraksi AI Rekening Koran -->
                <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-indigo-50/90 via-blue-50/70 to-white border border-indigo-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wide">Ekstraksi Otomatis Rekening Koran (AI OCR)</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">Gemini Multi-Modal</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">Unggah e-Statement PDF atau foto mutasi rekening. AI akan otomatis mengekstrak nama bank, pemilik, periode, saldo awal, dan menghitung total mutasi kredit/debit.</p>
                        </div>
                    </div>
                    <button type="button" onclick="bukaModalUploadOcrRekening()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer whitespace-nowrap shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Unggah Berkas Mutasi</span>
                    </button>
                </div>

                <div class="space-y-4" id="containerRekening">
                    <?php if (empty($rekening)): ?>
                        <!-- Default 1 Baris Rekening -->
                        <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                                </div>
                                <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Hapus
                                </button>
                            </div>
                            <!-- Baris 1: Identitas Rekening -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                                    <input type="text" name="rekening[0][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                                    <input type="text" name="rekening[0][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                                    <input type="text" name="rekening[0][nomor_rekening]" placeholder="Nomor rekening bank" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                                    <input type="text" name="rekening[0][atas_nama]" placeholder="Nama pemilik rekening" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                </div>
                            </div>
                            <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-3 border-t border-slate-100">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Saldo Awal Rekening (Rp)</label>
                                    <input type="text" name="rekening[0][saldo_awal]" placeholder="0" 
                                           class="w-full text-xs font-bold text-emerald-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-saldo-awal h-[38px]" 
                                           oninput="formatRupiah(this);">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                                    <input type="text" name="rekening[0][total_mutasi_kredit]" placeholder="0" 
                                           class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                           oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                                    <input type="text" name="rekening[0][total_mutasi_debit]" placeholder="0" 
                                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                           oninput="formatRupiah(this); hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                                    <input type="number" name="rekening[0][jumlah_bulan]" value="6" min="1" max="24" 
                                           class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                           oninput="hitungRataRataRekening();">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                                    <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                        Rp 0
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($rekening as $idx => $r): ?>
                            <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                                    </div>
                                    <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                                <!-- Baris 1: Identitas Rekening -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nama_bank]" value="<?= esc($r['nama_bank'] ?? '') ?>" placeholder="Contoh: BCA / Mandiri / BRI" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                                        <input type="text" name="rekening[<?= $idx ?>][periode]" value="<?= esc($r['periode'] ?? '') ?>" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][nomor_rekening]" value="<?= esc($r['nomor_rekening'] ?? '') ?>" placeholder="Nomor rekening bank" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                                        <input type="text" name="rekening[<?= $idx ?>][atas_nama]" value="<?= esc($r['atas_nama'] ?? '') ?>" placeholder="Nama pemilik rekening" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                                    </div>
                                </div>
                                <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-3 border-t border-slate-100">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Saldo Awal Rekening (Rp)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][saldo_awal]" 
                                               value="<?= number_format((float)($r['saldo_awal'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-emerald-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-saldo-awal h-[38px]" 
                                               oninput="formatRupiah(this);">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_kredit]" 
                                               value="<?= number_format((float)($r['total_mutasi_kredit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                                        <input type="text" name="rekening[<?= $idx ?>][total_mutasi_debit]" 
                                               value="<?= number_format((float)($r['total_mutasi_debit'] ?? 0), 0, ',', '.') ?>" 
                                               placeholder="0" class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                               oninput="formatRupiah(this); hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                                        <input type="number" name="rekening[<?= $idx ?>][jumlah_bulan]" 
                                               value="<?= esc($r['jumlah_bulan'] ?? 6) ?>" min="1" max="24" 
                                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                               oninput="hitungRataRataRekening();">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                                        <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                            Rp <?= number_format((float)($r['rata_rata_kredit'] ?? 0), 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Rekap Mutasi Seluruh Bank -->
                <div class="mt-6 p-5 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/60 border border-blue-200/90 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider">Total Omzet Arus Kas Mutasi Kredit</div>
                            <div class="text-xs text-slate-500">Akumulasi rata-rata arus kas masuk per bulan dari seluruh rekening bank terdaftar.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-[11px] text-slate-400 font-semibold uppercase">Rata-rata Gabungan / Bulan</div>
                            <div id="totalAvgKreditGabungan" class="text-lg font-extrabold text-blue-700">Rp 0</div>
                        </div>
                        <button type="button" onclick="salinOmzetKeLabaRugi()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer whitespace-nowrap">
                            Gunakan di Laba Rugi &rarr;
                        </button>
                    </div>
                </div>
            </div>

<!-- =======================================================
     MODAL UPLOAD REKENING KORAN & EKSTRAKSI AI (OCR)
======================================================== -->
<div id="modalUploadOcrRekening" onclick="if(event.target === this) tutupModalUploadOcrRekening()" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-2xl w-full p-5 sm:p-7 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white flex items-center justify-center shadow-md shadow-indigo-100">
                    <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Upload Rekening Koran & Ekstraksi AI</h3>
                    <p class="text-xs text-slate-500">Membaca dokumen e-Statement/foto mutasi dan mengisi otomatis ke formulir</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalUploadOcrRekening()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Scrollable -->
        <div class="flex-1 overflow-y-auto py-4 space-y-4 pr-1">
            <!-- Dropzone Area -->
            <div id="ocrDropzoneWrapper">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih atau Drag & Drop Berkas Mutasi</label>
                <div id="dropzoneRekening" onclick="document.getElementById('inputOcrRekeningFile').click()" 
                     class="border-2 border-dashed border-slate-300 hover:border-indigo-500 bg-slate-50/50 hover:bg-indigo-50/20 rounded-2xl p-6 text-center flex flex-col items-center justify-center transition cursor-pointer group shadow-2xs">
                    <input type="file" id="inputOcrRekeningFile" accept=".pdf,image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="handleOcrFileSelected(this)">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 group-hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition mb-3 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p id="labelOcrFileName" class="text-xs font-bold text-slate-700 group-hover:text-indigo-600 transition max-w-sm truncate">Klik untuk pilih berkas rekening koran atau seret berkas ke sini</p>
                    <p class="text-[11px] text-slate-400 mt-1" id="labelOcrFileInfo">Mendukung format PDF e-Statement, JPG, PNG, atau WebP (Maksimal 20MB)</p>
                </div>
            </div>

            <!-- Target Baris & Aksi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1" id="ocrOptionsSection">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Target Pengisian Formulir</label>
                    <select id="selectTargetRekeningRow" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:border-indigo-500 shadow-2xs cursor-pointer">
                        <option value="auto">Otomatis (Isi baris 1 atau tambah baris baru jika ada isinya)</option>
                        <option value="0">Terapkan ke Baris 1 (Gantikan isian)</option>
                        <option value="new">+ Tambah Sebagai Baris Rekening Baru</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="prosesOcrRekeningKoran()" id="btnProsesOcr" class="w-full h-[40px] px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                        <span>Mulai Ekstraksi AI</span>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div id="ocrLoadingState" class="hidden py-8 px-4 text-center space-y-4 bg-slate-50/70 border border-indigo-100 rounded-2xl">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 animate-pulse">
                    <svg class="w-7 h-7 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-800" id="ocrLoadingTitle">Gemini AI sedang memproses berkas...</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto" id="ocrLoadingSub">Membaca teks dokumen rekening koran, menghitung mutasi uang masuk (kredit) dan keluar (debit)...</p>
                </div>
            </div>

            <!-- Hasil Ekstraksi Container -->
            <div id="ocrResultContainer" class="hidden space-y-4 pt-1">
                <div class="p-4 bg-gradient-to-br from-indigo-50/90 via-blue-50/50 to-white border border-indigo-200/90 rounded-2xl shadow-xs space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-2.5 border-b border-indigo-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="text-xs font-bold text-indigo-950 uppercase tracking-wide">Hasil Ekstraksi Gemini AI</span>
                        </div>
                        <span id="badgeOcrBank" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-600 text-white shadow-2xs">
                            Bank Operasional
                        </span>
                    </div>

                    <!-- Ringkasan Identitas -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-100 shadow-2xs">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Nama Usaha / Pemilik</span>
                            <span class="font-bold text-slate-800 truncate block mt-0.5" id="resOcrAtasNama">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-100 shadow-2xs">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Periode Mutasi</span>
                            <span class="font-bold text-slate-800 truncate block mt-0.5" id="resOcrPeriode">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-100 shadow-2xs">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Nomor Rekening</span>
                            <span class="font-bold text-slate-800 truncate block mt-0.5" id="resOcrNoRek">-</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-slate-100 shadow-2xs">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Jumlah Bulan</span>
                            <span class="font-bold text-indigo-700 block mt-0.5" id="resOcrBulan">1 Bulan</span>
                        </div>
                    </div>

                    <!-- Metrik Finansial -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Saldo Awal</span>
                            <span class="font-extrabold text-slate-700 block text-xs mt-0.5" id="resOcrSaldoAwal">Rp 0</span>
                        </div>
                        <div class="bg-emerald-50/70 p-2.5 rounded-xl border border-emerald-200 shadow-2xs">
                            <span class="text-[10px] text-emerald-700 font-semibold block uppercase">Total Kredit (Masuk)</span>
                            <span class="font-extrabold text-emerald-700 block text-xs mt-0.5" id="resOcrTotalKredit">Rp 0</span>
                        </div>
                        <div class="bg-rose-50/70 p-2.5 rounded-xl border border-rose-200 shadow-2xs">
                            <span class="text-[10px] text-rose-700 font-semibold block uppercase">Total Debit (Keluar)</span>
                            <span class="font-extrabold text-rose-700 block text-xs mt-0.5" id="resOcrTotalDebit">Rp 0</span>
                        </div>
                        <div class="bg-blue-50/70 p-2.5 rounded-xl border border-blue-200 shadow-2xs">
                            <span class="text-[10px] text-blue-700 font-semibold block uppercase">Saldo Akhir</span>
                            <span class="font-extrabold text-blue-800 block text-xs mt-0.5" id="resOcrSaldoAkhir">Rp 0</span>
                        </div>
                    </div>

                    <!-- Highlight Omzet Rata-rata -->
                    <div class="p-3 bg-white rounded-xl border border-blue-200 flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                                📈
                            </div>
                            <div>
                                <span class="text-[11px] font-bold text-slate-800 block">Rata-rata Kredit / Bulan (Validasi Omzet)</span>
                                <span class="text-[10px] text-slate-400">Dasar validasi kapasitas omset pada Tab Laba Rugi</span>
                            </div>
                        </div>
                        <span class="text-sm font-extrabold text-blue-700" id="resOcrAvgKredit">Rp 0</span>
                    </div>

                    <!-- Accordion Transaksi -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                        <button type="button" onclick="toggleOcrTransaksiTable()" class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 text-left flex items-center justify-between transition cursor-pointer">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <span>Rincian Mutasi Transaksi (<span id="countOcrTransaksi">0</span> transaksi)</span>
                            </div>
                            <span id="arrowOcrTransaksi" class="text-xs text-slate-400 transition-transform duration-200">&darr;</span>
                        </button>
                        <div id="wrapperOcrTransaksiTable" class="hidden max-h-56 overflow-y-auto">
                            <table class="w-full text-[11px] text-left border-collapse">
                                <thead class="bg-slate-100 text-slate-600 uppercase font-bold sticky top-0">
                                    <tr>
                                        <th class="py-2 px-2.5">Tanggal</th>
                                        <th class="py-2 px-2.5">Keterangan</th>
                                        <th class="py-2 px-2.5 text-right">Kredit</th>
                                        <th class="py-2 px-2.5 text-right">Debit</th>
                                        <th class="py-2 px-2.5 text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyOcrTransaksi" class="divide-y divide-slate-100">
                                    <!-- Diisi via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 shrink-0 gap-3">
            <button type="button" onclick="tutupModalUploadOcrRekening()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
            <button type="button" id="btnTerapkanOcr" onclick="terapkanHasilOcrKeFormulir()" class="hidden px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Terapkan ke Formulir Rekening Koran</span>
            </button>
        </div>
    </div>
</div>
