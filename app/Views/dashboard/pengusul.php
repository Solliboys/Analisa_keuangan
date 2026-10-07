<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 p-8">
    
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Pengusul</h1>
                <p class="text-slate-500">Selamat datang, <?php echo esc($nama_lengkap); ?>!</p>
            </div>
            <a href="<?= base_url('auth/logout') ?>" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition">Logout</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-blue-50 rounded-xl border border-blue-100">
                <h3 class="font-bold text-blue-900 mb-2">Input Data Nasabah</h3>
                <p class="text-sm text-blue-700">Masukkan data nasabah baru untuk dianalisa sistem.</p>
                <button class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm transition">Input Nasabah</button>
            </div>
            <div class="p-6 bg-slate-100 rounded-xl border border-slate-200">
                <h3 class="font-bold text-slate-700 mb-2">Riwayat Pengajuan</h3>
                <p class="text-sm text-slate-500">Lihat status pengajuan nasabah yang telah diinput.</p>
                <button class="mt-4 px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-md text-sm transition">Lihat Riwayat</button>
            </div>
        </div>
    </div>

</body>
</html>
