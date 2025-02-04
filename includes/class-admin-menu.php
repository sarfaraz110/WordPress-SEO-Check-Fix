<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Admin_Menu {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Sums SEO Check Fix', 'sums-solution'),
            __('Sums SEO', 'sums-solution'),
            'manage_options',
            'sums-seo-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-chart-area',
            6
        );

        add_submenu_page(
            'sums-seo-dashboard',
            __('Dashboard', 'sums-solution'),
            __('Dashboard', 'sums-solution'),
            'manage_options',
            'sums-seo-dashboard',
            array($this, 'render_dashboard')
        );

        add_submenu_page(
            'sums-seo-dashboard',
            __('API Settings', 'sums-solution'),
            __('API Settings', 'sums-solution'),
            'manage_options',
            'sums-seo-api-settings',
            array($this, 'render_api_settings')
        );
    }

    public function render_dashboard() {
        include SUMS_SEO_PLUGIN_DIR . 'templates/dashboard.php';
    }

    public function render_api_settings() {
        include SUMS_SEO_PLUGIN_DIR . 'templates/api-settings.php';
    }
    public function render_reports() {
        include SUMS_SEO_PLUGIN_DIR . 'templates/reports.php';
    }

    public function add_reports_submenu() {
        add_submenu_page(
            'sums-seo-dashboard',
            __('Reports', 'sums-solution'),
            __('Reports', 'sums-solution'),
            'manage_options',
            'sums-seo-reports',
            array($this, 'render_reports')
        );
    }
}

add_action('admin_menu', function() {
    $menu = new Sums_SEO_Admin_Menu();
    $menu->add_reports_submenu();
});