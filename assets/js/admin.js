// Toggle Sidebar
document
  .querySelector(".toggle-sidebar")
  .addEventListener("click", function () {
    document.querySelector(".sidebar").classList.toggle("active");
  });

// Close sidebar when clicking outside
document.addEventListener("click", function (e) {
  const sidebar = document.querySelector(".sidebar");
  const toggleBtn = document.querySelector(".toggle-sidebar");

  if (
    !sidebar.contains(e.target) &&
    !toggleBtn.contains(e.target) &&
    sidebar.classList.contains("active")
  ) {
    sidebar.classList.remove("active");
  }
});

// Add active class to current nav item
document.addEventListener("DOMContentLoaded", function () {
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll(".sidebar-nav a");

  navLinks.forEach((link) => {
    if (currentPath.includes(link.getAttribute("href"))) {
      link.parentElement.classList.add("active");
    }
  });
});
