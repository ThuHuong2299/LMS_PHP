<?php
/**
 * File: ThongBaoRepository.php
 * Mục đích: Xử lý truy vấn database cho thông báo
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class ThongBaoRepository extends BaseRepository {
    
    /**
     * Lấy danh sách thông báo theo lớp học
     */
    public function timTheoLopHoc($lopHocId) {
        $sql = "SELECT 
                    tb.id, tb.tieu_de, tb.noi_dung, tb.thoi_gian_gui,
                    -- Thông tin người gửi
                    nd.ho_ten, nd.anh_dai_dien
                FROM thong_bao_lop_hoc tb
                JOIN nguoi_dung nd ON tb.nguoi_gui_id = nd.id
                WHERE tb.lop_hoc_id = :lop_hoc_id
                ORDER BY tb.thoi_gian_gui DESC";
        
        return $this->truyVan($sql, ['lop_hoc_id' => $lopHocId]);
    }
}
