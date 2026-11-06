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

// Daftar Permohonan SLIK Tersimpan
$daftarPermohonanSlik = [];
$daftarPermohonanSlikDocs = [];
if (!empty($kredit['daftar_permohonan_slik'])) {
    $decodedSlik = json_decode($kredit['daftar_permohonan_slik'], true);
    if (is_array($decodedSlik)) {
        $daftarPermohonanSlik = $decodedSlik;
        foreach ($daftarPermohonanSlik as $rowSlik) {
            $rIdx = $rowSlik['index'] ?? 0;
            $daftarPermohonanSlikDocs[$rIdx] = [
                'nama' => $rowSlik['nama'] ?? '',
                'identitas' => $rowSlik['file_identitas'] ?? null,
                'permohonan' => $rowSlik['file_permohonan'] ?? null
            ];
        }
    }
}

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


$isSuperadmin = (strtolower(session()->get('jabatan') ?? '') === 'superadmin');
$isPetugasSlikRole = (strpos(strtolower(session()->get('jabatan') ?? ''), 'slik') !== false);
$petugasSlikAktif = (int)($pengaturan_sistem['petugas_slik_aktif'] ?? (($pengaturan_sistem['ijin_pengusul_isi_slik'] ?? 1) == 0 ? 1 : 0));
$canEditSlik = ($isSuperadmin || $isPetugasSlikRole || ($petugasSlikAktif === 0));
?>
<div id="tab-6" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Hidden sync inputs for form submission -->
    <input type="hidden" name="daftar_permohonan_slik" id="hidden_daftar_permohonan_slik" value="<?= esc(!empty($kredit['daftar_permohonan_slik']) ? $kredit['daftar_permohonan_slik'] : '[]') ?>">
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
                <?= (!empty($view_folder) && $view_folder === 'badan_usaha') ? '5' : '6' ?>
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">INFORMASI DEBITUR (SLIK OJK / IDEB)</h3>
                <p class="text-[11px] text-slate-500">Pemeriksaan dan hasil verifikasi data fasilitas kredit nasabah dari SLIK OJK</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <?php if (!$canEditSlik): ?>
                <button type="button" 
                        id="btnKirimKePetugasSlikHeader" 
                        onclick="konfirmasiKirimPengajuanKePetugasSlik()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 <?= ($statusSlikVal === 'Menunggu Pengecekan') ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-blue-600 hover:bg-blue-700' ?> text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" 
                        title="Kirim Berkas Permohonan SLIK ke Petugas SLIK Terpusat">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    <span id="textBtnKirimSlikHeader"><?= ($statusSlikVal === 'Menunggu Pengecekan') ? 'Antrean Petugas SLIK' : 'Kirim Pengajuan ke Petugas SLIK' ?></span>
                </button>
            <?php endif; ?>
            <button type="button" onclick="bukaModalTambahPermohonanSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" title="Tambah permohonan SLIK untuk pasangan, penjamin, atau pengurus">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Pengajuan SLIK</span>
            </button>
            <?php if ($canEditSlik): ?>
                <button type="button" onclick="exportTxtPermohonanSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer" title="Export Berkas TXT Permohonan SLIK OJK (Otomatis dari data terpilih)">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Export TXT</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alert Status Alur Petugas SLIK vs Pengusul -->
    <?php if (!$canEditSlik): ?>
        <div class="mb-5 p-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs" id="bannerAlurPetugasSlik">
            <div class="flex items-start gap-3 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0 text-sm shadow-xs">
                    🔒
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-bold text-xs uppercase tracking-wide text-amber-900">Alur Verifikasi: Petugas SLIK OJK Terpusat</h4>
                        <span id="badgeAlertSlik" class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= ($statusSlikVal === 'Menunggu Pengecekan') ? 'bg-blue-100 text-blue-800' : 'bg-amber-200/80 text-amber-900' ?>">
                            <?= ($statusSlikVal === 'Menunggu Pengecekan') ? 'Antrean Petugas SLIK' : 'Verifikator Resmi (SOP Ketat)' ?>
                        </span>
                    </div>
                    <p id="deskripsiAlertSlik" class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                        <?= ($statusSlikVal === 'Menunggu Pengecekan')
                            ? 'Permohonan SLIK debitur telah berhasil dikirimkan ke <strong>Petugas Verifikator SLIK OJK</strong>. Anda dapat langsung melanjutkan pengisian formulir ke tahap berikutnya tanpa terhambat.'
                            : 'Pemeriksaan riwayat fasilitas kredit debitur ditangani oleh <strong>Petugas SLIK OJK</strong>. Silakan periksa data permohonan debitur di bawah, lalu klik <strong>Kirim Pengajuan ke Petugas SLIK</strong> untuk memproses penarikan iDeb resmi.'
                        ?>
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2 pl-11 sm:pl-0">
                <button type="button" onclick="konfirmasiKirimPengajuanKePetugasSlik()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    <span id="btnAksiBannerSlik"><?= ($statusSlikVal === 'Menunggu Pengecekan') ? 'Kirim Ulang ke Petugas' : 'Kirim ke Petugas SLIK' ?></span>
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/90 text-emerald-950 flex items-start gap-3 shadow-2xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0 text-sm shadow-xs">
                ⚡
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <h4 class="font-bold text-xs uppercase tracking-wide text-emerald-900">Mode Fleksibel Pengusul (Single AO)</h4>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-200/80 text-emerald-900">Petugas SLIK Nonaktif</span>
                </div>
                <p class="text-[11px] text-emerald-800 mt-1 leading-relaxed">
                    Fitur Petugas SLIK saat ini dinonaktifkan. Pengusul dapat <strong>langsung menginput hasil kolektibilitas, mengunggah berkas PDF/TXT hasil penarikan SLIK</strong>, dan mengelola rincian fasilitas pinjaman secara mandiri melalui tombol <strong>Proses</strong> pada tabel di bawah.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Tabel SLIK iDeb (Full Width - Tanpa Geser) -->
    <div class="w-full overflow-x-auto border-t border-b border-slate-100 my-2">
        <table class="w-full text-left border-collapse text-[11px] table-auto" id="tabelSlikIdeb">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 tracking-wider uppercase border-b border-slate-100 bg-slate-50/50">
                    <th class="py-2.5 px-2 text-center whitespace-nowrap w-8">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="checkAllSlik" onchange="toggleSelectAllSlik(this)" class="w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer" checked title="Pilih Semua">
                        </div>
                    </th>
                    <th class="py-2.5 px-2 whitespace-nowrap">STATUS</th>
                    <th class="py-2.5 px-2 whitespace-nowrap">NO REFF SLIK</th>
                    <th class="py-2.5 px-2 whitespace-nowrap">TANGGAL</th>
                    <th class="py-2.5 px-2 whitespace-nowrap">NO ID</th>
                    <th class="py-2.5 px-2 whitespace-nowrap">NAMA</th>
                    <th class="py-2.5 px-2 whitespace-nowrap text-center">HASIL</th>
                    <th class="py-2.5 px-2 whitespace-nowrap text-center">RINGKASAN</th>
                    <th class="py-2.5 px-2 whitespace-nowrap text-center">HASIL PDF</th>
                    <th class="py-2.5 px-2 whitespace-nowrap">PENGUSUL</th>
                    <th class="py-2.5 px-2 whitespace-nowrap text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-[11px]" id="bodySlikIdeb">
                <?php if (empty($daftarPermohonanSlik)): ?>
                    <tr id="emptyStateSlik">
                        <td colspan="11" class="py-10 text-center text-xs text-slate-400 font-medium">
                            Belum ada permohonan SLIK. Klik <strong>Tambah Pengajuan SLIK</strong> untuk menambah.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarPermohonanSlik as $row): 
                        $idx = $row['index'] ?? 0;
                        $rowNama = $row['nama'] ?? '';
                        $rowNoId = $row['no_id'] ?? '';
                        $rowKeterkaitan = $row['keterkaitan'] ?? 'Pemohon';
                        $rowNoReff = $row['no_reff'] ?? '';
                        $rowTgl = $row['tanggal'] ?? '';
                        $rowStatus = $row['status'] ?? 'Belum Terbit';
                        $rowHasil = $row['hasil'] ?? 'Menunggu';
                        $rowPengusul = $row['pengusul'] ?? ($pengusulVal ?: 'Staff Pengusul Kredit');
                    ?>
                    <?php 
                        $isTerbit = (strtolower($rowStatus) === "telah terbit");
                        $colorHasil = "border-slate-300 text-slate-500 bg-slate-50/50";
                        if (strtolower($rowHasil) === "lancar") {
                            $colorHasil = "border-[#00a859] text-[#00a859] bg-emerald-50/30";
                        } elseif (in_array(strtolower($rowHasil), ["dpk", "kurang lancar", "diragukan", "macet"])) {
                            $colorHasil = "border-amber-500 text-amber-600 bg-amber-50/30";
                        }
                    ?>
                    <tr class="hover:bg-slate-50/70 transition slik-row-item" data-index="<?= $idx ?>" id="slik-row-<?= $idx ?>">
                        <td class="py-2.5 px-2 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" class="slik-select-item w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer" data-index="<?= $idx ?>" value="<?= $idx ?>" checked>
                            </div>
                        </td>
                        <td class="py-2.5 px-2 whitespace-nowrap" id="status-badge-cell-<?= $idx ?>">
                            <span class="inline-block <?= $isTerbit ? "bg-[#00a859]" : "bg-[#f59e0b]" ?> text-white font-bold text-[10px] px-2 py-0.5 rounded shadow-2xs">
                                <?= esc($rowStatus) ?>
                            </span>
                        </td>
                        <td class="py-2.5 px-2 text-[11px] font-semibold text-slate-700 max-w-[130px] truncate" id="cell-reff-<?= $idx ?>" title="<?= esc($rowNoReff) ?>">
                            <?= esc($rowNoReff) ?>
                        </td>
                        <td class="py-2.5 px-2 whitespace-nowrap text-[11px] text-slate-600" id="cell-tgl-<?= $idx ?>">
                            <?= esc($rowTgl) ?>
                        </td>
                        <td class="py-2.5 px-2 whitespace-nowrap text-[11px] font-semibold text-slate-800" id="cell-noid-<?= $idx ?>">
                            <?= esc($rowNoId) ?>
                        </td>
                        <td class="py-2.5 px-2 whitespace-nowrap text-[11px] font-bold text-slate-900" id="cell-nama-<?= $idx ?>">
                            <div class="flex items-center gap-1">
                                <span><?= esc($rowNama) ?></span>
                                <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase"><?= esc($rowKeterkaitan) ?></span>
                            </div>
                        </td>
                        <td class="py-2.5 px-2 whitespace-nowrap text-center" id="hasil-badge-cell-<?= $idx ?>">
                            <span class="inline-block px-2 py-0.5 rounded-full border <?= $colorHasil ?> text-[10px] font-bold">
                                <?= esc($rowHasil) ?>
                            </span>
                        </td>
                        <td class="py-2.5 px-2 text-center whitespace-nowrap">
                            <button type="button" onclick="bukaModalRingkasanSlik(<?= $idx ?>)" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Lihat</span>
                            </button>
                        </td>
                        <td class="py-2.5 px-2 text-center whitespace-nowrap">
                            <button type="button" onclick="bukaModalPdfSlik(<?= $idx ?>)" 
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Lihat</span>
                            </button>
                        </td>
                        <td class="py-2.5 px-2 text-[11px] text-slate-600 max-w-[100px] truncate" id="cell-pengusul-<?= $idx ?>" title="<?= esc($rowPengusul) ?>">
                            <?= esc($rowPengusul) ?>
                        </td>
                        <td class="py-2.5 px-2 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1 justify-center">
                                <button type="button" onclick="bukaModalLihatDokumenSlik(<?= $idx ?>)" 
                                        class="p-1 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-md transition cursor-pointer border border-blue-200 shadow-2xs" 
                                        title="Lihat Dokumen yang Diunggah (Identitas & Permohonan)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </button>
                                <button type="button" onclick="toggleDropdownProsesSlik(event,<?= $idx ?>)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#0a0082] hover:bg-[#07005e] text-white rounded-md text-[11px] font-bold transition shadow-xs cursor-pointer"
                                        data-proses-idx="<?= $idx ?>">
                                    <span>Proses</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <button type="button" onclick="hapusBarisPermohonanSlik(<?= $idx ?>)" 
                                        class="p-1 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-md transition cursor-pointer border border-rose-200" title="Hapus permohonan ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
</div>

<!-- =======================================================
     MODAL 1: RINGKASAN HASIL SLIK
======================================================== -->
<div id="modalRingkasanSlik" onclick="if(event.target === this) tutupModalRingkasanSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
        <!-- Header: Judul & Tombol Export PDF / Export Excel + Close -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900">Ringkasan Hasil SLIK</h3>
                <p class="text-[11px] text-slate-500 mt-0.5" id="ringkasanModalSubJudul">Laporan riwayat dan evaluasi fasilitas pinjaman perbankan</p>
                <div id="loadingRingkasanSlik" class="hidden items-center gap-1.5 mt-1.5 text-xs font-semibold text-blue-600 animate-pulse">
                    <svg class="w-3.5 h-3.5 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat ringkasan...</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportPdfRingkasanSlik()" class="px-3.5 py-1.5 bg-white border border-[#06b6d4] text-[#0891b2] hover:bg-cyan-50 rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                    <span>Export PDF</span>
                </button>
                <button type="button" onclick="exportExcelRingkasanSlik()" class="px-3.5 py-1.5 bg-[#00a859] hover:bg-[#00914c] text-white rounded-md text-xs font-semibold transition cursor-pointer shadow-2xs flex items-center gap-1.5">
                    <span>Export Excel</span>
                </button>
                <button type="button" onclick="tutupModalRingkasanSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 ml-1 transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Body Scrollable -->
        <div class="py-5 overflow-y-auto space-y-4 flex-1">
            <!-- Banner Identitas Subjek Aktif -->
            <div class="p-3.5 bg-indigo-50/70 border border-indigo-200/70 rounded-xl flex flex-wrap items-center justify-between gap-3 text-xs shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        IDEB
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span id="ringkasanDebiturNama"><?= esc($namaDebiturVal ?: 'Debitur') ?></span>
                            <span id="ringkasanDebiturKeterkaitan" class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase">PEMOHON</span>
                        </div>
                        <div class="text-[11px] text-slate-600 mt-0.5">
                            <span>No. ID: <strong id="ringkasanDebiturNoId" class="font-mono"><?= esc($noIdVal ?: '-') ?></strong></span>
                            <span class="mx-1.5 text-slate-300">|</span>
                            <span>No. Reff: <span id="ringkasanDebiturNoReff" class="font-mono"><?= esc($noReffSlik) ?></span></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span id="ringkasanDebiturHasilBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        <?= esc($hasilSlikVal) ?>
                    </span>
                    <button type="button" onclick="tutupModalRingkasanSlik(); bukaModalPdfSlik(currentActiveSlikIndex);" class="px-3 py-1 bg-white border border-[#4834d4] text-[#4834d4] hover:bg-indigo-50 rounded-lg text-xs font-semibold shadow-2xs transition inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat PDF SLIK</span>
                    </button>
                </div>
            </div>
            
            <!-- Baris 1: 3 Kartu Metrik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Plafond -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Total Plafond</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricPlafon">
                        Rp <?= number_format($totalPlafonSlik, 0, ',', '.') ?>
                    </div>
                </div>

                <!-- Total Baki Debet -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Total Baki Debet</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBakiDebet">
                        Rp <?= number_format($totalBakiDebetSlik, 0, ',', '.') ?>
                    </div>
                </div>

                <!-- Kolektibilitas Terburuk -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Kolektibilitas Terburuk</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricKol">
                        <?= esc($kolTerburuk) ?>
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5" id="ringkasanMetricKolSub">Berdasarkan data fasilitas</div>
                </div>
            </div>

            <!-- Baris 2: 4 Kartu Breakdown Bank -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Bank Umum -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Bank Umum</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBankUmum">
                        <?= (int)$countBankUmum ?>
                    </div>
                </div>

                <!-- BPR / S -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">BPR / S</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricBpr">
                        <?= (int)$countBpr ?>
                    </div>
                </div>

                <!-- Lembaga Pembiayaan -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Lembaga Pembiayaan</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricLembaga">
                        <?= (int)$countLembagaPembiayaan ?>
                    </div>
                </div>

                <!-- Lainnya -->
                <div class="p-4 rounded-xl border border-slate-200/90 bg-white shadow-2xs">
                    <div class="text-[11px] text-slate-500 font-medium">Lainnya</div>
                    <div class="text-base font-bold text-slate-900 mt-1" id="ringkasanMetricLainnya">
                        <?= (int)$countLainnya ?>
                    </div>
                </div>
            </div>

            <!-- Header Tabel & Tombol Tambah Fasilitas -->
            <div class="flex items-center justify-between pt-2">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Rincian Fasilitas Pinjaman / Kredit</h4>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="bukaFormTambahFasilitasSlik()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Fasilitas</span>
                    </button>
                </div>
            </div>

            <!-- Form Tambah Fasilitas Cepat (Collapsible) -->
            <div id="formTambahFasilitasSlikContainer" class="hidden p-4 rounded-xl border border-blue-200 bg-blue-50/50 space-y-3 transition">
                <div class="font-bold text-xs text-blue-900 flex items-center justify-between">
                    <span>Input Catatan Fasilitas Pinjaman</span>
                    <button type="button" onclick="tutupFormTambahFasilitasSlik()" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nama Bank / Lembaga</label>
                        <input type="text" id="inputFasBank" placeholder="Contoh: Bank BRI / BCA" class="w-full bg-white border border-slate-300 rounded-lg p-2 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jenis Fasilitas / Kredit</label>
                        <input type="text" id="inputFasJenis" placeholder="Modal Kerja / Konsumtif" class="w-full bg-white border border-slate-300 rounded-lg p-2 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Plafond (Rp)</label>
                        <input type="text" id="inputFasPlafon" placeholder="0" oninput="this.value = formatRupiahInput(this.value)" class="w-full bg-white border border-slate-300 rounded-lg p-2 font-bold focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Baki Debet (Rp)</label>
                        <input type="text" id="inputFasOutstanding" placeholder="0" oninput="this.value = formatRupiahInput(this.value)" class="w-full bg-white border border-slate-300 rounded-lg p-2 font-bold text-blue-700 focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kolektibilitas</label>
                        <select id="inputFasKelancaran" class="w-full bg-white border border-slate-300 rounded-lg p-2 focus:border-blue-500 focus:outline-none cursor-pointer">
                            <option value="Lancar">Kol 1 (Lancar)</option>
                            <option value="DPK">Kol 2 (DPK)</option>
                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                            <option value="Macet">Kol 5 (Macet)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Suku Bunga</label>
                        <input type="text" id="inputFasBunga" placeholder="Contoh: 9%" class="w-full bg-white border border-slate-300 rounded-lg p-2 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="button" onclick="simpanFasilitasSlikBaru()" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition shadow-xs cursor-pointer">
                            Simpan ke Rincian
                        </button>
                        <button type="button" onclick="tutupFormTambahFasilitasSlik()" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg font-bold transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Rincian Fasilitas Kredit -->
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs mt-1">
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
                            <th class="py-2.5 px-2 text-center w-10">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700" id="bodyRincianFasilitasSlik">
                        <?php if (empty($slik)): ?>
                            <tr id="emptyFasilitasRow">
                                <td colspan="14" class="py-8 text-center text-xs text-slate-400 italic bg-white">
                                    Tidak ada catatan fasilitas pinjaman aktif.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slik as $i => $row): ?>
                                <tr class="hover:bg-slate-50/80 transition" id="row-fasilitas-<?= $i ?>">
                                    <td class="py-2 px-3 border-r border-slate-100 text-center font-bold"><?= $i + 1 ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-bold"><?= esc(!empty($row['nama_debitur']) ? $row['nama_debitur'] : ($namaDebiturVal ?? '-')) ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['bank_lembaga']) ? $row['bank_lembaga'] : '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['keterangan']) ? $row['keterangan'] : (!empty($row['jenis_fasilitas']) ? $row['jenis_fasilitas'] : 'Modal Kerja')) ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc(!empty($row['jenis_fasilitas']) ? $row['jenis_fasilitas'] : (!empty($row['keterangan']) ? $row['keterangan'] : 'Kredit Usaha')) ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-semibold">Rp <?= number_format((float)($row['plafon'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100 font-bold text-blue-700">Rp <?= number_format((float)($row['outstanding'] ?? 0), 0, ',', '.') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['suku_bunga'] ?? '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['tgl_akad'] ?? '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['jatuh_tempo'] ?? '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['jangka_waktu'] ?? '-') ?></td>
                                    <td class="py-2 px-3 border-r border-slate-100"><?= esc($row['tunggakan'] ?? '0') ?></td>
                                    <td class="py-2 px-3 text-center border-r border-slate-100">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= strtolower($row['kelancaran'] ?? '') === 'lancar' ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300' ?>">
                                            <?= esc($row['kelancaran'] ?? 'Lancar') ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-2 text-center">
                                        <button type="button" onclick="hapusFasilitasSlik(<?= $i ?>)" class="text-rose-500 hover:text-rose-700 p-1" title="Hapus">✕</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- Container hidden input sync untuk fasilitas SLIK agar otomatis tersimpan saat submit form -->
            <div id="hiddenFasilitasSlikContainer"></div>
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
<div id="modalUploadHasilSlik" onclick="if(event.target === this) tutupModalUploadHasilSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200">
        <!-- Header: Judul & Close -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Upload Hasil SLIK</h3>
            <button type="button" onclick="tutupModalUploadHasilSlik()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Form -->
        <div id="formUploadHasilSlik" class="py-4 space-y-4">
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
                <div class="min-w-0 overflow-hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil TXT</label>
                    <div id="dropzoneTxt" onclick="document.getElementById('inputTxtFile').click()" 
                         class="border border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group w-full max-w-full overflow-hidden">
                        <input type="file" id="inputTxtFile" accept=".txt" class="hidden" onchange="handleFileSelected(this, 'txt')">
                        <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <p id="labelFileNameTxt" class="text-xs text-slate-700 font-semibold group-hover:text-blue-600 transition max-w-[190px] sm:max-w-[220px] mx-auto px-2 truncate block overflow-hidden">Drag and drop a file here or click</p>
                        <span class="text-[10px] text-slate-400 mt-1 block truncate max-w-full" id="labelFileSubTxt">Format .TXT</span>
                    </div>
                </div>

                <!-- Box 2: Upload Hasil PDF -->
                <div class="min-w-0 overflow-hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Upload Hasil PDF</label>
                    <div id="dropzonePdf" onclick="document.getElementById('inputPdfFile').click()" 
                         class="border border-slate-200 hover:border-blue-400 rounded-2xl p-4 text-center flex flex-col items-center justify-center min-h-[160px] bg-white transition cursor-pointer shadow-2xs group w-full max-w-full overflow-hidden">
                        <input type="file" id="inputPdfFile" accept=".pdf" class="hidden" onchange="handleFileSelected(this, 'pdf')">
                        <div class="w-12 h-12 rounded-full bg-slate-50 group-hover:bg-blue-50 text-slate-400 group-hover:text-blue-600 flex items-center justify-center transition mb-2 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                        </div>
                        <p id="labelFileNamePdf" class="text-xs text-slate-700 font-semibold group-hover:text-blue-600 transition max-w-[190px] sm:max-w-[220px] mx-auto px-2 truncate block overflow-hidden">Drag and drop a file here or click</p>
                        <span class="text-[10px] text-slate-400 mt-1 block truncate max-w-full" id="labelFileSubPdf">Format .PDF</span>
                    </div>
                </div>
            </div>

            <!-- Footer: Batal & Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="tutupModalUploadHasilSlik()" class="px-5 py-2 bg-[#64748b] hover:bg-[#475569] text-white text-xs font-bold rounded-lg transition cursor-pointer">
                    Batal
                </button>
                <button type="button" onclick="submitFormUploadHasilSlik(event)" id="btnSimpanUploadSlik" class="px-5 py-2 bg-[#00a859] hover:bg-[#00914c] text-white text-xs font-bold rounded-lg transition shadow-xs cursor-pointer">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL 3: HASIL SLIK OJK (Menampilkan Hasil Upload PDF)
======================================================== -->
<div id="modalPdfSlik" onclick="if(event.target === this) tutupModalPdfSlik()" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-5xl w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
        <!-- Header: Hasil SLIK OJK & Close X -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Hasil SLIK OJK</h3>
            <button type="button" onclick="tutupModalPdfSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer" title="Tutup">
                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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



<!-- Modal Tambah Permohonan SLIK -->

<!-- =======================================================
     MODAL: LIHAT DOKUMEN PENGAJUAN SLIK (Identitas & Permohonan)
======================================================== -->
<div id="modalLihatDokumenSlik" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-lg w-full p-6 shadow-2xl relative animate-in fade-in duration-200 flex flex-col">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-900">Dokumen Pengajuan SLIK</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="modalLihatDokumenSub">Berkas identitas dan permohonan</p>
            </div>
            <button type="button" onclick="tutupModalLihatDokumenSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-3.5 py-4" id="modalLihatDokumenBody">
            <!-- Dokumen Identitas Card -->
            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-xs">
                        ID
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Dokumen Identitas (KTP/NPWP)</div>
                        <div class="text-[11px] text-slate-500 truncate" id="lihatDocNamaIdentitas">-</div>
                    </div>
                </div>
                <div id="lihatDocBtnIdentitas" class="shrink-0">
                    <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                </div>
            </div>

            <!-- Dokumen Permohonan Card -->
            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-xs">
                        REQ
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-800">Dokumen Permohonan SLIK</div>
                        <div class="text-[11px] text-slate-500 truncate" id="lihatDocNamaPermohonan">-</div>
                    </div>
                </div>
                <div id="lihatDocBtnPermohonan" class="shrink-0">
                    <span class="text-xs text-slate-400 italic">Tidak ada berkas</span>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-3 border-t border-slate-100">
            <button type="button" onclick="tutupModalLihatDokumenSlik()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- =======================================================
     MODAL PREVIEW BERKAS DOKUMEN SLIK (Image / PDF / TXT)
======================================================== -->
<div id="modalPreviewDocSlik" class="fixed inset-0 z-[60] bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 hidden">
    <div class="bg-white rounded-2xl border border-slate-200 max-w-4xl w-full p-4 sm:p-5 shadow-2xl relative animate-in fade-in duration-200 flex flex-col max-h-[92vh]">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
            <div class="min-w-0 pr-2">
                <h3 class="text-sm sm:text-base font-bold text-slate-900 truncate" id="previewDocTitle">Preview Dokumen</h3>
                <p class="text-xs text-slate-500 truncate" id="previewDocSub">Berkas pengajuan</p>
            </div>
            <button type="button" onclick="tutupPreviewDocSlik()" class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-auto py-3 flex items-center justify-center min-h-[350px] bg-slate-50/70 rounded-xl mt-3" id="previewDocContainer">
            <!-- Konten Pratinjau Dokumen -->
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 shrink-0 mt-3">
            <button type="button" onclick="tutupPreviewDocSlik()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg text-xs font-bold transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<div id="modalTambahPermohonanSlik" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-200">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Pengajuan SLIK <?= (isset($view_folder) && $view_folder === 'badan_usaha') ? 'Badan Usaha' : 'Perorangan' ?></h3>
            <button type="button" onclick="tutupModalTambahPermohonanSlik()" class="text-slate-400 hover:text-slate-600 text-lg p-1 transition cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Body -->
        <div class="space-y-4 text-xs sm:text-sm">
            <!-- Nama -->
            <div>
                <label class="block font-semibold text-slate-800 mb-1">Nama</label>
                <input type="text" id="inputSlikNama" placeholder="" class="w-full text-xs sm:text-sm border border-slate-300 rounded-lg px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition">
                <div class="mt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="checkNamaSamaPemohon" onchange="toggleNamaSamaPemohon(this)" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <span class="text-xs text-slate-700 font-medium">Nama Sama dengan Pemohon</span>
                    </label>
                </div>
            </div>

            <!-- Nomor Identitas -->
            <div>
                <label class="block font-semibold text-slate-800 mb-1">Nomor Identitas</label>
                <input type="text" id="inputSlikNoId" inputmode="numeric" pattern="[0-9]*" maxlength="20" placeholder="16 digit angka (contoh: 3201012345670001)" oninput="this.value = this.value.replace(/\D/g, '')" class="w-full text-xs sm:text-sm border border-slate-300 rounded-lg px-3.5 py-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition font-mono">
            </div>

            <!-- Keterkaitan -->
            <div>
                <label class="block font-semibold text-slate-800 mb-1">Keterkaitan</label>
                <div class="relative">
                    <select id="inputSlikKeterkaitan" class="w-full text-xs sm:text-sm border border-slate-300 rounded-lg px-3.5 py-2.5 pr-8 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition cursor-pointer appearance-none">
                        <option value="" disabled selected>Pilih Keterkaitan</option>
                        <option value="Pemohon">Pemohon / Debitur Utama</option>
                        <option value="Pasangan">Pasangan</option>
                        <option value="Penjamin">Penjamin</option>
                        <option value="Pengurus / Direksi">Pengurus / Direksi</option>
                        <option value="Pemegang Saham">Pemegang Saham</option>
                        <option value="Pemilik Jaminan">Pemilik Jaminan</option>
                    </select>
                    
                </div>
            </div>

            <!-- 2 Kolom Upload: Upload Identitas & Upload Permohonan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <!-- Upload Identitas -->
                <div class="min-w-0 overflow-hidden">
                    <label class="block font-semibold text-slate-800 mb-1">Upload Identitas</label>
                    <div onclick="document.getElementById('inputSlikFileIdentitas').click()"
                         id="dropzoneIdentitas"
                         class="border-2 border-dashed border-slate-200 hover:border-blue-400 bg-white hover:bg-slate-50/50 rounded-xl p-4 text-center cursor-pointer transition flex flex-col items-center justify-center min-h-[140px] w-full max-w-full overflow-hidden">
                        <input type="file" id="inputSlikFileIdentitas" accept=".pdf,image/*" class="hidden" onchange="handleModalFileSlik(this, 'identitas')">
                        <div class="text-slate-400 mb-2 shrink-0">
                            <svg class="w-7 h-7 mx-auto stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div id="labelFileIdentitas" class="text-xs text-slate-700 font-semibold truncate block max-w-[180px] sm:max-w-[210px] mx-auto px-2 overflow-hidden">Drag and drop a file here or click</div>
                        <div id="subFileIdentitas" class="text-[10px] text-slate-400 mt-0.5 block truncate max-w-full">Format PDF / Gambar</div>
                    </div>
                </div>

                <!-- Upload Permohonan -->
                <div class="min-w-0 overflow-hidden">
                    <label class="block font-semibold text-slate-800 mb-1">Upload Permohonan</label>
                    <div onclick="document.getElementById('inputSlikFilePermohonan').click()"
                         id="dropzonePermohonan"
                         class="border-2 border-dashed border-slate-200 hover:border-blue-400 bg-white hover:bg-slate-50/50 rounded-xl p-4 text-center cursor-pointer transition flex flex-col items-center justify-center min-h-[140px] w-full max-w-full overflow-hidden">
                        <input type="file" id="inputSlikFilePermohonan" accept=".pdf,image/*,.txt" class="hidden" onchange="handleModalFileSlik(this, 'permohonan')">
                        <div class="text-slate-400 mb-2 shrink-0">
                            <svg class="w-7 h-7 mx-auto stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div id="labelFilePermohonan" class="text-xs text-slate-700 font-semibold truncate block max-w-[180px] sm:max-w-[210px] mx-auto px-2 overflow-hidden">Drag and drop a file here or click</div>
                        <div id="subFilePermohonan" class="text-[10px] text-slate-400 mt-0.5 block truncate max-w-full">Format PDF / TXT</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-slate-200">
            <button type="button" onclick="tutupModalTambahPermohonanSlik()" class="px-5 py-2 bg-[#6b7280] hover:bg-[#4b5563] text-white text-xs sm:text-sm font-semibold rounded-lg transition cursor-pointer">
                Batal
            </button>
            <button type="button" onclick="simpanTambahPermohonanSlik()" class="px-5 py-2 bg-[#00a859] hover:bg-[#008f4c] text-white text-xs sm:text-sm font-semibold rounded-lg transition shadow-xs cursor-pointer">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
    window.slikDokumenUploaded = <?= json_encode(!empty($daftarPermohonanSlikDocs) ? (object)$daftarPermohonanSlikDocs : new stdClass()) ?>;
    window.slikFasilitasArray = <?= json_encode(!empty($slik) ? $slik : []) ?>;
    window.BERKAS_SLIK_PDF_VAL = '<?= esc($berkasSlikPdfVal) ?>';
    window.BERKAS_SLIK_TXT_VAL = '<?= esc($berkasSlikTxtVal) ?>';
    window.daftarPermohonanSlikArray = <?= json_encode($daftarPermohonanSlik) ?>;
    window.CAN_EDIT_SLIK = <?= $canEditSlik ? 'true' : 'false' ?>;
    window.PETUGAS_SLIK_AKTIF = <?= $petugasSlikAktif ?>;
    window.USER_ROLE = '<?= esc(strtolower(session()->get('jabatan') ?? 'pengusul')) ?>';
    window.KREDIT_ID_VAL = '<?= $kreditIdVal ?>';
    window.TGL_TAHUN_VAL = '<?= date("Y", $tglTimestamp) ?>';
    window.NAMA_DEBITUR_VAL = '<?= esc($namaDebiturVal) ?>';
    window.NO_ID_VAL = '<?= esc($noIdVal) ?>';
    window.JENIS_DEBITUR_VAL = '<?= esc($kredit['jenis_debitur'] ?? 'Individu') ?>';
    window.BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('js/pengajuan-slik.js?v=' . time()) ?>"></script>
