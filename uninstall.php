<?php
// If uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Clean up plugin data
// Example: Delete plugin options
delete_option('sums_seo_option_name');