<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Memastikan struktur tabel dan baris pengaturan_sistem lengkap (Auto-heal untuk VPS)
     */
    protected function ensurePengaturanSistemSchema($db = null)
    {
        try {
            $db = $db ?: \Config\Database::connect();
            $existingCols = $db->getFieldNames('pengaturan_sistem');
            $neededCols = [
                'slogan_aplikasi'        => "VARCHAR(100) NULL DEFAULT 'Sistem Analisa Kredit'",
                'alamat_lembaga'         => "VARCHAR(255) NULL DEFAULT 'Financial Center, Lantai 8, Jakarta Selatan'",
                'email_lembaga'          => "VARCHAR(100) NULL DEFAULT 'info@otwkredit.id'",
                'telepon_lembaga'        => "VARCHAR(50) NULL DEFAULT '(021) 5088-2900'",
                'nama_direktur'          => "VARCHAR(150) NULL DEFAULT 'Dr. H. Hendra Wijaya, S.E., M.M.'",
                'jabatan_direktur'       => "VARCHAR(100) NULL DEFAULT 'Direktur Utama / CEO'",
                'petugas_slik_aktif'     => "TINYINT(1) NULL DEFAULT 1",
                'ijin_pengusul_isi_slik' => "TINYINT(1) NULL DEFAULT 0",
                'prefix_nomor_aplikasi'  => "VARCHAR(20) NULL DEFAULT 'CAM'",
            ];

            foreach ($neededCols as $col => $colDef) {
                if (!in_array($col, $existingCols, true)) {
                    $db->query("ALTER TABLE `pengaturan_sistem` ADD COLUMN `{$col}` {$colDef}");
                }
            }

            // Pastikan baris pengaturan minimal 1 baris
            $firstRow = $db->table('pengaturan_sistem')->get()->getFirstRow('array');
            if (!$firstRow) {
                $db->table('pengaturan_sistem')->insert([
                    'id'                     => 1,
                    'nama_aplikasi'          => 'OTW Kredit',
                    'slogan_aplikasi'        => 'Sistem Analisa Kredit',
                    'nama_lembaga'           => 'PT Lembaga Pembiayaan',
                    'alamat_lembaga'         => 'Financial Center, Lantai 8, Jakarta Selatan',
                    'email_lembaga'          => 'info@otwkredit.id',
                    'telepon_lembaga'        => '(021) 5088-2900',
                    'petugas_slik_aktif'     => 1,
                    'ijin_pengusul_isi_slik' => 0,
                    'prefix_nomor_aplikasi'  => 'CAM',
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Auto-heal pengaturan_sistem error: ' . $e->getMessage());
        }
    }
}
