<?php
// File: controllers/KendaraanController.php (Versi FINAL DIPERBAIKI - Siap GitHub)

require_once "controllers/BaseController.php";

class KendaraanController extends BaseController {
    
    private $model;

    public function __construct($factory) {
        parent::__construct($factory); 
        $this->model = $this->factory->getModel('Kendaraan');
    }

    public function index() {
        $this->authorize(['admin', 'manajer', 'karyawan']);
        
        $limit = 9;
        $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($currentPage - 1) * $limit;
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'no_plat';
        $sortOrder = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
        
        $totalResults = $this->model->countAll($search);
        $totalPages = ceil($totalResults / $limit);
        $result = $this->model->getAll($search, $limit, $offset, $sortBy, $sortOrder);
        
        include "views/kendaraan/index.php";
    }

    public function create() {
        $this->authorize(['admin', 'manajer']);
        
        $errors = [];
        $data = ['jenis' => '', 'merk' => '', 'no_plat' => '', 'harga_per_jam' => 0.00, 'status' => 'tersedia', 'image' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail(); 
            
            $data['jenis'] = Sanitizer::text($_POST['jenis']);
            $data['merk'] = Sanitizer::text($_POST['merk']);
            $data['no_plat'] = Sanitizer::alphanum($_POST['no_plat']);
            $data['harga_per_jam'] = Sanitizer::numeric($_POST['harga_per_jam'] ?? 0);
            // status not required on create; default to 'tersedia'
            $data['status'] = 'tersedia';

            // Pass uploaded file into data for centralized validation
            $data['image_file'] = $_FILES['image'] ?? null;
            $data['image'] = null; // will be set after validation+move

            $validator = new Validator($this->factory->getDb());
            
            $validator->setFieldNames([
                'jenis' => 'Jenis Kendaraan', 
                'merk' => 'Merk',
                'no_plat' => 'No. Plat',
                'harga_per_jam' => 'Harga per Jam'
            ]);

            $rules = [
                'jenis' => 'required',
                'merk' => 'required',
                'no_plat' => 'required|unique:kendaraan,no_plat',
                'harga_per_jam' => 'required|numeric',
                'image_file' => 'image|maxFile:2048'
            ];

            if ($validator->validate($data, $rules)) {
                // If image_file uploaded and valid, move it now
                if (is_array($data['image_file']) && ($data['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $ext = pathinfo($data['image_file']['name'], PATHINFO_EXTENSION);
                    $uploadDir = __DIR__ . '/../public/uploads/kendaraan/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadDir . $filename;
                    if (move_uploaded_file($data['image_file']['tmp_name'], $target)) {
                        $data['image'] = 'public/uploads/kendaraan/' . $filename;
                    } else {
                        $errors[] = 'Gagal menyimpan gambar kendaraan.';
                    }
                }

                if (empty($errors)) {
                    $created = $this->safe(function() use ($data) {
                        return $this->model->create($data['no_plat'], $data['jenis'], $data['merk'], $data['harga_per_jam'], $data['image'], $data['status']);
                    });
                } else {
                    $created = false;
                }
                if ($created) {
                    header("Location: index.php?page=kendaraan");
                    exit();
                } else {
                    $errors[] = 'Gagal menyimpan kendaraan.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }
        
        include "views/kendaraan/create.php";
    }

    public function edit($id) {
        $this->authorize(['admin', 'manajer']);
        
        $errors = [];
        $data = $this->model->getById($id);
        if (!$data) {
            echo "Data kendaraan tidak ditemukan.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $data['jenis'] = Sanitizer::text($_POST['jenis']);
            $data['merk'] = Sanitizer::text($_POST['merk']);
            $data['no_plat'] = Sanitizer::alphanum($_POST['no_plat']);
            $data['harga_per_jam'] = Sanitizer::numeric($_POST['harga_per_jam'] ?? 0);
            $data['status'] = $_POST['status'] ?? $data['status'];

            // Preserve old image path so we can delete it after successful update
            $oldImage = $data['image'] ?? null;

            // Pass uploaded file into data for centralized validation
            $data['image_file'] = $_FILES['image'] ?? null;
            $image = $data['image'] ?? null;

            $validator = new Validator($this->factory->getDb());

            $validator->setFieldNames([
                'jenis' => 'Jenis Kendaraan',
                'merk' => 'Merk',
                'no_plat' => 'No. Plat',
                'status' => 'Status'
            ]);

            $rules = [
                'jenis' => 'required',
                'merk' => 'required',
                'no_plat' => "required|unique:kendaraan,no_plat,{$id}", 
                'harga_per_jam' => 'required|numeric',
                'image_file' => 'image|maxFile:2048'
            ];

            if ($validator->validate($data, $rules)) {
                // If image_file uploaded and valid, move it now
                if (is_array($data['image_file']) && ($data['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $ext = pathinfo($data['image_file']['name'], PATHINFO_EXTENSION);
                    $uploadDir = __DIR__ . '/../public/uploads/kendaraan/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $target = $uploadDir . $filename;
                    if (move_uploaded_file($data['image_file']['tmp_name'], $target)) {
                        $image = 'public/uploads/kendaraan/' . $filename;
                    } else {
                        $errors[] = 'Gagal menyimpan gambar kendaraan.';
                    }
                }

                if (empty($errors)) {
                    $updated = $this->safe(function() use ($data, $image, $id) {
                        // Pass original PK ($id) so model can update PK if it changed
                        return $this->model->update($data['no_plat'], $data['jenis'], $data['merk'], $data['harga_per_jam'], $image, $data['status'], $id);
                    });

                    // If updated and there was a previous image different from the new one, remove old file
                    if ($updated && !empty($oldImage) && !empty($image) && $oldImage !== $image) {
                        $oldPath = __DIR__ . '/../' . $oldImage;
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                } else {
                    $updated = false;
                }
                if ($updated) {
                    header("Location: index.php?page=kendaraan");
                    exit();
                } else {
                    $errors[] = 'Gagal memperbarui kendaraan.';
                }
            } else {
                $errors = $validator->getErrors();
            }
        }
        
        include "views/kendaraan/edit.php";
    }

    public function delete() { 
        $this->authorize(['admin', 'manajer']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            
            $id = $_POST['id_to_delete'] ?? null;
            
            if ($id) {
                $this->safe(function() use ($id) { return $this->model->delete($id); });
            }
        }
        
        header("Location: index.php?page=kendaraan");
    }

    // --- Recycle Bin (Hanya Admin) ---
    
    public function recycleBin() {
        $this->authorize(['admin']);
        $result = $this->model->getAllDeleted(); 
        include "views/kendaraan/recycle_bin.php";
    }
    
    public function restore($id) {
        $this->authorize(['admin']);
        $this->safe(function() use ($id) { return $this->model->restore($id); });
        header("Location: index.php?page=kendaraan&action=recycleBin");
    }

    public function deletePermanent($id) {
        $this->authorize(['admin']);
        $this->safe(function() use ($id) { return $this->model->deletePermanent($id); });
        header("Location: index.php?page=kendaraan&action=recycleBin");
    }

    public function bulkRecycleBin() {
        $this->authorize(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::verifyOrFail();
            $action = $_POST['bulk_action'] ?? null;
            $ids = $_POST['ids'] ?? [];
            
                if (!empty($ids) && $action == 'restore') {
                $this->safe(function() use ($ids) { return $this->model->restoreBulk($ids); });
            } elseif (!empty($ids) && $action == 'delete_permanent') {
                $this->safe(function() use ($ids) { return $this->model->deletePermanentBulk($ids); });
            }
        }
        header("Location: index.php?page=kendaraan&action=recycleBin");
    }
}
?>