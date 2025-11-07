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

    if (!$id_tugas || !in_array($new_status, ['belum', 'proses', 'selesai'])) {
        Helper::setFlashMessage('error', 'Aksi atau ID tugas tidak valid.');
        Helper::redirect($redirectUrl);
    }
    
    // 1. Panggil API untuk mengambil data tugas lama (PENTING untuk PUT)
    $responseOldTugas = $client->get('tugas', $id_tugas);
    $oldTugasData = $responseOldTugas[0] ?? null;

    if (!$oldTugasData) {
        Helper::setFlashMessage('error', 'Tugas tidak ditemukan di server.');
        Helper::redirect($redirectUrl);
    }

    // PENTING: Cek apakah karyawan ini adalah penerima tugas (Asumsi id_pembuat adalah penerima)
    // Ubah logika ini jika struktur DB Anda memiliki kolom id_penerima yang berbeda
    if ($oldTugasData['id_pembuat'] != $currentUserId) { 
         Helper::setFlashMessage('error', 'Anda tidak berhak mengubah status tugas ini.');
         Helper::redirect($redirectUrl);
    }

    // 2. Buat Payload untuk PUT (memperbarui tugas.status)
    $updatePayload = [
        // Kita hanya perlu mengirim kolom yang diubah.
        'status' => $new_status 
    ];

    // 3. Kirim PUT ke tabel tugas (Update Source of Truth)
    $responseTugasUpdate = $client->put('tugas', $id_tugas, $updatePayload); 

    // 4. Kirim POST ke tabel status_tugas (Buat Log/Riwayat)
    $logPayload = [
        'id_tugas' => $id_tugas,
        'id_user' => $currentUserId,
        'status' => $new_status,
        'catatan' => 'Status diubah oleh karyawan melalui papan tugas.'
    ];
    $client->post('status_tugas', $logPayload); // Log ini berjalan secara asinkron

    // 5. Proses Hasil
    if (isset($responseTugasUpdate['status']) && $responseTugasUpdate['status'] == 'success') {
        Helper::setFlashMessage('success', 'Status tugas berhasil diubah menjadi: ' . strtoupper($new_status));
    } else {
        $message = $responseTugasUpdate['message'] ?? 'Gagal update status tugas.';
        Helper::setFlashMessage('error', 'Gagal memperbarui status: ' . $message);
    }
    
    Helper::redirect($redirectUrl);
} else {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
    Helper::redirect($redirectUrl);
}
?>