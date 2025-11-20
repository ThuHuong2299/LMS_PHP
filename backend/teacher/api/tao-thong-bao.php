<?php
/**
 * File: tao-thong-bao.php
 * Mục đích: API tạo thông báo mới cho lớp học
 * Method: POST
 * Body: {lop_hoc_id, tieu_de, noi_dung}
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../../co-so/Database.php';
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

$controller = new GiangVienController();
$controller->taoThongBao();
