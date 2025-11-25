<?php
include 'header.php';
?>
<main class="flex-1 p-8 overflow-y-auto">
    <div class="max-w-3xl mx-auto bg-black/30 backdrop-blur-sm p-6 rounded-xl border border-white/10">
        <h2 class="text-2xl text-white font-semibold mb-4">Detail Transaksi - #<?= htmlspecialchars($data['id_rental']) ?></h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-text-secondary-dark">
            <div>
                <p class="text-white font-medium">Pelanggan</p>
                <p><?= htmlspecialchars($data['nama_pelanggan'] ?? '-') ?></p>

                <p class="text-white font-medium mt-3">No. KTP</p>
                <p><?= htmlspecialchars($data['no_ktp'] ?? '-') ?></p>

                <p class="text-white font-medium mt-3">No. HP</p>
                <p><?= htmlspecialchars($data['no_hp'] ?? '-') ?></p>

                <p class="text-white font-medium mt-3">Karyawan</p>
                <p><?= htmlspecialchars($data['nama_karyawan'] ?? ($_SESSION['nama_lengkap'] ?? '-')) ?></p>
            </div>

            <div>
                <p class="text-white font-medium">Kendaraan</p>
                <p><?= htmlspecialchars($data['merk_kendaraan'] ?? '-') ?> <span class="text-text-secondary-dark">(<?= htmlspecialchars($data['no_plat'] ?? '-') ?>)</span></p>

                <p class="text-white font-medium mt-3">Periode</p>
                <p><?= htmlspecialchars(date('d M Y H:i', strtotime($data['tanggal_sewa'] ?? ''))) ?> - <?= htmlspecialchars($data['tanggal_kembali'] ? date('d M Y H:i', strtotime($data['tanggal_kembali'])) : '-') ?></p>

                <p class="text-white font-medium mt-3">Harga per jam</p>
                <p class="text-primary font-semibold">Rp <?= number_format($data['harga_per_jam'] ?? 0, 0, ',', '.') ?></p>

                <p class="text-white font-medium mt-3">Total Biaya</p>
                <p class="text-primary font-semibold">Rp <?= number_format($data['total_biaya'] ?? 0, 0, ',', '.') ?></p>
            </div>

            <div class="md:col-span-2">
                <hr class="my-4 border-white/10" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-white font-medium">Jumlah Bayar</p>
                        <p><?= $data['jumlah_bayar'] !== null ? 'Rp ' . number_format($data['jumlah_bayar'], 0, ',', '.') : '-' ?></p>
                    </div>
                    <div>
                        <p class="text-white font-medium">Tanggal Bayar</p>
                        <p><?= $data['tanggal_bayar'] ? date('d M Y H:i', strtotime($data['tanggal_bayar'])) : '-' ?></p>
                    </div>
                    <div>
                        <p class="text-white font-medium">Metode Bayar</p>
                        <p><?= $data['metode_bayar'] ? htmlspecialchars(ucfirst($data['metode_bayar'])) : '-' ?></p>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-white font-medium">Status Pembayaran</p>
                    <p>
                        <?php
                        $isPaid = false;
                        if (isset($data['jumlah_bayar']) && $data['jumlah_bayar'] !== null) {
                            $isPaid = floatval($data['jumlah_bayar']) >= floatval($data['total_biaya']);
                        }
                        ?>
                        <?= $isPaid ? '<span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-emerald-600 text-white">Lunas</span>' : '<span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-red-600 text-white">Belum Lunas</span>' ?>
                    </p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="index.php?page=transaksi" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">Kembali</a>
                    <a href="index.php?page=transaksi&action=edit&id=<?= urlencode($data['id_rental']) ?>" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500/80 rounded-lg hover:bg-amber-500/90 transition">Edit</a>
                    <?php if (!$isPaid): ?>
                        <a href="index.php?page=transaksi&action=payment&id=<?= urlencode($data['id_rental']) ?>" class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-600/90 transition">Bayar Sekarang</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>