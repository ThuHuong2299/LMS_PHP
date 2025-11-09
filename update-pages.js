/**
 * SCRIPT TỰ ĐỘNG CẬP NHẬT TẤT CẢ CÁC TRANG
 * Chạy script này để áp dụng sidebar mới cho tất cả trang
 */

const fs = require('fs');
const path = require('path');

// Danh sách các file cần update
const files = [
  'ClassroomInfo.html',
  'Notification.html',
  'WorkDashBoard.html',
  'HomeWork.html',
  'HomeWorkInfo.html',
  'ChamBai.html'
];

const basePath = 'c:/Users/Admin/Desktop/project-giang-day-web/project-giang-day-web/public/teacher';

// Template head mới
const newHeadTemplate = `  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600;700&family=Quicksand:wght@400;500;600;700&family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">
  <base href="./assets/">
  
  <link rel="stylesheet" href="/public/teacher/CSS/components/shared.css">
  <link rel="stylesheet" href="/public/teacher/CSS/components/sidebar.css">`;

// Thay thế sidebar container
const sidebarReplacement = `  <div id="sidebar-container"></div>`;

// Script imports
const scriptImports = `
<script type="module" src="/public/teacher/js/main.js"></script>
</body>
</html>`;

files.forEach(fileName => {
  const filePath = path.join(basePath, fileName);
  
  try {
    let content = fs.readFileSync(filePath, 'utf8');
    
    // 1. Xóa sidebar cũ
    content = content.replace(
      /<div class="side-bar"[^>]*>[\s\S]*?<\/div>\s*<\/div>/,
      sidebarReplacement
    );
    
    // 2. Xóa script logout cũ
    content = content.replace(
      /<script>\s*function logout\(\)[\s\S]*?<\/script>/,
      ''
    );
    
    // 3. Thêm main.js nếu chưa có
    if (!content.includes('main.js')) {
      content = content.replace(
        /<\/body>\s*<\/html>/,
        scriptImports
      );
    }
    
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✅ Updated: ${fileName}`);
    
  } catch (error) {
    console.error(`❌ Error updating ${fileName}:`, error.message);
  }
});

console.log('\n✅ Hoàn thành!');
