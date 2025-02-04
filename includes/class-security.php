<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Security {
    public function __construct() {
        add_action('init', array($this, 'block_suspicious_user_agents'));
        add_action('sums_seo_daily_scan', array($this, 'monitor_spammy_backlinks'));
        add_filter('wp_headers', array($this, 'add_security_headers'));
        add_action('admin_init', array($this, 'verify_nonce'));
    }

    /**
     * Block suspicious user agents.
     */
    public function block_suspicious_user_agents() {
        $suspicious_agents = array('badbot', 'spambot', 'evilbot');
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        foreach ($suspicious_agents as $agent) {
            if (stripos($user_agent, $agent) !== false) {
                wp_die(__('Access denied.', 'sums-solution'), 403);
            }
        }
    }

    /**
     * Monitor for spammy backlinks.
     */
    public function monitor_spammy_backlinks() {
        // Placeholder for backlink monitoring logic
        // You can integrate with external APIs like Ahrefs or SEMrush
    }

    /**
     * Add security headers to HTTP responses.
     */
    public function add_security_headers($headers) {
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'SAMEORIGIN';
        $headers['X-XSS-Protection'] = '1; mode=block';
        return $headers;
    }

    /**
     * Verify nonce for admin requests.
     */
    public function verify_nonce() {
        if (isset($_POST['sums_seo_nonce']) && !wp_verify_nonce($_POST['sums_seo_nonce'], 'sums_seo_action')) {
            wp_die(__('Security check failed.', 'sums-solution'), 403);
        }
    }
}