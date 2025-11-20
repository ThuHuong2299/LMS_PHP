/**
 * File: bang-diem.js
 * Mục đích: Xử lý hiển thị bảng điểm theo chương
 */

class BangDiemManager {
  constructor() {
    this.chapterSelect = document.getElementById('chapter-select');
    this.gradesContainer = document.getElementById('grades-container');
    this.lopHocId = this.getLopHocIdFromURL();
    this.cachedData = null; // Cache dữ liệu từ API
    
    this.init();
  }
  
  /**
   * Lấy lop_hoc_id từ URL
   */
  getLopHocIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('lop_hoc_id');
  }
  
  async init() {
    if (!this.lopHocId) {
      this.showError('Thiếu thông tin lớp học trong URL');
      return;
    }
    
    // Load dữ liệu từ API
    await this.loadGradesData();
    
    // Lắng nghe sự kiện thay đổi chương
    if (this.chapterSelect) {
      this.chapterSelect.addEventListener('change', () => {
        this.renderBySelectedChapter();
      });
    }
  }
  
  /**
   * Hiển thị tất cả các chương
   */
  showAllChapters() {
    const allCards = this.gradesContainer.querySelectorAll('.chapter-grade-card');
    allCards.forEach(card => {
      card.style.display = 'block';
    });
  }
  
  /**
   * Lọc hiển thị chương cụ thể
   */
  filterChapter(chapterId) {
    const allCards = this.gradesContainer.querySelectorAll('.chapter-grade-card');
    
    allCards.forEach((card, index) => {
      // Giả sử index + 1 = chapter ID (tạm thời dùng cho demo)
      // Sau này sẽ dùng data-chapter-id attribute
      if ((index + 1).toString() === chapterId) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }
  
  /**
   * Load dữ liệu từ API
   */
  async loadGradesData() {
    try {
      const response = await fetch(`../../backend/student/api/diem-theo-chuong.php?lop_hoc_id=${this.lopHocId}`);
      const data = await response.json();
      
      console.log('API Response:', data);
      
      if (data.status === 'success' || data.thanh_cong) {
        const apiData = data.data || data.du_lieu;
        this.cachedData = apiData;
        
        // Populate dropdown với danh sách chương
        this.populateChapterDropdown(apiData.chuong);
        
        // Render dữ liệu ban đầu
        this.renderBySelectedChapter();
      } else {
        throw new Error(data.message || data.thong_bao || 'Không thể load dữ liệu điểm');
      }
    } catch (error) {
      console.error('Lỗi load dữ liệu điểm:', error);
      this.showError('Không thể tải dữ liệu điểm: ' + error.message);
    }
  }
  
  /**
   * Populate dropdown với danh sách chương từ API
   */
  populateChapterDropdown(danhSachChuong) {
    if (!this.chapterSelect || !danhSachChuong) return;
    
    // Xóa các option cũ (trừ "Tất cả")
    while (this.chapterSelect.options.length > 1) {
      this.chapterSelect.remove(1);
    }
    
    // Thêm các chương từ API
    danhSachChuong.forEach(chuong => {
      const option = document.createElement('option');
      option.value = chuong.chuong_id;
      option.textContent = `Chương ${chuong.so_thu_tu}: ${chuong.ten_chuong}`;
      this.chapterSelect.appendChild(option);
    });
  }
  
  /**
   * Render theo chương được chọn
   */
  renderBySelectedChapter() {
    if (!this.cachedData) return;
    
    const selectedValue = this.chapterSelect.value;
    
    if (selectedValue === 'all') {
      this.renderAllChapters(this.cachedData.chuong);
    } else {
      const chuongData = this.cachedData.chuong.find(c => c.chuong_id == selectedValue);
      if (chuongData) {
        this.renderSingleChapter(chuongData);
      }
    }
  }
  
  /**
   * Render tất cả các chương
   */
  renderAllChapters(danhSachChuong) {
    this.gradesContainer.innerHTML = '';
    danhSachChuong.forEach(chuong => {
      this.gradesContainer.appendChild(this.createChapterCard(chuong));
    });
  }
  
  /**
   * Render một chương duy nhất
   */
  renderSingleChapter(chuong) {
    this.gradesContainer.innerHTML = '';
    this.gradesContainer.appendChild(this.createChapterCard(chuong));
  }
  
  /**
   * Tạo HTML card cho một chương
   */
  createChapterCard(chuong) {
    const card = document.createElement('div');
    card.className = 'chapter-grade-card';
    card.setAttribute('data-chapter-id', chuong.chuong_id);
    
    card.innerHTML = `
      <h3 class="chapter-title">Chương ${chuong.so_thu_tu}: ${chuong.ten_chuong}</h3>
      
      <div class="grade-table">
        ${this.createHeaderRow()}
        ${this.createChuyenCanRows(chuong.diem_chuyen_can, chuong.chi_tiet_video)}
        ${this.createBaiTapRows(chuong.diem_bai_tap)}
        ${this.createKiemTraRows(chuong.diem_kiem_tra)}
        ${this.createTotalRow(chuong.diem_tong)}
      </div>
    `;
    
    return card;
  }
  
  createHeaderRow() {
    return `
      <div class="grade-row header-row">
        <div class="grade-cell type-cell">Loại điểm</div>
        <div class="grade-cell weight-cell">Trọng số</div>
        <div class="grade-cell score-cell">Điểm</div>
      </div>
    `;
  }
  
  createChuyenCanRows(diemCC, chiTiet) {
    const diem = diemCC.diem !== null ? diemCC.diem.toFixed(1) : '--';
    const badge = this.getScoreBadge(diemCC.diem);
    
    return `
      <div class="grade-row main-row">
        <div class="grade-cell type-cell"><strong>Chuyên cần (CC)</strong></div>
        <div class="grade-cell weight-cell">10%</div>
        <div class="grade-cell score-cell">
          <span class="score-badge ${badge}">${diem}/10</span>
        </div>
      </div>
      <div class="grade-row detail-row">
        <div class="grade-cell type-cell detail-text">
          <span class="detail-icon">↳</span> Video đã xem: ${diemCC.video_xem_xong}/${diemCC.tong_video}
        </div>
        <div class="grade-cell weight-cell"></div>
        <div class="grade-cell score-cell"></div>
      </div>
    `;
  }
  
  createBaiTapRows(diemBaiTap) {
    const diem = diemBaiTap.diem_trung_binh !== null ? diemBaiTap.diem_trung_binh.toFixed(1) : '--';
    const badge = this.getScoreBadge(diemBaiTap.diem_trung_binh);
    
    let detailRows = '';
    if (diemBaiTap.chi_tiet && diemBaiTap.chi_tiet.length > 0) {
      diemBaiTap.chi_tiet.forEach(bt => {
        const btDiem = bt.diem !== null ? (bt.diem / bt.diem_toi_da * 10).toFixed(1) + '/10' : '<span class="status-badge pending">Chưa nộp</span>';
        detailRows += `
          <div class="grade-row detail-row">
            <div class="grade-cell type-cell detail-text">
              <span class="detail-icon">↳</span> ${bt.tieu_de}
            </div>
            <div class="grade-cell weight-cell"></div>
            <div class="grade-cell score-cell">${btDiem}</div>
          </div>
        `;
      });
    }
    
    return `
      <div class="grade-row main-row">
        <div class="grade-cell type-cell"><strong>Bài tập</strong></div>
        <div class="grade-cell weight-cell">40%</div>
        <div class="grade-cell score-cell">
          <span class="score-badge ${badge}">${diem}/10</span>
        </div>
      </div>
      ${detailRows}
    `;
  }
  
  createKiemTraRows(diemKiemTra) {
    const diem = diemKiemTra.diem !== null ? diemKiemTra.diem.toFixed(1) : '--';
    const badge = diemKiemTra.diem !== null ? this.getScoreBadge(diemKiemTra.diem) : 'pending';
    const displayDiem = diemKiemTra.diem !== null ? `<span class="score-badge ${badge}">${diem}/10</span>` : '<span class="status-badge pending">Chưa làm</span>';
    
    return `
      <div class="grade-row main-row">
        <div class="grade-cell type-cell"><strong>Kiểm tra cuối chương</strong></div>
        <div class="grade-cell weight-cell">50%</div>
        <div class="grade-cell score-cell">${displayDiem}</div>
      </div>
    `;
  }
  
  createTotalRow(diemTong) {
    const diem = diemTong !== null ? diemTong.toFixed(1) + '/10' : '<span class="score-badge pending">Chưa đủ dữ liệu</span>';
    
    return `
      <div class="grade-row total-row">
        <div class="grade-cell type-cell"><strong>ĐIỂM CHƯƠNG</strong></div>
        <div class="grade-cell weight-cell">100%</div>
        <div class="grade-cell score-cell">
          ${diemTong !== null ? '<span class="score-badge total">' + diem + '</span>' : diem}
        </div>
      </div>
    `;
  }
  
  /**
   * Lấy class cho score badge dựa trên điểm
   */
  getScoreBadge(diem) {
    if (diem === null) return 'pending';
    if (diem >= 9) return 'excellent';
    if (diem >= 8) return 'good';
    if (diem >= 6.5) return 'average';
    return 'poor';
  }
  
  /**
   * Hiển thị lỗi
   */
  showError(message) {
    if (this.gradesContainer) {
      this.gradesContainer.innerHTML = `
        <div class="error-message" style="padding: 40px; text-align: center; color: #dc3545;">
          <h3>⚠️ Lỗi</h3>
          <p>${message}</p>
        </div>
      `;
    }
  }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
  new BangDiemManager();
});
