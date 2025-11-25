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
    - Alternatif (Windows CMD + XAMPP MySQL): buka Command Prompt dan jalankan:

```bat
cd \xampp\mysql\bin
mysql -u root -p rental_kendaraan < "C:\xampp\htdocs\rental_kendaraan\rental_kendaraan.sql"
```

    (Jika MySQL root tidak berpassword, omit `-p`.)

3.  **Koneksi**
    * Buka file `config.php`.
    * Sesuaikan kredensial database (`$host`, `$user`, `$pass`, `$db`) jika diperlukan.

4.  **Jalankan**
    * Pindahkan seluruh folder proyek ke dalam direktori `htdocs` XAMPP Anda.
    * Akses proyek melalui browser: `http://localhost/nama_folder_proyek_anda/`

---

## 🔧 Catatan Migrasi & Perubahan Penting

- Database schema diperbarui ke ERD baru:
    - `pelanggan` menggunakan `no_ktp` (varchar) sebagai primary key dan menyimpan `foto_sim`.
    - `kendaraan` menggunakan `no_plat` (varchar) sebagai primary key, menyertakan `harga_per_jam` (decimal) dan `image`.
    - `rental` menggabungkan transaksi + pembayaran, primary key `id_rental` (varchar), berisi `tanggal_sewa`, `tanggal_kembali`, `total_biaya`, `jumlah_bayar`, `tanggal_bayar`, `metode_bayar`.
    - `pengembalian` menyimpan `id_rental` dan `tanggal_dikembalikan` serta `denda`.

- Folder uploaded files: controllers menyimpan gambar ke `public/uploads/pelanggan/` dan `public/uploads/kendaraan/`. Pastikan folder ini bisa ditulis oleh server.

- Views dan controllers telah diubah untuk:
    - Menggunakan `no_ktp` / `no_plat` / `id_rental` sebagai identifier.
    - Menyediakan form upload untuk foto SIM dan gambar kendaraan.
    - Menghitung `total_biaya` (hours * `harga_per_jam`) server-side; client-side JS also shows realtime total in the transaksi form.

## ✅ Tes Singkat Setelah Migrasi

1. Buka `http://localhost/rental_kendaraan`.
2. Tambah `Kendaraan` (isi `harga_per_jam` dan upload gambar kecil).
3. Tambah `Pelanggan` (isi `no_ktp` dan upload foto SIM).
4. Buat `Transaksi` (pilih pelanggan dan kendaraan, gunakan `datetime-local` untuk jam) — coba tombol "Simpan & Bayar Sekarang".
5. Buka menu `Pembayaran` untuk melihat pembayaran yang sudah diprefill.

Jika ada error, periksa `php_error.log` (XAMPP) dan pastikan folder `public/uploads/*` writable.

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

---

## Manual Smoke Test (Quick checklist)

1. Import database: using Command Prompt (Windows/XAMPP):

```bat
cd \xampp\mysql\bin
mysql -u root -p rental_kendaraan < "C:\xampp\htdocs\rental_kendaraan\rental_kendaraan.sql"
```

2. Pastikan folder upload ada dan writable oleh server:

- `public/uploads/pelanggan/`
- `public/uploads/kendaraan/`

3. Jalankan alur utama di browser:
- Tambah `Kendaraan` (isi `harga_per_jam` dan upload gambar)
- Tambah `Pelanggan` (isi `no_ktp` dan upload foto SIM)
- Buat `Transaksi` (pilih pelanggan & kendaraan, coba `Simpan & Bayar Sekarang`)
- Periksa menu `Pembayaran` dan `Pengembalian` untuk memastikan prefill dan denda otomatis bekerja

4. Jika menemukan error, periksa log XAMPP (`apache/logs/error.log`) dan pastikan folder uploads dapat ditulis.

---

Jika Anda ingin, saya bisa menjalankan satu putaran QA otomatis lebih lanjut: (A) cari sisa referensi lama di seluruh repo, (B) jalankan lint lengkap, atau (C) buat skrip smoke-test minimal untuk dijalankan lokal. Pilih opsi yang diinginkan.
