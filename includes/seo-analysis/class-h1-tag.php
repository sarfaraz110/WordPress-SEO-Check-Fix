<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_H1_Tag {
    public function __construct() {
        add_action('admin_init', array($this, 'analyze_h1_tag'));
    }

    public function analyze_h1_tag($post_id) {
        $post_content = get_post_field('post_content', $post_id);
        preg_match_all('/<h1.*?>(.*?)<\/h1>/i', $post_content, $h1_tags);

        if (empty($h1_tags[0])) {
            return array(
                'status'  => 'warning',
                'message' => __('No H1 tag found in the content.', 'sums-solution'),
            );
        }

        if (count($h1_tags[0]) > 1) {
            return array(
                'status'  => 'warning',
                'message' => __('Multiple H1 tags found. Use only one H1 tag per page.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'success',
            'message' => __('H1 tag is optimized.', 'sums-solution'),
        );
    }
}

new Sums_H1_Tag();