<?php

/**
 * File: danh-sach-bai-hoc.php
 * Mục đích: API lấy danh sách bài học trong một chương
 * Method: GET
 * Params: chuong_id, lop_hoc_id
 * Output: Danh sách bài (video + bài tập + kiểm tra) với tiến độ
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
    $controller->layDanhSachBaiHoc();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
