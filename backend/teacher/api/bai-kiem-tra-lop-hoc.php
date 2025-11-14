<?php
/**
 * File: bai-kiem-tra-lop-hoc.php
 * Mục đích: API lấy danh sách bài kiểm tra của lớp học (Tab Bài kiểm tra)
 * Method: GET
 * Params: id (lop_hoc_id)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: [
 *     {
 *       id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau,
 *       so_cau_hoi, so_sinh_vien_da_lam, tong_sinh_vien,
 *       chuong: {so_thu_tu, ten_chuong} (nếu có)
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
    
    // Lấy tổng số sinh viên trong lớp
    $sql_total_sv = "SELECT COUNT(DISTINCT sinh_vien_id) as total
                     FROM sinh_vien_lop_hoc
                     WHERE lop_hoc_id = :lop_hoc_id
                     AND trang_thai = 'dang_hoc'";
    $stmt_total_sv = $db->prepare($sql_total_sv);
    $stmt_total_sv->execute(['lop_hoc_id' => $lop_hoc_id]);
    $tong_sinh_vien = (int)$stmt_total_sv->fetch()['total'];
    
    // Lấy danh sách bài kiểm tra với thông tin đầy đủ
    $sql = "SELECT 
                bkt.id,
                bkt.tieu_de,
                bkt.mo_ta,
                bkt.thoi_luong,
                bkt.thoi_gian_bat_dau,
                bkt.thoi_gian_ket_thuc,
                bkt.diem_toi_da,
                bkt.chuong_id,
                bkt.ngay_tao,
                -- Thông tin chương (nếu có)
                c.so_thu_tu_chuong,
                c.ten_chuong,
                -- Số câu hỏi
                (SELECT COUNT(*) FROM cau_hoi_trac_nghiem WHERE bai_kiem_tra_id = bkt.id) AS so_cau_hoi,
                -- Số sinh viên đã làm
                (SELECT COUNT(DISTINCT blkt.sinh_vien_id) 
                 FROM bai_lam_kiem_tra blkt 
                 WHERE blkt.bai_kiem_tra_id = bkt.id 
                 AND blkt.trang_thai IN ('da_nop', 'da_cham')) AS so_sinh_vien_da_lam
            FROM bai_kiem_tra bkt
            LEFT JOIN chuong c ON bkt.chuong_id = c.id
            WHERE bkt.lop_hoc_id = :lop_hoc_id
            ORDER BY bkt.ngay_tao DESC, bkt.thoi_gian_bat_dau ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['lop_hoc_id' => $lop_hoc_id]);
    $danh_sach_bai_kiem_tra = $stmt->fetchAll();
    
    // Format dữ liệu
    $result = array_map(function($bkt) use ($tong_sinh_vien) {
        $data = [
            'id' => (int)$bkt['id'],
            'tieu_de' => $bkt['tieu_de'],
            'mo_ta' => $bkt['mo_ta'],
            'thoi_luong' => (int)$bkt['thoi_luong'],
            'thoi_gian_bat_dau' => $bkt['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $bkt['thoi_gian_ket_thuc'],
            'diem_toi_da' => (float)$bkt['diem_toi_da'],
            'so_cau_hoi' => (int)$bkt['so_cau_hoi'],
            'so_sinh_vien_da_lam' => (int)$bkt['so_sinh_vien_da_lam'],
            'tong_sinh_vien' => $tong_sinh_vien,
            'ngay_tao' => $bkt['ngay_tao']
        ];
        
        // Thêm thông tin chương (nếu có)
        if ($bkt['chuong_id']) {
            $data['chuong'] = [
                'so_thu_tu' => (int)$bkt['so_thu_tu_chuong'],
                'ten_chuong' => $bkt['ten_chuong']
            ];
        } else {
            $data['chuong'] = null;
        }
        
        return $data;
    }, $danh_sach_bai_kiem_tra);
    
    // Trả về kết quả
    tra_ve_json(true, $result, 'Lấy danh sách bài kiểm tra thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong bai-kiem-tra-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy dữ liệu');
} catch (Exception $e) {
    error_log("Lỗi trong bai-kiem-tra-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
