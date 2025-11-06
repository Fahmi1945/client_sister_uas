<?php
// client/config/config.php

// 1. Definisikan path proyek Anda (ini sudah benar)
define('BASE_PROJECT_PATH', '/client_sister_uas/');

// 2. Buat BASE_URL dinamis (LEBIH BAIK DARI SEBELUMNYA)
// Ini akan otomatis menggunakan 'localhost' atau '192.168.18.243'
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . BASE_PROJECT_PATH);

// 3. Definisikan URL API Anda
// (Ganti 'localhost' jika server Anda ada di IP lain)
define('API_BASE_URL', 'http://localhost/uas_sister/server/server.php');

?>