<div id="tab-8" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                <?= (!empty($view_folder) && $view_folder === 'badan_usaha') ? '7' : '8' ?>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REKAPITULASI MUTASI REKENING KORAN</h3>
                <p class="text-[11px] text-slate-500">Pencatatan arus kas masuk (kredit) dan keluar (debit) untuk pembuktian mutasi rekening bank riil</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Hidden Direct File Input untuk upload langsung tanpa modal pop-up -->
            <input type="file" id="inputOcrRekeningDirect" accept=".pdf,image/png,image/jpeg,image/jpg,image/webp" class="hidden" onchange="handleOcrDirectUpload(this)">
            
            <button type="button" onclick="document.getElementById('inputOcrRekeningDirect').click()" id="btnUploadRekeningDirect" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" title="Unggah PDF e-Statement atau foto rekening koran untuk ekstraksi otomatis">
                <svg class="w-4 h-4 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span>Upload Rekening Koran (PDF / Foto)</span>
            </button>
        </div>
    </div>

    <!-- Inline Loading / Progress Bar saat Ekstraksi AI Berjalan (Tanpa Modal Popup) -->
    <div id="ocrInlineProgressContainer" class="hidden mb-6 p-4 rounded-2xl bg-blue-50/70 border border-blue-200 transition animate-in fade-in">
        <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-800 block" id="ocrInlineTitle">Membaca berkas rekening koran...</span>
                    <span class="text-[11px] text-slate-500 block" id="ocrInlineSub">AI sedang menganalisis mutasi transaksi, saldo, dan informasi rekening...</span>
                </div>
            </div>
            <span class="text-xs font-black text-blue-700 font-mono" id="ocrInlinePercent">0%</span>
        </div>
        <div class="w-full bg-blue-200/60 rounded-full h-2.5 overflow-hidden">
            <div id="ocrInlineProgressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 10%"></div>
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
                                <p class="text-xs font-medium text-slate-500">Belum ada data rekening koran yang diunggah.</p>
                                <p class="text-[11px] text-slate-400">Klik tombol <strong class="text-blue-700">Upload Rekening Koran</strong> di atas untuk memproses berkas mutasi bank langsung.</p>
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
                            $saldoAkhirVal = (float)($r['saldo_akhir'] ?? ($saVal + $krVal - $dbVal));
                            $rawJson = $r['transaksi_json'] ?? ($r['raw_json'] ?? '[]');
                        ?>
                        <tr class="rekening-item hover:bg-slate-50/80 transition" data-rek-index="<?= $idx ?>">
                            <td class="py-3 px-3 text-center font-bold text-slate-400 rek-no"><?= $idx + 1 ?></td>
                            <td class="py-3 px-3 font-bold text-slate-800 whitespace-nowrap">
                                <?= esc($r['nama_bank'] ?: '-') ?>
                                <input type="hidden" name="rekening[<?= $idx ?>][nama_bank]" value="<?= esc($r['nama_bank'] ?? '') ?>" class="rek-bank">
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-700 whitespace-nowrap">
                                <?= esc($r['periode'] ?: '-') ?>
                                <input type="hidden" name="rekening[<?= $idx ?>][periode]" value="<?= esc($r['periode'] ?? '') ?>" class="rek-periode">
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-700 whitespace-nowrap">
                                <?= esc($r['nomor_rekening'] ?: '-') ?>
                                <input type="hidden" name="rekening[<?= $idx ?>][nomor_rekening]" value="<?= esc($r['nomor_rekening'] ?? '') ?>" class="rek-norek">
                            </td>
                            <td class="py-3 px-3 font-semibold text-slate-800">
                                <?= esc($r['atas_nama'] ?: '-') ?>
                                <input type="hidden" name="rekening[<?= $idx ?>][atas_nama]" value="<?= esc($r['atas_nama'] ?? '') ?>" class="rek-nama">
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
                                <input type="hidden" name="rekening[<?= $idx ?>][saldo_akhir]" value="<?= number_format($saldoAkhirVal, 0, ',', '.') ?>" class="rek-saldo-akhir">
                                <input type="hidden" name="rekening[<?= $idx ?>][transaksi_json]" value="<?= esc(is_string($rawJson) ? $rawJson : json_encode($rawJson)) ?>" class="rek-transaksi-json">
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-blue-800 bg-blue-50/30 whitespace-nowrap rek-avg-kredit">
                                Rp <?= number_format($avgVal, 0, ',', '.') ?>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <input type="hidden" name="rekening[<?= $idx ?>][file_rekening]" value="<?= esc($r['file_rekening'] ?? '') ?>" class="rek-file-path">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="bukaModalDetailEkstraksiRekening(this)" title="Lihat rincian data rekening koran yang diekstrak" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-blue-500 hover:bg-blue-50 text-blue-600 rounded-lg text-[11px] font-bold transition cursor-pointer shadow-2xs">
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

    <!-- Hidden counters untuk kompatibilitas kalkulasi JS -->
    <span id="totalAvgKreditGabungan" class="hidden">Rp 0</span>
    <span id="totalAvgDebitGabungan" class="hidden">Rp 0</span>
    <span id="estimasiHppGabungan" class="hidden">0%</span>
</div>

<!-- =======================================================
     MODAL DETAIL DATA HASIL EKSTRAKSI REKENING KORAN
======================================================== -->
<div id="modalDetailEkstraksiRekening" onclick="if(event.target === this) tutupModalDetailEkstraksiRekening()" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-3xl w-full p-5 sm:p-7 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-100">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900" id="detRekTitle">Rincian Data Rekening Koran</h3>
                    <p class="text-xs text-slate-500" id="detRekSubtitle">Ringkasan transaksi dan saldo hasil ekstraksi rekening koran</p>
                </div>
            </div>
            <button type="button" onclick="tutupModalDetailEkstraksiRekening()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Scrollable -->
        <div class="flex-1 overflow-y-auto py-4 space-y-4 pr-1">
            <!-- Header Identitas Bank & Pemilik -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Nama Bank</span>
                    <span class="font-extrabold text-blue-900 text-xs sm:text-sm block mt-0.5" id="detRekNamaBank">-</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">No. Rekening</span>
                    <span class="font-mono font-bold text-slate-800 text-xs sm:text-sm block mt-0.5" id="detRekNoRek">-</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Atas Nama Pemilik</span>
                    <span class="font-bold text-slate-800 truncate block mt-0.5" id="detRekAtasNama">-</span>
                </div>
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Periode Mutasi</span>
                    <span class="font-bold text-slate-800 block mt-0.5" id="detRekPeriode">-</span>
                </div>
            </div>

            <!-- Kartu Metrik Finansial -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Saldo Awal</span>
                    <span class="font-black text-slate-700 block text-xs sm:text-sm mt-1" id="detRekSaldoAwal">Rp 0</span>
                </div>
                <div class="bg-emerald-50/60 p-3 rounded-xl border border-emerald-200 shadow-2xs">
                    <span class="text-[10px] text-emerald-700 font-bold block uppercase tracking-wider">Mutasi Kredit (Masuk)</span>
                    <span class="font-black text-emerald-700 block text-xs sm:text-sm mt-1" id="detRekTotalKredit">Rp 0</span>
                </div>
                <div class="bg-rose-50/60 p-3 rounded-xl border border-rose-200 shadow-2xs">
                    <span class="text-[10px] text-rose-700 font-bold block uppercase tracking-wider">Mutasi Debit (Keluar)</span>
                    <span class="font-black text-rose-700 block text-xs sm:text-sm mt-1" id="detRekTotalDebit">Rp 0</span>
                </div>
                <div class="bg-blue-50/60 p-3 rounded-xl border border-blue-200 shadow-2xs">
                    <span class="text-[10px] text-blue-700 font-bold block uppercase tracking-wider">Saldo Akhir</span>
                    <span class="font-black text-blue-800 block text-xs sm:text-sm mt-1" id="detRekSaldoAkhir">Rp 0</span>
                </div>
            </div>

            <!-- Highlight Rata-rata Kredit / Bulan -->
            <div class="p-3.5 bg-gradient-to-r from-blue-50 to-indigo-50/60 rounded-xl border border-blue-200 flex items-center justify-between shadow-2xs">
                <div>
                    <span class="text-xs font-bold text-slate-800 block">Rata-rata Kredit / Bulan:</span>
                    <span class="text-[10px] text-slate-500" id="detRekSubBulan">Dihitung dari total mutasi kredit dibagi durasi bulan</span>
                </div>
                <span class="text-base font-black text-blue-700" id="detRekAvgKredit">Rp 0</span>
            </div>

            <!-- Tabel Rincian Transaksi yang Diekstrak -->
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                <div class="px-3.5 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Rincian Mutasi Transaksi Bank</span>
                    <span class="text-[11px] font-semibold text-slate-500" id="detRekCountTrans">0 transaksi</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <table class="w-full text-[11px] text-left border-collapse">
                        <thead class="bg-slate-100 text-slate-600 uppercase font-bold sticky top-0">
                            <tr>
                                <th class="py-2 px-3 w-10 text-center">No</th>
                                <th class="py-2 px-3">Tanggal</th>
                                <th class="py-2 px-3">Keterangan</th>
                                <th class="py-2 px-3 text-right text-emerald-700">Kredit</th>
                                <th class="py-2 px-3 text-right text-rose-700">Debit</th>
                                <th class="py-2 px-3 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetailTransaksi" class="divide-y divide-slate-100">
                            <!-- Diisi dinamis via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-100 shrink-0 gap-3">
            <a id="btnLihatBerkasAsliPdf" href="#" target="_blank" class="hidden text-xs font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-1.5 underline cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Unduh / Lihat Berkas Asli (PDF / Gambar)</span>
            </a>
            <div class="flex-1"></div>
            <button type="button" onclick="tutupModalDetailEkstraksiRekening()" class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Self-hosted PDF.js untuk ekstraksi cepat PDF Rekening Koran multi-halaman & terenkripsi di browser -->
<script src="<?= base_url('libs/pdfjs/pdf.min.js?v=3.11.174') ?>"></script>
<script>
    if (window.pdfjsLib) {
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = '<?= base_url('libs/pdfjs/pdf.worker.min.js?v=3.11.174') ?>';
    }
</script>
