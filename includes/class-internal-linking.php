<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Internal_Linking {
    public function __construct() {
        add_action('sums_seo_scan_post', array($this, 'suggest_internal_links'));
    }

    /**
     * Suggest internal links for a post.
     */
    public function suggest_internal_links($post_id) {
        $post = get_post($post_id);
        $content = $post->post_content;

        // Get all posts for internal linking suggestions
        $args = array(
            'post_type' => array('post', 'page'),
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'exclude' => array($post_id)
        );

        $posts = get_posts($args);
        $suggestions = array();

        foreach ($posts as $suggested_post) {
            $keywords = explode(' ', $suggested_post->post_title);
            foreach ($keywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    $suggestions[] = array(
                        'post_id' => $suggested_post->ID,
                        'title' => $suggested_post->post_title,
                        'url' => get_permalink($suggested_post->ID)
                    );
                    break;
                }
            }
        }

        if (!empty($suggestions)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'auto_sums';

            $wpdb->insert($table_name, array(
                'post_id' => $post_id,
                'scan_date' => current_time('mysql'),
                'issues_found' => count($suggestions),
                'issues_solved' => 0,
                'content' => __('Internal linking suggestions found.', 'sums-solution')
            ));

            // Store suggestions in post meta
            update_post_meta($post_id, '_sums_internal_links', $suggestions);
        }
    }
}