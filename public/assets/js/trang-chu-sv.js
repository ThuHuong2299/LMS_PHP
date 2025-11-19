/**
 * File: trang-chu-sv.js
 * Mục đích: Xử lý API trang chủ sinh viên
 * - Gọi API lấy dữ liệu từ backend
 * - Render dữ liệu vào HTML
 */

// URL API - Sử dụng path tuyệt đối
const API_DASHBOARD = '/backend/student/api/trang-chu-dashboard.php';

/**
 * Khởi tạo trang chủ
 */
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

/**
 * Lấy dữ liệu từ API
 */
function loadDashboardData() {
    fetch(API_DASHBOARD, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.thanh_cong) {
            renderDashboard(data.du_lieu);
        } else {
            showError(data.thong_bao);
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showError('Không thể tải dữ liệu trang chủ');
    });
}

/**
 * Render dữ liệu vào trang
 */
function renderDashboard(data) {
    // Render thông tin sinh viên
    renderThongTinSinhVien(data.thong_tin_sinh_vien);
    
    // Render danh sách lớp học
    renderDanhSachLopHoc(data.danh_sach_lop_hoc);
    
    // Render nhắc nhở
    renderNhacNho(data.nhac_nho_san_co);
}

/**
 * Render thông tin sinh viên
 */
function renderThongTinSinhVien(thongTin) {
    // Cập nhật tên sinh viên
    const userDetailsElement = document.querySelector('.user-details h3');
    if (userDetailsElement) {
        userDetailsElement.textContent = thongTin.ho_ten;
    }
    
    // Cập nhật email (hoặc mã sinh viên)
    const userEmailElement = document.querySelector('.user-details p');
    if (userEmailElement) {
        userEmailElement.textContent = thongTin.ma_nguoi_dung;
    }
    
    // Cập nhật avatar
    if (thongTin.anh_dai_dien) {
        const avatarElement = document.querySelector('.user-avatar img');
        if (avatarElement) {
            avatarElement.src = thongTin.anh_dai_dien;
            avatarElement.alt = thongTin.ho_ten;
        }
    }
}

/**
 * Render danh sách lớp học
 */
function renderDanhSachLopHoc(lopHocList) {
    const container = document.querySelector('.frame-1000001983');
    
    // Xóa dữ liệu cũ (giữ lại template)
    const existingCards = container.querySelectorAll('.course-card');
    existingCards.forEach(card => card.remove());
    
    // Render từng lớp
    lopHocList.forEach(lop => {
        const card = createCourseCard(lop);
        container.appendChild(card);
    });
}

/**
 * Tạo card lớp học
 */
function createCourseCard(lop) {
    const phanTram = Math.round(lop.phan_tram_hoan_thanh);
    
    const card = document.createElement('div');
    card.className = 'course-card';
    card.onclick = function() {
        // Có thể điều hướng đến trang chi tiết lớp học
        window.location.href = `../Thông%20tin%20bài%20giảng.html?lop_id=${lop.lop_hoc_id}`;
    };
    
    card.innerHTML = `
        <div class="rectangle-3255"></div>
        <div class="frame-1000001977">
            <div>
                <div class="course-name">${escapeHtml(lop.ten_mon_hoc)}</div>
                <div class="mr-mrs-lecture-name">${escapeHtml(lop.ten_giang_vien)}</div>
            </div>
            <div class="frame-1000001975">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${phanTram}%"></div>
                </div>
                <div class="frame-1000001980">
                    <span>${phanTram}% Hoàn tất</span>
                </div>
            </div>
            <div class="frame-1000001974">
                <span>Kết thúc vào: ${formatDate(lop.ngay_ket_thuc || 'Đang diễn ra')}</span>
            </div>
        </div>
    `;
    
    return card;
}

/**
 * Render nhắc nhở
 */
function renderNhacNho(nhacNhoList) {
    const container = document.querySelector('.frame-1000001982');
    
    // Xóa dữ liệu cũ (giữ lại template)
    const existingCards = container.querySelectorAll('.notification-card');
    existingCards.forEach(card => card.remove());
    
    // Render từng nhắc nhở (max 3)
    nhacNhoList.slice(0, 3).forEach(nhacNho => {
        const card = createNotificationCard(nhacNho);
        container.appendChild(card);
    });
}

/**
 * Tạo card nhắc nhở
 */
function createNotificationCard(nhacNho) {
    const card = document.createElement('div');
    card.className = 'notification-card';
    
    // Xác định icon dựa trên loại
    let icon = getNotificationIcon(nhacNho.loai);
    
    // Tính thời gian còn lại
    let thongTinThoiGian = '';
    if (nhacNho.loai === 'thong_bao') {
        thongTinThoiGian = 'Mới đây';
    } else {
        const conBaoNhieu = nhacNho.con_bao_nhieu_ngay;
        if (conBaoNhieu <= 0) {
            thongTinThoiGian = 'Hôm nay hết hạn!';
        } else if (conBaoNhieu === 1) {
            thongTinThoiGian = 'Còn 1 ngày nữa!';
        } else {
            thongTinThoiGian = `Còn ${conBaoNhieu} ngày nữa!`;
        }
    }
    
    card.innerHTML = `
        ${icon}
        <div class="frame-1000001981">
            <div class="content">${escapeHtml(nhacNho.ten_mon_hoc)}</div>
            <div class="content2">${escapeHtml(nhacNho.tieu_de)}</div>
            <div class="content3">${thongTinThoiGian}</div>
        </div>
    `;
    
    // Thêm sự kiện click
    card.style.cursor = 'pointer';
    card.onclick = function() {
        handleNotificationClick(nhacNho);
    };
    
    return card;
}

/**
 * Xử lý click notification
 */
function handleNotificationClick(nhacNho) {
    if (nhacNho.loai === 'bai_tap') {
        window.location.href = `../Bài%20tập.html?bai_tap_id=${nhacNho.bai_id}`;
    } else if (nhacNho.loai === 'bai_kiem_tra') {
        window.location.href = `../Bài%20kiểm%20tra.html?bai_kiem_tra_id=${nhacNho.bai_id}`;
    } else if (nhacNho.loai === 'thong_bao') {
        window.location.href = `../Thông%20báo.html?thong_bao_id=${nhacNho.bai_id}`;
    }
}

/**
 * Lấy icon cho notification
 */
function getNotificationIcon(loai) {
    const svgBase = 'width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"';
    
    if (loai === 'bai_tap') {
        return `<svg ${svgBase}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`;
    } else if (loai === 'bai_kiem_tra') {
        return `<svg ${svgBase}><path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"></path><path d="M12 6v6l4 2"></path></svg>`;
    } else {
        return `<svg ${svgBase}><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`;
    }
}

/**
 * Format ngày tháng
 */
function formatDate(dateStr) {
    if (dateStr === 'Đang diễn ra') {
        return dateStr;
    }
    
    try {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    } catch (e) {
        return dateStr;
    }
}

/**
 * Escape HTML để tránh XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Hiển thị lỗi
 */
function showError(message) {
    alert('Lỗi: ' + message);
    console.error('Lỗi:', message);
}
