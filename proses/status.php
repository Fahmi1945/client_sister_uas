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
    $new_status = strtolower($_GET['status'] ?? ''); // Ambil status baru (belum, proses, selesai)
    $currentUserId = Auth::getUserData()['id_user'] ?? 0;

    // Ambil catatan dari input user, jika kosong gunakan default
    $catatanUser = trim($_GET['catatan'] ?? '');
    $catatan = !empty($catatanUser) ? $catatanUser : 'Status diubah menjadi ' . strtoupper($new_status) . ' oleh karyawan.';

    // Validasi input
    if (!$id_tugas || !in_array($new_status, ['belum', 'proses', 'selesai'])) {
        Helper::setFlashMessage('error', 'Aksi atau ID tugas tidak valid.');
        Helper::redirect($redirectUrl);
    }

    // 1. Cek apakah tugas ini ada dan apakah user berhak mengaksesnya
    $responseTugas = $client->get('tugas', $id_tugas);
    $tugasData = $responseTugas[0] ?? null;

    if (!$tugasData) {
        Helper::setFlashMessage('error', 'Tugas tidak ditemukan.');
        Helper::redirect($redirectUrl);
    }

    // 2. Cek apakah sudah ada record status_tugas untuk kombinasi id_tugas + id_user ini
    //    Gunakan endpoint dengan query parameter (sesuaikan dengan API Anda)
    $responseStatusTugas = $client->get('status_tugas', null, [
        'id_tugas' => $id_tugas,
        'id_user' => $currentUserId
    ]);

    $existingStatus = null;
    if (isset($responseStatusTugas['data']) && is_array($responseStatusTugas['data'])) {
        // Cari record yang cocok
        foreach ($responseStatusTugas['data'] as $record) {
            if ($record['id_tugas'] == $id_tugas && $record['id_user'] == $currentUserId) {
                $existingStatus = $record;
                break;
            }
        }
    }

    $response = null;

    if ($existingStatus) {
        // 3A. UPDATE record yang sudah ada di status_tugas
        $id_status = $existingStatus['id_status'];

        $updatePayload = [
            'status' => $new_status,
            'catatan' => $catatan
            // updated_at akan otomatis terupdate (ON UPDATE CURRENT_TIMESTAMP)
        ];

        $response = $client->put('status_tugas', $id_status, $updatePayload);
        $successMessage = 'Status tugas berhasil diubah menjadi: ' . strtoupper($new_status);

    } else {
        // 3B. INSERT record baru di status_tugas (jika belum ada)
        $insertPayload = [
            'id_tugas' => $id_tugas,
            'id_user' => $currentUserId,
            'status' => $new_status,
            'catatan' => $catatan
        ];

        $response = $client->post('status_tugas', $insertPayload);
        $successMessage = 'Status tugas berhasil dibuat: ' . strtoupper($new_status);
    }

    // 4. Proses Hasil
    if (isset($response['status']) && $response['status'] == 'success') {
        Helper::setFlashMessage('success', $successMessage);
    } else {
        $message = $response['message'] ?? 'Gagal memperbarui status tugas.';
        Helper::setFlashMessage('error', 'Gagal: ' . $message);
    }

    Helper::redirect($redirectUrl);

} else {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}