// Thông tin bài học functionality

function toggleChapter(element) {
  const lessonList = element.nextElementSibling;
  const arrow = element.querySelector('.arrow-icon');
  
  if (lessonList.classList.contains('active')) {
    lessonList.classList.remove('active');
    arrow.style.transform = 'rotate(-90deg)';
  } else {
    lessonList.classList.add('active');
    arrow.style.transform = 'rotate(0deg)';
  }
}

function toggleReplies(button, repliesId) {
  const repliesContainer = document.getElementById(repliesId);
  
  if (button.classList.contains('collapsed')) {
    // Hiện phản hồi
    button.classList.remove('collapsed');
    button.classList.add('active');
    button.innerHTML = 'Ẩn phản hồi ▲';
    repliesContainer.classList.remove('hidden');
  } else {
    // Ẩn phản hồi
    button.classList.add('collapsed');
    button.classList.remove('active');
    const replyCount = repliesContainer.querySelectorAll('.comment').length;
    button.innerHTML = `${replyCount} phản hồi ▼`;
    repliesContainer.classList.add('hidden');
  }
}

// Add hover effect to lesson items
document.querySelectorAll('.lesson-item').forEach(item => {
  item.addEventListener('click', function() {
    document.querySelectorAll('.lesson-item').forEach(i => i.classList.remove('active'));
    this.classList.add('active');
  });
});
