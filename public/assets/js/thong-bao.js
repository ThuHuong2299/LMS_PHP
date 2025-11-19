/**
 * File: thong-bao.js
 * Mục đích: Hệ thống hiển thị thông báo toast cho ứng dụng
 * Hỗ trợ: thành công, lỗi, cảnh báo, thông tin
 */

const ThongBao = {
  // Cấu hình mặc định
  cau_hinh: {
    thoi_gian_hien_thi: 3000, // 3 giây
    vi_tri: 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    cho_phep_dong: true,
    hieu_ung_animation: 'slide' // slide, fade, bounce
  },

  // Container chứa các thông báo
  container: null,

  /**
   * Khởi tạo container thông báo
   */
  khoi_tao_container() {
    if (this.container) return;

    this.container = document.createElement('div');
    this.container.className = 'thong-bao-container';
    this.container.setAttribute('data-vi-tri', this.cau_hinh.vi_tri);
    document.body.appendChild(this.container);
  },

  /**
   * Hiển thị thông báo
   * @param {string} noi_dung - Nội dung thông báo
   * @param {string} loai - Loại thông báo: 'thanh-cong', 'loi', 'canh-bao', 'thong-tin'
   * @param {object} tuy_chon - Tùy chọn bổ sung
   */
  hien_thi(noi_dung, loai = 'thong-tin', tuy_chon = {}) {
    this.khoi_tao_container();

    // Merge tùy chọn
    const cau_hinh_cuoi = {
      ...this.cau_hinh,
      ...tuy_chon
    };

    // Tạo phần tử thông báo
    const phan_tu_thong_bao = document.createElement('div');
    phan_tu_thong_bao.className = `thong-bao thong-bao-${loai}`;
    
    // Thêm animation class
    if (cau_hinh_cuoi.hieu_ung_animation) {
      phan_tu_thong_bao.classList.add(`animation-${cau_hinh_cuoi.hieu_ung_animation}`);
    }

    // Icon cho từng loại thông báo
    const icon_map = {
      'thanh-cong': '✓',
      'loi': '✕',
      'canh-bao': '⚠',
      'thong-tin': 'ℹ'
    };

    const icon = icon_map[loai] || icon_map['thong-tin'];

    // Tạo nội dung HTML
    phan_tu_thong_bao.innerHTML = `
      <div class="thong-bao-icon">
        <span class="icon">${icon}</span>
      </div>
      <div class="thong-bao-noi-dung">
        <p class="thong-bao-text">${noi_dung}</p>
      </div>
      ${cau_hinh_cuoi.cho_phep_dong ? '<button class="thong-bao-dong" onclick="ThongBao.dong_thong_bao(this)">×</button>' : ''}
    `;

    // Thêm vào container
    this.container.appendChild(phan_tu_thong_bao);

    // Tự động ẩn sau thời gian quy định
    if (cau_hinh_cuoi.thoi_gian_hien_thi > 0) {
      setTimeout(() => {
        this.an_thong_bao(phan_tu_thong_bao);
      }, cau_hinh_cuoi.thoi_gian_hien_thi);
    }

    // Thêm hiệu ứng xuất hiện
    setTimeout(() => {
      phan_tu_thong_bao.classList.add('hien-thi');
    }, 10);

    return phan_tu_thong_bao;
  },

  /**
   * Ẩn thông báo với animation
   * @param {HTMLElement} phan_tu - Phần tử thông báo cần ẩn
   */
  an_thong_bao(phan_tu) {
    if (!phan_tu) return;

    phan_tu.classList.remove('hien-thi');
    phan_tu.classList.add('an-di');

    setTimeout(() => {
      if (phan_tu.parentNode) {
        phan_tu.parentNode.removeChild(phan_tu);
      }
    }, 300);
  },

  /**
   * Đóng thông báo khi click nút X
   * @param {HTMLElement} nut_dong - Nút đóng được click
   */
  dong_thong_bao(nut_dong) {
    const phan_tu_thong_bao = nut_dong.closest('.thong-bao');
    this.an_thong_bao(phan_tu_thong_bao);
  },

  /**
   * Hiển thị thông báo thành công
   * @param {string} noi_dung - Nội dung thông báo
   * @param {object} tuy_chon - Tùy chọn bổ sung
   */
  thanh_cong(noi_dung, tuy_chon = {}) {
    return this.hien_thi(noi_dung, 'thanh-cong', tuy_chon);
  },

  /**
   * Hiển thị thông báo lỗi
   * @param {string} noi_dung - Nội dung thông báo
   * @param {object} tuy_chon - Tùy chọn bổ sung
   */
  loi(noi_dung, tuy_chon = {}) {
    return this.hien_thi(noi_dung, 'loi', tuy_chon);
  },

  /**
   * Hiển thị thông báo cảnh báo
   * @param {string} noi_dung - Nội dung thông báo
   * @param {object} tuy_chon - Tùy chọn bổ sung
   */
  canh_bao(noi_dung, tuy_chon = {}) {
    return this.hien_thi(noi_dung, 'canh-bao', tuy_chon);
  },

  /**
   * Hiển thị thông báo thông tin
   * @param {string} noi_dung - Nội dung thông báo
   * @param {object} tuy_chon - Tùy chọn bổ sung
   */
  thong_tin(noi_dung, tuy_chon = {}) {
    return this.hien_thi(noi_dung, 'thong-tin', tuy_chon);
  },

  /**
   * Xóa tất cả thông báo
   */
  xoa_tat_ca() {
    if (this.container) {
      this.container.innerHTML = '';
    }
  },

  /**
   * Thay đổi vị trí hiển thị thông báo
   * @param {string} vi_tri - Vị trí mới
   */
  doi_vi_tri(vi_tri) {
    this.cau_hinh.vi_tri = vi_tri;
    if (this.container) {
      this.container.setAttribute('data-vi-tri', vi_tri);
    }
  }
};

// Alias cho tương thích với code cũ sử dụng Toast
const Toast = {
  success: (msg, opts) => ThongBao.thanh_cong(msg, opts),
  error: (msg, opts) => ThongBao.loi(msg, opts),
  warning: (msg, opts) => ThongBao.canh_bao(msg, opts),
  info: (msg, opts) => ThongBao.thong_tin(msg, opts)
};

// Export để sử dụng ở module khác (nếu cần)
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { ThongBao, Toast };
}
