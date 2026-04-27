document.addEventListener("DOMContentLoaded", function () {
  // Only run on series archive pages
  if (!document.body.classList.contains("tax-series")) {
    return;
  }

  const seriesLinks = document.querySelectorAll(".series-post-link");

  seriesLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const url = this.href;

      // Update URL without page reload
      history.pushState(null, "", url);

      // Reload the page content (or use AJAX to update specific parts)
      window.location.reload();
    });
  });
});
