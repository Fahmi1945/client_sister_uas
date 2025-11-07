<?php
// client/pages/status_tugas/board.php

// 1. Muat File Inti dan Keamanan
require_once '../../core/Client.php';
require_once '../../core/Auth.php';
require_once '../../core/Helper.php';
require_once '../../config/config.php'; 

Auth::checkLogin('karyawan'); 

$currentUserId = Auth::getUserData()['id_user'] ?? 0;
$client = new Client();

// 2. Ambil data dari API
$allTugas = $client->get('tugas'); 
$allUsers = $client->get('users'); 
$allStatusTugas = $client->get('status_tugas'); 

// ... (Cek error API, userMap) ...
$userMap = [];
if (is_array($allUsers) && !isset($allUsers['status'])) {
    foreach ($allUsers as $user) {
        $userMap[$user['id_user']] = $user['nama'];
    }
}


// 3. Kritis: Buat Status Map (id_tugas -> Status TERAKHIR)
$statusMap = [];
if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    foreach ($allStatusTugas as $status) {
        // Hanya ambil status milik user yang login
        if ($status['id_user'] == $currentUserId) {
            $id_tugas = $status['id_tugas'];
            
            // Logika untuk mengambil record TERAKHIR (berdasarkan updated_at)
            // Karena tidak bisa order by updated_at di GET ALL, 
            // kita bandingkan waktu di PHP. Asumsi updated_at ada.
            
            $is_newer = true;
            if (isset($statusMap[$id_tugas])) {
                // Jika sudah ada record, bandingkan waktu
                $current_time = strtotime($statusMap[$id_tugas]['updated_at']);
                $new_time = strtotime($status['updated_at']);
                if ($current_time > $new_time) {
                    $is_newer = false; // Record yang sekarang lebih baru
                }
            }

            if ($is_newer) {
                $statusMap[$id_tugas] = $status;
            }
        }
    }
}


// 4. Filter dan Gabungkan tugas
$tugasBelum = [];
$tugasProses = [];
$tugasSelesai = [];

if (is_array($allTugas) && !isset($allTugas['status'])) {
    foreach ($allTugas as $tugas) {
        $id_tugas = $tugas['id_tugas'];
        
        // HANYA proses tugas yang sudah di-claim (ada record status_tugas terakhir)
        if (isset($statusMap[$id_tugas])) {
            $statusData = $statusMap[$id_tugas];
            
            // Gabungkan data status terbaru ke data tugas
            $tugas['status_current'] = strtolower($statusData['status'] ?? 'belum');
            $tugas['catatan'] = $statusData['catatan'] ?? '';
            $tugas['updated_at'] = $statusData['updated_at'] ?? '';
            $tugas['id_status'] = $statusData['id_status'] ?? 0;
            
            // Pisahkan berdasarkan status
            if ($tugas['status_current'] == 'proses') {
                $tugasProses[] = $tugas;
            } elseif ($tugas['status_current'] == 'selesai') {
                $tugasSelesai[] = $tugas;
            } else {
                $tugasBelum[] = $tugas;
            }
        }
    }
}

$pageTitle = "Papan Tugas Saya";
require_once '../../layout/header.php';
?>