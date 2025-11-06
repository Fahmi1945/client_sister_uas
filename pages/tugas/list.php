<?php
// client/pages/tugas/list.php

// 1. Set judul halaman
$pageTitle = "Manajemen Tugas"; 

// 2. Panggil Header
require_once '../layout/header.php'; 

// 3. (WAJIB) Keamanan: Pastikan hanya 'admin' yang bisa akses
Auth::checkLogin('admin'); 

// 4. Panggil Client untuk ambil data
require_once '../core/Client.php';
$client = new Client();

// --- INI BAGIAN PENTING ---
// Kita ambil SEMUA data yang kita perlukan
$tugasList = $client->get('tugas');      // Asumsi endpoint /tugas
$usersList = $client->get('users');      // Asumsi endpoint /users
$deptList = $client->get('departemen'); // Asumsi endpoint /departemen

// 5. Buat "Lookup Maps" (Peta Pencarian)
// Ini agar kita bisa konversi id -> nama
$userMap = [];
if (is_array($usersList)) {
    foreach ($usersList as $user) {
        $userMap[$user['id_user']] = $user['nama_lengkap']; // Asumsi PK 'id_user'
    }
}

$deptMap = [];
if (is_array($deptList)) {
    foreach ($deptList as $dept) {
        $deptMap[$dept['id_departemen']] = $dept['nama_departemen']; // Asumsi PK 'id_departemen'
    }
}

// 6. Logika Filter (Sesuai UI Brief)
$filterStatus = $_GET['status'] ?? 'Semua';
if (!is_array($tugasList)) {
    $tugasList = [];
}
// Jika filter diterapkan, saring array-nya
if ($filterStatus != 'Semua') {
    $tugasList = array_filter($tugasList, function($tugas) use ($filterStatus) {
        return $tugas['status'] == $filterStatus; // Asumsi ada kolom 'status'
    });
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <h1 class="text-2xl font-poppins font-semibold">
        <i class="bi bi-clipboard-check-fill text-primary mr-2"></i>
        Manajemen Tugas
    </h1>

    <div class="flex items-center space-x-4">
        <form method="GET" action="list.php" class="flex items-center space-x-2">
            <label for="status" class="text-sm font-medium">Filter Status:</label>
            <select name="status" id="status"
                class="w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                onchange="this.form.submit()">
                <option value="Semua" <?php echo ($filterStatus == 'Semua') ? 'selected' : ''; ?>>Semua Status</option>
                <option value="Belum" <?php echo ($filterStatus == 'Belum') ? 'selected' : ''; ?>>Belum Dikerjakan
                </option>
                <option value="Proses" <?php echo ($filterStatus == 'Proses') ? 'selected' : ''; ?>>Sedang Dikerjakan
                </option>
                <option value="Selesai" <?php echo ($filterStatus == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
            </select>
        </form>

        <a href="form.php"
            class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Tugas</span>
        </a>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">

    <div class="overflow-x-auto">
        <table class="w-full min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul
                        Tugas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Departemen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembuat
                        (Admin)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($tugasList)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data tugas ditemukan (atau sesuai filter).
                    </td>
                </tr>

                <?php else: ?>
                <?php foreach ($tugasList as $tugas): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            <?php echo htmlspecialchars($tugas['judul'] ?? 'N/A'); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-red-600">
                            <i class="bi bi-calendar-event mr-1"></i>
                            <?php echo date('d M Y', strtotime($tugas['deadline'] ?? 'now')); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            <?php 
                                    // Gunakan Map kita!
                                    $deptId = $tugas['id_departemen'] ?? null;
                                    echo htmlspecialchars($deptMap[$deptId] ?? 'N/A');
                                    ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-700">
                            <?php 
                                    // Gunakan Map kita!
                                    $userId = $tugas['id_user_pembuat'] ?? null;
                                    echo htmlspecialchars($userMap[$userId] ?? 'N/A');
                                    ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php 
                                // Sesuai UI Brief: Badge Warna untuk Status
                                $status = $tugas['status'] ?? 'Belum';
                                $badgeClass = 'bg-gray-100 text-gray-800'; // Default
                                if ($status == 'Selesai') {
                                    $badgeClass = 'bg-green-100 text-green-800';
                                } elseif ($status == 'Proses') {
                                    $badgeClass = 'bg-blue-100 text-blue-800';
                                } elseif ($status == 'Belum') {
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                }
                                ?>
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="detail.php?id=<?php echo $tugas['id_tugas']; // Asumsi PK 'id_tugas' ?>"
                                class="text-green-600 hover:text-green-800 p-2 rounded-md bg-green-50 hover:bg-green-100"
                                title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                            <a href="form.php?id=<?php echo $tugas['id_tugas']; ?>"
                                class="text-primary hover:text-primary-hover p-2 rounded-md bg-blue-50 hover:bg-blue-100"
                                title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="../proses/tugas.php?aksi=hapus&id=<?php echo $tugas['id_tugas']; ?>"
                                class="text-red-500 hover:text-red-700 p-2 rounded-md bg-red-50 hover:bg-red-100"
                                title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
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
// 7. Panggil Footer
require_once '../layout/footer.php'; 
?>