<?php
// File: controllers/AuthController.php (Versi Bersih - Siap GitHub)

// AuthController tidak mewarisi BaseController karena menangani login/logout
// sebelum session pengguna (hak akses) dibuat.
class AuthController {
    
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Menampilkan form login
    public function showLoginForm($error_message = null) {
        // Cek apakah ada error dari redirect
        if (isset($_SESSION['login_error'])) {
            $error_message = $_SESSION['login_error'];
            unset($_SESSION['login_error']); // Hapus setelah dibaca
        }
        
        include 'views/auth/login.php';
    }

    // Memproses data login dari form POST
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Cari user berdasarkan username
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Verifikasi password
                if (password_verify($password, $user['password'])) {
                    // Password benar, buat session baru
                    session_regenerate_id(true); // Keamanan: cegah session fixation
                    
                    // Support both legacy `id_user` and new `id_users` column
                    $_SESSION['user_id'] = $user['id_users'] ?? $user['id_user'] ?? null;
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Arahkan berdasarkan role
                    if ($user['role'] === 'admin' || $user['role'] === 'manajer') {
                        header('Location: index.php?page=dashboard');
                    } else {
                        // Default redirect untuk Karyawan
                        header('Location: index.php?page=kendaraan');
                    }
                    exit;
                }
            }
            
            // Jika username/password salah, panggil form login lagi dengan pesan error
            $this->showLoginForm("Username atau password salah.");
            exit;
        } else {
            // Jika diakses via GET, redirect ke form login
            header('Location: index.php?page=auth&action=showLoginForm');
            exit;
        }
    }

    // Menghancurkan session dan mengarahkan ke halaman login
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?page=auth&action=showLoginForm');
        exit;
    }
}
?>