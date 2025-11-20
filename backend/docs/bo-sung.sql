-- ================================================
-- CẬP NHẬT DỮ LIỆU MẪU CHO BẢNG CHƯƠNG
-- ================================================
-- Mô tả: Cập nhật trường 'noi_dung' (Nội dung chương học - mô tả chi tiết) 
-- và 'muc_tieu' (Mục tiêu chương học - danh sách bullet points chi tiết)
-- cho các chương hiện có trong dữ liệu mẫu (L001, L002: Power BI; L003: SQL Server).
-- Nội dung được mở rộng chi tiết dựa trên chủ đề môn học, tương tự ví dụ cung cấp.
-- Sử dụng định dạng TEXT: 
-- - noi_dung: Đoạn văn mô tả liền mạch.
-- - muc_tieu: Danh sách bullet points với ký tự '*' và xuống dòng (\n).
-- Giả sử các chương đã tồn tại từ file sample.sql, sử dụng UPDATE với điều kiện lop_hoc_id và so_thu_tu_chuong.
-- ================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ================================================
-- CẬP NHẬT CHƯƠNG CHO LỚP L001 VÀ L002 (MÔN HỌC: THỰC HÀNH LABS POWER BI)
-- Các lớp L001 và L002 có chương giống nhau về nội dung, nên cập nhật chung.
-- ================================================

-- Chương 1: Giới thiệu Power BI (so_thu_tu_chuong = 1)
UPDATE chuong 
SET 
    noi_dung = 'Chương này giới thiệu tổng quan về Power BI như một công cụ phân tích và trực quan hóa dữ liệu mạnh mẽ, nhấn mạnh vai trò của nó trong việc hỗ trợ ra quyết định kinh doanh dựa trên dữ liệu. Nội dung bao gồm làm quen với giao diện Power BI Desktop, các thành phần chính như Report View, Data View và Model View, cùng với các bước kết nối dữ liệu từ các nguồn phổ biến như Excel, SQL Server hoặc web services. Chương cũng tập trung vào khái niệm cơ bản về dữ liệu (data types, relationships), kỹ năng import dữ liệu ban đầu, và các tính năng bảo mật cơ bản. Cuối cùng, các ví dụ thực tế về việc tạo báo cáo đơn giản được thảo luận để giúp sinh viên áp dụng ngay vào dự án cá nhân, nhằm xây dựng nền tảng vững chắc cho việc phân tích dữ liệu chuyên sâu hơn.',
    muc_tieu = '* Hiểu rõ vai trò và lợi ích của Power BI trong phân tích dữ liệu kinh doanh và trực quan hóa thông tin.\n* Làm quen với giao diện người dùng của Power BI Desktop, bao gồm các view chính (Report, Data, Model).\n* Nắm vững quy trình kết nối và import dữ liệu từ các nguồn phổ biến như file Excel, cơ sở dữ liệu và web.\n* Xây dựng kỹ năng cơ bản về quản lý dữ liệu, bao gồm nhận diện data types và thiết lập relationships ban đầu.\n* Thực hành tạo báo cáo đơn giản đầu tiên, tập trung vào việc áp dụng dữ liệu thực tế để trình bày ý tưởng rõ ràng.\n* Nâng cao nhận thức về bảo mật dữ liệu và các best practices khi làm việc với nguồn dữ liệu bên ngoài.'
WHERE (lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') OR lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')) 
  AND so_thu_tu_chuong = 1;

-- Chương 2: Mô hình dữ liệu & Transform (so_thu_tu_chuong = 2)
UPDATE chuong 
SET 
    noi_dung = 'Chương tập trung vào việc xây dựng mô hình dữ liệu vững chắc trong Power BI, bao gồm sử dụng Power Query để làm sạch và biến đổi dữ liệu thô thành dạng sẵn sàng phân tích. Nội dung chi tiết về các kỹ thuật transform như filtering rows/columns, merging và appending tables, handling missing values, cùng với việc tạo calculated columns sử dụng M language. Chương cũng khám phá khái niệm star schema và snowflake schema để tối ưu hóa relationships giữa các bảng, giúp cải thiện hiệu suất query. Các ví dụ thực tế từ dữ liệu bán hàng hoặc HR được sử dụng để minh họa, nhấn mạnh cách transform dữ liệu giúp phát hiện insights ẩn và chuẩn bị cho các tính toán nâng cao hơn.',
    muc_tieu = '* Hiểu và áp dụng Power Query Editor để biến đổi dữ liệu thô, bao gồm cleaning, filtering và reshaping.\n* Xây dựng mô hình dữ liệu hiệu quả sử dụng relationships, star schema và các best practices về data modeling.\n* Nắm vững các hàm M language cơ bản để tạo custom columns và xử lý dữ liệu phức tạp.\n* Thực hành merging và appending tables từ nhiều nguồn để tạo dataset thống nhất.\n* Phát triển kỹ năng xử lý dữ liệu không hoàn chỉnh (missing values, duplicates) nhằm đảm bảo tính chính xác của phân tích.\n* Áp dụng kiến thức vào case study thực tế, như transform dữ liệu bán hàng để chuẩn bị cho dashboard báo cáo.'
WHERE (lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') OR lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')) 
  AND so_thu_tu_chuong = 2;

-- Chương 3: DAX cơ bản (so_thu_tu_chuong = 3)
UPDATE chuong 
SET 
    noi_dung = 'Chương giới thiệu ngôn ngữ DAX (Data Analysis Expressions) như công cụ cốt lõi để tạo các phép tính động trong Power BI, nhấn mạnh sự khác biệt giữa calculated columns và measures. Nội dung bao gồm các hàm cơ bản như SUM, AVERAGE, COUNT, cùng với time intelligence functions (DATEADD, TOTALYTD) để phân tích xu hướng theo thời gian. Chương cũng thảo luận về context (row context vs. filter context) và cách sử dụng CALCULATE để modify filters, giúp sinh viên xử lý các tình huống phức tạp như ranking hoặc percentage of total. Các bài tập thực hành tập trung vào việc xây dựng measures cho báo cáo KPI, nhằm trang bị kỹ năng tạo insights động từ dữ liệu tĩnh.',
    muc_tieu = '* Nắm vững cú pháp DAX cơ bản và sự khác biệt giữa calculated columns, measures và tables.\n* Áp dụng các hàm tổng hợp (SUM, AVERAGE, COUNT) và logic functions (IF, SWITCH) trong các tình huống phân tích thực tế.\n* Hiểu khái niệm context trong DAX (row và filter) và sử dụng CALCULATE để điều chỉnh filters động.\n* Xây dựng time intelligence measures để phân tích dữ liệu theo thời gian, như YTD sales hoặc month-over-month growth.\n* Thực hành tạo custom KPIs và rankings sử dụng DAX, tập trung vào việc tối ưu hóa performance.\n* Phát triển khả năng debug và troubleshoot DAX errors để đảm bảo độ chính xác của các phép tính.'
WHERE (lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') OR lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')) 
  AND so_thu_tu_chuong = 3;

-- Chương 4: Trực quan hoá & Dashboard (so_thu_tu_chuong = 4)
UPDATE chuong 
SET 
    noi_dung = 'Chương khám phá các kỹ thuật trực quan hóa dữ liệu tiên tiến trong Power BI, từ việc chọn visual phù hợp (bar charts, line graphs, maps) đến thiết kế dashboard tương tác sử dụng slicers, drill-through và bookmarks. Nội dung nhấn mạnh nguyên tắc thiết kế UX/UI cho báo cáo, như sử dụng color theory, layout và storytelling với dữ liệu để truyền tải thông điệp rõ ràng. Chương cũng bao gồm chia sẻ báo cáo qua Power BI Service, embedding và row-level security để bảo vệ dữ liệu nhạy cảm. Các case study từ doanh nghiệp thực tế được sử dụng để minh họa cách dashboard hỗ trợ decision-making, giúp sinh viên xây dựng portfolio chuyên nghiệp.',
    muc_tieu = '* Chọn và tùy chỉnh visuals phù hợp để đại diện dữ liệu một cách hiệu quả và hấp dẫn.\n* Thiết kế dashboard tương tác sử dụng slicers, drill-down và conditional formatting để tăng tính khám phá dữ liệu.\n* Áp dụng nguyên tắc UX/UI trong báo cáo, bao gồm color schemes, themes và storytelling để truyền tải insights.\n* Nâng cao kỹ năng chia sẻ và publish báo cáo qua Power BI Service, bao gồm workspaces và apps.\n* Triển khai row-level security và embedding để bảo mật và tích hợp báo cáo vào ứng dụng web.\n* Thực hành xây dựng end-to-end dashboard từ dữ liệu thực tế, tập trung vào việc trình bày insights kinh doanh rõ ràng và chuyên nghiệp.'
WHERE (lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L001') OR lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L002')) 
  AND so_thu_tu_chuong = 4;

-- ================================================
-- CẬP NHẬT CHƯƠNG CHO LỚP L003 (MÔN HỌC: LẬP TRÌNH SQL SERVER)
-- ================================================

-- Chương 1: Giới thiệu SQL Server (so_thu_tu_chuong = 1)
UPDATE chuong 
SET 
    noi_dung = 'Chương cung cấp cái nhìn tổng quan về SQL Server như một hệ quản trị cơ sở dữ liệu quan hệ (RDBMS) của Microsoft, nhấn mạnh vai trò của nó trong lưu trữ, quản lý và truy vấn dữ liệu lớn. Nội dung bao gồm hướng dẫn cài đặt SQL Server Express và SQL Server Management Studio (SSMS), làm quen với các thành phần chính như databases, tables và schemas. Chương cũng giới thiệu khái niệm ACID properties và transaction management cơ bản, cùng với các công cụ query đơn giản như SELECT * FROM. Các ví dụ thực tế từ database mẫu AdventureWorks được sử dụng để minh họa, giúp sinh viên thiết lập môi trường phát triển và hiểu rõ nền tảng cho các hoạt động CRUD (Create, Read, Update, Delete).',
    muc_tieu = '* Hiểu vai trò và lợi ích của SQL Server trong quản lý dữ liệu doanh nghiệp và phát triển ứng dụng.\n* Thành thạo quy trình cài đặt và cấu hình SQL Server Express cùng SSMS.\n* Làm quen với giao diện SSMS, bao gồm Object Explorer, Query Editor và các tính năng debug.\n* Nắm vững khái niệm cơ bản về databases, tables và schemas trong môi trường RDBMS.\n* Thực hành các câu lệnh SQL đơn giản để kết nối và truy vấn dữ liệu ban đầu.\n* Nhận thức về các nguyên tắc bảo mật cơ bản và best practices khi thiết lập môi trường phát triển.'
WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') 
  AND so_thu_tu_chuong = 1;

-- Chương 2: Bảng và dữ liệu (so_thu_tu_chuong = 2)
UPDATE chuong 
SET 
    noi_dung = 'Chương tập trung vào việc thiết kế và thao tác dữ liệu cơ bản trong SQL Server, bao gồm tạo database và tables với các kiểu dữ liệu phù hợp (INT, VARCHAR, DATE, etc.). Nội dung chi tiết về các ràng buộc (PRIMARY KEY, FOREIGN KEY, UNIQUE, CHECK) để đảm bảo tính toàn vẹn dữ liệu, cùng với các lệnh DML (INSERT, UPDATE, DELETE) và sử dụng WHERE clause để lọc dữ liệu. Chương cũng thảo luận về indexing cơ bản để cải thiện performance và transaction control (BEGIN TRANSACTION, COMMIT, ROLLBACK). Các bài tập thực hành sử dụng database quanly_sinhvien để minh họa, giúp sinh viên xây dựng schema đơn giản và thao tác dữ liệu an toàn.',
    muc_tieu = '* Thiết kế và tạo tables với các kiểu dữ liệu phù hợp, bao gồm xử lý null values và default constraints.\n* Áp dụng các ràng buộc dữ liệu (PRIMARY KEY, FOREIGN KEY, UNIQUE) để duy trì tính toàn vẹn.\n* Thành thạo các lệnh DML (INSERT, UPDATE, DELETE) kết hợp với WHERE và ORDER BY.\n* Hiểu và sử dụng transaction management để đảm bảo tính nhất quán dữ liệu.\n* Thực hành tạo indexes cơ bản để tối ưu hóa truy vấn trên tables lớn.\n* Xây dựng schema database đơn giản cho case study thực tế, như hệ thống quản lý sinh viên.'
WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') 
  AND so_thu_tu_chuong = 2;

-- Chương 3: Truy vấn SELECT nâng cao (so_thu_tu_chuong = 3)
UPDATE chuong 
SET 
    noi_dung = 'Chương nâng cao kỹ năng truy vấn với SELECT statements phức tạp, bao gồm filtering với WHERE (LIKE, BETWEEN, IN, EXISTS), sorting với ORDER BY, và grouping với GROUP BY/HAVING để tính toán aggregate (SUM, AVG, COUNT, MAX/MIN). Nội dung cũng bao gồm subqueries và common table expressions (CTE) để giải quyết các vấn đề nested queries. Chương nhấn mạnh cách sử dụng window functions cơ bản (ROW_NUMBER, RANK) cho analytics, cùng với pivot/unpivot để reshape dữ liệu. Các ví dụ từ dữ liệu bán hàng được sử dụng để minh họa, giúp sinh viên viết queries hiệu quả cho báo cáo kinh doanh.',
    muc_tieu = '* Viết các truy vấn SELECT phức tạp sử dụng WHERE clauses nâng cao (LIKE, BETWEEN, IN, EXISTS).\n* Áp dụng GROUP BY và HAVING để thực hiện aggregate functions và lọc nhóm dữ liệu.\n* Sử dụng subqueries và CTEs để xử lý logic nested và cải thiện readability của query.\n* Giới thiệu window functions (ROW_NUMBER, RANK, LAG/LEAD) cho phân tích theo thứ tự.\n* Thực hành pivot/unpivot để chuyển đổi dữ liệu từ rows sang columns và ngược lại.\n* Tối ưu hóa queries cơ bản bằng EXPLAIN và hiểu impact của indexes trên performance.'
WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') 
  AND so_thu_tu_chuong = 3;

-- Chương 4: JOIN & Quan hệ bảng (so_thu_tu_chuong = 4)
UPDATE chuong 
SET 
    noi_dung = 'Chương khám phá các loại JOIN (INNER, LEFT/RIGHT OUTER, FULL OUTER, CROSS) để kết hợp dữ liệu từ nhiều tables, nhấn mạnh thiết kế quan hệ với primary/foreign keys và normalization (1NF, 2NF, 3NF). Nội dung bao gồm self-joins cho hierarchical data và multi-table joins cho báo cáo phức tạp, cùng với sử dụng aliases và qualified names để tránh ambiguity. Chương cũng thảo luận về views và stored procedures để encapsulate logic joins, giúp tái sử dụng. Các case study từ database e-commerce được sử dụng để minh họa, trang bị cho sinh viên kỹ năng xây dựng queries cho hệ thống lớn.',
    muc_tieu = '* Hiểu và áp dụng các loại JOIN (INNER, OUTER, CROSS) để kết hợp dữ liệu từ multiple tables.\n* Thiết kế quan hệ bảng với primary/foreign keys và áp dụng nguyên tắc normalization để giảm redundancy.\n* Viết multi-table joins phức tạp sử dụng aliases và xử lý NULL values trong OUTER JOINs.\n* Sử dụng self-joins cho dữ liệu hierarchical như employee-manager relationships.\n* Tạo views và stored procedures để encapsulate JOIN logic và cải thiện maintainability.\n* Thực hành xây dựng end-to-end queries cho báo cáo kinh doanh, tập trung vào performance và accuracy.'
WHERE lop_hoc_id = (SELECT id FROM lop_hoc WHERE ma_lop_hoc = 'L003') 
  AND so_thu_tu_chuong = 4;

-- ================================================
-- KẾT THÚC CẬP NHẬT
-- ================================================
-- Sau khi chạy, kiểm tra bằng: SELECT * FROM chuong WHERE lop_hoc_id IN (SELECT id FROM lop_hoc WHERE ma_lop_hoc IN ('L001', 'L002', 'L003'));
-- Lưu ý: Chỉ cập nhật chương nào đã tồn tại trong sample data. Nếu cần thêm chương mới, sử dụng INSERT.

-- Thêm cột cho_phep_lam_lai vào bảng bai_kiem_tra
-- Mặc định = 0 (không cho phép làm lại)
-- Giảng viên set = 1 để cho phép sinh viên làm lại

ALTER TABLE bai_kiem_tra 
ADD COLUMN cho_phep_lam_lai TINYINT(1) DEFAULT 0 
COMMENT 'Cho phép sinh viên làm lại bài kiểm tra (0=không, 1=có)';



COMMIT;
SET FOREIGN_KEY_CHECKS = 1;