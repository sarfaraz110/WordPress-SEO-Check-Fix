<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Monthly_Reports {
    public function __construct() {
        add_action('sums_seo_monthly_report', array($this, 'generate_monthly_report'));
        $this->schedule_monthly_report();
    }

    /**
     * Schedule the monthly report.
     */
    private function schedule_monthly_report() {
        if (!wp_next_scheduled('sums_seo_monthly_report')) {
            wp_schedule_event(strtotime('first day of next month'), 'monthly', 'sums_seo_monthly_report');
        }
    }

    /**
     * Generate and email the monthly SEO report.
     */
    public function generate_monthly_report() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_sums';

        // Get data for the past month
        $start_date = date('Y-m-01 00:00:00', strtotime('last month'));
        $end_date = date('Y-m-t 23:59:59', strtotime('last month'));

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE scan_date BETWEEN %s AND %s",
            $start_date,
            $end_date
        ));

        if (empty($results)) {
            return;
        }

        // Prepare report content
        $report_content = '<h1>' . __('Monthly SEO Report', 'sums-solution') . '</h1>';
        $report_content .= '<p>' . __('Here is your monthly SEO performance summary:', 'sums-solution') . '</p>';
        $report_content .= '<table border="1" cellpadding="10" cellspacing="0">';
        $report_content .= '<tr><th>' . __('Post/Page', 'sums-solution') . '</th><th>' . __('Issues Found', 'sums-solution') . '</th><th>' . __('Issues Solved', 'sums-solution') . '</th></tr>';

        foreach ($results as $result) {
            $report_content .= '<tr>';
            $report_content .= '<td>' . get_the_title($result->post_id) . '</td>';
            $report_content .= '<td>' . esc_html($result->issues_found) . '</td>';
            $report_content .= '<td>' . esc_html($result->issues_solved) . '</td>';
            $report_content .= '</tr>';
        }

        $report_content .= '</table>';

        // Email the report
        $admin_email = get_option('admin_email');
        $subject = __('Monthly SEO Report', 'sums-solution');
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($admin_email, $subject, $report_content, $headers);
    }

    /**
     * Clear scheduled tasks on deactivation.
     */
    public static function clear_scheduled_tasks() {
        wp_clear_scheduled_hook('sums_seo_monthly_report');
    }
}