// Common functions used across student pages

function toggleSidebar(event) {
  // Ngăn sự kiện lan sang logo
  if (event) event.stopPropagation();

  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("collapsed");
}

function logout() {
  if (confirm("Bạn có chắc muốn đăng xuất không?")) {
    window.location.href = "../../Đăngnhập.sv.html";
  }
}

function toggleDropdown() {
  const submenu = document.getElementById("submenu");
  const arrow = document.getElementById("arrow");
  submenu.classList.toggle("open");
  arrow.classList.toggle("rotate");
}

// Initialize sidebar functionality
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const logoImg = document.querySelector(".logo img.objects");

  if (logoImg) {
    logoImg.addEventListener("click", (event) => {
      // Nếu sidebar đang thu gọn → mở rộng lại
      if (sidebar.classList.contains("collapsed")) {
        sidebar.classList.remove("collapsed");
      } else {
        // Nếu đang mở → về trang chủ
        window.location.href = "../Trang Chủ.html";
      }
    });
  }

  // Gắn lại listener cho nút thu gọn
  const toggleBtn = document.querySelector(".open-close");
  if (toggleBtn) {
    toggleBtn.addEventListener("click", toggleSidebar);
  }
});
