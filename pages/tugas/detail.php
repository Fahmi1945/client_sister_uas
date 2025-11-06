<?php
// client/pages/tugas/detail.php

// 1. Panggil file inti
require_once '../core/Auth.php';
require_once '../core/Helper.php';
require_once '../core/Client.php'; 

// 2. (WAJIB) Keamanan: Pastikan 'admin'
Auth::checkLogin('admin'); 
$client = new Client();

// 3. (WAJIB) Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    Helper::setFlashMessage('error', 'ID Tugas tidak valid.');
    Helper::redirect('pages/tugas/list.php');
}
$tugasId = $_GET['id'];
$pageTitle = "Detail Tugas";

// 4. Ambil data TUGAS SPESIFIK
$apiResponse = $client->get('tugas', $tugasId);
$tugas = $apiResponse[0] ?? null; 

// Jika tugas tidak ditemukan, lempar kembali
if (!$tugas) {
    Helper::setFlashMessage('error', 'Tugas tidak ditemukan.');
    Helper::redirect('pages/tugas/list.php');
}

// 5. Ambil data untuk "Lookup Maps" (relasi)
$usersList = $client->get('users');
$deptList = $client->get('departemen');

$userMap = [];
if (is_array($usersList)) {
    foreach ($usersList as $user) {
        $userMap[$user['id_user']] = $user['nama_lengkap'];
    }
}

$deptMap = [];
if (is_array($deptList)) {
    foreach ($deptList as $dept) {
        $deptMap[$dept['id_departemen']] = $dept['nama_departemen'];
    }
}

// 6. Panggil Header
require_once '../layout/header.php'; 
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-poppins font-semibold">
        <?php echo $pageTitle; ?>
    </h1>
    <div class="flex space-x-3">
        <a href="list.php" class="text-gray-600 hover:text-primary transition-colors flex items-center space-x-1">
            <i class="bi bi-arrow-left-circle"></i>
            <span>Kembali</span>
        </a>
        <a href="form.php?id=<?php echo $tugas['id_tugas']; ?>"
            class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="bi bi-pencil-fill"></i>
            <span>Edit Tugas Ini</span>
        </a>
    </div>
</div>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-md">

    <div class="flex justify-between items-start pb-4 border-b border-gray-200">
        <div>
            <h2 class="text-2xl font-bold font-poppins text-gray-900">
                <?php echo htmlspecialchars($tugas['judul']); ?>
            </h2>
        </div>
        <?php 
        // Badge Warna untuk Status
        $status = $tugas['status'] ?? 'Belum';
        $badgeClass = 'bg-gray-100 text-gray-800'; // Default
        if ($status == 'Selesai') {
            $badgeClass = 'bg-green-100 text-green-800';
        } elseif ($status == 'Proses') {
            $badgeClass = 'bg-blue-100 text-blue-800';
        } elseif ($status == 'Belum') {
            $badgeClass = 'bg-yellow-100 text-yellow-800';
        }
        ?>
        <span class="px-3 py-1 text-sm leading-6 font-semibold rounded-full <?php echo $badgeClass; ?>">
            <i class="bi bi-circle-fill text-xs mr-1"></i>
            <?php echo htmlspecialchars($status); ?>
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <div class="md:col-span-1 space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg border">
                <h4 class="text-xs font-medium text-gray-500 uppercase">Ditugaskan Kepada</h4>
                <p class="text-lg font-semibold text-gray-900 flex items-center space-x-2 mt-1">
                    <i class="bi bi-person-fill text-primary"></i>
                    <span>
                        <?php 
                        $penerimaId = $tugas['id_user_penerima'] ?? null;
                        echo htmlspecialchars($userMap[$penerimaId] ?? 'N/A');
                        ?>
                    </span>
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border">
                <h4 class="text-xs font-medium text-gray-500 uppercase">Departemen</h4>
                <p class="text-lg font-semibold text-gray-900 flex items-center space-x-2 mt-1">
                    <i class="bi bi-building text-green-600"></i>
                    <span>
                        <?php 
                        $deptId = $tugas['id_departemen'] ?? null;
                        echo htmlspecialchars($deptMap[$deptId] ?? 'N/A');
                        ?>
                    </span>
                </p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg border">
                <h4 class="text-xs font-medium text-gray-500 uppercase">Pembuat Tugas (Admin)</h4>
                <p class="text-lg font-semibold text-gray-900 flex items-center space-x-2 mt-1">
                    <i class="bi bi-person-badge text-gray-600"></i>
                    <span>
                        <?php 
                        $pembuatId = $tugas['id_user_pembuat'] ?? null;
                        echo htmlspecialchars($userMap[$pembuatId] ?? 'N/A');
                        ?>
                    </span>
                </p>
            </div>

            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <h4 class="text-xs font-medium text-red-500 uppercase">Deadline</h4>
                <p class="text-lg font-bold text-red-700 flex items-center space-x-2 mt-1">
                    <i class="bi bi-calendar-event"></i>
                    <span>
                        <?php echo date('d F Y', strtotime($tugas['deadline'] ?? 'now')); ?>
                    </span>
                </p>
            </div>
        </div>

        <div class="md:col-span-2">
            <h4 class="text-lg font-semibold font-poppins mb-2">Deskripsi Tugas</h4>
            <div class="prose max-w-none text-gray-700">
                <?php 
                // nl2br() untuk mengubah baris baru (\n) menjadi tag <br>
                echo nl2br(htmlspecialchars($tugas['deskripsi'] ?? 'Tidak ada deskripsi.')); 
                ?>
            </div>
        </div>

    </div>
</div>

<?php 
// 7. Panggil Footer
require_once '../layout/footer.php'; 
?>