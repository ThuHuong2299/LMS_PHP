<?php
/**
 * File: BaseController.php
 * Mục đích: Class controller cơ sở cho tất cả controllers
 */

abstract class BaseController {
    
    /**
     * Trả về JSON response thành công
     */
    protected function traVeThanhCong($duLieu = null, $thongBao = '') {
        $this->traVeJson(true, $duLieu, $thongBao);
    }
    
    /**
     * Trả về JSON response lỗi
     */
    protected function traVeLoi($thongBao = 'Có lỗi xảy ra', $duLieu = null) {
        $this->traVeJson(false, $duLieu, $thongBao);
    }
    
    /**
     * Trả về JSON response
     * Format: thanh_cong, du_lieu, thong_bao (format cũ - giữ nguyên để tương thích)
     * Hoặc: status, data, message (format mới)
     */
    protected function traVeJson($thanhCong, $duLieu = null, $thongBao = '') {
        header('Content-Type: application/json; charset=utf-8');
        
        // Hỗ trợ cả 2 format để frontend linh hoạt
        echo json_encode([
            // Format cũ (tương thích với code hiện tại)
            'thanh_cong' => $thanhCong,
            'du_lieu' => $duLieu,
            'thong_bao' => $thongBao,
            // Format mới (chuẩn REST API)
            'status' => $thanhCong ? 'success' : 'error',
            'data' => $duLieu,
            'message' => $thongBao
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Lấy tham số từ GET
     */
    protected function layThamSoGet($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Lấy tham số số nguyên từ GET
     */
    protected function layThamSoGetInt($key, $default = 0) {
        return isset($_GET[$key]) ? (int)$_GET[$key] : $default;
    }
    
    /**
     * Lấy dữ liệu JSON từ request body
     */
    protected function layDuLieuJson() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
    
    /**
     * Lấy tham số từ JSON body
     */
    protected function layThamSoJson($key, $default = null) {
        $data = $this->layDuLieuJson();
        return isset($data[$key]) ? $data[$key] : $default;
    }
    
    /**
     * Lấy tham số số nguyên từ JSON body
     */
    protected function layThamSoJsonInt($key, $default = 0) {
        $data = $this->layDuLieuJson();
        return isset($data[$key]) ? (int)$data[$key] : $default;
    }
    
    /**
     * Lấy ID người dùng hiện tại từ session
     */
    protected function layIdNguoiDung() {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }
    
    /**
     * Lấy vai trò người dùng hiện tại từ session
     */
    protected function layVaiTro() {
        return isset($_SESSION['vai_tro']) ? $_SESSION['vai_tro'] : null;
    }
    
    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    protected function kiemTraDangNhap() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['da_dang_nhap'])) {
            $this->traVeLoi('Vui lòng đăng nhập để tiếp tục');
        }
    }
    
    /**
     * Kiểm tra quyền giảng viên
     */
    protected function kiemTraQuyenGiangVien() {
        $this->kiemTraDangNhap();
        
        if ($this->layVaiTro() !== 'giang_vien') {
            $this->traVeLoi('Bạn không có quyền truy cập chức năng này');
        }
        
        return $this->layIdNguoiDung();
    }
    
    /**
     * Kiểm tra quyền sinh viên
     */
    protected function kiemTraQuyenSinhVien() {
        $this->kiemTraDangNhap();
        
        if ($this->layVaiTro() !== 'sinh_vien') {
            $this->traVeLoi('Bạn không có quyền truy cập chức năng này');
        }
        
        return $this->layIdNguoiDung();
    }
}
