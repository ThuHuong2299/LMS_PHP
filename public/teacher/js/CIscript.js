const STUDENTS_PER_PAGE = 5;
let currentPage = 1;
let allStudents = [];

// TODO: Fetch students from database
const AVATAR_COLORS = [
  '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
  '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#ABEBC6'
];

// Initialize
document.addEventListener('DOMContentLoaded', function () {
  renderBreadcrumb();
  initializeTabs();
  initializeModals();
  generateStudents();
  renderStudents();
});

// Render Breadcrumb
function renderBreadcrumb() {
  const breadcrumb = document.getElementById('contentBreadcrumb');
  if (!breadcrumb) return;
  
  breadcrumb.innerHTML = `
    <a href="/public/teacher/Classroom.html" style="color: inherit; text-decoration: none;">Lớp học</a>
    <span class="separator">›</span>
    <span>Chi tiết lớp</span>
  `;
}

// Tab switching
function initializeTabs() {
  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      
      // Remove active from all tabs and sections
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));

      // Add active to clicked tab and corresponding section
      this.classList.add('active');
      const tabName = this.dataset.tab;
      const target = document.getElementById(tabName + '-section');
      if (target) target.classList.add('active');
    });
  });
}

// Modal initialization
function initializeModals() {
  const modalIds = ['assignmentModal', 'examModal', 'notificationModal', 'documentModal'];
  
  modalIds.forEach(id => {
    const modal = document.getElementById(id);
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) {
          if (id === 'notificationModal') closeNotificationModal();
          else if (id === 'documentModal') closeDocumentModal();
          else closeModal(id);
        }
      });
    }
  });
}

// Chapter toggle
function toggleChapter(header) {
  const arrow = header.querySelector('.chapter-arrow');
  const content = header.nextElementSibling;
  if (arrow) arrow.classList.toggle('open');
  if (content) content.classList.toggle('open');
}

// Generic modal functions
function openModal(type) {
  const id = type === 'exam' ? 'examModal' : 'assignmentModal';
  const modal = document.getElementById(id);
  if (modal) modal.style.display = 'flex';
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.style.display = 'none';
}

// Assignment functions
function saveAssignment() {
  const title = document.getElementById('assignmentTitle');
  const deadline = document.getElementById('assignmentDeadline');
  
  if (!title || !deadline) {
    ThongBao.loi('Không tìm thấy form!');
    return;
  }
  
  if (!title.value || !deadline.value) {
    ThongBao.canh_bao('Vui lòng điền đầy đủ thông tin!');
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
    title: title.value, 
    deadline: deadline.value, 
    questions 
  });
  
  ThongBao.thanh_cong('Đã lưu bài tập!');
  closeModal('assignmentModal');
}

function addAssignmentQuestion() {
  const container = document.getElementById('assignmentQuestionsContainer');
  
  const newQuestion = document.createElement('div');
  newQuestion.className = 'question-wrapper';
  newQuestion.innerHTML = `
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
  `;
  
  container.appendChild(newQuestion);
}

function deleteAssignmentQuestion(button) {
  const questionWrapper = button.closest('.question-wrapper');
  const container = document.getElementById('assignmentQuestionsContainer');
  
  if (container.querySelectorAll('.question-wrapper').length === 1) {
    ThongBao.canh_bao('Phải có ít nhất 1 câu hỏi!');
    return;
  }
  
  if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
    questionWrapper.remove();
  }
}

// Exam functions
function toggleExamRadio(element) {
  const questionCard = element.closest('.exam-question-card');
  const allRadios = questionCard.querySelectorAll('.exam-radio-btn');
  allRadios.forEach(radio => radio.classList.remove('checked'));
  element.classList.add('checked');

  const allOptions = questionCard.querySelectorAll('.exam-option-input');
  allOptions.forEach(opt => {
    opt.classList.remove('correct');
    opt.classList.add('normal');
  });

  const parentOption = element.closest('.exam-option-wrapper');
  const input = parentOption.querySelector('.exam-option-input');
  input.classList.remove('normal');
  input.classList.add('correct');
}

function addExamOption(button) {
  const questionContent = button.closest('.exam-question-content');
  
  const newOption = document.createElement('div');
  newOption.className = 'exam-option-wrapper';
  newOption.innerHTML = `
    <div class="exam-radio-btn" onclick="toggleExamRadio(this)"></div>
    <input type="text" class="exam-option-input normal" placeholder="Lựa chọn mới">
  `;
  
  questionContent.insertBefore(newOption, button);
}

function addExamQuestion() {
  const container = document.getElementById('questionsContainer');
  
  const newQuestion = document.createElement('div');
  newQuestion.className = 'exam-question-wrapper';
  newQuestion.innerHTML = `
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
            <path d="M12 5V19M5 12H19" stroke="#3293F9" stroke-width="2" stroke-linecap="round"/>
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
  `;
  
  container.appendChild(newQuestion);
}

function deleteExamQuestion(button) {
  const questionWrapper = button.closest('.exam-question-wrapper');
  const container = document.getElementById('questionsContainer');
  
  if (container.querySelectorAll('.exam-question-wrapper').length === 1) {
    ThongBao.canh_bao('Phải có ít nhất 1 câu hỏi!');
    return;
  }
  
  if (confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
    questionWrapper.remove();
  }
}

function submitExam() {
  const title = document.querySelector('.exam-popup-body .exam-form-input[placeholder="Thông báo nghỉ học"]');
  const duration = document.querySelector('.exam-popup-body .exam-form-input[placeholder="VD: 60 phút"]');
  const datetime = document.querySelector('.exam-popup-body input[type="datetime-local"]');
  
  if (!title || !duration || !datetime) {
    ThongBao.loi('Không tìm thấy form!');
    return;
  }
  
  if (!title.value || !duration.value || !datetime.value) {
    ThongBao.canh_bao('Vui lòng điền đầy đủ thông tin!');
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
    title: title.value, 
    duration: duration.value, 
    datetime: datetime.value, 
    questions 
  });
  
  ThongBao.thanh_cong('Đã lưu bài kiểm tra!');
  closeModal('examModal');
}

// Notification functions
function openNotificationModal() {
  const modal = document.getElementById('notificationModal');
  if (modal) modal.classList.add('active');
}

function closeNotificationModal() {
  const modal = document.getElementById('notificationModal');
  if (modal) modal.classList.remove('active');
  
  const title = document.getElementById('notificationTitle');
  const content = document.getElementById('notificationContent');
  if (title) title.value = '';
  if (content) content.value = '';
}

function saveNotification() {
  const title = document.getElementById('notificationTitle').value.trim();
  const content = document.getElementById('notificationContent').value.trim();
  
    if (!title || !content) {
      if (window.Toast) Toast.warning('Vui lòng nhập đầy đủ tiêu đề và nội dung!');
      return;
    }  const card = document.createElement('div');
  card.className = 'notification-card';
  card.innerHTML = `
    <div class="notification-left">
      <div class="notification-icon">
        <img src="noti.svg" alt="icon" /> 
      </div>
      <div class="notification-content">
        <div class="notification-text"><strong>${escapeHtml(title)}</strong><br>${escapeHtml(content)}</div>
        <div class="notification-views">0 đã xem</div>
      </div>
    </div>
    <div class="notification-right">
      <div class="notification-menu" onclick="deleteNotification(this)">⋮</div>
      <div class="notification-time">Vừa xong</div>
    </div>
  `;
  
  const list = document.getElementById('notificationsList');
  if (list) list.prepend(card);
  closeNotificationModal();
}

function deleteNotification(btn) {
  if (!btn) return;
  if (confirm('Bạn có chắc muốn xóa thông báo này?')) {
    const card = btn.closest('.notification-card');
    if (card) card.remove();
  }
}

// Document functions
function openDocumentModal() {
  const modal = document.getElementById('documentModal');
  if (modal) modal.classList.add('active');
}

function closeDocumentModal() {
  const modal = document.getElementById('documentModal');
  if (modal) modal.classList.remove('active');
  
  const title = document.getElementById('documentTitle');
  const file = document.getElementById('documentFile');
  if (title) title.value = '';
  if (file) file.value = '';
}

function saveDocument() {
  const title = document.getElementById('documentTitle').value;
  const file = document.getElementById('documentFile').files[0];

  if (!title) {
    ThongBao.canh_bao('Vui lòng nhập tên tài liệu');
    return;
  }

  if (!file) {
    ThongBao.canh_bao('Vui lòng chọn file tài liệu');
    return;
  }

  ThongBao.thanh_cong(`Đã lưu tài liệu: ${title}`);
  closeDocumentModal();
}

// Student management functions (DEPRECATED - sử dụng classroomInfo.js thay thế)
function generateStudents() {
  // Logic cũ - không còn sử dụng
  // Data sẽ được load từ API trong classroomInfo.js
  console.log('generateStudents() deprecated - use classroomInfo.js');
}

function renderStudents() {
  const tbody = document.getElementById('studentList');
  if (!tbody) return;
  
  const startIndex = (currentPage - 1) * STUDENTS_PER_PAGE;
  const endIndex = startIndex + STUDENTS_PER_PAGE;
  const currentStudents = allStudents.slice(startIndex, endIndex);

  tbody.innerHTML = currentStudents.map((student, index) => {
    const actualIndex = startIndex + index;
    return `
      <tr class="student-row">
        <td>
          <div class="student-info">
            <div class="student-avatar" style="background: ${AVATAR_COLORS[actualIndex % AVATAR_COLORS.length]}">
              ${student.name.charAt(0)}
            </div>
            <div class="student-details">
              <div class="student-name">${student.name}</div>
              <div class="student-id">${student.id}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="progress-container">
            <div class="progress-bar">
              <div class="progress-fill" style="width: ${student.progress}%"></div>
            </div>
            <div class="progress-text">${student.progress}%</div>
          </div>
        </td>
        <td>
          <div class="last-updated">${student.updated}</div>
        </td>
      </tr>
    `}).join('');

  updatePagination();
}

function updatePagination() {
  const totalStudents = allStudents.length;
  const totalPages = Math.ceil(totalStudents / STUDENTS_PER_PAGE);
  const start = (currentPage - 1) * STUDENTS_PER_PAGE + 1;
  const end = Math.min(currentPage * STUDENTS_PER_PAGE, totalStudents);
  
  const paginationInfo = document.querySelector('.pagination-info');
  if (paginationInfo) {
    paginationInfo.textContent = `${start}-${end} của ${totalStudents}`;
  }

  const prevBtn = document.querySelector('.page-btn:first-child');
  const nextBtn = document.querySelector('.page-btn:last-child');

  if (prevBtn) prevBtn.disabled = currentPage === 1;
  if (nextBtn) nextBtn.disabled = currentPage === totalPages;
}

function addStudent() {
  ThongBao.thong_tin('Chức năng thêm sinh viên đang được phát triển');
}

function previousPage() {
  if (currentPage > 1) {
    currentPage--;
    renderStudents();
  }
}

function nextPage() {
  const totalPages = Math.ceil(allStudents.length / STUDENTS_PER_PAGE);
  if (currentPage < totalPages) {
    currentPage++;
    renderStudents();
  }
}

// Utility functions
function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}