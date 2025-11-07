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
        $userMap[$user['id_user']] = $user['nama'];
    }
}

// 5. Buat Status Map (id_tugas -> status data) untuk user yang sedang login
$statusMap = [];
if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    foreach ($allStatusTugas as $status) {
        // Hanya ambil status milik user yang login
        if ($status['id_user'] == $currentUserId) {
            $statusMap[$status['id_tugas']] = $status;
        }
    }
}

// 6. Filter dan Gabungkan tugas dengan status, lalu pisahkan ke 3 kolom
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        $id_tugas = $tugas['id_tugas'];
        
        // Cek apakah ada record status_tugas untuk user ini dan tugas ini
        if (isset($statusMap[$id_tugas])) {
            $statusData = $statusMap[$id_tugas];
            $status = strtolower($statusData['status'] ?? 'belum');
            
            // Gabungkan data tugas dengan status
            $tugas['status_current'] = $status;
            $tugas['catatan'] = $statusData['catatan'] ?? '';
            $tugas['updated_at'] = $statusData['updated_at'] ?? '';
            $tugas['id_status'] = $statusData['id_status'] ?? 0;
            
            // Pisahkan berdasarkan status
            if ($status == 'proses') {
                $tugasProses[] = $tugas;
            } elseif ($status == 'selesai') {
                $tugasSelesai[] = $tugas;
            } else {
                $tugasBelum[] = $tugas;
            }
        }
        // OPTIONAL: Jika ingin menampilkan tugas yang belum ada di status_tugas sama sekali
        // (tugas baru yang belum pernah diambil oleh karyawan)
        // Uncomment kode berikut:
        /*
        else {
            // Tugas yang belum pernah di-claim oleh user ini
            $tugas['status_current'] = 'belum';
            $tugas['catatan'] = '';
            $tugas['updated_at'] = '';
            $tugas['id_status'] = 0;
            $tugasBelum[] = $tugas;
        }
        */
    }
}

$pageTitle = "Papan Tugas Saya";
require_once '../../layout/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-poppins font-bold text-gray-800">
        Papan Tugas Karyawan
    </h1>
    <p class="text-sm text-gray-600 mt-1">
        Kelola status tugas Anda:
        <strong><?php echo htmlspecialchars(Auth::getUserData()['nama'] ?? 'Pengguna'); ?></strong>
    </p>
</div>

<div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

    <!-- Kolom: To Do (Belum) -->
    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-yellow-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-yellow-700 border-b pb-2">
            <i class="bi bi-clock-history mr-2"></i>
            To Do (Belum) <span class="text-sm font-normal">(<?php echo count($tugasBelum); ?>)</span>
        </h2>
        <div class="space-y-4 h-full min-h-[300px]">
            <?php if (empty($tugasBelum)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Tidak ada tugas baru saat ini.</p>
            <?php else: ?>
            <?php foreach ($tugasBelum as $tugas): ?>
            <?php include 'card_tugas.php'; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kolom: In Progress (Proses) -->
    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-blue-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-primary border-b pb-2">
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

    <!-- Kolom: Done (Selesai) -->
    <div class="flex-1 bg-gray-100 p-4 rounded-xl shadow-inner border border-green-200">
        <h2 class="text-xl font-poppins font-bold mb-4 text-green-700 border-b pb-2">
            <i class="bi bi-check-circle-fill mr-2"></i>
            Done (Selesai) <span class="text-sm font-normal">(<?php echo count($tugasSelesai); ?>)</span>
        </h2>
        <div class="space-y-4 h-full min-h-[300px]">
            <?php if (empty($tugasSelesai)): ?>
            <p class="text-sm text-gray-500 italic p-4 text-center">Anda belum menyelesaikan tugas apapun.</p>
            <?php else: ?>
            <?php foreach ($tugasSelesai as $tugas): ?>
            <?php include 'card_tugas.php'; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
require_once '../../layout/footer.php';
?>