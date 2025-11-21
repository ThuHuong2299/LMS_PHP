-- Thông báo 1: Nghỉ học (Lớp L001 - Power BI, GV001)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung) 
VALUES 
(
    (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001'),  -- lop_hoc_id = 1
    (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung = 'GV001'),  -- nguoi_gui_id = 1
    'Thông báo nghỉ học do thời tiết xấu - Lớp Power BI L001',
    'Kính gửi các em sinh viên lớp L001, do thời tiết mưa gió lớn dự báo từ ngày 22/11/2025, buổi học trực tuyến dự kiến sẽ được hoãn sang ngày 27/11/2025. Các em vui lòng ôn tập bài giảng Chương 2 và hoàn thành bài tập liên quan trước đó. Cô sẽ cập nhật lịch chi tiết qua hệ thống. Xin lỗi vì sự bất tiện!'
);
