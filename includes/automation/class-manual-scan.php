<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Manual_Scan {
    public function __construct() {
        add_action('wp_ajax_sums_manual_scan', array($this, 'handle_manual_scan'));
    }

    public function handle_manual_scan() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have sufficient permissions to access this page.', 'sums-solution'));
        }

        $model = get_option('sums_openai_model', 'gpt-4');
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();

                // Re-analyze SEO for each post
                (new Sums_SEO_Title())->analyze_seo_title($post_id);
                (new Sums_Meta_Description())->analyze_meta_description($post_id);
                (new Sums_Focus_Keyphrase())->analyze_focus_keyphrase($post_id);
                (new Sums_H1_Tag())->analyze_h1_tag($post_id);
                (new Sums_Indexability_Check())->check_indexability($post_id);

                // Get AI suggestions using the selected model
                $ai_suggestions = (new Sums_AI_Suggestions())->check_ai_suggestions($post_id);
                if ($ai_suggestions['status'] === 'success') {
                    update_post_meta($post_id, '_sums_ai_suggestions', $ai_suggestions['message']);
                }
            }
        }

        wp_send_json_success(__('Manual scan completed successfully using ' . esc_html($model) . '.', 'sums-solution'));
    }
}

new Sums_Manual_Scan();