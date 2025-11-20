/**
 * File: bai-tap.js
 * Mục đích: Quản lý trang làm bài tập - Load chi tiết bài tập, auto-save, submit
 */

class BaiTapManager {
    constructor() {
        this.baiTapId = null;
        this.baiLamId = null;
        this.baiLamTrangThai = null;
        this.chiTietBaiTap = null;
        this.danhSachCauHoi = [];
        this.traLoiDaLuu = {};
        this.autoSaveTimer = null;
        this.hasUnsavedChanges = false;
    }

    /**
     * Khởi tạo trang bài tập
     */
    async init() {
        try {
            // Lấy bai_tap_id từ URL
            const urlParams = new URLSearchParams(window.location.search);
            this.baiTapId = urlParams.get('bai_tap_id');

            if (!this.baiTapId) {
                throw new Error('Không tìm thấy ID bài tập');
            }

            // Load dữ liệu
            await this.loadChiTietBaiTap();

            // Render UI
            this.renderBaiTap();

            // Thiết lập auto-save
            this.setupAutoSave();

            // Thiết lập nút nộp bài
            this.setupSubmitButton();

        } catch (error) {
            console.error('Lỗi khởi tạo trang bài tập:', error);
            ThongBao.loi('Không thể tải bài tập. Vui lòng thử lại.');
        }
    }

    /**
     * Load chi tiết bài tập từ API
     */
    async loadChiTietBaiTap() {
        try {
            const response = await fetch(`/backend/student/api/chi-tiet-bai-tap.php?bai_tap_id=${this.baiTapId}`, {
                method: 'GET',
                credentials: 'include'
            });

            const result = await response.json();

            if (!result.thanh_cong) {
                throw new Error(result.thong_bao || 'Không thể tải bài tập');
            }

            // Lưu dữ liệu
            this.chiTietBaiTap = result.du_lieu.bai_tap;
            this.danhSachCauHoi = result.du_lieu.cau_hoi;
            this.baiLamId = result.du_lieu.bai_lam?.id || null;
            this.baiLamTrangThai = result.du_lieu.bai_lam?.trang_thai || 'chua_lam';

            // Kiểm tra nếu đã nộp bài
            if (this.baiLamTrangThai === 'da_nop' || this.baiLamTrangThai === 'da_cham') {
                // Chuyển đến trang Chốt bài tập
                const urlParams = new URLSearchParams(window.location.search);
                const lopHocId = urlParams.get('lop_hoc_id');
                const chuongId = urlParams.get('chuong_id');
                
                let redirectUrl = `Chốt%20bài%20tập.html?bai_tap_id=${this.baiTapId}`;
                if (lopHocId) redirectUrl += `&lop_hoc_id=${lopHocId}`;
                if (chuongId) redirectUrl += `&chuong_id=${chuongId}`;
                
                window.location.href = redirectUrl;
                return;
            }

            // Chuyển đổi trả lời đã lưu thành map (từ cau_hoi)
            this.danhSachCauHoi.forEach(cauHoi => {
                if (cauHoi.tra_loi_da_luu) {
                    this.traLoiDaLuu[cauHoi.id] = cauHoi.tra_loi_da_luu;
                }
            });

        } catch (error) {
            console.error('Lỗi load chi tiết bài tập:', error);
            throw error;
        }
    }

    /**
     * Render thông tin bài tập và câu hỏi
     */
    renderBaiTap() {
        // 1. Render tiêu đề bài tập
        const titleElement = document.querySelector('.t-n-b-i-t-p');
        if (titleElement) {
            titleElement.textContent = this.chiTietBaiTap.tieu_de;
        }

        // 2. Render hạn nộp (đã có trong HTML, chỉ cần update)
        const deadlineElement = document.querySelector('.h-n-n-p-23-10-2025');
        if (deadlineElement) {
            const hanNop = new Date(this.chiTietBaiTap.han_nop);
            const formattedDate = hanNop.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const formattedTime = hanNop.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });
            deadlineElement.textContent = `Hạn nộp: ${formattedDate} ${formattedTime}`;
        }

        // 3. Render danh sách câu hỏi
        this.renderDanhSachCauHoi();
    }

    /**
     * Render danh sách câu hỏi
     */
    renderDanhSachCauHoi() {
        const container = document.querySelector('.frame-1321316825');
        if (!container) return;

        // Xóa nội dung cũ (giữ nút nộp bài)
        const submitButton = container.querySelector('.frame-1321316835');
        container.innerHTML = '';

        // Render từng câu hỏi
        this.danhSachCauHoi.forEach((cauHoi, index) => {
            const cauHoiElement = this.createCauHoiElement(cauHoi, index + 1);
            container.appendChild(cauHoiElement);
        });

        // Thêm lại nút nộp bài
        if (submitButton) {
            container.appendChild(submitButton);
        }
    }

    /**
     * Tạo element cho một câu hỏi
     */
    createCauHoiElement(cauHoi, stt) {
        const wrapper = document.createElement('div');
        wrapper.className = 'cau-hoi-wrapper';
        wrapper.style.marginBottom = '30px';

        // Nội dung câu hỏi
        const questionDiv = document.createElement('div');
        questionDiv.className = 'frame-1321316824';
        questionDiv.innerHTML = `
            <div class="n-i-dung-c-u-h-i-sample-questiion-1-about-something">
                Câu ${stt}: ${cauHoi.noi_dung_cau_hoi}
            </div>
        `;

        // Textarea trả lời
        const answerDiv = document.createElement('div');
        answerDiv.className = 'frame-1321316823';
        
        const textarea = document.createElement('textarea');
        textarea.id = `cau-hoi-${cauHoi.id}`;
        textarea.className = 'textarea-tra-loi';
        textarea.placeholder = 'Nhập câu trả lời của bạn...';
        textarea.rows = 6;
        textarea.style.width = '100%';
        textarea.style.padding = '15px';
        textarea.style.fontSize = '16px';
        textarea.style.fontFamily = 'Be Vietnam Pro';
        textarea.style.borderRadius = '8px';
        textarea.style.border = '1px solid #ddd';
        textarea.style.resize = 'vertical';

        // Điền trả lời đã lưu (nếu có)
        if (this.traLoiDaLuu[cauHoi.id]) {
            textarea.value = this.traLoiDaLuu[cauHoi.id];
        }

        // Lắng nghe thay đổi để auto-save
        textarea.addEventListener('input', () => {
            this.hasUnsavedChanges = true;
            this.triggerAutoSave();
        });

        answerDiv.appendChild(textarea);

        wrapper.appendChild(questionDiv);
        wrapper.appendChild(answerDiv);

        return wrapper;
    }

    /**
     * Thiết lập auto-save (lưu sau 2 giây không có thay đổi)
     */
    setupAutoSave() {
        // Auto-save khi rời trang
        window.addEventListener('beforeunload', (e) => {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = '';
                this.saveTraLoi(false); // Silent save
            }
        });
    }

    /**
     * Trigger auto-save sau khoảng delay
     */
    triggerAutoSave() {
        // Hủy timer cũ
        if (this.autoSaveTimer) {
            clearTimeout(this.autoSaveTimer);
        }

        // Đặt timer mới (2 giây)
        this.autoSaveTimer = setTimeout(() => {
            this.saveTraLoi(true); // Show notification
        }, 2000);
    }

    /**
     * Lưu trả lời bài tập
     */
    async saveTraLoi(showNotification = false) {
        if (!this.hasUnsavedChanges) return;

        try {
            // Thu thập tất cả trả lời
            const traLoiList = [];

            this.danhSachCauHoi.forEach(cauHoi => {
                const textarea = document.getElementById(`cau-hoi-${cauHoi.id}`);
                if (textarea && textarea.value.trim() !== '') {
                    traLoiList.push({
                        cau_hoi_id: cauHoi.id,
                        noi_dung_tra_loi: textarea.value.trim()
                    });
                }
            });

            // Gửi request
            const response = await fetch('/backend/student/api/luu-tra-loi-bai-tap.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bai_tap_id: this.baiTapId,
                    tra_loi: traLoiList
                })
            });

            const result = await response.json();

            if (!result.thanh_cong) {
                throw new Error(result.thong_bao || 'Không thể lưu trả lời');
            }

            // Cập nhật state
            this.hasUnsavedChanges = false;

            if (showNotification) {
                ThongBao.thanh_cong('Đã lưu tự động', { thoi_gian_hien_thi: 2000 });
            }

        } catch (error) {
            console.error('Lỗi lưu trả lời:', error);
            if (showNotification) {
                ThongBao.loi('Lỗi khi lưu', { thoi_gian_hien_thi: 2000 });
            }
        }
    }

    /**
     * Thiết lập nút nộp bài
     */
    setupSubmitButton() {
        const submitBtn = document.querySelector('.n-p-b-i');
        if (!submitBtn) return;

        // Xóa onclick cũ
        submitBtn.removeAttribute('onclick');

        submitBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await this.nopBaiTap();
        });
    }

    /**
     * Nộp bài tập
     */
    async nopBaiTap() {
        // Xác nhận trước khi nộp
        if (!confirm('Bạn có chắc chắn muốn nộp bài? Sau khi nộp bạn sẽ không thể chỉnh sửa.')) {
            return;
        }

        try {
            // Lưu trước khi nộp
            if (this.hasUnsavedChanges) {
                await this.saveTraLoi(false);
            }

            // Gửi request nộp bài
            const response = await fetch('/backend/student/api/nop-bai-tap.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bai_tap_id: this.baiTapId
                })
            });

            const result = await response.json();

            if (!result.thanh_cong) {
                throw new Error(result.thong_bao || 'Không thể nộp bài');
            }

            // Hiển thị kết quả
            const thongKe = result.du_lieu;
            ThongBao.thanh_cong(
                `Nộp bài thành công! Số câu đã trả lời: ${thongKe.so_cau_tra_loi}/${thongKe.tong_so_cau}`,
                { thoi_gian_hien_thi: 2000 }
            );

            // Redirect đến trang Chốt bài tập sau 2 giây
            setTimeout(() => {
                const urlParams = new URLSearchParams(window.location.search);
                const lopHocId = urlParams.get('lop_hoc_id');
                const chuongId = urlParams.get('chuong_id');
                
                let redirectUrl = `Chốt%20bài%20tập.html?bai_tap_id=${this.baiTapId}`;
                if (lopHocId) redirectUrl += `&lop_hoc_id=${lopHocId}`;
                if (chuongId) redirectUrl += `&chuong_id=${chuongId}`;
                
                window.location.href = redirectUrl;
            }, 2000);

        } catch (error) {
            console.error('Lỗi nộp bài:', error);
            ThongBao.loi(error.message || 'Không thể nộp bài. Vui lòng thử lại.');
        }
    }

}

// Khởi tạo khi DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    const manager = new BaiTapManager();
    manager.init();
});

export default BaiTapManager;
