<?php
// client/pages/departemen/form.php

// 1. Panggil file inti
require_once '../core/Auth.php';
require_once '../core/Helper.php';
require_once '../core/Client.php'; 

// 2. (WAJIB) Keamanan: Pastikan hanya 'admin' yang bisa akses
Auth::checkLogin('admin'); 

// 3. Logika Mode Edit vs Mode Tambah
$isEditMode = false;
$deptData = [ // Data default untuk form 'tambah'
    'nama_departemen' => '',
    'deskripsi' => '',
    'lokasi' => ''
];
$deptId = null;
$pageTitle = "Tambah Departemen Baru"; // Judul default

// Cek apakah ada ID di URL (mode edit)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditMode = true;
    $deptId = $_GET['id'];
    $pageTitle = "Edit Departemen";
    
    // Ambil data departemen dari API
    $client = new Client();
    $apiResponse = $client->get('departemen', $deptId);
    
    // Server API mengembalikan array, ambil elemen pertama
    $deptData = $apiResponse[0] ?? null; 
    
    // Jika data tidak ada, lempar kembali
    if (!$deptData) {
        Helper::setFlashMessage('error', 'Departemen tidak ditemukan.');
        Helper::redirect('pages/departemen/list.php');
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
        Kembali ke Daftar Departemen
    </a>
</div>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-md">

    <form action="../proses/departemen.php" method="POST">

        <input type="hidden" name="aksi" value="<?php echo $isEditMode ? 'ubah' : 'tambah'; ?>">

        <?php if ($isEditMode): ?>
        <input type="hidden" name="id_departemen" value="<?php echo htmlspecialchars($deptData['id_departemen']); ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="md:col-span-1">
                <label for="nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">Nama
                    Departemen</label>
                <input type="text" id="nama_departemen" name="nama_departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($deptData['nama_departemen']); ?>" required>
            </div>

            <div class="md:col-span-1">
                <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($deptData['lokasi']); ?>">
            </div>

            <div class="md:col-span-2">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($deptData['deskripsi']); ?></textarea>
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
                <span><?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan Departemen'; ?></span>
            </button>
        </div>

    </form>
</div>

<?php 
// 5. Panggil Footer
require_once '../layout/footer.php'; 
?>