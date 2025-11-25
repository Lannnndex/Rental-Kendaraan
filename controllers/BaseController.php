<?php
// File: controllers/BaseController.php (Versi Bersih - Siap GitHub)

/**
 * Controller induk yang berisi fungsi hak akses (RBAC).
 * Semua controller lain (kecuali Auth) akan extends ke sini.
 */
class BaseController {
    
    protected $factory;
    protected $role;

    public function __construct($factory) {
        $this->factory = $factory;
        
        // Ambil role dari session saat controller dibuat
        $this->role = $_SESSION['role'] ?? null; 
    }

    // "Penjaga" Hak Akses (Authorization)
    // Memeriksa apakah role user ada di dalam array $allowedRoles.
    protected function authorize(array $allowedRoles) {
        
        // Jika role user tidak ada di dalam array $allowedRoles
        if (!in_array($this->role, $allowedRoles)) {
            
            // Tampilkan halaman 403 (Akses Ditolak)
            http_response_code(403);
            include 'views/errors/403.php'; 
            exit;
        }
    }

    // Safe wrapper to execute operations and catch unexpected exceptions.
    // Returns the callable's return value on success, or false on error and
    // sets a generic flash error message in the session.
    protected function safe(callable $fn) {
        try {
            return call_user_func($fn);
        } catch (Throwable $e) {
            // Log detailed error for server-side debugging
            error_log("[SafeHandler] " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
            $_SESSION['flash_error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
            return false;
        }
    }
}
?>