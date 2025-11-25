<?php
// File: models/User.php (Versi Bersih - Siap GitHub)

class User {
    
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Mengambil semua data pengguna (untuk halaman Kelola Pengguna)
    public function getAll() {
        // return id_user alias for backward compatibility with controllers
        $sql = "SELECT id_users AS id_user, nama_lengkap, username, role FROM {$this->table}";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Mengambil satu data pengguna berdasarkan ID (untuk form Edit)
    public function getById($id) {
        // use id_users internally but return id_user for compatibility
        $stmt = $this->conn->prepare("SELECT id_users AS id_user, nama_lengkap, username, role FROM {$this->table} WHERE id_users = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Membuat pengguna baru
    public function create($nama_lengkap, $username, $hashed_password, $role) {
        $sql = "INSERT INTO {$this->table} (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $nama_lengkap, $username, $hashed_password, $role);
        return $stmt->execute();
    }

    // Memperbarui data pengguna (dengan logika password opsional)
    public function update($id, $nama_lengkap, $username, $role, $password_baru) {
        
        // Cek apakah password baru diisi
        if (!empty($password_baru)) {
            // Jika ya, update password
            $hashed_password = password_hash($password_baru, PASSWORD_BCRYPT);
            $sql = "UPDATE {$this->table} SET nama_lengkap = ?, username = ?, role = ?, password = ? WHERE id_users = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssi", $nama_lengkap, $username, $role, $hashed_password, $id);
        } else {
            // Jika tidak, jangan update password
            $sql = "UPDATE {$this->table} SET nama_lengkap = ?, username = ?, role = ? WHERE id_users = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $nama_lengkap, $username, $role, $id);
        }
        return $stmt->execute();
    }

    // Menghapus pengguna (PERMANENT DELETE - tidak ada soft delete)
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id_users = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>