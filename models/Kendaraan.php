<?php
// File: models/Kendaraan.php (Versi Bersih - Siap GitHub)

class Kendaraan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Menghitung total aset aktif (non-recycle bin)
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM kendaraan WHERE deleted_at IS NULL";
        $result = $this->conn->query($sql); 
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Menghitung total data untuk paginasi, dengan filter pencarian
    public function countAll($search = '') {
        // use no_plat as PK in revised schema
        $sql = "SELECT COUNT(no_plat) as total FROM kendaraan WHERE deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (jenis LIKE ? OR merk LIKE ? OR no_plat LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm]; $types = 'sss';
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    
    // Mengambil semua data untuk tabel, dengan paginasi, pencarian, dan sorting
    public function getAll($search = '', $limit = 10, $offset = 0, $sortBy = 'no_plat', $sortOrder = 'ASC') {
        $allowedSortColumns = ['no_plat', 'jenis', 'merk', 'status'];
        if (!in_array($sortBy, $allowedSortColumns)) $sortBy = 'no_plat';
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) $sortOrder = 'ASC';
        $sql = "SELECT * FROM kendaraan WHERE deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (jenis LIKE ? OR merk LIKE ? OR no_plat LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm]; $types = 'sss';
        }
        $sql .= " ORDER BY $sortBy $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset; $types .= 'ii';
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Mengambil data by ID. $includeDeleted = true akan mencari di recycle bin.
    public function getById($id, $includeDeleted = false) {
        // $id is expected to be `no_plat` string in revised schema
        $sql = "SELECT * FROM kendaraan WHERE no_plat = ?";
        if ($includeDeleted == false) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // --- (Fungsi CRUD Standar) ---

    // Note: parameter order is ($no_plat, $jenis, $merk, $harga_per_jam, $image, $status)
    public function create($no_plat, $jenis, $merk, $harga_per_jam = 0.00, $image = null, $status = 'tersedia') {
        $stmt = $this->conn->prepare("INSERT INTO kendaraan (no_plat, jenis, merk, harga_per_jam, image, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdss", $no_plat, $jenis, $merk, $harga_per_jam, $image, $status);
        return $stmt->execute();
    }
    public function update($no_plat, $jenis, $merk, $harga_per_jam = 0.00, $image = null, $status = 'tersedia', $original_no_plat = null) {
        // If original_no_plat provided, update the row identified by original_no_plat (useful when primary key changed)
        if ($original_no_plat !== null && $original_no_plat !== $no_plat) {
            $stmt = $this->conn->prepare("UPDATE kendaraan SET no_plat=?, jenis=?, merk=?, harga_per_jam=?, image=?, status=? WHERE no_plat=?");
            $stmt->bind_param("sssdsss", $no_plat, $jenis, $merk, $harga_per_jam, $image, $status, $original_no_plat);
            return $stmt->execute();
        }

        $stmt = $this->conn->prepare("UPDATE kendaraan SET jenis=?, merk=?, harga_per_jam=?, image=?, status=? WHERE no_plat=?");
        // types: jenis(s), merk(s), harga_per_jam(d), image(s), status(s), no_plat(s)
        $stmt->bind_param("ssdsss", $jenis, $merk, $harga_per_jam, $image, $status, $no_plat);
        return $stmt->execute();
    }

    // --- (Fungsi Soft Delete & Recycle Bin) ---

    public function delete($no_plat) {
        // Ini adalah Soft Delete
        $stmt = $this->conn->prepare("UPDATE kendaraan SET deleted_at = NOW() WHERE no_plat = ?");
        $stmt->bind_param("s", $no_plat); return $stmt->execute();
    }
    public function getAllDeleted() {
        return $this->conn->query("SELECT * FROM kendaraan WHERE deleted_at IS NOT NULL ORDER BY no_plat ASC");
    }
    public function restore($no_plat) {
        $stmt = $this->conn->prepare("UPDATE kendaraan SET deleted_at = NULL WHERE no_plat = ?");
        $stmt->bind_param("s", $no_plat); return $stmt->execute();
    }
    public function deletePermanent($no_plat) {
        $stmt = $this->conn->prepare("DELETE FROM kendaraan WHERE no_plat = ?");
        $stmt->bind_param("s", $no_plat); return $stmt->execute();
    }
    public function restoreBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE kendaraan SET deleted_at = NULL WHERE no_plat IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function deletePermanentBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM kendaraan WHERE no_plat IN ($placeholders) AND deleted_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function autoDeleteOld($days = 30) {
        $sql = "DELETE FROM kendaraan WHERE deleted_at IS NOT NULL AND deleted_at < NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days); return $stmt->execute();
    }
    
    // (Fungsi helper untuk validasi - tidak ditampilkan penuh)
    public function isPlatExists($no_plat) { /* ... */ }
    public function isPlatExistsForAnotherVehicle($no_plat, $current_id) { /* ... */ }

    // --- (Fungsi Logika Bisnis & Kustom) ---

    // Mengambil semua kendaraan yang 'tersedia' (untuk dropdown form Transaksi)
    public function getAllAvailable() {
        $sql = "SELECT no_plat, merk, no_plat AS plat, status, harga_per_jam 
                FROM kendaraan 
                WHERE status = 'tersedia' AND deleted_at IS NULL 
                ORDER BY merk ASC";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Mengubah status kendaraan (dipanggil oleh Controller Transaksi/Pengembalian)
    public function updateStatus($no_plat, $status) {
        $sql = "UPDATE kendaraan SET status = ? WHERE no_plat = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $status, $no_plat);
        return $stmt->execute();
    }

    // Menghitung kendaraan 'tersedia' per jenis (untuk Dashboard)
    public function getAvailableCountByType() {
        $sql = "SELECT 
                    jenis, 
                    COUNT(*) as jumlah_tersedia 
                FROM 
                    kendaraan 
                WHERE 
                    status = 'tersedia' 
                    AND deleted_at IS NULL 
                GROUP BY 
                    jenis";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            return [];
        }

        $counts = [];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['jenis']] = $row['jumlah_tersedia'];
        }
        
        return $counts;
    }

}
?>