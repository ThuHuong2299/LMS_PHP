SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ================================================
-- PHẦN 1: NGƯỜI DÙNG (Giảng viên và Sinh viên)
-- ================================================

-- Thêm 5 giảng viên (mật khẩu: 123456)
INSERT INTO nguoi_dung (ma_nguoi_dung, ten_dang_nhap, email, mat_khau_hash, ho_ten, vai_tro) VALUES
('GV001', 'nguyenthihaiyen', 'nguyenthihaiyen@edu.vn', '123456', 'Nguyễn Thị Hải Yến', 'giang_vien'),
('GV002', 'tranthiluong', 'tranthiluong@edu.vn', '123456', 'Trần Thị Lương', 'giang_vien'),
('GV003', 'domylinh', 'domylinh@edu.vn', '123456', 'Đỗ Mỹ Linh', 'giang_vien'),
('GV004', 'phamthidung', 'phamthidung@edu.vn', '123456', 'Phạm Thị Dung', 'giang_vien'),
('GV005', 'nguyentrangnhung', 'nguyentrangnhung@edu.vn', '123456', 'Nguyễn Trang Nhung', 'giang_vien');

-- Thêm 25 sinh viên (mật khẩu: 123456)
INSERT INTO nguoi_dung (ma_nguoi_dung, ten_dang_nhap, email, mat_khau_hash, ho_ten, vai_tro) VALUES
('SV001', 'nguyenngocanhlinh', 'sv001@stu.vn', '123456', 'Nguyễn Ngọc Ánh Linh', 'sinh_vien'),
('SV002', 'dangthuthao', 'sv002@stu.vn', '123456', 'Đặng Thu Thảo', 'sinh_vien'),
('SV003', 'dothiha', 'sv003@stu.vn', '123456', 'Đỗ Thị Hà', 'sinh_vien'),
('SV004', 'nguyenvietvuong', 'sv004@stu.vn', '123456', 'Nguyễn Viết Vương', 'sinh_vien'),
('SV005', 'ngoduydo', 'sv005@stu.vn', '123456', 'Ngô Duy Đô', 'sinh_vien'),
('SV006', 'nguyenthuychi', 'sv006@stu.vn', '123456', 'Nguyễn Thùy Chi', 'sinh_vien'),
('SV007', 'nguyendieulinh', 'sv007@stu.vn', '123456', 'Nguyễn Diệu Linh', 'sinh_vien'),
('SV008', 'lethiquynhtrang', 'sv008@stu.vn', '123456', 'Lê Thị Quỳnh Trang', 'sinh_vien'),
('SV009', 'khuongngocanh', 'sv009@stu.vn', '123456', 'Khương Ngọc Ánh', 'sinh_vien'),
('SV010', 'ninhduonglanngoc', 'sv010@stu.vn', '123456', 'Ninh Dương Lan Ngọc', 'sinh_vien'),
('SV011', 'tranthikhanhly', 'sv011@stu.vn', '123456', 'Trần Thị Khánh Ly', 'sinh_vien'),
('SV012', 'tranhuyenmy', 'sv012@stu.vn', '123456', 'Trần Huyền My', 'sinh_vien'),
('SV013', 'nguyenminhhang', 'sv013@stu.vn', '123456', 'Nguyễn Minh Hằng', 'sinh_vien'),
('SV014', 'nguyenminhphuong', 'sv014@stu.vn', '123456', 'Nguyễn Minh Phương', 'sinh_vien'),
('SV015', 'hoangkimngan', 'sv015@stu.vn', '123456', 'Hoàng Kim Ngân', 'sinh_vien'),
('SV016', 'nguyenthithuy', 'sv016@stu.vn', '123456', 'Nguyễn Thị Thủy', 'sinh_vien'),
('SV017', 'trankimngan', 'sv017@stu.vn', '123456', 'Trần Kim Ngân', 'sinh_vien'),
('SV018', 'trantieuvy', 'sv018@stu.vn', '123456', 'Trần Tiểu Vy', 'sinh_vien'),
('SV019', 'lethaomy', 'sv019@stu.vn', '123456', 'Lê Thảo My', 'sinh_vien'),
('SV020', 'phamthuytrang', 'sv020@stu.vn', '123456', 'Phạm Thùy Trang', 'sinh_vien'),
('SV021', 'tranphuongthu', 'sv021@stu.vn', '123456', 'Trần Phương Thư', 'sinh_vien'),
('SV022', 'trananhthu', 'sv022@stu.vn', '123456', 'Trần Anh Thư', 'sinh_vien'),
('SV023', 'trinhtranphuongtuan', 'sv023@stu.vn', '123456', 'Trịnh Trần Phương Tuấn', 'sinh_vien'),
('SV024', 'nguyenthanhtung', 'sv024@stu.vn', '123456', 'Nguyễn Thanh Tùng', 'sinh_vien'),
('SV025', 'leanhnguyet', 'sv025@stu.vn', '123456', 'Lê Ánh Nguyệt', 'sinh_vien');

-- ================================================
-- PHẦN 2: MÔN HỌC VÀ LỚP HỌC
-- ================================================

-- Thêm 2 môn học: Power BI và SQL Server
INSERT INTO mon_hoc (ma_mon_hoc, ten_mon_hoc, mo_ta, so_tin_chi) VALUES
('MH001', 'Thực hành Labs Power BI', 'Tự học Power BI từ cơ bản đến nâng cao, phân tích và trực quan hóa dữ liệu', 3),
('MH002', 'Lập trình SQL Server', 'Thiết kế và truy vấn cơ sở dữ liệu SQL Server', 3);

-- Thêm 3 lớp học: 2 lớp Power BI (L001, L002) và 1 lớp SQL Server (L003)
INSERT INTO lop_hoc (ma_lop_hoc, mon_hoc_id, giang_vien_id, ten_lop_hoc) VALUES
('L001', (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH001'), (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung='GV001'), 'PowerBI - Lớp 01'),
('L002', (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH001'), (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung='GV002'), 'PowerBI - Lớp 02'),
('L003', (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH002'), (SELECT id FROM nguoi_dung WHERE ma_nguoi_dung='GV003'), 'SQL - Lớp 01');

-- ================================================
-- PHẦN 3: GHI DANH SINH VIÊN VÀO LỚP
-- Ràng buộc: Mỗi sinh viên chỉ được học 1 lớp cho 1 môn học
-- ================================================

-- PowerBI Lớp 01 (L001): 12 sinh viên (SV001..SV012)
INSERT INTO sinh_vien_lop_hoc (sinh_vien_id, lop_hoc_id, mon_hoc_id)
SELECT 
  nd.id, 
  (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
  (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH001')
FROM nguoi_dung nd 
WHERE nd.vai_tro='sinh_vien' AND nd.ma_nguoi_dung BETWEEN 'SV001' AND 'SV012';

-- PowerBI Lớp 02 (L002): 13 sinh viên (SV013..SV025)
INSERT INTO sinh_vien_lop_hoc (sinh_vien_id, lop_hoc_id, mon_hoc_id)
SELECT 
  nd.id, 
  (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
  (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH001')
FROM nguoi_dung nd 
WHERE nd.vai_tro='sinh_vien' AND nd.ma_nguoi_dung BETWEEN 'SV013' AND 'SV025';

-- SQL Server Lớp 01 (L003): 10 sinh viên (SV001..SV010)
-- Lưu ý: Các sinh viên này đã học Power BI, giờ học thêm SQL (môn khác)
INSERT INTO sinh_vien_lop_hoc (sinh_vien_id, lop_hoc_id, mon_hoc_id)
SELECT 
  nd.id, 
  (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'),
  (SELECT id FROM mon_hoc WHERE ma_mon_hoc='MH002')
FROM nguoi_dung nd 
WHERE nd.vai_tro='sinh_vien' AND nd.ma_nguoi_dung BETWEEN 'SV001' AND 'SV010';

-- ================================================
-- PHẦN 4: CHƯƠNG HỌC (4 chương mỗi lớp)
-- ================================================

-- Chương cho lớp Power BI L001
INSERT INTO chuong (lop_hoc_id, so_thu_tu_chuong, ten_chuong, muc_tieu) VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 1, 'Chương 1: Giới thiệu Power BI', 'Làm quen giao diện, kết nối dữ liệu cơ bản'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 2, 'Chương 2: Mô hình dữ liệu & Transform', 'Hiểu Power Query, làm sạch và biến đổi dữ liệu'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 3, 'Chương 3: DAX cơ bản', 'Xử lý form, Các hàm tính toán đơn giản, measure, calculated column'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 4, 'Chương 4: Trực quan hoá & Dashboard', 'Tạo biểu đồ, dashboard và chia sẻ báo cáo');

-- Chương cho lớp Power BI L002
INSERT INTO chuong (lop_hoc_id, so_thu_tu_chuong, ten_chuong, muc_tieu) VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 1, 'Chương 1: Giới thiệu Power BI', 'Làm quen giao diện, kết nối dữ liệu cơ bản'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 2, 'Chương 2: Mô hình dữ liệu & Transform', 'Hiểu Power Query, làm sạch và biến đổi dữ liệu'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 3, 'Chương 3: DAX cơ bản', 'Xử lý form, Các hàm tính toán đơn giản, measure, calculated column'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 4, 'Chương 4: Trực quan hoá & Dashboard', 'Tạo biểu đồ, dashboard và chia sẻ báo cáo');

-- Chương cho lớp SQL Server L003
INSERT INTO chuong (lop_hoc_id, so_thu_tu_chuong, ten_chuong, muc_tieu) VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 1, 'Chương 1: Giới thiệu SQL Server', 'Cài đặt, làm quen SQL Server Management Studio'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 2, 'Chương 2: Bảng và dữ liệu', 'Tạo database, bảng, kiểu dữ liệu, INSERT/UPDATE/DELETE'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 3, 'Chương 3: Truy vấn SELECT nâng cao', 'WHERE, ORDER BY, GROUP BY, HAVING, hàm tổng hợp'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 4, 'Chương 4: JOIN & Quan hệ bảng', 'INNER/LEFT JOIN, khoá chính, khoá ngoại, thiết kế quan hệ');

-- ================================================
-- PHẦN 5: BÀI GIẢNG (Video học tập)
-- Mỗi chương có nhiều bài giảng
-- ================================================

-- Bài giảng cho lớp L001 (Power BI) - Chương 1
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 1.1, 
 '1.1 - Làm quen với Data Analysis và Power BI (L001)', 
 'https://youtu.be/IiVEJN7cL1Q?si=mQ7v4H-EYjlCyJ0r'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 1.2, 
 '1.2 - Chuẩn bị dữ liệu trong Power BI (L001)', 
 'https://youtu.be/dkzaWBxB0vA?si=R_eDH0lTJavXt1l8'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 1.3, 
 '1.3 - Làm sạch và chuyển đổi dữ liệu trong Power BI Desktop (L001)', 
 'https://youtu.be/crlt8LNbc6E?si=q2qYdsJM-EXmwHQZ');

-- Bài giảng cho lớp L001 - Chương 2
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=2),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 2.1, 
 '2.1 - Xây dựng Data Model trong Power BI (Phần 1) (L001)', 
 'https://youtu.be/VAoKln3O0hM?si=OSqkQykzyROay-lm'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=2),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 2.2, 
 '2.2 - Xây dựng Data Model trong Power BI (Phần 2) (L001)', 
 'https://youtu.be/fb0oNFE0fLQ?si=FiAl7FsFm7wros5c'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=2),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 2.3, 
 '2.3 - Tạo các DAX measure trong Power BI (L001)', 
 'https://youtu.be/cCE_J0bmgJ4?si=4XTZ9xAYwTYEVdnd');

-- Bài giảng cho lớp L001 - Chương 3
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=3),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 3.1, 
 '3.1 - Hiểu về Filter Context trong Power BI (Phần 1) (L001)', 
 'https://youtu.be/pb3DjX_hgac?si=LSWsHbd9uXmKzbvg'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=3),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 3.2, 
 '3.2 - Hiểu về pattern DAX & Row Context trong Power BI (L001)', 
 'https://youtu.be/d90XhZj9XTg?si=hMTSZgPQTUlQUjOC'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=3),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 3.3, 
 '3.3 - Tạo các phép tính DAX nâng cao (L001)', 
 'https://youtu.be/DsUPvP8it68?si=wMVh_OYfLgj02X9J');

-- Bài giảng cho lớp L001 - Chương 4
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=4),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 4.1, 
 '4.1 - Thiết kế báo cáo trong Power BI phần 1 (L001)', 
 'https://youtu.be/HlXPiP0YioU?si=RfOd3tKRYONqBK3v'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=4),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 4.2, 
 '4.2 - Thiết kế báo cáo trong Power BI Desktop (L001)', 
 'https://youtu.be/LJ9h6fdG6XM?si=81x1AGqoMXhg2AoY'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=4),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), 4.3, 
 '4.3 - Phân tích dữ liệu nâng cao trong Power BI (L001)', 
 'https://youtu.be/TedUJQJ__sQ?si=zCUgjTAo3_i3Xthf');

-- Bài giảng cho lớp L002 (Power BI) - Tương tự L001 nhưng cho lớp khác
-- (Chỉ thêm vài bài giảng mẫu, thực tế có thể copy tương tự L001)
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 1.1, 
 '1.1 - Làm quen với Data Analysis và Power BI (L002)', 
 'https://youtu.be/IiVEJN7cL1Q?si=mQ7v4H-EYjlCyJ0r'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'), 1.2, 
 '1.2 - Chuẩn bị dữ liệu trong Power BI (L002)', 
 'https://youtu.be/dkzaWBxB0vA?si=R_eDH0lTJavXt1l8');

-- Bài giảng cho lớp L003 (SQL Server)
INSERT INTO bai_giang (chuong_id, lop_hoc_id, so_thu_tu_bai, tieu_de, duong_dan_video)
VALUES 
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 1.1, 
 '1.1 - Hướng dẫn cài đặt SQL Server 2022 (L003)', 
 'https://youtu.be/kQRpe1HkALE?si=PeWqHPidSiFAWfFp'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 1.2, 
 '1.2 - Hướng dẫn cài đặt SSMS SQL Server Management System (L003)', 
 'https://youtu.be/r_eh79UMBiw?si=mwesB9Wk2RbUfR3h'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=1),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 1.3, 
 '1.3 - Chuẩn bị cơ sở dữ liệu để thực hành câu lệnh SQL (L003)', 
 'https://youtu.be/z8tme072AI0?si=zczFvfN1S14k3YJQ'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=2),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 2.1, 
 '2.1 - Các phép toán trong SQL (L003)', 
 'https://youtu.be/njZ7PeRXVAs?si=FgmBHCTZo_A_LE5J'),
((SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=2),
 (SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), 2.2, 
 '2.2 - Lọc dữ liệu bằng mệnh đề WHERE trong SQL (L003)', 
 'https://youtu.be/NKhIgdTN_DI?si=tFmTeZ_3wvu_iBSj');

-- ================================================
-- PHẦN 6: BÀI TẬP (Hạn nộp trong tương lai)
-- ================================================

-- Bài tập cho lớp L001 (Power BI)
INSERT INTO bai_tap (lop_hoc_id, chuong_id, tieu_de, mo_ta, han_nop)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
 'Bài tập 1.1 - L001', 
 'Bài tập chương 1 - Tải file Excel bất kỳ (bảng doanh thu hoặc danh sách học sinh).', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY)),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=1),
 'Bài tập 1.2 - L001', 
 'Bài tập chương 1 - Viết mô tả ngắn về sự khác nhau giữa Import và DirectQuery', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY)),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=2),
 'Bài tập 2.1 - L001', 
 'Bài tập chương 2 - Import một bảng dữ liệu có ít nhất 50 dòng.', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 9 DAY)),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001') AND so_thu_tu_chuong=2),
 'Bài tập 2.2 - L001', 
 'Bài tập chương 2 - Thực hiện Merge 2 bảng hoặc Append bảng', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 9 DAY));

-- Bài tập cho lớp L002 (Power BI)
INSERT INTO bai_tap (lop_hoc_id, chuong_id, tieu_de, mo_ta, han_nop)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002') AND so_thu_tu_chuong=1),
 'Bài tập 1.1 - L002', 
 'Bài tập chương 1 - Tải file Excel bất kỳ (bảng doanh thu hoặc danh sách học sinh).', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY)),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002') AND so_thu_tu_chuong=1),
 'Bài tập 1.2 - L002', 
 'Bài tập chương 1 - Viết mô tả ngắn về sự khác nhau giữa Import và DirectQuery', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY));

-- Bài tập cho lớp L003 (SQL Server)
INSERT INTO bai_tap (lop_hoc_id, chuong_id, tieu_de, mo_ta, han_nop)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=1),
 'Bài tập 1.1 - L003', 
 'Bài tập chương 1 - Tạo database tên: quanly_sinhvien', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY)),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 (SELECT id FROM chuong WHERE lop_hoc_id=(SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003') AND so_thu_tu_chuong=1),
 'Bài tập 1.2 - L003', 
 'Bài tập chương 1 - Thiết lập id làm PRIMARY KEY (int auto increment).', 
 DATE_ADD(CURRENT_DATE(), INTERVAL 8 DAY));

-- ================================================
-- PHẦN 7: BÀI KIỂM TRA (Quiz) VÀ CÂU HỎI TRẮC NGHIỆM
-- ================================================

-- Bài kiểm tra giữa kỳ lớp L001 (Power BI)
INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, diem_toi_da)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), NULL, 
 'Kiểm tra giữa kỳ - Power BI L001', 
 'Bài kiểm tra tổng hợp Chương 1 và Chương 2', 
 30, DATE_ADD(NOW(), INTERVAL 2 DAY), 10.00);

-- Câu hỏi trắc nghiệm cho bài kiểm tra giữa kỳ L001
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem)
SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001'),
  1, 'Mục đích chính của Power BI trong phân tích dữ liệu là gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001'),
  2, 'Khu vực dùng để thiết kế báo cáo trực quan là gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001'),
  3, 'Power Query Editor dùng chủ yếu để làm gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001'),
  4, 'Data Model trong Power BI mô tả điều gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001'),
  5, 'DAX measure được tạo ra để làm gì?', 0.5;

-- Lựa chọn cho câu hỏi 1: Mục đích chính của Power BI
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT 
  ch.id, 1, 'A. Phân tích và trực quan hóa dữ liệu phục vụ ra quyết định', TRUE
FROM cau_hoi_trac_nghiem ch
WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 2, 'B. Soạn thảo văn bản và trình chiếu', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 3, 'C. Quản lý file hệ điều hành', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 4, 'D. Thiết kế giao diện website', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=1;

-- Lựa chọn cho câu hỏi 2: Khu vực thiết kế báo cáo
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT 
  ch.id, 1, 'A. Report View', TRUE
FROM cau_hoi_trac_nghiem ch
WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=2
UNION ALL SELECT ch.id, 2, 'B. Data View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=2
UNION ALL SELECT ch.id, 3, 'C. Model View', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=2
UNION ALL SELECT ch.id, 4, 'D. Power Query Editor', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - Power BI L001') AND ch.thu_tu=2;

-- Bài kiểm tra cuối kỳ lớp L001 (Power BI)
INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, diem_toi_da)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'), NULL, 
 'Kiểm tra cuối kỳ - Power BI L001', 
 'Bài kiểm tra tổng hợp Chương 3 và Chương 4', 
 45, DATE_ADD(NOW(), INTERVAL 15 DAY), 10.00);

-- Bài kiểm tra giữa kỳ lớp L003 (SQL Server)
INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, diem_toi_da)
VALUES 
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'), NULL, 
 'Kiểm tra giữa kỳ - SQL Server L003', 
 'Bài kiểm tra tổng hợp Chương 1 và Chương 2', 
 30, DATE_ADD(NOW(), INTERVAL 3 DAY), 10.00);

-- Câu hỏi trắc nghiệm cho bài kiểm tra giữa kỳ L003
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem)
SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003'),
  1, 'SQL Server là gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003'),
  2, 'Câu lệnh SELECT dùng để làm gì?', 0.5
UNION ALL SELECT 
  (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003'),
  3, 'PRIMARY KEY có đặc điểm gì?', 0.5;

-- Lựa chọn cho câu hỏi SQL: SQL Server là gì?
INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung)
SELECT 
  ch.id, 1, 'A. Hệ quản trị cơ sở dữ liệu quan hệ của Microsoft', TRUE
FROM cau_hoi_trac_nghiem ch
WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 2, 'B. Trình soạn thảo văn bản', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 3, 'C. Phần mềm thiết kế đồ họa', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003') AND ch.thu_tu=1
UNION ALL SELECT ch.id, 4, 'D. Hệ điều hành máy chủ', FALSE FROM cau_hoi_trac_nghiem ch WHERE ch.bai_kiem_tra_id = (SELECT id FROM bai_kiem_tra WHERE tieu_de='Kiểm tra giữa kỳ - SQL Server L003') AND ch.thu_tu=1;

-- ================================================
-- PHẦN 8: THÔNG BÁO LỚP HỌC
-- ================================================

-- Thông báo cho lớp L001 (Power BI)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung)
VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 'Khai giảng lớp Power BI L001',
 'Chào mừng các bạn đến với lớp Power BI L001. Vui lòng tham gia nhóm trao đổi và kiểm tra mục Tài liệu lớp.'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 'Cập nhật tài liệu Chương 1',
 'Đã cập nhật tài liệu "Giới thiệu Power BI và phân tích dữ liệu" trong mục Tài liệu. Các bạn đọc trước khi đến lớp.'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 'Thông báo kiểm tra giữa kỳ',
 'Bài kiểm tra giữa kỳ sẽ mở trong hệ thống với 5 câu trắc nghiệm mẫu. Nội dung từ Chương 1 đến hết Chương 2.');

-- Thông báo cho lớp L002 (Power BI)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung)
VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 'Chào mừng lớp Power BI L002',
 'Lớp Power BI L002 chính thức bắt đầu. Các bạn đọc kỹ mô tả khóa học và chuẩn bị môi trường cài đặt.'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L002'),
 'Tài liệu Chương 1 đã sẵn sàng',
 'Đã tải lên tài liệu "Làm quen với Data Analysis và Power BI". Vui lòng xem trước để buổi học hiệu quả hơn.');

-- Thông báo cho lớp L003 (SQL Server)
INSERT INTO thong_bao_lop_hoc (lop_hoc_id, nguoi_gui_id, tieu_de, noi_dung)
VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 'Khai giảng lớp SQL Server L003',
 'Chào mừng các bạn đến với lớp SQL Server. Vui lòng cài SQL Server 2022 và SSMS trước buổi học đầu tiên.'),
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L003'),
 'Thông báo kiểm tra giữa kỳ',
 'Giữa kỳ gồm 3 câu trắc nghiệm mẫu, nội dung từ Chương 1 và Chương 2 (WHERE, LIKE, BETWEEN, IN, GROUP BY...).');

-- ================================================
-- PHẦN 9: BÀI LÀM
-- ================================================
-- Dữ liệu mẫu cho bảng bài làm 
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES

-- Sinh viên 1
(1, 6, 'da_nop', 8.5, '2025-10-15 08:30:00', '2025-10-20 22:15:00', '2025-10-21 09:45:00', 1),
(2, 6, 'da_nop', 9.0, '2025-10-22 14:20:00', '2025-10-28 23:55:00', '2025-10-29 10:30:00', 1),
(3, 6, 'chu_lam', NULL, NULL, NULL, NULL, NULL),

-- Sinh viên 2
(1, 7, 'da_nop', 7.0, '2025-10-16 09:15:00', '2025-10-20 20:30:00', '2025-10-21 11:20:00', 1),
(2, 7, 'da_nop', 6.5, '2025-10-23 10:00:00', '2025-10-29 01:10:00', NULL, NULL), 
(3, 7, 'dang_lam', NULL, '2025-11-10 15:45:00', NULL, NULL, NULL),

-- Sinh viên 3
(1, 8, 'da_nop', 10.0, '2025-10-14 07:50:00', '2025-10-19 18:00:00', '2025-10-20 14:15:00', 1),
(2, 8, 'da_nop', 9.5, '2025-10-21 13:30:00', '2025-10-27 21:45:00', '2025-10-28 08:55:00', 1),
(3, 8, 'da_nop', 8.0, '2025-11-05 09:00:00', '2025-11-17 23:59:00', '2025-11-18 10:30:00', 1),

-- Sinh viên 4
(1, 9, 'da_nop', 5.5, '2025-10-17 11:20:00', '2025-10-21 02:30:00', '2025-10-21 15:40:00', 1), 
(2, 9, 'chu_lam', NULL, NULL, NULL, NULL, NULL),
(3, 9, 'dang_lam', NULL, '2025-11-12 20:15:00', NULL, NULL, NULL),

-- Sinh viên 5
(1, 10, 'da_nop', 9.0, '2025-10-15 10:00:00', '2025-10-20 19:45:00', '2025-10-21 09:10:00', 1),
(2, 10, 'da_nop', 8.5, '2025-10-22 15:10:00', '2025-10-28 22:30:00', '2025-10-29 11:15:00', 1),
(3, 10, 'da_nop', NULL, '2025-11-08 08:20:00', '2025-11-18 00:05:00', NULL, NULL); 

-- ================================================
-- PHẦN 10: BÀI LÀM KIỂM TRA
-- ================================================

INSERT INTO bai_lam_kiem_tra (
    bai_kiem_tra_id,
    sinh_vien_id,
    trang_thai,
    diem,
    so_cau_dung,
    tong_so_cau,
    thoi_gian_bat_dau,
    thoi_gian_nop,
    thoi_gian_lam_bai
) VALUES
-- Bài kiểm tra giữa kỳ Power BI (id = 1) - đã diễn ra ngày 20/10/2025
(1, 6, 'da_nop', 9.50, 18, 20, '2025-10-20 18:20:00', '2025-10-20 18:48:00', 28),
(1, 7, 'da_nop', 8.5, 17, 20, '2025-10-20 18:25:00', '2025-10-20 18:50:00', 27),
(1, 8, 'da_nop', 10.00, 20, 20, '2025-10-20 18:20:00', '2025-10-20 18:45:00', 30),
(1, 9, 'da_nop', 7.00, 14, 20, '2025-10-20 18:30:00', '2025-10-20 18:50:00', 24),
(1, 10, 'da_nop', 8.5, 17, 20, '2025-10-20 18:22:00', '2025-10-20 18:49:00', 27),

-- Bài kiểm tra cuối kỳ Power BI (id = 2) - đã diễn ra ngày 3/11/2025 
(2, 11, 'da_nop', 9.00, 18, 20, '2025-11-03 18:25:00', '2025-11-03 19:05:00', 40),
(2, 12, 'da_nop', 7.50, 15, 20, '2025-11-03 18:28:00', '2025-11-03 19:05:00', 37),
(2, 10, 'da_nop', 9.50, 19, 20, '2025-11-03 18:25:00', '2025-11-03 18:58:00', 33),
(2, 9, 'da_nop', 6.50, 13, 20, '2025-11-03 18:35:00', '2025-11-03 19:05:00', 30),
(2, 13, 'chua_lam', NULL, 0, 20, NULL, NULL, 0), -- Chưa làm bài cuối kỳ

-- Bài kiểm tra giữa kỳ SQL (id = 3) - đã diễn ra ngày 18/11/2025
(3, 8, 'da_nop', 10.00, 20, 20, '2025-11-18 18:20:00', '2025-11-18 18:45:00', 25),
(3, 9, 'da_nop', 8.50, 17, 20, '2025-11-18 18:25:00', '2025-11-18 18:50:00', 25),
(3, 10, 'da_nop', 9.00, 18, 20, '2025-11-18 18:22:00', '2025-11-18 18:48:00', 26),
(3, 11, 'dang_lam', NULL, 12, 20, '2025-11-18 18:40:00', NULL, 15), 
(3, 12, 'da_nop', 7.50, 15, 20, '2025-11-18 18:30:00', '2025-11-18 18:50:00', 20);

-- ================================================
-- PHẦN 11: BÌNH LUẬN BÀI TẬP
-- ================================================

-- Chèn bình luận mẫu vào bảng bình luận bài tập
INSERT INTO binh_luan_bai_tap (
    bai_lam_id,          
    nguoi_gui_id,        
    noi_dung,
    thoi_gian_gui
) VALUES
-- Bình luận của giảng viên cho các bài làm (thường là góp ý, chấm điểm)
(1, 1, 'Bài làm tốt, phần xử lý dữ liệu sạch sẽ. +0.5 điểm thưởng!', '2025-10-21 10:15:30'),
(1, 6, 'Cảm ơn thầy, em sẽ cố gắng hơn ở bài sau ạ!', '2025-10-21 10:20:15'),
(3, 1, 'Phần mô hình dữ liệu còn thiếu relationship giữa 2 bảng, cần bổ sung.', '2025-10-29 12:45:00'),
(4, 1, 'Nộp muộn 2 ngày, trừ 2 điểm theo quy định lớp mình.', '2025-10-21 16:10:00'),
(5, 1, 'Rất tốt! Dashboard trực quan, màu sắc hài hòa.', '2025-10-29 14:30:22'),

-- Sinh viên hỏi đáp lẫn nhau
(2, 7, 'Bạn ơi cho mình hỏi phần slicer bạn làm thế nào để lọc theo quý vậy?', '2025-10-28 20:15:40'),
(2, 8, '@20127002 bạn vào Visualizations → Slicer → chọn trường Quarter là xong ấy mà', '2025-10-28 20:18:10'),
(7, 9, 'Mình bị lỗi "Cannot connect to data source" ai giúp với :((', '2025-11-05 09:55:20'),
(7, 10, '@20127003 bạn kiểm tra lại đường dẫn file Excel xem có bị di chuyển không nhé', '2025-11-05 10:02:45'),
(9, 1, 'Bài này em làm rất sáng tạo với drill-through, giữ phong độ nhé!', '2025-11-18 11:25:00'),

-- Một số bình luận mới nhất hôm nay (18/11/2025)
(12, 1, 'Tất cả các bạn chú ý deadline bài tập cuối kỳ là 23h59 ngày 30/11 nhé!', '2025-11-18 08:30:00'),
(12, 8, 'Dạ vâng thầy!', '2025-11-18 08:32:10'),
(15, 9, 'Em nộp rồi ạ thầy cô chấm giúp em với ạ', '2025-11-18 00:15:30'),
(15, 1, '@9 Thầy thấy rồi, sẽ chấm trong tối nay!', '2025-11-18 09:10:45'),
(8, 1, 'Nhiều bạn chưa nộp bài tập tuần này, hạn cuối là tối nay 23h59.', '2025-11-17 20:00:00');

-- ================================================
-- PHẦN 12: CÂU HỎI BÀI TẬP
-- ================================================

INSERT INTO cau_hoi_bai_tap (
    bai_tap_id,
    noi_dung_cau_hoi,
    mo_ta,
    diem
) VALUES
-- Bài tập 1.1 - L001 (id = 1): Tải file Excel bất kỳ
(1, 'File Excel bạn vừa tải về có tên là gì và có bao nhiêu sheet?', NULL, 1.00),
(1, 'Tổng số dòng dữ liệu (bao gồm cả header) trong sheet chính là bao nhiêu?', NULL, 1.50),
(1, 'Hãy chụp màn hình cửa sổ Power Query sau khi bạn đã Remove Duplicates cột "Mã khách hàng"', 'Đính kèm ảnh', 2.50),

-- Bài tập 1.2 - L001 (id = 2): Viết mô tả sự khác nhau
(2, 'Hãy nêu ít nhất 3 điểm khác biệt chính giữa Power BI Desktop và Power BI Service.', 'Trả lời ngắn gọn, gạch đầu dòng', 4.00),
(2, 'Calculated Column và Measure khác nhau như thế nào về thời điểm tính toán và nơi lưu trữ?', NULL, 3.00),
(2, 'Trong DAX, hàm nào dùng để bỏ qua filter từ visual khác?', 'Gợi ý: ALL, REMOVEFILTERS...', 3.00),

-- Bài tập 2.1 - L001 (id = 3): Import một bảng dữ liệu có ít nhất 2 bảng
(3, 'Bạn đã import bao nhiêu bảng vào model? Hãy liệt kê tên các bảng.', NULL, 1.50),
(3, 'Chụp màn hình Model view thể hiện rõ các relationship bạn đã tạo', 'Bắt buộc đính kèm ảnh', 3.50),

-- Bài tập 2.2 - L001 (id = 4): Merge 2 bảng
(4, 'Khi Merge bảng Orders và Returns, bạn đã chọn kiểu Join nào? (Left Outer/Inner/...) Và vì sao?', NULL, 3.00),
(4, 'Sau khi Merge, cột mới tạo ra có tên là gì? Chụp màn hình bước Merge Queries', NULL, 4.00),

-- Bài tập 1.1 - L002 (id = 5) và 1.2 - L002 (id = 6) giống L001 → thêm câu hỏi nâng cao hơn
(5, 'Trong file Excel của bạn, hãy dùng Power Query để Unpivot các cột tháng (Jan → Dec) thành 2 cột "Tháng" và "Doanh thu"', 'Chụp ảnh bước Unpivot', 4.00),
(6, 'Viết công thức DAX tính doanh thu quý hiện tại (QTD) và so sánh với quý trước (Previous Quarter)', NULL, 5.00),

-- Bài tập SQL L003
(7, 'Viết câu lệnh CREATE TABLE cho bảng quanly_sinhvien bao gồm các ràng buộc: PRIMARY KEY, NOT NULL, UNIQUE cho email', NULL, 4.00),
(8, 'Viết câu lệnh ALTER TABLE để thêm FOREIGN KEY từ bảng diem_thi tham chiếu đến sinh_vien và mon_hoc', NULL, 3.50),
(8, 'Viết truy vấn SELECT liệt kê top 5 sinh viên có điểm trung bình cao nhất (sử dụng JOIN, GROUP BY, ORDER BY)', NULL, 4.50);

-- ================================================
-- PHẦN 13: CHI TIẾT TRẢ LỜI
-- ================================================

INSERT INTO chi_tiet_tra_loi (
    bai_lam_kiem_tra_id,   
    cau_hoi_id,            
    lua_chon_id,           
    dung_hay_sai,          
    thoi_gian_tra_loi      
) VALUES
-- Sinh viên 20127001 làm bài kiểm tra giữa kỳ Power BI (bai_lam_kiem_tra_id = 1)
(1, 1, NULL, 1, '2025-11-20 18:22:15'),  
(1, 2, NULL, 1, '2025-11-20 18:23:40'),
(1, 3, NULL, 1, '2025-11-20 18:25:10'),
(1, 4, NULL, 1, '2025-11-20 18:27:30'),  

-- Sinh viên 20127002 (có sai vài câu)
(2, 1, NULL, 1, '2025-11-20 18:26:20'),
(2, 2, NULL, 0, '2025-11-20 18:28:45'),  
(2, 3, NULL, 1, '2025-11-20 18:30:15'),
(2, 4, NULL, 0, '2025-11-20 18:33:20'),  

-- Sinh viên 20127004 làm dở dang (chỉ trả lời 2/4 câu)
(4, 1, NULL, 1, '2025-11-20 18:32:10'),
(4, 2, NULL, 0, '2025-11-20 18:35:55'),
-- Câu 3,4 chưa trả lời → dung_hay_sai = NULL

-- Sinh viên 20127003 làm bài kiểm tra cuối kỳ (bai_lam_kiem_tra_id = 8)
(8, 1, NULL, 1, '2025-12-03 18:27:30'),
(8, 2, NULL, 1, '2025-12-03 18:30:45'),
(8, 3, NULL, 1, '2025-12-03 18:34:20'),
(8, 4, NULL, 0, '2025-12-03 18:38:15'),  

-- Sinh viên 20127006 đang làm dở bài cuối kỳ hôm nay (18/11/2025 chưa tới hạn)
(9, 1, NULL, 1, '2025-12-03 18:40:10'),
(9, 2, NULL, NULL, '2025-12-03 18:42:30');

-- ================================================
-- PHẦN 14: TIẾN ĐỘ HỌC TẬP
-- ================================================

INSERT INTO tien_do_hoc_tap (
    sinh_vien_id,
    lop_hoc_id,
    loai_noi_dung,     
    noi_dung_id,       
    da_hoan_thanh,     
    phan_tram_hoan_thanh,
    lan_cap_nhat_cuoi
) VALUES
-- Sinh viên chăm chỉ nhất lớp 
(8, 1, 'bai_giang', 1, 1, 100.00, '2025-11-10 09:15:20'),
(8, 1, 'bai_giang', 2, 1, 100.00, '2025-11-10 14:30:45'),
(8, 1, 'bai_giang', 3, 1, 100.00, '2025-11-12 20:10:00'),
(8, 1, 'bai_giang', 4, 1, 100.00, '2025-11-15 08:45:30'),

-- xem đều, còn đang xem dở vài bài
(9, 1, 'bai_giang', 1, 1, 100.00, '2025-11-11 10:20:15'),
(9, 1, 'bai_giang', 2, 1, 100.00, '2025-11-13 18:55:00'),
(9, 1, 'bai_giang', 3, 0, 78.50, '2025-11-18 15:42:10'), 
(9, 1, 'bai_giang', 4, 0, 45.00, '2025-11-18 16:10:30'), 

-- học trước lớp luôn
(10, 1, 'bai_giang', 1, 1, 100.00, '2025-11-09 21:30:00'),
(10, 1, 'bai_giang', 2, 1, 100.00, '2025-11-09 22:15:40'),
(10, 1, 'bai_giang', 5, 1, 100.00, '2025-11-17 19:20:00'),

-- Sinh viên chậm tiến độ 
(11, 1, 'bai_giang', 1, 1, 100.00, '2025-11-18 09:05:00'), 
(11, 1, 'bai_giang', 2, 0, 20.00, '2025-11-18 09:30:15'), 

(12, 1, 'bai_giang', 1, 0, 95.00, '2025-11-18 14:55:00'), 
(12, 1, 'bai_giang', 2, 0,  0.00, NULL);

-- ================================================
-- PHẦN 15: TÀI LIỆU LỚP HỌC
-- ================================================

-- Tài liệu cho lớp L001 (Power BI) - 1 file PDF duy nhất
INSERT INTO tai_lieu_lop_hoc (lop_hoc_id, ten_tai_lieu, loai_file, duong_dan_file, ten_file_goc, kich_thuoc_file, nguoi_upload_id)
VALUES
((SELECT id FROM lop_hoc WHERE ma_lop_hoc='L001'),
 'Báo cáo hệ thống thông tin',
 'pdf',
 '/public/teacher/uploads/tai-lieu/L001/Power BI.pdf',
 'Power BI.pdf',
 245760,
 (SELECT giang_vien_id FROM lop_hoc WHERE ma_lop_hoc='L001'));

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
