document.addEventListener('DOMContentLoaded', function () {
    const menuList = document.getElementById('menu-list');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const pageInfo = document.getElementById('page-info');
  
    let itemsPerPage = calculateItemsPerPage();
    let currentPage = 1;

    function calculateItemsPerPage() {
      if (window.innerWidth < 700){
        return 6;
      }
      else if (window.innerWidth < 1000){
        return 9;
      }
      else{
        return 12;
      }
    }
  
    function showMenus() {
        const menus = document.querySelectorAll('.menu');
        const startIdx = (currentPage - 1) * itemsPerPage;
        const endIdx = startIdx + itemsPerPage;
      
        for (let index = 0; index < menus.length; index++) {
          const menu = menus[index];
      
          if (index >= startIdx && index < endIdx) {
            menu.style.display = 'block';
          } else {
            menu.style.display = 'none';
          }
        }
      
        pageInfo.textContent = `${currentPage}`;
    }
  
    function updatePagination() {
      const totalMenus = document.querySelectorAll('.menu').length;
      const totalPages = Math.ceil(totalMenus / itemsPerPage);
  
      prevBtn.disabled = currentPage === 1;
      nextBtn.disabled = currentPage === totalPages;
    }
  
    function goToPrevPage() {
      if (currentPage > 1) {
        currentPage--;
        showMenus();
        updatePagination();
      }
    }
  
    function goToNextPage() {
      const totalMenus = document.querySelectorAll('.menu').length;
      const totalPages = Math.ceil(totalMenus / itemsPerPage);
  
      if (currentPage < totalPages) {
        currentPage++;
        showMenus();
        updatePagination();
      }
    }

    window.addEventListener('resize', function () {
      itemsPerPage = calculateItemsPerPage();
      location.reload();
    });
  
    // Initial setup
    showMenus();
    updatePagination();
  
    // Event listeners
    prevBtn.addEventListener('click', goToPrevPage);
    nextBtn.addEventListener('click', goToNextPage);
  });

  // this is for changing background color of the Breakfast, Lunch, Dinner navigation bar dynamically
  document.addEventListener('DOMContentLoaded', function () {
    function handleBoxClick(category) {
      // resetting background color when click event happens -- to dark green
      document.getElementById('breakfast_box').style.backgroundColor = '#2b8840';
      document.getElementById('lunch_box').style.backgroundColor = '#2b8840';
      document.getElementById('dinner_box').style.backgroundColor = '#2b8840';

      // change color the selected box -- to darkest green
      document.getElementById(category + '_box').style.backgroundColor = '#123a1b';
    }
  });