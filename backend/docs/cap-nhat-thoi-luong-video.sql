-- Cập nhật thời lượng video (đơn vị: giây)
-- Ngày tạo: 2025-11-20
-- Lưu ý: Thời lượng này là ước tính, cần kiểm tra lại với video thực tế

-- =====================================================
-- POWER BI VIDEOS (ước tính mỗi video ~10-15 phút)
-- =====================================================

-- Chương 1: Làm quen với Data Analysis và Power BI
UPDATE bai_giang SET thoi_luong_giay = 720 WHERE id = 1;   -- 12 phút
UPDATE bai_giang SET thoi_luong_giay = 600 WHERE id = 2;   -- 10 phút
UPDATE bai_giang SET thoi_luong_giay = 900 WHERE id = 3;   -- 15 phút

-- Chương 2: Xây dựng Data Model
UPDATE bai_giang SET thoi_luong_giay = 840 WHERE id = 4;   -- 14 phút
UPDATE bai_giang SET thoi_luong_giay = 780 WHERE id = 5;   -- 13 phút
UPDATE bai_giang SET thoi_luong_giay = 660 WHERE id = 6;   -- 11 phút

-- Chương 3: DAX và Filter Context
UPDATE bai_giang SET thoi_luong_giay = 750 WHERE id = 7;   -- 12.5 phút
UPDATE bai_giang SET thoi_luong_giay = 900 WHERE id = 8;   -- 15 phút
UPDATE bai_giang SET thoi_luong_giay = 810 WHERE id = 9;   -- 13.5 phút

-- Chương 4: Thiết kế báo cáo
UPDATE bai_giang SET thoi_luong_giay = 720 WHERE id = 10;  -- 12 phút
UPDATE bai_giang SET thoi_luong_giay = 960 WHERE id = 11;  -- 16 phút
UPDATE bai_giang SET thoi_luong_giay = 840 WHERE id = 12;  -- 14 phút

-- Lớp 2 (L002) - Cùng thời lượng với L001
UPDATE bai_giang SET thoi_luong_giay = 720 WHERE id = 13;  -- 12 phút
UPDATE bai_giang SET thoi_luong_giay = 600 WHERE id = 14;  -- 10 phút

-- =====================================================
-- SQL SERVER VIDEOS (ước tính mỗi video ~8-12 phút)
-- =====================================================

-- Chương 1: Giới thiệu SQL Server
UPDATE bai_giang SET thoi_luong_giay = 600 WHERE id = 15;  -- 10 phút - Cài đặt SQL Server
UPDATE bai_giang SET thoi_luong_giay = 480 WHERE id = 16;  -- 8 phút - Cài đặt SSMS
UPDATE bai_giang SET thoi_luong_giay = 540 WHERE id = 17;  -- 9 phút - Chuẩn bị CSDL

-- Chương 2: Câu lệnh SQL cơ bản
UPDATE bai_giang SET thoi_luong_giay = 660 WHERE id = 18;  -- 11 phút - Phép toán
UPDATE bai_giang SET thoi_luong_giay = 720 WHERE id = 19;  -- 12 phút - WHERE

-- =====================================================
-- LƯU Ý: 
-- Thời lượng này là ước tính. Để có thời lượng chính xác:
-- 1. Dùng ffprobe: ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 video.mp4
-- 2. Hoặc đọc metadata từ file MP4 khi upload
-- =====================================================

-- KIỂM TRA KẾT QUẢ
SELECT 
    id,
    SUBSTRING(tieu_de, 1, 50) as tieu_de_ngan,
    thoi_luong_giay,
    CONCAT(FLOOR(thoi_luong_giay / 60), 'p', LPAD(thoi_luong_giay % 60, 2, '0'), 's') as thoi_gian_format,
    lop_hoc_id
FROM bai_giang 
ORDER BY id;
