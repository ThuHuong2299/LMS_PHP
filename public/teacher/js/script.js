// ========== SIDEBAR TOGGLE ==========
function toggleSidebar(event) {
  // Ngăn sự kiện lan sang logo
  if (event) event.stopPropagation();

  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("collapsed");
}

document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const logoImg = document.querySelector(".logo img.objects");

  logoImg.addEventListener("click", (event) => {
    // Nếu sidebar đang thu gọn → mở rộng lại
    if (sidebar.classList.contains("collapsed")) {
      sidebar.classList.remove("collapsed");
    } else {
      // Nếu đang mở → về trang chủ
      window.location.href = "../TrangChu.html";
    }
  });

  // Gắn lại listener cho nút thu gọn
  const toggleBtn = document.querySelector(".open-close");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", toggleSidebar);
  }
});

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
document.getElementById('openModal1')?.addEventListener('click', () => openModal('assignmentModal'));
document.getElementById('openModal2')?.addEventListener('click', () => openModal('examModal'));
document.getElementById('openModal3')?.addEventListener('click', () => openModal('taothongbao'));

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
    alert('Phải có ít nhất một câu hỏi!');
  }
}

function saveAssignment() {
  const classSelect = document.getElementById('assignmentClass');
  const chapter = document.getElementById('assignmentChapter');
  const title = document.getElementById('assignmentTitle');
  const deadline = document.getElementById('assignmentDeadline');
  
  if (!classSelect.value) {
    alert('Vui lòng chọn lớp giao!');
    return;
  }
  
  if (!title.value || !deadline.value) {
    alert('Vui lòng điền đầy đủ thông tin!');
    return;
  }
  
  const questions = [];
  document.querySelectorAll('#assignmentQuestionsContainer .question-wrapper').forEach((wrapper, index) => {
    const questionText = wrapper.querySelector('.question-title-input').value;
    const description = wrapper.querySelector('.question-textarea').value;
    
    questions.push({
      id: index + 1,
      question: questionText,
      description: description
    });
  });
  
  console.log('Assignment Data:', { 
    class: classSelect.options[classSelect.selectedIndex].text,
    chapter: chapter.value,
    title: title.value, 
    deadline: deadline.value, 
    questions 
  });
  
  alert('✅ Đã lưu bài tập!');
  closeModal('assignmentModal');
}

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
    alert('Phải có ít nhất một câu hỏi!');
  }
}

function submitExam() {
  const classSelect = document.getElementById('examClass');
  const title = document.getElementById('examTitle');
  const duration = document.getElementById('examDuration');
  const datetime = document.getElementById('examDatetime');
  
  if (!classSelect.value) {
    alert('Vui lòng chọn lớp giao!');
    return;
  }
  
  if (!title.value || !duration.value || !datetime.value) {
    alert('Vui lòng điền đầy đủ thông tin!');
    return;
  }
  
  const questions = [];
  document.querySelectorAll('.exam-question-wrapper').forEach((wrapper, index) => {
    const questionText = wrapper.querySelector('.exam-question-input').value;
    const options = [];
    
    wrapper.querySelectorAll('.exam-option-wrapper').forEach(opt => {
      const optionText = opt.querySelector('.exam-option-input').value;
      const isCorrect = opt.querySelector('.exam-radio-btn').classList.contains('checked');
      options.push({ text: optionText, correct: isCorrect });
    });
    
    questions.push({
      id: index + 1,
      question: questionText,
      options: options
    });
  });
  
  console.log('Exam Data:', { 
    class: classSelect.options[classSelect.selectedIndex].text,
    title: title.value, 
    duration: duration.value, 
    datetime: datetime.value, 
    questions 
  });
  
  alert('✅ Đã lưu bài kiểm tra!');
  closeModal('examModal');
}

// ========== NOTIFICATION FUNCTIONS ==========
function saveThongBao() {
  const classSelect = document.getElementById('classSelecttb');
  const title = document.getElementById('titleInputtb');
  const content = document.getElementById('notiInput');
  
  if (!classSelect.value) {
    alert('Vui lòng chọn lớp!');
    return;
  }
  if (!title.value.trim()) {
    alert('Vui lòng nhập tiêu đề!');
    return;
  }
  if (!content.value.trim()) {
    alert('Vui lòng nhập nội dung!');
    return;
  }
  
  const thongBao = {
    type: 'thongbao',
    class: classSelect.value,
    title: title.value,
    content: content.value,
    createdAt: new Date().toISOString()
  };
  
  console.log('Thông báo đã tạo:', thongBao);
  alert('✅ Đã gửi thông báo thành công!');
  
  closeModal('taothongbao');
}

// ========== SIDEBAR TOGGLE ==========
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('collapsed');
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