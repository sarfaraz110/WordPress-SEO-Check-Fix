<div class="wrap">
    <h1><?php _e('Sums SEO Dashboard', 'sums-solution'); ?></h1>
    <div id="sums-seo-dashboard">
        <button id="sums-scan-all-posts"><?php _e('Scan All Posts', 'sums-solution'); ?></button>

        <h2><?php _e('Premium Keyword Suggestions', 'sums-solution'); ?></h2>
        <div id="sums-keyword-suggestions">
            <?php
            $api_integration = new Sums_SEO_API_Integration();
            $suggestions = $api_integration->get_keyword_suggestions('SEO');

            if (!empty($suggestions)) {
                echo '<ul>';
                foreach ($suggestions as $suggestion) {
                    echo '<li>' . esc_html($suggestion) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . __('No keyword suggestions found.', 'sums-solution') . '</p>';
            }
            ?>
        </div>

        <h2><?php _e('Competitor Analysis', 'sums-solution'); ?></h2>
        <div id="sums-competitor-analysis">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'auto_sums';
            $results = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = 0 ORDER BY scan_date DESC");

            if (!empty($results)) {
                echo '<ul>';
                foreach ($results as $result) {
                    echo '<li>' . esc_html($result->content) . ' (' . esc_html($result->scan_date) . ')</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . __('No competitor analysis data found.', 'sums-solution') . '</p>';
            }
            ?>
        </div>
    </div>
</div>