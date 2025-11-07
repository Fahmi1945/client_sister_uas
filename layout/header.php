<?php
// client/layout/header.php

// ==========================================================
// ATURAN EMAS: Muat config.php PERTAMA!
// ==========================================================
require_once __DIR__ . '/../config/config.php';
// ==========================================================

// Muat file inti
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Helper.php';

// Pastikan user sudah login sebelum memuat layout ini
Auth::checkLogin(); 

$currentUser = Auth::getUserData();
$userRole = Auth::getRole();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $pageTitle ?? "Sistem Manajemen Tugas"; ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'primary': {
                        'DEFAULT': '#2563EB',
                        'hover': '#1D4ED8'
                    },
                },
                fontFamily: {
                    'poppins': ['Poppins', 'sans-serif'],
                    'inter': ['Inter', 'sans-serif']
                }
            }
        }
    }
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

</head>

<body class="bg-gray-100 font-inter text-gray-900">

    <div class="flex min-h-screen">

        <?php 
        // Panggil Sidebar
        require_once __DIR__ . '/sidebar.php'; 
        ?>

        <div class="flex-1 flex flex-col">

            <?php 
            // Panggil Navbar
            require_once __DIR__ . '/navbar.php'; 
            ?>

            <main class="flex-1 p-6 md:p-8 overflow-y-auto">

                <?php Helper::displayFlashMessage('success'); ?>
                <?php Helper::displayFlashMessage('error'); ?>

                <!-- Konten halaman akan dimasukkan di sini -->