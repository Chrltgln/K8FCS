document.addEventListener("DOMContentLoaded", function () {
  const applyDropdownBtn = document.getElementById("applyDropdownBtn");
  const applyDropdown = document.getElementById("applyDropdown");
  const applyDropdownBtnMobile = document.getElementById("applyDropdownBtnMobile");
  const applyDropdownMobile = document.getElementById("applyDropdownMobile");
  const sidebar = document.querySelector(".sidebar");
  const userDropdownBtn = document.getElementById("userDropdownBtn");
  const userDropdown = document.getElementById("userDropdown");

  let currentOpenDropdown = null;

  function toggleDropdown(button, dropdown) {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      if (currentOpenDropdown && currentOpenDropdown !== dropdown) {
        currentOpenDropdown.style.display = "none";
      }
      if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
        currentOpenDropdown = null;
      } else {
        dropdown.style.display = "block";
        currentOpenDropdown = dropdown;
      }
    });
  }

  function hideDropdown(dropdown) {
    if (dropdown) {
      dropdown.style.display = "none";
    }
  }

  function hideAllDropdowns() {
    hideDropdown(applyDropdown);
    hideDropdown(applyDropdownMobile);
    hideDropdown(userDropdown);
    currentOpenDropdown = null;
  }

  if (applyDropdownBtn && applyDropdown) {
    toggleDropdown(applyDropdownBtn, applyDropdown);
  } else {
    console.error("Apply dropdown button or content not found");
  }

  if (applyDropdownBtnMobile && applyDropdownMobile) {
    toggleDropdown(applyDropdownBtnMobile, applyDropdownMobile);
  } else {
    console.error("Apply dropdown button or content not found for mobile");
  }

  if (userDropdownBtn && userDropdown) {
    toggleDropdown(userDropdownBtn, userDropdown);
  } else {
    console.error("User dropdown button or content not found");
  }

  // Close sidebar if not mobile
  function handleResize() {
    if (window.innerWidth > 600) {
      sidebar.classList.remove("show");
    }
  }

  window.addEventListener("resize", handleResize);
  handleResize(); // Initial check

  const hamburger = document.querySelector(".hamburger");
  hamburger.addEventListener("click", function () {
    sidebar.classList.toggle("show");
    hideAllDropdowns();
  });
});