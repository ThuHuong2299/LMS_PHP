<?php
/**
 * File: BaiGiangRepository.php
 * Mục đích: Xử lý truy vấn database cho bài giảng
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class BaiGiangRepository extends BaseRepository {
    
    /**
     * Lấy thông tin lớp học và danh sách chương với bài giảng
     */
    public function layBaiGiangTheoLop($lopHocId) {
        // Lấy thông tin lớp học
        $sqlLop = "SELECT 
                    lh.id, lh.ma_lop_hoc, lh.ten_lop_hoc, lh.trang_thai,
                    mh.ten_mon_hoc, lh.giang_vien_id
                FROM lop_hoc lh
                JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
                WHERE lh.id = :lop_hoc_id";
        
        $thongTinLop = $this->truyVanMot($sqlLop, ['lop_hoc_id' => $lopHocId]);
        
        if (!$thongTinLop) {
            return null;
        }
        
        // Lấy danh sách chương
        $sqlChuong = "SELECT id, so_thu_tu_chuong, ten_chuong, muc_tieu, thu_tu_sap_xep
                     FROM chuong
                     WHERE lop_hoc_id = :lop_hoc_id
                     ORDER BY thu_tu_sap_xep ASC, so_thu_tu_chuong ASC";
        
        $danhSachChuong = $this->truyVan($sqlChuong, ['lop_hoc_id' => $lopHocId]);
        
        // Lấy bài giảng cho từng chương
        foreach ($danhSachChuong as &$chuong) {
            $sqlBaiGiang = "SELECT 
                            id, so_thu_tu_bai, tieu_de, 
                            duong_dan_video, ngay_tao
                        FROM bai_giang
                        WHERE chuong_id = :chuong_id
                        ORDER BY so_thu_tu_bai ASC";
            
            $chuong['bai_giang'] = $this->truyVan($sqlBaiGiang, ['chuong_id' => $chuong['id']]);
            
            // Chuyển đổi kiểu dữ liệu
            $chuong['id'] = (int)$chuong['id'];
            $chuong['so_thu_tu_chuong'] = (int)$chuong['so_thu_tu_chuong'];
            
            foreach ($chuong['bai_giang'] as &$baiGiang) {
                $baiGiang['id'] = (int)$baiGiang['id'];
                $baiGiang['so_thu_tu_bai'] = (int)$baiGiang['so_thu_tu_bai'];
            }
        }
        
        return [
            'thong_tin_lop' => $thongTinLop,
            'chuong_va_bai_giang' => $danhSachChuong
        ];
    }
    
    /**
     * Lấy tất cả chương của lớp
     */
    public function layAllChuong($lopHocId) {
        $sql = "SELECT id, so_thu_tu_chuong, ten_chuong, muc_tieu, noi_dung
                FROM chuong
                WHERE lop_hoc_id = :lop_hoc_id
                ORDER BY so_thu_tu_chuong ASC";
        
        return $this->truyVan($sql, ['lop_hoc_id' => $lopHocId]);
    }
    
    /**
     * Lấy thống kê chương: tổng video, tổng bài tập, tổng kiểm tra
     */
    public function tinhThongKeChương($chuongId) {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM bai_giang WHERE chuong_id = :chuong_id1) as tong_video,
                    (SELECT COUNT(*) FROM bai_tap WHERE chuong_id = :chuong_id2) as tong_bai_tap,
                    (SELECT COUNT(*) FROM bai_kiem_tra WHERE chuong_id = :chuong_id3) as tong_bai_kiem_tra";
        
        $result = $this->truyVanMot($sql, [
            'chuong_id1' => $chuongId,
            'chuong_id2' => $chuongId,
            'chuong_id3' => $chuongId
        ]);
        return $result ?: [
            'tong_video' => 0,
            'tong_bai_tap' => 0,
            'tong_bai_kiem_tra' => 0
        ];
    }
    
    /**
     * Lấy danh sách bài học trong chương (video + bài tập + kiểm tra)
     * Trả về mảng đã sắp xếp: video theo so_thu_tu_bai, bài tập theo bai_giang_id
     */
    public function layBaiHocChuong($chuongId, $lopHocId) {
        // Video
        $sqlVideo = "SELECT 
                        id, 'video' as loai, so_thu_tu_bai, tieu_de, 
                        duong_dan_video, 
                        CASE 
                            WHEN thoi_luong_giay IS NOT NULL AND thoi_luong_giay > 0 THEN
                                CASE 
                                    WHEN thoi_luong_giay % 60 = 0 THEN CONCAT(FLOOR(thoi_luong_giay / 60), ' phút')
                                    ELSE CONCAT(FLOOR(thoi_luong_giay / 60), 'p', LPAD(thoi_luong_giay % 60, 2, '0'), 's')
                                END
                            ELSE NULL
                        END as thoi_gian_phut,
                        NULL as mo_ta, ngay_tao, NULL as bai_giang_id,
                        NULL as so_cau_hoi,
                        thoi_luong_giay
                    FROM bai_giang
                    WHERE chuong_id = :chuong_id
                    ORDER BY so_thu_tu_bai ASC";
        
        $videoList = $this->truyVan($sqlVideo, ['chuong_id' => $chuongId]);
        
        // Bài tập (có bai_giang_id để nhóm)
        $sqlBaiTap = "SELECT 
                        bt.id, 'bai_tap' as loai, NULL as so_thu_tu_bai, bt.tieu_de,
                        NULL as duong_dan_video, 0 as thoi_gian_phut,
                        NULL as mo_ta, bt.ngay_tao, bt.bai_giang_id,
                        (SELECT COUNT(*) FROM cau_hoi_bai_tap WHERE bai_tap_id = bt.id) as so_cau_hoi,
                        NULL as thoi_luong_giay
                    FROM bai_tap bt
                    WHERE bt.chuong_id = :chuong_id
                    ORDER BY bt.bai_giang_id ASC, bt.id ASC";
        
        $baiTapList = $this->truyVan($sqlBaiTap, ['chuong_id' => $chuongId]);
        
        // Kiểm tra
        $sqlKiemTra = "SELECT 
                        bkt.id, 'bai_kiem_tra' as loai, NULL as so_thu_tu_bai, bkt.tieu_de,
                        NULL as duong_dan_video, 0 as thoi_gian_phut,
                        NULL as mo_ta, bkt.ngay_tao, NULL as bai_giang_id,
                        (SELECT COUNT(*) FROM cau_hoi_trac_nghiem WHERE bai_kiem_tra_id = bkt.id) as so_cau_hoi,
                        bkt.thoi_luong as thoi_luong_giay
                    FROM bai_kiem_tra bkt
                    WHERE bkt.chuong_id = :chuong_id
                    ORDER BY bkt.id ASC";
        
        $kiemTraList = $this->truyVan($sqlKiemTra, ['chuong_id' => $chuongId]);
        
        // Gộp tất cả
        $all = array_merge($videoList, $baiTapList, $kiemTraList);
        
        return $all;
    }
    
    /**
     * Lấy tiến độ của 1 bài
     */
    public function layTienDoBai($baiId, $loai, $sinhVienId) {
        if ($loai === 'video') {
            $sql = "SELECT 
                        CASE 
                            WHEN trang_thai = 'xem_xong' THEN 100
                            WHEN trang_thai = 'dang_xem' THEN 50
                            ELSE 0
                        END as tien_do
                    FROM tien_do_video
                    WHERE bai_giang_id = :bai_id AND sinh_vien_id = :sinh_vien_id";
            
            $result = $this->truyVanMot($sql, [
                'bai_id' => $baiId,
                'sinh_vien_id' => $sinhVienId
            ]);
            
            return $result ? (int)$result['tien_do'] : 0;
        }
        
        if ($loai === 'bai_tap') {
            $sql = "SELECT 
                        CASE 
                            WHEN trang_thai = 'da_nop' THEN 100
                            WHEN trang_thai = 'dang_lam' THEN 50
                            ELSE 0
                        END as tien_do
                    FROM bai_lam
                    WHERE bai_tap_id = :bai_id AND sinh_vien_id = :sinh_vien_id";
            
            $result = $this->truyVanMot($sql, [
                'bai_id' => $baiId,
                'sinh_vien_id' => $sinhVienId
            ]);
            
            return $result ? (int)$result['tien_do'] : 0;
        }
        
        if ($loai === 'bai_kiem_tra') {
            $sql = "SELECT 
                        CASE 
                            WHEN trang_thai IN ('da_nop', 'da_cham') THEN 100
                            WHEN trang_thai = 'dang_lam' THEN 50
                            ELSE 0
                        END as tien_do
                    FROM bai_lam_kiem_tra
                    WHERE bai_kiem_tra_id = :bai_id AND sinh_vien_id = :sinh_vien_id";
            
            $result = $this->truyVanMot($sql, [
                'bai_id' => $baiId,
                'sinh_vien_id' => $sinhVienId
            ]);
            
            return $result ? (int)$result['tien_do'] : 0;
        }
        
        return 0;
    }
    
    /**
     * Tính tiến độ của một chương
     * Logic: Đếm số bài đã hoàn thành trên tổng số bài
     * - Bài giảng: check tien_do_hoc_tap
     * - Bài tập: check bai_lam với trang_thai = 'da_nop'
     * - Bài kiểm tra: check tien_do_hoc_tap hoặc bảng kiểm tra tương ứng
     */
    public function tinhTienDoChuong($chuongId, $sinhVienId) {
        $sql = "SELECT 
                    ((SELECT COUNT(*) FROM bai_giang WHERE chuong_id = :chuong_id1) +
                    (SELECT COUNT(*) FROM bai_tap WHERE chuong_id = :chuong_id2) +
                    (SELECT COUNT(*) FROM bai_kiem_tra WHERE chuong_id = :chuong_id3)) as tong_bai,
                    
                    -- Đếm bài giảng đã xem (từ tien_do_hoc_tap)
                    ((SELECT COUNT(*) FROM tien_do_hoc_tap 
                      WHERE sinh_vien_id = :sinh_vien_id1
                      AND da_hoan_thanh = 1
                      AND loai_noi_dung = 'bai_giang'
                      AND noi_dung_id IN (SELECT id FROM bai_giang WHERE chuong_id = :chuong_id4))
                    +
                    -- Đếm bài tập đã nộp (từ bai_lam)
                    (SELECT COUNT(*) FROM bai_lam 
                     WHERE sinh_vien_id = :sinh_vien_id2
                     AND trang_thai = 'da_nop'
                     AND bai_tap_id IN (SELECT id FROM bai_tap WHERE chuong_id = :chuong_id5))
                    +
                    -- Đếm bài kiểm tra đã hoàn thành (từ tien_do_hoc_tap)
                    (SELECT COUNT(*) FROM tien_do_hoc_tap 
                     WHERE sinh_vien_id = :sinh_vien_id3
                     AND da_hoan_thanh = 1
                     AND loai_noi_dung = 'bai_kiem_tra'
                     AND noi_dung_id IN (SELECT id FROM bai_kiem_tra WHERE chuong_id = :chuong_id6))
                    ) as da_hoan_thanh";
        
        $result = $this->truyVanMot($sql, [
            'chuong_id1' => $chuongId,
            'chuong_id2' => $chuongId,
            'chuong_id3' => $chuongId,
            'chuong_id4' => $chuongId,
            'chuong_id5' => $chuongId,
            'chuong_id6' => $chuongId,
            'sinh_vien_id1' => $sinhVienId,
            'sinh_vien_id2' => $sinhVienId,
            'sinh_vien_id3' => $sinhVienId
        ]);
        
        if (!$result || $result['tong_bai'] == 0) {
            return 0;
        }
        
        return round(($result['da_hoan_thanh'] / $result['tong_bai']) * 100, 1);
    }
    
    /**
     * Lấy thông tin chi tiết một bài giảng
     */
    public function layBaiGiangTheoId($baiGiangId) {
        $sql = "SELECT 
                    bg.id,
                    bg.chuong_id,
                    bg.lop_hoc_id,
                    bg.so_thu_tu_bai,
                    bg.tieu_de,
                    bg.duong_dan_video,
                    bg.ngay_tao,
                    c.ten_chuong,
                    c.so_thu_tu_chuong,
                    lh.ma_lop_hoc,
                    lh.ten_lop_hoc
                FROM bai_giang bg
                JOIN chuong c ON bg.chuong_id = c.id
                JOIN lop_hoc lh ON bg.lop_hoc_id = lh.id
                WHERE bg.id = :bai_giang_id";
        
        return $this->truyVanMot($sql, ['bai_giang_id' => $baiGiangId]);
    }
    
    /**
     * Lấy thông tin chương
     */
    public function layThongTinChuong($chuongId) {
        $sql = "SELECT 
                    id,
                    lop_hoc_id,
                    so_thu_tu_chuong,
                    ten_chuong,
                    muc_tieu,
                    noi_dung,
                    thu_tu_sap_xep,
                    ngay_tao
                FROM chuong
                WHERE id = :chuong_id";
        
        return $this->truyVanMot($sql, ['chuong_id' => $chuongId]);
    }
}