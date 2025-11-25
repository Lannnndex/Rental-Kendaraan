<?php
include 'header.php';
?>


    <main class="flex-1 p-8 overflow-y-auto">

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-white">Recycle Bin - Pembayaran</h2>
            <a href="index.php?page=pembayaran" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                <span class="material-symbols-outlined text-base transform rotate-180">arrow_forward</span>
                Kembali ke Daftar
            </a>
        </div>

        <form method="POST" action="index.php?page=pembayaran&action=bulkRecycleBin">
            <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

            <div class="bg-black/30 backdrop-blur-sm p-6 rounded-xl shadow-2xl border border-white/10">

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs text-text-secondary-dark uppercase border-b border-white/10">
                            <tr>
                                <th class="px-6 py-4 font-semibold" scope="col">
                                    <input type="checkbox" id="checkAll" class="w-4 h-4 bg-white/10 border-white/30 rounded text-primary focus:ring-primary focus:ring-2">
                                </th>
                                <th class="px-6 py-4 font-semibold" scope="col">ID Bayar</th>
                                <th class="px-6 py-4 font-semibold" scope="col">Pelanggan</th>
                                <th class="px-6 py-4 font-semibold" scope="col">Jumlah Bayar</th>
                                <th class="px-6 py-4 font-semibold" scope="col">Tanggal Dihapus</th>
                                <th class="px-6 py-4 font-semibold" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-text-primary-dark">
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-5">
                                    <input type="checkbox" name="ids[]" value="<?= $row['id_pembayaran'] ?>" class="rowCheckbox w-4 h-4 bg-white/10 border-white/30 rounded text-primary focus:ring-primary focus:ring-2">
                                </td>
                                <td class="px-6 py-5 font-medium">#<?= htmlspecialchars($row['id_pembayaran']) ?></td>
                                <td class="px-6 py-5"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td class="px-6 py-5">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                                <td class="px-6 py-5"><?= DateHelper::indoFull($row['deleted_at']) ?></td>
                                <td class="px-6 py-5 flex items-center gap-2">
                                    <a href="index.php?page=pembayaran&action=restore&id=<?= $row['id_pembayaran'] ?>" class="px-3 py-1 text-xs font-medium text-emerald-300 bg-emerald-500/20 rounded-md hover:bg-emerald-500/30 transition" onclick="return confirm('Anda yakin ingin mengembalikan data ini?')">Restore</a>
                                    <a href="index.php?page=pembayaran&action=deletePermanent&id=<?= $row['id_pembayaran'] ?>" class="px-3 py-1 text-xs font-medium text-red-300 bg-red-500/20 rounded-md hover:bg-red-500/30 transition" onclick="return confirm('ANDA YAKIN? Data ini akan hilang selamanya!')">Hapus Permanen</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-4 pt-6 border-t border-white/10 mt-6">
                    <label for="bulk_action" class="text-sm font-medium text-text-primary-dark whitespace-nowrap">Aksi untuk yang dipilih:</label>
                    <select
                        name="bulk_action"
                        id="bulk_action"
                        class="w-full max-w-xs px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none border-white/20 focus:border-primary focus:ring-primary"
                    >
                        <option value="" class="bg-surface-dark">-- Pilih Aksi --</option>
                        <option value="restore" class="bg-surface-dark">Pulihkan (Restore)</option>
                        <option value="delete_permanent" class="bg-surface-dark">Hapus Permanen</option>
                    </select>
                    <button
                        type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300"
                        onclick="return confirm('Anda yakin ingin menerapkan aksi ini pada item yang dipilih?')"
                    >
                        Terapkan
                    </button>
                </div>

            </div>
        </form>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');

    checkAll.addEventListener('click', function() {
        rowCheckboxes.forEach(function(checkbox) {
            checkbox.checked = checkAll.checked;
        });
    });

    rowCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('click', function() {
            if (!this.checked) {
                checkAll.checked = false;
            } else if (document.querySelectorAll('.rowCheckbox:checked').length === rowCheckboxes.length) {
                checkAll.checked = true;
            }
        });
    });
});
</script>

<?php
include 'footer.php';
?>