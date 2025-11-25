<?php
include 'header.php';
?>
<main class="flex-1 p-8 overflow-y-auto">
    <div class="max-w-2xl mx-auto bg-black/30 backdrop-blur-sm p-6 rounded-xl border border-white/10">
        <h2 class="text-2xl text-white font-semibold mb-4">Struk Pembayaran - #<?= htmlspecialchars($data['id_rental']) ?></h2>
        <div class="grid grid-cols-2 gap-4 text-sm text-text-secondary-dark">
            <div>
                <p class="text-white font-medium">Pelanggan</p>
                <p><?= htmlspecialchars($data['nama_pelanggan'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Karyawan</p>
                <p><?= htmlspecialchars($data['nama_karyawan'] ?? ($_SESSION['nama_lengkap'] ?? '-')) ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Kendaraan</p>
                <p><?= htmlspecialchars($data['merk_kendaraan'] ?? '-') ?> (<?= htmlspecialchars($data['no_plat'] ?? '-') ?>)</p>
            </div>
            <div>
                <p class="text-white font-medium">Periode</p>
                <p><?= htmlspecialchars(date('d M Y H:i', strtotime($data['tanggal_sewa']))) ?> - <?= htmlspecialchars($data['tanggal_kembali'] ? date('d M Y H:i', strtotime($data['tanggal_kembali'])) : '-') ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Total Biaya</p>
                <p class="text-primary font-semibold">Rp <?= number_format($data['total_biaya'] ?? 0, 0, ',', '.') ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Jumlah Bayar</p>
                <p><?= $data['jumlah_bayar'] !== null ? 'Rp ' . number_format($data['jumlah_bayar'], 0, ',', '.') : '-' ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Tanggal Bayar</p>
                <p><?= $data['tanggal_bayar'] ? date('d M Y', strtotime($data['tanggal_bayar'])) : '-' ?></p>
            </div>
            <div>
                <p class="text-white font-medium">Metode Bayar</p>
                <p><?= $data['metode_bayar'] ? htmlspecialchars(ucfirst($data['metode_bayar'])) : '-' ?></p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="index.php?page=transaksi" class="btn-secondary">Kembali ke Daftar</a>
            <button onclick="window.print()" class="btn-primary">Cetak Struk</button>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>
