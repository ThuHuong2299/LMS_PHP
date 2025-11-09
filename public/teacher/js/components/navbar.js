class NavbarManager {
  constructor() {
    this.navbar = null;
    this.userName = null;
  }

  async khoiTao() {
    const container = document.getElementById('navbar-container');
    if (!container) return;

    const res = await fetch('/public/teacher/components/navbar.html');
    container.innerHTML = await res.text();
    
    this.navbar = container.querySelector('.navbar');
    this.userName = document.getElementById('navbarUserName');
    
    this.loadUserInfo();
  }

  loadUserInfo() {
    const user = JSON.parse(localStorage.getItem('thongTinGiangVien') || '{}');
    if (user.ten) this.userName.textContent = user.ten;
  }
}

export default NavbarManager;
