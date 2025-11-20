// ========== MODAL MANAGEMENT ==========
// ========== MODAL MANAGEMENT - FIXED ==========
const modals = {
  assignmentModal: document.getElementById('assignmentModal'),
  examModal: document.getElementById('examModal'),
  taothongbao: document.getElementById('taothongbao')
};

// Mở modal
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

// Đóng modal
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

// Event listeners cho các nút mở modal - FIXED
document.getElementById('openModal1')?.addEventListener('click', () => {
  loadDanhSachLopBaiTap();
  openModal('assignmentModal');
});
document.getElementById('openModal2')?.addEventListener('click', () => {
  loadDanhSachLopKiemTra();
  openModal('examModal');
});
document.getElementById('openModal3')?.addEventListener('click', () => {
  loadDanhSachLop();
  openModal('taothongbao');
});

// Đóng modal khi click bên ngoài
['assignmentModal', 'examModal', 'taothongbao'].forEach(modalId => {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeModal(modalId);
      }
    });
  }
});

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    ['assignmentModal', 'examModal', 'taothongbao'].forEach(closeModal);
  }
});

// ========== ASSIGNMENT FUNCTIONS ==========
function addAssignmentQuestion() {
  const container = document.getElementById('assignmentQuestionsContainer');
  const questionHTML = `
    <div class="question-wrapper">
      <div class="question-card-new">
        <div class="question-card-inner">
          <div class="question-header-section">
            <input type="text" placeholder="Nội dung câu hỏi" class="question-title-input">
          </div>
          <div class="question-description-section">
            <textarea placeholder="Miêu tả ngắn" rows="2" class="question-textarea"></textarea>
          </div>
        </div>
      </div>
      <div class="question-side-actions">
        <button class="btn-action-circle" onclick="addAssignmentQuestion()" title="Thêm câu hỏi">
          <img src="add.svg" alt="Thêm câu hỏi">
        </button>
        <button class="btn-action-circle" onclick="deleteAssignmentQuestion(this)" title="Xóa câu hỏi">
          <img src="delete.svg" alt="Xóa câu hỏi">
        </button>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', questionHTML);
}

function deleteAssignmentQuestion(button) {
  const container = document.getElementById('assignmentQuestionsContainer');
  const wrapper = button.closest('.question-wrapper');
  
  if (container.querySelectorAll('.question-wrapper').length > 1) {
    wrapper.remove();
  } else {
    if (window.Toast) Toast.warning('Phải có ít nhất một câu hỏi!');
  }
}

async function saveAssignment() {
  const classSelect = document.getElementById('assignmentClass');
  const chapterSelect = document.getElementById('assignmentChapter');
  const title = document.getElementById('assignmentTitle');
  const deadline = document.getElementById('assignmentDeadline');
  
  // Validation
  if (!classSelect || !chapterSelect || !title || !deadline) {
    ThongBao.loi('Không tìm thấy form!');
    return;
  }
  
  if (!classSelect.value) {
    ThongBao.canh_bao('Vui lòng chọn lớp!');
    return;
  }
  
  if (!chapterSelect.value) {
    ThongBao.canh_bao('Vui lòng chọn chương!');
    return;
  }
  
  if (!title.value.trim()) {
    ThongBao.canh_bao('Vui lòng nhập tiêu đề!');
    return;
  }
  
  if (!deadline.value) {
    ThongBao.canh_bao('Vui lòng chọn hạn nộp!');
    return;
  }
  
  // Lấy danh sách câu hỏi
  const cauHoiList = [];
  const questionWrappers = document.querySelectorAll('#assignmentQuestionsContainer .question-wrapper');
  
  if (questionWrappers.length === 0) {
    ThongBao.canh_bao('Phải có ít nhất 1 câu hỏi!');
    return;
  }
  
  let hasError = false;
  questionWrappers.forEach((wrapper, index) => {
    const noiDung = wrapper.querySelector('.question-title-input')?.value.trim();
    const moTa = wrapper.querySelector('.question-textarea')?.value.trim();
    
    if (!noiDung) {
      ThongBao.canh_bao(`Câu hỏi ${index + 1} không được để trống!`);
      hasError = true;
      return;
    }
    
    cauHoiList.push({
      noi_dung: noiDung,
      mo_ta: moTa || null,
      diem: 10 / questionWrappers.length // Chia đều điểm
    });
  });
  
  if (hasError) return;
  
  try {
    const response = await fetch('/backend/teacher/api/tao-bai-tap.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        lop_hoc_id: classSelect.value,
        chuong_id: chapterSelect.value,
        tieu_de: title.value.trim(),
        han_nop: deadline.value,
        cau_hoi: cauHoiList
      })
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      ThongBao.thanh_cong(data.thong_bao || 'Đã tạo bài tập thành công!');
      
      // Reset form
      classSelect.selectedIndex = 0;
      chapterSelect.innerHTML = '<option value="">Chọn chương</option>';
      title.value = '';
      deadline.value = '';
      
      // Xóa câu hỏi thừa, giữ lại 1
      const container = document.getElementById('assignmentQuestionsContainer');
      const firstQuestion = container.querySelector('.question-wrapper');
      if (firstQuestion) {
        container.innerHTML = firstQuestion.outerHTML;
        container.querySelector('.question-title-input').value = '';
        container.querySelector('.question-textarea').value = '';
      }
      
      closeModal('assignmentModal');
      
      // Reload danh sách bài tập nếu đang ở trang chi tiết lớp
      if (typeof loadTabBaiTap === 'function') {
        loadTabBaiTap();
      }
    } else {
      ThongBao.loi(data.thong_bao || 'Không thể tạo bài tập');
    }
  } catch (error) {
    console.error('Lỗi:', error);
    ThongBao.loi('Không thể kết nối đến server');
  }
}

/**
 * Load danh sách chương khi chọn lớp
 */
async function loadChuongTheoLop(lopHocId) {
  try {
    const response = await fetch(`/backend/teacher/api/chuong-theo-lop.php?lop_hoc_id=${lopHocId}`, {
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.thanh_cong && data.du_lieu) {
      const select = document.getElementById('assignmentChapter');
      if (!select) return;
      
      select.innerHTML = '<option value="">Chọn chương</option>';
      
      data.du_lieu.forEach(chuong => {
        const option = document.createElement('option');
        option.value = chuong.id;
        option.textContent = `Chương ${chuong.so_thu_tu} - ${chuong.ten_chuong}`;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error('Lỗi load chương:', error);
  }
}

/**
 * Load danh sách lớp học vào dropdown bài tập
 */
async function loadDanhSachLopBaiTap() {
  try {
    const response = await fetch('/backend/teacher/api/danh-sach-lop-hoc.php', {
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.thanh_cong && data.du_lieu) {
      const select = document.getElementById('assignmentClass');
      if (!select) {
        console.error('Không tìm thấy select#assignmentClass');
        return;
      }
      
      select.innerHTML = '<option value="">Tên môn - Mã lớp</option>';
      
      data.du_lieu.forEach(lop => {
        const option = document.createElement('option');
        option.value = lop.id;
        option.textContent = `${lop.ten_lop_hoc} - ${lop.ma_lop_hoc}`;
        select.appendChild(option);
      });
    } else {
      console.error('API không trả về dữ liệu:', data);
    }
  } catch (error) {
    console.error('Lỗi load danh sách lớp:', error);
  }
}

/**
 * Load danh sách lớp học vào dropdown bài kiểm tra
 */
async function loadDanhSachLopKiemTra() {
  try {
    const response = await fetch('/backend/teacher/api/danh-sach-lop-hoc.php', {
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.thanh_cong && data.du_lieu) {
      const select = document.getElementById('examClass');
      if (!select) {
        console.error('Không tìm thấy select#examClass');
        return;
      }
      
      select.innerHTML = '<option value="">Tên môn - Mã lớp</option>';
      
      data.du_lieu.forEach(lop => {
        const option = document.createElement('option');
        option.value = lop.id;
        option.textContent = `${lop.ten_lop_hoc} - ${lop.ma_lop_hoc}`;
        select.appendChild(option);
      });
    } else {
      console.error('API không trả về dữ liệu:', data);
    }
  } catch (error) {
    console.error('Lỗi load danh sách lớp:', error);
  }
}

/**
 * Load chương theo lớp vào dropdown bài kiểm tra
 */
async function loadChuongTheoLopKiemTra(lopHocId) {
  try {
    const response = await fetch(`/backend/teacher/api/chuong-theo-lop.php?lop_hoc_id=${lopHocId}`, {
      credentials: 'include'
    });
    const data = await response.json();
    
    const select = document.getElementById('examChapter');
    if (!select) {
      console.error('Không tìm thấy select#examChapter');
      return;
    }
    
    select.innerHTML = '<option value="">Chọn chương (không bắt buộc)</option>';
    
    if (data.thanh_cong && data.du_lieu) {
      data.du_lieu.forEach(chuong => {
        const option = document.createElement('option');
        option.value = chuong.id;
        option.textContent = `Chương ${chuong.so_thu_tu}: ${chuong.ten_chuong}`;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error('Lỗi load chương:', error);
  }
}

// Event listener cho select lớp trong modal bài tập
document.addEventListener('DOMContentLoaded', () => {
  // Modal bài tập
  const assignmentClassSelect = document.getElementById('assignmentClass');
  if (assignmentClassSelect) {
    assignmentClassSelect.addEventListener('change', function() {
      if (this.value) {
        loadChuongTheoLop(this.value);
      } else {
        const chapterSelect = document.getElementById('assignmentChapter');
        if (chapterSelect) {
          chapterSelect.innerHTML = '<option value="">Chọn chương</option>';
        }
      }
    });
  }
  
  // Modal bài kiểm tra
  const examClassSelect = document.getElementById('examClass');
  if (examClassSelect) {
    examClassSelect.addEventListener('change', function() {
      if (this.value) {
        loadChuongTheoLopKiemTra(this.value);
      } else {
        const chapterSelect = document.getElementById('examChapter');
        if (chapterSelect) {
          chapterSelect.innerHTML = '<option value="">Chọn chương (không bắt buộc)</option>';
        }
      }
    });
  }
});

// ========== EXAM FUNCTIONS ==========
function toggleExamRadio(radioBtn) {
  const wrapper = radioBtn.closest('.exam-question-card');
  wrapper.querySelectorAll('.exam-radio-btn').forEach(btn => {
    btn.classList.remove('checked');
  });
  radioBtn.classList.add('checked');
}

function addExamOption(button) {
  const content = button.closest('.exam-question-content');
  const newOption = `
    <div class="exam-option-wrapper">
      <div class="exam-radio-btn" onclick="toggleExamRadio(this)"></div>
      <input type="text" class="exam-option-input normal" placeholder="Lựa chọn mới">
    </div>
  `;
  button.insertAdjacentHTML('beforebegin', newOption);
}

function addExamQuestion() {
  const container = document.getElementById('questionsContainer');
  const questionHTML = `
    <div class="exam-question-wrapper">
      <div class="exam-question-card">
        <div class="exam-question-border"></div>
        <div class="exam-question-content">
          <div class="exam-question-input-wrapper">
            <input type="text" class="exam-question-input" placeholder="Nội dung câu hỏi">
          </div>

          <div class="exam-option-wrapper">
            <div class="exam-radio-btn checked" onclick="toggleExamRadio(this)"></div>
            <input type="text" class="exam-option-input correct" placeholder="Lựa chọn đúng">
          </div>

          <div class="exam-option-wrapper">
            <div class="exam-radio-btn" onclick="toggleExamRadio(this)"></div>
            <input type="text" class="exam-option-input normal" placeholder="Lựa chọn 1">
          </div>

          <button class="exam-add-option-btn" onclick="addExamOption(this)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M12 5V19M5 12H19" stroke="#3293F9" stroke-width="2" stroke-linecap="round" />
            </svg>
            Thêm lựa chọn
          </button>
        </div>
      </div>

      <div class="exam-action-buttons">
        <button class="exam-icon-btn" onclick="addExamQuestion()" title="Thêm câu hỏi">
          <img src="add.svg" alt="Thêm" />
        </button>
        <button class="exam-icon-btn" onclick="deleteExamQuestion(this)" title="Xóa câu hỏi">
          <img src="delete.svg" alt="Xóa" />
        </button>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', questionHTML);
}

function deleteExamQuestion(button) {
  const container = document.getElementById('questionsContainer');
  const wrapper = button.closest('.exam-question-wrapper');
  
  if (container.querySelectorAll('.exam-question-wrapper').length > 1) {
    wrapper.remove();
  } else {
    if (window.Toast) Toast.warning('Phải có ít nhất một câu hỏi!');
  }
}

async function submitExam() {
  const classSelect = document.getElementById('examClass');
  const chapterSelect = document.getElementById('examChapter');
  const title = document.getElementById('examTitle');
  const duration = document.getElementById('examDuration');
  const datetime = document.getElementById('examDatetime');
  
  // Validation
  if (!classSelect || !title || !duration || !datetime) {
    ThongBao.loi('Không tìm thấy form!');
    return;
  }
  
  if (!classSelect.value) {
    ThongBao.canh_bao('Vui lòng chọn lớp!');
    return;
  }
  
  if (!title.value.trim()) {
    ThongBao.canh_bao('Vui lòng nhập tiêu đề!');
    return;
  }
  
  const thoiLuong = parseInt(duration.value);
  if (!thoiLuong || thoiLuong <= 0) {
    ThongBao.canh_bao('Thời lượng phải lớn hơn 0!');
    return;
  }
  
  if (!datetime.value) {
    ThongBao.canh_bao('Vui lòng chọn thời gian bắt đầu!');
    return;
  }
  
  // Thu thập câu hỏi
  const cauHoi = [];
  const questionWrappers = document.querySelectorAll('.exam-question-wrapper');
  
  if (questionWrappers.length === 0) {
    ThongBao.canh_bao('Phải có ít nhất 1 câu hỏi!');
    return;
  }
  
  let hasError = false;
  questionWrappers.forEach((wrapper, index) => {
    const noiDungCauHoi = wrapper.querySelector('.exam-question-input')?.value.trim();
    
    if (!noiDungCauHoi) {
      ThongBao.canh_bao(`Câu hỏi ${index + 1} không được để trống!`);
      hasError = true;
      return;
    }
    
    // Thu thập lựa chọn
    const cacLuaChon = [];
    const optionWrappers = wrapper.querySelectorAll('.exam-option-wrapper');
    
    if (optionWrappers.length < 2) {
      ThongBao.canh_bao(`Câu hỏi ${index + 1} phải có ít nhất 2 lựa chọn!`);
      hasError = true;
      return;
    }
    
    let hasCorrectAnswer = false;
    optionWrappers.forEach((optWrapper, optIndex) => {
      const noiDung = optWrapper.querySelector('.exam-option-input')?.value.trim();
      const dung = optWrapper.querySelector('.exam-radio-btn')?.classList.contains('checked');
      
      if (!noiDung) {
        ThongBao.canh_bao(`Câu hỏi ${index + 1}, lựa chọn ${optIndex + 1} không được để trống!`);
        hasError = true;
        return;
      }
      
      if (dung) hasCorrectAnswer = true;
      
      cacLuaChon.push({
        thu_tu: optIndex + 1,
        noi_dung: noiDung,
        dung: dung
      });
    });
    
    if (!hasCorrectAnswer) {
      ThongBao.canh_bao(`Câu hỏi ${index + 1} phải có ít nhất 1 đáp án đúng!`);
      hasError = true;
      return;
    }
    
    cauHoi.push({
      thu_tu: index + 1,
      noi_dung_cau_hoi: noiDungCauHoi,
      diem: 1.0, // Có thể customize sau
      cac_lua_chon: cacLuaChon
    });
  });
  
  if (hasError) return;
  
  try {
    const response = await fetch('/backend/teacher/api/tao-bai-kiem-tra.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        lop_hoc_id: classSelect.value,
        chuong_id: chapterSelect?.value || null,
        tieu_de: title.value.trim(),
        thoi_luong: thoiLuong,
        thoi_gian_bat_dau: datetime.value,
        cho_phep_lam_lai: false,
        cau_hoi: cauHoi
      })
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      ThongBao.thanh_cong(data.thong_bao || 'Đã tạo bài kiểm tra thành công!');
      
      // Reset form
      classSelect.selectedIndex = 0;
      if (chapterSelect) chapterSelect.innerHTML = '<option value="">Chọn chương (không bắt buộc)</option>';
      title.value = '';
      duration.value = '';
      datetime.value = '';
      
      // Reset câu hỏi về mặc định
      const container = document.getElementById('questionsContainer');
      if (container) {
        const firstQuestion = container.querySelector('.exam-question-wrapper');
        if (firstQuestion) {
          container.innerHTML = firstQuestion.outerHTML;
          container.querySelector('.exam-question-input').value = '';
          const optInputs = container.querySelectorAll('.exam-option-input');
          optInputs.forEach(input => input.value = '');
        }
      }
      
      closeModal('examModal');
      
      // Reload danh sách bài kiểm tra nếu đang ở trang chi tiết lớp
      if (typeof loadTabBaiKiemTra === 'function') {
        loadTabBaiKiemTra();
      }
    } else {
      ThongBao.loi(data.thong_bao || 'Không thể tạo bài kiểm tra');
    }
  } catch (error) {
    console.error('Lỗi khi tạo bài kiểm tra:', error);
    ThongBao.loi('Đã xảy ra lỗi khi tạo bài kiểm tra');
  }
}

// ========== NOTIFICATION FUNCTIONS ==========
async function saveThongBao() {
  const classSelect = document.getElementById('classcode');
  const title = document.getElementById('titleInputtb');
  const content = document.getElementById('notiInput');
  
  if (!classSelect || !title || !content) {
    ThongBao.loi('Không tìm thấy form!');
    return;
  }
  
  if (!classSelect.value) {
    ThongBao.canh_bao('Vui lòng chọn lớp!');
    return;
  }
  if (!title.value.trim()) {
    ThongBao.canh_bao('Vui lòng nhập tiêu đề!');
    return;
  }
  if (!content.value.trim()) {
    ThongBao.canh_bao('Vui lòng nhập nội dung!');
    return;
  }
  
  try {
    const response = await fetch('/backend/teacher/api/tao-thong-bao.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include',
      body: JSON.stringify({
        lop_hoc_id: classSelect.value,
        tieu_de: title.value.trim(),
        noi_dung: content.value.trim()
      })
    });
    
    const data = await response.json();
    
    if (data.thanh_cong) {
      ThongBao.thanh_cong(data.thong_bao || 'Đã gửi thông báo thành công!');
      
      // Reset form
      classSelect.selectedIndex = 0;
      title.value = '';
      content.value = '';
      
      closeModal('taothongbao');
    } else {
      ThongBao.loi(data.thong_bao || 'Không thể gửi thông báo');
    }
  } catch (error) {
    console.error('Lỗi:', error);
    ThongBao.loi('Không thể kết nối đến server');
  }
}

/**
 * Load danh sách lớp học vào dropdown
 */
async function loadDanhSachLop() {
  try {
    const response = await fetch('/backend/teacher/api/danh-sach-lop-hoc.php', {
      credentials: 'include'
    });
    const data = await response.json();
    
    if (data.thanh_cong && data.du_lieu) {
      const select = document.getElementById('classcode');
      if (!select) {
        console.error('Không tìm thấy select#classcode');
        return;
      }
      
      select.innerHTML = '<option value="">Chọn lớp</option>';
      
      data.du_lieu.forEach(lop => {
        const option = document.createElement('option');
        option.value = lop.id;
        option.textContent = `${lop.ten_lop_hoc} - ${lop.ma_lop_hoc}`;
        select.appendChild(option);
      });
    } else {
      console.error('API không trả về dữ liệu:', data);
    }
  } catch (error) {
    console.error('Lỗi load danh sách lớp:', error);
    ThongBao.loi('Không thể tải danh sách lớp học');
  }
}

// ========== SEARCH FUNCTIONALITY ==========
document.getElementById('searchh')?.addEventListener('input', function(e) {
  const searchTerm = e.target.value.toLowerCase();
  console.log('Searching for:', searchTerm);
});

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
  console.log('Learn Lab Dashboard initialized');
  
  // Ẩn tất cả modal khi load trang
  ['assignmentModal', 'examModal', 'taothongbao'].forEach(modalId => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
  });
});