-- =====================================================
-- THÊM BẢNG BÌNH LUẬN BÀI GIẢNG
-- =====================================================
-- Ngày tạo: 20/11/2025
-- Mục đích: Tạo bảng bình luận và phản hồi cho bài giảng/video
-- Hỗ trợ: Bình luận lồng nhau (reply to reply)
-- Chạy được trên: phpMyAdmin
-- =====================================================

USE lms_hoc_tap;

-- Kiểm tra và xóa bảng cũ nếu tồn tại
DROP TABLE IF EXISTS binh_luan_bai_giang;

-- Tạo bảng bình luận bài giảng
CREATE TABLE binh_luan_bai_giang (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID bình luận',
    bai_giang_id INT NOT NULL COMMENT 'ID bài giảng',
    nguoi_gui_id INT NOT NULL COMMENT 'ID người gửi (GV hoặc SV)',
    binh_luan_cha_id INT NULL COMMENT 'ID bình luận cha (NULL = bình luận gốc, có giá trị = phản hồi)',
    noi_dung TEXT NOT NULL COMMENT 'Nội dung bình luận',
    thoi_gian_gui TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian gửi',
    thoi_gian_sua TIMESTAMP NULL COMMENT 'Thời gian sửa (nếu có)',
    trang_thai ENUM('hien_thi', 'da_xoa') DEFAULT 'hien_thi' COMMENT 'Trạng thái bình luận',
    
    FOREIGN KEY (bai_giang_id) REFERENCES bai_giang(id) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_gui_id) REFERENCES nguoi_dung(id) ON DELETE CASCADE,
    FOREIGN KEY (binh_luan_cha_id) REFERENCES binh_luan_bai_giang(id) ON DELETE CASCADE,
    
    INDEX idx_bai_giang_id (bai_giang_id),
    INDEX idx_nguoi_gui_id (nguoi_gui_id),
    INDEX idx_binh_luan_cha_id (binh_luan_cha_id),
    INDEX idx_thoi_gian (thoi_gian_gui),
    INDEX idx_trang_thai (trang_thai)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
COMMENT='Bảng bình luận và phản hồi về bài giảng/video học tập';

-- =====================================================
-- THÊM DỮ LIỆU MẪU (OPTIONAL)
-- =====================================================

-- Thêm bình luận mẫu cho bài giảng đầu tiên của lớp L001
-- Lấy ID bài giảng đầu tiên
SET @bai_giang_id = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') LIMIT 1);
SET @sinh_vien_id = (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV001');
SET @giang_vien_id = (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'GV001');

-- Bình luận gốc từ sinh viên
INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, binh_luan_cha_id, noi_dung) 
VALUES 
(@bai_giang_id, @sinh_vien_id, NULL, 'Em có thắc mắc về phần Data Analysis được giới thiệu ở phút thứ 5 trong video ạ. Thầy có thể giải thích thêm về khái niệm này không ạ?'),
(@bai_giang_id, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV002'), NULL, 'Video rất hay và dễ hiểu. Em đã có cái nhìn tổng quan về Power BI rồi ạ!');

-- Lưu ID của bình luận đầu tiên để tạo phản hồi
SET @binh_luan_1_id = LAST_INSERT_ID();

-- Phản hồi từ giảng viên cho bình luận đầu tiên
INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, binh_luan_cha_id, noi_dung) 
VALUES 
(@bai_giang_id, @giang_vien_id, @binh_luan_1_id, 'Chào em! Data Analysis là quá trình kiểm tra, làm sạch và mô hình hóa dữ liệu nhằm khám phá thông tin hữu ích để hỗ trợ ra quyết định. Trong Power BI, chúng ta sẽ học cách phân tích dữ liệu từ nhiều nguồn khác nhau. Em xem thêm tài liệu trong mục Tài liệu nhé!');

-- Phản hồi từ sinh viên khác cho bình luận gốc
INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, binh_luan_cha_id, noi_dung) 
VALUES 
(@bai_giang_id, (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV003'), @binh_luan_1_id, 'Em cũng thắc mắc phần này. Cảm ơn bạn đã hỏi!');

-- Thêm bình luận mẫu cho lớp L002
SET @bai_giang_id_2 = (SELECT id FROM bai_giang WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002') LIMIT 1);
SET @sinh_vien_id_2 = (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'SV015');

INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, binh_luan_cha_id, noi_dung) 
VALUES 
(@bai_giang_id_2, @sinh_vien_id_2, NULL, 'Bài giảng rất chi tiết và dễ theo dõi. Em rất thích cách thầy giảng giải ạ!');

-- =====================================================
-- KIỂM TRA KẾT QUẢ
-- =====================================================

-- Xem tất cả bình luận
SELECT 
    bl.id,
    bl.bai_giang_id,
    bg.tieu_de AS ten_bai_giang,
    nd.ho_ten AS nguoi_gui,
    nd.vai_tro,
    bl.binh_luan_cha_id,
    bl.noi_dung,
    bl.thoi_gian_gui,
    bl.trang_thai
FROM binh_luan_bai_giang bl
JOIN bai_giang bg ON bl.bai_giang_id = bg.id
JOIN nguoi_dung nd ON bl.nguoi_gui_id = nd.id
ORDER BY bl.bai_giang_id, bl.binh_luan_cha_id IS NULL DESC, bl.thoi_gian_gui ASC;

-- Đếm số bình luận cho mỗi bài giảng
SELECT 
    bg.id,
    bg.tieu_de,
    lh.ma_lop_hoc,
    COUNT(bl.id) AS so_binh_luan
FROM bai_giang bg
LEFT JOIN binh_luan_bai_giang bl ON bg.id = bl.bai_giang_id
JOIN lop_hoc lh ON bg.lop_hoc_id = lh.id
GROUP BY bg.id, bg.tieu_de, lh.ma_lop_hoc
HAVING so_binh_luan > 0
ORDER BY so_binh_luan DESC;

-- =====================================================
-- HOÀN THÀNH
-- =====================================================
-- Bảng đã được tạo thành công!
-- Có thể sử dụng cho tính năng bình luận trên trang Thông tin bài học
-- =====================================================
