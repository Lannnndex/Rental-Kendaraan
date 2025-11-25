<?php
// File: models/Pembayaran.php (Versi Bersih - Siap GitHub)

class Pembayaran {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Menghitung total data untuk paginasi, dengan filter pencarian
    public function countAll($search = '') {
        $sql = "SELECT COUNT(pb.id_pembayaran) as total FROM pembayaran pb
            JOIN rental r ON pb.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pb.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (p.nama LIKE ? OR pb.metode_bayar LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm]; $types = 'ss';
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Mengambil semua data untuk tabel, dengan paginasi, pencarian, dan sorting
    public function getAll($search = '', $limit = 10, $offset = 0, $sortBy = 'id_pembayaran', $sortOrder = 'ASC') {
        $allowedSortColumns = ['id_pembayaran', 'id_rental', 'nama_pelanggan', 'tgl_bayar', 'jumlah_bayar', 'metode_bayar'];
        $sortColumnMap = [
            'id_pembayaran' => 'pb.id_pembayaran', 'id_rental' => 'pb.id_rental',
            'nama_pelanggan' => 'p.nama', 'tgl_bayar' => 'pb.tgl_bayar',
            'jumlah_bayar' => 'pb.jumlah_bayar', 'metode_bayar' => 'pb.metode_bayar'
        ];
        if (!in_array($sortBy, $allowedSortColumns)) $sortBy = 'id_pembayaran';
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) $sortOrder = 'ASC';
        $orderByColumn = $sortColumnMap[$sortBy];
        
        $sql = "SELECT pb.*, p.nama AS nama_pelanggan FROM pembayaran pb
            JOIN rental r ON pb.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pb.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (p.nama LIKE ? OR pb.metode_bayar LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm]; $types = 'ss';
        }
        $sql .= " ORDER BY $orderByColumn $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset; $types .= 'ii';
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Mengambil satu data pembayaran berdasarkan ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pembayaran WHERE id_pembayaran = ? AND deleted_at IS NULL");
        $stmt->bind_param("i", $id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // --- (Fungsi CRUD Standar) ---

    public function create($id_rental, $jumlah_bayar, $tgl_bayar, $metode_bayar) {
        $stmt = $this->conn->prepare("INSERT INTO pembayaran (id_rental, jumlah_bayar, tgl_bayar, metode_bayar) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $id_rental, $jumlah_bayar, $tgl_bayar, $metode_bayar);
        return $stmt->execute();
    }
    public function update($id, $id_rental, $jumlah_bayar, $tgl_bayar, $metode_bayar) {
        $stmt = $this->conn->prepare("UPDATE pembayaran SET id_rental=?, jumlah_bayar=?, tgl_bayar=?, metode_bayar=? WHERE id_pembayaran=?");
        $stmt->bind_param("sdssi", $id_rental, $jumlah_bayar, $tgl_bayar, $metode_bayar, $id);
        return $stmt->execute();
    }

    // --- (Fungsi Soft Delete & Recycle Bin) ---

    public function delete($id) {
        // Ini adalah Soft Delete
        $stmt = $this->conn->prepare("UPDATE pembayaran SET deleted_at = NOW() WHERE id_pembayaran = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    public function getAllDeleted() {
        $sql = "SELECT pb.*, p.nama AS nama_pelanggan FROM pembayaran pb
            JOIN rental r ON pb.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pb.deleted_at IS NOT NULL ORDER BY pb.deleted_at DESC";
        return $this->conn->query($sql);
    }
    public function restore($id) {
        $stmt = $this->conn->prepare("UPDATE pembayaran SET deleted_at = NULL WHERE id_pembayaran = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    public function deletePermanent($id) {
        $stmt = $this->conn->prepare("DELETE FROM pembayaran WHERE id_pembayaran = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    public function restoreBulk(array $ids) {
        if (empty($ids)) return false;
        $idList = implode(',', $ids);
        foreach ($ids as $id) { if (!is_numeric($id)) return false; }
        $sql = "UPDATE pembayaran SET deleted_at = NULL WHERE id_pembayaran IN ($idList)";
        $stmt = $this->conn->prepare($sql); return $stmt->execute();
    }
    public function deletePermanentBulk(array $ids) {
        if (empty($ids)) return false;
        $idList = implode(',', $ids);
        foreach ($ids as $id) { if (!is_numeric($id)) return false; }
        $sql = "DELETE FROM pembayaran WHERE id_pembayaran IN ($idList) AND deleted_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql); return $stmt->execute();
    }
    public function autoDeleteOld($days = 30) {
        $sql = "DELETE FROM pembayaran WHERE deleted_at IS NOT NULL AND deleted_at < NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days); return $stmt->execute();
    }
}
?>