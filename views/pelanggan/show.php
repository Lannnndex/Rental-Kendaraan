<?php
include 'header.php';

$page_title = "Detail Pelanggan";
?>

    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Detail Pelanggan</h2>
            <p class="text-text-secondary-dark mt-1">Informasi lengkap pelanggan berikut termasuk foto SIM (jika tersedia).</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-3xl mx-auto">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-text-secondary-dark">Nama</p>
                    <h3 class="text-lg font-semibold text-white mb-4"><?= htmlspecialchars($data['nama']) ?></h3>

                    <p class="text-sm text-text-secondary-dark">Alamat</p>
                    <div class="prose text-white mb-4"><p><?= nl2br(htmlspecialchars($data['alamat'])) ?></p></div>

                    <p class="text-sm text-text-secondary-dark">No. Handphone</p>
                    <p class="text-white mb-4"><?= htmlspecialchars(
                        Sanitizer::formatPhone($data['no_hp'] ?? '')
                    ) ?></p>

                    <p class="text-sm text-text-secondary-dark">No. KTP</p>
                    <p class="text-white mb-4"><?= htmlspecialchars($data['no_ktp']) ?></p>

                </div>

                <div>
                    <p class="text-sm text-text-secondary-dark">Foto SIM</p>
                    <?php if (!empty($data['foto_sim']) && file_exists(__DIR__ . '/../../' . $data['foto_sim'])): ?>
                        <a href="<?= htmlspecialchars($data['foto_sim']) ?>" target="_blank" rel="noopener noreferrer">
                            <img src="<?= htmlspecialchars($data['foto_sim']) ?>" alt="Foto SIM <?= htmlspecialchars($data['nama']) ?>" class="w-full h-auto max-h-72 object-contain rounded-md border" />
                        </a>
                        <p class="text-text-secondary-dark text-sm mt-2">Klik gambar untuk memperbesar.</p>
                    <?php else: ?>
                        <div class="w-full h-48 flex items-center justify-center bg-white/5 rounded-md border border-white/10 text-text-secondary-dark">
                            Tidak ada foto SIM.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-6">
                <a href="index.php?page=pelanggan" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                    Kembali
                </a>
                <a href="index.php?page=pelanggan&action=edit&id=<?= urlencode($data['no_ktp']) ?>" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-500/80 rounded-lg hover:bg-amber-500/90 transition">Edit</a>
            </div>

        </div>

    </main>

<?php
include 'footer.php';
?>