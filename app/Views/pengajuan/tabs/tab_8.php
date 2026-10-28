<div id="tab-8" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            8
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA BISNIS & REKOMENDASI KREDIT</h3>
                            <p class="text-[11px] text-slate-500">Evaluasi kualitatif aspek 5C dan rekomendasi keputusan kredit serta limit plafon</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs">
                        Aspek 5C & Scoring
                    </span>
                </div>

                <div class="space-y-6">
                    <!-- Analisa Agunan -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Analisa Kualitas & Marketability Jaminan</label>
                            <div class="relative inline-block text-left dropdown-narasi-container">
                                <button type="button" onclick="toggleDropdownNarasi('menu_narasi_jaminan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                                    <span>ℹ️ Bantuan Narasi</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="menu_narasi_jaminan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                    <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Agunan:</div>
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
                        <textarea id="analisa_jaminan" name="analisa_jaminan" rows="3" placeholder="Lokasi strategis, akses jalan, kondisi fisik bangunan, dan kemudahan penjualan kembali..."
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisa_jaminan'] ?? '') ?></textarea>
                    </div>

                    <!-- Business Review -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Business Review & Prospek Pasar Usaha</label>
                            <div class="relative inline-block text-left dropdown-narasi-container">
                                <button type="button" onclick="toggleDropdownNarasi('menu_narasi_review')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                                    <span>ℹ️ Bantuan Narasi</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="menu_narasi_review" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                    <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Usaha:</div>
                                    <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'positif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Pangsa Pasar Stabil)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'netral'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Potensial & Kompetitif)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('business_review', 'business_review', 'negatif'); toggleDropdownNarasi('menu_narasi_review')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Persaingan Ketat)
                                    </button>
                                </div>
                            </div>
                        </div>
                        <textarea id="business_review" name="business_review" rows="3" placeholder="Model bisnis, segmen pelanggan, daya saing kompetitor, stabilitas pasokan bahan baku..."
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_review'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Catatan OTS -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Kunjungan / OTS</label>
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_ots')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div id="menu_narasi_ots" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi OTS:</div>
                                        <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'positif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Operasional Ramai)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'netral'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Berjalan Normal)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('business_catatan', 'business_catatan', 'negatif'); toggleDropdownNarasi('menu_narasi_ots')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Aktivitas Sepi)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <textarea id="business_catatan" name="business_catatan" rows="3" placeholder="Hasil observasi fisik di lokasi usaha nasabah..."
                                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['business_catatan'] ?? '') ?></textarea>
                        </div>
                        <!-- Rincian Modal Kerja -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Rincian Kebutuhan Modal Kerja</label>
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_kebutuhan')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div id="menu_narasi_kebutuhan" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Kebutuhan:</div>
                                        <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'positif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Pengadaan Bahan Baku)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'netral'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Perkuatan Kas)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('detail_kebutuhan', 'detail_kebutuhan', 'negatif'); toggleDropdownNarasi('menu_narasi_kebutuhan')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Rincian Belum Rinci)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <textarea id="detail_kebutuhan" name="detail_kebutuhan" rows="3" placeholder="Alokasi dana pinjaman yang diajukan..."
                                      class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['detail_kebutuhan'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Rekomendasi Keputusan & Plafon Kredit -->
                    <div class="bg-gradient-to-br from-blue-50/80 via-white to-blue-50/50 border border-blue-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-blue-100/90 pb-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 tracking-tight">Rekomendasi Keputusan Kredit</h4>
                                    <p class="text-xs text-slate-500">Hasil penilaian scoring kelayakan 5C dan rekomendasi limit pembiayaan.</p>
                                </div>
                            </div>
                            <button type="button" onclick="jalankanAnalisaAI()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-2 cursor-pointer whitespace-nowrap self-start sm:self-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Analisis Rekomendasi
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Skor Kelayakan (0 - 100)</label>
                                <input type="number" name="skor_kelayakan_ai" id="skor_kelayakan_ai" 
                                       value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>" min="0" max="100"
                                       class="w-full text-lg font-extrabold text-center text-blue-700 bg-blue-50/50 border border-blue-200 rounded-xl py-2 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Rekomendasi Keputusan</label>
                                <?php $rk = $analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh'; ?>
                                <select name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" class="w-full text-xs font-bold text-slate-800 bg-blue-50/50 border border-blue-200 rounded-xl py-2.5 px-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                    <option value="Disetujui Penuh" <?= $rk === 'Disetujui Penuh' ? 'selected' : '' ?>>Disetujui Penuh</option>
                                    <option value="Disetujui dengan Penurunan Plafon" <?= $rk === 'Disetujui dengan Penurunan Plafon' ? 'selected' : '' ?>>Disetujui dengan Penurunan Plafon</option>
                                    <option value="Pertimbangan Khusus" <?= $rk === 'Pertimbangan Khusus' ? 'selected' : '' ?>>Pertimbangan Khusus / Tambah Jaminan</option>
                                    <option value="Tolak" <?= $rk === 'Tolak' ? 'selected' : '' ?>>Tolak / Berisiko Tinggi</option>
                                </select>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Rekomendasi Plafon (Rp)</label>
                                <input type="text" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" 
                                       value="<?= number_format((float)($analisa['rekomendasi_plafon_ai'] ?? 500000000), 0, ',', '.') ?>" 
                                       class="w-full text-sm font-bold text-slate-900 bg-blue-50/50 border border-blue-200 rounded-xl py-2 px-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" oninput="formatRupiah(this)">
                            </div>
                            <div class="md:col-span-3 bg-white p-4 rounded-xl border border-blue-200/70 shadow-2xs">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Analisis Risiko & Mitigasi</label>
                                <textarea name="analisis_risiko_ai" id="analisis_risiko_ai" rows="3" class="w-full text-xs font-medium text-slate-800 bg-blue-50/30 border border-blue-200 rounded-xl p-3 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs leading-relaxed"><?= esc($analisa['analisis_risiko_ai'] ?? 'Profil usaha menunjukkan perputaran kas positif dengan rasio DSC di atas batas aman. Agunan memiliki nilai pasar dan legalitas kuat yang mampu memitigasi risiko gagal bayar.') ?></textarea>
                            </div>
                        </div>

                        <!-- Banner Laporan PDF Siap Dicetak -->
                        <div id="aiPdfBanner" class="mt-5 p-4 bg-white border border-blue-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-sm shrink-0">
                                    &check;
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-800" id="aiSumberLabel">Model Evaluasi Kredit Aktif</div>
                                    <div class="text-[11px] text-slate-500">Hasil analisa komprehensif dapat langsung dicetak sebagai Laporan Memo Kredit resmi (PDF).</div>
                                </div>
                            </div>
                            <button type="button" onclick="bukaLaporanPDF()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Cetak Laporan CAM (PDF) &rarr;
                            </button>
                        </div>
                    </div>
                </div>
            </div>
