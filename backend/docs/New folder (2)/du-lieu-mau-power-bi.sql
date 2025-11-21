-- =====================================================
-- DỮ LIỆU MẪU: POWER BI - 1 GIẢNG VIÊN, 9 SINH VIÊN
-- =====================================================
-- Ngày tạo: 21/11/2025
-- Mô tả: Dữ liệu demo đầy đủ cho môn Power BI
-- Cấu trúc: 2 chương, mỗi chương có 2 bài giảng, bài tập theo mục, bài kiểm tra cuối chương
-- =====================================================

USE lms_hoc_tap;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. NGƯỜI DÙNG (1 GIẢNG VIÊN + 9 SINH VIÊN)
-- =====================================================

-- Giảng viên
INSERT INTO nguoi_dung (ma_nguoi_dung, ten_dang_nhap, email, mat_khau_hash, ho_ten, vai_tro, trang_thai, ngay_tao) VALUES
('GV001', 'nguyenvana', 'nguyenvana@university.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Nguyễn Văn A', 'giang_vien', 'hoat_dong', '2024-09-01 08:00:00');

-- 9 Sinh viên
INSERT INTO nguoi_dung (ma_nguoi_dung, ten_dang_nhap, email, mat_khau_hash, ho_ten, vai_tro, trang_thai, ngay_tao) VALUES
('23D192001', 'tranthib', 'tranthib.23d192001@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Trần Thị B', 'sinh_vien', 'hoat_dong', '2024-09-01 09:00:00'),
('23D192002', 'levanc', 'levanc.23d192002@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Lê Văn C', 'sinh_vien', 'hoat_dong', '2024-09-01 09:05:00'),
('23D192003', 'phamthid', 'phamthid.23d192003@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Phạm Thị D', 'sinh_vien', 'hoat_dong', '2024-09-01 09:10:00'),
('23D192004', 'hoangvane', 'hoangvane.23d192004@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Hoàng Văn E', 'sinh_vien', 'hoat_dong', '2024-09-01 09:15:00'),
('23D192005', 'vuthif', 'vuthif.23d192005@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Vũ Thị F', 'sinh_vien', 'hoat_dong', '2024-09-01 09:20:00'),
('23D192006', 'dangvang', 'dangvang.23d192006@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Đặng Văn G', 'sinh_vien', 'hoat_dong', '2024-09-01 09:25:00'),
('23D192007', 'buithih', 'buithih.23d192007@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Bùi Thị H', 'sinh_vien', 'hoat_dong', '2024-09-01 09:30:00'),
('23D192008', 'dovani', 'dovani.23d192008@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Đỗ Văn I', 'sinh_vien', 'hoat_dong', '2024-09-01 09:35:00'),
('23D192009', 'ngothik', 'ngothik.23d192009@student.edu.vn', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890', 'Ngô Thị K', 'sinh_vien', 'hoat_dong', '2024-09-01 09:40:00');

-- =====================================================
-- 2. MÔN HỌC VÀ LỚP HỌC
-- =====================================================

-- Môn học Power BI
INSERT INTO mon_hoc (ma_mon_hoc, ten_mon_hoc, mo_ta, so_tin_chi, ngay_tao) VALUES
('POWERBI01', 'Trực quan hóa dữ liệu với Power BI', 
'Môn học cung cấp kiến thức và kỹ năng về trực quan hóa dữ liệu, tạo báo cáo và dashboard tương tác bằng Microsoft Power BI. Sinh viên sẽ học cách kết nối, xử lý dữ liệu và tạo các biểu đồ chuyên nghiệp để phân tích và ra quyết định kinh doanh.',
3, '2024-09-01 08:00:00');

-- Lớp học
INSERT INTO lop_hoc (ma_lop_hoc, mon_hoc_id, giang_vien_id, ten_lop_hoc, so_sinh_vien_toi_da, trang_thai, ngay_tao) VALUES
('213_POWERBI01_01', 1, 1, 'Lớp Power BI - K21.3', 30, 'dang_mo', '2024-09-05 08:00:00');

-- Đăng ký sinh viên vào lớp
INSERT INTO sinh_vien_lop_hoc (sinh_vien_id, lop_hoc_id, mon_hoc_id, ngay_dang_ky, trang_thai) VALUES
(2, 1, 1, '2024-09-05 09:00:00', 'dang_hoc'),
(3, 1, 1, '2024-09-05 09:10:00', 'dang_hoc'),
(4, 1, 1, '2024-09-05 09:20:00', 'dang_hoc'),
(5, 1, 1, '2024-09-05 09:30:00', 'dang_hoc'),
(6, 1, 1, '2024-09-05 09:40:00', 'dang_hoc'),
(7, 1, 1, '2024-09-05 09:50:00', 'dang_hoc'),
(8, 1, 1, '2024-09-05 10:00:00', 'dang_hoc'),
(9, 1, 1, '2024-09-05 10:10:00', 'dang_hoc'),
(10, 1, 1, '2024-09-05 10:20:00', 'dang_hoc');

-- =====================================================
-- 3. CHƯƠNG HỌC
-- =====================================================

-- Chương 1: Giới thiệu Power BI
INSERT INTO chuong (lop_hoc_id, so_thu_tu_chuong, ten_chuong, muc_tieu, noi_dung, thu_tu_sap_xep, ngay_tao) VALUES
(1, 1, 'Giới thiệu và Làm việc với Power BI',
'Làm quen với Power BI Desktop, hiểu các thành phần cơ bản và cách kết nối với nguồn dữ liệu. Sinh viên sẽ biết cách cài đặt, giao diện và các bước đầu tiên để chuẩn bị dữ liệu.',
'Chương này giới thiệu về Power BI Desktop, các thành phần chính (Power Query, Power Pivot, Power View), cách cài đặt và cấu hình. Sinh viên sẽ học cách kết nối với nhiều nguồn dữ liệu khác nhau (Excel, CSV, SQL Server, Web) và sử dụng Power Query để làm sạch và chuyển đổi dữ liệu.',
1, '2024-09-10 08:00:00');

-- Chương 2: Trực quan hóa dữ liệu
INSERT INTO chuong (lop_hoc_id, so_thu_tu_chuong, ten_chuong, muc_tieu, noi_dung, thu_tu_sap_xep, ngay_tao) VALUES
(1, 2, 'Trực quan hóa và Tương tác với Dữ liệu',
'Tạo các báo cáo trực quan chuyên nghiệp, sử dụng các loại biểu đồ phù hợp và thiết kế dashboard tương tác. Sinh viên sẽ biết cách chia sẻ và xuất bản báo cáo.',
'Chương này hướng dẫn cách tạo các loại biểu đồ (cột, đường, tròn, bản đồ, KPI), sử dụng slicer và filter để tương tác, thiết kế dashboard đẹp mắt và hiệu quả. Sinh viên sẽ học cách xuất bản lên Power BI Service và chia sẻ với người dùng.',
2, '2024-10-15 08:00:00');

-- =====================================================
-- 4. BÀI GIẢNG VÀ VIDEO
-- =====================================================

-- Bài giảng Chương 1
INSERT INTO bai_giang (chuong_id, tieu_de, mo_ta, noi_dung, thu_tu_sap_xep, ngay_tao) VALUES
-- 1.1 Giới thiệu và cài đặt Power BI
(1, '1.1. Giới thiệu và Cài đặt Power BI Desktop',
'Tổng quan về Power BI, các phiên bản và cách cài đặt Power BI Desktop trên máy tính.',
'<h3>Mục tiêu bài học</h3>
<ul>
<li>Hiểu Power BI là gì và ứng dụng trong thực tế</li>
<li>Phân biệt Power BI Desktop, Power BI Service, Power BI Mobile</li>
<li>Cài đặt và khởi động Power BI Desktop</li>
<li>Làm quen với giao diện và các thành phần chính</li>
</ul>

<h3>Nội dung chính</h3>
<h4>1. Power BI là gì?</h4>
<p>Power BI là công cụ phân tích và trực quan hóa dữ liệu của Microsoft, giúp người dùng:</p>
<ul>
<li>Kết nối với nhiều nguồn dữ liệu</li>
<li>Chuyển đổi và làm sạch dữ liệu</li>
<li>Tạo báo cáo và dashboard tương tác</li>
<li>Chia sẻ insights với tổ chức</li>
</ul>

<h4>2. Các thành phần của Power BI</h4>
<ul>
<li><strong>Power BI Desktop:</strong> Ứng dụng Windows để tạo báo cáo</li>
<li><strong>Power BI Service:</strong> Nền tảng cloud để xuất bản và chia sẻ</li>
<li><strong>Power BI Mobile:</strong> App di động để xem báo cáo</li>
</ul>

<h4>3. Cài đặt Power BI Desktop</h4>
<ol>
<li>Truy cập <a href="https://powerbi.microsoft.com" target="_blank">powerbi.microsoft.com</a></li>
<li>Tải về Power BI Desktop (miễn phí)</li>
<li>Cài đặt và khởi động ứng dụng</li>
<li>Đăng nhập bằng tài khoản Microsoft (tùy chọn)</li>
</ol>

<h4>4. Giao diện Power BI Desktop</h4>
<ul>
<li><strong>Report View:</strong> Tạo và thiết kế báo cáo</li>
<li><strong>Data View:</strong> Xem và kiểm tra dữ liệu</li>
<li><strong>Model View:</strong> Tạo quan hệ giữa các bảng</li>
<li><strong>Ribbon:</strong> Các công cụ và chức năng</li>
<li><strong>Visualizations pane:</strong> Các loại biểu đồ</li>
<li><strong>Fields pane:</strong> Các trường dữ liệu</li>
</ul>',
1, '2024-09-10 08:00:00'),

-- 1.2 Kết nối và chuẩn bị dữ liệu
(1, '1.2. Kết nối Dữ liệu và Power Query',
'Học cách kết nối với nguồn dữ liệu và sử dụng Power Query để làm sạch, chuyển đổi dữ liệu.',
'<h3>Mục tiêu bài học</h3>
<ul>
<li>Kết nối với các nguồn dữ liệu phổ biến (Excel, CSV, Database)</li>
<li>Sử dụng Power Query Editor để làm sạch dữ liệu</li>
<li>Thực hiện các phép biến đổi dữ liệu cơ bản</li>
<li>Load dữ liệu vào Data Model</li>
</ul>

<h3>Nội dung chính</h3>
<h4>1. Kết nối với nguồn dữ liệu</h4>
<p>Power BI hỗ trợ kết nối với hơn 100 nguồn dữ liệu:</p>
<ul>
<li><strong>File:</strong> Excel, CSV, XML, JSON, PDF</li>
<li><strong>Database:</strong> SQL Server, MySQL, Oracle, PostgreSQL</li>
<li><strong>Cloud:</strong> Azure, SharePoint, Dynamics 365</li>
<li><strong>Web:</strong> Web API, OData feed</li>
<li><strong>Other:</strong> Python, R script</li>
</ul>

<h4>2. Power Query Editor</h4>
<p>Power Query là công cụ ETL (Extract, Transform, Load) mạnh mẽ:</p>
<ul>
<li>Xem trước và làm sạch dữ liệu</li>
<li>Ghi lại các bước chuyển đổi (Applied Steps)</li>
<li>Tự động refresh khi dữ liệu nguồn thay đổi</li>
</ul>

<h4>3. Các phép biến đổi phổ biến</h4>
<ul>
<li><strong>Remove Rows:</strong> Xóa dòng trống, lỗi, duplicate</li>
<li><strong>Change Type:</strong> Đổi kiểu dữ liệu (text, number, date)</li>
<li><strong>Split Column:</strong> Tách cột theo delimiter</li>
<li><strong>Merge Columns:</strong> Gộp nhiều cột</li>
<li><strong>Filter Rows:</strong> Lọc dữ liệu theo điều kiện</li>
<li><strong>Replace Values:</strong> Thay thế giá trị</li>
<li><strong>Pivot/Unpivot:</strong> Chuyển đổi cấu trúc bảng</li>
</ul>

<h4>4. Best Practices</h4>
<ul>
<li>Luôn kiểm tra data type</li>
<li>Đặt tên cột rõ ràng</li>
<li>Loại bỏ cột không cần thiết</li>
<li>Xử lý missing values</li>
<li>Tối ưu performance với Query Folding</li>
</ul>',
2, '2024-09-17 08:00:00');

-- Video cho bài giảng 1.1
INSERT INTO video_bai_giang (bai_giang_id, tieu_de, duong_dan_video, thoi_luong, thu_tu, ngay_tao) VALUES
(1, 'Video 1.1: Giới thiệu và Cài đặt Power BI Desktop', '/public/assets/video/power-bi/PowerBI.01.mp4', 180, 1, '2024-09-10 08:00:00');

-- Video cho bài giảng 1.2
INSERT INTO video_bai_giang (bai_giang_id, tieu_de, duong_dan_video, thoi_luong, thu_tu, ngay_tao) VALUES
(2, 'Video 1.2: Kết nối Dữ liệu và Power Query', '/public/assets/video/power-bi/PowerBI.02.mp4', 180, 1, '2024-09-17 08:00:00');

-- Bài giảng Chương 2
INSERT INTO bai_giang (chuong_id, tieu_de, mo_ta, noi_dung, thu_tu_sap_xep, ngay_tao) VALUES
-- 2.1 Tạo báo cáo và biểu đồ
(2, '2.1. Tạo Báo cáo và Biểu đồ',
'Học cách tạo các loại biểu đồ phổ biến và thiết kế báo cáo chuyên nghiệp.',
'<h3>Mục tiêu bài học</h3>
<ul>
<li>Hiểu các loại biểu đồ và khi nào nên sử dụng</li>
<li>Tạo các biểu đồ cơ bản (cột, đường, tròn, bảng)</li>
<li>Format và tùy chỉnh biểu đồ</li>
<li>Thiết kế layout báo cáo đẹp mắt</li>
</ul>

<h3>Nội dung chính</h3>
<h4>1. Các loại biểu đồ trong Power BI</h4>
<ul>
<li><strong>Column/Bar Chart:</strong> So sánh giá trị giữa các danh mục</li>
<li><strong>Line Chart:</strong> Hiển thị xu hướng theo thời gian</li>
<li><strong>Pie/Donut Chart:</strong> Hiển thị tỷ lệ phần trăm</li>
<li><strong>Card/KPI:</strong> Hiển thị số liệu quan trọng</li>
<li><strong>Table/Matrix:</strong> Hiển thị dữ liệu chi tiết</li>
<li><strong>Map:</strong> Hiển thị dữ liệu theo địa lý</li>
<li><strong>Scatter Chart:</strong> Phân tích correlation</li>
<li><strong>Gauge:</strong> Hiển thị tiến độ đạt mục tiêu</li>
</ul>

<h4>2. Tạo biểu đồ</h4>
<ol>
<li>Chọn loại biểu đồ từ Visualizations pane</li>
<li>Kéo thả fields vào các buckets (Axis, Values, Legend)</li>
<li>Format biểu đồ (màu sắc, font, title)</li>
<li>Thêm data labels và tooltips</li>
</ol>

<h4>3. Design Principles</h4>
<ul>
<li>Chọn màu sắc phù hợp với brand</li>
<li>Sử dụng whitespace hợp lý</li>
<li>Align và group các visuals liên quan</li>
<li>Tạo visual hierarchy rõ ràng</li>
<li>Sử dụng consistent formatting</li>
</ul>

<h4>4. DAX cơ bản</h4>
<p>Tạo Measures để tính toán động:</p>
<ul>
<li><strong>SUM, AVERAGE, COUNT:</strong> Aggregation functions</li>
<li><strong>CALCULATE:</strong> Thay đổi filter context</li>
<li><strong>DISTINCTCOUNT:</strong> Đếm unique values</li>
<li><strong>DIVIDE:</strong> Chia an toàn (tránh divide by zero)</li>
</ul>',
3, '2024-10-15 08:00:00'),

-- 2.2 Tương tác và chia sẻ báo cáo
(2, '2.2. Tương tác và Chia sẻ Báo cáo',
'Học cách tạo dashboard tương tác và chia sẻ báo cáo với người dùng.',
'<h3>Mục tiêu bài học</h3>
<ul>
<li>Tạo slicers và filters để tương tác</li>
<li>Thiết lập cross-filtering giữa các visuals</li>
<li>Xuất bản báo cáo lên Power BI Service</li>
<li>Chia sẻ và phân quyền báo cáo</li>
</ul>

<h3>Nội dung chính</h3>
<h4>1. Tương tác trong Power BI</h4>
<ul>
<li><strong>Slicers:</strong> Bộ lọc trực quan cho user</li>
<li><strong>Filters pane:</strong> Filter ở các level (Visual, Page, Report)</li>
<li><strong>Drill-down/up:</strong> Khám phá dữ liệu theo hierarchy</li>
<li><strong>Cross-filtering:</strong> Click vào một visual để filter các visual khác</li>
<li><strong>Bookmarks:</strong> Lưu trạng thái báo cáo để navigation</li>
<li><strong>Buttons:</strong> Tạo navigation và actions</li>
</ul>

<h4>2. Slicer Types</h4>
<ul>
<li>List slicer: Hiển thị danh sách checkbox</li>
<li>Dropdown slicer: Tiết kiệm không gian</li>
<li>Date range slicer: Chọn khoảng thời gian</li>
<li>Hierarchy slicer: Filter theo cấp bậc</li>
</ul>

<h4>3. Xuất bản lên Power BI Service</h4>
<ol>
<li>Click nút "Publish" trên Ribbon</li>
<li>Đăng nhập Power BI Service</li>
<li>Chọn workspace để publish</li>
<li>Báo cáo sẽ xuất hiện trên web</li>
</ol>

<h4>4. Chia sẻ báo cáo</h4>
<ul>
<li><strong>Share link:</strong> Chia sẻ trực tiếp cho người dùng</li>
<li><strong>Embed:</strong> Nhúng vào website, SharePoint</li>
<li><strong>Export:</strong> Export sang PDF, PowerPoint</li>
<li><strong>Workspace:</strong> Phân quyền theo nhóm</li>
<li><strong>App:</strong> Đóng gói nhiều báo cáo thành app</li>
</ul>

<h4>5. Scheduled Refresh</h4>
<ul>
<li>Cấu hình Gateway để kết nối on-premises data</li>
<li>Thiết lập lịch refresh tự động (hàng ngày, hàng tuần)</li>
<li>Nhận notification khi refresh thất bại</li>
</ul>',
4, '2024-10-22 08:00:00');

-- Video cho bài giảng 2.1
INSERT INTO video_bai_giang (bai_giang_id, tieu_de, duong_dan_video, thoi_luong, thu_tu, ngay_tao) VALUES
(3, 'Video 2.1: Tạo Báo cáo và Biểu đồ', '/public/assets/video/power-bi/PowerBI.03.mp4', 180, 1, '2024-10-15 08:00:00');

-- Video cho bài giảng 2.2
INSERT INTO video_bai_giang (bai_giang_id, tieu_de, duong_dan_video, thoi_luong, thu_tu, ngay_tao) VALUES
(4, 'Video 2.2: Tương tác và Chia sẻ Báo cáo', '/public/assets/video/power-bi/PowerBI.04.mp4', 180, 1, '2024-10-22 08:00:00');

-- =====================================================
-- 5. TÀI LIỆU
-- =====================================================

INSERT INTO tai_lieu_lop_hoc (lop_hoc_id, ten_tai_lieu, loai_file, duong_dan_file, ten_file_goc, kich_thuoc_file, nguoi_upload_id, thoi_gian_upload) VALUES
(1, 'Giáo trình Power BI - Full Course', 
'pdf',
'/public/teacher/uploads/tai-lieu/L001/Power BI.pdf',
'Power BI.pdf',
5242880, 
1, 
'2024-09-10 08:00:00');

-- =====================================================
-- 6. BÀI TẬP (4 BÀI - MỖI BÀI 1 CÂU HỎI TỰ LUẬN)
-- =====================================================

-- Bài tập 1.1
INSERT INTO bai_tap (lop_hoc_id, chuong_id, bai_giang_id, tieu_de, mo_ta, han_nop, diem_toi_da, ngay_tao) VALUES
(1, 1, 1, 'Bài tập 1.1: Cài đặt và Khám phá Power BI',
'Thực hành cài đặt Power BI Desktop và làm quen với giao diện. Hãy mô tả chi tiết các bước và chức năng bạn khám phá được.',
'2024-09-24 23:59:59',
10.00,
'2024-09-10 08:00:00');

INSERT INTO cau_hoi_bai_tap (bai_tap_id, noi_dung_cau_hoi, mo_ta, diem) VALUES
(1, 'Hãy mô tả chi tiết quá trình cài đặt Power BI Desktop trên máy tính của bạn. Sau khi cài đặt, hãy khám phá giao diện và liệt kê ít nhất 5 thành phần chính mà bạn nhận thấy, kèm theo chức năng của từng thành phần đó. Cuối cùng, hãy cho biết bạn nghĩ Power BI có thể ứng dụng như thế nào trong công việc thực tế?',
'Câu hỏi đánh giá khả năng thực hành và tư duy ứng dụng của sinh viên',
10.00);

-- Bài tập 1.2
INSERT INTO bai_tap (lop_hoc_id, chuong_id, bai_giang_id, tieu_de, mo_ta, han_nop, diem_toi_da, ngay_tao) VALUES
(1, 1, 2, 'Bài tập 1.2: Kết nối và Xử lý Dữ liệu',
'Thực hành kết nối với nguồn dữ liệu và sử dụng Power Query để làm sạch dữ liệu. Trình bày các bước thực hiện và kết quả đạt được.',
'2024-10-01 23:59:59',
10.00,
'2024-09-17 08:00:00');

INSERT INTO cau_hoi_bai_tap (bai_tap_id, noi_dung_cau_hoi, mo_ta, diem) VALUES
(2, 'Hãy tải một file dữ liệu Excel (có thể tự tạo hoặc download mẫu) và thực hiện kết nối vào Power BI. Sau đó sử dụng Power Query Editor để: 1) Xóa các dòng trống/lỗi, 2) Thay đổi kiểu dữ liệu phù hợp cho các cột, 3) Thực hiện ít nhất 2 phép biến đổi khác (split, merge, filter, replace...). Hãy chụp ảnh màn hình các bước thực hiện và giải thích tại sao bạn thực hiện các phép biến đổi đó.',
'Câu hỏi đánh giá kỹ năng thực hành Power Query và tư duy xử lý dữ liệu',
10.00);

-- Bài tập 2.1
INSERT INTO bai_tap (lop_hoc_id, chuong_id, bai_giang_id, tieu_de, mo_ta, han_nop, diem_toi_da, ngay_tao) VALUES
(1, 2, 3, 'Bài tập 2.1: Tạo Dashboard Phân tích Bán hàng',
'Sử dụng dữ liệu mẫu để tạo một báo cáo phân tích bán hàng với các biểu đồ phù hợp. Áp dụng design principles đã học.',
'2024-10-29 23:59:59',
10.00,
'2024-10-15 08:00:00');

INSERT INTO cau_hoi_bai_tap (bai_tap_id, noi_dung_cau_hoi, mo_ta, diem) VALUES
(3, 'Sử dụng dữ liệu của bạn (hoặc dữ liệu mẫu), hãy tạo một dashboard phân tích bao gồm: 1) Một biểu đồ cột so sánh doanh thu theo tháng, 2) Một biểu đồ tròn thể hiện tỷ lệ sản phẩm, 3) Một KPI card hiển thị tổng doanh thu, 4) Một bảng chi tiết top 5 sản phẩm bán chạy. Hãy chụp ảnh dashboard của bạn và giải thích tại sao bạn chọn các loại biểu đồ này và cách bạn format để dashboard trông chuyên nghiệp.',
'Câu hỏi đánh giá kỹ năng thiết kế dashboard và lựa chọn biểu đồ phù hợp',
10.00);

-- Bài tập 2.2
INSERT INTO bai_tap (lop_hoc_id, chuong_id, bai_giang_id, tieu_de, mo_ta, han_nop, diem_toi_da, ngay_tao) VALUES
(1, 2, 4, 'Bài tập 2.2: Tương tác và Chia sẻ Dashboard',
'Thêm các thành phần tương tác vào báo cáo và chia sẻ lên Power BI Service. Trình bày quá trình và kết quả.',
'2024-11-05 23:59:59',
10.00,
'2024-10-22 08:00:00');

INSERT INTO cau_hoi_bai_tap (bai_tap_id, noi_dung_cau_hoi, mo_ta, diem) VALUES
(4, 'Dựa trên dashboard bạn đã tạo ở bài tập 2.1, hãy thêm các thành phần tương tác: 1) Ít nhất 2 slicers để filter dữ liệu, 2) Thiết lập cross-filtering giữa các visuals, 3) Tạo ít nhất 1 bookmark để lưu trạng thái xem. Sau đó xuất bản báo cáo lên Power BI Service và chia sẻ (hoặc chụp màn hình quá trình publish). Hãy giải thích cách các thành phần tương tác này giúp người dùng khám phá dữ liệu tốt hơn.',
'Câu hỏi đánh giá kỹ năng tạo tương tác và chia sẻ báo cáo',
10.00);

-- =====================================================
-- 7. BÀI KIỂM TRA CHƯƠNG 1 (10 CÂU TRẮC NGHIỆM)
-- =====================================================

INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, thoi_gian_ket_thuc, diem_toi_da, cho_phep_lam_lai, ngay_tao) VALUES
(1, 1, 'Bài kiểm tra Chương 1: Giới thiệu và Làm việc với Power BI',
'Bài kiểm tra đánh giá kiến thức về Power BI Desktop, giao diện, kết nối dữ liệu và Power Query Editor.',
30,
'2024-10-05 14:00:00',
'2024-10-05 14:30:00',
10.00,
0,
'2024-09-25 08:00:00');

-- Câu hỏi 1
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 1, 'Power BI Desktop là gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(1, 1, 'Một ứng dụng web để xem báo cáo', FALSE),
(1, 2, 'Một ứng dụng Windows để tạo và thiết kế báo cáo', TRUE),
(1, 3, 'Một ứng dụng mobile để xem báo cáo trên điện thoại', FALSE),
(1, 4, 'Một công cụ lập trình để viết code', FALSE);

-- Câu hỏi 2
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 2, 'Trong Power BI Desktop, View nào được sử dụng để tạo và thiết kế các biểu đồ?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(2, 1, 'Data View', FALSE),
(2, 2, 'Model View', FALSE),
(2, 3, 'Report View', TRUE),
(2, 4, 'Query View', FALSE);

-- Câu hỏi 3
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 3, 'Power Query Editor được sử dụng để làm gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(3, 1, 'Tạo biểu đồ và báo cáo', FALSE),
(3, 2, 'Kết nối và chuyển đổi dữ liệu', TRUE),
(3, 3, 'Tạo quan hệ giữa các bảng', FALSE),
(3, 4, 'Xuất bản báo cáo lên cloud', FALSE);

-- Câu hỏi 4
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 4, 'Loại file nào sau đây Power BI KHÔNG thể kết nối trực tiếp?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(4, 1, 'Excel (.xlsx)', FALSE),
(4, 2, 'CSV (.csv)', FALSE),
(4, 3, 'Video (.mp4)', TRUE),
(4, 4, 'JSON (.json)', FALSE);

-- Câu hỏi 5
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 5, 'Trong Power Query, "Applied Steps" có tác dụng gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(5, 1, 'Hiển thị danh sách các bước chuyển đổi dữ liệu đã thực hiện', TRUE),
(5, 2, 'Hiển thị danh sách các biểu đồ trong báo cáo', FALSE),
(5, 3, 'Hiển thị danh sách các bảng dữ liệu', FALSE),
(5, 4, 'Hiển thị lịch sử xuất bản báo cáo', FALSE);

-- Câu hỏi 6
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 6, 'Phép biến đổi nào sau đây KHÔNG có trong Power Query?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(6, 1, 'Remove Rows', FALSE),
(6, 2, 'Split Column', FALSE),
(6, 3, 'Change Type', FALSE),
(6, 4, 'Create Chart', TRUE);

-- Câu hỏi 7
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 7, 'Khi nào nên sử dụng chức năng "Remove Duplicates" trong Power Query?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(7, 1, 'Khi muốn xóa tất cả dữ liệu', FALSE),
(7, 2, 'Khi muốn loại bỏ các dòng dữ liệu trùng lặp', TRUE),
(7, 3, 'Khi muốn tạo biểu đồ', FALSE),
(7, 4, 'Khi muốn xuất file Excel', FALSE);

-- Câu hỏi 8
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 8, 'Tại sao cần thay đổi kiểu dữ liệu (Change Type) trong Power Query?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(8, 1, 'Để Power BI hiểu đúng định dạng dữ liệu và tính toán chính xác', TRUE),
(8, 2, 'Để tạo biểu đồ đẹp hơn', FALSE),
(8, 3, 'Để tăng tốc độ tải báo cáo', FALSE),
(8, 4, 'Để thay đổi màu sắc của dữ liệu', FALSE);

-- Câu hỏi 9
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 9, 'Power BI Service là gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(9, 1, 'Ứng dụng desktop để tạo báo cáo', FALSE),
(9, 2, 'Nền tảng cloud để xuất bản và chia sẻ báo cáo', TRUE),
(9, 3, 'Công cụ để làm sạch dữ liệu', FALSE),
(9, 4, 'Phần mềm quản lý database', FALSE);

-- Câu hỏi 10
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(1, 10, 'Model View trong Power BI Desktop được sử dụng để làm gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(10, 1, 'Tạo biểu đồ và dashboard', FALSE),
(10, 2, 'Tạo quan hệ (relationship) giữa các bảng dữ liệu', TRUE),
(10, 3, 'Làm sạch và chuyển đổi dữ liệu', FALSE),
(10, 4, 'Xuất bản báo cáo lên web', FALSE);

-- =====================================================
-- 8. BÀI KIỂM TRA CHƯƠNG 2 (10 CÂU TRẮC NGHIỆM)
-- =====================================================

INSERT INTO bai_kiem_tra (lop_hoc_id, chuong_id, tieu_de, mo_ta, thoi_luong, thoi_gian_bat_dau, thoi_gian_ket_thuc, diem_toi_da, cho_phep_lam_lai, ngay_tao) VALUES
(1, 2, 'Bài kiểm tra Chương 2: Trực quan hóa và Tương tác với Dữ liệu',
'Bài kiểm tra đánh giá kiến thức về các loại biểu đồ, thiết kế dashboard, tương tác và chia sẻ báo cáo.',
30,
'2024-11-10 14:00:00',
'2024-11-10 14:30:00',
10.00,
0,
'2024-10-30 08:00:00');

-- Câu hỏi 1
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 1, 'Loại biểu đồ nào phù hợp nhất để hiển thị xu hướng doanh thu theo thời gian?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(11, 1, 'Pie Chart', FALSE),
(11, 2, 'Line Chart', TRUE),
(11, 3, 'Scatter Chart', FALSE),
(11, 4, 'Gauge Chart', FALSE);

-- Câu hỏi 2
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 2, 'Biểu đồ Pie Chart (biểu đồ tròn) thường được sử dụng để hiển thị điều gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(12, 1, 'Xu hướng theo thời gian', FALSE),
(12, 2, 'Tỷ lệ phần trăm của các thành phần', TRUE),
(12, 3, 'Mối quan hệ giữa hai biến số', FALSE),
(12, 4, 'Vị trí địa lý', FALSE);

-- Câu hỏi 3
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 3, 'DAX là viết tắt của gì trong Power BI?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(13, 1, 'Data Analysis Expressions', TRUE),
(13, 2, 'Data Access Extension', FALSE),
(13, 3, 'Desktop Analysis Excel', FALSE),
(13, 4, 'Dynamic Advanced XML', FALSE);

-- Câu hỏi 4
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 4, 'Slicer trong Power BI có tác dụng gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(14, 1, 'Tạo quan hệ giữa các bảng', FALSE),
(14, 2, 'Lọc dữ liệu một cách trực quan cho người dùng', TRUE),
(14, 3, 'Xuất file PDF', FALSE),
(14, 4, 'Tạo backup dữ liệu', FALSE);

-- Câu hỏi 5
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 5, 'Cross-filtering trong Power BI là gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(15, 1, 'Xóa dữ liệu không cần thiết', FALSE),
(15, 2, 'Khi click vào một visual, các visual khác tự động lọc theo', TRUE),
(15, 3, 'Tạo biểu đồ chéo', FALSE),
(15, 4, 'Sao chép dữ liệu giữa các trang', FALSE);

-- Câu hỏi 6
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 6, 'KPI Card trong Power BI được sử dụng để làm gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(16, 1, 'Hiển thị bản đồ địa lý', FALSE),
(16, 2, 'Hiển thị số liệu quan trọng một cách nổi bật', TRUE),
(16, 3, 'Tạo animation cho biểu đồ', FALSE),
(16, 4, 'Kết nối với database', FALSE);

-- Câu hỏi 7
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 7, 'Bookmark trong Power BI có công dụng gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(17, 1, 'Lưu trạng thái báo cáo để tạo navigation hoặc storytelling', TRUE),
(17, 2, 'Lưu file backup', FALSE),
(17, 3, 'Đánh dấu trang web yêu thích', FALSE),
(17, 4, 'Tạo relationship tự động', FALSE);

-- Câu hỏi 8
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 8, 'Để xuất bản báo cáo lên Power BI Service, bạn cần có gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(18, 1, 'Tài khoản Microsoft và quyền truy cập workspace', TRUE),
(18, 2, 'Chỉ cần Power BI Desktop', FALSE),
(18, 3, 'Server riêng để host', FALSE),
(18, 4, 'Không cần gì cả, tự động publish', FALSE);

-- Câu hỏi 9
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 9, 'Scheduled Refresh trong Power BI Service có tác dụng gì?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(19, 1, 'Tự động cập nhật dữ liệu theo lịch định sẵn', TRUE),
(19, 2, 'Tạo lịch họp tự động', FALSE),
(19, 3, 'Xóa báo cáo cũ định kỳ', FALSE),
(19, 4, 'Gửi email nhắc nhở', FALSE);

-- Câu hỏi 10
INSERT INTO cau_hoi_trac_nghiem (bai_kiem_tra_id, thu_tu, noi_dung_cau_hoi, diem) VALUES
(2, 10, 'Để chia sẻ báo cáo cho người dùng không có tài khoản Power BI Pro, bạn có thể sử dụng cách nào?', 1.00);

INSERT INTO lua_chon_cau_hoi (cau_hoi_id, thu_tu, noi_dung_lua_chon, la_dap_an_dung) VALUES
(20, 1, 'Không thể chia sẻ', FALSE),
(20, 2, 'Publish to Web (public) hoặc Export sang PDF/PowerPoint', TRUE),
(20, 3, 'Chỉ có thể gửi qua email', FALSE),
(20, 4, 'Phải cài Power BI Desktop cho họ', FALSE);

-- =====================================================
-- 9. TIẾN ĐỘ XEM VIDEO CHƯƠNG 1 (TẤT CẢ 9 SINH VIÊN ĐÃ XEM HẾT)
-- =====================================================

-- Sinh viên 1 (23D192001 - Trần Thị B) - Hoàn thành xuất sắc
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 2, 180, 180, 'hoan_thanh', '2024-09-12 10:30:00'),
(2, 2, 180, 180, 'hoan_thanh', '2024-09-19 09:45:00');

-- Sinh viên 2 (23D192002 - Lê Văn C) - Hoàn thành tốt
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 3, 180, 180, 'hoan_thanh', '2024-09-12 14:20:00'),
(2, 3, 180, 180, 'hoan_thanh', '2024-09-19 15:30:00');

-- Sinh viên 3 (23D192003 - Phạm Thị D) - Hoàn thành xuất sắc
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 4, 180, 180, 'hoan_thanh', '2024-09-11 16:00:00'),
(2, 4, 180, 180, 'hoan_thanh', '2024-09-18 11:15:00');

-- Sinh viên 4 (23D192004 - Hoàng Văn E) - Hoàn thành tốt
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 5, 180, 180, 'hoan_thanh', '2024-09-13 08:30:00'),
(2, 5, 180, 180, 'hoan_thanh', '2024-09-20 10:00:00');

-- Sinh viên 5 (23D192005 - Vũ Thị F) - Hoàn thành tốt
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 6, 180, 180, 'hoan_thanh', '2024-09-12 13:00:00'),
(2, 6, 180, 180, 'hoan_thanh', '2024-09-19 14:00:00');

-- Sinh viên 6 (23D192006 - Đặng Văn G) - Hoàn thành tốt
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 7, 180, 180, 'hoan_thanh', '2024-09-13 15:45:00'),
(2, 7, 180, 180, 'hoan_thanh', '2024-09-20 16:20:00');

-- Sinh viên 7 (23D192007 - Bùi Thị H) - Hoàn thành
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 8, 180, 180, 'hoan_thanh', '2024-09-14 09:00:00'),
(2, 8, 180, 180, 'hoan_thanh', '2024-09-21 10:30:00');

-- Sinh viên 8 (23D192008 - Đỗ Văn I) - Hoàn thành
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 9, 180, 180, 'hoan_thanh', '2024-09-14 11:00:00'),
(2, 9, 180, 180, 'hoan_thanh', '2024-09-21 13:00:00');

-- Sinh viên 9 (23D192009 - Ngô Thị K) - Hoàn thành
INSERT INTO tien_do_video (video_id, sinh_vien_id, thoi_gian_xem, tong_thoi_gian, trang_thai, lan_xem_cuoi) VALUES
(1, 10, 180, 180, 'hoan_thanh', '2024-09-13 17:00:00'),
(2, 10, 180, 180, 'hoan_thanh', '2024-09-20 18:00:00');

-- =====================================================
-- 10. TIẾN ĐỘ HỌC TẬP CHƯƠNG 1 (TẤT CẢ HOÀN THÀNH)
-- =====================================================

-- Tất cả 9 sinh viên hoàn thành bài giảng 1.1 và 1.2
INSERT INTO tien_do_hoc_tap (sinh_vien_id, bai_giang_id, trang_thai, ti_le_hoan_thanh, lan_cuoi_xem) VALUES
-- Bài giảng 1.1
(2, 1, 'hoan_thanh', 100.00, '2024-09-12 10:30:00'),
(3, 1, 'hoan_thanh', 100.00, '2024-09-12 14:20:00'),
(4, 1, 'hoan_thanh', 100.00, '2024-09-11 16:00:00'),
(5, 1, 'hoan_thanh', 100.00, '2024-09-13 08:30:00'),
(6, 1, 'hoan_thanh', 100.00, '2024-09-12 13:00:00'),
(7, 1, 'hoan_thanh', 100.00, '2024-09-13 15:45:00'),
(8, 1, 'hoan_thanh', 100.00, '2024-09-14 09:00:00'),
(9, 1, 'hoan_thanh', 100.00, '2024-09-14 11:00:00'),
(10, 1, 'hoan_thanh', 100.00, '2024-09-13 17:00:00'),
-- Bài giảng 1.2
(2, 2, 'hoan_thanh', 100.00, '2024-09-19 09:45:00'),
(3, 2, 'hoan_thanh', 100.00, '2024-09-19 15:30:00'),
(4, 2, 'hoan_thanh', 100.00, '2024-09-18 11:15:00'),
(5, 2, 'hoan_thanh', 100.00, '2024-09-20 10:00:00'),
(6, 2, 'hoan_thanh', 100.00, '2024-09-19 14:00:00'),
(7, 2, 'hoan_thanh', 100.00, '2024-09-20 16:20:00'),
(8, 2, 'hoan_thanh', 100.00, '2024-09-21 10:30:00'),
(9, 2, 'hoan_thanh', 100.00, '2024-09-21 13:00:00'),
(10, 2, 'hoan_thanh', 100.00, '2024-09-20 18:00:00');

-- =====================================================
-- 11. BÌNH LUẬN BÀI GIẢNG CHƯƠNG 1 (NHIỀU TƯƠNG TÁC)
-- =====================================================

-- Bình luận bài giảng 1.1
INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(1, 2, 'Em cảm ơn thầy! Video rất dễ hiểu và chi tiết. Em đã cài đặt thành công Power BI Desktop và khám phá được các chức năng cơ bản.', '2024-09-12 10:35:00'),
(1, 4, 'Thầy ơi, em thấy giao diện Power BI khá giống Excel nhưng mạnh hơn nhiều. Em rất hứng thú với môn học này!', '2024-09-11 16:10:00'),
(1, 1, 'Rất tốt! Các em đã nắm được bước đầu tiên. Hãy thử khám phá thêm các mẫu template có sẵn trong Power BI nhé.', '2024-09-12 11:00:00'),
(1, 3, 'Thầy cho em hỏi, nếu máy em không cài được Power BI Desktop thì có cách nào khác không ạ?', '2024-09-12 14:30:00'),
(1, 1, 'Em có thể sử dụng Power BI Service trực tiếp trên web, nhưng chức năng sẽ hạn chế hơn. Thầy khuyên em nên cài Desktop để học tốt nhất.', '2024-09-12 15:00:00'),
(1, 5, 'Em đã cài xong và thử import file Excel vào. Rất thú vị! Cảm ơn thầy!', '2024-09-13 08:45:00'),
(1, 7, 'Video của thầy rất clear, em học được nhiều. Em đã note lại các phần quan trọng rồi ạ.', '2024-09-13 16:00:00'),
(1, 9, 'Thầy ơi, phần Visualizations pane có thể tùy chỉnh được không ạ? Em muốn thêm các loại biểu đồ khác.', '2024-09-14 11:15:00'),
(1, 1, 'Có em nhé! Em có thể tải thêm custom visuals từ AppSource. Thầy sẽ hướng dẫn ở các bài sau.', '2024-09-14 12:00:00'),
(1, 6, 'Em cảm thấy Power BI dễ sử dụng hơn em nghĩ. Thank you teacher!', '2024-09-12 13:15:00');

-- Bình luận bài giảng 1.2
INSERT INTO binh_luan_bai_giang (bai_giang_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(2, 2, 'Power Query thật sự rất mạnh! Em đã thử remove duplicates và split column, kết quả rất tốt.', '2024-09-19 10:00:00'),
(2, 4, 'Thầy ơi, Applied Steps có thể undo được không ạ? Em có thao tác nhầm một bước.', '2024-09-18 11:30:00'),
(2, 1, 'Có em! Em chỉ cần click vào dấu X bên cạnh step đó hoặc click vào step trước đó để quay lại. Rất đơn giản!', '2024-09-18 12:00:00'),
(2, 3, 'Em kết nối CSV file và thấy data type tự động detect không đúng. Em đã change type thành công. Cảm ơn thầy!', '2024-09-19 15:45:00'),
(2, 5, 'Phần Merge Columns rất hữu ích. Em đã gộp họ và tên thành một cột Full Name.', '2024-09-20 10:15:00'),
(2, 8, 'Thầy cho em hỏi, Query Folding là gì vậy ạ? Em thấy trong video có nhắc đến.', '2024-09-21 10:45:00'),
(2, 1, 'Query Folding là khi Power Query đẩy các phép biến đổi xuống database để xử lý, giúp tăng performance. Thầy sẽ giải thích kỹ hơn ở bài nâng cao.', '2024-09-21 11:00:00'),
(2, 6, 'Em đã làm sạch được một file Excel có nhiều lỗi. Power Query thật sự tiết kiệm thời gian!', '2024-09-19 14:20:00'),
(2, 7, 'Các bước transform rất trực quan. Em thích cái cách nó lưu lại từng bước để có thể review sau.', '2024-09-20 16:35:00'),
(2, 10, 'Video này hơi nhanh một chút, nhưng em đã rewatch và hiểu rõ rồi. Cảm ơn thầy!', '2024-09-20 18:15:00'),
(2, 9, 'Em thắc mắc về phần Replace Values, có thể replace theo pattern không ạ?', '2024-09-21 13:15:00'),
(2, 1, 'Có em! Em có thể dùng Replace Values với pattern hoặc dùng Transform > Replace Values > Advanced để sử dụng regex.', '2024-09-21 14:00:00');

-- =====================================================
-- 12. BÀI LÀM BÀI TẬP CHƯƠNG 1 (TẤT CẢ 9 SINH VIÊN)
-- =====================================================

-- BÀI TẬP 1.1 - Tất cả 9 sinh viên đã làm

-- Sinh viên 1: Trần Thị B - 10 điểm (xuất sắc)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 2, 'da_cham', 10.00, '2024-09-12 11:00:00', '2024-09-13 15:30:00', '2024-09-14 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(1, 1, 'Quá trình cài đặt Power BI Desktop:

Bước 1: Truy cập trang chủ powerbi.microsoft.com và click "Download" hoặc tải từ Microsoft Store
Bước 2: Chạy file cài đặt PBIDesktopSetup_x64.exe
Bước 3: Chọn ngôn ngữ và đồng ý điều khoản sử dụng
Bước 4: Chọn thư mục cài đặt (mặc định C:\\Program Files\\Microsoft Power BI Desktop)
Bước 5: Hoàn tất cài đặt và khởi động ứng dụng

5 thành phần chính trong giao diện Power BI Desktop:

1. Report View (biểu tượng báo cáo): Đây là view chính để tạo và thiết kế các visualizations. Tôi có thể kéo thả các trường dữ liệu để tạo biểu đồ, bảng, card và các visual khác.

2. Data View (biểu tượng bảng): View này cho phép xem, kiểm tra và validate dữ liệu đã import. Tôi có thể xem tất cả các bảng, cột và dữ liệu chi tiết.

3. Model View (biểu tượng quan hệ): View để tạo và quản lý relationships giữa các bảng. Rất hữu ích khi làm việc với nhiều bảng có liên kết.

4. Visualizations Pane (khung bên phải): Chứa tất cả các loại biểu đồ có sẵn như column, line, pie, map, table, matrix, KPI... Tôi có thể chọn visual phù hợp cho từng loại dữ liệu.

5. Fields Pane (khung bên phải dưới): Hiển thị tất cả các bảng và trường dữ liệu đã import. Tôi kéo thả từ đây vào canvas hoặc vào các buckets của visual.

Ứng dụng trong thực tế:
Power BI có thể ứng dụng rất nhiều trong công việc thực tế như:
- Phân tích doanh số bán hàng theo thời gian, sản phẩm, khu vực
- Tạo dashboard theo dõi KPI cho quản lý
- Phân tích hành vi khách hàng, xu hướng mua sắm
- Báo cáo tài chính, kế toán tự động
- Phân tích nhân sự (tuyển dụng, đánh giá, lương thưởng)
- Theo dõi vận hành sản xuất, inventory

Tôi nghĩ Power BI sẽ giúp các doanh nghiệp ra quyết định nhanh hơn dựa trên dữ liệu thực tế thay vì cảm tính.', 10.00, '2024-09-13 15:30:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(1, 1, 'Bài làm xuất sắc! Em đã mô tả rất chi tiết và chính xác các bước cài đặt cũng như các thành phần trong giao diện. Phần ứng dụng thực tế cũng rất hay. Điểm 10!', '2024-09-14 10:00:00'),
(1, 2, 'Em cảm ơn thầy ạ! Em sẽ tiếp tục cố gắng!', '2024-09-14 10:30:00');

-- Sinh viên 2: Lê Văn C - 9 điểm (giỏi)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 3, 'da_cham', 9.00, '2024-09-12 15:00:00', '2024-09-14 10:00:00', '2024-09-14 11:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(2, 1, 'Cài đặt Power BI Desktop:

Em tải Power BI Desktop từ trang chủ và chạy file setup. Quá trình cài đặt khá nhanh, chỉ mất khoảng 5 phút. Sau khi cài xong, em mở ứng dụng lên và đã thấy giao diện chính.

5 thành phần chính:

1. Report View: Nơi tạo báo cáo và các biểu đồ. Em có thể thiết kế layout và kéo thả visuals.

2. Data View: Xem dữ liệu dạng bảng. Em thấy rất tiện để kiểm tra data sau khi import.

3. Model View: Tạo relationship giữa các bảng. Em chưa dùng nhiều vì mới làm quen.

4. Visualizations Pane: Có rất nhiều loại biểu đồ để chọn. Em thích nhất là column chart và pie chart.

5. Fields Pane: Danh sách tất cả các trường dữ liệu. Em kéo từ đây vào visual để tạo biểu đồ.

Ứng dụng thực tế:
Em nghĩ Power BI có thể dùng trong nhiều lĩnh vực:
- Công ty có thể dùng để phân tích doanh thu, lợi nhuận
- Theo dõi hiệu suất nhân viên
- Phân tích xu hướng khách hàng
- Tạo báo cáo tự động thay vì làm Excel thủ công

Em thấy Power BI giúp tiết kiệm thời gian và làm việc hiệu quả hơn nhiều.', 9.00, '2024-09-14 10:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(2, 1, 'Bài làm tốt! Em đã nắm được các thành phần chính. Tuy nhiên lần sau em có thể mô tả chi tiết hơn về cách sử dụng từng thành phần. Điểm 9!', '2024-09-14 11:00:00'),
(2, 3, 'Em cảm ơn thầy! Em sẽ cố gắng viết chi tiết hơn ạ.', '2024-09-14 11:30:00');

-- Sinh viên 3: Phạm Thị D - 10 điểm (xuất sắc)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 4, 'da_cham', 10.00, '2024-09-11 17:00:00', '2024-09-13 09:00:00', '2024-09-14 11:15:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(3, 1, 'Chi tiết quá trình cài đặt Power BI Desktop:

Bước 1: Truy cập https://powerbi.microsoft.com/desktop
Bước 2: Click "Download free" và chọn phiên bản 64-bit
Bước 3: Chạy file PBIDesktopSetup_x64.exe với quyền Administrator
Bước 4: Chọn ngôn ngữ English (United States) - em thấy tiếng Anh dễ tìm tài liệu hơn
Bước 5: Accept terms and conditions
Bước 6: Chọn destination folder (em để mặc định)
Bước 7: Wait for installation (~2-3 minutes)
Bước 8: Launch Power BI Desktop

5 thành phần chính và chức năng:

1. REPORT VIEW (Canvas): 
- Đây là workspace chính để design reports
- Em có thể kéo thả visuals, resize, arrange layout
- Có ruler và gridlines để align chính xác
- Support multiple pages trong một report

2. DATA VIEW:
- Xem raw data sau khi import
- Có thể sort, filter để kiểm tra data quality
- Tạo calculated columns bằng DAX
- Useful để validate data trước khi visualize

3. MODEL VIEW:
- Quản lý data model và relationships
- Drag and drop để tạo relationship giữa tables
- Configure cardinality (1:1, 1:Many, Many:Many)
- Rất quan trọng khi work với multiple related tables

4. VISUALIZATIONS PANE:
- Library của 30+ built-in visuals
- Bao gồm: Column/Bar charts, Line charts, Pie/Donut, Cards, Tables, Matrix, Maps, Treemap, Waterfall, Funnel, Scatter, Gauge, KPI...
- Có thể import thêm custom visuals từ AppSource
- Mỗi visual có format options riêng

5. FIELDS PANE:
- Hierarchical view của all tables và columns
- Show data type icon (text, number, date, boolean)
- Right-click để rename, hide, create hierarchy
- Có search box để tìm field nhanh

Ứng dụng Power BI trong thực tế:

Sales & Marketing:
- Dashboard theo dõi doanh số realtime
- Phân tích customer segmentation
- Campaign performance tracking
- Sales forecasting

Finance:
- Báo cáo P&L tự động
- Budget vs Actual analysis
- Cash flow monitoring
- Financial ratios dashboard

Operations:
- Supply chain visibility
- Inventory optimization
- Production efficiency tracking
- Quality control metrics

HR Analytics:
- Headcount reporting
- Recruitment funnel analysis
- Employee performance dashboard
- Training effectiveness

Em nghĩ Power BI là công cụ cần thiết cho mọi doanh nghiệp data-driven trong kỷ nguyên số hóa. Thay vì làm báo cáo Excel thủ công hàng tuần, chúng ta có thể automate và real-time update, giúp decision makers có insights nhanh chóng và chính xác hơn.', 10.00, '2024-09-13 09:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(3, 1, 'Xuất sắc! Bài làm rất chi tiết, chuyên nghiệp và thể hiện sự hiểu biết sâu sắc. Em đã research thêm nhiều thông tin bên ngoài. Perfect 10!', '2024-09-14 11:15:00'),
(3, 4, 'Cảm ơn thầy! Em rất thích môn này nên đã tìm hiểu thêm nhiều tài liệu.', '2024-09-14 11:45:00');

-- Sinh viên 4: Hoàng Văn E - 8.5 điểm (khá giỏi)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 5, 'da_cham', 8.50, '2024-09-13 09:00:00', '2024-09-15 16:00:00', '2024-09-16 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(4, 1, 'Cài đặt Power BI:
Em download từ website Microsoft và cài đặt theo hướng dẫn. Khá đơn giản, chỉ cần next, next, finish.

Các thành phần chính:
1. Report View: Tạo báo cáo
2. Data View: Xem dữ liệu
3. Model View: Tạo liên kết bảng
4. Visualizations: Chọn biểu đồ
5. Fields: Các trường dữ liệu

Em cũng thấy có ribbon với các menu Home, Insert, Modeling, View, Help. Ngoài ra còn có Filters pane để lọc dữ liệu.

Ứng dụng thực tế:
- Phân tích doanh thu bán hàng
- Báo cáo tài chính
- Dashboard quản lý
- Phân tích dữ liệu khách hàng
- Theo dõi KPI công ty

Power BI giúp visualize data dễ hiểu hơn, giúp manager ra quyết định tốt hơn.', 8.50, '2024-09-15 16:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(4, 1, 'Bài làm khá tốt! Em đã nắm được các điểm chính. Tuy nhiên em nên mô tả chi tiết hơn về chức năng của từng thành phần. Điểm 8.5!', '2024-09-16 09:00:00');

-- Sinh viên 5: Vũ Thị F - 8 điểm (khá)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 6, 'da_cham', 8.00, '2024-09-12 14:00:00', '2024-09-16 10:00:00', '2024-09-16 14:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(5, 1, 'Em đã cài Power BI Desktop thành công theo video hướng dẫn của thầy.

5 thành phần em thấy:
1. Report View - làm báo cáo
2. Data View - xem data
3. Model View - liên kết bảng
4. Visualizations pane - chọn chart
5. Fields pane - các cột dữ liệu

Power BI có thể dùng để:
- Làm báo cáo doanh số
- Phân tích khách hàng
- Dashboard cho sếp
- Báo cáo tự động
- Thay thế Excel

Em nghĩ công cụ này rất hữu ích cho công việc sau này.', 8.00, '2024-09-16 10:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(5, 1, 'Bài làm đạt yêu cầu! Em đã liệt kê được các thành phần chính. Lần sau hãy mô tả kỹ hơn về chức năng và cách sử dụng nhé. Điểm 8!', '2024-09-16 14:00:00');

-- Sinh viên 6: Đặng Văn G - 7 điểm (trung bình khá)
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 7, 'da_cham', 7.00, '2024-09-13 16:00:00', '2024-09-17 18:00:00', '2024-09-18 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(6, 1, 'Cài đặt Power BI:
Em download và cài đặt bình thường.

Giao diện có:
1. Report View
2. Data View
3. Model View
4. Visualizations
5. Fields

Dùng để phân tích dữ liệu và tạo báo cáo cho công ty.', 7.00, '2024-09-17 18:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(6, 1, 'Bài làm của em còn sơ sài. Em cần mô tả chi tiết hơn về quá trình cài đặt và chức năng của từng thành phần. Hãy cố gắng hơn ở bài sau nhé! Điểm 7.', '2024-09-18 09:00:00'),
(6, 7, 'Vâng ạ, em sẽ cố gắng viết chi tiết hơn. Em cảm ơn thầy!', '2024-09-18 10:00:00');

-- Sinh viên 7: Bùi Thị H - 8 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 8, 'da_cham', 8.00, '2024-09-14 10:00:00', '2024-09-16 14:30:00', '2024-09-17 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(7, 1, 'Quá trình cài đặt Power BI Desktop:
Em tải từ trang chủ Microsoft và cài đặt theo wizard. Quá trình khá nhanh chóng.

5 thành phần chính:
1. Report View: Nơi thiết kế báo cáo và tạo visualizations
2. Data View: Xem và kiểm tra dữ liệu đã import
3. Model View: Tạo relationships giữa các bảng
4. Visualizations Pane: Chứa các loại biểu đồ có sẵn
5. Fields Pane: Danh sách tables và columns

Ứng dụng thực tế:
Power BI có thể ứng dụng trong nhiều lĩnh vực như:
- Phân tích kinh doanh: doanh số, lợi nhuận, chi phí
- Nhân sự: quản lý nhân viên, performance
- Marketing: campaign analysis
- Tài chính: báo cáo tài chính tự động

Em thấy Power BI giúp tiết kiệm thời gian so với làm báo cáo Excel thủ công.', 8.00, '2024-09-16 14:30:00');

-- Sinh viên 8: Đỗ Văn I - 6.5 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 9, 'da_cham', 6.50, '2024-09-14 12:00:00', '2024-09-18 20:00:00', '2024-09-19 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(8, 1, 'Em cài Power BI Desktop từ Microsoft Store.

Giao diện gồm:
1. Report View - tạo báo cáo
2. Data View - xem data
3. Model View - nối bảng
4. Visuals - biểu đồ
5. Fields - dữ liệu

Dùng để làm báo cáo và phân tích dữ liệu trong công ty.', 6.50, '2024-09-18 20:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(8, 1, 'Bài làm của em quá ngắn gọn. Em cần viết chi tiết hơn về các bước cài đặt và giải thích rõ chức năng của từng thành phần. Điểm 6.5.', '2024-09-19 09:00:00'),
(8, 9, 'Em xin lỗi thầy, lần sau em sẽ cố gắng làm kỹ hơn ạ.', '2024-09-19 10:00:00');

-- Sinh viên 9: Ngô Thị K - 7.5 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(1, 10, 'da_cham', 7.50, '2024-09-13 18:00:00', '2024-09-17 15:00:00', '2024-09-18 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(9, 1, 'Cài đặt Power BI Desktop:
Em vào trang powerbi.microsoft.com, chọn Download và cài đặt theo hướng dẫn. Sau khi cài xong, em mở app lên và khám phá giao diện.

5 thành phần chính:
1. Report View: Tạo và design reports với các visuals
2. Data View: Xem raw data đã import
3. Model View: Tạo relationship giữa tables
4. Visualizations Pane: Chọn loại biểu đồ (column, pie, line...)
5. Fields Pane: Danh sách các fields để kéo vào visual

Ứng dụng trong công việc:
- Dashboard theo dõi KPI
- Báo cáo bán hàng tự động
- Phân tích customer behavior
- Financial reporting
- HR analytics

Power BI giúp visualize data một cách trực quan, dễ hiểu, support decision making tốt hơn.', 7.50, '2024-09-17 15:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(9, 1, 'Bài làm khá tốt! Em đã liệt kê đầy đủ các thành phần. Lần sau hãy mô tả chi tiết hơn về cách sử dụng từng phần. Điểm 7.5!', '2024-09-18 10:00:00');

-- BÀI TẬP 1.2 - Tất cả 9 sinh viên đã làm

-- Sinh viên 1: Trần Thị B - 10 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 2, 'da_cham', 10.00, '2024-09-19 11:00:00', '2024-09-21 16:00:00', '2024-09-22 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(10, 2, 'Em đã thực hành kết nối và xử lý dữ liệu trong Power Query như sau:

BƯỚC 1: CHUẨN BỊ DỮ LIỆU
Em tải file Excel mẫu "Sales_Data_Sample.xlsx" từ internet với các cột: OrderDate, Product, Category, Quantity, UnitPrice, CustomerName, City, Country.

BƯỚC 2: KẾT NỐI DỮ LIỆU VÀO POWER BI
- Mở Power BI Desktop
- Click "Get Data" > "Excel Workbook"
- Browse và chọn file Sales_Data_Sample.xlsx
- Chọn sheet "Sales" và click "Transform Data" để mở Power Query Editor

BƯỚC 3: XÓA CÁC DÒNG TRỐNG/LỖI
[Hình ảnh Power Query Editor - Before]
- Em nhận thấy có 5 dòng trống ở cuối bảng
- Click "Remove Rows" > "Remove Blank Rows"
- Kết quả: Từ 105 dòng còn lại 100 dòng dữ liệu hợp lệ

BƯỚC 4: THAY ĐỔI KIỂU DỮ LIỆU
[Hình ảnh Change Type]
Ban đầu Power BI auto-detect không chính xác:
- OrderDate: Text → Change thành Date
- Quantity: Text → Change thành Whole Number
- UnitPrice: Text → Change thành Decimal Number
- CustomerName: đúng rồi (Text)
- City, Country: đúng rồi (Text)

Lý do: Nếu không đổi đúng type, em sẽ không thể tính toán số học (sum, average) hoặc sort theo thời gian đúng cách.

BƯỚC 5: PHÉP BIẾN ĐỔI 1 - SPLIT COLUMN
[Hình ảnh Split Column]
- Em split cột CustomerName thành FirstName và LastName
- Select column CustomerName > Transform > Split Column > By Delimiter
- Chọn delimiter là Space, split at "First occurrence"
- Kết quả: 2 cột mới FirstName và LastName

Lý do: Giúp phân tích theo tên hoặc họ riêng biệt, có thể personalize communication tốt hơn.

BƯỚC 6: PHÉP BIẾN ĐỔI 2 - ADD CUSTOM COLUMN (TotalAmount)
[Hình ảnh Add Column]
- Click "Add Column" > "Custom Column"
- Tên cột: TotalAmount
- Formula: [Quantity] * [UnitPrice]
- Kết quả: Cột mới tính tổng tiền mỗi order

Lý do: Cần thiết để phân tích doanh thu, không có sẵn trong data gốc.

BƯỚC 7: PHÉP BIẾN ĐỔI 3 - REPLACE VALUES
[Hình ảnh Replace Values]
- Em thấy trong cột Country có giá trị "US" và "USA" (không consistent)
- Select column Country > Transform > Replace Values
- Value to Find: "US"
- Replace With: "USA"

Lý do: Đảm bảo data consistency, tránh việc phân tích bị duplicate do tên khác nhau.

BƯỚC 8: PHÉP BIẾN ĐỔI 4 - FILTER ROWS
[Hình ảnh Filter]
- Em filter chỉ lấy các order từ năm 2024
- Click dropdown ở OrderDate > Date Filters > Year > 2024

Lý do: Focus vào dữ liệu gần đây nhất để phân tích xu hướng hiện tại.

KẾT QUẢ CUỐI CÙNG:
[Hình ảnh Final Data]
- 85 dòng data (sau khi filter năm 2024)
- Không còn dòng trống/lỗi
- Tất cả data type chính xác
- Có thêm cột TotalAmount để phân tích
- Data đã được standardize (USA thay vì US)

Applied Steps trong Power Query:
1. Source
2. Navigation
3. Removed Blank Rows
4. Changed Type (multiple columns)
5. Split Column by Delimiter
6. Added Custom (TotalAmount)
7. Replaced Value (US to USA)
8. Filtered Rows (Year 2024)

KẾT LUẬN:
Power Query rất mạnh mẽ trong việc clean và transform data. Em thực hiện các bước này vì:
- Remove blank rows: Loại bỏ noise trong data
- Change type: Đảm bảo tính toán chính xác
- Split column: Tăng tính linh hoạt phân tích
- Add custom column: Tạo metrics mới cần thiết
- Replace values: Standardize data
- Filter rows: Focus vào data relevant

Tất cả các bước này được lưu lại trong Applied Steps, nên nếu data source update, em chỉ cần Refresh là Power Query sẽ tự động apply lại toàn bộ transformations!', 10.00, '2024-09-21 16:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(10, 1, 'Xuất sắc! Em đã thực hành rất kỹ lưỡng và giải thích rõ ràng từng bước. Cách em trình bày với screenshots và lý do cho mỗi transformation rất chuyên nghiệp. Perfect 10!', '2024-09-22 10:00:00'),
(10, 2, 'Cảm ơn thầy! Em cảm thấy Power Query rất thú vị và hữu ích.', '2024-09-22 10:30:00');

-- Sinh viên 2: Lê Văn C - 9 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 3, 'da_cham', 9.00, '2024-09-19 16:00:00', '2024-09-22 14:00:00', '2024-09-23 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(11, 2, 'Em thực hành kết nối và xử lý dữ liệu Excel như sau:

Kết nối dữ liệu:
Em tải file Excel có data về nhân viên (Employee_Data.xlsx) với các cột: EmployeeID, FullName, Department, Salary, HireDate, Email.

Get Data > Excel > Chọn file > Transform Data

Xử lý trong Power Query:

1. Remove blank rows: 
Em thấy có 3 dòng trống, đã remove thành công.

2. Change data type:
- EmployeeID: Text (đúng rồi)
- Salary: Changed từ Text sang Decimal
- HireDate: Changed từ Text sang Date
Lý do: Để có thể tính toán salary và sort theo ngày tháng.

3. Split FullName column:
Em split thành FirstName và LastName bằng Space delimiter.
Lý do: Dễ dàng sort theo họ hoặc tên.

4. Replace Values trong Department:
Em thấy có "IT" và "Information Technology" nên replace hết thành "IT" cho consistent.

5. Add Custom Column "YearsOfService":
Formula: Duration.Years(DateTime.LocalNow() - [HireDate])
Tính số năm làm việc của nhân viên.

Kết quả: Data đã sạch và ready để phân tích. Em đã screenshot các bước trong Applied Steps.', 9.00, '2024-09-22 14:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(11, 1, 'Bài làm tốt! Em đã thực hiện đầy đủ các transformations cần thiết và giải thích rõ lý do. Lần sau hãy include screenshots để minh họa rõ hơn. Điểm 9!', '2024-09-23 09:00:00');

-- Sinh viên 3: Phạm Thị D - 10 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 4, 'da_cham', 10.00, '2024-09-18 12:00:00', '2024-09-20 18:00:00', '2024-09-21 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(12, 2, 'POWER QUERY DATA TRANSFORMATION PROJECT

Dataset: Customer_Purchases_2024.xlsx
Source: Kaggle - Retail Sales Dataset
Records: 250 rows x 8 columns

STEP 1: DATA CONNECTION
[Screenshot 1: Get Data Dialog]
- Power BI Desktop > Get Data > Excel Workbook
- File path: C:\\Users\\MyUser\\Downloads\\Customer_Purchases_2024.xlsx
- Selected Table: "Transactions"
- Action: Transform Data (opens Power Query Editor)

STEP 2: DATA PROFILING & QUALITY CHECK
[Screenshot 2: Column Quality View]
- Enabled: View > Column Quality, Column Distribution, Column Profile
- Findings:
  * 15 blank rows (6% of data)
  * "CustomerEmail" column: 23 errors (invalid email format)
  * "PurchaseDate" column: Mixed formats (DD/MM/YYYY and MM/DD/YYYY)

STEP 3: REMOVE BLANK ROWS
[Screenshot 3: Remove Rows Menu]
- Home > Remove Rows > Remove Blank Rows
- Result: 235 rows remaining
- Rationale: Blank rows contribute nothing to analysis and may cause calculation errors

STEP 4: CHANGE DATA TYPES (Critical!)
[Screenshot 4: Data Type Icons]
Original Types (Auto-detected incorrectly):
- TransactionID: Text (Wrong) → Changed to Text (Actually correct for ID)
- CustomerID: Whole Number ✓
- PurchaseDate: Text (Wrong) → Changed to Date
- ProductName: Text ✓
- Quantity: Text (Wrong) → Changed to Whole Number
- UnitPrice: Text (Wrong) → Changed to Decimal Number
- CustomerEmail: Text ✓
- PaymentMethod: Text ✓

Rationale: 
- Date type enables time intelligence functions
- Numeric types enable aggregations (SUM, AVERAGE)
- Wrong types = wrong calculations!

STEP 5: TRANSFORMATION 1 - SPLIT COLUMN (CustomerEmail)
[Screenshot 5: Split by Delimiter]
- Selected: CustomerEmail column
- Transform > Split Column > By Delimiter
- Delimiter: @ (at sign)
- Split at: Left-most delimiter
- Result: Two columns
  * CustomerEmail.1 (username)
  * CustomerEmail.2 (domain)
- Renamed: "Username" and "EmailDomain"

Rationale: 
- Analyze which email providers are most common (gmail, yahoo, corporate)
- Segment customers by email domain
- Useful for email marketing campaigns

STEP 6: TRANSFORMATION 2 - MERGE COLUMNS (Create ProductCategory)
[Screenshot 6: Merge Columns]
- Added Custom Column: "ProductCategory"
- Formula: 
  ```
  if Text.Contains([ProductName], "Laptop") then "Electronics"
  else if Text.Contains([ProductName], "Phone") then "Electronics"
  else if Text.Contains([ProductName], "Shirt") then "Clothing"
  else if Text.Contains([ProductName], "Shoes") then "Clothing"
  else "Other"
  ```
- Result: New categorical column for grouping

Rationale:
- Original data lacked product categorization
- Enables category-level analysis
- Facilitates drill-down in reports

STEP 7: TRANSFORMATION 3 - REPLACE VALUES (Standardization)
[Screenshot 7: Replace Values Dialog]
Issues found in PaymentMethod column:
- "Credit Card", "CreditCard", "CC" (3 variations)
- "Cash", "CASH" (case inconsistency)

Actions:
- Replace "CreditCard" with "Credit Card"
- Replace "CC" with "Credit Card"
- Replace "CASH" with "Cash"

Rationale:
- Data consistency is crucial for accurate grouping
- Prevents duplicate categories in visualizations
- Standardizes terminology across dataset

STEP 8: TRANSFORMATION 4 - FILTER ROWS (Focus on Relevant Data)
[Screenshot 8: Date Filter]
- Filtered PurchaseDate: Keep only Q4 2024 (Oct-Dec)
- Filter criteria: Date is after or equal to 2024-10-01

Secondary Filter:
- Filtered Quantity: Remove negative values (returns/errors)
- Keep only: Quantity > 0

Rationale:
- Q4 is holiday season - most relevant for current analysis
- Negative quantities indicate data errors or returns (separate analysis needed)
- Focuses dataset on actual purchases

STEP 9: ADD CALCULATED COLUMN (TotalRevenue)
[Screenshot 9: Add Custom Column]
- Column Name: TotalRevenue
- Formula: [Quantity] * [UnitPrice]
- Data Type: Decimal Number

Rationale:
- Critical metric not present in source data
- Enables revenue analysis without DAX calculations
- Computed once during data load (efficient)

FINAL APPLIED STEPS (12 steps total):
1. Source
2. Navigation
3. Promoted Headers
4. Changed Type (initial)
5. Removed Blank Rows
6. Changed Type (corrections)
7. Split Column by Delimiter (Email)
8. Renamed Columns
9. Added Custom (ProductCategory)
10. Replaced Value (Credit Card variations)
11. Replaced Value (Cash variations)
12. Filtered Rows (Date and Quantity)
13. Added Custom (TotalRevenue)

BEFORE vs AFTER COMPARISON:
[Screenshot 10: Before/After Side by Side]

Before:
- 250 rows (including 15 blanks)
- Multiple data type errors
- Inconsistent values
- No product categorization
- No revenue calculation

After:
- 187 rows (Q4 2024, valid transactions only)
- All correct data types
- Standardized values
- Product categories added
- TotalRevenue calculated
- Email domains extracted

QUERY FOLDING OPTIMIZATION:
[Screenshot 11: View Native Query]
- Checked "View Native Query" for applicable steps
- Steps 1-6 fold back to source (good performance)
- Steps 7-13 performed in Power Query Engine

KEY LEARNINGS:
1. Power Query is ETL (Extract, Transform, Load) tool
2. All transformations are repeatable and documented
3. Applied Steps = audit trail of data preparation
4. Query Folding = performance optimization
5. Always validate data types first
6. Think about end-user needs when transforming

WHY THESE TRANSFORMATIONS?
- Business users need clean, consistent data
- Analysts need calculated metrics ready to use
- Reports require proper data types for visuals
- Performance depends on efficient transformations

This exercise demonstrated real-world data preparation workflow that I will use in my future career as a data analyst.', 10.00, '2024-09-20 18:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(12, 1, 'Outstanding work! Bài làm của em ở level chuyên nghiệp với documentation chi tiết, screenshots đầy đủ và giải thích sâu sắc về từng transformation. Em đã hiểu bản chất của ETL process. Excellent! 10/10!', '2024-09-21 10:00:00'),
(12, 4, 'Thank you so much thầy! Em rất đam mê data analytics nên em dành nhiều thời gian research và practice.', '2024-09-21 10:30:00'),
(12, 1, 'Em có potential rất lớn trong lĩnh vực này. Hãy tiếp tục phát huy nhé!', '2024-09-21 11:00:00');

-- Sinh viên 4: Hoàng Văn E - 8 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 5, 'da_cham', 8.00, '2024-09-20 11:00:00', '2024-09-23 20:00:00', '2024-09-24 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(13, 2, 'Em kết nối file Excel về doanh số bán hàng vào Power BI.

Các bước xử lý:
1. Remove blank rows: Xóa được 10 dòng trống
2. Change type: Đổi cột Date thành Date type, Amount thành Decimal
3. Split column ProductName: Tách thành Product và Model
4. Replace values: Đổi "VN" thành "Vietnam" trong cột Country
5. Filter: Chỉ lấy data năm 2024

Lý do thực hiện:
- Remove blanks: Loại bỏ dữ liệu rác
- Change type: Để tính toán đúng
- Split: Phân tích theo product riêng và model riêng
- Replace: Thống nhất tên quốc gia
- Filter: Chỉ cần data gần đây

Kết quả: Data đã clean và ready để tạo visualizations.', 8.00, '2024-09-23 20:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(13, 1, 'Bài làm tốt! Em đã thực hiện các transformations cơ bản đúng. Lần sau hãy thêm screenshots và giải thích chi tiết hơn về cách thực hiện. Điểm 8!', '2024-09-24 09:00:00');

-- Sinh viên 5: Vũ Thị F - 7.5 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 6, 'da_cham', 7.50, '2024-09-19 15:00:00', '2024-09-24 18:00:00', '2024-09-25 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(14, 2, 'Em connect file CSV vào Power BI và xử lý:

1. Remove rows: Xóa dòng trống
2. Change type: Đổi kiểu dữ liệu cho cột số và ngày tháng
3. Split column: Tách cột Name thành First và Last name
4. Replace: Đổi "M" thành "Male", "F" thành "Female"

Làm để data sạch hơn và dễ phân tích. Có thể tạo báo cáo sau khi xử lý xong.', 7.50, '2024-09-24 18:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(14, 1, 'Bài làm đạt yêu cầu cơ bản. Em cần giải thích rõ hơn lý do tại sao thực hiện mỗi transformation và nên có hình ảnh minh họa. Điểm 7.5!', '2024-09-25 09:00:00');

-- Sinh viên 6: Đặng Văn G - 6.5 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 7, 'da_cham', 6.50, '2024-09-20 17:00:00', '2024-09-25 22:00:00', '2024-09-26 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(15, 2, 'Em import Excel vào Power BI.

Xử lý:
1. Xóa dòng trống
2. Đổi type cột
3. Split column
4. Replace values

Để data clean và dễ dùng.', 6.50, '2024-09-25 22:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(15, 1, 'Bài làm quá ngắn gọn. Em cần mô tả chi tiết từng bước, giải thích lý do và có hình ảnh minh họa. Hãy cố gắng hơn! Điểm 6.5.', '2024-09-26 09:00:00'),
(15, 7, 'Vâng ạ, em sẽ làm kỹ hơn ở bài sau.', '2024-09-26 10:00:00');

-- Sinh viên 7: Bùi Thị H - 8.5 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 8, 'da_cham', 8.50, '2024-09-21 11:00:00', '2024-09-24 15:00:00', '2024-09-25 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(16, 2, 'Em thực hành với file Student_Scores.xlsx:

Kết nối: Get Data > Excel > Select file > Transform Data

Power Query transformations:

1. Removed Blank Rows: 
Có 8 dòng trống, đã xóa thành công.
Lý do: Dòng trống làm sai lệch thống kê.

2. Changed Data Types:
- StudentID: Text
- Score: Whole Number (ban đầu là Text)
- TestDate: Date (ban đầu là Text)
Lý do: Để tính toán điểm trung bình và sort theo ngày.

3. Split Column (StudentName):
Tách thành FirstName và LastName bằng space delimiter.
Lý do: Dễ dàng sort và tìm kiếm theo họ hoặc tên.

4. Added Custom Column (Grade):
Formula: if [Score] >= 80 then "A" else if [Score] >= 65 then "B" else "C"
Lý do: Phân loại học lực để phân tích.

5. Replace Values:
Thay "Math" thành "Mathematics" trong Subject column để consistent.

Kết quả: Data đã sạch, có thể tạo reports để phân tích điểm số sinh viên.', 8.50, '2024-09-24 15:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(16, 1, 'Bài làm tốt! Em đã thực hiện đầy đủ các bước và giải thích khá rõ ràng. Custom column Grade là một ý tưởng hay. Điểm 8.5!', '2024-09-25 10:00:00');

-- Sinh viên 8: Đỗ Văn I - 7 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 9, 'da_cham', 7.00, '2024-09-21 14:00:00', '2024-09-26 19:00:00', '2024-09-27 09:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(17, 2, 'Em kết nối Excel file và xử lý data:

1. Remove blank rows
2. Change type cột Number và Date
3. Split Name column
4. Replace inconsistent values

Kết quả là data đã clean để analyze.', 7.00, '2024-09-26 19:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(17, 1, 'Bài làm còn sơ sài. Em cần mô tả chi tiết các bước thực hiện và giải thích lý do. Hãy tham khảo bài làm của các bạn khác. Điểm 7!', '2024-09-27 09:00:00');

-- Sinh viên 9: Ngô Thị K - 8 điểm
INSERT INTO bai_lam (bai_tap_id, sinh_vien_id, trang_thai, diem, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_cham, nguoi_cham_id) VALUES
(2, 10, 'da_cham', 8.00, '2024-09-20 19:00:00', '2024-09-25 16:00:00', '2024-09-26 10:00:00', 1);

INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(18, 2, 'Em thực hành với file Inventory_Data.xlsx:

Bước 1: Get Data từ Excel và Transform Data

Bước 2: Xử lý trong Power Query
- Remove blank rows: Xóa 12 dòng trống
- Change types: 
  * ProductID: Text
  * Quantity: Whole Number
  * Price: Decimal
  * LastUpdate: Date
  
- Split SKU column: Tách thành CategoryCode và ProductCode
  Lý do: Để phân tích theo category

- Replace values: "In Stock" và "InStock" thành "Available"
  Lý do: Standardize status

- Add custom column "StockValue": [Quantity] * [Price]
  Lý do: Tính giá trị tồn kho

Kết quả: Data ready for inventory analysis dashboard.', 8.00, '2024-09-25 16:00:00');

INSERT INTO binh_luan_bai_tap (bai_lam_id, nguoi_gui_id, noi_dung, thoi_gian_gui) VALUES
(18, 1, 'Bài làm khá tốt! Em đã thực hiện các transformations cần thiết và giải thích lý do. Custom column StockValue rất hợp lý. Điểm 8!', '2024-09-26 10:00:00');

-- =====================================================
-- 13. BÀI LÀM BÀI KIỂM TRA CHƯƠNG 1 (TẤT CẢ 9 SINH VIÊN)
-- =====================================================

-- Sinh viên 1: Trần Thị B - 10/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 2, 'da_cham', 10.00, 10, 10, '2024-10-05 14:00:00', '2024-10-05 14:25:00', 25);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(1, 1, 2, TRUE, '2024-10-05 14:02:00'),
(1, 2, 3, TRUE, '2024-10-05 14:04:00'),
(1, 3, 2, TRUE, '2024-10-05 14:06:00'),
(1, 4, 3, TRUE, '2024-10-05 14:08:00'),
(1, 5, 1, TRUE, '2024-10-05 14:11:00'),
(1, 6, 4, TRUE, '2024-10-05 14:14:00'),
(1, 7, 2, TRUE, '2024-10-05 14:17:00'),
(1, 8, 1, TRUE, '2024-10-05 14:20:00'),
(1, 9, 2, TRUE, '2024-10-05 14:23:00'),
(1, 10, 2, TRUE, '2024-10-05 14:25:00');

-- Sinh viên 2: Lê Văn C - 9/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 3, 'da_cham', 9.00, 9, 10, '2024-10-05 14:00:00', '2024-10-05 14:28:00', 28);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(2, 1, 2, TRUE, '2024-10-05 14:02:30'),
(2, 2, 3, TRUE, '2024-10-05 14:05:00'),
(2, 3, 2, TRUE, '2024-10-05 14:07:00'),
(2, 4, 3, TRUE, '2024-10-05 14:09:00'),
(2, 5, 1, TRUE, '2024-10-05 14:12:00'),
(2, 6, 4, TRUE, '2024-10-05 14:15:00'),
(2, 7, 1, FALSE, '2024-10-05 14:18:00'),
(2, 8, 1, TRUE, '2024-10-05 14:21:00'),
(2, 9, 2, TRUE, '2024-10-05 14:24:00'),
(2, 10, 2, TRUE, '2024-10-05 14:28:00');

-- Sinh viên 3: Phạm Thị D - 10/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 4, 'da_cham', 10.00, 10, 10, '2024-10-05 14:00:00', '2024-10-05 14:22:00', 22);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(3, 1, 2, TRUE, '2024-10-05 14:01:30'),
(3, 2, 3, TRUE, '2024-10-05 14:03:00'),
(3, 3, 2, TRUE, '2024-10-05 14:05:00'),
(3, 4, 3, TRUE, '2024-10-05 14:07:00'),
(3, 5, 1, TRUE, '2024-10-05 14:10:00'),
(3, 6, 4, TRUE, '2024-10-05 14:12:00'),
(3, 7, 2, TRUE, '2024-10-05 14:15:00'),
(3, 8, 1, TRUE, '2024-10-05 14:17:00'),
(3, 9, 2, TRUE, '2024-10-05 14:19:00'),
(3, 10, 2, TRUE, '2024-10-05 14:22:00');

-- Sinh viên 4: Hoàng Văn E - 8/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 5, 'da_cham', 8.00, 8, 10, '2024-10-05 14:00:00', '2024-10-05 14:29:00', 29);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(4, 1, 2, TRUE, '2024-10-05 14:03:00'),
(4, 2, 3, TRUE, '2024-10-05 14:05:30'),
(4, 3, 2, TRUE, '2024-10-05 14:08:00'),
(4, 4, 2, FALSE, '2024-10-05 14:10:00'),
(4, 5, 1, TRUE, '2024-10-05 14:13:00'),
(4, 6, 4, TRUE, '2024-10-05 14:16:00'),
(4, 7, 3, FALSE, '2024-10-05 14:19:00'),
(4, 8, 1, TRUE, '2024-10-05 14:22:00'),
(4, 9, 2, TRUE, '2024-10-05 14:25:00'),
(4, 10, 2, TRUE, '2024-10-05 14:29:00');

-- Sinh viên 5: Vũ Thị F - 7/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 6, 'da_cham', 7.00, 7, 10, '2024-10-05 14:00:00', '2024-10-05 14:30:00', 30);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(5, 1, 2, TRUE, '2024-10-05 14:03:30'),
(5, 2, 3, TRUE, '2024-10-05 14:06:00'),
(5, 3, 1, FALSE, '2024-10-05 14:09:00'),
(5, 4, 3, TRUE, '2024-10-05 14:11:00'),
(5, 5, 2, FALSE, '2024-10-05 14:14:00'),
(5, 6, 4, TRUE, '2024-10-05 14:17:00'),
(5, 7, 2, TRUE, '2024-10-05 14:20:00'),
(5, 8, 1, TRUE, '2024-10-05 14:23:00'),
(5, 9, 3, FALSE, '2024-10-05 14:26:00'),
(5, 10, 2, TRUE, '2024-10-05 14:30:00');

-- Sinh viên 6: Đặng Văn G - 6/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 7, 'da_cham', 6.00, 6, 10, '2024-10-05 14:00:00', '2024-10-05 14:30:00', 30);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(6, 1, 2, TRUE, '2024-10-05 14:04:00'),
(6, 2, 2, FALSE, '2024-10-05 14:07:00'),
(6, 3, 2, TRUE, '2024-10-05 14:10:00'),
(6, 4, 3, TRUE, '2024-10-05 14:12:00'),
(6, 5, 1, TRUE, '2024-10-05 14:15:00'),
(6, 6, 3, FALSE, '2024-10-05 14:18:00'),
(6, 7, 2, TRUE, '2024-10-05 14:21:00'),
(6, 8, 2, FALSE, '2024-10-05 14:24:00'),
(6, 9, 1, FALSE, '2024-10-05 14:27:00'),
(6, 10, 2, TRUE, '2024-10-05 14:30:00');

-- Sinh viên 7: Bùi Thị H - 8/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 8, 'da_cham', 8.00, 8, 10, '2024-10-05 14:00:00', '2024-10-05 14:27:00', 27);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(7, 1, 2, TRUE, '2024-10-05 14:02:00'),
(7, 2, 3, TRUE, '2024-10-05 14:05:00'),
(7, 3, 2, TRUE, '2024-10-05 14:07:00'),
(7, 4, 3, TRUE, '2024-10-05 14:10:00'),
(7, 5, 1, TRUE, '2024-10-05 14:13:00'),
(7, 6, 2, FALSE, '2024-10-05 14:16:00'),
(7, 7, 2, TRUE, '2024-10-05 14:19:00'),
(7, 8, 3, FALSE, '2024-10-05 14:22:00'),
(7, 9, 2, TRUE, '2024-10-05 14:24:00'),
(7, 10, 2, TRUE, '2024-10-05 14:27:00');

-- Sinh viên 8: Đỗ Văn I - 7/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 9, 'da_cham', 7.00, 7, 10, '2024-10-05 14:00:00', '2024-10-05 14:30:00', 30);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(8, 1, 2, TRUE, '2024-10-05 14:03:00'),
(8, 2, 3, TRUE, '2024-10-05 14:06:00'),
(8, 3, 3, FALSE, '2024-10-05 14:09:00'),
(8, 4, 3, TRUE, '2024-10-05 14:12:00'),
(8, 5, 1, TRUE, '2024-10-05 14:15:00'),
(8, 6, 4, TRUE, '2024-10-05 14:18:00'),
(8, 7, 1, FALSE, '2024-10-05 14:21:00'),
(8, 8, 1, TRUE, '2024-10-05 14:24:00'),
(8, 9, 3, FALSE, '2024-10-05 14:27:00'),
(8, 10, 2, TRUE, '2024-10-05 14:30:00');

-- Sinh viên 9: Ngô Thị K - 9/10 điểm
INSERT INTO bai_lam_kiem_tra (bai_kiem_tra_id, sinh_vien_id, trang_thai, diem, so_cau_dung, tong_so_cau, thoi_gian_bat_dau, thoi_gian_nop, thoi_gian_lam_bai) VALUES
(1, 10, 'da_cham', 9.00, 9, 10, '2024-10-05 14:00:00', '2024-10-05 14:26:00', 26);

INSERT INTO chi_tiet_tra_loi (bai_lam_kiem_tra_id, cau_hoi_id, lua_chon_id, dung_hay_sai, thoi_gian_tra_loi) VALUES
(9, 1, 2, TRUE, '2024-10-05 14:02:00'),
(9, 2, 3, TRUE, '2024-10-05 14:04:00'),
(9, 3, 2, TRUE, '2024-10-05 14:07:00'),
(9, 4, 3, TRUE, '2024-10-05 14:09:00'),
(9, 5, 1, TRUE, '2024-10-05 14:12:00'),
(9, 6, 4, TRUE, '2024-10-05 14:15:00'),
(9, 7, 2, TRUE, '2024-10-05 14:18:00'),
(9, 8, 2, FALSE, '2024-10-05 14:21:00'),
(9, 9, 2, TRUE, '2024-10-05 14:23:00'),
(9, 10, 2, TRUE, '2024-10-05 14:26:00');

-- =====================================================
-- KẾT THÚC FILE DỮ LIỆU MẪU POWER BI
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- HƯỚNG DẪN SỬ DỤNG
-- =====================================================
-- 1. Chạy file database_final.sql trước để tạo cấu trúc database
-- 2. Chạy file rang-buoc-database.sql để thêm các ràng buộc và trigger
-- 3. Chạy file này để import dữ liệu mẫu Power BI
-- 
-- Lưu ý: 
-- - File này chứa dữ liệu demo đầy đủ cho Chương 1
-- - Tất cả 9 sinh viên đã hoàn thành video, bài tập và bài kiểm tra Chương 1
-- - Có nhiều bình luận tương tác giữa sinh viên và giảng viên
-- - Điểm số đa dạng phản ánh năng lực thực tế của từng sinh viên
-- =====================================================
