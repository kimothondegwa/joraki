<?php
// includes/db.php - Database Connection using PDO

// Include configuration
require_once __DIR__ . '/config.php';

// ============================================
// PDO DATABASE CONNECTION
// ============================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // Log error in production, display in development
    if (DEBUG_MODE) {
        die("Database Connection Failed: " . $e->getMessage());
    } else {
        error_log("Database Error: " . $e->getMessage());
        die("Sorry, we're experiencing technical difficulties. Please try again later.");
    }
}

// ============================================
// DATABASE HELPER FUNCTIONS
// ============================================

/**
 * Execute a prepared statement with parameters
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement
 */
function db_query($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("Query Error: " . $e->getMessage() . "<br>SQL: " . $sql);
        } else {
            error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
            return false;
        }
    }
}

/**
 * Fetch all rows from a query
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array
 */
function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Fetch single row from a query
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|false
 */
function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Get single value from query
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return mixed
 */
function db_fetch_value($sql, $params = []) {
    $stmt = db_query($sql, $params);
    return $stmt ? $stmt->fetchColumn() : false;
}

/**
 * Insert data into table
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false Last insert ID or false
 */
function db_insert($table, $data) {
    global $pdo;
    
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    
    $stmt = db_query($sql, $data);
    return $stmt ? $pdo->lastInsertId() : false;
}

/**
 * Update data in table
 * @param string $table Table name
 * @param array $data Data to update
 * @param string $where WHERE clause
 * @param array $whereParams WHERE parameters
 * @return bool
 */
function db_update($table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = :{$key}";
    }
    $setClause = implode(', ', $setParts);
    
    $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    
    $params = array_merge($data, $whereParams);
    $stmt = db_query($sql, $params);
    
    return $stmt !== false;
}

/**
 * Delete from table
 * @param string $table Table name
 * @param string $where WHERE clause
 * @param array $params WHERE parameters
 * @return bool
 */
function db_delete($table, $where, $params = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    $stmt = db_query($sql, $params);
    return $stmt !== false;
}

/**
 * Count rows in table
 * @param string $table Table name
 * @param string $where Optional WHERE clause
 * @param array $params Optional parameters
 * @return int
 */
function db_count($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) FROM {$table}";
    if ($where) {
        $sql .= " WHERE {$where}";
    }
    return (int) db_fetch_value($sql, $params);
}

/**
 * Check if record exists
 * @param string $table Table name
 * @param string $where WHERE clause
 * @param array $params Parameters
 * @return bool
 */
function db_exists($table, $where, $params = []) {
    return db_count($table, $where, $params) > 0;
}

/**
 * Begin transaction
 */
function db_begin_transaction() {
    global $pdo;
    return $pdo->beginTransaction();
}

/**
 * Commit transaction
 */
function db_commit() {
    global $pdo;
    return $pdo->commit();
}

/**
 * Rollback transaction
 */
function db_rollback() {
    global $pdo;
    return $pdo->rollBack();
}

/**
 * Escape string for LIKE query
 * @param string $string String to escape
 * @return string
 */
function db_escape_like($string) {
    return str_replace(['%', '_'], ['\\%', '\\_'], $string);
}

/**
 * Get last insert ID
 * @return string
 */
function db_last_insert_id() {
    global $pdo;
    return $pdo->lastInsertId();
}

// ============================================
// DATABASE HEALTH CHECK (Optional)
// ============================================
/**
 * Check database connection health
 * @return bool
 */
function db_health_check() {
    try {
        global $pdo;
        $pdo->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
 