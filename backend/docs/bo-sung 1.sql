-- Thông báo 1: Nghỉ học (Lớp L001 - Power BI, GV001)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung) 
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001'),  -- lop_hoc_id = 1
    (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'GV001'),  -- nguoi_gui_id = 1
    'Thông báo nghỉ học do thời tiết xấu - Lớp Power BI L001',
    'Kính gửi các em sinh viên lớp L001, do thời tiết mưa gió lớn dự báo từ ngày 22/11/2025, buổi học trực tuyến dự kiến sẽ được hoãn sang ngày 27/11/2025. Các em vui lòng ôn tập bài giảng Chương 2 và hoàn thành bài tập liên quan trước đó. Cô sẽ cập nhật lịch chi tiết qua hệ thống. Xin lỗi vì sự bất tiện!'
);

-- Thông báo 2: Cập nhật tài liệu mới (Lớp L002 - Power BI, GV002)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung) 
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002'),  -- lop_hoc_id = 2
    (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'GV002'),  -- nguoi_gui_id = 2
    'Cập nhật tài liệu mới cho Chương 4 - Lớp Power BI L002',
    'Các em lớp L002 thân mến, cô đã upload tài liệu mới "Hướng dẫn thiết kế Dashboard nâng cao" vào phần Tài liệu lớp. Vui lòng tải về và nghiên cứu trước buổi học ngày 28/11/2025. Tài liệu này bao gồm các ví dụ thực hành cho bài giảng 4.1 và 4.2. Nếu cần hỗ trợ, hãy comment trực tiếp trên bài giảng.'
);

-- Thông báo 3: Nhắc nhở nội dung thi hết môn (Lớp L003 - SQL Server, GV003)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung) 
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003'),  -- lop_hoc_id = 3
    (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'GV003'),  -- nguoi_gui_id = 3
    'Nhắc nhở nội dung thi hết môn - Lớp SQL Server L003',
    'Chào các em lớp L003, kỳ thi hết môn sẽ diễn ra vào ngày 15/12/2025 (thời lượng 90 phút, 10 câu trắc nghiệm). Nội dung tập trung vào: Chương 3 (Truy vấn SELECT nâng cao: JOIN, SUBQUERY), Chương 4 (Quan hệ bảng, Stored Procedure cơ bản), và các bài tập thực hành trên database "quanly_sinhvien". Các em ôn tập kỹ và thực hành qua bài kiểm tra giữa kỳ để đạt kết quả tốt. Chúc các em thành công!'
);