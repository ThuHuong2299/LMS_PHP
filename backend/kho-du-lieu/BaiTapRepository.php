<?php
/**
 * File: BaiTapRepository.php
 * Mục đích: Xử lý truy vấn database cho bài tập
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class BaiTapRepository extends BaseRepository {
    
    /**
     * Lấy danh sách bài tập theo lớp học
     */
    public function timTheoLopHoc($lopHocId) {
        $sql = "SELECT 
                    bt.id, bt.tieu_de, bt.mo_ta, bt.han_nop, bt.diem_toi_da,
                    bt.chuong_id, bt.bai_giang_id, bt.ngay_tao,
                    -- Thông tin chương
                    c.so_thu_tu_chuong, c.ten_chuong,
                    -- Thông tin bài giảng (nếu có)
                    bg.so_thu_tu_bai, bg.tieu_de AS ten_bai_giang,
                    -- Số câu hỏi
                    (SELECT COUNT(*) FROM cau_hoi_bai_tap WHERE bai_tap_id = bt.id) AS so_cau_hoi,
                    -- Số sinh viên đã nộp
                    (SELECT COUNT(DISTINCT bl.sinh_vien_id) 
                     FROM bai_lam bl 
                     WHERE bl.bai_tap_id = bt.id 
                     AND bl.trang_thai IN ('da_nop', 'da_cham')) AS so_sinh_vien_da_nop
                FROM bai_tap bt
                LEFT JOIN chuong c ON bt.chuong_id = c.id
                LEFT JOIN bai_giang bg ON bt.bai_giang_id = bg.id
                WHERE bt.lop_hoc_id = :lop_hoc_id
                ORDER BY bt.ngay_tao DESC, bt.han_nop DESC";
        
        return $this->truyVan($sql, ['lop_hoc_id' => $lopHocId]);
    }
    
    /**
     * Đếm tổng số sinh viên trong lớp
     */
    public function demTongSinhVien($lopHocId) {
        $sql = "SELECT COUNT(DISTINCT sinh_vien_id) as total
                FROM sinh_vien_lop_hoc
                WHERE lop_hoc_id = :lop_hoc_id
                AND trang_thai = 'dang_hoc'";
        
        return $this->dem($sql, ['lop_hoc_id' => $lopHocId]);
    }
    
    /**
     * Lấy thông tin bài tập theo ID
     */
    public function timTheoId($baiTapId) {
        $sql = "SELECT 
                    bt.id, bt.tieu_de, bt.mo_ta, bt.han_nop, bt.diem_toi_da,
                    bt.lop_hoc_id, bt.ngay_tao,
                    lh.ten_lop_hoc, lh.giang_vien_id
                FROM bai_tap bt
                JOIN lop_hoc lh ON bt.lop_hoc_id = lh.id
                WHERE bt.id = :bai_tap_id";
        
        $result = $this->truyVan($sql, ['bai_tap_id' => $baiTapId]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Lấy thống kê bài tập
     */
    public function layThongKeBaiTap($baiTapId) {
        $sql = "SELECT 
                    COUNT(CASE WHEN bl.trang_thai = 'da_nop' THEN 1 END) AS so_bai_chua_cham,
                    COUNT(CASE WHEN bl.trang_thai = 'da_cham' THEN 1 END) AS so_bai_da_cham,
                    AVG(CASE WHEN bl.trang_thai = 'da_cham' AND bl.diem IS NOT NULL THEN bl.diem END) AS diem_trung_binh,
                    MAX(CASE WHEN bl.trang_thai = 'da_cham' AND bl.diem IS NOT NULL THEN bl.diem END) AS diem_cao_nhat,
                    MIN(CASE WHEN bl.trang_thai = 'da_cham' AND bl.diem IS NOT NULL THEN bl.diem END) AS diem_thap_nhat,
                    COUNT(CASE WHEN bl.trang_thai IN ('da_nop', 'da_cham') THEN 1 END) AS so_bai_da_nop
                FROM bai_lam bl
                WHERE bl.bai_tap_id = :bai_tap_id";
        
        $result = $this->truyVan($sql, ['bai_tap_id' => $baiTapId]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Lấy danh sách sinh viên làm bài (bao gồm cả sinh viên chưa làm)
     */
    public function layDanhSachSinhVienLamBai($baiTapId, $lopHocId) {
        $sql = "SELECT 
                    sv.id AS sinh_vien_id,
                    sv.ho_ten,
                    sv.ma_nguoi_dung AS ma_sinh_vien,
                    sv.anh_dai_dien,
                    COALESCE(bl.trang_thai, 'chua_lam') AS trang_thai,
                    bl.diem,
                    bl.thoi_gian_nop,
                    bl.thoi_gian_bat_dau,
                    CASE 
                        WHEN bl.trang_thai = 'da_cham' THEN 'da_cham'
                        WHEN bl.trang_thai = 'da_nop' THEN 'chua_cham'
                        ELSE 'chua_lam'
                    END AS trang_thai_cham
                FROM nguoi_dung sv
                JOIN sinh_vien_lop_hoc svlh ON sv.id = svlh.sinh_vien_id
                LEFT JOIN bai_lam bl ON sv.id = bl.sinh_vien_id AND bl.bai_tap_id = :bai_tap_id
                WHERE svlh.lop_hoc_id = :lop_hoc_id
                AND svlh.trang_thai = 'dang_hoc'
                AND sv.vai_tro = 'sinh_vien'
                ORDER BY sv.ho_ten ASC";
        
        return $this->truyVan($sql, [
            'bai_tap_id' => $baiTapId,
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Lấy chi tiết bài làm của sinh viên
     */
    public function layChiTietBaiLam($baiTapId, $sinhVienId) {
        $sql = "SELECT 
                    bl.id AS bai_lam_id,
                    bl.trang_thai,
                    bl.diem AS tong_diem,
                    bl.thoi_gian_bat_dau,
                    bl.thoi_gian_nop,
                    bl.thoi_gian_cham,
                    -- Thông tin sinh viên
                    sv.id AS sinh_vien_id,
                    sv.ho_ten AS sinh_vien_ho_ten,
                    sv.ma_nguoi_dung AS sinh_vien_ma,
                    sv.anh_dai_dien AS sinh_vien_anh,
                    -- Thông tin bài tập
                    bt.id AS bai_tap_id,
                    bt.tieu_de AS bai_tap_tieu_de,
                    bt.mo_ta AS bai_tap_mo_ta,
                    bt.han_nop,
                    bt.diem_toi_da
                FROM bai_lam bl
                JOIN nguoi_dung sv ON bl.sinh_vien_id = sv.id
                JOIN bai_tap bt ON bl.bai_tap_id = bt.id
                WHERE bl.bai_tap_id = :bai_tap_id
                AND bl.sinh_vien_id = :sinh_vien_id";
        
        $result = $this->truyVan($sql, [
            'bai_tap_id' => $baiTapId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Lấy danh sách câu hỏi và câu trả lời
     */
    public function layCauHoiVaTraLoi($baiTapId, $baiLamId) {
        $sql = "SELECT 
                    ch.id AS cau_hoi_id,
                    ch.noi_dung_cau_hoi,
                    ch.mo_ta AS cau_hoi_mo_ta,
                    ch.diem AS diem_toi_da,
                    tl.id AS tra_loi_id,
                    tl.noi_dung_tra_loi,
                    tl.diem AS diem_dat_duoc,
                    tl.thoi_gian_tra_loi
                FROM cau_hoi_bai_tap ch
                LEFT JOIN tra_loi_bai_tap tl ON ch.id = tl.cau_hoi_id AND tl.bai_lam_id = :bai_lam_id
                WHERE ch.bai_tap_id = :bai_tap_id
                ORDER BY ch.id ASC";
        
        return $this->truyVan($sql, [
            'bai_tap_id' => $baiTapId,
            'bai_lam_id' => $baiLamId
        ]);
    }
    
    /**
     * Lấy danh sách bình luận (chung hoặc theo câu hỏi)
     */
    public function layBinhLuan($baiLamId, $cauHoiId = null) {
        $sql = "SELECT 
                    bl.id,
                    bl.noi_dung,
                    bl.thoi_gian_gui,
                    bl.cau_hoi_id,
                    ng.id AS nguoi_gui_id,
                    ng.ho_ten AS nguoi_gui_ten,
                    ng.anh_dai_dien AS nguoi_gui_anh,
                    ng.vai_tro AS nguoi_gui_vai_tro
                FROM binh_luan_bai_tap bl
                JOIN nguoi_dung ng ON bl.nguoi_gui_id = ng.id
                WHERE bl.bai_lam_id = :bai_lam_id";
        
        $params = ['bai_lam_id' => $baiLamId];
        
        if ($cauHoiId !== null) {
            $sql .= " AND bl.cau_hoi_id = :cau_hoi_id";
            $params['cau_hoi_id'] = $cauHoiId;
        } else {
            $sql .= " AND bl.cau_hoi_id IS NULL";
        }
        
        $sql .= " ORDER BY bl.thoi_gian_gui ASC";
        
        return $this->truyVan($sql, $params);
    }
    
    /**
     * Thêm bình luận
     */
    public function themBinhLuan($baiLamId, $nguoiGuiId, $noiDung, $cauHoiId = null) {
        $sql = "INSERT INTO binh_luan_bai_tap 
                (bai_lam_id, nguoi_gui_id, noi_dung, cau_hoi_id, thoi_gian_gui)
                VALUES 
                (:bai_lam_id, :nguoi_gui_id, :noi_dung, :cau_hoi_id, NOW())";
        
        $result = $this->thucThi($sql, [
            'bai_lam_id' => $baiLamId,
            'nguoi_gui_id' => $nguoiGuiId,
            'noi_dung' => $noiDung,
            'cau_hoi_id' => $cauHoiId
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Lấy thông tin sinh viên theo ID
     */
    public function layThongTinSinhVien($sinhVienId) {
        $sql = "SELECT id, ho_ten, ma_nguoi_dung, anh_dai_dien 
                FROM nguoi_dung 
                WHERE id = :sinh_vien_id AND vai_tro = 'sinh_vien'";
        
        return $this->truyVanMot($sql, ['sinh_vien_id' => $sinhVienId]);
    }
    
    // ========================================
    // METHODS CHO SINH VIÊN LÀM BÀI TẬP
    // ========================================
    
    /**
     * Lấy chi tiết bài tập (dùng cho sinh viên)
     */
    public function layChiTietBaiTap($baiTapId) {
        $sql = "SELECT id, lop_hoc_id, chuong_id, bai_giang_id, tieu_de, mo_ta, 
                       han_nop, diem_toi_da, ngay_tao
                FROM bai_tap
                WHERE id = :bai_tap_id";
        
        return $this->truyVanMot($sql, ['bai_tap_id' => $baiTapId]);
    }
    
    /**
     * Lấy danh sách câu hỏi của bài tập
     */
    public function layCauHoiBaiTap($baiTapId) {
        $sql = "SELECT id, bai_tap_id, noi_dung_cau_hoi, mo_ta, diem
                FROM cau_hoi_bai_tap
                WHERE bai_tap_id = :bai_tap_id
                ORDER BY id ASC";
        
        return $this->truyVan($sql, ['bai_tap_id' => $baiTapId]);
    }
    
    /**
     * Lấy bài làm của sinh viên cho bài tập cụ thể
     */
    public function layBaiLamSinhVien($baiTapId, $sinhVienId) {
        $sql = "SELECT id, bai_tap_id, sinh_vien_id, trang_thai, diem,
                       thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id
                FROM bai_lam
                WHERE bai_tap_id = :bai_tap_id 
                  AND sinh_vien_id = :sinh_vien_id";
        
        return $this->truyVanMot($sql, [
            'bai_tap_id' => $baiTapId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Tạo bài làm mới cho sinh viên
     */
    public function taoBaiLam($baiTapId, $sinhVienId) {
        $sql = "INSERT INTO bai_lam 
                (bai_tap_id, sinh_vien_id, trang_thai, thoi_gian_bat_dau)
                VALUES 
                (:bai_tap_id, :sinh_vien_id, 'dang_lam', NOW())";
        
        $result = $this->thucThi($sql, [
            'bai_tap_id' => $baiTapId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Lấy tất cả câu trả lời đã lưu của sinh viên
     */
    public function layTraLoiDaLuu($baiLamId) {
        $sql = "SELECT tl.id, tl.bai_lam_id, tl.cau_hoi_id, tl.noi_dung_tra_loi, 
                       tl.diem, tl.thoi_gian_tra_loi
                FROM tra_loi_bai_tap tl
                WHERE tl.bai_lam_id = :bai_lam_id";
        
        return $this->truyVan($sql, ['bai_lam_id' => $baiLamId]);
    }
    
    /**
     * Kiểm tra câu trả lời đã tồn tại chưa
     */
    public function kiemTraTraLoiTonTai($baiLamId, $cauHoiId) {
        $sql = "SELECT id FROM tra_loi_bai_tap
                WHERE bai_lam_id = :bai_lam_id 
                  AND cau_hoi_id = :cau_hoi_id";
        
        $result = $this->truyVanMot($sql, [
            'bai_lam_id' => $baiLamId,
            'cau_hoi_id' => $cauHoiId
        ]);
        
        return $result ? $result['id'] : null;
    }
    
    /**
     * Thêm câu trả lời mới
     */
    public function themTraLoi($baiLamId, $cauHoiId, $noiDungTraLoi) {
        $sql = "INSERT INTO tra_loi_bai_tap 
                (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, thoi_gian_tra_loi)
                VALUES 
                (:bai_lam_id, :cau_hoi_id, :noi_dung_tra_loi, NOW())";
        
        return $this->thucThi($sql, [
            'bai_lam_id' => $baiLamId,
            'cau_hoi_id' => $cauHoiId,
            'noi_dung_tra_loi' => $noiDungTraLoi
        ]);
    }
    
    /**
     * Cập nhật câu trả lời đã có
     */
    public function capNhatTraLoi($traLoiId, $noiDungTraLoi) {
        $sql = "UPDATE tra_loi_bai_tap 
                SET noi_dung_tra_loi = :noi_dung_tra_loi,
                    thoi_gian_tra_loi = NOW()
                WHERE id = :tra_loi_id";
        
        return $this->thucThi($sql, [
            'tra_loi_id' => $traLoiId,
            'noi_dung_tra_loi' => $noiDungTraLoi
        ]);
    }
    
    /**
     * Lưu hoặc cập nhật câu trả lời (upsert)
     */
    public function luuTraLoi($baiLamId, $cauHoiId, $noiDungTraLoi) {
        // Kiểm tra đã tồn tại chưa
        $traLoiId = $this->kiemTraTraLoiTonTai($baiLamId, $cauHoiId);
        
        if ($traLoiId) {
            // Cập nhật
            return $this->capNhatTraLoi($traLoiId, $noiDungTraLoi);
        } else {
            // Thêm mới
            return $this->themTraLoi($baiLamId, $cauHoiId, $noiDungTraLoi);
        }
    }
    
    /**
     * Nộp bài tập (cập nhật trạng thái)
     */
    public function nopBaiTap($baiLamId) {
        $sql = "UPDATE bai_lam 
                SET trang_thai = 'da_nop',
                    thoi_gian_nop = NOW()
                WHERE id = :bai_lam_id 
                  AND trang_thai IN ('chua_lam', 'dang_lam')";
        
        return $this->thucThi($sql, ['bai_lam_id' => $baiLamId]);
    }
    
    /**
     * Đếm số câu đã trả lời và tổng số câu
     */
    public function demSoCauTraLoi($baiLamId, $baiTapId) {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM cau_hoi_bai_tap WHERE bai_tap_id = :bai_tap_id) as tong_so_cau,
                    (SELECT COUNT(*) FROM tra_loi_bai_tap WHERE bai_lam_id = :bai_lam_id) as so_cau_da_tra_loi";
        
        return $this->truyVanMot($sql, [
            'bai_tap_id' => $baiTapId,
            'bai_lam_id' => $baiLamId
        ]);
    }
    
    /**
     * Tạo bài tập mới
     */
    public function taoBaiTap($duLieu) {
        $sql = "INSERT INTO bai_tap 
                (lop_hoc_id, chuong_id, tieu_de, mo_ta, han_nop, diem_toi_da, ngay_tao) 
                VALUES 
                (:lop_hoc_id, :chuong_id, :tieu_de, :mo_ta, :han_nop, :diem_toi_da, NOW())";
        
        return $this->thucThi($sql, [
            'lop_hoc_id' => $duLieu['lop_hoc_id'],
            'chuong_id' => $duLieu['chuong_id'],
            'tieu_de' => $duLieu['tieu_de'],
            'mo_ta' => $duLieu['mo_ta'] ?? null,
            'han_nop' => $duLieu['han_nop'],
            'diem_toi_da' => $duLieu['diem_toi_da']
        ]);
    }
    
    /**
     * Thêm câu hỏi cho bài tập
     */
    public function themCauHoi($baiTapId, $cauHoi) {
        $sql = "INSERT INTO cau_hoi_bai_tap 
                (bai_tap_id, noi_dung_cau_hoi, mo_ta, diem) 
                VALUES 
                (:bai_tap_id, :noi_dung, :mo_ta, :diem)";
        
        return $this->thucThi($sql, [
            'bai_tap_id' => $baiTapId,
            'noi_dung' => $cauHoi['noi_dung'],
            'mo_ta' => $cauHoi['mo_ta'] ?? null,
            'diem' => $cauHoi['diem']
        ]);
    }
    
    /**
     * Lấy ID vừa thêm
     */
    public function layIdVuaThem() {
        return $this->layIdVuaInsert();
    }
    
    /**
     * Lấy thông tin trả lời
     */
    public function layThongTinTraLoi($traLoiId) {
        $sql = "SELECT tl.*, ch.bai_tap_id, ch.diem AS diem_toi_da_cau_hoi
                FROM tra_loi tl
                INNER JOIN cau_hoi_bai_tap ch ON tl.cau_hoi_id = ch.id
                WHERE tl.id = :tra_loi_id";
        
        $ketQua = $this->truyVan($sql, ['tra_loi_id' => $traLoiId]);
        return !empty($ketQua) ? $ketQua[0] : null;
    }
    
    /**
     * Lấy lớp học ID theo bài tập
     */
    public function layLopHocIdTheoBaiTap($baiTapId) {
        $sql = "SELECT lop_hoc_id FROM bai_tap WHERE id = :bai_tap_id";
        $ketQua = $this->truyVan($sql, ['bai_tap_id' => $baiTapId]);
        return !empty($ketQua) ? $ketQua[0]['lop_hoc_id'] : null;
    }
    
    /**
     * Lấy câu hỏi
     */
    public function layCauHoi($cauHoiId) {
        $sql = "SELECT * FROM cau_hoi_bai_tap WHERE id = :cau_hoi_id";
        $ketQua = $this->truyVan($sql, ['cau_hoi_id' => $cauHoiId]);
        return !empty($ketQua) ? $ketQua[0] : null;
    }
    
    /**
     * Cập nhật điểm trả lời
     */
    public function capNhatDiemTraLoi($traLoiId, $diem) {
        $sql = "UPDATE tra_loi 
                SET diem = :diem, 
                    ngay_cham = NOW() 
                WHERE id = :tra_loi_id";
        
        return $this->thucThi($sql, [
            'diem' => $diem,
            'tra_loi_id' => $traLoiId
        ]);
    }
    
    /**
     * Cập nhật tổng điểm bài làm
     */
    public function capNhatTongDiemBaiLam($baiLamId) {
        // Tính tổng điểm từ các trả lời
        $sql = "UPDATE bai_lam bl
                SET bl.diem = (
                    SELECT COALESCE(SUM(tl.diem), 0)
                    FROM tra_loi tl
                    WHERE tl.bai_lam_id = bl.id
                ),
                bl.trang_thai = 'da_cham',
                bl.ngay_cham = NOW()
                WHERE bl.id = :bai_lam_id";
        
        return $this->thucThi($sql, ['bai_lam_id' => $baiLamId]);
    }
}
