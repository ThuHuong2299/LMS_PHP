<?php
/**
 * File: lay-binh-luan-cau-hoi.php
 * Mục đích: Lấy danh sách bình luận của một câu hỏi
 * Method: GET
 * Params: bai_tap_id, cau_hoi_id
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
    $controller->layBinhLuanCauHoi();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
