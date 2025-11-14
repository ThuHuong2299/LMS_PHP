<?php
/**
 * File: ham-chung.php
 * Mục đích: Các hàm tiện ích dùng chung
 * Ngày tạo: 13/11/2025
 */

/**
 * Mã hóa mật khẩu sử dụng bcrypt
 * @param string $mat_khau Mật khẩu gốc
 * @return string Mật khẩu đã hash
 */
function ma_hoa_mat_khau($mat_khau) {
    return password_hash($mat_khau, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Xác thực mật khẩu
 * @param string $mat_khau Mật khẩu người dùng nhập
 * @param string $mat_khau_hash Mật khẩu hash trong database
 * @return bool
 */
function xac_thuc_mat_khau($mat_khau, $mat_khau_hash) {
    return password_verify($mat_khau, $mat_khau_hash);
}

/**
 * Validate email
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Làm sạch input từ người dùng
 * @param string $data
 * @return string
 */
function lam_sach_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Trả về JSON response
 * @param bool $thanh_cong
 * @param mixed $du_lieu
 * @param string $thong_bao
 */
function tra_ve_json($thanh_cong, $du_lieu = null, $thong_bao = '') {
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'thanh_cong' => $thanh_cong,
        'thong_bao' => $thong_bao
    ];
    
    if ($du_lieu !== null) {
        $response['du_lieu'] = $du_lieu;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Kiểm tra request method
 * @param string $method GET, POST, PUT, DELETE
 */
function kiem_tra_method($method) {
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        tra_ve_json(false, null, "Method không hợp lệ. Yêu cầu: $method");
    }
}

/**
 * Lấy JSON body từ request
 * @return array|null
 */
function lay_json_body() {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    return $data;
}

/**
 * Tính thời gian đã qua từ timestamp
 * @param string $timestamp Thời gian (MySQL format: Y-m-d H:i:s)
 * @return string Chuỗi hiển thị (VD: "5 phút trước")
 */
function tinh_thoi_gian_da_qua($timestamp) {
    if (empty($timestamp)) {
        return 'Không rõ';
    }
    
    $now = time();
    $time = strtotime($timestamp);
    
    if ($time === false) {
        return 'Không rõ';
    }
    
    $diff = $now - $time;
    
    // Vừa xong (< 1 phút)
    if ($diff < 60) {
        return 'Vừa xong';
    }
    
    // X phút trước (< 1 giờ)
    if ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' phút trước';
    }
    
    // X giờ trước (< 24 giờ)
    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' giờ trước';
    }
    
    // X ngày trước (< 7 ngày)
    if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' ngày trước';
    }
    
    // Hiển thị ngày đầy đủ (>= 7 ngày)
    return date('d/m/Y H:i', $time);
}
