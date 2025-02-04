<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_API_Settings {
    public function __construct() {
        add_action('admin_init', array($this, 'register_api_settings'));
    }

    public function register_api_settings() {
        register_setting('sums_seo_api_group', 'sums_seo_api_keys');

        add_settings_section(
            'sums_seo_api_section',
            __('API Settings', 'sums-solution'),
            array($this, 'render_api_section'),
            'sums-seo-api-settings'
        );

        add_settings_field(
            'sums_seo_api_key',
            __('API Key', 'sums-solution'),
            array($this, 'render_api_key_field'),
            'sums-seo-api-settings',
            'sums_seo_api_section'
        );
    }

    public function render_api_section() {
        echo '<p>' . __('Manage your API keys here.', 'sums-solution') . '</p>';
    }

    public function render_api_key_field() {
        $api_keys = get_option('sums_seo_api_keys', array());
        echo '<input type="text" name="sums_seo_api_keys[api_key]" value="' . esc_attr($api_keys['api_key'] ?? '') . '" class="regular-text">';
    }
}