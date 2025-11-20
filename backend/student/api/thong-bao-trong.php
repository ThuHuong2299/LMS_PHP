<?php
/**
 * File: thong-bao-trong.php
 * Mục đích: API lấy danh sách thông báo trong (hoạt động) của sinh viên
 * Method: GET
 * 
 * Endpoint: /backend/student/api/thong-bao-trong.php
 * Response: {thanh_cong, du_lieu: {thong_bao_list}, thong_bao}
 */

// Khởi động session
session_start();

// Cấu hình CORS
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Phương thức không được hỗ trợ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load các class cần thiết
require_once __DIR__ . '/../../co-so/Database.php';
require_once __DIR__ . '/../../dieu-khieu/SinhVienController.php';

try {
    // Khởi tạo controller và gọi method
    $controller = new SinhVienController();
    $controller->layThongBaoTrong();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
