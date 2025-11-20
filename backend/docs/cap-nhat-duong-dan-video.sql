-- Cập nhật đường dẫn video từ YouTube sang local file
-- Ngày tạo: 2025-11-20
-- Mục đích: Thay thế link YouTube bằng đường dẫn video local trong thư mục assets

-- =====================================================
-- POWER BI VIDEOS (Lớp 1 & 2)
-- =====================================================

-- Chương 1: Làm quen với Data Analysis và Power BI
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.01.mp4' WHERE id = 1;  -- 1.1 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.02.mp4' WHERE id = 2;  -- 1.2 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.03.mp4' WHERE id = 3;  -- 1.3 (L001)

-- Chương 2: Xây dựng Data Model
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.04.mp4' WHERE id = 4;  -- 2.1 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.05.mp4' WHERE id = 5;  -- 2.2 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.06.mp4' WHERE id = 6;  -- 2.3 (L001)

-- Chương 3: DAX và Filter Context
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.07.mp4' WHERE id = 7;  -- 3.1 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.08.mp4' WHERE id = 8;  -- 3.2 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.09.mp4' WHERE id = 9;  -- 3.3 (L001)

-- Chương 4: Thiết kế báo cáo
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.10.mp4' WHERE id = 10; -- 4.1 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.11.mp4' WHERE id = 11; -- 4.2 (L001)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.12.mp4' WHERE id = 12; -- 4.3 (L001)

-- Lớp 2 (L002) - Cùng video với L001
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.01.mp4' WHERE id = 13; -- 1.1 (L002)
UPDATE bai_giang SET duong_dan_video = '../assets/video/power-bi/PowerBI.02.mp4' WHERE id = 14; -- 1.2 (L002)

-- =====================================================
-- SQL SERVER VIDEOS (Lớp 3)
-- =====================================================

-- Chương 1: Giới thiệu SQL Server
UPDATE bai_giang SET duong_dan_video = '../assets/video/sql-server/SQL.01.mp4' WHERE id = 15; -- 1.1 Cài đặt SQL Server
UPDATE bai_giang SET duong_dan_video = '../assets/video/sql-server/SQL.02.mp4' WHERE id = 16; -- 1.2 Cài đặt SSMS
UPDATE bai_giang SET duong_dan_video = '../assets/video/sql-server/SQL.03.mp4' WHERE id = 17; -- 1.3 Chuẩn bị CSDL

-- Chương 2: Câu lệnh SQL cơ bản
UPDATE bai_giang SET duong_dan_video = '../assets/video/sql-server/SQL.04.mp4' WHERE id = 18; -- 2.1 Phép toán
UPDATE bai_giang SET duong_dan_video = '../assets/video/sql-server/SQL.05.mp4' WHERE id = 19; -- 2.2 WHERE

-- =====================================================
-- KIỂM TRA KẾT QUẢ
-- =====================================================

SELECT 
    id,
    SUBSTRING(tieu_de, 1, 50) as tieu_de_ngan,
    duong_dan_video,
    lop_hoc_id
FROM bai_giang 
ORDER BY id;
