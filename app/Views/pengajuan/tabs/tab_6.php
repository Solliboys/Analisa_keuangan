<?php
$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
$tglTimestamp = !empty($kredit['created_at']) ? strtotime($kredit['created_at']) : time();
$tglPermohonan = date('j', $tglTimestamp) . ' ' . ($bulanIndo[(int)date('n', $tglTimestamp)] ?? date('F', $tglTimestamp)) . ' ' . date('Y', $tglTimestamp);

$kreditIdVal = $kredit['kredit_id'] ?? ($kredit_id ?? '1');
$noIdVal = !empty($kredit['slik_permohonan_nik']) 
    ? $kredit['slik_permohonan_nik'] 
    : (!empty($kredit['npwp_pribadi']) ? $kredit['npwp_pribadi'] : (!empty($kredit['nik']) ? $kredit['nik'] : ''));
$namaDebiturVal = !empty($kredit['nama_debitur']) ? strtoupper($kredit['nama_debitur']) : '';

// Format: [Nomor_Reff]|01|[Entitas_I/B]|[Nomor_KTP/NPWP]
$baseReff = $kreditIdVal . '/BPR-SA/IDEB/' . date('Y', $tglTimestamp);
$kodeUrut = '01';
$entitas = (isset($kredit['jenis_debitur']) && (stripos($kredit['jenis_debitur'], 'badan') !== false || stripos($kredit['jenis_debitur'], 'usaha') !== false || stripos($kredit['jenis_debitur'], 'pt') !== false || stripos($kredit['jenis_debitur'], 'cv') !== false)) ? 'B' : 'I';
$noReffSlik = $baseReff . '|' . $kodeUrut . '|' . $entitas . '|' . ($noIdVal ?: '-');

$statusSlikVal = !empty($kredit['status_slik']) ? $kredit['status_slik'] : 'Belum Terbit';
$hasilSlikVal = !empty($kredit['hasil_slik']) ? $kredit['hasil_slik'] : 'Lancar';
$ringkasanSlikVal = $kredit['ringkasan_slik'] ?? '';
$berkasSlikPdfVal = $kredit['berkas_slik_pdf'] ?? '';
$berkasSlikTxtVal = $kredit['berkas_slik_txt'] ?? '';
$pengusulVal = !empty($kredit['slik_verified_by']) ? $kredit['slik_verified_by'] : (session()->get('nama_lengkap') ?: 'Staff Pengusul Kredit');

// Perhitungan Ringkasan SLIK
$totalPlafonSlik = 0;
$totalBakiDebetSlik = 0;
$kolTerburuk = '-';
$kolPriority = ['Macet' => 5, 'Diragukan' => 4, 'Kurang Lancar' => 3, 'DPK' => 2, 'Lancar' => 1];
$maxKolVal = 0;
$countBankUmum = 0;
$countBpr = 0;
$countLembagaPembiayaan = 0;
$countLainnya = 0;

if (!empty($slik) && is_array($slik)) {
    foreach ($slik as $s) {
        $pl = (float)($s['plafon'] ?? 0);
        $bd = (float)($s['outstanding'] ?? 0);
        $totalPlafonSlik += $pl;
        $totalBakiDebetSlik += $bd;

        $bank = strtolower($s['bank_lembaga'] ?? '');
        if (strpos($bank, 'bpr') !== false || strpos($bank, 'bprs') !== false) {
            $countBpr++;
        } elseif (strpos($bank, 'bank') !== false || strpos($bank, 'mandiri') !== false || strpos($bank, 'bca') !== false || strpos($bank, 'bri') !== false || strpos($bank, 'bni') !== false) {
            $countBankUmum++;
        } elseif (strpos($bank, 'finance') !== false || strpos($bank, 'leasing') !== false || strpos($bank, 'adira') !== false || strpos($bank, 'fif') !== false || strpos($bank, 'oto') !== false) {
            $countLembagaPembiayaan++;
        } else {
            $countLainnya++;
        }

        $kol = $s['kelancaran'] ?? 'Lancar';
        $p = $kolPriority[$kol] ?? 1;
        if ($p > $maxKolVal) {
            $maxKolVal = $p;
            $kolTerburuk = $kol;
        }
    }
}

// Data permohonan awal
$daftarPermohonan = [];
$daftarPermohonan[] = [
    'index'          => 0,
    'status'         => $statusSlikVal,
    'no_reff'        => $noReffSlik,
    'tgl_permohonan' => $tglPermohonan,
    'no_id'          => $noIdVal ?: '-',
    'nama'           => $namaDebiturVal ?: '-',
    'hasil'          => $hasilSlikVal,
    'ringkasan'      => $ringkasanSlikVal,
    'berkas_pdf'     => $berkasSlikPdfVal,
    'berkas_txt'     => $berkasSlikTxtVal,
    'pengusul'       => $pengusulVal,
    'is_utama'       => true
];
?>
<div id="tab-6" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Hidden sync inputs for form submission -->
    <input type="hidden" name="status_slik" id="hidden_status_slik" value="<?= esc($statusSlikVal) ?>">
    <input type="hidden" name="hasil_slik" id="hidden_hasil_slik" value="<?= esc($hasilSlikVal) ?>">
    <input type="hidden" name="ringkasan_slik" id="ringkasan_slik" value="<?= esc($ringkasanSlikVal) ?>">
    <input type="hidden" name="berkas_slik_pdf" id="hidden_berkas_slik_pdf" value="<?= esc($berkasSlikPdfVal) ?>">
    <input type="hidden" name="berkas_slik_txt" id="hidden_berkas_slik_txt" value="<?= esc($berkasSlikTxtVal) ?>">
    <input type="hidden" name="slik_permohonan_nik" id="hidden_slik_nik" value="<?= esc($noIdVal) ?>">

    <!-- Section Header -->
    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                6
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">INFORMASI DEBITUR (SLIK OJK / IDEB)</h3>
                <p class="text-[11px] text-slate-500">Pemeriksaan dan hasil verifikasi data fasilitas kredit nasabah dari SLIK OJK</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="exportTxtPermohonanSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" title="Export Berkas TXT Permohonan SLIK OJK (Otomatis dari Nama & NIK Debitur)">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Export TXT</span>
            </button>
        </div>
    </div>

    <!-- Tabel SLIK iDeb (Tanpa Kolom Pilih) -->
    <div class="overflow-x-auto border-t border-b border-slate-100 my-2">
        <table class="w-full text-left border-collapse" id="tabelSlikIdeb">
            <thead>
                <tr class="text-[11px] font-bold text-slate-400 tracking-wider uppercase border-b border-slate-100">
                    <th class="py-3 px-3 whitespace-nowrap">STATUS</th>
                    <th class="py-3 px-3 whitespace-nowrap">NO REFF SLIK</th>
                    <th class="py-3 px-3 whitespace-nowrap">TGL PERMOHONAN</th>
                    <th class="py-3 px-3 whitespace-nowrap">NO ID</th>
                    <th class="py-3 px-3 whitespace-nowrap">NAMA</th>
                    <th class="py-3 px-3 whitespace-nowrap text-center">HASIL</th>
                    <th class="py-3 px-3 whitespace-nowrap text-center">RINGKASAN SLIK</th>
                    <th class="py-3 px-3 whitespace-nowrap text-center">HASIL SLIK PDF</th>
                    <th class="py-3 px-3 whitespace-nowrap">PENGUSUL</th>
                    <th class="py-3 px-3 whitespace-nowrap text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs" id="bodySlikIdeb">
                <?php foreach ($daftarPermohonan as $item): ?>
                    <tr class="hover:bg-slate-50/70 transition slik-row-item" data-index="<?= $item['index'] ?>" id="slik-row-<?= $item['index'] ?>">
                        <!-- STATUS -->
                        <td class="py-3.5 px-3 whitespace-nowrap" id="status-badge-cell-<?= $item['index'] ?>">
                            <?php if ($item['status'] === 'Telah Terbit' || $item['status'] === 'Terbit'): ?>
                                <span class="inline-block bg-[#00a859] text-white font-bold text-[11px] px-3 py-1 rounded shadow-2xs">
                                    Telah Terbit
                                </span>
                            <?php elseif ($item['status'] === 'Menunggu Pengecekan'): ?>
                                <span class="inline-block bg-blue-600 text-white font-bold text-[11px] px-3 py-1 rounded shadow-2xs">
                                    Menunggu
                                </span>
                            <?php else: ?>
                                <span class="inline-block bg-[#f59e0b] text-white font-bold text-[11px] px-3 py-1 rounded shadow-2xs">
                                    Belum Terbit
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- NO REFF SLIK -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-xs font-semibold text-slate-800" id="cell-reff-<?= $item['index'] ?>">
                            <?= esc($item['no_reff']) ?>
                        </td>

                        <!-- TGL PERMOHONAN -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-xs text-slate-800" id="cell-tgl-<?= $item['index'] ?>">
                            <?= esc($item['tgl_permohonan']) ?>
                        </td>

                        <!-- NO ID -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-xs font-semibold text-slate-800" id="cell-noid-<?= $item['index'] ?>">
                            <?= esc($item['no_id']) ?>
                        </td>

                        <!-- NAMA -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-xs font-bold text-slate-900 tracking-wide" id="cell-nama-<?= $item['index'] ?>">
                            <?= esc($item['nama']) ?>
                        </td>

                        <!-- HASIL -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-center" id="hasil-badge-cell-<?= $item['index'] ?>">
                            <?php 
                                $hLower = strtolower($item['hasil']);
                                if ($hLower === 'lancar' || strpos($hLower, 'kol 1') !== false || empty($item['hasil'])):
                            ?>
                                <span class="inline-block px-3 py-0.5 rounded-full border border-emerald-500 text-emerald-600 text-xs font-semibold bg-emerald-50/20">
                                    Lancar
                                </span>
                            <?php elseif (strpos($hLower, 'dpk') !== false || strpos($hLower, 'kol 2') !== false): ?>
                                <span class="inline-block px-3 py-0.5 rounded-full border border-amber-500 text-amber-600 text-xs font-semibold bg-amber-50/20">
                                    DPK
                                </span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-0.5 rounded-full border border-rose-500 text-rose-600 text-xs font-semibold bg-rose-50/20">
                                    <?= esc($item['hasil']) ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- RINGKASAN SLIK (Lihat Button) -->
                        <td class="py-3.5 px-3 text-center whitespace-nowrap">
                            <button type="button" onclick="bukaModalRingkasanSlik(<?= $item['index'] ?>)" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs">
                                <svg class="w-4 h-4 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Lihat</span>
                            </button>
                        </td>

                        <!-- HASIL SLIK PDF (Lihat Button) -->
                        <td class="py-3.5 px-3 text-center whitespace-nowrap">
                            <button type="button" onclick="bukaModalPdfSlik(<?= $item['index'] ?>)" 
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs">
                                <svg class="w-4 h-4 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span>Lihat</span>
                            </button>
                        </td>

                        <!-- PENGUSUL -->
                        <td class="py-3.5 px-3 whitespace-nowrap text-xs text-slate-800" id="cell-pengusul-<?= $item['index'] ?>">
                            <?= esc($item['pengusul']) ?>
                        </td>

                        <!-- AKSI (Navy Button "Proses ∨") -->
                        <td class="py-3.5 px-3 text-center whitespace-nowrap">
                            <div class="inline-flex items-center justify-center">
                                <button type="button" onclick="bukaModalUploadHasilSlik(<?= $item['index'] ?>)" 
                                        class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#0a0082] hover:bg-[#07005e] text-white rounded-md text-xs font-bold transition shadow-xs cursor-pointer">
                                    <span>Proses</span>
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Info Catatan Ringkas di Bawah Tabel -->
    <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2 text-slate-600">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Data SLIK iDeb terverifikasi resmi oleh sistem verifikator kredit.</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="bukaModalRingkasanSlik(0)" class="text-blue-600 font-bold hover:underline cursor-pointer">
                Buka Ringkasan Hasil SLIK &rarr;
            </button>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL 1: RINGKASAN HASIL SLIK (Tanpa Catatan Narasi Evaluasi)
======================================================== -->
<div id="modalRingkasanSlik" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
        <!-- Header: Judul & Tombol Export PDF / Export Excel + Close -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Ringkasan Hasil SLIK</h3>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportPdfRingkasanSlik()" class="px-3.5 py-1.5 bg-white border border-[#06b6d4] text-[#0891b2] hover:bg-cyan-50 rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                    <span>Export PDF</span>
                </button>
                <button type="button" onclick="exportExcelRingkasanSlik()" class="px-3.5 py-1.5 bg-[#00a859] hover:bg-[#00914c] text-white rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                    <span>Export Excel</span>
                </button>
                <button type="button" onclick="tutupModalRingkasanSlik()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg ml-2 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Body Scrollable -->
        <div class="py-5 overflow-y-auto space-y-4 flex-1">
            
            <!-- Baris 1: 3 Kartu Metrik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Plafond -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Total Plafond</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        Rp <?= number_format($totalPlafonSlik, 0, ',', '.') ?>
                    </div>
                </div>

                <!-- Total Baki Debet -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Total Baki Debet</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        Rp <?= number_format($totalBakiDebetSlik, 0, ',', '.') ?>
                    </div>
                </div>

                <!-- Kolektibilitas Terburuk -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Kolektibilitas Terburuk</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        <?= esc($kolTerburuk) ?>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">-</div>
                </div>
            </div>

            <!-- Baris 2: 4 Kartu Breakdown Bank -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Bank Umum -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Bank Umum</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        <?= (int)$countBankUmum ?>
                    </div>
                </div>

                <!-- BPR / S -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">BPR / S</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        <?= (int)$countBpr ?>
                    </div>
                </div>

                <!-- Lembaga Pembiayaan -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Lembaga Pembiayaan</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        <?= (int)$countLembagaPembiayaan ?>
                    </div>
                </div>

                <!-- Lainnya -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Lainnya</div>
                    <div class="text-base font-bold text-slate-900 mt-1">
                        <?= (int)$countLainnya ?>
                    </div>
                </div>
            </div>

            <!-- Tabel Rincian Fasilitas Kredit -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs mt-4">
                <table class="w-full text-left border-collapse text-xs" id="tabelRincianFasilitasSlik">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-2.5 px-3 border-r border-slate-200 text-center w-10">NO</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">NAMA</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[110px]">BANK</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[130px]">JENIS PENGGUNAAN</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">JENIS KREDIT</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">PLAFOND</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[120px]">BAKI DEBET</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[90px]">SUKU BUNGA</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">TGL AKAD</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">JATUH TEMPO</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">JANGKA WAKTU</th>
                            <th class="py-2.5 px-3 border-r border-slate-200 min-w-[100px]">TUNGGAKAN</th>
                            <th class="py-2.5 px-3 min-w-[110px] text-center">KOLEKTIBILITAS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="bodyRincianFasilitasSlik">
                        <?php if (empty($slik)): ?>
                            <tr>
                                <td colspan="13" class="py-8 text-center text-xs text-slate-400 italic bg-white">
                                    Tidak ada catatan fasilitas pinjaman aktif.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slik as $i => $row): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2 px-3 border-r border-slate-100 text-center font-bold"><?= $i + 1 ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-bold"><?= esc($row['nama_debitur'] ?: $namaDebiturVal) ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['bank_lembaga'] ?: '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['keterangan'] ?: ($row['jenis_fasilitas'] ?: 'Modal Kerja')) ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['jenis_fasilitas'] ?: 'Kredit Usaha') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-semibold">Rp <?= number_format((float)($row['plafon'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-bold text-blue-700">Rp <?= number_format((float)($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100">-</td>
                                    <td class="py-2 px-3 border-r border-slate-100">-</td>
                                    <td class="py-2 px-3 border-r border-slate-100">-</td>
                                    <td class="py-2 px-3 border-r border-slate-100">-</td>
                                    <td class="py-2 px-3 border-r border-slate-100">-</td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= strtolower($row['kelancaran'] ?? '') === 'lancar' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300' ?>">
                                            <?= esc($row['kelancaran'] ?? 'Lancar') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <button type="button" onclick="tutupModalRingkasanSlik()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL 2: UPLOAD HASIL SLIK (Persis Sesuai Gambar Modal Upload Hasil SLIK)
======================================================== -->
<div id="modalUploadHasilSlik" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200">
        <!-- Header: Judul & Close -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Upload Hasil SLIK</h3>
            <button type="button" onclick="tutupModalUploadHasilSlik()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Form -->
        <form id="formUploadHasilSlik" onsubmit="submitFormUploadHasilSlik(event)" class="py-4 space-y-4">
            <!-- Hasil SLIK Dropdown -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hasil SLIK</label>
                <select id="modalHasilSlikSelect" name="hasil_slik" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white focus:outline-none focus:border-blue-500 shadow-2xs cursor-pointer">
                    <option value="Lancar">Lancar</option>
                    <option value="DPK">DPK</option>
                    <option value="Kurang Lancar">Kurang Lancar</option>
                    <option value="Diragukan">Diragukan</option>
                    <option value="Macet">Macet</option>
                </select>
            </div>

            <!-- Grid 2 Upload Box: Upload Hasil TXT & Upload Hasil PDF -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <!-- Box 1: Upload Hasil TXT -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil TXT</label>
                    <div id="dropzoneTxt" onclick="document.getElementById('inputTxtFile').click()" 
                         class="border border-slate-200 hover:border-blue-400 rounded-2xl p-6 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group">
                        <input type="file" id="inputTxtFile" accept=".txt" class="hidden" onchange="handleFileSelected(this, 'txt')">
                        <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-500 font-medium group-hover:text-blue-600 transition" id="labelFileNameTxt">Drag and drop a file here or click</p>
                        <span class="text-[10px] text-slate-400 mt-1" id="labelFileSubTxt">Format .TXT</span>
                    </div>
                </div>

                <!-- Box 2: Upload Hasil PDF -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil PDF</label>
                    <div id="dropzonePdf" onclick="document.getElementById('inputPdfFile').click()" 
                         class="border border-slate-200 hover:border-blue-400 rounded-2xl p-6 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group">
                        <input type="file" id="inputPdfFile" accept=".pdf" class="hidden" onchange="handleFileSelected(this, 'pdf')">
                        <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-500 font-medium group-hover:text-blue-600 transition" id="labelFileNamePdf">Drag and drop a file here or click</p>
                        <span class="text-[10px] text-slate-400 mt-1" id="labelFileSubPdf">Format .PDF</span>
                    </div>
                </div>
            </div>

            <!-- Footer: Batal & Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="tutupModalUploadHasilSlik()" class="px-5 py-2 bg-[#64748b] hover:bg-[#475569] text-white text-xs font-bold rounded-lg transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSimpanUploadSlik" class="px-5 py-2 bg-[#00a859] hover:bg-[#00914c] text-white text-xs font-bold rounded-lg transition shadow-xs cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =======================================================
     MODAL 3: HASIL SLIK OJK (Menampilkan Hasil Upload PDF)
======================================================== -->
<div id="modalPdfSlik" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
        <!-- Header: Hasil SLIK OJK & Close X -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Hasil SLIK OJK</h3>
            <button type="button" onclick="tutupModalPdfSlik()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body: Menampilkan Berkas PDF yang Diupload pada Proses -->
        <div class="py-4 flex-1 overflow-y-auto" id="modalPdfSlikContainer">
            <div id="pdfViewerWrapper" class="hidden">
                <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl mb-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        <span class="text-xs font-bold text-slate-800" id="pdfFileNameLabel">berkas_ideb_slik.pdf</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a id="btnBukaPdfTab" href="#" target="_blank" class="px-3 py-1 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-xs font-bold transition">
                            Buka di Tab Baru ↗
                        </a>
                        <button type="button" onclick="tutupModalPdfSlik(); bukaModalUploadHasilSlik(currentActiveSlikIndex);" class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                            Unggah Ulang
                        </button>
                    </div>
                </div>
                <iframe id="iframePdfSlik" src="" class="w-full h-[65vh] border border-slate-200 rounded-xl shadow-inner"></iframe>
            </div>

            <!-- Tampilan bila belum ada PDF yang diupload -->
            <div id="pdfEmptyWrapper" class="text-center py-16 px-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-xs">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Berkas Hasil SLIK (iDeb PDF) Belum Diunggah</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Silakan klik tombol <strong>Proses</strong> pada tabel permohonan untuk mengunggah berkas hasil penarikan SLIK (PDF & TXT) debitur.</p>
                <div class="mt-5">
                    <button type="button" onclick="tutupModalPdfSlik(); bukaModalUploadHasilSlik(currentActiveSlikIndex);" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0a0082] hover:bg-[#07005e] text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Buka Form Upload Hasil SLIK</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end pt-3 border-t border-slate-100">
            <button type="button" onclick="tutupModalPdfSlik()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-xl transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>



<script>

// ========================================================
// SINKRONISASI OTOMATIS DATA DEBITUR DARI TAB SEBELUMNYA
// ========================================================
function sinkronisasiDataDebiturKeSlik() {
    // 1. Ambil Nama Debitur dari Tab 1 atau Tab 2
    const elNama1 = document.getElementById("nama_debitur");
    const elNama2 = document.getElementById("nama_debitur_tab2");
    let namaVal = (elNama1?.value || elNama2?.value || "").trim().toUpperCase();

    // 2. Ambil NIK KTP / NPWP dari Tab 2
    const elNik = document.getElementById("npwp_pribadi");
    let nikVal = (elNik?.value || "").trim();

    // 3. Ambil Jenis Debitur dari Tab 1
    const elJd = document.getElementById("jenis_debitur");
    const jdVal = (elJd?.value || "Individu").toLowerCase();
    const entitas = (jdVal.includes("badan") || jdVal.includes("usaha") || jdVal.includes("pt") || jdVal.includes("cv")) ? "B" : "I";

    // 4. Update data baris 0 (Debitur Utama)
    const cellNama = document.getElementById("cell-nama-0");
    if (cellNama && namaVal) {
        cellNama.textContent = namaVal;
    }

    const cellNoid = document.getElementById("cell-noid-0");
    if (cellNoid && nikVal) {
        cellNoid.textContent = nikVal;
    }

    const cellReff = document.getElementById("cell-reff-0");
    const kid = document.getElementById("kredit_id")?.value || "<?= $kreditIdVal ?>";
    const tahun = "<?= date("Y", $tglTimestamp) ?>";
    const baseReff = kid + "/BPR-SA/IDEB/" + tahun;
    if (cellReff) {
        cellReff.textContent = `${baseReff}|01|${entitas}|${nikVal || "-"}`;
    }

    const hiddenNik = document.getElementById("hidden_slik_nik");
    if (hiddenNik && nikVal) {
        hiddenNik.value = nikVal;
    }
}
window.sinkronisasiDataDebiturKeSlik = sinkronisasiDataDebiturKeSlik;

// Pasang listener real-time agar saat input di Tab 1 / Tab 2 diisi, Tab 6 langsung terupdate
function pasangListenerSinkronisasiSlik() {
    sinkronisasiDataDebiturKeSlik();
    ["nama_debitur", "nama_debitur_tab2", "npwp_pribadi", "jenis_debitur"].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("input", sinkronisasiDataDebiturKeSlik);
            el.addEventListener("change", sinkronisasiDataDebiturKeSlik);
        }
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", pasangListenerSinkronisasiSlik);
} else {
    pasangListenerSinkronisasiSlik();
}
setTimeout(sinkronisasiDataDebiturKeSlik, 500);

let currentActiveSlikIndex = 0;
let permohonanCount = <?= count($daftarPermohonan) ?>;

// Filter Pencarian
function filterTabelSlik(query) {
    const q = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#bodySlikIdeb tr.slik-row-item');
    rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
    });
}

// Fungsi Download Berkas TXT di Browser
function downloadTxtContent(filename, text) {
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 200);
}
window.downloadTxtContent = downloadTxtContent;

// Fungsi Export TXT Permohonan SLIK
function exportTxtPermohonanSlik() {
    if (typeof sinkronisasiDataDebiturKeSlik === 'function') {
        sinkronisasiDataDebiturKeSlik();
    }

    const cellReff = document.getElementById('cell-reff-0');
    let lineReff = cellReff ? cellReff.innerText.trim() : '';

    const kid = document.getElementById('kredit_id')?.value || '<?= $kreditIdVal ?>';
    const tahun = '<?= date("Y", $tglTimestamp) ?>';

    // Jika cell belum terisi, bangun string No Reff secara dinamis
    if (!lineReff || lineReff.includes('|-')) {
        const elNama = (document.getElementById('nama_debitur')?.value || document.getElementById('nama_debitur_tab2')?.value || '<?= esc($namaDebiturVal) ?>').trim().toUpperCase();
        const elNik = (document.getElementById('npwp_pribadi')?.value || '<?= esc($noIdVal) ?>').trim();
        const jdVal = (document.getElementById('jenis_debitur')?.value || '<?= esc($kredit['jenis_debitur'] ?? 'Individu') ?>').toLowerCase();
        const entitas = (jdVal.includes('badan') || jdVal.includes('usaha') || jdVal.includes('pt') || jdVal.includes('cv')) ? 'B' : 'I';
        lineReff = `${kid}/BPR-SA/IDEB/${tahun}|01|${entitas}|${elNik || '3515082104990002'}`;
    }

    const cleanNama = (document.getElementById('nama_debitur')?.value || document.getElementById('nama_debitur_tab2')?.value || '<?= esc($namaDebiturVal) ?>' || 'DEBITUR').trim().replace(/[^a-zA-Z0-9]/g, '_');
    const filename = `Permohonan_SLIK_${kid}_${cleanNama || 'DEBITUR'}_${tahun}.txt`;

    downloadTxtContent(filename, lineReff + "\r\n");

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Berkas TXT Permohonan SLIK berhasil diekspor!',
            showConfirmButton: false,
            timer: 2000
        });
    }
}
window.exportTxtPermohonanSlik = exportTxtPermohonanSlik;

// Modal Upload Hasil SLIK (Dibuka saat tombol "Proses" diklik)
function bukaModalUploadHasilSlik(idx) {
    currentActiveSlikIndex = idx || 0;
    const currentHasil = document.getElementById('hidden_hasil_slik').value || 'Lancar';
    const sel = document.getElementById('modalHasilSlikSelect');
    if (sel) sel.value = currentHasil;

    // Reset label file input bila belum dipilih
    const pdfPath = document.getElementById('hidden_berkas_slik_pdf').value;
    if (pdfPath) {
        document.getElementById('labelFileNamePdf').innerText = pdfPath.split('/').pop();
        document.getElementById('labelFileSubPdf').innerText = 'Berkas tersimpan';
    } else {
        document.getElementById('labelFileNamePdf').innerText = 'Drag and drop a file here or click';
        document.getElementById('labelFileSubPdf').innerText = 'Format .PDF';
    }

    const txtPath = document.getElementById('hidden_berkas_slik_txt').value;
    if (txtPath) {
        document.getElementById('labelFileNameTxt').innerText = txtPath.split('/').pop();
        document.getElementById('labelFileSubTxt').innerText = 'Berkas tersimpan';
    } else {
        document.getElementById('labelFileNameTxt').innerText = 'Drag and drop a file here or click';
        document.getElementById('labelFileSubTxt').innerText = 'Format .TXT';
    }

    document.getElementById('modalUploadHasilSlik').classList.remove('hidden');
}

function tutupModalUploadHasilSlik() {
    document.getElementById('modalUploadHasilSlik').classList.add('hidden');
}

function handleFileSelected(input, type) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (type === 'txt') {
            document.getElementById('labelFileNameTxt').innerText = file.name;
            document.getElementById('labelFileSubTxt').innerText = (file.size / 1024).toFixed(1) + ' KB';
        } else if (type === 'pdf') {
            document.getElementById('labelFileNamePdf').innerText = file.name;
            document.getElementById('labelFileSubPdf').innerText = (file.size / 1024).toFixed(1) + ' KB';
        }
    }
}

// Drag & drop support
['dropzoneTxt', 'dropzonePdf'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('dragover', (e) => {
        e.preventDefault();
        el.classList.add('border-blue-500', 'bg-blue-50/40');
    });
    el.addEventListener('dragleave', () => {
        el.classList.remove('border-blue-500', 'bg-blue-50/40');
    });
    el.addEventListener('drop', (e) => {
        e.preventDefault();
        el.classList.remove('border-blue-500', 'bg-blue-50/40');
        const inputId = id === 'dropzoneTxt' ? 'inputTxtFile' : 'inputPdfFile';
        const type = id === 'dropzoneTxt' ? 'txt' : 'pdf';
        const inp = document.getElementById(inputId);
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            inp.files = e.dataTransfer.files;
            handleFileSelected(inp, type);
        }
    });
});

// Submit Form Upload Hasil SLIK
function submitFormUploadHasilSlik(e) {
    e.preventDefault();
    const hasilVal = document.getElementById('modalHasilSlikSelect').value;
    const filePdf = document.getElementById('inputPdfFile').files[0];
    const fileTxt = document.getElementById('inputTxtFile').files[0];
    const kid = document.getElementById('kredit_id')?.value || '';

    const formData = new FormData();
    formData.append('hasil_slik', hasilVal);
    if (kid) formData.append('kredit_id', kid);
    if (filePdf) formData.append('file_pdf', filePdf);
    if (fileTxt) formData.append('file_txt', fileTxt);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Menyimpan Hasil SLIK...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    }

    fetch('<?= base_url('pengajuan/upload-hasil-slik') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status) {
            // Update hidden inputs
            document.getElementById('hidden_status_slik').value = 'Telah Terbit';
            document.getElementById('hidden_hasil_slik').value = hasilVal;
            if (data.pdf_path) document.getElementById('hidden_berkas_slik_pdf').value = data.pdf_path;
            if (data.txt_path) document.getElementById('hidden_berkas_slik_txt').value = data.txt_path;

            // Update UI row tabel
            const statusCell = document.getElementById(`status-badge-cell-${currentActiveSlikIndex}`);
            if (statusCell) {
                statusCell.innerHTML = `<span class="inline-block bg-[#00a859] text-white font-bold text-[11px] px-3 py-1 rounded shadow-2xs">Telah Terbit</span>`;
            }

            const hasilCell = document.getElementById(`hasil-badge-cell-${currentActiveSlikIndex}`);
            if (hasilCell) {
                if (hasilVal === 'Lancar') {
                    hasilCell.innerHTML = `<span class="inline-block px-3 py-0.5 rounded-full border border-emerald-500 text-emerald-600 text-xs font-semibold bg-emerald-50/20">Lancar</span>`;
                } else if (hasilVal === 'DPK') {
                    hasilCell.innerHTML = `<span class="inline-block px-3 py-0.5 rounded-full border border-amber-500 text-amber-600 text-xs font-semibold bg-amber-50/20">DPK</span>`;
                } else {
                    hasilCell.innerHTML = `<span class="inline-block px-3 py-0.5 rounded-full border border-rose-500 text-rose-600 text-xs font-semibold bg-rose-50/20">${hasilVal}</span>`;
                }
            }

            tutupModalUploadHasilSlik();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1600,
                    showConfirmButton: false
                });
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Gagal Menyimpan', data.message || 'Terjadi kesalahan sistem.', 'error');
            }
        }
    })
    .catch(err => {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'Gagal memproses ke server.', 'error');
        }
    });
}

// Modal Ringkasan Hasil SLIK
function bukaModalRingkasanSlik(idx) {
    currentActiveSlikIndex = idx || 0;
    document.getElementById('modalRingkasanSlik').classList.remove('hidden');
}

function tutupModalRingkasanSlik() {
    document.getElementById('modalRingkasanSlik').classList.add('hidden');
}

function exportPdfRingkasanSlik() {
    window.print();
}

function exportExcelRingkasanSlik() {
    const table = document.getElementById('tabelRincianFasilitasSlik');
    if (!table) return;
    let html = table.outerHTML;
    const uri = 'data:application/vnd.ms-excel;base64,' + window.btoa(unescape(encodeURIComponent(html)));
    const a = document.createElement('a');
    a.href = uri;
    a.download = 'Ringkasan_Hasil_SLIK_' + (document.getElementById('nama_debitur')?.value || 'Nasabah') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Modal Hasil SLIK OJK PDF (Menampilkan PDF yang diupload di Proses tadi)
function bukaModalPdfSlik(idx) {
    currentActiveSlikIndex = idx || 0;
    const pdfPath = document.getElementById('hidden_berkas_slik_pdf').value;
    const wrapper = document.getElementById('pdfViewerWrapper');
    const emptyWrapper = document.getElementById('pdfEmptyWrapper');
    const iframe = document.getElementById('iframePdfSlik');
    const btnBuka = document.getElementById('btnBukaPdfTab');
    const label = document.getElementById('pdfFileNameLabel');

    if (pdfPath && pdfPath.trim() !== '') {
        const fullUrl = pdfPath.startsWith('http') ? pdfPath : ('<?= base_url() ?>/' + pdfPath.replace(/^\//, ''));
        iframe.src = fullUrl;
        btnBuka.href = fullUrl;
        if (label) label.innerText = pdfPath.split('/').pop();
        wrapper.classList.remove('hidden');
        emptyWrapper.classList.add('hidden');
    } else {
        iframe.src = '';
        wrapper.classList.add('hidden');
        emptyWrapper.classList.remove('hidden');
    }

    document.getElementById('modalPdfSlik').classList.remove('hidden');
}

function tutupModalPdfSlik() {
    document.getElementById('modalPdfSlik').classList.add('hidden');
}

function gantiPageLengthSlik(val) {
    // Pengaturan pagination
}

function hapusBarisPermohonanSlik(idx) {
    const row = document.getElementById(`slik-row-${idx}`);
    if (!row) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Permohonan SLIK?',
            text: 'Baris permohonan ini akan dihapus dari daftar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                row.remove();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Permohonan SLIK berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    } else {
        if (confirm('Hapus baris permohonan SLIK ini?')) {
            row.remove();
        }
    }
}
</script>
