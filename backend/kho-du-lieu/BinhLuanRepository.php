<?php
/**
 * File: BinhLuanRepository.php
 * Mục đích: Repository xử lý truy vấn database cho bình luận bài giảng
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class BinhLuanRepository extends BaseRepository {
    
    /**
     * Lấy danh sách bình luận theo bài giảng (có phân trang)
     * Chỉ lấy bình luận gốc, không lấy phản hồi
     */
    public function layBinhLuanTheoBaiGiang($baiGiangId, $offset = 0, $limit = 20) {
        $sql = "SELECT 
                    bl.id,
                    bl.bai_giang_id,
                    bl.nguoi_gui_id,
                    bl.binh_luan_cha_id,
                    bl.noi_dung,
                    bl.thoi_gian_gui,
                    bl.thoi_gian_sua,
                    bl.trang_thai,
                    nd.ho_ten AS nguoi_gui_ho_ten,
                    nd.ma_nguoi_dung AS nguoi_gui_ma,
                    nd.vai_tro AS nguoi_gui_vai_tro,
                    nd.anh_dai_dien AS nguoi_gui_anh
                FROM binh_luan_bai_giang bl
                JOIN nguoi_dung nd ON bl.nguoi_gui_id = nd.id
                WHERE bl.bai_giang_id = :bai_giang_id 
                  AND bl.binh_luan_cha_id IS NULL
                  AND bl.trang_thai = 'hien_thi'
                ORDER BY bl.thoi_gian_gui DESC
                LIMIT :limit OFFSET :offset";
        
        $params = [
            'bai_giang_id' => $baiGiangId,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        return $this->truyVan($sql, $params);
    }
    
    /**
     * Lấy tất cả phản hồi của một bình luận gốc
     */
    public function layPhanHoiBinhLuan($binhLuanChaId) {
        $sql = "SELECT 
                    bl.id,
                    bl.bai_giang_id,
                    bl.nguoi_gui_id,
                    bl.binh_luan_cha_id,
                    bl.noi_dung,
                    bl.thoi_gian_gui,
                    bl.thoi_gian_sua,
                    bl.trang_thai,
                    nd.ho_ten AS nguoi_gui_ho_ten,
                    nd.ma_nguoi_dung AS nguoi_gui_ma,
                    nd.vai_tro AS nguoi_gui_vai_tro,
                    nd.anh_dai_dien AS nguoi_gui_anh
                FROM binh_luan_bai_giang bl
                JOIN nguoi_dung nd ON bl.nguoi_gui_id = nd.id
                WHERE bl.binh_luan_cha_id = :binh_luan_cha_id
                  AND bl.trang_thai = 'hien_thi'
                ORDER BY bl.thoi_gian_gui ASC";
        
        return $this->truyVan($sql, ['binh_luan_cha_id' => $binhLuanChaId]);
    }
    
    /**
     * Lấy cây bình luận hoàn chỉnh (bình luận + phản hồi)
     */
    public function layBinhLuanCoPhanhoi($baiGiangId, $offset = 0, $limit = 20) {
        // Lấy bình luận gốc
        $binhLuanGoc = $this->layBinhLuanTheoBaiGiang($baiGiangId, $offset, $limit);
        
        // Lấy phản hồi cho từng bình luận
        foreach ($binhLuanGoc as &$binhLuan) {
            $binhLuan['phan_hoi'] = $this->layPhanHoiBinhLuan($binhLuan['id']);
            $binhLuan['so_phan_hoi'] = count($binhLuan['phan_hoi']);
        }
        
        return $binhLuanGoc;
    }
    
    /**
     * Đếm tổng số bình luận (bao gồm cả bình luận cha và con)
     */
    public function demTongBinhLuan($baiGiangId) {
        $sql = "SELECT COUNT(*) as tong 
                FROM binh_luan_bai_giang 
                WHERE bai_giang_id = :bai_giang_id 
                  AND trang_thai = 'hien_thi'";
        
        $result = $this->truyVanMot($sql, ['bai_giang_id' => $baiGiangId]);
        return $result ? (int)$result['tong'] : 0;
    }
    
    /**
     * Đếm tổng số phản hồi của một bình luận
     */
    public function demSoPhanHoi($binhLuanChaId) {
        $sql = "SELECT COUNT(*) as tong 
                FROM binh_luan_bai_giang 
                WHERE binh_luan_cha_id = :binh_luan_cha_id
                  AND trang_thai = 'hien_thi'";
        
        $result = $this->truyVanMot($sql, ['binh_luan_cha_id' => $binhLuanChaId]);
        return $result ? (int)$result['tong'] : 0;
    }
    
    /**
     * Thêm bình luận mới
     */
    public function themBinhLuan($data) {
        $sql = "INSERT INTO binh_luan_bai_giang 
                (bai_giang_id, nguoi_gui_id, binh_luan_cha_id, noi_dung) 
                VALUES 
                (:bai_giang_id, :nguoi_gui_id, :binh_luan_cha_id, :noi_dung)";
        
        return $this->thucThi($sql, $data);
    }
    
    /**
     * Lấy ID của bản ghi vừa insert
     */
    public function layIdCuoi() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Lấy thông tin một bình luận
     */
    public function layBinhLuanTheoId($binhLuanId) {
        $sql = "SELECT 
                    bl.id,
                    bl.bai_giang_id,
                    bl.nguoi_gui_id,
                    bl.binh_luan_cha_id,
                    bl.noi_dung,
                    bl.thoi_gian_gui,
                    bl.thoi_gian_sua,
                    bl.trang_thai,
                    nd.ho_ten AS nguoi_gui_ho_ten,
                    nd.ma_nguoi_dung AS nguoi_gui_ma,
                    nd.vai_tro AS nguoi_gui_vai_tro,
                    nd.anh_dai_dien AS nguoi_gui_anh
                FROM binh_luan_bai_giang bl
                JOIN nguoi_dung nd ON bl.nguoi_gui_id = nd.id
                WHERE bl.id = :binh_luan_id";
        
        return $this->truyVanMot($sql, ['binh_luan_id' => $binhLuanId]);
    }
    
    /**
     * Kiểm tra quyền sở hữu bình luận
     */
    public function kiemTraQuyenSoHuu($binhLuanId, $nguoiGuiId) {
        $sql = "SELECT COUNT(*) as tong 
                FROM binh_luan_bai_giang 
                WHERE id = :binh_luan_id 
                  AND nguoi_gui_id = :nguoi_gui_id";
        
        $result = $this->truyVanMot($sql, [
            'binh_luan_id' => $binhLuanId,
            'nguoi_gui_id' => $nguoiGuiId
        ]);
        
        return $result && (int)$result['tong'] > 0;
    }
    
    /**
     * Xóa bình luận (soft delete)
     */
    public function xoaBinhLuan($binhLuanId) {
        $sql = "UPDATE binh_luan_bai_giang 
                SET trang_thai = 'da_xoa' 
                WHERE id = :binh_luan_id";
        
        return $this->thucThi($sql, ['binh_luan_id' => $binhLuanId]);
    }
    
    /**
     * Cập nhật nội dung bình luận
     */
    public function capNhatBinhLuan($binhLuanId, $noiDung) {
        $sql = "UPDATE binh_luan_bai_giang 
                SET noi_dung = :noi_dung, 
                    thoi_gian_sua = CURRENT_TIMESTAMP 
                WHERE id = :binh_luan_id";
        
        return $this->thucThi($sql, [
            'binh_luan_id' => $binhLuanId,
            'noi_dung' => $noiDung
        ]);
    }
    
    /**
     * Kiểm tra bài giảng có tồn tại không
     */
    public function kiemTraBaiGiangTonTai($baiGiangId) {
        $sql = "SELECT COUNT(*) as tong 
                FROM bai_giang 
                WHERE id = :bai_giang_id";
        
        $result = $this->truyVanMot($sql, ['bai_giang_id' => $baiGiangId]);
        return $result && (int)$result['tong'] > 0;
    }
    
    /**
     * Kiểm tra bình luận cha có tồn tại không
     */
    public function kiemTraBinhLuanChaTonTai($binhLuanChaId) {
        if ($binhLuanChaId === null) {
            return true; // NULL là hợp lệ (bình luận gốc)
        }
        
        $sql = "SELECT COUNT(*) as tong 
                FROM binh_luan_bai_giang 
                WHERE id = :binh_luan_cha_id 
                  AND trang_thai = 'hien_thi'";
        
        $result = $this->truyVanMot($sql, ['binh_luan_cha_id' => $binhLuanChaId]);
        return $result && (int)$result['tong'] > 0;
    }
}
