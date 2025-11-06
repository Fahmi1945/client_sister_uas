<?php
// client/pages/dashboard_karyawan.php

// 1. Set judul halaman
$pageTitle = "Dashboard Karyawan"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan: 
// Pastikan yang login adalah 'karyawan'
Auth::checkLogin('karyawan'); 

// 4. Ambil data user yang sedang login
// Kita akan butuh ID-nya nanti untuk mengambil tugas spesifik
$currentUser = Auth::getUserData();
$userId = $currentUser['id_user'] ?? 0; // Asumsi nama kolomnya 'id_user'

// 5. Ambil data tugas (statis untuk sekarang)
// Nanti kita akan panggil:
// $client = new Client();
// $allMyTasks = $client->get('tugas', '?user_id=' . $userId);
// ... lalu kita filter ...

$tugasBerjalan = [
    ['judul' => 'Desain Homepage', 'deadline' => 'Besok'],
    ['judul' => 'Rapat Client', 'deadline' => '2 hari lagi'],
];
$tugasSelesai = 8;
$tugasBelum = 3;

?>

<h1 class="text-2xl font-poppins font-semibold mb-6">
    Selamat Datang Kembali,
    <span class="text-primary"><?php echo htmlspecialchars($currentUser['nama'] ?? 'Karyawan'); ?>!</span>
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-primary p-3 rounded-full">
            <i class="bi bi-hourglass-split text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Dikerjakan</h3>
            <p class="text-3xl font-bold"><?php echo count($tugasBerjalan); ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-500 p-3 rounded-full">
            <i class="bi bi-check2-circle text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Selesai</h3>
            <p class="text-3xl font-bold"><?php echo $tugasSelesai; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-red-500 p-3 rounded-full">
            <i class="bi bi-x-circle text-white text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Belum</h3>
            <p class="text-3xl font-bold"><?php echo $tugasBelum; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4 font-poppins">
        <i class="bi bi-list-task mr-2"></i>Tugas Aktif Anda
    </h3>

    <div class="space-y-4">
        <?php if (empty($tugasBerjalan)): ?>
        <p class="text-gray-500">
            <i class="bi bi-emoji-sunglasses mr-2"></i>
            Tidak ada tugas aktif. Kerja bagus!
        </p>
        <?php else: ?>
        <?php foreach ($tugasBerjalan as $tugas): ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md border border-gray-200">
            <div>
                <h4 class="font-semibold"><?php echo htmlspecialchars($tugas['judul']); ?></h4>
                <p class="text-sm text-red-600">
                    <i class="bi bi-alarm-fill mr-1"></i>
                    Deadline: <?php echo htmlspecialchars($tugas['deadline']); ?>
                </p>
            </div>
            <a href="/pages/status_tugas/board.php" class="text-primary hover:underline text-sm font-medium">
                Lihat Detail <i class="bi bi-arrow-right-short"></i>
            </a>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<?php 
// 6. Panggil Footer
require_once '../layout/footer.php'; 
?>