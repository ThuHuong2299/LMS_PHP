<?php
/**
 * File: danh-sach-chuong.php
 * Mục đích: API lấy danh sách chương + tiến độ cho sinh viên
 * Method: GET
 * Params: lop_hoc_id
 * Output: Danh sách chương với thống kê và tiến độ
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';
require_once __DIR__ . '/../../dieu-khieu/SinhVienController.php';

try {
    // Khởi tạo controller
    $controller = new SinhVienController();
    
    // Gọi method trong controller
    $controller->layDanhSachChuong();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
?>
