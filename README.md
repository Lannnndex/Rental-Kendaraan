# 🚗 Sistem Manajemen Rental Kendaraan 

Ini adalah aplikasi web sederhana yang dibangun menggunakan **PHP Native** untuk mengelola operasi rental kendaraan. Aplikasi ini menerapkan pola desain Model-View-Controller (MVC) dasar dan memiliki sistem hak akses berbasis peran (Role-Based Access Control - RBAC).

---

## 🌟 Fitur Utama

Sistem ini memiliki 3 level pengguna dengan hak akses yang berbeda:

### 1. Karyawan (Role: `karyawan`)
* **CRUD** data Pelanggan
* **CRUD** data Transaksi Sewa
* **CRUD** data Pembayaran
* **CRUD** data Pengembalian
* **Read-Only** (hanya melihat) data Kendaraan

### 2. Manajer (Role: `manajer`)
* **Semua hak akses Karyawan**
* **Melihat Dashboard** (ringkasan pendapatan dan ketersediaan aset)
* **CRUD** data Kendaraan (menambah, mengedit, menghapus aset)

### 3. Admin (Role: `admin`)
* **Semua hak akses Manajer**
* **CRUD** data Pengguna (mengelola akun admin, manajer, dan karyawan)
* **Recycle Bin**: Dapat me-*restore* atau menghapus permanen data yang sudah di-*soft-delete* di semua modul.

---

## 🚀 Cara Menjalankan Proyek

1.  **Clone Repositori**
    ```bash
    git clone [URL_GITHUB_ANDA]
    ```

2.  **Database**
    * Buka `phpMyAdmin`.
    * Buat database baru dengan nama `rental_kendaraan`.
    * Impor file `rental_kendaraan.sql` 

3.  **Koneksi**
    * Buka file `config.php`.
    * Sesuaikan kredensial database (`$host`, `$user`, `$pass`, `$db`) jika diperlukan.

4.  **Jalankan**
    * Pindahkan seluruh folder proyek ke dalam direktori `htdocs` XAMPP Anda.
    * Akses proyek melalui browser: `http://localhost/nama_folder_proyek_anda/`

---

## 🔒 Akun Demo

Anda dapat menggunakan akun berikut untuk menguji hak akses:

* **Admin**
    * **Username:** admin
    * **Password:** admin123
* **Manajer**
    * **Username:** manajer
    * **Password:** manajer123
* **Karyawan**
    * **Username:** karyawan
    * **Password:** karyawan123
