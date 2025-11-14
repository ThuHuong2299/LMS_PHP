/**
 * CHAM BAI - GRADING MODULE
 * Backend Integration Ready
 * 
 * This module handles:
 * - Fetching student answers from API
 * - Rendering questions dynamically
 * - Managing grading interactions
 * - Updating scores to backend
 * 
 * BACKEND API ENDPOINTS NEEDED:
 * - GET  /api/exams/{examId}/students/{studentId}/answers
 * - PUT  /api/answers/{questionId}/score
 * - GET  /api/exams/{examId}/students/{studentId}/stats
 */

// ==================== CONFIGURATION ====================
const API_BASE_URL = '/api'; // Change this to your backend API URL
const USE_MOCK_DATA = true;   // Set to false when backend is ready

// Get metadata from HTML
const pageContainer = document.querySelector('.page-container');
const EXAM_ID = pageContainer?.dataset.examId || '123';
const STUDENT_ID = pageContainer?.dataset.studentId || '456';

// ==================== MOCK DATA (Remove when backend is ready) ====================
const MOCK_DATA = {
  examId: 123,
  studentId: 456,
  studentName: "Nguyễn Văn A",
  totalQuestions: 25,
  questions: [
    {
      id: 1,
      number: 1,
      type: "multiple-choice",
      title: "Nội dung câu hỏi trắc nghiệm số 1",
      maxScore: 0.67,
      currentScore: 0.67,
      status: "checked",
      options: [
        { id: "a", text: "Lựa chọn 1", isCorrect: true },
        { id: "b", text: "Lựa chọn 2", isCorrect: false },
        { id: "c", text: "Lựa chọn 3", isCorrect: false },
        { id: "d", text: "Lựa chọn 4", isCorrect: false }
      ],
      studentAnswer: "a"
    },
    {
      id: 2,
      number: 2,
      type: "multiple-choice",
      title: "Nội dung câu hỏi trắc nghiệm số 2",
      maxScore: 0.67,
      currentScore: 0.67,
      status: "checked",
      options: [
        { id: "a", text: "Lựa chọn 1", isCorrect: false },
        { id: "b", text: "Lựa chọn 2", isCorrect: true },
        { id: "c", text: "Lựa chọn 3", isCorrect: false },
        { id: "d", text: "Lựa chọn 4", isCorrect: false }
      ],
      studentAnswer: "b"
    },
    {
      id: 3,
      number: 3,
      type: "multiple-choice",
      title: "Nội dung câu hỏi trắc nghiệm số 3",
      maxScore: 0.67,
      currentScore: 0.67,
      status: "checked",
      options: [
        { id: "a", text: "Lựa chọn 1", isCorrect: false },
        { id: "b", text: "Lựa chọn 2", isCorrect: false },
        { id: "c", text: "Lựa chọn 3", isCorrect: true },
        { id: "d", text: "Lựa chọn 4", isCorrect: false }
      ],
      studentAnswer: "c"
    },
    {
      id: 4,
      number: 4,
      type: "multiple-choice",
      title: "Nội dung câu hỏi trắc nghiệm số 4",
      maxScore: 0.67,
      currentScore: 0,
      status: "unchecked",
      options: [
        { id: "a", text: "Lựa chọn 1", isCorrect: true },
        { id: "b", text: "Lựa chọn 2", isCorrect: false },
        { id: "c", text: "Lựa chọn 3", isCorrect: false },
        { id: "d", text: "Lựa chọn 4", isCorrect: false }
      ],
      studentAnswer: "d"
    },
    // Add more mock questions as needed
    ...Array.from({ length: 21 }, (_, i) => ({
      id: i + 5,
      number: i + 5,
      type: "multiple-choice",
      title: `Câu hỏi trắc nghiệm số ${i + 5}`,
      maxScore: 0.67,
      currentScore: 0,
      status: "unchecked",
      options: [
        { id: "a", text: "Lựa chọn 1", isCorrect: i % 4 === 0 },
        { id: "b", text: "Lựa chọn 2", isCorrect: i % 4 === 1 },
        { id: "c", text: "Lựa chọn 3", isCorrect: i % 4 === 2 },
        { id: "d", text: "Lựa chọn 4", isCorrect: i % 4 === 3 }
      ],
      studentAnswer: null
    }))
  ],
  stats: {
    totalChecked: 3,
    totalUnchecked: 22,
    totalScore: 2.01,
    maxTotalScore: 16.75
  }
};

// ==================== API LAYER ====================
const API = {
  /**
   * Fetch student answers for a specific exam
   * @param {string} examId 
   * @param {string} studentId 
   * @returns {Promise<Object>}
   */
  async getStudentAnswers(examId, studentId) {
    if (USE_MOCK_DATA) {
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 500));
      return MOCK_DATA;
    }
    
    try {
      const response = await fetch(`${API_BASE_URL}/exams/${examId}/students/${studentId}/answers`);
      if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      return await response.json();
    } catch (error) {
      console.error('Failed to fetch student answers:', error);
      throw error;
    }
  },

  /**
   * Update score for a specific question
   * @param {string} questionId 
   * @param {number} score 
   * @returns {Promise<Object>}
   */
  async updateScore(questionId, score) {
    if (USE_MOCK_DATA) {
      // Simulate API delay
      await new Promise(resolve => setTimeout(resolve, 300));
      return { success: true, questionId, score };
    }
    
    try {
      const response = await fetch(`${API_BASE_URL}/answers/${questionId}/score`, {
        method: 'PUT',
        headers: { 
          'Content-Type': 'application/json',
          // Add authentication headers if needed
          // 'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ score })
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      return await response.json();
    } catch (error) {
      console.error('Failed to update score:', error);
      throw error;
    }
  },

  /**
   * Get grading statistics
   * @param {string} examId 
   * @param {string} studentId 
   * @returns {Promise<Object>}
   */
  async getGradingStats(examId, studentId) {
    if (USE_MOCK_DATA) {
      await new Promise(resolve => setTimeout(resolve, 200));
      return MOCK_DATA.stats;
    }
    
    try {
      const response = await fetch(`${API_BASE_URL}/exams/${examId}/students/${studentId}/stats`);
      if (!response.ok) throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      return await response.json();
    } catch (error) {
      console.error('Failed to fetch stats:', error);
      throw error;
    }
  }
};

// ==================== RENDER FUNCTIONS ====================

/**
 * Render question navigation grid
 * @param {Array} questions 
 */
function renderNavigation(questions) {
  const grid = document.getElementById('questionsNav');
  if (!grid) return;
  
  grid.innerHTML = '';
  
  questions.forEach(q => {
    const btn = document.createElement('button');
    btn.className = `question-btn status-${q.status}`;
    btn.dataset.questionId = q.id;
    btn.dataset.status = q.status;
    btn.textContent = q.number;
    btn.setAttribute('aria-label', `Câu hỏi ${q.number} - ${q.status === 'checked' ? 'Đã chấm' : 'Chưa chấm'}`);
    btn.onclick = () => scrollToQuestion(q.id);
    
    grid.appendChild(btn);
  });
  
  updateStats(questions);
}

/**
 * Render all questions in detail panel
 * @param {Array} questions 
 */
function renderQuestions(questions) {
  const container = document.getElementById('questionsList');
  if (!container) return;
  
  container.innerHTML = '';
  
  questions.forEach(question => {
    const card = createQuestionCard(question);
    container.appendChild(card);
  });
}

/**
 * Create a question card based on type
 * Only supports multiple-choice questions
 * @param {Object} question 
 * @returns {HTMLElement}
 */
function createQuestionCard(question) {
  // Only handle multiple-choice questions
  if (question.type === 'multiple-choice') {
    return createMultipleChoiceCard(question);
  } else {
    console.warn(`Unsupported question type: ${question.type}. Only multiple-choice is supported.`);
    return document.createElement('div');
  }
}

/**
 * Create multiple choice question card
 */
function createMultipleChoiceCard(question) {
  const card = document.createElement('div');
  card.className = 'question-card';
  card.id = `question-${question.id}`;
  card.dataset.questionId = question.id;
  card.dataset.type = 'multiple-choice';
  card.dataset.status = question.status;
  
  const header = document.createElement('div');
  header.className = 'question-header';
  header.innerHTML = `
    <h3 class="question-title">Câu hỏi ${question.number}: ${question.title}</h3>
    <span class="question-points">${question.maxScore} điểm</span>
  `;
  
  const optionsContainer = document.createElement('div');
  optionsContainer.className = 'question-options';
  
  question.options.forEach(option => {
    const isSelected = question.studentAnswer === option.id;
    const isCorrect = option.isCorrect;
    
    const optionEl = document.createElement('label');
    optionEl.className = 'option';
    if (isSelected) optionEl.classList.add('selected');
    if (isSelected && isCorrect) optionEl.classList.add('correct');
    if (isSelected && !isCorrect) optionEl.classList.add('incorrect');
    
    optionEl.innerHTML = `
      <span class="option-radio ${isSelected ? 'checked' : ''}"></span>
      <span class="option-text">${option.text}</span>
    `;
    
    optionsContainer.appendChild(optionEl);
  });
  
  card.appendChild(header);
  card.appendChild(optionsContainer);
  
  return card;
}

// File upload and essay card functions removed - only multiple-choice supported

/**
 * Update statistics display
 * @param {Array} questions 
 */
function updateStats(questions) {
  const unchecked = questions.filter(q => q.status === 'unchecked').length;
  const checked = questions.filter(q => q.status === 'checked').length;
  
  const uncheckedEl = document.getElementById('uncheckedCount');
  const checkedEl = document.getElementById('checkedCount');
  
  if (uncheckedEl) uncheckedEl.textContent = unchecked;
  if (checkedEl) checkedEl.textContent = checked;
}

// ==================== USER INTERACTIONS ====================

/**
 * Scroll to a specific question
 * @param {string} questionId 
 */
window.scrollToQuestion = function(questionId) {
  const element = document.getElementById(`question-${questionId}`);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    element.classList.add('highlight');
    setTimeout(() => element.classList.remove('highlight'), 2000);
  }
};

/**
 * Filter questions by status
 */
window.filterQuestions = function() {
  const filterValue = document.getElementById('filterStatus')?.value || 'all';
  const cards = document.querySelectorAll('.question-card');
  
  cards.forEach(card => {
    const status = card.dataset.status || 'unchecked';
    
    if (filterValue === 'all') {
      card.style.display = '';
    } else if (filterValue === status) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
};

/**
 * Submit score for a question
 * @param {HTMLElement} button 
 */
window.submitScore = async function(button) {
  const questionId = button.dataset.questionId;
  const card = document.getElementById(`question-${questionId}`);
  const scoreInput = card.querySelector('.score-input');
  const score = parseFloat(scoreInput.value);
  const maxScore = parseFloat(scoreInput.max);
  
  // Validation
  if (isNaN(score) || score < 0 || score > maxScore) {
    if (window.Toast) Toast.error(`Điểm phải từ 0 đến ${maxScore}`);
    return;
  }
  
  try {
    // Disable button
    button.disabled = true;
    button.textContent = 'Đang lưu...';
    
    // Send to backend
    await API.updateScore(questionId, score);
    
    // Update UI
    updateQuestionStatus(questionId, 'checked');
    button.textContent = '✓ Đã lưu';
    
    // Show success message
    showNotification('Đã lưu điểm thành công!', 'success');
    
    // Refresh stats
    const data = await API.getStudentAnswers(EXAM_ID, STUDENT_ID);
    updateStats(data.questions);
    
    // Reset button after delay
    setTimeout(() => {
      button.textContent = 'Xác nhận';
      button.disabled = false;
    }, 2000);
    
  } catch (error) {
    console.error('Error submitting score:', error);
    alert('Lỗi khi lưu điểm: ' + error.message);
    button.textContent = 'Xác nhận';
    button.disabled = false;
  }
};

/**
 * Update question status in UI
 * @param {string} questionId 
 * @param {string} status 
 */
function updateQuestionStatus(questionId, status) {
  // Update navigation button
  const navBtn = document.querySelector(`.question-btn[data-question-id="${questionId}"]`);
  if (navBtn) {
    navBtn.dataset.status = status;
    navBtn.className = `question-btn status-${status}`;
    navBtn.setAttribute('aria-label', `Câu hỏi ${navBtn.textContent} - ${status === 'checked' ? 'Đã chấm' : 'Chưa chấm'}`);
  }
  
  // Update card
  const card = document.getElementById(`question-${questionId}`);
  if (card) {
    card.dataset.status = status;
  }
}

/**
 * Show notification message
 * @param {string} message 
 * @param {string} type 
 */
function showNotification(message, type = 'info') {
  // Simple notification - can be replaced with better UI
  console.log(`[${type.toUpperCase()}] ${message}`);
  
  // TODO: Implement better notification UI
  // You can use a toast library or create custom notification component
}

// ==================== INITIALIZATION ====================

/**
 * Initialize the page
 */
async function init() {
  try {
    console.log('Initializing Cham Bai page...');
    console.log(`Exam ID: ${EXAM_ID}, Student ID: ${STUDENT_ID}`);
    
    // Show loading state (optional)
    const questionsList = document.getElementById('questionsList');
    if (questionsList) {
      questionsList.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">Đang tải dữ liệu...</div>';
    }
    
    // Fetch data from API
    const data = await API.getStudentAnswers(EXAM_ID, STUDENT_ID);
    console.log('Data loaded:', data);
    
    // Render UI
    renderNavigation(data.questions);
    renderQuestions(data.questions);
    
    console.log('Initialization complete!');
    
  } catch (error) {
    console.error('Failed to initialize:', error);
    
    // Show error message
    const questionsList = document.getElementById('questionsList');
    if (questionsList) {
      questionsList.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #f44336;">
          <h3>Không thể tải dữ liệu</h3>
          <p>${error.message}</p>
          <button onclick="window.location.reload()" style="margin-top: 16px; padding: 10px 24px; background: #2196f3; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Thử lại
          </button>
        </div>
      `;
    }
  }
}

// Start when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

// Export for debugging
window.ChamBaiModule = {
  API,
  renderNavigation,
  renderQuestions,
  updateStats,
  EXAM_ID,
  STUDENT_ID
};

console.log('ChamBai module loaded. Debug with window.ChamBaiModule');
