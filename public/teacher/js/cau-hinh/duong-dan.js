/**
 * CẤU HÌNH ROUTES & NAVIGATION
 * File này định nghĩa tất cả các routes và cấu trúc menu của ứng dụng
 */

// Định nghĩa các routes chính
// Lưu ý: duongDan chỉ là tên file, sẽ được convert thành absolute path khi sử dụng
export const ROUTES = {
  TRANG_CHU: {
    id: 'dashboard',
    duongDan: 'TrangChu.html',
    ten: 'Dashboard',
    icon: 'db0.svg',
    moTa: 'Trang chủ giảng viên'
  },
  LOP_HOC: {
    id: 'lophoc',
    duongDan: 'Classroom.html',
    ten: 'Lớp học',
    icon: 'study0.svg',
    moTa: 'Quản lý các lớp học'
  },
  THONG_BAO: {
    id: 'thongbao',
    duongDan: 'Notification.html',
    ten: 'Thông báo',
    icon: 'noti0.svg',
    moTa: 'Xem và tạo thông báo'
  }
};

// Các routes phụ (không hiển thị trong menu chính)
export const ROUTES_PHU = {
  CHI_TIET_LOP: {
    duongDan: 'ClassroomInfo.html',
    ten: 'Chi tiết lớp học',
    parent: 'lophoc'
  },
  DASHBOARD_BAI_TAP: {
    duongDan: 'WorkDashBoard.html',
    ten: 'Quản lý bài tập',
    parent: 'lophoc'
  },
  CHI_TIET_BAI_TAP: {
    duongDan: 'HomeWork.html',
    ten: 'Chi tiết bài tập',
    parent: 'lophoc'
  },
  THONG_TIN_BAI_TAP: {
    duongDan: 'HomeWorkInfo.html',
    ten: 'Thông tin bài tập',
    parent: 'lophoc'
  }
};

/**
 * Menu hiển thị trong sidebar (theo thứ tự)
 */
export const MENU_SIDEBAR = [
  ROUTES.TRANG_CHU,
  ROUTES.LOP_HOC,
  ROUTES.THONG_BAO
];

/**
 * Lấy route hiện tại dựa trên URL
 * @returns {Object|null} Route object hoặc null nếu không tìm thấy
 */
export function layRouteHienTai() {
  const tenFile = window.location.pathname.split('/').pop() || 'TrangChu.html';
  
  // Tìm trong routes chính
  for (const [key, route] of Object.entries(ROUTES)) {
    if (route.duongDan === tenFile) {
      return route;
    }
  }
  
  // Tìm trong routes phụ
  for (const [key, route] of Object.entries(ROUTES_PHU)) {
    if (route.duongDan === tenFile) {
      return route;
    }
  }
  
  return null;
}

/**
 * Lấy ID của trang hiện tại
 * @returns {string} ID của route hiện tại
 */
export function layIdTrangHienTai() {
  const route = layRouteHienTai();
  return route?.id || 'dashboard';
}

/**
 * Kiểm tra xem một route có đang active không
 * @param {string} routeId - ID của route cần kiểm tra
 * @returns {boolean}
 */
export function kiemTraRouteActive(routeId) {
  const routeHienTai = layRouteHienTai();
  
  // Nếu không tìm thấy route hiện tại, không đánh dấu gì là active
  if (!routeHienTai) return false;
  
  // Kiểm tra trực tiếp theo ID (chỉ cho routes chính có ID)
  if (routeHienTai.id && routeHienTai.id === routeId) return true;
  
  // Kiểm tra parent route (cho routes phụ)
  if (routeHienTai.parent === routeId) return true;
  
  return false;
}