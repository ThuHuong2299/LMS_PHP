<?php
/**
 * File: thong-bao-lop-hoc.php
 * Mục đích: API lấy danh sách thông báo (OOP)
 * Method: GET
 * Params: id (lop_hoc_id)
 */

session_start();

header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['thanh_cong' => false, 'thong_bao' => 'Phương thức không được hỗ trợ'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../../co-so/Database.php';
require_once __DIR__ . '/../../dieu-khieu/GiangVienController.php';

$controller = new GiangVienController();
$controller->layThongBaoLopHoc();
