<?php
/**
 * File: ket-qua-bai-kiem-tra.php
 * Mục đích: API xem kết quả bài kiểm tra (điểm, đáp án đúng/sai)
 * Method: GET
 * Params: bai_lam_id
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'thanh_cong' => false, 
        'thong_bao' => 'Phương thức không được hỗ trợ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../../dieu-khieu/SinhVienController.php';

try {
    $controller = new SinhVienController();
    $controller->xemKetQuaBaiKiemTra();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
