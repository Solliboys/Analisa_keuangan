<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::index');
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/registerProcess', 'Auth::registerProcess');
$routes->get('/auth/logout', 'Auth::logout');

$routes->get('/dashboard', 'Dashboard::index');
$routes->post('/dashboard/update-ai', 'Dashboard::updateAi');
$routes->get('/dashboard/setujui-user/(:num)', 'Dashboard::setujuiUser/$1');
$routes->get('/dashboard/tolak-user/(:num)', 'Dashboard::tolakUser/$1');
$routes->post('/dashboard/update-pengaturan', 'Dashboard::updatePengaturan');
$routes->get('/profil', 'Dashboard::profil');
$routes->post('/profil/update', 'Dashboard::updateProfil');
$routes->get('/log', 'LogAktivitas::index');
$routes->get('/log/download', 'LogAktivitas::download');
$routes->get('/log/detail/(:num)', 'LogAktivitas::detail/$1');

// Modul Pengajuan & Analisa Kredit (CAM)
$routes->group('pengajuan', static function ($routes) {
    $routes->get('/', 'Pengajuan::index');
    $routes->get('download', 'Pengajuan::download');
    $routes->get('baru', 'Pengajuan::baru');
    $routes->get('edit/(:num)', 'Pengajuan::edit/$1');
    $routes->post('simpan', 'Pengajuan::simpan');
    $routes->get('hapus/(:num)', 'Pengajuan::hapus/$1');
    $routes->post('upload-dokumen', 'Pengajuan::uploadDokumen');
    $routes->post('update-kategori-dokumen', 'Pengajuan::updateKategoriDokumen');
    $routes->post('analisa-ai', 'Pengajuan::analisaAi');
    $routes->get('cetak/(:num)', 'Pengajuan::cetak/$1');
});

// Modul Verifikasi SLIK OJK
$routes->group('slik', static function ($routes) {
    $routes->get('verifikasi/(:num)', 'Slik::verifikasi/$1');
    $routes->post('simpan', 'Slik::simpan');
    $routes->post('setujui-pengusul/(:num)', 'Slik::setujuiPengusul/$1');
    $routes->post('setujui-pengajuan-slik/(:num)', 'Slik::setujuiPengajuanSlik/$1');
    $routes->post('toggle-akses-pengusul', 'Slik::toggleAksesPengusul');
});

// Modul Reviewer & Komite Kredit
$routes->group('reviewer', static function ($routes) {
    $routes->get('review/(:num)', 'Reviewer::review/$1');
    $routes->post('simpan-keputusan', 'Reviewer::simpanKeputusan');
    $routes->get('detail-ai/(:num)', 'Reviewer::detailAi/$1');
    $routes->get('detail-review/(:num)', 'Reviewer::detailReview/$1');
    $routes->post('setujui/(:num)', 'Reviewer::setujui/$1');
    $routes->post('setujui', 'Reviewer::setujui');
    $routes->post('tolak/(:num)', 'Reviewer::tolak/$1');
    $routes->post('tolak', 'Reviewer::tolak');
});

// Modul Superadmin
$routes->group('superadmin', static function ($routes) {
    $routes->get('/', 'Superadmin::index');
    $routes->get('simulasi', 'Superadmin::simulasi');
    $routes->get('ai_5c', 'Superadmin::ai_5c');
    $routes->get('review', 'Superadmin::review');
    $routes->get('manajemen_user', 'Superadmin::manajemen_user');
    $routes->post('tambah-user', 'Superadmin::simpanUser');
    $routes->get('toggle-status-user/(:num)', 'Superadmin::toggleStatusUser/$1');
    $routes->get('hapus-user/(:num)', 'Superadmin::hapusUser/$1');
    $routes->post('hapus-user/(:num)', 'Superadmin::hapusUser/$1');
    $routes->post('simpan-pengaturan-ai', 'Superadmin::simpanPengaturanAi');
    $routes->get('pengaturan', 'Superadmin::pengaturan');
    $routes->get('hapus-simulasi/(:num)', 'Superadmin::hapusSimulasi/$1');
    $routes->post('hapus-simulasi/(:num)', 'Superadmin::hapusSimulasi/$1');
    $routes->get('log', 'Superadmin::log');
    $routes->get('log/download', 'Superadmin::downloadLog');
    $routes->get('log/detail/(:num)', 'Superadmin::detailLog/$1');
});


