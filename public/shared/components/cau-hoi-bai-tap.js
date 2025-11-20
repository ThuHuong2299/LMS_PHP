/**
 * File: cau-hoi-bai-tap.js
 * Mục đích: Component dùng chung để render câu hỏi bài tập (SV + GV)
 */

class CauHoiBaiTapRenderer {
    /**
     * Tạo element cho một câu hỏi
     * @param {Object} cauHoi - Dữ liệu câu hỏi
     * @param {Number} stt - Số thứ tự câu hỏi
     * @param {Object} options - Tùy chọn render
     * @returns {HTMLElement}
     */
    static createQuestionElement(cauHoi, stt, options = {}) {
        const {
            showScoreInput = false,     // Hiển thị input chấm điểm (GV)
            readOnly = true,            // Chế độ chỉ đọc
            allowComment = true,        // Cho phép bình luận
            onScoreSave = null,         // Callback khi lưu điểm
            baiTapId = null,            // ID bài tập (cho API comment)
            sinhVienId = null,          // ID sinh viên (cho API comment - GV only)
            apiEndpoint = null          // Endpoint API cho comments
        } = options;

        const wrapper = document.createElement('div');
        wrapper.className = 'question-wrapper';
        wrapper.setAttribute('data-cau-hoi-id', cauHoi.cau_hoi_id || cauHoi.id);

        // 1. QUESTION CONTENT
        const questionDiv = this._createQuestionContent(cauHoi, stt);
        wrapper.appendChild(questionDiv);

        // 2. ANSWER SECTION
        const answerDiv = this._createAnswerSection(cauHoi);
        wrapper.appendChild(answerDiv);

        // 3. SCORE INPUT (chỉ GV)
        if (showScoreInput) {
            const scoreInputDiv = this._createScoreInputSection(cauHoi, onScoreSave);
            wrapper.appendChild(scoreInputDiv);
        }

        // 4. COMMENT SECTION
        if (allowComment) {
            const commentDiv = this._createCommentSection(
                cauHoi.cau_hoi_id || cauHoi.id,
                baiTapId,
                sinhVienId,
                apiEndpoint
            );
            wrapper.appendChild(commentDiv);
        }

        return wrapper;
    }

    /**
     * Tạo phần nội dung câu hỏi
     */
    static _createQuestionContent(cauHoi, stt) {
        const div = document.createElement('div');
        div.className = 'question-content';

        const diemToiDa = cauHoi.diem_toi_da || cauHoi.diem || 0;
        const moTa = cauHoi.mo_ta || cauHoi.cau_hoi_mo_ta || '';

        div.innerHTML = `
            <div class="question-header">
                <div class="question-text">
                    <span class="question-number">Câu ${stt}:</span>
                    <span class="question-title">${this._escapeHtml(cauHoi.noi_dung || cauHoi.noi_dung_cau_hoi || '')}</span>
                    ${moTa ? `<div class="question-description">${this._escapeHtml(moTa)}</div>` : ''}
                </div>
                <div class="score-max">${diemToiDa} điểm</div>
            </div>
        `;

        return div;
    }

    /**
     * Tạo phần câu trả lời
     */
    static _createAnswerSection(cauHoi) {
        const div = document.createElement('div');
        div.className = 'answer-section';

        const traLoi = cauHoi.tra_loi || cauHoi.tra_loi_da_luu || '';
        const noiDungTraLoi = typeof traLoi === 'string' ? traLoi : (traLoi.noi_dung || '');
        const isAnswered = noiDungTraLoi.trim() !== '';

        // Xử lý điểm
        let diemDatDuoc = null;
        if (cauHoi.tra_loi && typeof cauHoi.tra_loi === 'object') {
            diemDatDuoc = cauHoi.tra_loi.diem;
        } else if (cauHoi.diem_dat_duoc !== undefined) {
            diemDatDuoc = cauHoi.diem_dat_duoc;
        }

        let scoreClass = 'not-scored';
        let scoreText = 'Chưa chấm';
        
        if (diemDatDuoc !== null && diemDatDuoc !== undefined) {
            scoreText = `${diemDatDuoc} điểm`;
            scoreClass = diemDatDuoc > 0 ? 'scored' : 'zero-score';
        }

        div.innerHTML = `
            <div class="answer-content ${!isAnswered ? 'empty' : ''}">
                ${isAnswered ? this._escapeHtml(noiDungTraLoi) : 'Chưa trả lời câu này'}
            </div>
        `;

        return div;
    }

    /**
     * Tạo phần input chấm điểm (GV only)
     */
    static _createScoreInputSection(cauHoi, onScoreSave) {
        const div = document.createElement('div');
        div.className = 'score-input-section';

        const traLoi = cauHoi.tra_loi;
        const hasAnswer = traLoi && (traLoi.noi_dung || traLoi.id);
        const currentScore = hasAnswer && traLoi.diem !== null ? traLoi.diem : '';
        const maxScore = cauHoi.diem_toi_da || cauHoi.diem || 0;
        const traLoiId = traLoi ? traLoi.id : '';

        div.innerHTML = `
            <div class="score-input-label">Chấm điểm:</div>
            <div class="score-input-wrapper">
                <input 
                    type="number" 
                    class="score-input" 
                    value="${currentScore}" 
                    placeholder="-"
                    min="0" 
                    max="${maxScore}" 
                    step="0.1"
                    data-cau-hoi-id="${cauHoi.cau_hoi_id || cauHoi.id}"
                    data-tra-loi-id="${traLoiId}"
                    ${!hasAnswer ? 'disabled' : ''}
                />
                <span class="score-separator">/</span>
                <span class="score-max">${maxScore}</span>
            </div>
        `;

        // Add event listener
        if (onScoreSave && hasAnswer) {
            const input = div.querySelector('.score-input');
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const diem = parseFloat(input.value);
                    const cauHoiId = input.dataset.cauHoiId;
                    const traLoiId = input.dataset.traLoiId;

                    if (!isNaN(diem) && traLoiId) {
                        onScoreSave(traLoiId, diem, input, cauHoiId, maxScore);
                    }
                }
            });
        }

        return div;
    }

    /**
     * Tạo phần bình luận
     */
    static _createCommentSection(cauHoiId, baiTapId, sinhVienId, apiEndpoint) {
        const section = document.createElement('div');
        section.className = 'comment-section';
        section.setAttribute('data-cau-hoi-id', cauHoiId);

        section.innerHTML = `
            <div class="comment-toggle">
                <svg class="comment-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span class="comment-toggle-text">Thảo luận câu này</span>
                <span class="comment-count">(0)</span>
            </div>
            <div class="comment-list" style="display: none;"></div>
            <div class="comment-input-wrapper" style="display: none;">
                <textarea 
                    class="comment-input" 
                    placeholder="Nhập bình luận của bạn..."
                    rows="3"
                ></textarea>
                <button class="btn-send-comment">Gửi</button>
            </div>
        `;

        // Setup events
        this._setupCommentEvents(section, cauHoiId, baiTapId, sinhVienId, apiEndpoint);

        return section;
    }

    /**
     * Setup sự kiện cho comment section
     */
    static _setupCommentEvents(section, cauHoiId, baiTapId, sinhVienId, apiEndpoint) {
        const toggle = section.querySelector('.comment-toggle');
        const commentList = section.querySelector('.comment-list');
        const inputWrapper = section.querySelector('.comment-input-wrapper');
        const sendBtn = section.querySelector('.btn-send-comment');
        const input = section.querySelector('.comment-input');

        let isExpanded = false;
        let commentsLoaded = false;

        // Toggle expand/collapse
        toggle.addEventListener('click', async () => {
            isExpanded = !isExpanded;
            toggle.classList.toggle('expanded', isExpanded);
            commentList.style.display = isExpanded ? 'block' : 'none';
            inputWrapper.style.display = isExpanded ? 'flex' : 'none';

            // Load comments lần đầu
            if (isExpanded && !commentsLoaded) {
                await this._loadComments(cauHoiId, baiTapId, sinhVienId, apiEndpoint, commentList, section);
                commentsLoaded = true;
            }
        });

        // Send comment
        sendBtn.addEventListener('click', async () => {
            const noiDung = input.value.trim();
            if (noiDung) {
                await this._sendComment(cauHoiId, baiTapId, sinhVienId, noiDung, apiEndpoint, commentList, input, section);
            }
        });

        // Load comment count ngay khi khởi tạo
        this._loadCommentCount(cauHoiId, baiTapId, sinhVienId, apiEndpoint, section);
    }

    /**
     * Load số lượng comments
     */
    static async _loadCommentCount(cauHoiId, baiTapId, sinhVienId, apiEndpoint, section) {
        try {
            const url = sinhVienId 
                ? `${apiEndpoint}?bai_tap_id=${baiTapId}&sinh_vien_id=${sinhVienId}&cau_hoi_id=${cauHoiId}`
                : `${apiEndpoint}?bai_tap_id=${baiTapId}&cau_hoi_id=${cauHoiId}`;

            const response = await fetch(url, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (result.thanh_cong) {
                const count = result.du_lieu.length;
                const countSpan = section.querySelector('.comment-count');
                if (countSpan) {
                    countSpan.textContent = `(${count})`;
                }
            }
        } catch (error) {
            console.error('Lỗi load comment count:', error);
        }
    }

    /**
     * Load danh sách comments
     */
    static async _loadComments(cauHoiId, baiTapId, sinhVienId, apiEndpoint, commentList, section) {
        try {
            commentList.innerHTML = '<div class="comment-empty">Đang tải...</div>';

            const url = sinhVienId 
                ? `${apiEndpoint}?bai_tap_id=${baiTapId}&sinh_vien_id=${sinhVienId}&cau_hoi_id=${cauHoiId}`
                : `${apiEndpoint}?bai_tap_id=${baiTapId}&cau_hoi_id=${cauHoiId}`;

            const response = await fetch(url, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (result.thanh_cong) {
                const comments = result.du_lieu;
                
                if (comments.length === 0) {
                    commentList.innerHTML = '<div class="comment-empty">Chưa có bình luận nào</div>';
                } else {
                    commentList.innerHTML = '';
                    comments.forEach(comment => {
                        const commentElement = this._createCommentElement(comment);
                        commentList.appendChild(commentElement);
                    });

                    // Update count
                    const countSpan = section.querySelector('.comment-count');
                    if (countSpan) {
                        countSpan.textContent = `(${comments.length})`;
                    }
                }
            } else {
                commentList.innerHTML = '<div class="comment-empty">Không thể tải bình luận</div>';
            }
        } catch (error) {
            console.error('Lỗi load comments:', error);
            commentList.innerHTML = '<div class="comment-empty">Lỗi khi tải bình luận</div>';
        }
    }

    /**
     * Gửi comment mới
     */
    static async _sendComment(cauHoiId, baiTapId, sinhVienId, noiDung, apiEndpoint, commentList, input, section) {
        try {
            const sendBtn = section.querySelector('.btn-send-comment');
            sendBtn.disabled = true;
            sendBtn.textContent = 'Đang gửi...';

            const body = sinhVienId 
                ? { bai_tap_id: baiTapId, sinh_vien_id: sinhVienId, cau_hoi_id: cauHoiId, noi_dung: noiDung }
                : { bai_tap_id: baiTapId, cau_hoi_id: cauHoiId, noi_dung: noiDung };

            const response = await fetch(apiEndpoint, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(body)
            });

            const result = await response.json();

            if (result.thanh_cong) {
                // Clear input
                input.value = '';

                // Reload comments
                await this._loadComments(cauHoiId, baiTapId, sinhVienId, apiEndpoint, commentList, section);

                if (typeof ThongBao !== 'undefined') {
                    ThongBao.thanh_cong('Đã gửi bình luận');
                }
            } else {
                if (typeof ThongBao !== 'undefined') {
                    ThongBao.loi(result.thong_bao || 'Không thể gửi bình luận');
                }
            }

            sendBtn.disabled = false;
            sendBtn.textContent = 'Gửi';

        } catch (error) {
            console.error('Lỗi gửi comment:', error);
            if (typeof ThongBao !== 'undefined') {
                ThongBao.loi('Không thể kết nối đến server');
            }
            
            const sendBtn = section.querySelector('.btn-send-comment');
            sendBtn.disabled = false;
            sendBtn.textContent = 'Gửi';
        }
    }

    /**
     * Tạo element cho một comment
     */
    static _createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';

        const nguoiGui = comment.nguoi_gui;
        const vaiTro = nguoiGui.vai_tro;
        const vaiTroClass = vaiTro === 'giang_vien' ? 'teacher' : 'student';
        
        // Avatar gradient based on role
        const avatarBg = vaiTro === 'giang_vien' 
            ? 'linear-gradient(135deg, #3293f9, #2178d4)'
            : 'linear-gradient(135deg, #28a745, #20c997)';

        // Format time
        const time = this._formatDateTime(comment.thoi_gian_gui);

        div.innerHTML = `
            <div class="comment-avatar" style="background: ${avatarBg};"></div>
            <div class="comment-body">
                <div class="comment-meta">
                    <div class="comment-author ${vaiTroClass}">${this._escapeHtml(nguoiGui.ho_ten)}</div>
                    <div class="comment-time">${time}</div>
                </div>
                <div class="comment-text">${this._escapeHtml(comment.noi_dung)}</div>
            </div>
        `;

        return div;
    }

    /**
     * Escape HTML để tránh XSS
     */
    static _escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format datetime
     */
    static _formatDateTime(datetime) {
        if (!datetime) return '';
        
        const date = new Date(datetime);
        const now = new Date();
        const diff = now - date;
        
        // Nếu trong vòng 24h
        if (diff < 86400000) {
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }
        
        // Ngày khác
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
}

// Export nếu dùng module
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CauHoiBaiTapRenderer;
}
