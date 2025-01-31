<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Load admin functionality
require_once __DIR__ . '/admin/class-admin-dashboard.php';
require_once __DIR__ . '/admin/class-api-settings.php';
require_once __DIR__ . '/admin/class-reports.php';

// Load SEO analysis functionality
require_once __DIR__ . '/seo-analysis/class-seo-title.php';
require_once __DIR__ . '/seo-analysis/class-meta-description.php';
require_once __DIR__ . '/seo-analysis/class-focus-keyphrase.php';
require_once __DIR__ . '/seo-analysis/class-h1-tag.php';
require_once __DIR__ . '/indexability/class-indexability-check.php';
require_once __DIR__ . '/algorithm-compliance/class-google-updates.php';
require_once __DIR__ . '/integrations/class-yoast-integration.php';
require_once __DIR__ . '/integrations/class-moz-integration.php';
require_once __DIR__ . '/automation/class-auto-fix.php';
require_once __DIR__ . '/automation/class-manual-scan.php';
require_once __DIR__ . '/automation/class-openai-fix.php';
require_once __DIR__ . '/common/class-pagination.php';
// Add more includes as needed

// Initialize the plugin
function sums_seo_check_fix_init() {
    // Load text domain for translations
    load_plugin_textdomain('sums-solution', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'sums_seo_check_fix_init');