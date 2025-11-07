<?php
// client/pages/status_tugas/board.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

Auth::checkLogin('karyawan'); 

$currentUserId = Auth::getUserData()['id_user'] ?? 0;
$client = new Client();

// 2. Ambil data dari API
$allTugas = $client->get('tugas'); 
$allUsers = $client->get('users'); 
$allStatusTugas = $client->get('status_tugas'); 

// 3. Buat User Map (id_user -> nama)
$userMap = [];
if (is_array($allUsers) && !isset($allUsers['status'])) {
    foreach ($allUsers as $user) {
        $userMap[$user['id_user']] = $user['nama'];
    }
}

// 4. Buat Status Map (id_tugas -> status data) untuk user yang sedang login
$statusMap = [];
if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    foreach ($allStatusTugas as $status) {
        // Ambil status paling baru milik user yang login untuk tugas ini
        // Asumsi: Kita hanya menyimpan status terakhir
        if ($status['id_user'] == $currentUserId) {
            // Karena tabel status_tugas adalah LOG, kita hanya ambil ENTRI TERAKHIR untuk setiap tugas
            // (Kita asumsikan record yang terakhir di GET adalah yang terbaru, atau rely on id_status)
            // UNTUK EFISIENSI, kita ambil yang terakhir muncul di array GET
            $statusMap[$status['id_tugas']] = $status; 
        }
    }
}


// 5. Filter dan Gabungkan tugas dengan status, lalu pisahkan ke 3 kolom
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        $id_tugas = $tugas['id_tugas'];
        
        // Cek apakah tugas ini ada di status_tugas milik user ini
        if (isset($statusMap[$id_tugas])) {
            $statusData = $statusMap[$id_tugas];
            
            // Gabungkan data status terbaru ke data tugas
            $tugas['status_current'] = strtolower($statusData['status'] ?? 'belum');
            $tugas['catatan'] = $statusData['catatan'] ?? '';
            $tugas['updated_at'] = $statusData['updated_at'] ?? '';
            $tugas['id_status'] = $statusData['id_status'] ?? 0;
            
            // Pisahkan berdasarkan status
            if ($tugas['status_current'] == 'proses') {
                $tugasProses[] = $tugas;
            } elseif ($tugas['status_current'] == 'selesai') {
                $tugasSelesai[] = $tugas;
            } else {
                $tugasBelum[] = $tugas;
            }
        }
        // Jika tugas belum ada di status_tugas, kita ABAIKAN (karena belum di-claim)
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
        Kelola status tugas Anda,
        <strong><?php echo htmlspecialchars(Auth::getUserData()['nama'] ?? 'Pengguna'); ?></strong>
    </p>
</div>

<div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">

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