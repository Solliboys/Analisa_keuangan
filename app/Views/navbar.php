<?php
/**
 * Sidebar Navigation View untuk Superadmin
 * Variabel pendukung:
 * - $active_menu (string): 'simulasi', 'ai_5c', 'users', 'pengaturan'
 * - $nama_lengkap (string)
 * - $jabatan (string)
 */
// Deteksi menu aktif secara otomatis dari URI jika belum diset
$current_uri = function_exists('uri_string') ? uri_string() : '';

if (empty($active_menu)) {
    if (str_contains($current_uri, 'ai_5c') || str_contains($current_uri, 'review')) {
        $active_menu = 'ai_5c';
    } elseif (str_contains($current_uri, 'user')) {
        $active_menu = 'users';
    } elseif (str_contains($current_uri, 'pengaturan')) {
        $active_menu = 'pengaturan';
    } else {
        $active_menu = 'simulasi';
    }
} else {
    // Jika active_menu default simulasi tapi sedang membuka ai_5c/users/pengaturan
    if ($active_menu === 'simulasi') {
        if (str_contains($current_uri, 'ai_5c') || str_contains($current_uri, 'review')) {
            $active_menu = 'ai_5c';
        } elseif (str_contains($current_uri, 'user')) {
            $active_menu = 'users';
        } elseif (str_contains($current_uri, 'pengaturan')) {
            $active_menu = 'pengaturan';
        }
    }
}

$current_name = $nama_lengkap ?? session()->get('nama_lengkap') ?? 'Budi Santoso';
$current_role = $jabatan ?? session()->get('jabatan') ?? 'Senior Credit Analyst';

// Hitung inisial nama
$words = explode(' ', trim($current_name));
$initials = '';
foreach (array_slice($words, 0, 2) as $w) {
    $initials .= strtoupper(substr($w, 0, 1));
}
if (empty($initials)) {
    $initials = 'BS';
}
?>

<!-- SIDEBAR NAVIGATION -->
<aside id="superadminSidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200/90 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full md:translate-x-0">
    
    <!-- Top Part: Logo & Menu -->
    <div class="p-5 flex flex-col h-full overflow-y-auto">
        
        <!-- App Logo & Brand -->
        <div class="flex items-center justify-between pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <!-- Shield Check Icon -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <div class="font-extrabold text-[17px] tracking-tight text-slate-900 leading-none">
                        OTW <span class="text-blue-600">Kredit</span>
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium tracking-tight mt-1">
                        Sistem Analisa Kredit
                    </div>
                </div>
            </div>

            <!-- Close button for mobile -->
            <button type="button" onclick="toggleSidebar()" class="md:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Section -->
        <div class="mt-6 flex-1">
            <div class="text-[11px] font-bold tracking-wider text-slate-400 px-3 uppercase mb-3">
                MENU UTAMA
            </div>
            
            <nav class="space-y-1.5">
                <!-- Simulasi Kredit -->
                <a href="<?= base_url('superadmin/simulasi') ?>" 
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($active_menu === 'simulasi') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <svg class="w-5 h-5 <?= ($active_menu === 'simulasi') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' ?> transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Simulasi Kredit</span>
                </a>

                <!-- AI 5C Analysis -->
                <a href="<?= base_url('superadmin/ai_5c') ?>" 
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($active_menu === 'ai_5c') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <svg class="w-5 h-5 <?= ($active_menu === 'ai_5c') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' ?> transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>AI 5C Analysis</span>
                </a>

                <!-- Manajemen User -->
                <a href="<?= base_url('superadmin/manajemen_user') ?>" 
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($active_menu === 'users') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <svg class="w-5 h-5 <?= ($active_menu === 'users') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' ?> transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Manajemen User</span>
                </a>

                <!-- Pengaturan -->
                <a href="<?= base_url('superadmin/pengaturan') ?>" 
                   class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 <?= ($active_menu === 'pengaturan') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <svg class="w-5 h-5 <?= ($active_menu === 'pengaturan') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' ?> transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </div>
    </div>

    <!-- Bottom Part: User Profile Card -->
    <div class="p-4 border-t border-slate-100 bg-white">


        <!-- User Profile Pill -->
        <div class="relative">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 hover:bg-slate-100/70 transition-colors">
                <div class="flex items-center gap-2.5 min-w-0">
                    <!-- Avatar circle with initials -->
                    <div class="w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 tracking-tight">
                        <?= esc($initials) ?>
                    </div>
                    <div class="min-w-0 truncate">
                        <div class="text-xs font-bold text-slate-900 truncate" title="<?= esc($current_name) ?>">
                            <?= esc($current_name) ?>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium truncate" title="<?= esc($current_role) ?>">
                            <?= esc($current_role) ?>
                        </div>
                    </div>
                </div>

                <!-- Notification Bell Icon with Red Dot -->
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" onclick="toggleNotificationDropdown()" class="relative p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-white transition" title="Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
                    </button>
                    <!-- Logout Quick Action -->
                    <a href="<?= base_url('auth/logout') ?>" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop overlay for mobile drawer -->
<div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-30 hidden md:hidden transition-opacity"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('superadminSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (!sidebar) return;
        
        const isClosed = sidebar.classList.contains('-translate-x-full');
        if (isClosed) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }

    function handleImportModal() {
        const modal = document.getElementById('importModal');
        if (modal) {
            modal.classList.remove('hidden');
        } else {
            alert('Fitur Import File Kalkulasi Simulasi (Excel / CSV). Silakan pilih berkas.');
        }
    }

    function handleSaveAction() {
        // Notification toast feedback
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-xs font-medium animate-bounce';
        toast.innerHTML = `
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Perubahan dan draf simulasi berhasil disimpan!</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function toggleNotificationDropdown() {
        alert('Tidak ada notifikasi baru yang belum dibaca.');
    }
</script>
