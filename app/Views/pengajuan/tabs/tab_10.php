<?php
$isBu = (isset($view_folder) && $view_folder === 'badan_usaha') 
        || (isset($kredit['jenis_debitur']) && (stripos($kredit['jenis_debitur'], 'badan') !== false || stripos($kredit['jenis_debitur'], 'pt') !== false || stripos($kredit['jenis_debitur'], 'cv') !== false));

if ($isBu) {
    echo $this->include('pengajuan/badan_usaha/tabs/tab_10');
} else {
    echo $this->include('pengajuan/perorangan/tabs/tab_10');
}
