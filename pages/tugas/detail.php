<?php
// client/pages/tugas/detail.php

// 1. Panggil file inti
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Helper.php';
require_once __DIR__ . '/../../core/Client.php';

// 2. Batasi akses. Hanya pengguna yang login (Admin atau Karyawan) yang boleh melihat detail.
Auth::checkLogin();

// 3. Ambil ID dari URL (Contoh: detail.php?id=5)
$id_tugas = $_GET['id'] ?? null;

// Validasi ID
if (!$id_tugas || !is_numeric($id_tugas)) {
    // Jika ID tidak ada atau tidak valid, redirect ke halaman list
    Helper::setFlashMessage('error', 'ID tugas tidak valid atau tidak ditemukan.');
    Helper::redirect('pages/tugas/list.php');
}

$client = new Client();

// 4. Ambil Data Tugas (tugasData)
$responseTugas = $client->get('tugas', $id_tugas); 

// Cek error dari API untuk data tugas
if (isset($responseTugas['status']) && $responseTugas['status'] == 'error') {
    Helper::setFlashMessage('error', 'Gagal mengambil data tugas: ' . $responseTugas['message']);
    Helper::redirect('pages/tugas/list.php');
}

// Ambil data tugas
$tugasData = $responseTugas[0] ?? null; 

// Cek apakah data tugas ditemukan
if (!$tugasData) {
    Helper::setFlashMessage('error', 'Data tugas dengan ID ' . htmlspecialchars($id_tugas) . ' tidak ditemukan.');
    Helper::redirect('pages/tugas/list.php');
}

// 5. Ambil Data Terkait (Departemen dan User/Karyawan)
$namaDepartemen = 'N/A';
$namaKaryawan = 'N/A';
$id_departemen = $tugasData['id_departemen'] ?? null;
$id_user = $tugasData['id_user'] ?? null;

// Ambil data Departemen
if ($id_departemen) {
    $responseDept = $client->get('departemen', $id_departemen);
    $deptData = $responseDept[0] ?? null;
    if ($deptData) {
        $namaDepartemen = htmlspecialchars($deptData['nama_departemen']);
    }
}

// Ambil data User/Karyawan
if ($id_user) {
    $responseUser = $client->get('users', $id_user);
    $userData = $responseUser[0] ?? null;
    if ($userData) {
        $namaKaryawan = htmlspecialchars($userData['nama_lengkap']);
    }
}

// 6. Atur Judul Halaman
$pageTitle = "Detail Tugas: " . htmlspecialchars($tugasData['judul']);

// 7. Panggil Header
require_once __DIR__ . '/../../layout/header.php';
?>

<div class="container mx-auto p-4">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden p-8">
        <h2 class="text-3xl font-bold mb-8 text-gray-800 font-poppins border-b pb-2">Detail Tugas</h2>

        <div class="space-y-6">
            <div class="border-b pb-4">
                <h3 class="text-xl font-semibold text-primary mb-2"><?php echo htmlspecialchars($tugasData['judul']); ?>
                </h3>
                <p class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($tugasData['deskripsi']); ?>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">ID Tugas:</strong>
                    <span class="text-gray-800"><?php echo htmlspecialchars($tugasData['id_tugas']); ?></span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Karyawan:</strong>
                    <span class="text-gray-800"><?php echo $namaKaryawan; ?></span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Departemen:</strong>
                    <span class="text-gray-800"><?php echo $namaDepartemen; ?></span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Status:</strong>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        <?php 
                        $status = strtolower($tugasData['status'] ?? 'unknown');
                        if ($status === 'to do') echo 'bg-gray-200 text-gray-800';
                        elseif ($status === 'in progress') echo 'bg-yellow-100 text-yellow-800';
                        elseif ($status === 'done') echo 'bg-green-100 text-green-800';
                        else echo 'bg-red-100 text-red-800';
                        ?>">
                        <?php echo htmlspecialchars(ucwords($tugasData['status'] ?? 'Tidak Diketahui')); ?>
                    </span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Mulai:</strong>
                    <span
                        class="text-gray-800"><?php echo date('d M Y', strtotime($tugasData['tanggal_mulai'] ?? '')); ?></span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Selesai:</strong>
                    <span
                        class="text-gray-800"><?php echo date('d M Y', strtotime($tugasData['tanggal_selesai'] ?? '')); ?></span>
                </div>
            </div>
        </div>

        <div class="mt-8 flex space-x-3">
            <a href="<?php echo BASE_URL; ?>pages/tugas/edit.php?id=<?php echo htmlspecialchars($tugasData['id_tugas']); ?>"
                class="flex items-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition-colors">
                <i class="bi bi-pencil-square mr-2"></i>Edit Tugas
            </a>
            <a href="<?php echo BASE_URL; ?>pages/tugas/list.php"
                class="flex items-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors">
                <i class="bi bi-arrow-left-circle mr-2"></i>Kembali
            </a>
        </div>

    </div>
</div>

<?php
// 8. Panggil Footer
require_once __DIR__ . '/../../layout/footer.php';
?>