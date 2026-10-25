<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Auth::index');
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

// Modul Pengajuan & Analisa Kredit (CAM)
$routes->group('pengajuan', static function ($routes) {
    $routes->get('/', 'Pengajuan::index');
    $routes->get('baru', 'Pengajuan::baru');
    $routes->get('edit/(:num)', 'Pengajuan::edit/$1');
    $routes->post('simpan', 'Pengajuan::simpan');
    $routes->get('hapus/(:num)', 'Pengajuan::hapus/$1');
    $routes->post('upload-dokumen', 'Pengajuan::uploadDokumen');
    $routes->post('analisa-ai', 'Pengajuan::analisaAi');
    $routes->get('cetak/(:num)', 'Pengajuan::cetak/$1');
});
