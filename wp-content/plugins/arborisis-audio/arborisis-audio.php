<?php
/**
 * Plugin Name: Arborisis Audio
 * Plugin URI: https://arborisis.example.com
 * Description: S3 direct upload and audio metadata extraction for Arborisis
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-audio
 */

if (!defined('ABSPATH')) exit;

define('ARB_AUDIO_VERSION', '1.0.0');
define('ARB_AUDIO_PATH', plugin_dir_path(__FILE__));
define('ARB_AUDIO_URL', plugin_dir_url(__FILE__));

// Load Composer autoloader for AWS SDK and other dependencies
if (file_exists(ABSPATH . 'vendor/autoload.php')) {
    require_once ABSPATH . 'vendor/autoload.php';
} else {
    // Fallback: try to load from plugin directory (if composer was run in plugin)
    if (file_exists(ARB_AUDIO_PATH . 'vendor/autoload.php')) {
        require_once ARB_AUDIO_PATH . 'vendor/autoload.php';
    } else {
        // Critical error: AWS SDK not available
        add_action('admin_notices', function() {
            echo '<div class="error"><p><strong>Arborisis Audio:</strong> AWS SDK not found. Please run <code>composer install</code> in the WordPress root directory.</p></div>';
        });
        return; // Stop plugin initialization
    }
}

// Require classes
require_once ARB_AUDIO_PATH . 'includes/class-s3-client.php';
require_once ARB_AUDIO_PATH . 'includes/class-rest-upload.php';
require_once ARB_AUDIO_PATH . 'includes/class-metadata-extractor.php';

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_audio_rest_init');
function arb_audio_rest_init() {
    ARB_REST_Upload::register_routes();
}

/**
 * WP-CLI commands
 */
if (defined('WP_CLI') && WP_CLI) {
    require_once ARB_AUDIO_PATH . 'includes/class-cli.php';
    WP_CLI::add_command('arborisis extract-metadata', ['ARB_Audio_CLI', 'extract_metadata']);
}
