<?php
// client/layout/sidebar.php

// Kita perlu tahu role pengguna untuk menampilkan menu yang tepat
// Variabel $userRole sudah ada dari header.php, 
// tapi kita bisa panggil lagi untuk memastikan file ini mandiri
$userRole = Auth::getRole();
?>

<aside class="w-64 bg-primary text-white flex-shrink-0 flex flex-col">

    <div class="h-16 flex items-center justify-center px-4 shadow-md">
        <img src="/assets/img/logo.png" alt="Logo" class="h-8 w-auto mr-3">
        <span class="text-xl font-poppins font-semibold">Manajemen</span>
    </div>

    <nav class="flex-1 p-4 space-y-2">

        <a href="<?php echo ($userRole == 'admin') ? '/pages/dashboard_admin.php' : '/pages/dashboard_karyawan.php'; ?>"
            class="flex items-center space-x-4 px-4 py-2 rounded-md hover:bg-primary-hover transition-colors">
            <i class="bi bi-pie-chart-fill text-lg"></i>
            <span>Dashboard</span>
        </a>

        <?php // === MENU KHUSUS ADMIN === ?>
        <?php if ($userRole == 'admin'): ?>
        <a href="/pages/users/list.php"
            class="flex items-center space-x-4 px-4 py-2 rounded-md hover:bg-primary-hover transition-colors">
            <i class="bi bi-people-fill text-lg"></i>
            <span>Manajemen User</span>
        </a>
        <a href="/pages/departemen/list.php"
            class="flex items-center space-x-4 px-4 py-2 rounded-md hover:bg-primary-hover transition-colors">
            <i class="bi bi-building text-lg"></i>
            <span>Manajemen Departemen</span>
        </a>
        <a href="/pages/tugas/list.php"
            class="flex items-center space-x-4 px-4 py-2 rounded-md hover:bg-primary-hover transition-colors">
            <i class="bi bi-clipboard-check-fill text-lg"></i>
            <span>Manajemen Tugas</span>
        </a>
        <?php endif; ?>

        <?php // === MENU KHUSUS KARYAWAN === ?>
        <?php if ($userRole == 'karyawan'): ?>
        <a href="/pages/status_tugas/board.php"
            class="flex items-center space-x-4 px-4 py-2 rounded-md hover:bg-primary-hover transition-colors">
            <i class="bi bi-check2-square text-lg"></i>
            <span>Status Tugas Saya</span>
        </a>
        <?php endif; ?>

    </nav>
</aside>