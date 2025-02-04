<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Database {
    public static function create_database_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_sums';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            ID bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            scan_date datetime NOT NULL,
            issues_found int(11) NOT NULL,
            issues_solved int(11) NOT NULL,
            focus_keyword varchar(255) DEFAULT '',
            meta_description text,
            slug varchar(255) DEFAULT '',
            api_status varchar(50) DEFAULT '',
            content longtext,
            PRIMARY KEY (ID),
            INDEX post_id_index (post_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}