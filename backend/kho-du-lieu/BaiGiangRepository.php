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
}
