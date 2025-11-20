<?php

/**
 * File: diem-theo-chuong.php
 * Mục đích: API lấy điểm theo chương hoặc tất cả chương
 * Method: GET
 * Params: lop_hoc_id (required), chuong_id (optional, default: all)
 * Output: Dữ liệu điểm với công thức 10% CC + 40% Bài tập + 50% Kiểm tra
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
    $controller->layDiemTheoChuong();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'thanh_cong' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
