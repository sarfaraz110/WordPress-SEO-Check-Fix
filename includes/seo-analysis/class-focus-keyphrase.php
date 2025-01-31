<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Focus_Keyphrase {
    public function __construct() {
        add_action('admin_init', array($this, 'analyze_focus_keyphrase'));
    }

    public function analyze_focus_keyphrase($post_id) {
        $focus_keyphrase = get_post_meta($post_id, '_sums_focus_keyphrase', true);
        if (empty($focus_keyphrase)) {
            return array(
                'status'  => 'warning',
                'message' => __('Focus keyphrase is missing.', 'sums-solution'),
            );
        }

        $post_content = get_post_field('post_content', $post_id);
        $keyphrase_count = substr_count(strtolower($post_content), strtolower($focus_keyphrase));

        if ($keyphrase_count < 1) {
            return array(
                'status'  => 'warning',
                'message' => __('Focus keyphrase not found in the content.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'success',
            'message' => __('Focus keyphrase is optimized.', 'sums-solution'),
        );
    }
}

new Sums_Focus_Keyphrase();