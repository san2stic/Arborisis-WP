<?php
/**
 * Plugin Name: Arborisis Geo
 * Plugin URI: https://arborisis.example.com
 * Description: Geospatial indexing and map clustering for Arborisis
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-geo
 */

if (!defined('ABSPATH')) exit;

define('ARB_GEO_VERSION', '1.0.0');
define('ARB_GEO_PATH', plugin_dir_path(__FILE__));
define('ARB_GEO_URL', plugin_dir_url(__FILE__));

// Require classes
require_once ARB_GEO_PATH . 'includes/class-geo-indexer.php';
require_once ARB_GEO_PATH . 'includes/class-clustering.php';
require_once ARB_GEO_PATH . 'includes/class-rest-map.php';

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'arb_geo_activate');
function arb_geo_activate() {
    global $wpdb;
    $table = $wpdb->prefix . 'arb_geo_index';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        sound_id BIGINT UNSIGNED PRIMARY KEY,
        latitude DECIMAL(10,8) NOT NULL,
        longitude DECIMAL(11,8) NOT NULL,
        geohash VARCHAR(12) NOT NULL,
        INDEX idx_geohash (geohash),
        INDEX idx_lat (latitude),
        INDEX idx_lon (longitude)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * Hook into sound save
 */
add_action('save_post_sound', 'arb_geo_index_sound', 20, 3);
function arb_geo_index_sound($post_id, $post, $update) {
    ARB_Geo_Indexer::index_sound($post_id);
}

/**
 * Hook into sound delete
 */
add_action('before_delete_post', 'arb_geo_delete_sound');
function arb_geo_delete_sound($post_id) {
    if (get_post_type($post_id) === 'sound') {
        ARB_Geo_Indexer::delete_sound($post_id);
    }
}

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_geo_rest_init');
function arb_geo_rest_init() {
    ARB_REST_Map::register_routes();
}

/**
 * WP-CLI commands
 */
if (defined('WP_CLI') && WP_CLI) {
    require_once ARB_GEO_PATH . 'includes/class-cli.php';
    WP_CLI::add_command('arborisis reindex-geo', ['ARB_Geo_CLI', 'reindex']);
}
