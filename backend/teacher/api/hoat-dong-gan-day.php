<?php
/**
 * File: hoat-dong-gan-day.php
 * Mục đích: API lấy danh sách hoạt động gần đây cho dashboard giảng viên
 * Method: GET
 * Params: ?limit=10 (số lượng hoạt động, mặc định 10)
 * Output: {
 *   thanh_cong: true,
 *   du_lieu: [
 *     {
 *       id: 1,
 *       loai: 'nop_bai|het_han_bai_tap|het_han_kiem_tra',
 *       icon: 'frame0.svg',
 *       ho_ten: 'Phạm Anh Tú',
 *       anh_dai_dien: 'url',
 *       tieu_de_bai: 'Bài tập chương 1',
 *       noi_dung: 'đã nộp bài',
 *       thoi_gian: '2025-11-14 10:30:00',
 *       thoi_gian_hien_thi: '5 phút trước'
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

// Lấy tham số limit
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$limit = max(1, min($limit, 50)); // Giới hạn từ 1-50

try {
    // Kết nối database
    $db = lay_ket_noi_db();
    
    if (!$db) {
        tra_ve_json(false, null, 'Không thể kết nối database');
    }
    
    // Query UNION để lấy các hoạt động từ nhiều nguồn
    $sql = "
        -- 1. Sinh viên nộp bài tập
        SELECT 
            CONCAT('nop_bai_', bl.id) as id,
            'nop_bai' as loai,
            'frame0.svg' as icon,
            nd.ho_ten,
            nd.anh_dai_dien,
            bt.tieu_de as tieu_de_bai,
            'đã nộp bài' as noi_dung,
            bl.thoi_gian_nop as thoi_gian
        FROM bai_lam bl
        JOIN nguoi_dung nd ON bl.sinh_vien_id = nd.id
        JOIN bai_tap bt ON bl.bai_tap_id = bt.id
        JOIN lop_hoc l ON bt.lop_hoc_id = l.id
        WHERE l.giang_vien_id = :giang_vien_id1
        AND bl.trang_thai IN ('da_nop', 'da_cham')
        AND bl.thoi_gian_nop IS NOT NULL
        AND bl.thoi_gian_nop >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        
        UNION ALL
        
        -- 2. Bài tập hết hạn nộp
        SELECT 
            CONCAT('het_han_bt_', bt.id) as id,
            'het_han_bai_tap' as loai,
            'exercise1.svg' as icon,
            NULL as ho_ten,
            NULL as anh_dai_dien,
            bt.tieu_de as tieu_de_bai,
            'hết hạn nộp' as noi_dung,
            bt.han_nop as thoi_gian
        FROM bai_tap bt
        JOIN lop_hoc l ON bt.lop_hoc_id = l.id
        WHERE l.giang_vien_id = :giang_vien_id2
        AND bt.han_nop IS NOT NULL
        AND bt.han_nop < NOW()
        AND bt.han_nop >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        
        UNION ALL
        
        -- 3. Bài kiểm tra hết hạn
        SELECT 
            CONCAT('het_han_kt_', bkt.id) as id,
            'het_han_kiem_tra' as loai,
            'exam1.svg' as icon,
            NULL as ho_ten,
            NULL as anh_dai_dien,
            bkt.tieu_de as tieu_de_bai,
            'hết hạn làm bài' as noi_dung,
            bkt.thoi_gian_ket_thuc as thoi_gian
        FROM bai_kiem_tra bkt
        JOIN lop_hoc l ON bkt.lop_hoc_id = l.id
        WHERE l.giang_vien_id = :giang_vien_id3
        AND bkt.thoi_gian_ket_thuc IS NOT NULL
        AND bkt.thoi_gian_ket_thuc < NOW()
        AND bkt.thoi_gian_ket_thuc >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        
        UNION ALL
        
        -- 4. Sinh viên nộp bài kiểm tra
        SELECT 
            CONCAT('nop_kt_', blkt.id) as id,
            'nop_bai' as loai,
            'frame0.svg' as icon,
            nd.ho_ten,
            nd.anh_dai_dien,
            bkt.tieu_de as tieu_de_bai,
            'đã nộp bài kiểm tra' as noi_dung,
            blkt.thoi_gian_nop as thoi_gian
        FROM bai_lam_kiem_tra blkt
        JOIN nguoi_dung nd ON blkt.sinh_vien_id = nd.id
        JOIN bai_kiem_tra bkt ON blkt.bai_kiem_tra_id = bkt.id
        JOIN lop_hoc l ON bkt.lop_hoc_id = l.id
        WHERE l.giang_vien_id = :giang_vien_id4
        AND blkt.trang_thai IN ('da_nop', 'da_cham')
        AND blkt.thoi_gian_nop IS NOT NULL
        AND blkt.thoi_gian_nop >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        
        ORDER BY thoi_gian DESC
        LIMIT :limit
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':giang_vien_id1', $giang_vien_id, PDO::PARAM_INT);
    $stmt->bindValue(':giang_vien_id2', $giang_vien_id, PDO::PARAM_INT);
    $stmt->bindValue(':giang_vien_id3', $giang_vien_id, PDO::PARAM_INT);
    $stmt->bindValue(':giang_vien_id4', $giang_vien_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $hoat_dong = $stmt->fetchAll();
    
    // Format thời gian hiển thị cho mỗi hoạt động
    foreach ($hoat_dong as &$item) {
        $item['thoi_gian_hien_thi'] = tinh_thoi_gian_da_qua($item['thoi_gian']);
        
        // Xử lý ảnh đại diện mặc định nếu null
        if (empty($item['anh_dai_dien'])) {
            $item['anh_dai_dien'] = 'frame0.svg';
        }
    }
    
    // Trả về kết quả
    tra_ve_json(true, $hoat_dong, 'Lấy hoạt động gần đây thành công');
    
} catch (PDOException $e) {
    error_log("Lỗi database trong hoat-dong-gan-day.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra khi lấy hoạt động');
} catch (Exception $e) {
    error_log("Lỗi trong hoat-dong-gan-day.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
