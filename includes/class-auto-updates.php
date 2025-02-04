<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Auto_Updates {
    public function __construct() {
        add_filter('auto_update_plugin', array($this, 'enable_auto_updates'), 10, 2);
    }

    /**
     * Enable auto-updates for the plugin.
     */
    public function enable_auto_updates($update, $item) {
        if ($item->plugin === plugin_basename(__FILE__)) {
            return true;
        }
        return $update;
    }
}