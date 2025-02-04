<div class="wrap">
    <h1><?php _e('SEO Audit Reports', 'sums-solution'); ?></h1>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Post/Page', 'sums-solution'); ?></th>
                <th><?php _e('Issues Found', 'sums-solution'); ?></th>
                <th><?php _e('Issues Solved', 'sums-solution'); ?></th>
                <th><?php _e('Last Scanned', 'sums-solution'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'auto_sums';
            $chart_results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY scan_date DESC");

            foreach ($chart_results as $result) {
                echo '<tr>';
                echo '<td>' . get_the_title($result->post_id) . '</td>';
                echo '<td>' . esc_html($result->issues_found) . '</td>';
                echo '<td>' . esc_html($result->issues_solved) . '</td>';
                echo '<td>' . esc_html($result->scan_date) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<div class="wrap">
    <h1><?php _e('SEO Audit Reports', 'sums-solution'); ?></h1>

    <canvas id="sums-seo-chart" width="400" height="200"></canvas>
    <script type="application/json" id="sums-seo-chart-data">
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_sums';
        $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY scan_date DESC");

        $labels = array();
        $issues_found = array();
        $issues_solved = array();

        foreach ($chart_results as $result) {
            $labels[] = get_the_title($result->post_id);
            $issues_found[] = $result->issues_found;
            $issues_solved[] = $result->issues_solved;
        }

        echo json_encode(array(
            'labels' => $labels,
            'issues_found' => $issues_found,
            'issues_solved' => $issues_solved
        ));
        ?>
    </script>

    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('Post/Page', 'sums-solution'); ?></th>
                <th><?php _e('Issues Found', 'sums-solution'); ?></th>
                <th><?php _e('Issues Solved', 'sums-solution'); ?></th>
                <th><?php _e('Last Scanned', 'sums-solution'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($results as $result) {
                echo '<tr>';
                echo '<td>' . get_the_title($result->post_id) . '</td>';
                echo '<td>' . esc_html($result->issues_found) . '</td>';
                echo '<td>' . esc_html($result->issues_solved) . '</td>';
                echo '<td>' . esc_html($result->scan_date) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<div class="wrap">
    <h1><?php _e('SEO Audit Reports', 'sums-solution'); ?></h1>

    <form method="get" action="">
        <input type="hidden" name="page" value="sums-seo-reports">
        <label for="filter-status"><?php _e('Filter by Status:', 'sums-solution'); ?></label>
        <select name="filter-status" id="filter-status">
            <option value=""><?php _e('All', 'sums-solution'); ?></option>
            <option value="solved" <?php selected($_GET['filter-status'], 'solved'); ?>><?php _e('Solved', 'sums-solution'); ?></option>
            <option value="unsolved" <?php selected($_GET['filter-status'], 'unsolved'); ?>><?php _e('Unsolved', 'sums-solution'); ?></option>
        </select>
        <input type="submit" value="<?php _e('Filter', 'sums-solution'); ?>" class="button">
    </form>

    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'auto_sums';

    // Pagination
    $per_page = 10;
    $current_page = max(1, $_GET['paged'] ?? 1);
    $offset = ($current_page - 1) * $per_page;

    // Filtering
    $filter_status = $_GET['filter-status'] ?? '';
    $where_clause = '';

    if ($filter_status === 'solved') {
        $where_clause = ' AND issues_solved > 0';
    } elseif ($filter_status === 'unsolved') {
        $where_clause = ' AND issues_solved = 0';
    }

    // Fetch data
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY scan_date DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 $where_clause");
    $total_pages = ceil($total_items / $per_page);

    // Display results
    if (!empty($results)) {
        echo '<table class="widefat">';
        echo '<thead><tr><th>' . __('Post/Page', 'sums-solution') . '</th><th>' . __('Issues Found', 'sums-solution') . '</th><th>' . __('Issues Solved', 'sums-solution') . '</th><th>' . __('Last Scanned', 'sums-solution') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($results as $result) {
            echo '<tr>';
            echo '<td>' . get_the_title($result->post_id) . '</td>';
            echo '<td>' . esc_html($result->issues_found) . '</td>';
            echo '<td>' . esc_html($result->issues_solved) . '</td>';
            echo '<td>' . esc_html($result->scan_date) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Pagination links
        echo '<div class="tablenav">';
        echo '<div class="tablenav-pages">';
        echo paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo; Previous', 'sums-solution'),
            'next_text' => __('Next &raquo;', 'sums-solution'),
            'total' => $total_pages,
            'current' => $current_page
        ));
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>' . __('No results found.', 'sums-solution') . '</p>';
    }
    ?>
</div>