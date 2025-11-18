<?php
/**
 * File: LopHocRepository.php
 * Mục đích: Xử lý truy vấn database cho lớp học
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';
require_once __DIR__ . '/../mau/LopHoc.php';

class LopHocRepository extends BaseRepository {
    
    /**
     * Lấy danh sách lớp học của giảng viên
     */
    public function timTheoGiangVien($giangVienId) {
        $sql = "SELECT 
                    lh.id,
                    lh.ma_lop_hoc,
                    lh.ten_lop_hoc,
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
        
        return $this->truyVan($sql, ['giang_vien_id' => $giangVienId]);
    }
    
    /**
     * Kiểm tra giảng viên có quyền truy cập lớp học không
     */
    public function kiemTraQuyenTruyCap($lopHocId, $giangVienId) {
        $sql = "SELECT giang_vien_id FROM lop_hoc WHERE id = :lop_hoc_id";
        $result = $this->truyVanMot($sql, ['lop_hoc_id' => $lopHocId]);
        
        if (!$result) {
            return false;
        }
        
        return (int)$result['giang_vien_id'] === (int)$giangVienId;
    }
    
    /**
     * Lấy thông tin lớp học theo ID
     */
    public function timTheoId($lopHocId) {
        $sql = "SELECT 
                    lh.id,
                    lh.ma_lop_hoc,
                    lh.ten_lop_hoc,
                    lh.trang_thai,
                    mh.ten_mon_hoc,
                    lh.giang_vien_id
                FROM lop_hoc lh
                JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
                WHERE lh.id = :lop_hoc_id";
        
        return $this->truyVanMot($sql, ['lop_hoc_id' => $lopHocId]);
    }
}
