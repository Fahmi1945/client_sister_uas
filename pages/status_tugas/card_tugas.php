<?php
// client/pages/status_tugas/card_tugas.php
// File ini adalah template dan dipanggil dari board.php
// Variabel $tugas dan $userMap sudah ada dari board.php
?>

<div class="bg-white p-4 rounded-md shadow border">
    <h3 class="font-semibold text-gray-800 mb-2">
        <?php echo htmlspecialchars($tugas['judul']); ?>
    </h3>

    <p class="text-sm text-gray-600 mb-3">
        Dibuat oleh:
        <strong>
            <?php echo $userMap[$tugas['id_user_pembuat']] ?? 'Admin'; ?>
        </strong>
    </p>

    <div class="text-sm text-red-600 mb-4">
        <i class="bi bi-calendar-event mr-1"></i>
        Deadline: <?php echo date('d M Y', strtotime($tugas['deadline'])); ?>
    </div>

    <div class="flex space-x-2">
        <?php if ($tugas['status'] == 'Belum'): ?>
        <a href="../proses/status.php?id=<?php echo $tugas['id_tugas']; ?>&status=Proses"
            class="flex-1 text-center bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-2 rounded">
            Mulai Kerjakan <i class="bi bi-play-fill"></i>
        </a>
        <?php elseif ($tugas['status'] == 'Proses'): ?>
        <a href="../proses/status.php?id=<?php echo $tugas['id_tugas']; ?>&status=Belum"
            class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 px-2 rounded">
            <i class="bi bi-pause-fill"></i> Tunda
        </a>
        <a href="../proses/status.php?id=<?php echo $tugas['id_tugas']; ?>&status=Selesai"
            class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 px-2 rounded">
            Selesai <i class="bi bi-check-lg"></i>
        </a>
        <?php elseif ($tugas['status'] == 'Selesai'): ?>
        <a href="../proses/status.php?id=<?php echo $tugas['id_tugas']; ?>&status=Proses"
            class="flex-1 text-center bg-gray-500 hover:bg-gray-600 text-white text-xs font-bold py-2 px-2 rounded">
            <i class="bi bi-arrow-counterclockwise"></i> Buka Lagi
        </a>
        <?php endif; ?>
    </div>
</div>