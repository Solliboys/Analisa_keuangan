<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            9
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REKAPITULASI MUTASI REKENING KORAN</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan arus kas masuk (kredit) dan keluar (debit) untuk validasi omzet usaha riil</p>
                        </div>
                    </div>
                    <button type="button" onclick="tambahBarisRekening()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Rekening Bank
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
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
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
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
