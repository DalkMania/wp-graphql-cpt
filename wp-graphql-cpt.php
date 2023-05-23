<?php
/**
 * Plugin Name: WP GraphQL Custom Post Types and Custom Taxonomies
 * Description: Exposes all registered Custom Post Types and Custom Taxonomies to the WPGraphQL EndPoint.
 * Author: Niklas Dahlqvist
 * Author URI: https://www.niklasdahlqvist.com
 * Version: 0.7
 * License: GPL2+
 */

namespace WPGraphQL\Extensions;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\WPGraphQL\Extensions\CPT')) {
    class CPT
    {
        public function __construct()
        {
            // Actions

            // Filters
            add_filter('register_post_type_args', [$this, 'filterPostTypes'], 10, 2);
            add_filter('register_taxonomy_args', [$this, 'filterTaxonomies'], 10, 2);
        }

        public function filterPostTypes($args, $post_type)
        {
            $graphQLArgs = [];
            $wp_default_post_types = [
                'post',
                'page',
                'attachment',
                'revision',
                'nav_menu_item',
                'custom_css',
                'customize_changeset',
                'oembed_cache',
                'user_request',
                'wp_block',
                'wp_template',
                'wp_template_part',
                'wp_global_styles',
                'wp_navigation',
                // Exclude ACF Field Groups
                'acf-field-group',
                // Exclude WooCommerce products
                'product',
                'product_variation',
                'shop_coupon',
                'shop_order',
                'shop_order_refund',
                'shop_order_placehold'
            ];

            $wp_default_post_types = apply_filters( 'graphql_cpt_excluded_post_types', $wp_default_post_types );

            // Filter Out Truly Custom Post Types, we don't want to mess around with the others
            if (!in_array($post_type, $wp_default_post_types) && !$this->graphQLKeysExists($args)) {
                if (isset($args['labels']) && isset($args['public']) && $args['public'] == true) {
                    $graphQLArgs = apply_filters( 'graphql_cpt_post_type_graphql_args', [
                        'show_in_graphql' => true,
                        'graphql_single_name' => $this->cleanStrings($args['labels']['singular_name']),
                        'graphql_plural_name' => $this->cleanStrings($args['labels']['name'])
                    ], $post_type );

                    $graphQLArgs = apply_filters( "graphql_cpt_{$post_type}_graphql_args", $graphQLArgs );

                    // Merge args together.
                    return apply_filters( "graphql_cpt_{$post_type}_merged_args", array_merge($args, $graphQLArgs) );
                }
            }

            return $args;
        }

        public function filterTaxonomies($args, $taxonomy)
        {
            $wp_default_taxonomies = [
                'category',
                'post_tag',
                'nav_menu',
                'link_category',
                'nav_menu_item',
                'post_format',
                'wp_theme',
                'wp_template_part_area',
                'product_type',
                'product_visibility',
                'product_cat',
                'product_tag',
                'product_shipping_class',
            ];

            $wp_default_taxonomies = apply_filters( 'graphql_cpt_excluded_taxonomies', $wp_default_taxonomies );

            // Filter Out Truly Custom Taxonomies, we don't want to mess around with the others
            if (!in_array($taxonomy, $wp_default_taxonomies) && !$this->graphQLKeysExists($args)) {
                if (isset($args['labels'])) {
                    $graphQLArgs = apply_filters( 'graphql_cpt_taxonomy_graphql_args', [
                        'show_in_graphql' => true,
                        'graphql_single_name' => $this->cleanStrings($args['labels']['singular_name']),
                        'graphql_plural_name' => $this->cleanStrings($args['labels']['name'])
                    ], $taxonomy );

                    $graphQLArgs = apply_filters( "graphql_cpt_{$taxonomy}_graphql_args", $graphQLArgs );

                    // Merge args together.
                    return apply_filters( "graphql_cpt_{$taxonomy}_merged_args", array_merge($args, $graphQLArgs) );
                }
            }

            return $args;
        }

        public function graphQLKeysExists($args)
        {
            $graphQLKeys = apply_filters( 'graphql_cpt_existing_keys_to_check_against', [
                'show_in_graphql',
                'graphql_single_name',
                'graphql_plural_name'
            ] );

            return !array_diff_key(array_flip($graphQLKeys), $args);
        }

        public function cleanStrings($string)
        {
            return preg_replace('/[^a-zA-Z0-9_]+/', '', lcfirst(ucwords($string)));
        }
    }
}

// Boot Plugin
add_action('plugins_loaded', function () {
    new CPT();
});
