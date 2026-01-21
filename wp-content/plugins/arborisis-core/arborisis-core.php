<?php
/**
 * Plugin Name: Arborisis Core
 * Plugin URI: https://arborisis.example.com
 * Description: Core CPT, taxonomies, roles, and REST API for Arborisis Field Recording platform
 * Version: 1.0.0
 * Author: Arborisis Team
 * License: GPL-2.0+
 * Text Domain: arborisis-core
 */

if (!defined('ABSPATH')) exit;

define('ARB_CORE_VERSION', '1.0.0');
define('ARB_CORE_PATH', plugin_dir_path(__FILE__));
define('ARB_CORE_URL', plugin_dir_url(__FILE__));

// Autoload classes
require_once ARB_CORE_PATH . 'includes/class-sound-cpt.php';
require_once ARB_CORE_PATH . 'includes/class-roles.php';
require_once ARB_CORE_PATH . 'includes/class-rest-sounds.php';
require_once ARB_CORE_PATH . 'includes/class-rest-users.php';
require_once ARB_CORE_PATH . 'includes/helpers.php';

/**
 * Activation hook
 */
register_activation_hook(__FILE__, 'arb_core_activate');
function arb_core_activate() {
    ARB_Sound_CPT::register();
    ARB_Roles::register();
    flush_rewrite_rules();
}

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, 'arb_core_deactivate');
function arb_core_deactivate() {
    flush_rewrite_rules();
}

/**
 * Initialize plugin
 */
add_action('init', 'arb_core_init');
function arb_core_init() {
    ARB_Sound_CPT::register();
    ARB_Roles::init();
}

/**
 * REST API initialization
 */
add_action('rest_api_init', 'arb_core_rest_init');
function arb_core_rest_init() {
    ARB_REST_Sounds::register_routes();
    ARB_REST_Users::register_routes();
}

/**
 * Add custom capabilities to map_meta_cap
 */
add_filter('map_meta_cap', 'arb_map_sound_meta_caps', 10, 4);
function arb_map_sound_meta_caps($caps, $cap, $user_id, $args) {
    if ($cap === 'edit_sound' || $cap === 'delete_sound') {
        $post_id = isset($args[0]) ? $args[0] : 0;
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'sound') {
            return $caps;
        }

        // Owner can edit/delete own sounds
        if ($user_id == $post->post_author) {
            $caps = ['edit_sounds'];
        }
        // Moderators can edit/delete all sounds
        elseif (user_can($user_id, 'moderate_sounds')) {
            $caps = ['moderate_sounds'];
        }
        // Others cannot
        else {
            $caps = ['do_not_allow'];
        }
    }

    return $caps;
}
