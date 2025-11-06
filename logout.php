<?php
// client/logout.php

// ==========================================================
// ATURAN EMAS: Muat config.php PERTAMA!
// ==========================================================
require_once 'config/config.php';
// ==========================================================

// Panggil file core lainnya
require_once 'core/Auth.php';
require_once 'core/Helper.php';

// 1. Panggil fungsi logout dari Auth.php
// Ini akan menghancurkan (destroy) sesi
Auth::logout();

// 2. Atur pesan singkat (opsional)
Helper::setFlashMessage('success', 'Anda telah berhasil logout.');

// 3. Arahkan pengguna kembali ke halaman login
// (Helper.php akan otomatis menggunakan BASE_URL)
Helper::redirect('pages/login.php');
?>