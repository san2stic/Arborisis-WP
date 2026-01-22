/**
 * Arborisis Theme Customizer Preview
 *
 * This file handles the live preview functionality in the WordPress Customizer.
 *
 * @package Arborisis
 */

(function($) {
    'use strict';

    // Site title
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });

    // Site description
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Header text color
    wp.customize('header_textcolor', function(value) {
        value.bind(function(to) {
            if ('blank' === to) {
                $('.site-title, .site-description').css({
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                });
            } else {
                $('.site-title, .site-description').css({
                    'clip': 'auto',
                    'position': 'relative'
                });
                $('.site-title a, .site-description').css({
                    'color': to
                });
            }
        });
    });

    // Primary color
    wp.customize('arborisis_primary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--color-primary', to);
        });
    });

    // Secondary color
    wp.customize('arborisis_secondary_color', function(value) {
        value.bind(function(to) {
            document.documentElement.style.setProperty('--color-secondary', to);
        });
    });

    // Hero title
    wp.customize('arborisis_hero_title', function(value) {
        value.bind(function(to) {
            $('.hero-title').text(to);
        });
    });

    // Hero subtitle
    wp.customize('arborisis_hero_subtitle', function(value) {
        value.bind(function(to) {
            $('.hero-subtitle').text(to);
        });
    });

})(jQuery);
