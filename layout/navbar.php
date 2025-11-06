<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nama = $_SESSION['nama'] ?? 'User';
$role = $_SESSION['role'] ?? 'karyawan';
?>
<!-- Navbar -->
<header class="flex justify-between items-center bg-white shadow px-6 py-3 w-full">
    <!-- Tombol toggle sidebar (untuk mobile) -->
    <button id="sidebarToggle" class="md:hidden text-gray-700 focus:outline-none text-2xl">
        ☰
    </button>

    <!-- Judul halaman -->
    <h1 class="text-lg font-semibold text-gray-700">
        <?= ucfirst(str_replace('_', ' ', basename($_SERVER['PHP_SELF'], '.php'))) ?>
    </h1>

    <!-- Profil user -->
    <div class="flex items-center space-x-3">
        <div class="text-right">
            <p class="font-medium text-gray-800"><?= htmlspecialchars($nama) ?></p>
            <p class="text-sm text-gray-500"><?= ucfirst($role) ?></p>
        </div>
        <img src="/client/assets/img/logo.png" alt="Profile" class="h-8 w-8 rounded-full border border-gray-300">
    </div>
</header>

<!-- Script toggle sidebar -->
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    const sidebar = document.querySelector('aside');
    sidebar.classList.toggle('hidden');
});
</script>