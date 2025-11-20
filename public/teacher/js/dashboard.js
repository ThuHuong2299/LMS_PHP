/**
 * File: dashboard.js
 * Mục đích: Xử lý load và hiển thị dữ liệu động cho dashboard giảng viên
 * Ngày tạo: 14/11/2025
 */

// ========== API CONFIGURATION ==========
const DashboardAPI = {
  BASE_URL: '/backend/teacher/api',
  
  /**
   * Lấy thống kê dashboard
   * @returns {Promise<Object>}
   */
  async getStats() {
    try {
      const response = await fetch(`${this.BASE_URL}/dashboard-stats.php`, {
        method: 'GET',
        credentials: 'include', // Gửi kèm session cookie
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy thống kê');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getStats:', error);
      throw error;
    }
  },
  
  /**
   * Lấy hoạt động gần đây
   * @param {number} limit - Số lượng hoạt động
   * @returns {Promise<Array>}
   */
  async getActivities(limit = 10) {
    try {
      const response = await fetch(`${this.BASE_URL}/hoat-dong-gan-day.php?limit=${limit}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy hoạt động');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getActivities:', error);
      throw error;
    }
  }
};

// ========== LOAD THỐNG KÊ DASHBOARD ==========
async function loadDashboardStats() {
  try {
    // Hiển thị loading state (optional)
    showStatsLoading();
    
    const stats = await DashboardAPI.getStats();
    
    // Cập nhật UI với dữ liệu thực
    updateStatsUI(stats);
    
  } catch (error) {
    console.error('Lỗi load dashboard stats:', error);
    showStatsError(error.message);
  }
}

/**
 * Hiển thị loading state cho stats cards
 */
function showStatsLoading() {
  const statNumbers = document.querySelectorAll('.classes-p ._8, .student-p ._80, .test-p ._12, .notifies-p ._3');
  statNumbers.forEach(el => {
    el.textContent = '...';
    el.style.opacity = '0.5';
  });
}

/**
 * Cập nhật UI với dữ liệu thống kê
 */
function updateStatsUI(stats) {
  // 1. Lớp giảng dạy
  const lopElement = document.querySelector('.classes-p ._8');
  if (lopElement) {
    lopElement.textContent = stats.lop_giang_day || 0;
    lopElement.style.opacity = '1';
  }
  
  // 2. Sinh viên theo học
  const svElement = document.querySelector('.student-p ._80');
  if (svElement) {
    svElement.textContent = stats.sinh_vien_theo_hoc || 0;
    svElement.style.opacity = '1';
  }
  
  // 3. Bài chờ chấm
  const baiElement = document.querySelector('.test-p ._12');
  if (baiElement) {
    baiElement.textContent = stats.bai_cho_cham || 0;
    baiElement.style.opacity = '1';
  }
  
  // 4. Thông báo mới
  const tbElement = document.querySelector('.notifies-p ._3');
  if (tbElement) {
    tbElement.textContent = stats.thong_bao_moi || 0;
    tbElement.style.opacity = '1';
  }
}

/**
 * Hiển thị lỗi khi load stats
 */
function showStatsError(message) {
  console.error('Lỗi stats:', message);
  // Có thể hiển thị toast notification hoặc giữ nguyên giá trị cũ
}

// ========== LOAD HOẠT ĐỘNG GẦN ĐÂY ==========
async function loadRecentActivities() {
  try {
    // Hiển thị loading
    showActivitiesLoading();
    
    const activities = await DashboardAPI.getActivities(10);
    
    // Render hoạt động
    renderActivities(activities);
    
  } catch (error) {
    console.error('Lỗi load activities:', error);
    showActivitiesError(error.message);
  }
}

/**
 * Hiển thị loading cho timeline
 */
function showActivitiesLoading() {
  const container = document.querySelector('.notification');
  if (!container) return;
  
  // Giữ lại tiêu đề, xóa nội dung cũ
  const title = container.querySelector('.ho-t-ng-g-n-y');
  const titleHTML = title ? title.outerHTML : '<div class="ho-t-ng-g-n-y">Hoạt động gần đây</div>';
  
  container.innerHTML = titleHTML + '<div style="padding: 20px; text-align: center; color: #999;">Đang tải...</div>';
}

/**
 * Render danh sách hoạt động
 */
function renderActivities(activities) {
  const container = document.querySelector('.notification');
  if (!container) return;
  
  // Giữ lại tiêu đề
  const title = container.querySelector('.ho-t-ng-g-n-y');
  const titleHTML = title ? title.outerHTML : '<div class="ho-t-ng-g-n-y">Hoạt động gần đây</div>';
  
  // Xóa nội dung cũ
  container.innerHTML = titleHTML;
  
  // Nếu không có hoạt động
  if (!activities || activities.length === 0) {
    container.innerHTML += '<div style="padding: 20px; text-align: center; color: #999;">Chưa có hoạt động nào</div>';
    return;
  }
  
  // Render từng hoạt động
  activities.forEach((activity, index) => {
    const html = renderActivityItem(activity, index);
    container.insertAdjacentHTML('beforeend', html);
  });
}

/**
 * Render một hoạt động
 */
function renderActivityItem(activity, index) {
  const classNumber = (index % 4) + 1; // Rotate giữa noti-10, noti-20, noti-30, noti-40
  
  if (activity.loai === 'nop_bai') {
    // Sinh viên nộp bài
    return `
      <div class="noti-${classNumber}0">
        <div class="frame-noti-${classNumber}0">
          <img class="frame" src="${activity.anh_dai_dien || '/public/teacher/assets/frame0.svg'}" alt="${activity.ho_ten}" />
        </div>
        <div class="noti-pa">
          <div class="sinh-vi-n-ph-m-anh-t-n-p-b-i">
            <span>
              <span class="sinh-vi-n-ph-m-anh-t-n-p-b-i-span">${activity.ho_ten}</span>
              <span class="sinh-vi-n-ph-m-anh-t-n-p-b-i-span2">${activity.noi_dung}</span>
            </span>
          </div>
          <div class="_5-ph-t-tr-c">${activity.thoi_gian_hien_thi}</div>
        </div>
      </div>
    `;
  } else if (activity.loai === 'het_han_bai_tap') {
    // Bài tập hết hạn
    return `
      <div class="noti-${classNumber}0">
        <div class="frame-noti-${classNumber}0">
          <img class="exercise2" src="${activity.icon}" alt="Bài tập" />
        </div>
        <div class="noti-${classNumber}0-pa">
          <div class="b-i-t-p-ch-ng-3-h-t-h-n-n-p">
            ${activity.tieu_de_bai} ${activity.noi_dung}
          </div>
          <div class="_15-ph-t-tr-c">${activity.thoi_gian_hien_thi}</div>
        </div>
      </div>
    `;
  } else if (activity.loai === 'het_han_kiem_tra') {
    // Bài kiểm tra hết hạn
    return `
      <div class="noti-${classNumber}0">
        <div class="noti-${classNumber}02">
          <img class="exam2" src="${activity.icon}" alt="Bài kiểm tra" />
        </div>
        <div class="noti-${classNumber}0-pa">
          <div class="b-i-ki-m-ch-ng-3-h-t-h-n-n-p">
            ${activity.tieu_de_bai} ${activity.noi_dung}
          </div>
          <div class="_25-ph-t-tr-c">${activity.thoi_gian_hien_thi}</div>
        </div>
      </div>
    `;
  }
  
  // Default fallback
  return '';
}

/**
 * Hiển thị lỗi khi load activities
 */
function showActivitiesError(message) {
  const container = document.querySelector('.notification');
  if (!container) return;
  
  const title = container.querySelector('.ho-t-ng-g-n-y');
  const titleHTML = title ? title.outerHTML : '<div class="ho-t-ng-g-n-y">Hoạt động gần đây</div>';
  
  container.innerHTML = titleHTML + `<div style="padding: 20px; text-align: center; color: #e74c3c;">Lỗi: ${message}</div>`;
}

// ========== AUTO REFRESH ==========
let statsRefreshInterval;
let activitiesRefreshInterval;

/**
 * Bật auto refresh
 */
function startAutoRefresh() {
  // Refresh stats mỗi 30 giây
  statsRefreshInterval = setInterval(() => {
    loadDashboardStats();
  }, 30000);
  
  // Refresh activities mỗi 60 giây
  activitiesRefreshInterval = setInterval(() => {
    loadRecentActivities();
  }, 60000);
}

/**
 * Tắt auto refresh (khi rời khỏi trang)
 */
function stopAutoRefresh() {
  if (statsRefreshInterval) {
    clearInterval(statsRefreshInterval);
  }
  if (activitiesRefreshInterval) {
    clearInterval(activitiesRefreshInterval);
  }
}

// ========== INITIALIZATION ==========
/**
 * Khởi tạo khi DOM ready
 */
function initDashboard() {
  // Load dữ liệu ban đầu
  loadDashboardStats();
  loadRecentActivities();
  
  // Bật auto refresh
  startAutoRefresh();
  
  // Tắt auto refresh khi rời trang
  window.addEventListener('beforeunload', stopAutoRefresh);
}

// Chạy khi DOM đã load xong
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDashboard);
} else {
  initDashboard();
}

// Export để có thể gọi từ bên ngoài nếu cần
window.DashboardManager = {
  refresh: () => {
    loadDashboardStats();
    loadRecentActivities();
  },
  stopAutoRefresh
};
