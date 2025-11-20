<?php
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Phương thức không được hỗ trợ']);
    exit;
}

try {
    // Kiểm tra đăng nhập
    $phien = QuanLyPhien::lay_thong_tin_phien();
    if (!$phien || $phien['vai_tro'] !== 'giang_vien') {
        http_response_code(401);
        echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Chưa đăng nhập hoặc không có quyền']);
        exit;
    }

    // Lấy dữ liệu từ request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['tra_loi_id']) || !isset($input['diem'])) {
        http_response_code(400);
        echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Thiếu thông tin tra_loi_id hoặc diem']);
        exit;
    }

    $controller = new GiangVienController();
    $result = $controller->chamDiemCauHoi($input['tra_loi_id'], $input['diem']);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Lỗi server: ' . $e->getMessage()
    ]);
}
