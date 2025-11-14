/**
 * File: login-teacher.js
 * Mục đích: Xử lý logic đăng nhập cho giảng viên
 * Ngày tạo: 14/11/2025
 */

// Tab switching với animation
const studentTab = document.querySelector('.frame-669');
const slider = document.querySelector('.tab-slider');
let isTransitioning = false;

studentTab.addEventListener('click', () => {
  if (isTransitioning) return;
  
  isTransitioning = true;
  slider.style.transition = 'transform 0.2s ease';
  slider.style.transform = 'translateX(100%)';
  
  setTimeout(() => {
    window.location.href = '/public/Login.student.html';
  }, 200);
});

// Password toggle functionality
let passwordVisible = false;

function togglePassword() {
  const passwordInput = document.getElementById('passwordInput');
  const eyeIcon = document.getElementById('eyeIcon');
  
  passwordVisible = !passwordVisible;
  
  if (passwordVisible) {
    passwordInput.type = 'text';
    // Icon mắt có gạch chéo (ẩn)
    eyeIcon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
  } else {
    passwordInput.type = 'password';
    // Icon mắt bình thường (hiện)
    eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
  }
}

// Login handler
async function handleLogin() {
  const email = document.getElementById('emailInput').value.trim();
  const password = document.getElementById('passwordInput').value;
  
  if (!email || !password) {
    alert('Vui lòng nhập đầy đủ email và mật khẩu!');
    return;
  }
  
  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Email không hợp lệ!');
    return;
  }
  
  // Hiển thị loading (tùy chọn)
  const loginButton = document.querySelector('.frame-13213168892');
  const originalText = loginButton.querySelector('.ng-nh-p2').textContent;
  loginButton.querySelector('.ng-nh-p2').textContent = 'Đang xử lý...';
  loginButton.style.opacity = '0.6';
  loginButton.style.pointerEvents = 'none';
  
  try {
    // Gọi API đăng nhập
    const response = await fetch('/backend/teacher/api/dang-nhap.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        email: email,
        mat_khau: password
      })
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      // Đăng nhập thành công
      alert(data.thong_bao || 'Đăng nhập thành công!');
      
      // Lưu thông tin người dùng vào localStorage (tùy chọn)
      if (data.du_lieu) {
        localStorage.setItem('nguoi_dung', JSON.stringify(data.du_lieu));
      }
      
      // Redirect sang trang chủ giảng viên
      window.location.replace('/public/teacher/TrangChu.html');
    } else {
      // Đăng nhập thất bại
      alert(data.thong_bao || 'Đăng nhập thất bại!');
      
      // Reset button
      loginButton.querySelector('.ng-nh-p2').textContent = originalText;
      loginButton.style.opacity = '1';
      loginButton.style.pointerEvents = 'auto';
    }
    
  } catch (error) {
    console.error('Lỗi kết nối API:', error);
    alert('Không thể kết nối đến server. Vui lòng kiểm tra kết nối!');
    
    // Reset button
    loginButton.querySelector('.ng-nh-p2').textContent = originalText;
    loginButton.style.opacity = '1';
    loginButton.style.pointerEvents = 'auto';
  }
}

// Enter key support
document.getElementById('passwordInput').addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    handleLogin();
  }
});

document.getElementById('emailInput').addEventListener('keypress', (e) => {
  if (e.key === 'Enter') {
    document.getElementById('passwordInput').focus();
  }
});
