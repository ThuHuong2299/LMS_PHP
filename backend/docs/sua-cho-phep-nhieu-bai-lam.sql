-- =====================================================
-- XÓA CONSTRAINT UNIQUE ĐỂ CHO PHÉP LÀM LẠI
-- =====================================================
-- Hiện tại: 1 sinh viên chỉ có 1 bài làm cho 1 bài kiểm tra
-- Sau khi xóa: 1 sinh viên có thể có nhiều bài làm (làm lại nhiều lần)
-- Bài làm mới nhất sẽ được sử dụng

USE lms_hoc_tap;

-- Xóa constraint UNIQUE
ALTER TABLE bai_lam_kiem_tra 
DROP INDEX unique_bai_lam_kiem_tra;

-- Cập nhật comment table
ALTER TABLE bai_lam_kiem_tra 
COMMENT 'Bài làm kiểm tra - Cho phép làm lại nếu giảng viên cho phép';

-- Kiểm tra lại
SHOW CREATE TABLE bai_lam_kiem_tra\G
