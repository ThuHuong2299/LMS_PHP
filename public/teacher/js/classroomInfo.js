/**
 * File: classroominfo.js
 * Mục đích: Xử lý load và hiển thị chi tiết lớp học
 * Ngày tạo: 14/11/2025
 */

import { ClassroomAPI } from './cau-hinh/api-classroom.js';

// Biến toàn cục
let currentLopHocId = null;
let currentTabData = {};
let currentPage = 1; // Trang hiện tại cho tab sinh viên

// ========== KHỞI TẠO ==========
document.addEventListener('DOMContentLoaded', function() {
  // Lấy ID lớp từ URL
  const urlParams = new URLSearchParams(window.location.search);
  currentLopHocId = parseInt(urlParams.get('id'));
  
  if (!currentLopHocId || isNaN(currentLopHocId)) {
    showError('Không tìm thấy ID lớp học');
    return;
  }
  
  // Load tab đầu tiên (Bài giảng)
  loadTabBaiGiang();
  
  // Xử lý chuyển tab
  initializeTabs();
});

// ========== XỬ LÝ TAB ==========
function initializeTabs() {
  const tabs = document.querySelectorAll('.tab');
  
  tabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Remove active
      tabs.forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
      
      // Add active
      this.classList.add('active');
      const tabName = this.dataset.tab;
      const section = document.getElementById(tabName + '-section');
      if (section) section.classList.add('active');
      
      // Load data cho tab tương ứng
      loadTabData(tabName);
    });
  });
}

function loadTabData(tabName) {
  switch(tabName) {
    case 'lessons':
      if (!currentTabData.lessons) loadTabBaiGiang();
      break;
    case 'assignments':
      if (!currentTabData.assignments) loadTabBaiTap();
      break;
    case 'exams':
      if (!currentTabData.exams) loadTabBaiKiemTra();
      break;
    case 'notifications':
      if (!currentTabData.notifications) loadTabThongBao();
      break;
    case 'documents':
      // Bỏ qua
      console.log('Tab Tài liệu - tạm thời không phát triển');
      break;
    case 'students':
      if (!currentTabData.students) loadTabSinhVien();
      break;
  }
}

// ========== TAB BÀI GIẢNG ==========
async function loadTabBaiGiang() {
  try {
    showLoadingState('lessons-section');
    
    // Gọi API
    const data = await ClassroomAPI.getBaiGiangLopHoc(currentLopHocId);
    
    // Lưu cache
    currentTabData.lessons = data;
    
    // Cập nhật thông tin lớp (header)
    updateCourseInfo(data.thong_tin_lop);
    
    // Render bài giảng
    renderBaiGiang(data.chuong_va_bai_giang);
    
  } catch (error) {
    console.error('Lỗi load bài giảng:', error);
    showErrorState('lessons-section', error.message);
  }
}

function updateCourseInfo(thongTinLop) {
  // Cập nhật breadcrumb
  const breadcrumb = document.getElementById('contentBreadcrumb');
  if (breadcrumb) {
    breadcrumb.innerHTML = `
      <a href="/public/teacher/Classroom.html" style="color: inherit; text-decoration: none;">Lớp học</a>
      <span class="separator">›</span>
      <span>Chi tiết lớp</span>
    `;
  }
  
  // Cập nhật tên môn học và mã lớp
  const courseTitle = document.querySelector('.course-title');
  const courseCode = document.querySelector('.course-code');
  
  if (courseTitle) courseTitle.textContent = thongTinLop.ten_mon_hoc;
  if (courseCode) courseCode.textContent = thongTinLop.ma_lop_hoc;
}

function renderBaiGiang(chuongVaBaiGiang) {
  const container = document.getElementById('lessons-section');
  if (!container) return;
  
  // Clear và thêm header
  container.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Danh sách bài giảng</h1>
    </div>
    <div id="chapters-container"></div>
  `;
  
  const chaptersContainer = container.querySelector('#chapters-container');
  
  if (!chuongVaBaiGiang || chuongVaBaiGiang.length === 0) {
    chaptersContainer.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Chưa có bài giảng nào</p>
      </div>
    `;
    return;
  }
  
  // Render từng chương
  chuongVaBaiGiang.forEach(chuong => {
    const chapterDiv = document.createElement('div');
    chapterDiv.className = 'chapter';
    
    const lessonsHTML = chuong.bai_giang.map(baiGiang => `
      <div class="lesson-item">
        <div class="lesson-thumbnail"></div>
        <div class="lesson-info">
          <div class="lesson-title">${escapeHtml(baiGiang.tieu_de)}</div>
          <div class="lesson-desc">${baiGiang.duong_dan_video ? 'Video bài giảng' : 'Nội dung bài giảng'}</div>
        </div>
      </div>
    `).join('');
    
    chapterDiv.innerHTML = `
      <div class="chapter-header" onclick="toggleChapter(this)">
        <span>Chương ${chuong.so_thu_tu_chuong}: ${escapeHtml(chuong.ten_chuong)}</span>
        <img class="chapter-arrow" src="chapter-arrow.svg" alt="arrow">
      </div>
      <div class="chapter-content open">
        ${lessonsHTML || '<p style="padding: 20px; color: #999;">Chương chưa có bài giảng</p>'}
      </div>
    `;
    
    chaptersContainer.appendChild(chapterDiv);
  });
}

// ========== UTILITY FUNCTIONS ==========
function showLoadingState(sectionId) {
  const section = document.getElementById(sectionId);
  if (!section) return;
  
  section.innerHTML = `
    <div style="text-align: center; padding: 60px;">
      <div style="font-size: 20px; color: #999;">Đang tải dữ liệu...</div>
    </div>
  `;
}

function showErrorState(sectionId, message) {
  const section = document.getElementById(sectionId);
  if (!section) return;
  
  section.innerHTML = `
    <div style="text-align: center; padding: 60px;">
      <div style="font-size: 20px; color: #ff6b6b; margin-bottom: 10px;">⚠️ Có lỗi xảy ra</div>
      <div style="font-size: 16px; color: #666;">${escapeHtml(message)}</div>
      <button onclick="location.reload()" style="
        margin-top: 20px;
        padding: 10px 24px;
        background: #3293F9;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
      ">Thử lại</button>
    </div>
  `;
}

function showError(message) {
  alert(message);
  window.location.href = '/public/teacher/Classroom.html';
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// ========== TAB BÀI TẬP ==========
async function loadTabBaiTap() {
  try {
    showLoadingState('assignments-section');
    
    // Gọi API
    const data = await ClassroomAPI.getBaiTapLopHoc(currentLopHocId);
    
    // Lưu cache
    currentTabData.assignments = data;
    
    // Render bài tập
    renderBaiTap(data);
    
  } catch (error) {
    console.error('Lỗi load bài tập:', error);
    showErrorState('assignments-section', error.message);
  }
}

function renderBaiTap(danhSachBaiTap) {
  const container = document.getElementById('assignments-section');
  if (!container) return;
  
  // Clear và thêm header
  container.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Danh sách bài tập</h1>
      <button class="btn-add" onclick="alert('Chức năng thêm mới sẽ phát triển sau')">+ Thêm mới</button>
    </div>
    <div id="assignments-container"></div>
  `;
  
  const assignmentsContainer = container.querySelector('#assignments-container');
  
  if (!danhSachBaiTap || danhSachBaiTap.length === 0) {
    assignmentsContainer.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Chưa có bài tập nào</p>
      </div>
    `;
    return;
  }
  
  // Render từng bài tập
  danhSachBaiTap.forEach(baiTap => {
    const cardDiv = document.createElement('div');
    cardDiv.className = 'assignment-card';
    cardDiv.style.cursor = 'pointer';
    cardDiv.onclick = () => window.location.href = `/public/teacher/WorkDashBoard.html?id=${baiTap.id}`;
    
    // Format thông tin chương và bài giảng
    let metaText = '';
    if (baiTap.chuong) {
      metaText = `Chương ${baiTap.chuong.so_thu_tu}`;
      if (baiTap.bai_giang) {
        metaText += ` - ${baiTap.bai_giang.so_thu_tu_bai}. ${baiTap.bai_giang.tieu_de}`;
      } else {
        metaText += ` - ${baiTap.chuong.ten_chuong}`;
      }
    } else {
      metaText = 'Bài tập tổng hợp';
    }
    
    // Format hạn nộp
    const hanNop = formatDeadline(baiTap.han_nop);
    
    cardDiv.innerHTML = `
      <div class="assignment-details">
        <div class="assignment-meta">${escapeHtml(metaText)}</div>
        <div class="assignment-title">${escapeHtml(baiTap.tieu_de)}</div>
        <div class="assignment-stats">
          <span>${baiTap.so_cau_hoi} câu</span>
          <div class="dot"></div>
          <span>${baiTap.so_sinh_vien_da_nop}/${baiTap.tong_sinh_vien} lớp đã làm</span>
        </div>
      </div>
      <div class="deadline">
        <div class="deadline-label">Hạn nộp</div>
        <div class="deadline-date">${hanNop}</div>
      </div>
    `;
    
    assignmentsContainer.appendChild(cardDiv);
  });
}

function formatDeadline(hanNopStr) {
  if (!hanNopStr) return 'Không có hạn';
  
  try {
    const date = new Date(hanNopStr);
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    
    return `${hours}:${minutes} ${day}/${month}/${year}`;
  } catch (e) {
    return hanNopStr;
  }
}

// Expose toggleChapter globally cho onclick
window.toggleChapter = function(header) {
  const arrow = header.querySelector('.chapter-arrow');
  const content = header.nextElementSibling;
  if (arrow) arrow.classList.toggle('open');
  if (content) content.classList.toggle('open');
};

// ========== TAB BÀI KIỂM TRA ==========
async function loadTabBaiKiemTra() {
  try {
    showLoadingState('exams-section');
    
    // Gọi API
    const data = await ClassroomAPI.getBaiKiemTraLopHoc(currentLopHocId);
    
    // Lưu cache
    currentTabData.exams = data;
    
    // Render bài kiểm tra
    renderBaiKiemTra(data);
    
  } catch (error) {
    console.error('Lỗi load bài kiểm tra:', error);
    showErrorState('exams-section', error.message);
  }
}

function renderBaiKiemTra(danhSachBaiKiemTra) {
  const container = document.getElementById('exams-section');
  if (!container) return;
  
  // Clear và thêm header
  container.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Danh sách bài kiểm tra</h1>
      <button class="btn-add" onclick="alert('Chức năng thêm mới sẽ phát triển sau')">+ Thêm mới</button>
    </div>
    <div id="exams-container"></div>
  `;
  
  const examsContainer = container.querySelector('#exams-container');
  
  if (!danhSachBaiKiemTra || danhSachBaiKiemTra.length === 0) {
    examsContainer.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Chưa có bài kiểm tra nào</p>
      </div>
    `;
    return;
  }
  
  // Render từng bài kiểm tra
  danhSachBaiKiemTra.forEach(baiKiemTra => {
    const cardDiv = document.createElement('div');
    cardDiv.className = 'assignment-card';
    cardDiv.style.cursor = 'pointer';
    // TODO: Thêm link đến trang chi tiết bài kiểm tra khi có
    cardDiv.onclick = () => alert('Chức năng xem chi tiết bài kiểm tra sẽ phát triển sau');
    
    // Format thông tin chương
    let metaText = '';
    if (baiKiemTra.chuong) {
      metaText = `Chương ${baiKiemTra.chuong.so_thu_tu} - ${baiKiemTra.chuong.ten_chuong}`;
    } else {
      metaText = 'Bài kiểm tra tổng hợp';
    }
    
    // Format thời gian bắt đầu
    const thoiGianBatDau = formatDeadline(baiKiemTra.thoi_gian_bat_dau);
    
    cardDiv.innerHTML = `
      <div class="assignment-details">
        <div class="assignment-meta">${escapeHtml(metaText)}</div>
        <div class="assignment-title">${escapeHtml(baiKiemTra.tieu_de)}</div>
        <div class="assignment-stats">
          <span>${baiKiemTra.so_cau_hoi} câu</span>
          <div class="dot"></div>
          <span>${baiKiemTra.so_sinh_vien_da_lam}/${baiKiemTra.tong_sinh_vien} lớp đã làm</span>
        </div>
      </div>
      <div class="deadline">
        <div class="deadline-label">Thời gian thi</div>
        <div class="deadline-date">${thoiGianBatDau}</div>
      </div>
    `;
    
    examsContainer.appendChild(cardDiv);
  });
}

// ========== TAB THÔNG BÁO ==========
async function loadTabThongBao() {
  try {
    showLoadingState('notifications-section');
    
    // Gọi API
    const data = await ClassroomAPI.getThongBaoLopHoc(currentLopHocId);
    
    // Lưu cache
    currentTabData.notifications = data;
    
    // Render thông báo
    renderThongBao(data);
    
  } catch (error) {
    console.error('Lỗi load thông báo:', error);
    showErrorState('notifications-section', error.message);
  }
}

function renderThongBao(danhSachThongBao) {
  const container = document.getElementById('notifications-section');
  if (!container) return;
  
  // Clear và thêm header
  container.innerHTML = `
    <div class="page-header">
      <h1 class="page-title">Danh sách thông báo</h1>
      <button class="btn-add" onclick="alert('Chức năng thêm mới sẽ phát triển sau')">+ Thêm mới</button>
    </div>
    <div id="notificationsList"></div>
  `;
  
  const notificationsContainer = container.querySelector('#notificationsList');
  
  if (!danhSachThongBao || danhSachThongBao.length === 0) {
    notificationsContainer.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Chưa có thông báo nào</p>
      </div>
    `;
    return;
  }
  
  // Render từng thông báo
  danhSachThongBao.forEach(thongBao => {
    const cardDiv = document.createElement('div');
    cardDiv.className = 'notification-card';
    
    // Tính thời gian đã qua
    const thoiGian = formatTimeAgo(thongBao.thoi_gian_gui);
    
    cardDiv.innerHTML = `
      <div class="notification-left">
        <div class="notification-icon">
          <img src="noti.svg" alt="icon" />
        </div>
        <div class="notification-content">
          <div class="notification-text">
            <strong>${escapeHtml(thongBao.tieu_de)}</strong><br>
            ${escapeHtml(thongBao.noi_dung)}
          </div>
        </div>
      </div>
      <div class="notification-right">
        <div class="notification-menu" onclick="deleteNotificationAlert(${thongBao.id})">⋮</div>
        <div class="notification-time">${thoiGian}</div>
      </div>
    `;
    
    notificationsContainer.appendChild(cardDiv);
  });
}

function formatTimeAgo(thoiGianStr) {
  if (!thoiGianStr) return 'Không rõ';
  
  try {
    const now = new Date();
    const time = new Date(thoiGianStr);
    const diffMs = now - time;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) return 'Vừa xong';
    if (diffMin < 60) return `${diffMin} phút trước`;
    if (diffHour < 24) return `${diffHour} giờ trước`;
    if (diffDay < 7) return `${diffDay} ngày trước`;
    
    // Hiển thị ngày đầy đủ
    const day = time.getDate().toString().padStart(2, '0');
    const month = (time.getMonth() + 1).toString().padStart(2, '0');
    const year = time.getFullYear();
    const hours = time.getHours().toString().padStart(2, '0');
    const minutes = time.getMinutes().toString().padStart(2, '0');
    
    return `${day}/${month}/${year} ${hours}:${minutes}`;
  } catch (e) {
    return thoiGianStr;
  }
}

function deleteNotificationAlert(id) {
  alert('Chức năng xóa thông báo sẽ phát triển sau');
}

// ========== TAB SINH VIÊN ==========
async function loadTabSinhVien(page = 1) {
  try {
    showLoadingState('students-section');
    
    // Gọi API với phân trang
    const data = await ClassroomAPI.getSinhVienLopHoc(currentLopHocId, page, 5);
    
    // Lưu cache
    currentTabData.students = data;
    currentPage = page;
    
    // Render sinh viên
    renderSinhVien(data);
    
  } catch (error) {
    console.error('Lỗi load sinh viên:', error);
    showErrorState('students-section', error.message);
  }
}

function renderSinhVien(data) {
  const section = document.getElementById('students-section');
  if (!section) return;
  
  const { sinh_vien, pagination } = data;
  
  if (!sinh_vien || sinh_vien.length === 0) {
    section.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Chưa có sinh viên nào trong lớp này</p>
      </div>
    `;
    return;
  }
  
  // Render danh sách sinh viên
  const sinhVienHtml = sinh_vien.map(sv => {
    const progressColor = sv.tien_do >= 70 ? '#4caf50' : sv.tien_do >= 40 ? '#ff9800' : '#f44336';
    const anhDaiDien = sv.anh_dai_dien || '/public/student/CSS/avatar-sv.webp';
    
    return `
      <div class="student-item" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #eee;">
        <div class="student-info" style="display: flex; align-items: center; gap: 15px;">
          <img src="${escapeHtml(anhDaiDien)}" alt="${escapeHtml(sv.ho_ten)}" 
               style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
          <div class="student-details">
            <h4 style="margin: 0; font-size: 16px;">${escapeHtml(sv.ho_ten)}</h4>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">${escapeHtml(sv.ma_nguoi_dung)}</p>
          </div>
        </div>
        <div class="student-progress" style="text-align: right; min-width: 200px;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-size: 14px; color: #666;">Tiến độ</span>
            <span style="font-size: 16px; font-weight: bold; color: ${progressColor};">${sv.tien_do}%</span>
          </div>
          <div class="progress-bar" style="width: 200px; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden;">
            <div class="progress-fill" style="width: ${sv.tien_do}%; height: 100%; background-color: ${progressColor}; transition: width 0.3s;"></div>
          </div>
          <p style="margin: 8px 0 0 0; font-size: 12px; color: #999;">Hoạt động: ${escapeHtml(sv.last_updated)}</p>
        </div>
      </div>
    `;
  }).join('');
  
  // Render pagination
  const paginationHtml = `
    <div class="pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 2px solid #eee;">
      <button 
        class="pagination-btn" 
        id="prev-page" 
        ${pagination.trang_hien_tai <= 1 ? 'disabled' : ''}
        style="padding: 8px 16px; border: 1px solid #ddd; background: ${pagination.trang_hien_tai <= 1 ? '#f5f5f5' : '#fff'}; 
               cursor: ${pagination.trang_hien_tai <= 1 ? 'not-allowed' : 'pointer'}; border-radius: 4px;"
      >
        ← Trước
      </button>
      <span class="pagination-info" style="font-size: 14px; color: #666;">
        ${pagination.bat_dau}-${pagination.ket_thuc} của ${pagination.tong_sinh_vien}
      </span>
      <button 
        class="pagination-btn" 
        id="next-page" 
        ${pagination.trang_hien_tai >= pagination.tong_trang ? 'disabled' : ''}
        style="padding: 8px 16px; border: 1px solid #ddd; background: ${pagination.trang_hien_tai >= pagination.tong_trang ? '#f5f5f5' : '#fff'}; 
               cursor: ${pagination.trang_hien_tai >= pagination.tong_trang ? 'not-allowed' : 'pointer'}; border-radius: 4px;"
      >
        Sau →
      </button>
    </div>
  `;
  
  section.innerHTML = `
    <div class="students-container">
      ${sinhVienHtml}
    </div>
    ${paginationHtml}
  `;
  
  // Thêm event listeners cho pagination
  const prevBtn = document.getElementById('prev-page');
  const nextBtn = document.getElementById('next-page');
  
  if (prevBtn && pagination.trang_hien_tai > 1) {
    prevBtn.addEventListener('click', () => loadTabSinhVien(currentPage - 1));
  }
  
  if (nextBtn && pagination.trang_hien_tai < pagination.tong_trang) {
    nextBtn.addEventListener('click', () => loadTabSinhVien(currentPage + 1));
  }
}

// Export functions
export { loadTabBaiGiang, loadTabBaiTap, loadTabBaiKiemTra, loadTabThongBao, loadTabSinhVien, loadTabData };
