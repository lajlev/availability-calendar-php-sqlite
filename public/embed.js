/**
 * Lite Booking Calendar Embed Script
 *
 * Usage:
 * 1. Include this script in your HTML or WordPress page:
 *    <script src="https://your-domain.com/embed.js"></script>
 *
 * 2. Add a container element where you want the calendar to appear:
 *    <div id="lite-booking-calendar" data-api="https://your-api-endpoint"></div>
 *
 * 3. Optional: Include the custom CSS file for styling:
 *    <link rel="stylesheet" href="https://your-domain.com/calendar-custom.css">
 *
 * 4. The calendar will automatically be embedded in the container element.
 */

(function () {
  // Wait for DOM to be ready
  document.addEventListener("DOMContentLoaded", function () {
    // Find all calendar containers
    const containers = document.querySelectorAll("#lite-booking-calendar");

    containers.forEach(function (container) {
      // Get API URL from data attribute or use default
      const apiUrl = container.getAttribute("data-api") || "";

      // Create iframe
      const iframe = document.createElement("iframe");

      // Set iframe attributes
      iframe.style.width = "100%";
      iframe.style.height = "800px";
      iframe.style.border = "none";
      iframe.style.overflow = "hidden";

      // Set the source URL with API parameter if provided
      const srcUrl = new URL(
        window.location.origin + "/embedded-calendar.html"
      );
      if (apiUrl) {
        srcUrl.searchParams.set("api", apiUrl);
      }
      iframe.src = srcUrl.toString();

      // Append iframe to container
      container.appendChild(iframe);

      // Add resize event listener to adjust iframe height
      window.addEventListener("message", function (event) {
        if (event.data && event.data.type === "lite-booking-height") {
          iframe.style.height = event.data.height + "px";
        }
      });
    });
  });
})();
