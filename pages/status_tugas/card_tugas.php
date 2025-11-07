<?php
// client/pages/status_tugas/card_tugas.php
// Variabel $tugas, $userMap, dan $currentUserId sudah tersedia dari board.php

$tugasId = $tugas['id_tugas'];
$currentStatus = strtolower($tugas['status_current'] ?? 'belum');
$catatan = $tugas['catatan'] ?? '';
$updatedAt = $tugas['updated_at'] ?? '';

// Tentukan warna border berdasarkan status
$borderColor = 'yellow'; // default: belum
if ($currentStatus == 'proses') {
    $borderColor = 'blue';
} elseif ($currentStatus == 'selesai') {
    $borderColor = 'green';
}
?>

<div
    class="bg-white p-4 rounded-lg shadow-md border-t-4 border-<?php echo $borderColor; ?>-500 hover:shadow-lg transition-shadow">

    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">
        <?php echo htmlspecialchars($tugas['judul']); ?>
    </h3>

    <?php if (!empty($tugas['deskripsi'])): ?>
    <p class="text-xs text-gray-600 mb-2 line-clamp-2">
        <?php echo htmlspecialchars($tugas['deskripsi']); ?>
    </p>
    <?php endif; ?>

    <div class="text-xs text-gray-500 space-y-1 mb-4">
        <?php if (isset($userMap[$tugas['id_user_pembuat']])): ?>
        <p><i class="bi bi-person-fill mr-1"></i> Dibuat oleh:
            **<?php echo htmlspecialchars($userMap[$tugas['id_user_pembuat']]); ?>**</p>
        <?php endif; ?>
        <?php if (!empty($updatedAt)): ?>
        <p><i class="bi bi-clock-fill mr-1"></i> Diperbarui: <?php echo date('d M Y, H:i', strtotime($updatedAt)); ?>
        </p>
        <?php endif; ?>
        <?php if (!empty($catatan)): ?>
        <p><i class="bi bi-journal-text mr-1"></i> Catatan: <?php echo htmlspecialchars($catatan); ?></p>
        <?php endif; ?>
    </div>

    <form action="../../proses/status.php" method="GET" class="flex space-x-2 mt-4">
        <input type="hidden" name="aksi" value="ubah">
        <input type="hidden" name="id" value="<?php echo $tugasId; ?>">

        <?php if ($currentStatus == 'belum'): ?>
        <button type="submit" name="status" value="proses"
            class="flex-1 text-center bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-play-fill"></i> Mulai Proses
        </button>

        <?php elseif ($currentStatus == 'proses'): ?>
        <button type="submit" name="status" value="belum"
            class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-arrow-counterclockwise"></i> Tunda
        </button>
        <button type="submit" name="status" value="selesai"
            class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-check-lg"></i> Selesaikan
        </button>

        <?php elseif ($currentStatus == 'selesai'): ?>
        <button type="submit" name="status" value="proses"
            class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-arrow-counterclockwise"></i> Buka Lagi
        </button>
        <?php endif; ?>
    </form>
</div>