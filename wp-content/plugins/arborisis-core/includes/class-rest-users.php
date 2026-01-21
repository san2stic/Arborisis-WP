<?php
/**
 * REST API Endpoints for Users
 */

if (!defined('ABSPATH')) exit;

class ARB_REST_Users {

    /**
     * Register REST routes
     */
    public static function register_routes() {
        // Get user by username
        register_rest_route('arborisis/v1', '/users/(?P<username>[a-zA-Z0-9_-]+)', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_user'],
            'permission_callback' => '__return_true',
        ]);

        // Update current user profile
        register_rest_route('arborisis/v1', '/users/me', [
            'methods'             => 'PUT',
            'callback'            => [__CLASS__, 'update_profile'],
            'permission_callback' => 'is_user_logged_in',
        ]);
    }

    /**
     * Get user by username
     */
    public static function get_user($request) {
        $username = $request['username'];
        $user = get_user_by('login', $username);

        if (!$user) {
            return new WP_Error('user_not_found', __('User not found', 'arborisis-core'), ['status' => 404]);
        }

        return new WP_REST_Response(self::format_user($user), 200);
    }

    /**
     * Update current user profile
     */
    public static function update_profile($request) {
        $user_id = get_current_user_id();
        $data = $request->get_json_params();

        // Update user meta
        if (isset($data['bio'])) {
            update_user_meta($user_id, '_arb_bio', wp_kses_post($data['bio']));
        }

        if (isset($data['website'])) {
            update_user_meta($user_id, '_arb_website', esc_url_raw($data['website']));
        }

        if (isset($data['twitter'])) {
            update_user_meta($user_id, '_arb_twitter', sanitize_text_field($data['twitter']));
        }

        if (isset($data['instagram'])) {
            update_user_meta($user_id, '_arb_instagram', sanitize_text_field($data['instagram']));
        }

        $user = get_user_by('ID', $user_id);
        return new WP_REST_Response(self::format_user($user), 200);
    }

    /**
     * Format user data
     */
    private static function format_user($user) {
        $sounds_count = count_user_posts($user->ID, 'sound', true);

        return [
            'id'           => $user->ID,
            'username'     => $user->user_login,
            'name'         => $user->display_name,
            'avatar'       => get_avatar_url($user->ID, ['size' => 200]),
            'bio'          => get_user_meta($user->ID, '_arb_bio', true),
            'website'      => get_user_meta($user->ID, '_arb_website', true),
            'social'       => [
                'twitter'   => get_user_meta($user->ID, '_arb_twitter', true),
                'instagram' => get_user_meta($user->ID, '_arb_instagram', true),
            ],
            'sounds_count' => $sounds_count,
            'plays_total'  => (int) get_user_meta($user->ID, '_arb_total_plays', true),
            'likes_total'  => (int) get_user_meta($user->ID, '_arb_total_likes', true),
            'joined_at'    => $user->user_registered,
        ];
    }
}
