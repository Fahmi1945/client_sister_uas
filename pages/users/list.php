<?php
// client/pages/users/list.php

// 1. Panggil file inti dan header layout
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';

// Ambil BASE_URL dari config.php (sekarang sudah dimuat via Client/Auth/Helper)
require_once '../../config/config.php'; 
$base_url = BASE_URL;

$pageTitle = "Manajemen User";
require_once '../../layout/header.php';

// 2. Keamanan: Pastikan hanya Admin yang bisa akses
Auth::checkLogin('admin');

// 3. Ambil data dari API
$client = new Client();
$response = $client->get('users'); // Memanggil endpoint users

$users = [];
// Cek jika response adalah array dan bukan format error
if (is_array($response) && !isset($response['status'])) {
    $users = $response;
} else {
    // Jika ada error (koneksi gagal/API error), tampilkan pesan
    $errorMessage = "Gagal mengambil data user.";
    if (isset($response['message'])) {
        $errorMessage .= " Pesan: " . $response['message'];
    }
    Helper::setFlashMessage('error', $errorMessage);
}
?>

<h1 class="text-3xl font-poppins font-bold text-gray-800 mb-6">
    Manajemen User
</h1>

<div class="mb-4">
    <a href="<?php echo $base_url; ?>pages/users/form.php"
        class="inline-flex items-center space-x-2 bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md font-medium transition-colors shadow-md">
        <i class="bi bi-person-plus-fill text-lg"></i>
        <span>Tambah User Baru</span>
    </a>
</div>

<div class="bg-white p-6 rounded-xl shadow-lg">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        #
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Username
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Lengkap
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Role
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">
                        Data user kosong atau gagal dimuat.
                    </td>
                </tr>
                <?php else: ?>
                <?php $no = 1; foreach ($users as $user): 
                        // Asumsikan primary key di tabel server adalah 'id_user'
                        $id = $user['id_user'] ?? $user['id'] ?? null; 
                    ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <?php echo $no++; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?php echo htmlspecialchars($user['username'] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <?php echo htmlspecialchars($user['nama_lengkap'] ?? '-'); ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                        if (($user['role'] ?? '') == 'admin') echo 'bg-red-100 text-red-800';
                                        else echo 'bg-green-100 text-green-800';
                                    ?>">
                            <?php echo htmlspecialchars(ucfirst($user['role'] ?? '-')); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <a href="<?php echo $base_url; ?>pages/users/form.php?id=<?php echo htmlspecialchars($id); ?>"
                            class="text-blue-600 hover:text-blue-900 mx-1">
                            <i class="bi bi-pencil-square text-lg"></i>
                        </a>
                        <a href="javascript:void(0);"
                            onclick="confirmDelete('<?php echo htmlspecialchars($id); ?>', '<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>')"
                            class="text-red-600 hover:text-red-900 mx-1">
                            <i class="bi bi-trash-fill text-lg"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus user: ${nama} (ID: ${id})?`)) {
        // Redirect ke file proses untuk menghapus
        window.location.href = `<?php echo $base_url; ?>proses/user.php?aksi=hapus&id=${id}`;
    }
}
</script>

<?php 
require_once '../../layout/footer.php'; 
?>