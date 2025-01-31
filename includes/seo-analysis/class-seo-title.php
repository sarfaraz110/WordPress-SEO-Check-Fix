<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_SEO_Title {
    public function __construct() {
        add_action('admin_init', array($this, 'analyze_seo_title'));
    }

    public function analyze_seo_title($post_id) {
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        $seo_title = get_post_meta($post_id, '_sums_seo_title', true);
        if (empty($seo_title)) {
            $seo_title = $post->post_title;
        }

        // Check title length
        $title_length = strlen($seo_title);
        if ($title_length < 40 || $title_length > 60) {
            return array(
                'status'  => 'warning',
                'message' => __('SEO title should be between 40 and 60 characters.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'success',
            'message' => __('SEO title is optimized.', 'sums-solution'),
        );
    }
}

new Sums_SEO_Title();