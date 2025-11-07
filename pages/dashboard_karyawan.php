<?php
// client/pages/dashboard_karyawan.php

// 1. Set judul halaman
$pageTitle = "Dashboard Karyawan"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (PENTING) Keamanan: Pastikan 'karyawan' yang login
Auth::checkLogin('karyawan'); 

// 4. Ambil data user yang sedang login
$currentUser = Auth::getUserData();
$currentUserId = $currentUser['id_user'] ?? 0;
$currentUserName = htmlspecialchars($currentUser['nama'] ?? 'Karyawan');

// 5. Inisialisasi Client untuk ambil data dari API
$client = new Client();

// 6. Ambil semua status_tugas milik karyawan ini
$allStatusTugas = $client->get('status_tugas');
$allTugas = $client->get('tugas');

// 7. Buat map tugas untuk akses cepat
$tugasMap = [];
if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        $tugasMap[$tugas['id_tugas']] = $tugas;
    }
}

// 8. Filter dan hitung statistik
$tugasBelum = 0;
$tugasProses = 0;
$tugasSelesai = 0;
$reminderTugas = []; // Array untuk tugas dengan deadline < 3 hari

if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    foreach ($allStatusTugas as $status) {
        // Hanya hitung tugas milik user yang login
        if ($status['id_user'] == $currentUserId) {
            $statusValue = strtolower($status['status'] ?? 'belum');
            
            // Hitung berdasarkan status
            if ($statusValue == 'belum') {
                $tugasBelum++;
            } elseif ($statusValue == 'proses') {
                $tugasProses++;
            } elseif ($statusValue == 'selesai') {
                $tugasSelesai++;
            }
            
            // Cek deadline untuk reminder (status belum atau proses)
            if (in_array($statusValue, ['belum', 'proses'])) {
                $idTugas = $status['id_tugas'];
                
                if (isset($tugasMap[$idTugas])) {
                    $tugas = $tugasMap[$idTugas];
                    $deadline = $tugas['deadline'] ?? null;
                    
                    if ($deadline) {
                        $deadlineTime = strtotime($deadline);
                        $now = time();
                        $selisihHari = floor(($deadlineTime - $now) / (60 * 60 * 24));
                        
                        // Jika deadline kurang dari 3 hari
                        if ($selisihHari <= 3 && $selisihHari >= 0) {
                            $deadlineText = '';
                            if ($selisihHari == 0) {
                                $deadlineText = 'Hari ini!';
                            } elseif ($selisihHari == 1) {
                                $deadlineText = 'Besok';
                            } else {
                                $deadlineText = $selisihHari . ' hari lagi';
                            }
                            
                            $reminderTugas[] = [
                                'judul' => $tugas['judul'],
                                'deadline' => $deadlineText,
                                'deadline_raw' => $deadline,
                                'status' => $statusValue,
                                'id_tugas' => $idTugas,
                                'selisih_hari' => $selisihHari
                            ];
                        }
                    }
                }
            }
        }
    }
}

// Urutkan reminder berdasarkan deadline terdekat
usort($reminderTugas, function($a, $b) {
    return $a['selisih_hari'] <=> $b['selisih_hari'];
});

$tugasAktif = $tugasBelum + $tugasProses; // Total tugas yang belum selesai

?>

<div class="mb-6">
    <h1 class="text-3xl font-poppins font-bold text-gray-800">
        Selamat Bekerja, <span class="text-primary"><?php echo $currentUserName; ?>!</span>
    </h1>
    <p class="text-gray-600 mt-1">Pantau progres tugas Anda hari ini</p>
</div>

<!-- Statistik Card -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Card: Tugas Aktif (Belum + Proses) -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 p-3 rounded-full">
                <i class="bi bi-hourglass-split text-2xl"></i>
            </div>
            <span class="text-4xl font-bold"><?php echo $tugasAktif; ?></span>
        </div>
        <h3 class="text-sm font-medium opacity-90">Tugas Aktif</h3>
        <p class="text-xs opacity-75 mt-1">Belum + Proses</p>
    </div>

    <!-- Card: Dalam Proses -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 p-3 rounded-full">
                <i class="bi bi-play-circle-fill text-2xl"></i>
            </div>
            <span class="text-4xl font-bold"><?php echo $tugasProses; ?></span>
        </div>
        <h3 class="text-sm font-medium opacity-90">Dalam Proses</h3>
        <p class="text-xs opacity-75 mt-1">Sedang dikerjakan</p>
    </div>

    <!-- Card: Tugas Selesai -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
        <div class="flex items-center justify-between mb-2">
            <div class="bg-white/20 p-3 rounded-full">
                <i class="bi bi-check2-circle text-2xl"></i>
            </div>
            <span class="text-4xl font-bold"><?php echo $tugasSelesai; ?></span>
        </div>
        <h3 class="text-sm font-medium opacity-90">Tugas Selesai</h3>
        <p class="text-xs opacity-75 mt-1">Total diselesaikan</p>
    </div>

    <!-- Card: Akses Papan Tugas -->
    <a href="<?php echo BASE_URL; ?>pages/status_tugas/board.php" class="block group">
        <div
            class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-all border-2 border-gray-200 hover:border-primary h-full">
            <div class="flex items-center justify-between mb-2">
                <div class="bg-primary/10 p-3 rounded-full group-hover:bg-primary/20 transition-colors">
                    <i class="bi bi-kanban text-primary text-2xl"></i>
                </div>
                <i class="bi bi-arrow-right text-2xl text-primary group-hover:translate-x-1 transition-transform"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-700 group-hover:text-primary transition-colors">
                Papan Tugas
            </h3>
            <p class="text-xs text-gray-500 mt-1">Kelola semua tugas</p>
        </div>
    </a>
</div>

<!-- Progress Bar -->
<?php 
$totalTugas = $tugasAktif + $tugasSelesai;
$persenSelesai = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100) : 0;
?>
<div class="bg-white p-6 rounded-xl shadow-lg mb-8">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold font-poppins text-gray-800 flex items-center">
            <i class="bi bi-graph-up text-primary mr-2"></i>
            Progress Keseluruhan
        </h3>
        <span class="text-2xl font-bold text-primary"><?php echo $persenSelesai; ?>%</span>
    </div>

    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
        <div class="bg-gradient-to-r from-primary to-blue-600 h-4 rounded-full transition-all duration-500 flex items-center justify-end pr-2"
            style="width: <?php echo $persenSelesai; ?>%">
            <?php if ($persenSelesai > 10): ?>
            <span class="text-xs text-white font-bold"><?php echo $persenSelesai; ?>%</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mt-4 text-center">
        <div>
            <p class="text-2xl font-bold text-yellow-600"><?php echo $tugasBelum; ?></p>
            <p class="text-xs text-gray-600">Belum Mulai</p>
        </div>
        <div>
            <p class="text-2xl font-bold text-blue-600"><?php echo $tugasProses; ?></p>
            <p class="text-xs text-gray-600">Proses</p>
        </div>
        <div>
            <p class="text-2xl font-bold text-green-600"><?php echo $tugasSelesai; ?></p>
            <p class="text-xs text-gray-600">Selesai</p>
        </div>
    </div>
</div>

<!-- Reminder Deadline Mendekat -->
<div class="bg-white p-6 rounded-xl shadow-lg">
    <h3 class="text-lg font-semibold mb-4 font-poppins text-gray-800 flex items-center">
        <i class="bi bi-bell-fill text-red-500 mr-2 animate-pulse"></i>
        Reminder Deadline Mendekat
        <?php if (count($reminderTugas) > 0): ?>
        <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
            <?php echo count($reminderTugas); ?>
        </span>
        <?php endif; ?>
    </h3>

    <div class="space-y-3">
        <?php if (empty($reminderTugas)): ?>
        <div class="text-center py-8">
            <i class="bi bi-check-circle text-6xl text-green-500 mb-2"></i>
            <p class="text-gray-500 font-medium">Tidak ada tugas dengan deadline mendesak</p>
            <p class="text-sm text-gray-400 mt-1">Semua tugas Anda terkendali dengan baik!</p>
        </div>
        <?php else: ?>
        <?php foreach ($reminderTugas as $tugas): ?>
        <?php 
                // Tentukan warna berdasarkan urgency
                $bgColor = 'bg-yellow-50 border-yellow-200';
                $textColor = 'text-yellow-800';
                $badgeColor = 'bg-yellow-500';
                
                if ($tugas['selisih_hari'] == 0) {
                    $bgColor = 'bg-red-50 border-red-300';
                    $textColor = 'text-red-800';
                    $badgeColor = 'bg-red-500';
                } elseif ($tugas['selisih_hari'] == 1) {
                    $bgColor = 'bg-orange-50 border-orange-200';
                    $textColor = 'text-orange-800';
                    $badgeColor = 'bg-orange-500';
                }
                
                $statusBadge = strtoupper($tugas['status']);
                $statusIcon = $tugas['status'] == 'proses' ? 'bi-play-circle-fill' : 'bi-clock-history';
                ?>
        <div class="p-4 <?php echo $bgColor; ?> border rounded-lg hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h4 class="font-bold <?php echo $textColor; ?>">
                            <?php echo htmlspecialchars($tugas['judul']); ?>
                        </h4>
                        <span class="text-xs font-medium px-2 py-1 rounded <?php echo $badgeColor; ?> text-white">
                            <i class="bi <?php echo $statusIcon; ?>"></i> <?php echo $statusBadge; ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-600">
                        <span>
                            <i class="bi bi-calendar-event"></i>
                            <?php echo date('d M Y', strtotime($tugas['deadline_raw'])); ?>
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold <?php echo $textColor; ?> whitespace-nowrap">
                        <i class="bi bi-alarm"></i>
                        <?php echo htmlspecialchars($tugas['deadline']); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>pages/status_tugas/board.php"
                        class="block mt-2 text-xs text-primary hover:underline">
                        Lihat Detail →
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
// 4. Panggil Footer
require_once '../layout/footer.php'; 
?>