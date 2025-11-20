<?php
/**
 * File: tao-bai-kiem-tra.php
 * Mục đích: API tạo bài kiểm tra trắc nghiệm
 * Method: POST
 * Body: {lop_hoc_id, chuong_id, tieu_de, thoi_luong, thoi_gian_bat_dau, cho_phep_lam_lai, cau_hoi: [{noi_dung_cau_hoi, diem, cac_lua_chon: [{noi_dung, dung}]}]}
 */

// Khởi động session
session_start();

// Cấu hình CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Phương thức không được hỗ trợ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Load các class cần thiết
require_once __DIR__ . '/../../co-so/Database.php';
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

// Khởi tạo controller và gọi method
$controller = new GiangVienController();
$controller->taoBaiKiemTra();
