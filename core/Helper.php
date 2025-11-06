<?php
// client/core/Helper.php

// Pastikan sesi selalu dimulai jika kita menggunakan helper
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Helper
{

    /**
     * Mengalihkan pengguna ke URL lain dan menghentikan eksekusi.
     * @param string $url URL tujuan (relatif dari root folder client)
     */
    public static function redirect($url)
    {
        require_once __DIR__ . '/../config/config.php';
        // Hapus '../' jika ada untuk konsistensi
        $url = ltrim($url, './');

        // Asumsi aplikasi Anda jalan di root domain/folder. 
        // Jika di dalam subfolder, Anda mungkin perlu atur base URL client.
        header("Location: " . BASE_PROJECT_PATH . "/{$url}");
        exit;
    }

    /**
     * Menetapkan pesan singkat (flash message) yang akan tampil satu kali.
     * @param string $key Tipe pesan (misal: 'success', 'error', 'info')
     * @param string $message Isi pesan
     */
    public static function setFlashMessage($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Mengambil dan menampilkan pesan singkat (jika ada), lalu menghapusnya.
     * @param string $key Tipe pesan (misal: 'success', 'error')
     */
    public static function displayFlashMessage($key)
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); // Hapus setelah diambil

            // Format output HTML (misal pakai Bootstrap alert)
            $alertClass = ($key == 'error') ? 'danger' : $key;
            echo "<div class='alert alert-{$alertClass}'>{$message}</div>";
        }
    }
}
?>