<?php
/**
 * File: BinhLuanService.php
 * Mục đích: Xử lý logic nghiệp vụ cho bình luận bài giảng
 */

require_once __DIR__ . '/../co-so/BaseService.php';
require_once __DIR__ . '/../kho-du-lieu/BinhLuanRepository.php';

class BinhLuanService extends BaseService {
    
    private $binhLuanRepo;
    
    public function __construct() {
        $this->binhLuanRepo = new BinhLuanRepository();
    }
    
    /**
     * Lấy danh sách bình luận với phân trang
     */
    public function layDanhSachBinhLuan($baiGiangId, $page = 1, $limit = 20) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiGiangId) || $baiGiangId <= 0) {
            $this->nemLoi('ID bài giảng không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($page) || $page < 1) {
            $page = 1;
        }
        
        if (!$this->kiemTraSoNguyen($limit) || $limit < 1 || $limit > 100) {
            $limit = 20;
        }
        
        // Kiểm tra bài giảng tồn tại
        if (!$this->binhLuanRepo->kiemTraBaiGiangTonTai($baiGiangId)) {
            $this->nemLoi('Bài giảng không tồn tại');
        }
        
        // Tính offset
        $offset = ($page - 1) * $limit;
        
        // Lấy dữ liệu
        $binhLuan = $this->binhLuanRepo->layBinhLuanCoPhanhoi($baiGiangId, $offset, $limit);
        $tongBinhLuan = $this->binhLuanRepo->demTongBinhLuan($baiGiangId);
        
        // Format dữ liệu
        foreach ($binhLuan as &$bl) {
            $bl['id'] = (int)$bl['id'];
            $bl['bai_giang_id'] = (int)$bl['bai_giang_id'];
            $bl['nguoi_gui_id'] = (int)$bl['nguoi_gui_id'];
            $bl['so_phan_hoi'] = (int)$bl['so_phan_hoi'];
            
            // Format phản hồi
            foreach ($bl['phan_hoi'] as &$ph) {
                $ph['id'] = (int)$ph['id'];
                $ph['nguoi_gui_id'] = (int)$ph['nguoi_gui_id'];
                $ph['binh_luan_cha_id'] = (int)$ph['binh_luan_cha_id'];
            }
        }
        
        return [
            'binh_luan' => $binhLuan,
            'pagination' => [
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total' => $tongBinhLuan,
                'total_pages' => ceil($tongBinhLuan / $limit)
            ]
        ];
    }
    
    /**
     * Thêm bình luận mới
     */
    public function themBinhLuan($baiGiangId, $nguoiGuiId, $noiDung, $binhLuanChaId = null) {
        // Validate
        if (!$this->kiemTraSoNguyen($baiGiangId) || $baiGiangId <= 0) {
            $this->nemLoi('ID bài giảng không hợp lệ');
        }
        
        if (!$this->kiemTraSoNguyen($nguoiGuiId) || $nguoiGuiId <= 0) {
            $this->nemLoi('ID người gửi không hợp lệ');
        }
        
        if (empty($noiDung) || !is_string($noiDung)) {
            $this->nemLoi('Nội dung bình luận không được để trống');
        }
        
        // Trim và kiểm tra độ dài
        $noiDung = trim($noiDung);
        if (mb_strlen($noiDung) < 1) {
            $this->nemLoi('Nội dung bình luận quá ngắn');
        }
        
        if (mb_strlen($noiDung) > 5000) {
            $this->nemLoi('Nội dung bình luận quá dài (tối đa 5000 ký tự)');
        }
        
        // Kiểm tra bài giảng tồn tại
        if (!$this->binhLuanRepo->kiemTraBaiGiangTonTai($baiGiangId)) {
            $this->nemLoi('Bài giảng không tồn tại');
        }
        
        // Kiểm tra bình luận cha (nếu là phản hồi)
        if ($binhLuanChaId !== null) {
            if (!$this->kiemTraSoNguyen($binhLuanChaId) || $binhLuanChaId <= 0) {
                $this->nemLoi('ID bình luận cha không hợp lệ');
            }
            
            if (!$this->binhLuanRepo->kiemTraBinhLuanChaTonTai($binhLuanChaId)) {
                $this->nemLoi('Bình luận cha không tồn tại');
            }
        }
        
        // Thêm bình luận
        $data = [
            'bai_giang_id' => $baiGiangId,
            'nguoi_gui_id' => $nguoiGuiId,
            'binh_luan_cha_id' => $binhLuanChaId,
            'noi_dung' => $noiDung
        ];
        
        $result = $this->binhLuanRepo->themBinhLuan($data);
        
        if (!$result) {
            $this->nemLoi('Không thể thêm bình luận');
        }
        
        // Lấy ID bình luận vừa thêm
        $binhLuanId = $this->binhLuanRepo->layIdCuoi();
        
        // Lấy thông tin bình luận vừa thêm
        $binhLuanMoi = $this->binhLuanRepo->layBinhLuanTheoId($binhLuanId);
        
        return $binhLuanMoi;
    }
    
    /**
     * Xóa bình luận
     */
    public function xoaBinhLuan($binhLuanId, $nguoiGuiId) {
        // Validate
        if (!$this->kiemTraSoNguyen($binhLuanId) || $binhLuanId <= 0) {
            $this->nemLoi('ID bình luận không hợp lệ');
        }
        
        // Kiểm tra quyền sở hữu
        if (!$this->binhLuanRepo->kiemTraQuyenSoHuu($binhLuanId, $nguoiGuiId)) {
            $this->nemLoi('Bạn không có quyền xóa bình luận này');
        }
        
        // Xóa bình luận (soft delete)
        $result = $this->binhLuanRepo->xoaBinhLuan($binhLuanId);
        
        if (!$result) {
            $this->nemLoi('Không thể xóa bình luận');
        }
        
        return true;
    }
    
    /**
     * Cập nhật bình luận
     */
    public function capNhatBinhLuan($binhLuanId, $nguoiGuiId, $noiDung) {
        // Validate
        if (!$this->kiemTraSoNguyen($binhLuanId) || $binhLuanId <= 0) {
            $this->nemLoi('ID bình luận không hợp lệ');
        }
        
        if (empty($noiDung) || !is_string($noiDung)) {
            $this->nemLoi('Nội dung bình luận không được để trống');
        }
        
        $noiDung = trim($noiDung);
        if (mb_strlen($noiDung) < 1) {
            $this->nemLoi('Nội dung bình luận quá ngắn');
        }
        
        if (mb_strlen($noiDung) > 5000) {
            $this->nemLoi('Nội dung bình luận quá dài');
        }
        
        // Kiểm tra quyền sở hữu
        if (!$this->binhLuanRepo->kiemTraQuyenSoHuu($binhLuanId, $nguoiGuiId)) {
            $this->nemLoi('Bạn không có quyền sửa bình luận này');
        }
        
        // Cập nhật
        $result = $this->binhLuanRepo->capNhatBinhLuan($binhLuanId, $noiDung);
        
        if (!$result) {
            $this->nemLoi('Không thể cập nhật bình luận');
        }
        
        // Lấy thông tin bình luận đã cập nhật
        return $this->binhLuanRepo->layBinhLuanTheoId($binhLuanId);
    }
}
