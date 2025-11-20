/**
 * File: classroom.js
 * Mục đích: Xử lý load và hiển thị danh sách lớp học động
 * Ngày tạo: 14/11/2025
 */

import { ClassroomAPI } from './cau-hinh/api-classroom.js';

// ========== LOAD DANH SÁCH LỚP HỌC ==========
async function loadDanhSachLopHoc() {
  try {
    // Hiển thị loading state
    showLoadingState();
    
    // Gọi API lấy danh sách lớp
    const danhSachLop = await ClassroomAPI.getDanhSachLopHoc();
    
    // Render danh sách lớp học
    renderDanhSachLop(danhSachLop);
    
  } catch (error) {
    console.error('Lỗi load danh sách lớp học:', error);
    showErrorState(error.message);
  }
}

/**
 * Hiển thị loading state
 */
function showLoadingState() {
  const container = document.querySelector('.classes-grid');
  if (!container) return;
  
  container.innerHTML = `
    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
      <div style="font-size: 24px; color: #999;">Đang tải dữ liệu...</div>
    </div>
  `;
}

/**
 * Hiển thị error state
 */
function showErrorState(message) {
  const container = document.querySelector('.classes-grid');
  if (!container) return;
  
  container.innerHTML = `
    <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
      <div style="font-size: 20px; color: #ff6b6b; margin-bottom: 10px;"> Có lỗi xảy ra</div>
      <div style="font-size: 16px; color: #666;">${message}</div>
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

/**
 * Render danh sách lớp học
 */
function renderDanhSachLop(danhSachLop) {
  const container = document.querySelector('.classes-grid');
  if (!container) return;
  
  // Nếu không có lớp nào
  if (!danhSachLop || danhSachLop.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <div style="font-size: 20px; color: #999; margin-bottom: 10px;"> Chưa có lớp học nào</div>
        <div style="font-size: 16px; color: #666;">Bạn chưa được phân công giảng dạy lớp nào</div>
      </div>
    `;
    return;
  }
  
  // Render từng lớp học
  container.innerHTML = danhSachLop.map((lop, index) => {
    // Chọn màu vector ngẫu nhiên (xoay vòng giữa 2 màu)
    const vectorColor = index % 2 === 0 ? '0' : '1';
    
    return `
      <div class="class-${index + 1}" onclick="window.location.href='/public/teacher/ClassroomInfo.html?id=${lop.id}'">
        <div class="c-c-h-th-ng-th-ng-tin-ph-bi-n-trong-doanh-nghi-p">
          ${escapeHtml(lop.ten_mon_hoc)}
        </div>
        <div class="_213-e-cit-2320-09">${escapeHtml(lop.ma_lop_hoc)}</div>
        <img class="vector-43" src="vector-43${vectorColor}.svg" />
        <div class="class-${index + 1}-info">
          <div class="class-${index + 1}-student">
            <div class="frame">
              <img class="frame2" src="frame${index % 2 === 0 ? '1' : '5'}.svg" />
            </div>
            <div class="_36-sinh-vi-n">${lop.so_sinh_vien} Sinh viên</div>
          </div>
          <div class="class-${index + 1}-homework">
            <div class="frame">
              <img class="exercise" src="exercise${vectorColor}.svg" />
            </div>
            <div class="_12-b-i-t-p">${lop.so_bai_tap} Bài tập</div>
          </div>
          <div class="class-${index + 1}-test">
            <div class="frame">
              <img class="exam" src="exam${vectorColor}.svg" />
            </div>
            <div class="_6-b-i-ki-m-tra">${lop.so_bai_kiem_tra} Bài kiểm tra</div>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

/**
 * Escape HTML để tránh XSS
 */
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// ========== KHỞI TẠO KHI DOM READY ==========
document.addEventListener('DOMContentLoaded', function() {
  loadDanhSachLopHoc();
});

// Export để có thể reload từ nơi khác
export { loadDanhSachLopHoc };
