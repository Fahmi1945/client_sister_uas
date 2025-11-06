<?php
// client/proses/tugas.php

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
    $response = $client->delete('tugas', $id); 

    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Tugas berhasil dihapus.');
    } else {
        Helper::setFlashMessage('error', 'Gagal menghapus tugas: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/tugas/list.php');
}

// 4. Logika Tambah (dari form.php)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    
    // Kumpulkan semua data dari form
    $data = [
        "judul" => $_POST['judul'],
        "deskripsi" => $_POST['deskripsi'],
        "deadline" => $_POST['deadline'],
        "status" => $_POST['status'],
        "id_departemen" => $_POST['id_departemen'],
        "id_user_penerima" => $_POST['id_user_penerima'], // Karyawan yg ditugaskan
        "id_user_pembuat" => $_POST['id_user_pembuat']  // Admin yg login
    ];
    
    $client = new Client();
    $response = $client->post('tugas', $data); // Kirim ke endpoint /tugas
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Tugas baru berhasil ditambahkan.');
    } else {
         Helper::setFlashMessage('error', 'Gagal menambah tugas: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/tugas/list.php');
}

// 5. Logika Ubah (dari form.php)
if (isset($_POST['aksi']) && $_POST['aksi'] == 'ubah') {
    
    $id = $_POST['id_tugas']; // Ambil ID dari form
    
    // Kumpulkan semua data dari form
    $data = [
        "judul" => $_POST['judul'],
        "deskripsi" => $_POST['deskripsi'],
        "deadline" => $_POST['deadline'],
        "status" => $_POST['status'],
        "id_departemen" => $_POST['id_departemen'],
        "id_user_penerima" => $_POST['id_user_penerima'],
        "id_user_pembuat" => $_POST['id_user_pembuat']
    ];

    $client = new Client();
    $response = $client->put('tugas', $id, $data); // Panggil metode PUT
    
    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data tugas berhasil diperbarui.');
    } else {
         Helper::setFlashMessage('error', 'Gagal memperbarui data: ' . ($response['message'] ?? 'Unknown error'));
    }
    Helper::redirect('pages/tugas/list.php');
}
?>