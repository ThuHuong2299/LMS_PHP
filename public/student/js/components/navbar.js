/**
 * File: navbar.js
 * Mục đích: Quản lý navbar component - tải user info từ API
 * - Load navbar template
 * - Cập nhật user info động từ API
 */

const API_NAVBAR = '/backend/student/api/trang-chu-dashboard.php';

class NavbarManager {
    constructor() {
        this.containerSelector = '#navbar-container';
        this.templatePath = '/public/student/components/navbar.html';
    }

    /**
     * Khởi tạo navbar
     */
    async init() {
        try {
            // Load template HTML
            await this.loadTemplate();
            
            // Set tiêu đề trang
            this.setPageTitle();
            
            // Load user data từ API
            await this.loadUserData();
        } catch (error) {
            console.error('Lỗi khi khởi tạo navbar:', error);
        }
    }

    /**
     * Set tiêu đề trang dựa trên URL hoặc document.title
     */
    setPageTitle() {
        const pageTitleElement = document.querySelector('#page-title');
        if (pageTitleElement) {
            // Lấy tiêu đề từ document.title
            let pageTitle = document.title;
            
            // Nếu document.title không phù hợp, extract từ URL
            if (pageTitle === 'Document' || pageTitle === '') {
                const fileName = window.location.pathname.split('/').pop().replace('.html', '');
                pageTitle = this.formatPageTitle(fileName);
            }
            
            pageTitleElement.textContent = pageTitle;
        }
    }

    /**
     * Format tên file thành tiêu đề dễ đọc
     * Ví dụ: "Thông%20báo.html" -> "Thông báo"
     */
    formatPageTitle(fileName) {
        if (!fileName) return 'Trang chủ';
        
        // Map file names tới tiêu đề
        const titleMap = {
            'Trang Chủ': 'Trang chủ',
            'Bài kiểm tra': 'Bài kiểm tra',
            'Bài tập': 'Bài tập',
            'Bảng điểm': 'Bảng điểm',
            'Chốt bài tập': 'Chốt bài tập',
            'Tài liệu': 'Tài liệu',
            'Thông báo': 'Thông báo',
            'Thông báo trong': 'Thông báo',
            'Thông tin bài giảng': 'Nội dung môn học',
            'Thông tin bài học': 'Thông tin bài giảng',
            'Thông tin môn học': 'Thông tin môn học'
        };
        
        // Tìm tiêu đề match
        for (const [key, title] of Object.entries(titleMap)) {
            if (fileName.includes(decodeURIComponent(key))) {
                return title;
            }
        }
        
        // Mặc định: return document.title
        return document.title || 'Trang chủ';
    }

    /**
     * Load navbar template
     */
    async loadTemplate() {
        try {
            const response = await fetch(this.templatePath);
            const html = await response.text();
            const container = document.querySelector(this.containerSelector);
            
            if (container) {
                container.innerHTML = html;
            }
        } catch (error) {
            console.error('Lỗi khi load navbar template:', error);
        }
    }

    /**
     * Load user info từ API và cập nhật navbar
     */
    async loadUserData() {
        try {
            const response = await fetch(API_NAVBAR, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.thanh_cong && data.du_lieu.thong_tin_sinh_vien) {
                this.renderThongTinSinhVien(data.du_lieu.thong_tin_sinh_vien);
            }
        } catch (error) {
            console.error('Lỗi khi load user data:', error);
        }
    }

    /**
     * Cập nhật thông tin sinh viên vào navbar
     */
    renderThongTinSinhVien(thongTin) {
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
}

// Khởi tạo navbar khi page load
document.addEventListener('DOMContentLoaded', function() {
    const navbar = new NavbarManager();
    navbar.init();
});
