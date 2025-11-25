<?php
// File: models/TransaksiSewa.php (Versi Bersih - Siap GitHub)

class TransaksiSewa {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Menghitung total transaksi aktif (untuk Dashboard)
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM rental WHERE deleted_at IS NULL";
        $result = $this->conn->query($sql);
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    // Menghitung total pendapatan (untuk Dashboard)
    public function sumActive() {
        $sql = "SELECT SUM(total_biaya) as total FROM rental WHERE deleted_at IS NULL";
        $result = $this->conn->query($sql);
        if (!$result) return 0;
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    // Menghitung total data untuk paginasi, dengan filter pencarian
    public function countAll($search = '') {
        $sql = "SELECT COUNT(r.id_rental) as total
                FROM rental r
                JOIN pelanggan p ON r.no_ktp = p.no_ktp
                JOIN kendaraan k ON r.no_plat = k.no_plat
                LEFT JOIN users u ON r.id_users = u.id_users
                WHERE r.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (p.nama LIKE ? OR k.merk LIKE ? OR k.no_plat LIKE ? OR r.id_rental LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm]; $types = 'ssss';
        }
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

    // Mengambil semua data untuk tabel, dengan paginasi, pencarian, dan sorting
    public function getAll($search = '', $limit = 10, $offset = 0, $sortBy = 'id_rental', $sortOrder = 'ASC') {
        $allowedSortColumns = ['id_rental', 'nama_pelanggan', 'merk_kendaraan', 'tanggal_sewa', 'tanggal_kembali', 'total_biaya'];
        $sortColumnMap = [
            'id_rental' => 'r.id_rental', 'nama_pelanggan' => 'p.nama',
            'merk_kendaraan' => 'k.merk', 'tanggal_sewa' => 'r.tanggal_sewa',
            'tanggal_kembali' => 'r.tanggal_kembali', 'total_biaya' => 'r.total_biaya'
        ];
        if (!in_array($sortBy, $allowedSortColumns)) $sortBy = 'id_rental';
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) $sortOrder = 'ASC';
        $orderByColumn = $sortColumnMap[$sortBy];
        
        $sql = "SELECT r.*, p.nama AS nama_pelanggan, k.merk AS merk_kendaraan, k.no_plat, k.harga_per_jam AS harga_per_jam, u.nama_lengkap AS nama_karyawan
            FROM rental r
            JOIN pelanggan p ON r.no_ktp = p.no_ktp
            JOIN kendaraan k ON r.no_plat = k.no_plat
            LEFT JOIN users u ON r.id_users = u.id_users
            WHERE r.deleted_at IS NULL";
        $params = []; $types = '';
        if (!empty($search)) {
            $sql .= " AND (p.nama LIKE ? OR k.merk LIKE ? OR k.no_plat LIKE ? OR r.id_rental LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm]; $types = 'ssss';
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
        // Return joined data so views have access to pelanggan, kendaraan, and karyawan fields
        $sql = "SELECT r.*, p.nama AS nama_pelanggan, p.no_hp, k.merk AS merk_kendaraan, k.no_plat, k.harga_per_jam AS harga_per_jam, u.nama_lengkap AS nama_karyawan
                FROM rental r
                JOIN pelanggan p ON r.no_ktp = p.no_ktp
                JOIN kendaraan k ON r.no_plat = k.no_plat
                LEFT JOIN users u ON r.id_users = u.id_users
                WHERE r.id_rental = ?";

        if ($includeDeleted == false) {
            $sql .= " AND r.deleted_at IS NULL";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Menghasilkan ID rental pendek bertipe A001, A002, ...
    public function getNextIdRental($prefix = 'A', $pad = 3) {
        $sql = "SELECT id_rental FROM rental WHERE id_rental LIKE CONCAT(?, '%') ORDER BY id_rental DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return $prefix . str_pad(1, $pad, '0', STR_PAD_LEFT);
        $stmt->bind_param('s', $prefix);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $last = $res['id_rental'] ?? null;
        if (!$last) {
            return $prefix . str_pad(1, $pad, '0', STR_PAD_LEFT);
        }
        // Ambil angka terakhir dari ID (hapus semua non-digit)
        $num = (int)preg_replace('/\D/', '', $last);
        $next = $num + 1;
        return $prefix . str_pad($next, $pad, '0', STR_PAD_LEFT);
    }
    
    // --- (Fungsi CRUD Standar) ---

    public function create($id_rental, $no_plat, $id_users, $no_ktp, $tanggal_sewa, $tanggal_kembali = null, $total_biaya = null, $jumlah_bayar = null, $tanggal_bayar = null, $metode_bayar = null) {
        $stmt = $this->conn->prepare("INSERT INTO rental (id_rental, no_plat, id_users, no_ktp, tanggal_sewa, tanggal_kembali, total_biaya, jumlah_bayar, tanggal_bayar, metode_bayar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // types: id_rental(s), no_plat(s), id_users(i), no_ktp(s), tanggal_sewa(s), tanggal_kembali(s), total_biaya(d), jumlah_bayar(d), tanggal_bayar(s), metode_bayar(s)
        $stmt->bind_param("ssisssddss", $id_rental, $no_plat, $id_users, $no_ktp, $tanggal_sewa, $tanggal_kembali, $total_biaya, $jumlah_bayar, $tanggal_bayar, $metode_bayar);
        return $stmt->execute();
    }
    public function update($id_rental, $no_ktp, $no_plat, $id_users, $tanggal_sewa, $tanggal_kembali = null, $total_biaya = null) {
        $stmt = $this->conn->prepare("UPDATE rental SET no_ktp=?, no_plat=?, id_users=?, tanggal_sewa=?, tanggal_kembali=?, total_biaya=? WHERE id_rental=?");
        $stmt->bind_param("ssissds", $no_ktp, $no_plat, $id_users, $tanggal_sewa, $tanggal_kembali, $total_biaya, $id_rental);
        return $stmt->execute();
    }

    // Update payment fields on a rental (jumlah_bayar, tanggal_bayar, metode_bayar)
    public function updatePayment($id_rental, $jumlah_bayar = null, $tanggal_bayar = null, $metode_bayar = null) {
        $sql = "UPDATE rental SET jumlah_bayar = ?, tanggal_bayar = ?, metode_bayar = ? WHERE id_rental = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        // types: jumlah_bayar (double), tanggal_bayar (string), metode_bayar (string), id_rental (string)
        $stmt->bind_param("dsss", $jumlah_bayar, $tanggal_bayar, $metode_bayar, $id_rental);
        return $stmt->execute();
    }

    // --- (Fungsi Soft Delete & Recycle Bin) ---

    public function delete($id) {
        // Ini adalah Soft Delete
        $stmt = $this->conn->prepare("UPDATE rental SET deleted_at = NOW() WHERE id_rental = ?");
        $stmt->bind_param("s", $id); return $stmt->execute();
    }
    public function getAllDeleted() {
        $sql = "SELECT r.*, p.nama AS nama_pelanggan, k.merk AS merk_kendaraan, k.no_plat
                FROM rental r
                JOIN pelanggan p ON r.no_ktp = p.no_ktp
                JOIN kendaraan k ON r.no_plat = k.no_plat
                WHERE r.deleted_at IS NOT NULL ORDER BY r.deleted_at DESC";
        return $this->conn->query($sql);
    }
    public function restore($id) {
        $stmt = $this->conn->prepare("UPDATE rental SET deleted_at = NULL WHERE id_rental = ?");
        $stmt->bind_param("s", $id); return $stmt->execute();
    }
    public function deletePermanent($id) {
        $stmt = $this->conn->prepare("DELETE FROM rental WHERE id_rental = ?");
        $stmt->bind_param("s", $id); return $stmt->execute();
    }
    public function restoreBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE rental SET deleted_at = NULL WHERE id_rental IN ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function deletePermanentBulk(array $ids) {
        if (empty($ids)) return false;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM rental WHERE id_rental IN ($placeholders) AND deleted_at IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', count($ids));
        $stmt->bind_param($types, ...$ids);
        return $stmt->execute();
    }
    public function autoDeleteOld($days = 30) {
        $sql = "DELETE FROM rental WHERE deleted_at IS NOT NULL AND deleted_at < NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days); return $stmt->execute();
    }
}
?>