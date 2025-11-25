<?php 
include 'header.php';
?>
    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Tambah Transaksi Sewa</h2>
            <p class="text-text-secondary-dark mt-1">Lengkapi detail transaksi di bawah ini.</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-2xl mx-auto">
            
            <form action="index.php?page=transaksi&action=create" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div>
                    <label for="no_ktp" class="block mb-2 text-sm font-medium text-text-primary-dark">Pelanggan</label>
                    <input id="filter_pelanggan" type="text" placeholder="Cari pelanggan..."  class="w-full px-5 mb-4 py-3 border bg-white/5 rounded-lg text-white" />
                        <select 
                            id="no_ktp" 
                            name="no_ktp" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none 
                               <?= isset($errors['id_pelanggan']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                    >
                        <option value="" class="bg-surface-dark text-text-secondary-dark">-- Pilih Pelanggan --</option>
                            <?php if (is_object($pelanggan) && method_exists($pelanggan, 'data_seek')) { $pelanggan->data_seek(0); } elseif (function_exists('mysqli_data_seek')) { mysqli_data_seek($pelanggan, 0); } // Reset pointer loop jika $pelanggan digunakan di tempat lain ?>
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
                    <input id="filter_kendaraan" type="text" placeholder="Cari kendaraan... merk, plat..." class="w-full px-5 mb-4 py-3 border bg-white/5 rounded-lg text-white" />
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
                                    <?= htmlspecialchars($k['merk']) ?> (<?= htmlspecialchars($k['no_plat']) ?>) - Rp <?= number_format($k['harga_per_jam'] ?? 0, 0, ',', '.') ?>/jam
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
                            value="<?= htmlspecialchars($data['tanggal_sewa'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($data['tanggal_kembali'] ?? '') ?>"
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
                    <input type="text" readonly class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? '-') ?>">
                    <input type="hidden" name="id_users" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
                </div>

                <!-- Pembayaran (opsional, tampil jika klik Bayar Sekarang) -->
                <?php $showPayment = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar_now'])) ? true : false; ?>
                <div id="payment_section" style="display: <?= $showPayment ? 'block' : 'none' ?>;">
                    <h3 class="text-white text-lg font-semibold mt-4">Pembayaran</h3>
                    <div>
                        <label for="jumlah_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Jumlah Bayar (Rp)</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($data['jumlah_bayar'] ?? '') ?>" placeholder="Masukkan jumlah bayar jika membayar sekarang">
                        <?php if (isset($errors['jumlah_bayar'])): ?><p class="text-red-400 text-xs italic mt-2"><?= $errors['jumlah_bayar'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="tgl_bayar" class="block mb-2 text-sm font-medium text-text-primary-dark">Tanggal Bayar</label>
                        <input type="date" id="tgl_bayar" name="tgl_bayar" class="w-full px-4 py-3 border bg-white/5 rounded-lg text-white" value="<?= htmlspecialchars($data['tgl_bayar'] ?? '') ?>">
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
                            Simpan Tanpa Bayar
                        </button>
                        <input type="hidden" name="bayar_now" id="bayar_now_input" value="">
                        <button type="button" id="btn_bayar_now" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg shadow-lg shadow-emerald-300 hover:bg-emerald-500 transition-all duration-300">
                            <span class="material-symbols-outlined text-base">attach_money</span>
                            Simpan & Bayar Sekarang
                        </button>
                </div>
            </form>
        </div>
 
<?php
include 'footer.php';
?>

<script>
// Realtime total calculation: reads selected kendaraan data-harga and datetime-local duration
(() => {
    // Lightweight select filter: hides options that don't match query
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
    // initial calc
    setTimeout(recalc, 200);
})();
// Show payment section when bayar_now button clicked (pre-submit)
(function(){
    const btn = document.getElementById('btn_bayar_now');
    const payment = document.getElementById('payment_section');
    if(!btn || !payment) return;
    btn.addEventListener('click', function(e){
        // Show payment fields and mark bayar_now
        payment.style.display = 'block';
        const input = document.getElementById('bayar_now_input');
        if (input) input.value = '1';
        // change this button to submit so user can submit when ready
        try {
            btn.type = 'submit';
            btn.innerHTML = '<span class="material-symbols-outlined text-base">save</span> Simpan & Bayar Sekarang';
            btn.classList.remove('bg-emerald-600');
            btn.classList.add('bg-primary');
        } catch (err) {}
        // focus first payment field
        const first = document.getElementById('jumlah_bayar') || document.getElementById('tgl_bayar');
        if (first) first.focus();
        // scroll into view
        payment.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
})();

// Client-side validation: when bayar_now is set ensure payment fields filled
// Note: client-side payment validation intentionally omitted to allow flow where user clicks "Simpan & Bayar Sekarang"
// but fills payment details on a dedicated payment page. Server-side will validate if payment fields are provided.
</script>