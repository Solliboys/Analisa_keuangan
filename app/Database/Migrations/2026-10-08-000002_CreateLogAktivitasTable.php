<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitasTable extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `log_aktivitas` (
            `id` int NOT NULL AUTO_INCREMENT,
            `user_id` int DEFAULT NULL,
            `nama_user` varchar(100) NOT NULL,
            `role` varchar(50) NOT NULL,
            `aktivitas` varchar(255) NOT NULL,
            `modul` varchar(50) NOT NULL,
            `status` varchar(20) NOT NULL DEFAULT 'SUCCESS',
            `ip_address` varchar(45) NOT NULL DEFAULT '127.0.0.1',
            `user_agent` text,
            `detail` text,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`),
            KEY `idx_modul` (`modul`),
            KEY `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas', true);
    }
}
