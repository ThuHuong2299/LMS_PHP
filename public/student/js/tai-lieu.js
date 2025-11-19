// Tài liệu page functionality

// Add click handlers for download buttons
document.querySelectorAll('.download-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    alert('Đang tải xuống tài liệu...');
  });
});

// Add click handlers for document items
document.querySelectorAll('.doc-info').forEach(item => {
  item.addEventListener('click', () => {
    alert('Mở tài liệu...');
  });
});
