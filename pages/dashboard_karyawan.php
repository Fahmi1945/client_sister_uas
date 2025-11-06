<?php
// client/pages/dashboard_karyawan.php

// 1. Set judul halaman
$pageTitle = "Dashboard Karyawan"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan: Pastikan 'karyawan' yang login
Auth::checkLogin('karyawan'); 

$currentUser = Auth::getUserData();
$currentUserName = htmlspecialchars($currentUser['nama'] ?? 'Karyawan');

// Data statis untuk ditampilkan (akan diganti dengan data API nanti)
$tugasAktif = 3;
$tugasSelesaiTotal = 18;
$reminderTugas = [
    ['judul' => 'Revisi Desain Halaman', 'deadline' => 'Besok, 10:00'],
    ['judul' => 'Laporan Mingguan', 'deadline' => '2 hari lagi'],
];

?>

<h1 class="text-2xl font-poppins font-semibold mb-6">
    Selamat Bekerja,
    <span class="text-primary"><?php echo $currentUserName; ?>!</span>
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-primary">
        <div class="bg-primary/10 p-3 rounded-full">
            <i class="bi bi-hourglass-split text-primary text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Aktif</h3>
            <p class="text-3xl font-bold"><?php echo $tugasAktif; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-green-500">
        <div class="bg-green-500/10 p-3 rounded-full">
            <i class="bi bi-check2-circle text-green-500 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-gray-500 text-sm font-medium">Tugas Selesai (Total)</h3>
            <p class="text-3xl font-bold"><?php echo $tugasSelesaiTotal; ?></p>
        </div>
    </div>

    <a href="<?php echo BASE_URL; ?>pages/status_tugas/board.php" class="block">
        <div
            class="bg-white p-6 rounded-xl shadow-lg flex items-center space-x-4 border-l-4 border-gray-400 hover:bg-gray-50 transition-colors h-full">
            <div class="bg-gray-400/10 p-3 rounded-full">
                <i class="bi bi-list-task text-gray-600 text-2xl"></i>
            </div>
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Lihat Semua Tugas</h3>
                <p class="text-lg font-bold text-primary hover:underline">Akses Papan Tugas</p>
            </div>
        </div>
    </a>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <h3 class="text-lg font-semibold mb-4 font-poppins text-gray-800 flex items-center">
        <i class="bi bi-bell-fill text-red-500 mr-2"></i>
        Reminder Deadline Mendekat
    </h3>

    <div class="space-y-3">
        <?php if (empty($reminderTugas)): ?>
        <p class="text-gray-500 italic">Tidak ada tugas dengan deadline mendesak.</p>
        <?php else: ?>
        <?php foreach ($reminderTugas as $tugas): ?>
        <div class="p-3 bg-red-50 border border-red-200 rounded-md flex justify-between items-center">
            <div>
                <h4 class="font-semibold text-red-800"><?php echo htmlspecialchars($tugas['judul']); ?></h4>
            </div>
            <span class="text-sm font-medium text-red-600">
                <?php echo htmlspecialchars($tugas['deadline']); ?>
            </span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
// 4. Panggil Footer
require_once '../layout/footer.php'; 
?>