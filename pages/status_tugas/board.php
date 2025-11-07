<?php
// client/pages/status_tugas/board.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

// Halaman ini HANYA untuk Karyawan
Auth::checkLogin('karyawan'); 

// 2. Ambil ID Karyawan yang sedang login
$currentUserId = Auth::getUserData()['id_user'] ?? 0;
$client = new Client();

// 3. Ambil data Tugas dan User (untuk pembuat)
$allTugas = $client->get('tugas'); // Semua tugas
$allUsers = $client->get('users'); 

// 4. Buat User Map (id_user -> nama) untuk menampilkan Pembuat Tugas
$userMap = [];
if (is_array($allUsers) && !isset($allUsers['status'])) {
    foreach ($allUsers as $user) {
        $userMap[$user['id_user']] = $user['nama']; // Gunakan 'nama' sesuai tabel
    }
}

// 5. Filter tugas HANYA untuk user ini dan pisahkan ke 3 kolom
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        // Asumsi relasi user ada di kolom tugas.id_user (atau tugas.id_pembuat)
        // KARENA INI UNTUK KARYAWAN, kita perlu tahu tugas mana yang DITUGASKAN KEPADANYA.
        // Asumsi: Kita asumsikan kolom penerima tugas di tabel 'tugas' adalah id_user.
        // Jika tugas.id_pembuat adalah pembuat (admin), ini tidak cukup.
        // KARENA STRUKTUR TUGAS ANDA TIDAK ADA ID PENERIMA, kita akan pakai TUGAS.id_pembuat SEMENTARA.
        // JIKA 'id_pembuat' di tugas adalah penerima, gunakan:
        if ($tugas['id_pembuat'] == $currentUserId) { 
             $status = strtolower($tugas['status'] ?? 'belum');

             if ($status == 'proses') {
                 $tugasProses[] = $tugas;
             } elseif ($status == 'selesai') {
                 $tugasSelesai[] = $tugas;
             } else {
                 $tugasBelum[] = $tugas;
             }
        }
    }
}

$pageTitle = "Papan Tugas Saya";
require_once '../../layout/header.php';
?>

<h1 class="text-3xl font-poppins font-bold text-gray-800 mb-6">
    Papan Tugas Karyawan
</h1>

<div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-yellow-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-yellow-700 border-b pb-2">
            <i class="bi bi-clock-history mr-2"></i>
            To Do (Belum) (<?php echo count($tugasBelum); ?>)
        </h2>
        <div class="space-y-4 h-full min-h-[300px]">
            <?php if (empty($tugasBelum)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Tidak ada tugas baru saat ini.</p>
            <?php endif; ?>
            <?php foreach ($tugasBelum as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-primary-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-primary border-b pb-2">
            <i class="bi bi-play-circle-fill mr-2"></i>
            In Progress (Proses) (<?php echo count($tugasProses); ?>)
        </h2>
        <div class="space-y-4 h-full min-h-[300px]">
            <?php if (empty($tugasProses)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Belum ada tugas yang sedang dikerjakan.</p>
            <?php endif; ?>
            <?php foreach ($tugasProses as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-green-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-green-700 border-b pb-2">
            <i class="bi bi-check-circle-fill mr-2"></i>
            Done (Selesai) (<?php echo count($tugasSelesai); ?>)
        </h2>
        <div class="space-y-4 h-full min-h-[300px]">
            <?php if (empty($tugasSelesai)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Anda belum menyelesaikan tugas apapun.</p>
            <?php endif; ?>
            <?php foreach ($tugasSelesai as $tugas): ?>
            <?php include 'card_tugas.php'; // Panggil template card ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php 
require_once '../../layout/footer.php';
?>