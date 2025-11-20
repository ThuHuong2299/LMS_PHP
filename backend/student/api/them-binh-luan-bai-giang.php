<?php

/**
 * File: them-binh-luan-bai-giang.php
 * Mục đích: API thêm bình luận hoặc phản hồi cho bài giảng
 * Method: POST
 * Body: { bai_giang_id, noi_dung, binh_luan_cha_id (optional) }
 * Output: Bình luận vừa tạo
 */

// Tắt hiển thị lỗi PHP ra output để không làm hỏng JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';
require_once __DIR__ . '/../../dieu-khieu/SinhVienController.php';

try {
    // Khởi tạo controller
    $controller = new SinhVienController();

    // Gọi method trong controller
    $controller->themBinhLuanBaiGiang();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'thanh_cong' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
