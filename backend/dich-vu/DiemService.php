<?php
/**
 * File: DiemService.php
 * Mục đích: Service xử lý logic tính toán điểm số
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/DiemRepository.php';

class DiemService extends BaseService {
    
    private $diemRepo;
    
    // Trọng số điểm
    const TRONG_SO_CHUYEN_CAN = 0.1;  // 10%
    const TRONG_SO_BAI_TAP = 0.4;     // 40%
    const TRONG_SO_KIEM_TRA = 0.5;    // 50%
    
    public function __construct() {
        $this->diemRepo = new DiemRepository();
    }
    
    /**
     * Lấy điểm theo chương hoặc tất cả
     */
    public function layDiemTheoChuong($lopHocId, $sinhVienId, $chuongId = null) {
        // Lấy danh sách chương
        $danhSachChuong = $this->diemRepo->layDanhSachChuong($lopHocId);
        
        if (empty($danhSachChuong)) {
            return [
                'tong_quan' => null,
                'chuong' => []
            ];
        }
        
        // Lấy tổng quan
        $tongQuan = $this->tinhTongQuan($lopHocId, $sinhVienId, $danhSachChuong);
        
        // Nếu chọn chương cụ thể
        if ($chuongId && $chuongId !== 'all') {
            $danhSachChuong = array_filter($danhSachChuong, function($c) use ($chuongId) {
                return $c['id'] == $chuongId;
            });
        }
        
        // Tính điểm cho từng chương
        $ketQuaChuong = [];
        foreach ($danhSachChuong as $chuong) {
            $ketQuaChuong[] = $this->tinhDiemChuong($chuong, $sinhVienId);
        }
        
        return [
            'tong_quan' => $tongQuan,
            'chuong' => $ketQuaChuong
        ];
    }
    
    /**
     * Tính tổng quan điểm
     */
    private function tinhTongQuan($lopHocId, $sinhVienId, $danhSachChuong) {
        $tongQuan = $this->diemRepo->layTongQuanDiem($lopHocId, $sinhVienId);
        
        // Tính điểm trung bình tất cả chương
        $tongDiem = 0;
        $soChuongCoDiem = 0;
        
        foreach ($danhSachChuong as $chuong) {
            $diemChuong = $this->tinhDiemChuong($chuong, $sinhVienId);
            if ($diemChuong['diem_tong'] !== null) {
                $tongDiem += $diemChuong['diem_tong'];
                $soChuongCoDiem++;
            }
        }
        
        $diemTrungBinh = $soChuongCoDiem > 0 ? round($tongDiem / $soChuongCoDiem, 2) : null;
        
        return [
            'diem_trung_binh' => $diemTrungBinh,
            'tong_chuong' => (int)$tongQuan['tong_chuong'],
            'chuong_hoan_thanh' => (int)$tongQuan['chuong_hoan_thanh'],
            'tong_video' => (int)$tongQuan['tong_video'],
            'video_da_xem' => (int)$tongQuan['video_da_xem']
        ];
    }
    
    /**
     * Tính điểm của một chương
     */
    private function tinhDiemChuong($chuong, $sinhVienId) {
        $chuongId = $chuong['id'];
        
        // 1. Tính điểm chuyên cần (CC)
        $tienDoVideo = $this->diemRepo->layTienDoVideoChuong($chuongId, $sinhVienId);
        $diemCC = $this->tinhDiemChuyenCan($tienDoVideo);
        
        // 2. Tính điểm bài tập
        $danhSachBaiTap = $this->diemRepo->layDiemBaiTapChuong($chuongId, $sinhVienId);
        $diemBaiTap = $this->tinhDiemBaiTap($danhSachBaiTap);
        
        // 3. Tính điểm kiểm tra
        $danhSachKiemTra = $this->diemRepo->layDiemKiemTraChuong($chuongId, $sinhVienId);
        $diemKiemTra = $this->tinhDiemKiemTra($danhSachKiemTra);
        
        // 4. Tính điểm tổng chương
        $diemTong = $this->tinhDiemTongChuong($diemCC, $diemBaiTap, $diemKiemTra);
        
        return [
            'chuong_id' => (int)$chuongId,
            'so_thu_tu' => (int)$chuong['so_thu_tu_chuong'],
            'ten_chuong' => $chuong['ten_chuong'],
            'diem_chuyen_can' => $diemCC,
            'diem_bai_tap' => $diemBaiTap,
            'diem_kiem_tra' => $diemKiemTra,
            'diem_tong' => $diemTong,
            'chi_tiet_video' => $tienDoVideo,
            'chi_tiet_bai_tap' => $danhSachBaiTap,
            'chi_tiet_kiem_tra' => $danhSachKiemTra
        ];
    }
    
    /**
     * Tính điểm chuyên cần từ tiến độ video
     * Công thức: (số video xem xong / tổng video) × 10
     */
    private function tinhDiemChuyenCan($tienDoVideo) {
        $tongVideo = (int)$tienDoVideo['tong_video'];
        $videoXemXong = (int)$tienDoVideo['video_xem_xong'];
        
        if ($tongVideo === 0) {
            return [
                'diem' => null,
                'diem_quy_doi' => null,
                'tong_video' => 0,
                'video_xem_xong' => 0
            ];
        }
        
        $diem = round(($videoXemXong / $tongVideo) * 10, 2);
        $diemQuyDoi = round($diem * self::TRONG_SO_CHUYEN_CAN, 2);
        
        return [
            'diem' => $diem,
            'diem_quy_doi' => $diemQuyDoi,
            'tong_video' => $tongVideo,
            'video_xem_xong' => $videoXemXong
        ];
    }
    
    /**
     * Tính điểm trung bình bài tập
     */
    private function tinhDiemBaiTap($danhSachBaiTap) {
        if (empty($danhSachBaiTap)) {
            return [
                'diem_trung_binh' => null,
                'diem_quy_doi' => null,
                'tong_bai_tap' => 0,
                'bai_tap_da_nop' => 0,
                'chi_tiet' => []
            ];
        }
        
        $tongDiem = 0;
        $soLuongCoDiem = 0;
        $chiTiet = [];
        
        foreach ($danhSachBaiTap as $baiTap) {
            $diem = $baiTap['diem'] !== null ? (float)$baiTap['diem'] : null;
            $diemToiDa = (float)$baiTap['diem_toi_da'];
            
            $chiTiet[] = [
                'id' => (int)$baiTap['id'],
                'tieu_de' => $baiTap['tieu_de'],
                'diem' => $diem,
                'diem_toi_da' => $diemToiDa,
                'trang_thai' => $baiTap['trang_thai'],
                'thoi_gian_nop' => $baiTap['thoi_gian_nop']
            ];
            
            if ($diem !== null) {
                // Quy đổi về thang điểm 10
                $tongDiem += ($diem / $diemToiDa) * 10;
                $soLuongCoDiem++;
            }
        }
        
        $diemTrungBinh = $soLuongCoDiem > 0 ? round($tongDiem / $soLuongCoDiem, 2) : null;
        $diemQuyDoi = $diemTrungBinh !== null ? round($diemTrungBinh * self::TRONG_SO_BAI_TAP, 2) : null;
        
        return [
            'diem_trung_binh' => $diemTrungBinh,
            'diem_quy_doi' => $diemQuyDoi,
            'tong_bai_tap' => count($danhSachBaiTap),
            'bai_tap_da_nop' => $soLuongCoDiem,
            'chi_tiet' => $chiTiet
        ];
    }
    
    /**
     * Tính điểm kiểm tra (lấy điểm cao nhất nếu có nhiều lần làm)
     */
    private function tinhDiemKiemTra($danhSachKiemTra) {
        if (empty($danhSachKiemTra)) {
            return [
                'diem' => null,
                'diem_quy_doi' => null,
                'tong_kiem_tra' => 0,
                'kiem_tra_da_lam' => 0,
                'chi_tiet' => []
            ];
        }
        
        $diemCaoNhat = null;
        $soLuongDaLam = 0;
        $chiTiet = [];
        
        foreach ($danhSachKiemTra as $kiemTra) {
            $diem = $kiemTra['diem'] !== null ? (float)$kiemTra['diem'] : null;
            $diemToiDa = (float)$kiemTra['diem_toi_da'];
            
            $chiTiet[] = [
                'id' => (int)$kiemTra['id'],
                'tieu_de' => $kiemTra['tieu_de'],
                'diem' => $diem,
                'diem_toi_da' => $diemToiDa,
                'thoi_luong' => (int)$kiemTra['thoi_luong'],
                'trang_thai' => $kiemTra['trang_thai'],
                'so_cau_dung' => $kiemTra['so_cau_dung'],
                'tong_so_cau' => $kiemTra['tong_so_cau'],
                'thoi_gian_lam_bai' => $kiemTra['thoi_gian_lam_bai']
            ];
            
            if ($diem !== null) {
                $soLuongDaLam++;
                // Quy đổi về thang điểm 10
                $diemQuyDoi10 = ($diem / $diemToiDa) * 10;
                if ($diemCaoNhat === null || $diemQuyDoi10 > $diemCaoNhat) {
                    $diemCaoNhat = $diemQuyDoi10;
                }
            }
        }
        
        $diemCaoNhat = $diemCaoNhat !== null ? round($diemCaoNhat, 2) : null;
        $diemQuyDoi = $diemCaoNhat !== null ? round($diemCaoNhat * self::TRONG_SO_KIEM_TRA, 2) : null;
        
        return [
            'diem' => $diemCaoNhat,
            'diem_quy_doi' => $diemQuyDoi,
            'tong_kiem_tra' => count($danhSachKiemTra),
            'kiem_tra_da_lam' => $soLuongDaLam,
            'chi_tiet' => $chiTiet
        ];
    }
    
    /**
     * Tính điểm tổng chương
     * Công thức: 10% CC + 40% Bài tập + 50% Kiểm tra
     */
    private function tinhDiemTongChuong($diemCC, $diemBaiTap, $diemKiemTra) {
        // Chỉ tính nếu có đủ dữ liệu
        if ($diemCC['diem_quy_doi'] === null || 
            $diemBaiTap['diem_quy_doi'] === null || 
            $diemKiemTra['diem_quy_doi'] === null) {
            return null;
        }
        
        $tongDiem = $diemCC['diem_quy_doi'] + 
                    $diemBaiTap['diem_quy_doi'] + 
                    $diemKiemTra['diem_quy_doi'];
        
        return round($tongDiem, 2);
    }
}
