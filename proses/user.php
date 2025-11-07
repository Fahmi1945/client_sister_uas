<?php
// client/proses/user.php

// 1. ATURAN EMAS: Muat config.php PERTAMA
require_once '../config/config.php';

// 2. Muat Core Files
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

// 3. Keamanan: Pastikan sesi dimulai dan hanya Admin yang bisa mengakses file ini
Auth::startSession();
Auth::checkLogin('admin'); 

// Inisialisasi Client
$client = new Client();
$redirectUrl = 'pages/users/list.php'; // Tujuan redirect

// Ambil aksi dari POST atau GET
$aksi = $_REQUEST['aksi'] ?? null;

// ==========================================================
// LOGIKA HAPUS (DELETE)
// ==========================================================
if ($aksi == 'hapus') {
    
    // Ambil ID dari URL (GET)
    $id_user = $_GET['id'] ?? null;
    
    if (!$id_user) {
        Helper::setFlashMessage('error', 'ID user tidak ditemukan untuk dihapus.');
        Helper::redirect($redirectUrl);
    }
    
    // Panggil metode DELETE pada Client
    $response = $client->delete('users', $id_user); 

    // Proses balasan dari server
    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'User berhasil dihapus.');
    } else {
        $message = $response['message'] ?? 'Terjadi kesalahan tak terduga saat menghapus.';
        Helper::setFlashMessage('error', 'Gagal menghapus user: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA TAMBAH (CREATE / POST)
// ==========================================================
if ($aksi == 'tambah') {
    
    // Ambil data dari FORM (POST)
    $data = [
        "username" => $_POST['username'] ?? '',
        "nama_lengkap" => $_POST['nama_lengkap'] ?? '',
        "email" => $_POST['email'] ?? '',
        "password" => $_POST['password'] ?? '', // Server yang harusnya melakukan hashing
        "role" => $_POST['role'] ?? 'karyawan',
        "departemen" => $_POST['departemen'] ?? ''
    ];

    // Panggil metode POST pada Client
    $response = $client->post('users', $data); 

    // Proses balasan
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'User baru berhasil ditambahkan.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat menambah user.';
         Helper::setFlashMessage('error', 'Gagal menambah user: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA UBAH (UPDATE / PUT)
// ==========================================================
if ($aksi == 'ubah') {
    
    // Ambil ID dan data dari FORM (POST)
    $id_user = $_POST['id_user'] ?? null;
    
    if (!$id_user) {
        Helper::setFlashMessage('error', 'ID user tidak ditemukan untuk diperbarui.');
        Helper::redirect($redirectUrl);
    }
    
    // Siapkan data yang akan dikirim (tanpa password awal)
    $data = [
        "username" => $_POST['username'] ?? '',
        "nama_lengkap" => $_POST['nama_lengkap'] ?? '',
        "email" => $_POST['email'] ?? '',
        "role" => $_POST['role'] ?? 'karyawan',
        "departemen" => $_POST['departemen'] ?? ''
    ];

    // Cek apakah password diisi (jika tidak kosong, tambahkan ke payload PUT)
    if (!empty($_POST['password'])) {
        $data['password'] = $_POST['password'];
    }

    // Panggil metode PUT pada Client
    $response = $client->put('users', $id_user, $data); 

    // Proses balasan
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data user berhasil diperbarui.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat memperbarui user.';
         Helper::setFlashMessage('error', 'Gagal memperbarui data user: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA DEFAULT (Jika tidak ada aksi)
// ==========================================================
if (!$aksi) {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}
?>