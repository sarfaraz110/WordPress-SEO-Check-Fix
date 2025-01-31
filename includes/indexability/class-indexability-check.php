<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Indexability_Check {
    public function __construct() {
        add_action('admin_init', array($this, 'check_indexability'));
    }

    public function check_indexability($post_id) {
        $indexability_issues = array();

        // Check if the post is set to noindex
        $noindex = get_post_meta($post_id, '_sums_noindex', true);
        if ($noindex) {
            $indexability_issues[] = __('This post is set to noindex and will not be indexed by search engines.', 'sums-solution');
        }

        // Check for canonical tag
        $canonical_url = get_post_meta($post_id, '_sums_canonical_url', true);
        if (empty($canonical_url)) {
            $indexability_issues[] = __('Canonical URL is missing.', 'sums-solution');
        }

        // Check robots.txt directives
        $robots_txt = $this->check_robots_txt();
        if (!empty($robots_txt)) {
            $indexability_issues[] = $robots_txt;
        }

        if (empty($indexability_issues)) {
            return array(
                'status'  => 'success',
                'message' => __('This post is indexable by search engines.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'warning',
            'message' => implode('<br>', $indexability_issues),
        );
    }

    private function check_robots_txt() {
        $robots_file = ABSPATH . 'robots.txt';
        if (!file_exists($robots_file)) {
            return __('robots.txt file is missing.', 'sums-solution');
        }

        $robots_content = file_get_contents($robots_file);
        if (strpos($robots_content, 'Disallow: /') !== false) {
            return __('robots.txt contains restrictive directives.', 'sums-solution');
        }

        return '';
    }
}

new Sums_Indexability_Check();