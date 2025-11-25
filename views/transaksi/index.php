<?php
// File: views/transaksi/index.php (Versi Bersih - Tombol Hapus AMAN)
$active_page = "transaksi";
include 'header.php';

function createSortLink($column, $text, $currentSortBy, $currentSortOrder, $currentSearch) {
    $nextSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
    $url = "index.php?page=transaksi&sort_by=$column&sort_order=$nextSortOrder&q=" . urlencode($currentSearch);
    $indicator = '';
    if ($currentSortBy == $column) {
        $indicator = ($currentSortOrder == 'ASC') ? ' &#9650;' : ' &#9660;';
    }
    return "<a href=\"$url\" class=\"hover:text-text-secondary-dark/70 transition-colors\">$text$indicator</a>";
}
?>
    <main class="flex-1 p-8 overflow-y-auto">

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-white">Manajemen Transaksi Sewa</h2>
            <div class="flex items-center gap-4">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="index.php?page=transaksi&action=recycleBin" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                    Data yang dihapus
                </a>
                <?php endif; ?>
                <a href="index.php?page=transaksi&action=create" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                    <span class="material-symbols-outlined text-base">add</span>
                    Tambah Transaksi
                </a>
            </div>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 rounded-xl shadow-2xl border border-white/10">
            
            <form action="index.php" method="GET" class="flex items-center gap-4 mb-6">
                <input type="hidden" name="page" value="transaksi">
                <div class="relative flex-grow">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary-dark">search</span>
                    <input 
                        class="w-full pl-12 pr-4 py-3 border border-white/20 bg-white/5 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark" 
                        placeholder="Cari nama pelanggan, merk, atau no. plat..." 
                        type="text"
                        name="q"
                        value="<?= isset($search) ? htmlspecialchars($search) : '' ?>"
                    />
                </div>
                <button type="submit" class="px-4 py-3 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">Cari</button>
                <a href="index.php?page=transaksi" class="px-4 py-3 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">Reset</a>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-text-secondary-dark uppercase border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('id_rental', 'No.', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('id_rental', 'ID Rental', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('nama_pelanggan', 'Pelanggan', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('merk_kendaraan', 'Kendaraan', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col"><?= createSortLink('tanggal_sewa', 'Periode', $sortBy, $sortOrder, $search) ?></th>
                            <th class="px-6 py-4 font-semibold" scope="col">Status</th>
                            <th class="px-6 py-4 font-semibold" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-text-primary-dark">
                        <?php 
                        $nomor;
                        if ($sortOrder == 'DESC') {
                            $nomor = $totalResults - (($currentPage - 1) * $limit);
                        } else {
                            $nomor = ($currentPage - 1) * $limit + 1;
                        }

                        if ($result->num_rows > 0):
                            while($row = $result->fetch_assoc()): 
                            ?>
                            <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-5 font-medium">
                                    <?php 
                                        if ($sortOrder == 'DESC') { echo $nomor--; } else { echo $nomor++; }
                                    ?>
                                </td>
                                <td class="px-6 py-5">#<?= htmlspecialchars($row['id_rental']) ?></td>
                                <td class="px-6 py-5"><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td class="px-6 py-5"><?= htmlspecialchars($row['merk_kendaraan']) ?> <span class="text-text-secondary-dark">(<?= htmlspecialchars($row['no_plat']) ?>)</span></td>
                                <td class="px-6 py-5"><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal_sewa']))) ?> - <?= htmlspecialchars($row['tanggal_kembali'] ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-') ?></td>
                                <?php
                                    $isPaid = false;
                                    if (isset($row['jumlah_bayar']) && $row['jumlah_bayar'] !== null) {
                                        $isPaid = floatval($row['jumlah_bayar']) >= floatval($row['total_biaya']);
                                    }
                                ?>
                                <td class="px-6 py-5">
                                    <?php if ($isPaid): ?>
                                        <span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-emerald-600 text-white">Telah Lunas</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-red-600 text-white">Belum Lunas</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="px-6 py-5 flex items-center gap-2">
                                    <a href="index.php?page=transaksi&action=show&id=<?= urlencode($row['id_rental']) ?>" class="px-3 py-1 text-xs font-medium text-sky-300 bg-sky-500/20 rounded-md hover:bg-sky-500/30 transition">Detail</a>
                                    <a href="index.php?page=transaksi&action=edit&id=<?= $row['id_rental'] ?>" class="px-3 py-1 text-xs font-medium text-amber-300 bg-amber-500/20 rounded-md hover:bg-amber-500/30 transition">Edit</a>
                                    <?php if (!$isPaid): ?>
                                        <a href="index.php?page=transaksi&action=payment&id=<?= $row['id_rental'] ?>" class="px-3 py-1 text-xs font-medium text-white bg-emerald-600/20 rounded-md hover:bg-emerald-600/30 transition">Bayar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center px-6 py-10">
                                    <span class="material-symbols-outlined text-4xl">search_off</span>
                                    <p class="mt-2">Data tidak ditemukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center items-center pt-6 text-sm text-text-secondary-dark">
                <?php if($totalPages > 1): ?>
                <nav class="flex items-center gap-2">
                    <?php 
                    $baseUrl = "index.php?page=transaksi&q=" . urlencode($search) . "&sort_by=$sortBy&sort_order=$sortOrder"; 
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