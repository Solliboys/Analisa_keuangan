<div id="tab-12" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            12
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">SIMULASI BIAYA & PENCAIRAN BERSIH (DISBURSEMENT)</h3>
                            <p class="text-[11px] text-slate-500">Rincian seluruh komponen biaya potong muka dan estimasi dana riil yang diterima debitur saat akad</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
                        Simulasi Potongan Muka
                    </span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left: Input Biaya -->
                    <div class="space-y-4">
                        <!-- Provisi -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Provisi (%)</label>
                                <input type="number" step="0.1" name="provisi_persen" id="provisi_persen" 
                                       value="<?= esc($disbursement['provisi_persen'] ?? 7) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Provisi</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomProvisi" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Fee -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Agent Fee (%)</label>
                                <input type="number" step="0.1" name="agent_persen" id="agent_persen" 
                                       value="<?= esc($disbursement['agent_persen'] ?? 1) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Agent Fee</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomAgent" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Fee -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Admin Fee (%)</label>
                                <input type="number" step="0.1" name="admin_persen" id="admin_persen" 
                                       value="<?= esc($disbursement['admin_persen'] ?? 1) ?>" 
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Admin Fee</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomAdmin" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hold Angsuran -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Hold (Bulan)</label>
                                <input type="number" name="hold_angsuran_bulan" id="hold_angsuran_bulan" 
                                       value="<?= esc($disbursement['hold_angsuran_bulan'] ?? 4) ?>" min="0" max="24"
                                       class="w-full text-xs font-bold text-slate-800 text-center border border-slate-200 rounded-xl px-2 py-2 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]" 
                                       oninput="hitungDisbursement();">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Hold Angsuran</label>
                                <div class="w-full text-xs font-bold text-slate-800 bg-slate-50/80 border border-slate-200 rounded-xl px-3 py-2 flex items-center shadow-2xs h-[38px]">
                                    <span id="nomHoldAngsuran" class="font-bold text-slate-800">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Asuransi Jiwa & Kebakaran -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Premi Asuransi Jiwa (Rp)</label>
                                <input type="text" name="asuransi_jiwa" id="asuransi_jiwa" 
                                       value="<?= number_format((float)($disbursement['asuransi_jiwa'] ?? 0), 0, ',', '.') ?>" 
                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]" 
                                       oninput="formatRupiah(this); hitungDisbursement();">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Premi Asuransi Kebakaran / Agunan (Rp)</label>
                                <input type="text" name="asuransi_kebakaran" id="asuransi_kebakaran" 
                                       value="<?= number_format((float)($disbursement['asuransi_kebakaran'] ?? 0), 0, ',', '.') ?>" 
                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]" 
                                       oninput="formatRupiah(this); hitungDisbursement();">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Summary Box (Clean Corporate Blue) -->
                    <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
                        <div>
                            <div class="flex items-center gap-2.5 pb-3 mb-5 border-b border-blue-100">
                                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Struktur Rekapitulasi Pencairan</h3>
                                    <span class="text-[11px] text-slate-400 font-medium">Kalkulasi dana riil siap cair</span>
                                </div>
                            </div>

                            <div class="space-y-3 p-4 bg-white rounded-xl border border-blue-100 shadow-2xs text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-slate-600">Plafon Pinjaman Pokok:</span>
                                    <span id="disbPlafon" class="font-bold text-slate-800 text-sm">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                                    <span class="font-semibold text-slate-600">Total Biaya & Potongan Muka:</span>
                                    <span id="disbTotalPotongan" class="font-bold text-rose-600 text-sm">- Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Highlight Dana Bersih -->
                        <div class="mt-6 p-5 bg-blue-600 text-white rounded-2xl shadow-xs">
                            <div class="flex items-center gap-2.5 mb-2">
                                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Estimasi Dana Bersih yang Dicairkan (Nett)</div>
                                    <div class="text-[11px] text-blue-100 font-medium">Dana riil masuk ke rekening debitur</div>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-blue-500/50 flex justify-between items-baseline">
                                <span class="text-xs text-blue-100 font-semibold">Total Bersih:</span>
                                <span id="disbDanaBersih" class="text-2xl sm:text-3xl font-black text-white tracking-tight whitespace-nowrap">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Finalisasi Pengajuan Pengusul -->
                <div class="mt-8 p-6 bg-slate-50/80 border border-slate-200/90 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Status Dokumen & Pengajuan</label>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-700 rounded-md">Tahap Akhir Pengusul</span>
                        </div>
                        <p class="text-xs text-slate-500">Pilih status pengajuan untuk disimpan sebagai draf atau diajukan langsung ke tahap sidang komite pembiayaan.</p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select name="status_pengajuan" id="status_pengajuan" class="w-full text-xs font-bold border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                            <?php $stp = $kredit['status_pengajuan'] ?? 'Draft'; ?>
                            <option value="Draft" <?= $stp === 'Draft' ? 'selected' : '' ?>>Draft (Belum Selesai)</option>
                            <option value="Diajukan" <?= $stp === 'Diajukan' ? 'selected' : '' ?>>Diajukan ke Komite</option>
                        </select>
                    </div>
                </div>
            </div>
