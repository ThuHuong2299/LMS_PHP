/**
 * File: dang-nhap-sinh-vien.js
 * Mục đích: Xử lý đăng nhập cho trang sinh viên
 * Ngày tạo: 18/11/2025
 */

// Theo dõi chuyển động mắt
function khoi_tao_theo_doi_mat() {
  const dong_tu_mat = document.querySelectorAll('.pupil');
  const cac_mat = document.querySelectorAll('.eye');

  document.addEventListener('mousemove', (su_kien) => {
    dong_tu_mat.forEach((dong_tu, chi_so) => {
      const mat = cac_mat[chi_so];
      const vi_tri_mat = mat.getBoundingClientRect();
      const tam_mat_x = vi_tri_mat.left + vi_tri_mat.width / 2;
      const tam_mat_y = vi_tri_mat.top + vi_tri_mat.height / 2;

      const goc = Math.atan2(su_kien.clientY - tam_mat_y, su_kien.clientX - tam_mat_x);

      const khoang_cach_toi_da = 4;
      const dong_tu_x = Math.cos(goc) * khoang_cach_toi_da;
      const dong_tu_y = Math.sin(goc) * khoang_cach_toi_da;

      dong_tu.style.transform = `translate(${dong_tu_x}px, ${dong_tu_y}px)`;
    });
  });
}

// Chuyển tab giảng viên
function khoi_tao_chuyen_tab() {
  const tab_giang_vien = document.querySelector('.frame-668');
  const tab_slider = document.querySelector('.tab-slider');
  let dang_chuyen_tab = false;

  if (tab_giang_vien) {
    tab_giang_vien.addEventListener('click', () => {
      if (dang_chuyen_tab) return;
      
      dang_chuyen_tab = true;
      tab_slider.style.transition = 'transform 0.2s ease';
      tab_slider.style.transform = 'translateX(0)';
      
      setTimeout(() => {
        window.location.href = '/public/Login.teacher.html';
      }, 200);
    });
  }
}

// Hiển thị/ẩn mật khẩu
let mat_khau_hien_thi = false;

function hienThiMatKhau() {
  const o_nhap_mat_khau = document.getElementById('passwordInput');
  const icon_mat = document.getElementById('eyeIcon');
  
  if (!o_nhap_mat_khau || !icon_mat) return;
  
  mat_khau_hien_thi = !mat_khau_hien_thi;
  
  if (mat_khau_hien_thi) {
    o_nhap_mat_khau.type = 'text';
    // Icon mắt có gạch chéo (ẩn)
    icon_mat.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
  } else {
    o_nhap_mat_khau.type = 'password';
    // Icon mắt bình thường (hiện)
    icon_mat.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
  }
}

// Validate email
function kiem_tra_dinh_dang_email(email) {
  const dinh_dang_email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return dinh_dang_email.test(email);
}

// Xử lý đăng nhập
async function xuLyDangNhap() {
  const o_nhap_email = document.getElementById('emailInput');
  const o_nhap_mat_khau = document.getElementById('passwordInput');
  
  if (!o_nhap_email || !o_nhap_mat_khau) {
    console.error('Không tìm thấy các ô nhập liệu');
    return;
  }
  
  const email = o_nhap_email.value.trim();
  const mat_khau = o_nhap_mat_khau.value;
  
  // Kiểm tra dữ liệu đầu vào
  if (!email || !mat_khau) {
    ThongBao.canh_bao('Vui lòng nhập đầy đủ email và mật khẩu!');
    return;
  }
  
  // Validate định dạng email
  if (!kiem_tra_dinh_dang_email(email)) {
    ThongBao.loi('Email không hợp lệ!');
    return;
  }
  
  // Hiển thị trạng thái đang xử lý (tùy chọn)
  const nut_dang_nhap = document.querySelector('.frame-13213168892');
  const noi_dung_nut_goc = nut_dang_nhap ? nut_dang_nhap.innerHTML : '';
  
  try {
    // Vô hiệu hóa nút trong khi xử lý
    if (nut_dang_nhap) {
      nut_dang_nhap.style.pointerEvents = 'none';
      nut_dang_nhap.style.opacity = '0.6';
    }
    
    // Gọi API đăng nhập
    const phan_hoi = await fetch('/backend/student/api/dang-nhap.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        email: email,
        mat_khau: mat_khau
      })
    });
    
    // Kiểm tra HTTP status
    if (!phan_hoi.ok) {
      throw new Error(`HTTP error! status: ${phan_hoi.status}`);
    }
    
    const du_lieu = await phan_hoi.json();
    
    // Xử lý kết quả
    if (du_lieu.thanh_cong) {
      ThongBao.thanh_cong(du_lieu.thong_bao);
      
      // Lưu thông tin vào localStorage (tùy chọn)
      if (du_lieu.du_lieu) {
        localStorage.setItem('sinh_vien_info', JSON.stringify(du_lieu.du_lieu));
      }
      
      // Chuyển hướng sau 1 giây
      // Base hiện tại: ./student/assets/ (từ /public/)
      // Cần lùi 2 cấp: ../../ để về /public/, rồi vào student/
      setTimeout(() => {
        window.location.href = '../../student/Trang Chủ.html';
      }, 1000);
    } else {
      ThongBao.loi(du_lieu.thong_bao || 'Đăng nhập thất bại!');
      
      // Khôi phục nút
      if (nut_dang_nhap) {
        nut_dang_nhap.style.pointerEvents = 'auto';
        nut_dang_nhap.style.opacity = '1';
      }
    }
  } catch (loi) {
    console.error('Lỗi kết nối:', loi);
    ThongBao.loi('Không thể kết nối đến server! Vui lòng kiểm tra kết nối mạng.');
    
    // Khôi phục nút
    if (nut_dang_nhap) {
      nut_dang_nhap.style.pointerEvents = 'auto';
      nut_dang_nhap.style.opacity = '1';
    }
  }
}

// Hỗ trợ phím Enter
function khoi_tao_phim_tat() {
  const o_nhap_mat_khau = document.getElementById('passwordInput');
  const o_nhap_email = document.getElementById('emailInput');
  
  if (o_nhap_mat_khau) {
    o_nhap_mat_khau.addEventListener('keypress', (su_kien) => {
      if (su_kien.key === 'Enter') {
        xuLyDangNhap();
      }
    });
  }
  
  if (o_nhap_email) {
    o_nhap_email.addEventListener('keypress', (su_kien) => {
      if (su_kien.key === 'Enter') {
        if (o_nhap_mat_khau) {
          o_nhap_mat_khau.focus();
        } else {
          xuLyDangNhap();
        }
      }
    });
  }
}

// Khởi tạo khi trang load xong
document.addEventListener('DOMContentLoaded', function() {
  console.log('Đang khởi tạo trang đăng nhập sinh viên...');
  
  // Khởi tạo các chức năng
  khoi_tao_theo_doi_mat();
  khoi_tao_chuyen_tab();
  khoi_tao_phim_tat();
  
  console.log('Trang đăng nhập sinh viên đã sẵn sàng!');
});

// Export các hàm để sử dụng trong HTML (nếu cần)
if (typeof window !== 'undefined') {
  window.xuLyDangNhap = xuLyDangNhap;
  window.hienThiMatKhau = hienThiMatKhau;
}
