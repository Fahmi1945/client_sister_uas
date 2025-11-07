<?php
// client/proses/departemen.php

// 1. Muat File Inti
require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

// 2. Keamanan: Cek sesi dan role Admin
Auth::startSession();
Auth::checkLogin('admin'); 

// Inisialisasi
$client = new Client();
$redirectUrl = 'pages/departemen/list.php'; 
$aksi = $_REQUEST['aksi'] ?? null;

// ==========================================================
// LOGIKA TAMBAH (CREATE / POST)
// ==========================================================
if ($aksi == 'tambah') {
    
    $data = [
        "nama_departemen" => $_POST['nama_departemen'] ?? '', 
        "deskripsi" => $_POST['deskripsi'] ?? '',
        "lokasi" => $_POST['lokasi'] ?? ''
    ];
    
    $response = $client->post('departemen', $data); 

    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Departemen baru berhasil ditambahkan.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat menambah departemen.';
         Helper::setFlashMessage('error', 'Gagal menambah departemen: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA UBAH (UPDATE / PUT)
// ==========================================================
if ($aksi == 'ubah') {
    
    $id_departemen = $_POST['id_departemen'] ?? null;
    
    if (!$id_departemen) {
        Helper::setFlashMessage('error', 'ID departemen tidak ditemukan.');
        Helper::redirect($redirectUrl);
    }
    
    $data = [
        "nama_departemen" => $_POST['nama_departemen'] ?? '', 
        "deskripsi" => $_POST['deskripsi'] ?? '',
        "lokasi" => $_POST['lokasi'] ?? ''
    ];

    $response = $client->put('departemen', $id_departemen, $data); 

    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data departemen berhasil diperbarui.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat memperbarui departemen.';
         Helper::setFlashMessage('error', 'Gagal memperbarui data departemen: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA HAPUS (DELETE)
// ==========================================================
if ($aksi == 'hapus') {
    
    $id_departemen = $_GET['id'] ?? null;
    
    if (!$id_departemen) {
        Helper::setFlashMessage('error', 'ID departemen tidak ditemukan untuk dihapus.');
        Helper::redirect($redirectUrl);
    }
    
    $response = $client->delete('departemen', $id_departemen); 

    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Departemen berhasil dihapus.');
    } else {
        $message = $response['message'] ?? 'Terjadi kesalahan tak terduga saat menghapus.';
        Helper::setFlashMessage('error', 'Gagal menghapus departemen: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}


// ==========================================================
// LOGIKA DEFAULT
// ==========================================================
if (!$aksi) {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}
?>