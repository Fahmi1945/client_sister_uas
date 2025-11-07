<?php
// client/pages/status_tugas/card_tugas.php
// Variabel $tugas dan $userMap sudah tersedia dari board.php
// ID pengguna yang sedang login (untuk log) sudah ada di $currentUserId
$tugasId = $tugas['id_tugas'];
?>

<div
    class="bg-white p-4 rounded-lg shadow-md border-t-4 border-<?php echo strtolower($tugas['status']) == 'selesai' ? 'green' : (strtolower($tugas['status']) == 'proses' ? 'primary' : 'yellow'); ?>-500">
    <h3 class="font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($tugas['judul']); ?></h3>

    <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
        <span class="truncate max-w-[50%]">Pembuat:
            <?php echo htmlspecialchars($userMap[$tugas['id_pembuat']] ?? 'Admin'); ?></span>
        <span class="text-red-600"><i class="bi bi-calendar-event"></i> Deadline:
            <?php echo date('d M', strtotime($tugas['deadline'])); ?></span>
    </div>

    <div class="flex space-x-2 mt-3 pt-2 border-t border-gray-100">
        <?php if (strtolower($tugas['status']) == 'belum'): ?>
        <a href="<?php echo BASE_URL; ?>proses/status.php?aksi=ubah&id=<?php echo $tugasId; ?>&status=proses"
            class="flex-1 text-center bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 rounded transition-colors">
            Mulai Proses <i class="bi bi-arrow-right-short"></i>
        </a>
        <?php elseif (strtolower($tugas['status']) == 'proses'): ?>
        <a href="<?php echo BASE_URL; ?>proses/status.php?aksi=ubah&id=<?php echo $tugasId; ?>&status=belum"
            class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-arrow-left-short"></i> Tunda
        </a>
        <a href="<?php echo BASE_URL; ?>proses/status.php?aksi=ubah&id=<?php echo $tugasId; ?>&status=selesai"
            class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 rounded transition-colors">
            Selesaikan <i class="bi bi-check-lg"></i>
        </a>
        <?php elseif (strtolower($tugas['status']) == 'selesai'): ?>
        <a href="<?php echo BASE_URL; ?>proses/status.php?aksi=ubah&id=<?php echo $tugasId; ?>&status=proses"
            class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white text-xs font-bold py-2 rounded transition-colors">
            <i class="bi bi-arrow-counterclockwise"></i> Buka Lagi
        </a>
        <?php endif; ?>
    </div>
</div>