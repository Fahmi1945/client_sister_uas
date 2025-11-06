<?php
// client/logout.php

require_once 'core/Auth.php';
require_once 'core/Helper.php';

// Hapus semua data sesi
Auth::logout();

// Atur pesan sukses (opsional)
Helper::setFlashMessage('success', 'Anda telah berhasil logout.');

// Arahkan kembali ke halaman login
Helper::redirect('pages/login.php');
?>