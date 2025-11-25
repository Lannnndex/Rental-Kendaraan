<?php
// File: views/kendaraan/index.php (Versi Bersih - Tombol Hapus AMAN)
$page_title = "Manajemen Kendaraan";
$active_page = "kendaraan";
include 'header.php';

$search = $search ?? '';
$sortBy = $sortBy ?? 'id_kendaraan';
$sortOrder = $sortOrder ?? 'ASC';
$totalResults = $totalResults ?? 0;
$limit = $limit ?? 10;
$currentPage = $currentPage ?? 1;

function createSortLink($column, $text, $currentSortBy, $currentSortOrder, $currentSearch) {
    $nextSortOrder = ($currentSortBy == $column && $currentSortOrder == 'ASC') ? 'DESC' : 'ASC';
    $url = "index.php?page=kendaraan&sort_by=$column&sort_order=$nextSortOrder&q=" . urlencode($currentSearch);
    $indicator = '';
    if ($currentSortBy == $column) {
        $indicator = ($currentSortOrder == 'ASC') ? ' &#9650;' : ' &#9660;';
    }
    return "<a href=\"$url\" class=\"hover:text-text-secondary-dark/70 transition-colors\">$text$indicator</a>";
}
?>

    <main class="flex-1 p-8 overflow-y-auto">

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-white">Manajemen Kendaraan</h2>
            <div class="flex items-center gap-4">
                
                <?php if ($user_role === 'admin'): ?>
                    <a href="index.php?page=kendaraan&action=recycleBin" class="px-5 py-2.5 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">
                        Data yang dihapus
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'admin' || $user_role === 'manajer'): ?>
                    <a href="index.php?page=kendaraan&action=create" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">
                        <span class="material-symbols-outlined text-base">add</span>
                        Tambah Kendaraan
                    </a>
                <?php endif; ?>
                </div>
        </div>

        <div class="bg-black/30 backdrop-blur-sm p-6 rounded-xl shadow-2xl border border-white/10">
            
            <form action="index.php" method="GET" class="flex items-center gap-4 mb-6">
                <input type="hidden" name="page" value="kendaraan">
                <div class="relative flex-grow">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-text-secondary-dark">search</span>
                    <input 
                        class="w-full pl-12 pr-4 py-3 border border-white/20 bg-white/5 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark" 
                        placeholder="Cari jenis, merk, atau no. plat..." 
                        type="text"
                        name="q"
                        value="<?= htmlspecialchars($search) ?>"
                    />
                </div>
                <button type="submit" class="px-4 py-3 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300">Cari</button>
                <a href="index.php?page=kendaraan" class="px-4 py-3 text-sm font-medium bg-white/10 border border-white/20 text-text-secondary-dark rounded-lg shadow-sm hover:bg-white/20 transition-colors">Reset</a>
            </form>

            <div>
                <?php if ($result->num_rows > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="bg-white/5 p-4 rounded-xl border border-white/8 shadow-sm hover:shadow-md transition">
                                <div class="h-40 w-full bg-gray-800 rounded-md overflow-hidden flex items-center justify-center mb-4">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['merk']) ?>" class="object-cover h-full w-full" />
                                    <?php else: ?>
                                        <span class="text-text-secondary-dark">No Image</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-lg font-semibold mb-1 text-white"><?= htmlspecialchars($row['merk']) ?></h3>
                                <p class="text-sm text-text-secondary-dark mb-2"><?= htmlspecialchars($row['jenis']) ?> • <span class="font-medium"><?= htmlspecialchars($row['no_plat']) ?></span></p>
                                <p class="text-sm text-text-secondary-dark mb-3">Harga: <span class="font-medium">Rp <?= number_format($row['harga_per_jam'] ?? 0, 0, ',', '.') ?></span> / jam</p>

                                <?php 
                                    $status = htmlspecialchars($row['status']);
                                    $statusClass = ''; $dotClass = '';
                                    if ($status == 'tersedia') {
                                        $statusClass = 'text-emerald-300 bg-emerald-500/20 border border-emerald-500/30';
                                        $dotClass = 'bg-emerald-400';
                                    } else if ($status == 'disewa') {
                                        $statusClass = 'text-amber-300 bg-amber-500/20 border border-amber-500/30';
                                        $dotClass = 'bg-amber-400';
                                    } else if ($status == 'perawatan') {
                                        $statusClass = 'text-gray-300 bg-gray-500/20 border border-gray-500/30';
                                        $dotClass = 'bg-gray-400';
                                    }
                                ?>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-medium <?= $statusClass ?> rounded-full">
                                        <span class="w-2 h-2 rounded-full <?= $dotClass ?>"></span>
                                        <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <?php if ($user_role === 'admin' || $user_role === 'manajer'): ?>
                                            <a href="index.php?page=kendaraan&action=edit&id=<?= urlencode($row['no_plat']) ?>" class="px-3 py-1 text-xs font-medium text-amber-300 bg-amber-500/20 rounded-md hover:bg-amber-500/30 transition">Edit</a>
                                            <form method="POST" action="index.php?page=kendaraan&action=delete" class="m-0 p-0">
                                                <input type="hidden" name="id_to_delete" value="<?= htmlspecialchars($row['no_plat']) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::getToken() ?>">
                                                <button type="submit" class="px-3 py-1 text-xs font-medium text-red-300 bg-red-500/20 rounded-md hover:bg-red-500/30 transition" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="index.php?page=transaksi&action=create&no_plat=<?= urlencode($row['no_plat']) ?>" class="px-3 py-1 text-xs font-medium text-white bg-primary rounded-md">Sewa</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-10">
                        <span class="material-symbols-outlined text-4xl">search_off</span>
                        <p class="mt-2">Data tidak ditemukan.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex justify-center items-center pt-6 text-sm text-text-secondary-dark">
                <?php if($totalPages > 1): ?>
                <nav class="flex items-center gap-2">
                    <?php 
                    $baseUrl = "index.php?page=kendaraan&q=" . urlencode($search) . "&sort_by=$sortBy&sort_order=$sortOrder"; 
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
    </main>

<?php
include 'footer.php';
?>