USE lms_hoc_tap;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. RÀNG BUỘC VỀ BÀI TẬP
-- =====================================================

-- Ràng buộc: Mỗi bài tập chỉ có tối đa 1 câu hỏi
DELIMITER $$
CREATE TRIGGER before_insert_cau_hoi_bai_tap
BEFORE INSERT ON cau_hoi_bai_tap
FOR EACH ROW
BEGIN
    DECLARE so_cau_hoi INT;
    
    -- Đếm số câu hỏi hiện tại của bài tập
    SELECT COUNT(*) INTO so_cau_hoi
    FROM cau_hoi_bai_tap
    WHERE bai_tap_id = NEW.bai_tap_id;
    
    -- Nếu đã có 1 câu hỏi, không cho phép thêm
    IF so_cau_hoi >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Mỗi bài tập chỉ được có tối đa 1 câu hỏi';
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 2. RÀNG BUỘC VỀ ĐIỂM BÀI TẬP
-- =====================================================

-- Trigger: Tự động set điểm mặc định là 10 khi tạo bài tập
DELIMITER $$
CREATE TRIGGER before_insert_bai_tap_default_diem
BEFORE INSERT ON bai_tap
FOR EACH ROW
BEGIN
    -- Nếu điểm tối đa không được set, mặc định là 10
    IF NEW.diem_toi_da IS NULL OR NEW.diem_toi_da = 0 THEN
        SET NEW.diem_toi_da = 10;
    END IF;
END$$
DELIMITER ;

-- Ràng buộc: Điểm số phải nằm trong khoảng 0 đến điểm tối đa
DELIMITER $$
CREATE TRIGGER before_insert_diem_bai_tap_check
BEFORE INSERT ON diem
FOR EACH ROW
BEGIN
    DECLARE diem_max DECIMAL(5,2);
    
    -- Nếu là bài tập, kiểm tra điểm tối đa
    IF NEW.bai_tap_id IS NOT NULL THEN
        SELECT diem_toi_da INTO diem_max
        FROM bai_tap
        WHERE id = NEW.bai_tap_id;
        
        -- Kiểm tra điểm trong khoảng hợp lệ
        IF NEW.diem < 0 OR NEW.diem > diem_max THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Điểm phải nằm trong khoảng từ 0 đến điểm tối đa';
        END IF;
    END IF;
    
    -- Nếu là bài kiểm tra, kiểm tra điểm tối đa
    IF NEW.bai_kiem_tra_id IS NOT NULL THEN
        SELECT diem_toi_da INTO diem_max
        FROM bai_kiem_tra
        WHERE id = NEW.bai_kiem_tra_id;
        
        -- Kiểm tra điểm trong khoảng hợp lệ
        IF NEW.diem < 0 OR NEW.diem > diem_max THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Điểm phải nằm trong khoảng từ 0 đến điểm tối đa';
        END IF;
    END IF;
END$$
DELIMITER ;

-- Trigger tương tự cho UPDATE
DELIMITER $$
CREATE TRIGGER before_update_diem_bai_tap_check
BEFORE UPDATE ON diem
FOR EACH ROW
BEGIN
    DECLARE diem_max DECIMAL(5,2);
    
    -- Nếu là bài tập, kiểm tra điểm tối đa
    IF NEW.bai_tap_id IS NOT NULL THEN
        SELECT diem_toi_da INTO diem_max
        FROM bai_tap
        WHERE id = NEW.bai_tap_id;
        
        -- Kiểm tra điểm trong khoảng hợp lệ
        IF NEW.diem < 0 OR NEW.diem > diem_max THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Điểm phải nằm trong khoảng từ 0 đến điểm tối đa';
        END IF;
    END IF;
    
    -- Nếu là bài kiểm tra, kiểm tra điểm tối đa
    IF NEW.bai_kiem_tra_id IS NOT NULL THEN
        SELECT diem_toi_da INTO diem_max
        FROM bai_kiem_tra
        WHERE id = NEW.bai_kiem_tra_id;
        
        -- Kiểm tra điểm trong khoảng hợp lệ
        IF NEW.diem < 0 OR NEW.diem > diem_max THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Điểm phải nằm trong khoảng từ 0 đến điểm tối đa';
        END IF;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 3. RÀNG BUỘC: SINH VIÊN PHẢI TRẢ LỜI TRƯỚC KHI CÓ ĐIỂM
-- =====================================================

-- Trigger: Kiểm tra sinh viên đã trả lời bài tập trước khi chấm điểm
DELIMITER $$
CREATE TRIGGER before_insert_diem_check_tra_loi
BEFORE INSERT ON diem
FOR EACH ROW
BEGIN
    DECLARE co_tra_loi INT DEFAULT 0;
    
    -- Kiểm tra bài tập
    IF NEW.bai_tap_id IS NOT NULL THEN
        SELECT COUNT(*) INTO co_tra_loi
        FROM bai_lam_bai_tap
        WHERE sinh_vien_id = NEW.sinh_vien_id
        AND bai_tap_id = NEW.bai_tap_id
        AND trang_thai IN ('da_nop', 'da_cham');
        
        IF co_tra_loi = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Sinh viên chưa nộp bài tập, không thể chấm điểm';
        END IF;
    END IF;
    
    -- Kiểm tra bài kiểm tra
    IF NEW.bai_kiem_tra_id IS NOT NULL THEN
        SELECT COUNT(*) INTO co_tra_loi
        FROM bai_lam_bai_kiem_tra
        WHERE sinh_vien_id = NEW.sinh_vien_id
        AND bai_kiem_tra_id = NEW.bai_kiem_tra_id
        AND trang_thai IN ('da_nop', 'da_cham');
        
        IF co_tra_loi = 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Sinh viên chưa làm bài kiểm tra, không thể chấm điểm';
        END IF;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 4. TỰ ĐỘNG CẬP NHẬT TIẾN ĐỘ HỌC TẬP
-- =====================================================

-- Trigger: Tự động cập nhật tiến độ khi sinh viên xem video
DELIMITER $$
CREATE TRIGGER after_insert_tien_do_video_update_hoc_tap
AFTER INSERT ON tien_do_video
FOR EACH ROW
BEGIN
    DECLARE tong_video INT DEFAULT 0;
    DECLARE video_hoan_thanh INT DEFAULT 0;
    DECLARE ti_le_hoan_thanh DECIMAL(5,2);
    DECLARE bai_giang_id_var INT;
    
    -- Lấy bài giảng từ video
    SELECT bai_giang_id INTO bai_giang_id_var
    FROM video_bai_giang
    WHERE id = NEW.video_id;
    
    -- Đếm tổng số video của bài giảng
    SELECT COUNT(*) INTO tong_video
    FROM video_bai_giang
    WHERE bai_giang_id = bai_giang_id_var;
    
    -- Đếm số video đã hoàn thành
    SELECT COUNT(DISTINCT tv.video_id) INTO video_hoan_thanh
    FROM tien_do_video tv
    JOIN video_bai_giang vbg ON tv.video_id = vbg.id
    WHERE tv.sinh_vien_id = NEW.sinh_vien_id
    AND vbg.bai_giang_id = bai_giang_id_var
    AND tv.trang_thai = 'hoan_thanh';
    
    -- Tính tỷ lệ
    IF tong_video > 0 THEN
        SET ti_le_hoan_thanh = (video_hoan_thanh / tong_video) * 100;
    ELSE
        SET ti_le_hoan_thanh = 0;
    END IF;
    
    -- Cập nhật hoặc tạo mới tiến độ học tập
    INSERT INTO tien_do_hoc_tap (sinh_vien_id, bai_giang_id, trang_thai, ti_le_hoan_thanh, lan_cuoi_xem)
    VALUES (NEW.sinh_vien_id, bai_giang_id_var, 
            IF(ti_le_hoan_thanh >= 100, 'hoan_thanh', 'dang_hoc'),
            ti_le_hoan_thanh, NOW())
    ON DUPLICATE KEY UPDATE
        trang_thai = IF(ti_le_hoan_thanh >= 100, 'hoan_thanh', 'dang_hoc'),
        ti_le_hoan_thanh = ti_le_hoan_thanh,
        lan_cuoi_xem = NOW();
END$$
DELIMITER ;

-- Trigger tương tự khi cập nhật tiến độ video
DELIMITER $$
CREATE TRIGGER after_update_tien_do_video_update_hoc_tap
AFTER UPDATE ON tien_do_video
FOR EACH ROW
BEGIN
    DECLARE tong_video INT DEFAULT 0;
    DECLARE video_hoan_thanh INT DEFAULT 0;
    DECLARE ti_le_hoan_thanh DECIMAL(5,2);
    DECLARE bai_giang_id_var INT;
    
    -- Lấy bài giảng từ video
    SELECT bai_giang_id INTO bai_giang_id_var
    FROM video_bai_giang
    WHERE id = NEW.video_id;
    
    -- Đếm tổng số video của bài giảng
    SELECT COUNT(*) INTO tong_video
    FROM video_bai_giang
    WHERE bai_giang_id = bai_giang_id_var;
    
    -- Đếm số video đã hoàn thành
    SELECT COUNT(DISTINCT tv.video_id) INTO video_hoan_thanh
    FROM tien_do_video tv
    JOIN video_bai_giang vbg ON tv.video_id = vbg.id
    WHERE tv.sinh_vien_id = NEW.sinh_vien_id
    AND vbg.bai_giang_id = bai_giang_id_var
    AND tv.trang_thai = 'hoan_thanh';
    
    -- Tính tỷ lệ
    IF tong_video > 0 THEN
        SET ti_le_hoan_thanh = (video_hoan_thanh / tong_video) * 100;
    ELSE
        SET ti_le_hoan_thanh = 0;
    END IF;
    
    -- Cập nhật tiến độ học tập
    UPDATE tien_do_hoc_tap
    SET trang_thai = IF(ti_le_hoan_thanh >= 100, 'hoan_thanh', 'dang_hoc'),
        ti_le_hoan_thanh = ti_le_hoan_thanh,
        lan_cuoi_xem = NOW()
    WHERE sinh_vien_id = NEW.sinh_vien_id
    AND bai_giang_id = bai_giang_id_var;
END$$
DELIMITER ;

-- =====================================================
-- 5. TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI BÀI LÀM KHI CHẤM ĐIỂM
-- =====================================================

-- Trigger: Cập nhật trạng thái bài làm bài tập khi có điểm
DELIMITER $$
CREATE TRIGGER after_insert_diem_update_trang_thai_bai_tap
AFTER INSERT ON diem
FOR EACH ROW
BEGIN
    IF NEW.bai_tap_id IS NOT NULL THEN
        UPDATE bai_lam_bai_tap
        SET trang_thai = 'da_cham'
        WHERE sinh_vien_id = NEW.sinh_vien_id
        AND bai_tap_id = NEW.bai_tap_id;
    END IF;
    
    IF NEW.bai_kiem_tra_id IS NOT NULL THEN
        UPDATE bai_lam_bai_kiem_tra
        SET trang_thai = 'da_cham'
        WHERE sinh_vien_id = NEW.sinh_vien_id
        AND bai_kiem_tra_id = NEW.bai_kiem_tra_id;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 6. KIỂM TRA DEADLINE
-- =====================================================

-- Trigger: Kiểm tra thời gian nộp bài không được sau deadline
DELIMITER $$
CREATE TRIGGER before_insert_bai_lam_check_deadline
BEFORE INSERT ON bai_lam_bai_tap
FOR EACH ROW
BEGIN
    DECLARE deadline_var TIMESTAMP;
    
    -- Lấy deadline của bài tập
    SELECT deadline INTO deadline_var
    FROM bai_tap
    WHERE id = NEW.bai_tap_id;
    
    -- Nếu có deadline và đã quá hạn, không cho nộp
    IF deadline_var IS NOT NULL AND NOW() > deadline_var AND NEW.trang_thai = 'da_nop' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Đã quá hạn nộp bài tập';
    END IF;
END$$
DELIMITER ;

-- Trigger tương tự cho bài kiểm tra
DELIMITER $$
CREATE TRIGGER before_insert_bai_lam_kiem_tra_check_deadline
BEFORE INSERT ON bai_lam_bai_kiem_tra
FOR EACH ROW
BEGIN
    DECLARE deadline_var TIMESTAMP;
    
    -- Lấy deadline của bài kiểm tra
    SELECT thoi_gian_ket_thuc INTO deadline_var
    FROM bai_kiem_tra
    WHERE id = NEW.bai_kiem_tra_id;
    
    -- Nếu có deadline và đã quá hạn, không cho làm
    IF deadline_var IS NOT NULL AND NOW() > deadline_var THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Đã quá thời gian làm bài kiểm tra';
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 7. RÀNG BUỘC: KHÔNG THỂ LÀM BÀI KIỂM TRA NẾU CHƯA HỌC HẾT CHƯƠNG
-- =====================================================

DELIMITER $$
CREATE TRIGGER before_insert_bai_lam_kiem_tra_check_hoc_tap
BEFORE INSERT ON bai_lam_bai_kiem_tra
FOR EACH ROW
BEGIN
    DECLARE chuong_id_var INT;
    DECLARE tong_bai_giang INT DEFAULT 0;
    DECLARE bai_giang_hoan_thanh INT DEFAULT 0;
    
    -- Lấy chương của bài kiểm tra
    SELECT chuong_id INTO chuong_id_var
    FROM bai_kiem_tra
    WHERE id = NEW.bai_kiem_tra_id;
    
    -- Đếm tổng số bài giảng trong chương
    SELECT COUNT(*) INTO tong_bai_giang
    FROM bai_giang
    WHERE chuong_id = chuong_id_var;
    
    -- Đếm số bài giảng sinh viên đã hoàn thành
    SELECT COUNT(DISTINCT bg.id) INTO bai_giang_hoan_thanh
    FROM bai_giang bg
    LEFT JOIN tien_do_hoc_tap tdht ON bg.id = tdht.bai_giang_id
    WHERE bg.chuong_id = chuong_id_var
    AND tdht.sinh_vien_id = NEW.sinh_vien_id
    AND tdht.trang_thai = 'hoan_thanh';
    
    -- Nếu chưa học hết, không cho làm bài kiểm tra
    IF bai_giang_hoan_thanh < tong_bai_giang THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Bạn phải hoàn thành tất cả bài giảng trong chương trước khi làm bài kiểm tra';
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- 8. INDEX BỔ SUNG ĐỂ TỐI ƯU HIỆU SUẤT
-- =====================================================

-- Index cho các truy vấn thường xuyên
CREATE INDEX idx_tien_do_video_sinh_vien_trang_thai ON tien_do_video(sinh_vien_id, trang_thai);
CREATE INDEX idx_tien_do_hoc_tap_sinh_vien_trang_thai ON tien_do_hoc_tap(sinh_vien_id, trang_thai);
CREATE INDEX idx_bai_lam_bai_tap_sinh_vien_trang_thai ON bai_lam_bai_tap(sinh_vien_id, trang_thai);
CREATE INDEX idx_bai_lam_kiem_tra_sinh_vien_trang_thai ON bai_lam_bai_kiem_tra(sinh_vien_id, trang_thai);
CREATE INDEX idx_diem_sinh_vien ON diem(sinh_vien_id);
CREATE INDEX idx_bai_tap_deadline ON bai_tap(deadline);
CREATE INDEX idx_bai_kiem_tra_thoi_gian ON bai_kiem_tra(thoi_gian_bat_dau, thoi_gian_ket_thuc);

SET FOREIGN_KEY_CHECKS = 1;

