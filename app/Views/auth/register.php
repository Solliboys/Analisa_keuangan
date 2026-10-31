<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Pengusul | OTW Credit Engine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-slate-50 antialiased">

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row w-full max-w-4xl border border-slate-200">
        
        <!-- Left Side: Informational Brand Box -->
        <div class="w-full md:w-5/12 bg-gradient-to-br from-blue-700 via-indigo-800 to-slate-900 p-8 sm:p-10 text-white flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center font-black text-xl border border-white/20 mb-6">
                    AO
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Pendaftaran Petugas Pengusul</h1>
                <p class="text-xs text-blue-100 mt-2 leading-relaxed">
                    Daftarkan data diri Anda sebagai Account Officer / Staff Pengusul Kredit. Setelah mendaftar, akun Anda akan diverifikasi dan di-approve oleh Super Administrator.
                </p>

                <div class="mt-8 space-y-3 text-xs text-blue-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-bold">&check;</span>
                        <span>Input data analisa kredit komprehensif</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-bold">&check;</span>
                        <span>Simulasi real-time angsuran & DSC ratio</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px] font-bold">&check;</span>
                        <span>Cetak otomatis Credit Application Memo (PDF)</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-white/10 text-[11px] text-blue-200">
                Punya akun yang sudah aktif? 
                <a href="<?= base_url('auth') ?>" class="text-white font-bold underline ml-1">Masuk Sekarang</a>
            </div>
        </div>

        <!-- Right Side: Registration Form -->
        <div class="w-full md:w-7/12 p-8 sm:p-10 flex flex-col justify-center">
            
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-slate-900">Formulir Registrasi Pengusul</h2>
                <p class="text-xs text-slate-500 mt-0.5">Lengkapi identitas Anda untuk mendapatkan akses ke sistem kredit.</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl font-semibold flex items-center justify-between">
                    <span><?= session()->getFlashdata('error') ?></span>
                    <button onclick="this.parentElement.remove()" class="text-rose-500 font-bold">&times;</button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/registerProcess') ?>" method="POST" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Petugas *</label>
                    <input type="text" name="nama_lengkap" value="<?= old('nama_lengkap') ?>" placeholder="Contoh: Rian Pratama, S.E." required
                           class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Kantor / Pribadi</label>
                        <input type="email" name="email" value="<?= old('email') ?>" placeholder="petugas@bpr.co.id"
                               class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor WhatsApp Aktif</label>
                        <input type="text" name="nomor_hp" value="<?= old('nomor_hp') ?>" placeholder="081234567xxx"
                               class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username Login *</label>
                    <input type="text" name="username" value="<?= old('username') ?>" placeholder="Gunakan huruf kecil tanpa spasi" required
                           class="w-full text-xs font-mono border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                        <input type="password" id="regPassword" name="password" placeholder="Min. 8 karakter berkarakter" required
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$"
                               title="Minimal 8 karakter, wajib kombinasi huruf besar (A-Z), huruf kecil (a-z), angka (0-9), dan simbol (!@#$%^&*)"
                               class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ulangi Password *</label>
                        <input type="password" name="konfirmasi_password" placeholder="Ketik ulang password" required
                               class="w-full text-xs border border-slate-300 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="p-3 bg-blue-50/60 border border-blue-200/80 rounded-xl text-[11px] text-slate-700">
                    <span class="font-bold text-blue-900 block mb-1">🔒 Syarat Keamanan Password (Standar Perbankan):</span>
                    <ul class="grid grid-cols-2 gap-1 list-disc list-inside text-[10px] text-slate-600">
                        <li>Minimal 8 karakter</li>
                        <li>Mengandung huruf besar (A-Z)</li>
                        <li>Mengandung huruf kecil (a-z)</li>
                        <li>Kombinasi angka & simbol (!@#$)</li>
                    </ul>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-700 to-indigo-800 hover:from-blue-800 hover:to-indigo-900 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Daftar & Ajukan Akun Pengusul
                    </button>
                </div>

                <div class="text-center pt-2">
                    <p class="text-xs text-slate-400">
                        Sudah punya akun? 
                        <a href="<?= base_url('auth') ?>" class="font-bold text-blue-700 hover:text-blue-900 underline">
                            Login di sini
                        </a>
                    </p>
                </div>

            </form>

        </div>

    </div>

</body>
</html>
