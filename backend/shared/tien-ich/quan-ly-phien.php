<?php
/**
 * Khởi động session nếu chưa có
 */
function bat_dau_phien() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Lưu thông tin người dùng vào session
 * @param array $nguoi_dung Thông tin người dùng từ database
 */
function luu_phien_dang_nhap($nguoi_dung) {
    bat_dau_phien();
    
    $_SESSION['da_dang_nhap'] = true;
    $_SESSION['user_id'] = $nguoi_dung['id'];
    $_SESSION['ma_nguoi_dung'] = $nguoi_dung['ma_nguoi_dung'];
    $_SESSION['email'] = $nguoi_dung['email'];
    $_SESSION['ho_ten'] = $nguoi_dung['ho_ten'];
    $_SESSION['vai_tro'] = $nguoi_dung['vai_tro'];
    $_SESSION['anh_dai_dien'] = $nguoi_dung['anh_dai_dien'] ?? null;
    $_SESSION['thoi_gian_dang_nhap'] = time();
    
    // Cập nhật lần đăng nhập cuối trong database
    cap_nhat_lan_dang_nhap_cuoi($nguoi_dung['id']);
}

/**
 * Cập nhật thời gian đăng nhập cuối
 * @param int $user_id
 */
function cap_nhat_lan_dang_nhap_cuoi($user_id) {
    require_once __DIR__ . '/../cau-hinh/ket-noi-db.php';
    
    try {
        $db = lay_ket_noi_db();
        if ($db) {
            $sql = "UPDATE nguoi_dung SET lan_dang_nhap_cuoi = NOW() WHERE id = :user_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);
        }
    } catch (Exception $e) {
        error_log("Lỗi cập nhật lần đăng nhập cuối: " . $e->getMessage());
    }
}

/**
 * Kiểm tra người dùng đã đăng nhập chưa
 * @return bool
 */
function da_dang_nhap() {
    bat_dau_phien();
    return isset($_SESSION['da_dang_nhap']) && $_SESSION['da_dang_nhap'] === true;
}

/**
 * Lấy thông tin người dùng từ session
 * @return array|null
 */
function lay_thong_tin_nguoi_dung() {
    bat_dau_phien();
    
    if (!da_dang_nhap()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'ma_nguoi_dung' => $_SESSION['ma_nguoi_dung'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'ho_ten' => $_SESSION['ho_ten'] ?? null,
        'vai_tro' => $_SESSION['vai_tro'] ?? null,
        'anh_dai_dien' => $_SESSION['anh_dai_dien'] ?? null
    ];
}

/**
 * Đăng xuất - xóa session
 */
function dang_xuat() {
    bat_dau_phien();
    
    // Xóa tất cả session variables
    $_SESSION = array();
    
    // Xóa session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Hủy session
    session_destroy();
}

/**
 * Kiểm tra quyền truy cập theo vai trò
 * @param string $vai_tro_yeu_cau 'giang_vien' hoặc 'sinh_vien'
 * @return bool
 */
function kiem_tra_quyen($vai_tro_yeu_cau) {
    bat_dau_phien();
    
    if (!da_dang_nhap()) {
        return false;
    }
    
    return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === $vai_tro_yeu_cau;
}

/**
 * Yêu cầu đăng nhập - redirect nếu chưa đăng nhập
 * @param string $redirect_url URL chuyển hướng nếu chưa đăng nhập
 */
function yeu_cau_dang_nhap($redirect_url = '/public/Login.teacher.html') {
    if (!da_dang_nhap()) {
        header("Location: $redirect_url");
        exit;
    }
}

/**
 * Yêu cầu quyền giảng viên cho API
 * Kiểm tra đăng nhập và quyền, trả về user_id nếu hợp lệ
 * LƯU Ý: Cần gọi session_start() TRƯỚC KHI require file này
 * @return int ID giảng viên
 */
function yeu_cau_quyen_giang_vien() {
    // Session đã được start ở đầu API file
    if (!da_dang_nhap()) {
        // Cần require ham-chung.php để dùng tra_ve_json
        if (!function_exists('tra_ve_json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'thanh_cong' => false,
                'thong_bao' => 'Vui lòng đăng nhập để tiếp tục'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        tra_ve_json(false, null, 'Vui lòng đăng nhập để tiếp tục');
    }
    
    if (!kiem_tra_quyen('giang_vien')) {
        if (!function_exists('tra_ve_json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'thanh_cong' => false,
                'thong_bao' => 'Bạn không có quyền truy cập chức năng này'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        tra_ve_json(false, null, 'Bạn không có quyền truy cập chức năng này');
    }
    
    $nguoi_dung = lay_thong_tin_nguoi_dung();
    return $nguoi_dung['id'];
}

/**
 * Yêu cầu quyền sinh viên cho API
 * LƯU Ý: Cần gọi session_start() TRƯỚC KHI require file này
 * @return int ID sinh viên
 */
function yeu_cau_quyen_sinh_vien() {
    // Session đã được start ở đầu API file
    if (!da_dang_nhap()) {
        if (!function_exists('tra_ve_json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'thanh_cong' => false,
                'thong_bao' => 'Vui lòng đăng nhập để tiếp tục'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        tra_ve_json(false, null, 'Vui lòng đăng nhập để tiếp tục');
    }
    
    if (!kiem_tra_quyen('sinh_vien')) {
        if (!function_exists('tra_ve_json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'thanh_cong' => false,
                'thong_bao' => 'Bạn không có quyền truy cập chức năng này'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        tra_ve_json(false, null, 'Bạn không có quyền truy cập chức năng này');
    }
    
    $nguoi_dung = lay_thong_tin_nguoi_dung();
    return $nguoi_dung['id'];
}
