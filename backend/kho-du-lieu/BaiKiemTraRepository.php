<?php
/**
 * File: BaiKiemTraRepository.php
 * Mục đích: Xử lý truy vấn database cho bài kiểm tra
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class BaiKiemTraRepository extends BaseRepository {
    
    /**
     * Lấy danh sách bài kiểm tra theo lớp học
     */
    public function timTheoLopHoc($lopHocId) {
        $sql = "SELECT 
                    bkt.id, bkt.tieu_de, bkt.mo_ta, 
                    bkt.thoi_luong, bkt.thoi_gian_bat_dau, bkt.thoi_gian_ket_thuc,
                    bkt.diem_toi_da, bkt.chuong_id, bkt.ngay_tao,
                    -- Thông tin chương (nếu có)
                    c.so_thu_tu_chuong, c.ten_chuong,
                    -- Số câu hỏi
                    (SELECT COUNT(*) FROM cau_hoi_trac_nghiem WHERE bai_kiem_tra_id = bkt.id) AS so_cau_hoi,
                    -- Số sinh viên đã làm
                    (SELECT COUNT(DISTINCT blkt.sinh_vien_id) 
                     FROM bai_lam_kiem_tra blkt 
                     WHERE blkt.bai_kiem_tra_id = bkt.id 
                     AND blkt.trang_thai IN ('da_nop', 'da_cham')) AS so_sinh_vien_da_lam
                FROM bai_kiem_tra bkt
                LEFT JOIN chuong c ON bkt.chuong_id = c.id
                WHERE bkt.lop_hoc_id = :lop_hoc_id
                ORDER BY bkt.ngay_tao DESC, bkt.thoi_gian_bat_dau ASC";
        
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
     * Lấy thống kê bài kiểm tra (số bài chấm, điểm TB, cao nhất, thấp nhất)
     */
    public function layThongKeBaiKiemTra($baiKiemTraId) {
        $sql = "SELECT 
                    COUNT(CASE WHEN blkt.trang_thai = 'da_nop' THEN 1 END) AS so_bai_chua_cham,
                    COUNT(CASE WHEN blkt.trang_thai = 'da_cham' THEN 1 END) AS so_bai_da_cham,
                    AVG(CASE WHEN blkt.trang_thai = 'da_cham' AND blkt.diem IS NOT NULL THEN blkt.diem END) AS diem_trung_binh,
                    MAX(CASE WHEN blkt.trang_thai = 'da_cham' AND blkt.diem IS NOT NULL THEN blkt.diem END) AS diem_cao_nhat,
                    MIN(CASE WHEN blkt.trang_thai = 'da_cham' AND blkt.diem IS NOT NULL THEN blkt.diem END) AS diem_thap_nhat,
                    COUNT(CASE WHEN blkt.trang_thai IN ('da_nop', 'da_cham') THEN 1 END) AS so_bai_da_nop
                FROM bai_lam_kiem_tra blkt
                WHERE blkt.bai_kiem_tra_id = :bai_kiem_tra_id";
        
        $result = $this->truyVan($sql, ['bai_kiem_tra_id' => $baiKiemTraId]);
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Lấy danh sách sinh viên làm bài kiểm tra (bao gồm cả sinh viên chưa làm)
     */
    public function layDanhSachSinhVienLamBaiKiemTra($baiKiemTraId, $lopHocId) {
        $sql = "SELECT 
                    sv.id AS sinh_vien_id,
                    sv.ho_ten,
                    sv.ma_nguoi_dung AS ma_sinh_vien,
                    sv.anh_dai_dien,
                    COALESCE(blkt.trang_thai, 'chua_lam') AS trang_thai,
                    blkt.diem,
                    blkt.thoi_gian_nop,
                    blkt.thoi_gian_bat_dau,
                    blkt.thoi_gian_lam_bai,
                    CASE 
                        WHEN blkt.trang_thai = 'da_cham' THEN 'da_cham'
                        WHEN blkt.trang_thai = 'da_nop' THEN 'chua_cham'
                        ELSE 'chua_lam'
                    END AS trang_thai_cham
                FROM nguoi_dung sv
                JOIN sinh_vien_lop_hoc svlh ON sv.id = svlh.sinh_vien_id
                LEFT JOIN bai_lam_kiem_tra blkt ON sv.id = blkt.sinh_vien_id AND blkt.bai_kiem_tra_id = :bai_kiem_tra_id
                WHERE svlh.lop_hoc_id = :lop_hoc_id
                AND svlh.trang_thai = 'dang_hoc'
                AND sv.vai_tro = 'sinh_vien'
                ORDER BY sv.ho_ten ASC";
        
        return $this->truyVan($sql, [
            'bai_kiem_tra_id' => $baiKiemTraId,
            'lop_hoc_id' => $lopHocId
        ]);
    }
    
    /**
     * Lấy danh sách bài kiểm tra cho sinh viên (kèm trạng thái đã làm)
     */
    public function layDanhSachChoSinhVien($lopHocId, $sinhVienId) {
        $sql = "SELECT 
                    bkt.id, bkt.tieu_de, bkt.mo_ta, 
                    bkt.thoi_luong, bkt.thoi_gian_bat_dau, bkt.thoi_gian_ket_thuc,
                    bkt.diem_toi_da, bkt.chuong_id, bkt.cho_phep_lam_lai,
                    -- Thông tin chương
                    c.so_thu_tu_chuong, c.ten_chuong,
                    -- Số câu hỏi
                    (SELECT COUNT(*) FROM cau_hoi_trac_nghiem WHERE bai_kiem_tra_id = bkt.id) AS so_cau_hoi,
                    -- Trạng thái bài làm của sinh viên
                    blkt.id AS bai_lam_id,
                    blkt.trang_thai,
                    blkt.diem,
                    blkt.so_cau_dung,
                    blkt.tong_so_cau,
                    blkt.thoi_gian_bat_dau AS thoi_gian_bat_dau_lam,
                    blkt.thoi_gian_nop,
                    blkt.thoi_gian_lam_bai
                FROM bai_kiem_tra bkt
                LEFT JOIN chuong c ON bkt.chuong_id = c.id
                LEFT JOIN bai_lam_kiem_tra blkt ON bkt.id = blkt.bai_kiem_tra_id 
                    AND blkt.sinh_vien_id = :sinh_vien_id
                WHERE bkt.lop_hoc_id = :lop_hoc_id
                ORDER BY bkt.thoi_gian_bat_dau DESC";
        
        return $this->truyVan($sql, [
            'lop_hoc_id' => $lopHocId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Lấy chi tiết bài kiểm tra
     */
    public function layChiTiet($baiKiemTraId) {
        $sql = "SELECT 
                    bkt.*,
                    c.so_thu_tu_chuong, c.ten_chuong,
                    lh.ma_lop_hoc, lh.ten_lop_hoc,
                    mh.ten_mon_hoc
                FROM bai_kiem_tra bkt
                LEFT JOIN chuong c ON bkt.chuong_id = c.id
                INNER JOIN lop_hoc lh ON bkt.lop_hoc_id = lh.id
                INNER JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
                WHERE bkt.id = :id";
        
        $result = $this->truyVanMot($sql, ['id' => $baiKiemTraId]);
        // Đảm bảo cho_phep_lam_lai có giá trị mặc định
        if ($result && !isset($result['cho_phep_lam_lai'])) {
            $result['cho_phep_lam_lai'] = 0;
        }
        return $result;
    }
    
    /**
     * Lấy danh sách câu hỏi của bài kiểm tra
     */
    public function layCauHoi($baiKiemTraId, $hiemThiDapAn = false) {
        $sql = "SELECT 
                    ch.id, ch.thu_tu, ch.noi_dung_cau_hoi, ch.diem
                FROM cau_hoi_trac_nghiem ch
                WHERE ch.bai_kiem_tra_id = :bai_kiem_tra_id
                ORDER BY ch.thu_tu ASC";
        
        $cauHoi = $this->truyVan($sql, ['bai_kiem_tra_id' => $baiKiemTraId]);
        
        // Lấy lựa chọn cho từng câu hỏi
        foreach ($cauHoi as &$ch) {
            $ch['lua_chon'] = $this->layLuaChonCauHoi($ch['id'], $hiemThiDapAn);
        }
        
        return $cauHoi;
    }
    
    /**
     * Lấy lựa chọn của câu hỏi
     */
    public function layLuaChonCauHoi($cauHoiId, $hiemThiDapAn = false) {
        if ($hiemThiDapAn) {
            // Không hiển thị đáp án đúng (cho khi làm bài)
            $sql = "SELECT id, thu_tu, noi_dung_lua_chon
                    FROM lua_chon_cau_hoi
                    WHERE cau_hoi_id = :cau_hoi_id
                    ORDER BY thu_tu ASC";
        } else {
            // Hiển thị đáp án đúng (cho khi xem kết quả)
            $sql = "SELECT id, thu_tu, noi_dung_lua_chon, la_dap_an_dung
                    FROM lua_chon_cau_hoi
                    WHERE cau_hoi_id = :cau_hoi_id
                    ORDER BY thu_tu ASC";
        }
        
        return $this->truyVan($sql, ['cau_hoi_id' => $cauHoiId]);
    }
    
    /**
     * Kiểm tra sinh viên đã làm bài kiểm tra chưa
     * Lấy bài làm MỚI NHẤT (để hỗ trợ làm lại nhiều lần)
     */
    public function kiemTraDaLam($baiKiemTraId, $sinhVienId) {
        $sql = "SELECT id, trang_thai, thoi_gian_bat_dau, thoi_gian_nop
                FROM bai_lam_kiem_tra
                WHERE bai_kiem_tra_id = :bai_kiem_tra_id
                AND sinh_vien_id = :sinh_vien_id
                ORDER BY thoi_gian_bat_dau DESC
                LIMIT 1";
        
        return $this->truyVanMot($sql, [
            'bai_kiem_tra_id' => $baiKiemTraId,
            'sinh_vien_id' => $sinhVienId
        ]);
    }
    
    /**
     * Tạo bài làm mới
     */
    public function taoBaiLam($baiKiemTraId, $sinhVienId) {
        $sql = "INSERT INTO bai_lam_kiem_tra 
                (bai_kiem_tra_id, sinh_vien_id, trang_thai, thoi_gian_bat_dau, tong_so_cau)
                VALUES (:bai_kiem_tra_id, :sinh_vien_id, 'dang_lam', NOW(), 
                        (SELECT COUNT(*) FROM cau_hoi_trac_nghiem WHERE bai_kiem_tra_id = :bai_kiem_tra_id_2))";
        
        $this->thucThi($sql, [
            'bai_kiem_tra_id' => $baiKiemTraId,
            'bai_kiem_tra_id_2' => $baiKiemTraId,
            'sinh_vien_id' => $sinhVienId
        ]);
        
        return $this->layIdVuaInsert();
    }
    
    /**
     * Lưu câu trả lời
     */
    public function luuTraLoi($baiLamId, $cauHoiId, $luaChonId) {
        // Kiểm tra xem đã trả lời câu này chưa
        $sql = "SELECT id FROM chi_tiet_tra_loi 
                WHERE bai_lam_kiem_tra_id = :bai_lam_id 
                AND cau_hoi_id = :cau_hoi_id";
        
        $existing = $this->truyVanMot($sql, [
            'bai_lam_id' => $baiLamId,
            'cau_hoi_id' => $cauHoiId
        ]);
        
        if ($existing) {
            // Cập nhật
            $sql = "UPDATE chi_tiet_tra_loi 
                    SET lua_chon_id = :lua_chon_id, 
                        thoi_gian_tra_loi = NOW()
                    WHERE bai_lam_kiem_tra_id = :bai_lam_id 
                    AND cau_hoi_id = :cau_hoi_id";
        } else {
            // Thêm mới
            $sql = "INSERT INTO chi_tiet_tra_loi 
                    (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, thoi_gian_tra_loi)
                    VALUES (:bai_lam_id, :cau_hoi_id, :lua_chon_id, NOW())";
        }
        
        return $this->thucThi($sql, [
            'bai_lam_id' => $baiLamId,
            'cau_hoi_id' => $cauHoiId,
            'lua_chon_id' => $luaChonId
        ]);
    }
    
    /**
     * Chấm điểm bài làm
     * Logic mới: Tổng điểm luôn là 10, điểm mỗi câu = 10 / tổng số câu
     */
    public function chamDiem($baiLamId) {
        // Cập nhật dung_hay_sai cho từng câu trả lời
        $sql = "UPDATE chi_tiet_tra_loi ctl
                INNER JOIN lua_chon_cau_hoi lc ON ctl.lua_chon_id = lc.id
                SET ctl.dung_hay_sai = lc.la_dap_an_dung
                WHERE ctl.bai_lam_kiem_tra_id = :bai_lam_id";
        
        $this->thucThi($sql, ['bai_lam_id' => $baiLamId]);
        
        // Lấy tổng số câu hỏi của bài kiểm tra
        $sql = "SELECT COUNT(*) as tong_so_cau
                FROM cau_hoi_trac_nghiem ch
                INNER JOIN bai_lam_kiem_tra blkt ON ch.bai_kiem_tra_id = blkt.bai_kiem_tra_id
                WHERE blkt.id = :bai_lam_id";
        
        $tongSoCau = $this->truyVanMot($sql, ['bai_lam_id' => $baiLamId]);
        $tongSoCau = (int)($tongSoCau['tong_so_cau'] ?? 0);
        
        // Tính số câu đúng
        $sql = "SELECT COUNT(*) AS so_cau_dung
                FROM chi_tiet_tra_loi
                WHERE bai_lam_kiem_tra_id = :bai_lam_id
                AND dung_hay_sai = 1";
        
        $result = $this->truyVanMot($sql, ['bai_lam_id' => $baiLamId]);
        $soCauDung = (int)($result['so_cau_dung'] ?? 0);
        
        // Tính điểm: (số câu đúng / tổng số câu) * 10, làm tròn 2 chữ số
        $tongDiem = $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 10, 2) : 0;
        
        return [
            'so_cau_dung' => $soCauDung,
            'tong_diem' => $tongDiem,
            'tong_so_cau' => $tongSoCau
        ];
    }
    
    /**
     * Nộp bài kiểm tra
     */
    public function nopBai($baiLamId, $soCauDung, $tongDiem, $thoiGianLamBai) {
        $sql = "UPDATE bai_lam_kiem_tra
                SET trang_thai = 'da_nop',
                    so_cau_dung = :so_cau_dung,
                    diem = :diem,
                    thoi_gian_nop = NOW(),
                    thoi_gian_lam_bai = :thoi_gian_lam_bai
                WHERE id = :id";
        
        return $this->thucThi($sql, [
            'id' => $baiLamId,
            'so_cau_dung' => $soCauDung,
            'diem' => $tongDiem,
            'thoi_gian_lam_bai' => $thoiGianLamBai
        ]);
    }
    
    /**
     * Lấy kết quả bài làm
     */
    public function layKetQua($baiLamId) {
        $sql = "SELECT 
                    blkt.*,
                    bkt.tieu_de, bkt.thoi_luong, bkt.diem_toi_da,
                    sv.ho_ten, sv.ma_nguoi_dung
                FROM bai_lam_kiem_tra blkt
                INNER JOIN bai_kiem_tra bkt ON blkt.bai_kiem_tra_id = bkt.id
                INNER JOIN nguoi_dung sv ON blkt.sinh_vien_id = sv.id
                WHERE blkt.id = :id";
        
        return $this->truyVanMot($sql, ['id' => $baiLamId]);
    }
    
    /**
     * Lấy chi tiết trả lời của sinh viên
     */
    public function layChiTietTraLoi($baiLamId) {
        $sql = "SELECT 
                    ctl.id, ctl.cau_hoi_id, ctl.lua_chon_id, ctl.dung_hay_sai,
                    ch.thu_tu, ch.noi_dung_cau_hoi, ch.diem,
                    lc.noi_dung_lua_chon, lc.la_dap_an_dung
                FROM chi_tiet_tra_loi ctl
                INNER JOIN cau_hoi_trac_nghiem ch ON ctl.cau_hoi_id = ch.id
                LEFT JOIN lua_chon_cau_hoi lc ON ctl.lua_chon_id = lc.id
                WHERE ctl.bai_lam_kiem_tra_id = :bai_lam_id
                ORDER BY ch.thu_tu ASC";
        
        return $this->truyVan($sql, ['bai_lam_id' => $baiLamId]);
    }
    
    /**
     * Lấy thông tin bài làm bao gồm cả đáp án đúng
     */
    public function layBaiLamVoiDapAn($baiLamId) {
        $baiLam = $this->layKetQua($baiLamId);
        
        if (!$baiLam) {
            return null;
        }
        
        // Lấy tất cả câu hỏi kèm đáp án
        $sql = "SELECT 
                    ch.id AS cau_hoi_id, ch.thu_tu, ch.noi_dung_cau_hoi, ch.diem,
                    ctl.lua_chon_id AS lua_chon_da_chon,
                    ctl.dung_hay_sai
                FROM cau_hoi_trac_nghiem ch
                LEFT JOIN chi_tiet_tra_loi ctl ON ch.id = ctl.cau_hoi_id 
                    AND ctl.bai_lam_kiem_tra_id = :bai_lam_id
                WHERE ch.bai_kiem_tra_id = :bai_kiem_tra_id
                ORDER BY ch.thu_tu ASC";
        
        $cauHoi = $this->truyVan($sql, [
            'bai_lam_id' => $baiLamId,
            'bai_kiem_tra_id' => $baiLam['bai_kiem_tra_id']
        ]);
        
        // Lấy lựa chọn cho từng câu (kèm đáp án đúng)
        foreach ($cauHoi as &$ch) {
            $ch['lua_chon'] = $this->layLuaChonCauHoi($ch['cau_hoi_id'], false);
        }
        
        $baiLam['cau_hoi'] = $cauHoi;
        
        return $baiLam;
    }
    
    /**
     * Tạo bài kiểm tra mới
     */
    public function taoBaiKiemTra($duLieu) {
        $sql = "INSERT INTO bai_kiem_tra 
                (lop_hoc_id, chuong_id, tieu_de, thoi_luong, thoi_gian_bat_dau, diem_toi_da, cho_phep_lam_lai)
                VALUES 
                (:lop_hoc_id, :chuong_id, :tieu_de, :thoi_luong, :thoi_gian_bat_dau, :diem_toi_da, :cho_phep_lam_lai)";
        
        $this->thucThi($sql, [
            'lop_hoc_id' => $duLieu['lop_hoc_id'],
            'chuong_id' => $duLieu['chuong_id'],
            'tieu_de' => $duLieu['tieu_de'],
            'thoi_luong' => $duLieu['thoi_luong'],
            'thoi_gian_bat_dau' => $duLieu['thoi_gian_bat_dau'],
            'diem_toi_da' => $duLieu['diem_toi_da'],
            'cho_phep_lam_lai' => $duLieu['cho_phep_lam_lai'] ?? 0
        ]);
        
        return $this->layIdVuaThem();
    }
    
    /**
     * Thêm câu hỏi trắc nghiệm
     */
    public function themCauHoi($baiKiemTraId, $cauHoi) {
        $sql = "INSERT INTO cau_hoi_trac_nghiem 
                (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem)
                VALUES 
                (:bai_kiem_tra_id, :thu_tu, :noi_dung_cau_hoi, :diem)";
        
        $this->thucThi($sql, [
            'bai_kiem_tra_id' => $baiKiemTraId,
            'thu_tu' => $cauHoi['thu_tu'],
            'noi_dung_cau_hoi' => $cauHoi['noi_dung_cau_hoi'],
            'diem' => $cauHoi['diem']
        ]);
        
        return $this->layIdVuaThem();
    }
    
    /**
     * Thêm lựa chọn cho câu hỏi
     */
    public function themLuaChon($cauHoiId, $luaChon) {
        $sql = "INSERT INTO lua_chon_cau_hoi 
                (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
                VALUES 
                (:cau_hoi_id, :thu_tu, :noi_dung_lua_chon, :la_dap_an_dung)";
        
        $this->thucThi($sql, [
            'cau_hoi_id' => $cauHoiId,
            'thu_tu' => $luaChon['thu_tu'],
            'noi_dung_lua_chon' => $luaChon['noi_dung'],
            'la_dap_an_dung' => $luaChon['dung'] ? 1 : 0
        ]);
    }
    
    /**
     * Lấy ID vừa thêm
     */
    public function layIdVuaThem() {
        return $this->layIdVuaInsert();
    }
    
    /**
     * Bắt đầu transaction
     */
    public function batDauGiaoDich() {
        $this->batDauTransaction();
    }
    
    /**
     * Xác nhận transaction
     */
    public function xacNhanGiaoDich() {
        $this->commit();
    }
    
    /**
     * Hủy transaction
     */
    public function huyGiaoDich() {
        $this->rollback();
    }
}
