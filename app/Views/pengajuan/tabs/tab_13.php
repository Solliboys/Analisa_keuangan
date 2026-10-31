<div id="tab-13" class="tab-panel hidden" style="display: none !important;">
                <!-- Disabled Alert Banner -->
                <div class="p-4 mb-6 rounded-2xl bg-amber-50 border border-amber-200/80 flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wide">Tahap 13 Dinonaktifkan Sementara</h4>
                        <p class="text-xs text-amber-700">Proses pengusul kredit cukup diselesaikan sampai tahap 12 (Disbursement). Tahap 13 (Approval Komite) dinonaktifkan sementara dan tidak dapat diakses pengusul.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">13. Persetujuan & Lembar Opini Komite Pembiayaan</h2>
                        <p class="text-sm text-slate-500">Pencatatan persetujuan berjenjang komite kredit, rekomendasi limit, dan syarat pencairan.</p>
                    </div>
                    <button type="button" onclick="tambahBarisKomite()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        + Tambah Approver Komite
                    </button>
                </div>

                <div class="space-y-4 mb-8" id="containerKomite">
                    <?php if (empty($komite)): ?>
                        <!-- Default 2 Approvers -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                            <button type="button" onclick="hapusBarisTabel(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                                    <select name="komite[0][role_approver]" disabled class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                        <option value="Reviewer / Analis Senior">Reviewer / Analis Senior</option>
                                        <option value="Komite 1">Komite 1 (Branch Manager)</option>
                                        <option value="Komite 2">Komite 2 (Head of Credit)</option>
                                        <option value="Direksi">Direksi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                                    <input type="text" name="komite[0][nama_approver]" disabled placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                                    <select name="komite[0][status_approval]" disabled class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 font-semibold cursor-not-allowed">
                                        <option value="Pending" selected>Pending / Menunggu Review</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Disetujui Bersyarat">Disetujui Bersyarat</option>
                                        <option value="Ditolak">Ditolak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                                    <input type="date" name="komite[0][tanggal_approval]" disabled value="<?= date('Y-m-d') ?>" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                </div>
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                                    <textarea name="komite[0][catatan_approval]" disabled rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed"></textarea>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($komite as $idx => $k): ?>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                                        <select name="komite[<?= $idx ?>][role_approver]" disabled class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                            <?php $ra = $k['role_approver'] ?? 'Komite 1'; ?>
                                            <option value="Reviewer / Analis Senior" <?= $ra === 'Reviewer / Analis Senior' ? 'selected' : '' ?>>Reviewer / Analis Senior</option>
                                            <option value="Komite 1" <?= $ra === 'Komite 1' ? 'selected' : '' ?>>Komite 1 (Branch Manager)</option>
                                            <option value="Komite 2" <?= $ra === 'Komite 2' ? 'selected' : '' ?>>Komite 2 (Head of Credit)</option>
                                            <option value="Direksi" <?= $ra === 'Direksi' ? 'selected' : '' ?>>Direksi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                                        <input type="text" name="komite[<?= $idx ?>][nama_approver]" disabled value="<?= esc($k['nama_approver'] ?? '') ?>" placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                                        <select name="komite[<?= $idx ?>][status_approval]" disabled class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 font-semibold cursor-not-allowed">
                                            <?php $sa = $k['status_approval'] ?? 'Pending'; ?>
                                            <option value="Disetujui" <?= $sa === 'Disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                            <option value="Disetujui Bersyarat" <?= $sa === 'Disetujui Bersyarat' ? 'selected' : '' ?>>Disetujui Bersyarat</option>
                                            <option value="Pending" <?= $sa === 'Pending' ? 'selected' : '' ?>>Pending / Tinjau Kembali</option>
                                            <option value="Ditolak" <?= $sa === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                                        <input type="date" name="komite[<?= $idx ?>][tanggal_approval]" disabled value="<?= esc($k['tanggal_approval'] ?? date('Y-m-d')) ?>" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed">
                                    </div>
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                                        <textarea name="komite[<?= $idx ?>][catatan_approval]" disabled rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-slate-100 cursor-not-allowed"><?= esc($k['catatan_approval'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Status Final Pengajuan (Khusus Komite - Disabled di Tab 13) -->
                <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Status Pengajuan Akhir Dokumen CAM</label>
                        <p class="text-xs text-slate-500">Status final komite pembiayaan (dinonaktifkan sementara di tahap pengusul).</p>
                    </div>
                    <div class="w-full sm:w-64">
                        <select id="status_pengajuan_komite_disabled" disabled class="w-full text-xs font-bold border border-slate-200 rounded-xl p-2.5 bg-slate-100 cursor-not-allowed">
                            <?php $stp = $kredit['status_pengajuan'] ?? 'Draft'; ?>
                            <option value="Draft" <?= $stp === 'Draft' ? 'selected' : '' ?>>Draft (Belum Selesai)</option>
                            <option value="Diajukan" <?= $stp === 'Diajukan' ? 'selected' : '' ?>>Diajukan ke Komite</option>
                            <option value="Disetujui" <?= $stp === 'Disetujui' ? 'selected' : '' ?>>Disetujui (Approved)</option>
                            <option value="Ditolak" <?= $stp === 'Ditolak' ? 'selected' : '' ?>>Ditolak (Rejected)</option>
                        </select>
                    </div>
                </div>
            </div>
