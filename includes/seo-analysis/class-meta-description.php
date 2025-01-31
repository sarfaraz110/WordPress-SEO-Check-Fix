<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Meta_Description {
    public function __construct() {
        add_action('admin_init', array($this, 'analyze_meta_description'));
    }

    public function analyze_meta_description($post_id) {
        $meta_description = get_post_meta($post_id, '_sums_meta_description', true);
        if (empty($meta_description)) {
            return array(
                'status'  => 'warning',
                'message' => __('Meta description is missing.', 'sums-solution'),
            );
        }

        // Check description length
        $description_length = strlen($meta_description);
        if ($description_length < 120 || $description_length > 160) {
            return array(
                'status'  => 'warning',
                'message' => __('Meta description should be between 120 and 160 characters.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'success',
            'message' => __('Meta description is optimized.', 'sums-solution'),
        );
    }
}

new Sums_Meta_Description();