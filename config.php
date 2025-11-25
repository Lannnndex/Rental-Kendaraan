<?php
// File: config.php (Versi Ultra-Bersih - Siap GitHub)

// Mulai session di paling atas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Koneksi Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'rental_kendaraan'; 

// Create a PDO-backed adapter (keeps existing code-compatible wrapper)
require_once __DIR__ . '/core/PDOAdapter.php';
$conn = new PDOAdapter($host, $user, $pass, $db);

if (!empty($conn->error)) {
    die("Koneksi Database Gagal: " . $conn->error);
}

// 2. Muat file Inti (Core)
require_once "core/CSRF.php";
require_once "core/Validator.php";
require_once "core/Sanitizer.php";
require_once "core/DateHelper.php";

?>