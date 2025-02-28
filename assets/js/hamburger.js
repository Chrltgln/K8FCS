document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");
    const hamburger = document.querySelector(".hamburger");
  
    // Open sidebar when hamburger is clicked
    hamburger.addEventListener("click", () => {
      sidebar.classList.toggle("active");
    });
  
    // Close sidebar when clicking outside of it
    document.addEventListener("click", (event) => {
      const isClickInsideSidebar = sidebar.contains(event.target);
      const isClickOnHamburger = hamburger.contains(event.target);
  
      if (!isClickInsideSidebar && !isClickOnHamburger) {
        sidebar.classList.remove("active");
      }
    });
  });
  