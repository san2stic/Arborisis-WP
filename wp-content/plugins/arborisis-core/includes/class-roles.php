<?php
/**
 * Custom Roles and Capabilities
 */

if (!defined('ABSPATH')) exit;

class ARB_Roles {

    /**
     * Register custom roles (called on activation)
     */
    public static function register() {
        self::add_uploader_role();
        self::add_moderator_role();
        self::add_caps_to_admin();
    }

    /**
     * Initialize (called on init hook)
     */
    public static function init() {
        // Add any runtime role modifications here if needed
    }

    /**
     * Add Uploader role
     */
    private static function add_uploader_role() {
        add_role('uploader', __('Uploader', 'arborisis-core'), [
            'read'                   => true,
            'edit_posts'             => false,
            'delete_posts'           => false,
            'upload_files'           => true,
            'upload_sounds'          => true,
            'edit_sounds'            => true,
            'delete_sounds'          => true,
            'publish_sounds'         => true,
            'edit_published_sounds'  => true,
            'delete_published_sounds'=> true,
        ]);
    }

    /**
     * Add Moderator role
     */
    private static function add_moderator_role() {
        $uploader_caps = get_role('uploader');

        $moderator_caps = array_merge(
            $uploader_caps ? $uploader_caps->capabilities : [],
            [
                'moderate_sounds'          => true,
                'edit_others_sounds'       => true,
                'delete_others_sounds'     => true,
                'edit_private_sounds'      => true,
                'delete_private_sounds'    => true,
                'moderate_comments'        => true,
                'edit_users'               => true,
                'list_users'               => true,
            ]
        );

        add_role('moderator', __('Moderator', 'arborisis-core'), $moderator_caps);
    }

    /**
     * Add sound capabilities to Administrator
     */
    private static function add_caps_to_admin() {
        $admin = get_role('administrator');

        if ($admin) {
            $admin->add_cap('upload_sounds');
            $admin->add_cap('edit_sounds');
            $admin->add_cap('delete_sounds');
            $admin->add_cap('publish_sounds');
            $admin->add_cap('edit_published_sounds');
            $admin->add_cap('delete_published_sounds');
            $admin->add_cap('moderate_sounds');
            $admin->add_cap('edit_others_sounds');
            $admin->add_cap('delete_others_sounds');
            $admin->add_cap('edit_private_sounds');
            $admin->add_cap('delete_private_sounds');
            $admin->add_cap('view_stats');
        }
    }

    /**
     * Remove custom roles (called on deactivation)
     */
    public static function remove_roles() {
        remove_role('uploader');
        remove_role('moderator');
    }
}
