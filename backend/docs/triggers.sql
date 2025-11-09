USE quan_ly_hoc_tap_hoan_chinh;

-- ==================================================
-- TRIGGERS
-- ==================================================

DELIMITER //

-- Trigger cập nhật số lượng đăng ký khi có đăng ký mới
CREATE TRIGGER trg_DangKyKhoaHoc_Insert
AFTER INSERT ON dang_ky_khoa_hoc
FOR EACH ROW
BEGIN
    CALL sp_CapNhatSoLuongDangKy(NEW.khoa_hoc_id);
END //

-- Trigger cập nhật số lượng đăng ký khi có thay đổi trạng thái
CREATE TRIGGER trg_DangKyKhoaHoc_Update
AFTER UPDATE ON dang_ky_khoa_hoc
FOR EACH ROW
BEGIN
    IF OLD.trang_thai != NEW.trang_thai THEN
        CALL sp_CapNhatSoLuongDangKy(NEW.khoa_hoc_id);
    END IF;
END //

-- Trigger cập nhật số lượng đăng ký khi xóa đăng ký
CREATE TRIGGER trg_DangKyKhoaHoc_Delete
AFTER DELETE ON dang_ky_khoa_hoc
FOR EACH ROW
BEGIN
    CALL sp_CapNhatSoLuongDangKy(OLD.khoa_hoc_id);
END //

-- Trigger tự động cập nhật tiến độ khi có tiến độ chi tiết mới
CREATE TRIGGER trg_TienDoChiTiet_Update
AFTER UPDATE ON tien_do_chi_tiet
FOR EACH ROW
BEGIN
    IF OLD.da_hoan_thanh != NEW.da_hoan_thanh THEN
        CALL sp_CapNhatTienDoKhoaHoc(NEW.nguoi_dung_id, NEW.khoa_hoc_id);
    END IF;
END //

-- Trigger tự động cập nhật tiến độ khi thêm tiến độ mới
CREATE TRIGGER trg_TienDoChiTiet_Insert
AFTER INSERT ON tien_do_chi_tiet
FOR EACH ROW
BEGIN
    IF NEW.da_hoan_thanh = TRUE THEN
        CALL sp_CapNhatTienDoKhoaHoc(NEW.nguoi_dung_id, NEW.khoa_hoc_id);
    END IF;
END //

DELIMITER ;
