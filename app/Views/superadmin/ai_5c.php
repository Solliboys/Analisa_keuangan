<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Konfigurasi Parameter Analisis 5C | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <!-- SweetAlert2 CDN -->
    <link rel="stylesheet" href="<?= base_url('libs/sweetalert2/sweetalert2.min.css') ?>">
    <script src="<?= base_url('libs/sweetalert2/sweetalert2.all.min.js') ?>"></script>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('img/logo-oap.png?v=2') ?>">
    <link rel="stylesheet" href="<?= base_url('css/otw-portal.css?v=' . (@filemtime(FCPATH . 'css/otw-portal.css') ?: 1)) ?>">
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'ai_5c',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Offset by sidebar width md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen w-full">

        <!-- Mobile Header Bar Responsif -->
        <header class="md:hidden bg-white border-b border-slate-200 px-3.5 sm:px-4 py-2.5 sm:py-3 flex items-center justify-between gap-4 sm:gap-6 sticky top-0 z-20 w-full min-w-0">
            <div class="flex items-center gap-2.5 min-w-0 flex-1 mr-2 sm:mr-3">
                <button type="button" onclick="toggleSidebar()" class="p-1.5 sm:p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 shrink-0 cursor-pointer" aria-label="Toggle Sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2 min-w-0 flex-1 pr-1.5 sm:pr-2">
                    <?php 
                        $cfgHeader = !empty($pengaturan_sistem) ? $pengaturan_sistem : (\Config\Database::connect()->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? []);
                        $appHeaderLogo = !empty($cfgHeader['logo_aplikasi']) ? $cfgHeader['logo_aplikasi'] : '';
                        $appHeaderName = !empty($cfgHeader['nama_aplikasi']) ? $cfgHeader['nama_aplikasi'] : 'OTW Kredit';
                    ?>
                    <?php if (!empty($appHeaderLogo)): ?>
                        <img src="<?= base_url($appHeaderLogo) ?>" class="w-6 h-6 object-contain rounded shrink-0" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-xs sm:text-sm truncate whitespace-nowrap min-w-0 block"><?= esc($appHeaderName) ?></span>
                </div>
            </div>
            <div class="flex items-center shrink-0">
                <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200 hover:bg-rose-100 transition shrink-0 whitespace-nowrap">
                    Logout
                </a>
            </div>
        </header>

        <!-- Desktop Topbar Full Width -->
        <div class="bg-white border-b border-slate-200/80 px-6 lg:px-8 py-3.5 hidden md:flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-2.5">
                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Super Administrator</span>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">AI 5C Analysis</span>
            </div>
        </div>

        <!-- Main Content Full Width -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">
            
            <!-- Page Header -->
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-6">
                <div>
                    <!-- Title and Status Badges -->
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl sm:text-[26px] font-bold text-slate-900 tracking-tight">
                            Konfigurasi Parameter Analisis 5C
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active &amp; Verified
                        </span>
                    </div>
                    <p class="text-slate-500 text-sm mt-1.5 max-w-3xl leading-relaxed">
                        Atur bobot risiko, kriteria kelayakan, dan system instruction pipeline penilaian kredit otomatis untuk verifikasi agunan BPR.
                    </p>
                </div>

                <!-- Right Metadata -->
                <div class="flex flex-col sm:items-end gap-1.5 shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Total Bobot 5C:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            100% (Balanced)
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Terakhir diperbarui: Hari ini, 14:20 oleh Risk Team</span>
                    </div>
                </div>
            </div>

<!-- Tab Navigation (Pill Tabs dengan Nomor 1 - 6) -->
            <div class="bg-slate-100/80 p-1.5 rounded-2xl mb-6 overflow-x-auto">
                <div class="flex items-center gap-1.5 min-w-max">
                    
                    <!-- Tab 1: Character -->
                    <button type="button" onclick="switchPillar('character')" id="tab-character" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>1. Character</span>
                        <span class="text-slate-400 font-normal">(25%)</span>
                    </button>

                    <!-- Tab 2: Capacity -->
                    <button type="button" onclick="switchPillar('capacity')" id="tab-capacity" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>2. Capacity</span>
                        <span class="text-slate-400 font-normal">(25%)</span>
                    </button>

                    <!-- Tab 3: Capital -->
                    <button type="button" onclick="switchPillar('capital')" id="tab-capital" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>3. Capital</span>
                        <span class="text-slate-400 font-normal">(15%)</span>
                    </button>

                    <!-- Tab 4: Collateral -->
                    <button type="button" onclick="switchPillar('collateral')" id="tab-collateral" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>4. Collateral</span>
                        <span class="text-slate-400 font-normal">(20%)</span>
                    </button>

                    <!-- Tab 5: Condition -->
                    <button type="button" onclick="switchPillar('condition')" id="tab-condition" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>5. Condition</span>
                        <span class="text-slate-400 font-normal">(15%)</span>
                    </button>

                    <!-- Tab 6: Reviewer -->
                    <button type="button" onclick="switchPillar('reviewer')" id="tab-reviewer" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white/60 transition">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[10px]">✓</span>
                        <span>6. Reviewer</span>
                        <span class="text-slate-400 font-normal">(100%)</span>
                    </button>

                </div>
            </div>

            <!-- Card 2: Parameter Metrik Risiko Pilar (Berdasarkan Tab Aktif) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 mb-6 shadow-2xs">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800" id="metricTitle">
                            PARAMETER METRIK RISIKO PILAR AGUNAN
                        </h2>
                        <span class="text-xs text-slate-400 font-medium">(Regulasi OJK &amp; Kebijakan Risiko Pembiayaan)</span>
                    </div>
                    <a href="javascript:void(0)" onclick="alert('Panduan Scoring sesuai kebijakan manajemen risiko pembiayaan dan prinsip kehati-hatian OJK.')" class="text-xs font-semibold text-blue-700 hover:text-blue-800 flex items-center gap-1 transition">
                        <span>Panduan Scoring</span>
                        <span>&gt;</span>
                    </a>
                </div>

                <!-- 2 Metric Columns (Bobot & Threshold) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- Col 1: Bobot Portofolio Pilar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Bobot Portofolio Pilar</label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">Max 40%</span>
                        </div>
                        <div class="relative">
                            <input type="number" id="inputBobot" value="20" class="w-full pl-3.5 pr-8 py-2.5 text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Alokasi porsi skor terhadap total 5C</p>
                    </div>

                    <!-- Col 2: Threshold Minimum -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Threshold Minimum</label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Toleransi Sedang</span>
                        </div>
                        <div class="relative">
                            <input type="number" id="inputThreshold" value="75" class="w-full pl-3.5 pr-14 py-2.5 text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400">/ 100</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Batas minimum kelayakan skor pilar</p>
                    </div>

                </div>

            </div>

            <!-- Card 2: Panduan Penilaian AI (Mode Bahasa Sehari-hari untuk Admin) -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden mb-8">

                <!-- Header Ramah Admin (Tanpa Jargon Kode) -->
                <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 space-y-3">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-950" id="directiveTitle">
                                Panduan Penilaian AI: Pilar Agunan (Collateral)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Tulis arahan dengan bahasa sehari-hari seperti menjelaskan ke analis kredit. Data debitur (plafon, agunan, omzet, SLIK) otomatis dibaca sistem tanpa perlu sintaks.
                            </p>
                        </div>

                        <!-- Preset Gaya Penilaian (1-Klik) -->
                        <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                            <span class="text-[11px] font-medium text-slate-500 mr-1">Gaya Penilaian:</span>
                            <button type="button" onclick="terapkanPresetGaya('ketat')" class="px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 text-slate-700 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                                Ketat (Konservatif)
                            </button>
                            <button type="button" onclick="terapkanPresetGaya('standar')" class="px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 text-slate-700 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                                Seimbang (Standar)
                            </button>
                            <button type="button" onclick="terapkanPresetGaya('fleksibel')" class="px-2.5 py-1 rounded-md bg-white hover:bg-blue-50 hover:text-blue-700 hover:border-blue-300 text-slate-700 text-[11px] font-semibold border border-slate-200 transition cursor-pointer">
                                Fleksibel
                            </button>
                        </div>
                    </div>

                    <!-- Chip Tambah Poin Aturan Cepat (Dinamis per Pilar) -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-200/80">
                        <span class="text-[11px] font-medium text-slate-500 mr-1">Tambah Poin Aturan:</span>
                        <div id="quickRuleChips" class="flex flex-wrap items-center gap-1.5">
                            <!-- Diisi dinamis oleh JS sesuai pilar aktif -->
                        </div>
                        <button type="button" onclick="formatPrompt()" class="ml-auto px-2.5 py-1 rounded-md bg-white hover:bg-slate-100 text-slate-600 font-medium text-[11px] border border-slate-200 transition cursor-pointer">
                            Rapikan Teks
                        </button>
                    </div>
                </div>

                <!-- Kotak Aturan Bahasa Normal -->
                <div class="p-4 sm:p-5 bg-white">
                    <textarea id="promptEditor" rows="10" placeholder="Contoh: Pastikan nilai likuidasi agunan minimal 100% dari plafon pinjaman, sertifikat jelas atas nama debitur, dan beri catatan risiko bila agunan sulit dijual." class="w-full text-[13px] text-slate-800 leading-relaxed outline-none resize-y border border-slate-200 rounded-md p-3.5 bg-white focus:border-blue-700 focus:ring-2 focus:ring-blue-700/10" spellcheck="false"></textarea>
                </div>

                <!-- Footer Status Ramah Admin -->
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2 text-slate-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                        <span>Aturan siap dipakai — data debitur otomatis disambungkan oleh sistem saat analisis berjalan.</span>
                    </div>
                    <span class="text-slate-400 text-[11px] tabular-nums shrink-0" id="tokenCount">
                        0 kata
                    </span>
                </div>

            </div>

            <!-- Bottom Action Buttons Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                
                <!-- Left Buttons -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <button type="button" onclick="resetDefaultPrompt()" class="flex-1 sm:flex-none px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors shadow-2xs">
                        Kembalikan ke Default
                    </button>
                    <button type="button" onclick="openSandboxModal()" class="flex-1 sm:flex-none px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold inline-flex items-center justify-center gap-1.5 transition-colors shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <span>Uji Coba Sandbox</span>
                    </button>
                </div>

                <!-- Right Buttons -->
                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <button type="button" onclick="saveDraft()" class="flex-1 sm:flex-none px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition shadow-2xs">
                        Simpan Draf
                    </button>
                    <button type="button" onclick="applyAndRunAnalysis()" class="flex-1 sm:flex-none px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold inline-flex items-center justify-center gap-2 transition shadow-xs shadow-blue-500/20">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                            <path d="M5 3l14 9-14 9V3z"/>
                        </svg>
                        <span id="btnRunText">Terapkan &amp; Jalankan Analisis (Collateral)</span>
                    </button>
                </div>

            </div>

        </main>
    </div>

    <!-- MODAL: Uji Coba Sandbox -->
    <div id="sandboxModal" onclick="if(event.target === this) closeSandboxModal()" class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-lg shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-slate-950 text-sm">Uji Coba Sandbox Evaluasi 5C</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Simulasi cepat perhitungan LTV &amp; estimasi skor agunan.</p>
                </div>
                <button type="button" onclick="closeSandboxModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-md hover:bg-slate-100 cursor-pointer" aria-label="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 space-y-4 text-xs">
                <div>
                    <label for="sb_agunan" class="block font-medium text-slate-700 mb-1.5">Deskripsi Agunan (SHM / BPKB)</label>
                    <input type="text" id="sb_agunan" value="SHM No. 412 Luas 240m2 An. Debitur" class="w-full h-9 px-3 border border-slate-300 rounded-md text-xs text-slate-900 focus:border-blue-700 focus:outline-none" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="sb_pasar" class="block font-medium text-slate-700 mb-1.5">Taksasi Pasar (Rp)</label>
                        <input type="text" id="sb_pasar" value="380.000.000" oninput="hitungSandboxAi5c()" class="w-full h-9 px-3 border border-slate-300 rounded-md text-xs font-semibold text-slate-900 tabular-nums focus:border-blue-700 focus:outline-none" />
                    </div>
                    <div>
                        <label for="sb_plafon" class="block font-medium text-slate-700 mb-1.5">Plafon Diajukan (Rp)</label>
                        <input type="text" id="sb_plafon" value="250.000.000" oninput="hitungSandboxAi5c()" class="w-full h-9 px-3 border border-slate-300 rounded-md text-xs font-semibold text-slate-900 tabular-nums focus:border-blue-700 focus:outline-none" />
                    </div>
                </div>

                <div class="border border-slate-200 rounded-md divide-y divide-slate-200 bg-slate-50/70 tabular-nums">
                    <div class="px-4 py-2.5 flex items-center justify-between gap-2">
                        <span class="text-slate-600">Rasio LTV (Plafon / Pasar)</span>
                        <span id="sb_out_ltv" class="font-bold text-emerald-700">65,8% (Aman)</span>
                    </div>
                    <div class="px-4 py-2.5 flex items-center justify-between gap-2">
                        <span class="text-slate-600">Estimasi Skor Kelayakan Agunan</span>
                        <span id="sb_out_skor" class="font-bold text-blue-700">88 / 100 (Sangat Layak)</span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeSandboxModal()" class="px-4 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold rounded-md text-xs transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    <script>
        function hitungSandboxAi5c() {
            const pNum = v => parseFloat(String(v || '0').replace(/[^0-9]/g, '')) || 0;
            const pasar = pNum(document.getElementById('sb_pasar')?.value);
            const plafon = pNum(document.getElementById('sb_plafon')?.value);
            const ltv = pasar > 0 ? (plafon / pasar) * 100 : 0;
            const outLtv = document.getElementById('sb_out_ltv');
            const outSkor = document.getElementById('sb_out_skor');
            if (!outLtv || !outSkor) return;

            let ltvLabel = 'Aman';
            let ltvCls = 'font-bold text-emerald-700';
            let skor = 88;
            let skorLabel = 'Sangat Layak';
            if (ltv > 85) {
                ltvLabel = 'Risiko Tinggi';
                ltvCls = 'font-bold text-rose-700';
                skor = 54;
                skorLabel = 'Perlu Tambahan Agunan';
            } else if (ltv > 70) {
                ltvLabel = 'Moderat';
                ltvCls = 'font-bold text-amber-700';
                skor = 74;
                skorLabel = 'Layak Bersyarat';
            }
            outLtv.className = ltvCls;
            outLtv.textContent = `${ltv.toFixed(1).replace('.', ',')}% (${ltvLabel})`;
            outSkor.textContent = `${skor} / 100 (${skorLabel})`;
        }
    </script>

    <!-- Client-side Interactive Scripts -->
    <script>
        const defaultPrompts = {
            character: `Anda adalah Analis Kredit Senior spesialis evaluasi PILAR CHARACTER (Karakter & Integritas Debitur). Lakukan penilaian menyeluruh terhadap itikad baik (willingness to pay) dan rekam jejak kepatuhan debitur.

Fokus & Aspek Evaluasi Utama:
1. Rekam Jejak SLIK OJK / iDeb:
   - Analisa status kolektibilitas historis maupun berjalan (Kolektibilitas 1 Lancar, Kol 2 DPK, Kol 3 Kurang Lancar, Kol 4 Diragukan, Kol 5 Macet).
   - Telusuri riwayat tunggakan (Days Past Due / DPD), frekuensi restrukturisasi kredit, baki debet pinjaman aktif di bank/lembaga lain, dan kedisiplinan pelunasan.
2. Integritas Hukum & Reputasi Publik (Web Check):
   - Verifikasi kebersihan nama debitur dan pengurus badan usaha pada Sistem Informasi Penelusuran Perkara Pengadilan (SIPP) dari perkara perdata wanprestasi, sengketa bisnis, dan perkara kepailitan/PKPU.
   - Pastikan debitur bebas dari daftar terduga terorisme (DTTOT PPATK), daftar hitam penarik cek kosong Bank Indonesia, serta lelang eksekusi hak tanggungan.
3. Kepatuhan & Kejujuran:
   - Evaluasi kesesuaian dokumen legalitas identitas (KTP, NPWP, Akta/NIB) dan keterbukaan informasi kondisi keuangan usaha.

Standar & Tolok Ukur Penilaian:
- Skor 85 - 100 (Sangat Baik): Kolektibilitas 1 murni tanpa catatan tunggakan, tidak ada perkara hukum, reputasi usaha bersih dan kredibel.
- Skor 65 - 84 (Cukup / Perlu Perhatian): Pernah Kol 2 karena faktor teknis atau administratif namun sudah lunas, tidak ada sengketa hukum material.
- Skor < 60 (Tinggi Risiko): Tercatat Kol 3, 4, 5 atau terlibat perkara gugatan wanprestasi/pailit aktif di pengadilan (Wajib ditolak atau mitigasi ketat).`,

            capacity: `Anda adalah Analis Keuangan & Cashflow Underwriter spesialis evaluasi PILAR CAPACITY (Kemampuan Membayar). Evaluasi kapasitas arus kas debitur untuk memastikan kelancaran pembayaran kewajiban pokok dan bunga kredit (ability to pay).

Fokus & Aspek Evaluasi Utama:
1. Validasi Omzet Riil & Arus Kas Masuk:
   - Uji perputaran omzet bulanan berdasarkan mutasi kredit rekening koran debitur, hilangkan transaksi anomali atau pemindahbukuan internal (inter-account transfer).
   - Perhitungkan struktur Harga Pokok Penjualan (HPP), biaya operasional (OPEX), dan biaya hidup/pengeluaran rutin pemohon.
2. Surplus Arus Kas & Kemampuan Angsuran:
   - Hitung Laba Bersih Operasional / Surplus Kas Bulanan riil yang tersedia untuk melayani utang (Free Cash Flow for Debt Service).
   - Debt Service Coverage (DSC) / DSCR: Rasio Laba Bersih terhadap Angsuran Bulanan Kredit Usulan. Standar kehati-hatian lembaga pembiayaan mensyaratkan DSC minimal 1.30x (di atas 1.50x dikategorikan sehat & tangguh). Jika DSC < 1.00x, arus kas dinilai defisit.
   - Debt Service Ratio (DSR): Rasio total angsuran terhadap pendapatan kotor maksimal 35% - 40%.
3. Kesesuaian Skema & Beban Bunga:
   - Skema Installment (Pokok + Bunga bulanan): Membutuhkan stabilitas arus kas masuk setiap bulan.
   - Skema Balloon Payment (Bunga bulanan, Pokok di akhir tenor): Debitur wajib memiliki perputaran piutang/termin proyek atau cadangan dana likuid yang terbukti untuk melunasi pokok saat jatuh tempo.

Standar & Tolok Ukur Penilaian:
- Skor 85 - 100 (Kapasitas Prima): DSC >= 1.50x, arus kas rekening koran aktif dan seimbang dengan omzet pengakuan, surplus kas melimpah.
- Skor 70 - 84 (Kapasitas Memadai): DSC 1.20x - 1.49x, surplus kas positif namun memerlukan penyesuaian plafon pinjaman agar beban angsuran lebih ringan.
- Skor < 65 (Kapasitas Rawan): DSC < 1.10x atau arus kas rekening koran pasif/fluktuatif tajam tanpa cadangan likuiditas (Rekomendasi Tolak atau turun plafon drastis).`,

            capital: `Anda adalah Analis Struktur Modal & Solvabilitas spesialis evaluasi PILAR CAPITAL (Permodalan & Posisi Ekuitas). Evaluasi kekuatan modal sendiri debitur untuk menilai ketahanan menghadapi risiko usaha dan komitmen finansial pemohon.

Fokus & Aspek Evaluasi Utama:
1. Kekuatan Modal Sendiri (Net Worth / Ekuitas):
   - Hitung nilai Harta Bersih Bebas Jaminan (Kekayaan Bersih) untuk debitur perorangan, atau Total Ekuitas & Modal Disetor untuk debitur badan usaha.
   - Pastikan pemilik usaha memiliki modal sendiri yang cukup (skin in the game) sehingga risiko bisnis tidak dialihkan sepenuhnya kepada lembaga pembiayaan.
2. Rasio Solvabilitas & Leverage Keuangan:
   - Debt to Asset Ratio (DAR): Rasio Total Liabilitas/Utang terhadap Total Aset. Standar pembiayaan yang sehat mensyaratkan DAR maksimal 50% - 60%.
   - Debt to Equity Ratio (DER): Rasio Total Utang terhadap Modal Sendiri idealnya tidak melampaui 1.50x - 2.00x.
3. Penyangga Likuiditas (Financial Buffer):
   - Evaluasi ketersediaan aset lancar (tabungan, kas, piutang lancar) yang dapat segera dicairkan untuk menopang kewajiban usaha saat terjadi penurunan omzet mendadak.

Standar & Tolok Ukur Penilaian:
- Skor 85 - 100 (Modal Sangat Kuat): Ekuitas mendominasi aset, DAR < 40%, kekayaan bersih bebas agunan jauh melampaui plafon kredit.
- Skor 70 - 84 (Modal Cukup): DAR berkisar antara 40% - 65%, struktur utang seimbang dengan modal sendiri, usaha memiliki solvabilitas memadai.
- Skor < 60 (Over-leverage): DAR > 70%, modal sendiri minim atau negatif, debitur terlalu bergantung pada utang luar (Risiko insolvensi tinggi).`,

            collateral: `Anda adalah Penilai Agunan (Appraisal Officer) & Legal Underwriter spesialis evaluasi PILAR COLLATERAL (Agunan & Jaminan Pembiayaan). Nilai kekuatan agunan sebagai jalur penyelamatan kedua (second way out) pelunasan fasilitas kredit.

Fokus & Aspek Evaluasi Utama:
1. Aspek Legalitas & Marketabilitas Agunan:
   - Keabsahan yuridis dokumen kepemilikan: Sertifikat Hak Milik (SHM) / Hak Guna Bangunan (SHGB) atas nama debitur/keluarga inti, atau BPKB Kendaraan (wajib faktur & fisik terverifikasi), atau Bilyet Deposito perbankan.
   - Kepastian bahwa agunan tidak sedang dalam sengketa, bebas sita jaminan, memiliki akses jalan yang layak, dan dapat diikat secara sempurna melalui Hak Tanggungan (HT) atau Fidusia Notariil.
2. Penilaian Nilai Pasar & Nilai Likuidasi:
   - Taksasi Nilai Pasar Wajar (Market Value / MV) berdasarkan harga transaksi riil di lokasi agunan.
   - Taksasi Nilai Likuidasi Wajar (Liquidation Value / LV), umumnya diperhitungkan 60% - 70% dari nilai pasar untuk skenario penjualan lelang cepat.
3. Rasio Penutupan Jaminan (Coverage Ratio) & LTV:
   - Coverage Nilai Pasar (Coverage MV): Standar kehati-hatian lembaga pembiayaan minimal 120% - 130% dari plafon pengajuan (di atas 150% sangat aman).
   - Coverage Nilai Likuidasi (Coverage LV): Standar lembaga pembiayaan minimal 100% dari plafon pengajuan agar pokok kredit tetap tertutup utuh saat lelang eksekusi.
   - Loan to Value (LTV): Plafon kredit maksimal 70% - 80% dari Nilai Pasar Agunan.

Standar & Tolok Ukur Penilaian:
- Skor 85 - 100 (Agunan Prima): Coverage MV >= 130%, Coverage LV >= 100%, LTV <= 70%, lokasi sangat likuid, sertifikat SHM on hand.
- Skor 70 - 84 (Agunan Cukup): Coverage MV 100% - 129%, agunan marketable namun perlu penyesuaian plafon pinjaman agar Coverage LV mencapai 100%.
- Skor < 60 (Agunan Kurang): Coverage LV < 90%, agunan sulit dipindahtangankan, atau terdapat cacat yuridis pada bukti kepemilikan (Wajib tolak atau minta agunan tambahan).`,

            condition: `Anda adalah Penilai Risiko Ekonomi & Makro Sektoral spesialis evaluasi PILAR CONDITION (Kondisi Makro & Prospek Sektor Usaha). Evaluasi ketahanan sektor bisnis pemohon terhadap pengaruh lingkungan eksternal dan tren ekonomi.

Fokus & Aspek Evaluasi Utama:
1. Karakteristik Sektor Usaha & Kebutuhan Pasar:
   - Evaluasi apakah sektor usaha pemohon tergolong kebutuhan primer/defensif (pangan, kebutuhan harian, logistik) yang stabil atau sektor siklikal/konsumtif yang rentan terhadap inflasi, kenaikan suku bunga, dan penurunan daya beli.
   - Tinjau tingkat persaingan bisnis di wilayah operasional debitur dan ketergantungan pada pemasok atau pelanggan tertentu.
2. Pengalaman & Durasi Beroperasi Usaha:
   - Usaha yang telah beroperasi stabil lebih dari 2-3 tahun memiliki ketahanan siklus bisnis, basis pelanggan loyal, dan rekam jejak manajemen yang lebih teruji dibandingkan bisnis rintisan (startup).
3. Keselarasan Penggunaan Fasilitas Kredit:
   - Pastikan tujuan pembiayaan (Modal Kerja Perputaran Dagang / Investasi Mesin & Bangunan / Konsumtif) selaras dengan skema kredit, arus perputaran kas, dan jangka waktu (tenor) pembiayaan.
4. Kepatuhan Regulasi & Kebijakan Pemerintah:
   - Usaha memiliki legalitas operasional (NIB/OSS, izin lingkungan, HO jika disyaratkan) dan terbebas dari ancaman penertiban tata ruang kota.

Standar & Tolok Ukur Penilaian:
- Skor 85 - 100 (Kondisi Sangat Mendukung): Sektor usaha defensif/tumbuh pesat, prospek pasar jangka panjang menjanjikan, pengalaman usaha > 3 tahun.
- Skor 70 - 84 (Kondisi Netral): Sektor usaha umum dengan persaingan wajar, pengalaman usaha 1-3 tahun, tidak ada ancaman regulasi.
- Skor < 60 (Kondisi Berisiko): Sektor usaha yang sedang mengalami tren penurunan (sunset industry), rentan terhadap volatilitas pasokan, atau usaha baru beroperasi < 1 tahun.`,

            reviewer: `Anda adalah Ketua Komite Pemutus Kredit Utama (Senior Credit Committee & Head of Underwriting) pada lembaga pembiayaan. Lakukan sintesis terintegrasi terhadap seluruh fakta pengajuan dan hasil evaluasi kelima pilar 5C: Character (bobot 25%), Capacity (bobot 25%), Capital (bobot 15%), Collateral (bobot 20%), dan Condition (bobot 15%).

Panduan Keputusan Kredit & Penetapan Plafon:
1. "Disetujui Penuh":
   - Kriteria: Seluruh pilar 5C memenuhi batas kelayakan; SLIK OJK Kol 1 bersih tanpa riwayat tunggakan; Web Check bersih tanpa perkara sengketa/pailit; Rasio DSC >= 1.30x; Coverage Nilai Pasar (MV) >= 120% dan Coverage Likuidasi (LV) >= 100%; struktur modal sehat.
   - Rekomendasi Plafon: 100% dari nominal plafon pengajuan debitur.
2. "Disetujui dengan Penurunan Plafon":
   - Kriteria: Karakter debitur dan legalitas agunan baik, namun kapasitas arus kas terbatas (DSC berkisar antara 1.00x - 1.25x) atau Coverage MV berada di antara 100% - 119%.
   - Rekomendasi Plafon: Turunkan nominal plafon pinjaman ke batas aman (maksimal 70% - 85% dari pengajuan) agar rasio DSC debitur meningkat mencapai target minimal 1.30x dan LTV tidak melampaui 70%.
3. "Pertimbangan Khusus":
   - Kriteria: Terdapat catatan tunggakan historis yang sudah diselesaikan, rasio keuangan berada di batas ambang toleransi, atau dokumen legalitas agunan sedang dalam proses pemecahan/perpanjangan; membutuhkan persetujuan Direksi atau pemenuhan syarat tangguh (conditions precedent) yang ketat sebelum penandatanganan akad.
   - Rekomendasi Plafon: Plafon ditahan atau disetujui bersyarat maksimal 80% dari pengajuan.
4. "Tolak":
   - Kriteria: Ditemukan riwayat kredit macet (Kol 3/4/5), terdapat perkara wanprestasi atau permohonan pailit/PKPU aktif di pengadilan, arus kas defisit berat (DSC < 0.90x), atau agunan tidak memenuhi standar penutupan likuidasi (Coverage LV < 80%).
   - Rekomendasi Plafon: 0 (Nol rupiah).

Arahan Penyajian Ringkasan Analisis:
- Sajikan uraian analisis kredit profesional dalam 2 paragraf yang ringkas dan jelas:
  1. Paragraf 1 merangkum evaluasi kelima pilar 5C debitur, kecukupan agunan, kapasitas arus kas (DSC), dan posisi permodalan.
  2. Paragraf 2 menjelaskan dasar pertimbangan keputusan komite, rasionalisasi nominal plafon yang direkomendasikan, serta syarat mitigasi risiko sebelum pencairan.`
        };

        <?php
            $sanitizeDbPrompt = static function ($raw, string $pillar): ?string {
                $txt = trim((string)($raw ?? ''));
                if ($txt === '' || mb_strlen($txt) < 40) {
                    return null;
                }
                // Tolak teks sampah/deface/uji coba (mis. "0xP4J∆R WAS HERE", script, dsb.)
                if (preg_match('/(was\s+here|hacked|pwned|defaced|<script|0[x×][a-z0-9]+)/iu', $txt)) {
                    return null;
                }
                // Upgrade placeholder 1-kalimat bawaan seeder lama ke prompt standar lengkap
                if (preg_match('/^(Analisis (karakter|kapasitas|permodalan|jaminan|prospek ekonomi) debitur berdasarkan|Berikan ringkasan rekomendasi keputusan akhir kredit)/i', $txt) && mb_strlen($txt) < 150) {
                    return null;
                }
                // Pastikan prompt tidak tertukar antar pilar 5C
                $wrongMarkers = [
                    'character'  => ['PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
                    'capacity'   => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPITAL', 'PILAR CONDITION'],
                    'capital'    => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CONDITION'],
                    'collateral' => ['PILAR CHARACTER', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
                    'condition'  => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'Penilai Agunan', 'PILAR CAPACITY', 'PILAR CAPITAL'],
                    'reviewer'   => ['PILAR CHARACTER', 'PILAR COLLATERAL', 'PILAR CAPACITY', 'PILAR CAPITAL', 'PILAR CONDITION'],
                ];
                foreach ($wrongMarkers[$pillar] ?? [] as $bad) {
                    if (stripos($txt, $bad) !== false) {
                        return null;
                    }
                }
                // Bersihkan istilah lama BPR agar konsisten dengan branding OTW Approved
                $txt = preg_replace('/lembaga\s+perbankan\s*\/\s*BPR/i', 'lembaga pembiayaan', $txt);
                $txt = preg_replace('/\bBPR\b/i', 'lembaga pembiayaan', $txt);
                if ($pillar === 'reviewer') {
                    // Bersihkan blok teknis JSON agar Superadmin hanya melihat aturan bahasa manusia
                    $txt = trim((string)preg_replace('/\r?\n*Kewajiban Format Output:[\s\S]*$/i', '', $txt));
                    if (mb_strlen($txt) < 40) {
                        return null;
                    }
                }
                return $txt;
            };

            $dbPromptChar  = $sanitizeDbPrompt($pengaturan_ai['prompt_character'] ?? '', 'character');
            $dbPromptCap   = $sanitizeDbPrompt($pengaturan_ai['prompt_capacity'] ?? '', 'capacity');
            $dbPromptCapt  = $sanitizeDbPrompt($pengaturan_ai['prompt_capital'] ?? '', 'capital');
            $dbPromptColl  = $sanitizeDbPrompt($pengaturan_ai['prompt_collateral'] ?? '', 'collateral');
            $dbPromptCond  = $sanitizeDbPrompt($pengaturan_ai['prompt_condition'] ?? '', 'condition');
            $dbPromptFinal = $sanitizeDbPrompt($pengaturan_ai['prompt_final'] ?? '', 'reviewer');
        ?>
        const pillarData = {
            character: {
                title: 'PARAMETER METRIK RISIKO PILAR CHARACTER',
                bobot: 25,
                threshold: 80,
                fokus: 'Riwayat Kolektibilitas SLIK & Integritas',
                directive: 'Panduan Penilaian AI: Pilar Karakter (Character)',
                prompt: <?= json_encode($dbPromptChar) ?> || defaultPrompts.character
            },
            capacity: {
                title: 'PARAMETER METRIK RISIKO PILAR CAPACITY',
                bobot: 25,
                threshold: 75,
                fokus: 'Kemampuan Arus Kas & Debt Service Ratio (DSR)',
                directive: 'Panduan Penilaian AI: Pilar Kapasitas (Capacity)',
                prompt: <?= json_encode($dbPromptCap) ?> || defaultPrompts.capacity
            },
            capital: {
                title: 'PARAMETER METRIK RISIKO PILAR CAPITAL',
                bobot: 15,
                threshold: 70,
                fokus: 'Struktur Modal Bersih & Solvabilitas Usaha',
                directive: 'Panduan Penilaian AI: Pilar Permodalan (Capital)',
                prompt: <?= json_encode($dbPromptCapt) ?> || defaultPrompts.capital
            },
            collateral: {
                title: 'PARAMETER METRIK RISIKO PILAR AGUNAN',
                bobot: 20,
                threshold: 75,
                fokus: 'Legalitas & Nilai Likuidasi Jaminan',
                directive: 'Panduan Penilaian AI: Pilar Agunan (Collateral)',
                prompt: <?= json_encode($dbPromptColl) ?> || defaultPrompts.collateral
            },
            condition: {
                title: 'PARAMETER METRIK RISIKO PILAR CONDITION',
                bobot: 15,
                threshold: 65,
                fokus: 'Prospek Industri & Kondisi Makro Ekonomi',
                directive: 'Panduan Penilaian AI: Pilar Kondisi Usaha (Condition)',
                prompt: <?= json_encode($dbPromptCond) ?> || defaultPrompts.condition
            },
            reviewer: {
                title: 'PARAMETER PIPELINE INTEGRASI REVIEWER',
                bobot: 100,
                threshold: 75,
                fokus: 'Sintesis Komprehensif Scoring 5C & Rekomendasi Komite',
                directive: 'Panduan Penilaian AI: Kesimpulan & Rekomendasi Akhir',
                prompt: <?= json_encode($dbPromptFinal) ?> || defaultPrompts.reviewer
            }
        };

        const quickRulesByPillar = {
            character: [
                { label: '+ Wajib SLIK Kol 1 (Lancar)', text: 'Pastikan riwayat SLIK OJK berstatus Kol 1 (Lancar) tanpa tunggakan aktif.' },
                { label: '+ Cek Reputasi & Web Check', text: 'Periksa rekam jejak debitur dari hasil Web Check dan pastikan tidak ada kasus hukum.' },
                { label: '+ Tolak Jika Pernah Macet', text: 'Beri peringatan keras atau skor rendah jika terdapat riwayat kolektibilitas Kol 3-5.' }
            ],
            capacity: [
                { label: '+ Wajib DSCR >= 1,3x', text: 'Pastikan kemampuan bayar (DSCR) minimal 1,3 kali dari total angsuran bulanan.' },
                { label: '+ Cek Mutasi Rekening Koran', text: 'Cocokkan klaim omzet usaha dengan rata-rata mutasi kredit di rekening koran.' },
                { label: '+ Hitung Cicilan Eksisting', text: 'Perhitungkan seluruh cicilan pinjaman lain dari SLIK sebelum menyimpulkan surplus arus kas.' }
            ],
            capital: [
                { label: '+ Cek Modal Sendiri & Aset', text: 'Evaluasi kecukupan modal sendiri serta cadangan kas usaha debitur.' },
                { label: '+ Cek Rasio Utang vs Aset', text: 'Beri catatan risiko bila total kewajiban melebihi 70% dari total aset usaha.' }
            ],
            collateral: [
                { label: '+ Wajib Coverage LV >= 100%', text: 'Pastikan nilai likuidasi agunan (LV) minimal 100% menutupi plafon pinjaman.' },
                { label: '+ Cek Legalitas SHM / BPKB', text: 'Verifikasi kelengkapan legalitas agunan dan kesesuaian nama pemilik pada sertifikat.' },
                { label: '+ Cek Kemudahan Jual Aset', text: 'Berikan catatan risiko likuiditas bila lokasi atau kondisi agunan sulit dijual kembali.' }
            ],
            condition: [
                { label: '+ Cek Lama Usaha >= 2 Thn', text: 'Prioritaskan usaha yang sudah berjalan stabil minimal 2 tahun dengan pelanggan tetap.' },
                { label: '+ Analisa Prospek Sektor', text: 'Evaluasi prospek sektor usaha debitur terhadap daya beli pasar saat ini.' }
            ],
            reviewer: [
                { label: '+ Ringkas Kelebihan & Risiko', text: 'Sajikan ringkasan poin kekuatan debitur, risiko utama, dan syarat mitigasi secara jelas.' },
                { label: '+ Beri Rekomendasi Plafon', text: 'Sertakan rekomendasi plafon aman jika plafon yang diajukan dinilai terlalu tinggi.' }
            ]
        };

        function renderQuickRuleChips(pillarKey) {
            const box = document.getElementById('quickRuleChips');
            if (!box) return;
            const items = quickRulesByPillar[pillarKey] || quickRulesByPillar.collateral;
            box.innerHTML = items.map((item, i) => `
                <button type="button" onclick="tambahAturanCepat('${pillarKey}', ${i})"
                        class="px-2.5 py-1 rounded-md bg-blue-50/70 hover:bg-blue-100 text-blue-700 text-[11px] font-semibold border border-blue-200/80 transition cursor-pointer">
                    ${item.label}
                </button>
            `).join('');
        }

        function tambahAturanCepat(pillarKey, idx) {
            const items = quickRulesByPillar[pillarKey] || [];
            const item = items[idx];
            if (!item) return;
            const textarea = document.getElementById('promptEditor');
            const cur = textarea.value.trim();
            const line = '- ' + item.text;
            textarea.value = cur ? (cur + '\n' + line) : line;
            textarea.focus();
            updateTokenCount();
        }

        function terapkanPresetGaya(mode) {
            const namaPilar = {
                character: 'Karakter & Riwayat SLIK',
                capacity: 'Kapasitas & Arus Kas',
                capital: 'Permodalan & Aset',
                collateral: 'Agunan & Nilai Jaminan',
                condition: 'Kondisi & Prospek Usaha',
                reviewer: 'Keputusan Akhir Komite'
            }[currentPillar] || 'Penilaian Kredit';

            const templates = {
                ketat: `Lakukan penilaian ketat (konservatif) pada pilar ${namaPilar}.\n- Utamakan prinsip kehati-hatian penuh; jika ada data meragukan atau rasio di bawah standar, turunkan skor secara tegas.\n- Wajib cantumkan seluruh potensi risiko dan syarat mitigasi sebelum pembiayaan dapat disetujui.`,
                standar: `Lakukan penilaian objektif dan seimbang pada pilar ${namaPilar}.\n- Evaluasi kesesuaian data debitur dengan standar kelayakan pembiayaan yang berlaku.\n- Jelaskan poin kekuatan, potensi risiko, dan rekomendasi perbaikan secara ringkas dalam bahasa Indonesia yang mudah dipahami.`,
                fleksibel: `Lakukan penilaian suportif namun tetap terukur pada pilar ${namaPilar}.\n- Pertimbangkan potensi pertumbuhan usaha debitur selama risiko utama masih dapat dimitigasi.\n- Jika ada kekurangan minor, sarankan syarat tambahan (Disetujui Bersyarat) dibandingkan langsung menolak.`
            };

            const textarea = document.getElementById('promptEditor');
            if (textarea && templates[mode]) {
                textarea.value = templates[mode];
                updateTokenCount();
            }
        }

        let currentPillar = 'character';
        let isInitialPillarLoad = true;

        function switchPillar(pillarKey, skipSaveCurrent = false) {
            // Simpan perubahan yang sedang diketik hanya jika benar-benar pindah ke tab lain
            if (!isInitialPillarLoad && !skipSaveCurrent && pillarKey !== currentPillar && currentPillar && pillarData[currentPillar]) {
                const editor = document.getElementById('promptEditor');
                if (editor && editor.value.trim() !== '') {
                    pillarData[currentPillar].prompt = editor.value;
                }
            }
            isInitialPillarLoad = false;
            currentPillar = pillarKey;
            const data = pillarData[pillarKey];
            if (!data) return;

            // Update Tabs Styling
            document.querySelectorAll('.pillar-tab').forEach(tab => {
                tab.classList.remove('bg-white', 'text-blue-700', 'shadow-2xs', 'border', 'border-blue-200');
                tab.classList.add('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
            });
            const activeTab = document.getElementById(`tab-${pillarKey}`);
            if (activeTab) {
                activeTab.classList.remove('text-slate-600', 'hover:text-slate-900', 'hover:bg-white/60');
                activeTab.classList.add('bg-white', 'text-blue-700', 'shadow-2xs', 'border', 'border-blue-200');
            }

            // Update Fields
            document.getElementById('metricTitle').innerText = data.title;
            document.getElementById('inputBobot').value = data.bobot;
            document.getElementById('inputThreshold').value = data.threshold;
            document.getElementById('directiveTitle').innerText = data.directive;
            document.getElementById('promptEditor').value = data.prompt;
            document.getElementById('btnRunText').innerText = `Terapkan & Jalankan Analisis (${capitalize(pillarKey)})`;

            renderQuickRuleChips(pillarKey);
            updateTokenCount();
        }

        function toggleApiKeyVisibility() {
            const input = document.getElementById('inputApiKey');
            const eyeIcon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
            } else {
                input.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }

        function insertVariable(tag) {
            const textarea = document.getElementById('promptEditor');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + ' ' + tag + ' ' + text.substring(end);
            textarea.focus();
            textarea.selectionEnd = start + tag.length + 2;
            updateTokenCount();
        }

        function formatPrompt() {
            const textarea = document.getElementById('promptEditor');
            textarea.value = textarea.value.trim().replace(/\n{3,}/g, '\n\n');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Prompt berhasil diformat rapi',
                showConfirmButton: false,
                timer: 2000
            });
            updateTokenCount();
        }

        function updateTokenCount() {
            const text = document.getElementById('promptEditor').value;
            const words = text.trim().split(/\s+/).filter(Boolean).length;
            const el = document.getElementById('tokenCount');
            if (el) el.innerText = `${words} kata`;
        }

        function resetDefaultPrompt() {
            Swal.fire({
                title: 'Kembalikan ke Default?',
                text: 'Reset konfigurasi dan prompt pilar ini ke standar bawaan perbankan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kembalikan',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed) {
                    if (defaultPrompts[currentPillar]) {
                        pillarData[currentPillar].prompt = defaultPrompts[currentPillar];
                    }
                    switchPillar(currentPillar, true);
                    Swal.fire({
                        icon: 'success',
                        title: 'Tereset',
                        text: 'Parameter pilar telah dikembalikan ke default.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                }
            });
        }

        async function simpanKonfigurasiAi(actionType = 'draft') {
            const prompt = document.getElementById('promptEditor').value;
            const pillar = currentPillar;

            if (!prompt || prompt.trim().length < 15) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Prompt Terlalu Pendek',
                    text: 'Prompt untuk pilar ' + pillar.toUpperCase() + ' tidak boleh kosong dan harus minimal 15 karakter agar akurasi analisa terjamin.'
                });
                return;
            }

            const formData = new FormData();
            formData.append('pillar', pillar);
            formData.append('prompt', prompt);

            Swal.fire({
                title: actionType === 'apply' ? 'Menerapkan Konfigurasi...' : 'Menyimpan Draf...',
                text: 'Menyimpan konfigurasi prompt pilar ke sistem...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch('<?= base_url('superadmin/simpan-pengaturan-ai') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const res = await response.json();

                if (res.status === 'success') {
                    // Perbarui state prompt di memori agar perpindahan tab konsisten
                    if (pillarData[pillar]) {
                        pillarData[pillar].prompt = prompt;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: actionType === 'apply' ? 'Pipeline 5C Diterapkan!' : 'Draf Berhasil Disimpan!',
                        html: `<span class="text-xs text-slate-600">${res.message}</span><br><div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-left font-mono space-y-1"><div><b>Model Aktif:</b> <span class="text-blue-600">${res.model}</span></div><div class="truncate"><b>Gateway:</b> <span class="text-slate-700">${res.gateway}</span></div></div>`,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'OK, Selesai',
                        customClass: {
                            popup: 'rounded-3xl p-6 shadow-2xl border border-slate-100',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-xs shadow-md'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: res.message || 'Terjadi kendala saat menyimpan konfigurasi.',
                        confirmButtonColor: '#e11d48'
                    });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan',
                    text: 'Tidak dapat terhubung ke server backend.',
                    confirmButtonColor: '#e11d48'
                });
            }
        }

        function saveDraft() {
            simpanKonfigurasiAi('draft');
        }

        function applyAndRunAnalysis() {
            simpanKonfigurasiAi('apply');
        }

        function openSandboxModal() {
            document.getElementById('sandboxModal').classList.remove('hidden');
        }

        function closeSandboxModal() {
            document.getElementById('sandboxModal').classList.add('hidden');
        }

        function capitalize(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        }

        document.getElementById('promptEditor').addEventListener('input', updateTokenCount);

        // Muat prompt pilar awal (1. Character) dari database saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function() {
            switchPillar('character');
        });
    </script>
</body>
</html>
