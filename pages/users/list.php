<?php
// client/pages/users/list.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';

// Pastikan hanya admin yang bisa mengakses
Auth::checkLogin('admin');

// Atur Judul Halaman
$pageTitle = "Manajemen User";

// 2. Inisialisasi Client dan Ambil Data
$client = new Client();
$response = $client->get('users');

$users = [];
$apiError = false;

// Periksa apakah respons adalah array data atau error
if (is_array($response) && isset($response[0]['id_user'])) {
    // Jika respons adalah array data user
    $users = $response;
} elseif (is_array($response) && isset($response['status']) && $response['status'] == 'error') {
    // Jika ada error dari API
    $apiError = true;
    $errorMessage = $response['message'];
} elseif (is_array($response) && empty($response)) {
    // Jika respons adalah array kosong (tidak ada data)
    $users = [];
} else {
    // Error koneksi atau format respons tidak terduga
    $apiError = true;
    $errorMessage = "Gagal mengambil data dari API. Periksa koneksi atau server.";
}


// 3. Panggil Header (yang akan menampilkan flash message)
require_once '../../layout/header.php';
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-poppins font-semibold text-gray-800">Daftar Pengguna</h2>

        <a href="/pages/users/form.php"
            class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-md">
            <i class="bi bi-person-plus-fill"></i>
            <span>Tambah User</span>
        </a>
    </div>

    <?php if ($apiError): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Kesalahan!</strong>
        <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        Departemen</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">

                <?php $no = 1; ?>
                <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $no++; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo htmlspecialchars($user['id_user']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?php echo htmlspecialchars($user['nama']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo ($user['role'] == 'admin') ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800'; ?>">
                            <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo htmlspecialchars($user['id_departemen'] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="/pages/users/form.php?id=<?php echo $user['id_user']; ?>"
                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="bi bi-pencil-square"></i> Ubah
                        </a>

                        <a href="/proses/user.php?aksi=hapus&id=<?php echo $user['id_user']; ?>"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus user: <?php echo htmlspecialchars($user['nama']); ?>?')"
                            class="text-red-600 hover:text-red-900">
                            <i class="bi bi-trash-fill"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data pengguna yang
                        ditemukan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// 4. Panggil Footer
require_once '../../layout/footer.php';
?>