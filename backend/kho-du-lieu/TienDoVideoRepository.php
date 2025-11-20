<?php
/**
 * File: TienDoVideoRepository.php
 * Mục đích: Repository xử lý truy vấn database cho tiến độ video
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class TienDoVideoRepository extends BaseRepository {
    
    /**
     * Lấy tiến độ video của sinh viên
     */
    public function layTienDoVideo($baiGiangId, $sinhVienId) {
        $sql = "SELECT 
                    id,
                    bai_giang_id,
                    sinh_vien_id,
                    trang_thai,
                    thoi_gian_xem,
                    phan_tram_hoan_thanh,
                    lan_xem_cuoi,
                    ngay_tao
                FROM tien_do_video
                WHERE bai_giang_id = :bai_giang_id 
                  AND sinh_vien_id = :sinh_vien_id";
        
        return $this->truyVanMot($sql, [
            'bai_giang_id' => $baiGiangId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Tạo hoặc cập nhật tiến độ video
     */
    public function capNhatTienDoVideo($data) {
        $sql = "INSERT INTO tien_do_video 
                (bai_giang_id, sinh_vien_id, trang_thai, thoi_gian_xem, phan_tram_hoan_thanh, lan_xem_cuoi)
                VALUES 
                (:bai_giang_id, :sinh_vien_id, :trang_thai, :thoi_gian_xem, :phan_tram_hoan_thanh, CURRENT_TIMESTAMP)
                ON DUPLICATE KEY UPDATE
                    trang_thai = VALUES(trang_thai),
                    thoi_gian_xem = VALUES(thoi_gian_xem),
                    phan_tram_hoan_thanh = VALUES(phan_tram_hoan_thanh),
                    lan_xem_cuoi = CURRENT_TIMESTAMP";
        
        return $this->thucThi($sql, $data);
    }
    
    /**
     * Đánh dấu video đã xem xong
     */
    public function danhDauXemXong($baiGiangId, $sinhVienId) {
        $sql = "INSERT INTO tien_do_video 
                (bai_giang_id, sinh_vien_id, trang_thai, phan_tram_hoan_thanh, lan_xem_cuoi)
                VALUES 
                (:bai_giang_id, :sinh_vien_id, 'xem_xong', 100.00, CURRENT_TIMESTAMP)
                ON DUPLICATE KEY UPDATE
                    trang_thai = 'xem_xong',
                    phan_tram_hoan_thanh = 100.00,
                    lan_xem_cuoi = CURRENT_TIMESTAMP";
        
        return $this->thucThi($sql, [
            'bai_giang_id' => $baiGiangId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Lấy danh sách tiến độ của sinh viên trong một lớp
     */
    public function layTienDoTheoLop($sinhVienId, $lopHocId) {
        $sql = "SELECT 
                    tdv.id,
                    tdv.bai_giang_id,
                    tdv.trang_thai,
                    tdv.thoi_gian_xem,
                    tdv.phan_tram_hoan_thanh,
                    tdv.lan_xem_cuoi,
                    bg.tieu_de AS ten_bai_giang,
                    bg.so_thu_tu_bai,
                    c.ten_chuong
                FROM tien_do_video tdv
                JOIN bai_giang bg ON tdv.bai_giang_id = bg.id
                JOIN chuong c ON bg.chuong_id = c.id
                WHERE tdv.sinh_vien_id = :sinh_vien_id
                  AND bg.lop_hoc_id = :lop_hoc_id
                ORDER BY bg.so_thu_tu_bai ASC";
        
        return $this->truyVan($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Đếm số video đã xem trong chương
     */
    public function demVideoXemTrongChuong($sinhVienId, $chuongId) {
        $sql = "SELECT 
                    COUNT(*) as tong_video,
                    SUM(CASE WHEN tdv.trang_thai = 'xem_xong' THEN 1 ELSE 0 END) as video_hoan_thanh,
                    AVG(tdv.phan_tram_hoan_thanh) as phan_tram_trung_binh
                FROM bai_giang bg
                LEFT JOIN tien_do_video tdv 
                    ON bg.id = tdv.bai_giang_id 
                    AND tdv.sinh_vien_id = :sinh_vien_id
                WHERE bg.chuong_id = :chuong_id";
        
        return $this->truyVanMot($sql, [
            'sinh_vien_id' => $sinhVienId,
            'chuong_id' => $chuongId
        ]);
    }
    
    /**
     * Tính phần trăm hoàn thành lớp học
     */
    public function tinhPhanTramLop($sinhVienId, $lopHocId) {
        $sql = "SELECT 
                    COUNT(bg.id) as tong_video,
                    SUM(CASE WHEN tdv.trang_thai = 'xem_xong' THEN 1 ELSE 0 END) as video_hoan_thanh
                FROM bai_giang bg
                LEFT JOIN tien_do_video tdv 
                    ON bg.id = tdv.bai_giang_id 
                    AND tdv.sinh_vien_id = :sinh_vien_id
                WHERE bg.lop_hoc_id = :lop_hoc_id";
        
        $result = $this->truyVanMot($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
        
        if (!$result || $result['tong_video'] == 0) {
            return 0;
        }
        
        $videoHoanThanh = $result['video_hoan_thanh'] ?? 0;
        return round(($videoHoanThanh / $result['tong_video']) * 100, 2);
    }
    
    /**
     * Kiểm tra xem sinh viên đã xem video chưa
     */
    public function kiemTraDaXem($baiGiangId, $sinhVienId) {
        $sql = "SELECT COUNT(*) as tong 
                FROM tien_do_video 
                WHERE bai_giang_id = :bai_giang_id 
                  AND sinh_vien_id = :sinh_vien_id
                  AND trang_thai IN ('dang_xem', 'xem_xong')";
        
        $result = $this->truyVanMot($sql, [
            'bai_giang_id' => $baiGiangId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        return $result && (int)$result['tong'] > 0;
    }
    
    /**
     * Lấy danh sách video chưa xem trong lớp
     */
    public function layVideoChuaXem($sinhVienId, $lopHocId) {
        $sql = "SELECT 
                    bg.id,
                    bg.tieu_de,
                    bg.so_thu_tu_bai,
                    c.ten_chuong
                FROM bai_giang bg
                JOIN chuong c ON bg.chuong_id = c.id
                LEFT JOIN tien_do_video tdv 
                    ON bg.id = tdv.bai_giang_id 
                    AND tdv.sinh_vien_id = :sinh_vien_id
                WHERE bg.lop_hoc_id = :lop_hoc_id
                  AND (tdv.id IS NULL OR tdv.trang_thai = 'chua_xem')
                ORDER BY bg.so_thu_tu_bai ASC";
        
        return $this->truyVan($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Xóa tiến độ video (nếu cần reset)
     */
    public function xoaTienDoVideo($baiGiangId, $sinhVienId) {
        $sql = "DELETE FROM tien_do_video 
                WHERE bai_giang_id = :bai_giang_id 
                  AND sinh_vien_id = :sinh_vien_id";
        
        return $this->thucThi($sql, [
            'bai_giang_id' => $baiGiangId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
}
