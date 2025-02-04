<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_WooCommerce {
    public function __construct() {
        add_action('sums_seo_scan_post', array($this, 'scan_product'));
    }

    /**
     * Scan a WooCommerce product for SEO issues.
     */
    public function scan_product($post_id) {
        if ('product' !== get_post_type($post_id)) {
            return;
        }

        $product = wc_get_product($post_id);

        if (!$product) {
            return;
        }

        $issues = array();

        // Check for missing product description
        if (empty($product->get_short_description())) {
            $issues[] = 'Product description is missing.';
        }

        // Check for missing product image
        if (!$product->get_image_id()) {
            $issues[] = 'Product image is missing.';
        }

        // Save scan results
        if (!empty($issues)) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'auto_sums';

            $wpdb->insert($table_name, array(
                'post_id' => $post_id,
                'scan_date' => current_time('mysql'),
                'issues_found' => count($issues),
                'issues_solved' => 0,
                'content' => implode(', ', $issues)
            ));
        }
    }
}

class Sums_SEO_WooCommerce_Schema_UI {
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_schema_fields'));
        add_action('woocommerce_process_product_meta', array($this, 'save_schema_fields'));
    }

    /**
     * Add schema fields to the WooCommerce product edit screen.
     */
    public function add_schema_fields() {
        echo '<div class="options_group">';
        woocommerce_wp_text_input(array(
            'id' => '_sums_schema_brand',
            'label' => __('Brand', 'sums-solution'),
            'desc_tip' => true,
            'description' => __('Enter the brand name for schema markup.', 'sums-solution')
        ));
        echo '</div>';
    }

    /**
     * Save schema fields.
     */
    public function save_schema_fields($post_id) {
        $brand = sanitize_text_field($_POST['_sums_schema_brand'] ?? '');
        update_post_meta($post_id, '_sums_schema_brand', $brand);
    }
}

// Initialize the classes
new Sums_SEO_WooCommerce();
new Sums_SEO_WooCommerce_Schema_UI();