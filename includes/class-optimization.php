<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Optimization {
    public function __construct() {
        add_action('init', array($this, 'enable_caching'));
        add_action('wp_enqueue_scripts', array($this, 'lazy_load_resources'));
    }

    /**
     * Enable caching for SEO data.
     */
    public function enable_caching() {
        if (!wp_cache_get('sums_seo_data')) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'auto_sums';
            $data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY scan_date DESC LIMIT 100");
            wp_cache_set('sums_seo_data', $data, '', 3600); // Cache for 1 hour
        }
    }

    /**
     * Lazy-load resources.
     */
    public function lazy_load_resources() {
        wp_enqueue_script('sums-seo-lazy-load', SUMS_SEO_PLUGIN_URL . 'assets/js/lazy-load.js', array(), null, true);
    }
}