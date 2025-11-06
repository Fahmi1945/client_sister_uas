<?php
// client/pages/login.php

// Panggil file core untuk sesi dan redirect
require_once '../core/Auth.php';
require_once '../core/Helper.php';

// Mulai sesi untuk menampilkan flash message (jika ada)
Auth::startSession();

// Jika pengguna SUDAH login, lempar dia ke index (yang akan me-redirect ke dashboard)
if (Auth::isLoggedIn()) {
    Helper::redirect('client_sister_uas/index.php');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Tugas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
    // Konfigurasi Tailwind yang sama dengan layout
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
</head>

<body class="bg-gradient-to-br from-blue-50 to-gray-100 font-inter">

    <div class="flex min-h-screen items-center justify-center p-4">

        <div class="bg-white w-full max-w-md p-8 rounded-lg shadow-lg">

            <img src="/assets/img/logo.png" alt="Logo" class="mx-auto h-12 w-auto mb-6">

            <h2 class="text-2xl font-bold text-center text-gray-900 font-poppins mb-6">
                Login ke Akun Anda
            </h2>

            <?php Helper::displayFlashMessage('error'); ?>

            <form action="..~/proses/auth.php" method="POST">
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" name="password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center items-center space-x-2 bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded-md transition-colors duration-200">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Login</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>