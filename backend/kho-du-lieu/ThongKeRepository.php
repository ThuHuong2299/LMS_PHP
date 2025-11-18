<?php
/**
 * File: ThongKeRepository.php
 * Mục đích: Xử lý truy vấn database cho thống kê
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class ThongKeRepository extends BaseRepository {
    
    /**
     * Đếm số lớp giảng dạy
     */
    public function demSoLopGiangDay($giangVienId) {
        $sql = "SELECT COUNT(*) as total FROM lop_hoc WHERE giang_vien_id = :giang_vien_id";
        return $this->dem($sql, ['giang_vien_id' => $giangVienId]);
    }
    
    /**
     * Đếm số sinh viên theo học
     */
    public function demSinhVienTheoHoc($giangVienId) {
        $sql = "SELECT COUNT(DISTINCT sv.sinh_vien_id) as total
                FROM sinh_vien_lop_hoc sv
                JOIN lop_hoc l ON sv.lop_hoc_id = l.id
                WHERE l.giang_vien_id = :giang_vien_id
                AND sv.trang_thai = 'dang_hoc'";
        return $this->dem($sql, ['giang_vien_id' => $giangVienId]);
    }
    
    /**
     * Đếm bài chờ chấm
     */
    public function demBaiChoCham($giangVienId) {
        $sql = "SELECT COUNT(*) as total
                FROM bai_lam bl
                JOIN bai_tap bt ON bl.bai_tap_id = bt.id
                JOIN lop_hoc l ON bt.lop_hoc_id = l.id
                WHERE l.giang_vien_id = :giang_vien_id
                AND bl.trang_thai = 'da_nop'";
        return $this->dem($sql, ['giang_vien_id' => $giangVienId]);
    }
    
    /**
     * Đếm thông báo mới (24 giờ gần đây)
     */
    public function demThongBaoMoi($giangVienId) {
        $sql = "SELECT COUNT(*) as total
                FROM thong_bao_lop_hoc tb
                JOIN lop_hoc l ON tb.lop_hoc_id = l.id
                WHERE l.giang_vien_id = :giang_vien_id
                AND tb.thoi_gian_gui >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        return $this->dem($sql, ['giang_vien_id' => $giangVienId]);
    }
    
    /**
     * Lấy tất cả thống kê cùng lúc (tối ưu hơn)
     */
    public function layTatCaThongKe($giangVienId) {
        // Có thể tối ưu bằng cách gộp các query thành 1 query duy nhất
        return [
            'lop_giang_day' => $this->demSoLopGiangDay($giangVienId),
            'sinh_vien_theo_hoc' => $this->demSinhVienTheoHoc($giangVienId),
            'bai_cho_cham' => $this->demBaiChoCham($giangVienId),
            'thong_bao_moi' => $this->demThongBaoMoi($giangVienId)
        ];
    }
}
