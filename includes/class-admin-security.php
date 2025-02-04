<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Admin_Security {
    public function __construct() {
        add_action('admin_init', array($this, 'add_security_headers'));
    }

    /**
     * Add security headers to admin pages.
     */
    public function add_security_headers() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'sums-seo') === 0) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
        }
    }
}