class ThongTinBaiGiangManager {
  constructor() {
    this.lopHocId = this.getUrlParam('lop_hoc_id');
    this.chuongId = this.getUrlParam('chuong_id');
    this.chuongData = null;
    this.baiHocList = [];
  }

  getUrlParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
  }

  async initialize() {
    if (!this.lopHocId || !this.chuongId) {
      console.error('Thiếu lop_hoc_id hoặc chuong_id trong URL');
      return;
    }

    try {
      // Load dữ liệu
      await Promise.all([
        this.loadThongTinChuong(),
        this.loadDanhSachBaiHoc()
      ]);

      // Render
      this.renderThongTinChuong();
      this.renderDanhSachBaiHoc();

      console.log('✅ Đã load xong dữ liệu trang Thông tin bài giảng');
    } catch (error) {
      console.error('Lỗi khởi tạo trang:', error);
    }
  }

  /**
   * BƯỚC 1: Load thông tin chương
   */
  async loadThongTinChuong() {
    const response = await fetch(
      `/backend/student/api/danh-sach-chuong.php?lop_hoc_id=${this.lopHocId}`,
      {
        method: 'GET',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' }
      }
    );

    const data = await response.json();

    if (data.thanh_cong && Array.isArray(data.du_lieu)) {
      // Tìm chương theo chuong_id
      this.chuongData = data.du_lieu.find(
        c => c.id == this.chuongId
      );

      if (!this.chuongData) {
        throw new Error('Không tìm thấy chương');
      }
    } else {
      throw new Error('API trả về lỗi: ' + (data.thong_bao || 'Unknown'));
    }
  }

  /**
   * BƯỚC 2: Load danh sách bài học
   */
  async loadDanhSachBaiHoc() {
    const response = await fetch(
      `/backend/student/api/danh-sach-bai-hoc.php?chuong_id=${this.chuongId}&lop_hoc_id=${this.lopHocId}`,
      {
        method: 'GET',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' }
      }
    );

    const data = await response.json();

    if (data.thanh_cong && Array.isArray(data.du_lieu)) {
      this.baiHocList = data.du_lieu;
    } else {
      throw new Error('API trả về lỗi: ' + (data.thong_bao || 'Unknown'));
    }
  }

  /**
   * Render thông tin chương
   */
  renderThongTinChuong() {
    if (!this.chuongData) return;

    const { so_thu_tu_chuong, ten_chuong, noi_dung, muc_tieu, thong_ke, tien_do } = this.chuongData;

    // 1. Render tiêu đề chương
    const titleEl = document.querySelector('.chapter-title');
    if (titleEl) {
      // Kiểm tra nếu ten_chuong đã có "Chương X:" thì không thêm nữa
      const hasChapterPrefix = /^Chương\s+\d+:/i.test(ten_chuong);
      titleEl.textContent = hasChapterPrefix ? ten_chuong : `Chương ${so_thu_tu_chuong}: ${ten_chuong}`;
    }

    // 2. Render stats
    document.getElementById('stat-time').textContent = `${tien_do?.tong_phut_bai_giang || 0} phút`;
    document.getElementById('stat-video').textContent = `${thong_ke?.tong_video || 0} video`;
    document.getElementById('stat-exercise').textContent = `${thong_ke?.tong_bai_tap || 0} bài tập`;
    document.getElementById('stat-exam').textContent = `${thong_ke?.tong_bai_kiem_tra || 0} bài kiểm tra`;

    // 3. Render nội dung chương
    const noiDungEl = document.getElementById('noi-dung-chuong');
    if (noiDungEl) {
      noiDungEl.textContent = noi_dung || 'Chưa có nội dung';
    }

    // 4. Render mục tiêu
    const mucTieuEl = document.getElementById('muc-tieu-chuong');
    if (mucTieuEl && muc_tieu) {
      mucTieuEl.innerHTML = '';
      
      // Nếu muc_tieu là string, split thành array
      let mucTieuList = [];
      if (typeof muc_tieu === 'string') {
        mucTieuList = muc_tieu.split('\n').filter(m => m.trim());
      } else if (Array.isArray(muc_tieu)) {
        mucTieuList = muc_tieu;
      }

      if (mucTieuList.length === 0) {
        mucTieuEl.innerHTML = '<li>Chưa có mục tiêu</li>';
      } else {
        mucTieuList.forEach(item => {
          const li = document.createElement('li');
          li.textContent = item.replace(/^[-•*]\s*/, ''); // Remove bullet nếu có
          mucTieuEl.appendChild(li);
        });
      }
    }
  }

  /**
   * Render danh sách bài học
   * Logic: Hiển thị video, sau đó các bài tập thuộc video đó ngay bên dưới
   */
  renderDanhSachBaiHoc() {
    const container = document.getElementById('danh-sach-bai-hoc');
    if (!container) return;

    // Xóa nội dung cũ
    container.innerHTML = '';

    // Tách video, bài tập, kiểm tra
    const videos = this.baiHocList.filter(b => b.loai === 'video');
    const baiTaps = this.baiHocList.filter(b => b.loai === 'bai_tap');
    const kiemTras = this.baiHocList.filter(b => b.loai === 'bai_kiem_tra');

    // Render từng video + bài tập của nó
    videos.forEach(video => {
      // Render video
      const videoEl = this.createBaiHocElement(video);
      container.appendChild(videoEl);

      // Render các bài tập thuộc video này
      const baiTapsCuaVideo = baiTaps.filter(bt => bt.bai_giang_id == video.id);
      baiTapsCuaVideo.forEach(baiTap => {
        const baiTapEl = this.createBaiHocElement(baiTap);
        container.appendChild(baiTapEl);
      });
    });

    // Render bài tập không thuộc video nào (bai_giang_id = null)
    const baiTapKhongCoVideo = baiTaps.filter(bt => !bt.bai_giang_id);
    baiTapKhongCoVideo.forEach(baiTap => {
      const baiTapEl = this.createBaiHocElement(baiTap);
      container.appendChild(baiTapEl);
    });

    // Render kiểm tra
    kiemTras.forEach(kiemTra => {
      const kiemTraEl = this.createBaiHocElement(kiemTra);
      container.appendChild(kiemTraEl);
    });
  }

  /**
   * Tạo element cho 1 bài học
   */
  createBaiHocElement(bai) {
    const { id, loai, tieu_de, tien_do } = bai;
    
    // Xác định icon, label, metadata
    let icon, label, metadata, link, wrapperClass;
    
    if (loai === 'video') {
      icon = './assets/frame-10000019960.svg';
      label = 'Video';
      // Format: 10p30s hoặc 2p5s - chỉ hiển thị khi có thời lượng
      if (bai.thoi_luong_giay && bai.thoi_luong_giay > 0) {
        const phut = Math.floor(bai.thoi_luong_giay / 60);
        const giay = bai.thoi_luong_giay % 60;
        metadata = giay > 0 ? `${phut}p${giay}s` : `${phut}p`;
      } else {
        metadata = null;
      }
      link = `Thông%20tin%20bài%20học.html?bai_id=${id}&loai=video&chuong_id=${this.chuongId}&lop_hoc_id=${this.lopHocId}`;
      wrapperClass = 'frame-1000002002';
    } else if (loai === 'bai_tap') {
      icon = './assets/exam2.svg';
      label = 'Bài tập luyện tập';
      metadata = bai.so_cau_hoi ? `${bai.so_cau_hoi} câu` : '0 câu';
      link = `Bài%20tập.html?bai_tap_id=${id}&chuong_id=${this.chuongId}&lop_hoc_id=${this.lopHocId}`;
      wrapperClass = 'frame-1000002003';
    } else if (loai === 'bai_kiem_tra') {
      icon = './assets/exam2.svg';
      label = 'Bài kiểm tra';
      // Hiển thị cả thời lượng và số câu
      const thoiLuong = bai.thoi_luong_giay ? `${bai.thoi_luong_giay}p` : '';
      const soCau = bai.so_cau_hoi ? `${bai.so_cau_hoi} câu` : '';
      metadata = [thoiLuong, soCau].filter(Boolean).join(' • ');
      link = `Bài%20kiểm%20tra.html?bai_kiem_tra_id=${id}&chuong_id=${this.chuongId}&lop_hoc_id=${this.lopHocId}`;
      wrapperClass = 'frame-1000002003';
    }

    // tien_do là số nguyên từ 0-100 (không phải object)
    const phanTram = tien_do || 0;

    // Tạo HTML - MỖI BÀI LÀ 1 WRAPPER RIÊNG (để xếp dọc)
    const wrapper = document.createElement('div');
    wrapper.className = wrapperClass;
    
    if (loai === 'video') {
      // Video không có .bao wrapper
      wrapper.innerHTML = `
        <div class="frame-1000002001" onclick="window.location.href='${link}'" style="cursor: pointer;">
          <img class="frame-1000001996" src="${icon}" />
          <div class="frame-1000001998">
            <div class="_1-1-get-to-know-each-other">${this.escapeHtml(tieu_de)}</div>
            <div class="frame-1000001997">
              <div class="video">${label}</div>
              ${metadata ? `<div class="ellipse-994"></div><div class="_15-ph-t">${metadata}</div>` : ''}
            </div>
          </div>
        </div>
        <div class="group-32">
          <div class="_803">${phanTram}%</div>
          <div class="ellipse-9963"></div>
          <div class="ellipse-9973"></div>
        </div>
      `;
    } else {
      // Bài tập và kiểm tra có .bao wrapper
      wrapper.innerHTML = `
        <div class="bao">
          <img class="vector-41" src="./assets/vector-410.svg">
          <div class="frame-1000002001" onclick="window.location.href='${link}'" style="cursor: pointer;">
            <img class="exam3" src="${icon}" />
            <div class="frame-1000001998">
              <div class="b-i-t-p-speak">${this.escapeHtml(tieu_de)}</div>
              <div class="frame-1000001997">
                <div class="b-i-t-p-luy-n-t-p">${label}</div>
                <div class="ellipse-994"></div>
                <div class="_8-b-i">${metadata}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="group-32">
          <div class="_803">${phanTram}%</div>
          <div class="ellipse-9963"></div>
          <div class="ellipse-9973"></div>
        </div>
      `;
    }

    return wrapper;
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
  const manager = new ThongTinBaiGiangManager();
  manager.initialize();
});
