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
        // Sekarang file ini otomatis tahu BASE_URL dari Langkah 2
        $url = ltrim($url, './');

        // Ganti menjadi BASE_URL agar lebih lengkap
        header("Location: " . BASE_URL . "{$url}");
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