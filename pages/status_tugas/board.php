<?php
// client/pages/status_tugas/board.php

// 1. Set judul halaman
$pageTitle = "Papan Tugas Saya"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (WAJIB) Keamanan: Pastikan 'karyawan'
Auth::checkLogin('karyawan'); 
$client = new Client();

// 4. Ambil ID Karyawan yang sedang login
$currentUserId = Auth::getUserData()['id_user'] ?? 0;

// 5. Ambil data
$allTugas = $client->get('tugas');
$allUsers = $client->get('users'); // Untuk mencari nama Admin pembuat

// 6. Buat User Map (untuk cari nama Admin)
$userMap = [];
if (is_array($allUsers)) {
    foreach ($allUsers as $user) {
        $userMap[$user['id_user']] = $user['nama_lengkap'];
    }
}

// 7. Filter tugas HANYA untuk user ini
$myTasks = [];
if (is_array($allTugas)) {
    $myTasks = array_filter($allTugas, function($tugas) use ($currentUserId) {
        return $tugas['id_user_penerima'] == $currentUserId;
    });
}

// 8. Pisahkan tugas ke 3 kolom
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

foreach ($myTasks as $tugas) {
    if ($tugas['status'] == 'Proses') {
        $tugasProses[] = $tugas;
    } elseif ($tugas['status'] == 'Selesai') {
        $tugasSelesai[] = $tugas;
    } else {
        // Anggap 'Belum' sebagai default
        $tugasBelum[] = $tugas;
    }
}
?>

<div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0">

    <div class="flex-1 bg-gray-100 p-4 rounded-lg border">
        <h2 class="text-lg font-poppins font-semibold mb-4 text-yellow-700">
            <i class="bi bi-pause-circle-fill mr-2"></i>
            Belum Dikerjakan (<?php echo count($tugasBelum); ?>)
        </h2>
        <div class="space-y-4">
            <?php if (empty($tugasBelum)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Tidak ada tugas.</p>
            <?php endif; ?>
            <?php foreach ($tugasBelum as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-1 bg-gray-100 p-4 rounded-lg border">
        <h2 class="text-lg font-poppins font-semibold mb-4 text-primary">
            <i class="bi bi-play-circle-fill mr-2"></i>
            Sedang Dikerjakan (<?php echo count($tugasProses); ?>)
        </h2>
        <div class="space-y-4">
            <?php if (empty($tugasProses)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Tidak ada tugas.</p>
            <?php endif; ?>
            <?php foreach ($tugasProses as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-1 bg-gray-100 p-4 rounded-lg border">
        <h2 class="text-lg font-poppins font-semibold mb-4 text-green-600">
            <i class="bi bi-check-circle-fill mr-2"></i>
            Selesai (<?php echo count($tugasSelesai); ?>)
        </h2>
        <div class="space-y-4">
            <?php if (empty($tugasSelesai)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Tidak ada tugas.</p>
            <?php endif; ?>
            <?php foreach ($tugasSelesai as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php 
// 9. Panggil Footer
require_once '../layout/footer.php'; 
?>