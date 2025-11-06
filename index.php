<?php
// client/index.php

// 1. Panggil file inti
require_once 'core/Auth.php';
require_once 'core/Helper.php';

// define('BASE_PROJECT_PATH', '/client_sister_uas/');
// 2. Selalu mulai sesi
Auth::startSession();

// 3. Logika routing
if (Auth::isLoggedIn()) {
    // Jika sudah login, cek rolenya
    $role = Auth::getRole();
    
    if ($role == 'admin') {
        Helper::redirect('pages/dashboard_admin.php');
    } elseif ($role == 'karyawan') {
        Helper::redirect('pages/dashboard_karyawan.php');
    } else {
        // Role tidak dikenal, fallback ke dashboard admin
        Helper::redirect('pages/dashboard_admin.php');
    }
} else {
    // 4. Jika belum login, lempar ke halaman login
    Helper::redirect('pages/login.php');
}

?>