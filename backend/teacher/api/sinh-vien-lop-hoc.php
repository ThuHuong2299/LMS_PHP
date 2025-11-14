<?php
/**
 * File: sinh-vien-lop-hoc.php
 * Mục đích: API lấy danh sách sinh viên và tiến độ học tập (Tab Sinh viên)
 * Method: GET
 * Params: id (lop_hoc_id), page (trang hiện tại, mặc định 1), limit (số sinh viên/trang, mặc định 5)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: {
 *     sinh_vien: [
 *       {
 *         id, ma_nguoi_dung, ho_ten, anh_dai_dien,
 *         tien_do: 75.5,
 *         last_updated: "2 giờ trước"
 *       }
 *     ],
 *     pagination: {
 *       trang_hien_tai, tong_trang, tong_sinh_vien, 
 *       bat_dau, ket_thuc
 *     }
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
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 5;
$offset = ($page - 1) * $limit;

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
    
    // Đếm tổng số sinh viên trong lớp
    $sql_count = "SELECT COUNT(*) as total
                  FROM sinh_vien_lop_hoc svlh
                  WHERE svlh.lop_hoc_id = :lop_hoc_id
                  AND svlh.trang_thai = 'dang_hoc'";
    $stmt_count = $db->prepare($sql_count);
    $stmt_count->execute(['lop_hoc_id' => $lop_hoc_id]);
    $tong_sinh_vien = (int)$stmt_count->fetch()['total'];
    $tong_trang = ceil($tong_sinh_vien / $limit);
    
    // Lấy tổng số nội dung trong lớp (bài giảng + bài tập + bài kiểm tra)
    $sql_total_content = "SELECT 
                            (SELECT COUNT(*) FROM bai_giang WHERE lop_hoc_id = :lop_hoc_id1) +
                            (SELECT COUNT(*) FROM bai_tap WHERE lop_hoc_id = :lop_hoc_id2) +
                            (SELECT COUNT(*) FROM bai_kiem_tra WHERE lop_hoc_id = :lop_hoc_id3) AS tong_noi_dung";
    $stmt_total = $db->prepare($sql_total_content);
    $stmt_total->execute([
        'lop_hoc_id1' => $lop_hoc_id,
        'lop_hoc_id2' => $lop_hoc_id,
        'lop_hoc_id3' => $lop_hoc_id
    ]);
    $tong_noi_dung = (int)$stmt_total->fetch()['tong_noi_dung'];
    
    // Lấy danh sách sinh viên với tiến độ
    $sql = "SELECT 
                nd.id,
                nd.ma_nguoi_dung,
                nd.ho_ten,
                nd.anh_dai_dien,
                svlh.ngay_dang_ky,
                -- Tính tiến độ: đếm số nội dung đã hoàn thành
                (SELECT COUNT(*) 
                 FROM tien_do_hoc_tap td 
                 WHERE td.sinh_vien_id = nd.id 
                 AND td.lop_hoc_id = :lop_hoc_id 
                 AND td.da_hoan_thanh = TRUE) AS so_noi_dung_hoan_thanh,
                -- Lấy thời gian cập nhật tiến độ gần nhất
                (SELECT MAX(td.lan_cap_nhat_cuoi) 
                 FROM tien_do_hoc_tap td 
                 WHERE td.sinh_vien_id = nd.id 
                 AND td.lop_hoc_id = :lop_hoc_id2) AS last_updated
            FROM sinh_vien_lop_hoc svlh
            JOIN nguoi_dung nd ON svlh.sinh_vien_id = nd.id
            WHERE svlh.lop_hoc_id = :lop_hoc_id3
            AND svlh.trang_thai = 'dang_hoc'
            ORDER BY nd.ho_ten ASC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':lop_hoc_id', $lop_hoc_id, PDO::PARAM_INT);
    $stmt->bindValue(':lop_hoc_id2', $lop_hoc_id, PDO::PARAM_INT);
    $stmt->bindValue(':lop_hoc_id3', $lop_hoc_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $danh_sach_sinh_vien = $stmt->fetchAll();
    
    // Format dữ liệu
    $sinh_vien_formatted = array_map(function($sv) use ($tong_noi_dung) {
        $so_hoan_thanh = (int)$sv['so_noi_dung_hoan_thanh'];
        
        // Tính phần trăm tiến độ
        $tien_do = 0;
        if ($tong_noi_dung > 0) {
            $tien_do = round(($so_hoan_thanh / $tong_noi_dung) * 100, 1);
        }
        
        // Format thời gian last updated
        $last_updated = 'Chưa có hoạt động';
        if ($sv['last_updated']) {
            $last_updated = tinh_thoi_gian_da_qua($sv['last_updated']);
        }
        
        return [
            'id' => (int)$sv['id'],
            'ma_nguoi_dung' => $sv['ma_nguoi_dung'],
            'ho_ten' => $sv['ho_ten'],
            'anh_dai_dien' => $sv['anh_dai_dien'],
            'tien_do' => $tien_do,
            'last_updated' => $last_updated
        ];
    }, $danh_sach_sinh_vien);
    
    // Tính số thứ tự sinh viên
    $bat_dau = $offset + 1;
    $ket_thuc = min($offset + $limit, $tong_sinh_vien);
    
    // Trả về kết quả
    tra_ve_json(true, [
        'sinh_vien' => $sinh_vien_formatted,
        'pagination' => [
            'trang_hien_tai' => $page,
            'tong_trang' => $tong_trang,
            'tong_sinh_vien' => $tong_sinh_vien,
            'bat_dau' => $bat_dau,
            'ket_thuc' => $ket_thuc,
            'limit' => $limit
        ]
    ], 'Lấy danh sách sinh viên thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong sinh-vien-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy dữ liệu');
} catch (Exception $e) {
    error_log("Lỗi trong sinh-vien-lop-hoc.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
