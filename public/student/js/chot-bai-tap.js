/**
 * File: chot-bai-tap.js
 * Mục đích: Hiển thị bài làm đã nộp (read-only)
 */

class ChotBaiTapManager {
    constructor() {
        this.baiTapId = null;
        this.lopHocId = null;
        this.chuongId = null;
        this.chiTietBaiTap = null;
        this.danhSachCauHoi = [];
        this.baiLam = null;
    }

    /**
     * Khởi tạo trang chốt bài tập
     */
    async init() {
        try {
            // Lấy params từ URL
            const urlParams = new URLSearchParams(window.location.search);
            this.baiTapId = urlParams.get('bai_tap_id');
            this.lopHocId = urlParams.get('lop_hoc_id');
            this.chuongId = urlParams.get('chuong_id');

            if (!this.baiTapId) {
                throw new Error('Không tìm thấy ID bài tập');
            }

            // Load dữ liệu
            await this.loadChiTietBaiLam();

            // Render UI
            this.renderBaiLam();

            // Setup nút trở lại
            this.setupBackButton();

        } catch (error) {
            console.error('Lỗi khởi tạo trang chốt bài tập:', error);
            ThongBao.loi('Không thể tải bài làm. Vui lòng thử lại.');
        }
    }

    /**
     * Load chi tiết bài làm từ API
     */
    async loadChiTietBaiLam() {
        try {
            const response = await fetch(`/backend/student/api/chi-tiet-bai-tap.php?bai_tap_id=${this.baiTapId}`, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (!result.thanh_cong) {
                throw new Error(result.thong_bao || 'Không thể tải bài làm');
            }

            // Lưu dữ liệu
            this.chiTietBaiTap = result.du_lieu.bai_tap;
            this.danhSachCauHoi = result.du_lieu.cau_hoi;
            this.baiLam = result.du_lieu.bai_lam;

            // Kiểm tra nếu chưa nộp bài
            if (!this.baiLam || this.baiLam.trang_thai === 'chua_lam' || this.baiLam.trang_thai === 'dang_lam') {
                throw new Error('Bài tập chưa được nộp');
            }

        } catch (error) {
            console.error('Lỗi load chi tiết bài làm:', error);
            throw error;
        }
    }

    /**
     * Render bài làm
     */
    renderBaiLam() {
        // Render tiêu đề
        const titleElement = document.querySelector('.assignment-title h1');
        if (titleElement) {
            titleElement.textContent = this.chiTietBaiTap.tieu_de;
        }

        // Render danh sách câu hỏi và trả lời
        this.renderDanhSachCauHoi();
    }

    /**
     * Render danh sách câu hỏi và trả lời
     */
    renderDanhSachCauHoi() {
        const contentWrapper = document.querySelector('.content-wrapper');
        if (!contentWrapper) return;

        // Xóa nội dung cũ
        const existingQuestions = contentWrapper.querySelectorAll('.question-wrapper');
        existingQuestions.forEach(el => el.remove());

        // Render từng câu hỏi
        this.danhSachCauHoi.forEach((cauHoi, index) => {
            const questionElement = this.createQuestionElement(cauHoi, index + 1);
            contentWrapper.appendChild(questionElement);
        });
    }

    /**
     * Tạo element cho một câu hỏi (read-only)
     */
    createQuestionElement(cauHoi, stt) {
        const wrapper = document.createElement('div');
        wrapper.className = 'question-wrapper';
        wrapper.style.cssText = `
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        `;

        // Câu hỏi với điểm tối đa
        const questionDiv = document.createElement('div');
        questionDiv.className = 'question-content';
        questionDiv.style.cssText = `
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        `;
        questionDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: start; gap: 16px;">
                <div style="flex: 1;">
                    <span style="font-weight: 600; color: #495057;">Câu ${stt}:</span>
                    <span style="margin-left: 8px; color: #212529;">${cauHoi.noi_dung_cau_hoi}</span>
                </div>
                <div style="min-width: 80px; text-align: right; color: #6c757d; font-size: 14px;">
                    ${cauHoi.diem} điểm
                </div>
            </div>
        `;

        // Trả lời
        const answerDiv = document.createElement('div');
        answerDiv.className = 'answer-content';
        
        const traLoi = cauHoi.tra_loi_da_luu || '';
        const isAnswered = traLoi.trim() !== '';
        const diemDatDuoc = cauHoi.diem_dat_duoc;

        // Tính toán điểm hiển thị
        let diemHienThi = '';
        let diemColor = '#6c757d';
        
        if (diemDatDuoc !== null && diemDatDuoc !== undefined) {
            diemHienThi = `${diemDatDuoc} điểm`;
            diemColor = diemDatDuoc > 0 ? '#28a745' : '#dc3545';
        } else {
            diemHienThi = 'Chưa chấm';
            diemColor = '#6c757d';
        }

        answerDiv.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div style="font-weight: 500; color: #495057;">
                    Câu trả lời của bạn:
                </div>
                <div style="font-weight: 600; color: ${diemColor}; font-size: 15px;">
                    ${diemHienThi}
                </div>
            </div>
            <div style="
                background: ${isAnswered ? '#f8f9fa' : '#fff3cd'};
                border: 1px solid ${isAnswered ? '#dee2e6' : '#ffc107'};
                border-radius: 6px;
                padding: 12px;
                min-height: 80px;
                white-space: pre-wrap;
                word-wrap: break-word;
                color: ${isAnswered ? '#212529' : '#856404'};
                font-style: ${isAnswered ? 'normal' : 'italic'};
            ">
                ${isAnswered ? traLoi : 'Chưa trả lời câu này'}
            </div>
        `;

        // Phần bình luận
        const commentSection = this.createCommentSection(cauHoi.id);

        wrapper.appendChild(questionDiv);
        wrapper.appendChild(answerDiv);
        wrapper.appendChild(commentSection);

        return wrapper;
    }

    /**
     * Tạo phần bình luận cho câu hỏi
     */
    createCommentSection(cauHoiId) {
        const section = document.createElement('div');
        section.className = 'comment-section';
        section.setAttribute('data-cau-hoi-id', cauHoiId);
        section.style.cssText = `
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
        `;

        section.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; cursor: pointer;" class="comment-toggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span style="font-weight: 500; color: #495057;">Thảo luận câu này</span>
                <span class="comment-count" style="color: #6c757d; font-size: 14px;">(0)</span>
            </div>
            <div class="comment-list" style="display: none; margin-bottom: 12px;"></div>
            <div class="comment-input-wrapper" style="display: none;">
                <div style="display: flex; gap: 8px;">
                    <textarea 
                        class="comment-input" 
                        placeholder="Nhập bình luận của bạn..."
                        style="
                            flex: 1;
                            border: 1px solid #dee2e6;
                            border-radius: 6px;
                            padding: 8px 12px;
                            font-family: 'Be Vietnam Pro', sans-serif;
                            font-size: 14px;
                            resize: vertical;
                            min-height: 60px;
                        "
                    ></textarea>
                    <button 
                        class="btn-send-comment"
                        style="
                            background: #3293f9;
                            color: white;
                            border: none;
                            border-radius: 6px;
                            padding: 8px 16px;
                            cursor: pointer;
                            font-weight: 500;
                            align-self: flex-start;
                        "
                    >
                        Gửi
                    </button>
                </div>
            </div>
        `;

        // Setup sự kiện
        const toggle = section.querySelector('.comment-toggle');
        const commentList = section.querySelector('.comment-list');
        const inputWrapper = section.querySelector('.comment-input-wrapper');
        const sendBtn = section.querySelector('.btn-send-comment');
        const input = section.querySelector('.comment-input');

        let isExpanded = false;

        toggle.addEventListener('click', async () => {
            isExpanded = !isExpanded;
            commentList.style.display = isExpanded ? 'block' : 'none';
            inputWrapper.style.display = isExpanded ? 'block' : 'none';

            if (isExpanded && commentList.children.length === 0) {
                await this.loadComments(cauHoiId, commentList, section);
            }
        });

        sendBtn.addEventListener('click', async () => {
            const noiDung = input.value.trim();
            if (noiDung) {
                await this.sendComment(cauHoiId, noiDung, commentList, input, section);
            }
        });

        // Load số lượng bình luận ngay khi khởi tạo
        this.loadCommentCount(cauHoiId, section);

        return section;
    }

    /**
     * Load số lượng bình luận (không load nội dung)
     */
    async loadCommentCount(cauHoiId, section) {
        try {
            const response = await fetch(`/backend/student/api/lay-binh-luan-cau-hoi.php?bai_tap_id=${this.baiTapId}&cau_hoi_id=${cauHoiId}`, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (result.thanh_cong) {
                const comments = result.du_lieu;
                const countEl = section.querySelector('.comment-count');
                countEl.textContent = `(${comments.length})`;
            }
        } catch (error) {
            console.error('Lỗi load số lượng bình luận:', error);
        }
    }

    /**
     * Load bình luận
     */
    async loadComments(cauHoiId, commentList, section) {
        try {
            const response = await fetch(`/backend/student/api/lay-binh-luan-cau-hoi.php?bai_tap_id=${this.baiTapId}&cau_hoi_id=${cauHoiId}`, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (result.thanh_cong) {
                const comments = result.du_lieu;
                this.renderComments(comments, commentList, section);
            }
        } catch (error) {
            console.error('Lỗi load bình luận:', error);
        }
    }

    /**
     * Render danh sách bình luận
     */
    renderComments(comments, commentList, section) {
        commentList.innerHTML = '';
        
        const countEl = section.querySelector('.comment-count');
        countEl.textContent = `(${comments.length})`;

        comments.forEach(comment => {
            const commentEl = document.createElement('div');
            commentEl.style.cssText = `
                background: #f8f9fa;
                border-radius: 6px;
                padding: 12px;
                margin-bottom: 8px;
            `;

            const isTeacher = comment.nguoi_gui.vai_tro === 'giang_vien';
            const defaultAvatar = isTeacher ? './assets/avatar-gv.webp' : '/public/student/CSS/avatar-sv.webp';
            const avatar = comment.nguoi_gui.anh_dai_dien || defaultAvatar;
            const time = new Date(comment.thoi_gian_gui).toLocaleString('vi-VN');

            commentEl.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <img src="${avatar}" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                    <span style="font-weight: 600; color: ${isTeacher ? '#3293f9' : '#212529'}; font-size: 14px;">
                        ${isTeacher ? '👨‍🏫 ' : ''}${comment.nguoi_gui.ho_ten}
                    </span>
                    <span style="color: #6c757d; font-size: 12px;">${time}</span>
                </div>
                <div style="color: #495057; font-size: 14px; white-space: pre-wrap; word-wrap: break-word;">
                    ${comment.noi_dung}
                </div>
            `;

            commentList.appendChild(commentEl);
        });
    }

    /**
     * Gửi bình luận
     */
    async sendComment(cauHoiId, noiDung, commentList, input, section) {
        try {
            const response = await fetch('/backend/student/api/them-binh-luan-cau-hoi.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bai_tap_id: this.baiTapId,
                    cau_hoi_id: cauHoiId,
                    noi_dung: noiDung
                })
            });

            const result = await response.json();

            if (result.thanh_cong) {
                input.value = '';
                ThongBao.thanh_cong('Gửi bình luận thành công');
                await this.loadComments(cauHoiId, commentList, section);
            } else {
                ThongBao.loi(result.thong_bao || 'Không thể gửi bình luận');
            }
        } catch (error) {
            console.error('Lỗi gửi bình luận:', error);
            ThongBao.loi('Không thể gửi bình luận');
        }
    }

    /**
     * Setup nút trở lại chương
     */
    setupBackButton() {
        const backBtn = document.querySelector('.btn-back');
        if (backBtn) {
            backBtn.style.cursor = 'pointer';
            backBtn.addEventListener('click', () => {
                if (this.lopHocId && this.chuongId) {
                    window.location.href = `Thông%20tin%20bài%20giảng.html?lop_hoc_id=${this.lopHocId}&chuong_id=${this.chuongId}`;
                } else {
                    window.history.back();
                }
            });
        }
    }
}

// Khởi tạo khi DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    const manager = new ChotBaiTapManager();
    manager.init();
});

export default ChotBaiTapManager;
