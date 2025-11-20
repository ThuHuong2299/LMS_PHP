<?php
/**
 * File: TienDoHocTapRepository.php
 * Mục đích: Repository xử lý truy vấn dữ liệu tiến độ học tập
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class TienDoHocTapRepository extends BaseRepository {
    
    /**
     * Lấy phần trăm hoàn thành (trung bình) của lớp học cho sinh viên
     * Tính dựa trên: video đã xem, bài tập đã nộp, bài kiểm tra đã làm
     * @param int $sinhVienId - ID sinh viên
     * @param int $lopHocId - ID lớp học
     * @return float - Phần trăm hoàn thành (0-100)
     */
    public function getPhanTramHoanThanhLopHoc($sinhVienId, $lopHocId) {
        $sql = "
            SELECT 
                -- Tổng số video trong lớp
                (SELECT COUNT(*) FROM bai_giang WHERE lop_hoc_id = :lop_hoc_id1) AS tong_video,
                -- Số video đã xem xong
                (SELECT COUNT(*) FROM tien_do_video tdv
                 JOIN bai_giang bg ON tdv.bai_giang_id = bg.id
                 WHERE tdv.sinh_vien_id = :sinh_vien_id1 
                 AND bg.lop_hoc_id = :lop_hoc_id2
                 AND tdv.trang_thai = 'xem_xong') AS video_xem_xong,
                -- Tổng số bài tập
                (SELECT COUNT(*) FROM bai_tap WHERE lop_hoc_id = :lop_hoc_id3) AS tong_bai_tap,
                -- Số bài tập đã nộp
                (SELECT COUNT(*) FROM bai_lam bl
                 JOIN bai_tap bt ON bl.bai_tap_id = bt.id
                 WHERE bl.sinh_vien_id = :sinh_vien_id2
                 AND bt.lop_hoc_id = :lop_hoc_id4
                 AND bl.trang_thai = 'da_nop') AS bai_tap_da_nop,
                -- Tổng số bài kiểm tra
                (SELECT COUNT(*) FROM bai_kiem_tra WHERE lop_hoc_id = :lop_hoc_id5) AS tong_bai_kiem_tra,
                -- Số bài kiểm tra đã làm
                (SELECT COUNT(DISTINCT bai_kiem_tra_id) FROM bai_lam_kiem_tra blkt
                 JOIN bai_kiem_tra bkt ON blkt.bai_kiem_tra_id = bkt.id
                 WHERE blkt.sinh_vien_id = :sinh_vien_id3
                 AND bkt.lop_hoc_id = :lop_hoc_id6
                 AND blkt.trang_thai = 'da_nop') AS bai_kiem_tra_da_lam
        ";
        
        $result = $this->truyVanMot($sql, [
            'sinh_vien_id1' => $sinhVienId,
            'sinh_vien_id2' => $sinhVienId,
            'sinh_vien_id3' => $sinhVienId,
            'lop_hoc_id1' => $lopHocId,
            'lop_hoc_id2' => $lopHocId,
            'lop_hoc_id3' => $lopHocId,
            'lop_hoc_id4' => $lopHocId,
            'lop_hoc_id5' => $lopHocId,
            'lop_hoc_id6' => $lopHocId
        ]);
        
        if (!$result) return 0;
        
        // Tính tổng số items và items đã hoàn thành
        $tongSo = $result['tong_video'] + $result['tong_bai_tap'] + $result['tong_bai_kiem_tra'];
        $daHoanThanh = $result['video_xem_xong'] + $result['bai_tap_da_nop'] + $result['bai_kiem_tra_da_lam'];
        
        if ($tongSo == 0) return 0;
        
        return round(($daHoanThanh / $tongSo) * 100, 2);
    }
    
    /**
     * Lấy tất cả tiến độ của sinh viên trong lớp
     * @param int $sinhVienId
     * @param int $lopHocId
     * @return array
     */
    public function getTienDoChiTiet($sinhVienId, $lopHocId) {
        $sql = "
            SELECT 
                id,
                loai_noi_dung,
                noi_dung_id,
                da_hoan_thanh,
                phan_tram_hoan_thanh,
                lan_cap_nhat_cuoi
            FROM tien_do_hoc_tap
            WHERE sinh_vien_id = :sinh_vien_id 
            AND lop_hoc_id = :lop_hoc_id
            ORDER BY lan_cap_nhat_cuoi DESC
        ";
        
        return $this->truyVan($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Cập nhật tiến độ học tập
     * @param int $sinhVienId
     * @param int $lopHocId
     * @param string $loaiNoiDung - 'bai_giang', 'bai_tap', 'bai_kiem_tra'
     * @param int $noiDungId - ID của nội dung
     * @param bool $daHoanThanh
     * @param float $phanTramHoanThanh
     * @return bool
     */
    public function capNhatTienDo($sinhVienId, $lopHocId, $loaiNoiDung, $noiDungId, $daHoanThanh, $phanTramHoanThanh = 0) {
        // Kiểm tra xem có record này chưa
        $sql = "
            SELECT id FROM tien_do_hoc_tap
            WHERE sinh_vien_id = :sinh_vien_id 
            AND lop_hoc_id = :lop_hoc_id
            AND loai_noi_dung = :loai_noi_dung
            AND noi_dung_id = :noi_dung_id
            LIMIT 1
        ";
        
        $existing = $this->truyVanMot($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId,
            'loai_noi_dung' => $loaiNoiDung,
            'noi_dung_id' => $noiDungId
        ]);
        
        if ($existing) {
            // Update
            $updateSql = "
                UPDATE tien_do_hoc_tap
                SET da_hoan_thanh = :da_hoan_thanh,
                    phan_tram_hoan_thanh = :phan_tram_hoan_thanh,
                    lan_cap_nhat_cuoi = CURRENT_TIMESTAMP
                WHERE sinh_vien_id = :sinh_vien_id 
                AND lop_hoc_id = :lop_hoc_id
                AND loai_noi_dung = :loai_noi_dung
                AND noi_dung_id = :noi_dung_id
            ";
            
            return $this->thucThi($updateSql, [
                'sinh_vien_id' => $sinhVienId,
                'lop_hoc_id' => $lopHocId,
                'loai_noi_dung' => $loaiNoiDung,
                'noi_dung_id' => $noiDungId,
                'da_hoan_thanh' => $daHoanThanh ? 1 : 0,
                'phan_tram_hoan_thanh' => $phanTramHoanThanh
            ]);
        } else {
            // Insert
            $insertSql = "
                INSERT INTO tien_do_hoc_tap 
                (sinh_vien_id, lop_hoc_id, loai_noi_dung, noi_dung_id, da_hoan_thanh, phan_tram_hoan_thanh)
                VALUES 
                (:sinh_vien_id, :lop_hoc_id, :loai_noi_dung, :noi_dung_id, :da_hoan_thanh, :phan_tram_hoan_thanh)
            ";
            
            return $this->thucThi($insertSql, [
                'sinh_vien_id' => $sinhVienId,
                'lop_hoc_id' => $lopHocId,
                'loai_noi_dung' => $loaiNoiDung,
                'noi_dung_id' => $noiDungId,
                'da_hoan_thanh' => $daHoanThanh ? 1 : 0,
                'phan_tram_hoan_thanh' => $phanTramHoanThanh
            ]);
        }
    }
}
