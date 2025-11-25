<?php 

include 'header.php';
?>


    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Edit Kendaraan</h2>
            <p class="text-text-secondary-dark mt-1">Perbarui detail formulir di bawah ini.</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-2xl mx-auto">
            
            <form action="index.php?page=kendaraan&action=edit&id=<?= urlencode($data['no_plat']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div>
                    <label for="jenis" class="block mb-2 text-sm font-medium text-text-primary-dark">Jenis Kendaraan</label>
                    <input 
                        type="text" 
                        id="jenis" 
                        name="jenis" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['jenis']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['jenis'] ?? '') ?>"
                        placeholder="Cth: Mobil, Motor, Truk..."
                    >
                    <?php if (isset($errors['jenis'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['jenis'] ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="merk" class="block mb-2 text-sm font-medium text-text-primary-dark">Merk</label>
                    <input 
                        type="text" 
                        id="merk" 
                        name="merk" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['merk']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['merk'] ?? '') ?>"
                        placeholder="Cth: Toyota Avanza, Honda Beat"
                    >
                    <?php if (isset($errors['merk'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['merk'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="no_plat" class="block mb-2 text-sm font-medium text-text-primary-dark">No. Plat</label>
                    <input 
                        type="text" 
                        id="no_plat" 
                        name="no_plat" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['no_plat']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['no_plat'] ?? '') ?>"
                        placeholder="Cth: B 1234 XYZ"
                    >
                    <?php if (isset($errors['no_plat'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['no_plat'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="harga_per_jam" class="block mb-2 text-sm font-medium text-text-primary-dark">Harga per Jam (Rp)</label>
                    <input
                        type="number"
                        step="0.01"
                        id="harga_per_jam"
                        name="harga_per_jam"
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark <?= isset($errors['harga_per_jam']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['harga_per_jam'] ?? '') ?>"
                        placeholder="Cth: 50000"
                    >
                    <?php if (isset($errors['harga_per_jam'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['harga_per_jam'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="image" class="block mb-2 text-sm font-medium text-text-primary-dark">Gambar Kendaraan (opsional)</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full text-sm text-white/80">
                    <?php if (!empty($data['image'])): ?>
                        <p class="text-text-secondary-dark text-sm mt-2">Gambar saat ini: <a href="<?= htmlspecialchars($data['image']) ?>" target="_blank" class="underline">Lihat</a></p>
                    <?php endif; ?>
                    <div id="image_preview" class="mt-2">
                        <?php if (!empty($data['image'])): ?>
                            <img src="<?= htmlspecialchars($data['image']) ?>" style="max-width:200px;max-height:150px" class="rounded-md border" />
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="status" class="block mb-2 text-sm font-medium text-text-primary-dark">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white appearance-none 
                               <?= isset($errors['status']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                    >
                        <option value="tersedia" class="bg-gray-800" <?= ($data['status'] ?? 'tersedia') == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="disewa" class="bg-gray-800" <?= ($data['status'] ?? '') == 'disewa' ? 'selected' : '' ?>>Disewa</option>
                    </select>
                    <?php if (isset($errors['status'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['status'] ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="index.php?page=kendaraan" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
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
document.getElementById('image')?.addEventListener('change', function(e){
    const preview = document.getElementById('image_preview');
    preview.innerHTML = '';
    const file = this.files && this.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    const img = document.createElement('img');
    img.src = url;
    img.style.maxWidth = '200px';
    img.style.maxHeight = '150px';
    img.className = 'rounded-md border';
    preview.appendChild(img);
});
</script>