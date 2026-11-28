<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Error') ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 max-w-md w-full text-center">
        <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h2 class="text-base font-extrabold text-slate-800 mb-2"><?= esc($title ?? 'Surat Keputusan Belum Tersedia') ?></h2>
        <p class="text-sm text-slate-500"><?= esc($pesan ?? 'Terjadi kesalahan. Silakan coba lagi.') ?></p>
        <?php if (!empty($kredit['nomor_aplikasi'])): ?>
        <p class="text-xs text-slate-400 mt-2">No. Aplikasi: <?= esc($kredit['nomor_aplikasi']) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
