<?php
// client/proses/status.php

// 1. Muat File Inti
require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

Auth::startSession();
Auth::checkLogin('karyawan'); // HANYA karyawan yang bisa mengubah status

$client = new Client();
$redirectUrl = 'pages/status_tugas/board.php'; 
$aksi = $_REQUEST['aksi'] ?? null;

if ($aksi == 'ubah') {
    
    $id_tugas = $_GET['id'] ?? null;
    $new_status = strtolower($_GET['status'] ?? ''); 
    $catatanUser = trim($_GET['catatan'] ?? '');
    $currentUserId = Auth::getUserData()['id_user'] ?? 0;
    
    // Default catatan
    $catatan = !empty($catatanUser) ? $catatanUser : 'Status diubah menjadi ' . strtoupper($new_status);

    if (!$id_tugas || !in_array($new_status, ['belum', 'proses', 'selesai'])) {
        Helper::setFlashMessage('error', 'Aksi atau ID tugas tidak valid.');
        Helper::redirect($redirectUrl);
    }
    
    // --- 1. Payload untuk Log Status Baru ---
    $insertPayload = [
        'id_tugas' => $id_tugas,
        'id_user' => $currentUserId,
        'status' => $new_status,
        'catatan' => $catatan
    ];
        
    // --- 2. Selalu Kirim POST (INSERT) ke status_tugas ---
    // Karena ini adalah tabel riwayat/log, setiap perubahan harus menjadi record baru.
    $response = $client->post('status_tugas', $insertPayload);
    
    // --- 3. Proses Hasil ---
    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Status tugas berhasil dicatat sebagai: ' . strtoupper($new_status));
    } else {
        $message = $response['message'] ?? 'Gagal mencatat status baru.';
        Helper::setFlashMessage('error', 'Gagal: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
    
} else {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}
?>