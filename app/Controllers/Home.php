<?php

namespace App\Controllers;

use Config\Database;

class Home extends BaseController
{
    public function index(): string
    {
        $db = Database::connect();
        
        // Ambil pengaturan sistem dari database
        $pengaturanSistem = $db->table('pengaturan_sistem')->where('id', 1)->get()->getRowArray() ?? [];
        
        // Ambil pengaturan AI
        $pengaturanAi = $db->table('pengaturan_ai')->where('id', 1)->get()->getRowArray() ?? [];

        // Data statistik agregat untuk live showcase
        $totalKredit = $db->table('kredit')->countAllResults();
        $totalPlafonRow = $db->table('kredit')->selectSum('plafon_pengajuan')->get()->getRowArray();
        $totalPlafon = (float)($totalPlafonRow['plafon_pengajuan'] ?? 48500000000);

        $data = [
            'pengaturan_sistem' => $pengaturanSistem,
            'pengaturan_ai'     => $pengaturanAi,
            'total_kredit'      => $totalKredit,
            'total_plafon'      => $totalPlafon,
        ];

        return view('welcome_message', $data);
    }
}
