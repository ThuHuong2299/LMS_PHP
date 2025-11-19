<?php
/**
 * File: dang-nhap.php
 * Mục đích: API xử lý đăng nhập sinh viên
 * Method: POST
 * Input: {email, mat_khau}
 * Output: {thanh_cong, du_lieu, thong_bao}
 */

// Khởi động session NGAY ĐẦU
session_start();

// Cho phép CORS với credentials
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Import các file cần thiết
require_once __DIR__ . '/../../shared/cau-hinh/ket-noi-db.php';
require_once __DIR__ . '/../../shared/tien-ich/ham-chung.php';
require_once __DIR__ . '/../../shared/tien-ich/quan-ly-phien.php';

// Kiểm tra method phải là POST
kiem_tra_method('POST');

// Lấy dữ liệu JSON từ request
$du_lieu = lay_json_body();

// Validate dữ liệu đầu vào
if (!$du_lieu || !isset($du_lieu['email']) || !isset($du_lieu['mat_khau'])) {
    tra_ve_json(false, null, 'Vui lòng nhập đầy đủ email và mật khẩu');
}

$email = lam_sach_input($du_lieu['email']);
$mat_khau = $du_lieu['mat_khau']; // Không làm sạch password vì cần giữ nguyên

// Validate email format
if (!validate_email($email)) {
    tra_ve_json(false, null, 'Email không hợp lệ');
}

try {
    // Kết nối database
    $db = lay_ket_noi_db();
    
    if (!$db) {
        tra_ve_json(false, null, 'Không thể kết nối database');
    }
    
    // Truy vấn tìm sinh viên theo email
    $sql = "SELECT 
                id, 
                ma_nguoi_dung, 
                ten_dang_nhap,
                email, 
                mat_khau_hash, 
                ho_ten, 
                anh_dai_dien,
                vai_tro,
                trang_thai
            FROM nguoi_dung 
            WHERE email = :email 
            AND vai_tro = 'sinh_vien'
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute(['email' => $email]);
    
    $nguoi_dung = $stmt->fetch();
    
    // Kiểm tra người dùng có tồn tại không
    if (!$nguoi_dung) {
        tra_ve_json(false, null, 'Email hoặc mật khẩu không đúng');
    }
    
    // Kiểm tra tài khoản có bị khóa không
    if ($nguoi_dung['trang_thai'] !== 'hoat_dong') {
        tra_ve_json(false, null, 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên');
    }
    
    // Xác thực mật khẩu (so sánh trực tiếp - không dùng hash)
    if ($mat_khau !== $nguoi_dung['mat_khau_hash']) {
        tra_ve_json(false, null, 'Email hoặc mật khẩu không đúng');
    }
    
    // Đăng nhập thành công - Lưu session
    luu_phien_dang_nhap($nguoi_dung);
    
    // Chuẩn bị dữ liệu trả về (không trả password hash)
    // Nếu không có avatar, dùng avatar mặc định
    $anh_dai_dien = $nguoi_dung['anh_dai_dien'];
    if (empty($anh_dai_dien)) {
        $anh_dai_dien = '/public/student/CSS/avatar-sv.webp';
    }
    
    $du_lieu_tra_ve = [
        'id' => $nguoi_dung['id'],
        'ma_nguoi_dung' => $nguoi_dung['ma_nguoi_dung'],
        'ten_dang_nhap' => $nguoi_dung['ten_dang_nhap'],
        'email' => $nguoi_dung['email'],
        'ho_ten' => $nguoi_dung['ho_ten'],
        'anh_dai_dien' => $anh_dai_dien,
        'vai_tro' => $nguoi_dung['vai_tro']
    ];
    
    // Trả về kết quả thành công
    tra_ve_json(
        true, 
        $du_lieu_tra_ve, 
        'Đăng nhập thành công! Chào mừng ' . $nguoi_dung['ho_ten']
    );
    
} catch (PDOException $e) {
    error_log("Lỗi database trong dang-nhap.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
} catch (Exception $e) {
    error_log("Lỗi trong dang-nhap.php: " . $e->getMessage());
    tra_ve_json(false, null, 'Có lỗi xảy ra. Vui lòng thử lại sau');
}
