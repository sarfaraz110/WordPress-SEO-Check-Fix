<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Image_SEO {
    public function __construct() {
        add_action('add_attachment', array($this, 'optimize_image_on_upload'));
        add_action('sums_seo_scan_post', array($this, 'scan_images_in_post'));
    }

    /**
     * Optimize image on upload.
     */
    public function optimize_image_on_upload($attachment_id) {
        $alt_text = $this->generate_alt_text($attachment_id);
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);

        $this->optimize_image_name($attachment_id);
    }

    /**
     * Generate alt text for an image.
     */
    private function generate_alt_text($attachment_id) {
        $image_title = get_the_title($attachment_id);
        return sprintf(__('Image of %s', 'sums-solution'), $image_title);
    }

    /**
     * Optimize the image file name.
     */
    private function optimize_image_name($attachment_id) {
        $file_path = get_attached_file($attachment_id);
        $file_info = pathinfo($file_path);
        $new_file_name = sanitize_title($file_info['filename']) . '.' . $file_info['extension'];

        if ($new_file_name !== $file_info['basename']) {
            $new_file_path = $file_info['dirname'] . '/' . $new_file_name;
            rename($file_path, $new_file_path);
            update_attached_file($attachment_id, $new_file_path);
        }
    }

    /**
     * Scan images in a post for SEO issues.
     */
    public function scan_images_in_post($post_id) {
        $post = get_post($post_id);
        preg_match_all('/<img[^>]+>/i', $post->post_content, $matches);

        if (empty($matches[0])) {
            return;
        }

        $issues = array();

        foreach ($matches[0] as $img_tag) {
            if (!preg_match('/alt=["\']([^"\']+)["\']/i', $img_tag)) {
                $issues[] = 'Missing alt text in image.';
            }
        }

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