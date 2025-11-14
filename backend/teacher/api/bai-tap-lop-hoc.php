<?php
/**
 * File: bai-tap-lop-hoc.php
 * Mục đích: API lấy danh sách bài tập của lớp học (Tab Bài tập)
 * Method: GET
 * Params: id (lop_hoc_id)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: [
 *     {
 *       id, tieu_de, mo_ta, han_nop,
 *       so_cau_hoi, so_sinh_vien_da_nop, tong_sinh_vien,
 *       chuong: {so_thu_tu, ten_chuong},
 *       bai_giang: {so_thu_tu_bai, tieu_de} (nếu có)
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
    
    // Lấy danh sách bài tập với thông tin đầy đủ
    $sql = "SELECT 
                bt.id,
                bt.tieu_de,
                bt.mo_ta,
                bt.han_nop,
                bt.diem_toi_da,
                bt.chuong_id,
                bt.bai_giang_id,
                bt.ngay_tao,
                -- Thông tin chương
                c.so_thu_tu_chuong,
                c.ten_chuong,
                -- Thông tin bài giảng (nếu có)
                bg.so_thu_tu_bai,
                bg.tieu_de AS ten_bai_giang,
                -- Số câu hỏi
                (SELECT COUNT(*) FROM cau_hoi_bai_tap WHERE bai_tap_id = bt.id) AS so_cau_hoi,
                -- Số sinh viên đã nộp
                (SELECT COUNT(DISTINCT bl.sinh_vien_id) 
                 FROM bai_lam bl 
                 WHERE bl.bai_tap_id = bt.id 
                 AND bl.trang_thai IN ('da_nop', 'da_cham')) AS so_sinh_vien_da_nop
            FROM bai_tap bt
            LEFT JOIN chuong c ON bt.chuong_id = c.id
            LEFT JOIN bai_giang bg ON bt.bai_giang_id = bg.id
            WHERE bt.lop_hoc_id = :lop_hoc_id
            ORDER BY bt.ngay_tao DESC, bt.han_nop ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['lop_hoc_id' => $lop_hoc_id]);
    $danh_sach_bai_tap = $stmt->fetchAll();
    
    // Format dữ liệu
    $result = array_map(function($bt) use ($tong_sinh_vien) {
        $data = [
            'id' => (int)$bt['id'],
            'tieu_de' => $bt['tieu_de'],
            'mo_ta' => $bt['mo_ta'],
            'han_nop' => $bt['han_nop'],
            'diem_toi_da' => (float)$bt['diem_toi_da'],
            'so_cau_hoi' => (int)$bt['so_cau_hoi'],
            'so_sinh_vien_da_nop' => (int)$bt['so_sinh_vien_da_nop'],
            'tong_sinh_vien' => $tong_sinh_vien,
            'ngay_tao' => $bt['ngay_tao']
        ];
        
        // Thêm thông tin chương (nếu có)
        if ($bt['chuong_id']) {
            $data['chuong'] = [
                'so_thu_tu' => (int)$bt['so_thu_tu_chuong'],
                'ten_chuong' => $bt['ten_chuong']
            ];
        } else {
            $data['chuong'] = null;
        }
        
        // Thêm thông tin bài giảng (nếu có)
        if ($bt['bai_giang_id']) {
            $data['bai_giang'] = [
                'so_thu_tu_bai' => (float)$bt['so_thu_tu_bai'],
                'tieu_de' => $bt['ten_bai_giang']
            ];
        } else {
            $data['bai_giang'] = null;
        }
        
        return $data;
    }, $danh_sach_bai_tap);
    
    // Trả về kết quả
    tra_ve_json(true, $result, 'Lấy danh sách bài tập thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong bai-tap-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy dữ liệu');
} catch (Exception $e) {
    error_log("Lỗi trong bai-tap-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
