<?php
/**
 * File: SinhVienRepository.php
 * Mục đích: Repository xử lý truy vấn dữ liệu sinh viên
 */

require_once __DIR__ . '/../co-so/BaseRepository.php';

class SinhVienRepository extends BaseRepository {
    
    /**
     * Lấy danh sách lớp học của sinh viên (không bao gồm tiến độ)
     * @param int $sinhVienId
     * @return array
     */
    public function getDanhSachLopHoc($sinhVienId) {
        $sql = "
            SELECT 
                lh.id AS lop_hoc_id,
                lh.ma_lop_hoc,
                mh.id AS mon_hoc_id,
                mh.ten_mon_hoc,
                nd.id AS giang_vien_id,
                nd.ho_ten AS ten_giang_vien,
                nd.anh_dai_dien AS anh_giang_vien,
                svlh.trang_thai,
                svlh.ngay_dang_ky
            FROM sinh_vien_lop_hoc svlh
            JOIN lop_hoc lh ON svlh.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON svlh.mon_hoc_id = mh.id
            JOIN nguoi_dung nd ON lh.giang_vien_id = nd.id
            WHERE svlh.sinh_vien_id = :sinh_vien_id
            AND svlh.trang_thai = 'dang_hoc'
            ORDER BY mh.ten_mon_hoc ASC
        ";
        
        return $this->truyVan($sql, ['sinh_vien_id' => $sinhVienId]);
    }
    
    /**
     * Lấy nhắc nhở sắp hết hạn (bài tập + kiểm tra, sắp xếp theo deadline)
     * @param int $sinhVienId
     * @param int $limit - Số lượng kết quả tối đa
     * @return array
     */
    public function getNhacNhoSapHetHan($sinhVienId, $limit = 5) {
        // Query 1: Bài tập sắp hết hạn
        $sqlBaiTap = "
            SELECT 
                'bai_tap' AS loai,
                bt.id AS bai_id,
                bt.tieu_de,
                mh.ten_mon_hoc,
                bt.han_nop AS deadline,
                DATEDIFF(bt.han_nop, NOW()) AS con_bao_nhieu_ngay,
                CASE 
                    WHEN bl.id IS NULL THEN 'chua_nop'
                    WHEN bl.trang_thai = 'da_nop' THEN 'da_nop'
                    ELSE bl.trang_thai
                END AS trang_thai,
                lh.id AS lop_hoc_id
            FROM bai_tap bt
            JOIN lop_hoc lh ON bt.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sv_id_1
            LEFT JOIN bai_lam bl ON bt.id = bl.bai_tap_id AND bl.sinh_vien_id = :sv_id_2
            WHERE bt.han_nop >= NOW()
            AND svlh.trang_thai = 'dang_hoc'
            AND (bl.id IS NULL OR bl.trang_thai IN ('chua_lam', 'dang_lam'))
        ";
        
        $stmtBaiTap = $this->db->prepare($sqlBaiTap);
        $stmtBaiTap->execute([':sv_id_1' => $sinhVienId, ':sv_id_2' => $sinhVienId]);
        $baiTapList = $stmtBaiTap->fetchAll(PDO::FETCH_ASSOC);
        
        // Query 2: Bài kiểm tra sắp tới
        $sqlBaiKiemTra = "
            SELECT 
                'bai_kiem_tra' AS loai,
                bkt.id AS bai_id,
                bkt.tieu_de,
                mh.ten_mon_hoc,
                bkt.thoi_gian_bat_dau AS deadline,
                DATEDIFF(bkt.thoi_gian_bat_dau, NOW()) AS con_bao_nhieu_ngay,
                CASE 
                    WHEN blkt.id IS NULL THEN 'chua_lam'
                    WHEN blkt.trang_thai = 'da_nop' THEN 'da_nop'
                    ELSE blkt.trang_thai
                END AS trang_thai,
                lh.id AS lop_hoc_id
            FROM bai_kiem_tra bkt
            JOIN lop_hoc lh ON bkt.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sv_id_3
            LEFT JOIN bai_lam_kiem_tra blkt ON bkt.id = blkt.bai_kiem_tra_id AND blkt.sinh_vien_id = :sv_id_4
            WHERE bkt.thoi_gian_bat_dau >= NOW()
            AND svlh.trang_thai = 'dang_hoc'
            AND (blkt.id IS NULL OR blkt.trang_thai IN ('chua_lam', 'dang_lam'))
        ";
        
        $stmtBaiKiemTra = $this->db->prepare($sqlBaiKiemTra);
        $stmtBaiKiemTra->execute([':sv_id_3' => $sinhVienId, ':sv_id_4' => $sinhVienId]);
        $baiKiemTraList = $stmtBaiKiemTra->fetchAll(PDO::FETCH_ASSOC);
        
        // Kết hợp 2 kết quả
        $allResults = array_merge($baiTapList, $baiKiemTraList);
        
        // Sắp xếp theo deadline
        usort($allResults, function($a, $b) {
            $deadlineA = strtotime($a['deadline']);
            $deadlineB = strtotime($b['deadline']);
            return $deadlineA - $deadlineB;
        });
        
        // Lấy top N
        return array_slice($allResults, 0, $limit);
    }
    
    /**
     * Lấy thông báo mới nhất của sinh viên
     * @param int $sinhVienId
     * @param int $limit
     * @return array
     */
    public function getThongBaoMoiNhat($sinhVienId, $limit = 3) {
        $sql = "
            SELECT 
                'thong_bao' AS loai,
                tb.id AS bai_id,
                tb.tieu_de,
                mh.ten_mon_hoc,
                tb.thoi_gian_gui AS deadline,
                0 AS con_bao_nhieu_ngay,
                'moi' AS trang_thai,
                lh.id AS lop_hoc_id
            FROM thong_bao_lop_hoc tb
            JOIN lop_hoc lh ON tb.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sinh_vien_id
            WHERE svlh.trang_thai = 'dang_hoc'
            ORDER BY tb.thoi_gian_gui DESC
            LIMIT :limit
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sinh_vien_id', $sinhVienId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy thông tin sinh viên từ ID
     * @param int $sinhVienId
     * @return array|null
     */
    public function getThongTinSinhVien($sinhVienId) {
        $sql = "
            SELECT 
                id,
                ma_nguoi_dung,
                ten_dang_nhap,
                email,
                ho_ten,
                anh_dai_dien,
                vai_tro,
                trang_thai
            FROM nguoi_dung
            WHERE id = :id AND vai_tro = 'sinh_vien'
            LIMIT 1
        ";
        
        $result = $this->truyVanMot($sql, ['id' => $sinhVienId]);
        
        // Gán avatar mặc định nếu không có
        if ($result && empty($result['anh_dai_dien'])) {
            $result['anh_dai_dien'] = '/public/student/CSS/avatar-sv.webp';
        }
        
        return $result;
    }
    
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
        
        $result = $this->truyVanMot($sql, ['lop_hoc_id' => $lopHocId]);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Kiểm tra sinh viên có đăng ký lớp học này không
     * @param int $sinhVienId
     * @param int $lopHocId
     * @return bool
     */
    public function kiemTraSinhVienTrongLop($sinhVienId, $lopHocId) {
        $sql = "SELECT id FROM sinh_vien_lop_hoc
                WHERE sinh_vien_id = :sinh_vien_id 
                AND lop_hoc_id = :lop_hoc_id
                AND trang_thai = 'dang_hoc'
                LIMIT 1";
        
        $result = $this->truyVanMot($sql, [
            'sinh_vien_id' => $sinhVienId,
            'lop_hoc_id' => $lopHocId
        ]);
        
        return !empty($result);
    }
    
    /**
     * Lấy thông báo trong (hoạt động) của sinh viên
     * Bao gồm: bài tập sắp hết hạn, bài kiểm tra sắp hết hạn, thông báo lớp học
     * @param int $sinhVienId
     * @return array
     */
    public function getThongBaoTrong($sinhVienId) {
        $sql = "
            -- 1. Bài tập sắp hết hạn (7 ngày tới)
            SELECT 
                'bai_tap' AS loai,
                bt.id AS bai_id,
                bt.tieu_de,
                mh.ten_mon_hoc,
                bt.han_nop AS deadline,
                DATEDIFF(bt.han_nop, NOW()) AS con_bao_nhieu_ngay,
                CASE 
                    WHEN bl.id IS NULL THEN 'chua_nop'
                    WHEN bl.trang_thai = 'luu_nhap' THEN 'dang_lam'
                    ELSE 'da_nop'
                END AS trang_thai,
                lh.id AS lop_hoc_id,
                DATE_FORMAT(bt.han_nop, '%H:%i') AS thoi_gian,
                DATE_FORMAT(bt.han_nop, '%d/%m/%Y') AS ngay
            FROM bai_tap bt
            JOIN lop_hoc lh ON bt.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sinh_vien_id1
            LEFT JOIN bai_lam bl ON bt.id = bl.bai_tap_id AND bl.sinh_vien_id = :sinh_vien_id2
            WHERE svlh.trang_thai = 'dang_hoc'
            AND bt.han_nop IS NOT NULL
            AND bt.han_nop >= NOW()
            AND bt.han_nop <= DATE_ADD(NOW(), INTERVAL 7 DAY)
            AND (bl.id IS NULL OR bl.trang_thai != 'da_nop')
            
            UNION ALL
            
            -- 2. Bài kiểm tra sắp đến hạn (7 ngày tới)
            SELECT 
                'bai_kiem_tra' AS loai,
                bkt.id AS bai_id,
                bkt.tieu_de,
                mh.ten_mon_hoc,
                bkt.thoi_gian_bat_dau AS deadline,
                DATEDIFF(bkt.thoi_gian_bat_dau, NOW()) AS con_bao_nhieu_ngay,
                CASE 
                    WHEN blkt.id IS NULL THEN 'chua_lam'
                    WHEN blkt.trang_thai = 'da_nop' THEN 'da_lam'
                    ELSE 'dang_lam'
                END AS trang_thai,
                lh.id AS lop_hoc_id,
                DATE_FORMAT(bkt.thoi_gian_bat_dau, '%H:%i') AS thoi_gian,
                DATE_FORMAT(bkt.thoi_gian_bat_dau, '%d/%m/%Y') AS ngay
            FROM bai_kiem_tra bkt
            JOIN lop_hoc lh ON bkt.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sinh_vien_id3
            LEFT JOIN bai_lam_kiem_tra blkt ON bkt.id = blkt.bai_kiem_tra_id AND blkt.sinh_vien_id = :sinh_vien_id4
            WHERE svlh.trang_thai = 'dang_hoc'
            AND bkt.thoi_gian_bat_dau IS NOT NULL
            AND bkt.thoi_gian_bat_dau >= NOW()
            AND bkt.thoi_gian_bat_dau <= DATE_ADD(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- 3. Thông báo lớp học (7 ngày gần nhất)
            SELECT 
                'thong_bao' AS loai,
                tb.id AS bai_id,
                tb.tieu_de,
                mh.ten_mon_hoc,
                tb.thoi_gian_gui AS deadline,
                0 AS con_bao_nhieu_ngay,
                'moi' AS trang_thai,
                lh.id AS lop_hoc_id,
                DATE_FORMAT(tb.thoi_gian_gui, '%H:%i') AS thoi_gian,
                DATE_FORMAT(tb.thoi_gian_gui, '%d/%m/%Y') AS ngay
            FROM thong_bao_lop_hoc tb
            JOIN lop_hoc lh ON tb.lop_hoc_id = lh.id
            JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
            JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.sinh_vien_id = :sinh_vien_id5
            WHERE svlh.trang_thai = 'dang_hoc'
            AND tb.thoi_gian_gui >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            ORDER BY deadline DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sinh_vien_id1', $sinhVienId, PDO::PARAM_INT);
        $stmt->bindValue(':sinh_vien_id2', $sinhVienId, PDO::PARAM_INT);
        $stmt->bindValue(':sinh_vien_id3', $sinhVienId, PDO::PARAM_INT);
        $stmt->bindValue(':sinh_vien_id4', $sinhVienId, PDO::PARAM_INT);
        $stmt->bindValue(':sinh_vien_id5', $sinhVienId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
