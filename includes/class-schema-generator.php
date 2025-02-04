<?php
if (!defined('ABSPATH')) {
    exit;
}

class Sums_SEO_Schema_Generator {
    /**
     * Generate schema markup for a post.
     */
    public function generate_post_schema($post) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->post_title,
            'description' => wp_strip_all_tags($post->post_excerpt),
            'datePublished' => get_the_date('c', $post->ID),
            'dateModified' => get_the_modified_date('c', $post->ID),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            )
        );

        return $schema;
    }

    /**
     * Generate schema markup for a WooCommerce product.
     */
    public function generate_product_schema($product) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->get_name(),
            'description' => wp_strip_all_tags($product->get_short_description()),
            'image' => wp_get_attachment_url($product->get_image_id()),
            'sku' => $product->get_sku(),
            'offers' => array(
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
            )
        );

        // Add reviews and ratings if available
        $average_rating = $product->get_average_rating();
        $review_count = $product->get_review_count();

        if ($average_rating > 0) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $average_rating,
                'reviewCount' => $review_count
            );
        }

        // Add brand if available
        $brand = $product->get_attribute('brand');
        if (!empty($brand)) {
            $schema['brand'] = array(
                '@type' => 'Brand',
                'name' => $brand
            );
        }

        return $schema;
    }

    /**
     * Output schema markup in the head section.
     */
    public function output_schema_markup() {
        if (is_singular('post')) {
            global $post;
            $schema = $this->generate_post_schema($post);
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        }

        if (is_singular('product')) {
            global $product;
            $schema = $this->generate_product_schema($product);
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        }
    }
}