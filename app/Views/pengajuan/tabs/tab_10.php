<div id="tab-10" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            10
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA LABA RUGI KOMPARATIF</h3>
                            <p class="text-[11px] text-slate-500">Verifikasi dual track profitabilitas debitur berdasarkan bukti rekening koran bank vs hasil wawancara</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                        Dual Track Verification
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Stream 1: Berdasarkan Rekening Koran -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Jalur Rekening Koran</h3>
                                        <span class="text-[11px] text-slate-400 font-medium">Validasi bukti mutasi bank</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-100/70 border border-blue-200">
                                    Data Riil Bank
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_rk" id="pendapatan_rk" 
                                           value="<?= number_format((float)($keuangan['pendapatan_rk'] ?? 150000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_rk" id="hpp_persen_rk" 
                                               value="<?= esc($keuangan['hpp_persen_rk'] ?? 60) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomHppRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                                    <span class="font-bold text-blue-900">Laba Kotor (Gross Profit):</span>
                                    <span id="grossRK" class="text-sm font-black text-blue-700">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Biaya Hidup</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBiayaHidupRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Beban Lain</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBebanLainRK" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Laba Bersih Operasional</div>
                                    <div class="text-xs font-extrabold text-white truncate">Rekening Koran</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span id="nettRK" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stream 2: Berdasarkan Hasil Wawancara (Warna Biru Senada) -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                        2
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Jalur Hasil Wawancara</h3>
                                        <span class="text-[11px] text-slate-400 font-medium">Klaim usaha lapangan / OTS</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-100/70 border border-blue-200">
                                    Data Wawancara
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pendapatan / Omset Bulanan (Rp)</label>
                                    <input type="text" name="pendapatan_iv" id="pendapatan_iv" 
                                           value="<?= number_format((float)($keuangan['pendapatan_iv'] ?? 160000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                                           oninput="formatRupiah(this); hitungLabaRugi();">
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">HPP (%)</label>
                                        <input type="number" step="0.1" name="hpp_persen_iv" id="hpp_persen_iv" 
                                               value="<?= esc($keuangan['hpp_persen_iv'] ?? 60) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal HPP</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomHppIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-blue-50/80 border border-blue-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                                    <span class="font-bold text-blue-900">Laba Kotor (Gross Profit):</span>
                                    <span id="grossIV" class="text-sm font-black text-blue-700">Rp 0</span>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Biaya Hidup (%)</label>
                                        <input type="number" step="0.1" name="biaya_hidup_persen_iv" id="biaya_hidup_persen_iv" 
                                               value="<?= esc($keuangan['biaya_hidup_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Biaya Hidup</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBiayaHidupIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Beban Lain (%)</label>
                                        <input type="number" step="0.1" name="beban_lainnya_persen_iv" id="beban_lainnya_persen_iv" 
                                               value="<?= esc($keuangan['beban_lainnya_persen_iv'] ?? 10) ?>" 
                                               class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]"
                                               oninput="hitungLabaRugi();">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Beban Lain</label>
                                        <div class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                            <span id="nomBebanLainIV" class="font-bold text-slate-800">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Laba Bersih Operasional</div>
                                    <div class="text-xs font-extrabold text-white truncate">Hasil Wawancara</div>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span id="nettIV" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
