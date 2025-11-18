<?php
/**
 * File: ThongBaoService.php
 * Mục đích: Xử lý logic nghiệp vụ cho thông báo
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/ThongBaoRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class ThongBaoService extends BaseService {
    
    private $thongBaoRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->thongBaoRepo = new ThongBaoRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách thông báo theo lớp học
     */
    public function layThongBaoTheoLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy danh sách thông báo
        $danhSach = $this->thongBaoRepo->timTheoLopHoc($lopHocId);
        
        // Format dữ liệu
        return array_map(function($tb) {
            return [
                'id' => (int)$tb['id'],
                'tieu_de' => $tb['tieu_de'],
                'noi_dung' => $tb['noi_dung'],
                'thoi_gian_gui' => $tb['thoi_gian_gui'],
                'nguoi_gui' => [
                    'ho_ten' => $tb['ho_ten'],
                    'anh_dai_dien' => $tb['anh_dai_dien']
                ]
            ];
        }, $danhSach);
    }
}
