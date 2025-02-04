<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_AJAX_Handler {
    public function __construct() {
        add_action('wp_ajax_sums_seo_handle_request', array($this, 'handle_request'));
        add_action('wp_ajax_nopriv_sums_seo_handle_request', array($this, 'handle_request'));
    }

    /**
     * Handle AJAX requests.
     */
    public function handle_request() {
        if (!isset($_POST['action_type'])) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }

        $action_type = sanitize_text_field($_POST['action_type']);

        switch ($action_type) {
            case 'scan_all_posts':
                $this->scan_all_posts();
                break;
            default:
                wp_send_json_error(array('message' => 'Invalid action type.'));
        }
    }

    /**
     * Scan all posts for SEO issues.
     */
    private function scan_all_posts() {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts($args);

        if (empty($posts)) {
            wp_send_json_success(array('message' => 'No posts found.'));
        }

        foreach ($posts as $post) {
            do_action('sums_seo_scan_post', $post->ID);
        }

        wp_send_json_success(array('message' => 'Scanning all posts in the background.'));
    }
}