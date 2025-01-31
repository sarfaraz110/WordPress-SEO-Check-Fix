<?php
/**
 * Plugin Name: Sums SEO Check Fix
 * Plugin URI: https://sumssolution.com/
 * Description: Advanced SEO optimization plugin for WordPress. Automatically detects and resolves SEO and readability issues in posts and pages.
 * Version: 1.0.0
 * Author: Sums Solution
 * Author URI: https://sumssolution.com/
 * License: GPL-3.0+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: sums-solution
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/includes/init.php';

function sums_enqueue_scripts() {
    // Enqueue CSS
    wp_enqueue_style('sums-style', plugins_url('css/sums-style.css', __FILE__));

    // Enqueue JS
    wp_enqueue_script('sums-ajax', plugins_url('js/ajax.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);

    // Localize script for AJAX
    wp_localize_script('sums-ajax', 'sums_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('sums_ajax_nonce'),
    ));
}
add_action('admin_enqueue_scripts', 'sums_enqueue_scripts');