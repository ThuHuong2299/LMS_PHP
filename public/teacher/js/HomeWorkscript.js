// ==================== CONFIGURATION ====================
let baiTapId = null;
let sinhVienId = null;
let lopHocId = null;
let currentData = null;

// ==================== INITIALIZATION ====================

/**
 * Lấy tham số từ URL
 */
function getUrlParams() {
  const urlParams = new URLSearchParams(window.location.search);
  
  baiTapId = urlParams.get('bai_tap_id');
  sinhVienId = urlParams.get('sinh_vien_id');
  lopHocId = urlParams.get('lop_hoc_id');
  
  if (!baiTapId || !sinhVienId) {
    console.error('Thiếu params trong URL');
    showError('Thiếu thông tin. Vui lòng truy cập từ trang WorkDashBoard.');
    return false;
  }
  
  console.log('📋 Params:', { baiTapId, sinhVienId, lopHocId });
  return true;
}

/**
 * Fetch dữ liệu từ API
 */
async function fetchHomeWorkData() {
  try {
    const response = await fetch(
      `/backend/teacher/api/chi-tiet-bai-lam.php?bai_tap_id=${baiTapId}&sinh_vien_id=${sinhVienId}`,
      {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      }
    );
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      currentData = data.du_lieu;
      renderAllData(currentData);
    } else {
      showError(data.thong_bao || 'Không thể lấy dữ liệu');
    }
  } catch (error) {
    console.error('Lỗi khi fetch dữ liệu:', error);
    showError('Không thể kết nối đến server');
  }
}

// ==================== RENDER FUNCTIONS ====================

/**
 * Render tất cả dữ liệu
 */
function renderAllData(data) {
  renderStudentInfo(data.sinh_vien);
  renderQuestions(data.cau_hoi, data.bai_lam);
  renderComments(data.binh_luan);
}

/**
 * Render thông tin sinh viên (sidebar)
 */
function renderStudentInfo(sinhVien) {
  const studentName = document.querySelector('.student-name');
  const studentId = document.querySelector('.student-id');
  const studentAvatar = document.querySelector('.student-avatar');
  
  if (studentName) studentName.textContent = sinhVien.ho_ten;
  if (studentId) studentId.textContent = sinhVien.ma_sinh_vien;
  if (studentAvatar && sinhVien.anh_dai_dien) {
    studentAvatar.style.background = `url(${sinhVien.anh_dai_dien}) center/cover no-repeat`;
  }
}

/**
 * Render danh sách câu hỏi
 */
function renderQuestions(cauHoi, baiLam) {
  const mainSection = document.querySelector('.main-section');
  if (!mainSection) return;
  
  // Clear existing content
  mainSection.innerHTML = '';
  
  if (!cauHoi || cauHoi.length === 0) {
    mainSection.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #999;">
        <p>Bài tập này chưa có câu hỏi</p>
      </div>
    `;
    return;
  }
  
  // Render từng câu hỏi
  cauHoi.forEach((ch, index) => {
    const questionCard = document.createElement('div');
    questionCard.className = 'question-card';
    
    const hasAnswer = ch.tra_loi && ch.tra_loi.noi_dung;
    const scoreText = ch.tra_loi && ch.tra_loi.diem !== null 
      ? `${ch.tra_loi.diem}` 
      : '-';
    
    questionCard.innerHTML = `
      <div class="question-title">
        ${index + 1}. ${escapeHtml(ch.noi_dung)}
        ${ch.mo_ta ? `<div style="font-size: 14px; color: #666; margin-top: 8px;">${escapeHtml(ch.mo_ta)}</div>` : ''}
      </div>
      
      ${hasAnswer ? `
        <div class="submission-file">
          <div class="file-type">
            <span class="file-type-text">TXT</span>
          </div>
          <div class="file-info">
            <span class="file-name">Câu trả lời của sinh viên</span>
          </div>
        </div>
        
        <div style="padding: 16px; background: #f9fafb; border-radius: 8px; margin: 16px 0;">
          <div style="white-space: pre-wrap; color: #374151;">${escapeHtml(ch.tra_loi.noi_dung)}</div>
        </div>
      ` : `
        <div style="padding: 16px; background: #fef3c7; border-radius: 8px; margin: 16px 0; color: #92400e;">
          Sinh viên chưa trả lời câu hỏi này
        </div>
      `}
      
      <div class="score-section">
        <div class="score-label">Điểm số:</div>
        <div class="score-value">
          <span class="score-number">${scoreText}</span>
          <span class="score-total"> / ${ch.diem_toi_da}</span>
        </div>
      </div>
    `;
    
    mainSection.appendChild(questionCard);
  });
  
  // Thêm comments section
  const commentsSection = document.createElement('div');
  commentsSection.className = 'comments-section';
  commentsSection.id = 'comments-container';
  mainSection.appendChild(commentsSection);
}

/**
 * Render danh sách bình luận
 */
function renderComments(binhLuan) {
  const commentsContainer = document.getElementById('comments-container');
  if (!commentsContainer) return;
  
  commentsContainer.innerHTML = '';
  
  if (!binhLuan || binhLuan.length === 0) {
    commentsContainer.innerHTML = `
      <div style="text-align: center; padding: 20px; color: #999;">
        <p>Chưa có bình luận nào</p>
      </div>
    `;
    return;
  }
  
  binhLuan.forEach(bl => {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment';
    
    const isTeacher = bl.nguoi_gui.vai_tro === 'giang_vien';
    const avatarColor = isTeacher 
      ? 'linear-gradient(135deg, #fbbf24, #f59e0b)' 
      : 'linear-gradient(135deg, #34d399, #10b981)';
    
    const time = formatDateTime(bl.thoi_gian_gui);
    
    commentDiv.innerHTML = `
      <div class="comment-avatar" style="background: ${avatarColor};"></div>
      <div class="comment-content">
        <div class="comment-header">
          <div class="comment-author">${escapeHtml(bl.nguoi_gui.ho_ten)}</div>
          <div class="comment-time">
            <span>${time.time}</span>
            <span>${time.date}</span>
          </div>
        </div>
        <div class="comment-text">
          ${escapeHtml(bl.noi_dung)}
        </div>
      </div>
    `;
    
    commentsContainer.appendChild(commentDiv);
  });
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Format DateTime
 */
function formatDateTime(dateString) {
  if (!dateString) return { time: '-', date: '-' };
  
  const date = new Date(dateString);
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  
  return {
    time: `${hours}:${minutes}`,
    date: `${day}/${month}/${year}`
  };
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

/**
 * Show Error
 */
function showError(message) {
  console.error(message);
  alert(message);
}

// ==================== INITIALIZATION ====================

document.addEventListener('DOMContentLoaded', function() {
  if (getUrlParams()) {
    fetchHomeWorkData();
  }
});
