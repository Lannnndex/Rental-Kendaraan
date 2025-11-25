<?php
include 'header.php';
?>

    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Edit Transaksi Sewa</h2>
            <p class="text-text-secondary-dark mt-1">Perbarui detail transaksi di bawah ini.</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-2xl mx-auto">

            <form action="index.php?page=transaksi&action=edit&id=<?= urlencode($data['id_rental']) ?>" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div>
                    <label for="no_ktp" class="block mb-2 text-sm font-medium text-text-primary-dark">Pelanggan</label>
                    <input id="filter_pelanggan" type="text" placeholder="Cari pelanggan..." class="form-input mb-2" />
                    <select
                        id="no_ktp"
                        name="no_ktp"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none
                               <?= isset($errors['id_pelanggan']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                    >
                        <option value="" class="bg-surface-dark text-text-secondary-dark">-- Pilih Pelanggan --</option>
                        <?php if (is_object($pelanggan) && method_exists($pelanggan, 'data_seek')) { $pelanggan->data_seek(0); } elseif (function_exists('mysqli_data_seek')) { mysqli_data_seek($pelanggan, 0); } // Reset pointer loop ?>
                        <?php while($p = $pelanggan->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($p['no_ktp']) ?>" class="bg-surface-dark"
                                <?= (isset($data['no_ktp']) && $data['no_ktp'] == $p['no_ktp']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (isset($errors['no_ktp'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['no_ktp'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="no_plat" class="block mb-2 text-sm font-medium text-text-primary-dark">Kendaraan</label>
                    <input id="filter_kendaraan" type="text" placeholder="Cari kendaraan... merk, plat..." class="form-input mb-2" />
                    <select
                        id="no_plat"
                        name="no_plat"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none
                               <?= isset($errors['id_kendaraan']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                    >
                        <option value="" class="bg-surface-dark text-text-secondary-dark">-- Pilih Kendaraan --</option>
                        <?php if (is_object($kendaraan) && method_exists($kendaraan, 'data_seek')) { $kendaraan->data_seek(0); } elseif (function_exists('mysqli_data_seek')) { mysqli_data_seek($kendaraan, 0); } // Reset pointer loop ?>
                        <?php while($k = $kendaraan->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['no_plat']) ?>" data-harga="<?= htmlspecialchars($k['harga_per_jam']) ?>" class="bg-surface-dark"
                                <?= (isset($data['no_plat']) && $data['no_plat'] == $k['no_plat']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['merk']) ?> (<?= htmlspecialchars($k['no_plat']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (isset($errors['no_plat'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['no_plat'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="tanggal_sewa" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Sewa</label>
                    <input
                        type="datetime-local"
                        id="tanggal_sewa"
                        name="tanggal_sewa"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark
                               <?= isset($errors['tgl_sewa']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['tanggal_sewa'] ?? $data['tgl_sewa'] ?? '') ?>"
                    >
                    <?php if (isset($errors['tanggal_sewa'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['tanggal_sewa'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="tanggal_kembali" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Kembali</label>
                    <input
                        type="datetime-local"
                        id="tanggal_kembali"
                        name="tanggal_kembali"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark
                               <?= isset($errors['tgl_kembali']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['tanggal_kembali'] ?? $data['tgl_kembali'] ?? '') ?>"
                    >
                    <?php if (isset($errors['tanggal_kembali'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['tanggal_kembali'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="total_biaya" class="block mb-2 text-sm font-medium text-text-primary-dark">Total Biaya (Rp)</label>
                    <input
                        type="number"
                        id="total_biaya"
                        name="total_biaya" readonly
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark
                               <?= isset($errors['total_biaya']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['total_biaya'] ?? '') ?>"
                        placeholder="Masukkan hanya angka, cth: 500000"
                    >
                    <?php if (isset($errors['total_biaya'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['total_biaya'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-text-primary-dark">Karyawan (Penanggung Jawab)</label>
                    <input type="text" readonly class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($data['nama_karyawan'] ?? ($_SESSION['nama_lengkap'] ?? '-')) ?>">
                </div>

                <!-- Pembayaran (dapat diubah saat edit) -->
                <div id="payment_section_edit" class="mt-4">
                    <h3 class="text-white text-lg font-semibold mt-2">Pembayaran</h3>
                    <div>
                        <label for="jumlah_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Jumlah Bayar (Rp)</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($data['jumlah_bayar'] ?? '') ?>" placeholder="Masukkan jumlah bayar jika sudah/akan dibayar">
                        <?php if (isset($errors['jumlah_bayar'])): ?><p class="text-red-400 text-xs italic mt-2"><?= $errors['jumlah_bayar'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="tgl_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Bayar</label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($data['tanggal_bayar'] ?? '') ?>">
                        <?php if (isset($errors['tgl_bayar'])): ?><p class="text-red-400 text-xs italic mt-2"><?= $errors['tgl_bayar'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="metode_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Metode Bayar</label>
                        <select id="metode_bayar" name="metode_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white">
                            <option value="">-- Pilih Metode --</option>
                            <option value="tunai" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='tunai') ? 'selected' : '' ?>>Tunai</option>
                            <option value="kartu" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='kartu') ? 'selected' : '' ?>>Kartu</option>
                            <option value="transfer" <?= (isset($data['metode_bayar']) && $data['metode_bayar']=='transfer') ? 'selected' : '' ?>>Transfer</option>
                        </select>
                        <?php if (isset($errors['metode_bayar'])): ?><p class="text-red-400 text-xs italic mt-2"><?= $errors['metode_bayar'] ?></p><?php endif; ?>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="index.php?page=transaksi" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                        <span class="material-symbols-outlined text-base">save</span>
                        Update
                    </button>
                </div>
            </form>
        </div>

<?php
include 'footer.php';
?>

<script>
// Realtime total calculation for edit form
(() => {
    function makeFilter(inputId, selectId) {
        const input = document.getElementById(inputId);
        const select = document.getElementById(selectId);
        if (!input || !select) return;
        input.addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            Array.from(select.options).forEach(opt => {
                const txt = (opt.text || '').toLowerCase();
                opt.hidden = q !== '' && !txt.includes(q);
            });
        });
    }
    makeFilter('filter_pelanggan', 'no_ktp');
    makeFilter('filter_kendaraan', 'no_plat');
    const noPlatEl = document.getElementById('no_plat');
    const startEl = document.getElementById('tanggal_sewa') || document.getElementById('tgl_sewa');
    const endEl = document.getElementById('tanggal_kembali') || document.getElementById('tgl_kembali');
    const totalEl = document.getElementById('total_biaya');

    function parseDateLocal(v){ if(!v) return null; return new Date(v); }

    function recalc(){
        const selected = noPlatEl?.selectedOptions[0];
        if(!selected){ return; }
        const harga = parseFloat(selected.getAttribute('data-harga') || '0');
        const s = parseDateLocal(startEl?.value);
        const e = parseDateLocal(endEl?.value);
        if(!s || !e || isNaN(harga)) { totalEl.value = ''; return; }
        const seconds = (e.getTime() - s.getTime()) / 1000;
        if(seconds <= 0){ totalEl.value = ''; return; }
        const hours = Math.ceil(seconds / 3600);
        const total = Math.round(hours * harga);
        totalEl.value = total;
    }

    if(noPlatEl) noPlatEl.addEventListener('change', recalc);
    if(startEl) startEl.addEventListener('change', recalc);
    if(endEl) endEl.addEventListener('change', recalc);
    setTimeout(recalc, 200);
})();
</script>