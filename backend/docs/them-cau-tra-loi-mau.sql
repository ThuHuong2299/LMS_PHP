-- =====================================================
-- TẠO DỮ LIỆU MẪU CÂU TRẢ LỜI CHO CÁC BÀI LÀM ĐÃ CÓ ĐIỂM
-- =====================================================
-- Ngày tạo: 20/11/2025
-- Mục đích: Tạo câu trả lời cho sinh viên đã nộp bài và có điểm
-- =====================================================

USE lms_hoc_tap;

-- Xóa dữ liệu cũ nếu có
DELETE FROM tra_loi_bai_tap;

-- =====================================================
-- BÀI LÀM 1: SV001 - Bài tập 1 (Điểm: 8.5/5.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(1, 1, 'File có tên "Dữ liệu khách hàng Q3.xlsx" và có 3 sheet: Sheet1 (dữ liệu chính), Sheet2 (tham khảo), Sheet3 (backup)', 1.00, '2025-10-20 19:45:00'),
(1, 2, 'Tổng số dòng dữ liệu là 1523 dòng (bao gồm 1 dòng header và 1522 dòng dữ liệu)', 1.50, '2025-10-20 19:50:00'),
(1, 3, 'Đã chụp màn hình Power Query Editor sau khi Remove Duplicates.\nKết quả: Trước khi remove có 1522 dòng, sau khi remove còn 1489 dòng.\nĐã loại bỏ được 33 dòng trùng lặp ở cột "Mã khách hàng".', 2.50, '2025-10-20 20:15:00');

-- =====================================================
-- BÀI LÀM 2: SV001 - Bài tập 2 (Điểm: 9.0/10.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(2, 4, '3 điểm khác biệt chính:
1. Power BI Desktop: Ứng dụng desktop miễn phí, dùng để tạo và thiết kế báo cáo. Power BI Service: Dịch vụ cloud trả phí, dùng để publish và share báo cáo.
2. Desktop: Có đầy đủ tính năng modeling và DAX. Service: Chỉ xem và tương tác với báo cáo.
3. Desktop: Làm việc offline. Service: Cần internet, hỗ trợ collaboration và refresh tự động.', 4.00, '2025-10-29 00:30:00'),
(2, 5, 'Calculated Column: Được tính toán khi load hoặc refresh dữ liệu, lưu vào bảng, tốn bộ nhớ. Tính toán theo từng dòng (row context).
Measure: Được tính toán động khi user tương tác với visual, không lưu vào bảng. Tính toán theo filter context, tiết kiệm bộ nhớ hơn.', 2.50, '2025-10-29 00:45:00'),
(2, 6, 'Hàm ALL() trong DAX dùng để bỏ qua filter từ visual khác. Ví dụ: CALCULATE(SUM(Sales[Amount]), ALL(Product[Category])) sẽ tính tổng tất cả sản phẩm bất kể filter nào.', 2.50, '2025-10-29 01:05:00');

-- =====================================================
-- BÀI LÀM 4: SV002 - Bài tập 1 (Điểm: 7.0/5.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(4, 1, 'File tên "Data_KH_2024.xlsx", có 2 sheet: Main và Summary', 0.50, '2025-10-20 19:00:00'),
(4, 2, 'Có 1520 dòng dữ liệu (bao gồm header)', 1.50, '2025-10-20 19:30:00'),
(4, 3, 'Đã chụp màn hình Power Query.\nSau khi Remove Duplicates ở cột Mã khách hàng, còn lại 1485 dòng unique.\nĐã remove được 35 dòng duplicate.', 2.50, '2025-10-20 20:20:00');

-- =====================================================
-- BÀI LÀM 5: SV002 - Bài tập 2 (Điểm: 6.5/10.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(5, 4, 'Power BI Desktop là app cài trên máy để làm báo cáo, còn Power BI Service là web để xem báo cáo online. Desktop miễn phí, Service có trả phí. Desktop làm offline được, Service cần internet.', 3.00, '2025-10-29 00:15:00'),
(5, 5, 'Calculated Column tính khi load data và lưu vào table. Measure tính khi user click vào visual, không lưu.', 2.00, '2025-10-29 00:40:00'),
(5, 6, 'Dùng hàm REMOVEFILTERS() hoặc ALL()', 1.50, '2025-10-29 01:00:00');

-- =====================================================
-- BÀI LÀM 7: SV003 - Bài tập 1 (Điểm: 10.0/5.0) - ĐIỂM TUYỆT ĐỐI
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(7, 1, 'File Excel có tên "Khach_Hang_Q3_2024_Final.xlsx" và chứa 3 sheet:
- Sheet1 "DuLieuChinh": Chứa toàn bộ dữ liệu khách hàng
- Sheet2 "ThongKe": Các bảng thống kê tổng hợp
- Sheet3 "MetaData": Mô tả cấu trúc dữ liệu và data dictionary', 1.00, '2025-10-16 10:00:00'),
(7, 2, 'Tổng số dòng dữ liệu trong sheet chính là 1525 dòng, bao gồm:
- 1 dòng header (tên các cột)
- 1524 dòng dữ liệu khách hàng thực tế
Đã verify bằng cách dùng Ctrl+Down để jump đến dòng cuối cùng.', 1.50, '2025-10-16 10:15:00'),
(7, 3, 'Đã thực hiện các bước sau trong Power Query:
1. Import file Excel vào Power Query Editor
2. Chọn cột "Mã khách hàng" 
3. Right-click → Remove Duplicates
4. Kết quả: Từ 1524 dòng giảm xuống còn 1490 dòng
5. Đã loại bỏ được 34 dòng trùng lặp
Đính kèm screenshot Power Query Editor với Applied Steps hiển thị step "Removed Duplicates".', 2.50, '2025-10-16 10:35:00');

-- =====================================================
-- BÀI LÀM 8: SV003 - Bài tập 2 (Điểm: 9.5/10.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(8, 4, 'Ba điểm khác biệt chính giữa Power BI Desktop và Power BI Service:

1. **Môi trường và Licensing:**
   - Desktop: Ứng dụng Windows miễn phí, cài đặt local trên máy tính
   - Service: Nền tảng SaaS cloud-based, yêu cầu license Pro/Premium ($9.99/user/month)

2. **Chức năng và Use Case:**
   - Desktop: Công cụ phát triển báo cáo - data modeling, DAX, custom visuals, advanced transformations
   - Service: Nền tảng chia sẻ và collaboration - publish reports, dashboards, apps, scheduled refresh

3. **Data Connectivity:**
   - Desktop: Kết nối trực tiếp đến mọi nguồn dữ liệu (100+ connectors), làm việc offline
   - Service: Chỉ xem dữ liệu đã published, cần gateway cho on-premises data sources', 4.00, '2025-10-23 11:00:00'),
(8, 5, 'Sự khác biệt giữa Calculated Column và Measure trong Power BI:

**Calculated Column:**
- Thời điểm tính toán: Khi data được load/refresh vào model
- Storage: Giá trị được lưu trữ trong table, chiếm dung lượng bộ nhớ
- Context: Row context - tính toán theo từng dòng độc lập
- Use case: Dùng cho filtering, slicing, grouping (vì là cột thật trong table)
- Syntax: `Column Name = RELATED(Table[Field])` hoặc `IF([Field] > 100, "High", "Low")`

**Measure:**
- Thời điểm tính toán: Runtime khi user tương tác với visual (dynamic)
- Storage: Không lưu giá trị, chỉ lưu formula - tiết kiệm RAM
- Context: Filter context - tính toán dựa trên filters từ slicers, visuals
- Use case: Tính toán aggregation động (SUM, AVERAGE, percentage, YoY growth)
- Syntax: `Total Sales = SUM(Sales[Amount])` hoặc `Sales YTD = TOTALYTD([Total Sales], Calendar[Date])`

**Best Practice:** Ưu tiên dùng Measure thay vì Calculated Column để optimize performance.', 3.00, '2025-10-23 11:25:00'),
(8, 6, 'Trong DAX, có nhiều hàm để bỏ qua filter từ visual khác, bao gồm:

1. **ALL()**: Bỏ qua tất cả filters trên table hoặc column
   ```
   Total Sales (No Filter) = CALCULATE(SUM(Sales[Amount]), ALL(Sales))
   ```

2. **REMOVEFILTERS()**: Tương tự ALL() nhưng rõ ràng hơn về ý nghĩa
   ```
   Sales All Products = CALCULATE([Total Sales], REMOVEFILTERS(Product[Category]))
   ```

3. **ALLEXCEPT()**: Bỏ qua tất cả filters TRỪ các column được chỉ định
   ```
   Sales All Except Region = CALCULATE([Total Sales], ALLEXCEPT(Sales, Sales[Region]))
   ```

4. **ALLSELECTED()**: Bỏ qua filters từ visual nhưng giữ lại filters từ slicers/page-level
   ```
   % of Total Selected = DIVIDE([Total Sales], CALCULATE([Total Sales], ALLSELECTED()))
   ```

Trong thực tế, ALL() và REMOVEFILTERS() được dùng phổ biến nhất để tính % of Grand Total.', 2.50, '2025-10-23 11:40:00');

-- =====================================================
-- BÀI LÀM 9: SV003 - Bài tập 3 (Điểm: 8.0/5.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(9, 7, 'Đã import 5 bảng vào data model:
1. dimProduct (Danh mục sản phẩm)
2. dimCustomer (Thông tin khách hàng)
3. dimDate (Calendar table)
4. factSales (Dữ liệu bán hàng)
5. dimStore (Thông tin cửa hàng)', 1.50, '2025-11-10 16:30:00'),
(9, 8, 'Đã chụp màn hình Model View với đầy đủ relationships:
- dimProduct[ProductID] → factSales[ProductID] (Many-to-One)
- dimCustomer[CustomerID] → factSales[CustomerID] (Many-to-One)
- dimDate[Date] → factSales[OrderDate] (One-to-Many)
- dimStore[StoreID] → factSales[StoreID] (Many-to-One)
Tất cả relationships đều active và sử dụng cross-filter direction Single.', 3.50, '2025-11-10 17:00:00');

-- =====================================================
-- BÀI LÀM 10: SV004 - Bài tập 1 (Điểm: 5.5/5.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(10, 1, 'File tên KhachHang.xlsx, có 2 sheet', 0.50, '2025-10-20 18:00:00'),
(10, 2, '1500 dòng', 1.00, '2025-10-20 18:20:00'),
(10, 3, 'Đã remove duplicates, còn 1480 records', 1.50, '2025-10-20 18:45:00');

-- =====================================================
-- BÀI LÀM 13: SV005 - Bài tập 1 (Điểm: 9.0/5.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(13, 1, 'File Excel có tên "DuLieu_KhachHang_Q3_2024.xlsx" và chứa 2 sheet:
- Sheet "Data": Dữ liệu khách hàng chính
- Sheet "Notes": Ghi chú và hướng dẫn', 1.00, '2025-10-19 14:00:00'),
(13, 2, 'Sheet chính có tổng cộng 1521 dòng dữ liệu (1 header + 1520 dòng data thực tế)', 1.50, '2025-10-19 14:20:00'),
(13, 3, 'Đã thực hiện Remove Duplicates trên cột "Mã khách hàng" trong Power Query.
Kết quả: Từ 1520 dòng ban đầu, sau khi loại bỏ trùng lặp còn lại 1488 dòng unique.
Đã chụp screenshot Power Query Editor với Applied Steps panel hiển thị bước "Removed Duplicates" và Preview panel hiển thị 1488 rows.', 2.50, '2025-10-19 14:45:00');

-- =====================================================
-- BÀI LÀM 14: SV005 - Bài tập 2 (Điểm: 8.5/10.0)
-- =====================================================
INSERT INTO tra_loi_bai_tap (bai_lam_id, cau_hoi_id, noi_dung_tra_loi, diem, thoi_gian_tra_loi) VALUES
(14, 4, '3 điểm khác biệt chính giữa Power BI Desktop và Service:

1. Platform: Desktop là Windows application miễn phí cài local, Service là cloud platform với subscription model (Pro/Premium).

2. Capabilities: Desktop dùng để build và design reports (data modeling, transformations, DAX), còn Service dùng để share và collaborate (publish, dashboards, apps, alerts).

3. Data Access: Desktop connect trực tiếp đến data sources và work offline, Service cần internet và require gateway cho on-premises sources.', 3.50, '2025-10-28 20:00:00'),
(14, 5, 'Calculated Column vs Measure:

Calculated Column:
- Tính toán lúc data refresh, lưu vào table chiếm RAM
- Sử dụng row context
- Dùng cho filtering và grouping
- VD: `Customer Age = YEAR(TODAY()) - YEAR(Customer[BirthDate])`

Measure:
- Tính toán dynamic khi user interact với visual
- Không lưu data, chỉ lưu formula
- Sử dụng filter context
- Dùng cho aggregations
- VD: `Total Revenue = SUM(Sales[Amount])`', 2.50, '2025-10-28 20:25:00'),
(14, 6, 'Hàm ALL() trong DAX dùng để bỏ qua filters từ visuals khác.

Example: 
```
Total Sales All = CALCULATE(SUM(Sales[Amount]), ALL(Sales))
```

Hoặc dùng REMOVEFILTERS() cho readable hơn:
```
Sales No Filter = CALCULATE([Total Sales], REMOVEFILTERS(Product))
```', 2.50, '2025-10-28 20:45:00');

-- =====================================================
-- VERIFY DỮ LIỆU
-- =====================================================
SELECT 
    bl.id as bai_lam_id,
    nd.ma_nguoi_dung,
    nd.ho_ten,
    bt.tieu_de as bai_tap,
    bl.diem as tong_diem,
    COUNT(tl.id) as so_cau_tra_loi
FROM bai_lam bl
JOIN nguoi_dung nd ON bl.sinh_vien_id = nd.id
JOIN bai_tap bt ON bl.bai_tap_id = bt.id
LEFT JOIN tra_loi_bai_tap tl ON tl.bai_lam_id = bl.id
WHERE bl.diem IS NOT NULL
GROUP BY bl.id, nd.ma_nguoi_dung, nd.ho_ten, bt.tieu_de, bl.diem
ORDER BY bl.id;
