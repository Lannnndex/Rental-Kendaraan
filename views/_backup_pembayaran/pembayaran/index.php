<?php
$active_page = "pembayaran";
include 'header.php';


function createSortLink($column, $text, $currentSortBy, $currentSortOrder, $currentSearch) {
    $nextSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
    $url = "index.php?page=pembayaran&sort_by=$column&sort_order=$nextSortOrder&q=" . urlencode($currentSearch);
    $indicator = '';
    if ($currentSortBy == $column) {
        $indicator = ($currentSortOrder == 'ASC') ? ' &#9650;' : ' &#9660;';
    }

    return "<a href=\"$url\" class=\"hover:text-text-secondary-dark/70 transition-colors\">$text$indicator</a>";
}

?>


        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-white">Manajemen Pembayaran</h2>
            <div class="flex items-center gap-4">
                <a href="index.php?page=pembayaran&action=recycleBin" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                    Data yang dihapus
                </a>
                <a href="index.php?page=pembayaran&action=create" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                    <span class="material-symbols-outlined text-base">add</span>
                    Tambah Pembayaran
                </a>
            </div>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 rounded-xl shadow-2xl border border-white/10">
            
            <form action="index.php" method="GET" class="flex items-center gap-4 mb-6">
                <input type="hidden" name="page" value="pembayaran">
                <div class="relative flex-grow">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary-dark">search</span>
                    <input 
                        class="w-full pl-12 pr-4 py-3 border border-white/20 bg-white/5 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark" 
                        placeholder="Cari nama pelanggan atau metode bayar..." 
                        type="text"
                        name="q"
                        value="<?= isset($search) ? htmlspecialchars($search) : '' ?>"
                    />
                </div>
                <button type="submit" class="px-4 py-3 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">Cari</button>
                <a href="index.php?page=pembayaran" class="px-4 py-3 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">Reset</a>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-text-secondary-dark uppercase border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('id_pembayaran', 'No.', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('id_rental', 'ID Rental', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('nama_pelanggan', 'Pelanggan', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('tgl_bayar', 'Tgl Bayar', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('jumlah_bayar', 'Jumlah', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('metode_bayar', 'Metode', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-text-primary-dark">
                        <?php 
                        // LOGIKA PENOMORAN DINAMIS
                        $nomor;
                        if ($sortOrder == 'DESC') {
                            $nomor = $totalResults - (($currentPage - 1) * $limit);
                        } else {
                            $nomor = ($currentPage - 1) * $limit + 1;
                        }

                        while($row = $result->fetch_assoc()): 
                        ?>
                        <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                            <td class="px-6 py-5 font-medium">
                                <?php 
                                    // Menampilkan nomor naik/turun
                                    if ($sortOrder == 'DESC') {
                                        echo $nomor--;
                                    } else {
                                        echo $nomor++;
                                    }
                                ?>
                            </td>
                            <td class="px-6 py-5">#<?= htmlspecialchars($row['id_rental']) ?></td>
                            <td class="px-6 py-5"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                            
                            <td class="px-6 py-5"><?= htmlspecialchars(date('d M Y', strtotime($row['tgl_bayar']))) ?></td>
                            
                            <td class="px-6 py-5 font-semibold text-primary">Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                            
                            <td class="px-6 py-5">
                                <?php 
                                    // Menggunakan badge untuk metode bayar
                                    $metode = htmlspecialchars(ucfirst($row['metode_bayar']));
                                    $metodeClass = match(strtolower($row['metode_bayar'])) {
                                        'transfer' => 'text-indigo-300 bg-indigo-500/20 border-indigo-500/30',
                                        'tunai' => 'text-yellow-300 bg-yellow-500/20 border-yellow-500/30',
                                        default => 'text-text-secondary-dark bg-white/10 border-white/20',
                                    };
                                ?>
                                <span class="inline-flex items-center px-3 py-1 text-xs font-medium <?= $metodeClass ?> rounded-full border">
                                    <?= $metode ?>
                                </span>
                            </td>

                            <td class="px-6 py-5 flex items-center gap-2">
                                <a href="index.php?page=pembayaran&action=edit&id=<?= $row['id_pembayaran'] ?>" class="px-3 py-1 text-xs font-medium text-amber-300 bg-amber-500/20 rounded-md hover:bg-amber-500/30 transition">Edit</a>
                                <a href="index.php?page=pembayaran&action=delete&id=<?= $row['id_pembayaran'] ?>" class="px-3 py-1 text-xs font-medium text-red-300 bg-red-500/20 rounded-md hover:bg-red-500/30 transition" onclick="return confirm('Apakah Anda yakin ingin menghapus data pembayaran ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center items-center pt-6 text-sm text-text-secondary-dark">
                <?php if($totalPages > 1): ?>
                <nav class="flex items-center gap-2">
                    <?php 
                    $baseUrl = "index.php?page=pembayaran&q=" . urlencode($search) . "&sort_by=$sortBy&sort_order=$sortOrder"; 
                    ?>
                    
                    <a href="<?= $baseUrl ?>&p=<?= $currentPage - 1 ?>" class="<?= $currentPage <= 1 ? 'pointer-events-none text-gray-700 bg-white/5' : '' ?> flex items-center justify-center w-10 h-10 border border-white/20 bg-white/10 rounded-md hover:bg-white/20 transition-colors">«</a>
                    
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?= $baseUrl ?>&p=<?= $i ?>" class="<?= $i == $currentPage ? 'z-10 border border-primary bg-primary text-white' : 'bg-white/10 border-white/20 text-text-primary-dark hover:bg-white/20' ?> flex items-center justify-center w-10 h-10 border rounded-md transition-colors"><?= $i ?></a>
                    <?php endfor; ?>

                    <a href="<?= $baseUrl ?>&p=<?= $currentPage + 1 ?>" class="<?= $currentPage >= $totalPages ? 'pointer-events-none text-gray-700 bg-white/5' : '' ?> flex items-center justify-center w-10 h-10 border border-white/20 bg-white/10 rounded-md hover:bg-white/20 transition-colors">»</a>
                </nav>
                <?php endif; ?>
            </div>
        </div>
 
<?php
include 'footer.php';
?>