<?php
include 'header.php';
?>
<main class="flex-1 p-8 overflow-y-auto">
    <div class="max-w-2xl mx-auto bg-black/30 backdrop-blur-sm p-6 rounded-xl border border-white/10">
        <h2 class="text-2xl text-white font-semibold mb-4">Pembayaran - #<?= htmlspecialchars($data['id_rental']) ?></h2>
        <p class="text-text-secondary-dark mb-4">Lengkapi pembayaran untuk transaksi ini.</p>

        <form method="POST" action="index.php?page=transaksi&action=payment&id=<?= urlencode($data['id_rental']) ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-text-secondary-dark">Pelanggan</p>
                    <p class="text-white font-medium"><?= htmlspecialchars($data['nama_pelanggan'] ?? '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-text-secondary-dark">Kendaraan</p>
                    <p class="text-white font-medium"><?= htmlspecialchars($data['merk_kendaraan'] ?? '-') ?> <span class="text-text-secondary-dark">(<?= htmlspecialchars($data['no_plat'] ?? '-') ?>)</span></p>
                </div>
                <div>
                    <p class="text-sm text-text-secondary-dark">Periode</p>
                    <p class="text-white font-medium"><?= htmlspecialchars(date('d M Y H:i', strtotime($data['tanggal_sewa'] ?? ''))) ?> - <?= htmlspecialchars($data['tanggal_kembali'] ? date('d M Y H:i', strtotime($data['tanggal_kembali'])) : '-') ?></p>
                </div>
                <div>
                    <p class="text-sm text-text-secondary-dark">Total Biaya</p>
                    <p class="text-primary font-semibold">Rp <?= number_format($data['total_biaya'] ?? 0, 0, ',', '.') ?></p>
                </div>
            </div>

            <div>
                <label for="jumlah_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Jumlah Bayar (Rp)</label>
                <input id="jumlah_bayar" name="jumlah_bayar" type="number" class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white" value="<?= htmlspecialchars($data['jumlah_bayar'] ?? '') ?>">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="tgl_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Bayar</label>
                    <input id="tgl_bayar" name="tgl_bayar" type="date" class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white" value="<?= htmlspecialchars($data['tanggal_bayar'] ? date('Y-m-d', strtotime($data['tanggal_bayar'])) : '') ?>">
                </div>
                <div>
                    <label for="metode_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Metode Bayar</label>
                    <select id="metode_bayar" name="metode_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white">
                        <option value="">-- Pilih Metode --</option>
                        <option value="tunai" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='tunai') ? 'selected' : '' ?>>Tunai</option>
                        <option value="kartu" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='kartu') ? 'selected' : '' ?>>Kartu</option>
                        <option value="transfer" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='transfer') ? 'selected' : '' ?>>Transfer</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="index.php?page=transaksi" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">Batal</a>
                <button type="submit" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</main>
<?php include 'footer.php'; ?>
