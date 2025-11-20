<?php

/**
 * File: chi-tiet-bai-giang.php (Teacher version)
 * Mục đích: API lấy chi tiết bài giảng cho giảng viên
 * Method: GET
 * Params: bai_giang_id, lop_hoc_id
 * Output: Thông tin bài giảng, chương
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

try {
    require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
    require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
    require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';
    require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';
    
    // Khởi tạo controller
    $controller = new GiangVienController();

    // Gọi method trong controller
    $controller->layChiTietBaiGiang();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi hệ thống: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
