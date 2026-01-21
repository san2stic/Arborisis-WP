<?php
/**
 * Likes Manager
 */

if (!defined('ABSPATH')) exit;

class ARB_Likes_Manager {

    /**
     * Toggle like (like/unlike)
     */
    public static function toggle($sound_id) {
        if (!is_user_logged_in()) {
            return new WP_Error(
                'not_logged_in',
                __('You must be logged in to like sounds', 'arborisis-stats'),
                ['status' => 401]
            );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'arb_likes';
        $user_id = get_current_user_id();

        // Check if already liked
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND sound_id = %d",
            $user_id,
            $sound_id
        ));

        if ($existing) {
            // Unlike
            $wpdb->delete($table, [
                'user_id'  => $user_id,
                'sound_id' => $sound_id,
            ]);

            // Decrement count
            $current_count = (int) get_post_meta($sound_id, '_arb_likes_count', true);
            update_post_meta($sound_id, '_arb_likes_count', max(0, $current_count - 1));

            $liked = false;
            $count = max(0, $current_count - 1);
        } else {
            // Like
            $wpdb->insert($table, [
                'user_id'    => $user_id,
                'sound_id'   => $sound_id,
                'created_at' => current_time('mysql'),
            ]);

            // Increment count
            $current_count = (int) get_post_meta($sound_id, '_arb_likes_count', true);
            update_post_meta($sound_id, '_arb_likes_count', $current_count + 1);

            $liked = true;
            $count = $current_count + 1;
        }

        // Invalidate cache
        arb_redis_delete_pattern('arb:stats:*');
        arb_redis_delete_pattern('arb:sound:' . $sound_id . ':*');

        return new WP_REST_Response([
            'liked'       => $liked,
            'likes_count' => $count,
        ], 200);
    }

    /**
     * Get likes count for sound
     */
    public static function get_count($sound_id) {
        return (int) get_post_meta($sound_id, '_arb_likes_count', true);
    }

    /**
     * Check if user has liked sound
     */
    public static function has_liked($sound_id, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'arb_likes';

        $liked = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND sound_id = %d",
            $user_id,
            $sound_id
        ));

        return (bool) $liked;
    }

    /**
     * Get sounds liked by user
     */
    public static function get_user_likes($user_id, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_likes';

        $sound_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT sound_id FROM {$table}
             WHERE user_id = %d
             ORDER BY created_at DESC
             LIMIT %d",
            $user_id,
            $limit
        ));

        return $sound_ids;
    }
}
