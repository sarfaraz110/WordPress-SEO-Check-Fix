<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_OpenAI_Fix {
    private $api_key;

    public function __construct() {
        $this->api_key = get_option('sums_openai_api_key', '');
        add_action('wp_ajax_sums_openai_fix', array($this, 'handle_openai_fix'));
    }

    public function handle_openai_fix() {
        if (!isset($_POST['post_id']) || !current_user_can('edit_posts')) {
            wp_send_json_error(__('Invalid request or insufficient permissions.', 'sums-solution'));
        }
    
        $post_id = intval($_POST['post_id']);
        $results = array();
    
        // Fix SEO Title using OpenAI
        $seo_title = $this->fix_seo_title_with_openai($post_id);
        if ($seo_title) {
            $results[] = $seo_title;
        }
    
        // Fix Meta Description using OpenAI
        $meta_description = $this->fix_meta_description_with_openai($post_id);
        if ($meta_description) {
            $results[] = $meta_description;
        }
    
        // Fix Focus Keyphrase using OpenAI
        $focus_keyphrase = $this->fix_focus_keyphrase_with_openai($post_id);
        if ($focus_keyphrase) {
            $results[] = $focus_keyphrase;
        }
    
        // Fix H1 Tag using OpenAI
        $h1_tag = $this->fix_h1_tag_with_openai($post_id);
        if ($h1_tag) {
            $results[] = $h1_tag;
        }
    
        if (empty($results)) {
            wp_send_json_error(__('No fixes were applied.', 'sums-solution'));
        }
    
        // Prepare success message
        $message = __('SEO issues fixed successfully using OpenAI:', 'sums-solution') . '<br>';
        foreach ($results as $result) {
            $message .= '- ' . $result . '<br>';
        }
    
        wp_send_json_success($message);
    }

    private function fix_seo_title_with_openai($post_id) {
        $post = get_post($post_id);
        $response = $this->get_openai_suggestion('Write an optimized SEO title for: ' . $post->post_title);
        if ($response) {
            update_post_meta($post_id, '_sums_seo_title', $response);
            return __('SEO title fixed using OpenAI.', 'sums-solution');
        }
        return __('Failed to fix SEO title with OpenAI.', 'sums-solution');
    }

    private function fix_meta_description_with_openai($post_id) {
        $post = get_post($post_id);
        $response = $this->get_openai_suggestion('Write an optimized meta description for: ' . $post->post_content);
        if ($response) {
            update_post_meta($post_id, '_sums_meta_description', $response);
            return __('Meta description fixed using OpenAI.', 'sums-solution');
        }
        return __('Failed to fix meta description with OpenAI.', 'sums-solution');
    }

    private function fix_focus_keyphrase_with_openai($post_id) {
        $post = get_post($post_id);
        $response = $this->get_openai_suggestion('Suggest a focus keyphrase for: ' . $post->post_title);
        if ($response) {
            update_post_meta($post_id, '_sums_focus_keyphrase', $response);
            return __('Focus keyphrase fixed using OpenAI.', 'sums-solution');
        }
        return __('Failed to fix focus keyphrase with OpenAI.', 'sums-solution');
    }

    private function fix_h1_tag_with_openai($post_id) {
        $post = get_post($post_id);
        $response = $this->get_openai_suggestion('Write an optimized H1 tag for: ' . $post->post_title);
        if ($response) {
            $updated_content = '<h1>' . $response . '</h1>' . $post->post_content;
            wp_update_post(array(
                'ID'           => $post_id,
                'post_content' => $updated_content,
            ));
            return __('H1 tag fixed using OpenAI.', 'sums-solution');
        }
        return __('Failed to fix H1 tag with OpenAI.', 'sums-solution');
    }

    private function get_openai_suggestion($prompt) {
        if (empty($this->api_key)) {
            error_log('OpenAI API key is missing.');
            return false;
        }
    
        $url = 'https://api.openai.com/v1/chat/completions';
        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body' => json_encode(array(
                'model'      => get_option('sums_openai_model', 'gpt-4'),
                'prompt'     => $prompt,
                'max_tokens' => 100,
            )),
        );
    
        $response = wp_remote_post($url, $args);
        if (is_wp_error($response)) {
            error_log('OpenAI API request failed: ' . $response->get_error_message());
            return false;
        }
    
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($body['error'])) {
            error_log('OpenAI API error: ' . $body['error']['message']);
            return false;
        }
    
        if (!isset($body['choices'][0]['text'])) {
            error_log('OpenAI API response is invalid: ' . print_r($body, true));
            return false;
        }
    
        return $body['choices'][0]['text'];
    }
}

new Sums_OpenAI_Fix();