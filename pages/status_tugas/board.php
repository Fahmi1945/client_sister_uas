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

// 3. Ambil data dari API
$allTugas = $client->get('tugas'); // Semua tugas
$allUsers = $client->get('users'); // Semua user (untuk pembuat)
$allStatusTugas = $client->get('status_tugas'); // Semua status_tugas

// 4. Buat User Map (id_user -> nama)
$userMap = [];
if (is_array($allUsers) && !isset($allUsers['status'])) {
    foreach ($allUsers as $user) {
        $userMap[$user['id_user']] = $user['nama_lengkap']; // Menggunakan nama_lengkap
    }
}

// 5. Buat Status Map (id_tugas -> status data) untuk user yang sedang login
// Status yang ditampilkan adalah status terakhir tugas tersebut oleh karyawan yang sedang login
$statusMap = [];
if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    foreach ($allStatusTugas as $status) {
        // Ambil hanya status untuk user yang sedang login
        if ($status['id_user'] == $currentUserId) {
             // Simpan data status dengan id_tugas sebagai key
            $statusMap[$status['id_tugas']] = [
                'status' => $status['status'],
                'catatan' => $status['catatan'],
                'updated_at' => $status['updated_at']
            ];
        }
    }
}

// 6. Pisahkan tugas berdasarkan status untuk user saat ini
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        // Gabungkan status dari statusMap ke data tugas
        // Default status adalah 'belum', jika ada di statusMap, gunakan yang itu
        $tugas['status_current'] = $statusMap[$tugas['id_tugas']]['status'] ?? 'belum';
        $tugas['catatan'] = $statusMap[$tugas['id_tugas']]['catatan'] ?? '';
        $tugas['updated_at'] = $statusMap[$tugas['id_tugas']]['updated_at'] ?? '';

        // Hanya tampilkan tugas yang ditujukan untuk user saat ini
        if ($tugas['id_user_penerima'] == $currentUserId) {
            $status = strtolower($tugas['status_current']);
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

// Set Judul Halaman
$pageTitle = "Board Tugas Saya";

// Muat Header
include '../../layout/header.php';
?>

<div class="space-y-6">

    <h1 class="text-3xl font-poppins font-bold text-gray-800">Board Tugas Saya</h1>

    <div class="flex flex-col lg:flex-row space-y-6 lg:space-y-0 lg:space-x-6">

        <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-yellow-200">
            <h2 class="text-xl font-poppins font-bold mb-4 text-yellow-700 border-b pb-2">
                <i class="bi bi-clock-fill mr-2"></i>
                To Do (Belum) <span class="text-sm font-normal">(<?php echo count($tugasBelum); ?>)</span>
            </h2>
            <div class="space-y-4 h-full min-h-[300px]">
                <?php if (empty($tugasBelum)): ?>
                <p class="text-sm text-gray-500 italic p-4 text-center">Belum ada tugas yang harus dikerjakan.</p>
                <?php else: ?>
                <?php foreach ($tugasBelum as $tugas): ?>
                <?php include 'card_tugas.php'; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-blue-200">
            <h2 class="text-xl font-poppins font-bold mb-4 text-blue-700 border-b pb-2">
                <i class="bi bi-play-circle-fill mr-2"></i>
                In Progress (Proses) <span class="text-sm font-normal">(<?php echo count($tugasProses); ?>)</span>
            </h2>
            <div class="space-y-4 h-full min-h-[300px]">
                <?php if (empty($tugasProses)): ?>
                <p class="text-sm text-gray-500 italic p-4 text-center">Belum ada tugas yang sedang dikerjakan.</p>
                <?php else: ?>
                <?php foreach ($tugasProses as $tugas): ?>
                <?php include 'card_tugas.php'; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-green-200">
            <h2 class="text-xl font-poppins font-bold mb-4 text-green-700 border-b pb-2">
                <i class="bi bi-check-circle-fill mr-2"></i>
                Done (Selesai) <span class="text-sm font-normal">(<?php echo count($tugasSelesai); ?>)</span>
            </h2>
            <div class="space-y-4 h-full min-h-[300px]">
                <?php if (empty($tugasSelesai)): ?>
                <p class="text-sm text-gray-500 italic p-4 text-center">Belum ada tugas yang selesai.</p>
                <?php else: ?>
                <?php foreach ($tugasSelesai as $tugas): ?>
                <?php include 'card_tugas.php'; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<?php
// Muat Footer
include '../../layout/footer.php';
?>