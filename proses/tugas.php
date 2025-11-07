<?php
// client/proses/tugas.php

// 1. Muat File Inti
require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

Auth::startSession();
Auth::checkLogin('admin'); 

$client = new Client();
$redirectUrl = 'pages/tugas/list.php'; 
$aksi = $_REQUEST['aksi'] ?? null;

// ==========================================================
// LOGIKA TAMBAH (CREATE / POST)
// ==========================================================
if ($aksi == 'tambah') {
    
    $data = [
        "judul" => $_POST['judul'] ?? '', 
        "deskripsi" => $_POST['deskripsi'] ?? '',
        "deadline" => $_POST['deadline'] ?? null,
        "id_pembuat" => $_POST['id_pembuat'] ?? null, // Diambil dari hidden input
        "id_departemen" => $_POST['id_departemen'] ?? null
    ];
    
    // Konversi nilai 'kosong' menjadi NULL
    if (empty($data['deadline'])) unset($data['deadline']);
    if (empty($data['id_departemen'])) $data['id_departemen'] = null; // Biarkan null jika kosong
    
    $response = $client->post('tugas', $data); 

    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Tugas baru berhasil ditambahkan.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat menambah tugas.';
         Helper::setFlashMessage('error', 'Gagal menambah tugas: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA UBAH (UPDATE / PUT)
// ==========================================================
if ($aksi == 'ubah') {
    
    $id_tugas = $_POST['id_tugas'] ?? null;
    if (!$id_tugas) {
        Helper::setFlashMessage('error', 'ID tugas tidak ditemukan.');
        Helper::redirect($redirectUrl);
    }
    
    $data = [
        "judul" => $_POST['judul'] ?? '', 
        "deskripsi" => $_POST['deskripsi'] ?? '',
        "deadline" => $_POST['deadline'] ?? null,
        "id_pembuat" => $_POST['id_pembuat'] ?? null, 
        "id_departemen" => $_POST['id_departemen'] ?? null
    ];

    // Konversi nilai 'kosong' menjadi NULL
    if (empty($data['deadline'])) unset($data['deadline']);
    if (empty($data['id_departemen'])) $data['id_departemen'] = null;
    
    $response = $client->put('tugas', $id_tugas, $data); 

    if (isset($response['status']) && $response['status'] == 'success') {
         Helper::setFlashMessage('success', 'Data tugas berhasil diperbarui.');
    } else {
         $message = $response['message'] ?? 'Kesalahan API saat memperbarui tugas.';
         Helper::setFlashMessage('error', 'Gagal memperbarui data tugas: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
}

// ==========================================================
// LOGIKA HAPUS (DELETE)
// ==========================================================
if ($aksi == 'hapus') {
    
    $id_tugas = $_GET['id'] ?? null;
    if (!$id_tugas) {
        Helper::setFlashMessage('error', 'ID tugas tidak ditemukan untuk dihapus.');
        Helper::redirect($redirectUrl);
    }
    
    $response = $client->delete('tugas', $id_tugas); 

    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Tugas berhasil dihapus.');
    } else {
        $message = $response['message'] ?? 'Terjadi kesalahan tak terduga saat menghapus.';
        Helper::setFlashMessage('error', 'Gagal menghapus tugas: ' . $message);
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