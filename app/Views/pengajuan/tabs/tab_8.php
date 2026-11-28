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
                    <th class="py-3 px-3 text-center w-52">Aksi</th>
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
                            $rekIdVal = $r['rekening_id'] ?? ($r['id'] ?? '');

                            // 1. Coba ambil dari file_rekening baris
                            $rowFileRekening = !empty($r['file_rekening']) ? $r['file_rekening'] : ($kredit['berkas_rekening_koran'] ?? '');

                            // 2. Jika kosong, telusuri dokumen terunggah di Tab 5 (kategori Rekening Koran atau nama file rekening)
                            if (empty($rowFileRekening) && !empty($dokumen) && is_array($dokumen)) {
                                foreach ($dokumen as $doc) {
                                    $kat = strtolower($doc['kategori_dokumen'] ?? '');
                                    $namaF = strtolower($doc['nama_file_asli'] ?? '');
                                    $pathF = $doc['path_file'] ?? ($doc['file_path'] ?? '');
                                    if (!empty($pathF) && (strpos($kat, 'rekening') !== false || strpos($namaF, 'rekening') !== false || strpos($namaF, 'mutasi') !== false || strpos($namaF, 'mandiri') !== false || strpos($namaF, 'bca') !== false || strpos($namaF, 'bri') !== false || strpos($namaF, 'bni') !== false)) {
                                        $rowFileRekening = $pathF;
                                        break;
                                    }
                                }
                                if (empty($rowFileRekening)) {
                                    foreach ($dokumen as $doc) {
                                        $namaF = strtolower($doc['nama_file_asli'] ?? '');
                                        $tipeF = strtolower($doc['tipe_file'] ?? '');
                                        $pathF = $doc['path_file'] ?? ($doc['file_path'] ?? '');
                                        if (!empty($pathF) && (strpos($namaF, '.pdf') !== false || strpos($tipeF, 'pdf') !== false)) {
                                            $rowFileRekening = $pathF;
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!empty($rowFileRekening)) {
                                $rowFileUrl = (strpos($rowFileRekening, 'http') === 0)
                                    ? $rowFileRekening
                                    : base_url('pengajuan/stream-berkas?file=' . urlencode($rowFileRekening));
                            } else {
                                $rowFileUrl = base_url('pengajuan/stream-berkas?kredit_id=' . ($kredit['kredit_id'] ?? '') . '&rekening_id=' . $rekIdVal);
                            }
                        ?>
                        <tr class="rekening-item hover:bg-slate-50/80 transition" data-rek-index="<?= $idx ?>" data-rek-id="<?= esc($rekIdVal) ?>">
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
                                <input type="hidden" name="rekening[<?= $idx ?>][file_rekening]" value="<?= esc($rowFileRekening) ?>" class="rek-file-path">
                                <div class="inline-flex items-center justify-center gap-1.5">
                                    <!-- Tombol 1: Lihat PDF Asli -->
                                    <button type="button" onclick="bukaModalPdfRekening(this)" data-file-url="<?= esc($rowFileUrl) ?>" data-bank="<?= esc($r['nama_bank'] ?? '') ?>" data-norek="<?= esc($r['nomor_rekening'] ?? '') ?>" data-nama="<?= esc($r['atas_nama'] ?? '') ?>" data-rek-id="<?= esc($rekIdVal) ?>" title="Lihat berkas asli Rekening Koran (PDF / Gambar)" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-rose-400 hover:bg-rose-50 text-rose-600 rounded-lg text-[11px] font-bold transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span>Lihat PDF</span>
                                    </button>
                                    <!-- Tombol 2: Detail Analisa Mutasi Format Spreadsheet Gambar 2 -->
                                    <button type="button" onclick="bukaModalDetailEkstraksiRekening(this)" data-file-url="<?= esc($rowFileUrl) ?>" data-rek-id="<?= esc($rekIdVal) ?>" title="Lihat Rincian & Analisa Mutasi Bulanan Rekening Koran" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-blue-500 hover:bg-blue-50 text-blue-600 rounded-lg text-[11px] font-bold transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span>Detail</span>
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <button type="button" onclick="hapusBarisRekening(this)" title="Hapus data rekening ini" class="inline-flex items-center justify-center w-7 h-7 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer">
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
     MODAL 1: VIEWER BERKAS ASLI PDF REKENING KORAN
======================================================== -->
<div id="modalPdfRekening" onclick="if(event.target === this) tutupModalPdfRekening()" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-5xl w-full p-4 sm:p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 h-[92vh] flex flex-col">
        <!-- Header Modal PDF -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-100">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900" id="pdfRekTitle">Berkas Asli Rekening Koran</h3>
                    <p class="text-xs text-slate-500" id="pdfRekSubtitle">Pratinjau dokumen rekening koran resmi nasabah</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Tombol Unggah File PDF Fisik -->
                <button type="button" onclick="document.getElementById('inputUploadPdfModal').click()" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer" title="Unggah berkas fisik PDF / Foto Rekening">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Unggah PDF</span>
                </button>
                <!-- Tombol Cetak / Simpan PDF -->
                <button type="button" onclick="cetakDigitalStatementRekening()" class="px-3 py-1.5 text-xs font-semibold text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer" title="Cetak atau simpan e-Statement sebagai PDF">
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak / PDF</span>
                </button>
                <button type="button" id="btnToggleDigitalStatement" onclick="toggleTampilanDigitalStatement()" class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span id="textToggleDigital">e-Statement Digital</span>
                </button>
                <a id="btnBukaTabBaruPdf" href="#" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Tab Baru</span>
                </a>
                <a id="btnDownloadPdf" href="#" download class="px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Download</span>
                </a>
                <button type="button" onclick="tutupModalPdfRekening()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Body Viewer -->
        <div class="flex-1 overflow-hidden py-2 relative bg-slate-100/70 rounded-2xl mt-3 flex items-center justify-center">
            <!-- Hidden input file untuk tombol Unggah PDF di header modal -->
            <input type="file" id="inputUploadPdfModal" accept=".pdf,image/jpeg,image/png,image/webp" onchange="uploadPdfLangsungModal(this)" class="hidden">

            <iframe id="iframePdfRekening" src="" class="w-full h-full rounded-xl border border-slate-200 bg-white shadow-inner"></iframe>
            <img id="imgPreviewRekening" src="" alt="Pratinjau Rekening Koran" class="max-w-full max-h-full object-contain rounded-xl border border-slate-200 bg-white hidden shadow-md">
            
            <!-- Tampilan e-Statement Digital -->
            <div id="digitalStatementContainer" class="hidden w-full h-full overflow-y-auto bg-white p-6 rounded-xl border border-slate-200 shadow-inner">
                <!-- Konten e-Statement Digital di-render dinamis -->
            </div>

            <!-- Fallback Belum Ada Berkas: Terselubung / Hidden -->
            <div id="pdfFallbackEmpty" class="hidden flex flex-col items-center justify-center p-6 sm:p-8 text-center max-w-lg w-full">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 border border-rose-200 flex items-center justify-center mb-3 shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Pratinjau Dokumen Rekening</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">Dokumen sedang dimuat ke dalam penampil berkas resmi.</p>
            </div>
        </div>
        
        <!-- Footer Modal PDF -->
        <div class="flex items-center justify-end pt-3 border-t border-slate-100 shrink-0 mt-2">
            <button type="button" onclick="tutupModalPdfRekening()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL 2: DETAIL REKAPITULASI & ANALISA MUTASI (FORMAT GAMBAR 2)
======================================================== -->
<div id="modalDetailEkstraksiRekening" onclick="if(event.target === this) tutupModalDetailEkstraksiRekening()" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 max-w-6xl w-full p-4 sm:p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200 max-h-[94vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-100">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900" id="detRekTitle">Analisa Mutasi Rekening Koran (Format Rekapitulasi Perbankan)</h3>
                    <p class="text-xs text-slate-500" id="detRekSubtitle">Rekapitulasi mutasi bulanan, perputaran saldo mengendap, debit, kredit, dan bunga/pajak</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportMatrixRekeningKeCsv()" title="Download spreadsheet rekapitulasi mutasi dalam format CSV Excel" class="px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Export Excel</span>
                </button>
                <button type="button" id="btnLihatPdfDariDetail" onclick="bukaModalPdfRekeningDariDetail()" title="Lihat berkas asli PDF rekening koran" class="px-3 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Lihat PDF Asli</span>
                </button>
                <button type="button" onclick="tutupModalDetailEkstraksiRekening()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Body Scrollable -->
        <div class="flex-1 overflow-y-auto py-3 space-y-4 pr-1">
            <!-- Header Identitas Bank & Pemilik Ringkas -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs">
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Nama Bank</span>
                    <span class="font-extrabold text-blue-900 text-xs block mt-0.5 truncate" id="detRekNamaBank">-</span>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">No. Rekening</span>
                    <span class="font-mono font-bold text-slate-800 text-xs block mt-0.5 truncate" id="detRekNoRek">-</span>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Atas Nama</span>
                    <span class="font-bold text-slate-800 truncate block mt-0.5" id="detRekAtasNama">-</span>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Periode Mutasi</span>
                    <span class="font-bold text-slate-800 block mt-0.5 truncate" id="detRekPeriode">-</span>
                </div>
                <div class="bg-blue-50/80 p-2.5 rounded-xl border border-blue-200">
                    <span class="text-[10px] text-blue-700 font-bold block uppercase tracking-wider">Rata² Kredit / Bln</span>
                    <span class="font-extrabold text-blue-800 text-xs block mt-0.5" id="detRekAvgKredit">Rp 0</span>
                </div>
            </div>

            <!-- Kartu Metrik Ringkas -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Saldo Awal</span>
                    <span class="font-black text-slate-700 block text-xs sm:text-sm mt-0.5" id="detRekSaldoAwal">Rp 0</span>
                </div>
                <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-200 shadow-2xs">
                    <span class="text-[10px] text-emerald-700 font-bold block uppercase tracking-wider">Total Mutasi Kredit</span>
                    <span class="font-black text-emerald-700 block text-xs sm:text-sm mt-0.5" id="detRekTotalKredit">Rp 0</span>
                </div>
                <div class="bg-rose-50/60 p-2.5 rounded-xl border border-rose-200 shadow-2xs">
                    <span class="text-[10px] text-rose-700 font-bold block uppercase tracking-wider">Total Mutasi Debit</span>
                    <span class="font-black text-rose-700 block text-xs sm:text-sm mt-0.5" id="detRekTotalDebit">Rp 0</span>
                </div>
                <div class="bg-blue-50/60 p-2.5 rounded-xl border border-blue-200 shadow-2xs">
                    <span class="text-[10px] text-blue-700 font-bold block uppercase tracking-wider">Saldo Akhir</span>
                    <span class="font-black text-blue-800 block text-xs sm:text-sm mt-0.5" id="detRekSaldoAkhir">Rp 0</span>
                </div>
            </div>

            <!-- =========================================================
                 TABEL SPREADSHEET REKAPITULASI MUTASI (FORMAT GAMBAR 2)
            ========================================================== -->
            <div class="border border-slate-300 rounded-2xl bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-2.5 bg-slate-100 border-b border-slate-300 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Matriks Rekapitulasi Mutasi Bulanan (Format Perbankan)</span>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500">Mata Uang: IDR (Rupiah)</span>
                </div>
                
                <div class="overflow-x-auto" id="containerSpreadsheetRekening">
                    <!-- Tabel Gambar 2 di-render secara dinamis di sini -->
                    <table class="w-full text-xs border-collapse border border-slate-300 min-w-[760px]" id="tableSpreadsheetGambar2">
                        <!-- Konten dinamis dari JS pengajuan-form.js -->
                    </table>
                </div>
            </div>

            <!-- Tabel Riwayat Mutasi Lengkap (Collapsible) -->
            <details class="border border-slate-200 rounded-2xl bg-slate-50/50 p-3 transition text-xs">
                <summary class="font-bold text-slate-700 cursor-pointer flex items-center justify-between hover:text-blue-600 transition list-none">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        <span>Lihat Riwayat Seluruh Transaksi yang Diekstrak (<span id="detRekCountTrans">0 transaksi</span>)</span>
                    </div>
                    <span class="text-blue-600 text-[11px] font-semibold underline">Buka / Tutup Log</span>
                </summary>
                <div class="mt-3 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
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
            </details>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between pt-3 border-t border-slate-100 shrink-0 gap-3">
            <a id="btnLihatBerkasAsliPdf" href="#" target="_blank" class="hidden text-xs font-bold text-blue-700 hover:text-blue-900 inline-flex items-center gap-1.5 underline cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Unduh / Buka Berkas Asli di Tab Baru</span>
            </a>
            <div class="flex-1"></div>
            <button type="button" onclick="tutupModalDetailEkstraksiRekening()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
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
