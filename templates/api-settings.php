<div class="wrap">
    <h1><?php _e('API Settings', 'sums-solution'); ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('sums_seo_api_group');
        do_settings_sections('sums-seo-api-settings');
        wp_nonce_field('sums_seo_action', 'sums_seo_nonce');
        submit_button();
        ?>
    </form>

    <h2><?php _e('API Status', 'sums-solution'); ?></h2>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('API', 'sums-solution'); ?></th>
                <th><?php _e('Status', 'sums-solution'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php _e('Duplicate Content API', 'sums-solution'); ?></td>
                <td>
                    <?php
                    $api_key = get_option('sums_seo_api_keys')['duplicate_content_api'] ?? '';
                    if (!empty($api_key)) {
                        echo '<span style="color: green;">' . __('Connected', 'sums-solution') . '</span>';
                    } else {
                        echo '<span style="color: red;">' . __('Disconnected', 'sums-solution') . '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php _e('Grammar API', 'sums-solution'); ?></td>
                <td>
                    <?php
                    $api_key = get_option('sums_seo_api_keys')['grammar_api'] ?? '';
                    if (!empty($api_key)) {
                        echo '<span style="color: green;">' . __('Connected', 'sums-solution') . '</span>';
                    } else {
                        echo '<span style="color: red;">' . __('Disconnected', 'sums-solution') . '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><?php _e('Keyword API', 'sums-solution'); ?></td>
                <td>
                    <?php
                    $api_key = get_option('sums_seo_api_keys')['keyword_api'] ?? '';
                    if (!empty($api_key)) {
                        echo '<span style="color: green;">' . __('Connected', 'sums-solution') . '</span>';
                    } else {
                        echo '<span style="color: red;">' . __('Disconnected', 'sums-solution') . '</span>';
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>