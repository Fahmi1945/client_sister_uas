<?php
// client/proses/status.php

// 1. Panggil SEMUA file inti
require_once '../core/Client.php';
require_once '../core/Helper.php';
require_once '../core/Auth.php';

// 2. (WAJIB) Keamanan: Pastikan yang akses adalah karyawan
Auth::checkLogin('karyawan'); 

// 3. Cek parameter dari URL
if (isset($_GET['id']) && isset($_GET['status'])) {
    
    $id_tugas = $_GET['id'];
    $status_baru = $_GET['status'];
    $currentUserId = Auth::getUserData()['id_user'];
    
    $client = new Client();

    try {
        // 4. (SANGAT PENTING) Ambil data tugas lama
        $apiResponse = $client->get('tugas', $id_tugas);
        $tugas = $apiResponse[0] ?? null;

        if (!$tugas) {
            Helper::setFlashMessage('error', 'Tugas tidak ditemukan.');
            Helper::redirect('pages/status_tugas/board.php');
        }
        
        // 5. (SANGAT PENTING) Cek Kepemilikan Tugas
        if ($tugas['id_user_penerima'] != $currentUserId) {
            Helper::setFlashMessage('error', 'Anda tidak memiliki akses ke tugas ini.');
            Helper::redirect('pages/error403.php'); // Lempar ke 403
        }

        // 6. Ubah status di data lama
        $tugas['status'] = $status_baru;

        // 7. Kirim (PUT) SELURUH data kembali ke API
        $response = $client->put('tugas', $id_tugas, $tugas);

        if (isset($response['status']) && $response['status'] == 'success') {
            Helper::setFlashMessage('success', 'Status tugas berhasil diperbarui.');
        } else {
            Helper::setFlashMessage('error', 'Gagal memperbarui status: ' . ($response['message'] ?? 'Error'));
        }

    } catch (Exception $e) {
        Helper::setFlashMessage('error', 'Koneksi ke server gagal: ' . $e->getMessage());
    }

} else {
    Helper::setFlashMessage('error', 'Aksi tidak valid.');
}

// 8. Selalu kembalikan ke papan tugas
Helper::redirect('pages/status_tugas/board.php');
?>