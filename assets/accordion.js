/**
 * Accordion Toggle Functionality
 * Handles expand/collapse of accordion items with smooth icon rotation
 */
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".accordion-toggle").forEach((button) => {
    button.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const content = document.getElementById(targetId);
      const icon = this.querySelector(".accordion-icon");

      if (content) {
        content.classList.toggle("hidden");
      }

      if (icon) {
        icon.classList.toggle("rotate-180");
      }
    });
  });
});
