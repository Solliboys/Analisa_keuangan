<div id="tab-12" class="tab-panel bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs shadow-xs">
                12
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-bold text-slate-800 tracking-wider uppercase">REPAYMENT CAPACITY & DEBT SERVICE COVERAGE (DSC)</h3>
                <p class="text-[11px] text-slate-500">Pengujian kemampuan bayar debitur terhadap angsuran pinjaman (Standar aman DSC &ge; 1.30x)</p>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 shadow-2xs self-start sm:self-auto">
            Standar DSC &ge; 1.30x
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <!-- Angsuran Balloon Payment -->
        <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Angsuran Balloon Payment</label>
                <input type="text" name="angsuran_balloon" id="angsuran_balloon" 
                       value="<?= number_format((float)($keuangan['angsuran_balloon'] ?? 0), 0, ',', '.') ?>" 
                       class="w-full text-lg font-black text-blue-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
            </div>
            <div class="text-[11px] text-slate-400 font-medium mt-2 pt-2 border-t border-blue-100/60">Pembayaran bunga bulanan</div>
        </div>

        <!-- Angsuran Installment -->
        <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-200/80 flex flex-col justify-between shadow-2xs">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Angsuran Installment</label>
                <input type="text" name="angsuran_installment" id="angsuran_installment" 
                       value="<?= number_format((float)($keuangan['angsuran_installment'] ?? 0), 0, ',', '.') ?>" 
                       class="w-full text-lg font-black text-blue-700 bg-transparent border-0 p-0 focus:ring-0 cursor-default" readonly>
            </div>
            <div class="text-[11px] text-slate-400 font-medium mt-2 pt-2 border-t border-blue-100/60">Pokok + Bunga bulanan</div>
        </div>

        <!-- Angsuran yang Dipilih Sebagai Acuan -->
        <div class="p-5 bg-blue-600 text-white rounded-2xl shadow-xs flex flex-col justify-between">
            <div>
                <label class="block text-[11px] font-bold text-blue-100 uppercase tracking-wider mb-2">Angsuran yang Dipilih (Acuan)</label>
                <input type="text" name="angsuran_dipilih" id="angsuran_dipilih" 
                       value="<?= number_format((float)($keuangan['angsuran_dipilih'] ?? 0), 0, ',', '.') ?>" 
                       class="w-full text-base font-black text-blue-900 bg-white border border-white/40 rounded-xl px-3.5 py-2 rupiah-input focus:ring-2 focus:ring-blue-300 transition shadow-2xs h-[42px]"
                       oninput="formatRupiah(this); hitungRasioDSC(); hitungDisbursement();">
            </div>
            <div class="text-[11px] text-blue-100 font-medium mt-2 pt-2 border-t border-blue-500/50">Acuan rasio DSC & Hold Angsuran</div>
        </div>
    </div>

    <!-- Hasil Perhitungan Rasio DSC -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl border border-blue-200/80 bg-white shadow-2xs hover:border-blue-300 transition">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rasio DSC (Basis Rekening Koran)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Data Riil Bank</span>
            </div>
            <div class="flex items-baseline gap-3 my-2">
                <span id="labelRasioDSCRK" class="text-4xl font-black text-slate-900 tracking-tight">0.00x</span>
                <span id="badgeStatusDSCRK" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100 font-medium">Rumus: Laba Bersih Rekening Koran / Angsuran Acuan</div>
        </div>

        <div class="p-6 rounded-2xl border border-blue-200/80 bg-white shadow-2xs hover:border-blue-300 transition">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Rasio DSC (Basis Wawancara)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Data Wawancara</span>
            </div>
            <div class="flex items-baseline gap-3 my-2">
                <span id="labelRasioDSCIV" class="text-4xl font-black text-slate-900 tracking-tight">0.00x</span>
                <span id="badgeStatusDSCIV" class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Analisa</span>
            </div>
            <div class="text-[11px] text-slate-400 mt-3 pt-3 border-t border-slate-100 font-medium">Rumus: Laba Bersih Wawancara / Angsuran Acuan</div>
        </div>
    </div>

    <!-- Hidden input status_pengajuan untuk menjaga konsistensi data -->
    <input type="hidden" name="status_pengajuan" id="status_pengajuan" value="<?= esc($kredit['status_pengajuan'] ?? 'Draft') ?>">
</div>
