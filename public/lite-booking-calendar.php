<?php
/**
 * Plugin Name: Lite Booking Calendar
 * Plugin URI: https://your-domain.com
 * Description: A simple booking calendar that can be embedded in WordPress sites.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://your-domain.com
 * Text Domain: lite-booking-calendar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Lite_Booking_Calendar {
    /**
     * Constructor
     */
    public function __construct() {
        // Register shortcode
        add_shortcode('lite_booking_calendar', array($this, 'render_calendar_shortcode'));
        
        // Register and enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue custom CSS if enabled
        if (get_option('lite_booking_use_custom_css', 'yes') === 'yes') {
            wp_enqueue_style(
                'lite-booking-custom-css',
                plugin_dir_url(__FILE__) . 'calendar-custom.css',
                array(),
                '1.0.0'
            );
        }
        
        // Enqueue embed script
        wp_enqueue_script(
            'lite-booking-embed',
            plugin_dir_url(__FILE__) . 'embed.js',
            array(),
            '1.0.0',
            true
        );
    }
    
    /**
     * Render calendar shortcode
     */
    public function render_calendar_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'api' => get_option('lite_booking_api_url', ''),
            ),
            $atts,
            'lite_booking_calendar'
        );
        
        $api_url = esc_url($atts['api']);
        
        ob_start();
        ?>
        <div id="lite-booking-calendar" data-api="<?php echo $api_url; ?>"></div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Lite Booking Calendar Settings',
            'Lite Booking Calendar',
            'manage_options',
            'lite-booking-calendar',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('lite_booking_calendar_options', 'lite_booking_api_url');
        register_setting('lite_booking_calendar_options', 'lite_booking_use_custom_css');
        
        add_settings_section(
            'lite_booking_calendar_section',
            'Calendar Settings',
            array($this, 'render_settings_section'),
            'lite-booking-calendar'
        );
        
        add_settings_field(
            'lite_booking_api_url',
            'API URL',
            array($this, 'render_api_url_field'),
            'lite-booking-calendar',
            'lite_booking_calendar_section'
        );
        
        add_settings_field(
            'lite_booking_use_custom_css',
            'Use Custom CSS',
            array($this, 'render_custom_css_field'),
            'lite-booking-calendar',
            'lite_booking_calendar_section'
        );
    }
    
    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>Configure the settings for your Lite Booking Calendar.</p>';
    }
    
    /**
     * Render API URL field
     */
    public function render_api_url_field() {
        $api_url = get_option('lite_booking_api_url', '');
        ?>
        <input type="url" name="lite_booking_api_url" value="<?php echo esc_attr($api_url); ?>" class="regular-text">
        <p class="description">Enter the URL of your booking API endpoint.</p>
        <?php
    }
    
    /**
     * Render custom CSS field
     */
    public function render_custom_css_field() {
        $use_custom_css = get_option('lite_booking_use_custom_css', 'yes');
        ?>
        <select name="lite_booking_use_custom_css">
            <option value="yes" <?php selected($use_custom_css, 'yes'); ?>>Yes</option>
            <option value="no" <?php selected($use_custom_css, 'no'); ?>>No</option>
        </select>
        <p class="description">Enable or disable the custom CSS styling for the calendar.</p>
        <p>
            <a href="<?php echo esc_url(plugin_dir_url(__FILE__) . 'calendar-custom.css'); ?>" target="_blank">View custom CSS file</a> |
            <a href="<?php echo esc_url(admin_url('theme-editor.php?file=calendar-custom.css&theme=' . get_stylesheet())); ?>" target="_blank">Edit custom CSS</a>
        </p>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('lite_booking_calendar_options');
                do_settings_sections('lite-booking-calendar');
                submit_button('Save Settings');
                ?>
            </form>
            
            <div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2>How to Use the Calendar</h2>
                <p>Use the following shortcode to display the booking calendar in your posts or pages:</p>
                <code>[lite_booking_calendar]</code>
                
                <p>You can also specify a custom API URL for a specific calendar instance:</p>
                <code>[lite_booking_calendar api="https://your-custom-api.com"]</code>
                
                <p>If no API URL is specified in the shortcode, the plugin will use the default API URL configured in the settings above.</p>
                
                <h3>Customizing the Calendar</h3>
                <p>You can customize the appearance of the calendar by editing the <code>calendar-custom.css</code> file. This file contains CSS variables that control the colors, sizes, and other visual aspects of the calendar.</p>
                <p>To edit the CSS file, click the "Edit custom CSS" link in the settings above.</p>
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin
new Lite_Booking_Calendar();