<?php
include 'header.php';
?>

    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Tambah Data Pengembalian</h2>
            <p class="text-text-secondary-dark mt-1">Lengkapi detail pengembalian di bawah ini.</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-2xl mx-auto">

            <form action="index.php?page=pengembalian&action=create" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div>
                    <label for="id_rental" class="block mb-2 text-sm font-medium text-text-primary-dark">Transaksi (ID Rental)</label>
                    <input id="filter_id_rental" type="text" placeholder="Cari transaksi (ID, pelanggan, kendaraan)..." class="w-full px-5 mb-4 py-3 border bg-white/5 rounded-lg text-white" />
                    <select
                        id="id_rental"
                        name="id_rental"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none
                               <?= isset($errors['id_rental']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                    >
                        <option value="" class="bg-surface-dark text-text-secondary-dark">-- Pilih Transaksi --</option>
                        <?php if (is_object($transaksi) && method_exists($transaksi, 'data_seek')) { $transaksi->data_seek(0); } elseif (function_exists('mysqli_data_seek')) { mysqli_data_seek($transaksi, 0); } // Reset pointer loop ?>
                        <?php while($t = $transaksi->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($t['id_rental']) ?>" class="bg-surface-dark"
                                data-tgl-kembali="<?= htmlspecialchars($t['tanggal_kembali'] ?? '') ?>"
                                data-harga-per-jam="<?= htmlspecialchars($t['harga_per_jam'] ?? '') ?>"
                                <?= (isset($data['id_rental']) && $data['id_rental'] == $t['id_rental']) ? 'selected' : '' ?>>
                                #<?= htmlspecialchars($t['id_rental']) ?> - <?= htmlspecialchars($t['nama_pelanggan'] ?? '') ?> (<?= htmlspecialchars($t['merk_kendaraan'] ?? '') ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if (isset($errors['id_rental'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['id_rental'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="tanggal_dikembalikan" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Dikembalikan</label>
                    <input
                        type="datetime-local"
                        id="tanggal_dikembalikan"
                        name="tanggal_dikembalikan"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark
                               <?= isset($errors['tanggal_dikembalikan']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['tanggal_dikembalikan'] ?? '') ?>"
                    >
                    <?php if (isset($errors['tanggal_dikembalikan'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['tanggal_dikembalikan'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="denda" class="block mb-2 text-sm font-medium text-text-primary-dark">Denda (Rp) — kosongkan untuk auto-hitung</label>
                    <input
                        type="number"
                        id="denda"
                        name="denda"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark
                               <?= isset($errors['denda']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['denda'] ?? '0') ?>" placeholder="Masukkan hanya angka, cth: 50000"
                    >
                    <?php if (isset($errors['denda'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['denda'] ?></p>
                    <?php endif; ?>
                    <p id="denda_info" class="text-text-secondary-dark text-sm mt-2">Denda akan dihitung otomatis jika terlambat mengembalikan.</p>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="index.php?page=pengembalian" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                        <span class="material-symbols-outlined text-base">save</span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

<script>
    // Lightweight select filter for pengembalian form
    (function() {
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
        makeFilter('filter_id_rental', 'id_rental');
    })();

    // Auto-calculate fine (denda) when transaction or return datetime changes
    (function(){
        const select = document.getElementById('id_rental');
        const tanggalEl = document.getElementById('tanggal_dikembalikan');
        const dendaEl = document.getElementById('denda');
        const infoEl = document.getElementById('denda_info');

        function parseDateLocal(v){ if(!v) return null; return new Date(v); }

        function computeAndShow(){
            if(!select || !tanggalEl || !dendaEl) return;
            const opt = select.selectedOptions[0];
            if(!opt) return;
            const plannedRaw = opt.getAttribute('data-tgl-kembali');
            const hargaRaw = opt.getAttribute('data-harga-per-jam') || '0';
            if(!plannedRaw) {
                infoEl.textContent = 'Pilih transaksi untuk menghitung denda.';
                return;
            }
            // plannedRaw may be in MySQL DATETIME format; convert to iso if needed
            let planned = null;
            try {
                // if contains space ("YYYY-MM-DD HH:MM:SS"), convert to YYYY-MM-DDTHH:MM
                if (plannedRaw.indexOf(' ') !== -1) {
                    planned = new Date(plannedRaw.replace(' ', 'T'));
                } else {
                    planned = new Date(plannedRaw);
                }
            } catch (e) { planned = null; }
            const actual = parseDateLocal(tanggalEl.value);
            if(!actual || !planned) {
                infoEl.textContent = 'Atur tanggal dikembalikan untuk melihat estimasi denda.';
                return;
            }
            const secondsLate = (actual.getTime() - planned.getTime()) / 1000;
            if(secondsLate <= 0) {
                dendaEl.value = 0;
                infoEl.textContent = 'Tidak terlambat — denda 0.';
                return;
            }
            const hoursLate = Math.ceil(secondsLate / 3600);
            const harga = parseFloat(hargaRaw) || 0;
            const fine = Math.round(hoursLate * harga);
            dendaEl.value = fine;
            infoEl.textContent = `Telat ${hoursLate} jam — estimasi denda Rp ${fine.toLocaleString('id-ID')}.`;
        }

        if(select) select.addEventListener('change', computeAndShow);
        if(tanggalEl) tanggalEl.addEventListener('change', computeAndShow);
        // also compute on load if values present
        setTimeout(computeAndShow, 200);
    })();
</script>

<?php
include 'footer.php';
?>