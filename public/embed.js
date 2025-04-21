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
      iframe.style.height = "500px"; // Initial height, will be adjusted dynamically
      iframe.style.border = "none";
      iframe.style.overflow = "hidden";
      iframe.scrolling = "no"; // Disable scrolling in the iframe

      // Set the source URL with parameters
      const srcUrl = new URL(
        "https://calendar.landsbyidyl.dk/embedded-calendar.html"
      );

      // Add API parameter if provided
      if (apiUrl) {
        srcUrl.searchParams.set("api", apiUrl);
      }

      // Check if admin parameter is provided
      const isAdmin = container.getAttribute("data-admin") === "true";
      if (isAdmin) {
        srcUrl.searchParams.set("admin", "true");
      }

      iframe.src = srcUrl.toString();

      // Append iframe to container
      container.appendChild(iframe);

      // Add resize event listener to adjust iframe height
      window.addEventListener("message", function (event) {
        try {
          if (event.data && event.data.type === "lite-booking-height") {
            console.log("Received height update:", event.data.height);
            // Add a small buffer to prevent scrollbars (5px)
            iframe.style.height = event.data.height + 5 + "px";
          }
        } catch (error) {
          console.error("Error handling iframe height message:", error);
        }
      });
    });
  });
})();
