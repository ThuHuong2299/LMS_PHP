<?php
// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_NAME', 'lms_hoc_tap');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Biến toàn cục lưu kết nối
$ket_noi = null;

/**
 * Hàm lấy kết nối database
 * @return PDO|null
 */
function lay_ket_noi_db() {
    global $ket_noi;
    
    // Nếu đã có kết nối, trả về luôn
    if ($ket_noi !== null) {
        return $ket_noi;
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];
        
        $ket_noi = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $ket_noi;
        
    } catch (PDOException $e) {
        // Log lỗi (trong production nên ghi vào file log)
        error_log("Lỗi kết nối database: " . $e->getMessage());
        return null;
    }
}

/**
 * Hàm đóng kết nối database
 */
function dong_ket_noi_db() {
    global $ket_noi;
    $ket_noi = null;
}
