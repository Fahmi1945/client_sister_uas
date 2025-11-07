<?php
// client/proses/status.php

// 1. Muat File Inti
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php';

Auth::startSession();
// Pastikan hanya karyawan yang bisa mengakses file proses status
Auth::checkLogin('karyawan');

$client = new Client();
$redirectUrl = 'pages/status_tugas/board.php';
$aksi = $_REQUEST['aksi'] ?? null;

if ($aksi == 'ubah') {

    // Ambil data dari parameter URL (GET request)
    $id_tugas = $_GET['id'] ?? null;
    $new_status = strtolower($_GET['status'] ?? '');
    $catatanUser = trim($_GET['catatan'] ?? '');
    $currentUserId = Auth::getUserData()['id_user'] ?? 0;

    // Default catatan jika input kosong
    $catatan = !empty($catatanUser) ? $catatanUser : 'Status diubah menjadi ' . strtoupper($new_status);

    // Validasi ID Tugas dan Status yang Diizinkan
    $validStatuses = ['belum', 'proses', 'selesai'];
    if (!$id_tugas || !in_array($new_status, $validStatuses)) {
        Helper::setFlashMessage('error', 'Aksi, ID tugas, atau status tidak valid.');
        Helper::redirect($redirectUrl);
    }

    // --- 1. Cek Keamanan: Pastikan Tugas Ada (Minimal Cek Keberadaan) ---
    // Pastikan tugas benar-benar ada sebelum mencatat statusnya
    $responseTugas = $client->get('tugas', $id_tugas);
    if (!is_array($responseTugas) || isset($responseTugas['status']) || empty($responseTugas[0])) {
        Helper::setFlashMessage('error', 'Tugas yang dimaksud tidak ditemukan atau error API.');
        Helper::redirect($redirectUrl);
    }

    // --- 2. Buat Payload untuk Log Status Baru ---
    // Kolom sesuai tabel `status_tugas` Anda: id_tugas, id_user, status, catatan
    // Mencatat status ini untuk user yang sedang login (currentUserId)
    $insertPayload = [
        'id_tugas' => $id_tugas,
        'id_user' => $currentUserId,
        'status' => $new_status,
        'catatan' => $catatan
    ];

    // --- 3. Kirim POST (INSERT) ke status_tugas ---
    // Karena board.php menggunakan status_tugas terakhir per id_user, kita hanya perlu INSERT log baru.
    $response = $client->post('status_tugas', $insertPayload);

    // --- 4. Proses Hasil ---
    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', 'Status tugas berhasil dicatat sebagai: ' . strtoupper($new_status));
    } else {
        $message = $response['message'] ?? 'Gagal mencatat status baru. Coba lagi.';
        Helper::setFlashMessage('error', 'Gagal: ' . $message);
    }

    Helper::redirect($redirectUrl);

} else {
    // Aksi tidak valid (selain 'ubah')
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}
?>