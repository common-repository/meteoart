<?php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/meteoart/
 * @since             1.0.0
 * @package           Meteo
 *
 * @wordpress-plugin
 * Plugin Name:       MeteoArt
 * Plugin URI:        https://www.meteoart.com/widgets
 * Description:       Beautiful French weather forecasting widget.zz
 * Version:           1.0.0
 * Author:            meteoart
 * Author URI:        https://profiles.wordpress.org/meteoart/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       meteo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('METEO_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-meteo-activator.php
 */
function activate_meteo()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-meteo-activator.php';
    Meteo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-meteo-deactivator.php
 */
function deactivate_meteo()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-meteo-deactivator.php';
    Meteo_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_meteo');
register_deactivation_hook(__FILE__, 'deactivate_meteo');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-meteo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_meteo()
{

    $plugin = new Meteo();
    $plugin->run();

}

run_meteo();


class meteo_widget extends WP_Widget
{
    // Set up the widget name and description.
    public function __construct()
    {
        $widget_options = array(
            'classname' => 'meteo_widget',
            'description' => 'French weather forecast widget. All locations around the world',
            'show_in_rest' => true  // Add this line to support block editor
        );
        parent::__construct('meteo_widget', 'Meteo Widget', $widget_options);
    }


    // Create the widget output.
    public function widget($args, $instance)
    {
        // Keep this line
        echo $args['before_widget'];

        $city = $instance['city'];
        $country = $instance['country'];
        $backgroundColor = $instance['backgroundColor'];
        $widgetWidth = $instance['widgetWidth'];
        $textColor = $instance['textColor'];
        $days = $instance['days'];
        $showSunrise = $instance['showSunrise'];
        $showWind = $instance['showWind'];
        $language = $instance['language'];
        $showCurrent = $instance['showCurrent'];

        echo '<div class="meteo-widget weather_widget_wrap"
                 data-text-color="' . $textColor . '"
                 data-background="' . $backgroundColor . '"
                 data-width="' . $widgetWidth . '"
                 data-days="' . $days . '"
                 data-sunrise="' . $showSunrise . '"
                 data-wind="' . $showWind . '"
                 data-current="' . $showCurrent . '"
                 data-language="' . $language . '"
                 data-city="' . $city . '"
                 data-country="' . $country . '">
    
                <div class="weather_widget_placeholder"></div>
                <div style="font-size: 14px;text-align: center;padding-top: 6px;padding-bottom: 4px;background: rgba(0,0,0,0.03);">
                    Data from <a target="_blank" href="https://www.meteoart.com">MeteoArt.com</a>
                </div>
            </div>';

        echo $args['after_widget'];
    }


    // Create the admin area widget settings form.
    public function form($instance)
    {
        // print_r($instance);
        $city = !empty($instance['city']) ? $instance['city'] : 'Paris';
        $country = !empty($instance['country']) ? $instance['country'] : 'France';
        $backgroundColor = !empty($instance['backgroundColor']) ? $instance['backgroundColor'] : '#becffb';
        $textColor = !empty($instance['textColor']) ? $instance['textColor'] : '#000000';

        if (isset($instance['widgetWidth'])) {
            $widgetWidth = $instance['widgetWidth'];
        } else {
            $widgetWidth = '100';
        }

        if (isset($instance['days'])) {
            $days = $instance['days'];
        } else {
            $days = 3;
        }

        if (isset($instance['language'])) {
            $language = $instance['language'];
        } else {
            $language = "french";
        }


        if (isset($instance['showSunrise'])) {
            $showSunrise = $instance['showSunrise'];
        } else {
            $showSunrise = "";
        }

        if (isset($instance['showWind'])) {
            $showWind = $instance['showWind'];
        } else {
            $showWind = "";
        }

        $showCurrent = !empty($instance['showCurrent']) ? $instance['showCurrent'] : 'on';

        ?>
        <div class="meteo_form">
            <div class="form-section">
                <h3>Location</h3>
                <div class="form-line">
                    <label class="text-label" for="<?php echo $this->get_field_id('city'); ?>">City:</label>
                    <input type="text" id="<?php echo $this->get_field_id('city'); ?>"
                           name="<?php echo $this->get_field_name('city'); ?>"
                           value="<?php echo esc_attr($city); ?>"/>
                </div>
                <div class="form-line">
                    <label class="text-label" for="<?php echo $this->get_field_id('country'); ?>">Country:</label>
                    <input type="text" id="<?php echo $this->get_field_id('country'); ?>"
                           name="<?php echo $this->get_field_name('country'); ?>"
                           value="<?php echo esc_attr($country); ?>"/>
                </div>
            </div>

            <div class="form-section">
                <h3>Widget Language</h3>
                <div class="form-line">
                    <select name="<?php echo $this->get_field_name('language'); ?>">
                        <option value="english" <?php if ($language == "english") {
                            echo 'selected';
                        } ?>>English
                        </option>
                        <option value="french" <?php if ($language == "french") {
                            echo 'selected';
                        } ?>>French
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h3>Weather Data</h3>
                <div class="form-line">
                    <input type="checkbox"
                        <?php if ($showCurrent == 'on') {
                            echo 'checked';
                        }; ?>
                           id="<?php echo $this->get_field_id('showCurrent'); ?>"
                           name="<?php echo $this->get_field_name('showCurrent'); ?>"/>
                    <label for="<?php echo $this->get_field_id('showCurrent'); ?>">Show: Current weather</label>
                </div>
                <div class="form-line">
                    <input type="checkbox"
                        <?php if ($showWind == 'on') {
                            echo 'checked';
                        }; ?>
                           id="<?php echo $this->get_field_id('showWind'); ?>"
                           name="<?php echo $this->get_field_name('showWind'); ?>"/>
                    <label for="<?php echo $this->get_field_id('showWind'); ?>">Show: Chance for rain, Wind and
                        Humidity</label>
                </div>
                <div class="form-line">
                    <input type="checkbox"
                        <?php if ($showSunrise == 'on') {
                            echo 'checked';
                        }; ?>
                           id="<?php echo $this->get_field_id('showSunrise'); ?>"
                           name="<?php echo $this->get_field_name('showSunrise'); ?>"/>
                    <label for="<?php echo $this->get_field_id('showSunrise'); ?>">Show: Sunrise and sunset time</label>
                </div>
            </div>
            <div class="form-section">
                <h3>Daily Forecast</h3>
                <div class="form-line">
                    <select name="<?php echo $this->get_field_name('days'); ?>">
                        <option value="0" <?php if ($days == 0) {
                            echo 'selected';
                        } ?>>No Daily Forecast
                        </option>
                        <option value="2" <?php if ($days == 2) {
                            echo 'selected';
                        } ?>>2 Days
                        <option value="3" <?php if ($days == 3) {
                            echo 'selected';
                        } ?>>3 Days
                        </option>
                        <option value="4" <?php if ($days == 4) {
                            echo 'selected';
                        } ?>>4 Days
                        </option>
                        <option value="5" <?php if ($days == 5) {
                            echo 'selected';
                        } ?>>5 Days
                        </option>
                        <option value="6" <?php if ($days == 6) {
                            echo 'selected';
                        } ?>>6 Days
                        </option>
                    </select>
                </div>
            </div>


            <div class="form-section">
                <h3>Look & Feel</h3>

                <div class="form-line">
                    <label for="<?php echo $this->get_field_id('backgroundColor'); ?>">Background Color
                        (optional):</label>
                    <input type="color" id="<?php echo $this->get_field_id('backgroundColor'); ?>"
                           name="<?php echo $this->get_field_name('backgroundColor'); ?>"
                           value="<?php echo esc_attr($backgroundColor); ?>"/>
                </div>
                <div class="form-line">
                    <label for="<?php echo $this->get_field_id('textColor'); ?>">Text Color (optional):</label>
                    <input type="color" id="<?php echo $this->get_field_id('textColor'); ?>"
                           name="<?php echo $this->get_field_name('textColor'); ?>"
                           value="<?php echo esc_attr($textColor); ?>"/>
                </div>
                <div>
                    <div class="widget-width-line"><label for="<?php echo $this->get_field_id('widgetWidth'); ?>">Widget
                            Stretch (width):</label>
                    </div>
                    <div class="form-line">
                        <input type="radio" id="<?php echo $this->get_field_id('widgetWidth'); ?>"
                            <?php if ($widgetWidth == '100') {
                                echo 'checked';
                            }; ?>
                               name="<?php echo $this->get_field_name('widgetWidth'); ?>"
                               value="100"/> 100%
                        <input type="radio" id="<?php echo $this->get_field_id('widgetWidth'); ?>"
                            <?php if ($widgetWidth == 'tight') {
                                echo 'checked';
                            }; ?>
                               name="<?php echo $this->get_field_name('widgetWidth'); ?>"
                               value="tight"/> Tight as possible
                    </div>
                </div>
            </div>
        </div>
        <?php
    }


    // Apply settings to the widget instance.
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        if (!empty($new_instance['city'])) {
            $instance['city'] = sanitize_text_field(strip_tags($new_instance['city']));
        }

        if (!empty($new_instance['country'])) {
            $instance['country'] = sanitize_text_field(strip_tags($new_instance['country']));
        }
        $instance['backgroundColor'] = sanitize_hex_color(strip_tags($new_instance['backgroundColor']));
        $instance['textColor'] = sanitize_hex_color(strip_tags($new_instance['textColor']));
        $instance['widgetWidth'] = sanitize_text_field(strip_tags($new_instance['widgetWidth']));
        $instance['showSunrise'] = sanitize_text_field($new_instance['showSunrise']);
        $instance['showWind'] = sanitize_text_field($new_instance['showWind']);
        $instance['showCurrent'] = sanitize_text_field($new_instance['showCurrent']);
        $instance['days'] = sanitize_text_field(strip_tags($new_instance['days']));
        $instance['language'] = sanitize_text_field(strip_tags($new_instance['language']));
        if ($new_instance['showSunrise'] != "on") {
            $instance['showSunrise'] = "false";
        }
        if ($new_instance['showWind'] != "on") {
            $instance['showWind'] = "false";
        }
        if ($new_instance['showCurrent'] != "on") {
            $instance['showCurrent'] = "false";
        }

        // Add this line at the end of your existing update function:
        // Save a copy of the widget's settings globally for the shortcode to access.
        update_option('meteo_global_settings', $instance);

        return $instance;
    }
}

// Register the widget.

function jpen_register_meteo_widget()
{
    register_widget('meteo_widget');
}

add_action('widgets_init', 'jpen_register_meteo_widget');


function meteo_shortcode($atts = [], $content = null, $tag = '')
{
    // Fetch global settings
    $global_settings = get_option('meteo_global_settings', []);

    // Define default values
    $defaults = [
        'city' => $global_settings['city'] ?? 'Paris',
        'country' => $global_settings['country'] ?? 'France',
        'background_color' => $global_settings['backgroundColor'] ?? '#becffb',
        'text_color' => $global_settings['textColor'] ?? '#000000',
        'widget_width' => $global_settings['widgetWidth'] ?? '100',
        'days' => $global_settings['days'] ?? 3,
        'show_sunrise' => $global_settings['showSunrise'] ?? '',
        'show_wind' => $global_settings['showWind'] ?? '',
        'language' => $global_settings['language'] ?? 'french',
        'show_current' => $global_settings['showCurrent'] ?? 'on',
    ];

    // Override default attributes with user attributes
    $meteo_atts = shortcode_atts($defaults, $atts, $tag);

    ob_start();
    // The widget output code using $meteo_atts...

    echo '<div class="meteo-widget weather_widget_wrap" data-text-color="' . esc_attr($meteo_atts['text_color']) . '" data-background="' . esc_attr($meteo_atts['background_color']) . '" data-width="' . esc_attr($meteo_atts['widget_width']) . '" data-days="' . esc_attr($meteo_atts['days']) . '" data-sunrise="' . esc_attr($meteo_atts['show_sunrise']) . '" data-wind="' . esc_attr($meteo_atts['show_wind']) . '" data-current="' . esc_attr($meteo_atts['show_current']) . '" data-language="' . esc_attr($meteo_atts['language']) . '" data-city="' . esc_attr($meteo_atts['city']) . '" data-country="' . esc_attr($meteo_atts['country']) . '"><div style="font-size: 14px;text-align: center;padding-top: 6px;padding-bottom: 4px;background: rgba(0,0,0,0.03);">Data from <a target="_blank" href="https://www.meteoart.com">MeteoArt.com</a></div></div>';

    return ob_get_clean();
}

add_shortcode('meteo', 'meteo_shortcode');


function meteo_add_admin_menu_page()
{
    add_menu_page(
        __('Meteo Weather Widget', 'meteo'), // Page title
        __('Meteo Weather Widget', 'meteo'),          // Menu title
        'manage_options',               // Capability required
        'meteo-settings',               // Menu slug
        'meteo_admin_settings_page',    // Function to display the settings page
        'dashicons-cloud',              // Icon URL (use a dashicon name)
    );
}

add_action('admin_menu', 'meteo_add_admin_menu_page');


function meteo_admin_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <h2>Instructions and Documentation</h2>
        <p>Welcome to MeteoArt, the comprehensive weather widget for your WordPress site! Below are instructions to help you set up and use the widget:</p>

        <h3>Adding the Widget to Your Site</h3>
        <p>MeteoArt can be added to your site using the classic widget area or via the block editor:</p>
        <ul>
            <li><strong>Classic Widgets:</strong> Navigate to the Widgets section under Appearance in your WordPress dashboard, add the MeteoArt widget to your desired sidebar or footer area, and configure it with your preferred settings.</li>
            <li><strong>Block Editor:</strong> While editing a page or a post, add the MeteoArt block by searching for "Meteo Widget" in the block inserter. Configure the block settings directly in the editor.</li>
        </ul>

        <h3>Using the Shortcode</h3>
        <p>The [meteo] shortcode allows you to embed the weather widget into posts, pages, or even widget areas that support text/html. You can customize it using the following attributes:</p>
        <ul>
            <li><code>city</code>: The city for which you want to display the weather (default: "Paris").</li>
            <li><code>country</code>: The country that the city is in (default: "France").</li>
            <li><code>days</code>: The number of days to show the weather forecast for (default: 3).</li>
            <li><code>show_current</code>: Whether to display current weather conditions ("on" or "off").</li>
            <li><code>show_wind</code>: Whether to show wind information ("on" or "off").</li>
            <li><code>show_sunrise</code>: Whether to display sunrise and sunset times ("on" or "off").</li>
            <li><code>background_color</code>: Background color of the widget (default: "#becffb").</li>
            <li><code>text_color</code>: Color of the text in the widget (default: "#000000").</li>
            <li><code>language</code>: Language of the weather information, such as "english" or "french" (default: "french").</li>
        </ul>
        <p>Example: [meteo city="Nice" country="France" days="3" show_current="on"]</p>

        <h3>Fair Use Policy</h3>
        <p>The MeteoArt widget is provided as a free service for personal and commercial use. We encourage fair use of our services and reserve the right to limit or block access to any users who abuse the system, such as by making excessive requests or using the service for non-standard purposes. By using this widget, you agree to use it responsibly and within the usage limits established.</p>

        <h3>Help Us Grow</h3>
        <p>If you find the MeteoArt widget useful, please consider helping us by leaving a review. Your feedback is not only greatly appreciated, but it also helps us to improve and provide you with the best service possible. Thank you for your support!</p>
        <a href="https://wordpress.org/support/plugin/meteoart/reviews/#new-post" target="_blank" class="button button-primary">Leave a Review</a>


        <h3>Need More Help?</h3>
        <p>If you have any questions or need further assistance, please contact us.</p>

        <h3>Enjoy the Weather!</h3>
        <p>We hope you enjoy using MeteoArt. Don't forget to check the weather before you go out!</p>
    </div>
    <?php
}



function meteo_register_settings()
{
    // Register a new setting for "meteo-settings" page.
    register_setting('meteo-settings', 'meteo_options');

    // Register a new section in the "meteo-settings" page.
    add_settings_section(
        'meteo_section_id',
        __('Meteo Custom Settings', 'meteo'),
        'meteo_section_callback',
        'meteo-settings'
    );

    // Register a new field in the "meteo_section_id" section, inside the "meteo-settings" page.
    add_settings_field(
        'meteo_field_id',                          // As part of the section
        __('Meteo Custom Field', 'meteo'),         // Field title
        'meteo_field_callback',                    // Callback for field markup
        'meteo-settings',                          // Page to go on
        'meteo_section_id'                         // Section to go in
    );
}

add_action('admin_init', 'meteo_register_settings');

function meteo_section_callback()
{
    echo '<p>' . __('This section description can be left blank, or used to describe your settings section.', 'meteo') . '</p>';
}


function meteo_load_textdomain()
{
    load_plugin_textdomain('meteo', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'meteo_load_textdomain');

function meteo_field_callback()
{
    // Retrieve the option value from the database
    $options = get_option('meteo_options');
    // Render the output for the "meteo_field" field
    echo '<input type="text" id="meteo_field" name="meteo_options[meteo_field]" value="' . esc_attr($options['meteo_field'] ?? '') . '"/>';
}


function meteo_enqueue_block_editor_assets()
{
    wp_enqueue_script(
        'meteo-block-editor',
        plugins_url('public/js/meteo-block-editor.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'public/js/meteo-block-editor.js'),
        true
    );

    // If you have localizations to pass to your script
    wp_localize_script('meteo-block-editor', 'meteoLocalize', array(
        'some_data' => 'Some value',
        // Add other data here
    ));

    wp_enqueue_style(
        'meteo-style',
        plugins_url('public/css/meteo-public.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'public/css/meteo-public.css')
    );
}

add_action('enqueue_block_editor_assets', 'meteo_enqueue_block_editor_assets');


function register_meteo_block()
{
    // Register the block editor script
    wp_register_script(
        'meteo-block-editor',
        plugins_url('public/js/meteo-block-editor.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n', 'wp-editor'),
        filemtime(plugin_dir_path(__FILE__) . 'public/js/meteo-block-editor.js')
    );

    // Enqueue the block editor style
    wp_enqueue_style(
        'meteo-block-editor-style',
        plugins_url('public/css/meteo-block-editor.css', __FILE__), // Path to your custom CSS file
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'public/css/meteo-block-editor.css')
    );

    // Register the block with a render callback for server-side rendering
    register_block_type('meteo/widget', array(
        'editor_script' => 'meteo-block-editor',
        'render_callback' => 'render_meteo_widget', // Specify the render callback function
    ));
}

// The render callback function to generate the block's HTML based on attributes
function render_meteo_widget($attributes)
{
    // Default values for attributes
    $defaults = [
        'city' => 'Paris',
        'country' => 'France',
        'backgroundColor' => '#becffb',
        'widgetWidth' => '100',
        'textColor' => '#000000',
        'days' => 3,
        'showSunrise' => '',
        'showWind' => '',
        'language' => 'french',
        'showCurrent' => 'on',
    ];

    // Merge defaults with actual attributes
    $attributes = shortcode_atts($defaults, $attributes);

    ob_start(); // Start output buffering to capture the HTML output
    ?>

    <div class="meteo-widget weather_widget_wrap"
         data-text-color="<?php echo esc_attr($attributes['textColor']); ?>"
         data-background="<?php echo esc_attr($attributes['backgroundColor']); ?>"
         data-width="<?php echo esc_attr($attributes['widgetWidth']); ?>"
         data-days="<?php echo esc_attr($attributes['days']); ?>"
         data-sunrise="<?php echo esc_attr($attributes['showSunrise']); ?>"
         data-wind="<?php echo esc_attr($attributes['showWind']); ?>"
         data-current="<?php echo esc_attr($attributes['showCurrent']); ?>"
         data-language="<?php echo esc_attr($attributes['language']); ?>"
         data-city="<?php echo esc_attr($attributes['city']); ?>"
         data-country="<?php echo esc_attr($attributes['country']); ?>">
        <div style="font-size: 14px;text-align: center;padding-top: 6px;padding-bottom: 4px;background: rgba(0,0,0,0.03);">
            Data from <a target="_blank" href="https://www.meteoart.com">MeteoArt.com</a>
        </div>
    </div>

    <?php
    $output = ob_get_clean(); // End output buffering and get the contents
    return $output; // Return the generated HTML to be rendered by the block
}


add_action('init', 'register_meteo_block');
