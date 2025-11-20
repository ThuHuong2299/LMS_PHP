/**
 * File: thong-tin-bai-hoc.js
 * Mục đích: Quản lý trang thông tin bài học
 * - Load chi tiết bài giảng từ API
 * - Hiển thị video local (MP4)
 * - Load và hiển thị bình luận
 * - Thêm bình luận mới
 * - Tracking tiến độ xem video
 */

class BaiHocManager {
  constructor() {
    // Lấy tất cả các tham số có thể
    const urlParams = new URLSearchParams(window.location.search);
    
    // Debug: Log tất cả params
    console.log('=== DEBUG URL PARAMS ===');
    console.log('Full URL:', window.location.href);
    console.log('Search string:', window.location.search);
    
    for (const [key, value] of urlParams.entries()) {
      console.log(`Param: ${key} = ${value}`);
    }
    
    // Hỗ trợ cả bai_giang_id và bai_id (từ trang khác)
    this.baiGiangId = urlParams.get('bai_giang_id') || urlParams.get('bai_id');
    this.lopHocId = urlParams.get('lop_hoc_id') || urlParams.get('loai');
    
    // Fallback: nếu có chuong_id, có thể cần xử lý khác
    const chuongId = urlParams.get('chuong_id');
    
    console.log('=== EXTRACTED VALUES ===');
    console.log('baiGiangId:', this.baiGiangId);
    console.log('lopHocId:', this.lopHocId);
    console.log('chuongId:', chuongId);
    
    this.player = null;
    this.currentReplyTo = null;
    this.updateInterval = null;
    
    if (!this.baiGiangId || !this.lopHocId) {
      const errorMsg = `
Thiếu thông tin bài giảng!
- bai_giang_id: ${this.baiGiangId || 'KHÔNG CÓ'}
- lop_hoc_id: ${this.lopHocId || 'KHÔNG CÓ'}
- URL: ${window.location.search}

Vui lòng kiểm tra URL có đúng format:
?bai_giang_id=X&lop_hoc_id=Y
      `;
      console.error(errorMsg);
      
      // Hiển thị lỗi trên trang thay vì alert
      document.body.innerHTML = `
        <div style="padding: 40px; text-align: center; font-family: Arial;">
          <h2 style="color: red;">⚠️ Lỗi URL</h2>
          <pre style="background: #f5f5f5; padding: 20px; text-align: left; border-radius: 8px;">${errorMsg}</pre>
          <button onclick="history.back()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            ← Quay lại
          </button>
        </div>
      `;
      return;
    }
    
    console.log('=== INITIALIZATION START ===');
    this.init();
  }
  
  getUrlParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }
  
  async init() {
    try {
      console.log('Initializing BaiHocManager...');
      console.log('Bài giảng ID:', this.baiGiangId);
      console.log('Lớp học ID:', this.lopHocId);
      
      await this.loadChiTietBaiGiang();
      await this.loadBinhLuan();
      this.setupEventListeners();
    } catch (error) {
      console.error('Lỗi khởi tạo:', error);
      this.showError('Không thể tải dữ liệu bài học. Chi tiết lỗi: ' + error.message);
    }
  }
  
  async loadChiTietBaiGiang() {
    try {
      const apiUrl = `../../backend/student/api/chi-tiet-bai-giang.php?bai_giang_id=${this.baiGiangId}&lop_hoc_id=${this.lopHocId}`;
      
      console.log('=== LOADING BÀI GIẢNG ===');
      console.log('API URL:', apiUrl);
      console.log('Full URL:', new URL(apiUrl, window.location.href).href);
      
      console.log('Bắt đầu fetch...');
      const response = await fetch(apiUrl);
      console.log('✓ Fetch hoàn tất');
      
      console.log('Response status:', response.status);
      console.log('Response ok:', response.ok);
      console.log('Response type:', response.type);
      
      // Đọc text trước để debug
      console.log('Đọc response text...');
      const responseText = await response.text();
      console.log('=== RESPONSE TEXT ===');
      console.log(responseText);
      console.log('=== END RESPONSE ===');
      
      if (!response.ok) {
        throw new Error(`HTTP Error ${response.status}: ${responseText.substring(0, 500)}`);
      }
      
      // Parse JSON
      let data;
      try {
        console.log('Parse JSON...');
        data = JSON.parse(responseText);
        console.log('✓ Parse thành công');
      } catch (parseError) {
        console.error('❌ JSON Parse Error:', parseError);
        console.error('Response không phải JSON:', responseText.substring(0, 500));
        throw new Error('Server trả về dữ liệu không hợp lệ (không phải JSON)');
      }
      
      console.log('=== API DATA ===');
      console.log('Full response:', data);
      console.log('Status (new format):', data.thanh_cong);
      console.log('Status (old format):', data.status);
      console.log('Message:', data.message || data.thong_bao);
      console.log('Data:', data.data || data.du_lieu);
      console.log('=== END DATA ===');
      
      // Hỗ trợ cả 2 format API: cũ và mới
      const isSuccess = data.status === 'success' || data.thanh_cong === true;
      const apiData = data.data || data.du_lieu;
      const apiMessage = data.message || data.thong_bao;
      
      if (isSuccess && apiData) {
        console.log('✓ API success, render bài giảng...');
        this.renderBaiGiang(apiData);
      } else {
        console.error('❌ API error:', apiMessage);
        throw new Error(apiMessage || 'API trả về lỗi không xác định');
      }
    } catch (error) {
      console.error('=== LỖI LOAD BÀI GIẢNG ===');
      console.error('Error type:', error.constructor.name);
      console.error('Error message:', error.message);
      console.error('Error stack:', error.stack);
      console.error('=== END ERROR ===');
      
      ThongBao.loi('Lỗi tải bài giảng: ' + error.message);
      throw error;
    }
  }
  
  renderBaiGiang(data) {
    const { bai_giang, thong_tin_chuong } = data;
    
    // Cập nhật tiêu đề chương (xử lý trường hợp ten_chuong đã có prefix "Chương X:")
    let tenChuong = thong_tin_chuong.ten_chuong;
    const regex = /^Chương\s+\d+:\s*/i;
    if (regex.test(tenChuong)) {
      // Nếu đã có prefix "Chương X:", chỉ hiển thị như vậy
      document.getElementById('chapter-subtitle').textContent = tenChuong;
    } else {
      // Nếu chưa có, ghép "Chương X:" vào trước
      document.getElementById('chapter-subtitle').textContent = 
        `Chương ${thong_tin_chuong.so_thu_tu_chuong}: ${tenChuong}`;
    }
    
    document.getElementById('lesson-heading').textContent = bai_giang.tieu_de;
    
    // Load video local
    if (bai_giang.duong_dan_video) {
      this.loadLocalVideo(bai_giang.duong_dan_video);
    }
  }
  
  loadLocalVideo(url) {
    console.log('Loading local video:', url);
    
    const videoPlayer = document.getElementById('video-player');
    const videoSource = document.getElementById('video-source');
    
    if (!videoPlayer || !videoSource) {
      console.error('Không tìm thấy video player hoặc source element');
      return;
    }
    
    // Set video source
    videoSource.src = url;
    videoPlayer.load();
    
    this.player = videoPlayer;
    
    // Event listeners cho HTML5 video
    videoPlayer.addEventListener('loadedmetadata', () => {
      console.log('✓ Video metadata loaded');
      console.log('Duration:', videoPlayer.duration);
    });
    
    videoPlayer.addEventListener('play', () => {
      console.log('Video playing');
      this.capNhatTienDoVideo('dang_xem');
      
      // Bắt đầu interval cập nhật tiến độ
      if (this.updateInterval) {
        clearInterval(this.updateInterval);
      }
      this.updateInterval = setInterval(() => {
        this.updateTienDo();
      }, 10000); // Cập nhật mỗi 10 giây
    });
    
    videoPlayer.addEventListener('pause', () => {
      console.log('Video paused');
      if (this.updateInterval) {
        clearInterval(this.updateInterval);
      }
    });
    
    videoPlayer.addEventListener('ended', () => {
      console.log('Video ended');
      this.capNhatTienDoVideo('xem_xong', true);
      if (this.updateInterval) {
        clearInterval(this.updateInterval);
      }
    });
    
    videoPlayer.addEventListener('error', (e) => {
      console.error('Video error:', e);
      ThongBao.loi('Không thể phát video. Vui lòng kiểm tra đường dẫn.');
    });
  }
  
  updateTienDo() {
    if (!this.player) {
      console.warn('Player chưa sẵn sàng');
      return;
    }
    
    try {
      const currentTime = this.player.currentTime;
      const duration = this.player.duration;
      
      if (isNaN(currentTime) || isNaN(duration) || duration <= 0) {
        console.warn('Invalid video duration or time');
        return;
      }
      
      const percentage = (currentTime / duration * 100);
      
      console.log('Update tiến độ:', {
        currentTime: Math.round(currentTime),
        duration: Math.round(duration),
        percentage: percentage.toFixed(2)
      });
      
      this.capNhatTienDoVideo('dang_xem', false, Math.round(currentTime), percentage.toFixed(2));
    } catch (error) {
      console.error('Lỗi cập nhật tiến độ:', error);
    }
  }
  
  async capNhatTienDoVideo(trangThai, isCompleted = false, thoiGianXem = 0, phanTram = 0) {
    try {
      const body = {
        bai_giang_id: parseInt(this.baiGiangId),
        trang_thai: trangThai,
        thoi_gian_xem: thoiGianXem,
        phan_tram_hoan_thanh: isCompleted ? 100 : parseFloat(phanTram)
      };
      
      await fetch('../../backend/student/api/cap-nhat-tien-do-video.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
      });
    } catch (error) {
      console.error('Lỗi cập nhật tiến độ:', error);
    }
  }
  
  async loadBinhLuan(page = 1) {
    try {
      const apiUrl = `../../backend/student/api/binh-luan-bai-giang.php?bai_giang_id=${this.baiGiangId}&page=${page}&limit=20`;
      console.log('=== LOADING BÌNH LUẬN ===');
      console.log('API URL:', apiUrl);
      
      const response = await fetch(apiUrl);
      const responseText = await response.text();
      console.log('Response text:', responseText);
      
      const data = JSON.parse(responseText);
      console.log('Parsed data:', data);
      
      // Hỗ trợ cả 2 format
      const isSuccess = data.status === 'success' || data.thanh_cong === true;
      const apiData = data.data || data.du_lieu;
      
      console.log('Is success:', isSuccess);
      console.log('API data:', apiData);
      
      if (isSuccess && apiData) {
        this.renderBinhLuan(apiData);
      } else {
        throw new Error(data.message || data.thong_bao || 'Không thể load bình luận');
      }
    } catch (error) {
      console.error('Lỗi load bình luận:', error);
      const commentsList = document.getElementById('comments-list');
      if (commentsList) {
        commentsList.innerHTML = '<div class="error-message">Không thể tải bình luận: ' + error.message + '</div>';
      }
    }
  }
  
  renderBinhLuan(data) {
    console.log('=== RENDER BÌNH LUẬN ===');
    console.log('Data received:', data);
    
    const binh_luan = data.binh_luan || [];
    const pagination = data.pagination || { total: 0 };
    
    console.log('Số bình luận:', binh_luan.length);
    console.log('Total từ pagination:', pagination.total);
    
    const container = document.getElementById('comments-list');
    const countElement = document.getElementById('comment-count');
    
    if (!container) {
      console.error('Không tìm thấy element #comments-list');
      return;
    }
    
    // Cập nhật số lượng
    if (countElement) {
      countElement.textContent = pagination.total;
    }
    
    if (binh_luan.length === 0) {
      container.innerHTML = '<div class="no-comments" style="padding: 20px; text-align: center; color: #666;">Chưa có bình luận nào. Hãy là người đầu tiên!</div>';
      return;
    }
    
    container.innerHTML = '';
    
    binh_luan.forEach((comment, index) => {
      console.log(`Render comment ${index + 1}:`, comment);
      const commentHtml = this.createCommentElement(comment);
      container.appendChild(commentHtml);
    });
    
    console.log('✓ Render xong', binh_luan.length, 'bình luận');
  }
  
  createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'comment-wrapper';
    
    const mainComment = this.createSingleComment(comment, false);
    div.appendChild(mainComment);
    
    // Thêm nút toggle replies nếu có phản hồi
    if (comment.so_phan_hoi > 0) {
      const toggleBtn = document.createElement('button');
      toggleBtn.className = 'replies-toggle';
      toggleBtn.textContent = `${comment.so_phan_hoi} phản hồi ▼`;
      toggleBtn.onclick = () => this.toggleReplies(toggleBtn, `replies-${comment.id}`);
      div.appendChild(toggleBtn);
      
      // Container cho replies
      const repliesContainer = document.createElement('div');
      repliesContainer.id = `replies-${comment.id}`;
      repliesContainer.className = 'replies-container hidden';
      
      comment.phan_hoi.forEach(reply => {
        const replyElement = this.createSingleComment(reply, true);
        repliesContainer.appendChild(replyElement);
      });
      
      div.appendChild(repliesContainer);
    }
    
    return div;
  }
  
  createSingleComment(comment, isReply) {
    const div = document.createElement('div');
    div.className = 'comment';
    if (isReply) div.style.marginLeft = '50px';
    
    div.innerHTML = `
      <div class="comment-avatar"></div>
      <div class="comment-content">
        <div class="comment-header">
          <span class="comment-author">${comment.nguoi_gui_ho_ten}</span>
          ${comment.nguoi_gui_vai_tro === 'giang_vien' ? '<span class="badge-gv">Giảng viên</span>' : ''}
          <div class="comment-time">
            <span>${this.formatTime(comment.thoi_gian_gui)}</span>
            <span>${this.formatDate(comment.thoi_gian_gui)}</span>
          </div>
        </div>
        <div class="comment-text">${this.escapeHtml(comment.noi_dung)}</div>
        <span class="reply-button" data-comment-id="${comment.id}">Phản hồi</span>
      </div>
    `;
    
    return div;
  }
  
  toggleReplies(button, repliesId) {
    const repliesContainer = document.getElementById(repliesId);
    
    if (repliesContainer.classList.contains('hidden')) {
      repliesContainer.classList.remove('hidden');
      button.textContent = button.textContent.replace('▼', '▲');
    } else {
      repliesContainer.classList.add('hidden');
      button.textContent = button.textContent.replace('▲', '▼');
    }
  }
  
  setupEventListeners() {
    // Nút gửi bình luận
    const sendBtn = document.getElementById('send-comment-btn');
    sendBtn.addEventListener('click', () => this.themBinhLuan());
    
    // Enter để gửi (Ctrl+Enter)
    const input = document.getElementById('comment-input');
    input.addEventListener('keydown', (e) => {
      if (e.ctrlKey && e.key === 'Enter') {
        this.themBinhLuan();
      }
    });
    
    // Nút phản hồi (delegate event)
    document.getElementById('comments-list').addEventListener('click', (e) => {
      if (e.target.classList.contains('reply-button')) {
        const commentId = e.target.dataset.commentId;
        this.startReply(commentId);
      }
    });
  }
  
  startReply(commentId) {
    this.currentReplyTo = commentId;
    const input = document.getElementById('comment-input');
    input.placeholder = `Phản hồi bình luận #${commentId}...`;
    input.focus();
    
    // Thêm nút hủy
    const addCommentBox = document.querySelector('.add-comment-box');
    let cancelBtn = addCommentBox.querySelector('.cancel-reply-btn');
    
    if (!cancelBtn) {
      cancelBtn = document.createElement('button');
      cancelBtn.className = 'cancel-reply-btn';
      cancelBtn.textContent = 'Hủy phản hồi';
      cancelBtn.onclick = () => this.cancelReply();
      addCommentBox.querySelector('.add-comment-header').appendChild(cancelBtn);
    }
  }
  
  cancelReply() {
    this.currentReplyTo = null;
    const input = document.getElementById('comment-input');
    input.placeholder = 'Bình luận của bạn ...';
    
    const cancelBtn = document.querySelector('.cancel-reply-btn');
    if (cancelBtn) cancelBtn.remove();
  }
  
  async themBinhLuan() {
    const input = document.getElementById('comment-input');
    const noiDung = input.value.trim();
    
    if (!noiDung) {
      ThongBao.canh_bao('Vui lòng nhập nội dung bình luận');
      return;
    }
    
    try {
      const body = {
        bai_giang_id: parseInt(this.baiGiangId),
        noi_dung: noiDung,
        binh_luan_cha_id: this.currentReplyTo ? parseInt(this.currentReplyTo) : null
      };
      
      console.log('Sending comment:', body);
      
      const response = await fetch('../../backend/student/api/them-binh-luan-bai-giang.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
      });
      
      const responseText = await response.text();
      console.log('Response text:', responseText);
      
      const data = JSON.parse(responseText);
      console.log('Parsed data:', data);
      
      // Hỗ trợ cả 2 format
      const isSuccess = data.status === 'success' || data.thanh_cong === true;
      
      if (isSuccess) {
        // Reset form
        input.value = '';
        this.cancelReply();
        
        // Reload bình luận
        await this.loadBinhLuan();
        
        ThongBao.thanh_cong('Thêm bình luận thành công!');
      } else {
        throw new Error(data.message || data.thong_bao || 'Không thể thêm bình luận');
      }
    } catch (error) {
      console.error('Lỗi thêm bình luận:', error);
      ThongBao.loi('Không thể thêm bình luận: ' + error.message);
    }
  }
  
  formatTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
  }
  
  formatDate(datetime) {
    const date = new Date(datetime);
    return date.toLocaleDateString('vi-VN');
  }
  
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  showError(message) {
    ThongBao.loi(message);
  }
}

// Khởi tạo khi DOM ready
document.addEventListener('DOMContentLoaded', () => {
  new BaiHocManager();
});
