SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tạo database
DROP DATABASE IF EXISTS lms_hoc_tap;
CREATE DATABASE lms_hoc_tap 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE lms_hoc_tap;

-- =====================================================
-- 1. BẢNG NGƯỜI DÙNG
-- =====================================================

CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID người dùng',
    ma_nguoi_dung VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã sinh viên (VD: 23D192001) hoặc mã giảng viên',
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL COMMENT 'Tên đăng nhập',
    email VARCHAR(100) UNIQUE NOT NULL COMMENT 'Email',
    mat_khau_hash VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa',
    ho_ten VARCHAR(100) NOT NULL COMMENT 'Họ và tên đầy đủ',
    anh_dai_dien VARCHAR(255) NULL COMMENT 'Đường dẫn ảnh đại diện',
    vai_tro ENUM('giang_vien', 'sinh_vien') DEFAULT 'sinh_vien' COMMENT 'Vai trò',
    trang_thai ENUM('hoat_dong', 'khong_hoat_dong') DEFAULT 'hoat_dong' COMMENT 'Trạng thái tài khoản',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo tài khoản',
    lan_dang_nhap_cuoi TIMESTAMP NULL COMMENT 'Lần đăng nhập cuối',
    
    INDEX idx_ma_nguoi_dung (ma_nguoi_dung),
    INDEX idx_email (email),
    INDEX idx_vai_tro (vai_tro)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng quản lý người dùng (giảng viên, sinh viên)';

-- =====================================================
-- 2. BẢNG MÔN HỌC & LỚP HỌC
-- =====================================================

CREATE TABLE mon_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID môn học',
    ma_mon_hoc VARCHAR(20) UNIQUE NOT NULL COMMENT 'Mã môn học (VD: eCIT2320)',
    ten_mon_hoc VARCHAR(200) NOT NULL COMMENT 'Tên môn học',
    mo_ta TEXT NULL COMMENT 'Mô tả môn học',
    so_tin_chi INT DEFAULT 3 COMMENT 'Số tín chỉ',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    INDEX idx_ma_mon_hoc (ma_mon_hoc)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng môn học (một môn có thể mở nhiều lớp)';

CREATE TABLE lop_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID lớp học',
    ma_lop_hoc VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã lớp (VD: 213_eCIT2320_09)',
    mon_hoc_id INT NOT NULL COMMENT 'ID môn học',
    giang_vien_id INT NOT NULL COMMENT 'ID giảng viên phụ trách',
    ten_lop_hoc VARCHAR(200) NULL COMMENT 'Tên lớp tùy chỉnh (nếu có)',
    so_sinh_vien_toi_da INT DEFAULT 50 COMMENT 'Số sinh viên tối đa',
    trang_thai ENUM('dang_mo', 'da_dong', 'tam_khoa') DEFAULT 'dang_mo' COMMENT 'Trạng thái lớp',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo lớp',
    
    FOREIGN KEY (mon_hoc_id) REFERENCES mon_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (giang_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    INDEX idx_ma_lop_hoc (ma_lop_hoc),
    INDEX idx_mon_hoc_id (mon_hoc_id),
    INDEX idx_giang_vien_id (giang_vien_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng lớp học cụ thể (instance của môn học)';

CREATE TABLE sinh_vien_lop_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID đăng ký',
    sinh_vien_id INT NOT NULL COMMENT 'ID sinh viên',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    mon_hoc_id INT NOT NULL COMMENT 'ID môn học (để kiểm tra ràng buộc)',
    ngay_dang_ky TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày đăng ký',
    trang_thai ENUM('dang_hoc', 'da_hoan_thanh', 'da_huy') DEFAULT 'dang_hoc' COMMENT 'Trạng thái',
    
    FOREIGN KEY (sinh_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (mon_hoc_id) REFERENCES mon_hoc(id) ON DELETE CASCADE,
    
    -- Ràng buộc: Sinh viên học nhiều môn, nhưng mỗi môn chỉ học 1 lớp duy nhất
    UNIQUE KEY unique_sinh_vien_mon_hoc (sinh_vien_id, mon_hoc_id),
    
    INDEX idx_sinh_vien_id (sinh_vien_id),
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_mon_hoc_id (mon_hoc_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Danh sách sinh viên trong lớp - Ràng buộc: 1 sinh viên chỉ học 1 lớp cho mỗi môn';

-- =====================================================
-- 3. BẢNG NỘI DUNG HỌC TẬP
-- =====================================================

CREATE TABLE chuong (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID chương',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    so_thu_tu_chuong INT NOT NULL COMMENT 'Số thứ tự chương (1, 2, 3...)',
    ten_chuong VARCHAR(200) NOT NULL COMMENT 'Tên chương',
    muc_tieu TEXT NULL COMMENT 'Mục tiêu của chương',
    noi_dung TEXT NULL COMMENT 'Nội dung chương',
    thu_tu_sap_xep INT DEFAULT 0 COMMENT 'Thứ tự sắp xếp',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_chuong_lop (lop_hoc_id, so_thu_tu_chuong),
    
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_thu_tu (lop_hoc_id, thu_tu_sap_xep)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng chương học trong lớp';

CREATE TABLE bai_giang (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bài giảng',
    chuong_id INT NOT NULL COMMENT 'ID chương',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    so_thu_tu_bai DECIMAL(3,1) NOT NULL COMMENT 'Số thứ tự bài (1.1, 1.2, 2.1...)',
    tieu_de VARCHAR(200) NOT NULL COMMENT 'Tiêu đề bài giảng',
    duong_dan_video VARCHAR(500) NULL COMMENT 'Đường dẫn video',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    FOREIGN KEY (chuong_id) REFERENCES chuong(id) ON DELETE CASCADE,
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_bai_chuong (chuong_id, so_thu_tu_bai),
    
    INDEX idx_chuong_id (chuong_id),
    INDEX idx_lop_hoc_id (lop_hoc_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng bài giảng (chỉ hỗ trợ video)';

-- =====================================================
-- 4. BẢNG BÀI TẬP (TEXT)
-- =====================================================

CREATE TABLE bai_tap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bài tập',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    chuong_id INT NULL COMMENT 'ID chương giao (nếu có)',
    bai_giang_id INT NULL COMMENT 'ID bài giảng (nếu bài tập thuộc bài giảng cụ thể)',
    tieu_de VARCHAR(200) NOT NULL COMMENT 'Tiêu đề bài tập',
    mo_ta TEXT NULL COMMENT 'Mô tả/hướng dẫn bài tập',
    han_nop DATETIME NULL COMMENT 'Hạn nộp (ngày + giờ)',
    diem_toi_da DECIMAL(5,2) DEFAULT 10.00 COMMENT 'Điểm tối đa',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (chuong_id) REFERENCES chuong(id) ON DELETE SET NULL,
    FOREIGN KEY (bai_giang_id) REFERENCES bai_giang(id) ON DELETE SET NULL,
    
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_chuong_id (chuong_id),
    INDEX idx_bai_giang_id (bai_giang_id),
    INDEX idx_han_nop (han_nop)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng bài tập dạng text - Không phải bài giảng nào cũng có bài tập';

CREATE TABLE cau_hoi_bai_tap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID câu hỏi',
    bai_tap_id INT NOT NULL COMMENT 'ID bài tập',
    noi_dung_cau_hoi TEXT NOT NULL COMMENT 'Nội dung câu hỏi',
    mo_ta TEXT NULL COMMENT 'Miêu tả ngắn',
    diem DECIMAL(5,2) DEFAULT 1.00 COMMENT 'Điểm cho câu hỏi này',
    
    FOREIGN KEY (bai_tap_id) REFERENCES bai_tap(id) ON DELETE CASCADE,
    
    INDEX idx_bai_tap_id (bai_tap_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Câu hỏi text trong bài tập';

CREATE TABLE bai_lam (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bài làm',
    bai_tap_id INT NOT NULL COMMENT 'ID bài tập',
    sinh_vien_id INT NOT NULL COMMENT 'ID sinh viên',
    trang_thai ENUM('chua_lam', 'dang_lam', 'da_nop', 'da_cham') DEFAULT 'chua_lam' COMMENT 'Trạng thái làm bài',
    diem DECIMAL(5,2) NULL COMMENT 'Tổng điểm (NULL nếu chưa chấm)',
    thoi_gian_bat_dau TIMESTAMP NULL COMMENT 'Thời gian bắt đầu làm bài',
    thoi_gian_nop TIMESTAMP NULL COMMENT 'Thời gian nộp bài',
    thoi_gian_cham TIMESTAMP NULL COMMENT 'Thời gian giảng viên chấm xong',
    nguoi_cham_id INT NULL COMMENT 'ID giảng viên chấm bài',
    
    FOREIGN KEY (bai_tap_id) REFERENCES bai_tap(id) ON DELETE CASCADE,
    FOREIGN KEY (sinh_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_cham_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_bai_lam (bai_tap_id, sinh_vien_id),
    
    INDEX idx_bai_tap_id (bai_tap_id),
    INDEX idx_sinh_vien_id (sinh_vien_id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bài làm của sinh viên - Mỗi sinh viên chỉ làm 1 lần';

CREATE TABLE tra_loi_bai_tap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID trả lời',
    bai_lam_id INT NOT NULL COMMENT 'ID bài làm',
    cau_hoi_id INT NOT NULL COMMENT 'ID câu hỏi',
    noi_dung_tra_loi TEXT NOT NULL COMMENT 'Nội dung trả lời của sinh viên',
    diem DECIMAL(5,2) NULL COMMENT 'Điểm cho câu này (NULL nếu chưa chấm)',
    thoi_gian_tra_loi TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian trả lời',
    
    FOREIGN KEY (bai_lam_id) REFERENCES bai_lam(id) ON DELETE CASCADE,
    FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi_bai_tap(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_tra_loi (bai_lam_id, cau_hoi_id),
    
    INDEX idx_bai_lam_id (bai_lam_id),
    INDEX idx_cau_hoi_id (cau_hoi_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Trả lời của sinh viên cho từng câu hỏi';

CREATE TABLE binh_luan_bai_tap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bình luận',
    bai_lam_id INT NOT NULL COMMENT 'ID bài làm',
    nguoi_gui_id INT NOT NULL COMMENT 'ID người gửi (GV hoặc SV)',
    noi_dung TEXT NOT NULL COMMENT 'Nội dung bình luận',
    thoi_gian_gui TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian gửi',
    
    FOREIGN KEY (bai_lam_id) REFERENCES bai_lam(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    INDEX idx_bai_lam_id (bai_lam_id),
    INDEX idx_thoi_gian (thoi_gian_gui)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Comment trao đổi giữa GV và SV về bài tập';

-- =====================================================
-- 5. BẢNG BÀI KIỂM TRA (TRẮC NGHIỆM)
-- =====================================================

CREATE TABLE bai_kiem_tra (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bài kiểm tra',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    chuong_id INT NULL COMMENT 'ID chương (nếu là kiểm tra cuối chương)',
    tieu_de VARCHAR(200) NOT NULL COMMENT 'Tiêu đề bài kiểm tra',
    mo_ta TEXT NULL COMMENT 'Mô tả',
    thoi_luong INT NOT NULL COMMENT 'Thời lượng làm bài (phút)',
    thoi_gian_bat_dau DATETIME NOT NULL COMMENT 'Thời gian bắt đầu (ngày + giờ)',
    thoi_gian_ket_thuc DATETIME NULL COMMENT 'Thời gian kết thúc (tự động tính)',
    diem_toi_da DECIMAL(5,2) DEFAULT 10.00 COMMENT 'Điểm tối đa',
    cho_phep_lam_lai TINYINT(1) DEFAULT 0 COMMENT 'Cho phép sinh viên làm lại bài kiểm tra (0=không, 1=có)',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (chuong_id) REFERENCES chuong(id) ON DELETE SET NULL,
    
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_chuong_id (chuong_id),
    INDEX idx_thoi_gian (thoi_gian_bat_dau)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bài kiểm tra trắc nghiệm (cuối chương hoặc giữa kỳ)';

CREATE TABLE cau_hoi_trac_nghiem (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID câu hỏi',
    bai_kiem_tra_id INT NOT NULL COMMENT 'ID bài kiểm tra',
    thu_tu INT NOT NULL COMMENT 'Thứ tự câu hỏi (1, 2, 3...)',
    noi_dung_cau_hoi TEXT NOT NULL COMMENT 'Nội dung câu hỏi',
    diem DECIMAL(5,2) DEFAULT 1.00 COMMENT 'Điểm cho câu này',
    
    FOREIGN KEY (bai_kiem_tra_id) REFERENCES bai_kiem_tra(id) ON DELETE CASCADE,
    
    INDEX idx_bai_kiem_tra_id (bai_kiem_tra_id),
    INDEX idx_thu_tu (bai_kiem_tra_id, thu_tu)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Câu hỏi trắc nghiệm trong bài kiểm tra';

CREATE TABLE lua_chon_cau_hoi (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID lựa chọn',
    cau_hoi_id INT NOT NULL COMMENT 'ID câu hỏi',
    thu_tu INT NOT NULL COMMENT 'Thứ tự lựa chọn (A, B, C, D)',
    noi_dung_lua_chon TEXT NOT NULL COMMENT 'Nội dung lựa chọn',
    la_dap_an_dung BOOLEAN DEFAULT FALSE COMMENT 'Đáp án đúng hay không',
    
    FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi_trac_nghiem(id) ON DELETE CASCADE,
    
    INDEX idx_cau_hoi_id (cau_hoi_id),
    INDEX idx_thu_tu (cau_hoi_id, thu_tu)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Các lựa chọn đáp án cho câu hỏi trắc nghiệm';

CREATE TABLE bai_lam_kiem_tra (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bài làm',
    bai_kiem_tra_id INT NOT NULL COMMENT 'ID bài kiểm tra',
    sinh_vien_id INT NOT NULL COMMENT 'ID sinh viên',
    trang_thai ENUM('chua_lam', 'dang_lam', 'da_nop', 'da_cham') DEFAULT 'chua_lam' COMMENT 'Trạng thái',
    diem DECIMAL(5,2) NULL COMMENT 'Điểm số',
    so_cau_dung INT DEFAULT 0 COMMENT 'Số câu trả lời đúng',
    tong_so_cau INT DEFAULT 0 COMMENT 'Tổng số câu',
    thoi_gian_bat_dau TIMESTAMP NULL COMMENT 'Thời gian bắt đầu làm',
    thoi_gian_nop TIMESTAMP NULL COMMENT 'Thời gian nộp bài',
    thoi_gian_lam_bai INT DEFAULT 0 COMMENT 'Thời gian làm bài thực tế (phút)',
    
    FOREIGN KEY (bai_kiem_tra_id) REFERENCES bai_kiem_tra(id) ON DELETE CASCADE,
    FOREIGN KEY (sinh_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    -- Không cho phép làm lại (trừ khi giảng viên cho phép)
    UNIQUE KEY unique_bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id),
    
    INDEX idx_bai_kiem_tra_id (bai_kiem_tra_id),
    INDEX idx_sinh_vien_id (sinh_vien_id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bài làm kiểm tra - Không cho phép làm lại (ngoại trừ trường hợp đặc biệt)';

CREATE TABLE chi_tiet_tra_loi (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID chi tiết',
    bai_lam_kiem_tra_id INT NOT NULL COMMENT 'ID bài làm',
    cau_hoi_id INT NOT NULL COMMENT 'ID câu hỏi',
    lua_chon_id INT NULL COMMENT 'ID lựa chọn đã chọn (NULL nếu chưa chọn)',
    dung_hay_sai BOOLEAN NULL COMMENT 'Đúng hay sai (NULL nếu chưa chấm)',
    thoi_gian_tra_loi TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian trả lời',
    
    FOREIGN KEY (bai_lam_kiem_tra_id) REFERENCES bai_lam_kiem_tra(id) ON DELETE CASCADE,
    FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi_trac_nghiem(id) ON DELETE CASCADE,
    FOREIGN KEY (lua_chon_id) REFERENCES lua_chon_cau_hoi(id) ON DELETE CASCADE,
    
    -- Mỗi câu chỉ chọn 1 lần
    UNIQUE KEY unique_chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id),
    
    INDEX idx_bai_lam_kiem_tra_id (bai_lam_kiem_tra_id),
    INDEX idx_cau_hoi_id (cau_hoi_id)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Chi tiết trả lời từng câu hỏi trắc nghiệm';

-- =====================================================
-- 6. BẢNG THÔNG BÁO & TÀI LIỆU
-- =====================================================

CREATE TABLE thong_bao_lop_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID thông báo',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    nguoi_gui_id INT NOT NULL COMMENT 'ID giảng viên gửi',
    tieu_de VARCHAR(200) NOT NULL COMMENT 'Tiêu đề thông báo',
    noi_dung TEXT NOT NULL COMMENT 'Nội dung thông báo',
    so_luong_da_xem INT DEFAULT 0 COMMENT 'Số lượng sinh viên đã xem',
    thoi_gian_gui TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian gửi',
    
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_thoi_gian (thoi_gian_gui)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Thông báo gửi trực tiếp đến lớp học';

CREATE TABLE tai_lieu_lop_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID tài liệu',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    ten_tai_lieu VARCHAR(200) NOT NULL COMMENT 'Tên tài liệu',
    loai_file ENUM('pdf', 'docx', 'pptx', 'xlsx') NOT NULL COMMENT 'Loại file',
    duong_dan_file VARCHAR(500) NOT NULL COMMENT 'Đường dẫn file',
    ten_file_goc VARCHAR(255) NOT NULL COMMENT 'Tên file gốc',
    kich_thuoc_file BIGINT DEFAULT 0 COMMENT 'Kích thước file (bytes)',
    nguoi_upload_id INT NOT NULL COMMENT 'ID giảng viên upload',
    thoi_gian_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian upload',
    
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_upload_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_loai_file (loai_file)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Tài liệu tham khảo cho lớp học (không tracking số lần tải)';

-- =====================================================
-- 7. BẢNG TIẾN ĐỘ HỌC TẬP
-- =====================================================

CREATE TABLE tien_do_hoc_tap (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID tiến độ',
    sinh_vien_id INT NOT NULL COMMENT 'ID sinh viên',
    lop_hoc_id INT NOT NULL COMMENT 'ID lớp học',
    loai_noi_dung ENUM('bai_giang', 'bai_tap', 'bai_kiem_tra') NOT NULL COMMENT 'Loại nội dung',
    noi_dung_id INT NOT NULL COMMENT 'ID của bài giảng/bài tập/bài kiểm tra',
    da_hoan_thanh BOOLEAN DEFAULT FALSE COMMENT 'Đã hoàn thành chưa',
    phan_tram_hoan_thanh DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Phần trăm hoàn thành (0-100)',
    lan_cap_nhat_cuoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last Updated',
    
    FOREIGN KEY (sinh_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (lop_hoc_id) REFERENCES lop_hoc(id) ON DELETE CASCADE,
    
    -- Mỗi nội dung chỉ track 1 lần
    UNIQUE KEY unique_tien_do (sinh_vien_id, lop_hoc_id, loai_noi_dung, noi_dung_id),
    
    INDEX idx_sinh_vien_id (sinh_vien_id),
    INDEX idx_lop_hoc_id (lop_hoc_id),
    INDEX idx_loai_noi_dung (loai_noi_dung),
    INDEX idx_da_hoan_thanh (da_hoan_thanh)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Theo dõi tiến độ học tập (bài giảng, bài tập, bài kiểm tra)';

-- =====================================================
-- 8. VIEWS - THỐNG KÊ VÀ BÁO CÁO
-- =====================================================

-- View: Thống kê lớp học
CREATE VIEW v_thong_ke_lop_hoc AS
SELECT 
    lh.id,
    lh.ma_lop_hoc,
    mh.ten_mon_hoc,
    nd.ho_ten AS ten_giang_vien,
    COUNT(DISTINCT svlh.sinh_vien_id) AS so_sinh_vien,
    lh.so_sinh_vien_toi_da,
    COUNT(DISTINCT c.id) AS so_chuong,
    COUNT(DISTINCT bg.id) AS so_bai_giang,
    COUNT(DISTINCT bt.id) AS so_bai_tap,
    COUNT(DISTINCT bkt.id) AS so_bai_kiem_tra,
    lh.trang_thai,
    lh.ngay_tao
FROM lop_hoc lh
JOIN mon_hoc mh ON lh.mon_hoc_id = mh.id
JOIN nguoi_dung nd ON lh.giang_vien_id = nd.id
LEFT JOIN sinh_vien_lop_hoc svlh ON lh.id = svlh.lop_hoc_id AND svlh.trang_thai = 'dang_hoc'
LEFT JOIN chuong c ON lh.id = c.lop_hoc_id
LEFT JOIN bai_giang bg ON lh.id = bg.lop_hoc_id
LEFT JOIN bai_tap bt ON lh.id = bt.lop_hoc_id
LEFT JOIN bai_kiem_tra bkt ON lh.id = bkt.lop_hoc_id
GROUP BY lh.id, lh.ma_lop_hoc, mh.ten_mon_hoc, nd.ho_ten, lh.so_sinh_vien_toi_da, lh.trang_thai, lh.ngay_tao;

-- View: Tiến độ sinh viên trong lớp
CREATE VIEW v_tien_do_sinh_vien AS
SELECT 
    svlh.sinh_vien_id,
    nd.ho_ten AS ten_sinh_vien,
    nd.ma_nguoi_dung AS ma_sinh_vien,
    svlh.lop_hoc_id,
    lh.ma_lop_hoc,
    mh.ten_mon_hoc,
    COUNT(CASE WHEN td.loai_noi_dung = 'bai_giang' AND td.da_hoan_thanh = TRUE THEN 1 END) AS so_bai_giang_hoan_thanh,
    COUNT(CASE WHEN td.loai_noi_dung = 'bai_tap' AND td.da_hoan_thanh = TRUE THEN 1 END) AS so_bai_tap_hoan_thanh,
    COUNT(CASE WHEN td.loai_noi_dung = 'bai_kiem_tra' AND td.da_hoan_thanh = TRUE THEN 1 END) AS so_bai_kiem_tra_hoan_thanh,
    MAX(td.lan_cap_nhat_cuoi) AS last_updated,
    svlh.trang_thai
FROM sinh_vien_lop_hoc svlh
JOIN nguoi_dung nd ON svlh.sinh_vien_id = nd.id
JOIN lop_hoc lh ON svlh.lop_hoc_id = lh.id
JOIN mon_hoc mh ON svlh.mon_hoc_id = mh.id
LEFT JOIN tien_do_hoc_tap td ON svlh.sinh_vien_id = td.sinh_vien_id AND svlh.lop_hoc_id = td.lop_hoc_id
GROUP BY svlh.sinh_vien_id, nd.ho_ten, nd.ma_nguoi_dung, svlh.lop_hoc_id, lh.ma_lop_hoc, mh.ten_mon_hoc, svlh.trang_thai;

-- View: Bài chờ chấm của giảng viên
CREATE VIEW v_bai_cho_cham AS
SELECT 
    lh.giang_vien_id,
    nd.ho_ten AS ten_giang_vien,
    'bai_tap' AS loai_bai,
    bt.id AS bai_id,
    bt.tieu_de,
    lh.ma_lop_hoc,
    COUNT(bl.id) AS so_bai_cho_cham
FROM bai_tap bt
JOIN lop_hoc lh ON bt.lop_hoc_id = lh.id
JOIN nguoi_dung nd ON lh.giang_vien_id = nd.id
LEFT JOIN bai_lam bl ON bt.id = bl.bai_tap_id AND bl.trang_thai = 'da_nop'
GROUP BY lh.giang_vien_id, nd.ho_ten, bt.id, bt.tieu_de, lh.ma_lop_hoc

UNION ALL

SELECT 
    lh.giang_vien_id,
    nd.ho_ten AS ten_giang_vien,
    'bai_kiem_tra' AS loai_bai,
    bkt.id AS bai_id,
    bkt.tieu_de,
    lh.ma_lop_hoc,
    COUNT(blkt.id) AS so_bai_cho_cham
FROM bai_kiem_tra bkt
JOIN lop_hoc lh ON bkt.lop_hoc_id = lh.id
JOIN nguoi_dung nd ON lh.giang_vien_id = nd.id
LEFT JOIN bai_lam_kiem_tra blkt ON bkt.id = blkt.bai_kiem_tra_id AND blkt.trang_thai = 'da_nop'
GROUP BY lh.giang_vien_id, nd.ho_ten, bkt.id, bkt.tieu_de, lh.ma_lop_hoc;

-- =====================================================
-- 9. TRIGGERS - TỰ ĐỘNG CẬP NHẬT DỮ LIỆU
-- =====================================================

DELIMITER //

-- Trigger: Tự động tính thời gian kết thúc bài kiểm tra
CREATE TRIGGER trg_tinh_thoi_gian_ket_thuc_bai_kiem_tra
BEFORE INSERT ON bai_kiem_tra
FOR EACH ROW
BEGIN
    IF NEW.thoi_gian_ket_thuc IS NULL THEN
        SET NEW.thoi_gian_ket_thuc = DATE_ADD(NEW.thoi_gian_bat_dau, INTERVAL NEW.thoi_luong MINUTE);
    END IF;
END//

-- Trigger: Tự động thêm mon_hoc_id vào sinh_vien_lop_hoc
CREATE TRIGGER trg_them_mon_hoc_id_sinh_vien_lop_hoc
BEFORE INSERT ON sinh_vien_lop_hoc
FOR EACH ROW
BEGIN
    DECLARE v_mon_hoc_id INT;
    SELECT mon_hoc_id INTO v_mon_hoc_id FROM lop_hoc WHERE id = NEW.lop_hoc_id;
    SET NEW.mon_hoc_id = v_mon_hoc_id;
END//

-- Trigger: Tự động tính điểm bài làm kiểm tra
CREATE TRIGGER trg_tinh_diem_bai_kiem_tra
AFTER INSERT ON chi_tiet_tra_loi
FOR EACH ROW
BEGIN
    DECLARE v_so_cau_dung INT;
    DECLARE v_tong_so_cau INT;
    DECLARE v_diem_moi DECIMAL(5,2);
    DECLARE v_diem_toi_da DECIMAL(5,2);
    
    -- Đếm số câu đúng
    SELECT COUNT(*) INTO v_so_cau_dung
    FROM chi_tiet_tra_loi
    WHERE bai_lam_kiem_tra_id = NEW.bai_lam_kiem_tra_id AND dung_hay_sai = TRUE;
    
    -- Đếm tổng số câu
    SELECT COUNT(*) INTO v_tong_so_cau
    FROM chi_tiet_tra_loi
    WHERE bai_lam_kiem_tra_id = NEW.bai_lam_kiem_tra_id;
    
    -- Lấy điểm tối đa
    SELECT bkt.diem_toi_da INTO v_diem_toi_da
    FROM bai_lam_kiem_tra blkt
    JOIN bai_kiem_tra bkt ON blkt.bai_kiem_tra_id = bkt.id
    WHERE blkt.id = NEW.bai_lam_kiem_tra_id;
    
    -- Tính điểm
    IF v_tong_so_cau > 0 THEN
        SET v_diem_moi = (v_so_cau_dung / v_tong_so_cau) * v_diem_toi_da;
    ELSE
        SET v_diem_moi = 0;
    END IF;
    
    -- Cập nhật bài làm
    UPDATE bai_lam_kiem_tra
    SET so_cau_dung = v_so_cau_dung,
        tong_so_cau = v_tong_so_cau,
        diem = v_diem_moi
    WHERE id = NEW.bai_lam_kiem_tra_id;
END//

DELIMITER ;


SET FOREIGN_KEY_CHECKS = 1;


