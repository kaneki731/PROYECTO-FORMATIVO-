const sidebar = document.getElementById("sidebar");

sidebar.addEventListener("mouseenter", () => {
  document.body.classList.add("sidebar-open");
});

sidebar.addEventListener("mouseleave", () => {
  document.body.classList.remove("sidebar-open");
});