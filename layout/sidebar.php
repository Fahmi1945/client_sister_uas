<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? null;
?>
<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg hidden md:block">
    <div class="h-full flex flex-col">
        <div class="flex items-center justify-center py-6 border-b">
            <img src="/client/assets/img/logo.png" alt="Logo" class="h-10 w-10 mr-2">
            <h2 class="text-xl font-bold text-blue-600">Manajemen Tugas</h2>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="/client/index.php?page=dashboard" class="block py-2 px-4 rounded hover:bg-blue-100 font-medium">
                🏠 Dashboard
            </a>

            <?php if ($role === 'admin'): ?>
            <a href="/client/pages/users/list.php" class="block py-2 px-4 rounded hover:bg-blue-100 font-medium">
                👥 Manajemen User
            </a>
            <a href="/client/pages/departemen/list.php" class="block py-2 px-4 rounded hover:bg-blue-100 font-medium">
                🏢 Departemen
            </a>
            <a href="/client/pages/tugas/list.php" class="block py-2 px-4 rounded hover:bg-blue-100 font-medium">
                📋 Tugas
            </a>
            <?php endif; ?>

            <a href="/client/pages/status_tugas/board.php"
                class="block py-2 px-4 rounded hover:bg-blue-100 font-medium">
                ✅ Status Tugas
            </a>
        </nav>

        <div class="p-4 border-t">
            <a href="/client/logout.php" class="block text-red-500 hover:text-red-700 font-semibold">
                🚪 Logout
            </a>
        </div>
    </div>
</aside>