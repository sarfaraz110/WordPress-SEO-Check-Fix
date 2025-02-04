<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Lazy_Loading {
    public function __construct() {
        add_filter('the_content', array($this, 'add_lazy_loading_to_images'));
    }

    /**
     * Add lazy loading to images in post content.
     */
    public function add_lazy_loading_to_images($content) {
        if (is_feed() || is_admin()) {
            return $content;
        }

        // Add loading="lazy" attribute to all <img> tags
        $content = preg_replace_callback('/<img([^>]*)>/i', function ($matches) {
            $img_tag = $matches[0];

            // Skip if the image already has a loading attribute
            if (strpos($img_tag, 'loading=') !== false) {
                return $img_tag;
            }

            // Add loading="lazy" attribute
            return str_replace('<img', '<img loading="lazy"', $img_tag);
        }, $content);

        return $content;
    }
}