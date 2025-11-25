<?php
// File: views/dashboard/index.php (Versi Bersih - Siap GitHub)

// Set variabel untuk layout header
$page_title = "Dashboard Ringkasan";
$active_page = "dashboard";

// Panggil layout header
include 'header.php';

// Ambil data dari DashboardController
$summary = $summary ?? [
    'total_pelanggan' => 'N/A', 
    'total_transaksi' => 'N/A', 
    'total_pendapatan' => 0
]; 
$vehicleCounts = $vehicleCounts ?? []; 
$kpi = $kpi ?? [
    'ongoing_rentals' => 0,
    'available_vehicles' => 0,
    'monthly_revenue' => 0,
    'recent_transactions' => []
];
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Dashboard Ringkasan</h1>
    <p class="text-text-secondary-dark mt-1">Selamat datang! Berikut ringkasan data rental Anda.</p>
</div>


<h2 class="text-2xl font-semibold text-white mb-4">Ketersediaan Aset (Real-Time)</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <?php 
    // Helper function untuk memilih ikon berdasarkan jenis
    function getIconForVehicle($jenis) {
        $jenisLower = strtolower($jenis);
        if (strpos($jenisLower, 'mobil') !== false) return 'directions_car';
        if (strpos($jenisLower, 'motor') !== false) return 'two_wheeler';
        if (strpos($jenisLower, 'truk') !== false) return 'local_shipping';
        return 'key'; // Ikon default
    }

    // Loop untuk menampilkan kartu ketersediaan kendaraan
    if (!empty($vehicleCounts)):
        foreach ($vehicleCounts as $jenis => $jumlah): 
    ?>
    
    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-primary/20 text-primary border border-primary/30">
                <span class="material-symbols-outlined text-3xl"><?= getIconForVehicle($jenis) ?></span>
            </div>
            <div class="ml-4">
                <p class="text-sm text-text-secondary-dark font-medium"><?= htmlspecialchars($jenis) ?> Tersedia</p>
                <p class="text-3xl font-bold text-text-primary-dark"><?= htmlspecialchars($jumlah) ?></p>
            </div>
        </div>
    </div>

    <?php 
        endforeach; 
    else: 
    ?>
    <div class="col-span-1 sm:col-span-2 lg:col-span-4 bg-black/30 backdrop-blur-sm rounded-xl p-6 border border-white/10">
        <p class="text-text-secondary-dark text-center">Tidak ada kendaraan yang tersedia saat ini.</p>
    </div>
    <?php endif; ?>

</div> 
<h2 class="text-2xl font-semibold text-white mb-4">Ringkasan Bisnis</h2>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-secondary/20 text-secondary border border-secondary/30">
                 <span class="material-symbols-outlined text-3xl">group</span>
            </div>
            <div class="ml-4">
                <p class="text-sm text-text-secondary-dark font-medium">Total Pelanggan</p>
                <p class="text-3xl font-bold text-text-primary-dark"><?= htmlspecialchars($summary['total_pelanggan']) ?></p>
            </div>
        </div>
    </div>

    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-warning/20 text-warning border border-warning/30">
                <span class="material-symbols-outlined text-3xl">receipt_long</span>
            </div>
            <div class="ml-4">
                <p class="text-sm text-text-secondary-dark font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold text-text-primary-dark"><?= htmlspecialchars($summary['total_transaksi']) ?></p>
            </div>
        </div>
    </div>

    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-danger/20 text-danger border border-danger/30">
                <span class="material-symbols-outlined text-3xl">attach_money</span>
            </div>
            <div class="ml-4">
                <p class="text-sm text-text-secondary-dark font-medium">Total Pendapatan</p>
                <p class="text-3xl font-bold text-text-primary-dark">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></p>
            </div>
        </div>
    </div>

</div> 
 
<h2 class="text-2xl font-semibold text-white mb-4 mt-8">Key Performance Indicators</h2>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <p class="text-sm text-text-secondary-dark">Rental Sedang Berlangsung</p>
        <p class="text-3xl font-bold text-text-primary-dark"><?= htmlspecialchars($kpi['ongoing_rentals']) ?></p>
    </div>

    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <p class="text-sm text-text-secondary-dark">Kendaraan Tersedia</p>
        <p class="text-3xl font-bold text-text-primary-dark"><?= htmlspecialchars($kpi['available_vehicles']) ?></p>
    </div>

    <div class="bg-black/30 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/10 hover-scale">
        <p class="text-sm text-text-secondary-dark">Pendapatan Bulan Ini</p>
        <p class="text-3xl font-bold text-text-primary-dark">Rp <?= number_format($kpi['monthly_revenue'] ?? 0, 0, ',', '.') ?></p>
    </div>
</div>

<h2 class="text-2xl font-semibold text-white mb-4">Transaksi Terbaru</h2>
<div class="bg-black/30 backdrop-blur-sm rounded-xl p-4 border border-white/10">
    <?php $recent = $kpi['recent_transactions'] ?? []; ?>
    <?php if (!empty($recent)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-xs text-text-secondary-dark uppercase border-b border-white/10">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Pelanggan</th>
                    <th class="px-4 py-2">Kendaraan</th>
                    <th class="px-4 py-2">Periode</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-text-primary-dark">
                <?php foreach($recent as $r): ?>
                <?php
                    $isPaid = isset($r['jumlah_bayar']) && $r['jumlah_bayar'] !== null && floatval($r['jumlah_bayar']) >= floatval($r['total_biaya']);
                ?>
                <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                    <td class="px-4 py-2">#<?= htmlspecialchars($r['id_rental']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($r['merk_kendaraan']) ?> <span class="text-text-secondary-dark">(<?= htmlspecialchars($r['no_plat']) ?>)</span></td>
                    <td class="px-4 py-2"><?= htmlspecialchars(date('d M', strtotime($r['tanggal_sewa']))) ?> - <?= htmlspecialchars($r['tanggal_kembali'] ? date('d M', strtotime($r['tanggal_kembali'])) : '-') ?></td>
                    <td class="px-4 py-2 font-semibold text-primary">Rp <?= number_format($r['total_biaya'] ?? 0, 0, ',', '.') ?></td>
                    <td class="px-4 py-2"><?= $isPaid ? '<span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-emerald-600 text-white">Lunas</span>' : '<span class="inline-flex items-center whitespace-nowrap gap-2 px-2 py-1 text-xs font-semibold rounded-md bg-red-600 text-white">Belum Lunas</span>' ?></td>
                    <td class="px-4 py-2"><a href="index.php?page=transaksi&action=show&id=<?= urlencode($r['id_rental']) ?>" class="text-sky-300">Detail</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="text-text-secondary-dark">Belum ada transaksi untuk ditampilkan.</p>
    <?php endif; ?>
</div>
<?php 
// Panggil layout footer
include 'footer.php'; 
?>