<div id="tab-10" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                10
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">OPINI PENGUSUL</h3>
                <p class="text-[11px] text-slate-500">Evaluasi kualitatif dan pertimbangan pengusul terkait kelayakan usaha, kondisi agunan, dan kebutuhan pembiayaan nasabah</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs">
            Opini Pengusul
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

        <!-- Hidden fields untuk menjaga konsistensi data scoring & rekomendasi -->
        <input type="hidden" name="skor_kelayakan_ai" id="skor_kelayakan_ai" value="<?= esc($analisa['skor_kelayakan_ai'] ?? 82) ?>">
        <input type="hidden" name="rekomendasi_keputusan_ai" id="rekomendasi_keputusan_ai" value="<?= esc($analisa['rekomendasi_keputusan_ai'] ?? 'Disetujui Penuh') ?>">
        <input type="hidden" name="rekomendasi_plafon_ai" id="rekomendasi_plafon_ai" value="<?= esc($analisa['rekomendasi_plafon_ai'] ?? 500000000) ?>">
        <input type="hidden" name="analisis_risiko_ai" id="analisis_risiko_ai" value="<?= esc($analisa['analisis_risiko_ai'] ?? '') ?>">
    </div>
</div>
