<?php
/**
 * File: BaiGiangService.php
 * Mục đích: Xử lý logic nghiệp vụ cho bài giảng
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/BaiGiangRepository.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class BaiGiangService extends BaseService {
    
    private $baiGiangRepo;
    private $lopHocRepo;
    
    public function __construct() {
        $this->baiGiangRepo = new BaiGiangRepository();
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy thông tin lớp và danh sách bài giảng
     */
    public function layBaiGiangTheoLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền truy cập
        if (!$this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId)) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        // Lấy dữ liệu
        $duLieu = $this->baiGiangRepo->layBaiGiangTheoLop($lopHocId);
        
        if (!$duLieu) {
            $this->nemLoi('Không tìm thấy lớp học');
        }
        
        // Xóa thông tin nhạy cảm
        unset($duLieu['thong_tin_lop']['giang_vien_id']);
        
        return $duLieu;
    }
}
