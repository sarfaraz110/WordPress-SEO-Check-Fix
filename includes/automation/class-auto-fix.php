<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Auto_Fix {
    public function __construct() {
        add_action('wp_ajax_sums_auto_fix', array($this, 'handle_auto_fix'));
    }

    public function handle_auto_fix() {
        if (!isset($_POST['post_id']) || !current_user_can('edit_posts')) {
            wp_send_json_error(__('Invalid request or insufficient permissions.', 'sums-solution'));
        }

        $post_id = intval($_POST['post_id']);
        $results = array();

        // Fix SEO Title
        $results[] = $this->fix_seo_title($post_id);

        // Fix Meta Description
        $results[] = $this->fix_meta_description($post_id);

        // Fix Focus Keyphrase
        $results[] = $this->fix_focus_keyphrase($post_id);

        // Fix H1 Tag
        $results[] = $this->fix_h1_tag($post_id);

        // Prepare success message
        $message = __('SEO issues fixed successfully:', 'sums-solution') . '<br>';
        foreach ($results as $result) {
            $message .= '- ' . $result . '<br>';
        }

        wp_send_json_success($message);
    }

    private function fix_seo_title($post_id) {
        $seo_title = get_post_meta($post_id, '_sums_seo_title', true);
        if (empty($seo_title)) {
            $post = get_post($post_id);
            if ($post) {
                update_post_meta($post_id, '_sums_seo_title', sanitize_text_field($post->post_title));
                return __('SEO title fixed.', 'sums-solution');
            }
        }
        return __('SEO title already optimized.', 'sums-solution');
    }

    private function fix_meta_description($post_id) {
        $meta_description = get_post_meta($post_id, '_sums_meta_description', true);
        if (empty($meta_description)) {
            $post = get_post($post_id);
            if ($post) {
                $excerpt = wp_trim_words(strip_tags($post->post_content), 25);
                update_post_meta($post_id, '_sums_meta_description', sanitize_text_field($excerpt));
                return __('Meta description fixed.', 'sums-solution');
            }
        }
        return __('Meta description already optimized.', 'sums-solution');
    }

    private function fix_focus_keyphrase($post_id) {
        $focus_keyphrase = get_post_meta($post_id, '_sums_focus_keyphrase', true);
        if (empty($focus_keyphrase)) {
            $post = get_post($post_id);
            if ($post) {
                $keywords = implode(', ', array_slice(explode(' ', sanitize_text_field($post->post_title)), 0, 3));
                update_post_meta($post_id, '_sums_focus_keyphrase', $keywords);
                return __('Focus keyphrase fixed.', 'sums-solution');
            }
        }
        return __('Focus keyphrase already optimized.', 'sums-solution');
    }

    private function fix_h1_tag($post_id) {
        $post_content = get_post_field('post_content', $post_id);
        if (!preg_match('/<h1.*?>.*?<\/h1>/i', $post_content)) {
            $post = get_post($post_id);
            if ($post) {
                $updated_content = '<h1>' . esc_html($post->post_title) . '</h1>' . $post_content;
                wp_update_post(array(
                    'ID'           => $post_id,
                    'post_content' => $updated_content,
                ));
                return __('H1 tag fixed.', 'sums-solution');
            }
        }
        return __('H1 tag already optimized.', 'sums-solution');
    }
}

// Initialize Class
add_action('plugins_loaded', function () {
    new Sums_Auto_Fix();
});
