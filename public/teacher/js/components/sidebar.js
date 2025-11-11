/**
 * SIDEBAR MANAGER - QUẢN LÝ SIDEBAR
 * Class này xử lý tất cả logic liên quan đến sidebar:
 * - Load template HTML
 * - Render menu items
 * - Toggle sidebar (mở/đóng)
 * - Đánh dấu trang active
 * - Xử lý navigation
 * - Xử lý đăng xuất
 */

import { MENU_SIDEBAR, layIdTrangHienTai, kiemTraRouteActive } from '../config/routes.js';
import { STORAGE_KEYS, TIMINGS } from '../config/constants.js';

class SidebarManager {
  /**
   * Constructor - Khởi tạo các thuộc tính
   */
  constructor() {
    this.sidebar = null;
    this.menuContainer = null;
    this.btnToggle = null;
    this.btnLogout = null;
    this.logoIcon = null;
    this.trangHienTai = layIdTrangHienTai();
    this.daKhoiTao = false;
  }

  /**
   * Khởi tạo sidebar - Entry point chính
   * @returns {Promise<void>}
   */
  async khoiTao() {
    try {
      console.log('🔧 Đang khởi tạo Sidebar...');
      
      // Load template HTML
      await this.taiTemplateSidebar();
      
      // Lấy references đến các elements
      this.layReferences();
      
      // Render menu items
      this.renderMenu();
      
      // Gắn event listeners
      this.ganSuKien();
      
      // Đánh dấu trang active
      this.datTrangActive();
      
      // Khôi phục trạng thái sidebar từ lần trước
      this.khoiPhucTrangThai();
      
      this.daKhoiTao = true;
      console.log('✅ Sidebar khởi tạo thành công');
    } catch (loi) {
      console.error('❌ Lỗi khởi tạo Sidebar:', loi);
      this.hienThiLoiKhoiTao();
    }
  }

  /**
   * Tải template sidebar từ file HTML
   * @returns {Promise<void>}
   */
  async taiTemplateSidebar() {
    try {
      const container = document.getElementById('sidebar-container');
      if (!container) {
        throw new Error('Không tìm thấy #sidebar-container trong DOM');
      }

      const response = await fetch('/public/teacher/components/sidebar.html');
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const html = await response.text();
      container.innerHTML = html;
      
      // Đợi DOM update
      await new Promise(resolve => setTimeout(resolve, 50));
    } catch (loi) {
      console.error('Lỗi tải template sidebar:', loi);
      throw loi;
    }
  }

  /**
   * Lấy references đến các DOM elements
   */
  layReferences() {
    this.sidebar = document.getElementById('sidebar');
    this.menuContainer = document.getElementById('sidebarMenu');
    this.btnToggle = document.querySelector('.sidebar__toggle-btn');
    this.btnLogout = document.getElementById('btnLogout');
    this.logoIcon = document.querySelector('.sidebar__logo-icon');

    if (!this.sidebar || !this.menuContainer) {
      throw new Error('Không tìm thấy các elements cần thiết của sidebar');
    }
  }

  /**
   * Render menu items từ config
   */
  renderMenu() {
    if (!this.menuContainer) return;

    const htmlMenu = MENU_SIDEBAR.map(item => `
      <div class="sidebar__menu-item" 
           data-route-id="${item.id}"
           data-tooltip="${item.ten}">
        <img class="sidebar__menu-icon" 
             src="/public/teacher/assets/${item.icon}" 
             alt="Icon ${item.ten}" />
        <span class="sidebar__menu-text">${item.ten}</span>
      </div>
    `).join('');

    this.menuContainer.innerHTML = htmlMenu;
  }

  /**
   * Gắn tất cả event listeners
   */
  ganSuKien() {
    // Toggle sidebar khi click nút
    this.btnToggle?.addEventListener('click', (e) => {
      e.stopPropagation();
      this.toggleSidebar();
    });

    // Click logo
    this.logoIcon?.addEventListener('click', () => {
      this.xuLyClickLogo();
    });

    // Click menu items
    this.menuContainer?.addEventListener('click', (e) => {
      const menuItem = e.target.closest('.sidebar__menu-item');
      if (menuItem) {
        const routeId = menuItem.dataset.routeId;
        this.chuyenTrang(routeId);
      }
    });

    // Logout button
    this.btnLogout?.addEventListener('click', () => {
      this.xuLyDangXuat();
    });

    // Keyboard shortcuts
    this.ganPhimTat();
  }

  /**
   * Gắn keyboard shortcuts
   */
  ganPhimTat() {
    document.addEventListener('keydown', (e) => {
      // Ctrl/Cmd + B: Toggle sidebar
      if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
        e.preventDefault();
        this.toggleSidebar();
      }
    });
  }

  /**
   * Toggle mở/đóng sidebar
   */
  toggleSidebar() {
    if (!this.sidebar) return;

    const daThuGon = this.sidebar.classList.toggle('sidebar--collapsed');
    
    // Lưu trạng thái vào localStorage
    localStorage.setItem(STORAGE_KEYS.SIDEBAR_COLLAPSED, daThuGon);
    
    // Dispatch custom event để các component khác biết
    window.dispatchEvent(new CustomEvent('sidebar-toggle', { 
      detail: { collapsed: daThuGon } 
    }));

    console.log(`Sidebar ${daThuGon ? 'thu gọn' : 'mở rộng'}`);
  }

  /**
   * Xử lý click vào logo - Luôn về trang chủ
   */
  xuLyClickLogo() {
    // Logo luôn dẫn về trang chủ, không toggle sidebar
    this.chuyenTrang('dashboard');
  }

  /**
   * Đánh dấu menu item của trang hiện tại là active
   */
  datTrangActive() {
    // Xóa tất cả active cũ
    const tatCaItems = this.menuContainer?.querySelectorAll('.sidebar__menu-item');
    tatCaItems?.forEach(item => {
      item.classList.remove('sidebar__menu-item--active');
    });

    // Tìm và đánh dấu item active
    MENU_SIDEBAR.forEach(menuItem => {
      if (kiemTraRouteActive(menuItem.id)) {
        const element = this.menuContainer?.querySelector(`[data-route-id="${menuItem.id}"]`);
        element?.classList.add('sidebar__menu-item--active');
      }
    });
  }

  /**
   * Chuyển trang
   * @param {string} routeId - ID của route cần chuyển đến
   */
  chuyenTrang(routeId) {
    const route = MENU_SIDEBAR.find(item => item.id === routeId);
    
    if (route) {
      // Lưu route hiện tại trước khi chuyển
      localStorage.setItem(STORAGE_KEYS.LAST_ROUTE, window.location.pathname);
      
      // Chuyển trang với đường dẫn tuyệt đối để tránh lỗi với <base> tag
      window.location.href = `/public/teacher/${route.duongDan}`;
    } else {
      console.warn(`Không tìm thấy route với ID: ${routeId}`);
    }
  }

  /**
   * Khôi phục trạng thái sidebar từ localStorage
   */
  khoiPhucTrangThai() {
    const daThuGon = localStorage.getItem(STORAGE_KEYS.SIDEBAR_COLLAPSED) === 'true';
    
    if (daThuGon && this.sidebar) {
      this.sidebar.classList.add('sidebar--collapsed');
    }
  }

  /**
   * Xử lý đăng xuất
   */
  xuLyDangXuat() {
    // Hiển thị confirm dialog
    const xacNhan = confirm('Bạn có chắc muốn đăng xuất không?');
    
    if (xacNhan) {
      // Xóa tất cả dữ liệu trong localStorage
      this.xoaDuLieuDangNhap();
      
      // Redirect về trang đăng nhập
      window.location.href = '../Login.teacher.html';
    }
  }

  /**
   * Xóa dữ liệu đăng nhập
   */
  xoaDuLieuDangNhap() {
    const keysCanXoa = [
      STORAGE_KEYS.TOKEN,
      STORAGE_KEYS.REFRESH_TOKEN,
      STORAGE_KEYS.TOKEN_EXPIRY,
      STORAGE_KEYS.THONG_TIN_GIANG_VIEN,
      STORAGE_KEYS.USER_ROLE
    ];

    keysCanXoa.forEach(key => {
      localStorage.removeItem(key);
    });

    // Clear sessionStorage
    sessionStorage.clear();

    console.log('🔓 Đã xóa dữ liệu đăng nhập');
  }

  /**
   * Hiển thị thông báo lỗi khi không thể khởi tạo
   */
  hienThiLoiKhoiTao() {
    const container = document.getElementById('sidebar-container');
    if (container) {
      container.innerHTML = `
        <div style="padding: 20px; color: red;">
          <p>⚠️ Không thể tải sidebar</p>
          <p>Vui lòng tải lại trang</p>
        </div>
      `;
    }
  }

  /**
   * Destroy - Dọn dẹp khi không dùng nữa
   */
  destroy() {
    // Remove event listeners
    this.btnToggle?.removeEventListener('click', this.toggleSidebar);
    this.logoIcon?.removeEventListener('click', this.xuLyClickLogo);
    this.btnLogout?.removeEventListener('click', this.xuLyDangXuat);
    
    // Clear references
    this.sidebar = null;
    this.menuContainer = null;
    this.btnToggle = null;
    this.btnLogout = null;
    this.logoIcon = null;
    
    this.daKhoiTao = false;
    console.log('🧹 Sidebar đã được dọn dẹp');
  }

  /**
   * Refresh sidebar - Tải lại menu
   */
  refresh() {
    this.renderMenu();
    this.datTrangActive();
    console.log('🔄 Sidebar đã được làm mới');
  }

  /**
   * Kiểm tra sidebar đã khởi tạo chưa
   * @returns {boolean}
   */
  kiemTraDaKhoiTao() {
    return this.daKhoiTao;
  }
}

// Export class
export default SidebarManager;
