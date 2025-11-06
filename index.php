<?php
// client/index.php

// ==========================================================
// ATURAN EMAS: Muat config.php PERTAMA!
// ==========================================================
require_once 'config/config.php';
// ==========================================================

// Sekarang kita bisa memuat file core lain yang bergantung padanya
require_once 'core/Auth.php';
require_once 'core/Helper.php';

// Selalu mulai sesi
Auth::startSession();

// Logika routing
if (Auth::isLoggedIn()) {
    // Jika sudah login, cek rolenya
    $role = Auth::getRole();
    
    if ($role == 'admin') {
        // Panggil redirect TANPA slash di depan
        Helper::redirect('pages/dashboard_admin.php');
    } elseif ($role == 'karyawan') {
        Helper::redirect('pages/dashboard_karyawan.php');
    } else {
        // Fallback jika role tidak dikenal
        Helper::redirect('pages/dashboard_admin.php');
    }
} else {
    // 4. Jika belum login, lempar ke halaman login
    // Panggil redirect TANPA slash di depan
    Helper::redirect('pages/login.php');
}

?>