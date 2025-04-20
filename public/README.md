# Lite Booking Calendar - Embedded Calendar

This package provides an embeddable booking calendar that can be easily integrated into WordPress and HTML websites.

## Files Included

- `embedded-calendar.html` - The standalone calendar HTML file
- `embed.js` - JavaScript file for embedding the calendar in any website
- `embed-example.html` - Example HTML file showing how to use the embed script
- `lite-booking-calendar.php` - WordPress plugin for easy integration

## Integration Options

### Option 1: Direct HTML Integration

For regular HTML websites, you can embed the calendar using the provided embed script:

1. Upload all files to your web server
2. Include the embed script in your HTML page:
   ```html
   <script src="https://your-domain.com/embed.js"></script>
   ```
3. Add a container element where you want the calendar to appear:
   ```html
   <div id="lite-booking-calendar" data-api="https://your-api-endpoint"></div>
   ```

The `data-api` attribute is optional. If provided, it specifies the API endpoint for fetching calendar data.

### Option 2: WordPress Plugin

For WordPress websites, you can use the provided WordPress plugin:

1. Create a zip file containing `lite-booking-calendar.php`, `embed.js`, and `embedded-calendar.html`
2. Upload and activate the plugin through the WordPress admin panel
3. Configure the API URL in the plugin settings (Settings > Lite Booking Calendar)
4. Add the shortcode to any post or page:
   ```
   [lite_booking_calendar]
   ```

You can also specify a custom API URL for a specific calendar instance:
```
[lite_booking_calendar api="https://your-custom-api.com"]
```

### Option 3: WordPress Manual Integration

If you prefer not to use the plugin, you can still embed the calendar in WordPress:

1. Upload the `embed.js` and `embedded-calendar.html` files to your server
2. Add a Custom HTML block to your post or page
3. Insert the following code:
   ```html
   <script src="https://your-domain.com/embed.js"></script>
   <div id="lite-booking-calendar" data-api="https://your-api-endpoint"></div>
   ```

## API Integration

The calendar expects the API to provide date information in the following format:

```json
[
  {
    "date": "2023-01-01",
    "status": "available"
  },
  {
    "date": "2023-01-02",
    "status": "booked"
  },
  {
    "date": "2023-01-03",
    "status": "half-booked"
  }
]
```

The API endpoint should be accessible at `/api/dates` or at the URL specified in the `data-api` attribute.

## Customization

There are two ways to customize the appearance of the calendar:

1. **Using the custom CSS file**: The `calendar-custom.css` file contains CSS variables that can be modified to change the appearance of the calendar without editing the HTML file directly. Simply edit the variables in this file to match your website's design.

2. **Modifying the HTML**: You can also customize the appearance by modifying the CSS in the `embedded-calendar.html` file directly. The calendar uses Tailwind CSS for styling, making it easy to adjust colors, spacing, and other visual elements.

Example of customizing using the CSS variables:

```css
:root {
  /* Change the colors to match your website */
  --calendar-background: #f8f9fa;
  --calendar-text-color: #212529;
  
  /* Change the status colors */
  --status-available-background: #d1e7dd;
  --status-available-color: #0f5132;
  --status-booked-background: #f8d7da;
  --status-booked-color: #842029;
}
```

## Browser Compatibility

The embedded calendar is compatible with all modern browsers, including:
- Chrome
- Firefox
- Safari
- Edge

## Troubleshooting

If the calendar doesn't appear:
1. Check the browser console for any JavaScript errors
2. Verify that the API endpoint is accessible and returns data in the expected format
3. Ensure that the embed script and calendar HTML file are properly uploaded to your server

## License

This software is provided as-is with no warranty. You are free to use, modify, and distribute it as needed.