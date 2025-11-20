class SidebarManager {
  constructor() {
    this.sidebar = null;
    this.container = null;
  }

  async initialize() {
    try {
      // 1. Fetch sidebar template
      const container = document.getElementById('sidebar-container');
      if (!container) {
        console.error(' sidebar-container không tìm thấy');
        return;
      }

      const response = await fetch('/public/student/components/sidebar.html');
      if (!response.ok) {
        throw new Error(`Fetch failed: ${response.status}`);
      }

      container.innerHTML = await response.text();
      this.sidebar = document.getElementById('sidebar');
      this.container = container;

      // 2. Bind events
      this.bindEvents();

      // 3. Mark active page
      this.markActivePage();

      console.log('✅ Sidebar loaded');
    } catch (error) {
      console.error(' Lỗi load sidebar:', error);
    }
  }

  bindEvents() {
    // Lớp học menu
    const menuLopHoc = this.sidebar.querySelector('#menuLopHoc');
    if (menuLopHoc) {
      menuLopHoc.addEventListener('click', () => {
        window.location.href = '../student/Trang Chủ.html';
      });
    }

    // Thông báo menu
    const menuThongBao = this.sidebar.querySelector('#menuThongBao');
    if (menuThongBao) {
      menuThongBao.addEventListener('click', () => {
        window.location.href = '../student/Thông báo.html';
      });
    }

    // Logout button
    const btnLogout = this.sidebar.querySelector('#btnLogout');
    if (btnLogout) {
      btnLogout.addEventListener('click', () => {
        this.logout();
      });
    }

    // Collapse/Expand sidebar
    const openCloseBtn = this.sidebar.querySelector('.open-close');
    if (openCloseBtn) {
      openCloseBtn.addEventListener('click', () => {
        this.toggleSidebar();
      });
    }
  }

  toggleSidebar() {
    if (this.sidebar) {
      this.sidebar.classList.toggle('collapsed');
      // Save state to localStorage
      const isCollapsed = this.sidebar.classList.contains('collapsed');
      localStorage.setItem('sidebarCollapsed', isCollapsed ? 'true' : 'false');
    }
  }

  markActivePage() {
    const currentPage = this.getCurrentPageId();
    const menuItems = this.sidebar.querySelectorAll('[id^="menu"]');

    menuItems.forEach(item => {
      item.classList.remove('active');

      // Map menu IDs to page identifiers
      if (currentPage === 'home' && item.id === 'menuLopHoc') {
        item.classList.add('tab-side-bar2'); // Highlight current
      } else if (currentPage === 'notification' && item.id === 'menuThongBao') {
        item.classList.add('tab-side-bar2');
      }
    });
  }

  getCurrentPageId() {
    const path = window.location.pathname.toLowerCase();
    
    if (path.includes('trang chủ') || path.includes('trang chu')) return 'home';
    if (path.includes('thông báo') || path.includes('thong bao')) return 'notification';
    if (path.includes('bài kiểm tra') || path.includes('bai kiem tra')) return 'test';
    if (path.includes('bài tập') || path.includes('bai tap')) return 'homework';
    if (path.includes('bảng điểm') || path.includes('bang diem')) return 'scores';
    if (path.includes('tài liệu') || path.includes('tai lieu')) return 'materials';
    
    return 'unknown';
  }

  logout() {
    // Clear session storage/local storage
    sessionStorage.clear();
    localStorage.removeItem('userToken');
    localStorage.removeItem('userData');
    
    // Redirect to login
    window.location.href = '../Login.student.html';
  }

  restoreState() {
    // Restore sidebar collapsed state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed && this.sidebar) {
      this.sidebar.classList.add('collapsed');
    }
  }
}

export default SidebarManager;
