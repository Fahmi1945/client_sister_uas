<?php
// client/proses/departemen.php

// 1. Panggil SEMUA file inti yang dibutuhkan
require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

// 2. (WAJIB) Cek Keamanan: Pastikan yang akses adalah admin
Auth::checkLogin('admin'); 

// 3. Logika Hapus (dari link di list.php)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    
    $id = $_GET['id'];
    $client = new Client();
    $response = $client->delete('departemen', $id); 

    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Departemen berhasil dihapus.');
    } else {
        Helper::setFlashMessage('error', 'Gagal menghapus departemen: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/departemen/list.php');
}

// 4. Logika Tambah (dari form.php)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    
    $data = [
        "nama_departemen" => $_POST['nama_departemen'],
        "deskripsi" => $_POST['deskripsi'],
        "lokasi" => $_POST['lokasi']
    ];
    
    $client = new Client();
    $response = $client->post('departemen', $data);
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Departemen baru berhasil ditambahkan.');
    } else {
         Helper::setFlashMessage('error', 'Gagal menambah departemen: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/departemen/list.php');
}

// 5. Logika Ubah (dari form.php)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'ubah') {
    
    $id = $_POST['id_departemen']; // Ambil ID dari form
    $data = [
        "nama_departemen" => $_POST['nama_departemen'],
        "deskripsi" => $_POST['deskripsi'],
        "lokasi" => $_POST['lokasi']
    ];

    $client = new Client();
    $response = $client->put('departemen', $id, $data); // Panggil metode PUT
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data departemen berhasil diperbarui.');
    } else {
         Helper::setFlashMessage('error', 'Gagal memperbarui data: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/departemen/list.php');
}

?>