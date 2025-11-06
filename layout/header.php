<?php
// client/layout/header.php

// 1. Selalu panggil file inti di bagian paling atas
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Helper.php';

// 2. Mulai sesi
Auth::startSession();

// 3. Ambil data user (jika login), ini akan kita gunakan di navbar
$currentUser = Auth::getUserData();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $pageTitle ?? "Sistem Manajemen Tugas"; ?></title>

    <link rel="preconnect" href="[https://fonts.googleapis.com](https://fonts.googleapis.com)">
    <link rel="preconnect" href="[https://fonts.gstatic.com](https://fonts.gstatic.com)" crossorigin>
    <link
        href="[https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap)"
        rel="stylesheet">

    <script src="[https://cdn.tailwindcss.com](https://cdn.tailwindcss.com)"></script>
    <script>
    // 5. Konfigurasi Tailwind kustom
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary': {
                        'DEFAULT': '#2563EB',
                        'hover': '#1D4ED8'
                    },
                },
                fontFamily: {
                    'poppins': ['Poppins', 'sans-serif'],
                    'inter': ['Inter', 'sans-serif']
                }
            }
        }
    }
    </script>

    <link rel="stylesheet"
        href="[https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css](https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css)">
    <link rel="stylesheet" href="/assets/css/style.css">

</head>

<body class="bg-gray-100 font-inter text-gray-900">
    <div class="flex min-h-screen">
        <?php require_once __DIR__ . '/sidebar.php'; ?>
        <div class="flex-1 flex flex-col">
            <?php require_once __DIR__ . '/navbar.php'; ?>
            <main class="flex-1 p-6 md:p-8 overflow-y-auto">
                <?php Helper::displayFlashMessage('success'); ?>
                <?php Helper::displayFlashMessage('error'); ?>
                ```

                #### 2. `proses/user.php` (Paling Penting: Aktifkan Keamanan & Redirect)

                File `proses/user.php` Anda secara logis sudah benar, tetapi Anda masih membiarkan fitur keamanan dan
                *redirect*-nya dalam bentuk komentar (mungkin untuk *debugging*). Ini sangat berbahaya karena siapa pun
                bisa menghapus/menambah user tanpa login.

                Anda harus mengaktifkan `Auth.php`, `Helper.php`, dan `checkLogin`.

                **`proses/user.php` (Versi Lengkap dan Aman):**
                ```php
                <?php
// proses/user.php

// 1. Panggil SEMUA file inti yang dibutuhkan
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

// 2. (WAJIB) Cek Keamanan: Pastikan yang akses adalah admin
Auth::checkLogin('admin'); // Ini akan otomatis redirect jika belum login / bukan admin

// 3. Periksa aksi (misal dari URL ?aksi=hapus&id=12)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    
    $id_user = $_GET['id'];
    $client = new Client();
    $response = $client->delete('users', $id_user); 

    // 5. Proses balasan dari server (Gunakan FlashMessage dan Redirect)
    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'User berhasil dihapus.');
    } else {
        Helper::setFlashMessage('error', 'Gagal menghapus user: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/users/list.php');
}

// 4. Contoh untuk Tambah User (dari form POST)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    
    $data = [
        "username" => $_POST['username'],
        "nama_lengkap" => $_POST['nama_lengkap'],
        "password" => $_POST['password'], // (Server teman Anda harusnya menangani hashing)
        "role" => $_POST['role']
    ];
    
    $client = new Client();
    $response = $client->post('users', $data);
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'User baru berhasil ditambahkan.');
    } else {
         Helper::setFlashMessage('error', 'Gagal menambah user: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/users/list.php');
}

// 5. Logika untuk 'ubah' (PUT) akan mirip dengan 'tambah'
if (isset($_POST['aksi']) && $_POST['aksi'] == 'ubah') {
    
    $id_user = $_POST['id_user']; // Pastikan form Anda mengirimkan ID
    $data = [
        "username" => $_POST['username'],
        "nama_lengkap" => $_POST['nama_lengkap'],
        "role" => $_POST['role']
        // Anda mungkin tidak ingin mengirim password saat update, atau ada logika khusus
    ];

    $client = new Client();
    $response = $client->put('users', $id_user, $data); // Panggil metode PUT
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data user berhasil diperbarui.');
    } else {
         Helper::setFlashMessage('error', 'Gagal memperbarui data: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/users/list.php');
}

?>