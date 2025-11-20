<?php

/**
 * File: binh-luan-bai-giang.php
 * Mục đích: API lấy danh sách bình luận bài giảng
 * Method: GET
 * Params: bai_giang_id, page (optional), limit (optional)
 * Output: Danh sách bình luận + phản hồi với phân trang
 */

// Tắt hiển thị lỗi PHP ra output để không làm hỏng JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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
    $controller->layBinhLuanBaiGiang();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
