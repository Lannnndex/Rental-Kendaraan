<?php
// File: controllers/PengembalianController.php (Versi Bersih - FUNGSI DELETE AMAN)

require_once "controllers/BaseController.php";

class PengembalianController extends BaseController {
    
    private $pengembalianModel;
    private $transaksiModel;
    private $kendaraanModel;

    public function __construct($factory) {
        parent::__construct($factory); 
        
        $this->pengembalianModel = $this->factory->getModel('Pengembalian');
        $this->transaksiModel = $this->factory->getModel('TransaksiSewa');
        $this->kendaraanModel = $this->factory->getModel('Kendaraan');
    }

    public function index() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $limit = 10;
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($currentPage - 1) * $limit;
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id_pengembalian';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
        
        $totalResults = $this->pengembalianModel->countAll($search);
        $totalPages = ceil($totalResults / $limit);
        $result = $this->pengembalianModel->getAll($search, $limit, $offset, $sortBy, $sortOrder);
        
        include "views/pengembalian/index.php";
    }

    public function create() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = ['id_rental' => '', 'tanggal_dikembalikan' => '', 'denda' => 0];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $denda_input = isset($_POST['denda']) ? Sanitizer::numeric($_POST['denda']) : null;

            $data = [
                'id_rental' => Sanitizer::text($_POST['id_rental'] ?? ''),
                'tanggal_dikembalikan' => Sanitizer::text($_POST['tanggal_dikembalikan'] ?? ''),
                'denda' => $denda_input !== null ? $denda_input : null
            ];

            $validator = new Validator($this->factory->getDb()); 
            
            $validator->setFieldNames([
                'id_rental' => 'ID Rental',
                'tanggal_dikembalikan' => 'Tanggal Dikembalikan',
                'denda' => 'Denda'
            ]);

            $rules = [
                'id_rental' => 'required',
                'tanggal_dikembalikan' => 'required',
                'denda' => 'nullable|numeric|between:0,999999999'
            ];

            if ($validator->validate($data, $rules)) {
                // If denda not provided, compute based on lateness and harga_per_jam
                $computedDenda = $data['denda'];
                $rental = $this->transaksiModel->getById($data['id_rental']);
                if (!$rental) {
                    $errors[] = 'Rental tidak ditemukan.';
                } else {
                    if ($computedDenda === null) {
                        try {
                            $planned = new DateTime($rental['tanggal_kembali']);
                            $actual = new DateTime($data['tanggal_dikembalikan']);
                            $secondsLate = $actual->getTimestamp() - $planned->getTimestamp();
                            if ($secondsLate > 0) {
                                $hoursLate = ceil($secondsLate / 3600);
                                $vehicle = $this->kendaraanModel->getById($rental['no_plat']);
                                $harga = (float)($vehicle['harga_per_jam'] ?? 0);
                                // Default fine: 1 * harga_per_jam per hour late
                                $computedDenda = $hoursLate * $harga;
                            } else {
                                $computedDenda = 0;
                            }
                        } catch (Exception $e) {
                            $errors[] = 'Format tanggal tidak valid.';
                        }
                    }

                    if (empty($errors)) {
                        $created = $this->safe(function() use ($data, $computedDenda) {
                            return $this->pengembalianModel->create($data['id_rental'], $data['tanggal_dikembalikan'], $computedDenda);
                        });
                        if ($created) {
                            // Update kendaraan status to tersedia
                            $this->kendaraanModel->updateStatus($rental['no_plat'], 'tersedia');
                            header("Location: index.php?page=pengembalian");
                            exit();
                        } else {
                            $errors[] = 'Gagal menyimpan data pengembalian.';
                        }
                    }
                }
            } else {
                $errors = $validator->getErrors();
            }
        }

        $transaksi = $this->transaksiModel->getAll('', 999);
        include "views/pengembalian/create.php";
    }

    public function edit($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = $this->pengembalianModel->getById($id);
        if (!$data) {
            echo "Data pengembalian tidak ditemukan.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();

            $denda_input = Sanitizer::numeric($_POST['denda']);

            $data['id_rental'] = Sanitizer::text($_POST['id_rental']);
            $data['tanggal_dikembalikan'] = Sanitizer::text($_POST['tanggal_dikembalikan']);
            $data['denda'] = empty($denda_input) ? 0 : $denda_input;
            
            $validator = new Validator($this->factory->getDb()); 

            $validator->setFieldNames([
                'id_rental' => 'ID Rental',
                'tanggal_dikembalikan' => 'Tanggal Dikembalikan',
                'denda' => 'Denda'
            ]);

            $rules = [
                'id_rental' => 'required',
                'tanggal_dikembalikan' => 'required|dateFormat:Y-m-d',
                'denda' => 'numeric|between:0,999999999'
            ];

            if ($validator->validate($data, $rules)) {
                $updated = $this->safe(function() use ($id, $data) {
                    return $this->pengembalianModel->update($id, $data['id_rental'], $data['tanggal_dikembalikan'], $data['denda']);
                });
                if ($updated) {
                    header("Location: index.php?page=pengembalian");
                    exit();
                } else {
                    $errors[] = 'Gagal memperbarui data pengembalian.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }

        $transaksi = $this->transaksiModel->getAll('', 999);
        include "views/pengembalian/edit.php";
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
                // Logika Bisnis: Set status mobil kembali ke 'disewa'
                $pengembalian = $this->pengembalianModel->getById($id);
                if ($pengembalian) {
                    $transaksi = $this->transaksiModel->getById($pengembalian['id_rental']);
                    if ($transaksi) {
                        $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'disewa');
                    }
                }
                $this->safe(function() use ($id) {
                    return $this->pengembalianModel->delete($id);
                });
            }
        }
        
        header("Location: index.php?page=pengembalian");
    }
    // ======================================================
    
    public function recycleBin() {
        $this->authorize(['admin']);
        $result = $this->pengembalianModel->getAllDeleted();
        include "views/pengembalian/recycle_bin.php";
    }

    public function restore($id) {
        $this->authorize(['admin']);
        
        $pengembalian = $this->pengembalianModel->getById($id, true);
        if ($pengembalian) {
            $transaksi = $this->transaksiModel->getById($pengembalian['id_rental'], true);
            if ($transaksi) {
                $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'tersedia');
            }
        }
        
        $this->safe(function() use ($id) {
            return $this->pengembalianModel->restore($id);
        });
        header("Location: index.php?page=pengembalian&action=recycleBin");
    }

    public function deletePermanent($id) {
        $this->authorize(['admin']);
        $this->safe(function() use ($id) {
            return $this->pengembalianModel->deletePermanent($id);
        });
        header("Location: index.php?page=pengembalian&action=recycleBin");
    }

    public function bulkRecycleBin() {
        $this->authorize(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            $action = $_POST['bulk_action'] ?? null;
            $ids = $_POST['ids'] ?? [];
            
                if (!empty($ids) && $action == 'restore') {
                foreach ($ids as $id) {
                    $pengembalian = $this->pengembalianModel->getById($id, true);
                    if ($pengembalian) {
                        $transaksi = $this->transaksiModel->getById($pengembalian['id_rental'], true);
                        if ($transaksi) {
                            $this->kendaraanModel->updateStatus($transaksi['no_plat'], 'tersedia');
                        }
                    }
                }
                $this->safe(function() use ($ids) { return $this->pengembalianModel->restoreBulk($ids); }); 
                
            } elseif (!empty($ids) && $action == 'delete_permanent') {
                $this->safe(function() use ($ids) { return $this->pengembalianModel->deletePermanentBulk($ids); });
            }
        }
        header("Location: index.php?page=pengembalian&action=recycleBin");
    }
}
?>