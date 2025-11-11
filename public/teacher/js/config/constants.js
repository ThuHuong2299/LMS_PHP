/**
 * CÁC HẰNG SỐ VÀ CẤU HÌNH CHUNG
 * File này chứa tất cả các hằng số được sử dụng trong toàn bộ ứng dụng
 */

// ==================== API Configuration ====================
export const API_CONFIG = {
  BASE_URL: 'http://localhost:3000/api',
  TIMEOUT: 10000, // 10 giây
  RETRY_COUNT: 3
};

export const API_ENDPOINTS = {
  // Xác thực
  DANG_NHAP: '/auth/login',
  DANG_XUAT: '/auth/logout',
  KIEM_TRA_SESSION: '/auth/verify',
  
  // Giảng viên
  LAY_THONG_TIN_GIANG_VIEN: '/teacher/profile',
  CAP_NHAT_THONG_TIN: '/teacher/update',
  
  // Lớp học
  LAY_DANH_SACH_LOP: '/classes',
  LAY_CHI_TIET_LOP: '/classes/:id',
  TAO_LOP_MOI: '/classes/create',
  
  // Bài tập
  LAY_DANH_SACH_BAI_TAP: '/homework',
  TAO_BAI_TAP: '/homework/create',
  CAP_NHAT_BAI_TAP: '/homework/:id/update',
  XOA_BAI_TAP: '/homework/:id/delete',
  
  // Thông báo
  LAY_THONG_BAO: '/notifications',
  TAO_THONG_BAO: '/notifications/create',
  DANH_DAU_DA_DOC: '/notifications/:id/read'
};

// ==================== Màu sắc ====================
export const COLORS = {
  PRIMARY: '#3293F9',
  SECONDARY: '#739FFF',
  SUCCESS: '#ACD738',
  WARNING: '#F9A825',
  ERROR: '#F44336',
  INFO: '#2196F3',
  
  // Text colors
  TEXT_PRIMARY: '#000000',
  TEXT_SECONDARY: '#979797',
  TEXT_DISABLED: '#CCCCCC',
  
  // Background colors
  BG_PRIMARY: '#FFFFFF',
  BG_SECONDARY: '#F6F5F5',
  BG_HOVER: '#E5F0FF',
  BG_ACTIVE: '#F3F8FF',
  
  // Border colors
  BORDER_DEFAULT: '#979797',
  BORDER_LIGHT: '#DDDDDD',
  BORDER_FOCUS: '#3293F9',
  
  // Notification colors
  NOTI_SUCCESS: '#DEF6C9',
  NOTI_WARNING: '#FFF4E0',
  NOTI_ERROR: '#FFEBEE',
  NOTI_INFO: '#F0ECFA'
};

// ==================== Typography ====================
export const FONTS = {
  PRIMARY: '"Be Vietnam Pro", sans-serif',
  SECONDARY: '"Quicksand", sans-serif',
  HEADING: '"Bricolage Grotesque", sans-serif',
  
  SIZES: {
    XS: '12px',
    SM: '14px',
    MD: '16px',
    LG: '18px',
    XL: '20px',
    XXL: '24px',
    XXXL: '32px'
  },
  
  WEIGHTS: {
    REGULAR: 400,
    MEDIUM: 500,
    SEMIBOLD: 600,
    BOLD: 700
  }
};

// ==================== Timing & Animation ====================
export const TIMINGS = {
  // Debounce & Throttle
  DEBOUNCE_TIM_KIEM: 300, // ms
  DEBOUNCE_INPUT: 500,
  THROTTLE_SCROLL: 100,
  
  // Animation durations
  TRANSITION_FAST: 200, // ms
  TRANSITION_NORMAL: 300,
  TRANSITION_SLOW: 500,
  
  // Timeouts
  TOAST_DURATION: 3000, // 3 giây
  SESSION_CHECK_INTERVAL: 300000, // 5 phút
  AUTO_SAVE_INTERVAL: 30000, // 30 giây
  
  // Session
  TIMEOUT_SESSION: 3600000 // 1 giờ (60 phút)
};

// ==================== LocalStorage Keys ====================
export const STORAGE_KEYS = {
  // Xác thực
  TOKEN: 'userToken',
  REFRESH_TOKEN: 'refreshToken',
  TOKEN_EXPIRY: 'tokenExpiry',
  
  // Thông tin người dùng
  THONG_TIN_GIANG_VIEN: 'thongTinGiangVien',
  USER_ROLE: 'userRole',
  
  // UI State
  SIDEBAR_COLLAPSED: 'sidebarCollapsed',
  THEME: 'theme',
  LANGUAGE: 'language',
  
  // Cache
  CACHE_PREFIX: 'cache_',
  LAST_ROUTE: 'lastRoute'
};

// ==================== Kích thước & Breakpoints ====================
export const SIZES = {
  SIDEBAR_WIDTH: 250,
  SIDEBAR_COLLAPSED_WIDTH: 80,
  HEADER_HEIGHT: 80,
  
  // Responsive breakpoints
  BREAKPOINTS: {
    MOBILE: 576,
    TABLET: 768,
    DESKTOP: 1024,
    WIDE: 1440
  }
};

// ==================== Quy định & Giới hạn ====================
export const LIMITS = {
  MAX_FILE_SIZE: 10485760, // 10MB
  MAX_FILE_NAME_LENGTH: 255,
  MAX_QUESTION_COUNT: 100,
  MAX_OPTION_COUNT: 10,
  MAX_STUDENT_PER_CLASS: 100,
  MIN_PASSWORD_LENGTH: 6,
  
  // Pagination
  ITEMS_PER_PAGE: 5,
  MAX_PAGES_DISPLAY: 5
};

// ==================== Trạng thái ====================
export const TRANG_THAI = {
  BAI_TAP: {
    CHUA_GIAO: 'chua_giao',
    DA_GIAO: 'da_giao',
    HET_HAN: 'het_han',
    DA_CHAM: 'da_cham'
  },
  
  BAI_LAM: {
    CHUA_LAM: 'chua_lam',
    DANG_LAM: 'dang_lam',
    DA_NOP: 'da_nop',
    TRE_HAN: 'tre_han'
  },
  
  CHAM_BAI: {
    CHUA_CHAM: 'chua_cham',
    DA_CHAM: 'da_cham',
    DANG_CHAM: 'dang_cham'
  }
};

// ==================== Messages ====================
export const MESSAGES = {
  SUCCESS: {
    LUU_THANH_CONG: 'Lưu thành công!',
    XOA_THANH_CONG: 'Xóa thành công!',
    CAP_NHAT_THANH_CONG: 'Cập nhật thành công!',
    TAO_THANH_CONG: 'Tạo mới thành công!',
    DANG_XUAT_THANH_CONG: 'Đăng xuất thành công!'
  },
  
  ERROR: {
    LOI_HE_THONG: 'Đã xảy ra lỗi hệ thống!',
    KHONG_CO_QUYEN: 'Bạn không có quyền thực hiện thao tác này!',
    KHONG_TIM_THAY: 'Không tìm thấy dữ liệu!',
    PHIEN_HET_HAN: 'Phiên đăng nhập đã hết hạn!',
    LOI_MANG: 'Lỗi kết nối mạng!'
  },
  
  WARNING: {
    CHUA_LUU: 'Bạn có thay đổi chưa lưu. Bạn có muốn tiếp tục?',
    XAC_NHAN_XOA: 'Bạn có chắc muốn xóa không?',
    XAC_NHAN_DANG_XUAT: 'Bạn có chắc muốn đăng xuất không?'
  },
  
  VALIDATION: {
    TRUONG_BAT_BUOC: 'Trường này là bắt buộc!',
    EMAIL_KHONG_HOP_LE: 'Email không hợp lệ!',
    SO_DIEN_THOAI_KHONG_HOP_LE: 'Số điện thoại không hợp lệ!',
    MAT_KHAU_QUA_NGAN: 'Mật khẩu phải có ít nhất 6 ký tự!',
    FILE_QUA_LON: 'File quá lớn! (Tối đa 10MB)',
    DINH_DANG_KHONG_HOP_LE: 'Định dạng không hợp lệ!'
  }
};

// ==================== Icons ====================
export const ICONS = {
  DASHBOARD: 'db0.svg',
  CLASS: 'study0.svg',
  NOTIFICATION: 'noti0.svg',
  HOMEWORK: 'exercise0.svg',
  EXAM: 'exam0.svg',
  STUDENT: 'frame0.svg',
  SEARCH: 'search1.svg',
  LOGOUT: 'logout-icon.svg',
  ADD: 'add.svg',
  DELETE: 'delete.svg',
  EDIT: 'edit.svg',
  DOWNLOAD: 'download.svg',
  UPLOAD: 'upload.svg'
};

// ==================== File Types ====================
export const FILE_TYPES = {
  ALLOWED_UPLOAD: ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.jpg', '.jpeg', '.png'],
  MIME_TYPES: {
    PDF: 'application/pdf',
    DOC: 'application/msword',
    DOCX: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    IMAGE: 'image/*'
  }
};

// ==================== Export tất cả ====================
export default {
  API_CONFIG,
  API_ENDPOINTS,
  COLORS,
  FONTS,
  TIMINGS,
  STORAGE_KEYS,
  SIZES,
  LIMITS,
  TRANG_THAI,
  MESSAGES,
  ICONS,
  FILE_TYPES
};
