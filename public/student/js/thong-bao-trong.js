/**
 * File: thong-bao-trong.js
 * Mục đích: Quản lý hiển thị thông báo trong (hoạt động) của sinh viên
 */

const API_BASE_URL = 'http://localhost:3000/backend/student/api';

// Hàm khởi tạo trang
async function khoiTaoTrangThongBaoTrong() {
    try {
        await taiThongBaoTrong();
    } catch (error) {
        console.error('Lỗi khởi tạo trang thông báo trong:', error);
        hienThiThongBaoLoi('Không thể tải thông báo. Vui lòng thử lại sau.');
    }
}

// Hàm tải thông báo trong từ API
async function taiThongBaoTrong() {
    try {
        const response = await fetch(`${API_BASE_URL}/thong-bao-trong.php`, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const ketQua = await response.json();

        if (ketQua.thanh_cong && ketQua.du_lieu) {
            hienThiThongBao(ketQua.du_lieu.thong_bao_list);
        } else {
            hienThiThongBaoLoi(ketQua.thong_bao || 'Không thể tải thông báo');
        }
    } catch (error) {
        console.error('Lỗi tải thông báo:', error);
        hienThiThongBaoLoi('Lỗi kết nối đến server');
    }
}

// Hàm hiển thị danh sách thông báo
function hienThiThongBao(danhSachThongBao) {
    const container = document.getElementById('tab-thong-bao');
    
    if (!container) {
        console.error('Không tìm thấy container #tab-thong-bao');
        return;
    }

    // Xóa nội dung cũ
    container.innerHTML = '';

    if (!danhSachThongBao || danhSachThongBao.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #979797;">
                <p style="font-size: 18px;">Không có thông báo nào</p>
            </div>
        `;
        return;
    }

    // Hiển thị từng thông báo
    danhSachThongBao.forEach(thongBao => {
        const notificationItem = taoThongBaoHTML(thongBao);
        container.appendChild(notificationItem);
    });
}

// Hàm tạo HTML cho một thông báo
function taoThongBaoHTML(thongBao) {
    const div = document.createElement('div');
    div.className = 'notification-item';
    
    // Xác định icon dựa trên loại thông báo
    const icon = layIconThongBao(thongBao);
    
    // Tạo nội dung chính
    const { tieuDe, moTa } = layNoiDungThongBao(thongBao);
    
    div.innerHTML = `
        <div class="notification-icon">
            <img src="./assets/${icon}" alt="Thông báo" />
        </div>
        <div class="notification-content">
            <div class="notification-text">
                <h4>${tieuDe}</h4>
                <p>${moTa}</p>
            </div>
            <div class="notification-time">
                <div class="time">${thongBao.thoi_gian || '00:00'}</div>
                <div class="date">${thongBao.ngay || ''}</div>
            </div>
        </div>
    `;
    
    return div;
}

// Hàm xác định icon dựa trên loại thông báo
function layIconThongBao(thongBao) {
    switch (thongBao.loai) {
        case 'bai_tap':
            return 'exercise0.svg';
        case 'bai_kiem_tra':
            return 'test0.svg';
        case 'thong_bao':
            return 'exercise0.svg'; // Hoặc icon thông báo khác
        default:
            return 'exercise0.svg';
    }
}

// Hàm tạo nội dung thông báo
function layNoiDungThongBao(thongBao) {
    let tieuDe = '';
    let moTa = '';
    
    if (thongBao.loai === 'bai_tap') {
        tieuDe = `Bài tập ${thongBao.tieu_de}`;
        
        if (thongBao.trang_thai === 'chua_nop') {
            const soNgay = parseInt(thongBao.con_bao_nhieu_ngay);
            if (soNgay === 0) {
                moTa = 'Hết hạn hôm nay!';
            } else if (soNgay === 1) {
                moTa = 'Còn 1 ngày nữa!';
            } else {
                moTa = `Còn ${soNgay} ngày nữa!`;
            }
        } else if (thongBao.trang_thai === 'dang_lam') {
            moTa = 'Đang làm - Hãy hoàn thành và nộp bài';
        }
        
    } else if (thongBao.loai === 'bai_kiem_tra') {
        tieuDe = `Bài kiểm tra ${thongBao.tieu_de}`;
        
        const soNgay = parseInt(thongBao.con_bao_nhieu_ngay);
        if (soNgay === 0) {
            moTa = 'Bắt đầu hôm nay!';
        } else if (soNgay === 1) {
            moTa = 'Bắt đầu sau 1 ngày!';
        } else {
            moTa = `Bắt đầu sau ${soNgay} ngày!`;
        }
        
    } else if (thongBao.loai === 'thong_bao') {
        tieuDe = thongBao.tieu_de;
        moTa = `${thongBao.ten_mon_hoc}`;
    }
    
    return { tieuDe, moTa };
}

// Hàm hiển thị thông báo lỗi
function hienThiThongBaoLoi(thongBao) {
    const container = document.getElementById('tab-thong-bao');
    
    if (!container) {
        console.error('Không tìm thấy container #tab-thong-bao');
        return;
    }

    container.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #ff0000;">
            <p style="font-size: 18px;">${thongBao}</p>
        </div>
    `;
}

// Khởi động khi trang được load
document.addEventListener('DOMContentLoaded', function() {
    khoiTaoTrangThongBaoTrong();
});
