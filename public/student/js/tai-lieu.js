// Tài liệu page functionality

// Lấy lop_hoc_id từ URL parameters
function getLopHocIdFromURL() {
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get('lop_hoc_id');
  console.log('URL:', window.location.href);
  console.log('lop_hoc_id từ URL:', id);
  return id;
}

const lopHocId = getLopHocIdFromURL();

// Hàm lấy extension file từ tên file
function getFileExtension(filename) {
  if (!filename) return 'FILE';
  const ext = filename.split('.').pop().toUpperCase();
  return ext || 'FILE';
}

// Hàm render danh sách tài liệu
function renderTaiLieu(danhSachTaiLieu) {
  const documentsList = document.querySelector('.documents-list');
  
  if (!danhSachTaiLieu || danhSachTaiLieu.length === 0) {
    documentsList.innerHTML = '<div class="empty-message">Chưa có tài liệu nào</div>';
    return;
  }
  
  documentsList.innerHTML = danhSachTaiLieu.map(taiLieu => {
    const loaiFile = getFileExtension(taiLieu.ten_file_goc);
    const docTypeClass = loaiFile.toLowerCase();
    
    return `
      <div class="document-item" data-id="${taiLieu.id}">
        <div class="doc-type ${docTypeClass}">${loaiFile}</div>
        <div class="doc-info">
          <div class="doc-name">${taiLieu.ten_tai_lieu || taiLieu.ten_file_goc}</div>
          <button class="download-btn" data-url="${taiLieu.duong_dan_file}" data-name="${taiLieu.ten_file_goc}">
            <img src="./assets/download.svg" alt="Tải xuống">
          </button>
        </div>
      </div>
    `;
  }).join('');
  
  // Thêm event listeners cho nút download
  document.querySelectorAll('.download-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const url = btn.getAttribute('data-url');
      const name = btn.getAttribute('data-name');
      downloadFile(url, name);
    });
  });
}

// Hàm tải xuống file
function downloadFile(url, filename) {
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  link.target = '_blank';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Hàm gọi API lấy danh sách tài liệu
async function layDanhSachTaiLieu() {
  try {
    if (!lopHocId) {
      console.error('Không tìm thấy lop_hoc_id trong URL');
      document.querySelector('.documents-list').innerHTML = 
        '<div class="error-message">Thiếu thông tin lớp học. Vui lòng quay lại trang chủ và chọn lớp học.</div>';
      return;
    }
    
    const response = await fetch(`/backend/student/api/danh-sach-tai-lieu.php?lop_hoc_id=${lopHocId}`, {
      method: 'GET',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    const data = await response.json();
    
    if (data.status === 'success') {
      renderTaiLieu(data.data);
    } else {
      console.error('Lỗi:', data.message);
      document.querySelector('.documents-list').innerHTML = 
        `<div class="error-message">Lỗi: ${data.message}</div>`;
    }
  } catch (error) {
    console.error('Lỗi khi gọi API:', error);
    document.querySelector('.documents-list').innerHTML = 
      '<div class="error-message">Không thể tải danh sách tài liệu</div>';
  }
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', () => {
  layDanhSachTaiLieu();
});
