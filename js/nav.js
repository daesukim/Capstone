document.addEventListener("DOMContentLoaded", function() {
    var documentHeight = document.documentElement.scrollHeight;

    function calculateDocumentHeight() {
        documentHeight = document.documentElement.scrollHeight;
        console.log("Document height recalculated: " + documentHeight);
    }

    function openNav() {
        document.querySelector('#mySidenav').style.width = "250px"; 
        document.querySelector('.all-over-bkg').classList.add('is-visible');
        document.querySelector('.is-visible').style.height = documentHeight + 'px';
    }

    function closeNav() {
        document.querySelector('#mySidenav').style.width = "0"; 
        document.querySelector('.all-over-bkg').classList.remove('is-visible');
    }

    document.querySelector('.openbtn').addEventListener('click', openNav);
    document.querySelector('.closebtn').addEventListener('click', closeNav);

    window.addEventListener('resize', calculateDocumentHeight);
});