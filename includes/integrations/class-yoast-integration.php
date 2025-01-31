<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Yoast_Integration {
    public function __construct() {
        add_action('admin_init', array($this, 'check_yoast_compatibility'));
    }

    public function check_yoast_compatibility() {
        if (!defined('WPSEO_VERSION')) {
            return array(
                'status'  => 'warning',
                'message' => __('Yoast SEO plugin is not active. Please install and activate Yoast SEO for full compatibility.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'success',
            'message' => __('Yoast SEO plugin is active and compatible.', 'sums-solution'),
        );
    }

    public function get_yoast_data($post_id) {
        if (!defined('WPSEO_VERSION')) {
            return false;
        }

        $yoast_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
        $yoast_description = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
        $yoast_focus_keyword = get_post_meta($post_id, '_yoast_wpseo_focuskw', true);

        return array(
            'title'       => $yoast_title,
            'description' => $yoast_description,
            'focus_keyword' => $yoast_focus_keyword,
        );
    }
}

new Sums_Yoast_Integration();