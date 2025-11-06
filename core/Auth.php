<?php
// client/core/Auth.php

class Auth {

    /**
     * Selalu panggil ini di awal file (atau di header)
     * untuk memastikan sesi aktif.
     */
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menyimpan data pengguna ke sesi setelah login berhasil.
     * @param array $userData Data dari server (misal: id, nama, role)
     */
    public static function setLoginSession($userData) {
        self::startSession();
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user'] = $userData; // Simpan semua data user
    }

    /**
     * Memeriksa apakah pengguna sudah login.
     * @return bool
     */
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    /**
     * Menghapus sesi dan me-logout pengguna.
     */
    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }

    /**
     * Mengambil data pengguna yang sedang login.
     * @return array|null
     */
    public static function getUserData() {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Mengambil role pengguna (misal: 'admin', 'karyawan').
     * @return string
     */
    public static function getRole() {
        return $_SESSION['user']['role'] ?? 'guest'; // 'guest' jika tidak login
    }

    /**
     * Fungsi keamanan: Panggil di halaman yang butuh login.
     * Akan auto-redirect ke login.php jika belum login.
     * @param string $role Opsional. Tentukan role (misal: 'admin')
     */
    public static function checkLogin($role = null) {
        if (!self::isLoggedIn()) {
            require_once 'Helper.php'; // Panggil helper
            Helper::setFlashMessage('error', 'Anda harus login untuk mengakses halaman ini.');
            Helper::redirect('pages/login.php');
        }
        
        // Cek role jika diperlukan
        if ($role && self::getRole() !== $role) {
            require_once 'Helper.php';
            Helper::setFlashMessage('error', 'Anda tidak memiliki hak akses ke halaman ini.');
            Helper::redirect('pages/error403.php'); // Halaman "Akses Ditolak"
        }
    }
}
?>