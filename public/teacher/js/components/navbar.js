class NavbarManager {
  constructor() {
    this.navbar = null;
    this.userName = null;
    this.userCode = null;
    this.userAvatar = null;
  }

  async khoiTao() {
    const container = document.getElementById('navbar-container');
    if (!container) return;

    const res = await fetch('/public/teacher/components/navbar.html');
    container.innerHTML = await res.text();
    
    this.navbar = container.querySelector('.navbar');
    this.userName = document.getElementById('navbarUserName');
    this.userCode = document.getElementById('navbarUserCode');
    this.userAvatar = document.getElementById('navbarAvatar');
    
    this.loadUserInfo();
  }

  loadUserInfo() {
    try {
      // Đọc thông tin người dùng từ localStorage
      const userData = localStorage.getItem('nguoi_dung');
      
      if (!userData) {
        // Chưa đăng nhập - redirect về trang login
        console.warn('Chưa có thông tin đăng nhập');
        window.location.href = '/public/Login.teacher.html';
        return;
      }

      const nguoiDung = JSON.parse(userData);
      
      // Hiển thị tên người dùng
      if (this.userName && nguoiDung.ho_ten) {
        this.userName.textContent = nguoiDung.ho_ten;
      }
      
      // Hiển thị mã giảng viên
      if (this.userCode && nguoiDung.ma_nguoi_dung) {
        this.userCode.textContent = nguoiDung.ma_nguoi_dung;
      }
      
      // Hiển thị avatar (nếu có)
      if (this.userAvatar) {
        if (nguoiDung.anh_dai_dien && nguoiDung.anh_dai_dien !== null) {
          this.userAvatar.src = nguoiDung.anh_dai_dien;
        }
        // Nếu không có avatar, giữ nguyên ảnh mặc định
      }
      
    } catch (error) {
      console.error('Lỗi khi load thông tin người dùng:', error);
      // Nếu có lỗi, hiển thị thông tin mặc định
      if (this.userName) this.userName.textContent = 'Người dùng';
      if (this.userCode) this.userCode.textContent = 'N/A';
    }
  }
}

export default NavbarManager;
