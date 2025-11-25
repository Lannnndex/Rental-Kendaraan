<?php
// File: controllers/PelangganController.php (Versi Bersih - FUNGSI DELETE AMAN)

require_once "controllers/BaseController.php";
require_once "core/Validator.php"; 
require_once "core/Sanitizer.php"; 
require_once "core/CSRF.php"; 

class PelangganController extends BaseController {
    
    private $model;
    
    public function __construct($factory) {
        parent::__construct($factory); 
        $this->model = $this->factory->getModel('Pelanggan');
    }

    public function index() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $limit = 10;
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($currentPage - 1) * $limit;
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'no_ktp';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
        
        $totalResults = $this->model->countAll($search);
        $totalPages = ceil($totalResults / $limit);
        $result = $this->model->getAll($search, $limit, $offset, $sortBy, $sortOrder);
        
        include "views/pelanggan/index.php";
    }

    public function create() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = ['nama' => '','alamat' => '','no_hp' => '','no_ktp' => '','foto_sim' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $data['nama'] = Sanitizer::name($_POST['nama']); 
            $data['alamat'] = Sanitizer::text($_POST['alamat']); 
            $data['no_hp'] = Sanitizer::formatPhone($_POST['no_hp']); 
            $data['no_ktp'] = Sanitizer::alphanum($_POST['no_ktp']); 

            // Pass uploaded file into data for centralized validation
            $data['foto_sim_file'] = $_FILES['foto_sim'] ?? null;
            $data['foto_sim'] = null;

            $validator = new Validator($this->factory->getDb()); 
            
            $validator->setFieldNames([
                'nama' => 'Nama Pelanggan',
                'alamat' => 'Alamat',
                'no_hp' => 'No. HP',
                'no_ktp' => 'No. KTP',
                'foto_sim_file' => 'Foto SIM'
            ]);

            $rules = [
                'nama' => 'required',
                'alamat' => 'required',
                'no_hp' => 'required|unique:pelanggan,no_hp',
                'no_ktp' => 'required|numeric|unique:pelanggan,no_ktp',
                // Foto SIM wajib untuk create
                'foto_sim_file' => 'required|image|maxFile:2048'
            ];
            
            if ($validator->validate($data, $rules)) {
                // If foto_sim uploaded and valid, move it now
                if (is_array($data['foto_sim_file']) && ($data['foto_sim_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $ext = pathinfo($data['foto_sim_file']['name'], PATHINFO_EXTENSION);
                    $uploadDir = __DIR__ . '/../public/uploads/pelanggan/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadDir . $filename;
                    if (move_uploaded_file($data['foto_sim_file']['tmp_name'], $target)) {
                        $data['foto_sim'] = 'public/uploads/pelanggan/' . $filename;
                    } else {
                        $errors[] = 'Gagal menyimpan foto SIM.';
                    }
                }

                if (empty($errors)) {
                    $created = $this->safe(function() use ($data) {
                        return $this->model->create($data['no_ktp'], $data['nama'], $data['alamat'], $data['no_hp'], $data['foto_sim']);
                    });
                } else {
                    $created = false;
                }
                if ($created) {
                    header("Location: index.php?page=pelanggan");
                    exit();
                } else {
                    $errors[] = 'Gagal menyimpan pelanggan.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }
        
        include "views/pelanggan/create.php";
    }

    public function edit($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $errors = [];
        $data = $this->model->getById($id);
        if (!$data) {
            echo "Data pelanggan tidak ditemukan.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();

            $data['nama'] = Sanitizer::name($_POST['nama']); 
            $data['alamat'] = Sanitizer::text($_POST['alamat']); 
            $data['no_hp'] = Sanitizer::formatPhone($_POST['no_hp']); 
            $data['no_ktp'] = Sanitizer::alphanum($_POST['no_ktp']); 

            // Preserve old foto_sim path so we can delete it after successful update
            $oldFoto = $data['foto_sim'] ?? null;

            // Pass uploaded file into data for centralized validation
            $foto_sim = $data['foto_sim'] ?? null;
            $data['foto_sim_file'] = $_FILES['foto_sim'] ?? null;

            $validator = new Validator($this->factory->getDb()); 
            
            $validator->setFieldNames([
                'nama' => 'Nama Pelanggan',
                'alamat' => 'Alamat',
                'no_hp' => 'No. HP',
                'no_ktp' => 'No. KTP',
                'foto_sim_file' => 'Foto SIM'
            ]);

            $rules = [
                'nama' => 'required',
                'alamat' => 'required',
                'no_hp' => "required|unique:pelanggan,no_hp,{$id}",
                'no_ktp' => "required|numeric|unique:pelanggan,no_ktp,{$id}",
                // Jika pelanggan belum punya foto_sim, wajib upload saat edit juga
                'foto_sim_file' => (empty($data['foto_sim']) ? 'required|image|maxFile:2048' : 'image|maxFile:2048')
            ];

            if ($validator->validate($data, $rules)) {
                // If foto_sim uploaded and valid, move it now
                if (is_array($data['foto_sim_file']) && ($data['foto_sim_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $ext = pathinfo($data['foto_sim_file']['name'], PATHINFO_EXTENSION);
                    $uploadDir = __DIR__ . '/../public/uploads/pelanggan/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadDir . $filename;
                    if (move_uploaded_file($data['foto_sim_file']['tmp_name'], $target)) {
                        $foto_sim = 'public/uploads/pelanggan/' . $filename;
                    } else {
                        $errors[] = 'Gagal menyimpan foto SIM.';
                    }
                }

                if (empty($errors)) {
                    $updated = $this->safe(function() use ($data, $foto_sim) {
                        return $this->model->update($data['no_ktp'], $data['nama'], $data['alamat'], $data['no_hp'], $foto_sim);
                    });

                    if ($updated && !empty($oldFoto) && !empty($foto_sim) && $oldFoto !== $foto_sim) {
                        $oldPath = __DIR__ . '/../' . $oldFoto;
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                } else {
                    $updated = false;
                }
                if ($updated) {
                    header("Location: index.php?page=pelanggan");
                    exit();
                } else {
                    $errors[] = 'Gagal memperbarui pelanggan.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }
        
        include "views/pelanggan/edit.php";
    }

    public function show($id) {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        $data = $this->model->getById($id);
        if (!$data) {
            echo "Data pelanggan tidak ditemukan.";
            exit;
        }
        include "views/pelanggan/show.php";
    }

    // ======================================================
    // == INI ROMBAKAN BUG KEAMANAN (GET ke POST) ==
    // ======================================================
    public function delete() { // Parameter $id dihapus
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        // Hanya proses jika metodenya POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            // Ambil ID dari form POST, bukan URL
            $id = $_POST['id_to_delete'] ?? null;
            
            if ($id) {
                $this->safe(function() use ($id) {
                    return $this->model->delete($id);
                });
            }
        }
        
        // Selalu redirect kembali ke halaman index
        header("Location: index.php?page=pelanggan");
    }
    // ======================================================

    public function recycleBin() {
        $this->authorize(['admin']);
        $result = $this->model->getAllDeleted();
        include "views/pelanggan/recycle_bin.php";
    }

    public function restore($id) {
        $this->authorize(['admin']);
        $this->model->restore($id);
        header("Location: index.php?page=pelanggan&action=recycleBin");
    }

    public function deletePermanent($id) {
        $this->authorize(['admin']);
        $this->model->deletePermanent($id);
        header("Location: index.php?page=pelanggan&action=recycleBin");
    }

    public function bulkRecycleBin() {
        $this->authorize(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            $action = $_POST['bulk_action'] ?? null;
            $ids = $_POST['ids'] ?? [];
            if (!empty($ids) && $action == 'restore') {
                $this->model->restoreBulk($ids);
            } elseif (!empty($ids) && $action == 'delete_permanent') {
                $this->model->deletePermanentBulk($ids);
            }
        }
        header("Location: index.php?page=pelanggan&action=recycleBin");
    }
}
?>