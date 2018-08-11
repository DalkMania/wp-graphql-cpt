WP GraphQL Custom Post Types and Custom Taxonomies
===============

This plugin is an add-on for the awesome [WP GraphQL][wp-graphql]

It builds on top of WP GraphQL and adds all registered custom posttypes and taxonomies to the WP GraphQL Endpoint.

## Installing

1. Make sure that [WP GraphQL][wp-graphql] is installed and activated first.
2. Upload this repo (or git clone) to your plugins folder and activate it.

## Usage

Your theme or other plugins may use custom post types and taxonomies to add custom functionality. That data or functionality may be beneficial / required for a front end or other app that consumes your API.

Just activate the plugin and the data will be avialble to you at the WP GraphQL Endpoint for you to query.

[wp-graphql]: https://github.com/wp-graphql/wp-graphql