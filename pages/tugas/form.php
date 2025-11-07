<?php
// client/pages/tugas/form.php

// 1. Muat File Inti
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

Auth::checkLogin('admin'); 
$client = new Client();

// 2. Ambil data Dropdown
$usersList = $client->get('users');
$deptList = $client->get('departemen');
if (!is_array($usersList)) $usersList = [];
if (!is_array($deptList)) $deptList = [];

// 3. Inisialisasi Data Default dan Cek Mode
$isEditMode = false;
$tugasData = [ 
    'id_tugas' => null,
    'judul' => '',
    'deskripsi' => '',
    'deadline' => date('Y-m-d', strtotime('+7 days')),
    'id_pembuat' => Auth::getUserData()['id_user'] ?? null, // Default pembuat adalah admin yang sedang login
    'id_departemen' => null
];
$pageTitle = "Tambah Tugas Baru"; 

// Cek Mode Edit
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditMode = true;
    $tugasId = $_GET['id'];
    $pageTitle = "Ubah Data Tugas";
    
    $apiResponse = $client->get('tugas', $tugasId);
    if (is_array($apiResponse) && !isset($apiResponse['status']) && !empty($apiResponse[0])) {
        $tugasData = array_merge($tugasData, $apiResponse[0]); 
    } else {
        Helper::setFlashMessage('error', 'Tugas tidak ditemukan.');
        Helper::redirect('pages/tugas/list.php');
    }
}

require_once '../../layout/header.php';
?>

<h1 class="text-3xl font-poppins font-bold text-gray-800 mb-6">
    <?php echo $pageTitle; ?>
</h1>

<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>pages/tugas/list.php"
        class="text-gray-600 hover:text-primary transition-colors flex items-center space-x-1">
        <i class="bi bi-arrow-left-circle mr-1"></i>
        <span>Kembali ke Daftar Tugas</span>
    </a>
</div>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-lg">
    <form action="<?php echo BASE_URL; ?>proses/tugas.php" method="POST">

        <input type="hidden" name="aksi" value="<?php echo $isEditMode ? 'ubah' : 'tambah'; ?>">
        <input type="hidden" name="id_pembuat" value="<?php echo htmlspecialchars($tugasData['id_pembuat']); ?>">

        <?php if ($isEditMode): ?>
        <input type="hidden" name="id_tugas" value="<?php echo htmlspecialchars($tugasData['id_tugas']); ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="md:col-span-2">
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="judul" name="judul"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($tugasData['judul']); ?>" required>
            </div>

            <div>
                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                <input type="date" id="deadline" name="deadline"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($tugasData['deadline']); ?>" required>
            </div>

            <div>
                <label for="id_departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                <select id="id_departemen" name="id_departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="">-- Tidak Ada --</option>
                    <?php foreach ($deptList as $dept): ?>
                    <option value="<?php echo $dept['id_departemen']; ?>"
                        <?php echo ($tugasData['id_departemen'] == $dept['id_departemen']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dept['nama_departemen']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Tugas</label>
                <textarea id="deskripsi" name="deskripsi" rows="6"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($tugasData['deskripsi'] ?? ''); ?></textarea>
            </div>

        </div>
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-3">
            <a href="<?php echo BASE_URL; ?>pages/tugas/list.php"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit"
                class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
                <i class="bi bi-save-fill"></i>
                <span><?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan Tugas Baru'; ?></span>
            </button>
        </div>

    </form>
</div>

<?php 
require_once '../../layout/footer.php'; 
?>