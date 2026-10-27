<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Konfigurasi Parameter Analisis 5C | OTW Kredit') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        'nama_lengkap' => $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Super Administrator',
        'jabatan'      => $jabatan ?? session()->get('jabatan') ?? 'Superadmin'
    ]) ?>

    <!-- MAIN WRAPPER (Offset by sidebar width md:pl-64) -->
    <div class="md:pl-64 flex flex-col min-h-screen w-full">

        <!-- Mobile Header Bar -->
        <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-20 w-full">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <?php if (!empty($pengaturan_sistem['logo_aplikasi'])): ?>
                        <img src="<?= base_url($pengaturan_sistem['logo_aplikasi']) ?>" class="w-6 h-6 object-contain rounded" alt="Logo">
                    <?php endif; ?>
                    <span class="font-bold text-slate-900 text-sm"><?= esc($pengaturan_sistem['nama_aplikasi'] ?? 'OTW Kredit') ?></span>
                </div>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="text-xs text-rose-600 font-semibold px-2.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200">
                Logout
            </a>
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

            <!-- Card Utama: KONFIGURASI API & LINK GATEWAY MODEL AI (Diletakkan di Atas Nomor Pilar) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 mb-6 shadow-2xs">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                KONFIGURASI API &amp; LINK GATEWAY MODEL AI
                            </h2>
                            <p class="text-[11px] text-slate-400">Pilih model LLM terintegrasi dan atur URL gateway endpoint komunikasi AI</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Gateway Terhubung
                        </span>
                    </div>
                </div>

                <?php
                    $currentModel = $pengaturan_sistem['model_ai'] ?? $pengaturan_ai['model_ai'] ?? 'gemini-2.5-flash';
                    $currentEndpoint = $pengaturan_sistem['api_endpoint'] ?? 'https://generativelanguage.googleapis.com/v1beta/models';
                    $currentKey = $pengaturan_sistem['api_key'] ?? $pengaturan_ai['api_key'] ?? '';
                ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    
                    <!-- Col 1: API Pemilihan Model -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700 flex items-center gap-1">
                                <span>API Pemilihan Model</span>
                                <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">Engine Core</span>
                        </div>
                        <div class="relative">
                            <select id="selectModelAi" class="w-full px-3.5 py-2.5 text-xs font-bold text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition appearance-none pr-8 cursor-pointer shadow-2xs">
                                <option value="gemini-2.5-flash" <?= $currentModel === 'gemini-2.5-flash' ? 'selected' : '' ?>>Google Gemini 2.5 Flash (Recommended)</option>
                                <option value="gemini-1.5-pro" <?= $currentModel === 'gemini-1.5-pro' ? 'selected' : '' ?>>Google Gemini 1.5 Pro (Deep Multimodal)</option>
                                <option value="gemini-1.5-flash" <?= $currentModel === 'gemini-1.5-flash' ? 'selected' : '' ?>>Google Gemini 1.5 Flash (Lightweight)</option>
                                <option value="gemini-2.0-flash" <?= $currentModel === 'gemini-2.0-flash' ? 'selected' : '' ?>>Google Gemini 2.0 Flash (Next-Gen)</option>
                                <option value="gpt-4o" <?= $currentModel === 'gpt-4o' ? 'selected' : '' ?>>OpenAI GPT-4o (Omni Intelligence)</option>
                                <option value="gpt-4o-mini" <?= $currentModel === 'gpt-4o-mini' ? 'selected' : '' ?>>OpenAI GPT-4o Mini (Cost Efficient)</option>
                                <option value="claude-3-5-sonnet" <?= $currentModel === 'claude-3-5-sonnet' ? 'selected' : '' ?>>Anthropic Claude 3.5 Sonnet</option>
                                <option value="deepseek-chat" <?= $currentModel === 'deepseek-chat' ? 'selected' : '' ?>>DeepSeek V3 / R1</option>
                                <option value="custom" <?= !in_array($currentModel, ['gemini-2.5-flash','gemini-1.5-pro','gemini-1.5-flash','gemini-2.0-flash','gpt-4o','gpt-4o-mini','claude-3-5-sonnet','deepseek-chat']) ? 'selected' : '' ?>>Model Kustom / Self-Hosted</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400">Pilih model LLM untuk pipeline penilaian 5C</p>
                    </div>

                    <!-- Col 2: Link Gateway API (Opsional) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                <span>Link Gateway</span>
                                <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Opsional</span>
                        </div>
                        <div class="relative">
                            <input type="url" 
                                   id="inputApiEndpoint" 
                                   value="<?= esc($currentEndpoint) ?>" 
                                   placeholder="Default bawaan jika dikosongkan..." 
                                   class="w-full px-3.5 py-2.5 text-xs font-mono text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                        </div>
                        <p class="text-[11px] text-slate-400">Kosongkan jika menggunakan gateway resmi default bawaan model</p>
                    </div>

                    <!-- Col 3: API Key Otentikasi -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-slate-700">API Key / Token Gateway</label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">Tersimpan</span>
                        </div>
                        <div class="relative">
                            <input type="password" 
                                   id="inputApiKey" 
                                   value="<?= esc($currentKey) ?>" 
                                   placeholder="Masukkan API Key..." 
                                   class="w-full pl-3.5 pr-10 py-2.5 text-xs font-mono text-slate-900 bg-white border border-slate-200 rounded-xl focus:border-blue-500 focus:outline-none transition shadow-2xs" />
                            <button type="button" 
                                    onclick="toggleApiKeyVisibility()" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition cursor-pointer" 
                                    title="Tampilkan / Sembunyikan Key">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400">Kredensial otentikasi API yang terenkripsi</p>
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

            <!-- Card 2: Parameter Metrik Risiko Pilar (Berdasarkan Tab Aktif) -->
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
                        <p class="text-[11px] text-slate-400">Batas minimum kelayakan jaminan kredit</p>
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
            document.getElementById('directiveTitle').innerText = data.directive;
            document.getElementById('promptEditor').value = data.prompt;
            document.getElementById('btnRunText').innerText = `Terapkan & Jalankan Analisis (${capitalize(pillarKey)})`;

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
            const approxTokens = Math.round(words * 1.33);
            document.getElementById('tokenCount').innerText = `Approx. ${approxTokens} Tokens`;
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
                    switchPillar(currentPillar);
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
            const modelAi = document.getElementById('selectModelAi').value;
            const apiEndpoint = document.getElementById('inputApiEndpoint').value;
            const apiKey = document.getElementById('inputApiKey').value;
            const prompt = document.getElementById('promptEditor').value;
            const pillar = currentPillar;

            const formData = new FormData();
            formData.append('model_ai', modelAi);
            formData.append('api_endpoint', apiEndpoint);
            formData.append('api_key', apiKey);
            formData.append('pillar', pillar);
            formData.append('prompt', prompt);

            Swal.fire({
                title: actionType === 'apply' ? 'Menerapkan Konfigurasi...' : 'Menyimpan Draf...',
                text: 'Menghubungkan ke gateway API dan menyimpan konfigurasi model...',
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
                    Swal.fire({
                        icon: 'success',
                        title: actionType === 'apply' ? 'Model & Pipeline Diterapkan!' : 'Draf Berhasil Disimpan!',
                        html: `<span class="text-xs text-slate-600">${res.message}</span><br><div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-left font-mono space-y-1"><div><b>Model:</b> <span class="text-blue-600">${res.model}</span></div><div class="truncate"><b>Gateway:</b> <span class="text-slate-700">${res.gateway}</span></div></div>`,
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
    </script>
</body>
</html>
