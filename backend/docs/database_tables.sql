-- Thiết lập charset cho database
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tạo database nếu chưa có
CREATE DATABASE IF NOT EXISTS quan_ly_hoc_tap_hoan_chinh 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE quan_ly_hoc_tap_hoan_chinh;

-- ==================================================
-- BẢNG CỐT LÕI
-- ==================================================

-- Bảng người dùng
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mat_khau_hash VARCHAR(255) NOT NULL,
    ho_ten VARCHAR(100) NOT NULL,
    anh_dai_dien VARCHAR(255),
    vai_tro ENUM('giang_vien', 'hoc_vien') DEFAULT 'hoc_vien',
    trang_thai ENUM('hoat_dong', 'khong_hoat_dong', 'bi_khoa') DEFAULT 'hoat_dong',
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    lan_dang_nhap_cuoi TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_ten_dang_nhap (ten_dang_nhap),
    INDEX idx_vai_tro (vai_tro)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng khóa học (đơn giản hóa)
CREATE TABLE khoa_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tieu_de VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    mo_ta_ngan VARCHAR(500),
    anh_thu_nho VARCHAR(255),
    giang_vien_id INT NOT NULL,
    so_hoc_vien_toi_da INT DEFAULT 20,
    so_luong_dang_ky INT DEFAULT 0,
    trang_thai ENUM('ban_nhap', 'da_xuat_ban', 'luu_tru') DEFAULT 'ban_nhap',
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    thoi_gian_xuat_ban TIMESTAMP NULL,
    
    INDEX idx_giang_vien (giang_vien_id),
    INDEX idx_trang_thai (trang_thai),
    INDEX idx_tieu_de (tieu_de),
    
    CONSTRAINT FK_khoa_hoc_giang_vien FOREIGN KEY (giang_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Tạo Full-text index cho tìm kiếm
ALTER TABLE khoa_hoc ADD FULLTEXT(tieu_de, mo_ta_ngan);

-- Bảng chương học
CREATE TABLE chuong_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    khoa_hoc_id INT NOT NULL,
    so_chuong INT NOT NULL,
    ten_chuong VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    thu_tu_sap_xep INT DEFAULT 0,
    la_bai_kiem_tra_cuoi BOOLEAN DEFAULT FALSE,
    trang_thai ENUM('hoat_dong', 'khong_hoat_dong') DEFAULT 'hoat_dong',
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY UQ_chuong_khoa_hoc (khoa_hoc_id, so_chuong),
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_thu_tu (khoa_hoc_id, thu_tu_sap_xep),
    INDEX idx_trang_thai (trang_thai),
    
    CONSTRAINT FK_chuong_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng mục học
CREATE TABLE muc_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chuong_id INT NOT NULL,
    khoa_hoc_id INT NOT NULL,
    so_muc DECIMAL(3,1) NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    
    -- Thông tin bài giảng
    noi_dung_bai_giang TEXT,
    loai_noi_dung ENUM('tai_lieu', 'video', 'slide') DEFAULT 'tai_lieu',
    ten_tep VARCHAR(255),
    ten_tep_goc VARCHAR(255),
    duong_dan_tep VARCHAR(500),
    kich_thuoc_tep BIGINT DEFAULT 0,
    loai_tep ENUM('pdf', 'docx', 'doc', 'mp4', 'youtube', 'pptx') DEFAULT 'pdf',
    so_trang INT DEFAULT 0,
    so_lan_tai_xuong INT DEFAULT 0,
    
    -- Bài tập kèm theo (tùy chọn)
    co_bai_tap BOOLEAN DEFAULT FALSE,
    
    thu_tu_sap_xep INT DEFAULT 0,
    trang_thai ENUM('hoat_dong', 'khong_hoat_dong') DEFAULT 'hoat_dong',
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY UQ_muc_chuong (chuong_id, so_muc),
    INDEX idx_chuong (chuong_id),
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_thu_tu (chuong_id, thu_tu_sap_xep),
    INDEX idx_trang_thai (trang_thai),
    INDEX idx_loai_noi_dung (loai_noi_dung),
    
    CONSTRAINT FK_muc_chuong FOREIGN KEY (chuong_id) REFERENCES chuong_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_muc_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng đăng ký khóa học
CREATE TABLE dang_ky_khoa_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    khoa_hoc_id INT NOT NULL,
    thoi_gian_dang_ky TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    phan_tram_tien_do DECIMAL(5,2) DEFAULT 0.00,
    thoi_gian_hoan_thanh TIMESTAMP NULL,
    trang_thai ENUM('hoat_dong', 'da_hoan_thanh', 'da_huy') DEFAULT 'hoat_dong',
    lan_truy_cap_cuoi TIMESTAMP NULL,
    tong_thoi_gian_hoc INT DEFAULT 0, -- tổng phút học
    
    UNIQUE KEY UQ_dang_ky_duy_nhat (nguoi_dung_id, khoa_hoc_id),
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_trang_thai (trang_thai),
    
    CONSTRAINT FK_dang_ky_nguoi_dung FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_dang_ky_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng tiến độ chi tiết
CREATE TABLE tien_do_chi_tiet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    khoa_hoc_id INT NOT NULL,
    muc_hoc_id INT NULL,
    chuong_id INT NULL,
    
    -- Loại tiến độ
    loai_tien_do ENUM('muc_hoc', 'bai_tap', 'kiem_tra') DEFAULT 'muc_hoc',
    
    -- Tiến độ chung
    da_hoan_thanh BOOLEAN DEFAULT FALSE,
    phan_tram_hoan_thanh DECIMAL(5,2) DEFAULT 0.00,
    thoi_gian_hoc INT DEFAULT 0, -- phút
    thoi_gian_bat_dau TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_hoan_thanh TIMESTAMP NULL,
    
    -- Dành cho tài liệu
    trang_hien_tai INT DEFAULT 1,
    tong_so_trang_da_doc INT DEFAULT 0,
    
    -- Cập nhật cuối
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY UQ_tien_do_muc_hoc (nguoi_dung_id, muc_hoc_id, loai_tien_do),
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_muc_hoc (muc_hoc_id),
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_chuong (chuong_id),
    INDEX idx_hoan_thanh (da_hoan_thanh),
    INDEX idx_loai_tien_do (loai_tien_do),
    
    CONSTRAINT FK_tien_do_nguoi_dung FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_tien_do_muc_hoc FOREIGN KEY (muc_hoc_id) REFERENCES muc_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_tien_do_chuong FOREIGN KEY (chuong_id) REFERENCES chuong_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_tien_do_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng thông báo đơn giản (chỉ cho thông báo "chuông")
CREATE TABLE thong_bao_don_gian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tieu_de VARCHAR(200) NOT NULL,
    noi_dung TEXT NOT NULL,
    nguoi_nhan_id INT NOT NULL,
    nguoi_gui_id INT NULL,
    da_doc BOOLEAN DEFAULT FALSE,
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_doc TIMESTAMP NULL,
    
    INDEX idx_nguoi_nhan (nguoi_nhan_id),
    INDEX idx_da_doc (da_doc),
    INDEX idx_thoi_gian (thoi_gian_tao),
    
    CONSTRAINT FK_thong_bao_nguoi_nhan FOREIGN KEY (nguoi_nhan_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_thong_bao_nguoi_gui FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================
-- MODULE BÀI TẬP VÀ ĐÁNH GIÁ
-- ==================================================

-- Bảng bài tập/kiểm tra
CREATE TABLE bai_tap (
    id INT AUTO_INCREMENT PRIMARY KEY,
    khoa_hoc_id INT NOT NULL,
    muc_hoc_id INT NULL,
    chuong_id INT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    mo_ta TEXT,
    huong_dan TEXT,
    loai_bai_tap ENUM('trac_nghiem', 'tu_luan', 'nop_tep') DEFAULT 'trac_nghiem',
    loai_bai_tap_trong_khoa_hoc ENUM('bai_tap_muc', 'kiem_tra_cuoi') DEFAULT 'bai_tap_muc',
    diem_toi_da DECIMAL(5,2) DEFAULT 100.00,
    gioi_han_thoi_gian INT DEFAULT 0, -- phút, 0 = không giới hạn
    so_lan_lam_cho_phep INT DEFAULT 1,
    han_nop TIMESTAMP NULL,
    trang_thai ENUM('ban_nhap', 'da_xuat_ban', 'luu_tru') DEFAULT 'ban_nhap',
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_muc_hoc (muc_hoc_id),
    INDEX idx_chuong (chuong_id),
    INDEX idx_loai (loai_bai_tap),
    INDEX idx_loai_trong_khoa_hoc (loai_bai_tap_trong_khoa_hoc),
    INDEX idx_trang_thai (trang_thai),
    
    CONSTRAINT FK_bai_tap_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_bai_tap_muc_hoc FOREIGN KEY (muc_hoc_id) REFERENCES muc_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_bai_tap_chuong FOREIGN KEY (chuong_id) REFERENCES chuong_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng câu hỏi
CREATE TABLE cau_hoi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bai_tap_id INT NOT NULL,
    noi_dung_cau_hoi TEXT NOT NULL,
    loai_cau_hoi ENUM('trac_nghiem', 'dung_sai', 'tu_luan') DEFAULT 'trac_nghiem',
    diem DECIMAL(5,2) DEFAULT 1.00,
    thu_tu_sap_xep INT DEFAULT 0,
    dap_an_dung TEXT,
    giai_thich TEXT,
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_bai_tap (bai_tap_id),
    INDEX idx_loai (loai_cau_hoi),
    INDEX idx_thu_tu (bai_tap_id, thu_tu_sap_xep),
    
    CONSTRAINT FK_cau_hoi_bai_tap FOREIGN KEY (bai_tap_id) REFERENCES bai_tap(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng lựa chọn cho câu hỏi trắc nghiệm
CREATE TABLE lua_chon_cau_hoi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cau_hoi_id INT NOT NULL,
    noi_dung_lua_chon TEXT NOT NULL,
    dap_an_dung BOOLEAN DEFAULT FALSE,
    thu_tu_sap_xep INT DEFAULT 0,
    
    INDEX idx_cau_hoi (cau_hoi_id),
    INDEX idx_thu_tu (cau_hoi_id, thu_tu_sap_xep),
    
    CONSTRAINT FK_lua_chon_cau_hoi FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng nộp bài
CREATE TABLE bai_nop (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    bai_tap_id INT NOT NULL,
    lan_lam_thu INT DEFAULT 1,
    cau_tra_loi JSON, -- JSON data
    tep_tin JSON, -- JSON data cho việc nộp tệp
    diem DECIMAL(5,2) DEFAULT 0.00,
    diem_toi_da DECIMAL(5,2) DEFAULT 100.00,
    phan_tram DECIMAL(5,2) DEFAULT 0.00,
    trang_thai ENUM('dang_lam', 'da_nop', 'da_cham') DEFAULT 'dang_lam',
    thoi_gian_bat_dau TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_nop TIMESTAMP NULL,
    thoi_gian_cham TIMESTAMP NULL,
    nguoi_cham_id INT NULL,
    phan_hoi TEXT,
    thoi_gian_lam INT DEFAULT 0, -- phút
    
    INDEX idx_nguoi_dung (nguoi_dung_id),
    INDEX idx_bai_tap (bai_tap_id),
    INDEX idx_trang_thai (trang_thai),
    INDEX idx_diem (diem),
    
    CONSTRAINT FK_bai_nop_nguoi_dung FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_bai_nop_bai_tap FOREIGN KEY (bai_tap_id) REFERENCES bai_tap(id) ON DELETE CASCADE,
    CONSTRAINT FK_bai_nop_nguoi_cham FOREIGN KEY (nguoi_cham_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================
-- HỆ THỐNG HỎI ĐÁP THREAD
-- ==================================================

-- Bảng hỏi đáp theo mục học (thread discussion)
CREATE TABLE hoi_dap_muc_hoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    muc_hoc_id INT NOT NULL,
    khoa_hoc_id INT NOT NULL,
    nguoi_gui_id INT NOT NULL,
    noi_dung TEXT NOT NULL,
    
    -- Thread logic
    loai ENUM('cau_hoi_goc', 'tra_loi') DEFAULT 'cau_hoi_goc',
    thread_goc_id INT NULL COMMENT 'ID câu hỏi gốc, NULL nếu là câu hỏi gốc',
    
    -- Like/Dislike
    luot_like INT DEFAULT 0,
    luot_dislike INT DEFAULT 0,
    
    -- Timestamps
    thoi_gian_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    thoi_gian_cap_nhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_muc_hoc (muc_hoc_id),
    INDEX idx_khoa_hoc (khoa_hoc_id),
    INDEX idx_nguoi_gui (nguoi_gui_id),
    INDEX idx_thread_goc (thread_goc_id),
    INDEX idx_loai (loai),
    INDEX idx_thoi_gian (thoi_gian_tao),
    
    -- Constraints
    CONSTRAINT FK_hoi_dap_muc_hoc FOREIGN KEY (muc_hoc_id) REFERENCES muc_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_hoi_dap_khoa_hoc FOREIGN KEY (khoa_hoc_id) REFERENCES khoa_hoc(id) ON DELETE CASCADE,
    CONSTRAINT FK_hoi_dap_nguoi_gui FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_hoi_dap_thread_goc FOREIGN KEY (thread_goc_id) REFERENCES hoi_dap_muc_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Bảng vote cho hỏi đáp (tránh vote trùng)
CREATE TABLE vote_hoi_dap (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id INT NOT NULL,
    hoi_dap_id INT NOT NULL,
    loai_vote ENUM('like', 'dislike') NOT NULL,
    thoi_gian_vote TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY UQ_vote_user_message (nguoi_dung_id, hoi_dap_id),
    INDEX idx_hoi_dap (hoi_dap_id),
    INDEX idx_nguoi_dung (nguoi_dung_id),
    
    CONSTRAINT FK_vote_nguoi_dung FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    CONSTRAINT FK_vote_hoi_dap FOREIGN KEY (hoi_dap_id) REFERENCES hoi_dap_muc_hoc(id) ON DELETE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ==================================================
-- VIEWS TỐI ƯU HÓA
-- ==================================================

-- View thống kê khóa học (cập nhật với cấu trúc mới)
-- Dependencies: khoa_hoc, dang_ky_khoa_hoc, chuong_hoc, muc_hoc, bai_tap
CREATE VIEW thong_ke_khoa_hoc AS
SELECT 
    k.id,
    k.tieu_de,
    k.giang_vien_id,
    nd.ho_ten as ten_giang_vien,
    COUNT(DISTINCT dk.nguoi_dung_id) as so_hoc_vien_hien_tai,
    k.so_hoc_vien_toi_da,
    COUNT(DISTINCT ch.id) as so_chuong,
    COUNT(DISTINCT mh.id) as so_muc_hoc,
    COUNT(DISTINCT CASE WHEN bt.loai_bai_tap_trong_khoa_hoc = 'bai_tap_muc' THEN bt.id END) as so_bai_tap,
    COUNT(DISTINCT CASE WHEN bt.loai_bai_tap_trong_khoa_hoc = 'kiem_tra_cuoi' THEN bt.id END) as so_kiem_tra,
    COUNT(DISTINCT hd.id) as so_cau_hoi_hoi_dap,
    k.thoi_gian_tao,
    k.trang_thai
FROM khoa_hoc k
JOIN nguoi_dung nd ON k.giang_vien_id = nd.id
LEFT JOIN dang_ky_khoa_hoc dk ON k.id = dk.khoa_hoc_id AND dk.trang_thai = 'hoat_dong'
LEFT JOIN chuong_hoc ch ON k.id = ch.khoa_hoc_id AND ch.trang_thai = 'hoat_dong'
LEFT JOIN muc_hoc mh ON ch.id = mh.chuong_id AND mh.trang_thai = 'hoat_dong'
LEFT JOIN bai_tap bt ON k.id = bt.khoa_hoc_id AND bt.trang_thai = 'da_xuat_ban'
LEFT JOIN hoi_dap_muc_hoc hd ON k.id = hd.khoa_hoc_id
GROUP BY k.id, k.tieu_de, k.giang_vien_id, nd.ho_ten, k.so_hoc_vien_toi_da, k.thoi_gian_tao, k.trang_thai;

-- View tiến độ khóa học đơn giản
-- Dependencies: dang_ky_khoa_hoc, tien_do_chi_tiet
CREATE VIEW tien_do_khoa_hoc_don_gian AS
SELECT 
    dk.nguoi_dung_id,
    dk.khoa_hoc_id,
    nd.ho_ten as ten_hoc_vien,
    k.tieu_de as ten_khoa_hoc,
    dk.phan_tram_tien_do,
    dk.trang_thai,
    dk.thoi_gian_dang_ky,
    dk.tong_thoi_gian_hoc,
    COUNT(CASE WHEN td.loai_tien_do = 'muc_hoc' AND td.da_hoan_thanh = TRUE THEN 1 END) as so_muc_da_hoc,
    COUNT(CASE WHEN td.loai_tien_do = 'bai_tap' AND td.da_hoan_thanh = TRUE THEN 1 END) as so_bai_tap_da_lam,
    COUNT(CASE WHEN td.loai_tien_do = 'kiem_tra' AND td.da_hoan_thanh = TRUE THEN 1 END) as so_kiem_tra_da_lam
FROM dang_ky_khoa_hoc dk
JOIN nguoi_dung nd ON dk.nguoi_dung_id = nd.id
JOIN khoa_hoc k ON dk.khoa_hoc_id = k.id
LEFT JOIN tien_do_chi_tiet td ON dk.nguoi_dung_id = td.nguoi_dung_id 
    AND dk.khoa_hoc_id = td.khoa_hoc_id
GROUP BY dk.nguoi_dung_id, dk.khoa_hoc_id, nd.ho_ten, k.tieu_de, dk.phan_tram_tien_do, dk.trang_thai, dk.thoi_gian_dang_ky, dk.tong_thoi_gian_hoc;

-- View thống kê hỏi đáp theo mục học
CREATE VIEW thong_ke_hoi_dap_muc_hoc AS
SELECT 
    mh.id as muc_hoc_id,
    mh.tieu_de as ten_muc,
    ch.ten_chuong,
    k.tieu_de as ten_khoa_hoc,
    COUNT(DISTINCT CASE WHEN hd.loai = 'cau_hoi_goc' THEN hd.id END) as so_cau_hoi,
    COUNT(DISTINCT CASE WHEN hd.loai = 'tra_loi' THEN hd.id END) as so_tra_loi,
    COUNT(DISTINCT hd.nguoi_gui_id) as so_nguoi_tham_gia,
    SUM(hd.luot_like) as tong_like,
    SUM(hd.luot_dislike) as tong_dislike,
    MAX(hd.thoi_gian_tao) as hoat_dong_gan_nhat
FROM muc_hoc mh
JOIN chuong_hoc ch ON mh.chuong_id = ch.id
JOIN khoa_hoc k ON ch.khoa_hoc_id = k.id
LEFT JOIN hoi_dap_muc_hoc hd ON mh.id = hd.muc_hoc_id
WHERE mh.trang_thai = 'hoat_dong'
GROUP BY mh.id, mh.tieu_de, ch.ten_chuong, k.tieu_de;

-- Kích hoạt lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
