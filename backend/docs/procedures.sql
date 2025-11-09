USE quan_ly_hoc_tap_hoan_chinh;

-- ==================================================
-- STORED PROCEDURES CHO MYSQL
-- ==================================================

DELIMITER //

-- Thủ tục cập nhật số lượng đăng ký khóa học
CREATE PROCEDURE sp_CapNhatSoLuongDangKy(IN p_khoa_hoc_id INT)
BEGIN
    UPDATE khoa_hoc 
    SET so_luong_dang_ky = (
        SELECT COUNT(*) 
        FROM dang_ky_khoa_hoc 
        WHERE khoa_hoc_id = p_khoa_hoc_id AND trang_thai = 'hoat_dong'
    )
    WHERE id = p_khoa_hoc_id;
END //

-- Thủ tục cập nhật tiến độ khóa học
CREATE PROCEDURE sp_CapNhatTienDoKhoaHoc(IN p_nguoi_dung_id INT, IN p_khoa_hoc_id INT)
BEGIN
    DECLARE v_tong_muc INT DEFAULT 0;
    DECLARE v_da_hoan_thanh INT DEFAULT 0;
    DECLARE v_phan_tram DECIMAL(5,2) DEFAULT 0;
    
    -- Đếm tổng mục học của khóa học (không bao gồm chương kiểm tra cuối)
    SELECT COUNT(*) INTO v_tong_muc
    FROM muc_hoc mh
    JOIN chuong_hoc ch ON mh.chuong_id = ch.id
    WHERE ch.khoa_hoc_id = p_khoa_hoc_id 
    AND ch.trang_thai = 'hoat_dong' 
    AND mh.trang_thai = 'hoat_dong'
    AND ch.la_bai_kiem_tra_cuoi = FALSE;
    
    -- Đếm mục đã hoàn thành
    SELECT COUNT(*) INTO v_da_hoan_thanh
    FROM tien_do_chi_tiet td
    WHERE td.nguoi_dung_id = p_nguoi_dung_id 
    AND td.khoa_hoc_id = p_khoa_hoc_id 
    AND td.da_hoan_thanh = TRUE
    AND td.loai_tien_do = 'muc_hoc';
    
    -- Tính phần trăm
    IF v_tong_muc > 0 THEN
        SET v_phan_tram = (v_da_hoan_thanh / v_tong_muc) * 100;
    END IF;
    
    -- Cập nhật vào bảng đăng ký
    UPDATE dang_ky_khoa_hoc 
    SET phan_tram_tien_do = v_phan_tram,
        trang_thai = CASE 
            WHEN v_phan_tram >= 100 THEN 'da_hoan_thanh'
            ELSE trang_thai 
        END,
        thoi_gian_hoan_thanh = CASE 
            WHEN v_phan_tram >= 100 AND thoi_gian_hoan_thanh IS NULL THEN NOW()
            ELSE thoi_gian_hoan_thanh 
        END
    WHERE nguoi_dung_id = p_nguoi_dung_id AND khoa_hoc_id = p_khoa_hoc_id;
END //

-- Thủ tục tìm kiếm khóa học
CREATE PROCEDURE sp_TimKiemKhoaHoc(IN p_tu_khoa VARCHAR(255))
BEGIN
    SELECT k.*, nd.ho_ten as ten_giang_vien
    FROM khoa_hoc k
    JOIN nguoi_dung nd ON k.giang_vien_id = nd.id
    WHERE k.trang_thai = 'da_xuat_ban'
    AND (
        MATCH(k.tieu_de, k.mo_ta_ngan) AGAINST(p_tu_khoa IN NATURAL LANGUAGE MODE)
        OR k.tieu_de LIKE CONCAT('%', p_tu_khoa, '%')
        OR k.mo_ta_ngan LIKE CONCAT('%', p_tu_khoa, '%')
    )
    ORDER BY 
        MATCH(k.tieu_de, k.mo_ta_ngan) AGAINST(p_tu_khoa IN NATURAL LANGUAGE MODE) DESC,
        k.so_luong_dang_ky DESC;
END //

-- Thủ tục lấy cấu trúc khóa học
CREATE PROCEDURE sp_LayCauTrucKhoaHoc(IN p_khoa_hoc_id INT)
BEGIN
    SELECT 
        ch.id as chuong_id,
        ch.so_chuong,
        ch.ten_chuong,
        ch.la_bai_kiem_tra_cuoi,
        mh.id as muc_id,
        mh.so_muc,
        mh.tieu_de as ten_muc,
        mh.loai_noi_dung,
        mh.co_bai_tap,
        COUNT(bt.id) as so_bai_tap,
        COUNT(hd.id) as so_cau_hoi_hoi_dap
    FROM chuong_hoc ch
    LEFT JOIN muc_hoc mh ON ch.id = mh.chuong_id AND mh.trang_thai = 'hoat_dong'
    LEFT JOIN bai_tap bt ON mh.id = bt.muc_hoc_id AND bt.trang_thai = 'da_xuat_ban'
    LEFT JOIN hoi_dap_muc_hoc hd ON mh.id = hd.muc_hoc_id AND hd.loai = 'cau_hoi_goc'
    WHERE ch.khoa_hoc_id = p_khoa_hoc_id AND ch.trang_thai = 'hoat_dong'
    GROUP BY ch.id, ch.so_chuong, ch.ten_chuong, ch.la_bai_kiem_tra_cuoi, mh.id, mh.so_muc, mh.tieu_de, mh.loai_noi_dung, mh.co_bai_tap
    ORDER BY ch.thu_tu_sap_xep, mh.thu_tu_sap_xep;
END //

-- ==================================================
-- STORED PROCEDURES CHO HỎI ĐÁP
-- ==================================================

-- Lấy tất cả threads trong một mục học
CREATE PROCEDURE sp_LayDanhSachThreads(IN p_muc_hoc_id INT)
BEGIN
    SELECT 
        hd.id,
        hd.noi_dung,
        hd.luot_like,
        hd.luot_dislike,
        hd.thoi_gian_tao,
        nd.ho_ten as ten_nguoi_gui,
        nd.vai_tro,
        nd.anh_dai_dien,
        -- Đếm số trả lời
        COUNT(tl.id) as so_tra_loi,
        -- Hoạt động gần nhất
        COALESCE(MAX(tl.thoi_gian_tao), hd.thoi_gian_tao) as hoat_dong_gan_nhat
    FROM hoi_dap_muc_hoc hd
    JOIN nguoi_dung nd ON hd.nguoi_gui_id = nd.id
    LEFT JOIN hoi_dap_muc_hoc tl ON hd.id = tl.thread_goc_id
    WHERE hd.muc_hoc_id = p_muc_hoc_id 
    AND hd.loai = 'cau_hoi_goc'
    GROUP BY hd.id, hd.noi_dung, hd.luot_like, hd.luot_dislike, hd.thoi_gian_tao, nd.ho_ten, nd.vai_tro, nd.anh_dai_dien
    ORDER BY hoat_dong_gan_nhat DESC;
END //

-- Lấy chi tiết một thread
CREATE PROCEDURE sp_LayChiTietThread(IN p_thread_goc_id INT)
BEGIN
    SELECT 
        hd.id,
        hd.noi_dung,
        hd.loai,
        hd.luot_like,
        hd.luot_dislike,
        hd.thoi_gian_tao,
        hd.thoi_gian_cap_nhat,
        nd.ho_ten as ten_nguoi_gui,
        nd.vai_tro,
        nd.anh_dai_dien
    FROM hoi_dap_muc_hoc hd
    JOIN nguoi_dung nd ON hd.nguoi_gui_id = nd.id
    WHERE hd.thread_goc_id = p_thread_goc_id OR hd.id = p_thread_goc_id
    ORDER BY hd.thoi_gian_tao ASC;
END //

-- Vote cho hỏi đáp
CREATE PROCEDURE sp_VoteHoiDap(
    IN p_nguoi_dung_id INT,
    IN p_hoi_dap_id INT, 
    IN p_loai_vote ENUM('like', 'dislike')
)
BEGIN
    DECLARE v_old_vote VARCHAR(10) DEFAULT NULL;
    
    -- Kiểm tra vote cũ
    SELECT loai_vote INTO v_old_vote 
    FROM vote_hoi_dap 
    WHERE nguoi_dung_id = p_nguoi_dung_id AND hoi_dap_id = p_hoi_dap_id;
    
    -- Xóa vote cũ nếu có
    IF v_old_vote IS NOT NULL THEN
        DELETE FROM vote_hoi_dap 
        WHERE nguoi_dung_id = p_nguoi_dung_id AND hoi_dap_id = p_hoi_dap_id;
        
        -- Giảm counter cũ
        IF v_old_vote = 'like' THEN
            UPDATE hoi_dap_muc_hoc SET luot_like = luot_like - 1 WHERE id = p_hoi_dap_id;
        ELSE
            UPDATE hoi_dap_muc_hoc SET luot_dislike = luot_dislike - 1 WHERE id = p_hoi_dap_id;
        END IF;
    END IF;
    
    -- Thêm vote mới (nếu khác vote cũ)
    IF v_old_vote IS NULL OR v_old_vote != p_loai_vote THEN
        INSERT INTO vote_hoi_dap (nguoi_dung_id, hoi_dap_id, loai_vote) 
        VALUES (p_nguoi_dung_id, p_hoi_dap_id, p_loai_vote);
        
        -- Tăng counter mới
        IF p_loai_vote = 'like' THEN
            UPDATE hoi_dap_muc_hoc SET luot_like = luot_like + 1 WHERE id = p_hoi_dap_id;
        ELSE
            UPDATE hoi_dap_muc_hoc SET luot_dislike = luot_dislike + 1 WHERE id = p_hoi_dap_id;
        END IF;
    END IF;
    
    -- Trả về trạng thái vote hiện tại
    SELECT 
        CASE 
            WHEN v_old_vote IS NULL OR v_old_vote != p_loai_vote THEN p_loai_vote
            ELSE NULL 
        END as current_vote;
END //

DELIMITER ;
