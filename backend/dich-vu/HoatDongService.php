<?php
/**
 * File: HoatDongService.php
 * Mục đích: Xử lý logic nghiệp vụ cho hoạt động gần đây
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/HoatDongRepository.php';

class HoatDongService extends BaseService {
    
    private $hoatDongRepo;
    
    public function __construct() {
        $this->hoatDongRepo = new HoatDongRepository();
    }
    
    /**
     * Lấy danh sách hoạt động gần đây
     */
    public function layHoatDongGanDay($giangVienId, $limit = 10) {
        // Validate
        if (!$this->kiemTraSoNguyen($giangVienId)) {
            $this->nemLoi('ID giảng viên không hợp lệ');
        }
        
        // Giới hạn limit từ 1-50
        $limit = max(1, min($limit, 50));
        
        // Lấy dữ liệu
        $danhSach = $this->hoatDongRepo->layHoatDongGanDay($giangVienId, $limit);
        
        // Format dữ liệu và tính thời gian hiển thị
        return array_map(function($hoatDong) {
            return [
                'id' => $hoatDong['id'],
                'loai' => $hoatDong['loai'],
                'icon' => $hoatDong['icon'],
                'ho_ten' => $hoatDong['ho_ten'],
                'anh_dai_dien' => $hoatDong['anh_dai_dien'],
                'tieu_de_bai' => $hoatDong['tieu_de_bai'],
                'noi_dung' => $hoatDong['noi_dung'],
                'thoi_gian' => $hoatDong['thoi_gian'],
                'thoi_gian_hien_thi' => $this->tinhThoiGianTruoc($hoatDong['thoi_gian'])
            ];
        }, $danhSach);
    }
}
