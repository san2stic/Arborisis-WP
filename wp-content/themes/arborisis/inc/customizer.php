<?php
/**
 * Arborisis Theme Customizer
 *
 * @package Arborisis
 */

if (!defined('ABSPATH')) exit;

/**
 * Add postMessage support for site title and description
 */
add_action('customize_register', 'arborisis_customize_register');
function arborisis_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        $wp_customize->selective_refresh->add_partial(
            'blogname',
            array(
                'selector'        => '.site-title a',
                'render_callback' => 'arborisis_customize_partial_blogname',
            )
        );
        $wp_customize->selective_refresh->add_partial(
            'blogdescription',
            array(
                'selector'        => '.site-description',
                'render_callback' => 'arborisis_customize_partial_blogdescription',
            )
        );
    }

    // Theme Options Section
    $wp_customize->add_section('arborisis_theme_options', array(
        'title'    => __('Options du Thème', 'arborisis'),
        'priority' => 30,
    ));

    // Social Media Links
    $social_networks = array(
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'facebook'  => 'Facebook',
        'youtube'   => 'YouTube',
        'github'    => 'GitHub',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("arborisis_social_{$network}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("arborisis_social_{$network}", array(
            'label'   => sprintf(__('Lien %s', 'arborisis'), $label),
            'section' => 'arborisis_theme_options',
            'type'    => 'url',
        ));
    }

    // Footer Copyright Text
    $wp_customize->add_setting('arborisis_footer_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('arborisis_footer_text', array(
        'label'   => __('Texte du Footer', 'arborisis'),
        'section' => 'arborisis_theme_options',
        'type'    => 'text',
    ));

    // Enable/Disable Dark Mode
    $wp_customize->add_setting('arborisis_dark_mode', array(
        'default'           => true,
        'sanitize_callback' => 'arborisis_sanitize_checkbox',
    ));

    $wp_customize->add_control('arborisis_dark_mode', array(
        'label'   => __('Activer le mode sombre', 'arborisis'),
        'section' => 'arborisis_theme_options',
        'type'    => 'checkbox',
    ));

    // Homepage Settings Section
    $wp_customize->add_section('arborisis_homepage', array(
        'title'    => __('Paramètres Page d\'Accueil', 'arborisis'),
        'priority' => 35,
    ));

    // Hero Title
    $wp_customize->add_setting('arborisis_hero_title', array(
        'default'           => 'Explorez les Paysages Sonores du Monde',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('arborisis_hero_title', array(
        'label'   => __('Titre Hero', 'arborisis'),
        'section' => 'arborisis_homepage',
        'type'    => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('arborisis_hero_subtitle', array(
        'default'           => 'Une plateforme collaborative de field recording',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('arborisis_hero_subtitle', array(
        'label'   => __('Sous-titre Hero', 'arborisis'),
        'section' => 'arborisis_homepage',
        'type'    => 'textarea',
    ));

    // Color Scheme Section
    $wp_customize->add_section('arborisis_colors', array(
        'title'    => __('Couleurs', 'arborisis'),
        'priority' => 40,
    ));

    // Primary Color
    $wp_customize->add_setting('arborisis_primary_color', array(
        'default'           => '#22c55e',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'arborisis_primary_color', array(
        'label'   => __('Couleur Primaire', 'arborisis'),
        'section' => 'arborisis_colors',
    )));

    // Secondary Color
    $wp_customize->add_setting('arborisis_secondary_color', array(
        'default'           => '#a855f7',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'arborisis_secondary_color', array(
        'label'   => __('Couleur Secondaire', 'arborisis'),
        'section' => 'arborisis_colors',
    )));
}

/**
 * Render the site title for the selective refresh partial
 */
function arborisis_customize_partial_blogname() {
    bloginfo('name');
}

/**
 * Render the site description for the selective refresh partial
 */
function arborisis_customize_partial_blogdescription() {
    bloginfo('description');
}

/**
 * Sanitize checkbox values
 */
function arborisis_sanitize_checkbox($checked) {
    return ((isset($checked) && true === $checked) ? true : false);
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously
 */
add_action('customize_preview_init', 'arborisis_customize_preview_js');
function arborisis_customize_preview_js() {
    wp_enqueue_script(
        'arborisis-customizer',
        get_template_directory_uri() . '/js/customizer.js',
        array('customize-preview'),
        ARBORISIS_THEME_VERSION,
        true
    );
}

/**
 * Output custom CSS from customizer
 */
add_action('wp_head', 'arborisis_customizer_css');
function arborisis_customizer_css() {
    $primary_color = get_theme_mod('arborisis_primary_color', '#22c55e');
    $secondary_color = get_theme_mod('arborisis_secondary_color', '#a855f7');
    
    if ($primary_color !== '#22c55e' || $secondary_color !== '#a855f7') {
        ?>
        <style type="text/css">
            :root {
                --color-primary: <?php echo esc_attr($primary_color); ?>;
                --color-secondary: <?php echo esc_attr($secondary_color); ?>;
            }
            .text-primary-600,
            .text-primary-500 {
                color: <?php echo esc_attr($primary_color); ?>;
            }
            .bg-primary-600,
            .bg-primary-500 {
                background-color: <?php echo esc_attr($primary_color); ?>;
            }
            .border-primary-600,
            .border-primary-500 {
                border-color: <?php echo esc_attr($primary_color); ?>;
            }
            .text-secondary-600,
            .text-secondary-500 {
                color: <?php echo esc_attr($secondary_color); ?>;
            }
            .bg-secondary-600,
            .bg-secondary-500 {
                background-color: <?php echo esc_attr($secondary_color); ?>;
            }
        </style>
        <?php
    }
}
