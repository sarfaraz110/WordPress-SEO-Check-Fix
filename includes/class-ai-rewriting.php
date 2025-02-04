<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_AI_Rewriting {
    private $api_keys;

    public function __construct() {
        $this->api_keys = get_option('sums_seo_api_keys', array());
        add_action('wp_ajax_sums_seo_rewrite_content', array($this, 'rewrite_content'));
    }

    /**
     * Rewrite content using AI.
     */
    public function rewrite_content() {
        if (!isset($_POST['content']) || !current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }

        $content = sanitize_textarea_field($_POST['content']);
        $api_key = $this->api_keys['ai_rewriting_api'] ?? '';

        if (!$this->is_api_key_valid($api_key)) {
            wp_send_json_error(array('message' => 'API key is missing or invalid.'));
        }

        // Example API request for AI rewriting
        $response = wp_remote_post('https://api.ai-rewriting.com/v1/rewrite', array(
            'body' => array(
                'content' => $content,
                'api_key' => $api_key
            )
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => 'An error occurred while rewriting the content.'));
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($body['rewritten_content'])) {
            wp_send_json_error(array('message' => 'No rewritten content found.'));
        }

        wp_send_json_success(array(
            'message' => 'Content rewritten successfully.',
            'rewritten_content' => $body['rewritten_content']
        ));
    }

    /**
     * Check if an API key is valid.
     */
    private function is_api_key_valid($api_key) {
        return !empty($api_key);
    }
}