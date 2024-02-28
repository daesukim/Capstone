document.addEventListener('DOMContentLoaded', function () {
  const content = document.querySelector('.pageContent'); 
  const itemsPerPage = 1;
  let currentPage = 0;
  const items = Array.from(content.getElementsByTagName('article'));

function showPage(page) {
  const startIndex = page * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  items.forEach((item, index) => {
    item.classList.toggle('hidden', index < startIndex || index >= endIndex);
  });
  updateActiveButtonStates();
}

function createPageButtons() {
  const totalPages = Math.ceil(items.length / itemsPerPage);
  const paginationContainer = document.getElementById('paginationContainer');
  paginationContainer.classList.add('pagination');

  // Add page buttons
  const daysOfWeek = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];
  let test = document.getElementById("startDateNum").value;
  test = parseInt(test);
  for (let i = 0; i <= 6; i++) {
    if (test > 6) { test = 0; }
    const pageButton = document.createElement('a');
    pageButton.textContent = daysOfWeek[test];
    pageButton.addEventListener('click', () => {
      currentPage = i;
      showPage(currentPage);
      updateActiveButtonStates();
    });

      paginationContainer.appendChild(pageButton);
      test = test + 1;
    }
}

function updateActiveButtonStates() {
  const pageButtons = document.querySelectorAll('.pagination a');
  pageButtons.forEach((a, index) => {
    if (index === currentPage) {
      a.classList.add('active');
    } else {
      a.classList.remove('active');
    }
  });
}

  createPageButtons(); // Call this function to create the page buttons initially
  showPage(currentPage);
});
