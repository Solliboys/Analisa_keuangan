<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Konfigurasi Parameter Analisis 5C | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen">

    <!-- Include Sidebar Navbar -->
    <?= $this->include('navbar', [
        'active_menu'  => 'ai_5c',
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Budi Santoso',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Senior Credit Analyst'
    ]) ?>

    <!-- MAIN WRAPPER (Offset by sidebar width md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen">

        <!-- Mobile Header Bar -->
        <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="font-bold text-slate-900 text-sm">OTW <span class="text-blue-600">Kredit</span></span>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                Logout
            </a>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto">
            
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
                        <span class="px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-600 border border-slate-200">
                            Engine: CreditScoring-v3.4
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

            <!-- Tab Navigation (Pill Tabs) -->
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

                    <!-- Tab 4: Collateral (ACTIVE by default) -->
                    <button type="button" onclick="switchPillar('collateral')" id="tab-collateral" class="pillar-tab flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-white text-blue-700 shadow-2xs border border-blue-200 transition">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>4. Collateral</span>
                        <span class="px-1.5 py-0.5 rounded-md bg-blue-100 text-blue-700 text-[11px] font-bold">20%</span>
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

            <!-- Card 1: Parameter Metrik Risiko Pilar Agunan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 mb-6 shadow-2xs">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800" id="metricTitle">
                            PARAMETER METRIK RISIKO PILAR AGUNAN
                        </h2>
                        <span class="text-xs text-slate-400 font-medium">(Regulasi OJK &amp; Kebijakan Risiko BPR)</span>
                    </div>
                    <a href="javascript:void(0)" onclick="alert('Panduan Scoring sesuai POJK No. 40/POJK.03/2019 tentang Penilaian Kualitas Aset BPR.')" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition">
                        <span>Panduan Scoring</span>
                        <span>&gt;</span>
                    </a>
                </div>

                <!-- 3 Metric Columns -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <!-- Col 1: Bobot Portofolio Pilar -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Bobot Portofolio Pilar</label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">Max 40%</span>
                        </div>
                        <div class="relative">
                            <input type="number" id="inputBobot" value="20" class="w-full pl-3.5 pr-8 py-2.5 text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition" />
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
                            <input type="number" id="inputThreshold" value="75" class="w-full pl-3.5 pr-14 py-2.5 text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition" />
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-semibold text-slate-400">/ 100</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Batas minimum kelayakan jaminan kredit</p>
                    </div>

                    <!-- Col 3: Fokus Penilaian Utama -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">Fokus Penilaian Utama</label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 font-mono">OJK POJK-40</span>
                        </div>
                        <div>
                            <input type="text" id="inputFokus" value="Legalitas & Nilai Likuidasi Jaminan" class="w-full px-3.5 py-2.5 text-sm font-medium text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition" />
                        </div>
                        <p class="text-[11px] text-slate-400">Kepatuhan hak tanggungan &amp; coverage ratio</p>
                    </div>

                </div>
            </div>

            <!-- Card 2: System Directive / Prompt Pipeline Editor -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden mb-8">
                
                <!-- Dark Header Bar -->
                <div class="bg-[#0f172a] text-white p-4">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                        
                        <!-- Left: Directive Title & Model Temperature -->
                        <div class="flex items-center gap-3">
                            <div class="p-1.5 rounded-lg bg-slate-800 text-blue-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold tracking-tight text-white" id="directiveTitle">
                                System Directive: Collateral Underwriter
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                Model Temp: 0.15 (Strict)
                            </span>
                        </div>

                        <!-- Right: Variable Tag Pills -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <span class="text-slate-400 text-[11px] mr-1">Sisipkan Variabel:</span>
                            
                            <button type="button" onclick="insertVariable('{{dokumen_agunan}}')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-mono text-[11px] border border-slate-700 transition">
                                {{dokumen_agunan}}
                            </button>
                            <button type="button" onclick="insertVariable('{{nilai_pasar_agunan}}')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-mono text-[11px] border border-slate-700 transition">
                                {{nilai_pasar_agunan}}
                            </button>
                            <button type="button" onclick="insertVariable('{{plafon_diajukan}}')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-mono text-[11px] border border-slate-700 transition">
                                {{plafon_diajukan}}
                            </button>
                            <button type="button" onclick="insertVariable('{{coverage_ratio}}')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-mono text-[11px] border border-slate-700 transition">
                                {{coverage_ratio}}
                            </button>

                            <!-- Format Button -->
                            <button type="button" onclick="formatPrompt()" class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium text-[11px] flex items-center gap-1.5 transition ml-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                                <span>Format</span>
                            </button>
                        </div>

                    </div>
                </div>

                <!-- Code / Directive Editor Body -->
                <div class="p-4 sm:p-5 bg-white">
                    <textarea id="promptEditor" rows="12" class="w-full font-mono-code text-xs sm:text-[13px] text-slate-800 leading-relaxed outline-none resize-y border-0 bg-transparent focus:ring-0 p-0" spellcheck="false">Anda adalah Penilai Agunan & Legal Officer BPR berstandar perbankan untuk pilar COLLATERAL (Jaminan). Pastikan prinsip Kehati-hatian (Prudential Banking) terpenuhi secara mutlak.

Input Variabel Kredit:
- Dokumen Legalitas : {{dokumen_agunan}} (SHM / SHGB / BPKB)
- Taksasi Pasar     : {{nilai_pasar_agunan}}
- Estimasi Likuidasi: {{nilai_likuidasi}}
- Plafon Diajukan   : {{plafon_diajukan}}

System Execution Directives:
1. Kalkulasi Loan-to-Value (LTV) dan rasio likuidasi terhadap plafon kredit.</textarea>
                </div>

                <!-- Editor Status Footer -->
                <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Sintaks &amp; Variabel: 4/4 Terdeteksi Valid
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-blue-600 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z" />
                            </svg>
                            Output Schema: JSON Validated Strict
                        </span>
                    </div>
                    <span class="text-slate-400 font-mono text-[11px]" id="tokenCount">
                        Approx. 184 Tokens
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
    <div id="sandboxModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full p-6 animate-in fade-in duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Uji Coba Sandbox Evaluasi 5C</h3>
                </div>
                <button onclick="closeSandboxModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="mt-4 space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Simulasi Agunan (SHM / BPKB):</label>
                    <input type="text" value="SHM No. 412 Luas 240m2 An. Debitur" class="w-full px-3 py-2 border border-slate-200 rounded-xl" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Taksasi Pasar:</label>
                        <input type="text" value="Rp 380.000.000" class="w-full px-3 py-2 border border-slate-200 rounded-xl" />
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Plafon Diajukan:</label>
                        <input type="text" value="Rp 250.000.000" class="w-full px-3 py-2 border border-slate-200 rounded-xl" />
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 mt-2 space-y-1">
                    <div class="flex justify-between font-semibold text-slate-700">
                        <span>Hasil Perhitungan LTV:</span>
                        <span class="text-emerald-600 font-bold">65.7% (Aman &lt; 70%)</span>
                    </div>
                    <div class="flex justify-between font-semibold text-slate-700">
                        <span>Skor Kelayakan Agunan AI:</span>
                        <span class="text-blue-600 font-bold">88 / 100 (Sangat Layak)</span>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex justify-end gap-2 border-t border-slate-100 pt-3">
                <button type="button" onclick="closeSandboxModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Scripts -->
    <script>
        const pillarData = {
            character: {
                title: 'PARAMETER METRIK RISIKO PILAR CHARACTER',
                bobot: 25,
                threshold: 80,
                fokus: 'Riwayat Kolektibilitas SLIK & Integritas',
                directive: 'System Directive: Character Underwriter',
                prompt: `Anda adalah Analis Kredit Senior BPR untuk pilar CHARACTER (Karakter). Pastikan rekam jejak finansial debitur dianalisis secara akurat.\n\nInput Variabel:\n- SLIK iDeb Status   : {{slik_status}}\n- Track Record BI   : {{track_record}}\n- Lama Hubungan BPR : {{lama_hubungan}}`
            },
            capacity: {
                title: 'PARAMETER METRIK RISIKO PILAR CAPACITY',
                bobot: 25,
                threshold: 75,
                fokus: 'Kemampuan Arus Kas & Debt Service Ratio (DSR)',
                directive: 'System Directive: Capacity Underwriter',
                prompt: `Anda adalah Analis Keuangan BPR untuk pilar CAPACITY (Kemampuan Bayar). Evaluasi arus kas usaha dan kemampuan angsuran debitur.\n\nInput Variabel:\n- Omzet Bulanan     : {{omzet_bulanan}}\n- Biaya Operasional : {{biaya_operasional}}\n- Angsuran Berjalan : {{angsuran_lain}}`
            },
            capital: {
                title: 'PARAMETER METRIK RISIKO PILAR CAPITAL',
                bobot: 15,
                threshold: 70,
                fokus: 'Struktur Modal Bersih & Solvabilitas Usaha',
                directive: 'System Directive: Capital Underwriter',
                prompt: `Anda adalah Analis Permodalan BPR untuk pilar CAPITAL (Modal). Nilai kekuatan modal sendiri debitur dan rasio utang terhadap ekuitas.\n\nInput Variabel:\n- Modal Sendiri     : {{modal_sendiri}}\n- Total Aset        : {{total_aset}}\n- Utang Dagang      : {{utang_lancar}}`
            },
            collateral: {
                title: 'PARAMETER METRIK RISIKO PILAR AGUNAN',
                bobot: 20,
                threshold: 75,
                fokus: 'Legalitas & Nilai Likuidasi Jaminan',
                directive: 'System Directive: Collateral Underwriter',
                prompt: `Anda adalah Penilai Agunan & Legal Officer BPR berstandar perbankan untuk pilar COLLATERAL (Jaminan). Pastikan prinsip Kehati-hatian (Prudential Banking) terpenuhi secara mutlak.\n\nInput Variabel Kredit:\n- Dokumen Legalitas : {{dokumen_agunan}} (SHM / SHGB / BPKB)\n- Taksasi Pasar     : {{nilai_pasar_agunan}}\n- Estimasi Likuidasi: {{nilai_likuidasi}}\n- Plafon Diajukan   : {{plafon_diajukan}}\n\nSystem Execution Directives:\n1. Kalkulasi Loan-to-Value (LTV) dan rasio likuidasi terhadap plafon kredit.`
            },
            condition: {
                title: 'PARAMETER METRIK RISIKO PILAR CONDITION',
                bobot: 15,
                threshold: 65,
                fokus: 'Prospek Industri & Kondisi Makro Ekonomi',
                directive: 'System Directive: Condition Underwriter',
                prompt: `Anda adalah Penilai Risiko Makro BPR untuk pilar CONDITION (Kondisi Ekonomi). Tinjau ketahanan usaha debitur terhadap faktor eksternal pasar.\n\nInput Variabel:\n- Sektor Industri   : {{sektor_usaha}}\n- Tren Permintaan   : {{tren_pasar}}\n- Dampak Regulasi   : {{regulasi_terkait}}`
            },
            reviewer: {
                title: 'PARAMETER PIPELINE INTEGRASI REVIEWER',
                bobot: 100,
                threshold: 75,
                fokus: 'Sintesis Komprehensif Scoring 5C & Rekomendasi Komite',
                directive: 'System Directive: Master Underwriting Reviewer',
                prompt: `Anda adalah Komite Pemutus Kredit Utama BPR. Gabungkan hasil skor kelayakan dari kelima pilar 5C dan terbitkan rekomendasi persetujuan kredit formal.`
            }
        };

        let currentPillar = 'collateral';

        function switchPillar(pillarKey) {
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
            document.getElementById('inputFokus').value = data.fokus;
            document.getElementById('directiveTitle').innerText = data.directive;
            document.getElementById('promptEditor').value = data.prompt;
            document.getElementById('btnRunText').innerText = `Terapkan & Jalankan Analisis (${capitalize(pillarKey)})`;

            updateTokenCount();
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
            showToast('Prompt berhasil diformat rapi.');
            updateTokenCount();
        }

        function updateTokenCount() {
            const text = document.getElementById('promptEditor').value;
            const words = text.trim().split(/\s+/).filter(Boolean).length;
            const approxTokens = Math.round(words * 1.33);
            document.getElementById('tokenCount').innerText = `Approx. ${approxTokens} Tokens`;
        }

        function resetDefaultPrompt() {
            if (confirm('Kembalikan konfigurasi pilar ini ke pengaturan default standar perbankan?')) {
                switchPillar(currentPillar);
                showToast('Parameter dikembalikan ke default.');
            }
        }

        function saveDraft() {
            showToast('Draf konfigurasi parameter berhasil disimpan secara lokal!');
        }

        function applyAndRunAnalysis() {
            showToast(`Konfigurasi parameter ${capitalize(currentPillar)} aktif dan diterapkan ke AI Engine!`, 'success');
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

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-lg flex items-center gap-2.5 text-xs font-medium text-white ${type === 'success' ? 'bg-emerald-600' : 'bg-slate-900'} animate-bounce`;
            toast.innerHTML = `
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>${message}</span>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        document.getElementById('promptEditor').addEventListener('input', updateTokenCount);
    </script>
</body>
</html>
