<?php
/**
 * Plugin Name: Sums SEO Check Fix
 * Version: 1.0.0
 * Plugin URI: https://sumssolution.com/
 * Description: Sums SEO Check Fix is the Best WordPress SEO plugin with the features of many SEO and AI SEO tools in a single package to Fix Blog Keyword Dublicate Content H1 Tag Etc.
 * Author: Sums SEO Check Fix
 * Author URI: https://sumssolution.com/
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: sums-solution
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SUMS_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SUMS_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load necessary files
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-admin-menu.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-admin-security.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-ai-rewriting.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-api-auto-updates.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-api-integration.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-api-settings.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-auto-updates.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-broken-link-scanner.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-competitor-analysis.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-cron-jobs.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-database.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-image-seo.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-internal-linking.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-lazy-loading.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-monthly-reports.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-optimization.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-performance-tracking.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-schema-generator.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-security.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-seo-scanner.php';
require_once SUMS_SEO_PLUGIN_DIR . 'includes/class-woocommerce-seo.php';

// Initialize the plugin
function sums_seo_check_fix_init() {
    // Load translations
    load_plugin_textdomain('sums-solution', false, dirname(plugin_basename(__FILE__)) . '/languages/');

    // Initialize classes
    new Sums_SEO_Admin_Menu();
    
    new Sums_SEO_API_Settings();
    new Sums_SEO_Scanner();
    new Sums_SEO_Database();
    new Sums_SEO_Cron_Jobs();
    new Sums_SEO_AJAX_Handler();
}
add_action('plugins_loaded', 'sums_seo_check_fix_init');

// Activation hook
register_activation_hook(__FILE__, array('Sums_SEO_Database', 'create_database_table'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Sums_SEO_Cron_Jobs', 'clear_scheduled_tasks'));

function sums_seo_enqueue_assets() {
    wp_enqueue_style('sums-seo-admin-css', SUMS_SEO_PLUGIN_URL . 'assets/css/admin.css');
    wp_enqueue_script('sums-seo-admin-js', SUMS_SEO_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), null, true);
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    wp_enqueue_script('sums-seo-charts-js', SUMS_SEO_PLUGIN_URL . 'assets/js/charts.js', array('chart-js'), null, true);
}
add_action('admin_enqueue_scripts', 'sums_seo_enqueue_assets');

// Initialize schema generator
function sums_seo_schema_init() {
    $schema_generator = new Sums_SEO_Schema_Generator();
    add_action('wp_head', array($schema_generator, 'output_schema_markup'));
}
add_action('init', 'sums_seo_schema_init');

// Initialize broken link scanner
function sums_seo_broken_link_scanner_init() {
    new Sums_SEO_Broken_Link_Scanner();
}
add_action('init', 'sums_seo_broken_link_scanner_init');    

// Initialize WooCommerce SEO
function sums_seo_woocommerce_init() {
    if (class_exists('WooCommerce')) {
        new Sums_SEO_WooCommerce();
    }
}
add_action('init', 'sums_seo_woocommerce_init');

// Initialize Image SEO
function sums_seo_image_seo_init() {
    new Sums_SEO_Image_SEO();
}
add_action('init', 'sums_seo_image_seo_init');

// Initialize security features
function sums_seo_security_init() {
    new Sums_SEO_Security();
}
add_action('init', 'sums_seo_security_init');

// Initialize lazy loading
function sums_seo_lazy_loading_init() {
    new Sums_SEO_Lazy_Loading();
}
add_action('init', 'sums_seo_lazy_loading_init');

// Initialize internal linking
function sums_seo_internal_linking_init() {
    new Sums_SEO_Internal_Linking();
}
add_action('init', 'sums_seo_internal_linking_init');

// Initialize competitor analysis
function sums_seo_competitor_analysis_init() {
    new Sums_SEO_Competitor_Analysis();
}
add_action('init', 'sums_seo_competitor_analysis_init');

// Initialize API integration
function sums_seo_api_integration_init() {
    new Sums_SEO_API_Integration();
}
add_action('init', 'sums_seo_api_integration_init');

// Initialize monthly reports
function sums_seo_monthly_reports_init() {
    new Sums_SEO_Monthly_Reports();
}
add_action('init', 'sums_seo_monthly_reports_init');

// Initialize auto updates
function sums_seo_auto_updates_init() {
    new Sums_SEO_Auto_Updates();
}
add_action('init', 'sums_seo_auto_updates_init');

// Initialize WooCommerce schema UI
function sums_seo_woocommerce_schema_ui_init() {
    if (class_exists('WooCommerce')) {
        new Sums_SEO_WooCommerce_Schema_UI();
    }
}
add_action('init', 'sums_seo_woocommerce_schema_ui_init');

// Initialize performance tracking
function sums_seo_performance_tracking_init() {
    new Sums_SEO_Performance_Tracking();
}
add_action('init', 'sums_seo_performance_tracking_init');

// Initialize AI rewriting
function sums_seo_ai_rewriting_init() {
    new Sums_SEO_AI_Rewriting();
}
add_action('init', 'sums_seo_ai_rewriting_init');

// Initialize optimization
function sums_seo_optimization_init() {
    new Sums_SEO_Optimization();
}
add_action('init', 'sums_seo_optimization_init');

// Initialize API auto-updates
function sums_seo_api_auto_updates_init() {
    new Sums_SEO_API_Auto_Updates();
}
add_action('init', 'sums_seo_api_auto_updates_init');

// Initialize admin security
function sums_seo_admin_security_init() {
    new Sums_SEO_Admin_Security();
}
add_action('init', 'sums_seo_admin_security_init');