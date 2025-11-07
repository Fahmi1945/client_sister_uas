<?php
// client/pages/departemen/list.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

// Pastikan hanya admin yang bisa mengakses
Auth::checkLogin('admin');

// Atur Judul Halaman
$pageTitle = "Manajemen Departemen";

// 2. Ambil data dari API
$client = new Client();
$response = $client->get('departemen'); // Memanggil endpoint departemen

$departemen = [];
$apiError = false;

if (is_array($response) && !isset($response['status'])) {
    $departemen = $response;
} elseif (isset($response['status']) && $response['status'] == 'error') {
    $apiError = true;
    $errorMessage = $response['message'];
}

// Panggil Header
require_once '../../layout/header.php';
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-poppins font-semibold text-gray-800">Daftar Departemen</h2>

        <a href="<?php echo BASE_URL; ?>pages/departemen/form.php"
            class="flex items-center space-x-2 bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-md">
            <i class="bi bi-building"></i>
            <span>Tambah Departemen</span>
        </a>
    </div>

    <?php if ($apiError): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Kesalahan API!</strong>
        <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                        Departemen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat
                        Pada</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                <?php if (!empty($departemen)): ?>
                <?php foreach ($departemen as $dept): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($dept['id_departemen']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($dept['nama_departemen']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($dept['lokasi'] ?? '-'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo date('d M Y', strtotime($dept['created_at'])); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="<?php echo BASE_URL; ?>pages/departemen/form.php?id=<?php echo $dept['id_departemen']; ?>"
                            class="text-primary hover:text-indigo-900 mr-3">
                            <i class="bi bi-pencil-square"></i> Ubah
                        </a>
                        <a href="<?php echo BASE_URL; ?>proses/departemen.php?aksi=hapus&id=<?php echo $dept['id_departemen']; ?>"
                            onclick="return confirm('Yakin ingin menghapus departemen: <?php echo htmlspecialchars($dept['nama_departemen']); ?>?')"
                            class="text-red-600 hover:text-red-900">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data departemen.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
require_once '../../layout/footer.php';
?>