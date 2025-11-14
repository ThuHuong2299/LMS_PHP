/**
 * File: api-classroom.js
 * Mục đích: Cấu hình API endpoints cho trang Classroom
 * Ngày tạo: 14/11/2025
 */

export const ClassroomAPI = {
  BASE_URL: '/backend/teacher/api',
  
  /**
   * Lấy danh sách lớp học của giảng viên
   * @returns {Promise<Array>}
   */
  async getDanhSachLopHoc() {
    try {
      const response = await fetch(`${this.BASE_URL}/danh-sach-lop-hoc.php`, {
        method: 'GET',
        credentials: 'include', // Gửi kèm session cookie
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy danh sách lớp học');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getDanhSachLopHoc:', error);
      throw error;
    }
  },
  
  /**
   * Lấy thông tin lớp và bài giảng
   * @param {number} lopHocId - ID lớp học
   * @returns {Promise<Object>}
   */
  async getBaiGiangLopHoc(lopHocId) {
    try {
      const response = await fetch(`${this.BASE_URL}/bai-giang-lop-hoc.php?id=${lopHocId}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy bài giảng');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getBaiGiangLopHoc:', error);
      throw error;
    }
  },
  
  /**
   * Lấy danh sách bài tập
   * @param {number} lopHocId - ID lớp học
   * @returns {Promise<Array>}
   */
  async getBaiTapLopHoc(lopHocId) {
    try {
      const response = await fetch(`${this.BASE_URL}/bai-tap-lop-hoc.php?id=${lopHocId}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy bài tập');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getBaiTapLopHoc:', error);
      throw error;
    }
  },
  
  /**
   * Lấy danh sách bài kiểm tra
   * @param {number} lopHocId - ID lớp học
   * @returns {Promise<Array>}
   */
  async getBaiKiemTraLopHoc(lopHocId) {
    try {
      const response = await fetch(`${this.BASE_URL}/bai-kiem-tra-lop-hoc.php?id=${lopHocId}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy bài kiểm tra');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getBaiKiemTraLopHoc:', error);
      throw error;
    }
  },
  
  /**
   * Lấy danh sách thông báo
   * @param {number} lopHocId - ID lớp học
   * @returns {Promise<Array>}
   */
  async getThongBaoLopHoc(lopHocId) {
    try {
      const response = await fetch(`${this.BASE_URL}/thong-bao-lop-hoc.php?id=${lopHocId}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy thông báo');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getThongBaoLopHoc:', error);
      throw error;
    }
  },

  /**
   * Lấy danh sách sinh viên và tiến độ học tập
   * @param {number} lopHocId - ID lớp học
   * @param {number} page - Trang hiện tại
   * @param {number} limit - Số sinh viên mỗi trang
   * @returns {Promise<Object>}
   */
  async getSinhVienLopHoc(lopHocId, page = 1, limit = 5) {
    try {
      const response = await fetch(`${this.BASE_URL}/sinh-vien-lop-hoc.php?id=${lopHocId}&page=${page}&limit=${limit}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      const data = await response.json();
      
      if (!data.thanh_cong) {
        throw new Error(data.thong_bao || 'Lỗi khi lấy danh sách sinh viên');
      }
      
      return data.du_lieu;
    } catch (error) {
      console.error('Lỗi getSinhVienLopHoc:', error);
      throw error;
    }
  }
};
