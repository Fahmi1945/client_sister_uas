<?php
// client_sister_uas/test_layout.php

// 1. Tentukan judul halaman (dibutuhkan oleh header.php dan navbar.php)
$pageTitle = "TESTING LAYOUT BERHASIL"; 

// 2. Panggil Header.php (Ini yang memuat semua layout!)
// Jalur ini relatif dari root folder Anda.
require_once 'layout/header.php'; 

// 3. Tampilkan konten yang sederhana
?>

<div class="bg-white p-10 rounded-lg shadow-xl border-l-8 border-primary">
    <h2 class="text-3xl font-poppins font-bold text-gray-800 mb-4">
        ✅ Layout Berhasil Dimuat!
    </h2>
    <p class="text-gray-600">
        Jika Anda melihat sidebar, navbar, dan pesan ini, semua file layout sudah terhubung dengan benar.
    </p>
    <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded">
        <p class="text-sm font-semibold">Status Login:</p>
        <p class="text-sm">User: <?php echo htmlspecialchars($currentUser['nama'] ?? 'TEST USER'); ?></p>
        <p class="text-sm">Role: <?php echo htmlspecialchars($userRole ?? 'admin'); ?></p>
    </div>
</div>

<?php 
// 4. Panggil Footer.php
require_once 'layout/footer.php'; 
?>