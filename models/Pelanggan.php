<?php
// File: models/Pelanggan.php (Versi Bersih - Siap GitHub)

class Pelanggan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Menghitung total pelanggan aktif (untuk Dashboard)
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM pelanggan WHERE deleted_at IS NULL";
        $result = $this->conn->query($sql); 
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Menghitung total data untuk paginasi, dengan filter pencarian
    public function countAll($search = '') {
        // pelanggan now keyed by no_ktp
        $sql = "SELECT COUNT(no_ktp) as total FROM pelanggan WHERE deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (nama LIKE ? OR alamat LIKE ? OR no_hp LIKE ? OR no_ktp LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm]; $types = 'ssss';
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }
    
    // Mengambil semua data untuk tabel, dengan paginasi, pencarian, dan sorting
    public function getAll($search = '', $limit = 10, $offset = 0, $sortBy = 'no_ktp', $sortOrder = 'ASC') {
        $allowedSortColumns = ['no_ktp', 'nama', 'alamat', 'no_hp'];
        if (!in_array($sortBy, $allowedSortColumns)) $sortBy = 'no_ktp';
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) $sortOrder = 'ASC';
        $sql = "SELECT * FROM pelanggan WHERE deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (nama LIKE ? OR alamat LIKE ? OR no_hp LIKE ? OR no_ktp LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm]; $types = 'ssss';
        }
        $sql .= " ORDER BY $sortBy $sortOrder LIMIT ? OFFSET ?";
        $params[] = $limit; $params[] = $offset; $types .= 'ii';
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Mengambil satu data pelanggan berdasarkan ID
    public function getById($no_ktp) {
        // $no_ktp expected as string PK
        $stmt = $this->conn->prepare("SELECT * FROM pelanggan WHERE no_ktp = ?");
        $stmt->bind_param("s", $no_ktp); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    // --- (Fungsi CRUD Standar) ---

    // Note: use ($no_ktp, $nama, $alamat, $no_hp, $foto_sim) to match DB column order
    public function create($no_ktp, $nama, $alamat, $no_hp, $foto_sim = null) {
        $stmt = $this->conn->prepare("INSERT INTO pelanggan (no_ktp, nama, alamat, no_hp, foto_sim) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $no_ktp, $nama, $alamat, $no_hp, $foto_sim);
        return $stmt->execute();
    }
    public function update($no_ktp, $nama, $alamat, $no_hp, $foto_sim = null) {
        $stmt = $this->conn->prepare("UPDATE pelanggan SET nama=?, alamat=?, no_hp=?, foto_sim=? WHERE no_ktp=?");
        $stmt->bind_param("sssss", $nama, $alamat, $no_hp, $foto_sim, $no_ktp);
        return $stmt->execute();
    }

    // --- (Fungsi Soft Delete & Recycle Bin) ---

    public function delete($no_ktp) {
        // Ini adalah Soft Delete
        $stmt = $this->conn->prepare("UPDATE pelanggan SET deleted_at = NOW() WHERE no_ktp = ?");
        $stmt->bind_param("s", $no_ktp); return $stmt->execute();
    }
    public function getAllDeleted() {
        return $this->conn->query("SELECT * FROM pelanggan WHERE deleted_at IS NOT NULL ORDER BY no_ktp ASC");
    }
    public function restore($no_ktp) {
        $stmt = $this->conn->prepare("UPDATE pelanggan SET deleted_at = NULL WHERE no_ktp = ?");
        $stmt->bind_param("s", $no_ktp); return $stmt->execute();
    }
    public function deletePermanent($no_ktp) {
        $stmt = $this->conn->prepare("DELETE FROM pelanggan WHERE no_ktp = ?");
        $stmt->bind_param("s", $no_ktp); return $stmt->execute();
    }
    public function restoreBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE pelanggan SET deleted_at = NULL WHERE no_ktp IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function deletePermanentBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM pelanggan WHERE no_ktp IN ($placeholders) AND deleted_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function autoDeleteOld($days = 30) {
        $sql = "DELETE FROM pelanggan WHERE deleted_at IS NOT NULL AND deleted_at < NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days); return $stmt->execute();
    }
}
?>