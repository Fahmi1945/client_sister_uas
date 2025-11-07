<?php
// client/pages/dashboard_admin.php

// 1. Definisikan Judul Halaman
$pageTitle = "Dashboard Admin";

// 2. Sertakan Header (otomatis memuat Auth, Helper, dan memulai sesi)
// Pastikan path-nya benar (dari /pages/ ke /layout/)
require_once '../../layout/header.php';

// 3. Keamanan: Cek apakah user sudah login dan rolenya 'admin'
Auth::checkLogin('admin'); 
?>

<div class="space-y-6">

    <h2 class="text-2xl font-bold text-gray-800">Selamat Datang,
        <?php echo htmlspecialchars($currentUser['nama_lengkap'] ?? 'Admin'); ?>!</h2>
    <p class="text-gray-600">Ini adalah ringkasan sistem manajemen tugas untuk Administrator.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-xl shadow-lg border border-primary-light">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total User</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">25</p>
                </div>
                <i class="bi bi-people-fill text-4xl text-primary"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg border border-primary-light">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Departemen</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">5</p>
                </div>
                <i class="bi bi-building text-4xl text-primary"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg border border-primary-light">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tugas Belum Selesai</p>
                    <p class="text-3xl font-bold text-red-600 mt-1">12</p>
                </div>
                <i class="bi bi-clock-history text-4xl text-red-500"></i>
            </div>
        </div>

    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Aktivitas Terbaru</h3>
        <p class="text-gray-500">Di sini akan tampil log aktivitas atau tugas terbaru yang dikerjakan karyawan.</p>
    </div>
</div>

<?php 
// 4. Sertakan Footer (menutup tag HTML)
require_once '../layout/footer.php'; 
?>