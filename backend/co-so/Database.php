<?php
/**
 * File: Database.php
 * Mục đích: Singleton class quản lý kết nối database
 */

class Database {
    
    private static $instance = null;
    private $connection = null;
    
    // Thông tin kết nối database
    private $host = 'localhost';
    private $dbname = 'lms_hoc_tap';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    /**
     * Private constructor để ngăn khởi tạo trực tiếp
     */
    private function __construct() {
        $this->khoiTaoKetNoi();
    }
    
    /**
     * Lấy instance duy nhất của Database (Singleton)
     */
    public static function layTheBien() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Lấy kết nối PDO
     */
    public static function layKetNoi() {
        return self::layTheBien()->connection;
    }
    
    /**
     * Khởi tạo kết nối PDO
     */
    private function khoiTaoKetNoi() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            error_log("Lỗi kết nối database: " . $e->getMessage());
            die("Không thể kết nối database. Vui lòng thử lại sau.");
        }
    }
    
    /**
     * Ngăn clone object
     */
    private function __clone() {}
    
    /**
     * Ngăn unserialize
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
