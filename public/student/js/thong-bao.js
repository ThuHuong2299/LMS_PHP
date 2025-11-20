/**
 * File: thong-bao.js
 * Mục đích: Load và hiển thị danh sách thông báo từ API
 */

class ThongBaoManager {
  constructor() {
    this.container = document.getElementById('notifications-list');
    this.init();
  }

  async init() {
    await this.loadThongBao();
  }

  async loadThongBao() {
    try {
      const response = await fetch('../../backend/student/api/thong-bao-trong.php');
      const data = await response.json();

      console.log('API Response:', data);

      if (data.thanh_cong && data.du_lieu) {
        const thongBaoList = data.du_lieu.thong_bao_list || [];
        this.renderThongBao(thongBaoList);
      } else {
        throw new Error(data.thong_bao || 'Không thể load thông báo');
      }
    } catch (error) {
      console.error('Lỗi load thông báo:', error);
      this.showError('Không thể tải thông báo: ' + error.message);
    }
  }

  renderThongBao(thongBaoList) {
    if (!this.container) return;

    if (thongBaoList.length === 0) {
      this.container.innerHTML = `
        <div style="padding: 40px; text-align: center; color: #666;">
          <p>Không có thông báo nào.</p>
        </div>
      `;
      return;
    }

    this.container.innerHTML = '';
    thongBaoList.forEach(tb => {
      this.container.appendChild(this.createNotificationItem(tb));
    });
  }

  createNotificationItem(tb) {
    const div = document.createElement('div');
    div.className = 'notification-item';

    // Xác định icon dựa trên loại
    const iconMap = {
      'bai_tap': '/public/student/assets/exercise.svg',
      'bai_kiem_tra': '/public/student/assets/exam.svg',
      'thong_bao': '/public/student/assets/video-icon.svg'
    };

    const icon = iconMap[tb.loai] || '/public/student/assets/exercise.svg';

    // Format thời gian hiển thị
    const timeText = this.formatTimeText(tb);

    div.innerHTML = `
      <div class="notification-content">
        <div class="notification-icon">
          <img src="${icon}" alt="Icon">
        </div>
        <div class="notification-text">
          <div class="notification-title">${this.escapeHtml(tb.tieu_de)}</div>
          <div class="notification-time">${timeText}</div>
        </div>
      </div>
      <div class="notification-author">${this.escapeHtml(tb.ten_mon_hoc)}</div>
    `;

    return div;
  }

  formatTimeText(tb) {
    if (tb.loai === 'bai_tap' || tb.loai === 'bai_kiem_tra') {
      const ngay = parseInt(tb.con_bao_nhieu_ngay);
      if (ngay === 0) {
        return 'Hôm nay!';
      } else if (ngay === 1) {
        return 'Còn 1 ngày nữa!';
      } else {
        return `Còn ${ngay} ngày nữa!`;
      }
    } else if (tb.loai === 'thong_bao') {
      // Tính khoảng cách thời gian
      const now = new Date();
      const deadline = new Date(tb.deadline);
      const diff = now - deadline;
      const hours = Math.floor(diff / (1000 * 60 * 60));
      const days = Math.floor(hours / 24);

      if (hours < 1) {
        return 'Vừa mới đây';
      } else if (hours < 24) {
        return `${hours} giờ trước`;
      } else if (days === 1) {
        return 'Hôm qua';
      } else {
        return `${days} ngày trước`;
      }
    }
    return '';
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  showError(message) {
    if (this.container) {
      this.container.innerHTML = `
        <div style="padding: 40px; text-align: center; color: #dc3545;">
          <p>${message}</p>
        </div>
      `;
    }
  }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
  new ThongBaoManager();
});
