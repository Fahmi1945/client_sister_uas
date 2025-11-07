<?php
// client/pages/departemen/form.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

// Pastikan hanya admin yang bisa akses
Auth::checkLogin('admin'); 

// 2. Inisialisasi Data Default dan Cek Mode
$isEditMode = false;
$deptData = [ 
    'id_departemen' => null,
    'nama_departemen' => '',
    'deskripsi' => '',
    'lokasi' => ''
];
$pageTitle = "Tambah Departemen Baru"; 

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditMode = true;
    $deptId = $_GET['id'];
    $pageTitle = "Ubah Data Departemen";
    
    $client = new Client();
    $apiResponse = $client->get('departemen', $deptId);
    
    if (is_array($apiResponse) && !isset($apiResponse['status']) && !empty($apiResponse[0])) {
        $deptData = array_merge($deptData, $apiResponse[0]); 
    } else {
        Helper::setFlashMessage('error', 'Departemen tidak ditemukan.');
        Helper::redirect('pages/departemen/list.php');
    }
}

// Panggil Header
require_once '../../layout/header.php'; 
?>

<h1 class="text-3xl font-poppins font-bold text-gray-800 mb-6">
    <?php echo $pageTitle; ?>
</h1>

<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>pages/departemen/list.php"
        class="text-gray-600 hover:text-primary transition-colors flex items-center space-x-1">
        <i class="bi bi-arrow-left-circle mr-1"></i>
        <span>Kembali ke Daftar Departemen</span>
    </a>
</div>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-lg max-w-2xl mx-auto">

    <form action="<?php echo BASE_URL; ?>proses/departemen.php" method="POST">

        <input type="hidden" name="aksi" value="<?php echo $isEditMode ? 'ubah' : 'tambah'; ?>">

        <?php if ($isEditMode): ?>
        <input type="hidden" name="id_departemen" value="<?php echo htmlspecialchars($deptData['id_departemen']); ?>">
        <?php endif; ?>

        <div class="space-y-4">

            <div>
                <label for="nama_departemen" class="block text-sm font-medium text-gray-700 mb-1">Nama
                    Departemen</label>
                <input type="text" id="nama_departemen" name="nama_departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($deptData['nama_departemen']); ?>" required>
            </div>

            <div>
                <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($deptData['lokasi'] ?? ''); ?>">
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($deptData['deskripsi'] ?? ''); ?></textarea>
            </div>

        </div>
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>pages/departemen/list.php"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit"
                class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="bi bi-save-fill"></i>
                <span><?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan Departemen Baru'; ?></span>
            </button>
        </div>

    </form>
</div>

<?php 
require_once '../../layout/footer.php'; 
?>