<?php
// client/pages/tugas/form.php

// 1. Panggil file inti
require_once '../core/Auth.php';
require_once '../core/Helper.php';
require_once '../core/Client.php'; 

// 2. (WAJIB) Keamanan: Pastikan only 'admin'
Auth::checkLogin('admin'); 
$client = new Client();

// 3. (PENTING) Ambil data untuk Dropdown
// Ambil semua departemen
$deptList = $client->get('departemen');
if (!is_array($deptList)) $deptList = [];

// Ambil semua user, lalu filter hanya karyawan
$allUsers = $client->get('users');
$karyawanList = [];
if (is_array($allUsers)) {
    $karyawanList = array_filter($allUsers, function($user) {
        return $user['role'] == 'karyawan';
    });
}

// 4. Logika Mode Edit vs Mode Tambah
$isEditMode = false;
$tugasData = [ // Data default
    'judul' => '',
    'deskripsi' => '',
    'deadline' => date('Y-m-d', strtotime('+7 days')), // Default 1 minggu dari skrg
    'id_departemen' => null,
    'id_user_penerima' => null, // Karyawan yg ditugaskan
    'status' => 'Belum'
];
$pageTitle = "Tambah Tugas Baru";

// Cek apakah mode edit
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEditMode = true;
    $tugasId = $_GET['id'];
    $pageTitle = "Edit Tugas";
    
    // Ambil data tugas dari API
    $apiResponse = $client->get('tugas', $tugasId);
    $tugasData = $apiResponse[0] ?? null; 
    
    if (!$tugasData) {
        Helper::setFlashMessage('error', 'Tugas tidak ditemukan.');
        Helper::redirect('pages/tugas/list.php');
    }
}

// 5. Panggil Header
require_once '../layout/header.php'; 
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-poppins font-semibold">
        <?php echo $pageTitle; ?>
    </h1>
    <a href="list.php" class="text-gray-600 hover:text-primary transition-colors">
        <i class="bi bi-arrow-left-circle mr-1"></i>
        Kembali ke Daftar Tugas
    </a>
</div>

<div class="bg-white p-6 md:p-8 rounded-lg shadow-md">

    <form action="../proses/tugas.php" method="POST">

        <input type="hidden" name="aksi" value="<?php echo $isEditMode ? 'ubah' : 'tambah'; ?>">

        <?php if ($isEditMode): ?>
        <input type="hidden" name="id_tugas" value="<?php echo htmlspecialchars($tugasData['id_tugas']); ?>">
        <?php endif; ?>

        <input type="hidden" name="id_user_pembuat" value="<?php echo Auth::getUserData()['id_user']; ?>">

        <div class="space-y-6">

            <div>
                <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas</label>
                <input type="text" id="judul" name="judul"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                    value="<?php echo htmlspecialchars($tugasData['judul']); ?>" required>
            </div>

            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Tugas</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($tugasData['deskripsi'] ?? ''); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
                    <input type="date" id="deadline" name="deadline"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                        value="<?php echo date('Y-m-d', strtotime($tugasData['deadline'])); ?>" required>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                        <option value="Belum" <?php echo ($tugasData['status'] == 'Belum') ? 'selected' : ''; ?>>Belum
                            Dikerjakan</option>
                        <option value="Proses" <?php echo ($tugasData['status'] == 'Proses') ? 'selected' : ''; ?>>
                            Sedang Dikerjakan</option>
                        <option value="Selesai" <?php echo ($tugasData['status'] == 'Selesai') ? 'selected' : ''; ?>>
                            Selesai</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <select id="id_departemen" name="id_departemen"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                        required>
                        <option value="">-- Pilih Departemen --</option>
                        <?php foreach ($deptList as $dept): ?>
                        <option value="<?php echo $dept['id_departemen']; ?>"
                            <?php echo ($tugasData['id_departemen'] == $dept['id_departemen']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['nama_departemen']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="id_user_penerima" class="block text-sm font-medium text-gray-700 mb-1">Ditugaskan Kepada
                        (Karyawan)</label>
                    <select id="id_user_penerima" name="id_user_penerima"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                        required>
                        <option value="">-- Pilih Karyawan --</option>
                        <?php foreach ($karyawanList as $karyawan): ?>
                        <option value="<?php echo $karyawan['id_user']; ?>"
                            <?php echo ($tugasData['id_user_penerima'] == $karyawan['id_user']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($karyawan['nama_lengkap']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
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
                <span><?php echo $isEditMode ? 'Simpan Perubahan' : 'Simpan Tugas'; ?></span>
            </button>
        </div>

    </form>
</div>

<?php 
// 6. Panggil Footer
require_once '../layout/footer.php'; 
?>