<?php
/**
 * File: HoatDongRepository.php
 * Mục đích: Xử lý truy vấn database cho hoạt động gần đây
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';
require_once __DIR__ . '/../mau/HoatDong.php';

class HoatDongRepository extends BaseRepository {
    
    /**
     * Lấy danh sách hoạt động gần đây của giảng viên
     */
    public function layHoatDongGanDay($giangVienId, $limit = 10) {
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
                CONCAT('het_han_bkt_', bkt.id) as id,
                'het_han_kiem_tra' as loai,
                '/public/assets/icon-tb-bkt.jpg' as icon,
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
            
            ORDER BY thoi_gian DESC
            LIMIT :limit";
        
        $params = [
            'giang_vien_id1' => $giangVienId,
            'giang_vien_id2' => $giangVienId,
            'giang_vien_id3' => $giangVienId,
            'limit' => $limit
        ];
        
        return $this->truyVan($sql, $params);
    }
}
