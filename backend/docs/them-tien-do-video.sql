USE lms_hoc_tap;

-- Kiểm tra và xóa bảng cũ nếu tồn tại
DROP TABLE IF EXISTS tien_do_video;

-- Tạo bảng tiến độ video
CREATE TABLE tien_do_video (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID tiến độ',
    bai_giang_id INT NOT NULL COMMENT 'ID bài giảng',
    sinh_vien_id INT NOT NULL COMMENT 'ID sinh viên',
    trang_thai ENUM('chua_xem', 'dang_xem', 'xem_xong') DEFAULT 'chua_xem' COMMENT 'Trạng thái xem video',
    thoi_gian_xem INT DEFAULT 0 COMMENT 'Tổng thời gian đã xem (giây)',
    phan_tram_hoan_thanh DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Phần trăm hoàn thành (0-100)',
    lan_xem_cuoi TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Lần xem cuối',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo record',
    
    FOREIGN KEY (bai_giang_id) REFERENCES bai_giang(id) ON DELETE CASCADE,
    FOREIGN KEY (sinh_vien_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_tien_do (bai_giang_id, sinh_vien_id),
    INDEX idx_bai_giang (bai_giang_id),
    INDEX idx_sinh_vien (sinh_vien_id),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng theo dõi tiến độ xem video của sinh viên';

-- =====================================================
-- THÊM DỮ LIỆU MẪU (OPTIONAL)
-- =====================================================

-- Thêm tiến độ mẫu cho sinh viên SV001 trong lớp L001
SET @bai_giang_id_1 = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') ORDER BY so_thu_tu_bai LIMIT 1);
SET @bai_giang_id_2 = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') ORDER BY so_thu_tu_bai LIMIT 1 OFFSET 1);
SET @bai_giang_id_3 = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') ORDER BY so_thu_tu_bai LIMIT 1 OFFSET 2);

-- Sinh viên SV001: Đã xem xong 2 video đầu, đang xem video thứ 3
INSERT INTO tien_do_video (bai_giang_id, sinh_vien_id, trang_thai, thoi_gian_xem, phan_tram_hoan_thanh, lan_xem_cuoi)
VALUES 
(@bai_giang_id_1, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV001'), 'xem_xong', 1250, 100.00, NOW() - INTERVAL 2 DAY),
(@bai_giang_id_2, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV001'), 'xem_xong', 1180, 100.00, NOW() - INTERVAL 1 DAY),
(@bai_giang_id_3, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV001'), 'dang_xem', 680, 55.50, NOW());

-- Sinh viên SV002: Đã xem xong video đầu
INSERT INTO tien_do_video (bai_giang_id, sinh_vien_id, trang_thai, thoi_gian_xem, phan_tram_hoan_thanh, lan_xem_cuoi)
VALUES 
(@bai_giang_id_1, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV002'), 'xem_xong', 1250, 100.00, NOW() - INTERVAL 3 HOUR);

-- Sinh viên SV003: Chưa xem video nào (không cần insert, mặc định là chưa xem)

-- Thêm tiến độ cho lớp L002
SET @bai_giang_id_l002 = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002') ORDER BY so_thu_tu_bai LIMIT 1);

INSERT INTO tien_do_video (bai_giang_id, sinh_vien_id, trang_thai, thoi_gian_xem, phan_tram_hoan_thanh, lan_xem_cuoi)
VALUES 
(@bai_giang_id_l002, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV015'), 'xem_xong', 1200, 100.00, NOW() - INTERVAL 1 DAY),
(@bai_giang_id_l002, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV016'), 'dang_xem', 450, 38.00, NOW());
