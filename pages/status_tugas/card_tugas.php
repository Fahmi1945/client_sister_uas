<?php
// client/pages/status_tugas/card_tugas.php
// Variabel $tugas, $userMap, dan $currentUserId sudah tersedia dari board.php

$tugasId = $tugas['id_tugas'];
$currentStatus = strtolower($tugas['status_current'] ?? 'belum'); // Status TERAKHIR yang dicatat
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

    <div class="flex justify-between items-center text-xs text-gray-500 mb-3 gap-2">
        <span class="truncate flex-1"
            title="Pembuat: <?php echo htmlspecialchars($userMap[$tugas['id_pembuat']] ?? 'Admin'); ?>">
            <i class="bi bi-person-fill"></i>
            <?php echo htmlspecialchars($userMap[$tugas['id_pembuat']] ?? 'Admin'); ?>
        </span>
        <span class="text-red-600 whitespace-nowrap">
            <i class="bi bi-calendar-event"></i>
            Deadline: <?php echo date('d M Y', strtotime($tugas['deadline'])); ?>
        </span>
    </div>

    <?php if (!empty($catatan)): ?>
        <div class="bg-gray-50 p-2 rounded text-xs text-gray-600 mb-2 border-l-2 border-gray-300">
            <i class="bi bi-chat-left-text"></i>
            <span class="font-semibold">Catatan Terakhir:</span> <?php echo htmlspecialchars($catatan); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($updatedAt)): ?>
        <div class="text-xs text-gray-400 mb-2">
            <i class="bi bi-clock"></i> Update: <?php echo date('d M Y H:i', strtotime($updatedAt)); ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?php echo BASE_URL; ?>proses/status.php" class="mt-3 pt-2 border-t border-gray-100">
        <input type="hidden" name="aksi" value="ubah">
        <input type="hidden" name="id" value="<?php echo $tugasId; ?>">

        <div class="mb-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                <i class="bi bi-chat-square-dots"></i> Tambah Catatan (Opsional)
            </label>
            <textarea name="catatan" rows="2" placeholder="Catatan ini akan dicatat sebagai log status terbaru..."
                class="w-full text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-none"></textarea>
        </div>

        <div class="flex space-x-2">

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
        </div>
    </form>
</div>