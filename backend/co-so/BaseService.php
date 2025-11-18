<?php
/**
 * File: BaseService.php
 * Mục đích: Class service cơ sở cho tất cả services
 */

abstract class BaseService {
    
    /**
     * Validate email
     */
    protected function kiemTraEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate số nguyên dương
     */
    protected function kiemTraSoNguyen($value, $min = 1) {
        return is_numeric($value) && (int)$value >= $min;
    }
    
    /**
     * Validate chuỗi không rỗng
     */
    protected function kiemTraChuoiKhongRong($value) {
        return !empty(trim($value));
    }
    
    /**
     * Ném exception với thông báo
     */
    protected function nemLoi($thongBao) {
        throw new Exception($thongBao);
    }
    
    /**
     * Tính toán phân trang
     */
    protected function tinhPhanTrang($page, $limit, $maxLimit = 100) {
        $page = max(1, (int)$page);
        $limit = max(1, min($maxLimit, (int)$limit));
        $offset = ($page - 1) * $limit;
        
        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }
    
    /**
     * Tạo thông tin phân trang response
     */
    protected function taoThongTinPhanTrang($page, $limit, $tongSoBanGhi) {
        $tongTrang = ceil($tongSoBanGhi / $limit);
        $batDau = ($page - 1) * $limit + 1;
        $ketThuc = min($page * $limit, $tongSoBanGhi);
        
        return [
            'trang_hien_tai' => $page,
            'tong_trang' => $tongTrang,
            'tong_ban_ghi' => $tongSoBanGhi,
            'so_ban_ghi_moi_trang' => $limit,
            'bat_dau' => $batDau,
            'ket_thuc' => $ketThuc
        ];
    }
    
    /**
     * Chuyển đổi thời gian thành định dạng "... trước"
     */
    protected function tinhThoiGianTruoc($datetime) {
        $now = new DateTime();
        $past = new DateTime($datetime);
        $diff = $now->diff($past);
        
        if ($diff->y > 0) return $diff->y . ' năm trước';
        if ($diff->m > 0) return $diff->m . ' tháng trước';
        if ($diff->d > 0) return $diff->d . ' ngày trước';
        if ($diff->h > 0) return $diff->h . ' giờ trước';
        if ($diff->i > 0) return $diff->i . ' phút trước';
        return 'Vừa xong';
    }
}
