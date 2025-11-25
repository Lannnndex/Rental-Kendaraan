<?php
// Simple PDO adapter to provide a mysqli-like API for this project.
// It provides: prepare(), query(), error (property), lastInsertId(), and returns wrapper objects

class PDOResultWrapper {
    private $rows = [];
    private $pos = 0;
    public $num_rows = 0;

    public function __construct(array $rows) {
        $this->rows = $rows;
        $this->num_rows = count($rows);
        $this->pos = 0;
    }

    public function fetch_assoc() {
        if ($this->pos >= $this->num_rows) return null;
        $row = $this->rows[$this->pos];
        $this->pos++;
        return $row;
    }

    public function data_seek($offset) {
        $offset = (int)$offset;
        if ($offset < 0) $offset = 0;
        if ($offset > $this->num_rows) $offset = $this->num_rows;
        $this->pos = $offset;
    }

    public function fetch_all() {
        return $this->rows;
    }
}

class PDOStatementWrapper {
    private $stmt;
    private $bound = [];
    private $types = '';
    private $executed = false;

    public function __construct(PDOStatement $stmt) {
        $this->stmt = $stmt;
    }

    // Emulate mysqli_stmt::bind_param($types, &...$vars)
    public function bind_param($types /*, &...$vars */) {
        $args = array_slice(func_get_args(), 1);
        $this->types = $types;
        $this->bound = $args;
        return true;
    }

    public function execute() {
        try {
            // Convert bound params to values, respecting types string if present
            $params = [];
            if (!empty($this->bound)) {
                // types string may be like 'sisd' - map accordingly; if shorter, default to string
                $typeChars = str_split($this->types);
                foreach ($this->bound as $i => $v) {
                    $type = $typeChars[$i] ?? 's';
                    switch ($type) {
                        case 'i': $params[] = (int)$v; break;
                        case 'd': $params[] = (float)$v; break;
                        default: $params[] = (string)$v; break;
                    }
                }
            }
            $this->executed = $this->stmt->execute($params);
            return $this->executed;
        } catch (Exception $e) {
            return false;
        }
    }

    // Return a result wrapper similar to mysqli_stmt::get_result()
    public function get_result() {
        if (!$this->executed) {
            $this->execute();
        }
        $rows = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return new PDOResultWrapper($rows);
    }

    // Allow fetch single row directly
    public function fetch_assoc() {
        if (!$this->executed) $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}

class PDOAdapter {
    public $error = '';
    private $pdo;

    public function __construct($host, $user, $pass, $db, $charset = 'utf8mb4') {
        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Koneksi PDO gagal: ' . $this->error);
        }
    }

    public function prepare($sql) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return new PDOStatementWrapper($stmt);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function query($sql) {
        try {
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return new PDOResultWrapper($rows);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction() { return $this->pdo->beginTransaction(); }
    public function commit() { return $this->pdo->commit(); }
    public function rollBack() { return $this->pdo->rollBack(); }

    // expose raw PDO if needed
    public function getPdo() { return $this->pdo; }
}

?>