<?php
// client/proses/auth.php

// ==========================================================
// ATURAN EMAS: Muat config.php PERTAMA!
// ==========================================================
require_once '../config/config.php';
// ==========================================================

// Panggil file core lainnya
require_once '../core/Client.php';
require_once '../core/Auth.php';
require_once '../core/Helper.php';

// 1. Pastikan ini adalah request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Ambil data dari form login.php
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 3. Buat objek Client
        $client = new Client();
        
        // 4. Panggil API (Client.php akan memanggil ...?table=users)
        $users = $client->get('users');

        // 5. Cek apakah API mengembalikan error
        if (isset($users['status']) && $users['status'] == 'error') {
            // Misal: "Gagal decode JSON", "Query Gagal", "Koneksi DB Gagal"
            Helper::setFlashMessage('error', 'Server API Error: ' . $users['message']);
            Helper::redirect('pages/login.php');
        }

        // 6. Loop data user untuk mencari yang cocok
        $loggedInUser = null;
        if (!empty($users) && is_array($users)) {
            foreach ($users as $user) {
                // Pastikan kolom email dan password ada
                if (isset($user['email']) && isset($user['password'])) {
                    
                    // 7. Logika Pengecekan
                    // (Ini asumsi password di DB tidak di-hash. Untuk UAS, ini oke)
                    if ($user['email'] == $email && $user['password'] == $password) {
                        $loggedInUser = $user;
                        break; // Ditemukan! Hentikan loop
                    }
                }
            }
        }

        // 8. Proses Hasil Login
        if ($loggedInUser) {
            // BERHASIL LOGIN
            Auth::setLoginSession($loggedInUser);
            // Redirect ke index.php (nanti index.php yang urus ke dashboard)
            Helper::redirect('index.php');
        } else {
            // GAGAL LOGIN (Email/password salah)
            Helper::setFlashMessage('error', 'Email atau password yang Anda masukkan salah.');
            Helper::redirect('pages/login.php');
        }

    } catch (Exception $e) {
        // GAGAL KONEKSI (Misal server API mati total)
        Helper::setFlashMessage('error', 'Koneksi ke server gagal: ' . $e->getMessage());
        Helper::redirect('pages/login.php');
    }

} else {
    // Jika file ini diakses langsung (bukan via POST), tendang kembali
    Helper::redirect('pages/login.php');
}
?>