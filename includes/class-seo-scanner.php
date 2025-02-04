<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Scanner {
    private $api_integration;

    public function __construct() {
        $this->api_integration = new Sums_SEO_API_Integration();
        add_action('wp_ajax_sums_seo_scan_post', array($this, 'scan_post'));
    }

    /**
     * Scan a post for SEO issues.
     */
    public function scan_post() {
        if (!isset($_POST['post_id']) || !current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Invalid request.'));
        }

        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if (!$post) {
            wp_send_json_error(array('message' => 'Post not found.'));
        }

        // Perform SEO checks
        $issues_found = $this->check_seo_issues($post);
        $issues_solved = 0; // Placeholder for solved issues

        // Save scan results to the database
        $this->save_scan_results($post_id, $issues_found, $issues_solved);

        wp_send_json_success(array(
            'message' => 'Scan completed.',
            'issues_found' => $issues_found,
            'issues_solved' => $issues_solved
        ));
    }

    /**
     * Check for SEO issues in a post.
     */
    private function check_seo_issues($post) {
        $issues = array();

        // Check for duplicate content
        if ($this->api_integration->detect_duplicate_content($post->post_content)) {
            $issues[] = 'Duplicate content detected.';
        }

        // Check for grammar issues
        $grammar_issues = $this->api_integration->check_grammar($post->post_content);
        if (!empty($grammar_issues)) {
            $issues[] = 'Grammar issues detected.';
        }

        // Check for missing focus keyword
        $focus_keyword = get_post_meta($post->ID, '_sums_focus_keyword', true);
        if (empty($focus_keyword)) {
            $issues[] = 'Focus keyword is missing.';
        }

        // Check for missing meta description
        $meta_description = get_post_meta($post->ID, '_sums_meta_description', true);
        if (empty($meta_description)) {
            $issues[] = 'Meta description is missing.';
        }

        // Check for missing H1 tag
        if (!preg_match('/<h1.*?>(.*?)<\/h1>/i', $post->post_content)) {
            $issues[] = 'H1 tag is missing.';
        }

        return $issues;
    }

    /**
     * Save scan results to the database.
     */
    private function save_scan_results($post_id, $issues_found, $issues_solved) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_sums';

        // Delete old records for this post
        $wpdb->delete($table_name, array('post_id' => $post_id));

        // Insert new scan results
        $wpdb->insert($table_name, array(
            'post_id' => $post_id,
            'scan_date' => current_time('mysql'),
            'issues_found' => count($issues_found),
            'issues_solved' => $issues_solved,
            'focus_keyword' => get_post_meta($post_id, '_sums_focus_keyword', true),
            'meta_description' => get_post_meta($post_id, '_sums_meta_description', true),
            'slug' => get_post_field('post_name', $post_id),
            'api_status' => 'Active', // Placeholder for API status
            'content' => $post->post_content // Store limited content if needed
        ));
    }
}