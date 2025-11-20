# 🔄 Kế hoạch Refactor: Hợp nhất WorkDashBoard & TestDashBoard

**Ngày tạo:** 11/11/2025  
**Trạng thái:** Đang lên kế hoạch  
**Mục tiêu:** Tái sử dụng WorkDashBoard.html để xử lý cả bài tập và bài kiểm tra

---

## 📋 Tổng quan

Hiện tại có 2 trang giống hệt nhau về giao diện và logic:
- **WorkDashBoard.html** - Quản lý bài tập
- **TestDashBoard.html** - Quản lý bài kiểm tra

**Vấn đề:**
-  Duplicate code (HTML, JS, CSS)
-  Khó maintain (sửa phải sửa 2 chỗ)
-  Không scalable

**Giải pháp:**
✅ Nâng cấp WorkDashBoard thành trang đa năng với query parameter `?type=homework|exam`

---

## 🎯 Phương án được chọn: Tái sử dụng WorkDashBoard

### Lý do chọn:
1. Không cần tạo file mới
2. Ít thay đổi nhất
3. Backward compatible
4. Dễ test và maintain

---

## 📝 Chi tiết thay đổi

### 1️⃣ Nâng cấp `WorkDashBoard.html`

**File:** `public/teacher/WorkDashBoard.html`

**Thay đổi:**
```html
<!-- BEFORE -->
<title>Bảng điều khiển bài tập - Learn Lab</title>

<!-- AFTER -->
<title id="pageTitle">Bảng điều khiển bài tập - Learn Lab</title>
```

**Lý do:** Cho phép JavaScript update title động dựa vào type

---

### 2️⃣ Nâng cấp `WDBscript.js`

**File:** `public/teacher/js/WDBscript.js`

**Thêm vào đầu file:**

```javascript
// ========== PHÁT HIỆN LOẠI BÀI TẬP ==========
const urlParams = new URLSearchParams(window.location.search);
const assignmentType = urlParams.get('type') || 'homework'; // Mặc định là homework
const assignmentId = urlParams.get('id'); // ID của bài tập/kiểm tra

// ========== CẤU HÌNH THEO LOẠI ==========
const CONFIG = {
  homework: {
    title: 'Bảng điều khiển bài tập',
    detailPage: 'HomeWork.html',
    apiEndpoint: '/api/homework',
    breadcrumb: 'Bài tập'
  },
  exam: {
    title: 'Bảng điều khiển bài kiểm tra',
    detailPage: 'improve/ChamBai.html',
    apiEndpoint: '/api/exam',
    breadcrumb: 'Bài kiểm tra'
  }
};

const currentConfig = CONFIG[assignmentType];

// Cập nhật title trang
document.addEventListener('DOMContentLoaded', () => {
  const titleElement = document.getElementById('pageTitle');
  if (titleElement) {
    titleElement.textContent = `${currentConfig.title} - Learn Lab`;
  }
});
```

**Sửa hàm `renderStudents()`:**

```javascript
// BEFORE
tbody.innerHTML = currentStudents.map(student => `
  <div class="hs-1" data-marked="${student.marked}" 
       onclick="window.location.href='../HomeWork.html'">
    <!-- ... -->
  </div>
`).join('');

// AFTER
tbody.innerHTML = currentStudents.map(student => `
  <div class="hs-1" data-marked="${student.marked}" 
       onclick="window.location.href='${currentConfig.detailPage}?studentId=${student.id}&assignmentId=${assignmentId}'">
    <!-- ... -->
  </div>
`).join('');
```

**Thêm function fetch data từ API (optional):**

```javascript
// ========== FETCH DATA TỪ API ==========
async function fetchAssignmentData() {
  try {
    const response = await fetch(`${currentConfig.apiEndpoint}/${assignmentId}/submissions`);
    const data = await response.json();
    
    // Update allStudents với data thực
    // allStudents = data.submissions;
    
    renderStudents();
  } catch (error) {
    console.error('Lỗi fetch data:', error);
    // Fallback về mock data
    renderStudents();
  }
}
```

---

### 3️⃣ Cập nhật `ClassroomInfo.html`

**File:** `public/teacher/ClassroomInfo.html`

**Tìm phần Assignments section:**

```html
<!-- BEFORE -->
<div class="assignment-card" onclick="window.location.href='../WorkDashBoard.html'">
  <div class="assignment-details">
    <div class="assignment-meta">Chương 1 - 1.3. Tên mục lục</div>
    <div class="assignment-title">Bài tập Các hệ thống thông tin</div>
    <!-- ... -->
  </div>
</div>

<!-- AFTER -->
<div class="assignment-card" onclick="window.location.href='WorkDashBoard.html?type=homework&id=123'">
  <div class="assignment-details">
    <div class="assignment-meta">Chương 1 - 1.3. Tên mục lục</div>
    <div class="assignment-title">Bài tập Các hệ thống thông tin</div>
    <!-- ... -->
  </div>
</div>
```

**Tìm phần Exams section:**

```html
<!-- BEFORE -->
<div class="assignment-card">
  <div class="assignment-details">
    <div class="assignment-meta">Chương 1 - 1.3. Tên mục lục</div>
    <div class="assignment-title">Bài kiểm tra 01</div>
    <!-- ... -->
  </div>
</div>

<!-- AFTER -->
<div class="assignment-card" onclick="window.location.href='WorkDashBoard.html?type=exam&id=456'">
  <div class="assignment-details">
    <div class="assignment-meta">Chương 1 - 1.3. Tên mục lục</div>
    <div class="assignment-title">Bài kiểm tra 01</div>
    <!-- ... -->
  </div>
</div>
```

---

### 4️⃣ Cập nhật `routes.js` (Optional)

**File:** `public/teacher/js/config/routes.js`

```javascript
// BEFORE
DASHBOARD_BAI_TAP: {
  duongDan: 'WorkDashBoard.html',
  ten: 'Quản lý bài tập',
  parent: 'lophoc'
}

// AFTER
DASHBOARD_BAI_TAP: {
  duongDan: 'WorkDashBoard.html',
  ten: 'Quản lý bài tập & kiểm tra',
  parent: 'lophoc'
}
```

---

### 5️⃣ Thêm config vào `constants.js` (Optional)

**File:** `public/teacher/js/config/constants.js`

```javascript
// ==================== Assignment Types ====================
export const ASSIGNMENT_TYPES = {
  HOMEWORK: {
    key: 'homework',
    apiEndpoint: '/api/homework',
    detailPage: 'HomeWork.html',
    displayName: 'Bài tập',
    icon: 'exercise0.svg'
  },
  EXAM: {
    key: 'exam',
    apiEndpoint: '/api/exam',
    detailPage: 'improve/ChamBai.html',
    displayName: 'Bài kiểm tra',
    icon: 'exam0.svg'
  }
};

// API Endpoints cho assignments
export const API_ENDPOINTS = {
  // ... existing endpoints ...
  
  // Assignment Dashboard
  LAY_DANH_SACH_BAI_NOP: '/api/:type/:id/submissions',
  LAY_THONG_KE: '/api/:type/:id/statistics'
};
```

---

### 6️⃣ Di chuyển file vào `improve/`

**Các file cần di chuyển:**

```
improve/
├── ChamBai.html          ← Di chuyển (đã có sẵn - detail page cho exam)
├── chamBai.js            ← Di chuyển (đã có sẵn)
├── TestDashBoard.html    ← Di chuyển (legacy backup)
├── TDBscript.js          ← Di chuyển (legacy backup)
└── CBstyle.css           ← Di chuyển (CSS cho ChamBai)
```

**Lệnh di chuyển (PowerShell):**

```powershell
# Di chuyển TestDashBoard files vào improve (nếu chưa có)
Move-Item "public/teacher/TestDashBoard.html" "public/teacher/improve/" -Force
Move-Item "public/teacher/js/TDBscript.js" "public/teacher/improve/js/" -Force
Move-Item "public/teacher/CSS/TDBstyle.css" "public/teacher/improve/CSS/" -Force
```

---

## 🔄 Flow hoạt động mới

```
ClassroomInfo.html (Chi tiết lớp học)
    │
    ├─────► Click "Bài tập X"
    │       │
    │       └─► WorkDashBoard.html?type=homework&id=X
    │           │
    │           ├─► Hiển thị danh sách sinh viên
    │           ├─► Thống kê: đã chấm, chưa chấm, điểm TB
    │           │
    │           └─► Click sinh viên
    │               └─► HomeWork.html?studentId=123&assignmentId=X
    │                   └─► Xem chi tiết bài làm của sinh viên
    │
    └─────► Click "Bài kiểm tra Y"
            │
            └─► WorkDashBoard.html?type=exam&id=Y
                │
                ├─► Hiển thị danh sách sinh viên
                ├─► Thống kê: đã chấm, chưa chấm, điểm TB
                │
                └─► Click sinh viên
                    └─► improve/ChamBai.html?studentId=456&examId=Y
                        └─► Chấm bài kiểm tra của sinh viên
```

---

## 🎨 Cấu trúc thư mục sau refactor

```
public/teacher/
├── WorkDashBoard.html          ← NÂNG CẤP (xử lý cả homework & exam)
├── ClassroomInfo.html          ← CẬP NHẬT (navigation với query params)
├── HomeWork.html               ← GIỮ NGUYÊN (detail page cho homework)
├── HomeWorkInfo.html           ← GIỮ NGUYÊN
├── improve/
│   ├── ChamBai.html            ← DI CHUYỂN (detail page cho exam)
│   ├── TestDashBoard.html      ← DI CHUYỂN (legacy - có thể xóa sau)
│   ├── REFACTOR_PLAN.md        ← FILE NÀY
│   ├── js/
│   │   ├── chamBai.js          ← DI CHUYỂN
│   │   └── TDBscript.js        ← DI CHUYỂN (legacy)
│   └── CSS/
│       ├── CBstyle.css         ← DI CHUYỂN
│       └── TDBstyle.css        ← DI CHUYỂN (legacy)
├── CSS/
│   ├── WDBstyle.css            ← GIỮ NGUYÊN (dùng cho cả 2 type)
│   ├── HIstyle.css
│   └── ...
└── js/
    ├── WDBscript.js            ← NÂNG CẤP (thêm logic detect type)
    ├── CIscript.js             ← CẬP NHẬT (nếu có logic navigation)
    └── config/
        ├── routes.js           ← CẬP NHẬT (optional)
        └── constants.js        ← THÊM ASSIGNMENT_TYPES (optional)
```

---

## ✅ Checklist thực hiện

### Phase 1: Chuẩn bị (5 phút)
- [ ] Backup các file sẽ sửa
- [ ] Tạo folder `improve/js/` và `improve/CSS/` nếu chưa có
- [ ] Review lại code hiện tại

### Phase 2: Nâng cấp WorkDashBoard (20 phút)
- [ ] Sửa `WorkDashBoard.html` - thêm id="pageTitle"
- [ ] Sửa `WDBscript.js` - thêm logic detect type
- [ ] Sửa `WDBscript.js` - update renderStudents() với dynamic navigation
- [ ] Test với URL: `WorkDashBoard.html?type=homework`
- [ ] Test với URL: `WorkDashBoard.html?type=exam`

### Phase 3: Cập nhật navigation (10 phút)
- [ ] Sửa `ClassroomInfo.html` - assignments section
- [ ] Sửa `ClassroomInfo.html` - exams section
- [ ] Test click từ ClassroomInfo → WorkDashBoard

### Phase 4: Dọn dẹp (5 phút)
- [ ] Di chuyển `TestDashBoard.html` vào `improve/`
- [ ] Di chuyển `TDBscript.js` vào `improve/js/`
- [ ] Di chuyển `TDBstyle.css` vào `improve/CSS/` (nếu khác WDBstyle.css)
- [ ] Cập nhật `routes.js` (optional)
- [ ] Cập nhật `constants.js` (optional)

### Phase 5: Testing (10 phút)
- [ ] Test flow: ClassroomInfo → WorkDashBoard (homework)
- [ ] Test flow: ClassroomInfo → WorkDashBoard (exam)
- [ ] Test pagination
- [ ] Test filter (đã chấm/chưa chấm)
- [ ] Test navigation đến detail page
- [ ] Test backward compatibility (không có ?type)

### Phase 6: Documentation (5 phút)
- [ ] Update README nếu có
- [ ] Comment code rõ ràng
- [ ] Document API endpoints mới

---

##  Estimate thời gian

| Phase | Thời gian |
|-------|-----------|
| Chuẩn bị | 5 phút |
| Nâng cấp WorkDashBoard | 20 phút |
| Cập nhật navigation | 10 phút |
| Dọn dẹp | 5 phút |
| Testing | 10 phút |
| Documentation | 5 phút |
| **TỔNG** | **~55 phút** |

---

## 🎁 Lợi ích

### Trước refactor:
-  2 HTML files giống hệt nhau
-  2 JS files giống hệt nhau
-  2 CSS files giống hệt nhau
-  Sửa bug phải sửa 2 chỗ
-  Thêm feature phải code 2 lần

### Sau refactor:
- ✅ 1 HTML file duy nhất
- ✅ 1 JS file duy nhất
- ✅ 1 CSS file duy nhất
- ✅ DRY principle
- ✅ Dễ maintain
- ✅ Dễ mở rộng (thêm Quiz, Project, etc.)
- ✅ Consistent UI/UX
- ✅ Giảm 50% code cần maintain

---

## 🚀 Mở rộng tương lai

### Có thể thêm các loại assignment khác:

```javascript
const CONFIG = {
  homework: { /* ... */ },
  exam: { /* ... */ },
  quiz: {
    title: 'Bảng điều khiển bài quiz',
    detailPage: 'QuizDetail.html',
    apiEndpoint: '/api/quiz',
    breadcrumb: 'Quiz'
  },
  project: {
    title: 'Bảng điều khiển dự án',
    detailPage: 'ProjectDetail.html',
    apiEndpoint: '/api/project',
    breadcrumb: 'Dự án'
  }
};
```

### Thêm filter theo trạng thái:

- Đã nộp / Chưa nộp
- Đúng hạn / Trễ hạn
- Đã chấm / Chưa chấm
- Điểm cao / Điểm thấp

### Thêm tính năng:

- Export danh sách điểm ra Excel
- Gửi email nhắc nhở sinh viên chưa nộp
- Biểu đồ phân bố điểm
- So sánh điểm giữa các lớp

---

## 📝 Notes

- **Backward Compatibility:** Nếu URL không có `?type=`, mặc định là `homework`
- **URL Structure:** `WorkDashBoard.html?type=homework&id=123`
  - `type`: homework | exam | quiz | project
  - `id`: ID của assignment
- **Detail Page Navigation:** Tự động chuyển đến đúng detail page dựa vào type
- **API Integration:** Dễ dàng tích hợp API endpoint khác nhau cho từng type

---

## 🔗 Related Files

**Files cần sửa:**
- `public/teacher/WorkDashBoard.html`
- `public/teacher/js/WDBscript.js`
- `public/teacher/ClassroomInfo.html`

**Files optional:**
- `public/teacher/js/config/routes.js`
- `public/teacher/js/config/constants.js`

**Files di chuyển vào improve:**
- `public/teacher/TestDashBoard.html`
- `public/teacher/js/TDBscript.js`
- `public/teacher/CSS/TDBstyle.css`

---

##  Rủi ro & Giải pháp

| Rủi ro | Giải pháp |
|--------|-----------|
| Breaking existing links | Giữ backward compatibility với default type=homework |
| CSS conflicts | Test kỹ cả 2 type, merge CSS cẩn thận |
| API endpoint khác nhau | Dùng config object để map đúng endpoint |
| Detail page khác nhau | Dùng config.detailPage để navigate đúng |

---

**Người thực hiện:** [Tên của bạn]  
**Review bởi:** [Tên reviewer]  
**Ngày hoàn thành dự kiến:** [Ngày]

---

_Lưu ý: File này được tạo để document kế hoạch refactor. Sau khi hoàn thành, có thể archive hoặc update status._
