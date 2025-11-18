<?php
/**
 * File: BaiKiemTraService.php
 * Mục đích: Xử lý logic nghiệp vụ cho bài kiểm tra
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/BaiKiemTraRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class BaiKiemTraService extends BaseService {
    
    private $baiKiemTraRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->baiKiemTraRepo = new BaiKiemTraRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách bài kiểm tra theo lớp học
     */
    public function layBaiKiemTraTheoLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy tổng số sinh viên
        $tongSinhVien = $this->baiKiemTraRepo->demTongSinhVien($lopHocId);
        
        // Lấy danh sách bài kiểm tra
        $danhSach = $this->baiKiemTraRepo->timTheoLopHoc($lopHocId);
        
        // Format dữ liệu
        return array_map(function($bkt) use ($tongSinhVien) {
            return [
                'id' => (int)$bkt['id'],
                'tieu_de' => $bkt['tieu_de'],
                'mo_ta' => $bkt['mo_ta'],
                'thoi_luong' => (int)$bkt['thoi_luong'],
                'thoi_gian_bat_dau' => $bkt['thoi_gian_bat_dau'],
                'thoi_gian_ket_thuc' => $bkt['thoi_gian_ket_thuc'],
                'diem_toi_da' => (float)$bkt['diem_toi_da'],
                'so_cau_hoi' => (int)$bkt['so_cau_hoi'],
                'so_sinh_vien_da_lam' => (int)$bkt['so_sinh_vien_da_lam'],
                'tong_sinh_vien' => $tongSinhVien,
                'chuong' => $bkt['chuong_id'] ? [
                    'so_thu_tu' => (int)$bkt['so_thu_tu_chuong'],
                    'ten_chuong' => $bkt['ten_chuong']
                ] : null,
                'ngay_tao' => $bkt['ngay_tao']
            ];
        }, $danhSach);
    }
}
