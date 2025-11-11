/**
 * MAIN.JS - ENTRY POINT CỦA ỨNG DỤNG
 * File này được import vào tất cả các trang HTML
 * Nhiệm vụ: Khởi tạo các components và quản lý application state
 */

import SidebarManager from './components/sidebar.js';
import NavbarManager from './components/navbar.js';
import { STORAGE_KEYS, TIMINGS } from './config/constants.js';

/**
 * Class chính quản lý toàn bộ ứng dụng
 */
class App {
  constructor() {
    // Khởi tạo các manager components
    this.sidebarManager = new SidebarManager();
    this.navbarManager = new NavbarManager();
    
    // State của app
    this.state = {
      daKhoiTao: false,
      dangTaiLai: false
    };
  }

  /**
   * Khởi tạo ứng dụng - Entry point chính
   */
  async khoiTao() {
    console.log('🚀 Learn Lab đang khởi động...');
    console.log(`📅 Thời gian: ${new Date().toLocaleString('vi-VN')}`);
    
    try {
      // 1. Kiểm tra đăng nhập
      // this.kiemTraDangNhap(); // Tạm comment để test
      
      // 2. Khởi tạo sidebar & navbar
      await Promise.all([
        this.sidebarManager.khoiTao(),
        this.navbarManager.khoiTao()
      ]);
      
      // 3. Setup event listeners chung
      this.ganSuKienChung();
      
      // 4. Khôi phục trạng thái UI
      this.khoiPhucTrangThaiUI();
      
      // 5. Đánh dấu đã khởi tạo xong
      this.state.daKhoiTao = true;
      
      console.log('✅ Learn Lab khởi động thành công!');
      
      // Dispatch event để các module khác biết app đã ready
      window.dispatchEvent(new Event('app-ready'));
      
    } catch (loi) {
      console.error('❌ Lỗi khởi tạo ứng dụng:', loi);
      this.hienThiLoiKhoiTao(loi);
    }
  }

  /**
   * Kiểm tra người dùng đã đăng nhập chưa
   */
  kiemTraDangNhap() {
    const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
    const tokenExpiry = localStorage.getItem(STORAGE_KEYS.TOKEN_EXPIRY);
    
    if (!token) {
      console.warn('⚠️ Chưa đăng nhập - chuyển về trang login');
      this.chuyenVeTrangDangNhap();
      return false;
    }
    
    // Kiểm tra token hết hạn
    if (tokenExpiry) {
      const expiry = parseInt(tokenExpiry);
      if (Date.now() > expiry) {
        console.warn('⚠️ Phiên đăng nhập hết hạn');
        this.chuyenVeTrangDangNhap();
        return false;
      }
    }
    
    console.log('✓ Đã đăng nhập');
    return true;
  }

  /**
   * Chuyển về trang đăng nhập
   */
  chuyenVeTrangDangNhap() {
    // Lưu URL hiện tại để redirect lại sau khi login
    sessionStorage.setItem('redirectAfterLogin', window.location.pathname);
    
    // Redirect
    window.location.href = '../Login.teacher.html';
  }

  /**
   * Gắn các event listeners chung cho toàn app
   */
  ganSuKienChung() {
    // Xử lý lỗi global
    window.addEventListener('error', (e) => {
      console.error('🚨 Lỗi global:', e.error);
    });

    // Xử lý unhandled promise rejections
    window.addEventListener('unhandledrejection', (e) => {
      console.error('🚨 Promise rejection:', e.reason);
    });

    // Xử lý trước khi unload (đóng tab/thoát trang)
    window.addEventListener('beforeunload', (e) => {
      // Có thể kiểm tra nếu có thay đổi chưa lưu
      const coThayDoiChuaLuu = this.kiemTraThayDoiChuaLuu();
      if (coThayDoiChuaLuu) {
        e.preventDefault();
        e.returnValue = 'Bạn có thay đổi chưa lưu. Bạn có muốn rời khỏi trang?';
      }
    });

    // Lắng nghe sự kiện sidebar toggle
    window.addEventListener('sidebar-toggle', (e) => {
      console.log('Sidebar toggle:', e.detail);
      // Có thể điều chỉnh layout của main content ở đây
      this.dieuChinhLayoutKhiSidebarToggle(e.detail.collapsed);
    });

    // Kiểm tra session định kỳ
    this.batDauKiemTraSession();
  }

  /**
   * Điều chỉnh layout khi sidebar toggle
   * @param {boolean} collapsed - Sidebar có đang thu gọn không
   */
  dieuChinhLayoutKhiSidebarToggle(collapsed) {
    // CSS đã tự động xử lý margin-left của body qua :has() selector
    // Không cần set inline style để tránh xung đột với CSS
    console.log(`Layout đã tự động điều chỉnh theo sidebar: ${collapsed ? 'thu gọn' : 'mở rộng'}`);
  }

  /**
   * Bắt đầu kiểm tra session định kỳ
   */
  batDauKiemTraSession() {
    setInterval(() => {
      const tokenExpiry = localStorage.getItem(STORAGE_KEYS.TOKEN_EXPIRY);
      if (tokenExpiry) {
        const expiry = parseInt(tokenExpiry);
        const conLai = expiry - Date.now();
        
        // Nếu còn 5 phút là hết hạn
        if (conLai > 0 && conLai < 5 * 60 * 1000) {
          console.warn('⚠️ Phiên đăng nhập sắp hết hạn');
          // TODO: Hiển thị thông báo cho user
        }
        
        // Nếu đã hết hạn
        if (conLai <= 0) {
          console.warn('⚠️ Phiên đã hết hạn');
          this.chuyenVeTrangDangNhap();
        }
      }
    }, TIMINGS.SESSION_CHECK_INTERVAL);
  }

  /**
   * Khôi phục trạng thái UI từ lần trước
   */
  khoiPhucTrangThaiUI() {
    // Khôi phục theme (nếu có)
    const theme = localStorage.getItem(STORAGE_KEYS.THEME);
    if (theme) {
      document.body.classList.add(`theme-${theme}`);
    }

    // Khôi phục scroll position (nếu cần)
    const scrollPos = sessionStorage.getItem('scrollPosition');
    if (scrollPos) {
      window.scrollTo(0, parseInt(scrollPos));
    }
  }

  /**
   * Lưu scroll position
   */
  luuScrollPosition() {
    sessionStorage.setItem('scrollPosition', window.scrollY.toString());
  }

  /**
   * Kiểm tra có thay đổi chưa lưu không
   * @returns {boolean}
   */
  kiemTraThayDoiChuaLuu() {
    // TODO: Implement logic kiểm tra form dirty
    return false;
  }

  /**
   * Hiển thị lỗi khởi tạo cho user
   * @param {Error} loi - Error object
   */
  hienThiLoiKhoiTao(loi) {
    const body = document.body;
    body.innerHTML = `
      <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: 'Be Vietnam Pro', sans-serif;
        padding: 20px;
        text-align: center;
      ">
        <h1 style="color: #F44336; margin-bottom: 20px;">⚠️ Lỗi khởi tạo</h1>
        <p style="color: #666; margin-bottom: 20px;">
          Xin lỗi, đã có lỗi xảy ra khi khởi động ứng dụng.
        </p>
        <code style="
          background: #f5f5f5;
          padding: 10px 20px;
          border-radius: 8px;
          color: #d32f2f;
          margin-bottom: 20px;
        ">
          ${loi.message}
        </code>
        <button onclick="window.location.reload()" style="
          background: #3293F9;
          color: white;
          border: none;
          padding: 12px 24px;
          border-radius: 8px;
          font-size: 16px;
          cursor: pointer;
        ">
          Tải lại trang
        </button>
      </div>
    `;
  }

  /**
   * Refresh toàn bộ app
   */
  async refresh() {
    if (this.state.dangTaiLai) {
      console.warn('App đang trong quá trình tải lại');
      return;
    }

    this.state.dangTaiLai = true;
    
    try {
      // Refresh sidebar
      if (this.sidebarManager.kiemTraDaKhoiTao()) {
        this.sidebarManager.refresh();
      }
      
      console.log('🔄 App đã được làm mới');
    } catch (loi) {
      console.error('Lỗi khi refresh app:', loi);
    } finally {
      this.state.dangTaiLai = false;
    }
  }

  /**
   * Cleanup khi destroy app
   */
  destroy() {
    this.sidebarManager.destroy();
    console.log('🧹 App đã được dọn dẹp');
  }
}

// ==================== KHỞI ĐỘNG ỨNG DỤNG ====================

/**
 * Hàm khởi động - được gọi khi DOM ready
 */
function khoiDong() {
  // Tạo instance của App
  const app = new App();
  
  // Lưu instance vào window để có thể access từ console (debug)
  window.app = app;
  
  // Khởi tạo
  app.khoiTao();
}

// Chờ DOM ready rồi mới khởi động
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', khoiDong);
} else {
  // DOM đã sẵn sàng rồi
  khoiDong();
}

// Export để có thể import ở nơi khác (nếu cần)
export default App;
