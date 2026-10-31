<div id="tab-9" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                9
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">ANALISA LABA RUGI & PENDAPATAN</h3>
                <p class="text-[11px] text-slate-500">Perhitungan laba rugi operasional dari mutasi rekening bank serta pencatatan pendapatan lain-lain</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Verifikasi Rekening Koran & Ekstra Cashflow
        </span>
    </div>

    <!-- Layout 2 Kolom: Kiri Laba Rugi Operasional, Kanan Pendapatan Lain-Lain -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        <!-- Kolom Kiri: Berdasarkan Rekening Koran -->
        <div class="p-6 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div>
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-blue-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Laba Rugi Operasional</h3>
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

                    <!-- Biaya Hidup (Tanpa Persentase, Input Langsung Nominal Rupiah) -->
                    <?php
                        $nomBiayaHidup = (float)($keuangan['biaya_hidup_nominal_rk'] ?? 0);
                        if ($nomBiayaHidup <= 0 && !empty($keuangan['pendapatan_rk'])) {
                            $nomBiayaHidup = ((float)$keuangan['pendapatan_rk']) * (((float)($keuangan['biaya_hidup_persen_rk'] ?? 10)) / 100);
                        }
                        if ($nomBiayaHidup <= 0) {
                            $nomBiayaHidup = 15000000;
                        }

                        $rawDetailBeban = $keuangan['detail_beban_lainnya_rk'] ?? '';
                        $listDetailBeban = [];
                        if (!empty($rawDetailBeban)) {
                            $decBeban = json_decode($rawDetailBeban, true);
                            if (is_array($decBeban)) {
                                $listDetailBeban = $decBeban;
                            }
                        }
                        if (empty($listDetailBeban)) {
                            $nomBebanLama = (float)($keuangan['beban_lainnya_nominal_rk'] ?? 0);
                            if ($nomBebanLama > 0) {
                                $listDetailBeban = [['nama' => 'Beban Lain-Lain', 'nominal' => $nomBebanLama]];
                            }
                        }
                    ?>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nominal Biaya Hidup (Rp)</label>
                        <input type="text" name="biaya_hidup_nominal_rk" id="biaya_hidup_nominal_rk" 
                               value="<?= number_format($nomBiayaHidup, 0, ',', '.') ?>" 
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[38px]"
                               placeholder="Rp 0"
                               oninput="formatRupiah(this); hitungLabaRugi();">
                        <input type="hidden" name="biaya_hidup_persen_rk" id="biaya_hidup_persen_rk" value="<?= esc($keuangan['biaya_hidup_persen_rk'] ?? 10) ?>">
                    </div>

                    <!-- Beban Lain-Lain (Rincian Beban: Tambah/Hapus Item Seperti Biaya Sekolah, dll) -->
                    <div class="pt-2 border-t border-blue-100/80">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Beban Lain-Lain</label>
                                <p class="text-[10px] text-slate-400">Rincian beban (misal: Biaya Sekolah, Listrik, dll)</p>
                            </div>
                            <button type="button" onclick="tambahBebanLain();" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tambah Beban</span>
                            </button>
                        </div>

                        <!-- Container List Rincian Beban Dinamis -->
                        <div id="containerBebanLain" class="space-y-2 mb-2.5">
                            <!-- Diisi oleh renderListBebanLain() -->
                        </div>

                        <!-- Bar Total Nominal Beban Lain -->
                        <div class="p-2.5 bg-white border border-slate-200/90 rounded-xl flex justify-between items-center text-xs shadow-2xs">
                            <span class="font-bold text-slate-600 text-[11px] uppercase tracking-wider">Total Beban Lain:</span>
                            <span id="nomBebanLainRK" class="font-bold text-blue-700 text-xs sm:text-sm">Rp 0</span>
                        </div>

                        <!-- Hidden fields untuk database -->
                        <input type="hidden" name="beban_lainnya_nominal_rk" id="beban_lainnya_nominal_rk" value="<?= esc($keuangan['beban_lainnya_nominal_rk'] ?? 0) ?>">
                        <input type="hidden" name="detail_beban_lainnya_rk" id="detail_beban_lainnya_rk" value="<?= esc(!empty($listDetailBeban) ? json_encode($listDetailBeban) : '[]') ?>">
                        <input type="hidden" name="beban_lainnya_persen_rk" id="beban_lainnya_persen_rk" value="<?= esc($keuangan['beban_lainnya_persen_rk'] ?? 10) ?>">
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

        <!-- Kolom Kanan: PENDAPATAN LAIN-LAIN -->
        <?php 
            $adaPendapatanLain = !empty($keuangan['ada_bukti_pendapatan_lain_rk']) || (!empty($keuangan['pendapatan_lain_rk']) && (float)$keuangan['pendapatan_lain_rk'] > 0);
            $nominalPendapatanLain = (float)($keuangan['pendapatan_lain_rk'] ?? 0);
            $ketPendapatanLain = $keuangan['keterangan_pendapatan_lain_rk'] ?? '';
            $buktiRaw = $keuangan['bukti_pendapatan_lain_rk'] ?? '';
            $listBuktiLain = [];
            if (!empty($buktiRaw)) {
                $decoded = json_decode($buktiRaw, true);
                if (is_array($decoded)) {
                    $listBuktiLain = $decoded;
                } else {
                    $listBuktiLain = [['name' => basename($buktiRaw), 'path' => $buktiRaw]];
                }
            }
        ?>
        <div class="p-6 bg-slate-50/70 rounded-2xl border border-slate-200/90 flex flex-col justify-between shadow-2xs hover:border-blue-300 transition">
            <div>
                <!-- Header Card Pendapatan Lain-lain -->
                <div class="flex items-center justify-between pb-3 mb-5 border-b border-slate-200/80">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs shadow-xs">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider">Pendapatan Lain-Lain</h3>
                            <span class="text-[11px] text-slate-400 font-medium">Penghasilan tambahan di luar usaha pokok</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200">
                        Ekstra Cashflow
                    </span>
                </div>

                <!-- Tombol Centang: Apakah ada pendapatan lain-lain? -->
                <div class="p-4 rounded-xl border transition mb-5 <?= $adaPendapatanLain ? 'bg-blue-50/80 border-blue-300 ring-1 ring-blue-200' : 'bg-white border-slate-200 hover:border-slate-300' ?>" id="boxTogglePendapatanLain">
                    <label class="flex items-start gap-3.5 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="ada_bukti_pendapatan_lain_rk" 
                               id="ada_bukti_pendapatan_lain_rk" 
                               value="1" 
                               <?= $adaPendapatanLain ? 'checked' : '' ?>
                               onchange="togglePendapatanLain(this.checked)"
                               class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-0.5 cursor-pointer">
                        <div class="flex-1">
                            <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <span>Apakah ada pendapatan lain-lain?</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded-md uppercase">Centang jika Ada</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Aktifkan jika calon debitur memiliki sumber pemasukan tambahan (seperti gaji pasangan, sewa aset/ruko, dividen, atau bisnis sampingan).
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Form Input Pendapatan Lain-Lain (Tampil jika Dicentang) -->
                <div id="sectionPendapatanLain" class="space-y-4 <?= $adaPendapatanLain ? '' : 'hidden' ?>">
                    <!-- Nominal Pendapatan Lain -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                            Nominal Pendapatan Lain-Lain (Rp/Bulan)
                        </label>
                        <input type="text" 
                               name="pendapatan_lain_rk" 
                               id="pendapatan_lain_rk" 
                               value="<?= $nominalPendapatanLain > 0 ? number_format($nominalPendapatanLain, 0, ',', '.') : '0' ?>" 
                               class="w-full text-sm font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input h-[42px]"
                               placeholder="Rp 0"
                               oninput="formatRupiah(this); hitungLabaRugi();">
                    </div>

                    <input type="hidden" name="keterangan_pendapatan_lain_rk" id="keterangan_pendapatan_lain_rk" value="">

                    <!-- Upload Bukti (Opsional - Mendukung Banyak File & Ada Tombol Hapus) -->
                    <div class="pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                <span>Upload Bukti Pendapatan Lain</span>
                            </label>
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md">Opsional (Bisa &gt; 1 File)</span>
                        </div>

                        <!-- Hidden input menyimpan data JSON bukti -->
                        <input type="hidden" name="bukti_pendapatan_lain_rk" id="bukti_pendapatan_lain_rk" value="<?= esc(!empty($listBuktiLain) ? json_encode($listBuktiLain) : '') ?>">
                        
                        <!-- File input dengan multiple -->
                        <input type="file" 
                               id="file_bukti_pendapatan_lain" 
                               name="file_bukti_pendapatan_lain[]" 
                               multiple 
                               accept=".pdf,.jpg,.jpeg,.png" 
                               class="hidden" 
                               onchange="handlePilihBuktiLain(this)">

                        <!-- Tombol / Card Tambah Bukti Baru -->
                        <div onclick="document.getElementById('file_bukti_pendapatan_lain').click()" 
                             class="border border-dashed border-blue-300 hover:border-blue-500 bg-white hover:bg-blue-50/40 rounded-xl p-3 text-center cursor-pointer transition mb-3 group flex items-center justify-center gap-2.5 shadow-2xs">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <div class="text-left">
                                <div class="text-xs font-bold text-blue-800">Klik untuk Tambah / Pilih File Bukti</div>
                                <div class="text-[10px] text-slate-400">PDF, JPG, PNG (Maks 10 MB) &bull; Bisa upload lebih dari 1 file</div>
                            </div>
                        </div>

                        <!-- Daftar Bukti Terunggah -->
                        <div id="containerListBuktiLain" class="space-y-2">
                            <?php if (empty($listBuktiLain)): ?>
                                <div class="py-3 px-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/60 text-center">
                                    <span class="text-xs text-slate-400 font-medium">Belum ada bukti yang diunggah (Opsional)</span>
                                </div>
                            <?php else: ?>
                                <?php foreach ($listBuktiLain as $idx => $item): ?>
                                    <?php 
                                        $fName = $item['name'] ?? basename($item['path'] ?? 'Bukti');
                                        $fPath = $item['path'] ?? '';
                                        $ext = strtoupper(pathinfo($fName, PATHINFO_EXTENSION) ?: 'DOC');
                                        $fSize = $item['size'] ?? 'Baru saja diunggah • 30.3 KB';
                                        $fDesc = $item['desc'] ?? '';
                                    ?>
                                    <div class="p-3 bg-white border border-slate-200/90 rounded-2xl flex flex-col gap-2.5 text-xs hover:border-blue-300 hover:shadow-xs transition" id="buktiRow_<?= $idx ?>">
                                        <!-- Baris Atas: Info File & Tombol Aksi (Hapus di sebelah kiri Lihat File) -->
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-2.5 min-w-0">
                                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-[11px] shrink-0 border border-blue-100 mt-0.5">
                                                    <?= esc($ext) ?>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug truncate" title="<?= esc($fName) ?>">
                                                        <?= esc($fName) ?>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 inline-flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                            <?= esc($fSize) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Aksi: Hapus (Tempat Sampah di sebelah kiri) & Lihat File -->
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <!-- Tombol Hapus (Tempat Sampah di sebelah kiri Lihat File) -->
                                                <button type="button" onclick="hapusBuktiPendapatanLain(<?= $idx ?>)" class="inline-flex items-center justify-center p-1.5 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition shadow-2xs cursor-pointer" title="Hapus Berkas Dokumen">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>

                                                <!-- Tombol Lihat File -->
                                                <a href="<?= base_url($fPath) ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-[11px] border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-2xs">
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
                                            </div>
                                            <textarea rows="1" 
                                                      placeholder="Tulis keterangan dokumen (misal: KTP Istri, KTP Pribadi, Sertifikat Rumah No. 123, dll)..." 
                                                      oninput="updateCatatanBuktiLain(<?= $idx ?>, this.value)"
                                                      class="w-full text-[11px] font-normal text-slate-700 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 rounded-lg py-1.5 px-2.5 transition resize-y min-h-[30px] max-h-[80px] placeholder:text-[10px] placeholder:text-slate-400 leading-normal"><?= esc($fDesc) ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Info Box saat Belum Dicentang -->
                <div id="infoBelumCentang" class="p-6 text-center rounded-2xl bg-slate-100/70 border border-slate-200/60 <?= $adaPendapatanLain ? 'hidden' : '' ?>">
                    <div class="w-10 h-10 rounded-xl bg-slate-200/80 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <div class="text-xs font-bold text-slate-600">Tidak Ada Pendapatan Tambahan</div>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-xs mx-auto">Centang tombol di atas jika debitur memiliki penghasilan lain untuk memperkuat analisa kemampuan bayar.</p>
                </div>
            </div>

            <!-- Banner Total Pendapatan Tambahan -->
            <div class="mt-6 p-4 bg-blue-600 text-white rounded-2xl flex items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200 truncate">Tambahan Penghasilan</div>
                        <div class="text-xs font-extrabold text-white truncate">Pendapatan Lain-Lain</div>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <span id="labelTotalPendapatanLain" class="text-lg sm:text-xl font-black text-white tracking-tight whitespace-nowrap block">Rp 0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Total Cashflow Bersih Gabungan (Operasional + Lain-lain) -->
    <div class="mt-6 p-4 sm:p-5 bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0 border border-white/20">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-xs font-extrabold text-white uppercase tracking-wider">TOTAL KAPASITAS LABA BERSIH (TOTAL CASHFLOW)</h4>
                <p class="text-[11px] text-blue-200 mt-0.5">Laba Bersih Operasional + Pendapatan Lain-Lain sebagai dasar analisa kemampuan angsuran</p>
            </div>
        </div>
        <div class="text-right flex items-center gap-3">
            <span class="text-xs text-blue-200 font-medium hidden sm:inline">Total Bersih:</span>
            <span id="totalCashflowBersih" class="text-xl sm:text-2xl font-black text-emerald-400 tracking-tight whitespace-nowrap block">Rp 0</span>
        </div>
    </div>
</div>
