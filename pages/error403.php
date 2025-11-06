<?php
// client/pages/error403.php

// 1. Set judul halaman
$pageTitle = "Akses Ditolak (403)"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan:
// Kita hanya perlu memastikan mereka login. 
// JANGAN cek role, karena mereka di sini KARENA role-nya salah.
Auth::checkLogin(); 

// 4. Tentukan URL dashboard yang benar untuk tombol "Kembali"
$dashboardUrl = '/pages/dashboard_karyawan.php'; // Default
if (Auth::getRole() == 'admin') {
    // Jika admin entah bagaimana terdampar di sini
    $dashboardUrl = '/pages/dashboard_admin.php';
}
?>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-md text-center max-w-2xl mx-auto">

    <div class="text-red-500 mb-4">
        <i class="bi bi-shield-lock-fill" style="font-size: 6rem;"></i>
    </div>

    <h1 class="text-6xl font-poppins font-bold text-red-600">403</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mt-4 mb-2">Akses Ditolak</h2>

    <p class="text-gray-600 mb-8 text-lg">
        Anda tidak memiliki izin atau hak akses untuk melihat halaman yang Anda minta.
    </p>

    <a href="<?php echo $dashboardUrl; ?>"
        class="bg-primary hover:bg-primary-hover text-white font-bold py-3 px-6 rounded-lg flex items-center justify-center space-x-2 transition-colors max-w-xs mx-auto text-lg">
        <i class="bi bi-house-door-fill"></i>
        <span>Kembali ke Dashboard</span>
    </a>
</div>

<?php 
// 5. Panggil Footer
require_once '../layout/footer.php'; 
?>