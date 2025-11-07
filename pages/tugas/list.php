<?php
// client/pages/tugas/list.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

Auth::checkLogin('admin');

// 2. Ambil semua data relasi
$client = new Client();
$tugasList = $client->get('tugas');
$usersList = $client->get('users');
$deptList = $client->get('departemen');

// 3. Buat Peta Pencarian (Lookup Maps) untuk konversi ID ke Nama
$userMap = [];
if (is_array($usersList) && !isset($usersList['status'])) {
    foreach ($usersList as $user) {
        $userMap[$user['id_user']] = $user['nama']; // Gunakan 'nama' sesuai tabel users
    }
}

$deptMap = [];
if (is_array($deptList) && !isset($deptList['status'])) {
    foreach ($deptList as $dept) {
        $deptMap[$dept['id_departemen']] = $dept['nama_departemen'];
    }
}

// 4. Proses data tugas dan error checking
$tugas = [];
if (is_array($tugasList) && !isset($tugasList['status'])) {
    $tugas = $tugasList;
} elseif (isset($tugasList['status']) && $tugasList['status'] == 'error') {
    Helper::setFlashMessage('error', 'Gagal memuat daftar tugas: ' . $tugasList['message']);
}

$pageTitle = "Manajemen Tugas";
require_once '../../layout/header.php';
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-poppins font-semibold text-gray-800">Daftar Tugas</h2>

        <a href="<?php echo BASE_URL; ?>pages/tugas/form.php"
            class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-md">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Tugas</span>
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Tugas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat
                        Oleh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departemen</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                <?php if (!empty($tugas)): ?>
                <?php foreach ($tugas as $item): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($item['id_tugas']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($item['judul']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                        <?php echo date('d M Y', strtotime($item['deadline'])); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($userMap[$item['id_pembuat']] ?? 'N/A'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($deptMap[$item['id_departemen']] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="<?php echo BASE_URL; ?>pages/tugas/detail.php?id=<?php echo $item['id_tugas']; ?>"
                            title="Lihat Detail" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>pages/tugas/form.php?id=<?php echo $item['id_tugas']; ?>"
                            title="Ubah" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>proses/tugas.php?aksi=hapus&id=<?php echo $item['id_tugas']; ?>"
                            title="Hapus"
                            onclick="return confirm('Yakin ingin menghapus tugas: <?php echo htmlspecialchars($item['judul']); ?>?')"
                            class="text-red-600 hover:text-red-900">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data tugas yang
                        ditemukan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
require_once '../../layout/footer.php';
?>