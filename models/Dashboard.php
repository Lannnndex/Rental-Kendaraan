<?php
// File: models/Dashboard.php (Versi Bersih - Siap GitHub)

class Dashboard {
    private $conn; 

    public function __construct($db) {
        $this->conn = $db; 
    }

    /**
     * Mengambil data ringkasan untuk kartu-kartu di Dashboard.
     * Catatan: total_kendaraan diambil dari KendaraanModel::getAvailableCountByType()
     */
    public function getSummary() {
        $summary = [];

        // Hitung total pelanggan
        $sqlPelanggan = "SELECT COUNT(*) as total FROM pelanggan WHERE deleted_at IS NULL";
        $resultPelanggan = $this->conn->query($sqlPelanggan);
        $summary['total_pelanggan'] = $resultPelanggan->fetch_assoc()['total'];

        // Hitung total transaksi (menggunakan tabel `rental` yang baru)
        $sqlTransaksi = "SELECT COUNT(*) as total FROM rental WHERE deleted_at IS NULL";
        $resultTransaksi = $this->conn->query($sqlTransaksi);
        $summary['total_transaksi'] = $resultTransaksi->fetch_assoc()['total'] ?? 0;

        // Hitung total pendapatan (dari kolom total_biaya pada `rental`)
        $sqlPendapatan = "SELECT SUM(total_biaya) as total FROM rental WHERE deleted_at IS NULL";
        $resultPendapatan = $this->conn->query($sqlPendapatan);
        $summary['total_pendapatan'] = $resultPendapatan->fetch_assoc()['total'] ?? 0;

        return $summary;
    }

    // Hitungan rental yang sedang aktif (waktu sekarang berada di antara tanggal_sewa dan tanggal_kembali)
    public function getOngoingRentalsCount() {
        $sql = "SELECT COUNT(*) as total FROM rental WHERE deleted_at IS NULL AND tanggal_sewa <= NOW() AND tanggal_kembali >= NOW()";
        $result = $this->conn->query($sql);
        if (!$result) return 0;
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Kendaraan tersedia (total)
    public function getAvailableVehiclesCount() {
        $sql = "SELECT COUNT(*) as total FROM kendaraan WHERE status = 'tersedia' AND deleted_at IS NULL";
        $result = $this->conn->query($sql);
        if (!$result) return 0;
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Pendapatan bulan ini
    public function getMonthlyRevenueCurrent() {
        $sql = "SELECT SUM(total_biaya) as total FROM rental WHERE deleted_at IS NULL AND MONTH(tanggal_sewa) = MONTH(NOW()) AND YEAR(tanggal_sewa) = YEAR(NOW())";
        $result = $this->conn->query($sql);
        if (!$result) return 0;
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // Ambil transaksi terbaru (limit)
    public function getRecentTransactions($limit = 5) {
        $sql = "SELECT r.id_rental, r.tanggal_sewa, r.tanggal_kembali, r.total_biaya, r.jumlah_bayar, r.tanggal_bayar, p.nama AS nama_pelanggan, k.merk AS merk_kendaraan, k.no_plat
                FROM rental r
                JOIN pelanggan p ON r.no_ktp = p.no_ktp
                JOIN kendaraan k ON r.no_plat = k.no_plat
                WHERE r.deleted_at IS NULL
                ORDER BY r.tanggal_sewa DESC
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }
}
?>