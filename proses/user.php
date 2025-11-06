<?php
// proses/user.php

// (File ini akan dipanggil oleh form atau link)

// 1. Panggil Client
require_once '../core/Client.php';
// Anda mungkin juga perlu memanggil helper untuk session dan redirect
// require_once '../core/Helper.php';
// require_once '../core/Auth.php';

// Pastikan hanya admin yang bisa (contoh sederhana)
// session_start();
// if (Auth::getRole() !== 'admin') {
//     Helper::redirect('../pages/error403.php');
// }

// 2. Periksa aksi (misal dari URL ?aksi=hapus&id=12)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    
    // Ambil ID dari URL
    $id_user = $_GET['id'];
    
    // 3. Buat objek Client
    $client = new Client();
    
    // 4. Panggil metode 'delete'
    // Asumsikan tabel di server teman Anda namanya 'users'
    $response = $client->delete('users', $id_user); 

    // 5. Proses balasan dari server
    if (isset($response['status']) && $response['status'] == 'success') {
        // Jika sukses, set pesan dan redirect
        // Helper::setFlashMessage('success', 'User berhasil dihapus.');
        echo "Sukses: " . $response['message']; // Debug
        // Helper::redirect('../pages/users/list.php');
    } else {
        // Jika gagal
        // Helper::setFlashMessage('error', 'Gagal menghapus user: ' . $response['message']);
        echo "Error: " . $response['message']; // Debug
        // Helper::redirect('../pages/users/list.php');
    }
}

// Tambahkan logika untuk 'tambah' (POST) dan 'ubah' (PUT) di sini...

// Contoh untuk Tambah User (dari form POST)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    
    // 1. Siapkan data dari form
    $data = [
        "username" => $_POST['username'],
        "nama_lengkap" => $_POST['nama_lengkap'],
        "password" => $_POST['password'], // (Idealnya password di-hash dulu)
        "role" => $_POST['role']
    ];
    
    // 2. Buat Client dan panggil 'post'
    $client = new Client();
    $response = $client->post('users', $data); // Kirim ke tabel 'users'
    
    // 3. Redirect dengan pesan sukses/gagal...
    if (isset($response['status']) && $response['status'] == 'success') {
         echo "Sukses: " . $response['message'];
         // ... redirect ...
    } else {
         echo "Error: " . $response['message'];
         // ... redirect ...
    }
    if (isset($_POST['aksi']) && $_POST['aksi'] == 'ubah') {
    
    $id_user = $_POST['id_user']; // Ambil ID dari form
    
    // Siapkan data TANPA password dulu
    $data = [
        "username" => $_POST['username'], // Asumsi Anda akan menambahkan field ini
        "nama_lengkap" => $_POST['nama_lengkap'],
        "email" => $_POST['email'],
        "role" => $_POST['role'],
        "departemen" => $_POST['departemen']
    ];

    // (INI YANG BARU) Cek apakah password diisi
    if (!empty($_POST['password'])) {
        // Jika diisi, baru tambahkan password ke data yang akan dikirim
        $data['password'] = $_POST['password'];
    }
    // Jika $_POST['password'] kosong, kita tidak mengirimkannya ke API
    // sehingga server tidak akan meng-update-nya.

    $client = new Client();
    $response = $client->put('users', $id_user, $data); // Panggil metode PUT
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data user berhasil diperbarui.');
    } else {
         Helper::setFlashMessage('error', 'Gagal memperbarui data: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/users/list.php');
}
}