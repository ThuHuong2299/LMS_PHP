/**
 * TESTDASHBOARD SCRIPT
 * Logic riêng cho trang TestDashBoard
 * Sidebar và các component chung đã được xử lý bởi main.js
 */

// --- Tạo danh sách sinh viên mẫu ---
const allStudents = [];
const names = [
  'Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Minh Đức', 'Hoàng Thu Hà',
  'Vũ Đức Khang', 'Đặng Thùy Linh', 'Bùi Quốc Minh', 'Đỗ Hồng Nhung', 'Mai Xuân Phúc',
  'Ngô Thu Quyên', 'Phan Văn Sơn', 'Đinh Thị Trang', 'Lý Anh Tuấn', 'Trương Bảo Uyên',
  'Dương Minh Việt', 'Tô Hải Yến', 'Võ Thanh An', 'Hồ Thu Hằng', 'Chu Đức Huy'
];

for (let i = 0; i < 36; i++) {
  allStudents.push({
    name: names[i % names.length],
    id: `SV${String(i + 1).padStart(4, '0')}`,
    status: i % 3 === 0 ? 'Đã hoàn thành' : (i % 3 === 1 ? 'Đang làm bài' : 'Chưa làm'),
    score: (Math.random() * 10).toFixed(1),
    time: `${Math.floor(Math.random() * 40) + 10} phút`,
    marked: i % 2 === 0 ? 'đã chấm' : 'chưa chấm'
  });
}

// --- Biến điều khiển ---
let currentPage = 1;
const studentsPerPage = 5;
let filterMode = "all"; // "all" | "graded" | "ungraded"

// --- Hàm lấy dữ liệu đang hiển thị ---
function getFilteredStudents() {
  if (filterMode === "graded") return allStudents.filter(s => s.marked === 'đã chấm');
  if (filterMode === "ungraded") return allStudents.filter(s => s.marked === 'chưa chấm');
  return allStudents;
}

// --- Phân trang ---
function renderStudents() {
  const tbody = document.getElementById('studentList');
  const filtered = getFilteredStudents();

  const totalStudents = filtered.length;
  const totalPages = Math.ceil(totalStudents / studentsPerPage) || 1;
  if (currentPage > totalPages) currentPage = totalPages;

  const startIndex = (currentPage - 1) * studentsPerPage;
  const endIndex = startIndex + studentsPerPage;
  const currentStudents = filtered.slice(startIndex, endIndex);

  tbody.innerHTML = currentStudents.map(student => `
    <div class="hs-1" data-marked="${student.marked}" onclick="window.location.href='../ChamBai.html'">
      <div class="cell-4">
        <div class="box">
          <div class="custom-table-custom-cell">
            <div class="avatar" style="background: url(avatar0.png) center/cover no-repeat;"></div>
            <div class="frame-1321316798">
              <div class="t-n-sinh-vi-n">${student.name}</div>
              <div class="m-sinh-vi-n">${student.id}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="cell-6">
        <div class="box2">
          <div class="typography">
            <div class="body-2">${student.status}</div>
          </div>
        </div>
      </div>
      <div class="hspoint">
        <div class="body-2">${student.score}</div>
      </div>
      <div class="hstime">
        <div class="body-2">${student.time}</div>
      </div>
      <div class="hidden-status" style="display:none;">${student.marked}</div>
      <img class="vector-46" src="vector-460.svg" />
    </div>
  `).join('');

  updatePagination(totalStudents, totalPages);
}

// --- Cập nhật thanh phân trang ---
function updatePagination(totalStudents, totalPages) {
  const start = (currentPage - 1) * studentsPerPage + 1;
  const end = Math.min(currentPage * studentsPerPage, totalStudents);
  const info = document.querySelector('.pagination-info');

  if (totalStudents === 0) {
    info.textContent = 'Không có dữ liệu';
  } else {
    info.textContent = `${start}-${end} của ${totalStudents}`;
  }

  document.querySelector('.page-btn:first-child').disabled = currentPage === 1;
  document.querySelector('.page-btn:last-child').disabled = currentPage === totalPages;
}

// --- Nút điều hướng ---
function previousPage() {
  if (currentPage > 1) {
    currentPage--;
    renderStudents();
  }
}
function nextPage() {
  currentPage++;
  renderStudents();
}

// --- Xử lý nút lọc ---
const btnGraded = document.getElementById('baidacham');
const btnUngraded = document.getElementById('baichuacham');

// Ban đầu đều màu xám
btnGraded.classList.add('inactive');
btnUngraded.classList.add('inactive');

btnGraded.addEventListener('click', function () {
  if (filterMode === "graded") {
    // Nếu đang bật thì tắt -> hiển thị tất cả
    filterMode = "all";
    this.classList.add('inactive');
  } else {
    // Bật nút đã chấm, tắt nút kia
    filterMode = "graded";
    this.classList.remove('inactive');
    btnUngraded.classList.add('inactive');
  }
  currentPage = 1;
  renderStudents();
});

btnUngraded.addEventListener('click', function () {
  if (filterMode === "ungraded") {
    // Nếu đang bật thì tắt -> hiển thị tất cả
    filterMode = "all";
    this.classList.add('inactive');
  } else {
    // Bật nút chưa chấm, tắt nút kia
    filterMode = "ungraded";
    this.classList.remove('inactive');
    btnGraded.classList.add('inactive');
  }
  currentPage = 1;
  renderStudents();
});

// --- Khởi tạo ---
document.addEventListener('DOMContentLoaded', renderStudents);

// --- Logout đã được xử lý bởi SidebarManager trong main.js ---

