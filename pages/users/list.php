<?php
// client/pages/users/list.php

// 1. Set judul halaman
$pageTitle = "Manajemen User"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (WAJIB) Keamanan: Pastikan hanya 'admin' yang bisa akses
Auth::checkLogin('admin'); 

// 4. Panggil Client untuk ambil data
require_once '../core/Client.php';
$client = new Client();
$users = $client->get('users'); // Asumsi endpoint API adalah /users

// 5. Periksa jika data adalah array, jika tidak, buat array kosong
if (!is_array($users)) {
    $users = [];
    // Anda bisa set flash message di sini jika mau
    // Helper::setFlashMessage('error', 'Gagal mengambil data user dari server.');
}

?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-poppins font-semibold">
        Manajemen User
    </h1>

    <a href="form.php"
        class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
        <i class="bi bi-plus-lg"></i>
        <span>Tambah User</span>
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">

    <div class="overflow-x-auto">
        <table class="w-full min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departemen
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">

                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data user ditemukan.
                    </td>
                </tr>

                <?php else: ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($user['nama_lengkap'] ?? 'N/A'); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php 
                                // Sesuai UI Brief: Badge Warna untuk Role
                                $role = $user['role'] ?? 'guest';
                                $badgeClass = 'bg-gray-100 text-gray-800'; // Default
                                if ($role == 'admin') {
                                    $badgeClass = 'bg-green-100 text-green-800';
                                } elseif ($role == 'karyawan') {
                                    $badgeClass = 'bg-blue-100 text-blue-800';
                                }
                                ?>
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($role); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            <?php echo htmlspecialchars($user['departemen'] ?? 'N/A'); // Asumsi ada field ini ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="form.php?id=<?php echo $user['id_user']; // Asumsi PK adalah id_user ?>"
                                class="text-primary hover:text-primary-hover p-2 rounded-md bg-blue-50 hover:bg-blue-100"
                                title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>

                            <a href="../proses/user.php?aksi=hapus&id=<?php echo $user['id_user']; ?>"
                                class="text-red-500 hover:text-red-700 p-2 rounded-md bg-red-50 hover:bg-red-100"
                                title="Hapus"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus user <?php echo htmlspecialchars($user['nama_lengkap']); ?>?');">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

<?php 
// 6. Panggil Footer
require_once '../layout/footer.php'; 
?>