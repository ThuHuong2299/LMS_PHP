<?php
/**
 * File: hoat-dong-gan-day-oop.php
 * Mục đích: API lấy hoạt động gần đây (phiên bản OOP)
 * Method: GET
 * 
 * ĐÂY LÀ PHIÊN BẢN OOP - SO SÁNH VỚI FILE CŨ hoat-dong-gan-day.php
 */

// Khởi động session
session_start();

// Cấu hình CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
$controller->layHoatDongGanDay();
