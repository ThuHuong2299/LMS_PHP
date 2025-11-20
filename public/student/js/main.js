import SidebarManager from './components/sidebar.js';

class App {
  async initialize() {
    try {
      // Load sidebar
      const sidebarManager = new SidebarManager();
      await sidebarManager.initialize();
      sidebarManager.restoreState();

      console.log('✅ App initialized successfully');
    } catch (error) {
      console.error(' Lỗi khởi tạo app:', error);
    }
  }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new App().initialize();
});

// Also run if already loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new App().initialize();
  });
} else {
  new App().initialize();
}
