<?php
/**
 * File: dashboard-stats.php
 * Mục đích: API lấy thống kê dashboard cho giảng viên
 * Method: GET
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: {
 *     lop_giang_day: 8,
 *     sinh_vien_theo_hoc: 80,
 *     bai_cho_cham: 12,
 *     thong_bao_moi: 3
 *   }
 * }
 */

// Khởi động session NGAY ĐẦU
session_start();

// Cho phép CORS với credentials
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Import các file cần thiết
require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';

// Kiểm tra method
kiem_tra_method('GET');

// Kiểm tra quyền giảng viên
$giang_vien_id = yeu_cau_quyen_giang_vien();

try {
    // Kết nối database
    $db = lay_ket_noi_db();
    
    if (!$db) {
        tra_ve_json(false, null, 'Không thể kết nối database');
    }
    
    // 1. Đếm số lớp giảng dạy
    $sql_lop = "SELECT COUNT(*) as total FROM lop_hoc WHERE giang_vien_id = :giang_vien_id";
    $stmt_lop = $db->prepare($sql_lop);
    $stmt_lop->execute(['giang_vien_id' => $giang_vien_id]);
    $lop_giang_day = (int) $stmt_lop->fetch()['total'];
    
    // 2. Đếm số sinh viên theo học
    $sql_sv = "SELECT COUNT(DISTINCT sv.sinh_vien_id) as total
               FROM sinh_vien_lop_hoc sv
               JOIN lop_hoc l ON sv.lop_hoc_id = l.id
               WHERE l.giang_vien_id = :giang_vien_id
               AND sv.trang_thai = 'dang_hoc'";
    $stmt_sv = $db->prepare($sql_sv);
    $stmt_sv->execute(['giang_vien_id' => $giang_vien_id]);
    $sinh_vien_theo_hoc = (int) $stmt_sv->fetch()['total'];
    
    // 3. Đếm bài chờ chấm (bài tập text đã nộp chưa chấm)
    $sql_bai = "SELECT COUNT(*) as total
                FROM bai_lam bl
                JOIN bai_tap bt ON bl.bai_tap_id = bt.id
                JOIN lop_hoc l ON bt.lop_hoc_id = l.id
                WHERE l.giang_vien_id = :giang_vien_id
                AND bl.trang_thai = 'da_nop'";
    $stmt_bai = $db->prepare($sql_bai);
    $stmt_bai->execute(['giang_vien_id' => $giang_vien_id]);
    $bai_cho_cham = (int) $stmt_bai->fetch()['total'];
    
    // 4. Đếm thông báo mới (24 giờ gần đây)
    $sql_tb = "SELECT COUNT(*) as total
               FROM thong_bao_lop_hoc tb
               JOIN lop_hoc l ON tb.lop_hoc_id = l.id
               WHERE l.giang_vien_id = :giang_vien_id
               AND tb.thoi_gian_gui >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    $stmt_tb = $db->prepare($sql_tb);
    $stmt_tb->execute(['giang_vien_id' => $giang_vien_id]);
    $thong_bao_moi = (int) $stmt_tb->fetch()['total'];
    
    // Trả về kết quả
    tra_ve_json(true, [
        'lop_giang_day' => $lop_giang_day,
        'sinh_vien_theo_hoc' => $sinh_vien_theo_hoc,
        'bai_cho_cham' => $bai_cho_cham,
        'thong_bao_moi' => $thong_bao_moi
    ], 'Lấy thống kê thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong dashboard-stats.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy thống kê');
} catch (Exception $e) {
    error_log("Lỗi trong dashboard-stats.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
