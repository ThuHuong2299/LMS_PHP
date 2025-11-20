/**
 * File: bai-kiem-tra.js
 * Mục đích: Quản lý trang làm bài kiểm tra trắc nghiệm
 * - Load chi tiết bài kiểm tra từ API
 * - Bắt đầu làm bài
 * - Lưu câu trả lời
 * - Nộp bài và xem kết quả
 */

class BaiKiemTraManager {
    constructor() {
        // Lấy parameters từ URL
        const urlParams = new URLSearchParams(window.location.search);
        this.baiKiemTraId = urlParams.get('bai_kiem_tra_id');
        this.chuongId = urlParams.get('chuong_id');
        this.lopHocId = urlParams.get('lop_hoc_id');
        
        // Data
        this.baiKiemTra = null;
        this.cauHoiList = [];
        this.baiLamId = null;
        this.trangThai = null;
        
        // UI State
        this.currentQuestion = 0;
        this.timeLeft = 0;
        this.timerInterval = null;
        this.startTime = null;
        this.autoSaveInterval = null;
        
        // Validate params
        if (!this.baiKiemTraId || !this.lopHocId) {
            alert('Thiếu thông tin bài kiểm tra! Vui lòng quay lại.');
            window.history.back();
            return;
        }
        
        this.init();
    }
    
    async init() {
        try {
            console.log('Khởi tạo bài kiểm tra...');
            await this.loadChiTietBaiKiemTra();
            
            // Kiểm tra trạng thái
            if (this.trangThai === 'da_nop' || this.trangThai === 'da_cham') {
                // Đã làm xong
                const choPhepLamLai = this.baiKiemTra.cho_phep_lam_lai;
                
                if (choPhepLamLai == 1) {
                    // Cho phép làm lại - hỏi sinh viên
                    const lamLai = confirm('Bạn đã làm bài này rồi. Giảng viên cho phép làm lại.\n\nBạn có muốn làm lại không?\n\n(Lưu ý: Bài làm cũ sẽ vẫn được giữ lại)');
                    
                    if (lamLai) {
                        // Làm lại - bắt đầu bài mới (KHÔNG load lại câu hỏi vì sẽ ghi đè trangThai)
                        console.log('🔄 Sinh viên chọn làm lại bài kiểm tra');
                        
                        // Bắt đầu làm bài mới - batDauLamBai() sẽ tự động gọi initExamPage()
                        await this.batDauLamBai();
                        
                        console.log('✅ Đã khởi tạo bài làm mới, ID:', this.baiLamId);
                        // Không cần gọi gì thêm vì batDauLamBai() đã gọi initExamPage()
                    } else {
                        // Xem kết quả cũ
                        console.log('👁️ Sinh viên chọn xem kết quả cũ');
                        await this.showResultsPage();
                    }
                } else {
                    // Không cho làm lại - hiển thị kết quả
                    console.log('🚫 Không cho phép làm lại, hiển thị kết quả');
                    await this.showResultsPage();
                }
            } else if (this.trangThai === 'dang_lam') {
                // Đang làm dở, tiếp tục
                // Set startTime từ dữ liệu đã load
                if (this.baiKiemTra.thoi_gian_bat_dau_lam) {
                    this.startTime = new Date(this.baiKiemTra.thoi_gian_bat_dau_lam);
                }
                this.initExamPage();
            } else {
                // Chưa làm, bắt đầu
                await this.batDauLamBai();
            }
        } catch (error) {
            console.error('Lỗi khởi tạo:', error);
            alert('Không thể tải bài kiểm tra: ' + error.message);
        }
    }
    
    /**
     * Load chi tiết bài kiểm tra
     */
    async loadChiTietBaiKiemTra() {
        const response = await fetch(
            `../../backend/student/api/chi-tiet-bai-kiem-tra.php?bai_kiem_tra_id=${this.baiKiemTraId}`,
            {
                method: 'GET',
                credentials: 'include'
            }
        );
        
        // Debug: Kiểm tra response
        const responseText = await response.text();
        console.log('Response chi tiết bài kiểm tra:', responseText);
        
        const data = JSON.parse(responseText);
        
        if (data.thanh_cong && data.du_lieu) {
            this.baiKiemTra = data.du_lieu.thong_tin_bai_kiem_tra;
            this.cauHoiList = data.du_lieu.cau_hoi;
            this.trangThai = data.du_lieu.trang_thai_lam_bai;
            this.baiLamId = data.du_lieu.bai_lam_id;
            
            // Format câu hỏi thành cấu trúc cũ để tương thích
            this.cauHoiList = this.cauHoiList.map(ch => ({
                id: ch.id,
                thu_tu: ch.thu_tu,
                question: ch.noi_dung_cau_hoi,
                diem: ch.diem,
                answers: ch.lua_chon.map(lc => lc.noi_dung_lua_chon),
                lua_chon: ch.lua_chon,
                selected: null // Sẽ được cập nhật nếu đang làm dở
            }));
            
            console.log('✓ Loaded bài kiểm tra:', this.baiKiemTra.tieu_de);
        } else {
            throw new Error(data.thong_bao || 'Không thể tải bài kiểm tra');
        }
    }
    
    /**
     * Bắt đầu làm bài
     */
    async batDauLamBai() {
        try {
            const response = await fetch(
                '../../backend/student/api/bat-dau-bai-kiem-tra.php',
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bai_kiem_tra_id: parseInt(this.baiKiemTraId)
                    })
                }
            );
            
            const data = await response.json();
            
            if (data.thanh_cong && data.du_lieu) {
                this.baiLamId = data.du_lieu.bai_lam_id;
                this.startTime = new Date(data.du_lieu.thoi_gian_bat_dau);
                this.trangThai = 'dang_lam';
                
                console.log('✓ Bắt đầu làm bài, ID:', this.baiLamId);
                this.initExamPage();
            } else {
                throw new Error(data.thong_bao || 'Không thể bắt đầu làm bài');
            }
        } catch (error) {
            console.error('Lỗi bắt đầu làm bài:', error);
            alert('Không thể bắt đầu làm bài: ' + error.message);
        }
    }
    
    /**
     * Khởi tạo trang làm bài
     */
    initExamPage() {
        // Hiển thị trang làm bài
        document.querySelector('.exam-page').classList.add('active');
        document.querySelector('.results-page').classList.remove('active');
        
        // Set tiêu đề
        document.querySelector('.exam-title').textContent = this.baiKiemTra.tieu_de;
        
        // Tính thời gian còn lại
        this.calculateTimeLeft();
        
        // Init UI
        this.initQuestionGrid();
        this.displayQuestion();
        this.startTimer();
        this.startAutoSave();
    }
    
    /**
     * Tính thời gian còn lại
     */
    calculateTimeLeft() {
        if (this.trangThai === 'dang_lam' && this.baiLamId && this.startTime) {
            // Đang làm dở - tính dựa trên thời gian đã bắt đầu
            const now = new Date();
            const batDau = this.startTime; // Dùng startTime từ batDauLamBai() (đã set khi bắt đầu bài MỚI)
            const thoiLuongGiay = this.baiKiemTra.thoi_luong * 60; // Chuyển phút sang giây
            const elapsedSeconds = Math.floor((now - batDau) / 1000);
            this.timeLeft = Math.max(0, thoiLuongGiay - elapsedSeconds);
            
            console.log('⏱️ Tính thời gian:', {
                batDau: batDau.toISOString(),
                now: now.toISOString(),
                elapsedSeconds,
                timeLeft: this.timeLeft
            });
        } else {
            // Chưa làm - dùng toàn bộ thời lượng
            this.timeLeft = this.baiKiemTra.thoi_luong * 60; // Phút -> giây
            console.log('⏱️ Bài mới - thời gian đầy đủ:', this.timeLeft, 'giây');
        }
    }

    
    /**
     * Khởi tạo grid câu hỏi
     */
    initQuestionGrid() {
        const grid = document.getElementById('questionGrid');
        grid.innerHTML = '';
        for (let i = 0; i < this.cauHoiList.length; i++) {
            const btn = document.createElement('div');
            btn.className = 'question-number unanswered';
            btn.textContent = i + 1;
            btn.onclick = () => this.goToQuestion(i);
            grid.appendChild(btn);
        }
        this.updateQuestionGrid();
    }
    
    /**
     * Cập nhật grid câu hỏi
     */
    updateQuestionGrid() {
        const buttons = document.querySelectorAll('.question-number');
        buttons.forEach((btn, index) => {
            btn.classList.remove('answered', 'current', 'unanswered');
            if (index === this.currentQuestion) {
                btn.classList.add('current');
            } else if (this.cauHoiList[index].selected !== null) {
                btn.classList.add('answered');
            } else {
                btn.classList.add('unanswered');
            }
        });
        this.updateCounts();
    }
    
    /**
     * Cập nhật số lượng đã/chưa trả lời
     */
    updateCounts() {
        const answered = this.cauHoiList.filter(q => q.selected !== null).length;
        const unanswered = this.cauHoiList.length - answered;
        document.getElementById('answeredCount').textContent = `Đã trả lời (${answered})`;
        document.getElementById('unansweredCount').textContent = `Chưa trả lời (${unanswered})`;
    }
    
    /**
     * Hiển thị câu hỏi hiện tại
     */
    displayQuestion() {
        const q = this.cauHoiList[this.currentQuestion];
        document.getElementById('questionTitle').textContent = `Câu hỏi ${this.currentQuestion + 1} trên ${this.cauHoiList.length}`;
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
            option.onclick = () => this.selectAnswer(index);
            container.appendChild(option);
        });
    }
    
    /**
     * Chọn đáp án
     */
    async selectAnswer(index) {
        const cauHoi = this.cauHoiList[this.currentQuestion];
        cauHoi.selected = index;
        
        // Lưu câu trả lời lên server
        const luaChonId = cauHoi.lua_chon[index].id;
        await this.luuTraLoi(cauHoi.id, luaChonId);
        
        this.displayQuestion();
        this.updateQuestionGrid();
    }
    
    /**
     * Lưu câu trả lời lên server
     */
    async luuTraLoi(cauHoiId, luaChonId) {
        try {
            console.log('🔄 Đang lưu câu trả lời:', {
                bai_lam_id: this.baiLamId,
                cau_hoi_id: cauHoiId,
                lua_chon_id: luaChonId
            });
            
            const response = await fetch(
                '../../backend/student/api/luu-tra-loi-kiem-tra.php',
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bai_lam_id: this.baiLamId,
                        cau_hoi_id: cauHoiId,
                        lua_chon_id: luaChonId
                    })
                }
            );
            
            const data = await response.json();
            console.log('📥 Kết quả lưu câu trả lời:', data);
            
            if (!data.thanh_cong) {
                console.error('❌ Lỗi lưu câu trả lời:', data.thong_bao);
                alert('Lỗi lưu câu trả lời: ' + data.thong_bao);
            } else {
                console.log('✅ Lưu câu trả lời thành công');
            }
        } catch (error) {
            console.error('❌ Lỗi lưu câu trả lời:', error);
            alert('Lỗi lưu câu trả lời: ' + error.message);
        }
    }
    
    /**
     * Chuyển đến câu hỏi
     */
    goToQuestion(index) {
        this.currentQuestion = index;
        this.displayQuestion();
        this.updateQuestionGrid();
    }
    
    /**
     * Câu trước
     */
    previousQuestion() {
        if (this.currentQuestion > 0) {
            this.currentQuestion--;
            this.displayQuestion();
            this.updateQuestionGrid();
        }
    }
    
    /**
     * Câu tiếp theo
     */
    nextQuestion() {
        if (this.currentQuestion < this.cauHoiList.length - 1) {
            this.currentQuestion++;
            this.displayQuestion();
            this.updateQuestionGrid();
        }
    }
    
    /**
     * Nộp bài
     */
    async submitExam() {
        const answered = this.cauHoiList.filter(q => q.selected !== null).length;
        const unanswered = this.cauHoiList.length - answered;
        
        if (unanswered > 0) {
            const confirmSubmit = confirm(`Bạn còn ${unanswered} câu chưa trả lời. Bạn có chắc chắn muốn nộp bài?`);
            if (!confirmSubmit) return;
        }
        
        try {
            // Dừng timer
            clearInterval(this.timerInterval);
            clearInterval(this.autoSaveInterval);
            
            // Hiển thị loading
            const submitBtn = document.querySelector('.submit-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Đang nộp bài...';
            submitBtn.disabled = true;
            
            // Gọi API nộp bài
            const response = await fetch(
                '../../backend/student/api/nop-bai-kiem-tra.php',
                {
                    method: 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        bai_lam_id: this.baiLamId
                    })
                }
            );
            
            const data = await response.json();
            
            if (data.thanh_cong && data.du_lieu) {
                console.log('✓ Nộp bài thành công');
                // Hiển thị trang kết quả
                await this.showResultsPage();
            } else {
                throw new Error(data.thong_bao || 'Không thể nộp bài');
            }
        } catch (error) {
            console.error('Lỗi nộp bài:', error);
            alert('Không thể nộp bài: ' + error.message);
            
            // Khôi phục button
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.textContent = 'Nộp bài';
            submitBtn.disabled = false;
            
            // Khởi động lại timer
            this.startTimer();
        }
    }

    
    /**
     * Hiển thị trang kết quả
     */
    async showResultsPage() {
        try {
            // Lấy kết quả từ API
            const response = await fetch(
                `../../backend/student/api/ket-qua-bai-kiem-tra.php?bai_lam_id=${this.baiLamId}`,
                {
                    method: 'GET',
                    credentials: 'include'
                }
            );
            
            const data = await response.json();
            
            if (!data.thanh_cong || !data.du_lieu) {
                throw new Error(data.thong_bao || 'Không thể tải kết quả');
            }
            
            const ketQua = data.du_lieu;
            const thongTinBaiLam = ketQua.thong_tin_bai_lam;
            const cauHoiVaDapAn = ketQua.cau_hoi_va_dap_an;
            
            // Hiển thị điểm theo format: X.X/10
            const diemDatDuoc = thongTinBaiLam.diem || 0;
            document.getElementById('scorePercentage').textContent = diemDatDuoc.toFixed(1);
            
            // Tính thời gian làm bài
            const startTime = new Date(thongTinBaiLam.thoi_gian_bat_dau);
            const endTime = new Date(thongTinBaiLam.thoi_gian_nop);
            const timeSpentSeconds = Math.floor((endTime - startTime) / 1000);
            document.getElementById('timeSpent').textContent = this.formatTime(timeSpentSeconds);
            
            // Tính số câu đúng, sai
            const soCauDung = thongTinBaiLam.so_cau_dung || 0;
            const tongSoCau = thongTinBaiLam.tong_so_cau || 0;
            
            // Đếm số câu đã làm
            let soCauDaLam = 0;
            cauHoiVaDapAn.forEach(cauHoi => {
                if (cauHoi.lua_chon_da_chon !== null) {
                    soCauDaLam++;
                }
            });
            
            const soCauSai = soCauDaLam - soCauDung;
            
            // Hiển thị thống kê
            document.getElementById('correctCount').textContent = `${soCauDung} câu`;
            document.getElementById('wrongCount').textContent = `${soCauSai} câu`;
            
            // Cập nhật tiêu đề
            document.querySelectorAll('.exam-title').forEach(el => {
                el.textContent = thongTinBaiLam.tieu_de;
            });
            
            // Hiển thị chi tiết từng câu
            const reviewList = document.getElementById('reviewList');
            reviewList.innerHTML = '';
            
            cauHoiVaDapAn.forEach((cauHoi, i) => {
                const isCorrect = cauHoi.dung_hay_sai === true;
                const item = document.createElement('div');
                item.className = 'review-item';
                item.innerHTML = `
                    <div class="review-header">
                        <span class="review-question-number">Câu ${i + 1}</span>
                        <span class="review-status ${isCorrect ? 'correct' : 'incorrect'}">
                            ${isCorrect ? '✓ Đúng' : '✗ Sai'}
                        </span>
                    </div>
                    <div class="review-question">${cauHoi.noi_dung}</div>
                    <div class="review-answers">
                        ${cauHoi.lua_chon.map(luaChon => `
                            <div class="review-answer ${luaChon.la_dap_an_dung ? 'correct-answer' : ''} ${luaChon.da_chon ? 'selected-answer' : ''}">
                                ${luaChon.noi_dung}
                                ${luaChon.la_dap_an_dung ? '<span class="answer-label">Đáp án đúng</span>' : ''}
                                ${luaChon.da_chon && !luaChon.la_dap_an_dung ? '<span class="answer-label wrong">Bạn đã chọn</span>' : ''}
                            </div>
                        `).join('')}
                    </div>
                `;
                reviewList.appendChild(item);
            });
            
            // Chuyển sang trang kết quả
            document.getElementById('examPage').style.display = 'none';
            document.getElementById('resultsPage').style.display = 'block';
            
        } catch (error) {
            console.error('Lỗi hiển thị kết quả:', error);
            alert('Không thể hiển thị kết quả: ' + error.message);
        }
    }
    
    /**
     * Bắt đầu đếm giờ
     */
    startTimer() {
        this.updateTimer();
        this.timerInterval = setInterval(() => {
            if (this.timeLeft > 0) {
                this.timeLeft--;
                this.updateTimer();
            } else {
                clearInterval(this.timerInterval);
                alert('Hết thời gian làm bài!');
                this.submitExam();
            }
        }, 1000);
    }
    
    /**
     * Cập nhật timer trên UI
     */
    updateTimer() {
        document.getElementById('timer').textContent = this.formatTime(this.timeLeft);
    }
    
    /**
     * Format thời gian
     */
    formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }
    
    /**
     * Tự động lưu định kỳ
     */
    startAutoSave() {
        // Tự động lưu mỗi 30 giây (không cần thiết vì đã lưu ngay khi chọn đáp án)
        // Nhưng giữ lại để backup
        this.autoSaveInterval = setInterval(() => {
            console.log('Auto-save checkpoint');
        }, 30000);
    }
}

// Khởi tạo khi trang load
window.addEventListener('DOMContentLoaded', () => {
    const manager = new BaiKiemTraManager();
    
    // Bind event listeners cho các nút
    document.getElementById('prevBtn')?.addEventListener('click', () => manager.previousQuestion());
    document.getElementById('nextBtn')?.addEventListener('click', () => manager.nextQuestion());
    document.getElementById('submitBtn')?.addEventListener('click', () => manager.submitExam());
});
