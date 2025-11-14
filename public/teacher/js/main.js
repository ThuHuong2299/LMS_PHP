/**
 * MAIN.JS - ENTRY POINT CỦA ỨNG DỤNG
 * Khởi tạo sidebar và navbar components
 */

import SidebarManager from './components/sidebar.js';
import NavbarManager from './components/navbar.js';

class App {
  constructor() {
    this.sidebarManager = new SidebarManager();
    this.navbarManager = new NavbarManager();
  }

  async khoiTao() {
    try {
      await Promise.all([
        this.sidebarManager.khoiTao(),
        this.navbarManager.khoiTao()
      ]);
      
      console.log('✅ Đã tải sidebar & navbar');
    } catch (loi) {
      console.error('Lỗi:', loi);
    }
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new App().khoiTao());
} else {
  new App().khoiTao();
}

export default App;
