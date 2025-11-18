// ==================== CONFIGURATION ====================
let allStudents = [];
let currentPage = 1;
const studentsPerPage = 5;
let filterMode = "all";
let baiTapId = null;
let baiKiemTraId = null;
let lopHocId = null;
let workType = null; // 'bai_tap' hoặc 'bai_kiem_tra'

// ==================== API FUNCTIONS ====================

/**
 * Lấy tham số từ URL
 */
function getUrlParams() {
  const urlParams = new URLSearchParams(window.location.search);
  
  // Kiểm tra loại công việc
  baiTapId = urlParams.get('bai_tap_id') || urlParams.get('id');
  baiKiemTraId = urlParams.get('bai_kiem_tra_id');
  lopHocId = urlParams.get('lop_hoc_id');
  
  // Xác định workType
  if (baiTapId) {
    workType = 'bai_tap';
    console.log('✅ Loại: Bài tập, ID:', baiTapId);
  } else if (baiKiemTraId) {
    workType = 'bai_kiem_tra';
    console.log('✅ Loại: Bài kiểm tra, ID:', baiKiemTraId);
  } else {
    console.error('Không tìm thấy bai_tap_id hoặc bai_kiem_tra_id trong URL');
    console.log('URL hiện tại:', window.location.href);
    showError('Thiếu thông tin. Vui lòng truy cập từ trang ClassroomInfo.');
    return false;
  }
  
  console.log('📍 Params:', { workType, baiTapId, baiKiemTraId, lopHocId });
  return true;
}

/**
 * Fetch dữ liệu từ API
 */
async function fetchWorkDashboardData() {
  try {
    let apiUrl = '';
    
    if (workType === 'bai_tap') {
      apiUrl = `/backend/teacher/api/chi-tiet-bai-tap.php?bai_tap_id=${baiTapId}`;
    } else if (workType === 'bai_kiem_tra') {
      // TODO: Backend API cho bài kiểm tra chưa implement
      showError('Chức năng xem chi tiết bài kiểm tra đang được phát triển');
      return;
    }
    
    const response = await fetch(apiUrl, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      updateStatistics(data.du_lieu.thong_ke);
      allStudents = formatStudentData(data.du_lieu.danh_sach_sinh_vien);
      renderStudents();
    } else {
      showError(data.thong_bao || 'Không thể lấy dữ liệu');
    }
  } catch (error) {
    console.error('Lỗi khi fetch dữ liệu:', error);
    showError('Không thể kết nối đến server');
  }
}

/**
 * Cập nhật thống kê
 */
function updateStatistics(thongKe) {
  // Cập nhật số bài chưa chấm
  const soBaiChuaCham = document.querySelector('#baichuacham ._12-b-i');
  if (soBaiChuaCham) {
    soBaiChuaCham.textContent = `${thongKe.so_bai_chua_cham} bài`;
  }
  
  // Cập nhật số bài đã chấm
  const soBaiDaCham = document.querySelector('#baidacham ._12-b-i');
  if (soBaiDaCham) {
    soBaiDaCham.textContent = `${thongKe.so_bai_da_cham} bài`;
  }
  
  // Cập nhật điểm trung bình
  const diemTB = document.querySelector('.average ._8-5-10-span');
  if (diemTB) {
    diemTB.textContent = thongKe.diem_trung_binh !== null ? thongKe.diem_trung_binh.toFixed(1) : '-';
  }
  
  // Cập nhật điểm cao nhất
  const diemMax = document.querySelector('.highest ._8-8-10-span');
  if (diemMax) {
    diemMax.textContent = thongKe.diem_cao_nhat !== null ? thongKe.diem_cao_nhat.toFixed(1) : '-';
  }
  
  // Cập nhật điểm thấp nhất
  const diemMin = document.querySelector('.lowest ._1-2-10-span');
  if (diemMin) {
    diemMin.textContent = thongKe.diem_thap_nhat !== null ? thongKe.diem_thap_nhat.toFixed(1) : '-';
  }
  
  // Cập nhật tổng bài nộp
  const tongBaiNop = document.querySelector('.sum ._32-36-span');
  const tongSinhVien = document.querySelector('.sum ._32-36-span2');
  if (tongBaiNop && tongSinhVien) {
    tongBaiNop.textContent = thongKe.so_bai_da_nop;
    tongSinhVien.textContent = `/ ${thongKe.tong_sinh_vien}`;
  }
}

/**
 * Format dữ liệu sinh viên từ API
 */
function formatStudentData(danhSach) {
  return danhSach.map(sv => {
    // Xác định trạng thái hiển thị
    let statusText = '';
    let markedStatus = '';
    
    switch(sv.trang_thai) {
      case 'chua_lam':
        statusText = 'Chưa làm';
        markedStatus = 'chưa nộp';
        break;
      case 'dang_lam':
        statusText = 'Đang làm';
        markedStatus = 'chưa nộp';
        break;
      case 'da_nop':
        statusText = 'Đã nộp';
        markedStatus = 'chưa chấm';
        break;
      case 'da_cham':
        statusText = 'Đã chấm';
        markedStatus = 'đã chấm';
        break;
    }
    
    // Format điểm
    const scoreText = sv.diem !== null ? `${sv.diem}/10` : '-';
    
    // Format thời gian nộp
    const timeText = sv.thoi_gian_nop ? formatDateTime(sv.thoi_gian_nop) : '-';
    
    return {
      id: sv.sinh_vien_id,
      name: sv.ho_ten,
      studentCode: sv.ma_sinh_vien,
      avatar: sv.anh_dai_dien || 'avatar0.png',
      status: statusText,
      score: scoreText,
      time: timeText,
      marked: markedStatus,
      trang_thai: sv.trang_thai
    };
  });
}

/**
 * Format ngày giờ
 */
function formatDateTime(dateString) {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  
  return `${day}/${month}/${year} ${hours}:${minutes}`;
}

/**
 * Hiển thị lỗi
 */
function showError(message) {
  console.error(message);
  // TODO: Có thể hiển thị toast notification ở đây
  alert(message);
}

// ==================== FILTER & PAGINATION ====================

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
    <div class="hs-1" data-marked="${student.marked}" data-student-id="${student.id}" style="cursor: pointer;">
      <div class="cell-4">
        <div class="box">
          <div class="custom-table-custom-cell">
            <div class="avatar" style="background: url(${student.avatar}) center/cover no-repeat;"></div>
            <div class="frame-1321316798">
              <div class="t-n-sinh-vi-n">${student.name}</div>
              <div class="m-sinh-vi-n">${student.studentCode}</div>
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
  
  // Thêm event listener cho từng dòng sinh viên
  document.querySelectorAll('.hs-1').forEach(row => {
    row.addEventListener('click', function() {
      const sinhVienId = this.dataset.studentId;
      window.location.href = `../HomeWork.html?bai_tap_id=${baiTapId}&sinh_vien_id=${sinhVienId}&lop_hoc_id=${lopHocId}`;
    });
  });

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

// ==================== INITIALIZATION ====================

/**
 * Khởi tạo khi DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
  // Lấy params từ URL
  if (getUrlParams()) {
    // Fetch dữ liệu từ API
    fetchWorkDashboardData();
  }
});

