<?php
/**
 * Arborisis Theme Functions
 *
 * @package Arborisis
 */

if (!defined('ABSPATH'))
    exit;

define('ARBORISIS_THEME_VERSION', '1.0.0');
define('ARBORISIS_THEME_DIR', get_template_directory());
define('ARBORISIS_THEME_URI', get_template_directory_uri());

/**
 * Theme setup
 */
add_action('after_setup_theme', 'arborisis_setup');
function arborisis_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    add_theme_support('dark-editor-style');

    // Image sizes
    add_image_size('sound-thumbnail', 400, 400, true);
    add_image_size('sound-large', 1200, 800, true);
    add_image_size('sound-hero', 1920, 1080, true);

    // Register navigation menus
    register_nav_menus([
        'primary' => __('Primary Menu', 'arborisis'),
        'footer' => __('Footer Menu', 'arborisis'),
    ]);
}

/**
 * Enqueue Vite assets
 */
add_action('wp_enqueue_scripts', 'arborisis_enqueue_assets');
function arborisis_enqueue_assets()
{
    $is_dev = defined('WP_ENV') && WP_ENV === 'development';

    if ($is_dev) {
        // Development mode: Vite dev server
        wp_enqueue_script('vite-client', 'http://localhost:3000/@vite/client', [], null, false);
        wp_add_inline_script('vite-client', '', 'before');
        wp_script_add_data('vite-client', 'type', 'module');

        wp_enqueue_script('arborisis-main', 'http://localhost:3000/src/main.js', [], null, true);
        wp_script_add_data('arborisis-main', 'type', 'module');
    } else {
        // Production mode: compiled assets
        $manifest_path = ARBORISIS_THEME_DIR . '/dist/.vite/manifest.json';

        if (!file_exists($manifest_path)) {
            // Fallback for older Vite versions
            $manifest_path = ARBORISIS_THEME_DIR . '/dist/manifest.json';
        }

        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);

            // Main JS
            if (isset($manifest['src/main.js'])) {
                $main = $manifest['src/main.js'];
                wp_enqueue_script(
                    'arborisis-main',
                    ARBORISIS_THEME_URI . '/dist/' . $main['file'],
                    [],
                    ARBORISIS_THEME_VERSION,
                    true
                );

                // Main CSS
                if (isset($main['css'])) {
                    foreach ($main['css'] as $css) {
                        wp_enqueue_style(
                            'arborisis-main-css',
                            ARBORISIS_THEME_URI . '/dist/' . $css,
                            [],
                            ARBORISIS_THEME_VERSION
                        );
                    }
                }
            }
        }
    }

    // Localize script with API data
    wp_localize_script('arborisis-main', 'arborisData', [
        'apiUrl' => rest_url('arborisis/v1'),
        'nonce' => wp_create_nonce('wp_rest'),
        'userId' => get_current_user_id(),
        'isLoggedIn' => is_user_logged_in(),
        'homeUrl' => home_url('/'),
        'themeUrl' => ARBORISIS_THEME_URI,
        'strings' => [
            'loading' => __('Loading...', 'arborisis'),
            'error' => __('An error occurred', 'arborisis'),
            'play' => __('Play', 'arborisis'),
            'pause' => __('Pause', 'arborisis'),
            'like' => __('Like', 'arborisis'),
            'share' => __('Share', 'arborisis'),
            'download' => __('Download', 'arborisis'),
            'explore' => __('Explore', 'arborisis'),
        ],
    ]);

    // Google Fonts
    wp_enqueue_style(
        'arborisis-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap',
        [],
        null
    );
}

/**
 * Enqueue page-specific scripts
 */
add_action('wp_enqueue_scripts', 'arborisis_enqueue_page_scripts');
function arborisis_enqueue_page_scripts()
{
    if (!is_production())
        return;

    $manifest_path = ARBORISIS_THEME_DIR . '/dist/.vite/manifest.json';

    if (!file_exists($manifest_path)) {
        $manifest_path = ARBORISIS_THEME_DIR . '/dist/manifest.json';
    }

    if (!file_exists($manifest_path))
        return;

    $manifest = json_decode(file_get_contents($manifest_path), true);

    // Map page
    if (is_page_template('page-map.php') && isset($manifest['src/map.js'])) {
        $map = $manifest['src/map.js'];
        wp_enqueue_script(
            'arborisis-map',
            ARBORISIS_THEME_URI . '/dist/' . $map['file'],
            ['arborisis-main'],
            ARBORISIS_THEME_VERSION,
            true
        );
    }

    // Graph page
    if (is_page_template('page-graph.php') && isset($manifest['src/graph.js'])) {
        $graph = $manifest['src/graph.js'];
        wp_enqueue_script(
            'arborisis-graph',
            ARBORISIS_THEME_URI . '/dist/' . $graph['file'],
            ['arborisis-main'],
            ARBORISIS_THEME_VERSION,
            true
        );
    }

    // Single sound
    if (is_singular('sound') && isset($manifest['src/player.js'])) {
        $player = $manifest['src/player.js'];
        wp_enqueue_script(
            'arborisis-player',
            ARBORISIS_THEME_URI . '/dist/' . $player['file'],
            ['arborisis-main'],
            ARBORISIS_THEME_VERSION,
            true
        );
    }
}

/**
 * Check if production mode
 */
function is_production()
{
    return !defined('WP_ENV') || WP_ENV === 'production';
}

/**
 * Body classes
 */
add_filter('body_class', 'arborisis_body_classes');
function arborisis_body_classes($classes)
{
    // Add dark mode class if user preference
    if (isset($_COOKIE['dark-mode']) && $_COOKIE['dark-mode'] === 'true') {
        $classes[] = 'dark';
    }

    // Add page-specific classes
    if (is_page_template('page-map.php')) {
        $classes[] = 'has-map';
    }

    if (is_page_template('page-graph.php')) {
        $classes[] = 'has-graph';
    }

    if (is_singular('sound')) {
        $classes[] = 'single-sound';
    }

    return $classes;
}

/**
 * Custom excerpt length
 */
add_filter('excerpt_length', 'arborisis_excerpt_length');
function arborisis_excerpt_length($length)
{
    return 30;
}

/**
 * Custom excerpt more
 */
add_filter('excerpt_more', 'arborisis_excerpt_more');
function arborisis_excerpt_more($more)
{
    return '...';
}

/**
 * Add async/defer attributes to scripts
 */
add_filter('script_loader_tag', 'arborisis_script_loader_tag', 10, 3);
function arborisis_script_loader_tag($tag, $handle, $src)
{
    // Add defer to main scripts
    if (strpos($handle, 'arborisis-') === 0) {
        $tag = str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}

/**
 * Custom template tags
 */
require_once ARBORISIS_THEME_DIR . '/inc/template-tags.php';

/**
 * Custom walker for navigation
 */
require_once ARBORISIS_THEME_DIR . '/inc/class-walker-nav-menu.php';

/**
 * Customizer options
 */
require_once ARBORISIS_THEME_DIR . '/inc/customizer.php';
