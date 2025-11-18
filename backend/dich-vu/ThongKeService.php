<?php
/**
 * File: ThongKeService.php
 * Mục đích: Xử lý logic nghiệp vụ cho thống kê
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/ThongKeRepository.php';
require_once __DIR__ . '/../mau/ThongKe.php';

class ThongKeService extends BaseService {
    
    private $thongKeRepo;
    
    public function __construct() {
        $this->thongKeRepo = new ThongKeRepository();
    }
    
    /**
     * Lấy thống kê dashboard cho giảng viên
     */
    public function layThongKeDashboard($giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($giangVienId)) {
            $this->nemLoi('ID giảng viên không hợp lệ');
        }
        
        // Lấy tất cả thống kê
        $duLieu = $this->thongKeRepo->layTatCaThongKe($giangVienId);
        
        // Tạo object ThongKe
        $thongKe = new ThongKe();
        $thongKe->setLopGiangDay($duLieu['lop_giang_day']);
        $thongKe->setSinhVienTheoHoc($duLieu['sinh_vien_theo_hoc']);
        $thongKe->setBaiChoCham($duLieu['bai_cho_cham']);
        $thongKe->setThongBaoMoi($duLieu['thong_bao_moi']);
        
        return $thongKe->toArray();
    }
}
