<?php
/**
 * Plugin Name: Arborisis Stats
 * Plugin URI: https://arborisis.example.com
 * Description: Plays tracking, likes, comments, and statistics for Arborisis
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-stats
 */

if (!defined('ABSPATH')) exit;

define('ARB_STATS_VERSION', '1.0.0');
define('ARB_STATS_PATH', plugin_dir_path(__FILE__));
define('ARB_STATS_URL', plugin_dir_url(__FILE__));

// Require classes
require_once ARB_STATS_PATH . 'includes/class-plays-tracker.php';
require_once ARB_STATS_PATH . 'includes/class-likes-manager.php';
require_once ARB_STATS_PATH . 'includes/class-aggregator.php';
require_once ARB_STATS_PATH . 'includes/class-rest-stats.php';

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'arb_stats_activate');
function arb_stats_activate() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();

    $tables = [
        // Likes table
        "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}arb_likes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            sound_id BIGINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_user (user_id),
            INDEX idx_sound (sound_id),
            UNIQUE KEY unique_like (user_id, sound_id)
        ) {$charset};",

        // Plays events table
        "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}arb_plays (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sound_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            ip_hash VARCHAR(64) NOT NULL,
            user_agent_hash VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            INDEX idx_sound (sound_id),
            INDEX idx_created (created_at),
            INDEX idx_fingerprint (sound_id, ip_hash, user_agent_hash, created_at)
        ) {$charset};",

        // Plays daily aggregation
        "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}arb_plays_daily (
            sound_id BIGINT UNSIGNED NOT NULL,
            day DATE NOT NULL,
            plays_count INT UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY (sound_id, day),
            INDEX idx_day (day)
        ) {$charset};",
    ];

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ($tables as $sql) {
        dbDelta($sql);
    }
}

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_stats_rest_init');
function arb_stats_rest_init() {
    ARB_REST_Stats::register_routes();
}

/**
 * WP-CLI commands
 */
if (defined('WP_CLI') && WP_CLI) {
    require_once ARB_STATS_PATH . 'includes/class-cli.php';
    WP_CLI::add_command('arborisis aggregate-plays', ['ARB_Stats_CLI', 'aggregate_plays']);
    WP_CLI::add_command('arborisis compute-trending', ['ARB_Stats_CLI', 'compute_trending']);
    WP_CLI::add_command('arborisis cleanup-plays', ['ARB_Stats_CLI', 'cleanup_plays']);
    WP_CLI::add_command('arborisis warm-cache', ['ARB_Stats_CLI', 'warm_cache']);
}
