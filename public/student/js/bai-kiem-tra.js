// Bài kiểm tra functionality

const questions = [];
const correctAnswers = []; // Index of correct answer for each question

// Generate 25 questions with random correct answers
for (let i = 0; i < 25; i++) {
    questions.push({
        question: `Nội dung câu hỏi ${i + 1}: This is a sample question about something important?`,
        answers: ["A. Lựa chọn A", "B. Lựa chọn B", "C. Lựa chọn C", "D. Lựa chọn D"],
        selected: null
    });
    correctAnswers.push(Math.floor(Math.random() * 4)); // Random correct answer
}

let currentQuestion = 0;
let timeLeft = 45 * 60 + 59;
let timerInterval;
let startTime = Date.now();

function initQuestionGrid() {
    const grid = document.getElementById('questionGrid');
    grid.innerHTML = '';
    for (let i = 0; i < questions.length; i++) {
        const btn = document.createElement('div');
        btn.className = 'question-number unanswered';
        btn.textContent = i + 1;
        btn.onclick = () => goToQuestion(i);
        grid.appendChild(btn);
    }
    updateQuestionGrid();
}

function updateQuestionGrid() {
    const buttons = document.querySelectorAll('.question-number');
    buttons.forEach((btn, index) => {
        btn.classList.remove('answered', 'current', 'unanswered');
        if (index === currentQuestion) {
            btn.classList.add('current');
        } else if (questions[index].selected !== null) {
            btn.classList.add('answered');
        } else {
            btn.classList.add('unanswered');
        }
    });
    updateCounts();
}

function updateCounts() {
    const answered = questions.filter(q => q.selected !== null).length;
    const unanswered = questions.length - answered;
    document.getElementById('answeredCount').textContent = `Đã trả lời (${answered})`;
    document.getElementById('unansweredCount').textContent = `Chưa trả lời (${unanswered})`;
}

function displayQuestion() {
    const q = questions[currentQuestion];
    document.getElementById('questionTitle').textContent = `Câu hỏi ${currentQuestion + 1} trên ${questions.length}`;
    document.getElementById('questionText').textContent = q.question;
    
    const container = document.getElementById('answersContainer');
    container.innerHTML = '';
    q.answers.forEach((answer, index) => {
        const option = document.createElement('div');
        option.className = 'answer-option';
        if (q.selected === index) {
            option.classList.add('selected');
        }
        option.innerHTML = `
            <div class="radio"></div>
            <div class="answer-text">${answer}</div>
        `;
        option.onclick = () => selectAnswer(index);
        container.appendChild(option);
    });
}

function selectAnswer(index) {
    questions[currentQuestion].selected = index;
    displayQuestion();
    updateQuestionGrid();
}

function goToQuestion(index) {
    currentQuestion = index;
    displayQuestion();
    updateQuestionGrid();
}

function previousQuestion() {
    if (currentQuestion > 0) {
        currentQuestion--;
        displayQuestion();
        updateQuestionGrid();
    }
}

function nextQuestion() {
    if (currentQuestion < questions.length - 1) {
        currentQuestion++;
        displayQuestion();
        updateQuestionGrid();
    }
}

function submitExam() {
    const answered = questions.filter(q => q.selected !== null).length;
    const unanswered = questions.length - answered;
    
    if (unanswered > 0) {
        const confirm = window.confirm(`Bạn còn ${unanswered} câu chưa trả lời. Bạn có chắc chắn muốn nộp bài?`);
        if (!confirm) return;
    }
    
    clearInterval(timerInterval);
    showResultsPage();
}

function showResultsPage() {
    document.querySelector('.exam-page').classList.remove('active');
    document.querySelector('.results-page').classList.add('active');
    
    // Calculate results
    let correct = 0;
    let wrong = 0;
    let skipped = 0;
    
    questions.forEach((q, index) => {
        if (q.selected === null) {
            skipped++;
        } else if (q.selected === correctAnswers[index]) {
            correct++;
        } else {
            wrong++;
        }
    });
    
    const score = ((correct / questions.length) * 10).toFixed(1);
    const timeSpentMinutes = Math.floor((45 * 60 + 59 - timeLeft) / 60);
    
    // Update stats
    document.getElementById('finalScore').textContent = score;
    document.getElementById('correctCount').textContent = `${correct} câu`;
    document.getElementById('wrongCount').textContent = `${wrong} câu`;
    document.getElementById('skippedCount').textContent = `${skipped} câu`;
    document.getElementById('timeSpent').textContent = `${timeSpentMinutes} phút`;
    document.getElementById('totalQuestions').textContent = `${questions.length} câu`;
    
    // Display results list
    const resultsList = document.getElementById('resultsList');
    resultsList.innerHTML = '';
    
    questions.forEach((q, index) => {
        const item = document.createElement('div');
        item.className = 'result-item';
        
        let indicator = 'skipped';
        if (q.selected !== null) {
            indicator = q.selected === correctAnswers[index] ? 'correct' : 'wrong';
        }
        
        let answersHTML = '';
        q.answers.forEach((answer, ansIndex) => {
            let answerClass = '';
            if (ansIndex === q.selected) {
                answerClass = indicator === 'correct' ? 'correct-answer' : 'user-answer';
            } else if (ansIndex === correctAnswers[index]) {
                answerClass = 'correct-answer';
            }
            
            answersHTML += `
                <div class="result-answer ${answerClass}">
                    <div class="result-radio"></div>
                    <div class="result-answer-text">${answer}</div>
                </div>
            `;
        });
        
        item.innerHTML = `
            <div class="result-indicator ${indicator}"></div>
            <div class="result-content">
                <div class="result-question">Câu hỏi ${index + 1} : ${q.question}</div>
                <div class="result-answers">${answersHTML}</div>
            </div>
        `;
        
        resultsList.appendChild(item);
    });
}

function showExamPage() {
    document.querySelector('.results-page').classList.remove('active');
    document.querySelector('.exam-page').classList.add('active');
    
    // Reset exam
    questions.forEach(q => q.selected = null);
    currentQuestion = 0;
    timeLeft = 45 * 60 + 59;
    startTime = Date.now();
    
    initQuestionGrid();
    displayQuestion();
    startTimer();
}

function toggleSubmenu(element) {
    element.classList.toggle('expanded');
    const submenu = element.nextElementSibling;
    submenu.classList.toggle('show');
}

function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeLeft > 0) {
        timeLeft--;
    } else {
        clearInterval(timerInterval);
        alert('Hết giờ làm bài! Bài thi sẽ được tự động nộp.');
        submitExam();
    }
}

function startTimer() {
    clearInterval(timerInterval);
    timerInterval = setInterval(updateTimer, 1000);
}

// Initialize on page load
initQuestionGrid();
displayQuestion();
startTimer();
