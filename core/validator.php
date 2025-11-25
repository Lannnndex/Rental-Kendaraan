<?php
// File: core/Validator.php (Versi FINAL - Bug 'unique' DIPERBAIKI)

class Validator {
    private $db;
    private $errors = [];
    private $field_names = []; 

    public function __construct($db) {
        $this->db = $db;
    }

    public function setFieldNames(array $names) {
        $this->field_names = $names;
    }

    private function getFieldName(string $field) {
        return $this->field_names[$field] ?? $field;
    }
    
    public function getErrors() {
        return $this->errors;
    }

    private function addError(string $field, string $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function validate(array $data, array $rules) {
        $this->errors = []; 

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $rulesList = explode('|', $ruleString);
            $pretty_name = $this->getFieldName($field);

            foreach ($rulesList as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $params) = explode(':', $rule, 2);
                    $params = explode(',', $params);
                }

                switch ($rule) {
                    case 'required':
                        // Support files (arrays) and scalar values
                        if (is_array($value)) {
                            // If it's an uploaded file array, check upload error
                            if (isset($value['error'])) {
                                if ($value['error'] === UPLOAD_ERR_NO_FILE) {
                                    $this->addError($field, "{$pretty_name} wajib diisi.");
                                }
                            } else {
                                if (empty($value)) {
                                    $this->addError($field, "{$pretty_name} wajib diisi.");
                                }
                            }
                        } else {
                            if ($value === null || trim((string)$value) === '') {
                                $this->addError($field, "{$pretty_name} wajib diisi.");
                            }
                        }
                        break;
                    
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $this->addError($field, "{$pretty_name} harus berupa angka.");
                        }
                        break;
                    
                    case 'min':
                        if (strlen($value) < $params[0]) {
                            $this->addError($field, "{$pretty_name} minimal harus {$params[0]} karakter.");
                        }
                        break;

                    case 'date_after':
                        $date_to_compare_field = $params[0];
                        $date_to_compare_value = $data[$date_to_compare_field] ?? null;
                        
                        if ($value && $date_to_compare_value && strtotime($value) <= strtotime($date_to_compare_value)) {
                            $pretty_name_compare = $this->getFieldName($date_to_compare_field);
                            $this->addError($field, "{$pretty_name} harus setelah {$pretty_name_compare}.");
                        }
                        break;

                    case 'unique':
                        $table = $params[0];
                        $column = $params[1];
                        $exceptId = $params[2] ?? null;

                        // Map primary key names for tables that use non-standard PKs
                        $pkMap = [
                            'kendaraan' => 'no_plat',
                            'pelanggan' => 'no_ktp',
                            'users' => 'id_users',
                            'pembayaran' => 'id_pembayaran',
                            'pengembalian' => 'id_pengembalian',
                            'rental' => 'id_rental'
                        ];

                        $pk = $pkMap[$table] ?? ('id_' . rtrim($table, 's'));

                        // Build SQL
                        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
                        if ($table !== 'users') {
                            $sql .= " AND deleted_at IS NULL";
                        }

                        $types = "s";
                        $query_params = [$value];

                        if ($exceptId) {
                            $sql .= " AND {$pk} != ?";
                            $types .= is_numeric($exceptId) ? 'i' : 's';
                            $query_params[] = $exceptId;
                        }

                        $stmt = $this->db->prepare($sql);
                        if (!$stmt) {
                            // If prepare fails, consider it a validation error to avoid fatal exception
                            $this->addError($field, "Terjadi kesalahan validasi pada {$pretty_name}.");
                            break;
                        }
                        $stmt->bind_param($types, ...$query_params);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();

                        if (($result['count'] ?? 0) > 0) {
                            $this->addError($field, "{$pretty_name} ini sudah terdaftar.");
                        }
                        break;
                    
                    case 'dateFormat':
                        $format = $params[0]; 
                        $d = DateTime::createFromFormat($format, $value);
                        if (!$d || $d->format($format) !== $value) {
                            $this->addError($field, "{$pretty_name} harus dalam format {$format}.");
                        }
                        break;

                    case 'image':
                        // Validate uploaded image file array (expecting $_FILES['field'])
                        // If no file uploaded, skip validation (use required rule for mandatory files)
                        if (is_array($value) && isset($value['error']) && $value['error'] === UPLOAD_ERR_OK) {
                            $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = finfo_file($finfo, $value['tmp_name']);
                            finfo_close($finfo);
                            if (!in_array($mime, $allowed)) {
                                $this->addError($field, "{$pretty_name} harus berupa gambar JPG atau PNG.");
                            }
                        }
                        break;

                    case 'maxFile':
                        // Param 0 = max size in KB
                        $maxKb = (int)($params[0] ?? 0);
                        if ($maxKb > 0 && is_array($value) && isset($value['size']) && $value['error'] === UPLOAD_ERR_OK) {
                            if ($value['size'] > ($maxKb * 1024)) {
                                $this->addError($field, "{$pretty_name} tidak boleh melebihi {$maxKb} KB.");
                            }
                        }
                        break;

                    case 'between':
                        $min = $params[0];
                        $max = $params[1];
                        if (!is_numeric($value) || $value < $min || $value > $max) {
                            $this->addError($field, "{$pretty_name} harus di antara {$min} dan {$max}.");
                        }
                        break;
                        
                    case 'in':
                        if (!in_array($value, $params)) {
                            $this->addError($field, "{$pretty_name} tidak valid.");
                        }
                        break;
                }
            }
        }
        return empty($this->errors);
    }
}