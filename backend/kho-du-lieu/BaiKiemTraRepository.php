<?php
/**
 * File: BaiKiemTraRepository.php
 * Mục đích: Xử lý truy vấn database cho bài kiểm tra
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class BaiKiemTraRepository extends BaseRepository {
    
    /**
     * Lấy danh sách bài kiểm tra theo lớp học
     */
    public function timTheoLopHoc($lopHocId) {
        $sql = "SELECT 
                    bkt.id, bkt.tieu_de, bkt.mo_ta, 
                    bkt.thoi_luong, bkt.thoi_gian_bat_dau, bkt.thoi_gian_ket_thuc,
                    bkt.diem_toi_da, bkt.chuong_id, bkt.ngay_tao,
                    -- Thông tin chương (nếu có)
                    c.so_thu_tu_chuong, c.ten_chuong,
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
        
        return $this->truyVan($sql, ['lop_hoc_id' => $lopHocId]);
    }
    
    /**
     * Đếm tổng số sinh viên trong lớp
     */
    public function demTongSinhVien($lopHocId) {
        $sql = "SELECT COUNT(DISTINCT sinh_vien_id) as total
                FROM sinh_vien_lop_hoc
                WHERE lop_hoc_id = :lop_hoc_id
                AND trang_thai = 'dang_hoc'";
        
        return $this->dem($sql, ['lop_hoc_id' => $lopHocId]);
    }
}
