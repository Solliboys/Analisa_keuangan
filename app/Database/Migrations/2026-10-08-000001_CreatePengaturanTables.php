<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanTables extends Migration
{
    public function up()
    {
        // 1. Tabel pengaturan_sistem
        $this->db->query("CREATE TABLE IF NOT EXISTS `pengaturan_sistem` (
            `id` int NOT NULL AUTO_INCREMENT,
            `nama_aplikasi` varchar(100) NOT NULL DEFAULT 'OTW Credit Engine & CAM',
            `slogan_aplikasi` varchar(100) DEFAULT 'Sistem Analisa Kredit',
            `nama_lembaga` varchar(150) NOT NULL DEFAULT 'PT BPR / Lembaga Pembiayaan',
            `logo_aplikasi` varchar(255) DEFAULT NULL,
            `api_key` varchar(255) DEFAULT NULL,
            `model_ai` varchar(50) NOT NULL DEFAULT 'gemini-3.8-flash',
            `api_endpoint` varchar(255) NOT NULL DEFAULT 'https://generativelanguage.googleapis.com/v1beta/models',
            `prompt_sistem` text,
            `ijin_pengusul_isi_slik` tinyint(1) DEFAULT '1',
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `alamat_lembaga` varchar(255) DEFAULT 'BPR Financial Center, Lantai 8, Jakarta Selatan',
            `email_lembaga` varchar(100) DEFAULT 'info@otwkredit.id',
            `telepon_lembaga` varchar(50) DEFAULT '(021) 5088-2900',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        // 2. Tabel pengaturan_ai
        $this->db->query("CREATE TABLE IF NOT EXISTS `pengaturan_ai` (
            `id` int NOT NULL AUTO_INCREMENT,
            `api_key` varchar(255) NOT NULL,
            `model_ai` varchar(50) NOT NULL DEFAULT 'deepseek-chat',
            `prompt_character` text NOT NULL,
            `prompt_capacity` text NOT NULL,
            `prompt_capital` text NOT NULL,
            `prompt_collateral` text NOT NULL,
            `prompt_condition` text NOT NULL,
            `prompt_final` text NOT NULL,
            `diubah_oleh` int DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `diubah_oleh` (`diubah_oleh`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan_sistem', true);
        $this->forge->dropTable('pengaturan_ai', true);
    }
}
