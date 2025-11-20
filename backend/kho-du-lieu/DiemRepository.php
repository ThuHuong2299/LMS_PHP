<?php
/**
 * File: DiemRepository.php
 * Mục đích: Repository xử lý truy vấn database cho điểm số
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class DiemRepository extends BaseRepository {
    
    /**
     * Lấy danh sách chương của lớp học
     */
    public function layDanhSachChuong($lopHocId) {
        $sql = "SELECT 
                    id,
                    so_thu_tu_chuong,
                    ten_chuong,
                    muc_tieu,
                    thu_tu_sap_xep
                FROM chuong 
                WHERE lop_hoc_id = :lop_hoc_id
                ORDER BY thu_tu_sap_xep ASC, so_thu_tu_chuong ASC";
        
        return $this->truyVan($sql, ['lop_hoc_id' => $lopHocId]);
    }
    
    /**
     * Lấy tiến độ xem video của sinh viên theo chương
     */
    public function layTienDoVideoChuong($chuongId, $sinhVienId) {
        $sql = "SELECT 
                    COUNT(bg.id) as tong_video,
                    COUNT(CASE WHEN tdv.trang_thai = 'xem_xong' THEN 1 END) as video_xem_xong,
                    COUNT(CASE WHEN tdv.phan_tram_hoan_thanh >= 80 THEN 1 END) as video_hoan_thanh_80
                FROM bai_giang bg
                LEFT JOIN tien_do_video tdv ON bg.id = tdv.bai_giang_id 
                    AND tdv.sinh_vien_id = :sinh_vien_id
                WHERE bg.chuong_id = :chuong_id";
        
        $result = $this->truyVanMot($sql, [
            'chuong_id' => $chuongId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        return $result ?: ['tong_video' => 0, 'video_xem_xong' => 0, 'video_hoan_thanh_80' => 0];
    }
    
    /**
     * Lấy danh sách bài tập của chương với điểm
     */
    public function layDiemBaiTapChuong($chuongId, $sinhVienId) {
        $sql = "SELECT 
                    bt.id,
                    bt.tieu_de,
                    bt.diem_toi_da,
                    bl.diem,
                    bl.trang_thai,
                    bl.thoi_gian_nop
                FROM bai_tap bt
                LEFT JOIN bai_lam bl ON bt.id = bl.bai_tap_id 
                    AND bl.sinh_vien_id = :sinh_vien_id
                WHERE bt.chuong_id = :chuong_id
                ORDER BY bt.tieu_de ASC";
        
        return $this->truyVan($sql, [
            'chuong_id' => $chuongId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Lấy điểm kiểm tra cuối chương
     */
    public function layDiemKiemTraChuong($chuongId, $sinhVienId) {
        $sql = "SELECT 
                    bkt.id,
                    bkt.tieu_de,
                    bkt.diem_toi_da,
                    bkt.thoi_luong,
                    blkt.diem,
                    blkt.trang_thai,
                    blkt.so_cau_dung,
                    blkt.tong_so_cau,
                    blkt.thoi_gian_lam_bai,
                    blkt.thoi_gian_nop
                FROM bai_kiem_tra bkt
                LEFT JOIN bai_lam_kiem_tra blkt ON bkt.id = blkt.bai_kiem_tra_id 
                    AND blkt.sinh_vien_id = :sinh_vien_id
                WHERE bkt.chuong_id = :chuong_id
                ORDER BY bkt.thoi_gian_bat_dau ASC";
        
        return $this->truyVan($sql, [
            'chuong_id' => $chuongId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Lấy tổng quan điểm của sinh viên trong lớp
     */
    public function layTongQuanDiem($lopHocId, $sinhVienId) {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM chuong WHERE lop_hoc_id = :lop_hoc_id) as tong_chuong,
                    (SELECT COUNT(DISTINCT bg.chuong_id) 
                     FROM bai_giang bg
                     JOIN tien_do_video tdv ON bg.id = tdv.bai_giang_id
                     WHERE bg.lop_hoc_id = :lop_hoc_id 
                       AND tdv.sinh_vien_id = :sinh_vien_id
                       AND tdv.trang_thai = 'xem_xong') as chuong_hoan_thanh,
                    (SELECT COUNT(*) 
                     FROM bai_giang 
                     WHERE lop_hoc_id = :lop_hoc_id) as tong_video,
                    (SELECT COUNT(*) 
                     FROM bai_giang bg
                     JOIN tien_do_video tdv ON bg.id = tdv.bai_giang_id
                     WHERE bg.lop_hoc_id = :lop_hoc_id 
                       AND tdv.sinh_vien_id = :sinh_vien_id
                       AND tdv.trang_thai = 'xem_xong') as video_da_xem";
        
        $result = $this->truyVanMot($sql, [
            'lop_hoc_id' => $lopHocId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        return $result ?: [
            'tong_chuong' => 0, 
            'chuong_hoan_thanh' => 0,
            'tong_video' => 0,
            'video_da_xem' => 0
        ];
    }
}
