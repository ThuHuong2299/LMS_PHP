/**
 * File: constants.js
 * Mục đích: Các hằng số dùng chung trong ứng dụng
 */

export const STORAGE_KEYS = {
  USER_INFO: 'nguoi_dung',
  SESSION_TOKEN: 'session_token'
};

export const TIMINGS = {
  DEBOUNCE_DELAY: 300,
  ANIMATION_DURATION: 200,
  TOAST_DURATION: 3000
};

export const API_ENDPOINTS = {
  LOGIN: '/backend/teacher/api/dang-nhap.php',
  LOGOUT: '/backend/teacher/api/dang-xuat.php'
};
