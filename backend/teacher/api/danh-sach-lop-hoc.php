<?php
/**
 * File: danh-sach-lop-hoc.php
 * Mục đích: API lấy danh sách lớp học của giảng viên
 * Method: GET
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: [
 *     {
 *       id: 1,
 *       ma_lop_hoc: "213_eCIT2320_09",
 *       ten_mon_hoc: "Các Hệ thống thông tin phổ biến trong doanh nghiệp",
 *       so_sinh_vien: 36,
 *       so_bai_tap: 12,
 *       so_bai_kiem_tra: 6
 *     }
 *   ]
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
    
    // Query lấy danh sách lớp học với thông tin đầy đủ
    $sql = "SELECT 
                lh.id,
                lh.ma_lop_hoc,
                mh.ten_mon_hoc,
                lh.trang_thai,
                (SELECT COUNT(DISTINCT svlh.sinh_vien_id) 
                 FROM sinh_vien_lop_hoc svlh 
                 WHERE svlh.lop_hoc_id = lh.id 
                 AND svlh.trang_thai = 'dang_hoc') AS so_sinh_vien,
                (SELECT COUNT(*) 
                 FROM bai_tap bt 
                 WHERE bt.lop_hoc_id = lh.id) AS so_bai_tap,
                (SELECT COUNT(*) 
                 FROM bai_kiem_tra bkt 
                 WHERE bkt.lop_hoc_id = lh.id) AS so_bai_kiem_tra,
                lh.ngay_tao
            FROM lop_hoc lh
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            WHERE lh.giang_vien_id = :giang_vien_id
            ORDER BY lh.ngay_tao DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['giang_vien_id' => $giang_vien_id]);
    $danh_sach_lop = $stmt->fetchAll();
    
    // Chuyển đổi số từ string sang integer
    foreach ($danh_sach_lop as &$lop) {
        $lop['id'] = (int) $lop['id'];
        $lop['so_sinh_vien'] = (int) $lop['so_sinh_vien'];
        $lop['so_bai_tap'] = (int) $lop['so_bai_tap'];
        $lop['so_bai_kiem_tra'] = (int) $lop['so_bai_kiem_tra'];
    }
    
    // Trả về kết quả
    tra_ve_json(true, $danh_sach_lop, 'Lấy danh sách lớp học thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong danh-sach-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy danh sách lớp học');
} catch (Exception $e) {
    error_log("Lỗi trong danh-sach-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
