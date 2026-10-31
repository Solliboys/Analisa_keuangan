<?php
$pdo = new PDO('mysql:host=localhost;dbname=otw1;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Update kategori_dokumen sesuai nama berkas jika kosong atau 'lainnya'
$stmt = $pdo->query("SELECT dokumen_id, nama_file_asli, kategori_dokumen FROM kredit_dokumen");
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($docs as $d) {
    $fn = strtolower($d['nama_file_asli']);
    $kat = $d['kategori_dokumen'];

    if (empty($kat) || $kat === 'lainnya') {
        if (strpos($fn, 'rekening') !== false) {
            $kat = 'Rekening Koran 3-6 Bulan';
        } elseif (strpos($fn, 'laporan_keuangan') !== false || strpos($fn, 'neraca') !== false) {
            $kat = 'Laporan Keuangan & Pembukuan';
        } elseif (strpos($fn, 'sertifikat') !== false || strpos($fn, 'agunan') !== false || strpos($fn, 'pbb') !== false || strpos($fn, 'imb') !== false) {
            $kat = 'Sertifikat Agunan / PBB / IMB';
        } elseif (strpos($fn, 'whatsapp') !== false || strpos($fn, 'foto') !== false) {
            $kat = 'Foto Lokasi Usaha & Agunan';
        } elseif (strpos($fn, 'result') !== false) {
            $kat = 'Dokumen Pendukung Lainnya';
        } else {
            // Berkas 1.png, foto identitas dsb -> KTP & NPWP
            $kat = 'KTP & NPWP Debitur / Pasangan';
        }

        $up = $pdo->prepare("UPDATE kredit_dokumen SET kategori_dokumen = ? WHERE dokumen_id = ?");
        $up->execute([$kat, $d['dokumen_id']]);
        echo "Dokumen {$d['dokumen_id']} ({$d['nama_file_asli']}) updated to: $kat" . PHP_EOL;
    }
}

$stmt2 = $pdo->query("SELECT dokumen_id, nama_file_asli, kategori_dokumen FROM kredit_dokumen");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
