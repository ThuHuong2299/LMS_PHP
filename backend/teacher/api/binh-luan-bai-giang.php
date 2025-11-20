<?php

/**
 * File: binh-luan-bai-giang.php (Teacher version)
 * Mục đích: API lấy danh sách bình luận bài giảng cho giảng viên
 * Method: GET
 * Params: bai_giang_id
 * Output: Danh sách bình luận + phản hồi
 */

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
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

try {
    // Khởi tạo controller
    $controller = new GiangVienController();

    // Gọi method trong controller
    $controller->layBinhLuanBaiGiang();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
