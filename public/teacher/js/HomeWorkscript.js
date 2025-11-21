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
    
    console.log('📊 API Response:', data);
    
    if (data.thanh_cong) {
      console.log('✅ Dữ liệu:', data.du_lieu);
      console.log('📝 Câu hỏi:', data.du_lieu.cau_hoi);
      console.log('📋 Bài làm:', data.du_lieu.bai_lam);
      
      currentData = data.du_lieu;
      renderAllData(currentData);
    } else {
      console.error('❌ Lỗi API:', data.thong_bao);
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
  // Comments đã được render trong từng câu hỏi bởi shared component
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
  if (studentAvatar) {
    studentAvatar.style.background = `url(/public/student/CSS/avatar-sv.webp) center/cover no-repeat`;
  }
}

/**
 * Render danh sách câu hỏi bằng shared component
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
  
  // Render từng câu hỏi bằng shared component
  cauHoi.forEach((ch, index) => {
    const questionElement = CauHoiBaiTapRenderer.createQuestionElement(ch, index + 1, {
      showScoreInput: true,           // GV có input chấm điểm
      readOnly: true,                 // Chế độ đọc câu trả lời
      allowComment: true,             // Cho phép thảo luận
      onScoreSave: luuDiem,          // Callback khi lưu điểm
      baiTapId: baiTapId,            // ID bài tập
      sinhVienId: sinhVienId,        // ID sinh viên
      apiEndpoint: '/backend/teacher/api/binh-luan-cau-hoi.php'  // API endpoint
    });
    
    mainSection.appendChild(questionElement);
  });
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Show Error
 */
function showError(message) {
  console.error(message);
  ThongBao.loi(message);
}

/**
 * Lưu điểm cho câu hỏi
 * Callback function cho shared component
 * @param {Number} traLoiId - ID trả lời
 * @param {Number} diem - Điểm số
 * @param {HTMLElement} input - Input element
 * @param {Number} cauHoiId - ID câu hỏi
 * @param {Number} maxDiem - Điểm tối đa
 */
async function luuDiem(traLoiId, diem, input, cauHoiId, maxDiem) {
  // Validate
  if (isNaN(diem)) {
    ThongBao.canh_bao('Vui lòng nhập điểm hợp lệ');
    return;
  }
  
  if (diem < 0 || diem > maxDiem) {
    ThongBao.canh_bao(`Điểm phải từ 0 đến ${maxDiem}`);
    return;
  }
  
  if (!traLoiId) {
    ThongBao.canh_bao('Sinh viên chưa trả lời câu hỏi này');
    return;
  }
  
  try {
    // Disable input
    input.disabled = true;
    
    const response = await fetch('/backend/teacher/api/cham-diem-cau-hoi.php', {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        tra_loi_id: parseInt(traLoiId),
        diem: diem
      })
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      ThongBao.thanh_cong('Đã lưu điểm thành công');
      
      // Update score display
      const questionWrapper = input.closest('.question-wrapper');
      if (questionWrapper) {
        const scoreEarned = questionWrapper.querySelector('.score-earned');
        if (scoreEarned) {
          scoreEarned.textContent = `${diem} điểm`;
          scoreEarned.className = 'score-earned ' + (diem > 0 ? 'scored' : 'zero-score');
        }
      }
      
      // Re-enable input
      input.disabled = false;
    } else {
      ThongBao.loi(data.thong_bao || 'Không thể lưu điểm');
      input.disabled = false;
    }
  } catch (error) {
    console.error('Lỗi khi lưu điểm:', error);
    ThongBao.loi('Không thể kết nối đến server');
    input.disabled = false;
  }
}

// ==================== INITIALIZATION ====================

function initBreadcrumb() {
  const breadcrumb = new BreadcrumbManager();
  const html = breadcrumb.renderHomeWork();
  const container = document.getElementById('breadcrumb-container');
  if (container) {
    container.innerHTML = html;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  initBreadcrumb();
  if (getUrlParams()) {
    fetchHomeWorkData();
  }
});
