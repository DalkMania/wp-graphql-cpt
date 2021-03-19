<?php
/**
 * Plugin Name: WP GraphQL Custom Post Types and Custom Taxonomies
 * Description: Exposes all registered Custom Post Types and Custom Taxonomies to the WPGraphQL EndPoint.
 * Author: Niklas Dahlqvist
 * Author URI: https://www.niklasdahlqvist.com
 * Version: 0.5
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
                // Exclude ACF Field Groups
                'acf-field-group'
            ];

            // Filter Out Truly Custom Post Types, we don't want to mess around with the others
            if (!in_array($post_type, $wp_default_post_types) && !$this->graphQLKeysExists($args)) {
                if (isset($args['labels']) && isset($args['public']) && $args['public'] == true) {
                    $graphQLArgs = [
                        'show_in_graphql' => true,
                        'graphql_single_name' => $this->cleanStrings($args['labels']['singular_name']),
                        'graphql_plural_name' => $this->cleanStrings($args['labels']['name'])
                    ];
                }

                // Merge args together.
                return array_merge($args, $graphQLArgs);
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
                'action-group'
            ];

            // Filter Out Truly Custom Taxonomies, we don't want to mess around with the others
            if (!in_array($taxonomy, $wp_default_taxonomies) && !$this->graphQLKeysExists($args)) {
                if (isset($args['labels'])) {
                    $graphQLArgs = [
                        'show_in_graphql' => true,
                        'graphql_single_name' => $this->cleanStrings($args['labels']['singular_name']),
                        'graphql_plural_name' => $this->cleanStrings($args['labels']['name'])
                    ];

                    // Merge args together.
                    return array_merge($args, $graphQLArgs);
                }
            }

            return $args;
        }

        public function graphQLKeysExists($args)
        {
            $graphQLKeys = [
                'show_in_graphql',
                'graphql_single_name',
                'graphql_plural_name'
            ];

            return !array_diff_key(array_flip($graphQLKeys), $args);
        }

        public function cleanStrings($string)
        {
            return preg_replace('/[\s_-]/', '', lcfirst(ucwords($string)));
        }
    }
}

// Boot Plugin
add_action('plugins_loaded', function () {
    new CPT;
});
