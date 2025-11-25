<?php
// File: header.php (Layout Utama)
$page_title = $page_title ?? 'Rental Kendaraan';
$active_page = $active_page ?? ''; // Variabel untuk menyorot menu aktif

// Ambil role dari session untuk menentukan menu yang tampil
$user_role = $_SESSION['role'] ?? 'karyawan';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= htmlspecialchars($page_title) ?> - Rental Kendaraan</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#4F46E5",
                        secondary: "#10B981", // Hijau
                        warning: "#F59E0B", // Kuning
                        danger: "#EF4444", // Merah
                        "background-dark": "#121212",
                        "surface-dark": "#1E1E1E",
                        "text-primary-dark": "#E2E8F0",
                        "text-secondary-dark": "#94A3B8",
                    },
                    fontFamily: {
                        display: ["Plus Jakarta Sans", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        select option {
            background: #1E1E1E !important; 
            color: #E2E8F0 !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        /* Style global untuk form, tombol, dll. */
        .form-label {
            @apply block mb-2 text-sm font-medium text-text-primary-dark;
        }
        .form-input {
            @apply w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark border-white/20 focus:border-primary focus:ring-primary;
        }
        .form-select {
            @apply w-full px-4 py-3 border bg-white/5 rounded-lg focus:ring-primary transition-all duration-300 text-white placeholder:text-text-secondary-dark border-white/20 focus:border-primary focus:ring-primary;
        }
        .form-error-message {
            @apply text-red-400 text-sm mt-1;
        }
        .btn-primary {
            @apply inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-medium text-white bg-primary rounded-lg shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all duration-300;
        }
        .btn-secondary {
             @apply inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-medium text-text-primary-dark bg-white/10 rounded-lg hover:bg-white/20 transition-all duration-300;
        }
        .btn-icon-secondary {
            @apply inline-flex items-center justify-center w-8 h-8 rounded-full text-text-secondary-dark hover:bg-white/10 hover:text-white transition-colors;
        }
        .btn-icon-danger {
            @apply inline-flex items-center justify-center w-8 h-8 rounded-full text-red-400 hover:bg-danger/20 hover:text-red-300 transition-colors;
        }
    </style>
    </head>
<body class="font-display bg-gradient-to-br from-gray-900 to-black text-text-primary-dark">
<div class="flex h-screen">
    
    <aside class="w-64 bg-black/30 backdrop-blur-sm flex flex-col border-r border-white/10">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-white">Rental Kendaraan</h1>
            <p class="text-sm text-text-secondary-dark mt-1">
                Login sebagai: 
                <strong class="text-text-primary-dark"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User') ?></strong>
                (<?= htmlspecialchars(ucfirst($user_role)) ?>)
            </p>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            
            <?php if ($user_role === 'admin' || $user_role === 'manajer'): ?>
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors 
                    <?= ($active_page == 'dashboard') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=dashboard">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'dashboard') ? 'text-primary' : '' ?>">dashboard</span> Dashboard
                </a>
            <?php endif; ?>

            <?php if ($user_role === 'admin' || $user_role === 'manajer' || $user_role === 'karyawan'): ?>
                
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors
                    <?= ($active_page == 'kendaraan') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=kendaraan">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'kendaraan') ? 'text-primary' : '' ?>">directions_car</span> Kendaraan
                </a>
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors
                    <?= ($active_page == 'pelanggan') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=pelanggan">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'pelanggan') ? 'text-primary' : '' ?>">groups</span> Pelanggan
                </a>
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors
                    <?= ($active_page == 'transaksi') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=transaksi">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'transaksi') ? 'text-primary' : '' ?>">receipt_long</span> Transaksi
                </a>
                <!-- Link Pembayaran telah diarsipkan; pembayaran sekarang disimpan di tabel `rental` -->
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors
                    <?= ($active_page == 'pengembalian') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=pengembalian">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'pengembalian') ? 'text-primary' : '' ?>">assignment_return</span> Pengembalian
                </a>
            <?php endif; ?>

            <?php if ($user_role === 'admin'): ?>
                <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors
                    <?= ($active_page == 'users') ? 'bg-primary/20 text-white font-semibold' : 'text-text-secondary-dark hover:bg-white/10' ?>" 
                    href="index.php?page=users">
                    <span class="material-symbols-outlined text-xl <?= ($active_page == 'users') ? 'text-primary' : '' ?>">manage_accounts</span> Kelola Pengguna
                </a>
            <?php endif; ?>
            </nav>

        <div class="p-4 border-t border-white/10 mt-auto">
            <a class="flex items-center gap-3 px-4 py-2 rounded-md transition-colors text-text-secondary-dark hover:bg-danger/20 hover:text-red-300" 
                href="index.php?page=auth&action=logout">
                <span class="material-symbols-outlined text-xl">logout</span> Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="max-w-4xl mx-auto mb-6 p-4 rounded-md bg-red-800/80 text-red-100">
                <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>