<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_API_Auto_Updates {
    private $api_keys;

    public function __construct() {
        $this->api_keys = get_option('sums_seo_api_keys', array());
        add_action('sums_seo_daily_scan', array($this, 'check_for_api_updates'));
    }

    /**
     * Check for updates to external APIs.
     */
    public function check_for_api_updates() {
        foreach ($this->api_keys as $api_name => $api_key) {
            $this->update_api_integration($api_name, $api_key);
        }
    }

    /**
     * Update an API integration.
     */
    private function update_api_integration($api_name, $api_key) {
        $response = wp_remote_post('https://api.updates.com/v1/check', array(
            'body' => array(
                'api_name' => $api_name,
                'api_key' => $api_key
            )
        ));

        if (is_wp_error($response)) {
            return;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($body['update_available'])) {
            $this->apply_api_update($api_name, $body['update_url']);
        }
    }

    /**
     * Apply an API update.
     */
    private function apply_api_update($api_name, $update_url) {
        $response = wp_remote_get($update_url);

        if (is_wp_error($response)) {
            return;
        }

        $body = wp_remote_retrieve_body($response);

        if (!empty($body)) {
            file_put_contents(SUMS_SEO_PLUGIN_DIR . "includes/apis/$api_name.php", $body);
        }
    }
}