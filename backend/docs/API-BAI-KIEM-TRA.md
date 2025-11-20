# API BÀI KIỂM TRA - LMS SYSTEM

## 📋 Mô tả
File này mô tả chi tiết các API liên quan đến chức năng Bài kiểm tra trắc nghiệm cho sinh viên.

## 🔧 Cấu trúc Database

### Bảng `bai_kiem_tra`
- Lưu thông tin bài kiểm tra (tiêu đề, thời lượng, thời gian, điểm tối đa)
- Có thể gắn với chương học (`chuong_id`)

### Bảng `cau_hoi_trac_nghiem`
- Câu hỏi trắc nghiệm của bài kiểm tra
- Mỗi câu có điểm riêng

### Bảng `lua_chon_cau_hoi`
- Các lựa chọn A, B, C, D cho mỗi câu hỏi
- Có đánh dấu đáp án đúng (`la_dap_an_dung`)

### Bảng `bai_lam_kiem_tra`
- Bài làm của sinh viên (mỗi sinh viên chỉ làm 1 lần)
- Lưu trạng thái: `chua_lam`, `dang_lam`, `da_nop`, `da_cham`
- Tự động chấm điểm khi nộp bài

### Bảng `chi_tiet_tra_loi`
- Chi tiết câu trả lời của sinh viên cho từng câu hỏi

---

## 📡 API ENDPOINTS

### 1️⃣ Lấy danh sách bài kiểm tra

**Endpoint:** `GET /backend/student/api/danh-sach-bai-kiem-tra.php`

**Parameters:**
```
lop_hoc_id: int (required)
```

**Response:**
```json
{
  "thanh_cong": true,
  "du_lieu": [
    {
      "id": 1,
      "tieu_de": "Kiểm tra cuối Chương 1 - Giới thiệu Power BI",
      "mo_ta": "Bài kiểm tra đánh giá kiến thức Chương 1",
      "thoi_luong": 20,
      "thoi_gian_bat_dau": "2025-11-21 08:00:00",
      "thoi_gian_ket_thuc": "2025-11-21 08:20:00",
      "diem_toi_da": 10.00,
      "so_cau_hoi": 10,
      "chuong": {
        "so_thu_tu": 1,
        "ten_chuong": "Chương 1: Giới thiệu Power BI"
      },
      "trang_thai_lam_bai": "chua_lam",
      "co_the_lam": true,
      "bai_lam_id": null,
      "diem": null,
      "so_cau_dung": null,
      "tong_so_cau": null,
      "thoi_gian_nop": null
    }
  ],
  "thong_bao": "Lấy danh sách bài kiểm tra thành công"
}
```

**Trạng thái:**
- `chua_lam`: Chưa làm bài
- `dang_lam`: Đang làm dở
- `da_nop`: Đã nộp bài
- `da_cham`: Đã được chấm điểm (tự động)

---

### 2️⃣ Lấy chi tiết bài kiểm tra

**Endpoint:** `GET /backend/student/api/chi-tiet-bai-kiem-tra.php`

**Parameters:**
```
bai_kiem_tra_id: int (required)
```

**Response:**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "thong_tin_bai_kiem_tra": {
      "id": 1,
      "tieu_de": "Kiểm tra cuối Chương 1 - Giới thiệu Power BI",
      "mo_ta": "Bài kiểm tra đánh giá kiến thức Chương 1",
      "thoi_luong": 20,
      "thoi_gian_bat_dau": "2025-11-21 08:00:00",
      "thoi_gian_ket_thuc": "2025-11-21 08:20:00",
      "diem_toi_da": 10.00,
      "ten_mon_hoc": "Power BI & Data Analysis",
      "ma_lop_hoc": "L001"
    },
    "trang_thai_lam_bai": "chua_lam",
    "bai_lam_id": null,
    "thoi_gian_bat_dau_lam": null,
    "cau_hoi": [
      {
        "id": 1,
        "thu_tu": 1,
        "noi_dung_cau_hoi": "Power BI là gì?",
        "diem": 1.00,
        "lua_chon": [
          {
            "id": 1,
            "thu_tu": 1,
            "noi_dung_lua_chon": "A. Công cụ phân tích và trực quan hóa dữ liệu của Microsoft"
          },
          {
            "id": 2,
            "thu_tu": 2,
            "noi_dung_lua_chon": "B. Phần mềm soạn thảo văn bản"
          }
        ]
      }
    ],
    "co_the_lam": true
  },
  "thong_bao": "Lấy chi tiết bài kiểm tra thành công"
}
```

**Lưu ý:** 
- Khi `trang_thai_lam_bai` là `chua_lam` hoặc `dang_lam`: **KHÔNG** hiển thị `la_dap_an_dung`
- Khi `trang_thai_lam_bai` là `da_nop`: Hiển thị đáp án đúng

---

### 3️⃣ Bắt đầu làm bài kiểm tra

**Endpoint:** `POST /backend/student/api/bat-dau-bai-kiem-tra.php`

**Request Body (JSON):**
```json
{
  "bai_kiem_tra_id": 1
}
```

**Response (Thành công):**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "bai_lam_id": 123,
    "thoi_gian_bat_dau": "2025-11-21 08:05:30",
    "thong_bao": "Bắt đầu làm bài thành công"
  },
  "thong_bao": "Bắt đầu làm bài thành công"
}
```

**Response (Đang làm dở):**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "bai_lam_id": 123,
    "thoi_gian_bat_dau": "2025-11-21 08:05:30",
    "thong_bao": "Tiếp tục làm bài"
  }
}
```

**Validation:**
- Kiểm tra thời gian (phải trong khoảng `thoi_gian_bat_dau` - `thoi_gian_ket_thuc`)
- Không cho phép làm lại nếu đã nộp bài
- Tạo bản ghi `bai_lam_kiem_tra` với trạng thái `dang_lam`

---

### 4️⃣ Lưu câu trả lời

**Endpoint:** `POST /backend/student/api/luu-tra-loi-kiem-tra.php`

**Request Body (JSON):**
```json
{
  "bai_lam_id": 123,
  "cau_hoi_id": 5,
  "lua_chon_id": 18
}
```

**Response:**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "thong_bao": "Lưu câu trả lời thành công"
  },
  "thong_bao": "Lưu câu trả lời thành công"
}
```

**Chức năng:**
- Lưu hoặc cập nhật câu trả lời vào bảng `chi_tiet_tra_loi`
- Cho phép thay đổi câu trả lời khi đang làm bài
- Chỉ lưu được khi `trang_thai` là `dang_lam`

---

### 5️⃣ Nộp bài kiểm tra

**Endpoint:** `POST /backend/student/api/nop-bai-kiem-tra.php`

**Request Body (JSON):**
```json
{
  "bai_lam_id": 123
}
```

**Response:**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "diem": 8.50,
    "so_cau_dung": 9,
    "tong_so_cau": 10,
    "thoi_gian_lam_bai": 15,
    "thong_bao": "Nộp bài thành công"
  },
  "thong_bao": "Nộp bài thành công"
}
```

**Chức năng tự động:**
1. Chấm điểm tất cả câu trả lời
2. Tính tổng điểm dựa trên `diem` của từng câu hỏi
3. Đếm số câu đúng
4. Tính thời gian làm bài thực tế
5. Cập nhật trạng thái thành `da_nop`
6. Lưu `thoi_gian_nop`

---

### 6️⃣ Xem kết quả bài kiểm tra

**Endpoint:** `GET /backend/student/api/ket-qua-bai-kiem-tra.php`

**Parameters:**
```
bai_lam_id: int (required)
```

**Response:**
```json
{
  "thanh_cong": true,
  "du_lieu": {
    "thong_tin_bai_lam": {
      "id": 123,
      "tieu_de": "Kiểm tra cuối Chương 1 - Giới thiệu Power BI",
      "diem": 8.50,
      "diem_toi_da": 10.00,
      "so_cau_dung": 9,
      "tong_so_cau": 10,
      "thoi_luong": 20,
      "thoi_gian_bat_dau": "2025-11-21 08:05:30",
      "thoi_gian_nop": "2025-11-21 08:20:15",
      "thoi_gian_lam_bai": 15
    },
    "cau_hoi_va_dap_an": [
      {
        "cau_hoi_id": 1,
        "thu_tu": 1,
        "noi_dung": "Power BI là gì?",
        "diem": 1.00,
        "lua_chon_da_chon": 1,
        "dung_hay_sai": true,
        "lua_chon": [
          {
            "id": 1,
            "thu_tu": 1,
            "noi_dung": "A. Công cụ phân tích và trực quan hóa dữ liệu của Microsoft",
            "la_dap_an_dung": true,
            "da_chon": true
          },
          {
            "id": 2,
            "thu_tu": 2,
            "noi_dung": "B. Phần mềm soạn thảo văn bản",
            "la_dap_an_dung": false,
            "da_chon": false
          }
        ]
      }
    ]
  },
  "thong_bao": "Lấy kết quả bài kiểm tra thành công"
}
```

**Lưu ý:**
- Chỉ xem được khi đã nộp bài (`trang_thai` = `da_nop`)
- Hiển thị đầy đủ đáp án đúng và câu trả lời của sinh viên
- Highlight câu trả lời đúng/sai

---

## 🔐 Bảo mật

Tất cả API đều:
- ✅ Kiểm tra session đăng nhập
- ✅ Kiểm tra vai trò sinh viên
- ✅ Kiểm tra quyền truy cập lớp học
- ✅ Validate tất cả tham số đầu vào
- ✅ Không cho phép làm lại bài kiểm tra
- ✅ Kiểm tra thời gian làm bài hợp lệ

---

## 📦 Các file liên quan

### Backend
```
backend/
├── student/api/
│   ├── danh-sach-bai-kiem-tra.php       # API 1
│   ├── chi-tiet-bai-kiem-tra.php        # API 2
│   ├── bat-dau-bai-kiem-tra.php         # API 3
│   ├── luu-tra-loi-kiem-tra.php         # API 4
│   ├── nop-bai-kiem-tra.php             # API 5
│   └── ket-qua-bai-kiem-tra.php         # API 6
├── dieu-khieu/
│   └── SinhVienController.php           # Controller methods
├── dich-vu/
│   └── BaiKiemTraService.php            # Business logic
└── kho-du-lieu/
    └── BaiKiemTraRepository.php         # Database queries
```

### Database
```sql
backend/docs/
└── them-bai-kiem-tra-cuoi-chuong-1.sql  # Sample data
```

---

## 🎯 Flow hoàn chỉnh

```
1. Sinh viên vào trang danh sách bài kiểm tra
   → GET danh-sach-bai-kiem-tra.php

2. Xem chi tiết bài kiểm tra
   → GET chi-tiet-bai-kiem-tra.php

3. Bấm "Bắt đầu làm bài"
   → POST bat-dau-bai-kiem-tra.php
   → Nhận bai_lam_id

4. Chọn đáp án và lưu
   → POST luu-tra-loi-kiem-tra.php (nhiều lần)

5. Bấm "Nộp bài"
   → POST nop-bai-kiem-tra.php
   → Hệ thống tự động chấm điểm

6. Xem kết quả và đáp án
   → GET ket-qua-bai-kiem-tra.php
```

---

## 📊 Ví dụ sử dụng với JavaScript

```javascript
// 1. Lấy danh sách bài kiểm tra
fetch('/backend/student/api/danh-sach-bai-kiem-tra.php?lop_hoc_id=1', {
  credentials: 'include'
})
.then(res => res.json())
.then(data => console.log(data));

// 2. Bắt đầu làm bài
fetch('/backend/student/api/bat-dau-bai-kiem-tra.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({ bai_kiem_tra_id: 1 })
})
.then(res => res.json())
.then(data => {
  const baiLamId = data.du_lieu.bai_lam_id;
  // Lưu bai_lam_id để dùng cho các request tiếp theo
});

// 3. Lưu câu trả lời
fetch('/backend/student/api/luu-tra-loi-kiem-tra.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({
    bai_lam_id: 123,
    cau_hoi_id: 5,
    lua_chon_id: 18
  })
})
.then(res => res.json());

// 4. Nộp bài
fetch('/backend/student/api/nop-bai-kiem-tra.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  credentials: 'include',
  body: JSON.stringify({ bai_lam_id: 123 })
})
.then(res => res.json())
.then(data => {
  alert(`Điểm của bạn: ${data.du_lieu.diem}/${data.du_lieu.tong_so_cau}`);
});

// 5. Xem kết quả
fetch('/backend/student/api/ket-qua-bai-kiem-tra.php?bai_lam_id=123', {
  credentials: 'include'
})
.then(res => res.json())
.then(data => {
  // Hiển thị kết quả chi tiết
});
```

---

## ⚡ Tối ưu hóa

- Sử dụng transaction khi nộp bài để đảm bảo tính toàn vẹn dữ liệu
- Cache danh sách câu hỏi ở client để giảm load server
- Auto-save câu trả lời định kỳ (mỗi 30s) để tránh mất dữ liệu
- Countdown timer hiển thị thời gian còn lại
- Tự động nộp bài khi hết giờ

---

## 🐛 Error Handling

Các lỗi thường gặp:

```json
{
  "thanh_cong": false,
  "thong_bao": "Bạn đã làm bài kiểm tra này rồi"
}

{
  "thanh_cong": false,
  "thong_bao": "Bài kiểm tra chưa mở"
}

{
  "thanh_cong": false,
  "thong_bao": "Bài kiểm tra đã hết hạn"
}

{
  "thanh_cong": false,
  "thong_bao": "Không thể lưu câu trả lời khi bài làm đã nộp"
}
```

---

**Ngày tạo:** 20/11/2025  
**Phiên bản:** 1.0  
**Tác giả:** LMS Development Team
