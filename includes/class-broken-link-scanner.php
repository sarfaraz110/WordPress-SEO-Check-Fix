<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Broken_Link_Scanner {
    public function __construct() {
        add_action('sums_seo_daily_scan', array($this, 'scan_for_broken_links'));
    }

    /**
     * Scan for broken links in post content.
     */
    public function scan_for_broken_links() {
        $args = array(
            'post_type' => array('post', 'page'),
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts($args);

        foreach ($posts as $post) {
            $this->check_links_in_post($post);
        }
    }

    /**
     * Check links in a post's content.
     */
    private function check_links_in_post($post) {
        preg_match_all('/<a[^>]+href=([\'"])(?<href>.+?)\1[^>]*>/i', $post->post_content, $matches);

        if (empty($matches['href'])) {
            return;
        }

        foreach ($matches['href'] as $url) {
            $response = wp_remote_head($url);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                $this->log_broken_link($post->ID, $url);
            }
        }
    }

    /**
     * Log broken links in the database.
     */
    private function log_broken_link($post_id, $url) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_sums';

        $wpdb->insert($table_name, array(
            'post_id' => $post_id,
            'scan_date' => current_time('mysql'),
            'issues_found' => 1,
            'issues_solved' => 0,
            'content' => sprintf(__('Broken link found: %s', 'sums-solution'), $url)
        ));
    }
}