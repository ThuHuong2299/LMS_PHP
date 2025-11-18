<?php
/**
 * File: LopHocService.php
 * Mục đích: Xử lý logic nghiệp vụ cho lớp học
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/LopHocRepository.php';

class LopHocService extends BaseService {
    
    private $lopHocRepo;
    
    public function __construct() {
        $this->lopHocRepo = new LopHocRepository();
    }
    
    /**
     * Lấy danh sách lớp học của giảng viên
     */
    public function layDanhSachCuaGiangVien($giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($giangVienId)) {
            $this->nemLoi('ID giảng viên không hợp lệ');
        }
        
        // Lấy dữ liệu
        $danhSach = $this->lopHocRepo->timTheoGiangVien($giangVienId);
        
        // Chuyển đổi kiểu dữ liệu
        return array_map(function($lop) {
            return [
                'id' => (int)$lop['id'],
                'ma_lop_hoc' => $lop['ma_lop_hoc'],
                'ten_lop_hoc' => $lop['ten_lop_hoc'],
                'ten_mon_hoc' => $lop['ten_mon_hoc'],
                'trang_thai' => $lop['trang_thai'],
                'so_sinh_vien' => (int)$lop['so_sinh_vien'],
                'so_bai_tap' => (int)$lop['so_bai_tap'],
                'so_bai_kiem_tra' => (int)$lop['so_bai_kiem_tra'],
                'ngay_tao' => $lop['ngay_tao']
            ];
        }, $danhSach);
    }
    
    /**
     * Kiểm tra giảng viên có quyền truy cập lớp học không
     */
    public function kiemTraQuyenTruyCapLop($lopHocId, $giangVienId) {
        // Validate
        if (!$this->kiemTraSoNguyen($lopHocId)) {
            $this->nemLoi('ID lớp học không hợp lệ');
        }
        
        // Kiểm tra quyền
        $coQuyen = $this->lopHocRepo->kiemTraQuyenTruyCap($lopHocId, $giangVienId);
        
        if (!$coQuyen) {
            $this->nemLoi('Bạn không có quyền truy cập lớp học này');
        }
        
        return true;
    }
    
    /**
     * Lấy thông tin chi tiết lớp học
     */
    public function layThongTinLopHoc($lopHocId, $giangVienId) {
        // Kiểm tra quyền trước
        $this->kiemTraQuyenTruyCapLop($lopHocId, $giangVienId);
        
        // Lấy thông tin
        $thongTin = $this->lopHocRepo->timTheoId($lopHocId);
        
        if (!$thongTin) {
            $this->nemLoi('Không tìm thấy lớp học');
        }
        
        // Xóa thông tin nhạy cảm
        unset($thongTin['giang_vien_id']);
        
        return $thongTin;
    }
}
