<?php
// client/pages/dashboard_admin.php

// 1. Set judul halaman (unik untuk tiap halaman)
$pageTitle = "Dashboard Admin"; 

// 2. Panggil Header (yang akan memuat auth, config, sidebar, navbar)
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan: 
// Panggil checkLogin dan pastikan rolenya 'admin'.
// Ini akan otomatis me-redirect jika belum login atau rolenya salah.
Auth::checkLogin('admin'); 

// 4. Panggil Client untuk mengambil data
// (Untuk sekarang, kita gunakan data statis)
// require_once '../core/Client.php';
// $client = new Client();
// $totalUsers = count($client->get('users'));
// $totalTugas = count($client->get('tugas'));
// ... (logika ini akan kita implementasikan nanti) ...

// Data statis untuk sekarang:
$totalUsers = 12;
$totalDepartemen = 4;
$totalTugas = 56;
$tugasSelesai = 30;

?>

<div classclass="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-primary p-3 rounded-full">
            <i class="bi bi-people-fill text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total User</h3>
            <p class="text-3xl font-bold"><?php echo $totalUsers; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-500 p-3 rounded-full">
            <i class="bi bi-building text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Departemen</h3>
            <p class="text-3xl font-bold"><?php echo $totalDepartemen; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-500 p-3 rounded-full">
            <i class="bi bi-clipboard-check-fill text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Total Tugas</h3>
            <p class="text-3xl font-bold"><?php echo $totalTugas; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-500 p-3 rounded-full">
            <i class="bi bi-check2-square text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Selesai</h3>
            <p class="text-3xl font-bold"><?php echo $tugasSelesai; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-lg font-semibold mb-4 font-poppins">Progres Tugas</h3>
    <div class="h-80 bg-gray-200 rounded-md flex items-center justify-center">


        [Image of a business bar chart]

        <span class="text-gray-500">
            <i class="bi bi-bar-chart-line-fill text-3xl mr-2"></i>
            Chart akan tampil di sini
        </span>
    </div>
</div>

<?php 
// 5. Panggil Footer
require_once '../layout/footer.php'; 
?>