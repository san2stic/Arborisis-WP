<?php
/**
 * Plugin Name: Arborisis Search
 * Plugin URI: https://arborisis.example.com
 * Description: OpenSearch integration with fallback for Arborisis
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-search
 */

if (!defined('ABSPATH'))
    exit;

define('ARB_SEARCH_VERSION', '1.0.0');
define('ARB_SEARCH_PATH', plugin_dir_path(__FILE__));
define('ARB_SEARCH_URL', plugin_dir_url(__FILE__));

// Load Composer autoloader
if (file_exists(ARB_SEARCH_PATH . 'vendor/autoload.php')) {
    require_once ARB_SEARCH_PATH . 'vendor/autoload.php';
} else {
    add_action('admin_notices', function () {
        echo '<div class="error"><p><strong>Arborisis Search:</strong> Composer dependencies missing. Run <code>composer install</code> in the plugin directory.</p></div>';
    });
    return;
}

// Require classes
require_once ARB_SEARCH_PATH . 'includes/class-opensearch-client.php';
require_once ARB_SEARCH_PATH . 'includes/class-indexer.php';
require_once ARB_SEARCH_PATH . 'includes/class-rest-search.php';

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'arb_search_activate');
function arb_search_activate()
{
    // Create OpenSearch queue table if using async indexing
    global $wpdb;
    $table = $wpdb->prefix . 'arb_opensearch_queue';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sound_id BIGINT UNSIGNED NOT NULL,
        action ENUM('index', 'delete') NOT NULL,
        created_at DATETIME NOT NULL,
        processed_at DATETIME NULL,
        INDEX idx_pending (processed_at, created_at)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

/**
 * Hook into sound save/delete
 */
add_action('save_post_sound', 'arb_search_index_sound', 20, 3);
function arb_search_index_sound($post_id, $post, $update)
{
    ARB_Indexer::index_sound($post_id, $post, $update);
}

add_action('before_delete_post', 'arb_search_delete_sound');
function arb_search_delete_sound($post_id)
{
    ARB_Indexer::delete_sound($post_id);
}

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_search_rest_init');
function arb_search_rest_init()
{
    ARB_REST_Search::register_routes();
}

/**
 * WP-CLI commands
 */
if (defined('WP_CLI') && WP_CLI) {
    require_once ARB_SEARCH_PATH . 'includes/class-cli.php';
    WP_CLI::add_command('arborisis reindex', ['ARB_Search_CLI', 'reindex']);
    WP_CLI::add_command('arborisis process-opensearch-queue', ['ARB_Search_CLI', 'process_queue']);
}
