<?php
$rawJd = $kredit['jenis_debitur'] ?? ($jenis_debitur ?? 'Perorangan');
$folder = (stripos($rawJd, 'badan') !== false || stripos($rawJd, 'pt') !== false || stripos($rawJd, 'cv') !== false) ? 'badan_usaha' : 'perorangan';
echo view('pengajuan/' . $folder . '/form', get_defined_vars());

