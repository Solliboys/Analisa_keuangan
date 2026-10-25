<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTW Kredit - Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Latar belakang putih/abu-abu terang */
        }

        /* Gaya Pita Lembut (Soft Ribbon) */
        .ribbon {
            fill: none;
            stroke-linecap: round;
            filter: drop-shadow(0 4px 6px rgba(147, 197, 253, 0.3)); /* Shadow yang lebih halus */
        }

        /* Animasi Mengambang */
        @keyframes float-1 {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-3%) scale(1.02); }
        }
        @keyframes float-2 {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(3%) scale(1.03); }
        }
        
        .layer-1 { animation: float-1 8s ease-in-out infinite; }
        .layer-2 { animation: float-2 10s ease-in-out infinite reverse; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden bg-slate-50">

    <!-- Latar Belakang Pita Lembut (Biru Putih Samar) -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        
        <!-- Kumpulan Pita 1 -->
        <svg class="layer-1 absolute -top-[10%] -left-[10%] w-[120%] h-[120%] opacity-40" preserveAspectRatio="none" viewBox="0 0 1440 800">
            <!-- Pita Besar Biru Sangat Muda -->
            <path class="ribbon" stroke="#dbeafe" stroke-width="50" d="M-100,800 C400,800 600,200 1540,100"></path>
            <!-- Pita Sedang Biru Langit -->
            <path class="ribbon" stroke="#bfdbfe" stroke-width="15" d="M-100,780 C420,780 580,220 1540,80"></path>
            <!-- Pita Kecil Biru Agak Tua -->
            <path class="ribbon" stroke="#93c5fd" stroke-width="4" d="M-100,770 C430,770 570,230 1540,70"></path>
        </svg>

        <!-- Kumpulan Pita 2 -->
        <svg class="layer-2 absolute -top-[10%] -left-[10%] w-[120%] h-[120%] opacity-30" preserveAspectRatio="none" viewBox="0 0 1440 800">
            <path class="ribbon" stroke="#bfdbfe" stroke-width="80" d="M-100,900 C300,500 800,400 1540,600"></path>
            <path class="ribbon" stroke="#93c5fd" stroke-width="25" d="M-100,890 C310,490 790,390 1540,590"></path>
            <path class="ribbon" stroke="#60a5fa" stroke-width="3" d="M-100,885 C315,485 785,385 1540,585"></path>
        </svg>

    </div>

    <!-- Card Utama (Glassmorphism / Menyatu dengan background) -->
    <div class="bg-white/70 backdrop-blur-md rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row w-full max-w-2xl min-h-[450px] relative z-10 border border-white/60">
        
        <!-- KIRI: Gambar Ilustrasi -->
        <div class="w-full md:w-1/2 bg-blue-50 relative hidden md:block">
            <!-- Asumsi nama gambar ilustrasinya adalah login_illustration.png -->
            <img src="<?= base_url('login_illustration.png') ?>" alt="Ilustrasi Digital Banking" class="absolute inset-0 w-full h-full object-cover">
        </div>

        <!-- KANAN: Form Login -->
        <div class="w-full md:w-1/2 p-8 md:p-10 flex flex-col justify-center relative">
            
            <div class="mb-8">
                <!-- Judul OTW Kredit -->
                <h2 class="text-3xl mb-1.5 tracking-tight">
                    <span class="font-extrabold text-slate-800">OTW</span> 
                    <span class="font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#100a8f] to-indigo-700">Kredit</span>
                </h2>
                
                <!-- Salam Waktu Dinamis -->
                <?php 
                    $jam = date('H');
                    if ($jam >= 5 && $jam < 12) $salam = 'Pagi';
                    elseif ($jam >= 12 && $jam < 15) $salam = 'Siang';
                    elseif ($jam >= 15 && $jam < 18) $salam = 'Sore';
                    else $salam = 'Malam';
                ?>
                <p class="text-slate-500 text-sm font-medium">Selamat <?= $salam ?>. Silakan login terlebih dahulu</p>
            </div>

            <!-- Pesan Sukses -->
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-4 text-xs font-medium flex items-start space-x-2.5 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="font-bold text-emerald-900 mb-0.5">Pemberitahuan Sistem</p>
                        <p class="text-emerald-700 leading-relaxed"><?= session()->getFlashdata('success'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pesan Error -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-xs font-medium flex items-start space-x-2.5 shadow-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-bold text-red-900 mb-0.5">Akses Ditolak / Perhatian</p>
                        <p class="text-red-700 leading-relaxed"><?= session()->getFlashdata('error'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= base_url('auth/process') ?>" method="post" class="space-y-6">
                <?= csrf_field() ?>
                
                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="username" id="username" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-4 text-sm text-slate-800 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition-all placeholder-slate-400 font-medium" placeholder="Masukkan Username" required autofocus>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                        <a href="#" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900 transition-colors">Lupa sandi?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-12 text-sm text-slate-800 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/10 transition-all placeholder-slate-400 font-medium" placeholder="••••••••" required>
                        <!-- Icon Mata (Toggle Password) -->
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#1a1c6a] focus:ring-[#1a1c6a] cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-600 cursor-pointer">
                        Ingat sesi saya
                    </label>
                </div>

                <!-- Tombol Login Full Width -->
                <div class="pt-3">
                    <button type="submit" class="w-full flex items-center justify-center bg-gradient-to-r from-[#100a8f] to-indigo-800 hover:from-[#0a0663] hover:to-indigo-900 text-white text-sm font-bold py-3.5 px-8 rounded-xl transition-all duration-300 shadow-lg shadow-indigo-900/30 hover:-translate-y-1 hover:shadow-indigo-900/40">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <!-- Link Pendaftaran Pengusul Baru -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-500">
                        Belum punya akun petugas? 
                        <a href="<?= base_url('auth/register') ?>" class="font-bold text-indigo-700 hover:text-indigo-900 underline transition">
                            Daftar Akun Pengusul
                        </a>
                    </p>
                </div>
            </form>

            <!-- Footer Copyright (Paling Bawah) -->
            <div class="absolute bottom-6 left-0 right-0 text-center">
                <p class="text-[11px] text-slate-400 font-medium">
                    &copy;<?= date('Y') ?> OTW Computer Gusaha. All rights reserved.
                </p>
            </div>

        </div>
    </div>

</body>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Ubah icon jadi mata dicoret (eye-off)
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
        } else {
            passwordInput.type = 'password';
            // Ubah icon jadi mata normal (eye)
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }
</script>
</html>
