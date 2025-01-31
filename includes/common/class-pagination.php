<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Sums_Pagination {
    public static function render($total_posts, $posts_per_page, $current_page) {
        $total_pages = ceil($total_posts / $posts_per_page);
        if ($total_pages <= 1) {
            return;
        }

        echo '<div class="sums-pagination">';

        // Previous Button
        if ($current_page > 1) {
            echo '<a href="' . esc_url(add_query_arg('paged', $current_page - 1)) . '" class="button">' . __('Previous', 'sums-solution') . '</a>';
        }

        // Page Numbers
        echo '<div class="sums-pagination-numbers">';

        // Always show the first page
        if ($current_page > 2) {
            echo '<a href="' . esc_url(add_query_arg('paged', 1)) . '" class="button">1</a>';
            if ($current_page > 3) {
                echo '<span class="dots">...</span>';
            }
        }

        // Show pages around the current page
        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
            if ($i == $current_page) {
                echo '<span class="current">' . $i . '</span>';
            } else {
                echo '<a href="' . esc_url(add_query_arg('paged', $i)) . '" class="button">' . $i . '</a>';
            }
        }

        // Always show the last page
        if ($current_page < $total_pages - 1) {
            if ($current_page < $total_pages - 2) {
                echo '<span class="dots">...</span>';
            }
            echo '<a href="' . esc_url(add_query_arg('paged', $total_pages)) . '" class="button">' . $total_pages . '</a>';
        }

        echo '</div>';

        // Next Button
        if ($current_page < $total_pages) {
            echo '<a href="' . esc_url(add_query_arg('paged', $current_page + 1)) . '" class="button">' . __('Next', 'sums-solution') . '</a>';
        }

        echo '</div>';
    }
}