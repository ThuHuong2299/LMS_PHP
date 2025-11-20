-- =====================================================
-- BÀI KIỂM TRA CUỐI CHƯƠNG 1: GIỚI THIỆU POWER BI
-- =====================================================
-- Mô tả: Bài kiểm tra trắc nghiệm đánh giá kiến thức chương 1
-- Ngày tạo: 20/11/2025
-- Thời lượng: 20 phút
-- Số câu hỏi: 10 câu
-- Điểm tối đa: 10 điểm
-- =====================================================

USE lms_hoc_tap;

-- =====================================================
-- 1. THÊM BÀI KIỂM TRA CUỐI CHƯƠNG 1 CHO LỚP L001
-- =====================================================

INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, diem_toi_da)
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
    (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
    'Kiểm tra cuối Chương 1 - Giới thiệu Power BI',
    'Bài kiểm tra đánh giá kiến thức Chương 1: Giới thiệu Power BI, giao diện, kết nối dữ liệu cơ bản',
    20,
    DATE_ADD(NOW(), INTERVAL 1 DAY),
    10.00
);

-- =====================================================
-- 2. THÊM CÂU HỎI TRẮC NGHIỆM (10 CÂU)
-- =====================================================

INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem)
SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    1, 
    'Power BI là gì?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    2, 
    'Trong Power BI, thành phần nào được sử dụng để thiết kế và tạo báo cáo trực quan?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    3, 
    'Power BI Desktop được sử dụng chủ yếu để làm gì?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    4, 
    'View nào trong Power BI cho phép xem và quản lý quan hệ giữa các bảng dữ liệu?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    5, 
    'Nguồn dữ liệu nào KHÔNG thể kết nối trực tiếp với Power BI?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    6, 
    'Trong Power BI, "Get Data" dùng để làm gì?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    7, 
    'Sự khác biệt chính giữa Import và DirectQuery là gì?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    8, 
    'Power BI Service là gì?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    9, 
    'Trong giao diện Power BI Desktop, panel nào hiển thị danh sách các fields (trường) từ các bảng dữ liệu?', 
    1.00
UNION ALL SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')),
    10, 
    'File Power BI Desktop được lưu với đuôi mở rộng nào?', 
    1.00;

-- =====================================================
-- 3. THÊM LỰA CHỌN CHO TỪNG CÂU HỎI
-- =====================================================

-- Câu 1: Power BI là gì?
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Công cụ phân tích và trực quan hóa dữ liệu của Microsoft', TRUE
FROM cau_hoi_trac_nghiem ch
WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) 
AND ch.thu_tu=1
UNION ALL SELECT ch.id, 2, 'B. Phần mềm soạn thảo văn bản', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=1
UNION ALL SELECT ch.id, 3, 'C. Hệ quản trị cơ sở dữ liệu', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=1
UNION ALL SELECT ch.id, 4, 'D. Ngôn ngữ lập trình', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=1;

-- Câu 2: Thành phần thiết kế báo cáo
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Report View', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 2, 'B. Data View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 3, 'C. Model View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 4, 'D. Power Query Editor', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=2;

-- Câu 3: Power BI Desktop dùng để làm gì
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Tạo và thiết kế báo cáo trên máy tính cá nhân', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 2, 'B. Chia sẻ báo cáo trên cloud', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 3, 'C. Xem báo cáo trên thiết bị di động', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 4, 'D. Quản lý database SQL Server', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=3;

-- Câu 4: View quản lý quan hệ bảng
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Model View', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 2, 'B. Report View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 3, 'C. Data View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 4, 'D. Table View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=4;

-- Câu 5: Nguồn dữ liệu không thể kết nối
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. File ảnh JPG thông thường', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 2, 'B. File Excel (.xlsx)', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 3, 'C. SQL Server Database', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 4, 'D. File CSV', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=5;

-- Câu 6: Get Data dùng để làm gì
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Kết nối và nhập dữ liệu từ nhiều nguồn khác nhau', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 2, 'B. Tạo biểu đồ trực quan', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 3, 'C. Xuất báo cáo ra PDF', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 4, 'D. Xóa dữ liệu cũ', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=6;

-- Câu 7: Sự khác biệt Import và DirectQuery
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Import lưu dữ liệu vào file Power BI, DirectQuery truy vấn trực tiếp từ nguồn', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 2, 'B. Import nhanh hơn DirectQuery', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 3, 'C. Không có sự khác biệt', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 4, 'D. DirectQuery chỉ dùng cho Excel', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=7;

-- Câu 8: Power BI Service là gì
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Nền tảng cloud để chia sẻ và cộng tác báo cáo Power BI', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 2, 'B. Phần mềm cài đặt trên máy tính', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 3, 'C. Dịch vụ hỗ trợ kỹ thuật của Microsoft', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 4, 'D. Công cụ chỉnh sửa video', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=8;

-- Câu 9: Panel hiển thị fields
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Fields pane', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 2, 'B. Visualizations pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 3, 'C. Filters pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 4, 'D. Properties pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=9;

-- Câu 10: Đuôi file Power BI Desktop
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. .pbix', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 2, 'B. .xlsx', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 3, 'C. .pptx', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 4, 'D. .sql', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001')) AND ch.thu_tu=10;

-- =====================================================
-- 4. THÊM BÀI KIỂM TRA CUỐI CHƯƠNG 1 CHO LỚP L002
-- =====================================================

INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, diem_toi_da)
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
    (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002') AND so_thu_tu_chuong=1),
    'Kiểm tra cuối Chương 1 - Giới thiệu Power BI',
    'Bài kiểm tra đánh giá kiến thức Chương 1: Giới thiệu Power BI, giao diện, kết nối dữ liệu cơ bản',
    20,
    DATE_ADD(NOW(), INTERVAL 1 DAY),
    10.00
);

-- Copy câu hỏi cho lớp L002 (10 câu)
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem)
SELECT 
    (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    1, 'Power BI là gì?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    2, 'Trong Power BI, thành phần nào được sử dụng để thiết kế và tạo báo cáo trực quan?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    3, 'Power BI Desktop được sử dụng chủ yếu để làm gì?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    4, 'View nào trong Power BI cho phép xem và quản lý quan hệ giữa các bảng dữ liệu?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    5, 'Nguồn dữ liệu nào KHÔNG thể kết nối trực tiếp với Power BI?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    6, 'Trong Power BI, "Get Data" dùng để làm gì?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    7, 'Sự khác biệt chính giữa Import và DirectQuery là gì?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    8, 'Power BI Service là gì?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    9, 'Trong giao diện Power BI Desktop, panel nào hiển thị danh sách các fields (trường) từ các bảng dữ liệu?', 1.00
UNION ALL SELECT (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')),
    10, 'File Power BI Desktop được lưu với đuôi mở rộng nào?', 1.00;

-- Copy lựa chọn cho lớp L002
-- Câu 1
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Công cụ phân tích và trực quan hóa dữ liệu của Microsoft', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=1
UNION ALL SELECT ch.id, 2, 'B. Phần mềm soạn thảo văn bản', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=1
UNION ALL SELECT ch.id, 3, 'C. Hệ quản trị cơ sở dữ liệu', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=1
UNION ALL SELECT ch.id, 4, 'D. Ngôn ngữ lập trình', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=1;

-- Câu 2
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Report View', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 2, 'B. Data View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 3, 'C. Model View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=2
UNION ALL SELECT ch.id, 4, 'D. Power Query Editor', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=2;

-- Câu 3
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Tạo và thiết kế báo cáo trên máy tính cá nhân', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 2, 'B. Chia sẻ báo cáo trên cloud', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 3, 'C. Xem báo cáo trên thiết bị di động', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=3
UNION ALL SELECT ch.id, 4, 'D. Quản lý database SQL Server', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=3;

-- Câu 4
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Model View', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 2, 'B. Report View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 3, 'C. Data View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=4
UNION ALL SELECT ch.id, 4, 'D. Table View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=4;

-- Câu 5
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. File ảnh JPG thông thường', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 2, 'B. File Excel (.xlsx)', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 3, 'C. SQL Server Database', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=5
UNION ALL SELECT ch.id, 4, 'D. File CSV', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=5;

-- Câu 6
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Kết nối và nhập dữ liệu từ nhiều nguồn khác nhau', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 2, 'B. Tạo biểu đồ trực quan', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 3, 'C. Xuất báo cáo ra PDF', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=6
UNION ALL SELECT ch.id, 4, 'D. Xóa dữ liệu cũ', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=6;

-- Câu 7
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Import lưu dữ liệu vào file Power BI, DirectQuery truy vấn trực tiếp từ nguồn', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 2, 'B. Import nhanh hơn DirectQuery', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 3, 'C. Không có sự khác biệt', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=7
UNION ALL SELECT ch.id, 4, 'D. DirectQuery chỉ dùng cho Excel', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=7;

-- Câu 8
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Nền tảng cloud để chia sẻ và cộng tác báo cáo Power BI', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 2, 'B. Phần mềm cài đặt trên máy tính', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 3, 'C. Dịch vụ hỗ trợ kỹ thuật của Microsoft', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=8
UNION ALL SELECT ch.id, 4, 'D. Công cụ chỉnh sửa video', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=8;

-- Câu 9
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. Fields pane', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 2, 'B. Visualizations pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 3, 'C. Filters pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=9
UNION ALL SELECT ch.id, 4, 'D. Properties pane', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=9;

-- Câu 10
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT ch.id, 1, 'A. .pbix', TRUE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 2, 'B. .xlsx', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 3, 'C. .pptx', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=10
UNION ALL SELECT ch.id, 4, 'D. .sql', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra cuối Chương 1 - Giới thiệu Power BI' AND lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002')) AND ch.thu_tu=10;

-- =====================================================
-- KẾT THÚC FILE
-- =====================================================
-- Tổng kết:
-- - 2 bài kiểm tra cuối chương 1 (cho lớp L001 và L002)
-- - Mỗi bài có 10 câu hỏi trắc nghiệm
-- - Mỗi câu hỏi có 4 lựa chọn (A, B, C, D)
-- - Thời lượng: 20 phút
-- - Điểm tối đa: 10 điểm (mỗi câu 1 điểm)
-- =====================================================
