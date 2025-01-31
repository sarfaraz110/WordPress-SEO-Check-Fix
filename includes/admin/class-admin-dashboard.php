<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Admin_Dashboard {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Sums SEO Check Fix', // Page title
            'Sums SEO',           // Menu title
            'manage_options',     // Capability
            'sums-seo-dashboard', // Menu slug
            array($this, 'render_dashboard'), // Callback function
            'dashicons-chart-bar', // Icon
            6                      // Position
        );
    }

    public function render_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('All SEO Reports', 'sums-solution') . '</h1>';
    
        // Manual Scan Button
        echo '<div class="sums-manual-scan">';
        echo '<button id="sums-manual-scan-button" class="button button-primary">' . esc_html__('Manual Scan', 'sums-solution') . '</button>';
        echo '</div>';

        // Initialize Chart.js
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var ctx = document.getElementById("seoChart").getContext("2d");
                var seoChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: ["SEO Title", "Meta Description", "Focus Keyphrase", "H1 Tag", "Indexability"],
                        datasets: [{
                            label: "SEO Analysis",
                            data: [12, 19, 3, 5, 2],
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            borderColor: "rgba(75, 192, 192, 1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>';
    
        // Chart Display Section
        echo '<div class="chart-container">';
        echo '<canvas id="seoChart"></canvas>';  // Chart will be rendered here
        echo '</div>';
        
        // Render the SEO analysis table
        $this->render_seo_analysis_table();
        
        echo '</div>';
    }
    

    public function render_seo_analysis_table() {
        $posts_per_page = isset($_GET['posts_per_page']) ? intval($_GET['posts_per_page']) : 10;
        $current_page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $offset = ($current_page - 1) * $posts_per_page;
    
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => $posts_per_page,
            'offset'         => $offset,
        );
    
        $query = new WP_Query($args);
    
        // Initialize variables
        $total_posts = $query->found_posts;  // Get total posts count
        $issues_found = 0;  // Initialize issues count
    
        echo '<div class="wrap">';
    
        // Filters for rows per page
        echo '<div class="sums-filters">';
        echo '<form method="get" action="">';
        echo '<input type="hidden" name="page" value="sums-seo-reports">';
        echo '<label for="posts_per_page">' . esc_html__('Rows per page:', 'sums-solution') . '</label>';
        echo '<select name="posts_per_page" id="posts_per_page">';
        echo '<option value="10"' . selected($posts_per_page, 10, false) . '>10</option>';
        echo '<option value="25"' . selected($posts_per_page, 25, false) . '>25</option>';
        echo '<option value="50"' . selected($posts_per_page, 50, false) . '>50</option>';
        echo '<option value="100"' . selected($posts_per_page, 100, false) . '>100</option>';
        echo '<option value="200"' . selected($posts_per_page, 200, false) . '>200</option>';
        echo '<option value="300"' . selected($posts_per_page, 300, false) . '>300</option>';
        echo '<option value="500"' . selected($posts_per_page, 500, false) . '>500</option>';
        echo '</select>';
        echo '<input type="submit" class="button" value="' . esc_attr__('Apply', 'sums-solution') . '">';
        echo '</form>';
        echo '</div>';
    
        if ($query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>' . esc_html__('Post Title', 'sums-solution') . '</th>';
            echo '<th>' . esc_html__('SEO Title', 'sums-solution') . '</th>';
            echo '<th>' . esc_html__('Meta Description', 'sums-solution') . '</th>';
            echo '<th>' . esc_html__('Focus Keyphrase', 'sums-solution') . '</th>';
            echo '<th>' . esc_html__('H1 Tag', 'sums-solution') . '</th>';
            echo '<th>' . esc_html__('Indexability', 'sums-solution') . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
    
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
    
                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . esc_html($seo_title_analysis['message']) . '</td>';
                echo '<td>' . esc_html($meta_description_analysis['message']) . '</td>';
                echo '<td>' . esc_html($focus_keyphrase_analysis['message']) . '</td>';
                echo '<td>' . esc_html($h1_tag_analysis['message']) . '</td>';
                echo '<td>' . wp_kses_post($indexability_check['message']) . '</td>';
                echo '</tr>';
            }
    
            echo '</tbody></table>';
            echo '<p>' . sprintf(__('Total Posts: %d | Issues Found: %d', 'sums-solution'), $total_posts, $issues_found) . '</p>';
    
            // Render pagination
            Sums_Pagination::render($total_posts, $posts_per_page, $current_page);
        } else {
            echo '<p>' . esc_html__('No posts found.', 'sums-solution') . '</p>';
        }
    
        echo '</div>';
    }
    
}

new Sums_Admin_Dashboard();