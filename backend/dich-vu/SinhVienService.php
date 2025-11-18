<?php
/**
 * File: SinhVienService.php
 * Mục đích: Xử lý logic nghiệp vụ cho sinh viên
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/SinhVienRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class SinhVienService extends BaseService {
    
    private $sinhVienRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->sinhVienRepo = new SinhVienRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách sinh viên với phân trang
     */
    public function laySinhVienTheoLop($lopHocId, $giangVienId, $page = 1, $limit = 5) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Tính phân trang
        $phanTrang = $this->tinhPhanTrang($page, $limit);
        
        // Đếm tổng sinh viên
        $tongSinhVien = $this->sinhVienRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách sinh viên
        $danhSach = $this->sinhVienRepo->timTheoLopHocVoiPhanTrang(
            $lopHocId, 
            $phanTrang['offset'], 
            $phanTrang['limit']
        );
        
        // Format dữ liệu
        $sinhVien = array_map(function($sv) {
            return [
                'id' => (int)$sv['id'],
                'ma_nguoi_dung' => $sv['ma_nguoi_dung'],
                'ho_ten' => $sv['ho_ten'],
                'anh_dai_dien' => $sv['anh_dai_dien'],
                'email' => $sv['email'],
                'tien_do' => round((float)$sv['tien_do'], 1),
                'last_updated' => $sv['last_updated'] ? 
                    $this->tinhThoiGianTruoc($sv['last_updated']) : 'Chưa có hoạt động'
            ];
        }, $danhSach);
        
        // Tạo thông tin phân trang
        $pagination = $this->taoThongTinPhanTrang($page, $limit, $tongSinhVien);
        
        return [
            'sinh_vien' => $sinhVien,
            'pagination' => $pagination
        ];
    }
}
