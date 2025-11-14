<?php
/**
 * File: bai-giang-lop-hoc.php
 * Mục đích: API lấy thông tin lớp học và danh sách bài giảng (Tab Bài giảng)
 * Method: GET
 * Params: id (lop_hoc_id)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: {
 *     thong_tin_lop: {
 *       id, ma_lop_hoc, ten_mon_hoc, ten_lop_hoc, trang_thai
 *     },
 *     chuong_va_bai_giang: [
 *       {
 *         id, so_thu_tu_chuong, ten_chuong, muc_tieu,
 *         bai_giang: [
 *           {id, so_thu_tu_bai, tieu_de, duong_dan_video}
 *         ]
 *       }
 *     ]
 *   }
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
    
    // 1. Lấy thông tin lớp học và kiểm tra quyền truy cập
    $sql_lop = "SELECT 
                    lh.id,
                    lh.ma_lop_hoc,
                    lh.ten_lop_hoc,
                    lh.trang_thai,
                    mh.ten_mon_hoc,
                    lh.giang_vien_id
                FROM lop_hoc lh
                JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
                WHERE lh.id = :lop_hoc_id";
    
    $stmt_lop = $db->prepare($sql_lop);
    $stmt_lop->execute(['lop_hoc_id' => $lop_hoc_id]);
    $thong_tin_lop = $stmt_lop->fetch();
    
    if (!$thong_tin_lop) {
        tra_ve_json(false, null, 'Không tìm thấy lớp học');
    }
    
    // Kiểm tra giảng viên có quyền truy cập lớp này không
    if ((int)$thong_tin_lop['giang_vien_id'] !== $giang_vien_id) {
        tra_ve_json(false, null, 'Bạn không có quyền truy cập lớp học này');
    }
    
    // Xóa giang_vien_id khỏi response (không cần thiết)
    unset($thong_tin_lop['giang_vien_id']);
    
    // 2. Lấy danh sách chương
    $sql_chuong = "SELECT 
                        id,
                        so_thu_tu_chuong,
                        ten_chuong,
                        muc_tieu,
                        thu_tu_sap_xep
                    FROM chuong
                    WHERE lop_hoc_id = :lop_hoc_id
                    ORDER BY thu_tu_sap_xep ASC, so_thu_tu_chuong ASC";
    
    $stmt_chuong = $db->prepare($sql_chuong);
    $stmt_chuong->execute(['lop_hoc_id' => $lop_hoc_id]);
    $danh_sach_chuong = $stmt_chuong->fetchAll();
    
    // 3. Lấy tất cả bài giảng của lớp
    $sql_bai_giang = "SELECT 
                        id,
                        chuong_id,
                        so_thu_tu_bai,
                        tieu_de,
                        duong_dan_video,
                        ngay_tao
                    FROM bai_giang
                    WHERE lop_hoc_id = :lop_hoc_id
                    ORDER BY so_thu_tu_bai ASC";
    
    $stmt_bai_giang = $db->prepare($sql_bai_giang);
    $stmt_bai_giang->execute(['lop_hoc_id' => $lop_hoc_id]);
    $danh_sach_bai_giang = $stmt_bai_giang->fetchAll();
    
    // 4. Nhóm bài giảng theo chương (nested structure)
    $chuong_va_bai_giang = [];
    
    foreach ($danh_sach_chuong as $chuong) {
        $chuong_id = (int)$chuong['id'];
        
        // Lọc bài giảng thuộc chương này
        $bai_giang_cua_chuong = array_filter($danh_sach_bai_giang, function($bai_giang) use ($chuong_id) {
            return (int)$bai_giang['chuong_id'] === $chuong_id;
        });
        
        // Chuyển đổi số sang integer và format lại
        $bai_giang_formatted = array_map(function($bg) {
            return [
                'id' => (int)$bg['id'],
                'so_thu_tu_bai' => (float)$bg['so_thu_tu_bai'],
                'tieu_de' => $bg['tieu_de'],
                'duong_dan_video' => $bg['duong_dan_video'],
                'ngay_tao' => $bg['ngay_tao']
            ];
        }, array_values($bai_giang_cua_chuong));
        
        // Thêm vào kết quả
        $chuong_va_bai_giang[] = [
            'id' => $chuong_id,
            'so_thu_tu_chuong' => (int)$chuong['so_thu_tu_chuong'],
            'ten_chuong' => $chuong['ten_chuong'],
            'muc_tieu' => $chuong['muc_tieu'],
            'bai_giang' => $bai_giang_formatted
        ];
    }
    
    // Trả về kết quả
    tra_ve_json(true, [
        'thong_tin_lop' => [
            'id' => (int)$thong_tin_lop['id'],
            'ma_lop_hoc' => $thong_tin_lop['ma_lop_hoc'],
            'ten_mon_hoc' => $thong_tin_lop['ten_mon_hoc'],
            'ten_lop_hoc' => $thong_tin_lop['ten_lop_hoc'],
            'trang_thai' => $thong_tin_lop['trang_thai']
        ],
        'chuong_va_bai_giang' => $chuong_va_bai_giang
    ], 'Lấy thông tin bài giảng thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong bai-giang-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy dữ liệu');
} catch (Exception $e) {
    error_log("Lỗi trong bai-giang-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
