<?php
// File: index.php (Versi Ultra-Bersih - Siap GitHub)

require_once "config.php";
require_once "core/ModelFactory.php";

$factory = new ModelFactory($conn);

$page   = isset($_GET['page']) ? $_GET['page'] : "dashboard";
$action = isset($_GET['action']) ? $_GET['action'] : "index";

// Middleware: Paksa ke login jika belum ada session
$isAuthPage = ($page === 'auth');
if (!isset($_SESSION['user_id']) && !$isAuthPage) {
    header('Location: index.php?page=auth&action=showLoginForm');
    exit;
}

// Middleware: Paksa ke dashboard jika sudah login tapi akses halaman login
if (isset($_SESSION['user_id']) && $page === 'auth' && $action === 'showLoginForm') {
    $role = $_SESSION['role'] ?? 'karyawan';
    if ($role === 'admin' || $role === 'manajer') {
        header('Location: index.php?page=dashboard');
    } else {
        header('Location: index.php?page=kendaraan');
    }
    exit;
}

// Routing: Muat Controller berdasarkan 'page'
switch ($page) {
    case "dashboard":
        require_once "controllers/DashboardController.php";
        $controller = new DashboardController($factory);
        break;
    case "kendaraan":
        require_once "controllers/KendaraanController.php";
        $controller = new KendaraanController($factory);
        break;
    case "pelanggan":
        require_once "controllers/PelangganController.php";
        $controller = new PelangganController($factory);
        break;
    case "transaksi":
        require_once "controllers/TransaksiController.php";
        $controller = new TransaksiController($factory);
        break;
    case "pembayaran":
        require_once "controllers/PembayaranController.php";
        $controller = new PembayaranController($factory);
        break;
    case "pengembalian":
        require_once "controllers/PengembalianController.php";
        $controller = new PengembalianController($factory);
        break;
    case "users":
        require_once "controllers/UserController.php";
        $controller = new UserController($factory);
        break;
    case "auth":
        require_once "controllers/AuthController.php";
        $controller = new AuthController($conn);
        break;
    default:
        http_response_code(404);
        include 'views/errors/404.php';
        exit;
}

// Action Runner: Jalankan 'action' (method) pada Controller
if (method_exists($controller, $action)) {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    // Penanganan khusus untuk passing error login dari session ke view
    if ($action === 'showLoginForm' && isset($_SESSION['login_error'])) {
        $error_message = $_SESSION['login_error'];
        unset($_SESSION['login_error']);
        $controller->showLoginForm($error_message);
    } else {
        // Panggil action (method) dengan atau tanpa ID
        $controller->$action($id);
    }
} else {
    http_response_code(404);
    include 'views/errors/404.php';
    exit;
}

// PDO connections close automatically - no need for manual close()
// Remove: $conn->close();
?>