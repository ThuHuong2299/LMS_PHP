import { BREADCRUMB_MAP } from '../config/routes.js';

class NavbarManager {
  constructor() {
    this.navbar = null;
    this.breadcrumb = null;
    this.userName = null;
  }

  async khoiTao() {
    const container = document.getElementById('navbar-container');
    if (!container) return;

    const res = await fetch('/public/teacher/components/navbar.html');
    container.innerHTML = await res.text();
    
    this.navbar = container.querySelector('.navbar');
    this.breadcrumb = document.getElementById('navbarBreadcrumb');
    this.userName = document.getElementById('navbarUserName');
    
    this.renderBreadcrumb();
    this.loadUserInfo();
  }

  renderBreadcrumb() {
    const fileName = window.location.pathname.split('/').pop();
    const items = BREADCRUMB_MAP[fileName] || [];
    
    this.breadcrumb.innerHTML = items.map((item, i) => `
      ${item.duongDan ? 
        `<a href="${item.duongDan}" class="breadcrumb__item">${item.ten}</a>` :
        `<span class="breadcrumb__current">${item.ten}</span>`
      }
      ${i < items.length - 1 ? '<span class="breadcrumb__separator">›</span>' : ''}
    `).join('');
  }

  loadUserInfo() {
    const user = JSON.parse(localStorage.getItem('thongTinGiangVien') || '{}');
    if (user.ten) this.userName.textContent = user.ten;
  }
}

export default NavbarManager;
