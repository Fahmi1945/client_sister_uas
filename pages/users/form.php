<?php
// client/pages/users/form.php

// 1. Panggil file inti
require_once '../core/Auth.php';
require_once '../core/Helper.php';
require_once '../core/Client.php'; // Kita perlu client untuk mode edit

// 2. (WAJIB) Keamanan: Pastikan hanya 'admin' yang bisa akses
Auth::checkLogin('admin'); 

// 3. Logika Mode Edit vs Mode Tambah
$isEditMode = false;
$userData = [ // Data default untuk form 'tambah'
    'nama_lengkap' => '',
    'email' => '',
    'role' => 'karyawan', // Default-kan ke karyawan
    'departemen' => ''
];
$userId = null;
$pageTitle = "Tambah User Baru"; // Judul default

// Cek apakah ada ID di URL (mode edit)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditMode = true;
    $userId = $_GET['id'];
    $pageTitle = "Edit User";
    
    // Ambil data user dari API
    $client = new Client();
    $apiResponse = $client->get('users', $userId);
    
    // Server API mengembalikan array, ambil elemen pertama
    $userData = $apiResponse[0] ?? null; 
    
    // Jika user dengan ID itu tidak ada, lempar kembali
    if (!$userData) {
        Helper::setFlashMessage('error', 'User tidak ditemukan.');
        Helper::redirect('pages/users/list.php');
    }
}

// 4. Panggil Header (SETELAH $pageTitle di-set)
require_once '../layout/header.php'; 
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-poppins font-semibold">
        <?php echo $pageTitle; ?>
    </h1>
    <a href="list.php" class="text-gray-600 hover:text-primary transition-colors">
        <i class="bi bi-arrow-left-circle mr-1"></i>
        Kembali ke Daftar User
    </a>
</div>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-md">

    <form action="../proses/user.php" method="POST">

        <input type="hidden" name="aksi" value="<?php echo $isEditMode ? 'ubah' : 'tambah'; ?>">

        <?php if ($isEditMode): ?>
        <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($userData['id_user']); ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($userData['nama_lengkap']); ?>" required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="role" name="role"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="karyawan" <?php echo ($userData['role'] == 'karyawan') ? 'selected' : ''; ?>>Karyawan
                    </option>
                    <option value="admin" <?php echo ($userData['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div>
                <label for="departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                <input type="text" id="departemen" name="departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($userData['departemen'] ?? ''); ?>">
            </div>

            <div class="md:col-span-2">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    placeholder="<?php echo $isEditMode ? 'Kosongkan jika tidak ingin mengubah password' : ''; ?>"
                    <?php echo $isEditMode ? '' : 'required'; // Wajib diisi hanya saat mode 'tambah' ?>>
                <?php if ($isEditMode): ?>
                <p class="mt-1 text-xs text-gray-500">Kosongkan kolom ini jika Anda tidak ingin mengubah password.</p>
                <?php endif; ?>
            </div>

        </div>
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
            <a href="list.php"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit"
                class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="bi bi-save-fill"></i>
                <span><?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan User Baru'; ?></span>
            </button>
        </div>

    </form>
</div>

<?php 
// 5. Panggil Footer
require_once '../layout/footer.php'; 
?>