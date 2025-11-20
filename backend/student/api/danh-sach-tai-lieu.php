<?php

/**
 * File: danh-sach-tai-lieu.php
 * Mục đích: API lấy danh sách tài liệu của lớp học
 * Method: GET
 * Params: lop_hoc_id
 * Output: Danh sách tài liệu (PDF, DOCX, ...) của lớp học
 */

session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';
require_once __DIR__ . '/../../dieu-khieu/SinhVienController.php';

try {
    // Khởi tạo controller
    $controller = new SinhVienController();

    // Gọi method trong controller
    $controller->layDanhSachTaiLieu();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
