<?php
// File: controllers/TransaksiController.php (Versi Bersih - FUNGSI DELETE AMAN)

require_once "controllers/BaseController.php";
require_once "core/Validator.php"; 
require_once "core/Sanitizer.php"; 
require_once "core/CSRF.php"; 

class TransaksiController extends BaseController {
    
    private $transaksiModel;
    private $pelangganModel;
    private $kendaraanModel;
    
    public function __construct($factory) {
        parent::__construct($factory); 
        
        $this->transaksiModel = $this->factory->getModel('TransaksiSewa');
        $this->pelangganModel = $this->factory->getModel('Pelanggan');
        $this->kendaraanModel = $this->factory->getModel('Kendaraan');
    }

    // Generate a short sequential rental id (e.g. A001, A002)
    private function generateIdRental() {
        // Delegate to model which reads latest 'A' prefixed id and increments
        try {
            return $this->transaksiModel->getNextIdRental('A', 3);
        } catch (Exception $e) {
            // Fallback to timestamp-based id if anything goes wrong
            return 'R' . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 3));
        }
    }

    public function index() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $limit = 10;
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($currentPage - 1) * $limit;
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id_rental';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

        $totalResults = $this->transaksiModel->countAll($search);
        $totalPages = ceil($totalResults / $limit);
        $result = $this->transaksiModel->getAll($search, $limit, $offset, $sortBy, $sortOrder);
        
        include "views/transaksi/index.php";
    }

    public function create() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = [
            'no_ktp' => '', 'no_plat' => '', 'tanggal_sewa' => '',
            'tanggal_kembali' => '', 'total_biaya' => 0.00
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();

            $data = [
                'no_ktp' => Sanitizer::alphanum($_POST['no_ktp'] ?? ''),
                'no_plat' => Sanitizer::alphanum($_POST['no_plat'] ?? ''),
                'tanggal_sewa' => Sanitizer::text($_POST['tanggal_sewa'] ?? ''),
                'tanggal_kembali' => Sanitizer::text($_POST['tanggal_kembali'] ?? ''),
                'total_biaya' => 0.00
            ];

            $bayar_now = isset($_POST['bayar_now']) && $_POST['bayar_now'] === '1';

            // Raw payment inputs (may be null)
            $jumlah_bayar_raw = $_POST['jumlah_bayar'] ?? null;
            $tanggal_bayar_raw = $_POST['tgl_bayar'] ?? null;
            $metode_bayar_raw = $_POST['metode_bayar'] ?? null;

            // Sertakan field pembayaran mentah ke data agar validator dapat memeriksa ketika bayar_now
            $data['jumlah_bayar'] = $jumlah_bayar_raw;
            $data['tgl_bayar'] = $tanggal_bayar_raw;
            $data['metode_bayar'] = $metode_bayar_raw;

            $validator = new Validator($this->factory->getDb()); 

            $validator->setFieldNames([
                'no_ktp' => 'Pelanggan',
                'no_plat' => 'Kendaraan',
                'tanggal_sewa' => 'Tanggal Sewa',
                'tanggal_kembali' => 'Tanggal Kembali',
                'jumlah_bayar' => 'Jumlah Bayar',
                'tgl_bayar' => 'Tanggal Bayar',
                'metode_bayar' => 'Metode Bayar'
            ]);

            $rules = [
                'no_ktp' => 'required',
                'no_plat' => 'required',
                'tanggal_sewa' => 'required',
                'tanggal_kembali' => 'required|date_after:tanggal_sewa'
            ];

            // Jika bayar_now dan user sudah mengisi jumlah_bayar, maka validasi pembayaran wajib
            if ($bayar_now && !empty($jumlah_bayar_raw)) {
                $rules['jumlah_bayar'] = 'required|numeric|between:0,999999999';
                $rules['tgl_bayar'] = 'required';
                $rules['metode_bayar'] = 'required|in:tunai,kartu,transfer';
            }

            if ($validator->validate($data, $rules)) {
                // Calculate total biaya based on harga_per_jam and duration in hours
                $vehicle = $this->kendaraanModel->getById($data['no_plat']);
                if (!$vehicle) {
                    $errors[] = 'Kendaraan tidak ditemukan.';
                } else {
                    try {
                        $dtStart = new DateTime($data['tanggal_sewa']);
                        $dtEnd = new DateTime($data['tanggal_kembali']);
                        $seconds = $dtEnd->getTimestamp() - $dtStart->getTimestamp();
                        if ($seconds <= 0) {
                            $errors[] = 'Tanggal kembali harus setelah tanggal sewa.';
                        } else {
                            $hours = ceil($seconds / 3600);
                            $harga = (float)($vehicle['harga_per_jam'] ?? 0);
                            $total = $hours * $harga;

                            $id_rental = $this->generateIdRental();
                            $id_users = $_SESSION['user_id'] ?? null;

                            // Prepare payment values (may be null)
                            $jumlah_bayar = !empty($jumlah_bayar_raw) ? Sanitizer::numeric($jumlah_bayar_raw) : null;
                            $tanggal_bayar = !empty($tanggal_bayar_raw) ? Sanitizer::text($tanggal_bayar_raw) : null;
                            $metode_bayar = !empty($metode_bayar_raw) ? strtolower(trim(strip_tags($metode_bayar_raw))) : null;

                            // Create rental and include payment fields if provided
                            $created = $this->safe(function() use ($id_rental, $data, $id_users, $total, $jumlah_bayar, $tanggal_bayar, $metode_bayar) {
                                return $this->transaksiModel->create($id_rental, $data['no_plat'], $id_users, $data['no_ktp'], $data['tanggal_sewa'], $data['tanggal_kembali'], $total, $jumlah_bayar, $tanggal_bayar, $metode_bayar);
                            });
                            if ($created) {
                                // update kendaraan status
                                $this->kendaraanModel->updateStatus($data['no_plat'], 'disewa');

                                // If user chose to pay now
                                if ($bayar_now) {
                                    // If payment details were provided in the form, show receipt; otherwise redirect to payment form
                                    if ($jumlah_bayar !== null) {
                                        header("Location: index.php?page=transaksi&action=receipt&id=" . urlencode($id_rental));
                                        exit();
                                    } else {
                                        header("Location: index.php?page=transaksi&action=payment&id=" . urlencode($id_rental));
                                        exit();
                                    }
                                }

                                // Default: redirect back to transaksi list
                                header("Location: index.php?page=transaksi");
                                exit();
                            } else {
                                // Debug logging: write POST and DB error to file to diagnose why payment fields not saved
                                $logDir = __DIR__ . '/.. /logs';
                                $logDir = realpath(__DIR__ . '/../logs') ?: (__DIR__ . '/../logs');
                                if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
                                $logFile = $logDir . '/transaksi_create_debug.log';
                                $payload = [
                                    'time' => date('c'),
                                    'post' => $_POST,
                                    'created' => $created,
                                    'db_error' => ''
                                ];
                                $db = $this->factory->getDb();
                                if ($db) {
                                    $payload['db_error'] = $db->error ?? '';
                                }
                                @file_put_contents($logFile, print_r($payload, true) . "\n---\n", FILE_APPEND);
                                $errors[] = 'Gagal membuat transaksi.';
                            }
                        }
                    } catch (Exception $e) {
                        $errors[] = 'Format tanggal tidak valid.';
                    }
                }
            } else {
                $errors = $validator->getErrors();
            }
        }

        $pelanggan = $this->pelangganModel->getAll('', 999);
        $kendaraan = $this->kendaraanModel->getAllAvailable();
        
        include "views/transaksi/create.php";
    }

    // Show receipt / payment confirmation for a rental
    public function receipt($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        $data = $this->transaksiModel->getById($id);
        if (!$data) {
            $_SESSION['flash_error'] = 'Data transaksi tidak ditemukan.';
            header('Location: index.php?page=transaksi');
            exit();
        }
        include 'views/transaksi/receipt.php';
    }

    // Show full transaksi details (read-only)
    public function show($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        $data = $this->transaksiModel->getById($id);
        if (!$data) {
            $_SESSION['flash_error'] = 'Data transaksi tidak ditemukan.';
            header('Location: index.php?page=transaksi');
            exit();
        }
        include 'views/transaksi/show.php';
    }

    // Show and process payment page for an existing rental
    public function payment($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        $data = $this->transaksiModel->getById($id);
        if (!$data) {
            $_SESSION['flash_error'] = 'Data transaksi tidak ditemukan.';
            header('Location: index.php?page=transaksi');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            $jumlah_bayar = Sanitizer::numeric($_POST['jumlah_bayar'] ?? null);
            $tanggal_bayar = Sanitizer::text($_POST['tgl_bayar'] ?? null);
            $metode_bayar = strtolower(trim(strip_tags($_POST['metode_bayar'] ?? '')));

            $validator = new Validator($this->factory->getDb());
            $validator->setFieldNames([
                'jumlah_bayar' => 'Jumlah Bayar',
                'tgl_bayar' => 'Tanggal Bayar',
                'metode_bayar' => 'Metode Bayar'
            ]);
            $rules = [
                'jumlah_bayar' => 'required|numeric|between:0,999999999',
                'tgl_bayar' => 'required',
                'metode_bayar' => 'required|in:tunai,kartu,transfer'
            ];

            // Wrap payment values into $checkData for validator
            $checkData = ['jumlah_bayar' => $jumlah_bayar, 'tgl_bayar' => $tanggal_bayar, 'metode_bayar' => $metode_bayar];
            if ($validator->validate($checkData, $rules)) {
                $this->transaksiModel->updatePayment($id, $jumlah_bayar, $tanggal_bayar, $metode_bayar);
                header('Location: index.php?page=transaksi&action=receipt&id=' . urlencode($id));
                exit();
            } else {
                $errors = $validator->getErrors();
            }
        }

        include 'views/transaksi/payment.php';
    }

    public function edit($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = $this->transaksiModel->getById($id);
        if (!$data) {
            echo "Data transaksi tidak ditemukan.";
            exit;
        }

        // Ambil nama karyawan yang menangani (jika tersedia)
        $userModel = $this->factory->getModel('User');
        if (!empty($data['id_users'])) {
            $u = $userModel->getById($data['id_users']);
            $data['nama_karyawan'] = $u['nama_lengkap'] ?? null;
        } else {
            $data['nama_karyawan'] = null;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $data['no_ktp'] = Sanitizer::alphanum($_POST['no_ktp']);
            $data['no_plat'] = Sanitizer::alphanum($_POST['no_plat']);
            $data['tanggal_sewa'] = Sanitizer::text($_POST['tanggal_sewa']);
            $data['tanggal_kembali'] = Sanitizer::text($_POST['tanggal_kembali']);
            $data['total_biaya'] = 0.00; // will be recalculated
            // payment fields (optional)
            $jumlah_bayar_raw = $_POST['jumlah_bayar'] ?? null;
            $tanggal_bayar_raw = $_POST['tgl_bayar'] ?? null;
            $metode_bayar_raw = $_POST['metode_bayar'] ?? null;
            // Sertakan field pembayaran mentah ke data agar validator dapat memeriksa ketika ada input
            $data['jumlah_bayar'] = $jumlah_bayar_raw;
            $data['tgl_bayar'] = $tanggal_bayar_raw;
            $data['metode_bayar'] = $metode_bayar_raw;
            
            $validator = new Validator($this->factory->getDb()); 
            
            $validator->setFieldNames([
                'no_ktp' => 'Pelanggan',
                'no_plat' => 'Kendaraan',
                'tanggal_sewa' => 'Tanggal Sewa',
                'tanggal_kembali' => 'Tanggal Kembali'
            ]);

            $rules = [
                'no_ktp' => 'required',
                'no_plat' => 'required',
                'tanggal_sewa' => 'required',
                'tanggal_kembali' => 'required|date_after:tanggal_sewa'
            ];

            // optional payment validation when fields provided
            if (!empty($jumlah_bayar_raw) || !empty($tanggal_bayar_raw) || !empty($metode_bayar_raw)) {
                $rules['jumlah_bayar'] = 'numeric|between:0,999999999';
                $rules['tgl_bayar'] = 'date';
                $rules['metode_bayar'] = 'in:tunai,kartu,transfer';
            }

            if ($validator->validate($data, $rules)) {
                // Recalculate total
                $vehicle = $this->kendaraanModel->getById($data['no_plat']);
                if (!$vehicle) {
                    $errors[] = 'Kendaraan tidak ditemukan.';
                } else {
                    try {
                        $dtStart = new DateTime($data['tanggal_sewa']);
                        $dtEnd = new DateTime($data['tanggal_kembali']);
                        $seconds = $dtEnd->getTimestamp() - $dtStart->getTimestamp();
                        if ($seconds <= 0) {
                            $errors[] = 'Tanggal kembali harus setelah tanggal sewa.';
                        } else {
                            $hours = ceil($seconds / 3600);
                            $harga = (float)($vehicle['harga_per_jam'] ?? 0);
                            $total = $hours * $harga;

                            $id_users = $_SESSION['user_id'] ?? null;
                            $updated = $this->safe(function() use ($id, $data, $id_users) {
                                return $this->transaksiModel->update($id, $data['no_ktp'], $data['no_plat'], $id_users, $data['tanggal_sewa'], $data['tanggal_kembali'], $total);
                            });
                            if ($updated) {
                                // If payment fields provided, update payment columns
                                if (!empty($jumlah_bayar_raw) || !empty($tanggal_bayar_raw) || !empty($metode_bayar_raw)) {
                                    $jumlah_bayar = !empty($jumlah_bayar_raw) ? Sanitizer::numeric($jumlah_bayar_raw) : null;
                                    $tanggal_bayar = !empty($tanggal_bayar_raw) ? Sanitizer::text($tanggal_bayar_raw) : null;
                                    $metode_bayar = !empty($metode_bayar_raw) ? strtolower(trim(strip_tags($metode_bayar_raw))) : null;
                                    $this->transaksiModel->updatePayment($id, $jumlah_bayar, $tanggal_bayar, $metode_bayar);
                                }

                                header("Location: index.php?page=transaksi");
                                exit();
                            } else {
                                $errors[] = 'Gagal memperbarui transaksi.';
                            }
                        }
                    } catch (Exception $e) {
                        $errors[] = 'Format tanggal tidak valid.';
                    }
                }
            } else {
                $errors = $validator->getErrors();
            }
        }
        $pelanggan = $this->pelangganModel->getAll('', 999);
        $kendaraan = $this->kendaraanModel->getAll('', 999);
        include "views/transaksi/edit.php";
    }

    // ======================================================
    // == INI ROMBAKAN BUG KEAMANAN (GET ke POST) ==
    // ======================================================
    public function delete() { // Parameter $id dihapus
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $id = $_POST['id_to_delete'] ?? null;
            
            if ($id) {
                // Logika Bisnis: Kembalikan status mobil ke 'tersedia'
                $transaksi = $this->transaksiModel->getById($id);
                if ($transaksi) {
                    $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'tersedia');
                }
                
                $this->transaksiModel->delete($id);
            }
        }
        
        header("Location: index.php?page=transaksi");
    }
    // ======================================================

    
    public function recycleBin() {
        $this->authorize(['admin']);
        $result = $this->transaksiModel->getAllDeleted();
        include "views/transaksi/recycle_bin.php";
    }

    public function restore($id) {
        $this->authorize(['admin']);
        
        // Logika Bisnis: Kembalikan status mobil ke 'disewa'
        $transaksi = $this->transaksiModel->getById($id, true);
        if ($transaksi) {
            $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'disewa');
        }

        $this->transaksiModel->restore($id);
        
        header("Location: index.php?page=transaksi&action=recycleBin");
    }

    public function deletePermanent($id) {
        $this->authorize(['admin']);
        $this->transaksiModel->deletePermanent($id);
        header("Location: index.php?page=transaksi&action=recycleBin");
    }

    public function bulkRecycleBin() {
        $this->authorize(['admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            $action = $_POST['bulk_action'] ?? null;
            $ids = $_POST['ids'] ?? [];
            
            if (!empty($ids) && $action == 'restore') {
                foreach ($ids as $id) {
                    $transaksi = $this->transaksiModel->getById($id, true);
                    if ($transaksi) {
                        $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'disewa');
                    }
                }
                $this->transaksiModel->restoreBulk($ids); 
                
            } elseif (!empty($ids) && $action == 'delete_permanent') {
                $this->transaksiModel->deletePermanentBulk($ids);
            }
        }
        header("Location: index.php?page=transaksi&action=recycleBin");
    }
}
?>