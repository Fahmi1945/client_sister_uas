<?php
// client/proses/status.php

// 1. Muat File Inti
require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

Auth::startSession();
Auth::checkLogin('karyawan');

$client = new Client();
$redirectUrl = 'pages/status_tugas/board.php';
$aksi = $_REQUEST['aksi'] ?? null;

if ($aksi == 'ubah') {

    $id_tugas = $_GET['id'] ?? null;
    $new_status = strtolower($_GET['status'] ?? '');
    $catatanUser = trim($_GET['catatan'] ?? '');
    $currentUserId = Auth::getUserData()['id_user'] ?? 0;

    // Default catatan jika kosong
    $catatan = !empty($catatanUser) ? $catatanUser : 'Status diubah menjadi ' . strtoupper($new_status);

    // Validasi input
    if (!$id_tugas || !in_array($new_status, ['belum', 'proses', 'selesai'])) {
        Helper::setFlashMessage('error', 'Aksi atau ID tugas tidak valid.');
        Helper::redirect($redirectUrl);
    }

    // 1. (OPTIMASI) Cek apakah record status_tugas sudah ada untuk tugas ini dan user ini
    // KARENA API Anda TIDAK PUNYA endpoint search/filter, kita harus GET SEMUA dan filter di sini.
    $allStatusTugas = $client->get('status_tugas');
    $existingStatus = null;

    if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
        foreach ($allStatusTugas as $record) {
            // Asumsi: Kita mencari record yang id_tugas DAN id_user sama
            if ($record['id_tugas'] == $id_tugas && $record['id_user'] == $currentUserId) {
                $existingStatus = $record;
                break;
            }
        }
    }

    $response = null;
    $successMessage = '';

    if ($existingStatus) {
        // --- 2A. UPDATE record yang sudah ada ---
        $id_status = $existingStatus['id_status'];
        $updatePayload = [
            'status' => $new_status,
            'catatan' => $catatan
        ];

        $response = $client->put('status_tugas', $id_status, $updatePayload);
        $successMessage = 'Status dan catatan berhasil diperbarui.';

    } else {
        // --- 2B. INSERT record baru (Tugas baru di-claim) ---
        $insertPayload = [
            'id_tugas' => $id_tugas,
            'id_user' => $currentUserId,
            'status' => $new_status,
            'catatan' => $catatan
        ];

        $response = $client->post('status_tugas', $insertPayload);
        $successMessage = 'Tugas berhasil di-klaim dan status awal dibuat.';
    }

    // 3. Proses Hasil
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
?>