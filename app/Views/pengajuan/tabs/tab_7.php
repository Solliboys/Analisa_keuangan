<div id="tab-7" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-slate-100 mb-6 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs shrink-0">
                            7
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">VERIFIKASI LATAR BELAKANG & LEGAL CHECK</h3>
                            <p class="text-[11px] text-slate-500">Pemeriksaan profil reputasi debitur pada kanal publik, peradilan, dan daftar hitam kepatuhan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 self-start sm:self-auto">
                        <button type="button" id="btnAiWebcheck" onclick="jalankanAiWebcheck()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl text-xs font-bold transition shadow-md hover:shadow-lg cursor-pointer">
                            <svg class="w-4 h-4 text-amber-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>⚡ 1-Klik AI Scan 5 Aspek Web Check</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-5">
                    <!-- 1. SIPP -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-800">1. Penelusuran Perkara Pengadilan (SIPP)</span>
                                <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">PN / PA / PTUN / Pengadilan Niaga</span>
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button type="button" onclick="bukaModalCekMa()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-[11px] font-bold rounded-lg transition shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <span>🔍 Cek Perkara MA (Popup)</span>
                                </button>
                                <!-- Dropdown Bantuan Narasi -->
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_sipp')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-100 text-blue-700 text-[11px] font-bold rounded-lg border border-slate-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                <div id="menu_narasi_sipp" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                    <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi SIPP:</div>
                                    <button type="button" onclick="terapkanNarasi('sipp_keterangan', 'sipp', 'positif'); toggleDropdownNarasi('menu_narasi_sipp')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Nihil Perkara)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('sipp_keterangan', 'sipp', 'netral'); toggleDropdownNarasi('menu_narasi_sipp')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Perkara Tuntas)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('sipp_keterangan', 'sipp', 'negatif'); toggleDropdownNarasi('menu_narasi_sipp')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Ada Perkara Aktif)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <textarea id="sipp_keterangan" name="sipp_keterangan" rows="2" 
                                  placeholder="Tuliskan hasil penelusuran perkara pengadilan..." 
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs leading-relaxed"><?= esc($webcheck['sipp_keterangan'] ?? 'Tidak ditemukan perkara perdata, pidana, atau kepailitan aktif atas nama calon debitur.') ?></textarea>
                        <!-- Upload Bukti SIPP -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Upload Bukti SIPP (PDF/Gambar)</span>
                                    <input type="file" accept="image/*,application/pdf" onchange="uploadBuktiWebcheck(this, 'sipp_file')" class="hidden">
                                </label>
                                <input type="hidden" name="sipp_file" id="sipp_file" value="<?= esc($webcheck['sipp_file'] ?? '') ?>">
                            </div>
                            <div id="preview_sipp_file" class="text-xs text-slate-500">
                                <?php if (!empty($webcheck['sipp_file'])): ?>
                                    <a href="<?= base_url($webcheck['sipp_file']) ?>" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                        <span>📄 Bukti Terunggah: <?= basename($webcheck['sipp_file']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file bukti yang diunggah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Internet / Media -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-800">2. Pencarian Media Online & Jejak Digital</span>
                                <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">Google / Media Berita / Media Sosial</span>
                            </div>
                            <!-- Dropdown Bantuan Narasi -->
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button type="button" onclick="bukaGoogleNews()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white text-[11px] font-bold rounded-lg transition shadow-2xs cursor-pointer" title="Cari berita debitur di Google News">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    <span>🌐 Cek Google Berita</span>
                                </button>
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_internet')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-100 text-blue-700 text-[11px] font-bold rounded-lg border border-slate-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div id="menu_narasi_internet" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Media:</div>
                                        <button type="button" onclick="terapkanNarasi('internet_keterangan', 'internet', 'positif'); toggleDropdownNarasi('menu_narasi_internet')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Bebas Berita Negatif)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('internet_keterangan', 'internet', 'netral'); toggleDropdownNarasi('menu_narasi_internet')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Eksposur Minim/Wajar)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('internet_keterangan', 'internet', 'negatif'); toggleDropdownNarasi('menu_narasi_internet')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Keluhan Publik)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <textarea id="internet_keterangan" name="internet_keterangan" rows="2" 
                                  placeholder="Tuliskan hasil penelusuran media online..." 
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs leading-relaxed"><?= esc($webcheck['internet_keterangan'] ?? 'Hasil penelusuran nama debitur dan usaha bersih dari pemberitaan negatif.') ?></textarea>
                        <!-- Upload Bukti Media Online -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Upload Screenshot / Kliping Media</span>
                                    <input type="file" accept="image/*,application/pdf" onchange="uploadBuktiWebcheck(this, 'internet_file')" class="hidden">
                                </label>
                                <input type="hidden" name="internet_file" id="internet_file" value="<?= esc($webcheck['internet_file'] ?? '') ?>">
                            </div>
                            <div id="preview_internet_file" class="text-xs text-slate-500">
                                <?php if (!empty($webcheck['internet_file'])): ?>
                                    <a href="<?= base_url($webcheck['internet_file']) ?>" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                        <span>📄 Bukti Terunggah: <?= basename($webcheck['internet_file']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file bukti yang diunggah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 3. DTTOT / Watchlist -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-800">3. Daftar Terduga Teroris & DTTOT PPATK</span>
                                <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">Kepatuhan Anti Pencucian Uang (APU-PPT)</span>
                            </div>
                            <!-- Dropdown Bantuan Narasi -->
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button type="button" onclick="bukaPortalDttot()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-700 hover:to-red-700 text-white text-[11px] font-bold rounded-lg transition shadow-2xs cursor-pointer" title="Cek Daftar Terduga Teroris DTTOT PPATK">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span>🛡️ Warta DTTOT PPATK</span>
                                </button>
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_watchlist')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-100 text-blue-700 text-[11px] font-bold rounded-lg border border-slate-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div id="menu_narasi_watchlist" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi DTTOT:</div>
                                        <button type="button" onclick="terapkanNarasi('watchlist_keterangan', 'watchlist', 'positif'); toggleDropdownNarasi('menu_narasi_watchlist')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Bebas DTTOT)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('watchlist_keterangan', 'watchlist', 'netral'); toggleDropdownNarasi('menu_narasi_watchlist')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Kemiripan Nama Terverifikasi)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('watchlist_keterangan', 'watchlist', 'negatif'); toggleDropdownNarasi('menu_narasi_watchlist')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Perlu EDD)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <textarea id="watchlist_keterangan" name="watchlist_keterangan" rows="2" 
                                  placeholder="Tuliskan hasil verifikasi daftar hitam PPATK..." 
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs leading-relaxed"><?= esc($webcheck['watchlist_keterangan'] ?? 'Nama debitur TIDAK tercantum dalam daftar DTTOT / Proliferasi Senjata PPATK.') ?></textarea>
                        <!-- Upload Bukti DTTOT -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Upload Bukti Cek APU-PPT / DTTOT</span>
                                    <input type="file" accept="image/*,application/pdf" onchange="uploadBuktiWebcheck(this, 'watchlist_file')" class="hidden">
                                </label>
                                <input type="hidden" name="watchlist_file" id="watchlist_file" value="<?= esc($webcheck['watchlist_file'] ?? '') ?>">
                            </div>
                            <div id="preview_watchlist_file" class="text-xs text-slate-500">
                                <?php if (!empty($webcheck['watchlist_file'])): ?>
                                    <a href="<?= base_url($webcheck['watchlist_file']) ?>" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                        <span>📄 Bukti Terunggah: <?= basename($webcheck['watchlist_file']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file bukti yang diunggah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Lelang KPKNL -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-800">4. Status Objek Lelang KPKNL</span>
                                <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">Penelusuran Agunan pada lelang.go.id</span>
                            </div>
                            <!-- Dropdown Bantuan Narasi -->
                            <div class="flex items-center gap-2 self-end sm:self-auto">
                                <button type="button" onclick="bukaPortalLelang()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white text-[11px] font-bold rounded-lg transition shadow-2xs cursor-pointer" title="Cari pengumuman lelang agunan di lelang.go.id">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span>🏛️ Cek lelang.go.id</span>
                                </button>
                                <div class="relative inline-block text-left dropdown-narasi-container">
                                    <button type="button" onclick="toggleDropdownNarasi('menu_narasi_lelang')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-100 text-blue-700 text-[11px] font-bold rounded-lg border border-slate-200 transition shadow-2xs cursor-pointer">
                                        <span>ℹ️ Bantuan Narasi</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div id="menu_narasi_lelang" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                        <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Lelang:</div>
                                        <button type="button" onclick="terapkanNarasi('lelang_keterangan', 'lelang', 'positif'); toggleDropdownNarasi('menu_narasi_lelang')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Agunan Bersih Lelang)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('lelang_keterangan', 'lelang', 'netral'); toggleDropdownNarasi('menu_narasi_lelang')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Tidak Ada Permohonan)
                                        </button>
                                        <button type="button" onclick="terapkanNarasi('lelang_keterangan', 'lelang', 'negatif'); toggleDropdownNarasi('menu_narasi_lelang')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Terindikasi Sita Eksekusi)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <textarea id="lelang_keterangan" name="lelang_keterangan" rows="2" 
                                  placeholder="Tuliskan hasil verifikasi portal lelang..." 
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs leading-relaxed"><?= esc($webcheck['lelang_keterangan'] ?? 'Objek agunan tidak sedang dalam proses eksekusi atau pengumuman lelang KPKNL.') ?></textarea>
                        <!-- Upload Bukti Lelang -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Upload Bukti Cek lelang.go.id</span>
                                    <input type="file" accept="image/*,application/pdf" onchange="uploadBuktiWebcheck(this, 'lelang_file')" class="hidden">
                                </label>
                                <input type="hidden" name="lelang_file" id="lelang_file" value="<?= esc($webcheck['lelang_file'] ?? '') ?>">
                            </div>
                            <div id="preview_lelang_file" class="text-xs text-slate-500">
                                <?php if (!empty($webcheck['lelang_file'])): ?>
                                    <a href="<?= base_url($webcheck['lelang_file']) ?>" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                        <span>📄 Bukti Terunggah: <?= basename($webcheck['lelang_file']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file bukti yang diunggah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Lingkungan & Karakter -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-bold text-slate-800">5. Kroscek Lingkungan Sekitar & Reputasi</span>
                                <span class="text-[11px] text-slate-400 block sm:inline sm:ml-2">Konfirmasi Lingkungan Domisili & Mitra Usaha</span>
                            </div>
                            <!-- Dropdown Bantuan Narasi -->
                            <div class="relative inline-block text-left dropdown-narasi-container self-end sm:self-auto">
                                <button type="button" onclick="toggleDropdownNarasi('menu_narasi_negatif')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-slate-100 text-blue-700 text-[11px] font-bold rounded-lg border border-slate-200 transition shadow-2xs cursor-pointer">
                                    <span>ℹ️ Bantuan Narasi</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="menu_narasi_negatif" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                    <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi Lingkungan:</div>
                                    <button type="button" onclick="terapkanNarasi('negatif_keterangan', 'lingkungan', 'positif'); toggleDropdownNarasi('menu_narasi_negatif')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Reputasi Terpuji)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('negatif_keterangan', 'lingkungan', 'netral'); toggleDropdownNarasi('menu_narasi_negatif')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Hubungan Wajar)
                                    </button>
                                    <button type="button" onclick="terapkanNarasi('negatif_keterangan', 'lingkungan', 'negatif'); toggleDropdownNarasi('menu_narasi_negatif')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Keluhan Warga)
                                    </button>
                                </div>
                            </div>
                        </div>
                        <textarea id="negatif_keterangan" name="negatif_keterangan" rows="2" 
                                  placeholder="Tuliskan hasil kroscek lingkungan sekitar..." 
                                  class="w-full text-xs font-medium text-slate-800 border border-slate-200 rounded-xl p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs leading-relaxed"><?= esc($webcheck['negatif_keterangan'] ?? 'Informasi dari lingkungan sekitar menunjukkan reputasi yang baik dan tidak ada konflik.') ?></textarea>
                        <!-- Upload Bukti Lingkungan -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-1 border-t border-slate-200/60">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-blue-50 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <span>Upload Foto / Dokumen Cek Lingkungan</span>
                                    <input type="file" accept="image/*,application/pdf" onchange="uploadBuktiWebcheck(this, 'negatif_file')" class="hidden">
                                </label>
                                <input type="hidden" name="negatif_file" id="negatif_file" value="<?= esc($webcheck['negatif_file'] ?? '') ?>">
                            </div>
                            <div id="preview_negatif_file" class="text-xs text-slate-500">
                                <?php if (!empty($webcheck['negatif_file'])): ?>
                                    <a href="<?= base_url($webcheck['negatif_file']) ?>" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                        <span>📄 Bukti Terunggah: <?= basename($webcheck['negatif_file']) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada file bukti yang diunggah</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
