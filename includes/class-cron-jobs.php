<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Cron_Jobs {
    public function __construct() {
        add_action('sums_seo_daily_scan', array($this, 'run_daily_scan'));
        $this->schedule_cron_jobs();
    }

    /**
     * Schedule daily scans.
     */
    private function schedule_cron_jobs() {
        if (!wp_next_scheduled('sums_seo_daily_scan')) {
            wp_schedule_event(time(), 'daily', 'sums_seo_daily_scan');
        }
    }

    /**
     * Run daily SEO scans.
     */
    public function run_daily_scan() {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $posts = get_posts($args);

        if (empty($posts)) {
            return;
        }

        foreach ($posts as $post) {
            do_action('sums_seo_scan_post', $post->ID);
        }
    }

    /**
     * Clear scheduled tasks on deactivation.
     */
    public static function clear_scheduled_tasks() {
        wp_clear_scheduled_hook('sums_seo_daily_scan');
    }
}