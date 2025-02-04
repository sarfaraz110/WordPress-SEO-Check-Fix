<?php
// WordPress database access
global $wpdb;
$table_name = $wpdb->prefix . 'auto_sums';

// Pagination setup
$per_page = 10;
$current_page = max(1, intval($_GET['paged'] ?? 1));
$offset = ($current_page - 1) * $per_page;

// Filtering
$filter_status = $_GET['filter-status'] ?? '';
$where_clause = '';

if ($filter_status === 'solved') {
    $where_clause = ' AND issues_solved > 0';
} elseif ($filter_status === 'unsolved') {
    $where_clause = ' AND issues_solved = 0';
}

// Fetch paginated results
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY scan_date DESC LIMIT %d OFFSET %d",
    $per_page,
    $offset
));

$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 $where_clause");
$total_pages = ceil($total_items / $per_page);

// Fetch chart data
$chart_results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY scan_date DESC LIMIT 10");
$issues_found = [];
$issues_solved = [];

foreach ($chart_results as $result) {
    $issues_found[] = $result->issues_found;
    $issues_solved[] = $result->issues_solved;
}
?>

<div class="wrap">
    <h1><?php _e('SEO Audit Reports', 'sums-solution'); ?></h1>
    
    <canvas id="sums-seo-chart" width="400" height="200"></canvas>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById("sums-seo-chart").getContext("2d");
            var chartData = {
                labels: Array.from({ length: <?php echo count($issues_found); ?> }, (_, i) => "Entry " + (i + 1)),
                datasets: [
                    {
                        label: "Issues Found",
                        data: <?php echo json_encode($issues_found); ?>,
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1
                    },
                    {
                        label: "Issues Solved",
                        data: <?php echo json_encode($issues_solved); ?>,
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1
                    }
                ]
            };
            new Chart(ctx, {
                type: "bar",
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        });
    </script>
    
    <form method="get" action="">
        <input type="hidden" name="page" value="sums-seo-reports">
        <label for="filter-status"><?php _e('Filter by Status:', 'sums-solution'); ?></label>
        <select name="filter-status" id="filter-status">
            <option value=""><?php _e('All', 'sums-solution'); ?></option>
            <option value="solved" <?php selected($filter_status, 'solved'); ?>><?php _e('Solved', 'sums-solution'); ?></option>
            <option value="unsolved" <?php selected($filter_status, 'unsolved'); ?>><?php _e('Unsolved', 'sums-solution'); ?></option>
        </select>
        <input type="submit" value="<?php _e('Filter', 'sums-solution'); ?>" class="button">
    </form>
    
    <?php if (!empty($results)) : ?>
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
                <?php foreach ($results as $result) : ?>
                    <tr>
                        <td><?php echo esc_html(get_the_title($result->post_id)); ?></td>
                        <td><?php echo esc_html($result->issues_found); ?></td>
                        <td><?php echo esc_html($result->issues_solved); ?></td>
                        <td><?php echo esc_html($result->scan_date); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tablenav">
            <div class="tablenav-pages">
                <?php echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; Previous', 'sums-solution'),
                    'next_text' => __('Next &raquo;', 'sums-solution'),
                    'total' => $total_pages,
                    'current' => $current_page
                ]); ?>
            </div>
        </div>
    <?php else : ?>
        <p><?php _e('No results found.', 'sums-solution'); ?></p>
    <?php endif; ?>
</div>
