/**
 * SidebarCourseManager - Quản lý sidebar động cho trang Thông tin môn học
 * Xử lý:
 * - Load danh sách chương từ API
 * - Render chương + bài học theo 3 level (Môn → Chương → Bài)
 * - Toggle expand/collapse chương
 * - Display trạng thái tiến độ
 * - Event binding
 */

class SidebarCourseManager {
  constructor() {
    this.sidebar = null;
    this.container = null;
    this.chapters = [];
    this.expandedChapters = {};
    this.lopHocId = this.getLopHocIdFromUrl();
    this.currentPage = this.getCurrentPageInfo();
    this.isChaptersVisible = false; // Trạng thái hiển thị danh sách chương
  }

  /**
   * Xác định trang hiện tại để highlight tab
   */
  getCurrentPageInfo() {
    const path = window.location.pathname;
    const params = new URLSearchParams(window.location.search);
    const chuongId = params.get('chuong_id');
    
    // Xác định trang hiện tại
    let currentTab = null;
    if (path.includes('Thông%20tin%20bài%20giảng') || path.includes('Thông tin bài giảng') || path.includes('Th%C3%B4ng%20tin%20b%C3%A0i%20gi%E1%BA%A3ng')) {
      currentTab = 'chuong';
    } else if (path.includes('Bảng%20điểm') || path.includes('Bảng điểm') || path.includes('B%E1%BA%A3ng%20%C4%91i%E1%BB%83m')) {
      currentTab = 'diem';
    } else if (path.includes('Tài%20liệu') || path.includes('Tài liệu') || path.includes('T%C3%A0i%20li%E1%BB%87u') || path.includes('T%C3%A0i%2520li%E1%BB%87u')) {
      currentTab = 'tai-lieu';
    } else if (path.includes('Thông%20báo%20trong') || path.includes('Thông báo trong') || path.includes('Th%C3%B4ng%20b%C3%A1o%20trong') || path.includes('Th%C3%B4ng%2520b%C3%A1o%2520trong')) {
      currentTab = 'thong-bao';
    }
    
    console.log('Current page info:', { path, chuongId, currentTab });
    
    return {
      path: path,
      chuongId: chuongId,
      currentTab: currentTab,
      isChapterPage: currentTab === 'chuong'
    };
  }

  /**
   * Lấy lop_hoc_id từ URL query params
   */
  getLopHocIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('lop_hoc_id') || 1; // Default to 1 nếu không có
  }

  /**
   * Khởi tạo sidebar
   */
  async initialize() {
    try {
      // 1. Fetch template
      const container = document.getElementById('sidebar-course-container');
      if (!container) {
        console.error(' sidebar-course-container không tìm thấy');
        return;
      }

      const response = await fetch('/public/student/components/sidebar-course.html');
      if (!response.ok) {
        throw new Error(`Fetch template failed: ${response.status}`);
      }

      container.innerHTML = await response.text();
      this.sidebar = document.getElementById('sidebar');
      this.container = container;

      // 2. Load chapters data từ API
      await this.loadChapters();

      // 3. Bind events
      this.bindEvents();

      // 4. Bind logo click event
      this.bindLogoClick();

      // 5. Restore state từ localStorage
      this.restoreState();

      console.log('✅ Sidebar Course loaded');
    } catch (error) {
      console.error(' Lỗi load sidebar course:', error);
    }
  }

  /**
   * Load danh sách chương từ API
   */
  async loadChapters() {
    try {
      const apiUrl = `/backend/student/api/danh-sach-chuong.php?lop_hoc_id=${this.lopHocId}`;
      
      const response = await fetch(apiUrl, {
        method: 'GET',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error(`API returned ${response.status}`);
      }

      const data = await response.json();

      if (data.thanh_cong === true && Array.isArray(data.du_lieu)) {
        this.chapters = data.du_lieu;
        this.renderChapters();
      } else {
        console.warn(' API trả về dữ liệu không hợp lệ:', data);
        this.renderEmptyChapters();
      }
    } catch (error) {
      console.error(' Lỗi load chapters:', error);
      this.renderEmptyChapters();
    }
  }

  /**
   * Render chương vào sidebar
   */
  renderChapters() {
    const menu = this.sidebar.querySelector('#courseMenu');
    if (!menu) return;

    menu.innerHTML = '';

    // Main tab: Môn Học
    const mainTab = document.createElement('div');
    mainTab.className = 'tab-side-bar11';
    mainTab.id = 'tab-mon-hoc';
    
    const monHocText = document.createElement('div');
    monHocText.className = 'l-p-h-c';
    monHocText.textContent = 'Nội dung';
    monHocText.style.cssText = 'flex: 1; cursor: pointer;';
    
    // Thêm mũi tên để toggle chapters
    const arrow = document.createElement('span');
    arrow.className = 'arrow toggle-arrow';
    arrow.textContent = '▼';
    arrow.style.cssText = 'font-size: 10px; transition: transform 0.3s; cursor: pointer; padding: 5px;';
    arrow.title = 'Hiện/Ẩn danh sách chương';
    
    mainTab.appendChild(monHocText);
    mainTab.appendChild(arrow);

    // Click vào text -> navigate đến chương đầu tiên
    monHocText.addEventListener('click', (e) => {
      e.stopPropagation();
      if (this.chapters.length > 0) {
        const chuongDauTien = this.chapters[0];
        window.location.href = `/public/student/Thông%20tin%20bài%20giảng.html?lop_hoc_id=${this.lopHocId}&chuong_id=${chuongDauTien.id}`;
      }
    });
    
    // Click vào arrow -> toggle danh sách chương
    arrow.addEventListener('click', (e) => {
      e.stopPropagation();
      this.toggleChaptersList();
    });

    menu.appendChild(mainTab);

    // Chapters list container
    const chaptersList = document.createElement('div');
    chaptersList.id = 'chaptersList';
    // Mặc định ẩn danh sách chương, chỉ hiện khi đang ở trang chương hoặc click vào Môn Học
    const displayStyle = this.currentPage.currentTab === 'chuong' ? 'flex' : 'none';
    chaptersList.style.cssText = `display: ${displayStyle}; flex-direction: column; gap: 0;`;
    
    if (displayStyle === 'flex') {
      this.isChaptersVisible = true;
    }

    // Render từng chương
    this.chapters.forEach((chapter) => {
      const chapterEl = this.createChapterElement(chapter);
      chaptersList.appendChild(chapterEl);
    });

    menu.appendChild(chaptersList);

    // Other tabs (static)
    const otherTabs = [
      { id: 'diem', text: 'Điểm', href: `/public/student/Bảng%20điểm.html?lop_hoc_id=${this.lopHocId}` },
      { id: 'tai-lieu', text: 'Tài liệu', href: `/public/student/Tài%20liệu.html?lop_hoc_id=${this.lopHocId}` },
      { id: 'thong-bao', text: 'Thông báo', href: `/public/student/Thông%20báo%20trong.html?lop_hoc_id=${this.lopHocId}` }
    ];

    otherTabs.forEach(tab => {
      const isTabActive = this.currentPage.currentTab === tab.id;
      const tabEl = document.createElement('div');
      // Chỉ set tab-side-bar2 (active) cho tab hiện tại, còn lại dùng tab-side-bar11
      tabEl.className = isTabActive ? 'tab-side-bar2' : 'tab-side-bar11';
      
      const textDiv = document.createElement('div');
      textDiv.className = 'side-text';
      textDiv.textContent = tab.text;
      
      tabEl.appendChild(textDiv);
      
      if (tab.href !== '#') {
        tabEl.onclick = () => window.location.href = tab.href;
      }
      menu.appendChild(tabEl);
    });
  }

  /**
   * Tạo element cho 1 chương
   */
  createChapterElement(chapter) {
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'display: flex; flex-direction: column; padding: 0;';
    wrapper.id = `chapter-${chapter.id}`;
    wrapper.dataset.chapterId = chapter.id;

    // Chapter Header
    const header = document.createElement('div');
    
    // Kiểm tra xem có phải chapter hiện tại không
    const isActive = this.currentPage.isChapterPage && 
                     this.currentPage.chuongId == chapter.id;

    console.log(`Chapter ${chapter.id}: isActive=${isActive}, currentChuongId=${this.currentPage.chuongId}`);
    
    // Sử dụng class từ CSS thay vì inline style
    header.className = isActive ? 'tab-side-bar11' : 'tab-side-bar11';
    
    // Thêm style cho active state
    if (isActive) {
      header.style.backgroundColor = '#e5f0ff';
    }

    // Tạo text element với class side-text
    const textSpan = document.createElement('div');
    textSpan.className = 'side-text';
    textSpan.style.cssText = 'flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
    textSpan.textContent = chapter.ten_chuong;

    // Tạo arrow
    const arrowSpan = document.createElement('span');
    arrowSpan.className = 'arrow chapter-arrow';
    arrowSpan.textContent = '▶';
    arrowSpan.style.cssText = 'font-size: 10px; transition: transform 0.3s;';

    header.appendChild(textSpan);
    header.appendChild(arrowSpan);
    
    header.addEventListener('click', () => {
      // Navigate tới trang Thông tin bài giảng với chuong_id
      window.location.href = `/public/student/Thông%20tin%20bài%20giảng.html?lop_hoc_id=${this.lopHocId}&chuong_id=${chapter.id}`;
    });

    wrapper.appendChild(header);

    // Lessons List (render nếu có bai_hoc)
    if (Array.isArray(chapter.bai_hoc) && chapter.bai_hoc.length > 0) {
      const lessonsList = this.createLessonsListElement(chapter.bai_hoc);
      wrapper.appendChild(lessonsList);
    }

    return wrapper;
  }

  /**
   * Tạo danh sách bài học
   */
  createLessonsListElement(lessons) {
    const listEl = document.createElement('div');
    listEl.style.cssText = `
      display: none;
      flex-direction: column;
      gap: 0;
      padding: 0;
    `;
    listEl.className = 'lessons-list';

    lessons.forEach(lesson => {
      const lessonEl = this.createLessonElement(lesson);
      listEl.appendChild(lessonEl);
    });

    return listEl;
  }

  /**
   * Tạo element cho 1 bài học
   */
  createLessonElement(lesson) {
    const el = document.createElement('div');
    el.className = 'tab-side-bar21';
    el.title = lesson.tieu_de;

    // Xác định loại bài
    const loaiMap = {
      'video': { text: 'Video' },
      'bai_tap': { text: 'Bài Tập' },
      'bai_kiem_tra': { text: 'Kiểm Tra' }
    };

    const loaiInfo = loaiMap[lesson.loai] || { text: 'Nội dung' };

    // Status indicator
    const tienDo = lesson.tien_do || 0;
    let statusHtml = '';
    
    if (tienDo === 100) {
      statusHtml = '<span style="font-size: 12px; color: #4caf50; flex-shrink: 0; margin-left: auto;">✓</span>';
    } else if (tienDo > 0) {
      statusHtml = '<span style="font-size: 12px; color: #ff9800; flex-shrink: 0; margin-left: auto;">○</span>';
    }

    // Sử dụng cấu trúc HTML giống TTMH
    const titleDiv = document.createElement('div');
    titleDiv.className = 'lesson-title';
    titleDiv.textContent = lesson.tieu_de;

    const metaDiv = document.createElement('div');
    metaDiv.className = 'lesson-meta';
    metaDiv.innerHTML = `
      <span>${loaiInfo.text}</span>
      ${lesson.thoi_gian_phut ? '<span class="dot"></span>' : ''}
      ${lesson.thoi_gian_phut ? `<span>${lesson.thoi_gian_phut}</span>` : ''}
    `;

    el.appendChild(titleDiv);
    el.appendChild(metaDiv);
    if (statusHtml) {
      const statusSpan = document.createElement('span');
      statusSpan.innerHTML = statusHtml;
      el.appendChild(statusSpan);
    }

    el.addEventListener('click', (e) => {
      e.stopPropagation();
      this.handleLessonClick(lesson);
    });

    return el;
  }

  /**
   * Toggle expand/collapse chương
   */
  toggleChapter(chapterId, wrapper) {
    const lessonsList = wrapper.querySelector('.lessons-list');
    if (!lessonsList) return;

    const isHidden = lessonsList.style.display === 'none';
    lessonsList.style.display = isHidden ? 'flex' : 'none';

    // Rotate arrow
    const arrow = wrapper.querySelector('.chapter-arrow');
    if (arrow) {
      arrow.style.transform = isHidden ? 'rotate(90deg)' : 'rotate(0deg)';
    }

    this.expandedChapters[chapterId] = isHidden;
    localStorage.setItem('expandedChapters', JSON.stringify(this.expandedChapters));
  }

  /**
   * Handle click vào bài học
   */
  handleLessonClick(lesson) {
    // Nếu là video, navigate đến trang Thông tin bài học
    if (lesson.loai === 'video') {
      window.location.href = `/public/student/Thông%20tin%20bài%20học.html?bai_id=${lesson.id}&lop_hoc_id=${this.lopHocId}`;
    } else if (lesson.loai === 'bai_tap') {
      window.location.href = `/public/student/Bài%20tập.html?bai_tap_id=${lesson.id}`;
    } else if (lesson.loai === 'bai_kiem_tra') {
      window.location.href = `/public/student/Bài%20kiểm%20tra.html?bai_kiem_tra_id=${lesson.id}`;
    }
  }

  /**
   * Render empty chapters (khi API fail)
   */
  renderEmptyChapters() {
    if (!this.sidebar) {
      console.warn(' Sidebar chưa được load');
      return;
    }
    const menu = this.sidebar.querySelector('#courseMenu');
    if (!menu) return;

    menu.innerHTML = '';

    // Main tab: Môn Học
    const mainTab = document.createElement('div');
    mainTab.className = 'tab-side-bar2';
    mainTab.innerHTML = `
      <div class="l-p-h-c">Môn Học</div>
    `;
    menu.appendChild(mainTab);

    // Empty message
    const emptyDiv = document.createElement('div');
    emptyDiv.style.cssText = 'padding: 20px; text-align: center; color: #999; font-size: 12px;';
    emptyDiv.textContent = 'Không có dữ liệu chương';
    menu.appendChild(emptyDiv);
  }

  /**
   * Toggle hiển thị/ẩn danh sách chương
   */
  toggleChaptersList() {
    const chaptersList = this.sidebar.querySelector('#chaptersList');
    const arrow = this.sidebar.querySelector('.toggle-arrow');
    
    if (!chaptersList) return;
    
    this.isChaptersVisible = !this.isChaptersVisible;
    chaptersList.style.display = this.isChaptersVisible ? 'flex' : 'none';
    
    // Xoay mũi tên
    if (arrow) {
      arrow.style.transform = this.isChaptersVisible ? 'rotate(180deg)' : 'rotate(0deg)';
    }
  }

  /**
   * Bind logo click để về trang chủ
   */
  bindLogoClick() {
    if (!this.sidebar) return;
    
    const logo = this.sidebar.querySelector('.logo');
    if (logo) {
      logo.style.cursor = 'pointer';
      logo.addEventListener('click', () => {
        window.location.href = '/public/student/Trang%20Chủ.html';
      });
    }
  }

  /**
   * Bind event listeners
   */
  bindEvents() {
    if (!this.sidebar) return;
    
    // Logout button
    const btnLogout = this.sidebar.querySelector('#btnLogout');
    if (btnLogout) {
      btnLogout.addEventListener('click', () => this.logout());
    }
  }

  /**
   * Restore state từ localStorage
   */
  restoreState() {
    // Restore expanded chapters
    const expandedStr = localStorage.getItem('expandedChapters');
    if (expandedStr) {
      this.expandedChapters = JSON.parse(expandedStr);
      Object.entries(this.expandedChapters).forEach(([chapterId, isExpanded]) => {
        if (isExpanded) {
          const wrapper = this.sidebar.querySelector(`#chapter-${chapterId}`);
          if (wrapper) {
            const lessonsList = wrapper.querySelector('.lessons-list');
            if (lessonsList) {
              lessonsList.style.display = 'flex';
              const arrow = wrapper.querySelector('.chapter-arrow');
              if (arrow) arrow.style.transform = 'rotate(90deg)';
            }
          }
        }
      });
    }
  }

  /**
   * Logout
   */
  logout() {
    sessionStorage.clear();
    localStorage.removeItem('userToken');
    localStorage.removeItem('userData');
    window.location.href = '/public/Login.student.html';
  }

  /**
   * Escape HTML để tránh XSS
   */
  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

export default SidebarCourseManager;
