<?php
/**
 * File: SinhVienRepository.php
 * Mục đích: Xử lý truy vấn database cho sinh viên
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class SinhVienRepository extends BaseRepository {
    
    /**
     * Lấy danh sách sinh viên theo lớp học với phân trang
     */
    public function timTheoLopHocVoiPhanTrang($lopHocId, $offset, $limit) {
        // Lấy tổng số nội dung trong lớp
        $sqlTongNoiDung = "SELECT 
                            (SELECT COUNT(*) FROM bai_giang WHERE lop_hoc_id = :lop_hoc_id1) +
                            (SELECT COUNT(*) FROM bai_tap WHERE lop_hoc_id = :lop_hoc_id2) +
                            (SELECT COUNT(*) FROM bai_kiem_tra WHERE lop_hoc_id = :lop_hoc_id3) AS tong_noi_dung";
        
        $resultTong = $this->truyVanMot($sqlTongNoiDung, [
            'lop_hoc_id1' => $lopHocId,
            'lop_hoc_id2' => $lopHocId,
            'lop_hoc_id3' => $lopHocId
        ]);
        
        $tongNoiDung = (int)$resultTong['tong_noi_dung'];
        
        // Lấy danh sách sinh viên
        $sql = "SELECT 
                    nd.id, nd.ma_nguoi_dung, nd.ho_ten, nd.anh_dai_dien, nd.email,
                    IFNULL(
                        CASE 
                            WHEN :tong_noi_dung > 0 THEN 
                                COUNT(CASE WHEN td.da_hoan_thanh = 1 THEN 1 END) * 100.0 / :tong_noi_dung2
                            ELSE 0
                        END, 0
                    ) AS tien_do,
                    MAX(td.lan_cap_nhat_cuoi) AS last_updated
                FROM sinh_vien_lop_hoc svlh
                JOIN nguoi_dung nd ON svlh.sinh_vien_id = nd.id
                LEFT JOIN tien_do_hoc_tap td ON svlh.sinh_vien_id = td.sinh_vien_id 
                    AND td.lop_hoc_id = :lop_hoc_id
                WHERE svlh.lop_hoc_id = :lop_hoc_id2
                AND svlh.trang_thai = 'dang_hoc'
                GROUP BY nd.id, nd.ma_nguoi_dung, nd.ho_ten, nd.anh_dai_dien, nd.email
                ORDER BY nd.ho_ten ASC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':tong_noi_dung', $tongNoiDung, PDO::PARAM_INT);
        $stmt->bindValue(':tong_noi_dung2', $tongNoiDung, PDO::PARAM_INT);
        $stmt->bindValue(':lop_hoc_id', $lopHocId, PDO::PARAM_INT);
        $stmt->bindValue(':lop_hoc_id2', $lopHocId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số sinh viên trong lớp
     */
    public function demTongSinhVien($lopHocId) {
        $sql = "SELECT COUNT(*) as total
                FROM sinh_vien_lop_hoc svlh
                WHERE svlh.lop_hoc_id = :lop_hoc_id
                AND svlh.trang_thai = 'dang_hoc'";
        
        return $this->dem($sql, ['lop_hoc_id' => $lopHocId]);
    }
}
