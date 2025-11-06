<?php
// client/pages/dashboard_admin.php

// 1. Set judul halaman (digunakan oleh header.php dan navbar.php)
$pageTitle = "Dashboard Admin"; 

// 2. Panggil Header (Ini akan memuat semua layout, config, dan Auth)
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan: Pastikan hanya 'admin' yang bisa mengakses
Auth::checkLogin('admin'); 

// Data statis untuk ditampilkan di Card:
$totalUsers = 15;
$totalDepartemen = 5;
$totalTugas = 75;
$tugasSelesai = 45;

?>

<h1 class="text-3xl font-poppins font-bold text-gray-800 mb-8">
    Ringkasan Sistem
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-primary">
        <div class="bg-primary/10 p-3 rounded-full">
            <i class="bi bi-people-fill text-primary text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total User</h3>
            <p class="text-3xl font-bold"><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-green-500">
        <div class="bg-green-500/10 p-3 rounded-full">
            <i class="bi bi-building text-green-500 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Departemen</h3>
            <p class="text-3xl font-bold"><?php echo $totalDepartemen; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-yellow-500">
        <div class="bg-yellow-500/10 p-3 rounded-full">
            <i class="bi bi-clipboard-check-fill text-yellow-500 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Tugas</h3>
            <p class="text-3xl font-bold"><?php echo $totalTugas; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-blue-500">
        <div class="bg-blue-500/10 p-3 rounded-full">
            <i class="bi bi-check2-square text-blue-500 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Selesai</h3>
            <p class="text-3xl font-bold"><?php echo $tugasSelesai; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-lg font-semibold mb-4 font-poppins text-gray-800">Progres Tugas Mingguan</h3>
    <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">


        [Image of a business bar chart]

        <span class="text-gray-500 text-lg">
            <i class="bi bi-bar-chart-line-fill text-3xl mr-2"></i>
            Area Placeholder untuk Chart (Perlu JavaScript seperti Chart.js)
        </span>
    </div>
</div>

<?php 
// 4. Panggil Footer
require_once '../layout/footer.php'; 
?>