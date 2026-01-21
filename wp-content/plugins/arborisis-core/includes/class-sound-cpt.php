<?php
/**
 * Sound Custom Post Type
 */

if (!defined('ABSPATH')) exit;

class ARB_Sound_CPT {

    /**
     * Register Sound CPT and taxonomies
     */
    public static function register() {
        self::register_post_type();
        self::register_taxonomies();
    }

    /**
     * Register Sound post type
     */
    private static function register_post_type() {
        $labels = [
            'name'                  => _x('Sounds', 'Post type general name', 'arborisis-core'),
            'singular_name'         => _x('Sound', 'Post type singular name', 'arborisis-core'),
            'menu_name'             => _x('Sounds', 'Admin Menu text', 'arborisis-core'),
            'name_admin_bar'        => _x('Sound', 'Add New on Toolbar', 'arborisis-core'),
            'add_new'               => __('Add New', 'arborisis-core'),
            'add_new_item'          => __('Add New Sound', 'arborisis-core'),
            'new_item'              => __('New Sound', 'arborisis-core'),
            'edit_item'             => __('Edit Sound', 'arborisis-core'),
            'view_item'             => __('View Sound', 'arborisis-core'),
            'all_items'             => __('All Sounds', 'arborisis-core'),
            'search_items'          => __('Search Sounds', 'arborisis-core'),
            'parent_item_colon'     => __('Parent Sounds:', 'arborisis-core'),
            'not_found'             => __('No sounds found.', 'arborisis-core'),
            'not_found_in_trash'    => __('No sounds found in Trash.', 'arborisis-core'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'sound', 'with_front' => false],
            'capability_type'    => 'sound',
            'map_meta_cap'       => true,
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-microphone',
            'supports'           => ['title', 'editor', 'author', 'thumbnail', 'comments'],
            'show_in_rest'       => true,
            'rest_base'          => 'sounds',
        ];

        register_post_type('sound', $args);
    }

    /**
     * Register taxonomies for Sound
     */
    private static function register_taxonomies() {
        // Tags taxonomy
        $tag_labels = [
            'name'              => _x('Tags', 'taxonomy general name', 'arborisis-core'),
            'singular_name'     => _x('Tag', 'taxonomy singular name', 'arborisis-core'),
            'search_items'      => __('Search Tags', 'arborisis-core'),
            'all_items'         => __('All Tags', 'arborisis-core'),
            'edit_item'         => __('Edit Tag', 'arborisis-core'),
            'update_item'       => __('Update Tag', 'arborisis-core'),
            'add_new_item'      => __('Add New Tag', 'arborisis-core'),
            'new_item_name'     => __('New Tag Name', 'arborisis-core'),
            'menu_name'         => __('Tags', 'arborisis-core'),
        ];

        register_taxonomy('sound_tag', 'sound', [
            'labels'            => $tag_labels,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'tag'],
        ]);

        // License taxonomy
        $license_labels = [
            'name'              => _x('Licenses', 'taxonomy general name', 'arborisis-core'),
            'singular_name'     => _x('License', 'taxonomy singular name', 'arborisis-core'),
            'search_items'      => __('Search Licenses', 'arborisis-core'),
            'all_items'         => __('All Licenses', 'arborisis-core'),
            'parent_item'       => __('Parent License', 'arborisis-core'),
            'parent_item_colon' => __('Parent License:', 'arborisis-core'),
            'edit_item'         => __('Edit License', 'arborisis-core'),
            'update_item'       => __('Update License', 'arborisis-core'),
            'add_new_item'      => __('Add New License', 'arborisis-core'),
            'new_item_name'     => __('New License Name', 'arborisis-core'),
            'menu_name'         => __('Licenses', 'arborisis-core'),
        ];

        register_taxonomy('sound_license', 'sound', [
            'labels'            => $license_labels,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => ['slug' => 'license'],
        ]);
    }
}
