<?php
$ijinSlikPengusul = (int)($pengaturan_sistem['ijin_pengusul_isi_slik'] ?? 1);
$currentUserRole = strtolower(session()->get('jabatan') ?? '');
$isPetugasSlikRole = ($currentUserRole === 'superadmin' || strpos($currentUserRole, 'slik') !== false);
$bisaIsiSlik = ($isPetugasSlikRole || $ijinSlikPengusul === 1);
?>
<div id="tab-6" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                            6
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">RIWAYAT FASILITAS KREDIT (SLIK OJK)</h3>
                            <p class="text-[11px] text-slate-500">Pencatatan fasilitas pinjaman aktif di perbankan atau lembaga keuangan lain</p>
                        </div>
                    </div>
                    <?php if ($bisaIsiSlik): ?>
                        <button type="button" onclick="tambahBarisSlik()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-bold transition border border-blue-200/60 shadow-2xs">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Fasilitas
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (!$isPetugasSlikRole && $ijinSlikPengusul === 1): ?>
                    <!-- Alert Fleksibel: Pengusul Diizinkan Mengisi Draf -->
                    <div class="mb-6 p-4 rounded-2xl bg-blue-50/80 border border-blue-200/80 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Pengisian Data SLIK Fleksibel (Opsional bagi Pengusul)</h4>
                            <p class="text-xs text-blue-800 mt-0.5 leading-relaxed">
                                Anda dapat mencantumkan fasilitas pinjaman aktif nasabah yang Anda ketahui. Data ini akan tercatat sebagai <strong>usulan draf</strong> kepada <strong>Petugas SLIK</strong> untuk ditinjau dan disahkan melalui tombol persetujuan di workspace SLIK. Anda juga dapat melewati langkah ini bila pengecekan diserahkan penuh kepada Petugas SLIK.
                            </p>
                        </div>
                    </div>
                <?php elseif (!$isPetugasSlikRole && $ijinSlikPengusul === 0): ?>
                    <?php 
                        $statusSlikSaatIni = $kredit['status_slik'] ?? 'Belum Terbit';
                        $isSlikRequested = ($statusSlikSaatIni === 'Menunggu Pengecekan' || $statusSlikSaatIni === 'Terbit');
                    ?>
                    <!-- Alert Pengisian SLIK Dinonaktifkan bagi Pengusul & Tombol Setuju SLIK -->
                    <div class="p-6 sm:p-7 rounded-2xl <?= $isSlikRequested ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' ?> mb-6 text-center transition" id="boxStatusSlik">
                        <?php if ($isSlikRequested): ?>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-sm font-extrabold text-emerald-900 uppercase tracking-wide">Persetujuan Verifikasi SLIK Telah Dikirim</h4>
                            <p class="text-xs text-emerald-800 mt-1.5 max-w-lg mx-auto leading-relaxed">
                                Permohonan pemeriksaan riwayat fasilitas pinjaman (SLIK OJK) telah disetujui dan <strong>resmi masuk ke tabel antrean kerja Petugas SLIK</strong>. Anda dapat langsung melanjutkan ke tahapan berikutnya.
                            </p>
                            <div class="mt-4 flex items-center justify-center gap-3">
                                <button type="button" onclick="goToTab(7)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                    <span>Lanjut ke Langkah 7 (Legal & Web Check)</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h4 class="text-sm font-extrabold text-amber-900 uppercase tracking-wide">Persetujuan Pemeriksaan SLIK OJK (Khusus Petugas SLIK)</h4>
                            <p class="text-xs text-amber-800 mt-1.5 max-w-lg mx-auto leading-relaxed">
                                Fitur pengisian mandiri dinonaktifkan. Silakan klik tombol <strong>Setuju di SLIK</strong> di bawah ini agar berkas debitur ini <strong>diteruskan dan masuk ke tabel antrean Petugas SLIK</strong> untuk dilakukan verifikasi fasilitas pinjaman nasabah secara resmi.
                            </p>
                            <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                                <button type="button" id="btnSetujuSlik" onclick="setujuiPengajuanSlik(event)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Setuju di SLIK (Kirim ke Petugas SLIK)</span>
                                </button>
                                <button type="button" onclick="goToTab(7)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">
                                    <span>Lewati ke Langkah 7</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                    <table class="w-full text-left border-collapse" id="tabelSlik">
                        <thead class="bg-slate-50/80 text-slate-700 font-bold text-[11px] uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-3 min-w-[170px]">Nama Debitur / Pemilik</th>
                                <th class="py-3.5 px-3 min-w-[150px]">Bank / Lembaga</th>
                                <th class="py-3.5 px-3 min-w-[130px]">Plafon (Rp)</th>
                                <th class="py-3.5 px-3 min-w-[140px]">Baki Debet / Outstanding</th>
                                <th class="py-3.5 px-3 min-w-[160px]">Kualitas / Kol</th>
                                <th class="py-3.5 px-3 min-w-[170px]">Keterangan / Tujuan</th>
                                <th class="py-3.5 px-2 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs" id="bodySlik">
                            <?php if (empty($slik)): ?>
                                <?php if (!$bisaIsiSlik): ?>
                                    <tr>
                                        <td colspan="7" class="p-6 text-center text-xs text-slate-400 italic bg-slate-50/50">
                                            Belum ada catatan fasilitas kredit yang diterbitkan oleh Petugas SLIK.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][nama_debitur]" placeholder="Debitur / Pasangan" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][plafon]" placeholder="0" 
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                                                   oninput="formatRupiah(this)">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][outstanding]" placeholder="0" 
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                                                   oninput="formatRupiah(this); hitungTotalSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <select name="slik[0][kelancaran]" 
                                                    class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                                <option value="Lancar">Kol 1 (Lancar)</option>
                                                <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                                                <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                                                <option value="Diragukan">Kol 4 (Diragukan)</option>
                                                <option value="Macet">Kol 5 (Macet)</option>
                                            </select>
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[0][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                                        </td>
                                        <td class="p-2.5 bg-white text-center">
                                            <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100" title="Hapus Baris">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php foreach ($slik as $idx => $s): ?>
                                    <tr>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][nama_debitur]" value="<?= esc($s['nama_debitur'] ?? '') ?>" placeholder="Debitur / Pasangan" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][bank_lembaga]" value="<?= esc($s['bank_lembaga'] ?? '') ?>" placeholder="Nama Bank / Leasing" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][plafon]" value="<?= number_format((float)($s['plafon'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>" 
                                                   oninput="formatRupiah(this)">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][outstanding]" value="<?= number_format((float)($s['outstanding'] ?? 0), 0, ',', '.') ?>" placeholder="0" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>" 
                                                   oninput="formatRupiah(this); hitungTotalSlik();">
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <?php if (!$bisaIsiSlik): ?>
                                                <input type="hidden" name="slik[<?= $idx ?>][kelancaran]" value="<?= esc($s['kelancaran'] ?? 'Lancar') ?>">
                                                <input type="text" readonly value="<?= esc($s['kelancaran'] ?? 'Lancar') ?>" 
                                                       class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-100 cursor-not-allowed shadow-2xs">
                                            <?php else: ?>
                                                <select name="slik[<?= $idx ?>][kelancaran]" 
                                                        class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                                    <?php $kl = $s['kelancaran'] ?? 'Lancar'; ?>
                                                    <option value="Lancar" <?= $kl === 'Lancar' ? 'selected' : '' ?>>Kol 1 (Lancar)</option>
                                                    <option value="DPK" <?= $kl === 'DPK' ? 'selected' : '' ?>>Kol 2 (DPK / Perhatian Khusus)</option>
                                                    <option value="Kurang Lancar" <?= $kl === 'Kurang Lancar' ? 'selected' : '' ?>>Kol 3 (Kurang Lancar)</option>
                                                    <option value="Diragukan" <?= $kl === 'Diragukan' ? 'selected' : '' ?>>Kol 4 (Diragukan)</option>
                                                    <option value="Macet" <?= $kl === 'Macet' ? 'selected' : '' ?>>Kol 5 (Macet)</option>
                                                </select>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-2.5 bg-white">
                                            <input type="text" name="slik[<?= $idx ?>][keterangan]" value="<?= esc($s['keterangan'] ?? '') ?>" placeholder="Modal Kerja / KPR / dll" 
                                                   <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>">
                                        </td>
                                        <td class="p-2.5 bg-white text-center">
                                            <?php if ($bisaIsiSlik): ?>
                                                <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100 cursor-pointer" title="Hapus Baris">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            <?php else: ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-400 text-xs">
                                                    🔒
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Total Baki Debet Summary Box -->
                <div class="mt-4 p-4 bg-blue-50/60 border border-blue-200/60 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Total Baki Debet Terhutang (SLIK)</span>
                    </div>
                    <div class="text-sm sm:text-base font-extrabold text-blue-700" id="totalOutstandingSlik">
                        Rp 0
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Ringkasan & Catatan Historis SLIK OJK
                        </label>
                        <!-- Dropdown Bantuan Narasi -->
                        <div class="relative inline-block text-left dropdown-narasi-container">
                            <button type="button" onclick="toggleDropdownNarasi('menu_narasi_slik')" class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold rounded-lg border border-blue-200 transition shadow-2xs cursor-pointer">
                                <span>ℹ️ Bantuan Narasi</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div id="menu_narasi_slik" class="dropdown-narasi-menu hidden absolute right-0 z-30 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 text-xs text-slate-700">
                                <div class="px-3 py-1 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">Pilih Contoh Narasi:</div>
                                <button type="button" onclick="terapkanNarasi('ringkasan_slik', 'ringkasan_slik', 'positif'); toggleDropdownNarasi('menu_narasi_slik')" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-emerald-800 font-semibold flex items-center gap-2 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span> 🟢 Contoh Positif (Kolektibilitas 1)
                                </button>
                                <button type="button" onclick="terapkanNarasi('ringkasan_slik', 'ringkasan_slik', 'netral'); toggleDropdownNarasi('menu_narasi_slik')" class="w-full text-left px-3 py-2 hover:bg-amber-50 text-amber-800 font-semibold flex items-center gap-2 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span> 🟡 Contoh Netral (Pernah DPK)
                                </button>
                                <button type="button" onclick="terapkanNarasi('ringkasan_slik', 'ringkasan_slik', 'negatif'); toggleDropdownNarasi('menu_narasi_slik')" class="w-full text-left px-3 py-2 hover:bg-rose-50 text-rose-800 font-semibold flex items-center gap-2 cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span> 🔴 Contoh Negatif (Ada Tunggakan)
                                </button>
                            </div>
                        </div>
                    </div>
                    <textarea id="ringkasan_slik" name="ringkasan_slik" rows="3" 
                              <?= !$bisaIsiSlik ? 'readonly' : '' ?>
                              placeholder="Tuliskan kesimpulan profil karakter kredit nasabah, riwayat tunggakan (jika ada), serta rencana take over / pelunasan..."
                              class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs resize-y <?= !$bisaIsiSlik ? 'bg-slate-100 cursor-not-allowed' : '' ?>"><?= esc($kredit['ringkasan_slik'] ?? '') ?></textarea>
                </div>
            </div>
