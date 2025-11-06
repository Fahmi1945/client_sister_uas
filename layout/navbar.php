<?php
// client/layout/navbar.php

// Variabel $currentUser (dari Auth::getUserData()) 
// dan $pageTitle sudah tersedia
// karena file ini dipanggil *setelah* variabel itu 
// di-set di layout/header.php
?>

<header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 md:px-8 flex-shrink-0">

    <div>
        <h1 class="text-xl font-semibold text-gray-800 font-poppins">
            <?php echo $pageTitle ?? "Dashboard"; // Fallback jika $pageTitle lupa di-set ?>
        </h1>
    </div>

    <div class="flex items-center space-x-4">

        <span class="text-gray-600 hidden sm:block">
            Halo,
            <strong class="font-medium">
                <?php echo htmlspecialchars($currentUser['nama'] ?? 'Pengguna'); ?>
            </strong>
        </span>

        <a href="/logout.php"
            class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>

    </div>
</header>