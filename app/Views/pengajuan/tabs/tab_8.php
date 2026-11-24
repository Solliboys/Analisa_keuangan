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
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="tampilkanLabaRugiRekening()" id="btnLabaRugiRekening" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer" title="Tampilkan ringkasan Laba Rugi dari mutasi rekening koran di bawah tabel">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Laba Rugi</span>
                        </button>
                        <button type="button" onclick="bukaModalUploadOcrRekening()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" title="Unggah PDF atau foto rekening koran untuk pengisian otomatis">
                            <svg class="w-4 h-4 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Import Rekening Koran (AI)</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Data Rekening Koran (Read-Only Hasil Import AI) -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                    <table class="w-full text-left border-collapse min-w-[980px]">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200 text-[10px] font-bold text-slate-600 uppercase tracking-wider">
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3">Nama Bank</th>
                                <th class="py-3 px-3">Periode Mutasi</th>
                                <th class="py-3 px-3">No. Rekening</th>
                                <th class="py-3 px-3">Atas Nama</th>
                                <th class="py-3 px-3 text-right">Saldo Awal</th>
                                <th class="py-3 px-3 text-right text-blue-700">Mutasi Kredit</th>
                                <th class="py-3 px-3 text-right">Mutasi Debit</th>
                                <th class="py-3 px-3 text-center">Bln</th>
                                <th class="py-3 px-3 text-right text-blue-700 bg-blue-50/40">Rata² Kredit / Bln</th>
                                <th class="py-3 px-3 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="containerRekening" class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($rekening)): ?>
                                <tr id="emptyRowRekening">
                                    <td colspan="11" class="py-10 px-4 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <p class="text-xs font-medium text-slate-500">Belum ada data rekening koran yang diimpor.</p>
                                            <p class="text-[11px] text-slate-400">Klik tombol <strong class="text-indigo-600">Import Rekening Koran (AI)</strong> di pojok kanan atas untuk mengunggah berkas mutasi bank.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rekening as $idx => $r): ?>
                                    <?php
                                        $saVal = (float)($r['saldo_awal'] ?? 0);
                                        $krVal = (float)($r['total_mutasi_kredit'] ?? 0);
                                        $dbVal = (float)($r['total_mutasi_debit'] ?? 0);
                                        $blnVal = max(1, (int)($r['jumlah_bulan'] ?? 1));
                                        $avgVal = (float)($r['rata_rata_kredit'] ?? round($krVal / $blnVal));
                                    ?>
                                    <tr class="rekening-item hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3 text-center font-bold text-slate-400 rek-no"><?= $idx + 1 ?></td>
                                        <td class="py-3 px-3 font-bold text-slate-800 whitespace-nowrap">
                                            <?= esc($r['nama_bank'] ?: '-') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][nama_bank]" value="<?= esc($r['nama_bank'] ?? '') ?>">
                                        </td>
                                        <td class="py-3 px-3 font-medium text-slate-700 whitespace-nowrap">
                                            <?= esc($r['periode'] ?: '-') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][periode]" value="<?= esc($r['periode'] ?? '') ?>">
                                        </td>
                                        <td class="py-3 px-3 font-mono text-slate-700 whitespace-nowrap">
                                            <?= esc($r['nomor_rekening'] ?: '-') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][nomor_rekening]" value="<?= esc($r['nomor_rekening'] ?? '') ?>">
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-slate-800">
                                            <?= esc($r['atas_nama'] ?: '-') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][atas_nama]" value="<?= esc($r['atas_nama'] ?? '') ?>">
                                        </td>
                                        <td class="py-3 px-3 text-right font-bold text-emerald-700 whitespace-nowrap">
                                            Rp <?= number_format($saVal, 0, ',', '.') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][saldo_awal]" value="<?= number_format($saVal, 0, ',', '.') ?>" class="rek-saldo-awal">
                                        </td>
                                        <td class="py-3 px-3 text-right font-bold text-blue-700 whitespace-nowrap">
                                            Rp <?= number_format($krVal, 0, ',', '.') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][total_mutasi_kredit]" value="<?= number_format($krVal, 0, ',', '.') ?>" class="rek-kredit">
                                        </td>
                                        <td class="py-3 px-3 text-right font-semibold text-slate-700 whitespace-nowrap">
                                            Rp <?= number_format($dbVal, 0, ',', '.') ?>
                                            <input type="hidden" name="rekening[<?= $idx ?>][total_mutasi_debit]" value="<?= number_format($dbVal, 0, ',', '.') ?>" class="rek-debit">
                                        </td>
                                        <td class="py-3 px-3 text-center font-semibold text-slate-700 whitespace-nowrap">
                                            <?= $blnVal ?> Bln
                                            <input type="hidden" name="rekening[<?= $idx ?>][jumlah_bulan]" value="<?= $blnVal ?>" class="rek-bulan">
                                        </td>
                                        <td class="py-3 px-3 text-right font-extrabold text-blue-800 bg-blue-50/30 whitespace-nowrap rek-avg-kredit">
                                            Rp <?= number_format($avgVal, 0, ',', '.') ?>
                                        </td>
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            <input type="hidden" name="rekening[<?= $idx ?>][file_rekening]" value="<?= esc($r['file_rekening'] ?? '') ?>" class="rek-file-path">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <button type="button" onclick="bukaModalPreviewRekeningPdf('<?= esc($r['file_rekening'] ?? '', 'js') ?>', '<?= esc(($r['nama_bank'] ?: 'Rekening Koran') . (!empty($r['periode']) ? ' (' . $r['periode'] . ')' : ''), 'js') ?>')" title="Lihat berkas PDF / Gambar rekening koran" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-indigo-500 hover:bg-indigo-50 text-indigo-600 rounded-lg text-[11px] font-bold transition cursor-pointer shadow-2xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span>Detail</span>
                                                </button>
                                                <button type="button" onclick="hapusBarisRekening(this)" title="Hapus data ini" class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Ringkasan Laba Rugi dari Mutasi Rekening Koran (muncul di bawah tabel saat tombol Laba Rugi diklik) -->
                <div id="sectionLabaRugiRekening" class="hidden mt-6 rounded-2xl border border-blue-200/90 bg-slate-50/70 p-5 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200/80 mb-5">
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-slate-900 uppercase tracking-wider">Ringkasan Laba Rugi (Mutasi Rekening Koran)</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">Kalkulasi otomatis pendapatan, pengeluaran, dan surplus arus kas bulanan dari rekening koran di atas.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="terapkanLabaRugiRekeningKeTab9()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-semibold transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Terapkan ke Analisa Laba Rugi</span>
                            </button>
                            <button type="button" onclick="tutupLabaRugiRekening()" class="px-3 py-2 bg-white hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-lg text-xs font-semibold transition cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Pendapatan / Bln (Kredit)</div>
                            <div id="lrRekPendapatan" class="text-base font-bold text-blue-700 mt-1 tabular-nums">Rp 0</div>
                            <div id="lrRekTotalKreditSub" class="text-[10px] text-slate-400 mt-0.5">Total Kredit: Rp 0</div>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Pengeluaran / Bln (Debit)</div>
                            <div id="lrRekPengeluaran" class="text-base font-bold text-slate-800 mt-1 tabular-nums">Rp 0</div>
                            <div id="lrRekTotalDebitSub" class="text-[10px] text-slate-400 mt-0.5">Total Debit: Rp 0</div>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Surplus / Laba Bersih Kas</div>
                            <div id="lrRekLabaBersih" class="text-base font-bold text-emerald-700 mt-1 tabular-nums">Rp 0</div>
                            <div id="lrRekStatusKas" class="text-[10px] font-medium text-emerald-600 mt-0.5">Arus kas positif</div>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Estimasi Margin &amp; Rasio Biaya</div>
                            <div id="lrRekMargin" class="text-base font-bold text-slate-900 mt-1 tabular-nums">0%</div>
                            <div id="lrRekRasioBiaya" class="text-[10px] text-slate-400 mt-0.5">Rasio Pengeluaran: 0%</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200/90 overflow-x-auto">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-600 uppercase tracking-wider">
                                    <th class="py-2.5 px-3.5">Komponen Laba Rugi (Rekening Koran)</th>
                                    <th class="py-2.5 px-3.5 text-right">Total Periode</th>
                                    <th class="py-2.5 px-3.5 text-right">Rata-rata / Bulan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-700">Pemasukan / Mutasi Kredit (Omzet)</td>
                                    <td id="lrTblTotalKredit" class="py-2.5 px-3.5 text-right font-semibold text-slate-800 tabular-nums">Rp 0</td>
                                    <td id="lrTblAvgKredit" class="py-2.5 px-3.5 text-right font-bold text-blue-700 tabular-nums">Rp 0</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3.5 font-medium text-slate-700">Pengeluaran / Mutasi Debit (HPP &amp; Operasional)</td>
                                    <td id="lrTblTotalDebit" class="py-2.5 px-3.5 text-right font-semibold text-slate-800 tabular-nums">Rp 0</td>
                                    <td id="lrTblAvgDebit" class="py-2.5 px-3.5 text-right font-semibold text-slate-700 tabular-nums">Rp 0</td>
                                </tr>
                                <tr class="bg-blue-50/40">
                                    <td class="py-2.5 px-3.5 font-bold text-slate-900">Estimasi Laba Bersih / Surplus Kas</td>
                                    <td id="lrTblTotalNett" class="py-2.5 px-3.5 text-right font-bold text-emerald-700 tabular-nums">Rp 0</td>
                                    <td id="lrTblAvgNett" class="py-2.5 px-3.5 text-right font-extrabold text-emerald-700 tabular-nums">Rp 0</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Hidden counters untuk kompatibilitas kalkulasi JS -->
                <span id="totalAvgKreditGabungan" class="hidden">Rp 0</span>
                <span id="totalAvgDebitGabungan" class="hidden">Rp 0</span>
                <span id="estimasiHppGabungan" class="hidden">0%</span>
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

            <!-- Tombol Proses Ekstraksi AI -->
            <div class="pt-1" id="ocrOptionsSection">
                <button type="button" onclick="prosesOcrRekeningKoran()" id="btnProsesOcr" class="w-full h-[42px] px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                    <span>Mulai Ekstraksi AI</span>
                </button>
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

<!-- =======================================================
     MODAL PREVIEW BERKAS PDF / GAMBAR REKENING KORAN
======================================================== -->
<div id="modalPreviewRekeningPdf" onclick="if(event.target === this) tutupModalPreviewRekeningPdf()" class="fixed inset-0 z-50 bg-slate-900/65 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-4xl w-full p-4 sm:p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100 shrink-0 gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate" id="previewRekPdfTitle">Detail Dokumen Rekening Koran</h3>
                    <p class="text-[11px] text-slate-500 truncate" id="previewRekPdfSub">Pratinjau berkas mutasi bank yang diunggah</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a id="btnBukaRekPdfTabBaru" href="#" target="_blank" class="hidden px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-bold transition items-center gap-1">
                    <span>Buka di Tab Baru ↗</span>
                </a>
                <button type="button" onclick="tutupModalPreviewRekeningPdf()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-auto py-3 min-h-[400px] flex items-center justify-center bg-slate-50/70 rounded-xl mt-3 border border-slate-100" id="previewRekPdfContainer">
            <!-- Diisi dinamis via JS -->
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end pt-3.5 border-t border-slate-100 shrink-0 mt-3">
            <button type="button" onclick="tutupModalPreviewRekeningPdf()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>
