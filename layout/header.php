<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . " - Sistem Manajemen Tugas" : "Sistem Manajemen Tugas" ?></title>

    <!-- Favicon -->
    <link rel="icon" href="/client/assets/img/logo.png">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Style -->
    <link rel="stylesheet" href="/client/assets/css/style.css">

</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Wrapper layout -->
    <div class="flex min-h-screen w-full overflow-hidden">