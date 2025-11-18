<?php
/**
 * TaiLieuRepository.php
 * 
 * Repository xử lý truy vấn dữ liệu cho tài liệu lớp học
 * Bảng: tai_lieu_lop_hoc
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class TaiLieuRepository extends BaseRepository {
    
    /**
     * Lấy danh sách tài liệu theo lớp học
     * 
     * @param int $lopHocId ID của lớp học
     * @return array Danh sách tài liệu
     */
    public function layTheoLopHoc($lopHocId) {
        $sql = "SELECT 
                    tl.id,
                    tl.lop_hoc_id,
                    tl.ten_tai_lieu,
                    tl.loai_file,
                    tl.duong_dan_file,
                    tl.ten_file_goc,
                    tl.kich_thuoc_file,
                    tl.thoi_gian_upload,
                    tl.nguoi_upload_id,
                    nd.ho_ten as nguoi_upload_ten
                FROM tai_lieu_lop_hoc tl
                LEFT JOIN nguoi_dung nd ON tl.nguoi_upload_id = nd.id
                WHERE tl.lop_hoc_id = :lop_hoc_id
                ORDER BY tl.thoi_gian_upload DESC";
        
        return $this->truyVan($sql, [
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Lấy thông tin chi tiết một tài liệu
     * 
     * @param int $taiLieuId ID của tài liệu
     * @return array|null Thông tin tài liệu hoặc null
     */
    public function layTheoId($taiLieuId) {
        $sql = "SELECT 
                    tl.*,
                    nd.ho_ten as nguoi_upload_ten,
                    lh.ma_lop_hoc,
                    lh.ten_lop_hoc
                FROM tai_lieu_lop_hoc tl
                LEFT JOIN nguoi_dung nd ON tl.nguoi_upload_id = nd.id
                LEFT JOIN lop_hoc lh ON tl.lop_hoc_id = lh.id
                WHERE tl.id = :tai_lieu_id";
        
        return $this->truyVanMot($sql, [
            'tai_lieu_id' => $taiLieuId
        ]);
    }
    
    /**
     * Thêm tài liệu mới
     * 
     * @param array $duLieu Dữ liệu tài liệu cần thêm
     * @return int ID của tài liệu vừa thêm
     */
    public function them($duLieu) {
        $sql = "INSERT INTO tai_lieu_lop_hoc (
                    lop_hoc_id,
                    ten_tai_lieu,
                    loai_file,
                    duong_dan_file,
                    ten_file_goc,
                    kich_thuoc_file,
                    nguoi_upload_id
                ) VALUES (
                    :lop_hoc_id,
                    :ten_tai_lieu,
                    :loai_file,
                    :duong_dan_file,
                    :ten_file_goc,
                    :kich_thuoc_file,
                    :nguoi_upload_id
                )";
        
        $this->thucThi($sql, [
            'lop_hoc_id' => $duLieu['lop_hoc_id'],
            'ten_tai_lieu' => $duLieu['ten_tai_lieu'],
            'loai_file' => $duLieu['loai_file'],
            'duong_dan_file' => $duLieu['duong_dan_file'],
            'ten_file_goc' => $duLieu['ten_file_goc'],
            'kich_thuoc_file' => $duLieu['kich_thuoc_file'],
            'nguoi_upload_id' => $duLieu['nguoi_upload_id']
        ]);
        
        return $this->layIdVuaInsert();
    }
    
    /**
     * Xóa tài liệu
     * 
     * @param int $taiLieuId ID của tài liệu cần xóa
     * @return bool Kết quả xóa
     */
    public function xoa($taiLieuId) {
        $sql = "DELETE FROM tai_lieu_lop_hoc WHERE id = :tai_lieu_id";
        
        return $this->thucThi($sql, [
            'tai_lieu_id' => $taiLieuId
        ]);
    }
    
    /**
     * Đếm số lượng tài liệu của một lớp học
     * 
     * @param int $lopHocId ID của lớp học
     * @return int Số lượng tài liệu
     */
    public function demTheoLopHoc($lopHocId) {
        $sql = "SELECT COUNT(*) as so_luong 
                FROM tai_lieu_lop_hoc 
                WHERE lop_hoc_id = :lop_hoc_id";
        
        $ketQua = $this->truyVanMot($sql, [
            'lop_hoc_id' => $lopHocId
        ]);
        
        return $ketQua ? (int)$ketQua['so_luong'] : 0;
    }
}
