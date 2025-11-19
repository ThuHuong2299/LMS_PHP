<?php
/**
 * File: TienDoHocTapRepository.php
 * Mục đích: Repository xử lý truy vấn dữ liệu tiến độ học tập
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class TienDoHocTapRepository extends BaseRepository {
    
    /**
     * Lấy phần trăm hoàn thành (trung bình) của lớp học cho sinh viên
     * @param int $sinhVienId - ID sinh viên
     * @param int $lopHocId - ID lớp học
     * @return float - Phần trăm hoàn thành (0-100)
     */
    public function getPhanTramHoanThanhLopHoc($sinhVienId, $lopHocId) {
        $sql = "
            SELECT COALESCE(AVG(phan_tram_hoan_thanh), 0) AS phan_tram_trung_binh
            FROM tien_do_hoc_tap
            WHERE sinh_vien_id = :sinh_vien_id 
            AND lop_hoc_id = :lop_hoc_id
        ";
        
        $result = $this->truyVanMot($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
        
        return $result ? (float)$result['phan_tram_trung_binh'] : 0;
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
