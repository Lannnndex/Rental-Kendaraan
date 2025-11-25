<?php
// File: models/Pengembalian.php (Versi Bersih - Siap GitHub)

class Pengembalian {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Menghitung total data untuk paginasi, dengan filter pencarian
    public function countAll($search = '') {
        $sql = "SELECT COUNT(pg.id_pengembalian) as total FROM pengembalian pg
            JOIN rental r ON pg.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pg.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND p.nama LIKE ?";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm]; $types = 's';
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Mengambil semua data untuk tabel, dengan paginasi, pencarian, dan sorting
    public function getAll($search = '', $limit = 10, $offset = 0, $sortBy = 'id_pengembalian', $sortOrder = 'ASC') {
        $allowedSortColumns = ['id_pengembalian', 'id_rental', 'nama_pelanggan', 'tanggal_dikembalikan', 'denda'];
        $sortColumnMap = [
            'id_pengembalian' => 'pg.id_pengembalian', 'id_rental' => 'pg.id_rental',
            'nama_pelanggan' => 'p.nama', 'tanggal_dikembalikan' => 'pg.tanggal_dikembalikan',
            'denda' => 'pg.denda'
        ];
        if (!in_array($sortBy, $allowedSortColumns)) $sortBy = 'id_pengembalian';
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) $sortOrder = 'ASC';
        $orderByColumn = $sortColumnMap[$sortBy];
        
        $sql = "SELECT pg.*, p.nama AS nama_pelanggan FROM pengembalian pg
            JOIN rental r ON pg.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pg.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND p.nama LIKE ?";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm]; $types = 's';
        }
        $sql .= " ORDER BY $orderByColumn $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset; $types .= 'ii';
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Mengambil data by ID. $includeDeleted = true akan mencari di recycle bin.
    public function getById($id, $includeDeleted = false) {
        $sql = "SELECT * FROM pengembalian WHERE id_pengembalian = ?";
        
        if ($includeDeleted == false) {
            $sql .= " AND deleted_at IS NULL";
        }
        // Jika true, query akan mencari ID baik yang aktif maupun yang di soft-delete

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // --- (Fungsi CRUD Standar) ---

    public function create($id_rental, $tgl_dikembalikan, $denda) {
        $stmt = $this->conn->prepare("INSERT INTO pengembalian (id_rental, tanggal_dikembalikan, denda) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $id_rental, $tgl_dikembalikan, $denda);
        return $stmt->execute();
    }
    public function update($id, $id_rental, $tgl_dikembalikan, $denda) {
        $stmt = $this->conn->prepare("UPDATE pengembalian SET id_rental=?, tanggal_dikembalikan=?, denda=? WHERE id_pengembalian=?");
        $stmt->bind_param("ssdi", $id_rental, $tgl_dikembalikan, $denda, $id);
        return $stmt->execute();
    }
    
    // --- (Fungsi Soft Delete & Recycle Bin) ---

    public function delete($id) {
        // Ini adalah Soft Delete
        $stmt = $this->conn->prepare("UPDATE pengembalian SET deleted_at = NOW() WHERE id_pengembalian = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    
    public function getAllDeleted() {
        $sql = "SELECT pg.*, p.nama AS nama_pelanggan FROM pengembalian pg
            JOIN rental r ON pg.id_rental = r.id_rental
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            WHERE pg.deleted_at IS NOT NULL ORDER BY pg.deleted_at DESC";
        return $this->conn->query($sql);
    }
    
    public function restore($id) {
        $stmt = $this->conn->prepare("UPDATE pengembalian SET deleted_at = NULL WHERE id_pengembalian = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    
    public function deletePermanent($id) {
        $stmt = $this->conn->prepare("DELETE FROM pengembalian WHERE id_pengembalian = ?");
        $stmt->bind_param("i", $id); return $stmt->execute();
    }
    
    public function restoreBulk(array $ids) {
        if (empty($ids)) return false;
        $idList = implode(',', $ids);
        foreach ($ids as $id) { if (!is_numeric($id)) return false; }
        $sql = "UPDATE pengembalian SET deleted_at = NULL WHERE id_pengembalian IN ($idList)";
        $stmt = $this->conn->prepare($sql); return $stmt->execute();
    }
    
    public function deletePermanentBulk(array $ids) {
        if (empty($ids)) return false;
        $idList = implode(',', $ids);
        foreach ($ids as $id) { if (!is_numeric($id)) return false; }
        $sql = "DELETE FROM pengembalian WHERE id_pengembalian IN ($idList) AND deleted_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql); return $stmt->execute();
    }
    
    public function autoDeleteOld($days = 30) {
        $sql = "DELETE FROM pengembalian WHERE deleted_at IS NOT NULL AND deleted_at < NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days); return $stmt->execute();
    }
}
?>