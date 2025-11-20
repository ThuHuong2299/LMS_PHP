/**
 * File: thong-tin-bai-hoc.js (Teacher version)
 * Mục đích: Quản lý trang thông tin bài học cho giáo viên
 * - Load chi tiết bài giảng từ API
 * - Hiển thị video local (MP4)
 * - Load và hiển thị bình luận
 * - Thêm bình luận mới (từ phía giáo viên)
 */

class BaiHocManager {
  constructor() {
    const urlParams = new URLSearchParams(window.location.search);
    
    console.log('=== TEACHER - DEBUG URL PARAMS ===');
    console.log('Full URL:', window.location.href);
    console.log('Search string:', window.location.search);
    
    // Lấy params
    this.baiGiangId = urlParams.get('bai_giang_id') || urlParams.get('bai_id');
    this.lopHocId = urlParams.get('lop_hoc_id');
    
    console.log('=== EXTRACTED VALUES ===');
    console.log('baiGiangId:', this.baiGiangId);
    console.log('lopHocId:', this.lopHocId);
    
    this.currentReplyTo = null;
    
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
  
  async init() {
    try {
      console.log('Initializing BaiHocManager (Teacher)...');
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
      // Sử dụng API của teacher (đã có quyền truy cập)
      const apiUrl = `/backend/teacher/api/chi-tiet-bai-giang.php?bai_giang_id=${this.baiGiangId}&lop_hoc_id=${this.lopHocId}`;
      
      console.log('=== LOADING BÀI GIẢNG (TEACHER) ===');
      console.log('API URL:', apiUrl);
      console.log('Full URL:', window.location.origin + apiUrl);
      
      const response = await fetch(apiUrl);
      console.log('Response status:', response.status);
      
      const responseText = await response.text();
      console.log('Response text:', responseText.substring(0, 200));
      
      if (!response.ok) {
        throw new Error(`HTTP Error ${response.status}`);
      }
      
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON Parse Error:', parseError);
        throw new Error('Server trả về dữ liệu không hợp lệ');
      }
      
      console.log('API Data:', data);
      
      const isSuccess = data.status === 'success' || data.thanh_cong === true;
      const apiData = data.data || data.du_lieu;
      const apiMessage = data.message || data.thong_bao;
      
      if (isSuccess && apiData) {
        console.log('✓ API success, render bài giảng...');
        this.renderBaiGiang(apiData);
      } else {
        console.error('API error:', apiMessage);
        throw new Error(apiMessage || 'API trả về lỗi');
      }
    } catch (error) {
      console.error('=== LỖI LOAD BÀI GIẢNG ===');
      console.error('Error:', error);
      throw error;
    }
  }
  
  renderBaiGiang(data) {
    console.log('=== RENDER BÀI GIẢNG (TEACHER) ===');
    console.log('Data:', data);
    
    // Update breadcrumb và heading
    const chapterSubtitle = document.getElementById('chapter-subtitle');
    const lessonHeading = document.getElementById('lesson-heading');
    
    if (chapterSubtitle) {
      chapterSubtitle.textContent = data.ten_chuong || 'Chương';
    }
    
    if (lessonHeading) {
      lessonHeading.textContent = data.tieu_de || 'Bài giảng';
    }
    
    // Setup video
    if (data.duong_dan_video) {
      this.setupVideo(data.duong_dan_video);
    } else {
      this.showNoVideoMessage();
    }
  }
  
  setupVideo(videoPath) {
    const videoPlayer = document.getElementById('video-player');
    const videoSource = document.getElementById('video-source');
    
    if (!videoPlayer || !videoSource) {
      console.error('Video player elements not found');
      return;
    }
    
    // Xử lý đường dẫn video - thêm /public vào đầu
    let fullVideoPath = videoPath;
    
    if (videoPath.startsWith('http')) {
      // Giữ nguyên nếu là URL đầy đủ
      fullVideoPath = videoPath;
    } else if (videoPath.startsWith('/public/')) {
      // Đã có /public/ thì giữ nguyên
      fullVideoPath = videoPath;
    } else if (videoPath.startsWith('public/')) {
      // Thêm / vào đầu
      fullVideoPath = '/' + videoPath;
    } else if (videoPath.startsWith('../assets/') || videoPath.startsWith('assets/')) {
      // Đường dẫn tương đối từ DB - chuyển thành /public/assets/...
      const cleanPath = videoPath.replace(/^\.\.\//, '').replace(/^assets\//, 'assets/');
      fullVideoPath = '/public/' + cleanPath;
    } else if (videoPath.startsWith('/assets/')) {
      // Thiếu /public - thêm vào
      fullVideoPath = '/public' + videoPath;
    } else if (!videoPath.startsWith('/')) {
      // Đường dẫn không có / - thêm /public/
      fullVideoPath = '/public/' + videoPath;
    }
    
    console.log('Original video path:', videoPath);
    console.log('Full video path:', fullVideoPath);
    
    videoSource.src = fullVideoPath;
    videoPlayer.load();
    
    // Event listeners cho video (không cần tracking tiến độ cho GV)
    videoPlayer.addEventListener('loadedmetadata', () => {
      console.log('Video loaded successfully');
      console.log('Duration:', videoPlayer.duration);
    });
    
    videoPlayer.addEventListener('error', (e) => {
      console.error('Video error:', e);
      this.showVideoError();
    });
  }
  
  showNoVideoMessage() {
    const videoContainer = document.querySelector('.video-container');
    if (videoContainer) {
      videoContainer.innerHTML = `
        <div style="text-align: center; color: #999;">
          <p style="font-size: 18px;">Bài giảng này chưa có video</p>
        </div>
      `;
    }
  }
  
  showVideoError() {
    const videoContainer = document.querySelector('.video-container');
    if (videoContainer) {
      videoContainer.innerHTML = `
        <div style="text-align: center; color: #e74c3c;">
          <p style="font-size: 18px;">⚠️ Không thể tải video</p>
          <p style="font-size: 14px; margin-top: 10px;">Vui lòng kiểm tra đường dẫn video</p>
        </div>
      `;
    }
  }
  
  async loadBinhLuan() {
    try {
      // Sử dụng API của teacher
      const apiUrl = `/backend/teacher/api/binh-luan-bai-giang.php?bai_giang_id=${this.baiGiangId}`;
      
      console.log('=== LOADING BÌNH LUẬN ===');
      console.log('API URL:', apiUrl);
      
      const response = await fetch(apiUrl);
      const data = await response.json();
      
      console.log('Comments data:', data);
      
      if (data.status === 'success' || data.thanh_cong) {
        const apiData = data.data || data.du_lieu;
        
        // API trả về object có cấu trúc: { binh_luan: [...], pagination: {...} }
        let comments = [];
        if (Array.isArray(apiData)) {
          comments = apiData;
        } else if (apiData && apiData.binh_luan) {
          comments = apiData.binh_luan;
        }
        
        console.log('Parsed comments:', comments);
        this.renderBinhLuan(comments);
      } else {
        console.error('Load bình luận thất bại:', data.message || data.thong_bao);
        this.showNoComments();
      }
    } catch (error) {
      console.error('Lỗi load bình luận:', error);
      this.showNoComments();
    }
  }
  
  renderBinhLuan(comments) {
    const commentsList = document.getElementById('comments-list');
    const commentCount = document.getElementById('comment-count');
    
    if (!commentsList) return;
    
    // Đếm tổng số comments (bao gồm cả replies/phan_hoi)
    let totalCount = comments.length;
    comments.forEach(comment => {
      const replies = comment.phan_hoi || comment.replies || [];
      totalCount += replies.length;
    });
    
    if (commentCount) {
      commentCount.textContent = totalCount;
    }
    
    if (comments.length === 0) {
      this.showNoComments();
      return;
    }
    
    commentsList.innerHTML = comments.map(comment => this.createCommentHTML(comment)).join('');
    
    // Setup event listeners cho replies toggle
    this.setupRepliesToggle();
  }
  
  createCommentHTML(comment) {
    // API trả về field phan_hoi (không phải replies)
    const replies = comment.phan_hoi || comment.replies || [];
    const hasReplies = replies.length > 0;
    const repliesHTML = hasReplies ? 
      replies.map(reply => this.createReplyHTML(reply)).join('') : '';
    
    // Lấy thông tin từ các field của API
    const commentId = comment.id || comment.binh_luan_id;
    const authorName = comment.nguoi_gui_ho_ten || comment.ho_ten || 'Người dùng';
    const commentTime = comment.thoi_gian_gui || comment.thoi_gian_tao;
    const userRole = comment.nguoi_gui_vai_tro || comment.vai_tro;
    
    // Avatar mặc định dựa vào vai trò
    let defaultAvatar = '/public/student/CSS/avatar-sv.webp';
    if (userRole === 'giang_vien') {
      defaultAvatar = '/public/student/CSS/avatar-gv.jpg';
    }
    const avatar = comment.nguoi_gui_anh || comment.avatar || defaultAvatar;
    
    return `
      <div class="comment">
        <div class="comment-avatar" style="background-image: url('${avatar}');"></div>
        <div class="comment-content">
          <div class="comment-header">
            <div class="comment-author">${this.escapeHtml(authorName)}</div>
            <div class="comment-time">${this.formatTime(commentTime)}</div>
          </div>
          <div class="comment-text">${this.escapeHtml(comment.noi_dung)}</div>
          <button class="reply-button" onclick="baiHocManager.showReplyBox(${commentId})">
            Trả lời
          </button>
        </div>
      </div>
      ${hasReplies ? `
        <button class="replies-toggle" data-comment-id="${commentId}">
          Xem ${replies.length} câu trả lời
        </button>
        <div class="replies-container hidden" id="replies-${commentId}">
          ${repliesHTML}
        </div>
      ` : ''}
    `;
  }
  
  createReplyHTML(reply) {
    const authorName = reply.nguoi_gui_ho_ten || reply.ho_ten || 'Người dùng';
    const replyTime = reply.thoi_gian_gui || reply.thoi_gian_tao;
    const userRole = reply.nguoi_gui_vai_tro || reply.vai_tro;
    
    // Avatar mặc định dựa vào vai trò
    let defaultAvatar = '/public/assets/avatar-sv.webp';
    if (userRole === 'giang_vien') {
      defaultAvatar = '/public/assets/avatar-gv.jpg';
    }
    const avatar = reply.nguoi_gui_anh || reply.avatar || defaultAvatar;
    
    return `
      <div class="reply">
        <div class="comment-avatar" style="background-image: url('${avatar}');"></div>
        <div class="comment-content">
          <div class="comment-header">
            <div class="comment-author">${this.escapeHtml(authorName)}</div>
            <div class="comment-time">${this.formatTime(replyTime)}</div>
          </div>
          <div class="comment-text">${this.escapeHtml(reply.noi_dung)}</div>
        </div>
      </div>
    `;
  }
  
  setupRepliesToggle() {
    const toggleButtons = document.querySelectorAll('.replies-toggle');
    
    toggleButtons.forEach(button => {
      button.addEventListener('click', () => {
        const commentId = button.getAttribute('data-comment-id');
        const repliesContainer = document.getElementById(`replies-${commentId}`);
        
        if (repliesContainer) {
          repliesContainer.classList.toggle('hidden');
          
          if (repliesContainer.classList.contains('hidden')) {
            button.textContent = `Xem ${repliesContainer.querySelectorAll('.reply').length} câu trả lời`;
          } else {
            button.textContent = 'Ẩn câu trả lời';
          }
        }
      });
    });
  }
  
  showNoComments() {
    const commentsList = document.getElementById('comments-list');
    if (commentsList) {
      commentsList.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #999;">
          <p>Chưa có bình luận nào</p>
        </div>
      `;
    }
  }
  
  setupEventListeners() {
    const sendButton = document.getElementById('send-comment-btn');
    const commentInput = document.getElementById('comment-input');
    
    if (sendButton) {
      sendButton.addEventListener('click', () => this.sendComment());
    }
    
    if (commentInput) {
      commentInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          this.sendComment();
        }
      });
    }
  }
  
  async sendComment() {
    const commentInput = document.getElementById('comment-input');
    const noiDung = commentInput.value.trim();
    
    if (!noiDung) {
      alert('Vui lòng nhập nội dung bình luận');
      return;
    }
    
    try {
      // Giáo viên dùng API teacher
      const apiUrl = '/backend/teacher/api/them-binh-luan-bai-giang.php';
      
      const response = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          bai_giang_id: this.baiGiangId,
          noi_dung: noiDung,
          binh_luan_cha_id: this.currentReplyTo
        })
      });
      
      const data = await response.json();
      
      if (data.status === 'success' || data.thanh_cong) {
        if (typeof ThongBao !== 'undefined') {
          ThongBao.thanh_cong('Gửi bình luận thành công');
        } else {
          alert('Gửi bình luận thành công');
        }
        commentInput.value = '';
        this.currentReplyTo = null;
        await this.loadBinhLuan();
      } else {
        const errorMsg = data.message || data.thong_bao || 'Gửi bình luận thất bại';
        if (typeof ThongBao !== 'undefined') {
          ThongBao.loi(errorMsg);
        } else {
          alert(errorMsg);
        }
      }
    } catch (error) {
      console.error('Lỗi gửi bình luận:', error);
      if (typeof ThongBao !== 'undefined') {
        ThongBao.loi('Không thể gửi bình luận');
      } else {
        alert('Không thể gửi bình luẫn');
      }
    }
  }
  
  showReplyBox(commentId) {
    this.currentReplyTo = commentId;
    const commentInput = document.getElementById('comment-input');
    if (commentInput) {
      commentInput.focus();
      commentInput.placeholder = 'Trả lời bình luẫn...';
    }
    if (typeof ThongBao !== 'undefined') {
      ThongBao.thong_tin('Bạn đang trả lời bình luận');
    }
  }
  
  formatTime(timeString) {
    if (!timeString) return '';
    
    const date = new Date(timeString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Vừa xong';
    if (diffMins < 60) return `${diffMins} phút trước`;
    if (diffHours < 24) return `${diffHours} giờ trước`;
    if (diffDays < 7) return `${diffDays} ngày trước`;
    
    return date.toLocaleDateString('vi-VN');
  }
  
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  showError(message) {
    const mainContent = document.querySelector('.page-container');
    if (mainContent) {
      mainContent.innerHTML = `
        <div style="text-align: center; padding: 60px; color: #e74c3c;">
          <h2>⚠️ Lỗi</h2>
          <p style="margin-top: 20px;">${message}</p>
          <button onclick="history.back()" style="margin-top: 20px; padding: 10px 20px; cursor: pointer;">
            ← Quay lại
          </button>
        </div>
      `;
    }
  }
}

// Khởi tạo khi trang load
let baiHocManager;
document.addEventListener('DOMContentLoaded', () => {
  baiHocManager = new BaiHocManager();
});
