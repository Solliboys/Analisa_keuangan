<?php
$isBu = (!empty($view_folder) && $view_folder === 'badan_usaha');
if ($isBu) {
    echo $this->include('pengajuan/badan_usaha/tabs/tab_laporan');
} else {
    echo $this->include('pengajuan/perorangan/tabs/tab_laporan');
}

