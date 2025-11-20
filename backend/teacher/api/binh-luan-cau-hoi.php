<?php
/**
 * File: binh-luan-cau-hoi.php
 * Mục đích: API quản lý bình luận theo câu hỏi bài tập (Giảng viên)
 * Method: GET (lấy danh sách), POST (gửi bình luận mới)
 */

// Tắt hiển thị lỗi ra output (chỉ log vào file)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Khởi động session
session_start();

// Cấu hình CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Load các class cần thiết
require_once __DIR__ . '/../../co-so/Database.php';
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

// Khởi tạo controller và gọi method
$controller = new GiangVienController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Lấy danh sách bình luận
    $controller->layBinhLuanCauHoi();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gửi bình luận mới
    $controller->guiBinhLuanCauHoi();
} else {
    http_response_code(405);
    echo json_encode([
        'thanh_cong' => false,
        'thong_bao' => 'Phương thức không được hỗ trợ'
    ], JSON_UNESCAPED_UNICODE);
}
