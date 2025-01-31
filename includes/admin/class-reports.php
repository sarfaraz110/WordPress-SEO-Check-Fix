<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Reports {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_reports_page'));
    }

    public function add_reports_page() {
        add_submenu_page(
            'sums-seo-dashboard', // Parent menu slug
            'SEO Reports',        // Page title
            'SEO Reports',        // Menu title
            'manage_options',     // Capability
            'sums-seo-reports',   // Menu slug
            array($this, 'render_reports_page') // Callback function
        );
    }

    // Render SEO Reports Page
    public function render_reports_page() {
        // Pagination settings
        $posts_per_page = 10;
        $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        
        // Query posts
        $query_args = array(
            'post_type'      => 'post',
            'posts_per_page' => $posts_per_page,
            'paged'          => $current_page,
        );
        $query = new WP_Query($query_args);

        // Generate SEO report
        $report = $this->generate_seo_report();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('SEO Reports', 'sums-solution') . '</h1>';

        echo '<div class="sums-seo-report">';
        echo '<h2>' . esc_html__('Site-Wide SEO Report', 'sums-solution') . '</h2>';
        echo '<p>' . sprintf(__('Total Posts: %d | Issues Found: %d', 'sums-solution'), $report['total_posts'], $report['issues_found']) . '</p>';

         // Render the SEO analysis table
        $this->render_seo_analysis_table($query, $posts_per_page, $current_page);
        
        echo '</div>';
        echo '</div>';
    }

    // SEO Analysis Table
    // SEO Analysis Table
private function render_seo_analysis_table($query, $posts_per_page, $current_page) {
    if (!$query instanceof WP_Query) {
        echo '<p>' . esc_html__('Invalid query object.', 'sums-solution') . '</p>';
        return;
    }
    
    $total_posts = $query->found_posts;
    $issues_found = 0;

    if ($query->have_posts()) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . esc_html__('Post Title', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('SEO Title', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('Meta Description', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('Focus Keyphrase', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('H1 Tag', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('Indexability', 'sums-solution') . '</th>';
        echo '<th>' . esc_html__('Actions', 'sums-solution') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            $seo_title_analysis = class_exists('Sums_SEO_Title') ? (new Sums_SEO_Title())->analyze_seo_title($post_id) : ['status' => 'error', 'message' => 'N/A'];
            $meta_description_analysis = class_exists('Sums_Meta_Description') ? (new Sums_Meta_Description())->analyze_meta_description($post_id) : ['status' => 'error', 'message' => 'N/A'];
            $focus_keyphrase_analysis = class_exists('Sums_Focus_Keyphrase') ? (new Sums_Focus_Keyphrase())->analyze_focus_keyphrase($post_id) : ['status' => 'error', 'message' => 'N/A'];
            $h1_tag_analysis = class_exists('Sums_H1_Tag') ? (new Sums_H1_Tag())->analyze_h1_tag($post_id) : ['status' => 'error', 'message' => 'N/A'];
            $indexability_check = class_exists('Sums_Indexability_Check') ? (new Sums_Indexability_Check())->check_indexability($post_id) : ['status' => 'error', 'message' => 'N/A'];

            // Count issues
            if ($seo_title_analysis['status'] === 'warning' ||
                $meta_description_analysis['status'] === 'warning' ||
                $focus_keyphrase_analysis['status'] === 'warning' ||
                $h1_tag_analysis['status'] === 'warning' ||
                $indexability_check['status'] === 'warning') {
                $issues_found++;
                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . esc_html($seo_title_analysis['message']) . '</td>';
                echo '<td>' . esc_html($meta_description_analysis['message']) . '</td>';
                echo '<td>' . esc_html($focus_keyphrase_analysis['message']) . '</td>';
                echo '<td>' . esc_html($h1_tag_analysis['message']) . '</td>';
                echo '<td>' . wp_kses_post($indexability_check['message']) . '</td>';
                echo '<td>
                    <button class="button button-primary sums-auto-fix-button" data-post-id="' . esc_attr($post_id) . '">
                        <span class="sums-button-text">' . esc_html__('Auto Fix', 'sums-solution') . '</span>
                        <span class="sums-loading" style="display: none;">&#8635;</span>
                    </button>
                    <button class="button sums-openai-fix-button" data-post-id="' . esc_attr($post_id) . '">
                        <span class="sums-button-text">' . esc_html__('Fix with OpenAI', 'sums-solution') . '</span>
                        <span class="sums-loading" style="display: none;">&#8635;</span>
                    </button>
                </td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';
        
        // Render pagination
        if (class_exists('Sums_Pagination')) {
            Sums_Pagination::render($total_posts, $posts_per_page, $current_page);
        }
    } else {
        echo '<p>' . esc_html__('No posts found.', 'sums-solution') . '</p>';
    }
}


    // Generate SEO Report
    public function generate_seo_report() {
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => -1,
        );

        $query = new WP_Query($args);
        $total_posts = $query->found_posts;
        $issues_found = 0;
        $report_data = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $seo_title_analysis = (new Sums_SEO_Title())->analyze_seo_title($post_id);
                $meta_description_analysis = (new Sums_Meta_Description())->analyze_meta_description($post_id);
                $focus_keyphrase_analysis = (new Sums_Focus_Keyphrase())->analyze_focus_keyphrase($post_id);
                $h1_tag_analysis = (new Sums_H1_Tag())->analyze_h1_tag($post_id);
                $indexability_check = (new Sums_Indexability_Check())->check_indexability($post_id);

                // Count issues
                if ($seo_title_analysis['status'] === 'warning' ||
                    $meta_description_analysis['status'] === 'warning' ||
                    $focus_keyphrase_analysis['status'] === 'warning' ||
                    $h1_tag_analysis['status'] === 'warning' ||
                    $indexability_check['status'] === 'warning') {
                    $issues_found++;
                }

                $report_data[] = array(
                    'post_title'       => get_the_title(),
                    'seo_title'        => $seo_title_analysis['message'],
                    'meta_description' => $meta_description_analysis['message'],
                    'focus_keyphrase'  => $focus_keyphrase_analysis['message'],
                    'h1_tag'           => $h1_tag_analysis['message'],
                    'indexability'     => $indexability_check['message'],
                );
            }
        }

        return array(
            'total_posts'  => $total_posts,
            'issues_found' => $issues_found,
            'report_data'  => $report_data,
        );
    }
}

// Initialize Class
add_action('plugins_loaded', function () {
    new Sums_Reports();
});