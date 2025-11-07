<?php
// client/proses/debug_status.php
// File ini untuk debugging - hapus setelah masalah teratasi

require_once '../config/config.php';
require_once '../core/Client.php';
require_once '../core/Auth.php';

Auth::startSession();
Auth::checkLogin('karyawan');

$client = new Client();
$currentUserId = Auth::getUserData()['id_user'] ?? 0;

echo "<h1>Debug Status Tugas</h1>";
echo "<p><strong>User ID Login:</strong> {$currentUserId}</p>";
echo "<hr>";

// Ambil semua status_tugas
$allStatusTugas = $client->get('status_tugas');

echo "<h2>Data status_tugas dari API:</h2>";
echo "<pre>";
print_r($allStatusTugas);
echo "</pre>";

echo "<hr>";

echo "<h2>Filter status_tugas untuk user {$currentUserId}:</h2>";
if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
    $userStatusTugas = [];
    foreach ($allStatusTugas as $record) {
        if ($record['id_user'] == $currentUserId) {
            $userStatusTugas[] = $record;
        }
    }
    echo "<pre>";
    print_r($userStatusTugas);
    echo "</pre>";
    
    echo "<p><strong>Total record milik user ini:</strong> " . count($userStatusTugas) . "</p>";
} else {
    echo "<p>Error atau data kosong</p>";
}

echo "<hr>";

// Test pencarian specific record
$test_id_tugas = $_GET['test_id'] ?? null;
if ($test_id_tugas) {
    echo "<h2>Test Pencarian untuk id_tugas = {$test_id_tugas}:</h2>";
    
    $found = null;
    if (is_array($allStatusTugas) && !isset($allStatusTugas['status'])) {
        foreach ($allStatusTugas as $record) {
            if ($record['id_tugas'] == $test_id_tugas && $record['id_user'] == $currentUserId) {
                $found = $record;
                break;
            }
        }
    }
    
    if ($found) {
        echo "<p style='color: green;'><strong>✓ RECORD DITEMUKAN!</strong></p>";
        echo "<pre>";
        print_r($found);
        echo "</pre>";
        echo "<p><strong>id_status yang akan diupdate:</strong> {$found['id_status']}</p>";
    } else {
        echo "<p style='color: red;'><strong>✗ RECORD TIDAK DITEMUKAN</strong></p>";
        echo "<p>Record baru akan dibuat dengan POST</p>";
    }
}

echo "<hr>";
echo "<p><a href='?test_id=1'>Test dengan id_tugas=1</a> | ";
echo "<a href='?test_id=2'>Test dengan id_tugas=2</a></p>";
?>