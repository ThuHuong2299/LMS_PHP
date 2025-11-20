<?php
/**
 * File: bat-dau-bai-kiem-tra.php
 * Mục đích: API bắt đầu làm bài kiểm tra
 * Method: POST
 * Body JSON: { "bai_kiem_tra_id": 1 }
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    $controller->batDauBaiKiemTra();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
