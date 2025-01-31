<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Google_Updates {
    public function __construct() {
        add_action('admin_init', array($this, 'check_google_compliance'));
    }

    public function check_google_compliance($post_id) {
        $compliance_issues = array();

        // Check for HTTPS
        if (!is_ssl()) {
            $compliance_issues[] = __('Your site is not using HTTPS. Google prioritizes HTTPS websites.', 'sums-solution');
        }

        // Check for mobile-friendliness (basic check)
        if (!wp_is_mobile()) {
            $compliance_issues[] = __('Your site may not be mobile-friendly. Use Google’s Mobile-Friendly Test for a detailed analysis.', 'sums-solution');
        }

        if (empty($compliance_issues)) {
            return array(
                'status'  => 'success',
                'message' => __('Your site complies with Google’s latest algorithm updates.', 'sums-solution'),
            );
        }

        return array(
            'status'  => 'warning',
            'message' => implode('<br>', $compliance_issues),
        );
    }
}

new Sums_Google_Updates();