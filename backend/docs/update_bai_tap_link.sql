-- ================================================
-- CẬP NHẬT bai_giang_id CHO CÁC BÀI TẬP
-- Gán bài tập cho bài giảng tương ứng trong cùng chương
-- ================================================

-- Cập nhật cho lớp L001
-- Bài tập 1.1 -> Bài giảng 1.1
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.1
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.1 - L001';

-- Bài tập 1.2 -> Bài giảng 1.2
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.2
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.2 - L001';

-- Bài tập 2.1 -> Bài giảng 2.1
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') AND so_thu_tu_chuong = 2)
    AND so_thu_tu_bai = 2.1
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 2.1 - L001';

-- Bài tập 2.2 -> Bài giảng 2.2
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') AND so_thu_tu_chuong = 2)
    AND so_thu_tu_bai = 2.2
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 2.2 - L001';

-- Cập nhật cho lớp L002
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.1
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.1 - L002';

UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.2
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.2 - L002';

-- Cập nhật cho lớp L003 (SQL Server)
UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.1
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.1 - L003';

UPDATE bai_tap 
SET bai_giang_id = (
    SELECT id FROM bai_giang 
    WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003')
    AND chuong_id = (SELECT id FROM chuong WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') AND so_thu_tu_chuong = 1)
    AND so_thu_tu_bai = 1.2
    LIMIT 1
)
WHERE tieu_de = 'Bài tập 1.2 - L003';

-- Kiểm tra kết quả
SELECT bt.id, bt.tieu_de, bt.bai_giang_id, bg.tieu_de as ten_bai_giang
FROM bai_tap bt
LEFT JOIN bai_giang bg ON bt.bai_giang_id = bg.id
ORDER BY bt.id;
