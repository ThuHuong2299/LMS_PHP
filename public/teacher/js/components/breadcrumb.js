/**
 * File: breadcrumb.js
 * Muc dich: Xu ly breadcrumb navigation
 */

class BreadcrumbManager {
  constructor() {
    this.urlParams = new URLSearchParams(window.location.search);
    this.lopHocId = this.urlParams.get('lop_hoc_id') || this.urlParams.get('id');
  }

  /**
   * Tao breadcrumb HTML
   * @param {Array} items - Mang cac item breadcrumb
   * @returns {string} HTML string
   */
  render(items) {
    if (!items || items.length === 0) return '';

    return items.map((item, index) => {
      const isLast = index === items.length - 1;
      
      if (isLast) {
        // Item cuoi cung - khong co link
        return `<span class="current">${item.text}</span>`;
      } else {
        // Item co link
        const url = this.buildUrl(item.url, item.params);
        return `
          <a href="${url}" class="breadcrumb-link">${item.text}</a>
          <span class="separator">›</span>
        `;
      }
    }).join('');
  }

  /**
   * Xay dung URL voi params
   */
  buildUrl(baseUrl, params = {}) {
    if (!params || Object.keys(params).length === 0) {
      return baseUrl;
    }

    const queryString = Object.entries(params)
      .map(([key, value]) => `${key}=${value}`)
      .join('&');
    
    return `${baseUrl}?${queryString}`;
  }

  /**
   * Breadcrumb cho ClassroomInfo
   */
  renderClassroomInfo() {
    const items = [
      { text: 'Lớp học', url: '/public/teacher/Classroom.html' },
      { text: 'Chi tiết lớp', url: '' }
    ];
    return this.render(items);
  }

  /**
   * Breadcrumb cho ThongTinBaiHoc
   */
  renderThongTinBaiHoc() {
    const items = [
      { text: 'Lớp học', url: '/public/teacher/Classroom.html' },
      { 
        text: 'Chi tiết lớp', 
        url: '/public/teacher/ClassroomInfo.html',
        params: this.lopHocId ? { id: this.lopHocId } : {}
      },
      { text: 'Bài giảng', url: '' }
    ];
    return this.render(items);
  }

  /**
   * Breadcrumb cho HomeWork
   */
  renderHomeWork() {
    const items = [
      { text: 'Lớp học', url: '/public/teacher/Classroom.html' },
      { 
        text: 'Chi tiết lớp', 
        url: '/public/teacher/ClassroomInfo.html',
        params: this.lopHocId ? { id: this.lopHocId } : {}
      },
      { text: 'Bài tập', url: '' }
    ];
    return this.render(items);
  }

  /**
   * Breadcrumb cho WorkDashBoard
   */
  renderWorkDashBoard() {
    const baiKiemTraId = this.urlParams.get('bai_kiem_tra_id');
    const dashboardText = baiKiemTraId ? 'Dashboard bài kiểm tra' : 'Dashboard bài tập';
    
    const items = [
      { text: 'Lớp học', url: '/public/teacher/Classroom.html' },
      { 
        text: 'Chi tiết lớp', 
        url: '/public/teacher/ClassroomInfo.html',
        params: this.lopHocId ? { id: this.lopHocId } : {}
      },
      { text: dashboardText, url: '' }
    ];
    return this.render(items);
  }

  /**
   * Tu dong render breadcrumb theo trang hien tai
   */
  autoRender(containerId = 'breadcrumb-container') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const currentPage = this.getCurrentPage();
    let html = '';

    switch (currentPage) {
      case 'ClassroomInfo':
        html = this.renderClassroomInfo();
        break;
      case 'ThongTinBaiHoc':
        html = this.renderThongTinBaiHoc();
        break;
      case 'HomeWork':
        html = this.renderHomeWork();
        break;
      case 'WorkDashBoard':
        html = this.renderWorkDashBoard();
        break;
      default:
        html = '';
    }

    container.innerHTML = html;
  }

  /**
   * Lay ten trang hien tai
   */
  getCurrentPage() {
    const path = window.location.pathname;
    if (path.includes('ClassroomInfo.html')) return 'ClassroomInfo';
    if (path.includes('ThongTinBaiHoc.html')) return 'ThongTinBaiHoc';
    if (path.includes('HomeWork.html')) return 'HomeWork';
    if (path.includes('WorkDashBoard.html')) return 'WorkDashBoard';
    return '';
  }
}

// Export cho su dung
window.BreadcrumbManager = BreadcrumbManager;
