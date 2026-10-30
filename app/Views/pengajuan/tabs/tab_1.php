<div id="tab-1" class="tab-panel active space-y-6">
                        
                        <!-- Card: Data Aplikasi & Pemohon -->
                        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs">
                            <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                                        1
                                    </div>
                                    <div>
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">DATA APLIKASI & PEMOHON</h3>
                                        <p class="text-[11px] text-slate-500">Konfigurasi parameter dasar plafon dan skema kredit debitur</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200">
                                    Wajib Diisi
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- ID Pengajuan (Locked / Readonly) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                            ID Pengajuan
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-400 flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Terkunci
                                        </span>
                                    </label>
                                    <input type="text" id="display_nomor_aplikasi" value="<?= esc($nomor_aplikasi) ?>" readonly tabindex="-1"
                                           class="w-full text-sm font-bold text-slate-600 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-100/90 cursor-not-allowed select-all shadow-2xs focus:outline-none"
                                           title="Nomor ID pengajuan resmi yang dibuat otomatis oleh sistem dan tidak dapat diedit">
                                </div>

                                <!-- Tanggal Pengajuan (Locked / Readonly) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center justify-between">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Tanggal Pengajuan
                                        </span>
                                        <span class="text-[10px] font-semibold text-slate-400 flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            Terkunci
                                        </span>
                                    </label>
                                    <?php 
                                        $tglDisplay = !empty($kredit['created_at']) ? date('d/m/Y', strtotime($kredit['created_at'])) : date('d/m/Y');
                                        $tglDb = !empty($kredit['created_at']) ? date('Y-m-d', strtotime($kredit['created_at'])) : date('Y-m-d');
                                    ?>
                                    <input type="text" id="display_tanggal_pengajuan" value="<?= esc($tglDisplay) ?>" readonly tabindex="-1"
                                           class="w-full text-sm font-semibold text-slate-600 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-100/90 cursor-not-allowed select-all shadow-2xs focus:outline-none"
                                           title="Tanggal registrasi pengajuan dicatat otomatis oleh sistem dan tidak dapat diedit">
                                    <input type="hidden" name="tanggal_pengajuan" value="<?= esc($tglDb) ?>">
                                </div>

                                <!-- Tipe Aplikasi -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Tipe Aplikasi
                                    </label>
                                    <select name="tipe_aplikasi" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $ta = $kredit['tipe_aplikasi'] ?? 'Fasilitas Baru'; ?>
                                        <option value="Fasilitas Baru" <?= $ta === 'Fasilitas Baru' ? 'selected' : '' ?>>Fasilitas Baru</option>
                                        <option value="Perpanjangan" <?= $ta === 'Perpanjangan' ? 'selected' : '' ?>>Perpanjangan</option>
                                        <option value="Top Up" <?= $ta === 'Top Up' ? 'selected' : '' ?>>Top Up</option>
                                        <option value="Restrukturisasi" <?= $ta === 'Restrukturisasi' ? 'selected' : '' ?>>Restrukturisasi</option>
                                    </select>
                                </div>

                                <!-- Jenis Debitur -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Jenis Debitur
                                    </label>
                                    <select name="jenis_debitur" id="jenis_debitur" onchange="if(typeof ubahJenisDebitur === 'function') ubahJenisDebitur(this.value)" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $jd = $kredit['jenis_debitur'] ?? 'Perorangan'; ?>
                                        <option value="Perorangan" <?= ($jd === 'Perorangan' || $jd === 'Individu') ? 'selected' : '' ?>>Perorangan</option>
                                        <option value="Badan Usaha" <?= (strpos($jd, 'Badan') !== false || $jd === 'Badan Usaha') ? 'selected' : '' ?>>Badan Usaha</option>
                                    </select>
                                </div>

                                <!-- Nama Debitur -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                        Nama Debitur <span class="text-rose-500 font-extrabold">*</span>
                                    </label>
                                    <input type="text" name="nama_debitur" id="nama_debitur" 
                                           value="<?= esc($kredit['nama_debitur'] ?? '') ?>" 
                                           placeholder="Nama lengkap sesuai KTP"
                                           class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                </div>

                                <!-- Plafond Pengajuan (Rp) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Plafond Pengajuan (Rp) <span class="text-rose-500 font-extrabold">*</span>
                                    </label>
                                    <input type="text" name="plafon_pengajuan" id="plafon_pengajuan" 
                                           value="<?= number_format((float)($kredit['plafon_pengajuan'] ?? 200000000), 0, ',', '.') ?>" 
                                           class="w-full text-sm font-bold text-slate-900 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs text-right sm:text-left rupiah-input"
                                           oninput="formatRupiah(this); hitungKalkulasiAngsuran(); hitungCoverageAgunan(); hitungDisbursement();">
                                </div>

                                <!-- Tenor (Bulan) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Tenor (Bulan) <span class="text-rose-500 font-extrabold">*</span>
                                    </label>
                                    <select name="tenor_bulan" id="tenor_bulan" onchange="hitungKalkulasiAngsuran();" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $tb = (int)($kredit['tenor_bulan'] ?? 12); ?>
                                        <option value="6" <?= $tb === 6 ? 'selected' : '' ?>>6 Bulan</option>
                                        <option value="12" <?= $tb === 12 ? 'selected' : '' ?>>12 Bulan</option>
                                        <option value="18" <?= $tb === 18 ? 'selected' : '' ?>>18 Bulan</option>
                                        <option value="24" <?= $tb === 24 ? 'selected' : '' ?>>24 Bulan</option>
                                        <option value="36" <?= $tb === 36 ? 'selected' : '' ?>>36 Bulan</option>
                                        <option value="48" <?= $tb === 48 ? 'selected' : '' ?>>48 Bulan</option>
                                        <option value="60" <?= $tb === 60 ? 'selected' : '' ?>>60 Bulan</option>
                                    </select>
                                </div>

                                <!-- Jaminan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Jaminan
                                    </label>
                                    <select name="jenis_jaminan" id="jenis_jaminan" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $jj = $kredit['jenis_jaminan'] ?? 'SHM'; ?>
                                        <option value="SHM" <?= ($jj === 'SHM' || $jj === 'On Hand') ? 'selected' : '' ?>>SHM (Sertifikat Hak Milik)</option>
                                        <option value="SHGB" <?= $jj === 'SHGB' ? 'selected' : '' ?>>SHGB (Hak Guna Bangunan)</option>
                                        <option value="SHM Sarusun" <?= $jj === 'SHM Sarusun' ? 'selected' : '' ?>>SHM Sarusun (Apartemen)</option>
                                        <option value="BPKB Mobil" <?= ($jj === 'BPKB Mobil' || $jj === 'BPKB') ? 'selected' : '' ?>>BPKB Kendaraan Roda 4</option>
                                        <option value="Bilyet Deposito" <?= $jj === 'Bilyet Deposito' ? 'selected' : '' ?>>Bilyet Deposito</option>
                                        <option value="Tanpa Jaminan" <?= $jj === 'Tanpa Jaminan' ? 'selected' : '' ?>>Tanpa Jaminan</option>
                                    </select>
                                </div>

                                <!-- Tujuan Pembiayaan -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Tujuan Pembiayaan
                                    </label>
                                    <select name="tujuan_pembiayaan" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $tp = $kredit['tujuan_pembiayaan'] ?? 'Multiguna'; ?>
                                        <option value="Multiguna" <?= $tp === 'Multiguna' ? 'selected' : '' ?>>Multiguna</option>
                                        <option value="Modal Kerja" <?= $tp === 'Modal Kerja' ? 'selected' : '' ?>>Modal Kerja</option>
                                        <option value="Investasi" <?= $tp === 'Investasi' ? 'selected' : '' ?>>Investasi</option>
                                        <option value="Take Over" <?= $tp === 'Take Over' ? 'selected' : '' ?>>Take Over</option>
                                    </select>
                                </div>

                                <!-- Skema Pembayaran -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        Skema Pembayaran
                                    </label>
                                    <select name="skema_pembayaran" id="skema_pembayaran" onchange="hitungKalkulasiAngsuran();" class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <?php $sp = $kredit['skema_pembayaran'] ?? 'Installment'; ?>
                                        <option value="Installment" <?= $sp === 'Installment' ? 'selected' : '' ?>>Installment (Pokok + Bunga Rutin Tiap Bulan)</option>
                                        <option value="Balloon Payment" <?= $sp === 'Balloon Payment' ? 'selected' : '' ?>>Balloon Payment (Bunga Bulanan, Pokok Lunas di Akhir)</option>
                                    </select>
                                </div>

                                <!-- Perhitungan Bunga -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        Perhitungan Bunga
                                    </label>
                                    <?php $pb = strtoupper($kredit['perhitungan_bunga'] ?? 'ANUITAS'); ?>
                                    <select name="perhitungan_bunga" id="perhitungan_bunga" onchange="if(document.getElementById('skema_pembayaran').value==='Balloon Payment'){document.getElementById('skema_pembayaran').value='Installment';} hitungKalkulasiAngsuran();" 
                                            class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        <option value="FLAT" <?= $pb === 'FLAT' ? 'selected' : '' ?>>FLAT</option>
                                        <option value="EFEKTIF" <?= $pb === 'EFEKTIF' ? 'selected' : '' ?>>EFEKTIF</option>
                                        <option value="ANUITAS" <?= ($pb === 'ANUITAS' || empty($pb)) ? 'selected' : '' ?>>ANUITAS</option>
                                    </select>
                                    <input type="hidden" name="bunga_per_bulan" id="bunga_per_bulan" value="<?= esc($kredit['bunga_per_bulan'] ?? 3) ?>">
                                </div>

                                <!-- Bunga per Tahun (%) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Suku Bunga per Tahun (%)
                                    </label>
                                    <input type="number" step="0.01" name="bunga_per_tahun" id="bunga_per_tahun" 
                                           value="<?= esc($kredit['bunga_per_tahun'] ?? 36.0) ?>" 
                                           class="w-full text-sm font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs"
                                           oninput="syncBunga('tahun'); hitungKalkulasiAngsuran();">
                                </div>
                            </div>
                        </div>

                        <!-- Live Estimasi Box -->
                        <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-xs">
                            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                                <!-- Kiri: Judul & Penjelasan -->
                                <div>
                                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                        Kalkulasi Simulasi Angsuran
                                    </span>
                                    <div class="text-xs text-slate-500 mt-1">Dihitung otomatis berdasarkan plafon, tenor, dan suku bunga.</div>
                                </div>

                                <!-- Kanan: Nilai Estimasi Sejajar Bersih + Tombol Eksekusi Analisa -->
                                <div class="flex flex-wrap items-center gap-5 sm:gap-6 self-start xl:self-center shrink-0">
                                    <div class="text-left sm:text-right">
                                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Estimasi Balloon (Bunga/bln)</div>
                                        <div id="boxAngsuranBalloon" class="text-xl sm:text-2xl font-extrabold text-blue-600 mt-0.5 whitespace-nowrap">Rp 0</div>
                                    </div>
                                    <div class="h-10 w-px bg-slate-200 hidden sm:block"></div>
                                    <div class="text-left sm:text-right">
                                        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider whitespace-nowrap">Estimasi Installment (Pokok+Bunga)</div>
                                        <div id="boxAngsuranInstallment" class="text-xl sm:text-2xl font-extrabold text-emerald-500 mt-0.5 whitespace-nowrap">Rp 0</div>
                                    </div>
                                    <div class="h-10 w-px bg-slate-200 hidden sm:block"></div>
                                    <div>
                                        <button type="button" id="btnEksekusiAnalisa" onclick="eksekusiAnalisaKalkulasi()" 
                                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition-all duration-200 active:scale-95 cursor-pointer whitespace-nowrap">
                                            <svg id="iconEksekusiAnalisa" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            <span id="labelEksekusiAnalisa">Eksekusi Analisa</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hasil Eksekusi Analisa: Data Anda, Angsuran Anda & Tabel Angsuran (Sesuai Gambar 2) -->
                            <div id="boxHasilEksekusiAnalisa" class="<?= (!empty($kredit['skor_kelayakan_ai']) || !empty($kredit['rekomendasi_keputusan_ai'])) ? '' : 'hidden' ?> mt-6 pt-5 border-t border-slate-200/90 space-y-5">
                                <!-- Box 1: Data Anda (Sesuai Gambar 2) -->
                                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-2xs bg-white text-xs">
                                    <div class="bg-[#334177] text-white font-bold px-4 py-2 text-xs sm:text-sm tracking-wide">
                                        Data Anda
                                    </div>
                                    <div class="divide-y divide-slate-100 font-medium">
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-white items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Jumlah Pinjaman</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900" id="tabelDataPlafon">Rp 0,00</div>
                                        </div>
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-[#f0f3fa] items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Lama Pinjaman</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900">
                                                <span id="tabelDataTenorBulan">0 bulan</span>
                                                <span class="block text-[11px] font-normal text-slate-500" id="tabelDataTenorTahun">(0 tahun)</span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-white items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Bunga per Tahun</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900">
                                                <span id="tabelDataBungaTahun">0 % / tahun</span>
                                                <span class="block text-[11px] font-normal text-slate-500" id="tabelDataBungaBulan">(0 % / bulan)</span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-[#f0f3fa] items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Cicilan Tiap</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900">BULAN</div>
                                        </div>
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-white items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Mulai Meminjam</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900" id="tabelDataMulaiBulan">-</div>
                                        </div>
                                        <div class="grid grid-cols-12 py-2.5 px-4 bg-[#f0f3fa] items-center">
                                            <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Perhitungan Bunga</div>
                                            <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                            <div class="col-span-6 sm:col-span-7 font-bold text-slate-900" id="tabelDataMetodeBunga">ANUITAS</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Box 2: Angsuran Anda (Sesuai Gambar 2) -->
                                <div class="rounded-xl overflow-hidden border border-slate-200 shadow-2xs bg-white text-xs">
                                    <div class="bg-[#334177] text-white font-bold px-4 py-2 text-xs sm:text-sm tracking-wide">
                                        Angsuran Anda
                                    </div>
                                    <div class="grid grid-cols-12 py-3 px-4 bg-white items-center">
                                        <div class="col-span-5 sm:col-span-4 text-slate-800 font-semibold text-right pr-4">Angsuran per BULAN</div>
                                        <div class="col-span-1 text-center font-bold text-slate-400">:</div>
                                        <div class="col-span-6 sm:col-span-7 font-extrabold text-blue-700 text-sm sm:text-base" id="tabelDataAngsuranBulan">Rp 0,00</div>
                                    </div>
                                </div>

                                <!-- Box 3: Tabel Angsuran Lengkap (Sesuai Gambar 2) -->
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 mb-2">Tabel Angsuran</h4>
                                    <div class="overflow-x-auto rounded-xl border border-slate-200/90 shadow-2xs max-h-[520px] overflow-y-auto bg-white">
                                        <table class="w-full text-xs text-left border-collapse" id="tabelJadwalAngsuran">
                                            <thead class="bg-[#334177] text-white font-bold uppercase tracking-wider sticky top-0 z-10 text-[11px]">
                                                <tr>
                                                    <th class="py-3 px-3.5 text-center">Periode</th>
                                                    <th class="py-3 px-3.5 text-right">Angsuran Bunga</th>
                                                    <th class="py-3 px-3.5 text-right">Angsuran Pokok</th>
                                                    <th class="py-3 px-3.5 text-right">Total Angsuran</th>
                                                    <th class="py-3 px-3.5 text-right">Sisa pinjaman</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="bodyTabelJadwalAngsuran">
                                                <!-- Baris angsuran diisi via Javascript renderTabelSimulasiAngsuran -->
                                            </tbody>
                                            <tfoot class="bg-[#e9edf5] font-extrabold text-slate-900 border-t-2 border-slate-300 sticky bottom-0 z-10 text-[11px]" id="footTabelJadwalAngsuran">
                                                <!-- Total baris footer -->
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
