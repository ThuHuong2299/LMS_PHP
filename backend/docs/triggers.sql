
-- =====================================================
-- 9. TRIGGERS - TỰ ĐỘNG CẬP NHẬT DỮ LIỆU
-- =====================================================

DELIMITER //

-- Trigger: Tự động cập nhật số lượng đã xem thông báo (có thể implement sau)
-- Trigger: Tự động tính thời gian kết thúc bài kiểm tra
CREATE TRIGGER trg_tinh_thoi_gian_ket_thuc_bai_kiem_tra
BEFORE INSERT ON bai_kiem_tra
FOR EACH ROW
BEGIN
    IF NEW.thoi_gian_ket_thuc IS NULL THEN
        SET NEW.thoi_gian_ket_thuc = DATE_ADD(NEW.thoi_gian_bat_dau, INTERVAL NEW.thoi_luong MINUTE);
    END IF;
END//

-- Trigger: Tự động thêm mon_hoc_id vào sinh_vien_lop_hoc
CREATE TRIGGER trg_them_mon_hoc_id_sinh_vien_lop_hoc
BEFORE INSERT ON sinh_vien_lop_hoc
FOR EACH ROW
BEGIN
    DECLARE v_mon_hoc_id INT;
    SELECT mon_hoc_id INTO v_mon_hoc_id FROM lop_hoc WHERE id = NEW.lop_hoc_id;
    SET NEW.mon_hoc_id = v_mon_hoc_id;
END//

-- Trigger: Tự động tính điểm bài làm kiểm tra
CREATE TRIGGER trg_tinh_diem_bai_kiem_tra
AFTER INSERT ON chi_tiet_tra_loi
FOR EACH ROW
BEGIN
    DECLARE v_so_cau_dung INT;
    DECLARE v_tong_so_cau INT;
    DECLARE v_diem_moi DECIMAL(5,2);
    DECLARE v_diem_toi_da DECIMAL(5,2);
    
    -- Đếm số câu đúng
    SELECT COUNT(*) INTO v_so_cau_dung
    FROM chi_tiet_tra_loi
    WHERE bai_lam_kiem_tra_id = NEW.bai_lam_kiem_tra_id AND dung_hay_sai = TRUE;
    
    -- Đếm tổng số câu
    SELECT COUNT(*) INTO v_tong_so_cau
    FROM chi_tiet_tra_loi
    WHERE bai_lam_kiem_tra_id = NEW.bai_lam_kiem_tra_id;
    
    -- Lấy điểm tối đa
    SELECT bkt.diem_toi_da INTO v_diem_toi_da
    FROM bai_lam_kiem_tra blkt
    JOIN bai_kiem_tra bkt ON blkt.bai_kiem_tra_id = bkt.id
    WHERE blkt.id = NEW.bai_lam_kiem_tra_id;
    
    -- Tính điểm
    IF v_tong_so_cau > 0 THEN
        SET v_diem_moi = (v_so_cau_dung / v_tong_so_cau) * v_diem_toi_da;
    ELSE
        SET v_diem_moi = 0;
    END IF;
    
    -- Cập nhật bài làm
    UPDATE bai_lam_kiem_tra
    SET so_cau_dung = v_so_cau_dung,
        tong_so_cau = v_tong_so_cau,
        diem = v_diem_moi
    WHERE id = NEW.bai_lam_kiem_tra_id;
END//

DELIMITER ;
