<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisa Kelayakan Kredit - <?= esc($kredit['nomor_aplikasi']) ?></title>
    <script src="<?= base_url('libs/tailwind/tailwind.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('fonts/plus-jakarta-sans/plus-jakarta-sans.css') ?>">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; 
            font-size: 11px; 
            line-height: 1.5; 
            color: #1e293b;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        .font-mono { font-family: monospace; }

        table { 
            border-collapse: collapse; 
            width: 100%; 
            table-layout: auto;
        }
        th, td { 
            border: 1px solid #cbd5e1; 
            padding: 4px 6px; 
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        th { 
            background-color: #f8fafc; 
            font-weight: 700; 
            text-transform: uppercase; 
            font-size: 9.5px; 
            color: #334155; 
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @media (max-width: 640px) {
            body { font-size: 10px; }
            th, td { padding: 3px 4px; font-size: 8.5px; }
            th { font-size: 8px; }
        }

        html.is-modal .no-print-toolbar,
        .is-modal .no-print-toolbar { display: none !important; }
        html.is-modal body,
        .is-modal body { padding: 0.5rem !important; background: transparent !important; }

        /* PRINT STYLES - A4 Portrait with 10 exact pages */
        @media print {
            *, *::before, *::after {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .no-print, .no-print-toolbar { display: none !important; }
            html, body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important; 
                color: #000000 !important; 
                font-size: 9px !important;
                line-height: 1.35 !important;
                width: 100% !important;
            }
            .report-paper {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                border-radius: 0 !important;
            }
            .page-sheet { 
                border: none !important; 
                box-shadow: none !important; 
                padding: 0 !important; 
                margin: 0 !important; 
                max-width: 100% !important; 
                width: 100% !important; 
                min-height: 270mm !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            .page-break { 
                page-break-before: always; 
                break-before: page; 
                padding-top: 5mm; 
            }
            @page { 
                size: A4 portrait; 
                margin: 10mm 12mm; 
            }
            .overflow-x-auto, div[class*="overflow-x-auto"] {
                overflow: visible !important;
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            table {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                table-layout: fixed !important;
                font-size: 8px !important;
                border-collapse: collapse !important;
            }
            th, td {
                padding: 2.5px 3.5px !important;
                font-size: 8px !important;
                word-break: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
            }
            th {
                font-size: 7.5px !important;
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
    <script>
        if (window.self !== window.top || window.location.search.includes('modal=1')) {
            document.documentElement.classList.add('is-modal');
        }
    </script>
</head>
<body class="bg-slate-100 p-2 sm:p-6 md:p-8 antialiased">

    <?php 
    $isModalView = !empty($_GET['modal']) || isset($_GET['preview']) || isset($_GET['popup']);
    if (!$isModalView): 
        $roleCetak = strtolower(session()->get('jabatan') ?? '');
        $backUrlCetak = (strpos($roleCetak, 'review') !== false)
            ? base_url('reviewer/review/' . $kredit['kredit_id'])
            : ((strpos($roleCetak, 'ceo') !== false || strpos($roleCetak, 'direk') !== false || $roleCetak === 'superadmin')
                ? base_url('dashboard')
                : base_url('pengajuan/edit/' . $kredit['kredit_id']));
    ?>
    <!-- Top Action Toolbar (No-Print) -->
    <div id="topActionToolbar" class="max-w-5xl mx-auto mb-4 sm:mb-6 flex flex-wrap items-center justify-between gap-2 no-print no-print-toolbar">
        <a href="<?= $backUrlCetak ?>" class="px-3.5 py-2 bg-white border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-50 transition inline-flex items-center gap-1.5 shadow-2xs">
            &larr; Kembali
        </a>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('pengajuan/unduh-laporan-pdf/' . $kredit['kredit_id']) ?>" class="px-4 py-2 bg-[#0284c7] hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span>Unduh PDF</span>
            </a>
            <button type="button" onclick="window.print()" class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>
    <script>
        if (window.self !== window.top) {
            const tb = document.getElementById('topActionToolbar');
            if (tb) tb.style.display = 'none';
        }
    </script>
    <?php endif; ?>

    <!-- Paper Container (Max width 5xl for full spacious 10-chapter view) -->
    <div class="max-w-5xl mx-auto">
        <?php include APPPATH . 'Views/reviewer/komponen_laporan_10_bab.php'; ?>
    </div>

    <script>
        if (window.location.search.includes('print=1')) {
            window.addEventListener('load', () => {
                setTimeout(() => window.print(), 500);
            });
        }
    </script>
</body>
</html>
