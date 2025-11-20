import SidebarCourseManager from './components/sidebar-course.js';

class AppCourse {
  async initialize() {
    try {
      // Load sidebar course
      const sidebarCourseManager = new SidebarCourseManager();
      await sidebarCourseManager.initialize();

      console.log('✅ Course app initialized successfully');
    } catch (error) {
      console.error(' Lỗi khởi tạo course app:', error);
    }
  }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new AppCourse().initialize();
});

// Also run if already loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new AppCourse().initialize();
  });
} else {
  new AppCourse().initialize();
}
