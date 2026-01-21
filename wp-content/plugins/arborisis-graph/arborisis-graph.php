<?php
/**
 * Plugin Name: Arborisis Graph
 * Plugin URI: https://arborisis.example.com
 * Description: Graph explore with similarity-based relationships for Arborisis
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-graph
 */

if (!defined('ABSPATH')) exit;

define('ARB_GRAPH_VERSION', '1.0.0');
define('ARB_GRAPH_PATH', plugin_dir_path(__FILE__));
define('ARB_GRAPH_URL', plugin_dir_url(__FILE__));

// Require classes
require_once ARB_GRAPH_PATH . 'includes/class-graph-builder.php';
require_once ARB_GRAPH_PATH . 'includes/class-rest-graph.php';

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_graph_rest_init');
function arb_graph_rest_init() {
    ARB_REST_Graph::register_routes();
}

/**
 * Hook to invalidate cache when sounds are updated
 */
add_action('save_post_sound', 'arb_graph_invalidate_cache', 30);
function arb_graph_invalidate_cache($post_id) {
    arb_redis_delete_pattern('arb:graph:*');
}
