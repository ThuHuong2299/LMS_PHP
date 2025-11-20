-- =====================================================
-- Thêm cột cau_hoi_id vào bảng binh_luan_bai_tap
-- Cho phép bình luận riêng cho từng câu hỏi
-- =====================================================

USE lms_hoc_tap;

-- Thêm cột cau_hoi_id
ALTER TABLE binh_luan_bai_tap 
ADD COLUMN cau_hoi_id INT NULL COMMENT 'ID câu hỏi (NULL = bình luận chung bài tập)' AFTER bai_lam_id;

-- Thêm foreign key
ALTER TABLE binh_luan_bai_tap
ADD CONSTRAINT fk_binh_luan_cau_hoi 
FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi_bai_tap(id) ON DELETE CASCADE;

-- Thêm index để tăng tốc query
ALTER TABLE binh_luan_bai_tap
ADD INDEX idx_cau_hoi_id (cau_hoi_id);

-- Comment mô tả
ALTER TABLE binh_luan_bai_tap 
COMMENT = 'Bình luận trao đổi giữa GV và SV về bài tập (chung hoặc từng câu)';
