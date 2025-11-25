<?php 
include 'header.php';
?>


    <main class="flex-1 p-8 overflow-y-auto">

        <div class="mb-8">
            <h2 class="text-3xl font-bold text-white">Tambah Pelanggan Baru</h2>
            <p class="text-text-secondary-dark mt-1">Silakan isi detail pelanggan di bawah ini.</p>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 md:p-8 rounded-xl shadow-2xl border border-white/10 max-w-2xl mx-auto">
            
            <form action="index.php?page=pelanggan&action=create" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">

                <div>
                    <label for="nama" class="block mb-2 text-sm font-medium text-text-primary-dark">Nama Pelanggan</label>
                    <input 
                        type="text" 
                        id="nama" 
                        name="nama" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['nama']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['nama'] ?? '') ?>"
                        placeholder="Cth: John Doe"
                    >
                    <?php if (isset($errors['nama'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['nama'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="alamat" class="block mb-2 text-sm font-medium text-text-primary-dark">Alamat</label>
                    <textarea 
                        id="alamat" 
                        name="alamat" 
                        rows="3" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['alamat']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        placeholder="Cth: Jl. Pahlawan No. 123, Surabaya"
                    ><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
                    <?php if (isset($errors['alamat'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['alamat'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="no_hp" class="block mb-2 text-sm font-medium text-text-primary-dark">No. Handphone</label>
                    <input 
                        type="text" 
                        id="no_hp" 
                        name="no_hp" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['no_hp']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['no_hp'] ?? '') ?>"
                        placeholder="Cth: 08123456789"
                    >
                    <?php if (isset($errors['no_hp'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['no_hp'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="no_ktp" class="block mb-2 text-sm font-medium text-text-primary-dark">No. KTP</label>
                    <input 
                        type="text" 
                        id="no_ktp" 
                        name="no_ktp" 
                        class="w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark 
                               <?= isset($errors['no_ktp']) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-white/20 focus:border-primary focus:ring-primary' ?>"
                        value="<?= htmlspecialchars($data['no_ktp'] ?? '') ?>"
                        placeholder="Cth: 3578012345670001"
                    >
                    <?php if (isset($errors['no_ktp'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= $errors['no_ktp'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="foto_sim" class="block mb-2 text-sm font-medium text-text-primary-dark">Foto SIM <span class="text-amber-300">(wajib)</span></label>
                    <input type="file" id="foto_sim" name="foto_sim" accept="image/*" required class="w-full text-sm text-white/80">
                    <div id="foto_preview" class="mt-2"></div>
                    <?php if (isset($errors['foto_sim']) || isset($errors['foto_sim_file'])): ?>
                        <p class="text-red-400 text-xs italic mt-2"><?= htmlspecialchars($errors['foto_sim'] ?? $errors['foto_sim_file']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="index.php?page=pelanggan" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
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
                        document.getElementById('foto_sim')?.addEventListener('change', function(e){
                            const preview = document.getElementById('foto_preview');
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

<?php
include 'footer.php';
?>