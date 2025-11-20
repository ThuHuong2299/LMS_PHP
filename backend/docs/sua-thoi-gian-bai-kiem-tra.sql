-- =====================================================
-- SỬA THỜI GIAN BÀI KIỂM TRA
-- =====================================================
-- Mô tả: Cập nhật thời gian bắt đầu bài kiểm tra về quá khứ
--        để sinh viên có thể làm bài ngay
-- =====================================================

USE lms_hoc_tap;

-- Cập nhật thời gian bắt đầu về hiện tại
-- và thời gian kết thúc về 7 ngày sau (để có đủ thời gian làm bài)
UPDATE bai_kiem_tra
SET 
    thoi_gian_bat_dau = NOW(),
    thoi_gian_ket_thuc = DATE_ADD(NOW(), INTERVAL 7 DAY)
WHERE tieu_de LIKE 'Kiểm tra cuối Chương 1 - Giới thiệu Power BI%';

-- Kiểm tra kết quả
SELECT 
    id,
    tieu_de,
    thoi_gian_bat_dau,
    thoi_gian_ket_thuc,
    thoi_luong,
    CASE 
        WHEN NOW() < thoi_gian_bat_dau THEN 'Chưa mở'
        WHEN NOW() > thoi_gian_ket_thuc THEN 'Đã hết hạn'
        ELSE 'Đang mở'
    END AS trang_thai_thoi_gian
FROM bai_kiem_tra
WHERE tieu_de LIKE 'Kiểm tra cuối Chương 1 - Giới thiệu Power BI%';
