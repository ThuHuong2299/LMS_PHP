<?php
/**
 * File: thong-bao-lop-hoc.php
 * Mục đích: API lấy danh sách thông báo của lớp học (Tab Thông báo)
 * Method: GET
 * Params: id (lop_hoc_id)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: [
 *     {
 *       id, tieu_de, noi_dung, thoi_gian_gui,
 *       nguoi_gui: {ho_ten, anh_dai_dien}
 *     }
 *   ]
 * }
 */

// Khởi động session
session_start();

// Cho phép CORS
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Import files
require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';

// Kiểm tra method
kiem_tra_method('GET');

// Kiểm tra quyền giảng viên
$giang_vien_id = yeu_cau_quyen_giang_vien();

// Lấy tham số
$lop_hoc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lop_hoc_id <= 0) {
    tra_ve_json(false, null, 'ID lớp học không hợp lệ');
}

try {
    $db = lay_ket_noi_db();
    
    if (!$db) {
        tra_ve_json(false, null, 'Không thể kết nối database');
    }
    
    // Kiểm tra quyền truy cập lớp học
    $sql_check = "SELECT giang_vien_id FROM lop_hoc WHERE id = :lop_hoc_id";
    $stmt_check = $db->prepare($sql_check);
    $stmt_check->execute(['lop_hoc_id' => $lop_hoc_id]);
    $lop_info = $stmt_check->fetch();
    
    if (!$lop_info) {
        tra_ve_json(false, null, 'Không tìm thấy lớp học');
    }
    
    if ((int)$lop_info['giang_vien_id'] !== $giang_vien_id) {
        tra_ve_json(false, null, 'Bạn không có quyền truy cập lớp học này');
    }
    
    // Lấy danh sách thông báo (không có số người xem theo yêu cầu)
    $sql = "SELECT 
                tb.id,
                tb.tieu_de,
                tb.noi_dung,
                tb.thoi_gian_gui,
                -- Thông tin người gửi
                nd.ho_ten,
                nd.anh_dai_dien
            FROM thong_bao_lop_hoc tb
            JOIN nguoi_dung nd ON tb.nguoi_gui_id = nd.id
            WHERE tb.lop_hoc_id = :lop_hoc_id
            ORDER BY tb.thoi_gian_gui DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['lop_hoc_id' => $lop_hoc_id]);
    $danh_sach_thong_bao = $stmt->fetchAll();
    
    // Format dữ liệu
    $result = array_map(function($tb) {
        return [
            'id' => (int)$tb['id'],
            'tieu_de' => $tb['tieu_de'],
            'noi_dung' => $tb['noi_dung'],
            'thoi_gian_gui' => $tb['thoi_gian_gui'],
            'nguoi_gui' => [
                'ho_ten' => $tb['ho_ten'],
                'anh_dai_dien' => $tb['anh_dai_dien']
            ]
        ];
    }, $danh_sach_thong_bao);
    
    // Trả về kết quả
    tra_ve_json(true, $result, 'Lấy danh sách thông báo thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong thong-bao-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy dữ liệu');
} catch (Exception $e) {
    error_log("Lỗi trong thong-bao-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
