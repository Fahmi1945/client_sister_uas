<?php
// client/proses/auth.php

// 1. Panggil file inti
require_once '../core/Client.php';
require_once '../core/Auth.php';
require_once '../core/Helper.php';

// 2. Pastikan ini adalah request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Ambil data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 4. Buat objek Client
        $client = new Client();

        // 5. (LOGIKA LOGIN) Ambil SEMUA user dari API
        // Asumsi tabel di server bernama 'users'
        $users = $client->get('users');
        if (isset($users['status']) && $users['status'] == 'error') {
            // Jika API mengembalikan error (misal: koneksi gagal)
            Helper::setFlashMessage('error', 'Server API tidak merespon: ' . $users['message']);
            Helper::redirect('pages/login.php');
        }
        $loggedInUser = null;

        // 6. Loop semua user untuk mencari yang cocok
        if (!empty($users) && is_array($users)) {
            foreach ($users as $user) {
                // Asumsi kolomnya 'email' dan 'password'
                // Ini hanya bekerja jika password di server TIDAK di-hash
                if ($user['email'] == $email && $user['password'] == $password) {
                    $loggedInUser = $user;
                    break; // User ditemukan, hentikan loop
                }
            }
        }

        // 7. Proses Hasil
        if ($loggedInUser) {
            // BERHASIL LOGIN
            // Asumsi data user dari API berisi 'id', 'nama', 'email', 'role'
            Auth::setLoginSession($loggedInUser);
            // Redirect ke index, nanti index akan melempar ke dashboard
            Helper::redirect('client_sister_uas/index.php');
        } else {
            // GAGAL LOGIN
            Helper::setFlashMessage('error', 'Email atau password yang Anda masukkan salah.');
            Helper::redirect('pages/login.php');
        }

    } catch (Exception $e) {
        // Tangani jika koneksi ke API gagal
        Helper::setFlashMessage('error', 'Koneksi ke server gagal: ' . $e->getMessage());
        Helper::redirect('pages/login.php');
    }

} else {
    // Jika diakses langsung (bukan POST), tendang kembali
    Helper::redirect('pages/login.php');
}
?>